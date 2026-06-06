<?php
require_once 'template/seo_manager.php';
$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="https://balidiving.com/bali-diving-64.png" />
  <?php echo generate_seo_tags($page); ?>

  <!-- Perf: preconnect -->
  <link rel="preconnect" href="https://connect.facebook.net" crossorigin>
  <link rel="preconnect" href="https://www.facebook.com" crossorigin>
  <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>

  <?php include('template/style.php'); ?>
</head>

<body class="font-sans">
  <?php
    include('template/nav.php');
    include('template/header.php');
  ?>
  
  <main>
    <?php
      include('template/grid.php');
      include('template/ebook.php');
      include('template/review.php');
      include('template/myplan.php');
    ?>
  </main>

  <?php
    include('template/consent.php');
    include('template/footer.php');
  ?>

  <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 hidden z-[60]">
    <div id="lightbox-container" class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-full overflow-y-auto relative">
      <button id="lightbox-close" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800" aria-label="Close">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      <div>
        <img id="lightbox-image" src="" alt="Dive Site Image" class="w-full h-64 object-cover rounded-t-lg">
        <div class="p-6">
          <h3 id="lightbox-title" class="text-3xl font-bold text-navy mb-3"></h3>
          <p id="lightbox-description" class="text-gray-700"></p>
        </div>
      </div>
    </div>
  </div>



  <script>
    // ========= Perf helpers (no feature change) =========
    const __idle = (fn, timeout = 1500) => {
      if ('requestIdleCallback' in window) return requestIdleCallback(fn, { timeout });
      return setTimeout(fn, 800);
    };
    const __afterPaint = (fn) => requestAnimationFrame(() => requestAnimationFrame(fn));

    function scrollToSection(sectionId) {
      const el = document.getElementById(sectionId);
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    }

    // ========= Lightweight: scroll nav (RAF + passive) =========
    (function(){
      const navElement = document.querySelector('nav');
      const navContent = document.getElementById('nav-content');
      if (!navElement || !navContent) return;

      let ticking = false;
      const apply = () => {
        const scrolled = window.scrollY > 100;
        navElement.classList.toggle('bg-navy/70', scrolled);
        navElement.classList.toggle('shadow-2xl', scrolled);
        navContent.classList.toggle('h-12', scrolled);
        navContent.classList.toggle('h-16', !scrolled);
      };

      const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
          apply();
          ticking = false;
        });
      };

      window.addEventListener('scroll', onScroll, { passive: true });
      // apply initial state after first paint
      __afterPaint(apply);
    })();

    // ========= Critical small DOM tasks =========
    document.addEventListener('DOMContentLoaded', () => {
      const yearEl = document.getElementById('current-year');
      if (yearEl) yearEl.textContent = new Date().getFullYear();

      const mobileBtn = document.getElementById('mobile-menu-btn');
      const mobileMenu = document.getElementById('mobile-menu');
      if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
        mobileMenu.addEventListener('click', (e) => {
          const a = e.target.closest('a');
          if (!a) return;
          mobileMenu.classList.add('hidden');
        });
      }
    });

    // ========= Heavy tasks moved to idle (reduces TBT) =========
    __idle(() => {
      // Lightbox data (same content)
      const siteData = {
        'manta-point': { title: 'Manta Point', imageUrl: 'template/images/manta-point.jpg', description: 'Experience the breathtaking dance of majestic Manta Rays at their famous cleaning station. A truly unforgettable encounter for divers of all levels.' },
        'crystal-bay': { title: 'Crystal Bay', imageUrl: 'template/images/crystal.jpg', description: 'Famous for its crystal-clear visibility and vibrant coral reefs. Between July and October, it becomes the best place to spot the elusive and massive Mola Mola (Oceanic Sunfish).' },
        'usat-liberty': { title: 'USAT Liberty Wreck', imageUrl: 'template/images/usat.jpg', description: 'Explore one of the most famous shipwrecks in the world. This WWII cargo ship is now a spectacular artificial reef, fully encrusted with coral and home to thousands of fish.' },
        'coral-garden': { title: 'Coral Garden', imageUrl: 'template/images/coral.jpg', description: 'A stunning underwater garden located near the Liberty Wreck. This shallow reef is teeming with a huge variety of corals, anemones, and colorful reef fish.' }
      };

      const lightbox = document.getElementById('lightbox');
      const lightboxClose = document.getElementById('lightbox-close');
      const titleEl = document.getElementById('lightbox-title');
      const imgEl = document.getElementById('lightbox-image');
      const descEl = document.getElementById('lightbox-description');

      const openLightbox = (siteKey) => {
        const data = siteData[siteKey];
        if (!data || !lightbox) return;
        if (titleEl) titleEl.textContent = data.title;
        if (imgEl) imgEl.src = data.imageUrl;
        if (descEl) descEl.textContent = data.description;
        lightbox.classList.remove('hidden');
      };

      const closeLightbox = () => lightbox && lightbox.classList.add('hidden');

      // Delegate click: 1 listener only (lighter than many)
      document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-site]');
        if (!trigger) return;
        e.preventDefault();
        openLightbox(trigger.dataset.site);
      }, { passive: false });

      if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
      if (lightbox) {
        lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
      }
    });

  </script>

  <script>
    // ========= Slider images list from PHP =========
    <?php
      $files = glob('template/images/slider/*.webp');
      $imagePaths = $files ? array_map('basename', $files) : [];
      echo "const imageNames = " . json_encode($imagePaths) . ";";
    ?>

    // ========= Slider init delayed (reduces main-thread CPU at start) =========
    __afterPaint(() => {
      // delay a bit more to protect LCP
      setTimeout(() => {
        __idle(() => {
          const sliderContainer = document.getElementById('hero-slider');
          if (!sliderContainer || !Array.isArray(imageNames) || imageNames.length === 0) return;

          // Fisher-Yates shuffle
          const shuffledImages = [...imageNames];
          for (let i = shuffledImages.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffledImages[i], shuffledImages[j]] = [shuffledImages[j], shuffledImages[i]];
          }

          const addImg = (imageName) => {
            const img = document.createElement('img');

            // PERF FIX: path konsisten dengan glob
            img.src = `template/images/slider/${imageName}`;

            img.alt = 'Underwater background image';
            img.loading = 'lazy';
            img.decoding = 'async';
            img.classList.add('hero-background-image', 'absolute', 'inset-0');
            sliderContainer.appendChild(img);
          };

          // First 2 now, rest on idle to keep CPU low
          shuffledImages.forEach((name, idx) => {
            if (idx < 2) addImg(name);
            else __idle(() => addImg(name), 3000);
          });

          const images = sliderContainer.getElementsByClassName('hero-background-image');
          let currentIndex = 0;

          const startSlider = () => {
            if (!images || images.length === 0) return;
            images[0].classList.add('active');

            setInterval(() => {
              if (!images[currentIndex]) return;
              images[currentIndex].classList.remove('active');
              currentIndex = (currentIndex + 1) % images.length;
              if (images[currentIndex]) images[currentIndex].classList.add('active');
            }, 5000);
          };

          startSlider();
        }, 2500);
      }, 900);
    });
  </script>

  <?php include('template/chat.php'); ?>

  <!-- Perf: load tracking last (render first) -->
  <?php include('template/pixel.php'); ?>
  <?php include('template/gtag.php'); ?>

</body>
</html>
