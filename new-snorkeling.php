<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

ini_set('display_errors', '1');
error_reporting(E_ALL);

// ✅ SMART FINDER main-cart.php
$candidates = [
  __DIR__ . '/template/database/main-cart.php',                 // /main-website/template/...
  dirname(__DIR__) . '/template/database/main-cart.php',        // /balidiving.com/template/...
  dirname(__DIR__, 2) . '/template/database/main-cart.php',     // /public_html/template/...
  $_SERVER['DOCUMENT_ROOT'] . '/template/database/main-cart.php'
];

$cartDb = null;
foreach ($candidates as $p) {
  if (is_file($p)) { $cartDb = $p; break; }
}

if (!$cartDb) {
  http_response_code(500);
  echo "main-cart.php not found. Tried:<br><pre>" . htmlspecialchars(print_r($candidates, true)) . "</pre>";
  exit;
}

require $cartDb; // define $pdo


// ===========================
// LOAD PRODUCTS (Snorkeling only)
// ===========================
$productsSql = "
  SELECT id, name, price_usd, category, description, is_enquiry
  FROM bd_catalog_products
  WHERE category = 'Snorkeling'
  ORDER BY id
";
$snorkelingProducts = $pdo->query($productsSql)->fetchAll(PDO::FETCH_ASSOC);

// ===========================
// LOAD FIRST IMAGE PER PRODUCT
// ===========================
$imagesSql = "
  SELECT product_id, image_url
  FROM bd_catalog_product_images
  WHERE sort_order = 1
  ORDER BY product_id
";
$imageRows = $pdo->query($imagesSql)->fetchAll(PDO::FETCH_ASSOC);
$productImages = [];
foreach ($imageRows as $row) {
  $productImages[(int)$row['product_id']] = (string)$row['image_url'];
}

// ===========================
// LOAD ADDONS (optional untuk cart.js)
// ===========================
$addonsSql = "
  SELECT addon_key, name, price_usd
  FROM bd_catalog_addons
  ORDER BY id
";
$addonsRows = $pdo->query($addonsSql)->fetchAll(PDO::FETCH_ASSOC);
$addonMap = [];
foreach ($addonsRows as $row) {
  $addonMap[(string)$row['addon_key']] = [
    'name'  => (string)$row['name'],
    'price' => (float)$row['price_usd'],
  ];
}

// ===========================
// PRODUCT MAP untuk JS
// ===========================
$productMap = [];
foreach ($snorkelingProducts as $p) {
  $id = (int)$p['id'];
  $productMap[$id] = [
    'name'       => (string)$p['name'],
    'price'      => (float)$p['price_usd'],
    'category'   => (string)$p['category'],
    'desc'       => (string)$p['description'],
    'is_enquiry' => (bool)$p['is_enquiry'],
  ];
}

// ===========================
// Booking ID (tampilan)
// ===========================
function bd_generate_booking_id(): string {
  $now = new DateTime('now', new DateTimeZone('Asia/Makassar'));
  $rand = random_int(1000, 9999);
  return "BDV-" . $now->format('Ymd') . "-{$rand}";
}
$BOOKING_ID = bd_generate_booking_id();

