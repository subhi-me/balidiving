<?php
// index.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

// DEBUG sementara: bisa dihapus kalau sudah OK
ini_set('display_errors', '1');
error_reporting(E_ALL);

// ganti ke main.php (yang sudah dipakai file lain)
include __DIR__ . '/../template/database/main-cart.php'; // pastikan ini define $pdo, $USD_TO_IDR (jika ada)

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

// --- GROUP PER KATEGORI ---
$byCategory = [];
foreach ($allProducts as $p) {
    $byCategory[$p['category']][] = $p;
}

$snorkelingProducts   = $byCategory['Snorkeling']                ?? [];
$tryDivingProducts    = $byCategory['Try Diving']                ?? [];
$funDivingProducts    = $byCategory['Fun Diving']                ?? [];
$learnDivingProducts  = $byCategory['Learn Diving']              ?? [];
$safariProducts       = $byCategory['Diving Safari (Multi-day)'] ?? [];

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
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bali Diving – Booking Plan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      padding: 2rem;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: linear-gradient(135deg, #0070d3 0%, #3552c8 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
    }

    .demo-container {
      width: 100%;
      max-width: 1200px;
      display: flex;
      gap: 2rem;
      flex-wrap: wrap;
      align-items: flex-start;
    }

    .products-section {
      flex: 1 1 320px;
    }

    .products-grid {
      display: flex;
      flex-direction: column;
      gap: 2.5rem;
    }

    .category-section {
      background: rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 1.5rem;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.12);
    }

    .category-title {
      color: #ffffff;
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0 0 1rem 0;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid rgba(255,255,255,0.25);
    }

    .category-intro {
      color: #f9fafb;
      font-size: 0.9rem;
      line-height: 1.6;
      margin-bottom: 1.25rem;
      opacity: 0.9;
    }

    .category-products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
      gap: 1.5rem;
    }

    .product-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 1.25rem;
      box-shadow: 0 4px 10px rgba(15,23,42,0.2);
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .product-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 18px rgba(15,23,42,0.28);
    }

    .product-icon img {
      width: 100%;
      height: 140px;
      border-radius: 8px;
      object-fit: cover;
      display: block;
      margin-bottom: 0.5rem;
    }

    .product-name {
      font-size: 1rem;
      font-weight: 600;
      color: #111827;
    }

    .product-price {
      font-size: 1.1rem;
      font-weight: 700;
      color: #0070d3;
    }

    .product-description {
      font-size: 0.8rem;
      color: #6b7280;
      line-height: 1.4;
      min-height: 2.2em;
    }

    .read-article-link {
      font-size: 0.75rem;
      color: #0070d3;
      text-decoration: none;
      margin-top: 0.1rem;
      margin-bottom: 0.6rem;
      display: inline-block;
    }

    .read-article-link:hover {
      text-decoration: underline;
    }

    .add-to-cart-btn,
    .enquire-btn {
      width: 100%;
      padding: 0.65rem 0.75rem;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.9rem;
      text-align: center;
      transition: background 0.15s ease, transform 0.05s ease;
    }

    .add-to-cart-btn {
      background: #0070d3;
      color: #ffffff;
    }

    .add-to-cart-btn:hover {
      background: #0654a5;
    }

    .add-to-cart-btn:active {
      transform: scale(0.98);
    }

    .enquire-btn {
      background: #23a0b4;
      color: #ffffff;
      text-decoration: none;
      display: block;
    }

    .enquire-btn:hover {
      background: #1b7f8e;
    }

    /* FLOATING CART */
    .floating-cart {
      position: sticky;
      top: 1rem;
      align-self: flex-start;
      width: 360px;
      max-width: 100%;
      background: #ffffff;
      border-radius: 18px;
      box-shadow: 0 22px 60px rgba(15,23,42,0.45);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .cart-header {
      padding: 1rem 1.25rem;
      background: #0070d3;
      color: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 0.75rem;
    }

    .cart-header-main {
      display: flex;
      flex-direction: column;
      gap: 0.1rem;
    }

    .cart-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 700;
    }

    .cart-booking-label {
      font-size: 0.7rem;
      opacity: 0.9;
    }

    .cart-booking-id {
      font-size: 0.8rem;
      font-weight: 600;
    }

    .cart-count {
      background: rgba(15,23,42,0.18);
      padding: 0.25rem 0.6rem;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .cart-items {
      max-height: 420px;
      overflow-y: auto;
      padding: 0.75rem 1rem;
      background: #f9fafb;
    }

    .cart-item {
      display: flex;
      gap: 0.75rem;
      background: #ffffff;
      border-radius: 10px;
      padding: 0.6rem 0.6rem;
      margin-bottom: 0.5rem;
      box-shadow: 0 1px 4px rgba(15,23,42,0.15);
    }

    .cart-icon {
      width: 56px;
      height: 56px;
      border-radius: 8px;
      object-fit: cover;
      flex-shrink: 0;
    }

    .cart-info {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 0.2rem;
    }

    .cart-name {
      font-size: 0.85rem;
      font-weight: 600;
      color: #111827;
    }

    .cart-price {
      font-size: 0.8rem;
      color: #0070d3;
      font-weight: 600;
    }

    .cart-date {
      margin-top: 0.25rem;
      width: 100%;
      font-size: 0.75rem;
      padding: 0.25rem 0.35rem;
      border-radius: 6px;
      border: 1px solid #d1d5db;
    }

    .addons {
      margin-top: 0.25rem;
      border-top: 1px dashed #e5e7eb;
      padding-top: 0.25rem;
    }

    .addon-row {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.7rem;
      color: #4b5563;
      margin-bottom: 0.15rem;
    }

    .qty-row {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      margin-top: 0.35rem;
    }

    .qty-row button {
      padding: 0.1rem 0.45rem;
      border-radius: 6px;
      border: none;
      background: #0070d3;
      color: #ffffff;
      font-size: 0.8rem;
      cursor: pointer;
    }

    .qty-row span {
      min-width: 20px;
      text-align: center;
      font-size: 0.8rem;
    }

    .remove-btn {
      margin-left: auto;
      background: #f43f5e !important;
      font-size: 0.7rem !important;
      padding-inline: 0.45rem !important;
    }

    .cart-total {
      font-size: 0.8rem;
      font-weight: 700;
      color: #111827;
      white-space: nowrap;
      align-self: flex-start;
      margin-left: 0.25rem;
    }

    .cart-footer {
      padding: 0.9rem 1.1rem 1.1rem;
      background: #ffffff;
      border-top: 1px solid #e5e7eb;
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
    }

    .summary-label {
      font-size: 0.9rem;
      font-weight: 600;
      color: #111827;
    }

    .summary-amounts {
      text-align: right;
      font-size: 0.9rem;
      font-weight: 700;
      color: #0070d3;
    }

    .summary-amounts small {
      display: block;
      font-size: 0.75rem;
      color: #6b7280;
      font-weight: 500;
    }

    /* COUPON TICKET */
    .coupon-area {
      margin-top: 0.3rem;
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
    }

    .coupon-ticket {
      position: relative;
      background: radial-gradient(circle at 0% 0%, #22d3ee, #0ea5e9);
      border-radius: 12px;
      padding: 0.55rem 0.7rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      color: #0f172a;
      box-shadow: 0 8px 18px rgba(15,23,42,0.35);
      overflow: hidden;
    }

    .coupon-ticket::before,
    .coupon-ticket::after {
      content: "";
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      background: #ffffff;
      border-radius: 999px;
    }

    .coupon-ticket::before {
      left: -8px;
    }

    .coupon-ticket::after {
      right: -8px;
    }

    .coupon-main {
      display: flex;
      flex-direction: column;
      gap: 0.05rem;
      font-size: 0.8rem;
    }

    .coupon-title {
      font-weight: 700;
      font-size: 0.78rem;
      text-transform: uppercase;
      letter-spacing: 0.07em;
    }

    .coupon-note {
      font-size: 0.72rem;
      opacity: 0.9;
    }

    .coupon-value {
      font-size: 1rem;
      font-weight: 800;
      white-space: nowrap;
    }

    .coupon-ticket.applied {
      background: linear-gradient(135deg, #22c55e, #16a34a);
      color: #f9fafb;
    }

    .coupon-info {
      font-size: 0.72rem;
      color: #16a34a;
    }

    .customer-fields {
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
      margin-top: 0.25rem;
    }

    .customer-fields label {
      font-size: 0.75rem;
      color: #4b5563;
      display: flex;
      flex-direction: column;
      gap: 0.15rem;
    }

    .customer-fields input {
      font-size: 0.8rem;
      padding: 0.35rem 0.4rem;
      border-radius: 6px;
      border: 1px solid #d1d5db;
    }

    .pay-btn {
      width: 100%;
      margin-top: 0.35rem;
      padding: 0.7rem 0.8rem;
      border-radius: 9px;
      border: none;
      background: #0070d3;
      color: #ffffff;
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
    }

    .pay-btn:hover {
      background: #0654a5;
    }

    .cart-msg {
      position: fixed;
      right: 1rem;
      top: 1rem;
      background: #111827;
      color: #f9fafb;
      padding: 0.6rem 0.9rem;
      border-radius: 8px;
      font-size: 0.8rem;
      box-shadow: 0 15px 40px rgba(0,0,0,0.35);
      z-index: 2000;
    }

    .empty {
      font-size: 0.8rem;
      color: #6b7280;
      text-align: center;
      padding: 1.2rem 0.5rem;
    }

    .empty-cart-message {
      font-size: 0.8rem;
      color: #6b7280;
      text-align: center;
      padding: 1.2rem 0.5rem;
    }

    @media (max-width: 900px) {
      body {
        padding: 1rem;
      }
      .demo-container {
        flex-direction: column;
      }
      .floating-cart {
        position: static;
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="demo-container">
    <div class="products-section">
      <div class="products-grid">
        <?php
          include __DIR__ . '/snorkeling.php';

        ?>
      </div>
    </div>

    <!-- FLOATING BOOKING PLAN -->
    <aside class="floating-cart">
      <header class="cart-header">
        <div class="cart-header-main">
          <h2 class="cart-title">Your Booking Plan</h2>
          <div class="cart-booking-label">Booking ID:</div>
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
        </div>

        <button class="pay-btn" type="button" onclick="checkout()">Checkout &amp; Pay</button>
      </footer>
    </aside>
  </div>

  <!-- Data dari PHP ke JS -->
  <script>
    window.PRODUCT_MAP    = <?= json_encode($productMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.PRODUCT_IMAGES = <?= json_encode($productImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.ADDONS         = <?= json_encode($addonMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.USD_TO_IDR     = <?= json_encode($USD_TO_IDR, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script src="cart.js?=v15t"></script>
</body>
</html>
