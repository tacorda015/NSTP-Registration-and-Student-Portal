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
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
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
    .userdate{
        border: none;
        background: none;
        text-decoration: underline;
        font-weight: 600;
        color: #132c3e;
    }
    .userdate:hover{
        transform: translateY(-2px);
    }
    .userdate:active{
        transform: translateY(2px);
    }
</style>
        <div class="home-main-container">
            <div class="studentlist-container">
            <div id="loader-overlay" class="loader-overlay"></div>
            <div id="loader" class="loader">Sending <span></span></div>
                <div class="title-container">
                    <h2>Student List</h2>
                </div>
                <div class="header-container">
                    <div class="buttoncontainer d-flex flex-rows gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addstudentmodal">
                            Add Student
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Downloadmodal">
                        <i class="bx bx-download"></i>
                            Download List
                        </button>

                        <!-- <div class="download-button-container">
                            <a href="studentlist_download.php?" class="download-button btn btn-primary d-flex justify-content-center align-items-center gap-1"><i class="bx bx-download"></i>Student List</a>
                        </div> -->
                    </div>
                    <form method="get" enctype="multipart/form-data" action="studentlist.php">
                    <div style="display: flex;">  
                            <div class="search-container" style="position: relative;">
                                <!-- <input type="text" name="search" class="form-control" placeholder="Search..."> -->
                                <input id="search" style="padding-left: 25px;" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <span class="search-icon"></span>  
                            </div>
                                <div style="align-items: center; display: flex; margin-left: 10px;">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </div>
                    </form>
                    <!-- Start of modal -->
                    <div class="modal fade" id="addstudentmodal" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 style="text-align: center; padding: 5px 0;">Add Student</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" enctype="multipart/form-data" id="emailForm">
                                    <div class="modal-body" style="z-index: 0;">
                                        <div class="form-group">
                                            <label for="add_full_name">Full Name:</label>
                                            <input type="text" class="form-control" id="add_full_name" name="add_full_name" pattern='[A-Za-z.\s]+' required>
                                            <small id="fullnameError" style="color: red;"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="add_email_address">CvSU Email:</label>
                                            <input type="email" class="form-control" id="add_email_address" name="add_email_address" required>
                                            <small id="emailError" style="color: red;"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="add_student_number">Student Number:</label>
                                            <input type="text" class="form-control" id="add_student_number" name="add_student_number" required>
                                            <small id="studentNumberError" style="color: red;"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="component_id">Component:</label>
                                            <select class="form-control" id="component_id" name="add_component_id" required>
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
                                            <small id="componentIdError" style="color: red;"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="choosegroup">Group Name:</label>
                                            <select class="form-control" id="choosegroup" name="group_id" required></select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="sendButton" class="btn btn-primary" name="add_student" onclick="sendEmail()" disabled>Add Student</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- FOR DOWNLOAD -->
                    <div class="modal fade" id="Downloadmodal" tabindex="1" aria-labelledby="DownloadmodalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 style="text-align: center; padding: 5px 0;">Download List</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="get" action="studentlist_download.php" id="downloadForm">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="choosegroup">List:</label>
                                            <select class="form-control" id="download" name="download" required>
                                                <option value="" hidden>Choose List</option>
                                                <option value="all">All Student List</option>
                                                <option value="CWTS">CWTS Student List</option>
                                                <option value="ROTC">ROTC Student List</option>
                                                <option value="active">Active Student List</option>
                                                <option value="disabled">Disable Student List</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="downloadbutton" class="btn btn-primary" name="downloadbutton" disabled>Download</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End of modal -->
                </div>
                <?php
                // modal for updating student information
                if(isset($_POST['update_student'])) {
                    $user_account_id = mysqli_real_escape_string($con, $_POST['user_account_id']);
                    $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
                    $user_status = mysqli_real_escape_string($con, $_POST['user_status']);
                    $student_number = mysqli_real_escape_string($con, $_POST['student_number']);
                    $component_name = mysqli_real_escape_string($con, $_POST['component_name']);
                    $group_id = mysqli_real_escape_string($con, $_POST['group_id']);
                    
                    // Update the student record in the database
                    $update_query = "UPDATE useraccount SET full_name='$full_name', user_status='$user_status', student_number='$student_number', component_name='$component_name', group_id='$group_id' WHERE user_account_id='$user_account_id'";
                    
                    // Execute the update query
                    $result = mysqli_query($con, $update_query);
                
                    if($result) {
                        ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Update Successful',
                                text: 'The student record has been updated.',
                                showConfirmButton: false,
                                timer: 3000
                            }).then(function() {
                                window.location.href = 'studentlist.php';
                            });
                        </script>
                        <?php
                    } else {
                        ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: 'An error occurred while updating the student record:\n<?php echo mysqli_error($con); ?>'
                            });
                        </script>
                        <?php
                    }
                    exit();
                }

                $query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE t.role_account_id = 2";

                if(isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = mysqli_real_escape_string($con, $_GET['search']);
                    $query .= " AND t.full_name LIKE '%$search%' OR t.student_number LIKE '%$search%' OR t.component_name LIKE '%$search%' OR g.group_name LIKE '%$search%'";
                }

                $query .= " ORDER BY user_account_id DESC";

                $result = mysqli_query($con, $query);

                if (mysqli_num_rows($result) > 0) {
                    echo "<div class='table-container'>";
                    echo "<table class='responsive-table'>";
                    // echo "<thead><tr><th>Full Name</th><th>Student Number</th><th>Component Name</th><th>Group Name</th><th>Action</th></tr></thead>";
                    echo "<thead><tr><th>Full Name</th><th>Account Status</th><th>Component Name</th><th>Group Name</th><th>Action</th></tr></thead>";
                    echo "<tbody id='file-table-body'>";
                            // Loop through each record and add a row to the table for each one
                            while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td data-label='Full Name'>
                                        <form method='post' action='user_data.php'>
                                            <input type='hidden' name='use_account_id' value='".$row['user_account_id']."'>
                                            <button type='submit' class='userdate'>
                                                ".$row['full_name']."
                                            </button>
                                        </form>
                                    </td>";
                            // echo "<td data-label='Student Number'>{$row['student_number']}</td>";
                            echo "<td data-label='Account Status'>" . ucfirst($row['user_status']) . "</td>";
                            echo "<td data-label='Component Name'>{$row['component_name']}</td>";
                            echo "<td data-label='Group Name'>".($row['group_name'] ?? 'No group assigned')."</td>";
                            echo "<td data-label='Action'>
                                    <div class='action-btn'>
                                        <button style='border-radius: 0.25rem;' type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#studentupdatemodal".$row['student_number']."'>Update</button>
                                        <button type='submit' class='btn btn-danger' onclick='disablestudent(".$row['user_account_id'].");'>Disable</button>
                                    </div>
                                </td>";
                                // <button type='button' class='btn btn-danger' onclick='confirmDelete(". $row['student_number'] .");'>Delete</button>

                            echo "</tr>";
                            
                                echo "<div class='modal fade myModal' id='studentupdatemodal".$row['student_number']."' tabindex='-1' aria-labelledby='updatemodalLabel' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title' id='updatemodalLabel'>Update student Information</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close' onclick='location.reload();'></button>
                                        </div>
                                    <form method='post' enctype='multipart/form-data' action='studentlist.php'>
                                        <div class='modal-body'>
                                                <input type='hidden' name='user_account_id' value='".$row['user_account_id']."'>
                                                <div class='form-group'>
                                                    <label for='full_name'>Full Name:</label>
                                                    <input type='text' class='form-control' id='full_name' name='full_name' value='".$row['full_name']."' pattern='[A-Za-z.\s]+' required>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='student_number'>Student Number:</label>
                                                    <input type='text' class='form-control' id='student_number' name='student_number' value='".$row['student_number']."' readonly required>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='user_status'>Account Status:</label>
                                                    <select class='form-control' id='user_status' name='user_status' required>
                                                        <option value='active' ".(($row['user_status'] == 'active') ? 'selected' : '').">Active</option>
                                                        <option value='disabled' ".(($row['user_status'] == 'disabled') ? 'selected' : '')." hidden>Disabled</option>
                                                    </select>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='component_name'>Component:</label>
                                                    <input type='text' class='form-control' id='component_name' name='component_name' value='".$row['component_name']."' readonly required>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='updatechoosegroup'>Group Name:</label>
                                                    <select class='form-control updatechoosegroup' name='group_id' readonly>";
                                                        // Retrieve the list of groups for the selected component from the database
                                                        $group_id = $row['group_id'];
                                                        $group_component = $row['component_name'];
                                                        $component_id = ($group_component == 'ROTC') ? 1 : 2;
                                                        
                                                        $group_option_query = "SELECT * FROM grouptable WHERE component_id = $component_id";
                                                        $group_option_result = mysqli_query($con, $group_option_query);
                                                        if (mysqli_num_rows($group_option_result) > 0) {
                                                            $hasAssignedGroup = false;
                                                            while ($group_row = mysqli_fetch_assoc($group_option_result)) {
                                                                if ($group_row['group_id'] == $group_id) {
                                                                    echo "<option value='" . $group_row['group_id'] . "' selected>" . $group_row['group_name'] . "</option>";
                                                                    $hasAssignedGroup = true;
                                                                } else {
                                                                    echo "<option value='" . $group_row['group_id'] . "'>" . $group_row['group_name'] . "</option>";
                                                                }
                                                            }
                                                            if (!$hasAssignedGroup) {
                                                                echo "<option value='' selected hidden>No assigned group</option>";
                                                            }
                                                        } else {
                                                            echo "<option value='' selected hidden>No groups available</option>";
                                                        }
                                            echo "</select>
                                            <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal' id='closeModalBtn'>Close</button>
                                            
                                            <button type='submit' class='btn btn-primary' name='update_student'>Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>";
                    }
                echo '</tbody>
                </table>
            </div>';
            } else {
                echo "No records found.";
            }
            ?>
        </div>
        </div>
