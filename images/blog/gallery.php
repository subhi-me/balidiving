<?php
// generate_gallery_balidiving_share.php
// SEO-optimized Image Gallery with Share Buttons + Persuasive Text
// for https://www.balidiving.com/gallery

$outputFile = __DIR__ . '/index.php';
$ignoreList = [basename(__FILE__), basename($outputFile)];
$extensions = ['jpg','jpeg','png','gif','webp'];

$files = scandir(__DIR__);
$images = [];
foreach ($files as $f) {
  if (in_array($f, $ignoreList) || $f[0]==='.') continue;
  $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
  if (in_array($ext,$extensions) && is_file(__DIR__.'/'.$f)) $images[]=$f;
}
sort($images);

$domain = "https://www.balidiving.com/gallery";
$ogImage = "https://www.balidiving.com/images/og-gallery.jpg";

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bali Diving Image Gallery | Dive into Paradise</title>
<meta name="description" content="Join Bali Diving and experience the magic beneath Bali’s crystal-clear waters. Explore our gallery of stunning dive photography from Nusa Penida, Tulamben, and beyond.">
<meta name="keywords" content="Bali Diving, Scuba Diving Bali, Bali Dive Center, Nusa Penida Diving, Tulamben Wreck, Coral Reef Bali, Dive in Bali, Bali Diving Booking, balidiving.com">
<link rel="canonical" href="$domain">

<meta property="og:title" content="Bali Diving Image Gallery">
<meta property="og:description" content="Dive into paradise with Bali Diving — Bali’s premier PADI Dive Center. Discover breathtaking underwater moments and book your dive today!">
<meta property="og:type" content="website">
<meta property="og:url" content="$domain">
<meta property="og:image" content="$ogImage">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ImageGallery",
  "name": "Bali Diving Image Gallery",
  "url": "$domain",
  "description": "A curated collection of underwater beauty captured by Bali Diving.",
  "publisher": {
    "@type": "Organization",
    "name": "Bali Diving",
    "url": "https://www.balidiving.com"
  }
}
</script>

<style>
*{box-sizing:border-box;}
body{
  font-family:'Poppins','Inter',sans-serif;
  background:linear-gradient(180deg,#f9fafb 0%,#f1f5f9 100%);
  color:#1e293b;
  margin:0;display:flex;flex-direction:column;align-items:center;
}
header{text-align:center;padding:60px 20px 20px;}
h1{font-size:2.4rem;color:#0369a1;margin:0;}
h2{color:#475569;font-weight:400;margin-top:5px;font-size:1rem;}
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
  gap:22px;
  width:100%;max-width:1100px;
  padding:40px 25px 100px;
}
.card{
  position:relative;
  border-radius:16px;
  overflow:hidden;
  background:#fff;
  box-shadow:0 4px 14px rgba(0,0,0,0.08);
  cursor:pointer;
  transition:transform .3s ease,box-shadow .3s ease;
}
.card:hover{transform:translateY(-6px);box-shadow:0 8px 26px rgba(0,0,0,0.12);}
.card img{
  width:100%;height:220px;object-fit:cover;display:block;border-radius:16px 16px 0 0;
}
.overlay{
  position:absolute;top:0;left:0;width:100%;height:100%;
  background:rgba(255,255,255,0.85);
  display:flex;flex-direction:column;justify-content:center;align-items:center;
  opacity:0;transition:opacity .3s ease;
}
.card:hover .overlay{opacity:1;}
.overlay button{
  margin:5px;
  padding:8px 16px;
  border:none;border-radius:8px;
  background:#0284c7;color:#fff;
  font-weight:600;
  cursor:pointer;
  transition:transform .2s,background .2s;
}
.overlay button:hover{background:#0369a1;transform:scale(1.05);}
.modal{
  display:none;position:fixed;z-index:100;top:0;left:0;
  width:100%;height:100%;background:rgba(0,0,0,0.8);
  backdrop-filter:blur(6px);justify-content:center;align-items:center;
}
.modal img{
  max-width:90%;max-height:85vh;border-radius:10px;box-shadow:0 0 25px rgba(0,0,0,0.4);
}
.close{
  position:absolute;top:30px;right:40px;
  color:#fff;font-size:36px;font-weight:bold;cursor:pointer;
}
.close:hover{color:#38bdf8;}
footer{text-align:center;font-size:13px;color:#64748b;padding:20px;}
</style>
</head>
<body>
<header>
  <h1>Bali Diving Image Gallery</h1>
  <h2>Experience Bali’s underwater paradise — Join, Dive & Discover with <a href="https://www.balidiving.com" style="color:#0284c7;text-decoration:none;">BaliDiving.com</a></h2>
</header>
<div class="grid">
HTML;

foreach ($images as $img) {
  $src = htmlspecialchars($img);
  $alt = htmlspecialchars(pathinfo($img, PATHINFO_FILENAME));
  $shareText = rawurlencode("🌊 Dive into Paradise with Bali Diving! 🐠\nJoin us for an unforgettable underwater experience in Bali’s most breathtaking dive sites — Nusa Penida, Tulamben, Amed & more!\nBook your dive now at https://www.balidiving.com 💦");
  $shareUrl = rawurlencode("https://www.balidiving.com/gallery/$src");
  $html .= <<<CARD
  <div class="card" onclick="openModal('$src')">
    <img src="$src" alt="$alt" loading="lazy">
    <div class="overlay" onclick="event.stopPropagation()">
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
</div>

<footer>© 2025 BaliDiving.com — Dive into Paradise</footer>

<script>
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
// Share functions with persuasive message
function shareFB(url,text){
  window.open('https://www.facebook.com/sharer/sharer.php?u='+url+'&quote='+text,'_blank');
}
function shareTW(url,text){
  window.open('https://twitter.com/intent/tweet?url='+url+'&text='+text,'_blank');
}
function shareWA(url,text){
  window.open('https://api.whatsapp.com/send?text='+text+' '+url,'_blank');
}
function shareTG(url,text){
  window.open('https://t.me/share/url?url='+url+'&text='+text,'_blank');
}
</script>
</body>
</html>
HTML;

file_put_contents($outputFile,$html);
echo "✅ Bali Diving Image Gallery with Social Share Text created successfully (index.php)\n";
?>
