<?php
/* ============================================================
   /cart/checkout-act.php  (ULTRA ROBUST: auto-detect kolom items)
   Fix error: Unknown column 'product_name'
   - Detect kolom yang BENAR di bd_booking_items + bd_bookings
   - Simpan guest: phone + cert_level (kalau kolom ada)
   - Simpan fx_* (kalau kolom ada)
   - Simpan items pakai kolom yang tersedia (name/product_name, date/booking_date, dll)
   ============================================================ */
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

require __DIR__ . '/../template/database/main-cart.php';

function b64url_decode(string $data): string {
  $data = str_replace(['-','_'], ['+','/'], $data);
  $pad = strlen($data) % 4;
  if ($pad) $data .= str_repeat('=', 4 - $pad);
  $raw = base64_decode($data, true);
  return $raw === false ? '' : $raw;
}
function clean_str($v): string { return trim((string)($v ?? '')); }

function tableColumns(PDO $pdo, string $table): array {
  $cols = [];
  $st = $pdo->query("SHOW COLUMNS FROM `$table`");
  while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
    $cols[strtolower((string)$r['Field'])] = (string)$r['Field']; // map lowercase => actual
  }
  return $cols;
}
function hasCol(array $cols, string $name): bool {
  return isset($cols[strtolower($name)]);
}
function pickCol(array $cols, array $candidates): ?string {
  foreach ($candidates as $c) {
    $k = strtolower($c);
    if (isset($cols[$k])) return $cols[$k]; // actual case
  }
  return null;
}

$MY_BOOKING_URL = 'https://balidiving.com/cart/my-booking';
$PAYMENT_URL    = '/cart/payment-act.php?booking_id=';

$encoded = (string)($_GET['data'] ?? '');
if ($encoded === '') { header("Location: {$MY_BOOKING_URL}"); exit; }

$json = b64url_decode($encoded);
if ($json === '') { header("Location: {$MY_BOOKING_URL}"); exit; }

$payload = json_decode($json, true);
if (!is_array($payload)) { header("Location: {$MY_BOOKING_URL}"); exit; }

$bookingId = clean_str($payload['booking_id'] ?? '');
if ($bookingId === '') { header("Location: {$MY_BOOKING_URL}"); exit; }

// customer
$customerName  = clean_str($payload['customer']['name'] ?? $payload['customer_name'] ?? '');
$customerEmail = clean_str($payload['customer']['email'] ?? $payload['customer_email'] ?? '');
$customerPhone = clean_str($payload['customer']['phone'] ?? $payload['customer_phone'] ?? '');
$customerCert  = clean_str($payload['customer']['cert_level'] ?? $payload['customer_cert_level'] ?? 'Beginner / No Certificate');
if ($customerCert === '') $customerCert = 'Beginner / No Certificate';

// totals + fx
$totalUsd = (float)($payload['total_usd'] ?? 0);
$totalIdr = (float)($payload['total_idr'] ?? 0);

$fxBase  = clean_str($payload['fx']['base'] ?? 'USD');
$fxQuote = clean_str($payload['fx']['quote'] ?? 'IDR');
$fxRate  = (float)($payload['fx']['rate'] ?? 0);
$fxSrc   = clean_str($payload['fx']['source'] ?? 'BCA');
$fxUpd   = clean_str($payload['fx']['updated_at'] ?? '');

$items = $payload['items'] ?? [];
if (!is_array($items) || !$items) { header("Location: {$MY_BOOKING_URL}"); exit; }

$bookingCols = tableColumns($pdo, 'bd_bookings');
$itemCols    = tableColumns($pdo, 'bd_booking_items');

