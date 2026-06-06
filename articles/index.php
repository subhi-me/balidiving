<?php
// index.php (pagination 12 per page + deskripsi = "Judul • 20 kata pertama body", tanpa tampilkan url/filename)

declare(strict_types=1);

function titleCaseFromFilename(string $filename): string {
  $base = preg_replace('/\.php$/i', '', $filename);
  $base = str_replace(['-', '_'], ' ', $base);
  $words = preg_split('/\s+/', trim($base)) ?: [];
  $words = array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)) . mb_substr($w, 1), $words);
  return implode(' ', $words);
}

function excerptFromPhpFile(string $path, int $maxWords = 20): string {
  $raw = @file_get_contents($path);
  if ($raw === false) return '';

  // buang blok PHP
  $raw = preg_replace('/<\?php[\s\S]*?\?>/i', ' ', $raw) ?? $raw;

  // ke text
  $text = strip_tags($raw);
  $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $text = preg_replace('/\s+/u', ' ', trim($text)) ?? trim($text);
  if ($text === '') return '';

  $words = preg_split('/\s+/u', $text) ?: [];
  $slice = array_slice($words, 0, $maxWords);
  $excerpt = implode(' ', $slice);

  if (count($words) > $maxWords) $excerpt .= '…';
  return $excerpt;
}

function listPhpFiles(string $dir): array {
  $files = glob($dir . DIRECTORY_SEPARATOR . '*.php') ?: [];
  $out = [];

  foreach ($files as $path) {
    $name = basename($path);
    if (strcasecmp($name, basename(__FILE__)) === 0) continue;
    if (str_starts_with($name, '.')) continue;

    $title = titleCaseFromFilename($name);
    $body20 = excerptFromPhpFile($path, 20);

    $out[] = [
      'filename' => $name,
      'title'    => $title,
      'url'      => $name,
      // format: "Judul • isi body"
      'snippet'  => $title . ' • ' . ($body20 !== '' ? $body20 : 'Click to open'),
      'mtime'    => @filemtime($path) ?: 0,
    ];
  }

  usort($out, fn($a, $b) => ($b['mtime'] <=> $a['mtime']) ?: strcmp($a['filename'], $b['filename']));
  return $out;
}

