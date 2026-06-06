<?php
// fun-diving-settings.php
declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');

/*
 * DB TARGET:
 *  - TABLE  : bd_global_settings
 *  - COLUMNS: id (PK=1), cutoff_time, rate_mode, usd_to_idr, global_template, updated_at
 *
 * JSON STRUCT di kolom bd_global_settings.global_template:
 *
 * {
 *   "pages": {
 *     "fun_diving": {
 *       "hero_title": "...",
 *       "hero_subtitle": "...",
 *       "badge_left": "...",
 *       "badge_right": "...",
 *       "article_title": "...",
 *       "article_intro_html": "<p>...</p>",
 *       "accordion_title": "...",
 *       "accordion_body_html": "<p>...</p>",
 *       "locations_title": "...",
 *       "hero_images": ["https://...jpg","https://...jpg"],
 *       "include_items": ["item 1","item 2"],
 *       "exclude_items": ["item A","item B"]
 *     }
 *   }
 * }
 */

include('../template/database/main.php'); // harus define $pdo

$activityKey = 'fun_diving';

/* ---------- LOCAL JSON HEADERS (tidak bergantung ke main.php) ---------- */
function settings_json_headers(): void {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
}

/* ---------- HELPER: AMBIL & SIMPAN global_template ---------- */

function settings_load_global_template(PDO $pdo): array {
    $st = $pdo->query("SELECT global_template FROM bd_global_settings WHERE id = 1 LIMIT 1");
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        // kalau row belum ada, anggap template kosong
        return [];
    }
    if ($row['global_template'] === null || $row['global_template'] === '') {
        return [];
    }
    $data = json_decode($row['global_template'], true);
    return is_array($data) ? $data : [];
}

