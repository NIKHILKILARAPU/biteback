<?php
include '../config.php';
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['mobile'])) {
    header("Location: list_surplus_login.html");
    exit;
}

$sql = "CREATE TABLE IF NOT EXISTS surplus_food (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    food_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    discounted_price DECIMAL(10, 2),
    location VARCHAR(255) NOT NULL,
    pickup_time DATETIME NOT NULL,
    expiry_time DATETIME NOT NULL,
    latitude DECIMAL(10, 7),
    longitude DECIMAL(10, 7),
    notes TEXT,
    image VARCHAR(255)
) ENGINE=InnoDB";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit;
}

$food_name = mysqli_real_escape_string($conn, trim($_POST['food_name']));
$quantity = intval($_POST['quantity']);
$price = floatval($_POST['price']);
$discounted_price = round(($price * 30) / 100, 2); // 30% of price as discounted price
$location = mysqli_real_escape_string($conn, trim($_POST['location']));
$pickup_time = mysqli_real_escape_string($conn, trim($_POST['pickup_time']));
$expiry_time = mysqli_real_escape_string($conn, trim($_POST['expiry_time']));
$notes = mysqli_real_escape_string($conn, trim($_POST['notes']));
$latitude = floatval($_POST['latitude']);
$longitude = floatval($_POST['longitude']);

if (!isset($_FILES['image']) || empty($_FILES['image']['name'])) {
    header("Location: list_surplus.html?error=image");
    exit;
}

$image = basename($_FILES['image']['name']);
$temp = $_FILES['image']['tmp_name'];
$image_name = time() . "_" . preg_replace('/[^A-Za-z0-9_.-]/', '_', $image);
$target_dir = "../uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}
$target_file = $target_dir . $image_name;
$image_path = "uploads/" . $image_name;

if (!move_uploaded_file($temp, $target_file)) {
    header("Location: list_surplus.html?error=upload");
    exit;
}

$stmt = mysqli_prepare($conn, 'INSERT INTO surplus_food (name, mobile, food_name, quantity, price, discounted_price, location, pickup_time, expiry_time, notes, image, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$sessionUser = mysqli_real_escape_string($conn, $_SESSION['username']);
$sessionMobile = mysqli_real_escape_string($conn, $_SESSION['mobile']);
mysqli_stmt_bind_param($stmt, 'ssiiddsssssdd', $sessionUser, $sessionMobile, $food_name, $quantity, $price, $discounted_price, $location, $pickup_time, $expiry_time, $notes, $image_path, $latitude, $longitude);
if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: list_surplus.html?success=1");
    exit;
} else {
    mysqli_stmt_close($stmt);
    header("Location: list_surplus.html?error=db");
    exit;
}
?>