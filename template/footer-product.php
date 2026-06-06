<style>
    /* 🎯 Scoped Footer Styling (no conflict) */
.bd-footer {
  background-color: #063c7f;       /* paksa latar belakang footer = navy kamu */
}

/* teks biru muda di footer */
.bd-footer .text-lightblue {
  color: #a2d2fa;
}

/* link di footer: default lightblue, hover putih */
.bd-footer a {
  color: #a2d2fa;
  text-decoration: none;
}
.bd-footer a:hover {
  color: #ffffff;
}

/* garis atas footer */
.bd-footer .border-t {
  border-color: #3552c8;          /* pakai primary sebagai aksen */
}

/* logo partner / payment tetap terlihat di latar navy */
.bd-footer img.filter.brightness-0.invert {
  /* keep as-is, tidak diubah supaya tetap putih */
}

</style>
<footer class="bd-footer bg-navy text-white py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-8">

      <!-- Column 1 -->
      <div class="col-span-2 md:col-span-1">
        <div class="flex items-center space-x-2 mb-4">
          <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center">
            <img src="../images/bali-diving-logo.svg" alt="Bali Diving Logo" class="w-full h-auto filter">
          </div>
          <span class="text-2xl font-bold">Bali Diving</span>
        </div>
        <p class="text-lightblue mb-6 text-sm">
          Bali Diving is one of Bali’s longest established, internationally accredited Dive Centers.
        </p>
        <ul class="space-y-3 text-lightblue text-sm">
          <!-- Address -->
          <li class="flex items-start space-x-3">
            <svg class="w-5 h-5 mt-1 flex-shrink-0 text-white" fill="none" stroke="currentColor"
              viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>
              <a href="https://share.google/jno1tBuAct6SDgoLC">
                Jl. Bypass Ngurah Rai No.46E, Sanur Kauh, Denpasar Selatan, Kota Denpasar, Bali 80025
              </a>
            </span>
          </li>

          <!-- Office Phone -->
          <li class="flex items-center space-x-3">
            <svg class="w-5 h-5 flex-shrink-0 text-white" fill="none" stroke="currentColor"
              viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <a href="tel:+62361270791" class="hover:text-white transition-colors">+62 361 2707 91 (Office)</a>
          </li>

          <!-- Mobile -->
          <li class="flex items-center space-x-3">
            <svg class="w-5 h-5 flex-shrink-0 text-white" fill="none" stroke="currentColor"
              viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <a href="tel:+6287861190174" class="hover:text-white transition-colors">+62 878-6119-0174 (Mobile)</a>
          </li>

          <!-- Email Section -->
          <li class="flex flex-col space-y-2 mt-2">
            <span class="font-semibold text-white">EMAIL</span>
            <div class="pl-1">
              <p class="text-lightblue text-sm">
                Enquire & Booking:<br>
                <a href="mailto:sales@balidiving.com" class="hover:text-white">sales@balidiving.com</a>
              </p>
              <p class="text-lightblue text-sm mt-1">
                Information about your trip:<br>
                <a href="mailto:customer.service@balidiving.com" class="hover:text-white">customer.service@balidiving.com</a>
              </p>
            </div>
          </li>
        </ul>
      </div>

      <!-- Column 2 -->
      <div>
        <h4 class="text-lg font-bold mb-4 tracking-wide">Dive & Learn</h4>
        <ul class="space-y-2 text-lightblue text-sm">
          <li><h5 class="font-semibold text-white mb-1 mt-3">PADI Courses</h5></li>
          <li><a href="/courses/open-water" class="hover:text-white transition-colors">Open Water Diver</a></li>
          <li><a href="/courses/advanced-open-water" class="hover:text-white transition-colors">Advanced Open Water</a></li>
          <li><a href="/courses/rescue-diver" class="hover:text-white transition-colors">Rescue Diver</a></li>
          <li><a href="/courses/divemaster" class="hover:text-white transition-colors">Divemaster</a></li>
          <li><a href="/courses" class="hover:text-white font-semibold transition-colors">View All Courses...</a></li>
          <li><h5 class="font-semibold text-white mb-1 mt-3">Dive Sites</h5></li>
          <li><a href="/sites/nusa-penida" class="hover:text-white transition-colors">Nusa Penida</a></li>
          <li><a href="/sites/tulamben" class="hover:text-white transition-colors">Tulamben (USAT Liberty)</a></li>
          <li><a href="/sites/amed" class="hover:text-white transition-colors">Amed & Jemeluk Bay</a></li>
          <li><a href="/sites/padang-bai" class="hover:text-white transition-colors">Padang Bai</a></li>
          <li><a href="/sites" class="hover:text-white font-semibold transition-colors">Explore All Sites...</a></li>
        </ul>
      </div>

      <!-- Column 3 -->
      <div>
        <h4 class="text-lg font-bold mb-4 tracking-wide">Shop & Club</h4>
        <ul class="space-y-2 text-lightblue text-sm">
          <li><h5 class="font-semibold text-white mb-1 mt-3">Dive Shop</h5></li>
          <li><a href="/shop/equipment" class="hover:text-white transition-colors">Equipment</a></li>
          <li><a href="/shop/apparel" class="hover:text-white transition-colors">Apparel & Merch</a></li>
          <li><a href="/shop/accessories" class="hover:text-white transition-colors">Accessories</a></li>
          <li><a href="/rentals" class="hover:text-white transition-colors">Equipment Rentals</a></li>
          <li><h5 class="font-semibold text-white mb-1 mt-3">Membership</h5></li>
          <li><a href="https://balidiving.com/login" class="hover:text-white transition-colors">Join Our Dive Club</a></li>
          <li><a href="https://balidiving.com/login" class="hover:text-white transition-colors">Member Benefits</a></li>
          <li><a href="https://balidiving.com/login" class="hover:text-white transition-colors">Exclusive Events</a></li>
        </ul>
      </div>

      <!-- Column 4 -->
      <div>
        <h4 class="text-lg font-bold mb-4 tracking-wide">Resources</h4>
        <ul class="space-y-2 text-lightblue text-sm">
          <li><a href="/about-us" class="hover:text-white transition-colors">About Us</a></li>
          <li><a href="/team" class="hover:text-white transition-colors">Meet the Team</a></li>
          <li><a href="https://balidiving.com/articles" class="hover:text-white transition-colors">Dive Blog</a></li>
          <li><a href="/gallery" class="hover:text-white transition-colors">Photo Gallery</a></li>
          <li><a href="/faq" class="hover:text-white transition-colors">FAQ</a></li>
          <li><a href="/contact?page=contact" class="hover:text-white transition-colors">Contact Us</a></li>
          <li><a href="/privacy-policy" class="hover:text-white transition-colors">Privacy Policy</a></li>
          <li><a href="/terms-of-service" class="hover:text-white transition-colors">Terms of Service</a></li>
        </ul>
      </div>

      <!-- Column 5 - TikTok Card + Social Media -->
      <div class="col-span-2 md:col-span-1">
        <h4 class="text-lg font-bold mb-4 tracking-wide">Follow Our Dive Journey</h4>
        
        <!-- TikTok Card -->
        <a href="https://www.tiktok.com/@balidiving" target="_blank"
           class="relative block rounded-xl overflow-hidden shadow-lg group transition-transform transform hover:scale-[1.03] hover:shadow-2xl mb-6">
          <img src="../template/images/tiktok.jpg" alt="Bali Diving TikTok" 
               class="w-full h-60 object-cover group-hover:opacity-80 transition-opacity duration-300">
          <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/70 via-black/30 to-transparent p-4">
            <div class="flex items-center space-x-2 mb-2">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
