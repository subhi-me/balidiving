<!-- 🌐 Global Loader (paste sebelum </body>) -->
<div id="global-loader"
     role="status"
     aria-live="polite"
     aria-busy="true"
     aria-label="Loading…"
     class="loader-overlay"
     style="display:none;">
  <div class="loader-box">
    <img src="../images/icons/sticker/snorkeling.png" alt="" class="loader-icon" />
    <div class="loader-text">Loading…</div>
  </div>
</div>

<style>
  /* Overlay full-screen */
  .loader-overlay{
    position:fixed; inset:0; z-index:9999;
    display:flex; align-items:center; justify-content:center;
    background:rgba(6, 60, 127, 0.08); /* navy veil */
    -webkit-backdrop-filter:saturate(1.2) blur(4px);
    backdrop-filter:saturate(1.2) blur(4px);
    opacity:0; pointer-events:none; transition:opacity .25s ease;
  }
  .loader-overlay.visible{ opacity:1; pointer-events:auto; }

  /* Card container */
  .loader-box{
    display:flex; gap:.75rem; align-items:center;
    padding:.9rem 1.1rem; border-radius:14px;
    background:rgba(255,255,255,.9);
    box-shadow:0 18px 40px -12px rgba(6,60,127,.25);
    transform:translateY(4px); animation:rise .4s ease forwards;
  }

  .loader-icon{
    width:36px; height:36px; display:block;
    animation:spin 1.2s linear infinite, float 3s ease-in-out infinite;
  }
  .loader-text{
    font:600 0.95rem/1.1 system-ui, -apple-system, Segoe UI, Roboto, "Noto Sans", sans-serif;
    letter-spacing:.02em; color:#063c7f;
  }

  @keyframes spin { to { transform:rotate(360deg); } }
  @keyframes float {
    0%,100% { transform:translateY(0); }
    50%     { transform:translateY(-3px); }
  }
  @keyframes rise {
    from { transform:translateY(8px); opacity:.6; }
    to   { transform:translateY(0); opacity:1; }
  }

  /* Respect user settings */
  @media (prefers-reduced-motion: reduce) {
    .loader-icon{ animation:none; }
    .loader-overlay{ transition:none; }
    .loader-box{ animation:none; }
  }
</style>

<script>
(() => {
  const overlay = document.getElementById('global-loader');
  let showTimer = null;
  let activeFetches = 0;   // track concurrent fetch
  const SHOW_DELAY_MS = 600;  // hanya tampil jika >600ms (perceived fast)
  const MIN_SHOW_MS  = 400;   // minimal tampil biar tidak “kedip”

  function _show() {
    // jika sudah visible, abaikan
    if (overlay.style.display !== 'flex') {
      overlay.style.display = 'flex';
      requestAnimationFrame(() => overlay.classList.add('visible'));
      overlay.dataset.shownAt = String(Date.now());
    }
  }
  function _hide() {
    const since = Number(overlay.dataset.shownAt || 0);
    const wait = Math.max(0, MIN_SHOW_MS - (Date.now() - since));
    setTimeout(() => {
      overlay.classList.remove('visible');
      // tunggu transisi fade-out
      setTimeout(() => { overlay.style.display = 'none'; }, 250);
    }, wait);
  }

  // 1) Initial page load: show if load > SHOW_DELAY_MS
  showTimer = setTimeout(_show, SHOW_DELAY_MS);
  window.addEventListener('load', () => {
    if (showTimer) clearTimeout(showTimer);
    // jika sempat tampil, sembunyikan
    if (overlay.style.display === 'flex') _hide();
  });

  // 2) SPA/Link navigations: show quickly on unload
  window.addEventListener('beforeunload', () => {
    // tampilkan tanpa delay agar user punya feedback saat pindah halaman
    overlay.style.display = 'flex';
    overlay.classList.add('visible');
  });

  // 3) Slow fetch detector: wrapper untuk fetch()
  const origFetch = window.fetch.bind(window);
  window.fetch = async (...args) => {
    let slowTimer;
    activeFetches++;
    slowTimer = setTimeout(_show, SHOW_DELAY_MS);

    try {
      const res = await origFetch(...args);
      return res;
    } finally {
      clearTimeout(slowTimer);
      activeFetches = Math.max(0, activeFetches - 1);
      if (activeFetches === 0 && overlay.style.display === 'flex') _hide();
    }
  };

  // 4) Expose manual controls if needed
  window.Loader = {
    show: () => { if (showTimer) clearTimeout(showTimer); _show(); },
    hide: () => { if (showTimer) clearTimeout(showTimer); _hide(); }
  };
})();
</script>
