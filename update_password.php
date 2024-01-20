<?php
include('connection.php');
$con = connection();
session_start();
// Retrieve the new password and confirm password from the request
$newPassword = $_POST['newPassword'];
$confirmPassword = $_POST['confirmPassword'];

// Perform any necessary validation on the passwords
if ($newPassword !== $confirmPassword) {
    // Passwords do not match
    http_response_code(400); // Bad Request
    exit("Passwords do not match.");
}

$encodedPassword = base64_encode($newPassword);

// Update the password in the database
$userId = $_SESSION['user_id'];
$otpEmail = $_SESSION['email_address'];
$password_query = "UPDATE useraccount SET password = '$encodedPassword' WHERE email_address = '$otpEmail' AND user_account_id = '$userId'";
$password_result = mysqli_query($con, $password_query);

if ($password_result) {
    // Password update successful
    http_response_code(200); // OK
    exit("Password updated successfully!");
} else {
    // Password update failed
    http_response_code(500); // Internal Server Error
    exit("Failed to update password. Please try again later.");
}
