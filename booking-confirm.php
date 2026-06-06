<?php
// booking-confirm.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

// DEBUG: kalau ada error jangan blank
ini_set('display_errors', '1');
error_reporting(E_ALL);

include('../template/database/main.php'); // $pdo, $USD_TO_IDR

if (!isset($USD_TO_IDR) || !is_numeric($USD_TO_IDR)) {
    $USD_TO_IDR = 16000;
}

/* ---------- Helpers ---------- */
function normalize_phone(string $phone): string {
    $phone = trim($phone);
    if ($phone === '') return '';
    $phone = preg_replace('~[^0-9\+]~', '', $phone);
    if ($phone === '') return '';

    if ($phone[0] === '+') return $phone;
    if (strpos($phone, '62') === 0) return '+'.$phone;
    if ($phone[0] === '0') return '+62'.substr($phone, 1);
    return '+'.$phone;
}

function mask_phone(string $phone): string {
    $len = strlen($phone);
    if ($len <= 4) return $phone;
    return str_repeat('•', max(0, $len - 4)) . substr($phone, -4);
}

function lead_table_has_pin(PDO $pdo): bool {
    static $has = null;
    if ($has !== null) return $has;
    try {
        $st = $pdo->query("
            SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = 'leads'
              AND column_name = 'customer_pin'
            LIMIT 1
        ");
        $has = (bool)$st->fetchColumn();
    } catch (Throwable $e) {
        $has = false;
    }
    return $has;
}

/* ---------- Load global add-ons (optional) ---------- */
$globalAddons = [];
try {
    $st = $pdo->query("SELECT global_template FROM booking_globals WHERE id=1");
    if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $tpl = $row['global_template'] ? json_decode($row['global_template'], true) : null;
        if (is_array($tpl) && isset($tpl['addons']) && is_array($tpl['addons'])) {
            foreach ($tpl['addons'] as $ad) {
                if (empty($ad['name'])) continue;
                if (isset($ad['available']) && $ad['available'] === false) continue;
                $globalAddons[] = [
                    'name' => $ad['name'],
                    'usd'  => isset($ad['usd']) ? (float)$ad['usd'] : 0
                ];
            }
        }
    }
} catch (Throwable $e) {
    $globalAddons = [];
}

/* =========================================================
   MAIN FLOW
   --------------------------------------------------------- */

$errors  = [];
$success = false;
$phase   = 'review'; // 'review' atau 'done'

// Data booking dasar dari snorkeling.php
$activity     = $_POST['activity']      ?? 'snorkeling';
$locationKey  = $_POST['location_key']  ?? '';
$locationName = $_POST['location_name'] ?? '';
$baseUsd      = isset($_POST['base_usd']) ? (float)$_POST['base_usd'] : 0.0;
$dateStr      = trim($_POST['selected_date'] ?? '');
$rawContact   = trim($_POST['contact'] ?? '');

// Data form detail (step 2)
$fullName        = trim($_POST['full_name']  ?? '');
$phoneInputForm  = trim($_POST['phone']      ?? '');
$country         = trim($_POST['country']    ?? '');
$pax             = isset($_POST['pax']) ? (int)$_POST['pax'] : 0;
$notes           = trim($_POST['notes']      ?? '');
$secret          = trim($_POST['verify_secret'] ?? '');
$addonsSel       = $_POST['addons'] ?? [];
$leadId          = $_POST['lead_id'] ?? null;

// Flag existing lead
$existingLead    = false;
$maskedPhone     = '';
$storedPhoneRaw  = '';
$storedPhoneNorm = '';
$hasPin          = lead_table_has_pin($pdo);
$storedPin       = null;

// Kontak: bisa email atau phone
$email              = '';
$phoneNormForLookup = '';
$contactIsEmail     = false;

// Deteksi: kalau rawContact berisi @ dan valid → email
if ($rawContact !== '' && strpos($rawContact, '@') !== false && filter_var($rawContact, FILTER_VALIDATE_EMAIL)) {
    $email          = strtolower($rawContact);
    $contactIsEmail = true;
}

// Phone untuk lookup
if ($phoneInputForm !== '') {
    $phoneNormForLookup = normalize_phone($phoneInputForm);
} elseif (!$contactIsEmail && $rawContact !== '') {
    $phoneInputForm     = $rawContact;
    $phoneNormForLookup = normalize_phone($rawContact);
}

