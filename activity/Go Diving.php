<?php 
include('../template/start.php');
?>

<?php
// ---------- PHP: Handle Snorkeling Booking ----------
date_default_timezone_set('Asia/Makassar');

$booking_status = null;
$booking_msg = '';
$booking_code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'book') {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $participants = trim($_POST['participants'] ?? '');
    $date   = trim($_POST['date'] ?? '');

    $errors = [];
    if ($name === '')  $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($phone === '') $errors[] = 'Phone is required.';
    if ($destination === '') $errors[] = 'Please choose a snorkeling destination.';
    if ($participants === '' || $participants <= 0) $errors[] = 'Please specify number of participants.';
    if ($date === '') $errors[] = 'Please choose a date.';

    $minDate = (new DateTime('today'))->modify('+2 days')->format('Y-m-d');
    if ($date !== '' && $date < $minDate) {
        $errors[] = 'Date must be on or after ' . $minDate . '.';
    }

    if (empty($errors)) {
        // === Generate Booking Code ===
        $rand = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
        $booking_code = 'BD-' . date('Ymd') . '-' . $rand;

        // === Email Settings ===
        $to       = 'admin@balidiving.com';
        $subject  = 'New Snorkeling Booking - ' . $destination . ' [' . $booking_code . ']';
        $cc       = 'subhi@balidiving.com';
        $from     = 'noreply@balidiving.com';

        $body = "New Snorkeling Booking Request\n\n"
              . "Booking Code : {$booking_code}\n"
              . "Name          : {$name}\n"
              . "Email         : {$email}\n"
              . "Phone         : {$phone}\n"
              . "Destination   : {$destination}\n"
              . "Participants  : {$participants}\n"
              . "Preferred Date: {$date}\n\n"
              . "Submitted at  : " . date('Y-m-d H:i:s') . " WITA\n\n"
              . "Note: Our team will confirm details and pricing via email, phone, or WhatsApp.\n";

        $headers  = "From: Bali Diving <{$from}>\r\n";
        $headers .= "Reply-To: {$email}\r\n";
        $headers .= "CC: {$cc}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $sent = @mail($to, $subject, $body, $headers);

        if ($sent) {
            $booking_status = 'success';
            $booking_msg = "Your booking has been received successfully!<br>
                            <span class='font-semibold text-sky-700'>Booking Code:</span> 
                            <span class='font-mono text-lg text-sky-800'>{$booking_code}</span><br>
                            Our team will contact you soon.";
        } else {
            $booking_status = 'error';
            $booking_msg = 'Email sending failed. Please contact admin directly.';
        }
    } else {
        $booking_status = 'error';
        $booking_msg = implode(' ', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Bali Snorkeling Booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a2d9d5a58a.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gradient-to-br from-sky-50 via-blue-50 to-white min-h-screen text-gray-800">

  <div class="max-w-7xl mx-auto p-6 md:p-10">
    <h1 class="text-3xl font-bold text-sky-700 mb-6 flex items-center gap-3">
      <i class="fa-solid fa-person-swimming text-sky-500"></i> Snorkeling Booking
    </h1>

    <?php if ($booking_status === 'success'): ?>
      <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 p-4 text-center shadow animate-fadeIn">
        <i class="fa-solid fa-circle-check mr-2"></i>
        <p class="text-lg"><?= $booking_msg ?></p>
      </div>
    <?php elseif ($booking_status === 'error'): ?>
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-700 p-4">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i><?= htmlspecialchars($booking_msg) ?>
      </div>
    <?php endif; ?>

    <!-- Step 1: Destination + Book -->
    <?php if (!$booking_status): ?>
    <div id="bookingBoard" class="flex flex-col md:flex-row gap-6 justify-center items-start transition-all duration-500">

      <!-- LEFT: Destination Cards -->
      <div id="leftBoard" class="flex flex-col gap-4 w-full md:w-1/3 transition-all duration-700">
        <?php
        $destinations = [
          'Padang Bai' => 'fa-water',
          'Tulamben' => 'fa-ship',
          'Amed' => 'fa-fish',
          'Nusa Penida & Manta Point' => 'fa-water-ladder'
        ];
        foreach ($destinations as $name => $icon):
        ?>
          <div class="dest-card bg-white border border-gray-200 rounded-xl p-5 text-center cursor-pointer shadow hover:shadow-lg hover:scale-105 transition-all duration-300"
               onclick="selectDestination('<?= $name ?>', this)">
            <i class="fa-solid <?= $icon ?> text-2xl text-sky-600 mb-2"></i>
            <p class="font-semibold text-gray-700"><?= $name ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- MIDDLE: Trip Details (Hidden by default) -->
      <div id="middleBoard" class="hidden w-full md:w-1/3 transition-all duration-700">
        <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-lg flex flex-col justify-center items-center animate-fadeIn">
          <h3 class="text-lg font-semibold text-sky-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-calendar-day text-sky-600"></i> Trip Details
          </h3>
          <div class="w-full space-y-4 text-center">
            <div>
              <label class="text-sm text-gray-600">Number of Participants</label>
              <input type="number" id="participantInput" min="1" max="20"
                     placeholder="Enter participants"
                     class="w-full border-b border-gray-300 focus:border-sky-500 outline-none py-2 text-center" required>
            </div>
            <div>
              <label class="text-sm text-gray-600">Preferred Date (≥ day-after-tomorrow)</label>
              <input type="date" id="dateInputCard"
                     class="w-full border-b border-gray-300 focus:border-sky-500 outline-none py-2 text-center" required>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: Click to Book -->
      <div id="rightBoard" class="w-full md:w-1/3 flex justify-center">
        <div id="cardClickBook"
             onclick="showBookingForm()"
             class="cursor-pointer bg-white border border-sky-200 rounded-xl p-10 w-72 h-64 flex flex-col justify-center items-center shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 group">
          <i class="fa-solid fa-plus text-5xl text-sky-600 mb-3 group-hover:rotate-90 transition-transform duration-300"></i>
          <h2 class="text-lg font-semibold text-sky-700">Click to Book</h2>
        </div>
      </div>
    </div>

    <!-- Booking Form -->
    <div id="bookingFormCard" class="hidden mt-10 bg-white border border-gray-200 rounded-2xl p-8 max-w-md mx-auto shadow-xl transition-all duration-500">
      <h3 class="text-xl font-semibold mb-4 flex items-center gap-2 text-sky-700">
        <i class="fa-solid fa-calendar-days text-sky-600"></i> Booking Details
      </h3>

      <form method="post" onsubmit="return validateDateBeforeSubmit()">
        <input type="hidden" name="action" value="book">
        <input type="hidden" name="destination" id="selectedDestination">
        <input type="hidden" name="participants" id="participantCount">
        <input type="hidden" name="date" id="hiddenDate">

        <div class="space-y-4">
          <input type="text" name="name" placeholder="Full Name" class="w-full border-b border-gray-300 focus:border-sky-500 outline-none py-2" required>
          <input type="email" name="email" placeholder="Email Address" class="w-full border-b border-gray-300 focus:border-sky-500 outline-none py-2" required>
          <input type="tel" name="phone" placeholder="Phone / WhatsApp" class="w-full border-b border-gray-300 focus:border-sky-500 outline-none py-2" required>

          <p class="text-gray-700 text-sm mt-3">
            <i class="fa-solid fa-location-dot text-sky-600 mr-1"></i>
            Destination: <span id="displayDestination" class="font-semibold text-sky-700">-</span>
          </p>
        </div>

        <button type="submit" class="mt-6 bg-sky-500 hover:bg-sky-600 px-6 py-2 rounded-lg font-semibold text-white shadow-md w-full">
          Book Now
        </button>

        <!-- Accordion Info -->
        <div class="mt-8 border-t border-gray-200 pt-6 text-sm leading-relaxed text-gray-700">
          <button type="button" onclick="toggleAccordion()" 
                  class="w-full flex items-center justify-between text-sky-700 font-semibold">
            <span><i class="fa-solid fa-circle-info text-sky-600 mr-2"></i> What’s Included</span>
            <i id="accordionIcon" class="fa-solid fa-chevron-down transition-transform duration-300"></i>
          </button>

          <div id="accordionContent" class="mt-3 hidden">
            <ul class="list-disc pl-5 space-y-1 text-gray-700">
              <li>Complimentary hotel pickup & drop-off (Sanur, Seminyak, Kuta, Legian, Nusa Dua, Jimbaran, Uluwatu, Canggu)</li>
              <li>All marine park, boat & porter fees</li>
              <li>Lunch box, bottled water, tea & coffee at the dive center</li>
              <li>Experienced snorkeling guide</li>
              <li>Air-conditioned transportation</li>
              <li>Full snorkeling equipment (mask, fins, wetsuit, lifejacket)</li>
              <li>Dive computer & underwater camera</li>
              <li>Equipment washing & storage after trip</li>
              <li>Public Liability Insurance & Government Tax</li>
            </ul>
            <p class="mt-4 text-gray-600 italic text-xs">
              Everything’s taken care of — all you need is swimwear, sunscreen, and your best smile.
            </p>
          </div>
        </div>
      </form>
    </div>
    <?php endif; ?>
  </div>

  <?php include('../template/end.php'); ?>

  <script>
    let selected = null;

    function selectDestination(name, element) {
      document.querySelectorAll('.dest-card').forEach(card => {
        card.classList.remove('ring-4', 'ring-sky-400', 'scale-105');
      });
      element.classList.add('ring-4', 'ring-sky-400', 'scale-105');
      selected = name;

      // show Trip Details
      const mid = document.getElementById('middleBoard');
      mid.classList.remove('hidden');
      mid.classList.add('flex', 'animate-fadeIn');
      setMinDate();
    }

    function showBookingForm() {
      const participants = document.getElementById('participantInput')?.value || '';
      const date = document.getElementById('dateInputCard')?.value || '';

      if (!selected) {
        alert('Please select a snorkeling destination first.');
        return;
      }
      if (!participants || participants < 1) {
        alert('Please enter number of participants.');
        return;
      }
      if (!date) {
        alert('Please choose a preferred date.');
        return;
      }

      document.getElementById('participantCount').value = participants;
      document.getElementById('hiddenDate').value = date;

      // Animate transition
      document.getElementById('leftBoard').classList.add('opacity-0', '-translate-x-10');
      document.getElementById('middleBoard').classList.add('opacity-0', '-translate-x-10');
      setTimeout(() => {
        document.getElementById('leftBoard').classList.add('hidden');
        document.getElementById('middleBoard').classList.add('hidden');
      }, 400);

      const clickCard = document.getElementById('cardClickBook');
      clickCard.classList.add('opacity-0', 'scale-90');
      setTimeout(() => clickCard.classList.add('hidden'), 300);

      const form = document.getElementById('bookingFormCard');
      form.classList.remove('hidden');
      document.getElementById('selectedDestination').value = selected;
      document.getElementById('displayDestination').textContent = `${selected} (${participants} participants, ${date})`;
    }

    function toggleAccordion() {
      const content = document.getElementById('accordionContent');
      const icon = document.getElementById('accordionIcon');
      content.classList.toggle('hidden');
      icon.classList.toggle('rotate-180');
    }

    function setMinDate() {
      const d = new Date();
      d.setDate(d.getDate() + 2);
      const yyyy = d.getFullYear();
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      const dd = String(d.getDate()).padStart(2, '0');
      document.getElementById('dateInputCard').setAttribute('min', `${yyyy}-${mm}-${dd}`);
    }

    function validateDateBeforeSubmit() {
      const dateInput = document.getElementById('hiddenDate').value;
      const d = new Date();
      d.setDate(d.getDate() + 2);
      const min = d.toISOString().split('T')[0];
      if (dateInput < min) {
        alert(`Please choose a date on or after ${min}.`);
        return false;
      }
      return true;
    }
  </script>

  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
  </style>

</body>
</html>
