<?php
/* =========================================================
   BALI DIVING — Dive Club Directory (Editable + WA EN ASCII)
   - Columns: Name, Email, Phone, Certification (dropdown),
              Tier (dropdown), Points (number)
   - Editable (autosave on change, debounce)
   - Filter, Sort, Pagination, Export CSV
   - WA Button: English message (ASCII only)
   - Table borders: white
   - Auth: restrict to @balidiving.com (optional, mirrored)
   ========================================================= */

declare(strict_types=1);
session_start();

/* ---------- AUTH (optional) ---------- */
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
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function agent_name_from_email(string $email): string {
  $email = trim(strtolower($email));
  if ($email === '') return 'BALI DIVING Team';
  $local = explode('@', $email, 2)[0] ?? '';
  $local = preg_replace('/[^a-z0-9._-]+/', ' ', $local);
  $local = trim(preg_replace('/[._-]+/', ' ', $local));
  if ($local === '') return 'BALI DIVING Team';
  return ucwords($local);
}
/* English, ASCII-only WhatsApp message */
function build_wa_message(string $custName, ?string $tier, ?int $points, string $agentName): string {
  $name = trim($custName) !== '' ? $custName : 'Diver Friend';
  $t = $tier && $tier !== '' ? $tier : 'Member';
  $p = (int)($points ?? 0);
  $lines = [
    "Hello {$name}, how are you?",
    "This is {$agentName} from BALI DIVING. You are registered as a Bali Diving Club Member. Tier: {$t}. Points: {$p}.",
    "Our diving family in Bali is ready to welcome you for your next trip, from Tulamben to Nusa Penida and Amed.",
    "When is a good time to chat?"
  ];
  return implode("\n", $lines);
}
function wa_member_link(array $row, string $agentEmail): string {
  $phone = (string)($row['phone'] ?? '');
  $digits = preg_replace('/\D+/', '', $phone);
  if ($digits === '') return '';
  if ($digits[0] === '0') $digits = '62' . substr($digits, 1); // normalize ID numbers
  $agent = agent_name_from_email($agentEmail);
  $msg = build_wa_message((string)($row['name'] ?? ''), (string)($row['tier'] ?? ''), (int)($row['points'] ?? 0), $agent);
  $txt = urlencode($msg);
  return "https://wa.me/{$digits}?text={$txt}";
}

/* ---------- Dropdown Options ---------- */
$CERT_OPTIONS = ['Non-Cert','Open Water','Advanced Open Water','Rescue','Divemaster','Instructor'];
$TIER_OPTIONS = ['Bronze','Silver','Gold','Platinum','VIP'];

/* ---------- AJAX UPDATE ---------- */
if (($_GET['action'] ?? '') === 'update' && $_SERVER['REQUEST_METHOD']==='POST') {
  header('Content-Type: application/json; charset=UTF-8');
  $in = json_decode(file_get_contents('php://input'), true) ?? [];

  $id = trim((string)($in['id'] ?? ''));
  $field = trim((string)($in['field'] ?? ''));
  $value = $in['value'] ?? null;

  if ($id==='' || $field==='') { echo json_encode(['ok'=>false,'error'=>'Invalid payload']); exit; }

  $allow = [
    'name' => 'name',
    'email' => 'email',
    'phone' => 'phone',
    'cert' => 'cert',
    'tier' => 'loyalty_level',
    'points' => 'points_total',
  ];
  if (!isset($allow[$field])) { echo json_encode(['ok'=>false,'error'=>'Field not allowed']); exit; }

  // Validations
  if ($field==='email') {
    $value = trim((string)$value);
    if ($value!=='' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['ok'=>false,'error'=>'Invalid email']); exit;
    }
  }
  if ($field==='phone') {
    $value = trim((string)$value);
    $value = preg_replace('/\D+/', '', $value);
  }
  if ($field==='cert') {
    if ($value===null || $value==='') $value = null;
    elseif (!in_array($value, $CERT_OPTIONS, true)) {
      echo json_encode(['ok'=>false,'error'=>'Invalid certification']); exit;
    }
  }
  if ($field==='tier') {
    if ($value===null || $value==='') $value = null;
    elseif (!in_array($value, $TIER_OPTIONS, true)) {
      echo json_encode(['ok'=>false,'error'=>'Invalid tier']); exit;
    }
  }
  if ($field==='points') {
    $value = (int)$value;
    if ($value < 0) $value = 0;
  }

  $col = $allow[$field];
  $sql = "UPDATE leads SET `$col` = :v, updated_at = :u WHERE id = :id";
  $st = $pdo->prepare($sql);
  $st->execute([':v'=>$value, ':u'=>date('Y-m-d H:i:s'), ':id'=>$id]);

  // Return fresh row
  $st = $pdo->prepare("SELECT id,name,email,phone,cert,loyalty_level AS tier,points_total AS points FROM leads WHERE id=:id");
  $st->execute([':id'=>$id]);
  $row = $st->fetch();

  echo json_encode(['ok'=>true,'row'=>$row]); exit;
}

