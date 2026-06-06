<?php
$page = 'terms-of-service';
include('01-start.php');
?>
<div style="height:100px;"></div>

<style>
  /* PDF-like document style */
  .pdf-page {
    background: #fff;
    max-width: 820px;
    margin: 0 auto 80px;
    padding: 60px;
    box-shadow: 0 0 0 1px #e5e5e5, 0 20px 40px rgba(0, 0, 0, .08);
    font-family: "Times New Roman", Georgia, serif;
    color: #111;
  }

  .pdf-page h1 {
    font-size: 28px;
    margin-bottom: 6px;
  }

  .pdf-page h4 {
    font-size: 20px;
    margin-top: 32px;
    margin-bottom: 10px;
  }

  .pdf-page p {
    font-size: 15px;
    line-height: 1.8;
    margin: 8px 0;
    text-align: justify;
  }

  .pdf-page ul {
    margin: 10px 0 10px 22px;
  }

  .pdf-page li {
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 6px;
  }

  .pdf-meta {
    font-size: 13px;
    color: #555;
    margin-bottom: 30px;
  }

  /* Language switch */
  .lang-switch {
    max-width: 820px;
    margin: 0 auto 10px;
    padding: 0 10px;
    text-align: right;
    font-family: Arial, sans-serif;
  }

  .lang-btn {
    cursor: pointer;
    border: 1px solid #ddd;
    background: #fff;
    padding: 7px 10px;
    border-radius: 10px;
    font-size: 13px;
    margin-left: 6px;
  }

  .lang-btn.active {
    border-color: #3552c8;
    box-shadow: 0 0 0 2px rgba(53, 82, 200, .15);
  }

  @media print {
    .lang-switch {
      display: none;
    }

    body {
      background: #fff;
    }

    .pdf-page {
      box-shadow: none;
      margin: 0;
      padding: 40px;
    }
  }
</style>

<div class="lang-switch" aria-label="Language Switch">
  <button class="lang-btn" id="btnID" type="button" onclick="setLang('id')">🇮🇩 ID</button>
  <button class="lang-btn" id="btnEN" type="button" onclick="setLang('en')">🇬🇧 EN</button>
</div>

<!-- =========================
     INDONESIA (KBLI 93242)
