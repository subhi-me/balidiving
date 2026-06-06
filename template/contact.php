<?php
/*********************************************************
 * Quick Contact -> Email + Create New Lead (BALI DIVING)
 * PHP 8 / PDO MySQL  |  Update: Dropdown FAQ & Clean UI
 *********************************************************/

/** === DB CONFIG === */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/** === Helpers === */
function uid() { return strtoupper(dechex(time())).substr(strtoupper(md5(uniqid('', true))), 0, 8); }
function now(){ return date('Y-m-d H:i:s'); }
function clean($v){ return trim((string)$v); }

/** === Connect DB (PDO) === */
$pdo = null;
$db_connected = false;
$db_error = '';
try {
  $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  $db_connected = true;
  // Ensure leads table exists
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS leads (
      id VARCHAR(64) PRIMARY KEY,
      `column` VARCHAR(32) NOT NULL DEFAULT 'leads',
      stage VARCHAR(64) NOT NULL DEFAULT 'New',
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
      priority VARCHAR(16) NOT NULL DEFAULT 'medium',
      assigned_to VARCHAR(64) NULL,
      url TEXT NULL,
      notes TEXT NULL,
      brand VARCHAR(64) NOT NULL DEFAULT 'BALI DIVING',
      created_at DATETIME NOT NULL,
      updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");
} catch (Throwable $e) { $db_error = $e->getMessage(); }

/** === Form handling === */
$status_email = null; $status_db = null; $err_msg = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = clean($_POST['name']    ?? '');
    $email   = clean($_POST['email']   ?? '');
    $message = clean($_POST['message'] ?? '');
    $phone   = clean($_POST['phone']   ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $err_msg = "Please fill in all required fields.";
    } else {
        /** --- Send Email --- **/
        $to       = "sales@balidiving.com"; 
        $cc       = "admin@balidiving.com"; 
        $subject  = "Quick Contact Message - Bali Diving Website";
        $body     = "From: {$name}\nEmail: {$email}\nPhone: {$phone}\n\nMessage:\n{$message}";
        
        $fromAddr = "no-reply@balidiving.com";
        $headers  = "From: {$fromAddr}\r\n";
        $headers .= "Reply-To: {$email}\r\n";
        $headers .= "Cc: {$cc}\r\n";
        $headers .= "X-Mailer: PHP/".phpversion();

        if (@mail($to, $subject, $body, $headers)) { $status_email = 'success'; } 
        else { $status_email = 'error'; }

        /** --- Insert DB --- **/
        if ($db_connected) {
            try {
                $id = uid();
                $stmt = $pdo->prepare("INSERT INTO leads (id, name, email, phone, source, notes, brand, created_at, updated_at) VALUES (:id, :name, :email, :phone, 'Quick Contact', :notes, 'BALI DIVING', :created_at, :updated_at)");
                $stmt->execute([':id'=>$id, ':name'=>$name, ':email'=>$email, ':phone'=>$phone, ':notes'=>$message, ':created_at'=>now(), ':updated_at'=>now()]);
                $status_db = 'success';
            } catch (Throwable $e) { $status_db = 'error'; $err_msg = $e->getMessage(); }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact Bali Diving</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased">

<section class="min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden">
    
    <div class="bg-blue-800 p-8 text-white text-center">
      <h1 class="text-3xl font-bold tracking-tight">Get in Touch</h1>
      <p class="text-blue-100 mt-2">Bali Diving Enquiry & Support</p>
    </div>

    <div class="p-8">
      
      <?php if ($status_email === 'success'): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center">
          <i class="fa-solid fa-circle-check mr-3 text-xl"></i>
          <span>Thank you! Your enquiry has been sent successfully.</span>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-5">
        
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">Your Name</label>
          <div class="relative">
            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="name" required placeholder="Full Name"
              class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">Email</label>
            <div class="relative">
              <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input type="email" name="email" required placeholder="email@address.com"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">WhatsApp / Phone</label>
            <div class="relative">
              <i class="fa-brands fa-whatsapp absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input type="text" name="phone" placeholder="+62..."
                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">Quick Select Topic (Optional)</label>
          <div class="relative">
            <i class="fa-solid fa-list-ul absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            <select onchange="insertFAQ(this.value)"
              class="w-full bg-blue-50 border border-blue-100 text-blue-800 rounded-xl pl-11 pr-4 py-3 appearance-none focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
              <option value="">-- Click to choose a common question --</option>
              <option value="Hi, I am interested in the PADI Open Water Course. Could you provide the price and schedule?">PADI Open Water Course Inquiry</option>
              <option value="Hello, I want to book a Fun Dive trip. Do you have availability for next week?">Fun Dive Booking</option>
              <option value="I would like to know about the pick-up service. Is it included for the Sanur area?">Transport & Pick-up Info</option>
              <option value="Do you offer any group discounts for a group of 5 or more people?">Group Discounts</option>
              <option value="I am a certified diver but haven't dived in over a year. Do I need a Refresh Dive?">Refresher Course Inquiry</option>
            </select>
            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-blue-400 pointer-events-none text-xs"></i>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">Message</label>
          <textarea id="message" name="message" rows="4" required placeholder="Write your message here..."
            class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
        </div>

        <button type="submit" 
          class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 rounded-xl shadow-lg transition-all transform active:scale-[0.98] flex items-center justify-center">
          <i class="fa-solid fa-paper-plane mr-2"></i> Send Enquiry
        </button>

      </form>
    </div>

    <div class="bg-gray-50 p-6 border-t border-gray-100 flex flex-wrap justify-center gap-6 text-xs text-gray-400">
      <span><i class="fa-solid fa-location-dot mr-1"></i> Sanur, Bali</span>
      <span><i class="fa-solid fa-envelope mr-1"></i> sales@balidiving.com</span>
      <span><i class="fa-brands fa-whatsapp mr-1"></i> +62 878 6119 0174</span>
    </div>
  </div>
</section>

<script>
  function insertFAQ(val) {
    if(val !== "") {
      const textarea = document.getElementById('message');
      textarea.value = val;
      // Berikan sedikit efek focus agar user tahu teks sudah masuk
      textarea.focus();
    }
  }
</script>

</body>
</html>