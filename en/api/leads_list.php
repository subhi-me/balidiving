<?php
require __DIR__.'/_cors.php';
require __DIR__.'/db.php';

try {
  $stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC");
  $rows = $stmt->fetchAll();
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>true,'data'=>$rows], JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
