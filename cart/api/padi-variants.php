<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// keamanan basic
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// product_id wajib
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product_id']);
    exit;
}

// koneksi DB
require_once __DIR__ . '/../../template/database/main-cart.php';
// main-cart.php harus define $pdo (PDO instance)

try {
    $sql = "
        SELECT 
            id,
            label,
            price_usd
        FROM bd_catalog_padi_variants
        WHERE product_id = :pid
          AND is_active = 1
        ORDER BY sort_order ASC, id ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $productId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $options = [];
    foreach ($rows as $r) {
        $options[] = [
            'id'        => (int)$r['id'],
            'label'     => $r['label'],
            'price_usd' => (float)$r['price_usd'],
        ];
    }

    echo json_encode([
        'success' => true,
        'product_id' => $productId,
        'options' => $options
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        // uncomment ini kalau debugging
        // 'detail' => $e->getMessage()
    ]);
}
