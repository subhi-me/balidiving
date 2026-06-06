/**
 * ============================================================
 * 2) /cart/cart.js — BD_CART_V1 (REVISI FINAL + GUEST DETAIL)
 * ============================================================
 */
(() => {
  "use strict";

  const STORAGE_KEY = "BD_CART_V1";
  const CUSTOMER_KEY = "BD_CART_CUSTOMER_V1";

  const FX_API_BASE = "/template/api/fx_rates";
  const FX_BASE = "USD";
  const FX_QUOTE = "IDR";
  const FX_SOURCE_LABEL = "BCA";

  const TIMEOUT_MS = 6000;
  const RATE_FALLBACK = +window.USD_TO_IDR || 16780;

  const PRODUCT_MAP = window.PRODUCT_MAP || {};
  const PRODUCT_IMAGES = window.PRODUCT_IMAGES || {};
  const ADDONS = window.ADDONS || {};

  const UI = {
    cartItems: "cartItems",
    cartCount: "cartCount",
    totalUsd: "totalUsd",
    totalIdr: "totalIdr",
    custName: "custName",
    custEmail: "custEmail",
    custPhone: "custPhone",
    custCert: "custCert",
    cartBookingId: "cartBookingId",
  };

  const $ = (id) => document.getElementById(id);

  const elItems = $(UI.cartItems);
  const elCount = $(UI.cartCount);
  const elUsd = $(UI.totalUsd);
  const elIdr = $(UI.totalIdr);
  const elName = $(UI.custName);
  const elEmail = $(UI.custEmail);
  const elPhone = $(UI.custPhone);
  const elCert = $(UI.custCert);
  const elBid = $(UI.cartBookingId);

  let elRate = null;
  let elApprox = null;

  (function injectStyles() {
    if (document.getElementById("bd-cart-style-idr-main")) return;

    const style = document.createElement("style");
    style.id = "bd-cart-style-idr-main";
    style.textContent = `
      #${UI.totalIdr}{font-weight:900!important;font-size:1.18em!important;letter-spacing:.2px}
      .bd-usd-approx{margin-top:4px;font-size:12px;opacity:.78;line-height:1.2}
      .bd-rate-line{margin-top:4px;font-size:12px;opacity:.8;line-height:1.2}
      .bd-rate-loading{display:flex;gap:10px;align-items:center;padding:14px 14px;border-radius:12px;border:1px solid rgba(0,0,0,.08);background:rgba(255,255,255,.65);backdrop-filter:blur(6px)}
      .bd-spinner{width:18px;height:18px;border-radius:999px;border:2px solid rgba(0,0,0,.12);border-top-color:rgba(0,0,0,.6);animation:bdspin .9s linear infinite;flex:0 0 auto}
      @keyframes bdspin{to{transform:rotate(360deg)}}
      .bd-loading-text{font-size:13px;opacity:.88}
      .bd-loading-sub{font-size:12px;opacity:.65;margin-top:2px}
      .bd-line-idr{font-weight:800}
      .bd-line-sub{font-size:12px;opacity:.7;margin-top:2px}
    `;
    document.head.appendChild(style);
  })();

  const escapeHtml = (s) =>
    String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");

  const formatUsd = (n) => (+n || 0).toFixed(2);
  const formatUsdApprox = (n) => `≈ US$ ${formatUsd(n)}`;

  const formatIdr = (n) =>
    "Rp " + Math.round(+n || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  const toast = (msg) => {
    const d = document.createElement("div");
    d.className = "cart-msg";
    d.textContent = msg;
    document.body.appendChild(d);
    setTimeout(() => d.remove(), 2200);
  };

  const today = () => {
    const d = new Date();
    return (
      d.getFullYear() +
      "-" +
      String(d.getMonth() + 1).padStart(2, "0") +
      "-" +
      String(d.getDate()).padStart(2, "0")
    );
  };

  const b64url = (s) =>
    btoa(unescape(encodeURIComponent(s)))
      .replaceAll("+", "-")
      .replaceAll("/", "_")
      .replaceAll("=", "");

  const withTimeout = (promise, ms, ctrl) =>
    new Promise((resolve, reject) => {
      const t = setTimeout(() => {
        try { ctrl?.abort(); } catch {}
        reject(new Error("timeout"));
      }, ms);
      promise.then(
        (v) => { clearTimeout(t); resolve(v); },
        (e) => { clearTimeout(t); reject(e); }
      );
    });

  const productLabel = (p) => {
    const cat = String(p?.category || "").trim() || "Activity";
    const site = String(p?.name || "").trim() || "Dive Site";
    return `${cat} - ${site}`;
  };

  const normalizePhone = (s) => String(s || "").trim().replace(/[^\d+]/g, "");
  const isPhoneOk = (s) => normalizePhone(s).replace(/[^\d]/g, "").length >= 8;

  // =======================
  // CART STORAGE
  // =======================
  let cart = [];
  try { cart = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]"); } catch { cart = []; }

  cart = (Array.isArray(cart) ? cart : [])
    .map((it) => ({
      pid: +it.pid || +it.product_id || 0,
      q: Math.max(1, +it.q || +it.qty || 1),
      d: String(it.d || it.date || today()),
      a: Array.isArray(it.a || it.addons) ? (it.a || it.addons).map(String) : [],
    }))
    .filter((it) => PRODUCT_MAP[it.pid]);

  const saveCart = () => localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));

  // customer storage
  function loadCustomer() {
    try { return JSON.parse(localStorage.getItem(CUSTOMER_KEY) || "{}") || {}; }
    catch { return {}; }
  }
  function saveCustomer(c) {
    try { localStorage.setItem(CUSTOMER_KEY, JSON.stringify(c || {})); } catch {}
  }

  (function initCustomerFields() {
    const c = loadCustomer();

    if (elName && !elName.value && c.name) elName.value = c.name;
    if (elEmail && !elEmail.value && c.email) elEmail.value = c.email;
    if (elPhone && !elPhone.value && c.phone) elPhone.value = c.phone;

    if (elCert) {
      const cur = (elCert.value || "").trim();
      if (!cur && c.cert_level) elCert.value = c.cert_level;
      if (!elCert.value) elCert.value = "Beginner / No Certificate";
    }

    const sync = () => {
      saveCustomer({
        name: (elName?.value || "").trim(),
        email: (elEmail?.value || "").trim(),
        phone: (elPhone?.value || "").trim(),
        cert_level: (elCert?.value || "Beginner / No Certificate").trim(),
      });
    };

    if (elName) elName.addEventListener("input", sync);
    if (elEmail) elEmail.addEventListener("input", sync);
    if (elPhone) elPhone.addEventListener("input", sync);
    if (elCert) elCert.addEventListener("change", sync);
  })();

  // =======================
  // RATE
  // =======================
  let RATE = 0;
  let RATE_UPDATED_AT = "";

  function ensureRateElements() {
    const anchor = elIdr || elUsd;
    if (!anchor || !anchor.parentElement) return;

    if (!elApprox) {
      elApprox = document.createElement("div");
      elApprox.className = "bd-usd-approx";
      elApprox.id = "bdUsdApproxLine";
      anchor.insertAdjacentElement("afterend", elApprox);
    }
    if (!elRate) {
      elRate = document.createElement("div");
      elRate.className = "bd-rate-line";
      elRate.id = "bdFxRateLine";
      elApprox.insertAdjacentElement("afterend", elRate);
    }
  }

  function showRateLoading() {
    if (!elItems) return;

    elItems.innerHTML = `
      <div class="bd-rate-loading" role="status" aria-live="polite">
        <div class="bd-spinner"></div>
        <div>
          <div class="bd-loading-text">Loading bank rate (${escapeHtml(FX_SOURCE_LABEL)})...</div>
          <div class="bd-loading-sub">Please wait a moment.</div>
        </div>
      </div>
    `;

    if (elIdr) elIdr.textContent = "—";
    if (elUsd) elUsd.textContent = "";

    const pax = cart.reduce((sum, it) => sum + it.q, 0);
    if (elCount) elCount.textContent = `${pax} PAX`;

    ensureRateElements();
    if (elApprox) elApprox.textContent = "≈ US$ —";
    if (elRate) elRate.textContent = `Fetching rate from ${FX_SOURCE_LABEL}...`;
  }

  async function loadRateFromDB() {
    const ctrl = new AbortController();
    const url = `${FX_API_BASE}?base=${encodeURIComponent(FX_BASE)}&quote=${encodeURIComponent(FX_QUOTE)}&ts=${Date.now()}`;
    const fetchPromise = fetch(url, { cache: "no-store", signal: ctrl.signal });

    try {
      const res = await withTimeout(fetchPromise, TIMEOUT_MS, ctrl);
      if (!res.ok) throw new Error("fx api not ok");
      const data = await res.json();
      const r = Number(data?.rate);
      if (data?.ok && Number.isFinite(r) && r > 0) {
        RATE = r;
        RATE_UPDATED_AT = String(data?.updated_at || "");
        return true;
      }
      throw new Error("invalid fx payload");
    } catch {
      RATE = RATE_FALLBACK;
      RATE_UPDATED_AT = "";
      return false;
    }
  }

  // =======================
  // PRICING
  // =======================
  const itemTotalUsd = (it) => {
    const p = PRODUCT_MAP[it.pid];
    if (!p) return 0;

    let total = (+p.price || 0) * it.q;
    for (const addonKey of it.a) {
      if (ADDONS[addonKey]) total += (+ADDONS[addonKey].price || 0) * it.q;
    }
    return total;
  };

  const cartTotalUsd = () => cart.reduce((sum, it) => sum + itemTotalUsd(it), 0);

  const updateTotalsUI = () => {
    const totalUsd = cartTotalUsd();
    const rate = RATE || RATE_FALLBACK;
    const totalIdr = totalUsd * rate;

    if (elIdr) elIdr.textContent = formatIdr(totalIdr);
    if (elUsd) elUsd.textContent = "";

    const pax = cart.reduce((sum, it) => sum + it.q, 0);
    if (elCount) elCount.textContent = `${pax} PAX`;

    ensureRateElements();
    if (elApprox) elApprox.textContent = formatUsdApprox(totalUsd);
    if (elRate) elRate.textContent = `1 USD = ${formatIdr(rate)} • ${FX_SOURCE_LABEL}`;
  };

  // =======================
  // RENDER CART
  // =======================
  function renderCart() {
    if (!elItems) return;

    if (!cart.length) {
      elItems.innerHTML = `<div class="empty-cart-message">Your dive journey starts here.<br>
Add activities to build your personal dive plan in Bali..</div>`;
      updateTotalsUI();
      return;
    }

    const addonKeys = Object.keys(ADDONS);
    const rate = RATE || RATE_FALLBACK;

    elItems.innerHTML = cart
      .map((it) => {
        const p = PRODUCT_MAP[it.pid];
        const lineUsd = itemTotalUsd(it);
        const lineIdr = lineUsd * rate;

        const addonsHtml = addonKeys.length
          ? `<div class="addons">` +
            addonKeys
              .map((k) => {
                const a = ADDONS[k];
                const checked = it.a.includes(k) ? "checked" : "";
                return `
                  <label class="addon-row">
                    <input type="checkbox" data-addon="${escapeHtml(k)}" data-pid="${it.pid}" ${checked}>
                    <span>${escapeHtml(a.name)} (+${formatIdr((+a.price || 0) * rate)})</span>
                  </label>
                `;
              })
              .join("") +
            `</div>`
          : "";

        return `
          <div class="cart-item">
            <div class="cart-info">
              <div class="cart-name">${escapeHtml(productLabel(p))}</div>

              <div class="cart-price">
                <div class="bd-line-idr">${formatIdr(lineIdr)}</div>
                <div class="bd-line-sub">${it.q} PAX</div>
              </div>

              <input class="cart-date" type="date" value="${escapeHtml(it.d)}" data-date-pid="${it.pid}">

              ${addonsHtml}

              <div class="qty-row">
                <button type="button" data-qty="-1" data-pid="${it.pid}">-</button>
                <span>${it.q}</span>
                <button type="button" data-qty="1" data-pid="${it.pid}">+</button>
                <button type="button" class="remove-btn" data-remove="${it.pid}">Remove</button>
              </div>
            </div>
          </div>
        `;
      })
      .join("");

    elItems.querySelectorAll("[data-qty]").forEach((btn) => {
      btn.onclick = () => {
        const pid = +btn.dataset.pid;
        const it = cart.find((x) => x.pid === pid);
        if (!it) return;
        it.q = Math.max(1, it.q + +btn.dataset.qty);
        saveCart();
        renderCart();
      };
    });

    elItems.querySelectorAll("[data-remove]").forEach((btn) => {
      btn.onclick = () => {
        const pid = +btn.dataset.remove;
        cart = cart.filter((x) => x.pid !== pid);
        saveCart();
        renderCart();
      };
    });

    elItems.querySelectorAll("[data-date-pid]").forEach((input) => {
      input.onchange = () => {
        const pid = +input.dataset.datePid;
        const it = cart.find((x) => x.pid === pid);
        if (!it) return;
        it.d = input.value;
        saveCart();
      };
    });

    elItems.querySelectorAll('input[type="checkbox"][data-addon]').forEach((cb) => {
      cb.onchange = () => {
        const pid = +cb.dataset.pid;
        const addonKey = cb.dataset.addon;
        const it = cart.find((x) => x.pid === pid);
        if (!it) return;

        const has = it.a.includes(addonKey);
        if (cb.checked && !has) it.a.push(addonKey);
        if (!cb.checked && has) it.a = it.a.filter((z) => z !== addonKey);

        saveCart();
        renderCart();
      };
    });

    updateTotalsUI();
  }

  // =======================
  // GLOBAL API
  // =======================
  window.addToCart = (pid) => {
    pid = +pid;
    const p = PRODUCT_MAP[pid];
    if (!p) return toast("Product not found");
    if (p.is_enquiry) return toast("Enquiry-only");

    const it = cart.find((x) => x.pid === pid);
    if (it) it.q++;
    else cart.push({ pid, q: 1, d: today(), a: [] });

    saveCart();
    renderCart();
    toast("Added");
  };

  window.checkout = () => {
    if (!cart.length) return toast("Cart is empty");

    const name = (elName?.value || "").trim();
    const email = (elEmail?.value || "").trim();
    const phoneRaw = (elPhone?.value || "").trim();
    const cert_level = (elCert?.value || "Beginner / No Certificate").trim();
    const bookingId = (elBid?.textContent || "").trim();

    if (!name) return toast("Full name required");
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return toast("Valid email required");
    if (!phoneRaw) return toast("WhatsApp / Phone required");
    if (!isPhoneOk(phoneRaw)) return toast("Phone format looks invalid");
    for (const it of cart) if (!it.d) return toast("Select date for all items");

    const totalUsd = +cartTotalUsd().toFixed(2);
    const rate = RATE || RATE_FALLBACK;
    const totalIdr = Math.round(totalUsd * rate);

    const payload = {
      booking_id: bookingId,
      customer: {
        name,
        email,
        phone: normalizePhone(phoneRaw),
        cert_level: cert_level || "Beginner / No Certificate",
      },
      items: cart,
      total_usd: totalUsd,
      total_idr: totalIdr,
      fx: { base: FX_BASE, quote: FX_QUOTE, rate, source: FX_SOURCE_LABEL, updated_at: RATE_UPDATED_AT },
    };

    saveCustomer(payload.customer);
    location.href = "/cart/checkout-act.php?data=" + encodeURIComponent(b64url(JSON.stringify(payload)));
  };

  (async function init() {
    showRateLoading();
    await loadRateFromDB();
    renderCart();
  })();
})();