========================== -->
<section class="pdf-page lang-id">

  <h1>Syarat & Ketentuan Layanan</h1>
  <div class="pdf-meta">
    KBLI 93242 – Wisata Selam<br>
    Effective Date: January 2025
  </div>

  <h4>1. Penerimaan Syarat</h4>
  <p>
    Syarat & Ketentuan Layanan ("Ketentuan") ini mengatur akses dan penggunaan situs web,
    sistem booking, platform digital, serta layanan yang dioperasikan oleh <strong>Bali Diving</strong>
    ("kami").
  </p>
  <p>
    Dengan mengakses atau menggunakan layanan kami, Anda menyatakan telah membaca, memahami,
    dan menyetujui untuk terikat oleh Ketentuan ini. Jika Anda tidak setuju, mohon tidak menggunakan layanan kami.
  </p>

  <h4>2. Kelayakan Peserta</h4>
  <p>
    Anda harus berusia minimal 18 tahun atau memenuhi usia legal di yurisdiksi Anda untuk melakukan booking.
    Peserta di bawah usia tersebut hanya dapat mengikuti program tertentu dengan persetujuan wali yang sah
    dan sesuai kebijakan keselamatan yang berlaku.
  </p>

  <h4>3. Ruang Lingkup Layanan (KBLI 93242 – Wisata Selam)</h4>
  <p>
    Sesuai KBLI 93242 – Wisata Selam, layanan kami dapat mencakup aktivitas scuba diving,
    try diving/intro program, fun diving, snorkeling, freediving, trip/boat dive, pemanduan,
    penyewaan peralatan selam, serta layanan terkait yang diinformasikan pada halaman produk/booking.
  </p>
  <p>
    Seluruh layanan bergantung pada ketersediaan, standar keselamatan, kondisi cuaca/laut,
    persyaratan sertifikasi, dan pertimbangan operasional lainnya.
  </p>

  <h4>4. Booking, Pembayaran & Harga</h4>
  <p>
    Harga dapat berubah sewaktu-waktu. Booking dianggap terkonfirmasi setelah pembayaran berhasil
    atau setelah konfirmasi tertulis dari Bali Diving (sesuai metode booking yang digunakan).
  </p>
  <p>
    Pembayaran diproses melalui pihak ketiga (payment gateway/bank). Kami tidak menyimpan data kartu penuh
    dan tidak bertanggung jawab atas gangguan pemrosesan pembayaran di luar kendali kami.
  </p>

  <h4>5. Kebijakan Pembatalan & Pengembalian Dana</h4>
  <ol>
    <li><strong>Pembatalan yang dilakukan lebih dari 2 hari sebelum tanggal mulai trip:</strong><br>Pengembalian Dana Penuh dari semua pembayaran deposit yang telah dilakukan (Hanya berlaku untuk trip diving dan snorkeling).</li>
    <li><strong>Pembatalan yang dilakukan kurang dari 1 hari sebelum tanggal mulai trip:</strong><br>Tidak ada pengembalian dana yang akan diberikan.</li>
    <li>Pembayaran deposit untuk pembelian materi E-learning tidak dapat dikembalikan.</li>
    <li>Tidak ada pengembalian dana untuk penyelaman yang terlewat, layanan yang tidak digunakan, penyakit/sakit, atau alasan pribadi yang menghalangi peserta pada hari pelaksanaan.</li>
    <li>Jika pemesanan dilakukan melalui agen perjalanan (travel agent), semua pembatalan dan permintaan pengembalian dana harus ditangani melalui agen tersebut.</li>
    <li>Pada kasus yang jarang terjadi di mana Bali Diving membatalkan trip (tidak termasuk faktor cuaca atau kejadian force majeure), pengembalian dana penuh akan diberikan.</li>
    <li>Pastikan Anda memiliki asuransi perjalanan dan asuransi menyelam yang valid dan mencakup perlindungan pembatalan perjalanan.</li>
  </ol>
  <p><em>Terima kasih telah memilih Bali Diving. Kami tidak sabar menyambut Anda untuk bergabung.</em></p>

  <h4>6. Tanggung Jawab Peserta</h4>
  <p>
    Anda wajib memberikan informasi yang akurat (nama, kontak, dan detail booking). Anda bertanggung jawab
    memastikan memenuhi persyaratan kesehatan, kemampuan berenang (jika diperlukan), sertifikasi, dan standar
    keselamatan
    untuk kegiatan selam/aktivitas air.
  </p>
  <p>
    Anda dilarang menyalahgunakan situs, mengganggu keamanan sistem, melakukan penipuan,
    atau aktivitas melanggar hukum.
  </p>

  <h4>7. Kesehatan, Keselamatan & Risiko Inheren Selam</h4>
  <p>
    Aktivitas selam dan kegiatan perairan memiliki risiko inheren (arus, gelombang, cuaca, tekanan bawah air,
    faktor lingkungan, dan lain-lain). Dengan berpartisipasi, Anda mengakui dan menerima risiko tersebut.
  </p>
  <p>
    Anda wajib mengikuti briefing, SOP, instruksi instruktur/dive guide, serta menggunakan peralatan sesuai arahan.
    Demi keselamatan, kami dapat menolak atau menghentikan partisipasi jika kondisi dinilai tidak aman.
  </p>

  <h4>8. Perubahan Jadwal karena Cuaca/Operasional</h4>
  <p>
    Demi keselamatan, Bali Diving berhak menunda, memindahkan lokasi, menyesuaikan itinerary,
    atau membatalkan aktivitas apabila kondisi cuaca/laut atau faktor operasional dianggap tidak aman.
    Keputusan keselamatan bersifat final.
  </p>

  <h4>9. Peralatan, Kerusakan & Kehilangan</h4>
  <p>
    Peralatan rental diperiksa sebelum digunakan. Peserta bertanggung jawab untuk menjaga peralatan dengan wajar.
    Kehilangan, kerusakan, atau komponen hilang dapat dikenakan biaya perbaikan/penggantian sesuai penilaian kami.
  </p>

  <h4>10. Hak Kekayaan Intelektual</h4>
  <p>
    Seluruh konten situs (teks, foto, video, logo, desain, dan materi digital) adalah milik Bali Diving
    atau pemberi lisensinya. Dilarang menggandakan, mendistribusikan, atau menggunakan secara komersial tanpa izin.
  </p>

  <h4>11. Layanan Pihak Ketiga</h4>
  <p>
    Layanan kami dapat terhubung dengan platform pihak ketiga (payment, training provider, transport, dll).
    Bali Diving tidak bertanggung jawab atas kebijakan, perubahan, atau gangguan layanan pihak ketiga tersebut.
  </p>

  <h4>12. Batasan Tanggung Jawab</h4>
  <p>
    Sejauh diizinkan hukum, Bali Diving tidak bertanggung jawab atas kerugian tidak langsung, insidental,
    khusus, atau konsekuensial yang timbul dari penggunaan situs/layanan atau partisipasi aktivitas.
    Layanan diberikan "sebagaimana adanya" dan "sebagaimana tersedia".
  </p>

  <h4>13. Ganti Rugi (Indemnifikasi)</h4>
  <p>
    Anda setuju untuk membebaskan dan mengganti rugi Bali Diving, pemilik, staf, partner, dan afiliasi
    dari klaim/kerugian yang timbul akibat pelanggaran Ketentuan ini atau tindakan Anda.
  </p>

  <h4>14. Privasi & Dokumentasi Media</h4>
  <p>
    Penggunaan layanan juga tunduk pada <strong>Privacy Policy</strong> kami.
  </p>
  <p>
    Selama kegiatan berlangsung, staf atau instruktur kami dapat mengambil foto atau video secara bijak dan dengan tetap
    menghormati kenyamanan konsumen. Dokumentasi ini utamanya bertujuan untuk diberikan kepada konsumen secara pribadi,
    atau terkadang dapat digunakan untuk koleksi aktivitas di media sosial kami. Pengambilan gambar dilakukan tanpa
    paksaan, dan dengan menggunakan layanan kami, Anda menyetujui hal tersebut. Anda tentu saja berhak memberi tahu staf
    kami di lokasi jika tidak ingin difoto/direkam.
  </p>

  <h4>15. Hukum yang Berlaku & Yurisdiksi</h4>
  <p>
    Ketentuan ini diatur oleh hukum Republik Indonesia. Sengketa tunduk pada yurisdiksi pengadilan berwenang di
    Indonesia.
  </p>

  <h4>16. Perubahan Ketentuan</h4>
  <p>
    Kami dapat memperbarui Ketentuan ini sewaktu-waktu. Penggunaan layanan setelah perubahan dipublikasikan
    berarti Anda menerima Ketentuan yang diperbarui.
  </p>

  <h4>17. Kontak</h4>
  <p>
    Untuk pertanyaan terkait Ketentuan ini, hubungi:<br>
    <strong>Bali Diving</strong><br>
    Email: <a href="mailto:legal@balidiving.com">legal@balidiving.com</a><br>
    Office: Jl. Bypass Ngurah Rai 46E, Sanur, Bali – Indonesia
  </p>

  <p style="margin-top: 40px; color: #555;">
    Dengan menggunakan situs ini atau layanan Bali Diving, Anda menyatakan telah membaca,
    memahami, dan menyetujui Syarat & Ketentuan Layanan ini.
  </p>

