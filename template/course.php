<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PADI Certifications - PADI Diving Certifications</title>
  <meta name="description" content="Explore various PADI certification levels for professional divers: Open Water, Advanced, Rescue Diver, Divemaster, and Master Scuba Diver.">
  <meta name="keywords" content="PADI, diving certification, open water, advanced, rescue diver, divemaster, master scuba diver">
  <meta name="robots" content="index, follow">
  <script src="/_sdk/element_sdk.js"></script>
  <style>
        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100%;
        }

        .diving-section {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
        }

        .section-title {
            text-align: center;
            font-size: 2.8rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: 1px;
        }

        .course-section {
            margin-bottom: 4rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .section-heading {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(30, 64, 175, 0.2);
        }

        .section-description {
            font-size: 1.1rem;
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
            justify-items: center;
        }

        .diver-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 25px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
            max-width: 320px;
            width: 100%;
        }

        .diver-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .diver-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 102, 204, 0.2), transparent);
            transition: left 0.6s;
        }

        .diver-card:hover::before {
            left: 100%;
        }

        .diver-image-container {
            position: relative;
            margin-bottom: 2rem;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .diver-image {
            max-width: 120px;
            max-height: 120px;
            object-fit: contain;
            opacity: 0;
            transition: opacity 0.6s ease;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .diver-image.loaded {
            opacity: 1;
        }

        .diver-name {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1e40af;
            margin: 0 0 0.5rem 0;
            text-shadow: 0 1px 3px rgba(30, 64, 175, 0.2);
            line-height: 1.3;
        }

        .level-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);
            z-index: 10;
        }

        .level-badge.professional {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.4);
        }

        .depth-info {
            background: linear-gradient(135deg, #0ea5e9, #0369a1);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
            margin: 1rem 0 0.5rem 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 0 3px 10px rgba(14, 165, 233, 0.3);
        }

        /* Animasi organik seperti gerakan manusia berenang */
        .advanced-open-water .diver-image {
            animation: advancedSwim 8s ease-in-out infinite;
        }

        .divemaster .diver-image {
            animation: divemasterFloat 10s ease-in-out infinite;
        }

        .master-scuba-diver .diver-image {
            animation: masterDive 9s ease-in-out infinite;
        }

        .open-water .diver-image {
            animation: openWaterSwim 7s ease-in-out infinite;
        }

        .rescue-diver .diver-image {
            animation: rescueMove 11s ease-in-out infinite;
        }

        .rescue .diver-image {
            animation: rescueAction 6s ease-in-out infinite;
        }

        .try-diving .diver-image {
            animation: tryDivingFloat 5s ease-in-out infinite;
        }

        /* Gerakan berenang advanced - seperti penyelam berpengalaman */
        @keyframes advancedSwim {
            0%, 100% { 
                transform: translateX(0) translateY(0) rotate(0deg) scale(1); 
            }
            15% { 
                transform: translateX(8px) translateY(-6px) rotate(2deg) scale(1.02); 
            }
            30% { 
                transform: translateX(12px) translateY(-4px) rotate(1deg) scale(1.05); 
            }
            45% { 
                transform: translateX(6px) translateY(2px) rotate(-1deg) scale(1.03); 
            }
            60% { 
                transform: translateX(-4px) translateY(4px) rotate(-2deg) scale(1.01); 
            }
            75% { 
                transform: translateX(-8px) translateY(0) rotate(1deg) scale(1.04); 
            }
            90% { 
                transform: translateX(-3px) translateY(-3px) rotate(0deg) scale(1.02); 
            }
        }

        /* Gerakan mengambang divemaster - tenang dan terkontrol */
        @keyframes divemasterFloat {
            0%, 100% { 
                transform: translateY(0) translateX(0) rotate(0deg) scale(1); 
            }
            20% { 
                transform: translateY(-8px) translateX(3px) rotate(1deg) scale(1.03); 
            }
            40% { 
                transform: translateY(-12px) translateX(6px) rotate(0deg) scale(1.06); 
            }
            60% { 
                transform: translateY(-6px) translateX(2px) rotate(-1deg) scale(1.04); 
            }
            80% { 
                transform: translateY(2px) translateX(-2px) rotate(1deg) scale(1.02); 
            }
        }

        /* Gerakan master diver - sangat halus dan profesional */
        @keyframes masterDive {
            0%, 100% { 
                transform: translateX(0) translateY(0) rotate(0deg) scale(1); 
            }
            12% { 
                transform: translateX(5px) translateY(-8px) rotate(1deg) scale(1.02); 
            }
            25% { 
                transform: translateX(10px) translateY(-10px) rotate(2deg) scale(1.05); 
            }
            38% { 
                transform: translateX(8px) translateY(-5px) rotate(1deg) scale(1.07); 
            }
            50% { 
                transform: translateX(3px) translateY(0) rotate(0deg) scale(1.06); 
            }
            62% { 
                transform: translateX(-2px) translateY(3px) rotate(-1deg) scale(1.04); 
            }
            75% { 
                transform: translateX(-6px) translateY(1px) rotate(-2deg) scale(1.03); 
            }
            88% { 
                transform: translateX(-3px) translateY(-2px) rotate(0deg) scale(1.01); 
            }
        }

        /* Gerakan open water - seperti pemula yang antusias */
        @keyframes openWaterSwim {
            0%, 100% { 
                transform: translateX(0) translateY(0) rotate(0deg) scale(1); 
            }
            20% { 
                transform: translateX(6px) translateY(-4px) rotate(2deg) scale(1.03); 
            }
            40% { 
                transform: translateX(4px) translateY(2px) rotate(-1deg) scale(1.05); 
            }
            60% { 
                transform: translateX(-3px) translateY(5px) rotate(-2deg) scale(1.02); 
            }
            80% { 
                transform: translateX(-5px) translateY(-2px) rotate(1deg) scale(1.04); 
            }
        }

        /* Gerakan rescue diver - siap siaga dan responsif */
        @keyframes rescueMove {
            0%, 100% { 
                transform: translateX(0) translateY(0) rotate(0deg) scale(1); 
            }
            10% { 
                transform: translateX(7px) translateY(-5px) rotate(2deg) scale(1.02); 
            }
            25% { 
                transform: translateX(12px) translateY(-8px) rotate(3deg) scale(1.06); 
            }
            40% { 
                transform: translateX(8px) translateY(-3px) rotate(1deg) scale(1.08); 
            }
            55% { 
                transform: translateX(2px) translateY(2px) rotate(-1deg) scale(1.05); 
            }
            70% { 
                transform: translateX(-4px) translateY(4px) rotate(-2deg) scale(1.03); 
            }
            85% { 
                transform: translateX(-7px) translateY(0) rotate(1deg) scale(1.02); 
            }
        }

        /* Gerakan rescue action - cepat dan tegas */
        @keyframes rescueAction {
            0%, 100% { 
                transform: translateX(0) translateY(0) rotate(0deg) scale(1); 
            }
            25% { 
                transform: translateX(8px) translateY(-6px) rotate(3deg) scale(1.04); 
            }
            50% { 
                transform: translateX(-2px) translateY(4px) rotate(-2deg) scale(1.06); 
            }
            75% { 
                transform: translateX(-6px) translateY(-2px) rotate(2deg) scale(1.03); 
            }
        }

        /* Gerakan try diving - lembut dan hati-hati seperti pemula */
        @keyframes tryDivingFloat {
            0%, 100% { 
                transform: translateY(0) translateX(0) rotate(0deg) scale(1); 
            }
            25% { 
                transform: translateY(-3px) translateX(2px) rotate(1deg) scale(1.01); 
            }
            50% { 
                transform: translateY(-5px) translateX(0) rotate(0deg) scale(1.02); 
            }
            75% { 
                transform: translateY(-2px) translateX(-1px) rotate(-1deg) scale(1.01); 
            }
        }

        /* Tombol kontak bulat */
        .contact-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #0ea5e9, #0369a1);
            border-radius: 50%;
            border: none;
            cursor: pointer;
            color: white;
            font-size: 1.6rem;
            font-weight: bold;
            margin-top: 1.5rem;
            transition: all 0.4s ease;
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
            position: relative;
            overflow: hidden;
        }

        .contact-btn:hover {
            transform: scale(1.15) rotate(180deg);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.6);
            background: linear-gradient(135deg, #0284c7, #0c4a6e);
        }

        .contact-btn:active {
            transform: scale(0.95) rotate(180deg);
        }

        .contact-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .contact-btn:hover::before {
            left: 100%;
        }

        .contact-btn span {
            position: relative;
            z-index: 1;
            transition: transform 0.4s ease;
        }

        .contact-btn:hover span {
            transform: rotate(-180deg);
        }

        /* Booking Form Styles */
        .booking-form-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .booking-form-container.active {
            display: flex;
        }

        .booking-form {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 2.5rem;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.8);
            position: relative;
            transform: scale(0.8);
            opacity: 0;
            transition: all 0.4s ease;
        }

        .booking-form-container.active .booking-form {
            transform: scale(1);
            opacity: 1;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            font-size: 1rem;
            color: #64748b;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            background: white;
        }

        .form-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9, #0369a1);
            color: white;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            color: #475569;
        }

        .close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 35px;
            height: 35px;
            border: none;
            background: #f1f5f9;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: #e2e8f0;
            color: #374151;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .success-message {
            display: none;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            margin-top: 1rem;
            font-weight: 600;
        }

        .error-message {
            display: none;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            margin-top: 1rem;
            font-weight: 600;
        }

        /* Efek gelembung air yang lebih realistis */
        .bubble {
            position: absolute;
            background: radial-gradient(circle, rgba(173, 216, 230, 0.8), rgba(135, 206, 235, 0.4));
            border-radius: 50%;
            animation: bubbleRise 4s linear infinite;
            pointer-events: none;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes bubbleRise {
            0% {
                bottom: -15px;
                opacity: 0.8;
                transform: translateX(0) scale(0.5);
            }
            25% {
                opacity: 1;
                transform: translateX(8px) scale(0.8);
            }
            50% {
                opacity: 0.9;
                transform: translateX(-5px) scale(1);
            }
            75% {
                opacity: 0.6;
                transform: translateX(12px) scale(0.9);
            }
            100% {
                bottom: 120%;
                opacity: 0;
                transform: translateX(-8px) scale(0.3);
            }
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .cards-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .diving-section {
                padding: 1rem;
            }
            
            .section-title {
                font-size: 2.2rem;
                margin-bottom: 2rem;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .diver-card {
                padding: 2rem;
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .section-title {
                font-size: 1.9rem;
            }
            
            .diver-card {
                padding: 1.5rem;
            }
            
            .diver-name {
                font-size: 1.2rem;
            }
        }
    </style>
  <style>@view-transition { navigation: auto; }</style>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
  <script src="https://cdn.tailwindcss.com" type="text/javascript"></script>
 </head>
 <body>
  <main class="diving-section">
   <h1 class="section-title" id="sectionTitle">PADI Professional Certifications</h1><!-- Discover Scuba Diving Section -->
   <section class="course-section">
    <div class="section-header">
     <h2 class="section-heading">Discover Scuba Diving</h2>
     <p class="section-description">Learn the basics of scuba diving, and then dive with an instructor in best dive site of Bali. Try your new skills with confidence on an experience that counts as credit towards a PADI Open Water Diver course.</p>
    </div>
    <div class="cards-container">
     <article class="diver-card try-diving">
      <div class="level-badge" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);">
       Discover
      </div>
      <div class="diver-image-container"><img class="diver-image" data-src="https://www.balidiving.com/images/icons/diver/try.png" alt="PADI Try Diving - First diving experience" onerror="this.src=''; this.alt='Certification image could not be loaded'; this.style.display='none';" style="background: transparent;">
      </div>
      <h3 class="diver-name" id="padiTryDivingName">Discover Scuba Diving</h3>
      <div class="depth-info">
       Max Depth: 12 Metres
      </div><button class="contact-btn" onclick="openBookingForm('Discover Scuba Diving')" aria-label="Book Discover Scuba Diving course"> <span>+</span> </button>
     </article>
    </div>
   </section><!-- Courses for Beginners Section -->
   <section class="course-section">
    <div class="section-header">
     <h2 class="section-heading">Courses for Beginners</h2>
     <p class="section-description">Get your diving certificate with this Professional Association of Diving Instructors (PADI) open water course, the most popular scuba course in the world, while diving in the Coral Triangle, an area with the highest biodiversity of marine species.</p>
    </div>
    <div class="cards-container">
     <article class="diver-card open-water">
      <div class="level-badge">
       Beginner
      </div>
      <div class="diver-image-container"><img class="diver-image" data-src="https://www.balidiving.com/images/icons/diver/padi-open-water-2.png" alt="PADI Open Water - Basic diver certification" onerror="this.src=''; this.alt='Certification image could not be loaded'; this.style.display='none';">
      </div>
      <h3 class="diver-name" id="padiOpenWaterName">PADI Open Water Diver</h3>
      <div class="depth-info">
       Max Depth: 18 Metres
      </div><button class="contact-btn" onclick="openBookingForm('PADI Open Water Diver')" aria-label="Book PADI Open Water course"> <span>+</span> </button>
     </article>
    </div>
   </section><!-- Advance & Specialty Section -->
   <section class="course-section">
    <div class="section-header">
     <h2 class="section-heading">Advanced &amp; Specialty Courses</h2>
     <p class="section-description">Enhance your scuba diving skills and dive deeper with advanced PADI certifications. A wide range of PADI courses including all specialty courses help you increase your confidence and build your scuba skills so you can become more comfortable underwater.</p>
    </div>
    <div class="cards-container">
     <article class="diver-card advanced-open-water">
      <div class="level-badge">
       Advanced
      </div>
      <div class="diver-image-container"><img class="diver-image" data-src="https://www.balidiving.com/images/icons/diver/padi-advanced-open-water-2.png" alt="PADI Advanced Open Water - Advanced level diver certification" onerror="this.src=''; this.alt='Certification image could not be loaded'; this.style.display='none';">
      </div>
      <h3 class="diver-name" id="padiAdvancedOpenWaterName">PADI Advanced Open Water Diver</h3>
      <div class="depth-info">
       Max Depth: 30 Metres
      </div><button class="contact-btn" onclick="openBookingForm('PADI Advanced Open Water Diver')" aria-label="Book PADI Advanced Open Water course"> <span>+</span> </button>
     </article>
     <article class="diver-card rescue">
      <div class="level-badge">
       Specialty
      </div>
      <div class="diver-image-container"><img class="diver-image" data-src="https://www.balidiving.com/images/icons/diver/padi-rescue.png" alt="PADI Rescue - Rescue techniques certification" onerror="this.src=''; this.alt='Certification image could not be loaded'; this.style.display='none';">
      </div>
      <h3 class="diver-name" id="padiRescueName">PADI Emergency First Response</h3>
      <div class="depth-info">
       Max Depth: 30 Metres
      </div><button class="contact-btn" onclick="openBookingForm('PADI Emergency First Response')" aria-label="Book PADI Emergency First Response course"> <span>+</span> </button>
     </article>
     <article class="diver-card rescue-diver">
      <div class="level-badge">
       Expert
      </div>
      <div class="diver-image-container"><img class="diver-image" data-src="https://www.balidiving.com/images/icons/diver/padi-rescue-diver.png" alt="PADI Rescue Diver - Rescue diver certification" onerror="this.src=''; this.alt='Certification image could not be loaded'; this.style.display='none';">
      </div>
      <h3 class="diver-name" id="padiRescueDiverName">PADI Rescue Diver</h3>
      <div class="depth-info">
       Max Depth: 40 Metres
      </div><button class="contact-btn" onclick="openBookingForm('PADI Rescue Diver')" aria-label="Book PADI Rescue Diver course"> <span>+</span> </button>
     </article>
    </div>
   </section><!-- Professional Level Section -->
   <section class="course-section">
    <div class="section-header">
     <h2 class="section-heading">Professional Level Courses</h2>
     <p class="section-description">Start your career as a dive professional and live your passion. Become a PADI professional with our comprehensive training programs. Working closely with experienced instructors, you'll expand your dive knowledge and hone your skills to the professional level.</p>
    </div>
    <div class="cards-container">
     <article class="diver-card divemaster">
      <div class="level-badge professional">
       Professional
      </div>
      <div class="diver-image-container"><img class="diver-image" data-src="https://www.balidiving.com/images/icons/diver/padi-divemaste-2.png" alt="PADI Divemaster - Professional dive guide certification" onerror="this.src=''; this.alt='Certification image could not be loaded'; this.style.display='none';">
      </div>
      <h3 class="diver-name" id="padiDivemasteName">PADI Divemaster</h3>
      <div class="depth-info">
       Max Depth: 40 Metres
      </div><button class="contact-btn" onclick="openBookingForm('PADI Divemaster')" aria-label="Book PADI Divemaster course"> <span>+</span> </button>
     </article>
    </div>
   </section>
  </main><!-- Course Booking Form Modal -->
  <div class="booking-form-container" id="bookingFormContainer">
   <form class="booking-form" id="bookingForm"><button type="button" class="close-btn" onclick="closeBookingForm()" aria-label="Close booking form">×</button>
    <div class="form-header">
     <h2 class="form-title" id="formTitle">Book Your Course</h2>
     <p class="form-subtitle" id="formSubtitle">Fill in your details to reserve your spot</p>
    </div>
    <div class="form-group"><label for="courseDate" class="form-label">Course Date *</label> <input type="date" id="courseDate" name="courseDate" class="form-input" required>
    </div>
    <div class="form-group"><label for="fullName" class="form-label">Full Name *</label> <input type="text" id="fullName" name="fullName" class="form-input" placeholder="Enter your full name" required>
    </div>
    <div class="form-group"><label for="email" class="form-label">Email *</label> <input type="email" id="email" name="email" class="form-input" placeholder="example@email.com" required>
    </div>
    <div class="form-group"><label for="phone" class="form-label">Phone Number *</label> <input type="tel" id="phone" name="phone" class="form-input" placeholder="+62 812 3456 7890" required>
    </div>
    <div class="form-buttons"><button type="button" class="btn btn-secondary" onclick="closeBookingForm()">Cancel</button> <button type="submit" class="btn btn-primary" id="submitBtn">
      <div class="loading-spinner" id="loadingSpinner"></div><span id="submitText">Send Booking</span> </button>
    </div>
    <div class="success-message" id="successMessage">
     Booking sent successfully! We will contact you soon.
    </div>
    <div class="error-message" id="errorMessage">
     An error occurred while sending booking. Please try again.
    </div>
   </form>
  </div>
  <script>
        // Default configuration
        const defaultConfig = {
            section_title: "PADI Professional Certifications",
            padi_try_diving_name: "Discover Scuba Diving",
            padi_advanced_open_water_name: "PADI Advanced Open Water Diver",
            padi_divemaste_name: "PADI Divemaster",
            padi_master_scuba_diver_name: "PADI Master Scuba Diver",
            padi_open_water_name: "PADI Open Water Diver",
            padi_rescue_diver_name: "PADI Rescue Diver",
            padi_rescue_name: "PADI Emergency First Response"
        };

        // Render function to update UI
        async function render(config) {
            document.getElementById('sectionTitle').textContent = config.section_title || defaultConfig.section_title;
            document.getElementById('padiTryDivingName').textContent = config.padi_try_diving_name || defaultConfig.padi_try_diving_name;
            document.getElementById('padiAdvancedOpenWaterName').textContent = config.padi_advanced_open_water_name || defaultConfig.padi_advanced_open_water_name;
            document.getElementById('padiDivemasteName').textContent = config.padi_divemaste_name || defaultConfig.padi_divemaste_name;
            document.getElementById('padiOpenWaterName').textContent = config.padi_open_water_name || defaultConfig.padi_open_water_name;
            document.getElementById('padiRescueDiverName').textContent = config.padi_rescue_diver_name || defaultConfig.padi_rescue_diver_name;
            document.getElementById('padiRescueName').textContent = config.padi_rescue_name || defaultConfig.padi_rescue_name;
        }

        // Fungsi untuk capabilities
        function mapToCapabilities(config) {
            return {
                recolorables: [],
                borderables: [],
                fontEditable: undefined,
                fontSizeable: undefined
            };
        }

        // Fungsi untuk edit panel values
        function mapToEditPanelValues(config) {
            return new Map([
                ["section_title", config.section_title || defaultConfig.section_title],
                ["padi_try_diving_name", config.padi_try_diving_name || defaultConfig.padi_try_diving_name],
                ["padi_advanced_open_water_name", config.padi_advanced_open_water_name || defaultConfig.padi_advanced_open_water_name],
                ["padi_divemaste_name", config.padi_divemaste_name || defaultConfig.padi_divemaste_name],
                ["padi_master_scuba_diver_name", config.padi_master_scuba_diver_name || defaultConfig.padi_master_scuba_diver_name],
                ["padi_open_water_name", config.padi_open_water_name || defaultConfig.padi_open_water_name],
                ["padi_rescue_diver_name", config.padi_rescue_diver_name || defaultConfig.padi_rescue_diver_name],
                ["padi_rescue_name", config.padi_rescue_name || defaultConfig.padi_rescue_name]
            ]);
        }

        // Fungsi untuk memuat gambar setelah page load
        function lazyLoadImages() {
            const images = document.querySelectorAll('.diver-image[data-src]');
            
            images.forEach(img => {
                const src = img.getAttribute('data-src');
                const newImg = new Image();
                
                newImg.onload = function() {
                    img.src = src;
                    img.classList.add('loaded');
                    img.removeAttribute('data-src');
                };
                
                newImg.onerror = function() {
                    img.style.display = 'none';
                    img.alt = 'Certification image could not be loaded';
                };
                
                newImg.src = src;
            });
        }

        // Fungsi untuk membuat efek gelembung yang lebih realistis
        function createBubbles() {
            const cards = document.querySelectorAll('.diver-card');
            
            cards.forEach(card => {
                setInterval(() => {
                    if (Math.random() > 0.6) {
                        const bubble = document.createElement('div');
                        bubble.className = 'bubble';
                        bubble.style.left = (Math.random() * 80 + 10) + '%';
                        bubble.style.width = bubble.style.height = (Math.random() * 12 + 6) + 'px';
                        bubble.style.animationDelay = Math.random() * 3 + 's';
                        bubble.style.animationDuration = (Math.random() * 2 + 3) + 's';
                        
                        card.appendChild(bubble);
                        
                        setTimeout(() => {
                            if (bubble.parentNode) {
                                bubble.parentNode.removeChild(bubble);
                            }
                        }, 5000);
                    }
                }, 1500);
            });
        }

        // Booking form functionality
        let selectedCourse = '';

        function openBookingForm(courseName) {
            selectedCourse = courseName;
            document.getElementById('formTitle').textContent = `Book ${courseName}`;
            document.getElementById('formSubtitle').textContent = `Fill in your details to reserve your ${courseName} course`;
            
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('courseDate').min = today;
            
            // Show form with animation
            const container = document.getElementById('bookingFormContainer');
            container.classList.add('active');
            
            // Focus on first input
            setTimeout(() => {
                document.getElementById('courseDate').focus();
            }, 400);
        }

        function closeBookingForm() {
            const container = document.getElementById('bookingFormContainer');
            container.classList.remove('active');
            
            // Reset form after animation
            setTimeout(() => {
                document.getElementById('bookingForm').reset();
                hideMessages();
            }, 400);
        }

        function hideMessages() {
            document.getElementById('successMessage').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
        }

        function showLoading(show) {
            const spinner = document.getElementById('loadingSpinner');
            const submitText = document.getElementById('submitText');
            const submitBtn = document.getElementById('submitBtn');
            
            if (show) {
                spinner.style.display = 'block';
                submitText.textContent = 'Sending...';
                submitBtn.disabled = true;
            } else {
                spinner.style.display = 'none';
                submitText.textContent = 'Send Booking';
                submitBtn.disabled = false;
            }
        }

        async function sendEmail(formData) {
            // Simulate email sending with EmailJS or similar service
            // In a real implementation, you would use a service like EmailJS
            
            const emailData = {
                from_email: 'no-reply@balidiving.com',
                to_email: 'admin@balidiving.com',
                cc_email: 'subhi@balidiving.com',
                bcc_email: 'webmaster@balidiving.com',
                subject: `New Course Booking: ${selectedCourse}`,
                message: `
                    New course booking received:
                    
                    Course: ${selectedCourse}
                    Date: ${formData.courseDate}
                    Name: ${formData.fullName}
                    Email: ${formData.email}
                    Phone: ${formData.phone}
                    
                    Please contact the customer to confirm the booking.
                `
            };
            
            // Simulate API call delay
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            // Simulate success (in real implementation, this would be an actual API call)
            return { success: true };
        }

        // Handle form submission
        document.getElementById('bookingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            hideMessages();
            showLoading(true);
            
            try {
                const formData = {
                    courseDate: document.getElementById('courseDate').value,
                    fullName: document.getElementById('fullName').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value
                };
                
                // Validate form data
                if (!formData.courseDate || !formData.fullName || !formData.email || !formData.phone) {
                    throw new Error('Please fill in all required fields');
                }
                
                // Send email
                const result = await sendEmail(formData);
                
                if (result.success) {
                    document.getElementById('successMessage').style.display = 'block';
                    
                    // Auto close form after success
                    setTimeout(() => {
                        closeBookingForm();
                    }, 3000);
                } else {
                    throw new Error('Failed to send booking');
                }
                
            } catch (error) {
                console.error('Booking error:', error);
                document.getElementById('errorMessage').style.display = 'block';
            } finally {
                showLoading(false);
            }
        });

        // Close form when clicking outside
        document.getElementById('bookingFormContainer').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingForm();
            }
        });

        // Close form with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('bookingFormContainer').classList.contains('active')) {
                closeBookingForm();
            }
        });

        // Inisialisasi setelah DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                lazyLoadImages();
                createBubbles();
            }, 100);
        });

        // Inisialisasi Element SDK
        if (window.elementSdk) {
            window.elementSdk.init({
                defaultConfig,
                render,
                mapToCapabilities,
                mapToEditPanelValues
            });
        }
        async function sendEmail(formData) {
    const response = await fetch('send_booking.php', {
        method: 'POST',
        body: new URLSearchParams({
            courseName: selectedCourse,
            courseDate: formData.courseDate,
            fullName: formData.fullName,
            email: formData.email,
            phone: formData.phone
        })
    });
    const result = await response.text();
    return { success: result.trim() === 'success' };
}

    </script>
 <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98fe8fb241613e44',t:'MTc2MDY5MTE2Mi4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>