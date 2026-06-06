

<div id="weather-display">
    <span id="loader">Memuat data cuaca...</span>
</div>

<script>
    const displayElement = document.getElementById('weather-display');
    let weatherData = [];
    let currentIndex = 0;
    const intervalTime = 3000; // 3 detik

    /**
     * Mengambil data dari skrip PHP (weather_json.php) yang menghasilkan JSON.
     */
    async function fetchData() {
        try {
            // Memanggil weather_json.php yang akan menjalankan logika bisnis
            const response = await fetch('weather_json.php'); 
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            if (data.error) {
                // Tangani error dari DB atau API
                throw new Error(data.error);
            }

            return data;

        } catch (error) {
            console.error('Gagal memuat data:', error);
            // Tampilkan error ke layar
            displayElement.innerHTML = `ERROR: ${error.message}`;
            return null;
        }
    }

    /**
     * Memformat data untuk ditampilkan (Format: Lokasi Suhu °C | Kondisi | Kelembaban %)
     */
    function formatDisplay(item) {
        // Tambahkan ikon kecil berdasarkan deskripsi cuaca
        let icon = '';
        if (item.desc.toLowerCase().includes('clear')) {
            icon = '☀️';
        } else if (item.desc.toLowerCase().includes('cloud')) {
            icon = '☁️';
        } else if (item.desc.toLowerCase().includes('rain')) {
            icon = '🌧️';
        } else if (item.desc.toLowerCase().includes('n/a')) {
            icon = '⚠️';
        }

        return `${icon} ${item.name} ${item.temp} °C | ${item.desc} | Hum. ${item.humidity}%`;
    }

    /**
     * Mengganti tampilan cuaca berikutnya dengan efek fade.
     */
    function updateDisplay() {
        if (weatherData.length === 0) return;

        // 1. Fade out
        displayElement.style.opacity = 0;

        setTimeout(() => {
            // 2. Ganti konten
            const currentItem = weatherData[currentIndex];
            displayElement.innerHTML = formatDisplay(currentItem);
            
            // 3. Fade in
            displayElement.style.opacity = 1;

            // 4. Pindah ke indeks berikutnya
            currentIndex = (currentIndex + 1) % weatherData.length;
        }, 500); // Waktu yang sama dengan transisi CSS

        // Set timer untuk update berikutnya
        setTimeout(updateDisplay, intervalTime);
    }

    /**
     * Inisialisasi dan memuat data.
     */
    async function init() {
        displayElement.style.opacity = 0.5; 
        
        const data = await fetchData();
        
        if (data && Array.isArray(data)) {
            weatherData = data;
            if (weatherData.length > 0) {
                displayElement.style.opacity = 1; 
                // Tampilkan item pertama secara instan
                displayElement.innerHTML = formatDisplay(weatherData[currentIndex]);
                currentIndex = (currentIndex + 1) % weatherData.length;
                
                // Mulai timer setelah jeda interval pertama
                setTimeout(updateDisplay, intervalTime);
            } else {
                displayElement.innerHTML = "Tidak ada data lokasi yang ditemukan.";
            }
        }
    }

    // Jalankan inisialisasi saat halaman dimuat
    init();

</script>
