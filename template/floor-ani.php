<!-- Realistic Reef + Clownfish + Natural Seabed (transparent, no text) -->
<section id="reef-realistic" aria-hidden="true">
  <svg width="0" height="0" style="position:absolute;">
    <defs>
      <!-- Caustics + light rays -->
      <filter id="caustics" x="-20%" y="-20%" width="140%" height="140%">
        <feTurbulence type="fractalNoise" baseFrequency="0.008 0.02" numOctaves="2" seed="7" result="n"/>
        <feDisplacementMap in="SourceGraphic" in2="n" scale="6" xChannelSelector="R" yChannelSelector="G"/>
      </filter>
      <linearGradient id="rayGrad" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="white" stop-opacity="0.35"/>
        <stop offset="60%" stop-color="white" stop-opacity="0.08"/>
        <stop offset="100%" stop-color="white" stop-opacity="0"/>
      </linearGradient>
      <mask id="raysMask"><rect width="100%" height="100%" fill="url(#rayGrad)"/></mask>

      <!-- Fish symbols (A,B as before) -->
      <symbol id="fishA" viewBox="0 0 160 70">
        <defs><linearGradient id="fishBlue" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stop-color="#7ac7ff"/><stop offset="60%" stop-color="#2b8ad6"/><stop offset="100%" stop-color="#11649f"/>
        </linearGradient></defs>
        <ellipse cx="62" cy="35" rx="46" ry="24" fill="url(#fishBlue)"/>
        <polygon class="tail" points="106,35 158,14 158,56" fill="#1e82c1"/>
        <circle cx="34" cy="26" r="3" fill="white"/>
      </symbol>
      <symbol id="fishB" viewBox="0 0 150 64">
        <defs><linearGradient id="fishTeal" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stop-color="#a1f0e5"/><stop offset="70%" stop-color="#27b3a9"/><stop offset="100%" stop-color="#1a7f78"/>
        </linearGradient></defs>
        <ellipse cx="60" cy="32" rx="44" ry="22" fill="url(#fishTeal)"/>
        <polygon class="tail" points="98,32 148,12 148,52" fill="#1f9b91"/>
        <circle cx="30" cy="26" r="3" fill="white"/>
      </symbol>

      <!-- NEW: Clownfish (orange/white/black) -->
      <symbol id="clownfish" viewBox="0 0 140 60">
        <!-- body base -->
        <ellipse cx="60" cy="30" rx="42" ry="20" fill="#ff7a00" stroke="#1a1a1a" stroke-width="3" />
        <!-- white bands -->
        <g fill="#ffffff" stroke="#1a1a1a" stroke-width="3">
          <path d="M40,12 q8,10 8,18 q0,8 -8,18 q-10,0 -10,-18 q0,-18 10,-18z"/>
          <path d="M62,10 q8,10 8,20 q0,10 -8,20 q-10,0 -10,-20 q0,-20 10,-20z"/>
          <path d="M84,14 q8,10 8,16 q0,6 -8,16 q-10,0 -10,-16 q0,-16 10,-16z"/>
        </g>
        <!-- dorsal hint -->
        <path d="M50,8 q12,-8 24,0" fill="none" stroke="#1a1a1a" stroke-width="3" stroke-linecap="round"/>
        <!-- tail -->
        <polygon class="tail" points="100,30 138,14 138,46" fill="#ff7a00" stroke="#1a1a1a" stroke-width="3"/>
        <!-- eye -->
        <circle cx="36" cy="24" r="3.2" fill="white"/><circle cx="36" cy="24" r="1.6" fill="#1a1a1a"/>
      </symbol>

      <!-- Kelp + coral band -->
      <symbol id="kelp" viewBox="0 0 60 200">
        <path d="M30,200 C34,150 18,128 26,98 C36,66 24,42 28,12" fill="none" stroke="#147a80" stroke-width="10" stroke-linecap="round"/>
        <path d="M44,200 C48,160 36,140 42,112 C52,82 40,60 44,30" fill="none" stroke="#0f6a70" stroke-width="7" stroke-linecap="round"/>
      </symbol>
      <symbol id="coralBand" viewBox="0 0 1200 240">
        <path fill="#06253a"
          d="M0,220 C60,210 80,182 130,190 C165,196 175,210 200,220
             C260,240 350,230 420,210 C495,188 560,210 640,220
             C720,230 840,225 900,208 C980,186 1050,192 1120,210
             C1160,220 1180,222 1200,224 L1200,240 L0,240 Z"/>
        <path d="M160,210 q10,-48 30,-48 q10,0 16,14 q5,10 1,20 q-6,16 -20,24 Z" fill="#0a324a"/>
        <path d="M980,205 q12,-50 34,-50 q10,0 16,16 q5,10 1,20 q-7,18 -22,25 Z" fill="#0a324a"/>
      </symbol>

      <!-- NEW: Seabed (sand gradient, pebbles, starfish, shells) -->
      <symbol id="seabed" viewBox="0 0 1200 220">
        <defs>
          <linearGradient id="sandGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="rgba(242,216,165,0.55)"/>
            <stop offset="100%" stop-color="rgba(210,184,134,0.35)"/>
          </linearGradient>
        </defs>
        <!-- dunes -->
        <path d="M0,120 C120,100 220,130 360,120 C520,108 640,136 780,124 C940,110 1060,138 1200,126 L1200,220 L0,220 Z" fill="url(#sandGrad)"/>
        <!-- pebbles -->
        <g fill="rgba(120,96,64,0.35)">
          <ellipse cx="120" cy="168" rx="14" ry="7"/>
          <ellipse cx="168" cy="178" rx="8" ry="4"/>
          <ellipse cx="430" cy="172" rx="10" ry="5"/>
          <ellipse cx="620" cy="182" rx="16" ry="8"/>
          <ellipse cx="890" cy="174" rx="12" ry="6"/>
          <ellipse cx="1060" cy="186" rx="10" ry="5"/>
        </g>
        <!-- starfish -->
        <path d="M300,168 l8,-12 l8,12 l14,2 l-10,10 l4,14 l-14,-6 l-14,6 l4,-14 l-10,-10 z"
              fill="rgba(235,100,70,0.6)"/>
        <!-- shell -->
        <path d="M980,180 q16,-12 28,0 q-6,16 -28,16 q-22,0 -28,-16 q12,-12 28,0z"
              fill="rgba(245,220,200,0.6)"/>
      </symbol>

      <!-- NEW: Anemone cluster for clownfish -->
      <symbol id="anemone" viewBox="0 0 200 180">
        <g stroke-linecap="round" fill="none">
          <path d="M40,180 C50,120 30,110 36,80" stroke="rgba(180,120,200,.6)" stroke-width="16"/>
          <path d="M70,180 C80,110 58,96 64,60" stroke="rgba(200,140,220,.55)" stroke-width="14"/>
          <path d="M100,180 C110,125 92,112 98,70" stroke="rgba(170,110,190,.6)" stroke-width="18"/>
          <path d="M130,180 C140,116 118,100 126,66" stroke="rgba(190,130,210,.55)" stroke-width="15"/>
          <path d="M160,180 C172,122 150,108 158,74" stroke="rgba(175,115,200,.58)" stroke-width="17"/>
        </g>
        <ellipse cx="100" cy="180" rx="82" ry="18" fill="rgba(30,20,40,.55)"/>
      </symbol>
    </defs>
  </svg>

  <!-- Layers -->
  <div class="layer layer-caustics"></div>
  <div class="layer layer-rays"></div>

  <div class="layer kelp-back"></div>
  <div class="layer bubbles"></div>
  <div class="layer fish-layer"></div>

  <div class="layer coral-back"></div>
  <div class="layer kelp-front"></div>
  <div class="layer coral-front"></div>

  <!-- NEW seabed + anemone (front-most bottom, semi-transparent to keep overall transparent) -->
  <div class="layer seabed"></div>
  <div class="layer anemone-layer"></div>
