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


if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('./teachersidebar.php');
?>

        <div class="home-main-container">
            <div class="studentList-container">
                <div id="loader-overlay" class="loader-overlay"></div>
                <div id="loader" class="loader">Sending <span></span></div>
                <div class="page-title">
                    <div class="titleContainer">
                        <span class="group_id">Announcement Page</span>
                    </div>
                </div>
                <div class="buttonsContainer">
                    <div class="buttonHolder">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendannouncement_announcelist">
                            <i class='bx bx-mail-send' ></i>Send Announcement
                        </button>
                    </div>
                </div>
                <div class="tableContainer">
                    <table class="table table-sm caption-top">
                        <caption>List of Announcement</caption>
                        <thead class="custom-thead">
                            <tr>
                                <!-- <th>Sender</th> -->
                                <th>Sender</th>
                                <!-- <th>Reciever</th> -->
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th class='thAction'>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // $user_data['user_account_id'];
                            // $user_group_id = $user_data['group_id'];
                            $user_id = $user_data['user_account_id'];
                            $useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = {$user_id}";
                            $useraccount_result = $con->query($useraccount_query);
                            $useraccount_data = $useraccount_result->fetch_assoc();

                            $user_group_id = $useraccount_data['group_id'];
                            $query = "";
                            // Check if search term is provided
                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                $search = mysqli_real_escape_string($con, $_GET['search']);
                                $query .= " AND (ua.full_name LIKE '%$search%' OR at.message LIKE '%$search%' OR at.created_at LIKE '%$search%' OR at.subject LIKE '%$search%')";
                            }
                            $recordsPerPage = 10;
                            if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                                $currentPage = intval($_GET['page']);
                            } else {
                                $currentPage = 1;
                            }

                            // Calculate the offset for the current page
                            $offset = ($currentPage - 1) * $recordsPerPage;

                            $announcementQuery = "SELECT at.sender_id, at.announcement_id, at.view_status, at.announcement_batch, GROUP_CONCAT(DISTINCT at.reciever) AS receivers, at.recipient_id, at.subject, at.message, MAX(at.created_at) AS latest_date, ua.full_name, ua.user_account_id FROM announcementtable AS at";
                            $announcementQuery .= " LEFT JOIN useraccount AS ua ON ua.user_account_id = at.sender_id";
                            $announcementQuery .= " WHERE (at.sender_id = " . $user_data['user_account_id'] . " OR at.recipient_id = " . $user_data['user_account_id'] . ")";
                            $announcementQuery .= $query;
                            $announcementQuery .= " GROUP BY at.announcement_batch ORDER BY at.announcement_batch DESC";
                            $announcementQuery .= " LIMIT $recordsPerPage OFFSET $offset";
                            
                            $announcementResult = $con->query($announcementQuery);
                            

                            if ($announcementResult->num_rows > 0) {
                                while ($row = $announcementResult->fetch_assoc()) {
                                    $announcementBatch = $row['announcement_batch'];
                                    $announcementId = $row['announcement_id'];
                                    $sender_id = $row['sender_id'];
                                    $accountID = $user_data['user_account_id'];
                                    $senderName = $row['full_name'];
                                    $receivers = ($row['recipient_id'] != $user_data['user_account_id']) ? $row['receivers'] : $user_data['user_account_id'];
                                    $subject = $row['subject'];
                                    $message = $row['message'];
                                    $date = $row['latest_date'];

                                    // Overwrite $receivers with "Me" if it matches the current user's account ID
                                    if ($receivers == $accountID) {
                                        $receivers = "Me";
                                    }
                                    if ($sender_id == $accountID) {
                                        $senderName = "Me";
                                        $fontWeight = ($row['view_status'] == 1) ? 'font-weight: 500;' : '';
                                    }else{
                                        $fontWeight = ($row['view_status'] == 1) ? 'font-weight: 800;' : '';
                                    }
                                    
                                    // echo "<tr class='announcement-row' data-announcement-batch='$announcementBatch' data-sender-id='$sender_id'>"; this is also working
                                    echo "<tr data-announcement-batch='$announcementBatch' data-sender-id='$sender_id'>";
                                    echo "<td style='$fontWeight' data-label='Sender'>$senderName</td>";
                                    echo "<td data-label='Receiver' style='display: none; $fontWeight'>$receivers</td>";
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
                                    // echo "<td data-label='Action'><button type='button' class='btn btn-danger delete-button' data-announcement-batch='$announcementBatch' data-sender-id='$sender_id' data-account-id='$accountID' data-announcement-id='$announcementId'>Delete</button></td>"; // Delete button
                                    // echo "<td data-label='Action'><button type='button' class='btn btn-danger' data-announcement-batch='$announcementBatch'>Delete</button></td>"; // Delete button
                                    echo "</tr>";
                                }
                            } else {
                                // Display a message if no announcements are found
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
                <!-- <div style="opacity: 0; height: 10vh;">space Container</div> -->
                <!-- Start Modal Viewing Data-->
                <div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="announcementModalLabel">Announcement Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="d-flex flex-column"><strong>Sender:</strong> <span id="modalSender"></span></p>
                                <p class="d-flex flex-column"><strong>Receiver:</strong> <span id="modalReceiver"></span></p>
                                <p class="d-flex flex-column"><strong>Subject:</strong> <span id="modalSubject"></span></p>
                                <p class="d-flex flex-column"><strong>Message:</strong> <span id="modalMessage"></span></p>
                                <p class="d-flex flex-column"><strong>Date:</strong> <span id="modalDate"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Modal Viewing Data -->
                <!-- Start of Sending announcement modal -->
                <div class="modal fade" id="sendannouncement_announcelist" tabindex="1" aria-labelledby="sendannouncement_announcelist" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Send Announcement</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Start of Announcelist Form -->
                                <form method="post" enctype="multipart/form-data" id="emailForm">

                                    <div class="form-group">
                                        <label for="recipient">Recipient</label>
                                        <select class="form-control" id="recipient" name="recipient">
                                            <option value="all">All</option>
                                            <option value="specific">Specific Recipients</option>
                                            <!-- <option value="scheduling">Send Schedule</option> -->
                                        </select>
                                    </div>

                                    <div id="specificSection" style="display: none;">
                                        <div class="form-group">
                                            <label for="specificRecipients">Specific Recipients</label>
                                            <input type="text" class="form-control" id="specificRecipients" name="specificRecipients" placeholder="Enter recipient IDs or usernames" autocomplete="off">                           
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
                                        <input type="text" class="form-control" id="subject" name="subject" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="message">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                    </div>

                                    <div id="Scheduling" style="display: none;">
                                        <div class="form-group">
                                            <label for="activity_date">Activity Date</label>
                                            <input type="date" class="form-control" id="activity_date" name="activity_date">
                                        </div>

                                        <div class="form-group">
                                            <label for="activity_time">Activity Start Time</label>
                                            <input type="time" class="form-control" id="activity_time" name="activity_time">
                                        </div>

                                        <div class="form-group">
                                            <label for="activity_end_time">Activity End Time</label>
                                            <input type="time" class="form-control" id="activity_end_time" name="activity_end_time">
                                        </div>
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
    var subject = document.getElementById("subject").value;
    var hiddenspecificRecipientsValue = document.getElementById("hiddenspecificRecipients").value;
    var hiddenspecificRecipients = [];

    try {
        if (hiddenspecificRecipientsValue.trim() !== "") {
            hiddenspecificRecipients = JSON.parse(hiddenspecificRecipientsValue);
        }
    } catch (error) {
        console.error("Error parsing JSON: ", error);
    }

    var message = document.getElementById("message").value;
    var activity_date = document.getElementById("activity_date").value;
    var activity_time = document.getElementById("activity_time").value;
    var activity_end_time = document.getElementById("activity_end_time").value;
    var sendButton = document.getElementById("sendButton");

    console.log("recipient:", recipient);
    console.log("subject:", subject);
    console.log("hiddenspecificRecipients:", hiddenspecificRecipients);
    console.log("message:", message);
    console.log("activity_date:", activity_date);
    console.log("activity_time:", activity_time);
    console.log("activity_end_time:", activity_end_time);
    console.log("sendButton:", sendButton);

    if (recipient == "all" && (subject.trim() == "" || message.trim() == "")) {
        sendButton.disabled = true; // Disable the send button
    } else if (recipient == "specific" && (hiddenspecificRecipients.length == 0 || subject.trim() == "" || message.trim() == "")) {
        sendButton.disabled = true; // Disable the send button
    } else if (recipient == "scheduling" && (activity_date.trim() == "" || activity_time.trim() == "" || activity_end_time.trim() == "" || subject.trim() == "" || message.trim() == "")) {
        sendButton.disabled = true; // Disable the send button
    } else {
        sendButton.disabled = false; // Enable the send button
    }
}


    // Add event listeners to the form elements to trigger validation
    document.getElementById("recipient").addEventListener("change", validateForm);
    document.getElementById("subject").addEventListener("input", validateForm);
    document.getElementById("message").addEventListener("input", validateForm);
    // document.getElementById("hiddenspecificRecipients").addEventListener("input", validateForm);
    document.getElementById("specificRecipients").addEventListener("input", validateForm);
    document.getElementById("activity_date").addEventListener("input", validateForm);
    document.getElementById("activity_time").addEventListener("input", validateForm);
    document.getElementById("activity_end_time").addEventListener("input", validateForm);

    // Perform initial validation
    validateForm();
