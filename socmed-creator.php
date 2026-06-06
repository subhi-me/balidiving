<!doctype html>
<html lang="en" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BaliDiving | Story Editor</title>
  
  <!-- Google Fonts: Playfair Display (Elegant) & Inter (Clean) -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Tailwind Configuration for Custom Colors -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            deep: '#051937',
            ocean: '#004d7a',
            teal: '#008793',
            sand: '#F4A896',
            gold: '#FFD89B',
            rust: '#8B4513',
            night: '#020b1a',
            lagoon: '#40E0D0'
          },
          fontFamily: {
            serif: ['"Playfair Display"', 'serif'],
            sans: ['"Inter"', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <style>
    /* Custom Styles */
    body {
      background-color: #051937;
      color: white;
    }

    .glass-panel {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    .glass-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }
    .glass-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.5);
      outline: none;
    }

    /* Phone Preview Container */
    .canvas-phone {
      width: 480px;
      height: 720px;
      background: #000;
      border-radius: 0;
      overflow: hidden;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      border: 0;
      position: relative;
      flex-shrink: 0;
    }

    /* Background Animation (Ocean) */
    .bg-video-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      overflow: hidden;
    }
    
    /* --- NEW CINEMATIC LOOP ANIMATIONS (7-15s) --- */

    /* 1. Cinematic (Slow Zoom & Drift) - 15s */
    @keyframes cinematicBg {
      0% { transform: scale(1) translate(0, 0); }
      50% { transform: scale(1.15) translate(-2%, -1%); }
      100% { transform: scale(1) translate(0, 0); }
    }
    @keyframes cinematicText {
      0% { transform: translateY(0); opacity: 0.9; }
      50% { transform: translateY(-5px); opacity: 1; }
      100% { transform: translateY(0); opacity: 0.9; }
    }

    /* 2. Drift (Horizontal Pan) - 12s */
    @keyframes driftBg {
      0% { transform: scale(1.1) translateX(-3%); }
      100% { transform: scale(1.1) translateX(3%); }
    }
    @keyframes driftText {
      0% { transform: translateX(0); letter-spacing: normal; }
      50% { transform: translateX(3px); letter-spacing: 0.5px; }
      100% { transform: translateX(0); letter-spacing: normal; }
    }

    /* 3. Deep (Breathing/Pulse) - 10s */
    @keyframes deepBg {
      0% { transform: scale(1); filter: brightness(1); }
      50% { transform: scale(1.08); filter: brightness(1.1); }
      100% { transform: scale(1); filter: brightness(1); }
    }
    @keyframes deepText {
      0% { transform: scale(1); text-shadow: 0 4px 10px rgba(0,0,0,0.3); }
      50% { transform: scale(1.05); text-shadow: 0 8px 20px rgba(0,0,0,0.5); }
      100% { transform: scale(1); text-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    }

    /* 4. Current (Vertical Flow) - 8s */
    @keyframes currentBg {
      0% { transform: scale(1.1) translateY(0); }
      50% { transform: scale(1.1) translateY(-2%); }
      100% { transform: scale(1.1) translateY(0); }
    }
    @keyframes currentText {
      0% { transform: translateY(2px); }
      50% { transform: translateY(-2px); }
      100% { transform: translateY(2px); }
    }

    /* --- ADVANCED NATURAL EFFECTS --- */

    /* 1. CAUSTICS (Water Light Reflections) */
    @keyframes causticsFlow {
      0% { transform: scale(1.2) translateY(0) rotate(0deg); opacity: 0.3; }
      50% { transform: scale(1.3) translateY(-10px) rotate(2deg); opacity: 0.5; }
      100% { transform: scale(1.2) translateY(0) rotate(0deg); opacity: 0.3; }
    }
    
    .effect-caustics {
      background: 
        radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 0%, transparent 20%),
        repeating-linear-gradient(45deg, rgba(255,255,255,0.05) 0px, transparent 2px, transparent 10px);
      mix-blend-mode: overlay;
      filter: blur(3px);
      animation: causticsFlow 8s ease-in-out infinite;
    }

    /* 2. GOD RAYS (Sunbeams) */
    @keyframes rayRotate {
      0% { transform: rotate(-15deg) translateX(-10%); opacity: 0.4; }
      50% { transform: rotate(-10deg) translateX(0%); opacity: 0.6; }
      100% { transform: rotate(-15deg) translateX(-10%); opacity: 0.4; }
    }

    .effect-rays {
      background: conic-gradient(from 225deg at 0% 0%, rgba(255,255,255,0.4) 0deg, transparent 40deg, transparent 360deg);
      mix-blend-mode: overlay;
      filter: blur(8px);
      transform-origin: top left;
      animation: rayRotate 12s ease-in-out infinite;
    }

    /* 3. MARINE SNOW (Dust) */
    @keyframes floatingDust {
      0% { background-position: 0 0, 0 0; }
      100% { background-position: 0 -100px, 50px -150px; }
    }
    .effect-dust {
      background-image: 
        radial-gradient(rgba(255, 255, 255, 0.5) 1px, transparent 1px),
        radial-gradient(rgba(255, 255, 255, 0.3) 1px, transparent 1px);
      background-size: 150px 150px, 100px 100px;
      animation: floatingDust 25s linear infinite;
      mix-blend-mode: screen;
      opacity: 0.5;
    }

    /* 4. REALISTIC BUBBLES */
    @keyframes bubbleRise {
      0% { transform: translateY(120%) translateX(0); opacity: 0; }
      10% { opacity: 0.6; }
      90% { opacity: 0.6; }
      100% { transform: translateY(-20%) translateX(20px); opacity: 0; }
    }
    .bubble {
      position: absolute;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.8), rgba(255,255,255,0.1));
      border-radius: 50%;
      border: 1px solid rgba(255,255,255,0.3);
      bottom: -20px;
    }
    .b1 { left: 20%; width: 6px; height: 6px; animation: bubbleRise 8s linear infinite; animation-delay: 0s; }
    .b2 { left: 50%; width: 4px; height: 4px; animation: bubbleRise 12s linear infinite; animation-delay: 2s; }
    .b3 { left: 70%; width: 8px; height: 8px; animation: bubbleRise 7s linear infinite; animation-delay: 4s; }
    .b4 { left: 35%; width: 5px; height: 5px; animation: bubbleRise 10s linear infinite; animation-delay: 6s; }

    /* 5. FILM GRAIN */
    .effect-grain {
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.15'/%3E%3C/svg%3E");
      opacity: 0.25;
      mix-blend-mode: overlay;
    }

    /* 6. VIGNETTE & BLUR */
    .effect-lens {
      background: radial-gradient(circle at center, transparent 50%, rgba(0,0,0,0.6) 100%);
      box-shadow: inset 0 0 40px rgba(0,0,0,0.2);
    }
    
    /* 7. CHROMATIC ABERRATION (Text Effect) */
    .chromatic-text {
      text-shadow: 
        2px 0px 1px rgba(255,0,0,0.1),
        -2px 0px 1px rgba(0,0,255,0.1),
        0 4px 15px rgba(0,0,0,0.4);
    }

    /* Utility Classes for JS */
    .motion-cinematic-bg { animation: cinematicBg 15s ease-in-out infinite; }
    .motion-cinematic-text { animation: cinematicText 15s ease-in-out infinite; }
    
    .motion-drift-bg { animation: driftBg 12s ease-in-out infinite alternate; }
    .motion-drift-text { animation: driftText 12s ease-in-out infinite; }

    .motion-deep-bg { animation: deepBg 10s ease-in-out infinite; }
    .motion-deep-text { animation: deepText 10s ease-in-out infinite; }

    .motion-current-bg { animation: currentBg 8s ease-in-out infinite; }
    .motion-current-text { animation: currentText 8s ease-in-out infinite; }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #051937; }
    ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
  </style>
