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
      <a href="01-start.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">0</div>
        <div class="card-body">
          <div class="title">01 Start </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="02-content.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">0</div>
        <div class="card-body">
          <div class="title">02 Content </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="03-end.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">0</div>
        <div class="card-body">
          <div class="title">03 End </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="404.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">4</div>
        <div class="card-body">
          <div class="title">404 </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="a.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">A</div>
        <div class="card-body">
          <div class="title">A </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="about.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">A</div>
        <div class="card-body">
          <div class="title">About </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="about-us.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffd6e0">A</div>
        <div class="card-body">
          <div class="title">About Us </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="advanced-open-water.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb6c1">A</div>
        <div class="card-body">
          <div class="title">Advanced Open Water | Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="cl-advance-balidiving.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#c8e6c9">A</div>
        <div class="card-body">
          <div class="title">Advances your diving knowledge &amp; skills in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="padi-Learn-Advance.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#a5d8ff">B</div>
        <div class="card-body">
          <div class="title">Bali Diving - Diving Courses - Advanced</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="stepback.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#f8c291">B</div>
        <div class="card-body">
          <div class="title">Bali Diving - General</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="pricelist.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#dfe6e9">B</div>
        <div class="card-body">
          <div class="title">Bali Diving - Price List</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="public-insurance.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">B</div>
        <div class="card-body">
          <div class="title">Bali Diving - Public Insurance</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="video.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">B</div>
        <div class="card-body">
          <div class="title">Bali Diving - Scuba Diving Adventures in Bali | Best Dive Sites</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="download-center.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">B</div>
        <div class="card-body">
          <div class="title">Bali Diving Download Center - all  you need</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="food-menu.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">B</div>
        <div class="card-body">
          <div class="title">Bali Diving Food Menu</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="menu.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">B</div>
        <div class="card-body">
          <div class="title">Bali Diving Food Menu</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="login.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">B</div>
        <div class="card-body">
          <div class="title">Bali Diving Member - Login</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="privacy_policy.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffd6e0">B</div>
        <div class="card-body">
          <div class="title">Bali Diving Privacy Policy</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="bali-diving-assurance.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb6c1">B</div>
        <div class="card-body">
          <div class="title">Bali Diving Public Insurance</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="scuba-diving-bali-review.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#c8e6c9">B</div>
        <div class="card-body">
          <div class="title">Bali Diving Review</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="youtube.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#a5d8ff">B</div>
        <div class="card-body">
          <div class="title">Bali Diving Youtube Channel</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="register.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#f8c291">B</div>
        <div class="card-body">
          <div class="title">Bali Diving – Quick Registration</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="pricelist-packages.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#dfe6e9">B</div>
        <div class="card-body">
          <div class="title">Bali Diving — General Pricelist</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="prices.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">B</div>
        <div class="card-body">
          <div class="title">Bali Diving — Price List</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="dan-insurance.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">B</div>
        <div class="card-body">
          <div class="title">Bali Diving- International Insurance</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="Landingpage-go-scuba-diving-in-bali.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">B</div>
        <div class="card-body">
          <div class="title">Bali Diving-Go Scuba Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="Landingpage-go-scuba-diving-in-bali.html(1).html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">B</div>
        <div class="card-body">
          <div class="title">Bali Diving-Go Scuba Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="Landingpage-go-scuba-diving-in-bali.html.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">B</div>
        <div class="card-body">
          <div class="title">Bali Diving-Go Scuba Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="blog-bali-recommendations.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">B</div>
        <div class="card-body">
          <div class="title">Bali Reccomendations</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="bio.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffd6e0">B</div>
        <div class="card-body">
          <div class="title">Bali's Most Famous Dive Centre | PADI 5 Star Dive Centre Sanur</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="blog.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb6c1">B</div>
        <div class="card-body">
          <div class="title">Blog </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="blogs.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#c8e6c9">B</div>
        <div class="card-body">
          <div class="title">Blogs </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="cancelation-policy.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#a5d8ff">C</div>
        <div class="card-body">
          <div class="title">Cancelation Policy</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="checkrate.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#f8c291">C</div>
        <div class="card-body">
          <div class="title">Check Realtime Rate</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="conservation.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#dfe6e9">C</div>
        <div class="card-body">
          <div class="title">Conservation | Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="contact.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">C</div>
        <div class="card-body">
          <div class="title">Contact </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="cookies-policy.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">C</div>
        <div class="card-body">
          <div class="title">Cookies Policy of Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="courses-freediving.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">C</div>
        <div class="card-body">
          <div class="title">Courses Freediving | Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="courses-photography.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">C</div>
        <div class="card-body">
          <div class="title">Courses Photography | Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="cron_pricelist_digest.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">C</div>
        <div class="card-body">
          <div class="title">Cron Pricelist Digest </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="day trip.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">D</div>
        <div class="card-body">
          <div class="title">Day Trip </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="disclaimer.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffd6e0">D</div>
        <div class="card-body">
          <div class="title">Disclaimer of Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="discover-scuba-diving-in-bali.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb6c1">D</div>
        <div class="card-body">
          <div class="title">Discover Scuba Diving Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="padi-Learn-divemaster.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#c8e6c9">D</div>
        <div class="card-body">
          <div class="title">Diving Courses Dive Master in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="paadi-Learn-beginner.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#a5d8ff">D</div>
        <div class="card-body">
          <div class="title">Diving courses in Bali - basic (for beginners)</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="padi-Learn-beginners.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#f8c291">D</div>
        <div class="card-body">
          <div class="title">Diving courses in Bali - basic (for beginners)</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="padi-Learn-specialties.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#dfe6e9">D</div>
        <div class="card-body">
          <div class="title">Diving Courses Specialties in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="Landingpage-diving-safaris.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">D</div>
        <div class="card-body">
          <div class="title">Diving Safaris - Multi Day Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="diving-safaris.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">D</div>
        <div class="card-body">
          <div class="title">Diving Safaris - Multi Day Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="emailconfirm.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">E</div>
        <div class="card-body">
          <div class="title">Email Confirmed</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="mycart.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">F</div>
        <div class="card-body">
          <div class="title">Floating Booking Plan</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="fundiving.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">F</div>
        <div class="card-body">
          <div class="title">Fun Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="Landingpage-fundiving.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">F</div>
        <div class="card-body">
          <div class="title">Fun Diving in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="go-diving.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffd6e0">F</div>
        <div class="card-body">
          <div class="title">Fun Diving in Bali · Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="go-diving-edit.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb6c1">F</div>
        <div class="card-body">
          <div class="title">Fun Diving Page Settings</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="gallery.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#c8e6c9">G</div>
        <div class="card-body">
          <div class="title">Gallery | Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="googlea2acee522f385bf3.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#a5d8ff">G</div>
        <div class="card-body">
          <div class="title">Googlea2acee522f385bf3 </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="include_this.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#f8c291">I</div>
        <div class="card-body">
          <div class="title">Include This </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="index.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#dfe6e9">I</div>
        <div class="card-body">
          <div class="title">Index </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="certification.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">L</div>
        <div class="card-body">
          <div class="title">Learning Scuba Diving in Bali — Beginner Courses, Try Dives &amp; PADI Certifications</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="display.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">M</div>
        <div class="card-body">
          <div class="title">My Page</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="New file.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">N</div>
        <div class="card-body">
          <div class="title">New File </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="learn-diving.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">P</div>
        <div class="card-body">
          <div class="title">PADI Courses in Bali · Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="partners.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">P</div>
        <div class="card-body">
          <div class="title">Partners </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="photo-access.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">P</div>
        <div class="card-body">
          <div class="title">Photo Access </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="place.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffd6e0">P</div>
        <div class="card-body">
          <div class="title">Place </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="privacy-policy.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb6c1">P</div>
        <div class="card-body">
          <div class="title">Privacy Policy of Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="profile.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#c8e6c9">P</div>
        <div class="card-body">
          <div class="title">Profile </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="r40u58h7o3saa3ipzi9nktp6rqii1t.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#a5d8ff">R</div>
        <div class="card-body">
          <div class="title">R40u58h7o3saa3ipzi9nktp6rqii1t </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="return-policy.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#f8c291">R</div>
        <div class="card-body">
          <div class="title">Return Policy </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="review.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#dfe6e9">R</div>
        <div class="card-body">
          <div class="title">Review </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="booking-check.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">R</div>
        <div class="card-body">
          <div class="title">Review your booking · Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="booking-confirm.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">R</div>
        <div class="card-body">
          <div class="title">Review your booking · Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="scuba-diving-certification.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">S</div>
        <div class="card-body">
          <div class="title">Scuba Diving Certification Courses in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="scuba-diving-cost-pricelist.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">S</div>
        <div class="card-body">
          <div class="title">Scuba Diving Cost Pricelist </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="scuba-diving-review.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">S</div>
        <div class="card-body">
          <div class="title">Scuba Diving Review</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="s-gen.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">S</div>
        <div class="card-body">
          <div class="title">Sitemap XML Generator</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="select.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffd6e0">S</div>
        <div class="card-body">
          <div class="title">Snorkeling in
