<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adventure Discount Wheel</title>
    <style>
        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100%;
        }

        html {
            height: 100%;
        }

        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
        }

        .title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        .wheel-container {
            position: relative;
            width: 320px;
            height: 320px;
            margin: 0 auto 30px;
        }

        .wheel {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            border: 8px solid #333;
            transition: transform 4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .segment {
            position: absolute;
            width: 50%;
            height: 50%;
            transform-origin: 100% 100%;
            clip-path: polygon(0 0, 100% 0, 87% 87%, 0 100%);
        }

        .segment-content {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 20px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
            transform: rotate(-13.85deg);
            padding-left: 30px;
            padding-top: 20px;
        }

        .segment:nth-child(1) { 
            background: linear-gradient(135deg, #ff6b6b, #ee5a52); 
            transform: rotate(0deg); 
        }
        .segment:nth-child(2) { 
            background: linear-gradient(135deg, #4ecdc4, #44a08d); 
            transform: rotate(27.69deg); 
        }
        .segment:nth-child(3) { 
            background: linear-gradient(135deg, #45b7d1, #3498db); 
            transform: rotate(55.38deg); 
        }
        .segment:nth-child(4) { 
            background: linear-gradient(135deg, #96ceb4, #2ecc71); 
            transform: rotate(83.07deg); 
        }
        .segment:nth-child(5) { 
            background: linear-gradient(135deg, #feca57, #f39c12); 
            transform: rotate(110.76deg); 
        }
        .segment:nth-child(6) { 
            background: linear-gradient(135deg, #ff9ff3, #e91e63); 
            transform: rotate(138.45deg); 
        }
        .segment:nth-child(7) { 
            background: linear-gradient(135deg, #54a0ff, #2980b9); 
            transform: rotate(166.14deg); 
        }
        .segment:nth-child(8) { 
            background: linear-gradient(135deg, #5f27cd, #8e44ad); 
            transform: rotate(193.83deg); 
        }
        .segment:nth-child(9) { 
            background: linear-gradient(135deg, #00d2d3, #1abc9c); 
            transform: rotate(221.52deg); 
        }
        .segment:nth-child(10) { 
            background: linear-gradient(135deg, #ff6348, #e74c3c); 
            transform: rotate(249.21deg); 
        }
        .segment:nth-child(11) { 
            background: linear-gradient(135deg, #a55eea, #9b59b6); 
            transform: rotate(276.9deg); 
        }
        .segment:nth-child(12) { 
            background: linear-gradient(135deg, #26de81, #27ae60); 
            transform: rotate(304.59deg); 
        }
        .segment:nth-child(13) { 
            background: linear-gradient(135deg, #fd79a8, #e91e63); 
            transform: rotate(332.28deg); 
        }

        .pointer {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 40px solid #333;
            z-index: 10;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
        }

        .center-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #333, #555);
            border-radius: 50%;
            z-index: 5;
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
            border: 3px solid #fff;
        }

        .spin-button {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
            border: none;
            padding: 18px 45px;
            font-size: 1.3rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.4);
            margin: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .spin-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 107, 107, 0.5);
        }

        .spin-button:disabled {
            background: linear-gradient(45deg, #bbb, #999);
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .result {
            margin-top: 25px;
            padding: 25px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
            font-size: 1.6rem;
            font-weight: bold;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .result.show {
            opacity: 1;
            transform: translateY(0);
        }

        .instructions {
            margin-top: 25px;
            color: #666;
            font-size: 1rem;
            line-height: 1.5;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }

        .spinning .segment {
            animation: sparkle 0.1s infinite;
        }

        .spinning-numbers {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            text-shadow: 2px 2px 4px rgba(255,255,255,0.8);
            z-index: 15;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .spinning-numbers.show {
            opacity: 1;
        }

        .review-section {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
            margin: 30px 0;
        }

        .review-section.show {
            opacity: 1;
            transform: translateY(0);
        }

        .review-button {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            border: none;
            padding: 18px 35px;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(76, 175, 80, 0.4);
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .review-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(76, 175, 80, 0.5);
        }

        .wheel-container.hidden {
            opacity: 0.3;
            pointer-events: none;
            filter: blur(2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">🌟 Adventure Discount Wheel</h1>
        <p class="subtitle">Spin to unlock amazing savings for your next trip!</p>
        
        <div class="wheel-container">
            <div class="pointer"></div>
            <div class="wheel" id="wheel">
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>
                <div class="segment">
                    <div class="segment-content"></div>
                </div>

            </div>
            <div class="center-circle"></div>
            <div class="spinning-numbers" id="spinningNumbers"></div>
        </div>

        <button class="spin-button" id="spinButton" onclick="spinWheel()">
            🎯 Spin for Savings
        </button>
        
        <div class="review-section" id="reviewSection">
            <p style="color: #666; margin-bottom: 20px; font-size: 1.1rem;">
                ⭐ While you wait, help others discover our amazing diving experiences!
            </p>
            <a href="https://www.google.com/search?hl=id-ID&gl=id&q=Bali+Diving,+Jl.+Bypass+Ngurah+Rai+No.46E,+Sanur+Kauh,+Denpasar+Selatan,+Kota+Denpasar,+Bali+80025&ludocid=1828&lsig=AB86z5WTHCJCBqpcFBjlVcAqUDrB#lrd=0x2dd241bc3e6d6237:0xda863183d7006424,3" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="review-button">
                ⭐ Review Us While You Wait
            </a>
        </div>
        
        <div class="result" id="result"></div>
        
        <p class="instructions">
            Click the button or press Enter to spin!
        </p>
    </div>

    <script>
        let isSpinning = false;
        let currentRotation = 0;
        let lastSpinTime = 0;
        let spinCount = 0;
        let totalDiscount = 0;
        const COOLDOWN_TIME = 60000; // 1 minute in milliseconds

        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const nama = urlParams.get('n');
        const email = urlParams.get('e');
        const whatsapp = urlParams.get('wa');

        // Check if required parameters exist
        const hasRequiredParams = nama && email && whatsapp;

        document.addEventListener('DOMContentLoaded', function() {
            const spinButton = document.getElementById('spinButton');
            const wheelContainer = document.getElementById('wheel').parentElement;
            const instructions = document.querySelector('.instructions');
            
            if (!hasRequiredParams) {
                spinButton.disabled = true;
                spinButton.textContent = '🔒 Access Required';
                spinButton.style.background = 'linear-gradient(45deg, #bbb, #999)';
                instructions.innerHTML = 'Access denied. Required parameters missing: name (n), email (e), and WhatsApp number (wa).';
                wheelContainer.style.opacity = '0.3';
                wheelContainer.style.filter = 'blur(2px)';
                return;
            }
            
            spinButton.focus();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && !isSpinning && hasRequiredParams) {
                spinWheel();
            }
        });

        function spinWheel() {
            if (isSpinning || !hasRequiredParams) return;
            
            const currentTime = Date.now();
            const timeSinceLastSpin = currentTime - lastSpinTime;
            
            if (timeSinceLastSpin < COOLDOWN_TIME && lastSpinTime > 0) {
                const remainingTime = Math.ceil((COOLDOWN_TIME - timeSinceLastSpin) / 1000);
                const button = document.getElementById('spinButton');
                button.textContent = `⏰ Wait ${remainingTime}s`;
                return;
            }
            
            isSpinning = true;
            spinCount++;
            lastSpinTime = currentTime;
            const wheel = document.getElementById('wheel');
            const button = document.getElementById('spinButton');
            const result = document.getElementById('result');
            const spinningNumbers = document.getElementById('spinningNumbers');
            
            button.disabled = true;
            button.textContent = '⏳ Spinning...';
            result.classList.remove('show');
            wheel.classList.add('spinning');
            
            // Show spinning numbers
            spinningNumbers.classList.add('show');
            
            // Animate numbers while spinning
            const percentages = [];
            const dollars = [];
            for (let i = 1; i <= 20; i++) {
                percentages.push(i + '%');
            }
            for (let i = 5; i <= 25; i++) {
                dollars.push('$' + i);
            }
            
            let numberIndex = 0;
            let isPercentage = true;
            const numberInterval = setInterval(() => {
                if (isPercentage) {
                    spinningNumbers.textContent = percentages[numberIndex % percentages.length];
                } else {
                    spinningNumbers.textContent = dollars[numberIndex % dollars.length];
                }
                numberIndex++;
                isPercentage = !isPercentage;
            }, 100);
            
            // 5 full rotations + target position
            const fullRotations = 1800;
            let selectedSegment;
            
            if (spinCount === 1) {
                // First spin: 4%, 5%, 6%, 7%
                const validSegments = [
                    { discount: 4, angle: 83.07 },   // segment 4
                    { discount: 5, angle: 0 },       // segment 1  
                    { discount: 6, angle: 138.45 },  // segment 6
                    { discount: 7, angle: 249.21 }   // segment 10
                ];
                selectedSegment = validSegments[Math.floor(Math.random() * validSegments.length)];
            } else {
                // Second spin: always 2%
                selectedSegment = { discount: 2, angle: 27.69 }; // segment 2
            }
            
            const variation = (Math.random() - 0.5) * 15;
            const targetAngle = selectedSegment.angle + variation;
            const finalRotation = fullRotations + (360 - targetAngle);
            
            currentRotation += finalRotation;
            wheel.style.transform = `rotate(${currentRotation}deg)`;
            
            // Natural bounce effect
            setTimeout(() => {
                wheel.style.transition = 'transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                wheel.style.transform = `rotate(${currentRotation - 12}deg)`;
                
                setTimeout(() => {
                    wheel.style.transition = 'transform 0.3s ease-out';
                    wheel.style.transform = `rotate(${currentRotation - 3}deg)`;
                    
                    setTimeout(() => {
                        wheel.style.transition = 'transform 0.2s ease-in-out';
                        wheel.style.transform = `rotate(${currentRotation}deg)`;
                        
                        setTimeout(() => {
                            wheel.style.transition = 'transform 4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                        }, 200);
                    }, 300);
                }, 400);
            }, 3600);
            
            setTimeout(() => {
                wheel.classList.remove('spinning');
                clearInterval(numberInterval);
                
                // Hide spinning numbers
                spinningNumbers.classList.remove('show');
                
                // Add to total discount
                totalDiscount += selectedSegment.discount;
                
                if (spinCount < 2) {
                    result.innerHTML = `
                        🎉 Fantastic! 🎉<br>
                        You've unlocked <strong>${selectedSegment.discount}% OFF</strong> your next adventure!<br>
                        <small>Total so far: ${totalDiscount}%</small>
                    `;
                    result.classList.add('show');
                    
                    button.disabled = false;
                    isSpinning = false;
                    button.focus();
                    
                    // Start cooldown timer
                    startCooldownTimer();
                } else {
                    // Second spin completed - show final screen
                    showFinalScreen();
                }
            }, 4500);
        }

        function startCooldownTimer() {
            const button = document.getElementById('spinButton');
            const wheelContainer = document.getElementById('wheel').parentElement;
            const reviewSection = document.getElementById('reviewSection');
            
            // Hide wheel and show review section
            wheelContainer.classList.add('hidden');
            reviewSection.classList.add('show');
            
            const updateTimer = () => {
                const currentTime = Date.now();
                const timeSinceLastSpin = currentTime - lastSpinTime;
                const remainingTime = Math.ceil((COOLDOWN_TIME - timeSinceLastSpin) / 1000);
                
                if (remainingTime > 0) {
                    button.textContent = `⏰ Wait ${remainingTime}s`;
                    button.disabled = true;
                    setTimeout(updateTimer, 1000);
                } else {
                    button.textContent = '🎯 Spin for Savings';
                    button.disabled = false;
                    // Show wheel and hide review section
                    wheelContainer.classList.remove('hidden');
                    reviewSection.classList.remove('show');
                }
            };
            
            updateTimer();
        }

        function showFinalScreen() {
            // Hide all elements
            document.querySelector('.title').style.display = 'none';
            document.querySelector('.subtitle').style.display = 'none';
            document.querySelector('.wheel-container').style.display = 'none';
            document.getElementById('spinButton').style.display = 'none';
            document.getElementById('reviewSection').style.display = 'none';
            document.getElementById('result').style.display = 'none';
            document.querySelector('.instructions').style.display = 'none';
            
            // Create final screen
            const container = document.querySelector('.container');
            const finalScreen = document.createElement('div');
            finalScreen.innerHTML = `
                <div style="text-align: center; padding: 40px 20px;">
                    <h1 style="font-size: 3rem; color: #333; margin-bottom: 20px;">🎉 Congratulations!</h1>
                    <div style="background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 30px; border-radius: 20px; margin: 30px 0; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
                        <h2 style="font-size: 2.5rem; margin: 0 0 10px 0;">Total Discount Earned</h2>
                        <div style="font-size: 4rem; font-weight: bold; margin: 20px 0;">${totalDiscount}% OFF</div>
                        <p style="font-size: 1.2rem; margin: 0;">Your amazing diving adventure awaits!</p>
                    </div>
                    <a href="https://balidiving.com/promo/bonus/?v=${totalDiscount}&n=${encodeURIComponent(nama)}&e=${encodeURIComponent(email)}&wa=${encodeURIComponent(whatsapp)}" 
                       target="" 
                       rel="noopener noreferrer"
                       style="background: linear-gradient(45deg, #4CAF50, #45a049); color: white; text-decoration: none; padding: 20px 40px; font-size: 1.4rem; font-weight: bold; border-radius: 50px; display: inline-block; margin: 20px; box-shadow: 0 15px 35px rgba(76, 175, 80, 0.4); transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px;"
                       onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 20px 40px rgba(76, 175, 80, 0.5)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 15px 35px rgba(76, 175, 80, 0.4)';">
                        🎁 Claim Your Discount/Bonus
                    </a>
                    <p style="color: #666; margin-top: 30px; font-size: 1rem;">
                        Thank you for playing! Your discount is ready to be claimed.
                    </p>
                </div>
            `;
            
            container.appendChild(finalScreen);
        }

        window.addEventListener('load', function() {
            if (hasRequiredParams) {
                document.getElementById('spinButton').focus();
            }
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98d46d02c5d5f8e6',t:'MTc2MDI0OTMzOS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
