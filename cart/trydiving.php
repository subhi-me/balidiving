<?php
// trydiving.php
// expects: $tryDivingProducts (array), $productImages (id => url)
// expects: $USD_TO_IDR (float) from main-cart.php

if (empty($tryDivingProducts)) {
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
  <h3 class="category-title">Try Diving</h3>
  <p class="category-intro">
    If you do not have a diving certification or have never dived before, we can take you diving at these sites
    on a PADI “Try Scuba Diving” program. It is still two dives, the same as for certified divers, but you will be
    supervised directly by a qualified instructor at a maximum ratio of two divers per guide and limited to a
    maximum depth of 12 metres.
  </p>

  <div class="category-products">
    <?php foreach ($tryDivingProducts as $p):
      $pid = (int)$p['id'];
      $img = $productImages[$pid] ?? 'https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg';

      $priceUsd = (float)$p['price_usd'];
      $rate = (isset($USD_TO_IDR) && is_numeric($USD_TO_IDR) && (float)$USD_TO_IDR > 0) ? (float)$USD_TO_IDR : 0;
      $priceIdr = $rate > 0 ? (int)round($priceUsd * $rate) : 0;

      $isEnquiry = (int)$p['is_enquiry'] === 1;
    ?>
      <div class="product-card">
        <div class="product-icon">
          <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        </div>

        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>

        <?php if ($isEnquiry): ?>
          <div class="product-price">Contact for pricing</div>
        <?php else: ?>
          <!-- PRICE: IDR utama + USD estimation kecil -->
          <div class="product-price"
               data-usd="<?= htmlspecialchars((string)$priceUsd) ?>"
               data-idr="<?= htmlspecialchars((string)$priceIdr) ?>">

            <!-- IDR PRIMARY -->
            <div class="text-lg font-semibold text-blue-600 leading-tight">
              <?= $priceIdr > 0 ? fmt_idr($priceIdr) : 'Rp -' ?>
            </div>

            <!-- USD ESTIMATION (FORCED SMALL) -->
            <div class="text-blue-500 leading-tight"
                 style="font-size:11px !important; opacity:.8; margin-top:2px !important;">
              Estimation <?= fmt_usd($priceUsd) ?>
            </div>

          </div>
        <?php endif; ?>

        <div class="product-description">
          <?= htmlspecialchars($p['description']) ?>
        </div>

        <a href="#" class="read-article-link" onclick="event.preventDefault()">📖 Read Article</a>

        <?php if ($isEnquiry): ?>
          <a href="https://balidiving.com/contact?page=contact"
             target="_blank" rel="noopener noreferrer"
             class="enquire-btn">
            Enquire
          </a>
        <?php else: ?>
          <button class="add-to-cart-btn" onclick="addToCart(<?= $pid ?>)">
            Add to Cart
          </button>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
