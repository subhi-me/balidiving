<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Floating Booking Plan</title>
  <script src="/_sdk/element_sdk.js"></script>
  <style>
    body {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #0070d3 0%, #3552c8 100%);
      min-height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .demo-container {
      width: 100%;
      max-width: 1200px;
      display: flex;
      gap: 2rem;
      flex-wrap: wrap;
    }

    .products-section {
      flex: 1;
      min-width: 280px;
    }

    .products-grid {
      display: flex;
      flex-direction: column;
      gap: 2.5rem;
    }

    .category-section {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      padding: 1.5rem;
      backdrop-filter: blur(10px);
    }

    .category-title {
      color: white;
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0 0 1.2rem 0;
      padding-bottom: 0.75rem;
      border-bottom: 3px solid rgba(255, 255, 255, 0.3);
    }

    .category-intro {
      color: #ffffff;
      font-size: 0.9rem;
      line-height: 1.55;
      margin-top: -0.4rem;
      margin-bottom: 1.3rem;
      opacity: 0.9;
    }

    .category-products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1.5rem;
    }

    .product-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }

    .product-icon {
      text-align: center;
      margin-bottom: 1rem;
    }

    .product-icon img {
      width: 100%;
      height: 140px;
      object-fit: cover;
      border-radius: 8px;
      display: block;
    }

    .product-name {
      font-size: 1.125rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }

    .product-price {
      font-size: 1.25rem;
      font-weight: 700;
      color: #0070d3;
      margin-bottom: 0.5rem;
    }

    .product-description {
      font-size: 0.75rem;
      color: #6b7280;
      line-height: 1.4;
      margin-bottom: 0.75rem;
      min-height: 2.1rem;
    }

    .read-article-link {
      display: inline-block;
      font-size: 0.8rem;
      color: #0070d3;
      text-decoration: none;
      margin-bottom: 0.75rem;
      font-weight: 500;
      transition: color 0.2s;
    }

    .read-article-link:hover {
      color: #063c7f;
      text-decoration: underline;
    }

    .add-to-cart-btn, .enquire-btn {
      width: 100%;
      padding: 0.75rem;
      background: #0070d3;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s;
      text-decoration: none;
      display: block;
      text-align: center;
    }

    .add-to-cart-btn:hover, .enquire-btn:hover {
      background: #063c7f;
    }

    .add-to-cart-btn:active, .enquire-btn:active {
      transform: scale(0.98);
    }

    .enquire-btn {
      background: #23a0b4;
    }

    .enquire-btn:hover {
      background: #1a7f8f;
    }

    .floating-cart {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      width: 400px;
      max-width: calc(100% - 4rem);
      max-height: calc(100% - 4rem);
      background: white;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: visible;
      transition: transform 0.3s ease, opacity 0.3s ease;
      z-index: 9999;
      display: flex;
      flex-direction: column;
    }

    .floating-cart.hidden {
      transform: translateX(450px);
      opacity: 0;
      pointer-events: none;
    }

    .floating-cart.empty-cart {
      display: none;
    }

    .cart-header {
      background: #0070d3;
      color: white;
      padding: 1.25rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 16px 16px 0 0;
    }

    .cart-title {
      font-size: 1.25rem;
      font-weight: 700;
    }

    .cart-booking-id {
      font-size: 0.75rem;
      opacity: 0.9;
      margin-top: 2px;
    }

    .cart-count {
      background: rgba(255, 255, 255, 0.3);
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.875rem;
      font-weight: 600;
    }

    .cart-items {
      max-height: 400px;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 1rem;
      flex: 1;
    }

    .cart-item {
      display: flex;
      gap: 1rem;
      padding: 1rem;
      background: #f9fafb;
      border-radius: 8px;
      margin-bottom: 0.75rem;
    }

    .item-icon {
      width: 64px;
      height: 64px;
      border-radius: 8px;
      overflow: hidden;
      flex-shrink: 0;
    }

    .item-icon img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .item-details {
      flex: 1;
    }

    .item-name {
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 0.25rem;
    }

    .item-price {
      color: #0070d3;
      font-weight: 600;
      font-size: 0.875rem;
    }

    .item-controls {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }

    .item-date-picker {
      width: 100%;
      margin-top: 0.75rem;
      padding: 0.5rem;
      border: 2px solid #e5e7eb;
      border-radius: 6px;
      font-size: 0.875rem;
      font-weight: 500;
      color: #1f2937;
      cursor: pointer;
      transition: border-color 0.2s;
    }

    .item-date-picker:hover {
      border-color: #0070d3;
    }

    .item-date-picker:focus {
      outline: none;
      border-color: #0070d3;
      box-shadow: 0 0 0 3px rgba(0, 112, 211, 0.1);
    }

    .item-addons {
      margin-top: 0.75rem;
      padding-top: 0.75rem;
      border-top: 1px solid #e5e7eb;
    }

    .addons-title {
      font-size: 0.75rem;
      font-weight: 600;
      color: #6b7280;
      margin-bottom: 0.5rem;
    }

    .addon-option {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.4rem;
    }

    .addon-checkbox {
      width: 16px;
      height: 16px;
      cursor: pointer;
      accent-color: #0070d3;
    }

    .addon-label {
      font-size: 0.75rem;
      color: #1f2937;
      cursor: pointer;
      flex: 1;
    }

    .addon-price {
      font-size: 0.75rem;
      font-weight: 600;
      color: #0070d3;
    }

    .qty-btn {
      width: 28px;
      height: 28px;
      border: none;
      background: #0070d3;
      color: white;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 700;
      transition: background 0.2s;
    }

    .qty-btn:hover {
      background: #063c7f;
    }

    .qty-display {
      font-weight: 600;
      color: #1f2937;
      min-width: 30px;
      text-align: center;
    }

    .item-total {
      font-weight: 700;
      color: #1f2937;
      margin-left: auto;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
    }

    .item-total-usd {
      font-size: 0.875rem;
    }

    .remove-btn {
      background: #f23d4e;
      color: white;
      border: none;
      padding: 0.25rem 0.75rem;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.875rem;
      font-weight: 600;
      transition: background 0.2s;
    }

    .remove-btn:hover {
      background: #d62839;
    }

    .cart-footer {
      border-top: 2px solid #e5e7eb;
      padding: 1.25rem 1.5rem;
      background: #f9fafb;
      border-radius: 0 0 16px 16px;
    }

    .cart-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.75rem;
    }

    .total-label {
      font-size: 1.125rem;
      font-weight: 600;
      color: #1f2937;
    }

    .total-amount {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
    }

    .total-amount-usd {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0070d3;
    }

    .total-amount-idr {
      font-size: 0.875rem;
      color: #6b7280;
      font-weight: 500;
      margin-top: 0.25rem;
    }

    .total-discount-note {
      font-size: 0.75rem;
      color: #059669;
      margin-top: 0.25rem;
    }

    /* COUPON TICKET STYLE */
    .cart-coupon {
      position: relative;
      margin-bottom: 0.9rem;
      padding: 0.75rem 0.9rem;
      border-radius: 14px;
      background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 40%, #bfdbfe 100%);
      border: 1px dashed #60a5fa;
      cursor: pointer;
      overflow: hidden;
      transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.2s ease;
      display: flex;
      flex-direction: column;
      gap: 0.45rem;
    }

    .cart-coupon::before,
    .cart-coupon::after {
      content: "";
      position: absolute;
      top: 50%;
      width: 20px;
      height: 20px;
      border-radius: 999px;
      background: #f9fafb;
      transform: translateY(-50%);
    }

    .cart-coupon::before {
      left: -10px;
    }

    .cart-coupon::after {
      right: -10px;
    }

    .cart-coupon:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.15);
      background: linear-gradient(135deg, #e0f2fe 0%, #bfdbfe 40%, #bfdbfe 100%);
    }

    .cart-coupon.active {
      border-style: solid;
      border-color: #22c55e;
      background: linear-gradient(135deg, #bbf7d0 0%, #a7f3d0 40%, #6ee7b7 100%);
    }

    .cart-coupon-main {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.5rem;
    }

    .coupon-left {
      display: flex;
      flex-direction: column;
      gap: 0.15rem;
    }

    .coupon-title {
      font-size: 0.85rem;
      font-weight: 600;
      color: #1f2937;
    }

    .coupon-sub {
      font-size: 0.78rem;
      color: #374151;
    }

    .coupon-amount {
      font-size: 0.9rem;
      font-weight: 700;
      color: #1d4ed8;
      white-space: nowrap;
    }

    .cart-coupon.active .coupon-amount {
      color: #047857;
    }

    .coupon-tag {
      font-size: 0.68rem;
      padding: 0.12rem 0.4rem;
      border-radius: 999px;
      background: rgba(37, 99, 235, 0.08);
      color: #1d4ed8;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .cart-coupon.active .coupon-tag {
      background: rgba(22, 163, 74, 0.12);
      color: #15803d;
    }

    .coupon-fields {
      display: none;
      flex-direction: column;
      gap: 0.35rem;
      font-size: 0.78rem;
    }

    .cart-coupon.active .coupon-fields {
      display: flex;
    }

    .coupon-row {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .coupon-input {
      width: 100%;
      padding: 0.35rem 0.5rem;
      border-radius: 6px;
      border: 1px solid #d1d5db;
      font-size: 0.78rem;
      background: rgba(255, 255, 255, 0.9);
    }

    .coupon-input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
      background: #ffffff;
    }

    .coupon-note {
      font-size: 0.72rem;
      color: #4b5563;
    }

    .coupon-hint {
      font-size: 0.7rem;
      color: #0f172a;
      opacity: 0.85;
    }

    .pay-btn {
      width: 100%;
      padding: 1rem;
      background: #0070d3;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 1.125rem;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
    }

    .pay-btn:hover {
      background: #063c7f;
    }

    .pay-btn:active {
      transform: scale(0.98);
    }

    .pay-btn:disabled {
      background: #9ca3af;
      cursor: not-allowed;
      transform: none;
    }

    .success-message {
      position: fixed;
      top: 2rem;
      right: 2rem;
      background: #23a0b4;
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      font-weight: 600;
      z-index: 2000;
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from {
        transform: translateX(400px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    /* OFFCANVAS LEARN DIVING (LEFT) – LIGHT MODE */
    .learn-offcanvas {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 340px;
      max-width: 80%;
      background: #ffffff;
      color: #111827;
      box-shadow: 8px 0 30px rgba(0,0,0,0.25);
      transform: translateX(-110%);
      transition: transform 0.3s ease;
      z-index: 10000;
      display: flex;
      flex-direction: column;
    }

    .learn-offcanvas.open {
      transform: translateX(0);
    }

    .learn-offcanvas-header {
      padding: 1rem 1.25rem;
      border-bottom: 1px solid rgba(148, 163, 184, 0.5);
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #f9fafb;
    }

    .learn-offcanvas-title {
      font-size: 1rem;
      font-weight: 600;
      color: #111827;
    }

    .learn-offcanvas-close {
      background: transparent;
      border: none;
      color: #4b5563;
      font-size: 1.25rem;
      cursor: pointer;
      padding: 0.25rem 0.5rem;
      border-radius: 999px;
    }

    .learn-offcanvas-close:hover {
      background: #e5e7eb;
      color: #111827;
    }

    .learn-offcanvas-body {
      padding: 1rem 1.25rem;
      overflow-y: auto;
      flex: 1;
      background: #f9fafb;
    }

    .learn-course-name {
      font-size: 0.9rem;
      font-weight: 500;
      color: #111827;
      margin-bottom: 0.75rem;
    }

    .learn-course-note {
      font-size: 0.8rem;
      color: #4b5563;
      margin-bottom: 1rem;
    }

    .learn-option {
      display: flex;
      align-items: flex-start;
      gap: 0.6rem;
      padding: 0.6rem 0.5rem;
      border-radius: 0.5rem;
      cursor: pointer;
      border: 1px solid transparent;
      margin-bottom: 0.4rem;
      background: #ffffff;
      transition: background 0.15s ease, border-color 0.15s ease;
    }

    .learn-option:hover {
      background: #f3f4f6;
      border-color: rgba(59, 130, 246, 0.6);
    }

    .learn-option input[type="radio"] {
      margin-top: 0.15rem;
      cursor: pointer;
    }

    .learn-option-text {
      flex: 1;
    }

    .learn-option-label {
      font-size: 0.85rem;
      font-weight: 500;
      color: #111827;
      margin-bottom: 0.1rem;
    }

    .learn-option-desc {
      font-size: 0.75rem;
      color: #4b5563;
      margin-bottom: 0.15rem;
    }

    .learn-option-price {
      font-size: 0.8rem;
      color: #0ea5e9;
      font-weight: 600;
    }

    .learn-option-price span {
      display: block;
      font-size: 0.72rem;
      color: #6b7280;
      font-weight: 500;
    }

    .learn-offcanvas-footer {
      padding: 0.9rem 1.25rem 1.1rem;
      border-top: 1px solid rgba(148, 163, 184, 0.5);
      background: #ffffff;
    }

    .learn-confirm-btn {
      width: 100%;
      padding: 0.8rem;
      border-radius: 0.75rem;
      border: none;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      background: #0ea5e9;
      color: #0f172a;
      transition: background 0.2s, transform 0.1s;
    }

    .learn-confirm-btn:hover:enabled {
      background: #0369a1;
      color: #e5e7eb;
    }

    .learn-confirm-btn:active:enabled {
      transform: scale(0.98);
    }

    .learn-confirm-btn:disabled {
      background: #d1d5db;
      color: #9ca3af;
      cursor: not-allowed;
    }

    @media (max-width: 768px) {
      .floating-cart {
        bottom: 1rem;
        right: 1rem;
        width: calc(100% - 2rem);
      }

      .category-products {
        grid-template-columns: 1fr;
      }

      .category-title {
        font-size: 1.25rem;
      }
    }
  </style>
  <style>@view-transition { navigation: auto; }</style>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>
 </head>
 <body>
  <div class="demo-container">
   <div class="products-section">
    <div class="products-grid" id="productsGrid"></div>
   </div>
  </div>

  <!-- OFFCANVAS LEARN DIVING -->
  <div class="learn-offcanvas" id="learnOffcanvas">
    <div class="learn-offcanvas-header">
      <div class="learn-offcanvas-title" id="learnOffcanvasTitle">Choose your PADI program focus</div>
      <button class="learn-offcanvas-close" type="button" onclick="closeLearnOffcanvas()">×</button>
    </div>
    <div class="learn-offcanvas-body">
      <div class="learn-course-name" id="learnCourseName"></div>
      <div class="learn-course-note">
        All courses follow official <strong>PADI</strong> standards and certification pathways.
        Choose the program style that fits how you prefer to learn.
      </div>
      <div id="learnOffcanvasBody"></div>
    </div>
    <div class="learn-offcanvas-footer">
      <button 
        type="button" 
        class="learn-confirm-btn" 
        id="learnConfirmBtn" 
        disabled
        onclick="confirmLearnVariant()"
      >
        Add to Your Booking Plan
      </button>
    </div>
  </div>

  <div class="floating-cart empty-cart" id="floatingCart">
   <div class="cart-header">
    <div>
      <h2 class="cart-title" id="cartTitle">Your Booking Plan</h2>
      <div class="cart-booking-id" id="cartBookingId">Booking ID: -</div>
    </div>
    <span class="cart-count" id="cartCount">0 items</span>
   </div>
   <div class="cart-items" id="cartItems"></div>
   <div class="cart-footer" id="cartFooter" style="display: none;">
    <div class="cart-total-row">
     <span class="total-label">Total:</span>
     <div class="total-amount" id="totalAmount">
      <span class="total-amount-usd">$0.00</span>
      <span class="total-amount-idr">Rp 0</span>
     </div>
    </div>

    <!-- COUPON TICKET -->
    <div class="cart-coupon" id="cartCoupon" onclick="toggleCoupon()">
      <div class="cart-coupon-main">
        <div class="coupon-left">
          <div class="coupon-title">Welcome Dive Coupon</div>
          <div
          <div class="coupon-hint">Applied only once per booking plan.</div>
        </div>
        <div>
          <div class="coupon-amount">−$1.19</div>
       
        </div>
      </div>
      <div class="coupon-fields" onclick="event.stopPropagation()">
        <div class="coupon-row">
          <input 
            type="text" 
            id="customerName" 
            class="coupon-input" 
            placeholder="Your full name"
            oninput="onCouponDataChange()"
          >
        </div>
        <div class="coupon-row">
          <input 
            type="email" 
            id="customerEmail" 
            class="coupon-input" 
            placeholder="Your best email"
            oninput="onCouponDataChange()"
          >
        </div>
        <div class="coupon-note">
          We use your name and email to lock in this discount and send your booking details. No spam.
        </div>
      </div>
    </div>

    <button class="pay-btn" id="payBtn">Checkout Now</button>
   </div>
  </div>

  <script>
    const defaultConfig = {
      cart_title: "Your Booking Plan",
      button_text: "Checkout Now",
      empty_message: "Your booking plan is empty",
      primary_color: "#0070d3",
      secondary_color: "#063c7f",
      accent_color: "#a2d2fa",
      highlight_color: "#f23d4e",
      text_color: "#063c7f",
      background_color: "#ffffff",
      success_color: "#23a0b4"
    };

    const USD_TO_IDR = 15800;
    const COUPON_AMOUNT = 1.19;

    function generateBookingId() {
      const now = new Date();
      const y = now.getFullYear();
      const m = String(now.getMonth() + 1).padStart(2, '0');
      const d = String(now.getDate()).padStart(2, '0');
      const rand = Math.floor(Math.random() * 9000) + 1000;
      return `BDV-${y}${m}${d}-${rand}`;
    }

    const BOOKING_ID = generateBookingId();

    const products = [
      // Snorkeling
      { id: 1, name: "Padang Bai", price: 25, icon: "🐠", category: "Snorkeling", description: "Calm bay with rich marine life and colorful corals" },
      { id: 2, name: "Tulamben", price: 30, icon: "🚢", category: "Snorkeling", description: "Snorkel above the famous USAT Liberty shipwreck in shallow waters" },
      { id: 3, name: "Amed", price: 22, icon: "🐟", category: "Snorkeling", description: "Beautiful coral gardens perfect for snorkeling and underwater photos" },
      { id: 4, name: "Nusa Penida", price: 35, icon: "🐢", category: "Snorkeling", description: "Crystal clear waters with turtles and vibrant tropical fish" },
      { id: 5, name: "Manta Point", price: 40, icon: "🦈", category: "Snorkeling", description: "Swim with majestic manta rays in their natural habitat" },
      
      // Try Diving
      { id: 6, name: "Tulamben", price: 85, icon: "🚢", category: "Try Diving", description: "Guided shallow dives at the USAT Liberty shipwreck for first-time divers." },
      { id: 7, name: "Padang Bai", price: 75, icon: "🤿", category: "Try Diving", description: "Calm, clear conditions for your very first underwater experience." },
      { id: 8, name: "Amed", price: 70, icon: "🐡", category: "Try Diving", description: "Safe introduction to scuba diving with beautiful coral reefs." },
      
      // Fun Diving
      { id: 9, name: "Tulamben", price: 55, icon: "🚢", category: "Fun Diving", description: "Explore the legendary USAT Liberty wreck teeming with marine life." },
      { id: 10, name: "Padang Bai", price: 48, icon: "🐠", category: "Fun Diving", description: "Multiple dive sites with diverse underwater landscapes and creatures." },
      { id: 11, name: "Amed", price: 50, icon: "🤿", category: "Fun Diving", description: "Japanese wreck and vibrant coral gardens for certified divers." },
      { id: 12, name: "Manta Point", price: 75, icon: "🦈", category: "Fun Diving", description: "Unforgettable encounters with graceful manta rays and pelagic fish." },
      { id: 13, name: "Tepekong", price: 65, icon: "🦑", category: "Fun Diving", description: "Advanced drift dive with sharks and strong currents." },
      { id: 14, name: "Kubu", price: 58, icon: "🐙", category: "Fun Diving", description: "Dramatic underwater scenery with macro photography opportunities and fish." },
      
      // Diving Safari (Multi-day)
      { id: 15, name: "Menjangan", price: 0, icon: "🏝️", category: "Diving Safari (Multi-day)", description: "Pristine marine park with stunning wall dives variety.", isEnquiry: true },
      { id: 16, name: "East Coast (2D1N)", price: 0, icon: "🌅", category: "Diving Safari (Multi-day)", description: "Two day diving adventure exploring Bali's best eastern dive sites.", isEnquiry: true },
      { id: 17, name: "Banyuwangi", price: 0, icon: "🏔️", category: "Diving Safari (Multi-day)", description: "Volcanic underwater landscapes with unique marine biodiversity and pelagics.", isEnquiry: true },
      { id: 18, name: "Sumbawa", price: 0, icon: "🌊", category: "Diving Safari (Multi-day)", description: "Remote pristine reefs with world class diving experiences untouched.", isEnquiry: true },
      { id: 19, name: "Other", price: 0, icon: "✨", category: "Diving Safari (Multi-day)", description: "Custom multi day diving safari tailored to your preferences.", isEnquiry: true },
      
      // Learn Diving (all PADI programs)
      { id: 20, name: "PADI Discover Scuba Diving", price: 95, icon: "🎓", category: "Learn Diving", description: "First PADI experience: pool session + guided ocean introduction." },
      { id: 21, name: "PADI Open Water Diver", price: 450, icon: "📚", category: "Learn Diving", description: "Full entry level PADI certification to dive independently with a buddy." },
      { id: 22, name: "PADI Advanced Open Water", price: 380, icon: "🏆", category: "Learn Diving", description: "Level up your skills with deep, navigation, and other adventure dives." },
      { id: 23, name: "PADI Specialty Programs", price: 280, icon: "🎯", category: "Learn Diving", description: "Focused PADI specialties such as Nitrox, Wreck, or Peak Performance Buoyancy." },
      { id: 24, name: "PADI Divemaster Program", price: 950, icon: "⭐", category: "Learn Diving", description: "First professional level in the PADI system, train as a dive leader." }
    ];

    const productImages = {
      1: "https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg",
      2: "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg",
      3: "https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg",
      4: "https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg",
      5: "https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg",
      6: "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
      7: "https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg",
      8: "https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg",
      9: "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
      10: "https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg",
      11: "https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg",
      12: "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
      13: "https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg",
      14: "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg",
      15: "https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg",
      16: "https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg",
      17: "https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg",
      18: "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
      19: "https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg",
      20: "https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg",
      21: "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
      22: "https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg",
      23: "https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg",
      24: "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg"
    };

    const learnVariants = {
      20: [
        {
          id: "20_padi_dsd_standard",
          label: "PADI Discover Scuba Diving – Standard",
          desc: "Confident first PADI experience: pool practice + 1 easy ocean dive with instructor.",
          price: 95
        },
        {
          id: "20_padi_dsd_plus",
          label: "PADI Discover Scuba Diving – Extra Ocean Dive",
          desc: "Same PADI intro plus 2nd ocean dive for more time underwater and photos.",
          price: 130
        }
      ],
      21: [
        {
          id: "21_padi_ow_standard",
          label: "PADI Open Water Diver – Standard",
          desc: "Classic 3-day PADI Open Water course: theory, pool, and 4 open water dives.",
          price: 450
        },
        {
          id: "21_padi_ow_premium",
          label: "PADI Open Water Diver – Premium",
          desc: "More personal time with instructor, smaller group and flexible training pace.",
          price: 520
        }
      ],
      22: [
        {
          id: "22_padi_aow_standard",
          label: "PADI Advanced Open Water – 5 Adventure Dives",
          desc: "Deep, navigation + 3 more PADI adventure dives tailored to Bali conditions.",
          price: 380
        },
        {
          id: "22_padi_aow_deep_drift",
          label: "PADI Advanced – Deep & Drift Focus",
          desc: "Advanced course with extra coaching for deep dives and Bali-style currents.",
          price: 410
        }
      ],
      23: [
        {
          id: "23_padi_nitrox",
          label: "PADI Enriched Air (Nitrox)",
          desc: "Official PADI Nitrox specialty to extend no-stop limits on repetitive dives.",
          price: 280
        },
        {
          id: "23_padi_wreck_combo",
          label: "PADI Wreck + Photo Coaching",
          desc: "Wreck-focused PADI diving with extra tips for better Tulamben photo sessions.",
          price: 320
        }
      ],
      24: [
        {
          id: "24_padi_dm_standard",
          label: "PADI Divemaster – Standard Internship",
          desc: "Start your professional path in the PADI system, assisting courses & guiding.",
          price: 950
        },
        {
          id: "24_padi_dm_extended",
          label: "PADI Divemaster – Extended Pro",
          desc: "Longer internship with more dives, mentoring, and leadership experience.",
          price: 1200
        }
      ]
    };

    const addons = [
      { id: 'gopro', name: 'GoPro Camera Rental', price: 15 },
      { id: 'computer', name: 'Dive Computer Rental', price: 10 }
    ];

    let cart = [];
    let activeLearnProduct = null;
    let selectedLearn = null;
    let couponActive = false;

    function formatUSD(amount) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
      }).format(amount);
    }

    function formatIDR(amount) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(amount);
    }

    function convertToIDR(usdAmount) {
      return usdAmount * USD_TO_IDR;
    }

    function getCategoryIntro(category) {
      if (category === "Snorkeling") {
        return `
          <p class="category-intro">
            Go on one of our unforgettable snorkeling trips and explore Bali’s signature underwater highlights:
            colourful coral bommies, clear white-sand bottoms, and even the famous World War II shipwreck.
            You can also meet the magical manta rays around Nusa Penida.
            Snorkeling is suitable for almost everyone, including beginners and those who are not confident swimmers,
            as you will be guided and supported the whole time.
          </p>
        `;
      }

      if (category === "Try Diving") {
        return `
          <p class="category-intro">
            If you do not have a diving certification or have never dived before, we can take you diving at these sites
            on a PADI “Try Scuba Diving” program. It is still two dives, the same as for certified divers, but you will be
            supervised directly by a qualified instructor at a maximum ratio of two divers per guide and limited to a
            maximum depth of 12 metres.
          </p>
        `;
      }

      if (category === "Learn Diving") {
        return `
          <p class="category-intro">
            Through our PADI courses you will progress from pool training to Bali’s top open water dive sites.
            You start as a beginner and finish as a certified diver, ready to explore the underwater world with confidence.
          </p>
        `;
      }

      if (category === "Fun Diving") {
        return `
          <p class="category-intro">
            For certified divers, this is the right place to create your ultimate diving adventures and explore Bali’s
            best dive sites. Divers travel from all over the world to enjoy these conditions and build unforgettable
            dive logs with us.
          </p>
        `;
      }

      return "";
    }

    function renderProducts() {
      const grid = document.getElementById('productsGrid');
      
      const groupedProducts = products.reduce((acc, product) => {
        if (!acc[product.category]) {
          acc[product.category] = [];
        }
        acc[product.category].push(product);
        return acc;
      }, {});

      grid.innerHTML = Object.keys(groupedProducts).map(category => `
        <div class="category-section">
          <h3 class="category-title">${category}</h3>
          ${getCategoryIntro(category)}
          <div class="category-products">
            ${
              groupedProducts[category].map(product => {
                const imageUrl = productImages[product.id] || "https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg";
                return `
                  <div class="product-card">
                    <div class="product-icon">
                      <img src="${imageUrl}" alt="${product.name}">
                    </div>
                    <div class="product-name">${product.name}</div>
                    ${
                      product.isEnquiry ? 
                        '<div class="product-price">Contact for pricing</div>' :
                        `<div class="product-price">${formatUSD(product.price)}</div>`
                    }
                    <div class="product-description">${product.description}</div>
                    <a href="#" class="read-article-link" onclick="event.preventDefault()">📖 Read Article</a>
                    ${
                      product.isEnquiry ? 
                        '<a href="https://balidiving.com/contact?page=contact" target="_blank" rel="noopener noreferrer" class="enquire-btn">Enquire</a>' :
                        `<button class="add-to-cart-btn" onclick="addToCart(${product.id})">Add to Cart</button>`
                    }
                  </div>
                `;
              }).join('')
            }
          </div>
        </div>
      `).join('');
    }

    function addToCart(productId) {
      const product = products.find(p => p.id === productId);
      if (!product) return;

      if (product.category === "Learn Diving") {
        openLearnOffcanvas(productId);
        return;
      }

      const existingItem = cart.find(item => item.id === productId);

      if (existingItem) {
        existingItem.quantity += 1;

        if (existingItem.quantity > 8) {
          existingItem.quantity = 8;
          showSuccessMessage("Please make 2 booking for more 8 Pax");
        }
      } else {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const dateString = tomorrow.toISOString().split('T')[0];

        cart.push({ ...product, quantity: 1, bookingDate: dateString, addons: [] });
      }

      renderCart();
      showSuccessMessage(`${product.name} added to booking plan!`);
    }

    function updateQuantity(productId, change) {
      const item = cart.find(item => item.id === productId);
      if (item) {
        item.quantity += change;

        if (item.quantity > 8) {
          item.quantity = 8;
          showSuccessMessage("please make 2 different booking for more 8 Pax");
        }

        if (item.quantity <= 0) {
          removeFromCart(productId);
        } else {
          renderCart();
        }
      }
    }

    function removeFromCart(productId) {
      cart = cart.filter(item => item.id !== productId);
      renderCart();
    }

    function updateBookingDate(productId, newDate) {
      const item = cart.find(item => item.id === productId);
      if (item) {
        item.bookingDate = newDate;
      }
    }

    function toggleAddon(productId, addonId) {
      const item = cart.find(item => item.id === productId);
      if (item) {
        const addonIndex = item.addons.indexOf(addonId);
        if (addonIndex > -1) {
          item.addons.splice(addonIndex, 1);
        } else {
          item.addons.push(addonId);
        }
        renderCart();
      }
    }

    function getMinDate() {
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      return tomorrow.toISOString().split('T')[0];
    }

    function getRawTotals() {
      const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
      const totalPriceUSD = cart.reduce((sum, item) => {
        let itemTotal = item.price * item.quantity;
        item.addons.forEach(addonId => {
          const addon = addons.find(a => a.id === addonId);
          if (addon) {
            itemTotal += addon.price * item.quantity;
          }
        });
        return sum + itemTotal;
      }, 0);
      const totalPriceIDR = convertToIDR(totalPriceUSD);
      return { totalItems, totalPriceUSD, totalPriceIDR };
    }

    function getCustomerData() {
      const nameEl = document.getElementById('customerName');
      const emailEl = document.getElementById('customerEmail');

      return {
        name: nameEl ? nameEl.value.trim() : "",
        email: emailEl ? emailEl.value.trim() : ""
      };
    }

    function isCouponValid() {
      if (!couponActive) return false;
      const { name, email } = getCustomerData();
      return name !== "" && email !== "";
    }

    function getCouponAmount() {
      if (!isCouponValid()) return 0;
      return COUPON_AMOUNT;
    }

    function toggleCoupon() {
      if (cart.length === 0) {
        showSuccessMessage("Add at least one activity before using the coupon.");
        return;
      }
      couponActive = !couponActive;
      const couponEl = document.getElementById('cartCoupon');
      if (couponEl) {
        if (couponActive) {
          couponEl.classList.add('active');
          const tag = couponEl.querySelector('.coupon-tag');
          if (tag) tag.textContent = "Applied";
        } else {
          couponEl.classList.remove('active');
          const tag = couponEl.querySelector('.coupon-tag');
          if (tag) tag.textContent = "Tap to apply";
        }
      }
      renderCart();
    }

    function onCouponDataChange() {
      if (couponActive) {
        renderCart();
      }
    }

    function renderCart() {
      const floatingCart = document.getElementById('floatingCart');
      const cartItems = document.getElementById('cartItems');
      const cartCount = document.getElementById('cartCount');
      const totalAmount = document.getElementById('totalAmount');
      const cartFooter = document.getElementById('cartFooter');

      const { totalItems, totalPriceUSD } = getRawTotals();
      const discountUSD = getCouponAmount();
      const finalTotalUSD = Math.max(0, totalPriceUSD - discountUSD);
      const finalTotalIDR = convertToIDR(finalTotalUSD);

      cartCount.textContent = `${totalItems} item${totalItems !== 1 ? 's' : ''}`;
      totalAmount.innerHTML = `
        <span class="total-amount-usd">${formatUSD(finalTotalUSD)}</span>
        <span class="total-amount-idr">${formatIDR(finalTotalIDR)}</span>
        ${discountUSD > 0 ? `<span class="total-discount-note">Coupon discount: −${formatUSD(discountUSD)}</span>` : ''}
      `;

      if (cart.length === 0) {
        floatingCart.classList.add('empty-cart');
        cartItems.innerHTML = '';
        cartFooter.style.display = 'none';
      } else {
        floatingCart.classList.remove('empty-cart');
        cartItems.innerHTML = cart.map(item => {
          let itemTotalUSD = item.price * item.quantity;
          
          item.addons.forEach(addonId => {
            const addon = addons.find(a => a.id === addonId);
            if (addon) {
              itemTotalUSD += addon.price * item.quantity;
            }
          });
          
          const imageUrl = productImages[item.id] || "https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg";
          
          return `
          <div class="cart-item">
            <div class="item-icon">
              <img src="${imageUrl}" alt="${item.name}">
            </div>
            <div class="item-details">
              <div class="item-name">${item.name}</div>
              <div class="item-price">${formatUSD(item.price)}</div>
              <input 
                type="date" 
                class="item-date-picker" 
                value="${item.bookingDate}" 
                min="${getMinDate()}"
                onchange="updateBookingDate(${item.id}, this.value)"
                title="Select booking date (minimum tomorrow)"
              />
              <div class="item-addons">
                <div class="addons-title">Add Equipment:</div>
                ${addons.map(addon => `
                  <div class="addon-option">
                    <input 
                      type="checkbox" 
                      class="addon-checkbox" 
                      id="addon-${item.id}-${addon.id}"
                      ${item.addons.includes(addon.id) ? 'checked' : ''}
                      onchange="toggleAddon(${item.id}, '${addon.id}')"
                    />
                    <label class="addon-label" for="addon-${item.id}-${addon.id}">
                      ${addon.name}
                    </label>
                    <span class="addon-price">+${formatUSD(addon.price)}</span>
                  </div>
                `).join('')}
              </div>
              <div class="item-controls">
                <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">−</button>
                <span class="qty-display">${item.quantity}</span>
                <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                <button class="remove-btn" onclick="removeFromCart(${item.id})">Remove</button>
                <div class="item-total">
                  <span class="item-total-usd">${formatUSD(itemTotalUSD)}</span>
                </div>
              </div>
            </div>
          </div>
        `;
        }).join('');
        cartFooter.style.display = 'block';
      }
    }

    function showSuccessMessage(message) {
      const existingMsg = document.querySelector('.success-message');
      if (existingMsg) {
        existingMsg.remove();
      }

      const msgDiv = document.createElement('div');
      msgDiv.className = 'success-message';
      msgDiv.textContent = message;
      document.body.appendChild(msgDiv);

      setTimeout(() => {
        msgDiv.remove();
      }, 2000);
    }

    async function handlePayment() {
      if (cart.length === 0) {
        showSuccessMessage(defaultConfig.empty_message);
        return;
      }

      const { name, email } = getCustomerData();

      if (couponActive && !isCouponValid()) {
        showSuccessMessage("Please add your name and email to enjoy the $1.19 welcome discount 😊");
        const couponEl = document.getElementById('cartCoupon');
        if (couponEl) couponEl.classList.add('active');
        return;
      }

      const { totalPriceUSD: rawTotalUSD } = getRawTotals();
      const discountUSD = getCouponAmount();
      const finalTotalUSD = Math.max(0, rawTotalUSD - discountUSD);
      const finalTotalIDR = convertToIDR(finalTotalUSD);

      const payload = {
        cart,
        booking_ref: BOOKING_ID,
        totals: {
          raw_usd: rawTotalUSD,
          raw_idr: convertToIDR(rawTotalUSD),
          discount_usd: discountUSD,
          final_usd: finalTotalUSD,
          final_idr: finalTotalIDR,
          currency: "IDR"
        },
        customer: {
          name,
          email,
          coupon_used: isCouponValid(),
          coupon_value_usd: discountUSD
        },
        source: "cart.php"
      };

      try {
        const res = await fetch('/template/api/create-payment.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        if (!res.ok) {
          showSuccessMessage("Payment init failed, please try again.");
          return;
        }

        const data = await res.json();

        if (!data.ok || !data.payment_url) {
          showSuccessMessage("Payment not ready, please try again.");
          return;
        }

        window.location.href = data.payment_url;

      } catch (err) {
        console.error(err);
        showSuccessMessage("Payment error, please try again.");
      }
    }

    function openLearnOffcanvas(productId) {
      const product = products.find(p => p.id === productId);
      if (!product) return;

      activeLearnProduct = product;
      selectedLearn = null;

      const variants = learnVariants[productId] || [];
      const offcanvas = document.getElementById('learnOffcanvas');
      const titleEl = document.getElementById('learnOffcanvasTitle');
      const courseNameEl = document.getElementById('learnCourseName');
      const bodyEl = document.getElementById('learnOffcanvasBody');
      const btn = document.getElementById('learnConfirmBtn');

      titleEl.textContent = "Choose your PADI program focus";
      courseNameEl.textContent = product.name + " – " + product.category;

      if (!variants.length) {
        bodyEl.innerHTML = `
          <p style="font-size:0.8rem;color:#4b5563;">
            No PADI program variant configured yet for this course. Please contact us for details.
          </p>
        `;
        btn.disabled = true;
      } else {
        bodyEl.innerHTML = variants.map(v => `
          <label class="learn-option">
            <input 
              type="radio" 
              name="learnVariant" 
              value="${v.id}"
              onchange="selectLearnVariant(${productId}, '${v.id}')"
            />
            <div class="learn-option-text">
              <div class="learn-option-label">${v.label}</div>
              <div class="learn-option-desc">${v.desc}</div>
              <div class="learn-option-price">
                ${formatUSD(v.price)}
                <span>${formatIDR(convertToIDR(v.price))}</span>
              </div>
            </div>
          </label>
        `).join('');
        btn.disabled = true;
      }

      offcanvas.classList.add('open');
    }

    function closeLearnOffcanvas() {
      const offcanvas = document.getElementById('learnOffcanvas');
      offcanvas.classList.remove('open');
      activeLearnProduct = null;
      selectedLearn = null;
      document.getElementById('learnConfirmBtn').disabled = true;
    }

    function selectLearnVariant(productId, variantId) {
      selectedLearn = { productId, variantId };
      const btn = document.getElementById('learnConfirmBtn');
      btn.disabled = false;
    }

    function confirmLearnVariant() {
      if (!activeLearnProduct || !selectedLearn) return;

      const variants = learnVariants[selectedLearn.productId] || [];
      const variant = variants.find(v => v.id === selectedLearn.variantId);
      if (!variant) return;

      const base = activeLearnProduct;
      const customName = base.name + " – " + variant.label;

      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const dateString = tomorrow.toISOString().split('T')[0];

      cart.push({
        id: base.id,
        name: customName,
        category: base.category,
        price: variant.price,
        quantity: 1,
        bookingDate: dateString,
        addons: []
      });

      renderCart();
      showSuccessMessage(`${customName} added to booking plan!`);
      closeLearnOffcanvas();
    }

    async function onConfigChange(config) {
      document.getElementById('cartTitle').textContent = config.cart_title || defaultConfig.cart_title;
      document.getElementById('payBtn').textContent = config.button_text || defaultConfig.button_text;

      const primaryColor = config.primary_color || defaultConfig.primary_color;
      const highlightColor = config.highlight_color || defaultConfig.highlight_color;
      const successColor = config.success_color || defaultConfig.success_color;

      document.querySelectorAll('.cart-header').forEach(el => {
        el.style.background = primaryColor;
      });

      document.querySelectorAll('.add-to-cart-btn').forEach(el => {
        el.style.background = primaryColor;
      });

      document.querySelectorAll('.qty-btn').forEach(el => {
        el.style.background = primaryColor;
      });

      document.querySelectorAll('.pay-btn').forEach(el => {
        el.style.background = primaryColor;
      });

      document.querySelectorAll('.remove-btn').forEach(el => {
        el.style.background = highlightColor;
      });

      document.querySelectorAll('.product-price, .item-price, .total-amount-usd, .addon-price, .read-article-link').forEach(el => {
        el.style.color = primaryColor;
      });

      document.querySelectorAll('.success-message').forEach(el => {
        el.style.background = successColor;
      });

      document.querySelectorAll('.addon-checkbox').forEach(el => {
        el.style.accentColor = primaryColor;
      });
    }

    if (window.elementSdk) {
      window.elementSdk.init({
        defaultConfig,
        onConfigChange,
        mapToCapabilities: (config) => ({
          recolorables: [
            {
              get: () => config.background_color || defaultConfig.background_color,
              set: (value) => {
                config.background_color = value;
                window.elementSdk.setConfig({ background_color: value });
              }
            },
            {
              get: () => config.primary_color || defaultConfig.primary_color,
              set: (value) => {
                config.primary_color = value;
                window.elementSdk.setConfig({ primary_color: value });
              }
            },
            {
              get: () => config.secondary_color || defaultConfig.secondary_color,
              set: (value) => {
                config.secondary_color = value;
                window.elementSdk.setConfig({ secondary_color: value });
              }
            },
            {
              get: () => config.highlight_color || defaultConfig.highlight_color,
              set: (value) => {
                config.highlight_color = value;
                window.elementSdk.setConfig({ highlight_color: value });
              }
            },
            {
              get: () => config.success_color || defaultConfig.success_color,
              set: (value) => {
                config.success_color = value;
                window.elementSdk.setConfig({ success_color: value });
              }
            }
          ],
          borderables: [],
          fontEditable: undefined,
          fontSizeable: undefined
        }),
        mapToEditPanelValues: (config) => new Map([
          ["cart_title", config.cart_title || defaultConfig.cart_title],
          ["button_text", config.button_text || defaultConfig.button_text],
          ["empty_message", config.empty_message || defaultConfig.empty_message]
        ])
      });
    }

    document.getElementById('payBtn').addEventListener('click', handlePayment);

    const bookingIdEl = document.getElementById('cartBookingId');
    if (bookingIdEl) {
      bookingIdEl.textContent = `Booking ID: ${BOOKING_ID}`;
    }

    let scrollTimeout;
    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
      const floatingCart = document.getElementById('floatingCart');
      const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

      if (Math.abs(currentScroll - lastScrollTop) > 10) {
        floatingCart.classList.add('hidden');
      }

      lastScrollTop = currentScroll;

      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(function() {
        floatingCart.classList.remove('hidden');
      }, 150);
    });

    renderProducts();
    renderCart();
  </script>
  <script>
    (function(){
      function c(){
        var b=a.contentDocument||a.contentWindow.document;
        if(b){
          var d=b.createElement('script');
          d.innerHTML="window.__CF$cv$params={r:'9a5f4619354a2035',t:'MTc2NDM4OTYyMS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";
          b.getElementsByTagName('head')[0].appendChild(d)
        }
      }
      if(document.body){
        var a=document.createElement('iframe');
        a.height=1;a.width=1;
        a.style.position='absolute';
        a.style.top=0;a.style.left=0;
        a.style.border='none';
        a.style.visibility='hidden';
        document.body.appendChild(a);
        if('loading'!==document.readyState) c();
        else if(window.addEventListener)
          document.addEventListener('DOMContentLoaded',c);
        else{
          var e=document.onreadystatechange||function(){};
          document.onreadystatechange=function(b){
            e(b);
            'loading'!==document.readyState&&(document.onreadystatechange=e,c())
          }
        }
      }
    })();
  </script>
 </body>
</html>