$leadEmailForDisplay = $email; // untuk summary

// Step detection
$isConfirm = isset($_POST['confirm_booking']);

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errors[] = 'This page should be accessed from the snorkeling page.';
} else {
    // Validasi dasar
    if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', $dateStr)) {
        $errors[] = 'Invalid date.';
    }

    if ($email === '' && $phoneNormForLookup === '') {
        $errors[] = 'Please provide a valid email or phone / WhatsApp.';
    }

    if ($locationKey === '' || $locationName === '') {
        $errors[] = 'Missing location information.';
    }

    // Cari lead berdasar email ATAU phone
    if (!$errors) {
        try {
            $where  = [];
            $params = [];

            if ($email !== '') {
                $where[]          = 'email = :email';
                $params[':email'] = $email;
            }
            if ($phoneNormForLookup !== '') {
                $where[]                = 'phone = :phone_norm';
                $params[':phone_norm']  = $phoneNormForLookup;
            }

            if ($where) {
                if ($hasPin) {
                    $sql = "
                        SELECT id, name, email, phone, country, pax, customer_pin
                        FROM leads
                        WHERE " . implode(' OR ', $where) . "
                        ORDER BY created_at DESC
                        LIMIT 1
                    ";
                } else {
                    $sql = "
                        SELECT id, name, email, phone, country, pax
                        FROM leads
                        WHERE " . implode(' OR ', $where) . "
                        ORDER BY created_at DESC
                        LIMIT 1
                    ";
                }

                $st = $pdo->prepare($sql);
                $st->execute($params);

                if ($leadRow = $st->fetch(PDO::FETCH_ASSOC)) {
                    $existingLead = true;
                    $leadId       = $leadRow['id'];

                    if ($fullName === '')  $fullName  = $leadRow['name']    ?? '';
                    if ($leadEmailForDisplay === '' && !empty($leadRow['email'])) {
                        $leadEmailForDisplay = strtolower($leadRow['email']);
                    }
                    if ($phoneInputForm === '' && !empty($leadRow['phone'])) {
                        $phoneInputForm = $leadRow['phone'];
                    }
                    if ($country === '')   $country   = $leadRow['country'] ?? '';
                    if ($pax <= 0)         $pax       = (int)($leadRow['pax'] ?? 2);

                    $storedPhoneRaw  = $leadRow['phone'] ?? '';
                    $storedPhoneNorm = $storedPhoneRaw ? normalize_phone($storedPhoneRaw) : '';
                    $maskedPhone     = $storedPhoneNorm ? mask_phone($storedPhoneNorm) : '';

                    if ($hasPin && isset($leadRow['customer_pin'])) {
                        $storedPin = $leadRow['customer_pin'];
                    }
                } else {
                    $existingLead = false;
                    if ($pax <= 0) $pax = 2;
                }
            } else {
                if ($pax <= 0) $pax = 2;
            }
        } catch (Throwable $e) {
            $errors[] = 'Database error while loading your data.';
        }
    }

    // Step confirm: simpan booking
    if (!$errors && $isConfirm) {
        if ($fullName === '')        $errors[] = 'Please fill your name.';
        if ($phoneInputForm === '')  $errors[] = 'Please fill your phone / WhatsApp.';
        if ($pax <= 0)               $errors[] = 'Number of participants must be at least 1.';

        $normPhone = normalize_phone($phoneInputForm);

        // Verifikasi existing lead
        if ($existingLead) {
            if ($secret === '') {
                $errors[] = 'Please type your phone number or PIN to confirm it is you.';
            } else {
                $normInput = normalize_phone($secret);
                $phoneOk   = ($storedPhoneNorm && $normInput === $storedPhoneNorm);
                $pinOk     = ($storedPin !== null && $storedPin !== '' && $secret === $storedPin);
                if (!$phoneOk && !$pinOk) {
                    $errors[] = 'Verification failed. Please check your phone number / PIN.';
                }
            }
        }

        if (!$errors) {
            $now     = date('Y-m-d H:i:s');
            $package = $activity . ' - ' . $locationName;

            // Email yang disimpan
            $storeEmail = $leadEmailForDisplay ?: ($contactIsEmail ? $email : '');

            try {
                $pdo->beginTransaction();

                if ($existingLead && $leadId) {
                    $upd = $pdo->prepare("
                        UPDATE leads
                        SET name = :name,
                            email = :email,
                            phone = :phone,
                            country = :country,
                            pax = :pax,
                            package = :pkg,
                            dive_date = :dive_date,
                            updated_at = :u
                        WHERE id = :id
                    ");
                    $upd->execute([
                        ':name'      => $fullName,
                        ':email'     => $storeEmail,
                        ':phone'     => $normPhone,
                        ':country'   => $country,
                        ':pax'       => $pax,
                        ':pkg'       => $package,
                        ':dive_date' => $dateStr,
                        ':u'         => $now,
                        ':id'        => $leadId
                    ]);
                } else {
                    // generate string ID
                    $leadId = 'LD-' . bin2hex(random_bytes(6));
                    $ins = $pdo->prepare("
                        INSERT INTO leads(
                            id, `column`, name, email, phone, country,
                            package, dive_date, pax, created_at, updated_at
                        ) VALUES(
                            :id, 'leads', :name, :email, :phone, :country,
                            :pkg, :dive_date, :pax, :c, :u
                        )
                    ");
                    $ins->execute([
                        ':id'        => $leadId,
                        ':name'      => $fullName,
                        ':email'     => $storeEmail,
                        ':phone'     => $normPhone,
                        ':country'   => $country,
                        ':pkg'       => $package,
                        ':dive_date' => $dateStr,
                        ':pax'       => $pax,
                        ':c'         => $now,
                        ':u'         => $now
                    ]);
                    $existingLead = true;
                }

                // Hitung & simpan addons ke note
                $selectedAddons = [];
                $totalAddonUsd  = 0;
                foreach ($addonsSel as $idx) {
                    $i = (int)$idx;
                    if (isset($globalAddons[$i])) {
                        $selectedAddons[] = $globalAddons[$i];
                        $totalAddonUsd   += (float)$globalAddons[$i]['usd'];
                    }
                }

                $noteParts = [];
                $noteParts[] = "Activity: {$activity}";
                $noteParts[] = "Location: {$locationName} ({$locationKey})";
                $noteParts[] = "Date: {$dateStr}";
                $noteParts[] = "Base price USD: {$baseUsd}";
                $noteParts[] = "Add-ons total USD: {$totalAddonUsd}";
                if ($selectedAddons) {
                    $names = array_map(fn($a)=>$a['name'], $selectedAddons);
                    $noteParts[] = "Add-ons: ".implode(', ', $names);
                }
                if ($notes !== '') {
                    $noteParts[] = "Guest notes: ".$notes;
                }
                $noteFull = implode(" | ", $noteParts);

                // trip_history (optional)
                try {
                    $insTrip = $pdo->prepare("
                        INSERT INTO trip_history(
                            lead_id, package, dive_date, pax, budget,
                            payment_status, payment_method, deposit_amount, note, created_at
                        ) VALUES(
                            :lead_id, :pkg, :dive_date, :pax, :budget,
                            'unpaid', NULL, 0, :note, :c
                        )
                    ");
                    $insTrip->execute([
                        ':lead_id'   => $leadId,
                        ':pkg'       => $package,
                        ':dive_date' => $dateStr,
                        ':pax'       => $pax,
                        ':budget'    => $baseUsd,
                        ':note'      => $noteFull,
                        ':c'         => $now
                    ]);
                } catch (Throwable $e) {
                    // kalau tidak ada tabel trip_history, skip
                }

                $pdo->commit();
                $success = true;
                $phase   = 'done';
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $errors[] = 'Failed to save your booking. Please try again later.';
            }
        }
    }
}

if (!$success) {
    $phase = 'review';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Review your booking · Bali Diving</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-50">
  <div class="max-w-3xl mx-auto px-4 py-6">
    <header class="mb-6 border-b border-slate-800 pb-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl font-semibold">Review your booking</h1>
        <p class="text-sm text-slate-400">
          Snorkeling request • Bali Diving
        </p>
      </div>
      <a href="snorkeling.php" class="text-xs text-sky-400 hover:underline">
        ← Back to snorkeling page
      </a>
    </header>

    <?php if ($errors): ?>
      <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-100">
        <p class="font-semibold mb-1">We hit a small problem:</p>
        <ul class="list-disc list-inside space-y-1">
          <?php foreach ($errors as $e): ?>
            <li><?=htmlspecialchars($e, ENT_QUOTES, 'UTF-8')?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($phase === 'done' && !$errors): ?>
      <section class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Thank you! 🎉</h2>
        <p class="text-sm text-emerald-100">
          We’ve received your request and will get back to you shortly with availability and next steps.
        </p>
      </section>

      <section class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 text-sm space-y-2">
        <h3 class="font-semibold mb-2">Summary</h3>
        <p><span class="text-slate-400">Name:</span> <?=htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8')?></p>
        <p><span class="text-slate-400">Contact:</span>
          <?=htmlspecialchars($leadEmailForDisplay ?: $phoneInputForm ?: $rawContact, ENT_QUOTES, 'UTF-8')?>
        </p>
        <p><span class="text-slate-400">Activity:</span> <?=htmlspecialchars($activity, ENT_QUOTES, 'UTF-8')?> – <?=htmlspecialchars($locationName, ENT_QUOTES, 'UTF-8')?></p>
        <p><span class="text-slate-400">Date:</span> <?=htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8')?></p>
        <p><span class="text-slate-400">Participants:</span> <?=$pax?></p>
        <p><span class="text-slate-400">Base price (per person):</span>
          $<?=number_format($baseUsd,0)?> (approx IDR <?=number_format($baseUsd*$USD_TO_IDR,0,',','.')?>)
        </p>
        <?php if (!empty($addonsSel) && !empty($globalAddons)): ?>
          <p><span class="text-slate-400">Add-ons:</span>
            <?php
              $names = [];
              foreach ($addonsSel as $idx) {
                $i = (int)$idx;
                if (isset($globalAddons[$i])) $names[] = $globalAddons[$i]['name'];
              }
              echo htmlspecialchars(implode(', ', $names), ENT_QUOTES, 'UTF-8');
            ?>
          </p>
        <?php endif; ?>
        <?php if ($notes !== ''): ?>
          <p><span class="text-slate-400">Your notes:</span> <?=nl2br(htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'))?></p>
        <?php endif; ?>
      </section>

      <div class="mt-4 text-xs text-slate-500">
        You can safely close this page. If you don’t hear from us, please reach out via WhatsApp on our website.
      </div>
    <?php else: ?>
      <section class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 mb-6 text-sm">
        <h2 class="text-base font-semibold mb-3">Trip overview</h2>
        <div class="grid md:grid-cols-2 gap-3">
          <div>
            <p><span class="text-slate-400">Activity:</span> <?=htmlspecialchars($activity, ENT_QUOTES, 'UTF-8')?></p>
            <p><span class="text-slate-400">Location:</span> <?=htmlspecialchars($locationName, ENT_QUOTES, 'UTF-8')?> (<?=htmlspecialchars($locationKey, ENT_QUOTES, 'UTF-8')?>)</p>
            <p><span class="text-slate-400">Date:</span> <?=htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8')?></p>
          </div>
          <div>
            <p><span class="text-slate-400">Contact:</span>
              <?=htmlspecialchars($leadEmailForDisplay ?: $phoneInputForm ?: $rawContact, ENT_QUOTES, 'UTF-8')?>
            </p>
            <p>
              <span class="text-slate-400">Base price (per person):</span>
              $<?=number_format($baseUsd,0)?> · approx IDR <?=number_format($baseUsd*$USD_TO_IDR,0,',','.')?>
            </p>
            <p>
              <?php if ($existingLead): ?>
                <span class="text-emerald-400">We found your previous booking data with this contact.</span>
              <?php else: ?>
                <span class="text-sky-400">First time booking with this contact – we’ll remember your details for next time.</span>
              <?php endif; ?>
            </p>
          </div>
        </div>
      </section>

      <form method="post" class="space-y-4">
        <!-- hidden base data -->
        <input type="hidden" name="confirm_booking" value="1">
        <input type="hidden" name="activity"      value="<?=htmlspecialchars($activity, ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="location_key"  value="<?=htmlspecialchars($locationKey, ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="location_name" value="<?=htmlspecialchars($locationName, ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="base_usd"      value="<?=htmlspecialchars((string)$baseUsd, ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="selected_date" value="<?=htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="contact"       value="<?=htmlspecialchars($rawContact, ENT_QUOTES, 'UTF-8')?>">
        <?php if ($leadId): ?>
          <input type="hidden" name="lead_id" value="<?=htmlspecialchars($leadId, ENT_QUOTES, 'UTF-8')?>">
        <?php endif; ?>

        <section class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
          <h3 class="text-base font-semibold mb-3">Guest details</h3>
          <div class="space-y-3">
            <div>
              <label class="block text-slate-300 mb-1">Full name</label>
              <input type="text" name="full_name"
                     value="<?=htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8')?>"
                     class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
            </div>
            <div>
              <label class="block text-slate-300 mb-1">Phone / WhatsApp</label>
              <input type="text" name="phone"
                     value="<?=htmlspecialchars($phoneInputForm, ENT_QUOTES, 'UTF-8')?>"
                     class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
            </div>
            <div>
              <label class="block text-slate-300 mb-1">Country of residence</label>
              <input type="text" name="country"
                     value="<?=htmlspecialchars($country, ENT_QUOTES, 'UTF-8')?>"
                     class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
            </div>
            <div>
              <label class="block text-slate-300 mb-1">Number of participants</label>
              <input type="number" min="1" step="1" name="pax"
                     value="<?=$pax > 0 ? $pax : 2?>"
                     class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
            </div>
          </div>
        </section>

        <section class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
          <h3 class="text-base font-semibold mb-3">Add-ons (optional)</h3>
          <div class="space-y-2">
            <?php if (!$globalAddons): ?>
              <p class="text-slate-400 text-xs">No add-ons available for this activity right now.</p>
            <?php else: ?>
              <?php foreach ($globalAddons as $i => $ad):
                $usd = (float)$ad['usd'];
                $idr = $usd * $USD_TO_IDR;
                $checked = in_array((string)$i, (array)$addonsSel, true);
              ?>
                <label class="flex items-center gap-2">
                  <input type="checkbox" name="addons[]" value="<?=$i?>" <?=$checked ? 'checked' : ''?>>
                  <span>
                    <?=htmlspecialchars($ad['name'], ENT_QUOTES, 'UTF-8')?>
                    <span class="text-xs text-slate-400">
                      ($<?=number_format($usd,0)?> · IDR <?=number_format($idr,0,',','.')?>)
                    </span>
                  </span>
                </label>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>

        <section class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
          <h3 class="text-base font-semibold mb-3">Anything we should know?</h3>
          <textarea
            name="notes"
            rows="3"
            class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500"
            placeholder="Flight times, hotel area, special requests, non-swimmers, kids, etc."
          ><?=htmlspecialchars($notes, ENT_QUOTES, 'UTF-8')?></textarea>
        </section>

        <?php if ($existingLead): ?>
          <section class="rounded-xl border border-amber-500/40 bg-amber-500/10 p-4 text-sm">
            <h3 class="text-base font-semibold mb-2">Confirm it’s really you</h3>
            <p class="text-xs text-amber-100 mb-2">
              We recognise your contact from previous bookings. To protect your data, please type the phone number you used before
              (<?=$maskedPhone ? 'ends with '.$maskedPhone : 'the same number you used previously'?>).
              Format is flexible: 08…, 62…, or +62….
            </p>
            <?php if ($hasPin): ?>
              <p class="text-xs text-amber-200 mb-2">
                If you have a customer PIN with us, you can also type that instead of your phone.
              </p>
            <?php endif; ?>
            <input
              type="text"
              name="verify_secret"
              value="<?=htmlspecialchars($secret, ENT_QUOTES, 'UTF-8')?>"
              class="w-full rounded-md border border-amber-500/60 bg-slate-950 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-400"
              placeholder="Type your phone number or PIN here"
            >
          </section>
        <?php endif; ?>

        <div class="flex items-center justify-between pt-2 text-sm">
          <a href="snorkeling.php" class="text-xs text-slate-400 hover:text-slate-200 hover:underline">
            ← Change trip date or location
          </a>
          <button
            type="submit"
            class="inline-flex items-center gap-2 rounded-md bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-sky-400"
          >
            <span>Send booking request</span>
          </button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
