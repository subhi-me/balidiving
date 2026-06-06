

<?php
$page = 'events';
include('01-start.php');
?>
<!-- ===== Event Calendar (2 months) + Offcanvas ===== -->
<section class="w-full py-16 bg-gradient-to-b from-white via-slate-50 to-slate-100 text-slate-900">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-2xl md:text-3xl font-semibold">Event Calendar</h2>
        <p class="text-sm text-slate-600 mt-1">
          This month + next month. Click a date for details.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button id="calPrev"
          class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm font-medium">
          ← Prev
        </button>
        <button id="calToday"
          class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm font-medium">
          Today
        </button>
        <button id="calNext"
          class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm font-medium">
          Next →
        </button>
      </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap gap-2 mb-6 text-xs">
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white">
        <span class="w-2 h-2 rounded-full bg-white"></span> Today
      </span>
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
        <span class="w-2 h-2 rounded-full bg-blue-600"></span> Scuba / Ocean
      </span>
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
        <span class="w-2 h-2 rounded-full bg-amber-600"></span> Bali Holy Day
      </span>
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
        <span class="w-2 h-2 rounded-full bg-rose-600"></span> Indonesia Holiday
      </span>
    </div>

    <!-- Two Calendars -->
    <div id="calWrap" class="grid grid-cols-1 md:grid-cols-2 gap-5"></div>
  </div>
</section>

<!-- ===== Offcanvas ===== -->
<div id="offcanvas" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="closeOffcanvas()"></div>

  <div id="offcanvasPanel"
       class="absolute right-0 top-0 h-full w-full max-w-md
              bg-white shadow-2xl
              transform translate-x-full transition-transform duration-300">
    <div class="p-6 border-b flex justify-between items-center">
      <div>
        <h3 class="font-semibold text-lg" id="ocDate">Date</h3>
        <p class="text-xs text-slate-500" id="ocMeta">Local time</p>
      </div>
      <button onclick="closeOffcanvas()" class="text-slate-500 hover:text-slate-900">✕</button>
    </div>

    <div class="p-6 space-y-4">
      <div id="ocEvents" class="space-y-2"></div>

      <a id="ocCTA" href="https://balidiving.com/cart/my-booking"
         class="inline-flex w-full justify-center items-center
                 bg-slate-900 text-white py-3 font-medium
                hover:bg-slate-700 transition">
       Select One
      </a>
<div style="height:20px;"></div>
      <p class="text-xs text-slate-500 leading-relaxed">
           <a id="ocCTA" href="https://balidiving.com/cart/my-booking"
         class="inline-flex w-full justify-center items-center
                rounded-xl bg-slate-900 text-white py-3 font-medium
                hover:bg-slate-700 transition">
        ← My Plan
      </a>
      <div style="height:1px;"></div>
                 <a id="ocCTA" href="https://balidiving.com/contact?page=contact"
         class="inline-flex w-full justify-center items-center
                rounded-xl bg-slate-900 text-white py-3 font-medium
                hover:bg-slate-700 transition">
        Contact Us →
      </a>
            <div style="height:0px;"></div>
                 <a id="ocCTA" href="https://balidiving.com/weather"
         class="inline-flex w-full justify-center items-center
                rounded-xl bg-slate-900 text-white py-3 font-medium
                hover:bg-slate-700 transition">
        Check Weather ↑
      </a>
      </p>
    </div>
  </div>
</div>

