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
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require '../vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 

// Calling the sidebar
include_once('adminsidebar.php');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['uploadSerialNumberButton'])){

    $schoolYearID = $_POST['schoolyear_id'];

    $getSchoolYearRecord = "SELECT * FROM schoolyeartable WHERE schoolyear_id = $schoolYearID";
    $getSchoolYearRecord_result = $con->query($getSchoolYearRecord);
    $getSchoolYearRecord_data = $getSchoolYearRecord_result->fetch_assoc();
    $getInfoSemester = $getSchoolYearRecord_data['semester_id'];

    if (isset($_FILES['serialNumber']) && $_FILES['serialNumber']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['serialNumber']['name'];
        $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowed_ext = ['xls', 'csv', 'xlsx'];

        if (in_array($file_ext, $allowed_ext)) {
            $inputFileNamePath = $_FILES['serialNumber']['tmp_name'];
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
            // $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $data = $spreadsheet->getActiveSheet()->rangeToArray('A2:' . $spreadsheet->getActiveSheet()->getHighestColumn() . $spreadsheet->getActiveSheet()->getHighestRow(), null, true, true, true);

            // Assuming that the Excel file's first row contains headers

            $existingStudents = [];
            $errorMessages = [];
            $alredyhavegrade = [];
            $successFlag = false;

            foreach ($data as $row) {
                $excelstudent_number = mysqli_real_escape_string($con, $row['A']);
                $excelstudentSerialNumber = mysqli_real_escape_string($con, $row['B']);

                $uploadSerialNumberByExcel = "SELECT * FROM useraccount WHERE student_number = '$excelstudent_number' AND schoolyear_id = $schoolYearID AND semester_id = $getInfoSemester AND role_account_id = 2";
                $uploadSerialNumberByExcel_result = mysqli_query($con, $uploadSerialNumberByExcel);

                if ($uploadSerialNumberByExcel_result && $uploadSerialNumberByExcel_result->num_rows > 0) {
                    $uploadSerialNumberByExcel_data = $uploadSerialNumberByExcel_result->fetch_assoc();

                    $studentuser_account_id = $uploadSerialNumberByExcel_data['user_account_id'];

                    $updateQuery = "UPDATE useraccount SET serialNumber = '$excelstudentSerialNumber' WHERE student_number = '$excelstudent_number'";
                    $updateQuery_Result = $con->query($updateQuery);

                    if($updateQuery_Result){
                        $successFlag = true; // Set the flag to true if there were successful insertions
                    }else {
                        $errorMessages[] = "Failed to insert data for student number: " . $excelstudent_number;
                    }
                }else{
                    $existingStudents[] = array('number' => $excelstudent_number);
                }
            }
            if ($successFlag) {
                $successCount = (count($data) - count($existingStudents)) - count($alredyhavegrade);
                $successMessage = "Data uploaded successfully. Total records imported: $successCount";
                echo "<script>
                        Swal.fire({
                            title: 'Success',
                            icon: 'success',
                            text: '$successMessage',
                        }).then(function () {
                            window.location.href = 'studentlist.php';
                        });
                    </script>";
            }
            if (!empty($existingStudents)) {
                $errorMessage = "Successfully upload Serial Number: $successCount Record<br>";
                $errorMessage .= "The following student number records do not exist:<br>";
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
                            window.location.href = 'studentlist.php';
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
                            window.location.href = 'studentlist.php';
                        });
                    </script>";
            }
        }else {
            echo "<script>
                    Swal.fire({
                        title: 'Invalid File Format',
                        icon: 'error',
                        text: 'Only .xls, .csv, and .xlsx files are allowed.',
                    }).then(function () {
                        window.location.href = 'studentlist.php';
                    });
                </script>";
        }
    }else {
        echo "<script>
                Swal.fire({
                    title: 'Error',
                    icon: 'error',
                    text: 'No file selected.',
                }).then(function () {
                    window.location.href = 'studentlist.php';
                });
            </script>";
    }
}
?> 
<style>
    .swal2-html-container table{
        display: flex;
        justify-content: center;
    }
