<?php
// Handles Form Logic: Primary (PHP Mail)
// CRITICAL: This logic must run BEFORE any HTML output (including from included files)

$message_sent = false;

// 1. Check for success flag (if we redirect to self)
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $message_sent = true;
}

// 2. Handle Primary Submission via PHP Mail (AJAX Request)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Clear any previous output to ensure clean JSON
    if (ob_get_length())
        ob_clean();
    header('Content-Type: application/json');

    // Collect Data
    $activity = htmlspecialchars($_POST['activity'] ?? '');
    $experience = htmlspecialchars($_POST['experience'] ?? 'N/A');
    $date = htmlspecialchars($_POST['date'] ?? '');
    $participants = htmlspecialchars($_POST['participants'] ?? '');
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $whatsapp = htmlspecialchars($_POST['whatsapp'] ?? '');

    // Construct Email Subject
    $subject = "Group Enquiry: $activity ($participants Pax) - $name";

    // Construct Email Body (Natural Narrative)
    $experienceSentence = ($activity === 'Scuba Diving')
        ? ($experience === 'Beginner' ? "Note that we are beginners and will need instruction." : "We are certified divers (PADI/SSI).")
        : "We are planning a relaxing snorkeling trip.";

    // Construct Links for Buttons
    $waText = urlencode("Halo $name, thank you for your enquiry regarding group booking for $activity. Here is our proposal.");
    $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsapp) . "?text=$waText";

    $mailSubject = urlencode("Re: Group Enquiry: $activity ($participants Pax)");
    $mailBody = urlencode("Dear $name,\n\nThank you for your enquiry regarding $activity.\n\nWe are pleased to provide you with the following proposal...\n\nBest regards,\nBali Diving Team");
    $mailLink = "https://mail.google.com/mail/?view=cm&to=" . rawurlencode($email) . "&su=$mailSubject&body=$mailBody";

    $body = "
    <html>
    <head>
        <title>New Group Enquiry</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
            h2 { color: #0284c7; border-bottom: 2px solid #0284c7; padding-bottom: 10px; margin-top: 0; }
            .details { background-color: #fff; padding: 15px; border-radius: 5px; border: 1px solid #eee; margin-top: 15px; }
            .row { margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px; }
            .row:last-child { border-bottom: none; }
            .label { font-weight: bold; color: #555; display: inline-block; width: 140px; }
            .value { color: #000; }
            .footer { margin-top: 20px; font-size: 12px; color: #888; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; }
            a { color: #0284c7; text-decoration: none; }
        </style>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div class='container' style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;'>
            <h2 style='color: #0284c7; border-bottom: 2px solid #0284c7; padding-bottom: 10px; margin-top: 0;'>New Group Booking Enquiry</h2>
            <p><strong>Halo Bali Diving Team,</strong></p>
            <p>You have received a new group booking enquiry from the website.</p>
            
            <div class='details' style='background-color: #fff; padding: 15px; border-radius: 5px; border: 1px solid #eee; margin-top: 15px;'>
                <div class='row' style='margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;'>
                    <span class='label' style='font-weight: bold; color: #555; display: inline-block; width: 140px;'>Name:</span>
                    <span class='value' style='color: #000;'>$name</span>
                </div>
                <div class='row' style='margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;'>
                    <span class='label' style='font-weight: bold; color: #555; display: inline-block; width: 140px;'>Activity:</span>
                    <span class='value' style='color: #0284c7; font-weight:bold;'>$activity</span>
                </div>
                <div class='row' style='margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;'>
                    <span class='label' style='font-weight: bold; color: #555; display: inline-block; width: 140px;'>Group Size:</span>
                    <span class='value' style='color: #000;'>$participants Pax</span>
                </div>
                <div class='row' style='margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;'>
                    <span class='label' style='font-weight: bold; color: #555; display: inline-block; width: 140px;'>Planned Date:</span>
                    <span class='value' style='color: #000;'>$date</span>
                </div>
                <div class='row' style='margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;'>
                    <span class='label' style='font-weight: bold; color: #555; display: inline-block; width: 140px;'>Details:</span>
                    <span class='value' style='color: #000;'>$experienceSentence</span>
                </div>
                <div class='row' style='margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;'>
                    <span class='label' style='font-weight: bold; color: #555; display: inline-block; width: 140px;'>WhatsApp:</span>
                    <span class='value'><a href='https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsapp) . "' style='color: #0284c7;'>$whatsapp</a></span>
                </div>
                <div class='row' style='margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;'>
                    <span class='label' style='font-weight: bold; color: #555; display: inline-block; width: 140px;'>Email:</span>
                    <span class='value'><a href='mailto:$email' style='color: #0284c7;'>$email</a></span>
                </div>
                 <div class='row' style='margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;'>
                    <span class='label' style='font-weight: bold; color: #555; display: inline-block; width: 140px;'>IP Address:</span>
                    <span class='value' style='color: #000;'>" . $_SERVER['REMOTE_ADDR'] . "</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style='text-align: center; margin-top: 30px; margin-bottom: 20px;'>
                <!-- WhatsApp Button -->
                <a href='$waLink' 
                   style='background-color: #25D366; color: white; padding: 8px 16px; text-decoration: none; border-radius: 30px; font-weight: bold; display: inline-block; margin: 5px; box-shadow: 0 3px 5px rgba(37, 211, 102, 0.2); border: 1px solid #25D366; font-size: 12px;'>
                   💬 Follow Up WhatsApp
                </a>

                <!-- Gmail Button -->
                <a href='$mailLink' 
                   style='background-color: #EA4335; color: white; padding: 8px 16px; text-decoration: none; border-radius: 30px; font-weight: bold; display: inline-block; margin: 5px; box-shadow: 0 3px 5px rgba(234, 67, 53, 0.2); border: 1px solid #EA4335; font-size: 12px;'>
                   ✉️ Follow Up Gmail
                </a>
            </div>
            
            <div class='footer' style='margin-top: 20px; font-size: 12px; color: #888; text-align: center; border-top: 1px solid #ddd; padding-top: 10px;'>
                <p>Sent from Bali Diving Website</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Email Headers
    $to = "sales@balidiving.com";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From:Enquiry <no-reply@balidiving.com>" . "\r\n";
    $headers .= "Cc: admin@balidiving.com" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";

    // Attempt to Send
    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Email sent via PHP']);
    } else {
        // Log error or handle failure
        echo json_encode(['success' => false, 'message' => 'PHP Mail failed']);
    }
    exit; // Stop execution after JSON response
}
?>
<?php
$page = 'enquire';
include('01-start.php');
?>


<!-- Custom Styles for Eye Comfort & Natural Feel -->
<style>
    body {
        background-color: #f8fafc;
    }

    .soft-gradient {
        background: radial-gradient(circle at 10% 20%, rgb(240, 250, 255) 0%, rgb(255, 255, 255) 50%, rgb(245, 247, 250) 90%);
    }

    .step-container {
        display: none;
        opacity: 0;
        transform: translateY(15px);
        transition: all 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    .step-container.active {
        display: flex;
        opacity: 1;
        transform: translateY(0);
    }

    .option-card {
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .option-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
    }

    .option-card.selected {
        border-color: #3b82f6;
        background-color: #eff6ff;
        box-shadow: 0 10px 30px -10px rgba(59, 130, 246, 0.15);
    }

    .input-comfort {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .input-comfort:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Hide scrollbar */
    ::-webkit-scrollbar {
        width: 0px;
        background: transparent;
    }

    .progress-bar {
        transition: width 0.8s ease-in-out;
    }

    /* Spinner for loading state */
    .spinner {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 3px solid white;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 8px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<main class="min-h-screen soft-gradient flex flex-col pt-24 pb-12">

    <!-- Softer Progress Bar -->
    <div class="fixed top-0 left-0 w-full h-1.5 bg-slate-100 z-50">
        <div id="progressBar"
            class="h-full bg-gradient-to-r from-blue-400 to-cyan-400 w-[10%] shadow-[0_0_15px_rgba(56,189,248,0.5)]">
        </div>
    </div>

    <!-- Main Form Container -->
    <div class="flex-grow flex items-center justify-center p-4 md:p-6">
        <div
            class="w-full max-w-2xl bg-white/90 backdrop-blur-xl border border-white/60 rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] overflow-hidden min-h-[580px] flex flex-col relative">

            <!-- Branding Header -->
            <div class="absolute top-0 left-0 w-full p-8 flex justify-between items-center z-10 opacity-70">
                <div class="text-slate-500 font-bold text-xs tracking-[0.2em] uppercase">Group Booking</div>
                <div class="text-slate-400 text-xs font-medium tracking-wide" id="stepIndicator">Step 1 of 4</div>
            </div>

            <!-- Form Content -->
            <!-- Standard form action is empty initially to support PHP Mail (same page) -->
            <!-- JS will change action to FormSubmit if PHP Mail fails -->
            <form id="enquiryForm" method="POST" action="" class="flex-grow flex flex-col relative"
                onsubmit="prepareSubmission()">



                <!-- SUCCESS MESSAGE -->
                <div id="successOverlay"
                    class="<?php echo $message_sent ? 'flex' : 'hidden'; ?> absolute inset-0 z-50 bg-white/95 backdrop-blur-md flex-col items-center justify-center text-center p-8 animate-fade-in-up">
                    <div
                        class="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center text-5xl mb-6 shadow-sm">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-800 mb-4 tracking-tight">Request Sent!</h2>
                    <p class="text-slate-500 max-w-md mx-auto mb-10 text-lg leading-relaxed">
                        Thank you. We've received your details and will craft a personalized proposal for your group
                        shortly.
                    </p>
                    <a href="/"
                        class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition shadow-xl hover:shadow-2xl">
                        Back to Home
                    </a>
                </div>

                <!-- STEP 1: Activity -->
                <div id="step1"
                    class="step-container active flex-grow flex-col justify-center items-center h-full w-full p-6 md:p-12">
                    <h2
                        class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-10 text-center leading-tight tracking-tight">
                        What's your group <br><span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">interested
                            in?</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-xl">
                        <!-- Snorkeling Option -->
                        <div class="option-card cursor-pointer border border-slate-100 rounded-3xl p-8 hover:border-cyan-200 group bg-white shadow-sm"
                            onclick="selectActivity('Snorkeling', this)">
                            <div class="flex items-center justify-between mb-6">
                                <span
                                    class="w-14 h-14 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-swimmer"></i>
                                </span>
                                <div
                                    class="w-6 h-6 rounded-full border border-slate-200 flex items-center justify-center selected-check">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 hidden"></div>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700">Snorkeling</h3>
                            <p class="text-sm text-slate-400 mt-2 leading-relaxed">Fun surface exploration. Perfect for
                                families & beginners.</p>
                        </div>

                        <!-- Scuba Option -->
                        <div class="option-card cursor-pointer border border-slate-100 rounded-3xl p-8 hover:border-blue-200 group bg-white shadow-sm"
                            onclick="selectActivity('Scuba Diving', this)">
                            <div class="flex items-center justify-between mb-6">
                                <span
                                    class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-water"></i>
                                </span>
                                <div
                                    class="w-6 h-6 rounded-full border border-slate-200 flex items-center justify-center selected-check">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 hidden"></div>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700">Scuba Diving</h3>
                            <p class="text-sm text-slate-400 mt-2 leading-relaxed">Deep immersion. Explore reefs,
                                wrecks, and marine life.</p>
                        </div>
                    </div>
                    <input type="hidden" name="activity" id="inputActivity" required>
                </div>

                <!-- STEP 1.5: Scuba Experience (Conditional) -->
                <div id="stepScuba"
                    class="step-container flex-grow flex-col justify-center items-center h-full w-full p-6 md:p-12">
                    <button type="button" onclick="goBack()"
                        class="absolute top-24 left-8 text-slate-300 hover:text-slate-600 transition flex items-center gap-2 text-sm font-semibold tracking-wide">
                        <i class="fas fa-arrow-left text-xs"></i> Back
                    </button>

                    <h2
                        class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-10 text-center leading-tight tracking-tight">
                        Experience level?
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-xl">
                        <!-- Beginner -->
                        <div class="option-card cursor-pointer border border-slate-100 rounded-3xl p-8 hover:border-green-200 group bg-white shadow-sm"
                            onclick="selectExperience('Beginner', this)">
                            <div class="flex items-center justify-between mb-6">
                                <span
                                    class="w-14 h-14 rounded-2xl bg-green-50 text-green-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-seedling"></i>
                                </span>
                                <div
                                    class="w-6 h-6 rounded-full border border-slate-200 flex items-center justify-center selected-check">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 hidden"></div>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700">Beginner</h3>
                            <p class="text-sm text-slate-400 mt-2 leading-relaxed">No certification. We need guidance &
                                instruction.</p>
                        </div>

                        <!-- Certified -->
                        <div class="option-card cursor-pointer border border-slate-100 rounded-3xl p-8 hover:border-indigo-200 group bg-white shadow-sm"
                            onclick="selectExperience('Certified', this)">
                            <div class="flex items-center justify-between mb-6">
                                <span
                                    class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-certificate"></i>
                                </span>
                                <div
                                    class="w-6 h-6 rounded-full border border-slate-200 flex items-center justify-center selected-check">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 hidden"></div>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700">Certified</h3>
                            <p class="text-sm text-slate-400 mt-2 leading-relaxed">We have licenses (PADI/SSI) and want
                                to dive.</p>
                        </div>
                    </div>
                    <input type="hidden" name="experience" id="inputExperience">
                </div>

                <!-- STEP 2: Logistics (Date & Pax) -->
                <div id="step2"
                    class="step-container flex-grow flex-col justify-center items-center h-full w-full p-6 md:p-12">
                    <button type="button" onclick="goBack()"
                        class="absolute top-24 left-8 text-slate-300 hover:text-slate-600 transition flex items-center gap-2 text-sm font-semibold tracking-wide">
                        <i class="fas fa-arrow-left text-xs"></i> Back
                    </button>

                    <h2
                        class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-10 text-center leading-tight tracking-tight">
                        When & How Many?
                    </h2>

                    <div class="w-full max-w-sm space-y-8">
                        <!-- Date Input -->
                        <div class="group">
                            <label
                                class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider ml-1">Planned
                                Date</label>
                            <div class="relative">
                                <span
                                    class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-lg group-focus-within:text-blue-500 transition-colors"><i
                                        class="far fa-calendar-alt"></i></span>
                                <input type="date" name="date" required
                                    class="input-comfort w-full pl-14 pr-6 py-4 rounded-2xl text-lg font-semibold text-slate-700 outline-none">
                            </div>
                        </div>

                        <!-- Pax Input -->
                        <div class="group">
                            <label
                                class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider ml-1">Group
                                Size</label>
                            <div class="relative">
                                <span
                                    class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-lg group-focus-within:text-blue-500 transition-colors"><i
                                        class="fas fa-users"></i></span>
                                <input type="number" name="participants" id="inputPax" min="2" placeholder="e.g. 5"
                                    required
                                    class="input-comfort w-full pl-14 pr-6 py-4 rounded-2xl text-lg font-semibold text-slate-700 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="mt-12">
                        <button type="button" onclick="validateAndNext(2)"
                            class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition flex items-center gap-3 shadow-xl hover:shadow-2xl hover:-translate-y-1 transform duration-300">
                            Continue <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Contact Info -->
                <div id="step3"
                    class="step-container flex-grow flex-col justify-center items-center h-full w-full p-6 md:p-12">
                    <button type="button" onclick="goBack()"
                        class="absolute top-24 left-8 text-slate-300 hover:text-slate-600 transition flex items-center gap-2 text-sm font-semibold tracking-wide">
                        <i class="fas fa-arrow-left text-xs"></i> Back
                    </button>

                    <h2
                        class="text-3xl md:text-3xl font-extrabold text-slate-800 mb-8 text-center leading-tight tracking-tight">
                        Contact Details
                    </h2>

                    <div class="w-full max-w-sm space-y-6">
                        <!-- Name Input -->
                        <div class="group">
                            <label
                                class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider ml-1">Your
                                Name</label>
                            <input type="text" name="name" id="inputName" required placeholder="John Doe"
                                class="input-comfort w-full px-6 py-4 rounded-2xl text-lg font-medium text-slate-700 outline-none">
                        </div>

                        <!-- Email Input -->
                        <div class="group">
                            <label
                                class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider ml-1">Email
                                Address</label>
                            <input type="email" name="email" id="inputEmail" required placeholder="john@example.com"
                                class="input-comfort w-full px-6 py-4 rounded-2xl text-lg font-medium text-slate-700 outline-none">
                        </div>

                        <!-- WhatsApp Input -->
                        <div class="group">
                            <label
                                class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider ml-1">WhatsApp
                                Number</label>
                            <div class="relative">
                                <span
                                    class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-xl group-focus-within:text-green-500 transition-colors"><i
                                        class="fab fa-whatsapp"></i></span>
                                <input type="tel" name="whatsapp" id="inputWhatsApp" required placeholder="+62..."
                                    class="input-comfort w-full pl-14 pr-6 py-4 rounded-2xl text-lg font-medium text-slate-700 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 w-full max-w-sm">
                        <button type="submit" id="submitBtn"
                            class="w-full py-5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-2xl font-bold text-xl hover:from-blue-700 hover:to-cyan-600 transition transform hover:-translate-y-1 shadow-xl hover:shadow-blue-500/20 flex items-center justify-center gap-3">
                            <span>Submit Enquiry</span> <i class="fas fa-paper-plane"></i>
                        </button>
                        <p class="text-center text-xs text-slate-400 mt-6 leading-relaxed">
                            <i class="fas fa-lock text-slate-300 mr-1"></i> Your details are safe with us.<br>We usually
                            reply within 24 hours.
                        </p>
                    </div>
                </div>

            </form>

            <!-- Branding Footer -->
            <div class="py-6 text-center border-t border-slate-50">
                <span class="text-[10px] text-slate-300 font-semibold tracking-widest uppercase">Bali Diving • EST.
                    1999</span>
            </div>
        </div>
    </div>
</main>

<script>
    let currentStep = 'step1';
    let selectedActivity = '';

    // Handle Form Submission: PHP Mail (Primary) -> FormSubmit (Fallback)
    document.getElementById('enquiryForm').addEventListener('submit', function (e) {
        e.preventDefault();

        prepareSubmission(); // Prepare data (subject, natural message, etc.)

        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        const originalContent = submitBtn.innerHTML;

        // Show Loading State
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="spinner"></div> Sending...';

        const formData = new FormData(form);

        // Attempt 1: Send via Internal PHP Mail (AJAX)
        fetch(window.location.href, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => {
                // If response is not JSON (e.g. error page), throw to catch block
                if (!response.ok) throw new Error("Network response was not ok");
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // SUCCESS: PHP Mail sent
                    showSuccess();
                } else {
                    // FAIL
                    console.error('PHP Mail failed:', data.message);
                    alert("Sorry, we couldn't send your enquiry due to a system error. Please try again later or contact us via WhatsApp.");
                    resetSubmitButton(document.getElementById('submitBtn'));
                }
            })
            .catch(err => {
                // FAIL: Network/Server error
                console.error('Network error:', err);
                alert("Sorry, there was a network error. Please check your connection and try again.");
                resetSubmitButton(document.getElementById('submitBtn'));
            });
    });

    function resetSubmitButton(btn) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span>Submit Enquiry</span> <i class="fas fa-paper-plane"></i>';
        }
    }

    function showSuccess() {
        const overlay = document.getElementById('successOverlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');

        // Reset UI (though hidden behind overlay)
        resetSubmitButton(document.getElementById('submitBtn'));
    }

    function updateProgress(percent) {
        const bar = document.getElementById('progressBar');
        if (bar) bar.style.width = percent + '%';
    }

    function showStep(stepId) {
        // Hide all steps
        document.querySelectorAll('.step-container').forEach(el => {
            el.classList.remove('active');
            el.style.display = 'none';
        });

        // Show target step
        const target = document.getElementById(stepId);
        if (target) {
            target.style.display = 'flex';
            // Force reflow
            void target.offsetWidth;
            target.classList.add('active');
            currentStep = stepId;
        }

        // Update Text
        const indicator = document.getElementById('stepIndicator');
        if (indicator) {
            if (stepId === 'step1') indicator.textContent = 'Step 1 of 4';
            if (stepId === 'stepScuba') indicator.textContent = 'Step 2 of 4';
            if (stepId === 'step2') indicator.textContent = 'Step 3 of 4';
            if (stepId === 'step3') indicator.textContent = 'Final Step';
        }
    }

    function selectActivity(activity, element) {
        selectedActivity = activity;
        document.getElementById('inputActivity').value = activity;

        // Remove visual selection from all
        document.querySelectorAll('#step1 .option-card').forEach(card => {
            card.classList.remove('selected');
            const check = card.querySelector('.selected-check div');
            if (check) check.classList.add('hidden');
        });

        // Add visual selection to current
        element.classList.add('selected');
        const check = element.querySelector('.selected-check div');
        if (check) check.classList.remove('hidden');

        // Logic to move next
        setTimeout(() => {
            if (activity === 'Scuba Diving') {
                showStep('stepScuba');
                updateProgress(33);
            } else {
                showStep('step2');
                updateProgress(66);
            }
        }, 400);
    }

    function selectExperience(level, element) {
        document.getElementById('inputExperience').value = level;

        // Remove visual selection
        document.querySelectorAll('#stepScuba .option-card').forEach(card => {
            card.classList.remove('selected');
            const check = card.querySelector('.selected-check div');
            if (check) check.classList.add('hidden');
        });

        // Add visual selection
        element.classList.add('selected');
        const check = element.querySelector('.selected-check div');
        if (check) check.classList.remove('hidden');

        // Move next
        setTimeout(() => {
            showStep('step2');
            updateProgress(66);
        }, 400);
    }

    function validateAndNext(stepNum) {
        // Validation for Date/Pax
        const date = document.querySelector('input[name="date"]');
        const pax = document.querySelector('input[name="participants"]');

        let valid = true;

        if (!date.value) {
            date.classList.add('border-red-400', 'bg-red-50');
            valid = false;
        } else {
            date.classList.remove('border-red-400', 'bg-red-50');
        }

        if (!pax.value) {
            pax.classList.add('border-red-400', 'bg-red-50');
            valid = false;
        } else {
            pax.classList.remove('border-red-400', 'bg-red-50');
        }

        if (valid) {
            showStep('step3');
            updateProgress(90);
        }
    }

    function goBack() {
        if (currentStep === 'stepScuba') {
            showStep('step1');
            updateProgress(10);
        } else if (currentStep === 'step2') {
            if (selectedActivity === 'Scuba Diving') {
                showStep('stepScuba');
                updateProgress(33);
            } else {
                showStep('step1');
                updateProgress(10);
            }
        } else if (currentStep === 'step3') {
            showStep('step2');
            updateProgress(66);
        }
    }

    // Pre-submission logic (kept for potential validation or future use)
    function prepareSubmission() {
        // Data is now handled directly by PHP on submission
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        showStep('step1');
    });
</script>

<?php include('03-end.php') ?>