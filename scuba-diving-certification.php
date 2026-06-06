<?php
$page = 'learn-diving';
include('01-start.php');
?>

<main class="flex-grow pt-16">

    <!-- Simple Animated Header -->
    <section
        class="relative h-[60vh] flex flex-col justify-center items-center text-center text-white px-4 overflow-hidden bg-slate-900">
        <div class="absolute inset-0 z-0">
            <img src="https://balidiving.com/images/main/header/learn-diving.jpg" alt="Scuba Analysis"
                class="w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 bg-blue-900/40 mix-blend-multiply"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto space-y-6 animate-fade-in-up">
            <span
                class="inline-block py-1 px-3 rounded-full bg-blue-500/20 border border-blue-400/30 backdrop-blur-sm text-blue-100 text-sm font-medium tracking-wider mb-2">
                PADI 5 Star Dive Center
            </span>
            <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-tight text-white drop-shadow-lg">
                Learn Diving
            </h1>
            <p class="text-lg md:text-xl text-blue-50 max-w-2xl mx-auto font-light leading-relaxed opacity-90">
                Start your adventure or advance your skills with our comprehensive range of PADI courses. Taught by
                experienced instructors in Bali's best waters.
            </p>
        </div>
    </section>

    <!-- Course Categories -->
    <section class="py-20 bg-slate-50">
        <div class="container mx-auto px-6 max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Choose Your Path</h2>
                <div class="w-20 h-1 bg-blue-500 mx-auto rounded-full"></div>
                <p class="mt-4 text-gray-600 max-w-2xl mx-auto">From your very first breath underwater to professional
                    level training.</p>
            </div>

            <!-- Interactive Depth Timeline -->
            <div class="mb-20" x-data="{ activeLevel: 0 }">
                <div class="relative pt-12 pb-20 px-4">
                    <!-- Depth Line -->
                    <div
                        class="absolute top-24 left-4 right-4 h-1 bg-gradient-to-r from-cyan-300 to-blue-800 rounded-full hidden md:block">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-8 relative z-10">

                        <!-- Level 1 -->
                        <div class="relative group cursor-pointer" @mouseenter="activeLevel = 1"
                            @mouseleave="activeLevel = 0">
                            <div class="hidden md:flex flex-col items-center mb-4">
                                <div
                                    class="w-4 h-4 rounded-full bg-cyan-300 border-4 border-white shadow-md transform transition-transform group-hover:scale-150">
                                </div>
                            </div>
                            <div
                                class="bg-white p-6 rounded-2xl shadow-lg border border-cyan-100 transform transition-all duration-300 hover:-translate-y-2 hover:shadow-cyan-200/50">
                                <div class="text-cyan-500 font-black text-4xl mb-2">12m</div>
                                <h4 class="font-bold text-gray-900 mb-1">Discover Scuba</h4>
                                <p class="text-xs text-gray-500">Max Depth</p>
                                <div class="mt-4 h-1 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-cyan-400 w-[30%]"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Level 2 -->
                        <div class="relative group cursor-pointer" @mouseenter="activeLevel = 2"
                            @mouseleave="activeLevel = 0">
                            <div class="hidden md:flex flex-col items-center mb-4">
                                <div
                                    class="w-4 h-4 rounded-full bg-cyan-500 border-4 border-white shadow-md transform transition-transform group-hover:scale-150">
                                </div>
                            </div>
                            <div
                                class="bg-white p-6 rounded-2xl shadow-lg border border-cyan-100 transform transition-all duration-300 hover:-translate-y-2 hover:shadow-cyan-300/50">
                                <div class="text-cyan-600 font-black text-4xl mb-2">18m</div>
                                <h4 class="font-bold text-gray-900 mb-1">Open Water</h4>
                                <p class="text-xs text-gray-500">Max Depth</p>
                                <div class="mt-4 h-1 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-cyan-600 w-[45%]"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Level 3 -->
                        <div class="relative group cursor-pointer" @mouseenter="activeLevel = 3"
                            @mouseleave="activeLevel = 0">
                            <div class="hidden md:flex flex-col items-center mb-4">
                                <div
                                    class="w-4 h-4 rounded-full bg-blue-500 border-4 border-white shadow-md transform transition-transform group-hover:scale-150">
                                </div>
                            </div>
                            <div
                                class="bg-white p-6 rounded-2xl shadow-lg border border-blue-100 transform transition-all duration-300 hover:-translate-y-2 hover:shadow-blue-300/50">
                                <div class="text-blue-500 font-black text-4xl mb-2">30m</div>
                                <h4 class="font-bold text-gray-900 mb-1">Advanced</h4>
                                <p class="text-xs text-gray-500">Max Depth</p>
                                <div class="mt-4 h-1 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 w-[75%]"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Level 4 -->
                        <div class="relative group cursor-pointer" @mouseenter="activeLevel = 4"
                            @mouseleave="activeLevel = 0">
                            <div class="hidden md:flex flex-col items-center mb-4">
                                <div
                                    class="w-4 h-4 rounded-full bg-blue-700 border-4 border-white shadow-md transform transition-transform group-hover:scale-150">
                                </div>
                            </div>
                            <div
                                class="bg-white p-6 rounded-2xl shadow-lg border border-blue-100 transform transition-all duration-300 hover:-translate-y-2 hover:shadow-blue-500/50">
                                <div class="text-blue-700 font-black text-4xl mb-2">40m</div>
                                <h4 class="font-bold text-gray-900 mb-1">Deep Specialty</h4>
                                <p class="text-xs text-gray-500">Max Depth</p>
                                <div class="mt-4 h-1 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-700 w-full"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Level 5 -->
                        <div class="relative group cursor-pointer" @mouseenter="activeLevel = 5"
                            @mouseleave="activeLevel = 0">
                            <div class="hidden md:flex flex-col items-center mb-4">
                                <div
                                    class="w-4 h-4 rounded-full bg-indigo-600 border-4 border-white shadow-md transform transition-transform group-hover:scale-150">
                                </div>
                            </div>
                            <div
                                class="bg-white p-6 rounded-2xl shadow-lg border border-indigo-100 transform transition-all duration-300 hover:-translate-y-2 hover:shadow-indigo-500/50 bg-gradient-to-br from-white to-indigo-50">
                                <div class="text-indigo-600 font-black text-2xl mb-3 mt-1"><i
                                        class="fas fa-infinity"></i></div>
                                <h4 class="font-bold text-gray-900 mb-1">Dive Master</h4>
                                <p class="text-xs text-gray-500">Professional Level</p>
                                <div class="mt-5 text-xs font-semibold text-indigo-500">
                                    LEAD DIVES
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">

                <!-- Card 1: Discover Scuba -->
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                    <div class="h-56 overflow-hidden relative">
                        <img src="https://balidiving.com/images/main/learn/discover-scuba-diving.jpg"
                            alt="Discover Scuba"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    <div class="p-8 flex flex-col flex-grow">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                            Discover Scuba Diving</h3>
                        <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                            Learn the basics of scuba diving, and then dive with an instructor in the best dive sites of
                            Bali. Try your new skills with confidence on an experience that counts as credit towards a
                            PADI Open Water Diver course.
                        </p>
                        <button onclick="openDsdModal()"
                            class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors text-left">
                            Select Class <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </button>
                        <a href="https://balidiving.com/pricing-courses"
                            class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full transition-colors duration-200">
                            <i class="fas fa-graduation-cap"></i> Course Packages
                        </a>
                    </div>
                </div>

                <!-- Card 2: Beginners -->
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                    <div class="h-56 overflow-hidden relative">
                        <img src="https://balidiving.com/images/main/learn/course-for-beginner.jpg" alt="Beginner Courses"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    <div class="p-8 flex flex-col flex-grow">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-cyan-600 transition-colors">
                            Courses for Beginners</h3>
                        <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                            Get your diving certificate with this Professional Association of Diving Instructors (PADI)
                            Open Water course, the most popular scuba course in the world, while diving in the Coral
                            Triangle.
                        </p>
                        <a href="https://balidiving.com/padi-Learn-beginners" target="_self"
                            class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                            Select Class <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                        <a href="https://balidiving.com/pricing-courses"
                            class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full transition-colors duration-200">
                            <i class="fas fa-graduation-cap"></i> Course Packages
                        </a>
                    </div>
                </div>

                <!-- Card 3: Advance & Specialty -->
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                    <div class="h-56 overflow-hidden relative">
                        <img src="https://balidiving.com/images/main/learn/advance-and-specialty.jpg"
                            alt="Advanced Diving"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    <div class="p-8 flex flex-col flex-grow">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition-colors">
                            Advance & Specialty</h3>
                        <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                            Gain more scuba diving experience and dive deeper with new PADI levels. A wide range of
                            specialty courses helps you increase your confidence and build your skills.
                        </p>
                        <a href="https://balidiving.com/cl-advance-balidiving" target="_self"
                            class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                            Select Class <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                        <a href="https://balidiving.com/pricing-courses"
                            class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full transition-colors duration-200">
                            <i class="fas fa-graduation-cap"></i> Course Packages
                        </a>
                    </div>
                </div>

                <!-- Card 4: Dive Master -->
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                    <div class="h-56 overflow-hidden relative">
                        <img src="https://balidiving.com/images/main/learn/dive-master.jpg" alt="Dive Master"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    <div class="p-8 flex flex-col flex-grow">
                        <h3
                            class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors">
                            Dive Master Program</h3>
                        <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                            Start your career as a Divemaster. Become a PADI professional with Bali Diving. Expand your
                            dive knowledge and hone your skills to the professional level working closely with an
                            Instructor.
                        </p>
                        <a href="https://balidiving.com/padi-Learn-divemaster" target="_self"
                            class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                            Select Class <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                        <a href="https://balidiving.com/pricing-courses"
                            class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full transition-colors duration-200">
                            <i class="fas fa-graduation-cap"></i> Course Packages
                        </a>
                    </div>
                </div>

            </div>


        </div>
    </section>

    <!-- Specific List Section (Optional but good for SEO/Details) -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 max-w-5xl">
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Popular Specialties</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i>
                            PADI Deep Diver Specialty</li>
                        <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i>
                            PADI Wreck Diver Specialty</li>
                        <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i>
                            PADI Enriched Air (Nitrox)</li>
                        <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i>
                            PADI Underwater Naturalist</li>
                        <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i>
                            PADI Drift Diver Specialty</li>
                    </ul>
                </div>
                <div class="bg-blue-50 p-8 rounded-2xl">
                    <h3 class="text-xl font-bold text-blue-900 mb-3">Why Learn with Us?</h3>
                    <p class="text-blue-800/80 mb-4 text-sm leading-relaxed">
                        To ensure the highest quality of scuba education, all PADI dive courses at Bali Diving are
                        taught by highly qualified, renewed, and insured PADI Instructors with years of teaching
                        experience.
                    </p>
                    <p class="text-blue-800/80 text-sm leading-relaxed">
                        We minimize classroom sessions by encouraging you to complete knowledge reviews online or via
                        manual before arrival.
                    </p>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- DSD Clarification Modal -->
