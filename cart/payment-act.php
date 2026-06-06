<?php
// /cart/payment-act.php  (APPLIED: Guest Phone/WA + Certificate Level)
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

require __DIR__ . '/../template/database/main-cart.php';

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function idr($n): string { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function usd($n): string { return 'US$ ' . number_format((float)$n, 2); }
function fmtDate($v): string {
  $s = trim((string)$v);
  if ($s === '') return '-';
  $t = strtotime($s);
  return $t ? date('d M Y', $t) : $s;
}

$MY_BOOKING_URL = 'https://balidiving.com/cart/my-booking';

// --- booking id ---
$bookingId = trim((string)($_GET['booking_id'] ?? ''));
if ($bookingId === '') { header("Location: {$MY_BOOKING_URL}"); exit; }

// --- load booking ---
$stmt = $pdo->prepare("SELECT * FROM bd_bookings WHERE booking_id = :bid LIMIT 1");
$stmt->execute([':bid' => $bookingId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$booking) { header("Location: {$MY_BOOKING_URL}"); exit; }

// --- items ---
$items = $pdo->prepare("SELECT * FROM bd_booking_items WHERE booking_id = :bid ORDER BY id ASC");
$items->execute([':bid' => $bookingId]);
$list = $items->fetchAll(PDO::FETCH_ASSOC);

// ============================================================
// ✅ CATEGORY MAP (product_id -> category)
// ============================================================
$productIds = [];
foreach ($list as $it) {
  $pid = (int)($it['product_id'] ?? $it['pid'] ?? $it['catalog_product_id'] ?? $it['catalog_id'] ?? $it['id_product'] ?? 0);
  if ($pid > 0) $productIds[$pid] = true;
}
$categoryMap = [];
if ($productIds) {
  $ids = array_keys($productIds);
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $sqlCat = "SELECT id, category FROM bd_catalog_products WHERE id IN ($placeholders)";
  $stCat = $pdo->prepare($sqlCat);
  $stCat->execute($ids);
  while ($row = $stCat->fetch(PDO::FETCH_ASSOC)) {
    $categoryMap[(int)$row['id']] = (string)($row['category'] ?? '');
  }
}
$getCategoryForItem = function(array $it) use ($categoryMap): string {
  $pid = (int)($it['product_id'] ?? $it['pid'] ?? $it['catalog_product_id'] ?? $it['catalog_id'] ?? $it['id_product'] ?? 0);
  $cat = '';
  if ($pid > 0 && isset($categoryMap[$pid])) $cat = (string)$categoryMap[$pid];
  if ($cat === '') $cat = (string)($it['category'] ?? $it['product_category'] ?? '');
  $cat = trim($cat);
  return $cat !== '' ? $cat : '-';
};

// ============================================================
// ✅ ADDONS (ROBUST):
// 1) Try relation table: bd_booking_item_addons (if exists)
// 2) Fallback: scan any item columns containing "addon"
// Supports: JSON / comma-separated / PHP serialized
// ============================================================

function parseAddonRaw($v): array {
  if ($v === null) return [];
  $s = trim((string)$v);
  if ($s === '' || $s === '[]' || $s === '{}') return [];

  // JSON
  if ($s !== '' && ($s[0] === '[' || $s[0] === '{')) {
    $j = json_decode($s, true);
    if (json_last_error() === JSON_ERROR_NONE) {
      if (!is_array($j)) return [];
      if (array_keys($j) !== range(0, count($j) - 1)) $j = [$j]; // wrap assoc object
      return $j;
    }
  }

  // PHP serialize (legacy)
  if (function_exists('str_starts_with') && (str_starts_with($s, 'a:') || str_starts_with($s, 's:') || str_starts_with($s, 'O:'))) {
    try {
      $u = @unserialize($s);
      if (is_array($u)) return $u;
    } catch (Throwable $e) {}
  } else {
    // PHP < 8 fallback
    if (preg_match('/^(a:|s:|O:)/', $s)) {
      try {
        $u = @unserialize($s);
        if (is_array($u)) return $u;
      } catch (Throwable $e) {}
    }
  }

  // comma-separated
  $parts = array_map('trim', explode(',', $s));
  $parts = array_values(array_filter($parts, fn($x) => $x !== ''));
  return $parts;
}

function normalizeAddons(array $raw): array {
  $out = [];
  foreach ($raw as $x) {
    if (is_string($x)) {
      $k = trim($x);
      if ($k === '') continue;
      $out[] = ['key' => $k, 'name' => $k, 'price_usd' => null, 'price_idr' => null];
      continue;
    }
    if (is_array($x)) {
      $key  = trim((string)($x['key'] ?? $x['id'] ?? $x['code'] ?? $x['slug'] ?? ''));
      $name = trim((string)($x['name'] ?? $x['title'] ?? $key));
      $pu   = isset($x['price_usd']) ? (float)$x['price_usd'] : (isset($x['usd']) ? (float)$x['usd'] : null);
      $pi   = isset($x['price_idr']) ? (float)$x['price_idr'] : (isset($x['idr']) ? (float)$x['idr'] : null);

      $out[] = [
        'key' => $key !== '' ? $key : ($name !== '' ? $name : 'addon'),
        'name' => $name !== '' ? $name : ($key !== '' ? $key : 'Addon'),
        'price_usd' => $pu,
        'price_idr' => $pi,
      ];
    }
  }
  return $out;
}

// --------- 1) relation table map: booking_item_id => addons[]
$itemAddonsByItemId = [];
try {
  $st = $pdo->prepare("
    SELECT
      booking_item_id,
      addon_key,
      addon_name,
      price_usd,
      price_idr
    FROM bd_booking_item_addons
    WHERE booking_id = :bid
    ORDER BY booking_item_id ASC, id ASC
  ");
  $st->execute([':bid' => $bookingId]);

  while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
    $bidItemId = (int)($r['booking_item_id'] ?? 0);
    if ($bidItemId <= 0) continue;

    $k  = trim((string)($r['addon_key'] ?? ''));
    $n  = trim((string)($r['addon_name'] ?? $k));
    $pu = array_key_exists('price_usd', $r) ? (float)$r['price_usd'] : null;
    $pi = array_key_exists('price_idr', $r) ? (float)$r['price_idr'] : null;

    $itemAddonsByItemId[$bidItemId][] = [
      'key' => $k !== '' ? $k : ($n !== '' ? $n : 'addon'),
      'name' => $n !== '' ? $n : ($k !== '' ? $k : 'Addon'),
      'price_usd' => $pu,
      'price_idr' => $pi,
    ];
  }
} catch (Throwable $e) {
  // table not found -> ignore
}

// --------- 2) fallback scan item columns
function getAddonPayloadFromItemScan(array $it): array {
  $candidates = [
    $it['addons_json'] ?? null,
    $it['addons'] ?? null,
    $it['addon_keys'] ?? null,
    $it['selected_addons'] ?? null,
    $it['add_ons'] ?? null,
    $it['addons_selected'] ?? null,
    $it['addons_selected_json'] ?? null,
    $it['addon_selected'] ?? null,
    $it['addon'] ?? null,
    $it['a'] ?? null, // cart style payload
  ];
  foreach ($candidates as $v) {
    if ($v !== null && trim((string)$v) !== '') return parseAddonRaw($v);
  }

  foreach ($it as $k => $v) {
    if (stripos((string)$k, 'addon') !== false) {
      if ($v !== null && trim((string)$v) !== '') {
        $parsed = parseAddonRaw($v);
        if ($parsed) return $parsed;
      }
    }
  }
  return [];
}

// build idx(row) => addons[]
$itemAddonsByRow = [];
foreach ($list as $idx => $it) {
  $bookingItemId = (int)($it['id'] ?? $it['booking_item_id'] ?? 0);

  if ($bookingItemId > 0 && isset($itemAddonsByItemId[$bookingItemId])) {
    $itemAddonsByRow[$idx] = $itemAddonsByItemId[$bookingItemId];
    continue;
  }

  $raw = getAddonPayloadFromItemScan($it);
  $itemAddonsByRow[$idx] = normalizeAddons($raw);
}

// =========================
// COMPANY / BANK INFO
// =========================
$companyName    = "Bali Diving";
$companyEmail   = "sales@balidiving.com";
$companyWebsite = "balidiving.com";
$companyPhone   = "+62 878-6119-0174";
$companyAddress = "Bali, Indonesia";

$bcaAccountName   = "Bali Sunfish Safaris PT";
$bcaAccountNumber = "6700717273";
$bcaSwiftCode     = "CENAIDJA"; // optional

// =========================
// TOTALS
// =========================
$totalUsd = (float)($booking['total_usd'] ?? 0);
$totalIdr = (float)($booking['total_idr'] ?? 0);

if ($totalIdr <= 0 && $list) {
  $sum = 0.0;
  foreach ($list as $it) $sum += (float)($it['line_total_idr'] ?? 0);
  if ($sum > 0) $totalIdr = $sum;
}

$fxApprox = null;
if ($totalUsd > 0 && $totalIdr > 0) $fxApprox = $totalIdr / $totalUsd;

$createdAt = date('d M Y, H:i') . " (GMT+8)";

// =========================
// PAYMENT STATUS BADGE
// =========================
$rawStatus = (string)($booking['payment_status'] ?? $booking['status_payment'] ?? $booking['status'] ?? 'Pending');
$status = strtolower(trim($rawStatus));

$badgeLabel = 'Pending';
$badgeTone  = 'pending';

if (in_array($status, ['paid', 'settled', 'success', 'completed'], true)) {
  $badgeLabel = 'Paid';
  $badgeTone  = 'paid';
} elseif (in_array($status, ['cancelled', 'canceled', 'void', 'failed'], true)) {
  $badgeLabel = 'Cancelled';
  $badgeTone  = 'cancelled';
} elseif (in_array($status, ['refunded'], true)) {
  $badgeLabel = 'Refunded';
  $badgeTone  = 'refunded';
} else {
  $badgeLabel = $rawStatus ? ucfirst($rawStatus) : 'Pending';
  $badgeTone  = 'pending';
}

// =========================
// CUSTOMER (APPLIED: phone + cert level)
// =========================
$customerName  = (string)($booking['customer_name'] ?? $booking['full_name'] ?? $booking['name'] ?? '-');
$customerEmail = (string)($booking['customer_email'] ?? $booking['email'] ?? '-');

// NEW (DB columns per previous ALTER)
$customerPhone = (string)($booking['customer_phone'] ?? $booking['phone'] ?? $booking['customer_whatsapp'] ?? $booking['whatsapp'] ?? '-');
$customerCert  = (string)($booking['customer_cert_level'] ?? $booking['cert_level'] ?? $booking['certificate_level'] ?? 'Beginner / No Certificate');
$customerPhone = trim($customerPhone) !== '' ? $customerPhone : '-';
$customerCert  = trim($customerCert) !== '' ? $customerCert : 'Beginner / No Certificate';

// WhatsApp
$waNumber = preg_replace('/\D+/', '', $companyPhone);
$waText = rawurlencode("Hello Bali Diving, I have transferred the payment.\nBooking ID: {$bookingId}\nPlease help confirm. Thank you.");
$waLink = $waNumber ? "https://wa.me/{$waNumber}?text={$waText}" : "#";

// Payment methods (most common first)
$paymentMethods = [
  'Cash'            => 'Cash',
  'Credit Card'     => 'Credit Card',
  'Virtual Account' => 'Virtual Account',
  'QRIS'            => 'QRIS',
  'Paypal'          => 'PayPal',
  'Wise'            => 'Wise',
  'Retail'          => 'Retail',
  'Bitcoin'         => 'Bitcoin',
];

/**
 * Custom links per method
 * - full  : Full payment
 * - dep50 : 50% deposit
 *
 * IMPORTANT: EDIT THESE to your real endpoints.
 */
$paymentLinks = [
  'Credit Card' => [
    'full'  => 'https://balidiving.com/pay/card?booking_id=',
    'dep50' => 'https://balidiving.com/pay/card-deposit?booking_id=',
  ],
  'Virtual Account' => [
    'full'  => 'https://balidiving.com/pay/va?booking_id=',
    'dep50' => 'https://balidiving.com/pay/va-deposit?booking_id=',
  ],
  'QRIS' => [
    'full'  => 'https://balidiving.com/pay/qris?booking_id=',
    'dep50' => 'https://balidiving.com/pay/qris-deposit?booking_id=',
  ],
  'Paypal' => [
    'full'  => 'https://balidiving.com/pay/paypal?booking_id=',
    'dep50' => 'https://balidiving.com/pay/paypal-deposit?booking_id=',
  ],
  'Wise' => [
    'full'  => 'https://balidiving.com/pay/wise?booking_id=',
    'dep50' => 'https://balidiving.com/pay/wise-deposit?booking_id=',
  ],
  'Retail' => [
    'full'  => 'https://balidiving.com/pay/retail?booking_id=',
    'dep50' => 'https://balidiving.com/pay/retail-deposit?booking_id=',
  ],
  'Bitcoin' => [
    'full'  => 'https://balidiving.com/pay/bitcoin?booking_id=',
    'dep50' => 'https://balidiving.com/pay/bitcoin-deposit?booking_id=',
  ],
  // Cash handled separately
];

$totalIdrFloat = (float)$totalIdr;
$deposit50Idr  = (float)round($totalIdrFloat * 0.5);
$balance50Idr  = (float)($totalIdrFloat - $deposit50Idr);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Invoice <?= h($bookingId) ?> | <?= h($companyName) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">

  <style>
    :root{
      --bg:#f5f7fb; --card:#ffffff; --ink:#0f172a; --muted:#64748b; --line:#e2e8f0;
      --soft:#f8fafc; --brand:#1d4ed8; --brand2:#0ea5e9; --shadow:0 12px 28px rgba(2,6,23,.08);
      --radius:16px;

      --paid-bg:#ecfdf5; --paid-ink:#065f46; --paid-bd:#a7f3d0;
      --pend-bg:#fff7ed; --pend-ink:#9a3412; --pend-bd:#fed7aa;
      --canc-bg:#fef2f2; --canc-ink:#991b1b; --canc-bd:#fecaca;
      --ref-bg:#eff6ff;  --ref-ink:#1e3a8a; --ref-bd:#bfdbfe;
    }
    *{box-sizing:border-box}
    body{
      margin:0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Helvetica Neue", sans-serif;
      color:var(--ink); background:var(--bg); line-height:1.45; -webkit-print-color-adjust:exact; print-color-adjust:exact;
    }
    .wrap{max-width:980px;margin:28px auto;padding:0 16px 40px}
    .top{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;margin-bottom:14px}
    .brand{display:flex;gap:12px;align-items:center}
    .mark{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--brand),var(--brand2));box-shadow:0 12px 24px rgba(29,78,216,.18);position:relative}
    .mark:after{content:"";position:absolute;inset:10px;border:2px solid rgba(255,255,255,.75);border-radius:10px;transform:rotate(8deg)}
    .brand h1{margin:0;font-size:16px;font-weight:900;letter-spacing:.2px}
    .brand .sub{margin-top:2px;font-size:12.5px;color:var(--muted)}
    .actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end}
    .btn{
      appearance:none;border:1px solid var(--line);background:#fff;color:var(--ink);
      padding:9px 12px;border-radius:12px;font-weight:800;font-size:13px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;
    }
    .btn.primary{border-color:rgba(29,78,216,.25);background:linear-gradient(180deg,#2563eb,#1d4ed8);color:#fff;box-shadow:0 12px 22px rgba(29,78,216,.18)}
    .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
    .card-h{
      padding:16px 18px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fff,#fbfdff);
      display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;align-items:flex-start
    }
    .title{margin:0;font-size:18px;font-weight:900;letter-spacing:.2px}
    .meta{margin:4px 0 0;font-size:12.5px;color:var(--muted)}
    .rightmeta{display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex:1;min-width:240px;text-align:right}
    .badge{
      display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:900;
      border:1px solid var(--line);background:#fff;color:var(--muted);white-space:nowrap
    }
    .dot{width:8px;height:8px;border-radius:999px;background:#94a3b8}
    .badge.paid{background:var(--paid-bg);color:var(--paid-ink);border-color:var(--paid-bd)} .badge.paid .dot{background:#10b981}
    .badge.pending{background:var(--pend-bg);color:var(--pend-ink);border-color:var(--pend-bd)} .badge.pending .dot{background:#fb923c}
    .badge.cancelled{background:var(--canc-bg);color:var(--canc-ink);border-color:var(--canc-bd)} .badge.cancelled .dot{background:#ef4444}
    .badge.refunded{background:var(--ref-bg);color:var(--ref-ink);border-color:var(--ref-bd)} .badge.refunded .dot{background:#3b82f6}

    .card-b{padding:18px}
    .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:14px}
    @media(max-width:860px){.grid{grid-template-columns:1fr}.rightmeta{text-align:left;align-items:flex-start}}
    .box{border:1px solid var(--line);background:var(--soft);border-radius:14px;padding:14px}
    .kv{display:grid;grid-template-columns:140px 1fr;gap:8px 12px;font-size:13.5px}
    .k{color:var(--muted)} .v{font-weight:800}
    .amt-big{font-size:22px;font-weight:950;letter-spacing:.2px}
    .amt-sub{font-size:12.5px;color:var(--muted)}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px 12px;border-bottom:1px solid var(--line);vertical-align:top;font-size:13.5px}
    th{background:var(--soft);color:#334155;font-weight:900;font-size:12.5px;letter-spacing:.2px}
    td.num,th.num{text-align:right}
    .small{font-size:12.5px;color:var(--muted)}
    .section-title{margin:0 0 10px;font-size:13px;font-weight:950;letter-spacing:.2px}
    .notice{border:1px solid var(--line);background:#fff;border-radius:14px;padding:14px;display:flex;gap:12px;align-items:flex-start}
    .info{width:34px;height:34px;border-radius:12px;background:rgba(29,78,216,.08);color:var(--brand);display:grid;place-items:center;font-weight:950;flex:0 0 auto}

    .pmwrap{margin-top:12px}
    .pm-toggle{
      display:flex;align-items:center;gap:10px;flex-wrap:wrap;
      padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:var(--soft);
    }
    .pm-toggle input{width:18px;height:18px;accent-color:var(--brand);}
    .pm-toggle label{font-size:13px;font-weight:900;color:var(--ink);cursor:pointer}
    .pm-hint{font-size:12.5px;color:var(--muted)}
    .pm-note{
      margin-top:10px;
      padding:10px 12px;
      border-radius:12px;
      border:1px solid rgba(29,78,216,.18);
      background: rgba(29,78,216,.06);
      font-size:12.5px;
      color:#0f172a;
      display:none;
    }
    .pm-note.show{display:block}

    .modal-backdrop{
      position:fixed; inset:0;
      background:rgba(15,23,42,.55);
      display:none;
      align-items:center;
      justify-content:center;
      padding:16px;
      z-index:9999;
    }
    .modal-backdrop.show{display:flex}
    .modal{
      width:min(560px, 100%);
      background:#fff;
      border:1px solid var(--line);
      border-radius:18px;
      box-shadow:0 22px 60px rgba(2,6,23,.25);
      overflow:hidden;
    }
    .modal-h{
      padding:14px 16px;
      border-bottom:1px solid var(--line);
      background:linear-gradient(180deg,#fff,#fbfdff);
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
    }
    .modal-h .ttl{font-weight:950;letter-spacing:.2px}
    .modal-x{
      border:1px solid var(--line);
      background:#fff;
      width:36px;height:36px;
      border-radius:12px;
      cursor:pointer;
      font-weight:950;
    }
    .modal-b{padding:16px}
    .modal-b p{margin:0 0 12px;color:var(--muted);font-size:12.5px}
    .select{
      appearance:none;border:1px solid var(--line);background:#fff;color:var(--ink);
      padding:10px 12px;border-radius:12px;font-weight:900;font-size:13px;outline:none;
      width:100%;
    }
    .seg{
      margin-top:12px;
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      padding:10px 12px;
      border:1px solid var(--line);
      border-radius:12px;
      background:var(--soft);
    }
    .seg .opt{
      display:flex;align-items:center;gap:8px;
      padding:8px 10px;border-radius:12px;
      border:1px solid rgba(148,163,184,.35);
      background:#fff;
      cursor:pointer;
      user-select:none;
      font-size:13px;
      font-weight:900;
    }
    .seg input{accent-color:var(--brand)}
    .seg .desc{font-size:12.5px;color:var(--muted);font-weight:700;margin-left:26px;margin-top:-4px}
    .modal-actions{
      display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;
      padding:14px 16px;border-top:1px solid var(--line);background:#fff;
    }
    .btn.ghost{background:#fff}

    .bank-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:12px}
    @media(max-width:860px){.bank-grid{grid-template-columns:1fr}}
    .bank-line{display:flex;justify-content:space-between;gap:12px;font-size:13.5px;padding:6px 0;border-bottom:1px dashed rgba(148,163,184,.5)}
    .bank-line:last-child{border-bottom:none}
    .bank-line span:first-child{color:var(--muted)} .bank-line span:last-child{font-weight:900}
    .footer{margin-top:12px;color:var(--muted);font-size:12px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}

    @page{size:A4;margin:12mm}
    @media print{body{background:#fff}.wrap{margin:0;padding:0;max-width:none}.actions{display:none}.card{box-shadow:none;border-radius:0;border:none}.box,.notice,.modal-backdrop{display:none!important}th{background:#f1f5f9!important}}
  </style>
</head>

<body>
  <div class="wrap">

    <div class="top">
      <div class="brand">
        <div class="mark" aria-hidden="true"></div>
        <div>
          <h1><?= h($companyName) ?> — Invoice</h1>
          <div class="sub"><?= h($companyWebsite) ?> • <?= h($companyEmail) ?><?= $companyPhone ? " • " . h($companyPhone) : "" ?></div>
        </div>
      </div>

      <div class="actions">
        <a class="btn" href="<?= h($MY_BOOKING_URL) ?>">My Booking</a>
        <a class="btn" href="<?= h($waLink) ?>" target="_blank" rel="noopener">WhatsApp</a>
        <button class="btn primary" onclick="window.print()">Print / Save PDF</button>
      </div>
    </div>

    <div class="card">
      <div class="card-h">
        <div>
          <p class="title">Payment Invoice</p>
          <p class="meta">
            Booking ID: <b><?= h($bookingId) ?></b> • Generated: <?= h($createdAt) ?>
            <?= $companyAddress ? " • " . h($companyAddress) : "" ?>
          </p>
        </div>

        <div class="rightmeta">
          <span class="badge <?= h($badgeTone) ?>"><span class="dot"></span><?= h($badgeLabel) ?></span>
          <div class="meta">Please use the booking ID as transfer note</div>
        </div>
      </div>

      <div class="card-b">
        <div class="grid">

          <div class="box">
            <p class="section-title">Guest details</p>
            <div class="kv">
              <div class="k">Name</div><div class="v"><?= h($customerName ?: '-') ?></div>
              <div class="k">Email</div><div class="v"><?= h($customerEmail ?: '-') ?></div>

              <!-- ✅ APPLIED -->
              <div class="k">WhatsApp / Phone</div><div class="v"><?= h($customerPhone ?: '-') ?></div>
              <div class="k">Certificate level</div><div class="v"><?= h($customerCert ?: 'Beginner / No Certificate') ?></div>

              <div class="k">Booking ID</div><div class="v"><?= h($bookingId) ?></div>
            </div>
          </div>

          <div class="box">
            <p class="section-title">Total amount (IDR)</p>
            <div>
              <div class="amt-big"><?= idr($totalIdr) ?></div>
              <div class="amt-sub">
                <?= $totalUsd > 0 ? ("Reference: " . usd($totalUsd) . " (approx.)") : "Reference USD shown only if available" ?>
                <?= $fxApprox ? (" • FX approx: " . number_format((float)$fxApprox, 0, ',', '.') . " IDR/USD") : "" ?>
              </div>
              <div class="amt-sub">Default: Manual bank transfer (BCA)</div>
              <div class="amt-sub">Selected method: <b id="pmValue">Manual bank transfer</b></div>
            </div>
          </div>

        </div>

        <div style="margin-top:14px;">
          <p class="section-title">Booking items</p>
          <div style="border:1px solid var(--line); border-radius:14px; overflow:hidden;">
            <table>
              <thead>
                <tr>
                  <th style="width:160px;">Category</th>
                  <th>Item</th>
                  <th style="width:140px;">Date</th>
                  <th class="num" style="width:80px;">PAX</th>
                  <th class="num" style="width:180px;">Subtotal (IDR)</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!$list): ?>
                  <tr><td colspan="5" class="small">No items found for this booking.</td></tr>
                <?php else: ?>
                  <?php foreach ($list as $idx => $it): ?>
                    <?php
                      $name = (string)($it['name'] ?? $it['product_name'] ?? '-');
                      $cat  = $getCategoryForItem($it);

                      $q = (int)($it['quantity'] ?? 0);

                      // subtotal already includes addons
                      $lineIdr = (float)($it['line_total_idr'] ?? 0);
                      $lineUsd = (float)($it['line_total_usd'] ?? 0);
                      if ($lineIdr <= 0 && $lineUsd > 0 && $fxApprox) $lineIdr = $lineUsd * $fxApprox;

                      $date = (string)($it['booking_date'] ?? $it['date'] ?? '');

                      // ✅ addons for this row (robust)
                      $addons = $itemAddonsByRow[$idx] ?? [];
                    ?>
                    <tr>
                      <td><div style="font-weight:900;"><?= h($cat) ?></div></td>

                      <td>
                        <div style="font-weight:900;"><?= h($name) ?></div>

                        <?php if ($addons): ?>
                          <div class="small" style="margin-top:4px;">
                            <b>Add-ons selected:</b>
                            <?php foreach ($addons as $a): ?>
                              <?php
                                $an = (string)($a['name'] ?? $a['key'] ?? 'Addon');
                                $pi = $a['price_idr'] ?? null;
                                $pu = $a['price_usd'] ?? null;

                                $priceTxt = '';
                                if (is_numeric($pi) && (float)$pi > 0) $priceTxt = ' (+' . idr((float)$pi) . ')';
                                elseif (is_numeric($pu) && (float)$pu > 0) $priceTxt = ' (+' . usd((float)$pu) . ')';
                              ?>
                              <div>• <?= h($an) ?><?= $priceTxt ? h($priceTxt) : '' ?></div>
                            <?php endforeach; ?>
                          </div>
                        <?php endif; ?>
                      </td>

                      <td><?= h(fmtDate($date)) ?></td>
                      <td class="num"><?= (int)$q ?></td>
                      <td class="num">
                        <div style="font-weight:950;"><?= idr($lineIdr) ?></div>
                        <?php if ($lineUsd > 0): ?><div class="small"><?= usd($lineUsd) ?> (ref.)</div><?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div style="margin-top:14px;" class="notice">
          <div class="info">i</div>
          <div style="font-size:13.5px; width:100%;">
            <div style="font-weight:950; margin-bottom:4px;">Payment instructions</div>
            Please transfer <b><?= idr($totalIdr) ?></b> to the bank account below.
            After payment, send your proof via WhatsApp or email for confirmation.
            Use <b>Booking ID: <?= h($bookingId) ?></b> as the transfer note.
            <div class="small" style="margin-top:6px;">USD is shown only as an approximate reference (if available).</div>

            <div class="pmwrap">
              <div class="pm-toggle">
                <input type="checkbox" id="pmEnable" />
                <label for="pmEnable">Change payment method</label>
                <span class="pm-hint">Opens a secure payment options popup</span>
              </div>

              <div id="pmNote" class="pm-note"></div>
            </div>
          </div>
        </div>

        <div class="bank-grid">
          <div class="box" style="background:#fff;">
            <p class="section-title">Bank details (BCA)</p>
            <div class="bank-line"><span>Bank</span><span>BCA</span></div>
            <div class="bank-line"><span>Account name</span><span><?= h($bcaAccountName) ?></span></div>
            <div class="bank-line"><span>Account number</span><span><?= h($bcaAccountNumber) ?></span></div>
            <?php if ($bcaSwiftCode): ?><div class="bank-line"><span>SWIFT code</span><span><?= h($bcaSwiftCode) ?></span></div><?php endif; ?>
            <div class="bank-line"><span>Transfer amount</span><span><?= idr($totalIdr) ?></span></div>
            <div class="bank-line"><span>Transfer note</span><span><?= h($bookingId) ?></span></div>
            <div class="small" style="margin-top:10px;">Make sure the amount matches exactly to avoid verification delays.</div>
          </div>

          <div class="box" style="background:#fff;">
            <p class="section-title">Contact</p>
            <div class="bank-line"><span>Email</span><span><?= h($companyEmail) ?></span></div>
            <div class="bank-line"><span>Website</span><span><?= h($companyWebsite) ?></span></div>
            <div class="bank-line"><span>WhatsApp</span><span><?= h($companyPhone ?: '-') ?></span></div>

            <div class="small" style="margin-top:10px;">
              Status: <b><?= h($badgeLabel) ?></b><br>
              After transfer, contact us so we can confirm your booking.
            </div>

            <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
              <a class="btn" href="<?= h($MY_BOOKING_URL) ?>">Open My Booking</a>
              <a id="waProofBtn" class="btn primary" href="<?= h($waLink) ?>" target="_blank" rel="noopener">Send Proof via WhatsApp</a>
            </div>
          </div>
        </div>

        <div class="footer">
          <div>© <?= date('Y') ?> <?= h($companyName) ?> • Invoice</div>
          <div>Reference: <?= h($bookingId) ?> • Generated: <?= h($createdAt) ?></div>
        </div>

      </div>
    </div>

  </div>

  <!-- Modal -->
  <div id="pmModalBackdrop" class="modal-backdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="pmModalTitle">
      <div class="modal-h">
        <div class="ttl" id="pmModalTitle">Change payment method</div>
        <button class="modal-x" type="button" id="pmCloseBtn" aria-label="Close">×</button>
      </div>
      <div class="modal-b">
        <p>Select your preferred payment method. For non-cash methods, you can choose <b>Full payment</b> or <b>50% deposit</b>.</p>

        <select id="pmSelect" class="select">
          <?php foreach ($paymentMethods as $val => $label): ?>
            <option value="<?= h($val) ?>"><?= h($label) ?></option>
          <?php endforeach; ?>
        </select>

        <div id="payTypeWrap" class="seg" style="display:none;">
          <label class="opt">
            <input type="radio" name="payType" value="full" checked>
            Full payment
          </label>
          <label class="opt">
            <input type="radio" name="payType" value="dep50">
            Deposit 50%
          </label>
          <div class="desc" id="payTypeDesc"></div>
        </div>

        <p class="small" style="margin-top:10px; display:none;" id="cashRule">
          Note: <b>Cash</b> requires <b>50% deposit</b> now (<b><?= h(idr($deposit50Idr)) ?></b>)
          and <b>50% cash</b> at the office (<b><?= h(idr($balance50Idr)) ?></b>).
        </p>
      </div>
      <div class="modal-actions">
        <button class="btn ghost" type="button" id="pmCancelBtn">Cancel</button>
        <button class="btn primary" type="button" id="pmOkBtn">OK</button>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const pmEnable  = document.getElementById('pmEnable');
      const backdrop  = document.getElementById('pmModalBackdrop');
      const closeBtn  = document.getElementById('pmCloseBtn');
      const cancelBtn = document.getElementById('pmCancelBtn');
      const okBtn     = document.getElementById('pmOkBtn');
      const methodSel = document.getElementById('pmSelect');

      const payTypeWrap = document.getElementById('payTypeWrap');
      const payTypeDesc = document.getElementById('payTypeDesc');
      const cashRuleEl  = document.getElementById('cashRule');

      const pmValueEl = document.getElementById('pmValue');
      const pmNoteEl  = document.getElementById('pmNote');

      const waBtn     = document.getElementById('waProofBtn');

      const bookingId = <?= json_encode($bookingId) ?>;
      const number    = <?= json_encode($waNumber) ?>;

      const linksMap  = <?= json_encode($paymentLinks, JSON_UNESCAPED_SLASHES) ?>;

      const totalIdr  = <?= json_encode((float)$totalIdr) ?>;
      const dep50     = <?= json_encode((float)$deposit50Idr) ?>;
      const bal50     = <?= json_encode((float)$balance50Idr) ?>;

      function escHtml(s){
        return String(s ?? '')
          .replaceAll('&','&amp;')
          .replaceAll('<','&lt;')
          .replaceAll('>','&gt;')
          .replaceAll('"','&quot;')
          .replaceAll("'","&#039;");
      }

      function openModal(){
        backdrop.classList.add('show');
        backdrop.setAttribute('aria-hidden', 'false');
        setTimeout(() => { methodSel && methodSel.focus(); }, 10);
        syncUI();
      }
      function closeModal(uncheck){
        backdrop.classList.remove('show');
        backdrop.setAttribute('aria-hidden', 'true');
        if(uncheck && pmEnable) pmEnable.checked = false;
      }

      function buildWa(method, payType){
        if(!number) return waBtn.getAttribute('href');
        let typeLine = "";
        if(method !== "Cash") typeLine = "\nPayment Type: " + (payType === "dep50" ? "Deposit 50%" : "Full payment");
        const text =
          "Hello Bali Diving, I want to confirm payment.\n" +
          "Booking ID: " + bookingId + "\n" +
          "Payment Method: " + method + typeLine + "\n" +
          "Thank you.";
        return "https://wa.me/" + number + "?text=" + encodeURIComponent(text);
      }

      function showNote(html){
        if(!pmNoteEl) return;
        pmNoteEl.innerHTML = html;
        pmNoteEl.classList.add('show');
      }

      function setSelectedLabel(label){
        if(pmValueEl) pmValueEl.textContent = label;
      }

      function getPayType(){
        const r = document.querySelector('input[name="payType"]:checked');
        return r ? r.value : 'full';
      }

      function syncUI(){
        const method = methodSel ? methodSel.value : 'Cash';

        if(method === 'Cash'){
          if(payTypeWrap) payTypeWrap.style.display = 'none';
          if(cashRuleEl) cashRuleEl.style.display = 'block';
        }else{
          if(payTypeWrap) payTypeWrap.style.display = 'flex';
          if(cashRuleEl) cashRuleEl.style.display = 'none';

          const pt = getPayType();
          if(payTypeDesc){
            const amt = (pt === 'dep50') ? dep50 : totalIdr;
            payTypeDesc.textContent = (pt === 'dep50')
              ? ("Pay now: Rp " + amt.toLocaleString('id-ID') + " (50% deposit)")
              : ("Pay now: Rp " + amt.toLocaleString('id-ID') + " (full payment)");
          }
        }
      }

      if(pmEnable){
        pmEnable.addEventListener('change', function(){
          if(pmEnable.checked){
            openModal();
          }else{
            if(pmNoteEl){ pmNoteEl.classList.remove('show'); pmNoteEl.innerHTML = ''; }
            setSelectedLabel('Manual bank transfer');
            if(waBtn) waBtn.setAttribute('href', <?= json_encode($waLink, JSON_UNESCAPED_SLASHES) ?>);
          }
        });
      }

      if(closeBtn) closeBtn.addEventListener('click', () => closeModal(true));
      if(cancelBtn) cancelBtn.addEventListener('click', () => closeModal(true));
      backdrop.addEventListener('click', (e) => { if(e.target === backdrop) closeModal(true); });
      document.addEventListener('keydown', (e) => { if(e.key === 'Escape' && backdrop.classList.contains('show')) closeModal(true); });

      if(methodSel) methodSel.addEventListener('change', syncUI);
      document.addEventListener('change', function(e){
        if(e.target && e.target.name === 'payType') syncUI();
      });

      if(okBtn){
        okBtn.addEventListener('click', function(){
          const method = methodSel ? methodSel.value : 'Cash';

          if(method === 'Cash'){
            setSelectedLabel('Cash (50% deposit + 50% at office)');
            if(waBtn) waBtn.setAttribute('href', buildWa('Cash', 'dep50'));

            showNote(
              "<b>Cash payment selected.</b> Required: <b>50% deposit</b> now " +
              "(<b>Rp " + dep50.toLocaleString('id-ID') + "</b>) and <b>50% cash</b> at the office " +
              "(<b>Rp " + bal50.toLocaleString('id-ID') + "</b>). Please contact us via WhatsApp to arrange the deposit."
            );

            closeModal(false);
            return;
          }

          const payType = getPayType(); // full | dep50
          const methodMap = linksMap[method] || null;
          const base = methodMap ? (methodMap[payType] || '') : '';

          const label = method + " — " + (payType === 'dep50' ? 'Deposit 50%' : 'Full payment');
          setSelectedLabel(label);

          if(waBtn) waBtn.setAttribute('href', buildWa(method, payType));

          if(!base){
            showNote("<b>"+escHtml(method)+"</b> ("+(payType==='dep50'?'Deposit 50%':'Full payment')+") is not configured yet. Please contact us via WhatsApp.");
            closeModal(false);
            return;
          }

          const url = base + encodeURIComponent(bookingId);
          closeModal(false);
          window.location.href = url;
        });
      }

      setSelectedLabel('Manual bank transfer');
      if(waBtn) waBtn.setAttribute('href', <?= json_encode($waLink, JSON_UNESCAPED_SLASHES) ?>);
    })();
  </script>
</body>
</html>
