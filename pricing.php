<?php
require_once 'template/seo_manager.php';
$page = 'pricing';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://balidiving.com/bali-diving-64.png" />
    <title>Activity Price Packages | Bali Diving</title>
    <meta name="description"
        content="Explore our diving and snorkeling activity packages. Compare prices, check certification requirements, and book your Bali diving adventure online.">

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
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-4">
            <!-- Category Filter -->
            <div class="flex-1">
                <h3 class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1.5">Activity Type</h3>
                <div class="flex flex-wrap gap-1.5" id="filter-category">
                    <!-- Rendered via JS -->
                </div>
            </div>

            <!-- Cert Filter -->
            <div class="flex-1">
                <h3 class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1.5">Certification Level
                </h3>
                <div class="flex flex-wrap gap-1.5" id="filter-cert">
                    <!-- Rendered via JS -->
                </div>
            </div>
        </div>
    </div>
    <!-- Spacer for fixed filter bar -->
    <div id="filter-spacer"></div>

    <!-- HERO SECTION -->
    <section class="bg-slate-50 pt-8 pb-4">
        <div class="max-w-6xl mx-auto text-center px-4">
            <h1 class="sui-title">Activity Packages</h1>
            <p class="text-slate-500 max-w-2xl mx-auto mt-2 text-sm leading-relaxed">
                Find the right adventure for your certification level and book instantly online.
            </p>
            <div class="mt-4">
                <a href="https://balidiving.com/pricing-courses" class="inline-flex items-center text-xs font-semibold text-primary hover:text-accent transition-colors bg-white px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm">
                    <i class="fa-solid fa-graduation-cap mr-1.5 text-teal text-sm"></i> Looking for PADI Certification Courses only? View Course Prices &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- MAIN APP SECTION -->
    <section class="bg-slate-50 min-h-screen pb-20" id="pricing-app">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4">

            <!-- RESULTS METADATA -->
            <div class="mb-6 flex justify-between items-end">
                <h2 class="text-xl font-bold text-slate-800" id="results-title">All Packages</h2>
                <span class="text-sm text-slate-500 font-medium bg-slate-200 px-3 py-1 rounded-full"
                    id="results-count">0 packages</span>
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
                <h3 class="text-lg font-bold text-slate-800 mb-2">No packages found</h3>
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
                        <span>Are equipment rentals included in the price?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400"></i>
                    </button>
                    <div class="hidden mt-3 text-slate-600 text-sm leading-relaxed">
                        For most "Try Scuba", "Snorkeling", and beginner "PADI Courses", full equipment is included. For
                        "Fun Diving" (certified divers), you may need to add equipment rental during the booking process
                        if
                        you don't have your own. Please check the specific booking page details.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm">
                    <button
                        class="w-full flex items-center justify-between text-left font-semibold text-slate-800 focus:outline-none"
                        onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>What happens if I book but the weather is bad?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400"></i>
                    </button>
                    <div class="hidden mt-3 text-slate-600 text-sm leading-relaxed">
                        Safety is our top priority. If conditions are unsafe for diving or snorkeling, we will offer to
                        reschedule your activity to another day or a different, safer location in Bali. If rescheduling
                        is
                        impossible, a full refund for the affected day will be provided.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm">
                    <button
                        class="w-full flex items-center justify-between text-left font-semibold text-slate-800 focus:outline-none"
                        onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <span>Can I book now and pay later?</span>
                        <i class="fa-solid fa-chevron-down text-slate-400"></i>
                    </button>
                    <div class="hidden mt-3 text-slate-600 text-sm leading-relaxed">
                        Clicking "Book Now" will take you to our secure DiversDesk portal where you can view
                        availability
                        and secure your spot. Depending on the package and date, a deposit or full payment might be
                        required
                        to confirm the reservation.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DATA & LOGIC -->
    <script>
        const BOOKING_BASE = "https://balidiving.diversdesk.com/establishment/2db57e00-d266-4eb5-9e19-4c032269fccc/shop/activity/";

        const thumbnails = [
            "images/thumbnails/1-bali-diving.jpg", "images/thumbnails/10-bali-diving-underwater.jpg", "images/thumbnails/10-bali-diving.jpg", "images/thumbnails/11-bali-diving-underwater.jpg", "images/thumbnails/11-bali-diving.jpg", "images/thumbnails/12-bali-diving-underwater.jpg", "images/thumbnails/12-bali-diving.jpg", "images/thumbnails/13-bali-diving-underwater.jpg", "images/thumbnails/13-bali-diving.jpg", "images/thumbnails/14-bali-diving.jpg", "images/thumbnails/15-bali-diving.jpg", "images/thumbnails/16-bali-diving.jpg", "images/thumbnails/17-bali-diving.jpg", "images/thumbnails/18-bali-diving.jpg", "images/thumbnails/19-bali-diving.jpg", "images/thumbnails/2-bali-diving-underwater.jpg", "images/thumbnails/2-bali-diving.jpg", "images/thumbnails/20-bali-diving.jpg", "images/thumbnails/21-bali-diving.jpg", "images/thumbnails/22-bali-diving.jpg", "images/thumbnails/23-bali-diving.jpg", "images/thumbnails/24-bali-diving.jpg", "images/thumbnails/25-bali-diving.jpg", "images/thumbnails/26-bali-diving.jpg", "images/thumbnails/27-bali-diving.jpg", "images/thumbnails/28-bali-diving.jpg", "images/thumbnails/29-bali-diving.jpg", "images/thumbnails/3-bali-diving-underwater.jpg", "images/thumbnails/3-bali-diving.jpg", "images/thumbnails/30-bali-diving.jpg", "images/thumbnails/4-bali-diving-underwater.jpg", "images/thumbnails/4-bali-diving.jpg", "images/thumbnails/5-bali-diving-underwater.jpg", "images/thumbnails/5-bali-diving.jpg", "images/thumbnails/6-bali-diving-underwater.jpg", "images/thumbnails/6-bali-diving.jpg", "images/thumbnails/7-bali-diving-underwater.jpg", "images/thumbnails/7-bali-diving.jpg", "images/thumbnails/8-bali-diving-underwater.jpg", "images/thumbnails/8-bali-diving.jpg", "images/thumbnails/9-bali-diving-underwater.jpg", "images/thumbnails/9-bali-diving.jpg"
        ];

        const getThumbUrl = (id) => {
            let hash = 0;
            for (let i = 0; i < id.length; i++) {
                hash = id.charCodeAt(i) + ((hash << 5) - hash);
            }
            return thumbnails[Math.abs(hash) % thumbnails.length];
        };

        const activities = [
            // PADI COURSES
            { id: "padi-deep-diver", title: "PADI Deep Diver Specialty Course", shortTitle: "Deep Diver Specialty", description: "1 day / 3 dives at Padang Bai or Nusa Penida", category: "padi-course", priceIDR: 4500000, locations: ["Padang Bai", "Nusa Penida"], durationDays: 1, numberOfDives: 3, certificationRequired: "advanced", bookingUrl: BOOKING_BASE + "b95e72d0-f1b8-4e9d-8f92-4df609c3427e", highlights: ["Dive beyond 30m", "PADI certification", "3 deep dives included"] },
            { id: "padi-dad", title: "PADI Dive Against Debris Course", shortTitle: "Dive Against Debris", description: "PADI DAD Course (1 day / 2 dives)", category: "padi-course", priceIDR: 3750000, locations: ["Padang Bai"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "a96cb10d-61a0-49f3-85a5-1b921eb039d0", highlights: ["Conservation-focused", "Project AWARE certified", "Make an impact"] },
            { id: "padi-efr-rescue", title: "PADI EFR and Rescue Course", shortTitle: "EFR + Rescue", description: "3 days of intensive emergency and rescue training", category: "padi-course", priceIDR: 8800000, locations: ["Padang Bai", "Tulamben"], durationDays: 3, numberOfDives: 6, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "83f3d13d-aaf8-4dd2-8c1b-aec1526dd268", highlights: ["Lifesaving skills", "EFR + Rescue dual certification", "Step toward Divemaster"] },
            { id: "padi-aow-normal", title: "PADI Advanced Open Water Course", shortTitle: "Advanced Open Water", description: "5 dives in 2 days at Tulamben or Padang Bai", category: "padi-course", priceIDR: 6100000, locations: ["Tulamben", "Padang Bai"], durationDays: 2, numberOfDives: 5, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "f9abbc9b-a9f6-4526-a1f6-a42d1b2eddbc", highlights: ["5 adventure dives", "PADI AOW cert", "Dive to 30m"], popular: true },
            { id: "padi-owd-2d", title: "PADI Open Water Diver (2 Days)", shortTitle: "Open Water Diver — 2 Days", description: "4 dives in 2 days — the fast-track certification", category: "padi-course", priceIDR: 6800000, locations: ["Padang Bai", "Tulamben"], durationDays: 2, numberOfDives: 4, certificationRequired: "none", bookingUrl: BOOKING_BASE + "3eb9cb33-2d24-4473-8dac-f5af0a32817c", highlights: ["No experience required", "PADI OWD certification", "Dive worldwide"] },
            { id: "padi-owd-3d", title: "PADI Open Water Diver (3 Days)", shortTitle: "Open Water Diver — 3 Days", description: "4 dives in 3 days — the relaxed pace", category: "padi-course", priceIDR: 7100000, locations: ["Padang Bai", "Tulamben"], durationDays: 3, numberOfDives: 4, certificationRequired: "none", bookingUrl: BOOKING_BASE + "8f1fc6ea-5ef7-4443-9d3e-cfc2ec21cb56", highlights: ["No experience required", "Comfortable 3-day pace", "PADI OWD cert"], popular: true },
            { id: "padi-owd-referral", title: "PADI Open Water Referral (2 Days)", shortTitle: "Open Water Referral", description: "4 dives in 2 days — complete your in-progress OWD", category: "padi-course", priceIDR: 4500000, locations: ["Padang Bai", "Tulamben"], durationDays: 2, numberOfDives: 4, certificationRequired: "none", bookingUrl: BOOKING_BASE + "997c8c6e-a919-4d26-b027-88bed4dda66b", highlights: ["Finish your OWD in Bali", "Theory already done", "Open water dives only"] },
            { id: "padi-owd-upgrade", title: "PADI Open Water Upgrade from Scuba Diver", shortTitle: "OWD Upgrade", description: "2 dives in 1 day to upgrade your Scuba Diver to Open Water", category: "padi-course", priceIDR: 3800000, locations: ["Padang Bai"], durationDays: 1, numberOfDives: 2, certificationRequired: "scuba-diver", bookingUrl: BOOKING_BASE + "67469727-d440-4f3f-a957-1915eb536154", highlights: ["Quick upgrade path", "Full OWD certification", "Just 1 day"] },
            { id: "padi-ppb", title: "PADI Peak Performance Buoyancy Course", shortTitle: "Peak Performance Buoyancy", description: "1 day / 2 dives at Tulamben or Padang Bai", category: "padi-course", priceIDR: 3600000, locations: ["Tulamben", "Padang Bai"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "5dd36dae-57f5-4f68-8aa5-917c367801b6", highlights: ["Master buoyancy control", "Save air, dive longer", "PADI specialty cert"] },
            { id: "padi-aow-platinum", title: "PADI Platinum Advanced Open Water Course", shortTitle: "Platinum AOW", description: "5 dives in 2 days at Tulamben and Nusa Penida", category: "padi-course", priceIDR: 6700000, locations: ["Tulamben", "Nusa Penida"], durationDays: 2, numberOfDives: 5, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "c7ac29d0-fce5-4561-bb43-066234eeef90", highlights: ["Premium dive sites", "Tulamben + Nusa Penida", "PADI AOW certification"] },
            { id: "padi-refresher-pool", title: "PADI Refresher Program in the Pool", shortTitle: "Refresher (Pool)", description: "1 dive in a half day — knock the rust off", category: "padi-course", priceIDR: 1200000, locations: ["Pool"], durationDays: 0.5, numberOfDives: 1, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "e98b8359-38f3-4784-bf97-9904573bc507", highlights: ["Pool-based, safe environment", "Refresh your skills", "Just a half day"] },
            { id: "padi-rescue", title: "PADI Rescue Diver Course", shortTitle: "Rescue Diver", description: "PADI Rescue Diver Course — 2 days", category: "padi-course", priceIDR: 6100000, locations: ["Padang Bai", "Tulamben"], durationDays: 2, numberOfDives: 4, certificationRequired: "advanced", bookingUrl: BOOKING_BASE + "724e030e-970d-440f-887b-166c4e43fa30", highlights: ["Rescue & emergency skills", "Required for Divemaster", "Most rewarding course"] },
            { id: "padi-scuba-diver", title: "PADI Scuba Diver (2 Days)", shortTitle: "Scuba Diver", description: "2 dives in 2 days — the entry-level certification", category: "padi-course", priceIDR: 5700000, locations: ["Padang Bai"], durationDays: 2, numberOfDives: 2, certificationRequired: "none", bookingUrl: BOOKING_BASE + "4a4f5c7d-6ed5-4fc0-9fa1-51f6c25a32e2", highlights: ["Shorter than OWD", "PADI Scuba Diver cert", "Upgrade later"] },
            { id: "padi-wreck", title: "PADI Wreck Diver Course", shortTitle: "Wreck Diver", description: "2 days / 4 dives at the Tulamben USAT Liberty wreck", category: "padi-course", priceIDR: 5600000, locations: ["Tulamben"], durationDays: 2, numberOfDives: 4, certificationRequired: "advanced", bookingUrl: BOOKING_BASE + "2a915fe9-1dd4-46f6-8be5-91597cf1aba7", highlights: ["USAT Liberty WWII wreck", "PADI Wreck specialty", "Legendary dive site"] },

            // TRY SCUBA
            { id: "try-amed", title: "Try Scuba Diving — AMED", shortTitle: "Try Scuba — AMED", description: "Discover scuba at AMED, no certification needed", category: "try-scuba", priceIDR: 2100000, locations: ["AMED"], durationDays: 1, numberOfDives: 2, certificationRequired: "none", bookingUrl: BOOKING_BASE + "ac527d58-d2b0-4304-ae39-06028690c9c7", highlights: ["No experience needed", "Black sand bay", "Beginner-friendly"] },
            { id: "try-nusa-penida", title: "Try Scuba Diving — Nusa Penida", shortTitle: "Try Scuba — Nusa Penida", description: "Discover scuba at Nusa Penida, no certification needed", category: "try-scuba", priceIDR: 3150000, locations: ["Nusa Penida"], durationDays: 1, numberOfDives: 2, certificationRequired: "none", bookingUrl: BOOKING_BASE + "78aa6dbe-daaf-4b8b-8c1d-3a392f214a9a", highlights: ["Bali's iconic dive site", "Chance to see mola mola", "Beginner-friendly"], popular: true },
            { id: "try-padang-bai", title: "Try Scuba Diving — Padang Bai", shortTitle: "Try Scuba — Padang Bai", description: "Discover scuba at Padang Bai, no certification needed", category: "try-scuba", priceIDR: 2100000, locations: ["Padang Bai"], durationDays: 1, numberOfDives: 2, certificationRequired: "none", bookingUrl: BOOKING_BASE + "6bf7bf55-c4da-44b9-b97e-09dd6a63cb55", highlights: ["Calm sheltered bay", "Easy access", "Reef sharks & turtles"] },
            { id: "try-tulamben", title: "Try Scuba Diving — Tulamben Wreck", shortTitle: "Try Scuba — Tulamben", description: "Discover scuba at the Tulamben Wreck site, no certification needed", category: "try-scuba", priceIDR: 2100000, locations: ["Tulamben"], durationDays: 1, numberOfDives: 2, certificationRequired: "none", bookingUrl: BOOKING_BASE + "ba944d00-feb3-400d-b46a-2d164654b7af", highlights: ["USAT Liberty wreck nearby", "Easy shore entry", "Marine biodiversity"] },

            // DIVE SAFARI
            { id: "dive-safari-8", title: "Dive Safari — Tulamben & Nusa Penida", shortTitle: "Dive Safari (8 Dives)", description: "Multi-location dive safari across Tulamben and Nusa Penida", category: "dive-safari", priceIDR: 8150000, locations: ["Tulamben", "Nusa Penida"], durationDays: 4, numberOfDives: 8, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "0f8e4135-8a28-4473-83dd-203299d87c20", highlights: ["2 iconic dive regions", "8 dives total", "Tulamben + Nusa Penida"], popular: true },

            // FUN DIVING
            { id: "fun-amed", title: "Fun Diving — AMED", shortTitle: "Fun Diving — AMED", description: "Fun dive at AMED for certified divers", category: "fun-diving", priceIDR: 1950000, locations: ["AMED"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "16c88803-529d-41e4-91e4-70e95376a4b7", highlights: ["Macro paradise", "Black sand slopes", "Japanese WWII wreck"] },
            { id: "fun-gili-tepekong", title: "Fun Diving — Gili Tepekong / Mimpang", shortTitle: "Fun Diving — Gili Tepekong", description: "Fun dive at Gili Tepekong / Mimpang for certified divers", category: "fun-diving", priceIDR: 2250000, locations: ["Gili Tepekong"], durationDays: 1, numberOfDives: 2, certificationRequired: "advanced", bookingUrl: BOOKING_BASE + "447d4d89-5bb4-4420-a261-2c788bb8fc5e", highlights: ["Big fish encounters", "Drift dive", "Advanced site"] },
            { id: "fun-kubu-boga", title: "Fun Diving — Kubu Boga", shortTitle: "Fun Diving — Kubu Boga", description: "Fun dive at Kubu Boga for certified divers", category: "fun-diving", priceIDR: 1800000, locations: ["Kubu Boga"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "4b8dba66-5b1d-45d1-bd15-f3551b1b4083", highlights: ["Artificial reef temple gardens", "Easy conditions", "Underwater statues"] },
            { id: "fun-nusa-penida-manta", title: "Fun Diving — Nusa Penida Manta Point", shortTitle: "Fun Diving — Manta Point", description: "Fun dive at Nusa Penida Manta Point for certified divers", category: "fun-diving", priceIDR: 2950000, locations: ["Nusa Penida"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "86ee61e4-2137-4850-83a5-46f147b14f6d", highlights: ["Swim with manta rays", "World-famous site", "High-action dive"], popular: true },
            { id: "fun-package-7d-16", title: "Fun Diving Package — 7 Days / 16 Dives", shortTitle: "7-Day Package (16 Dives)", description: "Tulamben, Nusa Penida, Padang Bai — 16 dives over 7 days", category: "fun-diving", priceIDR: 13900000, locations: ["Tulamben", "Nusa Penida", "Padang Bai"], durationDays: 7, numberOfDives: 16, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "b1cab048-1475-4329-8533-c0a685f5a962", highlights: ["Bali's best 3 regions", "16 dives over 7 days", "Best per-dive value"] },
            { id: "fun-padang-bai", title: "Fun Diving — Padang Bai", shortTitle: "Fun Diving — Padang Bai", description: "Fun dive at Padang Bai for certified divers", category: "fun-diving", priceIDR: 1900000, locations: ["Padang Bai"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "4051a905-4b2c-4246-aae2-57e762239dc1", highlights: ["Sharks & turtles", "Easy boat dive", "Reliable conditions"] },
            { id: "fun-sanur", title: "Fun Diving — Sanur", shortTitle: "Fun Diving — Sanur", description: "Fun dive at Sanur Reef for certified divers", category: "fun-diving", priceIDR: 1650000, locations: ["Sanur"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "26300391-668b-405e-b69c-8964367a424d", highlights: ["Closest to Denpasar", "Affordable Bali entry", "Reef dive"] },
            { id: "fun-tulamben", title: "Fun Diving — Tulamben Wreck", shortTitle: "Fun Diving — Tulamben", description: "Fun dive at the Tulamben USAT Liberty wreck for certified divers", category: "fun-diving", priceIDR: 1800000, locations: ["Tulamben"], durationDays: 1, numberOfDives: 2, certificationRequired: "open-water", bookingUrl: BOOKING_BASE + "710cea45-4268-4317-802c-ffc21f365362", highlights: ["Famous USAT Liberty wreck", "Easy shore entry", "Top-3 Bali dive"], popular: true },

            // SNORKELING
            { id: "snorkel-amed", title: "Snorkeling Package — AMED", shortTitle: "Snorkeling — AMED", description: "Snorkeling package at AMED", category: "snorkeling", priceIDR: 1250000, locations: ["AMED"], durationDays: 1, numberOfDives: 0, certificationRequired: "none", bookingUrl: BOOKING_BASE + "d269ecf7-dd85-4402-bce5-62a899674a2c", highlights: ["Calm clear waters", "Coral gardens", "Family-friendly"] },
            { id: "snorkel-nusa-penida", title: "Snorkeling Package — Nusa Penida", shortTitle: "Snorkeling — Nusa Penida", description: "Snorkeling package at Nusa Penida", category: "snorkeling", priceIDR: 2000000, locations: ["Nusa Penida"], durationDays: 1, numberOfDives: 0, certificationRequired: "none", bookingUrl: BOOKING_BASE + "0e41a1c6-67d1-4bed-9c3f-48314930bce0", highlights: ["Iconic Nusa Penida", "Manta encounters possible", "Boat trip included"] },
            { id: "snorkel-padang-bai", title: "Snorkeling Package — Padang Bai", shortTitle: "Snorkeling — Padang Bai", description: "Snorkeling package at Padang Bai", category: "snorkeling", priceIDR: 1200000, locations: ["Padang Bai"], durationDays: 1, numberOfDives: 0, certificationRequired: "none", bookingUrl: BOOKING_BASE + "904583c4-c2fd-470a-96b1-1621c17bcba4", highlights: ["Sheltered bay", "Easy entry", "Turtles often spotted"] },
            { id: "snorkel-tulamben", title: "Snorkeling Package — Tulamben Wreck", shortTitle: "Snorkeling — Tulamben", description: "Snorkeling package at the Tulamben Wreck", category: "snorkeling", priceIDR: 1100000, locations: ["Tulamben"], durationDays: 1, numberOfDives: 0, certificationRequired: "none", bookingUrl: BOOKING_BASE + "04911d70-94a3-4bb6-bf0a-472783bb4ae8", highlights: ["See the wreck from above", "Shallow enough to snorkel", "Best snorkel value"] },
        ];

        const categories = [
            { id: 'all', name: 'All Activities' },
            { id: 'padi-course', name: 'PADI Courses' },
            { id: 'try-scuba', name: 'Try Scuba' },
            { id: 'fun-diving', name: 'Fun Diving' },
            { id: 'snorkeling', name: 'Snorkeling' },
            { id: 'dive-safari', name: 'Dive Safari' },
        ];

        const certLevels = [
            { id: 'all', name: 'Any Level' },
            { id: 'none', name: 'No Cert. Required' },
            { id: 'scuba-diver', name: 'Scuba Diver' },
            { id: 'open-water', name: 'Open Water' },
            { id: 'advanced', name: 'Advanced' },
        ];

        // State
        let state = {
            category: 'all',
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
            // Category
            const catContainer = document.getElementById('filter-category');
            catContainer.innerHTML = categories.map(cat => `
        <button 
          onclick="setFilter('category', '${cat.id}')"
          class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors duration-200 border shadow-sm
          ${state.category === cat.id
                    ? 'bg-navy text-white border-navy'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900'}">
          ${cat.name}
        </button>
      `).join('');

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
            state.category = 'all';
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

            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Checking...';
            btn.style.pointerEvents = 'none';

            // Open new window immediately to avoid popup blockers
            const newWindow = window.open('about:blank', '_self');
            if (newWindow) {
                newWindow.document.write('<div style="font-family: system-ui, sans-serif; text-align: center; margin-top: 100px; color: #475569;">Connecting to booking system...</div>');
            }

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 3000);

            fetch(url, { mode: 'no-cors', cache: 'no-store', signal: controller.signal })
                .then(() => {
                    clearTimeout(timeoutId);
                    if (newWindow) {
                        newWindow.location.href = url;
                    } else {
                        window.location.href = url;
                    }
                    btn.innerHTML = originalText;
                    btn.style.pointerEvents = 'auto';
                })
                .catch(() => {
                    // Fallback to WhatsApp if timeout or network error
                    const waText = encodeURIComponent(`Hi Bali Diving, I want to book ${packageTitle} but the online booking system seems to be unavailable. Can you help me?`);
                    const waUrl = `https://wa.me/6287861190174?text=${waText}`;

                    if (newWindow) {
                        newWindow.location.href = waUrl;
                    } else {
                        window.location.href = waUrl;
                    }
                    btn.innerHTML = originalText;
                    btn.style.pointerEvents = 'auto';
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
                const matchCat = state.category === 'all' || act.category === state.category;
                const matchCert = state.cert === 'all' || act.certificationRequired === state.cert;
                return matchCat && matchCert;
            });

            // Sort by price ascending (cheapest to most expensive)
            filtered.sort((a, b) => a.priceIDR - b.priceIDR);

            // Update Meta
            countEl.innerText = `${filtered.length} package${filtered.length !== 1 ? 's' : ''}`;
            if (state.category !== 'all') {
                titleEl.innerText = categories.find(c => c.id === state.category).name;
            } else {
                titleEl.innerText = "All Packages";
            }

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

                const thumbUrl = getThumbUrl(act.id);

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
            
            // Redirect to pricelist if referrer is not pricelist/pricing-courses
            if (!document.referrer.includes('balidiving.com/pricelist') && 
                !document.referrer.includes('balidiving.com/pricing-courses')) {
                setTimeout(() => {
                    window.location.href = 'https://balidiving.com/pricelist';
                }, 30000);
            }
        });
    </script>

    <?php include('template/footer.php'); ?>
    <?php include('template/chat.php'); ?>
    <?php include('template/pixel.php'); ?>
    <?php include('template/gtag.php'); ?>

</body>

</html>