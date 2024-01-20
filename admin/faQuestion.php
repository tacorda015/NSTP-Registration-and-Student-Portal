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

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('./adminsidebar.php');

?>
        <div class="home-main-container">
            <div class="studentList-container">
                <div class="page-title">
                    <div class="titleContainer">
                        <span class="group_id">Frequently Asked Questions Page</span>
                    </div>
                </div>
                <div class="buttonsContainer">
                    <div class="buttonHolder">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addfaq">
                            <i class='bx bx-plus-circle' ></i>Add Questions
                        </button>
                    </div>
                </div>
                <!-- Start Of Add Frequently Asked Questions Into Group Modal -->
                <div class="modal fade" id="addfaq" tabindex="-1" aria-labelledby="addfaq" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addfaq">Add Questions</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="addFaqForm">
                                <div class="modal-body">
                                <!-- <form method="POST" action="add_faq.php"> Pointing to the PHP script that handles form submission -->
                                    <div class="form-group">
                                        <label for="faq_question">Question</label>
                                        <input type="text" class="form-control" id="faq_question" name="faq_question" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="faq_answer">Answer</label>
                                        <textarea class="form-control" id="faq_answer" name="faq_answer" rows="3" required></textarea>
                                    </div>
                                    <!-- You can include additional form fields here if needed -->
                                    <div class="form-group">
                                        <label for="faq_status">Status</label>
                                        <select class="form-control" id="faq_status" name="faq_status">
                                            <option value="1">Publish</option>
                                            <option value="0">Unpublish</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add FAQ</button> <!-- Submit button -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End Of Add Frequently Asked Questions Into Group Modal -->
                <?php
                // Retrieve the FAQ data from the database
                $sql = "SELECT faq_id, faq_question, faq_answer, faq_status FROM faqtable";
                $result = $con->query($sql);
                ?>
                <div class="modal fade" id="updatefaq" tabindex="-1" aria-labelledby="updatefaq" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updatefaqLabel">Update FAQ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <input type="hidden" id="update_faq_id" name="update_faq_id">
                                    </div>
                                    <div class="form-group">
                                        <label for="update_faq_question">Question</label>
                                        <input type="text" class="form-control" id="update_faq_question" name="update_faq_question" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="update_faq_answer">Answer</label>
                                        <textarea class="form-control" id="update_faq_answer" name="update_faq_answer" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="update_faq_status">Status</label>
                                        <select class="form-control" id="update_faq_status" name="update_faq_status">
                                            <option value="1">Publish</option>
                                            <option value="0">Unpublish</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="updateFaqButton">Update FAQ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tableContainer">
                    <table class="table table-sm caption-top">
                    <caption>List of Frequently Asked Questions</caption>
                        <thead class="custom-thead">
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Status</th>
                                <th class='thAction'>Action</th>
                            </tr>
                        </thead>
                        <tbody id="file-table-body">
                            <?php
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr id='faq-" . htmlspecialchars($row["faq_id"]) . "'>";
                                        echo "<td data-label='Question' data-faqid='" . htmlspecialchars($row["faq_id"]) . "'>" . htmlspecialchars($row["faq_question"]) . "</td>";
                                        echo "<td data-label='Answer'>" . htmlspecialchars($row["faq_answer"]) . "</td>";
                                        echo "<td data-label='Status'>" . ($row["faq_status"] == 1 ? "Publish" : "Unpublish") . "</td>";
                                        echo "<td data-label='Actions'>
                                                <div class='groupButton'>
                                                    <button type='button' class='btn btn-primary update-faq-button' data-bs-toggle='modal' data-bs-target='#updatefaq'>
                                                        <i class='bx bx-wrench'></i>Update
                                                    </button>
                                                    <button type='button' class='btn btn-danger delete-faq-button' data-faqid='" . htmlspecialchars($row["faq_id"]) . "'>
                                                        <i class='bx bx-trash' ></i>Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No FAQs found</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                // Close the result and connection
                $result->close();
                $con->close();
                ?>
            </div>
        </div>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Get the form element
    var form = document.getElementById('addFaqForm');

    // Get the modal element
    var addFaqModal = document.getElementById('addfaq');

    // Add event listener for modal show event
    addFaqModal.addEventListener('show.bs.modal', function () {
        // Reset the form fields
        form.reset();
    });

    // Add event listener for form submission
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Create a new FormData object
        var formData = new FormData(form);

        // Send an AJAX request to the server
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_faq.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = xhr.responseText;
                // Handle the response
                if (response === 'success') {
                    // FAQ added successfully
                    // Show a SweetAlert2 success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'FAQ Added',
                        text: 'The FAQ has been added successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Refresh the table to display the new entry
                    refreshTable();
                    // Close the modal
                    $('#addfaq').modal('hide');
                } else {
                    // Error occurred while adding the FAQ entry
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while adding the FAQ. Please try again.'
                    });
                }
            }
        };
        xhr.send(formData);
    });

    // Function to refresh the table using AJAX
    function refreshTable() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_faqs.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = xhr.responseText;
                // Update the table body with the new data
                document.getElementById('file-table-body').innerHTML = response;
            }
        };
        xhr.send();
    }

    // Initial table refresh on page load
    refreshTable();
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the update modal element
        var updateModal = document.getElementById('updatefaq');
        // Get the modal body element inside the update modal
        var updateModalBody = updateModal.querySelector('.modal-body');

        // Function to handle the click event on the "Update" button
        function handleUpdateButtonClick(event) {
            // Get the row containing the clicked button
            var row = event.target.closest('tr');
            // Get the data from the row cells
            var question = row.querySelector('[data-label="Question"]').textContent;
            var answer = row.querySelector('[data-label="Answer"]').textContent;
            var status = row.querySelector('[data-label="Status"]').textContent;
            var faqId = row.querySelector('[data-label="Question"]').getAttribute('data-faqid');

            // Populate the update modal form fields with the data
            var updateQuestionField = updateModalBody.querySelector('#update_faq_question');
            var updateAnswerField = updateModalBody.querySelector('#update_faq_answer');
            var updateStatusField = updateModalBody.querySelector('#update_faq_status');
            var faqIdField = updateModalBody.querySelector('#update_faq_id');

            updateQuestionField.value = question;
            updateAnswerField.value = answer;
            updateStatusField.value = (status === 'Publish' ? '1' : '0');
            faqIdField.value = faqId;
        }

        var tableBody = document.getElementById('file-table-body');
        tableBody.addEventListener('click', function (event) {
            if (event.target.classList.contains('update-faq-button')) {
            handleUpdateButtonClick(event);
            }
        });

        // Function to handle the update FAQ request
        function handleUpdateFaq() {
            // Get the updated values from the form
            var updateQuestion = updateModalBody.querySelector('#update_faq_question').value;
            var updateAnswer = updateModalBody.querySelector('#update_faq_answer').value;
            var updateStatus = updateModalBody.querySelector('#update_faq_status').value;
            var faqId = updateModalBody.querySelector('#update_faq_id').value;

            // Prepare the data to be sent in the AJAX request
            var formData = new FormData();
            formData.append('update_faq_id', faqId);
            formData.append('update_faq_question', updateQuestion);
            formData.append('update_faq_answer', updateAnswer);
            formData.append('update_faq_status', updateStatus);

            // Send an AJAX request to update the FAQ entry
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_faq.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = xhr.responseText;
                    // Handle the response
                    if (response === 'success') {
                        // FAQ updated successfully
                        // Show a SweetAlert2 success notification
                        Swal.fire({
                            icon: 'success',
                            title: 'FAQ Updated',
                            text: 'The FAQ has been updated successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function () {
                            window.location.href = 'faQuestion.php';
                        });

                        // Refresh the table to display the updated entry
                        refreshTable();
                        // Close the modal
                        $('#updatefaq').modal('hide');
                    } else {
                        // Error occurred while updating the FAQ entry
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the FAQ. Please try again.'
                        });
                    }
                }
            };
            xhr.send(formData);
        }

        // Add event listener for the "Update FAQ" button inside the update modal
        var updateFaqButton = updateModalBody.querySelector('#updateFaqButton');
        updateFaqButton.addEventListener('click', handleUpdateFaq);

        // Function to refresh the table using AJAX
            function refreshTable() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_faqs.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = xhr.responseText;
                    // Update the table body with the new data
                    document.getElementById('file-table-body').innerHTML = response;
                    // Reattach event listeners to the "Update" buttons in the table
                    updateButtons = document.querySelectorAll('.update-faq-button');
                    updateButtons.forEach(function (button) {
                        button.addEventListener('click', handleUpdateButtonClick);
                    });
                }
            };
            xhr.send();
        }

        // Initial table load
        refreshTable();
    });
    
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the delete modal element
        var deleteModal = document.getElementById('deletefaq');

        // Function to handle the click event on the "Delete" button
        function handleDeleteButtonClick() {
            // Get the row containing the clicked button
            var row = this.closest('tr');
            // Get the data from the row cells
            var question = row.querySelector('[data-label="Question"]').textContent;
            var faqId = row.querySelector('[data-label="Question"]').getAttribute('data-faqid');

            // Show a confirmation SweetAlert2 dialog before deleting the FAQ
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'You are about to delete the FAQ: ' + question,
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed deletion, proceed with AJAX request
                    deleteFaq(faqId);
                }
            });
        }

        // Attach event listeners to the "Delete" buttons in the table
        var deleteButtons = document.querySelectorAll('.delete-faq-button');
        deleteButtons.forEach(function (button) {
            button.addEventListener('click', handleDeleteButtonClick);
        });

        // Function to handle the delete FAQ request
        function deleteFaq(faqId) {
            // Prepare the data to be sent in the AJAX request
            var formData = new FormData();
            formData.append('delete_faq_id', faqId);

            // Send an AJAX request to delete the FAQ entry
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_faq.php', true);
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText === 'success') {
                    // FAQ deleted successfully
                    // Show a success SweetAlert2 notification
                    Swal.fire({
                        icon: 'success',
                        title: 'FAQ Deleted',
                        text: 'The FAQ has been deleted successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Remove the deleted row from the table
                    var deletedRow = document.getElementById('faq-' + faqId);
                    deletedRow.remove();
                } else {
                    // Error occurred while deleting the FAQ entry
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while deleting the FAQ. Please try again.'
                    });
                }
            };
            xhr.send(formData);
        }

        // Function to refresh the table using AJAX
        function refreshTable() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_faqs.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = xhr.responseText;
                    // Update the table body with the new data
                    document.getElementById('file-table-body').innerHTML = response;
                    // Reattach event listeners to the "Delete" buttons in the table
                    deleteButtons = document.querySelectorAll('.delete-faq-button');
                    deleteButtons.forEach(function (button) {
                        button.addEventListener('click', handleDeleteButtonClick);
                    });
                }
            };
            xhr.send();
        }

        // Initial table load
        refreshTable();
    });
</script>

    <script src="../asset/js/index.js"></script>
    <script src="../asset/js/topbar.js"></script>
  </body>
</html>
