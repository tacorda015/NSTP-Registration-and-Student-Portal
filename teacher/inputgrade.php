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
                <div class='studentList-container'>
                    <?php

                    if ($group_id !== null) {
                        $group_query = "SELECT group_name FROM grouptable WHERE group_id = {$group_id}";
                        $group_result = $con->query($group_query);
                        $group_data = $group_result->fetch_assoc();

                        // Check if the form was submitted
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_grade'])) {

                            // Get the student number and grade from the form
                            $choiceupload = $_POST['choiceupload'];

                            if($choiceupload == "manual"){

                                // DATA FROM ADD GRADE MODAL
                                $student_number = $_POST['student_number'];
                                $student_grade = $_POST['student_grade'];
                                $teacher_schoolyear_id = $_POST['teacher_schoolyear_id'];
                                $teacher_semester_id = $_POST['teacher_semester_id'];

                                // Prepare and execute the query to check if the student number exists
                                $check_studentnumber_query = "SELECT user_account_id, year_level, student_section, group_id, course, full_name FROM useraccount WHERE student_number = '$student_number' AND schoolyear_id = $teacher_schoolyear_id AND semester_id = $teacher_semester_id ORDER BY user_account_id DESC";
                                $check_studentnumber_result = $con->query($check_studentnumber_query);
                                $check_studentnumber_data = $check_studentnumber_result->fetch_assoc();

                                // Check if the query was successful and if any rows were returned
                                if ($check_studentnumber_result && $check_studentnumber_result->num_rows > 0) {
                                    $user_account_id = $check_studentnumber_data['user_account_id'];
                                    $course = $check_studentnumber_data['course'];
                                    $year_level = $check_studentnumber_data['year_level'];
                                    $student_group_id = $check_studentnumber_data['group_id'];
                                    $full_name = $check_studentnumber_data['full_name'];
                                    $student_section = $check_studentnumber_data['student_section'];

                                    echo"<script>console.log($year_level);</script>";

                                    if($year_level == 'First Year'){
                                        $yearLevel = 1;
                                    }elseif($year_level == 'Second Year'){
                                        $yearLevel = 2;
                                    }elseif($year_level == 'Third Year'){
                                        $yearLevel = 3;
                                    }elseif($year_level == 'Fourth Year'){
                                        $yearLevel = 4;
                                    }

                                    if($group_id == $student_group_id){

                                        $grade_student_check = "SELECT * FROM gradetable WHERE student_id = $user_account_id";
                                        $grade_student_result = $con->query($grade_student_check);
                                        if($grade_student_result && $grade_student_result->num_rows > 0){
                                            echo "<script>
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Student Already have Grade',
                                                    text: 'Student with the number $student_number already have grade just update it!',
                                                });
                                            </script>";
                                        }else{
                                            $course_check = "SELECT * FROM coursetable WHERE course_name = '$course'";
                                            $course_check_result = $con->query($course_check);
                                            $course_check_data = $course_check_result->fetch_assoc();
                                            $department_id = $course_check_data['department_id'];
                                            $current_year_last_two_digits = date('y');

                                            $section_schedcode = "SELECT * FROM schedcodetable WHERE course = '$course' AND department_id = $department_id AND year_level = $yearLevel AND student_section = '$student_section' AND schoolyear_id = $teacher_schoolyear_id AND semester_id = $teacher_semester_id";
                                            $section_schedcode_result = $con->query($section_schedcode);
                                            if($section_schedcode_result && $section_schedcode_result->num_rows > 0){
                                                $section_schedcode_data = $section_schedcode_result->fetch_assoc();
                                                $schedcode_id = $section_schedcode_data['schedcode_id'];
                                            }else{
                                                // Retrieve the highest schedcode value from schedcodetable
                                                $check_schedcode_query = "SELECT lastfourdigit FROM schedcodetable ORDER BY schedcode_id DESC LIMIT 1";
                                                $result = $con->query($check_schedcode_query);

                                                if ($result && $result->num_rows > 0) {
                                                    $row = $result->fetch_assoc();
                                                    $highest_schedcode = $row['lastfourdigit'];

                                                    if($highest_schedcode == NULL){
                                                        $next_schedcode = 1;
                                                    }
                                                    $next_schedcode = $highest_schedcode + 1;
                                                    
                                                } else {
                                                    // Default value if no records in schedcodetable
                                                    $next_schedcode = 1;
                                                }
                                                $last_four_digits = substr($next_schedcode, -4);

                                                // Format next_schedcode to be four digits long with leading zeros
                                                $next_schedcode_formatted = str_pad($last_four_digits, 4, '0', STR_PAD_LEFT);

                                                // Concatenate the values and convert to a number
                                                $combined_value = intval($current_year_last_two_digits . $teacher_semester_id . $department_id . $next_schedcode_formatted);

                                                // Insert into schedcodetable
                                                $schedcode_query = "INSERT INTO schedcodetable (schedcode_number, lastfourdigit, department_id, year_level, course, student_section, schoolyear_id, semester_id) VALUES ($combined_value, $next_schedcode, $department_id, $yearLevel, '$course', '$student_section', $teacher_schoolyear_id, $teacher_semester_id)";
                                                $schedcode_result = $con->query($schedcode_query);

                                                $get_schedcode_id = "SELECT schedcode_id FROM schedcodetable ORDER BY schedcode_id DESC LIMIT 1";
                                                $get_schedcode_id_result = $con->query($get_schedcode_id);
                                                $get_schedcode_id_data = $get_schedcode_id_result->fetch_assoc();
                                                $schedcode_id = $get_schedcode_id_data['schedcode_id'];
                                            }
                                            
                                            $grade_query = "INSERT INTO gradetable (student_grade, student_id, group_id, schoolyear_id, semester_id, schedcode, timestamp) VALUES ('$student_grade', $user_account_id, $group_id, $teacher_schoolyear_id, $teacher_semester_id, $schedcode_id, NOW())";
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
                                        }
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

                                // DATA FROM ADD GRADE MODAL
                                $teacher_group_id = $_POST['teacher_group_id'];
                                $teacher_schoolyear_id = $_POST['teacher_schoolyear_id'];
                                $teacher_semester_id = $_POST['teacher_semester_id'];

                                if (isset($_FILES['excel_upload']) && $_FILES['excel_upload']['error'] === UPLOAD_ERR_OK) {
                        
                                    $fileName = $_FILES['excel_upload']['name'];
                                    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                    $allowed_ext = ['xls', 'csv', 'xlsx'];
                        
                                    if (in_array($file_ext, $allowed_ext)) {
                                        $inputFileNamePath = $_FILES['excel_upload']['tmp_name'];
                                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
                                        // $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                                        $data = $spreadsheet->getActiveSheet()->rangeToArray('A2:' . $spreadsheet->getActiveSheet()->getHighestColumn() . $spreadsheet->getActiveSheet()->getHighestRow(), null, true, true, true);

                                        // Assuming that the Excel file's first row contains headers
                        
                                        $existingStudents = [];
                                        $errorMessages = [];
                                        $alredyhavegrade = [];
                                        $successFlag = false;
                        
                                        foreach ($data as $row) {
                                            $student_number = mysqli_real_escape_string($con, $row['A']);
                                            $student_grade = mysqli_real_escape_string($con, $row['B']);
                        
                                            // check if the student number belongs to the teacher's group in the database
                                            $checkQuery = "SELECT * FROM useraccount WHERE student_number = '$student_number' AND schoolyear_id = $teacher_schoolyear_id AND semester_id = $teacher_semester_id AND group_id = $teacher_group_id AND role_account_id = 2";
                                            $checkResult = mysqli_query($con, $checkQuery);
                        
                                            if ($checkResult && $checkResult->num_rows > 0) {
                                                $checkData = $checkResult->fetch_assoc();
                                                $studentuser_account_id = $checkData['user_account_id'];
                                                $course = $checkData['course'];
                                                $year_level = $checkData['year_level'];
                                                $student_group_id = $checkData['group_id'];
                                                $full_name = $checkData['full_name'];
                                                $student_section = $checkData['student_section'];

                                                if($year_level == 'First Year'){
                                                    $yearLevel = 1;
                                                }elseif($year_level == 'Second Year'){
                                                    $yearLevel = 2;
                                                }elseif($year_level == 'Third Year'){
                                                    $yearLevel = 3;
                                                }elseif($year_level == 'Fourth Year'){
                                                    $yearLevel = 4;
                                                }

                                                if($group_id == $student_group_id){
                                                    $grade_student_check = "SELECT * FROM gradetable WHERE student_id = $studentuser_account_id";
                                                    $grade_student_result = $con->query($grade_student_check);
                                                    if($grade_student_result && $grade_student_result->num_rows > 0){
                                                        $alredyhavegrade[] = array('number' => $student_number);
                                                        // echo "<script>
                                                        //     Swal.fire({
                                                        //         icon: 'warning',
                                                        //         title: 'Student Already have Grade',
                                                        //         text: 'Student with the number $student_number already have grade just update it!',
                                                        //     });
                                                        // </script>";
                                                    }else{
                                                        $course_check = "SELECT * FROM coursetable WHERE course_name = '$course'";
                                                        $course_check_result = $con->query($course_check);
                                                        $course_check_data = $course_check_result->fetch_assoc();
                                                        $department_id = $course_check_data['department_id'];
                                                        $current_year_last_two_digits = date('y');
            
                                                        $section_schedcode = "SELECT * FROM schedcodetable WHERE course = '$course' AND department_id = $department_id AND year_level = $yearLevel AND student_section = '$student_section' AND schoolyear_id = $teacher_schoolyear_id AND semester_id = $teacher_semester_id";
                                                        $section_schedcode_result = $con->query($section_schedcode);
                                                        if($section_schedcode_result && $section_schedcode_result->num_rows > 0){
                                                            $section_schedcode_data = $section_schedcode_result->fetch_assoc();
                                                            $schedcode_id = $section_schedcode_data['schedcode_id'];
                                                        }else{
                                                            // Retrieve the highest schedcode value from schedcodetable
                                                            $check_schedcode_query = "SELECT lastfourdigit FROM schedcodetable ORDER BY schedcode_id DESC LIMIT 1";
                                                            $result = $con->query($check_schedcode_query);
            
                                                            if ($result && $result->num_rows > 0) {
                                                                $row = $result->fetch_assoc();
                                                                $highest_schedcode = $row['lastfourdigit'];
            
                                                                if($highest_schedcode == NULL){
                                                                    $next_schedcode = 1;
                                                                }
                                                                $next_schedcode = $highest_schedcode + 1;
                                                                
                                                            } else {
                                                                // Default value if no records in schedcodetable
                                                                $next_schedcode = 1;
                                                            }
                                                            $last_four_digits = substr($next_schedcode, -4);
            
                                                            // Format next_schedcode to be four digits long with leading zeros
                                                            $next_schedcode_formatted = str_pad($last_four_digits, 4, '0', STR_PAD_LEFT);
            
                                                            // Concatenate the values and convert to a number
                                                            $combined_value = intval($current_year_last_two_digits . $teacher_semester_id . $department_id . $next_schedcode_formatted);
            
                                                            // Insert into schedcodetable
                                                            $schedcode_query = "INSERT INTO schedcodetable (schedcode_number, lastfourdigit, department_id, year_level, course, student_section, schoolyear_id, semester_id) VALUES ($combined_value, $next_schedcode, $department_id, $yearLevel, '$course', '$student_section', $teacher_schoolyear_id, $teacher_semester_id)";
                                                            $schedcode_result = $con->query($schedcode_query);
            
                                                            $get_schedcode_id = "SELECT schedcode_id FROM schedcodetable ORDER BY schedcode_id DESC LIMIT 1";
                                                            $get_schedcode_id_result = $con->query($get_schedcode_id);
                                                            $get_schedcode_id_data = $get_schedcode_id_result->fetch_assoc();
                                                            $schedcode_id = $get_schedcode_id_data['schedcode_id'];
                                                        }
                                                        
                                                        $grade_query = "INSERT INTO gradetable (student_grade, student_id, group_id, schoolyear_id, semester_id, schedcode, timestamp) VALUES ('$student_grade', $studentuser_account_id, $group_id, $teacher_schoolyear_id, $teacher_semester_id, $schedcode_id, NOW())";
                                                        $grade_result = $con->query($grade_query);
                                                        if($grade_result){
                                                            $successFlag = true; // Set the flag to true if there were successful insertions
                                                        }else {
                                                            $errorMessages[] = "Failed to insert data for student number: " . $student_number;
                                                        }
                                                    }
                                                }else{
                                                    $existingStudents[] = array('number' => $student_number);
                                                }
                                            } else {
                                                $existingStudents[] = array('number' => $student_number);
                                            }
                                        }
                                        // $successCount = '0';
                                        if ($successFlag) {
                                            $successCount = (count($data) - count($existingStudents)) - count($alredyhavegrade);
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
                        
                                        if (!empty($existingStudents) && is_array($existingStudents)) {
                                            $errorMessage = "<p>Successful upload student grade: " . ($successCount ?? 0) . " Record<br>";
                                            $errorMessage .= "The following student number records do not exist or do not belong to the group:</p><br>";
                                            $errorMessage .= "<div style='max-height: 180px;'><table class='w-100'>";
                                            $errorMessage .= "<tr><th>Student Number</th></tr>";
                                            foreach ($existingStudents as $existingStudent) {
                                                $errorMessage .= "<tr><td>" . htmlspecialchars($existingStudent['number'], ENT_QUOTES, 'UTF-8') . "</td></tr>";
                                            }
                                            $errorMessage .= "</table></div>";
                                        
                                            // Use json_encode to handle the conversion of HTML content
                                            $encodedErrorMessage = json_encode($errorMessage);
                                        
                                            echo "<script>
                                                    Swal.fire({
                                                        title: 'Student Record Not Found',
                                                        icon: 'warning',
                                                        html: $encodedErrorMessage,
                                                    }).then(function () {
                                                        window.location.href = 'inputgrade.php';
                                                    });
                                                </script>";
                                        }
                                        
                                        
                                        if (!empty($alredyhavegrade)) {
                                            $errorMessage = "The following student number records already have grade:<br><br>";
                                            $errorMessage .= "<div style='max-height: 250px;'><table>";
                                            $errorMessage .= "<tr><th>Student Number</th></tr>";
                                            foreach ($alredyhavegrade as $alredyhavegradeStudent) {
                                                $errorMessage .= "<tr><td>" . $alredyhavegradeStudent['number'] . "</td></tr>";
                                            }
                                            $errorMessage .= "</table></div>";
                        
                                            echo "<script>
                                                    Swal.fire({
                                                        title: 'Student Alreay have Grade',
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
                    <div class="page-title">
                        <div class='titleContainer'>
                            <span class="group-id"><?php echo $group_data['group_name']; ?></span>
                            <label class='in-charge-label'>Group Name</label>
                        </div>
                    </div>
                    <div class='buttonsContainer'>
                        <div class='buttonHolder'>
                            <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#addstudentgrade' data-bs-placement="bottom" data-toggle="tooltip" data-bs-html="true" title="Data Needed:<br> Student Number,<br> Student Grade">
                                <i class='bx bx-cloud-upload'></i>Upload Grades
                            </button>
                        </div>
                    </div>
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
                                        <div id="manual-upload-fields" hidden>
                                            <div class="form-group">
                                                <?php
                                                    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                                                    $schoolyear_result = $con->query($schoolyear_query);
                                                    $schoolyear_data = $schoolyear_result->fetch_assoc();
                                                    $schoolyear_id = $schoolyear_data['schoolyear_id'];
                                                    $semester_id = $schoolyear_data['semester_id'];
                                                ?>
                                                <input type="hidden" name="teacher_schoolyear_id" value="<?php echo $schoolyear_id ?>">
                                                <input type="hidden" name="teacher_semester_id" value="<?php echo $semester_id ?>">
                                                <label for="student_number">Student Number:</label>
                                                <select class="form-control" id="student_number" name="student_number">
                                                
                                                <?php
                                                    $group_student_query = "SELECT student_number, user_account_id, full_name
                                                                            FROM useraccount
                                                                            WHERE user_account_id NOT IN (
                                                                                SELECT student_id
                                                                                FROM gradetable
                                                                            ) AND role_account_id = 2 AND group_id = $group_id";
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
                                                <select class="form-control" id="student_grade" name="student_grade">
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
                                        <div id="excel-upload-fields" hidden>
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
                                                <input class="form-control" type="file" name="excel_upload" id="excel_upload" accept=".xls, .xlsx, .csv">
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
                    <?php
                    $recordsPerPage = 10;
                    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                        $currentPage = intval($_GET['page']);
                    } else {
                        $currentPage = 1;
                    }
                
                    // Calculate the offset for the current page
                    $offset = ($currentPage - 1) * $recordsPerPage;
                
                    $grade_query = "SELECT g.*, u.full_name, u.student_number FROM gradetable g INNER JOIN useraccount u ON g.student_id = u.user_account_id WHERE g.group_id = $group_id AND u.role_account_id = 2 ORDER BY g.grade_id DESC LIMIT $recordsPerPage OFFSET $offset";
                    $grade_result = $con->query($grade_query);

                   if($grade_result){
                       if($grade_result->num_rows > 0){
                    ?>
                    <div class='tableContainer'>
                        <table class='table table-sm caption-top'>
                            <caption>List of Grades</caption>
                            <thead class="custom-thead">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Student Number</th>
                                    <th>Student Grade</th>
                                    <th>Date Upload</th>
                                    <th class='thAction'>Action</th>
                                </tr>
                            </thead>
                            <tbody id='file-table-body'>
                                <?php
                                    while($grade_data = $grade_result->fetch_assoc()){
                                        echo"
                                            <tr>
                                                <td data-label='Student Name'>". $grade_data['full_name'] ."</td>
                                                <td data-label='Student Number'>". $grade_data['student_number'] ."</td>
                                                <td data-label='Student Grade'>". $grade_data['student_grade'] ."</td>
                                                <td data-label='Date Uploaded'>". $grade_data['timestamp'] ."</td>
                                                <td data-label='Action'>
                                                    <div class = 'groupButton'>
                                                        <button type='button' class='btn btn-primary update-grade-button' data-bs-toggle='modal' data-bs-target='#updatestudentgrade' data-grade-id='". $grade_data['grade_id'] ."' data-student-id='". $grade_data['student_id'] ."'>
                                                        <i class='bx bx-wrench'></i>Update</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                            // Get the total number of records for pagination
                            $totalRecordsQuery = "SELECT COUNT(*) AS total FROM gradetable g INNER JOIN useraccount u ON g.student_id = u.user_account_id WHERE g.group_id = $group_id AND u.role_account_id = 2";
                            $totalRecordsResult = $con->query($totalRecordsQuery);
                            $totalRecordsData = $totalRecordsResult->fetch_assoc();
                            $totalRecords = $totalRecordsData['total'];

                            // Calculate the total number of pages
                            $totalPages = ceil($totalRecords / $recordsPerPage);

                            // Pagination links using Bootstrap
                            echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                                <ul class='pagination justify-content-center'>";

                            // Pagination links - Previous
                            $prevPage = $currentPage - 1;
                            echo "<li class='page-item " . ($currentPage == 1 ? 'disabled' : '') . "'>
                                    <a class='page-link' href='?page=$prevPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&laquo; Previous</a>
                                </li>";

                            for ($i = max(1, $currentPage - 2); $i <= min($currentPage + 2, $totalPages); $i++) {
                                echo "<li class='page-item " . ($i == $currentPage ? 'active' : '') . "'>
                                        <a class='page-link' href='?page=$i'>$i</a>
                                    </li>";
                            }

                            // Pagination links - Next
                            $nextPage = $currentPage + 1;
                            echo "<li class='page-item " . ($currentPage == $totalPages ? 'disabled' : '') . "'>
                                    <a class='page-link' href='?page=$nextPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>Next &raquo;</a>
                                </li>
                            </ul>
                            </nav>";
                        }else{
                            echo "<h2 style='text-align: center;'>No Grade Uploaded</h2>";
                            
                        }
                    }else{
                        echo "Error: " . $con->error;
                    }
                }else{
                    echo "<h2 style='text-align: center;'>No Assigned Group yet.</h2>";
                }
                ?>
            </div>
        </div>
    </section>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const choiceUploadElement = document.getElementById("choiceupload");
        const manualUploadFields = document.getElementById("manual-upload-fields");
        const excelUploadFields = document.getElementById("excel-upload-fields");
        const uploadGradeButton = document.querySelector("[name='upload_grade']");

        // Function to show or hide the appropriate fields based on user's choice
        function toggleUploadFields() {
            if (choiceUploadElement.value === "manual") {
                manualUploadFields.removeAttribute("hidden");
                excelUploadFields.setAttribute("hidden", "true");
                // Enable or disable the upload button based on manual upload validation
                const studentNumber = document.getElementById("student_number").value;
                const studentGrade = document.getElementById("student_grade").value;
                if (studentNumber && studentGrade) {
                    uploadGradeButton.removeAttribute("disabled");
                } else {
                    uploadGradeButton.setAttribute("disabled", "true");
                }
            } else if (choiceUploadElement.value === "automatic") {
                manualUploadFields.setAttribute("hidden", "true");
                excelUploadFields.removeAttribute("hidden");
                // Enable or disable the upload button based on automatic upload validation
                const excelUpload = document.getElementById("excel_upload");
                if (excelUpload.files.length > 0 && isExcelFile(excelUpload.files[0])) {
                    uploadGradeButton.removeAttribute("disabled");
                } else {
                    uploadGradeButton.setAttribute("disabled", "true");
                }
            }
        }

        // Function to check if the selected file is an Excel file
        function isExcelFile(file) {
            const validExtensions = [".xls", ".xlsx", ".csv"];
            const fileName = file.name.toLowerCase();
            return validExtensions.some(ext => fileName.endsWith(ext));
        }

        // Initial state based on user's choice on page load
        toggleUploadFields();

        // Add event listener to the dropdown to handle changes
        choiceUploadElement.addEventListener("change", toggleUploadFields);

        // Add event listener to the manual input fields to handle changes
        document.getElementById("student_number").addEventListener("change", toggleUploadFields);
        document.getElementById("student_grade").addEventListener("change", toggleUploadFields);

        // Add event listener to the Excel file input to handle changes
        document.getElementById("excel_upload").addEventListener("change", toggleUploadFields);
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
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>