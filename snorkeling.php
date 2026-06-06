<?php 
$page = 'services/snorkeling';
include('01-start.php'); 
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
            <img src="https://balidiving.com/images/main/header/snorkeling1.jpg" alt="Snorkeling Hero"
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
                Bali Snorkeling
            </h1>

            <p
                class="text-base sm:text-lg md:text-xl lg:text-2xl text-blue-50 max-w-3xl mx-auto font-medium leading-relaxed drop-shadow-lg text-glow opacity-95 px-4">
                Go on one of our amazing snorkeling trips. You can snorkel on coral bommies and white sand bottoms, the
                shipwreck of a World War II transport ship, or the magical Manta Rays at Nusa Penida. <br
                    class="hidden md:block">Snorkeling is something that anyone can do, even if they can't swim.
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
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 tracking-tight">Why Snorkel in Bali?</h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-blue-500 to-cyan-400 mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6 text-lg text-gray-600 leading-relaxed font-light">
                    <p>
                        <span class="font-semibold text-gray-900">Not a diver? No problem!</span> Snorkeling is the most
                        popular way to explore Bali's underwater paradise. We’ve curated the absolute best spots where
                        crystal-clear visibility meets vibrant marine life.
                    </p>
                    <p>
                        From ages 8 to 80, snorkeling is an inclusive adventure for the whole family. Witness the
                        stunning biodiversity of our oceans without the need for extensive training.
                    </p>
                    <div class="p-6 bg-blue-50/50 rounded-2xl border border-blue-100 shadow-sm">
                        <h4 class="font-bold text-blue-800 mb-2 flex items-center"><i
                                class="fas fa-check-circle mr-2"></i> All-Inclusive Experience</h4>
                        <p class="text-sm text-blue-900/80">
                            Our packages include <span class="font-medium">premium gear, experienced guides, lunch, and
                                transfer</span>. Plus, add us on Facebook for free photos of your trip!
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-cyan-400 rounded-3xl transform rotate-3 scale-105 opacity-20 blur-lg">
                    </div>
                    <img src="https://balidiving.com/images/main/mid/snorkeling-kadek.jpg" alt="Underwater Exploration"
                        class="relative rounded-3xl shadow-2xl transform transition hover:scale-[1.01] duration-500">
                </div>
            </div>
        </div>
    </section>

    <section id="tours" class="py-24 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-blue-600 font-bold tracking-wider uppercase text-sm mb-2 block">Choose Your
                    Destination</span>
                <h3 class="text-4xl font-bold text-gray-900">Premium Snorkeling Spots</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                <!-- Card 1 -->
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full border border-gray-100">
                    <div class="relative h-64 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity">
                        </div>
                        <img src="https://balidiving.com/images/main/thumbnail/padangbai-01.jpg" alt="Padang Bai"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <span
                            class="absolute top-4 right-4 z-20 bg-white/90 backdrop-blur text-blue-800 text-xs font-bold px-3 py-1 rounded-full shadow-sm">POPULAR</span>
                    </div>
                    <div class="p-6 flex-grow flex flex-col relative">
                        <h4 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition-colors">
                            Padang Bai</h4>
                        <p class="text-gray-500 text-sm mb-6 flex-grow leading-relaxed font-light">
                            Discover the calm bays of the eastern coast. Perfect for beginners and macro photography
                            enthusiasts.
                        </p>
                        <a href="https://balidiving.diversdesk.com/participant/mutate?product_id=904583c4-c2fd-470a-96b1-1621c17bcba4&form_id=24aa191e-f21e-4128-8bad-4ac247510703"
                            target="_self" data-title="Padang Bai"
                            data-image="https://balidiving.com/images/main/thumbnail/padangbai-01.jpg"
                            data-desc="Imagine stepping into a real-life aquarium. The Blue Lagoon offers crystal-clear, calm waters perfect for your first snorkeling experience. You'll float above vibrant coral bommies teeming with colorful tropical fish, just a short traditional boat ride from the white sandy beach. It's a hidden gem of East Bali that feels like a private paradise."
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            Select
                        </a>
                    </div>
                </div>

                <!-- Card 2 -->
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full border border-gray-100">
                    <div class="relative h-64 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity">
                        </div>
                        <img src="https://balidiving.com/images/main/thumbnail/tulamben-01.jpg" alt="Tulamben"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h4 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition-colors">
                            Tulamben Wreck</h4>
                        <p class="text-gray-500 text-sm mb-6 flex-grow leading-relaxed font-light">
                            A world-famous site where you can see the USAT Liberty Shipwreck right from the surface.
                            History meets marine life.
                        </p>
                        <a href="https://balidiving.diversdesk.com/participant/mutate?product_id=04911d70-94a3-4bb6-bf0a-472783bb4ae8&form_id=24aa191e-f21e-4128-8bad-4ac247510703"
                            target="_self" data-title="Tulamben Shipwreck"
                            data-image="https://balidiving.com/images/main/thumbnail/tulamben-01.jpg"
                            data-desc="Witness history come alive beneath the waves. You don't need to be a diver to see the majestic USAT Liberty, a WWII cargo ship resting just meters from the shore. Snorkel right above the wreck, surrounded by swirling schools of jackfish and friendly sea turtles. It's an eerie, beautiful, and absolutely unmissable underwater museum."
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            Select
                        </a>
                    </div>
                </div>

                <!-- Card 3 -->
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
                            Amed Coast</h4>
                        <p class="text-gray-500 text-sm mb-6 flex-grow leading-relaxed font-light">
                            Explore vibrant coral gardens along the traditional fishing villages. Authentic Bali vibes
                            with stunning reefs.
                        </p>
                        <a href="https://balidiving.diversdesk.com/participant/mutate?product_id=d269ecf7-dd85-4402-bce5-62a899674a2c&form_id=24aa191e-f21e-4128-8bad-4ac247510703"
                            target="_self" data-title="Amed Coral Coast"
                            data-image="https://balidiving.com/images/main/thumbnail/amed-01.jpg"
                            data-desc="Escape the crowds and discover the authentic charm of Bali's north-east coast. In Amed, the unique black volcanic sand makes the coral colors pop with incredible intensity. Glide over shallow, untouched reefs and artificial 'pyramids' home to blue-spotted rays and sea turtles. The view of Mount Agung from the water makes this a breathtaking experience above and below the surface."
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            Select
                        </a>
                    </div>
                </div>

                <!-- Card 4 -->
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full border border-gray-100">
                    <div class="relative h-64 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity">
                        </div>
                        <img src="https://balidiving.com/images/main/thumbnail/nusa-penida-01.jpg" alt="Nusa Penida"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <span
                            class="absolute top-4 right-4 z-20 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">MUST
                            SEE</span>
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h4 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition-colors">
                            Manta Point</h4>
                        <p class="text-gray-500 text-sm mb-6 flex-grow leading-relaxed font-light">
                            The ultimate experience. Swim alongside majestic Manta Rays in the pristine waters of Nusa
                            Penida.
                        </p>
                        <a href="https://balidiving.diversdesk.com/participant/mutate?product_id=0e41a1c6-67d1-4bed-9c3f-48314930bce0&form_id=24aa191e-f21e-4128-8bad-4ac247510703"
                            target="_self" data-title="Nusa Penida Manta Point"
                            data-image="https://balidiving.com/images/main/thumbnail/nusa-penida-01.jpg"
                            data-desc="Prepare for the encounter of a lifetime. Journey across the open ocean to the wild coast of Nusa Penida, where giant Manta Rays glide gracefully through the water. Watching these gentle giants, with wingspans up to 5 meters, dance around you is a humbling and magical experience that defines a Bali holiday."
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
            <h3 class="text-3xl md:text-4xl font-bold text-white mb-6">Custom Group Snorkeling?</h3>
            <p class="text-blue-100 mb-10 max-w-2xl mx-auto text-lg font-light leading-relaxed">
                Have a specific itinerary in mind? We customize snorkeling tours for groups and multi-day trips. Let us
                design your dream holiday.
            </p>
            <a href="https://balidiving.com/enquire"
                class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-blue-900 transition-all duration-200 bg-white rounded-full hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white shadow-xl hover:-translate-y-1">
                Enquire Group
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
                class="absolute bottom-6 left-6 right-6 z-20 transform translate-y-0 transition-transform duration-500">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-600/90 text-white text-xs font-bold mb-3 backdrop-blur-sm shadow-[0_4px_10px_rgba(37,99,235,0.3)] border border-blue-400/30">
                    <i class="fas fa-star text-yellow-300 text-[10px]"></i> Premium Experience
                </div>
                <h3 id="detailTitle" class="text-3xl md:text-4xl font-bold text-white shadow-sm leading-tight"></h3>
            </div>
        </div>

        <!-- Content Body -->
        <div class="flex-1 flex flex-col p-8 relative overflow-y-auto">
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

                    <!-- PADI Pro -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div
                            class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mr-3 shrink-0">
                            <i class="fas fa-user-shield text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Pro Guide</span>
                    </div>

                    <!-- Gear Rental -->
                    <div class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div
                            class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 mr-3 shrink-0">
                            <i class="fas fa-mask text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Gear Rental</span>
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
                        <span class="text-sm font-medium text-slate-700">Insurance*</span>
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

