<?php
/* ==========================================================
   SINGLE PAGE: Learning Scuba Diving (UI unchanged)
   + Lead capture to MySQL (u1783223_bd_crm.leads)
   + Email notification to subhi@balidiving.com (Cc: admin)
   ========================================================== */

/* ====== DB CONFIG (sesuaikan jika perlu) ====== */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/* ====== PDO ====== */
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

/* ====== Helpers ====== */
function json_headers(){
  header('Content-Type: application/json; charset=UTF-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');
}
function uid(){ return strtoupper(dechex(time())).substr(strtoupper(md5(uniqid('', true))), 0, 8); }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* ====== Schema ensure (idempotent) ====== */
function table_exists(PDO $pdo, string $table): bool {
  $sql="SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t LIMIT 1";
  $st=$pdo->prepare($sql); $st->execute([':t'=>$table]); return (bool)$st->fetchColumn();
}
function col_exists(PDO $pdo, string $table, string $col): bool {
  $sql="SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1";
  $st=$pdo->prepare($sql); $st->execute([':t'=>$table,':c'=>$col]); return (bool)$st->fetchColumn();
}
function qexec(PDO $pdo, $sql){ try{$pdo->exec($sql);}catch(Throwable $e){ error_log("SQL ERR: ".$e->getMessage()); }}

/* ====== API endpoint: lead_gateway ======
   Expect JSON:
   { name,email,phone,level,experience,medical_cleared,emergency_contact }
================================================ */
if (($_GET['action'] ?? '') === 'lead_gateway' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $isJson = isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
  $in = $isJson ? (json_decode(file_get_contents('php://input'), true) ?: []) : $_POST;

  $name    = trim((string)($in['name'] ?? ''));
  $email   = trim((string)($in['email'] ?? ''));
  $phone   = trim((string)($in['phone'] ?? ''));
  $level   = trim((string)($in['level'] ?? 'Course Registration'));
  $exp     = trim((string)($in['experience'] ?? ''));
  $medok   = (isset($in['medical_cleared']) && ($in['medical_cleared']===true || $in['medical_cleared']==='true' || $in['medical_cleared']==='on')) ? 'Yes' : 'No';
  $emerg   = trim((string)($in['emergency_contact'] ?? ''));

  if ($name==='' || $email==='' || $phone==='') {
    json_headers(); echo json_encode(['ok'=>false,'error'=>'Missing required fields']); exit;
  }

  try{
    $pdo = pdo_conn();

    // Create table if missing (compatible with previous page)
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
        notes TEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } else {
      // Ensure columns used exist
      foreach ([
        "`column` VARCHAR(32) NOT NULL DEFAULT 'leads'",
        "email VARCHAR(255) NULL",
        "phone VARCHAR(64) NULL",
        "source VARCHAR(64) NULL",
        "package VARCHAR(128) NULL",
        "cert VARCHAR(64) NULL",
        "activity VARCHAR(20) NULL",
        "brand VARCHAR(64) NOT NULL DEFAULT 'BALI DIVING'",
        "notes TEXT NULL",
        "created_at DATETIME NOT NULL",
        "updated_at DATETIME NOT NULL"
      ] as $def) {
        $col = trim(strtok($def,' '), '`');
        if(!col_exists($pdo,'leads',$col)){ qexec($pdo, "ALTER TABLE `leads` ADD COLUMN $def"); }
      }
    }

    $id  = uid();
    $now = date('Y-m-d H:i:s');

    $notes = "Learning Scuba Diving Registration\n".
             "Level: $level\nExperience: $exp\nMedical Cleared: $medok\nEmergency Contact: $emerg";

    $data = [
      ':id'              => $id,
      ':column'          => 'leads',
      ':name'            => $name,
      ':email'           => $email,
      ':phone'           => $phone,
      ':country'         => '',
      ':source'          => 'Website Course Registration',
      ':package'         => $level,
      ':cert'            => $exp,
      ':dive_date'       => null,
      ':pax'             => 1,
      ':budget'          => 0,
      ':photo_url'       => '',
      ':payment_status'  => 'unpaid',
      ':payment_method'  => '',
      ':deposit_amount'  => 0,
      ':points_total'    => 0,
      ':points_redeemed' => 0,
      ':promo_code'      => '',
      ':promo_used'      => 0,
      ':loyalty_level'   => '',
      ':social_ig'       => '',
      ':social_fb'       => '',
      ':social_tiktok'   => '',
      ':social_wechat'   => '',
      ':activity'        => 'Course',
      ':brand'           => 'BALI DIVING',
      ':notes'           => $notes,
      ':created_at'      => $now,
      ':updated_at'      => $now,
    ];

    $sql = "INSERT INTO leads
      (id,`column`,name,email,phone,country,source,package,cert,dive_date,pax,budget,
       photo_url,payment_status,payment_method,deposit_amount,points_total,points_redeemed,
       promo_code,promo_used,loyalty_level,social_ig,social_fb,social_tiktok,social_wechat,
       activity,brand,notes,created_at,updated_at)
      VALUES
      (:id,:column,:name,:email,:phone,:country,:source,:package,:cert,:dive_date,:pax,:budget,
       :photo_url,:payment_status,:payment_method,:deposit_amount,:points_total,:points_redeemed,
       :promo_code,:promo_used,:loyalty_level,:social_ig,:social_fb,:social_tiktok,:social_wechat,
       :activity,:brand,:notes,:created_at,:updated_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    // Email notification
    $to      = 'subhi@balidiving.com';
    $subject = 'New Course Registration — '.$level;
    $headers = [];
    $headers[] = 'From: Bali Diving <no-reply@balidiving.com>';
    $headers[] = 'Reply-To: '.($name !== '' ? "$name <$email>" : $email);
    $headers[] = 'Cc: admin@balidiving.com';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    $html = '<html><body style="font-family:Arial,Helvetica,sans-serif">
    <h2>New Learning Scuba Diving Registration</h2>
    <table cellpadding="6" cellspacing="0" border="0" style="border:1px solid #e5e7eb">
      <tr><td><b>ID</b></td><td>'.h($id).'</td></tr>
      <tr><td><b>Level</b></td><td>'.h($level).'</td></tr>
      <tr><td><b>Experience</b></td><td>'.h($exp).'</td></tr>
      <tr><td><b>Medical Cleared</b></td><td>'.h($medok).'</td></tr>
      <tr><td><b>Emergency Contact</b></td><td>'.nl2br(h($emerg)).'</td></tr>
      <tr><td><b>Name</b></td><td>'.h($name).'</td></tr>
      <tr><td><b>Email</b></td><td>'.h($email).'</td></tr>
      <tr><td><b>Phone</b></td><td>'.h($phone).'</td></tr>
      <tr><td><b>Created</b></td><td>'.h($now).'</td></tr>
    </table>
    </body></html>';

    @mail($to, $subject, $html, implode("\r\n", $headers));

    json_headers(); echo json_encode(['ok'=>true,'id'=>$id]); exit;

  }catch(Throwable $e){
    json_headers(); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); exit;
  }
}
?>

