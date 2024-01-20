<?php
session_start();
include('../connection.php');
$con = connection();

$user_data = $_SESSION['user_data'];
$sender_user_account_id = $user_data['user_account_id'];
$user_full_name = $user_data['full_name'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the event details from the POST data
    $group_id = htmlspecialchars($_POST["group_id"], ENT_QUOTES, 'UTF-8');
    $message_who = htmlspecialchars($_POST["message_who"], ENT_QUOTES, 'UTF-8');
    $message_what = htmlspecialchars($_POST["message_what"], ENT_QUOTES, 'UTF-8');
    $event_start_date = htmlspecialchars($_POST["event_start_date"], ENT_QUOTES, 'UTF-8');
    $event_end_date = htmlspecialchars($_POST["event_end_date"], ENT_QUOTES, 'UTF-8');
    $event_start_datetime = htmlspecialchars($_POST["event_start_datetime"], ENT_QUOTES, 'UTF-8');
    $event_end_datetime = htmlspecialchars($_POST["event_end_datetime"], ENT_QUOTES, 'UTF-8');
    $message_where = htmlspecialchars($_POST["message_where"], ENT_QUOTES, 'UTF-8');
    $message_notes = htmlspecialchars($_POST["message_notes"], ENT_QUOTES, 'UTF-8');

    $getLocationNameQuery = "SELECT * FROM activitylocation WHERE location_id = $message_where";
    $getLocationNameResult = $con->query($getLocationNameQuery);
    $getLocationNameData = $getLocationNameResult->fetch_assoc();
    $locationName = $getLocationNameData['location_name'];

    $activitydate = $event_start_date . ' ' . $event_start_datetime . ' - ' . $event_end_datetime;
    $messages = "Who: " . $message_who . "\nWhat: " . $message_what . "\nWhen: " . $activitydate . "\nWhere: " . $locationName . "\nNotes: " . $message_notes;

    // Fetch the current maximum announcement_batch value from the database
    $maxBatchQuery = "SELECT MAX(announcement_batch) AS max_batch FROM announcementtable";
    $maxBatchResult = $con->query($maxBatchQuery);
    $maxBatchData = $maxBatchResult->fetch_assoc();
    $currentBatch = $maxBatchData['max_batch'] ?? 0; // Set the initial batch number to 0 if no previous batches exist
    $currentBatch++;

    $user_query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE (role_account_id = 2 AND group_id = $group_id) AND user_status = 'active'";
    $sender_id = $sender_user_account_id;
    $subject = 'Incoming Schedule Activity';

    if (!empty($user_query)) {
        $user_result = $con->query($user_query);

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
    
        // Prepare the email content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $messages;

        if ($user_result->num_rows > 0) {
            while ($row = $user_result->fetch_assoc()) {
                $userAccountId = $row["user_account_id"];
                $fullName = $row["full_name"];
                $emailAddress = $row["email_address"];
                
                // Check if the email address is valid
                if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                    // Valid email address, add it as a recipient
                    $mail->addAddress($emailAddress);
                    
                    $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, activity_scheduled, announcement_batch) VALUES ('$sender_id', '$userAccountId', '$emailAddress', '$subject', '$messages', '$fullName', NOW(), NULL, '$currentBatch')";
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
                $schedule_query = "INSERT INTO scheduletable (group_id, location_id, schedule_title, schedule_date, schedule_date_end , schedule_start, schedule_end) VALUES ($group_id, '$message_where', '$message_what', '$event_start_date', '$event_end_date', '$event_start_datetime', '$event_end_datetime')";
                $con->query($schedule_query);

                $updateLocationStatus = "UPDATE activitylocation SET publish = 1 WHERE group_id = $group_id AND location_id = $message_where";
                $updateLocationStatusResult = $con->query($updateLocationStatus);

                $updateLocationStatusOff = "UPDATE activitylocation SET publish = 0 WHERE NOT location_id = $message_where AND group_id = $group_id";
                $con->query($updateLocationStatusOff);

                $response = array(
                    // 'status' => 'success',
                    'status' => true,
                    'msg' => "Activity Schedule has been sent successfully"
                ); 
            } else {
                $response = array(
                    // 'status' => 'error',
                    'status' => false,
                    'msg' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
                );
            }
        } catch (Exception $e) {
            $response = array(
                // 'status' => 'error',
                'status' => false,
                'msg' => 'Email could not be sent. Error: ' . $e->getMessage()
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

    // if (mysqli_query($con, $sql)) {
    //     $response = array(
    //         "status" => true,
    //         "msg" => "Event saved successfully."
    //     );
    // } else {
    //     $response = array(
    //         "status" => false,
    //         "msg" => "Error: " . mysqli_error($con)
    //     );
    // }

    // Send the response back as JSON
    header("Content-type: application/json");
    echo json_encode($response);
} else {
    // If the request is not a POST request, return an error
    $response = array(
        "status" => false,
        "msg" => "Invalid request method."
    );

    // Send the response back as JSON
    header("Content-type: application/json");
    echo json_encode($response);
}
?>
