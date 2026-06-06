<!-- ✅ FULL SECTION: Transparent Search (Dynamic Placeholder + Redirect) -->
<section class="bd-search-section" aria-label="Bali Diving Search">
  <div class="bd-search-wrap">
  
    <p class="bd-search-subtitle">
      Type keywords like <strong>Manta</strong>, <strong>Nusa Penida</strong>, <strong>Beginner</strong>, or <strong>Scuba Course</strong>.
    </p>

    <form id="bdSearchForm" class="bd-search-form" role="search" autocomplete="off">
      <div class="bd-search-input-group">
        <input
          type="search"
          id="bdSearchInput"
          name="q"
          class="bd-search-input"
          placeholder="Search snorkeling & diving experiences in Bali"
          aria-label="Search snorkeling and diving recommendations"
          required
        />
        <button type="submit" class="bd-search-btn">
          Search
        </button>
      </div>
    </form>
  </div>

  <style>
    /* SECTION FULL TRANSPARENT */
    .bd-search-section{
      padding: 24px 16px;
      background: transparent;
    }

    .bd-search-wrap{
      max-width: 860px;
      margin: 0 auto;
      background: transparent;
      border: none;
      box-shadow: none;
      color: #ffffff;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }

    .bd-search-title{
      margin: 0 0 6px;
      font-size: 22px;
      line-height: 1.25;
    }

    .bd-search-subtitle{
      margin: 0 0 14px;
      font-size: 14px;
      opacity: .85;
    }

    /* INPUT GROUP */
    .bd-search-input-group{
      display: flex;
      gap: 10px;
      align-items: center;
      padding: 10px;
      border-radius: 14px;
      backdrop-filter: blur(8px);
      background: rgba(255,255,255,.08); /* semi glass, NOT solid */
      border: 1px solid rgba(255,255,255,.20);
    }

    .bd-search-input{
      flex: 1;
      border: none;
      outline: none;
      background: transparent;
      color: #ffffff;
      font-size: 15px;
      padding: 10px 12px;
    }

    .bd-search-input::placeholder{
      color: rgba(255,255,255,.75);
    }

    .bd-search-btn{
      padding: 11px 18px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      font-weight: 700;
      font-size: 14px;
      color: #0b1220;
      background: #58d7ff;
      white-space: nowrap;
    }

    .bd-search-btn:hover{
      opacity: .92;
    }

    @media (max-width: 520px){
      .bd-search-input-group{
        flex-direction: column;
        align-items: stretch;
      }
      .bd-search-btn{
        width: 100%;
      }
    }
  </style>

  <script>
    (function(){
      const input = document.getElementById("bdSearchInput");
      const form  = document.getElementById("bdSearchForm");

      const placeholders = [
        "Search snorkeling & diving experiences in Bali",
        "Snorkeling in crystal-clear Bali waters",
        "Scuba diving with certified instructors in Bali",
        "Explore Bali’s best dive sites",
        "Snorkel with manta rays in Nusa Penida",
        "Beginner-friendly snorkeling trips in Bali",
        "Try scuba diving in Bali — no experience needed"
      ];

      let i = 0;
      let paused = false;

      setInterval(() => {
        if (paused) return;
        if (document.activeElement === input) return;
        if (input.value.trim() !== "") return;

        i = (i + 1) % placeholders.length;
        input.placeholder = placeholders[i];
      }, 2800);

      input.addEventListener("focus", () => paused = true);
      input.addEventListener("blur", () => paused = false);

      form.addEventListener("submit", function(e){
        e.preventDefault();
        const keyword = input.value.trim();
        if (!keyword) return;

        window.location.href =
          "https://balidiving.com/recommendations?q=" +
          encodeURIComponent(keyword);
      });
    })();
  </script>
</section>
