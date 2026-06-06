<style>
  body { box-sizing: border-box; }

  /* ===== CTA readability across any theme/background ===== */
  .cta-wrap{
    position: relative;
    margin-bottom: 16px;
    text-align: center;
    padding: 14px 14px;
    border-radius: 18px;
    background: rgba(15, 23, 42, 0.28); /* navy glass */
    border: 1px solid rgba(255,255,255,.16);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 14px 38px rgba(0,0,0,.18);
  }
  .cta-title{
    color:#ffffff;                 /* MUST white */
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.15;
    font-size: 26px;
    text-shadow: 0 2px 18px rgba(0,0,0,.55);
  }
  .cta-sub{
    margin-top: 8px;
    color: rgba(255,255,255,.92);
    font-size: 14px;
    line-height: 1.6;
    text-shadow: 0 1px 14px rgba(0,0,0,.55);
  }
  @media (min-width: 640px){
    .cta-title{ font-size: 32px; }
    .cta-sub{ font-size: 16px; }
    .cta-wrap{ margin-bottom: 18px; padding: 16px 18px; }
  }

  /* Pastikan teks input selalu hitam/navy */
  #searchInput{ color:#0f172a; }
  #searchInput::placeholder{ color:#64748b; }

  /* Dropdown items */
  .dropdown-item{
    padding: 14px 16px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    user-select: none;
  }
  .dropdown-item:hover{
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,.08);
    filter: saturate(1.1);
  }

  /* Special highlight – Bali Weather Dive */
  .highlight-weather{
    background: linear-gradient(135deg,#0ea5e9,#0284c7);
    color:#ffffff;
    box-shadow: 0 10px 30px rgba(14,165,233,.35);
    position: relative;
  }
  .highlight-weather::after{
    position:absolute;
    top:8px;
    right:10px;
    font-size:10px;
    font-weight:700;
    padding:2px 6px;
    border-radius:999px;
    background:rgba(255,255,255,.2);
    letter-spacing:.05em;
  }

  /* Smooth scrollbar (optional) */
  #dropdown .max-h-\[65vh\]::-webkit-scrollbar{ width: 10px; }
  #dropdown .max-h-\[65vh\]::-webkit-scrollbar-thumb{
    background: rgba(2,132,199,.25);
    border-radius: 999px;
    border: 3px solid rgba(255,255,255,.6);
  }

  /* ===== Explore quick link under search ===== */
  .explore-quick{
    margin-top: 12px;
    display: flex;
    justify-content: center;
  }
  .explore-pill{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding: 10px 14px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.28);
    border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 10px 28px rgba(0,0,0,.16);
    color:#fff;
    font-weight: 700;
    letter-spacing: -0.01em;
    text-decoration: none;
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .explore-pill:hover{
    transform: translateY(-1px);
    box-shadow: 0 14px 34px rgba(0,0,0,.22);
    background: rgba(15, 23, 42, 0.34);
  }
  .explore-icon{
    width: 18px;
    height: 18px;
    display:block;
    opacity: .95;
  }
</style>

<div class="min-h-full bg-transparent">
  <div class="flex items-center justify-center min-h-full py-5">
    <!-- TOP STACK CONTEXT -->
    <div class="relative w-full max-w-3xl mx-4 z-[9999]">

      <!-- CTA / TITLE (Readable in any theme) -->
      <div class="cta-wrap">
        <div class="cta-title">Find Your Perfect Dive in Bali</div>
        <div class="cta-sub">
          Search dives, courses & snorkeling — fast, simple, and ready to book.
        </div>
      </div>

      <!-- Search Bar -->
      <div class="relative">
        <input
          type="text"
          id="searchInput"
          placeholder="Search diving activities..."
          class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-2xl shadow-lg
                 focus:outline-none focus:border-blue-600 focus:shadow-xl
                 transition-all duration-300 bg-white"
        />

        <div
          class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer"
          onclick="performSearch()"
          aria-label="Search"
          role="button"
        >
          <svg class="w-6 h-6 text-gray-400 hover:text-blue-600 transition-colors duration-200"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- ✅ Explore link under search -->      
      <div class="explore-quick">
