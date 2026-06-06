<?php
/* =========================================================
   BALI DIVING - CRM Kanban (single file, updated)
   - Boards: leads/contacted/booked/archived (+ diveclub via widget)
   - BOOKED sub-board: Coming / On Trip / Finish / Reschedule / Cancel
     -> via view=?view=booking_status (kanban baru)
   - Contact history, Trip history, Referral, Dive Club, Chat Logs
   ========================================================= */

declare(strict_types=1);
session_start();

/* ---------- AUTH (optional) ---------- */
const AUTH_DEBUG = false;
function get_header_val(string $k): string {
  if (!empty($_SERVER[$k])) return (string)$_SERVER[$k];
  $alt = 'HTTP_' . strtoupper(str_replace('-', '_', $k));
  return $_SERVER[$alt] ?? '';
}
function normalize_email(?string $e): string {
  $e = trim((string)$e);
  if (preg_match('/<([^>]+@[^>]+)>/', $e, $m)) $e = $m[1];
  return strtolower($e);
}
function resolve_current_email(): string {
  $cand = [];
  foreach (['user_email','member_email','email'] as $k) if (!empty($_SESSION[$k])) $cand[]=$_SESSION[$k];
  if (!empty($_SESSION['user']['email'])) $cand[]=$_SESSION['user']['email'];
  if (!empty($_COOKIE['user_email'])) $cand[]=$_COOKIE['user_email'];
  foreach (['X-Authenticated-Email','X-Auth-Request-Email','X-Forwarded-User','Remote-User','X-Forwarded-Email','X-User-Email'] as $h){
    $v=get_header_val($h); if($v!=='') $cand[]=$v;
  }
  foreach ($cand as $raw){ $e=normalize_email($raw); if($e!=='') return $e; }
  return '';
}
function is_allowed_email(string $e): bool { return (bool)preg_match('/@balidiving\.com$/i',$e); }
function auth_log($m){ if(AUTH_DEBUG) error_log('[AUTH] '.$m); }

$me = resolve_current_email();
if ($me!=='' && is_allowed_email($me)) $_SESSION['user_email']=$me;
$GLOBALS['__email'] = $_SESSION['user_email'] ?? '';

/* ---------- DB ---------- */
$DB_HOST='localhost'; $DB_NAME='u1783223_bd_crm'; $DB_USER='u1783223_bd_crm'; $DB_PASS='finD0!bd.crm';
$dsn="mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$opt=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];
try { $pdo=new PDO($dsn,$DB_USER,$DB_PASS,$opt); }
catch(Throwable $e){ http_response_code(500); echo "<pre>DB connect failed: ".htmlspecialchars($e->getMessage())."</pre>"; exit; }

