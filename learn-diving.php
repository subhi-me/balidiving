<?php
// padi-courses.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

include('../template/database/main.php');

$activityKey = 'padi_courses';

/* ---------- API: AVAILABILITY PER COURSE LEVEL ---------- */
/*
   padi-courses.php?action=availability&sub_key=open_water&month=2025-11
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

/* ---------- PADI COURSES (LEVELS AS "LOCATIONS") ---------- */

$staticCourses = [
    'open_water' => [
        'id'       => 1,
        'key'      => 'open_water',
        'name'     => 'PADI Open Water Diver',
        'summary'  => 'Your first full certification – learn the basics and dive to 18m worldwide.',
        'description' => '',
        'duration' => '3–4 days',
        'basePrice'=> 430,
        'image'    => 'https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg'
    ],
    'advanced' => [
        'id'       => 2,
        'key'      => 'advanced',
        'name'     => 'PADI Advanced Open Water',
        'summary'  => 'Build confidence, try deep and navigation dives, and explore more of each site.',
        'description' => '',
        'duration' => '2–3 days',
        'basePrice'=> 390,
        'image'    => 'https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg'
    ],
    'rescue' => [
        'id'       => 3,
        'key'      => 'rescue',
        'name'     => 'PADI Rescue Diver',
        'summary'  => 'Learn to prevent problems, manage stress, and assist other divers in real scenarios.',
        'description' => '',
        'duration' => '3 days',
        'basePrice'=> 420,
        'image'    => 'https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg'
    ],
    'divemaster' => [
        'id'       => 4,
        'key'      => 'divemaster',
        'name'     => 'PADI Divemaster Internship',
        'summary'  => 'Go pro in Bali: weeks of diving, assisting, and learning how operations really run.',
        'description' => '',
        'duration' => 'Minimum 4–6 weeks',
        'basePrice'=> 1500,
        'image'    => 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg'
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

$courses = [];
foreach($staticCourses as $key => $base){
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
        $loc['image'] = 'https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg';
    }

    $courses[] = $loc;
}
if(!$courses){
    $courses = array_values($staticCourses);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php 
    require_once 'template/seo_manager.php';
    echo generate_seo_tags('learn-diving'); 
  ?>

  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>
  <?php include('template/style-product.php')?>
</head>
<body>
<header class="hero">
  <div class="hero-background" id="heroBackground"></div>
  <div class="hero-overlay">
    <div class="hero-badge">
      <span>Beginner to Pro</span> · <span>PADI 5★ Style Training</span>
    </div>
    <h1 class="hero-title" id="heroTitle">PADI Courses in Bali</h1>
    <p class="hero-subtitle" id="heroSubtitle">
      Turn “I’d love to try” into “I am a diver” – from your first course up to professional level.
    </p>
  </div>
</header>
<?php include('template/nav-product.php')?>

<section class="article-section">
  <h2 class="article-title" id="articleTitle">Build Your Diving Skills Step by Step</h2>
  <div class="article-content">
    <p>
      Training in Bali means warm water, great visibility, and plenty of chances to repeat skills
      until they feel natural. We slow things down when needed, challenge you when you’re ready,
      and keep safety at the center of every dive.
    </p>
  </div>
  <div class="accordion">
    <button class="accordion-button" id="accordionButton">
      <span>Which PADI course should I choose?</span>
      <span class="accordion-icon">▼</span>
    </button>
    <div class="accordion-content" id="accordionContent">
      <div class="accordion-text">
        <p>
          If you have never dived before, Open Water is your first full certification. Already certified?
          Advanced builds your confidence, Rescue builds your awareness, and Divemaster starts your
          professional journey. You can always message us and we’ll help you decide.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="cards-section">
  <h2 class="section-title" id="locationsTitle">Choose Your Next PADI Course</h2>
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
      Thank you! Your course enquiry has been sent. We will get back to you shortly.
    </div>

    <p class="offcanvas-description" id="offcanvasDescription"></p>

    <div class="price-section">
      <div class="price-label">Course price (from)</div>
      <div class="price-main">
        <span class="price-amount" id="offcanvasPrice"></span>
      </div>
      <div class="text-xs text-slate-500 mt-1">
        Prices in USD with IDR preview using today’s internal rate. Materials &amp; certification fees included unless stated.
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
        <a href="/special-packages" class="px-3 py-1 rounded-full border border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100">Special Packages</a>
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
        <label for="experienceInput" class="form-label">Your current level / last dives</label>
        <textarea id="experienceInput" class="form-input" rows="3" placeholder="Tell us your current certification and roughly how many dives you have."></textarea>
      </div>

      <div class="form-group hidden" id="emailGroup">
        <label for="emailInput" class="form-label">Email address</label>
        <input type="email" id="emailInput" class="form-input" placeholder="you@example.com" required>
      </div>

      <button type="submit" class="checkout-button" id="checkoutButton">Check schedule &amp; price</button>
    </form>
  </div>
</div>

<script>
  const courses = <?=json_encode($courses, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
  const usdToIdr  = <?= (int)$USD_TO_IDR; ?>;
  const activityKey = 'padi_courses';

  const heroImages = [
    "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg"
  ];

  let currentImageIndex = 0;
  let currentCourse   = null;

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
    if(!currentCourse) return;
    const base = currentCourse.basePrice || 0;
    const labels = formatUsdIdr(base);
    document.getElementById('offcanvasPrice').textContent =
      `${labels.usdLabel} · ${labels.idrLabel}`;
  }

  function renderCards(){
    const grid = document.getElementById('cardsGrid');
    grid.innerHTML = courses.map(c => `
      <div class="card" data-id="${c.id}">
        <div class="card-header">
          <img class="card-image" src="${c.image}" alt="${c.name}">
          <div class="duration-badge">
            <span><i class="fa-regular fa-clock"></i></span>
            <span>${c.duration}</span>
          </div>
        </div>
        <div class="card-content">
          <h3 class="card-title">${c.name}</h3>
          <p class="card-summary">${c.summary}</p>
          <button class="card-select-btn">See course details</button>
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
      const res = await fetch(`padi-courses.php?action=availability&sub_key=${encodeURIComponent(subKey)}&month=${encodeURIComponent(monthStr)}`, {cache:'no-store'});
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
    btn.textContent = 'Check schedule & send enquiry';
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
    currentCourse = courses.find(l => l.id === id);
    if(!currentCourse) return;

    currentSubKey = currentCourse.key || null;
    currentCalendarDate = new Date();
    selectedDateValue   = null;
    document.getElementById('selectedDate').value = '';

    document.getElementById('offcanvasTitle').textContent = currentCourse.name;
    document.getElementById('offcanvasCover').src = currentCourse.image;
    document.getElementById('offcanvasDescription').textContent = currentCourse.description;

    document.getElementById('successMessage').classList.remove('show');
    document.getElementById('bookingForm').reset();
    document.getElementById('emailGroup').classList.add('hidden');
    const btn = document.getElementById('checkoutButton');
    btn.classList.remove('show');
    btn.textContent = 'Check schedule & price';

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
  }

  document.getElementById('offcanvasClose').addEventListener('click',closeOffcanvas);
  offcanvasOverlay.addEventListener('click',closeOffcanvas);

  document.getElementById('bookingForm').addEventListener('submit',async (e)=>{
    e.preventDefault();
    if(!currentCourse) return;

    const email = document.getElementById('emailInput').value.trim();
    const date  = document.getElementById('selectedDate').value;
    const exp   = document.getElementById('experienceInput').value.trim();

    if(!email || !date) return;

    const btn = document.getElementById('checkoutButton');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    const payload = {
      type: 'padi_course_request',
      activity: activityKey,
      courseKey: currentCourse.key,
      courseName: currentCourse.name,
      email,
      date,
      experience: exp,
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
      console.log('Course enquiry mock payload:',payload);
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
    btn.textContent = 'Check schedule & price';
  });

  function initAccordions(){
    const mainBtn = document.getElementById('accordionButton');
    const mainContent = document.getElementById('accordionContent');
    mainBtn.addEventListener('click',()=>{
      const active = mainContent.classList.toggle('active');
      mainBtn.classList.toggle('active',active);
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
