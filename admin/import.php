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
include_once('adminsidebar.php');

require '../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 

function generateRandomPassword() {
    $length = 8; // Minimum length of the password
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';

    $characters = $lowercase . $uppercase . $digits;
    $password = '';

    // Add at least one lowercase letter, one uppercase letter, and one digit
    $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
    $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
    $password .= $digits[rand(0, strlen($digits) - 1)];

    // Add remaining characters
    $remainingLength = $length - 3;
    for ($i = 0; $i < $remainingLength; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Shuffle the characters to ensure random order
    $password = str_shuffle($password);

    return $password;
}
$cwts_groups = array(
    "Cluster A",
    "Cluster B",
    "Cluster C",
    "Cluster D",
    "Cluster E",
    "Cluster F",
    "Cluster G",
    "Cluster H",
    "Cluster I",
    "Cluster J",
    "Cluster K",
    "Cluster L",
    "Cluster M",
    "Cluster N",
    "Cluster O",
    "Cluster P",
    "Cluster Q",
    "Cluster R"
);

$rotc_groups = array(
    "Theoretical",
    "Medic",
    "Alpha 1st",
    "Alpha 2nd",
    "Alpha 3rd",
    "Bravo 1st",
    "Bravo 2nd",
    "Bravo 3rd",
    "Charlie 1st",
    "Charlie 2nd",
    "Charlie 3rd",
    "Delta 1st",
    "Delta 2nd",
    "Delta 3rd",
    "Echo 1st",
    "Echo 2nd",
    "Echo 3rd",
    "Foxtrot 1st",
    "Foxtrot 2nd",
    "Foxtrot 3rd"
);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importMISData'])) {
    $schoolyear_start = $_POST['schoolyear_start'];
    $schoolyear_end = $_POST['schoolyear_end'];
    $schoolyear = $_POST['schoolyear'];
    $semester_id = $_POST['semester_id'];

    $schoolyear_checking_query = "SELECT * FROM schoolyeartable WHERE schoolyear_start = '$schoolyear_start' AND schoolyear_end = '$schoolyear_end' AND semester_id = $semester_id";
    $schoolyear_checking_result = $con->query($schoolyear_checking_query);
    $schoolyear_checking_data = $schoolyear_checking_result->fetch_assoc();
    
    if ($schoolyear_checking_data) {
        $schoolyear_id = $schoolyear_checking_data['schoolyear_id'];
    } else {
        $max_id_query = "SELECT MAX(schoolyear_id) AS max_id FROM schoolyeartable";
        $max_id_result = $con->query($max_id_query);
        $max_id_data = $max_id_result->fetch_assoc();
        $schoolyear_id = $max_id_data['max_id'] + 1;
    }
    
    $successFlag = false; // Flag variable to track successful insertions
    $firstLoop = true; // Flag variable to indicate if it's the first loop
    $copy_teacher = false;
    $successtrigger = false;

    if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['import_file']['name'];
        $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowed_ext = ['xls', 'csv', 'xlsx'];

        if (in_array($file_ext, $allowed_ext)) {
            $inputFileNamePath = $_FILES['import_file']['tmp_name'];
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Skip the first row (header row)
            array_shift($data);

            $existingStudents = [];
            $errorMessages = [];

            foreach ($data as $row) {
                // escape special characters in fields
                $student_number = mysqli_real_escape_string($con, $row['A']);
                $student_name = mysqli_real_escape_string($con, $row['B']);
                $student_email = mysqli_real_escape_string($con, $row['C']);

                // Check if the student number is a valid integer and has 9 digits
                if (!ctype_digit($student_number) || strlen($student_number) !== 9) {
                    // Handle invalid student number format
                    $errorMessages[] = "Invalid student number format: " . $student_number;
                    continue; // Skip this iteration and move to the next row
                }

                // THIS IS THE START OF SECOND SEMESTER

                if($semester_id == 1){
                    // check if the record already exists in the database
                    $checkQuery = "SELECT * FROM enrolledstudent WHERE student_number = '$student_number' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";

                    $checkResult = mysqli_query($con, $checkQuery);
                    if (mysqli_num_rows($checkResult) === 0) {
                        // if the record does not exist, insert it into the database
                        $studentQuery = "INSERT INTO enrolledstudent (student_number, student_name, student_email, schoolyear_id, semester_id) VALUES ($student_number, '$student_name', '$student_email', $schoolyear_id, $semester_id)";
                        $result = mysqli_query($con, $studentQuery); // execute query
                        if (!$result) {
                            $errorMessages[] = "Failed to insert data: " . mysqli_error($con);
                        }
                    } else {
                        // Collect existing student numbers and names
                        $existingStudents[] = array('number' => $student_number, 'name' => $student_name);
                    }

                }elseif($semester_id == 2){
                    $previous_semester_id = $semester_id - 1;
                    $previous_schoolyear_id = $schoolyear_id - 1;
                    
                    // echo"<script>console.log($previous_semester_id);</script>";
                    // echo"<script>console.log($previous_schoolyear_id);</script>";
                    // echo"<script>console.log($firstLoop);</script>";

                    $checking_previews_group = "SELECT COUNT(*) as previewsGroup_count FROM grouptable WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                    $checking_previews_group_result = $con->query($checking_previews_group);
                    $checking_previews_group_data = $checking_previews_group_result->fetch_assoc();
                    $previewsGroup_count = $checking_previews_group_data['previewsGroup_count'];
                    echo "<script>console.log('" . empty($checking_previews_group_data) . "');</script>";
            
                    if ($previewsGroup_count == 0 && $firstLoop) {
                        echo"<script>console.log('previewsGroup_count');</script>";
                        // Insert new group records for the current semester based on the previous semester's incharge_person for each group
                        $new_group_record = "INSERT INTO grouptable (group_name, incharge_person, component_id, number_student, schoolyear_id, semester_id, date_created, date_updated)
                        SELECT group_name, incharge_person, component_id, number_student, $schoolyear_id as schoolyear_id, $semester_id as semester_id, NOW() as date_created, NOW() as date_updated 
                        FROM grouptable WHERE schoolyear_id = $previous_schoolyear_id AND semester_id = $previous_semester_id";
                        $new_group_record_result = $con->query($new_group_record);
            
                        if($new_group_record_result){
                            $firstLoop = false; // Set the flag to false after the first loop to prevent copying again
                            $copy_teacher = true;
                        }else{
                            $firstLoop = false; // Set the flag to false after the first loop to prevent copying again
                            $copy_teacher = false;
                        }
                        
                        if ($copy_teacher) {
                            $listOfTeacher = "SELECT * FROM useraccount WHERE role_account_id = 3 AND schoolyear_id = $previous_schoolyear_id AND semester_id = 1";
                            $listOfTeacher_result = $con->query($listOfTeacher);
                        
                            if ($listOfTeacher_result->num_rows > 0) {
                                while ($teacher = $listOfTeacher_result->fetch_assoc()) {
                                    $oldTeacherGroup_id = $teacher['group_id'];
                                    $oldTeacherUser_account_id = $teacher['user_account_id'];
                        
                                    echo "<script>console.log($oldTeacherGroup_id);</script>";
                                    if (!empty($oldTeacherGroup_id)) {
                                        $getOldGroupName = "SELECT group_name FROM grouptable WHERE group_id = '$oldTeacherGroup_id'";
                                        $getOldGroupName_result = $con->query($getOldGroupName);
                                        $getOldGroupName_data = $getOldGroupName_result->fetch_assoc();
                        
                                        if (!empty($getOldGroupName_data)) {
                                            $OldgroupName = $getOldGroupName_data['group_name'];
                        
                                            $get_group_id = "SELECT group_id FROM grouptable WHERE group_name = '$OldgroupName' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                                            $get_group_id_result = $con->query($get_group_id);
                                            $get_group_id_data = $get_group_id_result->fetch_assoc();
                        
                                            if (!empty($get_group_id_data)) {
                                                $new_group_id = $get_group_id_data['group_id'];
                                            } else {
                                                $new_group_id = "NULL";
                                            }
                                        } else {
                                            // Handle the case when group_name is not found
                                            $new_group_id = "NULL";
                                        }
                                    } else {
                                        $new_group_id = "NULL"; // Set $new_group_id to the string "NULL" (as it is not a variable but a SQL keyword)
                                    }
                        
                                    $copy_data = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, picture, schoolyear_id, semester_id) SELECT serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, $new_group_id as group_id, user_status, qrimage, picture, $schoolyear_id as schoolyear_id, $semester_id as semester_id FROM useraccount WHERE user_account_id = $oldTeacherUser_account_id";
                        
                                    $copy_data_result = $con->query($copy_data);
                                }
                            } else {
                                echo "<script>console.log('No teachers found matching the criteria.');</script>";
                            }
                        }
                        
                        
                        
                        
                    }

                    $checkQuery = "SELECT * FROM enrolledstudent WHERE student_number = '$student_number' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";

                    $checkResult = mysqli_query($con, $checkQuery);
                    if (mysqli_num_rows($checkResult) === 0) {
                       
                        $existing_studentInFirstSemester = "SELECT u.user_account_id, u.student_number, u.group_id, g.group_name FROM useraccount u LEFT JOIN grouptable g ON u.group_id = g.group_id WHERE u.student_number = $student_number AND u.schoolyear_id = $previous_schoolyear_id AND u.semester_id = 1";
                        $existing_studentInFirstSemester_result = $con->query($existing_studentInFirstSemester);
                        $existing_studentInFirstSemester_data = $existing_studentInFirstSemester_result->fetch_assoc();

                        if($existing_studentInFirstSemester_data){

                            $previews_useraccount = $existing_studentInFirstSemester_data['user_account_id'];
                            $previews_group_name = $existing_studentInFirstSemester_data['group_name'];

                            $get_group_id = "SELECT group_id FROM grouptable WHERE group_name = '$previews_group_name' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                            $get_group_id_result = $con->query($get_group_id);
                            $get_group_id_data = $get_group_id_result->fetch_assoc();
                            $new_group_id = $get_group_id_data['group_id'];

                            if($previews_useraccount){
                                $copy_data = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, picture, schoolyear_id, semester_id) SELECT serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, $new_group_id as group_id, user_status, qrimage, picture, $schoolyear_id as schoolyear_id, $semester_id as semester_id FROM useraccount WHERE user_account_id = $previews_useraccount";

                                $copy_data_result = $con->query($copy_data);

                                if($copy_data_result){
                                    $studentQuery = "INSERT INTO enrolledstudent (student_number, student_name, student_email, registration_status, schoolyear_id, semester_id) VALUES ($student_number, '$student_name', '$student_email', 'Registered', $schoolyear_id, $semester_id)";
                                    $result = mysqli_query($con, $studentQuery); // execute query
                                    if (!$result) {
                                        $errorMessages[] = "Failed to insert data: " . mysqli_error($con);
                                    }
                                }else{
                                    $studentQuery = "INSERT INTO enrolledstudent (student_number, student_name, student_email, schoolyear_id, semester_id) VALUES ($student_number, '$student_name', '$student_email', $schoolyear_id, $semester_id)";
                                    $result = mysqli_query($con, $studentQuery); // execute query
                                    if (!$result) {
                                        $errorMessages[] = "Failed to insert data: " . mysqli_error($con);
                                    }
                                }
                            }
                        }else{
                            $studentQuery = "INSERT INTO enrolledstudent (student_number, student_name, student_email, schoolyear_id, semester_id) VALUES ($student_number, '$student_name', '$student_email', $schoolyear_id, $semester_id)";
                            $result = mysqli_query($con, $studentQuery); // execute query
                            if (!$result) {
                                $errorMessages[] = "Failed to insert data: " . mysqli_error($con);
                            }
                        }
                    } else {
                        // Collect existing student numbers and names
                        $existingStudents[] = array('number' => $student_number, 'name' => $student_name);
                    }
                }
            }

            $successCount = count($data) - count($existingStudents);
            if ($successCount > 0) {
                $successFlag = true; // Set the flag to true if there were successful insertions
                $successMessage = "Data imported successfully. Total records imported: $successCount";
                $successtrigger = true;
            }

            // Display SweetAlert2 messages
            if (!empty($existingStudents)) {
                $errorMessage = "The following student records already exist:<br><br>";
                $errorMessage .= "<table>";
                $errorMessage .= "<tr><th>Student Number</th><th>Student Name</th></tr>";
                foreach ($existingStudents as $existingStudent) {
                    $errorMessage .= "<tr><td>" . $existingStudent['number'] . "</td><td>" . $existingStudent['name'] . "</td></tr>";
                }
                $errorMessage .= "</table>";

                echo "<script>
                    Swal.fire({
                        title: 'Student Record Already Exist',
                        icon: 'warning',
                        html: '$errorMessage',
                    }).then(function () {
                        window.location.href = 'import.php';
                    });
                </script>";
            }

            if (!empty($errorMessages)) {
                $errorMessage = implode("\\n", $errorMessages);
                echo "<script>
                    Swal.fire({
                        title: 'Error',
                        icon: 'error',
                        text: '$errorMessage',
                    }).then(function () {
                        window.location.href = 'import.php';
                    });
                </script>";
            } elseif ($successFlag && empty($schoolyear_checking_data)) {
                $schoolyear_save_query = "INSERT INTO schoolyeartable (schoolyear_id, schoolyear_start, schoolyear_end, schoolyear, semester_id) VALUES ($schoolyear_id, '$schoolyear_start', '$schoolyear_end', '$schoolyear', $semester_id)";
                $schoolyear_save_result = $con->query($schoolyear_save_query);
                
            }
            if ($successtrigger) {
                echo "<script>
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: '$successMessage',
                    }).then(function () {
                        window.location.href = 'import.php';
                    });
                </script>";
            } 
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error',
                    icon: 'error',
                    text: 'Invalid file format. Only .xls, .csv, and .xlsx files are allowed.',
                }).then(function () {
                    window.location.href = 'import.php';
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
                window.location.href = 'import.php';
            });
        </script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_excel_data'])) {
    $schoolyear_start = $_POST['schoolyear_start'];
    $schoolyear_end = $_POST['schoolyear_end'];
    $schoolyear = $_POST['schoolyear'];
    $semester_id = $_POST['semester_id'];
    $chooseComponent = $_POST['chooseComponent'];

    $schoolyear_checking_query = "SELECT * FROM schoolyeartable WHERE schoolyear_start = '$schoolyear_start' AND schoolyear_end = '$schoolyear_end' AND semester_id = $semester_id";
    $schoolyear_checking_result = $con->query($schoolyear_checking_query);
    $schoolyear_checking_data = $schoolyear_checking_result->fetch_assoc();
    
    if ($schoolyear_checking_data) {
        $schoolyear_id = $schoolyear_checking_data['schoolyear_id'];
    } else {
        $max_id_query = "SELECT MAX(schoolyear_id) AS max_id FROM schoolyeartable";
        $max_id_result = $con->query($max_id_query);
        $max_id_data = $max_id_result->fetch_assoc();
        $schoolyear_id = $max_id_data['max_id'] + 1;
    }
    
    $successFlag = false; // Flag variable to track successful insertions
    $firstLoop = true; // Flag variable to indicate if it's the first loop
    $copy_teacher = false;
    $successtrigger = false;

    if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['import_file']['name'];
        $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowed_ext = ['xls', 'csv', 'xlsx'];

        if (in_array($file_ext, $allowed_ext)) {
            $inputFileNamePath = $_FILES['import_file']['tmp_name'];
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Skip the first row (header row)
            array_shift($data);

            $existingStudents = [];
            $errorMessages = [];

            foreach ($data as $row) {
                // escape special characters in fields
                $timeStamp = mysqli_real_escape_string($con, $row['A']);
                $csvEmail_address = mysqli_real_escape_string($con, $row['B']);
                $csvSurname = mysqli_real_escape_string($con, $row['C']);
                $csvFirstname = mysqli_real_escape_string($con, $row['D']);
                $csvmiddlename = mysqli_real_escape_string($con, $row['E']);
                $csvTshirtSize = mysqli_real_escape_string($con, $row['F']);
                $csvCourse = mysqli_real_escape_string($con, $row['G']);
                $csvStudent_number = mysqli_real_escape_string($con, $row['H']);
                $csvHomeAddress = mysqli_real_escape_string($con, $row['I']);
                $csvYear_level = mysqli_real_escape_string($con, $row['J']);
                $csvContactNumber = mysqli_real_escape_string($con, $row['K']);
                $csvBirthday = mysqli_real_escape_string($con, $row['L']);
                $userAge = mysqli_real_escape_string($con, $row['M']);
                $csvPlaceOfBirth = mysqli_real_escape_string($con, $row['N']);
                $csvReligion = mysqli_real_escape_string($con, $row['O']);
                $csvStatus = mysqli_real_escape_string($con, $row['P']);
                $csvHeight = mysqli_real_escape_string($con, $row['Q']);
                $csvWeight = mysqli_real_escape_string($con, $row['R']);
                $csvComplexion = mysqli_real_escape_string($con, $row['S']);
                $csvBloodType = mysqli_real_escape_string($con, $row['T']);
                $csvGender = mysqli_real_escape_string($con, $row['U']);
                $csvSpouseName = mysqli_real_escape_string($con, $row['V']);
                $csvContactNumber = mysqli_real_escape_string($con, $row['W']);
                $csvOccupation = mysqli_real_escape_string($con, $row['X']);

                $csvguardianName = mysqli_real_escape_string($con, $row['Y']);
                $csvguardianRelationship = mysqli_real_escape_string($con, $row['Z']);
                $csvguardianContactNumber = mysqli_real_escape_string($con, $row['AA']);

                $csvFull_name = $csvSurname . ' ' . $csvFirstname . ' ' . $csvmiddlename;
                $cvsAge = preg_replace('/[^0-9]/', '', $userAge);
                $csvCourse = explode(',', $csvCourse)[0];

                // Check if the student number is a valid integer and has 9 digits
                if (!ctype_digit($csvStudent_number) || strlen($csvStudent_number) !== 9) {
                    // Handle invalid student number format
                    $errorMessages[] = "Invalid student number format: " . $csvStudent_number;
                    continue; // Skip this iteration and move to the next row
                }

                // THIS IS THE START OF SECOND SEMESTER

                if($semester_id == 1){
                    // check if the record already exists in the database
                    $checkQuery = "SELECT * FROM enrolledstudent WHERE student_number = '$csvStudent_number' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";

                    $checkResult = mysqli_query($con, $checkQuery);
                    if (mysqli_num_rows($checkResult) === 0) {

                        // if the record does not exist, insert it into the database
                        $studentQuery = "INSERT INTO enrolledstudent (student_number, student_name, student_email, schoolyear_id, semester_id) VALUES ($csvStudent_number, '$csvFull_name', '$csvEmail_address', $schoolyear_id, $semester_id)";
                        $result = mysqli_query($con, $studentQuery); // execute query

                        if (!$result) {
                            $errorMessages[] = "Failed to insert data: " . mysqli_error($con);
                        }else{

                            $temp_password = generateRandomPassword();
                            $password = base64_encode($temp_password);
    
                            $qrcodes_dir = '../qrcodes';
                            if (!is_dir($qrcodes_dir)) {
                                mkdir($qrcodes_dir, 0777, true); // Create directory with write permissions
                            }
    
                            $secret_code = base64_encode($csvStudent_number);
                            $qrfull_name = str_replace(['/', '\\', ' '], '', $csvFirstname);
                            require_once('../phpqrcode/qrlib.php');
                            $qrdata = $secret_code;
                            
                            $qrfile = "../qrcodes/$qrfull_name.png";
                            QRcode::png($qrdata, $qrfile);

                            $csvUseraccount_query = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, homeaddress, course, year_level, gender, birthday, placeOFBirth, studentReligion, studentAge, student_number, studentStatus, studentHeight, studentWeight, studentComplexion,studentBloodType, component_name, spouseName, spouseContactNumber, spouseOccupation, group_id, user_status, qrimage, picture, schoolyear_id, semester_id, sendEmail, `timeStamp`) VALUES (NULL, '$password', 2, '$csvSurname', '$csvFirstname', '$csvmiddlename', '$csvFull_name', '$csvEmail_address', '$csvContactNumber', '$csvHomeAddress', '$csvCourse', '$csvYear_level', '$csvGender', '$csvBirthday', '$csvPlaceOfBirth', '$csvReligion',$cvsAge, '$csvStudent_number', '$csvStatus', '$csvHeight', '$csvWeight', '$csvComplexion', '$csvBloodType', '$chooseComponent', '$csvSpouseName', '$csvContactNumber', '$csvOccupation', NULL, 'Active', '$qrfile', NULL, $schoolyear_id, $semester_id, 0, '$timeStamp')";
                            $csvUseraccount_result = $con->query($csvUseraccount_query);

                            // TURN to 1 the sendEmail if not trial

                            if($csvUseraccount_result){
                                $emergencyQuery = "INSERT INTO emergencycontact (studentNumber, guardianName, guardianRelationship, guardianContactNumber) VALUES ('$csvStudent_number', '$csvguardianName', '$csvguardianRelationship', '$csvguardianContactNumber')";
                                $emergencyResult = $con->query($emergencyQuery);

                                $updateStatus = "UPDATE enrolledstudent SET registration_status = 'Registered' WHERE student_number = $csvStudent_number";
                                $updateStatusResult = mysqli_query($con,$updateStatus);
                            }
                        }
                    } else {
                        // Collect existing student numbers and names
                        $existingStudents[] = array('number' => $csvStudent_number, 'name' => $csvFull_name);
                    }

                }elseif($semester_id == 2){
                    $previous_semester_id = $semester_id - 1;
                    $previous_schoolyear_id = $schoolyear_id - 1;

                    $checking_previews_group = "SELECT COUNT(*) as previewsGroup_count FROM grouptable WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                    $checking_previews_group_result = $con->query($checking_previews_group);
                    $checking_previews_group_data = $checking_previews_group_result->fetch_assoc();
                    $previewsGroup_count = $checking_previews_group_data['previewsGroup_count'];
                    echo "<script>console.log('" . empty($checking_previews_group_data) . "');</script>";
            
                    if ($previewsGroup_count == 0 && $firstLoop) {
                        echo"<script>console.log('previewsGroup_count');</script>";
                        // Insert new group records for the current semester based on the previous semester's incharge_person for each group
                        $new_group_record = "INSERT INTO grouptable (group_name, incharge_person, component_id, number_student, schoolyear_id, semester_id, date_created, date_updated)
                        SELECT group_name, incharge_person, component_id, number_student, $schoolyear_id as schoolyear_id, $semester_id as semester_id, NOW() as date_created, NOW() as date_updated 
                        FROM grouptable WHERE schoolyear_id = $previous_schoolyear_id AND semester_id = $previous_semester_id";
                        $new_group_record_result = $con->query($new_group_record);
            
                        if($new_group_record_result){
                            $firstLoop = false; // Set the flag to false after the first loop to prevent copying again
                            $copy_teacher = true;
                        }else{
                            $firstLoop = false; // Set the flag to false after the first loop to prevent copying again
                            $copy_teacher = false;
                        }
                        
                        if ($copy_teacher) {
                            $listOfTeacher = "SELECT * FROM useraccount WHERE role_account_id = 3 AND schoolyear_id = $previous_schoolyear_id AND semester_id = 1";
                            $listOfTeacher_result = $con->query($listOfTeacher);
                        
                            if ($listOfTeacher_result->num_rows > 0) {
                                while ($teacher = $listOfTeacher_result->fetch_assoc()) {
                                    $oldTeacherGroup_id = $teacher['group_id'];
                                    $oldTeacherUser_account_id = $teacher['user_account_id'];
                        
                                    echo "<script>console.log($oldTeacherGroup_id);</script>";
                                    if (!empty($oldTeacherGroup_id)) {
                                        $getOldGroupName = "SELECT group_name FROM grouptable WHERE group_id = '$oldTeacherGroup_id'";
                                        $getOldGroupName_result = $con->query($getOldGroupName);
                                        $getOldGroupName_data = $getOldGroupName_result->fetch_assoc();
                        
                                        if (!empty($getOldGroupName_data)) {
                                            $OldgroupName = $getOldGroupName_data['group_name'];
                        
                                            $get_group_id = "SELECT group_id FROM grouptable WHERE group_name = '$OldgroupName' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                                            $get_group_id_result = $con->query($get_group_id);
                                            $get_group_id_data = $get_group_id_result->fetch_assoc();
                        
                                            if (!empty($get_group_id_data)) {
                                                $new_group_id = $get_group_id_data['group_id'];
                                            } else {
                                                $new_group_id = "NULL";
                                            }
                                        } else {
                                            // Handle the case when group_name is not found
                                            $new_group_id = "NULL";
                                        }
                                    } else {
                                        $new_group_id = "NULL"; // Set $new_group_id to the string "NULL" (as it is not a variable but a SQL keyword)
                                    }
                        
                                    $copy_data = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, picture, schoolyear_id, semester_id) SELECT serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, $new_group_id as group_id, user_status, qrimage, picture, $schoolyear_id as schoolyear_id, $semester_id as semester_id FROM useraccount WHERE user_account_id = $oldTeacherUser_account_id";
                        
                                    $copy_data_result = $con->query($copy_data);
                                }
                            } else {
                                echo "<script>console.log('No teachers found matching the criteria.');</script>";
                            }
                        } 
                    }

                    $checkQuery = "SELECT * FROM enrolledstudent WHERE student_number = '$student_number' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";

                    $checkResult = mysqli_query($con, $checkQuery);
                    if (mysqli_num_rows($checkResult) === 0) {
                       
                        $existing_studentInFirstSemester = "SELECT u.user_account_id, u.student_number, u.group_id, g.group_name FROM useraccount u LEFT JOIN grouptable g ON u.group_id = g.group_id WHERE u.student_number = $student_number AND u.schoolyear_id = $previous_schoolyear_id AND u.semester_id = 1";
                        $existing_studentInFirstSemester_result = $con->query($existing_studentInFirstSemester);
                        $existing_studentInFirstSemester_data = $existing_studentInFirstSemester_result->fetch_assoc();

                        if($existing_studentInFirstSemester_data){

                            $previews_useraccount = $existing_studentInFirstSemester_data['user_account_id'];
                            $previews_group_name = $existing_studentInFirstSemester_data['group_name'];

                            $get_group_id = "SELECT group_id FROM grouptable WHERE group_name = '$previews_group_name' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                            $get_group_id_result = $con->query($get_group_id);
                            $get_group_id_data = $get_group_id_result->fetch_assoc();
                            $new_group_id = $get_group_id_data['group_id'];

                            if($previews_useraccount){
                                $copy_data = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, picture, schoolyear_id, semester_id) SELECT serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, $new_group_id as group_id, user_status, qrimage, picture, $schoolyear_id as schoolyear_id, $semester_id as semester_id FROM useraccount WHERE user_account_id = $previews_useraccount";

                                $copy_data_result = $con->query($copy_data);

                                if($copy_data_result){
                                    $studentQuery = "INSERT INTO enrolledstudent (student_number, student_name, student_email, registration_status, schoolyear_id, semester_id) VALUES ($student_number, '$student_name', '$student_email', 'Registered', $schoolyear_id, $semester_id)";
                                    $result = mysqli_query($con, $studentQuery); // execute query
                                    if (!$result) {
                                        $errorMessages[] = "Failed to insert data: " . mysqli_error($con);
                                    }
                                }else{
                                    $studentQuery = "INSERT INTO enrolledstudent (student_number, student_name, student_email, schoolyear_id, semester_id) VALUES ($student_number, '$student_name', '$student_email', $schoolyear_id, $semester_id)";
                                    $result = mysqli_query($con, $studentQuery); // execute query
                                    if (!$result) {
                                        $errorMessages[] = "Failed to insert data: " . mysqli_error($con);
                                    }
                                }
                            }
                        }else{
                            $studentQuery = "INSERT INTO enrolledstudent (student_number, student_name, student_email, schoolyear_id, semester_id) VALUES ($student_number, '$student_name', '$student_email', $schoolyear_id, $semester_id)";
                            $result = mysqli_query($con, $studentQuery); // execute query
                            if (!$result) {
                                $errorMessages[] = "Failed to insert data: " . mysqli_error($con);
                            }
                        }
                    } else {
                        // Collect existing student numbers and names
                        $existingStudents[] = array('number' => $student_number, 'name' => $student_name);
                    }
                }
            }

            // THIS IS THE CREATING OF GROUP BASE TO THE NUMBER OF STUDENTS IN EACH COMPONENT
            $cwtsGroupQuery = "SELECT COUNT(*) AS CWTSGroup FROM grouptable WHERE component_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $cwstGroupResult = $con->query($cwtsGroupQuery);
            $cwstGroupData = $cwstGroupResult->fetch_assoc();
            $numberCwtsGroup = $cwstGroupData['CWTSGroup'];

            $cwtsStudentQuery = "SELECT COUNT(*) AS CWTSStudent FROM useraccount WHERE role_account_id = 2 AND component_name = 'CWTS' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $cwtsStudentResult = $con->query($cwtsStudentQuery);
            $cwtsStudentData = $cwtsStudentResult->fetch_assoc();
            $numberCwtsStudent = $cwtsStudentData['CWTSStudent'];

            $rotcGroupQuery = "SELECT COUNT(*) AS ROTCGroup FROM grouptable WHERE component_id = 1 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $rotcGroupResult = $con->query($rotcGroupQuery);
            $rotcGroupData = $rotcGroupResult->fetch_assoc();
            $numberRotcGroup = $rotcGroupData['ROTCGroup'];

            $rotcStudentQuery = "SELECT COUNT(*) AS ROTCStudent FROM useraccount WHERE role_account_id = 2 AND component_name = 'ROTC' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $rotcStudentResult = $con->query($rotcStudentQuery);
            $rotcStudentData = $rotcStudentResult->fetch_assoc();
            $numberRotcStudent = $rotcStudentData['ROTCStudent'];

            $estimetedCwtsGroup = ceil($numberCwtsStudent / 60);
            
            if($estimetedCwtsGroup < 15){
                $estimetedCwtsGroup = 16;
            }else{
                $estimetedCwtsGroup = ceil($numberCwtsStudent / 60);
            }

            $estimetedRotcGroup = ceil($numberRotcStudent / 37);
            if($estimetedRotcGroup < 1){
                $estimetedRotcGroup = 2;
            }else{
                $estimetedRotcGroup = ceil($numberRotcStudent / 37);
            }

            if($numberCwtsGroup <= $estimetedCwtsGroup){
                for ($i = $numberCwtsGroup; $i < $estimetedCwtsGroup; $i++) {
                    $group_name = $cwts_groups[$i];
                    $insert_query_cwts = "INSERT INTO grouptable (group_name, incharge_person, component_id, number_student, schoolyear_id, semester_id, date_created, date_updated) VALUES ('$group_name', NULL, 2, 60, $schoolyear_id, $semester_id, NOW(), NOW())";
                    $insert_result_cwts = $con->query($insert_query_cwts);
                }
            }

            if($numberRotcGroup <= $estimetedRotcGroup){
                for ($i = $numberRotcGroup; $i < $estimetedRotcGroup; $i++) {
                    $group_name = $rotc_groups[$i];
                    $insert_query_rotc = "INSERT INTO grouptable (group_name, incharge_person, component_id, number_student, schoolyear_id, semester_id, date_created, date_updated) VALUES ('$group_name', NULL, 1, 37, $schoolyear_id, $semester_id, NOW(), NOW())";
                    $insert_result_rotc = $con->query($insert_query_rotc);
                }
            }

            $studentNoGroupCWTSQuery = "SELECT ua.*
            FROM useraccount ua
            JOIN (
                SELECT course
                FROM useraccount
                WHERE role_account_id = 2
                    AND component_name = 'CWTS'
                    AND schoolyear_id = $schoolyear_id
                    AND semester_id = $semester_id
                GROUP BY course
                HAVING COUNT(*) >= 2
            ) filtered_courses
            ON ua.course = filtered_courses.course
            WHERE ua.role_account_id = 2
                AND ua.component_name = 'CWTS'
                AND ua.schoolyear_id = $schoolyear_id
                AND ua.semester_id = $semester_id
                AND ua.group_id IS NULL
            ORDER BY ua.course";
            // $studentNoGroupCWTSQuery = "SELECT * FROM useraccount WHERE role_account_id = 2 AND component_name = 'CWTS' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id ORDER BY course";
            $studentNoGroupCWTSResult = $con->query($studentNoGroupCWTSQuery);

            if($studentNoGroupCWTSResult->num_rows > 0){
                while ($row = $studentNoGroupCWTSResult->fetch_assoc()) {
                    $studentCourse = $row['course'];
                    $studentGender = $row['gender'];
                    $userAccountId = $row['user_account_id'];

                    $placingStudentCWTSGroup = "SELECT * FROM grouptable WHERE component_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id ORDER BY group_name";
                    $placingStudentCWTSGroupResult = $con->query($placingStudentCWTSGroup);

                    if($placingStudentCWTSGroupResult->num_rows > 0){
                        while($groupCWTSRow = $placingStudentCWTSGroupResult->fetch_assoc()){
                            $groupCWTSId = $groupCWTSRow['group_id'];
                            $groupNumberOfStudent = $groupCWTSRow['number_student'];

                            $checkGroupIdQuery = "SELECT course, gender, COUNT(*) AS numberOfStudents FROM useraccount WHERE group_id = $groupCWTSId AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                            $checkGroupIdResult = $con->query($checkGroupIdQuery);
                            $checkGroupIdData = $checkGroupIdResult->fetch_assoc();
                            $groupCourse = $checkGroupIdData['course'];
                            $numberOfStudents = $checkGroupIdData['numberOfStudents'];

                            if($numberOfStudents > 0){
                                // if($groupNumberOfStudent > $numberOfStudents && $groupCourse == $studentCourse){
                                if($groupNumberOfStudent > $numberOfStudents){
                                    $updateGroupCWTSId = "UPDATE useraccount SET group_id = $groupCWTSId WHERE user_account_id = $userAccountId";
                                    $updateGroupIdResult = $con->query($updateGroupCWTSId);
                                    break;
                                }
                            }else{
                                $updateGroupCWTSId = "UPDATE useraccount SET group_id = $groupCWTSId WHERE user_account_id = $userAccountId";
                                $updateGroupIdResult = $con->query($updateGroupCWTSId);
                                break;
                            }
                        }
                    }
                }
            }
            $leftStudentCwtsGroup = "SELECT * FROM useraccount WHERE group_id IS NULL AND role_account_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND component_name = 'CWTS'";
            $leftStudentCwtsGroupResult = $con->query($leftStudentCwtsGroup);
            if($leftStudentCwtsGroupResult->num_rows > 0){
                while($groupCWTSRow = $leftStudentCwtsGroupResult->fetch_assoc()){
                    $userAccountId = $groupCWTSRow['user_account_id'];

                    $placingStudentCWTSGroup = "SELECT * FROM grouptable WHERE component_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id ORDER BY group_name";
                    $placingStudentCWTSGroupResult = $con->query($placingStudentCWTSGroup);

                    if($placingStudentCWTSGroupResult->num_rows > 0){
                        while($groupCWTSRow = $placingStudentCWTSGroupResult->fetch_assoc()){
                            $groupCWTSId = $groupCWTSRow['group_id'];
                            $groupNumberOfStudent = $groupCWTSRow['number_student'];

                            $checkGroupIdQuery = "SELECT course, gender, COUNT(*) AS numberOfStudents FROM useraccount WHERE group_id = $groupCWTSId AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                            $checkGroupIdResult = $con->query($checkGroupIdQuery);
                            $checkGroupIdData = $checkGroupIdResult->fetch_assoc();
                            $groupCourse = $checkGroupIdData['course'];
                            $numberOfStudents = $checkGroupIdData['numberOfStudents'];

                            if($numberOfStudents > 0){
                                // if($groupNumberOfStudent > $numberOfStudents && $groupCourse == $studentCourse){
                                if($groupNumberOfStudent > $numberOfStudents){
                                    $updateGroupCWTSId = "UPDATE useraccount SET group_id = $groupCWTSId WHERE user_account_id = $userAccountId";
                                    $updateGroupIdResult = $con->query($updateGroupCWTSId);
                                    break;
                                }
                            }else{
                                $updateGroupCWTSId = "UPDATE useraccount SET group_id = $groupCWTSId WHERE user_account_id = $userAccountId";
                                $updateGroupIdResult = $con->query($updateGroupCWTSId);
                                break;
                            }
                        }
                    }
                }
            }

            $studentNoGroupROTCQuery = "SELECT * FROM useraccount WHERE role_account_id = 2 AND component_name = 'ROTC' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND group_id IS NULL ORDER BY gender, student_section";
            $studentNoGroupROTCResult = $con->query($studentNoGroupROTCQuery);

            if($studentNoGroupROTCResult->num_rows > 0){
                while ($row = $studentNoGroupROTCResult->fetch_assoc()) {
                    $studentCourse = $row['course'];
                    $studentGender = $row['gender'];
                    $userAccountId = $row['user_account_id'];

                    $placingStudentROTCGroup = "SELECT * FROM grouptable WHERE component_id = 1 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id ORDER BY group_name";
                    $placingStudentROTCGroupResult = $con->query($placingStudentROTCGroup);
                    
                    if($placingStudentROTCGroupResult->num_rows > 0){
                        while($groupRow = $placingStudentROTCGroupResult->fetch_assoc()){
                            $groupId = $groupRow['group_id'];
                            $groupNumberOfStudent = $groupRow['number_student'];
        
                            $checkGroupIdQuery = "SELECT course, gender, COUNT(*) AS numberOfStudents FROM useraccount WHERE group_id = $groupId AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                            $checkGroupIdResult = $con->query($checkGroupIdQuery);
                            $checkGroupIdData = $checkGroupIdResult->fetch_assoc();
                            $groupGender = $checkGroupIdData['gender'];
                            $numberOfStudents = $checkGroupIdData['numberOfStudents'];

                            if($numberOfStudents > 0){
                                if($groupNumberOfStudent > $numberOfStudents && $groupGender == $studentGender){
                                    $updateGroupId = "UPDATE useraccount SET group_id = $groupId WHERE user_account_id = $userAccountId";
                                    $updateGroupIdResult = $con->query($updateGroupId);
                                }
                            }else{
                                $updateGroupId = "UPDATE useraccount SET group_id = $groupId WHERE user_account_id = $userAccountId";
                                $updateGroupIdResult = $con->query($updateGroupId);
                            }
                        }
                    }
                }
            }
            
            $leftStudentRotcGroup = "SELECT * FROM useraccount WHERE group_id IS NULL AND role_account_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND component_name = 'ROTC'";
            $leftStudentRotcGroupResult = $con->query($leftStudentRotcGroup);
            if($leftStudentRotcGroupResult->num_rows > 0){
                while($groupROTCRows = $leftStudentRotcGroupResult->fetch_assoc()){
                    $userAccountId = $groupROTCRows['user_account_id'];

                    $placingStudentROTCGroup = "SELECT * FROM grouptable WHERE component_id = 1 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id ORDER BY group_name";
                    $placingStudentROTCGroupResult = $con->query($placingStudentROTCGroup);

                    if($placingStudentROTCGroupResult->num_rows > 0){
                        while($groupRotcRow = $placingStudentROTCGroupResult->fetch_assoc()){
                            $groupRotcId = $groupRotcRow['group_id'];
                            $groupNumberOfStudent = $groupRotcRow['number_student'];

                            $checkGroupIdQuery = "SELECT course, gender, COUNT(*) AS numberOfStudents FROM useraccount WHERE group_id = $groupRotcId AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                            $checkGroupIdResult = $con->query($checkGroupIdQuery);
                            $checkGroupIdData = $checkGroupIdResult->fetch_assoc();
                            $groupCourse = $checkGroupIdData['course'];
                            $numberOfStudents = $checkGroupIdData['numberOfStudents'];

                            if($numberOfStudents > 0){
                                // if($groupNumberOfStudent > $numberOfStudents && $groupCourse == $studentCourse){
                                if($groupNumberOfStudent > $numberOfStudents){
                                    $updateGroupRotcId = "UPDATE useraccount SET group_id = $groupRotcId WHERE user_account_id = $userAccountId";
                                    $updateGroupIdResult = $con->query($updateGroupRotcId);
                                    break;
                                }
                            }else{
                                $updateGroupRotcId = "UPDATE useraccount SET group_id = $groupRotcId WHERE user_account_id = $userAccountId";
                                $updateGroupIdResult = $con->query($updateGroupRotcId);
                                break;
                            }
                        }
                    }
                }
            }

            $successCount = count($data) - count($existingStudents);
            if ($successCount > 0) {
                $successFlag = true; // Set the flag to true if there were successful insertions
                $successMessage = "Data imported successfully. Total records imported: $successCount";
                $successtrigger = true;
            }

            // Display SweetAlert2 messages
            if (!empty($existingStudents)) {
                $errorMessage = "The following student records already exist:<br><br>";
                $errorMessage .= "<table>";
                $errorMessage .= "<tr><th>Student Number</th><th>Student Name</th></tr>";
                foreach ($existingStudents as $existingStudent) {
                    $errorMessage .= "<tr><td>" . $existingStudent['number'] . "</td><td>" . $existingStudent['name'] . "</td></tr>";
                }
                $errorMessage .= "</table>";

                echo "<script>
                    Swal.fire({
                        title: 'Student Record Already Exist',
                        icon: 'warning',
                        html: '$errorMessage',
                    }).then(function () {
                        window.location.href = 'import.php';
                    });
                </script>";
            }

            if (!empty($errorMessages)) {
                $errorMessage = implode("\\n", $errorMessages);
                echo "<script>
                    Swal.fire({
                        title: 'Error',
                        icon: 'error',
                        text: '$errorMessage',
                    }).then(function () {
                        window.location.href = 'import.php';
                    });
                </script>";
            } elseif ($successFlag && empty($schoolyear_checking_data)) {
                $schoolyear_save_query = "INSERT INTO schoolyeartable (schoolyear_id, schoolyear_start, schoolyear_end, schoolyear, semester_id) VALUES ($schoolyear_id, '$schoolyear_start', '$schoolyear_end', '$schoolyear', $semester_id)";
                $schoolyear_save_result = $con->query($schoolyear_save_query);
                
            }
            if ($successtrigger) {
                echo "<script>
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: '$successMessage',
                    }).then(function () {
                        window.location.href = 'import.php';
                    });
                </script>";
            } 
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error',
                    icon: 'error',
                    text: 'Invalid file format. Only .xls, .csv, and .xlsx files are allowed.',
                }).then(function () {
                    window.location.href = 'import.php';
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
                window.location.href = 'import.php';
            });
        </script>";
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])){
  $studentName = $_POST['full_name'];
  $studentNumber = $_POST['student_number'];
  $student_email = $_POST['student_email'];

  $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
  $schoolyear_result = $con->query($schoolyear_query);
  $schoolyear_data = $schoolyear_result->fetch_assoc();
  $schoolyear_id = $schoolyear_data['schoolyear_id'];
  $semester_id = $schoolyear_data['semester_id'];

  $addstudent_record = "INSERT INTO enrolledstudent (student_number, student_name, student_email, schoolyear_id, semester_id) VALUES ('$studentNumber', '$studentName', '$student_email', $schoolyear_id, $semester_id)";
  if($con->query($addstudent_record)){
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Student record has been successfully added.'
            }).then(() => {
                window.location.href = 'import.php';
            });
            </script>";
  }else{
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error adding student. Please try again.'
            }).then(() => {
                window.location.href = 'import.php';
            });
            </script>";
  }
}
?>
<style>
    .custom-popover {
  --bs-popover-max-width: 200px;
  --bs-popover-border-color: var(--bd-violet-bg);
  --bs-popover-header-bg: red;
  --bs-popover-header-color: blue;
  --bs-popover-body-padding-x: 1rem;
  --bs-popover-body-padding-y: 0.5rem;
}
</style>
      <div class="home-main-container">
        <div class="studentList-container">
            <div class="page-title">
                <div class="titleContainer">
                    <span class="group_id">Import Student Data</span>
                    <label class="in-charge-label">Enrolled in NSTP subject</label>
                </div>
                <form method="get" enctype="multipart/form-data" action="import.php">
                    <div class="search-container">
                        <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>
                        <button class="btn btn-primary" type="submit"><i class='bx bx-search'></i></button>
                    </div>
                </form>
            </div>
            <div class="buttonsContainer">
                <div class="buttonHolder">
                    <!-- <button type="button" class="btn btn-primary" data-toggle="tooltip" data-bs-html="true" data-bs-toggle="modal" title="Data Needed:<br> Time Stamp, Email Address, Surname, First Name, Middle Name, T-shirt Size, Course, Home Address, Year Level, Contact Number, Birth day, Age, Place of birth, Religion, Status, Height, Weight, Complexion, Blood Type, Gender, Spouse Name, Spouse Contact Number, Occupation, Person to be Contact Name, Relation, Contact Number" data-bs-target="#uploadmodal">
                        <i class='bx bx-cloud-upload' ></i>Import Google Form Data
                    </button> -->
                    <button type="button" class="btn btn-primary" data-bs-placement="bottom" data-toggle="tooltip" data-bs-toggle="modal" data-bs-html="true" title="Data Needed:<br> Student Number, Student Name, Student Email" data-bs-target="#importMisDataButton">
                        <i class='bx bx-cloud-upload' ></i>Import MIS Data
                    </button>
                </div>
            </div>
            <!-- START OF UPLOAD MODAL -->
            <div class="modal fade" id="uploadmodal" tabindex="1" aria-labelledby="uploadmodalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 style="text-align: center; padding: 5px 0;">Upload Enrolled Student</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="component">Component:</label>
                                    <select name="chooseComponent" class="form-control" id="chooseComponent">
                                        <option value="" selected hidden>Choose Component</option>
                                        <option value="ROTC">ROTC</option>
                                        <option value="CWTS">CWTS</option>
                                    </select>
                                </div>
                                <?php
                                    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                                    $schoolyear_result = $con->query($schoolyear_query);
                                    if ($schoolyear_result && $schoolyear_result->num_rows > 0) {
                                        $schoolyear = $schoolyear_result->fetch_assoc();
                                        $schoolyear_start = $schoolyear["schoolyear_start"];
                                        $schoolyear_end = $schoolyear["schoolyear_end"];
                                        $current_schoolyear = $schoolyear_start . '-' . $schoolyear_end;
                                    } else {
                                        $current_year = date("Y");
                                        $schoolyear_start = $current_year;
                                        $schoolyear_end = intval($schoolyear_start) + 1;
                                        $current_schoolyear = $schoolyear_start . '-' . $schoolyear_end;
                                    }

                                    // Fetch semester options from semestertable
                                    $semester_query = "SELECT semester_id, semester FROM semestertable";
                                    $semester_result = $con->query($semester_query);
                                    $semester_options = '';
                                    if ($semester_result && $semester_result->num_rows > 0) {
                                        while ($semester_row = $semester_result->fetch_assoc()) {
                                            $semester_id = $semester_row["semester_id"];
                                            $semester = $semester_row["semester"];
                                            $semester_options .= '<option value="' . $semester_id . '">' . $semester . '</option>';
                                        }
                                    }

                                    // Construct school year options
                                    $schoolyear_options = '';
                                    $start_year = $schoolyear_start + 1;
                                    $end_year = $start_year + 1;
                                    // $schoolyear_options .= '<option value="' . ($schoolyear_start - 1) . '" selected>' . ($schoolyear_start - 1) . '-' . $schoolyear_start . '</option>';
                                    $schoolyear_options .= '<option value="' . $schoolyear_start . '">' . $schoolyear_start . '-' . ($schoolyear_start + 1) . '</option>';
                                    $schoolyear_options .= '<option value="' . $start_year . '">' . $start_year . '-' . $end_year . '</option>';

                                    echo '
                                        <input type="hidden" name="schoolyear_start" id="schoolyear_start" value="' . $schoolyear_start . '">
                                        <input type="hidden" name="schoolyear_end" id="schoolyear_end" value="' . $schoolyear_end . '">
                                        <div class="form-group">
                                            <label for="full_name">School Year:</label>
                                            <select name="schoolyear" class="form-control" id="schoolyear" onchange="updateSchoolYears(this.value)">
                                                ' . $schoolyear_options . '
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="full_name">Semester:</label>
                                            <select name="semester_id" class="form-control" id="semester_id">
                                                ' . $semester_options . '
                                            </select>
                                        </div>
                                        
                                        <script>
                                            function updateSchoolYears(selectedYear) {
                                                const startInput = document.getElementById("schoolyear_start");
                                                const endInput = document.getElementById("schoolyear_end");
                                                const [start] = selectedYear.split("-");
                                                const end = parseInt(start) + 1;

                                                startInput.value = start;
                                                endInput.value = end;
                                            }
                                        </script>';
                                ?>
                                <div class="form-group">
                                    <label for="student_number">Upload File:</label>
                                    <input type="file" name="import_file" accept=".xls, .csv, .xlsx" class="form-control" style="width: 35vh; margin: 0 10px 0 0" onchange="enableImportButton(this)" required/>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="save_excel_data" id="import_button" disabled>Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="importMisDataButton" tabindex="1" aria-labelledby="importMisDataButtonLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 style="text-align: center; padding: 5px 0;">Upload Enrolled Student</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <?php
                                    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                                    $schoolyear_result = $con->query($schoolyear_query);
                                    if ($schoolyear_result && $schoolyear_result->num_rows > 0) {
                                        $schoolyear = $schoolyear_result->fetch_assoc();
                                        $schoolyear_start = $schoolyear["schoolyear_start"];
                                        $schoolyear_end = $schoolyear["schoolyear_end"];
                                        $current_schoolyear = $schoolyear_start . '-' . $schoolyear_end;
                                    } else {
                                        $current_year = date("Y");
                                        $schoolyear_start = $current_year;
                                        $schoolyear_end = intval($schoolyear_start) + 1;
                                        $current_schoolyear = $schoolyear_start . '-' . $schoolyear_end;
                                    }

                                    // Fetch semester options from semestertable
                                    $semester_query = "SELECT semester_id, semester FROM semestertable";
                                    $semester_result = $con->query($semester_query);
                                    $semester_options = '';
                                    if ($semester_result && $semester_result->num_rows > 0) {
                                        while ($semester_row = $semester_result->fetch_assoc()) {
                                            $semester_id = $semester_row["semester_id"];
                                            $semester = $semester_row["semester"];
                                            $semester_options .= '<option value="' . $semester_id . '">' . $semester . '</option>';
                                        }
                                    }

                                    // Construct school year options
                                    $schoolyear_options = '';
                                    $start_year = $schoolyear_start + 1;
                                    $end_year = $start_year + 1;
                                    // $schoolyear_options .= '<option value="' . ($schoolyear_start - 1) . '" selected>' . ($schoolyear_start - 1) . '-' . $schoolyear_start . '</option>';
                                    $schoolyear_options .= '<option value="' . $schoolyear_start . '">' . $schoolyear_start . '-' . ($schoolyear_start + 1) . '</option>';
                                    $schoolyear_options .= '<option value="' . $start_year . '">' . $start_year . '-' . $end_year . '</option>';

                                    echo '
                                        <input type="hidden" name="schoolyear_start" id="schoolyear_start" value="' . $schoolyear_start . '">
                                        <input type="hidden" name="schoolyear_end" id="schoolyear_end" value="' . $schoolyear_end . '">
                                        <div class="form-group">
                                            <label for="full_name">School Year:</label>
                                            <select name="schoolyear" class="form-control" id="schoolyear" onchange="updateSchoolYears(this.value)">
                                                ' . $schoolyear_options . '
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="full_name">Semester:</label>
                                            <select name="semester_id" class="form-control" id="semester_id">
                                                ' . $semester_options . '
                                            </select>
                                        </div>
                                        
                                        <script>
                                            function updateSchoolYears(selectedYear) {
                                                const startInput = document.getElementById("schoolyear_start");
                                                const endInput = document.getElementById("schoolyear_end");
                                                const [start] = selectedYear.split("-");
                                                const end = parseInt(start) + 1;

                                                startInput.value = start;
                                                endInput.value = end;
                                            }
                                        </script>';
                                ?>
                                <div class="form-group">
                                    <label for="student_number">Upload File:</label>
                                    <input type="file" name="import_file" accept=".xls, .csv, .xlsx" class="form-control" style="width: 35vh; margin: 0 10px 0 0" onchange="enableImportButton(this)" required/>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="importMISData" id="importMISDataImport_button" disabled>Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- END OF UPLOAD MODAL -->
                
            <div class="modal fade" id="addstudentmodal" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h2 style="text-align: center; padding: 5px 0;">Add Student Record</h2>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form method="post" enctype="multipart/form-data">
                          <div class="modal-body">
                              <div class="form-group">
                                  <label for="full_name">Full Name:</label>
                                  <input type="text" class="form-control" id="full_name" name="full_name" pattern='[A-Za-z.\s]+' required>
                              </div>
                              <div class="form-group">
                                  <label for="student_number">Student Number:</label>
                                  <input type="text" class="form-control" id="student_number" name="student_number" required>
                                  <small id="studentNumberError" style="color: red;"></small>
                              </div>
                              <div class="form-group">
                                  <label for="student_email">Student Email:</label>
                                  <input type="text" class="form-control" id="student_email" name="student_email" required>
                                  <small id="studentEmailError" style="color: red;"></small>
                              </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary" name="add_student" id="add_student_button">Add Student</button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
            
                <?php
                // THIS IS THE ALGORITHM FOR AUTOMATIC CREATING GROUP
                $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                $schoolyear_result = $con->query($schoolyear_query);

                if($schoolyear_result->num_rows > 0){
                    $schoolyear_data = $schoolyear_result->fetch_assoc();
                    $schoolyear_id = $schoolyear_data['schoolyear_id'];
                    $semester_id = $schoolyear_data['semester_id'];
    
                    $group_check_query = "SELECT COUNT(*) as group_count FROM grouptable WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                    $group_check_result = $con->query($group_check_query);
                    if($group_check_result){
    
                        $group_check_data = $group_check_result->fetch_assoc();
                        $group_count = $group_check_data['group_count'];

                        $currentCountCWTS = "SELECT COUNT(*) AS numberCWTSGroup FROM grouptable WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND component_id = 2";
                        $currentCountCWTSResult = $con->query($currentCountCWTS);
                        $currentCountCWTSData = $currentCountCWTSResult->fetch_assoc();
                        $numberCWTSGroup = $currentCountCWTSData['numberCWTSGroup'];

                        $currentCountROTC = "SELECT COUNT(*) AS numberROTCGroup FROM grouptable WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND component_id = 1";
                        $currentCountROTCResult = $con->query($currentCountROTC);
                        $currentCountROTCData = $currentCountROTCResult->fetch_assoc();
                        $numberROTCGroup = $currentCountROTCData['numberROTCGroup'];

                        $enrollment_algo_query = "SELECT COUNT(*) as count FROM enrolledstudent";
                        $enrollment_algo_result = $con->query($enrollment_algo_query);
    
                        if ($enrollment_algo_result) {
                            $row = $enrollment_algo_result->fetch_assoc();
                            $count = $row['count'];
    
                            $cwts_percentage = 0.6 * $count;
                            $rotc_percentage = 0.4 * $count;
    
                            $cwts_number_of_student = 60; // 50 is the number of student each group
                            $rotc_number_of_student = 37; // 37 is the number of student each group
    
                            $result_group_cwts = ceil($cwts_percentage / $cwts_number_of_student); 
                            // the result of this will be the number of group
    
                            $result_group_rotc = ceil($rotc_percentage / $rotc_number_of_student); 
                            // the result of this will be the number of group
    
                            // Print the count, CWTS percentage, ROTC percentage, and the result_group for CWTS and ROTC using console.log
                            echo "<script>console.log('Count: $count');</script>";
                            echo "<script>console.log('CWTS: $cwts_percentage');</script>";
                            echo "<script>console.log('ROTC: $rotc_percentage');</script>";
                            echo "<script>console.log('Result Group for CWTS: ($result_group_cwts group)');</script>";
                            echo "<script>console.log('Result Group for ROTC: ($result_group_rotc group)');</script>";
    
                            if ($group_count == 0) {
                                // Create CWTS groups
                                for ($i = 0; $i < $result_group_cwts; $i++) {
                                    $group_name = $cwts_groups[$i % count($cwts_groups)];
                                    $insert_query_cwts = "INSERT INTO grouptable (group_name, incharge_person, component_id, number_student, schoolyear_id, semester_id, date_created, date_updated)
                                                          VALUES ('$group_name', NULL, 2, $cwts_number_of_student, $schoolyear_id, $semester_id, NOW(), NOW())";
                                    if (!$con->query($insert_query_cwts)) {
                                        echo "Error inserting CWTS group: " . $con->error;
                                    }
                                }
                                
                                // Create ROTC groups
                                for ($i = 0; $i < $result_group_rotc; $i++) {
                                    $group_name = $rotc_groups[$i % count($rotc_groups)];
                                    $insert_query_rotc = "INSERT INTO grouptable (group_name, incharge_person, component_id, number_student, schoolyear_id, semester_id, date_created, date_updated)
                                                          VALUES ('$group_name', NULL, 1, $rotc_number_of_student, $schoolyear_id, $semester_id, NOW(), NOW())";
                                    if (!$con->query($insert_query_rotc)) {
                                        echo "Error inserting ROTC group: " . $con->error;
                                    }
                                }
                        
                                echo "<script>console.log('Groups have been created successfully!');</script>";
                            }elseif($numberCWTSGroup < $result_group_cwts || $numberROTCGroup < $result_group_rotc){
                                // Create CWTS groups
                                for ($i = $numberCWTSGroup; $i < $result_group_cwts; $i++) {
                                    $group_name = $cwts_groups[$i % count($cwts_groups)];
                                    $insert_query_cwts = "INSERT INTO grouptable (group_name, incharge_person, component_id, number_student, schoolyear_id, semester_id, date_created, date_updated)
                                                          VALUES ('$group_name', NULL, 2, $cwts_number_of_student, $schoolyear_id, $semester_id, NOW(), NOW())";
                                    if (!$con->query($insert_query_cwts)) {
                                        echo "Error inserting CWTS group: " . $con->error;
                                    }
                                }
                                
                                // Create ROTC groups
                                for ($i = $numberROTCGroup; $i < $result_group_rotc; $i++) {
                                    $group_name = $rotc_groups[$i % count($rotc_groups)];
                                    $insert_query_rotc = "INSERT INTO grouptable (group_name, incharge_person, component_id, number_student, schoolyear_id, semester_id, date_created, date_updated)
                                                          VALUES ('$group_name', NULL, 1, $rotc_number_of_student, $schoolyear_id, $semester_id, NOW(), NOW())";
                                    if (!$con->query($insert_query_rotc)) {
                                        echo "Error inserting ROTC group: " . $con->error;
                                    }
                                }
                            }else{
                                echo "<script>console.log('Already Have Group');</script>";
                            }
    
                        } else {
                            echo "Error executing the query: " . $con->error;
                        }
                    }
                }
                
                // THIS IS THE END ALGORITHM FOR AUTOMATIC CREATING GROUP

                // $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                // $schoolyear_result = $con->query($schoolyear_query);
                // $schoolyear_data = $schoolyear_result->fetch_assoc();
                // $getschoolyear = $schoolyear_data['schoolyear_id'];
                // $getsemester = $schoolyear_data['semester_id'];
                // echo"<script>console.log($getschoolyear);</script>";
                // echo"<script>console.log($getsemester);</script>";

                $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                $schoolyear_result = $con->query($schoolyear_query);
                if($schoolyear_result->num_rows > 0){
                    $schoolyear_data = $schoolyear_result->fetch_assoc();
                    $schoolyear_id = $schoolyear_data['schoolyear_id'];
                    $semester_id = $schoolyear_data['semester_id'];

                    // Number of records to display per page
                    $recordsPerPage = 10;

                    // Determine the current page number
                    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                        $currentPage = intval($_GET['page']);
                    } else {
                        $currentPage = 1;
                    }

                    // Calculate the OFFSET for the query based on the current page number
                    $offset = ($currentPage - 1) * $recordsPerPage;

                    $query = "SELECT * FROM enrolledstudent";

                    // Add the filtering for schoolyear_id and semester_id within the same WHERE clause
                    $query .= " WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id";

                    if (isset($_GET['search'])) {
                        $search = mysqli_real_escape_string($con, $_GET['search']);
                        // $query .= " WHERE student_number LIKE '%$search%' OR student_name LIKE '%$search%' OR registration_status LIKE '%$search%'";
                        $query .= " AND (student_number LIKE '%$search%' OR student_name LIKE '%$search%' OR registration_status LIKE '%$search%')";
                    }
                    // $query .= " ORDER BY enrolledstudent_id DESC";
                    $query .= " ORDER BY registration_status DESC LIMIT $recordsPerPage OFFSET $offset";
                    $result = mysqli_query($con, $query);

                    if (mysqli_num_rows($result) > 0) {
                        
                        echo "<div class='tableContainer'>";
                        echo "<table class='table table-sm caption-top'>";
                        echo "<caption>List of Student</caption>";
                        echo "<thead class=\"custom-thead\"><tr><th>#</th><th>Student Number</th><th>Student Name</th><th>Register Status</th><th class='thAction'>Actions</th></tr></thead>";
                        echo "<tbody id='file-table-body'>";
                        $currentRow = $offset + 1;

                        // $counter = 1; // Initialize the counter variable

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td data-label='#'>" . $currentRow . "</td>"; // Display the counter value
                            echo "<td data-label='Student Number'>" . $row['student_number'] . "</td>";
                            echo "<td data-label='Student Name'>" . $row['student_name'] . "</td>";
                            echo "<td data-label='Register Status'>" . $row['registration_status'] . "</td>";
                            echo "<td data-label='Actions'>
                                    <form method='post' enctype='multipart/form-data'>
                                        <input type='hidden' name='student_id' value='" . $row['enrolledstudent_id'] . "'>
                                        <div class=\"groupButton\">
                                        <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#editModal" . $row['enrolledstudent_id'] . "'>
                                            <i class='bx bx-wrench'></i>Update
                                        </button>
                                        </div>
                                    </form>
                                </td>";
                            echo "</tr>";

                            // Bootstrap Modal
                            echo "<div class='modal fade' id='editModal" . $row['enrolledstudent_id'] . "' tabindex='-1' role='dialog' aria-labelledby='editModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog' role='document'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='editModalLabel'>Update Student Information</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <form method='post' enctype='multipart/form-data'>
                                                    <input type='hidden' name='student_id' value='" . $row['enrolledstudent_id'] . "'>
                                                    <div class='form-group'>
                                                        <label for='studentNumber'>Student Number</label>
                                                        <input type='text' class='form-control' id='studentNumber' name='student_number' value='" . $row['student_number'] . "'>
                                                    </div>
                                                    <div class='form-group mb-3'>
                                                        <label for='studentName'>Student Name</label>
                                                        <input type='text' class='form-control' id='studentName' name='student_name' value='" . $row['student_name'] . "'>
                                                    </div>
                                                    <div class='modal-footer'>
                                                    <button type='submit' name='save_changes' class='btn btn-primary'>Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";

                            // $counter++; // Increment the counter
                            $currentRow++;
                        }

                        echo "</tbody></table></div>";

                        // Pagination links using Bootstrap
                            echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                            <ul class='pagination justify-content-center'>";
                                $prevPage = $currentPage - 1;
                                echo"<li class='page-item " . ($currentPage == 1 ? 'disabled' : '') . "'>
                                        <a class='page-link' href='?page=$prevPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&laquo; Previous</a>
                                    </li>";

                                $totalRecordsQuery = "SELECT COUNT(*) as total FROM enrolledstudent WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id";

                                if (isset($_GET['search'])) {
                                    $search = mysqli_real_escape_string($con, $_GET['search']);
                                    // $query .= " WHERE student_number LIKE '%$search%' OR student_name LIKE '%$search%' OR registration_status LIKE '%$search%'";
                                    $totalRecordsQuery .= " AND (student_number LIKE '%$search%' OR student_name LIKE '%$search%' OR registration_status LIKE '%$search%')";
                                }

                                $totalResult = mysqli_query($con, $totalRecordsQuery);
                                $totalRows = mysqli_fetch_assoc($totalResult)['total']; 

                                $totalPages = ceil($totalRows / $recordsPerPage);

                                for ($i = max(1, $currentPage - 2); $i <= min($currentPage + 2, $totalPages); $i++) {
                                echo "<li class='page-item " . ($i == $currentPage ? 'active' : '') . "'><a class='page-link' href='?page=$i" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>$i</a></li>";
                                }

                                $nextPage = $currentPage + 1;
                                echo "<li class='page-item " . ($currentPage == $totalPages ? 'disabled' : '') . "'>
                                        <a class='page-link' href='?page=$nextPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>Next &raquo;</a>
                                      </li>
                            </ul>
                        </nav>";
                    } else {
                        echo '<h2 style="text-align:center;">No Student Enrolled.</h2>';
                    }
                }else{
                    echo '<h2 style="text-align:center;">No School Year Yet.</h2>';
                }

                        // Code to save changes to the database
                if (isset($_POST['save_changes']) && $_SERVER['REQUEST_METHOD'] == "POST") {
                $studentId = $_POST['student_id'];
                $newStudentNumber = mysqli_real_escape_string($con, $_POST['student_number']);
                $newStudentName = mysqli_real_escape_string($con, $_POST['student_name']);

                // Check if the condition is satisfied
                if ($newStudentNumber != '' && $newStudentName != '') {
                    // Retrieve the old student number from enrolledstudent table
                    $getOldStudentNumberQuery = "SELECT student_number FROM enrolledstudent WHERE enrolledstudent_id = $studentId";
                    $oldStudentNumberResult = $con->query($getOldStudentNumberQuery);
                    $oldStudentNumberRow = $oldStudentNumberResult->fetch_assoc();
                    $oldStudentNumber = $oldStudentNumberRow['student_number'];

                    $check_query = "SELECT COUNT(*) FROM useraccount WHERE student_number = '$oldStudentNumber'";
                    $check_result = mysqli_query($con, $check_query);
                    $check_count = mysqli_fetch_row($check_result)[0];

                    // Check if the student has already registered
                    if ($check_count > 0) {
                        $importdata_update = "UPDATE enrolledstudent SET student_number = '$newStudentNumber', student_name = '$newStudentName' WHERE enrolledstudent_id = $studentId";
                        $con->query($importdata_update);

                        // Update the student number in useraccount table
                        $student_list_query = "UPDATE useraccount SET student_number = '$newStudentNumber', full_name = '$newStudentName' WHERE student_number = '$oldStudentNumber'";
                        $con->query($student_list_query);
                    } else {
                        $importdata_update = "UPDATE enrolledstudent SET student_number = '$newStudentNumber', student_name = '$newStudentName' WHERE enrolledstudent_id = $studentId";
                        $con->query($importdata_update);
                    }
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Student record successfully updated.'
                        }).then(() => {
                            window.location.href = 'import.php';
                        });
                        </script>";
                } else {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please fill in all the required fields.'
                        });
                        </script>";
                }
                }
          ?>
        </div>
      </div>
  </section>