<?php
// Load our SEO manager
require_once 'template/seo_manager.php';

// Page identifier (from URL or default to 'home')
$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
      // Generate dynamic SEO tags (title, canonical, OG, etc.)
      echo generate_seo_tags($page);
    ?>

    <link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">

    <!-- Primary SEO for the target keyword: Learning Scuba Diving -->
    <title>Learning Scuba Diving in Bali — Beginner Courses, Try Dives & PADI Certifications</title>
    <meta name="description" content="Learning scuba diving made easy in Bali. Start with a fun try dive or a full PADI Open Water Course. Small groups, patient instructors, hotel pickup & world-class dive sites like Manta Point and the USAT Liberty Wreck." />
    <meta name="keywords" content="Learning scuba diving, learn scuba diving Bali, beginner diving course, PADI Open Water Bali, try dive Bali, scuba lessons, scuba training, Manta Point, USAT Liberty Wreck" />
    <meta name="robots" content="index, follow" />

    <!-- Helpful extras -->
    <meta property="og:title" content="Learning Scuba Diving in Bali — Beginner Courses & Try Dives">
    <meta property="og:description" content="Start learning scuba diving in Bali. Patient PADI pros, safe pool sessions, and unforgettable ocean dives.">
    <meta property="og:type" content="website">

    <?php include('template/style.php')?>
    <?php include('template/pixel.php')?>
