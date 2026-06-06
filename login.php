<?php
session_start();

/* ===== DEBUG (aktifkan sementara jika butuh) ===== */
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

/* Normalisasi nomor telepon (Indonesia default) -> hanya digit; map ke 62xxxxxxxxx
   Menerima: +62812..., 62812..., 0812..., 812..., bahkan 0062812...
*/
function norm_phone($p){
  $d = preg_replace('/\D+/', '', (string)$p); // buang non-digit
  if ($d === '') return '';

  // jika user pakai prefix internasional 00 (mis. 0062...), buang "00"
  if (strpos($d, '00') === 0) {
    $d = substr($d, 2);
  }

  // kalau sudah mulai dengan 62 -> sudah OK
  if (strpos($d, '62') === 0) {
    return $d;
  }

  // kalau lokal pakai 0 di depan (0812...) -> jadi 62 + sisa
  if ($d[0] === '0') {
    return '62' . substr($d, 1);
  }

  // kalau “telanjang” mulai dari 8 (812...) -> asumsikan Indonesia -> prepend 62
  if ($d[0] === '8') {
    return '62' . $d;
  }

  // fallback: kembalikan apa adanya (untuk negara lain atau format khusus)
  return $d;
}

/* Cek apakah email domain = balidiving.com atau subdomain-nya */
function is_bd_domain(string $email): bool {
  $email = strtolower(trim($email));
  $atPos = strrpos($email, '@');
  if ($atPos === false) return false;
  $domain = substr($email, $atPos + 1);
  if ($domain === 'balidiving.com') return true;
  return (bool)preg_match('/\.balidiving\.com$/', $domain);
}

/* ===== AJAX: LOGIN ===== */
if (isset($_GET['ajax']) && $_GET['ajax']==='login') {
  header('Content-Type: application/json; charset=UTF-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');

  try{
    $raw = file_get_contents('php://input');
    $in  = json_decode($raw, true);
    if(!is_array($in)) { echo json_encode(['ok'=>false,'error'=>'Invalid payload']); exit; }

    $email = trim($in['email'] ?? '');
    $pass  = (string)($in['password'] ?? ''); // diisi nomor HP
    if($email==='' || $pass===''){ echo json_encode(['ok'=>false,'error'=>'Email and phone are required']); exit; }

    $pdo = pdo_conn();

    // Ambil semua lead dengan email tsb
    $st = $pdo->prepare("SELECT id, name, email, phone, brand, created_at, updated_at
                         FROM leads WHERE email = :e ORDER BY updated_at DESC");
    $st->execute([':e'=>$email]);
    $rows = $st->fetchAll();

    if(!$rows){ echo json_encode(['ok'=>false,'error'=>'Email not found']); exit; }

    $inputPhone = norm_phone($pass);
    $okRow = null;

    foreach($rows as $r){
      $leadPhone = norm_phone($r['phone'] ?? '');
      if($leadPhone !== '' && $inputPhone !== ''){
        $fullMatch = ($leadPhone === $inputPhone);
        $last4Lead = substr($leadPhone, -4);
        $last4In   = substr($inputPhone, -4);
        $last4Match = ($last4Lead !== '' && $last4Lead === $last4In);
        if($fullMatch || $last4Match){ $okRow = $r; break; }
      }
    }

    if(!$okRow){
      echo json_encode(['ok'=>false,'error'=>'Phone does not match our record']);
      exit;
    }

    // Set session (member)
    $_SESSION['member_lead_id']   = $okRow['id'];
    $_SESSION['member_email']     = $okRow['email'];
    $_SESSION['member_name']      = $okRow['name'] ?? '';
    $_SESSION['member_brand']     = $okRow['brand'] ?? 'BALI DIVING';
    $_SESSION['member_logged_at'] = time();

    $_SESSION['user_email'] = strtolower(trim($okRow['email']));
    setcookie(
      'user_email',
      strtolower(trim($okRow['email'])),
      [
        'expires'  => time()+60*60*24*7,
        'path'     => '/',
        'domain'   => '.balidiving.com',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax',
      ]
    );

    $redirectUrl = is_bd_domain($okRow['email'])
      ? 'https://balidiving.com/en/crm'
      : 'https://balidiving.com/profile';

    echo json_encode([
      'ok'=>true,
      'redirect'=>$redirectUrl,
      'user'=>[
        'id'    => $okRow['id'],
        'email' => $okRow['email'],
        'name'  => $okRow['name'] ?? '',
        'brand' => $okRow['brand'] ?? 'BALI DIVING'
      ]
    ]);
    exit;

  }catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'Server error']);
    exit;
  }
}

/* Logout cepat */
if (isset($_GET['logout'])) {
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
  }
  setcookie('user_email','',time()-3600,'/','.balidiving.com',true,true);
  session_destroy();
  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}
