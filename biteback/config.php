<?php
$host = "sql304.infinityfree.com";
$user = "if0_42985208";
$pass = "ni1kh2il3";
$db = "if0_4295208_biteback";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>