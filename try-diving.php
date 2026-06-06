<?php
// try-diving.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

include('../template/database/main.php'); // harus define: $pdo, $USD_TO_IDR, $WEEKLY_DEFAULTS, $GLOBAL_TEMPLATE, json_headers(), weekday_key()

$activityKey = 'try_diving';

/* ---------- API: AVAILABILITY PER DIVE SITE ---------- */
/*
   try-diving.php?action=availability&sub_key=padang_bai&month=2025-11
*/
if(isset($_GET['action']) && $_GET['action']==='availability'){
    json_headers();

    $subKey = $_GET['sub_key'] ?? '';
    $month  = $_GET['month']  ?? ''; // YYYY-MM

    if(!$subKey || !preg_match('~^\d{4}-\d{2}$~', $month)){
        echo json_encode(['ok'=>false,'error'=>'bad_params']);
        exit;
    }

    $start = $month.'-01';
    $startDt = DateTime::createFromFormat('Y-m-d',$start);
    if(!$startDt){
        echo json_encode(['ok'=>false,'error'=>'bad_month']);
        exit;
    }
    $endDt = (clone $startDt)->modify('last day of this month');
    $end   = $endDt->format('Y-m-d');

    $rows = [];
    try{
        $st = $pdo->prepare("SELECT d, payload FROM booking_date_snapshots WHERE d BETWEEN :s AND :e");
        $st->execute([':s'=>$start, ':e'=>$end]);
        while($r=$st->fetch()){
            $rows[$r['d']] = $r['payload'] ? json_decode($r['payload'], true) : null;
        }
    }catch(Throwable $e){
        echo json_encode(['ok'=>false,'error'=>'db_error']);
        exit;
    }

    $result = [];
    $cursor = clone $startDt;
    while($cursor <= $endDt){
        $dStr = $cursor->format('Y-m-d');
        $wd   = weekday_key($cursor);

        $avail = $WEEKLY_DEFAULTS[$activityKey][$wd] ?? true;

        if(isset($rows[$dStr]) && is_array($rows[$dStr])){
            $p = $rows[$dStr];

            if(isset($p['subs'][$activityKey][$subKey])){
                $avail = (bool)$p['subs'][$activityKey][$subKey];
            } elseif(isset($p['svc'][$activityKey])) {
                $avail = (bool)$p['svc'][$activityKey];
            }
        }

        $result[$dStr] = $avail;
        $cursor->modify('+1 day');
    }

    echo json_encode(['ok'=>true,'dates'=>$result], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- BUILD TRY DIVING LOCATIONS ---------- */

$staticLocations = [
    'padang_bai' => [
        'id'       => 1,
        'key'      => 'padang_bai',
        'name'     => 'Try Diving · Padang Bai',
        'summary'  => 'First step to breathing underwater in a calm, shallow bay with sandy bottom.',
        'description' => '',
        'duration' => '7–8 hours',
        'basePrice'=> 95,
        'lat'      => -8.512345,
        'lng'      => 115.512345,
        'query'    => 'Padang Bai, Karangasem, Bali',
        'zoom'     => 13
    ],
    'tulamben' => [
        'id'       => 2,
        'key'      => 'tulamben',
        'name'     => 'Try Diving · Tulamben Wreck',
        'summary'  => 'Experience your first bubbles around the famous Liberty wreck from the shore.',
        'description' => '',
        'duration' => '9 hours',
        'basePrice'=> 110,
        'lat'      => -8.276543,
        'lng'      => 115.598765,
        'query'    => 'Tulamben, Kubu, Karangasem, Bali',
        'zoom'     => 13
    ],
    'amed' => [
        'id'       => 3,
        'key'      => 'amed',
        'name'     => 'Try Diving · Amed',
        'summary'  => 'Gentle conditions and long, shallow reefs for your very first dives.',
        'description' => '',
        'duration' => '9 hours',
        'basePrice'=> 105,
        'lat'      => -8.345679,
        'lng'      => 115.654321,
        'query'    => 'Amed, Abang, Karangasem, Bali',
        'zoom'     => 13
    ],
];

/* Ambil catalog dari booking_catalog */
$catalogMap = [];
try{
    $st = $pdo->prepare("SELECT sub_key, short_desc, long_desc, images FROM booking_catalog WHERE activity_key=:a");
    $st->execute([':a'=>$activityKey]);
    while($row=$st->fetch()){
        $catalogMap[$row['sub_key']] = [
            'short_desc' => $row['short_desc'] ?? '',
            'long_desc'  => $row['long_desc'] ?? '',
            'images'     => $row['images'] ? json_decode($row['images'], true) : null
        ];
    }
}catch(Throwable $e){}

/* Base price dari GLOBAL_TEMPLATE kalau tersedia */
$globalPrices = [];
if(is_array($GLOBAL_TEMPLATE) && isset($GLOBAL_TEMPLATE['prices'][$activityKey])){
    $globalPrices = $GLOBAL_TEMPLATE['prices'][$activityKey];
}

$locations = [];
foreach($staticLocations as $key => $base){
    $loc = $base;

    if(isset($globalPrices[$key]['usd']) && is_numeric($globalPrices[$key]['usd'])){
        $loc['basePrice'] = (float)$globalPrices[$key]['usd'];
    }

    if(isset($catalogMap[$key])){
        $cat = $catalogMap[$key];
        if(!empty($cat['short_desc'])) $loc['summary'] = $cat['short_desc'];
        if(!empty($cat['long_desc']))  $loc['description'] = $cat['long_desc'];
        if(is_array($cat['images']) && !empty($cat['images'][0])){
            $loc['image'] = $cat['images'][0];
        }
    }

    if(empty($loc['description'])) $loc['description'] = $loc['summary'];
    if(empty($loc['image'])){
        $loc['image'] = 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg';
    }

    $locations[] = $loc;
}
if(!$locations){
    $locations = array_values($staticLocations);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Try Diving in Bali · Bali Diving</title>

  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>
  <?php include('template/style-product.php')?>
</head>
<body>
<header class="hero">
  <div class="hero-background" id="heroBackground"></div>
  <div class="hero-overlay">
    <div class="hero-badge">
      <span>Discover Scuba · 1 Day</span> · <span>No license needed</span>
    </div>
    <h1 class="hero-title" id="heroTitle">Try Diving in Bali</h1>
    <p class="hero-subtitle" id="heroSubtitle">
      Your first breaths underwater with a patient instructor by your side – in warm, clear Balinese water.
    </p>
  </div>
</header>
<?php include('template/nav-product.php')?>

<section class="article-section">
  <h2 class="article-title" id="articleTitle">Your First Dive, Done the Safe Way</h2>
  <div class="article-content">
    <p>
      You don’t need a license to discover how it feels to breathe underwater. Our Try Diving program
      is built for absolute beginners who want to feel weightless, see coral, and meet reef fish – without
      committing to a full course yet.
    </p>
  </div>
  <div class="accordion">
    <button class="accordion-button" id="accordionButton">
      <span>How does Try Diving work?</span>
      <span class="accordion-icon">▼</span>
    </button>
    <div class="accordion-content" id="accordionContent">
      <div class="accordion-text">
        <p>
          In the morning, your instructor explains the basics on land, helps you with your gear, and guides
          you into shallow water to practice a few simple skills. Once you feel comfortable, we slowly move
          into deeper water where the reef begins – always within reach of the surface and your instructor.
        </p>
        <p>
          We keep groups small, move at your pace, and choose the right site based on conditions and your comfort.
          Many guests finish the day saying: “I didn’t know I could do this.”
        </p>
      </div>
    </div>
  </div>
</section>

<section class="cards-section">
  <h2 class="section-title" id="locationsTitle">Choose Your Try Diving Location</h2>
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
      Thank you! Your Try Diving request has been sent. We will get back to you shortly.
    </div>

    <p class="offcanvas-description" id="offcanvasDescription"></p>

    <div class="map-section">
      <h4 class="map-title">Location</h4>
      <iframe class="location-map" id="locationMap" src="" loading="lazy"></iframe>
      <a id="openInMapsBtn" class="open-map-link" target="_blank" rel="noopener">
        <span>Open in Google Maps</span>
      </a>
    </div>

    <div class="price-section">
      <div class="price-label">From price per person</div>
      <div class="price-main">
        <span class="price-original" id="originalPrice"></span>
        <span class="price-amount" id="offcanvasPrice"></span>
        <span class="price-discount" id="discountBadge">20% OFF</span>
      </div>
      <div class="countdown-section">
        <div class="countdown-label" id="countdownLabel">⏰ Limited time deal</div>
        <div class="countdown-timer">
          <div class="countdown-item">
            <span class="countdown-number" id="hoursLeft">00</span>
            <span class="countdown-unit">Hours</span>
          </div>
          <div class="countdown-item">
            <span class="countdown-number" id="minutesLeft">00</span>
            <span class="countdown-unit">Minutes</span>
          </div>
          <div class="countdown-item">
            <span class="countdown-number" id="secondsLeft">00</span>
            <span class="countdown-unit">Seconds</span>
          </div>
        </div>
      </div>
    </div>

    <div class="include-exclude-section">
      <div class="accordion" style="margin-bottom:.7rem;">
        <button class="accordion-button" id="includeButton">
          <span>✓ What’s included</span><span class="accordion-icon">▼</span>
        </button>
        <div class="accordion-content" id="includeContent">
          <div class="accordion-text">
            <ul style="list-style:none;padding:0;margin:0;">
              <li>🚐 Hotel pickup &amp; drop-off (selected areas)</li>
              <li>🤿 Full diving equipment (BCD, regulator, wetsuit, fins, mask)</li>
              <li>🧑‍🏫 1:1 or small-group instruction &amp; briefing</li>
              <li>🥗 Lunch, drinking water &amp; coffee/tea</li>
              <li>🛡️ Insurance &amp; all local fees</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="accordion">
        <button class="accordion-button" id="excludeButton">
          <span>✗ Not included</span><span class="accordion-icon">▼</span>
        </button>
        <div class="accordion-content" id="excludeContent">
          <div class="accordion-text">
            <ul style="list-style:none;padding:0;margin:0;">
              <li>👙 Personal swimwear &amp; towel</li>
              <li>💰 Personal expenses &amp; tips (optional)</li>
              <li>📸 Private photographer (available on request)</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- CROSS SELL -->
    <div class="mt-6 pt-4 border-t border-slate-200">
      <p class="text-sm font-semibold text-slate-700 mb-2">
        Other divers also loved:
      </p>
      <div class="flex flex-wrap gap-2 text-sm">
        <a href="/snorkeling" class="px-3 py-1 rounded-full border border-sky-200 text-sky-700 bg-sky-50 hover:bg-sky-100">Snorkeling Trips</a>
        <a href="/fun-diving" class="px-3 py-1 rounded-full border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100">Fun Diving</a>
        <a href="/padi-courses" class="px-3 py-1 rounded-full border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100">PADI Courses</a>
        <a href="/special-packages" class="px-3 py-1 rounded-full border border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100">Special Packages</a>
      </div>
    </div>

    <form id="bookingForm" class="mt-4">
      <div class="form-group">
        <label class="form-label">Preferred date</label>
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

      <div class="form-group hidden" id="emailGroup">
        <label for="emailInput" class="form-label">Email address</label>
        <input type="email" id="emailInput" class="form-input" placeholder="you@example.com" required>
      </div>

      <button type="submit" class="checkout-button" id="checkoutButton">Check availability</button>
    </form>
  </div>
</div>

<script>
  const locations = <?=json_encode($locations, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
  const usdToIdr  = <?= (int)$USD_TO_IDR; ?>;
  const activityKey = 'try_diving';

  const heroImages = [
    "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg"
  ];

  const defaultConfig = {
    hero_title: "Try Diving in Bali",
    hero_subtitle: "Your first breaths underwater with a patient instructor by your side.",
    article_title: "Your First Dive, Done the Safe Way",
    locations_title: "Choose Your Try Diving Location"
  };

  let currentImageIndex = 0;
  let currentLocation   = null;
  let countdownInterval = null;

  function rotateHeroBackground(){
    const heroBackground = document.getElementById('heroBackground');
    heroBackground.style.backgroundImage = `url('${heroImages[currentImageIndex]}')`;
    currentImageIndex = (currentImageIndex + 1) % heroImages.length;
  }

  function mapEmbedByLatLng(lat,lng,zoom){
    return `https://www.google.com/maps?q=${lat},${lng}&hl=en&z=${zoom}&output=embed`;
  }
  function mapEmbedByQuery(q,zoom){
    return `https://www.google.com/maps?q=${encodeURIComponent(q)}&hl=en&z=${zoom}&output=embed`;
  }
  function getMapSrc(loc){
    if(typeof loc.lat === 'number' && typeof loc.lng === 'number'){
      return mapEmbedByLatLng(loc.lat,loc.lng,loc.zoom || 12);
    }
    const q = loc.query || loc.name || 'Bali';
    return mapEmbedByQuery(q,loc.zoom || 12);
  }
  function getMapLink(loc){
    if(typeof loc.lat === 'number' && typeof loc.lng === 'number'){
      return `https://www.google.com/maps/search/?api=1&query=${loc.lat},${loc.lng}`;
    }
    const q = loc.query || loc.name || 'Bali';
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}`;
  }

  function getCurrentPricing(basePrice){
    const now = new Date();
    const wita = new Date(now.getTime() + 8*60*60*1000);
    const hour = wita.getUTCHours();
    const isFlash = hour >= 13 && hour < 15;

    if(isFlash){
      const disc = Math.round(basePrice * 0.95);
      return {current:disc, original:basePrice, discount:5, flash:true};
    }else{
      const disc = Math.round(basePrice * 0.98);
      return {current:disc, original:basePrice, discount:2, flash:false};
    }
  }

  function getNextFlashSaleTime(){
    const now = new Date();
    const wita = new Date(now.getTime() + 8*60*60*1000);
    const h = wita.getUTCHours();

    if(h >= 13 && h < 15){
      const end = new Date(wita);
      end.setUTCHours(15,0,0,0);
      return end;
    }
    if(h < 13){
      const next = new Date(wita);
      next.setUTCHours(13,0,0,0);
      return next;
    }
    return null;
  }

  function formatUsdIdr(usd){
    const rupiah = Math.round(usd * usdToIdr);
    return {
      usdLabel: `$${usd.toFixed(0)}`,
      idrLabel: `IDR ${rupiah.toLocaleString('id-ID')}`
    };
  }

  function updatePricing(){
    if(!currentLocation) return;
    const p = getCurrentPricing(currentLocation.basePrice || 0);
    const labels = formatUsdIdr(p.current);
    const origLabels = formatUsdIdr(p.original);

    document.getElementById('offcanvasPrice').textContent =
      `${labels.usdLabel} · ${labels.idrLabel}`;
    document.getElementById('originalPrice').textContent =
      p.discount ? `${origLabels.usdLabel}` : '';

    const badge = document.getElementById('discountBadge');
    if(p.flash){
      badge.textContent = `⚡ ${p.discount}% FLASH SALE`;
      badge.style.background = '#f97316';
    }else{
      badge.textContent = `${p.discount}% OFF`;
      badge.style.background = '#e11d48';
    }

    startCountdown(p.flash);
  }

  function startCountdown(isFlash){
    if(countdownInterval){
      clearInterval(countdownInterval);
      countdownInterval = null;
    }
    const label = document.getElementById('countdownLabel');
    const target = getNextFlashSaleTime();

    if(!target){
      label.textContent = '⏰ Next flash sale opens daily at 13:00 WITA';
      return;
    }

    label.textContent = isFlash
      ? '⚡ Flash sale ends in:'
      : '⏰ Next flash sale starts in:';

    function tick(){
      const now = new Date();
      const diff = target.getTime() - (now.getTime() + 8*60*60*1000 - now.getTime());
      if(diff <= 0){
        updatePricing();
        return;
      }
      const totalSec = Math.floor(diff/1000);
      const h = Math.floor(totalSec / 3600);
      const m = Math.floor((totalSec % 3600) / 60);
      const s = totalSec % 60;

      document.getElementById('hoursLeft').textContent   = String(h).padStart(2,'0');
      document.getElementById('minutesLeft').textContent = String(m).padStart(2,'0');
      document.getElementById('secondsLeft').textContent = String(s).padStart(2,'0');
    }
    tick();
    countdownInterval = setInterval(tick,1000);
  }

  function renderCards(){
    const grid = document.getElementById('cardsGrid');
    grid.innerHTML = locations.map(loc => `
      <div class="card" data-id="${loc.id}">
        <div class="card-header">
          <img class="card-image" src="${loc.image}" alt="${loc.name}">
          <div class="duration-badge">
            <span><i class="fa-regular fa-clock"></i></span>
            <span>${loc.duration}</span>
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
      btn.addEventListener('click',e=>{
        e.stopPropagation();
        const id = parseInt(card.closest('.card').dataset.id,10);
        openOffcanvas(id);
      });
    });
  }

  let currentCalendarDate = new Date();
  let selectedDateValue   = null;
  let currentAvailabilityMap = {};
  let currentSubKey = null;

  async function loadAvailabilityForMonth(subKey,year,monthIndex){
    const monthStr = `${year}-${String(monthIndex+1).padStart(2,'0')}`;
    try{
      const res = await fetch(`try-diving.php?action=availability&sub_key=${encodeURIComponent(subKey)}&month=${encodeURIComponent(monthStr)}`, {cache:'no-store'});
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
      const iso = dateObj.toISOString().split('T')[0];

      if(dateObj < today){
        el.classList.add('past');
      }else{
        let avail = true;
        if(Object.keys(currentAvailabilityMap).length > 0){
          if(currentAvailabilityMap.hasOwnProperty(iso)){
            avail = !!currentAvailabilityMap[iso];
          }
        }
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
    document.getElementById('selectedDate').value = date.toISOString().split('T')[0];

    document.getElementById('emailGroup').classList.remove('hidden');
    const btn = document.getElementById('checkoutButton');
    btn.classList.add('show');
    btn.textContent = 'Check & send request';
  }

  function initCalendar(){
    document.getElementById('prevMonth').addEventListener('click',()=>{
      currentCalendarDate.setMonth(currentCalendarDate.getMonth()-1);
      if(currentSubKey){
        loadAvailabilityForMonth(currentSubKey,currentCalendarDate.getFullYear(),currentCalendarDate.getMonth());
      }else{
        renderCalendar();
      }
    });
    document.getElementById('nextMonth').addEventListener('click',()=>{
      currentCalendarDate.setMonth(currentCalendarDate.getMonth()+1);
      if(currentSubKey){
        loadAvailabilityForMonth(currentSubKey,currentCalendarDate.getFullYear(),currentCalendarDate.getMonth());
      }else{
        renderCalendar();
      }
    });
    renderCalendar();
  }

  const offcanvasOverlay = document.getElementById('offcanvasOverlay');
  const offcanvas        = document.getElementById('offcanvas');

  function openOffcanvas(id){
    currentLocation = locations.find(l => l.id === id);
    if(!currentLocation) return;

    currentSubKey = currentLocation.key || null;
    currentCalendarDate = new Date();
    selectedDateValue   = null;
    document.getElementById('selectedDate').value = '';

    document.getElementById('offcanvasTitle').textContent = currentLocation.name;
    document.getElementById('offcanvasCover').src = currentLocation.image;
    document.getElementById('offcanvasDescription').textContent = currentLocation.description;

    const mapSrc = getMapSrc(currentLocation);
    document.getElementById('locationMap').src = mapSrc;
    document.getElementById('openInMapsBtn').href = getMapLink(currentLocation);

    document.getElementById('successMessage').classList.remove('show');
    document.getElementById('bookingForm').reset();
    document.getElementById('emailGroup').classList.add('hidden');
    const btn = document.getElementById('checkoutButton');
    btn.classList.remove('show');
    btn.textContent = 'Check availability';

    updatePricing();

    offcanvasOverlay.classList.add('active');
    offcanvas.classList.add('active');

    if(currentSubKey){
      loadAvailabilityForMonth(currentSubKey,currentCalendarDate.getFullYear(),currentCalendarDate.getMonth());
    }else{
      renderCalendar();
    }
  }

  function closeOffcanvas(){
    offcanvasOverlay.classList.remove('active');
    offcanvas.classList.remove('active');
    if(countdownInterval){
      clearInterval(countdownInterval);
      countdownInterval = null;
    }
  }

  document.getElementById('offcanvasClose').addEventListener('click',closeOffcanvas);
  offcanvasOverlay.addEventListener('click',closeOffcanvas);

  document.getElementById('bookingForm').addEventListener('submit',async (e)=>{
    e.preventDefault();
    if(!currentLocation) return;

    const email = document.getElementById('emailInput').value.trim();
    const date  = document.getElementById('selectedDate').value;

    if(!email || !date) return;

    const btn = document.getElementById('checkoutButton');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    const payload = {
      type: 'try_diving_request',
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
      console.log('Booking mock payload:',payload);
      ok = true;
    }

    if(ok){
      document.getElementById('successMessage').classList.add('show');
      document.getElementById('bookingForm').reset();
      document.getElementById('emailGroup').classList.add('hidden');
      btn.classList.remove('show');
      setTimeout(()=>document.getElementById('successMessage').classList.remove('show'),5000);
    }else{
      alert('Failed to send request. Please try again or contact us via WhatsApp.');
    }

    btn.disabled = false;
    btn.textContent = 'Check & send request';
  });

  function initAccordions(){
    const mainBtn = document.getElementById('accordionButton');
    const mainContent = document.getElementById('accordionContent');
    mainBtn.addEventListener('click',()=>{
      const active = mainContent.classList.toggle('active');
      mainBtn.classList.toggle('active',active);
    });

    const includeBtn = document.getElementById('includeButton');
    const includeContent = document.getElementById('includeContent');
    includeBtn.addEventListener('click',()=>{
      const active = includeContent.classList.toggle('active');
      includeBtn.classList.toggle('active',active);
    });

    const excludeBtn = document.getElementById('excludeButton');
    const excludeContent = document.getElementById('excludeContent');
    excludeBtn.addEventListener('click',()=>{
      const active = excludeContent.classList.toggle('active');
      excludeBtn.classList.toggle('active',active);
    });
  }

  (function init(){
    rotateHeroBackground();
    setInterval(rotateHeroBackground,2500);
    renderCards();
    initCalendar();
    initAccordions();
  })();
</script>

<?php include('template/footer-product.php')?>
</body>
</html>
