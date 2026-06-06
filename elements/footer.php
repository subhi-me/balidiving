<?php include 'elements/main-footers.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        // Daftar semua skrip yang ingin dimuat
        const scripts = [
            "assets/web/assets/jquery/jquery.min.js",
            "assets/tether/tether.min.js",
            "assets/popper/popper.min.js",
            "assets/bootstrap/js/bootstrap.min.js",
            "assets/vimeoplayer/jquery.mb.vimeo_player.js",
            "assets/viewportchecker/jquery.viewportchecker.js",
            "assets/ytplayer/jquery.mb.ytplayer.min.js",
            "assets/smoothscroll/smooth-scroll.js",
            "assets/sociallikes/social-likes.js",
            "assets/parallax/jarallax.min.js",
            "assets/dropdown/js/nav-dropdown.js",
            "assets/dropdown/js/navbar-dropdown.js",
            "assets/touchswipe/jquery.touch-swipe.min.js",
            "assets/theme/js/script.js"
        ];

        // Fungsi untuk memuat skrip satu per satu
        scripts.forEach(function(src) {
            let script = document.createElement('script');
            script.src = src;
            document.body.appendChild(script);
        });

    }, 2000); // 3000 milidetik = 5 detik
});
</script>

<input name="animation" type="hidden">