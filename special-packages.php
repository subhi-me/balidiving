<?php
// special-packages.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

include('../template/database/main.php');

$packages = [
    [
        'id'       => 1,
        'key'      => 'snorkel_try_combo',
        'name'     => 'Snorkeling + Try Diving Combo (2 Days)',
        'summary'  => 'Day 1 relaxed snorkeling, Day 2 your first dive with full support.',
        'description' => 'Perfect if you want to warm up on the surface first, then try diving the next day. Includes gear, transport, lunch, and guiding both days.',
        'duration' => '2 days',
        'basePrice'=> 285,
        'image'    => 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg'
    ],
    [
        'id'       => 2,
        'key'      => '3day_fun_safari',
        'name'     => '3-Day Fun Diving Safari',
        'summary'  => 'Mix of wrecks, reefs, and possibly Nusa Penida – tailored to your level.',
        'description' => 'We plan 3 days of diving based on your certification, conditions, and what you most want to see. Includes tanks, weights, guiding, and transfers from South Bali.',
        'duration' => '3 days',
        'basePrice'=> 480,
        'image'    => 'https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg'
    ],
    [
        'id'       => 3,
        'key'      => 'course_accommodation',
        'name'     => 'Open Water + Accommodation Bundle',
        'summary'  => 'Get certified and stay near the ocean with hand-picked local partners.',
        'description' => 'We bundle your PADI Open Water course with comfortable accommodation close to the training site, so you can wake up, walk to the water, and focus fully on learning.',
        'duration' => '4 days / 3 nights',
        'basePrice'=> 690,
        'image'    => 'https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg'
    ],
];

