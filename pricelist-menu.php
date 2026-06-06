<?php include('01-start.php') ?>

<!-- Custom Styles for Pricelist Menu -->
<style>
    .card-zoom-image {
        transition: transform 0.5s ease;
    }

    .group:hover .card-zoom-image {
        transform: scale(1.08);
    }

    .fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Stagger animations for cards */
    .grid-container>a:nth-child(1) {
        animation-delay: 0.1s;
    }

    .grid-container>a:nth-child(2) {
        animation-delay: 0.2s;
    }

    .grid-container>a:nth-child(3) {
        animation-delay: 0.3s;
    }

    .grid-container>a:nth-child(4) {
        animation-delay: 0.4s;
    }

    .grid-container>a:nth-child(5) {
        animation-delay: 0.5s;
    }

    .grid-container>a:nth-child(6) {
        animation-delay: 0.6s;
    }

    .grid-container>a:nth-child(n+7) {
        animation-delay: 0.7s;
    }

    /* cap delay */
</style>

<?php
// Placeholder Data (Generating exactly 8 Fun Dives, 17 Courses, 4 Snorkeling)

$fun_dives = [
    ['name' => 'Fun Diving - Padang Bai', 'price' => '1.900.000', 'location' => 'Padang Bai', 'badge' => 'Certified', 'img' => 'Padang+Bai', 'url' => 'https://balidiving.diversdesk.com/product/4051a905-4b2c-4246-aae2-57e762239dc1'],
    ['name' => 'Fun Diving - AMED', 'price' => '1.950.000', 'location' => 'Amed', 'badge' => 'Certified', 'img' => 'Amed', 'url' => 'https://balidiving.diversdesk.com/product/16c88803-529d-41e4-91e4-70e95376a4b7'],
    ['name' => 'Fun Diving - Tulamben Wreck', 'price' => '1.900.000', 'location' => 'Tulamben', 'badge' => 'Certified', 'img' => 'Tulamben', 'url' => '#'],
    ['name' => 'Fun Diving - Nusa Penida (Manta)', 'price' => '2.500.000', 'location' => 'Nusa Penida', 'badge' => 'Certified', 'img' => 'Nusa+Penida', 'url' => '#'],
    ['name' => 'Fun Diving - Nusa Lembongan', 'price' => '2.400.000', 'location' => 'Lembongan', 'badge' => 'Certified', 'img' => 'Lembongan', 'url' => '#'],
    ['name' => 'Fun Diving - Sanur Reef', 'price' => '1.500.000', 'location' => 'Sanur', 'badge' => 'Certified', 'img' => 'Sanur', 'url' => '#'],
    ['name' => 'Fun Diving - Tepekong / Mimpang', 'price' => '2.100.000', 'location' => 'Tepekong', 'badge' => 'Certified', 'img' => 'Tepekong', 'url' => '#'],
    ['name' => 'Fun Diving - Menjangan Island', 'price' => '2.700.000', 'location' => 'Menjangan', 'badge' => 'Certified', 'img' => 'Menjangan', 'url' => '#'],
];

$courses = [
    ['name' => 'PADI - Refresher Program (POOL)', 'price' => '1.200.000', 'location' => 'Pool', 'badge' => 'Refresher', 'img' => 'Pool+Refresher', 'url' => 'https://balidiving.diversdesk.com/product/e98b8359-38f3-4784-bf97-9904573bc507'],
    ['name' => 'Try Scuba Diving - AMED', 'price' => '2.100.000', 'location' => 'Amed', 'badge' => 'Beginner', 'img' => 'Try+Scuba', 'url' => 'https://balidiving.diversdesk.com/product/ac527d58-d2b0-4304-ae39-06028690c9c7'],
    ['name' => 'Try Scuba Diving - Padang Bai', 'price' => '2.000.000', 'location' => 'Padang Bai', 'badge' => 'Beginner', 'img' => 'Try+Scuba', 'url' => '#'],
    ['name' => 'Try Scuba Diving - Tulamben', 'price' => '2.100.000', 'location' => 'Tulamben', 'badge' => 'Beginner', 'img' => 'Try+Scuba', 'url' => '#'],
    ['name' => 'PADI Open Water Diver', 'price' => '5.900.000', 'location' => 'Sanur/Tulamben', 'badge' => 'Certification', 'img' => 'Open+Water', 'url' => '#'],
    ['name' => 'PADI Advanced Open Water', 'price' => '5.500.000', 'location' => 'Various', 'badge' => 'Advanced', 'img' => 'Advanced', 'url' => '#'],
    ['name' => 'PADI Rescue Diver', 'price' => '5.500.000', 'location' => 'Various', 'badge' => 'Advanced', 'img' => 'Rescue', 'url' => '#'],
    ['name' => 'PADI Divemaster', 'price' => '15.000.000', 'location' => 'Various', 'badge' => 'Pro Level', 'img' => 'Divemaster', 'url' => '#'],
    ['name' => 'Enriched Air Diver (Nitrox)', 'price' => '3.500.000', 'location' => 'Classroom', 'badge' => 'Specialty', 'img' => 'Nitrox', 'url' => '#'],
    ['name' => 'Deep Diver Specialty', 'price' => '4.200.000', 'location' => 'Various', 'badge' => 'Specialty', 'img' => 'Deep+Diver', 'url' => '#'],
    ['name' => 'Wreck Diver Specialty', 'price' => '4.200.000', 'location' => 'Tulamben', 'badge' => 'Specialty', 'img' => 'Wreck+Diver', 'url' => '#'],
    ['name' => 'Night Diver Specialty', 'price' => '4.500.000', 'location' => 'Various', 'badge' => 'Specialty', 'img' => 'Night+Diver', 'url' => '#'],
    ['name' => 'Peak Performance Buoyancy', 'price' => '3.500.000', 'location' => 'Various', 'badge' => 'Specialty', 'img' => 'Buoyancy', 'url' => '#'],
    ['name' => 'Digital Underwater Photographer', 'price' => '3.800.000', 'location' => 'Various', 'badge' => 'Specialty', 'img' => 'Photography', 'url' => '#'],
    ['name' => 'Emergency First Response (EFR)', 'price' => '3.200.000', 'location' => 'Classroom', 'badge' => 'Medical', 'img' => 'EFR', 'url' => '#'],
    ['name' => 'Discover Local Diving', 'price' => '1.500.000', 'location' => 'Sanur', 'badge' => 'Beginner', 'img' => 'Local+Diving', 'url' => '#'],
    ['name' => 'PADI Scuba Diver', 'price' => '4.500.000', 'location' => 'Various', 'badge' => 'Certification', 'img' => 'Scuba+Diver', 'url' => '#'],
];

