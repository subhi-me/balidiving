<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bali Diving - Scuba Diving Adventures in Bali | Best Dive Sites</title>
    <link rel="icon" href="images/bali-diving-logo.svg" type="image/svg+xml">
    <meta name="description" content="Experience world-class scuba diving in Bali with Bali Diving. Explore vibrant coral reefs, encounter manta rays, and discover underwater wonders. Book your diving adventure today!">
    <meta name="keywords" content="Bali diving, scuba diving Bali, dive sites Bali, underwater adventure, coral reefs, manta rays, diving tours">
    
    <script src="https://cdn.tailwindcss.com"></script>
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
    <style>
        .wave-animation {
            animation: wave 3s ease-in-out infinite;
        }
        @keyframes wave {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .gradient-primary {
            background: linear-gradient(135deg, #3552c8 0%, #0070d3 50%, #23a0b4 100%);
        }
        .gradient-secondary {
            background: linear-gradient(135deg, #f23d4e 0%, #eebe35 100%);
        }
        .gradient-ocean {
            background: linear-gradient(135deg, #0070d3 0%, #a2d2fa 50%, #23a0b4 100%);
        }
    </style>
</head>
<body class="font-sans">

    <nav class="fixed top-0 w-full bg-navy backdrop-blur-sm shadow-lg z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="nav-content" class="flex justify-between items-center h-16 transition-all duration-300">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 flex items-center justify-center">
                        <img src="images/bali-diving-logo.svg" width="80%" alt="Bali Diving Logo">
                    </div>
                    <span class="text-2xl font-bold text-white">Bali Diving</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="text-white hover:text-lightblue transition-colors">Home</a>
                    <a href="#experiences" class="text-white hover:text-lightblue transition-colors">Experiences</a>
                    <a href="#about" class="text-white hover:text-lightblue transition-colors">About</a>
                    <a href="#contact" class="text-white hover:text-lightblue transition-colors">Contact</a>
                </div>
                <button id="mobile-menu-btn" class="md:hidden text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-navy/95 border-t border-white/20">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#home" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Home</a>
                <a href="#experiences" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Experiences</a>
                <a href="#about" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">About</a>
                <a href="#contact" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Contact</a>
            </div>
        </div>
    </nav>

<?php include ('home.php')?>
<?php include ('main.php')?>
    <footer class="bg-navy text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 flex items-center justify-center"><img src="images/bali-diving-logo.svg" width="80%"></div>
                        <span class="text-2xl font-bold">Bali Diving</span>
                    </div>
                    <p class="text-lightblue">Bali's underwater paradise! From the majestic manta rays of Nusa Penida to the historic USAT Liberty wreck, we craft unforgettable diving experiences.</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-lightblue">
                        <li><a href="#home" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="#experiences" class="hover:text-white transition-colors">Experiences</a></li>
                        <li><a href="#about" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Dive Sites</h4>
                    <ul class="space-y-2 text-lightblue">
                        <li><a href="#" class="cursor-pointer hover:underline" data-site="manta-point">Manta Point</a></li>
                        <li><a href="#" class="cursor-pointer hover:underline" data-site="crystal-bay">Crystal Bay</a></li>
                        <li><a href="#" class="cursor-pointer hover:underline" data-site="usat-liberty">USAT Liberty Wreck</a></li>
                        <li><a href="#" class="cursor-pointer hover:underline" data-site="coral-garden">Coral Garden</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" aria-label="Instagram" class="text-white w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-accent transition-colors">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.65 7.2H16.66M8 20H16C18.2091 20 20 18.2091 20 16V8C20 5.79086 18.2091 4 16 4H8C5.79086 4 4 5.79086 4 8V16C4 18.2091 5.79086 20 8 20ZM15.75 12C15.75 14.0711 14.0711 15.75 12 15.75C9.92893 15.75 8.25 14.0711 8.25 12C8.25 9.92893 9.92893 8.25 12 8.25C14.0711 8.25 15.75 9.92893 15.75 12Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </a>
                        <a href="#" aria-label="Facebook" class="text-white w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-accent transition-colors">
                           <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-primary mt-8 pt-8 text-center text-lightblue">
                <p>&copy; <span id="current-year"></span> Bali Diving. All rights reserved. | Professional PADI Dive Center</p>
            </div>
        </div>
    </footer>

    <div id="chatbox-container" class="fixed bottom-6 right-6 z-50">
        <button id="chat-toggle" class="w-16 h-16 gradient-secondary rounded-full shadow-lg flex items-center justify-center text-white text-2xl hover:shadow-xl transform hover:scale-105 transition-all duration-300">💬</button>
        <div id="chat-window" class="hidden absolute bottom-20 right-0 w-80 bg-white rounded-2xl shadow-2xl border border-lightblue/20 overflow-hidden">
            <div class="gradient-primary p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 flex items-center justify-center"><img src="images/bali-diving-logo.svg" width="80%"></div>
                        <div>
                            <h3 class="font-bold">Customer Service</h3>
                            <p class="text-xs text-lightblue">Online on WhatsApp</p>
                        </div>
                    </div>
                    <button id="chat-close" class="text-white hover:text-lightblue transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            <div id="chat-messages" class="h-64 overflow-y-auto p-4 space-y-3 bg-gray-50">
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 gradient-primary rounded-full flex items-center justify-center flex-shrink-0"><span class="text-white text-sm">🤿</span></div>
                    <div class="bg-white rounded-lg p-3 shadow-sm max-w-xs">
                        <p id="initial-chat-message" class="text-sm text-navy"></p>
                        <span class="text-xs text-gray-500 mt-1 block">less 5 mins ago</span>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-lightblue/20">
                <div class="flex space-x-2">
                    <input type="text" id="chat-input" placeholder="Type your message..." class="flex-1 px-3 py-2 border border-lightblue/30 rounded-full text-sm focus:outline-none focus:border-primary">
                    <button id="chat-send" class="w-10 h-10 gradient-secondary rounded-full flex items-center justify-center text-white hover:shadow-lg transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2 text-center">Typically reply in <45 mins on WhatsApp.</p>
            </div>
        </div>
    </div>

    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 hidden z-50">
        <div id="lightbox-container" class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-full overflow-y-auto relative">
            <button id="lightbox-close" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div>
                <img id="lightbox-image" src="" alt="Dive Site Image" class="w-full h-64 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 id="lightbox-title" class="text-3xl font-bold text-navy mb-3"></h3>
                    <p id="lightbox-description" class="text-gray-700"></p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // --- Navbar Scroll Effect ---
        const navElement = document.querySelector('nav');
        const navContent = document.getElementById('nav-content');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navElement.classList.add('bg-navy/70', 'shadow-2xl');
                navContent.classList.remove('h-16');
                navContent.classList.add('h-12');
            } else {
                navElement.classList.remove('bg-navy/70', 'shadow-2xl');
                navContent.classList.remove('h-12');
                navContent.classList.add('h-16');
            }
        });

        // --- Mobile Menu Toggle ---
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => document.getElementById('mobile-menu').classList.add('hidden'));
        });

        // --- Smooth Scrolling ---
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
        }

        // --- Hero Slideshow ---
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;
        function showSlide(index) {
            slides.forEach((slide, i) => (slide.style.opacity = i === index ? '1' : '0'));
        }
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }
        if(slides.length > 1) setInterval(nextSlide, 5000);

        // --- Chatbox Functionality ---
        const chatToggle = document.getElementById('chat-toggle');
        const chatWindow = document.getElementById('chat-window');
        const chatClose = document.getElementById('chat-close');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');
        const chatMessages = document.getElementById('chat-messages');

        chatToggle.addEventListener('click', () => {
            chatWindow.classList.toggle('hidden');
            if (!chatWindow.classList.contains('hidden')) chatInput.focus();
        });
        chatClose.addEventListener('click', () => chatWindow.classList.add('hidden'));

        function sendMessage() {
            const message = chatInput.value.trim();
            if (!message) return;
            const userMessage = document.createElement('div');
            userMessage.className = 'flex items-start space-x-2 justify-end';
            userMessage.innerHTML = `<div class="bg-primary text-white rounded-lg p-3 shadow-sm max-w-xs"><p class="text-sm">${message}</p><span class="text-xs text-lightblue mt-1 block">Just now</span></div><div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0"><span class="text-sm">👤</span></div>`;
            chatMessages.appendChild(userMessage);
            chatInput.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;
            setTimeout(() => {
                const autoReply = document.createElement('div');
                autoReply.className = 'flex items-start space-x-2';
                autoReply.innerHTML = `<div class="w-8 h-8 gradient-primary rounded-full flex items-center justify-center flex-shrink-0"><span class="text-white text-sm">🤿</span></div><div class="bg-white rounded-lg p-3 shadow-sm max-w-xs"><p class="text-sm text-navy"></p><span class="text-xs text-gray-500 mt-1 block">Just now</span></div>`;
                chatMessages.appendChild(autoReply);
                const replyElement = autoReply.querySelector('.text-sm.text-navy');
                const replyChunks = ["Thanks so ", "much for ", "your message! ", "I've gone ", "ahead and ", "set up ", "a direct ", "WhatsApp link ", "for you ", "so we ", "can chat ", "more easily.<br><br>", `<a href="https://wa.me/6287861190174?text=Hi, I was on your website and have a question." class="text-primary font-semibold hover:underline" target="_blank">Click to Chat on WhatsApp</a>`, "<br><br>Talk to ", "you soon!"];
                typewriterEffect(replyElement, replyChunks, 150);
            }, 1000);
        }

        function typewriterEffect(element, chunks, speed = 150) {
            let i = 0;
            element.innerHTML = '';
            const cursor = document.createElement('span');
            cursor.style.animation = 'blink 1s step-end infinite';
            cursor.style.fontWeight = 'bold';
            cursor.innerHTML = '▋';
            element.appendChild(cursor);
            const typing = setInterval(() => {
                if (i < chunks.length) {
                    cursor.insertAdjacentHTML('beforebegin', chunks[i]);
                    i++;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } else {
                    clearInterval(typing);
                    cursor.remove();
                }
            }, speed);
        }

        chatSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') sendMessage(); });
        
        // --- General DOMContentLoaded Listener ---
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('current-year').textContent = new Date().getFullYear();
            
            // Typewriter for initial chat message
            const initialMessageElement = document.getElementById('initial-chat-message');
            if (initialMessageElement) {
                const initialMessageChunks = ["Hello! Welcome ", "to Bali ", "Diving! 🌊 ", "We're excited ", "to help ", "you discover ", "the amazing ", "underwater world ", "of Bali. ", "How can ", "we assist ", "you today?"];
                const startTyping = () => { if (initialMessageElement.innerHTML.length < 10) typewriterEffect(initialMessageElement, initialMessageChunks, 150); };
                chatToggle.addEventListener('click', startTyping, { once: true });
            }

            // Countdown Timer
            const updateCountdown = () => {
                const now = new Date();
                const utc8Offset = 8 * 60, localOffset = now.getTimezoneOffset();
                const currentTime = new Date(now.getTime() + (utc8Offset + localOffset) * 60000);
                const target = new Date(currentTime);
                target.setHours(16, 0, 0, 0);
                if (currentTime > target) target.setDate(target.getDate() + 1);
                const diff = target - currentTime;
                const hours = Math.floor(diff / 3600000).toString().padStart(2, '0');
                const minutes = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
                const seconds = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
                if(document.getElementById('hours')) document.getElementById('hours').textContent = hours;
                if(document.getElementById('minutes')) document.getElementById('minutes').textContent = minutes;
                if(document.getElementById('seconds')) document.getElementById('seconds').textContent = seconds;
            };
            updateCountdown();
            setInterval(updateCountdown, 1000);

            // Lightbox
            const siteData = {
                'manta-point': { title: 'Manta Point', imageUrl: 'images/manta-point.jpg', description: 'Experience the breathtaking dance of majestic Manta Rays at their famous cleaning station. A truly unforgettable encounter for divers of all levels.' },
                'crystal-bay': { title: 'Crystal Bay', imageUrl: 'images/crystal.jpg', description: 'Famous for its crystal-clear visibility and vibrant coral reefs. Between July and October, it becomes the best place to spot the elusive and massive Mola Mola (Oceanic Sunfish).' },
                'usat-liberty': { title: 'USAT Liberty Wreck', imageUrl: 'images/usat.jpg', description: 'Explore one of the most famous shipwrecks in the world. This WWII cargo ship is now a spectacular artificial reef, fully encrusted with coral and home to thousands of fish.' },
                'coral-garden': { title: 'Coral Garden', imageUrl: 'images/coral.jpg', description: 'A stunning underwater garden located near the Liberty Wreck. This shallow reef is teeming with a huge variety of corals, anemones, and colorful reef fish.' }
            };
            const lightbox = document.getElementById('lightbox'), lightboxClose = document.getElementById('lightbox-close');
            const openLightbox = (siteKey) => {
                const data = siteData[siteKey];
                if (data) {
                    document.getElementById('lightbox-title').textContent = data.title;
                    document.getElementById('lightbox-image').src = data.imageUrl;
                    document.getElementById('lightbox-description').textContent = data.description;
                    lightbox.classList.remove('hidden');
                }
            };
            const closeLightbox = () => lightbox.classList.add('hidden');
            document.querySelectorAll('[data-site]').forEach(link => link.addEventListener('click', e => { e.preventDefault(); openLightbox(link.dataset.site); }));
            lightboxClose.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });
        });
    </script>
</body>
</html>