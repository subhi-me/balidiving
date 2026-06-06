<?php
require __DIR__.'/_cors.php';
require __DIR__.'/db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$id = $data['id'] ?? '';
if ($id===''){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Missing id']); exit; }

try{
  $stmt = $pdo->prepare("DELETE FROM leads WHERE id = :id");
  $stmt->execute([':id'=>$id]);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>true,'deleted'=>$stmt->rowCount()]);
}catch(Throwable $e){
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
