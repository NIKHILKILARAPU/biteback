<?php
include 'config.php';

// Suppress notices and warnings for JSON output
error_reporting(E_ERROR | E_PARSE);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$food_id = isset($data['food_id']) ? intval($data['food_id']) : 0;
$customer_id = intval($_SESSION['customer_id']);

if ($food_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid food item']);
    exit();
}

$stmt = mysqli_prepare($conn, 'SELECT id, quantity FROM surplus_food WHERE id = ? AND quantity > 0 AND expiry_time > NOW()');
mysqli_stmt_bind_param($stmt, 'i', $food_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $foodIdResult, $foodQuantity);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (empty($foodIdResult)) {
    echo json_encode(['success' => false, 'message' => 'Food not available or has expired']);
    exit();
}

$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    food_id INT NOT NULL,
    order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'completed') DEFAULT 'pending',
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (food_id) REFERENCES surplus_food(id)
) ENGINE=InnoDB";
mysqli_query($conn, $sql);

mysqli_begin_transaction($conn);
$order_stmt = mysqli_prepare($conn, 'INSERT INTO orders (customer_id, food_id) VALUES (?, ?)');
mysqli_stmt_bind_param($order_stmt, 'ii', $customer_id, $food_id);
$qty_stmt = mysqli_prepare($conn, 'UPDATE surplus_food SET quantity = quantity - 1 WHERE id = ? AND quantity > 0');
mysqli_stmt_bind_param($qty_stmt, 'i', $food_id);

if (mysqli_stmt_execute($order_stmt) && mysqli_stmt_execute($qty_stmt)) {
    mysqli_commit($conn);
    mysqli_stmt_close($order_stmt);
    mysqli_stmt_close($qty_stmt);
    echo json_encode(['success' => true, 'message' => 'Order placed successfully']);
} else {
    mysqli_rollback($conn);
    mysqli_stmt_close($order_stmt);
    mysqli_stmt_close($qty_stmt);
    echo json_encode(['success' => false, 'message' => 'Failed to place order']);
}
?>"