<?php
$host = getenv('DB_HOST') ?: "localhost";
$dbname = getenv('DB_NAME') ?: "quincaillerie_manager";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
