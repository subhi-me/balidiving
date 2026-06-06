<?php
/* =========================================================
   BALI DIVING - Reservation (UI tetap) + Cek Email + Quick Register
   + Kirim Email saat Register (New Lead) + Hard-set column='leads'
   + Section Price Cards sebelum "Select Your Interests"
   + Log semua email yang mencoba cek pricelist (daily digest)
   + FACEBOOK CONVERSIONS API (CAPI) INTEGRATION
   File: reservation.php | PHP 8+, PDO MySQL
   ========================================================= */

date_default_timezone_set('Asia/Makassar');

/* ===== DB CONFIG (sesuaikan) ===== */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/* ===== PDO CONNECT ===== */
$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$opt = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];
try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $opt);
} catch (Throwable $e) {
  http_response_code(500);
  header('Content-Type: text/plain; charset=utf-8');
  echo "DB connect failed: " . $e->getMessage();
  error_log("[reservation.php] DB connect failed: " . $e->getMessage());
  exit;
}

/* ===== ONE-TIME DDL (aman jika berulang) ===== */
try {
  // Tabel log percobaan cek pricelist
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS pricelist_attempts (
      id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      email         VARCHAR(190) NOT NULL,
      activities    VARCHAR(255) NULL,
      status        ENUM('exists','new','registered','unknown') NOT NULL DEFAULT 'unknown',
      ip            VARCHAR(64) NULL,
      user_agent    VARCHAR(255) NULL,
      source        VARCHAR(64) NOT NULL DEFAULT 'Reservation Pricelist',
      emailed_daily TINYINT(1) NOT NULL DEFAULT 0,
      created_at    DATETIME NOT NULL,
      KEY (email),
      KEY (created_at),
      KEY (emailed_daily)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");
} catch (Throwable $e) {
  error_log('[reservation.php] DDL pricelist_attempts failed: ' . $e->getMessage());
}

/* ===== HELPERS ===== */
function read_json_body_or_form(): array
{
  $ct = $_SERVER['CONTENT_TYPE'] ?? '';
  if (stripos($ct, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
  }
  return $_POST ?: [];
}
function json_out($arr)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($arr);
  exit;
}
function now()
{
  return date('Y-m-d H:i:s');
}
function normalize_phone($raw)
{
  return preg_replace('/\D+/', '', (string) $raw);
}
function gen_id()
{
  return strtoupper(dechex(time())) . substr(strtoupper(md5(uniqid('', true))), 0, 8);
}
function client_ip()
{
  foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $k) {
    if (!empty($_SERVER[$k])) {
      $v = $_SERVER[$k];
      if ($k === 'HTTP_X_FORWARDED_FOR') {
        $v = trim(explode(',', $v)[0]);
      }
      return $v;
    }
  }
  return null;
}