</section>
    </div>
    <script>
    document.getElementById('downloadForm').addEventListener('submit', function() {
        // Display sweet alert
        Swal.fire({
            icon: 'success',
            title: 'Download Successful',
            text: 'The student list has been downloaded successfully.',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = './studentlist.php';
        });
    });
</script>
<script src="./js/studentlist_deletestudent.js"></script>
<script src="./js/studentlist_loader.js"></script>
<script src="../asset/js/index.js"></script>
<script src="./js/search.js"></script>
<script src="./js/studentlist_component.js"></script>
<script src="../asset/js/topbar.js"></script>
<script>
    addSearchFunctionality('search', '.search-icon', 'studentlist.php');
</script>
<script>
    // Validate email address
    const emailInput = document.getElementById('add_email_address');
    const emailError = document.getElementById('emailError');

    const studentNumberInput = document.getElementById('add_student_number');
    const studentNumberError = document.getElementById('studentNumberError');

    const fullNameInput = document.getElementById('add_full_name');
    const fullnameError = document.getElementById('fullnameError');

    const componentIdInput = document.getElementById('component_id');
    // const groupIdInput = document.getElementById('choosegroup');

    const submitButton = document.querySelector('button[name="add_student"]');

    componentIdInput.addEventListener('change', validateForm);
    // groupIdInput.addEventListener('change', validateForm);

    emailInput.addEventListener('input', function() {
        const email = emailInput.value.trim();
        const emailPattern = /^[a-zA-Z0-9._-]+@cvsu\.edu\.ph$/;
        if (!emailPattern.test(email)) {
            emailError.textContent = 'Email should be in the format example@cvsu.edu.ph';
        } else {
            emailError.textContent = '';
        }
        validateForm();
    });

    studentNumberInput.addEventListener('input', function() {
        const studentNumber = studentNumberInput.value.trim();
        const studentNumberPattern = /^\d{9}$/;
        if (!studentNumberPattern.test(studentNumber)) {
            studentNumberError.textContent = 'Student number should be 9 digits';
        } else {
            studentNumberError.textContent = '';
        }
        validateForm();
    });

    fullNameInput.addEventListener('input', function() {
        const fullnameValue = fullNameInput.value.trim();
        if (fullnameValue === '') {
            fullnameError.textContent = 'Fill up this full name';
        } else {
            fullnameError.textContent = '';
        }
        validateForm();
    });

    function validateForm() {
        const componentId = componentIdInput.value;
        // const groupId = groupIdInput.value;
        const email = emailInput.value.trim();
        const studentNumber = studentNumberInput.value.trim();
        const fullnameValue = fullNameInput.value.trim();

        if (
            emailError.textContent === '' &&
            studentNumberError.textContent === '' &&
            fullnameError.textContent === '' &&
            componentId !== '' &&
            email !== '' &&
            studentNumber !== '' &&
            fullnameValue !== ''
        ) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }

    validateForm();

    // Get the select element and download button
  const selectElement = document.getElementById('download');
  const downloadButton = document.getElementById('downloadbutton');

  // Add event listener to the select element
  selectElement.addEventListener('change', function() {
    // Enable the download button if a selection is made
    if (selectElement.value !== '') {
      downloadButton.disabled = false;
    } else {
      // Disable the download button if no selection is made
      downloadButton.disabled = true;
    }
  });
    </script>



<!-- <script>
  // Get all the modal elements with class 'myModal'
var modals = document.querySelectorAll('.myModal');

// Iterate over the modals and attach an event listener to each one
modals.forEach(function(modal) {
  modal.addEventListener('hidden.bs.modal', function () {
    // Reload the current page
    location.reload();
  });
});
</script> -->

  </body>
</html>