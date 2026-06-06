<nav class="fixed top-0 w-full bg-[#063c7f] shadow-lg border-b border-[#0b5594] z-[9]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div id="nav-content" class="flex justify-between items-center h-16 transition-all duration-300">
      <div class="flex items-center space-x-2">
        <div class="w-10 h-10 flex items-center justify-center">
          <a href="https://balidiving.com">
            <img src="../images/bali-diving-logo.svg" width="80%" alt="Bali Diving Logo">
          </a>
        </div>
        <h3 class="text-2xl font-bobalild text-white">Bali Diving</h3>
      </div>

      <div class="hidden md:flex space-x-6 items-center">
        <a href="https://balidiving.com/explore/" class="text-white hover:text-lightblue transition-colors">Explore Bali Diving</a>
 
      </div>

      <button id="mobile-menu-btn" class="md:hidden text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>
  </div>

  <div id="mobile-menu" class="hidden md:hidden bg-[#063c7f] border-t border-white/20">
    <div class="px-2 pt-2 pb-3 space-y-1">
      <a href="https://www.balidiving.com/snorkeling-packages.php" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Snorkeling</a>
      <a href="https://www.balidiving.com/try-diving" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Try Diving</a>
      <a href="https://www.balidiving.com/go-diving" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Go Diving</a>
      <a href="https://www.balidiving.com/learn-diving" class="block px-3 py-2 text-white hover:bg-white/10 rounded-md">Learn Diving</a>

      <div>
        <div id="pricelist-submenu" class="hidden pl-6 mt-1 space-y-1">
          <a href="#snorkeling-prices" class="block px-3 py-2 text-white/80 hover:bg-white/10 rounded-md text-sm">Snorkeling</a>
          <a href="#diving-prices" class="block px-3 py-2 text-white/80 hover:bg-white/10 rounded-md text-sm">Diving Courses</a>
          <a href="#fun-dives" class="block px-3 py-2 text-white/80 hover:bg-white/10 rounded-md text-sm">Fun Dives</a>
        </div>
      </div>
    </div>
  </div>
</nav>
