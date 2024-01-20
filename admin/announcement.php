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
$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = $user_id";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$role_account_id = $useraccount_data['role_account_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
} 

// Calling the sidebar
include_once('adminsidebar.php');
?>
            <div class="home-main-container">
                <div class="studentList-container">
                    <div id="loader-overlay" class="loader-overlay"></div>
                    <div id="loader" class="loader">Sending <span></span></div>
                    <div class="page-title">
                        <div class="titleContainer">
                            <span class="group_id">Announcement Page</span>
                        </div>
                        <!-- <form method="get" enctype="multipart/form-data" action="announcement.php">
                            <div class="search-container">
                                <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form> -->
                    </div>
                <div class="buttonsContainer">
                    <div class="buttonHolder">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendannouncement">
                            <i class='bx bx-mail-send' ></i>Send Announcement</button>
                    </div>
                </div>
                <?php 
                // Pagination setup
                $recordsPerPage = 10;

                if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                    $currentPage = intval($_GET['page']);
                } else {
                    $currentPage = 1;
                }
                // Calculate the OFFSET for the query based on the current page number
                $offset = ($currentPage - 1) * $recordsPerPage;

                $query = "";
                // Check if search term is provided
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = mysqli_real_escape_string($con, $_GET['search']);
                    $query .= " WHERE reciever LIKE '%$search%' OR message LIKE '%$search%' OR created_at LIKE '%$search%' OR subject LIKE '%$search%'";
                }

                // Fetch the announcement data from the database (without LIMIT and OFFSET)
                $announcementQuery = "SELECT announcement_id, announcement_batch, GROUP_CONCAT(DISTINCT reciever) AS receivers, sender_id, sender_view, view_status, subject, message, MAX(created_at) AS latest_date FROM announcementtable WHERE (sender_id = " . $user_data['user_account_id'] . " OR recipient_id = " . $user_data['user_account_id'] . ")" . $query . " GROUP BY announcement_batch ORDER BY announcement_batch DESC";
                
                $announcementQuery .= " LIMIT $recordsPerPage OFFSET $offset";
                $announcementResult = $con->query($announcementQuery);


                // // Fetch the announcement data from the database
                // $announcementQuery = "SELECT announcement_id, announcement_batch, GROUP_CONCAT(DISTINCT reciever) AS receivers, sender_id, sender_view, view_status, subject, message, MAX(created_at) AS latest_date FROM announcementtable WHERE (sender_id = " . $user_data['user_account_id'] . " OR recipient_id = " . $user_data['user_account_id'] . ")" . $query . " GROUP BY announcement_batch ORDER BY announcement_batch DESC";
                // $announcementResult = $con->query($announcementQuery);
                ?>
                <div class="tableContainer">
                    <table class="table table-sm caption-top">
                    <caption>List of Announcement</caption>
                        <thead class="custom-thead">
                            <tr>
                                <th>Receiver</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th class='thAction'>Action</th> <!-- New column for delete button -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($announcementResult->num_rows > 0) {
                                while ($row = $announcementResult->fetch_assoc()) {
                                    $announcementBatch = $row['announcement_batch'];
                                    $announcementId = $row['announcement_id'];
                                    $accountID = $user_data['user_account_id'];
                                    $receivers = $row['receivers'];
                                    $sender_id = $row['sender_id'];
                                    $subject = $row['subject'];
                                    $message = $row['message'];
                                    $date = $row['latest_date'];

                                    // Overwrite $receivers with "Me" if it matches the current user's account ID
                                    if ($receivers == $accountID) {
                                        $receivers = "Me";
                                    }
                                    if ($sender_id == $accountID) {
                                        $senderName = "Me";
                                        $fontWeight = ($row['sender_view'] == 1) ? 'font-weight: 500;' : '';
                                    }else{
                                        $fontWeight = ($row['view_status'] == 1) ? 'font-weight: 800;' : '';
                                    }

                                    // echo "<tr class='announcement-row' data-announcement-batch='$announcementBatch'>";
                                    echo "<tr data-announcement-batch='$announcementBatch'>";
                                    echo "<td style='$fontWeight' data-label='Receiver'>$receivers</td>";
                                    echo "<td style='$fontWeight' data-label='Subject'>$subject</td>";
                                    echo "<td style='$fontWeight' data-label='Message'>" . nl2br($message) . "</td>";
                                    echo "<td style='$fontWeight' data-label='Date'>$date</td>";
                                    echo "<td data-label='Action'>
                                            <div class=\"groupButton\">
                                                <button type='button' class='btn btn-primary announcement-row' data-announcement-id='$announcementId'>
                                                    <i class='bx bx-show-alt'></i>View
                                                </button>
                                            </div>
                                        </td>";
                                    // echo "<td data-label='Action'><button type='button' class='btn btn-danger delete-button' data-announcement-batch='$announcementBatch'>Delete</button></td>"; // Delete button
                                    // echo "<td data-label='Action'><button type='button' class='btn btn-danger' data-announcement-batch='$announcementBatch'>Delete</button></td>"; // Delete button
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center;'>No announcements found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                 if ($announcementResult->num_rows > 0) {
                    // Get the total number of records for pagination
                        $totalRecordsQuery = "SELECT COUNT(DISTINCT announcement_batch) AS total FROM announcementtable WHERE (sender_id = " . $user_data['user_account_id'] . " OR recipient_id = " . $user_data['user_account_id'] . ")";
                        $totalRecordsQuery .= $query;

                        $totalRecordsResult = $con->query($totalRecordsQuery);
                        $totalRecordsData = $totalRecordsResult->fetch_assoc();
                        $totalRecords = $totalRecordsData['total'];

                        // Calculate the total number of pages
                        $totalPages = ceil($totalRecords / $recordsPerPage);

                        // Pagination links using Bootstrap
                        echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                                <ul class='pagination justify-content-end''>";

                        // Pagination links - Previous
                        $prevPage = $currentPage - 1;
                        echo "<li class='page-item " . ($currentPage == 1 ? 'disabled' : '') . "'>
                                <a class='page-link' href='?page=$prevPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&laquo; Previous</a>
                            </li>";

                        for ($i = max(1, $currentPage - 2); $i <= min($currentPage + 2, $totalPages); $i++) {
                            echo "<li class='page-item " . ($i == $currentPage ? 'active' : '') . "'>
                                    <a class='page-link' href='?page=$i" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>$i</a>
                                </li>";
                        }
                        
                    // Pagination links - Next
                    $nextPage = $currentPage + 1;
                    echo "<li class='page-item " . ($currentPage == $totalPages ? 'disabled' : '') . "'>
                            <a class='page-link' href='?page=$nextPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>Next &raquo;</a>
                        </li>
                    </ul>
                    </nav>";
                 }
                ?>

            <!-- Modal -->
            <div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="announcementModalLabel">Announcement Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="d-flex flex-column"><strong>Receiver:</strong> <span class="overflow-y-auto" style="max-height: 100px;" id="modalReceiver"></span></p>
                            <p class="d-flex flex-column"><strong>Subject:</strong> <span class="overflow-y-auto" style="max-height: 100px;" id="modalSubject"></span></p>
                            <p class="d-flex flex-column"><strong>Message:</strong> <span class="overflow-y-auto" style="max-height: 100px;" id="modalMessage"></span></p>
                            <p class="d-flex flex-column"><strong>Date:</strong> <span class="overflow-y-auto" style="max-height: 100px;" id="modalDate"></span></p>
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
                                <form method="post" enctype="multipart/form-data" id="emailForm">

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
                                            <input type="text" class="form-control" id="specificRecipients" name="specificRecipients" placeholder="Enter recipient IDs or usernames" required>                           
                                            <!-- Hidden input field to store the selected recipients -->
                                            <input type="hidden" id="hiddenspecificRecipients" name="hiddenspecificRecipients">
                                            <div id="suggestionsContainer">
                                                <div id="suggestions"></div>
                                            </div>
                                            <div id="recipientsContainer"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <input type="text" class="form-control" id="subject" name="subject" required autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label for="message">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="send_announcement" id="sendButton" onclick="sendEmail()">Send</button>
                                    </div>
                                </form>
                                <!-- End of Announcement Form -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Sending announcement modal -->
            </div>
        </div>
    </section>
</div>
<script>
    function validateForm() {
        var recipient = document.getElementById("recipient").value;
        var rotcgroup = document.getElementById("rotcgroup").value;
        var cwtsgroup = document.getElementById("cwtsgroup").value;
        var hiddenspecificRecipientsValue = document.getElementById("hiddenspecificRecipients").value;
        var hiddenspecificRecipients = [];

        try {
            if (hiddenspecificRecipientsValue.trim() !== "") {
                hiddenspecificRecipients = JSON.parse(hiddenspecificRecipientsValue);
            }
        } catch (error) {
            console.error("Error parsing JSON: ", error);
        }
        var subject = document.getElementById("subject").value;
        var message = document.getElementById("message").value;
        var sendButton = document.getElementById("sendButton");

        if (recipient == "all" && (subject.trim() == "" || message.trim() == "")) {
            sendButton.disabled = true; // Disable the send button
        } else if (recipient == "specific" && (hiddenspecificRecipients.length == 0 || subject.trim() == "" || message.trim() == "")) {
            sendButton.disabled = true; // Disable the send button
        } else if (recipient == "rotcgroups" && (rotcgroup == "" || (subject.trim() == "" || message.trim() == ""))) {
            sendButton.disabled = true; // Disable the send button
        } else if (recipient == "cwtsgroups" && (cwtsgroup == "" || (subject.trim() == "" || message.trim() == ""))) {
            sendButton.disabled = true; // Disable the send button
        } else if (recipient == "students" && (subject.trim() == "" || message.trim() == "")) {
            sendButton.disabled = true; // Disable the send button
        } else if ((recipient == "teachers" || recipient == "trainers") && (subject.trim() == "" || message.trim() == "")) {
            sendButton.disabled = true; // Disable the send button
        } else {
            sendButton.disabled = false; // Enable the send button
        }
    }

    // Add event listeners to the form elements to trigger validation
    document.getElementById("recipient").addEventListener("change", validateForm);
    document.getElementById("rotcgroup").addEventListener("change", validateForm);
    document.getElementById("cwtsgroup").addEventListener("change", validateForm);
    document.getElementById("subject").addEventListener("input", validateForm);
    document.getElementById("message").addEventListener("input", validateForm);
    document.getElementById("specificRecipients").addEventListener("input", validateForm);

    // Perform initial validation
    validateForm();
</script>
<!-- THIS IS FOR THE SIDEBAR MINIMIZE AND MAXIMIZE -->
<script src="../asset/js/index.js"></script>
<!-- THIS IS FOR TOPBAR CLICKING THE PICTURE -->
<script src="../asset/js/topbar.js"></script>

<!-- THIS IS FOR DISPLAYING OF LOADING AND ADD FUNCTION GOING TO DATABASE. -->
<script src="./js/announcement_loader.js"></script>
<script src="./js/announcement_delete.js"></script>
<script src="./js/announcement_hideAndShowInModal.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./js/announcement_suggestion.js"></script>
<script src="./js/announcement_viewAndNotification.js"></script>
<script src="./js/search.js"></script>
<script>
    addSearchFunctionality('search', '.search-icon', 'announcement.php');
</script>
</body>
</html>