/* ===== FACEBOOK CAPI HELPER ===== */
function send_fb_capi_event($event_name, $user_data_raw)
{
  $access_token = 'EAAL4CBNKCPsBQ6FXCPPdy6mrCOJMRVgEgQZBlpdn21aTsnAiXibtZChKdJzxKy8pPjJj8cLkW1GkYQ3ex5cOj1YuNkBf7rtzRT0E7UZCyHZBC1L4btvcZBD0ozZCUbDOOXydvvvXHZAQsOZCSsvxWfeXaGJSBR4drRZAFPJUgkm7y0E7WOSG1iIbbcTVDzryJHQZDZD';
  $pixel_id     = '2151240455197949'; 
  $api_version  = 'v19.0';

  // Perbaikan: Hapus validasi strpos yang memblokir token asli
  if (empty($access_token)) return;

  $url = "https://graph.facebook.com/{$api_version}/{$pixel_id}/events?access_token={$access_token}";

  // Facebook mewajibkan enkripsi SHA256 untuk data pribadi
  $hashed_data = [];
  
  if (!empty($user_data_raw['email'])) {
    $hashed_data['em'] = [hash('sha256', strtolower(trim($user_data_raw['email'])))];
  }
  if (!empty($user_data_raw['phone'])) {
    $clean_phone = preg_replace('/\D+/', '', $user_data_raw['phone']); // Hanya angka
    $hashed_data['ph'] = [hash('sha256', $clean_phone)];
  }
  if (!empty($user_data_raw['name'])) {
    // Memecah nama depan untuk 'fn' (first name)
    $name_parts = explode(' ', strtolower(trim($user_data_raw['name'])));
    $hashed_data['fn'] = [hash('sha256', $name_parts[0])];
  }

  // Mengirim IP dan User Agent (Sangat penting untuk CAPI)
  $hashed_data['client_ip_address'] = client_ip();
  $hashed_data['client_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

  $payload = [
    'data' => [
      [
        'event_name' => $event_name,
        'event_time' => time(),
        'action_source' => 'website',
        'user_data' => $hashed_data
      ]
    ],

  ];

  // Eksekusi menggunakan cURL
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Maksimal nunggu FB 3 detik
  
  $response = curl_exec($ch);
  
  // Opsional log untuk melihat pesan error dari FB (bisa dicek di error_log server)
  // if(curl_errno($ch)) error_log('FB CAPI cURL Error: ' . curl_error($ch));
  // error_log('FB CAPI Response: ' . $response);
  
  curl_close($ch);
}

/* ===== EMAIL NOTIFICATION (mail bawaan PHP) ===== */
function send_lead_email(array $lead)
{
  $to = 'sales@balidiving.com';
  $cc = 'subhi@balidiving.com';
  $subject = "Follow Up! {$lead['name']} — New Lead Registered";

  $safe = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

  $cleanPhone = preg_replace('/\D+/', '', $lead['phone']);
  $waText = urlencode("Halo " . $lead['name'] . ", thank you for registering on Bali Diving. How can we help you plan your next dive?");
  $waLink = "https://wa.me/" . $cleanPhone . "?text=" . $waText;

  $draftSubject = "Your Inquiry with Bali Diving";
  $draftBody = "Dear " . $lead['name'] . ",\n\nThank you for registering on our website to view our pricelist and packages.\n\nWe are here to help you choose the best diving experience in Bali. Let us know if you have any questions or if you would like to book a trip!\n\nBest regards,\nBali Diving Team\nhttps://balidiving.com";
  $gmailLink = "https://mail.google.com/mail/?view=cm&to=" . rawurlencode($lead['email']) . "&su=" . rawurlencode($draftSubject) . "&body=" . rawurlencode($draftBody);

  $html = "
  <html><body style='font-family:Arial,sans-serif;color:#111'>
    <div style='max-width:640px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px'>
      <div style='padding:18px 22px;border-bottom:1px solid #e5e7eb'>
        <h2 style='margin:0;color:#0ea5e9'>New Lead Registered</h2>
        <div style='font-size:13px;color:#6b7280'>Bali Diving Reservation</div>
      </div>
      <div style='padding:22px'>
        <p style='margin-top:0'>A new contact has registered via reservation form. Please follow up.</p>
        <table cellspacing='0' cellpadding='8' style='width:100%;border-collapse:collapse;margin-bottom:24px'>
          <tr><td style='width:170px;background:#f9fafb;border:1px solid #eee'><b>Full Name</b></td><td style='border:1px solid #eee'>{$safe($lead['name'])}</td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Email</b></td><td style='border:1px solid #eee'><a href='mailto:{$safe($lead['email'])}'>{$safe($lead['email'])}</a></td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Country</b></td><td style='border:1px solid #eee'>{$safe($lead['country'])}</td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Phone</b></td><td style='border:1px solid #eee'><a href='https://wa.me/{$cleanPhone}' target='_blank'>{$safe($lead['phone'])}</a></td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Registered At</b></td><td style='border:1px solid #eee'>{$safe($lead['created_at'])}</td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Source</b></td><td style='border:1px solid #eee'>{$safe($lead['source'] ?? 'Reservation Pricelist')}</td></tr>
        </table>

        <!-- Action Buttons -->
        <div style='text-align: center; margin-top: 30px; margin-bottom: 20px;'>
            <!-- WhatsApp Button -->
            <a href='{$waLink}' target='_blank' 
               style='background-color: #25D366; color: white; padding: 8px 16px; text-decoration: none; border-radius: 30px; font-weight: bold; display: inline-block; margin: 5px; box-shadow: 0 3px 5px rgba(37, 211, 102, 0.2); border: 1px solid #25D366; font-size: 12px;'>
               💬 Follow Up WhatsApp
            </a>

            <!-- Gmail Button -->
            <a href='{$gmailLink}' target='_blank' 
               style='background-color: #EA4335; color: white; padding: 8px 16px; text-decoration: none; border-radius: 30px; font-weight: bold; display: inline-block; margin: 5px; box-shadow: 0 3px 5px rgba(234, 67, 53, 0.2); border: 1px solid #EA4335; font-size: 12px;'>
               ✉️ Follow Up Gmail
            </a>
        </div>
      </div>
      <div style='padding:16px 22px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280'>
        Bali Diving CRM Notification
      </div>
    </div>
  </body></html>";

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $headers .= "From: Bali Diving <noreply@balidiving.com>\r\n";
  $headers .= "Cc: $cc\r\n";

  if (!@mail($to, $subject, $html, $headers)) {
    error_log("[reservation.php] mail() failed for new lead: " . json_encode($lead));
  }
}

function send_unregistered_attempt_email(string $email, ?string $activities)
{
  $to = 'sales@balidiving.com';
  $cc = 'admin@balidiving.com';
  $subject = "Pricelist Attempt: Unregistered Email ($email)";

  $safe = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

  $draftSubject = "Your Scuba Diving Inquiry with Bali Diving";
  $draftBody = "Hi there,\n\nThank you for visiting balidiving.com! We noticed you were exploring our pricelist and packages earlier today.\n\nAt Bali Diving, we are dedicated to providing the safest and most unforgettable diving experiences in Bali. Whether you are looking to get certified, fun dive, or simply have questions about our locations, our team is here to help!\n\nCould you let us know what kind of adventure you're looking for? If you need any assistance finding the perfect package or have any questions at all, please feel free to reply directly to this email.\n\nWe would love to help you plan your next dive!\n\nBest regards,\nThe Bali Diving Team\nhttps://balidiving.com";
  $gmailLink = "https://mail.google.com/mail/?view=cm&to=" . rawurlencode($email) . "&su=" . rawurlencode($draftSubject) . "&body=" . rawurlencode($draftBody);

  $html = "
  <html><body style='font-family:Arial,sans-serif;color:#111'>
    <div style='max-width:640px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px'>
      <div style='padding:18px 22px;border-bottom:1px solid #e5e7eb'>
        <h2 style='margin:0;color:#f59e0b'>Pricelist Attempt (Unregistered)</h2>
        <div style='font-size:13px;color:#6b7280'>Bali Diving Reservation</div>
      </div>
      <div style='padding:22px'>
        <p style='margin-top:0; line-height:1.6;'>A user attempted to open the pricelist but their email is not registered in the database. <strong>Please follow up with them immediately to offer assistance.</strong></p>
        <table cellspacing='0' cellpadding='8' style='width:100%;border-collapse:collapse; margin-bottom: 24px;'>
          <tr><td style='width:170px;background:#f9fafb;border:1px solid #eee'><b>Email</b></td><td style='border:1px solid #eee'><a href='mailto:{$safe($email)}'>{$safe($email)}</a></td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Activities</b></td><td style='border:1px solid #eee'>{$safe($activities ?: 'None selected')}</td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Attempted At</b></td><td style='border:1px solid #eee'>" . date('Y-m-d H:i:s') . "</td></tr>
        </table>
        
        <!-- Action Buttons -->
        <div style='text-align: center; margin-top: 30px; margin-bottom: 20px;'>
            <!-- Gmail Button -->
            <a href='{$gmailLink}' target='_blank' 
               style='background-color: #EA4335; color: white; padding: 8px 16px; text-decoration: none; border-radius: 30px; font-weight: bold; display: inline-block; margin: 5px; box-shadow: 0 3px 5px rgba(234, 67, 53, 0.2); border: 1px solid #EA4335; font-size: 12px;'>
               ✉️ Follow Up Gmail
            </a>
        </div>
      </div>
    </div>
  </body></html>";

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $headers .= "From: Bali Diving <noreply@balidiving.com>\r\n";
  $headers .= "Cc: $cc\r\n";

  @mail($to, $subject, $html, $headers);
}

/* ===== LOG ATTEMPT HELPER ===== */
function log_pricelist_attempt(PDO $pdo, string $email, ?string $activitiesCSV, string $status)
{
  try {
    $pdo->prepare("
      INSERT INTO pricelist_attempts (email, activities, status, ip, user_agent, source, emailed_daily, created_at)
      VALUES (:email, :activities, :status, :ip, :ua, 'Reservation Pricelist', 0, :ts)
    ")->execute([
          ':email' => mb_strtolower(trim($email)),
          ':activities' => $activitiesCSV ?: null,
          ':status' => $status,
          ':ip' => client_ip(),
          ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
          ':ts' => now(),
        ]);
  } catch (Throwable $e) {
    error_log('[reservation.php] log_pricelist_attempt failed: ' . $e->getMessage());
  }
}

/* ===== API ENDPOINTS =====
   - POST ?action=check_email {email, activities?}
   - POST ?action=register {full_name, email, country, phone_code, phone_number}
   - GET  ?action=digest_preview   (opsional: lihat ringkasan hari ini di browser)
   ========================================================= */
$action = $_GET['action'] ?? '';

