<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Bali Diving – Booking Calendar</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    :root{
      --bg:#0b1426; --bg2:#0f172a;
      --surface:#0f1b2f; --surface2:#0b1527;
      --border:#233249; --ink:#e2e8f0; --muted:#9fb2cc;
      --primary:#06b6d4; --accent:#3b82f6;

      /* calendar colors */
      --green:#14532d; --green-ink:#d9f99d;
      --blue:#1e3a8a;  --blue-ink:#c7d2fe;
      --red:#7f1d1d;   --red-ink:#fecaca;
      --today-ring:rgba(6,182,212,.35);
    }
    body{
      margin:0; background:
        radial-gradient(900px 600px at -10% -10%, #0f2b4a 0%, transparent 40%),
        linear-gradient(135deg,var(--bg) 0%,var(--bg2) 100%);
      color:var(--ink); font-family:Inter, ui-sans-serif, system-ui, Segoe UI, Roboto, Helvetica, Arial;
      overflow-x:hidden;
    }
    .wrap{max-width:1400px; margin:0 auto; padding:24px;}
    .card{background:var(--surface); border:1px solid var(--border); border-radius:14px;}
    .btn-icon{width:40px;height:40px;border:1px solid var(--border);background:var(--surface2);display:grid;place-items:center;border-radius:10px;color:var(--ink);transition:.2s}
    .btn-icon:hover{background:#153359;border-color:#3a5f8f}

    /* calendar */
    .cal{display:grid;grid-template-columns:repeat(7,1fr);gap:.35rem}
    .cal-item{
      aspect-ratio:1; display:grid; place-items:center; border:1px solid #1b2a42; border-radius:.6rem;
      background:#0b172a; color:#cfe2ff; cursor:pointer; transition:.12s ease;
      position:relative; font-weight:600;
    }
    .cal-item:hover{box-shadow:0 0 0 2px var(--today-ring) inset; transform:translateY(-1px)}
    .cal-h{border:none;background:transparent;color:#7ea0c9;cursor:default}
    .state-green{background:linear-gradient(180deg,var(--green),#0d2f1f);color:var(--green-ink);border-color:#1f6b3a}
    .state-blue {background:linear-gradient(180deg,var(--blue),#0c214e); color:var(--blue-ink); border-color:#1d4ed8}
    .state-red  {background:linear-gradient(180deg,var(--red),#3b0a0a);  color:var(--red-ink);  border-color:#b91c1c}
    .today-ring{box-shadow:0 0 0 2px var(--today-ring) inset}

    .legend-dot{width:.8rem;height:.8rem;border-radius:.2rem}
    .lg-g{background:var(--green)} .lg-b{background:var(--blue)} .lg-r{background:var(--red)}

    /* offcanvas */
    .off{position:fixed;top:0;right:-480px;width:480px;height:100%;background:linear-gradient(180deg,#0f1b2e,#0b1322);border-left:1px solid var(--border);transition:.3s;z-index:60;overflow-y:auto}
    .off.open{right:0}
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.5);opacity:0;visibility:hidden;transition:.3s;z-index:50}
    .ov.open{opacity:1;visibility:visible}

    .toggle{inline-size:2.9rem;block-size:1.5rem;border-radius:999px;background:#243249;position:relative;border:1px solid var(--border);cursor:pointer;display:inline-flex;align-items:center;transition:.2s}
    .toggle::after{content:"";position:absolute;inline-size:1.15rem;block-size:1.15rem;left:.16rem;border-radius:999px;background:white;transition:.2s;box-shadow:0 1px 2px rgba(0,0,0,.25)}
    .toggle.on{background:linear-gradient(135deg,var(--primary),var(--accent));border-color:#0c97ad}
    .toggle.on::after{left:1.6rem}

    .num{width:4.2rem;background:var(--surface2);border:1px solid var(--border);color:var(--ink);border-radius:.5rem;padding:.4rem .45rem;text-align:center}
    .num:focus{outline:2px solid rgba(6,182,212,.25);border-color:#0c97ad}

    /* modal */
    .modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:70}
    .modal.show{display:flex}
    .modal-card{width:92%;max-width:420px;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px}
    .link{display:inline-flex;align-items:center;gap:.5rem;border:1px solid var(--border);background:var(--surface2);padding:.55rem .8rem;border-radius:.6rem}
    .link:hover{border-color:#3a5f8f}
  </style>
</head>
<body>
  <div class="wrap">
    <!-- header -->
    <header class="flex items-center justify-between gap-3 pb-4 border-b border-[var(--border)]">
      <div>
        <h1 class="m-0 text-2xl font-extrabold bg-clip-text text-transparent" style="background-image:linear-gradient(135deg,var(--primary),var(--accent))">Bali Diving</h1>
        <p class="m-0 text-[var(--muted)]">Booking Calendar • Per-date overrides (Offcanvas)</p>
      </div>
      <div class="flex items-center gap-2">
        <button class="btn-icon" aria-label="Notifications"><i class="fa-regular fa-bell"></i></button>
        <button class="btn-icon" aria-label="Settings"><i class="fa-solid fa-gear"></i></button>
      </div>
    </header>

    <!-- Global Settings -->
    <section class="mt-6 card p-4">
      <h2 class="m-0 text-lg font-semibold flex items-center gap-2"><i class="fa-solid fa-sliders"></i> Global Settings</h2>
      <p class="text-sm text-[var(--muted)] mt-1 mb-3">Only global <b>Cutoff</b> & <b>Currency/Conversion</b>. Per-date Offcanvas can override.</p>

      <!-- NOTE: Conversion card moved to COLUMN 2 as requested -->
      <div class="grid md:grid-cols-3 gap-4">
        <!-- 1) Cutoff -->
        <div class="p-3 rounded-lg border border-[var(--border)] bg-[var(--surface2)]">
          <label class="text-sm text-slate-300 flex items-center gap-2 mb-2"><i class="fa-regular fa-hourglass-half"></i> Cutoff (WITA/UTC+8)</label>
          <select id="globalCutoff" class="w-full bg-[#0f172a] border border-slate-600 rounded-md p-2 text-slate-100">
            <option value="11:00">11:00</option>
            <option value="12:00">12:00</option>
            <option value="13:00" selected>13:00</option>
            <option value="14:00">14:00</option>
            <option value="15:00">15:00</option>
            <option value="16:00">16:00</option>
          </select>
        </div>

        <!-- 2) Conversion (Manual/Automatic; default Manual) -->
        <div class="p-3 rounded-lg border border-[var(--border)] bg-[var(--surface2)]">
          <div class="flex items-center justify-between mb-2">
            <label class="text-sm text-slate-300 flex items-center gap-2">
              <i class="fa-solid fa-arrows-rotate"></i> Conversion
            </label>
            <div class="flex items-center gap-2">
              <label class="text-xs text-[var(--muted)]">Mode</label>
              <select id="rateMode" class="bg-[#0f172a] border border-slate-600 rounded-md p-1.5 text-slate-100 text-xs">
                <option value="manual" selected>Manual</option>
                <option value="auto">Automatic</option>
              </select>
            </div>
          </div>

          <div id="manualRow" class="flex items-center gap-2">
            <span class="px-2 py-2 rounded-md border border-slate-600 bg-[#0f172a] text-slate-200 text-sm">1 USD =</span>
            <input id="usdToIdr" type="number" min="0" step="1" placeholder="e.g. 16000" class="flex-1 bg-[#0f172a] border border-slate-600 rounded-md p-2 text-slate-100">
            <span class="px-2 py-2 rounded-md border border-slate-600 bg-[#0f172a] text-slate-200 text-sm">IDR</span>
            <a class="link" href="https://www.bca.co.id/en/informasi/kurs" target="_blank" rel="noopener">
              <i class="fa-solid fa-up-right-from-square"></i> Check
            </a>
          </div>

          <div id="autoRow" class="hidden mt-2 text-xs text-[var(--muted)]">
            Mode: <b>Automatic</b>. Use your external source (e.g. BCA). Click <a class="underline" href="https://www.bca.co.id/en/informasi/kurs" target="_blank" rel="noopener">Check</a>, then value is applied automatically by your backend/integration.
          </div>
        </div>

        <!-- 3) Currency -->
        <div class="p-3 rounded-lg border border-[var(--border)] bg-[var(--surface2)]">
          <label class="text-sm text-slate-300 flex items-center gap-2 mb-2"><i class="fa-solid fa-coins"></i> Currency</label>
          <select id="globalCurrency" class="w-full bg-[#0f172a] border border-slate-600 rounded-md p-2 text-slate-100">
            <option value="USD" selected>USD</option>
            <option value="IDR">IDR</option>
          </select>
        </div>
      </div>
    </section>

    <!-- Booking Calendar (2 months) -->
    <section class="mt-6 card p-4">
      <div class="flex items-center justify-between flex-wrap gap-3 mb-3">
        <h2 class="m-0 text-lg font-semibold flex items-center gap-2"><i class="fa-regular fa-calendar-days"></i> Booking Calendar</h2>
        <div class="flex items-center gap-4 text-sm">
          <span class="flex items-center gap-2"><span class="legend-dot lg-g"></span> All services</span>
          <span class="flex items-center gap-2"><span class="legend-dot lg-b"></span> Some</span>
          <span class="flex items-center gap-2"><span class="legend-dot lg-r"></span> None</span>
        </div>
        <div class="flex items-center gap-2">
          <button class="btn-icon" aria-label="Prev" onclick="prevMonth()"><i class="fa-solid fa-chevron-left"></i></button>
          <div id="monthLabel" class="min-w-[180px] text-center font-semibold"></div>
          <button class="btn-icon" aria-label="Next" onclick="nextMonth()"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>
      <div class="grid lg:grid-cols-2 gap-4">
        <div class="p-3 rounded-lg border border-[var(--border)] bg-[var(--surface2)]">
          <h3 id="curTitle" class="m-0 mb-2 text-center font-semibold"></h3>
          <div id="calA" class="cal"></div>
        </div>
        <div class="p-3 rounded-lg border border-[var(--border)] bg-[var(--surface2)]">
          <h3 id="nextTitle" class="m-0 mb-2 text-center font-semibold"></h3>
          <div id="calB" class="cal"></div>
        </div>
      </div>
    </section>
  </div>

  <!-- Offcanvas (per-date) -->
  <div id="overlay" class="ov" onclick="closeOff()"></div>
  <aside id="offcanvas" class="off" aria-hidden="true">
    <div class="flex items-center justify-between p-4 border-b border-[var(--border)]">
      <h3 class="m-0 text-lg font-semibold flex items-center gap-2">
        <i class="fa-regular fa-calendar"></i> <span id="dateTitle">—</span>
      </h3>
      <button class="btn-icon" onclick="closeOff()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="p-4 space-y-4">
      <!-- Per-date Cutoff override -->
      <div class="card p-3">
        <div class="flex items-center gap-3">
          <label class="text-sm text-slate-300 flex items-center gap-2">
            <i class="fa-regular fa-hourglass-half"></i> Cutoff (override, WITA)
          </label>
          <select id="dateCutoff" class="bg-[#0f172a] border border-slate-600 rounded-md p-2 text-slate-100">
            <option value="">Use Global</option>
            <option value="11:00">11:00</option>
            <option value="12:00">12:00</option>
            <option value="13:00">13:00</option>
            <option value="14:00">14:00</option>
            <option value="15:00">15:00</option>
            <option value="16:00">16:00</option>
          </select>
        </div>
      </div>

      <!-- Activities & sub (dive sites / levels) with max booking -->
      <div class="space-y-3">
        <!-- Snorkeling -->
        <div class="card p-3">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-person-swimming"></i>
              <div>
                <div class="font-semibold">Snorkeling</div>
                <div class="text-xs text-[var(--muted)]">Surface water exploration</div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-slate-300"><i class="fa-solid fa-users"></i> Max</label>
              <input id="max-snorkeling" type="number" min="0" step="1" class="num" value="8">
              <div class="toggle on" data-svc="snorkeling" onclick="toggleSvc(this)"></div>
            </div>
          </div>
          <div class="mt-3 grid gap-2">
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Padang Bai</span>
              <div class="toggle on" data-svc="snorkeling" data-sub="padang_bai" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Tulamben</span>
              <div class="toggle on" data-svc="snorkeling" data-sub="tulamben" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Amed</span>
              <div class="toggle on" data-svc="snorkeling" data-sub="amed" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> NPMP</span>
              <div class="toggle on" data-svc="snorkeling" data-sub="npmp" onclick="toggleSub(this)"></div>
            </div>
          </div>
        </div>

        <!-- Try Diving -->
        <div class="card p-3">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-water"></i>
              <div class="font-semibold">Try Diving</div>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-slate-300"><i class="fa-solid fa-users"></i> Max</label>
              <input id="max-try" type="number" min="0" step="1" class="num" value="8">
              <div class="toggle on" data-svc="try_diving" onclick="toggleSvc(this)"></div>
            </div>
          </div>
          <div class="mt-3 grid gap-2">
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Padang Bai</span>
              <div class="toggle on" data-svc="try_diving" data-sub="padang_bai" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Tulamben</span>
              <div class="toggle on" data-svc="try_diving" data-sub="tulamben" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Amed</span>
              <div class="toggle on" data-svc="try_diving" data-sub="amed" onclick="toggleSub(this)"></div>
            </div>
          </div>
        </div>

        <!-- Fun Diving -->
        <div class="card p-3">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-fish"></i>
              <div class="font-semibold">Fun Diving</div>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-slate-300"><i class="fa-solid fa-users"></i> Max</label>
              <input id="max-fun" type="number" min="0" step="1" class="num" value="8">
              <div class="toggle on" data-svc="fun_diving" onclick="toggleSvc(this)"></div>
            </div>
          </div>
          <div class="mt-3 grid gap-2">
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Padang Bai</span>
              <div class="toggle on" data-svc="fun_diving" data-sub="padang_bai" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Tulamben</span>
              <div class="toggle on" data-svc="fun_diving" data-sub="tulamben" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Amed</span>
              <div class="toggle on" data-svc="fun_diving" data-sub="amed" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> NPMP</span>
              <div class="toggle on" data-svc="fun_diving" data-sub="npmp" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Gili Tepekong/Mimpang</span>
              <div class="toggle on" data-svc="fun_diving" data-sub="gili_tepekong" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Kubu</span>
              <div class="toggle on" data-svc="fun_diving" data-sub="kubu" onclick="toggleSub(this)"></div>
            </div>
          </div>
        </div>

        <!-- PADI Courses -->
        <div class="card p-3">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-graduation-cap"></i>
              <div class="font-semibold">PADI Courses</div>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-slate-300"><i class="fa-solid fa-users"></i> Max</label>
              <input id="max-padi" type="number" min="0" step="1" class="num" value="8">
              <div class="toggle on" data-svc="padi_courses" onclick="toggleSvc(this)"></div>
            </div>
          </div>
          <div class="mt-3 grid gap-2">
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-regular fa-circle-dot"></i> Beginners</span>
              <div class="toggle on" data-svc="padi_courses" data-sub="beginners" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-regular fa-circle-dot"></i> Advanced</span>
              <div class="toggle on" data-svc="padi_courses" data-sub="advanced" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-regular fa-circle-dot"></i> Specialty</span>
              <div class="toggle on" data-svc="padi_courses" data-sub="specialty" onclick="toggleSub(this)"></div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-md border border-[var(--border)] bg-[var(--surface2)]">
              <span class="flex items-center gap-2"><i class="fa-regular fa-circle-dot"></i> Dive Master</span>
              <div class="toggle on" data-svc="padi_courses" data-sub="dive_master" onclick="toggleSub(this)"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </aside>

  <!-- Modal: Choose scope (Today only / Every {Day}) -->
  <div id="scopeModal" class="modal" aria-hidden="true">
    <div class="modal-card">
      <h3 class="m-0 text-lg font-semibold flex items-center gap-2 mb-2"><i class="fa-solid fa-circle-exclamation"></i> Apply availability change</h3>
      <p class="text-sm text-[var(--muted)] mb-3">This item is being set to <b>Not available</b>. Choose where to apply:</p>
      <div class="grid gap-2">
        <button id="scopeToday" class="w-full py-2 rounded-md bg-[var(--surface2)] border border-[var(--border)] hover:border-sky-600">
          Today only <span class="text-[var(--muted)]" id="scopeDateLabel"></span>
        </button>
        <button id="scopeWeekday" class="w-full py-2 rounded-md bg-[var(--surface2)] border border-[var(--border)] hover:border-sky-600">
          Every <span id="weekdayScopeName">Monday</span>
        </button>
      </div>
      <div class="mt-3 text-right">
        <button class="link" onclick="closeScopeModal()"><i class="fa-solid fa-xmark"></i> Cancel</button>
      </div>
    </div>
  </div>

  <script>
    /* ---------- GLOBAL SETTINGS (cutoff + currency + conversion mode) ---------- */
    let GLOBAL_CUTOFF = '13:00';
    let GLOBAL_CURRENCY = 'USD';
    let USD_TO_IDR = 16000;
    let RATE_MODE = 'manual'; // 'manual' | 'auto'

    const cutoffSel = document.getElementById('globalCutoff');
    const currencySel = document.getElementById('globalCurrency');
    const rateModeSel = document.getElementById('rateMode');
    const usdToIdrInput = document.getElementById('usdToIdr');
    const manualRow = document.getElementById('manualRow');
    const autoRow = document.getElementById('autoRow');

    cutoffSel.addEventListener('change', e=> GLOBAL_CUTOFF = e.target.value || '13:00');
    currencySel.addEventListener('change', e=> GLOBAL_CURRENCY = e.target.value || 'USD');
    usdToIdrInput.addEventListener('input', e=>{
      const v = parseInt(e.target.value,10);
      if(!isNaN(v) && v>0) USD_TO_IDR = v;
    });

    rateModeSel.addEventListener('change', e=>{
      RATE_MODE = e.target.value;
      if(RATE_MODE==='manual'){
        usdToIdrInput.disabled = false;
        manualRow.classList.remove('hidden');
        autoRow.classList.add('hidden');
      }else{
        // Automatic: disable manual field; backend/integration should set rate using external source
        usdToIdrInput.disabled = true;
        manualRow.classList.add('hidden');
        autoRow.classList.remove('hidden');
      }
    });

    // initialize default visual
    (function initConversionUI(){
      rateModeSel.value = 'manual';
      usdToIdrInput.disabled = false;
      manualRow.classList.remove('hidden');
      autoRow.classList.add('hidden');
    })();

    /* ---------- CALENDAR + DATA ---------- */
    const monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];
    const dayNames=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
    let baseDate = new Date();

    const weeklyDefaults = {
      snorkeling   : {mon:true,tue:true,wed:true,thu:true,fri:true,sat:true,sun:true},
      try_diving   : {mon:true,tue:true,wed:true,thu:true,fri:true,sat:true,sun:true},
      fun_diving   : {mon:true,tue:true,wed:true,thu:true,fri:true,sat:true,sun:true},
      padi_courses : {mon:true,tue:true,wed:true,thu:true,fri:true,sat:true,sun:true}
    };
    const weeklySubs = {
      snorkeling:   { mon:{padang_bai:true,tulamben:true,amed:true,npmp:true}, tue:{}, wed:{}, thu:{}, fri:{}, sat:{}, sun:{} },
      try_diving:   { mon:{padang_bai:true,tulamben:true,amed:true},           tue:{}, wed:{}, thu:{}, fri:{}, sat:{}, sun:{} },
      fun_diving:   { mon:{padang_bai:true,tulamben:true,amed:true,npmp:true,gili_tepekong:true,kubu:true}, tue:{}, wed:{}, thu:{}, fri:{}, sat:{}, sun:{} },
      padi_courses: { mon:{beginners:true,advanced:true,specialty:true,dive_master:true}, tue:{}, wed:{}, thu:{}, fri:{}, sat:{}, sun:{} }
    };
    ["tue","wed","thu","fri","sat","sun"].forEach(d=>{
      Object.keys(weeklySubs).forEach(svc=>{
        weeklySubs[svc][d] = JSON.parse(JSON.stringify(weeklySubs[svc].mon));
      });
    });

    const dateSettings = {};
    function fmtDateKey(d){ return d.toISOString().split('T')[0]; }
    function wdKey(d){ return ['sun','mon','tue','wed','thu','fri','sat'][d.getDay()]; }

    function ensureDate(d){
      const key = fmtDateKey(d);
      if(!dateSettings[key]){
        const wd = wdKey(d);
        dateSettings[key] = {
          cutoff: '',
          max:{ snorkeling:8, try_diving:8, fun_diving:8, padi_courses:8 },
          svc:{
            snorkeling: weeklyDefaults.snorkeling[wd],
            try_diving: weeklyDefaults.try_diving[wd],
            fun_diving: weeklyDefaults.fun_diving[wd],
            padi_courses: weeklyDefaults.padi_courses[wd]
          },
          subs:{
            snorkeling:   JSON.parse(JSON.stringify(weeklySubs.snorkeling[wd])),
            try_diving:   JSON.parse(JSON.stringify(weeklySubs.try_diving[wd])),
            fun_diving:   JSON.parse(JSON.stringify(weeklySubs.fun_diving[wd])),
            padi_courses: JSON.parse(JSON.stringify(weeklySubs.padi_courses[wd]))
          }
        };
      }
      return dateSettings[key];
    }

    function dayState(d){
      const S = ensureDate(d);
      const list = ['snorkeling','try_diving','fun_diving','padi_courses'];
      const count = list.filter(s=>S.svc[s]).length;
      if(count===4) return 'state-green';
      if(count===0) return 'state-red';
      return 'state-blue';
    }

    function labelMonths(){
      const a = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
      const b = new Date(a); b.setMonth(a.getMonth()+1);
      document.getElementById('monthLabel').textContent = `${monthNames[a.getMonth()]} ${a.getFullYear()} – ${monthNames[b.getMonth()]} ${b.getFullYear()}`;
      document.getElementById('curTitle').textContent = `${monthNames[a.getMonth()]} ${a.getFullYear()}`;
      document.getElementById('nextTitle').textContent = `${monthNames[b.getMonth()]} ${b.getFullYear()}`;
    }
    function makeGrid(root, dateObj){
      root.innerHTML='';
      dayNames.forEach(n=>{
        const h=document.createElement('div');
        h.className='cal-item cal-h';
        h.textContent=n;
        root.appendChild(h);
      });
      const y=dateObj.getFullYear(), m=dateObj.getMonth();
      const first=new Date(y,m,1);
      const start=new Date(first); start.setDate(start.getDate()-first.getDay());
      const todayKey = fmtDateKey(new Date());

      for(let i=0;i<42;i++){
        const d=new Date(start); d.setDate(start.getDate()+i);
        const el=document.createElement('div');
        el.className='cal-item';
        el.textContent=d.getDate();

        if(d.getMonth()!==m){ el.style.opacity='.35'; el.style.cursor='not-allowed'; }
        else{
          el.classList.add(dayState(d));
          el.addEventListener('click', ()=>openOff(d));
        }
        if(fmtDateKey(d)===todayKey){ el.classList.add('today-ring'); }
        root.appendChild(el);
      }
    }
    function draw(){
      labelMonths();
      const a = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
      const b = new Date(a); b.setMonth(a.getMonth()+1);
      makeGrid(document.getElementById('calA'), a);
      makeGrid(document.getElementById('calB'), b);
    }
    function prevMonth(){ baseDate.setMonth(baseDate.getMonth()-1); draw(); }
    function nextMonth(){ baseDate.setMonth(baseDate.getMonth()+1); draw(); }
    window.prevMonth=prevMonth; window.nextMonth=nextMonth;

    /* ---------- OFFCANVAS (per-date) ---------- */
    let currentDate = null;
    let pendingScope = null;
    const overlay = document.getElementById('overlay');
    const off = document.getElementById('offcanvas');

    function openOff(d){
      currentDate = new Date(d);
      const opt = {weekday:'long', year:'numeric', month:'long', day:'numeric'};
      document.getElementById('dateTitle').textContent = currentDate.toLocaleDateString('en-US', opt);
      const S = ensureDate(currentDate);

      const sel = document.getElementById('dateCutoff');
      sel.value = S.cutoff || '';
      sel.onchange = e=>{ S.cutoff = e.target.value; };

      document.getElementById('max-snorkeling').value = S.max.snorkeling;
      document.getElementById('max-try').value        = S.max.try_diving;
      document.getElementById('max-fun').value        = S.max.fun_diving;
      document.getElementById('max-padi').value       = S.max.padi_courses;

      document.getElementById('max-snorkeling').oninput = e=>{ S.max.snorkeling = clampInt(e.target.value,0); };
      document.getElementById('max-try').oninput        = e=>{ S.max.try_diving = clampInt(e.target.value,0); };
      document.getElementById('max-fun').oninput        = e=>{ S.max.fun_diving = clampInt(e.target.value,0); };
      document.getElementById('max-padi').oninput       = e=>{ S.max.padi_courses = clampInt(e.target.value,0); };

      setToggle('[data-svc="snorkeling"]', S.svc.snorkeling);
      setToggle('[data-svc="try_diving"]', S.svc.try_diving);
      setToggle('[data-svc="fun_diving"]', S.svc.fun_diving);
      setToggle('[data-svc="padi_courses"]', S.svc.padi_courses);

      setSub('snorkeling','padang_bai',S.subs.snorkeling.padang_bai);
      setSub('snorkeling','tulamben',S.subs.snorkeling.tulamben);
      setSub('snorkeling','amed',S.subs.snorkeling.amed);
      setSub('snorkeling','npmp',S.subs.snorkeling.npmp);

      setSub('try_diving','padang_bai',S.subs.try_diving.padang_bai);
      setSub('try_diving','tulamben',S.subs.try_diving.tulamben);
      setSub('try_diving','amed',S.subs.try_diving.amed);

      setSub('fun_diving','padang_bai',S.subs.fun_diving.padang_bai);
      setSub('fun_diving','tulamben',S.subs.fun_diving.tulamben);
      setSub('fun_diving','amed',S.subs.fun_diving.amed);
      setSub('fun_diving','npmp',S.subs.fun_diving.npmp);
      setSub('fun_diving','gili_tepekong',S.subs.fun_diving.gili_tepekong);
      setSub('fun_diving','kubu',S.subs.fun_diving.kubu);

      setSub('padi_courses','beginners',S.subs.padi_courses.beginners);
      setSub('padi_courses','advanced',S.subs.padi_courses.advanced);
      setSub('padi_courses','specialty',S.subs.padi_courses.specialty);
      setSub('padi_courses','dive_master',S.subs.padi_courses.dive_master);

      overlay.classList.add('open'); off.classList.add('open');
    }
    function closeOff(){ overlay.classList.remove('open'); off.classList.remove('open'); draw(); }

    function clampInt(v,min){ v=parseInt(v,10); return isNaN(v)?min:Math.max(min,v); }
    function setToggle(sel,on){ document.querySelectorAll(sel).forEach(el=>el.classList.toggle('on',!!on)); }
    function setSub(svc,sub,on){ const el = document.querySelector(`.toggle[data-svc="${svc}"][data-sub="${sub}"]`); if(el) el.classList.toggle('on',!!on); }

    function toggleSvc(el){
      const svc = el.getAttribute('data-svc');
      const S = ensureDate(currentDate);
      const newState = !el.classList.contains('on');

      if(!newState){
        openScopeModal({type:'svc', svc, newState:false, date:new Date(currentDate)});
      }else{
        S.svc[svc] = true;
        el.classList.add('on');
      }
    }
    function toggleSub(el){
      const svc = el.getAttribute('data-svc');
      const sub = el.getAttribute('data-sub');
      const newState = !el.classList.contains('on');

      if(!newState){
        openScopeModal({type:'sub', svc, sub, newState:false, date:new Date(currentDate)});
      }else{
        const S = ensureDate(currentDate);
        S.subs[svc][sub] = true;
        el.classList.add('on');
      }
    }
    window.toggleSvc = toggleSvc;
    window.toggleSub = toggleSub;

    /* ---------- MODAL: scope selection ---------- */
    const modal = document.getElementById('scopeModal');
    const scopeTodayBtn = document.getElementById('scopeToday');
    const scopeWeekdayBtn = document.getElementById('scopeWeekday');
    const scopeDateLabel = document.getElementById('scopeDateLabel');
    const weekdayScopeName = document.getElementById('weekdayScopeName');

    function openScopeModal(p){
      pendingScope = p;
      const opt = {weekday:'long', month:'long', day:'numeric', year:'numeric'};
      scopeDateLabel.textContent = `(${pendingScope.date.toLocaleDateString('en-US',opt)})`;
      weekdayScopeName.textContent = ` ${pendingScope.date.toLocaleDateString('en-US',{weekday:'long'})}`;
      modal.classList.add('show');
    }
    function closeScopeModal(){ modal.classList.remove('show'); pendingScope=null; }
    window.closeScopeModal = closeScopeModal;

    scopeTodayBtn.onclick = ()=>{
      if(!pendingScope) return;
      const S = ensureDate(pendingScope.date);
      if(pendingScope.type==='svc'){
        S.svc[pendingScope.svc] = false;
        Object.keys(S.subs[pendingScope.svc]).forEach(k=>S.subs[pendingScope.svc][k]=false);
        document.querySelectorAll(`.toggle[data-svc="${pendingScope.svc}"]`).forEach(el=>el.classList.remove('on'));
        document.querySelector(`.toggle[data-svc="${pendingScope.svc}"]:not([data-sub])`)?.classList.remove('on');
      }else{
        S.subs[pendingScope.svc][pendingScope.sub] = false;
        document.querySelector(`.toggle[data-svc="${pendingScope.svc}"][data-sub="${pendingScope.sub}"]`)?.classList.remove('on');
      }
      closeScopeModal();
      draw();
    };

    scopeWeekdayBtn.onclick = ()=>{
      if(!pendingScope) return;
      const wdMap = ['sun','mon','tue','wed','thu','fri','sat'];
      const wd = wdMap[pendingScope.date.getDay()];

      if(pendingScope.type==='svc'){
        weeklyDefaults[pendingScope.svc][wd] = false;
        Object.keys(weeklySubs[pendingScope.svc][wd]).forEach(k=>weeklySubs[pendingScope.svc][wd][k]=false);
      }else{
        weeklySubs[pendingScope.svc][wd][pendingScope.sub] = false;
        const allOff = Object.values(weeklySubs[pendingScope.svc][wd]).every(v=>v===false);
        if(allOff) weeklyDefaults[pendingScope.svc][wd] = false;
      }
      scopeTodayBtn.onclick(); // also reflect today
      draw();
    };

    /* ---------- INIT ---------- */
    document.addEventListener('DOMContentLoaded', draw);
  </script>
</body>
</html>