</div>
<script>
    const studentNumberInput = document.getElementById('student_number');
    const studentNumberError = document.getElementById('studentNumberError');

    const studentEmailInput = document.getElementById('student_email');
    const studentEmailError = document.getElementById('studentEmailError');

    const submitButton = document.querySelector('button[name="add_student"]');

    studentNumberInput.addEventListener('input', function() {
        const studentNumber = studentNumberInput.value.trim();
        const studentNumberPattern = /^\d{9}$/;
        if (!studentNumberPattern.test(studentNumber)) {
            studentNumberError.textContent = 'Student number should be 9 digits';
            submitButton.disabled = true; // Disable the submit button
        } else {
            studentNumberError.textContent = '';
            if (studentEmailError.textContent === '') {
                submitButton.disabled = false; // Enable the submit button if no errors
            }
        }
    });

    studentEmailInput.addEventListener('input', function() {
        const studentEmail = studentEmailInput.value.trim();
        const studentEmailPattern = /^[\w.%+-]+@cvsu\.edu\.ph$/i;
        if (!studentEmailPattern.test(studentEmail)) {
            studentEmailError.textContent = 'Student email should be in the format user@cvsu.edu.ph';
            submitButton.disabled = true; // Disable the submit button
        } else {
            studentEmailError.textContent = '';
            if (studentNumberError.textContent === '') {
                submitButton.disabled = false; // Enable the submit button if no errors
            }
        }
    });
</script>

<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
<script src="./js/search.js"></script>
<!-- FOR IMPORT BUTTON -->
<script>
function enableImportButton(input) {
    var importButton = document.getElementById("importMISDataImport_button");
    if (input.files && input.files[0]) {
        importButton.disabled = false;
    } else {
        importButton.disabled = true;
    }
}
</script>
<script>
    addSearchFunctionality('search', '.search-icon', 'import.php');
</script>
<script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl)
})

</script>
</body>
</html>


