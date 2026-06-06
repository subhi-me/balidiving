<?php
/* ============================================================
   1) /cart/cart/my-booking  (UPDATE: add Phone + Certificate fields)
   ============================================================ */
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

ini_set('display_errors', '1');
error_reporting(E_ALL);

include __DIR__ . '/../template/database/main-cart.php';

// --- LOAD PRODUCTS ---
$productsSql = "
  SELECT id, name, price_usd, category, description, is_enquiry
  FROM bd_catalog_products
  ORDER BY category, id
";
$allProducts = $pdo->query($productsSql)->fetchAll(PDO::FETCH_ASSOC);

// --- LOAD IMAGES ---
$imagesSql = "
  SELECT product_id, image_url
  FROM bd_catalog_product_images
  WHERE sort_order = 1
  ORDER BY product_id
";
$imageRows = $pdo->query($imagesSql)->fetchAll(PDO::FETCH_ASSOC);
$productImages = [];
foreach ($imageRows as $row) {
  $productImages[(int)$row['product_id']] = $row['image_url'];
}

// --- LOAD ADDONS ---
$addonsSql = "
  SELECT addon_key, name, price_usd
  FROM bd_catalog_addons
  ORDER BY id
";
$addonsRows = $pdo->query($addonsSql)->fetchAll(PDO::FETCH_ASSOC);
$addonMap = [];
foreach ($addonsRows as $row) {
  $addonMap[$row['addon_key']] = [
    'name'  => $row['name'],
    'price' => (float)$row['price_usd'],
  ];
}

// --- PRODUCT MAP FOR JS ---
$productMap = [];
foreach ($allProducts as $p) {
  $id = (int)$p['id'];
  $productMap[$id] = [
    'name'       => $p['name'],
    'price'      => (float)$p['price_usd'],
    'category'   => $p['category'],
    'desc'       => $p['description'],
    'is_enquiry' => (bool)$p['is_enquiry'],
  ];
}

function bd_generate_booking_id(): string {
  $now = new DateTime('now', new DateTimeZone('Asia/Makassar'));
  $y = $now->format('Y');
  $m = $now->format('m');
  $d = $now->format('d');
  $rand = random_int(1000, 9999);
  return "BDV-{$y}{$m}{$d}-{$rand}";
}
$BOOKING_ID = bd_generate_booking_id();

function get_dynamic_bca_rate($default_rate = 17595) {
  $cacheFile = __DIR__ . '/bca_usd_rate.json';
  $cacheTime = 3600 * 6; // 6 jam cache

  if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    $data = json_decode(file_get_contents($cacheFile), true);
    if (isset($data['rate']) && is_numeric($data['rate'])) {
      return (float)$data['rate'];
    }
  }

  $url = "https://www.bca.co.id/id/informasi/kurs";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  $html = curl_exec($ch);
  curl_close($ch);
  
  if ($html) {
    if (preg_match('/code="USD"[\s\S]*?rate-type="eRate-sell"[\s\S]*?<p>([\d\.,]+)<\/p>/i', $html, $matches)) {
      $rateStr = str_replace(['.', ','], ['', '.'], $matches[1]);
      if (is_numeric($rateStr)) {
        $rate = (float)$rateStr;
        file_put_contents($cacheFile, json_encode(['rate' => $rate, 'time' => time()]));
        return $rate;
      }
    }
  }
  
  if (file_exists($cacheFile)) {
    $data = json_decode(file_get_contents($cacheFile), true);
    if (isset($data['rate']) && is_numeric($data['rate'])) {
      return (float)$data['rate'];
    }
  }
  
  return $default_rate;
}

