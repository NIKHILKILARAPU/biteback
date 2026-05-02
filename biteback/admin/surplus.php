<?php
include '../config.php';
session_start();

$sql = "SELECT * FROM surplus";
$result = mysqli_query($conn, $sql);

$surplus = [];

if($result){
    while($row = mysqli_fetch_assoc($result)){
        $surplus[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "mobile" => $row['mobile']
        ];
    }
}

header("Content-Type: application/json");
echo json_encode($surplus);
?>