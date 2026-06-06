<?php
$page = 'services/try-scuba-diving';
include('01-start.php');

// ── Auto-scan gallery folders (all .webp files) ──────────────────
function scanGalleryFolder($folder) {
    $base = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/images/gallery/' . $folder . '/';
    $files = glob($base . '*.webp');
    if (!$files) return [];
    sort($files);
    return array_values(array_map(function($f) use ($folder) {
        return '/images/gallery/' . $folder . '/' . basename($f);
    }, $files));
}

$galleryData = [
    'tulamben'    => scanGalleryFolder('tulamben'),
    'padangbai'   => scanGalleryFolder('padangbai'),
    'amed'        => scanGalleryFolder('amed'),
    'nusa-penida' => scanGalleryFolder('nusa-penida'),
];
?>

<main class="flex-grow pt-16">

    <!-- Custom Styles for Animation -->
    <style>
        /* Enhanced Pan & Zoom */
        @keyframes pan-zoom {
            0% {
                transform: scale(1.1) translate(0, 0);
            }

            50% {
                transform: scale(1.25) translate(-2%, -1%);
            }

            100% {
                transform: scale(1.1) translate(0, 0);
            }
        }

        .animate-pan-zoom {
            animation: pan-zoom 30s ease-in-out infinite alternate;
            will-change: transform;
        }

        /* Underwater Light Caustics Effect */
        @keyframes sun-rays {
            0% {
                opacity: 0.1;
                transform: skewX(-20deg) translateX(0);
            }

            50% {
                opacity: 0.3;
                transform: skewX(-20deg) translateX(20px);
            }

            100% {
                opacity: 0.1;
                transform: skewX(-20deg) translateX(0);
            }
        }

        .sun-rays {
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.1) 20%,
                    transparent 40%,
                    rgba(255, 255, 255, 0.15) 60%,
                    transparent 100%);
            background-size: 200% 100%;
            animation: sun-rays 8s ease-in-out infinite;
        }

        /* Floating Bubbles/Particulates */
        @keyframes bubble-rise {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            20% {
                opacity: 0.5;
            }

            80% {
                opacity: 0.5;
            }

            100% {
                transform: translateY(-100vh) translateX(20px);
                opacity: 0;
            }
        }

        .bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            filter: blur(1px);
        }

        .text-glow {
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.6), 0 10px 20px rgba(0, 0, 0, 0.4);
        }
    </style>

    <!-- Immersive Hero Section -->
    <section
        class="relative min-h-[70vh] md:h-[80vh] flex flex-col justify-center items-center text-center text-white px-4 overflow-hidden bg-slate-900">

        <!-- Layer 1: Deep Ocean Background (Moved slower) -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <img src="https://balidiving.com/images/main/header/try-diving.jpg" alt="Snorkeling Hero"
                class="w-full h-full object-cover animate-pan-zoom opacity-90 transform-gpu">
        </div>

        <!-- Layer 2: Moving Light Rays (Caustics) -->
        <div class="absolute inset-0 z-1 pointer-events-none sun-rays h-full w-[150%] left-[-25%]"></div>

        <!-- Layer 3: Floating Particles/Bubbles -->
        <div class="absolute inset-0 z-1 pointer-events-none overflow-hidden">
            <div class="bubble w-2 h-2 left-[10%] bottom-[-20px]"
                style="animation: bubble-rise 15s linear infinite; animation-delay: 0s;"></div>
            <div class="bubble w-3 h-3 left-[20%] bottom-[-20px]"
                style="animation: bubble-rise 12s linear infinite; animation-delay: 2s;"></div>
            <div class="bubble w-1 h-1 left-[35%] bottom-[-20px]"
                style="animation: bubble-rise 18s linear infinite; animation-delay: 1s;"></div>
            <div class="bubble w-4 h-4 left-[50%] bottom-[-20px]"
                style="animation: bubble-rise 20s linear infinite; animation-delay: 5s;"></div>
            <div class="bubble w-2 h-2 left-[65%] bottom-[-20px]"
                style="animation: bubble-rise 14s linear infinite; animation-delay: 3s;"></div>
            <div class="bubble w-3 h-3 left-[80%] bottom-[-20px]"
                style="animation: bubble-rise 16s linear infinite; animation-delay: 7s;"></div>
            <div class="bubble w-1 h-1 left-[90%] bottom-[-20px]"
                style="animation: bubble-rise 19s linear infinite; animation-delay: 4s;"></div>
        </div>

        <!-- Layer 4: Depth & Color Grading Overlays -->
        <div class="absolute inset-0 z-1 pointer-events-none bg-blue-900/20 mix-blend-overlay"></div>
        <div
            class="absolute inset-0 z-1 pointer-events-none bg-gradient-to-b from-black/40 via-transparent to-black/80">
        </div>
        <div
            class="absolute inset-0 z-1 pointer-events-none bg-gradient-to-t from-blue-900/60 via-transparent to-transparent mix-blend-multiply">
        </div>

        <!-- Radiant Sun Glow at Top -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[60%] z-1 pointer-events-none opacity-60"
            style="background: radial-gradient(ellipse at top, rgba(200, 240, 255, 0.4) 0%, transparent 70%);">
        </div>

        <!-- Content (Highest Z-Index) -->
        <div class="relative z-20 max-w-5xl mx-auto space-y-4 md:space-y-8 animate-fade-in-up pt-8 md:pt-12">

            <div
                class="inline-flex items-center gap-2 py-2 px-4 md:px-5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs md:text-sm font-semibold tracking-wide mb-2 md:mb-4 shadow-xl text-blue-50 transition hover:bg-white/20">
                <i class="fas fa-certificate text-yellow-400 animate-pulse"></i> PADI 5 Star Dive Center
            </div>

            <h1
                class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black tracking-tight leading-none text-white text-glow drop-shadow-2xl px-2">
                Try Diving
            </h1>

            <p
                class="text-base sm:text-lg md:text-xl lg:text-2xl text-blue-50 max-w-3xl mx-auto font-medium leading-relaxed drop-shadow-lg text-glow opacity-95 px-4">
                No certification needed. Experience your first breath underwater <br class="hidden md:block">with Bali's
                most trusted PADI Instructors.
            </p>

            <div class="pt-4 md:pt-8 opacity-0 animate-[fadeInUp_1s_ease-out_0.5s_forwards]">
                <a href="#tours"
                    class="group relative inline-flex items-center justify-center px-8 md:px-12 py-4 md:py-5 text-base md:text-xl font-bold text-white transition-all duration-300 bg-blue-600 rounded-full hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/50 shadow-[0_0_20px_rgba(37,99,235,0.6)] hover:shadow-[0_0_40px_rgba(37,99,235,0.8)] hover:-translate-y-1 overflow-hidden">
                    <span
                        class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></span>
                    <span>Start Your Adventure</span>
                    <i class="fas fa-arrow-right ml-2 md:ml-3 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white relative overflow-hidden">
        <!-- Decorative Elements -->
        <div
            class="absolute top-0 left-0 w-64 h-64 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-70 -translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 right-0 w-64 h-64 bg-cyan-50 rounded-full mix-blend-multiply filter blur-3xl opacity-70 translate-x-1/2 translate-y-1/2">
        </div>

        <div class="container mx-auto px-6 max-w-5xl relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 tracking-tight">Your First Underwater
                    Breath</h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-blue-500 to-cyan-400 mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6 text-lg text-gray-600 leading-relaxed font-light">
                    <p>
                        <span class="font-semibold text-gray-900">Never dived before? Perfect!</span> The "Try Scuba
                        Diving" program is designed specifically for beginners. You don't need a certification to
                        explore the underwater world.
                    </p>
                    <p>
                        We prioritize your safety with a strict <span class="font-semibold text-blue-600">2:1
                            Student-to-Instructor ratio</span> (better than the standard 4:1). You'll receive a full
                        briefing, practice basic skills in shallow water, and then enjoy two supervised ocean dives to a
                        maximum depth of 12 meters.
                    </p>
                    <div class="p-6 bg-blue-50/50 rounded-2xl border border-blue-100 shadow-sm">
                        <h4 class="font-bold text-blue-800 mb-2 flex items-center"><i
                                class="fas fa-check-circle mr-2"></i> What's Included?</h4>
                        <p class="text-sm text-blue-900/80">
                            Includes <span class="font-medium">2 Ocean Dives, Private Instructor, Full Equipment, Lunch,
                                and Hotel Transfer</span>. Medically fit participants only.
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-cyan-400 rounded-3xl transform rotate-3 scale-105 opacity-20 blur-lg">
                    </div>
                    <img src="https://balidiving.com/images/main/mid/try-diving-kadek.jpg" alt="Underwater Exploration"
                        class="relative rounded-3xl shadow-2xl transform transition hover:scale-[1.01] duration-500">
                </div>
            </div>
        </div>
    </section>

    <section id="tours" class="py-24 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-blue-600 font-bold tracking-wider uppercase text-sm mb-2 block">Start Your
                    Adventure</span>
                <h3 class="text-4xl font-bold text-gray-900">Try Dive Packages</h3>
                <p class="mt-4 text-gray-500 max-w-2xl mx-auto">Choose from our top-rated beginner dive sites. No
                    certification required.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-center">

                <!-- Card 1: Tulamben -->
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full border border-gray-100">
                    <div class="relative h-64 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity">
                        </div>
                        <img src="https://balidiving.com/images/main/thumbnail/tulamben-01.jpg" alt="Tulamben Wreck"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <span
                            class="absolute top-4 right-4 z-20 bg-white/90 backdrop-blur text-blue-800 text-xs font-bold px-3 py-1 rounded-full shadow-sm">MOST
                            POPULAR</span>
                    </div>
                    <div class="p-6 flex-grow flex flex-col relative">
                        <h4 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition-colors">
                            Tulamben Shipwreck</h4>
                        <p class="text-gray-500 text-sm mb-6 flex-grow leading-relaxed font-light">
                            Dive the world-famous USAT Liberty Wreck. Seeing a WWII shipwreck on your very first dive is
                            an unforgettable experience.
                        </p>
                        <a href="https://balidiving.diversdesk.com/product/ba944d00-feb3-400d-b46a-2d164654b7af"
                            target="_self" data-title="Tulamben Shipwreck"
                            data-image="https://balidiving.com/images/main/thumbnail/tulamben-01.jpg"
                            data-gallery="tulamben"
                            data-desc="Dive the world-famous USAT Liberty Wreck. Seeing a WWII shipwreck on your very first dive is an unforgettable experience. The wreck starts at just 5 meters depth, making it perfect for beginners. You'll be surrounded by schools of fish and might even see a turtle!"
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            Select
                        </a>
                    </div>
                </div>

                <!-- Card 2: Padang Bai -->
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full border border-gray-100">
                    <div class="relative h-64 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity">
                        </div>
                        <img src="https://balidiving.com/images/main/thumbnail/padangbai-01.jpg" alt="Padang Bai"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h4 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition-colors">
                            Padang Bai</h4>
                        <p class="text-gray-500 text-sm mb-6 flex-grow leading-relaxed font-light">
                            Famous for its white sandy bottoms and crystal-clear blue waters. A calm and relaxing spot
                            perfect for your first breaths underwater.
                        </p>
                        <a href="https://balidiving.diversdesk.com/product/6bf7bf55-c4da-44b9-b97e-09dd6a63cb55"
                            target="_self" data-title="Padang Bai Blue Lagoon"
                            data-image="https://balidiving.com/images/main/thumbnail/padangbai-01.jpg"
                            data-gallery="padangbai"
                            data-desc="Famous for its white sandy bottoms and crystal-clear blue waters. A calm and relaxing spot perfect for your first breaths underwater. You'll see turtles, blue-spotted rays, and vibrant coral bommies in a shallow, protected bay."
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            Select
                        </a>
                    </div>
                </div>

                <!-- Card 3: Amed -->
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full border border-gray-100">
                    <div class="relative h-64 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity">
                        </div>
                        <img src="https://balidiving.com/images/main/thumbnail/amed-01.jpg" alt="Amed"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h4 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition-colors">
                            Amed</h4>
                        <p class="text-gray-500 text-sm mb-6 flex-grow leading-relaxed font-light">
                            A serene traditional village offering a hidden gem for divers. Beautiful reefs and a relaxed
                            atmosphere away from the crowds.
                        </p>
                        <a href="https://balidiving.diversdesk.com/product/ac527d58-d2b0-4304-ae39-06028690c9c7"
                            target="_self" data-title="Amed Coast"
                            data-image="https://balidiving.com/images/main/thumbnail/amed-01.jpg"
                            data-gallery="amed"
                            data-desc="A serene traditional village offering a hidden gem for divers. Beautiful reefs and a relaxed atmosphere away from the crowds. The black volcanic sand makes the coral colors pop, and the calm bay is ideal for learning skills."
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            Select
                        </a>
                    </div>
                </div>

                <!-- Card 4: Nusa Penida -->
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full border border-gray-100">
                    <div class="relative h-64 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity">
                        </div>
                        <img src="https://balidiving.com/images/main/thumbnail/nusa-penida-01.jpg" alt="Nusa Penida"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <span
                            class="absolute top-4 right-4 z-20 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">MANTA
                            RAYS</span>
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h4 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition-colors">
                            Nusa Penida</h4>
                        <p class="text-gray-500 text-sm mb-6 flex-grow leading-relaxed font-light">
                            Experience your first dive with a chance to see majestic Manta Rays. An incredible adventure
                            in crystal-clear waters.
                        </p>
                        <a href="https://balidiving.diversdesk.com/product/78aa6dbe-daaf-4b8b-8c1d-3a392f214a9a"
                            target="_self" data-title="Nusa Penida Manta Point"
                            data-image="https://balidiving.com/images/main/thumbnail/nusa-penida-01.jpg"
                            data-gallery="nusa-penida"
                            data-desc="Experience your first dive with a chance to see majestic Manta Rays. An incredible adventure in crystal-clear waters. Note: Conditions can be a bit more challenging here, so it's an exciting step up if you're comfortable in the water!"
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            Select
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-20 relative overflow-hidden">
        <!-- Gradient Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 to-slate-900 z-0"></div>
        <!-- Overlay Pattern -->
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>

        <div class="container mx-auto px-4 text-center relative z-10">
            <h3 class="text-3xl md:text-4xl font-bold text-white mb-6">Create Your Custom Adventure</h3>
            <p class="text-blue-100 mb-10 max-w-2xl mx-auto text-lg font-light leading-relaxed">
                Have a specific itinerary in mind? We customize diving trips for groups and multi-day safaris. Let us
                design your dream underwater holiday.
            </p>
            <a href="#"
                class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-blue-900 transition-all duration-200 bg-white rounded-full hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white shadow-xl hover:-translate-y-1">
                Contact Us Today
            </a>
        </div>
    </section>
