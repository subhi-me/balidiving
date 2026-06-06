<?php
session_start();

/* === Redirect kalau belum login === */
if (empty($_SESSION['member_lead_id']) && empty($_SESSION['member_email'])) {
  header('Location: /member-login.php'); // Ubah jika path login berbeda
  exit;
}

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

/* Helpers */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function norm_phone($p){
  $d = preg_replace('/\D+/', '', (string)$p);
  if($d==='') return '';
  if($d[0]==='0') $d = '62'.substr($d,1);
  return $d;
}
function fmt_phone($p){
  $d = norm_phone($p);
  if($d==='') return '';
  // +62 812-3456-7890 ringan
  if(str_starts_with($d,'62')) $d = '+'.$d;
  return $d;
}

/* ===== Ambil profil dari LEADS ===== */
$pdo = pdo_conn();
$leadId = $_SESSION['member_lead_id'] ?? null;
$email  = $_SESSION['member_email']   ?? null;

$lead = null;
if ($leadId) {
  $st = $pdo->prepare("SELECT * FROM leads WHERE id = :id LIMIT 1");
  $st->execute([':id'=>$leadId]);
  $lead = $st->fetch();
}
if (!$lead && $email) {
  $st = $pdo->prepare("SELECT * FROM leads WHERE email = :e ORDER BY updated_at DESC LIMIT 1");
  $st->execute([':e'=>$email]);
  $lead = $st->fetch();
}

/* Fallback jika tetap tidak ada (data db berubah) */
if (!$lead) {
  // paksa logout agar tidak empty
  $_SESSION = [];
  header('Location: /member-login.php?relogin=1');
  exit;
}

/* ===== Trip count dari activity_history ===== */
$tripCount = 0;
try{
  $st = $pdo->prepare("SELECT COUNT(*) FROM activity_history WHERE lead_id = :id");
  $st->execute([':id'=>$lead['id']]);
  $tripCount = (int)$st->fetchColumn();
}catch(Throwable $e){ $tripCount = 0; }

/* ===== Siapkan variabel display ===== */
$member_name   = $lead['name'] ?? ($_SESSION['member_name'] ?? 'Member');
$member_email  = $lead['email'] ?? ($_SESSION['member_email'] ?? '');
$member_phone  = fmt_phone($lead['phone'] ?? '');
$trip_history  = $tripCount." Dives Completed";

$ig     = $lead['social_ig']     ?? '';
$fb     = $lead['social_fb']     ?? '';
$tiktok = $lead['social_tiktok'] ?? '';

$points_total    = (int)($lead['points_total'] ?? 0);
$points_redeemed = (int)($lead['points_redeemed'] ?? 0);
$points_display  = number_format($points_total, 0, '.', ',')." Points";
$redeemed_display= number_format($points_redeemed, 0, '.', ',')." Points";

$promo_code = $lead['promo_code'] ?: 'WELCOME';
$host = $_SERVER['HTTP_HOST'] ?? 'balidiving.com';
$ref_code = substr(preg_replace('/[^A-Za-z0-9]/','',$lead['id']), -8);
$referral_url = $host.'/ref/'.$ref_code;

$payment_status = strtolower($lead['payment_status'] ?? 'unpaid');
$status_label = in_array($payment_status, ['paid','deposit']) ? 'Active Member' : 'Member';

