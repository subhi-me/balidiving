<?php
$page = 'explore';
include('../01-start.php');
include('../template/auto_article_creator.php');
?>

<style>
/* ===============================
   SCOPE VARIABLES (ANTI GLOBAL)
   =============================== */
#bdLinkHub{
  --bg1:#0a4d68;
  --bg2:#088395;
  --bg3:#05bfdb;

  --card: rgba(255,255,255,.92);
  --cardBorder: rgba(255,255,255,.18);
  --text:#083a50;
  --title:#ffffff;
  --muted:#e0f7ff;
  --action:#088395;

  --radius:18px;
  --shadow:0 14px 40px rgba(0,0,0,.20);
  --shadowHover:0 18px 55px rgba(0,0,0,.28);

  --glass: rgba(255,255,255,.14);
  --glassBorder: rgba(255,255,255,.22);
}

/* ===============================
   CONTAINER
   =============================== */
#bdLinkHub.ocean{
  min-height:100vh;
  background:linear-gradient(180deg,var(--bg1) 0%,var(--bg2) 52%,var(--bg3) 100%);
  padding:86px 18px 80px;
  overflow:hidden;
  position:relative;
  font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial;
}

#bdLinkHub.ocean::before{
  content:"";
  position:absolute;
  inset:-140px -140px auto -140px;
  height:560px;
  background:
    radial-gradient(closest-side at 22% 40%, rgba(255,255,255,.18), transparent 70%),
    radial-gradient(closest-side at 82% 70%, rgba(255,255,255,.12), transparent 68%),
    radial-gradient(closest-side at 55% 0%, rgba(255,255,255,.10), transparent 62%);
  filter: blur(1px);
  pointer-events:none;
  opacity:.95;
}

#bdLinkHub .wrap{
  max-width:1020px;
  margin:0 auto;
  position:relative;
  z-index:1;
}

/* ===============================
   HEADER
   =============================== */
#bdLinkHub .brand{
  text-align:center;
  margin-bottom:22px;
  animation: bdFadeDown .65s ease-out both;
}

#bdLinkHub .brand h1{
  color:var(--title);
  font-size:clamp(28px,3.2vw,46px);
  font-weight:900;
  letter-spacing:.2px;
  margin:0 0 10px;
  text-shadow:0 10px 22px rgba(0,0,0,.18);
}

#bdLinkHub .brand p{
  color:var(--muted);
  max-width:760px;
  margin:0 auto;
  line-height:1.6;
  text-shadow:0 6px 16px rgba(0,0,0,.16);
}

/* ===============================
   TOOLBAR (SEARCH ONLY) + COUNT PILL
   =============================== */
#bdLinkHub .toolbar{
  margin:16px auto 18px;
  max-width:820px;
  display:flex;
  align-items:center;
  justify-content:center;
  flex-wrap:wrap;
  gap:12px;
  animation: bdFadeUp .65s ease-out .06s both;
}

#bdLinkHub .search{
  width:min(820px,100%);
  position:relative;
}

#bdLinkHub .search input{
  width:100%;
  padding:14px 16px 14px 46px;
  border-radius:999px;
  border:1px solid var(--glassBorder);
  background:var(--glass);
  color:rgba(255,255,255,.96);
  outline:none;
  box-shadow:0 14px 28px rgba(0,0,0,.14);
  backdrop-filter: blur(10px);
  transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
}

#bdLinkHub .search input::placeholder{
  color:rgba(255,255,255,.78);
}

#bdLinkHub .search input:focus{
  transform: translateY(-1px);
  box-shadow:0 18px 40px rgba(0,0,0,.18);
  background:rgba(255,255,255,.17);
}

#bdLinkHub .search .icon{
  position:absolute;
  left:16px;
  top:50%;
  transform:translateY(-50%);
  font-size:18px;
  opacity:.95;
  pointer-events:none;
}

#bdLinkHub .count-pill{
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding:10px 14px;
  border-radius:999px;
  border:1px solid rgba(255,255,255,.22);
  background:rgba(0,0,0,.18);
  color:rgba(255,255,255,.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 10px 22px rgba(0,0,0,.12);
  font-size:13px;
  letter-spacing:.2px;
  animation: bdFadeUp .65s ease-out .12s both;
}