</script>

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
        xhr.open("POST", "send_addannouncelist.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        // Success
                        sendingEmail = false;
                        document.getElementById("loader-overlay").style.display = "none"; // Hide the loader overlay
                        document.getElementById("loader").style.display = "none"; // Hide the loader
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success"
                        }).then(function () {
                            window.location.href = 'announcelist.php';
                        });
                    } else {
                        // Error
                        sendingEmail = false;
                        document.getElementById("loader-overlay").style.display = "none"; // Hide the loader overlay
                        document.getElementById("loader").style.display = "none"; // Hide the loader
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error"
                        }).then(function () {
                            window.location.href = 'announcelist.php';
                        });
                    }
                } else {
                    // Error
                    document.getElementById("loader-overlay").style.display = "none"; // Hide the loader overlay
                    document.getElementById("loader").style.display = "none"; // Hide the loader
                    Swal.fire({
                        title: "Error",
                        text: "Email could not be sent. Please try again.",
                        icon: "error"
                    }); // Display error message
                }
            }
        };

        var recipient = document.getElementById("recipient").value;
        var specificRecipients = document.getElementById("specificRecipients").value;
        var hiddenspecificRecipients = document.getElementById("hiddenspecificRecipients").value;
        var subject = document.getElementById("subject").value;
        var message = document.getElementById("message").value;
        var activity_date = document.getElementById("activity_date").value;
        var activity_time = document.getElementById("activity_time").value;
        var activity_end_time = document.getElementById("activity_end_time").value;

        var data = "recipient=" + encodeURIComponent(recipient) + "&specificRecipients=" + encodeURIComponent(specificRecipients) + "&hiddenspecificRecipients=" + encodeURIComponent(hiddenspecificRecipients) + "&subject=" + encodeURIComponent(subject) + "&message=" + encodeURIComponent(message) + "&activity_date=" + encodeURIComponent(activity_date) + "&activity_time=" + encodeURIComponent(activity_time) + "&activity_end_time=" + encodeURIComponent(activity_end_time);

        xhr.send(data);
    }
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
<!-- Start of JS Suggestion -->
<script>
$(document).ready(function() {
  var selectedRecipients = []; // Array to store the selected recipients

  $('#specificRecipients').on('input', function() {
    var input = $(this).val();

    if (input.length > 0) {
      $.ajax({
        type: 'POST',
        url: 'fetch_announcelist_recipients.php',
        data: { 
            input: input
        },
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
            // Clear the input field and hide suggestions
            $('#specificRecipients').val('');
            suggestionsContainer.empty().hide();

            // Convert the selectedRecipients array to a JSON string
            var selectedRecipientsString = JSON.stringify(selectedRecipients);
            
            // Set the value of the input field
            document.getElementById("hiddenspecificRecipients").value = selectedRecipientsString;
            if (selectedRecipients.length != 0) {
            document.getElementById("sendButton").disabled = false;
            validateForm();
            }
        });

            suggestions.each(function() {
              var suggestionText = $(this).text().trim();
              var regex = new RegExp(input, 'i');
              var isNumber = !isNaN(input);

              if (isNumber) {
                $(this).show();
              } else if (regex.test(suggestionText)) {
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
    //   console.log('Selected Recipients:', selectedRecipients);

      // Update the hidden input field value
    var selectedRecipientsString = JSON.stringify(selectedRecipients);
    document.getElementById("hiddenspecificRecipients").value = selectedRecipientsString;
        if (selectedRecipients.length == 0) {
            document.getElementById("sendButton").disabled = true;
        }else{
            // document.getElementById("sendButton").disabled = false;
            validateForm();
        }
    });
    // Check if there are any selected recipients and disable the send button if none
  if (selectedRecipients.length == 0) {
    document.getElementById("sendButton").disabled = true;
  }else{
        // document.getElementById("sendButton").disabled = false;
        validateForm();
    }
  }

  updateRecipientsContainer(); // Call the updateRecipientsContainer function initially
});
</script>
<!-- End of JS Suggestion -->

<!-- Start of Modal hide/show -->
<script>
    $(document).ready(function () {
        $('#recipient').on('change', function () {
            var recipientOption = $(this).val();

            if (recipientOption === 'specific') {
                $('#specificSection').show();
                $('#Scheduling').hide();
                // Add 'required' attribute to specificSection inputs
                // $('#specificSection input').prop('required', true);
            } else if (recipientOption === 'scheduling') {
                $('#Scheduling').show();
                $('#specificSection').hide();
                // Add 'required' attribute to Scheduling inputs
                $('#Scheduling input').prop('required', true);
            } else {
                $('#specificSection').hide();
                $('#Scheduling').hide();
                // Remove 'required' attribute from all inputs
                $('input').prop('required', false);
            }
        });
    });
</script>
<!-- Start of Modal View Details -->
<script>
$(document).ready(function() {
    // Attach click event handler to the announcement rows
    $('.announcement-row').click(function() {
        var sender = $(this).closest('tr').find('td[data-label="Sender"]').text();
        var receiver = $(this).closest('tr').find('td[data-label="Receiver"]').text();
        var subject = $(this).closest('tr').find('td[data-label="Subject"]').text();
        var message = $(this).closest('tr').find('td[data-label="Message"]').text();
        var date = $(this).closest('tr').find('td[data-label="Date"]').text();

        // Replace newline characters with HTML line break tags
        message = message.replace(/\n/g, '<br>');

        // Update the modal with the announcement details
        $('#modalSender').text(sender);
        $('#modalReceiver').text(receiver);
        $('#modalSubject').text(subject);
        $('#modalMessage').html(message); // Use .html() to interpret the <br> tags
        $('#modalDate').text(date);

        // Show the modal
        $('#announcementModal').modal('show');
    });
});

$(document).ready(function() {
    // Attach click event handler to the announcement rows
    $('.announcement-row').click(function() {
        var row = $(this).closest('tr');
        var announcementId = $(this).data('announcement-id');

        // Update the view_status via AJAX
        $.ajax({
            url: 'update_view_status.php',
            type: 'POST',
            data: { announcementId: announcementId },
            success: function(response) {
                // Update the UI or perform any additional actions
                console.log(response);
                row.find('td[data-label="Sender"]').css('font-weight', '500');
                row.find('td[data-label="Receiver"]').css('font-weight', '500');
                row.find('td[data-label="Subject"]').css('font-weight', '500');
                row.find('td[data-label="Message"]').css('font-weight', '500');
                row.find('td[data-label="Date"]').css('font-weight', '500');

                // Update the teacher sidebar
                updateTeacherSidebar();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });

        // Rest of the code to display the modal...
    });

    // Function to update the teacher sidebar
    function updateTeacherSidebar() {
        // Make an AJAX request to retrieve the updated sidebar HTML
        $.ajax({
            url: 'get_sidebar.php', // Replace with the actual URL to retrieve the updated sidebar content
            type: 'GET',
            success: function(response) {
                // Update the teacher sidebar with the retrieved HTML
                $('.teacher-sidebar').html(response);
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }
});
</script>
<!-- Start of Delete Button -->
<script>
    // Attach click event handler to the delete button DELETE
$('.delete-button').click(function() {
event.stopPropagation();
var announcementBatch = $(this).data('announcement-batch');
var sender_id = $(this).data('sender-id');
var accountID = $(this).data('account-id');
var announcementId = $(this).data('announcement-id');

    console.log("announcementBatch:", announcementBatch);
    console.log("sender_id:", sender_id);
    console.log("accountID:", accountID);
    console.log("announcementId:", announcementId);
// Show confirmation dialog using SweetAlert2
Swal.fire({
    title: 'Confirmation',
    text: 'Are you sure you want to delete this announcement?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) {
        // User confirmed the deletion, send AJAX request to delete_announcement.php
        $.ajax({
            url: 'delete_announcelist.php',
            type: 'POST',
            data: { announcementBatch: announcementBatch, 
                    sender_id: sender_id,
                    accountID: accountID,
                    announcementId: announcementId},
            success: function(response) {
                if (response === 'success') {
                    // Announcement deleted successfully, show success alert
                    Swal.fire({
                        title: 'Success',
                        text: 'The announcement has been deleted.',
                        icon: 'success'
                    }).then(() => {
                        // Optionally, you can reload the page or perform any other actions
                        // Remove the deleted announcement row from the table
                        $('[data-announcement-batch="' + announcementBatch + '"]').remove();

                        // Log the values in the console
                        console.log('announcementBatch:', announcementBatch);
                        console.log('sender_id:', sender_id);
                        console.log('accountID:', accountID);
                        console.log('announcementId:', announcementId);
                    });
                } else {
                    // Error occurred while deleting the announcement, show error alert
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while deleting the announcement.',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                // AJAX request failed, show error alert
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while deleting the announcement.',
                    icon: 'error'
                });
            }
        });
    }
});
});
</script>
<script src="./js/search.js"></script>
<script>
    addSearchFunctionality('search', '.search-icon', 'announcelist.php');
</script>
</body>
</html>