<?php
include('../connection.php');
$con = connection();
// delete_student.php

if (isset($_POST['student_number'])) {
    $student_number = $_POST['student_number'];

    // Perform the deletion operation
    // Here you can use your existing code to delete the student record from the database

    // Example code:
    $delete_query = "DELETE FROM useraccount WHERE student_number='$student_number'";
    $result = mysqli_query($con, $delete_query);

    if ($result) {
        echo 'success';
    } else {
        echo mysqli_error($con);
    }
} else {
    echo 'Invalid request';
}
?>
