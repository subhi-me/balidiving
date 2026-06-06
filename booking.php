<!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bali Diving - Dive Activities</title>
<link rel="icon" href="https://balidiving.com/bali-diving-favicon.png" type="image/png" sizes="144x144">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');

    :root{
      --bg:#063c7f;
      --bgMid:#084a9e;
      --primary:#a2d2fa;
      --text:#ffffff;
      --accent:#f23d4e;

      --radius:16px;
      --shadow: 0 4px 12px rgba(0,0,0,.10);
      --shadowHover: 0 8px 24px rgba(162,210,250,.40);
      --focusRing: 0 0 0 4px rgba(162,210,250,.35);

      --font: Inter, -apple-system, BlinkMacSystemFont, sans-serif;
      --base:16px;

      /* Pattern controls */
      --patternOpacity: .12;
      --patternScale: 64px;
    }

    *{ margin:0; padding:0; box-sizing:border-box; }
    html, body{ height:100%; }
    body{
      font-family: var(--font);
      overflow-x:hidden;
      font-size: var(--base);
      background: var(--bg);
    }

    .app-container{
      min-height: 100%;
      width: 100%;
      position: relative;
      overflow:hidden;
      background: linear-gradient(135deg, var(--bg) 0%, var(--bgMid) 50%, var(--bg) 100%);
    }

    /* ===== Elegant scuba pattern overlay (inline SVG tiled) ===== */
    .app-container::before{
      content:"";
      position:absolute;
      inset:0;
      pointer-events:none;
      z-index:1;
      opacity: var(--patternOpacity);
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='128' height='128' viewBox='0 0 128 128'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.9)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M24 34c6-12 18-20 32-20s26 8 32 20' opacity='.55'/%3E%3Cpath d='M40 52c2-10 11-18 24-18s22 8 24 18' opacity='.55'/%3E%3Cpath d='M52 58l-10 8 10 8M76 58l10 8-10 8' opacity='.55'/%3E%3Cpath d='M56 78c0 6 4 10 8 10s8-4 8-10' opacity='.55'/%3E%3Cpath d='M18 96c10-6 20-6 30 0s20 6 30 0 20-6 30 0' opacity='.35'/%3E%3Cpath d='M92 96c0-10 8-18 18-18' opacity='.35'/%3E%3Ccircle cx='104' cy='78' r='6' opacity='.35'/%3E%3Cpath d='M20 94c0-10 8-18 18-18' opacity='.35'/%3E%3Ccircle cx='32' cy='78' r='6' opacity='.35'/%3E%3C/g%3E%3C/svg%3E");
      background-size: var(--patternScale) var(--patternScale);
      background-repeat: repeat;
      mix-blend-mode: soft-light;
      filter: blur(.15px);
    }

    /* Soft vignette for elegance */
    .app-container::after{
      content:"";
      position:absolute;
      inset:-2px;
      pointer-events:none;
      z-index:2;
      background:
        radial-gradient(1200px 700px at 50% 10%, rgba(255,255,255,.10), transparent 60%),
        radial-gradient(900px 600px at 10% 90%, rgba(162,210,250,.10), transparent 55%),
        radial-gradient(900px 600px at 90% 90%, rgba(242,61,78,.08), transparent 55%),
        radial-gradient(1200px 900px at 50% 50%, transparent 55%, rgba(0,0,0,.18) 100%);
    }

    /* Animated bubbles (kept, more subtle) */
    .bubble{
      position:absolute;
      border-radius:50%;
      background: radial-gradient(circle at 30% 30%, rgba(162, 210, 250, 0.26), rgba(162, 210, 250, 0.04));
      animation: float 20s infinite ease-in-out;
      pointer-events:none;
      z-index:3;
    }
    .bubble:nth-child(1){ width:300px; height:300px; top:-150px; left:-150px; animation-delay:0s; }
    .bubble:nth-child(2){ width:200px; height:200px; top:50%; right:-100px; animation-delay:7s; }
    .bubble:nth-child(3){ width:250px; height:250px; bottom:-125px; left:30%; animation-delay:14s; }

    @keyframes float{
      0%,100%{ transform:translate(0,0) scale(1); opacity:.26; }
      33%{ transform:translate(28px,-28px) scale(1.08); opacity:.40; }
      66%{ transform:translate(-28px,28px) scale(.92); opacity:.32; }
    }

    .content-wrapper{
      position:relative;
      z-index:10;
      padding: 64px 24px;
      max-width: 560px;
      margin: 0 auto;
    }

    .logo-container{
      text-align:center;
      margin-bottom: 26px;
      animation: fadeInDown .8s ease-out;
    }

    /* ===== Header scuba icon badge ===== */
    .brand-badge{
      width: 74px;
      height: 74px;
      margin: 0 auto 14px;
      border-radius: 22px;
      background: rgba(255,255,255,.10);
      border: 1px solid rgba(255,255,255,.18);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 14px 40px rgba(0,0,0,.18);
      display:flex;
      align-items:center;
      justify-content:center;
      position:relative;
      overflow:hidden;
    }
    .brand-badge::before{
      content:"";
      position:absolute;
      inset:-2px;
      background: radial-gradient(circle at 30% 30%, rgba(162,210,250,.35), transparent 55%),
                  radial-gradient(circle at 70% 70%, rgba(242,61,78,.16), transparent 60%);
      filter: blur(10px);
      opacity: .9;
    }
    .brand-badge svg{
      position:relative;
      width: 42px;
      height: 42px;
      color: rgba(255,255,255,.95);
      opacity: .95;
    }

    .title{
      color: var(--text);
      font-weight: 800;
      letter-spacing: -0.03em;
      line-height: 1.05;
      font-size: clamp(28px, 4.8vw, 40px);
      text-shadow: 0 10px 30px rgba(0,0,0,.25);
      margin-bottom: 10px;
    }

    .tagline{
      color: var(--text);
      opacity:.92;
      font-weight: 500;
      letter-spacing: .2px;
      line-height: 1.35;
      font-size: clamp(14px, 2.2vw, 18px);
    }

    .links-container{
      display:flex;
      flex-direction:column;
      gap: 14px;
      animation: fadeInUp .8s ease-out .2s both;
    }

    .link-item{
      position:relative;
      overflow:hidden;
      border-radius: var(--radius);
      transition: transform .25s ease;
      will-change: transform;
    }

    .link-item::before{
      content:'';
      position:absolute;
      inset:0;
      background: rgba(255,255,255,.95);
      border: 2px solid rgba(162,210,250,.28);
      border-radius: var(--radius);
      transition: all .25s ease;
      box-shadow: var(--shadow);
    }

    .link-item:hover{ transform: translateY(-4px); }
    .link-item:hover::before{
      background: rgba(255,255,255,1);
      border-color: rgba(162,210,250,.55);
      box-shadow: var(--shadowHover);
    }

    .link-button{
      position:relative;
      width:100%;
      padding: 18px 20px;
      text-decoration:none;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 14px;
      outline:none;
    }

    .link-button:focus-visible{
      border-radius: var(--radius);
      box-shadow: var(--focusRing);
    }

    .link-icon{
      width:24px;
      height:24px;
      flex-shrink:0;
      opacity:.98;
      color: var(--bg);
    }

    .link-text{
      flex:1;
      font-size: calc(var(--base) * 1.10);
      font-weight: 750;
      text-align:left;
      letter-spacing:.2px;
      color: var(--bg);
    }

    .link-arrow{
      width:20px; height:20px; flex-shrink:0;
      transition: transform .25s ease;
      color: var(--bg);
      opacity:.85;
    }

    .link-item:hover .link-arrow{ transform: translateX(4px); }

    /* Booking highlight */
    .booking-link{ margin-top: 18px; transform: scale(1.02); }
    .booking-link::before{
      background: rgba(255,255,255,.98);
      border: 3px solid var(--accent);
      box-shadow: 0 10px 40px rgba(242,61,78,.38);
    }
    .booking-link:hover{ transform: translateY(-6px) scale(1.03); }
    .booking-link:hover::before{ box-shadow: 0 12px 50px rgba(242,61,78,.48); }
    .booking-link .link-button{ padding: 20px 22px; }
    .booking-link .link-icon{ width: 28px; height: 28px; color: var(--accent); }
    .booking-link .link-arrow{ width: 24px; height: 24px; color: var(--accent); }
    .booking-link .link-text{
      font-size: calc(var(--base) * 1.18);
      font-weight: 850;
      color: var(--bg);
    }

    svg{ display:block; }
    .stroke-current{ stroke: currentColor; }

    @keyframes fadeInDown{ from{opacity:0; transform:translateY(-18px);} to{opacity:1; transform:translateY(0);} }
    @keyframes fadeInUp{ from{opacity:0; transform:translateY(18px);} to{opacity:1; transform:translateY(0);} }

    @media (max-width: 640px){
      .content-wrapper{ padding: 44px 18px; }
      .link-button{ padding: 16px 16px; }
      .link-text{ font-size: calc(var(--base) * 1.05); }
      :root{ --patternOpacity:.10; --patternScale: 56px; }
    }

    @media (prefers-reduced-motion: reduce){
      .bubble{ animation: none; }
      .logo-container, .links-container{ animation: none; }
      .link-item, .link-item::before, .link-arrow{ transition: none; }
    }
  </style>

  <style>@view-transition { navigation: auto; }</style>
