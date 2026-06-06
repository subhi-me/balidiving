<?php
/* ===== DEBUG (matikan di produksi) ===== */
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

/* ===== DB CONFIG (samakan dengan CRM kamu) ===== */
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

/* ===== Read Input ===== */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
  json_headers(); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit;
}
$isJson = isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
$in = $isJson ? (json_decode(file_get_contents('php://input'), true) ?: []) : $_POST;

/* ===== Expected fields from booking form ===== */
$dive_site     = trim((string)($in['dive_site'] ?? ''));
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

/* ===== Basic Validate ===== */
if ($name==='' || $email==='' || $phone==='' || $booking_date==='') {
  json_headers(); echo json_encode(['ok'=>false,'error'=>'Missing required fields']); exit;
}

try{
  $pdo = pdo_conn();

  $id  = uid();
  $now = date('Y-m-d H:i:s');

  /* ===== Build data sesuai schema `leads` kamu ===== */
  $data = [
    ':id'              => $id,
    ':column'          => 'leads',
    ':name'            => $name,
    ':email'           => $email,
    ':phone'           => $phone,
    ':country'         => '',
    ':source'          => 'Website Booking',
    ':package'         => $dive_site !== '' ? $dive_site : 'Dive Trip',
    ':cert'            => '',
    ':dive_date'       => $booking_date !== '' ? $booking_date : null,
    ':pax'             => max(1, $participants),
    ':budget'          => $total_amount,    // simpan total nilai transaksi
    ':photo_url'       => '',
    ':payment_status'  => 'unpaid',
    ':payment_method'  => '',
    ':deposit_amount'  => 0,
    ':points_total'    => max(0, $participants),
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

  /* ===== Insert ===== */
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

  /* ===== Email Notification ===== */
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

  // Kirim (PHP mail). Untuk produksi, lebih baik pakai SMTP (lihat blok opsional di bawah).
  @mail($to, $subject, $html, implode("\r\n", $headers));

  json_headers(); echo json_encode(['ok'=>true,'id'=>$id]); exit;

}catch(Throwable $e){
  json_headers(); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); exit;
}

/* ====== OPTIONAL: Contoh SMTP via PHPMailer (ganti blok mail() di atas) ======

require __DIR__.'/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host       = 'smtp.yourhost.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'no-reply@balidiving.com';
  $mail->Password   = 'password_smtp_kamu';
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // atau PHPMailer::ENCRYPTION_SMTPS
  $mail->Port       = 587; // atau 465

  $mail->setFrom('no-reply@balidiving.com', 'Bali Diving');
  $mail->addAddress('subhi@balidiving.com');
  $mail->addCC('admin@balidiving.com');
  if ($email) $mail->addReplyTo($email, $name ?: $email);

  $mail->isHTML(true);
  $mail->Subject = $subject;
  $mail->Body    = $html;

  $mail->send();
} catch (Exception $e) {
  // log error kalau perlu
}
========================================================================= */
