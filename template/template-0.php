<?php
// Panggil file manager SEO kita
require_once 'seo_manager.php';

// Tentukan pengenal untuk halaman ini.
$page = $_GET['page'] ?? 'home'; // Jika parameter 'page' tidak ada, anggap ini 'home'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bali Diving - Scuba Diving Adventures in Bali | Best Dive Sites</title>
    <?php
    // Panggil fungsi untuk mencetak semua tag SEO yang relevan untuk halaman ini.
    echo generate_seo_tags($page);
    ?>
    <link rel="icon" href="images/bali-diving-logo.svg" type="image/svg+xml">
    <meta name="description" content="Experience world-class scuba diving in Bali with Bali Diving. Explore vibrant coral reefs, encounter manta rays, and discover underwater wonders. Book your diving adventure today!">
    <meta name="keywords" content="Bali diving, scuba diving Bali, dive sites Bali, underwater adventure, coral reefs, manta rays, diving tours">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3552c8',
                        secondary: '#f23d4e',
                        accent: '#0070d3',
                        teal: '#23a0b4',
                        gold: '#eebe35',
                        lightblue: '#a2d2fa',
                        navy: '#063c7f'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="font-sans">
    
<?php include('nav.php')?>

    <section id="dynamic-content-section" class="bg-slate-50 py-16 md:py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <?php
                    // --- BLOK PHP YANG DIMODIFIKASI ---
                    // 1. Tentukan variabel untuk informasi situs
                    $siteName = 'Bali Diving';
                    $separator = '|';

                    // 2. Dapatkan nama file saat ini
                    $filename = basename($_SERVER['PHP_SELF'], '.php');
                    
                    // 3. Logika untuk menentukan judul
                    $fullPageTitle = '';
                    if ($filename == 'index' || $filename == 'home') {
                        // Judul khusus untuk halaman beranda
                        $fullPageTitle = 'Your Ultimate Underwater Journey Starts Here';
                    } else {
                        // Judul otomatis untuk halaman lain, dikombinasikan dengan nama situs
                        $pageName = ucwords(str_replace(['-', '_'], ' ', $filename));
                        $fullPageTitle = $pageName . ' ' . $separator . ' ' . $siteName;
                    }
                ?>
                <h2 class="text-3xl md:text-4xl font-bold text-navy mb-4"><?php echo htmlspecialchars($fullPageTitle); ?></h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    We're passionate about sharing Bali's underwater marvels. Our commitment goes beyond just diving; we create safe, unforgettable, and eco-conscious adventures for everyone.
                </p>
            </div>

            <div id="cardSlider" class="max-w-5xl mx-auto mb-16 relative shadow-lg rounded-xl overflow-hidden">
                <div id="cardSliderImages" class="flex transition-transform duration-500 ease-in-out">
                    </div>
                <button id="cardSliderPrev" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white/70 hover:bg-white text-navy rounded-full w-10 h-10 flex items-center justify-center transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="cardSliderNext" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white/70 hover:bg-white text-navy rounded-full w-10 h-10 flex items-center justify-center transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-in-out text-center flex flex-col">
                    <div class="flex-grow">
                        <div class="text-primary text-5xl mb-6 inline-block"><i class="fas fa-shield-alt"></i></div>
                        <h3 class="text-2xl font-bold text-navy mb-4">Uncompromising Safety</h3>
                        <p class="text-gray-600">Our PADI-certified divemasters follow strict safety protocols. We use top-tier, regularly serviced equipment and maintain small group sizes for personalized attention.</p>
                    </div>
                    <div class="mt-6"><button onclick="showMoreInfo('Uncompromising Safety')" class="bg-primary text-white font-semibold py-2 px-6 rounded-full hover:bg-opacity-90 transition-colors">See More</button></div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-in-out text-center flex flex-col">
                    <div class="flex-grow">
                        <div class="text-teal text-5xl mb-6 inline-block"><i class="fas fa-compass"></i></div>
                        <h3 class="text-2xl font-bold text-navy mb-4">Expert Local Guides</h3>
                        <p class="text-gray-600">Dive with local experts who know Bali's waters intimately. They'll guide you to the best spots, point out unique marine life, and share their deep knowledge of the ocean's secrets.</p>
                    </div>
                    <div class="mt-6"><button onclick="showMoreInfo('Expert Local Guides')" class="bg-primary text-white font-semibold py-2 px-6 rounded-full hover:bg-opacity-90 transition-colors">See More</button></div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-in-out text-center flex flex-col">
                    <div class="flex-grow">
                        <div class="text-gold text-5xl mb-6 inline-block"><i class="fas fa-fish-fins"></i></div>
                        <h3 class="text-2xl font-bold text-navy mb-4">Sustainable Adventures</h3>
                        <p class="text-gray-600">We are dedicated to protecting the reefs we love. We practice and promote responsible diving to ensure Bali's underwater paradise thrives for generations to come.</p>
                    </div>
                     <div class="mt-6"><button onclick="showMoreInfo('Sustainable Adventures')" class="bg-primary text-white font-semibold py-2 px-6 rounded-full hover:bg-opacity-90 transition-colors">See More</button></div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include('footer.php')?>

    <div id="infoOffCanvas" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col">
        <div class="flex justify-between items-center p-4 border-b bg-gray-50">
            <h2 id="offCanvasTitle" class="text-xl font-semibold text-navy"></h2>
            <button id="closeOffCanvasBtn" class="text-gray-500 hover:text-gray-800 transition-colors"><i class="fas fa-times fa-lg"></i></button>
        </div>
        <div id="offCanvasContent" class="flex-grow p-6 overflow-y-auto text-gray-700 leading-relaxed"></div>
    </div>
    <div id="offCanvasOverlay" class="fixed inset-0 bg-black bg-opacity-60 hidden z-40"></div>
    
    <script>
        <?php
            $files = glob('images/slider/*.webp');
            if (empty($files)) { $files = glob('images/slider/*.jpg'); }
            if (empty($files)) { $files = glob('images/slider/*.png'); }
            $imagePaths = [];
            foreach ($files as $file) { $imagePaths[] = basename($file); }
            $imageNamesJSON = json_encode($imagePaths);
            echo "const imageNames = " . $imageNamesJSON . ";";
        ?>
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof imageNames !== 'undefined' && imageNames.length > 0) {
                const sliderImagesContainer = document.getElementById('cardSliderImages');
                const prevBtn = document.getElementById('cardSliderPrev');
                const nextBtn = document.getElementById('cardSliderNext');

                imageNames.forEach(imageName => {
                    const slide = document.createElement('div');
                    slide.className = 'w-full flex-shrink-0';
                    const img = document.createElement('img');
                    img.className = 'w-full h-[512px] object-cover';
                    img.src = `images/slider/${imageName}`;
                    img.alt = 'Diving in Bali';
                    slide.appendChild(img);
                    sliderImagesContainer.appendChild(slide);
                });

                let currentIndex = 0;
                const totalSlides = imageNames.length;
                let autoSlideInterval;

                const goToSlide = (index) => {
                    if (index < 0) { currentIndex = totalSlides - 1; } 
                    else if (index >= totalSlides) { currentIndex = 0; } 
                    else { currentIndex = index; }
                    sliderImagesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
                };

                const startAutoSlide = () => { autoSlideInterval = setInterval(() => { goToSlide(currentIndex + 1); }, 5000); };
                const resetAutoSlide = () => { clearInterval(autoSlideInterval); startAutoSlide(); };

                nextBtn.addEventListener('click', () => { goToSlide(currentIndex + 1); resetAutoSlide(); });
                prevBtn.addEventListener('click', () => { goToSlide(currentIndex - 1); resetAutoSlide(); });

                startAutoSlide();
            } else {
                const sliderElement = document.getElementById('cardSlider');
                if(sliderElement) sliderElement.style.display = 'none';
            }
        });
    </script>

    <script>
        const infoOffCanvas = document.getElementById('infoOffCanvas');
        const offCanvasOverlay = document.getElementById('offCanvasOverlay');
        const closeOffCanvasBtn = document.getElementById('closeOffCanvasBtn');
        const offCanvasTitle = document.getElementById('offCanvasTitle');
        const offCanvasContent = document.getElementById('offCanvasContent');
        const offCanvasData = { 'Uncompromising Safety': { title: 'Our Commitment to Safety', content: `<p class="mb-4">Safety is our absolute number one priority. We believe that a great dive is a safe dive. Here’s how we ensure your peace of mind:</p><ul class="list-disc list-inside space-y-3"><li><strong>PADI Certified Professionals:</strong> All our dive guides and instructors are active PADI-certified professionals, trained in the latest safety and rescue techniques.</li><li><strong>Well-Maintained Equipment:</strong> Our rental gear from top brands like Scubapro and Aqualung is serviced meticulously and regularly, far exceeding industry standards.</li><li><strong>Small Groups, Big Care:</strong> We maintain a low diver-to-guide ratio (max 4:1) to ensure personalized attention and quick assistance if needed.</li><li><strong>Emergency Preparedness:</strong> All our boats are equipped with first aid kits, emergency oxygen, and communication devices. Our staff is trained in emergency response protocols.</li></ul>`}, 'Expert Local Guides': { title: 'Dive with Local Experts', content: `<p class="mb-4">Our guides aren't just experts; they're locals who have grown up with these waters. This intimate knowledge translates into a richer, safer, and more exciting dive experience for you.</p><ul class="list-disc list-inside space-y-3"><li><strong>Unmatched Knowledge:</strong> They know the secret spots, the best times to visit specific sites, and where to find rare and unique marine life that others might miss.</li><li><strong>Marine Life Spotters:</strong> With their trained eyes, they can point out camouflaged creatures like pygmy seahorses, ornate ghost pipefish, and frogfish.</li><li><strong>Cultural Insights:</strong> They love to share stories about Bali, its culture, and its connection to the ocean, adding another layer to your adventure.</li><li><strong>Passionate and Friendly:</strong> Their passion for diving is infectious, and they are dedicated to making your trip unforgettable.</li></ul>`}, 'Sustainable Adventures': { title: 'Protecting Paradise Together', content: `<p class="mb-4">We are privileged to call Bali's incredible underwater world our office. We have a profound responsibility to protect it. We are committed to sustainable and responsible diving practices.</p><ul class="list-disc list-inside space-y-3"><li><strong>No-Touch Policy:</strong> We strictly enforce a no-touch, no-take policy for all marine life and corals. Look with your eyes, not your hands.</li><li><strong>Buoyancy Control:</strong> We provide buoyancy workshops and tips to ensure divers do not accidentally damage fragile coral reefs.</li><li><strong>Reef-Safe Practices:</strong> We educate our guests on using reef-safe sunscreens and minimizing waste on our boats.</li><li><strong>Community Engagement:</strong> We actively participate in and organize local beach and reef cleanup initiatives. When you dive with us, you support a healthier ocean.</li></ul>`}};
        const openOffCanvas = () => { infoOffCanvas.classList.remove('translate-x-full'); offCanvasOverlay.classList.remove('hidden'); document.body.style.overflow = 'hidden'; };
        const closeOffCanvas = () => { infoOffCanvas.classList.add('translate-x-full'); offCanvasOverlay.classList.add('hidden'); document.body.style.overflow = ''; };
        closeOffCanvasBtn.addEventListener('click', closeOffCanvas);
        offCanvasOverlay.addEventListener('click', closeOffCanvas);
        function showMoreInfo(topic) { const data = offCanvasData[topic]; if (data) { offCanvasTitle.innerText = data.title; offCanvasContent.innerHTML = data.content; openOffCanvas(); } }
    </script>

    <?php include ('chat.php') ?>

</body>
</html>