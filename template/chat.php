<!-- Chat Widget UI -->
  <div class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] z-40 hidden" id="chatWidget">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
      <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
              <img src="bali-diving-logo.svg" alt="Bali Diving Logo">
            </div>
            <div>
              <h1 class="text-lg font-semibold text-white">Diving Expert</h1>
              <p class="text-sm text-blue-100 flex items-center">
                <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                Online now
              </p>
            </div>
          </div>
          <button onclick="toggleChat()" class="text-white hover:text-blue-200 transition-colors" aria-label="Close chat">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
          </button>
        </div>
      </div>
      <div class="bg-white" style="height: 400px; overflow-y: auto;" id="chatContainer">
        <div class="p-4 space-y-3" id="chatMessages"></div>
      </div>
      <div class="bg-gray-50 p-4 border-t border-gray-100">
        <div class="flex space-x-2">
          <input type="text" id="userInput" placeholder="Type your message..."
                 class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          <button onclick="sendMessage()" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full px-4 py-2 text-sm font-medium transition-colors">
            Send
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="fixed bottom-6 right-6 z-50" id="chatLauncher">
    <button onclick="toggleChat()"
            class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center group"
            aria-label="Open chat">
      <svg id="chatIcon" class="w-6 h-6 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
      </svg>
      <svg id="closeIcon" class="w-6 h-6 transition-transform duration-300 hidden" fill="currentColor" viewBox="0 0 24 24">
        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
      </svg>
      <span class="ml-2 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap" id="launcherText">
        Chat with us
      </span>
    </button>
  </div>