<a class="explore-pill"
   href="https://wa.me/6287861190174?text=Hi%20Bali%20Diving%20%0aI%20found%20you%20from%20your%20website."
   aria-label="WhatsApp"
   target="_blank" rel="noopener"
   title="Chat via WhatsApp">
  <i class="fa-brands fa-whatsapp"></i>
  
</a>

&nbsp;
      
<a class="explore-pill" href="#grid" aria-label="Explore"
   onclick="event.preventDefault(); document.getElementById('grid')?.scrollIntoView({ behavior:'smooth', block:'start' });">
  <i class="fa-solid fa-arrow-down"></i>
  
</a>&nbsp;
        <a class="explore-pill" href="https://balidiving.com/explore/" aria-label="Explore">
          <svg class="explore-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm2.8 7.2l-1.7 5.1-5.1 1.7 1.7-5.1z"/>
          </svg>
          Explore
        </a>
      </div>

      <!-- DROPDOWN GRID -->
      <div
        id="dropdown"
        class="absolute top-full left-0 right-0 mt-3
               bg-white rounded-2xl shadow-2xl border border-gray-100
               opacity-0 invisible translate-y-2
               transition-all duration-300
               z-[10000]"
      >
        <div class="max-h-[65vh] overflow-y-auto p-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

            <div class="dropdown-item bg-slate-50 text-slate-800"
                 data-url="https://balidiving.com/recommendations/tulamben">
              Tulamben Wreck Diving
            </div>

            <div class="dropdown-item bg-blue-50 text-blue-900"
                 data-url="https://balidiving.com/recommendations/amed">
              Amed Coral Diving
            </div>

            <div class="dropdown-item bg-emerald-50 text-emerald-900"
                 data-url="https://balidiving.com/recommendations/nusa-penida">
              Nusa Penida Manta Dive
            </div>

            <div class="dropdown-item bg-indigo-50 text-indigo-900"
                 data-url="https://balidiving.com/recommendations/menjangan">
              Menjangan Island Wall Diving
            </div>

            <div class="dropdown-item bg-cyan-50 text-cyan-900"
                 data-url="https://balidiving.com/recommendations/padang-bai">
              Padang Bai Blue Lagoon
            </div>

            <div class="dropdown-item bg-teal-50 text-teal-900"
                 data-url="https://balidiving.com/recommendations/snorkeling">
              Snorkeling for Non-Divers
            </div>

            <div class="dropdown-item bg-amber-50 text-amber-900"
                 data-url="https://balidiving.com/recommendations/open-water">
              PADI Open Water Course
            </div>

            <div class="dropdown-item bg-purple-50 text-purple-900"
                 data-url="https://balidiving.com/recommendations/advanced-open-water">
              Advanced Open Water Course
            </div>

            <div class="dropdown-item highlight-weather"
                 data-url="https://balidiving.com/weather">
              ☀️ Bali Weather Dive
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  setTimeout(() => {

    const searchInput = document.getElementById('searchInput');
    const dropdown = document.getElementById('dropdown');

    if (!searchInput || !dropdown) return;

    searchInput.addEventListener('focus', showDropdown);
    searchInput.addEventListener('click', showDropdown);

    searchInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') {
        e.preventDefault();
        performSearch();
      }
    });

    document.addEventListener('click', e => {
      if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
        hideDropdown();
      }
    });

    dropdown.addEventListener('click', e => {
      const item = e.target.closest('.dropdown-item');
      if (!item) return;
      searchInput.value = item.textContent.replace('FEATURED','').trim();
      window.open(item.dataset.url, '_self');
      hideDropdown();
    });

    function showDropdown(){
      dropdown.classList.remove('opacity-0','invisible','translate-y-2');
      dropdown.classList.add('opacity-100','visible','translate-y-0');
    }

    function hideDropdown(){
      dropdown.classList.add('opacity-0','invisible','translate-y-2');
      dropdown.classList.remove('opacity-100','visible','translate-y-0');
    }

    function performSearch(){
      const keyword = searchInput.value.trim();
      if(!keyword) return;
      const q = keyword.replace(/\s+/g,'+');
      window.open(`https://balidiving.com/explore/?q=${q}`,'_self');
    }

  }, 1000);
</script>