if ($action === 'check_email' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $in = read_json_body_or_form();
  $email = trim($in['email'] ?? '');
  // activities bisa string "a,b,c" atau array -> ke CSV
  $acts = $in['activities'] ?? '';
  if (is_array($acts)) {
    $acts = implode(',', $acts);
  }
  $acts = $acts ? preg_replace('/\s+/', '', (string) $acts) : null;

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_out(['ok' => false, 'error' => 'Invalid email']);
  }
  $st = $pdo->prepare("SELECT id, name FROM leads WHERE email = :em LIMIT 1");
  $st->execute([':em' => $email]);
  $row = $st->fetch();
  $exists = (bool) $row;

  // Log setiap percobaan cek pricelist
  log_pricelist_attempt($pdo, $email, $acts, $exists ? 'exists' : 'new');

  if (!$exists) {
      send_unregistered_attempt_email($email, $acts);
  }

  json_out(['ok' => true, 'exists' => $exists, 'name' => $row['name'] ?? null]);
}

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $in = read_json_body_or_form();

  $full_name = trim($in['full_name'] ?? '');
  $email = trim($in['email'] ?? '');
  $country = trim($in['country'] ?? '');
  $phone_code = trim($in['phone_code'] ?? '');
  $phone_num = trim($in['phone_number'] ?? '');
  $actsCSV = null; // optional kalau nanti Anda kirim dari modal

  if (isset($in['activities'])) {
    $actsCSV = is_array($in['activities']) ? implode(',', $in['activities']) : (string) $in['activities'];
    $actsCSV = $actsCSV ? preg_replace('/\s+/', '', $actsCSV) : null;
  }

  if ($full_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $country === '' || $phone_code === '' || $phone_num === '') {
    json_out(['ok' => false, 'error' => 'Please complete all fields with valid values.']);
  }

  try {
    $st = $pdo->prepare("SELECT id FROM leads WHERE email = :em LIMIT 1");
    $st->execute([':em' => $email]);
    if ($st->fetchColumn()) {
      // Sudah ada lead, tetap log percobaan register (status exists)
      log_pricelist_attempt($pdo, $email, $actsCSV, 'exists');
      json_out(['ok' => true, 'created' => false, 'lead_id' => null]);
    }

    $id = gen_id();
    $phone = ltrim($phone_code, '+') . normalize_phone($phone_num);
    $phone = $phone ? '+' . $phone : '';
    $ts = now();

    $sql = "INSERT INTO leads
              (id, `column`, name, email, phone, country, source, brand, created_at, updated_at)
            VALUES
              (:id, :col, :name, :email, :phone, :country, :source, :brand, :c, :u)";
    $pdo->prepare($sql)->execute([
      ':id' => $id,
      ':col' => 'leads',
      ':name' => $full_name,
      ':email' => $email,
      ':phone' => $phone,
      ':country' => $country,
      ':source' => 'Reservation Pricelist',
      ':brand' => 'BALI DIVING',
      ':c' => $ts,
      ':u' => $ts
    ]);

    // Email notifikasi lead baru (langsung)
    send_lead_email([
      'name' => $full_name,
      'email' => $email,
      'country' => $country,
      'phone' => $phone,
      'created_at' => $ts,
      'source' => 'Reservation Pricelist'
    ]);

    // ===== EKSEKUSI FACEBOOK CAPI =====
    send_fb_capi_event('CompleteRegistration', [
      'name'  => $full_name,
      'email' => $email,
      'phone' => $phone
    ]);
    // ===================================

    // Log status registered
    log_pricelist_attempt($pdo, $email, $actsCSV, 'registered');

    json_out(['ok' => true, 'created' => true, 'lead_id' => $id]);
  } catch (Throwable $e) {
    error_log("[reservation.php] INSERT lead failed: " . $e->getMessage());
    json_out(['ok' => false, 'error' => 'Insert failed']);
  }
}

