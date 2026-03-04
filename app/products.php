<?php
session_start();
require_once "../utils/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize message variable
$message = '';
$message_type = '';

// Handle Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $boxes = isset($_POST['boxes']) ? intval($_POST['boxes']) : 0;
    $items_per_box = isset($_POST['items_per_box']) ? intval($_POST['items_per_box']) : 0;
    $loose_items = isset($_POST['loose_items']) ? intval($_POST['loose_items']) : 0;
    $quantity = ($boxes * $items_per_box) + $loose_items;
    $price = $_POST['price'];
    $supplier = $_POST['supplier'];

    // Add error handling and debugging
    // Get company_id from session or use default
    $company_id = $_SESSION['company_id'] ?? 1; // Default to company ID 1 if not set
    
    $stmt = $conn->prepare("INSERT INTO products (company_id, name, quantity, boxes, items_per_box, loose_items, price, supplier) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("isiiiiis", $company_id, $name, $quantity, $boxes, $items_per_box, $loose_items, $price, $supplier);
    if (!$stmt->execute()) {
        $message = "Error adding product: " . $stmt->error;
        $message_type = 'error';
    } else {
        $message = "Product added successfully!";
        $message_type = 'success';
    }
    
    $stmt->close();
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $company_id = $_SESSION['company_id'] ?? 1;
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND company_id = ?");
    $stmt->bind_param("ii", $id, $company_id);
    $stmt->execute();
    $stmt->close();
    header("Location: products.php");
    exit();
}

