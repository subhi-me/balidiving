<?php
// snorkeling.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

include('../template/database/main.php'); // harus menyediakan $pdo, $USD_TO_IDR, $WEEKLY_DEFAULTS, $GLOBAL_TEMPLATE, weekday_key(), json_headers()

// Fallback kalau tidak ada $USD_TO_IDR
if (!isset($USD_TO_IDR) || !is_numeric($USD_TO_IDR)) {
    $USD_TO_IDR = 16000;
}

/* ---------- Helper JSON headers (fallback) ---------- */
if (!function_exists('json_headers')) {
    function json_headers(): void {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}

/* ---------- Helper weekday_key (fallback) ---------- */
if (!function_exists('weekday_key')) {
    function weekday_key(DateTime $dt): string {
        $map = ['sun','mon','tue','wed','thu','fri','sat'];
        return $map[(int)$dt->format('w')];
    }
}

/* ---------- Phone normalizer & masker ---------- */
function normalize_phone(string $phone): string {
    $phone = trim($phone);
    if ($phone === '') return '';

    // hapus spasi, strip dll, sisakan angka dan '+'
    $phone = preg_replace('~[^0-9\+]~', '', $phone);
    if ($phone === '') return '';

    // jika sudah mulai dengan +
    if ($phone[0] === '+') {
        return $phone;
    }

    // mulai dengan 62 -> jadikan +62
    if (strpos($phone, '62') === 0) {
        return '+'.$phone;
    }

    // mulai dengan 0 -> anggap Indonesia -> +62
    if ($phone[0] === '0') {
        return '+62'.substr($phone, 1);
    }

    // default: tambahkan +
    return '+'.$phone;
}

function mask_phone_for_output(string $phone): string {
    $len = strlen($phone);
    if ($len <= 4) return $phone;
    return str_repeat('•', max(0, $len - 4)) . substr($phone, -4);
}

/* =========================================================
   API ROUTES
   --------------------------------------------------------- */
$action = $_GET['action'] ?? '';

/* ---------- API: AVAILABILITY PER DIVE SITE ---------- */
/*
   snorkeling.php?action=availability&sub_key=padang_bai&month=2025-11
*/
if ($action === 'availability') {
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

    // Ambil semua snapshot dalam bulan tsb
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

        // default: weekly_defaults
        $avail = $WEEKLY_DEFAULTS['snorkeling'][$wd] ?? true;

        if(isset($rows[$dStr]) && is_array($rows[$dStr])){
            $p = $rows[$dStr];

            if(isset($p['subs']['snorkeling'][$subKey])){
                $avail = (bool)$p['subs']['snorkeling'][$subKey];
            } elseif(isset($p['svc']['snorkeling'])) {
                $avail = (bool)$p['svc']['snorkeling'];
            }
        }

        $result[$dStr] = $avail;
        $cursor->modify('+1 day');
    }

    echo json_encode(['ok'=>true,'dates'=>$result], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- API: CHECK EMAIL DI TABEL leads ---------- */
/*
   POST snorkeling.php?action=check_email
   body: { "email": "guest@example.com" }

   - Cari di tabel leads (email = ? order by created_at desc)
   - Kalau ketemu -> kirim data untuk prefill
   - Kalau tidak -> kirim template kosong (tapi email diisi)
*/
if ($action === 'check_email' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_headers();
    $in = json_decode(file_get_contents('php://input'), true) ?: [];
    $email = trim($in['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok'=>false,'error'=>'invalid_email']);
        exit;
    }

    $hasPin = true;
    try {
        $st = $pdo->prepare("
            SELECT id, name, email, phone, country, pax, customer_pin
            FROM leads
            WHERE email = :email
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $st->execute([':email'=>$email]);
    } catch (Throwable $e) {
        // fallback kalau kolom customer_pin tidak ada
        $hasPin = false;
        $st = $pdo->prepare("
            SELECT id, name, email, phone, country, pax
            FROM leads
            WHERE email = :email
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $st->execute([':email'=>$email]);
    }

    $row = $st->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $storedPhone = $row['phone'] ?? '';
        $normPhone   = $storedPhone ? normalize_phone($storedPhone) : '';
        $masked      = $normPhone ? mask_phone_for_output($normPhone) : '';

        $lead = [
            'id'           => $row['id'],
            'name'         => $row['name'] ?? '',
            'email'        => $row['email'] ?? $email,
            'phone'        => $storedPhone,
            'country'      => $row['country'] ?? '',
            'participants' => (int)($row['pax'] ?? 2),
        ];

        echo json_encode([
            'ok'           => true,
            'found'        => true,
            'lead'         => $lead,
            'has_pin'      => $hasPin && !empty($row['customer_pin'] ?? ''),
            'masked_phone' => $masked
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        exit;
    }

    // tidak ketemu -> template kosong
    echo json_encode([
        'ok'    => true,
        'found' => false,
        'lead'  => [
            'id'           => null,
            'name'         => '',
            'email'        => $email,
            'phone'        => '',
            'country'      => '',
            'participants' => 2
        ],
        'has_pin'      => false,
        'masked_phone' => ''
    ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- API: VERIFY IDENTITY (phone / pin) ---------- */
/*
   POST snorkeling.php?action=verify_identity
   body: { "email": "...", "secret": "phoneOrPin" }

   - Kalau email ada di leads:
       - normalisasi phone input & phone DB
       - kalau sama -> verified = true
       - (opsional pin kalau ada kolom customer_pin)
*/
if ($action === 'verify_identity' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_headers();
    $in = json_decode(file_get_contents('php://input'), true) ?: [];
    $email  = trim($in['email']  ?? '');
    $secret = trim($in['secret'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $secret === '') {
        echo json_encode(['ok'=>false,'error'=>'bad_params']);
        exit;
    }

    $hasPin = true;
    try {
        $st = $pdo->prepare("
            SELECT phone, customer_pin
            FROM leads
            WHERE email = :email
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $st->execute([':email'=>$email]);
    } catch (Throwable $e) {
        $hasPin = false;
        $st = $pdo->prepare("
            SELECT phone
            FROM leads
            WHERE email = :email
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $st->execute([':email'=>$email]);
    }

    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['ok'=>false,'error'=>'lead_not_found']);
        exit;
    }

    $normInput = normalize_phone($secret);
    $normStoredPhone = normalize_phone($row['phone'] ?? '');

    $phoneMatch = $normStoredPhone && $normInput && $normInput === $normStoredPhone;

    $pinMatch = false;
    if ($hasPin && isset($row['customer_pin']) && $row['customer_pin'] !== null && $row['customer_pin'] !== '') {
        // kalau user isi secret bukan nomor telp standar, tetap boleh cocok dengan pin mentah
        if ($secret === $row['customer_pin']) {
            $pinMatch = true;
        }
    }

    if ($phoneMatch || $pinMatch) {
        echo json_encode(['ok'=>true,'verified'=>true]);
    } else {
        echo json_encode(['ok'=>true,'verified'=>false,'error'=>'mismatch']);
    }
    exit;
}

/* =========================================================
   BUILD SNORKELING LOCATIONS
   --------------------------------------------------------- */
/*
   Static base (id/key/name/duration/map) + override dari booking_catalog & global_template:
   - booking_catalog (activity_key='snorkeling', sub_key=...) → short_desc, long_desc, images
   - GLOBAL_TEMPLATE['prices']['snorkeling'][sub_key]['usd'] → basePrice
*/

$staticLocations = [
    'padang_bai' => [
        'id'       => 1,
        'key'      => 'padang_bai',
        'name'     => 'Padang Bai',
        'summary'  => 'Sheltered bay with calm waters and bright coral gardens – perfect for beginners.',
        'description' => '',
        'duration' => '7 hours',
        'basePrice'=> 47,
        'lat'      => -8.512345,
        'lng'      => 115.512345,
        'query'    => 'Padang Bai, Karangasem, Bali',
        'zoom'     => 13
    ],
    'tulamben' => [
        'id'       => 2,
        'key'      => 'tulamben',
        'name'     => 'Tulamben Wreck',
        'summary'  => 'Home of the famous USAT Liberty shipwreck with easy shore access.',
        'description' => '',
        'duration' => '9 hours',
        'basePrice'=> 58,
        'lat'      => -8.276543,
        'lng'      => 115.598765,
        'query'    => 'Tulamben, Kubu, Karangasem, Bali',
        'zoom'     => 13
    ],
    'amed' => [
        'id'       => 3,
        'key'      => 'amed',
        'name'     => 'Amed',
        'summary'  => 'Black sand bays, clear water, and long shallow reefs full of life.',
        'description' => '',
        'duration' => '9 hours',
        'basePrice'=> 53,
        'lat'      => -8.345679,
        'lng'      => 115.654321,
        'query'    => 'Amed, Abang, Karangasem, Bali',
        'zoom'     => 13
    ],
    'npmp' => [
        'id'       => 4,
        'key'      => 'npmp',
        'name'     => 'Nusa Penida Marine Park',
        'summary'  => 'Crystal bays, dramatic cliffs, and a chance to meet manta rays.',
        'description' => '',
        'duration' => '8 hours',
        'basePrice'=> 79,
        'lat'      => -8.7234,
        'lng'      => 115.4567,
        'query'    => 'Nusa Penida Marine Park, Bali',
        'zoom'     => 12
    ],
];

/* Ambil catalog snorkeling dari booking_catalog */
$catalogMap = [];
try{
    $st = $pdo->prepare("SELECT sub_key, short_desc, long_desc, images FROM booking_catalog WHERE activity_key='snorkeling'");
    $st->execute();
    while($row=$st->fetch()){
        $catalogMap[$row['sub_key']] = [
            'short_desc' => $row['short_desc'] ?? '',
            'long_desc'  => $row['long_desc'] ?? '',
            'images'     => $row['images'] ? json_decode($row['images'], true) : null
        ];
    }
}catch(Throwable $e){
    // kalau error, ya sudah, pakai static saja
}

/* Base price dari GLOBAL_TEMPLATE kalau tersedia */
$globalPrices = [];
if(is_array($GLOBAL_TEMPLATE) && isset($GLOBAL_TEMPLATE['prices']['snorkeling'])){
    $globalPrices = $GLOBAL_TEMPLATE['prices']['snorkeling'];
}

/* Build final $locations */
$locations = [];
foreach($staticLocations as $key => $base){
    $loc = $base;

    // override harga dari global_template
    if(isset($globalPrices[$key]['usd']) && is_numeric($globalPrices[$key]['usd'])){
        $loc['basePrice'] = (float)$globalPrices[$key]['usd'];
    }

    // override desc & images dari booking_catalog
    if(isset($catalogMap[$key])){
        $cat = $catalogMap[$key];
        if(!empty($cat['short_desc'])) $loc['summary'] = $cat['short_desc'];
        if(!empty($cat['long_desc']))  $loc['description'] = $cat['long_desc'];
        if(is_array($cat['images']) && !empty($cat['images'][0])){
            $loc['image'] = $cat['images'][0];
        }
    }

    if(empty($loc['description'])){
        $loc['description'] = $loc['summary'];
    }

    if(empty($loc['image'])){
        $loc['image'] = 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg';
    }

    $locations[] = $loc;
}

/* Fallback kalau entah kenapa kosong */
if(!$locations){
    $locations = array_values($staticLocations);
}

/* ---------- Global add-ons dari booking_globals.global_template.addons ---------- */
$globalAddons = [];
try {
    $st = $pdo->query("SELECT global_template FROM booking_globals WHERE id=1");
    if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $tpl = $row['global_template'] ? json_decode($row['global_template'], true) : null;
        if (is_array($tpl) && isset($tpl['addons']) && is_array($tpl['addons'])) {
            foreach ($tpl['addons'] as $ad) {
                if (empty($ad['name'])) continue;
                if (isset($ad['available']) && $ad['available'] === false) continue;
                $globalAddons[] = [
                    'name' => $ad['name'],
                    'usd'  => isset($ad['usd']) ? (float)$ad['usd'] : 0
                ];
            }
        }
    }
} catch (Throwable $e) {
    $globalAddons = [];
}

$addonsJson = json_encode($globalAddons, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Snorkeling in Bali · Bali Diving</title>

  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>

  <?php include('template/style-product.php')?>
</head>
<body>
<header class="hero">
  <div class="hero-background" id="heroBackground"></div>
  <div class="hero-overlay">
    <div class="hero-badge">
      <span>25+ Years of Safe Diving</span> · <span>Family-run in Bali</span>
    </div>
    <h1 class="hero-title" id="heroTitle">Discover Bali's Snorkeling Paradise</h1>
    <p class="hero-subtitle" id="heroSubtitle">
      Crystal-clear water, warm currents, and reefs full of life – no experience needed.
    </p>
  </div>
</header>
<?php include('template/nav-product.php')?>

<section class="article-section">
  <h2 class="article-title" id="articleTitle">Snorkeling in Bali with Bali Diving</h2>
  <div class="article-content">
    <p>
      Bali’s shoreline hides a second island made of coral and light. Just a few fin kicks from the beach,
      you’ll find clouds of reef fish, turtles cruising in slow motion, and sunlight breaking into thousands
      of dancing rays. Our team has been taking guests into these waters safely for more than two decades.
    </p>
  </div>
  <div class="accordion">
    <button class="accordion-button" id="accordionButton">
      <span>Read more about snorkeling in Bali</span>
      <span class="accordion-icon">▼</span>
    </button>
    <div class="accordion-content" id="accordionContent">
      <div class="accordion-text">
        <p>
          From calm bays in the east to dramatic cliffs around Nusa Penida, each site has its own character.
          Some are perfect for first-timers who just want to float and breathe through a snorkel, others feel
          like swimming along a living wall of color. Our guides stay close, watch currents, and keep the pace
          relaxed so you can really enjoy every breath and every view.
        </p>
        <p>
          We handle all the logistics – hotel transfers, gear, permits, lunch – so your only job is to show up,
          relax, and let the ocean do the rest.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="cards-section">
  <h2 class="section-title" id="locationsTitle">Choose Your Snorkeling Adventure</h2>
  <div class="cards-grid" id="cardsGrid"></div>
</section>

<section class="article-section">
  <h2 class="article-title">Other divers also looked at</h2>
  <div class="flex flex-wrap gap-2 mt-2">
    <a href="try-diving.php" class="inline-flex items-center px-3 py-2 rounded-md border border-slate-300 text-sm hover:bg-slate-100">
      Try Diving – first bubbles with instructor
    </a>
    <a href="fun-diving.php" class="inline-flex items-center px-3 py-2 rounded-md border border-slate-300 text-sm hover:bg-slate-100">
      Fun Diving – certified diver day trips
    </a>
    <a href="padi-courses.php" class="inline-flex items-center px-3 py-2 rounded-md border border-slate-300 text-sm hover:bg-slate-100">
      PADI Courses – get your certification
    </a>
    <a href="special-packages.php" class="inline-flex items-center px-3 py-2 rounded-md border border-slate-300 text-sm hover:bg-slate-100">
      Special Packages – combine &amp; save
    </a>
  </div>
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
      Thank you! Your snorkeling request has been sent. We will get back to you shortly.
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
              <li>🤿 Full snorkeling equipment (mask, fins, snorkel, life jacket)</li>
              <li>🧑‍🏫 Professional guide &amp; safety briefing</li>
              <li>🍽️ Indonesian lunch &amp; drinking water</li>
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
              <li>👙 Personal swimwear &amp; sunscreen</li>
              <li>💰 Personal expenses &amp; tips (optional)</li>
              <li>📸 Private photographer (available on request)</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <form id="bookingForm" >
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

      <div class="form-group hidden" id="detailsGroup">
        <label class="form-label">Guest details</label>
        <input type="text" id="fullNameInput" class="form-input" placeholder="Full name" required>
        <input type="text" id="phoneInput" class="form-input" placeholder="Phone / WhatsApp" required>
        <input type="text" id="countryInput" class="form-input" placeholder="Country of residence">
        <input type="number" id="paxInput" class="form-input" min="1" step="1" placeholder="Number of participants">
      </div>

      <div class="form-group hidden" id="addonsGroup">
        <label class="form-label">Add-ons (optional)</label>
        <div id="addonsList"></div>
      </div>

      <div class="form-group hidden" id="verifyGroup">
        <label class="form-label" id="verifyLabel">For your security</label>
        <input type="text" id="verifyInput" class="form-input" placeholder="Type your phone number to confirm">
        <p id="verifyHint" style="font-size:12px;color:#64748b;margin-top:4px;"></p>
      </div>

  </div>
</div>

<script>
  // ---- DATA FROM PHP ----
  const locations    = <?=json_encode($locations, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
  const usdToIdr     = <?= (int)$USD_TO_IDR; ?>;
  const globalAddons = <?=$addonsJson?>;

  const heroImages = [
    "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg"
  ];

  const defaultConfig = {
    hero_title: "Discover Bali's Snorkeling Paradise",
    hero_subtitle: "Crystal water, friendly guides, and colorful reefs – no experience needed.",
    article_title: "Snorkeling in Bali with Bali Diving",
    locations_title: "Choose Your Snorkeling Adventure"
  };

  let currentImageIndex = 0;
  let currentLocation   = null;
  let countdownInterval = null;

  let currentCalendarDate    = new Date();
  let selectedDateValue      = null;
  let currentAvailabilityMap = {};
  let currentSubKey          = null;

  let bookingPhase     = 'check'; // 'check' atau 'submit'
  let lastCheckResult  = null;

  function rotateHeroBackground(){
    const heroBackground = document.getElementById('heroBackground');
    heroBackground.style.backgroundImage = `url('${heroImages[currentImageIndex]}')`;
    currentImageIndex = (currentImageIndex + 1) % heroImages.length;
  }

  // ---- MAP HELPERS (WITHOUT API KEY) ----
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

  // ---- PRICING / FLASH-SALE LOGIC ----
  function getCurrentPricing(basePrice){
    const now = new Date();
    const wita = new Date(now.getTime() + 8*60*60*1000);
    const hour = wita.getUTCHours(); // pseudo WITA hour

    const isFlash = hour >= 13 && hour < 15; // 13:00–15:00 WITA

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
      const diff = target.getTime() - now.getTime();
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

  // ---- RENDER CARDS ----
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

  // ---- CALENDAR / AVAILABILITY ----
  async function loadAvailabilityForMonth(subKey,year,monthIndex){
    const monthStr = `${year}-${String(monthIndex+1).padStart(2,'0')}`;
    try{
      const res = await fetch(`snorkeling.php?action=availability&sub_key=${encodeURIComponent(subKey)}&month=${encodeURIComponent(monthStr)}`, {cache:'no-store'});
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

    // padding previous month
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
          if(Object.prototype.hasOwnProperty.call(currentAvailabilityMap, iso)){
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
    document.getElementById('detailsGroup').classList.add('hidden');
    document.getElementById('addonsGroup').classList.add('hidden');
    document.getElementById('verifyGroup').classList.add('hidden');

    bookingPhase = 'check';
    lastCheckResult = null;

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

  // ---- OFFCANVAS OPEN/CLOSE ----
  const offcanvasOverlay = document.getElementById('offcanvasOverlay');
  const offcanvas        = document.getElementById('offcanvas');

  function openOffcanvas(id){
    currentLocation = locations.find(l => l.id === id);
    if(!currentLocation) return;

    currentSubKey = currentLocation.key || null;
    currentCalendarDate = new Date();
    selectedDateValue   = null;
    document.getElementById('selectedDate').value = '';

    bookingPhase = 'check';
    lastCheckResult = null;

    document.getElementById('offcanvasTitle').textContent = currentLocation.name;
    document.getElementById('offcanvasCover').src = currentLocation.image;
    document.getElementById('offcanvasDescription').textContent = currentLocation.description;

    const mapSrc = getMapSrc(currentLocation);
    document.getElementById('locationMap').src = mapSrc;
    document.getElementById('openInMapsBtn').href = getMapLink(currentLocation);

    document.getElementById('successMessage').classList.remove('show');
    document.getElementById('bookingForm').reset();
    document.getElementById('emailGroup').classList.add('hidden');
    document.getElementById('detailsGroup').classList.add('hidden');
    document.getElementById('addonsGroup').classList.add('hidden');
    document.getElementById('verifyGroup').classList.add('hidden');
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

  // ---- ADD-ONS RENDER ----
  function renderAddons(){
    const wrap = document.getElementById('addonsList');
    if(!wrap) return;
    if(!globalAddons || !globalAddons.length){
      wrap.innerHTML = '<p style="font-size:13px;color:#64748b;">No add-ons available for this activity right now.</p>';
      return;
    }
    wrap.innerHTML = globalAddons.map((ad, idx)=>{
      const labels = formatUsdIdr(ad.usd || 0);
      return `
        <label class="flex items-center gap-2 mb-1 text-sm">
          <input type="checkbox" name="addonOption" value="${idx}">
          <span>${ad.name} <span class="text-xs text-slate-400">(${labels.usdLabel} · ${labels.idrLabel})</span></span>
        </label>
      `;
    }).join('');
  }

  function fillCustomerDetails(lead, found){
    document.getElementById('fullNameInput').value = lead.name || '';
    document.getElementById('phoneInput').value    = lead.phone || '';
    document.getElementById('countryInput').value  = lead.country || '';
    document.getElementById('paxInput').value      = lead.participants || 2;

    let hintEl = document.getElementById('detailsHint');
    if(!hintEl){
      hintEl = document.createElement('p');
      hintEl.id = 'detailsHint';
      hintEl.style.fontSize = '12px';
      hintEl.style.color = '#94a3b8';
      hintEl.style.marginTop = '4px';
      document.getElementById('detailsGroup').appendChild(hintEl);
    }

    hintEl.textContent = found
      ? 'We found your previous details. You can update them below if something has changed.'
      : 'This is your first time with this email. Please fill your details – next time we will prefill them for you.';
  }

  function setupVerifyUI(result){
    const group = document.getElementById('verifyGroup');
    const label = document.getElementById('verifyLabel');
    const hint  = document.getElementById('verifyHint');
    document.getElementById('verifyInput').value = '';

    if(!result.found){
      group.classList.add('hidden');
      return;
    }
    group.classList.remove('hidden');
    label.textContent = 'Confirm it is you';

    if(result.masked_phone){
      hint.textContent = `For your security, please type the phone number that ends with ${result.masked_phone} which you used before. Format is flexible: 08..., 62..., or +62...`;
    }else{
      hint.textContent = 'For your security, please type the phone number you used before. Format is flexible: 08..., 62..., or +62...';
    }
  }

  // ---- BOOKING FORM ----
  document.getElementById('bookingForm').addEventListener('submit',async (e)=>{
    e.preventDefault();
    if(!currentLocation) return;

    const email = document.getElementById('emailInput').value.trim();
    const date  = document.getElementById('selectedDate').value;

    if(!email || !date){
      alert('Please select a date and fill your email.');
      return;
    }

    const btn = document.getElementById('checkoutButton');

    // PHASE 1: CHECK EMAIL & PREFILL
    if(bookingPhase === 'check'){
      btn.disabled = true;
      btn.textContent = 'Checking...';
      try{
        const res = await fetch('snorkeling.php?action=check_email',{
          method:'POST',
          headers:{'Content-Type':'application/json'},
          body:JSON.stringify({email})
        });
        const json = await res.json();
        if(!json.ok){
          throw new Error(json.error || 'check_email failed');
        }
        lastCheckResult = json;

        fillCustomerDetails(json.lead, json.found);
        renderAddons();
        setupVerifyUI(json);

        document.getElementById('detailsGroup').classList.remove('hidden');
        document.getElementById('addonsGroup').classList.remove('hidden');
        if(json.found){
          document.getElementById('verifyGroup').classList.remove('hidden');
        }else{
          document.getElementById('verifyGroup').classList.add('hidden');
        }

        bookingPhase = 'submit';
        btn.textContent = 'Send booking request';
      }catch(err){
        console.error(err);
        alert('Failed to check your email. Please try again.');
        btn.textContent = 'Check & send request';
      }finally{
        btn.disabled = false;
      }
      return;
    }

    // PHASE 2: SUBMIT BOOKING (dengan verifikasi phone untuk existing lead)
    const name    = document.getElementById('fullNameInput').value.trim();
    const phone   = document.getElementById('phoneInput').value.trim();
    const country = document.getElementById('countryInput').value.trim();
    const pax     = parseInt(document.getElementById('paxInput').value || '1',10);
    const secret  = document.getElementById('verifyInput').value.trim();

    if(!name){
      alert('Please fill your name.');
      return;
    }
    if(!phone){
      alert('Please fill your phone / WhatsApp number.');
      return;
    }

    if(pax <= 0 || isNaN(pax)){
      alert('Please set number of participants (at least 1).');
      return;
    }

    // Kalau email sudah pernah ada -> wajib verifikasi phone/pin
    if(lastCheckResult && lastCheckResult.found){
      if(!secret){
        alert('Please type your phone number (or PIN if set) to confirm this is you.');
        return;
      }

      btn.disabled = true;
      btn.textContent = 'Verifying...';
      try{
        const res = await fetch('snorkeling.php?action=verify_identity',{
          method:'POST',
          headers:{'Content-Type':'application/json'},
          body:JSON.stringify({email, secret})
        });
        const json = await res.json();
        if(!json.ok || !json.verified){
          alert('Verification failed. Please check your phone number / PIN.');
          btn.disabled = false;
          btn.textContent = 'Send booking request';
          return;
        }
      }catch(err){
        console.error(err);
        alert('Verification error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Send booking request';
        return;
      }
    }

    // collect addons
    const selectedAddons = [];
    document.querySelectorAll('input[name="addonOption"]:checked').forEach(ch=>{
      const idx = parseInt(ch.value,10);
      if(!isNaN(idx) && globalAddons && globalAddons[idx]){
        selectedAddons.push(globalAddons[idx]);
      }
    });

    btn.disabled = true;
    btn.textContent = 'Sending...';

    const payload = {
      type: 'snorkeling_request',
      locationKey: currentLocation.key,
      locationName: currentLocation.name,
      email,
      name,
      phone,
      country,
      participants: pax,
      date,
      addons: selectedAddons,
      baseUsd: currentLocation.basePrice || 0,
      usdToIdr: usdToIdr,
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

    if(ok){
      document.getElementById('successMessage').classList.add('show');
      bookingPhase = 'check';
      lastCheckResult = null;
      document.getElementById('bookingForm').reset();
      document.getElementById('emailGroup').classList.add('hidden');
      document.getElementById('detailsGroup').classList.add('hidden');
      document.getElementById('addonsGroup').classList.add('hidden');
      document.getElementById('verifyGroup').classList.add('hidden');
      btn.textContent = 'Check & send request';
      setTimeout(()=>document.getElementById('successMessage').classList.remove('show'),5000);
    }else{
      alert('Failed to send request. Please try again or contact us via WhatsApp.');
      btn.textContent = 'Send booking request';
    }

    btn.disabled = false;
  });

  // ---- ACCORDIONS (main + include/exclude) ----
  function initAccordions(){
    const mainBtn = document.getElementById('accordionButton');
    const mainContent = document.getElementById('accordionContent');
    mainBtn.addEventListener('click',()=>{
      const active = mainContent.classList.toggle('active');
      mainBtn.classList.toggle('active',active);
      mainBtn.querySelector('span').textContent = active
        ? 'Show less'
        : 'Read more about snorkeling in Bali';
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

  // ---- ELEMENT SDK (optional styling control) ----
  async function onConfigChange(config){
    document.getElementById('heroTitle').textContent =
      config.hero_title || defaultConfig.hero_title;
    document.getElementById('heroSubtitle').textContent =
      config.hero_subtitle || defaultConfig.hero_subtitle;
    document.getElementById('articleTitle').textContent =
      config.article_title || defaultConfig.article_title;
    document.getElementById('locationsTitle').textContent =
      config.locations_title || defaultConfig.locations_title;
  }

  async function initElementSdk(){
    if(!window.elementSdk) return;
    await window.elementSdk.init({
      defaultConfig,
      onConfigChange,
      mapToCapabilities:(config)=>({
        recolorables:[],
        borderables:[],
        fontEditable:undefined,
        fontSizeable:undefined
      }),
      mapToEditPanelValues:(config)=>new Map([
        ['hero_title',config.hero_title || defaultConfig.hero_title],
        ['hero_subtitle',config.hero_subtitle || defaultConfig.hero_subtitle],
        ['article_title',config.article_title || defaultConfig.article_title],
        ['locations_title',config.locations_title || defaultConfig.locations_title]
      ])
    });
    await onConfigChange(window.elementSdk.config);
  }

  async function initDataSdk(){
    if(!window.dataSdk) return;
    try{
      const res = await window.dataSdk.init({onDataChanged(){/* no-op */}});
      if(!res.isOk) console.error('dataSdk init failed');
    }catch(e){
      console.error(e);
    }
  }

  // ---- INIT ----
  (function init(){
    rotateHeroBackground();
    setInterval(rotateHeroBackground,2500);
    renderCards();
    initCalendar();
    initElementSdk();
    initDataSdk();
  })();
</script>
<?php include('template/footer-product.php')?>

</body>
</html>
