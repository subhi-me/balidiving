<?php
/**
 * Simple PHP Scanner — TikTok Style Cards 3 per row
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
    .ratio-9-16 {position:relative;width:100%;padding-bottom:177.77%;overflow:hidden;border-radius:1rem;}
    .ratio-9-16 img {position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
    .center-name {position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
      font-weight:600;font-size:1.2rem;color:white;text-shadow:0 2px 10px rgba(0,0,0,0.8);
      background:rgba(0,0,0,0.25);}
  </style>

<section class="bg-slate-100 min-h-screen py-10">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <?php foreach($files as $f): 
        $name = cleanName(basename($f));
        $thumb = pickThumb($thumbnails, $f);
        $url = basename($f);
      ?>
      <a href="<?php echo $url; ?>" class="block bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden">
        <div class="ratio-9-16">
          <img src="<?php echo $thumb; ?>" alt="">
          <div class="center-name"><?php echo htmlspecialchars($name); ?></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>