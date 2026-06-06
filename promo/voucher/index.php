<?php
// voucher-redeem.php — Final Version with Thank You Modal

function clean_param($key) {
    if (!isset($_GET[$key])) return '';
    $val = $_GET[$key];
    $loops = 0;
    while ($loops < 3 && preg_match('/%[0-9A-Fa-f]{2}/', $val)) {
        $val = urldecode($val);
        $loops++;
    }
    $val = preg_replace('/^\s*[\{\[]?\s*(.*?)\s*[\}\]]?\s*$/', '$1', $val);
    return trim(preg_replace('/\s+/', ' ', $val));
}
function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$version   = clean_param('v');
$name      = clean_param('n');
$email     = clean_param('e');
$whatsapp  = clean_param('wa');

$today   = new DateTime('today');
$expiry  = (clone $today)->modify('+2 years');
$discount = is_numeric($version) ? (int)$version : 0;
$refCode  = 'BD-'.str_pad((string)random_int(0,99999), 5, '0', STR_PAD_LEFT);

$submitted = ($_SERVER['REQUEST_METHOD'] === 'POST');
$errors = [];
$success = false;
$mail_error = '';

if ($submitted) {
    $friend_name  = trim($_POST['friend_name'] ?? '');
    $friend_email = trim($_POST['friend_email'] ?? '');
    $friend_wa    = trim($_POST['friend_wa'] ?? '');

    if ($friend_email !== '' && !filter_var($friend_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Friend email is invalid.";
    }

    if (empty($errors)) {
        $to = 'admin@balidiving.com';
        $subject = "Voucher Redemption ({$discount}% Off) — {$name} — {$refCode}";
        $fromEmail = 'no-reply@balidiving.com';
        $replyTo   = ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : $fromEmail;

        $isGift = ($friend_name !== '' || $friend_email !== '' || $friend_wa !== '');
        $gift_html = $isGift ? "
            <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Gifted?</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>Yes</td></tr>
            <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Recipient Name</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>".esc($friend_name)."</td></tr>
            <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Recipient Email</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>".esc($friend_email)."</td></tr>
            <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Recipient WhatsApp</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>".esc($friend_wa)."</td></tr>
        " : "
            <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Gifted?</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>No</td></tr>
        ";

        $ip   = $_SERVER['REMOTE_ADDR'] ?? '-';
        $ua   = $_SERVER['HTTP_USER_AGENT'] ?? '-';
        $when = (new DateTime('now'))->format('Y-m-d H:i:s');

        // Light Mode Email Template
        $html = "
        <div style='font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;background:#f8fafc;padding:20px;color:#0f172a;'>
          <div style='max-width:720px;margin:auto;background:white;border-radius:12px;box-shadow:0 3px 12px rgba(0,0,0,0.08);overflow:hidden'>
            <div style='background:#06b6d4;padding:16px 20px;color:white'>
              <h2 style='margin:0;font-size:22px'>Bali Diving Voucher Redemption</h2>
              <p style='margin:4px 0 0;font-size:14px;opacity:.9'>Reference: <strong>{$refCode}</strong> • Received: {$when}</p>
            </div>

            <div style='padding:20px'>
              <p style='color:#475569;font-size:14px;margin-bottom:10px'>
                Redemption was submitted early to secure the promo benefit. Bali Diving will review and confirm via email.
              </p>
              <table width='100%' cellspacing='0' cellpadding='0' style='border-collapse:collapse;font-size:15px;'>
                <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Name</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>".esc($name)."</td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Email</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>".esc($email)."</td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>WhatsApp</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>".esc($whatsapp)."</td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Discount</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'><strong>{$discount}%</strong></td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #e2e8f0;color:#334155;'>Expiry</td><td style='padding:8px;border-bottom:1px solid #e2e8f0;'>".esc($expiry->format('F j, Y'))." (2 years)</td></tr>
                {$gift_html}
              </table>

              <div style='margin-top:20px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:10px 14px;color:#475569;font-size:13px'>
                <strong>Meta Info:</strong> IP: {$ip} • User Agent: ".esc($ua)."
              </div>

              <div style='margin-top:24px'>
                <h3 style='margin:0 0 8px;font-size:16px;color:#0e7490'>Terms & Conditions</h3>
                <ul style='margin:0 0 6px 20px;padding:0;font-size:14px;color:#475569'>
                  <li>This promo is valid only for the person named above; if gifted, it is valid only for the designated recipient.</li>
                  <li>This promo can only be used through the official website: <a href=\"https://balidiving.com\" style=\"color:#0284c7;text-decoration:none;\">https://balidiving.com</a>.</li>
                  <li>To use this promo together with another promotion, please contact <a href=\"mailto:customer.service@balidiving.com\" style=\"color:#0284c7;text-decoration:none;\">customer.service@balidiving.com</a>.</li>
                  <li>Redemption should be done early to lock in your benefit; otherwise, the voucher may not be redeemable later.</li>
                  <li>balidiving.com reserves the right to decline the redemption in case of fraud, misuse, or policy violations.</li>
                  <li>Voucher expiry: ".esc($expiry->format('F j, Y'))." (2 years from the date of request).</li>
                  <li>We will notify you before the voucher is used.</li>
                </ul>
              </div>
            </div>

            <div style='background:#e0f2fe;padding:12px 16px;text-align:center;font-size:13px;color:#0369a1'>
              Bali Diving • www.balidiving.com
            </div>
          </div>
        </div>";

        // send email
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Bali Diving <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$replyTo}\r\n";

        $sent = @mail($to, $subject, $html, $headers);
        if ($sent) $success = true;
        else $mail_error = "Email sending failed — please contact us manually.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Voucher Redemption — Bali Diving</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root {
  --bg:#0f172a;--card:#111827;--text:#e5e7eb;--muted:#94a3b8;
  --accent:#06b6d4;--ok:#22c55e;--err:#ef4444;--radius:14px;
}
body{margin:0;background:linear-gradient(180deg,#0b1220,#0f172a);color:var(--text);
font:16px/1.5 system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,Arial;}
.wrap{max-width:820px;margin:40px auto;padding:24px}
.card{background:rgba(17,24,39,.6);border:1px solid rgba(255,255,255,.08);
backdrop-filter:blur(6px);border-radius:var(--radius);padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.4)}
h1{font-size:26px;margin:0 0 8px}
p.lead{color:var(--muted);margin:0 0 18px}
form{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.full{grid-column:1 / -1}
label{font-size:14px;font-weight:600;color:#d1d5db;margin-bottom:4px;display:block}
input,button{width:100%;padding:11px 12px;border-radius:10px;
border:1px solid rgba(255,255,255,.12);background:#0b1220;color:var(--text);}
input[readonly]{background:#1e293b;color:#9ca3af}
button{cursor:pointer;border:none;background:linear-gradient(180deg,#06b6d4,#0891b2);
font-weight:700;color:white;padding:12px 16px;border-radius:10px}
.alert{margin:16px 0;padding:12px 14px;border-radius:10px}
.alert.ok{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3)}
.alert.err{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3)}
.note{background:rgba(6,182,212,.1);border:1px solid rgba(6,182,212,.3);padding:14px 16px;
border-radius:12px;margin-bottom:18px}
small.muted{color:var(--muted)}
.modal{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.9);
display:flex;align-items:center;justify-content:center;z-index:9999;}
.modal-content{background:#ffffff;border-radius:14px;max-width:420px;text-align:center;
padding:30px 24px;color:#0f172a;box-shadow:0 8px 30px rgba(0,0,0,.6);}
.modal-content h2{margin:0 0 10px;color:#06b6d4;}
.modal-content p{color:#334155;font-size:15px;margin-bottom:20px;}
.modal-content a{display:inline-block;background:#06b6d4;color:white;text-decoration:none;
padding:10px 20px;border-radius:8px;font-weight:600;}
@media(max-width:700px){form{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">
<div class="card">
  <h1>Redeem Your Bali Diving Voucher</h1>
  <p class="lead">Your details are prefilled. You may optionally gift it to a friend.</p>

  <div class="note">
    <strong>Important:</strong> Redemption needs to be done early to lock in your benefit.  
    Otherwise, the voucher may not be redeemable later.
    <br><br>
    This voucher expires in <strong>2 years</strong> (until <strong><?= esc($expiry->format('F j, Y')); ?></strong>).  
    Your discount: <strong><?= esc($discount); ?>%</strong>.
    <br><small class="muted">Reference: <?= esc($refCode) ?></small>
  </div>

  <?php if ($submitted && (!empty($errors) || $mail_error)): ?>
    <div class="alert err">
      <strong>Unable to submit:</strong>
      <ul style="margin:6px 0 0 20px">
        <?php foreach($errors as $e): ?><li><?= esc($e); ?></li><?php endforeach; ?>
        <?php if ($mail_error): ?><li><?= esc($mail_error); ?></li><?php endif; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" id="redeemForm">
    <div>
      <label>Full Name</label>
      <input type="text" name="full_name" readonly value="<?= esc($name); ?>">
    </div>
    <div>
      <label>Email</label>
      <input type="email" name="email" readonly value="<?= esc($email); ?>">
    </div>
    <div>
      <label>WhatsApp Number</label>
      <input type="text" name="whatsapp" readonly value="<?= esc($whatsapp); ?>">
    </div>
    <div>
      <label>Discount (%)</label>
      <input type="text" readonly value="<?= esc($discount); ?>%">
    </div>

    <div class="full" style="margin-top:8px">
      <h3 style="margin:0 0 6px;color:#22d3ee">🎁 Gift to a Friend (Optional)</h3>
      <small class="muted">If you fill these fields, the promo will be valid for the recipient only.</small>
    </div>

    <div>
      <label>Recipient Name</label>
      <input type="text" name="friend_name" placeholder="Friend’s full name" value="<?= esc($_POST['friend_name'] ?? ''); ?>">
    </div>
    <div>
      <label>Recipient Email</label>
      <input type="email" name="friend_email" placeholder="friend@example.com" value="<?= esc($_POST['friend_email'] ?? ''); ?>">
    </div>
    <div>
      <label>Recipient WhatsApp</label>
      <input type="text" name="friend_wa" placeholder="+62…" value="<?= esc($_POST['friend_wa'] ?? ''); ?>">
    </div>

    <div class="full" style="text-align:right;margin-top:10px">
      <button type="submit">Submit Redemption</button>
    </div>
  </form>

  <div style="margin-top:18px;background:#0b1220;border:1px solid rgba(255,255,255,.06);border-radius:12px;padding:12px 14px">
    <h3 style="margin:0 0 8px;font-size:16px;color:#22d3ee">Terms & Conditions</h3>
    <ul style="margin:0 0 6px 18px;padding:0">
      <li>This promo is valid only for the person named on the voucher; if gifted, it is valid only for the designated recipient.</li>
      <li>This promo can only be used through the official website: <a href="https://balidiving.com" style="color:#22d3ee;">https://balidiving.com</a>.</li>
      <li>To use this promo together with another promotion, please contact <a href="mailto:customer.service@balidiving.com" style="color:#22d3ee;">customer.service@balidiving.com</a>.</li>
      <li>Redemption should be done early to lock in your benefit; otherwise, the voucher may not be redeemable later.</li>
      <li>balidiving.com reserves the right to decline the redemption in case of fraud, misuse, or policy violations.</li>
      <li>Voucher expiry: <?= esc($expiry->format('F j, Y')); ?> (2 years from the date of request).</li>
      <li>We will notify you before the voucher is used.</li>
    </ul>
  </div>
</div>
</div>

<?php if ($success): ?>
<div class="modal" id="thankyouModal">
  <div class="modal-content">
    <h2>Thank You!</h2>
    <p>Your redemption request has been received.<br>
    We’ll notify you before your voucher is used.</p>
    <a href="https://balidiving.com">← Back to BaliDiving.com</a>
  </div>
</div>
<script>
// Prevent closing modal by click outside or ESC
document.addEventListener('keydown', e => { e.preventDefault(); });
document.getElementById('thankyouModal').addEventListener('click', e => e.stopPropagation());
</script>
<?php endif; ?>
</body>
</html>
