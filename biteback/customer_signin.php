<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: customer_signin.html");
    exit;
}

$username = mysqli_real_escape_string($conn, trim($_POST['username']));
$email = mysqli_real_escape_string($conn, trim($_POST['email']));
$mobile = mysqli_real_escape_string($conn, trim($_POST['mobile_number']));
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($password !== $confirm_password) {
    header("Location: customer_signin.html?error=password");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: customer_signin.html?error=email");
    exit;
}

if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    header("Location: customer_signin.html?error=mobile");
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "CREATE TABLE IF NOT EXISTS customers(
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    mobile VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL
)";

mysqli_query($conn, $sql);

$stmt = mysqli_prepare($conn, 'INSERT INTO customers(username, email, mobile, password) VALUES (?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $mobile, $password_hash);
if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: customer_login.html?registered=1");
    exit;
} else {
    mysqli_stmt_close($stmt);
    header("Location: customer_signin.html?error=duplicate");
    exit;
}
?>