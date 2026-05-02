<?php
session_start();
$lat = $_GET['lat'] ?? '';
$lng = $_GET['lng'] ?? '';

if ($lat && $lng) {
    $_SESSION['lat'] = $lat;
    $_SESSION['lng'] = $lng;
    header("Location: find_food/find_food_page.php");
    exit;
}

header("Location: index.html");
exit;
?>