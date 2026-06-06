<?php
/**
 * ============================================================
 * /template/api/fx_rates.php
 * Ambil kurs dinamis dari BCA (berdasarkan get_dynamic_bca_rate)
 * ============================================================
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

date_default_timezone_set('Asia/Makassar');

$base  = strtoupper(trim((string)($_GET['base'] ?? 'USD')));
$quote = strtoupper(trim((string)($_GET['quote'] ?? 'IDR')));

// validasi sederhana: harus 3 huruf
if (!preg_match('/^[A-Z]{3}$/', $base) || !preg_match('/^[A-Z]{3}$/', $quote)) {
  http_response_code(400);
  echo json_encode([
    'ok' => false,
    'error' => 'Invalid currency code',
  ], JSON_UNESCAPED_SLASHES);
  exit;
}

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

try {
  if ($base === 'USD' && $quote === 'IDR') {
      $rate = get_dynamic_bca_rate(17595);
      
      echo json_encode([
        'ok' => true,
        'base' => $base,
        'quote' => $quote,
        'rate' => $rate,
        'updated_at' => date('Y-m-d H:i:s'),
      ], JSON_UNESCAPED_SLASHES);
      exit;
  }

  // Jika bukan USD ke IDR, coba ambil dari database (opsional, jika perlu fallback)
  require __DIR__ . '/../database/main-cart.php';
  
  $sql = "
    SELECT base, quote, rate, updated_at
    FROM fx_rates
    WHERE base = :base AND quote = :quote
    ORDER BY updated_at DESC
    LIMIT 1
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([':base' => $base, ':quote' => $quote]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    http_response_code(404);
    echo json_encode([
      'ok' => false,
      'error' => 'Rate not found',
      'base' => $base,
      'quote' => $quote,
    ], JSON_UNESCAPED_SLASHES);
    exit;
  }

  $rate = (float)($row['rate'] ?? 0);
  echo json_encode([
    'ok' => true,
    'base' => $row['base'],
    'quote' => $row['quote'],
    'rate' => $rate,
    'updated_at' => $row['updated_at'],
  ], JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'error' => 'Server error',
  ], JSON_UNESCAPED_SLASHES);
}
