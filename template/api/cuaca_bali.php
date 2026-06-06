<?php

// =======================================================
// 1. CONFIGURATION
// =======================================================

// --- Database Credentials ---
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

// --- API Configuration ---
// !!! IMPORTANT: REPLACE WITH YOUR ACTIVE OPENWEATHERMAP API KEY !!!
$API_KEY = "9f52bfe102e5934096c4d3666c3f7e19"; 

// Update interval in seconds (3600 seconds = 1 hour)
$CACHE_DURATION = 3600; 

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
 * @return mysqli Database connection object.
 */
function getDBConnection($host, $user, $pass, $name) {
    $conn = new mysqli($host, $user, $pass, $name);
    if ($conn->connect_error) {
        die("<h3>Database Connection Failed</h3><p>Error: " . $conn->connect_error . "</p>");
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
        die("Error creating table: " . $conn->error);
    }
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
    
    if (isset($data['cod']) && $data['cod'] == 401) {
        // API Key Invalid or not activated
        die("<p style='color:red;'>❌ API Error: Invalid API key.</p><p>Please ensure your key is correct and active (may take up to 2 hours).</p>");
    }
    if (isset($data['cod']) && $data['cod'] != 200) {
        // Other API errors (e.g., rate limit, server error)
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
// 4. MAIN EXECUTION
// =======================================================

// 1. Connect and ensure table exists
$conn = getDBConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
createWeatherTable($conn);

$results = [];

foreach ($diveSites as $site) {
    $siteName = $conn->real_escape_string($site['name']);
    $cachedData = null;
    $source = 'API';

    // --- A. Check Cache ---
    $sql = "SELECT * FROM `weather_cache` WHERE site_name = '$siteName'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastUpdatedTime = strtotime($row['last_updated']);
        $currentTime = time();
        
        // Check if data is fresh (within CACHE_DURATION)
        if (($currentTime - $lastUpdatedTime) < $CACHE_DURATION) {
            // Data is fresh, use cache
            $cachedData = $row;
            $source = 'Cache';
        }
    }

    // --- B. Fetch from API if Cache Miss or Expired ---
    if ($cachedData === null) {
        $weatherData = fetchWeatherFromApi($site['lat'], $site['lon'], $API_KEY);
        
        if ($weatherData) {
            updateCache($conn, $site, $weatherData);
            // Re-fetch the newly inserted data for display
            $result = $conn->query($sql);
            $cachedData = $result->fetch_assoc();
            $source = 'API (Updated)';
        } else {
             // Failed to fetch from API, try to display old cached data if exists
            if (isset($row)) {
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
    $results[] = $cachedData;
}

$conn->close();

// =======================================================
// 5. DISPLAY RESULTS
// =======================================================
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bali Dive Site Weather</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background-color: #f4f7f6; }
        h2 { color: #0056b3; border-bottom: 2px solid #0056b3; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background-color: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #e9ecef; color: #333; }
        td:nth-child(1) { text-align: left; font-weight: bold; }
        .source-api { background-color: #fff3cd; }
        .source-cache { background-color: #d4edda; }
        .source-fail { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<h2>Bali Dive Site Weather Forecast (Hourly Cached)</h2>
<p>Current time: <?php echo date('Y-m-d H:i:s') . " WITA"; ?></p>

<table>
    <thead>
        <tr>
            <th>Dive Site</th>
            <th>Temperature (°C)</th>
            <th>Humidity (%)</th>
            <th>Wind Speed (km/h)</th>
            <th>Condition</th>
            <th>Last Updated</th>
            <th>Source</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $item): ?>
            <?php 
                $source_class = '';
                if (strpos($item['source'], 'API') !== false) {
                    $source_class = 'source-api';
                } elseif (strpos($item['source'], 'Cache') !== false) {
                    $source_class = 'source-cache';
                } elseif (strpos($item['source'], 'Failed') !== false) {
                    $source_class = 'source-fail';
                }
            ?>
            <tr class="<?php echo $source_class; ?>">
                <td><?php echo htmlspecialchars($item['site_name']); ?></td>
                <td><?php echo htmlspecialchars($item['temp_c']); ?></td>
                <td><?php echo htmlspecialchars($item['humidity']); ?></td>
                <td><?php echo htmlspecialchars($item['wind_speed_kmh']); ?></td>
                <td><?php echo htmlspecialchars($item['weather_desc']); ?></td>
                <td><?php echo htmlspecialchars($item['last_updated']); ?></td>
                <td><?php echo htmlspecialchars($item['source']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p><strong>Note on Caching:</strong> This script limits API calls for each site to a maximum of once every **<?php echo $CACHE_DURATION / 60; ?> minutes** (24 times per day) by checking the database's `last_updated` timestamp.</p>

</body>