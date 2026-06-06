<?php
$page = 'recommendations';
include('../template/start.php');
include('../template/auto_article_creator.php');
?>

<?php if (!empty($newArticleInfo)): ?>
<!-- New Article Banner -->
<div id="new-article-banner" style="background:#0f172a;color:#fff;padding:14px 24px;display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;font-size:14px;font-family:ui-sans-serif,system-ui,sans-serif;">
  <span>&#127381; New article created about &ldquo;<strong><?php echo htmlspecialchars($newArticleInfo['keyword'], ENT_QUOTES, 'UTF-8'); ?></strong>&rdquo;</span>
  <a href="<?php echo htmlspecialchars($newArticleInfo['url'], ENT_QUOTES, 'UTF-8'); ?>" style="display:inline-flex;align-items:center;gap:6px;background:#0891b2;color:#fff;text-decoration:none;padding:7px 16px;border-radius:999px;font-weight:700;font-size:13px;transition:background .2s;">Another article about <?php echo htmlspecialchars($newArticleInfo['keyword'], ENT_QUOTES, 'UTF-8'); ?> &rarr;</a>
</div>
<?php endif; ?>

<?php
// Only show query CTA section if there's a ?q= parameter
if (isset($_GET['q']) && trim($_GET['q']) !== ''):
  ?>
  <!-- =========================
