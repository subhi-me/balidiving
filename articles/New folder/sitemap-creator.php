<?php
/**
 * generate_sitemap.php
 *
 * Scan recursively for .php files (source only, no execution),
 * extract title/description/image hints from source, and build a sitemap.xml
 * with image entries (Google image sitemap namespace).
 *
 * Usage:
 *   php generate_sitemap.php
 *
 * Output:
 *   sitemap.xml (in the same directory as this script)
 *
 * Notes:
 * - Set $baseUrl to your site (no trailing slash).
 * - Adjust $rootDir if you want to scan elsewhere.
 * - Excluded directories can be set in $excludeDirs.
 */

date_default_timezone_set('Asia/Makassar');

// -------- CONFIG --------
$baseUrl = 'https://balidiving.com'; // <-- Ubah jika perlu
$rootDir = realpath(__DIR__);       // <-- path root to scan, default = current dir
$excludeDirs = [
    '/vendor', '/node_modules', '/storage', '/logs', '/cache', '/.git'
];

// Fallback images (user-provided) - will be used if page has no detected images
$fallbackImages = [
    'https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg',
    'https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg'
];
// ------------------------

/**
 * Return true if path contains any excluded fragment
 */
function isExcluded($path, $excludeDirs) {
    foreach ($excludeDirs as $ex) {
        // normalize
        $exNorm = str_replace(['\\','/'], DIRECTORY_SEPARATOR, trim($ex, "/\\"));
        if ($exNorm === '') continue;
        if (stripos($path, DIRECTORY_SEPARATOR . $exNorm . DIRECTORY_SEPARATOR) !== false ||
            stripos($path, DIRECTORY_SEPARATOR . $exNorm) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Convert file system path to public URL based on $rootDir and $baseUrl
 */
function pathToUrl($filePath, $rootDir, $baseUrl) {
    $filePath = str_replace('\\', '/', $filePath);
    $rootDir  = str_replace('\\', '/', $rootDir);

    // If file is index.php inside folder, map to folder URL
    $rel = ltrim(substr($filePath, strlen($rootDir)), "/");
    $rel = rawurlencode($rel);
    // rawurlencode will encode slashes too, so fix it:
    $rel = str_replace('%2F', '/', $rel);

    // Remove .php extension
    if (substr($rel, -9) === 'index.php') {
        $rel = rtrim(substr($rel, 0, -9), '/'); // folder path
    } else {
        $rel = preg_replace('/\.php$/i', '', $rel);
    }

    // Build URL
    $url = rtrim($baseUrl, '/') . '/' . ltrim($rel, '/');
    // Ensure no double slashes
    $url = preg_replace('#([^:])//+#', '$1/', $url);

    // If empty, it's the root
    if ($url === rtrim($baseUrl, '/') . '/') {
        $url = rtrim($baseUrl, '/');
    }
    return $url;
}

/**
 * Extract title, meta description, og:image and first <img src> from raw PHP source.
 * We do not execute PHP; only parse source text via regex heuristics.
 */
function extractHintsFromSource($source) {
    $hints = [
        'title' => null,
        'description' => null,
        'images' => []
    ];

    // Remove PHP tags to reduce noise
    $noPhp = preg_replace('/<\?(?:php|=)?[\s\S]*?\?>/i', '', $source);

    // Title
    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $noPhp, $m)) {
        $hints['title'] = trim(strip_tags($m[1]));
    }

    // meta description
    if (preg_match('/<meta\s+(?:name|property)\s*=\s*["\'](?:description|og:description)["\']\s+content\s*=\s*["\'](.*?)["\']/is', $noPhp, $m)) {
        $hints['description'] = trim($m[1]);
    } elseif (preg_match('/<meta\s+content\s*=\s*["\'](.*?)["\']\s+name\s*=\s*["\']description["\']/is', $noPhp, $m)) {
        $hints['description'] = trim($m[1]);
    }

    // og:image or twitter:image
    if (preg_match_all('/<meta\s+(?:property|name)\s*=\s*["\'](?:og:image|twitter:image|image)["\']\s+content\s*=\s*["\'](https?:\/\/[^"\']+)["\']/is', $noPhp, $mm)) {
        foreach ($mm[1] as $img) $hints['images'][] = trim($img);
    }

    // img src attributes (absolute or relative)
    if (preg_match_all('/<img[^>]+src\s*=\s*["\']([^"\']+)["\']/is', $noPhp, $imgs)) {
        foreach ($imgs[1] as $img) {
            $img = trim($img);
            if ($img === '') continue;
            // ignore data: URIs
            if (stripos($img, 'data:') === 0) continue;
            $hints['images'][] = $img;
        }
    }

    // dedupe & normalize
    $hints['images'] = array_values(array_unique($hints['images']));

    return $hints;
}

/**
 * Normalize image URL:
 * - if it's absolute, return as-is
 * - if it's root-relative (starts with /) or relative, prefix with baseUrl
 */
function normalizeImageUrl($img, $pageUrl, $baseUrl) {
    $img = str_replace(' ', '%20', $img);
    if (preg_match('#^https?://#i', $img)) {
        return $img;
    }
    // If starts with // (protocol-relative)
    if (strpos($img, '//') === 0) {
        return 'https:' . $img;
    }
    // if starts with '/', absolute path on host:
    if (strpos($img, '/') === 0) {
        return rtrim($baseUrl, '/') . $img;
    }
    // relative path: combine with pageUrl directory
    $pageDir = preg_replace('#/[^/]*$#', '/', $pageUrl);
    return rtrim($pageDir, '/') . '/' . ltrim($img, '/');
}

// --- scan files ---
$files = [];
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($it as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();

    // Skip excluded directories
    if (isExcluded($path, $excludeDirs)) continue;

    // Only .php files
    if (preg_match('/\.php$/i', $path)) {
        // optionally skip files starting with dot or special ones
        $base = basename($path);
        if (substr($base, 0, 1) === '.') continue;
        // Skip maybe config files if you want (uncomment if desired)
        // if (preg_match('/^(config|env|\.env|wp-config)/i', $base)) continue;

        $files[] = $path;
    }
}

// Sort files to keep homepage/index first (nice for priority)
usort($files, function($a, $b) {
    $a_lower = strtolower($a); $b_lower = strtolower($b);
    if (strpos($a_lower, 'index.php') !== false && strpos($b_lower, 'index.php') === false) return -1;
    if (strpos($b_lower, 'index.php') !== false && strpos($a_lower, 'index.php') === false) return 1;
    return strcmp($a_lower, $b_lower);
});

// --- build XML ---
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;

// urlset root with required namespaces
$urlset = $dom->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$urlset->setAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
$dom->appendChild($urlset);

$fallbackIdx = 0;
$total = count($files);
$now = new DateTime('now', new DateTimeZone('Asia/Makassar'));

foreach ($files as $path) {
    $url = pathToUrl($path, $rootDir, $baseUrl);
    // read source safely
    $source = @file_get_contents($path);
    if ($source === false) continue;

    $hints = extractHintsFromSource($source);

    // compute lastmod from file mtime
    $mtime = filemtime($path);
    $lastmod = gmdate('Y-m-d\TH:i:s\+00:00', $mtime); // ISO 8601 in UTC

    // Heuristics for changefreq and priority
    $depth = substr_count(str_replace('\\','/',$path), '/');
    $priority = 0.5;
    if (stripos(basename($path), 'index.php') !== false || trim($url, '/') === trim($baseUrl, '/')) {
        $priority = 1.0;
        $changefreq = 'daily';
    } elseif (preg_match('/(blog|post|article|news)/i', $path)) {
        $priority = 0.8;
        $changefreq = 'weekly';
    } elseif (preg_match('/(contact|about|team|privacy|terms)/i', $path)) {
        $priority = 0.4;
        $changefreq = 'yearly';
    } else {
        // shallower files get slightly higher priority
        $priority = max(0.3, 1 - ($depth * 0.03));
        $changefreq = 'monthly';
    }

    // Build XML url node
    $urlNode = $dom->createElement('url');
    $loc = $dom->createElement('loc', htmlspecialchars($url, ENT_XML1 | ENT_COMPAT, 'UTF-8'));
    $urlNode->appendChild($loc);

    $lastmodNode = $dom->createElement('lastmod', $lastmod);
    $urlNode->appendChild($lastmodNode);

    $changefreqNode = $dom->createElement('changefreq', $changefreq);
    $urlNode->appendChild($changefreqNode);

    $priorityNode = $dom->createElement('priority', number_format($priority, 1, '.', ''));
    $urlNode->appendChild($priorityNode);

    // IMAGE: collect candidates from hints (normalize)
    $imgUrls = [];
    foreach ($hints['images'] as $img) {
        $normalized = normalizeImageUrl($img, $url, $baseUrl);
        // ensure same-host images only? For now include any absolute images.
        $imgUrls[] = $normalized;
    }

    // if none, use a fallback image (rotate)
    if (empty($imgUrls)) {
        $imgUrls[] = $fallbackImages[$fallbackIdx % count($fallbackImages)];
        $fallbackIdx++;
    }

    // attach up to 5 images per url
    $slice = array_slice($imgUrls, 0, 5);
    foreach ($slice as $imgSrc) {
        $imgEl = $dom->createElementNS('http://www.google.com/schemas/sitemap-image/1.1', 'image:image');

        $imgLoc = $dom->createElement('image:loc', htmlspecialchars($imgSrc, ENT_XML1 | ENT_COMPAT, 'UTF-8'));
        $imgEl->appendChild($imgLoc);

        // caption/ title if available
        if (!empty($hints['title'])) {
            $cap = substr($hints['title'], 0, 200);
            $imgCap = $dom->createElement('image:caption', htmlspecialchars($cap, ENT_XML1 | ENT_COMPAT, 'UTF-8'));
            $imgEl->appendChild($imgCap);
        } elseif (!empty($hints['description'])) {
            $cap = substr($hints['description'], 0, 200);
            $imgCap = $dom->createElement('image:caption', htmlspecialchars($cap, ENT_XML1 | ENT_COMPAT, 'UTF-8'));
            $imgEl->appendChild($imgCap);
        }

        // title tag
        $imgTitleVal = !empty($hints['title']) ? $hints['title'] : basename($imgSrc);
        $imgTitle = $dom->createElement('image:title', htmlspecialchars($imgTitleVal, ENT_XML1 | ENT_COMPAT, 'UTF-8'));
        $imgEl->appendChild($imgTitle);

        $urlNode->appendChild($imgEl);
    }

    $urlset->appendChild($urlNode);
}

// Write sitemap.xml
$sitemapPath = $rootDir . DIRECTORY_SEPARATOR . 'sitemap.xml';
file_put_contents($sitemapPath, $dom->saveXML());

echo "Sitemap generated: {$sitemapPath}\n";
echo "Total pages: " . count($files) . "\n";

// Optional: create compressed version
if (function_exists('gzencode')) {
    $gz = gzencode(file_get_contents($sitemapPath), 9);
    file_put_contents($sitemapPath . '.gz', $gz);
    echo "Compressed sitemap: {$sitemapPath}.gz\n";
}

echo "Done. Cron example: 0 * * * * /usr/bin/php " . escapeshellarg($rootDir . '/generate_sitemap.php') . " >/dev/null 2>&1\n";
