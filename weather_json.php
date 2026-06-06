<?php
// =======================================================
// 5. DATA FETCH & JSON OUTPUT
// =======================================================

// --- Tambahkan 3 baris berikut: ---
ob_start(); // 1. Mulai menangkap output
// ----------------------------------

// Sertakan file logika bisnis.
require_once 'weather_service.php';

// Ambil data cuaca.
$weatherData = fetchAllDiveSiteData($GLOBALS['diveSites']);

// --- Tambahkan 1 baris berikut: ---
ob_clean(); // 2. Hapus semua output yang tertangkap
// ----------------------------------

// Set header ke JSON dan cetak data
header('Content-Type: application/json');
echo json_encode($weatherData);

// Hentikan eksekusi skrip PHP
exit;