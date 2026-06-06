<?php
/************************************************************
 * crm.php — Single-file Lightweight CRM for Bali Diving
 * - Uses the same DB (u1783223_bd_crm) and leads table
 * - Auto-migrate: add missing helpful CRM columns
 * - Features: List, Search, Filter, Create/Edit, Notes, Timeline,
 *             Status pipeline, Quick Email, Export CSV, JSON API
 ************************************************************/

session_start();

/* ========= DB CONFIG (pakai yang sama) ========= */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/* ========= SIMPLE AUTH (opsional; matikan jika tak perlu) ========= */
if (!isset($_SESSION['crm_user'])) {
  // Auto-login dev mode (ubah ke form login jika perlu)
  $_SESSION['crm_user'] = 'admin@balidiving.com';
  $_SESSION['crm_role'] = 'admin';
}

/* ========= PDO ========= */
function pdo_conn(){
  static $pdo=null;
  if($pdo===null){
    $dsn="mysql:host=".$GLOBALS['DB_HOST'].";dbname=".$GLOBALS['DB_NAME'].";charset=utf8mb4";
    $opt=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];
    $pdo=new PDO($dsn,$GLOBALS['DB_USER'],$GLOBALS['DB_PASS'],$opt);
  }
  return $pdo;
}

/* ========= UTIL ========= */
function h($s){ return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
function uid(){ return strtoupper(dechex(time())).substr(strtoupper(md5(uniqid('',true))),0,8); }
function booking_code(){
  return 'BD-'.date('ymd').'-'.strtoupper(substr(base_convert((string)mt_rand(100000,999999),10,36),0,5));
}
function now(){ return date('Y-m-d H:i:s'); }
function csrf_token(){
  if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16));
  return $_SESSION['csrf'];
}
function check_csrf($t){ if(!hash_equals($_SESSION['csrf']??'', $t??'')) throw new Exception('Invalid CSRF'); }

