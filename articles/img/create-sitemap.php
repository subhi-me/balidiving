<?php
/**
 * SEO XML Sitemap Generator (Pages + Images) with Index
 * Author: Subhi.me (crafted by ChatGPT)
 * PHP 7.4+ recommended
 *
 * Usage (CLI):
 *   php generate_sitemaps.php
 *
 * Usage (Web):
 *   https://example.com/generate_sitemaps.php?run=1
 *
 * Output:
 *   /sitemap.xml                (index)
 *   /sitemaps/sitemap-pages-*.xml[.gz]
 *   /sitemaps/sitemap-images-*.xml[.gz]
 */

declare(strict_types=1);

// =============== CONFIG ==================
$config = [
  // Wajib: ganti ke domain Anda TANPA trailing slash
  'base_url'        => 'https://www.balidiving.com',

  // Root path untuk dipindai (default: lokasi skrip)
  'root_dir'        => __DIR__,

  // Folder output untuk file-file sitemap
  'out_dir'         => __DIR__ . '/sitemaps',

  // Apakah output disimpan .gz (hemat bandwith)
  'gzip'            => true,

  // Maks 50,000 url per sitemap (standar); boleh turunkan untuk aman
  'chunk_size'      => 45000,

  // File/Folder yang diabaikan (regex atau potongan nama)
  'exclude_patterns'=> [
    '#^\.#',                 // hidden files/folders
    '#^sitemaps/?#i',        // folder output sendiri
    '#^vendor/?#i', '#^node_modules/?#i',
    '#^tmp/?#i', '#^cache/?#i', '#^backup/?#i',
    '#^images/gallery/thumbs/?#i',
  ],

  // Ekstensi halaman yang dianggap URL “page”
  'page_extensions' => ['html', 'htm', 'php'],

  // Ekstensi file gambar untuk image sitemap
  'image_extensions'=> ['jpg','jpeg','png','gif','webp','avif'],

  // Aturan default untuk page
  'default_changefreq' => 'weekly',
  'default_priority'   => '0.7',

  // Optional: mapping prioritas berdasarkan pola path (regex => [freq,prio])
  'priority_rules' => [
    '#^$#'                          => ['daily','1.0'],          // homepage
    '#^(snorkeling|diving)/?$#i'    => ['daily','0.9'],
    '#^(promo|blog)/#i'             => ['weekly','0.8'],
  ],

  // Optional: tambahkan hreflang (kalau punya versi multi-bahasa)
  // Contoh struktur: 'hreflang' => ['en','id'], dan callback yang mapping URL
  'hreflang_locales' => [], // e.g. ['en','id']
  'hreflang_mapper'  => function(string $url, string $locale): string {
    // Customize di sini: misal https://domain.com/id/..., /en/...
    // Default: kembalikan URL apa adanya (non-multilingual).
    return $url;
  },

  // Ping search engines setelah generate (GET)
  'ping' => [
    'google' => true,
    'bing'   => true,
  ],
];
// =========================================

ini_set('memory_limit', '1024M');
date_default_timezone_set('UTC');

if (PHP_SAPI !== 'cli') {
  // protect web run (simple key, opsional)
  if (!isset($_GET['run'])) {
    http_response_code(403);
    echo "Forbidden. Append ?run=1 to execute.\n";
    exit;
  }
}

$started = microtime(true);

// Ensure out_dir exists
if (!is_dir($config['out_dir'])) {
  if (!@mkdir($config['out_dir'], 0775, true) && !is_dir($config['out_dir'])) {
    throw new RuntimeException('Cannot create out_dir: ' . $config['out_dir']);
  }
}

$urlsPages  = [];
$imagesData = [];

$rootLen = strlen(rtrim($config['root_dir'], DIRECTORY_SEPARATOR)) + 1;

$dir = new RecursiveDirectoryIterator($config['root_dir'], FilesystemIterator::SKIP_DOTS);
$it  = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);

