<?php
// =============== CONFIG DB (SAMA DENGAN CRM) ===============
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

function get_pdo() {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
            $DB_USER,
            $DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    return $pdo;
}

$pdo = get_pdo();

// Pastikan tabel chat_* ada
$pdo->exec("
  CREATE TABLE IF NOT EXISTS chat_sessions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    session_key VARCHAR(64) NOT NULL,
    user_name VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX(session_key),
    INDEX(user_name),
    INDEX(created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
$pdo->exec("
  CREATE TABLE IF NOT EXISTS chat_messages_log (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    session_id BIGINT UNSIGNED NOT NULL,
    is_bot TINYINT(1) NOT NULL DEFAULT 1,
    message_text TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX(session_id),
    INDEX(is_bot),
    INDEX(created_at),
    CONSTRAINT fk_chat_msg_session_admin
      FOREIGN KEY (session_id) REFERENCES chat_sessions(id)
      ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// =============== HANDLE POST ACTIONS ===============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Tambah / update intention
    if ($action === 'save_intention') {
        $id    = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $code  = trim($_POST['code'] ?? '');
        $label = trim($_POST['label'] ?? '');
        $order = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE chat_intentions SET code=?, label=?, sort_order=?, is_active=? WHERE id=?");
            $stmt->execute([$code, $label, $order, $isActive, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO chat_intentions (code, label, sort_order, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$code, $label, $order, $isActive]);
        }
    }

    if ($action === 'delete_intention') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("DELETE FROM chat_intentions WHERE id=?")->execute([$id]);
        }
    }

    // Tambah / update list
    if ($action === 'save_list') {
        $id    = isset($_POST['list_id']) ? (int)$_POST['list_id'] : 0;
        $code  = trim($_POST['list_code'] ?? '');
        $title = trim($_POST['list_title'] ?? '');
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE chat_lists SET code=?, title=? WHERE id=?");
            $stmt->execute([$code, $title, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO chat_lists (code, title) VALUES (?, ?)");
            $stmt->execute([$code, $title]);
        }
    }

    if ($action === 'delete_list') {
        $id = (int)($_POST['list_id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("DELETE FROM chat_lists WHERE id=?")->execute([$id]);
        }
    }

    // Tambah / update list item
    if ($action === 'save_item') {
        $id      = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $list_id = (int)($_POST['parent_list_id'] ?? 0);
        $label   = trim($_POST['item_label'] ?? '');
        $link    = trim($_POST['item_link'] ?? '');
        $emoji   = trim($_POST['item_emoji'] ?? '');
        $btnCls  = trim($_POST['item_button_class'] ?? 'bg-blue-500 hover:bg-blue-600');
        $order   = (int)($_POST['item_sort_order'] ?? 0);

        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE chat_list_items SET label=?, link=?, emoji=?, button_color_class=?, sort_order=? WHERE id=?");
            $stmt->execute([$label, $link, $emoji, $btnCls, $order, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO chat_list_items (list_id, label, link, emoji, button_color_class, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$list_id, $label, $link, $emoji, $btnCls, $order]);
        }
    }

    if ($action === 'delete_item') {
        $id = (int)($_POST['item_id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("DELETE FROM chat_list_items WHERE id=?")->execute([$id]);
        }
    }

    // Save text
    if ($action === 'save_text') {
        $code = trim($_POST['text_code'] ?? '');
        $txt  = trim($_POST['text_en'] ?? '');
        if ($code !== '') {
            $stmt = $pdo->prepare("SELECT id FROM chat_texts WHERE code=?");
            $stmt->execute([$code]);
            $id = $stmt->fetchColumn();
            if ($id) {
                $stmt = $pdo->prepare("UPDATE chat_texts SET text_en=? WHERE code=?");
                $stmt->execute([$txt, $code]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO chat_texts (code, text_en) VALUES (?, ?)");
                $stmt->execute([$code, $txt]);
            }
        }
    }

    header("Location: chat_admin.php");
    exit;
}

// =============== LOAD DATA UNTUK VIEW ===============
$intentions = $pdo->query("SELECT * FROM chat_intentions ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
$lists      = $pdo->query("SELECT * FROM chat_lists ORDER BY code ASC")->fetchAll(PDO::FETCH_ASSOC);

$listId = isset($_GET['list_id']) ? (int)$_GET['list_id'] : 0;
if (!$listId && $lists) {
    $listId = (int)$lists[0]['id'];
}

$listItems = [];
if ($listId) {
    $stmt = $pdo->prepare("SELECT * FROM chat_list_items WHERE list_id=? ORDER BY sort_order ASC, id ASC");
    $stmt->execute([$listId]);
    $listItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$texts = $pdo->query("SELECT * FROM chat_texts ORDER BY code ASC")->fetchAll(PDO::FETCH_ASSOC);

// Recent chat sessions (untuk monitoring cepat)
$chatSessions = $pdo->query("
    SELECT s.id, s.session_key, s.user_name, s.created_at,
           COUNT(m.id) AS msg_count,
           MAX(m.created_at) AS last_msg_at
    FROM chat_sessions s
    LEFT JOIN chat_messages_log m ON m.session_id = s.id
    GROUP BY s.id, s.session_key, s.user_name, s.created_at
    ORDER BY last_msg_at DESC, s.created_at DESC
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

function h($s){ return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Bali Diving · Chat Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100">
<div class="max-w-6xl mx-auto py-8 px-4">
  <h1 class="text-2xl font-bold mb-6">Bali Diving – Chat Assistant Admin</h1>

  <div class="grid md:grid-cols-3 gap-6">
    <!-- INTENTIONS -->
    <section class="bg-slate-900/60 border border-slate-800 rounded-xl p-4">
      <h2 class="font-semibold mb-3 text-sm">Intentions (Tombol Awal)</h2>

      <form method="post" class="space-y-2 mb-4 border border-slate-800 rounded-lg p-3">
        <input type="hidden" name="action" value="save_intention">
        <div class="grid grid-cols-2 gap-2 text-xs">
          <div>
            <label class="block mb-1">Code</label>
            <input name="code" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs" placeholder="first_time">
          </div>
          <div>
            <label class="block mb-1">Sort</label>
            <input name="sort_order" type="number" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs" value="0">
          </div>
        </div>
        <div class="text-xs">
          <label class="block mb-1">Label</label>
          <input name="label" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs" placeholder="First time diving experience">
        </div>
        <label class="inline-flex items-center gap-1 text-xs mt-1">
          <input type="checkbox" name="is_active" checked class="rounded border-slate-600 bg-slate-900">
          <span>Active</span>
        </label>
        <div class="pt-1 flex justify-end">
          <button class="px-3 py-1 rounded bg-sky-500 text-xs text-slate-950">Add Intention</button>
        </div>
      </form>

      <div class="space-y-1 max-h-72 overflow-y-auto text-xs">
        <?php foreach ($intentions as $int): ?>
          <div class="flex items-center justify-between gap-2 border border-slate-800 rounded px-2 py-1.5">
            <div>
              <div class="font-semibold"><?=h($int['label'])?></div>
              <div class="text-[10px] text-slate-400"><?=h($int['code'])?> · sort <?=$int['sort_order']?> · <?=$int['is_active'] ? 'active':'off'?></div>
            </div>
            <form method="post" onsubmit="return confirm('Delete this intention?')" class="ml-2">
              <input type="hidden" name="action" value="delete_intention">
              <input type="hidden" name="id" value="<?=$int['id']?>">
              <button class="text-[10px] text-red-400 hover:text-red-300">Delete</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- LISTS -->
    <section class="bg-slate-900/60 border border-slate-800 rounded-xl p-4">
      <h2 class="font-semibold mb-3 text-sm">Lists (Group Tombol)</h2>

      <form method="post" class="space-y-2 mb-4 border border-slate-800 rounded-lg p-3 text-xs">
        <input type="hidden" name="action" value="save_list">
        <div>
          <label class="block mb-1">Code (unik)</label>
          <input name="list_code" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs"
                 placeholder="diving_locations, beginner_locations, manta_locations, ...">
        </div>
        <div>
          <label class="block mb-1">Title (opsional)</label>
          <input name="list_title" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs"
                 placeholder="Diving Locations, Snorkeling Packages, ...">
        </div>
        <div class="pt-1 flex justify-end gap-2">
          <button class="px-3 py-1 rounded bg-sky-500 text-xs text-slate-950">Add List</button>
        </div>
      </form>

      <div class="space-y-1 max-h-72 overflow-y-auto text-xs">
        <?php foreach ($lists as $l): ?>
          <div class="flex items-center justify-between gap-2 border border-slate-800 rounded px-2 py-1.5 <?=($l['id']==$listId?'bg-slate-800/80':'')?>">
            <div>
              <div class="font-semibold"><?=h($l['code'])?></div>
              <div class="text-[10px] text-slate-400"><?=h($l['title'])?></div>
            </div>
            <div class="flex items-center gap-2">
              <a href="chat_admin.php?list_id=<?=$l['id']?>" class="text-[10px] text-cyan-400 hover:text-cyan-200">Items</a>
              <form method="post" onsubmit="return confirm('Delete this list (and its items)?')" class="m-0 p-0">
                <input type="hidden" name="action" value="delete_list">
                <input type="hidden" name="list_id" value="<?=$l['id']?>">
                <button class="text-[10px] text-red-400 hover:text-red-300">Del</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ITEMS + TEXTS -->
    <section class="bg-slate-900/60 border border-slate-800 rounded-xl p-4 text-xs">
      <h2 class="font-semibold mb-2 text-sm">Items in List</h2>
      <?php if ($listId): ?>
        <div class="mb-3 text-[11px] text-slate-300">
          Current list ID: <?=$listId?>
        </div>

        <!-- Form add item -->
        <form method="post" class="space-y-2 mb-3 border border-slate-800 rounded-lg p-3">
          <input type="hidden" name="action" value="save_item">
          <input type="hidden" name="parent_list_id" value="<?=$listId?>">
          <div>
            <label class="block mb-1">Label</label>
            <input name="item_label" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs"
                   placeholder="USAT Liberty Wreck, Tulamben">
          </div>
          <div>
            <label class="block mb-1">Link (URL)</label>
            <input name="item_link" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs"
                   placeholder="https://balidiving.diversdesk.com/...">
          </div>
          <div class="grid grid-cols-3 gap-2">
            <div>
              <label class="block mb-1">Emoji</label>
              <input name="item_emoji" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs"
                     placeholder="🐠">
            </div>
            <div>
              <label class="block mb-1">Sort</label>
              <input name="item_sort_order" type="number" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs" value="0">
            </div>
            <div>
              <label class="block mb-1">BTN Class</label>
              <input name="item_button_class" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-[10px]"
                     value="bg-blue-500 hover:bg-blue-600">
            </div>
          </div>
          <div class="flex justify-end">
            <button class="px-3 py-1 rounded bg-sky-500 text-xs text-slate-950">Add Item</button>
          </div>
        </form>

        <div class="space-y-1 max-h-40 overflow-y-auto">
          <?php foreach ($listItems as $it): ?>
            <div class="flex items-center justify-between gap-2 border border-slate-800 rounded px-2 py-1.5">
              <div>
                <div class="font-semibold"><?=h(($it['emoji'] ? $it['emoji'].' ':'').$it['label'])?></div>
                <div class="text-[10px] text-slate-400 break-all"><?=h($it['link'])?></div>
                <div class="text-[10px] text-slate-500">sort <?=$it['sort_order']?> · <?=h($it['button_color_class'])?></div>
              </div>
              <form method="post" onsubmit="return confirm('Delete item?')" class="m-0 p-0">
                <input type="hidden" name="action" value="delete_item">
                <input type="hidden" name="item_id" value="<?=$it['id']?>">
                <button class="text-[10px] text-red-400 hover:text-red-300">Del</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-[11px] text-slate-400 mb-4">Pilih list di kolom tengah untuk mengatur items.</p>
      <?php endif; ?>

      <hr class="my-4 border-slate-800">

      <!-- TEXTS -->
      <h2 class="font-semibold mb-2 text-sm">System Texts</h2>
      <form method="post" class="space-y-2 mb-3 border border-slate-800 rounded-lg p-3">
        <input type="hidden" name="action" value="save_text">
        <div>
          <label class="block mb-1">Code</label>
          <input name="text_code" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs"
                 placeholder="first_greeting, ask_name, intention_prompt, ...">
        </div>
        <div>
          <label class="block mb-1">Text</label>
          <textarea name="text_en" rows="3" class="w-full px-2 py-1 rounded bg-slate-900 border border-slate-700 text-xs"></textarea>
        </div>
        <div class="flex justify-end">
          <button class="px-3 py-1 rounded bg-sky-500 text-xs text-slate-950">Save Text</button>
        </div>
      </form>

      <div class="max-h-40 overflow-y-auto space-y-1">
        <?php foreach ($texts as $tx): ?>
          <details class="border border-slate-800 rounded px-2 py-1">
            <summary class="cursor-pointer text-[11px]"><?=h($tx['code'])?></summary>
            <div class="mt-1 text-[11px] whitespace-pre-line text-slate-300">
              <?=h($tx['text_en'])?>
            </div>
          </details>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <!-- RECENT CHAT SESSIONS -->
  <div class="mt-8 bg-slate-900/60 border border-slate-800 rounded-xl p-4">
    <h2 class="font-semibold mb-3 text-sm flex items-center gap-2">
      <span>Recent Chat Sessions</span>
      <span class="text-[11px] text-slate-400">Last 20 sessions from widget</span>
    </h2>
    <?php if (!$chatSessions): ?>
      <div class="text-[12px] text-slate-400">No chat sessions logged yet.</div>
    <?php else: ?>
      <div class="space-y-2 text-xs">
        <?php foreach ($chatSessions as $s): ?>
          <div class="border border-slate-800 rounded-lg p-2.5 bg-slate-950 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="font-semibold truncate">
                <?= h($s['user_name'] ?: '(No Name)') ?>
              </div>
              <div class="text-[10px] text-slate-400">
                Session: <span class="font-mono"><?= h(substr($s['session_key'],0,10)) ?>…</span>
                · Messages: <?= (int)$s['msg_count'] ?>
              </div>
              <div class="text-[10px] text-slate-500">
                Created: <?= h($s['created_at']) ?>
                <?php if ($s['last_msg_at']): ?> · Last: <?= h($s['last_msg_at']) ?><?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="mt-8 text-[11px] text-slate-500">
    <p><strong>Catatan SQL (sekali saja jika belum pernah create):</strong></p>
    <pre class="mt-2 bg-slate-900/80 p-3 rounded border border-slate-800 overflow-auto">
CREATE TABLE IF NOT EXISTS chat_intentions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL,
  label VARCHAR(255) NOT NULL,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS chat_lists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(100) NOT NULL,
  title VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS chat_list_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  list_id INT NOT NULL,
  label VARCHAR(255) NOT NULL,
  link VARCHAR(500) DEFAULT NULL,
  emoji VARCHAR(10) DEFAULT NULL,
  button_color_class VARCHAR(100) DEFAULT 'bg-blue-500 hover:bg-blue-600',
  sort_order INT DEFAULT 0,
  FOREIGN KEY (list_id) REFERENCES chat_lists(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS chat_texts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(100) NOT NULL,
  text_en TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS chat_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  session_key VARCHAR(64) NOT NULL,
  user_name VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX(session_key),
  INDEX(user_name),
  INDEX(created_at)
);

CREATE TABLE IF NOT EXISTS chat_messages_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  session_id BIGINT UNSIGNED NOT NULL,
  is_bot TINYINT(1) NOT NULL DEFAULT 1,
  message_text TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX(session_id),
  INDEX(is_bot),
  INDEX(created_at),
  CONSTRAINT fk_chat_msg_session FOREIGN KEY (session_id)
    REFERENCES chat_sessions(id) ON DELETE CASCADE
);
    </pre>
  </div>
</div>
</body>
</html>