<svg fill="#ffffff"  viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier">  <path d="M16.656 1.029c1.637-0.025 3.262-0.012 4.886-0.025 0.054 2.031 0.878 3.859 2.189 5.213l-0.002-0.002c1.411 1.271 3.247 2.095 5.271 2.235l0.028 0.002v5.036c-1.912-0.048-3.71-0.489-5.331-1.247l0.082 0.034c-0.784-0.377-1.447-0.764-2.077-1.196l0.052 0.034c-0.012 3.649 0.012 7.298-0.025 10.934-0.103 1.853-0.719 3.543-1.707 4.954l0.020-0.031c-1.652 2.366-4.328 3.919-7.371 4.011l-0.014 0c-0.123 0.006-0.268 0.009-0.414 0.009-1.73 0-3.347-0.482-4.725-1.319l0.040 0.023c-2.508-1.509-4.238-4.091-4.558-7.094l-0.004-0.041c-0.025-0.625-0.037-1.25-0.012-1.862 0.49-4.779 4.494-8.476 9.361-8.476 0.547 0 1.083 0.047 1.604 0.136l-0.056-0.008c0.025 1.849-0.050 3.699-0.050 5.548-0.423-0.153-0.911-0.242-1.42-0.242-1.868 0-3.457 1.194-4.045 2.861l-0.009 0.030c-0.133 0.427-0.21 0.918-0.21 1.426 0 0.206 0.013 0.41 0.037 0.61l-0.002-0.024c0.332 2.046 2.086 3.59 4.201 3.59 0.061 0 0.121-0.001 0.181-0.004l-0.009 0c1.463-0.044 2.733-0.831 3.451-1.994l0.010-0.018c0.267-0.372 0.45-0.822 0.511-1.311l0.001-0.014c0.125-2.237 0.075-4.461 0.087-6.698 0.012-5.036-0.012-10.060 0.025-15.083z"></path> </g></svg>
              <span class="text-lg font-semibold text-white">Bali Diving</span>
            </div>
            <p class="text-white text-sm">Watch our latest dives, ocean stories, and training highlights on TikTok!</p>
          </div>
        </a>

        <!-- Social Media Section -->
        <div class="mt-4">
          <h5 class="font-semibold text-white mb-3">Follow Us</h5>
          <div class="flex space-x-4">
            <a href="https://www.instagram.com/bali_diving/" aria-label="Instagram"
              class="text-white w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-accent transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16.65 7.2H16.66M8 20H16C18.2091 20 20 18.2091 20 16V8C20 5.79086 18.2091 4 16 4H8C5.79086 4 4 5.79086 4 8V16C4 18.2091 5.79086 20 8 20ZM15.75 12C15.75 14.0711 14.0711 15.75 12 15.75C9.92893 15.75 8.25 14.0711 8.25 12C8.25 9.92893 9.92893 8.25 12 8.25C14.0711 8.25 15.75 9.92893 15.75 12Z" />
              </svg>
            </a>
            <a href="https://www.facebook.com/balidivingsunfish" aria-label="Facebook"
              class="text-white w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-accent transition-colors">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                  d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h3.11V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                  clip-rule="evenodd" />
              </svg>
            </a>
            <a href="#" aria-label="YouTube"
              class="text-white w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-accent transition-colors">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M21.582,6.186c-0.23-0.86-0.908-1.538-1.768-1.768C18.254,4,12,4,12,4S5.746,4,4.186,4.418C3.326,4.648,2.648,5.326,2.418,6.186C2,7.746,2,12,2,12s0,4.254,0.418,5.814c0.23,0.86,0.908,1.538,1.768,1.768C5.746,20,12,20,12,20s6.254,0,7.814-0.418c0.86-0.23,1.538-0.908,1.768-1.768C22,16.254,22,12,22,12S22,7.746,21.582,6.186zM10,15.464V8.536L16,12L10,15.464z" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section -->
    <div class="border-t border-primary mt-10 pt-8 text-sm">
      <div class="flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="text-center md:text-left text-lightblue">
          &copy; <span id="current-year">2025</span> Bali Diving. All Rights Reserved.<br>
          <span class="opacity-80">PADI 5 Star Dive Center<br>PT. Bali Sunfish Safaris</span>
        </div>
<div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 md:gap-6">
  <img src="../images/logos/padi-logo.svg" alt="PADI 5 Star Dive Center"
       class="h-8 sm:h-10 md:h-12 filter brightness-0 invert object-contain">
  <img src="../images/logos/dan.svg" alt="DAN Partner"
       class="h-7 sm:h-9 md:h-10 filter brightness-0 invert object-contain">
  <img src="../images/logos/ta.svg" alt="TripAdvisor Partner"
       class="h-7 sm:h-9 md:h-10 filter brightness-0 invert object-contain">
  <img src="../images/logos/google-rating.svg" alt="Google Rating"
       class="h-7 sm:h-9 md:h-10 filter brightness-0 invert object-contain">
  <img src="../images/logos/gyg.svg" alt="GetYourGuide Partner"
       class="h-7 sm:h-9 md:h-10 filter brightness-0 invert object-contain">
</div>


        <div class="flex items-center gap-3">
          <span class="text-lightblue mr-2 hidden lg:block">We Accept:</span>
          <img src="../images/logos/pay.svg" alt="Payment Methods" class="h-10 filter brightness-0 invert">
        </div>
      </div>
    </div>
  </div>
</footer>

<script>
  document.getElementById('current-year').textContent = new Date().getFullYear();
</script>
