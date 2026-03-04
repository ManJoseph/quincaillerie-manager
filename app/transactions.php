<?php
session_start();
require_once '../utils/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
$message = '';
// Handle new transaction creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_transaction'])) {
    $buyer_name = trim($_POST['buyer_name']);
    $buyer_contact = trim($_POST['buyer_contact']);
    $transaction_date = $_POST['transaction_date'] ?: date('Y-m-d');
    $payment_status = $_POST['payment_status'];
    $amount_paid = floatval($_POST['amount_paid']);
    $company_id = isset($_SESSION['company_id']) ? intval($_SESSION['company_id']) : 0;
    $stmt = $conn->prepare('INSERT INTO transactions (buyer_name, buyer_contact, transaction_date, payment_status, amount_paid, company_id) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        $message = 'Prepare failed: ' . $conn->error;
    } else {
        $stmt->bind_param('ssssdi', $buyer_name, $buyer_contact, $transaction_date, $payment_status, $amount_paid, $company_id);
        if ($stmt->execute()) {
            $transaction_id = $stmt->insert_id;
            $message = 'Transaction created successfully!';
        } else {
            $message = 'Insert failed: ' . $stmt->error;
        }
        $stmt->close();
    }
    // No redirect; show the new transaction in the table below
}
// Fetch open/recent transactions
$transactions = $conn->query('SELECT * FROM transactions ORDER BY id DESC LIMIT 20');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - StoreMinder</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Transactions page specific styles that extend the design system */
        .transactions-container {
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
        
        .transactions-header {
            text-align: center;
            margin-bottom: 48px;
        }
        
        .transactions-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(24,49,83,0.1);
        }
        
        .transactions-subtitle {
            font-size: 1.1rem;
            color: var(--text);
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        /* Marketing elements */
        .transactions-motivation {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 24px;
            border-radius: var(--border-radius);
            margin-bottom: 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .transactions-motivation::before {
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
        
        .btn-small {
            background: var(--primary);
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-right: 4px;
        }
        
        .btn-small:hover {
            background: var(--accent);
            color: #111;
            transform: translateY(-1px);
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
        
        .transactions-table {
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
            .transactions-container {
                padding: 16px 8px;
            }
            
            .transactions-title {
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
        
        <div class="transactions-container">
            <div class="transactions-header">
                <h1 class="transactions-title">Transaction Management</h1>
                <p class="transactions-subtitle">Create and manage customer transactions efficiently</p>
            </div>
            
            <!-- Marketing motivation section -->
            <div class="transactions-motivation">
                <div class="motivation-content">
                    <h2 class="motivation-title">💳 Smart Transaction Management!</h2>
                    <p class="motivation-subtitle">Easily record, track, and analyze every sale with seamless transaction workflows.</p>
                </div>
            </div>
            
            <!-- Create Transaction Form -->
            <div class="form-container">
                <h3 class="form-title">Start a New Transaction</h3>
                
                <?php if ($message): ?>
                    <div class="<?= strpos($message, 'successfully') !== false ? 'message' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                
                <form method="post" action="transactions.php" autocomplete="off">
                    <input type="hidden" name="create_transaction" value="1">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="buyer_name">Buyer Name</label>
                            <input type="text" id="buyer_name" name="buyer_name" required placeholder="e.g. John Doe">
                        </div>
                        
                        <div class="form-group">
                            <label for="buyer_contact">Buyer Contact</label>
                            <input type="text" id="buyer_contact" name="buyer_contact" placeholder="e.g. 0788...">
                        </div>
                        
                        <div class="form-group">
                            <label for="transaction_date">Date</label>
                            <input type="date" id="transaction_date" name="transaction_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_status">Payment Status</label>
                            <select id="payment_status" name="payment_status" required>
                                <option value="paid">Paid</option>
                                <option value="partial">Partial</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="amount_paid">Amount Paid</label>
                            <input type="number" step="0.01" id="amount_paid" name="amount_paid" value="0" required>
                        </div>
                        
                        <div class="submit-section">
                            <button type="submit" class="btn-primary">Create Transaction</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Recent Transactions Table -->
            <div class="transactions-table">
                <h3 class="table-title">Recent Transactions</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Buyer</th>
                                <th>Status</th>
                                <th>Paid</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($transactions && $transactions->num_rows > 0): ?>
                                <?php while ($row = $transactions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['transaction_date'] ?></td>
                                        <td><?= htmlspecialchars($row['buyer_name']) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= $row['payment_status'] ?>">
                                                <?= ucfirst($row['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($row['amount_paid'], 2) ?></td>
                                        <td><?= isset($row['total_amount']) ? number_format($row['total_amount'], 2) : '0.00' ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="sales.php?transaction_id=<?= $row['id'] ?>" class="btn-small">Add Items</a>
                                                <a href="transaction_receipt.php?id=<?= $row['id'] ?>" class="btn-small">Receipt</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text); opacity: 0.7;">
                                        No transactions found. Create your first transaction above!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="dashboard.php" class="btn-secondary">⬅ Back to Dashboard</a>
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