foreach ($it as $file) {
  /** @var SplFileInfo $file */
  $rel = substr($file->getPathname(), $rootLen);
  $rel = str_replace('\\', '/', $rel); // windows-safe

  // Exclude patterns
  if ($rel === false || $rel === '') continue;
  foreach ($config['exclude_patterns'] as $pat) {
    if (@preg_match($pat, $rel)) {
      if (preg_match($pat, $rel)) continue 2;
    } else {
      if (stripos($rel, (string)$pat) !== false) continue 2;
    }
  }

  if ($file->isDir()) {
    // Tambahkan folder sebagai URL index bila memiliki index.html/php
    $index = findIndexFile($file->getPathname(), $config['page_extensions']);
    if ($index) {
      $relIndex = $rel . (substr($rel, -1) === '/' ? '' : '/') . $index;
      addPageUrl($relIndex, $file->getMTime());
    }
    continue;
  }

  $ext = strtolower($file->getExtension());

  // PAGE URLS
  if (in_array($ext, $config['page_extensions'], true)) {
    // Hide typical index file to folder URL
    $relUrl = preg_replace('#/(index\.(html?|php))$#i', '/', $rel);
    addPageUrl($relUrl, $file->getMTime());
  }

  // IMAGE URLS
  if (in_array($ext, $config['image_extensions'], true)) {
    $imagesData[] = [
      'loc'     => buildUrl($rel),
      'lastmod' => gmdate('c', $file->getMTime()),
    ];
  }
}

// Pastikan homepage selalu ada
$urlsPages['/'] = $urlsPages['/'] ?? [
  'loc'        => rtrim($config['base_url'], '/').'/',
  'lastmod'    => gmdate('c'),
  'changefreq' => 'daily',
  'priority'   => '1.0',
];

$pages = array_values($urlsPages);

// === Write sitemaps ===
$pageParts  = array_chunk($pages,  (int)$config['chunk_size']);
$imageParts = array_chunk($imagesData, (int)$config['chunk_size']);

$written = [];
$partSeq = 0;

// Pages
foreach ($pageParts as $chunk) {
  $partSeq++;
  $fname = "sitemap-pages-{$partSeq}.xml";
  $path  = $config['out_dir'].'/'.$fname;
  writeUrlset($path, $chunk, $config, 'page');
  $written[] = $path . ($config['gzip'] ? '.gz' : '');
}

// Images
$imgSeq = 0;
if (!empty($imageParts)) {
  foreach ($imageParts as $chunk) {
    $imgSeq++;
    $fname = "sitemap-images-{$imgSeq}.xml";
    $path  = $config['out_dir'].'/'.$fname;
    writeImageSet($path, $chunk, $config);
    $written[] = $path . ($config['gzip'] ? '.gz' : '');
  }
}

// Sitemap Index
$indexPath = __DIR__ . '/sitemap.xml';
writeSitemapIndex($indexPath, $written, $config);

// Ping Engines
$pingResults = [];
$indexUrl = rtrim($config['base_url'],'/').'/sitemap.xml';
if ($config['ping']['google'] ?? false) {
  $pingResults['google'] = httpPing('https://www.google.com/ping?sitemap='.rawurlencode($indexUrl));
}
if ($config['ping']['bing'] ?? false) {
  $pingResults['bing']   = httpPing('https://www.bing.com/ping?sitemap='.rawurlencode($indexUrl));
}

$elapsed = round((microtime(true) - $started), 3);