SECTION: Query-aware CTA (reads ?q= )
========================= -->
  <section class="relative w-full bg-white py-14 md:py-18 overflow-hidden" id="query-cta">
    <style>
      /* scoped styles */
      #query-cta .wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 24px;
      }

      #query-cta .card {
        position: relative;
        border-radius: 24px;
        border: 1px solid rgba(15, 23, 42, .10);
        background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(248, 250, 252, 1) 100%);
        box-shadow: 0 20px 60px rgba(2, 6, 23, .08);
        overflow: hidden;
      }

      #query-cta .card::before {
        content: "";
        position: absolute;
        inset: -40%;
        background:
          radial-gradient(800px circle at 15% 20%, rgba(34, 211, 238, .22), transparent 45%),
          radial-gradient(700px circle at 85% 35%, rgba(59, 130, 246, .18), transparent 46%),
          radial-gradient(600px circle at 55% 85%, rgba(56, 189, 248, .14), transparent 52%);
        pointer-events: none;
      }

      #query-cta .inner {
        position: relative;
        padding: 28px;
      }

      @media (min-width:768px) {
        #query-cta .inner {
          padding: 38px;
        }
      }

      #query-cta .badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(15, 23, 42, .06);
        border: 1px solid rgba(15, 23, 42, .08);
        color: #0f172a;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: .2px;
      }

      #query-cta .badge .dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: radial-gradient(circle at 30% 30%, #22d3ee, #3b82f6);
        box-shadow: 0 0 0 4px rgba(34, 211, 238, .14);
      }

      #query-cta h2 {
        margin-top: 14px;
        font-size: clamp(22px, 3.2vw, 34px);
        line-height: 1.15;
        letter-spacing: -.02em;
        color: #0f172a;
        font-weight: 800;
      }

      #query-cta p {
        margin-top: 10px;
        color: rgba(15, 23, 42, .70);
        font-size: 15px;
        line-height: 1.7;
        max-width: 70ch;
      }

      #query-cta .actions {
        margin-top: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
      }

      #query-cta .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        border: 1px solid transparent;
        transition: transform .25s ease, box-shadow .25s ease, background .25s ease, color .25s ease, border-color .25s ease;
        will-change: transform;
        white-space: nowrap;
      }

      #query-cta .btn:active {
        transform: translateY(1px) scale(.99);
      }

      #query-cta .btn-primary {
        background: #0f172a;
        color: #fff;
        box-shadow: 0 14px 34px rgba(2, 6, 23, .18);
      }

      #query-cta .btn-primary:hover {
        background: #0891b2;
        box-shadow: 0 18px 44px rgba(8, 145, 178, .26);
        transform: translateY(-1px);
      }

      #query-cta .btn-soft {
        background: rgba(15, 23, 42, .06);
        color: #0f172a;
        border-color: rgba(15, 23, 42, .10);
      }

      #query-cta .btn-soft:hover {
        background: rgba(8, 145, 178, .10);
        border-color: rgba(8, 145, 178, .22);
        transform: translateY(-1px);
      }

      @keyframes waPulse {
        0% {
          box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.5);
        }

        70% {
          box-shadow: 0 0 0 14px rgba(37, 211, 102, 0);
        }

        100% {
          box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
        }
      }

      /* your WA pill (kept exactly, just scoped for spacing) */
      #query-cta .explore-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 20px;
        height: 46px;
        border-radius: 999px;
        background: #25D366;
        border: 1px solid #1eaa51;
        color: #fff;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
        box-shadow: 0 10px 24px rgba(37, 211, 102, .25);
        animation: waPulse 2s infinite;
      }

      #query-cta .explore-pill:hover {
        background: #1ebd5a;
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(37, 211, 102, .4);
      }

      #query-cta .explore-pill i {
        font-size: 22px;
        line-height: 1;
      }

      #query-cta .divider {
        margin-top: 18px;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(15, 23, 42, .10), transparent);
      }

      #query-cta .hint {
        margin-top: 14px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        color: rgba(15, 23, 42, .62);
        font-size: 13px;
      }

      #query-cta .hint a {
        color: #0891b2;
        text-decoration: none;
        font-weight: 700;
      }

      #query-cta .hint a:hover {
        text-decoration: underline;
      }
    </style>

    <div class="wrap">
      <div class="card">
        <div class="inner">
          <span class="badge"><span class="dot"></span><span id="qBadgeText">Your interest</span></span>

          <h2 id="qTitle">Looking for something in Bali?</h2>
          <p id="qDesc">
            Tell us what you want to do underwater&mdash;snorkeling, scuba, freedive&mdash;then we'll guide you to the
            best spot and
            the best time.
          </p>

          <div class="actions">
            <a class="btn btn-primary" href="https://balidiving.com/pricelist">
              Booking & Price List
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14" />
                <path d="M13 5l7 7-7 7" />
              </svg>
            </a>

            <a class="btn btn-soft" href="#articles">
              Read related articles
            </a>

            <!-- ✓ Keep your WhatsApp anchor exactly as you gave -->
            <a class="explore-pill"
              href="https://wa.me/6287861190174?text=Hi%20Bali%20Diving%20%0aI%20found%20you%20from%20your%20website."
              aria-label="WhatsApp" target="_blank" rel="noopener" title="Chat via WhatsApp">
              <i class="fa-brands fa-whatsapp"></i>
            </a>
          </div>

          <div class="divider"></div>

          <div class="hint">
            <span id="qHint">Tip: type a more specific keyword in your search bar.</span>
            <span>&bull;</span>
            <a href="https://balidiving.com/cart/my-booking">Check your booking</a>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function () {
        const params = new URLSearchParams(window.location.search);
        let q = (params.get('q') || '').trim();

        q = q.replace(/\+/g, ' ').replace(/\s+/g, ' ').slice(0, 40);
        const qLower = q.toLowerCase();

        const badge = document.getElementById('qBadgeText');
        const title = document.getElementById('qTitle');
        const desc = document.getElementById('qDesc');
        const hint = document.getElementById('qHint');
        const waBtn = document.querySelector('.explore-pill');

        let waMessage = "Hi Bali Diving, I found you from your website and need some recommendations.";

        if (!q) {
          badge.textContent = "Quick guide";
          title.textContent = "What do you want to explore in Bali?";
          desc.textContent = "Type a keyword in the URL like ?q=snorkel or use the search. We'll point you to the best experience, or just hit our WhatsApp!";
          hint.textContent = "Example: ?q=snorkel, ?q=scuba, ?q=nusa penida";
        } else {
          badge.textContent = `You searched: ${q}`;
          title.textContent = `About "${q}" — looking for recommendations?`;
          desc.textContent = `We can recommend the best location, time, and package for ${q}. Chat with our team on WhatsApp for a fast & personalized suggestion!`;
          hint.textContent = `Try a more specific keyword: "${q} sanur", "${q} nusa penida", "${q} beginner".`;

          waMessage = `Hi Bali Diving, I found you from your website. I am interested in exploring "${q}". Could you give me some recommendations?`;

          const flavors = [
            { keys: ['snorkel', 'snorkeling'], t: 'Snorkeling in Bali — ready to pick your spot?', d: 'We\'ll match you to calm water and best visibility. Drop us a chat on WhatsApp for fast recommendations!' },
            { keys: ['scuba', 'dive', 'diving', 'padi', 'fun dive'], t: 'Scuba diving — choose depth, safety, and vibes', d: 'Tell us your level (beginner / certified) via WhatsApp and we\'ll suggest the best sites and schedules.' },
            { keys: ['nusa', 'penida', 'manta'], t: 'Nusa Penida & Manta rays — timing matters', d: 'We can help you choose the best day based on sea conditions. WhatsApp us to check the safest window!' },
            { keys: ['tulamben', 'wreck', 'liberty'], t: 'Tulamben Wreck — iconic, photogenic, unforgettable', d: 'Perfect for certified divers. Want sunrise slots? Chat with us on WhatsApp to secure your schedule.' },
            { keys: ['amed'], t: 'Amed reefs — chill water, colorful life', d: 'Great for relaxed divers and snorkelers. Need an easy and beautiful itinerary? Ask us on WhatsApp!' },
          ];

          for (const f of flavors) {
            if (f.keys.some(k => qLower.includes(k))) {
              title.textContent = f.t;
              desc.textContent = f.d;
              break;
            }
          }
        }

        if (waBtn) {
          waBtn.href = `https://wa.me/6287861190174?text=${encodeURIComponent(waMessage)}`;
        }
      })();
    </script>
  </section>
