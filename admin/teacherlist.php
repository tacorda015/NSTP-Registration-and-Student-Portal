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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
// Calling the sidebar
include_once('adminsidebar.php');
?> 
            <div class="home-main-container">
                <div class="studentList-container">
                <div id="loader-overlay" class="loader-overlay"></div>
                <div id="loader" class="loader">Sending <span></span></div>
                        <div class="page-title">
                            <div class="titleContainer">
                                <span class="group_id">CWTS Coordinator</span>
                            </div>
                            <form method="get" enctype="multipart/form-data" action="teacherlist.php">  
                                <div class="search-container">
                                    <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>
                                    <button class="btn btn-primary" type="submit"><i class='bx bx-search'></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="buttonsContainer">
                            <div class="buttonHolder">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addteachermodal">
                                    <i class='bx bx-user-plus'></i>Add Coordinator
                                </button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Downloadmodal">
                                <i class="bx bx-download"></i>
                                    Export Coordinator List
                                </button>
                            </div>
                        </div>
                        <!-- Start of modal -->
                        <div class="modal fade" id="addteachermodal" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" >
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h2 style="text-align: center; padding: 5px 0;">Add Coordinator</h2>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="post" enctype="multipart/form-data" id="emailForm">
                                        <div class="modal-body" style="max-height: 500px; overflow-y: scroll;">
                                            <div class="form-group mb-1">
                                                <label for="add_teacher_fname">First Name:</label>
                                                <input type="text" class="form-control" id="add_teacher_fname" name="add_teacher_fname" required placeholder="">
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="add_teacher_mname">Middle Name:</label>
                                                <input type="text" class="form-control" id="add_teacher_mname" name="add_teacher_mname" required placeholder="">
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="add_teacher_lname">Last Name:</label>
                                                <input type="text" class="form-control" id="add_teacher_lname" name="add_teacher_lname" required placeholder="">
                                            </div>
                                            <div class="dropdown-container d-flex flex-rows gap-2 my-2">
                                                <div class="form-group  flex-grow-1" style="min-width: 100px;">
                                                <label for="birthday">Birthday:</label>
                                                    <select class="form-control" id="birthday-month" name="birthday-month" required>
                                                        <option value="" selected disabled hidden>Month</option>
                                                        <option value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                </div>
                                                <div class="dropdown-container d-flex flex-rows gap-2">
                                                <div class="form-group" style="min-width: 100px;">
                                                    <label for="birthday" style="visibility: hidden; opacity: 0;">Birthday:</label>
                                                    <input type="text" class="form-control" placeholder="Day" name="birthday-day" id="birthday-day" oninput="restrictInputToRange(event, 1, 31)"/>
                                                </div>
                                                <div class="form-group" style="min-width: 100px;">
                                                    <label for="birthday" style="visibility: hidden; opacity: 0;">Birthday:</label>
                                                    <select class="form-control" id="birthday-year" name="birthday-year" required>
                                                    <option value="" selected disabled hidden>Year</option>
                                                    <?php
                                                       $currentYear = date("Y");
                                                       $newcurrentYear = $currentYear - 10;
                                                       for ($year = $newcurrentYear; $year >= $newcurrentYear - 30; $year--) {
                                                       echo "<option value='".$year."'>".$year."</option>";
                                                       }
                                                    ?>
                                                    </select>
                                                    <span id="birthday-year-error" class="error-message"></span>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-rows gap-2">
                                                <div class="form-group mb-1" style="flex: 1;">
                                                    <label for="add_teacher_contactnumber">Contact Number:</label>
                                                    <input type="text" class="form-control" id="add_teacher_contactnumber" name="add_teacher_contactnumber" required>
                                                    <small id="contactNumberError" style="color: red;"></small>
                                                </div>
                                                <div class="form-element" style="width: 80px;">
                                                    <label for="add_teacher_contactnumber">Gender:</label>
                                                    <select class="form-control" id="gender" name="gender" style="width: 90px;" required>
                                                    <option value="" selected disabled hidden>Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="add_teacher_saddress">Street/Baranggay:</label>
                                                <input type="text" class="form-control" id="add_teacher_saddress" name="add_teacher_saddress" required>                                      
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="add_teacher_caddress">City/Municipality:</label>
                                                <input type="text" class="form-control" id="add_teacher_caddress" name="add_teacher_caddress" required>                                      
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="add_teacher_paddress">Province:</label>
                                                <input type="text" class="form-control" id="add_teacher_paddress" name="add_teacher_paddress" required>                                      
                                            </div>
                                            <div class="form-group mb-1">
                                            <label for="add_teacher_course">Department:</label>
                                            <select class="form-control" id="course" name="course" required>
                                                <option value="" selected disabled hidden>Department</option>
                                                <option value="Department of Art and Sciences">Department of Art and Sciences</option>
                                                <option value="Department of Computer Studies">Department of Computer Studies</option>
                                                <option value="Department of Industrial Technology">Department of Industrial Technology</option>
                                                <option value="Department of Engineering">Department of Engineering</option>
                                                <option value="Department of Management Studies">Department of Management Studies</option>
                                                <option value="Department of Teacher Education">Department of Teacher Education</option>
                                                <option value="Office">Office</option>
                                            </select>
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="add_teacher_email">CvSU Email:</label>
                                                <input type="email" class="form-control" id="add_teacher_email" name="add_teacher_email" required>
                                                <small id="emailError" style="color: red;"></small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button"class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" id="sendButton" class="btn btn-primary" name="add_teacher" onclick="sendEmail()" disabled>Add Coordinator</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- End of modal -->
                        <!-- FOR DOWNLOAD -->
                        <div class="modal fade" id="Downloadmodal" tabindex="1" aria-labelledby="DownloadmodalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h2 style="text-align: center; padding: 5px 0;">Download List</h2>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="get" action="incharge_download.php" id="downloadForm">
                                        <div class="modal-body">
                                            <input type="hidden" name="component_name" value="CWTS">
                                            <?php
                                                $schoolyear_query = "SELECT * FROM schoolyeartable";
                                                $schoolyear_result = mysqli_query($con, $schoolyear_query);

                                                if ($schoolyear_result) {
                                                    echo'<div class="form-group">';
                                                    echo '<label for="schoolyear">Select School Year:</label>';
                                                    echo '<select name="schoolyear_id" class="form-control" id="schoolyear">';
                                                    
                                                    while ($row = mysqli_fetch_assoc($schoolyear_result)) {
                                                        $schoolyearID = $row['schoolyear_id'];
                                                        $schoolyearStart = $row['schoolyear_start'];
                                                        $schoolyearEnd = $row['schoolyear_end'];
                                                        $semester_id = $row['semester_id'];
                                                    
                                                        $semesterText = ($semester_id == 1) ? 'First Semester' : 'Second Semester';
                                                    
                                                        echo '<option value="' . $schoolyearID . '">' . $schoolyearStart . ' - ' . $schoolyearEnd . ' - ' . $semesterText . '</option>';
                                                    }
                                                    
                                                    echo '</select>';
                                                    echo'</div>';
                                                    } else {
                                                    echo 'Error: ' . mysqli_error($con);
                                                    }
                                            ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" id="downloadbutton" class="btn btn-primary" name="downloadbutton">Download</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- End of modal -->
                        <?php
                            // if(isset($_POST['delete_teacher'])) {
                            //     $teacher_id = mysqli_real_escape_string($con, $_POST['delete_teacher']);
                                
                            //     // Delete the teacher record from the database
                            //     $delete_query = "DELETE FROM teachertable WHERE teacher_id='$teacher_id'";
                            //     $result = mysqli_query($con, $delete_query);
                                
                            //     if($result) {
                            //         header('Location: teacherlist.php');
                            //         ob_end_flush();
                            //     } else {
                            //         echo "<script>alert('Error: " . mysqli_error($con) . "')</script>";
                            //     }
                            // }

                            $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                            $schoolyear_result = $con->query($schoolyear_query);
                            $schoolyear_data = $schoolyear_result->fetch_assoc();
                            if($schoolyear_data){
                                $schoolyear_id = $schoolyear_data['schoolyear_id'];
                                $semester_id = $schoolyear_data['semester_id'];

                                // Pagination setup
                                $recordsPerPage = 10;
                                if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                                    $currentPage = intval($_GET['page']);
                                } else {
                                    $currentPage = 1;
                                }

                                $query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE t.role_account_id = 3";

                                if (isset($_GET['search']) && !empty($_GET['search'])) {
                                    $search = mysqli_real_escape_string($con, $_GET['search']);
                                    $query .= " AND (t.full_name LIKE '%$search%' OR t.student_number LIKE '%$search%' OR t.contactNumber LIKE '%$search%' OR t.component_name LIKE '%$search%' OR g.group_name LIKE '%$search%')";
                                }

                                // Add the condition for schoolyear_id and semester_id
                                $query .= " AND t.component_name = 'CWTS' AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";

                                $query .= " ORDER BY t.user_account_id DESC";

                                // Modify the query to include LIMIT and OFFSET clauses for pagination
                                $offset = ($currentPage - 1) * $recordsPerPage;
                                $query .= " LIMIT $recordsPerPage OFFSET $offset";

                                $result = mysqli_query($con, $query);

                                if (!$result) {
                                    echo "Error: " . mysqli_error($con);
                                    // Handle the error appropriately, such as displaying an error message or logging it
                                    // You can also consider using a die() statement to halt the script execution
                                    die();
                                }

                                if (mysqli_num_rows($result) > 0) {
                                    echo "<div class='tableContainer'>";
                                    echo "<table class='table table-sm caption-top'>";
                                    echo "<caption>List of Coordinator</caption>";
                                    echo "<thead class=\"custom-thead\"><tr><th>Full Name</th><th>Account Status</th><th>Contact Number</th><th>Group name</th><th class='thAction'>Action</th></tr></thead>";
                                    echo "<tbody id='file-table-body'>";
                                    // Loop through each record and add a row to the table for each one
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td data-label='Full Name'>
                                            <form method='post' action='user_data.php'>
                                                <input type='hidden' name='use_account_id' value='".$row['user_account_id']."'>
                                                <button type='submit' class='clickableCharacter'>
                                                    ".$row['full_name']."
                                                </button>
                                            </form>
                                        </td>";
                                        echo "<td data-label='Account Status'>" . ucfirst($row['user_status']) . "</td>";
                                        echo "<td data-label='Contact Number'>{$row['contactNumber']}</td>";
                                        // echo "<td data-label='Email'>{$row['teacher_email']}</td>";
                                        echo "<td data-label='Group name'>".($row['group_name'] ?? 'No group assigned')."</td>";
                                        echo "<td data-label='Action'>
                                                <div class='groupButton'>
                                                    <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#teacherupdatemodal".$row['user_account_id']."'>
                                                        <i class='bx bx-wrench'></i>Update
                                                    </button>";
                                                    if($row['user_status'] != 'disabled'){
                                                        echo "<button type='button' class='btn btn-danger' onclick='disableteacher(" . $row['user_account_id'] . ");'>
                                                                <i class='bx bx-user-x'></i>Disable
                                                            </button>";
                                                    }                                                    
                                                echo"</div>
                                            </td>";
                                            // <button type='button' class='btn btn-danger' onclick='confirmDelete(". $row['teacher_id'].");'>Delete</button>
                                        echo "</tr>";
                                
                                echo "<div class='modal fade' id='teacherupdatemodal".$row['user_account_id']."' tabindex='-1' aria-labelledby='updatemodalLabel' aria-hidden='true'>
                                        <div class='modal-dialog'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='updatemodalLabel'>Update Coordinator Information</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>
                                                <form method='post' enctype='multipart/form-data' action='teacherlist.php'>
                                                    <div class='modal-body'>
                                                        <input type='hidden' id='teacher_id_".$row['user_account_id']."' name='teacher_id' value='".$row['user_account_id']."'>
                                                        <input type='hidden' id='teacher_uniquenumber_".$row['user_account_id']."' name='teacher_uniquenumber' value='".$row['student_number']."'>
                                                        <div class='form-group'>
                                                            <label for='firstname'>First Name:</label>
                                                            <input type='text' class='form-control' id='firstname_".$row['user_account_id']."' name='firstname' value='".$row['firstname']."' pattern='[A-Za-z.\s]+' required>
                                                        </div>
                                                        <div class='form-group'>
                                                            <label for='middlename'>Middle Name:</label>
                                                            <input type='text' class='form-control' id='middlename_".$row['user_account_id']."' name='middlename' value='".$row['middlename']."' pattern='[A-Za-z.\s]+' required>
                                                        </div>
                                                        <div class='form-group'>
                                                            <label for='surname'>Surname:</label>
                                                            <input type='text' class='form-control' id='surname_".$row['user_account_id']."' name='surname' value='".$row['surname']."' pattern='[A-Za-z.\s]+' required>
                                                        </div>
                                                        <div class='form-group'>
                                                        <label for='add_teacher_course'>Course:</label>
                                                        <select class='form-control' id='course_".$row['user_account_id']."' name='course'>
                                                            <option value='".$row['course']."' selected disabled>".($row['course'] ? $row['course'] : 'No Department')."</option>
                                                            <option value='Department of Art and Sciences'>Department of Art and Sciences</option>
                                                            <option value='Department of Computer Studies'>Department of Computer Studies</option>
                                                            <option value='Department of Industrial Technology'>Department of Industrial Technology</option>
                                                            <option value='Department of Engineering'>Department of Engineering</option>
                                                            <option value='Department of Management Studies'>Department of Management Studies</option>
                                                            <option value='Department of Teacher Education'>Department of Teacher Education</option>
                                                            <option value='Office'>Office</option>
                                                            </select>
                                                        </div>
                                                        <div class='form-group'>
                                                            <label for='user_status'>Account Status:</label>
                                                            <select class='form-control' id='user_status_".$row['user_account_id']."' name='user_status' required >
                                                            ";
                                                            if(($row['user_status'] == 'active')){
                                                                echo"<option value='active' ".(($row['user_status'] == 'active') ? 'selected' : '').">Active</option>
                                                                <option value='disabled' ".(($row['user_status'] == 'disabled') ? 'selected' : '')." hidden>Disabled</option>";
                                                            }else{
                                                                echo"<option value='disabled' ".(($row['user_status'] == 'disabled') ? 'selected' : '')." hidden>Disabled</option>
                                                                <option value='active' ".(($row['user_status'] == 'active') ? 'selected' : '').">Active</option>";
                                                            }
                                                                
                                                            echo"</select>
                                                        </div>
                                                        <div class='form-group'>
                                                            <label for='update_teacher_contactnumber'>Contact Number:</label>
                                                            <input type='text' class='form-control update_teacher_contactnumber' id='update_teacher_contactnumber_".$row['user_account_id']."' name='update_teacher_contactnumber' value='".$row['contactNumber']."' required>
                                                            <small class='update_contactNumberError' style='color: red;'></small>
                                                        </div>
                                                        <div class='form-group'>
                                                            <label for='update_teacher_email'>Email:</label>
                                                            <input type='email' class='form-control update_teacher_email' id='update_teacher_email_".$row['user_account_id']."' name='update_teacher_email' value='".$row['email_address']."' required>
                                                            <small class='update_emailError' style='color: red;'></small>
                                                        </div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                                        <button type='submit' class='btn btn-primary' id='updateButton' name='update_teacher' data-teacher-id='".$row['user_account_id']."'>Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        </div>";
                                }
                                // Close the HTML table
                                echo '
                                </tbody>
                        </table>
                    </div>';

                    // Pagination links using Bootstrap
                    echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                    <ul class='pagination justify-content-center'>";

                    // Determine the total number of pages
                    $totalRecordsQuery = "SELECT COUNT(*) as total FROM useraccount t 
                                LEFT JOIN grouptable g ON t.group_id = g.group_id 
                                WHERE t.role_account_id = 3";

                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = mysqli_real_escape_string($con, $_GET['search']);
                    $totalRecordsQuery .= " AND (t.full_name LIKE '%$search%' OR t.student_number LIKE '%$search%' OR t.component_name LIKE '%$search%' OR g.group_name LIKE '%$search%')";
                    }

                    // Add the condition for schoolyear_id and semester_id
                    $totalRecordsQuery .= " AND t.component_name = 'CWTS' AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";

                    $totalRecordsResult = mysqli_query($con, $totalRecordsQuery);
                    $totalRecordsRow = mysqli_fetch_assoc($totalRecordsResult);
                    $totalRecords = $totalRecordsRow['total'];

                    $totalPages = ceil($totalRecords / $recordsPerPage);

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
                } else {
                    echo '<h2 style="text-align:center;">No Records Found.</h2>';
                }
                }else{
                    echo '<h2 style="text-align:center;">No School Year Yet.</h2>';
                }
            ?>
            </div>
        </div>
    </section>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    var updateButtons = document.querySelectorAll("[name='update_teacher']");
    updateButtons.forEach(function(button) {
      button.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent the form from submitting normally
        var teacher_id = this.getAttribute("data-teacher-id");
        Updateinfo(teacher_id);
      });
    });
  });
    
   function Updateinfo(teacher_id) {

        // Send the form data asynchronously
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "updateteacherlist.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        // Success
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 15000
                        }).then(function () {
                            window.location.href = 'teacherlist.php';
                        });
                    } else {
                        // Error
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                            showConfirmButton: false,
                            timer: 15000
                        }).then(function () {
                            window.location.href = 'teacherlist.php';
                        });
                    }
                } else {
                    // Error
                    Swal.fire({
                        title: "Error",
                        text: "Updating Information could not save. Please try again.",
                        icon: "error"
                    }); // Display error message
                }
            }
        };

        var teacher_id = document.getElementById("teacher_id_" + teacher_id).value;
        var teacher_uniquenumber = document.getElementById("teacher_uniquenumber_" + teacher_id).value;
        var firstname = document.getElementById("firstname_" + teacher_id).value;
        var middlename = document.getElementById("middlename_" + teacher_id).value;
        var surname = document.getElementById("surname_" + teacher_id).value;
        var course = document.getElementById("course_" + teacher_id).value;
        var user_status = document.getElementById("user_status_" + teacher_id).value;
        var update_teacher_contactnumber = document.getElementById("update_teacher_contactnumber_" + teacher_id).value;
        var update_teacher_email = document.getElementById("update_teacher_email_" + teacher_id).value;


        var data = "teacher_id=" + encodeURIComponent(teacher_id) + "&teacher_uniquenumber=" + encodeURIComponent(teacher_uniquenumber) + "&firstname=" + encodeURIComponent(firstname) + "&middlename=" + encodeURIComponent(middlename) + "&surname=" + encodeURIComponent(surname) + "&course=" + encodeURIComponent(course) + "&user_status=" + encodeURIComponent(user_status) + "&update_teacher_contactnumber=" + encodeURIComponent(update_teacher_contactnumber) + "&update_teacher_email=" + encodeURIComponent(update_teacher_email);


        xhr.send(data);
    }
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
        xhr.open("POST", "send_addteacher.php", true);
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
                            window.location.href = 'teacherlist.php';
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
                            window.location.href = 'teacherlist.php';
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

        var teacher_fname = document.getElementById("add_teacher_fname").value;
        var teacher_lname = document.getElementById("add_teacher_lname").value;
        var teacher_mname = document.getElementById("add_teacher_mname").value;
        var birthday_month = document.getElementById("birthday-month").value;
        var birthday_day = document.getElementById("birthday-day").value;
        var birthday_year = document.getElementById("birthday-year").value;
        var teacher_contactnumber = document.getElementById("add_teacher_contactnumber").value;
        var teacher_saddress = document.getElementById("add_teacher_saddress").value;
        var teacher_caddress = document.getElementById("add_teacher_caddress").value;
        var teacher_paddress = document.getElementById("add_teacher_paddress").value;
        var teacher_email = document.getElementById("add_teacher_email").value;
        var teacher_department = document.getElementById("course").value;
        var teacher_gender = document.getElementById("gender").value;

        var data = "teacher_fname=" + encodeURIComponent(teacher_fname) + "&teacher_lname=" + encodeURIComponent(teacher_lname) + "&teacher_mname=" + encodeURIComponent(teacher_mname) + "&birthday-month=" + encodeURIComponent(birthday_month) + "&birthday-day=" + encodeURIComponent(birthday_day) + "&birthday-year=" + encodeURIComponent(birthday_year) + "&teacher_contactnumber=" + encodeURIComponent(teacher_contactnumber) + "&teacher_saddress=" + encodeURIComponent(teacher_saddress) + "&teacher_caddress=" + encodeURIComponent(teacher_caddress) + "&teacher_paddress=" + encodeURIComponent(teacher_paddress) + "&teacher_email=" + encodeURIComponent(teacher_email) + "&teacher_department=" + encodeURIComponent(teacher_department) + "&teacher_gender=" + encodeURIComponent(teacher_gender);


        xhr.send(data);
        // var formData = new FormData(document.getElementById("emailForm"));
        // formData.append("add_student", ""); // Add the 'add_student' parameter to the form data
        // xhr.send(formData);
    }
