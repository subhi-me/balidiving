<?php
// snorkeling.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

include('01-start.php'); // pastikan path ini benar
?>
<style>
  body {
    box-sizing: border-box;
  }
  
  .hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
  }
  
  .hero-background img {
    width: 110%;
    height: 110%;
    object-fit: cover;
    animation: panZoom 30s ease-in-out infinite;
  }
  
  @keyframes panZoom {
    0% {
      transform: scale(1) translate(0, 0);
    }
    25% {
      transform: scale(1.1) translate(-2%, -2%);
    }
    50% {
      transform: scale(1.05) translate(2%, 1%);
    }
    75% {
      transform: scale(1.08) translate(-1%, 2%);
    }
    100% {
      transform: scale(1) translate(0, 0);
    }
  }
  
  .hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.85) 0%, rgba(2, 132, 199, 0.75) 100%);
    z-index: 1;
  }
  
  .hero-content {
    position: relative;
    z-index: 2;
  }

  @view-transition {
    navigation: auto;
  }
</style>

<main class="pt-24 pb-12">
  <section class="max-w-5xl mx-auto px-4">
    
    <!-- HERO -->
    <div class="relative rounded-2xl overflow-hidden mb-12">
      <div class="hero-background">
        <img src="https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg"
             alt="Snorkeling in Bali"
             onerror="this.src=''; this.style.display='none';">
      </div>
      <div class="hero-overlay"></div>
      <div class="hero-content text-center py-16 px-4">
        <h1 id="main-title" class="font-bold mb-3 text-3xl md:text-4xl text-white">
          Snorkeling in Bali
        </h1>
        <p id="subtitle" class="opacity-90 mb-4 text-white/90">
          Discover the stunning underwater beauty of Bali with us
        </p>
        <p id="description" class="opacity-80 max-w-3xl mx-auto text-sm md:text-base text-white/90">
          Go on one of our amazing snorkeling trips. You can snorkel on coral bommies and white sand bottoms,
          the shipwreck of a World War II transport ship, or the magical Manta Rays at Nusa Penida.
          Snorkeling is something that anyone can do, even if they can't swim.
        </p>
      </div>
    </div>

    <!-- FORM WRAPPER -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- LOCATIONS -->
      <div class="lg:col-span-2">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-2">
          
          <!-- Padang Bai -->
          <label class="location-card cursor-pointer rounded-lg overflow-hidden border-2 border-transparent bg-white/5 backdrop-blur-md transition-all hover:shadow-xl hover:border-sky-400">
            <div class="relative">
              <img src="https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg"
                   alt="Padang Bai"
                   class="location-img w-full h-48 object-cover"
                   onerror="this.src=''; this.style.display='none';">
              <div class="selected-badge hidden absolute top-3 right-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                Selected
              </div>
              <input
                type="radio"
                name="location_key"
                value="padang_bai"
                data-name="Padang Bai"
                data-usd="30"
                class="location-radio hidden"
                required
              >
            </div>
            <div class="p-4 text-slate-100">
              <div class="location-title font-bold mb-1">Padang Bai</div>
              <div class="location-desc mb-2 text-sm text-slate-300">
                Crystal clear waters with vibrant coral gardens and colorful tropical fish.
              </div>
              <div class="location-price font-bold text-base text-amber-300">
                IDR 450,000
              </div>
            </div>
          </label>

          <!-- Tulamben -->
          <label class="location-card cursor-pointer rounded-lg overflow-hidden border-2 border-transparent bg-white/5 backdrop-blur-md transition-all hover:shadow-xl hover:border-sky-400">
            <div class="relative">
              <img src="https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg"
                   alt="Tulamben"
                   class="location-img w-full h-48 object-cover"
                   onerror="this.src=''; this.style.display='none';">
              <div class="selected-badge hidden absolute top-3 right-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                Selected
              </div>
              <input
                type="radio"
                name="location_key"
                value="tulamben"
                data-name="Tulamben"
                data-usd="35"
                class="location-radio hidden"
                required
              >
            </div>
            <div class="p-4 text-slate-100">
              <div class="location-title font-bold mb-1">Tulamben</div>
              <div class="location-desc mb-2 text-sm text-slate-300">
                Famous USAT Liberty shipwreck offering incredible underwater exploration and marine life.
              </div>
              <div class="location-price font-bold text-base text-amber-300">
                IDR 550,000
              </div>
            </div>
          </label>

          <!-- Amed -->
          <label class="location-card cursor-pointer rounded-lg overflow-hidden border-2 border-transparent bg-white/5 backdrop-blur-md transition-all hover:shadow-xl hover:border-sky-400">
            <div class="relative">
              <img src="https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg"
                   alt="Amed"
                   class="location-img w-full h-48 object-cover"
                   onerror="this.src=''; this.style.display='none';">
              <div class="selected-badge hidden absolute top-3 right-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                Selected
              </div>
              <input
                type="radio"
                name="location_key"
                value="amed"
                data-name="Amed"
                data-usd="32"
                class="location-radio hidden"
                required
              >
            </div>
            <div class="p-4 text-slate-100">
              <div class="location-title font-bold mb-1">Amed</div>
              <div class="location-desc mb-2 text-sm text-slate-300">
                Peaceful black sand beaches with pristine reefs and amazing sea creatures.
              </div>
              <div class="location-price font-bold text-base text-amber-300">
                IDR 500,000
              </div>
            </div>
          </label>

          <!-- Nusa Penida & Manta -->
          <label class="location-card cursor-pointer rounded-lg overflow-hidden border-2 border-transparent bg-white/5 backdrop-blur-md transition-all hover:shadow-xl hover:border-sky-400">
            <div class="relative">
              <img src="https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg"
                   alt="Nusa Penida & Manta Point"
                   class="location-img w-full h-48 object-cover"
                   onerror="this.src=''; this.style.display='none';">
              <div class="selected-badge hidden absolute top-3 right-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                Selected
              </div>
              <input
                type="radio"
                name="location_key"
                value="nusa_penida_manta"
                data-name="Nusa Penida & Manta Point"
                data-usd="45"
                class="location-radio hidden"
                required
              >
            </div>
            <div class="p-4 text-slate-100">
              <div class="location-title font-bold mb-1">Nusa Penida & Manta Point</div>
              <div class="location-desc mb-2 text-sm text-slate-300">
                Swim with majestic manta rays in turquoise waters, an unforgettable experience.
              </div>
              <div class="location-price font-bold text-base text-amber-300">
                IDR 650,000
              </div>
            </div>
          </label>
        </div>
        <p class="text-xs text-slate-400 mt-1">
          Prices shown are per person and may vary depending on final package and add-ons.
        </p>
      </div>

      <!-- BOOKING FORM -->
      <div>
        <div id="form-card" class="rounded-xl shadow-lg p-6 bg-slate-900/80 border border-slate-700 text-slate-100">
          <h2 id="form-title" class="font-bold mb-4 text-lg">
            Start Your Booking
          </h2>

          <div id="limit-message" class="hidden mb-4 p-3 rounded-lg border border-red-500 text-xs text-red-100 bg-red-900/40">
            Sorry, the maximum limit of bookings has been reached. Please contact us directly.
          </div>

          <form id="booking-form" method="post" action="booking-confirm.php" class="space-y-4">
            <!-- hidden fields dikirim ke booking-confirm.php -->
            <input type="hidden" name="activity" id="activity" value="snorkeling">
            <input type="hidden" name="location_key" id="location_key">
            <input type="hidden" name="location_name" id="location_name">
            <input type="hidden" name="base_usd" id="base_usd">

            <div>
              <label for="contact" class="form-label block mb-2 font-medium text-sm">
                Email or WhatsApp
              </label>
              <input
                type="text"
                id="contact"
                name="contact"
                class="form-input w-full px-4 py-3 rounded-lg border border-slate-600 bg-slate-950/70 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                placeholder="email@example.com or +62812345678"
                required
              >
            </div>

            <div>
              <label for="date" class="form-label block mb-2 font-medium text-sm">
                Preferred Date
              </label>
              <input
                type="date"
                id="date"
                name="selected_date"
                class="form-input w-full px-4 py-3 rounded-lg border border-slate-600 bg-slate-950/70 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                required
              >
            </div>

            <button
              type="submit"
              id="submit-btn"
              class="w-full py-3 rounded-lg font-semibold text-sm text-slate-950 bg-amber-400 hover:bg-amber-300 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Continue
            </button>
          </form>
        </div>
      </div>

    </div>
  </section>
