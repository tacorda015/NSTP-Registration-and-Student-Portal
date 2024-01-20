<?php
include('connection.php');
$con = connection();
session_start();
date_default_timezone_set('Asia/Manila');
$currentTime = date('H:i:s');
// otp-verification.php

// Check if OTP is provided
if (!isset($_SESSION['user_id'])) {
    header('Location: forgot-password.php');
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './admin/phpmailer/src/Exception.php';
require './admin/phpmailer/src/PHPMailer.php';
require './admin/phpmailer/src/SMTP.php';

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

// Generate the random password
$password = generateRandomPassword();
echo "<script>console.log($password);</script>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Sweet Alert -->
    <script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">

    <title>OTP Verification</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .verification-container{
            width: 100%;
            height: 100vh;
            background-color: #e5f1f9;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .verification-holder{
            width: 400px;
            /* height: 300px; */
            background-color: #f2f9fd;
            display: flex;
            flex-direction: column;
            /* justify-content: space-around; */
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #58aed8;
            box-shadow: 8px 10px 8px rgba(0, 0, 0, 0.4);
        }
        .header-container, .input-container{
            padding: 15px 10px;
            border-bottom: 1px solid #58aed8;
        }
        .body-container{
            padding: 15px 10px 0;
        }
        .input-container{
            display: flex;
            width: 100%;
            align-items: center;
            flex-direction: column;
        }
        .inputOtp{
            margin: 10px 5px;
            width: 30px;
            height: 40px;
            font-size: 20px;
            text-align: center;
            border: .13rem solid #000;
        }
        .verification-holder button{
            padding: 10px 20px;
            background-color: #3293c5;
            border: none;
            color: #f2f9fd;
            border-radius: 5px;
            cursor: pointer;
        }
        .verification-holder button:hover{
            background-color: #2277a7;
        }
        .verification-holder a{
            border: 1px solid #58aed8;
            background-color: #e5f1f9;
            padding: 8px 10px;
            border-radius: 8px;
            text-decoration: none;
            color: #132c3e;
        }
        .verification-holder a:hover{
            border: 1px solid #e5f1f9;
            background-color: #3293c5;
            color: #f2f9fd;
        }
        .button-container{
            display: flex;
            width: 100%;
            justify-content: end;
            gap: 10px;
            padding: 0 10px 0 0;
        }
        .inputOtp.invalid, 
        .inputOtp.invalid:active {
            border-color: red;
            /* background-color: red; */
        }
        p{
            font-size: 17px;
            line-height: 25px;
        }
    </style>
</head>
<body>
<?php
$userId = $_SESSION['user_id'];
$account_query = "SELECT * FROM useraccount WHERE user_account_id = '$userId'";
$account_result = $con->query($account_query);
$account_data = $account_result->fetch_assoc();
$userData = $account_data['email_address'];
$fullName = $account_data['full_name'];

$otp_query = "SELECT * FROM otptable WHERE otp_email = '$userData' AND otp_user_account_id = '$userId'";
$otp_result = $con->query($otp_query);
$otp_data = $otp_result->fetch_assoc();

$otp = $otp_data['otp_number'];
$otpEmail = $otp_data['otp_email'];
$otpRequestTime = $otp_data['otp_request'];

$expirationTime = strtotime('+5 minutes', strtotime($otpRequestTime));
$currentTimestamp = strtotime($currentTime);

if ($currentTimestamp > $expirationTime) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'OTP has expired!',
            text: 'Please request a new OTP.',
            showConfirmButton: false,
            timer: 4000
        }).then(function () {
            window.location.href = 'portal.php';
        });
    </script>";
    exit();
}

$numDigits = 6;

$otpInput = array_fill(0, $numDigits, '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    for ($i = 0; $i < $numDigits; $i++) {
        $inputName = 'otp_digit_' . $i;
        if (isset($_POST[$inputName])) {
            $otpInput[$i] = $_POST[$inputName];
        }
    }

    $enteredOTP = implode('', $otpInput);

    if ($enteredOTP === $otp) {
        // OTP is correct, display a modal to input a new password
        echo "<script>
            Swal.fire({
                title: 'Enter New Password',
                html: `<input type='password' id='newPassword' class='swal2-input' placeholder='New Password'>
                       <input type='password' id='confirmPassword' class='swal2-input' placeholder='Confirm Password'>`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Update Password',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const newPassword = document.getElementById('newPassword').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;
    
                    // Password validation
                    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
                    if (newPassword === '' || confirmPassword === '') {
                        Swal.showValidationMessage('Please enter a new password and confirm it.');
                        return false;
                    }
                    if (newPassword !== confirmPassword) {
                        Swal.showValidationMessage('Passwords do not match.');
                        return false;
                    }
                    if (!passwordRegex.test(newPassword)) {
                        Swal.showValidationMessage('Password should be at least 8 characters long and contain one lowercase letter, one uppercase letter, and one digit.');
                        return false;
                    }
                    
                    return { newPassword: newPassword, confirmPassword: confirmPassword };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const newPassword = result.value.newPassword;
                    const confirmPassword = result.value.confirmPassword;
    
                    // Send the newPassword and confirmPassword to the server using AJAX or form submission
                    const xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Password Updated Successfully!',
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(() => {
                                    window.location.href = 'portal.php';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Password Update Failed!',
                                    text: 'An error occurred while updating the password.',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        }
                    };
                    xhr.open('POST', 'update_password.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('newPassword=' + encodeURIComponent(newPassword) + '&confirmPassword=' + encodeURIComponent(confirmPassword));
                }
            });
        </script>";
    }else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Incorrect OTP!',
                text: 'Please enter the correct OTP to proceed.',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                // Handle error or allow the user to try again
            });
        </script>";
    }
}
?>
<div class="verification-container">
    <div class="verification-holder">
        <div class="header-container">
            <h2>Enter One-Time Password</h2>
        </div>
        <div class="body-container">
            <p>Please check your email for a message with your code. Your code is 6 numbers long.</p>
        </div>

        <form method="post">
            <div class="input-container">
                <div class="inputHolder">
                    <?php
                    for ($i = 0; $i < $numDigits; $i++) {
                        echo "<input type='text' class='inputOtp' name='otp_digit_$i' maxlength='1' value='{$otpInput[$i]}' required>";
                    }
                    ?>
                </div>
                <p>We sent your code to: <?php echo $otpEmail; ?></p>
            </div>
            <br>
            <div class="button-container">
                <a href="./portal.php">Cancel</a>
                <button type="submit">Verify OTP</button>
            </div>
        </form>
    </div>
</div>
<script>
    const inputs = document.querySelectorAll('.inputOtp');

    inputs.forEach((input, index) => {
        input.addEventListener('input', (event) => {
            const value = event.target.value;
            const nextInput = inputs[index + 1];

            event.target.value = value.replace(/\D/g, '');

            if (value.length === 1 && isValidInput(value) && nextInput) {
                nextInput.focus();
            }
        });

        input.addEventListener('blur', (event) => {
            const value = event.target.value;

            if (!isValidInput(value)) {
                event.target.classList.add('invalid');
            } else {
                event.target.classList.remove('invalid');
            }
        });
    });

    function isValidInput(value) {
        return /^\d$/.test(value);
    }
</script>
</body>
</html>