</script>
<script>
$(document).ready(function() {
  $(".modal").each(function() {
    var modal = $(this);
    var updateButton = modal.find('#updateButton');
    var contactNumberInput = modal.find('.form-control[name="update_teacher_contactnumber"]');
    var emailInput = modal.find('.form-control[name="update_teacher_email"]');
    var contactNumberError = modal.find('.update_contactNumberError');
    var emailError = modal.find('.update_emailError');

    function validateContactNumber() {
      var contactNumber = contactNumberInput.val();
      var contactNumberRegex = /^(09|\+639)\d{9}$/;
      var isValid = contactNumberRegex.test(contactNumber);

      if (!isValid) {
        contactNumberError.text("Please enter a valid contact number");
        updateButton.attr('disabled', 'disabled');
      } else {
        contactNumberError.text("");
        updateButton.removeAttr('disabled');
      }
    }

    function validateEmail() {
      var email = emailInput.val();
      var emailRegex = /^[A-Za-z0-9._%+-]+@cvsu\.edu\.ph$/;
      var isValid = emailRegex.test(email);

      if (!isValid) {
        emailError.text("Email should be in the format example@cvsu.edu.ph");
        updateButton.attr('disabled', 'disabled');
      } else {
        emailError.text("");
        updateButton.removeAttr('disabled');
      }
    }

    // Trigger validation on modal open
    modal.on('shown.bs.modal', function() {
      validateContactNumber();
      validateEmail();
    });

    // Trigger validation on input load
    validateContactNumber();
    validateEmail();

    // Trigger validation on input change
    contactNumberInput.on("input", validateContactNumber);
    emailInput.on("input", validateEmail);
  });
});

