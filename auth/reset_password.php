<?php
// reset_password.php
require_once '../utils/db.php';
$message = '';
$token = $_GET['token'] ?? '';
if ($token) {
    $stmt = $conn->prepare('SELECT user_id, expires FROM password_resets WHERE token = ?');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (strtotime($row['expires']) < time()) {
            $message = 'Reset link expired.';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password = '$password' WHERE id = {$row['user_id']}");
            $conn->query("DELETE FROM password_resets WHERE token = '$token'");
            $message = 'Password updated! You can now log in.';
        }
    } else {
        $message = 'Invalid reset link.';
    }
    $stmt->close();
} else {
    $message = 'No reset token provided.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if ($token && strpos($message, 'updated') === false && strpos($message, 'expired') === false): ?>
        <form method="post" action="reset_password.php?token=<?= htmlspecialchars($token) ?>">
            <input type="password" name="password" placeholder="New password" required>
            <button type="submit" class="btn">Update Password</button>
        </form>
        <?php endif; ?>
        <a href="login.php" class="btn">⬅ Back to Login</a>
    </div>
</body>
</html>