// JSON endpoint: /index.php?api=1&page=1&per_page=12
if (isset($_GET['api'])) {
  $page = max(1, (int)($_GET['page'] ?? 1));
  $perPage = (int)($_GET['per_page'] ?? 12);
  $perPage = ($perPage <= 0) ? 12 : min($perPage, 100);

  $all = listPhpFiles(__DIR__);
  $total = count($all);
  $totalPages = max(1, (int)ceil($total / $perPage));
  $page = min($page, $totalPages);

  $offset = ($page - 1) * $perPage;
  $slice = array_slice($all, $offset, $perPage);

  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

  echo json_encode([
    'ok' => true,
    'page' => $page,
    'per_page' => $perPage,
    'total' => $total,
    'total_pages' => $totalPages,
    'files' => $slice,
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit;
}
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP Files Thumbnail</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { box-sizing: border-box; }
    .thumbnail-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .thumbnail-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0,0,0,.1), 0 10px 10px -5px rgba(0,0,0,.04); }
    .thumbnail-img { transition: transform 0.3s ease; }
    .thumbnail-card:hover .thumbnail-img { transform: scale(1.05); }
    .loading-spinner { border: 3px solid rgba(255,255,255,.3); border-radius: 50%; border-top: 3px solid white; width: 40px; height: 40px; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
  </style>
</head>

<body class="h-full">
  <div class="w-full h-full overflow-auto" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="w-full min-h-full" style="background-color: #f8f9fa;">

      <header class="w-full" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 48px 24px;">
        <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
          <h1 style="font-size: 48px; font-weight: bold; color: #ffffff; margin-bottom: 16px; font-family: 'Georgia', serif;">
            Bali Diving Blog
          </h1>
          <p style="font-size: 20px; color: #e0e7ff; font-family: 'Arial', sans-serif;">
            Blog and Articles
          </p>
        </div>
      </header>

      <main style="max-width: 1200px; margin: 0 auto; padding: 48px 24px;">
        <div id="loading-state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 64px 24px;">
          <div class="loading-spinner"></div>
          <p style="margin-top: 24px; font-size: 16px; color: #6b7280;">Scanning for PHP files...</p>
        </div>

        <div id="empty-state" style="display: none; text-align: center; padding: 64px 24px;">
          <svg style="width: 80px; height: 80px; margin: 0 auto 24px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h2 style="font-size: 24px; font-weight: 600; color: #374151; margin-bottom: 12px;">No PHP files found</h2>
          <p style="font-size: 16px; color: #6b7280;">No PHP files found in the current directory</p>
        </div>

        <div id="thumbnail-grid" style="display: none; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 32px;"></div>

        <div id="pager" style="display:none; margin-top: 28px;">
          <div class="flex items-center justify-center gap-3 flex-wrap">
            <button id="btn-prev" class="px-4 py-2 rounded-lg bg-white/90 hover:bg-white shadow text-gray-800 font-medium">Prev</button>
            <div id="page-info" class="text-sm text-gray-700"></div>
            <button id="btn-next" class="px-4 py-2 rounded-lg bg-white/90 hover:bg-white shadow text-gray-800 font-medium">Next</button>
          </div>
        </div>
      </main>

      <footer style="background-color: #2d3748; color: #e2e8f0; padding: 32px 24px; margin-top: 64px;">
        <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
          <p style="font-size: 14px;">© <?= date('Y') ?>. Auto-indexer.</p>
        </div>
      </footer>

    </div>
  </div>

<script>
  const thumbnailUrls = [
    "https://balidiving.com/images/thumbnails/1-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/2-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/3-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/4-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/5-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/6-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/7-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/8-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/9-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/10-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/11-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/12-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/13-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/14-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/15-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/16-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/17-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/18-bali-diving.jpg",
    "https://balidiving.com/images/thumbnails/19-bali-diving.jpg",
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
    "https://balidiving.com/images/thumbnails/30-bali-diving.jpg"
  ];

  const PER_PAGE = 12;

  let currentPage = 1;
  let totalPages = 1;

  function getRandomThumbnail() {
    return thumbnailUrls[Math.floor(Math.random() * thumbnailUrls.length)];
  }

  function qs(name) {
    const url = new URL(window.location.href);
    return url.searchParams.get(name);
  }

  function setPageParam(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    window.history.pushState({}, '', url.toString());
  }

  function renderThumbnails(phpFiles) {
    const grid = document.getElementById('thumbnail-grid');
    grid.innerHTML = '';

    phpFiles.forEach((file) => {
      const card = document.createElement('div');
      card.className = 'thumbnail-card';
      card.style.cssText = `
        background-color:#fff;border-radius:12px;overflow:hidden;
        box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
        cursor:pointer;
      `;

      const imgContainer = document.createElement('div');
      imgContainer.style.cssText = `width:100%;height:220px;overflow:hidden;background:#e5e7eb;`;

      const img = document.createElement('img');
      img.src = getRandomThumbnail();
      img.alt = file.title || 'Post';
      img.className = 'thumbnail-img';
      img.style.cssText = `width:100%;height:100%;object-fit:cover;`;
      img.onerror = () => {
        imgContainer.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#9ca3af;font-size:14px;">📄</div>';
      };

      const content = document.createElement('div');
      content.style.cssText = `padding:20px;`;

      const title = document.createElement('h3');
      title.style.cssText = `font-size:18px;font-weight:600;color:#2d3748;margin-bottom:10px;font-family:Georgia, serif;`;
      title.textContent = file.title || 'Untitled';

      // tampilkan format: "Judul • isi body 20 kata"
      const description = document.createElement('p');
      description.style.cssText = `font-size:14px;color:#6b7280;line-height:1.6;`;
      description.textContent = (file.snippet && file.snippet.trim())
        ? file.snippet
        : ((file.title || 'Untitled') + ' • Click to open');

      content.appendChild(title);
      content.appendChild(description);

      imgContainer.appendChild(img);
      card.appendChild(imgContainer);
      card.appendChild(content);

      card.addEventListener('click', () => window.open(file.url, '_blank', 'noopener,noreferrer'));

      grid.appendChild(card);
    });
  }

  function updatePager(total, page, perPage, totalPages_) {
    const pager = document.getElementById('pager');
    const info = document.getElementById('page-info');
    const prev = document.getElementById('btn-prev');
    const next = document.getElementById('btn-next');

    totalPages = totalPages_;

    if (total <= perPage) {
      pager.style.display = 'none';
      return;
    }

    pager.style.display = 'block';
    info.textContent = `Page ${page} / ${totalPages} • Total ${total} Posts`;

    prev.disabled = (page <= 1);
    next.disabled = (page >= totalPages);

    prev.classList.toggle('opacity-50', prev.disabled);
    prev.classList.toggle('cursor-not-allowed', prev.disabled);
    next.classList.toggle('opacity-50', next.disabled);
    next.classList.toggle('cursor-not-allowed', next.disabled);
  }

  async function loadPage(page) {
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');
    const grid = document.getElementById('thumbnail-grid');

    loadingState.style.display = 'flex';
    emptyState.style.display = 'none';
    grid.style.display = 'none';

    try {
      const res = await fetch(`?api=1&page=${page}&per_page=${PER_PAGE}`, { cache: 'no-store' });
      const data = await res.json();

      currentPage = data.page || page;

      if (!data.files || data.files.length === 0) {
        loadingState.style.display = 'none';
        emptyState.style.display = 'block';
        grid.style.display = 'none';
        document.getElementById('pager').style.display = 'none';
        return;
      }

      loadingState.style.display = 'none';
      emptyState.style.display = 'none';
      grid.style.display = 'grid';

      renderThumbnails(data.files);
      updatePager(data.total, data.page, data.per_page, data.total_pages);
      setPageParam(currentPage);
      window.scrollTo({ top: 0, behavior: 'smooth' });

    } catch (e) {
      console.error(e);
      loadingState.style.display = 'none';
      emptyState.style.display = 'block';
      grid.style.display = 'none';
      document.getElementById('pager').style.display = 'none';
    }
  }

  document.getElementById('btn-prev').addEventListener('click', () => {
    if (currentPage > 1) loadPage(currentPage - 1);
  });
  document.getElementById('btn-next').addEventListener('click', () => {
    if (currentPage < totalPages) loadPage(currentPage + 1);
  });

  window.addEventListener('popstate', () => {
    const p = parseInt(qs('page') || '1', 10);
    loadPage(isNaN(p) ? 1 : p);
  });

  (function init(){
    const p = parseInt(qs('page') || '1', 10);
    loadPage(isNaN(p) ? 1 : p);
  })();
</script>
</body>
</html>
