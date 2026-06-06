<?php
// navbar.php
declare(strict_types=1);

/**
 * Scroll navbar for sections:
 * #snorkeling, #try-diving, #fun-diving, #learn-diving
 * - Smooth scroll
 * - Scroll-spy (auto active)
 * - Mobile menu toggle
 * - Cart badge from localStorage (safe fallback)
 */

$bd_nav_brand_url = $bd_nav_brand_url ?? 'https://balidiving.com';
$bd_nav_logo_src  = $bd_nav_logo_src  ?? 'https://balidiving.com/images/bali-diving-logo.png';
$bd_nav_cart_url  = $bd_nav_cart_url  ?? 'https://balidiving.com/cart/my-booking';

// Sections config (label => id)
$bd_sections = $bd_sections ?? [
  ['label' => 'Snorkeling',  'id' => 'snorkeling'],
  ['label' => 'Try Diving',  'id' => 'try-diving'],
  ['label' => 'Go Diving',  'id' => 'fun-diving'],
  ['label' => 'Learn Diving','id' => 'learn-diving'],
];
?>

<style>
  :root{
    --bd-primary:#3552c8;
    --bd-secondary:#f23d4e;
    --bd-accent:#0070d3;
    --bd-teal:#23a0b4;
    --bd-gold:#eebe35;
    --bd-lightblue:#a2d2fa;
    --bd-navy:#063c7f;
    --bd-white:#ffffff;
  }

  /* Enable smooth scroll globally (JS also covers older cases) */
  html { scroll-behavior: smooth; }

  /* Prevent sticky navbar covering headings when anchor scrolling */
  section, [id] { scroll-margin-top: 90px; }

  .bd-nav-wrap{
    position: sticky;
    top: 0;
    z-index: 50;
    width: 100%;
  }

  .bd-nav{
    background: rgba(6,60,127,.86);
    border-bottom: 1px solid rgba(255,255,255,.14);
    backdrop-filter: blur(10px);
    box-shadow: 0 14px 40px rgba(2,6,23,.28);
  }

  .bd-nav-inner{
    max-width: 1200px;
    margin: 0 auto;
    padding: 10px 14px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
  }

  .bd-brand{
    display:flex;
    align-items:center;
    gap: 10px;
    text-decoration:none;
    color: var(--bd-white);
    min-width: 180px;
  }
  .bd-brand img{
    width: 38px;
    height: 38px;
    border-radius: 10px;
    object-fit: cover;
    box-shadow: 0 10px 30px rgba(0,0,0,.18);
    background: rgba(255,255,255,.12);
  }
  .bd-brand .bd-title{
    display:flex;
    flex-direction:column;
    line-height:1.05;
  }
  .bd-brand .bd-title strong{
    font-size: 14px;
    letter-spacing:.02em;
  }
  .bd-brand .bd-title span{
    font-size: 12px;
    opacity:.9;
  }

  .bd-nav-links{
    display:flex;
    align-items:center;
    gap: 6px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .bd-link{
    position: relative;
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 9px 12px;
    border-radius: 12px;
    color: rgba(255,255,255,.92);
    text-decoration:none;
    font-size: 13px;
    font-weight: 700;
    transition: transform .12s ease, background .12s ease, color .12s ease;
    user-select: none;
  }
  .bd-link:hover{
    background: rgba(255,255,255,.10);
    transform: translateY(-1px);
    color: #fff;
  }
  .bd-link.is-active{
    background: linear-gradient(135deg, rgba(238,190,53,.26), rgba(0,112,211,.22));
    border: 1px solid rgba(255,255,255,.18);
    color: #fff;
  }

  .bd-dot{
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: var(--bd-gold);
    box-shadow: 0 0 0 4px rgba(238,190,53,.14);
  }

  .bd-actions{
    display:flex;
    align-items:center;
    gap: 10px;
    min-width: 180px;
    justify-content: flex-end;
  }

  .bd-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap: 8px;
    padding: 9px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 900;
    text-decoration:none;
    cursor:pointer;
    border: 1px solid rgba(255,255,255,.18);
    background: rgba(255,255,255,.10);
    color: #fff;
    transition: transform .12s ease, background .12s ease, box-shadow .12s ease;
    user-select: none;
  }
  .bd-btn:hover{
    transform: translateY(-1px);
    background: rgba(255,255,255,.14);
    box-shadow: 0 14px 40px rgba(2,6,23,.22);
  }

  .bd-cart{
    position: relative;
    padding-right: 14px;
  }
  .bd-badge{
    position:absolute;
    top: -7px;
    right: -7px;
    min-width: 18px;
    height: 18px;
    padding: 0 6px;
    border-radius: 999px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size: 11px;
    font-weight: 900;
    background: var(--bd-secondary);
    color: #fff;
    border: 2px solid rgba(6,60,127,.95);
    box-shadow: 0 10px 25px rgba(244,63,94,.35);
  }

  .bd-burger{
    display:none;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,.18);
    background: rgba(255,255,255,.10);
    color: #fff;
    cursor:pointer;
    user-select: none;
  }

  .bd-mobile{
    display:none;
    padding: 0 14px 12px;
    max-width: 1200px;
    margin: 0 auto;
  }
  .bd-mobile .bd-mobile-panel{
    margin-top: 10px;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.08);
    backdrop-filter: blur(10px);
    padding: 10px;
    display:flex;
    flex-direction:column;
    gap: 6px;
  }
  .bd-mobile .bd-link, .bd-mobile .bd-btn{
    width: 100%;
    justify-content: flex-start;
  }

  @media (max-width: 920px){
    .bd-nav-links{ display:none; }
    .bd-burger{ display:inline-flex; align-items:center; justify-content:center; }
    .bd-mobile{ display:block; }
    .bd-actions{ min-width: auto; }
    .bd-brand{ min-width:auto; }
  }
