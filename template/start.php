<?php
ob_start(); // aktifkan buffer
require '../template/seo_manager.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_seo_tags('home'); ?>
    <link rel="icon" href="../bali-diving-logo.svg" type="image/svg+xml">
    <?php
    // Ini dia bagian ajaibnya!
    // Panggil fungsi untuk mencetak semua tag SEO yang relevan untuk halaman ini.
    
    ?>
    <link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">
    <meta name="description" content="Experience world-class scuba diving in Bali with Bali Diving. Explore vibrant coral reefs, encounter manta rays, and discover underwater wonders. Book your diving adventure today!">
<meta name="keywords" content="Bali diving, scuba diving Bali, dive sites Bali, underwater adventure, coral reefs, manta rays, diving tours">
    

    <?php include('../template/style.php')?>
    <?php include('../template/pixel.php')?>
</head>
<body class="font-sans">
    <?php include('../template/nav.php')?>