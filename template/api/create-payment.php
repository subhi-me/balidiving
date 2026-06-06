<?php
// template/api/create-payment.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

ini_set('display_errors', '0');
error_reporting(E_ALL);

while (ob_get_level()) { @ob_end_clean(); }
ob_start();

header('Content-Type: application/json; charset=utf-8');

function json_out(array $arr, int $code = 200): void {
  if (ob_get_length()) { @ob_clean(); }
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit;
}

function ini_bytes(string $val): int {
  $val = trim($val);
  if ($val === '') return 0;
  $last = strtolower($val[strlen($val)-1]);
  $num = (int)$val;
  return match ($last) {
    'g' => $num * 1024 * 1024 * 1024,
    'm' => $num * 1024 * 1024,
    'k' => $num * 1024,
    default => (int)$val,
  };
}

try {
  $method = $_SERVER['REQUEST_METHOD'] ?? '';
  $ctype  = $_SERVER['CONTENT_TYPE'] ?? '';
  $clen   = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);

  // Ambil body
  $raw = file_get_contents('php://input');
  $raw = is_string($raw) ? $raw : '';
  $rawTrim = trim($raw);

  // Ambil fallback dari form-urlencoded
  $payloadPost = '';
  if (isset($_POST['payload']) && is_string($_POST['payload'])) {
    $payloadPost = trim($_POST['payload']);
  }

  // Jika kosong total -> kirim diagnosa
  if ($rawTrim === '' && $payloadPost === '') {
    $postMax = ini_get('post_max_size') ?: '';
    $uplMax  = ini_get('upload_max_filesize') ?: '';

    json_out([
      'ok' => false,
      'error' => 'empty_body',
      'diagnostic' => [
        'method' => $method,
        'content_type' => $ctype,
        'content_length' => $clen,
        'post_max_size' => $postMax,
        'upload_max_filesize' => $uplMax,
        'post_max_size_bytes' => ini_bytes($postMax),
        'upload_max_filesize_bytes' => ini_bytes($uplMax),
        'php_input_len' => strlen($raw),
        'post_keys' => array_keys($_POST),
        'note' => 'Jika content_length > post_max_size_bytes, PHP akan buang body (php://input kosong & $_POST kosong). Kalau content_length=0, body memang tidak terkirim/ter-strip oleh layer server/WAF.',
      ]
    ], 400);
  }

  // Pilih payload
  $payload = ($payloadPost !== '') ? $payloadPost : $rawTrim;

  $data = json_decode($payload, true);
  if (!is_array($data)) json_out(['ok'=>false,'error'=>'invalid_json'], 400);

  // ---- DB ----
  $dbFile = __DIR__ . '/../database/main-cart.php';
  if (!is_file($dbFile)) throw new RuntimeException("Missing DB bootstrap: {$dbFile}");
  require $dbFile;
  if (!isset($pdo) || !($pdo instanceof PDO)) throw new RuntimeException("PDO not initialized");

  // ---- Kurs ----
  $USD_TO_IDR = 15800;
  $kursFile = __DIR__ . '/kurs_bca.php';
  if (is_file($kursFile)) include $kursFile;
  if (!isset($USD_TO_IDR) || !is_numeric($USD_TO_IDR)) $USD_TO_IDR = 15800;
  $USD_TO_IDR = (float)$USD_TO_IDR;

  $bookingId = trim((string)($data['booking_id'] ?? ''));
  if ($bookingId === '') json_out(['ok'=>false,'error'=>'missing_booking_id'], 400);

  $customer  = is_array($data['customer'] ?? null) ? $data['customer'] : [];
  $custName  = trim((string)($customer['name'] ?? ''));
  $custEmail = trim((string)($customer['email'] ?? ''));

  $items = $data['items'] ?? [];
  if (!is_array($items) || count($items) === 0) json_out(['ok'=>false,'error'=>'no_items'], 400);

  $totalUsd = (float)($data['total_usd'] ?? 0);
  $totalIdr = (float)($data['total_idr'] ?? 0);
  if ($totalUsd <= 0) json_out(['ok'=>false,'error'=>'zero_total'], 400);
  if ($totalIdr <= 0) $totalIdr = (int)round($totalUsd * $USD_TO_IDR);

  $pdo->beginTransaction();

  $pdo->prepare("DELETE FROM bd_booking_items WHERE booking_id = :bid")->execute([':bid'=>$bookingId]);
  $pdo->prepare("DELETE FROM bd_bookings WHERE booking_id = :bid")->execute([':bid'=>$bookingId]);

  $pdo->prepare("
    INSERT INTO bd_bookings
    (booking_id, customer_name, customer_email, total_usd, total_idr, coupon_value_usd, currency, status, created_at, updated_at)
    VALUES (:booking_id, :customer_name, :customer_email, :total_usd, :total_idr, 0, 'USD', 'pending', NOW(), NOW())
  ")->execute([
    ':booking_id' => $bookingId,
    ':customer_name' => $custName,
    ':customer_email' => $custEmail,
    ':total_usd' => $totalUsd,
    ':total_idr' => $totalIdr,
  ]);

  $insItem = $pdo->prepare("
    INSERT INTO bd_booking_items
    (booking_id, product_id, name, category, booking_date, quantity, unit_price_usd, line_total_usd)
    VALUES (:booking_id, :product_id, :name, :category, :booking_date, :quantity, :unit_price_usd, :line_total_usd)
  ");

  foreach ($items as $it) {
    if (!is_array($it)) continue;
    $productId = (int)($it['product_id'] ?? 0);
    $name      = (string)($it['name'] ?? '');
    $category  = (string)($it['category'] ?? '');
    $date      = (string)($it['booking_date'] ?? '');
    $qty       = (int)($it['quantity'] ?? 1);
    $unitUsd   = (float)($it['unit_price_usd'] ?? 0);
    $lineUsd   = (float)($it['line_total_usd'] ?? ($unitUsd * $qty));

    $insItem->execute([
      ':booking_id' => $bookingId,
      ':product_id' => $productId,
      ':name' => $name,
      ':category' => $category,
      ':booking_date' => ($date !== '') ? $date : null,
      ':quantity' => ($qty > 0 ? $qty : 1),
      ':unit_price_usd' => $unitUsd,
      ':line_total_usd' => $lineUsd,
    ]);
  }

  $pdo->commit();

  $paymentUrl = '/cart/payment-act.php?booking_id=' . urlencode($bookingId);

  json_out(['ok'=>true,'payment_url'=>$paymentUrl,'booking_id'=>$bookingId]);

} catch (Throwable $e) {
  if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
  json_out(['ok'=>false,'error'=>'server_error','msg'=>$e->getMessage()], 500);
}