</head>

<body class="h-full">
  <div class="app-container">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="content-wrapper">
      <div class="logo-container">
        <!-- Scuba-themed badge icon -->


        <h1 class="title" id="main-title">Bali Diving</h1>
        <p class="tagline" id="tagline">Continue Browse Dive Activities Easily!</p>
      </div>

      <div class="links-container" aria-label="Dive activities links">
        <!-- Snorkeling (mask icon) -->
        <div class="link-item">
          <a href="https://balidiving.com/cart/#snorkeling" target="_self" rel="noopener noreferrer" class="link-button">
            <svg class="link-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path class="stroke-current" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M7 7.5h10c1.1 0 2 .9 2 2v2.2c0 2.7-2.2 4.8-4.8 4.8H9.8C7.2 16.5 5 14.4 5 11.7V9.5c0-1.1.9-2 2-2Z"/>
              <path class="stroke-current" stroke-width="2" stroke-linecap="round" d="M9 10.5h2.6M12.4 10.5H15"/>
            </svg>
            <span class="link-text" id="link-1-text">Snorkeling</span>
            <svg class="link-arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path class="stroke-current" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </div>

        <!-- Try Diving (tank icon) -->
        <div class="link-item">
          <a href="https://balidiving.com/cart/#try-diving" target="_self" rel="noopener noreferrer" class="link-button">
            <svg class="link-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path class="stroke-current" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M10 4h4m-4 0v2m4-2v2M9 8.5c0-1.4 1.1-2.5 2.5-2.5h1c1.4 0 2.5 1.1 2.5 2.5V18c0 1.7-1.3 3-3 3h-1c-1.7 0-3-1.3-3-3V8.5Z"/>
              <path class="stroke-current" stroke-width="2" stroke-linecap="round"
                d="M9 11h8M9 15h8"/>
            </svg>
            <span class="link-text" id="link-2-text">Try Diving</span>
            <svg class="link-arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path class="stroke-current" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </div>

        <!-- Fun Diving (fins icon) -->
        <div class="link-item">
          <a href="https://balidiving.com/cart/#fun-diving" target="_self" rel="noopener noreferrer" class="link-button">
            <svg class="link-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path class="stroke-current" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M7 4c3 2 5 6 5 10.5V20H7.5C6.1 20 5 18.9 5 17.5V8c0-1.6.7-3 2-4Z"/>
              <path class="stroke-current" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M17 4c-3 2-5 6-5 10.5V20h4.5c1.4 0 2.5-1.1 2.5-2.5V8c0-1.6-.7-3-2-4Z"/>
            </svg>
            <span class="link-text" id="link-3-text">Fun Diving</span>
            <svg class="link-arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path class="stroke-current" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </div>

        <!-- Learn Diving (compass icon) -->
        <div class="link-item">
          <a href="https://balidiving.com/cart/#learn-diving" target="_self" rel="noopener noreferrer" class="link-button">
            <svg class="link-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle class="stroke-current" cx="12" cy="12" r="9" stroke-width="2"/>
              <path class="stroke-current" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M14.8 9.2l-1.5 4.5-4.5 1.5 1.5-4.5 4.5-1.5Z"/>
              <path class="stroke-current" stroke-width="2" stroke-linecap="round"
                d="M12 3v2M12 19v2M3 12h2M19 12h2"/>
            </svg>
            <span class="link-text" id="link-4-text">Learn Diving</span>
            <svg class="link-arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path class="stroke-current" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </div>

        <!-- Booking (checklist + subtle dive accent) -->
        <div class="link-item booking-link">
          <a href="https://balidiving.com/cart/my-booking" target="_self" rel="noopener noreferrer" class="link-button">
            <svg class="link-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <rect class="stroke-current" x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/>
              <path class="stroke-current" stroke-width="2" d="M9 11l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>
              <path class="stroke-current" stroke-width="2" d="M8 3v4m8-4v4" stroke-linecap="round"/>
            </svg>
            <span class="link-text" id="link-5-text">My Booking Plan</span>
            <svg class="link-arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path class="stroke-current" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const defaultConfig = {
      background_color: "#063c7f",
      background_mid: "#084a9e",
      primary_color: "#a2d2fa",
      text_color: "#ffffff",
      accent_color: "#f23d4e",
      font_family: "Inter",
      font_size: 16,
      main_title: "Bali Diving",
      tagline: "Continue Browse Dive Activities Easily!",
      link_1_text: "Snorkeling",
      link_2_text: "Try Diving",
      link_3_text: "Fun Diving",
      link_4_text: "Learn Diving",
      link_5_text: "My Booking Plan"
    };

    function setCSSVar(key, val){
      document.documentElement.style.setProperty(key, val);
    }

    async function onConfigChange(config) {
      const bg = config.background_color || defaultConfig.background_color;
      const bgMid = config.background_mid || defaultConfig.background_mid;
      const primary = config.primary_color || defaultConfig.primary_color;
      const text = config.text_color || defaultConfig.text_color;
      const accent = config.accent_color || defaultConfig.accent_color;

      setCSSVar('--bg', bg);
      setCSSVar('--bgMid', bgMid);
      setCSSVar('--primary', primary);
      setCSSVar('--text', text);
      setCSSVar('--accent', accent);

      const customFont = config.font_family || defaultConfig.font_family;
      const baseSize = Number(config.font_size || defaultConfig.font_size) || 16;
      setCSSVar('--font', `${customFont}, -apple-system, BlinkMacSystemFont, sans-serif`);
      setCSSVar('--base', `${baseSize}px`);

      const title = document.getElementById('main-title');
      const tagline = document.getElementById('tagline');
      if (title) title.textContent = config.main_title || defaultConfig.main_title;
      if (tagline) tagline.textContent = config.tagline || defaultConfig.tagline;

      const ids = ['link-1-text','link-2-text','link-3-text','link-4-text','link-5-text'];
      ids.forEach((id, i) => {
        const el = document.getElementById(id);
        const key = `link_${i+1}_text`;
        if (el) el.textContent = config[key] || defaultConfig[key];
      });
    }

    if (window.elementSdk) {
      window.elementSdk.init({
        defaultConfig,
        onConfigChange,
        mapToCapabilities: (config) => ({
          recolorables: [
            { get: () => config.background_color || defaultConfig.background_color,
              set: (value) => { config.background_color = value; window.elementSdk.setConfig({ background_color: value }); } },
            { get: () => config.background_mid || defaultConfig.background_mid,
              set: (value) => { config.background_mid = value; window.elementSdk.setConfig({ background_mid: value }); } },
            { get: () => config.primary_color || defaultConfig.primary_color,
              set: (value) => { config.primary_color = value; window.elementSdk.setConfig({ primary_color: value }); } },
            { get: () => config.text_color || defaultConfig.text_color,
              set: (value) => { config.text_color = value; window.elementSdk.setConfig({ text_color: value }); } },
            { get: () => config.accent_color || defaultConfig.accent_color,
              set: (value) => { config.accent_color = value; window.elementSdk.setConfig({ accent_color: value }); } }
          ],
          borderables: [],
          fontEditable: {
            get: () => config.font_family || defaultConfig.font_family,
            set: (value) => { config.font_family = value; window.elementSdk.setConfig({ font_family: value }); }
          },
          fontSizeable: {
            get: () => config.font_size || defaultConfig.font_size,
            set: (value) => { config.font_size = value; window.elementSdk.setConfig({ font_size: value }); }
          }
        }),
        mapToEditPanelValues: (config) => new Map([
          ["main_title", config.main_title || defaultConfig.main_title],
          ["tagline", config.tagline || defaultConfig.tagline],
          ["link_1_text", config.link_1_text || defaultConfig.link_1_text],
          ["link_2_text", config.link_2_text || defaultConfig.link_2_text],
          ["link_3_text", config.link_3_text || defaultConfig.link_3_text],
          ["link_4_text", config.link_4_text || defaultConfig.link_4_text],
          ["link_5_text", config.link_5_text || defaultConfig.link_5_text]
        ])
      });
    } else {
      onConfigChange(defaultConfig);
    }
  </script>
</body>
</html>