</main>

<!-- Offcanvas for Booking -->
<div id="bookingOffcanvas"
    class="fixed inset-y-0 right-0 z-[9999] w-full md:w-[500px] lg:w-[600px] bg-white shadow-2xl transform translate-x-full transition-transform duration-500 ease-[cubic-bezier(0.25,0.8,0.25,1)]">

    <!-- View 1: Details / Splash -->
    <div id="detailsView" class="flex flex-col h-full bg-white transition-opacity duration-300">
        <!-- Hero Image -->
        <div class="relative h-[40vh] shrink-0 overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent z-10"></div>
            <img id="detailImage" src="" alt="Detail"
                class="w-full h-full object-cover transform transition-transform duration-1000 group-hover:scale-105">

            <!-- Close Button -->
            <button onclick="closeOffcanvas()"
                class="absolute top-6 right-6 z-20 w-10 h-10 flex items-center justify-center rounded-full bg-black/20 text-white backdrop-blur-md hover:bg-white hover:text-red-500 transition-all duration-300 shadow-lg border border-white/10 group-hover:rotate-90">
                <i class="fas fa-times"></i>
            </button>

            <!-- Title & Badges -->
            <div
                class="absolute bottom-6 left-6 z-20 transform translate-y-0 transition-transform duration-500">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-600/90 text-white text-xs font-bold mb-3 backdrop-blur-sm shadow-[0_4px_10px_rgba(37,99,235,0.3)] border border-blue-400/30">
                    <i class="fas fa-star text-yellow-300 text-[10px]"></i> Premium Experience
                </div>
                <h3 id="detailTitle" class="text-3xl md:text-4xl font-bold text-white shadow-sm leading-tight"></h3>
            </div>
        </div>

        <!-- Content Body -->
        <div class="flex-1 flex flex-col p-8 relative overflow-y-auto">

            <!-- Gallery Button — below title -->
            <button id="galleryOpenBtn" onclick="openGalleryModal()"
                class="w-full mb-6 group flex items-center justify-center gap-2.5 px-5 py-3 rounded-2xl border-2 border-blue-100 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300 font-semibold text-sm shadow-sm hover:shadow-[0_4px_20px_rgba(37,99,235,0.3)]">
                <i class="fas fa-images text-lg group-hover:scale-110 transition-transform duration-300"></i>
                <span>View Photo Gallery</span>
                <span id="galleryBtnCount" class="ml-auto text-xs font-normal opacity-60 group-hover:opacity-100"></span>
            </button>

            <!-- Description -->
            <div class="prose prose-lg text-slate-600 leading-relaxed max-w-none">
                <p id="detailDesc"></p>
            </div>

            <!-- Features / Price Includes -->
            <div class="mt-8 space-y-4">
                <h4
                    class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-l-4 border-green-500 pl-3">
                    Price Includes</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Hotel Pickup -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3 shrink-0">
                            <i class="fas fa-shuttle-van text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Hotel Pickup</span>
                    </div>

                    <!-- Lunch Included -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div
                            class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mr-3 shrink-0">
                            <i class="fas fa-utensils text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Lunch Included</span>
                    </div>

                    <!-- Fast Boat -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div
                            class="w-8 h-8 rounded-full bg-cyan-100 flex items-center justify-center text-cyan-600 mr-3 shrink-0">
                            <i class="fas fa-ship text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Fast Boat</span>
                    </div>

                    <!-- Instructor -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div
                            class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mr-3 shrink-0">
                            <i class="fas fa-user-shield text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Private Instructor</span>
                    </div>

                    <!-- Gear Rental -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div
                            class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 mr-3 shrink-0">
                            <i class="fas fa-mask text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Full Equipment</span>
                    </div>

                    <!-- Porter & Permits -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div
                            class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mr-3 shrink-0">
                            <i class="fas fa-ticket-alt text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Porter & Permits</span>
                    </div>

                    <!-- Insurance -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100 md:col-span-2">
                        <div
                            class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-3 shrink-0">
                            <i class="fas fa-shield-alt text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Insurance (If applied)</span>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="mt-auto pt-8">
                <button id="proceedToBookingBtn"
                    class="w-full group relative overflow-hidden rounded-2xl bg-blue-600 p-4 transition-all duration-300 hover:bg-blue-700 hover:shadow-[0_10px_30px_rgba(37,99,235,0.4)] hover:-translate-y-1">
                    <div class="relative z-10 flex items-center justify-center gap-2">
                        <span class="text-lg font-bold text-white">Check Availability</span>
                        <i class="fas fa-arrow-right text-white transition-transform group-hover:translate-x-1"></i>
                    </div>
                    <!-- Shine Effect -->
                    <div
                        class="absolute inset-0 -translate-x-full group-hover:animate-shimmer bg-gradient-to-r from-transparent via-white/20 to-transparent z-0">
                    </div>
                </button>
                <p class="text-center text-slate-400 text-xs mt-4 flex items-center justify-center gap-1">
                    <i class="fas fa-shield-alt text-slate-300"></i> Secure booking powered by DiversDesk
                </p>
            </div>
        </div>
    </div>


    <!-- View 2: Booking / Iframe -->
    <div id="bookingView" class="hidden h-full flex flex-col bg-white">
        <!-- Simple Header -->
        <div class="flex items-center justify-between p-4 border-b border-slate-100 bg-white z-20 shadow-sm">
            <button onclick="showDetailsView()"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-blue-600 transition-colors text-sm font-medium px-2 py-1 rounded-lg hover:bg-slate-50">
                <i class="fas fa-arrow-left"></i> <span>Back</span>
            </button>
            <span class="font-bold text-slate-800 text-sm tracking-wide uppercase">Booking Request</span>
            <button onclick="closeOffcanvas()" class="text-slate-400 hover:text-red-500 transition-colors p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Loader -->
        <div id="iframeLoader" class="absolute inset-0 z-10 flex items-center justify-center bg-white">
            <div class="text-center">
                <div class="relative w-16 h-16 mx-auto mb-4">
                    <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
                    <div
                        class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin">
                    </div>
                </div>
                <p class="text-slate-500 font-medium animate-pulse">Connecting to booking system...</p>
            </div>
        </div>

        <!-- Iframe Container -->
        <div class="flex-1 relative w-full h-full bg-slate-50">
            <iframe id="bookingIframe" class="w-full h-full border-0 absolute inset-0"
                sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-popups-to-escape-sandbox"></iframe>
        </div>
    </div>
