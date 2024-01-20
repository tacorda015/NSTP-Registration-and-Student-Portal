<?php
session_start();
include('connection.php');
$con = connection();

date_default_timezone_set('Asia/Manila');
$currentTime = date('H:i:s');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './admin/phpmailer/src/Exception.php';
require './admin/phpmailer/src/PHPMailer.php';
require './admin/phpmailer/src/SMTP.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicons -->
    <link href="assets/img/Logo.png" rel="icon" />

    <link rel="stylesheet" href="./assets/css/mainStyle.css">

    <!-- Link for Fontawosome -->
    <script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>

      <!-- Boxiocns CDN Link -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"rel="stylesheet" />
    <link rel="stylesheet" href="../boxicons-2.1.4/css/boxicons.min.css">

    <!-- For bootstrap -->
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./node_modules/bootstrap-icons/font/bootstrap-icons.css" />
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sweet Alert -->
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">


    <title>Forgot Password</title>
    <style>
      *{
          padding: 0;
          margin: 0;
          box-sizing: border-box;
      }
      .head-container {
        text-align: center;
        padding:  10px 0 20px;
        border-bottom: 1px solid #58aed8;
      }
      
      .head-container h3 {
        color: #333;
      }
      
      /* .body-container {
        padding: 20px 0;
      } */
      
      form {
        display: flex;
        flex-direction: column;
      }
      
      .input-container {
        position: relative;
        margin-bottom: 10px;
        /* display: flex;
        flex-direction: column;
        gap: 10px; */
      }
      .error-message {
        color: red;
        margin-top: 5px;
        font-size: .8em;
      }

      .invalid {
        border: 1px solid red !important;
      }
      /* @media screen and (max-width: 767px) {
        .forgot-container{
          margin: 0 15px;
        }
      } */
    </style>
