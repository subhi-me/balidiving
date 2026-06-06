<?php
// /cart/_invoice-lib.php
declare(strict_types=1);

function inv_h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function inv_money_usd(float $n): string { return '$' . number_format($n, 2); }
function inv_money_idr(float $n): string { return 'Rp ' . number_format($n, 0); }

function inv_schema_detect(PDO $pdo): array {
  $bookCols = [];
  $itemCols = [];
  $catCols  = [];

  try {
    foreach ($pdo->query("SHOW COLUMNS FROM bd_bookings")->fetchAll(PDO::FETCH_ASSOC) as $c) $bookCols[(string)$c['Field']] = true;
  } catch (Throwable $e) {}
  try {
    foreach ($pdo->query("SHOW COLUMNS FROM bd_booking_items")->fetchAll(PDO::FETCH_ASSOC) as $c) $itemCols[(string)$c['Field']] = true;
  } catch (Throwable $e) {}
  try {
    foreach ($pdo->query("SHOW COLUMNS FROM bd_catalog_products")->fetchAll(PDO::FETCH_ASSOC) as $c) $catCols[(string)$c['Field']] = true;
  } catch (Throwable $e) {}

  $phoneCol = isset($bookCols['customer_phone']) ? 'customer_phone'
           : (isset($bookCols['whatsapp']) ? 'whatsapp'
           : (isset($bookCols['phone']) ? 'phone' : ''));

  $certCol  = isset($bookCols['certificate_level']) ? 'certificate_level'
           : (isset($bookCols['cert_level']) ? 'cert_level' : '');

  $addonCol = isset($itemCols['addons_json']) ? 'addons_json'
           : (isset($itemCols['add_ons_json']) ? 'add_ons_json'
           : (isset($itemCols['addons']) ? 'addons' : ''));

  $unitUsdCol = isset($itemCols['unit_price_usd']) ? 'unit_price_usd'
            : (isset($itemCols['price_usd']) ? 'price_usd' : '');

  return [
    'book' => $bookCols,
    'item' => $itemCols,
    'cat'  => $catCols,
    'phone_col' => $phoneCol,
    'cert_col'  => $certCol,
    'addon_col' => $addonCol,
    'unit_usd_col' => $unitUsdCol,
    'has_payment_status' => isset($bookCols['payment_status']),
    'has_created_at'     => isset($bookCols['created_at']),
    'has_total_usd'      => isset($bookCols['total_usd']),
    'has_total_idr'      => isset($bookCols['total_idr']),
    'has_fx_rate'        => isset($bookCols['fx_rate']),
    'has_product_name_on_item' => isset($itemCols['product_name']),
    'has_product_id_on_item'   => isset($itemCols['product_id']),
    'has_qty'                  => isset($itemCols['quantity']),
  ];
}

function inv_parse_addons(string $raw): array {
  $raw = trim($raw);
  if ($raw === '') return ['text' => '', 'sum_usd' => 0.0, 'list' => []];

  $decoded = json_decode($raw, true);
  if (!is_array($decoded)) return ['text' => '', 'sum_usd' => 0.0, 'list' => []];

  $parts = [];
  $sum = 0.0;
  $list = [];

  foreach ($decoded as $a) {
    if (is_array($a)) {
      $name = trim((string)($a['name'] ?? $a['title'] ?? $a['label'] ?? ''));
      $price = 0.0;

      if (isset($a['price_usd'])) $price = (float)$a['price_usd'];
      elseif (isset($a['usd']))   $price = (float)$a['usd'];
      elseif (isset($a['price'])) $price = (float)$a['price'];

      if ($name !== '') {
        $sum += max(0.0, $price);
        $list[] = ['name' => $name, 'price_usd' => max(0.0, $price)];
        $parts[] = $name . (max(0.0, $price) > 0 ? " (" . inv_money_usd(max(0.0, $price)) . ")" : "");
      }
    } elseif (is_string($a) && trim($a) !== '') {
      $name = trim($a);
      $list[] = ['name' => $name, 'price_usd' => 0.0];
      $parts[] = $name;
    }
  }

  return [
    'text' => $parts ? ('Add-ons: ' . implode(', ', $parts)) : '',
    'sum_usd' => $sum,
    'list' => $list,
  ];
}

/**
 * Single source of truth:
 * Returns normalized invoice data used by BOTH HTML (/cart/payment-act.php) and PDF (/cart/invoice-pdf.php)
 */
