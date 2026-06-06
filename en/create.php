<?php
// generate_index.php — Dark Pastel "Master Dashboard"
// Scans folder, takes <title> from HTML/PHP files, and creates a secure, non-indexable index.php

$outputFile = __DIR__ . '/index.php';
$ignoreList = [basename(__FILE__), basename($outputFile)];

function get_page_title($path) {
    $content = @file_get_contents($path);
    if (preg_match('/<title>(.*?)<\/title>/si', $content, $m)) {
        return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
    return ucwords(str_replace(['-', '_', '.php', '.html'], ' ', basename($path)));
}

$files = scandir(__DIR__);
$links = [];
foreach ($files as $f) {
    if (in_array($f, $ignoreList) || $f[0] === '.') continue;
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (!in_array($ext, ['php', 'html'])) continue;

    $path = __DIR__ . DIRECTORY_SEPARATOR . $f;
    if (!is_file($path)) continue;

    $title = get_page_title($path);
    $links[] = [
        'file' => $f,
        'title' => $title
    ];
}

usort($links, fn($a,$b)=>strcasecmp($a['title'],$b['title']));

$colors = [
    '#ffb3ba', '#ffdfba', '#ffffba', '#baffc9', '#bae1ff',
    '#e0bbff', '#ffd6e0', '#ffb6c1', '#c8e6c9', '#a5d8ff',
    '#f8c291', '#dfe6e9'
];

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Master Dashboard</title>

<!-- Anti-SEO / Non-indexing -->
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
<meta name="googlebot" content="noindex, nofollow">
<meta name="bingbot" content="noindex, nofollow">
<meta name="referrer" content="no-referrer">
<meta name="author" content="Subhi.me Universe">
<meta name="description" content="Private internal dashboard for Subhi.me.">

<style>
  *{box-sizing:border-box;}
  html,body{
    margin:0; padding:0;
    font-family: 'Poppins', 'Inter', sans-serif;
    background: radial-gradient(circle at 30% 10%, #0f172a 0%, #020617 100%);
    color:#e2e8f0;
    display:flex;
    flex-direction:column;
    align-items:center;
    min-height:100vh;
  }
  header{
    text-align:center; padding:60px 20px 30px;
  }
  h1{
    font-size:2.6rem;
    background: linear-gradient(90deg,#38bdf8,#a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight:700;
    letter-spacing:0.8px;
    margin:0;
  }
  .grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
    gap:28px;
    width:100%; max-width:1100px;
    padding:40px 25px 100px;
  }
  .card{
    border-radius:22px;
    overflow:hidden;
    background:#1e293b;
    box-shadow:0 0 0 1px rgba(255,255,255,0.05), 0 8px 25px rgba(0,0,0,0.4);
    transition:all 0.3s ease;
  }
  .card:hover{
    transform:translateY(-6px) scale(1.03);
    box-shadow:0 10px 30px rgba(255,255,255,0.15);
  }
  .card a{
    display:block;
    color:inherit;
    text-decoration:none;
    height:100%;
  }
  .thumb{
    height:180px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:36px;
    font-weight:700;
    color:#0f172a;
  }
  .card-body{
    padding:22px 24px 26px;
    background:#0f172a;
    border-top:1px solid rgba(255,255,255,0.05);
  }
  .title{
    font-size:20px;
    font-weight:600;
    line-height:1.4;
    color:#e2e8f0;
  }
  .title:hover{
    color:#38bdf8;
  }
  footer{
    text-align:center;
    padding:30px;
    font-size:13px;
    color:#64748b;
    border-top:1px solid rgba(255,255,255,0.05);
    width:100%;
  }
</style>
</head>
<body>
  <header>
    <h1>Master Dashboard</h1>
  </header>
  <div class="grid">
HTML;

foreach ($links as $i => $l) {
    $color = $colors[$i % count($colors)];
    $file = htmlspecialchars($l['file']);
    $title = htmlspecialchars($l['title']);
    $initial = strtoupper(substr($title, 0, 1));
    $html .= <<<CARD
    <div class="card">
      <a href="$file" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:$color">$initial</div>
        <div class="card-body">
          <div class="title">$title</div>
        </div>
      </a>
    </div>
CARD;
}

$html .= <<<HTML
  </div>
  <footer>© Subhi.me Universe — Private Access Only</footer>
</body>
</html>
HTML;

file_put_contents($outputFile, $html);
echo "✅ index.php (Master Dashboard) created successfully and locked from search engines.\n";
?>
