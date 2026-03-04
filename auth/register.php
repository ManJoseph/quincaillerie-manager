<?php
session_start();
require_once '../utils/db.php';

function showError($msg) {
    echo '<!DOCTYPE html><html><head><title>Sign Up Error</title><link rel="stylesheet" href="../css/style.css"></head><body>';
    echo '<div class="container"><h2>Sign Up Error</h2><p style="color:#dc3545;">' . htmlspecialchars($msg) . '</p><a href="../app/index.php" class="btn">Back to Home</a></div>';
    echo '</body></html>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!$username || !$email || !$company || !$password || !$confirm) showError('All fields are required.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) showError('Invalid email format.');
    if (strlen($password) < 6) showError('Password must be at least 6 characters.');
    if ($password !== $confirm) showError('Passwords do not match.');
    // Check if email exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) showError('Email already registered. Please log in.');
    $stmt->close();
    // Create/find company
    $stmt = $conn->prepare('SELECT id FROM companies WHERE name = ?');
    $stmt->bind_param('s', $company);
    $stmt->execute();
    $stmt->bind_result($company_id);
    if ($stmt->fetch()) {
        $stmt->close();
    } else {
        $stmt->close();
        $stmt2 = $conn->prepare('INSERT INTO companies (name, created_at) VALUES (?, NOW())');
        $stmt2->bind_param('s', $company);
        $stmt2->execute();
        $company_id = $stmt2->insert_id;
        $stmt2->close();
    }
    // Create user
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (username, email, password, company_id, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->bind_param('sssi', $username, $email, $password_hash, $company_id);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['username'] = $username;
        $_SESSION['company_id'] = $company_id;
        header('Location: ../app/dashboard.php');
        exit();
    } else {
        showError('Registration failed. Please try again.');
    }
    $stmt->close();
    } else {
    header('Location: ../app/index.php');
    exit();
    } 