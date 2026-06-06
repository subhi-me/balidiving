<?php
// ===============================================================
// 🌊 BALI DIVING GALLERY GENERATOR – SEO Optimized + JSON-LD
// Author: Subhi Darajat / BaliDiving.com
// ===============================================================

$outputFile = __DIR__ . '/index.php';
$ignoreList = [basename(__FILE__), basename($outputFile)];
$extensions = ['jpg','jpeg','png','gif','webp'];

// --- Scan folder for images ---
$images = [];
foreach (scandir(__DIR__) as $f) {
  if ($f[0]==='.' || in_array($f, $ignoreList)) continue;
  $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
  if (in_array($ext, $extensions)) $images[] = $f;
}
sort($images);

// --- Domain & Meta Info ---
$domain = "https://www.balidiving.com/Images/gallery";
$ogImage = "https://www.balidiving.com/images/og-gallery.jpg";
$title = "Bali Diving Gallery – Scuba Diving & Underwater Paradise in Bali";
$desc = "Discover Bali’s stunning underwater world with Bali Diving. Explore dive sites like Nusa Penida, Tulamben, and Amed. See coral reefs, mantas, turtles, and book your diving adventure today!";
$keywords = "Bali Diving, Scuba Diving Bali, Nusa Penida, Tulamben, Amed, Manta Ray, Snorkeling, Water Sport Bali, Dive Center, Coral Reef, Underwater Photography, Diving Price List, balidiving.com";

// --- Build JSON-LD (ImageGallery + TouristAttraction) ---
$jsonImages = [];
foreach ($images as $img) {
  $jsonImages[] = "    \"$domain/$img\"";
}
$jsonImagesStr = implode(",\n", $jsonImages);

$jsonLD = <<<JSON
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": ["ImageGallery", "TouristAttraction"],
  "name": "Bali Diving Gallery",
  "description": "$desc",
  "url": "$domain",
  "image": [
$jsonImagesStr
  ],
  "touristType": ["Scuba Divers", "Snorkelers", "Underwater Photographers"],
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": -8.6723,
    "longitude": 115.1686
  },
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Bali",
    "addressCountry": "ID"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Bali Diving",
    "url": "https://www.balidiving.com",
    "logo": {
      "@type": "ImageObject",
      "url": "https://www.balidiving.com/images/logo.png"
    }
  }
}
</script>
JSON;

