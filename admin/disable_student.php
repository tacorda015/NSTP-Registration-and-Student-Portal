<?php
include('../connection.php');
$con = connection();
// delete_student.php

if (isset($_POST['user_account_id'])) {
    $user_account_id = $_POST['user_account_id'];

    $delete_query = "UPDATE useraccount SET user_status = 'disabled' WHERE user_account_id = '$user_account_id'";
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
