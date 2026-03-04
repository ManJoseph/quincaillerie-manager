<?php
session_start();
require_once "../utils/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['company_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$company_id = $_SESSION['company_id'];
$company_name = '';
$stmt = $conn->prepare("SELECT name FROM companies WHERE id = ?");
$stmt->bind_param('i', $company_id);
$stmt->execute();
$stmt->bind_result($company_name);
$stmt->fetch();
$stmt->close();

// Fetch dashboard data
$totalProducts = 0;
$totalSales = 0;
$totalIncome = 0;
$lowStockCount = 0;
$lowStockProducts = [];
$lowStockThreshold = 5; // You can change this threshold

// Get total products
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE company_id = ?");
$stmt->bind_param('i', $company_id);
$stmt->execute();
$stmt->bind_result($totalProducts);
$stmt->fetch();
$stmt->close();

// Get total sales
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM sales WHERE company_id = ?");
$stmt->bind_param('i', $company_id);
$stmt->execute();
$stmt->bind_result($totalSales);
$stmt->fetch();
$stmt->close();

// Get total income for current month
$stmt = $conn->prepare("SELECT SUM(total) AS income FROM sales WHERE company_id = ? AND MONTH(sale_date) = MONTH(CURRENT_DATE())");
$stmt->bind_param('i', $company_id);
$stmt->execute();
$stmt->bind_result($totalIncome);
$stmt->fetch();
$stmt->close();
$totalIncome = $totalIncome ?? 0;