/* ---------- Helpers ---------- */
function qexec(PDO $pdo,$sql){ try{$pdo->exec($sql);}catch(Throwable $e){ error_log($e->getMessage().' SQL='.$sql); } }
function table_exists(PDO $pdo,$t):bool{ $st=$pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=:t"); $st->execute([':t'=>$t]); return (bool)$st->fetchColumn(); }
function col_exists(PDO $pdo,$t,$c):bool{ $st=$pdo->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name=:t AND column_name=:c"); $st->execute([':t'=>$t,':c'=>$c]); return (bool)$st->fetchColumn(); }
function uid(){ return strtoupper(dechex(time())).substr(strtoupper(md5(uniqid('', true))),0,8); }
function h($s){ return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
function normalize_phone(string $p): string { $d=preg_replace('/\D+/','',$p); if($d==='')return''; if($d[0]==='0') $d='62'.substr($d,1); return $d; }
function wa($p,$name=''){ $d=normalize_phone((string)$p); if($d==='')return''; $txt=urlencode("Hi ".($name?:'there')." — this is BALI DIVING."); return "https://wa.me/{$d}?text={$txt}"; }
function json_headers(){ header('Content-Type: application/json; charset=UTF-8'); header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0'); header('Pragma: no-cache'); }

/* ---------- Schema: leads ---------- */
if(!table_exists($pdo,'leads')){
  qexec($pdo,"CREATE TABLE leads(
    id VARCHAR(64) PRIMARY KEY,
    `column` VARCHAR(32) NOT NULL DEFAULT 'leads',
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(64) NULL,
    country VARCHAR(64) NULL,
    source VARCHAR(64) NULL,
    package VARCHAR(128) NULL,
    cert VARCHAR(64) NULL,
    dive_date DATE NULL,
    pax INT NULL DEFAULT 0,
    budget DECIMAL(12,2) NULL DEFAULT 0,
    photo_url TEXT NULL,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid',
    payment_method VARCHAR(32) NULL,
    deposit_amount DECIMAL(12,2) NULL DEFAULT 0,
    booking_status VARCHAR(20) NOT NULL DEFAULT 'coming',
    points_total INT NULL DEFAULT 0,
    points_redeemed INT NULL DEFAULT 0,
    promo_code VARCHAR(64) NULL,
    promo_used TINYINT(1) NOT NULL DEFAULT 0,
    loyalty_level VARCHAR(20) NULL,
    social_ig VARCHAR(128) NULL,
    social_fb VARCHAR(128) NULL,
    social_tiktok VARCHAR(128) NULL,
    social_wechat VARCHAR(128) NULL,
    activity VARCHAR(20) NULL,
    brand VARCHAR(64) NOT NULL DEFAULT 'BALI DIVING',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}else{
  foreach([
    "`column` VARCHAR(32) NOT NULL DEFAULT 'leads'",
    "email VARCHAR(255) NULL",
    "phone VARCHAR(64) NULL",
    "country VARCHAR(64) NULL",
    "source VARCHAR(64) NULL",
    "package VARCHAR(128) NULL",
    "cert VARCHAR(64) NULL",
    "dive_date DATE NULL",
    "pax INT NULL DEFAULT 0",
    "budget DECIMAL(12,2) NULL DEFAULT 0",
    "photo_url TEXT NULL",
    "payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid'",
    "payment_method VARCHAR(32) NULL",
    "deposit_amount DECIMAL(12,2) NULL DEFAULT 0",
    "booking_status VARCHAR(20) NOT NULL DEFAULT 'coming'",
    "points_total INT NULL DEFAULT 0",
    "points_redeemed INT NULL DEFAULT 0",
    "promo_code VARCHAR(64) NULL",
    "promo_used TINYINT(1) NOT NULL DEFAULT 0",
    "loyalty_level VARCHAR(20) NULL",
    "social_ig VARCHAR(128) NULL",
    "social_fb VARCHAR(128) NULL",
    "social_tiktok VARCHAR(128) NULL",
    "social_wechat VARCHAR(128) NULL",
    "activity VARCHAR(20) NULL",
    "brand VARCHAR(64) NOT NULL DEFAULT 'BALI DIVING'",
    "created_at DATETIME NOT NULL",
    "updated_at DATETIME NOT NULL"
  ] as $def){
    $col=trim(strtok($def,' '),'`');
    if(!col_exists($pdo,'leads',$col)) qexec($pdo,"ALTER TABLE leads ADD COLUMN $def");
  }
}

/* ---------- Schema: lead_referrers ---------- */
if(!table_exists($pdo,'lead_referrers')){
  qexec($pdo,"CREATE TABLE lead_referrers(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    lead_email VARCHAR(190) NOT NULL,
    ref_email  VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL,
    KEY(lead_email), KEY(ref_email), KEY(created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

/* ---------- Schema: activity_history ---------- */
if(!table_exists($pdo,'activity_history')){
  qexec($pdo,"CREATE TABLE activity_history(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id VARCHAR(64) NOT NULL,
    activity VARCHAR(64) NOT NULL DEFAULT 'Other',
    activity_date DATE NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    INDEX(lead_id),
    INDEX(activity_date)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}
try{ qexec($pdo,"ALTER TABLE activity_history ADD CONSTRAINT fk_history_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE"); }catch(Throwable $e){}

/* ---------- Schema: contact_history ---------- */
if(!table_exists($pdo,'contact_history')){
  qexec($pdo,"CREATE TABLE contact_history(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id VARCHAR(64) NOT NULL,
    channel VARCHAR(32) NOT NULL DEFAULT 'WhatsApp',
    label VARCHAR(64) NULL,
    title VARCHAR(255) NULL,
    message TEXT NOT NULL,
    type VARCHAR(32) NULL,
    direction VARCHAR(16) NOT NULL DEFAULT 'inbound',
    quoted_message_id BIGINT UNSIGNED NULL,
    contact_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    created_by VARCHAR(255) NULL,
    INDEX(lead_id),
    INDEX(channel),
    INDEX(direction)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

/* ---------- Schema: chat_logs (NEW) ---------- */
if(!table_exists($pdo,'chat_logs')){
  qexec($pdo,"CREATE TABLE chat_logs(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    lead_id VARCHAR(64) NULL,
    session_id VARCHAR(64) NULL,
    visitor_name VARCHAR(190) NULL,
    visitor_email VARCHAR(190) NULL,
    sender VARCHAR(32) NOT NULL DEFAULT 'visitor',
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX(lead_id),
    INDEX(session_id),
    INDEX(visitor_email),
    INDEX(created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

/* ---------- Routes API ---------- */
$action = $_REQUEST['action'] ?? '';

/* Read lead (+referrer/referring) */
if($action==='read' && isset($_GET['id'])){
  json_headers();
  try{
    $st=$pdo->prepare("SELECT * FROM leads WHERE id=:id"); $st->execute([':id'=>$_GET['id']]); $row=$st->fetch();
    if(!$row){ echo json_encode(['ok'=>false,'error'=>'Lead not found']); exit; }

    $ref = ['referrer'=>null, 'referring'=>[]];
    $email = strtolower(trim((string)($row['email'] ?? '')));
    if($email!==''){
      $st1=$pdo->prepare("SELECT ref_email, created_at FROM lead_referrers WHERE lead_email=:e ORDER BY created_at DESC LIMIT 1");
      $st1->execute([':e'=>$email]);
      if($r=$st1->fetch()){
        $st1b=$pdo->prepare("SELECT id,name FROM leads WHERE email=:em ORDER BY updated_at DESC LIMIT 1");
        $st1b->execute([':em'=>$r['ref_email']]);
        $lr=$st1b->fetch();
        $ref['referrer'] = [
          'email'=>$r['ref_email'],
          'created_at'=>$r['created_at'],
          'lead_id'=>$lr['id'] ?? null,
          'name'=>$lr['name'] ?? null
        ];
      }

      $st2=$pdo->prepare("
        SELECT lr.lead_email, lr.created_at, l.id AS lead_id, l.name
        FROM lead_referrers lr
        LEFT JOIN leads l ON l.email = lr.lead_email
        WHERE lr.ref_email=:e
        ORDER BY lr.created_at DESC
        LIMIT 200");
      $st2->execute([':e'=>$email]);
      $ref['referring'] = $st2->fetchAll();
    }

    echo json_encode(['ok'=>true,'data'=>$row,'ref'=>$ref]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* Update lead */
if($action==='update' && $_SERVER['REQUEST_METHOD']==='POST'){
  json_headers();
  try{
    $isJson = isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'],'application/json')!==false;
    $in = $isJson ? (json_decode(file_get_contents('php://input'),true) ?: []) : $_POST;
    $id=$in['id']??''; if($id===''){ echo json_encode(['ok'=>false,'error'=>'Invalid ID']); exit; }
    $allowed=[
      'name','email','phone','country','source','package','cert','dive_date','pax','budget',
      'photo_url','payment_status','payment_method','deposit_amount','points_total','points_redeemed',
      'promo_code','promo_used','loyalty_level','social_ig','social_fb','social_tiktok','social_wechat',
      'activity','column','booking_status'
    ];
    $sets=[]; $p=[':id'=>$id,':u'=>date('Y-m-d H:i:s')];
    foreach($allowed as $k){
      if(array_key_exists($k,$in)){
        $sets[]="`$k`=:$k";
        if(in_array($k,['pax'],true)) $p[":$k"]=(int)($in[$k]??0);
        elseif(in_array($k,['budget','deposit_amount'],true)) $p[":$k"]=(float)($in[$k]??0);
        elseif($k==='promo_used') $p[":$k"]=(int)!empty($in[$k]);
        elseif($k==='dive_date'){ $val=trim((string)($in[$k]??'')); $p[":$k"]=($val==='')?null:$val; }
        else $p[":$k"]=trim((string)($in[$k]??''));
      }
    }
    if(!$sets){ echo json_encode(['ok'=>false,'error'=>'No fields']); exit; }
    $pdo->prepare("UPDATE leads SET ".implode(',',$sets).", updated_at=:u WHERE id=:id")->execute($p);
    $st=$pdo->prepare("SELECT * FROM leads WHERE id=:id"); $st->execute([':id'=>$id]); $row=$st->fetch();
    echo json_encode(['ok'=>true,'data'=>$row]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* Create lead */
if($action==='create' && $_SERVER['REQUEST_METHOD']==='POST'){
  $name=trim($_POST['name']??'');
  if($name===''){ header('Location: ?'); exit; }
  $id=uid();
  $data=[
    'id'=>$id,
    'column'=>'leads',
    'name'=>$name,
    'email'=>trim($_POST['email']??''),
    'phone'=>trim($_POST['phone']??''),
    'country'=>trim($_POST['country']??''),
    'source'=>trim($_POST['source']??''),
    'package'=>trim($_POST['package']??''),
    'cert'=>trim($_POST['cert']??''),
    'dive_date'=>(isset($_POST['dive_date'])&&trim($_POST['dive_date'])!=='')?$_POST['dive_date']:null,
    'pax'=>(int)($_POST['pax']??0),
    'budget'=>0,
    'photo_url'=>'',
    'payment_status'=>'unpaid',
    'payment_method'=>'',
    'deposit_amount'=>0,
    'booking_status'=>'coming',
    'points_total'=>0,
    'points_redeemed'=>0,
    'promo_code'=>'',
    'promo_used'=>0,
    'loyalty_level'=>'',
    'social_ig'=>'',
    'social_fb'=>'',
    'social_tiktok'=>'',
    'social_wechat'=>'',
    'activity'=>'Other',
    'brand'=>'BALI DIVING',
    'created_at'=>date('Y-m-d H:i:s'),
    'updated_at'=>date('Y-m-d H:i:s')
  ];
  $pdo->prepare("INSERT INTO leads
    (id,`column`,name,email,phone,country,source,package,cert,dive_date,pax,budget,photo_url,payment_status,payment_method,deposit_amount,booking_status,points_total,points_redeemed,promo_code,promo_used,loyalty_level,social_ig,social_fb,social_tiktok,social_wechat,activity,brand,created_at,updated_at)
    VALUES
    (:id,:column,:name,:email,:phone,:country,:source,:package,:cert,:dive_date,:pax,:budget,:photo_url,:payment_status,:payment_method,:deposit_amount,:booking_status,:points_total,:points_redeemed,:promo_code,:promo_used,:loyalty_level,:social_ig,:social_fb,:social_tiktok,:social_wechat,:activity,:brand,:created_at,:updated_at)
  ")->execute($data);
  header('Location: ?'); exit;
}

/* Delete lead */
if($action==='delete' && $_SERVER['REQUEST_METHOD']==='POST'){
  json_headers();
  try{
    $id=$_POST['id']??''; if($id!==''){ $pdo->prepare("DELETE FROM leads WHERE id=:id")->execute([':id'=>$id]); echo json_encode(['ok'=>true]); exit; }
    echo json_encode(['ok'=>false,'error'=>'Invalid ID']);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* Move board (incl. diveclub) */
if($action==='move' && $_SERVER['REQUEST_METHOD']==='POST'){
  json_headers();
  try{
    $id=$_POST['id']??''; $to=$_POST['to']??'leads';
    $valid=['leads','contacted','booked','archived','diveclub'];
    if($id!=='' && in_array($to,$valid,true)){
      if($to==='diveclub'){
        $pdo->prepare("UPDATE leads SET `column`='diveclub', loyalty_level='Dive Club', updated_at=:u WHERE id=:id")
            ->execute([':u'=>date('Y-m-d H:i:s'),':id'=>$id]);
      } else {
        $pdo->prepare("UPDATE leads SET `column`=:c, updated_at=:u WHERE id=:id")
            ->execute([':c'=>$to,':u'=>date('Y-m-d H:i:s'),':id'=>$id]);
      }
      $st=$pdo->prepare("SELECT * FROM leads WHERE id=:id"); $st->execute([':id'=>$id]); $row=$st->fetch();
      echo json_encode(['ok'=>true,'data'=>$row]); exit;
    }
    echo json_encode(['ok'=>false,'error'=>'Invalid move']);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* Move BOOKING STATUS (sub-board for booked) */
if($action==='move_booking_status' && $_SERVER['REQUEST_METHOD']==='POST'){
  json_headers();
  try{
    $id = $_POST['id'] ?? '';
    $to = strtolower(trim($_POST['to'] ?? ''));
    $valid = ['coming','on_trip','finish','reschedule','cancel'];
    if($id==='' || !in_array($to,$valid,true)){
      echo json_encode(['ok'=>false,'error'=>'Invalid move']); exit;
    }
    $pdo->prepare("UPDATE leads SET booking_status=:st, updated_at=NOW() WHERE id=:id AND `column`='booked'")
        ->execute([':st'=>$to,':id'=>$id]);
    $st=$pdo->prepare("SELECT * FROM leads WHERE id=:id"); $st->execute([':id'=>$id]); $row=$st->fetch();
    echo json_encode(['ok'=>true,'data'=>$row]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* Trip: add/list */
if($action==='add_activity' && $_SERVER['REQUEST_METHOD']==='POST'){
  json_headers();
  try{
    $lid=$_POST['lead_id']??''; if($lid===''){ echo json_encode(['ok'=>false,'error'=>'Invalid lead']); exit; }
    $pdo->prepare("INSERT INTO activity_history (lead_id,activity,activity_date,notes,created_at) VALUES (:l,:a,:d,:n,:c)")
        ->execute([
          ':l'=>$lid,
          ':a'=>($_POST['activity']??'Other'),
          ':d'=>($_POST['activity_date']??null)?:null,
          ':n'=>($_POST['notes']??''),
          ':c'=>date('Y-m-d H:i:s')
        ]);
    echo json_encode(['ok'=>true]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}
if($action==='list_activity' && isset($_GET['lead_id'])){
  json_headers();
  try{
    $st=$pdo->prepare("SELECT * FROM activity_history WHERE lead_id=:id ORDER BY activity_date DESC, id DESC"); $st->execute([':id'=>$_GET['lead_id']]);
    echo json_encode(['ok'=>true,'items'=>$st->fetchAll()]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* Contact History add/list/reply/import */
if($action==='add_contact' && $_SERVER['REQUEST_METHOD']==='POST'){
  json_headers();
  try{
    $lid=trim($_POST['lead_id']??''); if($lid===''){ echo json_encode(['ok'=>false,'error'=>'Invalid lead']); exit; }
    $data=[
      ':lead_id'=>$lid,
      ':channel'=>trim($_POST['channel']??'WhatsApp'),
      ':label'=>trim($_POST['label']??''),
      ':title'=>trim($_POST['title']??''),
      ':message'=>trim($_POST['message']??''),
      ':type'=>trim($_POST['type']??'Follow Up'),
      ':direction'=>trim($_POST['direction']??'outbound'),
      ':quoted_message_id'=>!empty($_POST['quoted_message_id'])?(int)$_POST['quoted_message_id']:null,
      ':contact_at'=>!empty($_POST['contact_at'])?$_POST['contact_at']:null,
      ':created_at'=>date('Y-m-d H:i:s'),
      ':created_by'=>($GLOBALS['__email']??'')
    ];
    $pdo->prepare("INSERT INTO contact_history (lead_id,channel,label,title,message,type,direction,quoted_message_id,contact_at,created_at,created_by) VALUES (:lead_id,:channel,:label,:title,:message,:type,:direction,:quoted_message_id,:contact_at,:created_at,:created_by)")->execute($data);
    echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}
if($action==='list_contact' && isset($_GET['lead_id'])){
  json_headers();
  try{
    $st=$pdo->prepare("SELECT * FROM contact_history WHERE lead_id=:id ORDER BY contact_at DESC, id DESC"); $st->execute([':id'=>$_GET['lead_id']]);
    echo json_encode(['ok'=>true,'items'=>$st->fetchAll()]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}
if($action==='reply_wa_link' && isset($_GET['lead_id'],$_GET['message_id'])){
  json_headers();
  try{
    $lid=$_GET['lead_id']; $mid=(int)$_GET['message_id'];
    $st=$pdo->prepare("SELECT name,phone FROM leads WHERE id=:id"); $st->execute([':id'=>$lid]); $lead=$st->fetch();
    if(!$lead){ echo json_encode(['ok'=>false,'error'=>'Lead not found']); exit; }
    $msisdn=normalize_phone($lead['phone']??''); if($msisdn===''){ echo json_encode(['ok'=>false,'error'=>'No phone']); exit; }
    $st2=$pdo->prepare("SELECT message FROM contact_history WHERE id=:mid AND lead_id=:lid"); $st2->execute([':mid'=>$mid,':lid'=>$lid]); $row=$st2->fetch();
    if(!$row){ echo json_encode(['ok'=>false,'error'=>'Message not found']); exit; }
    $quote="> ".preg_replace('/\r?\n/',"\n> ",trim((string)$row['message']));
    $template="Hi ".($lead['name']?:'there').",\n\n".$quote."\n\n— Reply: ";
    $url="https://wa.me/{$msisdn}?text=".rawurlencode($template);
    echo json_encode(['ok'=>true,'url'=>$url]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}
if($action==='import_whatsapp' && $_SERVER['REQUEST_METHOD']==='POST'){
  json_headers();
  try{
    $lid=trim($_POST['lead_id']??''); $raw=(string)($_POST['raw']??''); if($lid===''||$raw===''){ echo json_encode(['ok'=>false,'error'=>'lead/paste required']); exit; }
    $lines=preg_split('/\r\n|\r|\n/',$raw); $ins=$pdo->prepare("INSERT INTO contact_history (lead_id,channel,label,title,message,type,direction,quoted_message_id,contact_at,created_at,created_by) VALUES (:lead_id,'WhatsApp',:label,:title,:message,:type,:direction,NULL,:contact_at,:created_at,:created_by)");
    $n=0; $now=date('Y-m-d H:i:s');
    foreach($lines as $ln){
      $ln=trim($ln); if($ln==='') continue;
      if(preg_match('/^\[(\d{1,2}\/\d{1,2}\/\d{2,4}),\s*(\d{1,2}:\d{2})\]\s*([^:]+):\s*(.+)$/',$ln,$m)
      || preg_match('/^(\d{1,2}\/\d{1,2}\/\d{2,4}),\s*(\d{1,2}:\d{2})\s*-\s*([^:]+):\s*(.+)$/',$ln,$m)){
        $dmy=$m[1]; $hm=$m[2]; $who=trim($m[3]); $msg=$m[4];
        $dt=DateTime::createFromFormat('d/m/y H:i',$dmy.' '.$hm) ?: DateTime::createFromFormat('d/m/Y H:i',$dmy.' '.$hm);
        $ins->execute([
          ':lead_id'=>$lid,
          ':label'=>'Import',
          ':title'=>$who,
          ':message'=>$msg,
          ':type'=>'Follow Up',
          ':direction'=>(stripos($who,'you')!==false||stripos($who,'me')!==false)?'outbound':'inbound',
          ':contact_at'=>$dt?$dt->format('Y-m-d H:i:s'):$now,
          ':created_at'=>$now,
          ':created_by'=>($GLOBALS['__email']??'')
        ]);
        $n++; continue;
      }
      $ins->execute([
        ':lead_id'=>$lid,
        ':label'=>'Import',
        ':title'=>'',
        ':message'=>$ln,
        ':type'=>'Note',
        ':direction'=>'inbound',
        ':contact_at'=>$now,
        ':created_at'=>$now,
        ':created_by'=>($GLOBALS['__email']??'')
      ]);
      $n++;
    }
    echo json_encode(['ok'=>true,'count'=>$n]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* ---------- Chat Logs list ---------- */
if($action==='list_chat'){
  json_headers();
  try{
    $lead_id = trim($_GET['lead_id'] ?? '');
    $email   = strtolower(trim($_GET['email'] ?? ''));
    $conds   = [];
    $params  = [];
    if($lead_id!==''){ $conds[]='lead_id = :lid'; $params[':lid']=$lead_id; }
    if($email!==''){ $conds[]='LOWER(visitor_email) = :em'; $params[':em']=$email; }
    if(!$conds){ echo json_encode(['ok'=>true,'items'=>[]]); exit; }
    $sql = "SELECT * FROM chat_logs WHERE ".implode(' OR ',$conds)." ORDER BY created_at ASC";
    $st = $pdo->prepare($sql); $st->execute($params);
    echo json_encode(['ok'=>true,'items'=>$st->fetchAll()]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* List Dive Club */
if($action==='list_diveclub'){
  json_headers();
  try{
    $st=$pdo->query("SELECT id,name,email,phone,updated_at FROM leads WHERE `column`='diveclub' OR loyalty_level='Dive Club' ORDER BY updated_at DESC");
    echo json_encode(['ok'=>true,'items'=>$st->fetchAll()]);
  }catch(Throwable $e){
    echo json_encode(['ok'=>false,'error'=>'DB error: '.$e->getMessage()]);
  }
  exit;
}

/* ---------- Auto-move archived >= 30 hari ---------- */
qexec($pdo,"UPDATE leads SET `column`='diveclub', loyalty_level='Dive Club', updated_at=NOW()
            WHERE `column`='archived' AND updated_at < (NOW() - INTERVAL 30 DAY)");

/* ---------- View mode ---------- */
$view = $_GET['view'] ?? 'main';

/* ---------- Main board data (grid tanpa diveclub) ---------- */
$columns=[
  'leads'     => 'Leads',
  'contacted' => 'Contacted',
  'booked'    => 'Booked',
  'archived'  => 'Archived',
];
$themes=[
  'leads'     => ['bg'=>'#0b1b2a','text'=>'#E6F6FF','border'=>'#38bdf8','dot'=>'#38bdf8'],
  'contacted' => ['bg'=>'#261a0a','text'=>'#FFF7E6','border'=>'#f59e0b','dot'=>'#f59e0b'],
  'booked'    => ['bg'=>'#0b241b','text'=>'#E8FFF6','border'=>'#10b981','dot'=>'#10b981'],
  'archived'  => ['bg'=>'#111827','text'=>'#E5E7EB','border'=>'#94a3b8','dot'=>'#94a3b8'],
];

$dataByCol=[]; $counts=[];
foreach(array_keys($columns) as $c){
  $st=$pdo->prepare("SELECT * FROM leads WHERE `column`=:c ORDER BY updated_at DESC");
  $st->execute([':c'=>$c]); $rows=$st->fetchAll();

  $byEmail=[]; $used=[];
  foreach($rows as $r){ $e=strtolower(trim($r['email']??'')); if($e!=='') $byEmail[$e][]=$r; }
  $groups=[];
  foreach($byEmail as $e=>$list){
    if(count($list)>=2){
      usort($list,fn($a,$b)=>strcmp($b['updated_at'],$a['updated_at']));
      $groups[]=['kind'=>'email','key'=>$e,'items'=>$list];
      foreach($list as $r){ $used[$r['id']]=true; }
    }
  }
  $byPhone=[];
  foreach($rows as $r){
    if(!empty($used[$r['id']])) continue;
    $p=normalize_phone((string)($r['phone']??'')); if($p!=='') $byPhone[$p][]=$r;
  }
  foreach($byPhone as $p=>$list){
    if(count($list)>=2){
      usort($list,fn($a,$b)=>strcmp($b['updated_at'],$a['updated_at']));
      $groups[]=['kind'=>'phone','key'=>$p,'items'=>$list];
      foreach($list as $r){ $used[$r['id']]=true; }
    }
  }
  $singles=[]; foreach($rows as $r){ if(empty($used[$r['id']])) $singles[]=$r; }

  $counts[$c]=count($groups)+count($singles);
  $dataByCol[$c]=['groups'=>$groups,'singles'=>$singles];
}

/* Dive Club preload */
$dcStmt=$pdo->query("SELECT id,name,email,phone,updated_at FROM leads WHERE `column`='diveclub' OR loyalty_level='Dive Club' ORDER BY updated_at DESC");
$diveClub = $dcStmt->fetchAll();

/* Booking Status view data (only for view=booking_status) */
$statusCols = [
  'coming'     => 'Coming',
  'on_trip'    => 'On Trip',
  'finish'     => 'Finish',
  'reschedule' => 'Reschedule',
  'cancel'     => 'Cancel',
];
$bookedByStatus = []; $bookCounts = []; $totalBooked = 0;
if($view==='booking_status'){
  foreach($statusCols as $k=>$lbl){ $bookedByStatus[$k]=[]; $bookCounts[$k]=0; }
  $stB=$pdo->query("SELECT * FROM leads WHERE `column`='booked' ORDER BY updated_at DESC");
  $rowsB=$stB->fetchAll();
  foreach($rowsB as $r){
    $st = strtolower($r['booking_status'] ?? '');
    if(!isset($statusCols[$st])) $st='coming';
    $bookedByStatus[$st][]=$r;
    $bookCounts[$st] = ($bookCounts[$st] ?? 0) + 1;
    $totalBooked++;
  }
}

$total=array_sum($counts);
?>
<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <title>BALI DIVING CRM — Kanban</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    :root{ color-scheme: dark; }
    body{
      background:
        radial-gradient(1200px 600px at 20% -10%, rgba(56,189,248,.08), transparent 40%),
        radial-gradient(900px 500px at 110% 10%, rgba(16,185,129,.08), transparent 40%),
        #020617; color:#e2e8f0;
    }
    .kanban-card{
      transition:transform .15s, box-shadow .15s;
      cursor:pointer;
      background:var(--card-bg,#0f172a);
      color:var(--card-text,#e2e8f0);
      border:1px solid var(--card-border,#334155);
    }
    .kanban-card:hover{ transform:translateY(-2px); box-shadow:0 16px 40px rgba(0,0,0,.35); }
    .status-card{
      transition:transform .15s, box-shadow .15s;
      cursor:pointer;
      background:#020617;
      border:1px solid #1f2937;
    }
    .status-card:hover{ transform:translateY(-2px); box-shadow:0 16px 40px rgba(0,0,0,.35); }
    .dragging{ opacity:.65 }
    .drop-hint{ outline:2px dashed rgba(125,211,252,.7); outline-offset:8px }
    .offcanvas{ transform:translateX(100%); transition:transform .25s ease }
    .offcanvas.open{ transform:translateX(0) }
    .modal{ opacity:0; pointer-events:none; transition:opacity .2s ease }
    .modal.open{ opacity:1; pointer-events:auto }
    .btn{ display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .6rem; border-radius:.5rem; border:1px solid #334155; }
    details.acc { border:1px solid #1f2937; border-radius:0.75rem; background:#0b1220; }
    details.acc + details.acc { margin-top:0.75rem }
    details.acc[open]{ background:#0b1426; }
    details.acc summary{ list-style:none; cursor:pointer; user-select:none; padding:.75rem 1rem; display:flex; align-items:center; justify-content:space-between; }
    details.acc summary::-webkit-details-marker{ display:none }
    .acc-title{ display:flex; align-items:center; gap:.5rem; font-weight:600 }

    #diveClubWidget{ position: sticky; bottom: 0; z-index: 20; backdrop-filter: blur(6px); }
    #diveClubWidget .dropzone{ border:1px dashed #a3e635; border-radius: 10px; padding: 8px; text-align:center; color:#a3e635; background: rgba(17,24,39,.35); }
    #diveClubWidget .grid-names > div{ background:#0b1220; border:1px solid #1f2937; border-radius:10px; padding:8px; }

    #diveClubSheet{
      position: fixed; left:0; right:0; bottom:0; transform: translateY(100%);
      transition: transform .25s ease;
      z-index: 60; background:#0b1220; border-top:1px solid #334155; max-height: 85vh; display:flex; flex-direction:column;
      box-shadow: 0 -30px 60px rgba(0,0,0,.45);
    }
    #diveClubSheet.open{ transform: translateY(0); }
    #diveClubSheet .grab{ height: 16px; position: relative; }
    #diveClubSheet .grab::after{ content:''; position:absolute; left:50%; top:6px; transform:translateX(-50%); width:64px; height:4px; border-radius:4px; background:#334155; }
    #diveClubSheet .dropzone{ border:1px dashed #a3e635; border-radius: 12px; padding: 10px; text-align:center; color:#a3e635; background: rgba(17,24,39,.35); }
    #diveClubSheet .grid-names > div{ background:#111827; border:1px solid #374151; border-radius: 12px; padding:10px; }
  </style>
</head>
<body class="text-slate-100 min-h-screen">

<header class="sticky top-0 z-30 bg-slate-950/60 backdrop-blur border-b border-slate-800">
  <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="h-9 w-9 rounded-xl bg-sky-500 text-slate-900 font-bold flex items-center justify-center">BD</div>
      <div>
        <h1 class="text-xl md:text-2xl font-semibold">
          <?= $view==='booking_status' ? 'Booking Status · Booked Leads' : 'BALI DIVING CRM' ?>
        </h1>
        <p class="text-xs text-slate-400">
          <?= $view==='booking_status' ? 'Sub-board: Coming / On Trip / Finish / Reschedule / Cancel' : 'Leads Kanban' ?>
          • <?= h($GLOBALS['__email'] ?: 'guest') ?>
        </p>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <?php if($view==='booking_status'): ?>
        <button onclick="location.href='<?= h(basename($_SERVER['PHP_SELF'])) ?>';"
                class="px-3 py-2 rounded-lg bg-slate-800 text-slate-100 text-sm hover:bg-slate-700">
          <i class="fa-solid fa-arrow-left mr-1.5"></i> Back CRM
        </button>
      <?php else: ?>
        <span class="hidden md:inline text-slate-400 text-sm mr-2">Total: <b><?= (int)$total ?></b></span>
        <button id="openAddModalBtn" class="px-3 py-2 rounded-lg bg-sky-500 text-slate-900 text-sm hover:bg-sky-400">
          <i class="fa-solid fa-plus mr-1.5"></i> New Lead
        </button>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="max-w-7xl mx-auto px-4 py-6 space-y-6">
<?php if($view==='booking_status'): ?>

  <!-- ========== BOOKING STATUS KANBAN (Booked only) ========== -->
  <section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2 text-sm text-slate-300">
        <i class="fa-solid fa-calendar-check text-emerald-400"></i>
        <span>Managing <b><?= (int)$totalBooked ?></b> booked leads</span>
      </div>
      <div class="flex flex-wrap gap-2 text-xs text-slate-400">
        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-sky-400"></span> Coming</span>
        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> On Trip</span>
        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Finish</span>
        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-indigo-400"></span> Reschedule</span>
        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-400"></span> Cancel</span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
      <?php
        $statusTheme = [
          'coming'     => ['dot'=>'#38bdf8','border'=>'#38bdf8'],
          'on_trip'    => ['dot'=>'#fbbf24','border'=>'#fbbf24'],
          'finish'     => ['dot'=>'#22c55e','border'=>'#22c55e'],
          'reschedule' => ['dot'=>'#6366f1','border'=>'#6366f1'],
          'cancel'     => ['dot'=>'#f97373','border'=>'#f97373'],
        ];
      ?>
      <?php foreach($statusCols as $code=>$label): 
        $items = $bookedByStatus[$code] ?? [];
        $th = $statusTheme[$code];
      ?>
        <div class="bg-slate-900/70 backdrop-blur border border-slate-800 rounded-2xl overflow-hidden flex flex-col">
          <div class="px-4 py-3 flex items-center justify-between bg-slate-900/60 border-b border-slate-800">
            <div class="flex items-center gap-2">
              <span class="h-2 w-2 rounded-full" style="background:<?=h($th['dot'])?>"></span>
              <h3 class="font-semibold text-sm"><?= h($label) ?></h3>
            </div>
            <span class="text-xs text-slate-400"><?= (int)($bookCounts[$code] ?? 0) ?> items</span>
          </div>
          <div class="p-3 space-y-3 flex-1 min-h-[220px]"
               data-status-board="<?=h($code)?>">
            <?php if(empty($items)): ?>
              <div class="text-sm text-slate-500 italic">Drop booked leads here…</div>
            <?php else: foreach($items as $row): ?>
              <div class="status-card rounded-xl p-3" draggable="true" data-id="<?=h($row['id'])?>">
                <div class="flex items-center justify-between gap-3">
                  <div class="min-w-0">
                    <div class="font-medium truncate"><?= h($row['name']) ?></div>
                    <div class="text-[11px] text-slate-400 mt-0.5">
                      <?= $row['dive_date'] ? '<i class="fa-regular fa-calendar"></i> '.h($row['dive_date']).' · ' : '' ?>
                      Pax: <?= (int)($row['pax'] ?? 0) ?>
                    </div>
                    <?php if(!empty($row['package'])): ?>
                      <div class="text-[11px] text-slate-400 truncate mt-0.5">
                        <i class="fa-solid fa-box-open"></i> <?= h($row['package']) ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  <?php $waL=wa($row['phone']??'', $row['name']??''); ?>
                  <?php if($waL): ?>
                    <a href="<?=h($waL)?>" target="_blank" rel="noopener"
                       class="shrink-0 inline-flex items-center justify-center text-[11px] px-2 py-1 rounded-md bg-white text-slate-900 hover:opacity-90"
                       onclick="event.stopPropagation();">
                      <i class="fa-brands fa-whatsapp text-[12px]"></i>
                    </a>
                  <?php else: ?>
                    <span class="shrink-0 text-[11px] px-2 py-1 rounded-md bg-slate-700/80 text-slate-300">
                      <i class="fa-regular fa-circle-xmark text-[12px] mr-1"></i> WA
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

<?php else: ?>

  <!-- ========== MAIN CRM KANBAN (Leads / Contacted / Booked / Archived) ========== -->
  <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="kanban">
    <?php foreach ($columns as $key=>$label): $t=$themes[$key]; $payload=$dataByCol[$key]; ?>
      <div class="bg-slate-900/70 backdrop-blur border border-slate-800 rounded-2xl overflow-hidden flex flex-col">
        <div class="px-4 py-3 flex items-center justify-between bg-slate-900/60 border-b border-slate-800">
          <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full" style="background:<?=h($t['dot'])?>"></span>
            <h3 class="font-semibold"><?= h($label) ?></h3>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400"><?= (int)$counts[$key] ?> items</span>
            <?php if($key==='booked'): ?>
              <a href="?view=booking_status"
                 class="inline-flex items-center gap-1 text-[11px] px-2 py-1 rounded-md border border-emerald-500 text-emerald-300 hover:bg-emerald-500/10">
                <i class="fa-solid fa-square-kanban"></i> Status
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="p-3 space-y-3 flex-1 min-h-[220px]"
             data-column="<?=h($key)?>"
             style="--card-bg:<?=h($t['bg'])?>;--card-text:<?=h($t['text'])?>;--card-border:<?=h($t['border'])?>;"
             ondragover="event.preventDefault()">

          <?php
          $groups=$payload['groups']; $singles=$payload['singles'];
          if(empty($groups) && empty($singles)){
            echo '<div class="text-sm text-slate-400 italic"><i class="fa-regular fa-square-plus"></i> Drop a lead here…</div>';
          }
          ?>

          <!-- GROUP CARDS -->
          <?php foreach($groups as $g):
            $latest = $g['items'][0];
            $displayName = $latest['name'] ?: ($g['kind']==='email' ? $g['key'] : '+'.$g['key']);
            $groupId = 'grp_'.md5($g['kind'].'|'.$g['key'].'|'.$key);
          ?>
            <div class="kanban-card rounded-xl p-3 flex items-center justify-between" draggable="false" data-group-id="<?=h($groupId)?>">
              <div class="min-w-0">
                <div class="font-medium truncate"><?= h($displayName) ?></div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                  <?= $g['kind']==='email' ? '<i class="fa-regular fa-envelope"></i> Email Group' : '<i class="fa-brands fa-whatsapp"></i> WhatsApp Group' ?> • <?= count($g['items']) ?> leads
                </div>
              </div>
              <button type="button" class="btn text-xs hover:bg-slate-800" onclick="openGroupModal('<?=h($groupId)?>')">
                <i class="fa-solid fa-list"></i> List
              </button>
            </div>
            <template id="<?=h($groupId)?>__data"><?php
              $out=[]; foreach($g['items'] as $it){ $out[]=['id'=>$it['id'],'name'=>$it['name'],'email'=>$it['email'],'phone'=>$it['phone'],'updated_at'=>$it['updated_at']]; }
              echo h(json_encode($out, JSON_UNESCAPED_SLASHES));
            ?></template>
          <?php endforeach; ?>

          <!-- SINGLE CARDS -->
          <?php foreach($singles as $row): $waL=wa($row['phone']??'', $row['name']??''); ?>
            <div class="kanban-card rounded-xl p-3" draggable="true" data-id="<?=h($row['id'])?>">
              <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-medium truncate"><?=h($row['name'])?></div>
                  <?php if($key==='booked'): 
                    $st = strtolower($row['booking_status'] ?? 'coming');
                    $labelMap = [
                      'coming'=>'Coming',
                      'on_trip'=>'On Trip',
                      'finish'=>'Finish',
                      'reschedule'=>'Reschedule',
                      'cancel'=>'Cancel'
                    ];
                    $badgeMap = [
                      'coming'=>'bg-sky-600/20 text-sky-300 border-sky-500/60',
                      'on_trip'=>'bg-amber-600/20 text-amber-300 border-amber-500/60',
                      'finish'=>'bg-emerald-600/20 text-emerald-300 border-emerald-500/60',
                      'reschedule'=>'bg-indigo-600/20 text-indigo-300 border-indigo-500/60',
                      'cancel'=>'bg-rose-600/20 text-rose-300 border-rose-500/60',
                    ];
                    $lbl = $labelMap[$st] ?? 'Coming';
                    $cls = $badgeMap[$st] ?? $badgeMap['coming'];
                  ?>
                    <div class="mt-1">
                      <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full border <?=h($cls)?>">
                        <i class="fa-solid fa-location-dot"></i> <?= h($lbl) ?>
                      </span>
                    </div>
                  <?php endif; ?>
                </div>
                <?php if($waL): ?>
                  <a href="<?=h($waL)?>" target="_blank" rel="noopener"
                     class="shrink-0 inline-flex items-center gap-1.5 text-[11px] px-2 py-1 rounded-md bg-white text-slate-900 hover:opacity-90"
                     title="WhatsApp" onclick="event.stopPropagation();">
                    <i class="fa-brands fa-whatsapp text-[12px]"></i> WA
                  </a>
                <?php else: ?>
                  <button type="button" class="shrink-0 text-[11px] px-2 py-1 rounded-md bg-slate-700/80 text-slate-300 cursor-not-allowed"
                          title="No phone" onclick="event.stopPropagation();">
                    <i class="fa-regular fa-circle-xmark text-[12px] mr-1"></i> WA
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      </div>
    <?php endforeach; ?>
  </section>

<?php endif; ?>
</main>

<!-- ===== DIVE CLUB WIDGET (BOTTOM) - tetap ada di kedua view ===== -->
<section id="diveClubWidget" class="bg-slate-950/70 border-t border-slate-800 py-3">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between mb-2">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-users-line text-lime-400"></i>
        <h3 class="font-semibold">Dive Club</h3>
        <span id="dcCount" class="text-xs text-slate-400"></span>
      </div>
      <div class="flex items-center gap-2">
        <button id="openDiveSheetBtn" class="btn text-xs hover:bg-slate-800"><i class="fa-solid fa-up-right-from-square"></i> Open</button>
      </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 grid-names" id="diveClubWidgetGrid">
      <?php if(empty($diveClub)): ?>
        <div class="text-slate-400 text-sm italic col-span-full">Belum ada anggota Dive Club.</div>
      <?php else: foreach($diveClub as $dc): ?>
        <div class="cursor-pointer" data-diveclub-id="<?= h($dc['id']) ?>" title="Open lead">
          <div class="font-medium truncate"><?= h($dc['name'] ?: '(No Name)') ?></div>
        </div>
      <?php endforeach; endif; ?>
    </div>
    <div id="diveClubWidgetDrop" class="dropzone mt-2 text-xs">Put Here to join Dive Club</div>
  </div>
</section>

<!-- ===== OFFCANVAS LEAD (RIGHT) ===== -->
<aside id="offcanvas" class="offcanvas fixed top-0 right-0 w-full sm:w-[560px] h-full bg-slate-900 shadow-2xl z-50 flex flex-col border-l border-slate-800">
  <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
    <h3 class="text-lg font-semibold"><i class="fa-regular fa-id-card mr-2 text-slate-400"></i> Lead Details</h3>
    <button id="closeBtn" class="px-3 py-1.5 text-sm rounded border border-slate-700 hover:bg-slate-800"><i class="fa-solid fa-xmark"></i> Close</button>
  </div>
  <div class="flex-1 overflow-y-auto p-5">
    <form id="editForm" class="space-y-3">
      <section class="border border-slate-800 rounded-xl bg-slate-950 p-4 mb-3">
        <div class="flex items-center justify-between mb-3">
          <span class="flex items-center gap-2 font-semibold"><i class="fa-regular fa-id-card text-slate-400"></i> Basic</span>
        </div>
        <input type="hidden" id="f_id">
        <div class="grid grid-cols-3 gap-3">
          <div class="col-span-2">
            <label class="block text-sm text-slate-300 mb-1">Name</label>
            <input id="f_name" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" />
          </div>
          <div>
            <label class="block text-sm text-slate-300 mb-1">Board</label>
            <select id="f_column" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100">
              <option value="leads">Leads</option>
              <option value="contacted">Contacted</option>
              <option value="booked">Booked</option>
              <option value="archived">Archived</option>
              <option value="diveclub">Dive Club</option>
            </select>
          </div>
        </div>
        <div class="text-xs text-slate-400 mt-2" id="metaDates"></div>
      </section>

      <details class="acc">
        <summary><span class="acc-title"><i class="fa-regular fa-envelope text-slate-400"></i> Contact</span><i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i></summary>
        <div class="acc-body grid grid-cols-2 gap-3">
          <div><label class="block text-sm text-slate-300 mb-1">Email</label><input id="f_email" type="email" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
          <div><label class="block text-sm text-slate-300 mb-1">Phone</label><input id="f_phone" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
          <div><label class="block text-sm text-slate-300 mb-1">Country</label><input id="f_country" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
          <div><label class="block text-sm text-slate-300 mb-1">Source</label><input id="f_source" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
        </div>
      </details>

      <!-- REFERRALS -->
      <details class="acc" open>
        <summary>
          <span class="acc-title"><i class="fa-solid fa-link text-slate-400"></i> Referrals</span>
          <span id="ref_count_badge" class="ml-auto text-xs text-slate-400"></span>
          <i class="fa-solid fa-chevron-down text-slate-400 text-xs ml-3"></i>
        </summary>
        <div class="acc-body space-y-3">
          <div class="border border-slate-800 rounded-lg p-3 bg-slate-950">
            <div class="text-xs text-slate-400 mb-1">Referrer (dirujuk oleh)</div>
            <div id="ref_referrer" class="text-sm"></div>
          </div>
          <div class="border border-slate-800 rounded-lg p-3 bg-slate-950">
            <div class="text-xs text-slate-400 mb-2">Referring (mereferensikan)</div>
            <div id="ref_referring" class="space-y-2"></div>
          </div>
        </div>
      </details>

      <details class="acc">
        <summary><span class="acc-title"><i class="fa-solid fa-box-open text-slate-400"></i> Package</span><i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i></summary>
        <div class="acc-body grid grid-cols-2 gap-3">
          <div><label class="block text-sm text-slate-300 mb-1">Package</label><input id="f_package" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
          <div>
            <label class="block text-sm text-slate-300 mb-1">Certification</label>
            <select id="f_cert" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100">
              <optgroup label="Beginner">
                <option value="">—</option>
                <option>Discover Scuba Diving</option>
                <option>Open Water Diver</option>
                <option>Open Water Diver (Junior)</option>
              </optgroup>
              <optgroup label="Continuing">
                <option>Advanced Open Water Diver</option>
                <option>Rescue Diver</option>
                <option>Adventure Diver</option>
                <option>Specialty: Deep</option>
                <option>Specialty: Wreck</option>
                <option>Specialty: Nitrox (Enriched Air)</option>
                <option>Specialty: Peak Performance Buoyancy</option>
                <option>Specialty: Night</option>
                <option>Specialty: Drift</option>
                <option>Specialty: Underwater Photography</option>
              </optgroup>
              <optgroup label="Professional">
                <option>Divemaster</option>
                <option>Assistant Instructor</option>
                <option>Open Water Scuba Instructor</option>
                <option>MSDT (Master Scuba Diver Trainer)</option>
                <option>IDC Staff Instructor</option>
              </optgroup>
              <optgroup label="Freedive/Snorkel">
                <option>Basic Snorkeler</option>
                <option>Freediver Level 1</option>
              </optgroup>
            </select>
          </div>
          <div><label class="block text-sm text-slate-300 mb-1">Dive Date</label><input id="f_dive_date" type="date" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
          <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-sm text-slate-300 mb-1">Pax</label><input id="f_pax" type="number" min="0" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
            <div><label class="block text-sm text-slate-300 mb-1">Budget</label><input id="f_budget" type="number" step="0.01" min="0" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
          </div>
        </div>
      </details>

      <details class="acc">
        <summary><span class="acc-title"><i class="fa-regular fa-credit-card text-slate-400"></i> Payment & Loyalty</span><i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i></summary>
        <div class="acc-body grid grid-cols-3 gap-3">
          <div><label class="block text-sm text-slate-300 mb-1">Payment Status</label>
            <select id="f_payment_status" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100">
              <option value="unpaid">Unpaid</option>
              <option value="deposit">Deposit</option>
              <option value="paid">Paid</option>
              <option value="refunded">Refunded</option>
            </select>
          </div>
          <div><label class="block text-sm text-slate-300 mb-1">Payment Method</label>
            <select id="f_payment_method" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100">
              <option value="">—</option>
              <option value="cash">Cash</option>
              <option value="transfer">Bank Transfer</option>
              <option value="card">Card</option>
              <option value="ewallet">e-Wallet</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div><label class="block text-sm text-slate-300 mb-1">Deposit Paid</label><input id="f_deposit_amount" type="number" step="0.01" min="0" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100" /></div>
        </div>
      </details>

      <!-- Trip History -->
      <details class="acc mt-4">
        <summary>
          <span class="acc-title"><i class="fa-solid fa-timeline text-slate-400"></i> Trip History</span>
          <button id="addTripBtn" class="ml-auto px-2.5 py-1 text-xs rounded bg-emerald-600 text-white hover:bg-emerald-500"><i class="fa-solid fa-plus"></i> Add</button>
        </summary>
        <div class="acc-body"><div id="tripList" class="space-y-2 text-sm"></div></div>
      </details>

      <!-- Contact History -->
      <details class="acc mt-4" open>
        <summary>
          <span class="acc-title"><i class="fa-solid fa-comments text-slate-400"></i> Contact History</span>
          <div class="ml-auto flex items-center gap-2">
            <button id="btnAddContact" class="px-2.5 py-1 text-xs rounded bg-sky-600 text-white hover:bg-sky-500"><i class="fa-solid fa-plus"></i> Add</button>
            <button id="btnImportWA" class="px-2.5 py-1 text-xs rounded bg-amber-600 text-white hover:bg-amber-500"><i class="fa-brands fa-whatsapp"></i> Import</button>
          </div>
        </summary>
        <div class="acc-body space-y-3">
          <form id="contactForm" class="hidden grid grid-cols-1 gap-2 text-sm">
            <div class="grid grid-cols-3 gap-2">
              <div>
                <label class="block text-slate-300 text-xs mb-1">Channel</label>
                <select id="ch_channel" class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-1.5">
                  <option>WhatsApp</option>
                  <option>Email</option>
                  <option>Phone</option>
                  <option>IG</option>
                  <option>Other</option>
                </select>
              </div>
              <div>
                <label class="block text-slate-300 text-xs mb-1">Label</label>
                <select id="ch_label" class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-1.5">
                  <option>Follow Up 1</option>
                  <option>Follow Up 2</option>
                  <option>Follow Up 3</option>
                  <option>Follow Up 4</option>
                  <option>Follow Up 5</option>
                  <option>Closing</option>
                </select>
              </div>
              <div>
                <label class="block text-slate-300 text-xs mb-1">Title</label>
                <input id="ch_title" class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-1.5" placeholder="Opening chat"/>
              </div>
            </div>

            <div>
              <label class="block text-slate-300 text-xs mb-1">Message</label>
              <textarea id="ch_message" rows="3" class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-1.5"></textarea>
              <div class="mt-2 flex items-center gap-2">
                <button id="btnSendWA" class="btn hover:bg-slate-800"><i class="fa-brands fa-whatsapp"></i> Send WhatsApp</button>
                <span class="text-xs text-slate-400">Sends current message to lead’s phone</span>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-slate-300 text-xs mb-1">Contact at</label>
                <input id="ch_contact_at" type="datetime-local" class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-1.5"/>
              </div>
              <div class="flex items-end">
                <button id="ch_save" class="btn hover:bg-slate-800 w-full"><i class="fa-solid fa-floppy-disk"></i> Save Note</button>
              </div>
            </div>
          </form>

          <form id="waImportForm" class="hidden grid grid-cols-1 gap-2 text-sm">
            <div><label class="block text-slate-300 text-xs mb-1">Paste WhatsApp Chat</label><textarea id="wa_raw" rows="6" class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-1.5" placeholder="[12/10/24, 10:22] John Doe: Hello"></textarea></div>
            <div class="flex gap-2">
              <button id="wa_import" class="btn hover:bg-slate-800"><i class="fa-brands fa-whatsapp"></i> Import</button>
              <button type="button" id="wa_cancel" class="btn hover:bg-slate-800"><i class="fa-solid fa-xmark"></i> Cancel</button>
            </div>
          </form>

          <div id="contactList" class="space-y-2 text-sm"></div>
        </div>
      </details>

      <!-- Chat Logs -->
      <details class="acc mt-4" open>
        <summary>
          <span class="acc-title"><i class="fa-solid fa-message text-slate-400"></i> Chat Logs</span>
          <button id="btnReloadChat" class="ml-auto px-2.5 py-1 text-xs rounded bg-slate-800 text-slate-100 hover:bg-slate-700">
            <i class="fa-solid fa-rotate-right"></i> Reload
          </button>
        </summary>
        <div class="acc-body">
          <div id="chatLogsList" class="space-y-2 text-sm"></div>
          <p class="mt-2 text-[11px] text-slate-500">
            Data diambil dari widget chat berdasarkan <b>lead_id</b> dan <b>email</b>.
          </p>
        </div>
      </details>

    </form>
  </div>

  <div class="px-5 py-3 border-t border-slate-800 flex items-center justify-between">
    <div class="text-xs text-slate-400">Changes auto-saved.</div>
    <form id="deleteForm" method="post" action="?action=delete">
      <input type="hidden" name="id" id="del_id">
      <button class="px-3 py-1.5 text-sm rounded bg-rose-600 text-white hover:bg-rose-500" type="submit">
        <i class="fa-regular fa-trash-can mr-1.5"></i> Delete
      </button>
    </form>
  </div>
</aside>

<!-- ===== DIVE CLUB BOTTOM-SHEET ===== -->
<div id="diveClubSheet" role="dialog" aria-modal="true" aria-label="Dive Club Panel">
  <div class="grab"></div>
  <div class="px-4 py-3 border-b border-slate-800 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <i class="fa-solid fa-users-line text-lime-400"></i>
      <h3 class="font-semibold">Dive Club</h3>
      <span id="dcCountSheet" class="text-xs text-slate-400"></span>
    </div>
    <button id="closeDiveSheetBtn" class="btn text-xs hover:bg-slate-800"><i class="fa-solid fa-xmark"></i> Close</button>
  </div>
  <div class="p-4 overflow-y-auto flex-1">
    <div id="diveClubSheetDrop" class="dropzone text-sm mb-3">Put Here to join Dive Club</div>
    <div id="diveClubSheetGrid" class="grid-names grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
      <?php if(empty($diveClub)): ?>
        <div class="text-slate-400 text-sm italic col-span-full">Belum ada anggota Dive Club.</div>
      <?php else: foreach($diveClub as $dc): ?>
        <div class="cursor-pointer" data-diveclub-id="<?= h($dc['id']) ?>" title="Open lead">
          <div class="font-medium truncate"><?= h($dc['name'] ?: '(No Name)') ?></div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>

<!-- Modal: New Lead -->
<div id="addModal" class="modal fixed inset-0 z-[65] flex items-center justify-center">
  <div class="bg-slate-900 rounded-2xl shadow-2xl w-full max-w-2xl border border-slate-800">
    <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
      <h3 class="text-lg font-semibold">New Lead</h3>
      <button id="closeAddModalBtn" class="px-3 py-1.5 text-sm rounded border border-slate-700 hover:bg-slate-800"><i class="fa-solid fa-xmark"></i> Close</button>
    </div>
    <form method="post" action="?action=create" class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2"><label class="block text-sm text-slate-300 mb-1">Name*</label><input name="name" required class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div><label class="block text-sm text-slate-300 mb-1">Email</label><input name="email" type="email" class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div><label class="block text-sm text-slate-300 mb-1">Phone</label><input name="phone" class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div><label class="block text-sm text-slate-300 mb-1">Country</label><input name="country" class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div><label class="block text-sm text-slate-300 mb-1">Source</label><input name="source" class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div><label class="block text-sm text-slate-300 mb-1">Package</label><input name="package" class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div><label class="block text-sm text-slate-300 mb-1">Certification</label><input name="cert" class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div><label class="block text-sm text-slate-300 mb-1">Dive Date</label><input type="date" name="dive_date" class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div><label class="block text-sm text-slate-300 mb-1">Pax</label><input type="number" min="0" name="pax" class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/></div>
      <div class="md:col-span-2 flex gap-3 pt-2">
        <button type="button" id="closeAddModalBtn2" class="px-4 py-2 rounded-lg border border-slate-700 text-slate-200 hover:bg-slate-800"><i class="fa-regular fa-circle-xmark mr-1.5"></i> Cancel</button>
        <button class="px-4 py-2 rounded-lg bg-sky-500 text-slate-900 hover:bg-sky-400"><i class="fa-solid fa-floppy-disk mr-1.5"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Group Members Chooser -->
<div id="groupModal" class="modal fixed inset-0 z-[70] flex items-center justify-center">
  <div class="bg-slate-900 rounded-2xl shadow-2xl w-full max-w-xl border border-slate-800">
    <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
      <h3 class="text-lg font-semibold"><i class="fa-solid fa-list mr-2 text-slate-400"></i> Group Members</h3>
      <button id="groupCloseBtn" class="px-3 py-1.5 text-sm rounded border border-slate-700 hover:bg-slate-800"><i class="fa-solid fa-xmark"></i> Close</button>
    </div>
    <div class="p-4"><div id="groupList" class="space-y-2"></div></div>
    <div class="px-5 py-3 border-t border-slate-800 text-right"><button id="groupCloseBtn2" class="px-3 py-1.5 text-sm rounded border border-slate-700 hover:bg-slate-800">Close</button></div>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-4 left-1/2 -translate-x-1/2 hidden">
  <div class="px-4 py-2 rounded-xl shadow-2xl bg-slate-900 text-white text-sm border border-slate-700"></div>
</div>

<script>
(function(){
  function $(s){ return document.querySelector(s); }
  function $all(s){ return document.querySelectorAll(s); }
  function toast(msg, ok=true){
    const wrap=$('#toast'), box=wrap.firstElementChild;
    box.textContent=msg;
    box.className='px-4 py-2 rounded-xl shadow-2xl text-sm border '+(ok?'bg-slate-900 text-white border-slate-700':'bg-rose-700 text-white border-rose-600');
    wrap.classList.remove('hidden'); clearTimeout(wrap._t); wrap._t=setTimeout(()=>wrap.classList.add('hidden'),1600);
  }
  function esc(t){ const d=document.createElement('div'); d.textContent=(t==null?'':String(t)); return d.textContent; }
  function spn(){ return '<div class="text-slate-400 text-sm flex items-center gap-2"><i class="fa-solid fa-arrows-rotate fa-spin"></i> Loading…</div>'; }
  function err(){ return '<div class="text-rose-400 text-sm">Load failed</div>'; }
  function normalizeMsisdn(p){ return (String(p||'').replace(/\D+/g,'').replace(/^0/,'62')); }

  async function fetchJSON(u){
    const r = await fetch(u, { headers:{'X-Requested-With':'fetch'}, cache:'no-store' });
    if (!r.ok) throw new Error('HTTP '+r.status);
    const ct = r.headers.get('content-type') || '';
    if (!ct.includes('application/json')) throw new Error('Non-JSON response');
    return r.json();
  }
  async function postJSON(u,b){
    const r=await fetch(u,{method:'POST',headers:{'Content-Type':'application/json; charset=UTF-8','X-Requested-With':'fetch'},body:JSON.stringify(b),cache:'no-store'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const ct=r.headers.get('content-type')||''; if(!ct.includes('application/json')) throw new Error('Non-JSON');
    return r.json();
  }
  async function postForm(u,b){
    const r=await fetch(u,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8','X-Requested-With':'fetch'},body:new URLSearchParams(b),cache:'no-store'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const ct=r.headers.get('content-type')||''; if(!ct.includes('application/json')) throw new Error('Non-JSON');
    return r.json();
  }

  /* New Lead modal */
  const addModal=$('#addModal');
  if(addModal){
    const openAdd=()=>{ addModal.classList.add('open'); };
    const closeAdd=()=>{ addModal.classList.remove('open'); };
    $('#openAddModalBtn')?.addEventListener('click', openAdd);
    $('#closeAddModalBtn')?.addEventListener('click', closeAdd);
    $('#closeAddModalBtn2')?.addEventListener('click', closeAdd);
  }

  /* Lead Offcanvas (right) */
  const off=$('#offcanvas');
  const openOff=()=>{ off?.classList.add('open'); document.body.style.overflow='hidden'; };
  const closeOff=()=>{ off?.classList.remove('open'); document.body.style.overflow=''; };
  $('#closeBtn')?.addEventListener('click', closeOff);

  /* MAIN KANBAN drag & drop (board-level) */
  const boards=$all('[data-column]'); let dragEl=null, currentDrop=null, currentId=null;
  boards.forEach(b=>{
    b.addEventListener('dragover', e=>{ e.preventDefault(); if(currentDrop!==b){ if(currentDrop) currentDrop.classList.remove('drop-hint'); currentDrop=b; b.classList.add('drop-hint'); } });
    b.addEventListener('dragleave', e=>{ if(!b.contains(e.relatedTarget)){ b.classList.remove('drop-hint'); if(currentDrop===b) currentDrop=null; } });
    b.addEventListener('drop', async e=>{
      e.preventDefault(); const id=e.dataTransfer.getData('text/plain'); if(!id||!dragEl) return;
      b.classList.remove('drop-hint'); b.appendChild(dragEl);
      const to=b.dataset.column||'leads';
      try{
        const j=await postForm('?action=move',{id, to});
        if(j.ok){ toast('Moved to '+to,true); if(currentId && currentId===id){ const col=document.getElementById('f_column'); if(col) col.value=to; } }
        else { toast('Move failed',false); setTimeout(()=>location.reload(),600); }
      }catch(e){ toast('Network error',false); setTimeout(()=>location.reload(),600); }
    });
  });
  document.addEventListener('dragstart', e=>{
    const c=e.target.closest('.kanban-card'); if(!c || !c.hasAttribute('data-id')) return;
    dragEl=c; c.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; e.dataTransfer.setData('text/plain', c.dataset.id);
  });
  document.addEventListener('dragend', ()=>{ if(dragEl) dragEl.classList.remove('dragging'); if(currentDrop) currentDrop.classList.remove('drop-hint'); dragEl=null; currentDrop=null; });

  /* Open single card from main kanban */
  $all('.kanban-card[data-id]').forEach(card=>{
    card.addEventListener('click', async (e)=>{ if(e.target.closest('a,button')) return; await openLead(card.dataset.id); });
  });

  /* BOOKING STATUS KANBAN drag & drop (sub-board) */
  const statusBoards=$all('[data-status-board]');
  let dragStatusEl=null, currentStatusDrop=null;
  if(statusBoards.length){
    document.addEventListener('dragstart', e=>{
      const c=e.target.closest('.status-card'); if(!c || !c.hasAttribute('data-id')) return;
      dragStatusEl=c; c.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; e.dataTransfer.setData('text/plain', c.dataset.id);
    });
    document.addEventListener('dragend', e=>{
      if(dragStatusEl) dragStatusEl.classList.remove('dragging');
      if(currentStatusDrop) currentStatusDrop.classList.remove('drop-hint');
      dragStatusEl=null; currentStatusDrop=null;
    });
    statusBoards.forEach(b=>{
      b
