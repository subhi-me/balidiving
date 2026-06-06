<style>
    /* ===============================================
   AUTO HIDE TEXT "Cart" ON NARROW SCREEN
   (Before mobile menu activates)
   =============================================== */
    @media (max-width: 720px) {
        #openCartBtn span {
            display: none;
        }
    }
</style>

<nav class="fixed top-0 w-full bg-navy backdrop-blur-sm shadow-lg z-[21] transition-all duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div id="nav-content" class="flex justify-between items-center h-16 transition-all duration-300">

            <!-- Logo + Title -->
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 flex items-center justify-center">
                    <a href="https://balidiving.com">
                        <img src="../images/bali-diving-logo.svg" width="80%" alt="Bali Diving Logo">
                    </a>
                </div>
                <a href="https://balidiving.com">
                    <h3 class="text-2xl font-bobalild text-white">Bali Diving</h3>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-6 items-center">

                <a href="https://www.balidiving.com/snorkeling"
                    class="text-white hover:text-lightblue transition-colors">Snorkeling</a>

                <a href="https://www.balidiving.com/try-scuba-diving"
                    class="text-white hover:text-lightblue transition-colors">Try Diving</a>

                <a href="https://www.balidiving.com/discover-scuba-diving-in-bali"
                    class="text-white hover:text-lightblue transition-colors">Go Diving</a>

                <a href="https://www.balidiving.com/scuba-diving-certification"
                    class="text-white hover:text-lightblue transition-colors">Learn Diving</a>

                <a href="https://balidiving.com/pricelist"
                    class="text-white hover:text-lightblue transition-colors">Pricelist</a>

                <!-- Desktop Offcanvas Trigger -->
                <button type="button" onclick="toggleDesktopOffcanvas()"
                    class="text-white hover:text-lightblue transition-colors flex items-center" aria-label="Open Menu">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>


            </div>

            <!-- Mobile Button (ID TETAP, JS GLOBAL AKAN KENA) -->
            <button id="mobile-menu-btn" class="md:hidden text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>

        </div>
    </div>

    <!-- Mobile Menu (ID & STRUKTUR TETAP) -->
    <div id="mobile-menu" class="hidden md:hidden bg-navy/95 border-t border-white/20">
        <div class="px-2 pt-2 pb-3 space-y-1">

            <a href="https://www.balidiving.com/snorkeling"
                class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Snorkeling</a>

            <a href="https://www.balidiving.com/try-scuba-diving"
                class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Try Diving</a>

            <a href="https://www.balidiving.com/discover-scuba-diving-in-bali"
                class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Go Diving</a>

            <a href="https://www.balidiving.com/scuba-diving-certification"
                class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Learn Diving</a>

            <a href="https://balidiving.com/pricelist"
                class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Pricelist</a>

            <!-- Mobile Cart (REPLACE Mobile Chat) -->
            <a href="https://balidiving.com/cart/my-booking" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md
                      mt-2 border-t border-white/20">
                <i class="fas fa-clipboard-list"> </i>
                <span> My Booking</span>
            </a>

        </div>
    </div>

</nav>

<!-- Desktop Offcanvas Menu -->
<div id="desktop-offcanvas"
    class="fixed inset-y-0 right-0 max-w-sm w-full bg-navy border-l border-white/20 shadow-2xl z-[60] transform translate-x-full transition-transform duration-300 ease-in-out hidden md:flex flex-col">
    <div class="px-6 py-5 flex items-center justify-between border-b border-white/10">
        <h2 class="text-xl font-bold text-white">Menu</h2>
        <button onclick="toggleDesktopOffcanvas()"
            class="text-white hover:text-red-400 focus:outline-none transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="p-6 flex-1 overflow-y-auto space-y-2">
        <!-- My Booking -->
        <a id="openCartBtn" href="https://balidiving.com/cart/my-booking"
            class="flex items-center space-x-4 text-white hover:bg-white/10 px-4 py-4 rounded-xl transition-all border border-white/5 hover:border-white/20 mb-2">
            <i class="fas fa-clipboard-list text-xl text-lightblue w-6 text-center"></i>
            <span class="font-medium text-lg">My Booking</span>
        </a>

        <div class="h-px bg-white/10 my-4"></div>

        <a href="https://balidiving.com/weather"
            class="flex items-center space-x-4 text-white/90 hover:text-white hover:bg-white/10 px-4 py-3 rounded-xl transition-all">
            <i class="fas fa-cloud-sun text-lg text-lightblue w-6 text-center opacity-80"></i>
            <span class="font-medium">Weather</span>
        </a>

        <a href="https://balidiving.com/blog"
            class="flex items-center space-x-4 text-white/90 hover:text-white hover:bg-white/10 px-4 py-3 rounded-xl transition-all">
            <i class="fas fa-newspaper text-lg text-lightblue w-6 text-center opacity-80"></i>
            <span class="font-medium">Blog</span>
        </a>

        <a href="https://balidiving.com/recommendations"
            class="flex items-center space-x-4 text-white/90 hover:text-white hover:bg-white/10 px-4 py-3 rounded-xl transition-all">
            <i class="fas fa-star text-lg text-yellow-400 w-6 text-center opacity-80"></i>
            <span class="font-medium">Recommendations</span>
        </a>

        <div class="h-px bg-white/10 my-4"></div>

        <a href="https://balidiving.com/login"
            class="flex items-center space-x-4 text-white/90 hover:text-white hover:bg-white/10 px-4 py-3 rounded-xl transition-all">
            <i class="fas fa-sign-in-alt text-lg text-lightblue w-6 text-center opacity-80"></i>
            <span class="font-medium">Login</span>
        </a>
    </div>
</div>

<!-- Overlay for Desktop Offcanvas -->
<div id="desktop-offcanvas-overlay"
    class="fixed inset-0 bg-black/60 z-[50] hidden transition-opacity duration-300 opacity-0 backdrop-blur-sm"
    onclick="toggleDesktopOffcanvas()"></div>

<script>
    function toggleDesktopOffcanvas() {
        const offcanvas = document.getElementById('desktop-offcanvas');
        const overlay = document.getElementById('desktop-offcanvas-overlay');

        if (offcanvas.classList.contains('translate-x-full')) {
            // Open
            overlay.classList.remove('hidden');

            // Allow display change to propagate before animating opacity/transform
            requestAnimationFrame(() => {
                offcanvas.classList.remove('translate-x-full');
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
            });
            document.body.style.overflow = 'hidden';
        } else {
            // Close
            offcanvas.classList.add('translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');

            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
            document.body.style.overflow = '';
        }
    }
</script>