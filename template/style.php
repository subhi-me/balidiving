<!-- Tailwind CDN -->
<script  rel="preload" src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary: '#3552c8',
          secondary: '#f23d4e',
          accent: '#0070d3',
          teal: '#23a0b4',
          gold: '#eebe35',
          lightblue: '#a2d2fa',
          navy: '#063c7f'
        }
      }
    }
  }
</script>

<!-- Preconnects: kurangi latency font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="icon" href="https://balidiving.com/favicon.png" type="image/png">
<link rel="icon" sizes="192x192" href="https://balidiving.com/bd192x192.png">
<link rel="icon" href="https://balidiving.com/bali-diving-logo.svg" type="image/svg+xml">
<link rel="shortcut icon" href="https://balidiving.com/iconbd.png" type="image/png">
<link rel="apple-touch-icon" href="https://balidiving.com/bd192x192.png">
<!-- Google Fonts: preload stylesheet non-blocking -->
<link rel="preload"
      href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'">
<noscript>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap">
</noscript>

<!-- Font Awesome: preload stylesheet non-blocking -->
<link rel="preload"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'"
      crossorigin>
<noscript>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin>
</noscript>

<!-- Preload WOFF2 lokal kamu -->
<link rel="preload" href="../template/fonts/sui-generis.woff2" as="font" type="font/woff2" crossorigin>

<style>
/* 💎 Font Variables */
:root{
  --font-title: 'Sui Generis', 'Noto Sans', system-ui, sans-serif;
  --font-base: 'Noto Sans', system-ui, sans-serif;
}

/* 🧱 @font-face lokal dengan swap + metric overrides untuk cegah CLS */
@font-face{
  font-family: 'Sui Generis';
  src: local('Sui Generis'),
       url('/../template/fonts/sui-generis.woff2') format('woff2'),
       url('../template/fonts/sui-generis.woff') format('woff');
  font-weight: 300;
  font-style: normal;
  font-display: swap;
  /* Optional metric overrides (tweak sesuai hasil Lighthouse) */
  ascent-override: 92%;
  descent-override: 22%;
  line-gap-override: 0%;
  size-adjust: 102%;
}



/* 🌍 Global Reset & Layout */
html, body {
  overflow-x: hidden;
  margin: 0;
  padding: 0;
  width: 100%;
  font-family: var(--font-base);
  background-color: #fff;
  color: #111827;
}

* {
  box-sizing: border-box;
  max-width: 100vw;
}

/* 🏷️ Typography System */
h1, h2,h3 {
  font-family: var(--font-title);
  letter-spacing: 0.5px;
  line-height: 1.2;
  color: #0f172a;
  margin-bottom: 0.5em;
}
h1 {
  font-size: clamp(1.8rem, 4vw, 2.8rem);
  font-weight: 700;
}
h2 {
  font-size: clamp(1.4rem, 3vw, 2rem);
  font-weight: 600;
}
h3 {
  font-size: clamp(1.1rem, 2.5vw, 1.5rem);
  font-weight: 500;
}

p, span, a, li, label, input, textarea, button, small {
  font-family: var(--font-base);
  font-size: 1rem;
  line-height: 1.6;
}

/* ✨ Animations */
@keyframes slideIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.chat-bubble { animation: slideIn 0.3s ease-out; }

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
.typing-indicator { animation: pulse 1.5s infinite; }

.button-hover:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* 🌅 Hero Background */
.hero-background-image {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 1.5s ease-in-out;
}
.hero-background-image.active { opacity: 1; }
.hero-text-container { position: relative; z-index: 20; }

/* 🌊 Wave Animation */
.wave-container {
  position: absolute;
  bottom: 0; left: 0;
  width: 100%;
  z-index: 10;
  overflow: hidden;
}
.wave-svg {
  position: relative;
  display: block;
  width: 100%;
  height: auto;
  min-height: 100px;
}
.wave-svg .wave-path-1 { animation: subtleWave 8s infinite ease-in-out; }
.wave-svg .wave-path-2 {
  animation: subtleWave 10s infinite ease-in-out;
  opacity: 0.8;
}
@keyframes subtleWave {
  0% { transform: translateY(0); }
  50% { transform: translateY(10px); }
  100% { transform: translateY(0); }
}

/* 📱 Mobile Optimization */
@media (max-width: 768px) {
  #mobile-menu {
    max-height: 80vh;
    overflow-y: auto;
    padding-bottom: 0.5rem;
  }
  #mobile-menu a {
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    line-height: 1.4;
  }
  nav .text-2xl { font-size: 1.25rem; }
  nav img { width: 55%; }
  .chat-launcher {
    width: 38px;
    height: 38px;
    font-size: 0.9rem;
    color:white;
  }
}

/* 📱 Typography Responsive */
@media (max-width: 480px) {
  body { font-size: 0.95rem; line-height: 1.4; }
  h1 { font-size: 1.8rem; }
  h2 { font-size: 1.4rem; }
  h3 { font-size: 1.1rem; }

  nav .text-2xl { font-size: 1.1rem; }
  nav img { width: 45%; }

  .hero-text-container {
    padding: 1rem;
    text-align: center;
  }
  .wave-svg { min-height: 80px; }
}

/* 💻 Desktop Enhancement */
@media (min-width: 1024px) {
  .hero-text-container h1 { font-size: 3rem; }
  nav img { width: 70%; }
  nav .text-2xl { font-size: 1.75rem; }
}

/* 💬 Chat widget always on top */
.chat-widget,
.chatbox,
.chat-launcher,
 {
  position: fixed !important;
  z-index: 9999999999999999999999 !important;
}
/* 🌟 Perbesar Logo & Teks Navbar khusus versi mobile */
@media (max-width: 768px) {
  nav img {
    width: 70% !important;        /* sebelumnya 45–55%, sekarang lebih dominan */
    max-width: 80px !important;   /* batasi biar tidak pecah */
  }

  nav .text-2xl {
    font-size: 1.6rem !important; /* lebih besar tapi tetap elegan */
    font-weight: 700;
    letter-spacing: 0.5px;
  }

  /* pastikan konten navbar tetap rapi secara vertikal */
  #nav-content {
    height: 68px !important;
    align-items: center !important;
  }
}
/* 💬 Chat Launcher Styling */
.chat-launcher {
  color: #f1f5f9;

}

.chat-launcher:hover {
  transform: scale(1.1);
}


/* Pastikan elemen utama tidak melewati lebar viewport */
* {
  box-sizing: border-box;
  max-width: 100vw;

}

</style>