$USD_TO_IDR = get_dynamic_bca_rate(17595);
$MIN_DATE = (new DateTime('tomorrow', new DateTimeZone('Asia/Makassar')))->format('Y-m-d');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Booking - Bali Diving</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="https://balidiving.com/bali-diving-favicon.png" type="image/png" sizes="144x144">

  <style>
    *{box-sizing:border-box}
    :root{
      --bd-blue:#0070d3; --bd-indigo:#3552c8; --bd-teal:#23a0b4; --bd-ink:#0f172a;
      --bd-muted:#6b7280; --bd-bg:#f9fafb; --bd-border:#e5e7eb;
      --bd-navy:#063c7f; --bd-secondary:#f23d4e; --bd-lightblue:#a2d2fa;
    }
    body{
      margin:0; padding:1rem;
      font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      background-color: var(--bd-navy);
      background-image:
        url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='bubbles' x='0' y='0' width='100' height='100' patternUnits='userSpaceOnUse'%3E%3Ccircle cx='15' cy='20' r='3' fill='%23a2d2fa' opacity='0.15'/%3E%3Ccircle cx='45' cy='65' r='4' fill='%23a2d2fa' opacity='0.12'/%3E%3Ccircle cx='75' cy='35' r='2.5' fill='%23a2d2fa' opacity='0.18'/%3E%3Ccircle cx='30' cy='80' r='2' fill='%23a2d2fa' opacity='0.2'/%3E%3Ccircle cx='85' cy='75' r='3.5' fill='%23a2d2fa' opacity='0.1'/%3E%3Ccircle cx='60' cy='15' r='2' fill='%23a2d2fa' opacity='0.16'/%3E%3Ccircle cx='20' cy='50' r='2.5' fill='%23a2d2fa' opacity='0.14'/%3E%3Ccircle cx='90' cy='45' r='1.5' fill='%23a2d2fa' opacity='0.2'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100' height='100' fill='url(%23bubbles)'/%3E%3C/svg%3E"),
        url("data:image/svg+xml,%3Csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='waves' x='0' y='0' width='200' height='200' patternUnits='userSpaceOnUse'%3E%3Cpath d='M0 50 Q 50 30, 100 50 T 200 50' stroke='%23a2d2fa' stroke-width='1.5' fill='none' opacity='0.12'/%3E%3Cpath d='M0 100 Q 50 80, 100 100 T 200 100' stroke='%23a2d2fa' stroke-width='1.5' fill='none' opacity='0.08'/%3E%3Cpath d='M0 150 Q 50 130, 100 150 T 200 150' stroke='%2323a0b4' stroke-width='1' fill='none' opacity='0.06'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='200' height='200' fill='url(%23waves)'/%3E%3C/svg%3E"),
        linear-gradient(180deg, var(--bd-navy) 0%, var(--bd-indigo) 40%, #000000 100%);
      background-size: 100px 100px, 200px 200px, 100% 100%;
      background-position: 0 0, 0 0, center;
      animation: underwater-float 25s ease-in-out infinite;
      min-height:100vh; display:flex; align-items:center; justify-content:center;
    }

    @keyframes underwater-float {
      0%, 100% { background-position: 0 0, 0 0, center; }
      25% { background-position: 8px -8px, -12px 4px, center; }
      50% { background-position: -4px -16px, 16px -8px, center; }
      75% { background-position: -8px -4px, 4px 12px, center; }
    }

    .demo-container{width:100%;max-width:440px;display:flex;justify-content:center;position:relative;z-index:10}
    .products-section{display:none!important}

    .floating-cart{
      width:100%;max-width:440px;background:#fff;border-radius:22px;
      box-shadow:0 34px 90px rgba(2,6,23,.45);
      display:flex;flex-direction:column;overflow:hidden;border:1px solid rgba(162,210,250,.25);
    }
    .cart-header{
      padding:1.05rem 1.25rem;background:linear-gradient(135deg,var(--bd-navy),var(--bd-indigo));
      color:#fff;display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem;
    }
    .cart-header-main{display:flex;flex-direction:column;gap:.2rem}
    .cart-title{margin:0;font-size:1.12rem;font-weight:800;letter-spacing:.2px}
    .cart-booking-label{font-size:.72rem;opacity:.92}
    .cart-booking-id{font-size:.82rem;font-weight:700}
    .cart-count{
      background:rgba(15,23,42,0.22);padding:.28rem .62rem;border-radius:999px;
      font-size:.75rem;font-weight:700;white-space:nowrap;backdrop-filter:blur(6px);
    }

    .cart-items{max-height:52vh;overflow-y:auto;padding:.9rem 1rem;background:var(--bd-bg)}
    .cart-item{
      display:flex;gap:.8rem;background:#fff;border-radius:14px;padding:.72rem;margin-bottom:.55rem;
      box-shadow:0 2px 10px rgba(15,23,42,.10);border:1px solid rgba(229,231,235,.9);
    }
    .cart-info{flex:1;display:flex;flex-direction:column;gap:.25rem;min-width:0}
    .cart-name{font-size:.88rem;font-weight:800;color:#111827;line-height:1.25;word-break:break-word}
    .cart-date{
      margin-top:.28rem;width:100%;font-size:.80rem;padding:.42rem .52rem;border-radius:10px;
      border:1px solid #d1d5db;background:#fff;outline:none;
    }
    .cart-date:focus{border-color:var(--bd-teal);box-shadow:0 0 0 3px rgba(35,160,180,.25)}
    .addons{margin-top:.25rem;border-top:1px dashed var(--bd-border);padding-top:.35rem}
    .addon-row{display:flex;align-items:center;gap:.3rem;font-size:.72rem;color:#4b5563;margin-bottom:.2rem;flex-wrap:wrap}
    .qty-row{display:flex;align-items:center;gap:.45rem;margin-top:.42rem;flex-wrap:wrap}
    .qty-row button{
      padding:.18rem .55rem;border-radius:10px;border:none;background:var(--bd-blue);color:#fff;
      font-size:.85rem;font-weight:800;cursor:pointer;transition:transform .05s ease, background .15s ease;
    }
    .qty-row button:hover{background:var(--bd-indigo)}
    .qty-row button:active{transform:scale(.98)}
    .qty-row span{min-width:22px;text-align:center;font-size:.85rem;font-weight:800;color:#111827}
    .remove-btn{margin-left:auto;background:var(--bd-secondary)!important;font-size:.78rem!important;padding-inline:.55rem!important;font-weight:900!important}
    .remove-btn:hover{filter:brightness(.95)}

    .cart-footer{
      padding:1rem 1.1rem 1.15rem;background:#fff;border-top:1px solid var(--bd-border);
      display:flex;flex-direction:column;gap:.72rem;
    }
    .summary-row{display:flex;justify-content:space-between;align-items:baseline;gap:.75rem}
    .summary-label{font-size:.95rem;font-weight:900;color:#111827;letter-spacing:.2px}
    .summary-amounts{text-align:right;font-size:.95rem;font-weight:900;color:var(--bd-blue)}
    .summary-amounts small{display:block;font-size:.78rem;color:var(--bd-muted);font-weight:600;margin-top:.16rem}

    .customer-fields{display:flex;flex-direction:column;gap:.45rem;margin-top:.15rem}
    .customer-fields label{
      font-size:.76rem;color:#4b5563;display:flex;flex-direction:column;gap:.18rem;font-weight:700;
    }
    .customer-fields input, .customer-fields select{
      font-size:.88rem;padding:.55rem .6rem;border-radius:12px;border:1px solid #d1d5db;outline:none;background:#fff;
    }
    .customer-fields input:focus, .customer-fields select:focus{
      border-color:var(--bd-teal);box-shadow:0 0 0 3px rgba(35,160,180,.25);
    }

    .pay-btn{
      width:100%;margin-top:.35rem;padding:.82rem .9rem;border-radius:14px;border:none;
      background:linear-gradient(135deg,var(--bd-blue),var(--bd-indigo));color:#fff;font-weight:950;font-size:1rem;
      cursor:pointer;transition:transform .06s ease, filter .15s ease;
    }
    .pay-btn:hover{filter:brightness(.98)}
    .pay-btn:active{transform:scale(.99)}

    .empty,.empty-cart-message{font-size:.88rem;color:var(--bd-muted);text-align:center;padding:1.3rem .5rem}
    .cart-msg{
      position:fixed;right:1rem;top:1rem;background:#111827;color:#f9fafb;padding:.6rem .9rem;border-radius:10px;
      font-size:.82rem;box-shadow:0 15px 40px rgba(0,0,0,.35);z-index:2000;
    }
    @media (max-width:480px){
      body{padding:.75rem}
      .floating-cart{border-radius:18px}
      .cart-items{max-height:58vh}
      .demo-container{max-width:100%}
    }
  </style>
</head>

<body>
  <div class="demo-container">
    <aside class="floating-cart">
      <header class="cart-header">
        <div class="cart-header-main">
          <h2 class="cart-title">My Booking Plan</h2>
          <div class="cart-booking-label">Booking ID</div>
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
            <span id="totalUsd"></span>
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

          <!-- ✅ NEW -->
          <label>
            WhatsApp / Phone
            <input type="tel" id="custPhone" placeholder="+628123456789">
          </label>

          <!-- ✅ NEW -->
          <label>
            Certificate level
            <select id="custCert">
              <option value="Beginner / No Certificate">Beginner / No Certificate</option>
              <option value="Open Water Diver (OWD)">Open Water Diver (OWD)</option>
              <option value="Advanced Open Water (AOWD)">Advanced Open Water (AOWD)</option>
              <option value="Rescue Diver">Rescue Diver</option>
              <option value="Divemaster">Divemaster</option>
              <option value="Instructor">Instructor</option>
            </select>
          </label>
        </div>

        <button class="pay-btn" type="button" onclick="checkout()">Continue Booking</button>
      </footer>
    </aside>
  </div>

  <?php include('add.php'); ?>

  <script>
    window.PRODUCT_MAP    = <?= json_encode($productMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.PRODUCT_IMAGES = <?= json_encode($productImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.ADDONS         = <?= json_encode($addonMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.USD_TO_IDR     = <?= json_encode($USD_TO_IDR, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    window.BD_MIN_DATE = <?= json_encode($MIN_DATE, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    (function enforceMinDateOnCart(){
      const minDate = window.BD_MIN_DATE;
      const root = document.getElementById('cartItems');
      if(!root) return;

      const apply = () => {
        const inputs = root.querySelectorAll('input.cart-date');
        inputs.forEach((inp) => {
          try {
            inp.setAttribute('min', minDate);
            if (inp.value && inp.value < minDate) inp.value = minDate;
          } catch(e){}
        });
      };

      apply();
      const mo = new MutationObserver(() => apply());
      mo.observe(root, { childList:true, subtree:true });

      window.setTimeout(apply, 300);
      window.setTimeout(apply, 900);
    })();
  </script>

  <script>
    (function cartEmptyStateControl(){
      const btn = document.querySelector('.pay-btn');
      const cartItems = document.getElementById('cartItems');
      const customerFields = document.querySelector('.customer-fields');
      if(!btn || !cartItems || !customerFields) return;

      const updateUI = () => {
        const hasItems = cartItems.querySelector('.cart-item');
        if (!hasItems) {
          btn.textContent = 'Browse Dive Activities';
          btn.onclick = () => { window.location.href = 'https://balidiving.com/pricelist'; };
          btn.style.background = 'linear-gradient(135deg, var(--bd-teal), var(--bd-blue))';
          customerFields.style.display = 'none';
        } else {
          btn.textContent = 'Continue Booking';
          btn.onclick = () => checkout();
          btn.style.background = '';
          customerFields.style.display = '';
        }
      };

      updateUI();
      const observer = new MutationObserver(updateUI);
      observer.observe(cartItems, { childList: true, subtree: true });
    })();
  </script>

  <script src="cart.js?=vx106"></script>
</body>
</html>
