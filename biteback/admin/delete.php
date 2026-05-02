<?php
include '../config.php';
session_start();

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {

    $id = intval($_GET["id"]);

    $sql = "DELETE FROM surplus WHERE id = $id";

    if (mysqli_query($conn, $sql)) {

        echo json_encode([
            "status" => "success",
            "message" => "Surplus deleted successfully"
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => mysqli_error($conn)
        ]);
    }

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request"
    ]);
}
?>