/* ---------- Query Params ---------- */
$q = trim((string)($_GET['q'] ?? ''));
$sortable = [
  'name' => 'name',
  'email' => 'email',
  'phone' => 'phone',
  'cert' => 'cert',
  'tier' => 'loyalty_level',
  'points' => 'points_total',
  'updated' => 'updated_at',
];
$sort = $_GET['sort'] ?? 'updated';
$sortCol = $sortable[$sort] ?? 'updated_at';
$dir = strtolower((string)($_GET['dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = max(10, min(100, (int)($_GET['limit'] ?? 25)));
$offset = ($page - 1) * $limit;

$where = " ( `column`='diveclub' OR loyalty_level='Dive Club' OR loyalty_level IN ('".implode("','",$TIER_OPTIONS)."') ) ";
$params = [];
if ($q !== '') {
  $where .= " AND (name LIKE :q OR email LIKE :q OR phone LIKE :q OR cert LIKE :q OR loyalty_level LIKE :q) ";
  $params[':q'] = "%$q%";
}

/* ---------- Export CSV ---------- */
if (isset($_GET['export']) && $_GET['export']==='csv') {
  $sql = "SELECT name,email,phone,cert,loyalty_level AS tier,points_total AS points
          FROM leads
          WHERE $where
          ORDER BY $sortCol $dir";
  $st = $pdo->prepare($sql);
  $st->execute($params);

  header('Content-Type: text/csv; charset=UTF-8');
  header('Content-Disposition: attachment; filename="dive_club.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['Name','Email','Phone','Certification','Tier','Points']);
  while ($r = $st->fetch()) {
    fputcsv($out, [
      $r['name'] ?? '',
      $r['email'] ?? '',
      $r['phone'] ?? '',
      $r['cert'] ?? '',
      $r['tier'] ?? '',
      (string)($r['points'] ?? 0),
    ]);
  }
  fclose($out);
  exit;
}

/* ---------- Count & Data ---------- */
$countSql = "SELECT COUNT(*) FROM leads WHERE $where";
$st = $pdo->prepare($countSql); $st->execute($params); $total = (int)$st->fetchColumn();

$dataSql = "SELECT id,name,email,phone,cert,loyalty_level AS tier,points_total AS points, updated_at
            FROM leads
            WHERE $where
            ORDER BY $sortCol $dir
            LIMIT :lim OFFSET :off";
$st = $pdo->prepare($dataSql);
foreach ($params as $k=>$v) $st->bindValue($k,$v);
$st->bindValue(':lim',$limit,PDO::PARAM_INT);
$st->bindValue(':off',$offset,PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll();

/* ---------- Pagination Helpers ---------- */
$pages = max(1, (int)ceil($total / $limit));
function u($over=[]){
  $base = $_GET; foreach($over as $k=>$v){ if($v===null) unset($base[$k]); else $base[$k]=$v; }
  return '?'.http_build_query($base);
}

$AGENT_NAME = agent_name_from_email($GLOBALS['__email'] ?: '');
?>
<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <title>Dive Club — BALI DIVING (Editable)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">
  <style>
    :root{ color-scheme: dark; }
    body{
      background:
        radial-gradient(1200px 600px at 20% -10%, rgba(56,189,248,.08), transparent 40%),
        radial-gradient(900px 500px at 110% 10%, rgba(16,185,129,.08), transparent 40%),
        #020617;
      color:#e2e8f0;
    }
    .card { background:#0b1220; border:1px solid #1f2937; border-radius:16px; }
    .btn { display:inline-flex; align-items:center; gap:.4rem; padding:.45rem .7rem; border-radius:.6rem; border:1px solid #334155; }
    .btn:hover { background:#0b1426; }
    .muted { color:#94a3b8; }
    .table-wrap { overflow:auto; }

    /* Editable inputs */
    input.edit, select.edit {
      background:#0b1426; border:1px solid #334155; color:#e2e8f0; border-radius:.5rem; padding:.35rem .5rem; width:100%;
    }
    input.edit:focus, select.edit:focus { outline: 2px solid #38bdf8; }

    /* TABLE: all borders in white */
    table { border-collapse: collapse; width: 100%; }
    thead tr { border-bottom: 1px solid #ffffff; }
    tbody tr { border-bottom: 1px solid #ffffff; }
    th, td { border: none; } /* remove inner cell borders; row separators are white */
    /* If you also want cell grid lines, uncomment below:
       th, td { border: 1px solid #ffffff; }
    */

    .row-actions .btn { padding:.35rem .55rem; font-size:.75rem; }
    .toast { position:fixed; bottom:14px; left:50%; transform:translateX(-50%); background:#0b1220; border:1px solid #334155; padding:.5rem .8rem; border-radius:.75rem; display:none; }
    .toast.show { display:block; }
  </style>
</head>
<body class="min-h-screen">
  <header class="sticky top-0 z-20 bg-slate-950/60 backdrop-blur border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="h-9 w-9 rounded-xl bg-sky-500 text-slate-900 font-bold flex items-center justify-center">BD</div>
        <div>
          <h1 class="text-xl md:text-2xl font-semibold">Dive Club Directory (Editable)</h1>
          <p class="text-xs muted"><?= h($GLOBALS['__email'] ?: 'guest') ?> • <?= (int)$total ?> members</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <a class="btn text-sm" href="<?= u(['export'=>'csv']) ?>"><i class="fa-regular fa-file-lines"></i> Export CSV</a>
        <a class="btn text-sm" href="./crm"><i class="fa-solid fa-table-columns"></i> Back to CRM</a>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6 space-y-4">
    <!-- Filter & Controls -->
    <form method="get" class="card px-4 py-3 flex flex-col md:flex-row md:items-end gap-3">
      <div class="flex-1">
        <label class="block text-sm muted mb-1">Search</label>
        <input name="q" value="<?= h($q) ?>" placeholder="Name / Email / Phone / Certification / Tier"
               class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm muted mb-1">Sort</label>
        <select name="sort" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100">
          <option value="updated" <?= $sort==='updated'?'selected':'' ?>>Updated</option>
          <option value="name" <?= $sort==='name'?'selected':'' ?>>Name</option>
          <option value="email" <?= $sort==='email'?'selected':'' ?>>Email</option>
          <option value="phone" <?= $sort==='phone'?'selected':'' ?>>Phone</option>
          <option value="cert" <?= $sort==='cert'?'selected':'' ?>>Certification</option>
          <option value="tier" <?= $sort==='tier'?'selected':'' ?>>Tier</option>
          <option value="points" <?= $sort==='points'?'selected':'' ?>>Points</option>
        </select>
      </div>
      <div>
        <label class="block text-sm muted mb-1">Order</label>
        <select name="dir" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100">
          <option value="desc" <?= $dir==='DESC'?'selected':'' ?>>DESC</option>
          <option value="asc" <?= $dir==='ASC'?'selected':'' ?>>ASC</option>
        </select>
      </div>
      <div>
        <label class="block text-sm muted mb-1">Per page</label>
        <select name="limit" class="w-full border border-slate-700 bg-slate-950 rounded-lg px-3 py-2 text-slate-100">
          <?php foreach([25,50,75,100] as $n): ?>
            <option value="<?= $n ?>" <?= $limit===$n?'selected':'' ?>><?= $n ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex gap-2">
        <button class="btn"><i class="fa-solid fa-magnifying-glass"></i> Apply</button>
        <a class="btn" href="diveclub.php"><i class="fa-solid fa-rotate"></i> Reset</a>
      </div>
    </form>

    <!-- Table (Editable) -->
    <div class="card table-wrap">
      <table class="text-sm" id="tbl">
        <thead class="text-left">
          <tr class="text-slate-100">
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Phone</th>
            <th class="px-4 py-3">Certification</th>
            <th class="px-4 py-3">Tier</th>
            <th class="px-4 py-3">Points</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($rows)): ?>
            <tr><td colspan="7" class="px-4 py-6 text-center text-slate-100">No members found.</td></tr>
          <?php else: foreach($rows as $r): ?>
            <tr data-id="<?= h($r['id']) ?>">
              <!-- Name -->
              <td class="px-4 py-2.5">
                <input class="edit" type="text" data-field="name" value="<?= h($r['name'] ?? '') ?>">
              </td>
              <!-- Email -->
              <td class="px-4 py-2.5">
                <input class="edit" type="email" data-field="email" value="<?= h($r['email'] ?? '') ?>">
              </td>
              <!-- Phone -->
              <td class="px-4 py-2.5">
                <input class="edit" type="text" data-field="phone" placeholder="62..." value="<?= h($r['phone'] ?? '') ?>">
              </td>
              <!-- Certification Dropdown -->
              <td class="px-4 py-2.5">
                <select class="edit" data-field="cert">
                  <option value=""></option>
                  <?php foreach($CERT_OPTIONS as $opt): ?>
                    <option value="<?= h($opt) ?>" <?= ($r['cert']??'')===$opt ? 'selected':'' ?>><?= h($opt) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <!-- Tier Dropdown -->
              <td class="px-4 py-2.5">
                <select class="edit" data-field="tier">
                  <option value=""></option>
                  <?php foreach($TIER_OPTIONS as $opt): ?>
                    <option value="<?= h($opt) ?>" <?= ($r['tier']??'')===$opt ? 'selected':'' ?>><?= h($opt) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <!-- Points -->
              <td class="px-4 py-2.5">
                <input class="edit" type="number" min="0" step="1" data-field="points" value="<?= (int)($r['points'] ?? 0) ?>">
              </td>
              <!-- Actions -->
              <td class="px-4 py-2.5 text-right row-actions">
                <?php $wa = wa_member_link($r, $GLOBALS['__email'] ?? ''); ?>
                <div class="inline-flex items-center gap-2">
                  <?php if($wa): ?>
                    <a class="btn bg-white text-slate-900 hover:opacity-90" target="_blank" href="<?= h($wa) ?>">
                      <i class="fa-brands fa-whatsapp"></i> WA
                    </a>
                  <?php else: ?>
                    <span class="btn text-slate-400 cursor-not-allowed"><i class="fa-brands fa-whatsapp"></i> WA</span>
                  <?php endif; ?>
                  <a class="btn" href="./crm?id=<?= h($r['id']) ?>#open-offcanvas"><i class="fa-regular fa-folder-open"></i> Open in CRM</a>
                </div>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between">
      <div class="text-sm muted">Showing <?= min($total, $offset+1) ?>–<?= min($total, $offset+$limit) ?> of <?= (int)$total ?></div>
      <div class="flex items-center gap-1">
        <a class="btn text-xs <?= $page<=1?'opacity-50 pointer-events-none':'' ?>" href="<?= u(['page'=>1]) ?>"><i class="fa-solid fa-angles-left"></i></a>
        <a class="btn text-xs <?= $page<=1?'opacity-50 pointer-events-none':'' ?>" href="<?= u(['page'=>max(1,$page-1)]) ?>"><i class="fa-solid fa-angle-left"></i></a>
        <span class="px-2 text-sm">Page <?= (int)$page ?>/<?= (int)$pages ?></span>
        <a class="btn text-xs <?= $page>=$pages?'opacity-50 pointer-events-none':'' ?>" href="<?= u(['page'=>min($pages,$page+1)]) ?>"><i class="fa-solid fa-angle-right"></i></a>
        <a class="btn text-xs <?= $page>=$pages?'opacity-50 pointer-events-none':'' ?>" href="<?= u(['page'=>$pages]) ?>"><i class="fa-solid fa-angles-right"></i></a>
      </div>
    </div>
  </main>

  <div id="toast" class="toast">Saved</div>

  <!-- Agent name from PHP for WA message -->
  <script>
    const AGENT_NAME = <?= json_encode($AGENT_NAME ?: 'BALI DIVING Team') ?>;
  </script>

  <script>
    (function(){
      const tbl = document.getElementById('tbl');
      const toast = document.getElementById('toast');

      function showToast(msg, ok=true){
        toast.textContent = msg || (ok ? 'Saved' : 'Failed');
        toast.style.borderColor = ok ? '#334155' : '#b91c1c';
        toast.style.background = ok ? '#0b1220' : '#7f1d1d';
        toast.classList.add('show');
        clearTimeout(toast._t);
        toast._t = setTimeout(()=>toast.classList.remove('show'), 1200);
      }

      // Debounce per input
      const timers = new WeakMap();

      function payloadFromInput(tr, input){
        const id = tr?.dataset?.id || '';
        const field = input.dataset.field;
        let value = input.value;

        if (field==='phone'){
          value = (value||'').replace(/\D+/g,'');
        }
        if (field==='points'){
          value = parseInt(value||'0',10);
          if (isNaN(value) || value<0) value = 0;
        }
        return {id, field, value};
      }

      function buildWaText(row){
        const name = (row.name || 'Diver Friend');
        const tier = (row.tier && row.tier !== '') ? row.tier : 'Member';
        const points = parseInt(row.points || 0, 10);

        const lines = [
          `Hello ${name}, how are you?`,
          `This is ${AGENT_NAME} from BALI DIVING. You are registered as a Bali Diving Club Member. Tier: ${tier}. Points: ${points}.`,
          `Our diving family in Bali is ready to welcome you for your next trip, from Tulamben to Nusa Penida and Amed.`,
          `When is a good time to chat?`
        ];
        return lines.join('\n');
      }
      function buildWaLink(row){
        const p = (row.phone || '').replace(/\D+/g,'');
        if(!p) return '';
        const digits = p[0]==='0' ? ('62'+p.slice(1)) : p;
        const txt = encodeURIComponent(buildWaText(row));
        return `https://wa.me/${digits}?text=${txt}`;
      }

      function save(tr, input){
        const data = payloadFromInput(tr, input);
        if(!data.id || !data.field){ showToast('Invalid row', false); return; }

        fetch('?action=update', {
          method: 'POST',
          headers: {'Content-Type':'application/json; charset=UTF-8'},
          body: JSON.stringify(data),
          cache: 'no-store'
        })
        .then(r=>r.json())
        .then(j=>{
          if(j && j.ok){
            const rr = j.row || {};
            // refresh WA button (name/phone/tier/points changes)
            const actions = tr.querySelector('.row-actions');
            if (actions){
              const wa = buildWaLink(rr);
              actions.innerHTML = wa
                ? `<div class="inline-flex items-center gap-2">
                     <a class="btn bg-white text-slate-900 hover:opacity-90" target="_blank" href="${wa}">
                       <i class="fa-brands fa-whatsapp"></i> WA
                     </a>
                     <a class="btn" href="./crm?id=${rr.id}#open-offcanvas"><i class="fa-regular fa-folder-open"></i> Open in CRM</a>
                   </div>`
                : `<div class="inline-flex items-center gap-2">
                     <span class="btn text-slate-400 cursor-not-allowed">
                       <i class="fa-brands fa-whatsapp"></i> WA
                     </span>
                     <a class="btn" href="./crm?id=${rr.id}#open-offcanvas"><i class="fa-regular fa-folder-open"></i> Open in CRM</a>
                   </div>`;
            }
            showToast('Saved', true);
          } else {
            showToast((j && j.error) ? j.error : 'Save failed', false);
          }
        })
        .catch(()=> showToast('Network error', false));
      }

      tbl.addEventListener('input', (e)=>{
        const input = e.target.closest('.edit'); if(!input) return;
        const tr = input.closest('tr'); if(!tr) return;
        clearTimeout(timers.get(input));
        const t = setTimeout(()=> save(tr, input), 500); // debounce
        timers.set(input, t);
      });

      tbl.addEventListener('change', (e)=>{
        const el = e.target.closest('.edit'); if(!el) return;
        const tr = el.closest('tr'); if(!tr) return;
        save(tr, el); // commit immediately on select/number
      });
    })();
  </script>

  <!-- Icons -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js" defer></script>
</body>
</html>
