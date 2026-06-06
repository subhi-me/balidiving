<?php
// cart.php (CART ONLY - CENTERED + RESPONSIVE + DATE MIN = TOMORROW)
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

// DEBUG sementara: bisa dihapus kalau sudah OK
ini_set('display_errors', '1');
error_reporting(E_ALL);

// DB bootstrap (pastikan define $pdo, $USD_TO_IDR (jika ada))
include __DIR__ . '/../template/database/main-cart.php';

// --- LOAD PRODUCTS ---
$productsSql = "
  SELECT id, name, price_usd, category, description, is_enquiry
  FROM bd_catalog_products
  ORDER BY category, id
";
$allProducts = $pdo->query($productsSql)->fetchAll(PDO::FETCH_ASSOC);

// --- LOAD IMAGES (image pertama per product) ---
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

// --- Map produk untuk JS (id => data sederhana) ---
$productMap = [];
foreach ($allProducts as $p) {
    $id = (int)$p['id'];
    $productMap[$id] = [
        'name'       => $p['name'],
        'price'      => (float)$p['price_usd'],
        'category'   => $p['category'],
        'desc'       => $p['description'],
        // penting: key-nya 'is_enquiry' supaya cocok dengan cart.js
        'is_enquiry' => (bool)$p['is_enquiry'],
    ];
}

// --- Booking ID awal (tampilan) ---
function bd_generate_booking_id(): string {
    $now = new DateTime('now', new DateTimeZone('Asia/Makassar'));
    $y = $now->format('Y');
    $m = $now->format('m');
    $d = $now->format('d');
    $rand = random_int(1000, 9999);
    return "BDV-{$y}{$m}{$d}-{$rand}";
}
$BOOKING_ID = bd_generate_booking_id();

// --- Kurs USD -> IDR (fallback 15800 kalau tidak ada) ---
$USD_TO_IDR = isset($USD_TO_IDR) && is_numeric($USD_TO_IDR) ? (float)$USD_TO_IDR : 15800;

