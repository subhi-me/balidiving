<?php
// pricelist.php
/* =========================================================
   BALI DIVING — GENERAL PRICE LIST (DB VERSION)
   - Data dari tabel `activities`
   - Kurs USD→IDR dari https://balidiving.com/template/api/kurs_bca
   - USD = harga utama, Rp = konversi (3 digit terakhir opacity 80%)
   - Style kartu ala Traveloka + filter + sort
   ========================================================= */

declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

/* ===== DB CONFIG ===== */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/* ===== PDO ===== */
$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$opt = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $DB_USER, $DB_PASS, $opt);

/* ===== Helpers ===== */
function money_usd($n){
  return '$'.number_format((float)$n, 0); // tanpa desimal supaya clean
}

/**
 * Format Rp dengan 3 digit terakhir opacity 80%
 * contoh output: 1.234.<span class="opacity-80">000</span>
 */
function rupiah_split_html($n): string {
  $v = (int)round((float)$n);
  if ($v <= 0) {
    return '<span class="opacity-80">0</span>';
  }

  // ambil digit murni
  $pure = preg_replace('/\D/', '', (string)$v);
  if ($pure === '') {
    return '<span class="opacity-80">0</span>';
  }

  if (strlen($pure) <= 3) {
    return '<span class="opacity-80">'.$pure.'</span>';
  }

  $head = substr($pure, 0, -3);
  $tail = substr($pure, -3); // 3 digit terakhir

  // format head dengan thousand separator
  $headInt  = (int)$head;
  $headFmt  = number_format($headInt, 0, ',', '.');

  return $headFmt . '.<span class="opacity-80">'.$tail.'</span>';
}

/* =========================================================
   FX RATE FROM https://balidiving.com/template/api/kurs_bca
   ========================================================= */

function fetch_fx_usd_idr_from_bca(): float {
  $url  = 'https://balidiving.com/template/api/kurs_bca';
  $rate = null;

  try {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CONNECTTIMEOUT => 5,
      CURLOPT_TIMEOUT        => 8,
      CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $out = curl_exec($ch);
    curl_close($ch);

    if ($out === false || $out === null) {
      throw new RuntimeException('No response');
    }

    // coba parse JSON
    $json = json_decode($out, true);
    if (is_array($json)) {
      $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($json));
      foreach ($it as $val) {
        if (is_numeric($val) && (float)$val > 0) {
          $rate = (float)$val;
          break;
        }
      }
    }

    // kalau gagal JSON → parse angka dari string
    if (!$rate) {
      if (preg_match('~([0-9][0-9\.\,]+)~', $out, $m)) {
        $raw = $m[1];
        $raw = str_replace('.', '', $raw);
        $raw = str_replace(',', '.', $raw);
        $rate = (float)$raw;
      }
    }
  } catch (Throwable $e){
    // biar ke fallback
  }

  if (!$rate || $rate <= 0) {
    $rate = 16000.0; // fallback aman
  }
  return $rate;
}

$FX = fetch_fx_usd_idr_from_bca();

/* =========================================================
   LOAD DATA DARI TABEL activities
   ========================================================= */