</style>
        <div class="home-main-container">
            <div class="studentList-container">
            <div id="loader-overlay" class="loader-overlay"></div>
            <div id="loader" class="loader">Sending <span></span></div>
                <div class="page-title">
                    <div class="titleContainer">
                        <span>Student of NSTP</span>
                    </div>
                    <form method="get" enctype="multipart/form-data" action="studentlist.php">
                        <div class="search-container">
                            <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>
                            <button class="btn btn-primary" type="submit"><i class='bx bx-search'></i></button>
                        </div>
                    </form>
                </div>
                <div class="buttonsContainer">
                    <div class="buttonHolder">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addstudentmodal">
                            <i class='bx bx-user-plus'></i>Add Student
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Downloadmodal">
                            <i class="bx bx-download"></i>Export Student List
                        </button>
                        <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#UploadSerialNumber">
                            <i class='bx bx-cloud-upload' ></i>Upload Serial Number
                        </button> -->
                        <button type="button" class="btn btn-primary" data-bs-placement="bottom" data-toggle="tooltip" data-bs-toggle="modal" data-bs-html="true" title="Data Needed:<br> Student Number, Student Serial Number" data-bs-target="#UploadSerialNumber">
                            <i class='bx bx-cloud-upload' ></i>Upload Serial Number
                        </button>
                    </div>
                </div>
                <!-- Start of modal -->
                <div class="modal fade" id="addstudentmodal" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 style="text-align: center; padding: 5px 0;">Add Student</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" enctype="multipart/form-data" id="emailForm">
                                <div class="modal-body" style="max-height: 500px; overflow-y: scroll;">
                                    <div class="form-group mb-1">
                                        <label for="addfirstname">First Name:</label>
                                        <input type="text" class="form-control" id="addfirstname" name="addfirstname" required>
                                        <small id="addfirstnameError" style="color: red;"></small>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label for="addmiddlename">Middle Name:</label>
                                        <input type="text" class="form-control" id="addmiddlename" name="addmiddlename" required>
                                        <small id="addmiddlenameError" style="color: red;"></small>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label for="addsurname">Last Name:</label>
                                        <input type="text" class="form-control" id="addsurname" name="addsurname" required>
                                        <small id="addsurnameError" style="color: red;"></small>
                                    </div>
                                    <div class="dropdown-container d-flex flex-rows gap-2 my-2">
                                        <div class="form-group  flex-grow-1" style="min-width: 100px;">
                                        <label for="birthday">Birthday:</label>
                                            <select class="form-control" id="addbirthdayMonth" name="addbirthdayMonth" required>
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
                                            <small id="addbirthdayMonthError" style="color: red;"></small>
                                        </div>
                                        <div class="dropdown-container d-flex flex-rows gap-2">
                                        <div class="form-group" style="min-width: 100px;">
                                            <label for="birthday" style="visibility: hidden; opacity: 0;">Birthday:</label>
                                            <input type="text" class="form-control" placeholder="Day" name="addBirthDay" id="addBirthDay" oninput="restrictInputToRange(event, 1, 31)" required/>
                                            <small id="addBirthDayError" style="color: red;"></small>
                                        </div>
                                        <div class="form-group" style="min-width: 100px;">
                                            <label for="birthday" style="visibility: hidden; opacity: 0;">Birthday:</label>
                                            <select class="form-control" id="addBirthYear" name="addBirthYear" required>
                                            <option value="" selected disabled hidden>Year</option>
                                            <?php
                                                $currentYear = date("Y");
                                                $newcurrentYear = $currentYear - 10;
                                                for ($year = $newcurrentYear; $year >= $newcurrentYear - 30; $year--) {
                                                echo "<option value='".$year."'>".$year."</option>";
                                                }
                                            ?>
                                            </select>
                                            <small id="addBirthYearError" style="color: red;"></small>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-rows gap-2">
                                        <div class="form-group mb-1" style="flex: 1;">
                                            <label for="addcontactnumber">Contact Number:</label>
                                            <input type="text" class="form-control" id="addcontactnumber" name="addcontactnumber" required>
                                            <small id="addcontactNumberError" style="color: red;"></small>
                                        </div>
                                        <div class="form-element" style="width: 80px;">
                                            <label for="addgender">Gender:</label>
                                            <select class="form-control" id="addgender" name="addgender" style="width: 90px;" required>
                                            <option value="" selected disabled hidden>Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                            </select>
                                            <small id="addgenderError" style="color: red;"></small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="addEmailAddress">CvSU Email:</label>
                                        <input type="email" class="form-control" id="addEmailAddress" name="addEmailAddress" required>
                                        <small id="addEmailAddressError" style="color: red;"></small>
                                    </div>
                                    <div class="form-group">
                                        <label for="addStudentNumber">Student Number:</label>
                                        <input type="text" class="form-control" id="addStudentNumber" name="addStudentNumber" required>
                                        <small id="addStudentNumberError" style="color: red;"></small>
                                    </div>
                                    <div class="d-flex flex-rows gap-2">
                                        <div class="form-group">
                                            <label for="addComponentId">Component:</label>
                                            <select class="form-control" id="addComponentId" name="addComponentId" required>
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
                                            <small id="addComponentIdError" style="color: red;"></small>
                                        </div>
                                        <div class="form-element flex-grow-1">
                                            <label for="addyearlevel">Year Level:</label>
                                            <select class="form-control" id="addyearlevel" name="addyearlevel" required>
                                            <option value="" selected disabled hidden>Year Level</option>
                                            <option value="First Year">First Year</option>
                                            <option value="Second Year">Second Year</option>
                                            <option value="Third Year">Third Year</option>
                                            <option value="Fourth Year">Fourth Year</option>
                                            </select>
                                            <small id="addyearlevelError" style="color: red;"></small>
                                        </div>
                                        <div class="form-element flex-grow-1">
                                            <label for="addsection">Section:</label>
                                            <select class="form-control" id="addsection" name="addsection" required>
                                            <option value="" selected disabled hidden>Section</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                            <option value="E">E</option>
                                            <option value="F">F</option>
                                            <option value="G">G</option>
                                            <option value="H">H</option>
                                            </select>
                                            <small id="addsectionError" style="color: red;"></small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="addGroupId">Group Name:</label>
                                        <select class="form-control" id="addGroupId" name="addGroupId" required></select>
                                        <small id="addGroupIdError" style="color: red;"></small>
                                    </div>
                                    <div class="form-group mb-1">
                                    <label for="addCourse">Course:</label>
                                        <select class="form-control" id="addCourse" name="addCourse" required>
                                            <option value="" selected disabled hidden>Course</option>
                                            <?php
                                                $course_query = "SELECT course_name FROM coursetable";
                                                $course_result = $con->query($course_query);

                                                while($row = $course_result->fetch_assoc()){
                                                    $course_name = $row['course_name'];
                                                    echo"<option value='$course_name'>$course_name</option>";
                                                }
                                            ?>
                                        </select>
                                        <small id="addCourseError" style="color: red;"></small>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label for="addStreetAddress">Street/Baranggay:</label>
                                        <input type="text" class="form-control" id="addStreetAddress" name="addStreetAddress" required>          
                                        <small id="addStreetAddressError" style="color: red;"></small>                            
                                    </div>
                                    <div class="form-group mb-1">
                                        <label for="addCityAddress">City/Municipality:</label>
                                        <input type="text" class="form-control" id="addCityAddress" name="addCityAddress" required>   
                                        <small id="addCityAddressError" style="color: red;"></small>                                                  
                                    </div>
                                    <div class="form-group mb-1">
                                        <label for="addProvinceAddress">Province:</label>
                                        <input type="text" class="form-control" id="addProvinceAddress" name="addProvinceAddress" required>  
                                        <small id="addProvinceAddressError" style="color: red;"></small>                                    
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="sendButton" class="btn btn-primary" name="add_student" disabled>Add Student</button>
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
                                            <!-- <option value="active">Active Student List</option>
                                            <option value="disabled">Disable Student List</option> -->
                                        </select>
                                    </div>
                                    <?php
                                        $schoolyear_query = "SELECT * FROM schoolyeartable";
                                        $schoolyear_result = mysqli_query($con, $schoolyear_query);

                                        if ($schoolyear_result) {
                                            echo'<div class="form-group my-2">';
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
                                    <button type="submit" id="downloadbutton" class="btn btn-primary" name="downloadbutton" disabled>Download</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End of modal -->

                <!-- FOR Upload Serial Number -->
                <div class="modal fade" id="UploadSerialNumber" tabindex="1" aria-labelledby="UploadSerialNumberLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 style="text-align: center; padding: 5px 0;">Upload Serial Number</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form form method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-group" style="display: flex; flex-direction: column;">
                                        <label for="choosegroup">Serial Number File</label>
                                        <input class="form-control" type="file" id="serialNumber" name="serialNumber" accept=".xlsx, .xls, .csv">
                                    </div>
                                    <?php
                                        $schoolyear_query = "SELECT * FROM schoolyeartable";
                                        $schoolyear_result = mysqli_query($con, $schoolyear_query);

                                        if ($schoolyear_result) {
                                            echo'<div class="form-group my-2">';
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
                                    <button type="submit" id="uploadSerialNumberButton" class="btn btn-primary" name="uploadSerialNumberButton" disabled>Download</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End of modal -->
                <?php
                // modal for updating student information
                if(isset($_POST['update_student'])) {
                    $user_account_id = mysqli_real_escape_string($con, $_POST['user_account_id']);
                    $firstname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["firstname"])));
                    $middlename = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["middlename"])));
                    $surname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["surname"])));
                    $serialNumber = mysqli_real_escape_string($con, $_POST['serialNumber']);
                    $email_address = mysqli_real_escape_string($con, $_POST['email_address']);
                    $contactNumber = mysqli_real_escape_string($con, $_POST['contactNumber']);
                    $user_status = mysqli_real_escape_string($con, $_POST['user_status']);
                    $student_number = mysqli_real_escape_string($con, $_POST['student_number']);
                    $component_name = mysqli_real_escape_string($con, $_POST['component_name']);
                    $group_id = mysqli_real_escape_string($con, $_POST['group_id']);

                    echo'<script>console.log($group_id)</script>';

                    $modifymiddlename = strtoupper(substr($middlename, 0, 1));
                    $full_name = $firstname . ' ' . $modifymiddlename . '. ' . $surname;

                    $checkComponent = "SELECT component_name FROM useraccount WHERE user_account_id = '{$user_account_id}'";
                    $checkComponentResult = $con->query($checkComponent)->fetch_assoc();
                    
                    // Update the student record in the database
                    $update_query = "UPDATE useraccount SET serialNumber = '$serialNumber',firstname = '$firstname', middlename = '$middlename', surname = '$surname', full_name='$full_name', email_address = '$email_address', contactNumber='$contactNumber', user_status='$user_status', student_number='$student_number', component_name='$component_name', group_id='$group_id' WHERE user_account_id='$user_account_id'";
                    // $update_query = "UPDATE useraccount SET serialNumber = '$serialNumber', firstname = '$firstname', middlename = '$middlename', surname = '$surname', full_name='$full_name', email_address = '$email_address', contactNumber='$contactNumber', user_status='$user_status', student_number='$student_number', component_name='$component_name', group_id=" . (($checkComponentResult['component_name'] != $component_name) ? 'NULL' : "'$group_id'") . " WHERE user_account_id='$user_account_id'";
                    $result = mysqli_query($con, $update_query);

                    $enroll_query = "UPDATE enrolledstudent SET student_name = '$full_name', student_email = '$email_address' WHERE student_number = $student_number";
                    $enroll_result = $con->query($enroll_query);
                
                    if($result && $enroll_result) {
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

                    $query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE t.role_account_id = 2";

                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($con, $_GET['search']);
                        $query .= " AND (t.full_name LIKE '%$search%' OR t.student_number LIKE '%$search%' OR t.component_name LIKE '%$search%' OR g.group_name LIKE '%$search%')";
                    }

                    // Add the condition for schoolyear_id and semester_id
                    $query .= " AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";

                    $query .= " ORDER BY t.user_account_id DESC";

                    // Modify the query to include LIMIT and OFFSET clauses for pagination
                    $offset = ($currentPage - 1) * $recordsPerPage;
                    $query .= " LIMIT $recordsPerPage OFFSET $offset";

                    $result = mysqli_query($con, $query);


                    if (mysqli_num_rows($result) > 0) {
                        echo "<div class='tableContainer'>";
                        echo "<table class='table table-sm caption-top'>";
                        echo "<caption>List of Student</caption>";
                        echo "<thead class=\"custom-thead\"><tr><th>Full Name</th><th>Account Status</th><th>Component Name</th><th>Group Name</th><th class='thAction'>Action</th></tr></thead>";
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
                                // echo "<td data-label='Student Number'>{$row['student_number']}</td>";
                                echo "<td data-label='Account Status'>" . ucfirst($row['user_status']) . "</td>";
                                echo "<td data-label='Component Name'>{$row['component_name']}</td>";
                                echo "<td data-label='Group Name'>".($row['group_name'] ?? 'No group assigned')."</td>";
                                echo "<td data-label='Action'>
                                        <div class='groupButton'>
                                            <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#studentupdatemodal".$row['student_number']."'>
                                            <i class='bx bx-wrench'></i>Update</button>
                                            <button type='submit' class='btn btn-danger' onclick='disablestudent(".$row['user_account_id'].");'>
                                            <i class='bx bx-user-x'></i>Disable
                                            </button>
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
                                            <div class='modal-body' style='max-height: 500px; overflow-y: scroll;'>
                                                    <input type='hidden' name='user_account_id' value='".$row['user_account_id']."'>
                                                    <div class='form-group'>
                                                        <label for='serialNumber'>Serial Number:</label>
                                                        <input type='text' class='form-control' id='serialNumber' name='serialNumber' value='".$row['serialNumber']."'>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='firstname'>First Name:</label>
                                                        <input type='text' class='form-control' id='firstname' name='firstname' value='".$row['firstname']."' pattern='[A-Za-z.\s]+' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='middlename'>Middle Name:</label>
                                                        <input type='text' class='form-control' id='middlename' name='middlename' value='".$row['middlename']."' pattern='[A-Za-z.\s]+' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='surname'>Last Name:</label>
                                                        <input type='text' class='form-control' id='surname' name='surname' value='".$row['surname']."' pattern='[A-Za-z.\s]+' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='student_number'>Student Number:</label>
                                                        <input type='text' class='form-control' id='student_number' name='student_number' value='".$row['student_number']."' readonly required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='email_address'>Email Address:</label>
                                                        <input type='text' class='form-control' id='email_address' name='email_address' value='".$row['email_address']."' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='contactNumber'>Contact Number:</label>
                                                        <input type='text' class='form-control' id='contactNumber' name='contactNumber' value='".$row['contactNumber']."' required>
                                                    </div>
                                                    
                                                    <div class='form-group'>
                                                        <label for='user_status'>Account Status:</label>
                                                        <select class='form-control' id='user_status' name='user_status' required>
                                                            <option value='active' ".(($row['user_status'] == 'active') ? 'selected' : '').">Active</option>
                                                            <option value='disabled' ".(($row['user_status'] == 'disabled') ? 'selected' : '')." hidden>Disabled</option>
                                                        </select>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='component_name'>Component:</label>";
                                                        $getComponent = "SELECT * FROM componenttable WHERE component_name != '{$row['component_name']}'";
                                                        $getComponentResult = $con->query($getComponent)->fetch_assoc();
                                                        ?>
                                                            <select name="component_name" id="component_name_<?php echo $row['student_number']?>" class="form-select">
                                                                <option value="<?php echo $row['component_name'] ?>" selected><?php echo $row['component_name'] ?></option>
                                                                <option value="<?php echo $getComponentResult['component_name'] ?>"><?php echo $getComponentResult['component_name'] ?></option>
                                                            </select>
                                                            <input type="hidden" name="updateSchoolYear" id="updateSchoolYear_<?php echo $row['student_number']?>" value="<?php echo $schoolyear_id ?>">
                                                            <input type="hidden" name="updateSemester" id="updateSemester_<?php echo $row['student_number']?>" value="<?php echo $semester_id ?>">
                                                            <input type="hidden" name="updateGroupId" id="updateGroupId_<?php echo $row['student_number']?>" value="<?php echo $row['group_id'] ?>">
                                                        <?php
                                                    echo "</div>
                                                        <div class='form-group'>
                                                            <label for='group_id'>Group Name:</label>
                                                            <select class='form-control' name='group_id' id='group_id_".$row['student_number']."' readonly>
                                                            
                                                            </select>
                                                        </div>
                                            </div>
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

                // Pagination links using Bootstrap
                echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                <ul class='pagination justify-content-center'>";

                // Determine the total number of pages
                $totalRecordsQuery = "SELECT COUNT(*) as total FROM useraccount t 
                            LEFT JOIN grouptable g ON t.group_id = g.group_id 
                            WHERE t.role_account_id = 2";

                if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = mysqli_real_escape_string($con, $_GET['search']);
                $totalRecordsQuery .= " AND (t.full_name LIKE '%$search%' OR t.student_number LIKE '%$search%' OR t.component_name LIKE '%$search%' OR g.group_name LIKE '%$search%')";
                }

                // Add the condition for schoolyear_id and semester_id
                $totalRecordsQuery .= " AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";

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
<script>
    var sendingEmail = false;
