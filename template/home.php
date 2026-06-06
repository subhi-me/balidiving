    <section id="home" class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0">
            <div id="slideshow" class="w-full h-full relative">
                <div class="slide absolute inset-0 opacity-100 transition-opacity duration-1000">
                    <div class="w-full h-full bg-gradient-to-br from-primary via-accent to-teal"></div>
                </div>
                <div class="slide absolute inset-0 opacity-0 transition-opacity duration-1000">
                    <div class="w-full h-full bg-gradient-to-br from-accent via-teal to-primary"></div>
                </div>
                <div class="slide absolute inset-0 opacity-0 transition-opacity duration-1000">
                    <div class="w-full h-full bg-gradient-to-br from-navy via-primary to-accent"></div>
                </div>
            </div>
            <div class="absolute inset-0 bg-black/40"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="fade-in">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight drop-shadow-lg">
                    Dive Into <span class="text-gold">Paradise</span>
                </h1>
                <p class="text-xl md:text-2xl text-white mb-8 max-w-3xl mx-auto drop-shadow-md">
                    Discover Bali's underwater wonders with professional guides. Experience vibrant coral reefs, majestic manta rays, and crystal-clear waters.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <button onclick="scrollToSection('experiences')" class="gradient-secondary text-white px-8 py-4 rounded-full text-lg font-semibold hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        🌊 Book Your Adventure
                    </button>
                    <button onclick="scrollToSection('about')" class="bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white/30 transition-all duration-300 border border-white/30">
                        Learn More
                    </button>
                </div>
            </div>
        </div>
    </section>