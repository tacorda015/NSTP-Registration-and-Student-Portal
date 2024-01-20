<?php
include('./connection.php');
$con = connection();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'admin/phpmailer/src/Exception.php';
require 'admin/phpmailer/src/PHPMailer.php';
require 'admin/phpmailer/src/SMTP.php';

$checkSendEmailQuery = "SELECT * FROM useraccount WHERE sendEmail = 1 LIMIT 50";
$checkSendEmailResult = $con->query($checkSendEmailQuery);

if ($checkSendEmailResult->num_rows > 0) {
    // Create a PHPMailer instance
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nstpportal@gmail.com';
    $mail->Password = 'jiwxkeyahwlynktz';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    // $mail->SMTPSecure = 'tls';
    // $mail->Port = 587;
    
    // Set From address
    $mail->setFrom('nstpportal@gmail.com', 'Temporary Password');
    
    while ($row = $checkSendEmailResult->fetch_assoc()) {
        $full_name = $row['full_name'];
        $temporary_password = base64_decode($row['password']);
        
        // Compose the email
        $mail->addAddress($row['email_address'], $full_name);
        $mail->isHTML(true);
        $mail->Subject = 'Your Temporary Password';
        $mail->Body = "Hello $full_name,<br><br>Your temporary password is: $temporary_password<br><br>Please log in at <a href='https://ccat-nstp.online/portal.php'>ccat-nstp.online</a> with this password and change it immediately for security reasons.<br><br>Best regards,<br>CCAT NSTP Team";

        
        // Send the email
        if ($mail->send()) {
            // Update the database to mark the email as sent (set sendEmail = 1) for this user
            $updateEmailSentQuery = "UPDATE useraccount SET sendEmail = 0 WHERE user_account_id = {$row['user_account_id']}";
            $con->query($updateEmailSentQuery);
            
            echo "<script>Email sent to $full_name at {$row['email_address']}</script>";
            
        } else {
            echo "<script>Email could not be sent to $full_name at {$row['email_address']} - Error: " . $mail->ErrorInfo . "</script>";
        }
        
        // Clear recipients and reset the email object for the next user
        $mail->clearAddresses();
    }
    
    // Close the database connection
    $con->close();
}

?>
