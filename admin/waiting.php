<?php
session_start();
ob_start();
include('../connection.php');
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}

// get user data from session
$user_data = $_SESSION['user_data'];
$role = "SELECT * FROM roleaccount WHERE role_account_id = {$user_data['role_account_id']}";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
} elseif ($role_data['role_name'] == 'student') {
    header('Location: student.php');
} 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';


// Calling the sidebar
include_once('adminsidebar.php');
?> 
<div class="home-main-container">
            <div class="studentlist-container">
            <div id="loader-overlay" class="loader-overlay"></div>
            <div id="loader" class="loader">Sending <span></span></div>
</div>
<!-- Start of modal -->
<?php
                        // if (isset($_POST['add_student'])) {
                        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_student'])){
                            // Retrieve user inputs
                            $full_name = $_POST['full_name'];
                            $email_address = $_POST['email_address'];
                            $student_number = $_POST['student_number'];
                            $component_name = $_POST['component_id'];
                            $group_id = $_POST['group_id'];

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
                            $full_name = str_replace(['/', '\\'], '', $full_name);
                            require_once('../phpqrcode/qrlib.php');
                            $qrdata = $secret_code;
                            
                            $qrfile = "../qrcodes/$full_name.png";
                            QRcode::png($qrdata, $qrfile);

                            // Prepare and execute SQL statement
                            $stmt = mysqli_prepare($con, "INSERT INTO useraccount (password, role_account_id, full_name, email_address, student_number, component_name, group_id, user_status, qrimage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            mysqli_stmt_bind_param($stmt, 'sisssssss', $password, $role_account_id, $full_name, $email_address, $student_number, $component_name, $group_id, $user_status, $qrfile);
                        
                            $temp_password = generateRandomPassword();
                            $password = base64_encode($temp_password);
                            $role_account_id = 2; // 2 is the role_id for students
                            $user_status = 'active';
                            // $qrimage = $qrfile;
                            
                            // Send the email
                            try {
                                $mail = new PHPMailer(true);

                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'nstpportal@gmail.com';
                                $mail->Password = 'yaqrenxtmsqikfbt';
                                // $mail->SMTPSecure = 'tls';
                                // $mail->Port = 587;
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                                $mail->Port = 465;

                                $mail->setFrom('eduardotacorda17@gmail.com');
                                $mail->addAddress($email_address);
                                $mail->isHTML(false);
                                $mail->Subject = 'Temporary Account Information';
                                $mail->Body = "Hello $full_name,\n\nYour temporary Password is $temp_password.\n\nPlease change your password after logging in.\n\nThank you!";

                                if ($mail->send()) {
                                    mysqli_stmt_execute($stmt);
                                    echo '<script>
                                    document.getElementById("loader").style.display = "none"; // Hide the loader
                                    Swal.fire({
                                    title: "Success",
                                    text: "Student has been successfully added. The Temporary Password has been sent to student email.",
                                    icon: "success"
                                    }).then(function() {
                                        window.location.href = "studentlist.php";
                                    }); 
                                    </script>';
                                } else {
                                    $error_message = "Message could not be sent. Error: " . $mail->ErrorInfo;

                                    // Display error message using SweetAlert2
                                    echo "<script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: '$error_message'
                                    }).then(() => {
                                        window.location.href = 'studentlist.php';
                                    });
                                    </script>";
                                }
                            } catch (Exception $e) {
                                $error_message = "Message could not be sent. Error: " . $e->getMessage();

                                // Display error message using SweetAlert2
                                echo "<script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: '$error_message'
                                }).then(() => {
                                    window.location.href = 'studentlist.php';
                                });
                                </script>";
                            }

                            // Close statement and connection
                            mysqli_stmt_close($stmt);
                            mysqli_close($con);
                        }
                        ?>
                    <div class="modal fade" id="addstudentmodal" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 style="text-align: center; padding: 5px 0;">Add Student</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" enctype="multipart/form-data" action="studentlist.php" id="emailForm">
                                    <div class="modal-body" style="z-index: 0;">
                                        <div class="form-group">
                                            <label for="full_name">Full Name:</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" pattern='[A-Za-z.\s]+' required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email_address">CvSU Email:</label>
                                            <input type="email" class="form-control" id="email_address" name="email_address" required>
                                            <small id="emailError" style="color: red;"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="student_number">Student Number:</label>
                                            <input type="text" class="form-control" id="student_number" name="student_number" required>
                                            <small id="studentNumberError" style="color: red;"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="component_id">Component:</label>
                                            <select class="form-control" id="component_id" name="component_id" required>
                                                <option value="" selected disabled hidden>Choose here</option>
                                                <?php
                                                // Retrieve the list of components from the database
                                                $component_query = "SELECT * FROM componenttable";
                                                $component_result = mysqli_query($con, $component_query);
                                                while ($component_row = mysqli_fetch_assoc($component_result)) {
                                                    echo "<option value='" . $component_row['component_name'] . "'>" . $component_row['component_name'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="choosegroup">Group Name:</label>
                                            <select class="form-control" id="choosegroup" name="group_id" required></select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="sendButton" class="btn btn-primary" name="add_student" onclick="sendEmail()">Add Student</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal -->
                    </section>
    </div>
    <script>
        var sendingEmail = false;
        // Confirmation message when refreshing or leaving the page
        window.addEventListener('beforeunload', function (e) {
            if (sendingEmail) {
                // Show confirmation message only if email sending process has started
                e.preventDefault();
                e.returnValue = '';

                var confirmationMessage = 'Changes you made may not be saved. Are you sure you want to leave this page?';
                (e || window.event).returnValue = confirmationMessage;
                return confirmationMessage;
            }
        });

        function sendEmail() {
            sendingEmail = true;
            document.getElementById("sendButton").setAttribute("disabled", "disabled"); // Disable the button
            document.getElementById("loader-overlay").style.display = "block"; // Show the loader overlay
            document.getElementById("loader").style.display = "block"; // Show the loader

            // Send the form data asynchronously
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        if (xhr.responseText === 'success') {
                        // Success
                        sendingEmail = false;
                        document.getElementById("loader-overlay").style.display = "none"; // Hide the loader overlay
                        document.getElementById("loader").style.display = "none"; // Hide the loader
                    }else {
                        // Error
                        document.getElementById("loader-overlay").style.display = "none"; // Hide the loader overlay
                        document.getElementById("loader").style.display = "none"; // Hide the loader
                    }
                    } 
                }
            };

            var formData = new FormData(document.getElementById("emailForm"));
            formData.append("add_student", ""); // Add the 'add_student' parameter to the form data
            xhr.send(formData);
        }
    </script>
     </body>
</html>