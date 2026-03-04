<?php
session_start();
session_unset();
session_destroy();
header("Location: ../app/index.php");
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --accent: #22d3ee;
            --bg-light: #f4f6fa;
            --bg-dark: #18181b;
            --card-light: #fff;
            --card-dark: #23272f;
            --text-light: #222;
            --text-dark: #f3f4f6;
            --border-radius: 14px;
            --shadow: 0 4px 24px rgba(0,0,0,0.08);
            --transition: 0.25s cubic-bezier(.4,0,.2,1);
        }
        html[data-theme='dark'] {
            --bg: var(--bg-dark);
            --card: var(--card-dark);
            --text: var(--text-dark);
        }
        html[data-theme='light'], html {
            --bg: var(--bg-light);
            --card: var(--card-light);
            --text: var(--text-light);
        }
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            transition: background var(--transition), color var(--transition);
        }
        .form-container {
            background: var(--card);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 32px 28px 36px 28px;
            margin: 60px auto 32px auto;
            max-width: 420px;
            transition: background var(--transition), box-shadow var(--transition);
        }
        .btn {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 12px 28px;
            border: none;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(37,99,235,0.08);
            transition: background var(--transition), box-shadow var(--transition), transform var(--transition);
            cursor: pointer;
            margin: 10px 0;
            display: inline-block;
            letter-spacing: 0.02em;
        }
        .btn:hover, .btn:focus {
            background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary) 100%);
            box-shadow: 0 6px 24px rgba(37,99,235,0.15);
            transform: translateY(-2px) scale(1.04);
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
        @media (max-width: 900px) {
            .form-container {
                max-width: 98vw;
                padding: 12px 6px 18px 6px;
            }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Logout Successful</h2>
        <p>You have been logged out. Thank you for using our service.</p>
        <a href="../app/index.php" class="btn">Return to Home</a>
    </div>
</body>
</html>
