<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
      <link rel="icon" href="images/bali-diving-logo.svg" type="image/svg+xml">
    <style>
        .payment-option {
            transition: all 0.3s ease;
        }
        .payment-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .demo-badge {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Complete Your Payment</h1>
                <p class="text-gray-600">Choose your preferred payment method</p>

            </div>

            <!-- Order Summary -->
           <!-- <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Summary</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Scuba Diving</span>
                        <span class="font-medium">$99.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Processing Fee</span>
                        <span class="font-medium">$0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium">$0 (Included)</span>
                    </div>
                    <hr class="my-3">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-blue-600">$99.00</span>
                    </div>
                </div>
            </div>--> 

            <!-- Payment Methods -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Select Payment Method</h2>
                
                <div class="space-y-4" id="paymentMethods">
                    <!-- Bank Transfer -->
                    <div class="payment-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer" onclick="selectPayment('bank')">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H7m2 0v-5a2 2 0 012-2h2a2 2 0 012 2v5"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Bank Transfer</h3>
                                    <p class="text-sm text-gray-600">Direct transfer from your bank account</p>
                                </div>
                            </div>
                            <input type="radio" name="payment" value="bank" class="w-5 h-5 text-blue-600">
                        </div>
                        <div class="mt-4 pl-16 hidden" id="bankDetails">
                            <div class="bg-gray-50 rounded-lg p-4 text-sm">
                                <p class="font-medium mb-2">Bank Details:</p>
                                <p><strong>Bank Name:</strong> Bank Central Asia</p>
                                <p><strong>Account Name:</strong> PT. Bali Sunfish Safaris</p>
                                <p><strong>Bank Code:</strong> 1234567890</p>
                                <p><strong>Account Number:</strong> 021000021</p>
                                <p><strong>SWIFT:</strong> x123</p>
                            </div>
                        </div>
                    </div>

                    <!-- PayPal -->
                    <div class="payment-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer" onclick="selectPayment('paypal')">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.421c-.315-.178-.7-.295-1.134-.295H12.9l-.9 5.718h2.42c2.645 0 4.267-1.28 4.267-3.185 0-.518-.196-1.296-.465-1.817z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">PayPal</h3>
                                    <p class="text-sm text-gray-600">Pay securely with your PayPal account</p>
                                </div>
                            </div>
                            <input type="radio" name="payment" value="paypal" class="w-5 h-5 text-blue-600">
                        </div>
                        <div class="mt-4 pl-16 hidden" id="paypalDetails">
                            <div class="bg-gray-50 rounded-lg p-4 text-sm">
                                <p class="text-gray-600">You'll be redirected to PayPal to complete your payment securely.</p>
                                <p class="text-gray-600 mt-2">✓ Buyer Protection included</p>
                            </div>
                        </div>
                    </div>

                    <!-- Credit Card -->
                    <div class="payment-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer" onclick="selectPayment('card')">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Credit Card</h3>
                                    <p class="text-sm text-gray-600">Visa, Mastercard, American Express</p>
                                </div>
                            </div>
                            <input type="radio" name="payment" value="card" class="w-5 h-5 text-blue-600">
                        </div>
                        <div class="mt-4 pl-16 hidden" id="cardDetails">
                            <div class="bg-gray-50 rounded-lg p-4 text-sm">
                                <p class="text-gray-600 mb-2">Accepted Cards:</p>
                                <div class="flex space-x-2">
                                    <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs">VISA</span>
                                    <span class="bg-red-600 text-white px-2 py-1 rounded text-xs">MASTERCARD</span>
                                    <span class="bg-blue-800 text-white px-2 py-1 rounded text-xs">AMEX</span>
                                </div>
                                <p class="text-gray-600 mt-2">✓ 256-bit SSL encryption</p>
                            </div>
                        </div>
                    </div>

                    <!-- Other -->

                </div>

                <!-- Continue Button -->
                <div class="mt-8">
                    <button id="continueBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition duration-300 disabled:bg-gray-300 disabled:cursor-not-allowed" disabled onclick="processPayment()">
                        Continue to Payment
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-3">
                        🔒 Your payment information is secure and encrypted
                    </p>
                </div>
            </div>

            <!-- Security Features -->
            <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Security & Trust</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span class="text-gray-600">SSL Encrypted</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span class="text-gray-600">PCI Compliant</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span class="text-gray-600">Money Back Guarantee</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div id="payment-lightbox" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-lg w-full transform transition-transform scale-95 duration-300">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-2xl font-bold text-gray-800" id="lightboxTitle"></h3>
            <button class="text-gray-400 hover:text-gray-600 focus:outline-none" onclick="closeLightbox()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mt-4 text-gray-700 space-y-4">
            <p id="lightboxDescription"></p>
            <div id="lightboxDetails" class="space-y-2 text-sm bg-gray-100 p-4 rounded-lg"></div>
        </div>
        <div class="mt-6 flex justify-end">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg" onclick="closeLightbox()">
                I got it
            </button>
        </div>
    </div>
</div>
<script>
    let selectedPayment = null;

    function selectPayment(method) {
        // Remove previous selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('selected');
        });
        
        // Hide all details
        document.querySelectorAll('[id$="Details"]').forEach(detail => {
            detail.classList.add('hidden');
        });

        // Select current option
        const selectedOption = document.querySelector(`[onclick="selectPayment('${method}')"]`);
        selectedOption.classList.add('selected');
        
        // Check the radio button
        const radio = selectedOption.querySelector('input[type="radio"]');
        radio.checked = true;
        
        // Show details for selected method
        const details = document.getElementById(method + 'Details');
        if (details) {
            details.classList.remove('hidden');
        }
        
        selectedPayment = method;
        
        // Enable continue button
        const continueBtn = document.getElementById('continueBtn');
        continueBtn.disabled = false;
        continueBtn.classList.remove('disabled:bg-gray-300');
    }

    function processPayment() {
        if (!selectedPayment) {
            alert('Please select a payment method first.');
            return;
        }

        const lightbox = document.getElementById('payment-lightbox');
        const lightboxTitle = document.getElementById('lightboxTitle');
        const lightboxDescription = document.getElementById('lightboxDescription');
        const lightboxDetails = document.getElementById('lightboxDetails');

        const paymentData = {
            'bank': {
                title: 'Payment Instructions',
                description: 'To complete your reservation, please transfer the total amount to the bank account listed below. Your booking will be confirmed immediately after we verify the payment.',
                details: `
                    <p><strong>Bank Name:</strong> Bank Central Asia</p>
                    <p><strong>Account Name:</strong> PT. Bali Sunfish Safaris</p>
                    <p><strong>Bank Code:</strong> 1234567890</p>
                    <p><strong>Account Number:</strong> 021000021</p>
                    <p><strong>SWIFT:</strong> x123</p>
                `
            },
            'paypal': {
                title: 'Redirecting to PayPal',
                description: 'You are now being redirected to the secure PayPal payment page. Your transaction will be fully protected by PayPal\'s Buyer Protection.',
                details: `
                    <p>The payment process will continue on the official PayPal website.</p>
                    <p class="text-green-600 mt-2">✓ Your payment is protected by PayPal Buyer Protection.</p>
                `
            },
            'card': {
                title: 'Secure Payment Checkout',
                description: 'Your payment is 100% secure. On the next page, please enter your credit card details to finalize your booking. We use 256-bit SSL encryption to protect your data.',
                details: `
                    <p>Accepted Cards:</p>
                    <div class="flex space-x-2 mt-2">
                        <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs">VISA</span>
                        <span class="bg-red-600 text-white px-2 py-1 rounded text-xs">MASTERCARD</span>
                        <span class="bg-blue-800 text-white px-2 py-1 rounded text-xs">AMEX</span>
                    </div>
                `
            }
        };

        const data = paymentData[selectedPayment];

        lightboxTitle.textContent = data.title;
        lightboxDescription.textContent = data.description;
        lightboxDetails.innerHTML = data.details;

        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        setTimeout(() => {
            lightbox.querySelector('div').classList.remove('scale-95');
            lightbox.querySelector('div').classList.add('scale-100');
        }, 10);
    }

    function closeLightbox() {
        const lightbox = document.getElementById('payment-lightbox');
        lightbox.querySelector('div').classList.remove('scale-100');
        lightbox.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
        }, 300);
    }
</script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'977229de9057fe87',t:'MTc1NjUzNDYzMC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
