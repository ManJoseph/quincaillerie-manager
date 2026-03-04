<?php
// forgot_password.php
require_once '../utils/db.php';
require_once '../utils/mailer.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare('SELECT id, name FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $conn->query("INSERT INTO password_resets (user_id, token, expires) VALUES ({$row['id']}, '$token', '$expires')");
        $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
        sendMail($email, 'Password Reset', "Hello {$row['name']},\n\nReset your password: $resetLink\n\nThis link expires in 1 hour.");
        $message = 'If your email is registered, a password reset link has been sent.';
    } else {
        $message = 'If your email is registered, a password reset link has been sent.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <form method="post" action="forgot_password.php">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
        <a href="login.php" class="btn">⬅ Back to Login</a>
    </div>
</body>
</html>
