<?php
// learndiving.php
// expects: $learnDivingProducts (array), $productImages (id => url)
// expects: $USD_TO_IDR (float) from main-cart.php

if (empty($learnDivingProducts)) {
    return;
}

if (!function_exists('fmt_idr')) {
    function fmt_idr(int $n): string {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }
}
if (!function_exists('fmt_usd')) {
    function fmt_usd(float $n): string {
        $s = number_format($n, 2, '.', '');
        $s = rtrim(rtrim($s, '0'), '.');
        return 'US$' . $s;
    }
}
?>
<section class="category-section">
  <h3 class="category-title">Learn Diving (PADI)</h3>
  <p class="category-intro">
    Through our PADI courses you will progress from pool training to Bali’s top open water dive sites.
    You start as a beginner and finish as a certified diver, ready to explore the underwater world with confidence.
    Every program follows official PADI standards, from Discover Scuba Diving up to Divemaster level.
  </p>

  <div class="category-products">
    <?php foreach ($learnDivingProducts as $p):
      $pid = (int)$p['id'];
      $img = $productImages[$pid] ?? 'https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg';

      $priceUsd = (float)$p['price_usd'];
      $rate = (isset($USD_TO_IDR) && is_numeric($USD_TO_IDR) && (float)$USD_TO_IDR > 0) ? (float)$USD_TO_IDR : 0;
      $priceIdr = $rate > 0 ? (int)round($priceUsd * $rate) : 0;

      $isEnquiry = (int)$p['is_enquiry'] === 1;
      $name = (string)$p['name'];
      $desc = (string)$p['description'];
    ?>
      <div class="product-card">
        <div class="product-icon">
          <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($name) ?>">
        </div>

        <div class="product-name"><?= htmlspecialchars($name) ?></div>

        <?php if ($isEnquiry): ?>
          <div class="product-price">Contact for pricing</div>
        <?php else: ?>
          <!-- PRICE: IDR utama + USD estimation kecil (forced) -->
          <div class="product-price"
               data-usd="<?= htmlspecialchars((string)$priceUsd) ?>"
               data-idr="<?= htmlspecialchars((string)$priceIdr) ?>">

            <div class="text-lg font-semibold text-blue-600 leading-tight">
              <?= $priceIdr > 0 ? fmt_idr($priceIdr) : 'Rp -' ?>
            </div>

            <div class="text-blue-500 leading-tight"
                 style="font-size:11px !important; opacity:.8; margin-top:2px !important;">
              Estimation <?= fmt_usd($priceUsd) ?>
            </div>

          </div>
        <?php endif; ?>

        <div class="product-description">
          <?= htmlspecialchars($desc) ?>
        </div>

        <a href="#"
           class="read-article-link"
           onclick="event.preventDefault(); openPadiOffcanvas(<?= $pid ?>, '<?= htmlspecialchars(addslashes($name)) ?>');">
          📖 Read Article
        </a>

        <?php if ($isEnquiry): ?>
          <a href="https://balidiving.com/contact?page=contact" target="_blank" rel="noopener noreferrer" class="enquire-btn">
            Enquire
          </a>
        <?php else: ?>
          <!-- Learn Diving: addToCart diarahkan ke offcanvas varian PADI -->
          <button class="add-to-cart-btn"
                  onclick="openPadiOffcanvas(<?= $pid ?>, '<?= htmlspecialchars(addslashes($name)) ?>');">
            Add to Cart
          </button>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ==========================
     PADI OFFCANVAS (VARIANTS)
     ========================== -->
<div id="padiOverlay"
     onclick="closePadiOffcanvas()"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9998;"></div>

<aside id="padiOffcanvas"
       aria-hidden="true"
       style="position:fixed; top:0; right:0; height:100%; width:min(420px, 92vw);
              background:#fff; z-index:9999; transform:translateX(110%);
              transition:transform .25s ease; box-shadow:-10px 0 30px rgba(0,0,0,.15);">

  <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 14px; border-bottom:1px solid rgba(0,0,0,.08);">
    <div>
      <div style="font-weight:700; font-size:16px; line-height:1.2;">Select PADI Options</div>
      <div id="padiTitle" style="font-size:12px; opacity:.7; margin-top:2px;"></div>
    </div>
    <button type="button" onclick="closePadiOffcanvas()"
            style="border:0; background:transparent; font-size:18px; cursor:pointer; padding:6px;">
      ✕
    </button>
  </div>

  <div style="padding:14px;">
    <div id="padiBody" style="font-size:14px; line-height:1.45;">
      <!-- dynamic content -->
      <div style="opacity:.75;">Loading options...</div>
    </div>
  </div>

  <div style="position:absolute; left:0; right:0; bottom:0; padding:14px; border-top:1px solid rgba(0,0,0,.08); background:#fff;">
    <button id="padiConfirmBtn" type="button"
            style="width:100%; border:0; border-radius:10px; padding:12px 14px;
                   background:#0b57d0; color:#fff; font-weight:700; cursor:pointer;">
      Add Selected to Cart
    </button>
    <div style="font-size:11px; opacity:.7; margin-top:8px;">
      Tip: choose course package / participants / schedule, then confirm.
    </div>
  </div>
