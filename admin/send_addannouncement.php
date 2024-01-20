<?php
session_start();
ob_start();
include('../connection.php');
$con = connection();

$user_data = $_SESSION['user_data'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected recipient value from the form
    $recipient = $_POST["recipient"];
    $sender_id = $user_data['user_account_id'];

    // Fetch the current maximum announcement_batch value from the database
    $maxBatchQuery = "SELECT MAX(announcement_batch) AS max_batch FROM announcementtable";
    $maxBatchResult = $con->query($maxBatchQuery);
    $maxBatchData = $maxBatchResult->fetch_assoc();
    $currentBatch = $maxBatchData['max_batch'] ?? 0; // Set the initial batch number to 0 if no previous batches exist
    $currentBatch++;
    if ($recipient == "all") {
        // Query all recipients
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE (role_account_id = 2 OR role_account_id = 3) AND user_status = 'active'";
        $reciever = 'All';
    } elseif ($recipient == "teachers") {
        // Query all teachers
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE role_account_id = 3 AND component_name = 'CWTS'";
        $reciever = 'All ' . $recipient;
    } elseif ($recipient == "trainers") {
        // Query all trainers
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE role_account_id = 3 AND component_name = 'ROTC'";
        $reciever = 'All ' . $recipient;
    } elseif ($recipient == "students") {
        // Query all students
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE role_account_id = 2 AND user_status = 'active'";
        $reciever = $recipient;
        $reciever = 'All ' . $recipient;
    } elseif ($recipient == "rotcgroups") {
        // Get the selected group ID from the form
        $group_id = $_POST["rotcgroup"];
        $group_name_query = "SELECT group_name FROM grouptable WHERE group_id = $group_id";
        $group_name_result = $con->query($group_name_query);
        $group_name_data = mysqli_fetch_assoc($group_name_result);
        // Query recipients based on the selected group
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE group_id = $group_id AND component_name = 'ROTC'";
        $reciever = 'ROTC Group: ' . $group_name_data['group_name'];
    } elseif ($recipient == "cwtsgroups") {
        // Get the selected group ID from the form
        $group_id = $_POST["cwtsgroup"];
        $group_name_query = "SELECT group_name FROM grouptable WHERE group_id = $group_id";
        $group_name_result = $con->query($group_name_query);
        $group_name_data = mysqli_fetch_assoc($group_name_result);
        // Query recipients based on the selected group
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE group_id = $group_id AND component_name = 'CWTS'";
        $reciever = 'CWTS Group: ' . $group_name_data['group_name'];
    } elseif ($recipient == "specific") {
        $specificRecipients = $_POST['hiddenspecificRecipients'];
        $selectedRecipients = json_decode($specificRecipients, true);
        $subject = $con->real_escape_string($_POST["subject"]);
        $message = $con->real_escape_string($_POST["message"]);
    
        // Array to store successful and unsuccessful recipients
        $successfulRecipients = [];
        $unsuccessfulRecipients = [];
    
        // Instantiate PHPMailer
        $mail = new PHPMailer(true);
    
        // Configure SMTP settings
        $mail->isSMTP();
        // Enable SMTP keep-alive
        $mail->SMTPKeepAlive = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eduardo.tacorda@cvsu.edu.ph';
        // $mail->Password = 'yaqrenxtmsqikfbt';
        $mail->Password = 'dblrcnnlxmvmsejq'; // New 9-6-23
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
    
        $userName = 'Admin'; // User's name
        $userEmail = 'eduardo.tacorda@cvsu.edu.ph'; // User's email address

        // Set the sender
        $mail->setFrom($userEmail, $userName);
        // Set sender
        // $mail->setFrom('aileenbatiancila008@gmail.com');
        // $mail->setFrom('eduardotacorda17@gmail.com');
    
        // Prepare the email content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;
    
        // Iterate through the selected recipients using foreach
        foreach ($selectedRecipients as $recipient) {
            $userAccountId = $recipient['user_account_id'];
            $emailAddress = $recipient['email_address'];
            $fullName = $recipient['full_name'];
    
            // Check if the email address is valid
            if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                // Valid email address, add it as a recipient
                $mail->addAddress($emailAddress);
    
                // Save email details in the database
                $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, announcement_batch) VALUES ('$sender_id', '$userAccountId', '$emailAddress', '$subject', '$message', '$fullName', NOW(), '$currentBatch')";
                $con->query($saveQuery);
    
                // Add recipient to successful recipients array
                $successfulRecipients[] = $emailAddress;
            } else {
                // Invalid email address, handle the error accordingly
                $unsuccessfulRecipients[] = array(
                    "fullname" => $fullName,
                    "email" => $emailAddress,
                    "reason" => "Invalid email address"
                );
            }
        }
    
        try {
            // Send the email to all recipients
            $isEmailSent = $mail->send();
    
            if ($isEmailSent) {
                $response = array(
                    'status' => 'success',
                    'message' => "Announcement has been sent successfully"
                );  
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
                );
            }
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Email could not be sent. Error: ' . $e->getMessage()
            );
        }
    
        if (count($unsuccessfulRecipients) > 0) {
            $unsuccessBatch = $currentBatch++;
            $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, announcement_batch) VALUES ";
            $valueStrings = array();
            foreach ($unsuccessfulRecipients as $recipient) {
                $unsuccessBatch++;
                $email = $con->real_escape_string($recipient["email"]);
                $full_name = $con->real_escape_string($recipient["fullname"]);
                $reason = $con->real_escape_string($recipient["reason"]);
                $errorMessage = "Fullname: " . $full_name . "<br>Reason: " . $reason . "<br>";  // Initialize the error message inside the loop
                $valueStrings[] = "('$sender_id', 0, '$email', 'Unsuccessful send Announcement', '$errorMessage', '$sender_id', NOW(), '$unsuccessBatch')";
            }
            $saveQuery .= implode(", ", $valueStrings);
            $con->query($saveQuery);
        }
        
    }
    
    if (!empty($query)) {
        $result = $con->query($query);

        // Send the announcement to each user
        $subject = $con->real_escape_string($_POST["subject"]);
        $message = $con->real_escape_string($_POST["message"]);

        // Array to store successful and unsuccessful recipients
        $successfulRecipients = [];
        $unsuccessfulRecipients = [];
    
        // Instantiate PHPMailer
        $mail = new PHPMailer(true);
    
        // Configure SMTP settings
        $mail->isSMTP();
        // Enable SMTP keep-alive
        $mail->SMTPKeepAlive = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eduardo.tacorda@cvsu.edu.ph';
        // $mail->Password = 'yaqrenxtmsqikfbt';
        $mail->Password = 'dblrcnnlxmvmsejq'; // New 9-6-23
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        $userName = 'Admin'; // User's name
        $userEmail = 'eduardo.tacorda@cvsu.edu.ph'; // User's email address

        // Set the sender
        $mail->setFrom($userEmail, $userName);
    
        // Prepare the email content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $userAccountId = $row["user_account_id"];
                $fullName = $row["full_name"];

                $emailAddress = $row["email_address"];
                
                // Check if the email address is valid
                if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                    // Valid email address, add it as a recipient
                    $mail->addAddress($emailAddress);
        
                    // Save email details in the database
                    $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, announcement_batch) VALUES ('$sender_id', '$userAccountId', '$emailAddress', '$subject', '$message', '$fullName', NOW(), '$currentBatch')";
                    $con->query($saveQuery);
        
                    // Add recipient to successful recipients array
                    $successfulRecipients[] = $emailAddress;
                } else {
                    // Invalid email address, handle the error accordingly
                    $unsuccessfulRecipients[] = array(
                        "email" => $emailAddress,
                        "fullname" => $fullName,
                        "reason" => "Invalid email address"
                    );
                }
                
            }
        }

        try {
            // Send the email to all recipients
            $isEmailSent = $mail->send();
    
            if ($isEmailSent) {
                $response = array(
                    'status' => 'success',
                    'message' => "Announcement has been sent successfully"
                ); 
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
                );
            }
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Email could not be sent. Error: ' . $e->getMessage()
            );
        }
    
        if (count($unsuccessfulRecipients) > 0) {
            $unsuccessBatch = $currentBatch++;
            $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, announcement_batch) VALUES ";
            $valueStrings = array();
            foreach ($unsuccessfulRecipients as $recipient) {
                $unsuccessBatch++;
                $email = $con->real_escape_string($recipient["email"]);
                $full_name = $con->real_escape_string($recipient["fullname"]);
                $reason = $con->real_escape_string($recipient["reason"]);
                // When saving the error message to the database
                $errorMessage = "Fullname: " . $full_name . "\n"
                . "Email: " . $email . "\n"
                . "Reason: " . $reason;
  // Initialize the error message inside the loop
                $valueStrings[] = "('$sender_id', 0, '$email', 'Unsuccessful send Announcement', '$errorMessage', '$sender_id', NOW(), '$unsuccessBatch')";
            }
            $saveQuery .= implode(", ", $valueStrings);
            $con->query($saveQuery);
        }
    }
    echo json_encode($response);
    }
?>
