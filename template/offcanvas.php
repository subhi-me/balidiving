<div id="chatOffCanvas" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col">
    
    <div class="flex justify-between items-center p-4 border-b bg-gray-50">
        <h2 class="text-xl font-semibold text-navy">Chat with an Expert</h2>
        <button id="closeOffCanvasBtn" class="text-gray-500 hover:text-gray-800 transition-colors">
            <i class="fas fa-times fa-lg"></i>
        </button>
    </div>

    <div class="flex-grow p-6 overflow-y-auto">
        <p class="text-gray-600">
            Welcome! How can we help you plan your Bali diving adventure? Feel free to ask anything about our trips, safety, or the marine life you'll see.
        </p>
        </div>

    <div class="bg-gray-50 p-4 border-t">
        <div class="flex space-x-2">
            <input type="text" placeholder="Type your message..." class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <button class="bg-primary hover:bg-opacity-90 text-white rounded-full px-5 py-2 text-sm font-medium transition-colors">
                Send
            </button>
        </div>
    </div>
</div>

<div id="offCanvasOverlay" class="fixed inset-0 bg-black bg-opacity-60 hidden z-40"></div>
<script>
    // --- Logika untuk Off-Canvas ---
    const chatOffCanvas = document.getElementById('chatOffCanvas');
    const offCanvasOverlay = document.getElementById('offCanvasOverlay');
    const closeOffCanvasBtn = document.getElementById('closeOffCanvasBtn');

    // Fungsi untuk membuka off-canvas
    const openOffCanvas = () => {
        offCanvasOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Mencegah scroll di background
        chatOffCanvas.classList.remove('translate-x-full');
    };

    // Fungsi untuk menutup off-canvas
    const closeOffCanvas = () => {
        offCanvasOverlay.classList.add('hidden');
        document.body.style.overflow = ''; // Mengizinkan scroll lagi
        chatOffCanvas.classList.add('translate-x-full');
    };

    // Event listener untuk tombol close dan overlay
    closeOffCanvasBtn.addEventListener('click', closeOffCanvas);
    offCanvasOverlay.addEventListener('click', closeOffCanvas);

    // Fungsi baru yang akan dipanggil oleh tombol "Inquire via Chat"
    function inquireAndOpenCanvas(topic) {
        console.log("Inquiring about: " + topic);
        // Di sini Anda bisa menambahkan logika untuk mengisi chat dengan topik awal
        // Contoh: document.querySelector('#chatOffCanvas input').value = `I have a question about ${topic}`;
        
        openOffCanvas(); // Membuka panel
    }
</script>