</aside>

<script>
(function(){
  const overlay = document.getElementById('padiOverlay');
  const panel   = document.getElementById('padiOffcanvas');
  const titleEl = document.getElementById('padiTitle');
  const bodyEl  = document.getElementById('padiBody');
  const btn     = document.getElementById('padiConfirmBtn');

  let currentPid = null;

  window.openPadiOffcanvas = function(pid, name){
    currentPid = pid;
    titleEl.textContent = name || '';
    overlay.style.display = 'block';
    panel.style.transform = 'translateX(0)';
    panel.setAttribute('aria-hidden', 'false');

    // Load variants/options (server endpoint). If you already have another endpoint, ganti URL ini.
    // Expected response JSON example:
    // { "options":[ { "id":"opt1","label":"Open Water Course (3 Days)","price_usd":350 }, ... ] }
    const url = '/cart/api/padi-variants.php?product_id=' + encodeURIComponent(pid);

    bodyEl.innerHTML = '<div style="opacity:.75;">Loading options...</div>';
    btn.onclick = function(){};

    fetch(url, { credentials: 'same-origin' })
      .then(r => r.ok ? r.json() : Promise.reject(r))
      .then(data => {
        const opts = (data && Array.isArray(data.options)) ? data.options : [];
        if (!opts.length) {
          bodyEl.innerHTML = `
            <div style="padding:10px 12px; border:1px solid rgba(0,0,0,.08); border-radius:10px;">
              <div style="font-weight:700; margin-bottom:6px;">No variants found</div>
              <div style="font-size:13px; opacity:.8;">Please contact us for the best package recommendation.</div>
              <a href="https://balidiving.com/contact?page=contact" target="_blank" rel="noopener noreferrer"
                 style="display:inline-block; margin-top:10px; text-decoration:none; font-weight:700; color:#0b57d0;">
                Contact Us →
              </a>
            </div>`;
          btn.onclick = function(){ closePadiOffcanvas(); };
          btn.textContent = 'Close';
          return;
        }

        // Render radio list
        btn.textContent = 'Add Selected to Cart';

        let html = '<div style="display:flex; flex-direction:column; gap:10px;">';
        opts.forEach((o, idx) => {
          const oid = String(o.id ?? idx);
          const label = String(o.label ?? 'Option');
          const usd = Number(o.price_usd ?? 0);
          html += `
            <label style="display:flex; gap:10px; align-items:flex-start; padding:10px 12px;
                          border:1px solid rgba(0,0,0,.08); border-radius:12px; cursor:pointer;">
              <input type="radio" name="padiOption" value="${oid}" ${idx===0?'checked':''} style="margin-top:3px;">
              <div style="flex:1;">
                <div style="font-weight:700;">${escapeHtml(label)}</div>
                ${usd ? `<div style="font-size:12px; opacity:.75; margin-top:2px;">Estimation US$${trimUsd(usd)}</div>` : ``}
              </div>
            </label>`;
        });
        html += '</div>';
        bodyEl.innerHTML = html;

        btn.onclick = function(){
          const selected = panel.querySelector('input[name="padiOption"]:checked');
          const optionId = selected ? selected.value : null;

          // If your existing cart.js has a method for variants, call it here.
          // Fallback: use addToCart(pid, optionId) if exists, else just addToCart(pid)
          try {
            if (typeof window.addToCart === 'function') {
              // Support variant param if your addToCart accepts it
              if (optionId !== null) {
                window.addToCart(currentPid, optionId);
              } else {
                window.addToCart(currentPid);
              }
            }
          } catch(e) {}
          closePadiOffcanvas();
        };
      })
      .catch(() => {
        bodyEl.innerHTML = `
          <div style="padding:10px 12px; border:1px solid rgba(0,0,0,.08); border-radius:10px;">
            <div style="font-weight:700; margin-bottom:6px;">Couldn’t load options</div>
            <div style="font-size:13px; opacity:.8;">Please refresh or contact us for assistance.</div>
            <a href="https://balidiving.com/contact?page=contact" target="_blank" rel="noopener noreferrer"
               style="display:inline-block; margin-top:10px; text-decoration:none; font-weight:700; color:#0b57d0;">
              Contact Us →
            </a>
          </div>`;
        btn.textContent = 'Close';
        btn.onclick = function(){ closePadiOffcanvas(); };
      });
  };

  window.closePadiOffcanvas = function(){
    overlay.style.display = 'none';
    panel.style.transform = 'translateX(110%)';
    panel.setAttribute('aria-hidden', 'true');
  };

  function trimUsd(n){
    const s = n.toFixed(2).replace(/\.?0+$/,'');
    return s;
  }
  function escapeHtml(str){
    return String(str)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }
})();
</script>
