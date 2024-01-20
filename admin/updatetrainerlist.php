<?php
session_start();
include('../connection.php');
$con = connection();
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $trainer_id = $_POST['trainer_id'];
    $trainer_uniquenumber = $_POST['trainer_uniquenumber'];
    $firstname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["firstname"])));
    $middlename = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["middlename"])));
    $surname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["surname"])));
    $user_status = $_POST['user_status'];
    $course = $_POST['course'];
    $trainer_contactnumber = $_POST['update_trainer_contactnumber'];
    $trainer_email = $_POST['update_trainer_email'];

    $modifymiddlename = strtoupper(substr($middlename, 0, 1));
    $full_name = $firstname . ' ' . $modifymiddlename . '. ' . $surname;
    

    // Update the teacher record in the database
    $update_query = "UPDATE trainertable SET trainer_name='$full_name', trainer_contactnumber='$trainer_contactnumber', trainer_email='$trainer_email' WHERE trainer_uniquenumber='$trainer_uniquenumber'";
    $update_result = mysqli_query($con, $update_query);

    // Update the useraccount record in the database
    $useraccount_update_query = "UPDATE useraccount SET firstname='$firstname', middlename='$middlename', surname='$surname', full_name='$full_name', user_status='$user_status', course='$course', email_address='$trainer_email', contactNumber='$trainer_contactnumber' WHERE student_number='$trainer_uniquenumber'";
    $useraccount_update_result = mysqli_query($con, $useraccount_update_query);

    if ($update_result && $useraccount_update_result) {
        // Return success response
        $response = array(
            'status' => 'success',
            'message' => 'Trainer record updated successfully.'
        );
    } else {
        // Return error response
        $response = array(
            'status' => 'error',
            'message' => 'An error occurred while updating the trainer record.'
        );
    }
    
    // Send the JSON response
    echo json_encode($response);
}
?>