#bdLinkHub .dot{
  width:8px;height:8px;border-radius:999px;
  background:rgba(255,255,255,.9);
  box-shadow:0 0 0 4px rgba(255,255,255,.12);
}

#bdLinkHub .count-pill strong{
  font-weight:900;
}

/* ===============================
   GRID
   =============================== */
#bdLinkHub .grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:16px;
  margin-top:10px;
  animation: bdFadeUp .65s ease-out .18s both;
}

@media(max-width:980px){#bdLinkHub .grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:740px){#bdLinkHub .grid{grid-template-columns:repeat(2,1fr);gap:14px}}
@media(max-width:520px){#bdLinkHub .grid{grid-template-columns:1fr}}

/* ===============================
   CARD (MORE POLISHED)
   =============================== */
#bdLinkHub .card{
  background:var(--card);
  border-radius:var(--radius);
  overflow:hidden;
  box-shadow:var(--shadow);
  text-decoration:none;
  color:inherit;
  position:relative;
  transform: translateZ(0);
  transition: transform .28s ease, box-shadow .28s ease, filter .28s ease;
}

#bdLinkHub .card::after{
  content:"";
  position:absolute;
  inset:0;
  background: radial-gradient(circle at 20% 10%, rgba(255,255,255,.45), transparent 45%);
  opacity:.0;
  transition: opacity .28s ease;
  pointer-events:none;
}

#bdLinkHub .card:hover{
  transform:translateY(-8px);
  box-shadow:var(--shadowHover);
  filter: saturate(1.03);
}

#bdLinkHub .card:hover::after{ opacity:.28; }

#bdLinkHub .thumb{
  width:100%;
  height:142px;
  object-fit:cover;
  display:block;
}

@media(max-width:520px){#bdLinkHub .thumb{height:170px}}

#bdLinkHub .body{ padding:14px 14px 16px; }

#bdLinkHub .row{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:10px;
}

#bdLinkHub .label{
  font-weight:900;
  color:var(--text);
  line-height:1.15;
}

#bdLinkHub .meta{
  font-size:12.8px;
  margin-top:8px;
  color:rgba(8,58,80,.72);
  line-height:1.35;
  min-height:34px;
}

#bdLinkHub .arrow{
  width:36px;height:36px;border-radius:999px;
  display:flex;align-items:center;justify-content:center;
  background:rgba(8,131,149,.12);
  color:var(--action);
  transition: transform .28s ease, background .28s ease;
  flex:0 0 auto;
}

#bdLinkHub .card:hover .arrow{
  transform: translateX(5px);
  background:rgba(8,131,149,.16);
}

/* ===============================
   FOOTER SAFETY
   =============================== */
footer h4,.footer-follow-title{white-space:nowrap}

/* ===============================
   ANIMATIONS
   =============================== */
@keyframes bdFadeDown{from{opacity:0;transform:translateY(-14px)}to{opacity:1;transform:translateY(0)}}
@keyframes bdFadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}

@media (prefers-reduced-motion: reduce){
  #bdLinkHub *{animation:none!important;transition:none!important;}
}
</style>