<script>
    let currentBookingUrl = '';

    function openOffcanvas(url, title, desc, img) {
        currentBookingUrl = url;

        // Populate Detail View
        document.getElementById('detailTitle').textContent = title || 'Snorkeling Adventure';
        document.getElementById('detailDesc').textContent = desc || 'Experience the beauty of Bali underwater.';
        if (img) {
            document.getElementById('detailImage').src = img;
        }

        // Reset Views
        showDetailsView();

        const offcanvas = document.getElementById('bookingOffcanvas');
        const overlay = document.getElementById('offcanvasOverlay');

        // Show overlay
        overlay.classList.remove('hidden');
        // Small delay to allow display:block to apply before opacity transition
        requestAnimationFrame(() => {
            overlay.classList.add('opacity-100');
            offcanvas.classList.remove('translate-x-full');
        });

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        // Hide chat widget
        if (typeof toggleChatVisibility === 'function') {
            toggleChatVisibility(false);
        }
    }

    function showDetailsView() {
        document.getElementById('detailsView').classList.remove('hidden');
        document.getElementById('bookingView').classList.add('hidden');

        // Clear iframe to stop it running in background
        document.getElementById('bookingIframe').src = 'about:blank';
    }

    function showBookingView() {
        document.getElementById('detailsView').classList.add('hidden');
        document.getElementById('bookingView').classList.remove('hidden');

        const iframe = document.getElementById('bookingIframe');
        const loader = document.getElementById('iframeLoader');

        // Show loader
        loader.classList.remove('hidden');

        // Load URL
        iframe.src = currentBookingUrl;

        // Hide loader on load (Approximate)
        iframe.onload = function () {
            // setTimeout to ensure partial rendering
            setTimeout(() => {
                loader.classList.add('hidden');
            }, 500);
        };
    }

    function closeOffcanvas() {
        const offcanvas = document.getElementById('bookingOffcanvas');
        const overlay = document.getElementById('offcanvasOverlay');

        // Slide out
        offcanvas.classList.add('translate-x-full');
        overlay.classList.remove('opacity-100');

        setTimeout(() => {
            overlay.classList.add('hidden');
            document.getElementById('bookingIframe').src = 'about:blank';
        }, 500);

        document.body.style.overflow = '';

        // Show chat widget
        if (typeof toggleChatVisibility === 'function') {
            toggleChatVisibility(true);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Overlay Click Close
        document.getElementById('offcanvasOverlay').addEventListener('click', closeOffcanvas);

        // Proceed Button Logic
        document.getElementById('proceedToBookingBtn').addEventListener('click', showBookingView);

        // Link click handling
        const bookingButtons = document.querySelectorAll('a[href*="diversdesk.com"]');
        bookingButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const url = this.href;
                const title = this.getAttribute('data-title');
                const desc = this.getAttribute('data-desc');
                const img = this.getAttribute('data-image');

                openOffcanvas(url, title, desc, img);
            });
        });
    });
</script>

<!-- add Section End -->
<?php include('03-end.php') ?>