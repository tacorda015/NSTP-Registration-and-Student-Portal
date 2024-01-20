<?php
include('connection.php');
$con = connection();
session_start();

if (isset($_SESSION['user_id'])) {
  session_destroy();
  header('Location: portal.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicons -->
    <link href="assets/img/Logo.png" rel="icon" />

    <!-- Link for Fontawosome -->
    <script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>

    <!-- For customized CSS -->
    <link rel="stylesheet" href="./assets/css/mainStyle.css">
    <link rel="stylesheet" href="./assets/css/main-style.css">

    <!-- For bootstrap -->
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css" />
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">

    <!-- Boxiocns CDN Link -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"rel="stylesheet" />
    <link rel="stylesheet" href="../boxicons-2.1.4/css/boxicons.min.css">

    <!-- For AJAX -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <title>Portal</title>
    
    <style>
      @media screen and (max-width: 767px){
        .dropdown-container{
          flex-wrap: wrap;
        }
      }
    </style>
  </head>
  <body>
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signin"])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $encodedPassword = base64_encode($password);

        $stmt = $con->prepare("SELECT * FROM useraccount WHERE BINARY email_address = ? ORDER BY user_account_id DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $foundUser = false;

            while ($row = $result->fetch_assoc()) {
                $storedPassword = base64_decode($row['password']);
                if ($password == $storedPassword) {
                    $foundUser = true;
                    break;
                }
            }

            if ($foundUser) {
                if ($row['user_status'] == 'disabled') {
                    echo '<script>
                        Swal.fire({
                            icon: "warning",
                            title: "Your account has been disabled",
                            text: "Please go to OSAS to confirm the status of your account.",
                            showConfirmButton: false,
                            timer: 5000
                        }).then(() => {
                            window.location.href = "portal.php";
                        });
                        </script>';
                    exit();
                }

                $_SESSION['user_id'] = $row['user_account_id'];

                $role = "SELECT useraccount.*, roleaccount.role_name FROM useraccount, roleaccount WHERE useraccount.role_account_id = roleaccount.role_account_id AND useraccount.email_address = '$email'";
                $roleresult = mysqli_query($con, $role);

                if ($roleresult) {
                    $rolename = mysqli_fetch_assoc($roleresult)['role_name'];
                    $_SESSION['user_role'] = $rolename;

                    $user_id = $_SESSION['user_id'];
                    $user_query = "SELECT * FROM useraccount WHERE user_account_id = '$user_id'";
                    $user_result = mysqli_query($con, $user_query);
                    $user_data = mysqli_fetch_assoc($user_result);
                    $_SESSION['user_data'] = $user_data;

                    if ($rolename == 'Admin') {
                        echo "<script>window.location.href = 'admin.php';</script>";
                        exit();
                    } elseif ($rolename == 'Student') {
                        echo "<script>window.location.href = 'student.php';</script>";
                        exit();
                    } elseif ($rolename == 'Teacher') {
                        echo "<script>window.location.href = 'teacher.php';</script>";
                        exit();
                    } else {
                    echo 'Invalid role name';
                    }
                } else {
                    echo 'Error retrieving role';
                }
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "warning",
                        title: "Incorrect password",
                        text: "",
                        showConfirmButton: false,
                        timer: 3000
                    })
                    </script>';
            }
        } else {
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Account not found",
                    text: "",
                    showConfirmButton: false,
                    timer: 3000
                }).then(function () {
                            window.location = "./portal.php";
                        });
                </script>';
        }

        $stmt->close();
        mysqli_close($con);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
        // Get the user input values
        $firstname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["firstname"])));
        $middlename = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["middlename"])));
        $surname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $_POST["surname"])));
        $email = $_POST["email"];
        $studentnumber = $_POST["studentnumber"];
        $course = $_POST["course"];
        $component_id = $_POST['component_id'];
        $yearlevel = $_POST['yearlevel'];
        $student_section = $_POST['student_section'];
        $sex = $_POST['sex'];
        $birthdayMonth = $_POST['birthday-month'];
        $birthdayDay = $_POST['birthday-day'];
        $birthdayYear = $_POST['birthday-year'];
        $contactnumber = $_POST['contactnumber'];
        $street_barangay = $_POST['street_barangay'];
        $city_municipality = $_POST['city_municipality'];
        $province = $_POST['province'];
        $password = $_POST['password'];

        $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
        $schoolyear_result = $con->query($schoolyear_query);
        $schoolyear_data = $schoolyear_result->fetch_assoc();
        $schoolyear_id = $schoolyear_data['schoolyear_id'];
        $semester_id = $schoolyear_data['semester_id'];

        $modifymiddlename = strtoupper(substr($middlename, 0, 1));

        $full_name = $firstname . ' ' . $modifymiddlename . '. ' . $surname;
        $address = $street_barangay . " " . $city_municipality . " ". $province;
        $birthday = $birthdayMonth . "/" . $birthdayDay . "/" . $birthdayYear;

        $enrolled_check_query = "SELECT COUNT(*) FROM enrolledstudent WHERE student_number = '$studentnumber' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
        $enrolled_check_result = mysqli_query($con, $enrolled_check_query);
        $enrolled_count = mysqli_fetch_row($enrolled_check_result)[0];

        // Check if the user is enrolled in the NSTP subject
        if ($enrolled_count == 0) {
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Not Enrolled",
                    text: "You are not yet enrolled in the NSTP subject.",
                    confirmButtonText: "OK"
                });
                </script>';
        } else {
            // Check if the student has already registered
            $registration_check_query = "SELECT COUNT(*) FROM useraccount WHERE student_number = '$studentnumber' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $registration_check_result = mysqli_query($con, $registration_check_query);
            $registration_count = mysqli_fetch_row($registration_check_result)[0];

            // Check if the student has already registered
            if ($registration_count > 0) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Already Registered",
                        text: "This student number has already been registered. Please go to OSAS to assist you with this matter.",
                        confirmButtonText: "OK"
                    });
                    </script>';                          
            } else {
                // Check if the email already exists
                $email_check_query = "SELECT COUNT(*) FROM useraccount WHERE email_address = '$email' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                $email_check_result = mysqli_query($con, $email_check_query);
                $email_count = mysqli_fetch_row($email_check_result)[0];

                // Check if the email already exists
                if ($email_count > 0) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Email Already Used",
                            text: "The email you entered is already used.",
                            confirmButtonText: "OK"
                        });
                        </script>';                            
                } else {
                    // FOR COMPONENT NAME
                    // Retrieve the selected component name from the database
                    $component_query = "SELECT component_name FROM componenttable WHERE component_id = $component_id";
                    $component_result = mysqli_query($con, $component_query);
                    $component_row = mysqli_fetch_assoc($component_result);
                    $component_name = $component_row['component_name'];

                    $qrcodes_dir = 'qrcodes/';
                    if (!is_dir($qrcodes_dir)) {
                      mkdir($qrcodes_dir);
                    }

                    $Spassword = base64_encode($password);
                    $secret_code = base64_encode($studentnumber);

                    require_once('phpqrcode/qrlib.php');
                    $qrdata = $secret_code;

                    // Remove the space from fulname
                    $qrfull_name = str_replace(['/', '\\', ' '], '', $full_name);
                    $qrfile = "qrcodes/$qrfull_name.png";
                    QRcode::png($qrdata, $qrfile);

                    $save_student_query = "UPDATE enrolledstudent SET registration_status = 'Registered' WHERE student_number = ?";
                    $save_student_stmt = $con->prepare($save_student_query);
                    $save_student_stmt->bind_param("s", $studentnumber);
                    $save_student_result = $save_student_stmt->execute();

                    // START HERE
                    if ($component_name == 'CWTS') {
                        $cwts_group_query = "SELECT group_id, group_name, number_student FROM grouptable WHERE component_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                        $cwts_group_results = mysqli_query($con, $cwts_group_query);
                        $cwts_group_count = mysqli_num_rows($cwts_group_results);
                        $counter = 0;

                        // Fetch all the groups for CWTS and store group_id and group_name in the array
                        while ($cwts_group_row = mysqli_fetch_assoc($cwts_group_results)) {
                            $list_group_id = $cwts_group_row['group_id'];
                            $list_group_name = $cwts_group_row['group_name'];
                            $list_number_student = $cwts_group_row['number_student'];

                            $user_account_query = "SELECT * FROM useraccount WHERE group_id = $list_group_id AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND role_account_id = 2 LIMIT 1";
                            $user_account_result = mysqli_query($con, $user_account_query);
                            $user_account_data = mysqli_fetch_assoc($user_account_result);
                            $check_course = $user_account_data['course'];

                            // THIS IS CHECK IF THE GROUP IN THE QUERY HAVE STUDENT
                            if(!empty($user_account_data)){
                                echo "<script>console.log('Testing2');</script>";
                                // THIS IS THE CHECKING IF THE STUDENT OF THE GROUP HAVE SAME COURSE IN THE STUDENT REGISTER AND THE RECORD
                                // IF NOT SAME ITS CONTINUE THE IF CONDITION if(!empty($user_account_data))
                                if($check_course == $course){
                                    echo "<script>console.log('Testing3');</script>";
                                    $numberOfStudentInAGroup = "SELECT COUNT(*) as count FROM useraccount WHERE group_id = $list_group_id AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND role_account_id = 2";
                                    $numberOfStudentInAGroup_result = mysqli_query($con, $numberOfStudentInAGroup);
                                    $numberOfStudentInAGroup_data = mysqli_fetch_assoc($numberOfStudentInAGroup_result);
                                    $count = $numberOfStudentInAGroup_data['count'];

                                    // THIS IS THE CHECKING IF THE GROUP ARE FULL OR NOT
                                    if($count < $list_number_student){
                                        echo "<script>console.log('Testing4');</script>";
                                        $sql = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (NULL, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)";
                                        $stmt = $con->prepare($sql);
                                        $stmt->bind_param("ssssssssssssssssssssss", $Spassword, $surname, $firstname, $middlename, $full_name, $email, $contactnumber, $street_barangay, $city_municipality, $province, $address, $course, $yearlevel, $student_section, $sex, $birthday, $studentnumber, $component_name, $list_group_id, $qrfile, $schoolyear_id, $semester_id);
                                        $result = $stmt->execute();
                                    }else{ // THIS WILL TRIGGER WHEN THE GROUP ARE FULL AND FIND THE LAST GROUP
                                        echo "<script>console.log('Testing5');</script>";
                                        $numberoflimit = '0, 1';
                                        $numberoflimitrow = 0;

                                        while (true) {
                                            // Query the previous group using LIMIT
                                            $groupAreFull = "SELECT * FROM grouptable WHERE component_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id ORDER BY group_id DESC LIMIT $numberoflimit";
                                            $groupAreFull_result = $con->query($groupAreFull);
                                            $groupAreFull_data = $groupAreFull_result->fetch_assoc();

                                            // THIS IS THE CHECKING IF THE QUERY IS HAVE VALUE
                                            if ($groupAreFull_data) {
                                                echo "<script>console.log('Testing6');</script>";
                                                $last_group_name = $groupAreFull_data['group_name'];
                                                $last_group_id = $groupAreFull_data['group_id'];
                                                $last_number_student = $groupAreFull_data['number_student'];

                                                // Check the number of students in the previous group
                                                $UserAccountGroup = "SELECT COUNT(*) AS count FROM useraccount WHERE group_id = $last_group_id AND role_account_id = 2";
                                                $UserAccountGroup_result = mysqli_query($con, $UserAccountGroup);
                                                $UserAccountGroup_data = mysqli_fetch_assoc($UserAccountGroup_result);
                                                $count = $UserAccountGroup_data['count'];

                                                // THIS IS THE CHECKING OF THE LAST GROUP ARE FULL OR NOT
                                                if ($count < $last_number_student) {
                                                    echo "<script>console.log('Testing7');</script>";
                                                    // Insert into the previous group
                                                    $sql = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (NULL, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)";
                                                    $stmt = $con->prepare($sql);
                                                    $stmt->bind_param("ssssssssssssssssssssss", $Spassword, $surname, $firstname, $middlename, $full_name, $email, $contactnumber, $street_barangay, $city_municipality, $province, $address, $course, $yearlevel, $student_section, $sex, $birthday, $studentnumber, $component_name, $last_group_id, $qrfile, $schoolyear_id, $semester_id);
                                                    $result = $stmt->execute();
                                                    break;
                                                } else { // THIS IS WILL BE TRIGGER WHEN THE LAST GROUP ARE FULL AND CHECK THE PREVIEWS GROUP ARE NOT FULL
                                                    // Move to the next previous group
                                                    echo "<script>console.log('Testing8');</script>";
                                                    $numberoflimit = ($numberoflimitrow + 1) . ', 1';
                                                    $numberoflimitrow++;
                                                    echo "<script>console.log('numberoflimit:', " . json_encode($numberoflimit) . ");</script>";
                                                }
                                            } else {
                                                // No more previous groups, handle as needed (e.g., create a new group)
                                                echo "<script>console.log('No available group found');</script>";
                                                break;
                                            }
                                        }
                                    }
                                    break;
                                }else{
                                    $counter++;
                                }
                                if($cwts_group_count == $counter){
                                    $numberoflimit = '0, 1';
                                        $numberoflimitrow = 0;

                                        while (true) {
                                            // Query the previous group using LIMIT
                                            $groupAreFull = "SELECT * FROM grouptable WHERE component_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id ORDER BY group_id DESC LIMIT $numberoflimit";
                                            $groupAreFull_result = $con->query($groupAreFull);
                                            $groupAreFull_data = $groupAreFull_result->fetch_assoc();

                                            // THIS IS THE CHECKING IF THE QUERY IS HAVE VALUE
                                            if ($groupAreFull_data) {
                                                echo "<script>console.log('Testing8');</script>";
                                                $last_group_name = $groupAreFull_data['group_name'];
                                                $last_group_id = $groupAreFull_data['group_id'];
                                                $last_number_student = $groupAreFull_data['number_student'];

                                                // Check the number of students in the previous group
                                                $UserAccountGroup = "SELECT COUNT(*) AS count FROM useraccount WHERE group_id = $last_group_id AND role_account_id = 2";
                                                $UserAccountGroup_result = mysqli_query($con, $UserAccountGroup);
                                                $UserAccountGroup_data = mysqli_fetch_assoc($UserAccountGroup_result);
                                                $count = $UserAccountGroup_data['count'];

                                                // THIS IS THE CHECKING OF THE LAST GROUP ARE FULL OR NOT
                                                if ($count < $last_number_student) {
                                                    echo "<script>console.log('Testing9');</script>";
                                                    // Insert into the previous group
                                                    $sql = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (NULL, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)";
                                                    $stmt = $con->prepare($sql);
                                                    $stmt->bind_param("ssssssssssssssssssssss", $Spassword, $surname, $firstname, $middlename, $full_name, $email, $contactnumber, $street_barangay, $city_municipality, $province, $address, $course, $yearlevel, $student_section, $sex, $birthday, $studentnumber, $component_name, $last_group_id, $qrfile, $schoolyear_id, $semester_id);
                                                    $result = $stmt->execute();
                                                    break;
                                                } else { // THIS IS WILL BE TRIGGER WHEN THE LAST GROUP ARE FULL AND CHECK THE PREVIEWS GROUP ARE NOT FULL
                                                    // Move to the next previous group
                                                    echo "<script>console.log('Testing10');</script>";
                                                    $numberoflimit = ($numberoflimitrow + 1) . ', 1';
                                                    $numberoflimitrow++;
                                                    echo "<script>console.log('numberoflimit:', " . json_encode($numberoflimit) . ");</script>";
                                                }
                                            } else {
                                                // No more previous groups, handle as needed (e.g., create a new group)
                                                echo "<script>console.log('No available group found');</script>";
                                                break;
                                            }
                                        }
                                }
                            }else{ // THIS IS WILL TRIGER WHEN THE GROUP DON'T HAVE STUDENT
                                $sql = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (NULL, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)";
                                $stmt = $con->prepare($sql);
                                $stmt->bind_param("ssssssssssssssssssssss", $Spassword, $surname, $firstname, $middlename, $full_name, $email, $contactnumber, $street_barangay, $city_municipality, $province, $address, $course, $yearlevel, $student_section, $sex, $birthday, $studentnumber, $component_name, $list_group_id, $qrfile, $schoolyear_id, $semester_id);
                                $result = $stmt->execute();
                                break;
                            }
                        }
                    } else {
                        $rotc_group_query = "SELECT group_id, group_name, number_student FROM grouptable WHERE component_id = 1 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                        $rotc_group_results = mysqli_query($con, $rotc_group_query);

                        while ($rotc_group_row = mysqli_fetch_assoc($rotc_group_results)) {
                            $list_group_id = $rotc_group_row['group_id'];
                            $list_group_name = $rotc_group_row['group_name'];
                            $list_number_student = $rotc_group_row['number_student'];

                            $user_account_query = "SELECT * FROM useraccount WHERE group_id = $list_group_id AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND role_account_id = 2 LIMIT 1";
                            $user_account_result = mysqli_query($con, $user_account_query);
                            $user_account_data = mysqli_fetch_assoc($user_account_result);
                            $check_gender = $user_account_data['gender'];

                            // THIS IS THE CHECKING OF GROUP IF HAVE STUDENT
                            if(!empty($user_account_data)){
                                // THIS IS CHECKING IF THE GROUP THAT HAVE STUDENT ARE SAME GENDER TO THE REGISTER STUDENT IF NOT CONTINUE THE LOOP
                                if($check_gender == $sex){
                                    $numberOfStudentInAGroup = "SELECT COUNT(*) as count FROM useraccount WHERE group_id = $list_group_id AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND role_account_id = 2";
                                    $numberOfStudentInAGroup_result = mysqli_query($con, $numberOfStudentInAGroup);
                                    $numberOfStudentInAGroup_data = mysqli_fetch_assoc($numberOfStudentInAGroup_result);
                                    $count = $numberOfStudentInAGroup_data['count'];

                                    // THIS IS CHECKING IF THE GROUP ARE FULL OR NOT
                                    if($count < $list_number_student){ // here the insert of group_id if find a group with same gender
                                        $sql = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (NULL, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)";
                                        $stmt = $con->prepare($sql);
                                        $stmt->bind_param("ssssssssssssssssssssss", $Spassword, $surname, $firstname, $middlename, $full_name, $email, $contactnumber, $street_barangay, $city_municipality, $province, $address, $course, $yearlevel, $student_section, $sex, $birthday, $studentnumber, $component_name, $list_group_id, $qrfile, $schoolyear_id, $semester_id);
                                        $result = $stmt->execute();
                                    }else{ // THIS WILL TRIGGER WHEN THE GROUP ARE FULL AND CHECK THE NEXT GROUP

                                        // $numberoflimit = 1;
                                        $nextgroupcounter = $list_group_id + 1;
                                        while (true) {
                                            $nextgroupcounter++;
                                            echo"<script>console.log($nextgroupcounter)</script>";
                                            // Query the next group using LIMIT
                                            // $groupAreFull = "SELECT * FROM grouptable WHERE group_id = $nextgroupcounter AND component_id = 1 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id ORDER BY group_id DESC LIMIT $numberoflimit";
                                            $groupAreFull = "SELECT * FROM grouptable WHERE group_id = $nextgroupcounter AND component_id = 1 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                                            $groupAreFull_result = $con->query($groupAreFull);
                                            $groupAreFull_data = $groupAreFull_result->fetch_assoc();

                                            // THIS IS THE CHECK IF THE NEXT GROUP ARE AVAILABLE
                                            if ($groupAreFull_data) {
                                                $next_group_name = $groupAreFull_data['group_name'];
                                                $next_group_id = $groupAreFull_data['group_id'];
                                                $next_number_student = $groupAreFull_data['number_student'];

                                                // Check the number of students in the next group
                                                $UserAccountGroup = "SELECT COUNT(*) AS count FROM useraccount WHERE group_id = $next_group_id AND role_account_id = 2";
                                                $UserAccountGroup_result = mysqli_query($con, $UserAccountGroup);
                                                $UserAccountGroup_data = mysqli_fetch_assoc($UserAccountGroup_result);
                                                $count = $UserAccountGroup_data['count'];

                                                // THIS IS CHECKING IF THE NEXT GROUP ARE FULL OR NOT
                                                if ($count < $next_number_student) {
                                                    $next_query = "SELECT * FROM useraccount WHERE group_id = $next_group_id AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND role_account_id = 2 LIMIT 1";
                                                    $next_result = mysqli_query($con, $next_query);
                                                    $next_data = mysqli_fetch_assoc($next_result);

                                                    // THIS IS CHECKING IF THE NEXT GROUP HAVE STUDENT 
                                                    if($next_data){
                                                        $next_gender = $next_data['gender'];
                                                        // THIS IS THE CHECKING IF THE STUDENT IN THE NEXT GROUP AND THE REGISTER ARE SAME GROUP
                                                        if($next_gender == $sex){
                                                            // Insert into the next group
                                                            $sql = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (NULL, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)";
                                                            $stmt = $con->prepare($sql);
                                                            $stmt->bind_param("ssssssssssssssssssssss", $Spassword, $surname, $firstname, $middlename, $full_name, $email, $contactnumber, $street_barangay, $city_municipality, $province, $address, $course, $yearlevel, $student_section, $sex, $birthday, $studentnumber, $component_name, $next_group_id, $qrfile, $schoolyear_id, $semester_id);
                                                            $result = $stmt->execute();
                                                            break;
                                                        }else{ // THIS WILL TRIGGER WHEN THE STUDENT IN THE NEXT GROUP AND THE REGISTER ARE NOT SAME
                                                            continue;
                                                        }
                                                    }else{ // THIS WILL TRIGGER WHEN THE NEXT GROUP DON'T HAVE STUDENT
                                                        $sql = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (NULL, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)";
                                                        $stmt = $con->prepare($sql);
                                                        $stmt->bind_param("ssssssssssssssssssssss", $Spassword, $surname, $firstname, $middlename, $full_name, $email, $contactnumber, $street_barangay, $city_municipality, $province, $address, $course, $yearlevel, $student_section, $sex, $birthday, $studentnumber, $component_name, $next_group_id, $qrfile, $schoolyear_id, $semester_id);
                                                        $result = $stmt->execute();
                                                        break;
                                                    }
                                                }
                                            } else {
                                                // No more next groups, handle as needed (e.g., create a new group)
                                                echo "<script>console.log('No available group found');</script>";
                                                continue;
                                            }
                                        }
                                    }
                                    break;
                                }
                            }else{ // THIS WILL BE TRIGGER WHEN THE GROUP DON'T HAVE STUDENT
                            $sql = "INSERT INTO useraccount (serialNumber, password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES (NULL, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)";
                            $stmt = $con->prepare($sql);
                            $stmt->bind_param("ssssssssssssssssssssss", $Spassword, $surname, $firstname, $middlename, $full_name, $email, $contactnumber, $street_barangay, $city_municipality, $province, $address, $course, $yearlevel, $student_section, $sex, $birthday, $studentnumber, $component_name, $list_group_id, $qrfile, $schoolyear_id, $semester_id);
                            $result = $stmt->execute();
                            break;
                            }
                        }
                    }
                    // END HERE

                    if ($result && $save_student_result) {
                        echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Registration Successful",
                            confirmButtonText: "OK"
                            }).then(function () {
                            window.location = "./portal.php";
                        });
                        </script>';
                    } else {
                        echo '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Registration Failed",
                                confirmButtonText: "OK"
                                });
                            </script>';                                   
                    }
                }
            }
        } 
    }