// Confirmation message when refreshing or leaving the page
window.addEventListener('beforeunload', function (e) {
  if (sendingEmail) {
    // Show confirmation message only if email sending process has started
    e.preventDefault();
    e.returnValue = '';

    var confirmationMessage =
      'Changes you made may not be saved. Are you sure you want to leave this page?';
    (e || window.event).returnValue = confirmationMessage;
    return confirmationMessage;
  }
});
document
  .getElementById('emailForm')
  .addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Call the sendEmail function
    sendEmail();
  });

function sendEmail() {
  sendingEmail = true;
  document.getElementById('sendButton').setAttribute('disabled', 'disabled'); // Disable the button
  document.getElementById('loader-overlay').style.display = 'block'; // Show the loader overlay
  document.getElementById('loader').style.display = 'block'; // Show the loader

  // Send the form data asynchronously
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'send_addstudent.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        var response = JSON.parse(xhr.responseText);
        if (response.status === 'success') {
          // Success
          sendingEmail = false;
          document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
          document.getElementById('loader').style.display = 'none'; // Hide the loader
          Swal.fire({
            title: 'Success',
            text: response.message,
            icon: 'success',
          }).then(function () {
            window.location.href = 'studentlist.php';
          });
        } else {
          // Error
          sendingEmail = false;
          document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
          document.getElementById('loader').style.display = 'none'; // Hide the loader
          Swal.fire({
            title: 'Error',
            text: response.message,
            icon: 'error',
          })
        }
      } else {
        // Error
        document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
        document.getElementById('loader').style.display = 'none'; // Hide the loader
        Swal.fire({
          title: 'Error',
          text: 'Email could not be sent. Please try again.',
          icon: 'error',
        }); // Display error message
      }
    }
  };

  // var fullName = document.getElementById('add_full_name').value;
  var firstname = document.getElementById('addfirstname').value;
  var middlename = document.getElementById('addmiddlename').value;
  var surname = document.getElementById('addsurname').value;
  var birthdayMonth = document.getElementById('addbirthdayMonth').value;
  var birthday_day = document.getElementById('addBirthDay').value;
  var birthday_year = document.getElementById('addBirthYear').value;
  var contactnumber = document.getElementById('addcontactnumber').value;
  var gender = document.getElementById('addgender').value;
  var saddress = document.getElementById('addStreetAddress').value;
  var caddress = document.getElementById('addCityAddress').value;
  var paddress = document.getElementById('addProvinceAddress').value;
  var course = document.getElementById('addCourse').value;
  var yearlevel = document.getElementById('addyearlevel').value;
  var section = document.getElementById('addsection').value;
  var emailAddress = document.getElementById('addEmailAddress').value;
  var studentNumber = document.getElementById('addStudentNumber').value;
  var componentId = document.getElementById('addComponentId').value;
  var groupId = document.getElementById('addGroupId').value;

  var data =
    'firstname=' +
    encodeURIComponent(firstname) +
    '&middlename=' +
    encodeURIComponent(middlename) +
    '&surname=' +
    encodeURIComponent(surname) +
    '&birthdayMonth=' +
    encodeURIComponent(birthdayMonth) +
    '&birthday_day=' +
    encodeURIComponent(birthday_day) +
    '&birthday_year=' +
    encodeURIComponent(birthday_year) +
    '&contactnumber=' +
    encodeURIComponent(contactnumber) +
    '&gender=' +
    encodeURIComponent(gender) +
    '&saddress=' +
    encodeURIComponent(saddress) +
    '&caddress=' +
    encodeURIComponent(caddress) +
    '&paddress=' +
    encodeURIComponent(paddress) +
    '&course=' +
    encodeURIComponent(course) +
    '&yearlevel=' +
    encodeURIComponent(yearlevel) +
    '&section=' +
    encodeURIComponent(section) +
    '&email_address=' +
    encodeURIComponent(emailAddress) +
    '&student_number=' +
    encodeURIComponent(studentNumber) +
    '&component_id=' +
    encodeURIComponent(componentId) +
    '&group_id=' +
    encodeURIComponent(groupId);

  xhr.send(data);
  // var formData = new FormData(document.getElementById("emailForm"));
  // formData.append("add_student", ""); // Add the 'add_student' parameter to the form data
  // xhr.send(formData);
}

