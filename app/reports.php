<?php
// reports.php
// Page for viewing reports and graphs
// TODO: Implement income, sales, and stock reports with charts

session_start();
require_once '../utils/db.php';
// Check user authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}


// Filtering logic
$where = [];
$params = [];
if (!empty($_GET['date_from'])) {
    $where[] = "sale_date >= ?";
    $params[] = $_GET['date_from'];
}
if (!empty($_GET['date_to'])) {
    $where[] = "sale_date <= ?";
    $params[] = $_GET['date_to'];
}
if (!empty($_GET['deduction_type'])) {
    $where[] = "deduction_type = ?";
    $params[] = $_GET['deduction_type'];
}
$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

// Deduction type summary (filtered)
$deductionSummary = [];
$deductionSql = "SELECT deduction_type, COUNT(*) as count, SUM(total) as total FROM sales $whereSql GROUP BY deduction_type";
$stmt = $conn->prepare($deductionSql);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $deductionSummary[$row['deduction_type']] = [
        'count' => $row['count'],
        'total' => $row['total']
    ];
}
$stmt->close();

// Monthly income for last 6 months (filtered, only real sales)
$incomeData = [];
$incomeLabels = [];
$incomeWhere = $where;
$incomeParams = $params;
if (empty($_GET['deduction_type'])) {
    $incomeWhere[] = "deduction_type = 'sale'";
}
$incomeWhereSql = $incomeWhere ? ("WHERE " . implode(" AND ", $incomeWhere)) : "";
$incomeSql = "SELECT DATE_FORMAT(sale_date, '%Y-%m') as month, SUM(total) as income FROM sales $incomeWhereSql GROUP BY month ORDER BY month DESC LIMIT 6";
$stmt = $conn->prepare($incomeSql);
if ($incomeParams) {
    $types = str_repeat('s', count($incomeParams));
    $stmt->bind_param($types, ...$incomeParams);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $incomeLabels[] = $row['month'];
    $incomeData[] = $row['income'] ?? 0;
}
$incomeLabels = array_reverse($incomeLabels);
$incomeData = array_reverse($incomeData);
$stmt->close();

// Fetch best-selling products (top 5, filtered, only real sales)
$bestProducts = [];
$bestLabels = [];
$bestWhere = $where;
$bestParams = $params;
if (empty($_GET['deduction_type'])) {
    $bestWhere[] = "s.deduction_type = 'sale'";
}
$bestWhereSql = $bestWhere ? ("WHERE " . implode(" AND ", $bestWhere)) : "";
$bestSql = "SELECT p.name, SUM(s.quantity_sold) as sold FROM sales s JOIN products p ON s.product_id = p.id $bestWhereSql GROUP BY s.product_id ORDER BY sold DESC LIMIT 5";
$stmt = $conn->prepare($bestSql);
if ($stmt === false) {
    // Query preparation failed, log error and avoid fatal error
    error_log('Failed to prepare best-selling products query: ' . $conn->error);
    $bestLabels = [];
    $bestProducts = [];
} else {
    if ($bestParams) {
        $types = str_repeat('s', count($bestParams));
        $stmt->bind_param($types, ...$bestParams);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bestLabels[] = $row['name'];
        $bestProducts[] = $row['sold'];
    }
    $stmt->close();
}

// Fetch current stock for all products
$stockLabels = [];
$stockData = [];
$stockQuery = $conn->query("SELECT name, quantity FROM products ORDER BY name");
while ($row = $stockQuery->fetch_assoc()) {
    $stockLabels[] = $row['name'];
    $stockData[] = $row['quantity'];
}

