<?php include('01-start.php')?>
<section id="faq" class="relative w-full py-16 md:py-20">
  <div class="max-w-[980px] mx-auto px-4 sm:px-6">

    <div class="text-center mb-6">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-slate-200 bg-white/70 backdrop-blur text-[12px] font-extrabold tracking-wide text-slate-700">
        <i class="fa-solid fa-circle-question"></i>
        FAQ
      </div>

      <h2 class="mt-3 text-slate-900 font-black tracking-tight leading-[1.1] text-[clamp(26px,3.2vw,40px)]">
        Scuba Diving & Snorkeling FAQs
      </h2>

      <p class="mt-2 text-slate-600 text-[15px] leading-relaxed max-w-[62ch] mx-auto">
        Clear answers. Tap WhatsApp on any question to ask Bali Diving instantly.
      </p>
    </div>

    <div id="bdFaq" class="grid gap-3 md:gap-4">

      <!-- 1 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: Can complete beginners join a scuba dive in Bali?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-person-swimming"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Can beginners scuba dive?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Scuba • Intro / Try Dive</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Yes. We offer beginner-friendly intro dives with a full briefing and close supervision.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa
                 class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
              <a href="#grid"
                 onclick="event.preventDefault(); document.getElementById('grid')?.scrollIntoView({behavior:'smooth', block:'start'});"
                 class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-slate-700 font-extrabold">
                <i class="fa-solid fa-compass"></i>
                Explore packages
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 2 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: Do I need a license/certification to scuba dive?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-id-card"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Do I need a certification?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Scuba • Certified vs Intro</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              For fun dives, usually yes (Open Water). For intro/try dives, no certification is required.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 3 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: What is the best snorkeling area in Bali for first-timers?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-mask-snorkel"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Best snorkeling for beginners?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Snorkeling • Spots</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              It depends on sea conditions and your hotel location. We’ll recommend the best calm + clear spot for your day.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 4 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: Can I see manta rays? Which trip should I choose?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-water"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Manta rays: which trip?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Scuba/Snorkel • Nusa Penida</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Possible—wild animals, so no guarantees. We’ll suggest the best route + timing based on conditions.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 5 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: What should I bring for scuba/snorkeling and do you provide all gear?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-suitcase"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                What should I bring?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Gear • What to pack</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Bring a swimsuit, towel, sunscreen, and a change of clothes. We provide the gear—details depend on the package.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 6 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: Do you offer hotel pickup? Which areas are covered?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-van-shuttle"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Do you provide hotel pickup?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Logistics • Pickup areas</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Many trips include pickup (or it can be added). Coverage depends on the trip and start point.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 7 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: What is the minimum age for scuba diving and snorkeling?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-children"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Minimum age requirements?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Safety • Kids</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Age limits depend on the activity (intro dive vs course vs snorkeling). Tell us the age and we’ll match the safest option.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 8 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: How long does a typical day trip take (scuba or snorkeling)?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-clock"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                How long is the trip?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Schedule • Duration</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Most trips are half-day to full-day depending on location, boat time, and number of sessions.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 9 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: Is it safe if I cannot swim well? Can I still do snorkeling or an intro dive?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-life-ring"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                What if I’m not a strong swimmer?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Safety • Confidence</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Many guests are not strong swimmers. We’ll recommend the safest option (life jacket for snorkeling, extra supervision for intro dives).
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 10 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: Do you provide underwater photos/videos? What are the photo options and prices?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-camera"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Do you provide photos/videos?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Add-on • Memories</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Photo/video options depend on the trip and guide availability. Share your date + activity and we’ll confirm.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 11 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: What are the best months for visibility and calm sea in Bali for diving/snorkeling?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-cloud-sun"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Best season to dive/snorkel?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Weather • Visibility</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Bali is diveable year-round, but conditions vary by area. Share your travel dates and we’ll recommend the best spots.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 12 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: What is your cancellation or reschedule policy for diving/snorkeling trips?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Cancellation / reschedule policy?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Policy • Weather & safety</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              We prioritize safety and sea conditions. Share your trip + date and we’ll confirm the exact policy.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 13 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: Do you offer PADI courses (Open Water / Advanced / Refresher)?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Do you offer PADI courses?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Courses • PADI</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              Yes—courses and refreshers are available. Tell us your level and dates, and we’ll recommend the best plan.
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- 14 -->
      <article class="bd-faq-item rounded-2xl border border-slate-200/70 bg-white/90 shadow-[0_18px_55px_rgba(2,6,23,.14)] overflow-hidden"
        data-open="0" data-q="Hi Bali Diving, I have a question: I’m a certified diver. Which sites do you recommend (Tulamben, Amed, Padang Bai, Nusa Penida)?">
        <button class="bd-faq-q w-full flex items-center justify-between gap-4 p-4 md:p-[18px] text-left"
          type="button" aria-expanded="false">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl grid place-items-center border border-slate-200 bg-gradient-to-br from-blue-50 to-teal-50 text-blue-900 shrink-0">
              <i class="fa-solid fa-location-dot"></i>
            </div>
            <div class="min-w-0">
              <p class="font-black text-slate-900 leading-snug text-[15px] md:text-[16px]">
                Best dive sites for my level?
              </p>
              <p class="mt-1 text-[12.5px] text-slate-500">Scuba • Dive sites</p>
            </div>
          </div>
          <div class="bd-faq-chevron w-10 h-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50/60 text-slate-600 shrink-0 transition-transform duration-200">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </button>

        <div class="bd-faq-a max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out">
          <div class="p-4 md:p-[18px] pt-0 grid gap-3">
            <div class="h-px bg-slate-200/60"></div>
            <p class="text-[14px] leading-relaxed text-slate-700">
              It depends on your certification level, last dive date, and what you want to see (wreck, macro, manta, reef).
            </p>
            <div class="flex flex-wrap items-center gap-2">
              <a href="#" data-wa class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-gradient-to-br from-emerald-50 to-teal-50 text-slate-900 font-black shadow-[0_10px_26px_rgba(2,6,23,.10)] hover:-translate-y-[1px] transition">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                Ask on WhatsApp
              </a>
            </div>
          </div>
        </div>
      </article>

    </div>
  </div>

  <script>
    (function(){
      const root = document.getElementById('bdFaq');
      if(!root) return;

      const PHONE = '6287861190174';
      const waLink = (text) => 'https://wa.me/' + PHONE + '?text=' + encodeURIComponent(text || 'Hi Bali Diving, I have a question.');

      const items = Array.from(root.querySelectorAll('.bd-faq-item'));

      function closeAll(except){
        items.forEach(it => {
          if(except && it === except) return;
          it.dataset.open = '0';
          const btn = it.querySelector('.bd-faq-q');
          const panel = it.querySelector('.bd-faq-a');
          const chev = it.querySelector('.bd-faq-chevron');
          if(btn) btn.setAttribute('aria-expanded','false');
          if(panel) panel.style.maxHeight = '0px';
          if(chev) chev.classList.remove('rotate-180');
        });
      }

      function openItem(it){
        const panel = it.querySelector('.bd-faq-a');
        const inner = panel ? panel.firstElementChild : null;
        const btn = it.querySelector('.bd-faq-q');
        const chev = it.querySelector('.bd-faq-chevron');
        it.dataset.open = '1';
        if(btn) btn.setAttribute('aria-expanded','true');
        if(chev) chev.classList.add('rotate-180');
        if(panel && inner){
          panel.style.maxHeight = (inner.scrollHeight + 6) + 'px';
        }
      }

      items.forEach(it => {
        const qBtn = it.querySelector('.bd-faq-q');
        const panel = it.querySelector('.bd-faq-a');

        if(qBtn){
          qBtn.addEventListener('click', () => {
            const isOpen = it.dataset.open === '1';
            if(isOpen) closeAll();
            else { closeAll(it); openItem(it); }
          });
        }

        it.querySelectorAll('[data-wa]').forEach(a => {
          a.addEventListener('click', (e) => {
            e.preventDefault();
            const q = it.getAttribute('data-q') || 'Hi Bali Diving, I have a question.';
            window.open(waLink(q), '_blank', 'noopener,noreferrer');
          });
        });

        if(panel) panel.style.maxHeight = '0px';
      });

      window.addEventListener('resize', () => {
        const open = items.find(it => it.dataset.open === '1');
        if(open){
          const panel = open.querySelector('.bd-faq-a');
          const inner = panel ? panel.firstElementChild : null;
          if(panel && inner) panel.style.maxHeight = (inner.scrollHeight + 6) + 'px';
        }
      });
    })();
  </script>
</section>


<?php include('03-end.php')?>