</div>

<!-- Overlay -->
<div id="offcanvasOverlay"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9998] hidden transition-all duration-500">
</div>

<!-- ===== PHOTO GALLERY MODAL ===== -->
<div id="galleryModal"
    class="fixed inset-0 z-[10001] hidden">
    <!-- Backdrop -->
    <div id="galleryBackdrop"
        class="absolute inset-0 bg-black/95 backdrop-blur-xl opacity-0 transition-opacity duration-400">
    </div>

    <!-- Modal Content -->
    <div id="galleryContent"
        class="relative z-10 flex flex-col h-full opacity-0 translate-y-4 transition-all duration-400">

        <!-- Header -->
        <div
            class="flex items-center justify-between px-6 py-4 border-b border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center border border-blue-400/30">
                    <i class="fas fa-images text-blue-300 text-sm"></i>
                </div>
                <div>
                    <h4 id="galleryTitle" class="text-white font-bold text-lg leading-tight"></h4>
                    <p id="galleryCount" class="text-white/50 text-xs"></p>
                </div>
            </div>
            <button onclick="closeGalleryModal()"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-red-500 hover:text-white transition-all duration-300 border border-white/10">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Loading State -->
        <div id="galleryLoader" class="flex-1 flex flex-col items-center justify-center gap-4">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-white/10 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-blue-400 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p class="text-white/60 text-sm animate-pulse">Loading photos...</p>
        </div>

        <!-- Grid -->
        <div id="galleryGrid"
            class="hidden flex-1 overflow-y-auto p-4 md:p-6">
            <div id="galleryGridInner"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            </div>
        </div>

        <!-- Empty State -->
        <div id="galleryEmpty" class="hidden flex-1 flex flex-col items-center justify-center gap-4 text-center px-6">
            <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center mb-2">
                <i class="fas fa-image-slash text-white/30 text-3xl"></i>
            </div>
            <p class="text-white/60 text-base">No photos found for this location.</p>
            <p class="text-white/30 text-sm">Check back soon — we're adding new photos regularly!</p>
        </div>
    </div>
