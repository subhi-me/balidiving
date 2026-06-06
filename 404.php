
    <?php include('template/style.php')?>
    <?php include('template/pixel.php')?>
    <?php include('template/nav.php')?>
    <section class="relative flex items-center justify-center h-screen bg-black text-white overflow-hidden">
  <!-- Layer angka besar -->
  <div class="absolute inset-0 flex items-center justify-center">
    <h1 class="text-[30vw] font-extrabold text-white/10 select-none leading-none">404</h1>
  </div>

  <!-- Layer efek cahaya -->
  <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black opacity-60"></div>

  <!-- Layer utama teks -->
  <div class="relative text-center z-10">
    <h2 class="text-5xl md:text-6xl font-bold tracking-tight mb-4">Page Not Found</h2>
    <p class="text-gray-300 text-lg mb-8">The page you’re looking for seems to have dived too deep.</p>
    <a href="/" class="inline-block px-6 py-3 bg-white text-black font-semibold rounded-full hover:bg-gray-200 transition">
      Back to Home
    </a>
  </div>

  <!-- Layer bubble efek animasi -->
  <div class="absolute bottom-0 w-full h-32 overflow-hidden">
    <div class="absolute bottom-0 w-full h-full bg-gradient-to-t from-blue-900/40 to-transparent animate-pulse"></div>
  </div>
</section>

    <?php include('template/floor-ani.php') ?>
    <?php include('template/footer.php')?>
    <?php include('template/chat.php') ?>