/* ========= SCHEMA ENSURE ========= */
function table_exists(PDO $p,$t){$st=$p->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=:t");$st->execute([':t'=>$t]);return (bool)$st->fetchColumn();}
function col_exists(PDO $p,$t,$c){$st=$p->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name=:t AND column_name=:c");$st->execute([':t'=>$t,':c'=>$c]);return (bool)$st->fetchColumn();}
function qexec(PDO $p,$sql){ try{$p->exec($sql);}catch(Throwable $e){ error_log($e->getMessage()); } }

function ensure_schema(){
  $pdo=pdo_conn();
  // leads table (create if missing – compatible superset)
  if(!table_exists($pdo,'leads')){
    qexec($pdo,"CREATE TABLE `leads`(
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
      notes TEXT NULL,
      created_at DATETIME NOT NULL,
      updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  }
  // Helpful CRM columns
  $need=[
    'status'            => "ALTER TABLE `leads` ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'new'",
    'booking_code'      => "ALTER TABLE `leads` ADD COLUMN booking_code VARCHAR(32) NULL",
    'assigned_to'       => "ALTER TABLE `leads` ADD COLUMN assigned_to VARCHAR(255) NULL",
    'last_contact_at'   => "ALTER TABLE `leads` ADD COLUMN last_contact_at DATETIME NULL",
    'tags'              => "ALTER TABLE `leads` ADD COLUMN tags VARCHAR(255) NULL",
    'utm_source'        => "ALTER TABLE `leads` ADD COLUMN utm_source VARCHAR(64) NULL",
    'utm_campaign'      => "ALTER TABLE `leads` ADD COLUMN utm_campaign VARCHAR(64) NULL",
    'utm_medium'        => "ALTER TABLE `leads` ADD COLUMN utm_medium VARCHAR(64) NULL",
  ];
  foreach($need as $c=>$sql){ if(!col_exists($pdo,'leads',$c)) qexec($pdo,$sql); }

  // Notes & timeline
  qexec($pdo,"CREATE TABLE IF NOT EXISTS lead_notes(
    id VARCHAR(64) PRIMARY KEY,
    lead_id VARCHAR(64) NOT NULL,
    author VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX(lead_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  qexec($pdo,"CREATE TABLE IF NOT EXISTS lead_events(
    id VARCHAR(64) PRIMARY KEY,
    lead_id VARCHAR(64) NOT NULL,
    event_type VARCHAR(64) NOT NULL,
    payload JSON NULL,
    created_at DATETIME NOT NULL,
    INDEX(lead_id), INDEX(event_type)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}
ensure_schema();

/* ========= ACTIONS (HTML + JSON API) ========= */
$act = $_POST['action'] ?? $_GET['action'] ?? '';
try{
  switch($act){

    case 'create_lead': {
      check_csrf($_POST['csrf'] ?? '');
      $pdo=pdo_conn();
      $id=uid();
      $now=now();
      $name=trim($_POST['name']??'');
      if($name==='') throw new Exception('Name is required');
      $email=trim($_POST['email']??'');
      $phone=trim($_POST['phone']??'');
      $source=trim($_POST['source']??'Website');
      $package=trim($_POST['package']??'');
      $cert=trim($_POST['cert']??'');
      $dive_date=($_POST['dive_date']??'') ?: null;
      $pax=(int)($_POST['pax']??1);
      $budget=(float)($_POST['budget']??0);
      $status=trim($_POST['status']??'new');
      $assigned=$_POST['assigned_to']??'';
      $tags=trim($_POST['tags']??'');
      $booking=$_POST['booking_code']??booking_code();

      $sql="INSERT INTO leads(id,`column`,name,email,phone,country,source,package,cert,dive_date,pax,budget,
            payment_status,activity,brand,notes,created_at,updated_at,status,assigned_to,booking_code,tags,last_contact_at)
            VALUES(:id,'leads',:name,:email,:phone,NULL,:source,:package,:cert,:dive_date,:pax,:budget,
            'unpaid','Course','BALI DIVING','',:ca,:ua,:status,:assigned_to,:booking,:tags,NULL)";
      $st=$pdo->prepare($sql);
      $st->execute([
        ':id'=>$id,':name'=>$name,':email'=>$email,':phone'=>$phone,':source'=>$source,':package'=>$package,':cert'=>$cert,
        ':dive_date'=>$dive_date,':pax'=>$pax,':budget'=>$budget,':ca'=>$now,':ua'=>$now,':status'=>$status,
        ':assigned_to'=>$assigned,':booking'=>$booking,':tags'=>$tags
      ]);

      log_event($id,'created',['by'=>$_SESSION['crm_user'],'status'=>$status,'booking_code'=>$booking]);

      // Optional quick email notify
      if(filter_var($email,FILTER_VALIDATE_EMAIL)){
        quick_mail('subhi@balidiving.com','New Lead: '.$name,email_html([
          'Title'=>'New Lead Created',
          'Name'=>$name,'Email'=>$email,'Phone'=>$phone,'Source'=>$source,'Package'=>$package,'Cert'=>$cert,'Booking Code'=>$booking
        ]), 'admin@balidiving.com', $email);
      }

      respond(['ok'=>true,'id'=>$id,'booking_code'=>$booking]);
    } break;

    case 'update_lead': {
      check_csrf($_POST['csrf'] ?? '');
      $pdo=pdo_conn();
      $id=$_POST['id']??'';
      if($id==='') throw new Exception('Missing id');

      $fields=['name','email','phone','source','package','cert','dive_date','pax','budget','status','assigned_to','tags','booking_code','notes','payment_status'];
      $set=[]; $params=[':id'=>$id,':ua'=>now()];
      foreach($fields as $f){
        if(array_key_exists($f,$_POST)){
          $set[] = "$f = :$f";
          $params[":$f"] = ($_POST[$f]===''? null: $_POST[$f]);
        }
      }
      if(empty($set)) throw new Exception('Nothing to update');
      $sql="UPDATE leads SET ".implode(', ',$set).", updated_at=:ua WHERE id=:id";
      $pdo->prepare($sql)->execute($params);

      log_event($id,'updated',['by'=>$_SESSION['crm_user'],'fields'=>array_keys($_POST)]);

      respond(['ok'=>true,'id'=>$id]);
    } break;

    case 'change_status': {
      check_csrf($_POST['csrf'] ?? '');
      $pdo=pdo_conn();
      $id=$_POST['id']??''; $status=$_POST['status']??'';
      if(!$id || !$status) throw new Exception('Missing id/status');
      $st=$pdo->prepare("UPDATE leads SET status=:s, updated_at=:u WHERE id=:i");
      $st->execute([':s'=>$status,':u'=>now(),':i'=>$id]);
      if(in_array($status,['booked','paid']) && !col_exists($pdo,'leads','booking_code')){ qexec($pdo,"ALTER TABLE leads ADD COLUMN booking_code VARCHAR(32) NULL"); }
      if(in_array($status,['booked','paid'])){
        $code = booking_code();
        $pdo->prepare("UPDATE leads SET booking_code=:c WHERE id=:i AND (booking_code IS NULL OR booking_code='')")->execute([':c'=>$code,':i'=>$id]);
      }
      log_event($id,'status_changed',['to'=>$status,'by'=>$_SESSION['crm_user']]);
      respond(['ok'=>true]);
    } break;

    case 'add_note': {
      check_csrf($_POST['csrf'] ?? '');
      $pdo=pdo_conn();
      $id=$_POST['lead_id']??''; $content=trim($_POST['content']??'');
      if(!$id || $content==='') throw new Exception('Missing');
      $nid=uid();
      $pdo->prepare("INSERT INTO lead_notes(id,lead_id,author,content,created_at) VALUES(?,?,?,?,?)")
          ->execute([$nid,$id,$_SESSION['crm_user'],$content,now()]);
      $pdo->prepare("UPDATE leads SET last_contact_at=:t, updated_at=:t WHERE id=:i")->execute([':t'=>now(),':i'=>$id]);
      log_event($id,'note_added',['by'=>$_SESSION['crm_user']]);
      respond(['ok'=>true]);
    } break;

    case 'list_leads': {
      // JSON for async table (search/filter/pagination)
      $pdo=pdo_conn();
      $q=trim($_GET['q']??'');
      $status=$_GET['status']??'';
      $assigned=$_GET['assigned']??'';
      $date_from=$_GET['from']??'';
      $date_to=$_GET['to']??'';
      $page=max(1,(int)($_GET['page']??1));
      $per=min(100,max(10,(int)($_GET['per']??20)));
      $off=($page-1)*$per;

      $where=['1=1']; $p=[];
      if($q!==''){ $where[]="(name LIKE :q OR email LIKE :q OR phone LIKE :q OR booking_code LIKE :q OR package LIKE :q OR tags LIKE :q)"; $p[':q']="%$q%"; }
      if($status!==''){ $where[]="status=:st"; $p[':st']=$status; }
      if($assigned!==''){ $where[]="assigned_to=:as"; $p[':as']=$assigned; }
      if($date_from!==''){ $where[]="DATE(created_at)>=:df"; $p[':df']=$date_from; }
      if($date_to!==''){ $where[]="DATE(created_at)<=:dt"; $p[':dt']=$date_to; }

      $sql_base="FROM leads WHERE ".implode(' AND ',$where);
      $total = (int)pdo_conn()->prepare("SELECT COUNT(*) ".$sql_base)->execute($p) ?: 0;
      $stc=pdo_conn()->prepare("SELECT COUNT(*) ".$sql_base);
      $stc->execute($p);
      $cnt=(int)$stc->fetchColumn();

      $sql="SELECT * ".$sql_base." ORDER BY updated_at DESC LIMIT $per OFFSET $off";
      $st=pdo_conn()->prepare($sql); $st->execute($p);
      $rows=$st->fetchAll();

      respond(['ok'=>true,'rows'=>$rows,'page'=>$page,'per'=>$per,'total'=>$cnt]);
    } break;

    case 'lead_detail': {
      $pdo=pdo_conn();
      $id=$_GET['id']??'';
      if(!$id) throw new Exception('Missing id');
      $lead=$pdo->prepare("SELECT * FROM leads WHERE id=?"); $lead->execute([$id]); $lead=$lead->fetch();
      $notes=$pdo->prepare("SELECT * FROM lead_notes WHERE lead_id=? ORDER BY created_at DESC"); $notes->execute([$id]); $notes=$notes->fetchAll();
      $events=$pdo->prepare("SELECT * FROM lead_events WHERE lead_id=? ORDER BY created_at DESC LIMIT 50"); $events->execute([$id]); $events=$events->fetchAll();
      respond(['ok'=>true,'lead'=>$lead,'notes'=>$notes,'events'=>$events]);
    } break;

    case 'export_csv': {
      // Export current filter to CSV
      $pdo=pdo_conn();
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename=leads_'.date('Ymd_His').'.csv');
      $out=fopen('php://output','w');
      fputcsv($out,['id','name','email','phone','source','package','cert','dive_date','pax','budget','status','booking_code','assigned_to','tags','created_at','updated_at']);
      $st=$pdo->query("SELECT id,name,email,phone,source,package,cert,dive_date,pax,budget,status,booking_code,assigned_to,tags,created_at,updated_at FROM leads ORDER BY created_at DESC");
      while($r=$st->fetch(PDO::FETCH_NUM)){ fputcsv($out,$r); }
      exit;
    } break;

    case 'quick_email': {
      check_csrf($_POST['csrf'] ?? '');
      $to=$_POST['to']??''; $subject=$_POST['subject']??''; $message=$_POST['message']??'';
      if(!filter_var($to,FILTER_VALIDATE_EMAIL)) throw new Exception('Invalid recipient');
      quick_mail($to,$subject,nl2br($message),'subhi@balidiving.com');
      respond(['ok'=>true]);
    } break;

    default: /* no-op */ ;
  }
}catch(Throwable $e){
  respond(['ok'=>false,'error'=>$e->getMessage()]);
}

/* ========= MAIL ========= */
function quick_mail($to,$subject,$html,$cc=null,$replyTo=null){
  $h=[];
  $h[]='From: Bali Diving CRM <no-reply@balidiving.com>';
  if($replyTo) $h[]='Reply-To: '.$replyTo;
  if($cc) $h[]='Cc: '.$cc;
  $h[]='MIME-Version: 1.0';
  $h[]='Content-Type: text/html; charset=UTF-8';
  @mail($to,$subject,$html,implode("\r\n",$h));
}
function email_html($arr){
  $rows=''; foreach($arr as $k=>$v){ $rows.='<tr><td><b>'.h($k).'</b></td><td>'.h((string)$v).'</td></tr>'; }
  return '<html><body style="font-family:Arial"><table cellpadding="6" style="border:1px solid #eee">'.$rows.'</table></body></html>';
}

/* ========= EVENT LOG ========= */
function log_event($lead_id,$type,$payload=[]){
  $pdo=pdo_conn(); $id=uid();
  $st=$pdo->prepare("INSERT INTO lead_events(id,lead_id,event_type,payload,created_at) VALUES(?,?,?,?,?)");
  $st->execute([$id,$lead_id,$type,json_encode($payload,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),now()]);
}

/* ========= RENDER HTML ========= */
function respond($arr){
  if( isset($_GET['api']) || isset($_POST['action']) || (($_GET['action']??'') && $_GET['action']!=='') ){
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($arr); exit;
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bali Diving — CRM Leads</title>
<link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
 theme:{ extend:{ colors:{
  primary:'#3552c8', secondary:'#f23d4e', accent:'#0070d3', teal:'#23a0b4',
  gold:'#eebe35', lightblue:'#a2d2fa', navy:'#063c7f'
 }}}}
</script>
<style>
.badge{padding:.2rem .5rem;border-radius:.5rem;font-size:.75rem}
.status-new{background:#e5edff;color:#3552c8}
.status-contacted{background:#eafaf0;color:#1b7a3a}
.status-booked{background:#fff7e6;color:#8a5b00}
.status-paid{background:#e6fffb;color:#036666}
.status-cancelled{background:#fde2e1;color:#9b2226}
.table-fixed th, .table-fixed td { white-space: nowrap; }
input,select,textarea{outline:none}
</style>
</head>
<body class="bg-slate-50 text-slate-800">
<!-- Top Bar -->
<header class="bg-navy text-white shadow">
  <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <img src="bali-diving-logo.svg" class="h-8" alt="Bali Diving">
      <h1 class="font-bold text-lg">Bali Diving — CRM</h1>
      <span class="text-lightblue text-sm hidden md:inline">Leads & Pipeline</span>
    </div>
    <div class="text-sm">Signed in as <b><?=h($_SESSION['crm_user'])?></b></div>
  </div>
</header>

<!-- Filters -->
<section class="max-w-7xl mx-auto px-4 mt-6">
  <div class="bg-white rounded-2xl shadow p-4">
    <form id="filterForm" class="grid md:grid-cols-6 gap-3 items-end">
      <div class="md:col-span-2">
        <label class="text-xs text-slate-500">Search</label>
        <input name="q" id="q" class="w-full border rounded-lg px-3 py-2" placeholder="name, email, phone, booking, tags">
      </div>
      <div>
        <label class="text-xs text-slate-500">Status</label>
        <select name="status" id="status" class="w-full border rounded-lg px-3 py-2">
          <option value="">All</option>
          <option>new</option><option>contacted</option><option>booked</option><option>paid</option><option>cancelled</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-slate-500">Assigned</label>
        <input name="assigned" id="assigned" class="w-full border rounded-lg px-3 py-2" placeholder="email or name">
      </div>
      <div>
        <label class="text-xs text-slate-500">From</label>
        <input type="date" name="from" id="from" class="w-full border rounded-lg px-3 py-2">
      </div>
      <div>
        <label class="text-xs text-slate-500">To</label>
        <input type="date" name="to" id="to" class="w-full border rounded-lg px-3 py-2">
      </div>
      <div class="md:col-span-6 flex gap-2">
        <button type="button" onclick="loadLeads(1)" class="px-4 py-2 bg-primary text-white rounded-lg">Apply</button>
        <a href="?action=export_csv" class="px-4 py-2 bg-gold text-navy rounded-lg">Export CSV</a>
        <button type="button" onclick="openCreate()" class="ml-auto px-4 py-2 bg-teal text-white rounded-lg">+ Add Lead</button>
      </div>
    </form>
  </div>
</section>

<!-- Pipeline (quick counts) -->
<section class="max-w-7xl mx-auto px-4 mt-6">
  <div id="pipeline" class="grid md:grid-cols-5 gap-3"></div>
</section>

<!-- Table -->
<section class="max-w-7xl mx-auto px-4 mt-4">
  <div class="bg-white rounded-2xl shadow">
    <div class="overflow-auto">
      <table class="min-w-full table-fixed">
        <thead class="bg-slate-100 text-xs uppercase text-slate-500 sticky top-0">
          <tr>
            <th class="px-3 py-2">Updated</th>
            <th class="px-3 py-2">Name</th>
            <th class="px-3 py-2">Contact</th>
            <th class="px-3 py-2">Pkg/Cert</th>
            <th class="px-3 py-2">Status</th>
            <th class="px-3 py-2">Booking</th>
            <th class="px-3 py-2">Assigned</th>
            <th class="px-3 py-2">Tags</th>
            <th class="px-3 py-2">Actions</th>
          </tr>
        </thead>
        <tbody id="leadRows" class="text-sm"></tbody>
      </table>
    </div>
    <div id="pager" class="flex items-center justify-between p-3 text-sm"></div>
  </div>
</section>

<!-- Modals -->
<div id="modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center p-4 z-50">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 bg-navy text-white">
      <h3 id="modalTitle" class="font-semibold">Create Lead</h3>
      <button onclick="closeModal()" class="text-white/80 hover:text-white">✕</button>
    </div>
    <div id="modalBody" class="p-5"></div>
  </div>
</div>

<!-- Lead Detail Drawer -->
<div id="drawer" class="fixed top-0 right-0 w-full max-w-xl h-full bg-white shadow-2xl translate-x-full transition-transform z-50">
  <div class="flex items-center justify-between px-5 py-3 bg-navy text-white">
    <h3 id="drawerTitle" class="font-semibold">Lead Detail</h3>
    <button onclick="toggleDrawer(false)" class="text-white/80 hover:text-white">✕</button>
  </div>
  <div id="drawerBody" class="p-5 overflow-y-auto" style="height: calc(100% - 48px)"></div>
</div>

<script>
const CSRF = "<?=h(csrf_token())?>";
let state = { page:1, per:20, rows:[], total:0 };

function badge(status){
  const cls = {
    new:'status-new', contacted:'status-contacted',
    booked:'status-booked', paid:'status-paid', cancelled:'status-cancelled'
  }[status] || 'status-new';
  return `<span class="badge ${cls}">${status}</span>`;
}

async function loadLeads(page=1){
  const f = new FormData(document.getElementById('filterForm'));
  const params = new URLSearchParams({ action:'list_leads', api:1, page, per:state.per });
  ['q','status','assigned','from','to'].forEach(k=>{ if(f.get(k)) params.set(k,f.get(k)) });
  const r = await fetch('?'+params.toString()); const j = await r.json();
  if(!j.ok){ alert(j.error||'Load error'); return; }
  state.page=j.page; state.total=j.total; state.rows=j.rows;
  renderRows(); renderPager(); renderPipeline();
}

function renderRows(){
  const tb = document.getElementById('leadRows'); tb.innerHTML='';
  for(const r of state.rows){
    const tr = document.createElement('tr'); tr.className='border-b';
    tr.innerHTML = `
      <td class="px-3 py-2 text-xs text-slate-500">${escapeHtml(r.updated_at||'')}</td>
      <td class="px-3 py-2">
        <div class="font-semibold">${escapeHtml(r.name||'-')}</div>
        <div class="text-xs text-slate-500">${escapeHtml(r.source||'')}</div>
      </td>
      <td class="px-3 py-2">
        <div>${escapeHtml(r.email||'-')}</div>
        <div class="text-xs text-slate-500">${escapeHtml(r.phone||'')}</div>
      </td>
      <td class="px-3 py-2">
        <div>${escapeHtml(r.package||'-')}</div>
        <div class="text-xs text-slate-500">${escapeHtml(r.cert||'')}</div>
      </td>
      <td class="px-3 py-2">${badge(r.status||'new')}</td>
      <td class="px-3 py-2">${escapeHtml(r.booking_code||'')}</td>
      <td class="px-3 py-2">${escapeHtml(r.assigned_to||'')}</td>
      <td class="px-3 py-2">${escapeHtml(r.tags||'')}</td>
      <td class="px-3 py-2">
        <div class="flex gap-2">
          <button class="px-2 py-1 rounded bg-lightblue text-navy text-xs" onclick="openEdit('${r.id}')">Edit</button>
          <button class="px-2 py-1 rounded bg-primary text-white text-xs" onclick="openDetail('${r.id}')">View</button>
          <div class="relative">
            <select class="px-2 py-1 border rounded text-xs" onchange="quickStatus('${r.id}', this.value)">
              <option value="">Set Status</option>
              <option value="new">new</option>
              <option value="contacted">contacted</option>
              <option value="booked">booked</option>
              <option value="paid">paid</option>
              <option value="cancelled">cancelled</option>
            </select>
          </div>
        </div>
      </td>
    `;
    tb.appendChild(tr);
  }
}

function renderPager(){
  const pg=document.getElementById('pager');
  const pages = Math.max(1, Math.ceil(state.total / state.per));
  pg.innerHTML = `
    <div>Total: <b>${state.total}</b></div>
    <div class="flex items-center gap-2">
      <button class="px-3 py-1 border rounded" ${state.page<=1?'disabled':''} onclick="loadLeads(${state.page-1})">Prev</button>
      <span>Page <b>${state.page}</b> / ${pages}</span>
      <button class="px-3 py-1 border rounded" ${state.page>=pages?'disabled':''} onclick="loadLeads(${state.page+1})">Next</button>
    </div>
  `;
}

function renderPipeline(){
  const counts = {new:0,contacted:0,booked:0,paid:0,cancelled:0};
  for(const r of state.rows){ if(counts[r.status]!==undefined) counts[r.status]++; }
  const map = [
    ['new','New'],['contacted','Contacted'],['booked','Booked'],['paid','Paid'],['cancelled','Cancelled']
  ];
  const box=document.getElementById('pipeline'); box.innerHTML='';
  for(const [k,label] of map){
    const d=document.createElement('div');
    d.className='rounded-xl border bg-white p-4 flex items-center justify-between';
    d.innerHTML=`
      <div>
        <div class="text-xs text-slate-500">${label}</div>
        <div class="text-2xl font-bold">${counts[k]}</div>
      </div>
      <div>${badge(k)}</div>
    `;
    box.appendChild(d);
  }
}

function openCreate(){
  document.getElementById('modalTitle').textContent='Create Lead';
  document.getElementById('modalBody').innerHTML=formHtml();
  openModal();
}
function openEdit(id){
  const r = state.rows.find(x=>x.id===id);
  document.getElementById('modalTitle').textContent='Edit Lead';
  document.getElementById('modalBody').innerHTML=formHtml(r);
  openModal();
}

function formHtml(r={}){
  return `
  <form onsubmit="return saveLead(event)">
    <input type="hidden" name="csrf" value="${CSRF}">
    ${r.id?`<input type="hidden" name="id" value="${escapeHtml(r.id)}">`:''}
    <div class="grid md:grid-cols-2 gap-3">
      <div><label class="text-xs">Name</label><input name="name" required value="${escapeHtml(r.name||'')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Email</label><input name="email" type="email" value="${escapeHtml(r.email||'')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Phone</label><input name="phone" value="${escapeHtml(r.phone||'')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Source</label><input name="source" value="${escapeHtml(r.source||'Website')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Package</label><input name="package" value="${escapeHtml(r.package||'')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Cert/Experience</label><input name="cert" value="${escapeHtml(r.cert||'')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Dive Date</label><input type="date" name="dive_date" value="${escapeHtml(r.dive_date||'')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Pax</label><input name="pax" type="number" value="${escapeHtml(r.pax||1)}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Budget</label><input name="budget" type="number" step="0.01" value="${escapeHtml(r.budget||0)}" class="w-full border rounded px-3 py-2"></div>
      <div>
        <label class="text-xs">Status</label>
        <select name="status" class="w-full border rounded px-3 py-2">
          ${['new','contacted','booked','paid','cancelled'].map(s=>`<option ${r.status===s?'selected':''}>${s}</option>`).join('')}
        </select>
      </div>
      <div><label class="text-xs">Assigned To</label><input name="assigned_to" value="${escapeHtml(r.assigned_to||'')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Tags (comma)</label><input name="tags" value="${escapeHtml(r.tags||'')}" class="w-full border rounded px-3 py-2"></div>
      <div><label class="text-xs">Booking Code</label><input name="booking_code" value="${escapeHtml(r.booking_code||'')}" placeholder="auto when booked/paid" class="w-full border rounded px-3 py-2"></div>
      <div class="md:col-span-2"><label class="text-xs">Notes</label><textarea name="notes" rows="3" class="w-full border rounded px-3 py-2">${escapeHtml(r.notes||'')}</textarea></div>
    </div>
    <div class="mt-4 flex justify-end gap-2">
      <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded">Cancel</button>
      <button class="px-4 py-2 bg-primary text-white rounded">${r.id?'Save Changes':'Create Lead'}</button>
    </div>
  </form>`;
}

async function saveLead(e){
  e.preventDefault();
  const fd=new FormData(e.target);
  const action = fd.get('id') ? 'update_lead' : 'create_lead';
  fd.append('action', action);
  const r = await fetch('', {method:'POST', body:fd});
  const j = await r.json();
  if(!j.ok){ alert(j.error||'Save failed'); return; }
  closeModal(); loadLeads();
}

async function quickStatus(id, status){
  if(!status) return;
  const fd=new FormData(); fd.append('action','change_status'); fd.append('id',id); fd.append('status',status); fd.append('csrf',CSRF);
  const r=await fetch('',{method:'POST',body:fd}); const j=await r.json();
  if(!j.ok){ alert(j.error||'Status failed'); }
  loadLeads(state.page);
}

async function openDetail(id){
  toggleDrawer(true);
  document.getElementById('drawerTitle').textContent='Lead Detail';
  const r=await fetch('?action=lead_detail&api=1&id='+encodeURIComponent(id)); const j=await r.json();
  if(!j.ok){ document.getElementById('drawerBody').innerHTML='<p class="text-red-600">Error</p>'; return; }
  const L=j.lead, notes=j.notes, events=j.events;
  document.getElementById('drawerBody').innerHTML = detailHtml(L,notes,events);
}

function detailHtml(L,notes,events){
  return `
  <div class="grid md:grid-cols-2 gap-4">
    <div class="bg-slate-50 p-3 rounded">
      <div class="text-xs text-slate-500">Booking Code</div>
      <div class="font-mono">${escapeHtml(L.booking_code||'-')}</div>
      <div class="mt-3 text-xs text-slate-500">Status</div>
      <div>${badge(L.status||'new')}</div>
      <div class="mt-3 text-xs text-slate-500">Assigned</div>
      <div>${escapeHtml(L.assigned_to||'-')}</div>
      <div class="mt-3 text-xs text-slate-500">Tags</div>
      <div>${escapeHtml(L.tags||'-')}</div>
    </div>
    <div class="bg-slate-50 p-3 rounded">
      <div class="text-xs text-slate-500">Contact</div>
      <div class="font-semibold">${escapeHtml(L.name||'-')}</div>
      <div>${escapeHtml(L.email||'-')}</div>
      <div>${escapeHtml(L.phone||'-')}</div>
      <div class="mt-3 text-xs text-slate-500">Package / Cert</div>
      <div>${escapeHtml(L.package||'-')} / ${escapeHtml(L.cert||'-')}</div>
      <div class="mt-3 text-xs text-slate-500">Dive Date</div>
      <div>${escapeHtml(L.dive_date||'-')}</div>
    </div>
  </div>
  <div class="mt-4">
    <div class="text-sm font-semibold">Notes</div>
    <div class="space-y-2 mt-2">
      ${notes.map(n=>`
        <div class="p-3 border rounded">
          <div class="text-xs text-slate-500">${escapeHtml(n.created_at)} — ${escapeHtml(n.author)}</div>
          <div>${escapeHtml(n.content)}</div>
        </div>`).join('') || '<div class="text-slate-500 text-sm">No notes</div>'}
    </div>
    <form class="mt-3" onsubmit="return addNote('${L.id}', this)">
      <input type="hidden" name="csrf" value="${CSRF}">
      <textarea name="content" rows="2" class="w-full border rounded px-3 py-2" placeholder="Add a note..."></textarea>
      <div class="flex justify-end mt-2"><button class="px-3 py-1 bg-primary text-white rounded">Add Note</button></div>
    </form>
  </div>
  <div class="mt-6">
    <div class="text-sm font-semibold">Quick Email</div>
    <form class="grid gap-2 mt-2" onsubmit="return quickEmail('${L.email||''}', this)">
      <input type="hidden" name="csrf" value="${CSRF}">
      <input name="subject" class="border rounded px-3 py-2" placeholder="Subject (e.g. Your Bali Diving booking)">
      <textarea name="message" rows="3" class="border rounded px-3 py-2" placeholder="Message"></textarea>
      <div class="flex justify-end"><button class="px-3 py-1 bg-teal text-white rounded">Send</button></div>
    </form>
  </div>
  <div class="mt-6">
    <div class="text-sm font-semibold">Timeline</div>
    <div class="mt-2 space-y-2">
      ${events.map(e=>`
        <div class="text-xs p-2 border rounded">
          <div class="text-slate-500">${escapeHtml(e.created_at)} — ${escapeHtml(e.event_type)}</div>
          <pre class="whitespace-pre-wrap">${escapeHtml(e.payload||'')}</pre>
        </div>`).join('')}
    </div>
  </div>`;
}

async function addNote(lead_id, form){
  const fd=new FormData(form);
  if(!fd.get('content')) return false;
  fd.append('action','add_note'); fd.append('lead_id',lead_id);
  const r=await fetch('',{method:'POST',body:fd}); const j=await r.json();
  if(!j.ok){ alert(j.error||'Add note failed'); return false; }
  openDetail(lead_id); // reload
  return false;
}

async function quickEmail(to, form){
  const fd=new FormData(form);
  fd.append('action','quick_email'); fd.append('to',to);
  const r=await fetch('',{method:'POST',body:fd}); const j=await r.json();
  if(!j.ok){ alert(j.error||'Email failed'); return false; }
  alert('Email sent');
  return false;
}

function openModal(){ const m=document.getElementById('modal'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeModal(){ const m=document.getElementById('modal'); m.classList.add('hidden'); m.classList.remove('flex'); }
function toggleDrawer(show){ const d=document.getElementById('drawer'); d.style.transform = show?'translateX(0)':'translateX(100%)'; }

function escapeHtml(s){ return (s??'').toString().replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m])); }

// Init
loadLeads(1);
</script>

<footer class="max-w-7xl mx-auto px-4 py-8 text-center text-xs text-slate-500">
  © <?=date('Y')?> Bali Diving CRM
</footer>
</body>
</html>
