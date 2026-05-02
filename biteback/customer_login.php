<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
    $password = $_POST["password"] ?? '';

    $stmt = mysqli_prepare($conn, 'SELECT id, username, password FROM customers WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $userId, $userName, $passwordHash);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!empty($userId)) {
        $isValid = false;
        if (password_verify($password, $passwordHash)) {
            $isValid = true;
        } elseif ($password === $passwordHash) {
            $isValid = true;
        }

        if ($isValid) {
            $_SESSION["username"] = $userName;
            $_SESSION["customer_id"] = $userId;
            header("Location: find_food/find_food_page.php");
            exit;
        }
    }

    header("Location: customer_login.html?error=invalid");
    exit;
}

header("Location: customer_login.html");
exit;
?>