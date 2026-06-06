<?php include('template/footer.php')?>
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 hidden z-[60]">
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

    <div class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] z-40 hidden" id="chatWidget">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <img src="bali-diving-logo.svg">
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-white">Diving Expert</h1>
                            <p class="text-sm text-blue-100 flex items-center">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                Online now
                            </p>
                        </div>
                    </div>
                    <button onclick="toggleChat()" class="text-white hover:text-blue-200 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="bg-white" style="height: 400px; overflow-y: auto;" id="chatContainer">
                <div class="p-4 space-y-3" id="chatMessages">
                    </div>
            </div>
            <div class="bg-gray-50 p-4 border-t border-gray-100">
                <div class="flex space-x-2">
                    <input type="text" id="userInput" placeholder="Type your message..."
                           class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button onclick="sendMessage()" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full px-4 py-2 text-sm font-medium transition-colors">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed bottom-6 right-6 z-50" id="chatLauncher">
        <button onclick="toggleChat()"
                class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center group">
            <svg id="chatIcon" class="w-6 h-6 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
            </svg>
            <svg id="closeIcon" class="w-6 h-6 transition-transform duration-300 hidden" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
            <span class="ml-2 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap" id="launcherText">
                Chat with us
            </span>
        </button>
    </div>
    
    <script>
        // --- Original Page Script ---
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

        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => document.getElementById('mobile-menu').classList.add('hidden'));
        });

        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('current-year').textContent = new Date().getFullYear();
            
            const siteData = {
                'manta-point': { title: 'Manta Point', imageUrl: 'template/images/manta-point.jpg', description: 'Experience the breathtaking dance of majestic Manta Rays at their famous cleaning station. A truly unforgettable encounter for divers of all levels.' },
                'crystal-bay': { title: 'Crystal Bay', imageUrl: 'template/images/crystal.jpg', description: 'Famous for its crystal-clear visibility and vibrant coral reefs. Between July and October, it becomes the best place to spot the elusive and massive Mola Mola (Oceanic Sunfish).' },
                'usat-liberty': { title: 'USAT Liberty Wreck', imageUrl: 'template/images/usat.jpg', description: 'Explore one of the most famous shipwrecks in the world. This WWII cargo ship is now a spectacular artificial reef, fully encrusted with coral and home to thousands of fish.' },
                'coral-garden': { title: 'Coral Garden', imageUrl: 'template/images/coral.jpg', description: 'A stunning underwater garden located near the Liberty Wreck. This shallow reef is teeming with a huge variety of corals, anemones, and colorful reef fish.' }
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
    
    <script>
        // Menggunakan PHP untuk mendapatkan daftar file secara dinamis
        <?php
            $files = glob('template/images/slider/*.webp');
            $imagePaths = [];
            foreach ($files as $file) {
                // Hapus 'images/slider/' dari path
                $imagePaths[] = basename($file);
            }
            $imageNamesJSON = json_encode($imagePaths);
            echo "const imageNames = " . $imageNamesJSON . ";";
        ?>

        document.addEventListener('DOMContentLoaded', () => {
            const sliderContainer = document.getElementById('hero-slider');
            const shuffledImages = [...imageNames].sort(() => 0.5 - Math.random());
            let currentIndex = 0;

            // Memuat semua gambar secara dinamis
            const preloadImages = () => {
                if (shuffledImages.length === 0) {
                    console.error("Tidak ada gambar .webp yang ditemukan di folder 'images/slider'.");
                    return;
                }
                
                shuffledImages.forEach(imageName => {
                    const img = document.createElement('img');
                    img.src = `images/slider/${imageName}`;
                    img.alt = 'Underwater background image';
                    img.classList.add('hero-background-image', 'absolute', 'inset-0');
                    sliderContainer.appendChild(img);
                });
            };

            const images = sliderContainer.getElementsByClassName('hero-background-image');

            const startSlider = () => {
                if (images.length === 0) return;
                
                images[0].classList.add('active');
                
                setInterval(() => {
                    images[currentIndex].classList.remove('active');
                    currentIndex = (currentIndex + 1) % images.length;
                    images[currentIndex].classList.add('active');
                }, 5000); // Ganti gambar setiap 5 detik
            };

            preloadImages();
            startSlider();
        });
    </script>

    <?php include ('template/chat.php') ?>
</body>
</html>