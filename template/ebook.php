<section class="py-14 px-4 bg-white">
  <div class="ebook-wrap">
    
    <!-- TEXT -->
    <div class="ebook-text">
      <h2>Scuba Diving Guide (Bali)</h2>
      <p>
        A simple, clear guide to help you dive safely and confidently in Bali.
      </p>
    </div>

    <!-- CTA -->
    <div class="ebook-cta">
      <a 
        id="downloadBtn"
        href="https://docs.google.com/uc?export=download&id=1QbHay0NOQ1BARI2hoFMBfh5xFgcUGT8L"
        class="btn-download"
      >
        <span id="btnText">Download eBook</span>
        <span id="spinner"></span>
      </a>
    </div>

  </div>
</section>

<script>
  const btn = document.getElementById('downloadBtn');
  const text = document.getElementById('btnText');
  const spinner = document.getElementById('spinner');

  btn.addEventListener('click', function () {
    text.textContent = "Loading...";
    spinner.style.display = "inline-block";

    setTimeout(() => {
      spinner.style.display = "none";
      text.textContent = "Download Again";
    }, 15000);
  });
</script>

<style>
/* ===== Base ===== */
.ebook-wrap{
  max-width: 1100px;
  margin: auto;
  background: #f9fafc;
  border: 1px solid #e5eaf5;
  border-radius: 18px;
  padding: 32px;
  box-shadow: 0 12px 30px rgba(6,60,127,.08);
  display: flex;
  gap: 32px;
  align-items: center;
}

/* ===== Text ===== */
.ebook-text h2{
  font-size: 26px;
  font-weight: 700;
  color: #063c7f;
  margin-bottom: 10px;
}
.ebook-text p{
  font-size: 16px;
  color: #3552c8;
  max-width: 520px;
}

/* ===== CTA ===== */
.ebook-cta{
  margin-left: auto;
}
.btn-download{
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 28px;
  font-weight: 600;
  border-radius: 12px;
  background: #0070d3;
  color: #fff;
  text-decoration: none;
  box-shadow: 0 6px 16px rgba(0,112,211,.35);
  transition: all .25s ease;
}
.btn-download:hover{
  background:#3552c8;
  transform: translateY(-1px);
}

/* Spinner */
#spinner{
  display:none;
  width:16px;
  height:16px;
  border:2px solid rgba(255,255,255,.4);
  border-top-color:#fff;
  border-radius:50%;
  animation: spin .8s linear infinite;
}

/* ===== Mobile ===== */
@media (max-width: 768px){
  .ebook-wrap{
    flex-direction: column;
    text-align: center;
    padding: 28px 22px;
  }
  .ebook-text p{
    max-width: 100%;
  }
  .ebook-cta{
    margin-left: 0;
    margin-top: 10px;
  }
  .btn-download{
    width: 100%;
    justify-content: center;
  }
}

@keyframes spin{
  from{transform:rotate(0)}
  to{transform:rotate(360deg)}
}
</style>