<div id="dsdModal" class="fixed inset-0 z-[99999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity opacity-0" id="dsdModalBackdrop">
    </div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                id="dsdModalPanel">

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-info text-blue-600 text-lg"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-xl font-serif font-medium leading-6 text-slate-800" id="modal-title">A
                                Gentle
                                Note on Your Journey</h3>
                            <div class="mt-4">
                                <p class="text-slate-600 text-sm leading-relaxed">
                                    We kindly invite you to note that the <strong>Discover Scuba Diving</strong>
                                    experience is seamlessly integrated into our esteemed <strong>Try Scuba
                                        Diving</strong> program.
                                </p>
                                <p class="text-slate-600 text-sm leading-relaxed mt-3">
                                    This curated session is designed specifically for adventurers wishing to experience
                                    the underwater realm without prior certification. May you find wonder in the depths.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <a href="https://balidiving.com/try-scuba-diving"
                        class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-all hover:shadow-lg">
                        Continue to Try Diving
                    </a>
                    <button type="button" onclick="closeDsdModal()"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-colors">
                        Select Another Course
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openDsdModal() {
        const modal = document.getElementById('dsdModal');
        const backdrop = document.getElementById('dsdModalBackdrop');
        const panel = document.getElementById('dsdModalPanel');

        modal.classList.remove('hidden');

        // Slight delay for animation
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeDsdModal() {
        const modal = document.getElementById('dsdModal');
        const backdrop = document.getElementById('dsdModalBackdrop');
        const panel = document.getElementById('dsdModalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>

<?php include('03-end.php') ?>