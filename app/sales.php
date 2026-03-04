<?php
session_start();
require_once '../utils/db.php';
// Check user authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$message = '';
// Get transaction_id from query string or POST
$transaction_id = isset($_GET['transaction_id']) ? intval($_GET['transaction_id']) : (isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : 0);
if (!$transaction_id) {
    header('Location: transactions.php');
    exit();
}
// Fetch transaction details
$stmt = $conn->prepare('SELECT * FROM transactions WHERE id = ?');
$stmt->bind_param('i', $transaction_id);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle sale submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_sale'])) {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $boxes_sold = isset($_POST['boxes_sold']) ? intval($_POST['boxes_sold']) : 0;
    $items_per_box_sold = isset($_POST['items_per_box_sold']) ? intval($_POST['items_per_box_sold']) : 0;
    $loose_items_sold = isset($_POST['loose_items_sold']) ? intval($_POST['loose_items_sold']) : 0;
    $quantity_sold = ($boxes_sold * $items_per_box_sold) + $loose_items_sold;
    $price_per_unit = isset($_POST['price_per_unit']) ? floatval($_POST['price_per_unit']) : 0;
    $deduction_type = isset($_POST['deduction_type']) ? $_POST['deduction_type'] : 'sale';
    if ($quantity_sold <= 0) {
        $message = 'Quantity must be positive.';
    } else {
        // Get product info
        $stmt = $conn->prepare('SELECT name, quantity, price FROM products WHERE id = ?');
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($quantity_sold > $row['quantity']) {
                $message = 'Not enough stock for this deduction.';
            } else {
                $new_qty = $row['quantity'] - $quantity_sold;
                if ($price_per_unit <= 0) {
                    $price_per_unit = $row['price'];
                }
                $total = $quantity_sold * $price_per_unit;
                // Insert sale/deduction
                $stmt2 = $conn->prepare('INSERT INTO sales (transaction_id, product_id, quantity_sold, boxes_sold, items_per_box_sold, loose_items_sold, price_per_unit, total, sale_date, deduction_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
                $stmt2->bind_param('iiiiiddds', $transaction_id, $product_id, $quantity_sold, $boxes_sold, $items_per_box_sold, $loose_items_sold, $price_per_unit, $total, $deduction_type);
                $stmt2->execute();
                $stmt2->close();
                // Update product stock
                $stmt3 = $conn->prepare('UPDATE products SET quantity = ? WHERE id = ?');
                $stmt3->bind_param('ii', $new_qty, $product_id);
                $stmt3->execute();
                $stmt3->close();
                $message = ucfirst($deduction_type) . ' recorded successfully!';
            }
        } else {
            $message = 'Product not found.';
        }
        $stmt->close();
    }
}

// Fetch products for dropdown
$products_result = $conn->query('SELECT id, name, quantity, price FROM products ORDER BY name');
$products = [];
while ($row = $products_result->fetch_assoc()) {
    $products[] = $row;
}

