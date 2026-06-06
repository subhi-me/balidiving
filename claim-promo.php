<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title>Bali Diving Promo | Special Discount Min. 2 People - Bali Diving</title>
    <meta name="description"
        content="Get special price offers for Scuba Diving in Bali. Special promo for a minimum booking of 2 people. Explore the underwater beauty of Bali with professional instructors.">
    <meta name="keywords"
        content="bali diving, bali diving promo, scuba diving bali, nusa penida diving, tulamben diving, bali water sports">
    <meta name="author" content="Bali Diving">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://balidiving.com/">
    <meta property="og:title" content="Bali Diving Promo | Special Discount Min. 2 People">
    <meta property="og:description"
        content="Get special price offers for Scuba Diving in Bali. Special promo for a minimum booking of 2 people.">
    <meta property="og:image"
        content="https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=1200&auto=format&fit=crop">

    <!-- Favicon (fa-bolt custom SVG with #063c7f) -->
    <link rel="icon" type="image/x-icon" href="https://balidiving.com/bali-diving-64.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3552c8',
                        primaryHover: '#2841a3',
                        accent: '#f23d4e',
                        accentHover: '#d93645',
                        highlight: '#a2d2fa',
                        wa: '#25D366',
                        waHover: '#1ebc59',
                        light: '#ffffff',
                        dark: '#000000',
                        star: '#eebe35'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS for Glassmorphism & Animations -->
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .glass-dark {
            background: rgba(53, 82, 200, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Reveal Animation Classes */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Tooltip */
        .wa-tooltip {
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s;
        }

        .wa-container:hover .wa-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateX(-10px);
        }

        /* Parallax bg */
        .bg-parallax {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>

    <!-- Structured Data (Schema.org) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SportsActivityLocation",
      "name": "Bali Diving",
      "image": "https://balidiving.com/images/landingpage/bali-diving-diver.webp",
      "@id": "",
      "url": "https://balidiving.com",
      "telephone": "+6287861190174",
      "priceRange": "$$",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Sanur",
        "addressLocality": "Denpasar",
        "addressRegion": "Bali",
        "postalCode": "80228",
        "addressCountry": "ID"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -8.670458,
        "longitude": 115.236625
      }
    }
    </script>
</head>

<body class="font-sans bg-light text-dark antialiased overflow-x-hidden">

    <!-- Navbar -->
    <header class="fixed w-full top-0 z-50 transition-all duration-1000 opacity-0 pointer-events-none" id="navbar">
        <div
            class="glass max-w-7xl mx-auto mt-4 rounded-full px-6 py-3 mx-4 md:mx-auto flex justify-between items-center">
            <a href="#" class="flex items-center gap-2 group">
                <div
                    class="h-10 w-10 flex items-center justify-center group-hover:scale-105 transition-transform bg-white rounded-full p-1 border border-primary/20">
                    <img src="https://balidiving.com/images/bali-diving-logo.svg" alt="Bali Diving Logo"
                        class="h-full w-auto">
                </div>
                <span class="font-bold text-xl text-primary tracking-tight">Bali Diving</span>
            </a>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex gap-8 items-center font-medium text-slate-600">
                <a href="#home" class="hover:text-primary transition-colors">Home</a>
                <a href="#features" class="hover:text-primary transition-colors">Features</a>
                <a href="#pricing" class="hover:text-primary transition-colors">Promos</a>
                <a href="#gallery" class="hover:text-primary transition-colors">Gallery</a>
                <a href="#faq" class="hover:text-primary transition-colors">FAQ</a>
            </nav>

            <a href="#pricing"
                class="hidden md:inline-block bg-primary text-white px-6 py-2.5 rounded-full font-semibold hover:bg-primaryHover hover:shadow-lg hover:-translate-y-0.5 transition-all"
                data-id="Klaim Promo" data-en="Claim Promo">
                Claim Promo
            </a>

            <!-- Mobile Menu Button -->
            <button class="md:hidden text-2xl text-primary" id="mobile-menu-btn" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div class="md:hidden absolute top-20 left-4 right-4 glass rounded-2xl hidden flex-col p-4 shadow-xl gap-4"
            id="mobile-menu">
            <a href="#home" class="font-medium text-slate-700 p-2 hover:bg-slate-100 rounded-lg">Home</a>
            <a href="#features" class="font-medium text-slate-700 p-2 hover:bg-slate-100 rounded-lg">Features</a>
            <a href="#pricing" class="font-medium text-slate-700 p-2 hover:bg-slate-100 rounded-lg">Promos</a>
            <a href="#gallery" class="font-medium text-slate-700 p-2 hover:bg-slate-100 rounded-lg">Gallery</a>
            <a href="#faq" class="font-medium text-slate-700 p-2 hover:bg-slate-100 rounded-lg">FAQ</a>


        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center justify-center bg-parallax"
        style="background-image: url('https://balidiving.com/images/landingpage/bali-diving-diver.webp');">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-primary/40 to-black/70"></div>

        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto mt-20 reveal active">
            <div
                class="inline-block bg-accent px-4 py-1.5 rounded-full text-white text-sm font-semibold mb-3 tracking-wide shadow-lg shadow-accent/20">
                <i class="fa-solid fa-tag mr-2 text-white"></i> LIMITED SPECIAL PROMO
            </div>

            <div class="mb-5">
                <span id="promo-countdown"
                    class="inline-block font-mono bg-white/20 px-6 py-2 rounded-xl text-white font-bold text-lg md:text-xl border border-white/30 backdrop-blur-sm shadow-xl animate-pulse">Ends
                    in 00h 00m 00s</span>
            </div>

            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white leading-tight mb-6 drop-shadow-lg">
                Explore the Underwater Paradise of <span class="text-highlight">Bali</span>
            </h1>
            <p
                class="text-lg md:text-xl text-gray-100 mb-10 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md">
                Get a <strong>Special Price</strong> for a minimum booking of 2 people. Bring your partner and create
                unforgettable moments with a professional PADI instructor.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="#pricing"
                    class="bg-primary text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-primaryHover hover:shadow-xl hover:shadow-primary/30 hover:-translate-y-1 transition-all w-full sm:w-auto">
                    View Promo Packages
                </a>
                <a href="https://wa.me/6287861190174?text=Hello,%20I%20am%20interested%20in%20the%20Minimum%202%20People%20Discount%20promo%20I%20saw%20on%20the%20Website/Instagram"
                    target="_blank"
                    class="bg-accent text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-accentHover hover:shadow-xl hover:-translate-y-1 transition-all w-full sm:w-auto flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-xl"></i> Ask via WhatsApp
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <a href="#features" class="absolute bottom-10 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
            <i class="fa-solid fa-chevron-down text-3xl opacity-70"></i>
        </a>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <h2 class="text-primary font-bold tracking-wider uppercase text-sm mb-2">Why Choose Us</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-dark">The Best Diving Experience</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="bg-light p-8 rounded-3xl border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal group">
                    <div
                        class="w-16 h-16 bg-highlight/30 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
                        <i
                            class="fa-solid fa-id-badge text-2xl text-primary group-hover:text-white transition-colors"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-dark">Certified PADI Instructors</h4>
                    <p class="text-slate-600 leading-relaxed">Your safety is our priority. Accompanied by professional
                        Dive
                        Masters with high flight hours.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-light p-8 rounded-3xl border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal group"
                    style="transition-delay: 100ms;">
                    <div
                        class="w-16 h-16 bg-highlight/30 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
                        <i
                            class="fa-solid fa-mask-face text-2xl text-primary group-hover:text-white transition-colors"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-dark">Premium Equipment</h4>
                    <p class="text-slate-600 leading-relaxed">Using modern diving equipment that is regularly maintained
                        and
                        sanitized to international standards.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-light p-8 rounded-3xl border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal group"
                    style="transition-delay: 200ms;">
                    <div
                        class="w-16 h-16 bg-highlight/30 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
                        <i
                            class="fa-solid fa-camera text-2xl text-primary group-hover:text-white transition-colors"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-dark">Free Documentation</h4>
                    <p class="text-slate-600 leading-relaxed">Capture your underwater moments! Free HD quality
                        underwater photos and
                        videos for every package.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-light relative overflow-hidden">
        <!-- Decorative blob -->
        <div
            class="absolute top-0 right-0 w-96 h-96 bg-highlight rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float">
        </div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"
            style="animation-delay: 2s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 reveal">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-highlight/30 text-primary font-semibold text-sm mb-4">
                    <i class="fa-solid fa-fire text-accent"></i> Hot Promo
                </div>
                <h2 class="text-3xl md:text-5xl font-bold text-dark mb-4">Book for Two, Save More!</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Enjoy the beauty of Manta Point Nusa Penida or Liberty Wreck
                    Tulamben at a special price for a minimum of 2 Pax.</p>
            </div>

            <div class="max-w-3xl mx-auto">
                <!-- Main Package -->
                <div
                    class="glass bg-white/80 p-8 md:p-10 rounded-3xl shadow-2xl transition-all duration-300 reveal border border-primary relative transform hover:-translate-y-2">
                    <div
                        class="absolute top-0 right-0 bg-accent text-white text-sm font-bold px-6 py-2 rounded-bl-xl rounded-tr-3xl uppercase tracking-wider shadow-md">
                        All-Inclusive
                    </div>

                    <h3 class="text-3xl font-extrabold text-dark mb-3 mt-4">Bali Diving Experience</h3>
                    <p class="text-slate-500 mb-8 pb-8 border-b border-slate-200 text-lg">Everything You Need for a
                        Perfect Dive Day</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <ul class="space-y-4">
                            <li class="flex items-start gap-4 text-slate-700 font-medium">
                                <div class="bg-highlight/20 p-2 rounded-lg text-primary mt-0.5"><i
                                        class="fa-solid fa-car-side"></i></div>
                                Free hotel pickup & drop-off
                            </li>
                            <li class="flex items-start gap-4 text-slate-700 font-medium">
                                <div class="bg-highlight/20 p-2 rounded-lg text-primary mt-0.5"><i
                                        class="fa-solid fa-water"></i></div>
                                3 amazing ocean dives
                            </li>
                            <li class="flex items-start gap-4 text-slate-700 font-medium">
                                <div class="bg-highlight/20 p-2 rounded-lg text-primary mt-0.5"><i
                                        class="fa-solid fa-utensils"></i></div>
                                Tasty lunch & drinks
                            </li>
                            <li class="flex items-start gap-4 text-slate-700 font-medium">
                                <div class="bg-highlight/20 p-2 rounded-lg text-primary mt-0.5"><i
                                        class="fa-solid fa-ship"></i></div>
                                Fast boat to top dive spots
                            </li>
                        </ul>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-4 text-slate-700 font-medium">
                                <div class="bg-highlight/20 p-2 rounded-lg text-primary mt-0.5"><i
                                        class="fa-solid fa-chalkboard-user"></i></div>
                                Pro PADI instructor with you
                            </li>
                            <li class="flex items-start gap-4 text-slate-700 font-medium">
                                <div class="bg-highlight/20 p-2 rounded-lg text-primary mt-0.5"><i
                                        class="fa-solid fa-mask"></i></div>
                                Clean, ready-to-dive gear
                            </li>
                            <li class="flex items-start gap-4 text-slate-700 font-medium">
                                <div class="bg-highlight/20 p-2 rounded-lg text-primary mt-0.5"><i
                                        class="fa-solid fa-ticket"></i></div>
                                All fees & park tickets included
                            </li>
                            <li class="flex items-start gap-4 text-slate-700 font-medium">
                                <div class="bg-highlight/20 p-2 rounded-lg text-primary mt-0.5"><i
                                        class="fa-solid fa-shield-halved"></i></div>
                                Full local dive insurance
                            </li>
                        </ul>
                    </div>

                    <button onclick="bookPromo('All-Inclusive Bali Diving Experience')"
                        class="w-full bg-wa text-white py-5 rounded-2xl font-bold text-lg hover:bg-waHover shadow-lg hover:shadow-xl transition-all flex justify-center items-center gap-3">
                        <i class="fa-brands fa-whatsapp text-2xl"></i> Consult Dates & Secure Promo
                    </button>
                    <p class="text-center text-sm font-medium text-green-600 mt-4">*Limited Special Offer minimum
                        for 2 people</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal">
                <h2 class="text-3xl font-bold text-dark mb-4">Diving Gallery</h2>
                <p class="text-slate-600">Take a peek at the excitement of those who have explored with us.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="overflow-hidden rounded-2xl reveal relative group aspect-square">
                    <img src="https://balidiving.com/images/landingpage/bali-diving-diver.webp"
                        alt="Scuba diver with turtle"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        loading="lazy">
                    <div
                        class="absolute inset-0 bg-primary/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <i class="fa-brands fa-instagram text-white text-3xl"></i>
                    </div>
                </div>
                <div class="overflow-hidden rounded-2xl reveal relative group aspect-square"
                    style="transition-delay: 100ms;">
                    <img src="https://balidiving.com/images/landingpage/manta.webp" alt="Manta ray"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        loading="lazy">
                    <div
                        class="absolute inset-0 bg-primary/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <i class="fa-brands fa-instagram text-white text-3xl"></i>
                    </div>
                </div>
                <div class="overflow-hidden rounded-2xl reveal relative group aspect-square"
                    style="transition-delay: 200ms;">
                    <img src="https://balidiving.com/images/landingpage/reef.webp" alt="Coral reefs"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        loading="lazy">
                    <div
                        class="absolute inset-0 bg-primary/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <i class="fa-brands fa-instagram text-white text-3xl"></i>
                    </div>
                </div>
                <div class="overflow-hidden rounded-2xl reveal relative group aspect-square"
                    style="transition-delay: 300ms;">
                    <img src="https://balidiving.com/images/landingpage/coral.webp" alt="Group of divers"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        loading="lazy">
                    <div
                        class="absolute inset-0 bg-primary/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <i class="fa-brands fa-instagram text-white text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-24 bg-primary relative bg-parallax"
        style="background-image: url('https://images.unsplash.com/photo-1559494007-9f5847c49d94?q=80&w=2000&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-primary/90"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-white">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">What They Say</h2>
                <p class="text-highlight">Trusted by thousands of divers from around the world.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-dark p-8 rounded-3xl reveal border-t-2 border-primary">
                    <div class="flex text-star mb-4 text-sm">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-gray-200 italic mb-6">"Everything was perfect and we really appreciate our diving
                        instructor Ketut for being so understanding and patience which helps us a lot with our diving
                        adventure to go smoothly well. Really appreciate it and it was a fun experience!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden">
                            <img src="https://i.pravatar.cc/150?img=47" alt="User" class="w-full h-full object-cover"
                                loading="lazy">
                        </div>
                        <div>
                            <h4 class="font-bold">Naomi Choo</h4>
                            <p class="text-sm text-highlight">Japan</p>
                        </div>
                    </div>
                </div>

                <div class="glass-dark p-8 rounded-3xl reveal border-t-2 border-primary"
                    style="transition-delay: 100ms;">
                    <div class="flex text-star mb-4 text-sm">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-gray-200 italic mb-6">"Great crew, great experience! We went to Padang Bai for a
                        beginner's fun dive and, with professional instructors, immediately felt at home underwater!
                        Delicious lunch and pick-up/drop-off at the hotel included!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden">
                            <img src="https://i.pravatar.cc/150?img=11" alt="User" class="w-full h-full object-cover"
                                loading="lazy">
                        </div>
                        <div>
                            <h4 class="font-bold">Paul Wiesen</h4>
                            <p class="text-sm text-highlight">USA</p>
                        </div>
                    </div>
                </div>

                <div class="glass-dark p-8 rounded-3xl reveal border-t-2 border-primary"
                    style="transition-delay: 200ms;">
                    <div class="flex text-star mb-4 text-sm">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-gray-200 italic mb-6">"Excellent scuba diving experience with Bali Diving. The team
                        is lovely. The instructor, Chris, is attentive and very personable. I really enjoyed it. The
                        only slight downside was the travel time to Padang Bay; the activity took up the entire day due
                        to traffic."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden">
                            <img src="https://i.pravatar.cc/150?img=32" alt="User" class="w-full h-full object-cover"
                                loading="lazy">
                        </div>
                        <div>
                            <h4 class="font-bold">Emilie Schmitt</h4>
                            <p class="text-sm text-highlight">France</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-light">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal">
                <h2 class="text-3xl font-bold text-dark mb-4">Questions About the Promo</h2>
            </div>

            <div class="space-y-4 reveal">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <button
                        class="faq-btn w-full px-6 py-5 text-left font-semibold text-dark flex justify-between items-center focus:outline-none">
                        <span>Can beginners without certificates join?</span>
                        <i class="fa-solid fa-plus text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 bg-light px-6">
                        <p class="pb-5 text-slate-600">Of course! We provide a Discover Scuba Diving (DSD)
                            program specifically for beginners. You will be strictly accompanied 1-on-1 by our certified
                            instructors.</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <button
                        class="faq-btn w-full px-6 py-5 text-left font-semibold text-dark flex justify-between items-center focus:outline-none">
                        <span>How do I claim the Min 2 People promo?</span>
                        <i class="fa-solid fa-plus text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 bg-light px-6">
                        <p class="pb-5 text-slate-600">It's very easy, just click the WhatsApp button on this website.
                            Inform our admin that you want to claim the "Minimum 2 People Discount Promo from
                            Website", and the admin will guide the booking process.</p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <button
                        class="faq-btn w-full px-6 py-5 text-left font-semibold text-dark flex justify-between items-center focus:outline-none">
                        <span>What do I need to bring?</span>
                        <i class="fa-solid fa-plus text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 bg-light px-6">
                        <p class="pb-5 text-slate-600">Just bring a swimsuit, a change of clothes, sunblock (reef safe
                            recommended), and enough cash. We provide all diving equipment (wetsuit, fins,
                            mask, BCD, regulator).</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact & Footer -->
    <footer class="bg-dark pt-20 pb-10 border-t-4 border-primary text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

                <!-- Brand Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-12 w-12 bg-white rounded-full p-1.5 flex items-center justify-center">
                            <img src="https://balidiving.com/images/bali-diving-logo.svg" alt="Bali Diving Logo"
                                class="h-full w-auto">
                        </div>
                        <span class="font-bold text-3xl tracking-tight text-white">Bali <span
                                class="text-highlight">Diving</span></span>
                    </div>
                    <p class="mb-6 leading-relaxed max-w-sm">
                        Your trusted partner to explore the underwater beauty of Bali. Providing professional diving
                        services
                        with international safety standards.
                    </p>
                    <div class="flex gap-4">
                        <a href="https://www.instagram.com/bali_diving/" target="_blank"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.facebook.com/balidivingsunfish" target="_blank"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.tiktok.com/@balidiving" target="_blank"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                    </div>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">Quick Access</h4>
                    <ul class="space-y-3">
                        <li><a href="#home" class="hover:text-star transition-colors">Home</a></li>
                        <li><a href="#pricing" class="hover:text-star transition-colors">Promos</a></li>
                        <li><a href="#gallery" class="hover:text-star transition-colors">Gallery</a></li>
                        <li><a href="#faq" class="hover:text-star transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">Contact Us</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-star"></i>
                            <span>Jl. Bypass Ngurah Rai No.46E, Sanur Kauh, Denpasar Selatan, Kota Denpasar, Bali
                                80025</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-star"></i>
                            <a href="mailto:sales@balidiving.com"
                                class="hover:text-star transition-colors">sales@balidiving.com</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-brands fa-whatsapp text-star"></i>
                            <a href="https://wa.me/6287861190174" target="_blank"
                                class="hover:text-star transition-colors">+62 878-6119-0174</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-dark pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
                <p>&copy; <span id="copyright-year">2026</span> Bali Diving. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-star transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-star transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <div class="fixed bottom-6 right-6 z-50 wa-container flex items-center gap-3">
        <!-- Tooltip -->
        <div
            class="wa-tooltip bg-white text-dark px-4 py-2 rounded-xl shadow-lg border border-slate-100 font-medium text-sm hidden md:block">
            Chat via WhatsApp
            <div
                class="absolute top-1/2 -right-1 transform -translate-y-1/2 w-2 h-2 bg-white rotate-45 border-r border-t border-slate-100">
            </div>
        </div>

        <a href="https://wa.me/6287861190174?text=I%20am%20interested%20in%20the%20Minimum%202%20People%20Discount%20promo%20I%20saw%20on%20the%20Website/Instagram"
            target="_blank"
            class="w-16 h-16 bg-[#25D366] text-white rounded-full flex items-center justify-center text-3xl shadow-xl hover:scale-110 hover:shadow-2xl transition-all duration-300 relative group animate-bounce"
            aria-label="Chat via WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
            <!-- Ping animation effect -->
            <span
                class="absolute inline-flex h-full w-full rounded-full bg-[#25D366] opacity-30 animate-ping group-hover:hidden"></span>
        </a>
    </div>

    <!-- JavaScript Interactions -->
    <script>
        // 1. Mobile Menu Toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            menu.classList.toggle('flex');
        });

        // Close mobile menu on click link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.add('hidden');
                menu.classList.remove('flex');
            });
        });

        // 2. Navbar Glassmorphism on Scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('py-2');
                navbar.classList.remove('py-4');
            } else {
                navbar.classList.add('py-4');
                navbar.classList.remove('py-2');
            }
        });

        // 3. Scroll Reveal Animation (Intersection Observer)
        const revealElements = document.querySelectorAll('.reveal');

        const revealCallback = (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target); // Hanya animate sekali
                }
            });
        };

        const revealOptions = {
            threshold: 0.15,
            rootMargin: "0px 0px -50px 0px"
        };

        const revealObserver = new IntersectionObserver(revealCallback, revealOptions);

        revealElements.forEach(el => {
            revealObserver.observe(el);
        });

        // 4. FAQ Accordion Logic
        const faqBtns = document.querySelectorAll('.faq-btn');

        faqBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                // Tutup yang lain
                faqBtns.forEach(otherBtn => {
                    if (otherBtn !== btn) {
                        otherBtn.nextElementSibling.style.maxHeight = null;
                        otherBtn.querySelector('i').classList.remove('rotate-45');
                    }
                });

                // Toggle yang di-klik
                const content = this.nextElementSibling;
                const icon = this.querySelector('i');

                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                    icon.classList.remove('rotate-45');
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                    icon.classList.add('rotate-45');
                }
            });
        });

        // 5. Dynamic WhatsApp Booking Function
        function bookPromo(paket) {
            const phoneNumber = "6287861190174";
            const message = `Hello Bali Diving, I am interested in the ${paket} promo for a minimum 2 people discount that I saw on the Website. Can I get details on available dates?`;
            const encodedMessage = encodeURIComponent(message);
            window.open(`https://wa.me/${phoneNumber}?text=${encodedMessage}`, '_blank');
        }

        // 6. Daily Countdown Timer (Reset at 3 PM)
        function updateCountdown() {
            const now = new Date();
            let target = new Date();

            // Set target to today at 15:00:00 local time
            target.setHours(15, 0, 0, 0);

            // If current time is past 3 PM, set target to tomorrow 3 PM
            if (now > target) {
                target.setDate(target.getDate() + 1);
            }

            // Recalculate diff
            let diff = target - now;

            // If the time left is less than 3 hours (10800000 ms),
            // Add exactly 21 hours so that when it hits 2h 59m 59s, it jumps directly to 23h 59m 59s.
            if (diff <= 10800000) {
                diff += (21 * 60 * 60 * 1000);
            }

            // Calculate hours, minutes, and seconds. NO modulo 24 on hours so we can display exactly the amount we want!
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const hh = String(hours).padStart(2, '0');
            const mm = String(minutes).padStart(2, '0');
            const ss = String(seconds).padStart(2, '0');

            const timerElement = document.getElementById('promo-countdown');
            if (timerElement) {
                timerElement.innerText = `Ends in ${hh}h ${mm}m ${ss}s`;
            }
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();

        // 7. Navbar 1 Minute Delay Reveal
        setTimeout(() => {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                // Change to visible classes
                navbar.classList.remove('opacity-0', 'pointer-events-none');
                navbar.classList.add('opacity-100', 'pointer-events-auto');
            }
        }, 60000); // 60,000 milliseconds = 1 minute
    </script>
</body>

</html>