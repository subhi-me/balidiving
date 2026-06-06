
<?php


require_once __DIR__ . '/config.php';





$columns = [
    'leads'     => 'Leads',
    'contacted' => 'Contacted',
    'booked'    => 'Booked',
    'archived'  => 'Archived',
];

$themes = [
    'leads'     => ['bg'=>'#0b1b2a','dot'=>'#38bdf8'],
    'contacted' => ['bg'=>'#261a0a','dot'=>'#f59e0b'],
    'booked'    => ['bg'=>'#0b241b','dot'=>'#10b981'],
    'archived'  => ['bg'=>'#111827','dot'=>'#94a3b8'],
];

$kanban = [];
$total = 0;
foreach (array_keys($columns) as $col) {
    $st = $pdo->prepare("SELECT * FROM leads WHERE `column` = :c ORDER BY updated_at DESC");
    $st->execute([':c'=>$col]);
    $rows = $st->fetchAll();
    $kanban[$col] = $rows;
    $total += count($rows);
}
?>
<header class="sticky top-0 z-20 bg-slate-950/70 backdrop-blur border-b border-slate-800">
  <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="h-9 w-9 rounded-xl bg-sky-500 text-slate-900 font-bold flex items-center justify-center">BD</div>
      <div>
        <h1 class="text-xl md:text-2xl font-semibold">BALI DIVING CRM</h1>
        <p class="text-xs text-slate-400">Leads Kanban • Total: <b><?= (int)$total ?></b></p>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <button id="openAddModalBtn"
              class="px-3 py-2 rounded-lg bg-sky-500 text-slate-900 text-sm hover:bg-sky-400">
        <i class="fa-solid fa-plus mr-1.5"></i> New Lead
      </button>
    </div>
  </div>
</header>

<main class="max-w-7xl mx-auto px-4 py-6 space-y-6">
  <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="kanban">
    <?php foreach ($columns as $key => $label): 
      $rows = $kanban[$key];
      $theme = $themes[$key];
    ?>
    <div class="bg-slate-900/70 backdrop-blur border border-slate-800 rounded-2xl overflow-hidden flex flex-col">
      <div class="px-4 py-3 flex items-center justify-between bg-slate-900/60 border-b border-slate-800">
        <div class="flex items-center gap-2">
          <span class="h-2 w-2 rounded-full" style="background:<?=h($theme['dot'])?>"></span>
          <h3 class="font-semibold"><?= h($label) ?></h3>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-400">
          <span><?= count($rows) ?> items</span>
          <?php if ($key === 'booked'): ?>
            <a href="status_booked.php"
               class="px-2 py-1 rounded-md border border-emerald-500 text-emerald-300 hover:bg-emerald-500/10">
              Status
            </a>
          <?php elseif ($key === 'archived'): ?>
            <a href="status_archived.php"
               class="px-2 py-1 rounded-md border border-slate-500 text-slate-300 hover:bg-slate-500/10">
              Status
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="p-3 space-y-3 flex-1 min-h-[220px]"
           data-column="<?=h($key)?>"
           style="--card-bg:<?=h($theme['bg'])?>;"
           ondragover="event.preventDefault()">
        <?php if (empty($rows)): ?>
          <div class="text-sm text-slate-400 italic">
            <i class="fa-regular fa-square-plus"></i> Drop a lead here…
          </div>
        <?php endif; ?>

        <?php foreach ($rows as $row): ?>
          <div class="kanban-card rounded-xl p-3"
               style="background:var(--card-bg);border:1px solid rgba(148,163,184,.6);"
               draggable="true"
               data-id="<?=h($row['id'])?>">
            <div class="flex items-center justify-between gap-2">
              <div class="min-w-0">
                <div class="font-medium truncate"><?= h($row['name'] ?: '(No Name)') ?></div>
                <div class="text-[11px] text-slate-400 truncate">
                  <?= h($row['email'] ?? '') ?>
                  <?php if (!empty($row['phone'])): ?>
                    • <?= h($row['phone']) ?>
                  <?php endif; ?>
                </div>
                <?php if ($key === 'booked'): ?>
                  <div class="text-[11px] text-emerald-300 mt-1">
                    Booking: <?= h($row['booking_stage'] ?: 'Coming') ?>
                    <?php if (!empty($row['dive_date'])): ?>
                      • <?= h($row['dive_date']) ?>
                    <?php endif; ?>
                  </div>
                <?php elseif ($key === 'archived' && !empty($row['archived_stage'])): ?>
                  <div class="text-[11px] text-slate-300 mt-1">
                    Status: <?= h(ucwords(str_replace('_',' ',$row['archived_stage']))) ?>
                  </div>
                <?php endif; ?>
              </div>
              <button type="button"
                      class="shrink-0 text-[11px] px-2 py-1 rounded-md bg-slate-800 text-slate-100 hover:bg-slate-700 open-lead-btn"
                      data-open-id="<?=h($row['id'])?>">
                Open
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </section>
</main>

