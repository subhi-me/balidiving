<?php
$page = 'disclaimer';
include('01-start.php');
?>
<div style="height:100px;"></div>

<style>
/* PDF-like document style */
.pdf-page{
  background:#fff;
  max-width: 820px;
  margin: 0 auto 80px;
  padding: 60px;
  box-shadow: 0 0 0 1px #e5e5e5, 0 20px 40px rgba(0,0,0,.08);
  font-family: "Times New Roman", Georgia, serif;
  color:#111;
}
.pdf-page h1{font-size:28px;margin-bottom:6px;}
.pdf-page h4{font-size:20px;margin-top:32px;margin-bottom:10px;}
.pdf-page p{font-size:15px;line-height:1.8;margin:8px 0;text-align:justify;}
.pdf-page ul{margin:10px 0 10px 22px;}
.pdf-page li{font-size:15px;line-height:1.7;margin-bottom:6px;}
.pdf-meta{font-size:13px;color:#555;margin-bottom:30px;}

.lang-switch{
  max-width: 820px;
  margin: 0 auto 10px;
  padding: 0 10px;
  text-align: right;
  font-family: Arial, sans-serif;
}
.lang-btn{
  cursor:pointer;
  border:1px solid #ddd;
  background:#fff;
  padding:7px 10px;
  border-radius:10px;
  font-size:13px;
  margin-left:6px;
}
.lang-btn.active{
  border-color:#3552c8;
  box-shadow:0 0 0 2px rgba(53,82,200,.15);
}

@media print{
  .lang-switch{display:none;}
  body{background:#fff;}
  .pdf-page{box-shadow:none;margin:0;padding:40px;}
}
</style>

<div class="lang-switch" aria-label="Language Switch">
  <button class="lang-btn" id="btnID" type="button" onclick="setLang('id')">🇮🇩 ID</button>
  <button class="lang-btn" id="btnEN" type="button" onclick="setLang('en')">🇬🇧 EN</button>
</div>

<!-- =========================
     INDONESIAN VERSION
========================== -->
<section class="pdf-page lang-id">

  <h1>Disclaimer – Wisata Selam</h1>
  <div class="pdf-meta">
    KBLI 93242 – Wisata Selam<br>
    Effective Date: January 2025
  </div>

  <h4>1. Ruang Lingkup Kegiatan</h4>
  <p>
    Disclaimer ini berlaku untuk seluruh kegiatan wisata selam (scuba diving),
    snorkeling, freediving, pengenalan selam, pelatihan dasar, penyewaan peralatan,
    dan aktivitas perairan lainnya yang diselenggarakan oleh <strong>Bali Diving</strong>,
    sesuai dengan klasifikasi usaha <strong>KBLI 93242 – Wisata Selam</strong>.
  </p>

  <h4>2. Risiko Inheren Aktivitas Selam</h4>
  <p>
    Wisata selam dan aktivitas perairan mengandung risiko alamiah, termasuk namun tidak terbatas pada:
  </p>
  <ul>
    <li>Perubahan kondisi laut, arus, gelombang, dan cuaca.</li>
    <li>Risiko tekanan bawah air, barotrauma, dan dekompresi.</li>
    <li>Interaksi dengan biota laut.</li>
    <li>Kegagalan atau keterbatasan fungsi peralatan.</li>
  </ul>
  <p>
    Dengan mengikuti kegiatan wisata selam, peserta secara sadar memahami dan
    menerima seluruh risiko yang melekat pada aktivitas tersebut.
  </p>

  <h4>3. Tanggung Jawab Kesehatan & Kondisi Fisik</h4>
  <p>
    Peserta bertanggung jawab penuh untuk memastikan kondisi fisik dan kesehatannya
    layak untuk melakukan aktivitas selam atau kegiatan perairan lainnya.
  </p>
  <p>
    Bali Diving tidak bertanggung jawab atas kondisi medis tersembunyi, penyakit,
    cedera, atau komplikasi kesehatan yang terjadi sebelum, selama, maupun setelah kegiatan.
  </p>

  <h4>4. Sertifikasi, Pengalaman & Kepatuhan Instruksi</h4>
  <p>
    Untuk kegiatan tertentu, peserta wajib memiliki sertifikasi selam yang sah
    atau mengikuti program yang sesuai dengan tingkat pengalaman mereka.
  </p>
  <p>
    Peserta wajib mematuhi seluruh instruksi keselamatan, briefing, standar operasional,
    dan arahan instruktur atau pemandu selam selama kegiatan berlangsung.
  </p>

  <h4>5. Peralatan & Penggunaan</h4>
  <p>
    Peralatan selam yang disediakan telah melalui pemeriksaan standar operasional.
    Namun, peserta tetap bertanggung jawab untuk:
  </p>
  <ul>
    <li>Melaporkan ketidaknyamanan atau masalah pada peralatan sebelum digunakan.</li>
    <li>Menggunakan peralatan sesuai fungsi dan instruksi.</li>
    <li>Menjaga dan mengembalikan peralatan dalam kondisi wajar.</li>
  </ul>

  <h4>6. Kondisi Alam & Pembatalan Kegiatan</h4>
  <p>
    Demi keselamatan, Bali Diving berhak menunda, mengubah, atau membatalkan kegiatan
    apabila kondisi cuaca, laut, atau faktor alam lainnya dinilai tidak aman.
  </p>
  <p>
    Keputusan tersebut bersifat final dan diambil untuk melindungi keselamatan peserta dan kru.
  </p>

  <h4>7. Batasan Tanggung Jawab</h4>
  <p>
    Sejauh diizinkan oleh peraturan perundang-undangan yang berlaku,
    Bali Diving tidak bertanggung jawab atas kerugian tidak langsung,
    insidental, atau konsekuensial yang timbul dari partisipasi peserta
    dalam kegiatan wisata selam.
  </p>

  <h4>8. Kepatuhan Hukum & Yurisdiksi</h4>
  <p>
    Disclaimer ini disusun sesuai dengan ketentuan hukum yang berlaku di
    Republik Indonesia, termasuk regulasi pariwisata dan usaha wisata selam.
  </p>
  <p>
    Segala sengketa yang timbul akan diselesaikan berdasarkan hukum Indonesia
    dan berada dalam yurisdiksi pengadilan yang berwenang di Indonesia.
  </p>

  <h4>9. Perubahan Disclaimer</h4>
  <p>
    Bali Diving berhak memperbarui Disclaimer ini sewaktu-waktu.
    Perubahan berlaku efektif sejak tanggal dipublikasikan di halaman ini.
  </p>

  <h4>10. Kontak</h4>
  <p>
    Untuk pertanyaan terkait Disclaimer ini, silakan hubungi:<br>
    <strong>Bali Diving</strong><br>
    Email: <a href="mailto:customer.service@balidiving.com">customer.service@balidiving.com</a><br>
    Alamat: Jl. Bypass Ngurah Rai 46E, Sanur, Bali – Indonesia
  </p>

  <p style="margin-top:40px;color:#555;">
    Dengan mengikuti kegiatan wisata selam atau menggunakan layanan Bali Diving,
    Anda menyatakan telah membaca, memahami, dan menyetujui seluruh isi Disclaimer ini.
  </p>

</section>

<!-- =========================
     ENGLISH VERSION
========================== -->
<section class="pdf-page lang-en" style="display:none;">

  <h1>Disclaimer – Diving Tourism</h1>
  <div class="pdf-meta">
    Business Classification: KBLI 93242 – Diving Tourism<br>
    Effective Date: January 2025
  </div>

  <h4>1. Scope of Activities</h4>
  <p>
    This Disclaimer applies to all diving tourism activities (scuba diving),
    snorkeling, freediving, try-diving/intro programs, basic training activities,
    equipment rental, and other water-based activities organized by <strong>Bali Diving</strong>,
    in line with the business classification <strong>KBLI 93242 – Diving Tourism</strong>.
  </p>

  <h4>2. Inherent Risks</h4>
  <p>
    Diving and water activities involve inherent risks, including but not limited to:
  </p>
  <ul>
    <li>Changing sea conditions, currents, waves, and weather.</li>
    <li>Underwater pressure-related risks, including barotrauma and decompression illness.</li>
    <li>Interaction with marine life.</li>
    <li>Equipment limitations or malfunction.</li>
  </ul>
  <p>
    By joining any diving tourism activity, participants knowingly understand and accept
    all inherent risks associated with such activities.
  </p>

  <h4>3. Health & Fitness Responsibility</h4>
  <p>
    Participants are fully responsible for ensuring they are medically and physically fit
    to participate in diving or any water activity.
  </p>
  <p>
    Bali Diving is not responsible for undisclosed medical conditions, illness, injuries,
    or health complications occurring before, during, or after participation.
  </p>

  <h4>4. Certification, Experience & Compliance</h4>
  <p>
    For certain activities, participants must hold valid diving certification
    or enroll in programs appropriate to their experience level.
  </p>
  <p>
    Participants must follow all safety instructions, briefings, standard operating procedures,
    and directions given by instructors or dive guides throughout the activity.
  </p>

  <h4>5. Equipment & Use</h4>
  <p>
    Provided equipment is inspected under operational standards. However, participants remain responsible to:
  </p>
  <ul>
    <li>Report discomfort or any equipment concerns before use.</li>
    <li>Use equipment properly and as instructed.</li>
    <li>Care for and return equipment in reasonable condition.</li>
  </ul>

  <h4>6. Natural Conditions & Activity Changes</h4>
  <p>
    For safety reasons, Bali Diving may postpone, modify, relocate, or cancel activities
    if weather, sea conditions, or other natural factors are considered unsafe.
  </p>
  <p>
    Such decisions are final and are made to protect the safety of participants and crew.
  </p>

  <h4>7. Limitation of Liability</h4>
  <p>
    To the maximum extent permitted by applicable law, Bali Diving shall not be liable for
    indirect, incidental, or consequential losses arising from participation in diving tourism activities.
  </p>

  <h4>8. Legal Compliance & Jurisdiction</h4>
  <p>
    This Disclaimer is prepared under the laws and regulations applicable in the Republic of Indonesia,
    including tourism and diving-related business regulations.
  </p>
  <p>
    Any dispute arising in connection with this Disclaimer shall be governed by Indonesian law
    and subject to the jurisdiction of the competent courts in Indonesia.
  </p>

  <h4>9. Updates to This Disclaimer</h4>
  <p>
    Bali Diving may update this Disclaimer at any time. Updates become effective
    when posted on this page.
  </p>

  <h4>10. Contact</h4>
  <p>
    For questions regarding this Disclaimer, please contact:<br>
    <strong>Bali Diving</strong><br>
    Email: <a href="mailto:customer.service@balidiving.com">customer.service@balidiving.com</a><br>
    Address: Jl. Bypass Ngurah Rai 46E, Sanur, Bali – Indonesia
  </p>

  <p style="margin-top:40px;color:#555;">
    By accessing this website, using Bali Diving services, or participating in any activity,
    you acknowledge that you have read, understood, and agreed to this Disclaimer.
  </p>

</section>

<script>
function setLang(lang){
  // toggle sections
  document.querySelectorAll('.lang-id').forEach(el=>el.style.display = (lang==='id') ? 'block' : 'none');
  document.querySelectorAll('.lang-en').forEach(el=>el.style.display = (lang==='en') ? 'block' : 'none');

  // button active state
  var bID = document.getElementById('btnID');
  var bEN = document.getElementById('btnEN');
  if(bID && bEN){
    bID.classList.toggle('active', lang==='id');
    bEN.classList.toggle('active', lang==='en');
  }

  // persist
  try { localStorage.setItem('site_lang', lang); } catch(e) {}
}

// auto-load language: prefer saved choice; fallback to browser language
(function(){
  var saved = null;
  try { saved = localStorage.getItem('site_lang'); } catch(e) {}
  var browser = (navigator.language || 'en').toLowerCase();
  var defaultLang = (browser.startsWith('id')) ? 'id' : 'en';
  setLang(saved || defaultLang);
})();
</script>
<?php include('template/consent.php');?>
<?php include('03-end.php')?>