?>

    <!-- ======= Header ======= -->
<header id="header" class="fixed-top">
    <div class="container d-flex align-items-center mx-md-5 mx-2">
        <h1 class="logo me-auto">
            <img src="./assets/img/Logo3.png" alt="">
            <a href="index.php" style="color: #fff">NSTP Portal</a>
        </h1>
    </div>
</header>
<!-- End Header -->
<div class="main-container">
    <div class="form">
        <div class="back">
            <div style="padding: 10px;">
                <h2>Not yet Registered?</h2>
                <p>Register and keep updated to Activity</p>
                <button id="show-signup-form" class="slide btn btn-primary">Register</button>
            </div>
            <div style="padding: 10px;">
                <h2>Already Registered?</h2>
                <p>Sign in to access your account</p>
                <button id="show-signin-form" class="slide">Sign in</button>
            </div>
        </div>

        <div class="front">
            <div class="signin">
                <div class="signin-container">
                    <div class="title">Sign In</div>
                    <form method="post">
                        <div class="form-element">
                            <i class="fa fa-envelope"></i>
                            <input type="text" placeholder="Email" name="email" required autocomplete="off"/>
                        </div>
                        <div class="form-element">
                            <i class="fa fa-key"></i>
                            <input type="password" placeholder="Password" name="password" required/>
                        </div>
                        <div class="form-element">
                            <button type="submit" name="signin">Sign In</button>
                        </div>
                    </form>
                    <div class="form-element">
                        <a href="./forgot-password.php">Forgot password?</a>
                    </div>
                </div>
                <div id="show-signup-slide">
                    <h2>Not yet Registered?</h2>
                    <p>Register and keep updated to Activity</p>
                    <button>Register</button>
                </div>
            </div>
            <div class="signup" style="height: 95%;">
                <div class="register-contianer" style="overflow-y: scroll; height: 85%;">
                    <div class="title">Register</div>
                    <form  method="POST" id="myForm">
                        <div class="form-element">
                            <i class="fa fa-user"></i>
                            <input type="text" placeholder="First Name" name="firstname" id="firstname"/>
                            <span id="firstname-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <i class="fa fa-user"></i>
                            <input type="text" placeholder="Middle Name" name="middlename" id="middlename"/>
                            <span id="middlename-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <i class="fa fa-user"></i>
                            <input type="text" placeholder="Surname" name="surname" id="surname"/>
                            <span id="surname-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <i class="fa fa-envelope"></i>
                            <input type="email" placeholder="CvSU Email" name="email" id="email"/>
                            <span id="email-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <div class="studentnumbercomponent" style="flex: 1;">
                                <i class="fa fa-hashtag"></i>
                                <input type="text" placeholder="Student Number" name="studentnumber" id="student-number"/>
                                <span id="student-number-error" class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-element">
                            <select id="course" name="course" style="width: 100%;">
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
                            <span id="course-error" class="error-message "></span>
                        </div>

                        <div class="dropdown-container d-flex flex-rows gap-2 form-element">
                                <div class="flex-grow-1 w-100">
                                    <select id="year-level" name="yearlevel">
                                        <option value="" selected disabled hidden>Year Level</option>
                                        <option value="First Year">First Year</option>
                                        <option value="Second Year">Second Year</option>
                                        <option value="Third Year">Third Year</option>
                                        <option value="Fourth Year">Fourth Year</option>
                                    </select>
                                    <span id="year-level-error" class="error-message"></span>
                                </div>
                            <!-- <div class="dropdown-container d-flex flex-rows gap-2"> -->
                                <div style="max-width: 200px; min-width: 70px;">
                                    <select id="sex" name="sex">
                                        <option value="" selected disabled hidden>Sex</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <span id="sex-error" class="error-message"></span>
                                </div>
                            
                                <div class="flex-grow-1" style="min-width: 100px;">
                                    <select class="flex-1" id="student_section" name="student_section">
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
                                    <span id="student_section-error" class="error-message"></span>
                                </div>
                            <!-- </div> -->
                        </div>
                        <div class="form-element dropdown-container d-flex flex-rows gap-2">
                            <div class="flex-grow-1" style="min-width: 100px;">
                                <select id="birthday-month" name="birthday-month">
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
                                <span id="birthday-month-error" class="error-message"></span>
                            </div>

                            <div class="dropdown-container d-flex flex-rows gap-2">
                                <div style="min-width: 100px; flex: 1; position: relative;">
                                    <i class="fa fa-calendar-days"></i>
                                    <input type="text" placeholder="Day" name="birthday-day" id="birthday-day" min="1" max="31"/>
                                    <span id="birthday-day-error" class="error-message"></span>
                                </div>
                                <div style="min-width: 100px;">
                                    <select id="birthday-year" name="birthday-year">
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
                        <?php
                            $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                            $schoolyear_result = $con->query($schoolyear_query);
                            $schoolyear_data = $schoolyear_result->fetch_assoc();
                            if($schoolyear_data){
                                $schoolyear_id = $schoolyear_data['schoolyear_id'];
                                $semester_id = $schoolyear_data['semester_id'];

                                $countEnrolled = "SELECT COUNT(*) AS enrolledcount FROM enrolledstudent WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                                $countEnrolled_result = $con->query($countEnrolled);
                                $countEnrolled_data = $countEnrolled_result->fetch_assoc();
                                $enrolledcount = $countEnrolled_data['enrolledcount'];
                                $sixtyPercentEnrolled = 0.6 * $enrolledcount;
                                // $sixtyPercentEnrolled = 0.02 * $enrolledcount;

                                $cwtsflag = false; // this is will be the trigger if the 60% are meet

                                $cwtscount = "SELECT COUNT(*) AS cwtsregisteredcount FROM useraccount WHERE component_name = 'CWTS' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                                $cwtscount_result = $con->query($cwtscount);
                                $cwtscount_data = $cwtscount_result->fetch_assoc();
                                $cwtsregisteredcount = $cwtscount_data['cwtsregisteredcount'];

                                echo'<input type="hidden" name="sixtyPercentEnrolled" id="sixtyPercentEnrolled" value="'. $sixtyPercentEnrolled .'"/>';
                                echo'<input type="hidden" name="cwtsregisteredcount" id="cwtsregisteredcount" value="'. $cwtsregisteredcount .'"/>';

                                // Check if $cwtsregisteredcount is greater than or equal to $sixtyPercentEnrolled
                                if ($cwtsregisteredcount >= $sixtyPercentEnrolled) {
                                $cwtsflag = true;
                                echo'<input type="hidden" id="cwtsflag" value="'. $cwtsflag .'"/>';
                                echo '
                                <div class="form-element dropdown-container d-flex flex-rows gap-2" style="width: 100%;">
                                    <div style="width: 100%;">
                                        <div id="component-id">
                                            <select id="component_id" name="component_id" title="Choose a component">
                                                <option value="" selected disabled hidden>Choose Component</option>
                                                <option value="1">ROTC</option>
                                            </select>
                                            <span id="component-error" class="error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                ';
                                } else {
                                echo '
                                <div class="form-element dropdown-container d-flex flex-rows gap-2" style="width: 100%;">
                                    <div style="width: 100%;">
                                        <div id="component-id">
                                            <select id="component_id" name="component_id" title="Choose a component">
                                                <option value="" selected disabled hidden>Choose Component</option>
                                                <option value="1">ROTC</option>
                                                <option value="2">CWTS</option>
                                            </select>
                                            <span id="component-error" class="error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                ';
                                }
                            }
                        ?>
                        
                        <div class="form-element" style="flex: 1;">
                            <i class="fa fa-phone"></i>
                            <input type="text" placeholder="Contact Number" name="contactnumber" id="contact-number"/>
                            <span id="contact-number-error" class="error-message"></span>
                        </div>
                        <div class="form-element">
                            <i class="fa fa-house"></i>
                            <input type="text" placeholder="Street/Barangay" name="street_barangay" id="street-barangay"/>
                            <span id="street-barangay-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <i class="fa fa-house"></i>
                            <input type="text" placeholder="City/Municipality" name="city_municipality" id="city-municipality"/>
                            <span id="city-municipality-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <i class="fa fa-house"></i>
                            <input type="text" placeholder="Province" name="province" id="province"/>
                            <span id="province-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <i class="fa fa-key"></i>
                            <input type="password" placeholder="Password" name="password" id="password"/>
                            <span id="password-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <i class="fa fa-key"></i>
                            <input type="password" placeholder="Re-Type Password" name="re_password" id="re_password"/>
                            <span id="re_password-error" class="error-message"></span>
                        </div>

                        <div class="form-element">
                            <button type="submit" class="btn btn-primary" name="register">Register</button>
                        </div>
                    </form>
                </div>
                <div id="show-signin-slide">
                    <h2>Already Registered?</h2>
                    <p>Sign in to access your account</p>
                    <button >Sign in</button>
                </div>
            </div>
           
        </div>
    </div>