/* Untuk inject ke defaultConfig JS */
$cfg = [
  'member_name'      => $member_name,
  'member_email'     => $member_email,
  'member_phone'     => $member_phone,
  'trip_history'     => $trip_history,
  'instagram_handle' => $ig ?: '-',
  'facebook_handle'  => $fb ?: '-',
  'tiktok_handle'    => $tiktok ?: '-',
  'point_total'      => $points_display,
  'points_redeemed'  => $redeemed_display,
  'promo_code'       => $promo_code,
  'referral_url'     => $referral_url,
  'page_title'       => 'Bali Diving Member',
  'subtitle_text'    => 'Member Profile',
  'background_color' => '#f8fafc',
  'card_color'       => '#ffffff',
  'primary_color'    => '#1e40af',
  'text_color'       => '#1e293b',
  'accent_color'     => '#3b82f6',
  'status_label'     => $status_label
];
?>
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bali Diving Member - Profile</title>
  <script src="/_sdk/element_sdk.js"></script>
  <style>
        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
        }
        html { height: 100%; }
        .container { position: relative; z-index: 10; display:flex; justify-content:center; align-items:center; min-height:100%; padding:20px; }
        .profile-card { background:#ffffff; border-radius:16px; padding:32px; box-shadow:0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04); width:100%; max-width:480px; text-align:left; border:1px solid #e2e8f0; animation: slideUp .8s ease-out; }
        @keyframes slideUp { from{ transform:translateY(30px); opacity:0;} to{ transform:translateY(0); opacity:1;} }
        .logo-section{ margin-bottom:32px; text-align:center; position:relative; }
        .logo-icon{ width:80px; height:80px; margin:0 auto 16px; background:linear-gradient(135deg, #1e40af,#3b82f6); border-radius:20px; display:flex; align-items:center; justify-content:center; box-shadow:0 8px 24px rgba(30,64,175,0.2); }
        .title{ font-size:28px; font-weight:700; color:#1e293b; margin:0; letter-spacing:-0.025em; }
        .subtitle{ font-size:16px; color:#64748b; margin:8px 0 0 0; font-weight:500; }
        .profile-section{ margin-bottom:32px; background:#f8fafc; border-radius:12px; padding:20px; border:1px solid #e2e8f0; }
        .profile-section:last-child{ margin-bottom:0; }
        .section-title{ font-size:18px; font-weight:600; color:#1e40af; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .section-title::before{ content:''; width:4px; height:20px; background:linear-gradient(135deg, #1e40af, #3b82f6); border-radius:2px; }
        .profile-field{ display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #e2e8f0; }
        .profile-field:last-child{ border-bottom:none; }
        .field-label{ font-weight:500; color:#475569; font-size:14px; }
        .field-value{ color:#1e293b; font-size:14px; font-weight:600; }
        .social-link{ color:#1e40af; text-decoration:none; font-weight:600; transition:.3s; padding:4px 8px; border-radius:6px; background:rgba(30,64,175,0.05); }
        .social-link:hover{ color:#fff; background:#1e40af; text-decoration:none; transform:translateY(-1px); }
        .points-highlight{ background:linear-gradient(135deg, #059669,#10b981); color:#fff; padding:6px 12px; border-radius:8px; font-weight:700; font-size:16px; box-shadow:0 2px 8px rgba(5,150,105,0.2); }
        .promo-code{ background:linear-gradient(135deg, #7c3aed,#a855f7); color:#fff; padding:8px 12px; border-radius:8px; font-weight:600; font-family:'Courier New', monospace; letter-spacing:1px; cursor:pointer; transition:.3s; }
        .promo-code:hover{ transform:scale(1.05); box-shadow:0 4px 12px rgba(124,58,237,0.3); }
        .referral-url{ background:#f1f5f9; padding:8px 12px; border-radius:8px; font-family:'Courier New', monospace; font-size:12px; color:#475569; border:1px solid #cbd5e1; cursor:pointer; transition:.3s; }
        .referral-url:hover{ background:#e2e8f0; border-color:#94a3b8; }
        .status-badge{ display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg, #059669,#10b981); color:white; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
        .status-badge::before{ content:'●'; font-size:8px; }
        @media (max-width:480px){
          .profile-card{ padding:24px 20px; margin:10px; }
          .title{ font-size:24px; }
          .logo-icon{ width:70px; height:70px; }
          .profile-field{ flex-direction:column; align-items:flex-start; gap:4px; }
        }
    </style>
  <style>@view-transition { navigation: auto; }</style>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>
 </head>
 <body>
  <main class="container">
   <div class="profile-card">
    <div class="logo-section">
     <div class="logo-icon">
      <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
        <path d="M20 5C15 5 10 8 8 15C6 22 10 30 20 35C30 30 34 22 32 15C30 8 25 5 20 5Z" fill="white" opacity="0.9" />
        <circle cx="15" cy="18" r="2" fill="white" />
        <circle cx="25" cy="18" r="2" fill="white" />
        <path d="M12 25C14 27 18 28 20 28C22 28 26 27 28 25" stroke="white" stroke-width="2" stroke-linecap="round" />
        <path d="M8 12C10 10 15 8 20 8C25 8 30 10 32 12" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.7" />
      </svg>
     </div>
     <h1 class="title">Bali Diving Member</h1>
     <p class="subtitle">Member Profile</p>
     <div class="status-badge">
      <?= h($cfg['status_label']) ?>
     </div>
    </div>

    <!-- Personal Information Section -->
    <div class="profile-section">
     <h2 class="section-title">Personal Information</h2>
     <div class="profile-field"><span class="field-label">Name:</span>  <span class="field-value"><?= h($member_name) ?></span></div>
     <div class="profile-field"><span class="field-label">Email:</span> <span class="field-value"><?= h($member_email) ?></span></div>
     <div class="profile-field"><span class="field-label">Phone:</span> <span class="field-value"><?= h($member_phone ?: '-') ?></span></div>
     <div class="profile-field"><span class="field-label">Trip History:</span> <span class="field-value"><?= h($trip_history) ?></span></div>
    </div>

    <!-- Social Media Section -->
    <div class="profile-section">
     <h2 class="section-title">Social Media</h2>
     <div class="profile-field"><span class="field-label">Instagram:</span>   <a href="#instagram" class="social-link field-value"><?= h($ig ?: '-') ?></a></div>
     <div class="profile-field"><span class="field-label">Facebook/VK:</span> <a href="#facebook"  class="social-link field-value"><?= h($fb ?: '-') ?></a></div>
     <div class="profile-field"><span class="field-label">TikTok:</span>      <a href="#tiktok"   class="social-link field-value"><?= h($tiktok ?: '-') ?></a></div>
    </div>

    <!-- Rewards Section -->
    <div class="profile-section">
     <h2 class="section-title">Rewards &amp; Benefits</h2>
     <div class="profile-field"><span class="field-label">Point Total:</span> <span class="points-highlight"><?= h($points_display) ?></span></div>
     <div class="profile-field"><span class="field-label">Redeemed:</span>    <span class="field-value"><?= h($redeemed_display) ?></span></div>
     <div class="profile-field"><span class="field-label">Promo Code:</span>  <span class="promo-code" onclick="copyPromoCode()"><?= h($promo_code) ?></span></div>
     <div class="profile-field"><span class="field-label">Referral URL:</span>
      <div class="referral-url" onclick="copyReferralUrl()">
       <?= h($referral_url) ?>
      </div>
     </div>
    </div>
   </div>
  </main>

  <script>
    // Inject defaultConfig dari PHP agar tetap kompatibel dengan elementSdk
    const defaultConfig = <?= json_encode($cfg, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?>;

    async function render(config) {
      // Tetap jalankan renderer agar kompatibel, namun nilai sudah diisi via PHP
      const backgroundColor = config.background_color || defaultConfig.background_color;
      const primaryColor    = config.primary_color    || defaultConfig.primary_color;
      const textColor       = config.text_color       || defaultConfig.text_color;
      const accentColor     = config.accent_color     || defaultConfig.accent_color;

      document.body.style.background = `linear-gradient(135deg, ${backgroundColor} 0%, #e2e8f0 100%)`;
      document.querySelector('.logo-icon').style.background = `linear-gradient(135deg, ${primaryColor}, ${accentColor})`;

      document.querySelectorAll('.section-title').forEach(title => {
        title.style.color = primaryColor;
      });
      document.querySelectorAll('.social-link').forEach(link => {
        link.style.color = primaryColor;
        link.style.background = `rgba(30, 64, 175, 0.05)`;
      });
    }

    function mapToCapabilities(config) {
      return {
        recolorables: [
          { get: () => config.background_color || defaultConfig.background_color, set: v => { config.background_color=v; window.elementSdk?.setConfig({ background_color:v }); } },
          { get: () => config.primary_color || defaultConfig.primary_color,       set: v => { config.primary_color=v;    window.elementSdk?.setConfig({ primary_color:v }); } },
          { get: () => config.text_color || defaultConfig.text_color,             set: v => { config.text_color=v;      window.elementSdk?.setConfig({ text_color:v }); } },
          { get: () => config.accent_color || defaultConfig.accent_color,         set: v => { config.accent_color=v;    window.elementSdk?.setConfig({ accent_color:v }); } },
        ],
        borderables: [],
        fontEditable: undefined,
        fontSizeable: undefined
      };
    }

    function mapToEditPanelValues(config) {
      return new Map([
        ["page_title", config.page_title || defaultConfig.page_title],
        ["subtitle_text", config.subtitle_text || defaultConfig.subtitle_text],
        ["member_name", config.member_name || defaultConfig.member_name],
        ["member_email", config.member_email || defaultConfig.member_email],
        ["member_phone", config.member_phone || defaultConfig.member_phone],
        ["trip_history", config.trip_history || defaultConfig.trip_history],
        ["instagram_handle", config.instagram_handle || defaultConfig.instagram_handle],
        ["facebook_handle", config.facebook_handle || defaultConfig.facebook_handle],
        ["tiktok_handle", config.tiktok_handle || defaultConfig.tiktok_handle],
        ["point_total", config.point_total || defaultConfig.point_total],
        ["points_redeemed", config.points_redeemed || defaultConfig.points_redeemed],
        ["promo_code", config.promo_code || defaultConfig.promo_code],
        ["referral_url", config.referral_url || defaultConfig.referral_url]
      ]);
    }

    if (window.elementSdk) {
      window.elementSdk.init({ defaultConfig, render, mapToCapabilities, mapToEditPanelValues });
    }

    function showNotification(message, color) {
      const notification = document.createElement('div');
      notification.style.cssText = `
        position: fixed; top: 20px; right: 20px; background: ${color};
        color: white; padding: 12px 20px; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; font-weight: 500; font-size: 14px;
        animation: slideIn .3s ease-out;
      `;
      notification.textContent = message;
      document.body.appendChild(notification);
      setTimeout(() => notification.remove(), 3000);
    }

    function copyPromoCode() {
      const promoCode = document.querySelector('.promo-code').textContent.trim();
      navigator.clipboard.writeText(promoCode).then(() => {
        showNotification('Promo code copied to clipboard!', '#7c3aed');
      });
    }
    function copyReferralUrl() {
      const referralUrl = document.querySelector('.referral-url').textContent.trim();
      const url = referralUrl.match(/^https?:\/\//) ? referralUrl : `https://${referralUrl}`;
      navigator.clipboard.writeText(url).then(() => {
        showNotification('Referral URL copied to clipboard!', '#1e40af');
      });
    }

    document.querySelector('a[href="#instagram"]').addEventListener('click', function(e) {
      e.preventDefault(); showNotification('Opening Instagram profile...', '#e1306c');
    });
    document.querySelector('a[href="#facebook"]').addEventListener('click', function(e) {
      e.preventDefault(); showNotification('Opening Facebook profile...', '#1877f2');
    });
    document.querySelector('a[href="#tiktok"]').addEventListener('click', function(e) {
      e.preventDefault(); showNotification('Opening TikTok profile...', '#000000');
    });

    const style = document.createElement('style');
    style.textContent = `@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}`;
    document.head.appendChild(style);
  </script>
 <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'993708b875459fbc',t:'MTc2MTI4MzMyMS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>
 </body>
</html>
