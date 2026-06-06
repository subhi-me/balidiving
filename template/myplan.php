<section class="w-full py-16 md:py-24 bg-gradient-to-b from-white via-slate-50 to-slate-100">
  <div class="max-w-6xl mx-auto px-5 md:px-8">

    <!-- MOBILE (stacked, centered) + DESKTOP (wide, 2-column) -->
    <div class="rounded-3xl border border-slate-200/70 bg-white/70 backdrop-blur-xl shadow-[0_18px_45px_rgba(2,6,23,.08)]
                p-7 sm:p-10 md:p-14
                grid gap-10 md:gap-14
                md:grid-cols-[1.2fr_.8fr] md:items-center">

      <!-- Left: Copy -->
      <div class="text-center md:text-left">
        <span class="inline-flex items-center gap-2 mb-4 px-4 py-1.5 rounded-full text-sm font-semibold
                     bg-slate-100 text-slate-600 tracking-wide">
          <span class="h-2 w-2 rounded-full bg-slate-400"></span>
          Booking Overview
        </span>

        <h2 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-4 leading-tight">
          Ready to plan your dive?
        </h2>

        <p class="text-lg text-slate-600 leading-relaxed">
          All diving options are prepared and ready.
          <br class="hidden sm:block">
          <span class="text-slate-800 font-medium">
            Clear, straightforward, and pressure-free.
          </span>
          Simply review and proceed.
        </p>

        <!-- Desktop-only trust line under copy (looks premium) -->
        <div class="hidden md:flex items-center gap-3 mt-6 text-sm text-slate-500">
          <span class="inline-flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
            Trusted by divers worldwide
          </span>



        </div>
      </div>

      <!-- Right: CTA card -->
      <div class="text-center md:text-right">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-[0_12px_30px_rgba(2,6,23,.08)]
                    p-6 sm:p-7 md:p-8">
          <p class="text-sm font-semibold text-slate-700 mb-2">
            Next step
          </p>

          <p class="text-base text-slate-600 mb-6">
            Open your booking plan and continue to checkout.
          </p>

          <a href="https://balidiving.com/cart/my-booking"
             class="group inline-flex w-full md:w-auto items-center justify-center gap-3
                    px-9 py-4 rounded-full
                    bg-navy text-white font-semibold tracking-wide
                    shadow-lg shadow-navy/20
                    hover:shadow-xl hover:scale-[1.03]
                    transition-all duration-300">
            <span>My Plan</span>
            <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
          </a>

          <!-- Mobile trust line (kept small, clean) -->
          <p class="mt-5 md:hidden text-xs text-slate-500">
            Trusted by divers worldwide
          </p>
        </div>
      </div>

    </div>
  </div>
</section>
