<?php
session_start();
require_once "../utils/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$email || !$password) {
        echo "Email and password are required.";
        exit();
    }
    $stmt = $conn->prepare("SELECT id, username, password, company_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $username, $hashed_password, $company_id);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['company_id'] = $company_id;
            echo "OK";
            exit();
        } else {
            echo "Invalid password.";
            exit();
        }
    } else {
        echo "User not found.";
        exit();
    }
} else {
    header("Location: ../app/index.php");
    exit();
}
