<?php
require_once 'template/seo_manager.php';
$page = 'conservation';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php echo generate_seo_tags($page); ?>
<link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Fonts -->
<link href="https://fonts.cdnfonts.com/css/sui-generis" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: '#3552c8',
        secondary: '#f23d4e',
        accent: '#0070d3',
        teal: '#23a0b4',
        gold: '#eebe35',
        lightblue: '#a2d2fa',
        navy: '#063c7f'
      }
    }
  }
}
</script>

<style>
body {
  font-family: 'Noto Sans', sans-serif;
  color: #1a1a1a;
  line-height: 1.7;
}
h1, h2, h3 {
  font-family: 'Sui Generis', 'Noto Sans', sans-serif;
  letter-spacing: 0.5px;
}
.fade-in { animation: fadeIn 1.2s ease-in-out; }
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(20px);}
  to {opacity: 1; transform: translateY(0);}
}
</style>
</head>

<body class="bg-gray-50 text-gray-900">

<?php include('template/nav.php')?>


<!-- add Section start -->
<section class="relative py-20 px-6 bg-white overflow-hidden fade-in">
  <div class="max-w-6xl mx-auto text-center">
    <h1 class="text-5xl md:text-6xl font-extrabold text-navy mb-6 tracking-tight">Conservation</h1>
    <p class="text-lg text-gray-700 mb-10 leading-relaxed">
      Dive into the beauty of Conservation — one of Bali’s most stunning underwater destinations. 
      Perfect for divers seeking crystal-clear water, vibrant coral reefs, and unforgettable marine life encounters.
    </p>
    <a href="https://booking.balidiving.com/pricelist/?ref=balidiving_website" target="_blank" rel="noopener"
       class="inline-block bg-navy text-white px-8 py-3 rounded-full text-lg font-semibold shadow-lg hover:bg-blue-700 transition-all">
       Book Your Dive
    </a>
  </div>
  <div class="absolute inset-0 pointer-events-none">
    <svg class="absolute bottom-0 left-0 w-full h-32 text-blue-50" preserveAspectRatio="none" viewBox="0 0 1200 120">
      <path d="M321.39,56.44C231.54,77.19,119.24,95.24,0,100V120H1200V0C1071.84,8.89,930.84,30.08,800,50.33,
      601.74,81.36,411.52,35.68,321.39,56.44Z" class="fill-current text-blue-100"></path>
    </svg>
  </div>
</section>
<!-- add Section End -->


<?php include('template/footer.php')?>
<?php include('template/chat.php')?>

</body>
</html>