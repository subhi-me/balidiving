<?php
// ==============================================
// 🌊 BALI DIVING - Voucher Redemption Script v1.3
// - Generates approval code BD-xxxxx
// - Emails user & admin
// - Supports "Gift to a Friend" (friend name/email/WhatsApp) -> notified to admin
// ==============================================

date_default_timezone_set('Asia/Makassar');

// Helper: strip CRLF to avoid header injection
function no_crlf($str) {
  return preg_replace("/[\r\n]+/", " ", trim((string)$str));
}

// --- Ambil & sanitasi parameter pengunjung ---
$raw_name  = $_GET['n']  ?? '';
$raw_email = $_GET['e']  ?? '';
$raw_wa    = $_GET['wa'] ?? '';

$clean_name  = no_crlf($raw_name);
$clean_email = no_crlf($raw_email);
$clean_wa    = no_crlf($raw_wa);

$name   = htmlspecialchars(urldecode($clean_name), ENT_QUOTES, 'UTF-8');
$email  = filter_var(urldecode($clean_email), FILTER_SANITIZE_EMAIL);
$wa     = htmlspecialchars(urldecode($clean_wa), ENT_QUOTES, 'UTF-8');

// --- Ambil & sanitasi parameter teman (gift) ---
$raw_f_name  = $_GET['fn'] ?? '';
$raw_f_email = $_GET['fe'] ?? '';
$raw_f_wa    = $_GET['fw'] ?? '';

$clean_f_name  = no_crlf($raw_f_name);
$clean_f_email = no_crlf($raw_f_email);
$clean_f_wa    = no_crlf($raw_f_wa);

$f_name  = htmlspecialchars(urldecode($clean_f_name), ENT_QUOTES, 'UTF-8');
$f_email = filter_var(urldecode($clean_f_email), FILTER_SANITIZE_EMAIL);
$f_wa    = htmlspecialchars(urldecode($clean_f_wa), ENT_QUOTES, 'UTF-8');

