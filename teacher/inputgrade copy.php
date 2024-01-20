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

$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = {$user_id}";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$component_name = $useraccount_data['component_name'];
$student_number = $useraccount_data['student_number'];

if ($component_name == 'ROTC') {
    $rotc_query = "SELECT * FROM trainertable WHERE trainer_uniquenumber = {$student_number}";
    $rotc_result = $con->query($rotc_query);
    $incharge_data = $rotc_result->fetch_assoc();
} elseif ($component_name == 'CWTS') {
    $cwts_query = "SELECT * FROM teachertable WHERE teacher_uniquenumber = {$student_number}";
    $cwts_result = $con->query($cwts_query);
    $incharge_data = $cwts_result->fetch_assoc();
}

$group_id = $useraccount_data['group_id'];

// Calling the sidebar
include_once('./teachersidebar.php');

require '../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 
?>
            <div class='home-main-container'>
                <div class='modulelist-container'>
                    <?php

                    if ($group_id !== null) {
                        $group_query = "SELECT group_name FROM grouptable WHERE group_id = {$group_id}";
                        $group_result = $con->query($group_query);
                        $group_data = $group_result->fetch_assoc();

                        // Check if the form was submitted
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_grade'])) {

                            // Get the student number and grade from the form
                            $choiceupload = $_POST['choiceupload'];
                            $student_number = $_POST['student_number'];
                            $student_grade = $_POST['student_grade'];
                            $teacher_group_id = $_POST['teacher_group_id'];
                            $teacher_schoolyear_id = $_POST['teacher_schoolyear_id'];
                            $teacher_semester_id = $_POST['teacher_semester_id'];
                            echo"<script>console.log('$choiceupload');</script>";
                            echo"<script>console.log($student_number);</script>";
                            echo"<script>console.log($student_grade);</script>";
                            echo"<script>console.log($teacher_group_id);</script>";
                            echo"<script>console.log($teacher_schoolyear_id);</script>";
                            echo"<script>console.log($teacher_semester_id);</script>";

                            if($choiceupload == "manual"){
                                // Prepare and execute the query to check if the student number exists
                                $check_studentnumber_query = "SELECT user_account_id, group_id, full_name FROM useraccount WHERE student_number = '$student_number'";
                                $check_studentnumber_result = $con->query($check_studentnumber_query);
                                $check_studentnumber_data = $check_studentnumber_result->fetch_assoc();

                                // Check if the query was successful and if any rows were returned
                                if ($check_studentnumber_result && $check_studentnumber_result->num_rows > 0) {
                                    $user_account_id = $check_studentnumber_data['user_account_id'];
                                    $student_group_id = $check_studentnumber_data['group_id'];
                                    $full_name = $check_studentnumber_data['full_name'];

                                    echo "<script>console.log($user_account_id);</script>";
                                    echo "<script>console.log($student_group_id);</script>";
                                    echo "<script>console.log($group_id);</script>";
                                    if($group_id == $student_group_id){

                                        $grade_query = "INSERT INTO gradetable (student_grade, student_id, group_id, timestamp) VALUES ('$student_grade', $user_account_id, $group_id, NOW())";
                                        $grade_result = $con->query($grade_query);

                                        // Display a success message using SweetAlert 2 or any other method
                                        echo "<script>
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Grade Uploaded',
                                                text: 'Grade successfully uploaded for student name: $full_name!',
                                            }).then(function() {
                                                window.location = 'inputgrade.php';
                                            });
                                        </script>";

                                    }else{
                                        echo "<script>
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Student Not Belong to the Group',
                                                text: 'Student with the number $student_number does not belong to the group!',
                                            });
                                        </script>";
                                    }
                                } else {
                                    // Student number does not exist, show an error message using SweetAlert 2
                                    echo "<script>
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Student Not Found',
                                                text: 'Student with the number $student_number does not exist!',
                                            });
                                        </script>";
                                }
                            } elseif ($choiceupload == "automatic") {
                                echo "<script>console.log('Automatic Upload chosen');</script>";
                                var_dump($_FILES['excel_upload']); 
                                var_dump($_POST);

                                if (isset($_FILES['excel_upload']) && $_FILES['excel_upload']['error'] === UPLOAD_ERR_OK) {
                                    require 'vendor/autoload.php'; // Assuming you have the autoload file for PhpSpreadsheet
                        
                                    $fileName = $_FILES['excel_upload']['name'];
                                    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                    $allowed_ext = ['xls', 'csv', 'xlsx'];
                        
                                    if (in_array($file_ext, $allowed_ext)) {
                                        $inputFileNamePath = $_FILES['excel_upload']['tmp_name'];
                                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
                                        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                                        // Assuming that the Excel file's first row contains headers
                        
                                        $existingStudents = [];
                                        $errorMessages = [];
                                        $successFlag = false;
                        
                                        foreach ($data as $row) {
                                            $student_number = mysqli_real_escape_string($con, $row['A']);
                                            $student_grade = mysqli_real_escape_string($con, $row['B']);
                        
                                            // Check if the student number is a valid integer and has 9 digits
                                            if (!ctype_digit($student_number) || strlen($student_number) !== 9) {
                                                // Handle invalid student number format
                                                $errorMessages[] = "Invalid student number format: " . $student_number;
                                                continue; // Skip this iteration and move to the next row
                                            }
                        
                                            // check if the student number belongs to the teacher's group in the database
                                            $checkQuery = "SELECT * FROM useraccount WHERE student_number = '$student_number' AND schoolyear_id = $teacher_schoolyear_id AND semester_id = $teacher_semester_id AND group_id = $teacher_group_id AND role_account_id = 2";
                                            $checkResult = mysqli_query($con, $checkQuery);
                        
                                            if ($checkResult && $checkResult->num_rows > 0) {
                                                $checkData = $checkResult->fetch_assoc();
                                                $studentuser_account_id = $checkData['user_account_id'];
                                                $student_insert_query = "INSERT INTO gradetable (student_grade, student_id, group_id, timestamp) VALUES ('$student_grade', '$studentuser_account_id', $teacher_group_id, NOW())";
                                                $student_insert_result = $con->query($student_insert_query);
                        
                                                if ($student_insert_result) {
                                                    $successFlag = true; // Set the flag to true if there were successful insertions
                                                } else {
                                                    $errorMessages[] = "Failed to insert data for student number: " . $student_number;
                                                }
                                            } else {
                                                $existingStudents[] = array('number' => $student_number);
                                            }
                                        }
                        
                                        if ($successFlag) {
                                            $successCount = count($data) - count($existingStudents);
                                            $successMessage = "Data uploaded successfully. Total records imported: $successCount";
                                            echo "<script>
                                                    Swal.fire({
                                                        title: 'Success',
                                                        icon: 'success',
                                                        text: '$successMessage',
                                                    }).then(function () {
                                                        window.location.href = 'inputgrade.php';
                                                    });
                                                </script>";
                                        }
                        
                                        if (!empty($existingStudents)) {
                                            $errorMessage = "The following student number records do not exist or do not belong to the group:<br><br>";
                                            $errorMessage .= "<table>";
                                            $errorMessage .= "<tr><th>Student Number</th></tr>";
                                            foreach ($existingStudents as $existingStudent) {
                                                $errorMessage .= "<tr><td>" . $existingStudent['number'] . "</td></tr>";
                                            }
                                            $errorMessage .= "</table>";
                        
                                            echo "<script>
                                                    Swal.fire({
                                                        title: 'Student Record Not Found',
                                                        icon: 'warning',
                                                        html: '$errorMessage',
                                                    }).then(function () {
                                                        window.location.href = 'inputgrade.php';
                                                    });
                                                </script>";
                                        }
                        
                                        if (!empty($errorMessages)) {
                                            $errorMessage = implode("<br>", $errorMessages);
                                            echo "<script>
                                                    Swal.fire({
                                                        title: 'Error',
                                                        icon: 'error',
                                                        html: '$errorMessage',
                                                    }).then(function () {
                                                        window.location.href = 'inputgrade.php';
                                                    });
                                                </script>";
                                        }
                                    } else {
                                        echo "<script>
                                                Swal.fire({
                                                    title: 'Invalid File Format',
                                                    icon: 'error',
                                                    text: 'Only .xls, .csv, and .xlsx files are allowed.',
                                                }).then(function () {
                                                    window.location.href = 'inputgrade.php';
                                                });
                                            </script>";
                                    }
                                } else {
                                    echo "<script>
                                            Swal.fire({
                                                title: 'Error',
                                                icon: 'error',
                                                text: 'No file selected.',
                                            }).then(function () {
                                                window.location.href = 'inputgrade.php';
                                            });
                                        </script>";
                                }
                            }
                        }
                        // FOR UPDATE GRADE
                        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_student_grades'])){

                            $student_id = $_POST['update_student_number'];
                            $student_name = $_POST['student_name'];
                            $grade_id = $_POST['grade_id'];
                            $student_grade = $_POST['update_student_grade'];

                            $update_studentgrade_query = "UPDATE gradetable SET student_grade = '$student_grade' WHERE grade_id = $grade_id";
                            $update_studentgrade_result = $con->query($update_studentgrade_query);

                            if($update_studentgrade_result){
                                echo "<script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Student Grade Update',
                                        text: 'Grade successfully Updated for Student name: $student_name!',
                                    }).then(function() {
                                        window.location = 'inputgrade.php';
                                        });
                                </script>";
                            }
                        }
                    ?>
                    <div class='title-container'>
                        <h2 class="group-id"><?php echo $group_data['group_name']; ?></h2>
                        <label>Group Name</label>
                    </div>
                    <div class='header-container'>
                        <div class='title-container1'>
                            <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#addstudentgrade'>Upload Grades</button>
                        </div>
                    </div>
                    <div class='table-container'>
                        <table class='responsive-table'>
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Student Number</th>
                                    <th>Student Grade</th>
                                    <th>Date Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id='file-table-body'>
                                <?php
                                    $grade_query = "SELECT g.*, u.full_name, u.student_number FROM gradetable g INNER JOIN useraccount u ON g.student_id = u.user_account_id WHERE g.group_id = $group_id AND u.role_account_id = 2";
                                    $grade_result = $con->query($grade_query);

                                    if($grade_result){
                                        if($grade_result->num_rows > 0){
                                            while($grade_data = $grade_result->fetch_assoc()){
                                                echo"
                                                    <tr>
                                                        <td data-label='Student Name'>". $grade_data['full_name'] ."</td>
                                                        <td data-label='Student Name'>". $grade_data['student_number'] ."</td>
                                                        <td data-label='Student Name'>". $grade_data['student_grade'] ."</td>
                                                        <td data-label='Student Name'>". $grade_data['timestamp'] ."</td>
                                                        <td data-label='Student Name'>
                                                        <button type='button' class='btn btn-primary update-grade-button' data-bs-toggle='modal' data-bs-target='#updatestudentgrade' data-grade-id='". $grade_data['grade_id'] ."' data-student-id='". $grade_data['student_id'] ."'>Update Grades</button>
                                                        </td>
                                                    </tr>
                                                ";
                                            }
                                        }else{
                                            echo "<h2 style='height:50%' class='d-flex justify-content-center align-items-center'>No student assigned yet</h2>";
                                        }
                                    }else{
                                        echo "Error: " . $con->error;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                }else{
                    echo "<h2 style='text-align: center;'>No Assigned Group yet.</h2>";
                }
                ?>
            <!-- Start of Upload grade modal -->
            <div class="modal fade" id="addstudentgrade" tabindex="1" aria-labelledby="addstudentgrade" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">Upload Grades</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="choiceupload">Upload Method:</label>
                                    <select class="form-control" id="choiceupload" name="choiceupload">
                                        <option value="manual">Manual Upload</option>
                                        <option value="automatic">Excel File Upload</option>
                                    </select>
                                </div>
                                <div id="manual-upload-fields">
                                    <div class="form-group">
                                        <label for="student_number">Student Number:</label>
                                        <select class="form-control" id="student_number" name="student_number" required>
                                        <?php
                                            $group_student_query = "SELECT student_number, full_name FROM useraccount WHERE role_account_id = 2 AND group_id = $group_id";
                                            $group_student_result = $con->query($group_student_query);

                                            if($group_student_result){
                                                if($group_student_result->num_rows > 0){
                                                    echo "<option value='' disable hidden>Select Student</option>";
                                                    while($group_student_data = $group_student_result->fetch_assoc()){
                                                        echo "<option value='". $group_student_data['student_number'] ."'>". $group_student_data['full_name'] ."</option>";
                                                    }
                                                }else{
                                                    echo"<option value=''>No  Student Assigned</option>";
                                                }
                                            }else{
                                                echo"<option value=''>Error: ". $con->error ."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="student_grade">Grade:</label>
                                        <select class="form-control" id="student_grade" name="student_grade" required>
                                            <option value="" selected disabled hidden>Select a grade</option>
                                            <option value="1">1</option>
                                            <option value="1.25">1.25</option>
                                            <option value="1.5">1.5</option>
                                            <option value="1.75">1.75</option>
                                            <option value="2">2</option>
                                            <option value="2.25">2.25</option>
                                            <option value="2.5">2.5</option>
                                            <option value="2.75">2.75</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="Drop">Drop</option>
                                            <option value="Incomplete">Incomplete</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="excel-upload-fields" style="display: none;">
                                    <?php
                                    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                                    $schoolyear_result = $con->query($schoolyear_query);
                                    $schoolyear_data = $schoolyear_result->fetch_assoc();
                                    $schoolyear_id = $schoolyear_data['schoolyear_id'];
                                    $semester_id = $schoolyear_data['semester_id'];
                                    ?>
                                    <input type="hidden" name="teacher_group_id" value="<?php echo $group_id ?>">
                                    <input type="hidden" name="teacher_schoolyear_id" value="<?php echo $schoolyear_id ?>">
                                    <input type="hidden" name="teacher_semester_id" value="<?php echo $semester_id ?>">
                                    <div class="form-group">
                                        <label for="excel_upload">Choice Excel File:</label>
                                        <input class="form-control" type="file" name="excel_upload" id="excel_upload" accept=".xls, .xlsx">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="upload_grade">Upload Grade</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End of Upload grade modal -->
            <!-- Start of Update grade modal -->
            <div class="modal fade" id="updatestudentgrade" tabindex="1" aria-labelledby="updatestudentgrade" aria-hidden="true">
                <div class="modal-dialog" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">Update Grades</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="update_student_number">Student Number:</label>
                                    <input type="text" name="student_name" id="student_name" class="form-control" readonly>
                                    <input type="hidden" name="grade_id" id="grade_id" class="form-control">
                                    <input type="hidden" name="update_student_number" id="update_student_number">
                                </div>
                                <div class="form-group">
                                    <label for="update_student_grade">Grade:</label>
                                    <select class="form-control" id="update_student_grade" name="update_student_grade" required>
                                        <option value="" selected disabled hidden>Select a grade</option>
                                        <option value="1">1</option>
                                        <option value="1.25">1.25</option>
                                        <option value="1.5">1.5</option>
                                        <option value="1.75">1.75</option>
                                        <option value="2">2</option>
                                        <option value="2.25">2.25</option>
                                        <option value="2.5">2.5</option>
                                        <option value="2.75">2.75</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="Drop">Drop</option>
                                        <option value="Incomplete">Incomplete</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="update_student_grades">Update Grade</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End of Upload grade modal -->
            </div>
        </div>
    </section>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const choiceUploadElement = document.getElementById("choiceupload");
        const manualUploadFields = document.getElementById("manual-upload-fields");
        const excelUploadFields = document.getElementById("excel-upload-fields");

        // Function to show or hide the appropriate fields based on user's choice
        function toggleUploadFields() {
            if (choiceUploadElement.value === "manual") {
                manualUploadFields.style.display = "block";
                excelUploadFields.style.display = "none";
            } else if (choiceUploadElement.value === "automatic") {
                manualUploadFields.style.display = "none";
                excelUploadFields.style.display = "block";
            }
        }

        // Initial state based on user's choice on page load
        toggleUploadFields();

        // Add event listener to the dropdown to handle changes
        choiceUploadElement.addEventListener("change", toggleUploadFields);
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Add event listener to update grade buttons
        const updateGradeButtons = document.querySelectorAll(".update-grade-button");
        updateGradeButtons.forEach((button) => {
            button.addEventListener("click", function () {
                const gradeId = button.dataset.gradeId;
                const studentId = button.dataset.studentId;
                fetchStudentData(gradeId, studentId);
            });
        });

        // Function to fetch student data using AJAX
        function fetchStudentData(gradeId, studentId) {
            // AJAX request to fetch student data
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const studentData = JSON.parse(xhr.responseText);
                        populateUpdateModal(studentData);
                    } else {
                        console.error("Failed to fetch student data");
                    }
                }
            };

            xhr.open("GET", "get_student_data.php?grade_id=" + gradeId + "&student_id=" + studentId, true);
            xhr.send();
        }

        // Function to populate the update modal with student data
        function populateUpdateModal(studentData) {
            const studentNameInput = document.getElementById("student_name");
            const grade_id = document.getElementById("grade_id");
            const studentNumberInput = document.getElementById("update_student_number");
            const studentGradeSelect = document.getElementById("update_student_grade");

            studentNameInput.value = studentData.full_name;
            grade_id.value = studentData.grade_id;
            studentNumberInput.value = studentData.student_number;
            studentGradeSelect.value = studentData.student_grade;
        }
    });
</script>

<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>