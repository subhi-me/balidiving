<?php
// /cart/booking-list.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

require __DIR__ . '/../template/database/main-cart.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function csrf_token(): string {
  if (empty($_SESSION['csrf_booking_list'])) {
    $_SESSION['csrf_booking_list'] = bin2hex(random_bytes(32));
  }
  return (string)$_SESSION['csrf_booking_list'];
}
function csrf_check(?string $t): bool {
  return is_string($t) && $t !== '' && hash_equals((string)($_SESSION['csrf_booking_list'] ?? ''), $t);
}
function buildQuery(array $extra = []): string {
  $merged = array_merge($_GET, $extra);
  foreach ($merged as $k => $v) if ($v === null || $v === '') unset($merged[$k]);
  return http_build_query($merged);
}
function redirect_back(string $fallback = '/cart/booking-list.php'): never {
  $back = (string)($_POST['return_to'] ?? '');
  if ($back !== '' && str_starts_with($back, '/')) { header("Location: {$back}"); exit; }
  header("Location: {$fallback}"); exit;
}
function badgeClass(string $status): string {
  $s = strtolower($status);
  return match ($s) {
    'paid'      => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    'pending'   => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    'cancelled' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
    'failed'    => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
    'refunded'  => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200',
    default     => 'bg-slate-50 text-slate-700 ring-1 ring-slate-200',
  };
}

// =====================================================
// Schema detection (avoid crash if column missing)
// =====================================================
$colSet = [];
try {
  $cols = $pdo->query("SHOW COLUMNS FROM bd_bookings")->fetchAll(PDO::FETCH_ASSOC);
  foreach ($cols as $c) $colSet[(string)$c['Field']] = true;
} catch (Throwable $e) { /* ignore */ }

$hasPaymentStatus   = isset($colSet['payment_status']);
$hasPaymentUpdated  = isset($colSet['payment_updated_at']);
$hasCreatedAt       = isset($colSet['created_at']);
$hasTotalsUsd       = isset($colSet['total_usd']);
$hasTotalsIdr       = isset($colSet['total_idr']);

$allowedStatuses = ['pending','paid','cancelled','failed','refunded'];

// =====================================================
// POST actions
// =====================================================
$csrf = csrf_token();

$returnTo = "/cart/booking-list.php";
$qs = buildQuery([]);
if ($qs !== '') $returnTo .= "?" . $qs;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = trim((string)($_POST['action'] ?? ''));
  $token  = (string)($_POST['csrf'] ?? '');
  $bid    = trim((string)($_POST['booking_id'] ?? ''));

  if (!csrf_check($token)) { http_response_code(403); echo "Invalid CSRF token"; exit; }
  if ($bid === '') { http_response_code(400); echo "Missing booking_id"; exit; }

  if ($action === 'edit_booking') {
    $name  = trim((string)($_POST['customer_name'] ?? ''));
    $email = trim((string)($_POST['customer_email'] ?? ''));
    $newStatus = strtolower(trim((string)($_POST['payment_status'] ?? '')));

    if ($name === '') { http_response_code(400); echo "Customer name is required"; exit; }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { http_response_code(400); echo "Valid email is required"; exit; }

    $set = ["customer_name = :nm", "customer_email = :em"];
    $p = [':nm' => $name, ':em' => $email, ':bid' => $bid];

    if ($hasPaymentStatus) {
      if ($newStatus === '') $newStatus = 'pending';
      if (!in_array($newStatus, $allowedStatuses, true)) { http_response_code(400); echo "Invalid status"; exit; }
      $set[] = "payment_status = :st";
      $p[':st'] = $newStatus;
      if ($hasPaymentUpdated) $set[] = "payment_updated_at = NOW()";
    }

    $sql = "UPDATE bd_bookings SET " . implode(", ", $set) . " WHERE booking_id = :bid LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute($p);

    redirect_back();
  }

  if ($action === 'delete_booking') {
    try {
      $pdo->beginTransaction();
      $pdo->prepare("DELETE FROM bd_booking_items WHERE booking_id = :bid")->execute([':bid' => $bid]);
      $pdo->prepare("DELETE FROM bd_bookings WHERE booking_id = :bid LIMIT 1")->execute([':bid' => $bid]);
      $pdo->commit();
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      http_response_code(500);
      echo "Delete failed: " . h($e->getMessage());
      exit;
    }
    redirect_back();
  }

  http_response_code(400);
  echo "Unknown action";
  exit;
}