</style>

<div class="bd-nav-wrap" id="bdTop">
  <nav class="bd-nav" aria-label="Primary">
    <div class="bd-nav-inner">
      <a class="bd-brand" href="<?= htmlspecialchars($bd_nav_brand_url, ENT_QUOTES, 'UTF-8') ?>">
        <img src="<?= htmlspecialchars($bd_nav_logo_src, ENT_QUOTES, 'UTF-8') ?>" alt="Bali Diving">
        <div class="bd-title">
          <strong>Bali Diving</strong>
          <span>Pick a section &amp; build your plan</span>
        </div>
      </a>

      <div class="bd-nav-links" role="navigation" aria-label="Sections">
        <?php foreach ($bd_sections as $i => $s): ?>
          <a class="bd-link nav-link"
             href="#<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8') ?>"
             data-target="<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8') ?>">
            <?php if ($i === 0): ?><span class="bd-dot" aria-hidden="true"></span><?php endif; ?>
            <?= htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8') ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="bd-actions">
        <a class="bd-btn bd-cart" href="<?= htmlspecialchars($bd_nav_cart_url, ENT_QUOTES, 'UTF-8') ?>" aria-label="Open cart">
          📝 <span>My Booking</span>
          <span class="bd-badge" id="bdCartBadge" style="display:none">0</span>
        </a>

        <button class="bd-burger" type="button" id="bdNavToggle" aria-label="Open menu" aria-expanded="false">
          ☰
        </button>
      </div>
    </div>

    <div class="bd-mobile" id="bdMobileWrap" style="display:none">
      <div class="bd-mobile-panel" aria-label="Mobile Sections">
        <?php foreach ($bd_sections as $s): ?>
          <a class="bd-link nav-link"
             href="#<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8') ?>"
             data-target="<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8') ?>
          </a>
        <?php endforeach; ?>
        <a class="bd-btn" href="<?= htmlspecialchars($bd_nav_cart_url, ENT_QUOTES, 'UTF-8') ?>">🛒 Open Cart</a>
      </div>
    </div>
  </nav>
</div>

