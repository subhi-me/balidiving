<style>
/* Style dasar untuk card dan hover effect */
.grid-item {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
}

.grid-item:hover {
    transform: scale(1.02);
    z-index: 10;
}

/* Style overlay saat hover */
.popup-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,20,50,0.85) 0%, rgba(0,50,100,0.75) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 1rem;
    text-align: center;
}

.grid-item:hover .popup-overlay {
    opacity: 1;
}

.popup-content {
    transform: translateY(20px);
    transition: transform 0.3s ease;
}

.grid-item:hover .popup-content {
    transform: translateY(0);
}

/* Style container grid */
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Style untuk container tombol */
.popup-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    align-items: center;
}


/* Media query untuk tampilan mobile */
@media (max-width: 768px) {
    .grid-container {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 0.75rem;
    }
}
</style>

  
  <style>
        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            background: transparent;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .marine-section {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            background: transparent;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 3rem;
            text-shadow: 0 2px 4px rgba(3, 105, 161, 0.3);
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            justify-items: center;
        }

        .fish-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .fish-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .fish-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .fish-card:hover::before {
            left: 100%;
        }

        .fish-image-container {
            position: relative;
            margin-bottom: 1.5rem;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fish-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .fish-image.loaded {
            opacity: 1;
        }

        .fish-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e40af;
            margin: 0;
            text-shadow: 0 1px 2px rgba(30, 64, 175, 0.2);
        }

        /* Animasi berenang untuk setiap ikan */
        .try-diving .fish-image {
            animation: clownSwim 4s ease-in-out infinite;
        }

        .manta-ray .fish-image {
            animation: mantaGlide 6s ease-in-out infinite;
        }

        .mola-mola .fish-image {
            animation: molaFloat 5s ease-in-out infinite;
        }

        .whale-shark .fish-image {
            animation: whaleSwim 7s ease-in-out infinite;
        }

        @keyframes clownSwim {
            0%, 100% { transform: translateX(0) rotate(0deg) scale(1); }
            25% { transform: translateX(5px) rotate(2deg) scale(1.05); }
            50% { transform: translateX(0) rotate(0deg) scale(1); }
            75% { transform: translateX(-5px) rotate(-2deg) scale(1.05); }
        }

        @keyframes mantaGlide {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            33% { transform: translateY(-8px) rotate(1deg) scale(1.1); }
            66% { transform: translateY(4px) rotate(-1deg) scale(0.95); }
        }

        @keyframes molaFloat {
            0%, 100% { transform: translateY(0) translateX(0) rotate(0deg); }
            25% { transform: translateY(-6px) translateX(3px) rotate(1deg); }
            50% { transform: translateY(2px) translateX(-2px) rotate(0deg); }
            75% { transform: translateY(-3px) translateX(4px) rotate(-1deg); }
        }

        @keyframes whaleSwim {
            0%, 100% { transform: translateX(0) translateY(0) scale(1); }
            20% { transform: translateX(3px) translateY(-4px) scale(1.02); }
            40% { transform: translateX(-2px) translateY(2px) scale(0.98); }
            60% { transform: translateX(4px) translateY(-2px) scale(1.01); }
            80% { transform: translateX(-3px) translateY(3px) scale(0.99); }
        }

        /* Tombol kontak bulat */
        .contact-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #0ea5e9, #0369a1);
            border-radius: 50%;
            text-decoration: none;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
            position: relative;
            overflow: hidden;
        }

        .contact-btn:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.5);
            background: linear-gradient(135deg, #0284c7, #0c4a6e);
        }

        .contact-btn:active {
            transform: scale(0.95) rotate(90deg);
        }

        .contact-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .contact-btn:hover::before {
            left: 100%;
        }

        .contact-btn span {
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .contact-btn:hover span {
            transform: rotate(-90deg);
        }

        /* Efek gelembung air */
        .bubble {
            position: absolute;
            background: rgba(173, 216, 230, 0.6);
            border-radius: 50%;
            animation: bubbleRise 3s linear infinite;
            pointer-events: none;
        }

        @keyframes bubbleRise {
            0% {
                bottom: -10px;
                opacity: 0.7;
                transform: translateX(0);
            }
            50% {
                opacity: 1;
                transform: translateX(10px);
            }
            100% {
                bottom: 100%;
                opacity: 0;
                transform: translateX(-5px);
            }
        }

        /* Responsive design */
        @media (max-width: 1024px) and (min-width: 769px) {
            .cards-container {
                gap: 1.5rem;
            }
            
            .fish-card {
                padding: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .marine-section {
                padding: 1rem;
            }
            
            .section-title {
                font-size: 2rem;
                margin-bottom: 2rem;
            }
            
            .cards-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
            
            .fish-card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .section-title {
                font-size: 1.8rem;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
    </style>
  <style>@view-transition { navigation: auto; }</style>

  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>
 </head>
 <body>
  <main class="marine-section">
  <!-- The Ocean Is Our Second Home – Emotional Section -->
<section id="ocean-second-home" class="relative isolate overflow-hidden bg-white/40 text-[#0a2a5c]">
  <!-- wave top -->
  <div class="absolute top-0 left-0 w-full overflow-hidden leading-[0] rotate-180">
    <svg class="relative block w-[calc(100%+1.3px)] h-[80px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
      <path d="M985.66,92.83c-70.22-20.26-146.18-43.43-218.89-42.13-73.45,1.31-142.74,26.57-215.44,34.78C457.61,93.42,379.92,82.28,301,66.19c-84-17.43-168.1-39.83-252-31.06V120H1200V0C1117.27,19.36,1055.88,113.09,985.66,92.83Z" fill="#ffffff"></path>
    </svg>
  </div>

  <!-- subtle aura background -->
  <div class="pointer-events-none absolute inset-0 -z-10">
    <div class="absolute -top-40 left-1/2 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full blur-3xl opacity-30 bg-gradient-to-tr from-[#23a0b4] to-[#3552c8]"></div>
    <div class="absolute bottom-0 left-1/2 h-[20rem] w-[20rem] -translate-x-1/2 rounded-full blur-3xl opacity-20 bg-gradient-to-tr from-[#f23d4e] to-[#eebe35]"></div>
  </div>

  <div class="mx-auto max-w-3xl px-6 py-24 text-center relative z-10">
    <p class="text-xl font-semibold tracking-wide text-[#063c7f] uppercase mb-4">The Ocean is Our Second Home</p>

    <p class="mt-6 text-lg leading-relaxed text-[#0a2a5c]">
      Under the surface, life feels different.  
      Every breath is calm, every move feels natural.  
      Here, <strong>care</strong> is not a rule — it’s who we are.  
      <strong>Respect</strong> is not a lesson — it’s how we touch the sea.
    </p>

    <p class="mt-6 text-lg leading-relaxed text-[#0a2a5c]">
      Every dive begins with calm guidance, clear steps, and quiet focus.  
      What you hear below is not silence —  
      it’s the ocean softly saying: <em>you belong here.</em>
    </p>
  </div>
</section>


   <div class="cards-container">
           <article class="fish-card try-diving">
     <div class="fish-image-container"><img class="fish-image" data-src="https://www.balidiving.com/images/icons/diver/snorkel.png" alt="scuba diving" onerror="this.src=''; this.alt='Image not loaded'; this.style.display='none';">
     </div>
     <h2 class="fish-name" id="clownFishName">Snorkeling</h2><a href="https://www.balidiving.com/snorkeling"  rel="noopener noreferrer" class="contact-btn" aria-label="Hubungi untuk informasi Clown Fish"> <span>+</span> </a>
    </article>
    <article class="fish-card whale-shark">
     <div class="fish-image-container"><img class="fish-image" data-src="https://www.balidiving.com/images/icons/diver/try-diving-2.png" alt="scuba diving" onerror="this.src=''; this.alt='Image not loaded'; this.style.display='none';">
     </div>
     <h2 class="fish-name" id="diving">Try Diving</h2><a href="https://www.balidiving.com/try-scuba-diving"  rel="noopener noreferrer" class="contact-btn" aria-label="Hubungi untuk informasi Clown Fish"> <span>+</span> </a>
    </article>

        <article class="fish-card mola-mola">
     <div class="fish-image-container"><img class="fish-image" data-src="https://www.balidiving.com/images/icons/diver/padi-open-water.png" alt="scuba diving" onerror="this.src=''; this.alt='Image not loaded'; this.style.display='none';">
     </div>
     <h3 class="fish-name" id="clownFishName">Learn Diving</h2><a href="https://www.balidiving.com/scuba-diving-certification"  rel="noopener noreferrer" class="contact-btn" aria-label="Hubungi untuk informasi Clown Fish"> <span>+</span> </a>
    </article>
        <article class="fish-card try-diving">
     <div class="fish-image-container"><img class="fish-image" data-src="https://www.balidiving.com/images/icons/diver/padi-master-scuba-diver.png" alt="scuba diving" onerror="this.src=''; this.alt='Image not loaded'; this.style.display='none';">
     </div>
     <h2 class="fish-name" id="clownFishName">Go Diving</h2><a href="https://www.balidiving.com/discover-scuba-diving-in-bali"  rel="noopener noreferrer" class="contact-btn" aria-label="Hubungi untuk informasi Clown Fish"> <span>+</span> </a>
    </article>
    
   </div>
    <div class="mt-16 bg-gradient-to-br from-navy to-primary text-white rounded-2xl shadow-2xl p-8 lg:p-10 text-center transform hover:scale-[1.02] transition-transform duration-300">
  <h2 class="text-white md:text-4xl font-extrabold mb-8 text-center text-lightblue leading-tight">
    All-Inclusive<br>
    Bali Diving Experience<br>
    <span class="block text-base md:text-lg font-normal text-slate-300 tracking-wide mt-1">
      Everything You Need for a Perfect Dive Day
    </span>
  </h2>

  <div class="max-w-4xl mx-auto">
    <ul class="grid md:grid-cols-2 gap-x-8 gap-y-4 text-left text-lg mb-8">
      <li class="flex items-center space-x-3">
        <i class="fa-solid fa-check text-gold text-2xl flex-shrink-0"></i>
        <span class="text-lightblue">Free hotel pickup & drop-off</span>
      </li>
      <li class="flex items-center space-x-3">
        <i class="fa-solid fa-check text-gold text-2xl flex-shrink-0"></i>
        <span class="text-lightblue">3 amazing ocean dives</span>
      </li>
      <li class="flex items-center space-x-3">
        <i class="fa-solid fa-check text-gold text-2xl flex-shrink-0"></i>
        <span class="text-lightblue">Tasty lunch & drinks</span>
      </li>
      <li class="flex items-center space-x-3">
        <i class="fa-solid fa-check text-gold text-2xl flex-shrink-0"></i>
        <span class="text-lightblue">Fast boat to top dive spots</span>
      </li>
      <li class="flex items-center space-x-3">
        <i class="fa-solid fa-check text-gold text-2xl flex-shrink-0"></i>
        <span class="text-lightblue">Pro PADI instructor with you</span>
      </li>
      <li class="flex items-center space-x-3">
        <i class="fa-solid fa-check text-gold text-2xl flex-shrink-0"></i>
        <span class="text-lightblue">Clean, ready-to-dive gear</span>
      </li>
      <li class="flex items-center space-x-3">
        <i class="fa-solid fa-check text-gold text-2xl flex-shrink-0"></i>
        <span class="text-lightblue">All fees & park tickets included</span>
      </li>
      <li class="flex items-center space-x-3">
        <i class="fa-solid fa-check text-gold text-2xl flex-shrink-0"></i>
        <span class="text-lightblue">Full local dive insurance</span>
      </li>
    </ul>

    <a href="https://balidiving.com/pricelist"
       class="inline-block bg-[#0070d3] text-white px-6 py-2.5 rounded-md font-semibold hover:bg-blue-700 transition">
       See All Offers
    </a>
  </div>
</div>

  </main>
  <script>
        // Konfigurasi default
        const defaultConfig = {
            section_title: "Kehidupan Laut Eksotis",
            diving4: "Clown Fish",
            diving3: "Manta Ray",
            diving2: "Mola-Mola",
            diving: "Whale Shark"
        };

        // Fungsi render untuk memperbarui UI
        async function render(config) {
            document.getElementById('sectionTitle').textContent = config.section_title || defaultConfig.section_title;
            document.getElementById('clownFishName').textContent = config.diving4 || defaultConfig.diving4;
            document.getElementById('mantaRayName').textContent = config.diving3 || defaultConfig.diving3;
            document.getElementById('molaMolaName').textContent = config.diving2 || defaultConfig.diving2;
            document.getElementById('diving').textContent = config.diving || defaultConfig.diving;
        }

        // Fungsi untuk capabilities
        function mapToCapabilities(config) {
            return {
                recolorables: [],
                borderables: [],
                fontEditable: undefined,
                fontSizeable: undefined
            };
        }

        // Fungsi untuk edit panel values
        function mapToEditPanelValues(config) {
            return new Map([
                ["section_title", config.section_title || defaultConfig.section_title],
                ["diving4", config.diving4 || defaultConfig.diving4],
                ["diving3", config.diving3 || defaultConfig.diving3],
                ["diving2", config.diving2 || defaultConfig.diving2],
                ["diving", config.diving || defaultConfig.diving]
            ]);
        }

        // Fungsi untuk memuat gambar setelah page load
        function lazyLoadImages() {
            const images = document.querySelectorAll('.fish-image[data-src]');
            
            images.forEach(img => {
                const src = img.getAttribute('data-src');
                const newImg = new Image();
                
                newImg.onload = function() {
                    img.src = src;
                    img.classList.add('loaded');
                    img.removeAttribute('data-src');
                };
                
                newImg.onerror = function() {
                    img.style.display = 'none';
                    img.alt = 'Image not loaded';
                };
                
                newImg.src = src;
            });
        }

        // Fungsi untuk membuat efek gelembung
        function createBubbles() {
            const cards = document.querySelectorAll('.fish-card');
            
            cards.forEach(card => {
                setInterval(() => {
                    if (Math.random() > 0.7) {
                        const bubble = document.createElement('div');
                        bubble.className = 'bubble';
                        bubble.style.left = Math.random() * 100 + '%';
                        bubble.style.width = bubble.style.height = (Math.random() * 8 + 4) + 'px';
                        bubble.style.animationDelay = Math.random() * 2 + 's';
                        
                        card.appendChild(bubble);
                        
                        setTimeout(() => {
                            if (bubble.parentNode) {
                                bubble.parentNode.removeChild(bubble);
                            }
                        }, 3000);
                    }
                }, 2000);
            });
        }

        // Inisialisasi setelah DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Delay untuk memastikan page sudah fully loaded
            setTimeout(() => {
                lazyLoadImages();
                createBubbles();
            }, 100);
        });

        // Inisialisasi Element SDK
        if (window.elementSdk) {
            window.elementSdk.init({
                defaultConfig,
                render,
                mapToCapabilities,
                mapToEditPanelValues
            });
        }
    </script>

<header class="bg-white shadow-sm py-6 mb-8">
   
</header>
<main class="container mx-auto px-4 pb-12">
    <section id="grid" class="grid-container" role="main" aria-labelledby="gridHeading">

  <!-- Screen-reader only helper -->
  <style>
    .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
  </style>

  <!-- Judul seksi untuk struktur halaman (H2) -->
  <h2 id="gridHeading" class="sr-only">Gallery of Bali's Underwater Attractions</h2>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/images/main/grid/tulamben-wreck.jpg');"
           aria-labelledby="title-tulamben">
    <div class="w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-tulamben" class="text-white text-xl font-semibold mb-3" itemprop="name">
            Scuba Diving in Tulamben
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            Explore the iconic USAT Liberty shipwreck, home to thousands of marine creatures. A world-class diving experience awaits you.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/tulamben"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: Scuba Diving in Tulamben">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('Scuba Diving in Tulamben')"
                    aria-label="Chat about Scuba Diving in Tulamben">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/template/images/thumbnail/blue-lagoon-padangbai.jpg');"
           aria-labelledby="title-blue-lagoon">
    <div class="w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-blue-lagoon" class="text-white text-xl font-semibold mb-3" itemprop="name">
            Snorkeling at Blue Lagoon
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            Enjoy the clear, calm waters of Padangbai. A perfect spot for beginners and families who want to see beautiful coral reefs.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/snorkeling-at-blue-lagoon"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: Snorkeling at Blue Lagoon">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('Snorkeling at Blue Lagoon')"
                    aria-label="Chat about Snorkeling at Blue Lagoon">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/images/main/grid/manta.jpg');"
           aria-labelledby="title-manta-point">
    <div class="w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-manta-point" class="text-white text-xl font-semibold mb-3" itemprop="name">
            Meeting Manta Rays in Nusa Penida
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            Experience the thrill of swimming with majestic giant Manta Rays at Manta Point. An unforgettable moment under the seas of Nusa Penida.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/manta-point"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: Meeting Manta Rays in Nusa Penida">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('Meeting Manta Rays in Nusa Penida')"
                    aria-label="Chat about Meeting Manta Rays in Nusa Penida">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/template/images/thumbnail/amed.jpg');"
           aria-labelledby="title-amed">
    <div class="w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-amed" class="text-white text-xl font-semibold mb-3" itemprop="name">
            Amed's Biodiversity
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            Discover a 'muck diving' paradise with unique macro marine life like Pygmy Seahorses and Nudibranchs. A must-visit for underwater photographers.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/amed"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: Amed's Biodiversity">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('Amed\'s Biodiversity')"
                    aria-label="Chat about Amed's Biodiversity">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/template/images/thumbnail/menjangan.jpg');"
           aria-labelledby="title-menjangan">
    <div class="w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-menjangan" class="text-white text-xl font-semibold mb-3" itemprop="name">
            Diving at Menjangan Island
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            Dive along spectacular vertical coral walls (wall diving) in West Bali National Park. Stunningly clear and mesmerizing views.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/menjangan"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: Diving at Menjangan Island">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('Diving at Menjangan Island')"
                    aria-label="Chat about Diving at Menjangan Island">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/template/images/thumbnail/ow.jpg');"
           aria-labelledby="title-ow">
    <div class="w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-ow" class="text-white text-xl font-semibold mb-3" itemprop="name">
            PADI Open Water Course
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            Start your diving adventure in Bali. Get your PADI Open Water certification from professional instructors and explore a new world.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/padi-open-water"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: PADI Open Water Course">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('PADI Open Water Course')"
                    aria-label="Chat about PADI Open Water Course">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/template/images/thumbnail/coral.jpg');"
           aria-labelledby="title-coral">
    <div class="text-white w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-coral" class="text-white text-xl font-semibold mb-3" itemprop="name">
            Coral Reef Conservation Projects
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            Participate in efforts to protect Bali's precious coral reefs. A unique opportunity to contribute to marine sustainability.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/conservation"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: Coral Reef Conservation Projects">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('Coral Reef Conservation Projects')"
                    aria-label="Chat about Coral Reef Conservation Projects">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/template/images/thumbnail/photog1.jpg');"
           aria-labelledby="title-photo">
    <div class="w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-photo" class="text-white text-xl font-semibold mb-3" itemprop="name">
            Underwater Photography Workshops
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            Learn to capture the magic of the underwater world with expert guidance. Improve your skills in Bali's stunning dive sites.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/photography"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: Underwater Photography Workshops">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('Underwater Photography Workshops')"
                    aria-label="Chat about Underwater Photography Workshops">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <article class="grid-item bg-white rounded-lg shadow-md aspect-square"
           itemscope itemtype="https://schema.org/ImageObject"
           style="background-image: url('https://balidiving.com/template/images/thumbnail/unique.jpg');"
           aria-labelledby="title-unique">
    <div class="w-full h-full relative">
      <div class="popup-overlay">
        <div class="popup-content">
          <h2 id="title-unique" class="text-white text-xl font-semibold mb-3" itemprop="name">
            Unique Marine Life Spotting
          </h2>
          <p class="text-gray-200 text-sm mb-4 leading-relaxed" itemprop="description">
            From tiny pygmy seahorses to majestic whale sharks, Bali's waters offer incredible opportunities for spotting rare marine species.
          </p>
          <div class="popup-buttons">
            <a href="recommendations/marin-life"
               class="inline-block bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors"
               aria-label="Details: Unique Marine Life Spotting">Details</a>
            <button class="chat-launcher"
                    onclick="openChatWithTitle('Unique Marine Life Spotting')"
                    aria-label="Chat about Unique Marine Life Spotting">Chat</button>
          </div>
        </div>
      </div>
    </div>
  </article>

  <!-- Fish cards -->
  <article class="fish-card manta-ray" aria-labelledby="title-mantaRay">
    <div class="fish-image-container">
      <img class="fish-image"
           data-src="https://www.balidiving.com/images/icons/fish/manta.png"
           alt="Manta Ray - Ikan pari raksasa yang anggun berenang di laut dalam"
           onerror="this.src=''; this.alt='Image not loaded'; this.style.display='none';">
    </div>
    <h2 class="fish-name" id="title-mantaRay">Manta Ray</h2>
    <a href="https://balidiving.com/pricelist"
       rel="noopener noreferrer"
       class="contact-btn"
       aria-label="Hubungi untuk informasi: Manta Ray">
       <span>+</span>
    </a>
  </article>

  <article class="fish-card mola-mola" aria-labelledby="title-molaMola">
    <div class="fish-image-container">
      <img class="fish-image"
           data-src="https://www.balidiving.com/images/icons/fish/mola-mola.png"
           alt="Mola-Mola - Ikan matahari yang unik dengan bentuk bulat pipih"
           onerror="this.src=''; this.alt='Image not loaded'; this.style.display='none';">
    </div>
    <h2 class="fish-name" id="title-molaMola">Mola-Mola</h2>
    <a href="https://balidiving.com/pricelist"
       rel="noopener noreferrer"
       class="contact-btn"
       aria-label="Hubungi untuk informasi: Mola-Mola">
       <span>+</span>
    </a>
  </article>

  <article class="fish-card try-diving" aria-labelledby="title-clownFish">
    <div class="fish-image-container">
      <img class="fish-image"
           data-src="https://www.balidiving.com/images/icons/fish/clown-fish.png"
           alt="Clown Fish - ikan badut yang dikenal bersimbiosis dengan anemon"
           onerror="this.src=''; this.alt='Image not loaded'; this.style.display='none';">
    </div>
    <h2 class="fish-name" id="title-clownFish">Clown Fish</h2>
    <a href="https://balidiving.com/pricelist"
       rel="noopener noreferrer"
       class="contact-btn"
       aria-label="Hubungi untuk informasi: Clown Fish">
       <span>+</span>
    </a>
  </article>

</section>

    <section>
     <div class="container mx-auto px-4 text-center">
   <div style="height:50px;"></div>

        <p class="text-lg text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">Whether you want to explore a legendary shipwreck or come face-to-face with a massive Manta Ray, your dream adventure is waiting.!</p>
        
        <div class="max-w-5xl w-full mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                
                <button onclick="window.open('https://balidiving.com/login', '_blank')" class="dive-button bg-white border border-gray-200 rounded-lg px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors duration-200 ease-in-out transform hover:scale-105">
                    <i class="fa-solid fa-ship"></i> USAT Liberty Wreck
                </button>
                
                <button onclick="window.open('https://balidiving.com/login', '_blank')" class="dive-button bg-white border border-gray-200 rounded-lg px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors duration-200 ease-in-out transform hover:scale-105">
                    <i class="fa-solid fa-fish"></i> Manta Point
                </button>
                
                <button onclick="window.open('https://balidiving.com/login', '_blank')" class="dive-button bg-white border border-gray-200 rounded-lg px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors duration-200 ease-in-out transform hover:scale-105">
                    <i class="fa-solid fa-water"></i> Crystal Bay
                </button>
                
                <button onclick="window.open('https://balidiving.com/login', '_blank')" class="dive-button bg-white border border-gray-200 rounded-lg px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors duration-200 ease-in-out transform hover:scale-105">
                    <i class="fa-solid fa-island-tropical"></i> Menjangan Island
                </button>
                
                <button onclick="window.open('https://balidiving.com/login', '_blank')" class="dive-button bg-white border border-gray-200 rounded-lg px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors duration-200 ease-in-out transform hover:scale-105">
                    <i class="fa-solid fa-coral"></i> Amed
                </button>
                
              
            </div>
        </div>
    </div>
    </section>
</main>
<script>
    function openChatWithTitle(title) {
        // Assumptions: `chatIsOpen`, `toggleChat()`, and `addMessage()` exist from your main chat script.
        if (typeof chatIsOpen !== 'undefined' && !chatIsOpen) {
            if (typeof toggleChat === 'function') {
                toggleChat();
            }
        }

        const userMessage = `Hello, I'm interested in "${title}".`;
        
        if (typeof addMessage === 'function') {
            addMessage(userMessage, false); // `false` for a user message
        }

        setTimeout(() => {
            // Create the WhatsApp message with the relevant title
            const waMessage = encodeURIComponent(`Hello, I'd like to ask for more information about "${title}".`);
            const waURL = `https://wa.me/6287861190174?text=${waMessage}`;

            // Create the polite bot response with an inline WhatsApp button
            const botResponse = `
                Of course! We'd be happy to provide more information about "${title}". 
                For a faster response, please feel free to contact us directly on WhatsApp:<br> 
                <a href="${waURL}"  rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 8px; margin-left: 5px; padding: 8px 16px; background-color: #25D366; color: white; text-decoration: none; border-radius: 20px; font-weight: 500; font-size: 14px; vertical-align: middle;">
                    <svg xmlns="http://www.w3.org/2000/svg" heightmant="1em" viewBox="0 0 448 512" fill="white" style="vertical-align: middle;"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                    <span>Chat on WhatsApp</span>
                </a>
            `;

            if (typeof addMessage === 'function') {
                addMessage(botResponse, true); // `true` for a bot message
            }
            
            const userInput = document.getElementById('userInput');
            if(userInput) {
                userInput.focus();
            }
        }, 1000);
    }
</script>