</main>

<script>
  let isSubmitting = false;
  let currentRecordCount = 0; // kalau nanti mau pakai limit dari DB, tinggal diisi via AJAX

  function setDateConstraints() {
    const dateInput = document.getElementById('date');
    if (!dateInput) return;

    const now = new Date();
    const witaOffset = 8 * 60; // UTC+8
    const localOffset = now.getTimezoneOffset();
    const witaTime = new Date(now.getTime() + (witaOffset + localOffset) * 60000);

    const witaHour = witaTime.getHours();
    let minDate;
    if (witaHour < 13) {
      minDate = new Date(witaTime.getFullYear(), witaTime.getMonth(), witaTime.getDate());
    } else {
      minDate = new Date(witaTime.getFullYear(), witaTime.getMonth(), witaTime.getDate() + 1);
    }

    const minDateString = minDate.toISOString().split('T')[0];
    dateInput.setAttribute('min', minDateString);
  }

  function setupLocationSelection() {
    const radios = document.querySelectorAll('.location-radio');
    const badges = document.querySelectorAll('.selected-badge');

    radios.forEach(radio => {
      radio.addEventListener('change', function () {
        badges.forEach(b => b.classList.add('hidden'));
        if (this.checked) {
          const badge = this.parentElement.querySelector('.selected-badge');
          if (badge) badge.classList.remove('hidden');
        }
      });
    });
  }

  function setupForm() {
    const form = document.getElementById('booking-form');
    const submitBtn = document.getElementById('submit-btn');
    const limitMsg = document.getElementById('limit-message');

    form.addEventListener('submit', function (e) {
      if (isSubmitting) {
        e.preventDefault();
        return;
      }

      if (currentRecordCount >= 999) {
        e.preventDefault();
        if (limitMsg) limitMsg.classList.remove('hidden');
        return;
      }

      const contactInput = document.getElementById('contact');
      const dateInput    = document.getElementById('date');
      const checked      = document.querySelector('input.location-radio:checked');

      if (!checked) {
        e.preventDefault();
        alert('Please choose a snorkeling location first.');
        return;
      }

      if (!contactInput.value || !dateInput.value) {
        e.preventDefault();
        alert('Please fill in contact and date.');
        return;
      }

      // Set hidden fields
      const locationKey   = checked.value;
      const locationName  = checked.dataset.name || '';
      const baseUsd       = checked.dataset.usd  || '0';

      document.getElementById('location_key').value   = locationKey;
      document.getElementById('location_name').value  = locationName;
      document.getElementById('base_usd').value       = baseUsd;

      // jalankan submit normal (POST ke booking-confirm.php)
      isSubmitting = true;
      submitBtn.disabled = true;
      submitBtn.textContent = 'Loading...';
      // tidak preventDefault → biarkan form submit ke PHP
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    setDateConstraints();
    setupLocationSelection();
    setupForm();
  });
</script>

<?php include('03-end.php'); ?>