// Get low-stock products
$stmt = $conn->prepare("SELECT name, quantity FROM products WHERE company_id = ? AND quantity < ? ORDER BY quantity ASC");
$stmt->bind_param('ii', $company_id, $lowStockThreshold);
$stmt->execute();
$result = $stmt->get_result();
$lowStockCount = $result->num_rows;
while ($row = $result->fetch_assoc()) {
    $lowStockProducts[] = $row;
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - StoreMinder</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    /* Dashboard-specific styles that extend the design system */
    .dashboard-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 32px 16px;
    }
    
    /* Full-width header */
    .header {
      width: 100vw;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      background: linear-gradient(90deg, var(--primary) 60%, var(--silver) 100%);
      padding: 16px 32px;
      box-shadow: 0 2px 16px rgba(24,49,83,0.08);
      display: flex;
      align-items: center;
      justify-content: space-between;
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
      background: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .header-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: white;
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
      gap: 24px;
      margin-right: 60px; /* Add space to prevent overlap with theme toggle */
    }
    
    .header-nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    
    .header-nav a:hover {
      color: var(--accent);
    }
    
    /* Add top margin to main content to account for fixed header */
    .main-wrapper {
      padding-top: 80px;
    }
    
    .dashboard-header {
      text-align: center;
      margin-bottom: 48px;
    }
    
    .dashboard-title {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 8px;
      text-shadow: 0 2px 4px rgba(24,49,83,0.1);
    }
    
    .dashboard-subtitle {
      font-size: 1.1rem;
      color: var(--text);
      opacity: 0.8;
      margin-bottom: 0;
    }
    
    /* Marketing elements */
    .dashboard-motivation {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      padding: 24px;
      border-radius: var(--border-radius);
      margin-bottom: 32px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .dashboard-motivation::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }
    
    .motivation-content {
      position: relative;
      z-index: 2;
    }
    
    .motivation-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: var(--accent);
    }
    
    .motivation-subtitle {
      font-size: 1rem;
      opacity: 0.9;
      margin-bottom: 0;
    }
    
    .stats-intro {
      text-align: center;
      margin-bottom: 32px;
      color: var(--text);
      opacity: 0.8;
    }
    
    .stats-intro h3 {
      font-size: 1.3rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 8px;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
      margin-bottom: 48px;
    }
    
    .stat-card {
      background: var(--bg-card);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 32px 24px;
      text-align: center;
      transition: transform var(--transition), box-shadow var(--transition);
      border: 1px solid rgba(24,49,83,0.05);
    }
    
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 32px rgba(24,49,83,0.12);
    }
    
    .stat-card.warning {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      border: 1px solid #ffc107;
    }
    
    .stat-card.success {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      border: 1px solid #28a745;
    }
    
    .stat-card.info {
      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
      border: 1px solid #17a2b8;
    }
    
    .stat-card.primary {
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
      border: 1px solid var(--primary);
    }
    
    .stat-icon {
      font-size: 2.5rem;
      margin-bottom: 16px;
      display: block;
    }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 8px;
      display: block;
    }
    
    .stat-label {
      font-size: 1rem;
      color: var(--text);
      font-weight: 600;
      margin-bottom: 0;
    }
    
    .stat-description {
      font-size: 0.9rem;
      color: var(--text);
      opacity: 0.7;
      margin-top: 8px;
    }
    
    .actions-section {
      background: var(--bg-card);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 32px 24px;
      margin-bottom: 32px;
    }
    
    .actions-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 24px;
      text-align: center;
    }
    
    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
    }
    
    .action-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      background: var(--primary);
      color: var(--text-light);
      text-decoration: none;
      padding: 16px 24px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 1rem;
      box-shadow: 0 2px 8px rgba(24,49,83,0.08);
      transition: all var(--transition);
      border: none;
      cursor: pointer;
    }
    
    .action-btn:hover {
      background: var(--accent);
      color: #111;
      box-shadow: 0 4px 16px var(--accent);
      transform: translateY(-2px);
    }
    
    .action-btn .icon {
      font-size: 1.2rem;
    }
    
    .alert-section {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      border: 1px solid #ffc107;
      border-radius: var(--border-radius);
      padding: 24px;
      margin-bottom: 32px;
    }
    
    .alert-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #856404;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .alert-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .alert-list li {
      padding: 8px 0;
      border-bottom: 1px solid rgba(133, 100, 4, 0.1);
      color: #856404;
      font-weight: 500;
    }
    
    .alert-list li:last-child {
      border-bottom: none;
    }
    
    .logout-section {
      text-align: center;
      margin-top: 48px;
    }
    
    .logout-btn {
      background: #dc3545;
      color: white;
      padding: 12px 32px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: all var(--transition);
      display: inline-block;
    }
    
    .logout-btn:hover {
      background: #c82333;
      transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
      .dashboard-container {
        padding: 16px 8px;
      }
      
      .dashboard-title {
        font-size: 2rem;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
      }
      
      .actions-grid {
        grid-template-columns: 1fr;
      }
      
      .stat-card {
        padding: 24px 16px;
      }
      
      .stat-number {
        font-size: 2rem;
      }
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
    
    @media (max-width: 768px) {
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
  <div class="main-wrapper">
    <div class="header">
      <div class="header-brand">
        <img src="../img/StoreMinder_Logo.jpg" alt="StoreMinder Logo" class="header-logo">
        <span class="header-title">StoreMinder <span class="header-circuit">&#9679;&#9679;&#9679;</span></span>
      </div>
      <nav class="header-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="sales.php">Sales</a>
        <a href="reports.php">Reports</a>
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
          <a href="users.php">Users</a>
        <?php endif; ?>
        <a href="../auth/logout.php">Logout</a>
      </nav>
    </div>
    
    <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode">🌙</button>
    
    <div class="dashboard-container">
      <div class="dashboard-header">
        <h1 class="dashboard-title">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <p class="dashboard-subtitle">StoreMinder Dashboard - <?= htmlspecialchars($company_name) ?></p>
      </div>
      
      <!-- Marketing motivation section -->
      <div class="dashboard-motivation">
        <div class="motivation-content">
          <h2 class="motivation-title">🚀 Your Business is Growing!</h2>
          <p class="motivation-subtitle">Keep track of your success with real-time insights and smart inventory management.</p>
        </div>
      </div>
      
      <div class="stats-intro">
        <h3>📊 Your Business Overview</h3>
        <p>Here's how your store is performing today</p>
      </div>
      
      <div class="stats-grid">
        <div class="stat-card primary">
          <span class="stat-icon">📦</span>
          <span class="stat-number"><?= number_format($totalProducts) ?></span>
          <p class="stat-label">Total Products</p>
          <p class="stat-description">Products in inventory</p>
        </div>
        
        <div class="stat-card success">
          <span class="stat-icon">💸</span>
          <span class="stat-number"><?= number_format($totalSales) ?></span>
          <p class="stat-label">Total Sales</p>
          <p class="stat-description">Transactions recorded</p>
        </div>
        
        <div class="stat-card info">
          <span class="stat-icon">💰</span>
          <span class="stat-number"><?= number_format($totalIncome, 0) ?></span>
          <p class="stat-label">Monthly Income</p>
          <p class="stat-description">Frw this month</p>
        </div>
        
        <div class="stat-card warning">
          <span class="stat-icon">⚠️</span>
          <span class="stat-number"><?= $lowStockCount ?></span>
          <p class="stat-label">Low Stock Alerts</p>
          <p class="stat-description">Below <?= $lowStockThreshold ?> units</p>
        </div>
      </div>
      
      <?php if ($lowStockCount > 0): ?>
        <div class="alert-section">
          <h3 class="alert-title">
            <span>⚠️</span>
            Low Stock Products
          </h3>
          <ul class="alert-list">
            <?php foreach ($lowStockProducts as $prod): ?>
              <li><?= htmlspecialchars($prod['name']) ?> - <strong><?= $prod['quantity'] ?></strong> units remaining</li>
            <?php endforeach; ?>
          </ul>
          <p style="margin-top: 16px; margin-bottom: 0; font-weight: 600; color: #856404;">
            Consider restocking these products soon!
          </p>
        </div>
      <?php endif; ?>
      
      <div class="actions-section">
        <h2 class="actions-title">Quick Actions</h2>
        <div class="actions-grid">
          <a href="sales.php" class="action-btn">
            <span class="icon">💸</span>
            <span>Record Sales</span>
          </a>
          <a href="reports.php" class="action-btn">
            <span class="icon">📊</span>
            <span>Reports & Graphs</span>
          </a>
          <a href="products.php" class="action-btn">
            <span class="icon">📦</span>
            <span>Manage Products</span>
          </a>
          <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="users.php" class="action-btn">
              <span class="icon">👤</span>
              <span>User Management</span>
            </a>
          <?php endif; ?>
        </div>
      </div>
      

    </div>
    
    <!-- Footer Start -->
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
    <!-- Footer End -->
  </div>
  
  <script>
    // Theme toggle logic
    const toggleBtn = document.getElementById('themeToggle');
    
    function setTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('theme', theme);
      toggleBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
    }
    
    toggleBtn.onclick = () => {
      const current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      setTheme(current);
    };
    
    // On load
    const saved = localStorage.getItem('theme') || 'light';
    setTheme(saved);
  </script>
</body>
</html>
