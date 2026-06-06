<?php
// sitemap-generator.php
// One-file sitemap.xml generator: paste URLs, auto-grow inputs, SEO-safe normalization, and XML download/write.
// Now with image/icon support for Google Image Sitemaps.
// Author: ChatGPT (BaliDiving project)
// Timezone: Asia/Makassar

date_default_timezone_set('Asia/Makassar');

/* --------------------------
   Helpers
--------------------------- */
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function normalize_url($url, $opts){
  $url = trim($url);
  if($url==='') return '';

  // Strip surrounding quotes
  $url = trim($url, "\"' ");

  // If missing scheme, assume https
  if(!preg_match('~^https?://~i', $url)){
    $url = 'https://' . ltrim($url, '/');
  }

  $parts = parse_url($url);
  if(!$parts || empty($parts['host'])) return '';

  $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : 'https';
  $host   = strtolower($parts['host']);
  $port   = isset($parts['port']) ? (':' . $parts['port']) : '';
  $path   = isset($parts['path']) ? $parts['path'] : '/';
  $query  = isset($parts['query']) ? $parts['query'] : '';
  $frag   = ''; // always drop fragment for sitemaps

  if(!empty($opts['force_https'])) $scheme = 'https';

  // Remove common tracking params
  if(!empty($opts['strip_tracking']) && $query){
    parse_str($query, $q);
    $kill = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid','mc_cid','mc_eid','igshid'];
    foreach($kill as $k){ unset($q[$k]); }
    $query = http_build_query($q);
  }

  // Normalize path
  if(!empty($opts['lowercase_path'])){
    // Lowercase only path segments, not percent-encodings
    $path = implode('/', array_map(function($seg){ return rawurlencode(rawurldecode(strtolower($seg))); }, explode('/', $path)));
    if($path==='') $path='/';
  }

  if(!empty($opts['trailing_slash'])){
    if(!preg_match('~\.[a-z0-9]{1,6}$~i', $path)){ // don't touch file-like paths
      if(substr($path,-1) !== '/') $path .= '/';
    }
  }

  $out = $scheme . '://' . $host . $port . $path;
  if($query !== '') $out .= '?' . $query;
  // fragment dropped
  return $out;
}

function guess_changefreq($url){
  $u = strtolower($url);
  if(str_contains($u, '/blog') || preg_match('~/\d{4}/\d{2}/~', $u)) return 'weekly';
  if(str_contains($u, '/product') || str_contains($u, '/shop')) return 'daily';
  if($u==='/' || preg_match('~^https?://[^/]+/$~', $u)) return 'daily';
  if(str_contains($u, '/category') || str_contains($u, '/tag')) return 'weekly';
  return 'monthly';
}

function guess_priority($url){
  // Slightly higher priority for homepage and key hubs
  if(preg_match('~^https?://[^/]+/$~', $url)) return '1.0';
  if(str_contains(strtolower($url), '/about') || str_contains(strtolower($url), '/contact')) return '0.8';
  return '0.5';
}

function xml_item($loc, $lastmod, $changefreq, $priority, $images = []){
  $x = "  <url>\n";
  $x .= "    <loc>" . h($loc) . "</loc>\n";
  if($lastmod)    $x .= "    <lastmod>" . h($lastmod) . "</lastmod>\n";
  if($changefreq) $x .= "    <changefreq>" . h($changefreq) . "</changefreq>\n";
  if($priority)   $x .= "    <priority>" . h($priority) . "</priority>\n";

  // Image sitemap extension
  if(!empty($images)){
    foreach($images as $img){
      if(empty($img['loc'])) continue;
      $x .= "    <image:image>\n";
      $x .= "      <image:loc>" . h($img['loc']) . "</image:loc>\n";
      if(!empty($img['title'])){
        $x .= "      <image:title>" . h($img['title']) . "</image:title>\n";
      }
      if(!empty($img['caption'])){
        $x .= "      <image:caption>" . h($img['caption']) . "</image:caption>\n";
      }
      $x .= "    </image:image>\n";
    }
  }

  $x .= "  </url>\n";
  return $x;
}

function build_sitemap(array $rows){
  $buf = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  $buf .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\">\n";
  foreach($rows as $r){
    $images = isset($r['images']) ? $r['images'] : [];
    $buf .= xml_item($r['loc'], $r['lastmod'], $r['changefreq'], $r['priority'], $images);
  }
  $buf .= "</urlset>\n";
  return $buf;
}

