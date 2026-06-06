<script>
    // --- Chat Assistant Script ---
    let currentStep = 'greeting';
    let userProfile = {
        intention: '',
        hasCertificate: null,
        selectedLocation: ''
    };

    function saveChatState() {
        sessionStorage.setItem('userProfile', JSON.stringify(userProfile));
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
        { name: '💰 View All Prices & Packages', link: 'https://booking.balidiving.com/pricelist/?ref=balidiving_web_chat' }
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
        chatMessages.scrollTop = chatMessages.scrollHeight;
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
                addMessage(`No worries! We have fantastic beginner-friendly sites and can arrange training if needed. Here are some perfect locations for you in Bali:`);
                setTimeout(() => {
                    showBeginnerDivingLocations();
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        } else if (intention === 'Diving with manta rays') {
            setTimeout(() => {
                addMessage(`Amazing choice! Manta rays are truly magnificent creatures. Here are the best spots in Bali where you can encounter these gentle giants:`);
                setTimeout(() => {
                    showMantaRayLocations();
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        } else if (intention === 'Wreck diving adventure') {
            setTimeout(() => {
                addMessage(`Fantastic choice! Wreck diving offers incredible history and marine life. Here are the best shipwreck sites in Bali:`);
                setTimeout(() => {
                    showWreckDivingLocations();
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        } else if (intention === 'Snorkeling') {
            setTimeout(() => {
                addMessage(`Snorkeling is a fantastic way to see Bali's beautiful marine life from the surface, no certification needed! It's perfect for all ages. Here are our most popular snorkeling packages:`);
                setTimeout(() => {
                    showSnorkelingPackages();
                    setTimeout(() => {
                        const namePrefix = userName ? `Have a great time snorkeling, ${userName}! ` : 'Have a great time snorkeling! ';
                        addMessage(`${namePrefix}Click on any package to see more details and book your trip. 🐠`);
                        currentStep = 'completed';
                    }, 2000);
                }, 1000);
            }, 1000);
        } else if (intention === 'Other things') {
            setTimeout(() => {
                addMessage(`That's great! We offer many different diving experiences. Let me connect you with our specialists who can help you with your specific interests:`);
                setTimeout(() => {
                    showCustomerServiceOptions();
                    setTimeout(() => {
                        const namePrefix = userName ? `Thank you for choosing Bali Diving, ${userName}! ` : 'Thank you for choosing Bali Diving! ';
                        addMessage(`${namePrefix}Our team will help you find the perfect diving experience. 🌊🐠`);
                        currentStep = 'completed';
                    }, 1500);
                }, 1000);
            }, 1000);
        } else {
            setTimeout(() => {
                addMessage(`Perfect choice! Now, do you have a diving certification from PADI, SSI, or other diving organizations?`);
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
                addMessage(`Excellent! As a certified diver, you'll have access to our full range of dive sites. Here are some amazing locations I'd recommend in Bali:`);
                setTimeout(() => {
                    showDivingLocations();
                    startInactivityTimer();
                }, 1000);
            }, 1000);
        } else {
            setTimeout(() => {
                addMessage(`No worries! We have fantastic beginner-friendly sites and can arrange training if needed. Would you be interested in learning to dive with PADI certification like Open Water and higher levels? With certification, you'll have access to many more dive packages and better prices! 🏆`);
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
                addMessage(`Fantastic choice! PADI certification opens up a whole new world of diving adventures. Here are our popular certification courses, please choose one:`);
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
                        const namePrefix = userName ? `Thank you for your interest in PADI certification, ${userName}! ` : 'Thank you for your interest in PADI certification! ';
                        addMessage(`${namePrefix}Our certified instructors will help you become a confident, safe diver. 🌊🏆`);
                        currentStep = 'completed';
                    }, 2000);
                }, 1000);
            }, 1000);
        } else {
            setTimeout(() => {
                addMessage(`Great choice! Our 'Try Scuba Diving' program is the perfect way to experience the underwater world without commitment. Here are our top locations for it:`);
                setTimeout(() => {
                    showTryScubaLocations();
                    setTimeout(() => {
                        const namePrefix = userName ? `Enjoy your first dive, ${userName}! ` : 'Enjoy your first dive! ';
                        addMessage(`${namePrefix}Just pick a location to see the details and book your adventure. 🐠`);
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
            addMessage(`Great choice! ${divingLocations[index].name} is absolutely stunning. Would you like to explore this location on your own, or would you prefer personalized assistance through WhatsApp?`);
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
            addMessage(`Perfect choice for a beginner! ${beginnerDivingLocations[index].name} offers calm, shallow waters with amazing marine life - ideal for your first diving experience. Would you like to explore this location on your own, or would you prefer personalized assistance through WhatsApp?`);
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
            addMessage(`Incredible choice! ${mantaRayLocations[index].name} is one of the world's best spots for manta ray encounters. These majestic creatures can have wingspans up to 7 meters! Would you like to explore this location on your own, or would you prefer personalized assistance through WhatsApp?`);
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
            addMessage(`Excellent choice! ${wreckDivingLocations[index].name} offers fascinating underwater history and has become an artificial reef teeming with marine life. Perfect for exploring both history and nature! Would you like to explore this location on your own, or would you prefer personalized assistance through WhatsApp?`);
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
            const namePrefix = userName ? `Thank you for choosing Bali Diving, ${userName}! ` : 'Thank you for choosing Bali Diving! ';
            addMessage(`${namePrefix}We're excited to help you create unforgettable underwater memories. 🌊🐠`);
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
            const namePrefix = userName ? `Thank you for choosing Bali Diving, ${userName}! ` : 'Thank you for choosing Bali Diving! ';
            addMessage(`${namePrefix}We hope you find the perfect dive spot. Enjoy your exploration! 🌊🐠`);
            currentStep = 'completed';
        }, 2000);
    }

    function showWreckDivePackages() {
        const packages = {
            certified: [{ name: 'Tulamben Wreck', link: 'https://balidiving.diversdesk.com/product/710cea45-4268-4317-802c-ffc21f365362' }, { name: 'Amed', link: 'https://balidiving.diversdesk.com/product/16c88803-529d-41e4-91e4-70e95376a4b7' }, { name: 'Special 7 days/16 Dives', link: 'https://balidiving.diversdesk.com/product/b1cab048-1475-4329-8533-c0a685f5a962' }],
            other: [{ name: 'View All Pricelist', link: 'https://booking.balidiving.com/pricelist/?ref=balidiving_web_chat' }, ]
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
            const namePrefix = userName ? `Enjoy exploring the wreck diving options, ${userName}! ` : 'Enjoy exploring the wreck diving options! ';
            addMessage(`${namePrefix}Let us know if you need anything else. 🐠`);
            currentStep = 'completed';
        }, 2000);
    }
    
    function showCustomerServiceOptions(showAll = false) {
        const whatsappText = encodeURIComponent(`Hi, my name is ${userName || 'a visitor'}. I'm visiting Balidiving.com and have a question.`);
        let csHtml = `
            <div class="flex justify-start">
                <div class="max-w-md">
                    <div class="bg-gray-100 rounded-2xl p-4 space-y-3">
                        <div>
                            <p class="font-semibold text-gray-800 mb-2 text-md">Chat via WhatsApp</p>
                            <a href="https://wa.me/6287861190174?text=${whatsappText}" target="_blank" class="button-hover w-full flex items-center justify-center bg-green-500 hover:bg-green-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200">
                                💬 Open WhatsApp Chat
                            </a>
                        </div>`;

        if (showAll) {
            csHtml += `
                        <div>
                            <p class="font-semibold text-gray-800 mb-2 text-md">Send us an Email</p>
                            <a href="mailto:customer.service@balidiving.com" target="_blank" class="button-hover w-full flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200">
                                📧 customer.service@balidiving.com
                            </a>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 mb-2 text-md">Follow & DM us</p>
                            <a href="https://instagram.com/bali_diving" target="_blank" class="button-hover w-full flex items-center justify-center bg-purple-500 hover:bg-purple-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200">
                                📸 Follow @bali_diving on Instagram
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
                addMessage(`Perfect! I'll connect you with our diving specialists right away.`);
                setTimeout(() => {
                    showCustomerServiceOptions();
                    setTimeout(() => {
                        const namePrefix = userName ? `Thank you for choosing Bali Diving, ${userName}! ` : 'Thank you for choosing Bali Diving! ';
                        addMessage(`${namePrefix}We're excited to help you create unforgettable underwater memories. 🌊🐠`);
                        currentStep = 'completed';
                    }, 1500);
                }, 1000);
            }, 1000);
        } else {
            if (userProfile.intention === 'Wreck diving adventure') {
                setTimeout(() => {
                    addMessage(`Awesome! Here are our top packages and resources for wreck diving for you to explore:`);
                    setTimeout(() => {
                        showWreckDivePackages();
                    }, 1000);
                }, 1000);
            } else if (userProfile.hasCertificate) {
                setTimeout(() => {
                    addMessage(`Great! As a certified diver, here are some direct links to our top-rated dive sites for you to explore:`);
                    setTimeout(() => {
                        showCertifiedUserLocations();
                    }, 1000);
                }, 1000);
            } else {
                setTimeout(() => {
                    addMessage(`Wonderful! Here are some popular diving packages you might be interested in. Click any package to learn more:`);
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

            // <-- MODIFIKASI DIMULAI: Menambahkan deteksi untuk "I'm interested in" -->
            const isInterestedInQuery = lowerCaseMessage.startsWith("i'm interested in");
            // <-- MODIFIKASI SELESAI -->

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

            // <-- MODIFIKASI DIMULAI: Menambahkan blok kondisi baru untuk "I'm interested in" -->
            if (isInterestedInQuery) {
                setTimeout(() => {
                    addMessage(`That's great to hear! For a more detailed and personalized explanation, I can assist you directly through WhatsApp. Our diving specialists are ready to help with any questions you may have. 😊`);
                    setTimeout(() => {
                        showCustomerServiceOptions(false); // Hanya tampilkan tombol WhatsApp
                        currentStep = 'completed';
                    }, 1000);
                }, 7000); // Penundaan 7 detik
            } else if (isSpecificContactQuery) {
            // <-- MODIFIKASI SELESAI -->
                addMessage(`Of course! Here are all the ways you can contact our team:`);
                setTimeout(() => {
                    showCustomerServiceOptions(true);
                }, 1000);
            } else if (isGeneralHelpQuery) {
                addMessage("Of course! The fastest way to get in touch with our team is via WhatsApp:");
                setTimeout(() => {
                    showCustomerServiceOptions(false);
                }, 1000);
            } else if (isLanguageQuery) {
                addMessage("Our main language is English, but we also serve Indonesian and other possible languages. 😊");
            } else if (isLocationQuery) {
                addMessage(`Here's our location and all our diving spots in Bali! You can find us and explore all the amazing dive sites:`);
                setTimeout(() => {
                    const locationHtml = `<div class="flex justify-start"><div class="bg-green-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md"><p class="mb-3">View our location and all diving spots:</p><a href="https://share.google/bL0GacX2k57I0GlJV" target="_blank" class="inline-block bg-white text-green-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">📍 View Map & Locations</a></div></div>`;
                    addMessage(locationHtml, true, true);
                }, 1000);
            } else if (isSeasonQuery) {
                addMessage(`Great question! Bali has excellent diving year-round, but here's what to expect in each season:`);
                setTimeout(() => {
                    addMessage(`🌞 **Dry Season (April - October)**: Best overall conditions! Clear skies, calm seas, and excellent visibility (20-30m). Perfect for all dive sites including Nusa Penida and Tulamben.`);
                    setTimeout(() => {
                        addMessage(`🌧️ **Wet Season (November - March)**: Still great for diving! Some rain but underwater visibility remains good (15-25m). Manta rays are more active, and it's less crowded.`);
                        setTimeout(() => {
                            addMessage(`🏝️ **Year-round highlights**: Water temperature stays warm (26-29°C), and different sites have different peak seasons. East coast (Tulamben, Amed) is great when west coast has bigger swells!`);
                        }, 2000);
                    }, 2000);
                }, 1000);
            } else if (isCourseQuery) {
                addMessage(`Excellent question! Getting PADI certified is a fantastic investment. With certification, you'll have access to many more dive packages and better prices! 🏆`);
                setTimeout(() => {
                    const certificationHtml = `<div class="flex justify-start"><div class="max-w-md"><div class="bg-gray-100 rounded-2xl p-4"><div class="space-y-2"><a href="https://example.com/open-water-course" target="_blank" class="button-hover w-full bg-blue-700 hover:bg-blue-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🏊‍♂️ PADI Open Water Diver Course</a><a href="https://example.com/advanced-course" target="_blank" class="button-hover w-full bg-green-700 hover:bg-green-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🌊 PADI Advanced Open Water Course</a><a href="https://example.com/rescue-course" target="_blank" class="button-hover w-full bg-red-700 hover:bg-red-800 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">🚑 PADI Rescue Diver Course</a><a href="https://booking.balidiving.com/pricelist/?ref=balidiving_web_chat" target="_blank" class="button-hover w-full bg-orange-500 hover:bg-orange-600 text-white rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200 text-left block">💰 View All Course Prices</a></div></div></div></div>`;
                    addMessage(certificationHtml, true, true);
                }, 1000);
            } else if (isPromoQuery) {
                addMessage(`Great news! We have special offers and deals available right now:`);
                setTimeout(() => {
                    const promoHtml = `<div class="flex justify-start"><div class="bg-red-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md"><p class="mb-3">Check out our current promotions:</p><a href="https://www.balidiving.com/special-offers.php" target="_blank" class="inline-block bg-white text-red-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">🎉 View Special Offers</a></div></div>`;
                    addMessage(promoHtml, true, true);
                }, 1000);
            } else if (isPriceQuery) {
                addMessage(`I'd be happy to help you with pricing information! Please check our complete pricelist:`);
                setTimeout(() => {
                    const pricelistHtml = `<div class="flex justify-start"><div class="bg-orange-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md"><p class="mb-3">Click below to view our current prices and packages:</p><a href="https://booking.balidiving.com/pricelist/?ref=balidiving_web_chat" target="_blank" class="inline-block bg-white text-orange-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">💰 Open Pricelist</a></div></div>`;
                    addMessage(pricelistHtml, true, true);
                }, 1000);
            } else if (currentStep === 'completed') {
                addMessage(`We're online on WhatsApp! Please contact us through our WhatsApp channel to avoid any confusion. 💬`);
            } else {
                addMessage(`I'd love to help you step by step! Please use the buttons above to continue our conversation, or check our FAQ for more information:`);
                setTimeout(() => {
                    const faqHtml = `<div class="flex justify-start"><div class="bg-purple-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md"><p class="mb-3">Find answers to common questions:</p><a href="https://booking.balidiving.com/faqs-whatsapp" target="_blank" class="inline-block bg-white text-purple-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">❓ View FAQ</a></div></div>`;
                    addMessage(faqHtml, true, true);
                }, 1000);
            }
        }, 1000);
    }
    
    function initializeChat() {
        const savedName = localStorage.getItem('userName');
        const savedProfileData = sessionStorage.getItem('userProfile');

        if (savedName && savedProfileData) {
            userName = savedName;
            userProfile = JSON.parse(savedProfileData);
            setTimeout(() => {
                addMessage(`Hey! Welcome back. Are you still ${userName}?`);
                const confirmButtons = createButtonGroup(
                    ['Yes, that\'s me', 'No, I\'m someone else'],
                    'handleIdentityAndContinue'
                );
                addMessage(confirmButtons, true, true);
                currentStep = 'confirm_identity_and_continue';
            }, 500);

        } else if (savedName) {
            userName = savedName;
            setTimeout(() => {
                addMessage(`Hey! Welcome back, ${userName}. It looks like the page reloaded.`);
                setTimeout(() => {
                    addMessage(`Is this still you?`);
                    const confirmButtons = createButtonGroup(['Yes, that\'s me', 'No, I\'m someone else'], 'handleReloadConfirmation');
                    addMessage(confirmButtons, true, true);
                    currentStep = 'confirm_reload';
                }, 1500);
            }, 500);
        } else {
            setTimeout(() => {
                addMessage("Hey! So glad you're here at Bali Diving. It's a perfect time to plan an underwater adventure...");
                setTimeout(() => {
                    addMessage("Could you tell me your name, please?");
                    waitingForName = true;
                    currentStep = 'name';
                }, 1500);
            }, 500);
        }
    }

    function handleReloadConfirmation(index) {
        const isSameUser = index === 0;
        const response = isSameUser ? "Yes, that's me" : "No, I'm someone else";
        addMessage(response, false);

        if (isSameUser) {
            setTimeout(() => {
                addMessage(`Great! Let's find out what you're interested in.`);
                proceedAfterName(true);
            }, 1000);
        } else {
            localStorage.removeItem('userName');
            sessionStorage.removeItem('userProfile');
            userName = '';
            userProfile = { intention: '', hasCertificate: null, selectedLocation: '' };
            setTimeout(() => {
                addMessage(`Alright, no problem! Could you tell me your name, please?`);
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
                    addMessage(`Great! Last time you were interested in "${userProfile.intention}".`);
                    addMessage(`Would you like to continue with that, or explore something else?`);
                    const continueButtons = createButtonGroup(
                        [`Yes, continue with "${userProfile.intention}"`, 'Explore other options'],
                        'handleContinueChoice'
                    );
                    addMessage(continueButtons, true, true);
                    currentStep = 'confirm_continue';
                } else {
                    addMessage(`Great! Let's continue where you left off.`);
                    proceedAfterName(true);
                }
            }, 1000);
        } else {
            localStorage.removeItem('userName');
            sessionStorage.removeItem('userProfile');
            userName = '';
            userProfile = { intention: '', hasCertificate: null, selectedLocation: '' };
            setTimeout(() => {
                addMessage(`Alright, no problem! Could you tell me your name, please?`);
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
                addMessage("Sure thing! Let's see what else you might be interested in.");
                proceedAfterName(true);
            }, 1000);
        }
    }

    function proceedAfterName(isReload = false) {
        setTimeout(() => {
            if (isReload) {
                addMessage(`So, what are you interested in today, ${userName}?`);
            } else {
                addMessage(`Nice to meet you, ${userName}! 😊 What brings you to explore scuba diving in Bali today? Please select what interests you most:`);
            }
            setTimeout(() => {
                const intentionButtons = createButtonGroup(divingIntentions, 'selectIntention');
                addMessage(intentionButtons, true, true);
                currentStep = 'intention';
            }, 1000);
        }, 1000);
    }

    document.getElementById('userInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    let chatIsOpen = false;
    let inactivityTimer = null;
    let hasShownFollowUp = false;
    let autoOpenTimer = null;
    let hasAutoOpened = false;
    let userName = '';
    let waitingForName = false;

    function startInactivityTimer() {
        if (inactivityTimer) {
            clearTimeout(inactivityTimer);
        }
        if (hasShownFollowUp || currentStep === 'completed') {
            return;
        }
        inactivityTimer = setTimeout(() => {
            if (!hasShownFollowUp && chatIsOpen) {
                hasShownFollowUp = true;
                const namePrefix = userName ? `${userName}, ` : '';
                addMessage(`${namePrefix}still there? Need help through WhatsApp instead? 😊`);
                setTimeout(() => {
                    const whatsappText = encodeURIComponent(`Hi, my name is ${userName || 'a visitor'}. I got stuck on the website.`);
                    const whatsappFollowUpHtml = `
                        <div class="flex justify-start">
                            <div class="bg-green-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md">
                                <p class="mb-3">Get instant help from our diving experts:</p>
                                <a href="https://wa.me/6287861190174?text=${whatsappText}" target="_blank" class="inline-block bg-white text-green-500 rounded-lg px-4 py-2 font-medium hover:bg-gray-100 transition-colors">
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
            if (currentStep === 'greeting') {
                initializeChat();
            }
            startInactivityTimer();
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

    window.addEventListener('load', function() {
        autoOpenTimer = setTimeout(autoOpenChat, 180000);
    });
</script>