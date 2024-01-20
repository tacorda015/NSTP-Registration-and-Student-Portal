<?php
session_start();
ob_start();
include('../connection.php');
$con = connection();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $trainer_fname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["trainer_fname"])));
    $trainer_lname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["trainer_lname"])));
    $trainer_mname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["trainer_mname"])));
    $birthday_month = $_POST['birthday-month'];
    $birthday_day = $_POST['birthday-day'];
    $birthday_year = $_POST['birthday-year'];
    $trainer_contactnumber = $_POST['trainer_contactnumber'];
    $trainer_saddress = $_POST['trainer_saddress'];
    $trainer_caddress = $_POST['trainer_caddress'];
    $trainer_paddress = $_POST['trainer_paddress'];
    $trainer_department = $_POST['trainer_department'];
    $trainer_email = $_POST['trainer_email'];
    $trainer_gender = $_POST['trainer_gender'];

    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
    $schoolyear_result = $con->query($schoolyear_query);
    $schoolyear_data = $schoolyear_result->fetch_assoc();
    $schoolyear_id = $schoolyear_data['schoolyear_id'];
    $semester_id = $schoolyear_data['semester_id'];

    $modifymiddlename = strtoupper(substr($trainer_mname, 0, 1));

    $full_name = $trainer_fname . ' ' . $modifymiddlename . '. ' . $trainer_lname;
    $address = $trainer_saddress . " " . $trainer_caddress . " ". $trainer_paddress;
    $birthday = $birthday_month . "/" . $birthday_day . "/" . $birthday_year;

    function generateRandomPassword() {
        $length = 8; // Minimum length of the password
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';
    
        $characters = $lowercase . $uppercase . $digits;
        $password = '';
    
        // Add at least one lowercase letter, one uppercase letter, and one digit
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $digits[rand(0, strlen($digits) - 1)];
    
        // Add remaining characters
        $remainingLength = $length - 3;
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
    
        // Shuffle the characters to ensure random order
        $password = str_shuffle($password);
    
        return $password;
    }

    $trainer_uniquenumber = rand(10000000, 99999999);
    $temp_password = generateRandomPassword();
    $Spassword = base64_encode($temp_password);

    // Generate the message to be sent via email
    $message = "Hello $full_name,\n\n";
    $message .= "Your Temporary password is $temp_password.\n\n";
    $message .= "Please change your password after logging in.\n\n";
    $message .= "Thank you!";

    // Send the email
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eduardo.tacorda@cvsu.edu.ph';
        $mail->Password = 'dblrcnnlxmvmsejq'; // New 9-6-23
        // $mail->SMTPSecure = 'tls';
        // $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $userName = 'Admin'; // User's name
        $userEmail = 'eduardo.tacorda@cvsu.edu.ph'; // User's email address

        // Set the sender
        $mail->setFrom($userEmail, $userName);
        $mail->addAddress($trainer_email);
        $mail->isHTML(false);
        $mail->Subject = 'Temporary Account Information';
        $mail->Body = $message;

        $isEmailSent = $mail->send();

        if($isEmailSent) {
            // Insert the form data into the database
            $insert_query = "INSERT INTO trainertable (trainer_name, trainer_contactnumber, trainer_address, trainer_email, group_id, trainer_uniquenumber) VALUES ('$full_name', '$trainer_contactnumber', '$address', '$trainer_email', NULL, '$trainer_uniquenumber')";
            $result = mysqli_query($con, $insert_query);

            $account_query = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, gender, birthday, student_number, component_name, group_id, user_status, schoolyear_id, semester_id) VALUES (NULL,'$Spassword', 3, '$trainer_lname', '$trainer_fname', '$trainer_mname', '$full_name', '$trainer_email', '$trainer_contactnumber', '$trainer_saddress', '$trainer_caddress', '$trainer_paddress', '$address', '$trainer_department', '$trainer_gender', '$birthday', '$trainer_uniquenumber', 'ROTC', NULL, 'Active', $schoolyear_id, $semester_id)";
            $account_result = mysqli_query($con, $account_query);

            if($result && $account_result) {
                $response = array(
                    'status' => 'success',
                    'message' => "Trainer Staff has been successfully added. The Temporary Password has been sent to $trainer_email."
                );  
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
                );
            }
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
    echo json_encode($response);
}                    
?>