// Fetch sales for this transaction
$sales = $conn->query('SELECT s.id, s.sale_date, s.quantity_sold, s.boxes_sold, s.items_per_box_sold, s.loose_items_sold, s.price_per_unit, s.total, p.name AS product_name, s.deduction_type FROM sales s JOIN products p ON s.product_id = p.id WHERE s.transaction_id = ' . $transaction_id . ' ORDER BY s.sale_date DESC');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Recording - StoreMinder</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Sales page specific styles that extend the design system */
        .sales-container {
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
        
        .sales-header {
            text-align: center;
            margin-bottom: 48px;
        }
        
        .sales-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(24,49,83,0.1);
        }
        
        .sales-subtitle {
            font-size: 1.1rem;
            color: var(--text);
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        /* Marketing elements */
        .sales-motivation {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 24px;
            border-radius: var(--border-radius);
            margin-bottom: 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .sales-motivation::before {
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
        
        .transaction-summary {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 32px 24px;
            margin-bottom: 32px;
            border: 1px solid rgba(24,49,83,0.05);
        }
        
        .summary-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        
        .summary-item {
            padding: 12px;
            background: rgba(24,49,83,0.05);
            border-radius: 8px;
            text-align: center;
        }
        
        .summary-label {
            font-size: 0.9rem;
            color: var(--text);
            opacity: 0.7;
            margin-bottom: 4px;
        }
        
        .summary-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        .summary-value.outstanding {
            color: #dc3545;
        }
        
        .summary-value.paid {
            color: #28a745;
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
        
        .message {
            color: #22c55e;
            margin-bottom: 20px;
            font-weight: 500;
            border-radius: var(--border-radius);
            background: rgba(34,197,94,0.08);
            padding: 12px 20px;
            box-shadow: 0 2px 8px rgba(34,197,94,0.08);
            animation: fadeIn 0.5s;
            text-align: center;
        }
        
        .error {
            color: #ef4444;
            margin-bottom: 20px;
            font-weight: 500;
            border-radius: var(--border-radius);
            background: rgba(239,68,68,0.08);
            padding: 12px 20px;
            box-shadow: 0 2px 8px rgba(239,68,68,0.08);
            animation: fadeIn 0.5s;
            text-align: center;
        }
        
        .sales-history {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 32px 24px;
            margin-bottom: 32px;
            border: 1px solid rgba(24,49,83,0.05);
        }
        
        .history-title {
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
        
        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .type-sale {
            background: rgba(34,197,94,0.1);
            color: #22c55e;
        }
        
        .type-damaged {
            background: rgba(239,68,68,0.1);
            color: #ef4444;
        }
        
        .type-lost {
            background: rgba(245,158,11,0.1);
            color: #f59e0b;
        }
        
        .type-other {
            background: rgba(107,114,128,0.1);
            color: #6b7280;
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
            .sales-container {
                padding: 16px 8px;
            }
            
            .sales-title {
                font-size: 2rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                font-size: 0.8rem;
            }
            
            th, td {
                padding: 8px 12px;
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
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
        
        <div class="sales-container">
            <div class="sales-header">
                <h1 class="sales-title">Sales Recording</h1>
                <p class="sales-subtitle">Record sales and manage inventory efficiently</p>
            </div>
            
            <!-- Marketing motivation section -->
            <div class="sales-motivation">
                <div class="motivation-content">
                    <h2 class="motivation-title">💸 Boost Your Sales!</h2>
                    <p class="motivation-subtitle">Track every transaction and keep your inventory updated in real-time.</p>
                </div>
            </div>
            
            <!-- Transaction Summary -->
            <div class="transaction-summary">
                <h3 class="summary-title">Transaction Summary</h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Buyer</div>
                        <div class="summary-value"><?= htmlspecialchars($transaction['buyer_name']) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Contact</div>
                        <div class="summary-value"><?= htmlspecialchars($transaction['buyer_contact']) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Date</div>
                        <div class="summary-value"><?= $transaction['transaction_date'] ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Payment Status</div>
                        <div class="summary-value"><?= ucfirst($transaction['payment_status']) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Amount Paid</div>
                        <div class="summary-value paid"><?= number_format($transaction['amount_paid'], 2) ?> Frw</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Total Amount</div>
                        <div class="summary-value"><?= number_format($transaction['total_amount'], 2) ?> Frw</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Outstanding</div>
                        <div class="summary-value outstanding"><?= number_format($transaction['total_amount'] - $transaction['amount_paid'], 2) ?> Frw</div>
                    </div>
                </div>
            </div>
            
            <!-- Sales Form -->
            <div class="form-container">
                <h3 class="form-title">Record a Sale</h3>
                <?php if ($message): ?>
                    <div class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                
                <form method="post" action="sales.php?transaction_id=<?= $transaction_id ?>">
                    <input type="hidden" name="add_sale" value="1">
                    <input type="hidden" name="transaction_id" value="<?= $transaction_id ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select name="product_id" id="product_id" required>
                                <option value="">Select product</option>
                                <?php foreach ($products as $row): ?>
                                    <option value="<?= $row['id'] ?>" data-price="<?= $row['price'] ?>">
                                        <?= htmlspecialchars($row['name']) ?> (Stock: <?= $row['quantity'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="boxes_sold">Boxes</label>
                            <input type="number" id="boxes_sold" name="boxes_sold" min="0" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="items_per_box_sold">Items per Box</label>
                            <input type="number" id="items_per_box_sold" name="items_per_box_sold" min="0" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="loose_items_sold">Loose Items</label>
                            <input type="number" id="loose_items_sold" name="loose_items_sold" min="0" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="price_per_unit">Price per Unit (Frw)</label>
                            <input type="number" step="0.01" id="price_per_unit" name="price_per_unit" placeholder="Auto">
                        </div>
                        
                        <div class="form-group">
                            <label for="deduction_type">Deduction Type</label>
                            <select id="deduction_type" name="deduction_type" required>
                                <option value="sale">Sale</option>
                                <option value="damaged">Damaged</option>
                                <option value="lost">Lost</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-note">
                            Total quantity will be calculated as (boxes × items per box) + loose items.
                        </div>
                        
                        <div class="submit-section">
                            <button type="submit" class="btn-primary">Record Sale</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Sales History -->
            <div class="sales-history">
                <h3 class="history-title">Sales & Deductions History</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Boxes</th>
                                <th>Items/Box</th>
                                <th>Loose</th>
                                <th>Quantity</th>
                                <th>Price/Unit</th>
                                <th>Total</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sales && $sales->num_rows > 0): ?>
                                <?php while ($row = $sales->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['sale_date'] ?></td>
                                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                                        <td><?= $row['boxes_sold'] ?></td>
                                        <td><?= $row['items_per_box_sold'] ?></td>
                                        <td><?= $row['loose_items_sold'] ?></td>
                                        <td><?= $row['quantity_sold'] ?></td>
                                        <td><?= number_format($row['price_per_unit'], 0) ?> Frw</td>
                                        <td><?= number_format($row['total'], 0) ?> Frw</td>
                                        <td>
                                            <span class="type-badge type-<?= $row['deduction_type'] ?? 'sale' ?>">
                                                <?= ucfirst($row['deduction_type'] ?? 'sale') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 40px; color: var(--text); opacity: 0.7;">
                                        No sales or deductions recorded yet for this transaction.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="transactions.php" class="btn-secondary">⬅ Back to Transactions</a>
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
        
        // Auto-fill price per unit when product changes
        const productSelect = document.getElementById('product_id');
        const priceInput = document.getElementById('price_per_unit');
        if (productSelect && priceInput) {
            productSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const price = selected.getAttribute('data-price');
                if (price) priceInput.value = price;
                else priceInput.value = '';
            });
        }
    </script>
</body>
</html>