</script>
<script src="./js/studentlist_deletestudent.js"></script>
<!-- <script src="./js/studentlist_loader.js"></script> -->
<script src="../asset/js/index.js"></script>
<script src="./js/studentlist_component.js"></script>
<script src="../asset/js/topbar.js"></script>
<script>
    const addfirstname = document.getElementById('addfirstname');
    const addfirstnameError = document.getElementById('addfirstnameError');
    const addmiddlename = document.getElementById('addmiddlename');
    const addmiddlenameError = document.getElementById('addmiddlenameError');
    const addsurname = document.getElementById('addsurname');
    const addsurnameError = document.getElementById('addsurnameError');
    const addbirthdayMonth = document.getElementById('addbirthdayMonth');
    const addbirthdayMonthError = document.getElementById('addbirthdayMonthError');
    const addBirthDay = document.getElementById('addBirthDay');
    const addBirthDayError = document.getElementById('addBirthDayError');
    const addBirthYear = document.getElementById('addBirthYear');
    const addBirthYearError = document.getElementById('addBirthYearError');
    const addcontactnumber = document.getElementById('addcontactnumber');
    const addcontactNumberError = document.getElementById('addcontactNumberError');
    const addgender = document.getElementById('addgender');
    const addgenderError = document.getElementById('addgenderError');
    const addEmailAddress = document.getElementById('addEmailAddress');
    const addEmailAddressError = document.getElementById('addEmailAddressError');
    const addStudentNumber = document.getElementById('addStudentNumber');
    const addStudentNumberError = document.getElementById('addStudentNumberError');
    const addComponentId = document.getElementById('addComponentId');
    const addComponentIdError = document.getElementById('addComponentIdError');
    const addyearlevel = document.getElementById('addyearlevel');
    const addyearlevelError = document.getElementById('addyearlevelError');
    const addsection = document.getElementById('addsection');
    const addsectionError = document.getElementById('addsectionError');
    const addGroupId = document.getElementById('addGroupId');
    const addGroupIdError = document.getElementById('addGroupIdError');
    const addCourse = document.getElementById('addCourse');
    const addCourseError = document.getElementById('addCourseError');
    const addStreetAddress = document.getElementById('addStreetAddress');
    const addStreetAddressError = document.getElementById('addStreetAddressError');
    const addCityAddress = document.getElementById('addCityAddress');
    const addCityAddressError = document.getElementById('addCityAddressError');
    const addProvinceAddress = document.getElementById('addProvinceAddress');
    const addProvinceAddressError = document.getElementById('addProvinceAddressError');

    const emptyFields = [];

    const submitButton = document.querySelector('button[name="add_student"]');

    const formId = 'emailForm';  // Set the ID of your form

    // Select all input and select elements within the specified form
    const inputElements = document.querySelectorAll(`#${formId} input`);
    const selectElements = document.querySelectorAll(`#${formId} select`);

    // Add input and select elements to the emptyFields array
    inputElements.forEach((inputElement) => {
        addToEmptyFields(inputElement);
    });

    selectElements.forEach((selectElement) => {
        addToEmptyFields(selectElement);
    });

    addComponentId.addEventListener('change', validateForm);

    addEmailAddress.addEventListener('input', function () {
        const email = addEmailAddress.value.trim();
        const emailPattern = /^[a-zA-Z0-9._-]+@cvsu\.edu\.ph$/;
        if (!emailPattern.test(email)) {
            addEmailAddressError.textContent = 'Email should be in the format example@cvsu.edu.ph';
            // If validation fails, add the field to the emptyFields array
            addToEmptyFields(addEmailAddress);
        } else {
            addEmailAddressError.textContent = '';
            // If validation succeeds, remove the field from the emptyFields array
            removeFromEmptyFields(addEmailAddress);
        }
        validateForm();
    });

    addcontactnumber.addEventListener('input', function () {
        validateInput(addcontactnumber, addcontactNumberError, 'Fill up the contact number', /^\+639\d{9}$|^09\d{9}$/);
    });

    function validateInput(input, error, errorMessage, pattern) {
        const value = input.value.trim();

        if (!pattern.test(value)) {
            error.textContent = errorMessage;
            addToEmptyFields(addcontactnumber);
        } else {
            error.textContent = '';
            removeFromEmptyFields(addcontactnumber);
        }

        validateForm();
    }

    addStudentNumber.addEventListener('input', function() {
        const studentNumber = addStudentNumber.value.trim();
        const studentNumberPattern = /^\d{9}$/;
        if (!studentNumberPattern.test(studentNumber)) {
            addStudentNumberError.textContent = 'Student number should be 9 digits';
            addToEmptyFields(addStudentNumber);
        } else {
            addStudentNumberError.textContent = '';
            removeFromEmptyFields(addStudentNumber);
        }
        validateForm();
    });

    addfirstname.addEventListener('input', function() {
        const firstnameValue = addfirstname.value.trim();
        if (firstnameValue === '') {
            addfirstnameError.textContent = 'Fill up this first name';
            addToEmptyFields(addfirstname);
        } else {
            addfirstnameError.textContent = '';
            removeFromEmptyFields(addfirstname);
        }
        validateForm();
    });

    addmiddlename.addEventListener('input', function() {
        const middlenameValue = addmiddlename.value.trim();
        if (middlenameValue === '') {
            addmiddlenameError.textContent = 'Fill up this middle name';
            addToEmptyFields(addmiddlename);
        } else {
            addmiddlenameError.textContent = '';
            removeFromEmptyFields(addmiddlename);
        }
        validateForm();
    });

    addsurname.addEventListener('input', function() {
        const surnameValue = addsurname.value.trim();
        if (surnameValue === '') {
            addsurnameError.textContent = 'Fill up this surname name';
            addToEmptyFields(addsurname);
        } else {
            addsurnameError.textContent = '';
            removeFromEmptyFields(addsurname);
        }
        validateForm();
    });

    addStreetAddress.addEventListener('input', function() {
        const streetAddressValue = addStreetAddress.value.trim();
        if (streetAddressValue === '') {
            addStreetAddressError.textContent = 'Fill up this Street/Baranggay';
            addToEmptyFields(addStreetAddress);
        } else {
            addStreetAddressError.textContent = '';
            removeFromEmptyFields(addStreetAddress);
        }
        validateForm();
    });

    addCityAddress.addEventListener('input', function() {
        const cityAddressValue = addCityAddress.value.trim();
        if (cityAddressValue === '') {
            addCityAddressError.textContent = 'Fill up this Street/Baranggay';
            addToEmptyFields(addCityAddress);
        } else {
            addCityAddressError.textContent = '';
            removeFromEmptyFields(addCityAddress);
        }
        validateForm();
    });

    addCityAddress.addEventListener('input', function() {
        const cityAddressValue = addCityAddress.value.trim();
        if (cityAddressValue === '') {
            addCityAddressError.textContent = 'Fill up this City/Municipality';
            addToEmptyFields(addCityAddress);
        } else {
            addCityAddressError.textContent = '';
            removeFromEmptyFields(addCityAddress);
        }
        validateForm();
    });

    addProvinceAddress.addEventListener('input', function() {
        const provinceAddressValue = addProvinceAddress.value.trim();
        if (provinceAddressValue === '') {
            addProvinceAddressError.textContent = 'Fill up this Province';
            addToEmptyFields(addProvinceAddress);
        } else {
            addProvinceAddressError.textContent = '';
            removeFromEmptyFields(addProvinceAddress);
        }
        validateForm();
    });

    addGroupId.addEventListener('change', function() {
        const groupIdValue = addGroupId.value.trim();
        if (groupIdValue === '') {
            addGroupIdError.textContent = 'Fill up this Group Name';
            addToEmptyFields(addGroupId);
        } else {
            addGroupIdError.textContent = '';
            removeFromEmptyFields(addGroupId);
        }
        validateForm();
    });

    addyearlevel.addEventListener('change', function() {
        const yearLevelValue = addyearlevel.value.trim();
        if (yearLevelValue === '') {
            addyearlevelError.textContent = 'Fill up this Year Level';
            addToEmptyFields(addyearlevel);
        } else {
            addyearlevelError.textContent = '';
            removeFromEmptyFields(addyearlevel);
        }
        validateForm();
    });

    addsection.addEventListener('change', function() {
        const sectionValue = addsection.value.trim();
        if (sectionValue === '') {
            addsectionError.textContent = 'Fill up this Year Level';
            addToEmptyFields(addsection);
        } else {
            addsectionError.textContent = '';
            removeFromEmptyFields(addsection);
        }
        validateForm();
    });

    addComponentId.addEventListener('change', function() {
        const componentIdValue = addComponentId.value.trim();
        if (componentIdValue === '') {
            addComponentIdError.textContent = 'Fill up this Component';
            addToEmptyFields(addComponentId);
        } else {
            addComponentIdError.textContent = '';
            removeFromEmptyFields(addComponentId);
            addToEmptyFields(addGroupId);
        }
        validateForm();
    });

    addCourse.addEventListener('change', function() {
        const courseValue = addCourse.value.trim();
        if (courseValue === '') {
            addCourseError.textContent = 'Fill up this Course';
            addToEmptyFields(addCourse);
        } else {
            addCourseError.textContent = '';
            removeFromEmptyFields(addCourse);
        }
        validateForm();
    });

    addgender.addEventListener('change', function() {
        const genderValue = addgender.value.trim();
        if (genderValue === '') {
            addgenderError.textContent = 'Fill up this Gender';
            addToEmptyFields(addgender);
        } else {
            addgenderError.textContent = '';
            removeFromEmptyFields(addgender);
        }
        validateForm();
    });

    addBirthYear.addEventListener('change', function() {
        const birthYearValue = addBirthYear.value.trim();
        if (birthYearValue === '') {
            addBirthYearError.textContent = 'Fill up this Year';
            addToEmptyFields(addBirthYear);
        } else {
            addBirthYearError.textContent = '';
            removeFromEmptyFields(addBirthYear);
        }
        validateForm();
    });

    addBirthDay.addEventListener('change', function() {
        const birthDayValue = addBirthDay.value.trim();
        if (birthDayValue === '') {
            addBirthDayError.textContent = 'Fill up this Day';
            addToEmptyFields(addBirthDay);
        } else {
            addBirthDayError.textContent = '';
            removeFromEmptyFields(addBirthDay);
        }
        validateForm();
    });

    addbirthdayMonth.addEventListener('change', function() {
        const birthMonthValue = addbirthdayMonth.value.trim();
        if (birthMonthValue === '') {
            addbirthdayMonthError.textContent = 'Fill up this Month';
            addToEmptyFields(addbirthdayMonth);
        } else {
            addbirthdayMonthError.textContent = '';
            removeFromEmptyFields(addbirthdayMonth);
        }
        validateForm();
    });

    function addToEmptyFields(input) {
        const fieldName = input.name;
        if (!emptyFields.includes(fieldName)) {
            emptyFields.push(fieldName);
        }
    }

    function removeFromEmptyFields(input) {
        const fieldName = input.name;
        const index = emptyFields.indexOf(fieldName);
        if (index !== -1) {
            emptyFields.splice(index, 1);
        }
    }

    function validateForm() {

        if (emptyFields.length > 0) {
            console.log('Empty Fields:', emptyFields);
            submitButton.disabled = true;
        } else {
            submitButton.disabled = false;
        }

    }
    validateForm();

    // Get the select element and download button
    const selectElement = document.getElementById('download');
    const downloadButton = document.getElementById('downloadbutton');
    const selectedfile = document.getElementById('serialNumber');
    const uploadSerialNumberButton = document.getElementById('uploadSerialNumberButton');

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
    selectedfile.addEventListener('change', function() {
        // Enable the download button if a selection is made
        if (selectedfile.value !== '') {
        uploadSerialNumberButton.disabled = false;
        } else {
        // Disable the download button if no selection is made
        uploadSerialNumberButton.disabled = true;
        }
    });

    function restrictInputToRange(event, min, max) {
        const input = event.target;
        const value = input.value;
        
        if (isNaN(value) || value < min || value > max) {
            input.value = '';
        }
    }
    </script>
    <script>
    $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    });
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
    })

    </script>
    <script>
