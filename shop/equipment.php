<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dive Shop Product Categories</title>

  <!-- Optional SDK (kalau ada di server kamu). Kalau 404, fallback tetap jalan -->
  <script src="/_sdk/element_sdk.js"></script>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>

  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body { box-sizing: border-box; }

    /* mobile-first: hover effect cuma buat device yang support hover */
    .category-card { transition: transform .25s ease, box-shadow .25s ease; }
    @media (hover: hover) and (pointer: fine){
      .category-card:hover { transform: translateY(-8px); }
    }

    .modal-backdrop { backdrop-filter: blur(8px); animation: fadeIn .25s ease-out; }
    .modal-content { animation: slideUp .25s ease-out; }

    @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
    @keyframes slideUp { from {opacity:0; transform: translateY(18px);} to {opacity:1; transform: translateY(0);} }

    .service-tag { transition: transform .15s ease; }
    @media (hover: hover) and (pointer: fine){
      .service-tag:hover { transform: scale(1.05); }
    }
  </style>

  <style>@view-transition { navigation: auto; }</style>
</head>

<body class="h-full">
  <div id="app" class="w-full h-full overflow-auto"></div>

  <script>
    const defaultConfig = {
      background_color: "#f0f9ff",
      card_color: "#ffffff",
      text_color: "#1e293b",
      primary_action: "#0369a1",
      secondary_action: "#0891b2",
      font_family: "Inter",
      font_size: 16,
      main_title: "Our Product Categories",
      subtitle: "Choose a category to explore our premium diving equipment collection",
      coming_soon_title: "Not ready yet—stay salty 🌊 ",
      coming_soon_message: "Our products are diving to the ocean floor to find the best for you! But don't worry... Standard equipment rental is FREE — always..",
      services_intro: "While waiting, enjoy our fantastic services:",
      cta_question: "Have questions or ready for an adventure?",
      contact_button: "💬 Contact Us",
      plan_button: "🗓️ Continue to Diving Plan",
      close_button: "🏠 Back to BaliDiving.com"
    };

    const HOME_URL = "https://balidiving.com";

    const categories = [
      { id: 1, name: "Masker & Snorkel", icon: `<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 28c-4 0-8 3-8 8v4c0 3 2 6 5 7l3 1c2 0 4-1 5-3l1-3c0-2 2-4 4-4h12c2 0 4 2 4 4l1 3c1 2 3 3 5 3l3-1c3-1 5-4 5-7v-4c0-5-4-8-8-8"/><circle cx="24" cy="32" r="6"/><circle cx="40" cy="32" r="6"/><path d="M24 26v-8m16 8v-8"/></svg>` },
      { id: 2, name: "Regulator", icon: `<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="32" cy="32" r="12"/><circle cx="32" cy="32" r="6"/><path d="M32 20v-8m0 40v-8m12-12h8m-40 0h8"/><path d="M20 32c0-2 1-4 3-5m18 0c2 1 3 3 3 5"/></svg>` },
      { id: 3, name: "BCD & Wetsuit", icon: `<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M24 16h16v12h-16z"/><path d="M24 28v20l-4 8m20-28v20l4 8"/><path d="M28 16v-4c0-2 2-4 4-4s4 2 4 4v4"/><path d="M32 20v8m-4 4h8"/></svg>` },
      { id: 4, name: "Fins", icon: `<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 32h-8l-4 8v8h12v-8z"/><path d="M20 32h24c6 0 12 4 12 10v6H20V32z"/><path d="M28 32v16m8-16v16m8-16v16"/></svg>` },
      { id: 5, name: "Dive Computer", icon: `<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="20" y="24" width="24" height="20" rx="2"/><path d="M24 20v4m16-4v4"/><path d="M26 30h4m-4 4h8m-8 4h6"/><circle cx="38" cy="34" r="2"/></svg>` },
      { id: 6, name: "Underwater Camera", icon: `<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="16" y="24" width="32" height="20" rx="2"/><circle cx="34" cy="34" r="6"/><circle cx="34" cy="34" r="3"/><path d="M28 24l-2-4h12l-2 4"/><circle cx="42" cy="28" r="1"/></svg>` },
      { id: 7, name: "Dive Light", icon: `<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="26" y="16" width="12" height="24" rx="2"/><path d="M26 40l-4 8h20l-4-8"/><path d="M30 20h4m-4 4h4m-4 4h4"/><circle cx="32" cy="32" r="3"/></svg>` },
      { id: 8, name: "Tank & Accessories", icon: `<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="24" y="16" width="16" height="32" rx="8"/><circle cx="32" cy="14" r="3"/><path d="M28 24h8m-8 8h8m-8 8h8"/></svg>` }
    ];

    const services = [
      "🤿 Scuba Diving Tour",
      "🏊 Snorkeling",
      "📚 Diving Lessons (PADI Certification)",
      "🌊 And other services"
    ];

    let currentModal = null;

    async function onConfigChange(config) {
      const app = document.getElementById('app');
      const customFont = config.font_family || defaultConfig.font_family;
      const baseSize = config.font_size || defaultConfig.font_size;
      const baseFontStack = `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`;

      app.style.backgroundColor = config.background_color || defaultConfig.background_color;
      app.style.fontFamily = `${customFont}, ${baseFontStack}`;
      app.style.color = config.text_color || defaultConfig.text_color;

      if (!app.hasChildNodes()) {
        // mobile-first layout: 1 kolom default, baru melebar jadi grid
        app.innerHTML = `
          <div class="w-full" style="min-height: 100%; padding: ${Math.max(18, baseSize * 1.25)}px ${Math.max(14, baseSize * 0.9)}px;">
            <div style="max-width: 1100px; margin: 0 auto;">
              <div style="text-align: center; margin-bottom: ${baseSize * 1.25}px;">
                <h1 id="main-title" style="font-size: ${baseSize * 1.85}px; font-weight: 800; margin-bottom: ${baseSize * 0.35}px; color: #0c4a6e; letter-spacing:-0.02em;"></h1>
                <p id="subtitle" style="font-size: ${baseSize * 1.0}px; color: #334155; line-height:1.45;"></p>
              </div>

              <div id="categories-grid"
                   style="display: grid;
                          grid-template-columns: 1fr;
                          gap: ${baseSize}px;
                          margin-bottom: ${baseSize * 2}px;">
              </div>
            </div>
          </div>

          <div id="modal" style="display:none; position: fixed; inset: 0; z-index: 1000;">
            <div class="modal-backdrop" style="position:absolute; inset:0; background: rgba(0,0,0,0.52);"></div>

            <!-- mobile-first: modal full-width dengan bottom-sheet feel, desktop jadi center -->
            <div style="position: relative; width: 100%; height: 100%; display:flex; align-items:flex-end; justify-content:center; padding: ${baseSize}px;">
              <div class="modal-content"
                   id="modal-content"
                   style="background: white;
                          border-radius: ${baseSize * 1.25}px ${baseSize * 1.25}px ${baseSize * 1.25}px ${baseSize * 1.25}px;
                          width: 100%;
                          max-width: 640px;
                          max-height: 92%;
                          overflow-y: auto;
                          box-shadow: 0 20px 60px rgba(0,0,0,0.35);">
              </div>
            </div>
          </div>
        `;

        // responsive grid columns via JS (mobile-first, lalu upgrade)
        applyResponsiveGrid();

        const modal = document.getElementById('modal');
        const backdrop = modal.querySelector('.modal-backdrop');
        backdrop.addEventListener('click', () => closeModal("hide"));

        // ESC to close (desktop convenience)
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') closeModal("hide");
        });

        // resize handler
        window.addEventListener('resize', applyResponsiveGrid);

        renderCategories();
      }

      document.getElementById('main-title').textContent = config.main_title || defaultConfig.main_title;
      document.getElementById('subtitle').textContent = config.subtitle || defaultConfig.subtitle;

      updateStyles(config);
    }

    function applyResponsiveGrid(){
      const grid = document.getElementById('categories-grid');
      if (!grid) return;
      const w = window.innerWidth || 0;

      // mobile first: 1 kolom, lalu 2, lalu 3-4
      if (w >= 1024) grid.style.gridTemplateColumns = 'repeat(4, minmax(0, 1fr))';
      else if (w >= 820) grid.style.gridTemplateColumns = 'repeat(3, minmax(0, 1fr))';
      else if (w >= 520) grid.style.gridTemplateColumns = 'repeat(2, minmax(0, 1fr))';
      else grid.style.gridTemplateColumns = '1fr';
    }

    function updateStyles(config) {
      const cards = document.querySelectorAll('.category-card');
      cards.forEach(card => {
        card.style.backgroundColor = config.card_color || defaultConfig.card_color;
        card.style.color = config.text_color || defaultConfig.text_color;
      });
    }

    function renderCategories() {
      const grid = document.getElementById('categories-grid');
      const config = (window.elementSdk && window.elementSdk.config) ? window.elementSdk.config : defaultConfig;
      const baseSize = config.font_size || defaultConfig.font_size;

      grid.innerHTML = categories.map(cat => `
        <button
          class="category-card"
          onclick="showModal('${escapeHtml(cat.name)}')"
          style="background: ${config.card_color || defaultConfig.card_color};
                 padding: ${Math.max(14, baseSize)}px;
                 border-radius: ${Math.max(14, baseSize)}px;
                 border: 1px solid rgba(148,163,184,.55);
                 cursor: pointer;
                 box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06);
                 text-align: left;
                 display:flex;
                 gap:${Math.max(12, baseSize * 0.75)}px;
                 align-items:center;">
          <div style="flex:0 0 auto; width:${Math.max(44, baseSize*3)}px; height:${Math.max(44, baseSize*3)}px; border-radius:${Math.max(12, baseSize)}px;
                      display:flex; align-items:center; justify-content:center;
                      background: rgba(3,105,161,0.10);
                      color: ${config.primary_action || defaultConfig.primary_action};">
            <div style="width:${Math.max(28, baseSize*2)}px; height:${Math.max(28, baseSize*2)}px;">
              ${cat.icon}
            </div>
          </div>

          <div style="min-width:0;">
            <div style="font-weight:800; color:#0f172a; font-size:${Math.max(15, baseSize*1.02)}px; line-height:1.2;">
              ${escapeHtml(cat.name)}
            </div>
            <div style="margin-top:6px; font-size:${Math.max(12, baseSize*0.85)}px; color:#475569; line-height:1.35;">
              Tap to view details & services
            </div>
          </div>
        </button>
      `).join('');
    }

    function showModal(categoryName) {
      const modal = document.getElementById('modal');
      const content = document.getElementById('modal-content');
      const config = (window.elementSdk && window.elementSdk.config) ? window.elementSdk.config : defaultConfig;
      const baseSize = config.font_size || defaultConfig.font_size;

      content.innerHTML = `
        <div style="padding: ${Math.max(16, baseSize * 1.25)}px;">
          <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:${baseSize}px;">
            <div style="min-width:0;">
              <div style="font-size:${Math.max(12, baseSize*0.82)}px; color:#64748b; font-weight:700; letter-spacing:.06em; text-transform:uppercase;">
                ${escapeHtml(categoryName)}
              </div>
              <h2 style="margin-top:6px; font-size: ${Math.max(20, baseSize * 1.55)}px; font-weight: 900; line-height:1.15; color: ${config.text_color || defaultConfig.text_color};">
                ${escapeHtml(config.coming_soon_title || defaultConfig.coming_soon_title)}
              </h2>
            </div>

            <a href="${HOME_URL}"
               style="flex:0 0 auto;
                      background: rgba(15,23,42,.06);
                      color:#0f172a;
                      padding:10px 12px;
                      border-radius:12px;
                      text-decoration:none;
                      font-weight:800;
                      font-size:${Math.max(12, baseSize*0.85)}px;">
              Home
            </a>
          </div>

          <p style="font-size: ${Math.max(14, baseSize * 0.98)}px; color: ${config.text_color || defaultConfig.text_color}; opacity: 0.86; margin-bottom: ${baseSize * 1.2}px; line-height:1.55;">
            ${escapeHtml(config.coming_soon_message || defaultConfig.coming_soon_message)}
          </p>

          <div style="background: linear-gradient(135deg, rgba(3, 105, 161, 0.10), rgba(8, 145, 178, 0.10));
                      padding: ${Math.max(14, baseSize)}px;
                      border-radius: ${Math.max(14, baseSize)}px;
                      margin-bottom: ${baseSize * 1.2}px;
                      border: 1px solid rgba(3,105,161,0.18);">
            <p style="font-size: ${Math.max(14, baseSize)}px; font-weight: 800; margin-bottom: ${baseSize * 0.7}px; color: ${config.text_color || defaultConfig.text_color};">
              ${escapeHtml(config.services_intro || defaultConfig.services_intro)}
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:${Math.max(8, baseSize*0.5)}px;">
              ${services.map(service => `
                <span class="service-tag"
                      style="background:white;
                             padding:${Math.max(8, baseSize*0.5)}px ${Math.max(10, baseSize*0.75)}px;
                             border-radius:${Math.max(999, baseSize*2)}px;
                             font-size:${Math.max(12, baseSize*0.85)}px;
                             color:#0f172a;
                             box-shadow:0 2px 6px rgba(0,0,0,0.08);
                             border:1px solid rgba(3,105,161,0.18);">
                  ${escapeHtml(service)}
                </span>
              `).join('')}
            </div>
          </div>

          <div style="text-align:center; margin-bottom:${baseSize}px;">
            <p style="font-size:${Math.max(13, baseSize*0.95)}px; margin-bottom:${baseSize}px; color:${config.text_color || defaultConfig.text_color};">
              ${escapeHtml(config.cta_question || defaultConfig.cta_question)}
            </p>

            <!-- mobile-first: tombol full-width, desktop auto -->
            <div style="display:grid; grid-template-columns: 1fr; gap:${baseSize*0.75}px;">
              <a href="https://balidiving.com/contact?page=contact" target="_self" rel="noopener noreferrer"
                 style="background:${config.primary_action || defaultConfig.primary_action};
                        color:white;
                        padding:${Math.max(12, baseSize*0.8)}px ${Math.max(14, baseSize)}px;
                        border-radius:${Math.max(12, baseSize*0.75)}px;
                        text-decoration:none;
                        font-weight:900;
                        font-size:${Math.max(14, baseSize)}px;
                        display:block;">
                ${escapeHtml(config.contact_button || defaultConfig.contact_button)}
              </a>

              <a href="https://balidiving.com/cart/cart" target="_self" rel="noopener noreferrer"
                 style="background:${config.secondary_action || defaultConfig.secondary_action};
                        color:white;
                        padding:${Math.max(12, baseSize*0.8)}px ${Math.max(14, baseSize)}px;
                        border-radius:${Math.max(12, baseSize*0.75)}px;
                        text-decoration:none;
                        font-weight:900;
                        font-size:${Math.max(14, baseSize)}px;
                        display:block;">
                ${escapeHtml(config.plan_button || defaultConfig.plan_button)}
              </a>
            </div>
          </div>

          <!-- ✅ Close yang kamu minta: redirect ke HOME -->
          <a href="${HOME_URL}"
             style="width:100%;
                    display:block;
                    text-align:center;
                    background:transparent;
                    border:2px solid ${config.text_color || defaultConfig.text_color};
                    color:${config.text_color || defaultConfig.text_color};
                    padding:${Math.max(12, baseSize*0.8)}px;
                    border-radius:${Math.max(12, baseSize*0.75)}px;
                    text-decoration:none;
                    font-weight:900;
                    font-size:${Math.max(14, baseSize)}px;">
            ${escapeHtml(config.close_button || defaultConfig.close_button)}
          </a>
        </div>
      `;

      modal.style.display = 'block';
      currentModal = categoryName;
    }

    // mode:
    // - "hide" = tutup modal aja
    // - "home" = langsung ke home (kamu sekarang pakai link, tapi fungsi ini tetap aman)
    function closeModal(mode = "hide") {
      if (mode === "home") {
        window.location.href = HOME_URL;
        return;
      }
      const modal = document.getElementById('modal');
      if (modal) modal.style.display = 'none';
      currentModal = null;
    }

    function escapeHtml(str) {
      return String(str ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }

    const capabilities = {
      recolorables: [
        { get: () => (window.elementSdk?.config || defaultConfig).background_color, set: (value) => window.elementSdk?.setConfig?.({ background_color: value }) },
        { get: () => (window.elementSdk?.config || defaultConfig).card_color,       set: (value) => window.elementSdk?.setConfig?.({ card_color: value }) },
        { get: () => (window.elementSdk?.config || defaultConfig).text_color,       set: (value) => window.elementSdk?.setConfig?.({ text_color: value }) },
        { get: () => (window.elementSdk?.config || defaultConfig).primary_action,   set: (value) => window.elementSdk?.setConfig?.({ primary_action: value }) },
        { get: () => (window.elementSdk?.config || defaultConfig).secondary_action, set: (value) => window.elementSdk?.setConfig?.({ secondary_action: value }) }
      ],
      borderables: [],
      fontEditable:  { get: () => (window.elementSdk?.config || defaultConfig).font_family, set: (value) => window.elementSdk?.setConfig?.({ font_family: value }) },
      fontSizeable:  { get: () => (window.elementSdk?.config || defaultConfig).font_size,   set: (value) => window.elementSdk?.setConfig?.({ font_size: value }) }
    };

    function bootFallback() { onConfigChange(defaultConfig); }

    if (window.elementSdk && typeof window.elementSdk.init === "function") {
      try {
        window.elementSdk.init({
          defaultConfig,
          onConfigChange,
          mapToCapabilities: () => capabilities,
          mapToEditPanelValues: (config) => new Map([
            ['main_title', config.main_title || defaultConfig.main_title],
            ['subtitle', config.subtitle || defaultConfig.subtitle],
            ['coming_soon_title', config.coming_soon_title || defaultConfig.coming_soon_title],
            ['coming_soon_message', config.coming_soon_message || defaultConfig.coming_soon_message],
            ['services_intro', config.services_intro || defaultConfig.services_intro],
            ['cta_question', config.cta_question || defaultConfig.cta_question],
            ['contact_button', config.contact_button || defaultConfig.contact_button],
            ['plan_button', config.plan_button || defaultConfig.plan_button]
          ])
        });
      } catch (e) {
        console.error("elementSdk init failed, using fallback:", e);
        if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", bootFallback);
        else bootFallback();
      }
    } else {
      if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", bootFallback);
      else bootFallback();
    }
  </script>
</body>
</html>
