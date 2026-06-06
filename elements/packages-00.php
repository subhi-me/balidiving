<div id="delayed-package-wrapper" style="display: none;">

    <section class="features3 cid-rqwgfknW0g" id="package">
        <div class="mbr-overlay" style="opacity: 0.7; background-color: rgb(7, 59, 76);"></div>
        <div class="container">
            <div class="media-container-row">

                <div class="card p-3 col-12 col-md-6 col-lg-3">
                    <div class="card-wrapper">
                        <div class="card-img">
                            <a href="snorkeling">
                                <img data-src="assets/images/snorkeling-tours-bali-250x250.jpg" alt="Snorkeling Tours Bali" title="" class="lazy-image" width="250" height="250" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
                            </a>
                        </div>
                        <div class="card-box">
                            <h3 class="card-title mbr-fonts-style display-5">
                                <a href="snorkeling"><br>Snorkeling Tours<br></a>
                            </h3>
                            <p class="mbr-text mbr-fonts-style display-7">Anyone can explore Bali's beautiful coral reefs, even children and non-swimmers. Snorkel the same reefs as the divers.<br></p>
                        </div>
                        <div class="mbr-section-btn text-center"><a href="snorkeling" class="btn btn-primary display-4">More..</a></div>
                    </div>
                </div>

                <div class="card p-3 col-12 col-md-6 col-lg-3">
                    <div class="card-wrapper">
                        <div class="card-img">
                            <a href="try-scuba-diving">
                                <img data-src="assets/images/try-scuba-250x250.jpg" alt="Scuba Diving Bali" title="" class="lazy-image" width="250" height="250" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
                            </a>
                        </div>
                        <div class="card-box">
                            <h3 class="card-title mbr-fonts-style display-5"><a href="try-scuba-diving"><br>Try Scuba<br>Diving</a></h3>
                            <p class="mbr-text mbr-fonts-style display-7">Introduction to Scuba diving for virtually anyone. Experience Scuba Diving for the first time at Bali's best reef & wreck dives.<br></p>
                        </div>
                        <div class="mbr-section-btn text-center"><a href="try-scuba-diving" class="btn btn-primary display-4">More..</a></div>
                    </div>
                </div>

                <div class="card p-3 col-12 col-md-6 col-lg-3">
                    <div class="card-wrapper">
                        <div class="card-img">
                            <a href="scuba-diving-certification">
                                <img data-src="assets/images/day-diving-bali-250x250.jpg" alt="Day Diving in Bali" title="" class="lazy-image" width="250" height="250" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
                            </a>
                        </div>
                        <div class="card-box">
                            <h3 class="card-title mbr-fonts-style display-5"><a href="scuba-diving-certification"><br>Learn<br>Scuba Diving</a></h3>
                            <p class="mbr-text mbr-fonts-style display-7">Get your PADI with us, custom courses for novices available and Advanced courses available for qualified divers.</p>
                        </div>
                        <div class="mbr-section-btn text-center"><a href="scuba-diving-certification" class="btn btn-primary display-4">More..</a></div>
                    </div>
                </div>

                <div class="card p-3 col-12 col-md-6 col-lg-3">
                    <div class="card-wrapper">
                        <div class="card-img">
                            <a href="discover-scuba-diving-in-bali">
                                <img data-src="assets/images/dive-safaris-bali-349x349.jpg" alt="Dive Safaris in Bali" title="" class="lazy-image" width="250" height="250" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
                            </a>
                        </div>
                        <div class="card-box">
                            <h3 class="card-title mbr-fonts-style display-5">
                                <a href="discover-scuba-diving-in-bali"><br>Go Scuba<br>Diving<br></a>
                            </h3>
                            <p class="mbr-text mbr-fonts-style display-7">Single day trips or overnight packages for qualified divers at some of Bali's best dive sites. Single/group divers welcome.<br></p>
                        </div>
                        <div class="mbr-section-btn text-center"><a href="discover-scuba-diving-in-bali" class="btn btn-primary display-4">More..</a></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Menunda penampilan seksi selama 4 detik
    setTimeout(function() {
        const packageWrapper = document.getElementById('delayed-package-wrapper');
        if (packageWrapper) {
            packageWrapper.style.display = 'block';

            // Setelah seksi muncul, aktifkan lazy loading untuk gambar di dalamnya
            const lazyImages = packageWrapper.querySelectorAll('img.lazy-image');
            
            if ("IntersectionObserver" in window) {
                let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            let lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src; // Ganti src dengan data-src
                            lazyImage.classList.remove('lazy-image');
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            } else {
                // Fallback untuk browser yang sangat lama
                lazyImages.forEach(function(lazyImage) {
                    lazyImage.src = lazyImage.dataset.src;
                });
            }
        }
    }, 1000); // Penundaan 3000 milidetik = 4 detik
});
</script>