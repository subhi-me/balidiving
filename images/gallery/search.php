<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Bar dengan Dropdown</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
        }
    </style>
</head>
<body class="min-h-full" style="background: transparent;">
    <div class="flex items-center justify-center min-h-full py-20">
        <div class="relative w-full max-w-2xl mx-4">
            <!-- Search Bar -->
            <div class="relative">
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search diving activities or promos..."
                    class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-2xl shadow-lg focus:outline-none focus:border-blue-500 focus:shadow-xl transition-all duration-300 bg-white"
                >
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 cursor-pointer" onclick="performSearch()">
                    <svg class="w-6 h-6 text-gray-400 hover:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Dropdown Content -->
            <div 
                id="dropdown" 
                class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 opacity-0 invisible transform translate-y-2 transition-all duration-300 z-50"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    <!-- Aktivitas Populer -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-2">🔥</span>
                            Special Activities
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center p-3 rounded-xl hover:bg-blue-50 cursor-pointer transition-colors duration-200" onclick="window.open('https://example.com/tulamben-diving', '_blank')">
                                <span class="text-xl mr-3">🤿</span>
                                <div>
                                    <div class="font-medium text-gray-800">Tulamben Wreck Diving</div>
                                    <div class="text-sm text-gray-500">Bali • USAT Liberty Wreck</div>
                                </div>
                            </div>
                            <div class="flex items-center p-3 rounded-xl hover:bg-blue-50 cursor-pointer transition-colors duration-200" onclick="window.open('https://example.com/menjangan-diving', '_blank')">
                                <span class="text-xl mr-3">🐠</span>
                                <div>
                                    <div class="font-medium text-gray-800">Menjangan Island Diving</div>
                                    <div class="text-sm text-gray-500">Bali • Wall Diving & Coral Gardens</div>
                                </div>
                            </div>
                            <div class="flex items-center p-3 rounded-xl hover:bg-blue-50 cursor-pointer transition-colors duration-200" onclick="window.open('https://example.com/nusa-penida-diving', '_blank')">
                                <span class="text-xl mr-3">🦈</span>
                                <div>
                                    <div class="font-medium text-gray-800">Nusa Penida Manta Diving</div>
                                    <div class="text-sm text-gray-500">Bali • Manta Point & Crystal Bay</div>
                                </div>
                            </div>
                            <div class="flex items-center p-3 rounded-xl hover:bg-blue-50 cursor-pointer transition-colors duration-200" onclick="window.open('https://example.com/amed-diving', '_blank')">
                                <span class="text-xl mr-3">🐢</span>
                                <div>
                                    <div class="font-medium text-gray-800">Amed Coral Diving</div>
                                    <div class="text-sm text-gray-500">Bali • Coral Reefs & Macro Life</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Promo -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-2">🎉</span>
                            Special Expedition
                        </h3>
                        <div class="space-y-3">
                            <div class="p-4 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border border-red-100 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="window.open('https://example.com/bali-diving-package-50off', '_blank')">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium">TODAY PROMOTION</span>
                                    <span class="text-xs text-gray-500">Ends in 3 days</span>
                                </div>
                                <div class="font-medium text-gray-800">Bali Diving Package</div>

                            </div>
                            <div class="p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="window.open('https://example.com/wreck-diving-expedition', '_blank')">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium">SPECIAL DISCOUNT</span>
                                    <span class="text-xs text-gray-500">Limited time</span>
                                </div>
                                <div class="font-medium text-gray-800">Wreck Diving Expedition</div>

                            </div>
                            <div class="p-4 rounded-xl bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-100 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="window.open('https://example.com/manta-diving-special', '_blank')">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">GROUP PROMOTION</span>
                                    <span class="text-xs text-gray-500">Weekend only</span>
                                </div>
                                <div class="font-medium text-gray-800">Manta Ray Diving Special</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const dropdown = document.getElementById('dropdown');

        // Show dropdown when input is clicked or focused
        searchInput.addEventListener('click', showDropdown);
        searchInput.addEventListener('focus', showDropdown);

        // Handle Enter key press for search
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                performSearch();
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                hideDropdown();
            }
        });

        // Handle item clicks
        dropdown.addEventListener('click', function(event) {
            const item = event.target.closest('.cursor-pointer');
            if (item) {
                const title = item.querySelector('.font-medium');
                if (title) {
                    searchInput.value = title.textContent;
                    hideDropdown();
                }
            }
        });

        function showDropdown() {
            dropdown.classList.remove('opacity-0', 'invisible', 'translate-y-2');
            dropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
        }

        function hideDropdown() {
            dropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
            dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }

        function performSearch() {
            const keyword = searchInput.value.trim();
            if (keyword) {
                // Convert spaces to plus signs for URL parameter
                const encodedKeyword = keyword.replace(/\s+/g, '+');
                const searchUrl = `https://balidiving.com/Images/gallery/?q=${encodedKeyword}`;
                window.open(searchUrl, '_blank');
            }
        }
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98de2a7842d7f8e6',t:'MTc2MDM1MTQ3MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
