<?php
// --- LOGIC BARU UNTUK MENGAMBIL JUDUL DARI URL ---

// 1. Ambil path dari URL (contoh: "/template/padang-bai")
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 2. Ambil hanya bagian terakhir dari path (contoh: "padang-bai" atau "index.php")
$pageName = basename($path);

// 3. Atur judul default jika URL adalah halaman utama
if (empty($pageName) || $pageName === '/') {
    $pageTitle = 'Home';
} else {
    // 4. Hapus ekstensi file .php untuk mendapatkan nama bersih (contoh: "index")
    $cleanName = pathinfo($pageName, PATHINFO_FILENAME);
    
    // 5. Ganti tanda hubung '-' dengan spasi
    $pageTitleWithSpaces = str_replace('-', ' ', $cleanName);
    
    // 6. Kapitalisasi setiap kata untuk judul yang rapi (contoh: "Index" atau "Padang Bai")
    $pageTitle = ucwords($pageTitleWithSpaces);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Bali Diving</title>
    <link rel="icon" href="images/bali-diving-logo.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        /* CSS from Bali Diving Assistant */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .chat-bubble {
            animation: slideIn 0.3s ease-out;
        }

    </style>
</head>
<body class="font-sans bg-gray-50">
<?php include('nav.php')?>
<header class="bg-primary text-white pt-32 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">
            <?php echo htmlspecialchars($pageTitle); ?>
        </h1>
        <p class="mt-4 text-lg text-lightblue">Detail informasi mengenai <?php echo strtolower(htmlspecialchars($pageTitle)); ?>.</p>
    </div>
</header>


<main class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-navy mb-4">Konten Halaman</h2>
            <p class="text-gray-700">
                Ini adalah area untuk konten utama. Anda bisa memasukkan teks, gambar, atau komponen lainnya di sini.
            </p>
        </div>
    </div>
</main>


<?php include('footer.php')?>

<script>
    // Script untuk menu mobile
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
</body>
</html>