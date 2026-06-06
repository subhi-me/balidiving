<?php
// php_page_generator_form.php — Professional Edition (Sui Generis + Noto Sans)
$message = '';

function formatTitle($filename) {
    return ucwords(str_replace(['-', '_'], ' ', $filename));
}

function generateTemplate($filename) {
    $title = formatTitle($filename);
    return <<<PHP
<?php
require_once 'template/seo_manager.php';
\$page = \$_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$title} | Bali Diving</title>
<?php echo generate_seo_tags(\$page); ?>
<link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">
<meta name="description" content="Discover world-class scuba diving at {$title} in Bali. Explore vibrant reefs, meet marine life, and experience unforgettable underwater adventures with Bali Diving.">
<meta name="keywords" content="{$title}, Bali diving, scuba diving Bali, dive sites Bali, underwater adventure, coral reefs, manta rays, diving tours">

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
    <h1 class="text-5xl md:text-6xl font-extrabold text-navy mb-6 tracking-tight">{$title}</h1>
    <p class="text-lg text-gray-700 mb-10 leading-relaxed">
      Dive into the beauty of {$title} — one of Bali’s most stunning underwater destinations. 
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
PHP;
}

// ===== FORM PROCESS =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = trim($_POST['filename'] ?? '');
    $action = $_POST['action'] ?? '';
    if ($filename === '') {
        $message = "❌ Nama file tidak boleh kosong.";
    } else {
        $baseFile = __DIR__ . "/{$filename}.php";

        if (file_exists($baseFile) && $action === '') {
            $message = "EXISTS";
        } else {
            if ($action === 'new_version') {
                $version = 2;
                while (file_exists(__DIR__ . "/{$filename}-v{$version}.php")) {
                    $version++;
                }
                $targetFile = __DIR__ . "/{$filename}-v{$version}.php";
            } else {
                $targetFile = $baseFile;
            }

            $template = generateTemplate($filename);
            file_put_contents($targetFile, $template);
            $message = "✅ File <strong>" . basename($targetFile) . "</strong> berhasil dibuat (Heading: Sui Generis, Body: Noto Sans).";
            $createdFile = basename($targetFile);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Bali Diving - PHP File Generator</title>
<script src="https://cdn.tailwindcss.com"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-700 to-blue-500 min-h-screen flex flex-col items-center justify-center text-white font-sans">

<div class="bg-white text-gray-900 rounded-3xl shadow-2xl w-full max-w-md p-8 space-y-6 transform transition-all duration-500 hover:scale-[1.02]">
  <h1 class="text-2xl font-bold text-center text-navy" style="font-family:'Sui Generis',sans-serif;">Bali Diving File Generator</h1>
  <p class="text-center text-gray-500 text-sm">Masukkan nama file baru untuk membuat halaman PHP profesional</p>

  <form method="POST" class="space-y-4" id="generatorForm">
    <input type="text" name="filename" placeholder="contoh: manta-point" 
           value="<?php echo htmlspecialchars($_POST['filename'] ?? ''); ?>" 
           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    <input type="hidden" name="action" id="actionInput">
    <button type="submit" class="w-full bg-navy text-white py-3 rounded-lg font-semibold hover:bg-blue-600 transition-colors">Create New File</button>
  </form>

  <?php if ($message === "EXISTS"): ?>
      <div class="mt-6 text-center">
          <p class="text-yellow-600 font-medium">⚠️ File sudah ada. Apa yang ingin kamu lakukan?</p>
          <div class="flex gap-3 mt-4 justify-center">
              <button onclick="submitAction('overwrite')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">Timpa File</button>
              <button onclick="submitAction('new_version')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">Buat Versi Baru</button>
          </div>
      </div>
  <?php elseif ($message !== ''): ?>
      <div class="mt-4 text-center text-sm <?php echo strpos($message, '✅') !== false ? 'text-green-600' : 'text-red-600'; ?>">
          <?php echo $message; ?>
      </div>
      <?php if (!empty($createdFile)): ?>
          <div class="mt-4 text-center">
              <a href="<?php echo $createdFile; ?>" target="_blank" 
                 class="inline-block bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-md">
                 Open File
              </a>
          </div>
      <?php endif; ?>
  <?php endif; ?>

  <div class="border-t border-gray-200 pt-4 text-center text-xs text-gray-400">
      &copy; <span id="year"></span> Bali Diving Developer Tools
  </div>
</div>

<script>
function submitAction(action) {
  document.getElementById('actionInput').value = action;
  document.getElementById('generatorForm').submit();
}
document.getElementById('year').textContent = new Date().getFullYear();
</script>
</body>
</html>