<div class="ocean" id="bdLinkHub">
  <div class="wrap">

    <header class="brand">
      <div style="height:80px"></div>
      <h1 id="main-title">Bali Diving</h1>
      <p id="subtitle">Explore the underwater paradise</p>
    </header>

    <div class="toolbar">
      <div class="search">
        <span class="icon">🔎</span>
        <input id="searchLinks" type="search" placeholder="Search links… (home, price, contact, faq)" autocomplete="off">
      </div>

      <!-- modern count -->
      <div class="count-pill" id="visibleCount">
        <span class="dot" aria-hidden="true"></span>
        <span>Visible</span>
        <span>•</span>
        <strong id="visibleNum">0</strong>
        <span>/</span>
        <span id="totalNum">0</span>
        <span>links</span>
      </div>
    </div>

    <div class="grid" id="linksGrid">

      <a href="https://balidiving.com/" target="_self" rel="noopener noreferrer"
         class="card" data-title="Home" data-desc="Main website and quick access.">
        <img src="https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg" alt="Home" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">Home</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">Main website and quick access.</div>
        </div>
      </a>

      <a href="https://balidiving.com/pricelist" target="_self" rel="noopener noreferrer"
         class="card" data-title="Book Now" data-desc="See prices & book your next dive.">
        <img src="https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg" alt="Book Now" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">Book Now</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">See prices & book your next dive.</div>
        </div>
      </a>

      <a href="https://balidiving.com/contact?page=contact" target="_self" rel="noopener noreferrer"
         class="card" data-title="Contact" data-desc="WhatsApp, email, and location.">
        <img src="https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg" alt="Contact" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">Contact</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">WhatsApp, email, and location.</div>
        </div>
      </a>

      <a href="https://g.page/r/CSRkANeDMYbaEAE/review" target="_self" rel="noopener noreferrer"
         class="card" data-title="Review" data-desc="Leave a Google review (thank you!).">
        <img src="https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg" alt="Review" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">Review</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">Leave a Google review (thank you!).</div>
        </div>
      </a>

      <a href="https://balidiving.com/about-us" target="_self" rel="noopener noreferrer"
         class="card" data-title="About Us" data-desc="Know the team & story.">
        <img src="https://balidiving.com/images/thumbnails/15-bali-diving.jpg" alt="About Us" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">About Us</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">Know the team & story.</div>
        </div>
      </a>

      <a href="https://balidiving.com/courses/" target="_self" rel="noopener noreferrer"
         class="card" data-title="Learn Diving" data-desc="Courses, certifications, and training.">
        <img src="https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg" alt="Learn Diving" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">Learn Diving</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">Courses, certifications, and training.</div>
        </div>
      </a>

      <a href="https://balidiving.com/articles" target="_self" rel="noopener noreferrer"
         class="card" data-title="Blog" data-desc="Tips, guides, and Bali diving stories.">
        <img src="https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg" alt="Blog" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">Blog</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">Tips, guides, and Bali diving stories.</div>
        </div>
      </a>

      <a href="https://balidiving.com/team" target="_self" rel="noopener noreferrer"
         class="card" data-title="Meet The Team" data-desc="People behind Bali Diving.">
        <img src="https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg" alt="Meet The Team" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">Meet The Team</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">People behind Bali Diving.</div>
        </div>
      </a>

      <a href="https://balidiving.com/gallery" target="_self" rel="noopener noreferrer"
         class="card" data-title="Photo Gallery" data-desc="Photos & underwater moments.">
        <img src="https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg" alt="Photo Gallery" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">Photo Gallery</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">Photos & underwater moments.</div>
        </div>
      </a>

      <a href="https://balidiving.com/faq" target="_self" rel="noopener noreferrer"
         class="card" data-title="FAQ" data-desc="Quick answers for common questions.">
        <img src="https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg" alt="FAQ" class="thumb"
             onerror="this.style.display='none'">
        <div class="body">
          <div class="row">
            <div class="label">FAQ</div>
            <div class="arrow">→</div>
          </div>
          <div class="meta">Quick answers for common questions.</div>
        </div>
      </a>

    </div>

  </div>
</div>

<script>
(function(){
  const hub = document.getElementById('bdLinkHub');
  if(!hub) return;

  const q = hub.querySelector('#searchLinks');
  const cards = Array.from(hub.querySelectorAll('.card'));
  const elVisible = hub.querySelector('#visibleNum');
  const elTotal = hub.querySelector('#totalNum');

  const total = cards.length;
  elTotal.textContent = total.toString();

  function setCount(visible){
    elVisible.textContent = visible.toString();
    // subtle pulse when changed
    const pill = hub.querySelector('#visibleCount');
    pill.style.transform = 'translateY(-1px)';
    setTimeout(()=> pill.style.transform = 'translateY(0)', 120);
  }

  function filter(){
    const term = (q.value || '').trim().toLowerCase();
    let visible = 0;

    cards.forEach(card=>{
      const title = (card.dataset.title || '').toLowerCase();
      const desc  = (card.dataset.desc  || '').toLowerCase();
      const hit = !term || title.includes(term) || desc.includes(term);
      card.style.display = hit ? 'block' : 'none';
      if(hit) visible++;
    });

    setCount(visible);
  }

  q.addEventListener('input', filter);
  filter();
})();
</script>

<?php include('../03-end.php')?>