Padang Bai</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="snorkeling-packages.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb6c1">S</div>
        <div class="card-body">
          <div class="title">Snorkeling in Bali · Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="Landing-page-snorkeling.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#c8e6c9">S</div>
        <div class="card-body">
          <div class="title">Snorkeling Tours in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="snorkeling.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#a5d8ff">S</div>
        <div class="card-body">
          <div class="title">Snorkeling Tours in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="snorkeling.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#f8c291">S</div>
        <div class="card-body">
          <div class="title">Snorkeling Tours in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="snorkeling2.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#dfe6e9">S</div>
        <div class="card-body">
          <div class="title">Snorkeling2 </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="special-packages.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb3ba">S</div>
        <div class="card-body">
          <div class="title">Special Dive Packages · Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="team.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffdfba">T</div>
        <div class="card-body">
          <div class="title">Team | Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="back.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffffba">T</div>
        <div class="card-body">
          <div class="title">Terms and Conditions of Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="terms.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#baffc9">T</div>
        <div class="card-body">
          <div class="title">Terms and Conditions of Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="try-diving.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#bae1ff">T</div>
        <div class="card-body">
          <div class="title">Try Diving in Bali · Bali Diving</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="try-scuba-diving.html" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#e0bbff">T</div>
        <div class="card-body">
          <div class="title">Try Scuba Diving in Bali</div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="weather.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffd6e0">W</div>
        <div class="card-body">
          <div class="title">Weather </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="weather_json.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#ffb6c1">W</div>
        <div class="card-body">
          <div class="title">Weather Json </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="weather-sea-temperature.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#c8e6c9">W</div>
        <div class="card-body">
          <div class="title">Weather Sea Temperature </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="weather_service.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#a5d8ff">W</div>
        <div class="card-body">
          <div class="title">Weather Service </div>
        </div>
      </a>
    </div>    <div class="card">
      <a href="chat.php" target="_blank" rel="noopener noreferrer">
        <div class="thumb" style="background:#f8c291">W</div>
        <div class="card-body">
          <div class="title">WhatsApp Chat Widget</div>
        </div>
      </a>
    </div>  </div>
  <footer>© Subhi.me Universe — Private Access Only</footer>
</body>
</html>