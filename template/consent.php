<?php
// consent.php — lightweight cookie consent (GDPR/UK/ePrivacy-friendly baseline)
// Usage: include this file in your <head> or just before </body>
// Then use JS hooks: window.consent.has('analytics') / has('marketing') etc.

declare(strict_types=1);

$consentCookieName = 'site_consent_v1';
$consentMaxAgeDays = 180; // 6 months typical
$policyUrl = '/privacy-policy'; // change to your page
$siteName = $_SERVER['HTTP_HOST'] ?? 'this site';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<style>
/* Minimal, non-blocking, small footprint */
#cc-banner{position:fixed;left:16px;right:16px;bottom:16px;z-index:99999;max-width:960px;margin:0 auto;
  font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#0b1220;color:#fff;
  border:1px solid rgba(255,255,255,.12);border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.35);
  padding:14px 14px 12px;display:none}
#cc-banner p{margin:0 0 10px;font-size:14px;line-height:1.45;color:rgba(255,255,255,.92)}
#cc-banner a{color:#a2d2fa;text-decoration:none}
#cc-banner a:hover{text-decoration:underline}
#cc-actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
.cc-btn{border:0;border-radius:10px;padding:10px 12px;font-size:13px;cursor:pointer;line-height:1}
.cc-btn.primary{background:#3552c8;color:#fff}
.cc-btn.ghost{background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.12)}
.cc-btn.danger{background:#f23d4e;color:#fff}
#cc-modal{position:fixed;inset:0;z-index:100000;display:none;align-items:center;justify-content:center;
  background:rgba(0,0,0,.55);padding:18px}
#cc-card{width:min(720px,100%);background:#0b1220;color:#fff;border:1px solid rgba(255,255,255,.12);
  border-radius:16px;box-shadow:0 12px 40px rgba(0,0,0,.45);padding:16px}
#cc-card h3{margin:0 0 10px;font-size:16px}
#cc-card label{display:flex;gap:10px;align-items:flex-start;margin:10px 0;padding:10px;border-radius:12px;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.10)}
#cc-card small{display:block;color:rgba(255,255,255,.75);margin-top:2px}
#cc-card input[type="checkbox"]{margin-top:2px;transform:scale(1.1)}
#cc-card .row{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
#cc-card .muted{color:rgba(255,255,255,.75);font-size:13px;margin-top:8px}
</style>

<div id="cc-banner" role="dialog" aria-live="polite" aria-label="Cookie consent">
  <p>
    We use cookies to run <b><?=h($siteName)?></b> and (optionally) measure traffic and improve marketing.
    You can accept, reject non-essential cookies, or customize preferences.
    <a href="<?=h($policyUrl)?>">Privacy Policy</a>.
  </p>
  <div id="cc-actions">
    <button class="cc-btn danger" id="cc-reject">Reject non-essential</button>
    <button class="cc-btn ghost" id="cc-customize">Customize</button>
    <button class="cc-btn primary" id="cc-accept">Accept all</button>
  </div>
</div>

<div id="cc-modal" aria-hidden="true">
  <div id="cc-card" role="dialog" aria-label="Cookie preferences">
    <h3>Cookie Preferences</h3>

    <label>
      <input type="checkbox" checked disabled>
      <div>
        <b>Necessary</b>
        <small>Required for basic site functions (security, session, language).</small>
      </div>
    </label>

    <label>
      <input id="cc-analytics" type="checkbox">
      <div>
        <b>Analytics</b>
        <small>Helps us understand traffic and improve the website.</small>
      </div>
    </label>

    <label>
      <input id="cc-marketing" type="checkbox">
      <div>
        <b>Marketing</b>
        <small>Helps show relevant offers/ads and measure campaigns.</small>
      </div>
    </label>

    <div class="row">
      <button class="cc-btn ghost" id="cc-close">Close</button>
      <button class="cc-btn primary" id="cc-save">Save preferences</button>
    </div>
    <div class="muted">
      Tip: You can change this anytime via <a href="#" id="cc-open-again">cookie settings</a>.
    </div>
  </div>
</div>

