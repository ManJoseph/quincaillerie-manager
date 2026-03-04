<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>StoreMinder - Professional Inventory Management for Hardware Stores</title>
  <meta name="description" content="Transform your hardware store with StoreMinder. Professional inventory management, sales tracking, and business insights in one powerful platform.">
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Landing page specific styles that extend the design system */
    :root {
      --hero-gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      --accent-gradient: linear-gradient(135deg, var(--accent) 0%, #00ff88 100%);
      --card-shadow: 0 8px 32px rgba(24,49,83,0.12);
      --hover-shadow: 0 12px 48px rgba(24,49,83,0.18);
    }
    
    body {
      font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
    }
    
    .landing-wrapper {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    /* Header */
    .header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(24,49,83,0.1);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      padding: 16px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: all 0.3s ease;
    }
    
    .header-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .header-logo {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 2px 8px rgba(24,49,83,0.1);
    }
    
    .header-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .header-circuit {
      color: var(--accent);
      font-size: 1.2rem;
    }
    
    .header-nav {
      display: flex;
      align-items: center;
      gap: 32px;
    }
    
    .nav-link {
      color: var(--text);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    
    .nav-link:hover {
      color: var(--primary);
    }
    
    .header-cta {
      background: var(--primary);
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .header-cta:hover {
      background: var(--accent);
      color: #111;
      transform: translateY(-2px);
    }
    
    /* Hero Section */
    .hero {
      background: var(--hero-gradient);
      color: white;
      padding: 120px 32px 80px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }
    
    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .hero-badge {
      background: rgba(255,255,255,0.2);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 500;
      display: inline-block;
      margin-bottom: 24px;
      backdrop-filter: blur(10px);
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 800;
      margin-bottom: 24px;
      line-height: 1.2;
      color: #fff;
    }
    
    .hero-subtitle {
      font-size: 1.25rem;
      margin-bottom: 40px;
      opacity: 0.9;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .hero-cta-group {
      display: flex;
      gap: 16px;
      justify-content: center;
      flex-wrap: wrap;
      margin-bottom: 60px;
    }
    
    .btn-primary {
      background: var(--accent);
      color: #111;
      padding: 16px 32px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(57,255,20,0.3);
    }
    
    .btn-secondary {
      background: rgba(255,255,255,0.2);
      color: white;
      padding: 16px 32px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.3);
    }
    
    .btn-secondary:hover {
      background: rgba(255,255,255,0.3);
      transform: translateY(-3px);
    }
    
    .hero-stats {
      display: flex;
      justify-content: center;
      gap: 48px;
      flex-wrap: wrap;
    }
    
    .stat-item {
      text-align: center;
    }
    
    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      display: block;
      color: var(--accent);
    }
    
    .stat-label {
      font-size: 0.9rem;
      opacity: 0.8;
    }
    
    /* Features Section */
    .features {
      padding: 80px 32px;
      background: var(--bg-card);
    }
    
    .section-header {
      text-align: center;
      margin-bottom: 60px;
    }
    
    .section-title {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 16px;
    }
    
    .section-subtitle {
      font-size: 1.1rem;
      color: var(--text);
      opacity: 0.7;
      max-width: 600px;
      margin: 0 auto;
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 32px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .feature-card {
      background: white;
      padding: 32px;
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
      transition: all 0.3s ease;
      border: 1px solid rgba(24,49,83,0.05);
    }
    
    .feature-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--hover-shadow);
    }
    
    .feature-icon {
      width: 60px;
      height: 60px;
      background: var(--accent-gradient);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 24px;
    }
    
    .feature-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 12px;
    }
    
    .feature-description {
      color: var(--text);
      opacity: 0.8;
      line-height: 1.6;
    }
    
    /* Social Proof Section */
    .social-proof {
      padding: 80px 32px;
      background: var(--bg);
    }
    
    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
      max-width: 1000px;
      margin: 0 auto;
    }
    
    .testimonial-card {
      background: white;
      padding: 32px;
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
      position: relative;
    }
    
    .testimonial-card::before {
      content: '"';
      font-size: 4rem;
      color: var(--accent);
      position: absolute;
      top: 16px;
      left: 24px;
      opacity: 0.3;
    }
    
    .testimonial-text {
      font-style: italic;
      margin-bottom: 24px;
      color: var(--text);
      line-height: 1.6;
    }
    
    .testimonial-author {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .author-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: var(--accent-gradient);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      color: #111;
    }
    
    .author-info h4 {
      margin: 0;
      font-weight: 600;
      color: var(--primary);
    }
    
    .author-info p {
      margin: 0;
      font-size: 0.9rem;
      opacity: 0.7;
    }
    
    /* CTA Section */
    .cta-section {
      background: var(--hero-gradient);
      color: white;
      padding: 80px 32px;
      text-align: center;
    }
    
    .cta-content {
      max-width: 600px;
      margin: 0 auto;
    }
    
    .cta-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 16px;
    }
    
    .cta-subtitle {
      font-size: 1.1rem;
      margin-bottom: 32px;
      opacity: 0.9;
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(24,49,83,0.8);
      backdrop-filter: blur(8px);
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }
    
    .modal.active {
      display: flex;
    }
    
    .modal-content {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--hover-shadow);
      padding: 40px;
      max-width: 450px;
      width: 90vw;
      position: relative;
      transform: scale(0.9);
      transition: transform 0.3s ease;
    }
    
    .modal.active .modal-content {
      transform: scale(1);
    }
    
    .modal-close {
      position: absolute;
      top: 16px;
      right: 20px;
      font-size: 1.5rem;
      color: #888;
      cursor: pointer;
      background: none;
      border: none;
      padding: 8px;
      border-radius: 50%;
      transition: background 0.3s ease;
    }
    
    .modal-close:hover {
      background: rgba(0,0,0,0.1);
    }
    
    .modal-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 24px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 8px;
      color: var(--text);
    }
    
    .form-group input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e1e5e9;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
      box-sizing: border-box;
    }
    
    .form-group input:focus {
      outline: none;
      border-color: var(--primary);
    }
    
    .form-error {
      color: #dc3545;
      font-size: 0.9rem;
      margin-top: 8px;
    }
    
    .form-submit {
      width: 100%;
      background: var(--primary);
      color: white;
      padding: 14px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .form-submit:hover {
      background: var(--accent);
      color: #111;
    }
    
    .form-footer {
      margin-top: 16px;
      text-align: center;
    }
    
    .form-footer a {
      color: var(--primary);
      text-decoration: none;
      font-size: 0.9rem;
    }
    
    .form-footer a:hover {
      text-decoration: underline;
    }
    
    /* Footer Styles */
    .footer {
      background: linear-gradient(90deg, var(--primary) 60%, var(--silver) 100%);
      color: white;
      padding: 32px;
      margin-top: 80px;
      position: relative;
      bottom: 0;
      left: 0;
      width: 100%;
      box-sizing: border-box;
    }
    
    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 32px;
      align-items: start;
    }
    
    .footer-left {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    
    .footer-logo {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      background: white;
      object-fit: contain;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .footer-left span {
      font-weight: 500;
      font-size: 0.95rem;
    }
    
    .footer-contact {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    
    .footer-contact a {
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      transition: color 0.3s ease;
    }
    
    .footer-contact a:hover {
      color: var(--accent);
    }
    
    .footer-icon {
      width: 18px;
      height: 18px;
      filter: invert(1);
      opacity: 0.8;
    }
    
    .footer-social {
      display: flex;
      gap: 16px;
      align-items: center;
    }
    
    .footer-social a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
      transition: all 0.3s ease;
      text-decoration: none;
    }
    
    .footer-social a:hover {
      background: var(--accent);
      transform: translateY(-2px);
    }
    
    .footer-social .footer-icon {
      width: 20px;
      height: 20px;
      filter: invert(1);
    }
    
    .footer-legal {
      display: flex;
      gap: 16px;
      align-items: center;
      flex-wrap: wrap;
    }
    
    .footer-link {
      color: white;
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.3s ease;
    }
    
    .footer-link:hover {
      color: var(--accent);
      text-decoration: underline;
    }
    
    .footer-meta {
      display: flex;
      flex-direction: column;
      gap: 4px;
      align-items: flex-end;
      text-align: right;
    }
    
    .footer-meta span {
      font-size: 0.85rem;
      opacity: 0.8;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .header {
        padding: 12px 16px;
      }
      
      .header-nav {
        display: none;
      }
      
      .hero {
        padding: 100px 16px 60px;
      }
      
      .hero-title {
        font-size: 2.5rem;
      }
      
      .hero-cta-group {
        flex-direction: column;
        align-items: center;
      }
      
      .hero-stats {
        gap: 24px;
      }
      
      .features, .social-proof, .cta-section {
        padding: 60px 16px;
      }
      
      .section-title {
        font-size: 2rem;
      }
      
      .features-grid {
        grid-template-columns: 1fr;
      }
      
      .testimonials-grid {
        grid-template-columns: 1fr;
      }
      
      .footer {
        padding: 24px 16px;
        margin-top: 60px;
      }
      
      .footer-content {
        grid-template-columns: 1fr;
        gap: 24px;
        text-align: center;
      }
      
      .footer-left {
        justify-content: center;
      }
      
      .footer-contact {
        align-items: center;
      }
      
      .footer-social {
        justify-content: center;
      }
      
      .footer-legal {
        justify-content: center;
      }
      
      .footer-meta {
        align-items: center;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="landing-wrapper">
    <!-- Header -->
    <header class="header">
      <div class="header-brand">
        <img src="../img/StoreMinder_Logo.jpg" alt="StoreMinder Logo" class="header-logo">
        <span class="header-title">StoreMinder <span class="header-circuit">&#9679;&#9679;&#9679;</span></span>
      </div>
      <nav class="header-nav">
        <a href="#features" class="nav-link">Features</a>
        <a href="#testimonials" class="nav-link">Testimonials</a>
        <a href="about.php" class="nav-link">About</a>
        <a href="#login" class="header-cta" id="headerLoginBtn">Login</a>
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <div class="hero-badge">🚀 Trusted by 500+ Hardware Stores</div>
        <h1 class="hero-title">Transform Your Store with Smart Inventory Management</h1>
        <p class="hero-subtitle">Stop losing money to forgotten stock and missed payments. StoreMinder gives you complete control over your inventory, sales, and business insights in one powerful platform.</p>
        
        <div class="hero-cta-group">
          <a href="#signup" class="btn-primary" id="heroSignupBtn">
            <span>🚀</span>
            Start Free Trial
          </a>
          <a href="#demo" class="btn-secondary">
            <span>📺</span>
            Watch Demo
          </a>
        </div>
        
        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-number">500+</span>
            <span class="stat-label">Stores Trust Us</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">50K+</span>
            <span class="stat-label">Products Managed</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">99.9%</span>
            <span class="stat-label">Uptime</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
      <div class="section-header">
        <h2 class="section-title">Everything You Need to Succeed</h2>
        <p class="section-subtitle">Powerful features designed specifically for hardware stores to streamline operations and boost profits.</p>
      </div>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">📦</div>
          <h3 class="feature-title">Smart Inventory Management</h3>
          <p class="feature-description">Track stock levels in real-time, set low-stock alerts, and never run out of popular items again. Automatic reorder suggestions keep your shelves full.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">💸</div>
          <h3 class="feature-title">Sales & Payment Tracking</h3>
          <p class="feature-description">Record sales instantly, track payments, and manage customer accounts. Generate professional receipts and invoices with your branding.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">📊</div>
          <h3 class="feature-title">Business Intelligence</h3>
          <p class="feature-description">Get insights into your best-selling products, profit margins, and customer behavior. Make data-driven decisions to grow your business.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">👥</div>
          <h3 class="feature-title">Multi-User Access</h3>
          <p class="feature-description">Give your team secure access with role-based permissions. Everyone stays in sync with real-time updates across all devices.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">📱</div>
          <h3 class="feature-title">Mobile Ready</h3>
          <p class="feature-description">Access your store data anywhere, anytime. Our responsive design works perfectly on phones, tablets, and computers.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">🔒</div>
          <h3 class="feature-title">Secure & Reliable</h3>
          <p class="feature-description">Your data is protected with enterprise-grade security. Automatic backups ensure you never lose important information.</p>
        </div>
      </div>
    </section>

    <!-- Social Proof Section -->
    <section class="social-proof" id="testimonials">
      <div class="section-header">
        <h2 class="section-title">Loved by Hardware Store Owners</h2>
        <p class="section-subtitle">See what our customers say about how StoreMinder transformed their business.</p>
      </div>
      
      <div class="testimonials-grid">
        <div class="testimonial-card">
          <p class="testimonial-text">"StoreMinder saved us hours every week on inventory management. The low-stock alerts alone have prevented countless lost sales. Our profits increased by 25% in the first quarter!"</p>
          <div class="testimonial-author">
            <div class="author-avatar">JM</div>
            <div class="author-info">
              <h4>John Mwangi</h4>
              <p>Owner, Mwangi Hardware</p>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <p class="testimonial-text">"The reporting features are incredible. I can see exactly which products are selling and which aren't. This helped me optimize my inventory and increase my profit margins significantly."</p>
          <div class="testimonial-author">
            <div class="author-avatar">SK</div>
            <div class="author-info">
              <h4>Sarah Kamau</h4>
              <p>Manager, Kamau Tools</p>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <p class="testimonial-text">"Setup was incredibly easy, and the customer support is outstanding. My team was using the system effectively within a day. It's like having an extra employee!"</p>
          <div class="testimonial-author">
            <div class="author-avatar">DO</div>
            <div class="author-info">
              <h4>David Ochieng</h4>
              <p>CEO, Ochieng Supplies</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-content">
        <h2 class="cta-title">Ready to Transform Your Business?</h2>
        <p class="cta-subtitle">Join hundreds of successful hardware store owners who trust StoreMinder to manage their inventory and grow their profits.</p>
        <div class="hero-cta-group">
          <a href="#signup" class="btn-primary" id="ctaSignupBtn">
            <span>🚀</span>
            Start Your Free Trial
          </a>
          <a href="#login" class="btn-secondary" id="ctaLoginBtn">
            <span>🔑</span>
            Login to Account
          </a>
        </div>
      </div>
    </section>

    <!-- Login Modal -->
    <div class="modal" id="loginModal">
      <div class="modal-content">
        <button class="modal-close" id="closeLogin">&times;</button>
        <h2 class="modal-title">Welcome Back</h2>
        <form id="loginForm" method="post" action="../auth/login.php">
          <div class="form-error" id="loginError"></div>
          <div class="form-group">
            <label for="login_email">Email Address</label>
            <input type="email" id="login_email" name="email" required>
          </div>
          <div class="form-group">
            <label for="login_password">Password</label>
            <input type="password" id="login_password" name="password" required>
          </div>
          <button type="submit" class="form-submit">Sign In</button>
        </form>
        <div class="form-footer">
          <a href="../auth/forgot_password.php">Forgot your password?</a>
        </div>
      </div>
    </div>

    <!-- Sign Up Modal -->
    <div class="modal" id="signupModal">
      <div class="modal-content">
        <button class="modal-close" id="closeSignup">&times;</button>
        <h2 class="modal-title">Start Your Free Trial</h2>
        <form id="signupForm" method="post" action="../auth/register.php">
          <div class="form-error" id="signupError"></div>
          <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" required>
          </div>
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="company">Company Name</label>
            <input type="text" id="company" name="company" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6">
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
          </div>
          <button type="submit" class="form-submit">Create Account</button>
        </form>
        <div class="form-footer">
          <p>By signing up, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></p>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="footer-left">
          <img src="../img/StoreMinder_Logo.jpg" alt="StoreMinder Logo" class="footer-logo">
          <span>© 2025 StoreMinder. All rights reserved.</span>
        </div>
        
        <div class="footer-contact">
          <a href="mailto:support@storeminder.com" title="Email Support">
            <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/gmail.svg" alt="Email" class="footer-icon">
            support@storeminder.com
          </a>
          <a href="https://wa.me/250780000000" target="_blank" title="WhatsApp">
            <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/whatsapp.svg" alt="WhatsApp" class="footer-icon">
            WhatsApp Support
          </a>
        </div>
        
        <div class="footer-social">
          <a href="#" title="Facebook" target="_blank">
            <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/facebook.svg" alt="Facebook" class="footer-icon">
          </a>
          <a href="#" title="Twitter" target="_blank">
            <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/twitter.svg" alt="Twitter" class="footer-icon">
          </a>
          <a href="#" title="LinkedIn" target="_blank">
            <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/linkedin.svg" alt="LinkedIn" class="footer-icon">
          </a>
        </div>
        
        <div class="footer-legal">
          <a href="#" class="footer-link">Privacy Policy</a>
          <a href="#" class="footer-link">Terms of Service</a>
        </div>
        
        <div class="footer-meta">
          <span>v1.0.0</span>
          <span>Developed by Man.Joseph</span>
        </div>
      </div>
    </footer>
  </div>

  <script>
    // Modal functionality
    const loginBtns = document.querySelectorAll('#headerLoginBtn, #ctaLoginBtn');
    const signupBtns = document.querySelectorAll('#heroSignupBtn, #ctaSignupBtn');
    const loginModal = document.getElementById('loginModal');
    const signupModal = document.getElementById('signupModal');
    const closeLogin = document.getElementById('closeLogin');
    const closeSignup = document.getElementById('closeSignup');

    // Open modals
    loginBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        loginModal.classList.add('active');
      });
    });

    signupBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        signupModal.classList.add('active');
      });
    });

    // Close modals
    closeLogin.addEventListener('click', () => loginModal.classList.remove('active'));
    closeSignup.addEventListener('click', () => signupModal.classList.remove('active'));

    // Close on outside click
    window.addEventListener('click', (e) => {
      if (e.target === loginModal) loginModal.classList.remove('active');
      if (e.target === signupModal) signupModal.classList.remove('active');
    });

    // Login form submission
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      document.getElementById('loginError').textContent = '';
      
      const formData = new FormData(this);
      try {
        const response = await fetch('../auth/login.php', { method: 'POST', body: formData });
        const result = await response.text();
        
        if (result.trim() === 'OK') {
          window.location.href = 'dashboard.php';
        } else {
          document.getElementById('loginError').textContent = result;
        }
      } catch (error) {
        document.getElementById('loginError').textContent = 'An error occurred. Please try again.';
      }
    });

    // Signup form validation
    document.getElementById('signupForm').addEventListener('submit', function(e) {
      const name = document.getElementById('fullname').value.trim();
      const email = document.getElementById('email').value.trim();
      const company = document.getElementById('company').value.trim();
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      let error = '';
      
      if (!name || !email || !company || !password || !confirmPassword) {
        error = 'All fields are required.';
      } else if (!/^\S+@\S+\.\S+$/.test(email)) {
        error = 'Please enter a valid email address.';
      } else if (password.length < 6) {
        error = 'Password must be at least 6 characters long.';
      } else if (password !== confirmPassword) {
        error = 'Passwords do not match.';
      }
      
      if (error) {
        e.preventDefault();
        document.getElementById('signupError').textContent = error;
      }
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Header scroll effect
    window.addEventListener('scroll', () => {
      const header = document.querySelector('.header');
      if (window.scrollY > 100) {
        header.style.background = 'rgba(255, 255, 255, 0.98)';
        header.style.boxShadow = '0 2px 20px rgba(24,49,83,0.1)';
      } else {
        header.style.background = 'rgba(255, 255, 255, 0.95)';
        header.style.boxShadow = 'none';
      }
    });
  </script>
</body>
</html>
