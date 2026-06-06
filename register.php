<?php
/* =========================================================
   BALI DIVING – Quick Registration + Referral (Email-based)
   - Referral: email terdaftar; jika kosong/invalid/tidak ada -> sales@balidiving.com
   - Button: Continue (buka https://balidiving.com/login)
   - Kirim email welcome ke user (link login + email + password = no. HP 62xxxx atau input user)
   - Buat/pertahankan lead di CRM (tabel leads)
   - Validasi email TLD 2+ huruf (contoh: hi@subhi.me)
   File: register.php | PHP 8+, PDO MySQL
   ========================================================= */

declare(strict_types=1);
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
try { $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $opt); }
catch(Throwable $e){
  http_response_code(500);
  header('Content-Type: text/plain; charset=utf-8');
  echo "DB connect failed: ".$e->getMessage();
  error_log("[register.php] DB connect failed: ".$e->getMessage());
  exit;
}

/* ===== ONE-TIME DDL (aman jika berulang) =====
   Mapping referral email yang dipakai untuk setiap registrasi
================================================ */
try {
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS lead_referrers (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      lead_email VARCHAR(190) NOT NULL,
      ref_email  VARCHAR(190) NOT NULL,
      created_at DATETIME NOT NULL,
      KEY(lead_email),
      KEY(ref_email),
      KEY(created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");
} catch (Throwable $e) {
  error_log('[register.php] DDL lead_referrers failed: '.$e->getMessage());
}

/* ===== HELPERS ===== */
function read_json_body_or_form(): array {
  $ct = $_SERVER['CONTENT_TYPE'] ?? '';
  if (stripos($ct, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
  }
  return $_POST ?: [];
}
function json_out($arr){ header('Content-Type: application/json; charset=utf-8'); echo json_encode($arr); exit; }
function now(){ return date('Y-m-d H:i:s'); }
function normalize_phone($raw){ return preg_replace('/\D+/', '', (string)$raw); }
function gen_id(){ return strtoupper(dechex(time())).substr(strtoupper(md5(uniqid('', true))), 0, 8); }
function client_ip(){
  foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $k){
    if (!empty($_SERVER[$k])){
      $v = $_SERVER[$k];
      if ($k==='HTTP_X_FORWARDED_FOR'){ $v = trim(explode(',', $v)[0]); }
      return $v;
    }
  }
  return null;
}

/* ===== EMAIL: Notif ke Sales (Lead) ===== */
function send_lead_email(array $lead){
  $to = 'sales@balidiving.com';
  $cc = 'subhi@balidiving.com';
  $subject = "Follow Up! {$lead['name']} — New Lead Registered";

  $safe = fn($v)=>htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

  $cleanPhone = preg_replace('/\D+/', '', $lead['phone']);
  $waText = urlencode("Halo " . $lead['name'] . ", thank you for registering with Bali Diving. How can we help you plan your next dive?");
  $waLink = "https://wa.me/" . $cleanPhone . "?text=" . $waText;

  $draftSubject = "Welcome to Bali Diving - Your Inquiry";
  $draftBody = "Dear " . $lead['name'] . ",\n\nThank you for registering with Bali Diving.\n\nWe're excited to help you plan your next dive or snorkeling adventure in Bali. Let us know if you have any questions or need recommendations for the best sites!\n\nBest regards,\nBali Diving Team\nhttps://balidiving.com";
  $gmailLink = "https://mail.google.com/mail/?view=cm&to=" . rawurlencode($lead['email']) . "&su=" . rawurlencode($draftSubject) . "&body=" . rawurlencode($draftBody);

  $html = "
  <html><body style='font-family:Arial,sans-serif;color:#111'>
    <div style='max-width:640px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px'>
      <div style='padding:18px 22px;border-bottom:1px solid #e5e7eb'>
        <h2 style='margin:0;color:#0ea5e9'>New Lead Registered</h2>
        <div style='font-size:13px;color:#6b7280'>Bali Diving Registration</div>
      </div>
      <div style='padding:22px'>
        <table cellspacing='0' cellpadding='8' style='width:100%;border-collapse:collapse;margin-bottom:24px'>
          <tr><td style='width:170px;background:#f9fafb;border:1px solid #eee'><b>Full Name</b></td><td style='border:1px solid #eee'>{$safe($lead['name'])}</td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Email</b></td><td style='border:1px solid #eee'><a href='mailto:{$safe($lead['email'])}'>{$safe($lead['email'])}</a></td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Country</b></td><td style='border:1px solid #eee'>{$safe($lead['country'])}</td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Phone</b></td><td style='border:1px solid #eee'><a href='https://wa.me/{$cleanPhone}' target='_blank'>{$safe($lead['phone'])}</a></td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Registered At</b></td><td style='border:1px solid #eee'>{$safe($lead['created_at'])}</td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>Source</b></td><td style='border:1px solid #eee'>{$safe($lead['source'] ?? 'Registration')}</td></tr>
          <tr><td style='background:#f9fafb;border:1px solid #eee'><b>IP</b></td><td style='border:1px solid #eee'>{$safe($lead['ip'] ?? '')}</td></tr>
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
    </div>
  </body></html>";

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $headers .= "From: Bali Diving <noreply@balidiving.com>\r\n";
  $headers .= "Cc: $cc\r\n";
  @mail($to, $subject, $html, $headers);
}

/* ===== EMAIL: Welcome Login ke User ===== */
function send_login_email(string $toEmail, string $name, string $loginEmail, string $password): void {
  $safe = fn($v)=>htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
  $subject = 'Welcome to Bali Diving — Your Login Details';
  $loginUrl = 'https://balidiving.com/login?status=active';
  $html = "<html><body style='font-family:Arial,sans-serif;color:#0f172a'>
    <div style='max-width:640px;margin:auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px'>
      <div style='padding:20px 24px;border-bottom:1px solid #e5e7eb'>
        <h2 style='margin:0;color:#0ea5e9'>Welcome to Bali Diving</h2>
        <div style='font-size:13px;color:#64748b'>Your account is ready</div>
      </div>
      <div style='padding:24px'>
        <p style='margin-top:0'>Hi {$safe($name)},</p>
        <p>Thank you for registering with <b>BaliDiving.com</b>. Here are your login details:</p>
        <table cellspacing='0' cellpadding='8' style='width:100%;border-collapse:collapse;margin:10px 0'>
          <tr><td style='width:160px;background:#f8fafc;border:1px solid #e2e8f0'><b>Link</b></td><td style='border:1px solid #e2e8f0'><a href='{$loginUrl}' target='_blank'>{$loginUrl}</a></td></tr>
          <tr><td style='background:#f8fafc;border:1px solid #e2e8f0'><b>Email</b></td><td style='border:1px solid #e2e8f0'>{$safe($loginEmail)}</td></tr>
          <tr><td style='background:#f8fafc;border:1px solid #e2e8f0'><b>Password</b></td><td style='border:1px solid #e2e8f0'>{$safe($password)}</td></tr>
        </table>
        <p style='margin:12px 0 0'>For security, please change your password after logging in.</p>
        <p style='margin:4px 0 0'>If you didn’t request this, please ignore this email.</p>
        <p style='margin:18px 0 0'>Warm regards,<br><b>Bali Diving Team</b></p>
      </div>
    </div>
  </body></html>";

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $headers .= "From: Bali Diving <noreply@balidiving.com>\r\n";
  @mail($toEmail, $subject, $html, $headers);
}

/* ===== API: POST ?action=register ===== */
$action = $_GET['action'] ?? '';
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $in = read_json_body_or_form();

  $full_name     = trim($in['full_name'] ?? '');
  $email         = trim($in['email'] ?? '');
  $country       = trim($in['country'] ?? '');
  $phone_code    = trim($in['phone_code'] ?? '');
  $phone_num     = trim($in['phone_number'] ?? '');
  $ref_email     = trim($in['ref_email'] ?? ''); // optional
  $password_input= trim($in['password'] ?? '');  // NEW: optional password

  // Server-side validation (allow 2+ letter TLDs via FILTER_VALIDATE_EMAIL)
  if ($full_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $country === '' || $phone_code === '' || $phone_num === '') {
    json_out(['ok'=>false,'error'=>'Please complete all fields with valid values.']);
  }

  // Referral policy:
  // - If empty/invalid -> use default sales@balidiving.com
  // - If valid but NOT found in leads -> fallback to sales@balidiving.com
  $defaultRef = 'sales@balidiving.com';
  if ($ref_email === '' || !filter_var($ref_email, FILTER_VALIDATE_EMAIL)) {
    $ref_email_final = $defaultRef;
  } else {
    $chk = $pdo->prepare("SELECT 1 FROM leads WHERE email = :em LIMIT 1");
    $chk->execute([':em'=>$ref_email]);
    $ref_email_final = $chk->fetchColumn() ? $ref_email : $defaultRef;
  }

  // Build phone/password format
  $phoneDigits     = ltrim($phone_code, '+') . normalize_phone($phone_num);
  $password_login  = $password_input !== '' ? $password_input : $phoneDigits; // pakai input jika ada, else nomor HP
  $phone_display   = '+' . $phoneDigits;

  try {
    // Upsert-ish: if exists, don't duplicate
    $st = $pdo->prepare("SELECT id FROM leads WHERE email = :em LIMIT 1");
    $st->execute([':em'=>$email]);
    $exists = (bool)$st->fetchColumn();

    if (!$exists) {
      $id    = gen_id();
      $ts    = now();
      $sql = "INSERT INTO leads
                (id, `column`, name, email, phone, country, source, brand, created_at, updated_at)
              VALUES
                (:id, :col, :name, :email, :phone, :country, :source, :brand, :c, :u)";
      $pdo->prepare($sql)->execute([
        ':id'=>$id,
        ':col'=>'leads',
        ':name'=>$full_name,
        ':email'=>$email,
        ':phone'=>$phone_display,
        ':country'=>$country,
        ':source'=>'Registration',
        ':brand'=>'BALI DIVING',
        ':c'=>$ts,
        ':u'=>$ts
      ]);

      // Notifikasi ke sales (lead baru)
      send_lead_email([
        'name'=>$full_name,
        'email'=>$email,
        'country'=>$country,
        'phone'=>$phone_display,
        'created_at'=>$ts,
        'source'=>'Registration',
        'ip'=>client_ip()
      ]);
    }

    // Simpan mapping referral yang dipakai
    try {
      $pdo->prepare("INSERT INTO lead_referrers (lead_email, ref_email, created_at) VALUES (:lead, :ref, :t)")
          ->execute([':lead'=>mb_strtolower($email), ':ref'=>mb_strtolower($ref_email_final), ':t'=>now()]);
    } catch (Throwable $e) { error_log('[register.php] insert lead_referrers failed: '.$e->getMessage()); }

    // Kirim kredensial login ke user
    send_login_email($email, $full_name, $email, $password_login);

    json_out(['ok'=>true,'created'=>!$exists, 'ref_email_final'=>$ref_email_final, 'ref_fallback'=>($ref_email_final!==$ref_email)]);
  } catch(Throwable $e){
    error_log("[register.php] INSERT/SELECT failed: ".$e->getMessage());
    json_out(['ok'=>false,'error'=>'Server error']);
  }
}

/* ===== RENDER PAGE (UI) ===== */
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bali Diving – Quick Registration</title>
  <link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { box-sizing: border-box; }
    .ocean-gradient { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%); }
    .card-shadow { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .input-focus:focus { box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12); }
    .button-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(14, 165, 233, 0.3); }
    .fade-in { animation: fadeIn .5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(16px);} to { opacity:1; transform: translateY(0);} }
    .loading-spinner { border: 3px solid #f3f4f6; border-top: 3px solid #0ea5e9; border-radius: 50%; width: 18px; height: 18px; animation: spin 1s linear infinite; margin-right:8px; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body class="h-full ocean-gradient">
  <main class="min-h-full flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl card-shadow max-w-md w-full p-8 fade-in">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-user-plus text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Quick Registration</h1>
        <p class="text-slate-600 mt-1">One-time sign up. Continue to your account login.</p>
      </div>

      <div id="regErrorTop" class="hidden mb-4 rounded-lg px-3 py-2 text-sm bg-rose-50 text-rose-700 border border-rose-200"></div>

      <form id="registerForm" class="space-y-4" novalidate>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Full name</label>
          <input type="text" name="full_name" class="w-full border border-slate-300 rounded-lg px-3 py-2 input-focus" placeholder="Type your full name" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
          <input type="email" name="email" id="regEmail" class="w-full border border-slate-300 rounded-lg px-3 py-2 input-focus" placeholder="Type your email" required>
          <p class="text-xs text-slate-500 mt-1"></p>
        </div>

        <!-- NEW: Password (optional) -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Password (optional)</label>
          <input type="password" name="password" id="regPassword"
                 class="w-full border border-slate-300 rounded-lg px-3 py-2 input-focus"
                 placeholder="Min. 6 characters">
          <p class="text-xs text-slate-500 mt-1">Kosongkan untuk auto-generate dari nomor HP (+62…).</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Referral (optional)</label>
          <input type="email" name="ref_email" class="w-full border border-slate-300 rounded-lg px-3 py-2 input-focus" placeholder="sales@balidiving.com">
          <p class="text-xs text-slate-500 mt-1">If empty or not found in our customer list, we’ll use <b>sales@balidiving.com</b>.</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Country</label>
          <select id="countrySelect" name="country" class="w-full border border-slate-300 rounded-lg px-3 py-2 input-focus" required></select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Phone / WhatsApp</label>
          <div class="flex gap-2">
            <select id="phoneCodeSelect" name="phone_code" class="min-w-[160px] border border-slate-300 rounded-lg px-3 py-2 input-focus" required></select>
            <input type="tel" name="phone_number" class="flex-1 border border-slate-300 rounded-lg px-3 py-2 input-focus" placeholder="81234567890" required>
          </div>
          <p class="text-xs text-slate-500 mt-1">Choose +62 then fill 8123… (without leading 0).</p>
        </div>

        <button id="regSubmit" type="submit" class="w-full bg-sky-600 text-white rounded-lg px-4 py-2.5 hover:bg-sky-500 button-hover flex items-center justify-center">
          <span class="inline-flex items-center" id="btnLabel"><span>Continue</span></span>
        </button>
      </form>

      <div class="mt-6 pt-6 border-t border-slate-100">
        <div class="flex items-center justify-center gap-6 text-sm text-slate-500">
          <div class="flex items-center"><i class="fa-solid fa-shield-halved text-emerald-600 mr-2"></i>Secure & Trusted</div>
          <div class="flex items-center"><i class="fa-solid fa-envelope-circle-check text-blue-500 mr-2"></i>Email Protected</div>
        </div>
      </div>
    </div>
  </main>

  <script>
    const LOGIN_URL = 'https://balidiving.com/login';

    // ===== Country & Phone Code (All Countries) =====
    const POPULAR_ISO = ['AU','SG','MY','US','GB','DE','FR','NL','IN','CN','JP','KR','ID'];

    // Comprehensive list (ringkas tapi luas cakupan)
    const COUNTRIES = [
      {iso:'ID', name:'Indonesia', dial:'+62'},
      {iso:'AU', name:'Australia', dial:'+61'},
      {iso:'SG', name:'Singapore', dial:'+65'},
      {iso:'MY', name:'Malaysia', dial:'+60'},
      {iso:'TH', name:'Thailand', dial:'+66'},
      {iso:'PH', name:'Philippines', dial:'+63'},
      {iso:'VN', name:'Vietnam', dial:'+84'},
      {iso:'BN', name:'Brunei', dial:'+673'},
      {iso:'KH', name:'Cambodia', dial:'+855'},
      {iso:'LA', name:'Laos', dial:'+856'},
      {iso:'MM', name:'Myanmar', dial:'+95'},
      {iso:'TL', name:'Timor-Leste', dial:'+670'},
      {iso:'US', name:'United States', dial:'+1'},
      {iso:'CA', name:'Canada', dial:'+1'},
      {iso:'MX', name:'Mexico', dial:'+52'},
      {iso:'AR', name:'Argentina', dial:'+54'},
      {iso:'BR', name:'Brazil', dial:'+55'},
      {iso:'CL', name:'Chile', dial:'+56'},
      {iso:'CO', name:'Colombia', dial:'+57'},
      {iso:'PE', name:'Peru', dial:'+51'},
      {iso:'UY', name:'Uruguay', dial:'+598'},
      {iso:'PY', name:'Paraguay', dial:'+595'},
      {iso:'BO', name:'Bolivia', dial:'+591'},
      {iso:'EC', name:'Ecuador', dial:'+593'},
      {iso:'VE', name:'Venezuela', dial:'+58'},
      {iso:'GB', name:'United Kingdom', dial:'+44'},
      {iso:'IE', name:'Ireland', dial:'+353'},
      {iso:'FR', name:'France', dial:'+33'},
      {iso:'DE', name:'Germany', dial:'+49'},
      {iso:'NL', name:'Netherlands', dial:'+31'},
      {iso:'BE', name:'Belgium', dial:'+32'},
      {iso:'ES', name:'Spain', dial:'+34'},
      {iso:'PT', name:'Portugal', dial:'+351'},
      {iso:'IT', name:'Italy', dial:'+39'},
      {iso:'CH', name:'Switzerland', dial:'+41'},
      {iso:'AT', name:'Austria', dial:'+43'},
      {iso:'SE', name:'Sweden', dial:'+46'},
      {iso:'NO', name:'Norway', dial:'+47'},
      {iso:'DK', name:'Denmark', dial:'+45'},
      {iso:'FI', name:'Finland', dial:'+358'},
      {iso:'IS', name:'Iceland', dial:'+354'},
      {iso:'PL', name:'Poland', dial:'+48'},
      {iso:'CZ', name:'Czechia', dial:'+420'},
      {iso:'SK', name:'Slovakia', dial:'+421'},
      {iso:'HU', name:'Hungary', dial:'+36'},
      {iso:'RO', name:'Romania', dial:'+40'},
      {iso:'BG', name:'Bulgaria', dial:'+359'},
      {iso:'GR', name:'Greece', dial:'+30'},
      {iso:'TR', name:'Türkiye', dial:'+90'},
      {iso:'RU', name:'Russia', dial:'+7'},
      {iso:'UA', name:'Ukraine', dial:'+380'},
      {iso:'BY', name:'Belarus', dial:'+375'},
      {iso:'EE', name:'Estonia', dial:'+372'},
      {iso:'LV', name:'Latvia', dial:'+371'},
      {iso:'LT', name:'Lithuania', dial:'+370'},
      {iso:'SI', name:'Slovenia', dial:'+386'},
      {iso:'HR', name:'Croatia', dial:'+385'},
      {iso:'BA', name:'Bosnia & Herzegovina', dial:'+387'},
      {iso:'RS', name:'Serbia', dial:'+381'},
      {iso:'ME', name:'Montenegro', dial:'+382'},
      {iso:'MK', name:'North Macedonia', dial:'+389'},
      {iso:'AL', name:'Albania', dial:'+355'},
      {iso:'MD', name:'Moldova', dial:'+373'},
      {iso:'AM', name:'Armenia', dial:'+374'},
      {iso:'GE', name:'Georgia', dial:'+995'},
      {iso:'AZ', name:'Azerbaijan', dial:'+994'},
      {iso:'CY', name:'Cyprus', dial:'+357'},
      {iso:'MT', name:'Malta', dial:'+356'},
      {iso:'LU', name:'Luxembourg', dial:'+352'},
      {iso:'LI', name:'Liechtenstein', dial:'+423'},
      {iso:'AD', name:'Andorra', dial:'+376'},
      {iso:'MC', name:'Monaco', dial:'+377'},
      {iso:'SM', name:'San Marino', dial:'+378'},
      {iso:'JP', name:'Japan', dial:'+81'},
      {iso:'KR', name:'South Korea', dial:'+82'},
      {iso:'CN', name:'China', dial:'+86'},
      {iso:'HK', name:'Hong Kong', dial:'+852'},
      {iso:'MO', name:'Macao', dial:'+853'},
      {iso:'TW', name:'Taiwan', dial:'+886'},
      {iso:'IN', name:'India', dial:'+91'},
      {iso:'PK', name:'Pakistan', dial:'+92'},
      {iso:'BD', name:'Bangladesh', dial:'+880'},
      {iso:'LK', name:'Sri Lanka', dial:'+94'},
      {iso:'NP', name:'Nepal', dial:'+977'},
      {iso:'BT', name:'Bhutan', dial:'+975'},
      {iso:'MV', name:'Maldives', dial:'+960'},
      {iso:'AE', name:'United Arab Emirates', dial:'+971'},
      {iso:'SA', name:'Saudi Arabia', dial:'+966'},
      {iso:'QA', name:'Qatar', dial:'+974'},
      {iso:'KW', name:'Kuwait', dial:'+965'},
      {iso:'BH', name:'Bahrain', dial:'+973'},
      {iso:'OM', name:'Oman', dial:'+968'},
      {iso:'IR', name:'Iran', dial:'+98'},
      {iso:'IQ', name:'Iraq', dial:'+964'},
      {iso:'JO', name:'Jordan', dial:'+962'},
      {iso:'LB', name:'Lebanon', dial:'+961'},
      {iso:'IL', name:'Israel', dial:'+972'},
      {iso:'PS', name:'Palestine', dial:'+970'},
      {iso:'EG', name:'Egypt', dial:'+20'},
      {iso:'MA', name:'Morocco', dial:'+212'},
      {iso:'DZ', name:'Algeria', dial:'+213'},
      {iso:'TN', name:'Tunisia', dial:'+216'},
      {iso:'LY', name:'Libya', dial:'+218'},
      {iso:'SD', name:'Sudan', dial:'+249'},
      {iso:'ET', name:'Ethiopia', dial:'+251'},
      {iso:'KE', name:'Kenya', dial:'+254'},
      {iso:'UG', name:'Uganda', dial:'+256'},
      {iso:'TZ', name:'Tanzania', dial:'+255'},
      {iso:'RW', name:'Rwanda', dial:'+250'},
      {iso:'BI', name:'Burundi', dial:'+257'},
      {iso:'CD', name:'Congo (DRC)', dial:'+243'},
      {iso:'CG', name:'Congo (Republic)', dial:'+242'},
      {iso:'CM', name:'Cameroon', dial:'+237'},
      {iso:'GH', name:'Ghana', dial:'+233'},
      {iso:'NG', name:'Nigeria', dial:'+234'},
      {iso:'CI', name:'Côte d’Ivoire', dial:'+225'},
      {iso:'SN', name:'Senegal', dial:'+221'},
      {iso:'ML', name:'Mali', dial:'+223'},
      {iso:'BF', name:'Burkina Faso', dial:'+226'},
      {iso:'NE', name:'Niger', dial:'+227'},
      {iso:'BJ', name:'Benin', dial:'+229'},
      {iso:'TG', name:'Togo', dial:'+228'},
      {iso:'GM', name:'Gambia', dial:'+220'},
      {iso:'SL', name:'Sierra Leone', dial:'+232'},
      {iso:'LR', name:'Liberia', dial:'+231'},
      {iso:'ZA', name:'South Africa', dial:'+27'},
      {iso:'NA', name:'Namibia', dial:'+264'},
      {iso:'BW', name:'Botswana', dial:'+267'},
      {iso:'ZW', name:'Zimbabwe', dial:'+263'},
      {iso:'MZ', name:'Mozambique', dial:'+258'},
      {iso:'SZ', name:'Eswatini', dial:'+268'},
      {iso:'LS', name:'Lesotho', dial:'+266'},
      {iso:'MG', name:'Madagascar', dial:'+261'},
      {iso:'MU', name:'Mauritius', dial:'+230'},
      {iso:'SC', name:'Seychelles', dial:'+248'},
      {iso:'NZ', name:'New Zealand', dial:'+64'},
      {iso:'PG', name:'Papua New Guinea', dial:'+675'},
      {iso:'FJ', name:'Fiji', dial:'+679'},
      {iso:'WS', name:'Samoa', dial:'+685'},
      {iso:'TO', name:'Tonga', dial:'+676'},
      {iso:'SB', name:'Solomon Islands', dial:'+677'},
      {iso:'VU', name:'Vanuatu', dial:'+678'}
    ];

    const byISO = new Map(COUNTRIES.map(c => [c.iso, c]));
    const byDial = new Map();
    COUNTRIES.forEach(c => { if(!byDial.has(c.dial)) byDial.set(c.dial, []); byDial.get(c.dial).push(c); });

    function renderCountryOptions(selectEl){
      const popular = COUNTRIES.filter(c => POPULAR_ISO.includes(c.iso));
      const others = COUNTRIES.filter(c => !POPULAR_ISO.includes(c.iso)).sort((a,b)=> a.name.localeCompare(b.name));
      const frag = document.createDocumentFragment();

      const optPopular = document.createElement('optgroup'); optPopular.label = 'Recent Guests';
      popular.forEach(c=>{ const o=document.createElement('option'); o.value=c.name; o.textContent=c.name; o.dataset.iso=c.iso; o.dataset.dial=c.dial; optPopular.appendChild(o); });
      frag.appendChild(optPopular);

      const optAll = document.createElement('optgroup'); optAll.label = 'All Countries';
      others.forEach(c=>{ const o=document.createElement('option'); o.value=c.name; o.textContent=c.name; o.dataset.iso=c.iso; o.dataset.dial=c.dial; optAll.appendChild(o); });
      frag.appendChild(optAll);

      selectEl.innerHTML = ''; selectEl.appendChild(frag);
    }

    function renderDialOptions(selectEl){
      const uniq = Array.from(new Set(COUNTRIES.map(c=>c.dial))).sort((a,b) => (a.replace('+','')*1) - (b.replace('+','')*1));
      const frag = document.createDocumentFragment();

      const popularDials = [];
      POPULAR_ISO.forEach(iso=>{ const c=byISO.get(iso); if(c && !popularDials.includes(c.dial)) popularDials.push(c.dial); });

      const optPopular = document.createElement('optgroup'); optPopular.label='Popular Codes';
      popularDials.forEach(d=>{ const list=byDial.get(d)||[]; const label = `${d} (${list.map(x=>x.name).join(' / ')})`; const o=document.createElement('option'); o.value=d; o.textContent=label; o.dataset.isoList = list.map(x=>x.iso).join(','); optPopular.appendChild(o); });
      frag.appendChild(optPopular);

      const optAll = document.createElement('optgroup'); optAll.label='All Codes';
      uniq.forEach(d=>{ if(popularDials.includes(d)) return; const list=byDial.get(d)||[]; const label=`${d} (${list.map(x=>x.name).join(' / ')})`; const o=document.createElement('option'); o.value=d; o.textContent=label; o.dataset.isoList=list.map(x=>x.iso).join(','); optAll.appendChild(o); });
      frag.appendChild(optAll);

      selectEl.innerHTML = ''; selectEl.appendChild(frag);
    }

    const countrySelect = document.getElementById('countrySelect');
    const phoneCodeSelect = document.getElementById('phoneCodeSelect');
    renderCountryOptions(countrySelect);
    renderDialOptions(phoneCodeSelect);

    function setDefaultsToIndonesia(){
      const defISO = 'ID';
      const c = byISO.get(defISO);
      if(!c) return;
      const opt = Array.from(countrySelect.options).find(o => o.dataset && o.dataset.iso === defISO);
      if(opt){ countrySelect.value = opt.value; }
      phoneCodeSelect.value = c.dial;
    }
    setDefaultsToIndonesia();

    countrySelect.addEventListener('change', () => {
      const selectedOpt = countrySelect.options[countrySelect.selectedIndex];
      const dial = selectedOpt?.dataset?.dial;
      if(dial){ phoneCodeSelect.value = dial; }
    });

    phoneCodeSelect.addEventListener('change', () => {
      const dial = phoneCodeSelect.value;
      const list = byDial.get(dial) || [];
      if(list.length === 1){
        const iso = list[0].iso;
        const opt = Array.from(countrySelect.options).find(o => o.dataset && o.dataset.iso === iso);
        if(opt){ countrySelect.value = opt.value; }
      }
    });

    // ===== Submit =====
    const regForm = document.getElementById('registerForm');
    const regErrorTop = document.getElementById('regErrorTop');
    const regSubmit = document.getElementById('regSubmit');
    const btnLabel = document.getElementById('btnLabel');

    function setLoading(v){
      if(v){
        regSubmit.disabled = true; regSubmit.classList.add('opacity-75');
        btnLabel.innerHTML = '<span class="loading-spinner"></span><span>Processing…</span>';
      } else {
        regSubmit.disabled = false; regSubmit.classList.remove('opacity-75');
        btnLabel.innerHTML = '<span>Continue</span>';
      }
    }

    regForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      regErrorTop.classList.add('hidden'); regErrorTop.textContent = '';

      // Client-side email validation (allow 2+ letter TLDs)
      const fd = new FormData(regForm);
      const payload = Object.fromEntries(fd.entries());
      const emailRegex = /^[^\s@]+@[^\s@]+\.[A-Za-z]{2,63}$/;
      if(!emailRegex.test((payload.email||'').trim())){
        regErrorTop.textContent = 'Please enter a valid email address';
        regErrorTop.classList.remove('hidden');
        return;
      }
      // Optional password min length check (only if provided)
      if ((payload.password || '').length > 0 && (payload.password || '').length < 6) {
        regErrorTop.textContent = 'Password must be at least 6 characters or leave it blank.';
        regErrorTop.classList.remove('hidden');
        return;
      }
      // Client-side default referral if empty (server juga enforce)
      if(!payload.ref_email || !payload.ref_email.trim()){
        payload.ref_email = 'sales@balidiving.com';
      }

      setLoading(true);
      try {
        const res = await fetch(location.pathname + '?action=register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!data.ok) throw new Error(data.error || 'Register failed');

        // Open login page per request
        window.open(LOGIN_URL, '_blank', 'noopener,noreferrer');

        const ok = document.createElement('div');
        ok.className = 'mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm text-center';
        ok.textContent = (data.created ? 'Welcome! ' : 'Welcome back! ') + `Check your email for login details. Referral: ${data.ref_email_final}${data.ref_fallback ? ' (auto)' : ''}.`;
        regForm.appendChild(ok);
        setTimeout(()=>ok.remove(), 6000);
      } catch (err) {
        regErrorTop.textContent = err.message || 'Unexpected error';
        regErrorTop.classList.remove('hidden');
      } finally {
        setLoading(false);
      }
    });
  </script>
</body>
</html>