// $(document).ready(function () {
//     // Function to handle changes in the component_name dropdown
//     function handleComponentChange(student_number) {
//         var component_name = $('#component_name_' + student_number).val();
//         var updateSchoolYear = $('#updateSchoolYear_' + student_number).val();
//         var updateSemester = $('#updateSemester_' + student_number).val();

//         // AJAX request to get groups based on the selected component_name
//         $.ajax({
//             type: 'POST',
//             url: 'get_groups.php',
//             data: { component_name: component_name },
//             success: function (response) {
//                 // Update the group_id dropdown with the new options
//                 $('#group_id_' + student_number).html(response);
//             }
//         });
//     }

//     // Event handler for changes in the component_name dropdown
//     $('[id^=component_name]').change(function () {
//         var student_number = $(this).attr('id').split('_')[2];
//         handleComponentChange(student_number);
//     });

//     // Trigger onchange event when the modal is fully shown
//     $('.myModal').on('shown.bs.modal', function (e) {
//         var student_number = $(this).attr('id').split('studentupdatemodal')[1];
//         handleComponentChange(student_number);
//     });
// });
$(document).ready(function () {
    // Function to handle changes in the component_name dropdown
    function handleComponentChange(student_number) {
        var component_name = $('#component_name_' + student_number).val();
        var updateSchoolYear = $('#updateSchoolYear_' + student_number).val();
        var updateSemester = $('#updateSemester_' + student_number).val();
        var updateGroupId = $('#updateGroupId_' + student_number).val();

        // AJAX request to get groups based on the selected component_name
        $.ajax({
            type: 'POST',
            url: 'get_groups.php',
            data: { component_name: component_name },
            success: function (response) {
                // Update the group_id dropdown with the new options
                var groupDropdown = $('#group_id_' + student_number);
                groupDropdown.html(response);

                // Set the selected value to $row['group_id']
                groupDropdown.val(updateGroupId);
            }
        });
    }

    // Event handler for changes in the component_name dropdown
    $('[id^=component_name]').change(function () {
        var student_number = $(this).attr('id').split('_')[2];
        handleComponentChange(student_number);
    });

    // Trigger onchange event when the modal is fully shown
    $('.myModal').on('shown.bs.modal', function (e) {
        var student_number = $(this).attr('id').split('studentupdatemodal')[1];
        handleComponentChange(student_number);
    });
});

</script>
</body>
</html>