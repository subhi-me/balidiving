<?php
// fun-diving.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

include('../template/database/main.php'); 
// main.php diharapkan define: $pdo, $USD_TO_IDR, $WEEKLY_DEFAULTS, $GLOBAL_TEMPLATE,
// plus helper json_headers(), weekday_key()
//
// SEMUA FIELD KONTEN DIAMBIL DARI DATABASE:
// - booking_catalog           : per dive site (title, short_desc, long_desc, duration, base_price_usd, included_items, excluded_items, images)
// - bd_global_settings.global_template (JSON):
//       {
//         "pages": {
//           "fun_diving": {
//             "hero_title": "...",
//             "hero_subtitle": "...",
//             "badge_left": "Certified Divers",
//             "badge_right": "25+ Years in Bali",
//             "article_title": "...",
//             "article_intro_html": "<p>...</p>",
//             "accordion_title": "...",
//             "accordion_body_html": "<p>...</p>",
//             "locations_title": "...",
//             "hero_images": ["https://...jpg","https://...jpg"],
//             "include_items": ["item 1","item 2"],
//             "exclude_items": ["item A","item B"]
//           }
//         }
//       }

$activityKey = 'fun_diving';

/* ---------- API: AVAILABILITY PER DIVE SITE ---------- */
/*
   fun-diving.php?action=availability&sub_key=padang_bai&month=2025-11
*/
if (isset($_GET['action']) && $_GET['action'] === 'availability') {
    json_headers();

    $subKey = $_GET['sub_key'] ?? '';
    $month  = $_GET['month']  ?? ''; // YYYY-MM

    if (!$subKey || !preg_match('~^\d{4}-\d{2}$~', $month)) {
        echo json_encode(['ok' => false, 'error' => 'bad_params']);
        exit;
    }

    $start   = $month . '-01';
    $startDt = DateTime::createFromFormat('Y-m-d', $start);
    if (!$startDt) {
        echo json_encode(['ok' => false, 'error' => 'bad_month']);
        exit;
    }
    $endDt = (clone $startDt)->modify('last day of this month');
    $end   = $endDt->format('Y-m-d');

    $rows = [];
    try {
        $st = $pdo->prepare("SELECT d, payload FROM booking_date_snapshots WHERE d BETWEEN :s AND :e");
        $st->execute([':s' => $start, ':e' => $end]);
        while ($r = $st->fetch()) {
            $rows[$r['d']] = $r['payload'] ? json_decode($r['payload'], true) : null;
        }
    } catch (Throwable $e) {
        echo json_encode(['ok' => false, 'error' => 'db_error']);
        exit;
    }

    $result = [];
    $cursor = clone $startDt;
    while ($cursor <= $endDt) {
        $dStr = $cursor->format('Y-m-d');
        $wd   = weekday_key($cursor);

        $avail = $WEEKLY_DEFAULTS[$activityKey][$wd] ?? true;

        if (isset($rows[$dStr]) && is_array($rows[$dStr])) {
            $p = $rows[$dStr];

            if (isset($p['subs'][$activityKey][$subKey])) {
                $avail = (bool)$p['subs'][$activityKey][$subKey];
            } elseif (isset($p['svc'][$activityKey])) {
                $avail = (bool)$p['svc'][$activityKey];
            }
        }

        $result[$dStr] = $avail;
        $cursor->modify('+1 day');
    }

    echo json_encode(['ok' => true, 'dates' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- STATIC LOCATION (HANYA UNTUK MAP / LOKASI) ---------- */
/*
   Semua teks dan data untuk offcanvas (nama, summary, description, durasi, price,
   include/exclude, image) diambil dari database.
   Di sini hanya simpan info lokasi (lat/lng/query/zoom) per sub_key.
*/
$staticLocations = [
    'padang_bai' => [
        'lat'   => -8.512345,
        'lng'   => 115.512345,
        'query' => 'Padang Bai, Karangasem, Bali',
        'zoom'  => 13,
    ],
    'tulamben' => [
        'lat'   => -8.276543,
        'lng'   => 115.598765,
        'query' => 'Tulamben, Kubu, Karangasem, Bali',
        'zoom'  => 13,
    ],
    'amed' => [
        'lat'   => -8.345679,
        'lng'   => 115.654321,
        'query' => 'Amed, Abang, Karangasem, Bali',
        'zoom'  => 13,
    ],
    'npmp' => [
        'lat'   => -8.7234,
        'lng'   => 115.4567,
        'query' => 'Nusa Penida Marine Park, Bali',
        'zoom'  => 12,
    ],
    'gili_tepekong' => [
        'lat'   => -8.54321,
        'lng'   => 115.62222,
        'query' => 'Gili Tepekong, Bali',
        'zoom'  => 12,
    ],
    'kubu' => [
        'lat'   => -8.25001,
        'lng'   => 115.60,
        'query' => 'Kubu, Karangasem, Bali',
        'zoom'  => 13,
    ],
];

/*
 * booking_catalog:
 *  - activity_key
 *  - sub_key
 *  - short_desc
 *  - long_desc
 *  - images         (JSON array URL)
 *  - title
 *  - duration
 *  - base_price_usd (FLOAT)
 *  - included_items (JSON array string)
 *  - excluded_items (JSON array string)
 */

$catalogMap = [];
try {
    $st = $pdo->prepare("
        SELECT 
            sub_key,
            short_desc,
            long_desc,
            images,
            title,
            duration,
            base_price_usd,
            included_items,
            excluded_items
        FROM booking_catalog
        WHERE activity_key = :a
    ");
    $st->execute([':a' => $activityKey]);
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $catalogMap[$row['sub_key']] = [
            'title'          => $row['title']          ?? '',
            'short_desc'     => $row['short_desc']     ?? '',
            'long_desc'      => $row['long_desc']      ?? '',
            'duration'       => $row['duration']       ?? '',
            'base_price_usd' => is_numeric($row['base_price_usd'] ?? null) ? (float)$row['base_price_usd'] : null,
            'images'         => $row['images'] ? json_decode($row['images'], true) : null,
            'included'       => $row['included_items'] ? json_decode($row['included_items'], true) : [],
            'excluded'       => $row['excluded_items'] ? json_decode($row['excluded_items'], true) : [],
        ];
    }
} catch (Throwable $e) {
    // silent
}

/* Base price dari GLOBAL_TEMPLATE kalau tersedia (opsional) */
$globalPrices = [];
if (is_array($GLOBAL_TEMPLATE) && isset($GLOBAL_TEMPLATE['prices'][$activityKey])) {
    $globalPrices = $GLOBAL_TEMPLATE['prices'][$activityKey];
}

/* ---------- PAGE CONFIG DARI bd_global_settings.global_template (JSON) ---------- */

$pageConfig = [
    'hero_title'         => '',
    'hero_subtitle'      => '',
    'badge_left'         => '',
    'badge_right'        => '',
    'article_title'      => '',
    'article_intro_html' => '',
    'accordion_title'    => '',
    'accordion_body_html'=> '',
    'locations_title'    => '',
    'hero_images'        => [],
    'include_items'      => [],
    'exclude_items'      => [],
];

if (is_array($GLOBAL_TEMPLATE)
    && isset($GLOBAL_TEMPLATE['pages'][$activityKey])
    && is_array($GLOBAL_TEMPLATE['pages'][$activityKey])
) {
    $src = $GLOBAL_TEMPLATE['pages'][$activityKey];

    foreach ($pageConfig as $key => $default) {
        if (array_key_exists($key, $src)) {
            $pageConfig[$key] = $src[$key];
        }
    }
}

// Normalisasi
$heroTitle         = (string)($pageConfig['hero_title'] ?? '');
$heroSubtitle      = (string)($pageConfig['hero_subtitle'] ?? '');
$badgeLeft         = (string)($pageConfig['badge_left'] ?? '');
$badgeRight        = (string)($pageConfig['badge_right'] ?? '');
$articleTitle      = (string)($pageConfig['article_title'] ?? '');
$articleIntroHtml  = (string)($pageConfig['article_intro_html'] ?? '');
$accordionTitle    = (string)($pageConfig['accordion_title'] ?? '');
$accordionBodyHtml = (string)($pageConfig['accordion_body_html'] ?? '');
$locationsTitle    = (string)($pageConfig['locations_title'] ?? '');

$heroImages = [];
if (isset($pageConfig['hero_images']) && is_array($pageConfig['hero_images'])) {
    $heroImages = $pageConfig['hero_images'];
}

$defaultIncludeItems = [];
if (isset($pageConfig['include_items']) && is_array($pageConfig['include_items'])) {
    $defaultIncludeItems = $pageConfig['include_items'];
}
$defaultExcludeItems = [];
if (isset($pageConfig['exclude_items']) && is_array($pageConfig['exclude_items'])) {
    $defaultExcludeItems = $pageConfig['exclude_items'];
}

/* ---------- BUILD LOCATIONS (FULL DATA UNTUK CARD + OFFCANVAS) ---------- */

$locations = [];
$idCounter = 1;

foreach ($catalogMap as $subKey => $cat) {
    $images = is_array($cat['images']) ? $cat['images'] : [];
    $firstImage = '';
    if ($images && isset($images[0]) && is_string($images[0])) {
        $firstImage = $images[0];
    }

    $loc = [
        'id'          => $idCounter++,
        'key'         => $subKey,
        'name'        => (string)($cat['title'] ?? ''),
        'summary'     => (string)($cat['short_desc'] ?? ''),
        'description' => (string)($cat['long_desc'] ?? ''),
        'duration'    => (string)($cat['duration'] ?? ''),
        'basePrice'   => $cat['base_price_usd'] !== null ? (float)$cat['base_price_usd'] : (
            isset($globalPrices[$subKey]['usd']) && is_numeric($globalPrices[$subKey]['usd'])
                ? (float)$globalPrices[$subKey]['usd']
                : null
        ),
        'image'       => $firstImage,
        'include'     => is_array($cat['included']) ? $cat['included'] : [],
        'exclude'     => is_array($cat['excluded']) ? $cat['excluded'] : [],
        'lat'         => null,
        'lng'         => null,
        'query'       => null,
        'zoom'        => null,
    ];

    if (isset($staticLocations[$subKey])) {
        $loc['lat']   = $staticLocations[$subKey]['lat'];
        $loc['lng']   = $staticLocations[$subKey]['lng'];
        $loc['query'] = $staticLocations[$subKey]['query'];
        $loc['zoom']  = $staticLocations[$subKey]['zoom'];
    }

    $locations[] = $loc;
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fun Diving in Bali · Bali Diving</title>

  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>
  <?php include('template/style-product.php')?>
</head>
<body>
<header class="hero">
  <div class="hero-background" id="heroBackground"></div>
  <div class="hero-overlay">
    <div class="hero-badge">
      <?php if ($badgeLeft !== '' || $badgeRight !== ''): ?>
        <span><?= htmlspecialchars($badgeLeft, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php if ($badgeRight !== ''): ?>
          · <span><?= htmlspecialchars($badgeRight, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <h1 class="hero-title" id="heroTitle">
      <?= htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8'); ?>
    </h1>
    <p class="hero-subtitle" id="heroSubtitle">
      <?= htmlspecialchars($heroSubtitle, ENT_QUOTES, 'UTF-8'); ?>
    </p>
  </div>
</header>
<?php include('template/nav-product.php')?>

<section class="article-section">
  <h2 class="article-title" id="articleTitle">
    <?= htmlspecialchars($articleTitle, ENT_QUOTES, 'UTF-8'); ?>
  </h2>
  <div class="article-content">
    <?php
      // article_intro_html diasumsikan sudah berupa HTML aman dari admin
      echo $articleIntroHtml;
    ?>
  </div>
  <div class="accordion">
    <button class="accordion-button" id="accordionButton">
      <span><?= htmlspecialchars($accordionTitle, ENT_QUOTES, 'UTF-8'); ?></span>
      <span class="accordion-icon">▼</span>
    </button>
    <div class="accordion-content" id="accordionContent">
      <div class="accordion-text">
        <?php
          // accordion_body_html juga berupa HTML dari DB
          echo $accordionBodyHtml;
        ?>
      </div>
    </div>
  </div>
</section>

<section class="cards-section">
  <h2 class="section-title" id="locationsTitle">
    <?= htmlspecialchars($locationsTitle, ENT_QUOTES, 'UTF-8'); ?>
  </h2>
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
      Thank you! Your Fun Diving request has been sent. We will get back to you shortly.
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
        <span class="price-amount" id="offcanvasPrice"></span>
      </div>
      <div class="text-xs text-slate-500 mt-1">
        Prices in USD with live IDR preview based on today’s internal rate.
      </div>
    </div>

    <div class="include-exclude-section">
      <div class="accordion" style="margin-bottom:.7rem;">
        <button class="accordion-button" id="includeButton">
          <span>✓ What’s included</span><span class="accordion-icon">▼</span>
        </button>
        <div class="accordion-content" id="includeContent">
          <div class="accordion-text">
            <ul id="includeList" style="list-style:none;padding:0;margin:0;"></ul>
          </div>
        </div>
      </div>
      <div class="accordion">
        <button class="accordion-button" id="excludeButton">
          <span>✗ Not included</span><span class="accordion-icon">▼</span>
        </button>
        <div class="accordion-content" id="excludeContent">
          <div class="accordion-text">
            <ul id="excludeList" style="list-style:none;padding:0;margin:0;"></ul>
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
        <a href="/try-diving" class="px-3 py-1 rounded-full border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100">Try Diving</a>
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
  const locations   = <?=json_encode($locations, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
  const usdToIdr    = <?= (int)$USD_TO_IDR; ?>;
  const activityKey = 'fun_diving';

  // data dari DB (bd_global_settings.global_template -> pages.fun_diving)
  const heroImages = <?= json_encode($heroImages, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
  const defaultIncludeItems = <?= json_encode($defaultIncludeItems, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
  const defaultExcludeItems = <?= json_encode($defaultExcludeItems, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;

  let currentImageIndex      = 0;
  let currentLocation        = null;
  let currentCalendarDate    = new Date();
  let selectedDateValue      = null;
  let currentAvailabilityMap = {};
  let currentSubKey          = null;

  function rotateHeroBackground() {
    if (!heroImages || !heroImages.length) return;
    const heroBackground = document.getElementById('heroBackground');
    heroBackground.style.backgroundImage = `url('${heroImages[currentImageIndex]}')`;
    currentImageIndex = (currentImageIndex + 1) % heroImages.length;
  }

  function mapEmbedByLatLng(lat, lng, zoom) {
    return `https://www.google.com/maps?q=${lat},${lng}&hl=en&z=${zoom}&output=embed`;
  }
  function mapEmbedByQuery(q, zoom) {
    return `https://www.google.com/maps?q=${encodeURIComponent(q)}&hl=en&z=${zoom}&output=embed`;
  }
  function getMapSrc(loc) {
    if (typeof loc.lat === 'number' && typeof loc.lng === 'number') {
      return mapEmbedByLatLng(loc.lat, loc.lng, loc.zoom || 12);
    }
    const q = loc.query || loc.name || 'Bali';
    return mapEmbedByQuery(q, loc.zoom || 12);
  }
  function getMapLink(loc) {
    if (typeof loc.lat === 'number' && typeof loc.lng === 'number') {
      return `https://www.google.com/maps/search/?api=1&query=${loc.lat},${loc.lng}`;
    }
    const q = loc.query || loc.name || 'Bali';
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}`;
  }

  function formatUsdIdr(usd) {
    if (usd == null || isNaN(usd)) {
      return { usdLabel: '', idrLabel: '' };
    }
    const rupiah = Math.round(usd * usdToIdr);
    return {
      usdLabel: `$${usd.toFixed(0)}`,
      idrLabel: `IDR ${rupiah.toLocaleString('id-ID')}`
    };
  }

  function updatePricing() {
    const el = document.getElementById('offcanvasPrice');
    if (!currentLocation || currentLocation.basePrice == null) {
      el.textContent = '';
      return;
    }
    const labels = formatUsdIdr(currentLocation.basePrice);
    el.textContent = `${labels.usdLabel} · ${labels.idrLabel}`;
  }

  function renderIncludeExclude() {
    const includeList = document.getElementById('includeList');
    const excludeList = document.getElementById('excludeList');

    // include
    if (currentLocation && Array.isArray(currentLocation.include) && currentLocation.include.length > 0) {
      includeList.innerHTML = currentLocation.include
        .map(item => `<li>${item}</li>`)
        .join('');
    } else {
      includeList.innerHTML = defaultIncludeItems
        .map(item => `<li>${item}</li>`)
        .join('');
    }

    // exclude
    if (currentLocation && Array.isArray(currentLocation.exclude) && currentLocation.exclude.length > 0) {
      excludeList.innerHTML = currentLocation.exclude
        .map(item => `<li>${item}</li>`)
        .join('');
    } else {
      excludeList.innerHTML = defaultExcludeItems
        .map(item => `<li>${item}</li>`)
        .join('');
    }
  }

  function renderCards() {
    const grid = document.getElementById('cardsGrid');
    grid.innerHTML = locations.map(loc => `
      <div class="card" data-id="${loc.id}">
        <div class="card-header">
          ${loc.image ? `<img class="card-image" src="${loc.image}" alt="${loc.name}">` : ''}
          ${loc.duration ? `
          <div class="duration-badge">
            <span><i class="fa-regular fa-clock"></i></span>
            <span>${loc.duration}</span>
          </div>` : ''}
        </div>
        <div class="card-content">
          <h3 class="card-title">${loc.name ?? ''}</h3>
          <p class="card-summary">${loc.summary ?? ''}</p>
          <button class="card-select-btn">See details</button>
        </div>
      </div>
    `).join('');

    grid.querySelectorAll('.card').forEach(card => {
      card.addEventListener('click', () => {
        const id = parseInt(card.dataset.id, 10);
        openOffcanvas(id);
      });
      const btn = card.querySelector('.card-select-btn');
      btn.addEventListener('click', e => {
        e.stopPropagation();
        const id = parseInt(card.closest('.card').dataset.id, 10);
        openOffcanvas(id);
      });
    });
  }

  async function loadAvailabilityForMonth(subKey, year, monthIndex) {
    const monthStr = `${year}-${String(monthIndex + 1).padStart(2, '0')}`;
    try {
      const res  = await fetch(`fun-diving.php?action=availability&sub_key=${encodeURIComponent(subKey)}&month=${encodeURIComponent(monthStr)}`, {cache: 'no-store'});
      const json = await res.json();
      if (json.ok) {
        currentAvailabilityMap = json.dates || {};
      } else {
        currentAvailabilityMap = {};
      }
    } catch (e) {
      console.error('availability error', e);
      currentAvailabilityMap = {};
    }
    renderCalendar();
  }

  function renderCalendar() {
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

    const firstDay     = new Date(year, month, 1);
    const lastDay      = new Date(year, month + 1, 0);
    const daysInMonth  = lastDay.getDate();
    const startWeekday = firstDay.getDay();

    const today = new Date();
    today.setHours(0,0,0,0);

    for (let i = 0; i < startWeekday; i++) {
      const d = document.createElement('div');
      d.className = 'calendar-day other-month';
      daysContainer.appendChild(d);
    }

    for (let day = 1; day <= daysInMonth; day++) {
      const el = document.createElement('div');
      el.className = 'calendar-day';
      el.textContent = day;

      const dateObj = new Date(year, month, day);
      const iso     = dateObj.toISOString().split('T')[0];

      if (dateObj < today) {
        el.classList.add('past');
      } else {
        let avail = true;
        if (Object.keys(currentAvailabilityMap).length > 0) {
          if (Object.prototype.hasOwnProperty.call(currentAvailabilityMap, iso)) {
            avail = !!currentAvailabilityMap[iso];
          }
        }
        if (avail) {
          el.classList.add('available');
          el.addEventListener('click', () => selectDate(dateObj, el));
        } else {
          el.classList.add('unavailable');
        }
      }

      if (selectedDateValue && dateObj.getTime() === selectedDateValue.getTime()) {
        el.classList.add('selected');
      }

      daysContainer.appendChild(el);
    }
  }

  function selectDate(date, element) {
    document.querySelectorAll('.calendar-day.selected')
      .forEach(d => d.classList.remove('selected'));
    element.classList.add('selected');
    selectedDateValue = new Date(date);
    document.getElementById('selectedDate').value = date.toISOString().split('T')[0];

    document.getElementById('emailGroup').classList.remove('hidden');
    const btn = document.getElementById('checkoutButton');
    btn.classList.add('show');
    btn.textContent = 'Check & send request';
  }

  function initCalendar() {
    document.getElementById('prevMonth').addEventListener('click', () => {
      currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
      if (currentSubKey) {
        loadAvailabilityForMonth(currentSubKey, currentCalendarDate.getFullYear(), currentCalendarDate.getMonth());
      } else {
        renderCalendar();
      }
    });
    document.getElementById('nextMonth').addEventListener('click', () => {
      currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
      if (currentSubKey) {
        loadAvailabilityForMonth(currentSubKey, currentCalendarDate.getFullYear(), currentCalendarDate.getMonth());
      } else {
        renderCalendar();
      }
    });
    renderCalendar();
  }

  const offcanvasOverlay = document.getElementById('offcanvasOverlay');
  const offcanvas        = document.getElementById('offcanvas');

  function openOffcanvas(id) {
    currentLocation = locations.find(l => l.id === id);
    if (!currentLocation) return;

    currentSubKey          = currentLocation.key || null;
    currentCalendarDate    = new Date();
    selectedDateValue      = null;
    document.getElementById('selectedDate').value = '';

    document.getElementById('offcanvasTitle').textContent       = currentLocation.name ?? '';
    document.getElementById('offcanvasCover').src               = currentLocation.image ?? '';
    document.getElementById('offcanvasDescription').textContent = currentLocation.description ?? '';

    const mapSrc = getMapSrc(currentLocation);
    document.getElementById('locationMap').src    = mapSrc;
    document.getElementById('openInMapsBtn').href = getMapLink(currentLocation);

    document.getElementById('successMessage').classList.remove('show');
    document.getElementById('bookingForm').reset();
    document.getElementById('emailGroup').classList.add('hidden');
    const btn = document.getElementById('checkoutButton');
    btn.classList.remove('show');
    btn.textContent = 'Check availability';

    updatePricing();
    renderIncludeExclude();

    offcanvasOverlay.classList.add('active');
    offcanvas.classList.add('active');

    if (currentSubKey) {
      loadAvailabilityForMonth(currentSubKey, currentCalendarDate.getFullYear(), currentCalendarDate.getMonth());
    } else {
      renderCalendar();
    }
  }

  function closeOffcanvas() {
    offcanvasOverlay.classList.remove('active');
    offcanvas.classList.remove('active');
  }

  document.getElementById('offcanvasClose').addEventListener('click', closeOffcanvas);
  offcanvasOverlay.addEventListener('click', closeOffcanvas);

  document.getElementById('bookingForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!currentLocation) return;

    const email = document.getElementById('emailInput').value.trim();
    const date  = document.getElementById('selectedDate').value;

    if (!email || !date) return;

    const btn = document.getElementById('checkoutButton');
    btn.disabled   = true;
    btn.textContent = 'Sending...';

    const payload = {
      type: 'fun_diving_request',
      activity: activityKey,
      locationKey: currentLocation.key,
      locationName: currentLocation.name,
      email,
      date,
      createdAt: new Date().toISOString()
    };

    let ok = false;
    if (window.dataSdk && typeof window.dataSdk.create === 'function') {
      try {
        const res = await window.dataSdk.create(payload);
        ok = !!res.isOk;
      } catch (err) {
        console.error(err);
      }
    } else {
      console.log('Booking mock payload:', payload);
      ok = true;
    }

    if (ok) {
      document.getElementById('successMessage').classList.add('show');
      document.getElementById('bookingForm').reset();
      document.getElementById('emailGroup').classList.add('hidden');
      btn.classList.remove('show');
      setTimeout(() => document.getElementById('successMessage').classList.remove('show'), 5000);
    } else {
      alert('Failed to send request. Please try again or contact us via WhatsApp.');
    }

    btn.disabled   = false;
    btn.textContent = 'Check & send request';
  });

  function initAccordions() {
    const mainBtn     = document.getElementById('accordionButton');
    const mainContent = document.getElementById('accordionContent');
    mainBtn.addEventListener('click', () => {
      const active = mainContent.classList.toggle('active');
      mainBtn.classList.toggle('active', active);
    });

    const includeBtn     = document.getElementById('includeButton');
    const includeContent = document.getElementById('includeContent');
    includeBtn.addEventListener('click', () => {
      const active = includeContent.classList.toggle('active');
      includeBtn.classList.toggle('active', active);
    });

    const excludeBtn     = document.getElementById('excludeButton');
    const excludeContent = document.getElementById('excludeContent');
    excludeBtn.addEventListener('click', () => {
      const active = excludeContent.classList.toggle('active');
      excludeBtn.classList.toggle('active', active);
    });
  }

  (function init() {
    rotateHeroBackground();
    if (heroImages && heroImages.length) {
      setInterval(rotateHeroBackground, 2500);
    }
    renderCards();
    initCalendar();
    initAccordions();
  })();
</script>

<?php include('template/footer-product.php')?>
</body>
</html>
