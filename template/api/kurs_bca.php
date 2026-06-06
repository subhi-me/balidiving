<?php
// template/api/kurs_bca.php
declare(strict_types=1);

// jangan echo apa pun di file ini!
// cukup set $USD_TO_IDR

if (!isset($USD_TO_IDR) || !is_numeric($USD_TO_IDR)) {
  $USD_TO_IDR = 16780;
}

// kalau kamu punya logic fetch kurs BCA, taruh di sini,
// tapi hasil akhirnya cukup:
// $USD_TO_IDR = 16780; // contoh

$url = 'https://www.bca.co.id/id/informasi/kurs';

// --- 1. Ambil Konten HTML (Menggunakan cURL) ---
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$html = curl_exec($ch);

if (curl_errno($ch)) {
    die("Gagal pengambilan cURL. Error: " . curl_error($ch));
}
curl_close($ch);

// --- DIAGNOSTIK: Cek apakah konten berhasil diambil ---
if (strlen($html) < 10000) {
    die("Gagal: Konten yang diambil terlalu kecil. Kemungkinan besar IP Anda diblokir atau koneksi ditolak.");
}
// ---------------------------------------------------

// --- 2. Inisialisasi DOM dan XPath ---
$dom = new DOMDocument();
// Konversi encoding untuk meminimalisir error parsing
$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
@$dom->loadHTML($html, LIBXML_NOWARNING|LIBXML_NOERROR);
$xpath = new DOMXPath($dom);

// --- 3. Tentukan XPath Alternatif (Pencarian Lebih Fleksibel) ---
// Mencari elemen 'td' dengan teks 'USD', lalu ambil kolom ke-4 setelahnya (TT Counter Jual)
$query = "//td[normalize-space()='USD']/following-sibling::td[4]";

// --- 4. Eksekusi Query dan Ekstraksi ---
$nodes = $xpath->query($query);

if ($nodes->length > 0) {
    $harga_mentah = $nodes->item(0)->textContent;
    
    // --- 5. Bersihkan dan Format Data ---
    $harga_bersih = str_replace('.', '', $harga_mentah); // Hapus titik ribuan
    $harga_bersih = preg_replace('/,.*/', '', $harga_bersih); // Hapus desimal (misal: ,00)
    
    // --- 6. Tampilkan Hasil ---
    echo $harga_bersih; 
    
} else {
    echo "Data kurs USD TT Counter Jual tidak ditemukan (XPath gagal).";
}
?>