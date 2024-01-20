<?php
include('../connection.php');
$con = connection();

if (isset($_POST['teacher_id'])) {
    $teacher_id = $_POST['teacher_id'];
    $teacher_query = "SELECT teacher_uniquenumber FROM teachertable WHERE teacher_id = '$teacher_id'";
    $teacher_result = mysqli_query($con, $teacher_query);
    
    if ($teacher_result && mysqli_num_rows($teacher_result) > 0) {
        $row = mysqli_fetch_assoc($teacher_result);
        $teacher_uniquenumber = $row['teacher_uniquenumber'];

        // Perform the deletion operation
        // Here you can use your existing code to delete the teacher record from the database

        // Example code:
        $delete_query = "DELETE FROM teachertable WHERE teacher_id='$teacher_id'";
        $result = mysqli_query($con, $delete_query);

        $useraccount_update_query = "DELETE FROM useraccount WHERE student_number='$teacher_uniquenumber'";
        $result = mysqli_query($con, $useraccount_update_query);

        if ($result) {
            echo 'success';
        } else {
            echo mysqli_error($con);
        }
    } else {
        echo 'Teacher not found';
    }
} else {
    echo 'Invalid request';
}
?>
