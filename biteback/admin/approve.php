<?php
include '../config.php';
session_start();

$id = intval($_GET['id']); // secure id

$sql = "SELECT * FROM applicants WHERE id=$id";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){

    $row = mysqli_fetch_assoc($result);

    $name = $row['username'];
    $mobile = $row['mobile'];
    $password = $row['password'];

    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS surplus (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        password VARCHAR(255) NOT NULL
    )";

    mysqli_query($conn, $sql);

    // Correct table name here
    $sql = "INSERT INTO surplus (name, mobile, password)
            VALUES ('$name', '$mobile', '$password')";

    mysqli_query($conn, $sql);

    mysqli_query($conn, "DELETE FROM applicants WHERE id=$id");

    echo "Applicant Approved Successfully";

}else{
    echo "Applicant Not Found";
}
?>