<?php endif; ?>

<?php
/**
 * Simple PHP Scanner Ã¢â‚¬â€ TikTok Style Cards 3 per row
 * Enhanced aesthetic header (Sui Generis style)
 * by Subhi.me
 *
 * FIXES:
 * - Pagination per 9 items (?page=1,2,..) + preserves ?q=
 * - No PHP 7.4 arrow functions (compat safe)
 * - Guards: empty thumbnail array
 * - Faster load: lazy images + async decoding + fetchpriority on first visible
 */

$rootDir = __DIR__;
$ignoreFiles = array(basename(__FILE__), 'index.php', 'article-creator.php', 'fix_chars.php');

$thumbnails = array(
  "https://balidiving.com/images/thumbnails/1-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/10-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/11-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/12-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/13-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/14-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/15-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/16-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/17-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/18-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/19-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/2-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/20-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/21-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/22-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/23-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/24-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/25-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/26-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/27-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/28-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/29-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/3-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/30-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/4-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/5-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/6-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/7-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/8-bali-diving.jpg",
  "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
  "https://balidiving.com/images/thumbnails/9-bali-diving.jpg"
);

function pickThumb($arr, $path)
{
  if (!is_array($arr) || count($arr) === 0) {
    return "https://balidiving.com/images/thumbnails/1-bali-diving.jpg";
  }
  $idx = crc32((string) $path) % count($arr);
  return $arr[$idx];
}

function cleanName($name)
{
  $noExt = preg_replace('/\.php$/i', '', (string) $name);
  $clean = preg_replace('/[-_()]+/', ' ', $noExt);
  return ucwords(trim($clean));
}

$files = glob($rootDir . '/*.php');
if (!is_array($files))
  $files = array();

/* filter ignore files (PHP 7.0+ safe) */
$filtered = array();
foreach ($files as $f) {
  $base = basename($f);
  if (!in_array($base, $ignoreFiles, true)) {
    $filtered[] = $f;
  }
}
$files = $filtered;

/* sort by modified desc */
usort($files, function ($a, $b) {
  $ta = @filemtime($a);
  if ($ta === false)
    $ta = 0;
  $tb = @filemtime($b);
  if ($tb === false)
    $tb = 0;
  if ($ta === $tb)
    return 0;
  return ($ta > $tb) ? -1 : 1;
});

