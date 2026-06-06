<?php
// gear.php – Dive Site / Product Catalog Offcanvas + Autosave + Manual Save + Status
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

include('../template/database/main.php'); // expects $pdo

function gear_json_headers(){
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
}

$gearAction = $_GET['gear_action'] ?? '';

/**
 * DB asumsi table booking_catalog:
 *  - activity_key  (PK part)
 *  - sub_key       (PK part)
 *  - short_desc    TEXT
 *  - long_desc     TEXT
 *  - images        JSON/TEXT (array URL)
 *  - created_at    DATETIME
 *  - updated_at    DATETIME
 */

/* ---------- API: LOAD CATALOG ---------- */
/*
   gear.php?gear_action=load_catalog
*/
if ($gearAction === 'load_catalog') {
    gear_json_headers();

    $out = [];

    try{
        $st = $pdo->query("
            SELECT activity_key, sub_key, short_desc, long_desc, images
            FROM booking_catalog
        ");
        while($r = $st->fetch(PDO::FETCH_ASSOC)){
            $ak = $r['activity_key'] ?? '';
            $sk = $r['sub_key'] ?? '';
            if(!$ak || !$sk) continue;

            if(!isset($out[$ak])) $out[$ak] = [];

            $image = '';
            if(!empty($r['images'])){
                $imgs = json_decode($r['images'], true);
                if(is_array($imgs) && !empty($imgs[0])){
                    $image = (string)$imgs[0];
                }
            }

            $out[$ak][$sk] = [
                'short_desc' => $r['short_desc'] ?? '',
                'long_desc'  => $r['long_desc']  ?? '',
                'image'      => $image,
            ];
        }

        echo json_encode(['ok'=>true,'catalog'=>$out], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }catch(Throwable $e){
        echo json_encode(['ok'=>false,'error'=>'db_error']);
    }
    exit;
}

/* ---------- API: SAVE (AUTOSAVE / MANUAL) CATALOG ---------- */
/*
   POST gear.php?gear_action=autosave_catalog
   Body JSON:
   {
     "catalog": {
       "fun_diving": {
         "padang_bai": { "short_desc":"...", "long_desc":"...", "image":"https://..." },
         ...
       },
       ...
     }
   }
*/
if ($gearAction === 'autosave_catalog' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    gear_json_headers();

    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if(!is_array($data) || !isset($data['catalog']) || !is_array($data['catalog'])){
        echo json_encode(['ok'=>false,'error'=>'bad_json']);
        exit;
    }

    $catalog = $data['catalog'];

    try{
        $pdo->beginTransaction();

        $sql = "
            INSERT INTO booking_catalog
                (activity_key, sub_key, short_desc, long_desc, images, updated_at, created_at)
            VALUES
                (:ak, :sk, :sd, :ld, :img, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                short_desc = VALUES(short_desc),
                long_desc  = VALUES(long_desc),
                images     = VALUES(images),
                updated_at = VALUES(updated_at)
        ";
        $st = $pdo->prepare($sql);

        foreach($catalog as $ak => $subs){
            if(!is_array($subs)) continue;
            foreach($subs as $sk => $cfg){
                if(!$ak || !$sk) continue;

                $short_desc = trim((string)($cfg['short_desc'] ?? ''));
                $long_desc  = trim((string)($cfg['long_desc']  ?? ''));
                $image      = trim((string)($cfg['image']      ?? ''));

                $imagesJson = $image !== ''
                    ? json_encode([$image], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
                    : json_encode([], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                $st->execute([
                    ':ak'  => $ak,
                    ':sk'  => $sk,
                    ':sd'  => $short_desc,
                    ':ld'  => $long_desc,
                    ':img' => $imagesJson,
                ]);
            }
        }

        $pdo->commit();
        echo json_encode(['ok'=>true]);
    }catch(Throwable $e){
        if($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['ok'=>false,'error'=>'db_error']);
    }
    exit;
}
?>

<!-- Offcanvas: Dive Site / Product Catalog (Accordion) -->
<div id="gearOverlay" class="ov" onclick="closeGear()" style="z-index:55;"></div>

<aside id="gearOffcanvas" class="off" aria-hidden="true" style="z-index:65;">
  <div class="flex items-center justify-between p-4 border-b border-[var(--border)] gap-3">
    <div class="flex items-center gap-2">
      <h3 class="m-0 text-lg font-semibold flex items-center gap-2">
        <i class="fa-solid fa-gear"></i>
        <span>Dive Site &amp; Product Catalog</span>
      </h3>
    </div>
    <div class="flex items-center gap-3">
      <div id="gearStatus" class="text-[11px] text-[var(--muted)]">
        Ready
      </div>
      <button
        id="gearSaveBtn"
        type="button"
        class="inline-flex items-center gap-1 text-[11px] px-2 py-1 rounded border border-slate-600 hover:bg-slate-800 hover:border-slate-400 transition"
      >
        <i class="fa-solid fa-floppy-disk text-xs"></i>
        <span>Save now</span>
      </button>
      <button class="btn-icon" onclick="closeGear()" aria-label="Close Gear">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  </div>

  <div class="p-4 space-y-4">
    <p class="text-xs text-[var(--muted)]">
      Edit <b>short description</b>, <b>long description</b>, dan <b>main image</b> untuk tiap dive site / level.
      Perubahan akan ditandai dan bisa di-save ke <b>database (booking_catalog)</b> lewat tombol <b>Save now</b>.
      Autosave juga akan mencoba menyimpan perubahan secara berkala.
    </p>

    <!-- Accordion container -->
    <div id="gearAccordion" class="space-y-3"></div>
  </div>
</aside>

<script>
  // ---------- HELPER ----------
  function gear_escapeHtml(str){
    if(str == null) return '';
    return String(str)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  // ---------- CONFIG: sinkron dengan dive site / level di sistem ----------
  const gearActivities = {
    snorkeling: {
      label: 'Snorkeling',
      subs: {
        padang_bai: 'Padang Bai',
        tulamben : 'Tulamben',
        amed     : 'Amed',
        npmp     : 'NPMP (Nusa Penida Marine Park)'
      }
    },
    try_diving: {
      label: 'Try Diving',
      subs: {
        padang_bai: 'Padang Bai',
        tulamben : 'Tulamben',
        amed     : 'Amed'
      }
    },
    fun_diving: {
      label: 'Fun Diving',
      subs: {
        padang_bai   : 'Padang Bai',
        tulamben     : 'Tulamben Wreck',
        amed         : 'Amed',
        npmp         : 'Nusa Penida Marine Park',
        gili_tepekong: 'Gili Tepekong / Mimpang',
        kubu         : 'Kubu'
      }
    },
    padi_courses: {
      label: 'PADI Courses',
      subs: {
        beginners  : 'Beginners',
        advanced   : 'Advanced',
        specialty  : 'Specialty',
        dive_master: 'Dive Master'
      }
    }
    // Kalau ada activity_key lain di DB, akan ditambah otomatis setelah load.
  };

  // ---------- STATE ----------
  let gearCatalogState = {};   // { activity_key: { sub_key: { short_desc, long_desc, image } } }
  let gearLoaded = false;
  let gearAutosaveTimer = null;
  let gearDirty = false;

  const gearOverlay   = document.getElementById('gearOverlay');
  const gearOff       = document.getElementById('gearOffcanvas');
  const gearAccordion = document.getElementById('gearAccordion');
  const gearStatusEl  = document.getElementById('gearStatus');
  const gearSaveBtn   = document.getElementById('gearSaveBtn');

  // ---------- STATUS UI ----------
  function gear_setStatus(text, mode){
    if(!gearStatusEl) return;
    gearStatusEl.textContent = text;

    gearStatusEl.classList.remove('text-[var(--muted)]','text-emerald-300','text-amber-300','text-red-300');

    if(mode === 'saved'){
      gearStatusEl.classList.add('text-emerald-300');
    }else if(mode === 'dirty'){
      gearStatusEl.classList.add('text-amber-300');
    }else if(mode === 'error'){
      gearStatusEl.classList.add('text-red-300');
    }else{
      gearStatusEl.classList.add('text-[var(--muted)]');
    }
  }

  // ---------- OPEN / CLOSE ----------
  function openGear(){
    gearOverlay.classList.add('open');
    gearOff.classList.add('open');
    if(!gearLoaded){
      gear_loadCatalog();
    } else {
      gear_renderAll();
    }
  }

  async function closeGear(){
    // Kalau mau paksa save saat close, bisa aktifkan:
    // if (gearDirty) {
    //   await gear_sendSave(true);
    // }
    gearOverlay.classList.remove('open');
    gearOff.classList.remove('open');
  }

  window.openGear = openGear;

  // ---------- LOAD FROM SERVER ----------
  async function gear_loadCatalog(){
    gear_setStatus('Loading catalog…', 'default');
    try{
      const res  = await fetch('gear.php?gear_action=load_catalog', {cache:'no-store'});
      const json = await res.json();
      if(json.ok && json.catalog){
        gearCatalogState = json.catalog;
      }else{
        gearCatalogState = {};
      }
    }catch(e){
      console.error('gear load_catalog error', e);
      gearCatalogState = {};
      gear_setStatus('Failed to load catalog', 'error');
    }

    ['snorkeling','try_diving','fun_diving','padi_courses'].forEach(ak=>{
      if(!gearCatalogState[ak]) gearCatalogState[ak] = {};
    });

    Object.keys(gearCatalogState).forEach(ak=>{
      if(!gearActivities[ak]){
        gearActivities[ak] = {
          label: ak.replace(/_/g,' ').replace(/\b\w/g, c=>c.toUpperCase()),
          subs : {}
        };
      }
      const subs = gearCatalogState[ak] || {};
      Object.keys(subs).forEach(sk=>{
        if(!gearActivities[ak].subs[sk]){
          gearActivities[ak].subs[sk] = sk.replace(/_/g,' ').replace(/\b\w/g, c=>c.toUpperCase());
        }
      });
    });

    gearLoaded = true;
    gear_renderAll();
    gearDirty = false;
    gear_setStatus('All changes saved', 'saved');
  }

  // ---------- SAVE (AUTOSAVE + MANUAL) ----------
  function gear_scheduleAutosave(){
    gearDirty = true;
    gear_setStatus('Unsaved changes…', 'dirty');
    if(gearAutosaveTimer) clearTimeout(gearAutosaveTimer);
    gearAutosaveTimer = setTimeout(()=>gear_sendSave(false), 800);
  }

  async function gear_sendSave(force){
    if(!gearDirty && !force){
      return;
    }

    gear_setStatus('Saving…', 'default');
    if(gearSaveBtn){
      gearSaveBtn.disabled = true;
      gearSaveBtn.classList.add('opacity-60','cursor-not-allowed');
    }

    const payload = { catalog: gearCatalogState };
    gearAutosaveTimer = null;

    try{
      const res  = await fetch('gear.php?gear_action=autosave_catalog', {
        method : 'POST',
        headers: { 'Content-Type':'application/json' },
        body   : JSON.stringify(payload)
      });

      const text = await res.text(); // ambil mentah dulu
      let json;
      try{
        json = JSON.parse(text);
      }catch(parseErr){
        console.error('gear autosave_catalog: response is not valid JSON:', text);
        gear_setStatus('Save failed (invalid response)', 'error');
        if(gearSaveBtn){
          gearSaveBtn.disabled = false;
          gearSaveBtn.classList.remove('opacity-60','cursor-not-allowed');
        }
        return;
      }

      if(json.ok){
        gearDirty = false;
        gear_setStatus('Saved to DB', 'saved');
      }else{
        console.warn('gear autosave_catalog error payload', json);
        gear_setStatus('Save failed (server error)', 'error');
      }
    }catch(e){
      console.error('gear autosave_catalog network failed', e);
      gear_setStatus('Save failed (network)', 'error');
    }

    if(gearSaveBtn){
      gearSaveBtn.disabled = false;
      gearSaveBtn.classList.remove('opacity-60','cursor-not-allowed');
    }
  }

  function gear_manualSave(){
    gear_sendSave(true);
  }

  // Sync save sebelum unload (optional safety)
  function gear_sendSaveSync(){
    if (!gearDirty) return;

    const payload = JSON.stringify({ catalog: gearCatalogState });

    try {
      if (navigator.sendBeacon) {
        const blob = new Blob([payload], {type: 'application/json'});
        navigator.sendBeacon('gear.php?gear_action=autosave_catalog', blob);
        gearDirty = false;
      } else {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'gear.php?gear_action=autosave_catalog', false);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(payload);
        gearDirty = false;
      }
    } catch (e) {
      console.error('gear_sendSaveSync error', e);
    }
  }

  // ---------- STATE HELPERS ----------
  function gear_getState(activity, subKey){
    if(!gearCatalogState[activity]) gearCatalogState[activity] = {};
    if(!gearCatalogState[activity][subKey]){
      gearCatalogState[activity][subKey] = {
        short_desc : '',
        long_desc  : '',
        image      : ''
      };
    }
    return gearCatalogState[activity][subKey];
  }

  function gear_updateField(activity, subKey, field, value){
    const st = gear_getState(activity, subKey);
    st[field] = value;
    gear_scheduleAutosave(); // autosave + tandai dirty
  }

  // ---------- RENDER ----------
  function gear_renderActivityBody(activity){
    const cfg = gearActivities[activity];
    if(!cfg) return '<p class="text-xs text-[var(--muted)]">Unknown activity.</p>';

    let html = '';
    const subsEntries = Object.entries(cfg.subs);

    if(subsEntries.length === 0){
      html += '<p class="text-[11px] text-[var(--muted)] px-1">No dive site / level configured for this activity.</p>';
      return html;
    }

    subsEntries.forEach(([subKey, label])=>{
      const st = gear_getState(activity, subKey);
      const safeShort = st.short_desc || '';
      const safeLong  = st.long_desc || '';
      const safeImage = st.image || '';

      html += `
        <div class="card p-3 space-y-2">
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-location-dot text-sky-300"></i>
              <div>
                <div class="text-sm font-semibold">${gear_escapeHtml(label)}</div>
                <div class="text-[10px] text-[var(--muted)] break-all">${gear_escapeHtml(activity)} · ${gear_escapeHtml(subKey)}</div>
              </div>
            </div>
          </div>

          <div class="grid gap-2">
            <div>
              <label class="text-xs text-slate-300">Short description</label>
              <textarea
                rows="2"
                class="w-full bg-[#020617] border border-slate-700 rounded-md p-1.5 text-xs text-slate-100 resize-y"
                data-activity="${gear_escapeHtml(activity)}"
                data-sub="${gear_escapeHtml(subKey)}"
                data-field="short_desc"
              >${gear_escapeHtml(safeShort)}</textarea>
            </div>

            <div>
              <label class="text-xs text-slate-300">Long description</label>
              <textarea
                rows="4"
                class="w-full bg-[#020617] border border-slate-700 rounded-md p-1.5 text-xs text-slate-100 resize-y"
                data-activity="${gear_escapeHtml(activity)}"
                data-sub="${gear_escapeHtml(subKey)}"
                data-field="long_desc"
              >${gear_escapeHtml(safeLong)}</textarea>
            </div>

            <div class="grid grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)] gap-2 items-start">
              <div>
                <label class="text-xs text-slate-300">Main image URL</label>
                <input
                  type="text"
                  class="w-full bg-[#020617] border border-slate-700 rounded-md p-1.5 text-xs text-slate-100"
                  placeholder="https://balidiving.com/images/..."
                  value="${gear_escapeHtml(safeImage)}"
                  data-activity="${gear_escapeHtml(activity)}"
                  data-sub="${gear_escapeHtml(subKey)}"
                  data-field="image"
                />
                <p class="mt-1 text-[10px] text-[var(--muted)]">
                  Digunakan sebagai gambar utama di kartu / halaman produk untuk dive site / level ini.
                </p>
              </div>
              <div class="flex flex-col items-center gap-1">
                <div class="w-full aspect-video rounded-md border border-slate-700 overflow-hidden bg-[#020617] flex items-center justify-center">
                  ${
                    safeImage
                      ? `<img src="${gear_escapeHtml(safeImage)}" alt="${gear_escapeHtml(label)}" class="w-full h-full object-cover">`
                      : `<span class="text-[10px] text-[var(--muted)] px-2 text-center">No preview</span>`
                  }
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
    });

    return html;
  }

  function gear_renderAll(){
    const order = ['snorkeling','try_diving','fun_diving','padi_courses'];
    const allKeys = Array.from(new Set([...order, ...Object.keys(gearActivities)]));

    let html = '';

    allKeys.forEach(activity=>{
      const cfg = gearActivities[activity];
      if(!cfg) return;

      const bodyHtml = gear_renderActivityBody(activity);

      html += `
        <div class="card">
          <button
            type="button"
            class="w-full flex items-center justify-between gap-2 px-3 py-2 text-left hover:bg-[rgba(15,23,42,0.85)] border-b border-[var(--border)]"
            data-gear-acc-header="${gear_escapeHtml(activity)}"
          >
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-folder-tree text-sky-300"></i>
              <div>
                <div class="text-sm font-semibold">${gear_escapeHtml(cfg.label)}</div>
                <div class="text-[11px] text-[var(--muted)]">
                  ${Object.keys(cfg.subs).length} site / level
                </div>
              </div>
            </div>
            <i class="fa-solid fa-chevron-down text-xs text-[var(--muted)]" data-gear-acc-icon="${gear_escapeHtml(activity)}"></i>
          </button>
          <div
            class="border-t border-[var(--border)] p-3 space-y-2 hidden"
            data-gear-acc-body="${gear_escapeHtml(activity)}"
          >
            ${bodyHtml}
          </div>
        </div>
      `;
    });

    gearAccordion.innerHTML = html;

    // Accordion toggle
    gearAccordion.querySelectorAll('[data-gear-acc-header]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const activity = btn.getAttribute('data-gear-acc-header');
        const body  = gearAccordion.querySelector(`[data-gear-acc-body="${activity}"]`);
        const icon  = gearAccordion.querySelector(`[data-gear-acc-icon="${activity}"]`);
        if(!body) return;
        const isHidden = body.classList.contains('hidden');
        if(isHidden){
          body.classList.remove('hidden');
          if(icon) icon.style.transform = 'rotate(180deg)';
        }else{
          body.classList.add('hidden');
          if(icon) icon.style.transform = 'rotate(0deg)';
        }
      });
    });

    // Bind inputs → autosave
    gearAccordion.querySelectorAll('textarea[data-field], input[data-field]').forEach(el=>{
      const activity = el.getAttribute('data-activity');
      const sub      = el.getAttribute('data-sub');
      const field    = el.getAttribute('data-field');

      const handler = (e)=>{
        gear_updateField(activity, sub, field, e.target.value);
      };

      el.addEventListener('input', handler);
      el.addEventListener('change', handler);
      if(el.tagName === 'TEXTAREA'){
        el.addEventListener('blur', handler);
      }
    });
  }

  // Bind tombol Save now sekali
  if(gearSaveBtn){
    gearSaveBtn.addEventListener('click', gear_manualSave);
  }

  // Optional: sync save sebelum tutup tab
  window.addEventListener('beforeunload', function(){
    if (gearDirty) {
      gear_sendSaveSync();
    }
  });
</script>
