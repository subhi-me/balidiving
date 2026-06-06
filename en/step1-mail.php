<?php
// step1-mail.php — sends Page-1 data immediately
$to = "admin@balidiving.com";

$date     = trim($_POST['date'] ?? '');
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$wa       = trim($_POST['whatsapp'] ?? '');
$method   = trim($_POST['contact_method'] ?? '');
$promo    = trim($_POST['promo'] ?? '');
$activity = trim($_POST['activity'] ?? '');
$location = trim($_POST['location'] ?? '');

$subject = "🟦 STEP 1 — Booking started by {$name} | {$activity}";
$body = "
<html><head><meta charset='utf-8'></head><body style='font-family:Arial,Helvetica,sans-serif'>
  <h2 style='color:#0369a1;margin:0 0 10px'>STEP 1 — Booking Started</h2>
  <p><b>Name:</b> {$name}<br>
  <b>Email:</b> {$email}<br>
  <b>WhatsApp:</b> {$wa}<br>
  <b>Preferred Contact:</b> {$method}<br>
  <b>Promo:</b> " . ($promo ?: "-") . "<br>
  <b>Activity:</b> {$activity}<br>
  <b>Location:</b> {$location}<br>
  <b>Date:</b> {$date}</p>

  <hr>
  <p style='color:#555;font-size:12px'>
    IP: {$_SERVER['REMOTE_ADDR']}<br>
    UA: {$_SERVER['HTTP_USER_AGENT']}<br>
    Time: " . date("Y-m-d H:i:s") . "
  </p>
</body></html>";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: BaliDiving.com <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n";

if (mail($to, $subject, $body, $headers)) {
  http_response_code(200);
  echo "OK";
} else {
  http_response_code(500);
  echo "MAIL_FAIL";
}
