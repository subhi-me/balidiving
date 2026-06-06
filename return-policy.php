<?php
$page = 'return-policy';
include('01-start.php');
?>
<div style="height:100px;"></div>

<style>
/* PDF-like document style (uniform with other legal pages) */
.pdf-page{
  background:#fff;
  max-width: 820px;
  margin: 0 auto 80px;
  padding: 60px;
  box-shadow: 0 0 0 1px #e5e5e5, 0 20px 40px rgba(0,0,0,.08);
  font-family: "Times New Roman", Georgia, serif;
  color:#111;
}
.pdf-page h1{font-size:28px;margin-bottom:6px;}
.pdf-page h2{font-size:20px;margin-top:32px;margin-bottom:10px;}
.pdf-page p{font-size:15px;line-height:1.8;margin:8px 0;text-align:justify;}
.pdf-page ul{margin:10px 0 10px 22px;}
.pdf-page li{font-size:15px;line-height:1.7;margin-bottom:6px;}
.pdf-meta{font-size:13px;color:#555;margin-bottom:30px;}

@media print{
  body{background:#fff;}
  .pdf-page{box-shadow:none;margin:0;padding:40px;}
}
</style>

<section class="pdf-page">

  <h1>Return, Rental & Digital Goods Policy</h1>
  <div class="pdf-meta">Effective Date: January 2025</div>

  <h2>1. General Policy</h2>
  <p>
    This policy applies to all products, services, and digital materials offered by Bali Diving,
    including physical goods, rental equipment (Bali only), downloadable content, e-books,
    training videos, and third-party affiliated materials such as PADI learning products.
  </p>
  <p>
    All sales are final. Bali Diving does not provide refunds, returns, cancellations,
    or exchanges for any purchase, except where explicitly required under applicable Indonesian law.
    For customers outside Indonesia, no return or refund rights are granted unless mandated by their
    local jurisdiction and only to the extent that such laws apply.
  </p>
  <p>
    By purchasing or renting from Bali Diving, you agree to these terms in full.
  </p>

  <h2>2. Physical Products (For Sale)</h2>
  <p>
    All physical products are inspected prior to shipment to ensure they are complete,
    functional, and in acceptable condition. Bali Diving currently ships physical products
    only within Indonesia. International shipping is not available.
  </p>
  <p>
    Due to the nature of our inspection and verification process, all sales are final and
    cannot be returned or refunded unless the item arrives in a materially damaged,
    non-functional, or incorrect condition directly caused by our handling.
  </p>
  <p>
    In such eligible cases, the customer must notify Bali Diving within
    <strong>48 hours</strong> of delivery and provide clear photographic or video evidence.
    The item must be returned to our Bali office for verification before any resolution
    (repair or replacement) is considered.
  </p>
  <p>
    Bali Diving reserves the right to reject claims submitted after 48 hours or those lacking sufficient evidence.
  </p>

  <h2>3. Rental Equipment (Bali Only)</h2>
  <p>
    Rental equipment is inspected thoroughly before release to ensure it is safe, complete,
    and fully operational. By renting equipment, the customer accepts full responsibility
    for its use, care, and timely return.
  </p>
  <p>
    All rental items must be returned to the Bali Diving office in the same condition as received,
    excluding normal wear through proper use.
  </p>
  <p>
    Any loss, theft, damage, or missing parts will be charged at full repair or replacement cost.
    The customer agrees to these charges upon renting the equipment.
  </p>
  <p>
    Rental fees are strictly non-refundable once the equipment has been released to the customer.
  </p>

  <h2>4. Digital Goods & Downloadable Materials</h2>
  <p>
    Digital products—including e-books, instruction manuals, online videos, certification-related
    training materials, and third-party affiliated content (such as PADI educational materials)—
    are delivered instantly or through digital access links.
  </p>
  <p>
    Due to the non-returnable nature of digital goods, <strong>all digital purchases are final and non-refundable</strong>,
    regardless of download status, access issues caused by the customer's device or internet,
    or changes in the customer's circumstances.
  </p>
  <p>
    Customers are responsible for ensuring compatibility of their device, browser, or software
    before purchasing or accessing digital materials.
  </p>
  <p>
    Any third-party materials (e.g., PADI) remain governed by the terms and licensing restrictions
    of the respective provider. Bali Diving is not responsible for access limitations, expiration,
    or content changes imposed by third-party platforms.
  </p>

  <h2>5. Non-Returnable & Non-Refundable Conditions</h2>
  <p>This policy strictly confirms that:</p>
  <ul>
    <li>All sales—physical or digital—are final.</li>
    <li>No returns or refunds are provided for any product shipped outside Indonesia.</li>
    <li>Digital products cannot be cancelled, refunded, or exchanged under any circumstances.</li>
    <li>Rental fees are non-refundable once equipment is collected.</li>
    <li>Claims for damaged physical goods are limited to Indonesia and must follow the verification procedure.</li>
  </ul>

  <h2>6. Limitation of Liability</h2>
  <p>
    Bali Diving is not liable for any indirect, incidental, or consequential damages arising from
    the use of its products, rental equipment, or digital materials. All products and services are
    provided "as is" and "as available", subject to standard safety practices applicable to scuba diving
    and water activities.
  </p>
  <p>
    Customers are responsible for following all safety instructions, certification requirements,
    and usage guidelines associated with any diving, snorkeling, or training activity.
  </p>

  <h2>7. Contact Information</h2>
  <p>
    For questions regarding this policy or assistance with an order, please contact:<br>
    <strong>Bali Diving</strong><br>
    Email: <a href="mailto:customer.service@balidiving.com">customer.service@balidiving.com</a><br>
    Office (Bali): Jl. Bypass Ngurah Rai 46E, Sanur, Bali – Indonesia
  </p>

  <p style="margin-top: 40px; color: #555;">
    By purchasing, renting, or accessing any product or service from Bali Diving, you acknowledge
    that you have read, understood, and agreed to all terms stated in this Return, Rental & Digital Goods Policy.
  </p>

</section>
<?php include('template/consent.php');?>
<?php include('03-end.php')?>