// ===========================
// USD to IDR fallback
// ===========================
$USD_TO_IDR = isset($USD_TO_IDR) && is_numeric($USD_TO_IDR) ? (float)$USD_TO_IDR : 15800;

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bali Diving – Snorkeling</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    *{box-sizing:border-box}
    body{
      margin:0;padding:24px;
      font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      background:linear-gradient(135deg,#0070d3 0%,#3552c8 100%);
      min-height:100vh;
      display:flex;justify-content:center;
    }
    .wrap{width:100%;max-width:1200px;display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start}
    .products{flex:1 1 320px}
    .section{
      background:rgba(255,255,255,0.08);
      border:1px solid rgba(255,255,255,0.12);
      border-radius:16px;
      padding:18px;
      backdrop-filter:blur(10px);
    }
    .title{color:#fff;font-size:1.6rem;font-weight:800;margin:0 0 12px}
    .intro{color:#f9fafb;opacity:.9;line-height:1.6;margin:0 0 16px;font-size:.92rem}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
    .card{
      background:#fff;border-radius:12px;padding:14px;
      box-shadow:0 4px 10px rgba(15,23,42,.2);
      display:flex;flex-direction:column;gap:8px;
      transition:transform .15s ease, box-shadow .15s ease;
    }
    .card:hover{transform:translateY(-3px);box-shadow:0 8px 18px rgba(15,23,42,.28)}
    .thumb{width:100%;height:140px;border-radius:10px;object-fit:cover;background:#e5e7eb}
    .name{font-weight:800;color:#111827}
    .price{font-weight:900;color:#0070d3;font-size:1.05rem}
    .desc{font-size:.82rem;color:#6b7280;line-height:1.4;min-height:2.2em}
    .btn{
      width:100%;padding:10px 12px;border-radius:10px;border:none;
      font-weight:800;cursor:pointer;background:#0070d3;color:#fff;
    }
    .btn:hover{background:#0654a5}
    .btn:active{transform:scale(.99)}
    /* cart styles (minimal) */
    .floating-cart{
      position:sticky;top:16px;align-self:flex-start;
      width:360px;max-width:100%;
      background:#fff;border-radius:18px;
      box-shadow:0 22px 60px rgba(15,23,42,.45);
      overflow:hidden;display:flex;flex-direction:column;
    }
    .cart-header{padding:14px 16px;background:#0070d3;color:#fff;display:flex;justify-content:space-between;gap:10px}
    .cart-title{margin:0;font-size:1.05rem;font-weight:900}
    .cart-booking-label{font-size:.72rem;opacity:.9}
    .cart-booking-id{font-size:.85rem;font-weight:800}
    .cart-count{background:rgba(15,23,42,.18);padding:4px 10px;border-radius:999px;font-size:.78rem;font-weight:800;white-space:nowrap}
    .cart-items{max-height:420px;overflow:auto;padding:12px 12px;background:#f9fafb}
    .cart-footer{padding:14px 14px 16px;border-top:1px solid #e5e7eb}
    .summary-row{display:flex;justify-content:space-between;align-items:baseline}
    .summary-label{font-weight:900;color:#111827}
    .summary-amounts{text-align:right;font-weight:900;color:#0070d3}
    .summary-amounts small{display:block;font-size:.78rem;color:#6b7280;font-weight:600}
    .customer-fields{margin-top:10px;display:flex;flex-direction:column;gap:8px}
    .customer-fields label{font-size:.8rem;color:#4b5563;display:flex;flex-direction:column;gap:4px}
    .customer-fields input{padding:8px 10px;border:1px solid #d1d5db;border-radius:10px;font-size:.9rem}
    .pay-btn{
      margin-top:10px;width:100%;padding:12px;border-radius:12px;border:none;
      font-weight:900;background:#0070d3;color:#fff;cursor:pointer;
    }
    .pay-btn:hover{background:#0654a5}
    .cart-msg{
      position:fixed;right:16px;top:16px;z-index:9999;
      background:#111827;color:#f9fafb;padding:10px 12px;border-radius:10px;
      box-shadow:0 15px 40px rgba(0,0,0,.35);font-size:.85rem
    }
    .empty-cart-message{color:#6b7280;text-align:center;padding:18px 10px;font-size:.9rem}
    @media(max-width:900px){
      body{padding:12px}
      .wrap{flex-direction:column}
      .floating-cart{position:static;width:100%}
    }
  </style>
  
</head>
<body>

<div class="wrap">

  <section class="products">
    <div class="section">
      <h1 class="title">Snorkeling</h1>
      <p class="intro">Pick your snorkeling activity, then set your <b>Activity Plan</b> date inside the cart.</p>

      <div class="grid">
        <?php foreach ($snorkelingProducts as $p): 
          $pid = (int)$p['id'];
          $img = $productImages[$pid] ?? '';
          $name = (string)$p['name'];
          $desc = (string)$p['description'];
          $price = (float)$p['price_usd'];
          $isEnquiry = (bool)$p['is_enquiry'];
        ?>
          <article class="card">
            <?php if ($img !== ''): ?>
              <img class="thumb" src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="">
            <?php else: ?>
              <div class="thumb"></div>
            <?php endif; ?>

            <div class="name"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></div>
            <div class="price">$<?= number_format($price, 2) ?></div>
            <div class="desc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></div>

            <?php if ($isEnquiry): ?>
              <a class="btn" style="text-decoration:none;text-align:center;background:#23a0b4"
                 href="https://wa.me/6281234567890?text=Hello%20Bali%20Diving,%20I%20want%20to%20enquire:%20<?= urlencode($name) ?>">
                Enquire
              </a>
            <?php else: ?>
              <button class="btn" type="button" onclick="addToCart(<?= $pid ?>)">Add to Cart</button>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- CART -->
  <aside class="floating-cart">
    <header class="cart-header">
      <div>
        <h2 class="cart-title">Your Booking Plan</h2>
        <div class="cart-booking-label">Booking ID:</div>
        <div class="cart-booking-id">
          <span id="cartBookingId"><?= htmlspecialchars($BOOKING_ID, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
      </div>
      <div class="cart-count" id="cartCount">0 items</div>
    </header>

    <div class="cart-items" id="cartItems"></div>

    <footer class="cart-footer">
      <div class="summary-row">
        <div class="summary-label">Total</div>
        <div class="summary-amounts">
          <span id="totalUsd">$0.00</span>
          <small id="totalIdr">Rp 0</small>
        </div>
      </div>

      <div class="customer-fields">
        <label>
          Full Name
          <input type="text" id="custName" placeholder="Your full name">
        </label>
        <label>
          Email Address
          <input type="email" id="custEmail" placeholder="you@example.com">
        </label>
      </div>

      <button class="pay-btn" type="button" onclick="checkout()">Checkout</button>
    </footer>
  </aside>

</div>

<script>
  window.PRODUCT_MAP    = <?= json_encode($productMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  window.PRODUCT_IMAGES = <?= json_encode($productImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  window.ADDONS         = <?= json_encode($addonMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  window.USD_TO_IDR     = <?= json_encode($USD_TO_IDR, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<!-- pastikan file cart.js ada di folder yang sama dengan new-snorkeling.php -->
<script src="cart/cart.js?v=17xx"></script>

</body>
</html>
