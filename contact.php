<?php
// Handles Form Logic (PHP Mailer / Native Mail)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');

    $name = htmlspecialchars($_POST['name'] ?? 'Guest');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? 'New Enquiry');
    $message = htmlspecialchars($_POST['message'] ?? '');

    // Email Configuration
    $to = "sales@balidiving.com";
    $email_subject = "New Contact Enquiry: $subject";
    $email_body = "
    <html>
    <head>
        <title>New Contact Enquiry</title>
    </head>
    <body>
        <h2>New Contact Enquiry</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Subject:</strong> $subject</p>
        <p><strong>Message:</strong><br>$message</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Website <no-reply@balidiving.com>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";

    // Attempt to send
    $mail_success = mail($to, $email_subject, $email_body, $headers);

    if ($mail_success) {
        echo json_encode(['success' => true, 'message' => 'Email sent via PHP']);
    } else {
        echo json_encode(['success' => false, 'message' => 'PHP Mail failed']);
    }
    exit; // Stop execution after JSON response
}

// Ensure SEO Manager exists or just fallback safely
if (file_exists('template/seo_manager.php')) {
    require_once 'template/seo_manager.php';
} else {
    // Fallback stub if missing
    function generate_seo_tags($page)
    {
        return "<title>Contact Us - Bali Diving</title>";
    }
}

$page = $_GET['page'] ?? 'contact';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- Penting untuk mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_seo_tags($page); ?>
    <link rel="icon" href="bali-diving-logo.svg" type="image/svg+xml">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3552c8',
                        secondary: '#f23d4e',
                        accent: '#0070d3',
                        teal: '#23a0b4',
                        gold: '#eebe35',
                        lightblue: '#a2d2fa',
                        navy: '#063c7f'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .fade-in {
            animation: fadeIn 1.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid white;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
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
</head>

<body>

    <?php if (file_exists('template/nav-product.php'))
        include('template/nav-product.php'); ?>

    <!-- Section start -->
    <section
        class="relative bg-white overflow-hidden fade-in px-4 sm:px-6 pt-24 pb-12 sm:pt-28 sm:pb-16 md:pt-32 md:pb-20">
        <div class="max-w-6xl mx-auto">
            <!-- Judul + intro dibatasi lebar biar enak dibaca di HP -->
            <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-navy mb-4 sm:mb-6 tracking-tight">
                    Welcome to Bali Diving
                </h1>
                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                    Have questions or ready to book your dive? Fill out the form below and we'll get back to you
                    shortly.
                </p>
            </div>

            <!-- Wrapper form: fit di layar kecil -->
            <div
                class="w-full max-w-xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8 relative overflow-hidden">

                <!-- Success Message Overlay -->
                <div id="successMessage"
                    class="hidden absolute inset-0 bg-white z-50 flex flex-col items-center justify-center text-center p-6 animate-fade-in">
                    <div
                        class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-3xl mb-4">
                        <i class="fas fa-check"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Message Sent!</h3>
                    <p class="text-gray-500 mb-6">We have received your message and will reply via email shortly.</p>
                    <button onclick="window.location.reload()"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Send
                        Another</button>
                </div>

                <!-- Form -->
                <form id="contactForm" method="POST" action="" class="space-y-5">
                    <!-- FormSubmit Configuration (Hidden, used only for backup) -->
                    <input type="hidden" name="_cc" value="admin@balidiving.com">
                    <input type="hidden" name="_subject" value="New Website Contact">
                    <input type="hidden" name="_template" value="table">
                    <input type="hidden" name="_captcha" value="false">
                    <input type="hidden" name="_next" value="https://balidiving.com/contact.php?success=true">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" required placeholder="John Doe"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" required placeholder="john@example.com"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Subject</label>
                        <input type="text" name="subject" required placeholder="General Inquiry"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Message</label>
                        <textarea name="message" rows="4" required placeholder="How can we help you?"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition outline-none"></textarea>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full py-4 bg-navy hover:bg-blue-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <span>Send Message</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Wave background -->
        <div class="absolute inset-0 pointer-events-none -z-10">
            <svg class="absolute bottom-0 left-0 w-full h-24 sm:h-32 text-blue-50" preserveAspectRatio="none"
                viewBox="0 0 1200 120">
                <path
                    d="M321.39,56.44C231.54,77.19,119.24,95.24,0,100V120H1200V0C1071.84,8.89,930.84,30.08,800,50.33,601.74,81.36,411.52,35.68,321.39,56.44Z"
                    class="fill-current text-blue-100"></path>
            </svg>
        </div>
    </section>
    <!-- Section End -->

    <?php if (file_exists('template/footer.php'))
        include('template/footer.php'); ?>
    <?php if (file_exists('template/chat.php'))
        include('template/chat.php'); ?>

    <script>
        // Logic: Try PHP Mail first (AJAX). If fails, submit form to FormSubmit.co
        document.getElementById('contactForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="spinner"></div> Sending...';

            const formData = new FormData(form);

            // Attempt 1: Send via Internal PHP Mail
            fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // PHP Mail Success
                        showSuccess();
                    } else {
                        // PHP Mail Failed -> Fallback
                        console.warn('PHP Mail failed, falling back to FormSubmit...', data.message);
                        fallbackToFormSubmit(form);
                    }
                })
                .catch(err => {
                    // Network Error -> Fallback
                    console.warn('Network error, falling back to FormSubmit...', err);
                    fallbackToFormSubmit(form);
                });
        });

        function fallbackToFormSubmit(form) {
            // Change action to FormSubmit
            form.action = "https://formsubmit.co/sales@balidiving.com";

            // We must submit normally now, effectively reloading the page or redirecting
            form.submit();
        }

        function showSuccess() {
            const overlay = document.getElementById('successMessage');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            // Reset button state (though hidden)
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Send Message</span><i class="fas fa-paper-plane"></i>';

            document.getElementById('contactForm').reset();
        }

        // Check if redirected from FormSubmit success
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'true') {
            showSuccess();
        }
    </script>

</body>

</html>