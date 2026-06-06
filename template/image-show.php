
  <style>
        body {
            box-sizing: border-box;
        }
        
        .slider-container {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }
        
        .slider-container::before {
            content: 'BaliDiving.com';
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 20;
        }
        
        .slider-container::after {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid rgba(0, 0, 0, 0.8);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 20;
        }
        
        .slider-container:hover::before,
        .slider-container:hover::after {
            opacity: 1;
        }
        
        .slider-track {
            display: flex;
            transition: transform 0.3s ease-in-out;
        }
        
        .slide {
            min-width: 100%;
            aspect-ratio: 4/3;
            background: linear-gradient(135deg, #0077be 0%, #004d7a 50%, #002a42 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            animation: kenBurns 8s ease-in-out infinite alternate;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        @keyframes kenBurns {
            0% {
                transform: scale(1) rotate(0deg);
            }
            100% {
                transform: scale(1.1) rotate(1deg);
            }
        }
        
        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(0, 191, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 30%);
            animation: lightShimmer 6s ease-in-out infinite;
        }
        
        @keyframes lightShimmer {
            0%, 100% {
                opacity: 0.7;
                transform: translateX(-10px);
            }
            50% {
                opacity: 1;
                transform: translateX(10px);
            }
        }
        

        
        @keyframes float {
            0%, 100% {
                transform: translate(-50%, -50%) translateY(0px);
            }
            50% {
                transform: translate(-50%, -50%) translateY(-10px);
            }
        }
        
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: particleFloat 4s linear infinite;
        }
        
        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0px);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-10px) translateX(20px);
                opacity: 0;
            }
        }
        
        .slider-container:hover .slide {
            animation-play-state: paused;
        }
        
        .nav-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
            z-index: 10;
        }
        
        .nav-button:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
        }
        
        .nav-button:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .nav-button.prev {
            left: 12px;
        }
        
        .nav-button.next {
            right: 12px;
        }
        
        .nav-button:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            transform: translateY(-50%) scale(1);
        }
        
        .page-indicators {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
        }
        
        .page-number {
            min-width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #f0f2f5;
            color: #65676b;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .page-number:hover {
            background: #e4e6ea;
        }
        
        .page-number.active {
            background: #1877f2;
            color: white;
        }
        
        .page-number.active:hover {
            background: #166fe5;
        }
        
        .slide-counter {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 500;
            z-index: 10;
        }
    </style>
  <style>@view-transition { navigation: auto; }</style>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
 </head>
 <body class="h-full m-0 p-0">
  <main class="h-full flex items-center justify-center p-4">
   <div class="w-full max-w-2xl">
    <div class="slider-container" id="sliderContainer">
     <div class="slider-track" id="sliderTrack">
      <div class="slide" style="background-image: url('https://balidiving.com/images/gallery/2.jpg');">
       <div class="particles" id="particles1"></div>
      </div>
      <div class="slide" style="background-image: url('https://balidiving.com/images/gallery/3.jpg');">
       <div class="particles" id="particles2"></div>
      </div>
      <div class="slide" style="background-image: url('https://balidiving.com/images/gallery/jay.jpg');">
       <div class="particles" id="particles3"></div>
      </div>
      <div class="slide" style="background-image: url('https://balidiving.com/images/gallery/mn.jpg');">
       <div class="particles" id="particles4"></div>
      </div>
      <div class="slide" style="background-image: url('https://balidiving.com/images/gallery/tulamben.jpg');">
       <div class="particles" id="particles5"></div>
      </div>
     </div><button class="nav-button prev" id="prevBtn" aria-label="Previous photo">‹</button> <button class="nav-button next" id="nextBtn" aria-label="Next photo">›</button>
    </div>
    <div class="page-indicators" id="pageIndicators"><button class="page-number active" data-slide="0">1</button> <button class="page-number" data-slide="1">2</button> <button class="page-number" data-slide="2">3</button> <button class="page-number" data-slide="3">4</button> <button class="page-number" data-slide="4">5</button>
    </div>
   </div>
  </main>
  <script>
        const defaultConfig = {
            photo_count: "5"
        };

        let currentSlide = 0;
        let totalSlides = parseInt(defaultConfig.photo_count);

        function updateSlider() {
            const track = document.getElementById('sliderTrack');
            const pageNumbers = document.querySelectorAll('.page-number');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            // Update track position
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            // Update page numbers
            pageNumbers.forEach((pageNumber, index) => {
                pageNumber.classList.toggle('active', index === currentSlide);
            });
            
            // Update navigation buttons
            prevBtn.disabled = currentSlide === 0;
            nextBtn.disabled = currentSlide === totalSlides - 1;
        }

        function createParticles(container) {
            for (let i = 0; i < 15; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 4 + 's';
                particle.style.animationDuration = (3 + Math.random() * 2) + 's';
                container.appendChild(particle);
            }
        }

        function createSlides(count) {
            const track = document.getElementById('sliderTrack');
            const pageIndicatorsContainer = document.getElementById('pageIndicators');
            
            const imageUrls = [
                'https://balidiving.com/images/gallery/2.jpg',
                'https://balidiving.com/images/gallery/3.jpg',
                'https://balidiving.com/images/gallery/jay.jpg',
                'https://balidiving.com/images/gallery/mn.jpg',
                'https://balidiving.com/images/gallery/tulamben.jpg'
            ];
            
            // Clear existing slides and page numbers
            track.innerHTML = '';
            pageIndicatorsContainer.innerHTML = '';
            
            // Create slides
            for (let i = 0; i < count; i++) {
                const slide = document.createElement('div');
                slide.className = 'slide';
                
                // Use cycling images if count exceeds available images
                const imageUrl = imageUrls[i % imageUrls.length];
                slide.style.backgroundImage = `url('${imageUrl}')`;
                
                const particles = document.createElement('div');
                particles.className = 'particles';
                particles.id = `particles${i + 1}`;
                slide.appendChild(particles);
                
                track.appendChild(slide);
                
                // Create particles for this slide
                createParticles(particles);
                
                const pageNumber = document.createElement('button');
                pageNumber.className = `page-number ${i === 0 ? 'active' : ''}`;
                pageNumber.setAttribute('data-slide', i);
                pageNumber.textContent = i + 1;
                pageNumber.addEventListener('click', () => goToSlide(i));
                pageIndicatorsContainer.appendChild(pageNumber);
            }
            
            totalSlides = count;
            currentSlide = 0;
            updateSlider();
        }

        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            updateSlider();
        }

        function nextSlide() {
            if (currentSlide < totalSlides - 1) {
                currentSlide++;
                updateSlider();
            }
        }

        function prevSlide() {
            if (currentSlide > 0) {
                currentSlide--;
                updateSlider();
            }
        }

        // Event listeners
        document.getElementById('nextBtn').addEventListener('click', nextSlide);
        document.getElementById('prevBtn').addEventListener('click', prevSlide);

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') prevSlide();
            if (e.key === 'ArrowRight') nextSlide();
        });

        // Touch/swipe support
        let startX = 0;
        let isDragging = false;

        document.getElementById('sliderContainer').addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
        });

        document.getElementById('sliderContainer').addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
        });

        document.getElementById('sliderContainer').addEventListener('touchend', (e) => {
            if (!isDragging) return;
            
            const endX = e.changedTouches[0].clientX;
            const diff = startX - endX;
            
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
            
            isDragging = false;
        });

        // Element SDK integration
        async function onConfigChange(config) {
            const photoCount = parseInt(config.photo_count) || parseInt(defaultConfig.photo_count);
            const validCount = Math.max(1, Math.min(10, photoCount));
            
            if (validCount !== totalSlides) {
                createSlides(validCount);
            }
        }

        function mapToCapabilities(config) {
            return {
                recolorables: [],
                borderables: [],
                fontEditable: undefined,
                fontSizeable: undefined
            };
        }

        function mapToEditPanelValues(config) {
            return new Map([
                ["photo_count", config.photo_count || defaultConfig.photo_count]
            ]);
        }

        // Initialize
        if (window.elementSdk) {
            window.elementSdk.init({
                defaultConfig,
                onConfigChange,
                mapToCapabilities,
                mapToEditPanelValues
            });
        }

        // Initial setup
        updateSlider();
        
        // Initialize particles for existing slides
        for (let i = 1; i <= 5; i++) {
            const particleContainer = document.getElementById(`particles${i}`);
            if (particleContainer) {
                createParticles(particleContainer);
            }
        }
    </script>
 