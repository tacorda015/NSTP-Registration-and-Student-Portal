<?php
include('../connection.php');
session_start();
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
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
} 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
// Calling the sidebar
include_once('adminsidebar.php');
?>
<style>
.announcement-container .table-container {
  width: 80%;
  overflow-x: auto;
  max-height: 300px;
  overflow-y: auto;
  margin-top: 1rem;
  scroll-behavior: smooth;
}

.announcement-container .table-container::-webkit-scrollbar {
  display: none;
}

.announcement-container .responsive-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #ddd;
  background-color: #f5f5f5;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  table-layout: fixed; /* Add this line for sticky the thead */
}

.announcement-container .responsive-table th,
.announcement-container .responsive-table td {
  padding: 0.5rem;
  text-align: left;
  white-space: nowrap; /* Add this line for sticky the thead */
  overflow: hidden; /* Add this line for sticky the thead */
  text-overflow: ellipsis; /* Add this line for sticky the thead */
}

.announcement-container .responsive-table th {
  background-color: #adb5bd;
  color: white;
  position: sticky; /* Add this line for sticky the thead */
  top: 0; /* Add this line for sticky the thead */
  z-index: 1; /* Add this line for sticky the thead */
}

.announcement-container .responsive-table tbody tr:nth-child(even) {
  background-color: #fff;
}

.announcement-container {
  width: 100%;
  height: calc(100vh - 7rem);
  display: flex;
  flex-direction: column;
  /* justify-content: center; */
  align-items: center;
  margin-top: 3rem;
}

.announcement-container .group-id {
  text-align: center;
  font-size: 1.5rem;
  word-wrap: break-word;
}

.announcement-container .header-container {
  width: 80%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  height: fit-content;
}
.announcement-container span.search-icon {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 15px;
  height: 15px;
  background-size: 100%;
  cursor: pointer;
  background-image: url(../assets/img/xIcon2.png);
}
.announcement-container .title1-container{
    display: flex;
    align-items: center ;
    flex-direction: row-reverse;
    white-space: nowrap;
}
.announcement-container .title1-container h2{
    font-size: 1.75rem;
}
.announcement-container .title1-container label{
    margin-right: 10px;
    font-size: 1.2rem;
}
.announcement-container .title-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
    margin-bottom: 3rem;
}
@media screen and (max-width: 767px) {
    .announcement-container {
        width: 100%;
        height: 100%;
        display: block;
        justify-content: center;
        align-items: center;
        margin-top: 1rem;
    }

    .announcement-container .responsive-table {
        display: block;
        position: relative;
        width: 100%;
        min-width: 300px;
        margin-top: 1rem;
        height: 550px;
        overflow-y: scroll;
        box-shadow: none;
    }

    .announcement-container .responsive-table::-webkit-scrollbar{
        display: none;
    }

    .announcement-container .table-container {
        margin: auto;
        width: 100%;
        max-height: 600px;
    }

    .announcement-container .responsive-table thead {
        display: block;
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    .announcement-container .responsive-table tr {
        border: 1px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 0.5rem;
    }

    .announcement-container .responsive-table td {
    border: none;
    display: inline-block;
    text-align: right;
    padding: 0.5rem;
    font-size: 12px;
    width: 100%; /* Add this line */
    box-sizing: border-box; /* Add this line */
    white-space: normal; /* Add this line for sticky the thead */
    }


    .announcement-container .responsive-table td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-transform: uppercase;
        margin-right: 0.5rem;
    }

    .announcement-container .responsive-table tbody tr:nth-child(even) {
        background-color: white;
    }

    .announcement-container .header-container {
        flex-direction: column;
        width: 100%;
        margin-bottom: 10px;
        min-width: 300px;
        overflow-x: hidden;
    }
    .announcement-container .title-container {
    text-align: center;
    margin-bottom: 1rem;
    min-width: 300px;
    overflow-x: hidden;
    }

    .announcement-container .title-container h2 {
    font-size: 1.5rem;
    margin: 1rem 0 0;
    }

    .announcement-container .title-container label {
    display: block;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    }
    .announcement-container .title1-container{
        flex-direction: column;
    }
}
.recipient {
  display: inline-block;
  padding: 4px 8px;
  margin-right: 4px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #f5f5f5;
}