</section>

<style>
  #reef-realistic{ position:relative; width:100%; height:460px; background:transparent; overflow:hidden; pointer-events:none; isolation:isolate; }
  #reef-realistic .layer{ position:absolute; inset:0; transform:translateZ(0); }

  /* Coral */
  .coral-svg{ width:140%; height:auto; position:absolute; left:-20%; bottom:-6px; }
  #reef-realistic .coral-back{ z-index:3; }
  #reef-realistic .coral-front{ z-index:6; }
  #reef-realistic .coral-back svg use{ fill:#0a2e45; opacity:.85; }
  #reef-realistic .coral-front svg use{ fill:#041c2c; opacity:1; }

  /* Kelp */
  .kelp-wrap{ position:absolute; bottom:0; width:120px; height:220px; transform-origin:bottom center; animation:sway 5.5s ease-in-out infinite; opacity:.95; }
  .kelp-wrap.slow{ animation-duration:7s; } .kelp-wrap.fast{ animation-duration:4.5s; }
  @keyframes sway{ 0%,100%{transform:rotate(-2deg)} 50%{transform:rotate(2.6deg)} }

  /* Bubbles */
  .bubble{ position:absolute; bottom:-40px; width:8px; height:8px; border-radius:50%;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.65);
    background: radial-gradient(circle at 30% 30%, rgba(255,255,255,.9), rgba(255,255,255,.2) 40%, rgba(255,255,255,0) 60%);
    filter: blur(.2px); animation: rise linear infinite; opacity:.8; }
  @keyframes rise{ from{transform:translateY(0) translateX(0);opacity:0} 10%{opacity:.5} 80%{opacity:.9} to{transform:translateY(-120%) translateX(var(--drift,10px));opacity:0} }

  /* Fish (forward motion fix remains) */
  .fish{ position:absolute; width:var(--w,120px); height:auto; will-change:transform; z-index:5; }
  .fish .sprite{ display:block; transform: scaleX(-1); }  /* head faces forward when moving L->R */
  .fish .tail{ transform-origin: 85% 50%; animation: tailWiggle .55s ease-in-out infinite alternate; }
  @keyframes tailWiggle{ from{transform:rotate(7deg)} to{transform:rotate(-7deg)} }
  .fish.depth-near{ filter: drop-shadow(0 8px 10px rgba(0,0,0,.35)); opacity:.98; }
  .fish.depth-mid { filter: drop-shadow(0 6px  8px rgba(0,0,0,.28)); opacity:.88; }
  .fish.depth-far { filter: drop-shadow(0 3px  6px rgba(0,0,0,.20)); opacity:.78; }

  /* Caustics + Rays */
  #reef-realistic .layer-caustics::before{
    content:""; position:absolute; inset:-10%;
    background: radial-gradient(120% 80% at 50% 0%, rgba(255,255,255,0.08), rgba(255,255,255,0) 60%) top/100% 100% no-repeat;
    opacity:.7; mix-blend-mode:screen; animation:causticDrift 18s ease-in-out infinite alternate; filter:url(#caustics);
  }
  @keyframes causticDrift{ 0%{transform:translateY(-2%) scale(1.02);opacity:.55} 50%{transform:translateY(2%) scale(1.05);opacity:.75} 100%{transform:translateY(-1%) scale(1.03);opacity:.6} }
  #reef-realistic .layer-rays::before{
    content:""; position:absolute; inset:-10% -20% 0 -20%;
    background: repeating-linear-gradient(75deg, rgba(255,255,255,.22) 0 6px, rgba(255,255,255,0) 6px 36px);
    opacity:.32; transform-origin:top center; mask:url(#raysMask); animation:raysMove 24s linear infinite;
  }
  @keyframes raysMove{ from{transform:translateY(-2%) rotate(-6deg)} to{transform:translateY(2%) rotate(6deg)} }

  /* NEW: Seabed (semi-transparent so overall remains transparent) */
  .seabed svg{ position:absolute; left:-10%; bottom:-2px; width:120%; height:auto; }
  .anemone-layer svg{ position:absolute; bottom:0; left:8%; width:200px; height:180px; opacity:.95; }

  @media (prefers-reduced-motion: reduce){ #reef-realistic *{ animation:none !important; } }
</style>

<script>
(() => {
  const root = document.getElementById('reef-realistic');
  const fishLayer = root.querySelector('.fish-layer');
  const bubblesLayer = root.querySelector('.bubbles');

  // Coral layers
  root.querySelector('.coral-back').innerHTML  = `<svg class="coral-svg" viewBox="0 0 1200 240" aria-hidden="true"><use href="#coralBand"/></svg>`;
  root.querySelector('.coral-front').innerHTML = `<svg class="coral-svg" viewBox="0 0 1200 240" aria-hidden="true"><use href="#coralBand"/></svg>`;

  // Seabed + anemone (natural bottom)
  root.querySelector('.seabed').innerHTML = `<svg viewBox="0 0 1200 220" aria-hidden="true"><use href="#seabed"/></svg>`;
  root.querySelector('.anemone-layer').innerHTML = `<svg viewBox="0 0 200 180" aria-hidden="true"><use href="#anemone"/></svg>`;

  // Kelp clusters
  function addKelp(layerName, count, y=0){
    const layer = root.querySelector(layerName);
    const frag = document.createDocumentFragment();
    for(let i=0;i<count;i++){
      const wrap = document.createElement('div');
      wrap.className = 'kelp-wrap ' + (i%3===0?'slow': i%3===1?'':'fast');
      wrap.style.left = `calc(${Math.random()*100}% - 60px)`;
      wrap.style.bottom = `${y}px`;
      wrap.innerHTML = `<svg viewBox="0 0 60 200" width="120" height="220" aria-hidden="true"><use href="#kelp"/></svg>`;
      frag.appendChild(wrap);
    }
    layer.appendChild(frag);
  }
  addKelp('.kelp-back', 5, 0);
  addKelp('.kelp-front', 4, 0);

  // Bubbles
  function spawnBubble(){
    const b = document.createElement('div');
    b.className = 'bubble';
    const size = 4 + Math.random()*10;
    b.style.width = b.style.height = size + 'px';
    b.style.left = (5 + Math.random()*90) + '%';
    b.style.setProperty('--drift', (Math.random()*40 - 20) + 'px');
    const dur = 6 + Math.random()*8;
    b.style.animationDuration = dur + 's';
    bubblesLayer.appendChild(b);
    setTimeout(() => b.remove(), dur*1000);
  }
  setInterval(spawnBubble, 350);

  // Fish (A, B, and NEW clownfish)
  const TYPES = ['#fishA', '#fishB', '#clownfish'];

  function makeFish({ y=50, speed=1.0, amp=18, freq=1.0, tilt=12, scale=1, depth='mid', type=0, delay=0 }){
    const wrap = document.createElement('div');
    wrap.className = `fish depth-${depth}`;
    wrap.style.setProperty('--w', (scale*(type===0?120:type===1?110:105))+'px');

    const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
    svg.setAttribute('viewBox', type===0?'0 0 160 70': type===1?'0 0 150 64':'0 0 140 60');
    svg.setAttribute('width',  type===0?160: type===1?150:140);
    svg.setAttribute('height', type===0?70 : type===1?64 :60);
    svg.setAttribute('class','sprite');
    svg.setAttribute('aria-hidden','true');
    svg.innerHTML = `<use href="${TYPES[type]}"></use>`;
    wrap.appendChild(svg);
    fishLayer.appendChild(wrap);

    // Motion (left -> right, head forward)
    let x = -250 - Math.random()*200;
    const maxX = root.clientWidth + 300;
    const baseY = root.clientHeight * (y/100);
    let t = delay;

    function step(){
      x += speed;
      if(x > maxX){ x = -250 - Math.random()*200; }

      t += 0.016;
      const yOsc = Math.sin(t*freq)*amp + Math.sin(t*freq*0.37)*amp*0.25;
      const yy   = baseY + yOsc;

      const dy   = Math.cos(t*freq)*amp*freq + Math.cos(t*freq*0.37)*amp*0.25*0.37;
      const angle = Math.max(-tilt, Math.min(tilt, dy*0.6));

      wrap.style.transform = `translate(${x}px, ${yy}px) rotate(${angle}deg) scale(${scale})`;
      requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  // Schools: include clownfish near anemone (lower-left)
  const school = [
    // far (blue-green)
    {y:24, speed:0.7,  amp:12, freq:.8,  tilt:8,  scale:.8,  depth:'far',  type:1},
    {y:28, speed:0.75, amp:10, freq:.9,  tilt:9,  scale:.76, depth:'far',  type:1, delay:0.5},
    // mid (blue school)
    {y:46, speed:1.1,  amp:16, freq:1.0, tilt:11, scale:1.0, depth:'mid',  type:0},
    {y:53, speed:1.05, amp:18, freq:.95, tilt:12, scale:.95, depth:'mid',  type:0, delay:.7},
    {y:58, speed:1.0,  amp:14, freq:1.05,tilt:10, scale:.9,  depth:'mid',  type:1, delay:1.1},
    // near (mixed)
    {y:68, speed:1.35, amp:22, freq:1.1, tilt:13, scale:1.18, depth:'near', type:0},
    {y:72, speed:1.25, amp:20, freq:1.15,tilt:12, scale:1.08, depth:'near', type:1, delay:.4},
    // NEW: clownfish family hovering around anemone (lower-left quadrant)
    {y:78, speed:0.8, amp:12, freq:1.2, tilt:10, scale:.95, depth:'near', type:2, delay:.2},
    {y:75, speed:0.85,amp:10, freq:1.1, tilt:10, scale:.9,  depth:'near', type:2, delay:.6},
    {y:80, speed:0.9, amp:14, freq:1.0, tilt:11, scale:.98, depth:'near', type:2, delay:1.0},
  ];
  school.forEach(cfg => makeFish(cfg));

  // Kelp parallax on mouse
  const parallax = (x) => {
    const back  = root.querySelector('.coral-back .coral-svg');
    const front = root.querySelector('.coral-front .coral-svg');
    if(!back || !front) return;
    back.style.transform  = `translateX(${x * -0.03}px)`;
    front.style.transform = `translateX(${x * -0.07}px)`;
  };
  root.addEventListener('mousemove', (e)=>{
    const r = root.getBoundingClientRect();
    const p = ((e.clientX - r.left)/r.width - 0.5) * 200;
    parallax(p);
  });

  // Spawn bubbles
  setInterval(()=>spawnBubble(), 360);
  function spawnBubble(){
    const b = document.createElement('div');
    b.className = 'bubble';
    const size = 4 + Math.random()*10;
    b.style.width = b.style.height = size + 'px';
    b.style.left = (5 + Math.random()*90) + '%';
    b.style.setProperty('--drift', (Math.random()*40 - 20) + 'px');
    const dur = 6 + Math.random()*8;
    b.style.animationDuration = dur + 's';
    bubblesLayer.appendChild(b);
    setTimeout(() => b.remove(), dur*1000);
  }
})();
</script>
