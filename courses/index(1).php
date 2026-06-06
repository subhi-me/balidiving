
<?php
include('../template/start.php');

// ---------- PHP: Handle Enroll Submit ----------
date_default_timezone_set('Asia/Makassar');

$enroll_status = null;
$enroll_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'enroll') {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $date   = trim($_POST['date'] ?? '');

    $errors = [];
    if ($name === '')  $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($phone === '') $errors[] = 'Phone is required.';
    if ($course === '' || $course === '-- Select Course --') $errors[] = 'Please choose a course.';
    if ($date === '') $errors[] = 'Please choose a date.';

    $minDate = (new DateTime('today'))->modify('+2 days')->format('Y-m-d');
    if ($date !== '' && $date < $minDate) {
        $errors[] = 'Date must be on or after ' . $minDate . '.';
    }

    if (empty($errors)) {
        // === EMAIL SETTINGS ===
        $to       = 'admin@balidiving.com';
        $subject  = 'New PADI/Scuba Diving Enrollment - ' . $course;
        $cc       = 'subhi@balidiving.com';
        $from     = 'noreply@balidiving.com'; // gunakan domain sendiri (lebih aman)

        // === EMAIL BODY ===
        $body = "New Enrollment Request\n\n"
              . "Name   : {$name}\n"
              . "Email  : {$email}\n"
              . "Phone  : {$phone}\n"
              . "Course : {$course}\n"
              . "Preferred Date : {$date}\n\n"
              . "Submitted at: " . date('Y-m-d H:i:s') . " WITA\n\n"
              . "Note: Course price and details will be informed via email, phone, or WhatsApp.\n";

        // === EMAIL HEADERS (lebih aman untuk cPanel/hosting) ===
        $headers  = "From: Bali Diving <{$from}>\r\n";
        $headers .= "Reply-To: {$email}\r\n";
        $headers .= "CC: {$cc}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // === Kirim Email ===
        $sent = @mail($to, $subject, $body, $headers);

        if ($sent) {
            $enroll_status = 'success';
            $enroll_msg = 'Enrollment sent successfully! Our team will contact you soon via email, phone, or WhatsApp.';
        } else {
            $enroll_status = 'error';
            $enroll_msg = 'Email sending failed. Please contact admin directly or check your server mail() configuration.';
        }
    } else {
        $enroll_status = 'error';
        $enroll_msg = implode(' ', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Bali Diving Course Enrollment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a2d9d5a58a.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gradient-to-br from-white via-gray-50 to-gray-200 min-h-screen text-gray-800">

  <div class="max-w-6xl mx-auto p-6 md:p-10">
     <div class="my-10"></div>
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 flex items-center gap-3">
      <i class="fa-solid fa-water text-sky-600"></i> Dive Course Enrollment
    </h1>

    <?php if ($enroll_status === 'success'): ?>
      <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 p-4">
        <i class="fa-solid fa-circle-check mr-2"></i><?= htmlspecialchars($enroll_msg) ?>
      </div>
    <?php elseif ($enroll_status === 'error'): ?>
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-700 p-4">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i><?= htmlspecialchars($enroll_msg) ?>
      </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row flex-wrap gap-10 items-start justify-center" id="cardContainer">
      <!-- Card 1 -->
      <div class="bg-white border border-sky-200 rounded-2xl p-8 w-72 shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2 text-sky-800">
          <i class="fa-solid fa-water"></i> Learn Diving
        </h2>
        <p class="text-gray-500 text-sm">Discover the underwater world through our diving programs for all levels.</p>
      </div>

      <!-- Card 2 (+ New Course with Icon + Text) -->
      <div id="cardNewCourse" onclick="showFormCard()" 
           class="cursor-pointer bg-white border border-emerald-200 rounded-2xl p-8 w-72 h-44 flex flex-col justify-center items-center shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 group">
        <i class="fa-solid fa-plus text-4xl text-emerald-600 mb-3 group-hover:rotate-90 transition-transform duration-300"></i>
        <h2 class="text-lg font-semibold text-emerald-700">Add Your New Course</h2>
      </div>

      <!-- Form Card -->
      <div id="formCard" class="hidden bg-white border border-gray-200 rounded-2xl p-8 w-80 shadow-xl transition-all duration-300">
        <h3 class="text-xl font-semibold mb-4 flex items-center gap-2 text-gray-800">
          <i class="fa-solid fa-clipboard-user text-emerald-600"></i> Register New Course
        </h3>
        <form id="courseForm" onsubmit="return showDropdownCard(event)">
          <div class="space-y-4">
            <input type="text" name="name" id="f_name" placeholder="Full Name" class="w-full bg-transparent border-b border-gray-300 focus:border-emerald-500 outline-none py-2" required>
            <input type="email" name="email" id="f_email" placeholder="Email" class="w-full bg-transparent border-b border-gray-300 focus:border-emerald-500 outline-none py-2" required>
            <input type="tel" name="phone" id="f_phone" placeholder="Phone Number" class="w-full bg-transparent border-b border-gray-300 focus:border-emerald-500 outline-none py-2" required>
          </div>
          <button type="submit" class="mt-6 bg-emerald-500 hover:bg-emerald-600 px-6 py-2 rounded-lg font-semibold text-white shadow-md w-full">
            OK
          </button>
        </form>
      </div>

      <!-- Dropdown + Date + Enroll -->
      <div id="dropdownCard" class="hidden bg-white border border-sky-200 rounded-2xl p-8 w-80 shadow-xl">
        <h3 class="text-xl font-semibold mb-4 flex items-center gap-2 text-sky-700">
          <i class="fa-solid fa-water-ladder text-sky-600"></i> Choose Course & Date
        </h3>
        <form method="post" onsubmit="return validateDateBeforeSubmit()">
          <input type="hidden" name="action" value="enroll">
          <input type="hidden" name="name"  id="h_name">
          <input type="hidden" name="email" id="h_email">
          <input type="hidden" name="phone" id="h_phone">

          <label class="text-sm text-gray-600">Course</label>
          <select name="course" id="course" class="w-full bg-transparent border-b border-gray-300 focus:border-sky-500 outline-none py-2 mb-5" required>
            <option>-- Select Course --</option>
            <option>Open Water Diver</option>
            <option>Advanced Open Water</option>
            <option>Rescue Diver</option>
            <option>Dive Master</option>
            <option>Enriched Air (Nitrox)</option>
          </select>

          <label class="text-sm text-gray-600">Preferred Date (≥ day-after-tomorrow)</label>
          <input type="date" name="date" id="startDate" class="w-full bg-transparent border-b border-gray-300 focus:border-sky-500 outline-none py-2 mb-6" required>

          <button type="submit" class="bg-sky-500 hover:bg-sky-600 px-6 py-2 rounded-lg font-semibold text-white shadow-md w-full">
            Enroll
          </button>
        </form>
        <p class="mt-3 text-xs text-gray-500">
          * We’ll email <span class="font-semibold">admin@balidiving.com</span> and cc <span class="font-semibold">subhi@balidiving.com</span>.<br>
          <span class="italic text-gray-600">Course prices and details will be provided via email, phone, or WhatsApp.</span>
        </p>
      </div>
    </div>
  </div>

<?php include('../template/end.php'); ?>
  <script>
    function setMinDate() {
      const d = new Date();
      d.setDate(d.getDate() + 2);
      const yyyy = d.getFullYear();
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      const dd = String(d.getDate()).padStart(2, '0');
      const minDate = `${yyyy}-${mm}-${dd}`;
      document.getElementById('startDate').setAttribute('min', minDate);
    }

    function showFormCard() {
      const newCourseCard = document.getElementById('cardNewCourse');
      newCourseCard.classList.add('opacity-0', 'scale-90');
      setTimeout(() => newCourseCard.classList.add('hidden'), 300);
      document.getElementById('formCard').classList.remove('hidden');
    }

    function showDropdownCard(event) {
      event.preventDefault();
      document.getElementById('h_name').value  = document.getElementById('f_name').value.trim();
      document.getElementById('h_email').value = document.getElementById('f_email').value.trim();
      document.getElementById('h_phone').value = document.getElementById('f_phone').value.trim();

      document.getElementById('dropdownCard').classList.remove('hidden');
      setMinDate();
      return false;
    }

    function validateDateBeforeSubmit() {
      const input = document.getElementById('startDate');
      const chosen = input.value;
      const min = input.getAttribute('min');
      if (chosen < min) {
        alert(`Please choose a date on or after ${min}.`);
        return false;
      }
      return true;
    }

    document.addEventListener('DOMContentLoaded', setMinDate);
  </script>
 
</body>
</html>
