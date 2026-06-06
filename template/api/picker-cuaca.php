

<div id="weather-display">
    <span id="loader">connecting...</span>
</div>

<script>
    const displayElement = document.getElementById('weather-display');
    let weatherData = [];
    let currentIndex = 0;
    const intervalTime = 3000; // 3 detik

    /**
     * Mengambil data dari skrip PHP yang menghasilkan JSON.
     */
    async function fetchData() {
        try {
            // Panggil file PHP yang telah dimodifikasi
            const response = await fetch('fetch_data.php'); 
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            return data;

        } catch (error) {
            console.error('Gagal memuat data:', error);
            displayElement.innerHTML = `ERROR: ${error.message}. Cek file PHP/API/DB.`;
            return null;
        }
    }

    /**
     * Memformat data untuk ditampilkan.
     * Contoh format: Tulamben 28.3 °C | Overcast Cloud | Humidity 80%
     */
    function formatDisplay(item) {
        return `${item.name} ${item.temp} °C | ${item.desc} | Humidity ${item.humidity}%`;
    }

    /**
     * Mengganti tampilan cuaca berikutnya dengan efek fade.
     */
    function updateDisplay() {
        if (weatherData.length === 0) return;

        // Fade out
        displayElement.style.opacity = 0;

        setTimeout(() => {
            // Tampilkan data berikutnya
            const currentItem = weatherData[currentIndex];
            displayElement.innerHTML = formatDisplay(currentItem);
            
            // Fade in
            displayElement.style.opacity = 1;

            // Pindah ke indeks berikutnya
            currentIndex = (currentIndex + 1) % weatherData.length;
        }, 500); // 500ms untuk efek transisi

        // Set timer untuk update berikutnya
        setTimeout(updateDisplay, intervalTime);
    }

    /**
     * Fungsi utama untuk inisialisasi.
     */
    async function init() {
        displayElement.style.opacity = 0.5; // Tampilkan status loading samar
        
        const data = await fetchData();
        
        if (data) {
            weatherData = data;
            if (weatherData.length > 0) {
                // Hapus loading, mulai siklus pertama
                displayElement.style.opacity = 1; 
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