<!-- Modal: New Lead -->
<div id="addModal" class="modal fixed inset-0 z-30 flex items-center justify-center opacity-0 pointer-events-none transition-opacity">
  <div class="bg-slate-900 rounded-2xl shadow-2xl w-full max-w-2xl border border-slate-800">
    <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
      <h3 class="text-lg font-semibold">New Lead</h3>
      <button id="closeAddModalBtn" class="px-3 py-1.5 text-sm rounded border border-slate-700 hover:bg-slate-800">
        <i class="fa-solid fa-xmark"></i> Close
      </button>
    </div>
    <form method="post" action="crm_api.php?action=create_lead"
          class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm text-slate-300 mb-1">Name*</label>
        <input name="name" required
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm text-slate-300 mb-1">Email</label>
        <input name="email" type="email"
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm text-slate-300 mb-1">Phone</label>
        <input name="phone"
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm text-slate-300 mb-1">Country</label>
        <input name="country"
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm text-slate-300 mb-1">Source</label>
        <input name="source"
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm text-slate-300 mb-1">Package</label>
        <input name="package"
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm text-slate-300 mb-1">Certification</label>
        <input name="cert"
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm text-slate-300 mb-1">Dive Date</label>
        <input type="date" name="dive_date"
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div>
        <label class="block text-sm text-slate-300 mb-1">Pax</label>
        <input type="number" min="0" name="pax"
               class="w-full border border-slate-700 bg-slate-950 rounded-xl px-3 py-2 text-slate-100"/>
      </div>
      <div class="md:col-span-2 flex gap-3 pt-2">
        <button type="button" id="closeAddModalBtn2"
                class="px-4 py-2 rounded-lg border border-slate-700 text-slate-200 hover:bg-slate-800">
          <i class="fa-regular fa-circle-xmark mr-1.5"></i> Cancel
        </button>
        <button class="px-4 py-2 rounded-lg bg-sky-500 text-slate-900 hover:bg-sky-400">
          <i class="fa-solid fa-floppy-disk mr-1.5"></i> Save
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Offcanvas Lead Detail -->
<aside id="offcanvas" class="fixed top-0 right-0 w-full sm:w-[520px] h-full bg-slate-900 shadow-2xl z-40 border-l border-slate-800 transform translate-x-full transition-transform">
  <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
    <h3 class="text-lg font-semibold">
      <i class="fa-regular fa-id-card mr-2 text-slate-400"></i> Lead Details
    </h3>
    <button id="closeOffcanvasBtn"
            class="px-3 py-1.5 text-sm rounded border border-slate-700 hover:bg-slate-800">
      <i class="fa-solid fa-xmark"></i> Close
    </button>
  </div>
  <div class="flex-1 overflow-y-auto p-5">
    <form id="editForm" class="space-y-3 text-sm">
      <input type="hidden" id="f_id">
      <div class="grid grid-cols-3 gap-3 mb-2">
        <div class="col-span-2">
          <label class="block text-slate-300 text-xs mb-1">Name</label>
          <input id="f_name"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Board</label>
          <select id="f_column"
                  class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-2 text-slate-100 text-xs">
            <option value="leads">Leads</option>
            <option value="contacted">Contacted</option>
            <option value="booked">Booked</option>
            <option value="archived">Archived</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 text-xs mb-1">Email</label>
          <input id="f_email"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Phone</label>
          <input id="f_phone"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Country</label>
          <input id="f_country"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Source</label>
          <input id="f_source"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
      </div>

      <div class="border-t border-slate-800 pt-3 mt-2 grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 text-xs mb-1">Package</label>
          <input id="f_package"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Certification</label>
          <input id="f_cert"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Dive Date</label>
          <input id="f_dive_date" type="date"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-slate-300 text-xs mb-1">Pax</label>
            <input id="f_pax" type="number" min="0"
                   class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
          </div>
          <div>
            <label class="block text-slate-300 text-xs mb-1">Budget (USD)</label>
            <input id="f_budget" type="number" step="0.01" min="0"
                   class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
          </div>
        </div>
      </div>

      <div class="border-t border-slate-800 pt-3 mt-2 grid grid-cols-3 gap-3">
        <div>
          <label class="block text-slate-300 text-xs mb-1">Payment Status</label>
          <select id="f_payment_status"
                  class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-2 text-slate-100 text-xs">
            <option value="unpaid">Unpaid</option>
            <option value="deposit">Deposit</option>
            <option value="paid">Paid</option>
            <option value="refunded">Refunded</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Payment Method</label>
          <select id="f_payment_method"
                  class="w-full border border-slate-700 bg-slate-950 rounded px-2 py-2 text-slate-100 text-xs">
            <option value="">—</option>
            <option value="cash">Cash</option>
            <option value="transfer">Bank Transfer</option>
            <option value="card">Card</option>
            <option value="ewallet">E-wallet</option>
            <option value="ota">OTA</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Deposit Paid</label>
          <input id="f_deposit_amount" type="number" step="0.01" min="0"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
      </div>

      <div class="border-t border-slate-800 pt-3 mt-2">
        <label class="block text-slate-300 text-xs mb-1">Trip History</label>
        <div id="tripList" class="space-y-2 text-xs text-slate-300">
          <div class="text-slate-500 italic">No trip history</div>
        </div>
      </div>

      <div id="metaDates" class="text-[11px] text-slate-500 mt-2"></div>
    </form>
  </div>
  <div class="px-5 py-3 border-t border-slate-800 flex items-center justify-between">
    <button id="deleteLeadBtn"
            class="px-3 py-1.5 text-sm rounded bg-rose-600 text-white hover:bg-rose-500">
      <i class="fa-regular fa-trash-can mr-1.5"></i> Delete
    </button>
    <span class="text-xs text-slate-400">Auto-saved</span>
  </div>
