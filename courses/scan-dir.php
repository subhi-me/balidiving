<?php
// gallery_index_instagram.php
// Versi: 2.1 - Real folder scan + Esthetic Hero Title (Sui Generis)

$rootDir = __DIR__; // Scan current directory
$webBaseUrl = '';   // Kosongkan jika file ini diakses langsung di folder target

$thumbnails = [
    "https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg",
    "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
];

// --- Ambil daftar subfolder ---
function getSubfolders($dir) {
    $folders = [];
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) $folders[] = $item;
    }
    sort($folders, SORT_NATURAL | SORT_FLAG_CASE);
    return $folders;
}

// --- Hitung jumlah file dalam folder ---
function countFiles($dir) {
    $count = 0;
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        if (is_file($dir . DIRECTORY_SEPARATOR . $f)) $count++;
    }
    return $count;
}

$folders = getSubfolders($rootDir);
?>

<link href="https://fonts.googleapis.com/css2?family=Sui+Generis&display=swap" rel="stylesheet">

<style>
body {
    background: #fafafa;
    font-family: 'Sui generis', sans-serif;
    margin: 0;
    padding: 0;
}

/* --- Hero Title Section --- */
.title-section {
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  flex-direction: column;
  min-height: 50vh;
  background: radial-gradient(circle at center, #0f2027 0%, #203a43 50%, #2c5364 100%);
  color: #fff;
  position: relative;
  overflow: hidden;
  padding: 60px 20px;
}

.title-section::before {
  content: "";
  position: absolute;
  inset: 0;
  background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
  opacity: 0.3;
  filter: blur(5px);
  z-index: 0;
}

.title-wrapper {
  position: relative;
  z-index: 1;
  max-width: 900px;
  animation: fadeIn 2s ease-out forwards;
}

.title-section h1,
.title-section h2 {
  font-family: 'Sui Generis', sans-serif;
  letter-spacing: 1.5px;
  margin: 0;
}

.title-section h1 {
  font-size: 3rem;
  font-weight: 700;
  text-transform: uppercase;
  background: linear-gradient(90deg, #00b4db, #0083b0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 15px;
  text-shadow: 0 4px 25px rgba(0, 179, 255, 0.3);
}

.title-section h2 {
  font-size: 1.4rem;
  font-weight: 300;
  color: rgba(255,255,255,0.85);
  letter-spacing: 2px;
  text-shadow: 0 2px 12px rgba(0,0,0,0.4);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(25px); }
  to { opacity: 1; transform: translateY(0); }
}

/* --- Grid Gallery --- */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    padding: 50px 30px 80px;
    max-width: 1200px;
    margin: auto;
}

.card {
    position: relative;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.card:hover img {
    transform: scale(1.05);
}

.card-info {
    padding: 15px;
    text-align: center;
}

.card-info h2 {
    font-size: 1rem;
    color: #333;
    margin: 10px 0 5px;
    text-transform: capitalize;
}

.card-info p {
    font-size: 0.9rem;
    color: #777;
}

.badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.6);
    color: #fff;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
}
</style>


<!-- ===================== -->
<!--  HERO TITLE SECTION   -->
<!-- ===================== -->
<section class="title-section">
  <div class="title-wrapper">
    <h1>Learn Diving</h1>
    <h2>Learn diving from zero to hero</h2>
  </div>
</section>

<!-- Spacer -->
<div style="height:40px;"></div>

<!-- ===================== -->
<!--  GALLERY SECTION      -->
<!-- ===================== -->
<?php if (empty($folders)): ?>
    <p style="text-align:center;color:#777;">Tidak ada folder ditemukan.</p>
<?php else: ?>
<div class="gallery-grid">
    <?php foreach ($folders as $folder): 
        $thumb = $thumbnails[array_rand($thumbnails)];
        $count = countFiles($rootDir . DIRECTORY_SEPARATOR . $folder);
    ?>
    <a href="<?= htmlspecialchars($folder) ?>/" class="card">
        <span class="badge"><?= $count ?> courses</span>
        <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($folder) ?>">
        <div class="card-info">
            <h2><?= htmlspecialchars($folder) ?></h2>
            <p>Select Course →</p>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
