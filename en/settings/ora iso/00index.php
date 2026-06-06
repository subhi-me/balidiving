<?php
declare(strict_types=1);

/* Core */
require __DIR__.'/booking_config.php';
require __DIR__.'/booking_migrations.php';
require __DIR__.'/booking_api.php'; // HANYA akan jalan kalau ada ?action=...
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Bali Diving – Booking Calendar (USD Only · IDR Preview)</title>

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
      --green:#14532d; --green-ink:#d9f99d;
      --blue:#1e3a8a;  --blue-ink:#c7d2fe;
      --red:#7f1d1d;   --red-ink:#fecaca;
      --today-ring:rgba(6,182,212,.35);
    }
    body{
      margin:0;
      background:
        radial-gradient(900px 600px at -10% -10%, #0f2b4a 0%, transparent 40%),
        linear-gradient(135deg,var(--bg) 0%,var(--bg2) 100%);
      color:var(--ink);
      font-family: system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      overflow-x:hidden;
    }
    .wrap{
      max-width:1200px;
      margin:20px auto 40px auto;
      padding:16px;
    }
    .card-surface{
      background:linear-gradient(145deg,rgba(15,27,47,0.96),rgba(11,21,39,0.96));
      border-radius:18px;
      border:1px solid var(--border);
      box-shadow:0 24px 60px rgba(0,0,0,0.7);
      padding:20px;
      backdrop-filter:blur(20px);
    }
    .badge{
      display:inline-flex;
      align-items:center;
      gap:6px;
      border-radius:999px;
      border:1px solid rgba(148,163,184,0.5);
      padding:4px 10px;
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:0.06em;
      color:var(--muted);
    }
    .btn-icon{
      border-radius:999px;
      border:1px solid var(--border);
      background:rgba(15,23,42,0.9);
      color:var(--ink);
      padding:6px 10px;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      transition:all .15s ease-out;
    }
    .btn-icon:hover{
      border-color:var(--primary);
      color:var(--primary);
      transform:translateY(-1px);
      box-shadow:0 10px 25px rgba(15,118,110,0.3);
    }
    .pill{
      border-radius:999px;
      border:1px solid rgba(148,163,184,0.5);
      padding:6px 10px;
      font-size:12px;
      color:var(--muted);
      display:inline-flex;
      align-items:center;
      gap:6px;
      background:rgba(15,23,42,0.85);
    }
    .pill strong{
      font-weight:600;
      color:var(--ink);
    }
    .grid-catalog{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
      gap:14px;
      margin-top:16px;
    }
    .catalog-card{
      border-radius:16px;
      border:1px solid rgba(30,64,175,0.65);
      background:radial-gradient(circle at top left,rgba(56,189,248,0.16),transparent 55%),
                 radial-gradient(circle at bottom right,rgba(59,130,246,0.16),transparent 55%),
                 rgba(15,23,42,0.9);
      padding:14px 14px 12px 14px;
      position:relative;
      overflow:hidden;
    }
    .catalog-card::before{
      content:'';
      position:absolute;
      inset:0;
      border-radius:inherit;
      border:1px solid rgba(56,189,248,0.35);
      opacity:0;
      pointer-events:none;
      transition:opacity .15s ease-out;
    }
    .catalog-card:hover::before{
      opacity:1;
    }
    .catalog-title{
      font-size:15px;
      font-weight:600;
      color:#e5f3ff;
      margin:0 0 4px 0;
    }
    .catalog-category{
      font-size:11px;
      letter-spacing:0.08em;
      text-transform:uppercase;
      color:var(--muted);
      margin-bottom:6px;
    }
    .catalog-price{
      font-size:18px;
      font-weight:700;
    }
    .catalog-price span.small{
      font-size:11px;
      font-weight:500;
      opacity:.8;
    }
    .catalog-footer{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-top:10px;
      font-size:11px;
      color:var(--muted);
    }
    .btn-outline{
      border-radius:999px;
      border:1px solid rgba(148,163,184,0.7);
      background:transparent;
      color:var(--ink);
      padding:4px 10px;
      font-size:11px;
      display:inline-flex;
      align-items:center;
      gap:6px;
      cursor:pointer;
      transition:all .15s ease-out;
    }
    .btn-outline:hover{
      border-color:var(--primary);
      color:var(--primary);
      background:rgba(15,23,42,0.85);
    }
    .rate-input{
      background:rgba(15,23,42,0.9);
      border-radius:999px;
      border:1px solid var(--border);
      padding:6px 10px;
      color:var(--ink);
      font-size:13px;
      width:110px;
    }
    .rate-input:focus{
      outline:none;
      border-color:var(--primary);
      box-shadow:0 0 0 1px rgba(56,189,248,0.5);
    }
    .opacity-tail{
      opacity:0.8;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card-surface">
      <!-- Header -->
      <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-[var(--border)]">
        <div>
          <div class="badge mb-2">
            <i class="fa-solid fa-water"></i>
            <span>Internal Tool</span>
          </div>
          <h1 class="m-0 text-2xl font-extrabold bg-clip-text text-transparent"
              style="background-image:linear-gradient(135deg,var(--primary),var(--accent))">
            Bali Diving – Booking Calendar
          </h1>
          <p class="m-0 text-[13px] text-[var(--muted)]">
            USD base pricing with live IDR preview for internal quotation & booking flow.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <button class="btn-icon" aria-label="Notifications">
            <i class="fa-regular fa-bell"></i>
          </button>
          <button class="btn-icon" aria-label="Settings" onclick="alert('Gear panel bisa kamu tambah di versi berikutnya');">
            <i class="fa-solid fa-gear"></i>
          </button>
        </div>
      </header>

      <!-- Global rate panel -->
      <section class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
          <div class="pill">
            <i class="fa-solid fa-calendar-day text-[12px]"></i>
            <span id="today-label">Today: -</span>
          </div>
          <div class="pill">
            <i class="fa-solid fa-coins text-[12px]"></i>
            <span>Base currency: <strong>USD</strong></span>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-[11px] text-[var(--muted)] uppercase tracking-[0.16em]">Conversion Rate</span>
          <div class="flex items-center gap-2">
            <span class="text-[13px] text-[var(--muted)]">1 USD =</span>
            <input id="rate-input" type="number" min="1" step="1" class="rate-input" />
            <span class="text-[13px] text-[var(--muted)]">IDR</span>
          </div>
          <button class="btn-outline" id="btn-save-rate">
            <i class="fa-regular fa-floppy-disk text-[11px]"></i>
            <span>Save</span>
          </button>
        </div>
      </section>

      <!-- Catalog -->
      <section class="mt-5">
        <div class="flex items-center justify-between gap-2 mb-2">
          <h2 class="text-[13px] uppercase tracking-[0.18em] text-[var(--muted)]">
            Activities & Packages
          </h2>
          <span class="text-[11px] text-[var(--muted)]">
            Data from <code>booking_catalog</code>
          </span>
        </div>

        <div id="catalog-list" class="grid-catalog">
          <!-- Cards via JS -->
        </div>

        <div id="empty-state" class="mt-4 text-center text-[13px] text-[var(--muted)] hidden">
          No products found in <code>booking_catalog</code>.  
          You can insert data via database or extend API later.
        </div>
      </section>
    </div>
  </div>

  <script>
    const API_URL = window.location.pathname; // file yang sama (index.php)

    let GLOBALS = {
      usd_to_idr: 16000,
      rate_mode: 'manual'
    };

    async function apiCall(action, options = {}) {
      const url = API_URL + '?action=' + encodeURIComponent(action);
      const fetchOpts = {
        method: options.method || 'GET',
        headers: {
          'Accept': 'application/json'
        }
      };
      if (options.body) {
        fetchOpts.method = 'POST';
        fetchOpts.headers['Content-Type'] = 'application/json';
        fetchOpts.body = JSON.stringify(options.body);
      }
      const res = await fetch(url, fetchOpts);
      if (!res.ok) {
        const text = await res.text();
        throw new Error('API error ' + res.status + ': ' + text);
      }
      return res.json();
    }

    function formatIDR(value) {
      // pisahkan angka dan tail (tiga digit akhir untuk opacity)
      const full = Math.round(value).toString();
      if (full.length <= 3) {
        return `<span>Rp ${full}</span>`;
      }
      const head = full.slice(0, -3);
      const tail = full.slice(-3);
      // format head dengan thousand separator
      const headFormatted = head.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      return `<span>Rp ${headFormatted}.<span class="opacity-tail">${tail}</span></span>`;
    }

    function renderCatalog(catalog) {
      const container = document.getElementById('catalog-list');
      const empty = document.getElementById('empty-state');

      container.innerHTML = '';

      if (!catalog || catalog.length === 0) {
        empty.classList.remove('hidden');
        return;
      }
      empty.classList.add('hidden');

      catalog.forEach(item => {
        const usd = item.base_usd || 0;
        const idr = usd * GLOBALS.usd_to_idr;
        const idrHTML = formatIDR(idr);

        const card = document.createElement('div');
        card.className = 'catalog-card';

        card.innerHTML = `
          <div class="catalog-category">${item.category}</div>
          <h3 class="catalog-title">${item.name}</h3>
          <div class="flex items-end gap-2 mt-1">
            <div class="catalog-price">
              <span>$${usd.toFixed(0)}</span>
              <span class="small text-[var(--muted)]">/person</span>
            </div>
          </div>
          <div class="mt-1 text-[11px] text-[var(--muted)]">
            ${idrHTML} per person (preview)
          </div>
          <div class="catalog-footer">
            <span>ID: ${item.id}</span>
            <button class="btn-outline" type="button">
              <i class="fa-solid fa-link text-[10px]"></i>
              <span>Use in quote</span>
            </button>
          </div>
        `;
        container.appendChild(card);
      });
    }

    function setRateInput(value) {
      const input = document.getElementById('rate-input');
      input.value = Math.round(value);
    }

    async function loadInit() {
      try {
        const data = await apiCall('init');
        if (data.status !== 'ok') {
          console.error('INIT error', data);
          alert('Init error: ' + (data.message || 'Unknown error'));
          return;
        }
        GLOBALS = data.globals || GLOBALS;
        setRateInput(GLOBALS.usd_to_idr || 16000);

        const todayLabel = document.getElementById('today-label');
        if (todayLabel && data.today) {
          todayLabel.textContent = 'Today: ' + data.today;
        }

        renderCatalog(data.catalog || []);
      } catch (err) {
        console.error(err);
        alert('Failed to init: ' + err.message);
      }
    }

    async function saveRate() {
      const input = document.getElementById('rate-input');
      const raw = input.value.trim();
      const val = parseFloat(raw);
      if (!val || val <= 0) {
        alert('Rate tidak valid');
        return;
      }

      try {
        const res = await apiCall('save_globals', {
          body: {
            usd_to_idr: val,
            rate_mode: 'manual'
          }
        });
        if (res.status !== 'ok') {
          alert('Gagal menyimpan: ' + (res.message || 'Unknown error'));
          return;
        }
        GLOBALS = res.globals || GLOBALS;
        setRateInput(GLOBALS.usd_to_idr || val);
        // re-render catalog supaya IDR update
        const initAgain = await apiCall('init');
        renderCatalog(initAgain.catalog || []);
      } catch (err) {
        console.error(err);
        alert('Error saving rate: ' + err.message);
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      loadInit();

      const btnSaveRate = document.getElementById('btn-save-rate');
      btnSaveRate.addEventListener('click', saveRate);
    });
  </script>
</body>
</html>
