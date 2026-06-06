<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Master Dashboard</title>

<!-- Anti-SEO / Non-indexing -->
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
<meta name="googlebot" content="noindex, nofollow">
<meta name="bingbot" content="noindex, nofollow">
<meta name="referrer" content="no-referrer">
<meta name="author" content="Subhi.me Universe">
<meta name="description" content="Private internal dashboard for Subhi.me.">

<style>
  *{box-sizing:border-box;}
  html,body{
    margin:0; padding:0;
    font-family: 'Poppins', 'Inter', sans-serif;
    background: radial-gradient(circle at 30% 10%, #0f172a 0%, #020617 100%);
    color:#e2e8f0;
    display:flex;
    flex-direction:column;
    align-items:center;
    min-height:100vh;
  }
  header{
    text-align:center; padding:60px 20px 30px;
  }
  h1{
    font-size:2.6rem;
    background: linear-gradient(90deg,#38bdf8,#a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight:700;
    letter-spacing:0.8px;
    margin:0;
  }
  .grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
    gap:28px;
    width:100%; max-width:1100px;
    padding:40px 25px 100px;
  }
  .card{
    border-radius:22px;
    overflow:hidden;
    background:#1e293b;
    box-shadow:0 0 0 1px rgba(255,255,255,0.05), 0 8px 25px rgba(0,0,0,0.4);
    transition:all 0.3s ease;
  }
  .card:hover{
    transform:translateY(-6px) scale(1.03);
    box-shadow:0 10px 30px rgba(255,255,255,0.15);
  }
  .card a{
    display:block;
    color:inherit;
    text-decoration:none;
    height:100%;
  }
  .thumb{
    height:180px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:36px;
    font-weight:700;
    color:#0f172a;
  }
  .card-body{
    padding:22px 24px 26px;
    background:#0f172a;
    border-top:1px solid rgba(255,255,255,0.05);
  }
  .title{
    font-size:20px;
    font-weight:600;
    line-height:1.4;
    color:#e2e8f0;
  }
  .title:hover{
    color:#38bdf8;
  }
  footer{
    text-align:center;
    padding:30px;
    font-size:13px;
    color:#64748b;
    border-top:1px solid rgba(255,255,255,0.05);
    width:100%;
  }
</style>
</head>
<body>
  <header>
    <h1>Master Dashboard</h1>
  </header>
  <div class="grid">    <div class="card">
      <a href="booking-link.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">B</div>
        <div class="card-body">
          <div class="title">BaliDiving.com | Book Your Dive Experience</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="booking.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">B</div>
        <div class="card-body">
          <div class="title">BaliDiving.com | Book Your Dive Experience</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="book-now.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">O</div>
        <div class="card-body">
          <div class="title">Order Confirmation - BaliDiving.com</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="payment-gateway.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">P</div>
        <div class="card-body">
          <div class="title">Payment Gateway </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="dashboard.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">S</div>
        <div class="card-body">
          <div class="title">Scuba Diving &amp; Snorkeling Kanban Board</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="step1-mail.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">S</div>
        <div class="card-body">
          <div class="title">Step1 Mail </div>
        </div>
      </a>
    </div>  </div>
  <footer>© Subhi.me Universe — Private Access Only</footer>
</body>
</html>