/* pagination per 9 */
$perPage = 9;
$total = count($files);
$totalPages = ($total > 0) ? (int) ceil($total / $perPage) : 1;

$page = 1;
if (isset($_GET['page'])) {
  $page = (int) $_GET['page'];
}
if ($page < 1)
  $page = 1;
if ($page > $totalPages)
  $page = $totalPages;

$offset = ($page - 1) * $perPage;
$pagedFiles = array_slice($files, $offset, $perPage);

/* keep q in pagination links */
$qParam = '';
if (isset($_GET['q'])) {
  $qParam = trim((string) $_GET['q']);
}
function buildPageUrl($pageNum, $qParam)
{
  $params = array('page' => $pageNum);
  if ($qParam !== '')
    $params['q'] = $qParam;
  return '?' . http_build_query($params);
}
?>

<style>
  /* lighter font strategy: prefer system, no extra @import (faster) */
  body {
    font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", sans-serif;
    background-color: #f8fafc;
  }

  /* Sui Generis font jika sudah di-load di sistem */
  h1 {
    font-family: 'Sui Generis', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", sans-serif;
    letter-spacing: 1px;
    background: linear-gradient(135deg, #0a2540, #3552c8 40%, #00bcd4 90%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
    font-size: clamp(2rem, 6vw, 3.8rem);
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 3rem;
    position: relative;
  }

  h1::after {
    content: '';
    display: block;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #3552c8, #00bcd4);
    margin: 1rem auto 0;
    border-radius: 2px;
  }

  /* ===== IMPROVED: More Natural Card Styling ===== */
  .ratio-9-16 {
    position: relative;
    width: 100%;
    padding-bottom: 177.77%;
    overflow: hidden;
    border-radius: 1.5rem;
    isolation: isolate;
    background: linear-gradient(135deg, #001a2b, #002a3f);
    box-shadow:
      0 4px 12px rgba(0, 0, 0, 0.08),
      0 1px 3px rgba(0, 0, 0, 0.06);
    transition: box-shadow 0.4s ease, transform 0.3s ease;
  }

  .ratio-9-16:hover {
    box-shadow:
      0 12px 32px rgba(0, 0, 0, 0.15),
      0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-4px);
  }

  .ratio-9-16 img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.02);
    will-change: transform, filter;
    animation: bd-kenburns 16s ease-in-out infinite alternate;
    filter: saturate(1.05) contrast(1.04) brightness(0.95);
    transition: transform 0.6s ease, filter 0.4s ease;
  }

  /* subtle variations */
  a:nth-child(3n+1) .ratio-9-16 img {
    animation-duration: 15s;
    animation-delay: -2s;
  }

  a:nth-child(3n+2) .ratio-9-16 img {
    animation-duration: 17s;
    animation-delay: -6s;
  }

  a:nth-child(3n+3) .ratio-9-16 img {
    animation-duration: 19s;
    animation-delay: -9s;
  }

  .ratio-9-16:hover img {
    filter: saturate(1.12) contrast(1.08) brightness(1.0);
    transform: scale(1.05);
  }

  /* ===== IMPROVED: Better Text Readability ===== */
  .center-name {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding: 2rem 1.5rem;
    font-family: 'Sui Generis', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", sans-serif;
    font-weight: 700;
    font-size: 1.35rem;
    line-height: 1.3;
    color: #ffffff;
    text-align: center;
    /* IMPROVED: Multiple text shadows for better readability */
    text-shadow:
      0 1px 2px rgba(0, 0, 0, 0.3),
      0 2px 8px rgba(0, 0, 0, 0.5),
      0 4px 16px rgba(0, 0, 0, 0.4),
      0 0 40px rgba(0, 0, 0, 0.3);
    z-index: 10;
    transition: all 0.4s ease;
  }

  .ratio-9-16:hover .center-name {
    padding-bottom: 2.5rem;
    text-shadow:
      0 2px 4px rgba(0, 0, 0, 0.4),
      0 4px 12px rgba(0, 0, 0, 0.6),
      0 6px 20px rgba(0, 0, 0, 0.5),
      0 0 50px rgba(0, 0, 0, 0.4);
  }

  /* ===== IMPROVED: Strong Gradient Overlay for Text Readability ===== */
  .ratio-9-16::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 8;
    border-radius: 1.5rem;
    /* IMPROVED: Stronger gradient overlay */
    background:
      linear-gradient(to top,
        rgba(0, 0, 0, 0.85) 0%,
        rgba(0, 0, 0, 0.65) 25%,
        rgba(0, 0, 0, 0.35) 50%,
        rgba(0, 0, 0, 0.15) 70%,
        transparent 100%),
      radial-gradient(ellipse at bottom center,
        rgba(0, 0, 0, 0.6) 0%,
        transparent 70%);
    transition: opacity 0.4s ease;
  }

  .ratio-9-16:hover::before {
    background:
      linear-gradient(to top,
        rgba(0, 0, 0, 0.90) 0%,
        rgba(0, 0, 0, 0.70) 30%,
        rgba(0, 0, 0, 0.40) 55%,
        rgba(0, 0, 0, 0.20) 75%,
        transparent 100%),
      radial-gradient(ellipse at bottom center,
        rgba(0, 0, 0, 0.7) 0%,
        transparent 70%);
  }

  /* Subtle light reflection */
  .ratio-9-16::after {
    content: "";
    position: absolute;
    top: -10%;
    left: -10%;
    right: -10%;
    height: 40%;
    pointer-events: none;
    z-index: 5;
    border-radius: 1.5rem;
    opacity: .15;
    background:
      radial-gradient(ellipse at 30% 20%, rgba(255, 255, 255, .35), transparent 50%),
      radial-gradient(ellipse at 70% 30%, rgba(255, 255, 255, .25), transparent 45%);
    filter: blur(1px);
    mix-blend-mode: overlay;
    animation: bd-light-shimmer 8s ease-in-out infinite alternate;
  }

  /* Simplified animations for more natural feel */
  @keyframes bd-kenburns {
    0% {
      transform: scale(1.02) translate3d(-1%, -0.8%, 0);
    }

    50% {
      transform: scale(1.06) translate3d(0.8%, -0.4%, 0);
    }

    100% {
      transform: scale(1.04) translate3d(0.4%, 0.8%, 0);
    }
  }

  @keyframes bd-light-shimmer {
    0% {
      opacity: .12;
      transform: translateX(-2%);
    }

    50% {
      opacity: .18;
    }

    100% {
      opacity: .15;
      transform: translateX(2%);
    }
  }

  @media (prefers-reduced-motion: reduce) {

    .ratio-9-16 img,
    .ratio-9-16::after,
    .center-name {
      animation: none !important;
      transform: none !important;
    }
  }

  /* Pagination UI */
  .bd-pager {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 26px;
  }

  .bd-pager a,
  .bd-pager span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 14px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    border: 1px solid rgba(15, 23, 42, .12);
    background: rgba(255, 255, 255, .85);
    color: #0f172a;
    box-shadow: 0 10px 24px rgba(2, 6, 23, .06);
  }

  .bd-pager a:hover {
    border-color: rgba(8, 145, 178, .30);
    background: rgba(8, 145, 178, .08);
  }

  .bd-pager .active {
    background: #0f172a;
    border-color: #0f172a;
    color: #fff;
  }

  .bd-pager .muted {
    opacity: .55;
    cursor: not-allowed;
  }