</div>

<!-- ===== LIGHTBOX ===== -->
<div id="lightbox"
    class="fixed inset-0 z-[10002] hidden items-center justify-center bg-black/97">
    <!-- Close -->
    <button onclick="closeLightbox()"
        class="absolute top-4 right-4 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-red-500 transition-all z-10 border border-white/10">
        <i class="fas fa-times text-lg"></i>
    </button>
    <!-- Prev -->
    <button onclick="lightboxPrev()"
        class="absolute left-2 md:left-6 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/25 transition-all z-10 border border-white/10">
        <i class="fas fa-chevron-left text-lg"></i>
    </button>
    <!-- Next -->
    <button onclick="lightboxNext()"
        class="absolute right-2 md:right-6 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/25 transition-all z-10 border border-white/10">
        <i class="fas fa-chevron-right text-lg"></i>
    </button>
    <!-- Image -->
    <img id="lightboxImg"
        class="max-h-[90vh] max-w-[90vw] object-contain rounded-xl shadow-2xl transition-opacity duration-300"
        src="" alt="">
    <!-- Counter -->
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 backdrop-blur-md text-white text-sm px-4 py-1.5 rounded-full border border-white/10">
        <span id="lightboxCounter"></span>
    </div>
</div>

<script>
    let currentBookingUrl = '';
    let currentGalleryFolder = 'tulamben';
    let currentGalleryTitle = '';
    let galleryImages = [];
    let lightboxIndex = 0;

    // ─── Offcanvas ───────────────────────────────────────────────
    function openOffcanvas(url, title, desc, img, gallery) {
        currentBookingUrl = url;
        currentGalleryFolder = gallery || 'tulamben';
        currentGalleryTitle = title || 'Try Diving Adventure';

        // Populate Detail View
        document.getElementById('detailTitle').textContent = currentGalleryTitle;
        document.getElementById('detailDesc').textContent = desc || 'Experience the beauty of Bali underwater.';
        if (img) document.getElementById('detailImage').src = img;

        // Reset Views
        showDetailsView();

        // Update gallery button badge
        updateGalleryBtnCount(currentGalleryFolder);

        const offcanvas = document.getElementById('bookingOffcanvas');
        const overlay  = document.getElementById('offcanvasOverlay');

        overlay.classList.remove('hidden');
        requestAnimationFrame(() => {
            overlay.classList.add('opacity-100');
            offcanvas.classList.remove('translate-x-full');
        });

        document.body.style.overflow = 'hidden';
        if (typeof toggleChatVisibility === 'function') toggleChatVisibility(false);
    }

    function showDetailsView() {
        document.getElementById('detailsView').classList.remove('hidden');
        document.getElementById('bookingView').classList.add('hidden');
        document.getElementById('bookingIframe').src = 'about:blank';
    }

    function showBookingView() {
        document.getElementById('detailsView').classList.add('hidden');
        document.getElementById('bookingView').classList.remove('hidden');

        const iframe = document.getElementById('bookingIframe');
        const loader = document.getElementById('iframeLoader');
        loader.classList.remove('hidden');
        iframe.src = currentBookingUrl;
        iframe.onload = function () {
            setTimeout(() => loader.classList.add('hidden'), 500);
        };
    }

    function closeOffcanvas() {
        const offcanvas = document.getElementById('bookingOffcanvas');
        const overlay  = document.getElementById('offcanvasOverlay');

        offcanvas.classList.add('translate-x-full');
        overlay.classList.remove('opacity-100');
        setTimeout(() => {
            overlay.classList.add('hidden');
            document.getElementById('bookingIframe').src = 'about:blank';
        }, 500);

        document.body.style.overflow = '';
        if (typeof toggleChatVisibility === 'function') toggleChatVisibility(true);
    }

    // ─── Gallery Modal ────────────────────────────────────────────
    // PHP-scanned image lists (auto-detected from server .webp files)
    const GALLERY_DATA = <?php echo json_encode($galleryData, JSON_UNESCAPED_SLASHES); ?>;

    // Update button badge with photo count on offcanvas open
    function updateGalleryBtnCount(folder) {
        const imgs = GALLERY_DATA[folder] || [];
        const badge = document.getElementById('galleryBtnCount');
        if (badge) badge.textContent = imgs.length > 0 ? imgs.length + ' photos' : '';
    }

    function openGalleryModal() {
        const modal    = document.getElementById('galleryModal');
        const backdrop = document.getElementById('galleryBackdrop');
        const content  = document.getElementById('galleryContent');
        const loader   = document.getElementById('galleryLoader');
        const grid     = document.getElementById('galleryGrid');
        const empty    = document.getElementById('galleryEmpty');
        const gridInner= document.getElementById('galleryGridInner');
        const titleEl  = document.getElementById('galleryTitle');
        const countEl  = document.getElementById('galleryCount');

        // Reset state
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loader.classList.remove('hidden');
        grid.classList.add('hidden');
        empty.classList.add('hidden');
        gridInner.innerHTML = '';
        galleryImages = [];
        titleEl.textContent = currentGalleryTitle + ' — Photo Gallery';
        countEl.textContent = 'Loading...';

        // Animate in
        requestAnimationFrame(() => {
            backdrop.style.opacity = '1';
            content.style.opacity  = '1';
            content.style.transform = 'translateY(0)';
        });

        document.body.style.overflow = 'hidden';

        // Use PHP-scanned list — instant, no network probing needed
        const found = GALLERY_DATA[currentGalleryFolder] || [];
        loader.classList.add('hidden');

        if (found.length === 0) {
            empty.classList.remove('hidden');
            countEl.textContent = 'No photos found';
            return;
        }

        galleryImages = found;
        countEl.textContent = found.length + ' photos';
        grid.classList.remove('hidden');

        found.forEach((url, idx) => {
            const tile = document.createElement('div');
            tile.className = 'gallery-tile relative overflow-hidden rounded-xl cursor-pointer group/tile bg-white/5';
            tile.style.cssText = 'aspect-ratio:1; animation: galleryTileIn 0.4s ease forwards; animation-delay:' + (idx * 0.05) + 's; opacity:0;';
            // Full URL: prefix with site origin since PHP returns relative paths
            const fullUrl = url.startsWith('http') ? url : (window.location.origin + url);
            tile.innerHTML = `
                <img src="${fullUrl}" alt="Photo ${idx+1}" loading="lazy"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover/tile:scale-110">
                <div class="absolute inset-0 bg-black/0 group-hover/tile:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                    <i class="fas fa-expand text-white text-xl opacity-0 group-hover/tile:opacity-100 transition-opacity duration-300 drop-shadow-lg"></i>
                </div>`;
            tile.addEventListener('click', () => openLightbox(idx));
            gridInner.appendChild(tile);
        });
        // Update galleryImages with full URLs for lightbox
        galleryImages = found.map(u => u.startsWith('http') ? u : (window.location.origin + u));
    }

    function closeGalleryModal() {
        const modal    = document.getElementById('galleryModal');
        const backdrop = document.getElementById('galleryBackdrop');
        const content  = document.getElementById('galleryContent');

        backdrop.style.opacity  = '0';
        content.style.opacity   = '0';
        content.style.transform = 'translateY(16px)';

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 400);
    }

    // ─── Lightbox ─────────────────────────────────────────────────
    function openLightbox(idx) {
        lightboxIndex = idx;
        const lb  = document.getElementById('lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
        updateLightbox();
    }

    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
    }

    function lightboxPrev() {
        lightboxIndex = (lightboxIndex - 1 + galleryImages.length) % galleryImages.length;
        updateLightbox();
    }

    function lightboxNext() {
        lightboxIndex = (lightboxIndex + 1) % galleryImages.length;
        updateLightbox();
    }

    function updateLightbox() {
        const img = document.getElementById('lightboxImg');
        const ctr = document.getElementById('lightboxCounter');
        img.style.opacity = '0';
        setTimeout(() => {
            img.src = galleryImages[lightboxIndex];
            img.style.opacity = '1';
        }, 150);
        ctr.textContent = (lightboxIndex + 1) + ' / ' + galleryImages.length;
    }

    // ─── Init ─────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        // Overlay Click Close
        document.getElementById('offcanvasOverlay').addEventListener('click', closeOffcanvas);

        // Proceed Button Logic
        document.getElementById('proceedToBookingBtn').addEventListener('click', showBookingView);

        // Link click handling — include gallery folder
        const bookingButtons = document.querySelectorAll('a[href*="diversdesk.com"]');
        bookingButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                openOffcanvas(
                    this.href,
                    this.getAttribute('data-title'),
                    this.getAttribute('data-desc'),
                    this.getAttribute('data-image'),
                    this.getAttribute('data-gallery')
                );
            });
        });

        // Keyboard nav for lightbox
        document.addEventListener('keydown', function(e) {
            const lb = document.getElementById('lightbox');
            if (!lb.classList.contains('hidden')) {
                if (e.key === 'ArrowLeft')  lightboxPrev();
                if (e.key === 'ArrowRight') lightboxNext();
                if (e.key === 'Escape')     closeLightbox();
            }
            const gm = document.getElementById('galleryModal');
            if (!gm.classList.contains('hidden') && e.key === 'Escape') closeGalleryModal();
        });
    });

    // ─── Prep gallery modal initial state ────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        const backdrop = document.getElementById('galleryBackdrop');
        const content  = document.getElementById('galleryContent');
        backdrop.style.opacity  = '0';
        backdrop.style.transition = 'opacity 0.4s ease';
        content.style.opacity   = '0';
        content.style.transform = 'translateY(16px)';
        content.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    });
</script>

<style>
    @keyframes galleryTileIn {
        from { opacity: 0; transform: scale(0.92); }
        to   { opacity: 1; transform: scale(1); }
    }
    #galleryGrid { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.2) transparent; }
    #galleryGrid::-webkit-scrollbar { width: 6px; }
    #galleryGrid::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
    #lightboxImg { transition: opacity 0.15s ease; }
</style>

<!-- add Section End -->
<?php include('03-end.php') ?>