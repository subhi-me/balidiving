
  <style>
    /* Namespace all variables and rules under .faq-container */
    .faq-container {
      --primary-color: #0099cc;
      --card-bg: #fff;
      --page-bg: #f5f7fa;
      --text-color: #333;
      --indent: 30px;

      font-family: 'Segoe UI', sans-serif;
      background: var(--page-bg);
      color: var(--text-color);
      padding: 40px 20px;
    }
    .faq-container h1 {
      margin: 0 0 30px var(--indent);
      font-size: 2.5rem;
      color: var(--primary-color);
    }
    .faq-container .faq-item {
      background: var(--card-bg);
      border-left: 4px solid var(--primary-color);
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      margin: 0 0 20px var(--indent);
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
      position: relative;
    }
    .faq-container .faq-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .faq-container .faq-question {
      padding: 18px 24px;
      cursor: pointer;
      font-weight: 600;
      position: relative;
    }
    .faq-container .faq-question::after {
      content: '+';
      position: absolute;
      right: 24px;
      font-size: 1.5rem;
      color: var(--primary-color);
      transition: transform 0.2s;
    }
    .faq-container .faq-item.active .faq-question::after {
      transform: rotate(45deg);
    }
    .faq-container .faq-answer {
      max-height: 0;
      padding: 0 24px;
      margin-left: var(--indent);
      overflow: hidden;
      transition: max-height 0.4s ease, padding 0.4s ease;
    }
    .faq-container .faq-item.active .faq-answer {
      max-height: 800px;
      padding: 18px 24px;
    }
    .faq-container .typewriter {
      display: inline-block;
      white-space: normal;
      word-wrap: break-word;
      border-right: 2px solid var(--primary-color);
      line-height: 1.5;
    }
  </style>

  <div class="faq-container">
    <h2>FAQ</h2>

    <div class="faq-item">
      <div class="faq-question">What’s the difference between a Discover Scuba Diving experience and the Open Water Diver course?</div>
      <div class="faq-answer">
        <span class="typewriter"
              data-text="Try Diving/Discover Scuba Diving is a one-day intro—perfect if you’re curious but not ready to commit. The Open Water course spans 3–4 days, includes theory, pool practice, and 4 ocean dives, and earns you a globally recognized PADI certification."></span>
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">How do I book a dive trip with Bali Diving?</div>
      <div class="faq-answer">
        <span class="typewriter"
              data-text="Booking is simple: visit our website, choose your program and dates, then pay a deposit. We’ll follow up with a confirmation email, gear checklist, and meetup details. For group rates or special requests, just email us!"></span>
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">What’s included in Bali Diving’s scuba package?</div>
      <div class="faq-answer">
        <span class="typewriter"
              data-text="Every package includes PADI-certified instructors, all scuba gear (BCD, regulator, wetsuit), boat transfers, snacks, drinks, and dive insurance. Just bring your swimsuit and sense of adventure!"></span>
      </div>
    </div>
<a href="https://booking.balidiving.com/faqs-whatsapp/">More..</a>
    <!-- add more items as needed... -->

  </div>

  <script>
    document.querySelectorAll('.faq-container .faq-question').forEach(q => {
      q.addEventListener('click', () => {
        const item = q.parentElement;
        const writer = item.querySelector('.typewriter');
        // close others
        document.querySelectorAll('.faq-container .faq-item').forEach(f => {
          if (f !== item) {
            f.classList.remove('active');
            const tw = f.querySelector('.typewriter');
            tw.textContent = '';
            tw.style.borderRight = '2px solid var(--primary-color)';
          }
        });
        // toggle this one
        if (!item.classList.contains('active')) {
          item.classList.add('active');
          typeWriterByWord(writer);
        } else {
          item.classList.remove('active');
          writer.textContent = '';
        }
      });
    });

    function typeWriterByWord(el) {
      const words = el.getAttribute('data-text').split(' ');
      el.textContent = '';
      let i = 0;
      const speed = 300; // ms per word
      (function type() {
        if (i < words.length) {
          el.textContent += (i ? ' ' : '') + words[i++];
          setTimeout(type, speed);
        } else {
          el.style.borderRight = 'none';
        }
      })();
    }
  </script>