</aside>

<!-- Modal: Booking Payment -->
<div id="bookingModal" class="modal fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity">
  <div class="bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-800">
    <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
      <h3 class="text-lg font-semibold">
        <i class="fa-solid fa-credit-card mr-2 text-sky-400"></i> Booking Payment
      </h3>
      <button id="bookingCloseBtn"
              class="px-3 py-1.5 text-sm rounded border border-slate-700 hover:bg-slate-800">
        <i class="fa-solid fa-xmark"></i> Close
      </button>
    </div>
    <div class="p-5 space-y-3 text-sm">
      <input type="hidden" id="booking_lead_id">
      <input type="hidden" id="booking_context_source">
      <input type="hidden" id="booking_context_from_column">
      <input type="hidden" id="booking_context_card_selector">

      <div>
        <label class="block text-slate-300 text-xs mb-1">Budget (USD)</label>
        <input id="bm_budget" type="number" step="0.01" min="0"
               class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 text-xs mb-1">Payment Status</label>
          <div class="w-full border border-emerald-600/70 bg-emerald-900/40 rounded px-3 py-2 text-emerald-300 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>Paid (required)</span>
          </div>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Payment Method</label>
          <select id="bm_method"
                  class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100">
            <option value="">— Select —</option>
            <option value="cash">Cash</option>
            <option value="transfer">Bank Transfer</option>
            <option value="card">Card</option>
            <option value="ewallet">E-wallet</option>
            <option value="ota">OTA</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-slate-300 text-xs mb-1">
          Note <span class="text-rose-400">*</span>
          <span class="text-slate-500 text-[11px]">(required except Cash)</span>
        </label>
        <textarea id="bm_note" rows="3"
                  class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"
                  placeholder="Example: Paid via BCA, 50% deposit, OTA invoice #1234"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 text-xs mb-1">Deposit Paid</label>
          <input id="bm_deposit" type="number" step="0.01" min="0"
                 class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"/>
        </div>
        <div>
          <label class="block text-slate-300 text-xs mb-1">Currency</label>
          <select id="bm_currency"
                  class="w-full border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100">
            <option value="USD" selected>USD</option>
            <option value="IDR">IDR</option>
          </select>
        </div>
      </div>

      <div id="bm_rate_wrap" class="hidden">
        <label class="block text-slate-300 text-xs mb-1">Conversion Rate</label>
        <div class="flex items-center gap-2">
          <span class="text-slate-300 text-sm whitespace-nowrap">1 USD =</span>
          <input id="bm_rate" type="number" step="1" min="0"
                 class="flex-1 border border-slate-700 bg-slate-950 rounded px-3 py-2 text-slate-100"
                 placeholder="e.g. 16000"/>
          <span class="text-slate-300 text-sm whitespace-nowrap">IDR</span>
        </div>
        <p class="text-[11px] text-slate-500 mt-1">Required if currency = IDR.</p>
      </div>
    </div>
    <div class="px-5 py-3 border-t border-slate-800 flex items-center justify-end gap-2">
      <button id="bookingCancelBtn"
              class="px-3 py-1.5 text-sm rounded border border-slate-700 hover:bg-slate-800">
        Cancel
      </button>
      <button id="bookingSaveBtn"
              class="px-4 py-1.5 text-sm rounded bg-sky-500 text-slate-900 hover:bg-sky-400">
        <i class="fa-solid fa-floppy-disk mr-1.5"></i> Save
      </button>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-4 left-1/2 -translate-x-1/2 hidden">
  <div class="px-4 py-2 rounded-xl shadow-2xl bg-slate-900 text-white text-sm border border-slate-700"></div>
