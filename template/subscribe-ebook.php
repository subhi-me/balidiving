<?php
// subscribe-ebook.php
date_default_timezone_set('Asia/Makassar');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Bisa diarahkan ke halaman error sederhana
        header('Location: /download-error.html');
        exit;
    }

    // ===== EMAIL SETUP =====
    $to      = $email;
    $subject = 'Your Bali Scuba Diving Guide (Download Inside)';

    // Ganti URL ebook sesuai lokasi file asli di server Anda
    $ebookUrl = 'https://balidiving.com/files/Scuba-Diving-Guide-Bali.pdf';

    // WhatsApp number (sudah Anda berikan)
    $waNumber  = '6287861190174';
    $waLink    = 'https://wa.me/' . $waNumber . '?text=' . urlencode('Hi, I just downloaded the Bali scuba diving guide and I have a question.');

    // HTML Email Body
    $message = '
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Your Bali Scuba Diving Guide</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color:#f5f5f5; padding:24px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden;">
        <tr>
          <td style="padding:24px 24px 8px 24px; text-align:left;">
            <h2 style="margin:0 0 12px 0; font-size:22px; color:#111827;">
              Thank you for downloading our e-book!
            </h2>
            <p style="margin:0 0 16px 0; font-size:14px; color:#4b5563;">
              Hi,
            </p>
            <p style="margin:0 0 16px 0; font-size:14px; color:#4b5563;">
              Thank you for downloading <strong>“Scuba Diving Guide – Bali”</strong>.
              We are happy to share this guide with you and we hope it helps you
              plan a safe, enjoyable, and unforgettable diving experience in Bali.
            </p>
            <p style="margin:0 0 16px 0; font-size:14px; color:#4b5563;">
              You can download your e-book using the button below:
            </p>

            <p style="margin:0 0 24px 0; text-align:center;">
              <a href="' . $ebookUrl . '" 
                 style="display:inline-block; padding:12px 24px; background:#111827; color:#ffffff; text-decoration:none; border-radius:999px; font-size:14px;">
                 Download E-Book
              </a>
            </p>

            <hr style="border:none; border-top:1px solid #e5e7eb; margin:0 0 24px 0;">

            <p style="margin:0 0 12px 0; font-size:14px; color:#4b5563;">
              If you have any questions about diving in Bali or would like
              a personal recommendation, feel free to contact us on WhatsApp:
            </p>

            <p style="margin:0 0 24px 0; text-align:center;">
              <a href="' . $waLink . '" 
                 style="display:inline-block; padding:10px 20px; background:#25D366; color:#ffffff; text-decoration:none; border-radius:999px; font-size:14px;">
                 Chat via WhatsApp
              </a>
            </p>

            <p style="margin:0 0 6px 0; font-size:13px; color:#9ca3af;">
              Safe bubbles and warm greetings from Bali,
            </p>
            <p style="margin:0; font-size:13px; color:#4b5563;">
              Bali Diving Team
            </p>
          </td>
        </tr>
      </table>
    </body>
    </html>
    ';

    // Headers for HTML email
    $headers   = "MIME-Version: 1.0\r\n";
    $headers  .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers  .= "From: Bali Diving <no-reply@balidiving.com>\r\n";

    // Send the email
    @mail($to, $subject, $message, $headers);

    // Redirect ke halaman terimakasih / download success
    header('Location: /thank-you-ebook.html');
    exit;
} else {
    // Kalau akses langsung tanpa POST
    header('Location: /');
    exit;
}
