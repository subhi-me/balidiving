<?php
// === PHOTO ACCESS REQUEST FORM ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "customer.service@balidiving.com";
    $subject = "📸 Photo Access Request - Bali Diving Website";

    // Ambil data dari form
    $name       = htmlspecialchars($_POST['name']);
    $participant= htmlspecialchars($_POST['participant']);
    $email      = htmlspecialchars($_POST['email']);
    $date       = htmlspecialchars($_POST['date']);
    $purpose    = htmlspecialchars($_POST['purpose']);
    $album      = htmlspecialchars($_POST['album']);
    $message    = htmlspecialchars($_POST['message']);

    // Format email HTML yang rapi dan mudah dibaca
    $body = "
    <html>
    <head>
      <style>
        body { font-family: Arial, sans-serif; background-color: #f9fafb; color: #333; }
        .container { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 650px; margin:auto; }
        h2 { color: #0056b3; border-bottom: 2px solid #eaeaea; padding-bottom: 8px; }
        table { width:100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align:left; padding:8px; vertical-align: top; }
        th { background:#f0f4f8; width: 35%; color:#003366; }
        td { background:#fdfdfd; }
        .rules { margin-top:25px; background:#f5f8ff; padding:15px 20px; border-radius:10px; border-left:5px solid #007bff; }
        .rules h3 { color:#0056b3; font-size:16px; margin-bottom:10px; }
        .rules ul { margin:0; padding-left:20px; }
        .rules li { margin-bottom:6px; }
        .footer { margin-top:20px; font-size: 13px; color:#666; text-align:center; }
      </style>
    </head>
    <body>
      <div class='container'>
        <h2>Photo Access Request Details</h2>
        <table>
          <tr><th>Requested By</th><td>{$name}</td></tr>
          <tr><th>Participant Name</th><td>{$participant}</td></tr>
          <tr><th>Email</th><td>{$email}</td></tr>
          <tr><th>Date of Activity</th><td>{$date}</td></tr>
          <tr><th>Purpose of Use</th><td>{$purpose}</td></tr>
          <tr><th>Requested Album</th><td>{$album}</td></tr>
          <tr><th>Message</th><td>{$message}</td></tr>
        </table>

        <div class='rules'>
          <h3>📜 Photo & Video Usage Rules</h3>
          <ul>
            <li><strong>📷 Personal Use:</strong> Photos may be used for personal memories, social media, or private sharing with credit to <em>Bali Diving</em>.</li>
            <li><strong>🚫 No Commercial Use:</strong> Please do not use photos/videos for commercial or promotional purposes without written permission.</li>
            <li><strong>🪪 Copyright:</strong> All photos and videos remain the property of <em>Bali Diving</em> and respective photographers.</li>
            <li><strong>🌊 Respect Privacy:</strong> Do not share identifiable images of other participants without consent.</li>
            <li><strong>💌 Attribution:</strong> When posting online, please tag or mention <em>@balidiving</em> or <em>www.balidiving.com</em>.</li>
          </ul>
        </div>

        <div class='footer'>
          <p>This message was automatically sent from the <strong>BaliDiving.com</strong> Photo Access Request Form.</p>
        </div>
      </div>
    </body>
    </html>
    ";

    // Header agar dikirim sebagai HTML email
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: Bali Diving <no-reply@balidiving.com>\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Kirim email
    if (mail($to, $subject, $body, $headers)) {
        $status = "success";
    } else {
        $status = "error";
    }
}
?>

<!-- ================== FORM SECTION ================== -->
<section class="py-16 px-6 md:px-12 lg:px-24 bg-gradient-to-b from-sky-50 to-blue-100 text-gray-800">
  <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 bg-white rounded-2xl shadow-xl p-10">

    <!-- LEFT CONTENT -->
    <div class="space-y-4">
      <h2 class="text-3xl font-bold text-blue-800 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-camera-retro text-blue-600"></i> Request Access to Dive Photos
      </h2>
      <p class="text-gray-600">
        Please fill out this form to request access to your underwater photos or videos.
        Include accurate details to help us verify your participation.
      </p>

      <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 text-sm text-blue-800">
        <h3 class="font-semibold mb-2"><i class="fa-solid fa-scroll"></i> Usage Rules</h3>
        <ul class="list-disc list-inside space-y-1">
          <li><i class="fa-solid fa-user-shield"></i> For personal memories only</li>
          <li><i class="fa-solid fa-ban"></i> No resale or commercial use without permission</li>
          <li><i class="fa-solid fa-water"></i> Respect other divers' privacy</li>
          <li><i class="fa-solid fa-handshake"></i> Credit “Bali Diving” when sharing online</li>
        </ul>
      </div>
    </div>

    <!-- RIGHT FORM -->
    <div>
      <h3 class="text-xl font-semibold text-blue-700 mb-4">
        <i class="fa-solid fa-file-signature text-blue-500"></i> Photo Access Request Form
      </h3>

      <?php if (!empty($status)): ?>
        <?php if ($status == 'success'): ?>
          <div class="mb-4 bg-green-100 text-green-700 text-center py-2 px-4 rounded-lg shadow-sm">
            ✅ Thank you! Your request has been sent successfully.
          </div>
        <?php else: ?>
          <div class="mb-4 bg-red-100 text-red-700 text-center py-2 px-4 rounded-lg shadow-sm">
            ❌ Sorry, there was an error sending your request. Please try again.
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Requester Name</label>
          <input type="text" id="name" name="name" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
          <label for="participant" class="block text-sm font-medium text-gray-700 mb-1">Participant / Diver Name</label>
          <input type="text" id="participant" name="participant" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
          <input type="email" id="email" name="email" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
          <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date of Activity</label>
          <input type="date" id="date" name="date" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
          <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose of Use</label>
          <select id="purpose" name="purpose" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Select purpose...</option>
            <option value="personal">Personal Collection</option>
            <option value="social-media">Social Media Sharing</option>
            <option value="press">Press / Media</option>
            <option value="commercial">Commercial / Marketing</option>
          </select>
        </div>

        <div>
          <label for="album" class="block text-sm font-medium text-gray-700 mb-1">Album / Dive Site Name</label>
          <input type="text" id="album" name="album" placeholder="e.g. Nusa Penida - Manta Point" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
          <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Additional Message</label>
          <textarea id="message" name="message" rows="4"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Optional notes or reference dive guide name..."></textarea>
        </div>

        <button type="submit"
          class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg shadow-md hover:bg-blue-700 transition flex items-center justify-center gap-2">
          <i class="fa-solid fa-paper-plane"></i> Send Request
        </button>
      </form>
    </div>
  </div>
</section>