if(!$packages){
    $packages = [];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Special Dive Packages · Bali Diving</title>

  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>
  <?php include('template/style-product.php')?>
</head>
<body>
<header class="hero">
  <div class="hero-background" id="heroBackground"></div>
  <div class="hero-overlay">
    <div class="hero-badge">
      <span>Custom Itineraries</span> · <span>Family-run since 1990s</span>
    </div>
    <h1 class="hero-title" id="heroTitle">Special Dive Packages</h1>
    <p class="hero-subtitle" id="heroSubtitle">
      Short escapes, combo days, and tailor-made programs for couples, families, and small groups.
    </p>
  </div>
</header>
<?php include('template/nav-product.php')?>

<section class="article-section">
  <h2 class="article-title" id="articleTitle">Bundle Your Bali Ocean Days</h2>
  <div class="article-content">
    <p>
      Sometimes one day is not enough. These packages are designed to connect your days in the water
      into a smooth, memorable mini-holiday – with less planning and more time actually enjoying Bali.
    </p>
  </div>
</section>

<section class="cards-section">
  <h2 class="section-title" id="locationsTitle">Choose a Package</h2>
  <div class="cards-grid" id="cardsGrid"></div>
</section>

<div class="offcanvas-overlay" id="offcanvasOverlay"></div>
<div class="offcanvas" id="offcanvas">
  <div class="offcanvas-header">
    <button class="offcanvas-close" id="offcanvasClose">&times;</button>
    <h3 class="offcanvas-title" id="offcanvasTitle"></h3>
  </div>
  <div class="offcanvas-body">
    <img class="offcanvas-cover" id="offcanvasCover" src="" alt="">
    <div class="success-message" id="successMessage">
      Thank you! Your package enquiry has been sent. We will get back to you shortly.
    </div>

    <p class="offcanvas-description" id="offcanvasDescription"></p>

    <div class="price-section">
      <div class="price-label">Package price (from)</div>
      <div class="price-main">
        <span class="price-amount" id="offcanvasPrice"></span>
      </div>
      <div class="text-xs text-slate-500 mt-1">
        Prices in USD with IDR preview. Final quote depends on group size, dates, and custom add-ons.
      </div>
    </div>

    <!-- CROSS SELL -->
    <div class="mt-6 pt-4 border-t border-slate-200">
      <p class="text-sm font-semibold text-slate-700 mb-2">
        Other divers also loved:
      </p>
      <div class="flex flex-wrap gap-2 text-sm">
        <a href="/snorkeling" class="px-3 py-1 rounded-full border border-sky-200 text-sky-700 bg-sky-50 hover:bg-sky-100">Snorkeling</a>
        <a href="/try-diving" class="px-3 py-1 rounded-full border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100">Try Diving</a>
        <a href="/fun-diving" class="px-3 py-1 rounded-full border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100">Fun Diving</a>
        <a href="/padi-courses" class="px-3 py-1 rounded-full border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100">PADI Courses</a>
      </div>
    </div>

    <form id="bookingForm" class="mt-4">
      <div class="form-group">
        <label class="form-label">Preferred start date</label>
        <div class="calendar-container">
          <div class="calendar-header">
            <button type="button" class="calendar-nav" id="prevMonth">‹</button>
            <h3 class="calendar-title" id="calendarTitle"></h3>
            <button type="button" class="calendar-nav" id="nextMonth">›</button>
          </div>
          <div class="calendar-grid">
            <div class="calendar-weekdays">
              <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
              <div>Thu</div><div>Fri</div><div>Sat</div>
            </div>
            <div class="calendar-days" id="calendarDays"></div>
          </div>
        </div>
        <input type="hidden" id="selectedDate" name="selectedDate" required>
      </div>

      <div class="form-group">
        <label for="groupInput" class="form-label">Group size &amp; notes</label>
        <textarea id="groupInput" class="form-input" rows="3" placeholder="How many people, any kids, and what you’d like to include or change."></textarea>
      </div>

      <div class="form-group hidden" id="emailGroup">
        <label for="emailInput" class="form-label">Email address</label>
        <input type="email" id="emailInput" class="form-input" placeholder="you@example.com" required>
      </div>

      <button type="submit" class="checkout-button" id="checkoutButton">Request custom quote</button>
    </form>
  </div>
</div>

<script>
  const packagesData = <?=json_encode($packages, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
  const usdToIdr  = <?= (int)$USD_TO_IDR; ?>;

  const heroImages = [
    "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg"
  ];

  let currentImageIndex = 0;
  let currentPackage   = null;

  function rotateHeroBackground(){
    const heroBackground = document.getElementById('heroBackground');
    heroBackground.style.backgroundImage = `url('${heroImages[currentImageIndex]}')`;
    currentImageIndex = (currentImageIndex + 1) % heroImages.length;
  }

  function formatUsdIdr(usd){
    const rupiah = Math.round(usd * usdToIdr);
    return {
      usdLabel: `$${usd.toFixed(0)}`,
      idrLabel: `IDR ${rupiah.toLocaleString('id-ID')}`
    };
  }

  function updatePricing(){
    if(!currentPackage) return;
    const base = currentPackage.basePrice || 0;
    const labels = formatUsdIdr(base);
    document.getElementById('offcanvasPrice').textContent =
      `${labels.usdLabel} · ${labels.idrLabel}`;
  }

  function renderCards(){
    const grid = document.getElementById('cardsGrid');
    grid.innerHTML = packagesData.map(p => `
      <div class="card" data-id="${p.id}">
        <div class="card-header">
          <img class="card-image" src="${p.image}" alt="${p.name}">
          <div class="duration-badge">
            <span><i class="fa-regular fa-clock"></i></span>
            <span>${p.duration}</span>
          </div>
        </div>
        <div class="card-content">
          <h3 class="card-title">${p.name}</h3>
          <p class="card-summary">${p.summary}</p>
          <button class="card-select-btn">See package details</button>
        </div>
      </div>
    `).join('');

    grid.querySelectorAll('.card').forEach(card=>{
      card.addEventListener('click',()=>{
        const id = parseInt(card.dataset.id,10);
        openOffcanvas(id);
      });
      const btn = card.querySelector('.card-select-btn');
      btn.addEventListener('click',e=>{
        e.stopPropagation();
        const id = parseInt(card.closest('.card').dataset.id,10);
        openOffcanvas(id);
      });
    });
  }

  let currentCalendarDate = new Date();
  let selectedDateValue   = null;

  function renderCalendar(){
    const year  = currentCalendarDate.getFullYear();
    const month = currentCalendarDate.getMonth();
    const monthNames = [
      'January','February','March','April','May','June','July',
      'August','September','October','November','December'
    ];
    const title = document.getElementById('calendarTitle');
    title.textContent = `${monthNames[month]} ${year}`;

    const daysContainer = document.getElementById('calendarDays');
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

      if(dateObj < today){
        el.classList.add('past');
      }else{
        el.classList.add('available');
        el.addEventListener('click',()=>selectDate(dateObj,el));
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
    document.getElementById('selectedDate').value = date.toISOString().split('T')[0];

    document.getElementById('emailGroup').classList.remove('hidden');
    const btn = document.getElementById('checkoutButton');
    btn.classList.add('show');
    btn.textContent = 'Request quote for this date';
  }

  function initCalendar(){
    document.getElementById('prevMonth').addEventListener('click',()=>{
      currentCalendarDate.setMonth(currentCalendarDate.getMonth()-1);
      renderCalendar();
    });
    document.getElementById('nextMonth').addEventListener('click',()=>{
      currentCalendarDate.setMonth(currentCalendarDate.getMonth()+1);
      renderCalendar();
    });
    renderCalendar();
  }

  const offcanvasOverlay = document.getElementById('offcanvasOverlay');
  const offcanvas        = document.getElementById('offcanvas');

  function openOffcanvas(id){
    currentPackage = packagesData.find(l => l.id === id);
    if(!currentPackage) return;

    currentCalendarDate = new Date();
    selectedDateValue   = null;
    document.getElementById('selectedDate').value = '';

    document.getElementById('offcanvasTitle').textContent = currentPackage.name;
    document.getElementById('offcanvasCover').src = currentPackage.image;
    document.getElementById('offcanvasDescription').textContent = currentPackage.description;

    document.getElementById('successMessage').classList.remove('show');
    document.getElementById('bookingForm').reset();
    document.getElementById('emailGroup').classList.add('hidden');
    const btn = document.getElementById('checkoutButton');
    btn.classList.remove('show');
    btn.textContent = 'Request custom quote';

    updatePricing();

    offcanvasOverlay.classList.add('active');
    offcanvas.classList.add('active');

    renderCalendar();
  }

  function closeOffcanvas(){
    offcanvasOverlay.classList.remove('active');
    offcanvas.classList.remove('active');
  }

  document.getElementById('offcanvasClose').addEventListener('click',closeOffcanvas);
  offcanvasOverlay.addEventListener('click',closeOffcanvas);

  document.getElementById('bookingForm').addEventListener('submit',async (e)=>{
    e.preventDefault();
    if(!currentPackage) return;

    const email = document.getElementById('emailInput').value.trim();
    const date  = document.getElementById('selectedDate').value;
    const group = document.getElementById('groupInput').value.trim();

    if(!email || !date) return;

    const btn = document.getElementById('checkoutButton');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    const payload = {
      type: 'special_package_request',
      packageKey: currentPackage.key,
      packageName: currentPackage.name,
      email,
      date,
      groupNotes: group,
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
      console.log('Special package enquiry mock payload:',payload);
      ok = true;
    }

    if(ok){
      document.getElementById('successMessage').classList.add('show');
      document.getElementById('bookingForm').reset();
      document.getElementById('emailGroup').classList.add('hidden');
      btn.classList.remove('show');
      setTimeout(()=>document.getElementById('successMessage').classList.remove('show'),5000);
    }else{
      alert('Failed to send enquiry. Please try again or contact us via WhatsApp.');
    }

    btn.disabled = false;
    btn.textContent = 'Request custom quote';
  });

  (function init(){
    rotateHeroBackground();
    setInterval(rotateHeroBackground,2500);
    renderCards();
    initCalendar();
  })();
</script>

<?php include('template/footer-product.php')?>
</body>
</html>