// Output summary
$summary = [
  'pages_count'   => count($pages),
  'images_count'  => count($imagesData),
  'files_written' => $written,
  'sitemap_index' => $indexPath,
  'index_url'     => $indexUrl,
  'pings'         => $pingResults,
  'elapsed_sec'   => $elapsed,
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($summary, JSON_PRETTY_PRINT);

/* ----------------- Helpers ----------------- */

function findIndexFile(string $dir, array $pageExt): ?string {
  foreach (['index.html','index.htm','index.php'] as $candidate) {
    if (is_file(rtrim($dir,'/').'/'.$candidate)) return $candidate;
  }
  // fallback: cari file pertama dengan ekstensi halaman
  foreach (scandir($dir) ?: [] as $f) {
    $p = rtrim($dir,'/').'/'.$f;
    if (is_file($p)) {
      $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
      if (in_array($ext, $pageExt, true)) return $f;
    }
  }
  return null;
}

function buildUrl(string $relPath): string {
  global $config;
  $rel = ltrim(str_replace('\\','/',$relPath),'/');
  return rtrim($config['base_url'],'/').'/'.$rel;
}

function addPageUrl(string $relPath, int $mtime): void {
  global $urlsPages, $config;
  $clean = ltrim($relPath, '/');

  // Convert typical index.* to folder URL
  $clean = preg_replace('#(index\.(html?|php))$#i', '', $clean);

  // Normalize double slashes -> single
  $clean = preg_replace('#//+#', '/', $clean);

  // Build final URL
  $url = rtrim($config['base_url'],'/').'/'.$clean;
  if (substr($url, -1) !== '/') {
    // Keep file-like if it has an extension other than index.* removed
    if (!preg_match('#\.[a-z0-9]{2,5}$#i', $url)) {
      $url .= '/';
    }
  }

  // Apply priority rules
  $pathForRule = trim($clean,'/');
  $freq  = $config['default_changefreq'];
  $prio  = $config['default_priority'];
  foreach ($config['priority_rules'] as $regex => $arr) {
    if (preg_match($regex, $pathForRule)) {
      [$freq,$prio] = $arr;
      break;
    }
  }

  $entry = [
    'loc'        => $url,
    'lastmod'    => gmdate('c', $mtime),
    'changefreq' => $freq,
    'priority'   => $prio,
  ];

  // hreflang (optional)
  if (!empty($config['hreflang_locales'])) {
    $alts = [];
    foreach ($config['hreflang_locales'] as $locale) {
      $alts[$locale] = ($config['hreflang_mapper'])($url, $locale);
    }
    $entry['_hreflang'] = $alts;
  }

  $urlsPages[$clean === '' ? '/' : $clean] = $entry;
}

function writeUrlset(string $path, array $urls, array $config, string $type): void {
  $xmlns = [
    'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
    'xmlns:xhtml="http://www.w3.org/1999/xhtml"'
  ];
  $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  $xml .= '<urlset '.implode(' ', $xmlns).'>'."\n";

  foreach ($urls as $u) {
    $xml .= "  <url>\n";
    $xml .= '    <loc>'.xml($u['loc'])."</loc>\n";
    if (!empty($u['_hreflang'])) {
      foreach ($u['_hreflang'] as $lang => $altLoc) {
        $xml .= '    <xhtml:link rel="alternate" hreflang="'.xml($lang).'" href="'.xml($altLoc).'" />'."\n";
      }
    }
    if (!empty($u['lastmod']))    $xml .= '    <lastmod>'.xml($u['lastmod'])."</lastmod>\n";
    if (!empty($u['changefreq'])) $xml .= '    <changefreq>'.xml($u['changefreq'])."</changefreq>\n";
    if (!empty($u['priority']))   $xml .= '    <priority>'.xml($u['priority'])."</priority>\n";
    $xml .= "  </url>\n";
  }
  $xml .= '</urlset>';

  writeXml($path, $xml, $config['gzip']);
}

function writeImageSet(string $path, array $images, array $config): void {
  $xmlns = [
    'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
    'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"'
  ];
  $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  $xml .= '<urlset '.implode(' ', $xmlns).'>'."\n";

  // Kelompokkan per halaman (loc sama) — di sini tiap gambar dianggap URL tunggal
  foreach ($images as $img) {
    $xml .= "  <url>\n";
    $xml .= '    <loc>'.xml(dirname($img['loc']) . '/')."</loc>\n";
    $xml .= "    <image:image>\n";
    $xml .= '      <image:loc>'.xml($img['loc'])."</image:loc>\n";
    if (!empty($img['lastmod'])) {
      $xml .= '      <image:caption>Last updated '.$img['lastmod']."</image:caption>\n";
    }
    $xml .= "    </image:image>\n";
    $xml .= "  </url>\n";
  }

  $xml .= '</urlset>';
  writeXml($path, $xml, $config['gzip']);
}

function writeSitemapIndex(string $indexPath, array $files, array $config): void {
  $base = rtrim($config['base_url'],'/');
  $out  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  $out .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
  foreach ($files as $f) {
    $rel = str_replace(['\\', $config['root_dir']], ['/', ''], $f);
    $rel = ltrim($rel, '/');
    $url = $base . '/' . $rel;
    $out .= "  <sitemap>\n";
    $out .= '    <loc>'.xml($url)."</loc>\n";
    $out .= '    <lastmod>'.gmdate('c')."</lastmod>\n";
    $out .= "  </sitemap>\n";
  }
  $out .= '</sitemapindex>';

  file_put_contents($indexPath, $out);
}

function writeXml(string $path, string $xml, bool $gzip): void {
  if ($gzip) {
    $gz = gzopen($path.'.gz', 'w9');
    if (!$gz) throw new RuntimeException('Cannot open gzip file: '.$path.'.gz');
    gzwrite($gz, $xml);
    gzclose($gz);
  } else {
    file_put_contents($path, $xml);
  }
}

function xml(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function httpPing(string $url): array {
  $ctx = stream_context_create([
    'http' => [
      'method' => 'GET',
      'header' => "User-Agent: SitemapPinger/1.0\r\n",
      'timeout' => 10,
    ]
  ]);
  $res = @file_get_contents($url, false, $ctx);
  $status = 0;
  if (isset($http_response_header[0]) && preg_match('#\s(\d{3})\s#', $http_response_header[0], $m)) {
    $status = (int)$m[1];
  }
  return ['status' => $status, 'body' => $res !== false ? substr((string)$res,0,200) : null];
}
