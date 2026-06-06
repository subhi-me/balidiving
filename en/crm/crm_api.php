<?php
// crm_api.php
require_once __DIR__.'/crm_schema.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($action === '') {
    json_headers();
    echo json_encode(['ok' => false, 'error' => 'No action']);
    exit;
}

/* ====== READ LEAD ====== */
if ($action === 'read_lead') {
    json_headers();
    $id = $_GET['id'] ?? '';
    if ($id === '') {
        echo json_encode(['ok'=>false,'error'=>'Invalid ID']);
        exit;
    }

    try {
        $st = $pdo->prepare("SELECT * FROM leads WHERE id = :id");
        $st->execute([':id' => $id]);
        $lead = $st->fetch();
        if (!$lead) {
            echo json_encode(['ok'=>false,'error'=>'Lead not found']);
            exit;
        }

        $th = $pdo->prepare("SELECT * FROM trip_history WHERE lead_id = :id ORDER BY created_at DESC");
        $th->execute([':id' => $id]);
        $trips = $th->fetchAll();

        echo json_encode(['ok'=>true,'data'=>$lead,'trips'=>$trips]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

/* ====== CREATE LEAD ====== */
if ($action === 'create_lead' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        header('Location: index.php');
        exit;
    }

    $id = uid();
    $now = date('Y-m-d H:i:s');

    $data = [
        ':id'     => $id,
        ':column' => 'leads',
        ':name'   => $name,
        ':email'  => trim($_POST['email'] ?? ''),
        ':phone'  => trim($_POST['phone'] ?? ''),
        ':country'=> trim($_POST['country'] ?? ''),
        ':source' => trim($_POST['source'] ?? ''),
        ':package'=> trim($_POST['package'] ?? ''),
        ':cert'   => trim($_POST['cert'] ?? ''),
        ':dive_date' => !empty($_POST['dive_date']) ? $_POST['dive_date'] : null,
        ':pax'    => (int)($_POST['pax'] ?? 0),
        ':budget' => 0,
        ':payment_status' => 'unpaid',
        ':payment_method' => null,
        ':deposit_amount' => 0,
        ':deposit_currency' => 'USD',
        ':deposit_rate'   => null,
        ':booking_stage'  => null,
        ':archived_stage' => null,
        ':created_at'     => $now,
        ':updated_at'     => $now,
    ];

    $sql = "
        INSERT INTO leads (
            id, `column`, name, email, phone, country, source,
            package, cert, dive_date, pax, budget,
            payment_status, payment_method, deposit_amount,
            deposit_currency, deposit_rate, booking_stage, archived_stage,
            created_at, updated_at
        ) VALUES (
            :id, :column, :name, :email, :phone, :country, :source,
            :package, :cert, :dive_date, :pax, :budget,
            :payment_status, :payment_method, :deposit_amount,
            :deposit_currency, :deposit_rate, :booking_stage, :archived_stage,
            :created_at, :updated_at
        )
    ";

    try {
        $pdo->prepare($sql)->execute($data);
    } catch (Throwable $e) {
        // ignore for redirect
    }

    header('Location: index.php');
    exit;
}

/* ====== UPDATE LEAD (partial) ====== */
if ($action === 'update_lead' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_headers();
    $id = $_POST['id'] ?? '';
    if ($id === '') {
        echo json_encode(['ok'=>false,'error'=>'Invalid ID']);
        exit;
    }

    $allowed = [
        'name','email','phone','country','source',
        'package','cert','dive_date','pax','budget',
        'payment_status','payment_method','deposit_amount'
    ];

    $sets = [];
    $params = [':id' => $id, ':u' => date('Y-m-d H:i:s')];

    foreach ($allowed as $k) {
        if (array_key_exists($k, $_POST)) {
            $val = $_POST[$k];
            if ($k === 'pax') {
                $val = (int)$val;
            } elseif (in_array($k, ['budget','deposit_amount'], true)) {
                $val = (float)$val;
            } elseif ($k === 'dive_date') {
                $val = $val !== '' ? $val : null;
            } else {
                $val = trim((string)$val);
            }
            $sets[] = "`{$k}` = :{$k}";
            $params[":{$k}"] = $val;
        }
    }

    if (!$sets) {
        echo json_encode(['ok'=>false,'error'=>'No fields']);
        exit;
    }

    $sql = "UPDATE leads SET ".implode(',', $sets).", updated_at = :u WHERE id = :id";

    try {
        $pdo->prepare($sql)->execute($params);
        echo json_encode(['ok'=>true]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

/* ====== MOVE LEAD (BOARD) ====== */
if ($action === 'move_lead' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_headers();
    $id = $_POST['id'] ?? '';
    $to = $_POST['to'] ?? '';

    $valid = ['leads','contacted','booked','archived'];

    if ($id === '' || !in_array($to, $valid, true)) {
        echo json_encode(['ok'=>false,'error'=>'Invalid input']);
        exit;
    }

    try {
        $st = $pdo->prepare("UPDATE leads SET `column` = :c, updated_at = NOW() WHERE id = :id");
        $st->execute([':c'=>$to, ':id'=>$id]);
        echo json_encode(['ok'=>true]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

/* ====== DELETE LEAD ====== */
if ($action === 'delete_lead' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_headers();
    $id = $_POST['id'] ?? '';
    if ($id === '') {
        echo json_encode(['ok'=>false,'error'=>'Invalid ID']);
        exit;
    }

    try {
        $st = $pdo->prepare("DELETE FROM leads WHERE id = :id");
        $st->execute([':id'=>$id]);
        echo json_encode(['ok'=>true]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

/* ====== SET BOOKING PAYMENT (modal) ====== */
if ($action === 'set_booking_payment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_headers();

    $id = $_POST['id'] ?? '';
    if ($id === '') {
        echo json_encode(['ok'=>false,'error'=>'Invalid lead ID']);
        exit;
    }

    $budget        = isset($_POST['budget']) ? (float)$_POST['budget'] : 0;
    $paymentMethod = trim((string)($_POST['payment_method'] ?? ''));
    $note          = trim((string)($_POST['note'] ?? ''));
    $deposit       = isset($_POST['deposit']) ? (float)$_POST['deposit'] : 0;
    $currency      = strtoupper(trim((string)($_POST['currency'] ?? 'USD')));
    $rate          = isset($_POST['rate']) && $_POST['rate'] !== '' ? (float)$_POST['rate'] : null;

    if ($paymentMethod === '') {
        echo json_encode(['ok'=>false,'error'=>'Payment Method required']);
        exit;
    }

    if (strtolower($paymentMethod) !== 'cash' && $note === '') {
        echo json_encode(['ok'=>false,'error'=>'Note required for non-cash payments']);
        exit;
    }

    if ($currency === 'IDR' && (!$rate || $rate <= 0)) {
        echo json_encode(['ok'=>false,'error'=>'Conversion rate required for IDR']);
        exit;
    }

    try {
        $sql = "
            UPDATE leads
            SET budget          = :budget,
                payment_status  = 'paid',
                payment_method  = :pm,
                deposit_amount  = :deposit,
                deposit_currency= :cur,
                deposit_rate    = :rate,
                updated_at      = NOW()
            WHERE id = :id
        ";
        $pdo->prepare($sql)->execute([
            ':budget' => $budget,
            ':pm'     => $paymentMethod,
            ':deposit'=> $deposit,
            ':cur'    => $currency,
            ':rate'   => $rate,
            ':id'     => $id,
        ]);

        // Simpan note sebagai trip_history extra (tanpa reset booking data)
        if ($note !== '') {
            $st = $pdo->prepare("
                INSERT INTO trip_history
                (lead_id, package, dive_date, pax, budget,
                 payment_status, payment_method, deposit_amount, note, created_at)
                SELECT id, package, dive_date, pax, budget,
                       payment_status, payment_method, deposit_amount, :note, NOW()
                FROM leads
                WHERE id = :id
            ");
            $st->execute([':note'=>$note, ':id'=>$id]);
        }

        echo json_encode(['ok'=>true]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

/* ====== SET BOOKING STAGE (Coming/On Trip/Finish/Reschedule/Cancel) ====== */
if ($action === 'set_booking_stage' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_headers();

    $id    = $_POST['id'] ?? '';
    $stage = strtolower(trim((string)($_POST['stage'] ?? '')));

    $validStages = ['coming','on_trip','finish','reschedule','cancel'];

    if ($id === '' || !in_array($stage, $validStages, true)) {
        echo json_encode(['ok'=>false,'error'=>'Invalid input']);
        exit;
    }

    try {
        // ambil data booking sekarang
        $st = $pdo->prepare("SELECT * FROM leads WHERE id = :id");
        $st->execute([':id' => $id]);
        $lead = $st->fetch();
        if (!$lead) {
            echo json_encode(['ok'=>false,'error'=>'Lead not found']);
            exit;
        }

        // kalau finish/cancel -> pindahkan detail ke trip_history & reset field booking
        if (in_array($stage, ['finish','cancel'], true)) {
            $note = 'Auto snapshot on '.$stage.' stage';

            $ins = $pdo->prepare("
                INSERT INTO trip_history
                (lead_id, package, dive_date, pax, budget,
                 payment_status, payment_method, deposit_amount, note, created_at)
                VALUES
                (:lead_id, :package, :dive_date, :pax, :budget,
                 :p_status, :p_method, :deposit_amount, :note, NOW())
            ");

            $ins->execute([
                ':lead_id'       => $lead['id'],
                ':package'       => $lead['package'] ?? null,
                ':dive_date'     => $lead['dive_date'] ?? null,
                ':pax'           => $lead['pax'] ?? null,
                ':budget'        => $lead['budget'] ?? null,
                ':p_status'      => $lead['payment_status'] ?? null,
                ':p_method'      => $lead['payment_method'] ?? null,
                ':deposit_amount'=> $lead['deposit_amount'] ?? null,
                ':note'          => $note,
            ]);

            // reset detail booking
            $upd = $pdo->prepare("
                UPDATE leads
                SET booking_stage  = :stage,
                    package        = NULL,
                    dive_date      = NULL,
                    pax            = 0,
                    budget         = 0,
                    payment_status = 'unpaid',
                    payment_method = NULL,
                    deposit_amount = 0,
                    updated_at     = NOW()
                WHERE id = :id
            ");
            $upd->execute([':stage'=>$stage, ':id'=>$id]);
        } else {
            $upd = $pdo->prepare("
                UPDATE leads
                SET booking_stage = :stage,
                    updated_at     = NOW()
                WHERE id = :id
            ");
            $upd->execute([':stage'=>$stage, ':id'=>$id]);
        }

        echo json_encode(['ok'=>true]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

/* ====== SET ARCHIVED STAGE (Dive Club/Admin/Void) ====== */
if ($action === 'set_archived_stage' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_headers();

    $id    = $_POST['id'] ?? '';
    $stage = strtolower(trim((string)($_POST['stage'] ?? '')));

    $validStages = ['dive_club','admin','void'];

    if ($id === '' || !in_array($stage, $validStages, true)) {
        echo json_encode(['ok'=>false,'error'=>'Invalid input']);
        exit;
    }

    try {
        $upd = $pdo->prepare("
            UPDATE leads
            SET archived_stage = :stage,
                updated_at     = NOW()
            WHERE id = :id
        ");
        $upd->execute([':stage'=>$stage, ':id'=>$id]);
        echo json_encode(['ok'=>true]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

/* ====== LIST TRIP HISTORY ====== */
if ($action === 'list_trip_history') {
    json_headers();
    $lead_id = $_GET['lead_id'] ?? '';
    if ($lead_id === '') {
        echo json_encode(['ok'=>false,'error'=>'Invalid lead']);
        exit;
    }
    try {
        $st = $pdo->prepare("SELECT * FROM trip_history WHERE lead_id = :id ORDER BY created_at DESC");
        $st->execute([':id'=>$lead_id]);
        echo json_encode(['ok'=>true,'items'=>$st->fetchAll()]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
    }
    exit;
}

/* ====== FALLBACK ====== */
json_headers();
echo json_encode(['ok'=>false,'error'=>'Unknown action']);
