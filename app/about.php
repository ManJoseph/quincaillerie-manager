<?php
// about.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About StoreMinder</title>
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
      background: 
        linear-gradient(120deg, rgba(24,49,83,0.92) 60%, rgba(62,80,110,0.85) 100%),
        url('img/storeminder_bg.jpg') no-repeat center center fixed;
      background-size: cover;
      color: var(--text, #222);
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      flex-direction: column;
    }
    .main-wrapper {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      width: 100vw;
      min-height: 100vh;
      background: transparent;
      padding: 10px;
    }
    .header {
      width: 100vw;
      min-width: 100vw;
      left: 0;
      right: 0;
      position: fixed;
      top: 0;
      z-index: 100;
      background: linear-gradient(90deg, #183153 60%, #C0C0C0 100%);
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 18px 32px 18px 32px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    }
    .header-spacer {
      flex: 1;
    }
    .header-logo {
      width: 120px;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 2px 8px #cbd5e1;
      margin-right: 18px;
      object-fit: contain;
      background: #fff;
    }
    .header-title {
      font-size: 2.1rem;
      font-weight: 700;
      color: #222;
      letter-spacing: 0.03em;
      text-shadow: 0 0 8px #7eea7a, 0 2px 8px #101d2c;
      display: flex;
      align-items: center;
    }
    .header-circuit {
      margin-left: 18px;
      font-size: 1.5rem;
      color: #7eea7a;
      text-shadow: 0 0 8px #7eea7a, 0 0 16px #7eea7a;
      filter: drop-shadow(0 0 6px #7eea7a);
    }
    .about-link {
      font-weight: 600;
      color: #fff;
      text-decoration: none;
      margin-left: 32px;
      font-size: 1.05rem;
      transition: color 0.2s;
    }
    .about-link:hover {
      color: #7eea7a;
    }
    .container {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 0 24px rgba(24,49,83,0.18);
      padding: 48px 32px 40px 32px;
      max-width: 600px;
      margin: 100px auto 0 auto;
      text-align: left;
    }
    h1 {
      margin-bottom: 20px;
      font-size: 2rem;
      font-weight: 700;
      color: #183153;
      text-align: center;
    }
    .glow {
      color: #7eea7a;
      text-shadow: 0 0 8px #7eea7a, 0 0 16px #7eea7a;
      filter: drop-shadow(0 0 6px #7eea7a);
    }
    .btn {
      display: inline-block;
      margin-top: 24px;
      background: #183153;
      color: #fff;
      text-decoration: none;
      padding: 14px 32px;
      border-radius: 8px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      font-size: 1.08rem;
      box-shadow: 0 2px 8px rgba(24,49,83,0.08);
      transition: background 0.3s, box-shadow 0.3s, color 0.3s;
    }
    .btn:hover {
      background: #7eea7a;
      color: #183153;
      box-shadow: 0 4px 16px #7eea7a;
    }
    @media (max-width: 900px) {
      .main-wrapper {
        flex-direction: column;
        padding: 0;
      }
      .container {
        padding: 18px 4vw 18px 4vw;
        max-width: 98vw;
        margin-top: 90px;
      }
      .header {
        width: 100vw;
        min-width: 100vw;
        left: 0;
        right: 0;
        padding: 14px 10px;
      }
      .header-title {
        font-size: 1.3rem;
        margin-top: 8px;
      }
    }
  </style>
</head>
<body>
  <div class="main-wrapper">
    <div class="header">
      <img src="../img/StoreMinder_Logo.jpg" alt="StoreMinder Logo" class="header-logo">
      <span class="header-title">StoreMinder <span class="header-circuit">&#9679;&#9679;&#9679;</span></span>
      <span class="header-spacer"></span>
      <a href="index.php" class="about-link" style="color:#7eea7a;">Home</a>
    </div>
    <div class="container">
      <h1>Why Choose StoreMinder?</h1>
      <p>
        <span class="glow">StoreMinder</span> is your smart assistant for running a successful hardware or retail business. We make inventory, sales, and reporting simple, fast, and stress-free—so you can focus on growing your business, not paperwork.
      </p>
      <h2 style="color:#183153; font-size:1.2em; margin-bottom:8px;">What Makes StoreMinder Different?</h2>
      <ul style="margin: 1.5em 0 1em 1.5em; color: #222;">
        <li><strong>Easy to Use:</strong> No technical skills needed. If you can use a smartphone, you can use StoreMinder!</li>
        <li><strong>All-in-One Solution:</strong> Track your products, record every sale, and see who has paid or not—all in one place.</li>
        <li><strong>Instant Reports:</strong> Get clear, automatic reports on your sales, stock, and outstanding payments. Make smarter decisions, faster.</li>
        <li><strong>Professional Receipts:</strong> Print or share receipts with your buyers—showing exactly what they bought and what they owe.</li>
        <li><strong>Works Anywhere:</strong> Use StoreMinder on your phone, tablet, or computer—at your shop, at home, or on the go.</li>
        <li><strong>Safe & Private:</strong> Your business data is protected and only you can access it.</li>
        <li><strong>Grows With You:</strong> Whether you have one shop or many, StoreMinder adapts to your needs.</li>
      </ul>
      <h2 style="color:#183153; font-size:1.2em; margin-bottom:8px;">Who Is StoreMinder For?</h2>
      <ul style="margin: 1.5em 0 1em 1.5em; color: #222;">
        <li>Hardware store owners and managers</li>
        <li>Small and medium business owners</li>
        <li>Anyone who wants to stop losing money to forgotten stock or missed payments</li>
        <li>Anyone who wants to look professional and organized to their customers</li>
      </ul>
      <h2 style="color:#183153; font-size:1.2em; margin-bottom:8px;">How Will StoreMinder Help You?</h2>
      <ul style="margin: 1.5em 0 1em 1.5em; color: #222;">
        <li>Never run out of stock unexpectedly—get alerts and see what’s selling fast.</li>
        <li>Know exactly who owes you money and how much, with just a click.</li>
        <li>Save hours every week on paperwork and calculations.</li>
        <li>Impress your buyers with clear, professional receipts and reports.</li>
        <li>Feel confident and in control of your business, every single day.</li>
      </ul>
      <h2 style="color:#183153; font-size:1.2em; margin-bottom:8px;">Ready to Make Your Business Smarter?</h2>
      <p>
        Join hundreds of business owners who trust StoreMinder to keep their shops running smoothly. <br>
        <strong>Try StoreMinder today and experience the difference!</strong>
      </p>
    </div>
  </div>
<!-- Footer Start -->
<footer class="footer">
  <div class="footer-left">
    <img src="../img/StoreMinder_Logo.jpg" alt="StoreMinder Logo" class="footer-logo">
    <span>© 2024 StoreMinder. All rights reserved.</span>
  </div>
  <div class="footer-contact">
    <a href="mailto:support@storeminder.com" title="Email Support"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/gmail.svg" alt="Email" class="footer-icon" style="height:20px;width:20px;vertical-align:middle;"> support@storeminder.com</a><br>
    <a href="https://wa.me/250780000000" target="_blank" title="WhatsApp"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/whatsapp.svg" alt="WhatsApp" class="footer-icon" style="height:20px;width:20px;vertical-align:middle;"> WhatsApp</a>
  </div>
  <div class="footer-social">
    <a href="#" title="Facebook" target="_blank"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/facebook.svg" alt="Facebook" class="footer-icon"></a>
    <a href="#" title="Twitter" target="_blank"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/twitter.svg" alt="Twitter" class="footer-icon"></a>
    <a href="#" title="LinkedIn" target="_blank"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/linkedin.svg" alt="LinkedIn" class="footer-icon"></a>
  </div>
  <div class="footer-legal">
    <a href="#" class="footer-link">Privacy Policy</a> |
    <a href="#" class="footer-link">Terms of Service</a>
  </div>
  <div class="footer-meta">
    <span>v1.0.0</span>
    <span>Developed by Man.Joseph</span>
  </div>
</footer>
<style>
.footer {
  width: 100vw;
  background: linear-gradient(90deg, #183153 60%, #C0C0C0 100%);
  color: #fff;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  padding: 18px 32px;
  font-size: 0.98rem;
  box-shadow: 0 -2px 16px rgba(24,49,83,0.08);
  margin-top: 40px;
  gap: 18px;
  background-color: #333;
  position:relative;
  bottom: 0;
  left: 0;
  width: 100%;
}
.footer-logo {
  height: 32px;
  width: 32px;
  border-radius: 8px;
  background: #fff;
  margin-right: 10px;
  vertical-align: middle;
}
.footer-left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.footer-contact, .footer-social, .footer-legal, .footer-meta {
  display: flex;
  align-items: center;
  gap: 10px;
}
.footer-contact {
  flex-direction: column;
  align-items: flex-start;
}
.footer-social a {
  margin-right: 8px;
}
.footer-icon {
  height: 20px;
  width: 20px;
  filter: invert(1);
  margin-right: 4px;
}
.footer-link {
  color: #fff;
  text-decoration: underline;
  margin: 0 4px;
}
.footer-link:hover {
  color: #39FF14;
}
.footer-meta {
  flex-direction: column;
  align-items: flex-end;
  font-size: 0.95rem;
  color: #C0C0C0;
}
@media (max-width: 900px) {
  .footer {
    flex-direction: column;
    align-items: flex-start;
    padding: 18px 10px;
    gap: 10px;
  }
  .footer-meta {
    align-items: flex-start;
  }
}
</style>
<!-- Footer End -->
</body>
</html>