// Fetch all products for the current company
$company_id = $_SESSION['company_id'] ?? 1; // Default to company ID 1 if not set
$stmt = $conn->prepare("SELECT * FROM products WHERE company_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$products = $stmt->get_result();
if (!$products) {
    die("Query failed: " . $conn->error);
}

// Fetch the product to edit
$editProduct = null;

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $company_id = $_SESSION['company_id'] ?? 1;
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND company_id = ?");
    $stmt->bind_param("ii", $edit_id, $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $editProduct = $result->fetch_assoc();
    }
    $stmt->close();
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $boxes = isset($_POST['boxes']) ? intval($_POST['boxes']) : 0;
    $items_per_box = isset($_POST['items_per_box']) ? intval($_POST['items_per_box']) : 0;
    $loose_items = isset($_POST['loose_items']) ? intval($_POST['loose_items']) : 0;
    $quantity = ($boxes * $items_per_box) + $loose_items;
    $price = $_POST['price'];
    $supplier = $_POST['supplier'];

    // Get company_id from session or use default
    $company_id = $_SESSION['company_id'] ?? 1; // Default to company ID 1 if not set
    
    $stmt = $conn->prepare("UPDATE products SET company_id=?, name=?, quantity=?, boxes=?, items_per_box=?, loose_items=?, price=?, supplier=? WHERE id=?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("isiiiiisi", $company_id, $name, $quantity, $boxes, $items_per_box, $loose_items, $price, $supplier, $id);
    if (!$stmt->execute()) {
        $message = "Error updating product: " . $stmt->error;
        $message_type = 'error';
    } else {
        $message = "Product updated successfully!";
        $message_type = 'success';
    }
    
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products - StoreMinder</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    /* Products page specific styles that extend the design system */
    .products-container {
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
    
    .products-header {
      text-align: center;
      margin-bottom: 48px;
    }
    
    .products-title {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 8px;
      text-shadow: 0 2px 4px rgba(24,49,83,0.1);
    }
    
    .products-subtitle {
      font-size: 1.1rem;
      color: var(--text);
      opacity: 0.8;
      margin-bottom: 0;
    }
    
    /* Marketing elements */
    .products-motivation {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      padding: 24px;
      border-radius: var(--border-radius);
      margin-bottom: 32px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .products-motivation::before {
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
    
    .form-container {
      background: var(--bg-card);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 32px 24px;
      margin-bottom: 32px;
      border: 1px solid rgba(24,49,83,0.05);
    }
    
    .form-title {
      font-size: 1.3rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 24px;
      text-align: center;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    
    .form-group {
      margin-bottom: 0;
    }
    
    .form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 8px;
      color: var(--text);
    }
    
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e1e5e9;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
      box-sizing: border-box;
    }
    
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--primary);
    }
    
    .form-note {
      grid-column: 1 / -1;
      font-size: 0.9rem;
      color: var(--text);
      opacity: 0.7;
      text-align: center;
      margin-top: 8px;
    }
    
    .submit-section {
      grid-column: 1 / -1;
      text-align: center;
      margin-top: 20px;
    }
    
    .btn-primary {
      background: var(--primary);
      color: white;
      padding: 14px 32px;
      border: none;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(24,49,83,0.08);
    }
    
    .btn-primary:hover {
      background: var(--accent);
      color: #111;
      box-shadow: 0 4px 16px var(--accent);
      transform: translateY(-2px);
    }
    
    .btn-secondary {
      background: rgba(24,49,83,0.1);
      color: var(--primary);
      padding: 12px 24px;
      border: 1px solid var(--primary);
      border-radius: 8px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      display: inline-block;
    }
    
    .btn-secondary:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-2px);
    }
    
    .btn-danger {
      background: #dc3545;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
      background: #c82333;
      transform: translateY(-1px);
    }
    
    .btn-warning {
      background: #ffc107;
      color: #222;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .btn-warning:hover {
      background: #e0a800;
      transform: translateY(-1px);
    }
    
    .products-table {
      background: var(--bg-card);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 32px 24px;
      margin-bottom: 32px;
      border: 1px solid rgba(24,49,83,0.05);
    }
    
    .table-title {
      font-size: 1.3rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 24px;
      text-align: center;
    }
    
    .table-container {
      overflow-x: auto;
      border-radius: var(--border-radius);
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      font-size: 0.9rem;
    }
    
    th, td {
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid #e1e5e9;
    }
    
    th {
      background: var(--primary);
      color: white;
      font-weight: 600;
      position: sticky;
      top: 0;
    }
    
    tr:hover {
      background: rgba(24,49,83,0.02);
    }
    
    .stock-status {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 500;
    }
    
    .stock-high {
      background: rgba(34,197,94,0.1);
      color: #22c55e;
    }
    
    .stock-medium {
      background: rgba(245,158,11,0.1);
      color: #f59e0b;
    }
    
    .stock-low {
      background: rgba(239,68,68,0.1);
      color: #ef4444;
    }
    
    .action-buttons {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
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
      .products-container {
        padding: 16px 8px;
      }
      
      .products-title {
        font-size: 2rem;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .table-container {
        font-size: 0.8rem;
      }
      
      th, td {
        padding: 8px 12px;
      }
      
      .action-buttons {
        flex-direction: column;
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
  <div class="main-wrapper">
    <!-- Header -->
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
    
    <div class="products-container">
      <?php if ($message): ?>
        <div class="message <?= $message_type ?>" style="
          padding: 16px;
          margin-bottom: 24px;
          border-radius: 8px;
          text-align: center;
          font-weight: 500;
          <?= $message_type === 'success' ? 'background: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2);' : 'background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2);' ?>
        ">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
      
      <div class="products-header">
        <h1 class="products-title">Manage Products</h1>
        <p class="products-subtitle">Add, edit, and manage your inventory efficiently</p>
      </div>
      
      <!-- Marketing motivation section -->
      <div class="products-motivation">
        <div class="motivation-content">
          <h2 class="motivation-title">📦 Master Your Inventory!</h2>
          <p class="motivation-subtitle">Keep your stock organized and never run out of popular items again.</p>
        </div>
      </div>
      
      <!-- Add/Edit Product Form -->
      <div class="form-container">
        <h3 class="form-title"><?= $editProduct ? 'Edit Product' : 'Add New Product' ?></h3>
        
        <form method="post" action="products.php">
          <?php if ($editProduct): ?>
            <input type="hidden" name="update_product" value="1">
            <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
          <?php else: ?>
            <input type="hidden" name="add_product" value="1">
          <?php endif; ?>
          
          <div class="form-grid">
            <div class="form-group">
              <label for="name">Product Name</label>
              <input type="text" id="name" name="name" value="<?= $editProduct ? htmlspecialchars($editProduct['name']) : '' ?>" required>
            </div>
            
            <div class="form-group">
              <label for="supplier">Supplier</label>
              <input type="text" id="supplier" name="supplier" value="<?= $editProduct ? htmlspecialchars($editProduct['supplier']) : '' ?>" required>
            </div>
            
            <div class="form-group">
              <label for="boxes">Boxes</label>
              <input type="number" id="boxes" name="boxes" min="0" value="<?= $editProduct ? $editProduct['boxes'] : '0' ?>">
            </div>
            
            <div class="form-group">
              <label for="items_per_box">Items per Box</label>
              <input type="number" id="items_per_box" name="items_per_box" min="0" value="<?= $editProduct ? $editProduct['items_per_box'] : '0' ?>">
            </div>
            
            <div class="form-group">
              <label for="loose_items">Loose Items</label>
              <input type="number" id="loose_items" name="loose_items" min="0" value="<?= $editProduct ? $editProduct['loose_items'] : '0' ?>">
            </div>
            
            <div class="form-group">
              <label for="price">Price per Unit (Frw)</label>
              <input type="number" step="0.01" id="price" name="price" value="<?= $editProduct ? $editProduct['price'] : '' ?>" required>
            </div>
            
            <div class="form-note">
              Total quantity will be calculated as (boxes × items per box) + loose items.
            </div>
            
            <div class="submit-section">
              <button type="submit" class="btn-primary">
                <?= $editProduct ? 'Update Product' : 'Add Product' ?>
              </button>
              <?php if ($editProduct): ?>
                <a href="products.php" class="btn-secondary" style="margin-left: 12px;">Cancel</a>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
      
      <!-- Products Table -->
      <div class="products-table">
        <h3 class="table-title">Product Inventory</h3>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Supplier</th>
                <th>Boxes</th>
                <th>Items/Box</th>
                <th>Loose</th>
                <th>Total Qty</th>
                <th>Price/Unit</th>
                <th>Stock Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['supplier']) ?></td>
                    <td><?= $row['boxes'] ?></td>
                    <td><?= $row['items_per_box'] ?></td>
                    <td><?= $row['loose_items'] ?></td>
                    <td><strong><?= $row['quantity'] ?></strong></td>
                    <td><?= number_format($row['price'], 0) ?> Frw</td>
                    <td>
                      <?php
                        $stockClass = 'stock-high';
                        $stockText = 'High';
                        if ($row['quantity'] <= 5) {
                          $stockClass = 'stock-low';
                          $stockText = 'Low';
                        } elseif ($row['quantity'] <= 20) {
                          $stockClass = 'stock-medium';
                          $stockText = 'Medium';
                        }
                      ?>
                      <span class="stock-status <?= $stockClass ?>"><?= $stockText ?></span>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <a href="products.php?edit=<?= $row['id'] ?>" class="btn-warning">Edit</a>
                        <a href="products.php?delete=<?= $row['id'] ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="10" style="text-align: center; padding: 40px; color: var(--text); opacity: 0.7;">
                    No products found. Add your first product above!
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
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
    
    // Auto-hide success messages after 5 seconds
    const messageElement = document.querySelector('.message.success');
    if (messageElement) {
      setTimeout(() => {
        messageElement.style.opacity = '0';
        setTimeout(() => {
          messageElement.remove();
        }, 300);
      }, 5000);
    }
  </script>
</body>
</html>
