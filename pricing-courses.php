<?php
require_once 'template/seo_manager.php';
$page = 'pricing-courses';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://balidiving.com/bali-diving-64.png" />
    <?php echo generate_seo_tags('pricing-courses'); ?>

    <!-- Perf: preconnect -->
    <link rel="preconnect" href="https://connect.facebook.net" crossorigin>
    <link rel="preconnect" href="https://www.facebook.com" crossorigin>
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>

    <?php include('template/style.php'); ?>

    <style>
        /* Base body style adopted from index.php */
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", sans-serif;
            background-color: #f8fafc;
        }

        /* Title styling from index.php */
        h1.sui-title {
            font-family: 'Sui Generis', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", sans-serif;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #063c7f, #3552c8 40%, #23a0b4 90%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1rem;
            position: relative;
        }

        h1.sui-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #3552c8, #23a0b4);
            margin: 1rem auto 0;
            border-radius: 2px;
        }

        /* Custom Filter Scrollbar for Mobile */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Card Hover Effects */
        .package-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .package-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.15);
        }
    </style>
</head>

<body class="font-sans">
    <?php include('template/nav.php'); ?>

    <div style="height:70px;"></div>

    <!-- FIXED FILTERS BAR -->
    <div id="filter-bar"
        class="bg-white border-b border-slate-200 fixed w-full left-0 right-0 top-[64px] z-40 shadow-sm py-3 px-4 sm:px-6 transition-all duration-300">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Cert Filter -->
            <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-3">
                <h3 class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1.5 sm:mb-0 text-center sm:text-left">Filter by Required Certification Level:</h3>
                <div class="flex flex-wrap gap-1.5 justify-center sm:justify-start" id="filter-cert">
                    <!-- Rendered via JS -->
                </div>
            </div>
            <!-- Course Packages Button -->
            <div class="flex justify-center md:justify-end">
                <a href="https://balidiving.com/scuba-diving-certification" class="px-4 py-2 bg-gradient-to-r from-navy to-primary hover:from-teal hover:to-accent text-white text-xs font-bold rounded-xl transition-all duration-300 shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-graduation-cap text-lightblue"></i> Course Packages
                </a>
            </div>
        </div>
    </div>
    <!-- Spacer for fixed filter bar -->
    <div id="filter-spacer"></div>

    <!-- HERO SECTION -->
    <section class="bg-slate-50 pt-8 pb-4">
        <div class="max-w-6xl mx-auto text-center px-4">
            <h1 class="sui-title">PADI Course Pricing</h1>
            <p class="text-slate-500 max-w-2xl mx-auto mt-2 text-sm leading-relaxed">
                Get certified or advance your scuba skills in Bali. Choose the right PADI course for your certification level and book instantly online.
            </p>
            <div class="mt-4">
                <a href="https://balidiving.com/pricing" class="inline-flex items-center text-xs font-semibold text-primary hover:text-accent transition-colors bg-white px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm">
                    <i class="fa-solid fa-compass mr-1.5 text-teal text-sm"></i> Looking for Fun Diving or Snorkeling? View All Packages &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- MAIN APP SECTION -->
    <section class="bg-slate-50 min-h-screen pb-20" id="pricing-app">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4">

            <!-- RESULTS METADATA -->
            <div class="mb-6 flex justify-between items-end">
                <h2 class="text-xl font-bold text-slate-800" id="results-title">PADI Courses</h2>
                <span class="text-sm text-slate-500 font-medium bg-slate-200 px-3 py-1 rounded-full"
                    id="results-count">0 courses</span>
            </div>

             <!-- DISCLAIMER -->
            <div class="mb-8 bg-lightblue/10 border-l-4 border-accent p-4 rounded-r-xl shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-info text-accent mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-navy">
                            <strong>Note:</strong> The prices shown are base reference rates. Final prices are
                            determined after date confirmation and may vary based on your selected dates on the booking
                            page.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARDS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="packages-grid">
                <!-- Cards rendered via JS -->
            </div>

            <!-- NO RESULTS STATE -->
            <div id="no-results" class="hidden text-center py-20">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-200 text-slate-400 mb-4">
                    <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">No courses found</h3>
                <p class="text-slate-500 mb-4">Try adjusting your filters to see more results.</p>
                <button onclick="resetFilters()" class="text-accent font-semibold hover:text-primary underline">Clear
                    all filters</button>
            </div>

        </div>
    </section>

    <!-- FAQ SECTION -->
    <section class="relative w-full py-20 bg-white overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-32 -left-32 w-[420px] h-[420px] bg-cyan-50 rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-3xl mx-auto px-6 relative z-10">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-8 text-center">Frequently Asked Questions</h2>

            <div class="space-y-4">
                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm">
                    <button
                        class="w-full flex items-center justify-between text-left font-semibold text-slate-800 focus:outline-none"
                        onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>Are course materials and certification fees included?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400"></i>
                    </button>
                    <div class="hidden mt-3 text-slate-600 text-sm leading-relaxed">
                        Yes, our PADI Course prices are all-inclusive. This includes the PADI eLearning materials, certification fees, full equipment rental, pool sessions, ocean training dives, logbook, and hotel transfers in designated areas.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm">
                    <button
                        class="w-full flex items-center justify-between text-left font-semibold text-slate-800 focus:outline-none"
                        onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>How long does it take to get certified?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400"></i>
                    </button>
                    <div class="hidden mt-3 text-slate-600 text-sm leading-relaxed">
                        For the PADI Open Water Diver course, it typically takes 2 to 3 days. The theory is completed online beforehand via PADI eLearning, meaning you will focus on practical pool sessions and 4 open water dives once you arrive in Bali.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm">
                    <button
                        class="w-full flex items-center justify-between text-left font-semibold text-slate-800 focus:outline-none"
                        onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>What if I cannot complete all course requirements?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400"></i>
                    </button>
                    <div class="hidden mt-3 text-slate-600 text-sm leading-relaxed">
                        Safety and comfort are our main priorities. If you need extra time, our instructors will work with you. If you cannot complete the course, we can issue a PADI Referral so you can complete the remaining dives at any other PADI Dive Center worldwide within 12 months.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DATA & LOGIC -->
    <script>
        const BOOKING_BASE = "https://balidiving.diversdesk.com/establishment/2db57e00-d266-4eb5-9e19-4c032269fccc/shop/activity/";

        const activities = [
            // PADI COURSES
            { id: "padi-deep-diver", title: "PADI Deep Diver Specialty Course", shortTitle: "Deep Diver Specialty", description: "1 day / 3 dives at Padang Bai or Nusa Penida", category: "padi-course", priceIDR: 4500000, locations: ["Padang Bai", "Nusa Penida"], durationDays: 1, numberOfDives: 3, certificationRequired: "advanced", bookingUrl: BOOKING_BASE + "b95e72d0-f1b8-4e9d-8f92-4df609c3427e", highlights: ["Dive beyond 30m", "PADI certification", "3 deep dives included"], thumb: "images/thumbnail/course/padi-deep-diver-course-bali.jpg" },
            { id: "padi-dad", title: "PADI Dive Against Debris Course", shortTitle: "Dive Against Debris", description: "PADI DAD Course (1 day / 2 dives)", category: "padi-course", priceIDR: 3750000, locations: ["Padang Bai"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "a96cb10d-61a0-49f3-85a5-1b921eb039d0", highlights: ["Conservation-focused", "Project AWARE certified", "Make an impact"], thumb: "images/thumbnail/course/padi-dive-against-debris-course-bali.jpg" },
            { id: "padi-efr-rescue", title: "PADI EFR and Rescue Course", shortTitle: "EFR + Rescue", description: "3 days of intensive emergency and rescue training", category: "padi-course", priceIDR: 8800000, locations: ["Padang Bai", "Tulamben"], durationDays: 3, numberOfDives: 6, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "83f3d13d-aaf8-4dd2-8c1b-aec1526dd268", highlights: ["Lifesaving skills", "EFR + Rescue dual certification", "Step toward Divemaster"], thumb: "images/thumbnail/course/padi-efr-rescue-diver-course-bali.jpg" },
            { id: "padi-aow-normal", title: "PADI Advanced Open Water Course", shortTitle: "Advanced Open Water", description: "5 dives in 2 days at Tulamben or Padang Bai", category: "padi-course", priceIDR: 6100000, locations: ["Tulamben", "Padang Bai"], durationDays: 2, numberOfDives: 5, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "f9abbc9b-a9f6-4526-a1f6-a42d1b2eddbc", highlights: ["5 adventure dives", "PADI AOW cert", "Dive to 30m"], popular: true, thumb: "images/thumbnail/course/padi-advanced-open-water-course-bali.jpg" },
            { id: "padi-owd-2d", title: "PADI Open Water Diver (2 Days)", shortTitle: "Open Water Diver — 2 Days", description: "4 dives in 2 days — the fast-track certification", category: "padi-course", priceIDR: 6800000, locations: ["Padang Bai", "Tulamben"], durationDays: 2, numberOfDives: 4, certificationRequired: "none", bookingUrl: BOOKING_BASE + "3eb9cb33-2d24-4473-8dac-f5af0a32817c", highlights: ["No experience required", "PADI OWD certification", "Dive worldwide"], thumb: "images/thumbnail/course/padi-open-water-diver-course-bali.jpg" },
            { id: "padi-owd-3d", title: "PADI Open Water Diver (3 Days)", shortTitle: "Open Water Diver — 3 Days", description: "4 dives in 3 days — the relaxed pace", category: "padi-course", priceIDR: 7100000, locations: ["Padang Bai", "Tulamben"], durationDays: 3, numberOfDives: 4, certificationRequired: "none", bookingUrl: BOOKING_BASE + "8f1fc6ea-5ef7-4443-9d3e-cfc2ec21cb56", highlights: ["No experience required", "Comfortable 3-day pace", "PADI OWD cert"], popular: true, thumb: "images/thumbnail/course/padi-open-water-diver-3-day-course-bali.jpg" },
            { id: "padi-owd-referral", title: "PADI Open Water Referral (2 Days)", shortTitle: "Open Water Referral", description: "4 dives in 2 days — complete your in-progress OWD", category: "padi-course", priceIDR: 4500000, locations: ["Padang Bai", "Tulamben"], durationDays: 2, numberOfDives: 4, certificationRequired: "none", bookingUrl: BOOKING_BASE + "997c8c6e-a919-4d26-b027-88bed4dda66b", highlights: ["Finish your OWD in Bali", "Theory already done", "Open water dives only"], thumb: "images/thumbnail/course/padi-open-water-referral-course-bali.jpg" },
            { id: "padi-owd-upgrade", title: "PADI Open Water Upgrade from Scuba Diver", shortTitle: "OWD Upgrade", description: "2 dives in 1 day to upgrade your Scuba Diver to Open Water", category: "padi-course", priceIDR: 3800000, locations: ["Padang Bai"], durationDays: 1, numberOfDives: 2, certificationRequired: "scuba-diver", bookingUrl: BOOKING_BASE + "67469727-d440-4f3f-a957-1915eb536154", highlights: ["Quick upgrade path", "Full OWD certification", "Just 1 day"], thumb: "images/thumbnail/course/padi-scuba-diver-upgrade-course-bali.jpg" },
            { id: "padi-ppb", title: "PADI Peak Performance Buoyancy Course", shortTitle: "Peak Performance Buoyancy", description: "1 day / 2 dives at Tulamben or Padang Bai", category: "padi-course", priceIDR: 3600000, locations: ["Tulamben", "Padang Bai"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "5dd36dae-57f5-4f68-8aa5-917c367801b6", highlights: ["Master buoyancy control", "Save air, dive longer", "PADI specialty cert"], thumb: "images/thumbnail/course/padi-peak-performance-buoyancy-course-bali.jpg" },
            { id: "padi-aow-platinum", title: "PADI Platinum Advanced Open Water Course", shortTitle: "Platinum AOW", description: "5 dives in 2 days at Tulamben and Nusa Penida", category: "padi-course", priceIDR: 6700000, locations: ["Tulamben", "Nusa Penida"], durationDays: 2, numberOfDives: 5, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "c7ac29d0-fce5-4561-bb43-066234eeef90", highlights: ["Premium dive sites", "Tulamben + Nusa Penida", "PADI AOW certification"], thumb: "images/thumbnail/course/padi-platinum-advanced-open-water-bali.jpg" },
            { id: "padi-refresher-pool", title: "PADI Refresher Program in the Pool", shortTitle: "Refresher (Pool)", description: "1 dive in a half day — knock the rust off", category: "padi-course", priceIDR: 1200000, locations: ["Pool"], durationDays: 0.5, numberOfDives: 1, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "e98b8359-38f3-4784-bf97-9904573bc507", highlights: ["Pool-based, safe environment", "Refresh your skills", "Just a half day"], thumb: "images/thumbnail/course/padi-refresher-pool-session-bali.jpg" },
            { id: "padi-rescue", title: "PADI Rescue Diver Course", shortTitle: "Rescue Diver", description: "PADI Rescue Diver Course — 2 days", category: "padi-course", priceIDR: 6100000, locations: ["Padang Bai", "Tulamben"], durationDays: 2, numberOfDives: 4, certificationRequired: "advanced", bookingUrl: BOOKING_BASE + "724e030e-970d-440f-887b-166c4e43fa30", highlights: ["Rescue & emergency skills", "Required for Divemaster", "Most rewarding course"], thumb: "images/thumbnail/course/padi-rescue-diver-course-bali.jpg" },
            { id: "padi-scuba-diver", title: "PADI Scuba Diver (2 Days)", shortTitle: "Scuba Diver", description: "2 dives in 2 days — the entry-level certification", category: "padi-course", priceIDR: 5700000, locations: ["Padang Bai"], durationDays: 2, numberOfDives: 2, certificationRequired: "none", bookingUrl: BOOKING_BASE + "4a4f5c7d-6ed5-4fc0-9fa1-51f6c25a32e2", highlights: ["Shorter than OWD", "PADI Scuba Diver cert", "Upgrade later"], thumb: "images/thumbnail/course/padi-scuba-diver-course-bali-01.jpg" },
            { id: "padi-wreck", title: "PADI Wreck Diver Course", shortTitle: "Wreck Diver", description: "2 days / 4 dives at the Tulamben USAT Liberty wreck", category: "padi-course", priceIDR: 5600000, locations: ["Tulamben"], durationDays: 2, numberOfDives: 4, certificationRequired: "advanced", bookingUrl: BOOKING_BASE + "2a915fe9-1dd4-46f6-8be5-91597cf1aba7", highlights: ["USAT Liberty WWII wreck", "PADI Wreck specialty", "Legendary dive site"], thumb: "images/thumbnail/course/padi-wreck-diver-tulamben-bali.jpg" }
        ];

        const certLevels = [
            { id: 'all', name: 'Any Level' },
            { id: 'none', name: 'No Cert. Required' },
            { id: 'scuba-diver', name: 'Scuba Diver' },
            { id: 'open-water', name: 'Open Water' },
            { id: 'advanced', name: 'Advanced' }
        ];

        // State
        let state = {
            category: 'padi-course',
            cert: 'all'
        };

        // Utility to format IDR psychologically
        const formatPsychologicalIDR = (price) => {
            const formatted = new Intl.NumberFormat('id-ID').format(price);
            const parts = formatted.split('.');
            if (parts.length > 1 && parts[parts.length - 1] === '000') {
                const mainPart = parts.slice(0, -1).join('.');
                return `<span class="text-sm font-bold text-slate-500 mr-1">IDR</span><span class="text-3xl font-black text-slate-900 leading-none">${mainPart}</span><span class="text-lg font-bold text-slate-400">.000</span>`;
            }
            return `<span class="text-sm font-bold text-slate-500 mr-1">IDR</span><span class="text-3xl font-black text-slate-900 leading-none">${formatted}</span>`;
        };

        const getFakeOriginalPrice = (price) => {
            const markup = price * 1.18;
            const rounded = Math.ceil(markup / 50000) * 50000;
            return new Intl.NumberFormat('id-ID').format(rounded);
        };

        // Render Filters
        const renderFilters = () => {
            // Cert Level
            const certContainer = document.getElementById('filter-cert');
            certContainer.innerHTML = certLevels.map(cert => `
        <button 
          onclick="setFilter('cert', '${cert.id}')"
          class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors duration-200 border shadow-sm
          ${state.cert === cert.id
                    ? 'bg-teal text-white border-teal'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900'}">
          ${cert.name}
        </button>
      `).join('');

            // Adjust spacer after rendering
            setTimeout(adjustSpacer, 50);
        };

        const adjustSpacer = () => {
            const filterBar = document.getElementById('filter-bar');
            const spacer = document.getElementById('filter-spacer');
            if (filterBar && spacer) {
                spacer.style.height = filterBar.offsetHeight + 'px';
            }
        };
        window.addEventListener('resize', adjustSpacer);

        // Set Filter
        window.setFilter = (type, value) => {
            state[type] = value;
            renderFilters();
            renderPackages();
        };

        // Reset Filters
        window.resetFilters = () => {
            state.cert = 'all';
            renderFilters();
            renderPackages();
        };

        // Booking Handler with Timeout
        window.handleBookingClick = (event, activityId) => {
            event.preventDefault();
            const activity = activities.find(a => a.id === activityId);
            if (!activity) return;

            const url = activity.bookingUrl;
            const packageTitle = activity.shortTitle;
            const btn = event.currentTarget;
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Connecting...';
            btn.style.pointerEvents = 'none';

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 2000);

            fetch(url, { mode: 'no-cors', cache: 'no-store', signal: controller.signal })
                .then(() => {
                    clearTimeout(timeoutId);
                    window.location.href = url;
                })
                .catch(() => {
                    // Fallback to WhatsApp if timeout or network error
                    const waText = encodeURIComponent(`Hi Bali Diving, I want to book ${packageTitle} but the online booking system seems to be unavailable. Can you help me?`);
                    const waUrl = `https://wa.me/6287861190174?text=${waText}`;
                    window.location.href = waUrl;
                });
        };

        // Render Packages
        const renderPackages = () => {
            const grid = document.getElementById('packages-grid');
            const noResults = document.getElementById('no-results');
            const countEl = document.getElementById('results-count');
            const titleEl = document.getElementById('results-title');

            // Filter Logic
            const filtered = activities.filter(act => {
                const matchCat = act.category === 'padi-course';
                const matchCert = state.cert === 'all' || act.certificationRequired === state.cert;
                return matchCat && matchCert;
            });

            // Sort by price ascending (cheapest to most expensive)
            filtered.sort((a, b) => a.priceIDR - b.priceIDR);

            // Update Meta
            countEl.innerText = `${filtered.length} course${filtered.length !== 1 ? 's' : ''}`;
            titleEl.innerText = "PADI Courses";

            if (filtered.length === 0) {
                grid.innerHTML = '';
                grid.classList.add('hidden');
                noResults.classList.remove('hidden');
                return;
            }

            grid.classList.remove('hidden');
            noResults.classList.add('hidden');

            grid.innerHTML = filtered.map(act => {
                // Determine badges
                const popularBadge = act.popular ? `<span class="absolute top-4 right-4 bg-secondary text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm z-10"><i class="fa-solid fa-fire mr-1"></i> Popular</span>` : '';

                // Map cert level to human readable
                const certLabel = certLevels.find(c => c.id === act.certificationRequired)?.name || act.certificationRequired;

                // Highlights
                const highlightsHtml = act.highlights.map(h => `
          <li class="flex items-start text-sm text-slate-600 mb-1">
            <i class="fa-solid fa-check text-teal mt-1 mr-2 text-xs"></i>
            <span>${h}</span>
          </li>
        `).join('');

                const thumbUrl = act.thumb;

                return `
          <div class="package-card relative flex flex-col bg-white rounded-2xl border border-slate-200 overflow-hidden">
            ${popularBadge}
            <div class="h-48 w-full relative overflow-hidden bg-slate-200">
               <img src="${thumbUrl}" alt="${act.shortTitle}" class="object-cover w-full h-full transform hover:scale-105 transition-transform duration-500" loading="lazy">
               <!-- Location Overlay -->
               <div class="absolute bottom-3 left-3 flex gap-2 flex-wrap">
                  <span class="inline-block bg-navy/80 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-md uppercase tracking-wide shadow-sm">
                    <i class="fa-solid fa-location-dot mr-1 text-lightblue"></i> ${act.locations.join(' & ')}
                  </span>
               </div>
            </div>
            
            <div class="p-6 flex-grow pt-5">
               <h3 class="text-xl font-bold text-slate-900 mb-2 leading-tight">${act.shortTitle}</h3>
               <p class="text-slate-500 text-sm mb-5 min-h-[40px]">${act.description}</p>
              
               <!-- Key Details Grid -->
               <div class="grid grid-cols-2 gap-y-3 gap-x-2 mb-5">
                <div class="flex items-center text-sm text-slate-700 font-medium">
                  <div class="w-8 h-8 rounded-full bg-lightblue/10 flex items-center justify-center text-accent mr-2">
                    <i class="fa-regular fa-clock"></i>
                  </div>
                  ${act.durationDays} ${act.durationDays === 1 ? 'Day' : 'Days'}
                </div>
                <div class="flex items-center text-sm text-slate-700 font-medium">
                  <div class="w-8 h-8 rounded-full bg-teal/10 flex items-center justify-center text-teal mr-2">
                    <i class="fa-solid fa-water"></i>
                  </div>
                  ${act.numberOfDives > 0 ? act.numberOfDives + ' Dives' : 'Snorkel'}
                </div>
                <div class="flex items-center text-sm text-slate-700 font-medium col-span-2 mt-1">
                  <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 mr-2">
                    <i class="fa-solid fa-id-card"></i>
                  </div>
                  Req: ${certLabel}
                </div>
              </div>

              <div class="h-px bg-slate-100 w-full mb-5"></div>
              
              <ul class="mb-2">
                ${highlightsHtml}
              </ul>
            </div>
            
            <!-- Price & Action (Bottom) -->
            <div class="p-6 bg-slate-50 border-t border-slate-100 mt-auto">
              <div class="flex flex-col mb-4">
                <div class="flex justify-between items-end mb-1">
                  <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Online Rate</span>
                  <span class="text-sm font-semibold text-slate-400 line-through decoration-slate-300">IDR ${getFakeOriginalPrice(act.priceIDR)}</span>
                </div>
                <div class="flex items-baseline mb-1">
                  ${formatPsychologicalIDR(act.priceIDR)}
                </div>
                ${act.numberOfDives > 1 ? `<span class="text-xs font-medium text-teal"><i class="fa-solid fa-tag mr-1"></i> Only IDR ${new Intl.NumberFormat('id-ID').format(Math.round(act.priceIDR / act.numberOfDives))} / dive</span>` : '<span class="text-xs font-medium text-slate-400 opacity-0 hidden sm:block">_</span>'}
              </div>
              
              <a href="${act.bookingUrl}" onclick="handleBookingClick(event, '${act.id}')" class="block w-full py-3.5 px-4 bg-gradient-to-r from-accent to-primary text-white text-center font-bold rounded-xl hover:from-primary hover:to-accent transition-all duration-300 shadow-md hover:shadow-lg">
                Book Now
              </a>
            </div>
          </div>
        `;
            }).join('');
        };

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            renderFilters();
            renderPackages();
        });
    </script>

    <?php include('template/footer.php'); ?>
    <?php include('template/chat.php'); ?>
    <?php include('template/pixel.php'); ?>
    <?php include('template/gtag.php'); ?>

</body>

</html>
