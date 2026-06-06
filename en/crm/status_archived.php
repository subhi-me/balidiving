<?php
// status_archived.php
require_once __DIR__.'/crm_schema.php';

$st = $pdo->prepare("SELECT * FROM leads WHERE `column` = 'archived' ORDER BY updated_at DESC");
$st->execute();
$rows = $st->fetchAll();

$stages = [
    'dive_club' => 'Dive Club',
    'admin'     => 'Admin',
    'void'      => 'Void',
];

$byStage = [];
foreach (array_keys($stages) as $k) $byStage[$k] = [];
foreach ($rows as $r) {
    $stage = $r['archived_stage'] ?: 'admin';
    if (!isset($byStage[$stage])) $byStage['admin'][] = $r;
    else $byStage[$stage][] = $r;
}
?><!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <title>Archived Status · Bali Diving CRM</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
  <style>
    body{background:#020617;color:#e2e8f0;}
    .card{background:#020617;border:1px solid #1f2937;border-radius:0.75rem;padding:0.75rem;}
  </style>
</head>
<body class="min-h-screen">
<header class="sticky top-0 bg-slate-950/70 backdrop-blur border-b border-slate-800">
  <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="index.php" class="h-8 w-8 rounded-xl bg-slate-500 text-slate-900 flex items-center justify-center font-bold">BD</a>
      <div>
        <h1 class="text-lg font-semibold">Archived – Status Board</h1>
        <p class="text-xs text-slate-400">Drag & drop between Archived statuses</p>
      </div>
    </div>
    <a href="index.php"
       class="px-3 py-1.5 text-xs rounded border border-slate-700 hover:bg-slate-800">
      Back to CRM
    </a>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <?php foreach ($stages as $key => $label): $list = $byStage[$key]; ?>
      <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden flex flex-col">
        <div class="px-3 py-2 flex items-center justify-between border-b border-slate-800 bg-slate-900/70">
          <span class="text-xs font-semibold"><?= h($label) ?></span>
          <span class="text-[11px] text-slate-400"><?= count($list) ?> items</span>
        </div>
        <div class="p-2 space-y-2 min-h-[180px]" data-stage="<?=h($key)?>">
          <?php if (empty($list)): ?>
            <div class="text-[11px] text-slate-500 italic">Drop archived leads here…</div>
          <?php endif; ?>
          <?php foreach ($list as $row): ?>
            <div class="card" draggable="true" data-id="<?=h($row['id'])?>">
              <div class="text-sm font-medium truncate"><?= h($row['name'] ?: '(No Name)') ?></div>
              <div class="text-[11px] text-slate-400 truncate">
                <?= h($row['email'] ?? '') ?>
              </div>
              <div class="text-[11px] text-slate-500">
                Updated: <?= h($row['updated_at'] ?? '') ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<div id="toast" class="fixed bottom-4 left-1/2 -translate-x-1/2 hidden">
  <div class="px-4 py-2 rounded-xl shadow-2xl bg-slate-900 text-white text-sm border border-slate-700"></div>
</div>

<script>
(function(){
  const $ = (s)=>document.querySelector(s);
  const $$ = (s)=>document.querySelectorAll(s);
  function toast(msg, ok=true){
    const wrap = $('#toast'), box = wrap.firstElementChild;
    box.textContent = msg;
    box.className = 'px-4 py-2 rounded-xl shadow-2xl text-sm border '+
      (ok?'bg-slate-900 text-white border-slate-700':'bg-rose-700 text-white border-rose-600');
    wrap.classList.remove('hidden');
    clearTimeout(wrap._t); wrap._t = setTimeout(()=>wrap.classList.add('hidden'), 1600);
  }
  async function postForm(data){
    const resp = await fetch('crm_api.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body:new URLSearchParams(data)
    });
    const ct = resp.headers.get('content-type') || '';
    if(!ct.includes('application/json')) throw new Error('Non-JSON');
    return resp.json();
  }

  let dragEl = null;
  $$('.card').forEach(c=>{
    c.addEventListener('dragstart', e=>{
      dragEl = c;
      c.classList.add('opacity-60');
      e.dataTransfer.effectAllowed='move';
      e.dataTransfer.setData('text/plain', c.getAttribute('data-id'));
    });
    c.addEventListener('dragend', ()=>{
      if(dragEl) dragEl.classList.remove('opacity-60');
      dragEl = null;
    });
  });

  $$('[data-stage]').forEach(col=>{
    col.addEventListener('dragover', e=>{
      e.preventDefault();
      col.classList.add('ring-1','ring-slate-500');
    });
    col.addEventListener('dragleave', e=>{
      if(!col.contains(e.relatedTarget)){
        col.classList.remove('ring-1','ring-slate-500');
      }
    });
    col.addEventListener('drop', async e=>{
      e.preventDefault();
      col.classList.remove('ring-1','ring-slate-500');
      const id = e.dataTransfer.getData('text/plain');
      if(!id || !dragEl) return;
      col.appendChild(dragEl);
      const stage = col.dataset.stage;
      try{
        const j = await postForm({action:'set_archived_stage', id, stage});
        if(j.ok) toast('Stage: '+stage,true);
        else toast(j.error||'Update failed',false);
      }catch(e){
        toast('Network error',false);
      }
    });
  });

})();
</script>
</body>
</html>
