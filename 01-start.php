<?php
// Panggil file manager SEO kita
require_once 'template/seo_manager.php';

// Tentukan pengenal untuk halaman ini.
// Memungkinkan halaman induk mendefinisikan $page sebelum include('01-start.php')
$page = $page ?? $_GET['page'] ?? 'home'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Ini dia bagian ajaibnya!
    // Panggil fungsi untuk mencetak semua tag SEO yang relevan untuk halaman ini.
    echo generate_seo_tags($page);
    ?>
    <link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">
    

    <?php include('template/style.php')?>
    <?php include('template/pixel.php')?>
</head>
<body class="font-sans">
    <?php include('template/nav.php')?>