<?php
include '../config.php';

$sql = "SELECT * FROM applicants";
$result = mysqli_query($conn, $sql);

$applicants = [];

if($result){
    while($row = mysqli_fetch_assoc($result)){
        $applicants[] = [
            "id" => $row["id"],
            "username" => $row["username"],
            "mobile" => $row["mobile"]
        ];
    }
}

header("Content-Type: application/json");
echo json_encode($applicants);
?>