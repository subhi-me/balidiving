<?php include('01-start.php'); ?>

<div class="magazine-box">
    <h3 class="magazine-title">Dive Bali with Confidence: The Underwater Sanctuary</h3>

    <p>
        Bali's underwater environment is globally renowned for creating a genuinely 
        <strong>calm and confident</strong> diving experience. Unlike rougher ocean regions, 
        key Balinese dive sites offer stable conditions and naturally warm waters 
        (typically <strong>26°C to 29°C</strong>), significantly reducing thermal stress and 
        greatly enhancing diver comfort.
    </p>

    <p>
        The vast biodiversity—from the macro-life of Amed to the giant pelagics around 
        Nusa Penida—is nurtured by a thriving marine ecosystem. Even when currents appear, 
        they are expertly navigated by our team, often resulting in 
        <strong>exceptional visibility</strong> (20–40 meters), a key element for diver serenity.
    </p>

    <p>
        Choosing <strong>Balidiving.com</strong> means embracing an added shield of safety. 
        Our experienced local Divemasters possess deep knowledge of currents, entry points, 
        and timing—ensuring your underwater journey is smooth, secure, and unforgettable. 
        Whether exploring the Liberty Wreck or seeking the elusive Mola Mola, you can dive 
        with absolute confidence.
    </p>
</div>

<style>
    .magazine-box {
        background: #ffffff;
        border-radius: 22px;
        padding: 50px;
        max-width: 900px;
        margin: 50px auto;
        line-height: 1.8;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #1e293b;
        border: 1px solid #dbeafe;
        box-shadow: 0 12px 40px rgba(0, 40, 100, 0.08);
    }
    .magazine-title {
        margin-top: 0;
        margin-bottom: 25px;
        font-size: 1.9rem;
        font-weight: 700;
        background: linear-gradient(90deg, #0056b3, #07b6d4);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .magazine-box p {
        margin-bottom: 24px;
        font-size: 1.08rem;
    }
    @media(max-width:768px) {
        .magazine-box { padding: 32px; }
        .magazine-title { font-size: 1.6rem; }
        .magazine-box p { font-size: 1rem; }
    }
</style>

<?php
// =======================================================
// 1. CONFIGURATION
// =======================================================
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

$API_KEY = "9f52bfe102e5934096c4d3666c3f7e19"; 
$CACHE_DURATION = 3600; // 1 hour

$diveSites = [
    ['name' => 'Tulamben', 'lat' => -8.28, 'lon' => 115.58],
    ['name' => 'Nusa Penida (Manta Point)', 'lat' => -8.82, 'lon' => 115.53],
    ['name' => 'Padang Bai', 'lat' => -8.55, 'lon' => 115.50],
    ['name' => 'Menjangan Island', 'lat' => -8.11, 'lon' => 114.51],
    ['name' => 'Amed', 'lat' => -8.32, 'lon' => 115.68],
];

// Buat mapping name -> koordinat untuk dipakai di card (map)
$siteCoords = [];
foreach ($diveSites as $s) {
    $siteCoords[$s['name']] = [
        'lat' => $s['lat'],
        'lon' => $s['lon'],
    ];
}

// =======================================================
// META DIVE SITE: Depth + Availability + PADI Requirement
// =======================================================
$diveMeta = [
    'Tulamben' => [
        'depth'      => '5–30 m',
        'snorkeling' => 'Yes – calm bay, shallow reef',
        'scuba'      => 'Yes – ideal for all levels',
        'padi'       => 'All Levels'
    ],
    'Nusa Penida (Manta Point)' => [
        'depth'      => '8–25 m',
        'snorkeling' => 'Possible (Join us)',
        'scuba'      => 'Yes, with confidence',
        'padi'       => 'Open Water to Advanced.'
    ],
    'Padang Bai' => [
        'depth'      => '5–30 m',
        'snorkeling' => 'Yes – nice shallow reef areas',
        'scuba'      => 'Yes – training to fun dives',
        'padi'       => 'All Levels.'
    ],
    'Menjangan Island' => [
        'depth'      => '5–40 m',
        'snorkeling' => 'Yes – clear & shallow reef',
        'scuba'      => 'Yes – relaxed wall dives',
        'padi'       => 'Min. Open Water.'
    ],
    'Amed' => [
        'depth'      => '5–30 m',
        'snorkeling' => 'Yes – easy access from shore',
        'scuba'      => 'Yes – excellent for beginners',
        'padi'       => 'All levels.'
    ],
];

// =======================================================
// 2. DATABASE FUNCTIONS
// =======================================================
function getDBConnection($host, $user, $pass, $name) {
    $conn = new mysqli($host, $user, $pass, $name);
    if ($conn->connect_error) {
        die("<h3>Database Connection Failed</h3><p>Error: " . $conn->connect_error . "</p>");
    }
    return $conn;
}

function createWeatherTable(mysqli $conn) {
    $sql = "CREATE TABLE IF NOT EXISTS `weather_cache` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `site_name` VARCHAR(100) NOT NULL UNIQUE,
        `latitude` DECIMAL(10,8) NOT NULL,
        `longitude` DECIMAL(11,8) NOT NULL,
        `temp_c` DECIMAL(5,2),
        `humidity` INT(3),
        `wind_speed_kmh` DECIMAL(5,2),
        `weather_desc` VARCHAR(255),
        `last_updated` DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($sql);
}

// =======================================================
// 3. API & DATA MANAGEMENT
// =======================================================
function fetchWeatherFromApi($lat, $lon, $apiKey) {
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=en";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (!$data || !isset($data['main'])) return null;

    return [
        'temp_c'        => round($data['main']['temp'], 1),
        'humidity'      => $data['main']['humidity'],
        'wind_speed_kmh'=> isset($data['wind']['speed']) ? round($data['wind']['speed'] * 3.6, 1) : null,
        'weather_desc'  => ucwords($data['weather'][0]['description']),
    ];
}

function updateCache(mysqli $conn, $site, $data) {
    $siteName = $conn->real_escape_string($site['name']);
    $lat      = $site['lat'];
    $lon      = $site['lon'];
    $temp     = $data['temp_c'];
    $humid    = $data['humidity'];
    $wind     = $data['wind_speed_kmh'];
    $desc     = $conn->real_escape_string($data['weather_desc']);
    $now      = date('Y-m-d H:i:s');

    $sql = "INSERT INTO `weather_cache` (site_name, latitude, longitude, temp_c, humidity, wind_speed_kmh, weather_desc, last_updated)
            VALUES ('$siteName', '$lat', '$lon', '$temp', '$humid', '$wind', '$desc', '$now')
            ON DUPLICATE KEY UPDATE
                temp_c=VALUES(temp_c),
                humidity=VALUES(humidity),
                wind_speed_kmh=VALUES(wind_speed_kmh),
                weather_desc=VALUES(weather_desc),
                last_updated=VALUES(last_updated)";
    $conn->query($sql);
}

// =======================================================
// 4. MAIN EXECUTION
// =======================================================
$conn = getDBConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
createWeatherTable($conn);

$results = [];

foreach ($diveSites as $site) {
    $name = $conn->real_escape_string($site['name']);
    $sql  = "SELECT * FROM `weather_cache` WHERE site_name='$name'";
    $res  = $conn->query($sql);

    $useCache = false;
    $row = null;

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $lastUpdated = strtotime($row['last_updated']);
        if (time() - $lastUpdated < $CACHE_DURATION) {
            $results[] = $row + ['source' => 'Cache'];
            $useCache = true;
        }
    }

    if (!$useCache) {
        $data = fetchWeatherFromApi($site['lat'], $site['lon'], $API_KEY);
        if ($data) {
            updateCache($conn, $site, $data);
            $data['site_name'] = $site['name'];
            $data['source']    = 'API';
            $results[] = $data;
        } elseif ($row) {
            $results[] = $row + ['source' => 'Cache (Old)'];
        } else {
            $results[] = [
                'site_name'       => $site['name'],
                'temp_c'          => 'N/A',
                'humidity'        => 'N/A',
                'wind_speed_kmh'  => 'N/A',
                'weather_desc'    => 'N/A',
                'source'          => 'No Data',
            ];
        }
    }
}