function chunk_array_50k($arr){
  return array_chunk($arr, 50000);
}

/* --------------------------
   Handle POST
--------------------------- */
$generated_files = [];
$errors = [];
$xml_preview = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $urls  = $_POST['url'] ?? [];
  $mods  = $_POST['lastmod'] ?? [];
  $freqs = $_POST['changefreq'] ?? [];
  $pris  = $_POST['priority'] ?? [];
  $imgs  = $_POST['images'] ?? [];

  $opts = [
    'force_https'    => !empty($_POST['force_https']),
    'trailing_slash' => !empty($_POST['trailing_slash']),
    'lowercase_path' => !empty($_POST['lowercase_path']),
    'strip_tracking' => !empty($_POST['strip_tracking'])
  ];

  // For media we don't want to force trailing slash on file-like paths,
  // but normalize scheme/host/query etc.
  $imgOpts = $opts;
  $imgOpts['trailing_slash'] = false;

  $rows = [];
  foreach($urls as $i=>$raw){
    $norm = normalize_url($raw, $opts);
    if($norm==='') continue;

    $lastmod = trim($mods[$i] ?? '');
    if($lastmod==='') $lastmod = date('c');

    $cf = trim($freqs[$i] ?? '');
    if($cf==='') $cf = guess_changefreq($norm);

    $pr = trim($pris[$i] ?? '');
    if($pr==='') $pr = guess_priority($norm);

    // Parse images/icons for this URL
    $imagesRaw = isset($imgs[$i]) ? trim($imgs[$i]) : '';
    $images = [];
    if($imagesRaw !== ''){
      // Split by newline or comma
      $parts = preg_split('~[\r\n,]+~', $imagesRaw);
      foreach($parts as $part){
        $part = trim($part);
        if($part==='') continue;

        // Format: url | title | caption (title/caption optional)
        $bits = explode('|', $part);
        $urlPart = trim($bits[0]);
        $title   = isset($bits[1]) ? trim($bits[1]) : '';
        $caption = isset($bits[2]) ? trim($bits[2]) : '';

        if($urlPart==='') continue;

        // Normalize image URL (no trailing-slash forcing)
        $imgLoc = normalize_url($urlPart, $imgOpts);
        if($imgLoc==='') continue;

        $images[] = [
          'loc'     => $imgLoc,
          'title'   => $title,
          'caption' => $caption,
        ];
      }
    }

    $rows[] = [
      'loc'        => $norm,
      'lastmod'    => $lastmod,
      'changefreq' => $cf,
      'priority'   => $pr,
      'images'     => $images,
    ];
  }

  // Deduplicate by URL (keep first occurrence)
  $seen = [];
  $rows = array_values(array_filter($rows, function($r) use (&$seen){
    if(isset($seen[$r['loc']])) return false;
    $seen[$r['loc']] = true;
    return true;
  }));

  if(empty($rows)){
    $errors[] = 'Please enter at least one valid URL.';
  } else {
    // Sort for stable output: by host then path
    usort($rows, function($a,$b){ return strcmp($a['loc'],$b['loc']); });

    // Chunk into <= 50,000 URLs per file
    $chunks = chunk_array_50k($rows);

    if(count($chunks)===1){
      $xml = build_sitemap($chunks[0]);
      $fname = '/mnt/data/sitemap.xml';
      file_put_contents($fname, $xml);
      $generated_files[] = $fname;
      $xml_preview = $xml;
    } else {
      // Build multiple sitemaps + index
      $index = [];
      foreach($chunks as $k=>$ch){
        $xml = build_sitemap($ch);
        $fn  = "/mnt/data/sitemap-" . ($k+1) . ".xml";
        file_put_contents($fn, $xml);
        $generated_files[] = $fn;
        $index[] = [ 'loc' => 'sitemap-' . ($k+1) . '.xml', 'lastmod' => date('c') ];
      }
      // Build index
      $buf = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
      $buf .= "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
      foreach($index as $it){
        $buf .= "  <sitemap>\n";
        $buf .= "    <loc>" . h($it['loc']) . "</loc>\n";
        $buf .= "    <lastmod>" . h($it['lastmod']) . "</lastmod>\n";
        $buf .= "  </sitemap>\n";
      }
      $buf .= "</sitemapindex>\n";
      $idxname = '/mnt/data/sitemap-index.xml';
      file_put_contents($idxname, $buf);
      $generated_files[] = $idxname;
      $xml_preview = $buf;
    }
  }
}