$rows = [];
try {
  $rows = $pdo->query("
    SELECT *
    FROM activities
    WHERE is_active = 1
    ORDER BY category, is_best DESC, updated_at DESC
  ")->fetchAll();
} catch(Throwable $e){
  $rows = [];
}

/* Fallback dummy kalau benar-benar kosong */
if (!$rows) {
  $rows[] = [
    'id'           => 1,
    'category'     => 'snorkeling',
    'title'        => 'Snorkeling – Padang Bai',
    'location'     => 'Padang Bai',
    'level'        => 'Beginner friendly',
    'dives'        => '',
    'duration'     => 'Day Trip',
    'highlights'   => "Calm bay with colorful shallow reefs\nHotel pickup & all equipment\nProfessional local guides",
    'usd_price'    => 47,
    'discount_pct' => 0,
    'is_best'      => 1,
    'image_url'    => 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg',
    'updated_at'   => date('Y-m-d H:i:s'),
  ];
}

/* =========================================================
   CATEGORY LABEL & ICON
   ========================================================= */

function category_meta(string $cat): array {
  $key = strtolower(trim($cat));
  switch ($key) {
    case 'snorkeling':
      return ['label'=>'Snorkeling', 'icon'=>'fa-water'];
    case 'scuba':
    case 'fun_diving':
    case 'fun diving':
      return ['label'=>'Fun Diving', 'icon'=>'fa-scuba-tank'];
    case 'try_diving':
    case 'try diving':
      return ['label'=>'Try Diving', 'icon'=>'fa-person-swimming'];
    case 'padi_courses':
    case 'course':
    case 'courses':
      return ['label'=>'PADI Courses', 'icon'=>'fa-certificate'];
    case 'special_packages':
    case 'package':
    case 'packages':
      return ['label'=>'Special Packages', 'icon'=>'fa-gift'];
    case 'add_ons':
    case 'add-on':
    case 'addon':
      return ['label'=>'Add-ons', 'icon'=>'fa-plus-circle'];
    default:
      return ['label'=>ucwords($cat ?: 'Other'), 'icon'=>'fa-water'];
  }
}

/* =========================================================
   FILTER DATA: LOCATIONS & CATEGORIES
   ========================================================= */

$locations  = [];
$categories = [];

foreach ($rows as $r) {
  $loc = trim((string)($r['location'] ?? ''));
  if ($loc !== '') $locations[$loc] = true;

  $cat = trim((string)($r['category'] ?? ''));
  if ($cat !== '') $categories[$cat] = true;
}
$locations  = array_keys($locations);
sort($locations);
$categories = array_keys($categories);
sort($categories);

/* =========================================================
   BEST PICK UNTUK BANNER ATAS
   ========================================================= */

usort($rows, function($a,$b){
  $ah = isset($a['usd_price']) && $a['usd_price'] !== null;
  $bh = isset($b['usd_price']) && $b['usd_price'] !== null;

  if (!empty($a['is_best']) && empty($b['is_best'])) return -1;
  if (empty($a['is_best']) && !empty($b['is_best'])) return 1;

  if ($ah && !$bh) return -1;
  if (!$ah && $bh) return 1;

  $ua = $ah ? (float)$a['usd_price'] : 999999;
  $ub = $bh ? (float)$b['usd_price'] : 999999;
  if ($ua === $ub) return strcmp($a['title'], $b['title']);
  return $ua < $ub ? -1 : 1;
});

$best = $rows[0];
$bestUsd   = isset($best['usd_price']) ? (float)$best['usd_price'] : null;
$bestDisc  = isset($best['discount_pct']) ? (float)$best['discount_pct'] : 0;
$bestUsdFinal = $bestUsd !== null ? $bestUsd * (1 - $bestDisc/100) : null;
$bestIdrFinal = $bestUsdFinal !== null ? $bestUsdFinal * $FX : null;
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bali Diving — General Pricelist</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { background: #f7fbff; }
    .card { box-shadow: 0 10px 30px rgba(2,132,199,.08); border: 1px solid #e6eef6; }
    .chip { background: #eaf6ff; color:#0369a1; font-weight:600; }
    .badge { background:#ecfdf5; color:#047857; font-weight:700; }
  </style>
</head>
<body class="min-h-full">
<div class="max-w-6xl mx-auto px-4 py-6">

  <!-- HEADER -->
  <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Bali Diving – General Pricelist</h1>
      <p class="text-slate-600 text-sm">
        All programs from snorkeling to pro-level courses.<br>
        Prices in <strong>USD</strong> with estimated <strong>IDR</strong>
        using rate <strong><?=number_format($FX,0,',','.')?></strong> per $1.
      </p>
    </div>
    <div class="flex items-center gap-2">
      <div class="chip px-3 py-1 rounded-full text-sm">
        <i class="fa-solid fa-shield-check mr-1"></i>25+ Years Experience
      </div>
      <div class="chip px-3 py-1 rounded-full text-sm">
        <i class="fa-solid fa-life-ring mr-1"></i>PADI 5-Star Team
      </div>
    </div>
  </header>

  <!-- FILTERS -->
  <section class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-5">
    <select id="filterCategory" class="w-full border rounded-lg px-3 py-2 text-sm">
      <option value="">All Categories</option>
      <?php foreach($categories as $cat): ?>
        <option value="<?=htmlspecialchars($cat)?>"><?=htmlspecialchars(ucwords($cat))?></option>
      <?php endforeach; ?>
    </select>

    <select id="filterLocation" class="w-full border rounded-lg px-3 py-2 text-sm">
      <option value="">All Locations</option>
      <?php foreach($locations as $loc): ?>
        <option value="<?=htmlspecialchars($loc)?>"><?=htmlspecialchars($loc)?></option>
      <?php endforeach; ?>
    </select>

    <select id="sortBy" class="w-full border rounded-lg px-3 py-2 text-sm">
      <option value="recommended">Recommended</option>
      <option value="price_asc">Price: Low to High</option>
      <option value="price_desc">Price: High to Low</option>
      <option value="name_az">Name A–Z</option>
    </select>

    <input id="filterQuery" class="w-full border rounded-lg px-3 py-2 text-sm"
           placeholder="Search product title…">
  </section>

  <!-- BEST PICK BANNER -->
  <section class="mb-6">
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div class="flex items-start gap-3">
        <span class="badge px-3 py-1 rounded-full text-xs uppercase tracking-wide flex items-center gap-1">
          <i class="fa-solid fa-star"></i> Best value pick
        </span>
        <div class="text-slate-800">
          <div class="font-semibold">
            <?=htmlspecialchars($best['title'])?>
            <?php if(!empty($best['location'])): ?>
              — <span class="text-slate-500"><?=htmlspecialchars($best['location'])?></span>
            <?php endif; ?>
          </div>
          <div class="text-xs text-emerald-700 font-medium">
            <?= $bestUsdFinal !== null
              ? 'Good balance of price & experience'
              : 'Popular choice – ask us for price details'
            ?>
          </div>
        </div>
      </div>
      <div class="text-right w-full sm:w-auto">
        <?php if($bestUsdFinal !== null): ?>
          <div class="text-lg font-bold text-slate-900">
            <?=money_usd($bestUsdFinal)?>
          </div>
          <div class="text-xs text-slate-500">
            ≈ Rp <?=rupiah_split_html($bestIdrFinal)?>
          </div>
        <?php else: ?>
          <div class="text-sm font-semibold text-slate-800">
            Contact us for today’s rate
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- LIST CARDS -->
  <section id="cards" class="space-y-4">
    <?php foreach($rows as $r):
      $usd  = isset($r['usd_price']) ? (float)$r['usd_price'] : null;
      $disc = isset($r['discount_pct']) ? (float)$r['discount_pct'] : 0;
      $usdFinal = $usd !== null ? $usd * (1 - $disc/100) : null;
      $idrFinal = $usdFinal !== null ? $usdFinal * $FX : null;
      $saveIdr  = ($usd !== null && $usdFinal !== null) ? ($usd - $usdFinal) * $FX : 0;

      $img = $r['image_url'] ?? '';
      if (!$img) {
        $img = 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg';
      }

      $meta = category_meta($r['category'] ?? '');
      $highlights = [];
      if (!empty($r['highlights'])) {
        $highlights = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string)$r['highlights'])));
      }
    ?>
    <article class="card bg-white rounded-xl p-4 flex gap-4 items-stretch"
             data-category="<?=htmlspecialchars($r['category'])?>"
             data-location="<?=htmlspecialchars(strtolower((string)$r['location']))?>"
             data-title="<?=htmlspecialchars(strtolower((string)$r['title']))?>"
             data-price="<?=$usdFinal !== null ? htmlspecialchars($usdFinal) : ''?>">

      <div class="w-28 h-28 shrink-0 rounded-lg bg-cover bg-center border"
           style="background-image:url('<?=htmlspecialchars($img)?>')"></div>

      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-1">
          <span class="chip px-2 py-0.5 rounded text-xs inline-flex items-center">
            <i class="fa-solid <?=$meta['icon']?> mr-1"></i><?=$meta['label']?>
          </span>
          <?php if(!empty($r['level'])): ?>
            <span class="text-xs text-slate-500">• <?=htmlspecialchars($r['level'])?></span>
          <?php endif; ?>
          <?php if(!empty($r['dives'])): ?>
            <span class="text-xs text-slate-500">• <?=htmlspecialchars($r['dives'])?> dives</span>
          <?php endif; ?>
          <?php if(!empty($r['duration'])): ?>
            <span class="text-xs text-slate-500">• <?=htmlspecialchars($r['duration'])?></span>
          <?php endif; ?>
        </div>

        <h3 class="font-semibold text-slate-900 leading-tight">
          <?=htmlspecialchars($r['title'])?>
        </h3>

        <?php if(!empty($r['location'])): ?>
          <div class="text-sm text-slate-600 mb-2">
            <i class="fa-solid fa-location-dot mr-1 text-sky-600"></i>
            <?=htmlspecialchars($r['location'])?>
          </div>
        <?php endif; ?>

        <?php if($highlights): ?>
          <ul class="text-sm text-slate-700 list-disc pl-4">
            <?php foreach(array_slice($highlights,0,3) as $h): ?>
              <li><?=htmlspecialchars($h)?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="w-40 flex flex-col items-end justify-between">
        <div class="text-right">
          <?php if($usdFinal !== null): ?>
            <?php if($disc>0 && $saveIdr>0): ?>
              <div class="text-xs text-emerald-700 font-semibold">
                Save Rp <?=rupiah_split_html($saveIdr)?>
              </div>
            <?php endif; ?>
            <div class="text-xl font-bold text-slate-900">
              <?=money_usd($usdFinal)?>
            </div>
            <div class="text-xs text-slate-500">
              ≈ Rp <?=rupiah_split_html($idrFinal)?>
            </div>
          <?php else: ?>
            <div class="text-sm font-semibold text-slate-800">
              Contact us for price
            </div>
            <div class="text-xs text-slate-500">
              Program available — final rate depends on season &amp; request.
            </div>
          <?php endif; ?>
        </div>

        <a href="reservation.php"
           class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-600 text-white font-medium hover:bg-sky-700 text-sm">
          Choose
          <i class="fa-solid fa-arrow-right-long"></i>
        </a>
      </div>
    </article>
    <?php endforeach; ?>
  </section>
</div>

<script>
  const cards   = Array.from(document.querySelectorAll('#cards article'));
  const fCat    = document.getElementById('filterCategory');
  const fLoc    = document.getElementById('filterLocation');
  const fQuery  = document.getElementById('filterQuery');
  const fSort   = document.getElementById('sortBy');
  const cardsContainer = document.getElementById('cards');

  function applyFilterAndSort(){
    const cat = (fCat.value || '').toLowerCase();
    const loc = (fLoc.value || '').toLowerCase();
    const q   = (fQuery.value || '').trim().toLowerCase();
    const sort= fSort.value || 'recommended';

    const visible = [];
    cards.forEach(c=>{
      const okCat = !cat || (c.dataset.category || '').toLowerCase() === cat;
      const okLoc = !loc || (c.dataset.location || '').toLowerCase() === loc;
      const okQ   = !q   || (c.dataset.title || '').includes(q);
      const show  = okCat && okLoc && okQ;
      c.style.display = show ? '' : 'none';
      if(show) visible.push(c);
    });

    if (sort !== 'recommended') {
      visible.sort((a,b)=>{
        const pa = parseFloat(a.dataset.price || '9999999');
        const pb = parseFloat(b.dataset.price || '9999999');
        const ta = (a.dataset.title || '');
        const tb = (b.dataset.title || '');
        if (sort === 'price_asc')  return pa - pb;
        if (sort === 'price_desc') return pb - pa;
        if (sort === 'name_az')    return ta.localeCompare(tb);
        return 0;
      });
      visible.forEach(c=>cardsContainer.appendChild(c));
    }
  }

  [fCat, fLoc, fSort].forEach(el=>el.addEventListener('change', applyFilterAndSort));
  fQuery.addEventListener('input', applyFilterAndSort);
</script>
</body>
</html>