function settings_save_global_template(PDO $pdo, array $tpl): void {
    $json = json_encode($tpl, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Pastikan id=1 selalu ada: pakai INSERT ... ON DUPLICATE KEY UPDATE
    $sql = "
        INSERT INTO bd_global_settings (id, cutoff_time, rate_mode, usd_to_idr, global_template, updated_at)
        VALUES (1, '13:00', 'manual', 16000, :j, NOW())
        ON DUPLICATE KEY UPDATE
          global_template = VALUES(global_template),
          updated_at      = VALUES(updated_at)
    ";
    $st = $pdo->prepare($sql);
    $st->execute([':j' => $json]);
}

/* ---------- API: LOAD / SAVE (AJAX) ---------- */

$action = $_GET['action'] ?? '';

if ($action === 'load') {
    settings_json_headers();
    try {
        $tpl  = settings_load_global_template($pdo);
        $page = $tpl['pages'][$activityKey] ?? [];

        $resp = [
            'hero_title'          => (string)($page['hero_title'] ?? ''),
            'hero_subtitle'       => (string)($page['hero_subtitle'] ?? ''),
            'badge_left'          => (string)($page['badge_left'] ?? ''),
            'badge_right'         => (string)($page['badge_right'] ?? ''),
            'article_title'       => (string)($page['article_title'] ?? ''),
            'article_intro_html'  => (string)($page['article_intro_html'] ?? ''),
            'accordion_title'     => (string)($page['accordion_title'] ?? ''),
            'accordion_body_html' => (string)($page['accordion_body_html'] ?? ''),
            'locations_title'     => (string)($page['locations_title'] ?? ''),
            'hero_images'         => [],
            'include_items'       => [],
            'exclude_items'       => [],
        ];

        if (isset($page['hero_images']) && is_array($page['hero_images'])) {
            $resp['hero_images'] = $page['hero_images'];
        }
        if (isset($page['include_items']) && is_array($page['include_items'])) {
            $resp['include_items'] = $page['include_items'];
        }
        if (isset($page['exclude_items']) && is_array($page['exclude_items'])) {
            $resp['exclude_items'] = $page['exclude_items'];
        }

        echo json_encode(['ok' => true, 'page' => $resp], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } catch (Throwable $e) {
        echo json_encode([
            'ok'           => false,
            'error'        => 'load_failed',
            'error_detail' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    exit;
}

if ($action === 'save') {
    settings_json_headers();

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data) || !isset($data['page']) || !is_array($data['page'])) {
        echo json_encode(['ok' => false, 'error' => 'bad_payload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $p = $data['page'];

    // Normalisasi isi
    $page = [
        'hero_title'          => (string)($p['hero_title'] ?? ''),
        'hero_subtitle'       => (string)($p['hero_subtitle'] ?? ''),
        'badge_left'          => (string)($p['badge_left'] ?? ''),
        'badge_right'         => (string)($p['badge_right'] ?? ''),
        'article_title'       => (string)($p['article_title'] ?? ''),
        'article_intro_html'  => (string)($p['article_intro_html'] ?? ''),
        'accordion_title'     => (string)($p['accordion_title'] ?? ''),
        'accordion_body_html' => (string)($p['accordion_body_html'] ?? ''),
        'locations_title'     => (string)($p['locations_title'] ?? ''),
        'hero_images'         => [],
        'include_items'       => [],
        'exclude_items'       => [],
    ];

    // hero_images (array string)
    if (isset($p['hero_images']) && is_array($p['hero_images'])) {
        $imgs = [];
        foreach ($p['hero_images'] as $v) {
            $v = trim((string)$v);
            if ($v !== '') {
                $imgs[] = $v;
            }
        }
        $page['hero_images'] = $imgs;
    }

    // include_items
    if (isset($p['include_items']) && is_array($p['include_items'])) {
        $items = [];
        foreach ($p['include_items'] as $v) {
            $v = trim((string)$v);
            if ($v !== '') {
                $items[] = $v;
            }
        }
        $page['include_items'] = $items;
    }

    // exclude_items
    if (isset($p['exclude_items']) && is_array($p['exclude_items'])) {
        $items = [];
        foreach ($p['exclude_items'] as $v) {
            $v = trim((string)$v);
            if ($v !== '') {
                $items[] = $v;
            }
        }
        $page['exclude_items'] = $items;
    }

    try {
        $tpl = settings_load_global_template($pdo);
        if (!isset($tpl['pages']) || !is_array($tpl['pages'])) {
            $tpl['pages'] = [];
        }
        $tpl['pages'][$activityKey] = $page;

        settings_save_global_template($pdo, $tpl);

        echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } catch (Throwable $e) {
        echo json_encode([
            'ok'           => false,
            'error'        => 'save_failed',
            'error_detail' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    exit;
}

/* ---------- HALAMAN HTML (FORM + AUTOSAVE) ---------- */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fun Diving Page Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { background:#020617; color:#e5e7eb; }
    .card {
      background:#020617;
      border-radius:1rem;
      border:1px solid #1f2937;
      padding:1.5rem;
      box-shadow:0 20px 40px rgba(15,23,42,0.6);
    }
    .label {
      font-size:0.8rem;
      text-transform:uppercase;
      letter-spacing:.08em;
      color:#9ca3af;
      margin-bottom:0.25rem;
      display:block;
    }
    .input, .textarea {
      width:100%;
      border-radius:0.75rem;
      border:1px solid #374151;
      background:#020617;
      padding:0.6rem 0.75rem;
      font-size:0.9rem;
      color:#e5e7eb;
      outline:none;
    }
    .input:focus, .textarea:focus {
      border-color:#38bdf8;
      box-shadow:0 0 0 1px rgba(56,189,248,0.5);
    }
    .textarea {
      min-height:90px;
      resize:vertical;
      font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas,"Liberation Mono","Courier New",monospace;
      font-size:0.8rem;
      line-height:1.4;
      white-space:pre-wrap;
    }
    .badge {
      font-size:0.7rem;
      padding:0.15rem 0.4rem;
      border-radius:999px;
      background:#0f172a;
      border:1px solid #1f2937;
      color:#9ca3af;
    }
    .status-pill {
      position:fixed;
      right:1.25rem;
      bottom:1.25rem;
      padding:0.35rem 0.8rem;
      border-radius:999px;
      font-size:0.75rem;
      background:#0f172a;
      border:1px solid #1f2937;
      color:#9ca3af;
      display:flex;
      align-items:center;
      gap:0.4rem;
    }
    .status-dot {
      width:8px;
      height:8px;
      border-radius:999px;
      background:#4b5563;
    }
    .status-pill.saving .status-dot { background:#f97316; }
    .status-pill.saved  .status-dot { background:#22c55e; }
    .status-pill.error  .status-dot { background:#ef4444; }
  </style>
</head>
<body class="min-h-screen">
  <div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-xl font-semibold text-sky-100">Fun Diving Page Settings</h1>
        <p class="text-xs text-slate-400 mt-1">
          Edit hero, article, dan offcanvas text untuk
          <span class="badge">pages.fun_diving</span>
          di <span class="badge">bd_global_settings.global_template</span>.
        </p>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-5 mb-6">
      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-sky-200">Hero Section</h2>
          <span class="badge">Hero</span>
        </div>

        <label class="label" for="hero_title">Hero title</label>
        <input id="hero_title" class="input mb-3" type="text" placeholder="Fun Diving in Bali">

        <label class="label" for="hero_subtitle">Hero subtitle</label>
        <textarea id="hero_subtitle" class="textarea mb-3" placeholder="Short subtitle for hero"></textarea>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label" for="badge_left">Badge left</label>
            <input id="badge_left" class="input" type="text" placeholder="Certified Divers">
          </div>
          <div>
            <label class="label" for="badge_right">Badge right</label>
            <input id="badge_right" class="input" type="text" placeholder="25+ Years in Bali">
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-sky-200">Hero Images</h2>
          <span class="badge">Array</span>
        </div>

        <label class="label" for="hero_images">Hero image URLs (one per line)</label>
        <textarea id="hero_images" class="textarea" placeholder="https://...jpg&#10;https://...jpg"></textarea>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-5 mb-6">
      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-sky-200">Article Section</h2>
          <span class="badge">Content</span>
        </div>

        <label class="label" for="article_title">Article title</label>
        <input id="article_title" class="input mb-3" type="text" placeholder="Playtime Underwater for Certified Divers">

        <label class="label" for="article_intro_html">Intro HTML</label>
        <textarea id="article_intro_html" class="textarea" placeholder="<p>Intro paragraph...</p>"></textarea>
      </div>

      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-sky-200">Accordion (What is Fun Diving?)</h2>
          <span class="badge">Content</span>
        </div>

        <label class="label" for="accordion_title">Accordion title</label>
        <input id="accordion_title" class="input mb-3" type="text" placeholder="What is Fun Diving with Bali Diving?">

        <label class="label" for="accordion_body_html">Accordion body HTML</label>
        <textarea id="accordion_body_html" class="textarea" placeholder="<p>Body text...</p>"></textarea>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-5 mb-6">
      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-sky-200">Locations Section</h2>
          <span class="badge">Title</span>
        </div>

        <label class="label" for="locations_title">Locations title</label>
        <input id="locations_title" class="input" type="text" placeholder="Choose Your Fun Diving Day">
      </div>

      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-sky-200">Include / Exclude</h2>
          <span class="badge">Offcanvas</span>
        </div>

        <label class="label" for="include_items">Include items (one per line)</label>
        <textarea id="include_items" class="textarea mb-3" placeholder="🚐 Hotel pickup..."></textarea>

        <label class="label" for="exclude_items">Exclude items (one per line)</label>
        <textarea id="exclude_items" class="textarea" placeholder="💳 Dive insurance beyond..."></textarea>
      </div>
    </div>
  </div>

  <div id="statusPill" class="status-pill">
    <span class="status-dot"></span>
    <span id="statusText">Idle</span>
  </div>

<script>
  const apiUrl = 'fun-diving-settings.php';
  let saveTimer = null;
  let isSaving  = false;

  const fields = {
    hero_title:         document.getElementById('hero_title'),
    hero_subtitle:      document.getElementById('hero_subtitle'),
    badge_left:         document.getElementById('badge_left'),
    badge_right:        document.getElementById('badge_right'),
    article_title:      document.getElementById('article_title'),
    article_intro_html: document.getElementById('article_intro_html'),
    accordion_title:    document.getElementById('accordion_title'),
    accordion_body_html:document.getElementById('accordion_body_html'),
    locations_title:    document.getElementById('locations_title'),
    hero_images:        document.getElementById('hero_images'),
    include_items:      document.getElementById('include_items'),
    exclude_items:      document.getElementById('exclude_items'),
  };

  const statusPill = document.getElementById('statusPill');
  const statusText = document.getElementById('statusText');

  function setStatus(mode, text) {
    statusPill.classList.remove('saving','saved','error');
    if (mode) statusPill.classList.add(mode);
    statusText.textContent = text;
  }

  async function loadData() {
    setStatus('', 'Loading...');
    try {
      const res = await fetch(apiUrl + '?action=load', { cache: 'no-store' });
      const json = await res.json();
      if (!json.ok) {
        console.error('LOAD ERROR', json.error, json.error_detail);
        setStatus('error', 'Load failed');
        return;
      }
      const p = json.page || {};

      fields.hero_title.value         = p.hero_title || '';
      fields.hero_subtitle.value      = p.hero_subtitle || '';
      fields.badge_left.value         = p.badge_left || '';
      fields.badge_right.value        = p.badge_right || '';
      fields.article_title.value      = p.article_title || '';
      fields.article_intro_html.value = p.article_intro_html || '';
      fields.accordion_title.value    = p.accordion_title || '';
      fields.accordion_body_html.value= p.accordion_body_html || '';
      fields.locations_title.value    = p.locations_title || '';

      const imgs = Array.isArray(p.hero_images) ? p.hero_images : [];
      fields.hero_images.value = imgs.join('\n');

      const inc = Array.isArray(p.include_items) ? p.include_items : [];
      fields.include_items.value = inc.join('\n');

      const exc = Array.isArray(p.exclude_items) ? p.exclude_items : [];
      fields.exclude_items.value = exc.join('\n');

      setStatus('', 'Loaded');
      setTimeout(() => setStatus('', 'Idle'), 1000);
    } catch (e) {
      console.error('LOAD EXCEPTION', e);
      setStatus('error', 'Load failed');
    }
  }

  function collectPayload() {
    const heroImages = fields.hero_images.value
      .split('\n')
      .map(v => v.trim())
      .filter(v => v !== '');

    const includeItems = fields.include_items.value
      .split('\n')
      .map(v => v.trim())
      .filter(v => v !== '');

    const excludeItems = fields.exclude_items.value
      .split('\n')
      .map(v => v.trim())
      .filter(v => v !== '');

    return {
      hero_title:         fields.hero_title.value.trim(),
      hero_subtitle:      fields.hero_subtitle.value.trim(),
      badge_left:         fields.badge_left.value.trim(),
      badge_right:        fields.badge_right.value.trim(),
      article_title:      fields.article_title.value.trim(),
      article_intro_html: fields.article_intro_html.value,
      accordion_title:    fields.accordion_title.value.trim(),
      accordion_body_html:fields.accordion_body_html.value,
      locations_title:    fields.locations_title.value.trim(),
      hero_images:        heroImages,
      include_items:      includeItems,
      exclude_items:      excludeItems,
    };
  }

  async function doSave() {
    if (isSaving) return;
    isSaving = true;
    setStatus('saving', 'Saving...');

    const payload = { page: collectPayload() };

    try {
      const res = await fetch(apiUrl + '?action=save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (!json.ok) {
        console.error('SAVE ERROR', json.error, json.error_detail);
        setStatus('error', 'Save failed');
      } else {
        setStatus('saved', 'Saved');
        setTimeout(() => setStatus('', 'Idle'), 800);
      }
    } catch (e) {
      console.error('SAVE EXCEPTION', e);
      setStatus('error', 'Save failed');
    } finally {
      isSaving = false;
    }
  }

  function scheduleSave() {
    if (saveTimer) clearTimeout(saveTimer);
    setStatus('saving', 'Typing...');
    saveTimer = setTimeout(doSave, 600);
  }

  function initAutosave() {
    Object.values(fields).forEach(el => {
      el.addEventListener('input', scheduleSave);
      el.addEventListener('change', scheduleSave);
    });
  }

  (function init() {
    loadData();
    initAutosave();
  })();
</script>
</body>
</html>
