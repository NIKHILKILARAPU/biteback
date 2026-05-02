<?php
include '../config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
    $password = $_POST["password"] ?? '';

    $stmt = mysqli_prepare($conn, 'SELECT mobile, password FROM surplus WHERE name = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $mobile, $passwordHash);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!empty($mobile)) {
        if (password_verify($password, $passwordHash) || $password === $passwordHash) {
            $_SESSION["username"] = $username;
            $_SESSION["mobile"] = $mobile;
            header("Location: list_surplus.html");
            exit;
        }
    }

    header("Location: list_surplus_login.html?error=invalid");
    exit;
}

header("Location: list_surplus_login.html");
exit;
?>
