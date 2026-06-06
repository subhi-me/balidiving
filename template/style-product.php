 <style>
      *{box-sizing:border-box;}

    .hero{position:relative;height:480px;overflow:hidden;}
    .hero-background{
      position:absolute;top:0;left:0;width:110%;height:110%;
      background-size:cover;background-position:center;
      animation:heroPan 9s infinite;
      transform-origin:center center;
    }
    @keyframes heroPan{
      0%{transform:scale(1) translate(0,0);}
      40%{transform:scale(1.06) translate(-2%, -1%);}
      50%{transform:scale(1); }
      90%{transform:scale(1.06) translate(2%,1%);}
      100%{transform:scale(1) translate(0,0);}
    }
    .hero-overlay{
      position:absolute;inset:0;
      background:linear-gradient(135deg,rgba(0,119,182,.8),rgba(0,180,216,.7));
      display:flex;flex-direction:column;justify-content:center;align-items:center;
      text-align:center;padding:2rem;
      color:#fff;
    }
    .hero-title{font-size:3.2rem;font-weight:800;margin:0 0 1rem;text-shadow:0 3px 8px rgba(0,0,0,.4);}
    .hero-subtitle{font-size:1.4rem;margin:0 0 1rem;text-shadow:0 2px 6px rgba(0,0,0,.3);}
    .hero-badge{
      display:inline-flex;align-items:center;gap:.5rem;
      background:rgba(15,23,42,.8);padding:.4rem .9rem;border-radius:999px;
      font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;
    }

    .article-section{max-width:900px;margin:3rem auto;padding:0 1.5rem;}
    .article-title{text-align:center;font-size:2.4rem;color:#0f172a;margin-bottom:1rem;}
    .article-content{font-size:1.05rem;color:#334155;line-height:1.8;text-align:justify;}

    .accordion{margin-top:1.5rem;}
    .accordion-button{
      width:100%;padding:1rem 1.1rem;border-radius:12px;border:1px solid #cbd5f5;
      background:#e0f2fe;display:flex;align-items:center;justify-content:space-between;
      font-size:.98rem;color:#075985;cursor:pointer;transition:.2s;
    }
    .accordion-button:hover{background:#dbeafe;}
    .accordion-content{
      max-height:0;overflow:hidden;
      transition:max-height .25s ease;
      border-radius:0 0 12px 12px;border:1px solid #cbd5f5;border-top:none;
      background:#fff;
    }
    .accordion-content.active{max-height:600px;}
    .accordion-text{padding:1.2rem 1.4rem;font-size:1rem;color:#334155;line-height:1.8;}
    .accordion-icon{transition:transform .2s;}
    .accordion-button.active .accordion-icon{transform:rotate(180deg);}

    .section-title{text-align:center;font-size:2.3rem;color:#0f172a;margin:3rem 0 2rem;}
    .cards-section{max-width:1200px;margin:0 auto 3rem;padding:0 1.5rem;}
    .cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.8rem;}
    .card{
      background:#fff;border-radius:18px;overflow:hidden;
      box-shadow:0 15px 35px rgba(15,23,42,.12);
      cursor:pointer;transition:.25s;
    }
    .card:hover{transform:translateY(-6px);box-shadow:0 22px 45px rgba(15,23,42,.18);}
    .card-header{position:relative;}
    .card-image{width:100%;height:210px;object-fit:cover;display:block;}
    .duration-badge{
      position:absolute;top:1rem;right:1rem;
      padding:.35rem .75rem;border-radius:999px;
      background:rgba(15,23,42,.82);color:#e0f2fe;font-size:.75rem;font-weight:600;
      display:inline-flex;align-items:center;gap:.35rem;
    }
    .card-content{padding:1.4rem 1.5rem 1.3rem;}
    .card-title{margin:0 0 .4rem;font-size:1.25rem;color:#0f172a;}
    .card-summary{margin:0 0 1rem;font-size:.95rem;color:#64748b;line-height:1.6;}
    .card-select-btn{
      width:100%;padding:.7rem;border-radius:999px;border:none;
      background:linear-gradient(135deg,#0ea5e9,#22c55e);color:#fff;font-weight:600;
      cursor:pointer;font-size:.96rem;box-shadow:0 10px 20px rgba(14,165,233,.35);
      transition:.18s;
    }
    .card-select-btn:hover{transform:translateY(-1px);box-shadow:0 13px 28px rgba(14,165,233,.4);}

    .offcanvas-overlay{
      position:fixed;inset:0;background:rgba(15,23,42,.6);
      display:none;z-index:60;
    }
    .offcanvas-overlay.active{display:block;}
    .offcanvas{
      position:fixed;top:0;right:-100%;width:95%;max-width:520px;height:100%;
      background:#fff;border-radius:1.1rem 0 0 1.1rem;
      box-shadow:-18px 0 45px rgba(15,23,42,.35);
      z-index:70;display:flex;flex-direction:column;
      transition:right .28s ease;
    }
    .offcanvas.active{right:0;}
    .offcanvas-header{
      position:relative;padding:1.1rem 1.3rem;
      background:#0f172a;color:#fff;
    }
    .offcanvas-close{
      border:none;background:transparent;color:#fff;font-size:1.6rem;cursor:pointer;float:right;
    }
    .offcanvas-title{margin:0;padding:.7rem .9rem;border-radius:.8rem;
      background:rgba(15,23,42,.75);backdrop-filter:blur(10px);
      display:inline-flex;align-items:center;gap:.5rem;font-size:1.25rem;}
    .offcanvas-body{padding:1.5rem;overflow-y:auto;flex:1;}
    .offcanvas-cover{width:100%;height:260px;object-fit:cover;border-radius:.9rem;}
    .offcanvas-description{margin:1.1rem 0 1.4rem;font-size:.98rem;color:#1f2933;line-height:1.7;}

    .map-section{margin-bottom:1.6rem;}
    .map-title{font-size:1.05rem;margin-bottom:.6rem;color:#0f172a;font-weight:600;}
    .location-map{width:100%;height:230px;border:none;border-radius:.9rem;}
    .open-map-link{
      display:inline-flex;align-items:center;gap:.4rem;margin-top:.7rem;
      padding:.4rem .7rem;border-radius:999px;border:1px solid #cbd5f5;
      font-size:.85rem;color:#0369a1;text-decoration:none;background:#eff6ff;
    }

    .price-section{background:#eff6ff;border-radius:1rem;padding:1.2rem 1.1rem;margin-bottom:1.4rem;}
    .price-label{font-size:.82rem;color:#64748b;margin-bottom:.15rem;text-transform:uppercase;letter-spacing:.08em;}
    .price-main{display:flex;align-items:flex-end;gap:.5rem;}
    .price-amount{font-size:1.9rem;font-weight:800;color:#0f766e;}
    .price-original{font-size:.98rem;color:#94a3b8;text-decoration:line-through;}
    .price-discount{
      padding:.15rem .45rem;border-radius:.45rem;background:#f97316;
      color:#fff;font-size:.75rem;font-weight:700;text-transform:uppercase;
    }

    .countdown-section{margin-top:.85rem;border-top:1px dashed #bfdbfe;padding-top:.7rem;}
    .countdown-label{font-size:.8rem;color:#b91c1c;margin-bottom:.5rem;}
    .countdown-timer{display:flex;gap:.5rem;}
    .countdown-item{
      flex:1;display:flex;flex-direction:column;align-items:center;
      background:#fef2f2;border-radius:.6rem;border:1px solid #fecaca;
      padding:.5rem .3rem;
    }
    .countdown-number{font-size:1.25rem;font-weight:700;color:#b91c1c;}
    .countdown-unit{font-size:.7rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;}

    .include-exclude-section{margin-bottom:1.4rem;}

    .success-message{
      display:none;margin:.4rem 0 1rem;padding:.7rem .8rem;
      border-radius:.7rem;background:#dcfce7;color:#166534;font-size:.86rem;
    }
    .success-message.show{display:block;}

    .calendar-container{border-radius:1rem;border:1px solid #e2e8f0;background:#fff;overflow:hidden;}
    .calendar-header{
      display:flex;align-items:center;justify-content:space-between;
      padding:.8rem 1rem;background:#eff6ff;border-bottom:1px solid #e2e8f0;
    }
    .calendar-title{margin:0;font-size:.98rem;font-weight:600;color:#0f172a;}
    .calendar-nav{
      border:none;background:transparent;font-size:1.4rem;color:#0ea5e9;
      cursor:pointer;border-radius:.5rem;width:32px;height:32px;display:grid;place-items:center;
    }
    .calendar-nav:hover{background:rgba(14,165,233,.08);}
    .calendar-grid{padding:.7rem .8rem .9rem;}
    .calendar-weekdays{
      display:grid;grid-template-columns:repeat(7,1fr);gap:.25rem;margin-bottom:.2rem;
      font-size:.75rem;color:#94a3b8;text-align:center;
    }
    .calendar-days{
      display:grid;grid-template-columns:repeat(7,1fr);gap:.3rem;
    }
    .calendar-day{
      aspect-ratio:1;border-radius:.55rem;font-size:.78rem;
      display:flex;align-items:center;justify-content:center;
      border:1px solid transparent;cursor:pointer;transition:.15s;
    }
    .calendar-day.available{background:#f8fafc;color:#0f172a;}
    .calendar-day.available:hover{background:#e0f2fe;border-color:#0ea5e9;}
    .calendar-day.unavailable{background:#f3f4f6;color:#cbd5f5;cursor:not-allowed;opacity:.6;}
    .calendar-day.selected{background:#0ea5e9;color:#fff;font-weight:700;}
    .calendar-day.other-month{background:transparent;color:#cbd5e1;cursor:default;}
    .calendar-day.past{background:#f9fafb;color:#d1d5db;cursor:not-allowed;opacity:.7;}

    .form-group{margin-top:1.2rem;margin-bottom:1.1rem;}
    .form-label{display:block;font-size:.9rem;color:#111827;margin-bottom:.35rem;font-weight:500;}
    .form-input{
      width:100%;padding:.7rem .75rem;border-radius:.6rem;border:1px solid #d1d5db;
      font-size:.96rem;
    }
    .form-input:focus{outline:none;border-color:#0ea5e9;box-shadow:0 0 0 1px rgba(14,165,233,.3);}
    .form-group.hidden{display:none;}

    .checkout-button{
      width:100%;margin-top:.3rem;padding:.85rem;border-radius:.9rem;border:none;
      background:linear-gradient(135deg,#0ea5e9,#22c55e);
      color:#fff;font-weight:700;font-size:1rem;cursor:pointer;
      display:none;box-shadow:0 12px 25px rgba(14,165,233,.4);
      transition:.18s;
    }
    .checkout-button.show{display:block;}
    .checkout-button:hover{transform:translateY(-1px);box-shadow:0 15px 30px rgba(14,165,233,.5);}
    .checkout-button:disabled{opacity:.65;cursor:not-allowed;transform:none;box-shadow:none;}

    @media (max-width:768px){
      .hero-title{font-size:2.4rem;}
      .hero-subtitle{font-size:1.1rem;}
      .offcanvas{width:100%;border-radius:0;}
    }
  </style>