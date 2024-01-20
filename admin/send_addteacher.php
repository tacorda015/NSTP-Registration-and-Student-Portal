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

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $teacher_fname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["teacher_fname"])));
    $teacher_lname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["teacher_lname"])));
    $teacher_mname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["teacher_mname"])));
    $birthday_month = $_POST['birthday-month'];
    $birthday_day = $_POST['birthday-day'];
    $birthday_year = $_POST['birthday-year'];
    $teacher_contactnumber = $_POST['teacher_contactnumber'];
    $teacher_saddress = $_POST['teacher_saddress'];
    $teacher_caddress = $_POST['teacher_caddress'];
    $teacher_paddress = $_POST['teacher_paddress'];
    $teacher_department = $_POST['teacher_department'];
    $teacher_email = $_POST['teacher_email'];
    $teacher_gender = $_POST['teacher_gender'];

    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
    $schoolyear_result = $con->query($schoolyear_query);
    $schoolyear_data = $schoolyear_result->fetch_assoc();
    $schoolyear_id = $schoolyear_data['schoolyear_id'];
    $semester_id = $schoolyear_data['semester_id'];

    $modifymiddlename = strtoupper(substr($teacher_mname, 0, 1));

    $full_name = $teacher_fname . ' ' . $modifymiddlename . '. ' . $teacher_lname;
    $address = $teacher_saddress . " " . $teacher_caddress . " ". $teacher_paddress;
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
    $teacher_uniquenumber = rand(10000000, 99999999);
    $temp_password = generateRandomPassword();
    $Spassword = base64_encode($temp_password);

    // Generate the message to be sent via email
    $message = "Hello $full_name,\n\n";
    $message .= "Your temporary Password is $temp_password.\n\n";
    $message .= "Please change your password after logging in.\n\n";
    $message .= "Thank you!";

    // Send the email
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eduardo.tacorda@cvsu.edu.ph';
        // $mail->Password = 'yaqrenxtmsqikfbt';
        $mail->Password = 'dblrcnnlxmvmsejq'; // New 9-6-23
        // $mail->SMTPSecure = 'tls';
        // $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $userName = 'Admin'; // User's name
        $userEmail = 'eduardo.tacorda@cvsu.edu.ph'; // User's email address

        // Set the sender
        $mail->setFrom($userEmail, $userName);
        $mail->addAddress($teacher_email);
        $mail->isHTML(false);
        $mail->Subject = 'Temporary Account Information';
        $mail->Body = $message;

        $isEmailSend = $mail->send();

        if($isEmailSend){
            // Insert the form data into the database
            $insert_query = "INSERT INTO teachertable (teacher_name, teacher_contactnumber, teacher_address, teacher_email, group_id, teacher_uniquenumber) VALUES ('$full_name', '$teacher_contactnumber', '$address', '$teacher_email', NULL, '$teacher_uniquenumber')";
            $result = mysqli_query($con, $insert_query);

            $account_query = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, gender, birthday, student_number, component_name, group_id, user_status, schoolyear_id, semester_id) VALUES (NULL,'$Spassword', 3, '$teacher_lname', '$teacher_fname', '$teacher_mname', '$full_name', '$teacher_email', '$teacher_contactnumber', '$teacher_saddress', '$teacher_caddress', '$teacher_paddress', '$address', '$teacher_department', '$teacher_gender', '$birthday', '$teacher_uniquenumber', 'CWTS', NULL, 'Active', $schoolyear_id, $semester_id)";
            $account_result = mysqli_query($con, $account_query);
            if ($result && $account_result) {
                $response = array(
                    'status' => 'success',
                    'message' => "Adviser has been successfully added. The Temporary Password has been sent to $teacher_email."
                );  
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
                );
            }
        }else {
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