<?php if (!empty($safariProducts)): ?>
  <div class="category-section">
    <h3 class="category-title">Diving Safari (Multi-day)</h3>
    <p class="category-intro">
      Multi-day Bali & beyond dive trips for divers who want more than a day tour.
      Stay close to the best dive sites, enjoy flexible schedules, and let us handle
      logistics, boats, tanks, and local arrangements.
    </p>

    <div class="category-products">
      <?php foreach ($safariProducts as $p): 
        $pid       = (int)$p['id'];
        $img       = $productImages[$pid] ?? 'https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg';
        $isEnquiry = (bool)$p['is_enquiry'];
      ?>
        <div class="product-card">
          <div class="product-icon">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          </div>

          <div class="product-name">
            <?= htmlspecialchars($p['name']) ?>
          </div>

          <?php if ($isEnquiry): ?>
            <div class="product-price">Contact for pricing</div>
          <?php else: ?>
            <div class="product-price">
              $<?= number_format((float)$p['price_usd'], 2) ?>
            </div>
          <?php endif; ?>

          <div class="product-description">
            <?= htmlspecialchars($p['description']) ?>
          </div>

          <a href="#" class="read-article-link" onclick="event.preventDefault()">
            📖 Read Article
          </a>

          <?php if ($isEnquiry): ?>
            <a 
              href="https://balidiving.com/contact?page=contact" 
              target="_blank" 
              rel="noopener noreferrer" 
              class="enquire-btn"
            >
              Enquire
            </a>
          <?php else: ?>
            <button 
              class="add-to-cart-btn" 
              type="button"
              onclick="addToCart(<?= $pid ?>)"
            >
              Add to Booking Plan
            </button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>