// =====================================================
// GET filters
// =====================================================
$q        = trim((string)($_GET['q'] ?? ''));
$status   = trim((string)($_GET['status'] ?? ''));
$dateFrom = trim((string)($_GET['from'] ?? ''));
$dateTo   = trim((string)($_GET['to'] ?? ''));
$sort     = trim((string)($_GET['sort'] ?? 'new'));
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 20;
$offset   = ($page - 1) * $perPage;

$validDate = function(string $d): bool {
  if ($d === '') return true;
  $dt = DateTime::createFromFormat('Y-m-d', $d);
  return $dt && $dt->format('Y-m-d') === $d;
};
if (!$hasCreatedAt) { $dateFrom = ''; $dateTo = ''; }
if (!$validDate($dateFrom)) $dateFrom = '';
if (!$validDate($dateTo))   $dateTo = '';
if (!$hasPaymentStatus) $status = '';

$orderBy = "b.id DESC";
switch ($sort) {
  case 'old':        $orderBy = "b.id ASC"; break;
  case 'total_desc': $orderBy = ($hasTotalsUsd ? "b.total_usd DESC, b.id DESC" : "b.id DESC"); break;
  case 'total_asc':  $orderBy = ($hasTotalsUsd ? "b.total_usd ASC, b.id DESC" : "b.id DESC"); break;
  default:           $orderBy = "b.id DESC";
}

$where = [];
$params = [];

if ($q !== '') {
  $where[] = "(b.booking_id LIKE :q OR b.customer_name LIKE :q OR b.customer_email LIKE :q)";
  $params[':q'] = "%{$q}%";
}
if ($hasPaymentStatus && $status !== '') {
  $where[] = "b.payment_status = :status";
  $params[':status'] = $status;
}
if ($hasCreatedAt && $dateFrom !== '') {
  $where[] = "DATE(b.created_at) >= :from";
  $params[':from'] = $dateFrom;
}
if ($hasCreatedAt && $dateTo !== '') {
  $where[] = "DATE(b.created_at) <= :to";
  $params[':to'] = $dateTo;
}
$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

// Summary
$summary = ['total_bookings'=>0,'pending_count'=>0,'paid_count'=>0,'total_usd'=>0.0,'total_idr'=>0.0];
try {
  $sumSql = "
    SELECT
      COUNT(*) AS total_bookings,
      " . ($hasPaymentStatus ? "
      COALESCE(SUM(CASE WHEN LOWER(COALESCE(b.payment_status,'')) = 'pending' THEN 1 ELSE 0 END),0) AS pending_count,
      COALESCE(SUM(CASE WHEN LOWER(COALESCE(b.payment_status,'')) = 'paid' THEN 1 ELSE 0 END),0) AS paid_count,
      " : "0 AS pending_count, 0 AS paid_count,") . "
      " . ($hasTotalsUsd ? "COALESCE(SUM(COALESCE(b.total_usd,0)),0) AS total_usd," : "0 AS total_usd,") . "
      " . ($hasTotalsIdr ? "COALESCE(SUM(COALESCE(b.total_idr,0)),0) AS total_idr" : "0 AS total_idr") . "
    FROM bd_bookings b
    $whereSql
  ";
  $stSum = $pdo->prepare($sumSql);
  $stSum->execute($params);
  $rowSum = $stSum->fetch(PDO::FETCH_ASSOC);
  if ($rowSum) {
    $summary['total_bookings'] = (int)($rowSum['total_bookings'] ?? 0);
    $summary['pending_count']  = (int)($rowSum['pending_count'] ?? 0);
    $summary['paid_count']     = (int)($rowSum['paid_count'] ?? 0);
    $summary['total_usd']      = (float)($rowSum['total_usd'] ?? 0);
    $summary['total_idr']      = (float)($rowSum['total_idr'] ?? 0);
  }
} catch (Throwable $e) { /* keep zeros */ }