?>
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bali Diving Member - Login</title>
  <script src="/_sdk/element_sdk.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {margin:0;padding:0;height:100%;font-family:'Segoe UI',sans-serif;
          background:linear-gradient(135deg,#001e3c,#0066cc);overflow:hidden;}
    html{height:100%}
    .ocean-bg{position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;}
    .bubble{position:absolute;background:rgba(255,255,255,0.1);border-radius:50%;animation:float 6s infinite ease-in-out;}
    @keyframes float{0%{transform:translateY(100vh) scale(0);opacity:0;}10%{opacity:1;}90%{opacity:1;}100%{transform:translateY(-100px) scale(1);opacity:0;}}
    .container{position:relative;z-index:10;display:flex;justify-content:center;align-items:center;min-height:100vh;}
    .login-card{background:rgba(255,255,255,0.95);padding:40px;border-radius:20px;box-shadow:0 20px 40px rgba(0,30,60,0.3);width:100%;max-width:400px;text-align:center;}
    .login-button{width:100%;padding:15px;background:linear-gradient(135deg,#0066cc,#4da6ff);color:#fff;border:none;border-radius:12px;font-size:16px;font-weight:600;cursor:pointer;transition:all .3s;}
    .login-button:hover{background:linear-gradient(135deg,#0052a3,#3d8bff);transform:translateY(-2px);}
  </style>
 </head>
 <body>
  <div class="ocean-bg">
    <div class="bubble" style="width:20px;height:20px;left:10%;animation-delay:0s"></div>
    <div class="bubble" style="width:25px;height:25px;left:30%;animation-delay:2s"></div>
    <div class="bubble" style="width:30px;height:30px;left:70%;animation-delay:1s"></div>
  </div>

  <main class="container">
    <div class="login-card">
      <img src="images/bali-diving-logo.svg" alt="Bali Diving" width="80" height="80" class="mx-auto mb-3">
      <h2 class="text-2xl font-bold mb-4 text-blue-900">Welcome Back</h2>
      <form id="loginForm">
        <div class="mb-4 text-left">
          <label class="block mb-2 font-semibold text-blue-900">Email</label>
          <input type="email" id="email" name="email" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Enter your email" required>
        </div>
        <div class="mb-4 text-left">
          <label class="block mb-2 font-semibold text-blue-900">Password</label>
          <input type="password" id="password" name="password" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Enter your phone number" required>
        </div>
        <button type="submit" class="login-button">Sign In</button>
      </form>
      <div class="mt-6 text-sm">
        <a href="#forgot" class="text-blue-600 font-medium">Forgot Password?</a>
        <span class="mx-2 text-gray-500">|</span>
        <a href="https://balidiving.com/register" id="registerLink" class="text-green-600 font-medium">Register</a>
      </div>
    </div>
  </main>

  <script>
    function toast(msg, gradient){
      const el=document.createElement('div');
      el.style.cssText=`position:fixed;top:20px;right:20px;background:${gradient};color:white;padding:12px 18px;border-radius:8px;box-shadow:0 3px 10px rgba(0,0,0,.2);z-index:999;`;
      el.textContent=msg;document.body.appendChild(el);
      setTimeout(()=>el.remove(),3500);
    }

    document.getElementById('loginForm').addEventListener('submit', async(e)=>{
      e.preventDefault();
      const email=document.getElementById('email').value.trim();
      const password=document.getElementById('password').value.trim();
      if(!email||!password) return;

      const btn=document.querySelector('.login-button');
      const ori=btn.textContent;
      btn.textContent='Processing...';btn.disabled=true;

      try{
        const res=await fetch(location.pathname+'?ajax=login',{
          method:'POST',
          headers:{'Content-Type':'application/json'},
          body:JSON.stringify({email,password}),
          credentials:'same-origin'
        });
        const data=await res.json();
        if(data.ok){
          toast('Login successful!','linear-gradient(135deg,#10b981,#34d399)');
          setTimeout(()=>location.href=data.redirect||'https://balidiving.com/profile',500);
        }else{
          toast(data.error||'Login failed','linear-gradient(135deg,#ef4444,#f87171)');
        }
      }catch{
        toast('Network/Server error','linear-gradient(135deg,#ef4444,#f87171)');
      }finally{
        btn.textContent=ori;btn.disabled=false;
      }
    });

    document.querySelector('a[href="#forgot"]').addEventListener('click',(e)=>{
      e.preventDefault();
      toast('Please contact our staff to reset access.','linear-gradient(135deg,#3b82f6,#60a5fa)');
    });

    document.getElementById('registerLink').addEventListener('click',()=>{
      toast('Opening pricelist & registration…','linear-gradient(135deg,#10b981,#34d399)');
    });
  </script>
 </body>
</html>
