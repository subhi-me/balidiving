    <?php include('../template/start.php')?>
<?php
/* ===========================================
   SINGLE PAGE: Booking UI + Lead Gateway API
   =========================================== */

/* ===== DEBUG (matikan di produksi) ===== */
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

/* ===== DB CONFIG ===== */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/* ===== PDO ===== */
function pdo_conn(){
  static $pdo=null;
  if($pdo===null){
    $dsn = "mysql:host=".$GLOBALS['DB_HOST'].";dbname=".$GLOBALS['DB_NAME'].";charset=utf8mb4";
    $opt = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASS'], $opt);
  }
  return $pdo;
}

/* ===== Helpers ===== */
function json_headers(){
  header('Content-Type: application/json; charset=UTF-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');
}
function uid(){ return strtoupper(dechex(time())).substr(strtoupper(md5(uniqid('', true))), 0, 8); }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* ===== Ensure schema (idempotent) ===== */
function table_exists(PDO $pdo, string $table): bool {
  $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t LIMIT 1";
  $st = $pdo->prepare($sql); $st->execute([':t'=>$table]); return (bool)$st->fetchColumn();
}
function col_exists(PDO $pdo, string $table, string $col): bool {
  $sql = "SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1";
  $st = $pdo->prepare($sql); $st->execute([':t'=>$table,':c'=>$col]); return (bool)$st->fetchColumn();
}
function qexec(PDO $pdo, $sql){
  try { $pdo->exec($sql); } catch(Throwable $e){ error_log("SQL ERR: ".$e->getMessage()." IN: ".$sql); }
}

