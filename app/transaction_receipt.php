<?php
session_start();
require_once '../utils/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
$transaction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$transaction_id) {
    echo 'Transaction not found.';
    exit();
}
// Fetch transaction
$stmt = $conn->prepare('SELECT * FROM transactions WHERE id = ?');
$stmt->bind_param('i', $transaction_id);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();
$stmt->close();
// Handle payment update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment'])) {
    $new_amount_paid = intval($_POST['amount_paid']);
    $new_status = $_POST['payment_status'];
    $stmt = $conn->prepare('UPDATE transactions SET amount_paid = ?, payment_status = ? WHERE id = ?');
    $stmt->bind_param('isi', $new_amount_paid, $new_status, $transaction_id);
    $stmt->execute();
    $stmt->close();
    // Refresh transaction data
    $stmt = $conn->prepare('SELECT * FROM transactions WHERE id = ?');
    $stmt->bind_param('i', $transaction_id);
    $stmt->execute();
    $transaction = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
// Fetch sales for this transaction
$sales = $conn->query('SELECT s.*, p.name AS product_name FROM sales s JOIN products p ON s.product_id = p.id WHERE s.transaction_id = ' . $transaction_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - StoreMinder</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Receipt page specific styles that extend the design system */
        .receipt-container {
            max-width: 800px;
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
        
        .receipt-header {
            text-align: center;
            margin-bottom: 48px;
        }
        
        .receipt-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(24,49,83,0.1);
        }
        
        .receipt-subtitle {
            font-size: 1.1rem;
            color: var(--text);
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        /* Marketing elements */
        .receipt-motivation {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 24px;
            border-radius: var(--border-radius);
            margin-bottom: 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .receipt-motivation::before {
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
        
        .receipt-card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 32px 24px;
            margin-bottom: 32px;
            border: 1px solid rgba(24,49,83,0.05);
        }
        
        .receipt-info {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(24,49,83,0.1);
        }
        
        .receipt-info h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
        }
        
        .receipt-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .receipt-detail {
            text-align: center;
            padding: 12px;
            background: rgba(24,49,83,0.05);
            border-radius: 8px;
        }
        
        .receipt-detail strong {
            display: block;
            color: var(--primary);
            margin-bottom: 4px;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 24px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary:hover {
            background: var(--accent);
            color: #111;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-secondary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .receipt-table th,
        .receipt-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .receipt-table th {
            background: var(--primary);
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        .receipt-table tr:hover {
            background: rgba(24,49,83,0.02);
        }
        
        .summary-section {
            background: rgba(24,49,83,0.05);
            padding: 24px;
            border-radius: var(--border-radius);
            margin-bottom: 24px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        
        .summary-item {
            text-align: center;
            padding: 16px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .summary-item strong {
            display: block;
            color: var(--primary);
            font-size: 1.1rem;
            margin-bottom: 4px;
        }
        
        .summary-item .amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text);
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: rgba(34,197,94,0.1);
            color: #22c55e;
        }
        
        .status-partial {
            background: rgba(245,158,11,0.1);
            color: #f59e0b;
        }
        
        .status-unpaid {
            background: rgba(239,68,68,0.1);
            color: #ef4444;
        }
        
        .payment-form {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid rgba(24,49,83,0.05);
        }
        
        .payment-form h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 16px;
            text-align: center;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: end;
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
            padding: 10px 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .email-form {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .email-form input[type="email"] {
            flex: 1;
            min-width: 200px;
            padding: 8px 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 0.9rem;
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
            .receipt-container {
                padding: 16px 8px;
            }
            
            .receipt-title {
                font-size: 2rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .email-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .summary-grid {
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
                <a href="transactions.php">Transactions</a>
                <a href="reports.php">Reports</a>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="users.php">Users</a>
                <?php endif; ?>
                <a href="../auth/logout.php">Logout</a>
            </nav>
        </div>
        
        <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode">🌙</button>
        
        <div class="receipt-container">
            <div class="receipt-header">
                <h1 class="receipt-title">Transaction Receipt</h1>
                <p class="receipt-subtitle">Professional receipt for transaction #<?= $transaction_id ?></p>
            </div>
            
            <!-- Marketing motivation section -->
            <div class="receipt-motivation">
                <div class="motivation-content">
                    <h2 class="motivation-title">🧾 Professional Receipts!</h2>
                    <p class="motivation-subtitle">Generate, print, and share professional receipts with your customers.</p>
                </div>
            </div>
            
            <!-- Receipt Card -->
            <div class="receipt-card">
                <div class="receipt-info">
                    <h2>Sales Receipt</h2>
                    <div class="receipt-details">
                        <div class="receipt-detail">
                            <strong>Transaction ID</strong>
                            <span>#<?= $transaction_id ?></span>
                        </div>
                        <div class="receipt-detail">
                            <strong>Date</strong>
                            <span><?= $transaction['transaction_date'] ?></span>
                        </div>
                        <div class="receipt-detail">
                            <strong>Buyer</strong>
                            <span><?= htmlspecialchars($transaction['buyer_name']) ?></span>
                        </div>
                        <div class="receipt-detail">
                            <strong>Contact</strong>
                            <span><?= htmlspecialchars($transaction['buyer_contact']) ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button onclick="window.print()" class="btn-primary">
                        🖨️ Print Receipt
                    </button>
                    <a href="transaction_receipt_pdf.php?id=<?= $transaction_id ?>" class="btn-primary" target="_blank">
                        📄 Download PDF
                    </a>
                    <a href="https://wa.me/?text=<?= urlencode('Here is your receipt: ' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?id=' . $transaction_id) ?>" class="btn-primary" target="_blank">
                        📱 Send via WhatsApp
                    </a>
                </div>
                
                <!-- Email Form -->
                <form method="post" action="send_receipt_email.php" class="email-form">
                    <input type="hidden" name="transaction_id" value="<?= $transaction_id ?>">
                    <input type="email" name="email" placeholder="Customer Email" value="<?= htmlspecialchars($transaction['buyer_contact']) ?>" required>
                    <button type="submit" class="btn-primary">📧 Send by Email</button>
                </form>
            </div>
            
            <!-- Receipt Table -->
            <div class="receipt-card">
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price/Unit</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $grand_total = 0; while ($row = $sales->fetch_assoc()): $grand_total += $row['total']; ?>
                        <tr>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= $row['quantity_sold'] ?></td>
                            <td><?= number_format($row['price_per_unit'], 0) ?> Frw</td>
                            <td><?= number_format($row['total'], 0) ?> Frw</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Summary Section -->
            <?php
            $total_amount = $grand_total;
            $amount_paid = intval($transaction['amount_paid']);
            $outstanding = $total_amount - $amount_paid;
            ?>
            <div class="summary-section">
                <div class="summary-grid">
                    <div class="summary-item">
                        <strong>Total Amount</strong>
                        <div class="amount"><?= number_format($total_amount, 0) ?> Frw</div>
                    </div>
                    <div class="summary-item">
                        <strong>Amount Paid</strong>
                        <div class="amount"><?= number_format($amount_paid, 0) ?> Frw</div>
                    </div>
                    <div class="summary-item">
                        <strong>Outstanding</strong>
                        <div class="amount"><?= number_format($outstanding, 0) ?> Frw</div>
                    </div>
                    <div class="summary-item">
                        <strong>Payment Status</strong>
                        <div class="amount">
                            <span class="status-badge status-<?= $transaction['payment_status'] ?>">
                                <?= ucfirst($transaction['payment_status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Update Form -->
            <div class="payment-form">
                <h3>Update Payment Information</h3>
                <form method="post" action="transaction_receipt.php?id=<?= $transaction_id ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="amount_paid">Amount Paid (Frw)</label>
                            <input type="number" id="amount_paid" name="amount_paid" min="0" value="<?= $amount_paid ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_status">Payment Status</label>
                            <select id="payment_status" name="payment_status" required>
                                <option value="paid" <?= $transaction['payment_status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="partial" <?= $transaction['payment_status'] == 'partial' ? 'selected' : '' ?>>Partial</option>
                                <option value="unpaid" <?= $transaction['payment_status'] == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="update_payment" class="btn-primary" style="width: 100%;">
                                💳 Update Payment
                            </button>
                        </div>
                    </div>
                </form>
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
    </script>
</body>
</html> 
