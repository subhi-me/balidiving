<?php
// ================== CONFIG DATABASE (SAMA DENGAN CRM) ==================
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

// ================== FUNCTION KONEKSI ==================
function get_pdo() {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
            $DB_USER,
            $DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        // Pastikan tabel chat_* ada (sekali saja, aman kalau sudah ada)
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
            CONSTRAINT fk_chat_msg_session
              FOREIGN KEY (session_id) REFERENCES chat_sessions(id)
              ON DELETE CASCADE
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
    return $pdo;
}

// ================== API: LOG MESSAGE ==================
if (isset($_GET['action']) && $_GET['action'] === 'log' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $data = json_decode(file_get_contents('php://input'), true);
    $sessionKey = $data['session_key'] ?? null;
    $isBot      = !empty($data['is_bot']) ? 1 : 0;
    $text       = $data['text'] ?? '';
    $userName   = $data['user_name'] ?? null;

    if (!$sessionKey || !$text) {
        echo json_encode(['status' => 'error', 'message' => 'invalid']);
        exit;
    }

    $pdo = get_pdo();

    // find or create session
    $stmt = $pdo->prepare("SELECT id FROM chat_sessions WHERE session_key = ?");
    $stmt->execute([$sessionKey]);
    $sessionId = $stmt->fetchColumn();

    if (!$sessionId) {
        $stmt = $pdo->prepare("INSERT INTO chat_sessions (session_key, user_name) VALUES (?, ?)");
        $stmt->execute([$sessionKey, $userName]);
        $sessionId = $pdo->lastInsertId();
    } else {
        // update name jika kosong dan sekarang ada
        if ($userName) {
            $stmt = $pdo->prepare("UPDATE chat_sessions SET user_name = COALESCE(user_name, ?) WHERE id = ?");
            $stmt->execute([$userName, $sessionId]);
        }
    }

    // insert message
    $stmt = $pdo->prepare("INSERT INTO chat_messages_log (session_id, is_bot, message_text) VALUES (?, ?, ?)");
    $stmt->execute([$sessionId, $isBot, $text]);

    echo json_encode(['status' => 'ok']);
    exit;
}

// ================== LOAD CONFIG UNTUK WIDGET ==================
$pdo = get_pdo();

// 1. Intentions (tombol awal)
$stmt = $pdo->query("SELECT code, label FROM chat_intentions WHERE is_active = 1 ORDER BY sort_order ASC");
$intentions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Lists & items
$stmt = $pdo->query("
    SELECT l.id as list_id, l.code AS list_code, l.title,
           i.label, i.link, i.emoji, i.button_color_class, i.sort_order
    FROM chat_lists l
    JOIN chat_list_items i ON i.list_id = l.id
    ORDER BY l.code, i.sort_order
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$lists = [];
foreach ($rows as $row) {
    $code = $row['list_code'];
    if (!isset($lists[$code])) {
        $lists[$code] = [
            'title' => $row['title'],
            'items' => []
        ];
    }
    $lists[$code]['items'][] = [
        'label' => $row['label'],
        'link'  => $row['link'],
        'emoji' => $row['emoji'],
        'button_class' => $row['button_color_class']
    ];
}

// 3. Texts (beberapa teks utama saja)
$stmt = $pdo->query("SELECT code, text_en FROM chat_texts");
$texts = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $texts[$row['code']] = $row['text_en'];
}

