<?php
/**
 * admin-catalog.php
 * Simple admin editor for:
 * - bd_catalog_products
 * - bd_catalog_product_images (primary image sort_order=1)
 * - bd_catalog_addons
 *
 * Requirements:
 * - main-cart.php must define $pdo (PDO)
 */
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');
ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');

// ====== BASIC AUTH (simple but effective) ======
// Change these 2 values (and keep this file out of public if possible)
const ADMIN_USER = 'admin';
const ADMIN_PASS = 'admin';

if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== ADMIN_USER ||
    $_SERVER['PHP_AUTH_PW']   !== ADMIN_PASS
) {
    header('WWW-Authenticate: Basic realm="Bali Diving Admin"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Unauthorized";
    exit;
}

include __DIR__ . '/../template/database/main-cart.php'; // must define $pdo

if (!($pdo instanceof PDO)) {
    http_response_code(500);
    echo "PDO not found. Check main-cart.php";
    exit;
}

// ====== CSRF ======
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(24));
}
$CSRF = $_SESSION['csrf'];

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function fnum($v): float { return is_numeric($v) ? (float)$v : 0.0; }
function b01($v): int { return !empty($v) ? 1 : 0; }

function require_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf'] ?? '', (string)$token)) {
            http_response_code(403);
            echo "CSRF invalid";
            exit;
        }
    }
}

function flash_set(string $msg, string $type='ok'): void {
    $_SESSION['flash'] = ['msg'=>$msg,'type'=>$type];
}
function flash_get(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// ====== ACTION HANDLER ======
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    try {
        if ($action === 'save_product') {
            $id          = (int)($_POST['id'] ?? 0);
            $name        = trim((string)($_POST['name'] ?? ''));
            $price_usd   = fnum($_POST['price_usd'] ?? 0);
            $category    = trim((string)($_POST['category'] ?? ''));
            $description = trim((string)($_POST['description'] ?? ''));
            $is_enquiry  = b01($_POST['is_enquiry'] ?? 0);

            if ($name === '' || $category === '') {
                throw new RuntimeException("Name & Category wajib diisi.");
            }

            if ($id > 0) {
                $stmt = $pdo->prepare("
                    UPDATE bd_catalog_products
                    SET name=:name, price_usd=:price_usd, category=:category, description=:description, is_enquiry=:is_enquiry
                    WHERE id=:id
                ");
                $stmt->execute([
                    ':name' => $name,
                    ':price_usd' => $price_usd,
                    ':category' => $category,
                    ':description' => $description,
                    ':is_enquiry' => $is_enquiry,
                    ':id' => $id
                ]);
                flash_set("Product updated (#{$id}).");
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO bd_catalog_products (name, price_usd, category, description, is_enquiry)
                    VALUES (:name, :price_usd, :category, :description, :is_enquiry)
                ");
                $stmt->execute([
                    ':name' => $name,
                    ':price_usd' => $price_usd,
                    ':category' => $category,
                    ':description' => $description,
                    ':is_enquiry' => $is_enquiry,
                ]);
                $newId = (int)$pdo->lastInsertId();
                flash_set("Product created (#{$newId}).");
            }

            // Upsert primary image (sort_order=1)
            $imgUrl = trim((string)($_POST['primary_image_url'] ?? ''));
            $pidForImage = $id > 0 ? $id : (int)$pdo->lastInsertId();
            if ($pidForImage > 0) {
                // delete if empty
                if ($imgUrl === '') {
                    $del = $pdo->prepare("DELETE FROM bd_catalog_product_images WHERE product_id=:pid AND sort_order=1");
                    $del->execute([':pid'=>$pidForImage]);
                } else {
                    // if exists update else insert
                    $chk = $pdo->prepare("SELECT id FROM bd_catalog_product_images WHERE product_id=:pid AND sort_order=1 LIMIT 1");
                    $chk->execute([':pid'=>$pidForImage]);
                    $row = $chk->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $up = $pdo->prepare("UPDATE bd_catalog_product_images SET image_url=:url WHERE id=:id");
                        $up->execute([':url'=>$imgUrl, ':id'=>(int)$row['id']]);
                    } else {
                        $ins = $pdo->prepare("
                            INSERT INTO bd_catalog_product_images (product_id, image_url, sort_order)
                            VALUES (:pid, :url, 1)
                        ");
                        $ins->execute([':pid'=>$pidForImage, ':url'=>$imgUrl]);
                    }
                }
            }

            header("Location: ".$_SERVER['PHP_SELF']."?tab=products");
            exit;
        }

        if ($action === 'delete_product') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new RuntimeException("Invalid ID.");
            // delete images first (avoid FK errors if any)
            $pdo->prepare("DELETE FROM bd_catalog_product_images WHERE product_id=:id")->execute([':id'=>$id]);
            $pdo->prepare("DELETE FROM bd_catalog_products WHERE id=:id")->execute([':id'=>$id]);
            flash_set("Product deleted (#{$id}).", 'warn');
            header("Location: ".$_SERVER['PHP_SELF']."?tab=products");
            exit;
        }

        if ($action === 'save_addon') {
            $id        = (int)($_POST['id'] ?? 0);
            $addon_key = trim((string)($_POST['addon_key'] ?? ''));
            $name      = trim((string)($_POST['name'] ?? ''));
            $price_usd = fnum($_POST['price_usd'] ?? 0);

            if ($addon_key === '' || $name === '') {
                throw new RuntimeException("Addon key & name wajib diisi.");
            }

            if ($id > 0) {
                $stmt = $pdo->prepare("
                    UPDATE bd_catalog_addons
                    SET addon_key=:addon_key, name=:name, price_usd=:price_usd
                    WHERE id=:id
                ");
                $stmt->execute([
                    ':addon_key'=>$addon_key,
                    ':name'=>$name,
                    ':price_usd'=>$price_usd,
                    ':id'=>$id
                ]);
                flash_set("Addon updated (#{$id}).");
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO bd_catalog_addons (addon_key, name, price_usd)
                    VALUES (:addon_key, :name, :price_usd)
                ");
                $stmt->execute([
                    ':addon_key'=>$addon_key,
                    ':name'=>$name,
                    ':price_usd'=>$price_usd,
                ]);
                $newId = (int)$pdo->lastInsertId();
                flash_set("Addon created (#{$newId}).");
            }

            header("Location: ".$_SERVER['PHP_SELF']."?tab=addons");
            exit;
        }

        if ($action === 'delete_addon') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new RuntimeException("Invalid ID.");
            $pdo->prepare("DELETE FROM bd_catalog_addons WHERE id=:id")->execute([':id'=>$id]);
            flash_set("Addon deleted (#{$id}).", 'warn');
            header("Location: ".$_SERVER['PHP_SELF']."?tab=addons");
            exit;
        }

        throw new RuntimeException("Unknown action: ".$action);

    } catch (Throwable $e) {
        flash_set("Error: ".$e->getMessage(), 'err');
        $fallbackTab = ($_POST['tab'] ?? 'products');
        header("Location: ".$_SERVER['PHP_SELF']."?tab=".urlencode((string)$fallbackTab));
        exit;
    }
}