</head>
<body class="font-sans">
    <!-- SR-only H1 for SEO while keeping your existing header visuals -->
    <h1 class="sr-only">Learning Scuba Diving in Bali — Beginner Courses, Try Dives & PADI Certifications</h1>

    <?php include('template/nav.php')?>

  <style>
        body {
            box-sizing: border-box;
        }
        
        .timeline-line {
            background: linear-gradient(to bottom, #0ea5e9, #0284c7, #0369a1);
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.3);
        }
        
        .level-card {
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .level-card:hover {
            transform: translateX(5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .add-btn {
            transition: all 0.2s ease;
        }
        
        .add-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(34, 197, 94, 0.4);
        }
        
        .registration-form {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-message {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
  <style>@view-transition { navigation: auto; }</style>
 </head>
 <div style="height:50px;"></div>
 <body class="min-h-full bg-gradient-to-br from-blue-50 to-cyan-100 font-sans">
  <div class="container mx-auto px-4 py-8 max-w-4xl"><!-- Header -->
   <div class="text-center mb-12">
       <section class="bg-gradient-to-b from-blue-50 to-white py-16 px-6">
  <div class="max-w-5xl mx-auto text-center">
    <h1 class="text-3xl md:text-4xl font-bold text-blue-800 mb-6 tracking-tight">
      PADI SCUBA DIVING COURSES
    </h1>
    <p class="text-lg md:text-xl text-gray-700 leading-relaxed max-w-3xl mx-auto">
      Why not try to get your Scuba Diving Certification?  
      If you have always wanted to learn how to scuba dive, discover new adventures,  
      or simply see the wondrous world beneath the waves — this is where you start.
    </p>

  </div>
</section>
    
<section id="skill-highlight" class="bg-black text-white py-8 px-6 relative overflow-hidden">
  <button onclick="document.getElementById('skill-highlight').remove()" 
          class="absolute top-4 right-4 text-gray-400 hover:text-white transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
  </button>

  <div class="max-w-3xl mx-auto text-center">
    <p class="text-gray-300 text-lg leading-relaxed max-w-2xl mx-auto">
      Click Button <span class="bg-white text-black rounded-full px-3 py-1 mx-1 inline-block font-black shadow-md">
        +
      </span>to Register a New Skill<br>Expand your diving journey — each new skill brings new confidence and mastery.  
    </p>

</section>

   </div><!-- Timeline Container -->
   <div class="relative flex justify-center"><!-- Vertical Timeline Line -->
    <div class="timeline-line absolute w-1 h-full left-1/2 transform -translate-x-1/2 rounded-full"></div><!-- Timeline Items -->
    <div class="space-y-16 w-full max-w-2xl"><!-- Try Diving -->
     <div class="relative flex items-center">
      <div class="level-card bg-white/90 rounded-xl p-6 shadow-lg border border-blue-200 w-80 ml-8">
       <div class="flex items-center justify-between">
        <div>
         <h3 class="text-lg font-bold text-blue-900">Try Diving</h3>
         <p class="text-sm text-blue-600">First diving experience</p>
         <p class="text-xs text-gray-500 mt-1">Duration: 1 day | Depth: 6m</p>
        </div><button class="add-btn bg-green-500 hover:bg-green-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-xl" onclick="showRegistrationForm('Try Diving')">+</button>
       </div>
      </div>
      <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-blue-500 rounded-full border-4 border-white shadow-lg"></div>
     </div><!-- Open Water Diver -->
     <div class="relative flex items-center justify-end">
      <div class="level-card bg-white/90 rounded-xl p-6 shadow-lg border border-blue-200 w-80 mr-8">
       <div class="flex items-center justify-between">
        <div>
         <h3 class="text-lg font-bold text-blue-900">Open Water Diver</h3>
         <p class="text-sm text-blue-600">Basic diving certification</p>
         <p class="text-xs text-gray-500 mt-1">Duration: 3-4 days | Depth: 18m</p>
        </div><button class="add-btn bg-green-500 hover:bg-green-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-xl" onclick="showRegistrationForm('Open Water Diver')">+</button>
       </div>
      </div>
      <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-blue-500 rounded-full border-4 border-white shadow-lg"></div>
     </div><!-- Advanced Open Water -->
     <div class="relative flex items-center">
      <div class="level-card bg-white/90 rounded-xl p-6 shadow-lg border border-blue-200 w-80 ml-8">
       <div class="flex items-center justify-between">
        <div>
         <h3 class="text-lg font-bold text-blue-900">Advanced Open Water</h3>
         <p class="text-sm text-blue-600">Enhance your diving skills</p>
         <p class="text-xs text-gray-500 mt-1">Duration: 2-3 days | Depth: 30m</p>
        </div><button class="add-btn bg-green-500 hover:bg-green-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-xl" onclick="showRegistrationForm('Advanced Open Water')">+</button>
       </div>
      </div>
      <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-blue-500 rounded-full border-4 border-white shadow-lg"></div>
     </div><!-- Rescue Diver -->
     <div class="relative flex items-center justify-end">
      <div class="level-card bg-white/90 rounded-xl p-6 shadow-lg border border-blue-200 w-80 mr-8">
       <div class="flex items-center justify-between">
        <div>
         <h3 class="text-lg font-bold text-blue-900">Rescue Diver</h3>
         <p class="text-sm text-blue-600">Rescue training course</p>
         <p class="text-xs text-gray-500 mt-1">Duration: 3-4 days | Depth: 30m</p>
        </div><button class="add-btn bg-green-500 hover:bg-green-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-xl" onclick="showRegistrationForm('Rescue Diver')">+</button>
       </div>
      </div>
      <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-blue-500 rounded-full border-4 border-white shadow-lg"></div>
     </div><!-- Specialty Programs -->
     <div class="relative flex items-center justify-end">
      <div class="level-card bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 shadow-lg border border-purple-200 w-80 mr-8">
       <div class="flex items-center justify-between">
        <div>
         <h3 class="text-lg font-bold text-purple-900">Specialty Programs</h3>
         <p class="text-sm text-purple-600">Specialized diving skills</p>
         <p class="text-xs text-gray-500 mt-1">Duration: 1-2 days each | Various depths</p>
        </div><button class="add-btn bg-purple-500 hover:bg-purple-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-xl" onclick="showSpecialtyForm()">+</button>
       </div>
      </div>
      <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-purple-500 rounded-full border-4 border-white shadow-lg"></div>
     </div><!-- Dive Master -->
     <div class="relative flex items-center">
      <div class="level-card bg-white/90 rounded-xl p-6 shadow-lg border border-blue-200 w-80 ml-8">
       <div class="flex items-center justify-between">
        <div>
         <h3 class="text-lg font-bold text-blue-900">Dive Master</h3>
         <p class="text-sm text-blue-600">Professional dive leader</p>
         <p class="text-xs text-gray-500 mt-1">Duration: 4-6 weeks | Depth: 40m</p>
        </div><button class="add-btn bg-green-500 hover:bg-green-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-xl" onclick="showRegistrationForm('Dive Master')">+</button>
       </div>
      </div>
      
      <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-blue-500 rounded-full border-4 border-white shadow-lg"></div>
     </div>
    </div>
    
   </div><!-- Registration Form Modal -->
   <section class="bg-white py-16 px-6">
  <div class="max-w-5xl mx-auto text-center">

    <p class="text-lg md:text-xl text-gray-700 leading-relaxed max-w-3xl mx-auto">
      To ensure the highest quality of scuba education, all of the PADI dive courses conducted at 
      <span class="font-semibold text-blue-700">Bali Diving</span> are taught by highly qualified, renewed, 
      and insured PADI Instructors with many years of teaching experience. 
      To minimize classroom sessions, we strongly suggest that you read and complete all the 
      knowledge reviews in the <span class="font-medium">PADI manual</span> for your chosen course 
      prior to arrival at Bali Diving.
    </p>
  </div>
</section>

   <div id="registration-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="registration-form bg-white rounded-2xl p-8 max-w-md w-full max-h-[90%] overflow-y-auto shadow-2xl">
     <div class="flex justify-between items-center mb-6">
      <h2 id="form-title" class="text-2xl font-bold text-blue-900">Course Registration</h2><button onclick="hideRegistrationForm()" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
     </div>
     <form id="registration-form" onsubmit="submitRegistration(event)">
      <div class="space-y-4">
       <div><label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label> <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
       </div>
       <div><label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label> <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
       </div>
       <div><label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label> <input type="tel" id="phone" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
       </div>
       <div><label for="experience" class="block text-sm font-medium text-gray-700 mb-1">Diving Experience</label> <select id="experience" name="experience" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"> <option value="">Select experience level</option> <option value="Beginner">Beginner (Never dived before)</option> <option value="Some">Some (1-5 dives)</option> <option value="Experienced">Experienced (&gt;5 dives)</option> </select>
       </div>
       <div><label for="emergency-contact" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label> <input type="text" id="emergency-contact" name="emergency-contact" required placeholder="Name &amp; Phone Number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
       </div>
       <div class="flex items-center"><input type="checkbox" id="medical-cleared" name="medical-cleared" required class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"> <label for="medical-cleared" class="ml-2 block text-sm text-gray-700"> I am in good health and ready to participate in diving courses </label>
       </div>
      </div>
      <div class="mt-6 flex space-x-3"><button type="button" onclick="hideRegistrationForm()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"> Cancel </button> <button type="submit" id="submit-btn" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"> Register Now </button>
      </div>
     </form>
    </div>
   </div><!-- Specialty Programs Modal -->
   <div id="specialty-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="registration-form bg-white rounded-2xl p-8 max-w-2xl w-full max-h-[90%] overflow-y-auto shadow-2xl">
     <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-purple-900">Specialty Programs in Bali</h2><button onclick="hideSpecialtyForm()" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
     </div>
     <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <div class="specialty-option p-4 border border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer" onclick="selectSpecialty('Deep Diver')">
       <h3 class="font-bold text-purple-900">Deep Diver</h3>
       <p class="text-sm text-gray-600">Explore depths up to 40m</p>
      </div>
      <div class="specialty-option p-4 border border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer" onclick="selectSpecialty('Wreck Diver')">
       <h3 class="font-bold text-purple-900">Wreck Diver</h3>
       <p class="text-sm text-gray-600">Explore USAT Liberty &amp; more</p>
      </div>
      <div class="specialty-option p-4 border border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer" onclick="selectSpecialty('Night Diver')">
       <h3 class="font-bold text-purple-900">Night Diver</h3>
       <p class="text-sm text-gray-600">Discover nocturnal marine life</p>
      </div>
      <div class="specialty-option p-4 border border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer" onclick="selectSpecialty('Drift Diver')">
       <h3 class="font-bold text-purple-900">Drift Diver</h3>
       <p class="text-sm text-gray-600">Master current diving</p>
      </div>
      <div class="specialty-option p-4 border border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer" onclick="selectSpecialty('Underwater Photography')">
       <h3 class="font-bold text-purple-900">Underwater Photography</h3>
       <p class="text-sm text-gray-600">Capture underwater moments</p>
      </div>
      <div class="specialty-option p-4 border border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer" onclick="selectSpecialty('Peak Performance Buoyancy')">
       <h3 class="font-bold text-purple-900">Peak Performance Buoyancy</h3>
       <p class="text-sm text-gray-600">Perfect your buoyancy control</p>
      </div>
      <div class="specialty-option p-4 border border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer" onclick="selectSpecialty('Coral Reef Conservation')">
       <h3 class="font-bold text-purple-900">Coral Reef Conservation</h3>
       <p class="text-sm text-gray-600">Learn reef protection</p>
      </div>
      <div class="specialty-option p-4 border border-purple-200 rounded-lg hover:bg-purple-50 cursor-pointer" onclick="selectSpecialty('Fish Identification')">
       <h3 class="font-bold text-purple-900">Fish Identification</h3>
       <p class="text-sm text-gray-600">Identify tropical fish species</p>
      </div>
     </div>
     <div class="text-center"><button onclick="hideSpecialtyForm()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"> Close </button>
     </div>
    </div>
   </div><!-- Success Message -->
   <div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg hidden success-message">
    <div class="flex items-center"><span class="mr-2">✓</span> <span>Registration successful! We will contact you soon.</span>
    </div>
   </div>
  </div>
  <script>
        let currentLevel = '';
        let registrations = [];
        let recordCount = 0;

        // Default configuration
        const defaultConfig = {
            main_title: "PADI Scuba Diving Certification",
            subtitle: "Start Your Diving Journey",
            primary_color: "#1e40af",
            secondary_color: "#0ea5e9",
            text_color: "#1e3a8a",
            background_color: "#f0f9ff",
            accent_color: "#22c55e"
        };

        // Data handler for SDK
        const dataHandler = {
            onDataChanged(data) {
                registrations = data;
                recordCount = data.length;
            }
        };

        // Element SDK functions
        async function onConfigChange(config) {
            const mainTitle = config.main_title || defaultConfig.main_title;
            const subtitle = config.subtitle || defaultConfig.subtitle;
            const primaryColor = config.primary_color || defaultConfig.primary_color;
            const secondaryColor = config.secondary_color || defaultConfig.secondary_color;
            const textColor = config.text_color || defaultConfig.text_color;
            const backgroundColor = config.background_color || defaultConfig.background_color;
            const accentColor = config.accent_color || defaultConfig.accent_color;

            document.getElementById('main-title').textContent = mainTitle;
            document.getElementById('subtitle').textContent = subtitle;

            // Apply colors
            document.body.style.background = `linear-gradient(to bottom right, ${backgroundColor}, #cffafe)`;
            document.getElementById('main-title').style.color = primaryColor;
            document.getElementById('subtitle').style.color = textColor;
            const ci = document.getElementById('contact-info'); if (ci) ci.style.color = textColor;

            // Update timeline and cards
            const timelineLine = document.querySelector('.timeline-line');
            if (timelineLine) {
                timelineLine.style.background = `linear-gradient(to bottom, ${secondaryColor}, ${primaryColor}, ${textColor})`;
            }

            const levelCards = document.querySelectorAll('.level-card h3');
            levelCards.forEach(card => {
                card.style.color = primaryColor;
            });

            const addButtons = document.querySelectorAll('.add-btn');
            addButtons.forEach(btn => {
                btn.style.backgroundColor = accentColor;
            });
        }

        function mapToCapabilities(config) {
            return {
                recolorables: [
                    {
                        get: () => config.primary_color || defaultConfig.primary_color,
                        set: (value) => { if (window.elementSdk) window.elementSdk.setConfig({ primary_color: value }); }
                    },
                    {
                        get: () => config.secondary_color || defaultConfig.secondary_color,
                        set: (value) => { if (window.elementSdk) window.elementSdk.setConfig({ secondary_color: value }); }
                    },
                    {
                        get: () => config.text_color || defaultConfig.text_color,
                        set: (value) => { if (window.elementSdk) window.elementSdk.setConfig({ text_color: value }); }
                    },
                    {
                        get: () => config.background_color || defaultConfig.background_color,
                        set: (value) => { if (window.elementSdk) window.elementSdk.setConfig({ background_color: value }); }
                    },
                    {
                        get: () => config.accent_color || defaultConfig.accent_color,
                        set: (value) => { if (window.elementSdk) window.elementSdk.setConfig({ accent_color: value }); }
                    }
                ],
                borderables: [],
                fontEditable: undefined,
                fontSizeable: undefined
            };
        }

        function mapToEditPanelValues(config) {
            return new Map([
                ["main_title", config.main_title || defaultConfig.main_title],
                ["subtitle", config.subtitle || defaultConfig.subtitle],
                ["contact_info", config.contact_info || defaultConfig.contact_info]
            ]);
        }

        // Registration functions
        function showRegistrationForm(level) {
            currentLevel = level;
            document.getElementById('form-title').textContent = `${level} Registration`;
            document.getElementById('registration-modal').classList.remove('hidden');
            document.getElementById('registration-modal').classList.add('flex');
            document.getElementById('registration-form').reset();
        }

        function showSpecialtyForm() {
            document.getElementById('specialty-modal').classList.remove('hidden');
            document.getElementById('specialty-modal').classList.add('flex');
        }

        function hideSpecialtyForm() {
            document.getElementById('specialty-modal').classList.add('hidden');
            document.getElementById('specialty-modal').classList.remove('flex');
        }

        function selectSpecialty(specialty) {
            hideSpecialtyForm();
            showRegistrationForm(specialty);
        }

        function hideRegistrationForm() {
            document.getElementById('registration-modal').classList.add('hidden');
            document.getElementById('registration-modal').classList.remove('flex');
        }

        // ====== ENHANCED: submitRegistration
        // Keeps UI/flow the same, adds POST to PHP API for DB+Email
        async function submitRegistration(event) {
            event.preventDefault();
            
            if (recordCount >= 999) {
                showMessage("Maximum limit of 999 registrations reached. Please contact admin.", "error");
                return;
            }

            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Registering...';

            const formData = new FormData(event.target);
            const registrationData = {
                id: Date.now().toString(),
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                level: currentLevel,
                experience: formData.get('experience'),
                medical_cleared: formData.get('medical-cleared') === 'on',
                emergency_contact: formData.get('emergency-contact'),
                registration_date: new Date().toISOString()
            };

            // Keep existing data SDK behavior (if present) – UI unchanged
            if (window.dataSdk) {
                try {
                    const result = await window.dataSdk.create(registrationData);
                    if (!result.isOk) {
                        console.warn("dataSdk create failed, continuing to API lead gateway");
                    }
                } catch(e){ console.warn("dataSdk error:", e); }
            }

            // NEW: Send to backend API on the same page for DB insert + email
            try{
                await fetch(location.pathname + '?action=lead_gateway', {
                    method: 'POST',
                    headers: { 'Content-Type':'application/json; charset=UTF-8' },
                    body: JSON.stringify({
                        name: registrationData.name,
                        email: registrationData.email,
                        phone: registrationData.phone,
                        level: registrationData.level,
                        experience: registrationData.experience,
                        medical_cleared: registrationData.medical_cleared,
                        emergency_contact: registrationData.emergency_contact
                    }),
                    cache: 'no-store',
                    keepalive: true
                });
            }catch(e){ console.warn('Lead gateway call failed:', e); }

            hideRegistrationForm();
            showMessage("Registration successful! We will contact you soon.", "success");

            submitBtn.disabled = false;
            submitBtn.textContent = 'Register Now';
        }

        function showMessage(message, type) {
            const messageEl = document.getElementById('success-message');
            messageEl.textContent = message;
            messageEl.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg success-message ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            messageEl.classList.remove('hidden');
            
            setTimeout(() => {
                messageEl.classList.add('hidden');
            }, 5000);
        }

        // Initialize SDKs
        async function initializeApp() {
            if (window.dataSdk) {
                try{
                    const initResult = await window.dataSdk.init(dataHandler);
                    if (!initResult.isOk) {
                        console.error("Failed to initialize data SDK");
                    }
                }catch(e){ console.warn("dataSdk init error:", e); }
            }

            if (window.elementSdk) {
                window.elementSdk.init({
                    defaultConfig,
                    onConfigChange,
                    mapToCapabilities,
                    mapToEditPanelValues
                });
            }
        }

        // Start the application
        initializeApp();
    </script>
    <?php include('template/floor-ani.php') ?>
    <?php include('template/footer.php')?>

    <!-- Lightbox -->
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 hidden z-[60]" role="dialog" aria-modal="true" aria-labelledby="lightbox-title" aria-describedby="lightbox-description">
        <div id="lightbox-container" class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-full overflow-y-auto relative">
            <button id="lightbox-close" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800" aria-label="Close image lightbox">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div>
                <img id="lightbox-image" src="" alt="Dive site image for learning scuba diving in Bali" class="w-full h-64 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 id="lightbox-title" class="text-3xl font-bold text-navy mb-3"></h3>
                    <p id="lightbox-description" class="text-gray-700"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Widget -->
    <div class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] z-40 hidden" id="chatWidget" aria-live="polite">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <img src="bali-diving-logo.svg" alt="Bali Diving Logo" width="28" height="28" loading="lazy">
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Diving Expert</h2>
                            <p class="text-sm text-blue-100 flex items-center">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2" aria-hidden="true"></span>
                                Online now
                            </p>
                        </div>
                    </div>
                    <button onclick="toggleChat()" class="text-white hover:text-blue-200 transition-colors" aria-label="Close chat">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" role="img" aria-hidden="true">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="bg-white" style="height: 400px; overflow-y: auto;" id="chatContainer">
                <div class="p-4 space-y-3" id="chatMessages"></div>
            </div>
            <div class="bg-gray-50 p-4 border-t border-gray-100">
                <div class="flex space-x-2">
                    <input type="text" id="userInput" placeholder="Ask about learning scuba diving…" aria-label="Type your message"
                           class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button onclick="sendMessage()" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full px-4 py-2 text-sm font-medium transition-colors" aria-label="Send message">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed bottom-6 right-6 z-50" id="chatLauncher">
        <button onclick="toggleChat()"
                class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center group"
                aria-expanded="false" aria-controls="chatWidget" aria-label="Open chat">
            <svg id="chatIcon" class="w-6 h-6 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24" role="img" aria-hidden="true">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
            </svg>
            <svg id="closeIcon" class="w-6 h-6 transition-transform duration-300 hidden" fill="currentColor" viewBox="0 0 24 24" role="img" aria-hidden="true">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
            <span class="ml-2 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap" id="launcherText">
                Chat with us
            </span>
        </button>
    </div>

    <script>
        // --- Navigation behavior ---
        const navElement = document.querySelector('nav');
        const navContent = document.getElementById('nav-content');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navElement.classList.add('bg-navy/70', 'shadow-2xl');
                if (navContent){ navContent.classList.remove('h-16'); navContent.classList.add('h-12'); }
            } else {
                navElement.classList.remove('bg-navy/70', 'shadow-2xl');
                if (navContent){ navContent.classList.remove('h-12'); navContent.classList.add('h-16'); }
            }
        });

        // Mobile menu accessibility
        const mobileBtn = document.getElementById('mobile-menu-btn');
        if (mobileBtn) {
          mobileBtn.setAttribute('aria-label', 'Open mobile menu');
          mobileBtn.addEventListener('click', function() {
              const menu = document.getElementById('mobile-menu');
              if (!menu) return;
              const isHidden = menu.classList.toggle('hidden');
              mobileBtn.setAttribute('aria-expanded', String(!isHidden));
          });
        }
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                const m = document.getElementById('mobile-menu');
                if (m) m.classList.add('hidden');
            });
        });

        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
        }

        // --- Lightbox content focused on "Learning Scuba Diving" ---
        document.addEventListener('DOMContentLoaded', () => {
            const cy = document.getElementById('current-year'); if (cy) cy.textContent = new Date().getFullYear();

            const siteData = {
                'manta-point': {
                    title: 'Learning Scuba Diving at Manta Point',
                    imageUrl: 'template/images/manta-point.jpg',
                    description: 'Perfect for learning scuba diving with gentle conditions on selected days. Meet majestic manta rays while you build skills with our PADI pros.'
                },
                'crystal-bay': {
                    title: 'Learning Scuba Diving at Crystal Bay',
                    imageUrl: 'template/images/crystal.jpg',
                    description: 'Known for clear visibility and vibrant reefs. When in season, you might spot mola mola while practicing buoyancy and safe learning progressions.'
                },
                'usat-liberty': {
                    title: 'Learning Scuba Diving — USAT Liberty Wreck',
                    imageUrl: 'template/images/usat.jpg',
                    description: 'A world-famous wreck in shallow water—ideal for beginner confidence. Explore coral-covered structures with your instructor by your side.'
                },
                'coral-garden': {
                    title: 'Learning Scuba Diving at Coral Garden',
                    imageUrl: 'template/images/coral.jpg',
                    description: 'Calm, colorful reef life in shallow depths—great for your first open-water skills after pool practice during your learning scuba diving journey.'
                }
            };

            const lightbox = document.getElementById('lightbox'),
                  lightboxClose = document.getElementById('lightbox-close');

            const openLightbox = (siteKey) => {
                const data = siteData[siteKey];
                if (data) {
                    document.getElementById('lightbox-title').textContent = data.title;
                    const img = document.getElementById('lightbox-image');
                    img.src = data.imageUrl;
                    img.alt = data.title + ' — Bali';
                    document.getElementById('lightbox-description').textContent = data.description;
                    lightbox.classList.remove('hidden');
                }
            };
            const closeLightbox = () => lightbox.classList.add('hidden');

            document.querySelectorAll('[data-site]').forEach(link => link.addEventListener('click', e => {
                e.preventDefault();
                openLightbox(link.dataset.site);
            }));

            lightboxClose.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });
        });
    </script>

    <script>
        // Dynamic hero slider (kept as-is; now with keyword-friendly alt)
        <?php
            $files = glob('template/images/slider/*.webp');
            $imagePaths = [];
            foreach ($files as $file) {
                $imagePaths[] = basename($file);
            }
            $imageNamesJSON = json_encode($imagePaths);
            echo "const imageNames = " . $imageNamesJSON . ";";
        ?>

        document.addEventListener('DOMContentLoaded', () => {
            const sliderContainer = document.getElementById('hero-slider');
            if (!sliderContainer) return;
            const shuffledImages = [...imageNames].sort(() => 0.5 - Math.random());
            let currentIndex = 0;

            const preloadImages = () => {
                if (shuffledImages.length === 0) {
                    console.error("No .webp images found in 'images/slider' folder.");
                    return;
                }

                shuffledImages.forEach(imageName => {
                    const img = document.createElement('img');
                    img.src = `images/slider/${imageName}`;
                    img.alt = 'Learning scuba diving in Bali — underwater scene';
                    img.loading = 'eager';
                    img.decoding = 'async';
                    img.classList.add('hero-background-image', 'absolute', 'inset-0');
                    sliderContainer.appendChild(img);
                });
            };

            const startSlider = () => {
                const images = sliderContainer.getElementsByClassName('hero-background-image');
                if (images.length === 0) return;
                images[0].classList.add('active');
                setInterval(() => {
                    images[currentIndex].classList.remove('active');
                    currentIndex = (currentIndex + 1) % images.length;
                    images[currentIndex].classList.add('active');
                }, 5000);
            };

            preloadImages();
            startSlider();
        });
    </script>
    
    <?php include('template/chat.php') ?>
</body>
</html>
