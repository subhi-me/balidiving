<?php
// snorkeling.php
// expects: $snorkelingProducts (array), $productImages (id => url)
if (empty($snorkelingProducts)) {
    return;
}
?>
<section class="category-section">
  <h3 class="category-title">Snorkeling</h3>
  <p class="category-intro">
    Go on one of our unforgettable snorkeling trips and explore Bali’s signature underwater highlights:
    colourful coral bommies, clear white-sand bottoms, and even the famous World War II shipwreck.
    You can also meet the magical manta rays around Nusa Penida.
    Snorkeling is suitable for almost everyone, including beginners and those who are not confident swimmers,
    as you will be guided and supported the whole time.
  </p>
  <div class="category-products">
    <?php foreach ($snorkelingProducts as $p): 
      $pid = (int)$p['id'];
      $img = $productImages[$pid] ?? 'https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg';
      $price = (float)$p['price_usd'];
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
          <div class="product-price">$<?= number_format($price, 2) ?></div>
        <?php endif; ?>
        <div class="product-description">
          <?= htmlspecialchars($p['description']) ?>
        </div>
        <a href="#" class="read-article-link" onclick="event.preventDefault()">📖 Read Article</a>
        <?php if ($isEnquiry): ?>
          <a href="https://balidiving.com/contact?page=contact" target="_blank" rel="noopener noreferrer" class="enquire-btn">
            Enquire
          </a>
        <?php else: ?>
          <button class="add-to-cart-btn" onclick="addToCart(<?= $pid ?>)">Add to Cart</button>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
