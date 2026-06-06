<?php
session_start();

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
function send_json(array $arr, int $code=200){
  if (!headers_sent()) {
    http_response_code($code);
    json_headers();
  }
  if (function_exists('ob_get_length') && ob_get_length()){ @ob_clean(); }
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}
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
  if(str_starts_with($d,'62')) $d = '+'.$d;
  return $d;
}
function wa_link($phone, $name=''){
  $d = norm_phone($phone);
  if($d==='') return '';
  $txt = urlencode("Hi ".($name ?: 'there')." — this is BALI DIVING.");
  return "https://wa.me/{$d}?text={$txt}";
}
function linkify($val, $platform=''){
  $v = trim((string)$val);
  if($v==='') return ['-', '#'];
  if(!preg_match('~^https?://~i', $v)){
    if($platform==='ig')  $v = 'https://instagram.com/'.ltrim($v,'@');
    if($platform==='fb')  $v = 'https://facebook.com/'.ltrim($v,'@');
    if($platform==='tt')  $v = 'https://tiktok.com/@'.ltrim($v,'@');
  }
  return [$val, $v];
}

/* ===== AJAX endpoint: update-only (NO INSERT) ===== */
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && (($_POST['action'] ?? '') === 'update_social')) {
  if (function_exists('ob_get_length') && ob_get_length()){ @ob_clean(); }

  $sessLeadId = $_SESSION['member_lead_id'] ?? null;
  $sessEmail  = $_SESSION['member_email']   ?? null;
  if (empty($sessLeadId) && empty($sessEmail)) {
    send_json(['ok'=>false,'error'=>'Not logged in'], 401);
  }

  // Only allow social fields (NO photo_url here)
  $allowed = ['social_ig','social_fb','social_tiktok','social_wechat'];
  $in = [];
  foreach ($allowed as $k) {
    if (array_key_exists($k, $_POST)) $in[$k] = trim((string)$_POST[$k]);
  }
  if (!$in) { send_json(['ok'=>false,'error'=>'No fields'], 400); }

  try{
    $pdo = pdo_conn();
    $pdo->beginTransaction();

    // Lock target row strictly, update-only
    $row = null;
    if ($sessLeadId) {
      $st = $pdo->prepare("SELECT id, email FROM leads WHERE id=:id LIMIT 1 FOR UPDATE");
      $st->execute([':id'=>$sessLeadId]);
      $row = $st->fetch();
    }

    if (!$row && $sessEmail) {
      // Ambil yang paling baru untuk email tsb (dan kunci)
      $st = $pdo->prepare("SELECT id, email FROM leads WHERE email=:e ORDER BY updated_at DESC, id DESC LIMIT 1 FOR UPDATE");
      $st->execute([':e'=>$sessEmail]);
      $row = $st->fetch();
    }

    if (!$row) {
      $pdo->rollBack();
      send_json(['ok'=>false,'error'=>'Lead not found (update only)'], 404);
    }

    $id    = $row['id'];
    $email = $row['email'];

    // Rakitan SET
    $sets=[]; $params=[':id'=>$id, ':u'=>date('Y-m-d H:i:s')];
    foreach($in as $k=>$v){ $sets[]="`$k`=:$k"; $params[":$k"]=$v; }

    // Tambahkan guard by email bila tersedia ⇒ kurangi risiko salah id cross-account
    if (!empty($email)) {
      $sql = "UPDATE leads SET ".implode(',', $sets).", updated_at=:u WHERE id=:id AND email=:email LIMIT 1";
      $params[':email'] = $email;
    } else {
      $sql = "UPDATE leads SET ".implode(',', $sets).", updated_at=:u WHERE id=:id LIMIT 1";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $aff = $stmt->rowCount();

    if ($aff < 1) {
      // Tidak ada row yang berubah → fail keras, tetap NO INSERT
      $pdo->rollBack();
      send_json(['ok'=>false,'error'=>'Update guard mismatch (no rows affected)'], 409);
    }

    $pdo->commit();
    send_json(['ok'=>true], 200);

  }catch(Throwable $e){
    if ($pdo && $pdo->inTransaction()) { $pdo->rollBack(); }
    // error_log('update_social error: '.$e->getMessage());
    send_json(['ok'=>false,'error'=>'DB error'], 500);
  }
  // exit via send_json
}

/* ===== Page logic (no redirects) ===== */
$NEED_LOGIN = false;
$NO_DATA    = false;
$MSG        = '';

$lead = null; $pdo = null;
$leadId = $_SESSION['member_lead_id'] ?? null;
$email  = $_SESSION['member_email']   ?? null;

if (empty($leadId) && empty($email)) {
  $NEED_LOGIN = true;
  $MSG = 'You are not logged in. Please log in to view your member profile.';
} else {
  try{
    $pdo = pdo_conn();
    if ($leadId) {
      $st = $pdo->prepare("SELECT * FROM leads WHERE id = :id LIMIT 1");
      $st->execute([':id'=>$leadId]);
      $lead = $st->fetch();
    }
    if (!$lead && $email) {
      $st = $pdo->prepare("SELECT * FROM leads WHERE email = :e ORDER BY updated_at DESC, id DESC LIMIT 1");
      $st->execute([':e'=>$email]);
      $lead = $st->fetch();
    }
    if (!$lead) {
      $NO_DATA = true;
      $MSG = 'Member data not found. Please log in again.';
      $_SESSION = []; // optional: clear stale session
    }
  }catch(Throwable $e){
    $NO_DATA = true;
    $MSG = 'An error occurred while fetching data. Please try again.';
  }
}

/* ===== View vars ===== */
$member_name   = $lead['name']  ?? ($_SESSION['member_name'] ?? 'Member');
$member_email  = $lead['email'] ?? ($_SESSION['member_email'] ?? '');
$member_phone  = fmt_phone($lead['phone'] ?? '');
$wa            = isset($lead['phone']) ? wa_link($lead['phone'], $lead['name'] ?? '') : '';

$tripCount = 0;
if ($lead && $pdo){
  try{
    $st = $pdo->prepare("SELECT COUNT(*) FROM activity_history WHERE lead_id = :id");
    $st->execute([':id'=>$lead['id']]);
    $tripCount = (int)$st->fetchColumn();
  }catch(Throwable $e){}
}
$trip_history  = $tripCount." Dives Completed";

$ig_raw = $lead['social_ig']     ?? '';
$fb_raw = $lead['social_fb']     ?? '';
$tt_raw = $lead['social_tiktok'] ?? '';
$wc_raw = $lead['social_wechat'] ?? '';
$photo  = $lead['photo_url']      ?? '';

[$ig_text, $ig_url] = linkify($ig_raw, 'ig');
[$fb_text, $fb_url] = linkify($fb_raw, 'fb');
[$tt_text, $tt_url] = linkify($tt_raw, 'tt');

$points_total     = (int)($lead['points_total'] ?? 0);
$points_redeemed  = (int)($lead['points_redeemed'] ?? 0);
$points_display   = number_format($points_total, 0, '.', ',')." Points";
$redeemed_display = number_format($points_redeemed, 0, '.', ',' )." Points";

$promo_code = ($lead['promo_code'] ?? '') ?: 'WELCOME';
$host = $_SERVER['HTTP_HOST'] ?? 'balidiving.com';
$ref_code = $lead ? substr(preg_replace('/[^A-Za-z0-9]/','',$lead['id']), -8) : 'REFERRAL';
$referral_url = "https://".$host.'/ref/'.$ref_code;

$payment_status = strtolower($lead['payment_status'] ?? 'unpaid');
$is_active = in_array($payment_status, ['paid','deposit'], true);
$status_label  = $is_active ? 'Active Member' : 'Member';
$status_colors = $is_active ? ['#059669','#10b981'] : ['#6b7280','#9ca3af'];

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bali Diving Member - Profile</title>
  <style>
    body{
      box-sizing:border-box;margin:0;padding:0;min-height:100%;
      font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
      background:linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      display:flex;align-items:center;justify-content:center;padding:20px;
    }
    .card{
      width:100%;max-width:640px;background:#fff;border:1px solid #e2e8f0;
      border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.08), 0 2px 8px rgba(0,0,0,.04);
      padding:24px 20px;animation:fadeUp .5s ease-out;
    }
    @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    .logo{width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,#1e40af,#3b82f6);
      display:flex;align-items:center;justify-content:center;margin:0 auto 10px;box-shadow:0 8px 24px rgba(30,64,175,.2)}
    .title{margin:6px 0 0;font-size:24px;font-weight:700;color:#1e293b;text-align:center}
    .subtitle{margin:4px 0 12px;font-size:14px;color:#64748b;text-align:center}
    .badge{display:inline-flex;align-items:center;gap:6px;color:#fff;padding:4px 12px;border-radius:20px;
      font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
    .center{display:flex;justify-content:center}
    .section{margin-top:18px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px}
    .section h3{margin:0 0 10px;font-size:16px;color:#1e40af;display:flex;align-items:center;gap:8px}
    .row{display:grid;grid-template-columns:160px 1fr;gap:12px;padding:8px 0;border-bottom:1px solid #e2e8f0;align-items:center}
    .row:last-child{border-bottom:none}
    .label{color:#475569;font-weight:500;font-size:14px}
    .val{color:#1e293b;font-weight:600;font-size:14px}
    .btn-row{display:flex;gap:8px;justify-content:center;margin-top:10px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;font-weight:600;
      border:1px solid #e2e8f0;background:#fff;color:#1e293b;text-decoration:none}
    .btn:hover{background:#eef2ff;border-color:#c7d2fe}
    .notice{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;padding:14px;border-radius:12px}
    .login-link{display:inline-block;margin-top:10px;background:#1e40af;color:#fff;padding:10px 14px;border-radius:10px;text-decoration:none}
    .login-link:hover{background:#1d4ed8}
    input[type="text"]{
      width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; color:#0f172a;
      background:#fff; outline:none;
    }
    input[type="text"]:focus{
      border-color:#94a3b8; box-shadow:0 0 0 4px rgba(59,130,246,0.10);
    }
    .actions{display:flex;gap:10px;justify-content:flex-end;margin-top:10px}
    .btn-primary{background:#1e40af;color:#fff;border:1px solid #1e40af}
    .btn-primary:hover{background:#1d4ed8;border-color:#1d4ed8}
    .hint{font-size:12px;color:#64748b;margin-top:-6px}
    .mono{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;}
    .linkpill{background:#f1f5f9;padding:6px 10px;border-radius:8px;border:1px solid #cbd5e1;display:inline-block}
    .linkpill.small{font-size:10px;line-height:1.2;padding:2px 6px;display:inline-block;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  </style>
</head>
<body>
  <div class="card">
    <div class="logo">
      <svg width="40" height="40" viewBox="0 0 40 40" fill="none" aria-hidden="true">
        <path d="M20 5C15 5 10 8 8 15C6 22 10 30 20 35C30 30 34 22 32 15C30 8 25 5 20 5Z" fill="white" opacity="0.9" />
        <circle cx="15" cy="18" r="2" fill="white" />
        <circle cx="25" cy="18" r="2" fill="white" />
        <path d="M12 25C14 27 18 28 20 28C22 28 26 27 28 25" stroke="white" stroke-width="2" stroke-linecap="round" />
        <path d="M8 12C10 10 15 8 20 8C25 8 30 10 32 12" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.7" />
      </svg>
    </div>
    <h1 class="title">Bali Diving Member</h1>
    <p class="subtitle">Member Profile</p>

    <?php if ($NEED_LOGIN || $NO_DATA): ?>
      <div class="notice">
        <strong><?= h($NEED_LOGIN ? 'Login required' : 'Data unavailable') ?></strong><br>
        <?= h($MSG) ?>
        <div class="center">
          <a class="login-link" href="https://balidiving.com/login">Login</a>
        </div>
      </div>
    <?php else: ?>
      <div class="center" style="margin-bottom:8px;">
        <div class="badge" style="background:linear-gradient(135deg, <?= h($status_colors[0]) ?>, <?= h($status_colors[1]) ?>)">● <?= h($status_label) ?></div>
      </div>

      <div class="btn-row">
        <?php if(!empty($wa)): ?>
          <a class="btn" href="<?= h('contact') ?>" target="_blank" rel="noopener">Help</a>
        <?php endif; ?>
        <?php if(!empty($photo)): ?>
          <a id="btnMediaTop" class="btn" href="<?= h($photo) ?>" target="_blank" rel="noopener">Your Photos/Videos</a>
        <?php else: ?>
          <a id="btnMediaTop" class="btn" href="#" onclick="return false;" title="No Photo/Video URL provided">Your Photos/Videos</a>
        <?php endif; ?>
        <a class="btn" href="/member-logout.php">Logout</a>
      </div>

      <!-- Personal Information -->
      <div class="section">
        <h3>Personal Information</h3>
        <div class="row"><span class="label">Name:</span>  <span class="val"><?= h($member_name) ?></span></div>
        <div class="row"><span class="label">Email:</span> <span class="val"><?= h($member_email ?: '-') ?></span></div>
        <div class="row"><span class="label">Phone:</span> <span class="val"><?= h($member_phone ?: '-') ?></span></div>
        <div class="row"><span class="label">Trip History:</span> <span class="val"><?= h($trip_history) ?></span></div>
      </div>

      <!-- Social Media (Editable) -->
      <form id="socialForm" class="section" onsubmit="return false;">
        <h3>Social Media (Editable)</h3>
        <div class="row">
          <span class="label">Instagram</span>
          <div>
            <input type="text" name="social_ig" id="f_ig" placeholder="@handle or URL" value="<?= h($ig_raw) ?>">
            <?php if($ig_text !== '-'): ?>
              <div class="hint">Current link: <a href="<?= h($ig_url) ?>" target="_blank"><?= h($ig_text) ?></a></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="row">
          <span class="label">Facebook/VK</span>
          <div>
            <input type="text" name="social_fb" id="f_fb" placeholder="Username or URL" value="<?= h($fb_raw) ?>">
            <?php if($fb_text !== '-'): ?>
              <div class="hint">Current link: <a href="<?= h($fb_url) ?>" target="_blank"><?= h($fb_text) ?></a></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="row">
          <span class="label">TikTok</span>
          <div>
            <input type="text" name="social_tiktok" id="f_tt" placeholder="@handle or URL" value="<?= h($tt_raw) ?>">
            <?php if($tt_text !== '-'): ?>
              <div class="hint">Current link: <a href="<?= h($tt_url) ?>" target="_blank"><?= h($tt_text) ?></a></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="row">
          <span class="label">WeChat</span>
          <div>
            <input type="text" name="social_wechat" id="f_wc" placeholder="WeChat ID / note" value="<?= h($wc_raw) ?>">
          </div>
        </div>

        <!-- Photo/Video URL: NON-EDITABLE -->
        <div class="row">
          <span class="label">Photo/Video</span>
          <div>
<?php if($photo): ?>
  <span id="photoUrl" class="mono linkpill"
        style="font-size:10px;line-height:1.2;padding:2px 6px;display:inline-block;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
        title="Click to copy"><?= h($photo) ?></span>
<?php else: ?>
  <span id="photoUrl" class="mono linkpill"
        style="font-size:10px;line-height:1.2;padding:2px 6px;display:inline-block;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
        title="No URL">—</span>
<?php endif; ?>
            <div class="actions" style="justify-content:flex-start;margin-top:10px">
              <button id="btnOpenMedia" class="btn" type="button">Your Photos/Videos</button>
            </div>
          </div>
        </div>

        <div class="actions">
          <button id="btnSave" class="btn btn-primary" type="button">Save Changes</button>
        </div>
      </form>

      <!-- Rewards -->
      <div class="section">
        <h3>Rewards & Benefits</h3>
        <div class="row"><span class="label">Point Total:</span> <span class="val" style="background:linear-gradient(135deg,#059669,#10b981);color:#fff;padding:4px 10px;border-radius:8px"><?= h($points_display) ?></span></div>
        <div class="row"><span class="label">Redeemed:</span>    <span class="val"><?= h($redeemed_display) ?></span></div>
        <div class="row"><span class="label">Promo Code:</span>  <span class="val"><span id="promo" style="background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;padding:6px 10px;border-radius:8px;cursor:pointer" title="Click to copy"><?= h($promo_code) ?></span></span></div>
        <div class="row"><span class="label">Referral URL:</span> <span class="val"><span id="ref" class="mono linkpill" title="Click to copy"><?= h($referral_url) ?></span></span></div>
      </div>
    <?php endif; ?>
  </div>

  <script>
    function notify(msg, color){
      const n = document.createElement('div');
      n.style.cssText = `
        position:fixed;right:20px;top:20px;background:${color||'#1e40af'};
        color:#fff;padding:10px 14px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,.15);
        font-weight:600;z-index:9999;opacity:0;transform:translateY(-6px);
        transition:opacity .2s ease, transform .2s ease;
      `;
      n.textContent = msg;
      document.body.appendChild(n);
      requestAnimationFrame(()=>{n.style.opacity='1';n.style.transform='translateY(0)';});
      setTimeout(()=>{n.style.opacity='0';n.style.transform='translateY(-6px)';setTimeout(()=>n.remove(),200);},2400);
    }

    const promo = document.getElementById('promo');
    if (promo){
      promo.addEventListener('click', ()=>{
        const t = promo.textContent.trim();
        navigator.clipboard.writeText(t).then(()=>notify('Promo code copied!', '#7c3aed'));
      });
    }
    const ref = document.getElementById('ref');
    if (ref){
      ref.addEventListener('click', ()=>{
        const t = ref.textContent.trim();
        navigator.clipboard.writeText(t).then(()=>notify('Referral URL copied!', '#1e40af'));
      });
    }
    const photoSpan = document.getElementById('photoUrl');
    if (photoSpan && photoSpan.textContent.trim() && photoSpan.textContent.trim() !== '—'){
      photoSpan.style.cursor = 'pointer';
      photoSpan.addEventListener('click', ()=>{
        const t = photoSpan.textContent.trim();
        navigator.clipboard.writeText(t).then(()=>notify('Photo/Video URL copied!', '#0ea5e9'));
      });
    }

    (function(){
      const btnOpenMedia = document.getElementById('btnOpenMedia');
      const btnTop = document.getElementById('btnMediaTop');
      const src = (photoSpan && photoSpan.textContent.trim() && photoSpan.textContent.trim() !== '—') ? photoSpan.textContent.trim() : '';

      function openMedia(url){
        if(!url){ notify('No Photo/Video URL provided', '#9ca3af'); return; }
        let u = url;
        if(!/^https?:\/\//i.test(u)) u = 'https://' + u.replace(/^\/+/, '');
        window.open(u, '_blank', 'noopener');
      }
      if(btnOpenMedia){
        btnOpenMedia.addEventListener('click', ()=> openMedia(src));
      }
      if(btnTop && (!src || src==='—')){
        btnTop.addEventListener('click', (e)=>{ e.preventDefault(); notify('No Photo/Video URL provided', '#9ca3af'); });
      }
    })();

    async function safeJson(res){
      const text = await res.text();
      try { return JSON.parse(text); } catch(e){}
      const m = text.match(/\{[\s\S]*\}/);
      if (m){
        try { return JSON.parse(m[0]); } catch(e){}
      }
      return { ok:false, error:'Invalid JSON response' };
    }

    (function(){
      const btn = document.getElementById('btnSave');
      const form = document.getElementById('socialForm');
      if(!btn || !form) return;

      btn.addEventListener('click', async ()=>{
        const fd = new FormData(form);
        fd.append('action','update_social');

        btn.disabled = true; const old = btn.textContent; btn.textContent = 'Saving...';
        try{
          const res = await fetch(location.href, {
            method:'POST',
            body: fd,
            cache:'no-store',
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
          });

          const j = await safeJson(res);
          if (j.ok){
            notify('Saved (updated existing record)', '#16a34a');
          } else {
            const msg = j.error ? `Save failed: ${j.error}` : `Save failed (${res.status})`;
            notify(msg, '#dc2626');
          }
        }catch(e){
          notify('Network error', '#dc2626');
        }finally{
          btn.disabled = false; btn.textContent = old;
        }
      });
    })();
  </script>
</body>
</html>