/* ===== Optional: Preview digest hari ini di browser ===== */
if ($action === 'digest_preview' && $_SERVER['REQUEST_METHOD'] === 'GET') {
  $today = date('Y-m-d');
  $stmt = $pdo->prepare("
    SELECT id,email,activities,status,ip,user_agent,created_at
    FROM pricelist_attempts
    WHERE DATE(created_at)=:d
    ORDER BY created_at ASC
  ");
  $stmt->execute([':d' => $today]);
  $rows = $stmt->fetchAll();

  header('Content-Type: text/html; charset=utf-8');
  echo "<h2>Preview Pricelist Attempts (Today: {$today})</h2>";
  if (!$rows) {
    echo "<p>No data today.</p>";
    exit;
  }

  echo "<table border='1' cellspacing='0' cellpadding='6'>
    <tr><th>#</th><th>Time</th><th>Email</th><th>Activities</th><th>Status</th><th>IP</th><th>UA</th></tr>";
  $i = 1;
  foreach ($rows as $r) {
    echo "<tr>
      <td>" . ($i++) . "</td>
      <td>" . htmlspecialchars($r['created_at']) . "</td>
      <td>" . htmlspecialchars($r['email']) . "</td>
      <td>" . htmlspecialchars($r['activities'] ?? '') . "</td>
      <td>" . htmlspecialchars($r['status']) . "</td>
      <td>" . htmlspecialchars($r['ip'] ?? '') . "</td>
      <td>" . htmlspecialchars($r['user_agent'] ?? '') . "</td>
    </tr>";
  }
  echo "</table>";
  exit;
}

if (!function_exists('get_dynamic_bca_rate')) {
  function get_dynamic_bca_rate($default_rate = 17595) {
    $cacheFile = __DIR__ . '/cart/bca_usd_rate.json';
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

$db_fallback = 16000;
try {
  $g = $pdo->query("SELECT usd_to_idr FROM booking_globals ORDER BY id DESC LIMIT 1")->fetch();
  if ($g && !empty($g['usd_to_idr']) && (int)$g['usd_to_idr'] > 0) {
    $db_fallback = (int)$g['usd_to_idr'];
  }
} catch (Throwable $e) {
  // ignore, fallback is 16000
}

$USD_TO_IDR = (int)get_dynamic_bca_rate($db_fallback);

$usd_snorkeling = (int)round(1100000 / $USD_TO_IDR);
$usd_try_diving = (int)round(2100000 / $USD_TO_IDR);
$usd_go_diving = (int)round(1650000 / $USD_TO_IDR);
$usd_learn_diving = (int)round(1200000 / $USD_TO_IDR);

/* ===== RENDER PAGE (UI tetap) ===== */
?>
<!doctype html>
<html lang="id" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
    require_once 'template/seo_manager.php';
    echo generate_seo_tags('pricelist');
  ?>
  <link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#3552c8',
            secondary: '#f23d4e',
            accent: '#0070d3',
            teal: '#23a0b4',
            gold: '#eebe35',
            lightblue: '#a2d2fa',
            navy: '#063c7f'
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Exo+2:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,500;1,700&display=swap');

    body {
      box-sizing: border-box;
      font-family: 'Exo 2', sans-serif;
    }

    .ocean-gradient {
      background-color: #063c7f;
      background-image:
        url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='bubbles' x='0' y='0' width='100' height='100' patternUnits='userSpaceOnUse'%3E%3Ccircle cx='15' cy='20' r='3' fill='%23a2d2fa' opacity='0.15'/%3E%3Ccircle cx='45' cy='65' r='4' fill='%23a2d2fa' opacity='0.12'/%3E%3Ccircle cx='75' cy='35' r='2.5' fill='%23a2d2fa' opacity='0.18'/%3E%3Ccircle cx='30' cy='80' r='2' fill='%23a2d2fa' opacity='0.2'/%3E%3Ccircle cx='85' cy='75' r='3.5' fill='%23a2d2fa' opacity='0.1'/%3E%3Ccircle cx='60' cy='15' r='2' fill='%23a2d2fa' opacity='0.16'/%3E%3Ccircle cx='20' cy='50' r='2.5' fill='%23a2d2fa' opacity='0.14'/%3E%3Ccircle cx='90' cy='45' r='1.5' fill='%23a2d2fa' opacity='0.2'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100' height='100' fill='url(%23bubbles)'/%3E%3C/svg%3E"),
        url("data:image/svg+xml,%3Csvg width='200' height='200' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='waves' x='0' y='0' width='200' height='200' patternUnits='userSpaceOnUse'%3E%3Cpath d='M0 50 Q 50 30, 100 50 T 200 50' stroke='%23a2d2fa' stroke-width='1.5' fill='none' opacity='0.12'/%3E%3Cpath d='M0 100 Q 50 80, 100 100 T 200 100' stroke='%23a2d2fa' stroke-width='1.5' fill='none' opacity='0.08'/%3E%3Cpath d='M0 150 Q 50 130, 100 150 T 200 150' stroke='%2323a0b4' stroke-width='1' fill='none' opacity='0.06'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='200' height='200' fill='url(%23waves)'/%3E%3C/svg%3E"),
        linear-gradient(180deg, #063c7f 0%, #3552c8 40%, #000000 100%);
      background-size: 100px 100px, 200px 200px, 100% 100%;
      background-position: 0 0, 0 0, center;
      animation: underwater-float 25s ease-in-out infinite;
    }

    @keyframes underwater-float {
      0%, 100% { background-position: 0 0, 0 0, center; }
      25% { background-position: 8px -8px, -12px 4px, center; }
      50% { background-position: -4px -16px, 16px -8px, center; }
      75% { background-position: -8px -4px, 4px 12px, center; }
    }

    /* Glassmorphism Sport Dashboard */
    .scuba-card {
      background: rgba(6, 60, 127, 0.45);
      border: 1px solid rgba(162, 210, 250, 0.25);
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5), 0 0 15px rgba(162, 210, 250, 0.1);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      color: #f1f5f9;
      position: relative;
      overflow: hidden;
    }

    .scuba-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -50%;
      width: 200%;
      height: 100%;
      background: linear-gradient(
        to right,
        rgba(255, 255, 255, 0) 0%,
        rgba(162, 210, 250, 0.05) 50%,
        rgba(255, 255, 255, 0) 100%
      );
      transform: skewX(-25deg);
      pointer-events: none;
      animation: sweep 12s ease-in-out infinite;
    }

    @keyframes sweep {
      0% { left: -95%; }
      50% { left: 95%; }
      100% { left: 95%; }
    }

    .scuba-header-ring {
      background: linear-gradient(135deg, #0070d3 0%, #23a0b4 100%);
      box-shadow: 0 0 20px rgba(0, 112, 211, 0.4);
      border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .animate-spin-slow {
      animation: spin 12s linear infinite;
    }

    /* Carbon Texture for header and lists */
    .carbon-bg {
      background-color: rgba(6, 60, 127, 0.25);
      background-image: 
        radial-gradient(rgba(35, 160, 180, 0.15) 1px, transparent 0),
        radial-gradient(rgba(35, 160, 180, 0.1) 1px, transparent 0);
      background-size: 8px 8px;
      background-position: 0 0, 4px 4px;
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .neon-text-gradient {
      background: linear-gradient(135deg, #a2d2fa 10%, #ffffff 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* Activity Card Styles */
    .activity-tile {
      background: rgba(6, 60, 127, 0.35);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
      position: relative;
    }

    .activity-tile:hover {
      border-color: rgba(35, 160, 180, 0.5);
      background: rgba(6, 60, 127, 0.60);
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3), 0 0 12px rgba(35, 160, 180, 0.15);
    }

    .activity-tile.selected {
      border-color: #23a0b4;
      background: rgba(35, 160, 180, 0.15);
      box-shadow: 0 0 15px rgba(35, 160, 180, 0.25), inset 0 0 10px rgba(35, 160, 180, 0.1);
    }

    .activity-price {
      color: #f23d4e; /* Coral Red */
      font-weight: 700;
    }

    /* Sporty Inputs */
    .sport-input {
      background: rgba(6, 60, 127, 0.3);
      border: 1px solid rgba(162, 210, 250, 0.2);
      color: #f1f5f9;
      transition: all 0.3s;
    }

    .sport-input:focus {
      outline: none;
      border-color: #23a0b4;
      box-shadow: 0 0 0 3px rgba(35, 160, 180, 0.25), 0 0 12px rgba(35, 160, 180, 0.15);
      background: rgba(6, 60, 127, 0.5);
    }

    /* High-Performance Submit Button */
    .sport-btn {
      background: linear-gradient(135deg, #0070d3 0%, #3552c8 100%);
      color: #ffffff;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      box-shadow: 0 4px 15px rgba(0, 112, 211, 0.35);
      transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      cursor: pointer;
    }

    .sport-btn:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 112, 211, 0.55), 0 0 15px rgba(255, 255, 255, 0.2);
      filter: brightness(1.1);
    }

    .sport-btn:active:not(:disabled) {
      transform: translateY(1px);
      box-shadow: 0 2px 8px rgba(0, 112, 211, 0.35);
    }

    .sport-btn:disabled {
      background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
      color: #64748b;
      border-color: rgba(255, 255, 255, 0.05);
      box-shadow: none;
      cursor: not-allowed;
    }

    .fade-in {
      animation: fadeIn 0.7s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .loading-spinner {
      border: 3px solid rgba(255, 255, 255, 0.2);
      border-top: 3px solid #ffffff;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Modal Styling */
    .scuba-modal {
      background: rgba(6, 60, 127, 0.95);
      border: 1px solid rgba(162, 210, 250, 0.35);
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.8), 0 0 25px rgba(35, 160, 180, 0.2);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      color: #f1f5f9;
    }

    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(2, 6, 12, 0.8);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 50;
    }

    .modal-backdrop.show {
      display: flex;
    }

    .reef-container {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 0;
      pointer-events: none;
      line-height: 0;
      opacity: 0.35;
    }

    .reef-layer {
      transform-origin: bottom center;
    }

    .sway-1 { animation: sway 8s ease-in-out infinite alternate; }
    .sway-2 { animation: sway 6s ease-in-out infinite alternate-reverse; }
    .sway-3 { animation: sway 10s ease-in-out infinite alternate; }

    @keyframes sway {
      0% { transform: rotate(0deg) translateX(0); }
      100% { transform: rotate(2deg) translateX(10px); }
    }
  </style>
</head>

<body class="h-full ocean-gradient relative overflow-x-hidden">
  <main class="min-h-full flex items-center justify-center p-3 sm:p-4 relative z-10">
    <div class="scuba-card rounded-2xl max-w-md w-full p-5 sm:p-8 fade-in">
      <div class="text-center mb-6">
        <div class="w-16 h-16 scuba-header-ring rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-compass text-2xl text-[#020c1b] animate-spin-slow"></i>
        </div>
        <h1 id="main-title" class="text-2xl font-black neon-text-gradient tracking-wider mb-2 uppercase">PRICELIST</h1>
        <p id="subtitle" class="text-xs text-slate-300 leading-relaxed">Thank you for coming to Bali Diving! We really appreciate you choosing us and we're grateful for your warm presence.</p>
      </div>

      <form id="reservationForm" class="space-y-5">
        <div>
          <div class="carbon-bg rounded-xl p-4 mb-4">
            <h3 class="text-[10px] font-bold uppercase tracking-widest text-teal mb-3 flex items-center gap-1.5">
              <i class="fa-solid fa-circle-check text-xs"></i>
              <span>All-Inclusive Package Inclusions</span>
            </h3>
            <ul class="grid grid-cols-2 gap-x-4 gap-y-2 text-[11px] text-slate-300">
              <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-angles-right text-teal text-[9px]"></i>
                <span>Hotel Pickup</span>
              </li>
              <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-angles-right text-teal text-[9px]"></i>
                <span>Up to 3 Ocean Dives</span>
              </li>
              <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-angles-right text-teal text-[9px]"></i>
                <span>Lunch Included</span>
              </li>
              <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-angles-right text-teal text-[9px]"></i>
                <span>Fast Boat</span>
              </li>
              <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-angles-right text-teal text-[9px]"></i>
                <span>PADI Pro Guide</span>
              </li>
              <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-angles-right text-teal text-[9px]"></i>
                <span>Premium Gear</span>
              </li>
              <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-angles-right text-teal text-[9px]"></i>
                <span>Porter &amp; Permits</span>
              </li>
              <li class="flex items-center gap-1.5">
                <i class="fa-solid fa-angles-right text-teal text-[9px]"></i>
                <span>Dive Insurance</span>
              </li>
            </ul>
          </div>

          <label id="activity-label" class="block text-xs font-bold uppercase tracking-widest text-lightblue mb-3">
            Select Your Interests <span class="text-slate-400 font-normal normal-case">(Choose one or more) *</span>
          </label>
          <div class="grid grid-cols-2 gap-3">
            <label class="activity-tile flex flex-col items-center p-3 cursor-pointer text-center select-none">
              <input type="checkbox" name="activities" value="snorkeling" class="hidden">
              <div class="w-full h-[75px] rounded-lg overflow-hidden mb-2 border border-slate-700/60 shadow-inner">
                <img src="https://balidiving.com/images/thumbnail/pricelist/1.jpg" alt="Snorkeling" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
              </div>
              <div class="font-bold text-slate-100 text-xs tracking-wide">Snorkeling</div>
              <div class="text-[9px] text-slate-400 mt-0.5">Explore underwater beauty</div>
              <div class="text-[11px] mt-1.5 flex flex-col items-center">
                <span class="text-rose-400 font-extrabold text-[13px]">IDR 1.100</span>
                <span class="text-teal text-[10px] font-bold mt-0.5">(approx. $<?= $usd_snorkeling; ?> USD)</span>
              </div>
            </label>
            <label class="activity-tile flex flex-col items-center p-3 cursor-pointer text-center select-none">
              <input type="checkbox" name="activities" value="try-diving" class="hidden">
              <div class="w-full h-[75px] rounded-lg overflow-hidden mb-2 border border-slate-700/60 shadow-inner">
                <img src="https://balidiving.com/images/thumbnail/pricelist/2.jpg" alt="Try Diving" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
              </div>
              <div class="font-bold text-slate-100 text-xs tracking-wide">Try Diving</div>
              <div class="text-[9px] text-slate-400 mt-0.5">No certificate required</div>
              <div class="text-[11px] mt-1.5 flex flex-col items-center">
                <span class="text-rose-400 font-extrabold text-[13px]">IDR 2.100</span>
                <span class="text-teal text-[10px] font-bold mt-0.5">(approx. $<?= $usd_try_diving; ?> USD)</span>
              </div>
            </label>
            <label class="activity-tile flex flex-col items-center p-3 cursor-pointer text-center select-none">
              <input type="checkbox" name="activities" value="certified-diving" class="hidden">
              <div class="w-full h-[75px] rounded-lg overflow-hidden mb-2 border border-slate-700/60 shadow-inner">
                <img src="https://balidiving.com/images/thumbnail/pricelist/3.jpg" alt="Go Diving" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
              </div>
              <div class="font-bold text-slate-100 text-xs tracking-wide">Go Diving</div>
              <div class="text-[9px] text-slate-400 mt-0.5">For certified divers</div>
              <div class="text-[11px] mt-1.5 flex flex-col items-center">
                <span class="text-rose-400 font-extrabold text-[13px]">IDR 1.650</span>
                <span class="text-teal text-[10px] font-bold mt-0.5">(approx. $<?= $usd_go_diving; ?> USD)</span>
              </div>
            </label>
            <label class="activity-tile flex flex-col items-center p-3 cursor-pointer text-center select-none">
              <input type="checkbox" name="activities" value="learn-diving" class="hidden">
              <div class="w-full h-[75px] rounded-lg overflow-hidden mb-2 border border-slate-700/60 shadow-inner">
                <img src="https://balidiving.com/images/thumbnail/pricelist/4.jpg" alt="Learn Diving" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
              </div>
              <div class="font-bold text-slate-100 text-xs tracking-wide">Learn Diving</div>
              <div class="text-[9px] text-slate-400 mt-0.5">Get certified</div>
              <div class="text-[11px] mt-1.5 flex flex-col items-center">
                <span class="text-rose-400 font-extrabold text-[13px]">IDR 1.200</span>
                <span class="text-teal text-[10px] font-bold mt-0.5">(approx. $<?= $usd_learn_diving; ?> USD)</span>
              </div>
            </label>
          </div>
          <div class="text-[10px] text-slate-400 text-center mt-3.5 font-semibold tracking-wide leading-relaxed">
            * Price in Rupiah(IDR) and '000'  (thousands omitted)<br>
            Rate US$ 1 = IDR <?= number_format($USD_TO_IDR, 0, ',', '.'); ?> (Based Bank Central Asia)<!-- Dynamic BCA Rate Sync check: <?= $USD_TO_IDR ?> -->
          </div>
        </div>

        <div>
          <label for="email" id="email-label" class="block text-xs font-bold uppercase tracking-widest text-lightblue mb-2">
            Email Address * <span class="text-slate-400 font-normal normal-case">(Existing or create new)</span>
          </label>
          <input type="email" id="email" name="email" required
            class="w-full px-4 py-3 rounded-xl sport-input transition-all duration-300 text-sm"
            placeholder="your-email@adventure.com">
          <div id="email-error" class="mt-1.5 text-xs text-rose-400 hidden font-medium"></div>
        </div>

        <button type="submit" id="submit-btn" class="w-full sport-btn py-3.5 px-6 rounded-xl flex items-center justify-center">
          <span id="button-text">Unlock Full Pricelist & Deals</span>
        </button>
      </form>

      <div class="mt-6 pt-5 border-t border-slate-800">
        <div class="flex items-center justify-center space-x-6 text-xs text-slate-400">
          <div class="flex items-center gap-1.5">
            <i class="fa-solid fa-shield-halved text-teal"></i>
            <span>Secure &amp; Trusted</span>
          </div>
          <div class="flex items-center gap-1.5">
            <i class="fa-solid fa-lock text-teal"></i>
            <span>Email Protected</span>
          </div>
        </div>
      </div>
    </div>
  </main>

  <div class="reef-container">
    <svg class="w-full h-auto min-h-[120px] max-h-[250px]" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"
      preserveAspectRatio="none">
      <defs>
        <linearGradient id="reef-grad-1" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" style="stop-color:#23a0b4;stop-opacity:0.6" />
          <stop offset="100%" style="stop-color:#063c7f;stop-opacity:0.9" />
        </linearGradient>
        <linearGradient id="reef-grad-2" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" style="stop-color:#0070d3;stop-opacity:0.5" />
          <stop offset="100%" style="stop-color:#3552c8;stop-opacity:0.8" />
        </linearGradient>
      </defs>

      <path class="reef-layer sway-3" fill="url(#reef-grad-2)"
        d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,197.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
      </path>

      <path class="reef-layer sway-1" fill="#063c7f" fill-opacity="0.8"
        d="M0,256L60,245.3C120,235,240,213,360,218.7C480,224,600,256,720,266.7C840,277,960,267,1080,240C1200,213,1320,171,1380,149.3L1440,128L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z">
      </path>

      <path class="reef-layer sway-2" fill="#3552c8" fill-opacity="0.9"
        d="M0,288L40,272C80,256,160,224,240,218.7C320,213,400,235,480,250.7C560,267,640,277,720,261.3C800,245,880,203,960,197.3C1040,192,1120,224,1200,240C1280,256,1360,256,1400,256L1440,256L1440,320L1400,320C1360,320,1280,320,1200,320C1120,320,1040,320,960,320C880,320,800,320,720,320C640,320,560,320,480,320C400,320,320,320,240,320C160,320,80,320,40,320L0,320Z">
      </path>

      <circle cx="100" cy="280" r="3" fill="white" opacity="0.3">
        <animate attributeName="cy" from="280" to="100" dur="4s" repeatCount="indefinite" />
        <animate attributeName="opacity" values="0.3;0" dur="4s" repeatCount="indefinite" />
      </circle>
      <circle cx="400" cy="250" r="2" fill="white" opacity="0.4">
        <animate attributeName="cy" from="250" to="50" dur="6s" repeatCount="indefinite" />
        <animate attributeName="opacity" values="0.4;0" dur="6s" repeatCount="indefinite" />
      </circle>
      <circle cx="900" cy="290" r="4" fill="white" opacity="0.2">
        <animate attributeName="cy" from="290" to="120" dur="5s" repeatCount="indefinite" />
        <animate attributeName="opacity" values="0.2;0" dur="5s" repeatCount="indefinite" />
      </circle>
    </svg>
  </div>

  <div id="registerModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-label="Register new profile">
    <div class="scuba-modal w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-base font-extrabold neon-text-gradient uppercase tracking-wider">Create Your Profile</h3>
        <button id="closeModal" class="p-2 rounded-lg text-slate-400 hover:text-teal transition-colors" aria-label="Close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <p class="text-xs text-slate-300 mb-4">Just one time! Enjoy all our services</p>

      <div id="regError"
        class="hidden mb-4 rounded-xl px-3.5 py-2.5 text-xs bg-rose-950/60 text-rose-300 border border-rose-800/80"></div>

      <form id="registerForm" class="space-y-4">
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-lightblue mb-1.5">Country</label>
          <select id="countrySelect" name="country" class="w-full sport-input rounded-xl px-3 py-2 text-sm"
            required></select>
        </div>

        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-lightblue mb-1.5">Phone / WhatsApp</label>
          <div class="flex gap-2">
            <select id="phoneCodeSelect" name="phone_code"
              class="min-w-[130px] sport-input rounded-xl px-3 py-2 text-sm" required></select>
            <input type="tel" name="phone_number" class="flex-1 sport-input rounded-xl px-3 py-2 text-sm"
              placeholder="81234567890" required>
          </div>
          <p class="text-[9px] text-slate-400 mt-1">Example: choose +62 and fill 8123… (without 0)</p>
        </div>

        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-lightblue mb-1.5">Full name</label>
          <input type="text" name="full_name" class="w-full sport-input rounded-xl px-3 py-2 text-sm" placeholder="John Doe" required>
        </div>

        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-lightblue mb-1.5">Email</label>
          <input type="email" name="email" id="regEmail" class="w-full sport-input rounded-xl px-3 py-2 text-sm"
            required>
        </div>

        <button id="regSubmit" type="submit" class="w-full sport-btn py-3 px-4 rounded-xl font-bold mt-2">
          Unlock Full Pricelist & Deals
        </button>
      </form>
    </div>
  </div>


  <script>

    // ====== Config text (optional integration with elementSdk) ======
    const defaultConfig = {
      main_title: "UPDATED PRICELIST",
      subtitle: "Thank you for coming to Bali Diving! We really appreciate you choosing us and we're grateful for your warm presence.",
      activity_label: "Select Your Interests (Choose one or more) *",
      email_label: "Email Address *",
      button_text: "Unlock Full Pricelist & Deals"
    };
    if (window.elementSdk) {
      window.elementSdk.init({
        defaultConfig: defaultConfig,
        onConfigChange: async (config) => {
          document.getElementById('main-title').textContent = config.main_title || defaultConfig.main_title;
          document.getElementById('subtitle').textContent = config.subtitle || defaultConfig.subtitle;
          document.getElementById('activity-label').textContent = config.activity_label || defaultConfig.activity_label;
          document.getElementById('email-label').textContent = config.email_label || defaultConfig.email_label;
          document.getElementById('button-text').textContent = config.button_text || defaultConfig.button_text;
        },
        mapToCapabilities: (config) => ({
          recolorables: [],
          borderables: [],
          fontEditable: undefined,
          fontSizeable: undefined
        }),
        mapToEditPanelValues: (config) => new Map([
          ["main_title", config.main_title || defaultConfig.main_title],
          ["subtitle", config.subtitle || defaultConfig.subtitle],
          ["activity_label", config.activity_label || defaultConfig.activity_label],
          ["email_label", config.email_label || defaultConfig.email_label],
          ["button_text", config.button_text || defaultConfig.button_text]
        ])
      });
    }

    const baseUrl = 'https://balidiving.com/pricing';

    function openPricelist(email, selectedActivities) {
      const params = new URLSearchParams({
        source: 'pricelist_gate',
        email: email
      });
      let targetUrl = baseUrl;
      if (selectedActivities && selectedActivities.length > 0) {
        params.append('activities', selectedActivities.join(','));
        // If the user ONLY selected "learn-diving", send them directly to the courses pricing page
        if (selectedActivities.length === 1 && selectedActivities[0] === 'learn-diving') {
          targetUrl = 'https://balidiving.com/pricing-courses';
        }
      }
      const redirectUrl = `${targetUrl}?${params.toString()}`;
      window.location.href = redirectUrl;
    }

    // ====== Form handling ======
    const form = document.getElementById('reservationForm');
    const submitBtn = document.getElementById('submit-btn');
    const buttonText = document.getElementById('button-text');
    const emailError = document.getElementById('email-error');

    const modal = document.getElementById('registerModal');
    const closeModal = document.getElementById('closeModal');
    closeModal.addEventListener('click', () => modal.classList.remove('show'));

    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const fd = new FormData(this);
      const selectedActivities = fd.getAll('activities');
      const email = (fd.get('email') || '').trim();

      // Reset error state
      emailError.classList.add('hidden');
      emailError.textContent = '';

      if (selectedActivities.length === 0) {
        emailError.textContent = 'Please select at least one activity';
        emailError.classList.remove('hidden');
        return;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        emailError.textContent = 'Please enter a valid email address';
        emailError.classList.remove('hidden');
        return;
      }

      const spamPatterns = [
        /@(?:test|fake|spam|temp|dummy|example|sample|mailinator|yopmail)\./i,
        /^(?:test|fake|spam|dummy)@/i
      ];
      if (spamPatterns.some(p => p.test(email))) {
        emailError.textContent = 'Please enter a real email address. This looks like a temporary or fake email.';
        emailError.classList.remove('hidden');
        return;
      }

      submitBtn.disabled = true;
      submitBtn.classList.add('opacity-75');
      buttonText.innerHTML = '<div class="loading-spinner mr-2"></div>Processing...';

      try {
        const res = await fetch(location.pathname + '?action=check_email', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          // >>> KIRIM ACTIVITIES AGAR TERLOGGING
          body: JSON.stringify({ email, activities: selectedActivities })
        });
        const data = await res.json();

        if (!data.ok) throw new Error(data.error || 'Check failed');

        if (data.exists) {
          openPricelist(email, selectedActivities);
        } else {
          document.getElementById('regEmail').value = email;
          // Simpan juga activities ke form register supaya bisa dikirim saat register (opsional)
          // Tambahkan input hidden dinamis
          const oldActs = document.querySelector('#registerForm input[name="activities"]');
          if (oldActs) oldActs.remove();
          const hiddenActs = document.createElement('input');
          hiddenActs.type = 'hidden';
          hiddenActs.name = 'activities';
          hiddenActs.value = selectedActivities.join(',');
          document.getElementById('registerForm').appendChild(hiddenActs);

          modal.classList.add('show');
        }

        const successDiv = document.createElement('div');
        successDiv.className = 'mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm text-center';
        successDiv.textContent = data.exists
          ? 'Success! Reservation page has been opened in a new tab.'
          : 'Please complete registration to continue.';
        form.appendChild(successDiv);
        setTimeout(() => successDiv.remove(), 5000);

      } catch (err) {
        emailError.textContent = err.message || 'Unexpected error';
        emailError.classList.remove('hidden');
      } finally {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75');
        buttonText.innerHTML = 'Unlock Full Pricelist & Deals';
      }
    });

    // ====== Country & Phone Code data ======
    const POPULAR_ISO = ['AU', 'SG', 'MY', 'US', 'GB', 'DE', 'FR', 'NL', 'IN', 'CN', 'JP', 'KR', 'ID'];
    const COUNTRIES = [
      { iso: 'ID', name: 'Indonesia', dial: '+62' },
      { iso: 'AU', name: 'Australia', dial: '+61' },
      { iso: 'SG', name: 'Singapore', dial: '+65' },
      { iso: 'MY', name: 'Malaysia', dial: '+60' },
      { iso: 'US', name: 'United States', dial: '+1' },
      { iso: 'GB', name: 'United Kingdom', dial: '+44' },
      { iso: 'DE', name: 'Germany', dial: '+49' },
      { iso: 'FR', name: 'France', dial: '+33' },
      { iso: 'NL', name: 'Netherlands', dial: '+31' },
      { iso: 'IN', name: 'India', dial: '+91' },
      { iso: 'CN', name: 'China', dial: '+86' },
      { iso: 'JP', name: 'Japan', dial: '+81' },
      { iso: 'KR', name: 'South Korea', dial: '+82' },
      { iso: 'NZ', name: 'New Zealand', dial: '+64' },
      { iso: 'CA', name: 'Canada', dial: '+1' },
      { iso: 'IE', name: 'Ireland', dial: '+353' },
      { iso: 'ES', name: 'Spain', dial: '+34' },
      { iso: 'IT', name: 'Italy', dial: '+39' },
      { iso: 'CH', name: 'Switzerland', dial: '+41' },
      { iso: 'AT', name: 'Austria', dial: '+43' },
      { iso: 'BE', name: 'Belgium', dial: '+32' },
      { iso: 'SE', name: 'Sweden', dial: '+46' },
      { iso: 'NO', name: 'Norway', dial: '+47' },
      { iso: 'DK', name: 'Denmark', dial: '+45' },
      { iso: 'FI', name: 'Finland', dial: '+358' },
      { iso: 'PT', name: 'Portugal', dial: '+351' },
      { iso: 'GR', name: 'Greece', dial: '+30' },
      { iso: 'CZ', name: 'Czechia', dial: '+420' },
      { iso: 'PL', name: 'Poland', dial: '+48' },
      { iso: 'HU', name: 'Hungary', dial: '+36' },
      { iso: 'RO', name: 'Romania', dial: '+40' },
      { iso: 'RU', name: 'Russia', dial: '+7' },
      { iso: 'UA', name: 'Ukraine', dial: '+380' },
      { iso: 'TR', name: 'Türkiye', dial: '+90' },
      { iso: 'AE', name: 'United Arab Emirates', dial: '+971' },
      { iso: 'SA', name: 'Saudi Arabia', dial: '+966' },
      { iso: 'QA', name: 'Qatar', dial: '+974' },
      { iso: 'KW', name: 'Kuwait', dial: '+965' },
      { iso: 'BH', name: 'Bahrain', dial: '+973' },
      { iso: 'OM', name: 'Oman', dial: '+968' },
      { iso: 'EG', name: 'Egypt', dial: '+20' },
      { iso: 'ZA', name: 'South Africa', dial: '+27' },
      { iso: 'TH', name: 'Thailand', dial: '+66' },
      { iso: 'PH', name: 'Philippines', dial: '+63' },
      { iso: 'VN', name: 'Vietnam', dial: '+84' },
      { iso: 'KH', name: 'Cambodia', dial: '+855' },
      { iso: 'LA', name: 'Laos', dial: '+856' },
      { iso: 'MM', name: 'Myanmar', dial: '+95' },
      { iso: 'BN', name: 'Brunei', dial: '+673' },
      { iso: 'LK', name: 'Sri Lanka', dial: '+94' },
      { iso: 'NP', name: 'Nepal', dial: '+977' },
      { iso: 'BD', name: 'Bangladesh', dial: '+880' },
      { iso: 'PK', name: 'Pakistan', dial: '+92' },
      { iso: 'HK', name: 'Hong Kong', dial: '+852' },
      { iso: 'MO', name: 'Macao', dial: '+853' },
      { iso: 'TW', name: 'Taiwan', dial: '+886' },
      { iso: 'BR', name: 'Brazil', dial: '+55' },
      { iso: 'AR', name: 'Argentina', dial: '+54' },
      { iso: 'MX', name: 'Mexico', dial: '+52' },
      { iso: 'CL', name: 'Chile', dial: '+56' },
      { iso: 'PE', name: 'Peru', dial: '+51' },
      { iso: 'CO', name: 'Colombia', dial: '+57' },
      { iso: 'IL', name: 'Israel', dial: '+972' },
      { iso: 'IR', name: 'Iran', dial: '+98' },
      { iso: 'KE', name: 'Kenya', dial: '+254' },
      { iso: 'NG', name: 'Nigeria', dial: '+234' },
      { iso: 'MA', name: 'Morocco', dial: '+212' },
      { iso: 'TN', name: 'Tunisia', dial: '+216' }
    ];
    const byISO = new Map(COUNTRIES.map(c => [c.iso, c]));
    const byDial = new Map();
    COUNTRIES.forEach(c => { if (!byDial.has(c.dial)) byDial.set(c.dial, []); byDial.get(c.dial).push(c); });

    function renderCountryOptions(selectEl) {
      const popular = COUNTRIES.filter(c => POPULAR_ISO.includes(c.iso));
      const others = COUNTRIES.filter(c => !POPULAR_ISO.includes(c.iso)).sort((a, b) => a.name.localeCompare(b.name));

      const frag = document.createDocumentFragment();
      const optPopular = document.createElement('optgroup'); optPopular.label = 'Recents guest';
      popular.forEach(c => { const o = document.createElement('option'); o.value = c.name; o.textContent = c.name; o.dataset.iso = c.iso; o.dataset.dial = c.dial; optPopular.appendChild(o); });
      frag.appendChild(optPopular);

      const optAll = document.createElement('optgroup'); optAll.label = 'All Countries';
      others.forEach(c => { const o = document.createElement('option'); o.value = c.name; o.textContent = c.name; o.dataset.iso = c.iso; o.dataset.dial = c.dial; optAll.appendChild(o); });
      frag.appendChild(optAll);

      selectEl.innerHTML = ''; selectEl.appendChild(frag);
    }

    function renderDialOptions(selectEl) {
      const uniq = Array.from(new Set(COUNTRIES.map(c => c.dial))).sort((a, b) => (a.replace('+', '') * 1) - (b.replace('+', '') * 1));
      const frag = document.createDocumentFragment();

      const popularDials = [];
      POPULAR_ISO.forEach(iso => { const c = byISO.get(iso); if (c && !popularDials.includes(c.dial)) popularDials.push(c.dial); });

      const optPopular = document.createElement('optgroup'); optPopular.label = 'Popular Codes';
      popularDials.forEach(d => { const list = byDial.get(d) || []; const label = `${d} (${list.map(x => x.name).join(' / ')})`; const o = document.createElement('option'); o.value = d; o.textContent = label; o.dataset.isoList = list.map(x => x.iso).join(','); optPopular.appendChild(o); });
      frag.appendChild(optPopular);

      const optAll = document.createElement('optgroup'); optAll.label = 'All Codes';
      uniq.forEach(d => { if (popularDials.includes(d)) return; const list = byDial.get(d) || []; const label = `${d} (${list.map(x => x.name).join(' / ')})`; const o = document.createElement('option'); o.value = d; o.textContent = label; o.dataset.isoList = list.map(x => x.iso).join(','); optAll.appendChild(o); });
      frag.appendChild(optAll);

      selectEl.innerHTML = ''; selectEl.appendChild(frag);
    }

    const countrySelect = document.getElementById('countrySelect');
    const phoneCodeSelect = document.getElementById('phoneCodeSelect');

    renderCountryOptions(countrySelect);
    renderDialOptions(phoneCodeSelect);

    function setDefaultsToIndonesia() {
      const defISO = 'ID';
      const c = byISO.get(defISO);
      if (!c) return;
      const opt = Array.from(countrySelect.options).find(o => o.dataset && o.dataset.iso === defISO);
      if (opt) { countrySelect.value = opt.value; }
      phoneCodeSelect.value = c.dial;
    }
    setDefaultsToIndonesia();

    countrySelect.addEventListener('change', () => {
      const selectedOpt = countrySelect.options[countrySelect.selectedIndex];
      const dial = selectedOpt?.dataset?.dial;
      if (dial) {
        phoneCodeSelect.value = dial;
        if (phoneCodeSelect.value !== dial) {
          renderDialOptions(phoneCodeSelect);
          phoneCodeSelect.value = dial;
        }
      }
    });

    phoneCodeSelect.addEventListener('change', () => {
      const dial = phoneCodeSelect.value;
      const list = byDial.get(dial) || [];
      if (list.length === 1) {
        const iso = list[0].iso;
        const opt = Array.from(countrySelect.options).find(o => o.dataset && o.dataset.iso === iso);
        if (opt) { countrySelect.value = opt.value; }
      }
    });

    // ====== Register submit ======
    const regForm = document.getElementById('registerForm');
    const regError = document.getElementById('regError');
    const regSubmit = document.getElementById('regSubmit');

    regForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      regError.classList.add('hidden');
      regError.textContent = '';

      const fd = new FormData(regForm);
      const payload = Object.fromEntries(fd.entries());

      // pastikan activities (hidden) juga ikut, kalau ada
      if (payload.activities && typeof payload.activities === 'string') {
        payload.activities = payload.activities.split(',').filter(Boolean);
      }

      regSubmit.disabled = true;
      regSubmit.classList.add('opacity-75');
      regSubmit.textContent = 'Processing…';

      try {
        const res = await fetch(location.pathname + '?action=register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!data.ok) throw new Error(data.error || 'Register failed');

        const actFD = new FormData(document.getElementById('reservationForm'));
        const selectedActivities = actFD.getAll('activities');
        const email = payload.email;
        openPricelist(email, selectedActivities);

        if (data.created && data.lead_id) {
          console.log('New lead_id:', data.lead_id);
        }

        modal.classList.remove('show');
      } catch (err) {
        regError.textContent = err.message || 'Unexpected error';
        regError.classList.remove('hidden');
      } finally {
        regSubmit.disabled = false;
        regSubmit.classList.remove('opacity-75');
        regSubmit.textContent = 'Unlock Full Pricelist & Deals';
      }
    });

    // ====== Checkbox visual on/off (sporty) ======
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener('change', function () {
        const label = this.closest('label');
        if (this.checked) {
          label.classList.add('selected');
        } else {
          label.classList.remove('selected');
        }
      });
    });
  </script>
</body>

</html>