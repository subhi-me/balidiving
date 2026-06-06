<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Chat Widget</title>
    <link rel="stylesheet" href="https://d1azc1qln24ryf.cloudfront.net/114779/Socicon/style-cf.css?4d7ib0">
    <style>
        * {
            box-sizing: border-box;
        }

        #floating-chat-badge {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            cursor: pointer;
            color: #FFFFFF;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: pulse 2s infinite;
            z-index: 1000;
        }

        #floating-chat-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(37, 211, 102, 0.6);
        }

        #floating-chat-badge:active {
            transform: scale(0.95);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            }
            50% {
                box-shadow: 0 4px 30px rgba(37, 211, 102, 0.6);
            }
            100% {
                box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            }
        }

        #chat-modal {
            position: fixed;
            bottom: 120px;
            right: 30px;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            padding: 0;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            width: 350px;
            max-width: 90vw;
            z-index: 999;
            opacity: 0;
            transform: translateY(20px) scale(0.9);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            overflow: hidden;
        }

        #chat-modal.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }

        #chat-header {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: white;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            position: relative;
        }

        #close-modal-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.2s;
        }

        #close-modal-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .chat-body {
            padding: 20px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .small-btn {
            padding: 12px 16px;
            color: #128C7E;
            background: #E8F5E9;
            border: 2px solid transparent;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .small-btn:hover {
            background: #128C7E;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(18, 140, 126, 0.3);
        }

        .divider-text {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin: 20px 0 15px;
            position: relative;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .divider-text::before,
        .divider-text::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #e0e0e0;
        }

        .divider-text::before {
            left: 0;
        }

        .divider-text::after {
            right: 0;
        }

        #chat-input {
            width: 100%;
            height: 100px;
            padding: 15px;
            font-size: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            resize: none;
            background-color: #FFFFFF;
            margin-bottom: 15px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            transition: all 0.2s;
        }

        #chat-input:focus {
            outline: none;
            border-color: #25D366;
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
        }

        #chat-input::placeholder {
            color: #999;
        }

        #send-btn {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        #send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
        }

        #send-btn:active {
            transform: translateY(0);
        }

        .typing-indicator {
            display: none;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }

        .typing-indicator.show {
            display: flex;
        }

        .dot {
            width: 6px;
            height: 6px;
            background: #666;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 480px) {
            #chat-modal {
                right: 10px;
                left: 10px;
                width: auto;
                bottom: 100px;
            }
            
            #floating-chat-badge {
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div id="floating-chat-badge">
        <i class="socicon-whatsapp" style="font-size: 30px;"></i>
    </div>

    <div id="chat-modal">
        <div id="chat-header">
            How can we assist you?
            <button id="close-modal-btn">×</button>
        </div>
        <div class="chat-body">
            <div class="quick-actions">
                <a href="https://booking.balidiving.com/" target="_blank" class="small-btn">📋 Enquire</a>
                <a href="https://booking.balidiving.com/pricelist/?ref=chat" target="_blank" class="small-btn">💰 Pricelist</a>
                <a href="https://booking.balidiving.com/faqs-whatsapp/" target="_blank" class="small-btn">❓ FAQs</a>
                <a href="special-offers.php" class="small-btn">🎉 Promo</a>
                <a href="https://www.google.com/search?hl=id-ID&gl=id&q=Bali+Diving,+Jl.+Bypass+Ngurah+Rai+No.46E,+Sanur+Kauh,+Denpasar+Selatan,+Kota+Denpasar,+Bali+80025&ludocid=15746327589465121828&lsig=AB86z5WTHCJCBqpcFBjlVcAqUDrB#lrd=0x2dd241bc3e6d6237:0xda863183d7006424,3" target="_blank" class="small-btn" style="grid-column: span 2;">⭐ Reviews</a>
            </div>
            <div class="divider-text">Or send WhatsApp message</div>
            <textarea id="chat-input" placeholder="Write your name and message here..."></textarea>
            <button id="send-btn">
                <i class="socicon-whatsapp"></i>
                Send Message
            </button>
            <div class="typing-indicator">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span style="margin-left: 5px;">Agent is typing...</span>
            </div>
        </div>
    </div>

    <script>
        const chatBadge = document.getElementById('floating-chat-badge');
        const chatModal = document.getElementById('chat-modal');
        const chatInput = document.getElementById('chat-input');
        const sendBtn = document.getElementById('send-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const typingIndicator = document.querySelector('.typing-indicator');

        // Open modal when badge is clicked
        chatBadge.addEventListener('click', () => {
            chatModal.classList.add('show');
            chatInput.focus();
        });

        // Close modal when close button is clicked
        closeModalBtn.addEventListener('click', () => {
            chatModal.classList.remove('show');
        });

        // Close modal when clicking outside
        document.addEventListener('click', (e) => {
            if (!chatModal.contains(e.target) && !chatBadge.contains(e.target)) {
                chatModal.classList.remove('show');
            }
        });

        // Send message via WhatsApp
        sendBtn.addEventListener('click', sendMessage);
        
        // Send on Enter (but not Shift+Enter)
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        function sendMessage() {
            const message = chatInput.value.trim();
            if (message) {
                // Show typing indicator briefly
                typingIndicator.classList.add('show');
                
                setTimeout(() => {
                    const phoneNumber = '+6287861190174';
                    const fullMessage = message + '\n\n_sent from website balidiving.com_';
                    const encodedMessage = encodeURIComponent(fullMessage);
                    const whatsappUrl = `https://api.whatsapp.com/send?phone=${phoneNumber}&text=${encodedMessage}`;

                    window.open(whatsappUrl, '_blank');

                    chatInput.value = '';
                    chatModal.classList.remove('show');
                    typingIndicator.classList.remove('show');
                }, 800);
            } else {
                // Shake animation for empty input
                chatInput.style.animation = 'shake 0.5s';
                setTimeout(() => {
                    chatInput.style.animation = '';
                }, 500);
            }
        }

        // Add shake animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(style);

        // Character counter
        chatInput.addEventListener('input', () => {
            const remaining = 500 - chatInput.value.length;
            if (remaining < 50) {
                chatInput.style.borderColor = '#ff9800';
            } else {
                chatInput.style.borderColor = '';
            }
        });
    </script>
</body>
</html>