// Export logic
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    require_once('fpdf186/fpdf.php');
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Reports & Graphs', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    // Sales & Deductions Summary
    $pdf->Cell(0, 10, 'Sales & Deductions Summary', 0, 1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Type', 1);
    $pdf->Cell(40, 8, 'Count', 1);
    $pdf->Cell(50, 8, 'Total Value', 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 11);
    foreach ($deductionSummary as $type => $info) {
        $pdf->Cell(50, 8, ucfirst($type), 1);
        $pdf->Cell(40, 8, $info['count'], 1);
        $pdf->Cell(50, 8, number_format($info['total'], 0) . ' Frw', 1);
        $pdf->Ln();
    }
    $pdf->Ln(5);
    // Monthly Income
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Monthly Income (Last 6 Months)', 0, 1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Month', 1);
    $pdf->Cell(50, 8, 'Income', 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 11);
    foreach ($incomeLabels as $i => $month) {
        $pdf->Cell(50, 8, $month, 1);
        $pdf->Cell(50, 8, number_format($incomeData[$i], 0) . ' Frw', 1);
        $pdf->Ln();
    }
    $pdf->Ln(5);
    // Best-Selling Products
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Best-Selling Products (Top 5)', 0, 1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(70, 8, 'Product', 1);
    $pdf->Cell(40, 8, 'Units Sold', 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 11);
    foreach ($bestLabels as $i => $name) {
        $pdf->Cell(70, 8, $name, 1);
        $pdf->Cell(40, 8, number_format($bestProducts[$i], 0) . ' Frw', 1);
        $pdf->Ln();
    }
    $pdf->Ln(5);
    // Current Stock by Product
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Current Stock by Product', 0, 1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(70, 8, 'Product', 1);
    $pdf->Cell(40, 8, 'Stock Quantity', 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 11);
    foreach ($stockLabels as $i => $name) {
        $pdf->Cell(70, 8, $name, 1);
        $pdf->Cell(40, 8, number_format($stockData[$i], 0) . ' Frw', 1);
        $pdf->Ln();
    }
    $pdf->Output('D', 'report_' . date('Ymd_His') . '.pdf');
    exit();
} elseif (isset($_GET['export'])) {
    $format = $_GET['export'];
    $filename = 'report_' . date('Ymd_His');
    if ($format === 'excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        // Sales & Deductions Summary
        echo "<table border='1'>";
        echo "<tr><th colspan='3' style='font-size:16px;'>Sales & Deductions Summary</th></tr>";
        echo "<tr><th>Type</th><th>Count</th><th>Total Value</th></tr>";
        foreach ($deductionSummary as $type => $info) {
            echo "<tr><td>" . ucfirst($type) . "</td><td>" . $info['count'] . "</td><td>" . number_format($info['total'], 0) . " Frw</td></tr>";
        }
        echo "</table><br>";
        // Monthly Income
        echo "<table border='1'>";
        echo "<tr><th colspan='2' style='font-size:16px;'>Monthly Income (Last 6 Months)</th></tr>";
        echo "<tr><th>Month</th><th>Income</th></tr>";
        foreach ($incomeLabels as $i => $month) {
            echo "<tr><td>" . $month . "</td><td>" . number_format($incomeData[$i], 0) . " Frw</td></tr>";
        }
        echo "</table><br>";
        // Best-Selling Products
        echo "<table border='1'>";
        echo "<tr><th colspan='2' style='font-size:16px;'>Best-Selling Products (Top 5)</th></tr>";
        echo "<tr><th>Product</th><th>Units Sold</th></tr>";
        foreach ($bestLabels as $i => $name) {
            echo "<tr><td>" . $name . "</td><td>" . number_format($bestProducts[$i], 0) . " Frw</td></tr>";
        }
        echo "</table><br>";
        // Current Stock by Product
        echo "<table border='1'>";
        echo "<tr><th colspan='2' style='font-size:16px;'>Current Stock by Product</th></tr>";
        echo "<tr><th>Product</th><th>Stock Quantity</th></tr>";
        foreach ($stockLabels as $i => $name) {
            echo "<tr><td>" . $name . "</td><td>" . number_format($stockData[$i], 0) . " Frw</td></tr>";
        }
        echo "</table>";
        exit();
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Graphs - StoreMinder</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reports page specific styles that extend the design system */
        .reports-container {
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
        
        .reports-header {
            text-align: center;
            margin-bottom: 48px;
        }
        
        .reports-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(24,49,83,0.1);
        }
        
        .reports-subtitle {
            font-size: 1.1rem;
            color: var(--text);
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        /* Marketing elements */
        .reports-motivation {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 24px;
            border-radius: var(--border-radius);
            margin-bottom: 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .reports-motivation::before {
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            margin: 0 8px;
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
            margin: 0 8px;
        }
        
        .btn-secondary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .chart-container {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 32px 24px;
            margin-bottom: 32px;
            border: 1px solid rgba(24,49,83,0.05);
        }
        
        .chart-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 24px;
            text-align: center;
        }
        
        .chart-wrapper {
            position: relative;
            height: 400px;
            margin: 20px 0;
        }
        
        .summary-table {
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
            .reports-container {
                padding: 16px 8px;
            }
            
            .reports-title {
                font-size: 2rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-wrapper {
                height: 300px;
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
        
        .export-btn {
            background: linear-gradient(90deg, var(--accent) 0%, var(--primary) 100%);
            color: #222;
        }
        
        .export-btn:hover {
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            color: #fff;
        }
        
        .message {
            color: #22c55e;
            margin-bottom: 10px;
            font-weight: 500;
            border-radius: var(--border-radius);
            background: rgba(34,197,94,0.08);
            padding: 10px 18px;
            box-shadow: 0 2px 8px rgba(34,197,94,0.08);
            animation: fadeIn 0.5s;
        }
        
        .error {
            color: #ef4444;
            margin-bottom: 10px;
            font-weight: 500;
            border-radius: var(--border-radius);
            background: rgba(239,68,68,0.08);
            padding: 10px 18px;
            box-shadow: 0 2px 8px rgba(239,68,68,0.08);
            animation: fadeIn 0.5s;
        }
        
        .chart-stack {
            display: flex;
            flex-direction: column;
            gap: 40px;
            align-items: center;
            width: 100%;
            margin: 48px auto;
        }
        
        .theme-toggle {
            position: fixed;
            top: 18px;
            right: 18px;
            background: var(--primary-dark);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            font-size: 1.3rem;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            z-index: 100;
            transition: background var(--transition), box-shadow var(--transition);
        }
        
        .theme-toggle:hover {
            background: var(--primary);
            box-shadow: 0 4px 16px rgba(37,99,235,0.18);
        }
        
        .filter-form {
            width: 100%;
            max-width: 700px;
            margin: 24px auto 0 auto;
            background: var(--card);
            padding: 18px 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
            transition: background var(--transition), box-shadow var(--transition);
        }
        
        .filter-form label {
            margin-right: 5px;
            font-weight: 500;
            color: var(--primary-dark);
        }
        
        input, select {
            font-family: inherit;
            font-size: 1rem;
            padding: 8px 12px;
            border-radius: var(--border-radius);
            border: 1px solid #e3e3e3;
            outline: none;
            transition: border var(--transition), box-shadow var(--transition);
        }
        input:focus, select:focus {
            border: 1.5px solid var(--primary);
            box-shadow: 0 2px 8px rgba(37,99,235,0.08);
        }
        @media (max-width: 900px) {
            .chart-container, .form-container, .filter-form {
                max-width: 98vw;
                padding: 12px 6px 18px 6px;
            }
            .chart-stack {
                gap: 24px;
            }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode">🌙</button>
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
        
        <div class="reports-container">
            <div class="reports-header">
                <h1 class="reports-title">Reports & Graphs</h1>
                <p class="reports-subtitle">Analyze your business performance with detailed insights</p>
            </div>
            
            <!-- Marketing motivation section -->
            <div class="reports-motivation">
                <div class="motivation-content">
                    <h2 class="motivation-title">📊 Data-Driven Decisions!</h2>
                    <p class="motivation-subtitle">Turn your business data into actionable insights for growth.</p>
                </div>
            </div>
            
            <!-- Filter Form -->
            <div class="form-container">
                <h3 class="form-title">Filter Reports</h3>
                <form method="get" action="reports.php">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="deduction_type">Type</label>
                            <select id="deduction_type" name="deduction_type">
                                <option value="">All</option>
                                <option value="sale" <?= (($_GET['deduction_type'] ?? '') == 'sale') ? 'selected' : '' ?>>Sale</option>
                                <option value="damaged" <?= (($_GET['deduction_type'] ?? '') == 'damaged') ? 'selected' : '' ?>>Damaged</option>
                                <option value="lost" <?= (($_GET['deduction_type'] ?? '') == 'lost') ? 'selected' : '' ?>>Lost</option>
                                <option value="other" <?= (($_GET['deduction_type'] ?? '') == 'other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="submit-section">
                            <button type="submit" class="btn-primary">Filter</button>
                            <button type="submit" name="export" value="pdf" class="btn-secondary">Export PDF</button>
                            <button type="submit" name="export" value="excel" class="btn-secondary">Export Excel</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Sales Summary Table -->
            <div class="summary-table">
                <h3 class="table-title">Sales & Deductions Summary</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Count</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deductionSummary as $type => $info): ?>
                                <tr>
                                    <td><?= ucfirst($type) ?></td>
                                    <td><?= $info['count'] ?></td>
                                    <td><?= number_format($info['total'], 0) ?> Frw</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p style="text-align: center; margin-top: 16px; color: var(--text); opacity: 0.7; font-size: 0.9rem;">
                    Only 'Sale' type is included in charts below.
                </p>
            </div>
            
            <!-- Monthly Income Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Monthly Income (Last 6 Months)</h3>
                <div class="chart-wrapper">
                    <canvas id="incomeChart"></canvas>
                </div>
                <p style="text-align: center; margin-top: 16px; color: var(--text); opacity: 0.7; font-size: 0.9rem;">
                    This chart shows the total income from sales for each of the last 6 months. Use it to spot trends and seasonality in your business revenue.
                </p>
            </div>
            
            <!-- Best-Selling Products Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Best-Selling Products (Top 5)</h3>
                <div class="chart-wrapper">
                    <canvas id="bestChart"></canvas>
                </div>
                <p style="text-align: center; margin-top: 16px; color: var(--text); opacity: 0.7; font-size: 0.9rem;">
                    This bar chart displays the top 5 products with the highest sales volume. It helps you identify your most popular items.
                </p>
            </div>
            
            <!-- Current Stock Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Current Stock by Product</h3>
                <div class="chart-wrapper">
                    <canvas id="stockChart"></canvas>
                </div>
                <p style="text-align: center; margin-top: 16px; color: var(--text); opacity: 0.7; font-size: 0.9rem;">
                    This chart shows the current stock levels for each product. Use it to monitor inventory and plan restocking.
                </p>
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
        // Income Chart
        const incomeCtx = document.getElementById('incomeChart').getContext('2d');
        new Chart(incomeCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($incomeLabels) ?>,
                datasets: [{
                    label: 'Income (Frw)',
                    data: <?= json_encode($incomeData) ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true }
        });

        // Best-Selling Products Chart
        const bestCtx = document.getElementById('bestChart').getContext('2d');
        new Chart(bestCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($bestLabels) ?>,
                datasets: [{
                    label: 'Units Sold',
                    data: <?= json_encode($bestProducts) ?>,
                    backgroundColor: '#28a745'
                }]
            },
            options: { responsive: true }
        });

        // Stock Chart
        const stockCtx = document.getElementById('stockChart').getContext('2d');
        new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($stockLabels) ?>,
                datasets: [{
                    label: 'Stock Quantity',
                    data: <?= json_encode($stockData) ?>,
                    backgroundColor: '#ffc107'
                }]
            },
            options: { responsive: true, indexAxis: 'y' }
        });
    </script>
</body>
</html>
