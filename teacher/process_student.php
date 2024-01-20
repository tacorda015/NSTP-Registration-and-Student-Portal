<?php
$user_account_id = $_POST['user_account_id'];

// Store the user_account_id in a session variable
session_start();
$_SESSION['student_data'] = $user_account_id;

// Redirect the user to view the student's details
header('Location: view_student.php');
exit();
?>