</head>

<body class="h-full flex flex-col font-sans overflow-x-hidden selection:bg-teal selection:text-white">

  <!-- Background SVG Animation -->
  <div class="bg-video-container pointer-events-none">
    <svg class="w-full h-full object-cover opacity-60" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice">
      <defs>
        <linearGradient id="waterGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#051937" />
          <stop offset="100%" stop-color="#004d7a" />
        </linearGradient>
      </defs>
      <rect width="100%" height="100%" fill="url(#waterGrad)" />
      <!-- Animated Bubbles -->
      <g fill="rgba(255,255,255,0.1)">
        <circle cx="10%" cy="110%" r="10"><animate attributeName="cy" to="-10%" dur="20s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;0.5;0" dur="20s" repeatCount="indefinite"/></circle>
        <circle cx="20%" cy="110%" r="5"><animate attributeName="cy" to="-10%" dur="15s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;0.3;0" dur="15s" repeatCount="indefinite"/></circle>
        <circle cx="50%" cy="110%" r="8"><animate attributeName="cy" to="-10%" dur="25s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;0.4;0" dur="25s" repeatCount="indefinite"/></circle>
        <circle cx="80%" cy="110%" r="12"><animate attributeName="cy" to="-10%" dur="18s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;0.2;0" dur="18s" repeatCount="indefinite"/></circle>
      </g>
    </svg>
  </div>

  <!-- Header -->
  <nav class="relative z-10 w-full px-6 py-4 flex justify-between items-center border-b border-white/10 glass-panel">
    <div class="flex items-center gap-2">
      <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-teal to-blue-500"></div>
      <span class="font-serif text-xl tracking-wide font-semibold">BaliDiving Story.</span>
    </div>
    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-full text-sm font-medium transition-all backdrop-blur-md border border-white/20">
      Pro Version
    </button>
  </nav>

  <!-- Main Workspace -->
  <main class="relative z-10 flex-1 flex flex-col lg:flex-row min-h-0 overflow-hidden">
    
    <!-- LEFT PANEL: CONTROLS -->
    <aside class="w-full lg:w-1/4 p-6 overflow-y-auto border-r border-white/10 glass-panel lg:h-full z-20">
      <h2 class="font-serif text-2xl mb-6 text-sand">Editor Controls</h2>
      
      <div class="space-y-6">
        <!-- Title Input -->
        <div class="space-y-2">
          <label class="text-xs uppercase tracking-wider text-white/60 font-semibold">Story Title</label>
          <input type="text" id="story-title" value="Into the Deep" 
            class="w-full glass-input rounded-lg px-4 py-3 text-white placeholder-white/40 font-serif text-lg">
        </div>

        <!-- Subtitle Input -->
        <div class="space-y-2">
          <label class="text-xs uppercase tracking-wider text-white/60 font-semibold">Subtitle</label>
          <input type="text" id="story-subtitle" value="Scuba Life" 
            class="w-full glass-input rounded-lg px-4 py-3 text-white placeholder-white/40 font-sans text-sm tracking-wide">
        </div>

        <!-- Image Upload -->
        <div class="space-y-2">
           <label class="text-xs uppercase tracking-wider text-white/60 font-semibold">Background Image</label>
           <label class="flex items-center justify-center w-full h-24 border-2 border-dashed border-white/20 rounded-lg cursor-pointer hover:bg-white/5 transition-colors">
              <div class="text-center">
                 <p class="text-sm text-white/70">Upload Photo</p>
                 <p class="text-xs text-white/40 mt-1">JPG or PNG</p>
              </div>
              <input type="file" id="bg-upload" class="hidden" accept="image/*">
           </label>
           <button id="clear-bg" class="text-xs text-red-300 hover:text-red-400 underline w-full text-right hidden">Remove Image</button>
        </div>

        <!-- Font Size Slider -->
        <div class="space-y-2">
          <div class="flex justify-between">
             <label class="text-xs uppercase tracking-wider text-white/60 font-semibold">Size</label>
             <span id="size-val" class="text-xs text-white/60">48px</span>
          </div>
          <input type="range" id="font-size" min="24" max="96" value="48" class="w-full accent-teal h-1 bg-white/20 rounded-lg appearance-none cursor-pointer">
        </div>

        <!-- Color Palette -->
        <div class="space-y-2">
          <label class="text-xs uppercase tracking-wider text-white/60 font-semibold">Color</label>
          <div class="grid grid-cols-5 gap-2">
            <button class="color-btn w-full aspect-square rounded-full border-2 border-white/80 transform hover:scale-110 transition-transform" style="background: #FFFFFF" data-color="#FFFFFF"></button>
            <button class="color-btn w-full aspect-square rounded-full border-2 border-transparent hover:border-white/50 transform hover:scale-110 transition-transform" style="background: #F4A896" data-color="#F4A896"></button>
            <button class="color-btn w-full aspect-square rounded-full border-2 border-transparent hover:border-white/50 transform hover:scale-110 transition-transform" style="background: #FFD89B" data-color="#FFD89B"></button>
            <button class="color-btn w-full aspect-square rounded-full border-2 border-transparent hover:border-white/50 transform hover:scale-110 transition-transform" style="background: #008793" data-color="#008793"></button>
            <button class="color-btn w-full aspect-square rounded-full border-2 border-transparent hover:border-white/50 transform hover:scale-110 transition-transform" style="background: #051937" data-color="#051937"></button>
          </div>
        </div>

        <!-- Animation Toggles (UPDATED) -->
        <div class="space-y-2">
          <label class="text-xs uppercase tracking-wider text-white/60 font-semibold">Motion Preset (Loop)</label>
          <div class="grid grid-cols-2 gap-2">
            <button class="anim-btn active-anim px-3 py-2 rounded-md border border-teal bg-teal/20 text-xs text-white" data-anim="cinematic">Cinematic (15s)</button>
            <button class="anim-btn px-3 py-2 rounded-md border border-white/10 hover:bg-white/10 text-xs text-white/70" data-anim="drift">Drift (12s)</button>
            <button class="anim-btn px-3 py-2 rounded-md border border-white/10 hover:bg-white/10 text-xs text-white/70" data-anim="deep">Deep (10s)</button>
            <button class="anim-btn px-3 py-2 rounded-md border border-white/10 hover:bg-white/10 text-xs text-white/70" data-anim="current">Current (8s)</button>
          </div>
        </div>
      </div>
    </aside>

    <!-- CENTER PANEL: PREVIEW -->
    <section class="flex-1 flex items-center justify-center p-8 bg-black/20 lg:h-full overflow-y-auto">
      <div class="canvas-phone relative shadow-2xl transition-all duration-300">
        
        <!-- Dynamic Background (With Motion Class) -->
        <div id="preview-bg" class="absolute inset-0 bg-cover bg-center transition-all duration-500 origin-center z-0" 
             style="background-image: linear-gradient(135deg, #051937 0%, #004d7a 100%);">
        </div>

        <!-- ADVANCED NATURAL OVERLAYS -->
        <div class="absolute inset-0 z-1 pointer-events-none overflow-hidden">
           
           <!-- 1. Caustics (Water Light Reflections) -->
           <div class="effect-caustics absolute inset-0"></div>

           <!-- 2. God Rays (Sunbeams from top left) -->
           <div class="effect-rays absolute inset-0"></div>

           <!-- 3. Rising Bubbles (Physical elements) -->
           <div class="bubble b1"></div>
           <div class="bubble b2"></div>
           <div class="bubble b3"></div>
           <div class="bubble b4"></div>

           <!-- 4. Marine Snow / Dust -->
           <div class="effect-dust absolute inset-0"></div>

           <!-- 5. Noise / Grain -->
           <div class="effect-grain absolute inset-0"></div>

           <!-- 6. Vignette & Lens Blur Edges -->
           <div class="effect-lens absolute inset-0"></div>
        </div>
        
        <!-- Content Area -->
        <div class="relative w-full h-full flex flex-col items-center justify-center p-6 text-center z-10 pointer-events-none">
          <h1 id="preview-text" class="chromatic-text font-serif font-bold leading-tight drop-shadow-lg" 
              style="font-size: 48px; color: #FFFFFF;">
            Into the Deep
          </h1>
          <p id="preview-subtitle" class="font-sans text-xs text-white/80 mt-4 uppercase tracking-[0.2em]">
            Scuba Life
          </p>
        </div>

        <!-- FOOTER IN PREVIEW -->
        <div class="absolute bottom-10 left-0 w-full text-center z-20 pointer-events-none">
           <p class="text-white/70 text-[10px] font-sans tracking-[0.15em] uppercase font-medium shadow-sm">
             BaliDiving.com <span class="mx-1 text-teal">|</span> @bali_diving
           </p>
        </div>
      </div>
    </section>

    <!-- RIGHT PANEL: PRESETS -->
    <aside class="w-full lg:w-1/5 p-6 border-l border-white/10 glass-panel lg:h-full overflow-y-auto z-20">
      <h3 class="font-serif text-xl mb-6 text-white">Presets</h3>
      
      <div class="space-y-6">
        <!-- Scuba Category -->
        <div>
          <h4 class="text-xs uppercase text-white/50 mb-3 font-semibold">Scuba Diving</h4>
          <div class="grid gap-3">
             <!-- Default -->
             <div class="preset-card cursor-pointer group" onclick="applyPreset('deep')">
                <div class="h-16 rounded-lg bg-gradient-to-br from-deep to-ocean mb-2 border border-white/10 group-hover:border-teal transition-colors relative overflow-hidden"></div>
                <p class="text-sm font-medium">Deep Ocean</p>
             </div>
             <!-- Coral -->
             <div class="preset-card cursor-pointer group" onclick="applyPreset('coral')">
                <div class="h-16 rounded-lg bg-gradient-to-br from-teal to-blue-400 mb-2 border border-white/10 group-hover:border-teal transition-colors"></div>
                <p class="text-sm font-medium">Coral Reef</p>
             </div>
             <!-- NEW: Night Dive -->
             <div class="preset-card cursor-pointer group" onclick="applyPreset('night')">
                <div class="h-16 rounded-lg bg-gradient-to-br from-black to-blue-900 mb-2 border border-white/10 group-hover:border-teal transition-colors"></div>
                <p class="text-sm font-medium">Night Dive</p>
             </div>
             <!-- NEW: Shipwreck -->
             <div class="preset-card cursor-pointer group" onclick="applyPreset('wreck')">
                <div class="h-16 rounded-lg bg-gradient-to-br from-gray-800 to-rust mb-2 border border-white/10 group-hover:border-teal transition-colors"></div>
                <p class="text-sm font-medium">Shipwreck</p>
             </div>
          </div>
        </div>

        <!-- Snorkeling Category -->
        <div>
          <h4 class="text-xs uppercase text-white/50 mb-3 font-semibold">Snorkeling</h4>
          <div class="grid gap-3">
             <!-- Golden Hour -->
             <div class="preset-card cursor-pointer group" onclick="applyPreset('sunset')">
                <div class="h-16 rounded-lg bg-gradient-to-br from-sand to-gold mb-2 border border-white/10 group-hover:border-teal transition-colors"></div>
                <p class="text-sm font-medium">Golden Hour</p>
             </div>
             <!-- Clear Water -->
             <div class="preset-card cursor-pointer group" onclick="applyPreset('clear')">
                <div class="h-16 rounded-lg bg-gradient-to-br from-cyan-300 to-blue-200 mb-2 border border-white/10 group-hover:border-teal transition-colors"></div>
                <p class="text-sm font-medium">Clear Water</p>
             </div>
             <!-- NEW: Blue Lagoon -->
             <div class="preset-card cursor-pointer group" onclick="applyPreset('lagoon')">
                <div class="h-16 rounded-lg bg-gradient-to-br from-blue-400 to-lagoon mb-2 border border-white/10 group-hover:border-teal transition-colors"></div>
                <p class="text-sm font-medium">Blue Lagoon</p>
             </div>
             <!-- NEW: Turtle Bay -->
             <div class="preset-card cursor-pointer group" onclick="applyPreset('turtle')">
                <div class="h-16 rounded-lg bg-gradient-to-br from-emerald-600 to-teal mb-2 border border-white/10 group-hover:border-teal transition-colors"></div>
                <p class="text-sm font-medium">Turtle Bay</p>
             </div>
          </div>
        </div>
      </div>
    </aside>
  </main>

  <script>
    // State
    const state = {
      text: "Into the Deep",
      subtitle: "Scuba Life",
      color: "#FFFFFF",
      size: 48,
      animation: "cinematic", // Default to cinematic
      bgImage: null
    };

    // Elements
    const previewText = document.getElementById('preview-text');
    const previewSubtitle = document.getElementById('preview-subtitle');
    const previewBg = document.getElementById('preview-bg');
    
    const titleInput = document.getElementById('story-title');
    const subtitleInput = document.getElementById('story-subtitle');
    const sizeInput = document.getElementById('font-size');
    const bgUpload = document.getElementById('bg-upload');
    const clearBgBtn = document.getElementById('clear-bg');

    // --- Core Functions ---

    function updatePreview() {
      // Update Text
      previewText.textContent = state.text;
      previewSubtitle.textContent = state.subtitle;
      
      // Update Style
      previewText.style.color = state.color;
      previewText.style.fontSize = `${state.size}px`;
      
      // Re-trigger Animation
      triggerAnimation(state.animation);
    }

    function triggerAnimation(animName) {
      // 1. Remove ALL existing animation classes from BG and Text
      const animClasses = [
        'motion-cinematic-bg', 'motion-cinematic-text',
        'motion-drift-bg', 'motion-drift-text',
        'motion-deep-bg', 'motion-deep-text',
        'motion-current-bg', 'motion-current-text'
      ];
      
      previewBg.classList.remove(...animClasses);
      previewText.classList.remove(...animClasses);
      previewSubtitle.classList.remove(...animClasses);

      // Force reflow to ensure animation restarts cleanly
      void previewBg.offsetWidth;
      void previewText.offsetWidth;
      
      // 2. Add New Classes based on animName
      previewBg.classList.add(`motion-${animName}-bg`);
      previewText.classList.add(`motion-${animName}-text`);
      
      // Subtitle uses same text animation for consistency, but maybe we can delay it later
      previewSubtitle.classList.add(`motion-${animName}-text`);
    }

    // --- Event Listeners ---

    // 1. Title Input
    titleInput.addEventListener('input', (e) => {
      state.text = e.target.value || " ";
      previewText.textContent = state.text;
    });

    // 2. Subtitle Input
    subtitleInput.addEventListener('input', (e) => {
      state.subtitle = e.target.value || " ";
      previewSubtitle.textContent = state.subtitle;
    });

    // 3. Font Size
    sizeInput.addEventListener('input', (e) => {
      state.size = e.target.value;
      document.getElementById('size-val').textContent = `${state.size}px`;
      previewText.style.fontSize = `${state.size}px`;
    });

    // 4. Color Picker
    document.querySelectorAll('.color-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        document.querySelectorAll('.color-btn').forEach(b => b.classList.replace('border-white/80', 'border-transparent'));
        e.target.classList.replace('border-transparent', 'border-white/80');
        
        state.color = e.target.dataset.color;
        previewText.style.color = state.color;
      });
    });

    // 5. Animation Switcher (UPDATED LOGIC)
    document.querySelectorAll('.anim-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        // UI Update
        document.querySelectorAll('.anim-btn').forEach(b => {
           b.classList.remove('bg-teal/20', 'border-teal', 'text-white');
           b.classList.add('border-white/10', 'text-white/70');
        });
        e.target.classList.remove('border-white/10', 'text-white/70');
        e.target.classList.add('bg-teal/20', 'border-teal', 'text-white');

        // Logic Update
        state.animation = e.target.dataset.anim;
        triggerAnimation(state.animation);
      });
    });

    // 6. Image Upload
    bgUpload.addEventListener('change', (e) => {
       const file = e.target.files[0];
       if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
             previewBg.style.backgroundImage = `url(${e.target.result})`;
             clearBgBtn.classList.remove('hidden');
          }
          reader.readAsDataURL(file);
       }
    });

    // 7. Clear Background
    clearBgBtn.addEventListener('click', () => {
       previewBg.style.backgroundImage = 'linear-gradient(135deg, #051937 0%, #004d7a 100%)';
       bgUpload.value = '';
       clearBgBtn.classList.add('hidden');
    });

    // --- Presets Logic (Updated with new motions) ---
    window.applyPreset = function(type) {
       switch(type) {
          case 'deep':
             previewBg.style.backgroundImage = 'linear-gradient(135deg, #051937 0%, #004d7a 100%)';
             updateControls('Into the Deep', 'Scuba Life', '#FFFFFF', 'cinematic');
             break;
          case 'coral':
             previewBg.style.backgroundImage = 'linear-gradient(135deg, #008793 0%, #7DD3C0 100%)';
             updateControls('Coral Paradise', 'Reef Explorer', '#FFFFFF', 'drift');
             break;
          case 'night': 
             previewBg.style.backgroundImage = 'linear-gradient(180deg, #000000 0%, #051937 100%)';
             updateControls('Night Dive', 'Mystery of the Deep', '#F4A896', 'deep');
             break;
          case 'wreck': 
             previewBg.style.backgroundImage = 'linear-gradient(135deg, #2c3e50 0%, #8B4513 100%)';
             updateControls('Shipwreck', 'History Below', '#FFD89B', 'cinematic');
             break;
          case 'sunset':
             previewBg.style.backgroundImage = 'linear-gradient(135deg, #F4A896 0%, #FFD89B 100%)';
             updateControls('Sunset Dive', 'Golden Moments', '#051937', 'deep');
             break;
          case 'clear':
             previewBg.style.backgroundImage = 'linear-gradient(180deg, #67e8f9 0%, #06b6d4 100%)';
             updateControls('Crystal Clear', 'Just Keep Swimming', '#051937', 'drift');
             break;
          case 'lagoon': 
             previewBg.style.backgroundImage = 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
             updateControls('Blue Lagoon', 'Island Vibes', '#051937', 'current');
             break;
          case 'turtle': 
             previewBg.style.backgroundImage = 'linear-gradient(135deg, #134E5E 0%, #71B280 100%)';
             updateControls('Turtle Bay', 'Ocean Friends', '#FFFFFF', 'drift');
             break;
       }
       // Reset image upload when using preset
       clearBgBtn.classList.add('hidden');
    }

    function updateControls(text, subtitle, color, anim) {
       state.text = text;
       state.subtitle = subtitle;
       state.color = color;
       state.animation = anim;

       // Update UI Elements
       titleInput.value = text;
       subtitleInput.value = subtitle;
       
       // Update Motion Buttons UI
       document.querySelectorAll('.anim-btn').forEach(b => {
          if (b.dataset.anim === anim) {
             b.classList.remove('border-white/10', 'text-white/70');
             b.classList.add('bg-teal/20', 'border-teal', 'text-white');
          } else {
             b.classList.remove('bg-teal/20', 'border-teal', 'text-white');
             b.classList.add('border-white/10', 'text-white/70');
          }
       });

       // Trigger logic
       updatePreview();
    }

    // Initial Trigger
    triggerAnimation('cinematic');

  </script>
</body>
</html>