<script>
    // --- Chat Assistant Script ---
    let currentStep = 'greeting';
    let userProfile = {
        intention: '',
        hasCertificate: null,
        selectedLocation: ''
    };
    let chatIsOpen = false;
    let inactivityTimer = null;
    let hasShownFollowUp = false;
    let autoOpenTimer = null;
    let hasAutoOpened = false;
    let userName = '';
    let waitingForName = false;

    // Global function to toggle chat visibility from other scripts
    window.toggleChatVisibility = function (show) {
        const chatWidget = document.getElementById('chatWidget');
        const chatLauncher = document.getElementById('chatLauncher');

        if (show) {
            if (chatLauncher) chatLauncher.classList.remove('hidden');
            // Restore widget visibility if it was open
            if (chatIsOpen && chatWidget) {
                chatWidget.classList.remove('hidden');
            }
        } else {
            if (chatWidget) chatWidget.classList.add('hidden');
            if (chatLauncher) chatLauncher.classList.add('hidden');
        }
    };

    // Chat History Management
    const CHAT_STORAGE_KEY = 'balidiving_chat_history';
    const CHAT_TIMESTAMP_KEY = 'balidiving_chat_timestamp';
    const CHAT_EXPIRY_HOURS = 12;

    function saveChatState() {
        sessionStorage.setItem('userProfile', JSON.stringify(userProfile));
    }

    function saveChatHistory() {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            const messages = [];
            chatMessages.querySelectorAll('.chat-bubble').forEach(bubble => {
                messages.push({
                    html: bubble.innerHTML,
                    isBot: bubble.classList.contains('justify-start')
                });
            });

            localStorage.setItem(CHAT_STORAGE_KEY, JSON.stringify(messages));
            localStorage.setItem(CHAT_TIMESTAMP_KEY, Date.now().toString());
        }
    }

    function loadChatHistory() {
        const timestamp = localStorage.getItem(CHAT_TIMESTAMP_KEY);
        const messages = localStorage.getItem(CHAT_STORAGE_KEY);

        if (!timestamp || !messages) {
            return false;
        }

        // Check if chat is older than CHAT_EXPIRY_HOURS
        const ageInHours = (Date.now() - parseInt(timestamp)) / (1000 * 60 * 60);

        if (ageInHours > CHAT_EXPIRY_HOURS) {
            // Clear old chat
            clearChatHistory();
            return false;
        }

        // Load messages
        try {
            const parsedMessages = JSON.parse(messages);
            const chatMessages = document.getElementById('chatMessages');

            parsedMessages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-bubble ${msg.isBot ? 'flex justify-start' : 'flex justify-end'}`;
                messageDiv.innerHTML = msg.html;
                chatMessages.appendChild(messageDiv);
            });

            return parsedMessages.length > 0;
        } catch (e) {
            console.error('Error loading chat history:', e);
            clearChatHistory();
            return false;
        }
    }

    function clearChatHistory() {
        localStorage.removeItem(CHAT_STORAGE_KEY);
        localStorage.removeItem(CHAT_TIMESTAMP_KEY);
    }

    function resetChat() {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.innerHTML = '';
        }
        clearChatHistory();
        currentStep = 'greeting';
        hasShownFollowUp = false;
        userName = '';
        waitingForName = false;
        userProfile = {
            intention: '',
            hasCertificate: null,
            selectedLocation: ''
        };
        // Restart chat
        setTimeout(initializeChat, 500);
    }

    const divingIntentions = [
        'First time diving experience',
        'Explore coral reefs',
        'See tropical fish',
        'Wreck diving adventure',
        'Diving with manta rays',
        'Snorkeling',
        'Other things'
    ];

    const divingLocations = [
        { name: 'USAT Liberty Wreck, Tulamben', link: 'https://example.com/tulamben' },
        { name: 'Manta Point, Nusa Penida', link: 'https://example.com/manta-point' },
        { name: 'Crystal Bay, Nusa Penida', link: 'https://example.com/crystal-bay' },
        { name: 'Blue Lagoon, Padang Bai', link: 'https://example.com/blue-lagoon' },
        { name: 'Menjangan Island', link: 'https://example.com/menjangan' },
        { name: 'Amed Coral Gardens', link: 'https://example.com/amed' },
        { name: 'Sanur Reef', link: 'https://example.com/sanur' },
        { name: 'Pemuteran Bay', link: 'https://example.com/pemuteran' }
    ];

    const beginnerDivingLocations = [
        { name: 'Sanur Reef - Perfect for First Timers', link: 'https://example.com/sanur-beginner' },
        { name: 'Blue Lagoon, Padang Bai - Calm Waters', link: 'https://example.com/blue-lagoon-beginner' },
        { name: 'Pemuteran Bay - Shallow & Protected', link: 'https://example.com/pemuteran-beginner' },
        { name: 'Jemeluk Bay, Amed - Easy Entry', link: 'https://example.com/jemeluk-beginner' },
        { name: 'Bias Tugel Beach - Gentle Currents', link: 'https://example.com/bias-tugel-beginner' },
        { name: 'Candidasa Reef - Beginner Friendly', link: 'https://example.com/candidasa-beginner' }
    ];

    const mantaRayLocations = [
        { name: 'Manta Point, Nusa Penida - Main Manta Site', link: 'https://example.com/manta-point' },
        { name: 'Manta Bay, Nusa Penida - Cleaning Station', link: 'https://example.com/manta-bay' },
        { name: 'Crystal Bay, Nusa Penida - Manta Encounters', link: 'https://example.com/crystal-bay-manta' },
        { name: 'Kelingking Secret Point - Manta Sightings', link: 'https://example.com/kelingking-manta' }
    ];

    const wreckDivingLocations = [
        { name: 'USAT Liberty Wreck, Tulamben - Famous WWII Ship', link: 'https://example.com/usat-liberty' },
        { name: 'Japanese Wreck, Amed - WWII Patrol Boat', link: 'https://example.com/japanese-wreck-amed' },
        { name: 'Boga Wreck, Kubu - Traditional Fishing Vessel', link: 'https://example.com/boga-wreck' },
        { name: 'Coral Garden Wreck, Tulamben - Small Cargo Ship', link: 'https://example.com/coral-garden-wreck' }
    ];

    const divingPackages = [
        { name: '🐠 Try Scuba Diving - Tulamben', link: 'https://balidiving.diversdesk.com/product/ba944d00-feb3-400d-b46a-2d164654b7af' },
        { name: '🐠 Try Scuba Diving - Padang Bai', link: 'https://balidiving.diversdesk.com/product/6bf7bf55-c4da-44b9-b97e-09dd6a63cb55' },
        { name: '🐠 Try Scuba Diving - Amed', link: 'https://balidiving.diversdesk.com/product/ac527d58-d2b0-4304-ae39-06028690c9c7' },
        { name: '💰 View All Prices & Packages', link: 'https://balidiving.com/pricelist' }
    ];

    const certifiedUserLocations = [
        { name: 'Padang Bai', link: 'https://balidiving.diversdesk.com/product/4051a905-4b2c-4246-aae2-57e762239dc1' },
        { name: 'Gili Tepekong / Mimpang', link: 'https://balidiving.diversdesk.com/product/447d4d89-5bb4-4420-a261-2c788bb8fc5e' },
        { name: 'Sanur', link: 'https://balidiving.diversdesk.com/product/26300391-668b-405e-b69c-8964367a424d' },
        { name: 'Kubu Boga', link: 'https://balidiving.diversdesk.com/product/4b8dba66-5b1d-45d1-bd15-f3551b1b4083' },
        { name: 'Amed', link: 'https://balidiving.diversdesk.com/product/16c88803-529d-41e4-91e4-70e95376a4b7' },
        { name: 'Nusa Penida Manta Point', link: 'https://balidiving.diversdesk.com/product/86ee61e4-2137-4850-83a5-46f147b14f6d' },
        { name: 'Tulamben Wreck', link: 'https://balidiving.diversdesk.com/product/710cea45-4268-4317-802c-ffc21f365362' }
    ];

    const tryScubaLocations = [
        { name: 'Tulamben Wreck', link: 'https://balidiving.diversdesk.com/product/ba944d00-feb3-400d-b46a-2d164654b7af' },
        { name: 'Amed', link: 'https://balidiving.diversdesk.com/product/ac527d58-d2b0-4304-ae39-06028690c9c7' },
        { name: 'Padang Bai', link: 'https://balidiving.diversdesk.com/product/6bf7bf55-c4da-44b9-b97e-09dd6a63cb55' },
        { name: 'Nusa Penida', link: 'https://balidiving.diversdesk.com/product/78aa6dbe-daaf-4b8b-8c1d-3a392f214a9a' }
    ];

    const snorkelingPackages = [
        { name: 'Nusa Penida', link: 'https://balidiving.diversdesk.com/product/0e41a1c6-67d1-4bed-9c3f-48314930bce0' },
        { name: 'Padang Bai', link: 'https://balidiving.diversdesk.com/product/904583c4-c2fd-470a-96b1-1621c17bcba4' },
        { name: 'Amed', link: 'https://balidiving.diversdesk.com/product/d269ecf7-dd85-4402-bce5-62a899674a2c' },
        { name: 'Tulamben Wreck', link: 'https://balidiving.diversdesk.com/product/04911d70-94a3-4bb6-bf0a-472783bb4ae8' }
    ];

    function addMessage(message, isBot = true, isButtons = false) {
        const chatMessages = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-bubble ${isBot ? 'flex justify-start' : 'flex justify-end'}`;

        if (isButtons) {
            messageDiv.innerHTML = message;
        } else {
            messageDiv.innerHTML = `
            <div class="${isBot ? 'bg-gray-100 text-gray-800' : 'bg-blue-500 text-white'} rounded-2xl px-4 py-3 max-w-xs lg:max-w-md">
                ${message}
            </div>
        `;
        }

        chatMessages.appendChild(messageDiv);

        // Ensure accurate scrolling after DOM update
        setTimeout(() => {
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'end' });
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 50);

        // Show unread badge if chat is closed and message is from bot
        if (isBot && !chatIsOpen) {
            showUnreadBadge();
        }

        // Save chat history to localStorage
        saveChatHistory();
    }

    function showUnreadBadge() {
        const badge = document.getElementById('unreadBadge');
        if (badge) {
            badge.classList.remove('hidden');
            badge.classList.add('animate-bounce');
        }
    }

    function hideUnreadBadge() {
        const badge = document.getElementById('unreadBadge');
        if (badge) {
            badge.classList.add('hidden');
            badge.classList.remove('animate-bounce');
        }
    }

    function createButtonGroup(buttons, callback) {
        let buttonsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><div class="grid gap-2">';

        buttons.forEach((button, index) => {
            buttonsHtml += `
            <button onclick="${callback}(${index})"
                    class="button-hover bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 text-left">
                ${button}
            </button>
        `;
        });

        buttonsHtml += '</div></div></div></div>';
        return buttonsHtml;
    }

    function triggerNextStepForIntention(intention) {
        if (intention === 'First time diving experience') {
            setTimeout(() => {
                addMessage(`No worries at all! We've got some amazing beginner-friendly spots, and we can set up training if you'd like. Here are some perfect places to start in Bali:`);
                setTimeout(() => {
                    showBeginnerDivingLocations();
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        } else if (intention === 'Diving with manta rays') {
            setTimeout(() => {
                addMessage(`Oh wow, great choice! Manta rays are absolutely incredible. Here's where you can see these gentle giants in Bali:`);
                setTimeout(() => {
                    showMantaRayLocations();
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        } else if (intention === 'Wreck diving adventure') {
            setTimeout(() => {
                addMessage(`Love it! Wreck diving's got both amazing history and tons of marine life. Check out the best shipwreck sites in Bali:`);
                setTimeout(() => {
                    showWreckDivingLocations();
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        } else if (intention === 'Snorkeling') {
            setTimeout(() => {
                addMessage(`Snorkeling's awesome! You can see so much of Bali's beautiful marine life right from the surface - no certification needed! It's perfect for anyone. Here are our most popular packages:`);
                setTimeout(() => {
                    showSnorkelingPackages();
                    setTimeout(() => {
                        const namePrefix = userName ? `You're gonna love it, ${userName}! ` : 'You\'re gonna love it! ';
                        addMessage(`${namePrefix}Just click any package to check out the details and book. 🐠`);
                        currentStep = 'completed';
                    }, 2000);
                }, 1000);
            }, 1000);
        } else if (intention === 'Other things') {
            setTimeout(() => {
                addMessage(`Perfect! We've got lots of different diving experiences. Let me connect you with our team - they'll help you figure out exactly what you're looking for:`);
                setTimeout(() => {
                    showCustomerServiceOptions();
                    setTimeout(() => {
                        const namePrefix = userName ? `Thanks for choosing us, ${userName}! ` : 'Thanks for choosing us! ';
                        addMessage(`${namePrefix}Our team's here to help you find your perfect dive. 🌊🐠`);
                        currentStep = 'completed';
                    }, 1500);
                }, 1000);
            }, 1000);
        } else {
            setTimeout(() => {
                addMessage(`Nice! Quick question - do you have a diving certification? Like PADI, SSI, or anything like that?`);
                setTimeout(() => {
                    const certButtons = createButtonGroup(["Yes, I'm certified", "No, I'm a beginner"], 'selectCertification');
                    addMessage(certButtons, true, true);
                    currentStep = 'certification';
                    startInactivityTimer();
                }, 500);
            }, 1000);
        }
    }

    function selectIntention(index) {
        userProfile.intention = divingIntentions[index];
        saveChatState();
        addMessage(divingIntentions[index], false);
        startInactivityTimer();
        triggerNextStepForIntention(userProfile.intention);
    }

    function selectCertification(index) {
        userProfile.hasCertificate = index === 0;
        saveChatState();
        const response = index === 0 ? "Yes, I'm certified" : "No, I'm a beginner";
        addMessage(response, false);
        startInactivityTimer();

        if (userProfile.hasCertificate) {
            setTimeout(() => {
                addMessage(`Awesome! Since you're certified, you can dive at all our sites. Here are some really amazing spots I'd recommend:`);
                setTimeout(() => {
                    showDivingLocations();
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        } else {
            setTimeout(() => {
                addMessage(`No problem at all! We've got great beginner-friendly sites. Would you be interested in getting PADI certified? Like Open Water or higher? Once you're certified, you'll get way more options and better prices too! 🏆`);
                setTimeout(() => {
                    const certButtons = createButtonGroup(["Yes, I want to get certified", "No, I want without certificate"], 'selectCertificationInterest');
                    addMessage(certButtons, true, true);
                    currentStep = 'certification_interest';
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        }
    }

    function selectCertificationInterest(index) {
        const response = index === 0 ? "Yes, I want to get certified" : "No, I want without certificate";
        addMessage(response, false);
        startInactivityTimer();

        if (index === 0) {
            setTimeout(() => {
                addMessage(`Great choice! PADI certification really opens up a whole new world of diving. Here are our most popular courses:`);
                setTimeout(() => {
                    const certificationHtml = `
                    <div class="flex justify-start">
                        <div class="max-w-md">
                            <div class="bg-gray-100 rounded-2xl p-4">
                                <div class="space-y-1">
                                    <p class="font-semibold text-gray-800 pt-2 pb-1 text-md">PADI Open Water</p>
                                    <a href="https://balidiving.diversdesk.com/product/8f1fc6ea-5ef7-4443-9d3e-cfc2ec21cb56" target="_blank" class="button-hover w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">Open Water Diver (3 days)</a>
                                    <a href="https://balidiving.diversdesk.com/product/3eb9cb33-2d24-4473-8dac-f5af0a32817c" target="_blank" class="button-hover w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">Open Water Diver (2 days)</a>
                                    <a href="https://balidiving.diversdesk.com/product/997c8c6e-a919-4d26-b027-88bed4dda66b" target="_blank" class="button-hover w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">Open Water Referral (2 Days)</a>
                                    <p class="font-semibold text-gray-800 pt-3 pb-1 text-md">PADI Advanced Open Water Course</p>
                                    <a href="https://balidiving.diversdesk.com/product/f9abbc9b-a9f6-4526-a1f6-a42d1b2eddbc" target="_blank" class="button-hover w-full bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">Advanced Open Water (2 Days)</a>
                                    <a href="https://balidiving.diversdesk.com/product/c7ac29d0-fce5-4561-bb43-066234eeef90" target="_blank" class="button-hover w-full bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">Platinum Advanced Open Water (2 Days)</a>
                                    <p class="font-semibold text-gray-800 pt-3 pb-1 text-md">More PADI: Specialty, rescue, etc.</p>
                                    <a href="https://balidiving.diversdesk.com/product/724e030e-970d-440f-887b-166c4e43fa30" target="_blank" class="button-hover w-full bg-gray-700 hover:bg-gray-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">PADI - Rescue Diver Course</a>
                                    <a href="https://balidiving.diversdesk.com/product/2a915fe9-1dd4-46f6-8be5-91597cf1aba7" target="_blank" class="button-hover w-full bg-gray-700 hover:bg-gray-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">Wreck Diver Course (1 Day)</a>
                                    <a href="https://balidiving.diversdesk.com/product/5dd36dae-57f5-4f68-8aa5-917c367801b6" target="_blank" class="button-hover w-full bg-gray-700 hover:bg-gray-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">Peak Performance Buoyancy Course (1 Day)</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    addMessage(certificationHtml, true, true);
                    setTimeout(() => {
                        const namePrefix = userName ? `So glad you're interested in getting certified, ${userName}! ` : 'So glad you\'re interested in getting certified! ';
                        addMessage(`${namePrefix}Our instructors will make sure you become a confident and safe diver. 🌊🏆`);
                        currentStep = 'completed';
                    }, 2000);
                }, 1000);
            }, 1000);
        } else {
            setTimeout(() => {
                addMessage(`Perfect! Our 'Try Scuba Diving' program is an awesome way to experience the underwater world - no commitment needed. Here's where you can try it:`);
                setTimeout(() => {
                    showTryScubaLocations();
                    setTimeout(() => {
                        const namePrefix = userName ? `Have fun on your first dive, ${userName}! ` : 'Have fun on your first dive! ';
                        addMessage(`${namePrefix}Just click any spot to see details and book. 🐠`);
                        currentStep = 'completed';
                    }, 2000);
                }, 1000);
            }, 1000);
        }
    }

    function showTryScubaLocations() {
        let locationsHtml = `<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><p class="font-semibold text-gray-800 mb-3 text-md">Category: Try Scuba Diving</p><div class="space-y-2">`;
        tryScubaLocations.forEach(location => {
            locationsHtml += `<a href="${location.link}" target="_blank" class="button-hover w-full bg-green-500 hover:bg-green-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🐠 ${location.name}</a>`;
        });
        locationsHtml += '</div></div></div></div>';
        addMessage(locationsHtml, true, true);
    }

    function showSnorkelingPackages() {
        let packagesHtml = `<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><p class="font-semibold text-gray-800 mb-3 text-md">Snorkeling Packages</p><div class="space-y-2">`;
        snorkelingPackages.forEach(pkg => {
            packagesHtml += `<a href="${pkg.link}" target="_blank" class="button-hover w-full bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🏊 ${pkg.name}</a>`;
        });
        packagesHtml += '</div></div></div></div>';
        addMessage(packagesHtml, true, true);
    }

    function showDivingLocations() {
        let locationsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><div class="space-y-2">';
        divingLocations.forEach((location, index) => {
            locationsHtml += `<button onclick="selectLocation(${index})" class="button-hover w-full bg-blue-500 hover:bg-blue-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left">🏝️ ${location.name}</button>`;
        });
        locationsHtml += '</div></div></div></div>';
        addMessage(locationsHtml, true, true);
        currentStep = 'location';
    }

    function showBeginnerDivingLocations() {
        let locationsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><div class="space-y-2">';
        beginnerDivingLocations.forEach((location, index) => {
            locationsHtml += `<button onclick="selectBeginnerLocation(${index})" class="button-hover w-full bg-green-500 hover:bg-green-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left">🐠 ${location.name}</button>`;
        });
        locationsHtml += '</div></div></div></div>';
        addMessage(locationsHtml, true, true);
        currentStep = 'location';
    }

    function showMantaRayLocations() {
        let locationsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><div class="space-y-2">';
        mantaRayLocations.forEach((location, index) => {
            locationsHtml += `<button onclick="selectMantaLocation(${index})" class="button-hover w-full bg-purple-500 hover:bg-purple-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left">🦋 ${location.name}</button>`;
        });
        locationsHtml += '</div></div></div></div>';
        addMessage(locationsHtml, true, true);
        currentStep = 'location';
    }

    function showWreckDivingLocations() {
        let locationsHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><div class="space-y-2">';
        wreckDivingLocations.forEach((location, index) => {
            locationsHtml += `<button onclick="selectWreckLocation(${index})" class="button-hover w-full bg-gray-700 hover:bg-gray-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left">🚢 ${location.name}</button>`;
        });
        locationsHtml += '</div></div></div></div>';
        addMessage(locationsHtml, true, true);
        currentStep = 'location';
    }

    function selectLocation(index) {
        userProfile.selectedLocation = divingLocations[index].name;
        saveChatState();
        addMessage(`🏝️ ${divingLocations[index].name}`, false);
        startInactivityTimer();
        setTimeout(() => {
            addMessage(`Nice pick! ${divingLocations[index].name} is absolutely stunning. Wanna explore on your own, or would you like some help through WhatsApp?`);
            setTimeout(() => {
                const helpButtons = createButtonGroup(["I'll explore on my own", "I need help via WhatsApp"], 'selectHelp');
                addMessage(helpButtons, true, true);
                currentStep = 'help';
                startInactivityTimer();
            }, 1000);
        }, 1000);
    }

    function selectBeginnerLocation(index) {
        userProfile.selectedLocation = beginnerDivingLocations[index].name;
        saveChatState();
        addMessage(`🐠 ${beginnerDivingLocations[index].name}`, false);
        startInactivityTimer();
        setTimeout(() => {
            addMessage(`Perfect spot for beginners! ${beginnerDivingLocations[index].name} has calm, shallow waters with tons of marine life - ideal for your first time. Want to explore on your own, or need some help through WhatsApp?`);
            setTimeout(() => {
                const helpButtons = createButtonGroup(["I'll explore on my own", "I need help via WhatsApp"], 'selectHelp');
                addMessage(helpButtons, true, true);
                currentStep = 'help';
                startInactivityTimer();
            }, 1000);
        }, 1000);
    }

    function selectMantaLocation(index) {
        userProfile.selectedLocation = mantaRayLocations[index].name;
        saveChatState();
        addMessage(`🦋 ${mantaRayLocations[index].name}`, false);
        startInactivityTimer();
        setTimeout(() => {
            addMessage(`Amazing choice! ${mantaRayLocations[index].name} is one of the world's best spots to see manta rays. These incredible creatures can have wingspans up to 7 meters! Want to explore on your own, or need help through WhatsApp?`);
            setTimeout(() => {
                const helpButtons = createButtonGroup(["I'll explore on my own", "I need help via WhatsApp"], 'selectHelp');
                addMessage(helpButtons, true, true);
                currentStep = 'help';
                startInactivityTimer();
            }, 1000);
        }, 1000);
    }

    function selectWreckLocation(index) {
        userProfile.selectedLocation = wreckDivingLocations[index].name;
        saveChatState();
        addMessage(`🚢 ${wreckDivingLocations[index].name}`, false);
        startInactivityTimer();
        setTimeout(() => {
            addMessage(`Great pick! ${wreckDivingLocations[index].name} has fascinating underwater history and it's become this incredible artificial reef full of marine life. Perfect mix of history and nature! Wanna explore on your own, or need help through WhatsApp?`);
            setTimeout(() => {
                const helpButtons = createButtonGroup(["I'll explore on my own", "I need help via WhatsApp"], 'selectHelp');
                addMessage(helpButtons, true, true);
                currentStep = 'help';
                startInactivityTimer();
            }, 1000);
        }, 1000);
    }

    function showDivingPackages() {
        let packagesHtml = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><div class="space-y-2">';
        divingPackages.forEach((package, index) => {
            const isSpecial = package.name.includes('💰');
            const buttonClass = isSpecial ? 'w-full bg-red-500 hover:bg-red-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left' : 'w-full bg-blue-500 hover:bg-blue-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left';
            packagesHtml += `<a href="${package.link}" target="_blank" class="button-hover ${buttonClass} block">${package.name}</a>`;
        });
        packagesHtml += '</div></div></div></div>';
        addMessage(packagesHtml, true, true);
        setTimeout(() => {
            const namePrefix = userName ? `Thanks for choosing us, ${userName}! ` : 'Thanks for choosing us! ';
            addMessage(`${namePrefix}Can't wait to help you create some unforgettable underwater memories. 🌊🐠`);
            currentStep = 'completed';
        }, 2000);
    }

    function showCertifiedUserLocations() {
        let html = '<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4">';
        html += '<p class="font-semibold text-gray-800 mb-3">For certified divers:</p>';
        html += '<div class="space-y-2">';
        certifiedUserLocations.forEach((location) => {
            html += `<a href="${location.link}" target="_blank" class="button-hover w-full bg-blue-500 hover:bg-blue-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">${location.name}</a>`;
        });
        html += '</div></div></div></div>';
        addMessage(html, true, true);
        setTimeout(() => {
            const namePrefix = userName ? `Thanks for choosing us, ${userName}! ` : 'Thanks for choosing us! ';
            addMessage(`${namePrefix}Hope you find the perfect spot. Have an amazing time exploring! 🌊🐠`);
            currentStep = 'completed';
        }, 2000);
    }

    function showWreckDivePackages() {
        const packages = {
            certified: [{ name: 'Tulamben Wreck', link: 'https://balidiving.diversdesk.com/product/710cea45-4268-4317-802c-ffc21f365362' }, { name: 'Amed', link: 'https://balidiving.diversdesk.com/product/16c88803-529d-41e4-91e4-70e95376a4b7' }, { name: 'Special 7 days/16 Dives', link: 'https://balidiving.diversdesk.com/product/b1cab048-1475-4329-8533-c0a685f5a962' }],
            other: [{ name: 'View All Pricelist', link: 'https://balidiving.com/pricelist' },]
        };
        let html = `<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4 space-y-4">`;
        html += `<div><p class="font-semibold text-gray-800 mb-2 text-md">For Certified Diver</p><div class="space-y-2">`;
        packages.certified.forEach(pkg => {
            html += `<a href="${pkg.link}" target="_blank" class="button-hover w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🚢 ${pkg.name}</a>`;
        });
        html += `</div></div>`;
        html += `<div><p class="font-semibold text-gray-800 mb-2 text-md">Other</p><div class="space-y-2">`;
        packages.other.forEach(pkg => {
            html += `<a href="${pkg.link}" target="_blank" class="button-hover w-full bg-gray-600 hover:bg-gray-700 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">📋 ${pkg.name}</a>`;
        });
        html += `</div></div>`;
        html += `</div></div></div>`;
        addMessage(html, true, true);
        setTimeout(() => {
            const namePrefix = userName ? `Have fun checking out the wreck diving options, ${userName}! ` : 'Have fun checking out the wreck diving options! ';
            addMessage(`${namePrefix}Just let us know if you need anything. 🐠`);
            currentStep = 'completed';
        }, 2000);
    }

    // --- BARU ---
    // Fungsi ini mendeteksi apakah pengguna menggunakan perangkat mobile.
    function isMobileDevice() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    // Fungsi ini membuat teks pesan WhatsApp yang dinamis berdasarkan pilihan pengguna.
    function generateWhatsAppText() {
        // Pesan dasar jika tidak ada pilihan yang dibuat
        let message = `Hi Balidiving, my name is ${userName || 'a visitor'}. I'm contacting you from your website and have a question.`;

        // Cek jika pengguna telah memilih minat/tujuan
        if (userProfile.intention && userProfile.intention !== 'Other things') {

            // Jika lokasi spesifik juga telah dipilih
            if (userProfile.selectedLocation) {
                // Membersihkan nama lokasi dari deskripsi tambahan (misal: " - Calm Waters")
                const cleanLocation = userProfile.selectedLocation.split(' - ')[0];
                message = `Hi Balidiving, my name is ${userName || 'a visitor'}. I'm interested in the "${userProfile.intention}" at ${cleanLocation}. Could you provide more details please?`;

                // Jika hanya minat yang dipilih, tanpa lokasi
            } else {
                message = `Hi Balidiving, my name is ${userName || 'a visitor'}. I'd like to ask for more information about your "${userProfile.intention}" options.`;
            }
        }

        // Mengenkode pesan agar sesuai untuk URL
        return encodeURIComponent(message);
    }


    // --- MODIFIKASI ---
    // Fungsi showCustomerServiceOptions sekarang menggunakan generateWhatsAppText() dan deteksi mobile
    function showCustomerServiceOptions(showAll = false) {
        const whatsappText = generateWhatsAppText();
        const isMobile = isMobileDevice();
        const whatsappLink = isMobile
            ? `whatsapp://send?phone=6287861190174&text=${whatsappText}`
            : `https://wa.me/6287861190174?text=${whatsappText}`;

        let csHtml = `
        <div class="flex justify-start">
            <div class="max-w-md">
                <div class="bg-gray-100 rounded-2xl p-4 space-y-3">
                    <div>
                        <p class="font-semibold text-gray-800 mb-2 text-md">Chat via WhatsApp</p>
                        <a href="${whatsappLink}" target="_blank" class="button-hover w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                        </a>
                    </div>`;

        if (showAll) {
            csHtml += `
                    <div>
                        <p class="font-semibold text-gray-800 mb-2 text-md">Send us an Email</p>
                        <a href="mailto:customer.service@balidiving.com" target="_blank" class="button-hover w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200">
                             <i class="fas fa-envelope mr-2"></i> Email Us
                        </a>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-2 text-md">Follow & DM us</p>
                        <a href="https://instagram.com/bali_diving" target="_blank" class="button-hover w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200">
                             <i class="fab fa-instagram mr-2"></i> Instagram
                        </a>
                    </div>`;
        }

        csHtml += `
                </div>
            </div>
        </div>`;
        addMessage(csHtml, true, true);
    }

    function selectHelp(index) {
        const response = index === 0 ? "I'll explore on my own" : "I need help via WhatsApp";
        addMessage(response, false);
        startInactivityTimer();

        if (index === 1) {
            setTimeout(() => {
                addMessage(`Perfect! Let me connect you with our team right now.`);
                setTimeout(() => {
                    showCustomerServiceOptions();
                    setTimeout(() => {
                        const namePrefix = userName ? `Thanks for choosing us, ${userName}! ` : 'Thanks for choosing us! ';
                        addMessage(`${namePrefix}We're so excited to help you create amazing underwater memories. 🌊🐠`);
                        currentStep = 'completed';
                    }, 1500);
                }, 1000);
            }, 1000);
        } else {
            if (userProfile.intention === 'Wreck diving adventure') {
                setTimeout(() => {
                    addMessage(`Awesome! Here are our best wreck diving packages:`);
                    setTimeout(() => {
                        showWreckDivePackages();
                    }, 1000);
                }, 1000);
            } else if (userProfile.hasCertificate) {
                setTimeout(() => {
                    addMessage(`Great! Since you're certified, here are our top-rated dive sites:`);
                    setTimeout(() => {
                        showCertifiedUserLocations();
                    }, 1000);
                }, 1000);
            } else {
                setTimeout(() => {
                    addMessage(`Awesome! Here are some popular packages you might like. Just click to learn more:`);
                    setTimeout(() => {
                        showDivingPackages();
                    }, 1000);
                }, 1000);
            }
        }
    }

    function extractName(userInput) {
        const prefixesToRemove = ['my name is', 'i\'m', 'i am', 'call me', 'it\'s', 'they call me', 'nama saya adalah', 'nama saya', 'nama ku', 'panggil saya', 'panggil saja'];
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
        if (!name) {
            return "Friend";
        }
        return name.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function sendMessage() {
        const input = document.getElementById('userInput');
        const message = input.value.trim();
        if (message === '') return;
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

        setTimeout(() => {
            const lowerCaseMessage = message.toLowerCase();

            // Casual greetings and appreciation responses
            const greetingKeywords = ['hi', 'hello', 'hey', 'halo', 'hai', 'hola'];
            const isGreeting = greetingKeywords.some(keyword => lowerCaseMessage === keyword || lowerCaseMessage.startsWith(keyword + ' '));

            const thanksKeywords = ['thanks', 'thank you', 'thx', 'ty', 'terima kasih', 'makasih', 'thanks you', 'thankyou'];
            const isThanks = thanksKeywords.some(keyword => lowerCaseMessage.includes(keyword));

            const goodbyeKeywords = ['bye', 'goodbye', 'see you', 'good bye', 'sampai jumpa', 'dadah'];
            const isGoodbye = goodbyeKeywords.some(keyword => lowerCaseMessage.includes(keyword));

            // Handle casual responses first
            if (isGreeting && !waitingForName) {
                const greetingResponses = [
                    'Hey! 👋',
                    'Hi there! 😊',
                    'Hello! 🙌',
                    'Hey hey! 👋'
                ];
                const randomGreeting = greetingResponses[Math.floor(Math.random() * greetingResponses.length)];
                addMessage(randomGreeting);
                return;
            }

            if (isThanks) {
                const thanksResponses = [
                    'You\'re welcome! 😊',
                    'Happy to help! 🙌',
                    'Anytime! 😊',
                    'No problem at all! 👍',
                    'My pleasure! ✨'
                ];
                const randomThanks = thanksResponses[Math.floor(Math.random() * thanksResponses.length)];
                addMessage(randomThanks);
                return;
            }

            if (isGoodbye) {
                const goodbyeResponses = [
                    'See you! 👋',
                    'Bye! Have a great day! 🌊',
                    'Take care! See you soon! 😊',
                    'Goodbye! Happy diving! 🤿'
                ];
                const randomGoodbye = goodbyeResponses[Math.floor(Math.random() * goodbyeResponses.length)];
                addMessage(randomGoodbye);
                return;
            }

            // Continue with existing conversation logic
            const isInterestedInQuery = lowerCaseMessage.startsWith("i'm interested in");
            const generalHelpKeywords = ['help', 'support', 'bantuan', 'dukungan', 'cs'];
            const isGeneralHelpQuery = generalHelpKeywords.some(keyword => lowerCaseMessage.includes(keyword));
            const specificContactKeywords = [
                'customer service', 'contact', 'human', 'person', 'kontak', 'cara lain',
                'no whatsapp', "don't have whatsapp", 'tidak punya whatsapp',
                'email', 'instagram', 'ig'
            ];
            const isSpecificContactQuery = specificContactKeywords.some(keyword => lowerCaseMessage.includes(keyword));
            const languageKeywords = ['bahasa', 'language', 'speak', 'ngomong', 'bicara', 'english', 'indonesian'];
            const isLanguageQuery = languageKeywords.some(keyword => lowerCaseMessage.includes(keyword));
            const priceKeywords = ['price', 'cost', 'how much', 'expensive', 'cheap', 'budget', 'money', 'fee', 'rate', 'tariff', 'harga', 'biaya'];
            const isPriceQuery = priceKeywords.some(keyword => lowerCaseMessage.includes(keyword));
            const locationKeywords = ['location', 'place', 'where', 'tempat', 'lokasi', 'dimana', 'di mana', 'spot', 'site', 'area', 'map', 'peta', 'alamat', 'address'];
            const isLocationQuery = locationKeywords.some(keyword => lowerCaseMessage.includes(keyword));
            const promoKeywords = ['promo', 'promotion', 'deal', 'discount', 'offer', 'special', 'diskon', 'penawaran', 'khusus', 'murah', 'sale'];
            const isPromoQuery = promoKeywords.some(keyword => lowerCaseMessage.includes(keyword));
            const seasonKeywords = ['season', 'weather', 'when', 'best time', 'musim', 'cuaca', 'kapan', 'waktu terbaik', 'bulan', 'month', 'rain', 'hujan', 'dry', 'kering', 'wind', 'angin', 'visibility', 'current', 'arus'];
            const isSeasonQuery = seasonKeywords.some(keyword => lowerCaseMessage.includes(keyword));
            const courseKeywords = ['course', 'certification', 'padi', 'open water', 'advanced', 'rescue', 'learn', 'training', 'kursus', 'sertifikasi', 'belajar', 'pelatihan'];
            const isCourseQuery = courseKeywords.some(keyword => lowerCaseMessage.includes(keyword));
            const whatsappKeywords = ['whatsapp', 'wa', 'phone', 'call', 'contact', 'telepon', 'hubungi', 'telpon', 'calling', 'reach'];
            const isWhatsAppQuery = whatsappKeywords.some(keyword => lowerCaseMessage.includes(keyword));

            if (isInterestedInQuery) {
                setTimeout(() => {
                    addMessage(`That's great! For more detailed info, let's chat on WhatsApp. Our team's standing by to answer any questions you've got. 😊`);
                    setTimeout(() => {
                        showCustomerServiceOptions(false);
                        currentStep = 'completed';
                    }, 1000);
                }, 7000);
            } else if (isSpecificContactQuery) {
                addMessage(`Sure thing! Here's how you can reach us:`);
                setTimeout(() => {
                    showCustomerServiceOptions(true);
                }, 1000);
            } else if (isGeneralHelpQuery) {
                addMessage("Of course! Fastest way to reach us is WhatsApp:");
                setTimeout(() => {
                    showCustomerServiceOptions(false);
                }, 1000);
            } else if (isWhatsAppQuery) {
                addMessage("Sure! Here's our WhatsApp - just click to chat with us:");
                setTimeout(() => {
                    showCustomerServiceOptions(false);
                }, 1000);
            } else if (isLanguageQuery) {
                addMessage("We mainly speak English, but we can also help you in Indonesian and some other languages. 😊");
            } else if (isLocationQuery) {
                addMessage(`Here's where we are and all our diving spots in Bali! Check it out:`);
                setTimeout(() => {
                    const locationHtml = `<div class="flex justify-start"><div class="bg-green-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md"><p class="mb-3">View our location and all diving spots:</p><a href="https://share.google/bL0GacX2k57I0GlJV" target="_blank" class="inline-block bg-white text-green-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">📍 View Map & Locations</a></div></div>`;
                    addMessage(locationHtml, true, true);
                }, 1000);
            } else if (isSeasonQuery) {
                addMessage(`Good question! You can dive in Bali year-round, but here's what each season's like:`);
                setTimeout(() => {
                    addMessage(`🌞 **Dry Season (April - October)**: Best conditions overall! Clear skies, calm water, and amazing visibility (20-30m). Perfect for all sites including Nusa Penida and Tulamben.`);
                    setTimeout(() => {
                        addMessage(`🌧️ **Wet Season (November - March)**: Still awesome for diving! A bit of rain but visibility's still good (15-25m). Manta rays are super active, and it's way less crowded.`);
                        setTimeout(() => {
                            addMessage(`🏝️ **Year-round**: Water stays nice and warm (26-29°C), and different sites peak at different times. East coast (Tulamben, Amed) is great when the west coast gets rougher!`);
                        }, 2000);
                    }, 2000);
                }, 1000);
            } else if (isCourseQuery) {
                addMessage(`Great question! Getting PADI certified is totally worth it. Once you're certified, you'll get way more options and better prices too! 🏆`);
                setTimeout(() => {
                    const certificationHtml = `<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><div class="space-y-2"><a href="https://example.com/open-water-course" target="_blank" class="button-hover w-full bg-blue-700 hover:bg-blue-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🏊‍♂️ PADI Open Water Diver Course</a><a href="https://example.com/advanced-course" target="_blank" class="button-hover w-full bg-green-700 hover:bg-green-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🌊 PADI Advanced Open Water Course</a><a href="https://example.com/rescue-course" target="_blank" class="button-hover w-full bg-red-700 hover:bg-red-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🚑 PADI Rescue Diver Course</a><a href="https://balidiving.com/pricelist" target="_blank" class="button-hover w-full bg-orange-500 hover:bg-orange-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">💰 View All Course Prices</a></div></div></div></div>`;
                    addMessage(certificationHtml, true, true);
                }, 1000);
            } else if (isPromoQuery) {
                addMessage(`Good news! We've got some special offers going on right now:`);
                setTimeout(() => {
                    const promoHtml = `<div class="flex justify-start"><div class="bg-red-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md"><p class="mb-3">Check out our current promotions:</p><a href="https://www.balidiving.com/special-offers.php" target="_blank" class="inline-block bg-white text-red-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">🎉 View Special Offers</a></div></div>`;
                    addMessage(promoHtml, true, true);
                }, 1000);
            } else if (isPriceQuery) {
                addMessage(`Sure thing! Check out our full pricelist here:`);
                setTimeout(() => {
                    const pricelistHtml = `<div class="flex justify-start"><div class="bg-orange-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md"><p class="mb-3">Click below to view our current prices and packages:</p><a href="https://balidiving.com/pricelist" target="_blank" class="inline-block bg-white text-orange-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">💰 Open Pricelist</a></div></div>`;
                    addMessage(pricelistHtml, true, true);
                }, 1000);
            } else if (currentStep === 'completed') {
                addMessage(`We're on WhatsApp! Just hit us up there so we can help you out properly. 💬`);
                setTimeout(() => {
                    showCustomerServiceOptions(false);
                }, 1000);
            } else {
                addMessage(`Happy to help! Just use the buttons above to keep chatting, or check out our FAQ:`);
                setTimeout(() => {
                    const faqHtml = `<div class="flex justify-start"><div class="bg-purple-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md"><p class="mb-3">Answers to common questions:</p><a href="https://balidiving.com/faq" target="_blank" class="inline-block bg-white text-purple-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">❓ Check FAQ</a></div></div>`;
                    addMessage(faqHtml, true, true);
                }, 1000);
            }
        }, 1000);
    }



    function handleReloadConfirmation(index) {
        const isSameUser = index === 0;
        const response = isSameUser ? "Yes, that's me" : "No, I'm someone else";
        addMessage(response, false);

        if (isSameUser) {
            setTimeout(() => {
                addMessage(`Great! Let's see what you're interested in.`);
                proceedAfterName(true);
            }, 1000);
        } else {
            localStorage.removeItem('userName');
            sessionStorage.removeItem('userProfile');
            userName = '';
            userProfile = { intention: '', hasCertificate: null, selectedLocation: '' };
            setTimeout(() => {
                addMessage(`No problem! What's your name?`);
                waitingForName = true;
                currentStep = 'name';
            }, 1000);
        }
    }

    function handleIdentityAndContinue(index) {
        const isSameUser = index === 0;
        const response = isSameUser ? "Yes, that's me" : "No, I'm someone else";
        addMessage(response, false);

        if (isSameUser) {
            setTimeout(() => {
                if (userProfile.intention) {
                    addMessage(`Great! Last time you were checking out "${userProfile.intention}".`);
                    addMessage(`Want to continue with that, or try something else?`);
                    const continueButtons = createButtonGroup(
                        [`Yes, continue with "${userProfile.intention}"`, 'Explore other options'],
                        'handleContinueChoice'
                    );
                    addMessage(continueButtons, true, true);
                    currentStep = 'confirm_continue';
                } else {
                    addMessage(`Great! Let's pick up where we left off.`);
                    proceedAfterName(true);
                }
            }, 1000);
        } else {
            localStorage.removeItem('userName');
            sessionStorage.removeItem('userProfile');
            userName = '';
            userProfile = { intention: '', hasCertificate: null, selectedLocation: '' };
            setTimeout(() => {
                addMessage(`No problem! What's your name?`);
                waitingForName = true;
                currentStep = 'name';
            }, 1000);
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
                triggerNextStepForIntention(userProfile.intention);
            }, 1000);
        } else {
            sessionStorage.removeItem('userProfile');
            userProfile = { intention: '', hasCertificate: null, selectedLocation: '' };
            setTimeout(() => {
                addMessage("Sure! Let's see what else might interest you.");
                proceedAfterName(true);
            }, 1000);
        }
    }

    function proceedAfterName(isReload = false) {
        setTimeout(() => {
            if (isReload) {
                addMessage(`So, what are you interested in today, ${userName}?`);
            } else {
                addMessage(`Nice to meet you, ${userName}! 😊 So what brings you here today? Pick what sounds most interesting:`);
            }
            setTimeout(() => {
                const intentionButtons = createButtonGroup(divingIntentions, 'selectIntention');
                addMessage(intentionButtons, true, true);
                currentStep = 'intention';
            }, 1000);
        }, 1000);
    }

    document.getElementById('userInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // --- MODIFIKASI ---
    // Timer inaktivitas sekarang juga menggunakan pesan WA yang dinamis dan relevan serta deteksi mobile
    // --- MODIFIKASI ---
    // Timer inaktivitas sekarang juga menggunakan pesan WA yang dinamis dan relevan serta deteksi mobile
    function startInactivityTimer() {
        if (inactivityTimer) {
            clearTimeout(inactivityTimer);
        }

        // Check global flag to prevent showing this message more than once per user session lifetime
        const hasGlobalShown = localStorage.getItem('hasShownFollowUp') === 'true';

        if (hasShownFollowUp || currentStep === 'completed' || hasGlobalShown) {
            return;
        }

        inactivityTimer = setTimeout(() => {
            // Double check before showing
            if (!hasShownFollowUp && localStorage.getItem('hasShownFollowUp') !== 'true') {
                hasShownFollowUp = true;
                localStorage.setItem('hasShownFollowUp', 'true'); // Persist the flag

                const namePrefix = userName ? `${userName}, ` : '';
                addMessage(`${namePrefix}still there? Need help through WhatsApp instead? 😊`);
                setTimeout(() => {
                    const whatsappText = generateWhatsAppText();
                    const isMobile = isMobileDevice();
                    const whatsappLink = isMobile
                        ? `whatsapp://send?phone=6287861190174&text=${whatsappText}`
                        : `https://wa.me/6287861190174?text=${whatsappText}`;

                    const whatsappFollowUpHtml = `
                    <div class="flex justify-start">
                        <div class="bg-green-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md">
                            <p class="mb-3">Get instant help from our diving experts:</p>
                            <a href="${whatsappLink}" target="_blank" class="inline-block bg-white text-green-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">
                                💬 Chat on WhatsApp
                            </a>
                        </div>
                    </div>`;
                    addMessage(whatsappFollowUpHtml, true, true);
                }, 1000);
            }
        }, 60000);
    }

    function resetInactivityTimer() {
        if (inactivityTimer) {
            clearTimeout(inactivityTimer);
            inactivityTimer = null;
        }
    }

    function initializeChat() {
        // Restore user profile from session if available
        const savedProfileData = sessionStorage.getItem('userProfile');
        if (savedProfileData) {
            try {
                userProfile = JSON.parse(savedProfileData);
            } catch (e) {
                console.error('Error parsing user profile:', e);
            }
        }

        // Restore global follow-up flag check (to prevent showing it again if already shown)
        const hasGlobalShown = localStorage.getItem('hasShownFollowUp') === 'true';
        if (hasGlobalShown) {
            hasShownFollowUp = true;
        }

        // Try to load previous chat history
        const hasHistory = loadChatHistory();
        const savedName = localStorage.getItem('userName');

        // Always restore username if available
        if (savedName) {
            userName = savedName;
        }

        if (!hasHistory) {
            // No previous history or expired, start fresh
            setTimeout(() => {
                if (savedName) {
                    addMessage(`Hey ${userName}! Great to see you again! 👋`);
                    setTimeout(() => {
                        addMessage(`What brings you back? Still thinking about diving in Bali?`);
                        setTimeout(() => {
                            const buttons = createButtonGroup(
                                ["Yes, that's me", "No, I'm someone else"],
                                'handleReloadConfirmation'
                            );
                            addMessage(buttons, true, true);
                            currentStep = 'confirm_identity';
                        }, 1500);
                    }, 1000);
                } else {
                    addMessage(`Hey! 👋 Welcome to Bali Diving`);
                    setTimeout(() => {
                        addMessage(`What's your name?`);
                        waitingForName = true;
                        currentStep = 'name';
                    }, 1000);
                }
            }, 500);
        } else {
            // History loaded, user can continue from where they left off
            console.log('Chat history loaded successfully');
            // Ensure scrolled to bottom
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                setTimeout(() => {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 100);
            }
        }
    }

    function toggleChat() {
        const chatWidget = document.getElementById('chatWidget');
        const chatIcon = document.getElementById('chatIcon');
        const closeIcon = document.getElementById('closeIcon');
        const launcherText = document.getElementById('launcherText');
        chatIsOpen = !chatIsOpen;

        if (chatIsOpen) {
            chatWidget.classList.remove('hidden');
            chatIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            launcherText.textContent = 'Close chat';

            // Only initialize if no messages exist
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages && chatMessages.children.length === 0) {
                initializeChat();
            }

            startInactivityTimer();
            hideUnreadBadge(); // Hide badge when opening chat
        } else {
            chatWidget.classList.add('hidden');
            chatIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            launcherText.textContent = 'Chat with us';
            resetInactivityTimer();
        }
    }

    function autoOpenChat() {
        if (!hasAutoOpened && !chatIsOpen) {
            hasAutoOpened = true;
            toggleChat();
        }
    }

    window.addEventListener('load', function () {
        autoOpenTimer = setTimeout(autoOpenChat, 180000);
    });



</script>