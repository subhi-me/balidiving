<?php
/* =========================================================
   BALI DIVING - Price List (Scuba & Snorkeling)
   - Ambil kurs USD→IDR (cache 24 jam ke fx_rates)
   - Tampil list bergaya Traveloka (kartu)
   - Filter kategori & lokasi (front-end)
   ========================================================= */

date_default_timezone_set('Asia/Makassar');

/* ===== DB CONFIG (samakan) ===== */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/* ===== PDO ===== */
$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$opt = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];
$pdo = new PDO($dsn, $DB_USER, $DB_PASS, $opt);

/* ===== Helpers ===== */
function now(){ return date('Y-m-d H:i:s'); }
function rupiah($n){
  // format: Rp 1.234.500
  return 'Rp '.number_format((float)$n, 0, ',', '.');
}
function money_usd($n){
  return '$'.number_format((float)$n, 2);
}
function fetch_fx_usd_idr(PDO $pdo): float {
  // 1) coba baca cache yang <= 24 jam
  $st = $pdo->prepare("SELECT rate, updated_at FROM fx_rates WHERE base='USD' AND quote='IDR' LIMIT 1");
  $st->execute();
  if ($row = $st->fetch()){
    $age = time() - strtotime($row['updated_at']);
    if ($age <= 86400 && (float)$row['rate'] > 0){
      return (float)$row['rate'];
    }
  }

  // 2) coba tarik dari API publik (exchangerate.host) – tidak mandatory, ada fallback
  $rate = null;
  $url = "https://api.exchangerate.host/latest?base=USD&symbols=IDR";
  try{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CONNECTTIMEOUT => 5,
      CURLOPT_TIMEOUT => 8,
      CURLOPT_SSL_VERIFYPEER => true
    ]);
    $out = curl_exec($ch);
    if ($out !== false){
      $json = json_decode($out, true);
      if (isset($json['rates']['IDR'])){
        $rate = (float)$json['rates']['IDR'];
      }
    }
    curl_close($ch);
  }catch(Throwable $e){
    // ignore
  }

  // 3) fallback: pakai 15500 bila API gagal
  if (!$rate || $rate <= 0) {
    $rate = 15500.0;
  }

  // simpan/update cache
  $pdo->prepare("REPLACE INTO fx_rates (base, quote, rate, updated_at) VALUES ('USD','IDR',:r,:u)")
      ->execute([':r'=>$rate, ':u'=>now()]);

  return $rate;
}

$FX = fetch_fx_usd_idr($pdo);

/* Ambil data aktif */
$rows = $pdo->query("SELECT * FROM activities WHERE is_active=1 ORDER BY is_best DESC, updated_at DESC")->fetchAll();

