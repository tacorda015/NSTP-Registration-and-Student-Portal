<?php
session_start();
include('../connection.php');
$con = connection();
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $teacher_id = $_POST['teacher_id'];
    $teacher_uniquenumber = $_POST['teacher_uniquenumber'];
    $firstname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["firstname"])));
    $middlename = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["middlename"])));
    $surname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["surname"])));
    $user_status = $_POST['user_status'];
    $course = $_POST['course'];
    $teacher_contactnumber = $_POST['update_teacher_contactnumber'];
    $teacher_email = $_POST['update_teacher_email'];

    $modifymiddlename = strtoupper(substr($middlename, 0, 1));
    $full_name = $firstname . ' ' . $modifymiddlename . '. ' . $surname;
    

    // Update the teacher record in the database
    $update_query = "UPDATE teachertable SET teacher_name='$full_name', teacher_contactnumber='$teacher_contactnumber', teacher_email='$teacher_email' WHERE teacher_uniquenumber='$teacher_uniquenumber'";
    $update_result = mysqli_query($con, $update_query);

    // Update the useraccount record in the database
    $useraccount_update_query = "UPDATE useraccount SET firstname='$firstname', middlename='$middlename', surname='$surname', full_name='$full_name', user_status='$user_status', course='$course', email_address='$teacher_email', contactNumber='$teacher_contactnumber' WHERE student_number='$teacher_uniquenumber'";
    $useraccount_update_result = mysqli_query($con, $useraccount_update_query);

    if ($update_result && $useraccount_update_result) {
        // Return success response
        $response = array(
            'status' => 'success',
            'message' => 'Teacher record updated successfully.'
        );
    } else {
        // Return error response
        $response = array(
            'status' => 'error',
            'message' => 'An error occurred while updating the teacher record.'
        );
    }
    
    // Send the JSON response
    echo json_encode($response);
}
?>
