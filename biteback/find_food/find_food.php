<?php
include '../config.php';

// Suppress notices and warnings for JSON output
error_reporting(E_ERROR | E_PARSE);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (isset($_GET['lat']) && isset($_GET['lng'])) {
    $_SESSION['lat'] = floatval($_GET['lat']);
    $_SESSION['lng'] = floatval($_GET['lng']);
    echo json_encode([
        'status' => 'ok'
    ]);
    exit();
}

if (!isset($_SESSION['lat']) || !isset($_SESSION['lng'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User location not found'
    ]);
    exit();
}

$user_lat = floatval($_SESSION['lat']);
$user_lng = floatval($_SESSION['lng']);

$radiusKm = 50;
$sql = "
SELECT *,
(
    6371 * acos(
        cos(radians($user_lat)) *
        cos(radians(latitude)) *
        cos(radians(longitude) - radians($user_lng)) +
        sin(radians($user_lat)) *
        sin(radians(latitude))
    )
) AS distance

FROM surplus_food
WHERE expiry_time > NOW()
HAVING distance < $radiusKm
ORDER BY distance ASC
";

$result = mysqli_query($conn, $sql);
$food_items = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $food_items[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'mobile' => $row['mobile'],
            'food_name' => $row['food_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'discounted_price' => $row['discounted_price'],
            'location' => $row['location'],
            'pickup_time' => $row['pickup_time'],
            'expiry_time' => $row['expiry_time'],
            'notes' => $row['notes'],
            'image' => $row['image'],
            'distance' => round($row['distance'], 2)
        ];
    }
}

if (empty($food_items)) {
    $fallbackSql = "
    SELECT *,
    (
        6371 * acos(
            cos(radians($user_lat)) *
            cos(radians(latitude)) *
            cos(radians(longitude) - radians($user_lng)) +
            sin(radians($user_lat)) *
            sin(radians(latitude))
        )
    ) AS distance

    FROM surplus_food
    WHERE expiry_time > NOW() AND quantity > 0
    ORDER BY distance ASC
    LIMIT 20
    ";

    $fallbackResult = mysqli_query($conn, $fallbackSql);
    if ($fallbackResult) {
        while ($row = mysqli_fetch_assoc($fallbackResult)) {
            $food_items[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'mobile' => $row['mobile'],
                'food_name' => $row['food_name'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'discounted_price' => $row['discounted_price'],
                'location' => $row['location'],
                'pickup_time' => $row['pickup_time'],
                'expiry_time' => $row['expiry_time'],
                'notes' => $row['notes'],
                'image' => $row['image'],
                'distance' => round($row['distance'], 2)
            ];
        }
    }
}

echo json_encode($food_items);
?>