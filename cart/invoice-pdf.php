<?php
// /cart/invoice-pdf.php — MINIMAL OFFICIAL INVOICE (IDR ONLY, NO FX RATE)
// - Minimal, no double info
// - Shows Certificate Level
// - Item label: "Category - Dive Site"
// - Shows IDR per item: Unit + Amount
// Single source of truth: inv_get_invoice() from /cart/_invoice-lib.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

require __DIR__ . '/../template/database/main-cart.php';
require __DIR__ . '/_invoice-lib.php';

$bookingId = trim((string)($_GET['booking_id'] ?? ''));
if ($bookingId === '') {
  header("Location: https://balidiving.com/cart/my-booking");
  exit;
}

// =====================================
// TCPDF Loader (Hostinger-safe)
// =====================================
$autoload1 = __DIR__ . '/../vendor/autoload.php';
$autoload2 = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload1)) require_once $autoload1;
elseif (file_exists($autoload2)) require_once $autoload2;

$tcpdfManual = __DIR__ . '/lib/tcpdf.php';
if (!class_exists('TCPDF') && file_exists($tcpdfManual)) require_once $tcpdfManual;

if (!class_exists('TCPDF')) {
  http_response_code(500);
  header("Content-Type: text/plain; charset=utf-8");
  echo "TCPDF not installed.\nExpected file: /cart/lib/tcpdf.php\n";
  exit;
}

// =====================================
// Load invoice data (single source)
// =====================================
try {
  $inv = inv_get_invoice($pdo, $bookingId);
} catch (Throwable $e) {
  header("Location: https://balidiving.com/cart/my-booking");
  exit;
}

$customer = (array)($inv['customer'] ?? []);
$meta     = (array)($inv['meta'] ?? []);
$lines    = (array)($inv['lines'] ?? []);
$totals   = (array)($inv['totals'] ?? []);
$schema   = (array)($inv['schema'] ?? []);

// =====================================
// Company identity
// =====================================
$companyName    = "Bali Diving";
$companyAddress = "Bali, Indonesia";
$companyEmail   = "info@balidiving.com";
$brandSite      = "balidiving.com";

// Invoice meta (single block)
$invoiceNo = "INV-" . preg_replace('/[^A-Za-z0-9\-]/', '', (string)($inv['booking_id'] ?? $bookingId));
$bookingNo = (string)($inv['booking_id'] ?? $bookingId);

$createdAtRaw = trim((string)($meta['created_at'] ?? ''));
$issuedLabel  = ($createdAtRaw !== '' ? date('d M Y H:i', strtotime($createdAtRaw)) : date('d M Y H:i')) . " WITA";

$paymentStatus = strtoupper(trim((string)($meta['payment_status'] ?? 'PENDING')) ?: 'PENDING');

// Total IDR: prefer DB total_idr, fallback calc
$totalIdr = (float)($totals['total_idr'] ?? 0);
if ($totalIdr <= 0) $totalIdr = (float)($totals['total_idr_calc'] ?? 0);
if ($totalIdr <= 0) $totalIdr = (float)($totals['subtotal_idr_calc'] ?? 0);

// Guest
$guestName  = trim((string)($customer['name'] ?? ''));
$guestEmail = trim((string)($customer['email'] ?? ''));
$guestPhone = trim((string)($customer['phone'] ?? ''));
$guestCert  = trim((string)($customer['certificate_level'] ?? ''));

// =====================================
// Category map (best effort)
// =====================================
$categoryByProductId = [];
try {
  $hasProductIdOnItem = (bool)($schema['has_product_id_on_item'] ?? false);
  if ($hasProductIdOnItem) {
    $pids = [];
    $st = $pdo->prepare("SELECT product_id FROM bd_booking_items WHERE booking_id = :bid");
    $st->execute([':bid' => $bookingId]);
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
      $pid = (int)($r['product_id'] ?? 0);
      if ($pid > 0) $pids[$pid] = true;
    }
    if ($pids) {
      $in = implode(',', array_fill(0, count($pids), '?'));
      $st2 = $pdo->prepare("SELECT id, category FROM bd_catalog_products WHERE id IN ($in)");
      $st2->execute(array_keys($pids));
      foreach ($st2->fetchAll(PDO::FETCH_ASSOC) as $r2) {
        $pid = (int)($r2['id'] ?? 0);
        $cat = trim((string)($r2['category'] ?? ''));
        if ($pid > 0 && $cat !== '') $categoryByProductId[$pid] = $cat;
      }
    }
  }
} catch (Throwable $e) {
  // ignore
}

function build_item_label(PDO $pdo, string $bookingId, array $categoryByProductId, int $index, string $productName): string {
  static $pidOrder = null;
  if ($pidOrder === null) {
    $pidOrder = [];
    try {
      $st = $pdo->prepare("SELECT product_id FROM bd_booking_items WHERE booking_id = :bid ORDER BY id ASC");
      $st->execute([':bid' => $bookingId]);
      foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) $pidOrder[] = (int)($r['product_id'] ?? 0);
    } catch (Throwable $e) {
      $pidOrder = [];
    }
  }
  $pid  = (int)($pidOrder[$index] ?? 0);
  $cat  = ($pid > 0 && isset($categoryByProductId[$pid])) ? (string)$categoryByProductId[$pid] : '';
  $site = trim($productName);
  return ($cat !== '') ? ($cat . " - " . $site) : $site;
}

function safe_txt(string $v): string {
  $v = trim($v);
  return $v === '' ? '-' : $v;
}