// Validasi minimum: email pengunjung wajib
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $error = "Alamat email tidak valid atau tidak disertakan.";
} else {
  // Generate approval code: BD- + 5 digit
  try { $rand = random_int(0, 99999); } catch (Exception $e) { $rand = mt_rand(0, 99999); }
  $approval_code = 'BD-' . str_pad((string)$rand, 5, '0', STR_PAD_LEFT);

  // Flag gift jika email teman valid
  $gift_enabled = (!empty($f_email) && filter_var($f_email, FILTER_VALIDATE_EMAIL));

  // Logging
  $timestamp = date("Y-m-d H:i:s");
  $log_line = "{$timestamp} | USER: {$name} <{$email}> | WA: {$wa} | CODE: {$approval_code}";
  if ($gift_enabled) {
    $log_line .= " | GIFT-> {$f_name} <{$f_email}> | WA: {$f_wa}";
  }
  $log_line .= "\n";
  @file_put_contents(__DIR__ . '/redeem_log.txt', $log_line, FILE_APPEND | LOCK_EX);

  // Email headers
  $from_email = "noreply@balidiving.com";
  $headers  = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=UTF-8\r\n";
  $headers .= "From: Bali Diving <{$from_email}>\r\n";
  $headers .= "Reply-To: Bali Diving <{$from_email}>\r\n";

  // --- Email ke ADMIN (berisi detail user + gift jika ada) ---
  $to_admin = "admin@balidiving.com";
  $subject_admin = "🎟️ [Voucher Redemption] {$approval_code}" . ($gift_enabled ? " + GIFT" : "");
  $message_admin = "
  <html><body style='font-family:Arial,sans-serif;'>
    <h2>Voucher Redemption Request</h2>
    <p><b>Waktu:</b> {$timestamp} WITA</p>
    <h3>Data Peminta</h3>
    <p>
      <b>Nama:</b> {$name}<br>
      <b>Email:</b> {$email}<br>
      <b>WhatsApp:</b> ".($wa ?: '-')."<br>
      <b>Approval Code:</b> <span style='color:green;'>{$approval_code}</span>
    </p>";

  if ($gift_enabled) {
    $message_admin .= "
    <h3>Gift to a Friend (Tindaklanjuti oleh Admin)</h3>
    <p>
      <b>Nama Teman:</b> {$f_name}<br>
      <b>Email Teman:</b> {$f_email}<br>
      <b>WhatsApp Teman:</b> ".($f_wa ?: '-')."
    </p>
    <p><i>Instruksi:</i> Mohon admin mengirim voucher/approval ke teman di atas dengan menyertakan Kode Approval <b>{$approval_code}</b> dan langkah redeem.</p>";
  }

  $message_admin .= "
    <hr>
    <p style='font-size:12px;color:#666'>Email otomatis dari BaliDiving.com/promo</p>
  </body></html>";

  @mail($to_admin, $subject_admin, $message_admin, $headers);

  // --- Email ke USER (konfirmasi & kode) ---
  $subject_user = "🎉 Bali Diving - Konfirmasi Redeem Voucher (Kode: {$approval_code})";
  $gift_note = $gift_enabled
    ? "<p><b>Gift:</b> Kami juga sudah memberi tahu admin tentang temanmu <b>{$f_name}</b> (<b>{$f_email}</b>). Admin kami akan menghubungi dan mengirim voucher ke temanmu.</p>"
    : "";

  $message_user = "
  <html><body style='font-family:Arial,sans-serif;'>
    <div style='max-width:640px; margin:auto; padding:20px; background:#f8f9fa; border-radius:10px;'>
      <h2 style='color:#0077b6;'>Terima kasih, {$name}!</h2>
      <p>Permintaanmu untuk <b>redeem voucher diskon</b> telah kami terima.</p>
      <p><b>Kode Approval:</b> <span style='font-size:18px; font-weight:700; color:#0b6'>{$approval_code}</span></p>
      {$gift_note}
      <p>Kami akan menghubungimu via email <b>{$email}</b>".($wa ? " dan WhatsApp <b>{$wa}</b>" : "")." jika diperlukan.</p>
      <hr>
      <p style='font-size:12px;color:#666'>Jika kamu tidak melakukan permintaan ini, abaikan email ini atau hubungi admin@balidiving.com</p>
    </div>
  </body></html>";

  @mail($email, $subject_user, $message_user, $headers);

  $success = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Voucher Promo Bali Diving</title>
<style>
  body{font-family:Arial, sans-serif; background:#f4f6f8; padding:40px; text-align:center}
  .card{background:#fff; padding:28px; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.08); max-width:560px; margin:0 auto}
  h2{color:#0077b6}
  .code{display:inline-block; padding:10px 16px; background:#e9fbe9; color:#046; border-radius:8px; font-weight:700; margin-top:10px}
  .error{color:#c22}
  .kv{font-size:14px;color:#333; text-align:left; margin:10px auto 0; max-width:480px}
  .kv div{display:flex; justify-content:space-between; border-bottom:1px dashed #eee; padding:6px 0}
</style>
</head>
<body>
  <div class="card">
    <?php if (isset($error)): ?>
      <h2 class="error">❌ <?= $error ?></h2>
      <p>Pastikan parameter `n` (nama) dan `e` (email) terisi dengan benar pada URL.</p>
    <?php else: ?>
      <h2>Permintaan terkirim ✅</h2>
      <p>Terima kasih, <strong><?= $name ?: 'Pengunjung' ?></strong>.</p>
      <p>Kode approval juga sudah dikirim ke email <strong><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></strong>.</p>
      <div class="code"><?= $approval_code ?></div>

      <div class="kv">
        <div><span>WhatsApp kamu</span><span><?= $wa ?: '-' ?></span></div>
        <div><span>Gift to a Friend</span><span><?= $gift_enabled ? 'Ya' : 'Tidak' ?></span></div>
        <?php if ($gift_enabled): ?>
          <div><span>Nama teman</span><span><?= $f_name ?></span></div>
          <div><span>Email teman</span><span><?= htmlspecialchars($f_email, ENT_QUOTES, 'UTF-8') ?></span></div>
          <div><span>WA teman</span><span><?= $f_wa ?: '-' ?></span></div>
        <?php endif; ?>
      </div>

      <p style="margin-top:12px; font-size:13px; color:#666;">
        Admin kami juga telah menerima detail gift agar dapat mengirim voucher ke temanmu.
      </p>
    <?php endif; ?>
  </div>
</body>
</html>