$totalRows  = (int)$summary['total_bookings'];
$totalPages = max(1, (int)ceil($totalRows / $perPage));
if ($page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

// List
$listSql = "
  SELECT
    b.*,
    COALESCE((SELECT SUM(i.quantity) FROM bd_booking_items i WHERE i.booking_id = b.booking_id),0) AS item_qty_total,
    COALESCE((SELECT COUNT(*) FROM bd_booking_items i WHERE i.booking_id = b.booking_id),0) AS item_lines
  FROM bd_bookings b
  $whereSql
  ORDER BY $orderBy
  LIMIT :limit OFFSET :offset
";
$stList = $pdo->prepare($listSql);
foreach ($params as $k => $v) $stList->bindValue($k, $v);
$stList->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stList->bindValue(':offset', $offset, PDO::PARAM_INT);
$stList->execute();
$bookings = $stList->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Bookings Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @media print { .no-print{display:none!important} body{background:#fff!important} .print-flat{box-shadow:none!important} }
  </style>
</head>

<body class="bg-slate-50 text-slate-900">
  <div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
      <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Bookings Dashboard</h1>
        <p class="text-sm text-slate-600">Booking ID click = open Invoice PDF. Dropdown: Invoice PDF / Edit / Delete.</p>
      </div>
      <div class="flex gap-2 no-print">
        <a href="/cart/booking-list.php" class="px-3 py-2 rounded-xl bg-white ring-1 ring-slate-200 text-sm font-semibold hover:bg-slate-50">Reset Filters</a>
        <button onclick="window.print()" class="px-3 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Print / Save PDF</button>
      </div>
    </div>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
      <div class="bg-white rounded-2xl p-4 ring-1 ring-slate-200 print-flat">
        <div class="text-xs text-slate-500 font-semibold">Total Bookings</div>
        <div class="text-2xl font-extrabold mt-1"><?= (int)$summary['total_bookings'] ?></div>
      </div>
      <div class="bg-white rounded-2xl p-4 ring-1 ring-slate-200 print-flat">
        <div class="text-xs text-slate-500 font-semibold">Pending</div>
        <div class="text-2xl font-extrabold mt-1"><?= (int)$summary['pending_count'] ?></div>
      </div>
      <div class="bg-white rounded-2xl p-4 ring-1 ring-slate-200 print-flat">
        <div class="text-xs text-slate-500 font-semibold">Paid</div>
        <div class="text-2xl font-extrabold mt-1"><?= (int)$summary['paid_count'] ?></div>
      </div>
      <div class="bg-white rounded-2xl p-4 ring-1 ring-slate-200 print-flat">
        <div class="text-xs text-slate-500 font-semibold">Total USD</div>
        <div class="text-xl font-extrabold mt-1">$<?= number_format((float)$summary['total_usd'], 2) ?></div>
      </div>
      <div class="bg-white rounded-2xl p-4 ring-1 ring-slate-200 print-flat">
        <div class="text-xs text-slate-500 font-semibold">Total IDR</div>
        <div class="text-xl font-extrabold mt-1">Rp <?= number_format((float)$summary['total_idr'], 0) ?></div>
      </div>
    </div>

    <!-- FILTERS -->
    <form method="get" class="bg-white rounded-2xl p-4 ring-1 ring-slate-200 mb-6 no-print print-flat">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div class="md:col-span-2">
          <label class="text-xs font-semibold text-slate-600">Search</label>
          <input name="q" value="<?= h($q) ?>" placeholder="booking_id / name / email"
                 class="mt-1 w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0" />
        </div>

        <div>
          <label class="text-xs font-semibold text-slate-600">Status</label>
          <select name="status" class="mt-1 w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0" <?= $hasPaymentStatus ? '' : 'disabled' ?>>
            <option value="">All</option>
            <?php foreach ($allowedStatuses as $s): ?>
              <option value="<?= h($s) ?>" <?= $status===$s?'selected':'' ?>><?= h(ucfirst($s)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="text-xs font-semibold text-slate-600">From</label>
          <input type="date" name="from" value="<?= h($dateFrom) ?>"
                 class="mt-1 w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0"
                 <?= $hasCreatedAt ? '' : 'disabled' ?> />
        </div>

        <div>
          <label class="text-xs font-semibold text-slate-600">To</label>
          <input type="date" name="to" value="<?= h($dateTo) ?>"
                 class="mt-1 w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0"
                 <?= $hasCreatedAt ? '' : 'disabled' ?> />
        </div>
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4">
        <div class="flex items-center gap-2">
          <label class="text-xs font-semibold text-slate-600">Sort</label>
          <select name="sort" class="rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0 text-sm">
            <option value="new" <?= $sort==='new'?'selected':'' ?>>Newest</option>
            <option value="old" <?= $sort==='old'?'selected':'' ?>>Oldest</option>
            <option value="total_desc" <?= $sort==='total_desc'?'selected':'' ?>>Total (High → Low)</option>
            <option value="total_asc" <?= $sort==='total_asc'?'selected':'' ?>>Total (Low → High)</option>
          </select>
        </div>

        <div class="flex gap-2">
          <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
          <a href="/cart/booking-list.php" class="px-4 py-2 rounded-xl bg-white ring-1 ring-slate-200 text-sm font-semibold hover:bg-slate-50">Clear</a>
        </div>
      </div>

      <div class="text-xs text-slate-500 mt-3">
        Showing <b><?= (int)$totalRows ?></b> result(s). Page <b><?= (int)$page ?></b> of <b><?= (int)$totalPages ?></b>.
      </div>
    </form>

    <!-- LIST -->
    <div class="bg-white rounded-2xl ring-1 ring-slate-200 overflow-hidden print-flat">
      <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
        <div class="font-extrabold">Bookings</div>
        <div class="text-xs text-slate-500 no-print">Invoice PDF is the default view.</div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="text-left px-4 py-3 font-extrabold">Booking ID</th>
              <th class="text-left px-4 py-3 font-extrabold">Customer</th>
              <th class="text-left px-4 py-3 font-extrabold">Email</th>
              <th class="text-left px-4 py-3 font-extrabold">Payment Status</th>
              <th class="text-right px-4 py-3 font-extrabold">Items</th>
              <th class="text-right px-4 py-3 font-extrabold">Total USD</th>
              <th class="text-right px-4 py-3 font-extrabold">Total IDR</th>
              <th class="text-left px-4 py-3 font-extrabold">Created</th>
              <th class="text-right px-4 py-3 font-extrabold">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <?php if (!$bookings): ?>
              <tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">No bookings found.</td></tr>
            <?php else: ?>
              <?php foreach ($bookings as $b): ?>
                <?php
                  $bid = (string)($b['booking_id'] ?? '');
                  $name = (string)($b['customer_name'] ?? '');
                  $email = (string)($b['customer_email'] ?? '');
                  $st = $hasPaymentStatus ? strtolower((string)($b['payment_status'] ?? 'pending')) : 'n/a';
                  $created = $hasCreatedAt ? (string)($b['created_at'] ?? '-') : '-';
                  $pdfUrl = "/cart/invoice-pdf.php?booking_id=" . urlencode($bid);
                ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3 font-extrabold">
                    <a class="hover:underline" href="<?= h($pdfUrl) ?>" target="_blank" rel="noopener noreferrer">
                      <?= h($bid) ?>
                    </a>
                    <div class="text-[11px] text-slate-500 font-semibold">Open Invoice PDF</div>
                  </td>
                  <td class="px-4 py-3 font-semibold"><?= h($name ?: '-') ?></td>
                  <td class="px-4 py-3 text-slate-700"><?= h($email ?: '-') ?></td>
                  <td class="px-4 py-3">
                    <?php if ($hasPaymentStatus): ?>
                      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold <?= h(badgeClass($st)) ?>">
                        <?= h(strtoupper($st)) ?>
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-slate-50 text-slate-700 ring-1 ring-slate-200">N/A</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <span class="font-extrabold"><?= (int)($b['item_lines'] ?? 0) ?></span>
                    <span class="text-slate-500">(qty <?= (int)($b['item_qty_total'] ?? 0) ?>)</span>
                  </td>
                  <td class="px-4 py-3 text-right font-extrabold">
                    <?= $hasTotalsUsd ? ('$' . number_format((float)($b['total_usd'] ?? 0), 2)) : '-' ?>
                  </td>
                  <td class="px-4 py-3 text-right font-extrabold">
                    <?= $hasTotalsIdr ? ('Rp ' . number_format((float)($b['total_idr'] ?? 0), 0)) : '-' ?>
                  </td>
                  <td class="px-4 py-3 text-slate-700"><?= h($created) ?></td>
                  <td class="px-4 py-3 text-right no-print">
                    <select
                      class="rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0 text-sm"
                      data-bid="<?= h($bid) ?>"
                      data-name="<?= h($name) ?>"
                      data-email="<?= h($email) ?>"
                      data-status="<?= h($st) ?>"
                      onchange="handleActionChange(this)"
                    >
                      <option value="" selected>Action</option>
                      <option value="invoice_pdf">Invoice (PDF)</option>
                      <option value="edit">Edit</option>
                      <option value="delete">Delete</option>
                    </select>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-200 no-print">
        <div class="text-xs text-slate-500">
          Page <b><?= (int)$page ?></b> of <b><?= (int)$totalPages ?></b> • Total <b><?= (int)$totalRows ?></b>
        </div>
        <div class="flex gap-2">
          <?php $prev=max(1,$page-1); $next=min($totalPages,$page+1); ?>
          <a class="px-3 py-2 rounded-xl bg-white ring-1 ring-slate-200 text-xs font-extrabold hover:bg-slate-50 <?= $page<=1?'pointer-events-none opacity-50':'' ?>"
             href="/cart/booking-list.php?<?= h(buildQuery(['page'=>$prev])) ?>">Prev</a>
          <a class="px-3 py-2 rounded-xl bg-white ring-1 ring-slate-200 text-xs font-extrabold hover:bg-slate-50 <?= $page>=$totalPages?'pointer-events-none opacity-50':'' ?>"
             href="/cart/booking-list.php?<?= h(buildQuery(['page'=>$next])) ?>">Next</a>
        </div>
      </div>
    </div>

  </div>

  <!-- OFFCANVAS EDIT -->
  <div id="offcanvasBackdrop" class="hidden fixed inset-0 bg-black/40 z-40" onclick="closeOffcanvas()"></div>

  <aside id="offcanvas" class="fixed top-0 right-0 h-full w-full sm:w-[520px] bg-white z-50 translate-x-full transition-transform duration-200 shadow-2xl">
    <div class="h-full flex flex-col">
      <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
        <div>
          <div class="text-sm text-slate-500 font-semibold">Edit Booking</div>
          <div class="text-xl font-extrabold" id="ocTitle">—</div>
        </div>
        <button class="px-3 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800" onclick="closeOffcanvas()">Close</button>
      </div>

      <form method="post" class="p-5 flex-1 overflow-auto" onsubmit="return confirmEditSubmit();">
        <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
        <input type="hidden" name="action" value="edit_booking">
        <input type="hidden" name="booking_id" id="ocBookingId" value="">
        <input type="hidden" name="return_to" value="<?= h($returnTo) ?>">

        <div class="space-y-4">
          <div>
            <label class="text-xs font-semibold text-slate-600">Customer Name</label>
            <input name="customer_name" id="ocName" class="mt-1 w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0" required>
          </div>

          <div>
            <label class="text-xs font-semibold text-slate-600">Email</label>
            <input type="email" name="customer_email" id="ocEmail" class="mt-1 w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0" required>
          </div>

          <div>
            <label class="text-xs font-semibold text-slate-600">Payment Status</label>
            <?php if ($hasPaymentStatus): ?>
              <select name="payment_status" id="ocStatus" class="mt-1 w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-0">
                <?php foreach ($allowedStatuses as $s): ?>
                  <option value="<?= h($s) ?>"><?= h(ucfirst($s)) ?></option>
                <?php endforeach; ?>
              </select>
              <?php if ($hasPaymentUpdated): ?>
                <div class="text-xs text-slate-500 mt-2">Note: payment_updated_at will be set automatically.</div>
              <?php endif; ?>
            <?php else: ?>
              <div class="mt-1 text-sm text-rose-600 font-semibold">
                payment_status column not found. Add it in DB:
                <span class="font-mono text-xs text-slate-700 block mt-1">ALTER TABLE bd_bookings ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'pending' AFTER customer_email;</span>
              </div>
            <?php endif; ?>
          </div>

          <div class="pt-2 flex gap-2">
            <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-extrabold hover:bg-slate-800">Save Changes</button>
            <button type="button" class="px-4 py-2 rounded-xl bg-white ring-1 ring-slate-200 text-sm font-extrabold hover:bg-slate-50" onclick="closeOffcanvas()">Cancel</button>
          </div>

          <div class="text-xs text-slate-500">
            Editable fields: Customer, Email, Payment Status.
          </div>
        </div>
      </form>
    </div>
  </aside>

  <!-- Hidden DELETE form -->
  <form id="deleteForm" method="post" class="hidden">
    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
    <input type="hidden" name="action" value="delete_booking">
    <input type="hidden" name="booking_id" id="delBookingId" value="">
    <input type="hidden" name="return_to" value="<?= h($returnTo) ?>">
  </form>

  <script>
    const OFF = document.getElementById('offcanvas');
    const BD  = document.getElementById('offcanvasBackdrop');

    function openOffcanvas(payload){
      document.getElementById('ocTitle').textContent = payload.bid || '—';
      document.getElementById('ocBookingId').value   = payload.bid || '';
      document.getElementById('ocName').value        = payload.name || '';
      document.getElementById('ocEmail').value       = payload.email || '';

      const sel = document.getElementById('ocStatus');
      if (sel) {
        const st = (payload.status || '').toLowerCase();
        const ok = Array.from(sel.options).some(o => o.value === st);
        sel.value = ok ? st : 'pending';
      }

      BD.classList.remove('hidden');
      OFF.classList.remove('translate-x-full');
      document.body.style.overflow = 'hidden';
    }

    function closeOffcanvas(){
      BD.classList.add('hidden');
      OFF.classList.add('translate-x-full');
      document.body.style.overflow = '';
    }

    function confirmEditSubmit(){
      const bid = document.getElementById('ocBookingId').value || '';
      return confirm("Save changes for booking " + bid + "?");
    }

    function handleActionChange(sel){
      const action = sel.value;
      const bid    = sel.dataset.bid || '';
      const name   = sel.dataset.name || '';
      const email  = sel.dataset.email || '';
      const status = sel.dataset.status || '';

      sel.value = ""; // reset
      if (!bid) return;

      if (action === 'invoice_pdf') {
        window.open("/cart/invoice-pdf.php?booking_id=" + encodeURIComponent(bid), "_blank", "noopener,noreferrer");
        return;
      }

      if (action === 'edit') {
        openOffcanvas({ bid, name, email, status });
        return;
      }

      if (action === 'delete') {
        const ok = confirm(
          "Delete booking " + bid + "?\n\n" +
          "This will remove the booking AND all booking items.\n" +
          "This action cannot be undone."
        );
        if (!ok) return;
        document.getElementById('delBookingId').value = bid;
        document.getElementById('deleteForm').submit();
        return;
      }
    }

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeOffcanvas();
    });
  </script>
</body>
</html>
