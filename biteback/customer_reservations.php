<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['customer_id'])) {
    header("Location: customer_login.html");
    exit();
}

include 'config.php';
$customer_id = $_SESSION['customer_id'];

$sql = "SELECT o.*, sf.food_name, sf.price, sf.discounted_price, sf.location, sf.pickup_time, sf.image, sf.name as seller_name, sf.mobile as seller_mobile
        FROM orders o
        JOIN surplus_food sf ON o.food_id = sf.id
        WHERE o.customer_id = $customer_id
        ORDER BY o.order_time DESC";

$result = mysqli_query($conn, $sql);
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - BiteBack</title>
    <link rel="stylesheet" href="customer_login.css">
    <style>
        .reservations {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .reservation-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .reservation-card:hover {
            transform: translateY(-5px);
        }
        .reservation-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .reservation-body {
            padding: 15px;
        }
        .food-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .price {
            margin-bottom: 10px;
        }
        .old-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 10px;
        }
        .new-price {
            color: green;
            font-weight: bold;
        }
        .info {
            margin-bottom: 5px;
            font-size: 14px;
        }
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-size: 12px;
            text-transform: uppercase;
        }
        .status.pending { background: orange; }
        .status.confirmed { background: green; }
        .status.completed { background: blue; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="BiteBack Logo" class="logo_img">
            <a class="logo_name" href="index.html"><span style="color: black;">Bite</span><span style="color: green;">Back</span></a>
        </div>
        <div class="menu_toggle" onclick="toggleMenu()">☰</div>
        <div class="nav" id="navMenu">
            <a href="find_food/find_food_page.php" class="nav1">Find Food</a>
            <a href="customer_logout.php" class="nav2">Logout</a>
        </div>
    </div>
    <div class="content">
        <h1>🍽️ My Reservations</h1>
        <p>Your reserved food items</p>
        <div class="reservations">
            <?php if (empty($orders)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                    <h2>No reservations yet 😔</h2>
                    <p><a href="find_food/find_food_page.php" style="color: green;">Find some food to reserve!</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="reservation-card">
                        <img src="<?php echo htmlspecialchars($order['image']); ?>" alt="<?php echo htmlspecialchars($order['food_name']); ?>">
                        <div class="reservation-body">
                            <div class="food-name"><?php echo htmlspecialchars($order['food_name']); ?></div>
                            <div class="price">
                                <span class="old-price">₹<?php echo $order['price']; ?></span>
                                <span class="new-price">₹<?php echo $order['discounted_price']; ?></span>
                            </div>
                            <div class="info">👤 <?php echo htmlspecialchars($order['seller_name']); ?></div>
                            <div class="info">📞 <?php echo htmlspecialchars($order['seller_mobile']); ?></div>
                            <div class="info">📍 <?php echo htmlspecialchars($order['location']); ?></div>
                            <div class="info">⏰ Pickup: <?php echo htmlspecialchars($order['pickup_time']); ?></div>
                            <div class="info">🕒 Ordered: <?php echo date('M d, Y H:i', strtotime($order['order_time'])); ?></div>
                            <div class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
<script>
function toggleMenu() {
    document.getElementById("navMenu").classList.toggle("active");
}
</script>
</html>