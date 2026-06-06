<script>
(function(){
  function $(sel){ return document.querySelector(sel); }
  function getNumFromMoney(s){
    if(!s) return 0;
    const n = (s+'').replace(/[^\d.]/g,'');
    return parseFloat(n||'0')||0;
  }
  function text(elId){ const el=document.getElementById(elId); return el?el.textContent.trim():''; }
  function val(elId){ const el=document.getElementById(elId); return el?el.value.trim():''; }

  function attachBookingHook(){
    const btn = document.getElementById('confirmBooking');
    if(!btn) return;
    if(btn._bdHookAttached) return;
    btn._bdHookAttached = true;

    btn.addEventListener('click', async function(){
      try{
        const dive_site       = text('offcanvasTitle') || 'Dive Trip';
        const booking_date    = val('bookingDate');
        const participants    = parseInt(val('participants')||'1',10) || 1;
        const name            = val('fullName');
        const email           = val('email');
        const phone           = val('phone');
        const note            = val('note');

        const price_per_person= getNumFromMoney(text('pricePerPerson'));
        const total_amount    = getNumFromMoney(text('totalAmount'));

        const couponCodeEl    = document.getElementById('couponCode');
        const couponRow       = document.getElementById('couponDiscountRow');
        const couponValEl     = document.getElementById('couponDiscount');

        const coupon_code     = couponCodeEl ? (couponCodeEl.value||'') : '';
        const coupon_value    = (couponRow && couponRow.style.display !== 'none')
                                  ? getNumFromMoney(couponValEl ? couponValEl.textContent : '0')
                                  : 0;

        // Biarkan validasi UI asli bekerja; kalau field wajib kosong, jangan post
        if(!booking_date || !name || !email || !phone){ return; }

        const payload = {
          dive_site,
          booking_date,
          participants,
          name, email, phone, note,
          coupon_code, coupon_value,
          price_per_person, total_amount
        };

        // Kirim ke endpoint (silent, tidak mengganggu UX)
        fetch('/crm/lead_gateway.php', {
          method: 'POST',
          headers: { 'Content-Type':'application/json; charset=UTF-8' },
          body: JSON.stringify(payload),
          cache: 'no-store',
          keepalive: true
        }).catch(()=>{ /* silent fail */ });

      }catch(e){
        // silent fail, jangan ganggu UI existing
      }
    });
  }

  if(document.readyState==='loading'){
    document.addEventListener('DOMContentLoaded', attachBookingHook);
  }else{
    attachBookingHook();
  }

  // Re-attach kalau tombol/DOM muncul belakangan (offcanvas dibuka)
  document.addEventListener('click', function(e){
    const t = e.target;
    if(t && (t.id==='bookNowBtn' || (t.closest && t.closest('#bookNowBtn')))) {
      setTimeout(attachBookingHook, 200);
    }
  });
})();
</script>
