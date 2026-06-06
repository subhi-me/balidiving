<section class="relative h-screen w-full flex items-center justify-center text-white overflow-hidden" id="hero">
  <style>
    /* ================= Base Styles (as provided) ================= */
    .search-container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      padding: 10px 30px; /* atas-bawah | kiri-kanan */
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      max-width: 700px;
      width: 100%;
      margin: auto;
      position: relative; /* needed for loading bar */
    }

    .search-title {
      color: #ffffff;
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .search-form {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
    }

    .search-input-wrapper { position: relative; flex: 1; }

    .search-input {
      width: 100%;
      padding: 15px 20px 15px 50px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50px;
      font-size: 16px;
      outline: none;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.1);
      color: #ffffff;
    }

    .search-input::placeholder { color: rgba(255, 255, 255, 0.7); }

    /* default focus (will be overridden by loading theme below for consistency) */
    .search-input:focus {
      border-color: #3498db;
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
      background: rgba(255, 255, 255, 0.15);
    }

    .search-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.7);
      font-size: 18px;
    }

    .search-button {
      padding: 15px 25px;
      background: linear-gradient(135deg, #3498db, #2980b9);
      color: white;
      border: none;
      border-radius: 50px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      min-width: 120px;
      justify-content: center;
    }

    .search-button:hover {
      background: linear-gradient(135deg, #2980b9, #1f5f8b);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }

    .suggestion-container {
      display: none;
      margin-top: 20px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }

    .suggestion-container.show { display: flex; }

    .suggestion-text {
      color: #ffffff;
      font-size: 16px;
      font-weight: 500;
      margin: 0;
      flex: 1;
      text-align: left;
      line-height: 1.6;
    }

    /* Base look + siap ikon kiri */
    .suggestion-activity-link {
      display: inline-flex;         /* ikon & teks rata tengah */
      align-items: center;
      gap: 10px;
      margin-top: 8px;
      color: #ffffff;
      text-decoration: none;        /* no underline */
      font-weight: 400;
      font-size: 15px;
      padding: 10px 14px;           /* rasa pill button */
      border-radius: 12px;
      transition: background .25s ease, box-shadow .25s ease, transform .06s ease;
    }

    /* Hover & keyboard focus: rasa tombol/selected */
    .suggestion-activity-link:hover,
    .suggestion-activity-link:focus-visible {
      background: linear-gradient(135deg, #0070d3, #005bb0);
      color: #ffffff;
      text-decoration: none;
      box-shadow: 0 6px 16px rgba(0, 112, 211, 0.28);
      transform: translateY(-1px);
      outline: none;
    }

    /* Active/pressed */
    .suggestion-activity-link:active {
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(0, 112, 211, 0.22);
    }

    /* (Opsional) Versi “selected” permanen */
    .suggestion-activity-link.is-selected {
      background: linear-gradient(135deg, #0070d3, #005bb0);
      box-shadow: 0 6px 16px rgba(0, 112, 211, 0.28);
    }

    /* Style ikon kiri */
    .suggestion-activity-icon {
      font-size: 16px;
      opacity: 0.9;
      color: #a2d2fa;               /* subtle ocean accent */
      transition: color .2s ease, opacity .2s ease, transform .06s ease;
    }
    .suggestion-activity-link:hover .suggestion-activity-icon,
    .suggestion-activity-link:focus-visible .suggestion-activity-icon {
      color: #ffffff;
      opacity: 1;
      transform: translateY(-1px);
    }

    .suggestion-more-actions { display: flex; flex-direction: column; align-items: center; gap: 10px; }

    .suggestion-actions-title {
      color: #ffffff;
      font-weight: 600;
      font-size: 15px;
      margin: 0;
      opacity: 0.6;
    }

    .suggestion-actions { display: flex; gap: 10px; }

    .suggestion-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 20px;
      background: linear-gradient(135deg, #27ae60, #229954);
      color: white;
      text-decoration: none;
      border-radius: 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      font-family: inherit;
      cursor: pointer;
    }

    .suggestion-link-2 {
      /* Properti Layout Dasar (TIDAK BERUBAH) */
      display: inline-flex;
      align-items: center;
      cursor: pointer;
      text-decoration: none;
      font-family: inherit;
      border: none;
      transition: all 0.3s ease;
      font-weight: 600;

      /* Perubahan untuk membuat sangat kecil */
      font-size: 0.75rem; /* Ukuran font dikecilkan */
      padding: 4px 8px;   /* Padding sangat kecil */
      gap: 4px;           /* Jarak antar ikon/teks dikecilkan */
      border-radius: 12px;/* Radius sudut disesuaikan agar proporsional */

      /* Properti Estetika (TETAP SAMA) */
      background: linear-gradient(135deg, #27ae60, #229954);
      color: white;
    }

    .suggestion-link:hover {
      background: linear-gradient(135deg, #0070d3, #0070d3);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
    }

    .diving-icon { color: #3498db; font-size: 28px; }

    .chat-button { background: linear-gradient(135deg, #e67e22, #d35400); }
    .chat-button:hover {
      background: linear-gradient(135deg, #d35400, #c0392b);
      box-shadow: 0 5px 15px rgba(230, 126, 34, 0.3);
    }

    .wave-container {
      position: absolute; bottom: 0; left: 0;
      width: 100%; height: 120px; overflow: hidden;
    }
    .waves { position: relative; width: 100%; height: 100%; }
    .parallax > use { animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite; }
    .parallax > use:nth-child(1) { animation-delay: -2s; animation-duration: 7s;  fill: rgba(255, 255, 255, 0.7); }
    .parallax > use:nth-child(2) { animation-delay: -3s; animation-duration: 10s; fill: rgba(255, 255, 255, 0.5); }
    .parallax > use:nth-child(3) { animation-delay: -4s; animation-duration: 13s; fill: rgba(255, 255, 255, 0.3); }
    .parallax > use:nth-child(4) { animation-delay: -5s; animation-duration: 20s; fill: rgb(255, 255, 255); }

    @keyframes move-forever { 0% { transform: translate3d(-90px,0,0); } 100% { transform: translate3d(85px,0,0); } }

    @media (max-width: 768px) {
      .search-form { flex-direction: column; }
      .search-container { padding: 30px 20px; }
      .search-title { font-size: 20px; }
      .suggestion-container { flex-direction: column; align-items: stretch; gap: 15px; }
      .suggestion-text { text-align: center; }
      .suggestion-more-actions { margin-top: 15px; }
      .suggestion-actions { justify-content: center; }
      .wave-container { height: 80px; }
    }

    /* =============== Dynamic Heading Fade =============== */
    #dynamicHeading { transition: opacity 0.8s ease-in-out; }

    /* =============== LOADING THEME OVERRIDES (#0070d3) =============== */
    :root { --loading-color: #0070d3; }

    /* Progress bar tipis di atas card saat loading */
    .search-container::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      height: 3px; width: 0%;
      background: var(--loading-color);
      border-radius: 20px 20px 0 0;
      transition: width .6s ease;
    }
    .search-container.is-loading::before { width: 100%; }

    /* Spinner kecil di tombol */
    .btn-spinner {
      width: 18px; height: 18px;
      border-radius: 50%;
      border: 2px solid rgba(255,255,255,.35);
      border-top-color: #fff;
      animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Lock input & tombol saat loading */
    .is-loading .search-input,
    .is-loading .search-button { pointer-events: none; opacity: .85; }

    /* Fokus & glow biru konsisten */
    .search-input:focus {
      border-color: var(--loading-color) !important;
      box-shadow: 0 0 0 3px rgba(0,112,211,.2) !important;
    }

    /* Tombol hover & loading ke nuansa #0070d3 */
    .search-button:hover {
      background: linear-gradient(135deg, #005bb0, #004a8f) !important;
      box-shadow: 0 5px 15px rgba(0,112,211,0.3) !important;
    }
    .is-loading .search-button {
      background: linear-gradient(135deg, var(--loading-color), #005bb0) !important;
      box-shadow: 0 5px 15px rgba(0,112,211,.32);
    }

    /* Ikon ikut biru saat loading */
    .is-loading .search-icon,
    .is-loading .diving-icon { color: var(--loading-color) !important; }

    /* ====== Hero preloader gradient (smooth) ====== */
    #hero-slider {
      /* fallback: gradient dulu sebelum gambar */
      background: linear-gradient(120deg, #3492D0, #083C7C);
    }

    #hero-slider.gradient-preload {
      background: linear-gradient(120deg, #3492D0, #083C7C);
      background-size: 200% 200%;
      animation: gradient-pan 6s ease-in-out infinite alternate;
      will-change: background-position, opacity;
    }

    /* Saat gambar sudah siap, kita cross-fade dari gradient ke image */
    #hero-slider.images-ready {
      transition: opacity .8s ease, filter .8s ease, background-image .6s ease-in;
      opacity: 1;
    }

    /* Sedikit soft glow saat transisi */
    #hero-slider.transitioning {
      filter: saturate(1.05) contrast(1.02);
    }

    /* Animasi gerak halus antara dua warna */
    @keyframes gradient-pan {
      0%   { background-position: 0% 50%; }
      100% { background-position: 100% 50%; }
    }

    /* Hormati preferensi pengguna */
    @media (prefers-reduced-motion: reduce) {
      #hero-slider.gradient-preload { animation: none; }
    }
  </style>

  <!-- Layer background (gradient -> image) -->
  <div class="absolute inset-0 z-0" id="hero-slider"></div>
  <div class="absolute inset-0 bg-black/35 backdrop-blur-sm z-10"></div>

  <!-- Content -->
  <div class="relative z-20 text-center px-4 fade-in hero-text-container flex flex-col items-center">
    
    <!-- Badges Wrapper -->
    <div class="flex flex-wrap justify-center items-center gap-3 mb-5">
      <!-- Price Badge -->
      <a href="pricelist.php" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-md text-sm font-medium text-white shadow-lg hover:bg-white/20 hover:border-white/30 transition-all duration-300 cursor-pointer group">
        <span class="relative flex h-2.5 w-2.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
        </span>
        Start From <span class="font-bold text-green-300 group-hover:text-green-200">1,100</span> <span class="text-[10px] md:text-xs text-white/70 font-normal uppercase tracking-wide -ml-1">IDR</span>
      </a>
    </div>

<h3 id="dynamicHeading"
    class="text-[22px] sm:text-[26px] md:text-4xl lg:text-5xl 
           font-light tracking-tight leading-snug md:leading-tight 
           mb-3 md:mb-4 text-white transition-all duration-1000 ease-in-out">
</h3>


    <p class="max-w-2xl mx-auto text-lg md:text-xl text-white/90 mb-8 font-light">
      Dive confident, trusted - <span class="font-semibold text-lightblue">PADI 5-Star</span>.
    </p>

    <div class="search-container">
        
    <?php include 'search2.php'; ?>
    </div>
  </div>

  <!-- Waves -->
  <div class="wave-container">
    <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
         viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
      <defs>
        <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
      </defs>
      <g class="parallax">
        <use xlink:href="#gentle-wave" x="48" y="0" />
        <use xlink:href="#gentle-wave" x="48" y="3" />
        <use xlink:href="#gentle-wave" x="48" y="5" />
        <use xlink:href="#gentle-wave" x="48" y="7" />
      </g>
    </svg>
  </div>
</section>

<script>
  // ================= Dynamic Headings (tetap) =================
  const headings = [
   
  "Dive with Trust",
  "Real Diving",
  "True Underwater",
  "Trusted PADI Center",
  "Since 1991",
  "Tulamben Wreck",
  "Amed Reefs",
  "Manta Encounters",
  "Padang Bai",
  "Sanur Dives",
  "Safety First",
  "PADI Guided",
  "Handled with Care",
  "Professional Diving",
  "Dive Right",
  "Real People",
  "Dive Smart",
  "Calm Ocean",
  "Respect Ocean",
  "Trusted Diving",
  "Learn Dive Grow",
  "First Dive",
  "Stay Calm",
  "Ocean Guides",
  "Experience Matters"

  ];

  const headingEl = document.getElementById('dynamicHeading');
  let currentIndex = Math.floor(Math.random() * headings.length);
  headingEl.innerHTML = headings[currentIndex];

  function changeHeading() {
    headingEl.style.opacity = 0;
    setTimeout(() => {
      currentIndex = (currentIndex + 1) % headings.length;
      headingEl.innerHTML = headings[currentIndex];
      headingEl.style.opacity = 1;
    }, 800);
  }
  setInterval(changeHeading, 30000);
</script>
