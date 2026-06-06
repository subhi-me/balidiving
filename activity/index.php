<?php include('../template/start.php'); ?>
<?php
/**
 * Simple PHP Scanner — TikTok Style Cards 3 per row
 * Enhanced aesthetic header (Sui Generis style)
 * by Subhi.me
 */

$rootDir = __DIR__;
$ignoreFiles = [basename(__FILE__), 'index.php'];
$thumbnails = [
  "https://www.balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg",
  "https://www.balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
];

function pickThumb($arr, $path){
  return $arr[crc32($path) % count($arr)];
}
function cleanName($name){
  $noExt = preg_replace('/\.php$/i', '', $name);
  $clean = preg_replace('/[-_()]+/', ' ', $noExt);
  return ucwords(trim($clean));
}

$files = glob($rootDir.'/*.php');
$files = array_filter($files, fn($f) => !in_array(basename($f), $ignoreFiles));
usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap');
  body { font-family: 'Noto Sans', sans-serif; background-color: #f8fafc; }

  /* Sui Generis font jika sudah di-load di Akang punya sistem */
  h1 {
    font-family: 'Sui Generis', 'Noto Sans', sans-serif;
    letter-spacing: 1px;
    background: linear-gradient(135deg, #0a2540, #3552c8 40%, #00bcd4 90%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
    font-size: clamp(2rem, 6vw, 3.8rem);
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 3rem;
    position: relative;
  }
  h1::after {
    content: '';
    display: block;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #3552c8, #00bcd4);
    margin: 1rem auto 0;
    border-radius: 2px;
  }

  .ratio-9-16 {
    position: relative;
    width: 100%;
    padding-bottom: 177.77%;
    overflow: hidden;
    border-radius: 1rem;
  }
  .ratio-9-16 img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.7s ease;
  }
  .ratio-9-16:hover img {
    transform: scale(1.05);
  }
  .center-name {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Sui Generis', 'Noto Sans', sans-serif;
    font-weight: 600;
    font-size: 1.3rem;
    color: white;
    text-shadow: 0 2px 10px rgba(0,0,0,0.8);
    background: rgba(0, 0, 0, 0.3);
    transition: background 0.4s ease;
  }
  .ratio-9-16:hover .center-name {
    background: rgba(0,0,0,0.45);
  }
</style>

<div style="height:50px;"></div>

<section class="bg-slate-100 min-h-screen py-10">
  <div class="max-w-6xl mx-auto text-center">
    <h1>Activities</h1>
    <p class="text-slate-500 max-w-2xl mx-auto mt-3 text-sm md:text-base leading-relaxed">
      Explore Underwater activities.</em>
    </p>
  </div>

  <div class="max-w-7xl mx-auto px-6 mt-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <?php foreach($files as $f): 
        $name = cleanName(basename($f));
        $thumb = pickThumb($thumbnails, $f);
        $url = basename($f);
      ?>
      <a href="<?php echo $url; ?>" class="block bg-white rounded-2xl shadow hover:shadow-2xl transition overflow-hidden">
        <div class="ratio-9-16">
          <img src="<?php echo $thumb; ?>" alt="">
          <div class="center-name"><?php echo htmlspecialchars($name); ?></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include('../template/end.php'); ?>
