<style>
  :root{
    --navy:#063c7f;
    --teal:#23a0b4;
    --red:#f23d4e;
    --soft:#eef4ff;
    --ink:#0f172a;
    --muted:#64748b;
    --ring: rgba(6,60,127,.18);
  }

  .gap{ height: 48px; }

  /* ===== Google-style Reviews Shell ===== */
  .g-reviews{
    max-width: 980px;
    margin: 0 auto;
    padding: 0 16px;
  }

  .g-shell{
    position: relative;
    border-radius: 22px;
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 18px 45px rgba(2, 6, 23, .10);
    backdrop-filter: blur(10px);
    overflow: hidden;
  }

  .g-shell::before{
    content:"";
    position:absolute; inset:-2px;
    background:
      radial-gradient(1200px 420px at 18% -12%, rgba(35,160,180,.16), transparent 60%),
      radial-gradient(900px 420px at 92% 8%, rgba(6,60,127,.16), transparent 58%);
    pointer-events:none;
  }

  /* ===== Header (Google-like summary) ===== */
  .g-head{
    position: relative;
    z-index: 1;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
    padding: 18px 18px 12px;
    border-bottom: 1px solid rgba(15,23,42,.06);
  }

  .g-brand{
    display:flex; align-items:center; gap:10px;
  }

  .g-title{
    margin:0;
    font-size: 16px;
    font-weight: 750;
    color: var(--ink);
    line-height: 1.2;
  }

  .g-sub{
    margin: 2px 0 0;
    font-size: 12px;
    color: var(--muted);
  }

  .g-score{
    text-align:right;
    min-width: 120px;
  }

  .g-score .num{
    font-size: 26px;
    font-weight: 800;
    color: var(--ink);
    line-height: 1;
    letter-spacing: -0.02em;
  }

  .g-score .meta{
    margin-top: 4px;
    font-size: 12px;
    color: var(--muted);
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap: 6px;
    white-space: nowrap;
  }

  /* Stars (SVG) */
  .g-stars{
    display:inline-flex;
    gap: 2px;
    vertical-align: middle;
  }
  .g-stars svg{
    width: 14px;
    height: 14px;
    fill: #fbbc04; /* Google-ish gold */
    filter: drop-shadow(0 1px 0 rgba(0,0,0,.08));
  }

  /* ===== Track (scroll-snap, Google-ish) ===== */
  .g-track-wrap{
    position: relative;
    z-index: 1;
    padding: 14px 14px 10px;
  }

  .g-track{
    display:flex;
    gap: 12px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 8px;
  }

  .g-track::-webkit-scrollbar{ height: 8px; }
  .g-track::-webkit-scrollbar-thumb{
    background: rgba(100,116,139,.28);
    border-radius: 999px;
  }
  .g-track::-webkit-scrollbar-track{ background: transparent; }

  .g-card{
    scroll-snap-align: start;
    flex: 0 0 86%;
    background: rgba(255,255,255,.96);
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(2,6,23,.08);
    padding: 14px 14px 12px;
  }

  @media (min-width: 640px){
    .g-head{ padding: 18px 20px 12px; }
    .g-track-wrap{ padding: 14px 18px 10px; }
    .g-card{ flex-basis: 48%; }
  }

  @media (min-width: 1024px){
    .g-card{ flex-basis: 32%; }
  }

  /* Card top row */
  .g-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
    margin-bottom: 10px;
  }

  .g-user{
    display:flex;
    align-items:center;
    gap: 10px;
    min-width: 0;
  }

  .g-avatar{
    width: 38px; height: 38px;
    border-radius: 999px;
    display:flex; align-items:center; justify-content:center;
    color:#fff;
    font-weight:800;
    box-shadow: 0 10px 22px rgba(2,6,23,.14);
    flex: 0 0 auto;
  }

  .g-name{
    font-weight: 750;
    color: var(--ink);
    font-size: 14px;
    line-height: 1.15;
    margin:0;
    overflow:hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 210px;
  }

  .g-mini{
    margin-top: 3px;
    font-size: 12px;
    color: var(--muted);
    display:flex;
    align-items:center;
    gap: 8px;
    white-space: nowrap;
  }

  .g-mini .sep{ opacity:.6; }

  .g-google-pill{
    display:inline-flex;
    align-items:center;
    gap: 6px;
    font-size: 12px;
    color: var(--muted);
  }

  .g-google-pill svg{ width: 16px; height: 16px; }

  /* Review text (Google-ish clamp) */
  .g-text{
    margin:0;
    color: #1f2937;
    font-size: 13.5px;
    line-height: 1.55;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* Dots */
  .g-dots{
    position: relative;
    z-index: 1;
    display:flex;
    justify-content:center;
    gap: 8px;
    padding: 0 0 14px;
  }
  .g-dot{
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: rgba(100,116,139,.35);
    border: 1px solid rgba(15,23,42,.06);
    transition: all .22s ease;
    cursor:pointer;
  }
  .g-dot.active{
    width: 24px;
    background: var(--navy);
    box-shadow: 0 10px 24px rgba(6,60,127,.20);
  }

  /* CTA buttons */
  .g-actions{
    display:flex;
    gap: 12px;
    justify-content:center;
    padding: 0 16px 16px;
    position: relative;
    z-index: 1;
  }
  .g-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding: 12px 16px;
    border-radius: 14px;
    font-weight: 750;
    font-size: 14px;
    text-decoration:none;
    color:#fff;
    background: var(--navy);
    box-shadow: 0 14px 34px rgba(6,60,127,.20);
    transition: transform .22s ease, box-shadow .22s ease, filter .22s ease;
    min-width: 150px;
    text-align:center;
  }
  .g-btn:hover{
    transform: translateY(-2px);
    filter: brightness(1.02);
    box-shadow: 0 18px 45px rgba(2,6,23,.18);
  }
  .g-btn.secondary{
    background: linear-gradient(135deg, var(--navy), var(--teal));
  }

  /* Floating + button */
  .g-plus{
    position:absolute;
    top: 14px; right: 14px;
    width: 36px; height: 36px;
    border-radius: 999px;
    display:flex; align-items:center; justify-content:center;
    color:#fff;
    font-size: 20px;
    font-weight: 800;
    background: linear-gradient(135deg, #063c7f, #23a0b4);
    border: 1px solid rgba(255,255,255,.25);
    box-shadow: 0 10px 24px rgba(6,60,127,.22), 0 0 0 4px rgba(6,60,127,.10);
    cursor:pointer;
    transition: transform .22s ease, box-shadow .22s ease;
    z-index: 5;
  }
  .g-plus:hover{
    transform: scale(1.06);
    box-shadow: 0 14px 32px rgba(6,60,127,.30), 0 0 0 6px rgba(35,160,180,.16);
  }

  /* Modal */
  .confirmation-card{
    position: fixed;
    inset: 0;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 18px;
    z-index: 60;
    opacity: 0;
    pointer-events:none;
    transition: opacity .25s ease;
  }
  .confirmation-card.active{
    opacity: 1;
    pointer-events:auto;
  }
  .confirmation-backdrop{
    position:absolute; inset:0;
    background: rgba(2,6,23,.45);
    backdrop-filter: blur(6px);
  }
  .confirmation-box{
    position: relative;
    width: min(360px, 92vw);
    border-radius: 18px;
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 28px 70px rgba(2,6,23,.30);
    padding: 22px 20px;
    transform: translateY(10px) scale(.98);
    transition: transform .28s ease;
  }
  .confirmation-card.active .confirmation-box{
    transform: translateY(0) scale(1);
  }
  .confirmation-title{
    font-size: 16px;
    font-weight: 750;
    color: var(--ink);
    margin-bottom: 14px;
    text-align:center;
  }
  .modal-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    margin: 6px 8px;
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 750;
    font-size: 14px;
    cursor:pointer;
    transition: transform .22s ease, box-shadow .22s ease, filter .22s ease;
    text-decoration:none;
    border: 1px solid rgba(15,23,42,.08);
  }
  .modal-btn.yes{
    background: linear-gradient(135deg, var(--teal), var(--navy));
    color:#fff;
    box-shadow: 0 16px 35px rgba(35,160,180,.22);
    border-color: rgba(255,255,255,.18);
  }
  .modal-btn.no{
    background: linear-gradient(135deg, var(--red), #c81d2b);
    color:#fff;
    box-shadow: 0 16px 35px rgba(242,61,78,.18);
    border-color: rgba(255,255,255,.18);
  }
  .modal-btn:hover{ transform: translateY(-2px); filter: brightness(1.02); }
</style>

<div class="g-reviews">
  <div class="g-shell">
    <div class="g-plus" id="reviewAddBtn" aria-label="Add review">+</div>

    <!-- Header -->
    <div class="g-head">
      <div class="g-brand">
        <!-- Google G -->
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
          <path fill="#EA4335" d="M12 11.2v3.9h5.5c-.2 1.3-1.5 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.1.8 3.8 1.5l2.6-2.5C17.8 4.3 15.2 3 12 3 6.9 3 2.8 7.1 2.8 12S6.9 21 12 21c5.9 0 9.8-4.1 9.8-9.9 0-.7-.1-1.2-.2-1.7H12z"/>
          <path fill="#FBBC05" d="M3.7 8.8l3.2 2.3C7.8 8.5 9.7 7 12 7c1.9 0 3.1.8 3.8 1.5l2.6-2.5C17.8 4.3 15.2 3 12 3 8.5 3 5.5 5 3.7 8.8z" opacity=".0"/>
          <path fill="#34A853" d="M12 21c3.2 0 5.8-1.0 7.7-2.7l-3.6-2.8c-1 .7-2.2 1.1-4.1 1.1-3.3 0-6-2.2-7-5.2l-3.4 2.6C3.3 18.1 7.3 21 12 21z" opacity=".0"/>
          <path fill="#4285F4" d="M21.8 11.1c0-.7-.1-1.2-.2-1.7H12v3.9h5.5c-.3 1.5-1.3 2.8-2.8 3.6l3.6 2.8c2.1-2 3.5-4.8 3.5-8.6z" opacity=".0"/>
        </svg>

        <div>
          <h3 class="g-title">Customer Reviews</h3>
          <p class="g-sub">Real feedback • pulled from Google Reviews</p>
        </div>
      </div>

      <div class="g-score">
        <div class="num">5.0</div>
        <div class="meta">
          <span class="g-stars" aria-label="5 stars">
            <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
            <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
            <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
            <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
            <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
          </span>
          <span>•</span>
          <span>Top rated</span>
        </div>
      </div>
    </div>

    <!-- Track -->
    <div class="g-track-wrap">
      <div class="g-track" id="gTrack">
        <!-- Card 1 -->
        <article class="g-card">
          <div class="g-top">
            <div class="g-user">
              <div class="g-avatar" style="background: linear-gradient(135deg, #063c7f, #23a0b4);">A</div>
              <div>
                <p class="g-name">Austin</p>
                <div class="g-mini">
                  <span class="g-stars" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                  </span>
                  <span class="sep">•</span>
                  <span>a month ago</span>
                </div>
              </div>
            </div>

            <div class="g-google-pill" aria-label="Google review">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M12 11.2v3.9h5.5c-.5 2.6-2.7 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.8 0 3 .7 3.7 1.4l2.5-2.5C17.7 4.2 15.2 3 12 3 7 3 2.9 7.1 2.9 12S7 21 12 21c5.6 0 9.5-3.9 9.5-9.8 0-.7-.1-1.3-.2-1.9H12z"/>
              </svg>
              <span>Google</span>
            </div>
          </div>
          <p class="g-text">Open Water cert done. Eka was excellent. Highlight: USAT Liberty wreck (Tulamben).</p>
        </article>

        <!-- Card 2 -->
        <article class="g-card">
          <div class="g-top">
            <div class="g-user">
              <div class="g-avatar" style="background: linear-gradient(135deg, #f23d4e, #063c7f);">E</div>
              <div>
                <p class="g-name">Eswaran R</p>
                <div class="g-mini">
                  <span class="g-stars" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                  </span>
                  <span class="sep">•</span>
                  <span>a month ago</span>
                </div>
              </div>
            </div>

            <div class="g-google-pill" aria-label="Google review">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M12 11.2v3.9h5.5c-.5 2.6-2.7 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.8 0 3 .7 3.7 1.4l2.5-2.5C17.7 4.2 15.2 3 12 3 7 3 2.9 7.1 2.9 12S7 21 12 21c5.6 0 9.5-3.9 9.5-9.8 0-.7-.1-1.3-.2-1.9H12z"/>
              </svg>
              <span>Google</span>
            </div>
          </div>
          <p class="g-text">Super professional &amp; transparent. Eka was patient and motivating. Highly recommended.</p>
        </article>

        <!-- Card 3 -->
        <article class="g-card">
          <div class="g-top">
            <div class="g-user">
              <div class="g-avatar" style="background: linear-gradient(135deg, #063c7f, #3552c8);">E</div>
              <div>
                <p class="g-name">Ehsan Söchting</p>
                <div class="g-mini">
                  <span class="g-stars" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                  </span>
                  <span class="sep">•</span>
                  <span>2 months ago</span>
                </div>
              </div>
            </div>

            <div class="g-google-pill" aria-label="Google review">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M12 11.2v3.9h5.5c-.5 2.6-2.7 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.8 0 3 .7 3.7 1.4l2.5-2.5C17.7 4.2 15.2 3 12 3 7 3 2.9 7.1 2.9 12S7 21 12 21c5.6 0 9.5-3.9 9.5-9.8 0-.7-.1-1.3-.2-1.9H12z"/>
              </svg>
              <span>Google</span>
            </div>
          </div>
          <p class="g-text">Very well organized. Safety first. Ketut was attentive and made it unforgettable.</p>
        </article>

        <!-- Card 4 -->
        <article class="g-card">
          <div class="g-top">
            <div class="g-user">
              <div class="g-avatar" style="background: linear-gradient(135deg, #23a0b4, #063c7f);">C</div>
              <div>
                <p class="g-name">Christine Chong</p>
                <div class="g-mini">
                  <span class="g-stars" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                  </span>
                  <span class="sep">•</span>
                  <span>3 months ago</span>
                </div>
              </div>
            </div>

            <div class="g-google-pill" aria-label="Google review">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M12 11.2v3.9h5.5c-.5 2.6-2.7 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.8 0 3 .7 3.7 1.4l2.5-2.5C17.7 4.2 15.2 3 12 3 7 3 2.9 7.1 2.9 12S7 21 12 21c5.6 0 9.5-3.9 9.5-9.8 0-.7-.1-1.3-.2-1.9H12z"/>
              </svg>
              <span>Google</span>
            </div>
          </div>
          <p class="g-text">Fun diving at Nusa Penida. First time abroad diving—Eka took great care of us.</p>
        </article>

        <!-- Card 5 -->
        <article class="g-card">
          <div class="g-top">
            <div class="g-user">
              <div class="g-avatar" style="background: linear-gradient(135deg, #f23d4e, #23a0b4);">R</div>
              <div>
                <p class="g-name">Ronald Harford</p>
                <div class="g-mini">
                  <span class="g-stars" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                  </span>
                  <span class="sep">•</span>
                  <span>10 months ago</span>
                </div>
              </div>
            </div>

            <div class="g-google-pill" aria-label="Google review">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M12 11.2v3.9h5.5c-.5 2.6-2.7 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.8 0 3 .7 3.7 1.4l2.5-2.5C17.7 4.2 15.2 3 12 3 7 3 2.9 7.1 2.9 12S7 21 12 21c5.6 0 9.5-3.9 9.5-9.8 0-.7-.1-1.3-.2-1.9H12z"/>
              </svg>
              <span>Google</span>
            </div>
          </div>
          <p class="g-text">Friendly &amp; responsive. Wreck dive worth it. Chris made me feel safe.</p>
        </article>

        <!-- Card 6 -->
        <article class="g-card">
          <div class="g-top">
            <div class="g-user">
              <div class="g-avatar" style="background: linear-gradient(135deg, #063c7f, #f23d4e);">Z</div>
              <div>
                <p class="g-name">Zi Ying Ong</p>
                <div class="g-mini">
                  <span class="g-stars" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                    <svg viewBox="0 0 24 24"><path d="M12 17.3l-6.2 3.7 1.7-7.1L2 9.2l7.2-.6L12 2l2.8 6.6 7.2.6-5.5 4.7 1.7 7.1z"/></svg>
                  </span>
                  <span class="sep">•</span>
                  <span>a year ago</span>
                </div>
              </div>
            </div>

            <div class="g-google-pill" aria-label="Google review">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M12 11.2v3.9h5.5c-.5 2.6-2.7 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.8 0 3 .7 3.7 1.4l2.5-2.5C17.7 4.2 15.2 3 12 3 7 3 2.9 7.1 2.9 12S7 21 12 21c5.6 0 9.5-3.9 9.5-9.8 0-.7-.1-1.3-.2-1.9H12z"/>
              </svg>
              <span>Google</span>
            </div>
          </div>
          <p class="g-text">Open Water was smooth. Ari was kind. Best moment: USAT dive.</p>
        </article>
      </div>
    </div>

    <!-- Dots (auto-generated) -->
    <div class="g-dots" id="gDots" aria-label="Review pages"></div>

    <!-- Actions -->
    <div class="g-actions">
      <a class="g-btn secondary" href="https://g.page/r/CSRkANeDMYbaEAE/review" target="_blank" rel="noopener">Write a Review</a>
      <a class="g-btn" href="login">Access Photos</a>
    </div>
  </div>

  <div style="height:18px;"></div>

  <div class="text-center" style="max-width:860px;margin:0 auto;padding:0 12px;color:#334155;">
    It was a pleasure having you with us. Follow
    <a href="https://www.instagram.com/bali_diving" target="_blank" rel="noopener" style="color:var(--navy);text-decoration:none;font-weight:750;">
      @bali_diving
    </a>
    for updates &amp; offers.
  </div>

  <!-- Confirmation modal -->
  <div class="confirmation-card" id="confirmationCard">
    <div class="confirmation-backdrop" id="confirmationBackdrop"></div>
    <div class="confirmation-box">
      <div class="confirmation-title">Have you joined our activity before?</div>
      <div class="flex items-center justify-center flex-wrap">
        <a href="https://g.page/r/CSRkANeDMYbaEAE/review" target="_blank" rel="noopener" class="modal-btn yes">Yes</a>
        <a href="https://balidiving.com/cart/cart" class="modal-btn no">No</a>
      </div>
    </div>
  </div>
</div>

<div class="gap"></div>

<script>
(() => {
  const track = document.getElementById('gTrack');
  const dotsWrap = document.getElementById('gDots');
  const cards = Array.from(track.querySelectorAll('.g-card'));

  // Modal
  const addBtn = document.getElementById('reviewAddBtn');
  const confirmationCard = document.getElementById('confirmationCard');
  const confirmationBackdrop = document.getElementById('confirmationBackdrop');
  let hideTimeout;

  function openModal(){
    confirmationCard.classList.add('active');
    clearTimeout(hideTimeout);
    hideTimeout = setTimeout(closeModal, 7000);
  }
  function closeModal(){
    confirmationCard.classList.remove('active');
    clearTimeout(hideTimeout);
  }

  addBtn.addEventListener('click', () => {
    confirmationCard.classList.contains('active') ? closeModal() : openModal();
  });
  confirmationBackdrop.addEventListener('click', closeModal);

  // Paging (Google-like: per viewport)
  let currentPage = 0;
  let autoTimer;

  const getCardsPerView = () => {
    const w = window.innerWidth;
    if (w >= 1024) return 3;
    if (w >= 640) return 2;
    return 1;
  };

  const pageCount = () => Math.max(1, Math.ceil(cards.length / getCardsPerView()));

  function buildDots(){
    dotsWrap.innerHTML = '';
    const total = pageCount();
    for(let i=0;i<total;i++){
      const b = document.createElement('button');
      b.className = 'g-dot' + (i===currentPage ? ' active' : '');
      b.type = 'button';
      b.setAttribute('aria-label', 'Page ' + (i+1));
      b.addEventListener('click', () => goToPage(i, true));
      dotsWrap.appendChild(b);
    }
  }

  function setActiveDot(){
    const dots = Array.from(dotsWrap.querySelectorAll('.g-dot'));
    dots.forEach((d,i)=>d.classList.toggle('active', i===currentPage));
  }

  function goToPage(p, userAction=false){
    const total = pageCount();
    currentPage = (p + total) % total;

    const x = Math.round(track.clientWidth * currentPage);
    track.scrollTo({ left: x, behavior: 'smooth' });
    setActiveDot();

    if(userAction) restartAuto();
  }

  function nextPage(){
    goToPage(currentPage + 1, false);
  }

  function restartAuto(){
    clearInterval(autoTimer);
    autoTimer = setInterval(nextPage, 5200);
  }

  // Sync when user scrolls manually
  let scrollRAF = null;
  track.addEventListener('scroll', () => {
    if (scrollRAF) cancelAnimationFrame(scrollRAF);
    scrollRAF = requestAnimationFrame(() => {
      const w = track.clientWidth || 1;
      const p = Math.round(track.scrollLeft / w);
      const total = pageCount();
      currentPage = Math.min(total-1, Math.max(0, p));
      setActiveDot();
    });
  }, { passive:true });

  // Init + responsive rebuild
  function init(){
    buildDots();
    goToPage(0);
    restartAuto();
  }

  let lastCPV = getCardsPerView();
  window.addEventListener('resize', () => {
    const cpv = getCardsPerView();
    if (cpv !== lastCPV){
      lastCPV = cpv;
      currentPage = 0;
      buildDots();
      goToPage(0);
    }
  });

  init();
})();
</script>