</style>

<div style="height:50px;"></div>

<section class="bg-slate-100 min-h-screen py-10" id="articles">
  <div class="max-w-6xl mx-auto text-center">
    <h1>Recommendations</h1>
    <p class="text-slate-500 max-w-2xl mx-auto mt-3 text-sm md:text-base leading-relaxed">
      Explore Underwater activities.
    </p>

    <div class="mt-4 text-slate-500 text-xs md:text-sm">
      Showing <strong class="text-slate-700"><?php echo (int) $total; ?></strong> articles &bull;
      Page <strong class="text-slate-700"><?php echo (int) $page; ?></strong> of <strong
        class="text-slate-700"><?php echo (int) $totalPages; ?></strong>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-6 mt-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <?php
      $i = 0;
      foreach ($pagedFiles as $f):
        $i++;
        $name = cleanName(basename($f));
        $thumb = pickThumb($thumbnails, $f);
        $url = basename($f);

        $isFirst = ($i === 1);
        $fetchPriority = $isFirst ? 'high' : 'low';
        $loading = $isFirst ? 'eager' : 'lazy';
        ?>
        <a href="<?php echo htmlspecialchars($url); ?>"
          class="block bg-white rounded-2xl shadow hover:shadow-2xl transition overflow-hidden">
          <div class="ratio-9-16">
            <img src="<?php echo htmlspecialchars($thumb); ?>" alt="<?php echo htmlspecialchars($name); ?>"
              loading="<?php echo $loading; ?>" decoding="async" fetchpriority="<?php echo $fetchPriority; ?>">
            <div class="center-name"><?php echo htmlspecialchars($name); ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="bd-pager">
      <?php
      $prev = $page - 1;
      $next = $page + 1;

      if ($page > 1) {
        echo '<a href="' . htmlspecialchars(buildPageUrl($prev, $qParam)) . '#articles">&larr; Prev</a>';
      } else {
        echo '<span class="muted">&larr; Prev</span>';
      }

      /* compact pager */
      $window = 2;
      $start = max(1, $page - $window);
      $end = min($totalPages, $page + $window);

      if ($start > 1) {
        echo '<a href="' . htmlspecialchars(buildPageUrl(1, $qParam)) . '#articles">1</a>';
        if ($start > 2)
          echo '<span class="muted">&hellip;</span>';
      }

      for ($p = $start; $p <= $end; $p++) {
        if ($p == $page) {
          echo '<span class="active">' . (int) $p . '</span>';
        } else {
          echo '<a href="' . htmlspecialchars(buildPageUrl($p, $qParam)) . '#articles">' . (int) $p . '</a>';
        }
      }

      if ($end < $totalPages) {
        if ($end < $totalPages - 1)
          echo '<span class="muted">&hellip;</span>';
        echo '<a href="' . htmlspecialchars(buildPageUrl($totalPages, $qParam)) . '#articles">' . (int) $totalPages . '</a>';
      }

      if ($page < $totalPages) {
        echo '<a href="' . htmlspecialchars(buildPageUrl($next, $qParam)) . '#articles">Next &rarr;</a>';
      } else {
        echo '<span class="muted">Next &rarr;</span>';
      }
      ?>
    </div>
  </div>
