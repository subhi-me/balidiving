<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Instagram Story - Bali Diving</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Bubbles */
    @keyframes floatUp {
      0% { transform: translateY(0) scale(1); opacity: 0.2; }
      50% { opacity: 0.5; }
      100% { transform: translateY(-120%) scale(1.1); opacity: 0; }
    }
    .bubble { border-radius: 9999px; will-change: transform, opacity; }

    /* Crossfade seamless (duration dikontrol via JS) */
    .slide-plane { position:absolute; inset:0; opacity:0; filter: blur(0.25px); transition: opacity 900ms ease, filter 900ms ease; --flipX: 1; transform: scaleX(var(--flipX)); }
    .slide-plane.show { opacity:1; filter: blur(0); }
    .slide-plane.hide { opacity:0; filter: blur(0.8px); }
    .slide-img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }

    /* Ken Burns subtle */
    @keyframes panZoomWriggle {
      0%   { transform: scale(1.02) translate(0px, 0px) rotate(0deg); }
      15%  { transform: scale(1.06) translate(6px, -4px) rotate(0.06deg); }
      35%  { transform: scale(1.1) translate(-6px, 5px) rotate(-0.05deg); }
      55%  { transform: scale(1.12) translate(5px, -3px) rotate(0.07deg); }
      75%  { transform: scale(1.08) translate(-4px, 4px) rotate(-0.06deg); }
      100% { transform: scale(1.02) translate(0px, 0px) rotate(0deg); }
    }
    .slide-img { transform: scaleX(var(--flipX)); }
    .kenburns { animation: panZoomWriggle 8s cubic-bezier(.22,.61,.36,1) infinite alternate; will-change: transform; transform-origin: 48% 58%; }

    /* Underwater overlays + tint (dikontrol via CSS variables) */
    #storyRoot { 
      --raysOpacity: .30; 
      --causticsOpacity: .18; 
      --caustics2Opacity: .10;
      --dustOpacity: .24;
      --dust2Opacity: .14;
      --ripplesOpacity: .10;
      --tintColor: rgba(0, 80, 120, .08);
      --tintOpacity: .08;
      --raysSpeed: 10s;
      --causticsSpeed: 9s;
    }
    .tint-overlay {
      position: absolute; inset: 0; pointer-events: none; z-index: 1;
      background: var(--tintColor);
      opacity: var(--tintOpacity);
      mix-blend-mode: multiply;
      transition: background-color .25s ease, opacity .25s ease;
    }

    /* Caustics: dua lapis */
    @keyframes causticsMoveA {
      0% { background-position: 0 0, 0 0; transform: translateY(0) rotate(0deg); }
      50% { background-position: 120px 140px, -90px 110px; transform: translateY(-2.8%) rotate(0.6deg); }
      100% { background-position: 0 0, 0 0; transform: translateY(0) rotate(0deg); }
    }
    @keyframes causticsMoveB {
      0% { background-position: 0 0, 0 0; transform: translateY(0) rotate(0deg) scale(1); }
      50% { background-position: -150px 100px, 110px -80px; transform: translateY(-3.5%) rotate(-0.7deg) scale(1.02); }
      100% { background-position: 0 0, 0 0; transform: translateY(0) rotate(0deg) scale(1); }
    }
    .water-caustics {
      position: absolute; inset: 0; pointer-events: none; z-index: 4;
      background:
        radial-gradient(110px 80px at 22% 32%, rgba(255,255,255,0.20), rgba(255,255,255,0) 60%),
        radial-gradient(130px 90px at 70% 62%, rgba(255,255,255,0.13), rgba(255,255,255,0) 60%);
      mix-blend-mode: screen;
      opacity: var(--causticsOpacity);
      filter: blur(1px);
      animation: causticsMoveA var(--causticsSpeed) ease-in-out infinite;
      transition: opacity .25s ease;
    }
    .water-caustics2 {
      position: absolute; inset: 0; pointer-events: none; z-index: 5;
      background:
        radial-gradient(80px 60px at 30% 70%, rgba(255,255,255,0.16), rgba(255,255,255,0) 60%),
        radial-gradient(90px 70px at 75% 35%, rgba(255,255,255,0.10), rgba(255,255,255,0) 60%);
      mix-blend-mode: screen;
      opacity: var(--caustics2Opacity);
      filter: blur(0.8px);
      animation: causticsMoveB calc(var(--causticsSpeed) * 1.2) ease-in-out infinite;
      transition: opacity .25s ease;
    }

    /* Rays */
    @keyframes raysDrift {
      0% { transform: translateY(0) translateX(0) rotate(-8deg); }
      50% { transform: translateY(-4%) translateX(2%) rotate(-6.5deg); }
      100% { transform: translateY(0) translateX(0) rotate(-8deg); }
    }
    .water-rays {
      position: absolute; inset: -12% -12% -12% -12%; pointer-events: none; z-index: 3;
      background:
        repeating-linear-gradient(72deg, rgba(255,255,255,0.10) 0px, rgba(255,255,255,0.10) 2px, rgba(255,255,255,0.0) 12px, rgba(255,255,255,0.0) 28px),
        repeating-linear-gradient(76deg, rgba(255,255,255,0.06) 0px, rgba(255,255,255,0.06) 3px, rgba(255,255,255,0.0) 14px, rgba(255,255,255,0.0) 30px);
      mask-image: radial-gradient(120% 80% at 50% -10%, black 30%, transparent 80%);
      -webkit-mask-image: radial-gradient(120% 80% at 50% -10%, black 30%, transparent 80%);
      mix-blend-mode: screen;
      filter: blur(1.6px) saturate(1.08);
      opacity: var(--raysOpacity);
      animation: raysDrift var(--raysSpeed) ease-in-out infinite;
      transition: opacity .25s ease;
    }

    /* Sway */
    @keyframes sway {
      0% { transform: translateY(0) rotate(0deg) scale(1); }
      50% { transform: translateY(1.6%) rotate(0.15deg) scale(1.002); }
      100% { transform: translateY(0) rotate(0deg) scale(1); }
    }
    .water-sway {
      position: absolute; inset: 0; pointer-events: none; z-index: 2;
      backdrop-filter: blur(1px) saturate(1.06);
      animation: sway 6s ease-in-out infinite;
    }

    /* Partikel & Ripple */
    @keyframes dustDrift { 0% { background-position: 0 0, 0 0; } 50% { background-position: 20px -30px, -25px 18px; } 100% { background-position: 0 0, 0 0; } }
    .water-dust{
      position:absolute; inset:0; pointer-events:none; z-index:6;
      background:
        radial-gradient(2.2px 2.2px at 10% 20%, rgba(255,255,255,0.45), transparent 60%),
        radial-gradient(1.8px 1.8px at 70% 60%, rgba(255,255,255,0.34), transparent 60%),
        radial-gradient(2.0px 2.0px at 30% 80%, rgba(255,255,255,0.28), transparent 60%),
        radial-gradient(1.5px 1.5px at 85% 40%, rgba(255,255,255,0.26), transparent 60%);
      filter: blur(0.2px);
      opacity: var(--dustOpacity);
      animation: dustDrift 12s ease-in-out infinite;
    }
    .water-dust2{
      position:absolute; inset:0; pointer-events:none; z-index:7;
      background:
        radial-gradient(1.2px 1.2px at 20% 35%, rgba(255,255,255,0.35), transparent 60%),
        radial-gradient(1.0px 1.0px at 40% 75%, rgba(255,255,255,0.28), transparent 60%),
        radial-gradient(0.9px 0.9px at 78% 50%, rgba(255,255,255,0.24), transparent 60%),
        radial-gradient(1.4px 1.4px at 62% 25%, rgba(255,255,255,0.22), transparent 60%);
      filter: blur(0.3px);
      opacity: var(--dust2Opacity);
      animation: dustDrift2 18s ease-in-out infinite, dustBlink 3.6s ease-in-out infinite;
    }
    @keyframes dustDrift2 {
      0% { background-position: 0 0, 0 0, 0 0, 0 0; }
      50% { background-position: -18px 24px, 26px -22px, -14px 16px, 18px -14px; }
      100% { background-position: 0 0, 0 0, 0 0, 0 0; }
    }
    @keyframes dustBlink {
      0%, 100% { opacity: 0.85; }
      40% { opacity: 1; }
      60% { opacity: 0.7; }
    }
    @keyframes rippleDrift{ 0% { background-position: 0 0, 0 0; } 50% { background-position: 30px 18px, -22px -14px; } 100% { background-position: 0 0, 0 0; } }
    .water-ripples{
      position:absolute; inset:-2% -2% -2% -2%; pointer-events:none; z-index:2;
      background:
        radial-gradient(60% 30% at 50% 10%, rgba(255,255,255,0.08), transparent 60%),
        radial-gradient(40% 20% at 55% 12%, rgba(255,255,255,0.05), transparent 60%);
      mix-blend-mode: soft-light;
      opacity: var(--ripplesOpacity);
      animation: rippleDrift 16s ease-in-out infinite;
    }

    /* Caption themes */
    .caption-box { transition: background-color .25s ease, color .25s ease, border-color .25s ease; }
    .caption-dark { background: rgba(0,0,0,0.45); color: #ffffff; border-color: rgba(255,255,255,0.25); text-shadow: 0 1px 2px rgba(0,0,0,.65), 0 6px 18px rgba(0,0,0,.35); }
    .caption-light { background: rgba(255,255,255,0.72); color: #0f172a; border-color: rgba(255,255,255,0.65); text-shadow: 0 1px 1px rgba(255,255,255,.25), 0 1px 14px rgba(255,255,255,.2); }

    /* Main Title: topmost with protective backdrop for readability */
    #mainTitleWrap { position:absolute; inset:0; z-index:50; pointer-events:none; }
    #mainTitleBox {
      opacity: 0;
      transition-property: opacity, transform;
      transition-timing-function: ease;
      display: inline-block;
      padding: .6rem 1rem;
      background: linear-gradient(to bottom, rgba(0,0,0,.38), rgba(0,0,0,.22));
      border: 1px solid rgba(255,255,255,.16);
      border-radius: 14px;
      backdrop-filter: blur(4px) saturate(1.02);
      box-shadow: 0 8px 28px rgba(0,0,0,.35);
    }
    #mainTitle {
      text-align:center;
      line-height: 1.2;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-cyan-900 via-sky-900 to-indigo-900 text-white antialiased selection:bg-cyan-300 selection:text-cyan-900">
  <div class="max-w-[1100px] mx-auto px-4 py-8">
    <header class="mb-6">
      <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Instagram Story – Bali Diving</h1>
      <p class="text-cyan-200/80 mt-1">Unggah gambar atau tempelkan URL gambar, lalu story akan berputar otomatis.</p>
      <p class="text-amber-300/90 text-sm mt-2">Catatan: Memuat otomatis dari folder lokal "images/bg" tidak didukung. Silakan gunakan unggahan atau URL.</p>
    </header>

    <!-- Controls: Upload + URL -->
    <section class="mb-5 grid lg:grid-cols-3 gap-4">
      <div class="bg-white/5 border border-white/10 rounded-xl p-4 backdrop-blur">
        <h2 class="font-medium mb-2">Sumber Gambar</h2>
        <div class="flex items-center gap-3 flex-wrap">
          <input id="fileInput" type="file" accept="image/*" multiple class="hidden" />
          <button id="btnUpload" class="px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 active:bg-cyan-600 text-cyan-950 font-semibold transition">Unggah Gambar</button>
          <span class="text-sm text-cyan-200/80">atau tempel URL di kanan</span>
        </div>
        <p id="uploadInfo" class="text-xs text-cyan-200/70 mt-2">Belum ada gambar.</p>
      </div>

      <div class="bg-white/5 border border-white/10 rounded-xl p-4 backdrop-blur">
        <h2 class="font-medium mb-2">Tempel URL Gambar</h2>
        <form id="urlForm" class="space-y-2">
          <textarea id="urlTextarea" rows="3" placeholder="Satu URL per baris (harus diawali https://)" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 text-white placeholder-white/50 outline-none focus:ring-2 focus:ring-cyan-400"></textarea>
          <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 active:bg-indigo-600 text-indigo-50 font-semibold transition">Muat URL</button>
            <button id="btnClear" type="button" class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition">Hapus Semua</button>
          </div>
        </form>
        <p class="text-xs text-cyan-200/70 mt-2">Contoh: https://images.unsplash.com/photo-...</p>
      </div>

      <!-- Settings -->
      <div class="bg-white/5 border border-white/10 rounded-xl p-4 backdrop-blur">
        <h2 class="font-medium mb-2">Pengaturan</h2>
        <div class="space-y-3">
          <div>
            <label for="durationSelect" class="block text-sm text-white/80 mb-1">Durasi transisi</label>
            <select id="durationSelect" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400">
              <option value="600">600 ms</option>
              <option value="900" selected>900 ms (disarankan)</option>
              <option value="1200">1200 ms</option>
              <option value="1600">1600 ms</option>
            </select>
          </div>
          <div>
            <label for="effectSelect" class="block text-sm text-white/80 mb-1">Efek bawah laut (warna/tint)</label>
            <select id="effectSelect" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400">
              <option value="none">Nonaktif</option>
              <option value="soft">Halus</option>
              <option value="classic" selected>Klasik</option>
              <option value="deep">Deep Blue</option>
              <option value="emerald">Emerald</option>
              <option value="dreamy">Dreamy</option>
            </select>
          </div>
          <div>
            <label for="animSelect" class="block text-sm text-white/80 mb-1">Animasi bawah laut</label>
            <select id="animSelect" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400">
              <option value="all" selected>Semua (gelombang + sinar + caustics)</option>
              <option value="none">Tanpa animasi</option>
              <option value="sway">Hanya gelombang</option>
              <option value="rays">Hanya sinar</option>
              <option value="caustics">Hanya caustics</option>
              <option value="dust">Hanya partikel kecil</option>
              <option value="intense">Semua (lebih kuat)</option>
            </select>
          </div>
          <div>
            <label for="mirrorSelect" class="block text-sm text-white/80 mb-1">Probabilitas mirror</label>
            <select id="mirrorSelect" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400">
              <option value="0">0% (tidak pernah)</option>
              <option value="0.25">25%</option>
              <option value="0.5" selected>50% (default)</option>
              <option value="0.75">75%</option>
              <option value="1">100% (selalu mirror)</option>
            </select>
          </div>

          <!-- NEW: Main Title controls -->
          <div class="pt-2 border-t border-white/10">
            <h3 class="text-sm font-medium mb-2">Judul Utama</h3>
            <div class="flex items-center gap-3 mb-2">
              <label class="inline-flex items-center gap-2 text-sm">
                <input id="titleEnable" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-white/10">
                Tampilkan Judul Utama
              </label>
            </div>
            <input id="titleTextInput" type="text" placeholder="Tulis judul utama di sini..." class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 text-white placeholder-white/50 outline-none focus:ring-2 focus:ring-emerald-400 mb-2" />
            <label for="titlePresetSelect" class="block text-xs text-white/70 mb-1">Preset gaya, warna, durasi & interval</label>
            <select id="titlePresetSelect" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400">
              <option value="classic">Elegan Putih (halus)</option>
              <option value="cyanGlow" selected>Bold Glow Cyan</option>
              <option value="goldSerif">Serif Emas</option>
              <option value="neonOcean">Neon Ocean</option>
              <option value="minimal">Minimal Slate</option>
            </select>
            <p class="text-xs text-cyan-200/70 mt-1">Preset mengatur font style, warna, durasi fade, dan jeda tampil otomatis.</p>
          </div>

          <!-- Website input -->
          <div class="pt-2 border-t border-white/10">
            <label for="websiteInput" class="block text-sm text-white/80 mb-1">Alamat website</label>
            <input id="websiteInput" type="text" placeholder="contoh: www.balidiving.com atau https://domainmu.com" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 text-white placeholder-white/50 outline-none focus:ring-2 focus:ring-emerald-400" />
            <p class="text-xs text-cyan-200/70 mt-1">Tersimpan otomatis. Tombol di story akan ikut berubah.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Story -->
    <section class="w-full">
      <div class="mx-auto w-full max-w-[430px] sm:max-w-[480px] md:max-w-[540px] lg:max-w-[600px]">
        <div id="storyRoot" class="relative w-full aspect-[9/16] rounded-[28px] overflow-hidden shadow-2xl ring-1 ring-white/10 bg-black/20">
          <!-- Underwater overlays -->
          <div class="tint-overlay" id="tintOverlay"></div>
          <div class="water-sway"></div>
          <div class="water-ripples"></div>
          <div class="water-rays"></div>
          <div class="water-caustics"></div>
          <div class="water-caustics2"></div>
          <div class="water-dust"></div>
          <div class="water-dust2"></div>

          <!-- Slides -->
          <div id="slideLayer" class="absolute inset-0">
            <div id="placeholder" class="absolute inset-0">
              <div class="absolute inset-0 bg-gradient-to-tr from-cyan-700 via-sky-700 to-indigo-800"></div>
              <svg class="absolute bottom-0 left-0 w-[140%] h-[60%] opacity-70" viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="rgba(255,255,255,0.12)" d="M0,256L60,224C120,192,240,128,360,106.7C480,85,600,107,720,138.7C840,171,960,213,1080,218.7C1200,224,1320,192,1380,176L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
              </svg>
              <div id="bubbles" class="absolute inset-0 pointer-events-none"></div>
            </div>

            <!-- Two planes -->
            <div id="imgA" class="slide-plane hide kenburns" style="--flipX:1"><img alt="Slide A" class="slide-img" onerror="this.src=''; this.alt='Image failed to load'"></div>
            <div id="imgB" class="slide-plane hide kenburns" style="--flipX:1"><img alt="Slide B" class="slide-img" onerror="this.src=''; this.alt='Image failed to load'"></div>
          </div>

          <!-- Caption -->
          <div class="absolute inset-0 p-5 sm:p-6 flex">
            <div class="mt-auto mb-24 w-full">
              <div id="captionBox" class="caption-box caption-dark max-w-[88%] backdrop-blur-md border rounded-2xl p-4 sm:p-5 shadow-lg transition-opacity duration-500">
                <h3 id="autoText" title="Double-klik untuk edit" class="cursor-text select-text text-lg sm:text-xl md:text-2xl font-semibold leading-snug">
                  Menyelam di perairan Bali bersama Bali Diving — air jernih, karang warna-warni, dan pengalaman yang tak terlupakan.
                </h3>
              </div>
            </div>
          </div>

          <!-- Website -->
          <a id="websiteLink" href="https://www.balidiving.com" target="_blank" rel="noopener noreferrer" title="Double-klik untuk mengedit" class="absolute bottom-16 left-1/2 -translate-x-1/2 text-[15px] sm:text-base tracking-wide text-white bg-black/75 hover:bg-black/80 px-4 py-2 rounded-full border border-white/30 transition shadow-lg backdrop-blur">
            <span id="websiteText">www.balidiving.com</span>
          </a>

          <!-- NEW: Main Title Layer (topmost) -->
          <div id="mainTitleWrap" class="flex items-start justify-center">
            <div class="w-full px-5 pt-16 sm:pt-20 flex justify-center">
              <div id="mainTitleBox" class="ring-1 ring-white/10"><h1 id="mainTitle" class="mx-auto max-w-[92%] leading-tight"></h1></div>
            </div>
          </div>
        </div>

        <!-- Offcanvas trigger -->
        <div class="mt-4 flex flex-col items-center">
          <button id="openNotes" class="px-5 py-2.5 rounded-full bg-emerald-400 text-emerald-950 font-semibold hover:bg-emerald-300 active:bg-emerald-500 transition shadow">Notes & Captions</button>
          <p class="text-xs text-white/70 mt-2">Tip: Double-klik caption untuk edit cepat. Panel ini menyimpan 10 catatan dan bisa di-export TXT.</p>
        </div>
      </div>
    </section>
  </div>

  <!-- Offcanvas & Backdrop -->
  <div id="backdrop" class="fixed inset-0 bg-black/50 hidden z-40"></div>
  <aside id="offcanvas" class="fixed left-0 right-0 bottom-0 bg-slate-900 text-white border-t border-white/10 rounded-t-2xl shadow-2xl p-5 sm:p-6 max-h-[80vh] overflow-y-auto z-50" style="transform: translateY(100%);">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold">Notes & Captions (10)</h3>
      <button id="closeNotes" class="px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/20">Tutup</button>
    </div>
    <p class="text-sm text-cyan-200/80 mt-1">Isi 10 catatan. Tersimpan otomatis di perangkatmu. Caption akan memakai isian ini.</p>

    <form id="notesForm" class="mt-4 grid sm:grid-cols-2 gap-4">
      <div><label class="block text-xs text-white/70 mb-1">Catatan 1</label><textarea id="note1" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 2</label><textarea id="note2" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 3</label><textarea id="note3" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 4</label><textarea id="note4" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 5</label><textarea id="note5" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 6</label><textarea id="note6" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 7</label><textarea id="note7" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 8</label><textarea id="note8" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 9</label><textarea id="note9" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
      <div><label class="block text-xs text-white/70 mb-1">Catatan 10</label><textarea id="note10" rows="2" class="w-full rounded-lg bg-white/10 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-400"></textarea></div>
    </form>

    <div class="mt-4 flex flex-wrap gap-3">
      <button id="applyNotes" class="px-4 py-2 rounded-full bg-emerald-500 text-emerald-950 font-semibold hover:bg-emerald-400">Gunakan sebagai Caption</button>
      <button id="exportNotes" class="px-4 py-2 rounded-full bg-indigo-500 text-white font-semibold hover:bg-indigo-400">EXPORT (TXT)</button>
      <button id="clearNotes" class="px-4 py-2 rounded-full bg-white/10 text-white hover:bg-white/20">Bersihkan</button>
    </div>
  </aside>

  <script>
    // State
    const imageSources = [];
    let current = 0;
    const intervalMs = 4000;
    let fadeMs = 900;
    let timer = null;

    // Upload elements
    const fileInput = document.getElementById('fileInput');
    const btnUpload = document.getElementById('btnUpload');
    const uploadInfo = document.getElementById('uploadInfo');
    const urlForm = document.getElementById('urlForm');
    const urlTextarea = document.getElementById('urlTextarea');
    const btnClear = document.getElementById('btnClear');

    // Settings
    const durationSelect = document.getElementById('durationSelect');
    const effectSelect = document.getElementById('effectSelect');
    const animSelect = document.getElementById('animSelect');
    const mirrorSelect = document.getElementById('mirrorSelect');
    let mirrorProb = parseFloat(mirrorSelect ? mirrorSelect.value : '0.5') || 0.5;

    // Title controls
    const titleEnable = document.getElementById('titleEnable');
    const titleTextInput = document.getElementById('titleTextInput');
    const titlePresetSelect = document.getElementById('titlePresetSelect');
    const mainTitle = document.getElementById('mainTitle');

    // Slide planes
    const placeholder = document.getElementById('placeholder');
    const planeA = document.getElementById('imgA');
    const planeB = document.getElementById('imgB');
    const imgA = planeA.querySelector('img');
    const imgB = planeB.querySelector('img');
    let front = planeA;
    let back = planeB;

    // Caption
    const captionBox = document.getElementById('captionBox');
    const autoTextEl = document.getElementById('autoText');
    let isEditing = false;
    let savedEditedText = '';

    // Overlays
    const storyRoot = document.getElementById('storyRoot');

    // Offcanvas
    const offcanvas = document.getElementById('offcanvas');
    const backdrop = document.getElementById('backdrop');
    const openNotes = document.getElementById('openNotes');
    const closeNotes = document.getElementById('closeNotes');

    // Website
    const websiteLink = document.getElementById('websiteLink');
    const websiteText = document.getElementById('websiteText');
    const websiteInput = document.getElementById('websiteInput');

    // Notes
    const ids = ['note1','note2','note3','note4','note5','note6','note7','note8','note9','note10'];
    const fields = ids.map(id => document.getElementById(id));
    const applyNotes = document.getElementById('applyNotes');
    const exportNotes = document.getElementById('exportNotes');
    const clearNotes = document.getElementById('clearNotes');

    // Captions default
    let captions = [
      "Sunray ripples, Bali bubbles — dive in.",
      "Blue hush, coral rush. Island calm.",
      "Glide past gardens — colors that glow.",
      "Mask on, smile on. Ocean therapy.",
      "Turtles drift by like old friends.",
      "Salt skin, light fins, easy grins.",
      "Beneath the swell, stories begin.",
      "Wreck shadows, fish confetti — wow.",
      "Slow breaths, steady beats — peace.",
      "Surface with bigger smiles than before."
    ];

    // Bubbles - organic random bursts with drift
    const bubblesLayer = document.getElementById('bubbles');
    function spawnBubbles() {
      bubblesLayer.innerHTML = '';
      const width = bubblesLayer.clientWidth || 400;
      const baseCount = 14;
      const extra = Math.floor(Math.random() * 8); // randomize density
      const count = baseCount + extra;
      for (let i = 0; i < count; i++) {
        const b = document.createElement('div');
        const size = Math.floor(Math.random() * 26) + 6; // 6–32px
        const left = Math.random() * 100;
        const delay = (Math.random() * 5).toFixed(2);
        const duration = (Math.random() * 10 + 6).toFixed(2); // 6–16s
        const driftX = (Math.random() * 16 - 8).toFixed(1); // -8 to 8px
        const blur = Math.random() < 0.35 ? 1 : 0;
        // build
        b.className = 'bubble absolute bottom-[-20px] bg-white/25 shadow-[0_0_12px_rgba(255,255,255,0.25)]';
        b.style.width = size + 'px';
        b.style.height = size + 'px';
        b.style.left = left + '%';
        b.style.filter = blur ? 'blur(0.6px)' : 'none';
        b.style.animation = `floatUp ${duration}s linear ${delay}s forwards`;
        // subtle horizontal drift using CSS variable and JS tick
        const startX = (Math.random()*width/6 - width/12);
        let t = 0, vx = (Math.random()*0.6 + 0.2) * (Math.random()<0.5?-1:1);
        const tick = () => {
          t += 0.016;
          const x = startX + Math.sin(t * (Math.random()*0.8+0.5)) * driftX;
          b.style.transform = `translateX(${x}px)`;
          if (document.body.contains(b)) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        bubblesLayer.appendChild(b);
      }
    }
    spawnBubbles();
    setInterval(spawnBubbles, 11000);

    function updateUploadInfo() {
      uploadInfo.textContent = imageSources.length === 0 ? 'Belum ada gambar.' : `${imageSources.length} gambar siap diputar.`;
    }

    // Crossfade logic
    let isTransitioning = false;
    function crossfadeTo(index) {
      const flip = Math.random() < mirrorProb ? -1 : 1;
      back.style.setProperty('--flipX', flip);
      front.style.setProperty('--flipX', 1);
      back.querySelector('img').style.setProperty('--flipX', flip);
      front.querySelector('img').style.setProperty('--flipX', 1);
      if (imageSources.length === 0) return;
      const src = imageSources[index];
      isTransitioning = true;

      const loader = new Image();
      loader.crossOrigin = 'anonymous';
      loader.onload = () => {
        back.querySelector('img').src = src;
        placeholder.classList.add('hidden');
        back.classList.add('kenburns');
        [front, back].forEach(el => el.style.transition = `opacity ${fadeMs}ms ease, filter ${fadeMs}ms ease`);
        back.classList.add('show'); back.classList.remove('hide');
        front.classList.add('hide'); front.classList.remove('show');

        setTimeout(() => {
          const temp = front; front = back; back = temp;
          back.classList.add('hide'); back.classList.remove('show');
          back.querySelector('img').src = '';
          isTransitioning = false;
          adjustCaptionTheme(src);
        }, fadeMs);
      };
      loader.onerror = () => { isTransitioning = false; };
      loader.src = src;
    }

    function showInitial() {
      const flipFirst = Math.random() < mirrorProb ? -1 : 1;
      front.style.setProperty('--flipX', flipFirst);
      front.querySelector('img').style.setProperty('--flipX', flipFirst);
      if (imageSources.length === 0) {
        placeholder.classList.remove('hidden');
        front.classList.add('hide'); back.classList.add('hide');
        return;
      }
      placeholder.classList.add('hidden');
      [front, back].forEach(el => el.style.transition = `opacity ${fadeMs}ms ease, filter ${fadeMs}ms ease`);
      front.querySelector('img').src = imageSources[current];
      front.classList.add('show','kenburns');
      front.classList.remove('hide');
      back.classList.add('hide');
      adjustCaptionTheme(imageSources[current]);
    }

    function nextIndexRandom() {
      if (imageSources.length <= 1) return 0;
      let n; do { n = Math.floor(Math.random() * imageSources.length); } while (n === current);
      return n;
    }

    function schedule() {
      if (timer) clearInterval(timer);
      if (imageSources.length === 0) return;
      timer = setInterval(() => {
        if (isTransitioning) return;
        const n = nextIndexRandom();
        current = n;
        crossfadeTo(current);
      }, intervalMs);
    }

    // Caption loop
    let captionTimer = null;
    let captionIndex = 0;
    function startCaptionLoop() {
      if (captionTimer) clearInterval(captionTimer);
      captionTimer = setInterval(() => {
        if (captions.length === 0 || isEditing) return;
        captionIndex = (captionIndex + 1) % captions.length;
        fadeCaption(captions[captionIndex]);
      }, 7000);
    }
    function fadeCaption(text) {
      if (isEditing) return;
      autoTextEl.style.opacity = 0;
      setTimeout(() => {
        autoTextEl.textContent = text;
        autoTextEl.style.opacity = 1;
      }, 200);
    }
    function updateCaption() {
      if (captions.length === 0) return;
      fadeCaption(captions[captionIndex % captions.length]);
    }

    // Upload handlers
    function handleFiles(fileList) {
      const reads = [];
      for (const f of fileList) {
        if (!f.type.startsWith('image/')) continue;
        reads.push(new Promise((resolve) => {
          const reader = new FileReader();
          reader.onload = (e) => resolve(e.target.result);
          reader.readAsDataURL(f);
        }));
      }
      Promise.all(reads).then((results) => {
        results.forEach(src => imageSources.push(src));
        current = 0;
        updateUploadInfo();
        showInitial();
        schedule();
      });
    }
    function addUrlsFromTextarea() {
      const lines = urlTextarea.value.split('\n').map(v => v.trim()).filter(v => v.startsWith('https://'));
      if (lines.length === 0) return;
      lines.forEach(url => imageSources.push(url));
      urlTextarea.value = '';
      current = 0;
      updateUploadInfo();
      showInitial();
      schedule();
    }

    // Upload events
    btnUpload.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (e) => { handleFiles(e.target.files); fileInput.value = ''; });
    urlForm.addEventListener('submit', (e) => { e.preventDefault(); addUrlsFromTextarea(); });
    btnClear.addEventListener('click', () => {
      imageSources.length = 0; current = 0;
      if (timer) clearInterval(timer);
      front.src = ''; back.src = '';
      front.classList.remove('show','kenburns'); front.classList.add('hide');
      back.classList.remove('show','kenburns'); back.classList.add('hide');
      placeholder.classList.remove('hidden');
      updateUploadInfo();
    });

    // Editable caption dblclick
    autoTextEl.addEventListener('dblclick', () => {
      if (isEditing) return;
      isEditing = true; savedEditedText = autoTextEl.textContent;
      autoTextEl.setAttribute('contenteditable', 'true'); autoTextEl.focus();
      const range = document.createRange(); range.selectNodeContents(autoTextEl);
      const sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(range);
    });
    function stopEditing(commit = true) {
      if (!isEditing) return;
      autoTextEl.removeAttribute('contenteditable');
      if (!commit) autoTextEl.textContent = savedEditedText;
      isEditing = false; autoTextEl.style.opacity = 1;
    }
    autoTextEl.addEventListener('blur', () => stopEditing(true));
    autoTextEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') { e.preventDefault(); stopEditing(true); }
      else if (e.key === 'Escape') { e.preventDefault(); stopEditing(false); }
    });

    // Offcanvas
    function openPanel() {
      backdrop.classList.remove('hidden');
      offcanvas.style.transition = 'transform 320ms ease';
      offcanvas.style.transform = 'translateY(0)';
    }
    function closePanel() {
      offcanvas.style.transition = 'transform 280ms ease';
      offcanvas.style.transform = 'translateY(100%)';
      setTimeout(() => backdrop.classList.add('hidden'), 280);
    }
    openNotes.addEventListener('click', openPanel);
    closeNotes.addEventListener('click', closePanel);
    backdrop.addEventListener('click', closePanel);

    // Website input + save
    const LS_WEB = 'bd_website_v1';
    function toUrl(val){
      let s = (val || '').trim();
      if(!s) return '';
      if (!/^https?:\/\//i.test(s)) s = 'https://' + s.replace(/^\/*/, '');
      return s;
    }
    function setWebsite(val, save=true){
      const text = (val || 'www.balidiving.com').trim();
      const url = toUrl(text);
      websiteText.textContent = text;
      websiteLink.href = url || 'https://www.balidiving.com';
      if (websiteInput) websiteInput.value = text;
      if (save) try{ localStorage.setItem(LS_WEB, text); }catch{}
    }
    try{ const savedWeb = localStorage.getItem(LS_WEB); if(savedWeb){ setWebsite(savedWeb, false); } else { setWebsite(websiteText.textContent || 'www.balidiving.com', false);} }catch{ setWebsite(websiteText.textContent || 'www.balidiving.com', false); }
    if (websiteInput){
      websiteInput.addEventListener('input', ()=> setWebsite(websiteInput.value, true));
      websiteInput.addEventListener('blur', ()=> setWebsite(websiteInput.value, true));
      websiteInput.addEventListener('keydown', (e)=>{ if(e.key==='Enter'){ e.preventDefault(); setWebsite(websiteInput.value, true); websiteInput.blur(); } });
    }
    websiteLink.addEventListener('dblclick', (e)=>{
      e.preventDefault();
      if(!websiteInput) return;
      websiteInput.focus();
      websiteInput.select();
    });

    // Notes LS
    const LS_KEY = 'bd_notes_v2';
    function loadNotes() {
      try { const raw = localStorage.getItem(LS_KEY); if (!raw) return null; const arr = JSON.parse(raw); return Array.isArray(arr) ? arr : null; } catch { return null; }
    }
    function saveNotes(arr) { try { localStorage.setItem(LS_KEY, JSON.stringify(arr)); } catch {} }
    const saved = loadNotes();
    const initialNotes = saved && saved.length ? saved : [
      captions[0]||'', captions[1]||'', captions[2]||'', captions[3]||'', captions[4]||'',
      captions[5]||'', captions[6]||'', captions[7]||'', captions[8]||'', captions[9]||''
    ];
    fields.forEach((el, i) => el.value = initialNotes[i] || '');
    document.getElementById('notesForm').addEventListener('input', () => {
      const arr = fields.map(f => f.value.trim());
      saveNotes(arr);
      const nonEmpty = arr.filter(t => t.length > 0);
      if (nonEmpty.length > 0) {
        captions = nonEmpty; captionIndex = Math.min(captionIndex, captions.length - 1);
        updateCaption();
      }
    });
    applyNotes.addEventListener('click', () => {
      const arr = fields.map(f => f.value.trim());
      const nonEmpty = arr.filter(t => t.length > 0);
      if (nonEmpty.length > 0) { captions = nonEmpty; captionIndex = 0; updateCaption(); }
    });
    exportNotes.addEventListener('click', () => {
      const lines = fields.map(f => f.value.trim()).filter(Boolean);
      const content = lines.join('\n');
      const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a'); a.href = url; a.download = 'captions.txt';
      document.body.appendChild(a); a.click();
      setTimeout(() => { URL.revokeObjectURL(url); a.remove(); }, 0);
    });
    clearNotes.addEventListener('click', () => {
      fields.forEach(f => f.value = ''); saveNotes(new Array(10).fill(''));
      captions = []; autoTextEl.textContent = '';
    });

    // Caption readability sampler
    const samplerCanvas = document.createElement('canvas'); samplerCanvas.width = samplerCanvas.height = 8;
    const ctx = samplerCanvas.getContext('2d');
    function setCaptionTheme(light) {
      captionBox.classList.toggle('caption-light', !!light);
      captionBox.classList.toggle('caption-dark', !light);
    }
    function adjustCaptionTheme(src) {
      if (!src) { setCaptionTheme(false); return; }
      const probe = new Image(); probe.crossOrigin = 'anonymous';
      probe.onload = () => {
        try {
          const w = samplerCanvas.width, h = samplerCanvas.height;
          ctx.clearRect(0,0,w,h);
          ctx.drawImage(probe, Math.max(0, probe.width/2 - 100), Math.max(0, probe.height*0.65 - 80), 200, 160, 0, 0, w, h);
          const data = ctx.getImageData(0,0,w,h).data;
          let r=0,g=0,b=0, count=0;
          for (let i=0; i<data.length; i+=4) { r+=data[i]; g+=data[i+1]; b+=data[i+2]; count++; }
          const avgR=r/count, avgG=g/count, avgB=b/count;
          const brightness = Math.sqrt(0.241*avgR*avgR + 0.691*avgG*avgG + 0.068*avgB*avgB);
          setCaptionTheme(brightness > 160);
        } catch { setCaptionTheme(false); }
      };
      probe.onerror = () => setCaptionTheme(false);
      probe.src = src;
    }

    // Apply settings
    function applyDuration(ms) {
      fadeMs = ms;
      [planeA, planeB].forEach(el => el.style.transition = `opacity ${ms}ms ease, filter ${ms}ms ease`);
    }
    function applyEffect(preset) {
      const sets = (rays, caus, caus2, dust, rip, tint, tintCol, speeds={})=>{
        storyRoot.style.setProperty('--raysOpacity', rays);
        storyRoot.style.setProperty('--causticsOpacity', caus);
        storyRoot.style.setProperty('--caustics2Opacity', caus2);
        storyRoot.style.setProperty('--dustOpacity', dust);
        storyRoot.style.setProperty('--ripplesOpacity', rip);
        storyRoot.style.setProperty('--tintOpacity', tint);
        storyRoot.style.setProperty('--tintColor', tintCol);
        if (speeds.rays) storyRoot.style.setProperty('--raysSpeed', speeds.rays);
        if (speeds.caustics) storyRoot.style.setProperty('--causticsSpeed', speeds.caustics);
      };
      switch (preset) {
        case 'none': sets('0','0','0','0','0','0','rgba(0,0,0,0)'); break;
        case 'soft': sets('.18','.10','.06','.08','.08','.06','rgba(0,80,120,.10)', {rays:'12s', caustics:'11s'}); break;
        case 'classic': sets('.30','.18','.10','.14','.10','.08','rgba(0,90,130,.12)', {rays:'10s', caustics:'9s'}); break;
        case 'deep': sets('.22','.14','.08','.10','.08','.16','rgba(0,50,120,.22)', {rays:'11s', caustics:'10s'}); break;
        case 'emerald': sets('.24','.15','.10','.12','.10','.16','rgba(0,120,90,.22)', {rays:'11s', caustics:'10s'}); break;
        case 'dreamy': sets('.36','.22','.12','.16','.12','.18','rgba(160,200,255,.12)', {rays:'9s', caustics:'8s'}); break;
      }
    }
    function applyAnim(preset){
      const sway = document.querySelector('.water-sway');
      const rays = document.querySelector('.water-rays');
      const caustics = document.querySelector('.water-caustics');
      const caustics2 = document.querySelector('.water-caustics2');
      const ripples = document.querySelector('.water-ripples');
      const dust = document.querySelector('.water-dust');
      [sway, rays, caustics, caustics2, ripples, dust].forEach(el=> el && (el.style.display=''));

      switch(preset){
        case 'none':
          [sway, rays, caustics, caustics2, ripples, dust].forEach(el=> el && (el.style.display='none'));
          break;
        case 'sway':
          [rays, caustics, caustics2, ripples, dust].forEach(el=> el && (el.style.display='none'));
          sway.style.display='';
          break;
        case 'rays':
          [sway, caustics, caustics2, ripples, dust].forEach(el=> el && (el.style.display='none'));
          rays.style.display='';
          break;
        case 'caustics':
          [sway, rays, ripples, dust].forEach(el=> el && (el.style.display='none'));
          caustics.style.display='';
          caustics2.style.display='';
          break;
        case 'dust':
          [sway, rays, caustics, caustics2, ripples].forEach(el=> el && (el.style.display='none'));
          dust.style.display='';
          document.querySelector('.water-dust2').style.display='';
          storyRoot.style.setProperty('--dustOpacity', '.32');
          storyRoot.style.setProperty('--dust2Opacity', '.22');
          break;
        case 'intense':
          [sway, rays, caustics, caustics2, ripples, dust].forEach(el=> el && (el.style.display=''));
          document.querySelector('.water-dust2').style.display='';
          storyRoot.style.setProperty('--raysOpacity', '.46');
          storyRoot.style.setProperty('--causticsOpacity', '.30');
          storyRoot.style.setProperty('--caustics2Opacity', '.16');
          storyRoot.style.setProperty('--dustOpacity', '.28');
          storyRoot.style.setProperty('--dust2Opacity', '.18');
          storyRoot.style.setProperty('--ripplesOpacity', '.16');
          storyRoot.style.setProperty('--raysSpeed', '8s');
          storyRoot.style.setProperty('--causticsSpeed', '7s');
          break;
        case 'all':
        default:
          [sway, rays, caustics, caustics2, ripples, dust].forEach(el=> el && (el.style.display=''));
          break;
      }
    }

    // Title presets and loop
    const LS_TITLE = 'bd_title_v1';
    const titlePresets = {
      classic: {
        label: 'Elegan Putih', color: '#ffffff', shadow: '0 2px 18px rgba(0,0,0,.35), 0 1px 2px rgba(0,0,0,.8)',
        fontFamily: "'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif",
        weight: 700, letterSpacing: '0.02em',
        size: 'clamp(22px, 4.5vw, 38px)',
        transitionMs: 900, visibleMs: 2800, intervalMs: 8000
      },
      cyanGlow: {
        label: 'Bold Glow Cyan', color: '#dbfbff', shadow: '0 0 22px rgba(34,211,238,.55), 0 2px 12px rgba(0,0,0,.45)',
        fontFamily: "'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif",
        weight: 800, letterSpacing: '0.03em',
        size: 'clamp(24px, 5vw, 42px)',
        transitionMs: 800, visibleMs: 2400, intervalMs: 6500
      },
      goldSerif: {
        label: 'Serif Emas', color: '#fff4c2', shadow: '0 0 16px rgba(251,191,36,.5), 0 2px 10px rgba(0,0,0,.45)',
        fontFamily: "Georgia, 'Times New Roman', Times, serif",
        weight: 700, letterSpacing: '0.01em',
        size: 'clamp(22px, 4.6vw, 40px)',
        transitionMs: 1100, visibleMs: 3000, intervalMs: 9000, italic: true
      },
      neonOcean: {
        label: 'Neon Ocean', color: '#c8f1ff', shadow: '0 0 30px rgba(56,189,248,.70), 0 0 8px rgba(125,211,252,.85)',
        fontFamily: "'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif",
        weight: 900, letterSpacing: '0.04em',
        size: 'clamp(24px, 5.2vw, 44px)',
        transitionMs: 700, visibleMs: 2200, intervalMs: 6000, uppercase: true
      },
      minimal: {
        label: 'Minimal Slate', color: '#f1f5f9', shadow: '0 1px 8px rgba(0,0,0,.35)',
        fontFamily: "'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif",
        weight: 600, letterSpacing: '0',
        size: 'clamp(20px, 4.2vw, 34px)',
        transitionMs: 700, visibleMs: 2000, intervalMs: 7000
      }
    };
    let titleTimerA = null, titleTimerB = null;
    function clearTitleTimers(){
      if (titleTimerA) clearTimeout(titleTimerA);
      if (titleTimerB) clearTimeout(titleTimerB);
      titleTimerA = titleTimerB = null;
    }
    function applyTitlePreset(key){
      const p = titlePresets[key] || titlePresets.cyanGlow;
      // Apply style
      mainTitle.style.color = p.color;
      mainTitle.style.textShadow = p.shadow + ', 0 0 2px rgba(0,0,0,.35), 0 0 18px rgba(0,0,0,.25)';
      mainTitle.style.fontFamily = p.fontFamily;
      mainTitle.style.fontWeight = String(p.weight);
      mainTitle.style.letterSpacing = p.letterSpacing;
      mainTitle.style.fontStyle = p.italic ? 'italic' : 'normal';
      mainTitle.style.textTransform = p.uppercase ? 'uppercase' : 'none';
      mainTitle.style.fontSize = p.size;
      mainTitle.style.transitionDuration = p.transitionMs + 'ms';
      document.getElementById('mainTitleBox').style.transitionDuration = p.transitionMs + 'ms';
      mainTitle.dataset.visibleMs = p.visibleMs;
      mainTitle.dataset.intervalMs = p.intervalMs;
    }
    function startTitleLoop(){
      clearTitleTimers();
      const enabled = !!titleEnable.checked;
      const text = (titleTextInput.value || '').trim();
      if (!enabled || !text) { mainTitle.style.opacity = 0; return; }
      mainTitle.textContent = text;
      const visibleMs = parseInt(mainTitle.dataset.visibleMs||'2400',10);
      const intervalMs = parseInt(mainTitle.dataset.intervalMs||'6500',10);

      function cycle(){
        document.getElementById('mainTitleBox').style.opacity = 1;
        document.getElementById('mainTitleBox').style.transform = 'translateY(0)';
        titleTimerA = setTimeout(()=>{
          document.getElementById('mainTitleBox').style.opacity = 0;
          titleTimerB = setTimeout(cycle, intervalMs);
        }, visibleMs);
      }
      // small leading delay to settle
      setTimeout(cycle, 200);
    }

    // Persist title settings
    function saveTitleLS(){
      try {
        const obj = { enabled: !!titleEnable.checked, text: titleTextInput.value||'', preset: titlePresetSelect.value||'cyanGlow' };
        localStorage.setItem(LS_TITLE, JSON.stringify(obj));
      } catch {}
    }
    function loadTitleLS(){
      try {
        const raw = localStorage.getItem(LS_TITLE);
        if (!raw) return null;
        const obj = JSON.parse(raw);
        return obj && typeof obj==='object' ? obj : null;
      } catch { return null; }
    }

    // Glitch every 20s
    const glitchCss = document.createElement('style');
    glitchCss.textContent = `
      @keyframes glitchJitter { 0% { clip-path: inset(0 0 0 0); transform: translate(0,0); } 20% { clip-path: inset(10% 0 0 0); transform: translate(-1px,1px) skewX(0.4deg); } 40% { clip-path: inset(0 0 12% 0); transform: translate(1px,-1px) skewY(-0.3deg); } 60% { clip-path: inset(8% 0 0 0); transform: translate(-1px,0.5px); } 80% { clip-path: inset(0 0 6% 0); transform: translate(0.5px,-0.5px); } 100% { clip-path: inset(0 0 0 0); transform: translate(0,0); } }
      .glitching::after, .glitching::before { content:''; position:absolute; inset:0; background: inherit; background-clip: content-box; mix-blend-mode: screen; opacity:.35; pointer-events:none; }
      .glitching::before{ filter:hue-rotate(10deg) saturate(1.2) contrast(1.05); animation: glitchJitter 320ms steps(6,end); }
      .glitching::after{ filter:hue-rotate(-10deg) saturate(1.2) contrast(1.05); animation: glitchJitter 360ms steps(6,end) reverse; }
    `;
    document.head.appendChild(glitchCss);
    function triggerGlitch(){
      const host = front;
      if(!host) return;
      host.classList.add('glitching');
      setTimeout(()=> host.classList.remove('glitching'), 420);
    }
    setInterval(triggerGlitch, 20000);

    // Events
    durationSelect.addEventListener('change', (e) => {
      const ms = parseInt(e.target.value || '900', 10);
      applyDuration(ms);
    });
    effectSelect.addEventListener('change', (e) => { applyEffect(e.target.value); });
    animSelect.addEventListener('change', (e)=>{ applyAnim(e.target.value); });
    if (mirrorSelect) mirrorSelect.addEventListener('change', (e)=>{ mirrorProb = parseFloat(e.target.value || '0.5') || 0.5; });

    // Title events
    titlePresetSelect.addEventListener('change', ()=>{
      applyTitlePreset(titlePresetSelect.value);
      saveTitleLS();
      startTitleLoop();
    });
    titleTextInput.addEventListener('input', ()=>{ saveTitleLS(); startTitleLoop(); });
    titleEnable.addEventListener('change', ()=>{ saveTitleLS(); startTitleLoop(); });

    // Init
    function init() {
      applyDuration(parseInt(durationSelect.value, 10));
      applyEffect(effectSelect.value);
      applyAnim(document.getElementById('animSelect').value);

      // Title init
      const savedTitle = loadTitleLS();
      if (savedTitle){
        titleEnable.checked = !!savedTitle.enabled;
        titleTextInput.value = savedTitle.text || '';
        if (savedTitle.preset && titlePresets[savedTitle.preset]) {
          titlePresetSelect.value = savedTitle.preset;
        }
      } else {
        titleEnable.checked = false;
        titleTextInput.value = '';
        titlePresetSelect.value = 'cyanGlow';
      }
      applyTitlePreset(titlePresetSelect.value);
      startTitleLoop();

      updateUploadInfo();
      showInitial();
      schedule();
      startCaptionLoop();

      // Website saved
      try{ const savedWeb = localStorage.getItem('bd_website_v1'); if(savedWeb){ setWebsite(savedWeb, false); } }catch{}
    }
    init();
  </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98410729f570ff89',t:'MTc1ODcwMzc2MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
