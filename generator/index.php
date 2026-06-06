<?php
/**
 * generator-enhanced.php
 * Multi-File PHP Generator (Enhanced Premium Version) — by Subhi.me
 *
 * NEW FEATURES:
 * - Premium spacing & design
 * - Unsplash API integration for relevant images
 * - Modern SEO practices (2026)
 * - Prominent "Check Your Plan" CTA
 * - Subtle blog reference
 * - Enhanced readability & structure
 */

mb_internal_encoding("UTF-8");

// ================== SETTINGS ==================
$OUTPUT_DIR_DEFAULT = __DIR__;
$SITE_NAME = "Bali Diving";
$AUTHOR_NAME = "Bali Diving Team";
$PUBLIC_BASE_URL = "https://www.balidiving.com/recommendations";
$BASE_URL_HINT = "";
$SITEMAP_PATH = $OUTPUT_DIR_DEFAULT . DIRECTORY_SEPARATOR . "sitemap.xml";

// Complete Bali Diving Image Collection
$BALI_DIVING_IMAGES = [
    "https://balidiving.com/images/thumbnails/1-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/10-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/11-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/12-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/13-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/14-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/15-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/16-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/17-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/18-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/19-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/2-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/20-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/21-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/22-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/23-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/24-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/25-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/26-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/27-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/28-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/29-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/3-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/30-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/4-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/5-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/6-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/7-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/8-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/9-bali-diving.jpg",
];

// Thumbnail pool for related grid (smaller selection for variety)
$THUMBS = $BALI_DIVING_IMAGES;

// ================== BALI DIVING IMAGE FUNCTIONS ==================
// Get random image for content sections
function getContentImage($keyword = '')
{
    global $BALI_DIVING_IMAGES;
    $randomIndex = array_rand($BALI_DIVING_IMAGES);
    return $BALI_DIVING_IMAGES[$randomIndex];
}

// Get random featured/hero image
function getFeaturedImage($keyword = '')
{
    global $BALI_DIVING_IMAGES;
    $randomIndex = array_rand($BALI_DIVING_IMAGES);
    return $BALI_DIVING_IMAGES[$randomIndex];
}

// ================== HELPERS ==================
function slugify($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $trans = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    if ($trans !== false)
        $text = $trans;
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return $text ?: 'page';
}

