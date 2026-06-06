<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar URL Gambar</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }
        p {
            color: #666;
            font-size: 16px;
        }
        textarea {
            width: 100%;
            height: 400px;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Daftar URL Gambar</h1>
        <p>Berikut adalah daftar URL lengkap untuk semua gambar di folder ini. Anda bisa memilih semua (Ctrl+A atau Cmd+A) dan menyalinnya (Ctrl+C atau Cmd+C).</p>

        <textarea readonly>
<?php
// 1. Tentukan ekstensi file gambar yang diizinkan
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

// 2. Bangun URL dasar (base URL)
// Protokol 'https://' sesuai permintaan
$protocol = 'https://';
// Nama host (domain atau IP) dari server
$host = $_SERVER['HTTP_HOST'];
// Path direktori tempat script ini dijalankan
$directory = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
// Gabungkan untuk membuat URL dasar
$base_url = $protocol . $host . $directory . '/';


// 3. Scan semua file di dalam direktori saat ini ('.')
$files = scandir('.');

$image_urls = [];

// 4. Lakukan loop untuk setiap file yang ditemukan
foreach ($files as $file) {
    // Dapatkan informasi path dari file
    $path_info = pathinfo($file);

    // Periksa apakah file memiliki ekstensi dan ekstensi tersebut ada di dalam daftar yang diizinkan
    if (isset($path_info['extension']) && in_array(strtolower($path_info['extension']), $allowed_extensions)) {
        // Tambahkan URL lengkap ke dalam array
        $image_urls[] = $base_url . rawurlencode($file);
    }
}

// 5. Cetak hasilnya, satu URL per baris.
if (!empty($image_urls)) {
    echo implode("\n", $image_urls);
} else {
    echo "Tidak ada gambar yang ditemukan di folder ini.";
}
?>
        </textarea>
    </div>

</body>
</html>