// --- Minimal date = besok (Asia/Makassar) ---
$MIN_DATE = (new DateTime('tomorrow', new DateTimeZone('Asia/Makassar')))->format('Y-m-d');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bali Diving – Cart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="https://balidiving.com/bali-diving-favicon.png" type="image/png" sizes="144x144">
  <style>
    * { box-sizing: border-box; }

    :root{
      --bd-blue:#0070d3;
      --bd-indigo:#3552c8;
      --bd-teal:#23a0b4;
      --bd-ink:#0f172a;
      --bd-muted:#6b7280;
      --bd-bg:#f9fafb;
      --bd-border:#e5e7eb;
    }

    body{
      margin:0;
      padding: 1rem;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: radial-gradient(1200px 900px at 10% 10%, rgba(34,211,238,.25), transparent 55%),
                  radial-gradient(900px 700px at 90% 20%, rgba(238,190,53,.18), transparent 60%),
                  linear-gradient(135deg, var(--bd-blue) 0%, var(--bd-indigo) 100%);
      min-height: 100vh;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    /* container khusus cart only */
    .demo-container{
      width:100%;
      max-width: 440px;
      display:flex;
      justify-content:center;
    }

    /* hide products section total (cart only) */
    .products-section{ display:none !important; }

    /* CART CARD */
    .floating-cart{
      position: relative;
      width: 100%;
      max-width: 440px;
      background:#fff;
      border-radius: 22px;
      box-shadow: 0 34px 90px rgba(2, 6, 23, 0.45);
      display:flex;
      flex-direction:column;
      overflow:hidden;
      border: 1px solid rgba(255,255,255,.18);
    }

    .cart-header{
      padding: 1.05rem 1.25rem;
      background: linear-gradient(135deg, var(--bd-blue), var(--bd-indigo));
      color:#fff;
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap: .75rem;
    }
    .cart-header-main{ display:flex; flex-direction:column; gap:.2rem; }
    .cart-title{ margin:0; font-size:1.12rem; font-weight:800; letter-spacing:.2px; }
    .cart-booking-label{ font-size:.72rem; opacity:.92; }
    .cart-booking-id{ font-size:.82rem; font-weight:700; }

    .cart-count{
      background: rgba(15,23,42,0.22);
      padding: .28rem .62rem;
      border-radius: 999px;
      font-size:.75rem;
      font-weight:700;
      white-space:nowrap;
      backdrop-filter: blur(6px);
    }

    .cart-items{
      max-height: 52vh;
      overflow-y:auto;
      padding: .9rem 1rem;
      background: var(--bd-bg);
    }

    .cart-item{
      display:flex;
      gap:.8rem;
      background:#fff;
      border-radius:14px;
      padding:.72rem;
      margin-bottom:.55rem;
      box-shadow: 0 2px 10px rgba(15,23,42,0.10);
      border: 1px solid rgba(229,231,235,.9);
    }

    .cart-icon{
      width:60px;
      height:60px;
      border-radius:12px;
      object-fit:cover;
      flex-shrink:0;
      background: #e5e7eb;
    }

    .cart-info{
      flex:1;
      display:flex;
      flex-direction:column;
      gap:.25rem;
      min-width:0;
    }

    .cart-name{
      font-size:.88rem;
      font-weight:800;
      color:#111827;
      line-height:1.25;
      word-break:break-word;
    }

    .cart-price{
      font-size:.82rem;
      color: var(--bd-blue);
      font-weight:800;
    }

    .cart-date{
      margin-top:.28rem;
      width:100%;
      font-size:.80rem;
      padding:.42rem .52rem;
      border-radius:10px;
      border:1px solid #d1d5db;
      background:#fff;
      outline:none;
    }
    .cart-date:focus{
      border-color:#93c5fd;
      box-shadow: 0 0 0 3px rgba(59,130,246,.18);
    }

    .addons{
      margin-top:.25rem;
      border-top:1px dashed var(--bd-border);
      padding-top:.35rem;
    }

    .addon-row{
      display:flex;
      align-items:center;
      gap:.3rem;
      font-size:.72rem;
      color:#4b5563;
      margin-bottom:.2rem;
      flex-wrap:wrap;
    }

    .qty-row{
      display:flex;
      align-items:center;
      gap:.45rem;
      margin-top:.42rem;
      flex-wrap:wrap;
    }

    .qty-row button{
      padding:.18rem .55rem;
      border-radius:10px;
      border:none;
      background: var(--bd-blue);
      color:#fff;
      font-size:.85rem;
      font-weight:800;
      cursor:pointer;
      transition: transform .05s ease, background .15s ease;
    }
    .qty-row button:hover{ background:#0654a5; }
    .qty-row button:active{ transform: scale(.98); }

    .qty-row span{
      min-width:22px;
      text-align:center;
      font-size:.85rem;
      font-weight:800;
      color:#111827;
    }

    .remove-btn{
      margin-left:auto;
      background:#f43f5e !important;
      font-size:.78rem !important;
      padding-inline:.55rem !important;
      font-weight:900 !important;
    }
    .remove-btn:hover{ filter: brightness(.95); }

    .cart-total{
      font-size:.82rem;
      font-weight:900;
      color:#111827;
      white-space:nowrap;
      align-self:flex-start;
      margin-left:.25rem;
    }

    .cart-footer{
      padding: 1rem 1.1rem 1.15rem;
      background:#fff;
      border-top:1px solid var(--bd-border);
      display:flex;
      flex-direction:column;
      gap:.72rem;
    }

    .summary-row{
      display:flex;
      justify-content:space-between;
      align-items:baseline;
      gap:.75rem;
    }

    .summary-label{
      font-size:.95rem;
      font-weight:900;
      color:#111827;
      letter-spacing:.2px;
    }

    .summary-amounts{
      text-align:right;
      font-size:.95rem;
      font-weight:900;
      color: var(--bd-blue);
    }

    .summary-amounts small{
      display:block;
      font-size:.78rem;
      color: var(--bd-muted);
      font-weight:600;
      margin-top:.16rem;
    }

    .customer-fields{
      display:flex;
      flex-direction:column;
      gap:.45rem;
      margin-top:.15rem;
    }

    .customer-fields label{
      font-size:.76rem;
      color:#4b5563;
      display:flex;
      flex-direction:column;
      gap:.18rem;
      font-weight:700;
    }

    .customer-fields input{
      font-size:.88rem;
      padding:.55rem .6rem;
      border-radius:12px;
      border:1px solid #d1d5db;
      outline:none;
      background:#fff;
    }

    .customer-fields input:focus{
      border-color:#93c5fd;
      box-shadow: 0 0 0 3px rgba(59,130,246,.18);
    }

    .mini-note{
      font-size:.72rem;
      color:#6b7280;
      line-height:1.35;
      margin-top: -.15rem;
    }

    .pay-btn{
      width:100%;
      margin-top:.35rem;
      padding:.82rem .9rem;
      border-radius:14px;
      border:none;
      background: linear-gradient(135deg, var(--bd-blue), #0ea5e9);
      color:#fff;
      font-weight:950;
      font-size:1rem;
      cursor:pointer;
      transition: transform .06s ease, filter .15s ease;
    }
    .pay-btn:hover{ filter: brightness(.98); }
    .pay-btn:active{ transform: scale(.99); }

    .empty, .empty-cart-message{
      font-size:.88rem;
      color: var(--bd-muted);
      text-align:center;
      padding: 1.3rem .5rem;
    }

    .cart-msg{
      position:fixed;
      right:1rem;
      top:1rem;
      background:#111827;
      color:#f9fafb;
      padding:.6rem .9rem;
      border-radius:10px;
      font-size:.82rem;
      box-shadow: 0 15px 40px rgba(0,0,0,.35);
      z-index:2000;
    }

    @media (max-width: 480px){
      body{ padding:.75rem; }
      .floating-cart{ border-radius:18px; }
      .cart-items{ max-height: 58vh; }
      .demo-container{ max-width: 100%; }
    }
  </style>
</head>

<body>
  <div class="demo-container">

    <!-- CART ONLY (CENTERED) -->
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

      <div class="cart-items" id="cartItems">
        <!-- cart content from JS -->
      </div>

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
          <div class="mini-note">
            
          </div>
        </div>

        <button class="pay-btn" type="button" onclick="checkout()">Continue Booking</button>
      </footer>
    </aside>

  </div>

  <!-- Data dari PHP ke JS -->
  <script>
    window.PRODUCT_MAP    = <?= json_encode($productMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.PRODUCT_IMAGES = <?= json_encode($productImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.ADDONS         = <?= json_encode($addonMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.USD_TO_IDR     = <?= json_encode($USD_TO_IDR, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    // MIN DATE (besok) untuk semua input date yang dibuat oleh cart.js
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

      // initial
      apply();

      // watch changes from cart.js renders
      const mo = new MutationObserver(() => apply());
      mo.observe(root, { childList:true, subtree:true });

      // fallback periodic (just in case)
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
      // EMPTY CART STATE
      btn.textContent = 'Browse Dive Activities';
      btn.onclick = () => {
        window.location.href = 'https://balidiving.com/pricelist';
      };
      btn.style.background = 'linear-gradient(135deg, #23a0b4, #0ea5e9)';
      customerFields.style.display = 'none';
    } else {
      // HAS ITEMS STATE
      btn.textContent = 'Continue Booking';
      btn.onclick = () => checkout();
      btn.style.background = '';
      customerFields.style.display = '';
    }
  };

  // initial
  updateUI();

  // observe cart.js changes
  const observer = new MutationObserver(updateUI);
  observer.observe(cartItems, { childList: true, subtree: true });
})();
</script>


  <script src="cart.js?=a10"></script>
</body>
</html>
