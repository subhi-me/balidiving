<?php
require __DIR__.'/_cors.php';
require __DIR__.'/db.php';

header('Content-Type: application/json; charset=utf-8');

/** Normalisasi 1 item lead */
function norm($it){
  $now = date('Y-m-d H:i:s');
  return [
    'id' => (string)($it['id'] ?? ''),
    'column' => (string)($it['column'] ?? 'leads'),
    'stage'  => (string)($it['stage']  ?? 'New'),
    'name'   => (string)($it['name']   ?? ''),
    'email'  => isset($it['email']) ? (string)$it['email'] : null,
    'phone'  => isset($it['phone']) ? (string)$it['phone'] : null,
    'country'=> isset($it['country']) ? (string)$it['country'] : null,
    'source' => isset($it['source']) ? (string)$it['source'] : null,
    'package'=> isset($it['package']) ? (string)$it['package'] : null,
    'cert'   => isset($it['cert']) ? (string)$it['cert'] : null,
    'dive_date'=> empty($it['dive_date']) ? null : (string)$it['dive_date'],
    'pax'    => isset($it['pax']) ? (int)$it['pax'] : 0,
    'budget' => isset($it['budget']) ? (float)$it['budget'] : 0,
    'priority'=> (string)($it['priority'] ?? 'medium'),
    'assigned_to'=> isset($it['assigned_to']) ? (string)$it['assigned_to'] : null,
    'next_action_date'=> empty($it['next_action_date']) ? null : (string)$it['next_action_date'],
    'next_action'=> isset($it['next_action']) ? (string)$it['next_action'] : null,
    'url'    => isset($it['url']) ? (string)$it['url'] : null,
    'notes'  => isset($it['notes']) ? (string)$it['notes'] : null,
    'brand'  => (string)($it['brand'] ?? 'BALI DIVING'),
    'created_at'=> !empty($it['created_at']) ? date('Y-m-d H:i:s', strtotime($it['created_at'])) : $now,
    'updated_at'=> !empty($it['updated_at']) ? date('Y-m-d H:i:s', strtotime($it['updated_at'])) : $now,
  ];
}

/** Parser ultra-toleran: POST/GET, JSON/body/form/query; abaikan header apapun */
function parse_body_tolerant() {
  // A) GET ?items= (URL-encoded JSON)
  if (isset($_GET['items'])) {
    $arr = json_decode($_GET['items'], true);
    if (json_last_error() === JSON_ERROR_NONE) return isset($arr[0]) ? $arr : [$arr];
    http_response_code(400); echo json_encode(['ok'=>false,'error'=>'GET items must be valid JSON']); exit;
  }

  // B) Raw body → coba JSON walau header salah
  $raw = file_get_contents('php://input') ?: '';
  if ($raw !== '') {
    $probe = ltrim($raw);
    if ($probe !== '' && ($probe[0] === '{' || $probe[0] === '[')) {
      $json = json_decode($raw, true);
      if (json_last_error() === JSON_ERROR_NONE) return isset($json[0]) ? $json : [$json];
      // lanjut ke fallback form
    }
  }

  // C) Form: items (JSON string)
  if (isset($_POST['items'])) {
    $arr = json_decode($_POST['items'], true);
    if (json_last_error() === JSON_ERROR_NONE) return isset($arr[0]) ? $arr : [$arr];
    http_response_code(400); echo json_encode(['ok'=>false,'error'=>'items must be valid JSON']); exit;
  }

  // D) Form field biasa → 1 item
  if (!empty($_POST)) return [ $_POST ];

  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'No data found (use POST JSON, form, or GET ?items=)']); exit;
}

try {
  $data = parse_body_tolerant();

  // Normalisasi & auto-id
  $items = [];
  foreach ($data as $it) {
    $n = norm($it);
    if ($n['id'] === '') {
      $n['id'] = strtoupper(dechex(time())).substr(strtoupper(md5(uniqid('', true))), 0, 8);
    }
    $items[] = $n;
  }

  $sql = "INSERT INTO leads
   (id, `column`, stage, name, email, phone, country, source, package, cert, dive_date, pax, budget, priority,
    assigned_to, next_action_date, next_action, url, notes, brand, created_at, updated_at)
   VALUES
   (:id, :column, :stage, :name, :email, :phone, :country, :source, :package, :cert, :dive_date, :pax, :budget, :priority,
    :assigned_to, :next_action_date, :next_action, :url, :notes, :brand, :created_at, :updated_at)
   ON DUPLICATE KEY UPDATE
    `column`=VALUES(`column`),
    stage=VALUES(stage),
    name=VALUES(name),
    email=VALUES(email),
    phone=VALUES(phone),
    country=VALUES(country),
    source=VALUES(source),
    package=VALUES(package),
    cert=VALUES(cert),
    dive_date=VALUES(dive_date),
    pax=VALUES(pax),
    budget=VALUES(budget),
    priority=VALUES(priority),
    assigned_to=VALUES(assigned_to),
    next_action_date=VALUES(next_action_date),
    next_action=VALUES(next_action),
    url=VALUES(url),
    notes=VALUES(notes),
    brand=VALUES(brand),
    updated_at=VALUES(updated_at)";

  $pdo->beginTransaction();
  $stmt = $pdo->prepare($sql);
  $cnt = 0;
  foreach ($items as $p) {
    $stmt->execute([
      ':id'=>$p['id'], ':column'=>$p['column'], ':stage'=>$p['stage'],
      ':name'=>$p['name'], ':email'=>$p['email'], ':phone'=>$p['phone'],
      ':country'=>$p['country'], ':source'=>$p['source'], ':package'=>$p['package'],
      ':cert'=>$p['cert'], ':dive_date'=>$p['dive_date'], ':pax'=>$p['pax'],
      ':budget'=>$p['budget'], ':priority'=>$p['priority'], ':assigned_to'=>$p['assigned_to'],
      ':next_action_date'=>$p['next_action_date'], ':next_action'=>$p['next_action'],
      ':url'=>$p['url'], ':notes'=>$p['notes'], ':brand'=>$p['brand'],
      ':created_at'=>$p['created_at'], ':updated_at'=>$p['updated_at'],
    ]);
    $cnt++;
  }
  $pdo->commit();

  echo json_encode(['ok'=>true,'upserted'=>$cnt]);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