.remove-recipient {
  cursor: pointer;
  color: #999;
  margin-left: 4px;
}
#suggestionsContainer {
  position: relative;
}

#suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: #fff;
  border: 1px solid #ccc;
  max-height: 200px;
  overflow-y: auto;
  z-index: 999;
}

#recipientsContainer {
  max-height: 200px;
  overflow-y: auto;
}

</style>
<?php
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
        $reciever = $recipient;
    } elseif ($recipient == "teachers") {
        // Query all teachers
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE role_account_id = 3 AND component_name = 'CWTS'";
        $reciever = $recipient;
    } elseif ($recipient == "trainers") {
        // Query all trainers
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE role_account_id = 3 AND component_name = 'ROTC'";
        $reciever = $recipient;
    } elseif ($recipient == "students") {
        // Query all students
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE role_account_id = 2 AND user_status = 'active'";
        $reciever = $recipient;
    } elseif ($recipient == "rotcgroups") {
        // Get the selected group ID from the form
        $group_id = $_POST["rotcgroup"];
        $group_name_query = "SELECT group_name FROM grouptable WHERE group_id = $group_id";
        $group_name_result = $con->query($group_name_query);
        $group_name_data = mysqli_fetch_assoc($group_name_result);
        // Query recipients based on the selected group
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE group_id = $group_id AND component_name = 'ROTC'";
        $reciever = $group_name_data['group_name'];
    } elseif ($recipient == "cwtsgroups") {
        // Get the selected group ID from the form
        $group_id = $_POST["cwtsgroup"];
        $group_name_query = "SELECT group_name FROM grouptable WHERE group_id = $group_id";
        $group_name_result = $con->query($group_name_query);
        $group_name_data = mysqli_fetch_assoc($group_name_result);
        // Query recipients based on the selected group
        $query = "SELECT user_account_id, full_name, email_address FROM useraccount WHERE group_id = $group_id AND component_name = 'CWTS'";
        $reciever = $group_name_data['group_name'];
    } elseif ($recipient == "specific") {
        $specificRecipients = $_POST['hiddenspecificRecipients'];
        $selectedRecipients = json_decode($specificRecipients, true);
        $subject = $_POST["subject"];
        $message = $_POST["message"];
    
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
        $mail->Username = 'aileenbatiancila008@gmail.com';
        $mail->Password = 'iifutkftxbqwnmbs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
    
        // Set sender
        $mail->setFrom('aileenbatiancila008@gmail.com');
    
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
                    "email" => $emailAddress,
                    "reason" => "Invalid email address"
                );
            }
        }
    
        try {
            // Send the email to all recipients
            $isEmailSent = $mail->send();
    
            if ($isEmailSent) {
                // Display SweetAlert2 notification for successful send
                echo '<script>Swal.fire("Success", "Announcement has been sent successfully", "success")</script>';
            } else {
                // Display SweetAlert2 notification for unsuccessful send
                $errorMessage = "Error sending emails: " . $mail->ErrorInfo;
                echo '<script>Swal.fire("Error", "'.$errorMessage.'", "error")</script>';
            }
        } catch (Exception $e) {
            // Exception occurred while sending email
            $errorMessage = "Error sending emails: " . $e->getMessage();
            echo '<script>Swal.fire("Error", "'.$errorMessage.'", "error")</script>';
        }
    
        // Display SweetAlert2 notifications
        if (!empty($successfulRecipients)) {
            echo '<script>Swal.fire("Success", "Announcement has been sent successfully", "success")</script>';
        }
    
        if (count($unsuccessfulRecipients) > 0) {
            $unsuccessBatch = $currentBatch++;
            $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, announcement_batch) VALUES ";
            $valueStrings = array();
            foreach ($unsuccessfulRecipients as $recipient) {
                $unsuccessBatch++;
                $email = $con->real_escape_string($recipient["email"]);
                $reason = $con->real_escape_string($recipient["reason"]);
                $errorMessage = "Email: " . $email . ",\nReason: " . $reason . "<br>";  // Initialize the error message inside the loop
                $valueStrings[] = "('$sender_id', '', '$email', 'Unsuccessful send Announcement', '$errorMessage', '$sender_id', NOW(), '$unsuccessBatch')";
            }
            $saveQuery .= implode(", ", $valueStrings);
            $con->query($saveQuery);
        }
        
    }
    
    

    // If a query is defined, retrieve the recipient list
    if (!empty($query)) {
        $result = $con->query($query);
    
        if ($result->num_rows > 0) {
            // Initialize arrays to store successful and unsuccessful recipients
            $successfulRecipients = array();
            $unsuccessfulRecipients = array();
    
            // Initialize batch size and counter variables
            $batchSize = 50;
            $counter = 0;
    
            // Initialize SMTP connection outside the loop
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            // Enable SMTP keep-alive
            $mail->SMTPKeepAlive = true;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            // $mail->Username = 'eduardotacorda17@gmail.com';
            // $mail->Password = 'qpithesnqwsmttra';
            $mail->Username = 'aileenbatiancila008@gmail.com';
            $mail->Password = 'iifutkftxbqwnmbs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom('eduardotacorda17@gmail.com');
            $mail->isHTML(false);
            $mail->Subject = $_POST["subject"];
            $mail->Body = $_POST["message"];
    
            while ($row = $result->fetch_assoc()) {
                $recipient_id = $row["user_account_id"];
                $recipient_list[] = $row["full_name"] . " (" . $row["email_address"] . ")";
    
                // Add recipient to the email
                $email = $row["email_address"];
    
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Valid email address, add it as a recipient
                    $mail->addAddress($email);
                } else {
                    // Invalid email address, handle the error accordingly
                    $unsuccessfulRecipients[] = array(
                        "email" => $email,
                        "reason" => "Invalid email address"
                    );
                    continue; // Skip to the next recipient
                }
                
    
                try {
                    $isEmailSent = $mail->send();
    
                    if (!$isEmailSent) {
                        // Email sending failed for this recipient
                        $unsuccessfulRecipients[] = array(
                            "email" => $row["email_address"],
                            "reason" => $mail->ErrorInfo
                        );
                    } else {
                        // Email sent successfully for this recipient
                        $successfulRecipients[] = $row["email_address"];
                    }
                } catch (Exception $e) {
                    // Exception occurred while sending email
                    $unsuccessfulRecipients[] = array(
                        "email" => $row["email_address"],
                        "reason" => $e->getMessage()
                    );
                }
    
                // Increment the counter
                $counter++;
    
                // Check if the batch size or end of recipients is reached
                if ($counter % $batchSize === 0 || $counter === $result->num_rows) {
                    // Check if there are unsuccessful recipients in the batch
                    if (count($unsuccessfulRecipients) > 0) {
                        // Failed to send email for some recipients in the batch
                        $unsuccessfulEmails = "";
                        foreach ($unsuccessfulRecipients as $recipient) {
                            $unsuccessfulEmails .= $recipient["email"] . ": " . $recipient["reason"] . "\n";
                        }
    
                        // Clear the unsuccessful recipients array for the next batch
                        $unsuccessfulRecipients = array();
    
                        // Clear recipients for the next batch
                        $mail->clearAddresses();
                    }
                }
                // Save the unsuccessful recipients to the announcement table
                if (count($unsuccessfulRecipients) > 0) {
                    $unsuccessBatch = $currentBatch++;
                    $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, announcement_batch) VALUES ";
                    $valueStrings = array();
                    foreach ($unsuccessfulRecipients as $recipient) {
                        $email = $con->real_escape_string($recipient["email"]);
                        $reason = $con->real_escape_string($recipient["reason"]);
                        $valueStrings[] = "('$sender_id', '$recipient_id', '$email', 'Unsuccessful send Announcement', '$reason', '$reciever', NOW(), '$unsuccessBatch')";
                    }
                    $saveQuery .= implode(", ", $valueStrings);
                    $con->query($saveQuery);
                }
        
                // Save the announcement in the database for all successfully sent emails
                $saveQuery = "INSERT INTO announcementtable (sender_id, recipient_id, email_address, subject, message, reciever, created_at, announcement_batch) VALUES ";
                $valueStrings = array();
                foreach ($successfulRecipients as $email) {
                    $valueStrings[] = "('$sender_id', '$recipient_id', '$email', '".$_POST["subject"]."', '".$_POST["message"]."', '$reciever', NOW(), '$currentBatch')";
                }
                $saveQuery .= implode(", ", $valueStrings);
                $con->query($saveQuery);
        
            }
            // Check if all emails were sent successfully
            if (count($unsuccessfulRecipients) == 0) {
                // All emails sent successfully
                ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Announcement Sent',
                        text: 'The announcement has been successfully sent to the selected recipients.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function() {
                        // Redirect to desired page after displaying the alert
                        window.location.href = 'announcement.php';
                    });
                </script>
                <?php
            } else {
                // Display an error message
                ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Send Announcement',
                        text: 'Failed to send the announcement to the following recipients:\n\n<?php echo $unsuccessfulEmails; ?>',
                    });
                </script>
                <?php
            }
        }
    }
    
}
?>

    <div class="announcement-container">
        <div class="title-container">
            <h2 class="group_id">Announcement Page</h2>
        </div>
        <div class="header-container">
            <h2>For Search</h2>
            <div class="title-container1">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendannouncement">Send Announcement</button>
            </div>
        </div>
        <div class="table-container">
    <table class="responsive-table">
        <thead>
            <tr>
                <th>Receiver</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch the announcement data from the database
            $announcementQuery = "SELECT announcement_batch, GROUP_CONCAT(DISTINCT reciever) AS receivers, subject, message, MAX(created_at) AS latest_date FROM announcementtable GROUP BY announcement_batch ORDER BY announcement_batch DESC";
            $announcementResult = $con->query($announcementQuery);

            if ($announcementResult->num_rows > 0) {
                while ($row = $announcementResult->fetch_assoc()) {
                    $announcementBatch = $row['announcement_batch'];
                    $user_data['user_account_id'];
                    $receivers = $row['receivers'];
                    $subject = $row['subject'];
                    $message = $row['message'];
                    $date = $row['latest_date'];
                    
                    // Overwrite $receivers with "Me" if it matches the current user's account ID
                    if ($receivers == $user_data['user_account_id']) {
                        $receivers = "Me";
                    }

                    echo "<tr class='announcement-row' data-announcement-batch='$announcementBatch'>";
                    echo "<td data-label='Receiver'>$receivers</td>";
                    echo "<td data-label='Subject'>$subject</td>";
                    echo "<td data-label='Message'>$message</td>";
                    echo "<td data-label='Date'>$date</td>";
                    echo "</tr>";
                }
            } else {
                // Display a message if no announcements are found
                echo "<tr><td colspan='5'>No announcements found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">Announcement Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Receiver:</strong> <span id="modalReceiver"></span></p>
                <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
                <p><strong>Message:</strong> <span id="modalMessage"></span></p>
                <p><strong>Date:</strong> <span id="modalDate"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    
    <!-- Start of Sending announcement modal -->
    <div class="modal fade" id="sendannouncement" tabindex="1" aria-labelledby="sendannouncement" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Send Announcement</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Start of Announcement Form -->
                    <form action="announcement.php" method="post" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="recipient">Recipient</label>
                            <select class="form-control" id="recipient" name="recipient">
                                <option value="all">All</option>
                                <option value="teachers">All Teachers</option>
                                <option value="trainers">All Trainers</option>
                                <option value="students">All Students</option>
                                <option value="rotcgroups">ROTC Group</option>
                                <option value="cwtsgroups">CWTS Group</option>
                                <option value="specific">Specific Recipients</option>
                            </select>
                        </div>

                        <?php
                        // Query the database to retrieve ROTC groups
                        $rotc_group_query = "SELECT group_id, group_name FROM grouptable WHERE component_id = 1";
                        $result = $con->query($rotc_group_query);

                        if ($result->num_rows > 0) {
                            ?>
                            <div id="rotcgroupSection" style="display: none;">
                                <div class="form-group">
                                    <label for="rotcgroup">Group</label>
                                    <select class="form-control" id="rotcgroup" name="rotcgroup">
                                        <?php
                                        // Output the group options
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . $row["group_id"] . '">' . $row["group_name"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                        }

                        // Query the database to retrieve CWTS groups
                        $cwts_group_query = "SELECT group_id, group_name FROM grouptable WHERE component_id = 2";
                        $result = $con->query($cwts_group_query);

                        if ($result->num_rows > 0) {
                            ?>
                            <div id="cwtsgroupSection" style="display: none;">
                                <div class="form-group">
                                    <label for="cwtsgroup">Group</label>
                                    <select class="form-control" id="cwtsgroup" name="cwtsgroup">
                                        <?php
                                        // Output the group options
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . $row["group_id"] . '">' . $row["group_name"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div id="specificSection" style="display: none;">
                        <div class="form-group">
                            <label for="specificRecipients">Specific Recipients</label>
                            <input type="text" class="form-control" id="specificRecipients" name="specificRecipients" placeholder="Enter recipient IDs or usernames">                           
                            <!-- Hidden input field to store the selected recipients -->
                            <input type="hidden" id="hiddenspecificRecipients" name="hiddenspecificRecipients" value="">
                            <div id="suggestionsContainer">
                            <div id="suggestions"></div>
                            </div>
                            <div id="recipientsContainer"></div>
                        </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="send_announcement" id="submit-button">Send</button>
                        </div>
                    </form>
                    <!-- End of Announcement Form -->
                </div>
            </div>
        </div>
    </div>
    <!-- End of Sending announcement modal -->
</section>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
<script>
    $(document).ready(function () {
        $('#recipient').on('change', function () {
            var recipientOption = $(this).val();

            if (recipientOption === 'rotcgroups') {
                $('#rotcgroupSection').show();
                $('#cwtsgroupSection').hide();
                $('#specificSection').hide();
            } else if (recipientOption === 'cwtsgroups') {
                $('#rotcgroupSection').hide();
                $('#cwtsgroupSection').show();
                $('#specificSection').hide();
            }else if (recipientOption === 'specific') {
                $('#rotcgroupSection').hide();
                $('#ctwsgroupSection').hide();
                $('#specificSection').show();
            } else {
                $('#rotcgroupSection').hide();
                $('#cwtsgroupSection').hide();
                $('#specificSection').hide();
            }
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
  var selectedRecipients = []; // Array to store the selected recipients

  $('#specificRecipients').on('input', function() {
    var input = $(this).val();

    if (input.length > 0) {
      $.ajax({
        type: 'POST',
        url: 'fetch_recipients.php',
        data: { input: input },
        success: function(response) {
          var suggestionsContainer = $('#suggestions');
          suggestionsContainer.html(response);

          var suggestions = suggestionsContainer.find('.suggestion');
          if (suggestions.length > 0) {
            suggestionsContainer.show();
            if (suggestions.length > 5) {
              suggestions.slice(5).hide();
            }

            suggestionsContainer.off('click').on('click', '.suggestion', function() {
            var clickedSuggestion = $(this).text().trim();

            // Find the clicked suggestion's data attributes
            var userAccountId = $(this).data('user_account_id');
            var emailAddress = $(this).data('email_address');
            var fullName = $(this).data('full_name');

            // Create an object with the recipient's details
            var recipientData = {
            user_account_id: userAccountId,
            email_address: emailAddress,
            full_name: fullName
            };

            // Add the recipient's details to the selected recipients array if not already present
            var isAlreadySelected = selectedRecipients.some(function(recipient) {
            return recipient.user_account_id === userAccountId;
            });

            if (!isAlreadySelected) {
            selectedRecipients.push(recipientData);
            updateRecipientsContainer();
            }

            // Log the selected recipient's details
            console.log('User Account ID: ', userAccountId);
            console.log('Email Address: ', emailAddress);
            console.log('Full Name: ', fullName);

            // Clear the input field and hide suggestions
            $('#specificRecipients').val('');
            suggestionsContainer.empty().hide();

            // Log the array of selected recipients
            console.log('Selected Recipients:', selectedRecipients);
            // Assuming selectedRecipients is an array of objects

            // Convert the selectedRecipients array to a JSON string
            var selectedRecipientsString = JSON.stringify(selectedRecipients);
            

            // Set the value of the input field
            document.getElementById("hiddenspecificRecipients").value = selectedRecipientsString;

        });

            suggestions.each(function() {
              var suggestionText = $(this).text().trim();
              if (suggestionText.includes(input)) {
                $(this).show();
              } else {
                $(this).hide();
              }
            });
          } else {
            suggestionsContainer.hide();
          }
        }
      });
    } else {
      $('#suggestions').empty().hide();
    }
  });

  $('#recipientsContainer').on('click', '.remove-recipient', function() {
    var removedRecipient = $(this).parent().data('recipient');

    // Remove the recipient from the selected recipients
    var index = selectedRecipients.indexOf(removedRecipient);
    if (index !== -1) {
      selectedRecipients.splice(index, 1);
      updateRecipientsContainer();
    }
  });

  function updateRecipientsContainer() {
    var recipientsContainer = $('#recipientsContainer');
    recipientsContainer.empty();

    selectedRecipients.forEach(function(recipient) {
      var recipientElement = $('<div class="recipient">' + recipient.email_address + '<span class="remove-recipient">&times;</span></div>');
      recipientElement.data('recipient', recipient); // Store the recipient data as a data attribute
      recipientsContainer.append(recipientElement);
    });

    // Add a click event handler to the newly added "x" buttons
    recipientsContainer.find('.remove-recipient').off('click').on('click', function() {
      var removedRecipient = $(this).parent().data('recipient');
      var index = selectedRecipients.findIndex(function(recipient) {
        return recipient.user_account_id === removedRecipient.user_account_id;
      });

      if (index !== -1) {
        selectedRecipients.splice(index, 1);
        updateRecipientsContainer();
      }

      // Log the array of selected recipients
      console.log('Selected Recipients:', selectedRecipients);

      // Update the hidden input field value
    var selectedRecipientsString = JSON.stringify(selectedRecipients);
    document.getElementById("hiddenspecificRecipients").value = selectedRecipientsString;
    });
  }

  updateRecipientsContainer(); // Call the updateRecipientsContainer function initially
});
</script>
<script>
$(document).ready(function() {
    // Attach click event handler to the announcement rows
    $('.announcement-row').click(function() {
        // var announcementBatch = $(this).data('announcement-batch');
        var receiver = $(this).find('td[data-label="Receiver"]').text();
        var subject = $(this).find('td[data-label="Subject"]').text();
        var message = $(this).find('td[data-label="Message"]').text();
        var date = $(this).find('td[data-label="Date"]').text();

        // Update the modal with the announcement details
        // $('#modalAnnouncementBatch').text(announcementBatch);
        $('#modalReceiver').text(receiver);
        $('#modalSubject').text(subject);
        $('#modalMessage').text(message);
        $('#modalDate').text(date);

        // Show the modal
        $('#announcementModal').modal('show');
    });
});
</script>

</body>
</html>