<script>
(function(){
  // ===== Mobile toggle =====
  var btn = document.getElementById('bdNavToggle');
  var wrap = document.getElementById('bdMobileWrap');
  if(btn && wrap){
    btn.addEventListener('click', function(){
      var open = wrap.style.display !== 'none';
      wrap.style.display = open ? 'none' : 'block';
      btn.setAttribute('aria-expanded', open ? 'false' : 'true');
    });
  }

  // ===== Smooth scroll (JS fallback + close mobile menu after click) =====
  function closeMobile(){
    if(wrap && btn){
      wrap.style.display = 'none';
      btn.setAttribute('aria-expanded', 'false');
    }
  }

  var links = document.querySelectorAll('.nav-link');
  links.forEach(function(link){
    link.addEventListener('click', function(e){
      var href = link.getAttribute('href') || '';
      if(href.charAt(0) !== '#') return;

      var target = document.querySelector(href);
      if(!target) return;

      e.preventDefault();
      closeMobile();

      try{
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }catch(err){
        // fallback very old browsers
        var top = target.getBoundingClientRect().top + window.pageYOffset - 90;
        window.scrollTo(0, top);
      }

      // update URL hash without jump
      if(history && history.replaceState){
        history.replaceState(null, '', href);
      }
    });
  });

  // ===== Scroll spy (auto active) =====
  function uniqueLinksByTarget(nodeList){
    var map = {};
    nodeList.forEach(function(a){
      var id = a.getAttribute('data-target') || '';
      if(!id) return;
      map[id] = map[id] || [];
      map[id].push(a);
    });
    return map;
  }

  var linkMap = uniqueLinksByTarget(Array.from(links));
  var sectionIds = Object.keys(linkMap);
  var sections = sectionIds.map(function(id){ return document.getElementById(id); }).filter(Boolean);

  function setActive(id){
    sectionIds.forEach(function(sid){
      (linkMap[sid] || []).forEach(function(a){
        a.classList.toggle('is-active', sid === id);
      });
    });
  }

  function onScroll(){
    var current = null;

    for(var i=0;i<sections.length;i++){
      var rect = sections[i].getBoundingClientRect();
      // "120px from top" feels natural with sticky nav
      if(rect.top <= 120 && rect.bottom >= 120){
        current = sections[i].id;
        break;
      }
    }

    // If none matched, pick first section above fold
    if(!current){
      for(var j=sections.length-1;j>=0;j--){
        var r = sections[j].getBoundingClientRect();
        if(r.top <= 120){
          current = sections[j].id;
          break;
        }
      }
    }

    if(current) setActive(current);
  }

  window.addEventListener('scroll', onScroll, { passive:true });
  window.addEventListener('resize', onScroll);
  onScroll();

  // ===== Cart badge (safe fallback) =====
  function countFromCartObject(obj){
    try{
      if(!obj) return 0;
      if(Array.isArray(obj)){
        return obj.reduce(function(a,x){
          return a + (Number(x.qty || x.quantity || 1) || 1);
        }, 0);
      }
      if(typeof obj === 'object'){
        var total = 0;
        Object.keys(obj).forEach(function(k){
          var it = obj[k];
          if(it && typeof it === 'object'){
            total += (Number(it.qty || it.quantity || 1) || 1);
          }else{
            total += 1;
          }
        });
        return total;
      }
      return 0;
    }catch(e){ return 0; }
  }

  function getCartCount(){
    var keys = ['BD_CART', 'bd_cart', 'cart', 'booking_cart', 'BALIDIVING_CART'];
    for(var i=0;i<keys.length;i++){
      var raw = null;
      try { raw = localStorage.getItem(keys[i]); } catch(e) { raw = null; }
      if(!raw) continue;

      try{
        var parsed = JSON.parse(raw);
        var n = countFromCartObject(parsed);
        if(n > 0) return n;
      }catch(e){}
    }
    return 0;
  }

  function paintBadge(){
    var badge = document.getElementById('bdCartBadge');
    if(!badge) return;
    var n = getCartCount();
    if(n > 0){
      badge.textContent = String(n);
      badge.style.display = 'flex';
    }else{
      badge.style.display = 'none';
    }
  }

  paintBadge();
  window.addEventListener('storage', paintBadge);
  setInterval(paintBadge, 1500);
})();
</script>
