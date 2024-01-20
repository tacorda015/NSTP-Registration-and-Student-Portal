<?php
session_start();
ob_start();
include('../connection.php');
$con = connection();
date_default_timezone_set('Asia/Manila');

$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = {$user_id}";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
$user_group_id = $useraccount_data['group_id'];
$user_full_name = $useraccount_data['full_name'];
// echo"$user_group_id";
// echo"$user_full_name";
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected recipient value from the form
    $recipient = $_POST["recipient"];
    // Get the activity date and time from the form
    $activityDate = $_POST["activity_date"];
    $activityTime = $_POST["activity_time"];
    $activityEndTime = $_POST["activity_end_time"];

    // // Combine the date and time values into a single DateTime object
    // $startDateTime = new DateTime($activityDate . ' ' . $activityTime);
    // $endDateTime = new DateTime($activityDate . ' ' . $activityEndTime);
    // $activity_datetime_range = $startDateTime . '-'. $endDateTime;

    // // Format the activity date and time for display in the message
    // $formattedActivityDateTime = $startDateTime->format('Y-m-d H:i:s');

    $startDateTime = new DateTime($activityDate . ' ' . $activityTime);
    $endDateTime = new DateTime($activityDate . ' ' . $activityEndTime);

    $formattedActivityDateTime = $startDateTime->format('Y-m-d H:i:s');
    $formattedActivityEndTime = $endDateTime->format('H:i:s');

    $activityDateTimeRange = $formattedActivityDateTime . '-' . $formattedActivityEndTime;


    $sender_id = $user_data['user_account_id'];

    // Fetch the current maximum announcement_batch value from the database
    $maxBatchQuery = "SELECT MAX(announcement_batch) AS max_batch FROM announcementtable";
    $maxBatchResult = $con->query($maxBatchQuery);
    $maxBatchData = $maxBatchResult->fetch_assoc();
    $currentBatch = $maxBatchData['max_batch'] ?? 0; // Set the initial batch number to 0 if no previous batches exist
    $currentBatch++;
    if ($recipient == "all") {
        // Query all recipients
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE (role_account_id = 2 AND group_id = $user_group_id) AND user_status = 'active'";
        $reciever = 'All';
    } elseif ($recipient == "scheduling") {
        // Query all recipients
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE (role_account_id = 2 AND group_id = $user_group_id) AND user_status = 'active'";
        $reciever = 'All';
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
        $mail->Password = 'dblrcnnlxmvmsejq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
    
        $userName = $user_full_name; // User's name
        $userEmail = 'eduardo.tacorda@cvsu.edu.ph'; // User's email address

        // Set the sender
        $mail->setFrom($userEmail, $userName);
        // Set sender
    
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
                $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, activity_scheduled,  announcement_batch) VALUES ('$sender_id', '$userAccountId', '$emailAddress', '$subject', '$message', '$fullName', NOW(), '$activityDateTimeRange', '$currentBatch')";
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
        if($recipient == "scheduling"){
            $messages = $message . "\n\nActivity Schedule: " . $activityDateTimeRange;
        }else{
            $messages = $message;
        }
        

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
        $mail->Password = 'dblrcnnlxmvmsejq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        $userName = $user_full_name; // User's name
        $userEmail = 'nstpportal@gmail.com'; // User's email address

        // Set the sender
        $mail->setFrom($userEmail, $userName);
    
        // Prepare the email content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $messages;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $userAccountId = $row["user_account_id"];
                $fullName = $row["full_name"];

                $emailAddress = $row["email_address"];
                
                // Check if the email address is valid
                if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                    // Valid email address, add it as a recipient
                    $mail->addAddress($emailAddress);

                    if ($recipient == "all") {
                        // Save email details in the database
                        $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, activity_scheduled, announcement_batch) VALUES ('$sender_id', '$userAccountId', '$emailAddress', '$subject', '$message', '$fullName', NOW(), NULL, '$currentBatch')";
                        $con->query($saveQuery);
                    } elseif ($recipient == "scheduling") {
                        // Save email details in the database
                        $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, activity_scheduled, announcement_batch) VALUES ('$sender_id', '$userAccountId', '$emailAddress', '$subject', '$messages', '$fullName', NOW(), '$activityDateTimeRange', '$currentBatch')";
                        $con->query($saveQuery);
                    }
        
                    // // Save email details in the database
                    // $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, activity_scheduled, announcement_batch) VALUES ('$sender_id', '$userAccountId', '$emailAddress', '$subject', '$messages', '$fullName', NOW(), '$activityDateTimeRange', '$currentBatch')";
                    // $con->query($saveQuery);
        
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
                if($recipient == "scheduling"){
                    // Save Data to scheduletable
                    $schedule_query = "INSERT INTO scheduletable (group_id, schedule_date, schedule_start, schedule_end) VALUES ('$user_group_id', '$activityDate', '$activityTime', '$activityEndTime')";
                    $con->query($schedule_query);
                }
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