$conn->close();
?>

<style>
    .intro-card {
        background: linear-gradient(140deg, #e0f4ff 0%, #f7fbff 100%);
        border: 1px solid #c7e3ff;
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 8px 24px rgba(0, 80, 180, 0.06);
        max-width: 900px;
        margin: auto;
        line-height: 1.65;
    }
    .intro-card h3 {
        margin-top: 0;
        font-size: 1.6rem;
        background: linear-gradient(90deg, #0077c8, #00b4d8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 18px;
        font-weight: 700;
    }
    h2 {
        font-size: 1.7rem;
        color: #0056b3;
        margin-top: 40px;
        font-weight: 700;
        text-align: center;
    }
    .weather-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
        margin-top: 30px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    @media(max-width: 1024px) {
        .weather-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media(max-width: 640px) {
        .weather-grid { grid-template-columns: repeat(1, 1fr); }
    }
    .weather-card {
        background: white;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 6px 22px rgba(0,0,0,0.06);
        transition: transform .2s ease, box-shadow .2s ease;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .weather-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.09);
    }
    .card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
    }
    .weather-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.96rem;
    }
    .weather-row:last-child { border-bottom: none; }
    .weather-label { 
        font-weight: 600; 
        color: #475569; 
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .weather-value { color: #0f172a; font-weight: 600; }

    .api  { border-left: 5px solid #fbbf24; }
    .cache{ border-left: 5px solid #34d399; }
    .fail { border-left: 5px solid #ef4444; }

    .icon { 
        font-size: 1rem; 
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .weather-card a {
        text-decoration: none;
    }

    .map-wrapper {
        margin-top: 10px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .map-iframe {
        width: 100%;
        height: 210px;
        border: 0;
    }

    /* DIVE META BLOCK */
    .dive-meta-box {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
        font-size: 0.9rem;
    }
    .dive-meta-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .dive-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 4px;
    }
    .dive-meta-label {
        font-weight: 600;
        color: #64748b;
    }
    .dive-meta-value {
        text-align: right;
        color: #0f172a;
    }

    /* Realtime button (small & highlighted) */
    .realtime-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 999px;
        background: #0f172a;
        color: #e0f2fe;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        box-shadow: 0 0 0 1px #0ea5e9;
        transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }
    .realtime-btn:hover {
        background: #0ea5e9;
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(14,165,233,0.4);
    }
    .realtime-btn i {
        font-size: 0.8rem;
    }
</style>

<h2>Bali Dive Site Weather</h2>
<h2>Updated Hourly</h2>

<div class="weather-grid">

<?php foreach ($results as $r): 
    $cls = (strpos($r['source'],'API')!==false
        ? 'api'
        : (strpos($r['source'],'Cache')!==false ? 'cache' : 'fail'));

    $siteName = $r['site_name'];
    $lat = null;
    $lon = null;

    if (isset($siteCoords[$siteName])) {
        $lat = $siteCoords[$siteName]['lat'];
        $lon = $siteCoords[$siteName]['lon'];
    }

    $meta = isset($diveMeta[$siteName]) ? $diveMeta[$siteName] : null;
?>
    <div class="weather-card <?= $cls ?>">

        <div class="card-title">
            <?= htmlspecialchars($siteName) ?>
        </div>

        <div class="weather-row">
            <span class="weather-label">
                <span class="icon"><i class="fa-solid fa-temperature-half"></i></span>
                Temperature
            </span>
            <span class="weather-value">
                <?= htmlspecialchars($r['temp_c']) ?> °C
            </span>
        </div>

        <div class="weather-row">
            <span class="weather-label">
                <span class="icon"><i class="fa-solid fa-droplet"></i></span>
                Humidity
            </span>
            <span class="weather-value">
                <?= htmlspecialchars($r['humidity']) ?> %
            </span>
        </div>

        <div class="weather-row">
            <span class="weather-label">
                <span class="icon"><i class="fa-solid fa-wind"></i></span>
                Wind Speed
            </span>
            <span class="weather-value">
                 <?= htmlspecialchars($r['wind_speed_kmh']) ?> km/h
            </span>
        </div>

        <div class="weather-row">
            <span class="weather-label">
                <span class="icon"><i class="fa-solid fa-water"></i></span>
                Sea Temperature
            </span>
            <span class="weather-value">
               <a href="https://balidiving.com/weather-sea-temperature" class="realtime-btn">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Check</span>
               </a>
            </span>
        </div>

        <div class="weather-row">
            <span class="weather-label">
                <span class="icon"><i class="fa-solid fa-cloud-sun"></i></span>
                Condition
            </span>
            <span class="weather-value">
                <?= htmlspecialchars($r['weather_desc']) ?>
            </span>
        </div>

        <?php if ($meta): ?>
        <div class="dive-meta-box">
            <div class="dive-meta-title">Dive Profile & Availability</div>

            <div class="dive-meta-row">
                <span class="dive-meta-label">Typical Depth</span>
                <span class="dive-meta-value">
                    <?= htmlspecialchars($meta['depth']) ?>
                </span>
            </div>

            <div class="dive-meta-row">
                <span class="dive-meta-label">Snorkeling</span>
                <span class="dive-meta-value">
                    <?= htmlspecialchars($meta['snorkeling']) ?>
                </span>
            </div>

            <div class="dive-meta-row">
                <span class="dive-meta-label">Scuba Diving</span>
                <span class="dive-meta-value">
                    <?= htmlspecialchars($meta['scuba']) ?>
                </span>
            </div>

            <div class="dive-meta-row">
                <span class="dive-meta-label">PADI Level</span>
                <span class="dive-meta-value">
                    <?= htmlspecialchars($meta['padi']) ?>
                </span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($lat !== null && $lon !== null): ?>
        <div class="map-wrapper">
            <iframe 
                class="map-iframe"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q=<?= urlencode($lat) ?>,<?= urlencode($lon) ?>&z=11&output=embed">
            </iframe>
        </div>
        <?php endif; ?>

        <!-- Free Consultation -->
        <a href="https://balidiving.com/contact?page=contact" 
           class="mt-3 text-center bg-[#0077c8] hover:bg-[#005a96] text-white font-semibold py-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
           Free Consultation
        </a>

    </div>

<?php endforeach; ?>

</div>

<div style="height:120px;"></div>

<?php
include('template/ebook.php');
include('03-end.php'); ?>