</section>

<section class="relative w-full py-24 bg-white overflow-hidden">
  <!-- subtle decor -->
  <div class="absolute inset-0 pointer-events-none">
    <div class="absolute -top-32 -right-32 w-[420px] h-[420px] bg-cyan-100 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -left-32 w-[420px] h-[420px] bg-blue-100 rounded-full blur-3xl"></div>
  </div>

  <div class="relative max-w-4xl mx-auto px-6 text-center">
    <span
      class="inline-block mb-4 px-4 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-600 tracking-wide">
      Availability Open
    </span>

    <h2 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-5 leading-tight">
      Date Available,<br class="sm:hidden">
      <span class="text-cyan-600">Booking Now</span>
    </h2>

    <p class="text-base md:text-lg text-slate-600 mb-12 leading-relaxed">
      Real-time slots. Clear schedule. No overbooking.<br class="hidden sm:block">
      Just choose your date and dive.
    </p>

    <a href="https://balidiving.com/cart/my-booking"
      class="group inline-flex items-center justify-center gap-3 px-10 py-4 rounded-full bg-slate-900 text-white font-semibold hover:bg-cyan-600 transition-all duration-300 shadow-lg shadow-slate-900/20">
      Book My Date
      <svg xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
      </svg>
    </a>
  </div>
</section>

<?php include('../template/end.php'); ?>
// Cache buster