$pdo->beginTransaction();
try {
  // --- upsert booking
  $st = $pdo->prepare("SELECT booking_id FROM bd_bookings WHERE booking_id = :bid LIMIT 1");
  $st->execute([':bid' => $bookingId]);
  $exists = (bool)$st->fetchColumn();

  $setParts = [];
  $insCols  = [];
  $insVals  = [];
  $params   = [':booking_id' => $bookingId];

  $addBooking = function(string $col, string $param, $val) use (&$setParts, &$insCols, &$insVals, &$params, $bookingCols) {
    if (!isset($bookingCols[strtolower($col)])) return;
    $colActual = $bookingCols[strtolower($col)];
    $setParts[] = "`$colActual` = $param";
    $insCols[]  = "`$colActual`";
    $insVals[]  = $param;
    $params[$param] = $val;
  };

  // common booking fields (only if exist)
  $addBooking('customer_name', ':customer_name', $customerName);
  $addBooking('customer_email', ':customer_email', $customerEmail);
  $addBooking('customer_phone', ':customer_phone', $customerPhone);
  $addBooking('customer_cert_level', ':customer_cert_level', $customerCert);

  $addBooking('total_usd', ':total_usd', $totalUsd);
  $addBooking('total_idr', ':total_idr', $totalIdr);

  // fx (optional)
  $addBooking('fx_base', ':fx_base', $fxBase);
  $addBooking('fx_quote', ':fx_quote', $fxQuote);
  $addBooking('fx_rate', ':fx_rate', $fxRate);
  $addBooking('fx_source', ':fx_source', $fxSrc);
  $addBooking('fx_updated_at', ':fx_updated_at', $fxUpd);

  $addBooking('payment_status', ':payment_status', 'Pending');

  $nowExpr = "NOW()";
  $hasCreated = hasCol($bookingCols, 'created_at');
  $hasUpdated = hasCol($bookingCols, 'updated_at');

  if ($exists) {
    if ($hasUpdated) $setParts[] = "`{$bookingCols['updated_at']}` = {$nowExpr}";
    $sql = "UPDATE bd_bookings SET " . implode(", ", array_unique($setParts)) . " WHERE booking_id = :booking_id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  } else {
    // ensure booking_id column exists and inserted
    if (hasCol($bookingCols, 'booking_id')) {
      $bidActual = $bookingCols['booking_id'];
      $insCols[] = "`$bidActual`";
      $insVals[] = ":booking_id";
    }
    if ($hasCreated) { $insCols[] = "`{$bookingCols['created_at']}`"; $insVals[] = $nowExpr; }
    if ($hasUpdated) { $insCols[] = "`{$bookingCols['updated_at']}`"; $insVals[] = $nowExpr; }

    if (!$insCols) {
      throw new RuntimeException("bd_bookings has no insertable columns detected.");
    }

    $sql = "INSERT INTO bd_bookings (" . implode(", ", array_unique($insCols)) . ")
            VALUES (" . implode(", ", $insVals) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  }

  // --- reset items
  $del = $pdo->prepare("DELETE FROM bd_booking_items WHERE booking_id = :bid");
  $del->execute([':bid' => $bookingId]);

  // --- detect item columns
  $colBookingId = pickCol($itemCols, ['booking_id']);
  $colProductId = pickCol($itemCols, ['product_id','pid','catalog_product_id','catalog_id','id_product']);
  $colName      = pickCol($itemCols, ['product_name','name','item_name','title']);
  $colDate      = pickCol($itemCols, ['booking_date','date','trip_date','activity_date']);
  $colQty       = pickCol($itemCols, ['quantity','qty','q','pax']);
  $colAddons    = pickCol($itemCols, ['addons_json','addons','addon_json','selected_addons','addon_keys','a']);
  $colLineUsd   = pickCol($itemCols, ['line_total_usd','subtotal_usd','total_usd','amount_usd']);
  $colLineIdr   = pickCol($itemCols, ['line_total_idr','subtotal_idr','total_idr','amount_idr']);
  $colCreatedAt = pickCol($itemCols, ['created_at','created']);

  if (!$colBookingId || !$colProductId || !$colQty) {
    $found = implode(', ', array_values($itemCols));
    throw new RuntimeException("bd_booking_items columns not compatible. Found: {$found}");
  }

  // product + addon lookup
  $getProd = $pdo->prepare("SELECT name, price_usd FROM bd_catalog_products WHERE id = :id LIMIT 1");
  $getAddon = $pdo->prepare("SELECT addon_key, name, price_usd FROM bd_catalog_addons WHERE addon_key = :k LIMIT 1");

  // build dynamic INSERT for items
  $insertCols = [];
  $insertParams = [];

  $insertCols[] = "`{$colBookingId}`"; $insertParams[] = ":booking_id";
  $insertCols[] = "`{$colProductId}`"; $insertParams[] = ":product_id";
  if ($colName)    { $insertCols[] = "`{$colName}`";    $insertParams[] = ":name"; }
  if ($colDate)    { $insertCols[] = "`{$colDate}`";    $insertParams[] = ":date"; }
  $insertCols[] = "`{$colQty}`";       $insertParams[] = ":qty";
  if ($colAddons)  { $insertCols[] = "`{$colAddons}`";  $insertParams[] = ":addons"; }
  if ($colLineUsd) { $insertCols[] = "`{$colLineUsd}`"; $insertParams[] = ":line_usd"; }
  if ($colLineIdr) { $insertCols[] = "`{$colLineIdr}`"; $insertParams[] = ":line_idr"; }
  if ($colCreatedAt) { $insertCols[] = "`{$colCreatedAt}`"; $insertParams[] = "NOW()"; }

  $sqlInsItem = "INSERT INTO bd_booking_items (" . implode(", ", $insertCols) . ")
                VALUES (" . implode(", ", $insertParams) . ")";
  $insItem = $pdo->prepare($sqlInsItem);

  foreach ($items as $it) {
    $pid = (int)($it['pid'] ?? $it['product_id'] ?? 0);
    $qty = max(1, (int)($it['q'] ?? $it['qty'] ?? 1));
    $date = clean_str($it['d'] ?? $it['date'] ?? '');

    if ($pid <= 0) continue;

    $getProd->execute([':id' => $pid]);
    $p = $getProd->fetch(PDO::FETCH_ASSOC);
    if (!$p) continue;

    $pName  = (string)($p['name'] ?? 'Item');
    $pPrice = (float)($p['price_usd'] ?? 0);

    $addonsKeys = $it['a'] ?? $it['addons'] ?? [];
    if (!is_array($addonsKeys)) $addonsKeys = [];

    $addonsNorm = [];
    $addonsTotalUsd = 0.0;

    foreach ($addonsKeys as $k) {
      $k = clean_str($k);
      if ($k === '') continue;

      $getAddon->execute([':k' => $k]);
      $a = $getAddon->fetch(PDO::FETCH_ASSOC);

      $aName = $a ? (string)($a['name'] ?? $k) : $k;
      $aUsd  = $a ? (float)($a['price_usd'] ?? 0) : 0.0;

      $addonsNorm[] = ['key' => $k, 'name' => $aName, 'price_usd' => $aUsd];
      $addonsTotalUsd += $aUsd;
    }

    $lineUsd = (($pPrice + $addonsTotalUsd) * $qty);
    $lineIdr = $fxRate > 0 ? (float)round($lineUsd * $fxRate) : 0.0;

    $bind = [
      ':booking_id' => $bookingId,
      ':product_id' => $pid,
      ':qty'        => $qty,
    ];
    if ($colName)   $bind[':name'] = $pName;
    if ($colDate)   $bind[':date'] = $date;
    if ($colAddons) $bind[':addons'] = json_encode($addonsNorm, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($colLineUsd) $bind[':line_usd'] = $lineUsd;
    if ($colLineIdr) $bind[':line_idr'] = $lineIdr;

    $insItem->execute($bind);
  }

  $pdo->commit();
} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(500);
  echo "Checkout error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
  exit;
}

header("Location: {$PAYMENT_URL}" . rawurlencode($bookingId));
exit;
