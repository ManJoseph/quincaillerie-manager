<?php
session_start();
require_once "../utils/db.php";
// Only allow admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
// Handle add user
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $username, $email, $role, $password);
    if ($stmt->execute()) {
        $message = 'User added successfully!';
    } else {
        $message = 'Error: ' . $stmt->error;
    }
    $stmt->close();
}

// Handle delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header('Location: users.php');
    exit();
}

// Fetch all users
$users = $conn->query("SELECT id, username, email, role FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - StoreMinder</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Users page specific styles that extend the design system */
        .users-container {
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
        
        .users-header {
            text-align: center;
            margin-bottom: 48px;
        }
        
        .users-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(24,49,83,0.1);
        }
        
        .users-subtitle {
            font-size: 1.1rem;
            color: var(--text);
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        /* Marketing elements */
        .users-motivation {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 24px;
            border-radius: var(--border-radius);
            margin-bottom: 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .users-motivation::before {
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
        
        .users-table {
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
        
        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .role-admin {
            background: rgba(239,68,68,0.1);
            color: #ef4444;
        }
        
        .role-user {
            background: rgba(34,197,94,0.1);
            color: #22c55e;
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
            .users-container {
                padding: 16px 8px;
            }
            
            .users-title {
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
                <a href="reports.php">Reports</a>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="users.php">Users</a>
                <?php endif; ?>
                <a href="../auth/logout.php">Logout</a>
            </nav>
        </div>
        
        <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode">🌙</button>
        
        <div class="users-container">
            <div class="users-header">
                <h1 class="users-title">User Management</h1>
                <p class="users-subtitle">Manage system users and their permissions</p>
            </div>
            
            <!-- Marketing motivation section -->
            <div class="users-motivation">
                <div class="motivation-content">
                    <h2 class="motivation-title">👥 Team Management!</h2>
                    <p class="motivation-subtitle">Control access and manage your team members efficiently.</p>
                </div>
            </div>
            
            <!-- Add User Form -->
            <div class="form-container">
                <h3 class="form-title">Add New User</h3>
                
                <?php if ($message): ?>
                    <div class="<?= strpos($message, 'successfully') !== false ? 'message' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                
                <form method="post" action="users.php">
                    <input type="hidden" name="add_user" value="1">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="">Select role</option>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="submit-section">
                            <button type="submit" class="btn-primary">Add User</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Users Table -->
            <div class="users-table">
                <h3 class="table-title">System Users</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users && $users->num_rows > 0): ?>
                                <?php while ($row = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td>
                                            <span class="role-badge role-<?= $row['role'] ?>">
                                                <?= ucfirst($row['role']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="users.php?delete=<?= $row['id'] ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text); opacity: 0.7;">
                                        No users found. Add your first user above!
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