// ====== LOAD DATA ======
$tab = $_GET['tab'] ?? 'products';
if (!in_array($tab, ['products','addons'], true)) $tab = 'products';

$search = trim((string)($_GET['q'] ?? ''));
$cat    = trim((string)($_GET['cat'] ?? ''));

// categories for dropdown
$cats = $pdo->query("SELECT DISTINCT category FROM bd_catalog_products ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// products with primary image
$where = [];
$params = [];
if ($search !== '') {
    $where[] = "(p.name LIKE :q OR p.description LIKE :q OR p.category LIKE :q)";
    $params[':q'] = "%{$search}%";
}
if ($cat !== '') {
    $where[] = "p.category = :cat";
    $params[':cat'] = $cat;
}
$whereSql = $where ? ("WHERE ".implode(" AND ", $where)) : "";

$products = [];
if ($tab === 'products') {
    $stmt = $pdo->prepare("
        SELECT
          p.id, p.name, p.price_usd, p.category, p.description, p.is_enquiry,
          img.image_url AS primary_image_url
        FROM bd_catalog_products p
        LEFT JOIN bd_catalog_product_images img
          ON img.product_id = p.id AND img.sort_order = 1
        {$whereSql}
        ORDER BY p.category, p.id
    ");
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$addons = [];
if ($tab === 'addons') {
    $addons = $pdo->query("SELECT id, addon_key, name, price_usd FROM bd_catalog_addons ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
}

// editing targets
$editProduct = null;
if ($tab === 'products' && isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid > 0) {
        $stmt = $pdo->prepare("
            SELECT
              p.id, p.name, p.price_usd, p.category, p.description, p.is_enquiry,
              img.image_url AS primary_image_url
            FROM bd_catalog_products p
            LEFT JOIN bd_catalog_product_images img
              ON img.product_id = p.id AND img.sort_order = 1
            WHERE p.id=:id
            LIMIT 1
        ");
        $stmt->execute([':id'=>$eid]);
        $editProduct = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

$editAddon = null;
if ($tab === 'addons' && isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid > 0) {
        $stmt = $pdo->prepare("SELECT id, addon_key, name, price_usd FROM bd_catalog_addons WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$eid]);
        $editAddon = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

$flash = flash_get();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin – Catalog Editor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    *{box-sizing:border-box}
    body{
      margin:0; padding:24px;
      font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background: #0b1220;
      color:#e5e7eb;
    }
    a{color:inherit}
    .wrap{max-width:1200px;margin:0 auto}
    .topbar{
      display:flex; gap:12px; align-items:center; justify-content:space-between;
      padding:14px 16px; border-radius:14px;
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.10);
      box-shadow: 0 18px 55px rgba(0,0,0,.35);
    }
    .brand{font-weight:800; letter-spacing:-.02em}
    .tabs{display:flex; gap:8px; align-items:center}
    .tab{
      padding:8px 12px; border-radius:999px; text-decoration:none;
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.10);
      opacity:.9;
    }
    .tab.active{background:#2563eb;border-color:rgba(255,255,255,.18);opacity:1}
    .grid{display:grid; grid-template-columns: 1.2fr .8fr; gap:16px; margin-top:16px}
    @media(max-width:980px){ .grid{grid-template-columns:1fr} }

    .card{
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.10);
      border-radius:16px;
      box-shadow: 0 18px 55px rgba(0,0,0,.25);
      overflow:hidden;
    }
    .card-h{
      padding:12px 14px;
      display:flex; align-items:center; justify-content:space-between; gap:10px;
      border-bottom:1px solid rgba(255,255,255,.08);
    }
    .card-h h2{margin:0;font-size:14px;letter-spacing:.02em;text-transform:uppercase;opacity:.9}
    .card-b{padding:14px}

    .flash{
      margin-top:14px; padding:12px 14px; border-radius:14px;
      border:1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
    }
    .flash.ok{border-color: rgba(34,197,94,.35)}
    .flash.warn{border-color: rgba(245,158,11,.35)}
    .flash.err{border-color: rgba(244,63,94,.35)}

    .toolbar{display:flex; gap:10px; flex-wrap:wrap}
    .input, .select, .textarea{
      width:100%;
      padding:10px 12px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,.14);
      background: rgba(11,18,32,.55);
      color:#e5e7eb;
      outline:none;
    }
    .textarea{min-height:110px; resize:vertical}
    .row{display:grid; grid-template-columns:1fr 1fr; gap:10px}
    @media(max-width:640px){ .row{grid-template-columns:1fr} }

    .btn{
      display:inline-flex; align-items:center; justify-content:center;
      padding:10px 12px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.08);
      color:#e5e7eb;
      cursor:pointer;
      text-decoration:none;
      font-weight:700;
      gap:8px;
    }
    .btn.primary{background:#2563eb;border-color:rgba(255,255,255,.18)}
    .btn.danger{background:#ef4444;border-color:rgba(255,255,255,.18)}
    .btn.ghost{background:transparent}
    .btn:active{transform:scale(.99)}

    table{
      width:100%;
      border-collapse:separate;
      border-spacing:0;
      overflow:hidden;
      border-radius:14px;
      border:1px solid rgba(255,255,255,.10);
    }
    th, td{padding:10px 10px; text-align:left; vertical-align:top}
    th{
      font-size:12px;
      opacity:.85;
      background: rgba(255,255,255,.06);
      border-bottom:1px solid rgba(255,255,255,.08);
    }
    tr td{border-bottom:1px solid rgba(255,255,255,.06)}
    tr:last-child td{border-bottom:none}
    .muted{opacity:.75; font-size:12px}
    .pill{
      display:inline-flex; padding:3px 8px; border-radius:999px;
      border:1px solid rgba(255,255,255,.16);
      background: rgba(255,255,255,.06);
      font-size:12px;
      gap:6px;
      align-items:center;
      white-space:nowrap;
    }
    .imgthumb{
      width:52px;height:40px;border-radius:10px;object-fit:cover;
      border:1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.04);
    }
    .right{display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap}
    .small{font-size:12px; opacity:.9}
    code{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace}
  </style>
</head>
<body>
<div class="wrap">
  <div class="topbar">
    <div class="brand">Admin Catalog Editor <span class="muted">/ BD</span></div>
    <div class="tabs">
      <a class="tab <?= $tab==='products'?'active':'' ?>" href="<?= h($_SERVER['PHP_SELF']) ?>?tab=products">Products</a>
      <a class="tab <?= $tab==='addons'?'active':'' ?>" href="<?= h($_SERVER['PHP_SELF']) ?>?tab=addons">Addons</a>
    </div>
  </div>

  <?php if ($flash): ?>
    <div class="flash <?= h($flash['type']) ?>"><?= h($flash['msg']) ?></div>
  <?php endif; ?>

  <div class="grid">
    <!-- LIST -->
    <div class="card">
      <div class="card-h">
        <h2><?= $tab==='products' ? 'Products' : 'Addons' ?></h2>
        <div class="right">
          <?php if ($tab==='products'): ?>
            <a class="btn primary" href="<?= h($_SERVER['PHP_SELF']) ?>?tab=products&edit=0">+ New Product</a>
          <?php else: ?>
            <a class="btn primary" href="<?= h($_SERVER['PHP_SELF']) ?>?tab=addons&edit=0">+ New Addon</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-b">
        <?php if ($tab === 'products'): ?>
          <form class="toolbar" method="get" action="">
            <input type="hidden" name="tab" value="products">
            <div style="flex:1;min-width:220px">
              <input class="input" name="q" value="<?= h($search) ?>" placeholder="Search name/desc/category...">
            </div>
            <div style="min-width:220px">
              <select class="select" name="cat">
                <option value="">All categories</option>
                <?php foreach ($cats as $c): ?>
                  <option value="<?= h((string)$c) ?>" <?= $cat===(string)$c?'selected':'' ?>><?= h((string)$c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button class="btn" type="submit">Filter</button>
            <a class="btn ghost" href="<?= h($_SERVER['PHP_SELF']) ?>?tab=products">Reset</a>
          </form>

          <div style="height:12px"></div>

          <table>
            <thead>
              <tr>
                <th style="width:70px">Image</th>
                <th>Product</th>
                <th style="width:140px">Price (USD)</th>
                <th style="width:170px">Category</th>
                <th style="width:150px">Flags</th>
                <th style="width:160px"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$products): ?>
                <tr><td colspan="6" class="muted">No products found.</td></tr>
              <?php else: foreach ($products as $p): ?>
                <tr>
                  <td>
                    <?php if (!empty($p['primary_image_url'])): ?>
                      <img class="imgthumb" src="<?= h((string)$p['primary_image_url']) ?>" alt="">
                    <?php else: ?>
                      <div class="imgthumb"></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div style="font-weight:800"><?= h((string)$p['name']) ?></div>
                    <div class="muted">#<?= (int)$p['id'] ?> · <?= h(mb_strimwidth((string)$p['description'], 0, 90, '…')) ?></div>
                  </td>
                  <td><span class="pill">$<?= number_format((float)$p['price_usd'], 2) ?></span></td>
                  <td><span class="pill"><?= h((string)$p['category']) ?></span></td>
                  <td>
                    <?php if ((int)$p['is_enquiry'] === 1): ?>
                      <span class="pill">is_enquiry</span>
                    <?php else: ?>
                      <span class="muted">—</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="right">
                      <a class="btn" href="<?= h($_SERVER['PHP_SELF']) ?>?tab=products&edit=<?= (int)$p['id'] ?>">Edit</a>
                      <form method="post" action="" onsubmit="return confirm('Delete product #<?= (int)$p['id'] ?>?');">
                        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                        <input type="hidden" name="tab" value="products">
                        <input type="hidden" name="action" value="delete_product">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn danger" type="submit">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th style="width:90px">ID</th>
                <th>Addon</th>
                <th style="width:140px">Price (USD)</th>
                <th style="width:160px"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$addons): ?>
                <tr><td colspan="4" class="muted">No addons found.</td></tr>
              <?php else: foreach ($addons as $a): ?>
                <tr>
                  <td><span class="pill">#<?= (int)$a['id'] ?></span></td>
                  <td>
                    <div style="font-weight:800"><?= h((string)$a['name']) ?></div>
                    <div class="muted"><code><?= h((string)$a['addon_key']) ?></code></div>
                  </td>
                  <td><span class="pill">$<?= number_format((float)$a['price_usd'], 2) ?></span></td>
                  <td>
                    <div class="right">
                      <a class="btn" href="<?= h($_SERVER['PHP_SELF']) ?>?tab=addons&edit=<?= (int)$a['id'] ?>">Edit</a>
                      <form method="post" action="" onsubmit="return confirm('Delete addon #<?= (int)$a['id'] ?>?');">
                        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
                        <input type="hidden" name="tab" value="addons">
                        <input type="hidden" name="action" value="delete_addon">
                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                        <button class="btn danger" type="submit">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- EDIT FORM -->
    <div class="card">
      <div class="card-h">
        <h2><?= $tab==='products' ? 'Edit Product' : 'Edit Addon' ?></h2>
        <div class="right">
          <a class="btn ghost" href="<?= h($_SERVER['PHP_SELF']) ?>?tab=<?= h($tab) ?>">Close</a>
        </div>
      </div>
      <div class="card-b">
        <?php if ($tab === 'products'): ?>
          <?php
            $p = $editProduct ?: [
              'id'=>0,'name'=>'','price_usd'=>'','category'=>'','description'=>'','is_enquiry'=>0,'primary_image_url'=>''
            ];
          ?>
          <form method="post" action="">
            <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
            <input type="hidden" name="tab" value="products">
            <input type="hidden" name="action" value="save_product">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">

            <div class="row">
              <div>
                <label class="small">Name</label>
                <input class="input" name="name" value="<?= h((string)$p['name']) ?>" placeholder="Product name">
              </div>
              <div>
                <label class="small">Price (USD)</label>
                <input class="input" name="price_usd" value="<?= h((string)$p['price_usd']) ?>" placeholder="e.g. 35">
              </div>
            </div>

            <div style="height:10px"></div>

            <div class="row">
              <div>
                <label class="small">Category</label>
                <input class="input" name="category" value="<?= h((string)$p['category']) ?>" placeholder="e.g. Fun Diving">
                <div class="muted" style="margin-top:6px">Tip: category harus konsisten biar grouping rapi.</div>
              </div>
              <div>
                <label class="small">Primary Image URL (sort_order=1)</label>
                <input class="input" name="primary_image_url" value="<?= h((string)($p['primary_image_url'] ?? '')) ?>" placeholder="https://...jpg">
              </div>
            </div>

            <div style="height:10px"></div>

            <div>
              <label class="small">Description</label>
              <textarea class="textarea" name="description" placeholder="Short description..."><?= h((string)$p['description']) ?></textarea>
            </div>

            <div style="height:10px"></div>

            <label style="display:flex;gap:10px;align-items:center">
              <input type="checkbox" name="is_enquiry" value="1" <?= ((int)$p['is_enquiry']===1?'checked':'') ?>>
              <span class="small">is_enquiry (jika 1 → tombol enquiry / WA)</span>
            </label>

            <div style="height:14px"></div>
            <button class="btn primary" type="submit">Save Product</button>
          </form>

          <div class="muted" style="margin-top:12px">
            Catetan nerdy: kalau field di DB beda (misalnya column name lain), bilang aja—kita adjust 2 menit.
          </div>

        <?php else: ?>
          <?php
            $a = $editAddon ?: ['id'=>0,'addon_key'=>'','name'=>'','price_usd'=>''];
          ?>
          <form method="post" action="">
            <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
            <input type="hidden" name="tab" value="addons">
            <input type="hidden" name="action" value="save_addon">
            <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">

            <div>
              <label class="small">Addon Key (unique)</label>
              <input class="input" name="addon_key" value="<?= h((string)$a['addon_key']) ?>" placeholder="e.g. equipment_rental">
            </div>

            <div style="height:10px"></div>

            <div class="row">
              <div>
                <label class="small">Name</label>
                <input class="input" name="name" value="<?= h((string)$a['name']) ?>" placeholder="e.g. Equipment Rental">
              </div>
              <div>
                <label class="small">Price (USD)</label>
                <input class="input" name="price_usd" value="<?= h((string)$a['price_usd']) ?>" placeholder="e.g. 5">
              </div>
            </div>

            <div style="height:14px"></div>
            <button class="btn primary" type="submit">Save Addon</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div style="height:18px"></div>
  <div class="muted">
    Security reminder (sok bijak dikit, tapi penting): taruh file ini di folder yang nggak public, atau minimal pake BasicAuth + strong password. Jangan “admin/admin”, itu dosa kecil yang cepat jadi dosa besar. 🙂
  </div>
</div>
</body>
</html>