/* ===== API: lead_gateway (same page) ===== */
if (($_GET['action'] ?? '') === 'lead_gateway' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $isJson = isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
  $in = $isJson ? (json_decode(file_get_contents('php://input'), true) ?: []) : $_POST;

  $dive_site     = trim((string)($in['dive_site'] ?? 'Dive Trip'));
  $booking_date  = trim((string)($in['booking_date'] ?? ''));
  $participants  = (int)($in['participants'] ?? 1);
  $name          = trim((string)($in['name'] ?? ''));
  $email         = trim((string)($in['email'] ?? ''));
  $phone         = trim((string)($in['phone'] ?? ''));
  $note          = trim((string)($in['note'] ?? ''));
  $coupon_code   = trim((string)($in['coupon_code'] ?? ''));
  $coupon_value  = (float)($in['coupon_value'] ?? 0);
  $price_person  = (float)($in['price_per_person'] ?? 0);
  $total_amount  = (float)($in['total_amount'] ?? 0);

  if ($name==='' || $email==='' || $phone==='' || $booking_date==='') {
    json_headers(); echo json_encode(['ok'=>false,'error'=>'Missing required fields']); exit;
  }

  try{
    $pdo = pdo_conn();

    /* Bootstrap schema (safe to run repeatedly) */
    if(!table_exists($pdo,'leads')){
      qexec($pdo, "CREATE TABLE `leads`(
        id VARCHAR(64) PRIMARY KEY,
        `column` VARCHAR(32) NOT NULL DEFAULT 'leads',
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NULL,
        phone VARCHAR(64) NULL,
        country VARCHAR(64) NULL,
        source VARCHAR(64) NULL,
        package VARCHAR(128) NULL,
        cert VARCHAR(64) NULL,
        dive_date DATE NULL,
        pax INT NULL DEFAULT 0,
        budget DECIMAL(12,2) NULL DEFAULT 0,
        photo_url TEXT NULL,
        payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid',
        payment_method VARCHAR(32) NULL,
        deposit_amount DECIMAL(12,2) NULL DEFAULT 0,
        points_total INT NULL DEFAULT 0,
        points_redeemed INT NULL DEFAULT 0,
        promo_code VARCHAR(64) NULL,
        promo_used TINYINT(1) NOT NULL DEFAULT 0,
        loyalty_level VARCHAR(20) NULL,
        social_ig VARCHAR(128) NULL,
        social_fb VARCHAR(128) NULL,
        social_tiktok VARCHAR(128) NULL,
        social_wechat VARCHAR(128) NULL,
        activity VARCHAR(20) NULL,
        brand VARCHAR(64) NOT NULL DEFAULT 'BALI DIVING',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } else {
      $adds = [
        "`column` VARCHAR(32) NOT NULL DEFAULT 'leads'",
        "email VARCHAR(255) NULL",
        "phone VARCHAR(64) NULL",
        "country VARCHAR(64) NULL",
        "source VARCHAR(64) NULL",
        "package VARCHAR(128) NULL",
        "cert VARCHAR(64) NULL",
        "dive_date DATE NULL",
        "pax INT NULL DEFAULT 0",
        "budget DECIMAL(12,2) NULL DEFAULT 0",
        "photo_url TEXT NULL",
        "payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid'",
        "payment_method VARCHAR(32) NULL",
        "deposit_amount DECIMAL(12,2) NULL DEFAULT 0",
        "points_total INT NULL DEFAULT 0",
        "points_redeemed INT NULL DEFAULT 0",
        "promo_code VARCHAR(64) NULL",
        "promo_used TINYINT(1) NOT NULL DEFAULT 0",
        "loyalty_level VARCHAR(20) NULL",
        "social_ig VARCHAR(128) NULL",
        "social_fb VARCHAR(128) NULL",
        "social_tiktok VARCHAR(128) NULL",
        "social_wechat VARCHAR(128) NULL",
        "activity VARCHAR(20) NULL",
        "brand VARCHAR(64) NOT NULL DEFAULT 'BALI DIVING'",
        "created_at DATETIME NOT NULL",
        "updated_at DATETIME NOT NULL"
      ];
      foreach($adds as $def){
        $col = trim(strtok($def, ' '), '`');
        if(!col_exists($pdo,'leads',$col)){
          qexec($pdo, "ALTER TABLE `leads` ADD COLUMN $def");
        }
      }
    }

    $id  = uid();
    $now = date('Y-m-d H:i:s');

    $data = [
      ':id'              => $id,
      ':column'          => 'leads',
      ':name'            => $name,
      ':email'           => $email,
      ':phone'           => $phone,
      ':country'         => '',
      ':source'          => 'Website Booking',
      ':package'         => $dive_site,
      ':cert'            => '',
      ':dive_date'       => $booking_date !== '' ? $booking_date : null,
      ':pax'             => max(1, (int)$participants),
      ':budget'          => $total_amount,
      ':photo_url'       => '',
      ':payment_status'  => 'unpaid',
      ':payment_method'  => '',
      ':deposit_amount'  => 0,
      ':points_total'    => max(0, (int)$participants),
      ':points_redeemed' => 0,
      ':promo_code'      => $coupon_code,
      ':promo_used'      => $coupon_code !== '' ? 1 : 0,
      ':loyalty_level'   => '',
      ':social_ig'       => '',
      ':social_fb'       => '',
      ':social_tiktok'   => '',
      ':social_wechat'   => '',
      ':activity'        => 'Go Diving',
      ':brand'           => 'BALI DIVING',
      ':created_at'      => $now,
      ':updated_at'      => $now,
    ];

    $sql = "INSERT INTO leads
      (id,`column`,name,email,phone,country,source,package,cert,dive_date,pax,budget,
       photo_url,payment_status,payment_method,deposit_amount,points_total,points_redeemed,
       promo_code,promo_used,loyalty_level,social_ig,social_fb,social_tiktok,social_wechat,
       activity,brand,created_at,updated_at)
      VALUES
      (:id,:column,:name,:email,:phone,:country,:source,:package,:cert,:dive_date,:pax,:budget,
       :photo_url,:payment_status,:payment_method,:deposit_amount,:points_total,:points_redeemed,
       :promo_code,:promo_used,:loyalty_level,:social_ig,:social_fb,:social_tiktok,:social_wechat,
       :activity,:brand,:created_at,:updated_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    /* Email Notification */
    $to      = 'subhi@balidiving.com';
    $subject = 'New Booking Lead — '.$data[':package'];
    $headers = [];
    $headers[] = 'From: Bali Diving <no-reply@balidiving.com>';
    $headers[] = 'Reply-To: '.($name !== '' ? "$name <$email>" : $email);
    $headers[] = 'Cc: admin@balidiving.com';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    $html = '<html><body style="font-family:Arial,Helvetica,sans-serif">
    <h2>New Booking Lead</h2>
    <table cellpadding="6" cellspacing="0" border="0" style="border:1px solid #e5e7eb">
      <tr><td><b>ID</b></td><td>'.h($id).'</td></tr>
      <tr><td><b>Dive Site</b></td><td>'.h($data[':package']).'</td></tr>
      <tr><td><b>Date</b></td><td>'.h($booking_date).'</td></tr>
      <tr><td><b>Participants</b></td><td>'.h($data[':pax']).'</td></tr>
      <tr><td><b>Price/Person</b></td><td>$'.number_format($price_person, 2).'</td></tr>
      <tr><td><b>Coupon</b></td><td>'.($coupon_code!=='' ? h($coupon_code).' ( -$'.number_format($coupon_value,2).' )' : '—').'</td></tr>
      <tr><td><b>Total</b></td><td>$'.number_format($total_amount,2).'</td></tr>
      <tr><td><b>Name</b></td><td>'.h($name).'</td></tr>
      <tr><td><b>Email</b></td><td>'.h($email).'</td></tr>
      <tr><td><b>Phone</b></td><td>'.h($phone).'</td></tr>
      <tr><td><b>Note</b></td><td>'.nl2br(h($note)).'</td></tr>
      <tr><td><b>Created</b></td><td>'.h($now).'</td></tr>
    </table>
    </body></html>';

    @mail($to, $subject, $html, implode("\r\n", $headers));

    json_headers(); echo json_encode(['ok'=>true,'id'=>$id]); exit;

  }catch(Throwable $e){
    json_headers(); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); exit;
  }
}

/* ===== If not API: render page ===== */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Bali Diving — Dive Sites & Booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
        body { box-sizing: border-box; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(6,60,127,.15), 0 10px 10px -5px rgba(6,60,127,.08); }
        .rating-stars { color: #fbbf24; }
        .offcanvas { position: fixed; top: 0; right: -100%; width: 100%; max-width: 500px; height: 100%; background: white; box-shadow: -5px 0 15px rgba(6,60,127,.1); transition: right .3s ease; z-index: 1000; overflow-y: auto; }
        .offcanvas.show { right: 0; }
        .offcanvas-backdrop { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(6,60,127,.5); opacity:0; visibility:hidden; transition: all .3s ease; z-index:999; }
        .offcanvas-backdrop.show { opacity:1; visibility:visible; }
  </style>
  <style>@view-transition { navigation: auto; }</style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
</head>
<body class="min-h-full" style="background-color: #f8fafc;">
  <main class="min-h-full" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;">
    <!-- Header Section -->
    <header class="text-center py-12 px-4" style="background: linear-gradient(135deg, #063C7F 0%, #0070D3 100%);">
      <div style="height: 50px;"></div>
      <h1 id="page-title" class="text-4xl md:text-5xl font-bold text-white mb-4">Discover Bali's Best Dive Sites</h1>
      <p id="page-subtitle" class="text-xl text-white opacity-90 max-w-2xl mx-auto">Explore underwater paradise with world-class diving experiences</p>
    </header>

    <!-- Products Grid (KONTEN ASLI KAMU – TIDAK DIUBAH) -->
    <section class="max-w-7xl mx-auto px-4 py-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="card-hover bg-white rounded-xl overflow-hidden shadow-md cursor-pointer">
          <div class="relative">
            <img src="https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg" alt="USAT Liberty Wreck diving" class="h-48 w-full object-cover"
                 onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center\'><svg class=\'w-16 h-16 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z\'/></svg></div>'">
            <button class="absolute top-3 right-3 p-2 rounded-full bg-white bg-opacity-80 hover:bg-opacity-100 transition-all duration-200 wishlist-btn" data-index="0">
              <i class="fas fa-heart w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200"></i>
            </button>
          </div>
          <div class="p-4">
            <h3 class="font-semibold mb-1 line-clamp-2" style="color: #063C7F;">USAT Liberty Wreck - Tulamben</h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">Famous WWII shipwreck with amazing marine life</p>
            <div class="flex items-center mb-2">
              <div class="rating-stars text-sm">★★★★★</div><span class="text-gray-600 text-sm ml-1">(4.9) 1,234 reviews</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm mb-3"><span>• 3-4 hours</span></div>
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2"><span class="text-2xl font-bold" style="color: #063C7F;">$45</span> <span class="text-lg text-gray-400 line-through">$60</span></div>
                <span class="text-xs text-green-600 font-medium">+1 Point</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 2 -->
        <div class="card-hover bg-white rounded-xl overflow-hidden shadow-md cursor-pointer">
          <div class="relative">
            <img src="https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg" alt="Manta Point diving" class="h-48 w-full object-cover"
                 onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center\'><svg class=\'w-16 h-16 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z\' clip-rule=\'evenodd\'/></svg></div>'">
            <button class="absolute top-3 right-3 p-2 rounded-full bg-white bg-opacity-80 hover:bg-opacity-100 transition-all duration-200 wishlist-btn" data-index="1">
              <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364 4.5 4.5 0 00-6.364 0L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="p-4">
            <h3 class="font-semibold mb-1 line-clamp-2" style="color: #063C7F;">Manta Point - Nusa Penida</h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">Swim with giant manta rays up to 7 meters</p>
            <div class="flex items-center mb-2">
              <div class="rating-stars text-sm">★★★★★</div><span class="text-gray-600 text-sm ml-1">(4.8) 987 reviews</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm mb-3"><span>• 6-7 hours</span></div>
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2"><span class="text-2xl font-bold" style="color: #063C7F;">$65</span> <span class="text-lg text-gray-400 line-through">$85</span></div>
                <span class="text-xs text-green-600 font-medium">+1 Point</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 3 -->
        <div class="card-hover bg-white rounded-xl overflow-hidden shadow-md cursor-pointer">
          <div class="relative">
            <img src="https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg" alt="Crystal Bay diving" class="h-48 w-full object-cover"
                 onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center\'><svg class=\'w-16 h-16 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z\'/></svg></div>'">
            <button class="absolute top-3 right-3 p-2 rounded-full bg-white bg-opacity-80 hover:bg-opacity-100 transition-all duration-200 wishlist-btn" data-index="2">
              <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364 4.5 4.5 0 00-6.364 0L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="p-4">
            <h3 class="font-semibold mb-1 line-clamp-2" style="color: #063C7F;">Crystal Bay - Nusa Penida</h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">Famous for Mola Mola sunfish sightings</p>
            <div class="flex items-center mb-2">
              <div class="rating-stars text-sm">★★★★☆</div><span class="text-gray-600 text-sm ml-1">(4.6) 756 reviews</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm mb-3"><span>• 5-6 hours</span></div>
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2"><span class="text-2xl font-bold" style="color: #063C7F;">$55</span> <span class="text-lg text-gray-400 line-through">$70</span></div>
                <span class="text-xs text-green-600 font-medium">+1 Point</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 4 -->
        <div class="card-hover bg-white rounded-xl overflow-hidden shadow-md cursor-pointer">
          <div class="relative">
            <img src="https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg" alt="Menjangan Island diving" class="h-48 w-full object-cover"
                 onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center\'><svg class=\'w-16 h-16 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z\' clip-rule=\'evenodd\'/></svg></div>'">
            <button class="absolute top-3 right-3 p-2 rounded-full bg-white bg-opacity-80 hover:bg-opacity-100 transition-all duration-200 wishlist-btn" data-index="3">
              <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364 4.5 4.5 0 00-6.364 0L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="p-4">
            <h3 class="font-semibold mb-1 line-clamp-2" style="color: #063C7F;">Menjangan Island</h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">Spectacular wall diving with pristine corals</p>
            <div class="flex items-center mb-2">
              <div class="rating-stars text-sm">★★★★★</div><span class="text-gray-600 text-sm ml-1">(4.7) 543 reviews</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm mb-3"><span>• 4-5 hours</span></div>
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2"><span class="text-2xl font-bold" style="color: #063C7F;">$50</span> <span class="text-lg text-gray-400 line-through">$65</span></div>
                <span class="text-xs text-green-600 font-medium">+1 Point</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 5 -->
        <div class="card-hover bg-white rounded-xl overflow-hidden shadow-md cursor-pointer">
          <div class="relative">
            <img src="https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg" alt="Blue Lagoon diving" class="h-48 w-full object-cover"
                 onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center\'><svg class=\'w-16 h-16 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z\'/></svg></div>'">
            <button class="absolute top-3 right-3 p-2 rounded-full bg-white bg-opacity-80 hover:bg-opacity-100 transition-all duration-200 wishlist-btn" data-index="4">
              <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364 4.5 4.5 0 00-6.364 0L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="p-4">
            <h3 class="font-semibold mb-1 line-clamp-2" style="color: #063C7F;">Padang Bai - Blue Lagoon</h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">Perfect for beginners with calm conditions</p>
            <div class="flex items-center mb-2">
              <div class="rating-stars text-sm">★★★★☆</div><span class="text-gray-600 text-sm ml-1">(4.5) 432 reviews</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm mb-3"><span>• 3-4 hours</span></div>
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2"><span class="text-2xl font-bold" style="color: #063C7F;">$35</span> <span class="text-lg text-gray-400 line-through">$45</span></div>
                <span class="text-xs text-green-600 font-medium">+1 Point</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 6 -->
        <div class="card-hover bg-white rounded-xl overflow-hidden shadow-md cursor-pointer">
          <div class="relative">
            <img src="https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg" alt="Japanese Wreck diving" class="h-48 w-full object-cover"
                 onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center\'><svg class=\'w-16 h-16 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z\' clip-rule=\'evenodd\'/></svg></div>'">
            <button class="absolute top-3 right-3 p-2 rounded-full bg-white bg-opacity-80 hover:bg-opacity-100 transition-all duration-200 wishlist-btn" data-index="5">
              <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364 4.5 4.5 0 00-6.364 0L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="p-4">
            <h3 class="font-semibold mb-1 line-clamp-2" style="color: #063C7F;">Amed - Japanese Wreck</h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">WWII patrol boat wreck exploration</p>
            <div class="flex items-center mb-2">
              <div class="rating-stars text-sm">★★★★★</div><span class="text-gray-600 text-sm ml-1">(4.8) 678 reviews</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm mb-3"><span>• 3-4 hours</span></div>
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2"><span class="text-2xl font-bold" style="color: #063C7F;">$40</span> <span class="text-lg text-gray-400 line-through">$55</span></div>
                <span class="text-xs text-green-600 font-medium">+1 Point</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 7 -->
        <div class="card-hover bg-white rounded-xl overflow-hidden shadow-md cursor-pointer">
          <div class="relative">
            <img src="https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg" alt="Turtle Point diving" class="h-48 w-full object-cover"
                 onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center\'><svg class=\'w-16 h-16 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg></div>'">
            <button class="absolute top-3 right-3 p-2 rounded-full bg-white bg-opacity-80 hover:bg-opacity-100 transition-all duration-200 wishlist-btn" data-index="6">
              <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364 4.5 4.5 0 00-6.364 0L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="p-4">
            <h3 class="font-semibold mb-1 line-clamp-2" style="color: #063C7F;">Sanur - Turtle Point</h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">Green and hawksbill turtle encounters</p>
            <div class="flex items-center mb-2">
              <div class="rating-stars text-sm">★★★★☆</div><span class="text-gray-600 text-sm ml-1">(4.4) 321 reviews</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm mb-3"><span>• 2-3 hours</span></div>
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2"><span class="text-2xl font-bold" style="color: #063C7F;">$30</span> <span class="text-lg text-gray-400 line-through">$40</span></div>
                <span class="text-xs text-green-600 font-medium">+1 Point</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 8 -->
        <div class="card-hover bg-white rounded-xl overflow-hidden shadow-md cursor-pointer">
          <div class="relative">
            <img src="https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg" alt="Gili Tepekong diving" class="h-48 w-full object-cover"
                 onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center\'><svg class=\'w-16 h-16 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\' clip-rule=\'evenodd\'/></svg></div>'">
            <button class="absolute top-3 right-3 p-2 rounded-full bg-white bg-opacity-80 hover:bg-opacity-100 transition-all duration-200 wishlist-btn" data-index="7">
              <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 000-6.364 4.5 4.5 0 00-6.364 0L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="p-4">
            <h3 class="font-semibold mb-1 line-clamp-2" style="color: #063C7F;">Candidasa - Gili Tepekong</h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">Advanced drift diving with sharks &amp; tuna</p>
            <div class="flex items-center mb-2">
              <div class="rating-stars text-sm">★★★★★</div><span class="text-gray-600 text-sm ml-1">(4.9) 567 reviews</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm mb-3"><span>• 4-5 hours</span></div>
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2"><span class="text-2xl font-bold" style="color: #063C7F;">$60</span> <span class="text-lg text-gray-400 line-through">$80</span></div>
                <span class="text-xs text-green-600 font-medium">+1 Point</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Load More Button -->
    <div class="text-center pb-12">
      <button class="font-semibold py-3 px-8 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg text-white"
              style="background-color: #0070D3; color: white;"
              onmouseover="this.style.backgroundColor='#063C7F'; this.style.color='white';"
              onmouseout="this.style.backgroundColor='#0070D3'; this.style.color='white';">
        Load More Dive Sites
      </button>
    </div>
  </main>

  <!-- Offcanvas Backdrop -->
  <div class="offcanvas-backdrop" id="offcanvasBackdrop"></div>
  <!-- Offcanvas Panel -->
  <div class="offcanvas" id="offcanvasPanel">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold" style="color: #063C7F;" id="offcanvasTitle">Dive Site Details</h2>
        <button class="text-gray-500 hover:text-gray-700 text-2xl" id="closeOffcanvas">×</button>
      </div>
      <div class="mb-6">
        <img src="https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg" alt="Dive site detail"
             class="h-64 w-full object-cover rounded-lg" id="offcanvasImage"
             onerror="this.src=''; this.alt='Image failed to load'; this.style.display='none'; this.parentElement.innerHTML='<div class=\'h-64 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center\'><svg class=\'w-20 h-20 text-white opacity-80\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z\'/></svg></div>'">
      </div>
      <div class="flex items-center mb-4">
        <div class="rating-stars text-lg" id="offcanvasRating">★★★★★</div>
        <span class="text-gray-600 ml-2" id="offcanvasReviews">(4.8) 2,341 reviews</span>
      </div>
      <div class="mb-6">
        <div class="flex items-center">
          <span class="text-3xl font-bold" style="color: #063C7F;" id="offcanvasPrice">$35</span>
          <span class="text-gray-500 text-lg line-through ml-2" id="offcanvasOriginalPrice">$45</span>
        </div>
        <span class="text-green-600 font-medium" id="offcanvasSavings">Save 22%</span>
      </div>
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3" style="color: #063C7F;">Description</h3>
        <p class="text-gray-700 leading-relaxed" id="offcanvasDescription">Experience unforgettable diving at one of Bali's premier dive sites. With excellent visibility and amazing marine biodiversity.</p>
      </div>
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3" style="color: #063C7F;">Trip Details</h3>
        <div class="space-y-2">
          <div class="flex justify-between"><span class="text-gray-600">Duration:</span> <span class="font-medium" id="offcanvasDuration">3-4 hours</span></div>
          <div class="flex justify-between"><span class="text-gray-600">Depth:</span> <span class="font-medium" id="offcanvasDepth">5-30 meters</span></div>
          <div class="flex justify-between"><span class="text-gray-600">Level:</span> <span class="font-medium" id="offcanvasLevel">All Levels</span></div>
          <div class="flex justify-between"><span class="text-gray-600">Visibility:</span> <span class="font-medium" id="offcanvasVisibility">15-25 meters</span></div>
        </div>
      </div>
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3" style="color: #063C7F;">Included</h3>
        <ul class="space-y-2 text-gray-700">
          <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Complete diving equipment</li>
          <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Professional dive guide</li>
          <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Hotel pickup &amp; drop-off</li>
          <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Lunch &amp; refreshments</li>
        </ul>
      </div>

      <!-- Book Button -->
      <button id="bookNowBtn" class="w-full font-semibold py-4 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg text-white"
              style="background-color: #0070D3; color: white;"
              onmouseover="this.style.backgroundColor='#063C7F'; this.style.color='white';"
              onmouseout="this.style.backgroundColor='#0070D3'; this.style.color='white';">
        Book Now
      </button>

      <!-- Booking Form (Hidden by default) -->
      <div id="bookingForm" class="hidden mt-6 p-6 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4" style="color: #063C7F;">Booking Details</h3>
        <div class="mb-4">
          <label for="bookingDate" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-2"></i>Select Date</label>
          <input type="date" id="bookingDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Booking Policy:</strong> For tomorrow's trip, booking must be made before 11:00 WITA today. After 11:00 WITA, minimum booking is for the day after tomorrow.
          </div>
        </div>
        <div class="mb-4">
          <label for="participants" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-users mr-2"></i>Number of Participants</label>
          <select id="participants" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="1">1 Person</option><option value="2">2 People</option><option value="3">3 People</option><option value="4">4 People</option>
            <option value="5">5 People</option><option value="6">6 People</option><option value="7">7 People</option><option value="8">8 People</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-user mr-2"></i>Full Name</label>
          <input type="text" id="fullName" placeholder="Enter your full name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-envelope mr-2"></i>Email Address</label>
          <input type="email" id="email" placeholder="Enter your email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div class="mb-4">
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-phone mr-2"></i>Phone Number</label>
          <input type="tel" id="phone" placeholder="Enter your phone number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div class="mb-4">
          <label for="note" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-sticky-note mr-2"></i>Special Notes (Optional)</label>
          <textarea id="note" rows="3" placeholder="Any special requests or notes..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
        </div>
        <div class="mb-6">
          <label for="couponCode" class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-tag mr-2"></i>Coupon Code (Optional)</label>
          <div class="flex gap-2">
            <input type="text" id="couponCode" placeholder="Enter coupon code" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="button" id="applyCoupon" class="px-4 py-2 font-medium rounded-lg transition-colors duration-200 text-white"
                    style="background-color:#0070D3;" onmouseover="this.style.backgroundColor='#063C7F';" onmouseout="this.style.backgroundColor='#0070D3';">
              Apply
            </button>
          </div>
          <div id="couponMessage" class="mt-2 text-sm hidden"></div>
        </div>
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
          <h4 class="text-lg font-semibold mb-3" style="color: #063C7F;"><i class="fas fa-credit-card mr-2"></i>Payment Summary</h4>
          <div class="space-y-3">
            <div class="flex justify-between items-center"><span class="text-gray-700">Price per person:</span> <span class="font-medium" style="color: #063C7F;" id="pricePerPerson">$45</span></div>
            <div class="flex justify-between items-center"><span class="text-gray-700">Number of participants:</span> <span class="font-medium" id="participantCount">1</span></div>
            <div class="flex justify-between items-center text-green-600" id="couponDiscountRow" style="display: none;">
              <span class="text-gray-700">Coupon discount:</span> <span class="font-medium" id="couponDiscount">-$0</span>
            </div>
            <hr class="border-gray-300">
            <div class="flex justify-between items-center text-lg"><span class="font-semibold" style="color: #063C7F;">Total Amount:</span> <span class="font-bold text-xl" style="color: #063C7F;" id="totalAmount">$45</span></div>
            <div class="text-center"><span class="text-green-600 font-medium text-sm" id="bonusPoints">+1 Bonus Points</span></div>
          </div>
        </div>
        <div class="flex gap-3">
          <button id="cancelBooking" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200">Cancel</button>
          <button id="confirmBooking" class="flex-1 font-semibold py-3 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg text-white"
                  style="background-color:#0070D3; color:white;"
                  onmouseover="this.style.backgroundColor='#063C7F'; this.style.color='white';"
                  onmouseout="this.style.backgroundColor='#0070D3'; this.style.color='white';">
            Confirm Booking
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    /* ===== Default configuration / Element SDK (as-is) ===== */
    const defaultConfig = {
      page_title: "Discover Bali's Best Dive Sites",
      page_subtitle: "Explore underwater paradise with world-class diving experiences",
      background_color: "#f8fafc",
      header_color: "#063C7F",
      text_color: "#063C7F",
      card_color: "#ffffff",
      accent_color: "#0070D3"
    };
    if (window.elementSdk) {
      window.elementSdk.init({
        defaultConfig: defaultConfig,
        onConfigChange: async (config) => {
          const titleElement = document.getElementById('page-title');
          const subtitleElement = document.getElementById('page-subtitle');
          if (titleElement) titleElement.textContent = config.page_title || defaultConfig.page_title;
          if (subtitleElement) subtitleElement.textContent = config.page_subtitle || defaultConfig.page_subtitle;
          const backgroundColor = config.background_color || defaultConfig.background_color;
          const headerColor = config.header_color || defaultConfig.header_color;
          const cardColor = config.card_color || defaultConfig.card_color;
          const accentColor = config.accent_color || defaultConfig.accent_color;
          document.body.style.backgroundColor = backgroundColor;
          const header = document.querySelector('header');
          if (header) header.style.background = `linear-gradient(135deg, ${headerColor} 0%, ${accentColor} 100%)`;
          document.querySelectorAll('.card-hover').forEach(card => { card.style.backgroundColor = cardColor; });
          document.querySelectorAll('h3').forEach(el => { el.style.color = config.text_color || defaultConfig.text_color; });
          document.querySelectorAll('button').forEach(button => {
            if (button.id !== 'closeOffcanvas' && button.id !== 'cancelBooking') {
              button.style.backgroundColor = accentColor; button.style.color = 'white';
            }
          });
        },
        mapToCapabilities: (config) => ({
          recolorables: [
            { get: () => config.background_color || defaultConfig.background_color, set: (v) => { config.background_color=v; window.elementSdk.setConfig({ background_color:v }); } },
            { get: () => config.header_color || defaultConfig.header_color, set: (v) => { config.header_color=v; window.elementSdk.setConfig({ header_color:v }); } },
            { get: () => config.text_color || defaultConfig.text_color, set: (v) => { config.text_color=v; window.elementSdk.setConfig({ text_color:v }); } },
            { get: () => config.card_color || defaultConfig.card_color, set: (v) => { config.card_color=v; window.elementSdk.setConfig({ card_color:v }); } },
            { get: () => config.accent_color || defaultConfig.accent_color, set: (v) => { config.accent_color=v; window.elementSdk.setConfig({ accent_color:v }); } },
          ],
          borderables: [],
          fontEditable: {
            get: () => config.font_family || 'Inter',
            set: (value) => { config.font_family = value; window.elementSdk.setConfig({ font_family: value }); }
          },
          fontSizeable: undefined
        }),
        mapToEditPanelValues: (config) => new Map([
          ["page_title", config.page_title || defaultConfig.page_title],
          ["page_subtitle", config.page_subtitle || defaultConfig.page_subtitle]
        ])
      });
    }

    /* ===== Dive site data / Offcanvas (as-is) ===== */
    let currentDiveSite = null;
    const diveSites = [
      { title:"USAT Liberty Wreck - Tulamben", rating:"★★★★★", reviews:"(4.9) 1,234 reviews", price:"$45", originalPrice:"$60", savings:"Save 25%", duration:"3-4 hours", depth:"5-30 meters", level:"All Levels", visibility:"20-30 meters", description:"Explore the famous American warship wreck that sank in 1942. USAT Liberty is one of Bali's most renowned dive sites featuring incredible marine biodiversity. You'll see various tropical fish species, beautiful coral reefs, and the intact ship structure.", image:"https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg" },
      { title:"Manta Point - Nusa Penida", rating:"★★★★★", reviews:"(4.8) 987 reviews", price:"$65", originalPrice:"$85", savings:"Save 24%", duration:"6-7 hours", depth:"10-25 meters", level:"Advanced", visibility:"15-25 meters", description:"Swim with giant Manta Rays at one of the world's best cleaning stations. An unforgettable experience seeing manta rays with wingspans up to 7 meters. This location also offers spectacular coral reef views.", image:"https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg" },
      { title:"Crystal Bay - Nusa Penida", rating:"★★★★☆", reviews:"(4.6) 756 reviews", price:"$55", originalPrice:"$70", savings:"Save 21%", duration:"5-6 hours", depth:"5-30 meters", level:"Intermediate", visibility:"10-20 meters", description:"Famous dive site known for Mola Mola (Ocean Sunfish) sightings during certain seasons. Crystal Bay offers good visibility and challenging currents, perfect for experienced divers wanting to see pelagic fish.", image:"https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg" },
      { title:"Menjangan Island", rating:"★★★★★", reviews:"(4.7) 543 reviews", price:"$50", originalPrice:"$65", savings:"Save 23%", duration:"4-5 hours", depth:"3-40 meters", level:"All Levels", visibility:"25-35 meters", description:"North Bali's best coral reefs with excellent visibility. Menjangan Island offers spectacular wall diving with drop-offs up to 60 meters. Perfect for underwater photography with amazing soft coral diversity.", image:"https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg" },
      { title:"Padang Bai - Blue Lagoon", rating:"★★★★☆", reviews:"(4.5) 432 reviews", price:"$35", originalPrice:"$45", savings:"Save 22%", duration:"3-4 hours", depth:"5-18 meters", level:"Beginner", visibility:"15-20 meters", description:"Perfect dive site for beginners with calm water conditions and good visibility. Blue Lagoon is famous for nudibranch diversity and macro life. Ideal for underwater photography and training dives.", image:"https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg" },
      { title:"Amed - Japanese Wreck", rating:"★★★★★", reviews:"(4.8) 678 reviews", price:"$40", originalPrice:"$55", savings:"Save 27%", duration:"3-4 hours", depth:"6-45 meters", level:"Intermediate", visibility:"15-25 meters", description:"Japanese patrol boat wreck from WWII located at 6-45 meters depth. Challenging wreck diving with intact ship structure inhabited by various reef fish species. Perfect for divers interested in underwater history.", image:"https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg" },
      { title:"Sanur - Turtle Point", rating:"★★★★☆", reviews:"(4.4) 321 reviews", price:"$30", originalPrice:"$40", savings:"Save 25%", duration:"2-3 hours", depth:"5-15 meters", level:"Beginner", visibility:"10-15 meters", description:"Famous dive site known for green turtle and hawksbill turtle populations. Easily accessible location from Sanur with calm current conditions. Perfect for fun diving and seeing diverse marine life in shallow depths.", image:"https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg" },
      { title:"Candidasa - Gili Tepekong", rating:"★★★★★", reviews:"(4.9) 567 reviews", price:"$60", originalPrice:"$80", savings:"Save 25%", duration:"4-5 hours", depth:"10-40 meters", level:"Advanced", visibility:"20-30 meters", description:"Challenging drift diving with strong currents and opportunities to see large pelagic fish like sharks, barracuda, and tuna. Gili Tepekong offers adrenaline rush for experienced divers with dramatic underwater topography.", image:"https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg" }
    ];

    function openOffcanvas(index) {
      const site = diveSites[index]; currentDiveSite = site;
      const offcanvas = document.getElementById('offcanvasPanel');
      const backdrop = document.getElementById('offcanvasBackdrop');
      document.getElementById('offcanvasTitle').textContent = site.title;
      document.getElementById('offcanvasRating').textContent = site.rating;
      document.getElementById('offcanvasReviews').textContent = site.reviews;
      document.getElementById('offcanvasPrice').textContent = site.price;
      document.getElementById('offcanvasDescription').textContent = site.description;
      document.getElementById('offcanvasDuration').textContent = site.duration;
      document.getElementById('offcanvasDepth').textContent = site.depth;
      document.getElementById('offcanvasLevel').textContent = site.level;
      document.getElementById('offcanvasVisibility').textContent = site.visibility;
      const imageEl = document.getElementById('offcanvasImage');
      imageEl.src = site.image; imageEl.alt = `${site.title} diving`;
      const originalPriceEl = document.getElementById('offcanvasOriginalPrice');
      const savingsEl = document.getElementById('offcanvasSavings');
      if (site.originalPrice) {
        originalPriceEl.textContent = site.originalPrice; originalPriceEl.style.display = 'inline';
        savingsEl.textContent = site.savings; savingsEl.style.display = 'inline';
      } else { originalPriceEl.style.display = 'none'; savingsEl.style.display = 'none'; }
      backdrop.classList.add('show'); offcanvas.classList.add('show'); document.body.style.overflow = 'hidden';
    }
    function closeOffcanvas() {
      const offcanvas = document.getElementById('offcanvasPanel');
      const backdrop = document.getElementById('offcanvasBackdrop');
      backdrop.classList.remove('show'); offcanvas.classList.remove('show'); document.body.style.overflow = 'auto';
    }
    document.querySelectorAll('.card-hover').forEach((card, index) => {
      card.addEventListener('click', function(){ openOffcanvas(index); });
    });
    document.getElementById('closeOffcanvas').addEventListener('click', closeOffcanvas);
    document.getElementById('offcanvasBackdrop').addEventListener('click', closeOffcanvas);
    document.querySelector('button').addEventListener('click', function() {
      if (this.textContent === 'Load More Dive Sites') {
        this.textContent = 'Loading...'; this.disabled = true;
        setTimeout(() => { this.textContent = 'Load More Dive Sites'; this.disabled = false; }, 1500);
      }
    });

    function setMinimumDate() {
      const now = new Date(); const currentTime = now.getHours() + (now.getMinutes()/60);
      let minDate = new Date(); minDate.setDate(minDate.getDate() + (currentTime >= 11.0 ? 2 : 1));
      const minDateString = minDate.toISOString().split('T')[0];
      const dateInput = document.getElementById('bookingDate'); if (dateInput) { dateInput.min = minDateString; dateInput.value = minDateString; }
    }
    const validCoupons = { 'SAVE10':10, 'WELCOME15':15, 'DIVE20':20, 'NEWBIE5':5, 'BALI25':25 };
    let appliedCouponDiscount = 0;

    function updatePaymentDetails() {
      if (!currentDiveSite) return;
      const participants = parseInt(document.getElementById('participants').value) || 1;
      const pricePerPerson = parseInt(currentDiveSite.price.replace('$','')) || 0;
      const subtotal = pricePerPerson * participants;
      const totalAmount = subtotal - appliedCouponDiscount;
      document.getElementById('pricePerPerson').textContent = currentDiveSite.price;
      document.getElementById('participantCount').textContent = participants;
      document.getElementById('bonusPoints').textContent = `+${participants} Bonus Points`;
      document.getElementById('totalAmount').textContent = `$${totalAmount}`;
      if (appliedCouponDiscount > 0) {
        document.getElementById('couponDiscountRow').style.display = 'flex';
        document.getElementById('couponDiscount').textContent = `-$${appliedCouponDiscount}`;
      } else {
        document.getElementById('couponDiscountRow').style.display = 'none';
      }
    }
    function applyCoupon() {
      const couponCode = document.getElementById('couponCode').value.trim().toUpperCase();
      const messageEl = document.getElementById('couponMessage');
      if (!couponCode) { showCouponMessage('Please enter a coupon code', 'error'); return; }
      if (validCoupons[couponCode]) {
        appliedCouponDiscount = validCoupons[couponCode];
        showCouponMessage(`Coupon applied! You saved $${appliedCouponDiscount}`, 'success');
        document.getElementById('applyCoupon').textContent = 'Applied';
        document.getElementById('applyCoupon').disabled = true;
        document.getElementById('couponCode').disabled = true;
        updatePaymentDetails();
      } else { showCouponMessage('Invalid coupon code. Please try again.', 'error'); }
    }
    function showCouponMessage(message, type) {
      const messageEl = document.getElementById('couponMessage');
      messageEl.textContent = message;
      messageEl.className = `mt-2 text-sm ${type === 'success' ? 'text-green-600' : 'text-red-600'}`;
      messageEl.classList.remove('hidden');
    }
    function resetCoupon() {
      appliedCouponDiscount = 0;
      document.getElementById('couponCode').value = '';
      document.getElementById('couponCode').disabled = false;
      document.getElementById('applyCoupon').textContent = 'Apply';
      document.getElementById('applyCoupon').disabled = false;
      document.getElementById('couponMessage').classList.add('hidden');
      updatePaymentDetails();
    }
    function initializeBookingForm() {
      const bookNowBtn = document.getElementById('bookNowBtn');
      const bookingForm = document.getElementById('bookingForm');
      const cancelBooking = document.getElementById('cancelBooking');
      const confirmBooking = document.getElementById('confirmBooking');

      if (bookNowBtn && bookingForm) {
        bookNowBtn.addEventListener('click', function(e) {
          e.stopPropagation(); bookingForm.classList.remove('hidden'); bookNowBtn.style.display = 'none';
          setMinimumDate(); updatePaymentDetails();
        });
        const participantsSelect = document.getElementById('participants');
        if (participantsSelect) { participantsSelect.addEventListener('change', updatePaymentDetails); }
        const applyCouponBtn = document.getElementById('applyCoupon');
        if (applyCouponBtn) { applyCouponBtn.addEventListener('click', applyCoupon); }
        const couponInput = document.getElementById('couponCode');
        if (couponInput) {
          couponInput.addEventListener('keypress', function(e){ if (e.key==='Enter'){ e.preventDefault(); applyCoupon(); } });
        }
        if (cancelBooking) {
          cancelBooking.addEventListener('click', function(e){
            e.stopPropagation(); bookingForm.classList.add('hidden'); bookNowBtn.style.display = 'block';
            document.getElementById('fullName').value = ''; document.getElementById('email').value = '';
            document.getElementById('phone').value = ''; document.getElementById('note').value = '';
            document.getElementById('participants').value = '1'; resetCoupon(); setMinimumDate();
          });
        }
        if (confirmBooking) {
          confirmBooking.addEventListener('click', function(e) {
            e.stopPropagation();
            const date = document.getElementById('bookingDate').value;
            const participants = document.getElementById('participants').value;
            const name = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const note = document.getElementById('note').value.trim();

            if (!date || !name || !email || !phone) {
              let errorMsg = document.getElementById('bookingError');
              if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.id = 'bookingError';
                errorMsg.className = 'mt-3 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm';
                confirmBooking.parentNode.insertBefore(errorMsg, confirmBooking.nextSibling);
              }
              errorMsg.textContent = 'Please fill in all required fields (Date, Name, Email, Phone)';
              return;
            }
            const existingError = document.getElementById('bookingError'); if (existingError) existingError.remove();

            let successMsg = document.getElementById('bookingSuccess');
            if (!successMsg) {
              successMsg = document.createElement('div');
              successMsg.id = 'bookingSuccess';
              successMsg.className = 'mt-3 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg';
              confirmBooking.parentNode.insertBefore(successMsg, confirmBooking.nextSibling);
            }
            const formattedDate = new Date(date).toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
            const totalAmount = document.getElementById('totalAmount').textContent;
            const couponUsed = (document.getElementById('couponDiscountRow').style.display !== 'none')
              ? document.getElementById('couponCode').value.toUpperCase() : null;

            successMsg.innerHTML = `
              <div class="font-semibold mb-2">✅ Booking Successful!</div>
              <div class="text-sm space-y-1">
                <div><strong>Date:</strong> ${formattedDate}</div>
                <div><strong>Participants:</strong> ${participants} people</div>
                <div><strong>Total Amount:</strong> ${totalAmount}</div>
                ${couponUsed ? `<div><strong>Coupon:</strong> ${couponUsed}</div>` : ''}
                <div><strong>Name:</strong> ${name}</div>
                <div><strong>Email:</strong> ${email}</div>
                <div><strong>Phone:</strong> ${phone}</div>
              </div>
              <div class="mt-3 text-sm">A confirmation email will be sent to your email address.</div>
            `;

            bookingForm.style.display = 'none';
            setTimeout(() => {
              if (successMsg) successMsg.remove();
              bookingForm.style.display = 'block'; bookingForm.classList.add('hidden'); bookNowBtn.style.display = 'block';
              document.getElementById('fullName').value = ''; document.getElementById('email').value = '';
              document.getElementById('phone').value = ''; document.getElementById('note').value = '';
              document.getElementById('participants').value = '1'; resetCoupon(); setMinimumDate();
            }, 5000);
          });
        }
      }
    }

    /* Initialize booking form when offcanvas opened */
    const originalOpenOffcanvas = openOffcanvas;
    openOffcanvas = function(index) {
      originalOpenOffcanvas(index);
      setTimeout(() => { initializeBookingForm(); }, 100);
    };

    /* ===== Lead Gateway Hook (same page, no UI changes) ===== */
    (function(){
      function $(sel){ return document.querySelector(sel); }
      function getNumFromMoney(s){ if(!s) return 0; const n=(s+'').replace(/[^\d.]/g,''); return parseFloat(n||'0')||0; }
      function text(id){ const el=document.getElementById(id); return el?el.textContent.trim():''; }
      function val(id){ const el=document.getElementById(id); return el?el.value.trim():''; }
      function attachBookingHook(){
        const btn = document.getElementById('confirmBooking');
        if(!btn || btn._bdHookAttached) return;
        btn._bdHookAttached = true;
        btn.addEventListener('click', function(){
          try{
            const dive_site       = text('offcanvasTitle') || 'Dive Trip';
            const booking_date    = val('bookingDate');
            const participants    = parseInt(val('participants')||'1',10) || 1;
            const name            = val('fullName');
            const email           = val('email');
            const phone           = val('phone');
            const note            = val('note');

            const price_per_person= getNumFromMoney(text('pricePerPerson'));
            const total_amount    = getNumFromMoney(text('totalAmount'));

            const couponCodeEl    = document.getElementById('couponCode');
            const couponRow       = document.getElementById('couponDiscountRow');
            const couponValEl     = document.getElementById('couponDiscount');
            const coupon_code     = couponCodeEl ? (couponCodeEl.value||'') : '';
            const coupon_value    = (couponRow && couponRow.style.display !== 'none')
                                      ? getNumFromMoney(couponValEl ? couponValEl.textContent : '0')
                                      : 0;

            if(!booking_date || !name || !email || !phone){ return; }

            const payload = {
              dive_site, booking_date, participants, name, email, phone, note,
              coupon_code, coupon_value, price_per_person, total_amount
            };

            fetch(location.pathname + '?action=lead_gateway', {
              method: 'POST',
              headers: { 'Content-Type':'application/json; charset=UTF-8' },
              body: JSON.stringify(payload),
              cache: 'no-store',
              keepalive: true
            }).catch(()=>{});
          }catch(e){}
        });
      }
      if(document.readyState==='loading'){
        document.addEventListener('DOMContentLoaded', attachBookingHook);
      } else { attachBookingHook(); }
      document.addEventListener('click', function(e){
        const t = e.target;
        if(t && (t.id==='bookNowBtn' || (t.closest && t.closest('#bookNowBtn')))) {
          setTimeout(attachBookingHook, 200);
        }
      });
    })();
  </script>
</body>
</html>

 

    <?php include('../template/end.php')?>