function esc($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function metaDescription($kw)
{
    return "Discover {$kw} in Bali: sites, conditions, safety tips, pricing insights, and how to plan an unforgettable underwater experience with {$kw}.";
}

function h1Line($kw)
{
    return "Experience " . $kw . " in Bali";
}
function subheading($kw)
{
    return "Plan, dive, and thrive with practical tips for " . $kw . " enthusiasts.";
}

function paragraph($kw)
{
    return "Thinking about {$kw} in Bali? This guide covers real-world conditions, how to choose sites by skill level, safety protocols, seasonality, and the must-know logistics to maximize your underwater time. We keep it practical, concise, and field-tested.";
}

function bullets($kw)
{
    return [
        "Best months for {$kw} and what to expect underwater.",
        "Entry requirements, depth profiles, and current awareness.",
        "Equipment checklist tailored for {$kw} scenarios.",
        "Local etiquette, conservation practices, and reef-safe habits.",
        "Budgeting tips: where to save vs. where to invest."
    ];
}

// NEW: Detailed content sections
function getDetailedSections($kw)
{
    return [
        'preparation' => [
            'title' => 'Preparation & Planning',
            'icon' => 'fa-clipboard-check',
            'content' => "Planning your {$kw} adventure requires attention to detail. Check certification requirements, book in advance during peak season (April-October), and consider weather patterns. Most operators require 24-48 hours notice for cancellations. Review your insurance coverage and ensure your dive medical is current."
        ],
        'what_to_expect' => [
            'title' => 'What to Expect',
            'icon' => 'fa-binoculars',
            'content' => "During {$kw}, you'll encounter diverse marine life, varying visibility conditions (15-40 meters typical), and water temperatures around 26-29°C. Most sites feature gentle to moderate currents, making them accessible for intermediate divers. Expect vibrant coral formations, tropical fish species, and occasional encounters with larger pelagics."
        ],
        'safety' => [
            'title' => 'Safety & Best Practices',
            'icon' => 'fa-shield-halved',
            'content' => "Always dive within your certification limits, maintain proper buoyancy control, and follow the buddy system. Conduct pre-dive safety checks, monitor air supply regularly, and respect marine life by maintaining safe distances. Surface marker buoys are essential for Bali's busier sites. Never dive if feeling unwell or fatigued."
        ],
        'gear' => [
            'title' => 'Essential Gear Guide',
            'icon' => 'fa-gear',
            'content' => "For {$kw}, pack a 3mm wetsuit (5mm for cooler months), reef-safe sunscreen, dive computer, underwater camera, and surface marker buoy. Most operators provide BCD, regulators, and tanks—but bringing your own mask ensures perfect fit and comfort throughout your dives."
        ]
    ];
}

function faqPairs($kw)
{
    return [
        [
            "Is {$kw} suitable for beginners?",
            "Yes, with proper guidance and site selection. We match conditions and depth to your experience and maintain conservative safety margins."
        ],
        [
            "What's the ideal season for {$kw} in Bali?",
            "Generally April–November offers calmer seas, but micro-conditions vary by site. Always check the latest local forecast and briefings."
        ],
        [
            "How much does {$kw} typically cost?",
            "Costs vary by site access, boat charter, gear rental, and guiding. Transparent pricing helps plan ahead—ask for itemized quotes."
        ],
        [
            "Do I need special gear for {$kw}?",
            "A streamlined setup usually works. Add reef-safe sunscreen, surface signaling devices, and a computer with conservative ascent profiles."
        ],
        [
            "Can I combine {$kw} with a certification course?",
            "Absolutely. Many divers bundle guided dives with skill upgrades to accelerate learning while exploring Bali's signature spots."
        ]
    ];
}

function jsonLdArticle($title, $desc, $url, $author, $imageUrl)
{
    $now = date('c');
    $data = [
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $title,
        "description" => $desc,
        "image" => $imageUrl,
        "author" => ["@type" => "Organization", "name" => $author],
        "publisher" => [
            "@type" => "Organization",
            "name" => "Bali Diving",
            "logo" => ["@type" => "ImageObject", "url" => "https://www.balidiving.com/logo.png"]
        ],
        "datePublished" => $now,
        "dateModified" => $now,
        "mainEntityOfPage" => ["@type" => "WebPage", "@id" => $url ?: ""],
    ];
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

function jsonLdFAQ($pairs)
{
    $items = [];
    foreach ($pairs as $p) {
        $items[] = [
            "@type" => "Question",
            "name" => $p[0],
            "acceptedAnswer" => ["@type" => "Answer", "text" => $p[1]]
        ];
    }
    $data = ["@context" => "https://schema.org", "@type" => "FAQPage", "mainEntity" => $items];
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

// Build "Related Articles" grid
function buildRelatedGrid($allPages, $currentSlug, $thumbs, $limit = 4)
{
    $cards = [];
    $i = 0;
    foreach ($allPages as $p) {
        if ($p['slug'] === $currentSlug)
            continue;
        $thumb = $thumbs[$i % count($thumbs)];
        $cards[] = '
      <a href="' . esc($p['url']) . '" class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 block bg-white/5 border border-white/10 hover:border-lightblue/30">
        <img src="' . esc($thumb) . '" alt="' . esc($p['title']) . '" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 p-4 w-full">
          <h3 class="text-white text-base font-bold group-hover:text-lightblue transition-colors">' . esc($p['title']) . '</h3>
          <p class="text-white/70 text-xs mt-1 line-clamp-2">Explore ' . esc($p['keyword']) . ' in Bali</p>
        </div>
      </a>';
        $i++;
        if ($i >= $limit)
            break;
    }
    if (empty($cards)) {
        return '<p class="text-white/70 text-center py-8">More articles coming soon.</p>';
    }
    $html = '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">';
    for ($j = 0; $j < min(4, count($cards)); $j++)
        $html .= $cards[$j];
    $html .= '</div>';
    return $html;
}

// ================== BUILD PAGE CONTENT ==================
function buildFileContent($kw, $siteName, $authorName, $baseUrlHint, $thisPage, $allPages, $thumbs)
{
    $slug = $thisPage['slug'];
    $title = $siteName . " | " . ucwords($kw);
    $h1 = h1Line($kw);
    $sub = subheading($kw);
    $desc = metaDescription($kw);
    $para = paragraph($kw);
    $bul = bullets($kw);
    $faqs = faqPairs($kw);
    $sections = getDetailedSections($kw);

    // Get images from Bali Diving collection (random)
    $featuredImg = getFeaturedImage($kw);
    $contentImg = getContentImage($kw);

    // Canonical URL
    $canonical = $baseUrlHint
        ? rtrim($baseUrlHint, "/") . "/" . $slug
        : ($thisPage['url'] ?? "");

    // FAQ accordion with enhanced styling
    $faqHtml = '';
    foreach ($faqs as $f) {
        $q = esc($f[0]);
        $a = esc($f[1]);
        $faqHtml .= '
      <div class="border border-white/10 rounded-2xl p-6 bg-white/5 hover:bg-white/10 transition-all duration-300 group">
        <button class="w-full flex items-center justify-between text-left gap-4" onclick="this.nextElementSibling.classList.toggle(\'hidden\');this.querySelector(\'.chevron\').classList.toggle(\'rotate-180\')">
          <span class="font-semibold text-base md:text-lg group-hover:text-lightblue transition-colors">' . $q . '</span>
          <i class="fa-solid fa-chevron-down text-sm chevron transition-transform duration-300 flex-shrink-0"></i>
        </button>
        <div class="hidden mt-4 text-white/80 leading-relaxed text-base">' . $a . '</div>
      </div>';
    }

    $relatedGrid = buildRelatedGrid($allPages, $slug, $thumbs, 4);

    ob_start();
    ?>
    <?php echo "<?php include('../template/start.php'); ?>\n"; ?>

    <!-- ===================== Enhanced SEO Metadata (2026 Best Practices) ===================== -->
    <title>
        <?= esc($title) ?>
    </title>
    <meta name="description" content="<?= esc($desc) ?>">
    <meta name="keywords"
        content="<?= esc($kw) ?>, Bali diving, scuba diving Bali, dive sites, underwater adventure, PADI certification">
    <?php if ($canonical): ?>
        <link rel="canonical" href="<?= esc($canonical) ?>">
    <?php endif; ?>

    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="<?= esc($title) ?>">
    <meta property="og:description" content="<?= esc($desc) ?>">
    <meta property="og:image" content="<?= esc($featuredImg) ?>">
    <meta property="og:url" content="<?= esc($canonical) ?>">
    <meta property="og:type" content="article">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($title) ?>">
    <meta name="twitter:description" content="<?= esc($desc) ?>">
    <meta name="twitter:image" content="<?= esc($featuredImg) ?>">

    <!-- Structured Data -->
    <?= jsonLdArticle($title, $desc, $canonical, $authorName, $featuredImg) . "\n" ?>
    <?= jsonLdFAQ($faqs) . "\n" ?>

    <!-- ===== Premium Page Wrapper ===== -->
    <div class="min-h-screen bg-gradient-to-b from-[#0b1220] via-[#0d1528] to-[#0b1220] text-white">

        <!-- ===================== HERO SECTION ===================== -->
        <section class="relative min-h-[80vh] w-full flex items-center justify-center overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img src="<?= esc($featuredImg) ?>" alt="<?= esc($h1) ?>" class="w-full h-full object-cover opacity-40"
                    loading="eager">
                <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-navy/60 to-black/90"></div>
            </div>

            <!-- Hero Content -->
            <div class="relative z-10 max-w-6xl mx-auto px-6 py-24 text-center">
                <!-- Badge -->
                <div
                    class="inline-flex items-center gap-3 text-white/90 mb-8 bg-white/10 backdrop-blur-md px-5 py-3 rounded-full border border-white/20">
                    <i class="fa-solid fa-water text-lightblue"></i>
                    <span class="uppercase tracking-wider text-xs font-semibold">Bali • Underwater Adventure</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-8 text-white drop-shadow-2xl leading-tight">
                    <?= esc($h1) ?>
                </h1>

                <!-- Subheading -->
                <p class="text-white/90 text-lg md:text-xl max-w-4xl mx-auto mb-12 leading-relaxed">
                    <?= esc($sub) ?>
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-5 justify-center items-center">
                    <!-- PRIMARY: Check Your Plan (HIGHLIGHTED) -->
                    <a href="https://balidiving.com/cart/my-booking"
                        class="group px-10 py-5 rounded-2xl bg-gradient-to-r from-accent via-primary to-teal hover:from-teal hover:via-accent hover:to-primary transition-all duration-500 inline-flex items-center justify-center gap-4 shadow-2xl hover:shadow-accent/50 hover:scale-105 transform border-2 border-white/30 font-bold text-lg md:text-xl">
                        <i class="fa-solid fa-calendar-check text-2xl group-hover:animate-pulse"></i>
                        <span>Check Your Plan</span>
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </a>

                    <a href="#guide"
                        class="px-8 py-5 rounded-2xl bg-white/10 backdrop-blur-md hover:bg-white/20 transition-all duration-300 inline-flex items-center justify-center gap-3 border border-white/30 hover:border-white/50 font-semibold text-lg">
                        <i class="fa-solid fa-compass text-xl"></i>
                        <span>Read the Guide</span>
                    </a>

                    <a href="#faq"
                        class="px-8 py-5 rounded-2xl bg-white/5 backdrop-blur-md hover:bg-white/15 transition-all duration-300 inline-flex items-center justify-center gap-3 border border-white/20 hover:border-white/40 text-lg">
                        <i class="fa-solid fa-circle-question text-xl"></i>
                        <span>FAQs</span>
                    </a>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
                <i class="fa-solid fa-chevron-down text-white/50 text-3xl"></i>
            </div>
        </section>

        <!-- ===================== INTRO SECTION ===================== -->
        <section class="relative w-full py-24 bg-gradient-to-b from-transparent to-navy/30">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <!-- Content -->
                    <div class="space-y-8">
                        <div class="inline-block">
                            <span
                                class="text-gold uppercase tracking-widest text-sm font-bold bg-gold/10 px-5 py-2 rounded-full border border-gold/30">
                                <i class="fa-solid fa-star mr-2"></i>Featured Guide
                            </span>
                        </div>
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-lightblue leading-tight">
                            Your Complete<br>
                            <?= esc(ucwords($kw)) ?> Guide
                        </h2>
                        <p class="text-white/80 text-lg leading-relaxed">
                            <?= esc($desc) ?>
                        </p>
                        <p class="text-white/70 text-lg leading-relaxed">
                            <?= esc($para) ?>
                        </p>
                    </div>

                    <!-- Image -->
                    <div class="relative">
                        <div
                            class="rounded-3xl overflow-hidden shadow-2xl border-2 border-white/10 transform hover:scale-105 transition-transform duration-500">
                            <img src="<?= esc($contentImg) ?>" alt="<?= esc($kw) ?> in Bali"
                                class="w-full h-96 object-cover" loading="lazy">
                        </div>
                        <!-- Floating Badge -->
                        <div
                            class="absolute -bottom-8 -left-8 bg-gradient-to-br from-accent to-primary text-white px-8 py-5 rounded-2xl shadow-2xl border-2 border-white/20">
                            <div class="text-sm opacity-90 font-medium">Expert Certified</div>
                            <div class="text-3xl font-bold">PADI 5★</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== KEY TAKEAWAYS ===================== -->
        <section class="relative w-full py-24">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-5 text-white">
                        <i class="fa-solid fa-lightbulb text-gold mr-3"></i>Key Takeaways
                    </h2>
                    <p class="text-white/70 text-lg max-w-3xl mx-auto">
                        Everything you need to know for an unforgettable diving experience
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <?php foreach ($bul as $idx => $b): ?>
                        <div
                            class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8 hover:bg-white/10 hover:border-gold/50 transition-all duration-300 group">
                            <div class="flex items-start gap-5">
                                <div
                                    class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-gold/30 to-gold/10 flex items-center justify-center text-gold font-bold text-xl border-2 border-gold/30 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                                    <?= $idx + 1 ?>
                                </div>
                                <p class="text-white/90 leading-relaxed text-lg flex-1 pt-2">
                                    <?= esc($b) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ===================== DETAILED CONTENT ===================== -->
        <section id="guide" class="relative w-full py-24 bg-navy/30">
            <div class="max-w-6xl mx-auto px-6">
                <div class="space-y-20">
                    <?php foreach ($sections as $key => $section): ?>
                        <article
                            class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-10 md:p-14 hover:border-lightblue/30 transition-all duration-500 shadow-lg hover:shadow-2xl">
                            <h3 class="text-xl md:text-2xl lg:text-3xl font-bold mb-8 text-lightblue flex items-center gap-4">
                                <i class="fa-solid <?= esc($section['icon']) ?> text-2xl"></i>
                                <?= esc($section['title']) ?>
                            </h3>
                            <p class="text-white/80 leading-relaxed text-lg md:text-xl">
                                <?= esc(str_replace('{$kw}', $kw, $section['content'])) ?>
                            </p>
                        </article>
                    <?php endforeach; ?>

                    <!-- Additional Content -->
                    <article
                        class="bg-gradient-to-br from-accent/10 to-primary/10 backdrop-blur-sm border-2 border-accent/30 rounded-3xl p-10 md:p-14">
                        <h3 class="text-xl md:text-2xl lg:text-3xl font-bold mb-8 text-gold flex items-center gap-4">
                            <i class="fa-solid fa-map-location-dot text-2xl"></i>
                            Choosing Sites by Objective
                        </h3>
                        <p class="text-white/80 leading-relaxed text-lg md:text-xl mb-8">
                            Match your goal to site characteristics: entry/exit, current maps, visibility ranges, and known
                            marine life. For training objectives, keep profiles conservative, build repetition, and debrief
                            every dive.
                        </p>
                        <p class="text-white/70 leading-relaxed text-lg">
                            Popular sites for
                            <?= esc($kw) ?> include locations with varied depth profiles, accessible entry points, and
                            marine biodiversity. Always consult with local dive operators for current conditions and site
                            recommendations based on your experience level.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- ===================== FAQs ===================== -->
        <section id="faq" class="relative w-full py-24">
            <div class="max-w-5xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-5 text-white">
                        <i class="fa-solid fa-messages-question text-accent mr-3"></i>
                        Frequently Asked Questions
                    </h2>
                    <p class="text-white/70 text-lg max-w-3xl mx-auto">
                        Get answers to common questions about
                        <?= esc($kw) ?>
                    </p>
                </div>

                <div class="space-y-5">
                    <?= $faqHtml ?>
                </div>

                <!-- Notice -->
                <div
                    class="mt-16 p-8 rounded-3xl bg-gradient-to-r from-gold/10 to-orange-500/10 border-2 border-gold/30 text-base text-white/90 leading-relaxed shadow-xl">
                    <div class="flex items-start gap-4">
                        <i class="fa-solid fa-triangle-exclamation text-gold text-3xl flex-shrink-0 mt-1"></i>
                        <div>
                            <strong class="text-gold text-lg">Important Notice:</strong><br>
                            Information provided is for guidance only. Dive conditions can change rapidly due to weather,
                            currents, and seasonality. Always follow your professional dive guide's briefing, respect local
                            regulations, and dive within your certification limits.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== BOOKING CTA BANNER ===================== -->
        <section class="relative w-full py-20 bg-gradient-to-r from-accent via-primary to-teal overflow-hidden">
            <!-- Animated Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0"
                    style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                </div>
            </div>

            <div class="relative z-10 max-w-6xl mx-auto px-6 text-center">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
                    Ready to Dive In?
                </h2>
                <p class="text-white/95 text-lg md:text-xl mb-12 max-w-3xl mx-auto leading-relaxed">
                    Check your booking status, view your dive schedule, or plan your next underwater adventure with us.
                </p>
                <a href="https://balidiving.com/cart/my-booking"
                    class="inline-flex items-center gap-4 px-12 py-6 rounded-2xl bg-white text-primary hover:bg-gray-50 transition-all duration-300 font-bold text-xl shadow-2xl hover:scale-105 transform">
                    <i class="fa-solid fa-calendar-check text-3xl"></i>
                    <span>Check Your Plan Now</span>
                    <i class="fa-solid fa-arrow-right text-xl"></i>
                </a>
            </div>
        </section>

        <!-- ===================== RELATED ARTICLES ===================== -->
        <section class="relative w-full py-24 bg-gradient-to-b from-navy/60 to-transparent">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-12 gap-4">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-lightblue mb-3">
                            <i class="fa-solid fa-newspaper mr-3"></i>Related Articles
                        </h2>
                        <p class="text-white/60 text-lg">Explore more diving guides and tips</p>
                    </div>
                    <a href="./"
                        class="hidden sm:inline-flex items-center gap-3 text-gold hover:text-white transition px-6 py-3 rounded-xl bg-white/5 hover:bg-white/10 border-2 border-gold/30 hover:border-white/30 font-semibold">
                        <i class="fa-solid fa-grip text-xl"></i>
                        <span>View All</span>
                    </a>
                </div>

                <?= $relatedGrid ?>

                <!-- Mobile Button -->
                <div class="mt-10 text-center sm:hidden">
                    <a href="./"
                        class="inline-flex items-center gap-3 text-gold hover:text-white transition px-8 py-4 rounded-2xl bg-white/5 hover:bg-white/10 border-2 border-gold/30 font-semibold text-lg">
                        <i class="fa-solid fa-grip text-xl"></i>
                        <span>View All Articles</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- ===================== FOOTER ===================== -->
        <footer class="relative w-full py-10 bg-black/40 border-t border-white/10">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex flex-col md:flex-row items-center gap-6 text-white/50 text-sm">
                        <span class="font-medium">©
                            <?= date('Y') ?> Bali Diving. All rights reserved.
                        </span>
                        <span class="hidden md:inline text-white/30">•</span>
                        <a href="https://blog.balidiving.com/"
                            class="hover:text-white/80 transition inline-flex items-center gap-2 opacity-70 hover:opacity-100"
                            target="_blank" rel="noopener">
                            <i class="fa-solid fa-blog"></i>
                            <span>More insights on our blog</span>
                        </a>
                    </div>

                    <div class="flex items-center gap-5">
                        <a href="https://balidiving.com/cart/my-booking"
                            class="text-accent hover:text-lightblue transition font-bold inline-flex items-center gap-2 text-base">
                            <i class="fa-solid fa-calendar-days text-xl"></i>
                            <span>My Bookings</span>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <?php echo "<?php include('../template/end.php'); ?>\n"; ?>
    <?php
    return trim(ob_get_clean());
}

// ================== SITEMAP BUILDER ==================
function generateSitemapXML($pages, $sitemapPath)
{
    $now = date('c');
    $xml = [];
    $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($pages as $p) {
        $loc = $p['url'];
        $xml[] = '  <url>';
        $xml[] = '    <loc>' . htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') . '</loc>';
        $xml[] = '    <lastmod>' . $now . '</lastmod>';
        $xml[] = '    <changefreq>weekly</changefreq>';
        $xml[] = '    <priority>0.80</priority>';
        $xml[] = '  </url>';
    }
    $xml[] = '</urlset>';
    $content = implode("\n", $xml);
    if (file_put_contents($sitemapPath, $content) === false) {
        return "Failed to write sitemap: " . esc($sitemapPath);
    }
    return null;
}

// ================== HANDLE REQUEST ==================
$errors = [];
$messages = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = trim($_POST['keywords'] ?? '');
    $outDir = trim($_POST['outdir'] ?? '');
    $overwrite = isset($_POST['overwrite']);

    if ($raw === '')
        $errors[] = "Please provide at least one keyword (one per line).";

    $targetDir = $outDir !== '' ? rtrim($outDir, '/\\') : $GLOBALS['OUTPUT_DIR_DEFAULT'];
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0775, true))
            $errors[] = "Cannot create output directory: " . esc($targetDir);
    }
    if (!is_writable($targetDir))
        $errors[] = "Output directory is not writable: " . esc($targetDir);

    if (!$errors) {
        $lines = array_filter(array_map('trim', preg_split('/\R+/', $raw)));
        $lines = array_values(array_unique($lines));

        $pages = [];
        foreach ($lines as $k) {
            $slug = slugify($k);
            $filename = $targetDir . DIRECTORY_SEPARATOR . $slug . '.php';
            $url = rtrim($PUBLIC_BASE_URL, '/') . '/' . $slug;
            $pages[] = [
                'keyword' => $k,
                'slug' => $slug,
                'filename' => $filename,
                'url' => $url,
                'title' => ucwords($k),
            ];
        }

        $created = 0;
        $skipped = 0;
        $overwrote = 0;
        foreach ($pages as $p) {
            $content = buildFileContent($p['keyword'], $SITE_NAME, $AUTHOR_NAME, $BASE_URL_HINT, $p, $pages, $THUMBS);

            if (file_exists($p['filename']) && !$overwrite) {
                $skipped++;
                $messages[] = "Skipped (exists): " . basename($p['filename']);
                continue;
            }
            if (file_put_contents($p['filename'], $content) === false) {
                $errors[] = "Failed to write: " . esc($p['filename']);
            } else {
                if ($overwrite && file_exists($p['filename']))
                    $overwrote++;
                else
                    $created++;
                $messages[] = "✅ Generated: " . basename($p['filename']);
            }
        }

        // Build index
        $indexPath = $targetDir . DIRECTORY_SEPARATOR . 'index.php';
        if (!file_exists($indexPath) || $overwrite) {
            $listItems = "";
            $i = 0;
            foreach ($pages as $p) {
                $thumb = $THUMBS[$i % count($THUMBS)];
                $listItems .= '
        <a href="' . esc($p['url']) . '" class="group block rounded-2xl overflow-hidden bg-white/5 border border-white/10 hover:bg-white/10 hover:border-lightblue/30 transition-all duration-300">
          <div class="flex gap-5 items-center">
            <img src="' . esc($thumb) . '" class="w-32 h-24 object-cover" alt="' . esc($p['title']) . '">
            <div class="p-4 flex-1">
              <h3 class="text-white font-bold text-lg group-hover:text-lightblue transition-colors">' . esc($p['title']) . '</h3>
              <p class="text-white/70 text-sm mt-1">' . esc(metaDescription($p['keyword'])) . '</p>
            </div>
          </div>
        </a>';
                $i++;
            }
            $idx = "<?php include('../template/start.php'); ?>\n" .
                '<div class="min-h-screen bg-gradient-to-b from-[#0b1220] via-[#0d1528] to-[#0b1220] text-white">' .
                '<section class="min-h-[70vh] py-16"><div class="max-w-6xl mx-auto px-6">' .
                '<h1 class="text-4xl md:text-5xl font-bold mb-10 text-lightblue"><i class="fa-solid fa-newspaper mr-4"></i>All Diving Articles</h1>' .
                '<div class="grid md:grid-cols-2 gap-6">' . $listItems . '</div>' .
                '</div></section>' .
                '</div>' . "\n" .
                "<?php include('../template/end.php'); ?>\n";
            @file_put_contents($indexPath, $idx);
        }

        // Generate sitemap
        $sitemapError = generateSitemapXML($pages, $SITEMAP_PATH);
        if ($sitemapError)
            $errors[] = $sitemapError;
        else
            $messages[] = "✅ Sitemap generated: " . esc($SITEMAP_PATH);

        if (!$errors) {
            $messages[] = "🎉 Done! Created: {$created}, Overwrote: {$overwrote}, Skipped: {$skipped}.";
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Enhanced Article Generator - Bali Diving</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
</head>

<body class="bg-gradient-to-b from-[#0b1220] via-[#0d1528] to-[#0b1220] text-white min-h-screen">
    <div class="max-w-5xl mx-auto p-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-3 text-lightblue">
                <i class="fa-solid fa-wand-magic-sparkles mr-3"></i>Enhanced Article Generator
            </h1>
            <p class="text-white/70 text-lg">Generate premium, SEO-optimized diving articles with Unsplash images</p>
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full border border-green-500/30">
                    <i class="fa-solid fa-check mr-1"></i>Premium Design
                </span>
                <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full border border-blue-500/30">
                    <i class="fa-solid fa-image mr-1"></i>Unsplash Images
                </span>
                <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full border border-purple-500/30">
                    <i class="fa-solid fa-search mr-1"></i>SEO 2026
                </span>
                <span class="bg-gold/20 text-gold px-3 py-1 rounded-full border border-gold/30">
                    <i class="fa-solid fa-star mr-1"></i>Booking CTA
                </span>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="mb-6 rounded-2xl border-2 border-secondary bg-secondary/10 p-6 backdrop-blur-sm">
                <div class="font-semibold mb-3 text-lg"><i
                        class="fa-solid fa-circle-exclamation mr-2 text-secondary"></i>Errors</div>
                <ul class="list-disc ml-5 space-y-1">
                    <?php foreach ($errors as $e): ?>
                        <li>
                            <?= $e ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($messages)): ?>
            <div class="mb-6 rounded-2xl border-2 border-teal bg-teal/10 p-6 backdrop-blur-sm">
                <div class="font-semibold mb-3 text-lg"><i class="fa-solid fa-circle-check mr-2 text-teal"></i>Results</div>
                <ul class="list-disc ml-5 space-y-1">
                    <?php foreach ($messages as $m): ?>
                        <li>
                            <?= esc($m) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-6 bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8">
            <div>
                <label class="block text-base font-semibold mb-3">Keywords (one per line)</label>
                <textarea name="keywords" rows="12"
                    class="w-full rounded-2xl bg-white/5 border border-white/10 p-5 focus:border-lightblue focus:ring-2 focus:ring-lightblue/50 transition-all font-mono text-sm"
                    placeholder="e.g.
Tulamben wreck diving
Nusa Penida manta dive
Amed macro photography
Bali scuba refresher
Open Water Course Bali"><?= isset($_POST['keywords']) ? esc($_POST['keywords']) : '' ?></textarea>
            </div>

            <div class="grid md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-2">Output Directory (optional)</label>
                    <input type="text" name="outdir"
                        class="w-full rounded-xl bg-white/5 border border-white/10 p-4 focus:border-lightblue focus:ring-2 focus:ring-lightblue/50 transition-all"
                        placeholder="<?= esc($OUTPUT_DIR_DEFAULT) ?>"
                        value="<?= isset($_POST['outdir']) ? esc($_POST['outdir']) : '' ?>">
                </div>
                <div class="flex items-end">
                    <label
                        class="inline-flex items-center gap-3 cursor-pointer bg-white/5 px-5 py-4 rounded-xl border border-white/10 hover:bg-white/10 transition-all w-full">
                        <input type="checkbox" name="overwrite" class="w-5 h-5 accent-accent">
                        <span class="font-medium">Overwrite existing</span>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full px-8 py-5 rounded-2xl bg-gradient-to-r from-accent via-primary to-teal hover:from-teal hover:via-accent hover:to-primary transition-all duration-500 inline-flex items-center justify-center gap-3 font-bold text-lg shadow-2xl hover:scale-105 transform">
                <i class="fa-solid fa-bolt text-2xl"></i>
                <span>Generate Premium Articles</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>

            <div class="text-sm text-white/50 text-center">
                <i class="fa-solid fa-info-circle mr-2"></i>
                Tip: Re-run with the same keywords to refresh internal linking and sitemap.xml.
            </div>
        </form>
    </div>
</body>

</html>