/* Siapkan lokasi unik untuk filter */
$locations = array_values(array_unique(array_map(fn($r)=>$r['location'], $rows)));
sort($locations);
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bali Diving — Price List</title>
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
    <header class="flex items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Bali Diving – Packages</h1>
        <p class="text-slate-600 text-sm">Prices in <strong>USD</strong> with real-time <strong>IDR</strong> estimate (fx <?=number_format($FX,0)?>).</p>
      </div>
      <div class="hidden sm:flex items-center gap-2">
        <div class="chip px-3 py-1 rounded-full text-sm"><i class="fa-solid fa-shield-check mr-1"></i>Trusted Center</div>
        <div class="chip px-3 py-1 rounded-full text-sm"><i class="fa-solid fa-life-ring mr-1"></i>PADI Pro Guides</div>
      </div>
    </header>

    <!-- Filters -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
      <select id="filterCategory" class="w-full border rounded-lg px-3 py-2">
        <option value="">All Categories</option>
        <option value="scuba">Scuba Diving</option>
        <option value="snorkeling">Snorkeling</option>
      </select>

      <select id="filterLocation" class="w-full border rounded-lg px-3 py-2">
        <option value="">All Locations</option>
        <?php foreach($locations as $loc): ?>
          <option value="<?=htmlspecialchars($loc)?>"><?=htmlspecialchars($loc)?></option>
        <?php endforeach; ?>
      </select>

      <input id="filterQuery" class="w-full border rounded-lg px-3 py-2" placeholder="Search package title…">
    </section>

    <!-- Best pick -->
    <?php if (!empty($rows)): 
      $best = $rows[0];
      $usd = (float)$best['usd_price'];
      $disc = (float)$best['discount_pct'];
      $usd_final = $usd * (1 - $disc/100);
      $idr_final = $usd_final * $FX;
    ?>
    <section class="mb-6">
      <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="badge px-3 py-1 rounded-full text-xs uppercase tracking-wide">Best value</span>
          <div class="text-slate-800">
            <div class="font-semibold"><?=$best['title']?> — <span class="text-slate-500"><?=$best['location']?></span></div>
            <?php if($best['discount_pct']>0): ?>
              <div class="text-xs text-emerald-700 font-medium">Save <?=$best['discount_pct']?>% today</div>
            <?php endif; ?>
          </div>
        </div>
        <div class="text-right">
          <div class="text-lg font-bold text-slate-900"><?=rupiah(round($idr_final, -2))?></div>
          <div class="text-xs text-slate-500"><?=money_usd($usd_final)?></div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- List -->
    <section id="cards" class="space-y-4">
      <?php foreach($rows as $r):
        $usd = (float)$r['usd_price'];
        $disc = (float)$r['discount_pct'];
        $usd_final = $usd * (1 - $disc/100);
        $idr_final = $usd_final * $FX;
        $save_idr = ($usd - $usd_final) * $FX;
      ?>
      <article class="card bg-white rounded-xl p-4 flex gap-4 items-stretch" 
               data-category="<?=$r['category']?>" 
               data-location="<?=htmlspecialchars($r['location'])?>"
               data-title="<?=htmlspecialchars(strtolower($r['title']))?>">
        <div class="w-28 h-28 shrink-0 rounded-lg bg-cover bg-center border" 
             style="background-image:url('<?=htmlspecialchars($r['image_url'] ?: 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg')?>')"></div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            <?php if($r['category']==='scuba'): ?>
              <span class="chip px-2 py-0.5 rounded text-xs"><i class="fa-solid fa-person-swimming mr-1"></i>Scuba</span>
            <?php else: ?>
              <span class="chip px-2 py-0.5 rounded text-xs"><i class="fa-solid fa-water mr-1"></i>Snorkeling</span>
            <?php endif; ?>
            <?php if($r['level']): ?>
              <span class="text-xs text-slate-500">• <?=$r['level']?></span>
            <?php endif; ?>
            <?php if($r['dives']): ?>
              <span class="text-xs text-slate-500">• <?=$r['dives']?> dives</span>
            <?php endif; ?>
            <?php if($r['duration']): ?>
              <span class="text-xs text-slate-500">• <?=$r['duration']?></span>
            <?php endif; ?>
          </div>
          <h3 class="font-semibold text-slate-900 leading-tight"><?=$r['title']?></h3>
          <div class="text-sm text-slate-600 mb-2"><i class="fa-solid fa-location-dot mr-1 text-sky-600"></i><?=$r['location']?></div>

          <?php if($r['highlights']): 
            $bullets = array_slice(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $r['highlights']))),0,3);
          ?>
          <ul class="text-sm text-slate-700 list-disc pl-4">
            <?php foreach($bullets as $b): ?>
              <li><?=htmlspecialchars($b)?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>

        <div class="w-40 flex flex-col items-end justify-between">
          <div class="text-right">
            <?php if($disc>0): ?>
              <div class="text-xs text-emerald-700 font-semibold">Save <?=rupiah(round($save_idr,-2))?></div>
            <?php endif; ?>
            <div class="text-xl font-bold text-slate-900"><?=rupiah(round($idr_final,-2))?></div>
            <div class="text-xs text-slate-500"><?=money_usd($usd_final)?><?php if($disc>0): ?> <span class="line-through ml-1 text-slate-400"><?=money_usd($usd)?></span><?php endif; ?></div>
          </div>
          <a href="reservation.php" class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-600 text-white font-medium hover:bg-sky-700">
            Choose
            <i class="fa-solid fa-arrow-right-long"></i>
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </section>
  </div>

  <script>
    // Client-side filter
    const cards = Array.from(document.querySelectorAll('#cards article'));
    const fCat = document.getElementById('filterCategory');
    const fLoc = document.getElementById('filterLocation');
    const fQuery = document.getElementById('filterQuery');

    function applyFilter(){
      const cat = (fCat.value||'').toLowerCase();
      const loc = (fLoc.value||'').toLowerCase();
      const q   = (fQuery.value||'').trim().toLowerCase();

      cards.forEach(c=>{
        const okCat = !cat || c.dataset.category===cat;
        const okLoc = !loc || (c.dataset.location||'').toLowerCase()===loc;
        const okQ   = !q   || (c.dataset.title||'').includes(q);
        c.style.display = (okCat && okLoc && okQ) ? '' : 'none';
      });
    }
    [fCat,fLoc,fQuery].forEach(el=>el.addEventListener('input', applyFilter));
  </script>
</body>
</html>