</script>


<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
<script src="./js/teacherlist_deleteteacher.js"></script>
<script>
    // FOR DATE IN BIRTHDAY
    function restrictInputToRange(event, min, max) {
        const input = event.target;
        const value = input.value;
        
        if (isNaN(value) || value < min || value > max) {
            input.value = '';
        }
    }
// const addTeacherForm = document.getElementById('emailForm');
// const inputFields = addTeacherForm.querySelectorAll('input, select');
// const addButton = document.getElementById('sendButton');

// inputFields.forEach(function (input) {
//   input.addEventListener('input', validateForm);
// });

// function validateForm() {
//   let isValid = true;

//   inputFields.forEach(function (input) {
//     if (input.tagName === 'SELECT' && input.value === '') {
//       isValid = false;
//     } else if (input.tagName === 'INPUT' && input.value.trim() === '') {
//       isValid = false;
//     }
//   });

//   addButton.disabled = !isValid;
// }

// validateForm();
// const emailInput = document.getElementById('add_teacher_email');
//     const emailError = document.getElementById('emailError');

//     const contactNumberInput = document.getElementById('add_teacher_contactnumber');
//     const contactNumberError = document.getElementById('contactNumberError');

//     // const submitButton = document.querySelector('button[name="add_teacher"]');
//     const submitButton = document.getElementById('sendButton');