</div>

<script>
(function(){
  const $ = (s)=>document.querySelector(s);
  const $$ = (s)=>document.querySelectorAll(s);

  function showToast(msg, ok=true){
    const wrap = $('#toast');
    const box  = wrap.firstElementChild;
    box.textContent = msg;
    box.className = 'px-4 py-2 rounded-xl shadow-2xl text-sm border ' +
      (ok ? 'bg-slate-900 text-white border-slate-700' : 'bg-rose-700 text-white border-rose-600');
    wrap.classList.remove('hidden');
    clearTimeout(wrap._t);
    wrap._t = setTimeout(()=>wrap.classList.add('hidden'), 1800);
  }

  async function postForm(url, data){
    const resp = await fetch(url, {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body: new URLSearchParams(data)
    });
    const ct = resp.headers.get('content-type') || '';
    if (!ct.includes('application/json')) throw new Error('Non-JSON');
    return resp.json();
  }
  async function fetchJSON(url){
    const resp = await fetch(url, {cache:'no-store'});
    const ct = resp.headers.get('content-type') || '';
    if (!ct.includes('application/json')) throw new Error('Non-JSON');
    return resp.json();
  }

  /* ===== New Lead Modal ===== */
  const addModal = $('#addModal');
  const openAdd = ()=>{ addModal.style.opacity='1'; addModal.style.pointerEvents='auto'; };
  const closeAdd = ()=>{ addModal.style.opacity='0'; addModal.style.pointerEvents='none'; };

  $('#openAddModalBtn')?.addEventListener('click', openAdd);
  $('#closeAddModalBtn')?.addEventListener('click', closeAdd);
  $('#closeAddModalBtn2')?.addEventListener('click', closeAdd);

  /* ===== Offcanvas ===== */
  const offcanvas = $('#offcanvas');
  const openOff = ()=>{ offcanvas.style.transform='translateX(0)'; document.body.style.overflow='hidden'; };
  const closeOff = ()=>{ offcanvas.style.transform='translateX(100%)'; document.body.style.overflow=''; };

  $('#closeOffcanvasBtn')?.addEventListener('click', closeOff);

  let currentId = null;
  let oldBoard = 'leads';
  const f = {
    id: $('#f_id'),
    name: $('#f_name'),
    column: $('#f_column'),
    email: $('#f_email'),
    phone: $('#f_phone'),
    country: $('#f_country'),
    source: $('#f_source'),
    pack: $('#f_package'),
    cert: $('#f_cert'),
    dive_date: $('#f_dive_date'),
    pax: $('#f_pax'),
    budget: $('#f_budget'),
    pay_status: $('#f_payment_status'),
    pay_method: $('#f_payment_method'),
    deposit: $('#f_deposit_amount'),
  };
  const metaDates = $('#metaDates');
  const tripList  = $('#tripList');
  let saveTimer = null;

  async function loadLead(id){
    try{
      const j = await fetchJSON('crm_api.php?action=read_lead&id='+encodeURIComponent(id));
      if(!j.ok){ showToast(j.error||'Load failed', false); return; }
      const d = j.data;
      currentId = d.id;

      f.id.value       = d.id;
      f.name.value     = d.name || '';
      f.column.value   = d.column || 'leads';
      f.email.value    = d.email || '';
      f.phone.value    = d.phone || '';
      f.country.value  = d.country || '';
      f.source.value   = d.source || '';
      f.pack.value     = d.package || '';
      f.cert.value     = d.cert || '';
      f.dive_date.value= d.dive_date || '';
      f.pax.value      = d.pax ?? '';
      f.budget.value   = d.budget ?? '';
      f.pay_status.value  = d.payment_status || 'unpaid';
      f.pay_method.value  = d.payment_method || '';
      f.deposit.value  = d.deposit_amount ?? '';

      oldBoard = d.column || 'leads';

      const created = d.created_at ? new Date(d.created_at.replace(' ','T')) : null;
      const updated = d.updated_at ? new Date(d.updated_at.replace(' ','T')) : null;
      metaDates.textContent = 'Created: ' + (created?created.toLocaleString():'') +
        ' • Updated: ' + (updated?updated.toLocaleString():'');

      // Trip history
      const trips = j.trips || [];
      if(!trips.length){
        tripList.innerHTML = '<div class="text-slate-500 italic">No trip history</div>';
      } else {
        tripList.innerHTML = '';
        trips.forEach(t =>{
          const div = document.createElement('div');
          div.className = 'border border-slate-700 rounded-lg px-3 py-2 bg-slate-950';
          div.innerHTML = `
            <div class="flex justify-between items-center">
              <div class="font-medium text-xs">${t.package || '(No package)'}</div>
              <div class="text-[11px] text-slate-500">${t.created_at}</div>
            </div>
            <div class="text-[11px] text-slate-400">
              ${t.dive_date ? 'Dive: '+t.dive_date+' • ' : ''}Pax: ${t.pax ?? 0}
              • Budget: ${t.budget ?? 0}
              • ${t.payment_status || ''} ${t.payment_method ? '('+t.payment_method+')':''}
            </div>
            ${t.note ? '<div class="text-[11px] mt-1">'+t.note.replace(/</g,'&lt;')+'</div>' : ''}
          `;
          tripList.appendChild(div);
        });
      }

      openOff();
    }catch(e){
      showToast('Network error', false);
    }
  }

  $$('.open-lead-btn').forEach(btn=>{
    btn.addEventListener('click', (ev)=>{
      const id = btn.getAttribute('data-open-id');
      if(id) loadLead(id);
      ev.stopPropagation();
    });
  });

  // autosave
  $('#editForm')?.addEventListener('input', ()=>{
    if(!currentId) return;
    clearTimeout(saveTimer);
    saveTimer = setTimeout(async ()=>{
      if(!currentId) return;
      const data = {
        action: 'update_lead',
        id: currentId,
        name: f.name.value,
        email: f.email.value,
        phone: f.phone.value,
        country: f.country.value,
        source: f.source.value,
        package: f.pack.value,
        cert: f.cert.value,
        dive_date: f.dive_date.value,
        pax: f.pax.value,
        budget: f.budget.value,
        payment_status: f.pay_status.value,
        payment_method: f.pay_method.value,
        deposit_amount: f.deposit.value
      };
      try{
        const j = await postForm('crm_api.php', data);
        if(!j.ok) showToast('Save failed', false);
      }catch(e){
        showToast('Network error', false);
      }
    }, 600);
  });

  // change board from offcanvas
  f.column?.addEventListener('change', (e)=>{
    if(!currentId) return;
    const to = e.target.value;
    if(to === 'booked' && oldBoard !== 'booked'){
      // buka modal booking, jangan langsung move
      e.target.value = oldBoard;
      openBookingModal(currentId, 'select', oldBoard, '');
      return;
    }
    // normal move
    postForm('crm_api.php', {action:'move_lead', id:currentId, to})
      .then(j=>{
        if(j.ok){
          showToast('Moved to '+to, true);
          oldBoard = to;
        } else showToast(j.error||'Move failed', false);
      })
      .catch(()=>showToast('Network error', false));
  });

  // delete
  $('#deleteLeadBtn')?.addEventListener('click', ()=>{
    if(!currentId) return;
    if(!confirm('Delete this lead?')) return;
    postForm('crm_api.php', {action:'delete_lead', id:currentId})
      .then(j=>{
        if(j.ok){
          showToast('Deleted', true);
          const card = document.querySelector('.kanban-card[data-id="'+currentId+'"]');
          if(card) card.remove();
          closeOff();
        } else showToast(j.error||'Delete failed', false);
      })
      .catch(()=>showToast('Network error', false));
  });

  /* ===== Drag & Drop ===== */
  let dragEl = null;
  let dragFromColumn = null;

  $$('.kanban-card').forEach(card=>{
    card.addEventListener('dragstart', e=>{
      dragEl = card;
      dragFromColumn = card.closest('[data-column]')?.dataset.column || null;
      card.classList.add('opacity-60');
      e.dataTransfer.effectAllowed='move';
      e.dataTransfer.setData('text/plain', card.getAttribute('data-id'));
    });
    card.addEventListener('dragend', ()=>{
      if(dragEl) dragEl.classList.remove('opacity-60');
      dragEl = null;
    });
  });

  $$('[data-column]').forEach(col=>{
    col.addEventListener('dragover', e=>{
      e.preventDefault();
      col.classList.add('ring-1','ring-sky-500');
    });
    col.addEventListener('dragleave', e=>{
      if(!col.contains(e.relatedTarget)){
        col.classList.remove('ring-1','ring-sky-500');
      }
    });
    col.addEventListener('drop', e=>{
      e.preventDefault();
      col.classList.remove('ring-1','ring-sky-500');
      const id = e.dataTransfer.getData('text/plain');
      if(!id || !dragEl) return;
      col.appendChild(dragEl);
      const to = col.dataset.column || 'leads';

      if(to === 'booked'){
        const selector = '.kanban-card[data-id="'+id+'"]';
        openBookingModal(id, 'drag', dragFromColumn || 'leads', selector);
        return;
      }

      postForm('crm_api.php', {action:'move_lead', id, to})
        .then(j=>{
          if(j.ok) showToast('Moved to '+to, true);
          else showToast(j.error||'Move failed', false);
        })
        .catch(()=>showToast('Network error', false));
    });
  });

  /* ===== Booking Modal Logic ===== */
  const bookingModal = $('#bookingModal');
  const bm_lead_id   = $('#booking_lead_id');
  const bm_source    = $('#booking_context_source');
  const bm_from_col  = $('#booking_context_from_column');
  const bm_card_sel  = $('#booking_context_card_selector');
  const bm_budget    = $('#bm_budget');
  const bm_method    = $('#bm_method');
  const bm_note      = $('#bm_note');
  const bm_deposit   = $('#bm_deposit');
  const bm_currency  = $('#bm_currency');
  const bm_rate_wrap = $('#bm_rate_wrap');
  const bm_rate      = $('#bm_rate');

  function openBookingModal(id, source, fromColumn, cardSelector){
    bm_lead_id.value = id;
    bm_source.value  = source || '';
    bm_from_col.value= fromColumn || '';
    bm_card_sel.value= cardSelector || '';

    bm_budget.value  = '';
    bm_method.value  = '';
    bm_note.value    = '';
    bm_deposit.value = '';
    bm_currency.value= 'USD';
    bm_rate.value    = '';
    bm_rate_wrap.classList.add('hidden');

    bookingModal.style.opacity='1';
    bookingModal.style.pointerEvents='auto';
  }
  function closeBookingModal(){
    bookingModal.style.opacity='0';
    bookingModal.style.pointerEvents='none';
  }

  bm_currency?.addEventListener('change', ()=>{
    if(bm_currency.value === 'IDR'){
      bm_rate_wrap.classList.remove('hidden');
    } else {
      bm_rate_wrap.classList.add('hidden');
      bm_rate.value = '';
    }
  });

  $('#bookingCloseBtn')?.addEventListener('click', closeBookingModal);
  $('#bookingCancelBtn')?.addEventListener('click', ()=>{
    const src  = bm_source.value;
    const from = bm_from_col.value;
    const cardSel = bm_card_sel.value;
    if(src === 'drag' && from && cardSel){
      const card = document.querySelector(cardSel);
      const col  = document.querySelector('[data-column="'+from+'"]');
      if(card && col) col.appendChild(card);
    }
    closeBookingModal();
  });

  $('#bookingSaveBtn')?.addEventListener('click', async ()=>{
    const id   = bm_lead_id.value;
    const src  = bm_source.value;
    const from = bm_from_col.value;

    const budget  = parseFloat(bm_budget.value || '0');
    const method  = bm_method.value;
    const note    = bm_note.value.trim();
    const deposit = parseFloat(bm_deposit.value || '0');
    const cur     = bm_currency.value || 'USD';
    const rate    = bm_rate.value !== '' ? parseFloat(bm_rate.value) : '';

    if(!id){ showToast('Invalid lead', false); return; }
    if(!method){ showToast('Payment Method required', false); return; }
    if(method !== 'cash' && note === ''){
      showToast('Note required for non-cash payments', false); return;
    }
    if(cur === 'IDR' && (!rate || rate <= 0)){
      showToast('Rate required for IDR', false); return;
    }

    try{
      const j = await postForm('crm_api.php', {
        action:'set_booking_payment',
        id,
        budget: String(budget || 0),
        payment_method: method,
        note,
        deposit: String(deposit || 0),
        currency: cur,
        rate: String(rate || '')
      });
      if(!j.ok){
        showToast(j.error||'Save failed', false);
        return;
      }

      // setelah payment OK, baru pindah ke booked
      await postForm('crm_api.php', {action:'move_lead', id, to:'booked'});
      showToast('Booking payment saved (Paid)', true);

      // update board dropdown kalau lead sedang dibuka
      if(currentId === id && f.column){
        f.column.value = 'booked';
        oldBoard = 'booked';
      }

      closeBookingModal();
    }catch(e){
      showToast('Network error', false);
    }
  });

})();
</script>
