<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>BaliDiving.com | Book Your Dive Experience</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .hidden{display:none;}
    .fade{transition:all .35s ease;opacity:0;transform:translateX(24px)}
    .fade.active{opacity:1;transform:translateX(0)}
    .error{color:#dc2626;font-size:.85rem;margin-top:4px}
  </style>
</head>
<body class="bg-gradient-to-br from-sky-100 via-white to-blue-50 flex items-center justify-center min-h-screen px-4">

<form id="bookingForm" class="w-full max-w-2xl bg-white/80 backdrop-blur-md border border-sky-200 rounded-3xl shadow-xl p-8 md:p-10">
  <div id="stepIndicator" class="text-center text-sky-700 font-semibold text-sm mb-6">
    Step 1 of 3 · Basic Information
  </div>

  <!-- ========== PAGE 1 ========== -->
  <div id="page1" class="fade active space-y-6">
    <h1 class="text-3xl md:text-4xl font-extrabold text-sky-700 text-center">🌊 Book Your Dive Adventure</h1>

    <!-- Preferred Date -->
    <div>
      <label class="block font-semibold text-sky-700 mb-1">Preferred Date *</label>
      <input type="date" id="date" name="date" required
             class="w-full px-4 py-3 rounded-xl border border-sky-300 focus:ring-4 focus:ring-sky-200">
      <p class="text-xs text-gray-500 mt-1">Only future dates allowed.</p>
    </div>

    <!-- Name -->
    <div>
      <label class="block font-semibold text-gray-700 mb-1">Full Name</label>
      <input type="text" id="name" name="name" required placeholder="Your full name"
             class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-sky-200">
    </div>

    <!-- Email -->
    <div>
      <label class="block font-semibold text-gray-700 mb-1">Email Address</label>
      <input type="email" id="email" name="email" required placeholder="you@example.com"
             class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-200">
      <p id="emailError" class="error hidden">Please enter a valid email address.</p>
    </div>

    <!-- WhatsApp -->
    <div>
      <label class="block font-semibold text-gray-700 mb-1">WhatsApp Number</label>
      <input type="text" id="whatsapp" name="whatsapp" required placeholder="+62 812 3456 7890"
             class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-green-200">
      <p id="waError" class="error hidden">Enter a valid WhatsApp number (9–15 digits).</p>
    </div>

    <!-- Contact Method -->
    <div>
      <label class="block font-semibold text-gray-700 mb-1">Preferred Contact Method</label>
      <select id="contact_method" name="contact_method" required
              class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-sky-200">
        <option value="">Select one</option>
        <option>WhatsApp</option><option>Email</option><option>Phone Call</option>
      </select>
    </div>

    <!-- Promo -->
    <div>
      <label class="block font-semibold text-gray-700 mb-1">Promo Code (optional)</label>
      <input type="text" id="promo" name="promo" placeholder="e.g. DIVEBALI10"
             class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-yellow-200">
    </div>

    <!-- Activity & Location (URL-driven) -->
    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="block font-semibold text-gray-700 mb-1">Activity</label>
        <input type="text" id="activity" name="activity" readonly
               class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50">
      </div>
      <div>
        <label class="block font-semibold text-gray-700 mb-1">Preferred Location</label>
        <input type="text" id="location" name="location" readonly
               class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50">
      </div>
    </div>

    <div class="flex justify-end">
      <button type="button" id="next1"
        class="px-6 py-3 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700 shadow-md">
        Next ➡️
      </button>
    </div>
  </div>

  <!-- ========== PAGE 2 ========== -->
  <div id="page2" class="fade hidden space-y-6">
    <div class="text-center">
      <h2 class="text-2xl font-bold text-sky-700">👥 Participants & Add-Ons</h2>
      <p id="certNote" class="text-gray-600 text-sm">Fill participant names and choose add-ons below.</p>
    </div>

    <div>
      <label class="block font-semibold text-gray-700 mb-1">Number of Participants</label>
      <input type="number" id="participants" name="participants" min="1" max="10" value="1"
             class="w-28 px-4 py-2 text-center rounded-xl border border-gray-300 focus:ring-4 focus:ring-sky-200">
      <p class="text-xs text-gray-500 mt-1">Maximum 10 participants per booking.</p>
    </div>

    <div id="participantFields" class="space-y-4"></div>

    <div class="flex justify-between">
      <button type="button" id="back1"
        class="px-6 py-3 bg-gray-300 rounded-xl font-semibold hover:bg-gray-400">⬅️ Back</button>
      <button type="button" id="next2"
        class="px-6 py-3 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">Next ➡️</button>
    </div>
  </div>

  <!-- ========== PAGE 3 ========== -->
  <div id="page3" class="fade hidden space-y-6">
    <div class="text-center">
      <h2 class="text-2xl font-bold text-sky-700">✅ Booking Confirmation</h2>
      <p class="text-gray-600 text-sm">Review your details before payment.</p>
    </div>
    <div id="summary" class="bg-white/70 border border-sky-200 rounded-2xl p-5 space-y-2 text-sm text-gray-800"></div>
    <div class="flex justify-between">
      <button type="button" id="back2"
        class="px-6 py-3 bg-gray-300 rounded-xl font-semibold hover:bg-gray-400">⬅️ Back</button>
      <button type="submit" id="submitBtn"
        class="px-6 py-3 bg-gradient-to-r from-sky-600 to-blue-500 text-white font-semibold rounded-xl hover:opacity-90 shadow-md">
        💳 Pay Now
      </button>
    </div>
  </div>
</form>

<script>
/* ====== Helpers & Globals ====== */
const today=new Date();today.setDate(today.getDate()+1);
document.getElementById("date").min=today.toISOString().split("T")[0];

const params=new URLSearchParams(window.location.search);
const activityInput=document.getElementById('activity');
if(params.has('activity')) activityInput.value=decodeURIComponent(params.get('activity'));
if(params.has('location')) document.getElementById('location').value=decodeURIComponent(params.get('location'));

let hideCertGlobally=false;
if(activityInput.value.toLowerCase().includes("snorkel") || activityInput.value.toLowerCase().includes("try")){
  hideCertGlobally=true;
  document.getElementById('certNote').textContent = "Certification is not required for this activity.";
}

/* ====== Step navigation ====== */
const step=document.getElementById("stepIndicator");
const page1=document.getElementById("page1"),page2=document.getElementById("page2"),page3=document.getElementById("page3");
function show(pg){
  [page1,page2,page3].forEach(e=>{e.classList.add("hidden");e.classList.remove("active");});
  pg.classList.remove("hidden");setTimeout(()=>pg.classList.add("active"),10);
  step.textContent = pg===page1 ? "Step 1 of 3 · Basic Information"
    : pg===page2 ? "Step 2 of 3 · Participants & Add-Ons"
    : "Step 3 of 3 · Confirmation & Payment";
}

/* ====== Validate Page 1 ====== */
function validateP1(){
  const em=document.getElementById("email"), wa=document.getElementById("whatsapp");
  const eE=document.getElementById("emailError"), wE=document.getElementById("waError");
  let ok=true;
  const emP=/^[^ ]+@[^ ]+\.[a-z]{2,}$/i, waP=/^[+]?\d{9,15}$/;
  if(!emP.test(em.value.trim())){ eE.classList.remove("hidden"); ok=false; } else eE.classList.add("hidden");
  if(!waP.test(wa.value.trim())){ wE.classList.remove("hidden"); ok=false; } else wE.classList.add("hidden");
  return ok;
}

/* ====== Send Page-1 to email BEFORE moving on ====== */
async function sendStep1Once(){
  if(sessionStorage.getItem("step1_sent")==="1") return true;  // avoid double-email
  const payload = new URLSearchParams({
    date: document.getElementById("date").value,
    name: document.getElementById("name").value,
    email: document.getElementById("email").value,
    whatsapp: document.getElementById("whatsapp").value,
    contact_method: document.getElementById("contact_method").value,
    promo: document.getElementById("promo").value,
    activity: document.getElementById("activity").value,
    location: document.getElementById("location").value
  });
  try{
    const res = await fetch("step1-mail.php", {
      method: "POST",
      headers: {"Content-Type":"application/x-www-form-urlencoded"},
      body: payload.toString()
    });
    if(!res.ok) throw new Error("Network error");
    sessionStorage.setItem("step1_sent","1");
    return true;
  }catch(e){
    alert("Unable to send initial booking info. Please check your connection and try again.");
    return false;
  }
}

/* ====== Dynamic participants ====== */
const nameF=document.getElementById("name"),
      partI=document.getElementById("participants"),
      cont =document.getElementById("participantFields");

function renderParts(){
  cont.innerHTML="";
  const t=parseInt(partI.value)||1;
  for(let i=1;i<=t;i++){
    const main=i===1;
    cont.innerHTML+=`
      <div class="border border-sky-200 rounded-2xl p-4 bg-gradient-to-br from-white to-sky-50 shadow-sm">
        <div class="flex justify-between items-center">
          <h3 class="font-semibold text-sky-700">Participant ${i}</h3>
          ${main ? '<span class="text-xs text-gray-500">(Primary Name)</span>' : `
            <div class="flex items-center gap-2 text-sm text-gray-700">
              <input type="checkbox" id="observer_${i}" onchange="toggleObs(${i})" class="h-4 w-4 text-orange-500">
              <label for="observer_${i}">Observer (Not Diving)</label>
            </div>`}
        </div>

        <input type="text" id="p_${i}" name="p_${i}" value="${main?nameF.value:""}" ${main?"readonly":""} required
               placeholder="Full name of participant ${i}"
               class="w-full mt-2 px-4 py-2 rounded-xl border border-gray-300 focus:ring-4 focus:ring-sky-200 ${main?"bg-gray-100":""}">

        <div id="certSec_${i}" class="mt-3 ${hideCertGlobally?'hidden':''}">
          <label class="block text-gray-700 text-sm mb-1">Certification Level</label>
          <select name="cert_${i}" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:ring-4 focus:ring-sky-200">
            <option>No Certificate / Beginner</option>
            <option>PADI Open Water Diver</option>
            <option>PADI Advanced Open Water</option>
            <option>Rescue Diver</option>
            <option>Divemaster</option>
            <option>Instructor</option>
          </select>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-3">
          <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="addon_gopro_${i}" class="h-4 w-4 text-sky-600">Add-on: GoPro Camera</label>
          <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="addon_towel_${i}" class="h-4 w-4 text-sky-600">Add-on: Towel</label>
        </div>
      </div>`;
  }
}
window.toggleObs=function(i){
  const chk=document.getElementById(`observer_${i}`),
        sec=document.getElementById(`certSec_${i}`);
  if(chk&&sec) sec.classList.toggle("hidden", chk.checked || hideCertGlobally);
};
nameF.addEventListener("input", renderParts);
partI.addEventListener("input", renderParts);

/* ====== Page events ====== */
document.getElementById("next1").onclick = async () => {
  if(!validateP1()) return;
  const sent = await sendStep1Once();       // EMAIL PAGE-1 DI SINI ✅
  if(!sent) return;
  renderParts();
  document.getElementById("page1").classList.add("hidden");
  show(page2);
};
document.getElementById("back1").onclick = ()=> show(page1);
document.getElementById("next2").onclick = ()=>{
  // Hard-validate participant names (semua wajib)
  const total = parseInt(partI.value)||1;
  for(let i=1;i<=total;i++){
    const val = (document.getElementById(`p_${i}`)?.value || "").trim();
    if(!val){ alert(`Participant ${i} name is required.`); return; }
  }
  buildSummary(); show(page3);
};
document.getElementById("back2").onclick = ()=> show(page2);

/* ====== Summary (Page 3) ====== */
function buildSummary(){
  const s=document.getElementById("summary");
  const n=parseInt(partI.value)||1;
  let html = `
    <p><b>Activity:</b> ${document.getElementById("activity").value}</p>
    <p><b>Location:</b> ${document.getElementById("location").value}</p>
    <p><b>Date:</b> ${document.getElementById("date").value}</p>
    <p><b>Name:</b> ${document.getElementById("name").value}</p>
    <p><b>Email:</b> ${document.getElementById("email").value}</p>
    <p><b>WhatsApp:</b> ${document.getElementById("whatsapp").value}</p>
    <p><b>Contact Method:</b> ${document.getElementById("contact_method").value}</p>
    <p><b>Promo Code:</b> ${document.getElementById("promo").value || "-"}</p>
    <p><b>Participants:</b> ${n}</p>
    <hr>
  `;
  for(let i=1;i<=n;i++){
    const nm=document.getElementById(`p_${i}`).value;
    const obs=document.getElementById(`observer_${i}`)?.checked;
    html += `<p class="mt-2"><b>${nm}</b> – ${obs? "Observer" : "Diver"}</p>`;
  }
  s.innerHTML = html;
}

/* ====== Fake submit (replace later with real payment) ====== */
document.getElementById("bookingForm").addEventListener("submit", e=>{
  e.preventDefault();
  const btn=document.getElementById("submitBtn");
  btn.textContent="⏳ Processing Payment..."; btn.disabled=true;
  setTimeout(()=>{ btn.textContent="✅ Payment Processed – Thank You!"; },1800);
});

/* init */
show(page1);
renderParts();
</script>
</body>
</html>
