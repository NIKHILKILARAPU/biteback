<?php
include '../config.php';

$id = intval($_GET['id']); // secure numeric id

$result = mysqli_query($conn, "DELETE FROM applicants WHERE id=$id");

if($result){
    echo "Applicant Rejected Successfully";
}else{
    echo "Failed to Reject Applicant";
}
?>