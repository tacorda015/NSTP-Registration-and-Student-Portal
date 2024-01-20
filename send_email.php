<?php
include('connection.php');
$con = connection();
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './admin/phpmailer/src/Exception.php';
require './admin/phpmailer/src/PHPMailer.php';
require './admin/phpmailer/src/SMTP.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the email details from the form
    $recipient = $_POST['recipient'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer();

    try {
        // Configure the PHPMailer settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'nstpportal@gmail.com';  // Replace with your email
        $mail->Password = 'yaqrenxtmsqikfbt';  // Replace with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Set the email details
        $mail->setFrom('nstpportal@gmail.com', 'Eduardo');  // Replace with your email and name
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Send the email
        if ($mail->send()) {
            // Email sent successfully
            $response = array(
                'status' => 'success',
                'message' => 'Email sent successfully!'
            );
        } else {
            // Failed to send email
            $response = array(
                'status' => 'error',
                'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
            );
        }
    } catch (Exception $e) {
        // Exception occurred
        $response = array(
            'status' => 'error',
            'message' => 'Email could not be sent. Please try again.'
        );
    }

    // Return the response as JSON
    echo json_encode($response);
}
?>