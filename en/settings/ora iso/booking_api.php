<?php
declare(strict_types=1);

/**
 * booking_api.php
 * Dipakai sebagai:
 * - library (di-include di index.php)
 * - endpoint API (?action=...)
 *
 * Penting:
 * - Kalau TIDAK ada ?action => return; (jangan exit!)
 */

require_once __DIR__.'/booking_config.php';
require_once __DIR__.'/booking_migrations.php';

/* Kalau tidak ada action, artinya dipanggil dari index untuk render HTML */
if (!isset($_GET['action']) || $_GET['action'] === '') {
    return;
}

/* Mulai mode API */
header('Content-Type: application/json; charset=utf-8');

if (!empty($db_error)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'DB error: '.$db_error,
    ]);
    exit;
}

$pdo    = db();
$action = $_GET['action'];

/**
 * Helper: ambil globals
 */
function booking_get_globals(PDO $pdo): array
{
    $row = $pdo->query("SELECT * FROM booking_globals ORDER BY id DESC LIMIT 1")->fetch();
    if (!$row) {
        return [
            'usd_to_idr' => 16000,
            'rate_mode'  => 'manual',
            'updated_at' => now(),
        ];
    }
    return [
        'usd_to_idr' => (float)$row['usd_to_idr'],
        'rate_mode'  => $row['rate_mode'],
        'updated_at' => $row['updated_at'],
    ];
}

/**
 * Helper: ambil catalog aktif
 */
function booking_get_catalog(PDO $pdo): array
{
    $stmt = $pdo->prepare("
        SELECT id, category, name, description, base_usd, is_active, created_at, updated_at
        FROM booking_catalog
        ORDER BY category, name
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'id'          => (int)$r['id'],
            'category'    => $r['category'],
            'name'        => $r['name'],
            'description' => $r['description'],
            'base_usd'    => (float)$r['base_usd'],
            'is_active'   => (int)$r['is_active'],
        ];
    }
    return $out;
}

/**
 * Helper: update globals (manual save rate)
 */
function booking_save_globals(PDO $pdo, float $usd_to_idr, string $mode = 'manual'): void
{
    $stmt = $pdo->prepare("
        INSERT INTO booking_globals (usd_to_idr, rate_mode, updated_at)
        VALUES (:rate, :mode, :updated_at)
    ");
    $stmt->execute([
        ':rate'       => $usd_to_idr,
        ':mode'       => $mode,
        ':updated_at' => now(),
    ]);
}

/* === Routing sederhana === */
try {
    switch ($action) {
        case 'init':
            $globals = booking_get_globals($pdo);
            $catalog = booking_get_catalog($pdo);

            echo json_encode([
                'status'    => 'ok',
                'today'     => today(),
                'globals'   => $globals,
                'catalog'   => $catalog,
            ]);
            exit;

        case 'save_globals':
            $body = file_get_contents('php://input');
            $json = json_decode($body, true);

            if (!is_array($json)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON body']);
                exit;
            }

            $rate = isset($json['usd_to_idr']) ? (float)$json['usd_to_idr'] : 0;
            $mode = isset($json['rate_mode']) ? (string)$json['rate_mode'] : 'manual';

            if ($rate <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid rate']);
                exit;
            }

            booking_save_globals($pdo, $rate, $mode);
            $globals = booking_get_globals($pdo);

            echo json_encode([
                'status'  => 'ok',
                'message' => 'Globals updated',
                'globals' => $globals,
            ]);
            exit;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
            exit;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Server error: '.$e->getMessage(),
    ]);
    exit;
}