</div>
<script src="./assets/js/portal.js"></script>
<script>
    document.getElementById('show-signup-form').addEventListener('click', function () {
    document.getElementsByClassName('form')[0].classList.add('active');
    });
    document.getElementById('show-signin-form').addEventListener('click', function () {
    document.getElementsByClassName('form')[0].classList.remove('active');
    });

    document.getElementById('show-signup-slide').addEventListener('click', function () {
    document.getElementsByClassName('form')[0].classList.add('active');
    });

    document.getElementById('show-signin-slide').addEventListener('click', function () {
    document.getElementsByClassName('form')[0].classList.remove('active');
    });
</script>

<!-- Customized component drop down -->
<script>
    // Function to update the custom select
    function updateCustomSelect(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex].text;
        $(selectElement).next('.styledSelect').text(selectedOption);
    }

    // Add event listener to "year-level" select element
    const cwtsflag = document.getElementById("cwtsflag");
    const yearLevelSelect = document.getElementById("year-level");
    const componentSelect = document.getElementById("component_id");

    yearLevelSelect.addEventListener("change", function () {
        const selectedYearLevel = yearLevelSelect.value;
        componentSelect.innerHTML = "";

        // Add the hidden default option back
        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "Choose Component";
        defaultOption.style.display = "none";
        componentSelect.appendChild(defaultOption);

        if (selectedYearLevel === "First Year" && cwtsflag) {
        const optionROTC = document.createElement("option");
        optionROTC.value = "1";
        optionROTC.textContent = "ROTC";
        componentSelect.appendChild(optionROTC);

        } else if (selectedYearLevel === "First Year"){
        const optionROTC = document.createElement("option");
        optionROTC.value = "1";
        optionROTC.textContent = "ROTC";
        componentSelect.appendChild(optionROTC);

        const optionCWTS = document.createElement("option");
        optionCWTS.value = "2";
        optionCWTS.textContent = "CWTS";
        componentSelect.appendChild(optionCWTS);
        }else {
        const optionROTC = document.createElement("option");
        optionROTC.value = "1";
        optionROTC.textContent = "ROTC";
        componentSelect.appendChild(optionROTC);
        }

        // Update the custom select
        updateCustomSelect(componentSelect);
    });

    // Custom select click event
    $('.styledSelect').click(function (e) {
    e.stopPropagation();
    const $this = $(this);
    // Check if the styled select is already active
    if ($this.hasClass('active')) {
    $this.removeClass('active').next('ul.options').slideUp('fast');
    } else {
    // Hide any other active styled selects and their option lists
    $('div.styledSelect.active').each(function () {
    $(this).removeClass('active').next('ul.options').slideUp('fast');
    });
    // Show the current styled select and its option list
    $this.addClass('active').next('ul.options').slideDown('fast');
    }
    });

    // Custom select list item click event
    $('.options li').click(function (e) {
    e.stopPropagation();
    const $this = $(this);
    const $styledSelect = $this.closest('.styledSelect');
    $styledSelect.text($this.text()).removeClass('active');
    const value = $this.attr('rel');
    componentSelect.value = value;
    $styledSelect.next('ul.options').slideUp('fast');
    });

    // Hide the custom select when clicking outside of it
    $(document).click(function () {
    $('.styledSelect.active').removeClass('active').next('ul.options').slideUp('fast');
    });

    // Set the initial default option for the custom select
    updateCustomSelect(componentSelect);
</script>
</body>
</html>
