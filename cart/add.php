<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Floating Action Button</title>

  <style>
    @view-transition { navigation: auto; }
    
    .bd-fab-wrapper {
      position: fixed;
      left: 0; right: 0; bottom: 1.5rem;
      z-index: 9999;
      display: flex; justify-content: center; align-items: center;
      pointer-events: none;
    }
    
    .bd-fab-btn {
      pointer-events: auto;
      display: flex; justify-content: center; align-items: center;
      width: 4rem; height: 4rem;
      border-radius: 9999px;
      color: #fff;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
      transition: all 0.3s ease-out;
      opacity: 0; transform: translateY(12px) scale(0.95);
      text-decoration: none;
    }
    
    .bd-fab-btn.show {
      opacity: 1; transform: translateY(0) scale(1);
    }
    
    .bd-fab-btn:hover {
      transform: scale(1.1);
    }
    
    .bd-fab-btn:active {
      transform: scale(0.95);
    }
    
    .bd-fab-btn:focus {
      outline: none;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
    }
    
    .bd-fab-icon {
      width: 2rem; height: 2rem;
      transition: transform 0.3s;
    }
    
    .bd-fab-btn:hover .bd-fab-icon {
      transform: rotate(90deg);
    }
  </style>
</head>

<body>
  <!-- FAB wrapper: fixed biar selalu kebaca & nggak ketimpa layout -->
  <div class="bd-fab-wrapper">
    <a id="floatingButton"
       href="https://balidiving.com/booking"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Book now"
       class="bd-fab-btn"
    >
      <svg class="bd-fab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
      </svg>
    </a>
  </div>

  <script>
    const defaultConfig = {
      button_url: "https://balidiving.com/booking",
      primary_color: "#3b82f6",
      secondary_color: "#2563eb"
    };

    let config = { ...defaultConfig };

    function applyConfig() {
      const button = document.getElementById("floatingButton");
      if (!button) return;

      const url = config.button_url || defaultConfig.button_url;
      const primaryColor = config.primary_color || defaultConfig.primary_color;
      const secondaryColor = config.secondary_color || defaultConfig.secondary_color;

      button.href = url;

      // Background gradient
      button.style.background = `linear-gradient(to bottom right, ${primaryColor}, ${secondaryColor})`;
    }

    async function onConfigChange(newConfig) {
      config = { ...config, ...newConfig };
      applyConfig();
    }

    function mapToCapabilities(cfg) {
      return {
        recolorables: [
          {
            get: () => cfg.primary_color || defaultConfig.primary_color,
            set: (value) => {
              cfg.primary_color = value;
              if(window.elementSdk) window.elementSdk.setConfig({ primary_color: value });
            }
          },
          {
            get: () => cfg.secondary_color || defaultConfig.secondary_color,
            set: (value) => {
              cfg.secondary_color = value;
              if(window.elementSdk) window.elementSdk.setConfig({ secondary_color: value });
            }
          }
        ],
        borderables: [],
        fontEditable: undefined,
        fontSizeable: undefined
      };
    }

    function mapToEditPanelValues(cfg) {
      return new Map([
        ["button_url", cfg.button_url || defaultConfig.button_url]
      ]);
    }

    // Hanya panggil elementSdk.init jika environment mendukung (menghindari undefined error di production)
    if (window.elementSdk && typeof window.elementSdk.init === "function") {
      window.elementSdk.init({
        defaultConfig,
        onConfigChange,
        mapToCapabilities,
        mapToEditPanelValues
      });
    }

    // initial apply
    applyConfig();

    // Show after 5 seconds + subtle entrance
    setTimeout(() => {
      const button = document.getElementById("floatingButton");
      if (button) {
        button.classList.add("show");
      }
    }, 5000);
  </script>
</body>
</html>
