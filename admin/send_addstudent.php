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

// if (isset($_POST['add_student'])) {
if($_SERVER['REQUEST_METHOD'] == "POST"){
    // Retrieve user inputs
    $firstname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["firstname"])));
    $middlename = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["middlename"])));
    $surname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["surname"])));
    $birthdayMonth = $_POST['birthdayMonth'];
    $birthday_day = $_POST['birthday_day'];
    $birthday_year = $_POST['birthday_year'];
    $contactnumber = $_POST['contactnumber'];
    $gender = $_POST['gender'];
    $saddress = $_POST['saddress'];
    $caddress = $_POST['caddress'];
    $paddress = $_POST['paddress'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];
    $section = $_POST['section'];
    $email_address = $_POST['email_address'];
    $student_number = $_POST['student_number'];
    $component_name = $_POST['component_id'];
    $group_id = $_POST['group_id'];

    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
    $schoolyear_result = $con->query($schoolyear_query);
    $schoolyear_data = $schoolyear_result->fetch_assoc();
    $schoolyear_id = $schoolyear_data['schoolyear_id'];
    $semester_id = $schoolyear_data['semester_id'];

    $modifymiddlename = strtoupper(substr($middlename, 0, 1));

    $full_name = $firstname . ' ' . $modifymiddlename . '. ' . $surname;
    $birthday = $birthdayMonth . "/" . $birthday_day . "/" . $birthday_year;
    $address = $saddress . " " . $caddress . " ". $paddress;

    $check_register_query = "SELECT COUNT(*) FROM enrolledstudent WHERE student_number = $student_number";
    $check_register_result = $con->query($check_register_query);
    $count = $check_register_result->fetch_row()[0];
    if($count > 0){
        // The student number already exists in the database
        $response = array(
            'status' => 'error',
            'message' => "Student number $student_number is already registered."
        );
    }else{
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
    
        $qrcodes_dir = '../qrcodes';
        if (!is_dir($qrcodes_dir)) {
            mkdir($qrcodes_dir, 0777, true); // Create directory with write permissions
        }
    
        $secret_code = base64_encode($student_number);
        $qrfull_name = str_replace(['/', '\\', ' '], '', $full_name);
        require_once('../phpqrcode/qrlib.php');
        $qrdata = $secret_code;
        
        $qrfile = "../qrcodes/$qrfull_name.png";
        QRcode::png($qrdata, $qrfile);
        
        
        // Prepare and execute SQL statement
        $stmt = mysqli_prepare($con, "INSERT INTO useraccount (password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, student_section, year_level, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sissssssssssssssssssssss', $password, $role_account_id, $surname, $firstname, $middlename, $full_name, $email_address, $contactnumber, $saddress, $caddress, $paddress, $address, $course, $section, $yearlevel, $gender, $birthday, $student_number, $component_name, $group_id, $user_status, $qrfile, $schoolyear_id, $semester_id);
    
        $temp_password = generateRandomPassword();
        $password = base64_encode($temp_password);
        $role_account_id = 2; // 2 is the role_id for students
        $user_status = 'Active';
        // $qrimage = $qrfile;
        
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
            $mail->addAddress($email_address);
            $mail->isHTML(false);
            $mail->Subject = 'Temporary Account Information';
            $mail->Body = "Hello $full_name,\n\nYour temporary Password is $temp_password.\n\nPlease change your password after logging in.\n\nThank you!";
    
            if ($mail->send()) {
                mysqli_stmt_execute($stmt);
                $save_student_query = "INSERT INTO enrolledstudent (student_number, student_name, student_email, registration_status, schoolyear_id, semester_id) VALUES ($student_number, '$full_name', '$email_address', 'Registered', $schoolyear_id, $semester_id)";
                $save_student_result = $con->query($save_student_query);
                // Email sent successfully
                $response = array(
                    'status' => 'success',
                    'message' => "Student has been successfully added. The Temporary Password has been sent to $email_address."
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
                'message' => 'Email could not be sent. Error: ' . $e->getMessage()
            );
        }
        
        // Close statement and connection
        mysqli_stmt_close($stmt);
        mysqli_close($con);
    }

    echo json_encode($response);
}
?>