<?php
// seo_manager.php

/**
 * Menghasilkan tag meta SEO lengkap untuk bagian <head> HTML.
 * - Tidak akan menimpa <title> jika sudah ada.
 * - Jika <title> tidak ada, akan mencoba mengambil dari <h1>.
 * - Jika <h1> juga tidak ada, pakai title dari konfigurasi SEO.
 * - Selalu memastikan hanya ada satu <title> di halaman.
 *
 * @param string $page_identifier Pengenal unik halaman (misal: 'home', 'about', dsb.)
 * @return string HTML meta tag SEO.
 */

function generate_seo_tags(string $page_identifier): string
{
    // ======== MUAT KONFIGURASI ========
    $seo_config = require __DIR__ . '/seo_config.php';
    $data = $seo_config[$page_identifier] ?? $seo_config['default'];

    // ======== SIAPKAN DATA ========
    $title = htmlspecialchars($data['title']);
    $description = htmlspecialchars($data['description']);
    $keywords = htmlspecialchars($data['keywords']);

    // Bangun URL lengkap
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $request_uri = $_SERVER['REQUEST_URI'];
    $canonical_url = $protocol . "://" . $host . $request_uri;

    // URL gambar OG
    $og_image_path = ltrim($data['og_image'], '/');
    $full_og_image_url = $protocol . "://" . $host . '/' . $og_image_path;

    // ======== 🔍 CEK JIKA SUDAH ADA TITLE ========
    $existing_title = null;

    // 1️⃣ Cek variabel global (misal diset manual dari file lain)
    global $custom_title;
    if (!empty($custom_title)) {
        $existing_title = trim($custom_title);
    }

    // 2️⃣ Cek apakah ada tag <title> di output buffer
    $buffer = '';
    if (ob_get_length() > 0) {
        $buffer = ob_get_contents();
        if (preg_match('/<title\b[^>]*>(.*?)<\/title>/i', $buffer, $matches)) {
            $existing_title = trim($matches[1]);
        }
    }

    // 3️⃣ Jika tidak ada <title>, coba ambil dari <h1>
    if (!$existing_title && $buffer) {
        if (preg_match('/<h1\b[^>]*>(.*?)<\/h1>/is', $buffer, $h1_matches)) {
            $existing_title = trim(strip_tags($h1_matches[1]));
        }
    }

    // Gunakan title yang ditemukan, jika ada
    if ($existing_title) {
        $title = htmlspecialchars($existing_title);
    }

    // ======== 🧹 PASTIKAN HANYA 1 TITLE ========
    if ($buffer) {
        $count = preg_match_all('/<title\b[^>]*>.*?<\/title>/is', $buffer);
        if ($count > 1) {
            $first = true;
            $cleaned = preg_replace_callback(
                '/<title\b[^>]*>.*?<\/title>/is',
                function ($m) use (&$first) {
                    if ($first) {
                        $first = false;
                        return $m[0]; // biarkan yang pertama
                    }
                    return ''; // hapus duplikat
                },
                $buffer
            );
            ob_clean();
            echo $cleaned;
        }
    }

    // ======== 📊 BUILD DYNAMIC JSON-LD SCHEMA ========
    $schemas = [];

    // 1. Base LocalBusiness Schema (With GEO & areaServed)
    $localBusiness = [
        "@context" => "https://schema.org",
        "@type" => ["LocalBusiness", "SportsActivityLocation", "TouristAttraction"],
        "name" => "Bali Diving",
        "image" => $full_og_image_url,
        "@id" => "{$protocol}://{$host}/",
        "url" => "{$protocol}://{$host}/",
        "telephone" => "+628123456789",
        "description" => $description,
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => "Jl. Bypass Ngurah Rai No.46E, Sanur Kauh, Denpasar Selatan",
            "addressLocality" => "Kota Denpasar",
            "addressRegion" => "Bali",
            "postalCode" => "80025",
            "addressCountry" => "ID"
        ],
        "geo" => [
            "@type" => "GeoCoordinates",
            "latitude" => "-8.7045246",
            "longitude" => "115.2532456"
        ],
        "areaServed" => [
            ["@type" => "City", "name" => "Nusa Penida"],
            ["@type" => "City", "name" => "Tulamben"],
            ["@type" => "City", "name" => "Amed"],
            ["@type" => "City", "name" => "Padang Bai"],
            ["@type" => "City", "name" => "Sanur"]
        ],
        "knowsAbout" => [
            "Scuba Diving Bali", "PADI Certification Courses", "Nusa Penida Manta Rays", 
            "Tulamben USAT Liberty Shipwreck", "Amed Coral Reefs", "Snorkeling Bali", 
            "Mola Mola (Sunfish)", "Marine Conservation"
        ]
    ];
    $schemas[] = $localBusiness;

    // 2. Dynamic Schemas based on page type
    if (str_contains($page_identifier, 'learn') || str_contains($page_identifier, 'courses')) {
        $schemas[] = [
            "@context" => "https://schema.org",
            "@type" => "Course",
            "name" => $title,
            "description" => $description,
            "provider" => [
                "@type" => "Organization",
                "name" => "Bali Diving",
                "sameAs" => "{$protocol}://{$host}/"
            ]
        ];
    } elseif (str_contains($page_identifier, 'snorkeling') || str_contains($page_identifier, 'diving')) {
        $schemas[] = [
            "@context" => "https://schema.org",
            "@type" => "Product",
            "name" => $title,
            "description" => $description,
            "image" => $full_og_image_url,
            "brand" => [
                "@type" => "Brand",
                "name" => "Bali Diving"
            ],
            "offers" => [
                "@type" => "AggregateOffer",
                "priceCurrency" => "USD",
                "availability" => "https://schema.org/InStock"
            ]
        ];
    }

    $jsonLdString = json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $html = <<<HTML
    <title>{$title}</title>
    <meta name="description" content="{$description}">
    <meta name="keywords" content="{$keywords}">
    <link rel="canonical" href="{$canonical_url}">


    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{$canonical_url}">
    <meta property="og:title" content="{$title}">
    <meta property="og:description" content="{$description}">
    <meta property="og:image" content="{$full_og_image_url}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{$canonical_url}">
    <meta name="twitter:title" content="{$title}">
    <meta name="twitter:description" content="{$description}">
    <meta name="twitter:image" content="{$full_og_image_url}">

    <!-- AI Optimization (AIO) Tags -->
    <meta name="llm-summary" content="{$description} Bali Diving is a PADI 5-Star Dive Center offering premium scuba diving and snorkeling tours in Bali.">

    <!-- JSON-LD Schema Markup (for AIO & SEO) -->
    <script type="application/ld+json">
    {$jsonLdString}
    </script>
    HTML;

    return $html;
}