<script>
  // ========= Helpers =========
  const pad2 = n => String(n).padStart(2,'0');
  const iso = (y,m,d) => `${y}-${pad2(m+1)}-${pad2(d)}`; // m is 0-based
  const monthLabel = (y,m) => new Date(y,m,1).toLocaleString(undefined,{month:'long', year:'numeric'});
  const weekdayShort = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

  // ========= Events =========
  // type: 'dive' | 'bali' | 'holiday'
  function buildEventIndex(){
    const idx = {};

    function add(dateISO, item){
      idx[dateISO] ||= [];
      idx[dateISO].push(item);
    }

    // --- Fixed global ocean/scuba days (recurring) ---
    // World Oceans Day: Jun 8
    // World Reef Day: Jun 1 (often used by ocean orgs)
    // World Environment Day: Jun 5
    // Earth Day: Apr 22
    for (let y = 2024; y <= 2030; y++){
      add(`${y}-06-08`, { type:'dive', title:'World Oceans Day', note:'Great for ocean-themed promos & cleanups.' });
      add(`${y}-06-01`, { type:'dive', title:'World Reef Day', note:'Reef awareness content + conservation drive.' });
      add(`${y}-06-05`, { type:'dive', title:'World Environment Day', note:'Eco campaign moment.' });
      add(`${y}-04-22`, { type:'dive', title:'Earth Day', note:'Sustainability spotlight.' });

      // International Coastal Cleanup Day (3rd Saturday of September)
      const sept = new Date(y, 8, 1); // Sep 1
      const firstSatOffset = (6 - sept.getDay() + 7) % 7;
      const firstSat = 1 + firstSatOffset;
      const thirdSat = firstSat + 14;
      add(`${y}-09-${pad2(thirdSat)}`, { type:'dive', title:'Coastal Cleanup Day', note:'Community cleanup + PR.' });
    }

    // --- Indonesia National Holidays (from official SKB 2026 + Setkab 2025) ---
    // 2025 (we only inject what you need now; add more if you want)
    add(`2025-12-25`, { type:'holiday', title:'Christmas Day (ID)', note:'National holiday.' });

    // 2026 (from SKB 2026 table)
    add(`2026-01-01`, { type:'holiday', title:'New Year (ID)', note:'Indonesia' });
    add(`2026-01-16`, { type:'holiday', title:"Isra Mi'raj (ID)", note:'Indonesia' });
    add(`2026-02-17`, { type:'holiday', title:'Chinese New Year (ID)', note:'Indonesia' });
    add(`2026-03-19`, { type:'holiday', title:'Nyepi (ID)', note:'Libur Nasional (Bali silent day).' });
    add(`2026-03-21`, { type:'holiday', title:'Eid al-Fitr (ID)', note:'Indonesia' });
    add(`2026-03-22`, { type:'holiday', title:'Eid al-Fitr (ID)', note:'Indonesia' });
    add(`2026-04-03`, { type:'holiday', title:'Good Friday (ID)', note:'Indonesia' });
    add(`2026-04-05`, { type:'holiday', title:'Easter (ID)', note:'Indonesia' });
    add(`2026-05-01`, { type:'holiday', title:'Labour Day (ID)', note:'Indonesia' });
    add(`2026-05-14`, { type:'holiday', title:'Ascension Day (ID)', note:'Indonesia' });
    add(`2026-05-27`, { type:'holiday', title:'Eid al-Adha (ID)', note:'Indonesia' });
    add(`2026-05-31`, { type:'holiday', title:'Vesak (ID)', note:'Indonesia' });
    add(`2026-06-01`, { type:'holiday', title:'Pancasila Day (ID)', note:'Indonesia' });
    add(`2026-06-16`, { type:'holiday', title:'Islamic New Year (ID)', note:'Indonesia' });
    add(`2026-08-17`, { type:'holiday', title:'Independence Day (ID)', note:'Indonesia' });
    add(`2026-08-25`, { type:'holiday', title:'Mawlid (ID)', note:'Indonesia' });
    add(`2026-12-25`, { type:'holiday', title:'Christmas Day (ID)', note:'Indonesia' });

    // --- Bali Holy Days (extra highlight for ops planning) ---
    add(`2026-03-19`, { type:'bali', title:'Nyepi (Bali)', note:'Island slows down. Plan logistics.' });
    add(`2026-06-17`, { type:'bali', title:'Galungan (Bali)', note:'Ceremonies, traffic patterns change.' });

    return idx;
  }

  const EVENT_INDEX = buildEventIndex();

  function dayTagClass(type){
    if (type === 'dive') return 'bg-blue-50 text-blue-700 border border-blue-200';
    if (type === 'bali') return 'bg-amber-50 text-amber-800 border border-amber-200';
    return 'bg-rose-50 text-rose-700 border border-rose-200';
  }

  // ========= Render =========
  const calWrap = document.getElementById('calWrap');
  const btnPrev = document.getElementById('calPrev');
  const btnNext = document.getElementById('calNext');
  const btnToday = document.getElementById('calToday');

  let base = new Date(); // base month = this month initially
  base.setDate(1);

  function renderTwoMonths(){
    calWrap.innerHTML = '';
    const m1 = new Date(base.getFullYear(), base.getMonth(), 1);
    const m2 = new Date(base.getFullYear(), base.getMonth() + 1, 1);
    calWrap.appendChild(renderMonthCard(m1));
    calWrap.appendChild(renderMonthCard(m2));
  }

  function renderMonthCard(dateObj){
    const y = dateObj.getFullYear();
    const m = dateObj.getMonth();
    const card = document.createElement('div');

    card.className = 'rounded-2xl bg-white/90 border border-slate-200 shadow-sm overflow-hidden';

    // Header
    const head = document.createElement('div');
    head.className = 'px-5 py-4 border-b bg-gradient-to-b from-white to-slate-50 flex items-center justify-between';
    head.innerHTML = `
      <div class="font-semibold text-slate-900">${monthLabel(y,m)}</div>
      <div class="text-xs text-slate-500">Events + Holidays</div>
    `;
    card.appendChild(head);

    // Weekdays
    const grid = document.createElement('div');
    grid.className = 'p-4 grid grid-cols-7 gap-2 text-center';
    weekdayShort.forEach(d => {
      const el = document.createElement('div');
      el.className = 'text-[11px] uppercase text-slate-400 font-medium';
      el.textContent = d;
      grid.appendChild(el);
    });

    const firstDay = new Date(y,m,1).getDay();
    const daysInMonth = new Date(y,m+1,0).getDate();

    for(let i=0;i<firstDay;i++){
      grid.appendChild(document.createElement('div'));
    }

    const now = new Date();
    const isThisMonth = (y === now.getFullYear() && m === now.getMonth());
    const today = now.getDate();

    for(let d=1; d<=daysInMonth; d++){
      const dateISO = iso(y,m,d);
      const events = EVENT_INDEX[dateISO] || [];
      const hasDive = events.some(e => e.type === 'dive');
      const hasBali = events.some(e => e.type === 'bali');
      const hasHoliday = events.some(e => e.type === 'holiday');

      const isToday = isThisMonth && d === today;

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = `
        relative aspect-square rounded-xl
        flex items-center justify-center
        text-sm font-semibold transition
        ${isToday
          ? 'bg-slate-900 text-white ring-2 ring-slate-900'
          : 'bg-white hover:bg-slate-50 text-slate-800 border border-slate-200'}
      `;

      btn.innerHTML = `
        <span class="relative z-10">${d}</span>
        ${(hasDive || hasBali || hasHoliday) ? `
          <span class="absolute bottom-1 left-1/2 -translate-x-1/2 flex gap-1">
            ${hasDive ? `<i class="w-1.5 h-1.5 rounded-full bg-blue-600 inline-block"></i>` : ``}
            ${hasBali ? `<i class="w-1.5 h-1.5 rounded-full bg-amber-600 inline-block"></i>` : ``}
            ${hasHoliday ? `<i class="w-1.5 h-1.5 rounded-full bg-rose-600 inline-block"></i>` : ``}
          </span>
        ` : ``}
      `;

      btn.addEventListener('click', () => openOffcanvas(dateISO, events));
      grid.appendChild(btn);
    }

    card.appendChild(grid);
    return card;
  }

  // ========= Offcanvas =========
  const offcanvas = document.getElementById('offcanvas');
  const panel = document.getElementById('offcanvasPanel');
  const ocDate = document.getElementById('ocDate');
  const ocMeta = document.getElementById('ocMeta');
  const ocEvents = document.getElementById('ocEvents');
  const ocCTA = document.getElementById('ocCTA');

  function openOffcanvas(dateISO, events){
    const d = new Date(dateISO + 'T00:00:00');
    ocDate.textContent = d.toLocaleDateString(undefined, { weekday:'long', year:'numeric', month:'long', day:'numeric' });
    ocMeta.textContent = `ISO: ${dateISO}`;

    ocEvents.innerHTML = '';

    if (!events.length){
      ocEvents.innerHTML = `
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700">
          No special event. You can add a custom event for this date.
        </div>
      `;
    } else {
      events.forEach(ev => {
        const row = document.createElement('div');
        row.className = `p-4 rounded-xl ${dayTagClass(ev.type)}`;
        row.innerHTML = `
          <div class="font-semibold">${ev.title}</div>
          ${ev.note ? `<div class="text-xs opacity-80 mt-1">${ev.note}</div>` : ``}
        `;
        ocEvents.appendChild(row);
      });
    }

    // You can wire this to your event editor:
    ocCTA.href = `#event-${dateISO}`;

    offcanvas.classList.remove('hidden');
    requestAnimationFrame(() => panel.classList.remove('translate-x-full'));
  }

  function closeOffcanvas(){
    panel.classList.add('translate-x-full');
    setTimeout(() => offcanvas.classList.add('hidden'), 300);
  }
  window.closeOffcanvas = closeOffcanvas;

  // ========= Nav =========
  btnPrev.addEventListener('click', () => {
    base = new Date(base.getFullYear(), base.getMonth() - 1, 1);
    renderTwoMonths();
  });

  btnNext.addEventListener('click', () => {
    base = new Date(base.getFullYear(), base.getMonth() + 1, 1);
    renderTwoMonths();
  });

  btnToday.addEventListener('click', () => {
    base = new Date();
    base.setDate(1);
    renderTwoMonths();
  });

  // init
  renderTwoMonths();
</script>



<!-- add Section End -->
<?php include('03-end.php')?>
