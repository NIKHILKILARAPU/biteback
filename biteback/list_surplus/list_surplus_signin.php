<?php
include '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list_surplus_signin.html");
    exit;
}

$name = mysqli_real_escape_string($conn, trim($_POST['name']));
$mobile = mysqli_real_escape_string($conn, trim($_POST['mobile_number']));
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($password !== $confirm_password) {
    header("Location: list_surplus_signin.html?error=password");
    exit;
}

if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    header("Location: list_surplus_signin.html?error=mobile");
    exit;
}

$sql = "CREATE TABLE IF NOT EXISTS applicants(
    id int primary key auto_increment,
    username varchar(255) not null,
    mobile varchar(20) not null,
    password varchar(255) not null
)";

mysqli_query($conn, $sql);
$stmt = mysqli_prepare($conn, 'INSERT INTO applicants(username, mobile, password) VALUES (?, ?, ?)');
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
mysqli_stmt_bind_param($stmt, 'sss', $name, $mobile, $hashedPassword);
if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: list_surplus_login.html?registered=1");
    exit;
} else {
    mysqli_stmt_close($stmt);
    header("Location: list_surplus_signin.html?error=db");
    exit;
}
?>