//     emailInput.addEventListener('input', function() {
//         const email = emailInput.value.trim();
//         const emailPattern = /^[a-zA-Z0-9._-]+@cvsu\.edu\.ph$/;
//         if (!emailPattern.test(email)) {
//             emailError.textContent = 'Email should be in the format example@cvsu.edu.ph';
//             submitButton.disabled = true; // Disable the submit button
//         } else {
//             emailError.textContent = '';
//         }
//         validateForms();
//     });

//     contactNumberInput.addEventListener('input', function() {
//         const contactNumber = contactNumberInput.value.trim();
//         const contactNumberPattern = /^(09|\+639)\d{9}$/;
//         if (!contactNumberPattern.test(contactNumber)) {
//             contactNumberError.textContent = 'Please enter a valid contact number';
//             submitButton.disabled = true; // Disable the submit button
//         } else {
//             contactNumberError.textContent = '';
//         }
//         validateForms();
//     });

//     function validateForms() {
//         if (emailError.textContent === '' && contactNumberError.textContent === '') {
//             submitButton.disabled = false; // Enable the submit button
//         } else {
//             submitButton.disabled = true; // Disable the submit button
//         }
//     }
document.addEventListener('DOMContentLoaded', function () {
    const addTeacherForm = document.getElementById('emailForm');
    const addButton = document.getElementById('sendButton');

    // Separate variables for input boxes and select elements
    const requiredInputBoxes = addTeacherForm.querySelectorAll('input[required]');
    const requiredSelects = addTeacherForm.querySelectorAll('select[required]');

    const validateForm = () => {
      let isValid = true;

      // Check input boxes
      requiredInputBoxes.forEach((input) => {
        isValid = isValid && input.value.trim() !== '';
      });

      // Check select elements
      requiredSelects.forEach((select) => {
        isValid = isValid && select.value !== '' && select.selectedIndex !== 0;
      });

      addButton.disabled = !isValid;
      validateEmail();
      validateContactNumber();
    };

    const validateEmail = () => {
      const emailInput = document.getElementById('add_teacher_email');
      const emailError = document.getElementById('emailError');
      const email = emailInput.value.trim();
      const emailPattern = /^[a-zA-Z0-9._-]+@cvsu\.edu\.ph$/;

      if (!emailPattern.test(email) || email.indexOf('@cvsu.edu.ph') === -1) {
        emailError.textContent = 'Email should be in the format example@cvsu.edu.ph';
        addButton.disabled = true; // Disable the submit button
      } else {
        emailError.textContent = '';
        // validateForm();
      }
    };

    const validateContactNumber = () => {
      const contactNumberInput = document.getElementById('add_teacher_contactnumber');
      const contactNumberError = document.getElementById('contactNumberError');
      const contactNumber = contactNumberInput.value.trim();
      const contactNumberPattern = /^(09|\+639)\d{9}$/;

      if (!contactNumberPattern.test(contactNumber)) {
        contactNumberError.textContent = 'Please enter a valid contact number';
        addButton.disabled = true
      } else {
        contactNumberError.textContent = '';
      }

    //   validateForm();
    };

    // Listen for input events on input boxes
    requiredInputBoxes.forEach((input) => {
      input.addEventListener('input', validateForm);
    });

    // Listen for input events on select elements
    requiredSelects.forEach((select) => {
      select.addEventListener('input', validateForm);
    });

    // Listen for input events on specific elements
    document.getElementById('add_teacher_email').addEventListener('input', validateEmail);
    document.getElementById('add_teacher_contactnumber').addEventListener('input', validateContactNumber);
  });
    </script>
  </body>
</html>