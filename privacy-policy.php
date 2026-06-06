<?php include('01-start.php')?>
<div style="height:100px;"></div>

<style>
/* ===== PDF-like document style ===== */
.pdf-page{
  background:#fff; max-width:820px; margin:0 auto 80px; padding:60px;
  box-shadow:0 0 0 1px #e5e5e5, 0 20px 40px rgba(0,0,0,.08);
  font-family:"Times New Roman", Georgia, serif; color:#111;
}
.pdf-page h1{font-size:28px;margin-bottom:6px;}
.pdf-page h4{font-size:20px;margin-top:32px;margin-bottom:10px;}
.pdf-page p{font-size:15px;line-height:1.8;margin:8px 0;text-align:justify;}
.pdf-page ul{margin:10px 0 10px 22px;}
.pdf-page li{font-size:15px;line-height:1.7;margin-bottom:6px;}
.pdf-meta{font-size:13px;color:#555;margin-bottom:30px;}

.lang-switch{max-width:820px;margin:0 auto 10px;text-align:right;font-family:Arial,sans-serif;}
.lang-btn{border:1px solid #ddd;background:#fff;padding:6px 10px;border-radius:8px;font-size:13px;cursor:pointer;margin-left:6px}
.lang-btn.active{border-color:#3552c8;box-shadow:0 0 0 2px rgba(53,82,200,.15)}

@media print{
  .lang-switch{display:none;}
  body{background:#fff;}
  .pdf-page{box-shadow:none;margin:0;padding:40px;}
}
</style>

<div class="lang-switch">
  <button class="lang-btn" id="btn-en" onclick="setLang('en')">🇬🇧 EN</button>
  <button class="lang-btn" id="btn-id" onclick="setLang('id')">🇮🇩 ID</button>
  <button class="lang-btn" id="btn-de" onclick="setLang('de')">🇩🇪 DE</button>
  <button class="lang-btn" id="btn-fr" onclick="setLang('fr')">🇫🇷 FR</button>
  <button class="lang-btn" id="btn-jp" onclick="setLang('jp')">🇯🇵 JP</button>
</div>

<!-- =========================
     ENGLISH (GB) – FULL
========================== -->
<section class="pdf-page lang-en">
<h1>Privacy Policy</h1>
<div class="pdf-meta">Effective Date: January 2025</div>

<h4>1. Introduction</h4>
<p>This Privacy Policy explains how <strong>Bali Diving</strong> collects, uses, stores, and protects personal information obtained through our website, booking systems, and diving-related services.</p>
<p>By accessing or using our services, you agree to the practices described in this Privacy Policy.</p>

<h4>2. Information We Collect</h4>
<ul>
  <li>Personal identification data (name, email address, phone number).</li>
  <li>Booking and transaction information.</li>
  <li>Technical data (IP address, browser type, device information, logs).</li>
  <li>Payment-related metadata processed securely by third-party providers.</li>
</ul>

<h4>3. How We Use Your Information</h4>
<ul>
  <li>Process bookings, rentals, and purchases.</li>
  <li>Communicate service-related information.</li>
  <li>Improve website performance and safety.</li>
  <li>Comply with legal and regulatory obligations.</li>
</ul>

<h4>4. Cookies & Tracking</h4>
<p>Essential cookies are used for site functionality. Non-essential cookies (analytics/marketing) are activated only after explicit consent via our cookie consent system.</p>

<h4>5. Data Sharing</h4>
<p>We do not sell personal data. Data may be shared with trusted partners (payment processors, booking partners, hosting providers) only as necessary and legally permitted.</p>

<h4>6. Data Retention</h4>
<p>Personal data is retained only for as long as required for the stated purposes or as required by law.</p>

<h4>7. Your Rights</h4>
<ul>
  <li>Access, correct, or delete your personal data.</li>
  <li>Withdraw consent for non-essential processing.</li>
  <li>Object to or restrict certain processing activities.</li>
</ul>

<h4>8. Data Security</h4>
<p>We implement reasonable technical and organizational measures to protect personal data. Absolute security cannot be guaranteed.</p>

<h4>9. International Users</h4>
<p>Our operations are based in Indonesia. By using our services, you consent to international data processing.</p>

<h4>10. Changes</h4>
<p>We may update this Privacy Policy at any time. Changes take effect upon publication.</p>

<h4>11. Contact</h4>
<p><strong>Bali Diving</strong><br>Email: customer.service@balidiving.com<br>Sanur, Bali – Indonesia</p>
</section>

<!-- =========================
     BAHASA INDONESIA – FULL
========================== -->
<section class="pdf-page lang-id" style="display:none;">
<h1>Kebijakan Privasi</h1>
<div class="pdf-meta">Berlaku: Januari 2025</div>

<h4>1. Pendahuluan</h4>
<p>Kebijakan Privasi ini menjelaskan bagaimana <strong>Bali Diving</strong> mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi melalui situs web, sistem booking, dan layanan wisata selam.</p>
<p>Dengan menggunakan layanan kami, Anda menyetujui praktik yang dijelaskan dalam Kebijakan Privasi ini.</p>

<h4>2. Data yang Kami Kumpulkan</h4>
<ul>
  <li>Data identitas (nama, email, nomor telepon).</li>
  <li>Informasi booking dan transaksi.</li>
  <li>Data teknis (alamat IP, browser, perangkat).</li>
  <li>Metadata pembayaran yang diproses oleh pihak ketiga secara aman.</li>
</ul>

<h4>3. Penggunaan Data</h4>
<ul>
  <li>Memproses booking, sewa, dan pembelian.</li>
  <li>Komunikasi terkait layanan.</li>
  <li>Peningkatan performa dan keselamatan.</li>
  <li>Kepatuhan hukum dan peraturan.</li>
</ul>

<h4>4. Cookies & Pelacakan</h4>
<p>Cookies esensial digunakan untuk fungsi situs. Cookies non-esensial hanya aktif setelah persetujuan melalui sistem cookie consent.</p>

<h4>5. Pembagian Data</h4>
<p>Kami tidak menjual data pribadi. Data hanya dibagikan kepada mitra tepercaya sesuai kebutuhan operasional dan hukum.</p>

<h4>6. Retensi Data</h4>
<p>Data pribadi disimpan hanya selama diperlukan atau sesuai ketentuan hukum.</p>

<h4>7. Hak Anda</h4>
<ul>
  <li>Mengakses, memperbaiki, atau menghapus data pribadi.</li>
  <li>Mencabut persetujuan pemrosesan non-esensial.</li>
  <li>Mengajukan keberatan atau pembatasan pemrosesan.</li>
</ul>

<h4>8. Keamanan Data</h4>
<p>Kami menerapkan langkah teknis dan organisasi yang wajar untuk melindungi data. Keamanan absolut tidak dapat dijamin.</p>

<h4>9. Pengguna Internasional</h4>
<p>Operasional kami berada di Indonesia. Dengan menggunakan layanan, Anda menyetujui pemrosesan data lintas negara.</p>

<h4>10. Perubahan</h4>
<p>Kebijakan Privasi ini dapat diperbarui sewaktu-waktu dan berlaku sejak dipublikasikan.</p>

<h4>11. Kontak</h4>
<p><strong>Bali Diving</strong><br>Email: customer.service@balidiving.com<br>Sanur, Bali – Indonesia</p>
</section>

<!-- =========================
     DE / FR / JP – SUMMARY
========================== -->
<section class="pdf-page lang-de" style="display:none;">
<h1>Datenschutzerklärung</h1>
<p>Diese Seite fasst zusammen, wie Bali Diving personenbezogene Daten verarbeitet. Für vollständige rechtliche Details gilt die englische Version (GB).</p>
</section>

<section class="pdf-page lang-fr" style="display:none;">
<h1>Politique de Confidentialité</h1>
<p>Cette page résume la gestion des données personnelles par Bali Diving. La version anglaise (GB) prévaut juridiquement.</p>
</section>

<section class="pdf-page lang-jp" style="display:none;">
<h1>プライバシーポリシー</h1>
<p>本ページは概要です。法的には英語版（GB）が優先されます。</p>
</section>

<script>
function setLang(lang){
  document.querySelectorAll('.pdf-page').forEach(s=>s.style.display='none');
  document.querySelector('.lang-'+lang).style.display='block';
  document.querySelectorAll('.lang-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('btn-'+lang).classList.add('active');
  localStorage.setItem('site_lang', lang);
}
// default = GB English
(function(){ setLang(localStorage.getItem('site_lang') || 'en'); })();
</script>
<?php include('template/consent.php');?>
<?php include('03-end.php')?>