$chatConfig = [
    'intentions' => $intentions,
    'lists'      => $lists,
    'texts'      => $texts
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Bali Diving · Chat Widget</title>
  <!-- Tailwind CDN (hapus jika sudah ada di template utama) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .chat-launcher {
      position: fixed;
      right: 1rem;
      bottom: 1rem;
      z-index: 999999999;
      border-radius: 9999px;
      background: #3552c8;
      color: #f9fafb;
      width: 48px;
      height: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 25px rgba(15,23,42,0.35);
      cursor: pointer;
      transition: transform .15s ease, box-shadow .15s ease;
    }
    .chat-launcher:hover{
      transform: translateY(-2px) scale(1.03);
      box-shadow: 0 14px 30px rgba(15,23,42,0.45);
    }

    #chatWidget {
      position: fixed;
      right: 1rem;
      bottom: 4.5rem;
      width: 100%;
      max-width: 380px;
      height: 50vh;
      max-height: 420px;
      z-index: 999999998;
      box-shadow: 0 18px 45px rgba(15,23,42,0.45);
      border-radius: 1.5rem;
      overflow: hidden;
    }

    @media (max-width: 768px){
      #chatWidget{
        left: .5rem;
        right: .5rem;
        max-width: none;
        height: 45vh;
        max-height: 360px;
      }
    }

    #chatMessages{
      max-height: calc(100% - 64px);
      overflow-y: auto;
    }

    .chat-bubble{
      animation: slideIn .25s ease-out;
    }
    @keyframes slideIn{
      from{opacity:0;transform:translateY(6px);}
      to{opacity:1;transform:translateY(0);}
    }

    .button-hover:hover{
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(15,23,42,0.3);
    }
  </style>
</head>
<body class="bg-white text-slate-900">

<!-- CHAT WIDGET -->
<div id="chatWidget" class="hidden bg-white border border-slate-200">
  <!-- Header -->
  <div class="flex items-center justify-between px-4 py-3 bg-slate-900 text-slate-50">
    <div>
      <p class="text-sm font-semibold">Bali Diving Assistant</p>
      <p class="text-[11px] opacity-80">Plan your dive & chat with us</p>
    </div>
    <button id="chatCloseBtn" class="text-xs px-2 py-1 border border-slate-500 rounded-full hover:bg-slate-800">
      ✕
    </button>
  </div>

  <!-- Messages -->
  <div id="chatMessages" class="p-3 space-y-2 bg-slate-50 text-sm"></div>

  <!-- Input -->
  <div class="border-t border-slate-200 bg-white px-3 py-2 flex items-center gap-2">
    <input id="userInput"
           type="text"
           class="flex-1 text-xs px-2 py-1.5 border border-slate-300 rounded-full focus:outline-none focus:ring-1 focus:ring-primary"
           placeholder="Type a question... (enter to send)">
    <button id="sendBtn"
            class="text-xs px-3 py-1.5 rounded-full bg-primary text-white hover:bg-blue-700">
      Send
    </button>
  </div>
</div>

<!-- Launcher -->
<div id="chatLauncher" class="chat-launcher">
  <i class="fa fa-comment-dots text-lg"></i>
</div>

<!-- Config dari PHP -> JS -->
<script>
  window.chatConfig = <?php
    echo json_encode($chatConfig, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;
</script>

<!-- Font Awesome (untuk icon launcher) -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      crossorigin="anonymous"/>

<script>
// ================== CONFIG DARI PHP ==================
const cfg = window.chatConfig || {};
let divingIntentions        = (cfg.intentions || []).map(i => i.label);
const listMap               = cfg.lists || {};
const chatTexts             = cfg.texts || {};

// Helper ambil list dari kode
function getList(code) {
  const l = listMap[code];
  if (!l) return [];
  return (l.items || []).map(item => ({
    name: (item.emoji ? item.emoji + ' ' : '') + item.label,
    link: item.link,
    buttonClass: item.button_class || 'bg-blue-500 hover:bg-blue-600'
  }));
}

// Mapping list
let divingLocations        = getList('diving_locations');
let beginnerDivingLocations= getList('beginner_locations');
let mantaRayLocations      = getList('manta_locations');
let wreckDivingLocations   = getList('wreck_locations');
let divingPackages         = getList('diving_packages');
let certifiedUserLocations = getList('certified_locations');
let tryScubaLocations      = getList('try_scuba_locations');
let snorkelingPackages     = getList('snorkeling_packages');

// ================== LOGIC CHAT ==================
let currentStep = 'greeting';
let userProfile = {
  intention: '',
  hasCertificate: null,
  selectedLocation: ''
};

function saveChatState() {
  sessionStorage.setItem('userProfile', JSON.stringify(userProfile));
}

// session key untuk log
let chatSessionKey = localStorage.getItem('chatSessionKey');
if (!chatSessionKey) {
  chatSessionKey = 'bd_' + Math.random().toString(36).substring(2) + Date.now();
  localStorage.setItem('chatSessionKey', chatSessionKey);
}

let chatIsOpen = false;
let inactivityTimer = null;
let hasShownFollowUp = false;
let userName = '';
let waitingForName = false;

// ========== LOGGING ==========
function logMessageToServer(message, isBot) {
  fetch('chat.php?action=log', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({
      session_key: chatSessionKey,
      is_bot: isBot ? 1 : 0,
      text: message,
      user_name: userName || null
    })
  }).catch(e => console.error('log error', e));
}

