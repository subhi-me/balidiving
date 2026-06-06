
    <style>
        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ocean-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #4facfe 100%);
            z-index: -2;
        }

        .bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out;
        }

        .bubble:nth-child(1) { left: 10%; width: 20px; height: 20px; animation-delay: 0s; }
        .bubble:nth-child(2) { left: 20%; width: 30px; height: 30px; animation-delay: 1s; }
        .bubble:nth-child(3) { left: 35%; width: 15px; height: 15px; animation-delay: 2s; }
        .bubble:nth-child(4) { left: 50%; width: 25px; height: 25px; animation-delay: 3s; }
        .bubble:nth-child(5) { left: 70%; width: 18px; height: 18px; animation-delay: 4s; }
        .bubble:nth-child(6) { left: 85%; width: 22px; height: 22px; animation-delay: 5s; }

        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .subtitle {
            color: #4facfe;
            font-size: 1.2rem;
            margin-bottom: 30px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .treasure-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
            position: relative;
            z-index: 1;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        label {
            display: block;
            color: #1e3c72;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px 20px;
            border: 3px solid #e0e7ff;
            border-radius: 15px;
            font-size: 1.1rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 0 0 20px rgba(79, 172, 254, 0.3);
            transform: scale(1.02);
        }

        .redeem-btn {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            color: white;
            border: none;
            padding: 18px 40px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
            position: relative;
            z-index: 1;
        }

        .redeem-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 107, 107, 0.4);
        }

        .redeem-btn:active {
            transform: translateY(-1px);
        }

        .result {
            margin-top: 30px;
            padding: 25px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.1rem;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
            position: relative;
            z-index: 1;
        }

        .result.show {
            opacity: 1;
            transform: translateY(0);
        }

        .success {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
            border: 3px solid #4facfe;
        }

        .error {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            border: 3px solid #ff6b6b;
        }

        .discount-amount {
            font-size: 2.5rem;
            color: #ffd700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin: 15px 0;
            animation: glow 2s infinite alternate;
        }

        @keyframes glow {
            from { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3), 0 0 10px #ffd700; }
            to { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3), 0 0 20px #ffd700, 0 0 30px #ffd700; }
        }

        .fun-text {
            color: #666;
            font-style: italic;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .logo {
                font-size: 2rem;
            }
            
            .treasure-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="ocean-bg"></div>
    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <main class="container">
        <h1 class="logo">🏝️ BALI DIVING</h1>
        <p class="subtitle">Dive into Amazing Discounts!</p>
        
        <div class="treasure-icon">🏴‍☠️</div>
        
        <form id="voucherForm">
            <div class="form-group">
                <label for="voucherCode">Enter Your Treasure Code:</label>
                <input type="text" id="voucherCode" name="voucherCode" placeholder="DIVE24" maxlength="8" required>
            </div>
            
            <button type="submit" class="redeem-btn">🌊 Claim Your Treasure! 🌊</button>
        </form>
        
        <div id="result" class="result"></div>
        
        <p class="fun-text">
            🐠 Psst... Try codes like CORAL, TURTLE, or MANTA! 🐠
        </p>
    </main>

    <script>
        const voucherCodes = {
            'CORAL': { actualDiscount: 4, message: 'Coral Reef Explorer Discount!' },
            'TURTLE': { actualDiscount: 3, message: 'Sea Turtle Adventure Savings!' },
            'MANTA': { actualDiscount: 6, message: 'Manta Ray Encounter Special!' },
            'DIVE24': { actualDiscount: 5, message: 'New Year Diving Bonanza!' },
            'NEMO': { actualDiscount: 2, message: 'Finding Nemo Fun Dive!' },
            'SHARK': { actualDiscount: 6, message: 'Shark Encounter Mega Deal!' },
            'REEF': { actualDiscount: 4, message: 'Ultimate Reef Explorer Package!' }
        };

        function getRandomDiscount() {
            return Math.floor(Math.random() * 100) + 1; // 1-100%
        }

        function animateDiscountCounter(finalDiscount, callback) {
            const discountElement = document.querySelector('.discount-amount');
            let currentDiscount = 1;
            const increment = Math.max(1, Math.floor(100 / 30)); // Show about 30 numbers
            
            const interval = setInterval(() => {
                discountElement.textContent = `${currentDiscount}%`;
                currentDiscount += increment;
                
                if (currentDiscount >= 100) {
                    clearInterval(interval);
                    // Show final random number briefly
                    const randomShow = getRandomDiscount();
                    discountElement.textContent = `${randomShow}%`;
                    
                    setTimeout(() => {
                        discountElement.textContent = `${finalDiscount}%`;
                        callback();
                    }, 800);
                }
            }, 50);
        }

        document.getElementById('voucherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const voucherInput = document.getElementById('voucherCode');
            const resultDiv = document.getElementById('result');
            const code = voucherInput.value.trim().toUpperCase();
            
            // Reset result display
            resultDiv.classList.remove('show', 'success', 'error');
            
            setTimeout(() => {
                if (voucherCodes[code]) {
                    const voucher = voucherCodes[code];
                    resultDiv.className = 'result success show';
                    resultDiv.innerHTML = `
                        <div>🎉 TREASURE FOUND! 🎉</div>
                        <div class="discount-amount">0% OFF</div>
                        <div>${voucher.message}</div>
                        <div style="margin-top: 15px; font-size: 0.9rem;">
                            🏊‍♀️ Ready to explore Bali's underwater paradise? 🏊‍♂️
                        </div>
                        <button id="claimBtn" class="redeem-btn" style="margin-top: 20px; background: linear-gradient(135deg, #4facfe, #00f2fe); display: none;">
                            🏊‍♂️ Claim Your Discount! 🏊‍♀️
                        </button>
                    `;
                    
                    // Start the exciting discount animation
                    animateDiscountCounter(voucher.actualDiscount, () => {
                        // Animation complete - show claim button
                        const claimBtn = document.getElementById('claimBtn');
                        claimBtn.style.display = 'inline-block';
                        claimBtn.onclick = () => {
                            window.open(`https://balidiving.com/promo/claim?d=${voucher.actualDiscount}`, '_blank', 'noopener,noreferrer');
                        };
                    });
                } else if (code === '') {
                    resultDiv.className = 'result error show';
                    resultDiv.innerHTML = `
                        <div>🤔 Oops!</div>
                        <div>Please enter a treasure code to continue your adventure!</div>
                    `;
                } else {
                    resultDiv.className = 'result error show';
                    resultDiv.innerHTML = `
                        <div>🗺️ Treasure Not Found!</div>
                        <div>This code doesn't exist in our treasure map.</div>
                        <div style="margin-top: 10px; font-size: 0.9rem;">
                            Try one of the hint codes below! 🐠
                        </div>
                    `;
                }
            }, 300);
        });

        // Add some interactive effects
        document.getElementById('voucherCode').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>