// =====================================
// PDF setup
// =====================================
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator($companyName);
$pdf->SetAuthor($companyName);
$pdf->SetTitle('Invoice ' . $invoiceNo);
$pdf->SetMargins(12, 14, 12);
$pdf->SetAutoPageBreak(true, 14);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Minimal CSS
$css = '
<style>
  .muted { color:#6b7280; }
  .tiny { font-size:9px; }
  .title { font-size:16px; font-weight:bold; color:#0f172a; }
  .h1 { font-size:12px; font-weight:bold; color:#0f172a; }
  .box { border:1px solid #e5e7eb; border-radius:10px; }
  .thead { background-color:#f8fafc; font-weight:bold; color:#334155; }
  .hr { border-bottom:1px solid #eef2f7; }
</style>
';

// =====================================
// Build HTML (no duplicates)
// =====================================
$html = $css . '
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td width="70%">
      <div class="title">'.inv_h($companyName).'</div>
      <div class="tiny muted">'.inv_h($companyAddress).' • '.inv_h($companyEmail).'</div>
      <div class="tiny muted">'.inv_h($brandSite).'</div>
    </td>
    <td width="30%" align="right">
      <div class="tiny muted">Payment Status</div>
      <div style="font-size:12px; font-weight:bold; color:#0f172a;">'.inv_h($paymentStatus).'</div>
    </td>
  </tr>
</table>

<div style="height:10px;"></div>

<table width="100%" cellpadding="10" cellspacing="0" class="box">
  <tr>
    <td width="52%">
      <div class="h1">Invoice</div>
      <div class="tiny muted">Invoice No: <b>'.inv_h($invoiceNo).'</b></div>
      <div class="tiny muted">Booking ID: <b>'.inv_h($bookingNo).'</b></div>
      <div class="tiny muted">Issued: <b>'.inv_h($issuedLabel).'</b></div>
      <div class="tiny muted">Currency: <b>IDR</b></div>
    </td>
    <td width="48%">
      <div class="tiny muted">Guest</div>
      <div style="font-size:12px; font-weight:bold; color:#0f172a;">'.inv_h(safe_txt($guestName)).'</div>
      <div style="font-size:10px; color:#334155;">'.inv_h(safe_txt($guestEmail)).'</div>
      <div style="font-size:10px; color:#334155;">Phone/WhatsApp: <b>'.inv_h(safe_txt($guestPhone)).'</b></div>
      <div style="font-size:10px; color:#334155;">Certificate Level: <b>'.inv_h(safe_txt($guestCert)).'</b></div>
    </td>
  </tr>
</table>

<div style="height:12px;"></div>

<div class="h1">Items</div>
<div style="height:6px;"></div>

<table width="100%" cellpadding="7" cellspacing="0" style="border:1px solid #e5e7eb; border-radius:10px;">
  <tr class="thead">
    <td width="56%">Description</td>
    <td width="10%" align="right">Qty</td>
    <td width="17%" align="right">Unit</td>
    <td width="17%" align="right">Amount</td>
  </tr>
';

if (!$lines) {
  $html .= '<tr><td colspan="4" class="muted" style="padding:14px; text-align:center;">No items.</td></tr>';
} else {
  $idx = 0;
  foreach ($lines as $ln) {
    $qty     = max(1, (int)($ln['qty'] ?? 1));
    $lineIdr = (float)($ln['line_idr'] ?? 0);
    $unitIdr = ($lineIdr > 0 && $qty > 0) ? ($lineIdr / $qty) : 0;

    $addonsText = trim((string)($ln['addons_text'] ?? ''));
    $addonLine  = ($addonsText !== '')
      ? '<div class="tiny muted" style="margin-top:2px;">'.inv_h($addonsText).'</div>'
      : '';

    $label = build_item_label($pdo, $bookingId, $categoryByProductId, $idx, (string)($ln['product_name'] ?? ''));

    $html .= '
      <tr class="hr">
        <td width="56%"><b style="color:#0f172a;">'.inv_h($label).'</b>'.$addonLine.'</td>
        <td width="10%" align="right" style="color:#0f172a; font-weight:bold;">'.$qty.'</td>
        <td width="17%" align="right" style="color:#0f172a; font-weight:bold;">'.($unitIdr > 0 ? inv_money_idr($unitIdr) : 'Rp -').'</td>
        <td width="17%" align="right" style="color:#0f172a; font-weight:bold;">'.($lineIdr > 0 ? inv_money_idr($lineIdr) : 'Rp -').'</td>
      </tr>
    ';
    $idx++;
  }
}

$html .= '
</table>

<div style="height:10px;"></div>

<table width="100%" cellpadding="10" cellspacing="0" class="box">
  <tr>
    <td width="60%">
      <div class="tiny muted">
        Electronic invoice • Valid without signature
      </div>
    </td>
    <td width="40%" align="right">
      <div class="tiny muted">Total (IDR)</div>
      <div style="font-size:18px; font-weight:bold; color:#0f172a;">'.($totalIdr > 0 ? inv_money_idr($totalIdr) : 'Rp -').'</div>
    </td>
  </tr>
</table>

<div style="height:8px;"></div>
<div class="tiny muted">'.inv_h($brandSite).' • '.inv_h($invoiceNo).'</div>
';

$pdf->writeHTML($html, true, false, true, false, '');

$filename = $invoiceNo . ".pdf";
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.$filename.'"');
$pdf->Output($filename, 'I');
exit;