// --- Build HTML ---
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>$title</title>
<meta name="description" content="$desc">
<meta name="keywords" content="$keywords">
<link rel="canonical" href="$domain">
<meta property="og:title" content="$title">
<meta property="og:description" content="$desc">
<meta property="og:type" content="website">
<meta property="og:url" content="$domain">
<meta property="og:image" content="$ogImage">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="$title">
<meta name="twitter:description" content="$desc">
<meta name="twitter:image" content="$ogImage">
<meta name="author" content="Bali Diving">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="icon" href="https://www.balidiving.com/favicon.ico">
$htmlMinifier
<style>
*{box-sizing:border-box;}
body{
  font-family:'Poppins','Inter',sans-serif;
  background:#f8fafc;
  color:#1e293b;
  margin:0;display:flex;flex-direction:column;align-items:center;
}
header{text-align:center;padding:60px 20px 20px;}
h1{font-size:2.4rem;color:#0369a1;margin:0;}
h2{color:#475569;font-weight:400;margin-top:5px;font-size:1rem;}
.masonry{
  column-count:3;
  column-gap:1.2rem;
  max-width:1100px;
  padding:30px 25px 100px;
}
@media(max-width:900px){.masonry{column-count:2;}}
@media(max-width:600px){.masonry{column-count:1;}}
.item{
  position:relative;
  margin-bottom:1.2rem;
  border-radius:14px;
  overflow:hidden;
  cursor:pointer;
  box-shadow:0 4px 14px rgba(0,0,0,0.08);
  transition:transform .3s ease,box-shadow .3s ease;
}
.item img{
  width:100%;border-radius:14px;display:block;transition:transform .4s ease,filter .4s ease;
}
.item:hover img{transform:scale(1.07);filter:brightness(1.1);}
.share-menu{
  display:none;
  position:absolute;
  top:10px;left:50%;
  transform:translateX(-50%);
  background:rgba(255,255,255,0.95);
  border-radius:10px;
  box-shadow:0 4px 10px rgba(0,0,0,0.15);
  padding:10px 12px;
  animation:fadeIn .3s ease;
}
.share-menu button{
  margin:4px;
  padding:6px 10px;
  border:none;
  border-radius:8px;
  font-size:13px;
  font-weight:600;
  cursor:pointer;
  background:#0284c7;
  color:#fff;
}
.share-menu button:hover{background:#0369a1;}
.modal{
  display:none;position:fixed;z-index:100;top:0;left:0;
  width:100%;height:100%;
  background:rgba(0,0,0,0.85);
  backdrop-filter:blur(6px);
  justify-content:center;align-items:center;
  flex-direction:column;
}
.modal img{
  max-width:90%;max-height:80vh;border-radius:10px;box-shadow:0 0 25px rgba(0,0,0,0.4);
  margin-bottom:20px;
}
.close{
  position:absolute;top:30px;right:40px;
  color:#fff;font-size:36px;font-weight:bold;cursor:pointer;
}
.close:hover{color:#38bdf8;}
.book-btn{
  background:linear-gradient(90deg,#0284c7,#38bdf8);
  color:#fff;
  padding:12px 24px;
  border:none;
  border-radius:8px;
  font-size:16px;
  font-weight:600;
  cursor:pointer;
  transition:transform .2s,box-shadow .2s;
}
.book-btn:hover{
  transform:scale(1.05);
  box-shadow:0 0 20px rgba(56,189,248,0.4);
}
footer{text-align:center;font-size:13px;color:#64748b;padding:20px;}
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
</style>
</head>
<body>
<header>
  <h1>Bali Diving Image Gallery</h1>
  <h2>Explore the beauty beneath — <a href="https://www.balidiving.com" style="color:#0284c7;text-decoration:none;">BaliDiving.com</a></h2>
</header>
<div class="masonry">
HTML;

// --- Build gallery items ---
foreach ($images as $img) {
  $altRaw = pathinfo($img, PATHINFO_FILENAME);
  $alt = ucwords(str_replace(['-', '_'], ' ', $altRaw));
  $shareText = rawurlencode("🌊 Dive into Paradise with Bali Diving! 🐠 Explore $alt and more underwater beauty in Bali! Check Price List at https://booking.balidiving.com/pricelist/?ref=gallery 💦");
  $shareUrl = rawurlencode("$domain/$img");
  $html .= <<<CARD
  <div class="item">
    <img src="$img" alt="Bali Diving - $alt" loading="lazy" onclick="openModal('$img')">
    <div class="share-menu" id="share-$img" onclick="event.stopPropagation()">
      <button onclick="shareFB('$shareUrl','$shareText')">Facebook</button>
      <button onclick="shareTW('$shareUrl','$shareText')">Twitter</button>
      <button onclick="shareWA('$shareUrl','$shareText')">WhatsApp</button>
      <button onclick="shareTG('$shareUrl','$shareText')">Telegram</button>
    </div>
  </div>
CARD;
}

$html .= <<<HTML
</div>

<!-- Modal -->
<div id="imageModal" class="modal" onclick="closeModal()">
  <span class="close" onclick="closeModal()">&times;</span>
  <img id="modalImg" src="" alt="Bali Diving Image Preview">
  <button class="book-btn" onclick="window.open('https://booking.balidiving.com/pricelist/?ref=gallery','_blank')">View Price List</button>
</div>

<footer>© 2025 BaliDiving.com — Scuba Diving & Water Sport in Bali</footer>

$jsonLD

<script>
// Modal
function openModal(src){
  const modal=document.getElementById('imageModal');
  const img=document.getElementById('modalImg');
  modal.style.display='flex';
  img.src=src;
}
function closeModal(){
  const modal=document.getElementById('imageModal');
  modal.style.display='none';
  document.getElementById('modalImg').src='';
}
// Toggle share
document.querySelectorAll('.item').forEach(item=>{
  item.addEventListener('click',()=>{
    const img=item.querySelector('img').getAttribute('src');
    const menu=document.getElementById('share-'+img);
    if(menu.style.display==='flex'){menu.style.display='none';return;}
    document.querySelectorAll('.share-menu').forEach(m=>m.style.display='none');
    menu.style.display='flex';
  });
});
// Share
function shareFB(url,text){window.open('https://www.facebook.com/sharer/sharer.php?u='+url+'&quote='+text,'_blank');}
function shareTW(url,text){window.open('https://twitter.com/intent/tweet?url='+url+'&text='+text,'_blank');}
function shareWA(url,text){window.open('https://api.whatsapp.com/send?text='+text+' '+url,'_blank');}
function shareTG(url,text){window.open('https://t.me/share/url?url='+url+'&text='+text,'_blank');}
</script>
</body>
</html>
HTML;

// --- Write file ---
file_put_contents($outputFile, $html);
echo "✅ SEO-Optimized Bali Diving Gallery created successfully (index.php)\n";
?>