</section>

<!-- =========================
     ENGLISH (KBLI 93242)
========================== -->
<section class="pdf-page lang-en" style="display:none;">

  <h1>Terms of Service</h1>
  <div class="pdf-meta">
    Business Classification: KBLI 93242 – Diving Tourism<br>
    Effective Date: January 2025
  </div>

  <h4>1. Acceptance of Terms</h4>
  <p>
    These Terms of Service ("Terms") govern your access to and use of the website,
    booking systems, digital platforms, and services operated by <strong>Bali Diving</strong>
    ("we", "us", or "our").
  </p>
  <p>
    By accessing, browsing, or using any part of our services, you confirm that you have read,
    understood, and agreed to be bound by these Terms. If you do not agree, you must not use our services.
  </p>

  <h4>2. Eligibility</h4>
  <p>
    You must be at least 18 years old, or the legal age in your jurisdiction, to make bookings.
    Minors may only join certain programs with lawful guardian consent and subject to safety policies.
  </p>

  <h4>3. Services Provided (KBLI 93242 – Diving Tourism)</h4>
  <p>
    In line with <strong>KBLI 93242 – Diving Tourism</strong>, our services may include scuba diving activities,
    try-diving/intro programs, fun diving, snorkeling, freediving, boat dives/trips, guiding services,
    equipment rental, and other related services as described on our product/booking pages.
  </p>
  <p>
    All services are subject to availability, safety requirements, sea/weather conditions,
    certification requirements, and operational considerations.
  </p>

  <h4>4. Bookings, Payments & Pricing</h4>
  <p>
    Prices may change without notice. A booking is confirmed only after successful payment
    or explicit written confirmation by Bali Diving (depending on the booking method used).
  </p>
  <p>
    Payments are processed via third-party providers (payment gateway/bank). We do not store full card details
    and are not responsible for payment processing issues beyond our control.
  </p>

  <h4>5. Cancellation & Refund Policy</h4>
  <ol>
    <li><strong>Cancellations were made more than 2 days before the trip start date:</strong><br>Full Refund of all
      deposit payments made (Only apply for diving and snorkeling trips).</li>
    <li><strong>Cancellations made less than 1 day before the trip start date:</strong><br>No Refund will be issued.
    </li>
    <li>Deposit payment for purchasing the E-learning materials cannot be refunded.</li>
    <li>No refunds will be given for missed dives, unused services, illness, or personal reasons preventing participants
      on the day.</li>
    <li>If a booking was made through a travel agent, all cancellations and refund requests must be handled through that
      agent.</li>
    <li>In the rare case that Bali Diving cancels a trip (excluding weather or force majeure events), a full refund will
      be provided.</li>
    <li>Please ensure you have valid travel and dive insurance that includes trip cancellation coverage.</li>
  </ol>
  <p><em>Thank you for choosing Bali Diving. We look forward to welcoming you on board.</em></p>

  <h4>6. User Responsibilities</h4>
  <p>
    You agree to provide accurate, current, and complete information. You are responsible for ensuring that you
    meet health, swimming ability (if required), certification, and safety requirements for diving/water activities.
  </p>
  <p>
    You agree not to misuse the website, interfere with system security, engage in fraud, or any unlawful activity.
  </p>

  <h4>7. Health, Safety & Assumption of Risk</h4>
  <p>
    Diving and water activities involve inherent risks (currents, waves, weather, underwater pressure, environmental
    factors, etc.).
    By participating, you acknowledge and voluntarily assume all associated risks.
  </p>
  <p>
    You must follow all briefings, SOPs, and instructor/dive guide instructions, and use equipment as directed.
    For safety reasons, we may refuse or stop participation if conditions are deemed unsafe.
  </p>

  <h4>8. Schedule Changes Due to Sea/Weather/Operations</h4>
  <p>
    For safety reasons, Bali Diving may postpone, relocate, adjust itineraries, or cancel activities if sea/weather
    conditions or operational factors are considered unsafe. Safety decisions are final.
  </p>

  <h4>9. Equipment Rental, Damage & Loss</h4>
  <p>
    Rental equipment is inspected before use. Participants must take reasonable care of equipment.
    Any loss, damage, or missing parts may be charged at repair/replacement cost as assessed by us.
  </p>

  <h4>10. Intellectual Property</h4>
  <p>
    All website content (text, images, videos, logos, designs, and digital materials) is owned by Bali Diving
    or its licensors. Unauthorized reproduction, distribution, or commercial use is prohibited.
  </p>

  <h4>11. Third-Party Services</h4>
  <p>
    Our services may involve third-party platforms (payments, training providers, transport, etc.).
    Bali Diving is not responsible for third-party policies, changes, or outages.
  </p>

  <h4>12. Limitation of Liability</h4>
  <p>
    To the maximum extent permitted by law, Bali Diving shall not be liable for any indirect, incidental,
    special, or consequential damages arising from the use of our services, website, or participation in activities.
    All services are provided "as is" and "as available".
  </p>

  <h4>13. Indemnification</h4>
  <p>
    You agree to indemnify and hold harmless Bali Diving, its owners, staff, partners, and affiliates
    from claims, damages, losses, or expenses arising from your use of our services or violation of these Terms.
  </p>

  <h4>14. Privacy & Media Documentation</h4>
  <p>
    Your use of our services is also governed by our <strong>Privacy Policy</strong>.
  </p>
  <p>
    During our activities, our staff or instructors may wisely and respectfully take photos or videos of the experience.
    This documentation is primarily intended to be given to you as a personal keepsake, or occasionally used for our
    activity collections on social media. These are taken without coercion, and by using our services, you consent to
    this documentation. You always have the right to inform our staff on-site if you prefer not to be photographed or
    recorded.
  </p>

  <h4>15. Governing Law & Jurisdiction</h4>
  <p>
    These Terms are governed by the laws of the Republic of Indonesia. Any disputes shall be subject to the
    jurisdiction of the competent courts in Indonesia.
  </p>

  <h4>16. Changes to These Terms</h4>
  <p>
    Bali Diving reserves the right to update or modify these Terms at any time.
    Continued use of the services after changes are posted constitutes acceptance of the revised Terms.
  </p>

  <h4>17. Contact Information</h4>
  <p>
    For questions regarding these Terms of Service, please contact:<br>
    <strong>Bali Diving</strong><br>
    Email: <a href="mailto:legal@balidiving.com">legal@balidiving.com</a><br>
    Office: Jl. Bypass Ngurah Rai 46E, Sanur, Bali – Indonesia
  </p>

  <p style="margin-top: 40px; color: #555;">
    By using this website or any services provided by Bali Diving,
    you acknowledge that you have read, understood, and agreed to these Terms of Service.
  </p>

</section>

<script>
  function setLang(lang) {
    document.querySelectorAll('.lang-id').forEach(el => el.style.display = (lang === 'id') ? 'block' : 'none');
    document.querySelectorAll('.lang-en').forEach(el => el.style.display = (lang === 'en') ? 'block' : 'none');

    var bID = document.getElementById('btnID');
    var bEN = document.getElementById('btnEN');
    if (bID && bEN) {
      bID.classList.toggle('active', lang === 'id');
      bEN.classList.toggle('active', lang === 'en');
    }

    try { localStorage.setItem('site_lang', lang); } catch (e) { }
  }

  (function () {
    var saved = null;
    try { saved = localStorage.getItem('site_lang'); } catch (e) { }
    var browser = (navigator.language || 'en').toLowerCase();
    var defaultLang = (browser.startsWith('id')) ? 'id' : 'en';
    setLang(saved || defaultLang);
  })();
</script>
<?php include('template/consent.php'); ?>
<?php include('03-end.php') ?>