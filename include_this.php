<?php
// include_this.php
// Shared JS + helper untuk halaman produk (snorkeling, try-diving, dsb.)

if (!function_exists('render_cross_sell_block')) {
    function render_cross_sell_block(): void { ?>
      <div class="mt-6 pt-4 border-t border-slate-200">
        <p class="text-sm font-semibold text-slate-700 mb-2">
          Other divers also loved:
        </p>
        <div class="flex flex-wrap gap-2 text-sm">
          <a href="/snorkeling" class="px-3 py-1 rounded-full border border-sky-200 text-sky-700 bg-sky-50 hover:bg-sky-100">
            Snorkeling Trips
          </a>
          <a href="/try-diving" class="px-3 py-1 rounded-full border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100">
            Try Diving
          </a>
          <a href="/fun-diving" class="px-3 py-1 rounded-full border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
            Fun Diving
          </a>
          <a href="/padi-courses" class="px-3 py-1 rounded-full border border-violet-200 text-violet-700 bg-violet-50 hover:bg-violet-100">
            PADI Courses
          </a>
          <a href="/special-packages" class="px-3 py-1 rounded-full border border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100">
            Special Packages
          </a>
        </div>
      </div>
    <?php }
}
?>
<script>
// =========================
// Shared helpers
// =========================