</head>
<body>
<?php
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['retrieve'])) {
    $userInput = $_POST['forgotAccount'];
    $studentNumber = isset($_POST['studentNumber']) ? $_POST['studentNumber'] : '';

    $user_query = "SELECT user_account_id, email_address, student_number, full_name FROM useraccount WHERE email_address = '$userInput' AND student_number = '$studentNumber'";
    $user_result = mysqli_query($con, $user_query);

    if ($user_result && mysqli_num_rows($user_result) > 0) {
        while ($row = mysqli_fetch_assoc($user_result)) {
            $userAccountId = $row['user_account_id'];
            $emailAddress = $row['email_address'];
            $studentNumber = $row['student_number'];
            $fullName = $row['full_name'];
            $_SESSION['user_id'] = $row['user_account_id']; // store user ID in session
            $_SESSION['email_address'] = $row['email_address']; // store user ID in session

            $existing_otp_query = "SELECT otp_id FROM otptable WHERE otp_email = '$emailAddress' AND otp_user_account_id = $userAccountId";
            $existing_otp_result = mysqli_query($con, $existing_otp_query);

            $otp_email = $emailAddress;
            $otp_number = rand(100000, 999999);
            $otp_request = $currentTime;

            // Generate the message to be sent via email
            $message = "Dear $fullName,\n\n";
            $message .= "Your One-Time Password for account retrieval is: $otp_number.\n\n";
            $message .= "Please use this One-Time Password within 5 minutes to complete the account retrieval process.\n\n";
            $message .= "Note: This One-Time Password is for one-time use only and should be kept confidential. If you did not initiate the account retrieval process, please disregard this email.\n\n";
            $message .= "Thank you,\n";

            // Send the email
            try {
                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'eduardo.tacorda@cvsu.edu.ph';
                $mail->Password = 'dblrcnnlxmvmsejq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $userName = 'One-Time Password (OTP)'; // User's name
                $userEmail = 'nstpportal@gmail.com'; // User's email address

                // Set the sender
                $mail->setFrom($userEmail, $userName);
                $mail->addAddress($otp_email);
                $mail->isHTML(false);
                $mail->Subject = 'One-Time Password (OTP)';
                $mail->Body = $message;

                $mail->send();

                if ($existing_otp_result && mysqli_num_rows($existing_otp_result) > 0) {
                  // Update the existing OTP record
                  $existing_otp_row = mysqli_fetch_assoc($existing_otp_result);
                  $otp_id = $existing_otp_row['otp_id'];
                }

                if (isset($otp_id)) {
                    // Update the existing OTP record
                    $update_otp_query = "UPDATE otptable SET otp_number = '$otp_number', otp_request = '$otp_request' WHERE otp_id = '$otp_id' AND otp_user_account_id = '$userAccountId'";
                    $update_otp_result = mysqli_query($con, $update_otp_query);

                    if ($update_otp_result) {
                      // echo $update_otp_result;
                        echo "<script>
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Updated',
                                text: 'The OTP has been successfully updated and sent to $otp_email. Please check your email to retrieve the OTP.'
                            }).then(function() {
                                window.location.href = 'otp-verification.php'; // Redirect to the desired page
                            });
                        </script>";
                    } else {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'OTP Update Failed',
                                text: 'Error: " . mysqli_error($con) . "'
                            });
                        </script>";
                    }
                } else {
                    // Insert a new OTP record
                    $otp_query = "INSERT INTO otptable (otp_user_account_id, otp_email, otp_number, otp_request) VALUES ('$userAccountId', '$otp_email', '$otp_number', '$otp_request')";
                    $otp_result = mysqli_query($con, $otp_query);

                    if ($otp_result) {
                        echo "<script>
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Successful',
                                text: 'The OTP has been successfully sent to $otp_email. Please check your email to retrieve the OTP.'
                            }).then(function() {
                                window.location.href = 'otp-verification.php'; // Redirect to the desired page
                            });
                        </script>";
                    } else {
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'OTP Sending Failed',
                                text: 'Error: " . mysqli_error($con) . "'
                            });
                        </script>";
                    }
                }
            } catch (Exception $e) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Error',
                        text: 'Message could not be sent. Error: " . $mail->ErrorInfo . "'
                    });
                </script>";
            }
        }
    } else {
        // echo "No results found."; make this sweet alert also and i add new column to store also the user_account_id
        echo '<script>
                  Swal.fire({
                      icon: "info",
                      title: "No Student Data found.",
                      text: "",
                  });
              </script>';
    }
}
?>
    <div class="container">
      <header id="header" class="fixed-top">
        <div class="container d-flex align-items-center">
          <h1 class="logo me-auto">
            <img src="./assets/img/Logo3.png" alt="">
            <a href="index.php" style="color: #fff;  text-decoration: none;">NSTP Portal</a>
          </h1>
          <!-- Uncomment below if you prefer to use an image logo -->
          <!-- <a href="index.php" class="logo me-auto"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

          <nav id="navbar" class="navbar">
            <ul>
              <li><a class="nav-link scrollto" href="index.php#hero">Home</a></li>
              <li><a class="nav-link scrollto" href="index.php#about">About</a></li>
              <li><a class="nav-link scrollto" href="index.php#portfolio">Gallery</a> </li>
              <li><a class="nav-link scrollto" href="index.php#eventCalendar">Event Calendar</a></li>
              <!-- <li><a class="getstarted " data-bs-toggle="modal" data-bs-target="#enrollmodal" >Enroll</a > </li>
              <li><a class="getstarted " data-bs-toggle="modal" data-bs-target="#loginModal" >Log In</a > </li> -->
              <li><a class="nav-link scrollto" href="./portal.php">Portal</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
          </nav>
          <!-- .navbar -->
        </div>
      </header>
      <!-- End Header -->
      <div class="forgot-container border rounded shadow px-4 py-3" style="max-width: 400px; margin: 7rem auto 0;">
          <div class="head-container">
              <h3>Retrieve Account</h3>
          </div>
          <div class="body-container my-3">
              <p class="m-0">Please Enter your CvSU Email to retrieve your account</p>
          </div>
          <form method="post" id="inputValidation">

              <div class="form-floating px-1 mt-3">
                  <input type="text" name="forgotAccount" id="forgotAccount" class="form-control" placeholder="CvSU Email" autocomplete="new-password">
                  <label for="passwordfloatingInput"><i class="bi bi-envelope-at"></i> CvSU Emails</label>
                  <span id="email-error" class="error-message"></span>
              </div>
              <div class="form-floating px-1 mt-3">
                  <input type="text" name="studentNumber" id="studentNumber" class="form-control" placeholder="Student Number" autocomplete="new-password">
                  <label for="passwordfloatingInput"><i class="bi bi-hash"></i> Student Number</label>
                  <span id="student-number-error" class="error-message"></span>
              </div>
              <div class="button-container mt-3 d-flex justify-content-end gap-2">
                  <a href="./portal.php" class="btn btn-outline-primary">Cancel</a>
                  <button type="submit" name="retrieve" class="btn btn-primary">Send OTP</button>
              </div>
          </form>
      </div>
        </div>
    <script>
  document.addEventListener("DOMContentLoaded", function() {
    const forms = document.getElementById('inputValidation');
    forms.addEventListener('submit', function(event) {
      event.preventDefault();

      // Perform full name validation
      const emailInput = document.getElementById('forgotAccount');
      const emailError = document.getElementById('email-error');
      const emailValue = emailInput.value.trim();
      const emailRegex = /^[A-Za-z0-9._%+-]+@cvsu\.edu\.ph$/;

      if (emailValue === '') {
        emailError.textContent = 'Email is required';
        emailInput.classList.add('invalid');
        setTimeout(function() {
          emailError.textContent = '';
          emailInput.classList.remove('invalid');
        }, 30000); // Display the error message for 3 seconds
      } else if (!emailRegex.test(emailValue)) {
        emailError.textContent = 'Please enter a valid CvSU email address';
        emailInput.classList.add('invalid');
        setTimeout(function() {
          emailError.textContent = '';
          emailInput.classList.remove('invalid');
        }, 3000); // Display the error message for 3 seconds
      } else {
        // Email is valid, remove any error messages
        emailError.textContent = '';
        emailInput.classList.remove('invalid');

        // Perform student number validation
        const studentNumberInput = document.getElementById('studentNumber');
        const studentNumberError = document.getElementById('student-number-error');
        const studentNumberValue = studentNumberInput.value.trim();
        const studentNumberRegex = /^\d{9}$/;

        if (studentNumberValue === '') {
          studentNumberError.textContent = 'Student number is required';
          studentNumberInput.classList.add('invalid');
          setTimeout(function() {
            studentNumberError.textContent = '';
            studentNumberInput.classList.remove('invalid');
          }, 3000); // Display the error message for 3 seconds
        } else if (!studentNumberRegex.test(studentNumberValue)) {
          studentNumberError.textContent = 'Please enter a valid student number';
          studentNumberInput.classList.add('invalid');
          setTimeout(function() {
            studentNumberError.textContent = '';
            studentNumberInput.classList.remove('invalid');
          }, 3000); // Display the error message for 3 seconds
        } else {
          // Student number is valid, remove any error messages
          studentNumberError.textContent = '';
          studentNumberInput.classList.remove('invalid');

          // If all validations pass, trigger the form submission
          const registerInput = document.createElement('input');
          registerInput.setAttribute('type', 'hidden');
          registerInput.setAttribute('name', 'retrieve');
          registerInput.setAttribute('value', '1');
          forms.appendChild(registerInput);
          forms.submit();
        }
      }
    });
  });
</script>
<script src="assets/js/main.js"></script>
</body>
</html>