<?php

// =======================================================
// 1. CONFIGURATION (GANTI NILAI INI DENGAN KREDENSIAL ANDA)
// =======================================================

// --- Database Credentials ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'u1783223_bd_crm');
define('DB_USER', 'u1783223_bd_crm');
define('DB_PASS', 'finD0!bd.crm');

// --- API Configuration ---
// !!! GANTI DENGAN API KEY OPENWEATHERMAP ANDA YANG AKTIF !!!
define('API_KEY', "9f52bfe102e5934096c4d3666c3f7e19"); 

// Update interval in seconds (3600 seconds = 1 hour)
define('CACHE_DURATION', 3600); 

// --- Bali Dive Sites and Coordinates ---
$diveSites = [
    ['name' => 'Tulamben', 'lat' => -8.28, 'lon' => 115.58],
    ['name' => 'Nusa Penida (Manta Point)', 'lat' => -8.82, 'lon' => 115.53],
    ['name' => 'Padang Bai', 'lat' => -8.55, 'lon' => 115.50],
    ['name' => 'Menjangan Island', 'lat' => -8.11, 'lon' => 114.51],
    ['name' => 'Amed', 'lat' => -8.32, 'lon' => 115.68],
];


// =======================================================
// 2. DATABASE FUNCTIONS
// =======================================================

/**
 * Connects to the database and handles connection errors.
 * Menggunakan define() untuk kredensial
 * @return mysqli Database connection object.
 */
function getDBConnection() {
    // Menggunakan konstanta global yang didefinisikan di atas
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        // Pada production, log error ini, jangan tampilkan ke user
        error_log("Database connection failed: " . $conn->connect_error);
        return null;
    }
    return $conn;
}

/**
 * Creates the weather_cache table if it does not exist.
 */
function createWeatherTable(mysqli $conn) {
    $sql = "CREATE TABLE IF NOT EXISTS `weather_cache` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `site_name` VARCHAR(100) NOT NULL UNIQUE,
        `latitude` DECIMAL(10, 8) NOT NULL,
        `longitude` DECIMAL(11, 8) NOT NULL,
        `temp_c` DECIMAL(5, 2),
        `humidity` INT(3),
        `wind_speed_kmh` DECIMAL(5, 2),
        `weather_desc` VARCHAR(255),
        `last_updated` DATETIME NOT NULL
    )";
    if (!$conn->query($sql)) {
        error_log("Error creating table: " . $conn->error);
        return false;
    }
    return true;
}

// =======================================================
// 3. API & DATA MANAGEMENT FUNCTIONS
// =======================================================

/**
 * Fetches weather data from OpenWeatherMap API.
 * @return array|null Weather data array or null on failure.
 */
function fetchWeatherFromApi($lat, $lon, $apiKey) {
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=en";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $json_data = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($json_data, true);
    
    if (isset($data['cod']) && $data['cod'] != 200) {
        error_log("OpenWeatherMap API Error Code: " . $data['cod'] . (isset($data['message']) ? " - " . $data['message'] : ""));
        return null;
    }
    
    // Extract and format data
    $weather = [
        'temp_c' => round($data['main']['temp'], 1),
        'humidity' => $data['main']['humidity'],
        'wind_speed_kmh' => round($data['wind']['speed'] * 3.6, 1), // m/s to km/h
        'weather_desc' => ucwords($data['weather'][0]['description']),
    ];
    
    return $weather;
}

/**
 * Updates or inserts the weather data into the cache table.
 */
function updateCache(mysqli $conn, $site, $weatherData) {
    $siteName = $conn->real_escape_string($site['name']);
    $lat = $site['lat'];
    $lon = $site['lon'];
    $temp = $weatherData['temp_c'];
    $humid = $weatherData['humidity'];
    $wind = $weatherData['wind_speed_kmh'];
    $desc = $conn->real_escape_string($weatherData['weather_desc']);
    $now = date('Y-m-d H:i:s');

    $sql = "INSERT INTO `weather_cache` (site_name, latitude, longitude, temp_c, humidity, wind_speed_kmh, weather_desc, last_updated) 
            VALUES ('$siteName', '$lat', '$lon', '$temp', '$humid', '$wind', '$desc', '$now')
            ON DUPLICATE KEY UPDATE 
            temp_c = VALUES(temp_c), 
            humidity = VALUES(humidity), 
            wind_speed_kmh = VALUES(wind_speed_kmh), 
            weather_desc = VALUES(weather_desc), 
            last_updated = VALUES(last_updated)";
            
    $conn->query($sql);
}

// =======================================================
// 4. MAIN EXECUTION FUNCTION (FUNGSI UTAMA YANG AKAN DIPANGGIL)
// =======================================================

/**
 * Fetches data for all dive sites, prioritizing cache.
 * @param array $diveSites The list of sites and coordinates.
 * @return array The processed weather data.
 */
function fetchAllDiveSiteData(array $diveSites): array {
    $conn = getDBConnection();

    if ($conn === null) {
        return ['error' => 'Database connection failed. Check logs.'];
    }
    
    if (!createWeatherTable($conn)) {
        $conn->close();
        return ['error' => 'Table creation failed. Check logs.'];
    }

    $results = [];

    foreach ($diveSites as $site) {
        $siteName = $conn->real_escape_string($site['name']);
        $cachedData = null;
        $source = 'API';
        $row = []; // Inisialisasi

        // --- A. Check Cache ---
        $sql = "SELECT * FROM `weather_cache` WHERE site_name = '$siteName'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastUpdatedTime = strtotime($row['last_updated']);
            $currentTime = time();
            
            // Check if data is fresh
            if (($currentTime - $lastUpdatedTime) < CACHE_DURATION) {
                $cachedData = $row;
                $source = 'Cache';
            }
        }

        // --- B. Fetch from API if Cache Miss or Expired ---
        if ($cachedData === null) {
            $weatherData = fetchWeatherFromApi($site['lat'], $site['lon'], API_KEY);
            
            if ($weatherData) {
                updateCache($conn, $site, $weatherData);
                
                // Re-fetch the newly inserted data for consistent structure
                $result = $conn->query($sql);
                $cachedData = $result->fetch_assoc();
                $source = 'API (Updated)';
            } else {
                // Failed to fetch from API, use old cached data if exists
                if (!empty($row)) {
                    $cachedData = $row;
                    $source = 'Cache (Expired, API Failed)';
                } else {
                    // Completely failed to get data
                    $cachedData = ['site_name' => $site['name'], 'temp_c' => 'N/A', 'humidity' => 'N/A', 'wind_speed_kmh' => 'N/A', 'weather_desc' => 'N/A', 'last_updated' => 'N/A'];
                    $source = 'Failed';
                }
            }
        }

        $cachedData['source'] = $source;
        
        // Hanya ambil data yang diperlukan untuk tampilan minimalis
        $results[] = [
            'name' => $cachedData['site_name'],
            'temp' => $cachedData['temp_c'],
            'desc' => $cachedData['weather_desc'],
            'humidity' => $cachedData['humidity'],
            'updated' => $cachedData['last_updated'],
            'source' => $cachedData['source']
        ];
    }

    $conn->close();
    return $results;
}

// File ini tidak akan melakukan apa-apa kecuali dipanggil oleh file lain.