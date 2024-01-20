<?php
include('../connection.php');
$con = connection();

if (isset($_POST['trainer_id'])) {
    $trainer_id = $_POST['trainer_id'];
    $trainer_query = "SELECT trainer_uniquenumber FROM trainertable WHERE trainer_id = '$trainer_id'";
    $trainer_result = mysqli_query($con, $trainer_query);
    
    if ($trainer_result && mysqli_num_rows($trainer_result) > 0) {
        $row = mysqli_fetch_assoc($trainer_result);
        $trainer_uniquenumber = $row['trainer_uniquenumber'];

        // Perform the deletion operation
        // Here you can use your existing code to delete the trainer record from the database

        // Example code:
        $delete_query = "DELETE FROM trainertable WHERE trainer_id='$trainer_id'";
        $result = mysqli_query($con, $delete_query);

        $useraccount_update_query = "DELETE FROM useraccount WHERE student_number='$trainer_uniquenumber'";
        $result = mysqli_query($con, $useraccount_update_query);

        if ($result) {
            echo 'success';
        } else {
            echo mysqli_error($con);
        }
    } else {
        echo 'Trainer not found';
    }
} else {
    echo 'Invalid request';
}
?>
