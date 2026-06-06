<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BaliDiving.com | Book Your Dive Experience</title>
  <meta name="description" content="Book your Bali scuba diving or snorkeling experience with BaliDiving.com — PADI 5★ Dive Centers.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-sky-100 via-white to-blue-50 flex items-center justify-center min-h-screen px-4">

  <form action="send-mail.php" method="POST"
    class="w-full max-w-2xl bg-white/80 backdrop-blur-md border border-sky-200 rounded-3xl shadow-xl p-8 md:p-10 space-y-8 transition-all hover:shadow-2xl">

    <div class="text-center space-y-2">
      <h1 class="text-3xl md:text-4xl font-extrabold text-sky-700 tracking-tight">
        🌊 Book Your Dive Adventure
      </h1>
      <p class="text-gray-600 text-sm md:text-base">
        PADI 5★ Dive Centers — Trusted by Divers Worldwide
      </p>
    </div>

    <!-- Preferred Date -->
    <div>
      <label class="block text-sky-700 font-semibold mb-1 text-lg">Preferred Date <span class="text-red-500">*</span></label>
      <input type="date" id="date" name="date" required
        class="w-full px-4 py-3 rounded-xl border border-sky-300 focus:outline-none focus:ring-4 focus:ring-sky-200 text-sky-800 font-medium bg-white shadow-sm" />
      <p class="text-xs text-gray-500 mt-1">Select a future date only.</p>
    </div>

    <!-- Full Name -->
    <div>
      <label class="block text-gray-700 font-semibold mb-1">Full Name</label>
      <input type="text" id="name" name="name" required placeholder="Your full name"
        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm" />
    </div>

    <!-- WhatsApp -->
    <div>
      <label class="block text-gray-700 font-semibold mb-1">WhatsApp Number</label>
      <input type="text" name="whatsapp" required placeholder="+62 812 3456 7890"
        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-green-200 shadow-sm" />
    </div>

    <!-- Certification Level -->
    <div>
      <label class="block text-gray-700 font-semibold mb-1">Certification Level</label>
      <select name="level" required
        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-sky-200 bg-white shadow-sm">
        <option value="">-- Select Certification Level --</option>
        <option value="No Certificate / Beginner">No Certificate / Beginner</option>
        <option value="PADI Scuba Diver">PADI Scuba Diver</option>
        <option value="PADI Open Water Diver">PADI Open Water Diver</option>
        <option value="PADI Advanced Open Water">PADI Advanced Open Water</option>
        <option value="Rescue Diver">Rescue Diver</option>
        <option value="Divemaster">Divemaster</option>
        <option value="Instructor">Instructor</option>
        <option value="Other Agency (SSI, NAUI, CMAS, etc.)">Other Agency (SSI, NAUI, CMAS, etc.)</option>
      </select>
    </div>

    <!-- Activity & Location -->
    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Activity</label>
        <input type="text" id="activity" name="activity" readonly
          class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50 text-gray-700 font-medium shadow-inner" />
      </div>
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Preferred Location</label>
        <input type="text" id="location" name="location" readonly
          class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50 text-gray-700 font-medium shadow-inner" />
      </div>
    </div>

    <!-- Number of Participants -->
    <div>
      <label class="block text-gray-700 font-semibold mb-1">Number of Participants</label>
      <input type="number" id="participants" name="participants" min="1" max="10" value="1" required
        class="w-28 px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm text-center font-medium" />
      <p class="text-xs text-gray-500 mt-1">Maximum 10 participants per booking.</p>
    </div>

    <!-- Participant Checklist -->
    <div id="participantFields" class="space-y-4"></div>

    <!-- Notes -->
    <div>
      <label class="block text-gray-700 font-semibold mb-1">Additional Notes</label>
      <textarea name="message" rows="4" placeholder="Please mention any special requests..."
        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm"></textarea>
    </div>

    <!-- Submit -->
    <button type="submit"
      class="w-full py-3 text-white font-semibold rounded-xl bg-gradient-to-r from-sky-600 to-blue-500 hover:from-sky-700 hover:to-blue-600 shadow-lg hover:shadow-xl transition-all">
      🚀 Submit Booking Request
    </button>

    <p class="text-xs text-center text-gray-500 pt-2">
      Secured by BaliDiving.com — PADI 5★ Dive Centers | Visit Indonesia
    </p>
  </form>

  <script>
    // === Date restriction ===
    const today = new Date();
    today.setDate(today.getDate() + 1);
    document.getElementById("date").setAttribute("min", today.toISOString().split("T")[0]);

    // === Autofill URL ===
    const params = new URLSearchParams(window.location.search);
    if (params.has('activity')) document.getElementById('activity').value = decodeURIComponent(params.get('activity'));
    if (params.has('location')) document.getElementById('location').value = decodeURIComponent(params.get('location'));

    const participantsInput = document.getElementById("participants");
    const participantContainer = document.getElementById("participantFields");
    const nameField = document.getElementById("name");

    function renderParticipants() {
      participantContainer.innerHTML = "";
      const total = parseInt(participantsInput.value) || 1;
      for (let i = 1; i <= total; i++) {
        const isMain = i === 1;
        const nameVal = isMain ? nameField.value : "";

        participantContainer.innerHTML += `
          <div class="bg-white border border-sky-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-sky-700 text-lg">Participant ${i}</h3>
              ${isMain ? `<span class="text-xs text-gray-500">(Booking Name)</span>` : ""}
            </div>

            <input type="text" id="participant_${i}" name="participant_${i}" value="${nameVal}" ${isMain ? "readonly" : ""} required
              placeholder="Full name of participant ${i}"
              class="w-full mt-2 px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-sky-200 ${isMain ? "bg-gray-100" : ""}" />

            <div class="mt-3 grid grid-cols-2 gap-2">
              <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_diver_${i}" class="h-4 w-4 text-sky-600 rounded">
                <span class="text-sm text-gray-700">Diving / Snorkeling</span>
              </label>
              <label class="flex items-center space-x-2">
                <input type="checkbox" id="observer_${i}" name="observer_${i}" onchange="toggleObserver(${i})"
                  class="h-4 w-4 text-orange-500 rounded">
                <span class="text-sm text-gray-700">Observer Only</span>
              </label>
            </div>

            <div class="mt-3">
              <label class="block text-gray-700 text-sm mb-1">Certification Level</label>
              <select id="cert_${i}" name="cert_${i}" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-sky-200 bg-white text-gray-700">
                <option value="No Certificate / Beginner">No Certificate / Beginner</option>
                <option value="PADI Scuba Diver">PADI Scuba Diver</option>
                <option value="PADI Open Water Diver">PADI Open Water Diver</option>
                <option value="PADI Advanced Open Water">PADI Advanced Open Water</option>
                <option value="Rescue Diver">Rescue Diver</option>
                <option value="Divemaster">Divemaster</option>
                <option value="Instructor">Instructor</option>
                <option value="Other Agency (SSI, NAUI, CMAS, etc.)">Other Agency (SSI, NAUI, CMAS, etc.)</option>
              </select>
            </div>

            <div class="mt-3 flex items-center space-x-2">
              <input type="checkbox" name="equipment_${i}" class="h-4 w-4 text-green-600 rounded">
              <span class="text-sm text-gray-700">Equipment Ready</span>
            </div>
          </div>
        `;
      }
    }

    function toggleObserver(i) {
      const observer = document.getElementById(`observer_${i}`);
      const cert = document.getElementById(`cert_${i}`);
      if (observer.checked) {
        cert.disabled = true;
        cert.classList.add("bg-gray-200");
      } else {
        cert.disabled = false;
        cert.classList.remove("bg-gray-200");
      }
    }

    nameField.addEventListener("input", renderParticipants);
    participantsInput.addEventListener("input", renderParticipants);
    renderParticipants();
  </script>

</body>
</html>