<script>
(function(){
  "use strict";

  const COOKIE_NAME = "<?=h($consentCookieName)?>";
  const MAX_AGE = <?= (int)($consentMaxAgeDays*24*60*60) ?>;

  // ----- helpers -----
  const $ = (id)=>document.getElementById(id);
  const banner = $("cc-banner");
  const modal  = $("cc-modal");

  function setCookie(name, value, maxAge){
    // SameSite=Lax works broadly; Secure only when HTTPS
    const secure = (location.protocol === "https:") ? "; Secure" : "";
    document.cookie = name + "=" + encodeURIComponent(value)
      + "; Max-Age=" + maxAge
      + "; Path=/; SameSite=Lax" + secure;
  }

  function getCookie(name){
    const m = document.cookie.match(new RegExp("(^| )"+name.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')+"=([^;]+)"));
    return m ? decodeURIComponent(m[2]) : "";
  }

  function parseConsent(){
    try{
      const raw = getCookie(COOKIE_NAME);
      if(!raw) return null;
      const obj = JSON.parse(raw);
      if(!obj || typeof obj !== "object") return null;
      return obj;
    }catch(e){
      return null;
    }
  }

  function saveConsent(consent){
    // consent: {v:1, ts:..., necessary:true, analytics:false, marketing:false}
    consent.v = 1;
    consent.ts = Date.now();
    setCookie(COOKIE_NAME, JSON.stringify(consent), MAX_AGE);
    window.consent = window.consent || {};
    window.consent.state = consent;
    window.dispatchEvent(new CustomEvent("consent:updated", {detail: consent}));
  }

  // public API for gating scripts
  window.consent = window.consent || {};
  window.consent.state = parseConsent() || null;
  window.consent.has = function(cat){
    const s = window.consent.state;
    if(!s) return false;
    if(cat === "necessary") return true;
    return !!s[cat];
  };
  window.consent.open = function(){
    openModalFromState();
    modal.style.display = "flex";
    modal.setAttribute("aria-hidden","false");
  };

  function showBanner(){
    banner.style.display = "block";
  }
  function hideBanner(){
    banner.style.display = "none";
  }
  function closeModal(){
    modal.style.display = "none";
    modal.setAttribute("aria-hidden","true");
  }

  function openModalFromState(){
    const s = window.consent.state || {analytics:false, marketing:false};
    $("cc-analytics").checked = !!s.analytics;
    $("cc-marketing").checked = !!s.marketing;
  }

  // ----- actions -----
  $("cc-accept").addEventListener("click", function(){
    saveConsent({necessary:true, analytics:true, marketing:true});
    hideBanner();
  });

  $("cc-reject").addEventListener("click", function(){
    saveConsent({necessary:true, analytics:false, marketing:false});
    hideBanner();
  });

  $("cc-customize").addEventListener("click", function(){
    openModalFromState();
    modal.style.display = "flex";
    modal.setAttribute("aria-hidden","false");
  });

  $("cc-close").addEventListener("click", closeModal);

  $("cc-save").addEventListener("click", function(){
    saveConsent({
      necessary:true,
      analytics: $("cc-analytics").checked,
      marketing: $("cc-marketing").checked
    });
    hideBanner();
    closeModal();
  });

  $("cc-open-again").addEventListener("click", function(e){
    e.preventDefault();
    window.consent.open();
  });

  // click outside closes modal
  modal.addEventListener("click", function(e){
    if(e.target === modal) closeModal();
  });

  // initial
  if(!window.consent.state){
    // No choice yet => show banner
    showBanner();
  }

})();
</script>

<!-- Optional: add a small link somewhere in footer:
<a href="#" onclick="window.consent && window.consent.open(); return false;">Cookie settings</a>
-->
<script>
window.addEventListener("consent:updated", function(){
  // Analytics example
  if(window.consent.has("analytics") && !window.__ga_loaded){
    window.__ga_loaded = true;

    // Load GA only after consent
    var s=document.createElement("script");
    s.async=true;
    s.src="https://www.googletagmanager.com/gtag/js?id=AW-17535474834";
    document.head.appendChild(s);

    window.dataLayer=window.dataLayer||[];
    function gtag(){dataLayer.push(arguments);}
    window.gtag=gtag;
    gtag('js', new Date());
    gtag('config','AW-17535474834');
  }

  // Marketing example (Meta Pixel) – load only after marketing consent
  if(window.consent.has("marketing") && !window.__fb_loaded){
    window.__fb_loaded = true;
    // inject your pixel loader here
  }
});

// Also handle if consent already exists on page load:
document.dispatchEvent(new Event("consent:updated"));
</script>