/* --------------------------
   UI
--------------------------- */
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sitemap XML Generator</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { primary: '#3552c8', navy:'#063c7f' }
        }
      }
    }
  </script>
  <style>
    .mono{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace}
    .grid-head{position:sticky;top:0;background:rgba(15,23,42,.9);backdrop-filter:saturate(180%) blur(6px)}
  </style>
</head>
<body class="min-h-full bg-slate-900 text-slate-100">
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-semibold">Sitemap XML Generator</h1>
    <p class="text-slate-300 mt-2">
      Paste your URLs. Each time you fill the last URL field, a new row appears automatically.
      We normalize for SEO and generate a compliant <span class="mono">sitemap.xml</span>, including
      optional <span class="mono">&lt;image:image&gt;</span> entries for images/icons.
    </p>

    <?php if(!empty($errors)): ?>
      <div class="mt-4 p-4 rounded-lg bg-red-800/40 border border-red-500">
        <ul class="list-disc pl-6"><?php foreach($errors as $e){ echo '<li>'.h($e).'</li>'; } ?></ul>
      </div>
    <?php endif; ?>

    <form method="post" class="mt-6" id="formGen">
      <div class="flex flex-wrap gap-4 p-4 rounded-xl bg-slate-800/60 border border-slate-700">
        <label class="flex items-center gap-2"><input type="checkbox" name="force_https" class="accent-primary" checked> <span>Force HTTPS</span></label>
        <label class="flex items-center gap-2"><input type="checkbox" name="trailing_slash" class="accent-primary" checked> <span>Add trailing slash</span></label>
        <label class="flex items-center gap-2"><input type="checkbox" name="lowercase_path" class="accent-primary" checked> <span>Lowercase path</span></label>
        <label class="flex items-center gap-2"><input type="checkbox" name="strip_tracking" class="accent-primary" checked> <span>Strip tracking params</span></label>
      </div>

      <div class="mt-6 overflow-x-auto rounded-xl border border-slate-700">
        <table class="min-w-[1100px] w-full text-sm">
          <thead class="grid-head">
            <tr class="bg-slate-800 text-slate-200">
              <th class="text-left p-3 w-[32%]">URL</th>
              <th class="text-left p-3 w-[18%]">Lastmod (ISO 8601)</th>
              <th class="text-left p-3 w-[14%]">Changefreq</th>
              <th class="text-left p-3 w-[10%]">Priority</th>
              <th class="text-left p-3 w-[26%]">Images / Icons (URL[|title|caption])</th>
            </tr>
          </thead>
          <tbody id="rows">
            <?php
              $prefill = $_POST && isset($_POST['url']) ? $_POST['url'] : [''];
              $n = max( count($prefill), 5 );
              for($i=0;$i<$n;$i++):
                $u = $_POST['url'][$i] ?? '';
                $m = $_POST['lastmod'][$i] ?? '';
                $c = $_POST['changefreq'][$i] ?? '';
                $p = $_POST['priority'][$i] ?? '';
                $imgField = $_POST['images'][$i] ?? '';
            ?>
            <tr class="odd:bg-slate-900 even:bg-slate-800/40">
              <td class="p-2">
                <input name="url[]" value="<?=h($u)?>" placeholder="https://example.com/" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">
              </td>
              <td class="p-2">
                <input name="lastmod[]" value="<?=h($m)?>" placeholder="<?=h(date('c'))?>" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">
              </td>
              <td class="p-2">
                <select name="changefreq[]" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">
                  <option value="" <?= $c===''?'selected':'' ?>>Auto</option>
                  <?php foreach(['always','hourly','daily','weekly','monthly','yearly','never'] as $opt){
                    $sel = $c===$opt?'selected':''; echo "<option $sel>$opt</option>"; }
                  ?>
                </select>
              </td>
              <td class="p-2">
                <input name="priority[]" value="<?=h($p)?>" placeholder="auto" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">
              </td>
              <td class="p-2">
                <textarea name="images[]" rows="2" placeholder="https://example.com/img.jpg|Title|Caption&#10;https://example.com/icon.svg|Icon Title" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700"><?=h($imgField)?></textarea>
              </td>
            </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-6 flex gap-3 flex-wrap">
        <button class="px-4 py-2 rounded-xl bg-primary hover:bg-navy transition text-white">Generate sitemap</button>
        <button type="button" id="add10" class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600">+ Add 10 rows</button>
        <button type="button" id="pasteBulk" class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600">Paste bulk</button>
      </div>
    </form>

    <?php if(!empty($generated_files)): ?>
      <div class="mt-10 p-4 rounded-xl border border-emerald-600 bg-emerald-900/30">
        <h2 class="text-xl font-semibold mb-2">Generated file(s)</h2>
        <ul class="list-disc pl-6">
          <?php foreach($generated_files as $g): ?>
            <li><a class="underline" href="<?=h($g)?>" download>Download <?=h(basename($g))?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="mt-6">
        <h3 class="text-lg font-semibold">Preview</h3>
        <pre class="mono p-4 rounded-xl bg-slate-950 overflow-x-auto border border-slate-800"><?php echo h($xml_preview); ?></pre>
      </div>
    <?php endif; ?>

    <div class="mt-10 text-slate-400 text-sm">
      <h3 class="font-semibold text-slate-200 mb-2">Tips</h3>
      <ul class="list-disc pl-6">
        <li>Upload <span class="mono">sitemap.xml</span> to your site root and reference it in <span class="mono">robots.txt</span> with <span class="mono">Sitemap: https://yourdomain.com/sitemap.xml</span>.</li>
        <li>For very large sites, we automatically split into multiple files and create a <span class="mono">sitemap-index.xml</span>.</li>
        <li>You can add images/icons per URL. Use format <span class="mono">image-url|Title|Caption</span> to help search engines index visual content.</li>
        <li>Keep URLs canonical, HTTPS, without tracking params, and end with trailing slashes for clean SEO.</li>
      </ul>
    </div>
  </div>

  <script>
  (function(){
    const rowsEl = document.getElementById('rows');

    function addRow(url = '', lastmod = '', changefreq = '', priority = '', images = ''){
      const tr = document.createElement('tr');
      tr.className = 'odd:bg-slate-900 even:bg-slate-800/40';
      const iso = new Date().toISOString();
      tr.innerHTML = `
        <td class="p-2">
          <input name="url[]" value="${url}" placeholder="https://example.com/" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">
        </td>
        <td class="p-2">
          <input name="lastmod[]" value="${lastmod}" placeholder="${iso}" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">
        </td>
        <td class="p-2">
          <select name="changefreq[]" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">
            <option value="" ${changefreq==='' ? 'selected' : ''}>Auto</option>
            <option${changefreq==='always' ? ' selected' : ''}>always</option>
            <option${changefreq==='hourly' ? ' selected' : ''}>hourly</option>
            <option${changefreq==='daily' ? ' selected' : ''}>daily</option>
            <option${changefreq==='weekly' ? ' selected' : ''}>weekly</option>
            <option${changefreq==='monthly' ? ' selected' : ''}>monthly</option>
            <option${changefreq==='yearly' ? ' selected' : ''}>yearly</option>
            <option${changefreq==='never' ? ' selected' : ''}>never</option>
          </select>
        </td>
        <td class="p-2">
          <input name="priority[]" value="${priority}" placeholder="auto" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">
        </td>
        <td class="p-2">
          <textarea name="images[]" rows="2" placeholder="https://example.com/img.jpg|Title|Caption&#10;https://example.com/icon.svg|Icon Title" class="w-full p-2 rounded-lg bg-slate-900 border border-slate-700">${images}</textarea>
        </td>
      `;
      rowsEl.appendChild(tr);
      wireAutoGrow();
    }

    function wireAutoGrow(){
      const urlInputs = rowsEl.querySelectorAll('input[name="url[]"]');
      if(urlInputs.length===0) return;
      const last = urlInputs[urlInputs.length-1];
      function maybeGrow(){
        if(last.value.trim()!==''){
          // Only grow once per field
          last.removeEventListener('input', maybeGrow);
          addRow('','','','','');
        }
      }
      last.addEventListener('input', maybeGrow);
    }

    // Initial wiring
    wireAutoGrow();

    // Add 10 rows
    document.getElementById('add10').addEventListener('click', ()=>{
      for(let i=0;i<10;i++) addRow();
    });

    // Paste bulk: splits by whitespace, comma, or newline
    document.getElementById('pasteBulk').addEventListener('click', ()=>{
      const txt = prompt('Paste URLs (separated by newlines, spaces, or commas):','');
      if(!txt) return;
      const parts = txt.split(/[\n,\s]+/).map(s=>s.trim()).filter(Boolean);
      parts.forEach(u=> addRow(u));
    });
  })();
  </script>
</body>
</html>