function bdMapEmbedByLatLng(lat,lng,zoom){
  return `https://www.google.com/maps?q=${lat},${lng}&hl=en&z=${zoom}&output=embed`;
}
function bdMapEmbedByQuery(q,zoom){
  return `https://www.google.com/maps?q=${encodeURIComponent(q)}&hl=en&z=${zoom}&output=embed`;
}
function bdGetMapSrc(loc){
  if(typeof loc.lat === 'number' && typeof loc.lng === 'number'){
    return bdMapEmbedByLatLng(loc.lat,loc.lng,loc.zoom || 12);
  }
  const q = loc.query || loc.name || 'Bali';
  return bdMapEmbedByQuery(q,loc.zoom || 12);
}
function bdGetMapLink(loc){
  if(typeof loc.lat === 'number' && typeof loc.lng === 'number'){
    return `https://www.google.com/maps/search/?api=1&query=${loc.lat},${loc.lng}`;
  }
  const q = loc.query || loc.name || 'Bali';
  return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}`;
}

function bdFormatUsdIdr(usd, rate){
  const rupiah = Math.round((usd || 0) * (rate || 16000));
  return {
    usdLabel: `$${(usd || 0).toFixed(0)}`,
    idrLabel: `IDR ${rupiah.toLocaleString('id-ID')}`
  };
}

// =========================
// Main initializer
// =========================

function initProductPage(config){
  // config:
  // - locations      : array
  // - usdToIdr       : number
  // - activityKey    : 'snorkeling' | 'try_diving' | 'fun_diving' | 'padi_courses' | null
  // - endpoint       : 'snorkeling.php', 'try-diving.php', ...
  // - requestType    : 'snorkeling_request', ...
  // - heroImages     : array of URLs
  // - defaultConfig  : { hero_title, hero_subtitle, article_title, locations_title }

  const locations   = config.locations || [];
  const usdToIdr    = config.usdToIdr || 16000;
  const activityKey = config.activityKey || null;
  const endpoint    = config.endpoint || '';
  const requestType = config.requestType || 'booking_request';
  const heroImages  = config.heroImages || [];
  const defaults    = config.defaultConfig || {};

  let currentImageIndex = 0;
  let currentLocation   = null;

  // Hero rotation
  function rotateHeroBackground(){
    if(!heroImages.length) return;
    const el = document.getElementById('heroBackground');
    if(!el) return;
    el.style.backgroundImage = `url('${heroImages[currentImageIndex]}')`;
    currentImageIndex = (currentImageIndex + 1) % heroImages.length;
  }
  rotateHeroBackground();
  if(heroImages.length > 1){
    setInterval(rotateHeroBackground, 2500);
  }

  // Set default texts (if elementSdk tidak dipakai)
  if(!window.elementSdk){
    const hTitle = document.getElementById('heroTitle');
    const hSub   = document.getElementById('heroSubtitle');
    const aTitle = document.getElementById('articleTitle');
    const lTitle = document.getElementById('locationsTitle');
    if(hTitle && defaults.hero_title)     hTitle.textContent = defaults.hero_title;
    if(hSub   && defaults.hero_subtitle)  hSub.textContent   = defaults.hero_subtitle;
    if(aTitle && defaults.article_title)  aTitle.textContent = defaults.article_title;
    if(lTitle && defaults.locations_title)lTitle.textContent = defaults.locations_title;
  }

  // Cards
  function renderCards(){
    const grid = document.getElementById('cardsGrid');
    if(!grid) return;
    grid.innerHTML = locations.map(loc => `
      <div class="card" data-id="${loc.id}">
        <div class="card-header">
          <img class="card-image" src="${loc.image}" alt="${loc.name}">
          <div class="duration-badge">
            <span><i class="fa-regular fa-clock"></i></span>
            <span>${loc.duration || ''}</span>
          </div>
        </div>
        <div class="card-content">
          <h3 class="card-title">${loc.name}</h3>
          <p class="card-summary">${loc.summary}</p>
          <button class="card-select-btn">See details</button>
        </div>
      </div>
    `).join('');

    grid.querySelectorAll('.card').forEach(card=>{
      card.addEventListener('click',()=>{
        const id = parseInt(card.dataset.id,10);
        openOffcanvas(id);
      });
      const btn = card.querySelector('.card-select-btn');
      if(btn){
        btn.addEventListener('click',e=>{
          e.stopPropagation();
          const id = parseInt(card.closest('.card').dataset.id,10);
          openOffcanvas(id);
        });
      }
    });
  }

  // Offcanvas + pricing (simple, no flash sale)
  const offcanvasOverlay = document.getElementById('offcanvasOverlay');
  const offcanvas        = document.getElementById('offcanvas');

  function updatePricing(){
    if(!currentLocation) return;
    const base = currentLocation.basePrice || 0;
    const labels = bdFormatUsdIdr(base, usdToIdr);
    const priceEl = document.getElementById('offcanvasPrice');
    const origEl  = document.getElementById('originalPrice');
    const badge   = document.getElementById('discountBadge');
    if(priceEl) priceEl.textContent = `${labels.usdLabel} · ${labels.idrLabel}`;
    if(origEl)  origEl.textContent  = '';     // no original price
    if(badge)   badge.style.display = 'none'; // hide badge jika markup masih ada
  }

  function openOffcanvas(id){
    currentLocation = locations.find(l => l.id === id);
    if(!currentLocation || !offcanvas || !offcanvasOverlay) return;

    currentSubKey = currentLocation.key || null;
    currentCalendarDate = new Date();
    selectedDateValue   = null;
    const dateInput = document.getElementById('selectedDate');
    if(dateInput) dateInput.value = '';

    const t = document.getElementById('offcanvasTitle');
    const img = document.getElementById('offcanvasCover');
    const desc = document.getElementById('offcanvasDescription');

    if(t)   t.textContent   = currentLocation.name;
    if(img) img.src         = currentLocation.image;
    if(desc)desc.textContent= currentLocation.description || currentLocation.summary || '';

    const mapIframe = document.getElementById('locationMap');
    const mapLink   = document.getElementById('openInMapsBtn');
    if(mapIframe) mapIframe.src = bdGetMapSrc(currentLocation);
    if(mapLink)   mapLink.href  = bdGetMapLink(currentLocation);

    const successMsg = document.getElementById('successMessage');
    if(successMsg) successMsg.classList.remove('show');

    const form = document.getElementById('bookingForm');
    if(form) form.reset();

    const emailGroup = document.getElementById('emailGroup');
    const btn = document.getElementById('checkoutButton');
    if(emailGroup) emailGroup.classList.add('hidden');
    if(btn){
      btn.classList.remove('show');
      btn.textContent = 'Check availability';
      btn.disabled = false;
    }

    updatePricing();

    offcanvasOverlay.classList.add('active');
    offcanvas.classList.add('active');

    if(activityKey && currentSubKey){
      loadAvailabilityForMonth(currentSubKey,currentCalendarDate.getFullYear(),currentCalendarDate.getMonth());
    }else{
      renderCalendar();
    }
  }

  function closeOffcanvas(){
    if(!offcanvas || !offcanvasOverlay) return;
    offcanvasOverlay.classList.remove('active');
    offcanvas.classList.remove('active');
  }

  if(document.getElementById('offcanvasClose')){
    document.getElementById('offcanvasClose').addEventListener('click',closeOffcanvas);
  }
  if(offcanvasOverlay){
    offcanvasOverlay.addEventListener('click',closeOffcanvas);
  }

  // Calendar & availability (optional)
  let currentCalendarDate = new Date();
  let selectedDateValue   = null;
  let currentAvailabilityMap = {};
  let currentSubKey = null;

  async function loadAvailabilityForMonth(subKey,year,monthIndex){
    if(!activityKey || !endpoint){
      // Tidak pakai API, semua tanggal masa depan = available
      currentAvailabilityMap = {};
      renderCalendar();
      return;
    }
    const monthStr = `${year}-${String(monthIndex+1).padStart(2,'0')}`;
    try{
      const res = await fetch(`${endpoint}?action=availability&sub_key=${encodeURIComponent(subKey)}&month=${encodeURIComponent(monthStr)}`, {cache:'no-store'});
      const json = await res.json();
      if(json.ok){
        currentAvailabilityMap = json.dates || {};
      }else{
        currentAvailabilityMap = {};
      }
    }catch(e){
      console.error('availability error',e);
      currentAvailabilityMap = {};
    }
    renderCalendar();
  }

  function renderCalendar(){
    const year  = currentCalendarDate.getFullYear();
    const month = currentCalendarDate.getMonth();
    const monthNames = [
      'January','February','March','April','May','June','July',
      'August','September','October','November','December'
    ];
    const title = document.getElementById('calendarTitle');
    const daysContainer = document.getElementById('calendarDays');
    if(!title || !daysContainer) return;

    title.textContent = `${monthNames[month]} ${year}`;
    daysContainer.innerHTML = '';

    const firstDay = new Date(year,month,1);
    const lastDay  = new Date(year,month+1,0);
    const daysInMonth = lastDay.getDate();
    const startWeekday = firstDay.getDay();

    const today = new Date();
    today.setHours(0,0,0,0);

    for(let i=0;i<startWeekday;i++){
      const d = document.createElement('div');
      d.className='calendar-day other-month';
      daysContainer.appendChild(d);
    }

    for(let day=1; day<=daysInMonth; day++){
      const el = document.createElement('div');
      el.className = 'calendar-day';
      el.textContent = day;

      const dateObj = new Date(year,month,day);
      const iso = dateObj.toISOString().split('T')[0];

      if(dateObj < today){
        el.classList.add('past');
      }else{
        let avail = true;
        if(activityKey && Object.keys(currentAvailabilityMap).length > 0){
          if(currentAvailabilityMap.hasOwnProperty(iso)){
            avail = !!currentAvailabilityMap[iso];
          }
        }
        // Kalau tidak pakai API / map kosong => anggap available
        if(avail){
          el.classList.add('available');
          el.addEventListener('click',()=>selectDate(dateObj,el));
        }else{
          el.classList.add('unavailable');
        }
      }

      if(selectedDateValue && dateObj.getTime() === selectedDateValue.getTime()){
        el.classList.add('selected');
      }

      daysContainer.appendChild(el);
    }
  }

  function selectDate(date,element){
    document.querySelectorAll('.calendar-day.selected').forEach(d=>d.classList.remove('selected'));
    element.classList.add('selected');
    selectedDateValue = new Date(date);
    const hidden = document.getElementById('selectedDate');
    if(hidden) hidden.value = date.toISOString().split('T')[0];

    const emailGroup = document.getElementById('emailGroup');
    const btn = document.getElementById('checkoutButton');
    if(emailGroup) emailGroup.classList.remove('hidden');
    if(btn){
      btn.classList.add('show');
      btn.textContent = 'Check & send request';
    }
  }

  function initCalendar(){
    const prev = document.getElementById('prevMonth');
    const next = document.getElementById('nextMonth');
    if(prev){
      prev.addEventListener('click',()=>{
        currentCalendarDate.setMonth(currentCalendarDate.getMonth()-1);
        if(activityKey && currentSubKey){
          loadAvailabilityForMonth(currentSubKey,currentCalendarDate.getFullYear(),currentCalendarDate.getMonth());
        }else{
          renderCalendar();
        }
      });
    }
    if(next){
      next.addEventListener('click',()=>{
        currentCalendarDate.setMonth(currentCalendarDate.getMonth()+1);
        if(activityKey && currentSubKey){
          loadAvailabilityForMonth(currentSubKey,currentCalendarDate.getFullYear(),currentCalendarDate.getMonth());
        }else{
          renderCalendar();
        }
      });
    }
    renderCalendar();
  }

  // Booking form
  const bookingForm = document.getElementById('bookingForm');
  if(bookingForm){
    bookingForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      if(!currentLocation) return;

      const emailEl = document.getElementById('emailInput');
      const dateEl  = document.getElementById('selectedDate');
      if(!emailEl || !dateEl) return;

      const email = emailEl.value.trim();
      const date  = dateEl.value;
      if(!email || !date) return;

      const btn = document.getElementById('checkoutButton');
      if(btn){
        btn.disabled = true;
        btn.textContent = 'Sending...';
      }

      const payload = {
        type: requestType,
        activity: activityKey,
        locationKey: currentLocation.key,
        locationName: currentLocation.name,
        email,
        date,
        createdAt: new Date().toISOString()
      };

      let ok = false;
      if(window.dataSdk && typeof window.dataSdk.create === 'function'){
        try{
          const res = await window.dataSdk.create(payload);
          ok = !!res.isOk;
        }catch(err){
          console.error(err);
        }
      }else{
        console.log('Booking mock payload:', payload);
        ok = true;
      }

      const successMsg = document.getElementById('successMessage');
      const emailGroup = document.getElementById('emailGroup');

      if(ok){
        if(successMsg) successMsg.classList.add('show');
        bookingForm.reset();
        if(emailGroup) emailGroup.classList.add('hidden');
        if(btn) btn.classList.remove('show');
        setTimeout(()=>{
          if(successMsg) successMsg.classList.remove('show');
        },5000);
      }else{
        alert('Failed to send request. Please try again or contact us via WhatsApp.');
      }

      if(btn){
        btn.disabled = false;
        btn.textContent = 'Check & send request';
      }
    });
  }

  // Accordions (main + include/exclude)
  function initAccordions(){
    const mainBtn = document.getElementById('accordionButton');
    const mainContent = document.getElementById('accordionContent');
    if(mainBtn && mainContent){
      mainBtn.addEventListener('click',()=>{
        const active = mainContent.classList.toggle('active');
        mainBtn.classList.toggle('active',active);
      });
    }

    const includeBtn = document.getElementById('includeButton');
    const includeContent = document.getElementById('includeContent');
    if(includeBtn && includeContent){
      includeBtn.addEventListener('click',()=>{
        const active = includeContent.classList.toggle('active');
        includeBtn.classList.toggle('active',active);
      });
    }

    const excludeBtn = document.getElementById('excludeButton');
    const excludeContent = document.getElementById('excludeContent');
    if(excludeBtn && excludeContent){
      excludeBtn.addEventListener('click',()=>{
        const active = excludeContent.classList.toggle('active');
        excludeBtn.classList.toggle('active',active);
      });
    }
  }

  // Init
  renderCards();
  initCalendar();
  initAccordions();
}
</script>