function inv_get_invoice(PDO $pdo, string $bookingId): array {
  $schema = inv_schema_detect($pdo);

  $stB = $pdo->prepare("SELECT * FROM bd_bookings WHERE booking_id = :bid LIMIT 1");
  $stB->execute([':bid' => $bookingId]);
  $booking = $stB->fetch(PDO::FETCH_ASSOC);
  if (!$booking) throw new RuntimeException("Booking not found");

  $items = [];
  $stI = $pdo->prepare("SELECT * FROM bd_booking_items WHERE booking_id = :bid ORDER BY id ASC");
  $stI->execute([':bid' => $bookingId]);
  $items = $stI->fetchAll(PDO::FETCH_ASSOC);

  // Join catalog names if needed
  $catalogNames = [];
  if (!$schema['has_product_name_on_item'] && $schema['has_product_id_on_item'] && !empty($schema['cat']['id']) && !empty($schema['cat']['name'])) {
    $pids = [];
    foreach ($items as $it) {
      $pid = (int)($it['product_id'] ?? 0);
      if ($pid > 0) $pids[$pid] = true;
    }
    if ($pids) {
      $in = implode(',', array_fill(0, count($pids), '?'));
      try {
        $stP = $pdo->prepare("SELECT id, name FROM bd_catalog_products WHERE id IN ($in)");
        $stP->execute(array_keys($pids));
        foreach ($stP->fetchAll(PDO::FETCH_ASSOC) as $r) {
          $catalogNames[(int)$r['id']] = (string)$r['name'];
        }
      } catch (Throwable $e) {}
    }
  }

  $fxRate = $schema['has_fx_rate'] ? (float)($booking['fx_rate'] ?? 0) : 0.0;

  $lines = [];
  $calcSubtotalUsd = 0.0;
  $calcSubtotalIdr = 0.0;

  foreach ($items as $it) {
    $qty = $schema['has_qty'] ? max(1, (int)($it['quantity'] ?? 1)) : 1;

    if ($schema['has_product_name_on_item']) {
      $productName = (string)($it['product_name'] ?? 'Booking Item');
    } elseif ($schema['has_product_id_on_item']) {
      $pid = (int)($it['product_id'] ?? 0);
      $productName = $catalogNames[$pid] ?? ($pid > 0 ? "Product #{$pid}" : "Booking Item");
    } else {
      $productName = "Booking Item";
    }

    $unitUsd = 0.0;
    if ($schema['unit_usd_col'] !== '') $unitUsd = (float)($it[$schema['unit_usd_col']] ?? 0);

    $addonsText = '';
    $addonsSumUsd = 0.0;
    $addonsList = [];

    if ($schema['addon_col'] !== '') {
      $raw = (string)($it[$schema['addon_col']] ?? '');
      $addons = inv_parse_addons($raw);
      $addonsText = $addons['text'];
      $addonsSumUsd = (float)$addons['sum_usd'];
      $addonsList = (array)$addons['list'];
    }

    // Add-ons affect subtotal (per your requirement) + still shown per item
    $unitUsdFinal = $unitUsd + $addonsSumUsd;

    $lineUsd = $unitUsdFinal * $qty;
    $lineIdr = ($fxRate > 0 ? $lineUsd * $fxRate : 0.0);

    $calcSubtotalUsd += $lineUsd;
    $calcSubtotalIdr += $lineIdr;

    $lines[] = [
      'product_name' => $productName,
      'qty' => $qty,
      'unit_usd_base' => $unitUsd,
      'addons_sum_usd' => $addonsSumUsd,
      'unit_usd_final' => $unitUsdFinal,
      'line_usd' => $lineUsd,
      'line_idr' => $lineIdr,
      'addons_text' => $addonsText,
      'addons_list' => $addonsList,
    ];
  }

  $totalUsd = $schema['has_total_usd'] ? (float)($booking['total_usd'] ?? 0) : 0.0;
  $totalIdr = $schema['has_total_idr'] ? (float)($booking['total_idr'] ?? 0) : 0.0;

  if ($totalUsd <= 0) $totalUsd = $calcSubtotalUsd;
  if ($totalIdr <= 0) $totalIdr = $calcSubtotalIdr;

  $phone = '-';
  if ($schema['phone_col'] !== '') $phone = trim((string)($booking[$schema['phone_col']] ?? '')) ?: '-';

  $cert = '-';
  if ($schema['cert_col'] !== '') $cert = trim((string)($booking[$schema['cert_col']] ?? '')) ?: '-';

  $createdAt = $schema['has_created_at'] ? (string)($booking['created_at'] ?? '') : '';

  $status = $schema['has_payment_status']
    ? strtoupper(trim((string)($booking['payment_status'] ?? 'PENDING')) ?: 'PENDING')
    : 'PENDING';

  return [
    'schema' => $schema,
    'booking_id' => $bookingId,
    'booking' => $booking,
    'customer' => [
      'name' => (string)($booking['customer_name'] ?? 'Guest'),
      'email' => (string)($booking['customer_email'] ?? '-'),
      'phone' => $phone,
      'certificate_level' => $cert,
    ],
    'meta' => [
      'payment_status' => $status,
      'created_at' => $createdAt,
      'fx_rate' => $fxRate,
    ],
    'lines' => $lines,
    'totals' => [
      'subtotal_usd_calc' => $calcSubtotalUsd,
      'subtotal_idr_calc' => $calcSubtotalIdr,
      'total_usd' => $totalUsd,
      'total_idr' => $totalIdr,
    ],
  ];
}
