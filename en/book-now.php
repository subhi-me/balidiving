<?php
$to = "admin@balidiving.com";

$name         = trim($_POST['name'] ?? '');
$whatsapp     = trim($_POST['whatsapp'] ?? '');
$activity     = trim($_POST['activity'] ?? '');
$location     = trim($_POST['location'] ?? '');
$date         = trim($_POST['date'] ?? '');
$level        = trim($_POST['level'] ?? '');
$participants = trim($_POST['participants'] ?? '');
$message      = trim($_POST['message'] ?? '');

$participantList = "";
for ($i = 1; $i <= $participants; $i++) {
    $pname = trim($_POST["participant_$i"] ?? '');
    $participantList .= "<li>👤 Participant $i: " . htmlspecialchars($pname) . "</li>";
}

if (!empty($name) && !empty($whatsapp)) {

    $subject = "🌐 Booking Request - {$activity} by {$name}";
    $emailBody = "
    <html><body style='font-family: Arial, sans-serif;'>
    <h2>New Booking Request</h2>
    <p><b>Name:</b> {$name}<br>
    <b>WhatsApp:</b> {$whatsapp}<br>
    <b>Activity:</b> {$activity}<br>
    <b>Location:</b> {$location}<br>
    <b>Date:</b> {$date}<br>
    <b>Certification Level:</b> {$level}<br>
    <b>Participants:</b> {$participants}</p>
    <ul>{$participantList}</ul>
    <p><b>Notes:</b><br>" . nl2br(htmlspecialchars($message)) . "</p>
    </body></html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: BaliDiving.com <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n";

    mail($to, $subject, $emailBody, $headers);

    // Confirmation Page
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
      <meta charset='UTF-8'>
      <title>Order Confirmation - BaliDiving.com</title>
      <script src='https://cdn.tailwindcss.com'></script>
    </head>
    <body class='bg-gradient-to-br from-sky-100 via-white to-blue-50 flex items-center justify-center min-h-screen px-4'>
      <div class='bg-white/80 backdrop-blur-md border border-sky-200 rounded-3xl shadow-xl p-8 md:p-10 w-full max-w-2xl space-y-6'>
        <h2 class='text-3xl font-bold text-sky-700 text-center'>✅ Thank you, {$name}!</h2>
        <p class='text-center text-gray-600'>Your booking has been received. Please review your order below and proceed with payment.</p>

        <div class='bg-sky-50 border border-sky-200 rounded-xl p-6 space-y-3'>
          <p><b>Full Name:</b> {$name}</p>
          <p><b>WhatsApp:</b> {$whatsapp}</p>
          <p><b>Activity:</b> {$activity}</p>
          <p><b>Location:</b> {$location}</p>
          <p><b>Preferred Date:</b> {$date}</p>
          <p><b>Certification Level:</b> {$level}</p>
          <p><b>Participants:</b> {$participants}</p>
          <ul class='list-disc list-inside text-gray-700'>{$participantList}</ul>
          <p><b>Additional Notes:</b> " . nl2br(htmlspecialchars($message)) . "</p>
        </div>

        <form action='payment-gateway.php' method='POST'>
          <input type='hidden' name='name' value='{$name}'>
          <input type='hidden' name='whatsapp' value='{$whatsapp}'>
          <input type='hidden' name='activity' value='{$activity}'>
          <input type='hidden' name='location' value='{$location}'>
          <input type='hidden' name='date' value='{$date}'>
          <input type='hidden' name='level' value='{$level}'>
          <input type='hidden' name='participants' value='{$participants}'>
          <input type='hidden' name='message' value='" . htmlspecialchars($message) . "'>

          <button type='submit'
            class='w-full py-3 bg-gradient-to-r from-green-600 to-emerald-500 text-white font-semibold rounded-xl hover:opacity-90 transition-all shadow-md hover:shadow-lg'>
            💳 Pay Now / Bayar Sekarang
          </button>
        </form>

        <p class='text-xs text-center text-gray-500 pt-2'>
          Secured by BaliDiving.com — PADI 5★ Dive Centers | Visit Indonesia
        </p>
      </div>
    </body>
    </html>";
}
?>