$snorkels = [
    ['name' => 'Snorkeling Package Tulamben Wreck', 'price' => '1.100.000', 'location' => 'Tulamben', 'badge' => 'Snorkel', 'img' => 'Tulamben', 'url' => 'https://balidiving.diversdesk.com/product/04911d70-94a3-4bb6-bf0a-472783bb4ae8'],
    ['name' => 'Snorkeling Package Padang Bai', 'price' => '1.200.000', 'location' => 'Padang Bai', 'badge' => 'Snorkel', 'img' => 'Padang+Bai', 'url' => 'https://balidiving.diversdesk.com/product/904583c4-c2fd-470a-96b1-1621c17bcba4'],
    ['name' => 'Snorkeling Package Nusa Penida', 'price' => '1.800.000', 'location' => 'Nusa Penida', 'badge' => 'Snorkel', 'img' => 'Nusa+Penida', 'url' => '#'],
    ['name' => 'Snorkeling Package Amed', 'price' => '1.150.000', 'location' => 'Amed', 'badge' => 'Snorkel', 'img' => 'Amed', 'url' => '#'],
];
?>

<main class="min-h-screen bg-slate-50 pt-24 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="text-center mb-16 fade-in-up" style="animation-delay: 0.1s;">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-6 shadow-sm">
                <i class="fas fa-th-large text-2xl"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-navy mb-4 tracking-tight">Pricelist Diving Menu</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Discover our full selection of top-tier diving packages: from fun dives and full certification courses
                to breathtaking snorkeling trips.
            </p>
        </div>

        <!-- 1. Fun Diving Section -->
        <div id="fun-diving" class="mb-20 fade-in-up scroll-mt-32" style="animation-delay: 0.2s;">
            <div class="flex items-end justify-between mb-8 border-b-2 border-gray-200 pb-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-water text-blue-500"></i> Fun Diving
                    </h2>
                    <p class="text-gray-500 text-sm mt-2">All <?= count($fun_dives) ?> Packages for Certified Divers.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 grid-container">
                <?php foreach ($fun_dives as $dive): ?>
                    <a href="<?= $dive['url'] ?>"
                        target="<?= $dive['url'] === '#' ? '_self' : '_blank' ?>"
                        class="group fade-in-up bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col">
                        <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100 relative border-b border-gray-100">
                            <img src="https://placehold.co/600x450/e2e8f0/475569?text=<?= $dive['img'] ?>"
                                alt="<?= $dive['location'] ?>" class="w-full h-full object-cover card-zoom-image"
                                loading="lazy">
                            <div
                                class="absolute top-3 left-3 bg-navy/90 backdrop-blur text-white text-[10px] uppercase tracking-wider font-bold px-3 py-1.5 rounded-full shadow-sm">
                                <?= $dive['badge'] ?>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3
                                class="text-[15px] font-bold text-navy group-hover:text-blue-600 transition-colors leading-snug mb-3 min-h-[44px]">
                                <?= $dive['name'] ?>
                            </h3>
                            <div class="flex items-center gap-2 mb-4 text-gray-500 text-xs">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                <span><?= $dive['location'] ?> • 2x Dives</span>
                            </div>
                            <div class="mt-auto flex items-center justify-between">
                                <span class="text-blue-600 font-extrabold text-sm">IDR <?= $dive['price'] ?></span>
                                <span
                                    class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2. Diving Course Section -->
        <div id="course" class="mb-20 fade-in-up scroll-mt-32" style="animation-delay: 0.3s;">
            <div class="flex items-end justify-between mb-8 border-b-2 border-gray-200 pb-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-graduation-cap text-orange-500"></i> Diving Course
                    </h2>
                    <p class="text-gray-500 text-sm mt-2">All <?= count($courses) ?> Certification & Learning Packages.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 grid-container">
                <?php foreach ($courses as $course): ?>
                    <a href="<?= $course['url'] ?>"
                        target="<?= $course['url'] === '#' ? '_self' : '_blank' ?>"
                        class="group fade-in-up bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col">
                        <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100 relative border-b border-gray-100">
                            <img src="https://placehold.co/600x450/ffedd5/ea580c?text=<?= $course['img'] ?>"
                                alt="<?= $course['location'] ?>" class="w-full h-full object-cover card-zoom-image"
                                loading="lazy">
                            <div
                                class="absolute top-3 left-3 bg-orange-600/90 backdrop-blur text-white text-[10px] uppercase tracking-wider font-bold px-3 py-1.5 rounded-full shadow-sm">
                                <?= $course['badge'] ?>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3
                                class="text-[15px] font-bold text-navy group-hover:text-orange-600 transition-colors leading-snug mb-3 min-h-[44px]">
                                <?= $course['name'] ?>
                            </h3>
                            <div class="flex items-center gap-2 mb-4 text-gray-500 text-xs">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                <span><?= $course['location'] ?></span>
                            </div>
                            <div class="mt-auto flex items-center justify-between">
                                <span class="text-orange-600 font-extrabold text-sm">IDR <?= $course['price'] ?></span>
                                <span
                                    class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 3. Snorkeling Section -->
        <div id="snorkeling" class="mb-10 fade-in-up scroll-mt-32" style="animation-delay: 0.4s;">
            <div class="flex items-end justify-between mb-8 border-b-2 border-gray-200 pb-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-mask text-teal-500"></i> Snorkeling
                    </h2>
                    <p class="text-gray-500 text-sm mt-2">All <?= count($snorkels) ?> Beautiful Surface Exploration
                        Packages.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 grid-container">
                <?php foreach ($snorkels as $snrk): ?>
                    <a href="<?= $snrk['url'] ?>"
                        target="<?= $snrk['url'] === '#' ? '_self' : '_blank' ?>"
                        class="group fade-in-up bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col">
                        <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100 relative border-b border-gray-100">
                            <img src="https://placehold.co/600x450/ccfbf1/0f766e?text=<?= $snrk['img'] ?>"
                                alt="<?= $snrk['location'] ?>" class="w-full h-full object-cover card-zoom-image"
                                loading="lazy">
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3
                                class="text-[15px] font-bold text-navy group-hover:text-teal-600 transition-colors leading-snug mb-3 min-h-[44px]">
                                <?= $snrk['name'] ?>
                            </h3>
                            <div class="flex items-center gap-2 mb-4 text-gray-500 text-xs">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                <span><?= $snrk['location'] ?> • Snorkeling</span>
                            </div>
                            <div class="mt-auto flex items-center justify-between">
                                <span class="text-teal-600 font-extrabold text-sm">IDR <?= $snrk['price'] ?></span>
                                <span
                                    class="w-8 h-8 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition-colors">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Floating Quick Menu -->
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-white/80 backdrop-blur-lg px-2 sm:px-6 py-2 sm:py-3 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-white/50 flex items-center gap-2 sm:gap-6 scale-[0.9] sm:scale-100 transition-all origin-bottom">
            <a href="#fun-diving" class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2 text-blue-600 hover:text-blue-800 transition-colors group px-2 sm:px-0">
                <div class="w-10 h-10 sm:w-8 sm:h-8 rounded-full bg-blue-50/80 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm">
                    <i class="fas fa-water text-lg sm:text-sm"></i>
                </div>
                <span class="text-[11px] sm:text-sm font-bold whitespace-nowrap">Fun Dive</span>
            </a>
            <div class="w-[1px] h-8 sm:h-8 bg-gray-200"></div>
            <a href="#course" class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2 text-orange-600 hover:text-orange-800 transition-colors group px-2 sm:px-0">
                <div class="w-10 h-10 sm:w-8 sm:h-8 rounded-full bg-orange-50/80 flex items-center justify-center group-hover:bg-orange-100 transition-colors shadow-sm">
                    <i class="fas fa-graduation-cap text-lg sm:text-sm"></i>
                </div>
                <span class="text-[11px] sm:text-sm font-bold whitespace-nowrap">Course</span>
            </a>
            <div class="w-[1px] h-8 sm:h-8 bg-gray-200"></div>
            <a href="#snorkeling" class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2 text-teal-600 hover:text-teal-800 transition-colors group px-2 sm:px-0">
                <div class="w-10 h-10 sm:w-8 sm:h-8 rounded-full bg-teal-50/80 flex items-center justify-center group-hover:bg-teal-100 transition-colors shadow-sm">
                    <i class="fas fa-mask text-lg sm:text-sm"></i>
                </div>
                <span class="text-[11px] sm:text-sm font-bold whitespace-nowrap">Snorkel</span>
            </a>
        </div>

    </div>
</main>

<?php include('03-end.php') ?>