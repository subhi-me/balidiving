<?php
// File yang berisi data blog
$file_path = 'database/blog.txt'; // Ganti dengan path file yang sesuai

// Fungsi untuk memuat dan menampilkan konten dari file
function loadBlogPosts($file_path) {
    if (!file_exists($file_path)) {
        echo "<p>File blog tidak ditemukan. Silakan periksa kembali lokasi file.</p>";
        return;
    }

    $file_contents = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (empty($file_contents)) {
        echo "<p>Jika artikel kosong, silakan kunjungi <a href='https://blog.balidiving.com/blog'>blog.balidiving.com</a> untuk konten lebih lanjut.</p>";
        return;
    }

    echo '<style>
            /* ... (style Anda tetap di sini) ... */
          </style>';

    echo '<div class="container">';
    echo '<h2 class="text-center">Blog, News and Articles</h2>';
    echo '<br><br>';
    echo '<div class="row">';

    foreach ($file_contents as $line) {
        list($title, $url, $image_url, $keyword, $summary) = explode(';', $line);

        echo '<div class="col-md-4 mb-4">'; 
        echo '<div class="card h-100">';
        echo '<a href="' . htmlspecialchars($url) . '" target="_blank">';
        
        // --- PERUBAHAN DI SINI ---
        // Menambahkan loading="lazy" untuk menunda pemuatan gambar
        echo '<img src="' . htmlspecialchars($image_url) . '" class="card-img-top" alt="' . htmlspecialchars($keyword) . '" loading="lazy" width="350" height="200">';
        
        echo '</a>';
        echo '<div class="card-body">';
        echo '<h5 class="card-title"><a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($title) . '</a></h5>';
        echo '<h3>' . htmlspecialchars($keyword) . '</h3>';
        echo '<p class="card-text">' . htmlspecialchars($summary) . '</p>';

        echo '<a href="https://api.whatsapp.com/send?text=' . urlencode($title . ' ' . $url) . '" class="btn btn-dark btn-sm mt-3" target="_blank">';
        echo '<i class="fab fa-whatsapp"> </i> &nbsp;Share</a>';

        echo '<a href="' . htmlspecialchars($url) . '" class="btn btn-white btn-sm mt-3 ml-2" target="_blank">';
        echo 'Read &rarr;</a>';

        echo '</div>'; // End of card body
        echo '</div>'; // End of card
        echo '</div>'; // End of col-md-4
    }

    echo '</div>'; // End of row
    echo '</div>'; // End of container
}

// Memanggil fungsi untuk menampilkan blog posts
loadBlogPosts($file_path);
?>