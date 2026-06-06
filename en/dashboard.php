<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scuba Diving & Snorkeling Kanban Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
        }
        .drag-over {
            background-color: #e0f2fe;
            border: 2px dashed #0284c7;
        }
        .card-dragging {
            opacity: 0.5;
            transform: rotate(5deg);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-cyan-100 min-h-full">
    <div class="container mx-auto p-6">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-blue-900 mb-2">🏊‍♂️ Scuba Diving & Snorkeling Kanban Board</h1>
            <p class="text-blue-700">Manage diving and snorkeling activities with ease</p>
        </div>

        <!-- Edit Mode Toggle -->
        <div class="mb-6 text-center">
            <button id="editModeBtn" onclick="toggleEditMode()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition-all duration-200 transform hover:scale-105 mr-4">
                ✏️ Edit Mode
            </button>
            <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition-all duration-200 transform hover:scale-105">
                ➕ Add New Activity
            </button>
        </div>

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Coming Column -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                    <h2 class="text-xl font-bold text-gray-800">Coming</h2>
                    <span id="coming-count" class="ml-auto bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm font-semibold">0</span>
                </div>
                <div id="coming-column" class="space-y-4 min-h-96" ondrop="drop(event, 'coming')" ondragover="allowDrop(event)" onclick="handleColumnClick(event, 'coming')">
                    <!-- Cards will be added here -->
                </div>
            </div>

            <!-- On Trip Column -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-4 h-4 bg-blue-500 rounded-full mr-3"></div>
                    <h2 class="text-xl font-bold text-gray-800">On Trip</h2>
                    <span id="ontrip-count" class="ml-auto bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-semibold">0</span>
                </div>
                <div id="ontrip-column" class="space-y-4 min-h-96" ondrop="drop(event, 'ontrip')" ondragover="allowDrop(event)">
                    <!-- Cards will be added here -->
                </div>
            </div>

            <!-- Done Column -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                    <h2 class="text-xl font-bold text-gray-800">Done</h2>
                    <span id="done-count" class="ml-auto bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-semibold">0</span>
                </div>
                <div id="done-column" class="space-y-4 min-h-96" ondrop="drop(event, 'done')" ondragover="allowDrop(event)">
                    <!-- Cards will be added here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Activity Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 max-h-full overflow-y-auto">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Add New Activity</h3>
            <form id="addActivityForm" onsubmit="addActivity(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="activityName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Example: John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp Number</label>
                        <input type="tel" id="whatsappNumber" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Example: 628123456789">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Example: customer@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Additional notes..."></textarea>
                    </div>

                </div>
                <div class="flex gap-4 mt-6">
                    <button type="button" onclick="closeAddModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Offcanvas Detail -->
    <div id="offcanvas" class="fixed inset-y-0 right-0 w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50">
        <div class="h-full flex flex-col">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold">Activity Details</h3>
                    <button onclick="closeOffcanvas()" class="text-white hover:text-gray-200 text-2xl">×</button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div id="offcanvasContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas Overlay -->
    <div id="offcanvasOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40" onclick="closeOffcanvas()"></div>

    <script>
        let activities = [];
        let draggedElement = null;
        let isEditMode = false;

        // Load data from localStorage
        function loadData() {
            const saved = localStorage.getItem('divingActivities');
            if (saved) {
                activities = JSON.parse(saved);
                renderAllCards();
            } else {
                // Add sample data
                const today = new Date().toISOString().split('T')[0];
                const yesterday = new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                
                activities = [
                    {
                        id: 1,
                        name: "John Doe",
                        whatsapp: "628123456789",
                        email: "john@email.com",
                        notes: "Group of 4 people, hotel pickup at 7 AM",
                        promoLink: generatePromoLink("John Doe", "john@email.com"),
                        status: "coming",
                        createdDate: today
                    },
                    {
                        id: 2,
                        name: "Sarah Wilson",
                        whatsapp: "628987654321",
                        email: "sarah@email.com",
                        notes: "Advanced diver, PADI certified",
                        promoLink: generatePromoLink("Sarah Wilson", "sarah@email.com"),
                        status: "ontrip",
                        createdDate: yesterday
                    }
                ];
                renderAllCards();
            }
        }

        // Save data to localStorage
        function saveData() {
            localStorage.setItem('divingActivities', JSON.stringify(activities));
        }

        // Generate unique ID
        function generateId() {
            return Date.now() + Math.random();
        }

        // Generate promo link with name and email parameters
        function generatePromoLink(name, email) {
            const encodedName = encodeURIComponent(name);
            const encodedEmail = encodeURIComponent(email);
            return `https://balidiving.com/promo/?n=${encodedName}&e=${encodedEmail}`;
        }

        // Format date for display
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric' 
            });
        }

        // Create card HTML
        function createCardHTML(activity) {
            const statusColors = {
                coming: 'border-l-yellow-500 bg-yellow-50',
                ontrip: 'border-l-blue-500 bg-blue-50',
                done: 'border-l-green-500 bg-green-50'
            };

            const deleteButton = isEditMode ? 
                `<button onclick="event.stopPropagation(); deleteActivity(${activity.id})" class="text-red-500 hover:text-red-700 p-1 rounded transition-colors text-xs">
                    🗑️
                </button>` : '';

            const cardClick = isEditMode ? `openEditModal(${activity.id})` : `openOffcanvas(${activity.id})`;

            // Check if card was created before today
            const today = new Date().toISOString().split('T')[0];
            const cardDate = activity.createdDate || today;
            const isOld = cardDate < today;
            
            // Apply smaller size for old cards
            const cardSize = isOld ? 'p-2 text-xs' : 'p-4';
            const titleSize = isOld ? 'text-xs' : 'text-lg';
            const opacity = isOld ? 'opacity-60' : '';

            return `
                <div class="card bg-white rounded-lg shadow-md border-l-4 ${statusColors[activity.status]} ${cardSize} ${opacity} cursor-move transition-all duration-200 hover:shadow-lg" 
                     draggable="true" 
                     ondragstart="dragStart(event, ${activity.id})"
                     onclick="${cardClick}"
                     data-id="${activity.id}">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-gray-800 ${titleSize}">🏊‍♂️ ${activity.name}</h4>
                        ${deleteButton}
                    </div>
                    ${isOld ? `<div class="text-xs text-gray-500 mt-1">${formatDate(cardDate)}</div>` : ''}
                </div>
            `;
        }

        // Render all cards
        function renderAllCards() {
            const columns = {
                coming: document.getElementById('coming-column'),
                ontrip: document.getElementById('ontrip-column'),
                done: document.getElementById('done-column')
            };

            // Clear all columns
            Object.values(columns).forEach(column => column.innerHTML = '');

            // Add cards to respective columns
            activities.forEach(activity => {
                columns[activity.status].innerHTML += createCardHTML(activity);
            });

            updateCounts();
        }

        // Update counts
        function updateCounts() {
            const counts = activities.reduce((acc, activity) => {
                acc[activity.status] = (acc[activity.status] || 0) + 1;
                return acc;
            }, {});

            document.getElementById('coming-count').textContent = counts.coming || 0;
            document.getElementById('ontrip-count').textContent = counts.ontrip || 0;
            document.getElementById('done-count').textContent = counts.done || 0;
        }

        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('addModal').classList.add('flex');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('addModal').classList.remove('flex');
            document.getElementById('addActivityForm').reset();
            delete document.getElementById('addActivityForm').dataset.editingId;
            document.querySelector('#addModal h3').textContent = 'Add New Activity';
        }

        // Add new activity
        function addActivity(event) {
            event.preventDefault();
            
            const editingId = document.getElementById('addActivityForm').dataset.editingId;
            
            if (editingId) {
                // Update existing activity
                const activity = activities.find(a => a.id == editingId);
                if (activity) {
                    activity.name = document.getElementById('activityName').value;
                    activity.whatsapp = document.getElementById('whatsappNumber').value;
                    activity.email = document.getElementById('email').value;
                    activity.notes = document.getElementById('notes').value;
                    activity.promoLink = generatePromoLink(activity.name, activity.email);
                }
                delete document.getElementById('addActivityForm').dataset.editingId;
            } else {
                // Add new activity
                const activityName = document.getElementById('activityName').value;
                const activityEmail = document.getElementById('email').value;
                const newActivity = {
                    id: generateId(),
                    name: activityName,
                    whatsapp: document.getElementById('whatsappNumber').value,
                    email: activityEmail,
                    notes: document.getElementById('notes').value,
                    promoLink: generatePromoLink(activityName, activityEmail),
                    status: 'coming',
                    createdDate: new Date().toISOString().split('T')[0]
                };
                activities.push(newActivity);
            }

            saveData();
            renderAllCards();
            closeAddModal();
        }

        // Open edit modal
        function openEditModal(id) {
            const activity = activities.find(a => a.id === id);
            if (!activity) return;

            document.getElementById('activityName').value = activity.name;
            document.getElementById('whatsappNumber').value = activity.whatsapp;
            document.getElementById('email').value = activity.email;
            document.getElementById('notes').value = activity.notes || '';
            
            document.getElementById('addActivityForm').dataset.editingId = id;
            document.querySelector('#addModal h3').textContent = 'Edit Activity';
            
            openAddModal();
        }

        // Delete activity
        function deleteActivity(id) {
            if (confirm('Are you sure you want to delete this activity?')) {
                activities = activities.filter(activity => activity.id !== id);
                saveData();
                renderAllCards();
            }
        }

        // Send thank you message
        function sendThankYou(id) {
            const activity = activities.find(a => a.id === id);
            if (!activity) return;

            const message = `Hello! Thank you for joining our diving/snorkeling activity!\n\nWe hope you had an amazing underwater experience!\n\n${activity.promoLink ? `Don't forget, we have a special promo for your next trip: ${activity.promoLink}\n\n` : ''}See you on your next underwater adventure!`;

            const whatsappUrl = `https://wa.me/${activity.whatsapp}?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
        }

        // Offcanvas functions
        function openOffcanvas(id) {
            const activity = activities.find(a => a.id === id);
            if (!activity) return;

            const content = `
                <div class="space-y-6">
                    <div class="text-center">
                        <h4 class="text-2xl font-bold text-gray-800 mb-2">🏊‍♂️ ${activity.name}</h4>
                        <div class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${getStatusBadge(activity.status)} mb-2">
                            ${getStatusText(activity.status)}
                        </div>
                        <div class="text-sm text-gray-500">
                            Created: ${formatDate(activity.createdDate || new Date().toISOString().split('T')[0])}
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <span class="text-2xl mr-3">📱</span>
                                <span class="font-semibold text-gray-700">WhatsApp</span>
                            </div>
                            <a href="https://wa.me/${activity.whatsapp}" target="_blank" rel="noopener noreferrer" 
                               class="text-green-600 hover:underline font-medium">${activity.whatsapp}</a>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <span class="text-2xl mr-3">📧</span>
                                <span class="font-semibold text-gray-700">Email</span>
                            </div>
                            <a href="mailto:${activity.email}" 
                               class="text-blue-600 hover:underline font-medium">${activity.email}</a>
                        </div>
                        
                        ${activity.notes ? `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <span class="text-2xl mr-3">📝</span>
                                <span class="font-semibold text-gray-700">Notes</span>
                            </div>
                            <p class="text-gray-600">${activity.notes}</p>
                        </div>
                        ` : ''}
                        
                        ${activity.promoLink ? `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <span class="text-2xl mr-3">🎁</span>
                                <span class="font-semibold text-gray-700">Promo Link</span>
                            </div>
                            <a href="${activity.promoLink}" target="_blank" rel="noopener noreferrer" 
                               class="text-purple-600 hover:underline font-medium break-all">${activity.promoLink}</a>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <button onclick="sendThankYou(${activity.id})" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg font-semibold transition-colors mb-3">
                            💚 Send Thank You Message
                        </button>
                        <button onclick="deleteActivity(${activity.id}); closeOffcanvas();" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg font-semibold transition-colors">
                            🗑️ Delete Activity
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('offcanvasContent').innerHTML = content;
            document.getElementById('offcanvasOverlay').classList.remove('hidden');
            document.getElementById('offcanvas').classList.remove('translate-x-full');
        }

        function closeOffcanvas() {
            document.getElementById('offcanvas').classList.add('translate-x-full');
            document.getElementById('offcanvasOverlay').classList.add('hidden');
        }

        function getStatusBadge(status) {
            const badges = {
                coming: 'bg-yellow-100 text-yellow-800',
                ontrip: 'bg-blue-100 text-blue-800',
                done: 'bg-green-100 text-green-800'
            };
            return badges[status] || 'bg-gray-100 text-gray-800';
        }

        function getStatusText(status) {
            const texts = {
                coming: 'Coming',
                ontrip: 'On Trip',
                done: 'Done'
            };
            return texts[status] || status;
        }

        // Drag and drop functions
        function dragStart(event, id) {
            draggedElement = id;
            event.target.classList.add('card-dragging');
        }

        function allowDrop(event) {
            event.preventDefault();
            event.currentTarget.classList.add('drag-over');
        }

        function drop(event, newStatus) {
            event.preventDefault();
            event.currentTarget.classList.remove('drag-over');
            
            if (draggedElement) {
                const activity = activities.find(a => a.id === draggedElement);
                if (activity) {
                    activity.status = newStatus;
                    saveData();
                    renderAllCards();
                }
                draggedElement = null;
            }
        }

        // Remove drag over effect when leaving
        document.addEventListener('dragleave', function(event) {
            if (event.target.classList.contains('space-y-4')) {
                event.target.classList.remove('drag-over');
            }
        });

        document.addEventListener('dragend', function(event) {
            event.target.classList.remove('card-dragging');
            document.querySelectorAll('.drag-over').forEach(el => {
                el.classList.remove('drag-over');
            });
        });

        // Edit Mode Functions
        function toggleEditMode() {
            isEditMode = !isEditMode;
            const editBtn = document.getElementById('editModeBtn');
            
            if (isEditMode) {
                editBtn.textContent = '✅ Exit Edit Mode';
                editBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                editBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                editBtn.textContent = '✏️ Edit Mode';
                editBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                editBtn.classList.add('bg-purple-600', 'hover:bg-purple-700');
            }
            
            renderAllCards();
        }

        function handleColumnClick(event, columnType) {
            // Only handle clicks on the column itself, not on cards
            if (event.target.id === 'coming-column' && columnType === 'coming') {
                openAddModal();
            }
        }

        // Initialize
        loadData();
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98cf3086d6026bf4',t:'MTc2MDE5NDQzMy4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