// ========== UI MESSAGE ==========
function addMessage(message, isBot = true, isButtons = false) {
  const chatMessages = document.getElementById('chatMessages');
  const messageDiv = document.createElement('div');
  messageDiv.className = `chat-bubble ${isBot ? 'flex justify-start' : 'flex justify-end'}`;

  if (isButtons) {
    messageDiv.innerHTML = message;
    logMessageToServer('[buttons_html]', true);
  } else {
    messageDiv.innerHTML = `
      <div class="${isBot ? 'bg-gray-100 text-gray-800' : 'bg-primary text-white'} rounded-2xl px-3 py-2 max-w-xs lg:max-w-md text-xs">
        ${message}
      </div>
    `;
    logMessageToServer(message, isBot);
  }

  chatMessages.appendChild(messageDiv);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

function createButtonGroup(buttons, callback) {
  let buttonsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-3"><div class="grid gap-1.5">';
  buttons.forEach((button, index) => {
    buttonsHtml += `
      <button onclick="${callback}(${index})"
              class="button-hover bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-200 text-left">
        ${button}
      </button>
    `;
  });
  buttonsHtml += '</div></div></div></div>';
  return buttonsHtml;
}

// ================== FLOW DARI INTENTION (sama seperti versi sebelumnya, dipersingkat) ==================
function showDivingLocations() {
  let locationsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-3"><div class="space-y-1.5">';
  divingLocations.forEach((location, index) => {
    const btnClass = location.buttonClass || 'bg-blue-500 hover:bg-blue-600';
    locationsHtml += `<button onclick="selectLocation(${index})" class="button-hover w-full ${btnClass} text-white rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200 text-left">${location.name}</button>`;
  });
  locationsHtml += '</div></div></div></div>';
  addMessage(locationsHtml, true, true);
  currentStep = 'location';
}

function showBeginnerDivingLocations() {
  let locationsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-3"><div class="space-y-1.5">';
  beginnerDivingLocations.forEach((location, index) => {
    const btnClass = location.buttonClass || 'bg-green-500 hover:bg-green-600';
    locationsHtml += `<button onclick="selectBeginnerLocation(${index})" class="button-hover w-full ${btnClass} text-white rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200 text-left">${location.name}</button>`;
  });
  locationsHtml += '</div></div></div></div>';
  addMessage(locationsHtml, true, true);
  currentStep = 'location';
}

function showMantaRayLocations() {
  let locationsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-3"><div class="space-y-1.5">';
  mantaRayLocations.forEach((location, index) => {
    const btnClass = location.buttonClass || 'bg-purple-500 hover:bg-purple-600';
    locationsHtml += `<button onclick="selectMantaLocation(${index})" class="button-hover w-full ${btnClass} text-white rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200 text-left">${location.name}</button>`;
  });
  locationsHtml += '</div></div></div></div>';
  addMessage(locationsHtml, true, true);
  currentStep = 'location';
}

function showWreckDivingLocations() {
  let locationsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-3"><div class="space-y-1.5">';
  wreckDivingLocations.forEach((location, index) => {
    const btnClass = location.buttonClass || 'bg-slate-700 hover:bg-slate-800';
    locationsHtml += `<button onclick="selectWreckLocation(${index})" class="button-hover w-full ${btnClass} text-white rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200 text-left">${location.name}</button>`;
  });
  locationsHtml += '</div></div></div></div>';
  addMessage(locationsHtml, true, true);
  currentStep = 'location';
}

function showTryScubaLocations() {
  if (!tryScubaLocations.length) return;
  let locationsHtml = `<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-3"><p class="font-semibold text-gray-800 mb-2 text-xs">Category: Try Scuba Diving</p><div class="space-y-1.5">`;
  tryScubaLocations.forEach(location => {
    const btnClass = location.buttonClass || 'bg-green-500 hover:bg-green-600';
    locationsHtml += `<a href="${location.link}" target="_blank" class="button-hover w-full ${btnClass} text-white rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200 text-left block">${location.name}</a>`;
  });
  locationsHtml += '</div></div></div></div>';
  addMessage(locationsHtml, true, true);
}

function showSnorkelingPackages() {
  if (!snorkelingPackages.length) return;
  let packagesHtml = `<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-3"><p class="font-semibold text-gray-800 mb-2 text-xs">Snorkeling Packages</p><div class="space-y-1.5">`;
  snorkelingPackages.forEach(pkg => {
    const btnClass = pkg.buttonClass || 'bg-cyan-500 hover:bg-cyan-600';
    packagesHtml += `<a href="${pkg.link}" target="_blank" class="button-hover w-full ${btnClass} text-white rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200 text-left block">${pkg.name}</a>`;
  });
  packagesHtml += '</div></div></div></div>';
  addMessage(packagesHtml, true, true);
}

// ==== Intention selection ====
function selectIntention(index) {
  userProfile.intention = divingIntentions[index];
  saveChatState();
  addMessage(divingIntentions[index], false);
  startInactivityTimer();

  const intention = userProfile.intention;

  if (intention === 'First time diving experience') {
    setTimeout(() => {
      addMessage(chatTexts['first_time_intro'] || `No worries! We have fantastic beginner-friendly sites and can arrange training if needed. Here are some perfect locations for you in Bali:`);
      setTimeout(() => {
        showBeginnerDivingLocations();
        startInactivityTimer();
      }, 800);
    }, 600);
  } else if (intention === 'Diving with manta rays') {
    setTimeout(() => {
      addMessage(chatTexts['manta_intro'] || `Amazing choice! Manta rays are truly magnificent creatures. Here are the best spots in Bali where you can encounter these gentle giants:`);
      setTimeout(() => {
        showMantaRayLocations();
        startInactivityTimer();
      }, 800);
    }, 600);
  } else if (intention === 'Wreck diving adventure') {
    setTimeout(() => {
      addMessage(chatTexts['wreck_intro'] || `Fantastic choice! Wreck diving offers incredible history and marine life. Here are the best shipwreck sites in Bali:`);
      setTimeout(() => {
        showWreckDivingLocations();
        startInactivityTimer();
      }, 800);
    }, 600);
  } else if (intention === 'Snorkeling') {
    setTimeout(() => {
      addMessage(chatTexts['snorkeling_intro'] || `Snorkeling is a fantastic way to see Bali's beautiful marine life from the surface, no certification needed! It's perfect for all ages. Here are our most popular snorkeling packages:`);
      setTimeout(() => {
        showSnorkelingPackages();
      }, 800);
    }, 600);
  } else {
    // fallback: langsung tanya sertifikat
    setTimeout(() => {
      addMessage(chatTexts['cert_question'] || `Perfect choice! Now, do you have a diving certification from PADI, SSI, or other diving organizations?`);
      setTimeout(() => {
        const certButtons = createButtonGroup(["Yes, I'm certified", "No, I'm a beginner"], 'selectCertification');
        addMessage(certButtons, true, true);
        currentStep = 'certification';
        startInactivityTimer();
      }, 600);
    }, 600);
  }
}

function selectCertification(index) {
  userProfile.hasCertificate = index === 0;
  saveChatState();
  const response = index === 0 ? "Yes, I'm certified" : "No, I'm a beginner";
  addMessage(response, false);
  startInactivityTimer();

  if (userProfile.hasCertificate) {
    setTimeout(() => {
      addMessage(chatTexts['cert_yes_intro'] || `Excellent! As a certified diver, you'll have access to our full range of dive sites. Here are some amazing locations I'd recommend in Bali:`);
      setTimeout(() => {
        showDivingLocations();
        startInactivityTimer();
      }, 800);
    }, 600);
  } else {
    setTimeout(() => {
      addMessage(chatTexts['cert_no_intro'] || `No worries! We have fantastic beginner-friendly sites and can arrange training if needed.`);
      setTimeout(() => {
        showBeginnerDivingLocations();
        startInactivityTimer();
      }, 800);
    }, 600);
  }
}

// ================== NAME HANDLING ==================
function extractName(userInput) {
  const prefixesToRemove = ['my name is', "i'm", 'i am', 'call me', "it's", 'they call me', 'nama saya adalah', 'nama saya', 'nama ku', 'panggil saya', 'panggil saja'];
  let name = userInput.trim().toLowerCase();
  for (const prefix of prefixesToRemove) {
    if (name.startsWith(prefix + ' ')) {
      name = name.substring(prefix.length).trim();
      break;
    }
  }
  if (name.startsWith('saya ') && name.split(' ').length > 1) {
    name = name.substring('saya'.length).trim();
  }
  name = name.replace(/[^a-z\s]/g, '');
  if (!name) return "Friend";
  return name.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
}

function proceedAfterName() {
  setTimeout(() => {
    const txt = chatTexts['intention_prompt'] || `Nice to meet you, ${userName}! 😊 What brings you to explore scuba diving in Bali today? Please select what interests you most:`;
    addMessage(txt.replace('%NAME%', userName || 'Friend'));
    setTimeout(() => {
      const intentionButtons = createButtonGroup(divingIntentions, 'selectIntention');
      addMessage(intentionButtons, true, true);
      currentStep = 'intention';
    }, 800);
  }, 500);
}

// ================== INPUT HANDLER ==================
function sendMessage() {
  const input = document.getElementById('userInput');
  const message = (input.value || '').trim();
  if (!message) return;
  addMessage(message, false);
  input.value = '';
  startInactivityTimer();

  if (waitingForName) {
    userName = extractName(message);
    localStorage.setItem('userName', userName);
    waitingForName = false;
    proceedAfterName();
    return;
  }

  addMessage(`Thanks, ${userName || 'Friend'}! Please use the buttons above so I can guide you step by step. 😊`);
}

// ================== GREETING ==================
function initializeChat() {
  const savedName = localStorage.getItem('userName');
  const savedProfileData = sessionStorage.getItem('userProfile');

  if (savedName && savedProfileData) {
    userName = savedName;
    userProfile = JSON.parse(savedProfileData);
    setTimeout(() => {
      addMessage(chatTexts['return_greeting'] || `Hey! Welcome back. Are you still ${userName}?`);
      const confirmButtons = createButtonGroup(
        ["Yes, that's me", "No, I'm someone else"],
        'handleIdentityAndContinue'
      );
      addMessage(confirmButtons, true, true);
      currentStep = 'confirm_identity_and_continue';
    }, 300);
  } else if (savedName) {
    userName = savedName;
    setTimeout(() => {
      addMessage(chatTexts['reload_greeting'] || `Hey! Welcome back, ${userName}. It looks like the page reloaded.`);
      setTimeout(() => {
        addMessage(`Is this still you?`);
        const confirmButtons = createButtonGroup(["Yes, that's me", "No, I'm someone else"], 'handleReloadConfirmation');
        addMessage(confirmButtons, true, true);
        currentStep = 'confirm_reload';
      }, 700);
    }, 300);
  } else {
    setTimeout(() => {
      addMessage(chatTexts['first_greeting'] || `Hey! So glad you're here at Bali Diving. It's a perfect time to plan an underwater adventure...`);
      setTimeout(() => {
        addMessage(chatTexts['ask_name'] || `Could you tell me your name, please?`);
        waitingForName = true;
        currentStep = 'name';
      }, 900);
    }, 300);
  }
}

function handleReloadConfirmation(index) {
  const isSameUser = index === 0;
  const response = isSameUser ? "Yes, that's me" : "No, I'm someone else";
  addMessage(response, false);

  if (isSameUser) {
    setTimeout(() => {
      addMessage(`Great! Let's find out what you're interested in.`);
      proceedAfterName();
    }, 500);
  } else {
    localStorage.removeItem('userName');
    sessionStorage.removeItem('userProfile');
    userName = '';
    userProfile = { intention: '', hasCertificate: null, selectedLocation: '' };
    setTimeout(() => {
      addMessage(`Alright, no problem! Could you tell me your name, please?`);
      waitingForName = true;
      currentStep = 'name';
    }, 500);
  }
}

function handleIdentityAndContinue(index) {
  const isSameUser = index === 0;
  const response = isSameUser ? "Yes, that's me" : "No, I'm someone else";
  addMessage(response, false);

  if (isSameUser) {
    setTimeout(() => {
      if (userProfile.intention) {
        addMessage(`Great! Last time you were interested in "${userProfile.intention}".`);
        addMessage(`Would you like to continue with that, or explore something else?`);
        const continueButtons = createButtonGroup(
          [`Yes, continue with "${userProfile.intention}"`, 'Explore other options'],
          'handleContinueChoice'
        );
        addMessage(continueButtons, true, true);
        currentStep = 'confirm_continue';
      } else {
        addMessage(`Great! Let's continue where you left off.`);
        proceedAfterName();
      }
    }, 500);
  } else {
    localStorage.removeItem('userName');
    sessionStorage.removeItem('userProfile');
    userName = '';
    userProfile = { intention: '', hasCertificate: null, selectedLocation: '' };
    setTimeout(() => {
      addMessage(`Alright, no problem! Could you tell me your name, please?`);
      waitingForName = true;
      currentStep = 'name';
    }, 500);
  }
}

function handleContinueChoice(index) {
  const shouldContinue = index === 0;
  const response = shouldContinue ? `Continue with "${userProfile.intention}"` : "Explore other options";
  addMessage(response, false);
  startInactivityTimer();

  if (shouldContinue) {
    setTimeout(() => {
      addMessage(`Perfect! Let's continue with "${userProfile.intention}".`);
      selectIntention(divingIntentions.indexOf(userProfile.intention));
    }, 500);
  } else {
    sessionStorage.removeItem('userProfile');
    userProfile = { intention: '', hasCertificate: null, selectedLocation: '' };
    setTimeout(() => {
      addMessage("Sure thing! Let's see what else you might be interested in.");
      proceedAfterName();
    }, 500);
  }
}

// ================== INACTIVITY TIMER ==================
function startInactivityTimer() {
  if (inactivityTimer) clearTimeout(inactivityTimer);
  if (hasShownFollowUp || currentStep === 'completed') return;
  inactivityTimer = setTimeout(() => {
    if (!hasShownFollowUp && chatIsOpen) {
      hasShownFollowUp = true;
      const namePrefix = userName ? `${userName}, ` : '';
      addMessage(`${namePrefix}still there? Need help through WhatsApp instead? 😊`);
    }
  }, 60000);
}

function resetInactivityTimer() {
  if (inactivityTimer) {
    clearTimeout(inactivityTimer);
    inactivityTimer = null;
  }
}

// ================== TOGGLE WIDGET ==================
const chatWidget   = document.getElementById('chatWidget');
const chatLauncher = document.getElementById('chatLauncher');
const chatCloseBtn = document.getElementById('chatCloseBtn');
const sendBtn      = document.getElementById('sendBtn');
const userInputEl  = document.getElementById('userInput');

chatLauncher.addEventListener('click', () => {
  chatIsOpen = true;
  chatWidget.classList.remove('hidden');
  chatLauncher.classList.add('hidden');
  if (currentStep === 'greeting') {
    initializeChat();
  }
  startInactivityTimer();
});

chatCloseBtn.addEventListener('click', () => {
  chatIsOpen = false;
  chatWidget.classList.add('hidden');
  chatLauncher.classList.remove('hidden');
  resetInactivityTimer();
});

sendBtn.addEventListener('click', sendMessage);
userInputEl.addEventListener('keypress', e => {
  if (e.key === 'Enter') sendMessage();
});
</script>

</body>
</html>
