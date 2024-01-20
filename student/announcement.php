<?php
ob_start();
session_start();
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
$user_group_id = $useraccount_data['group_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
} 

// Calling the side bar
include_once('./studentsidebar.php')
?>
        <div class="home-main-container">
            <div class="studentList-container">
                
                <?php
                $query = "";

                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = mysqli_real_escape_string($con, $_GET['search']);
                    $query .= " AND (useraccount.full_name LIKE '%$search%' OR announcementtable.message LIKE '%$search%' OR announcementtable.created_at LIKE '%$search%' OR announcementtable.subject LIKE '%$search%')";
                }

                // Pagination setup
                $recordsPerPage = 10;
                if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                    $currentPage = intval($_GET['page']);
                } else {
                    $currentPage = 1;
                }

                // Calculate the offset for the current page
                $offset = ($currentPage - 1) * $recordsPerPage;

                // Fetch the announcement data with pagination
                $paginatedAnnouncementQuery = "SELECT announcementtable.sender_id, announcementtable.sender_id, announcementtable.recipient_id, announcementtable.view_status, announcementtable.announcement_id, announcementtable.announcement_batch, GROUP_CONCAT(DISTINCT announcementtable.reciever) AS receivers, announcementtable.recipient_id, announcementtable.subject, announcementtable.message, MAX(announcementtable.created_at) AS latest_date, useraccount.full_name FROM announcementtable";
                $paginatedAnnouncementQuery .= " LEFT JOIN useraccount ON useraccount.user_account_id = announcementtable.sender_id";
                $paginatedAnnouncementQuery .= " WHERE announcementtable.recipient_id = " . $user_data['user_account_id'];
                $paginatedAnnouncementQuery .= $query;
                $paginatedAnnouncementQuery .= " GROUP BY announcementtable.announcement_batch ORDER BY announcementtable.announcement_batch DESC";
                $paginatedAnnouncementQuery .= " LIMIT $recordsPerPage OFFSET $offset";

                $announcementResult = $con->query($paginatedAnnouncementQuery);

                ?>
                <div class="page-title">
                    <div class="titleContainer">
                        <span class="group_id">Announcement Page</span>
                    </div>
                    <form method="get" enctype="multipart/form-data" action="announce.php" autocomplete="off">  
                            <div class="search-container">
                                <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>
                                <button class="btn btn-primary" type="submit"><i class='bx bx-search'></i></button>
                            </div>
                    </form>
                </div>

                <div class="tableContainer">
                    <table class="table table-sm caption-top">
                    <caption>List of Announcement</caption>
                        <thead class="custom-thead">
                            <tr>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th class='thAction'>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($announcementResult->num_rows > 0) {
                                while ($row = $announcementResult->fetch_assoc()) {
                                    $announcementId = $row['announcement_id'];
                                    $senderName = $row['full_name'];
                                    $subject = $row['subject'];
                                    $receivers = ($row['recipient_id'] != $user_data['user_account_id']) ? $row['receivers'] : $user_data['user_account_id'];
                                    $sender_id = $row['sender_id'];
                                    $accountID = $user_data['user_account_id'];
                                    $message = $row['message'];
                                    $date = $row['latest_date'];

                                    if ($receivers == $user_data['user_account_id']) {
                                        $receivers = "Me";
                                    }
                                    if ($sender_id == $accountID) {
                                        $senderName = "Me";
                                        $fontWeight = ($row['view_status'] == 1) ? 'font-weight: 500;' : '';
                                    } else {
                                        $fontWeight = ($row['view_status'] == 1) ? 'font-weight: 800;' : '';
                                    }

                                    echo "<tr data-announcement-batch='$announcementId'>";
                                    echo "<td style='$fontWeight' data-label='Sender'>$senderName</td>";
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
                    ?>
                <!-- Pagination links using Bootstrap -->
                <nav aria-label="Page navigation" class = 'tablePagination'>
                    <ul class="pagination justify-content-center">
                        <?php
                        // Get the total number of records for pagination
                        $totalRecordsQuery = "SELECT COUNT(*) AS total FROM announcementtable WHERE announcementtable.recipient_id = " . $user_data['user_account_id'];
                        $totalRecordsQuery .= $query;
                        $totalRecordsResult = mysqli_query($con, $totalRecordsQuery);
                        $totalRecordsData = mysqli_fetch_assoc($totalRecordsResult);
                        $totalRecords = $totalRecordsData['total'];

                        // Calculate the total number of pages
                        $totalPages = ceil($totalRecords / $recordsPerPage);

                        echo "<li class='page-item " . ($currentPage == 1 ? 'disabled' : '') . "'>
                                <a class='page-link' href='?page=1" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&laquo;</a>
                            </li>";

                        for ($i = max(1, $currentPage - 2); $i <= min($currentPage + 2, $totalPages); $i++) {
                            echo "<li class='page-item " . ($i == $currentPage ? 'active' : '') . "'>
                                    <a class='page-link' href='?page=$i" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>$i</a>
                                </li>";
                        }

                        echo "<li class='page-item " . ($currentPage == $totalPages ? 'disabled' : '') . "'>
                                <a class='page-link' href='?page=$totalPages" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&raquo;</a>
                            </li>";
                        ?>
                    </ul>
                </nav>
                <?php } ?>
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
            </div>
        </div>
    </section>
</div>
<!-- Start of Modal View Details -->
<script>
$(document).ready(function() {
    // Attach click event handler to the announcement rows
    $('.announcement-row').click(function() {
        var sender = $(this).closest('tr').find('td[data-label="Sender"]').text();
        var subject = $(this).closest('tr').find('td[data-label="Subject"]').text();
        var message = $(this).closest('tr').find('td[data-label="Message"]').text();
        var date = $(this).closest('tr').find('td[data-label="Date"]').text();

        // Replace newline characters with HTML line break tags
        message = message.replace(/\n/g, '<br>');

        // Update the modal with the announcement details
        $('#modalSender').text(sender);
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
                row.find('td[data-label="Subject"]').css('font-weight', '500');
                row.find('td[data-label="Message"]').css('font-weight', '500');
                row.find('td[data-label="Date"]').css('font-weight', '500');

                // Update the student sidebar
                updateStudentSidebar();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });

        // Rest of the code to display the modal...
    });

    // Function to update the student sidebar
    function updateStudentSidebar() {
        // Make an AJAX request to retrieve the updated sidebar HTML
        $.ajax({
            url: 'get_sidebar.php', // Replace with the actual URL to retrieve the updated sidebar content
            type: 'GET',
            success: function(response) {
                // Update the student sidebar with the retrieved HTML
                $('.student-sidebar').html(response);
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }
});
</script>
<!-- End of Modal View Details -->

<!-- Start of Delete Button -->
<script>
    // Attach click event handler to the delete button DELETE
$('.delete-button').click(function() {
event.stopPropagation();
var announcementId = $(this).data('announcement-batch');
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
            url: 'delete_announce.php',
            type: 'POST',
            data: { announcementId: announcementId },
            success: function(response) {
                if (response === 'success') {
                    // Announcement deleted successfully, show success alert
                    Swal.fire({
                        title: 'Success',
                        text: 'The announcement has been deleted.',
                        icon: 'success'
                    }).then(() => {
                        // Optionally, you can reload the page or perform any other actions
                        location.reload();
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
<!-- End of Delete Button -->
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
<script src="./js/search.js"></script>
<script>
    addSearchFunctionality('search', '.search-icon', 'announce.php');
</script>
</body>
</html>