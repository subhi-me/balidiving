<?php
// template/database/main-cart.php
declare(strict_types=1);

date_default_timezone_set('Asia/Makassar');

$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
} catch (Throwable $e) {
  // jangan die/echo supaya endpoint API tetap bisa balikin JSON
  throw new RuntimeException('DB connection failed');
}

if (!function_exists('get_dynamic_bca_rate')) {
  function get_dynamic_bca_rate($default_rate = 17595) {
    $cacheFile = __DIR__ . '/../../cart/bca_usd_rate.json';
    $cacheTime = 3600 * 6; // 6 jam cache

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
      $data = json_decode(file_get_contents($cacheFile), true);
      if (isset($data['rate']) && is_numeric($data['rate'])) {
        return (float)$data['rate'];
      }
    }

    $url = "https://www.bca.co.id/id/informasi/kurs";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $html = curl_exec($ch);
    curl_close($ch);
    
    if ($html) {
      if (preg_match('/code="USD"[\s\S]*?rate-type="eRate-sell"[\s\S]*?<p>([\d\.,]+)<\/p>/i', $html, $matches)) {
        $rateStr = str_replace(['.', ','], ['', '.'], $matches[1]);
        if (is_numeric($rateStr)) {
          $rate = (float)$rateStr;
          file_put_contents($cacheFile, json_encode(['rate' => $rate, 'time' => time()]));
          return $rate;
        }
      }
    }
    
    if (file_exists($cacheFile)) {
      $data = json_decode(file_get_contents($cacheFile), true);
      if (isset($data['rate']) && is_numeric($data['rate'])) {
        return (float)$data['rate'];
      }
    }
    
    return $default_rate;
  }
}

$db_fallback = 15800;
try {
  $g = $pdo->query("SELECT usd_to_idr FROM booking_globals ORDER BY id DESC LIMIT 1")->fetch();
  if ($g && !empty($g['usd_to_idr']) && (int)$g['usd_to_idr'] > 0) {
    $db_fallback = (int)$g['usd_to_idr'];
  }
} catch (Throwable $e) {
  // ignore
}

$USD_TO_IDR = (int)get_dynamic_bca_rate($db_fallback);

function bd_get_catalog(PDO $pdo): array {
  $sqlProducts = "
    SELECT id, name, price_usd, category, description, is_enquiry
    FROM bd_catalog_products
    ORDER BY category, id
  ";
  $products = $pdo->query($sqlProducts)->fetchAll();

  $sqlImages = "
    SELECT product_id, image_url
    FROM bd_catalog_product_images
    WHERE sort_order = 1
    ORDER BY product_id
  ";
  $imagesRows = $pdo->query($sqlImages)->fetchAll();
  $images = [];
  foreach ($imagesRows as $row) {
    $images[(int)$row['product_id']] = (string)$row['image_url'];
  }

  $variants = [];
  try {
    $sqlVariants = "
      SELECT product_id, variant_key, label, description, price_usd
      FROM bd_catalog_variants
      ORDER BY product_id, id
    ";
    $variantsRows = $pdo->query($sqlVariants)->fetchAll();
    foreach ($variantsRows as $row) {
      $pid = (int)$row['product_id'];
      $variants[$pid][] = [
        'id' => (string)$row['variant_key'],
        'label' => (string)$row['label'],
        'desc' => (string)($row['description'] ?? ''),
        'price' => (float)$row['price_usd'],
      ];
    }
  } catch (Throwable $e) {
    $variants = [];
  }

  $sqlAddons = "
    SELECT addon_key, name, price_usd
    FROM bd_catalog_addons
    ORDER BY id
  ";
  $addonsRows = $pdo->query($sqlAddons)->fetchAll();
  $addons = [];
  foreach ($addonsRows as $row) {
    $addons[] = [
      'id' => (string)$row['addon_key'],
      'name' => (string)$row['name'],
      'price' => (float)$row['price_usd'],
    ];
  }

  return [$products, $images, $variants, $addons];
}
