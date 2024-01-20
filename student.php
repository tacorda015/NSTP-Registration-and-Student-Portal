<?php
include('connection.php');
session_start();
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}

// get user data from session
$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];

$user_profile_query = " SELECT t.*, g.group_name, c.course_code FROM useraccount t 
                        LEFT JOIN grouptable g ON t.group_id = g.group_id
                        LEFT JOIN coursetable c ON t.course = c.course_name
                        WHERE user_account_id = {$user_id}";
$user_profile_result = $con->query($user_profile_query);
$user_profile_data = $user_profile_result->fetch_assoc();

$role_account_id = $user_profile_data['role_account_id'];
$studentNumber = $user_profile_data['student_number'];
$groupName = $user_profile_data['group_name'];
$student_group = $user_profile_data['group_id'];
$student_account_id = $user_id;

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 
if($student_group){
  $inchargeName = "SELECT full_name FROM useraccount WHERE group_id = $student_group AND role_account_id = 3";
  $inchargeNameResult = $con->query($inchargeName);
  
  if($user_profile_data['year_level'] == 'First Year'){
    $yearLevel = 10;
  }else if ($user_profile_data['year_level'] == 'Second Year'){
    $yearLevel = 20;
  }else if ($user_profile_data['year_level'] == 'Third Year'){
    $yearLevel = 30;
  }else if ($user_profile_data['year_level'] == 'Fourth Year'){
    $yearLevel = 20;
  }else{
    $yearLevel = 'NA';
  }
}else{
  $yearLevel = NULL;
}


$qr_code = $user_data['qrimage'];
$passwordshow = base64_decode($user_profile_data['password']);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- For Css -->
    <!-- <link rel="stylesheet" href="./assets/css/student.css"/> -->
    <!-- <link rel="stylesheet" href="./assets/css/main-style.css"/> -->
    <link rel="stylesheet" href="./assets/css/mainStyle.css">

    <!-- Favicons -->
    <link href="assets/img/Logo.png" rel="icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Boxiocns CDN Link -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./boxicons-2.1.4/css/boxicons.min.css">

    <!-- For bootstrap -->
    <link rel="stylesheet" href="./node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css" />
    <script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Link for Fontawosome -->
    <script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>
  </head>
  <body style="background-color: #edf8ff;">
    <div class="wrapper">
      <div class="sidebar close">
        <div class="logo-details">
          <a href="./student.php">
            <img src="./assets/img/logo3.png" alt="NSTP Logo">
          </a>
        </div>
        <ul class="nav-links">
          <li>
            <a href="./student.php"><i class="bi bi-person-circle"></i>
              <span class="link_name">Profile</span>
            </a>
          </li>
          <li>
            <div class="iocn-link">
              <a href="./student/attendance.php"><i class="bi bi-calendar3"></i>
                <span class="link_name">Attendance</span>
              </a>
            </div>
          </li>
          <li>
            <div class="iocn-link">
              <a href="./student/qrcode.php"><i class="bi bi-qr-code"></i>
                <span class="link_name">QR Code</span>
              </a>
            </div>
          </li>
          <li>
            <div class="iocn-link">
              <a href="./student/module.php">
                <i class="bi bi-journals"></i>
                <span class="link_name">Lecture</span>
              </a>
            </div>
          </li>
          <li>
            <div class="iocn-link">
              <a href="./student/locationlist.php"><i class="bi bi-geo-alt"></i>
                <span class="link_name">Map</span>
              </a>
            </div>
          </li>
          <li>
          <div class="iocn-link">
            <a href="./student/schedule.php"><i class="bi bi-calendar-event"></i>
              <span class="link_name">Schedule Calendar</span>
            </a>
          </div>
        </li>
          <li class="student-sidebar">
            <div class="iocn-link">
              <a href="./student/announcement.php">
                <?php
                $userId = $user_data['user_account_id'];
                $status_query = "SELECT COUNT(*) AS status_count FROM announcementtable WHERE view_status = 1 AND sender_id != $userId AND recipient_id = $userId";
                $status_result = $con->query($status_query);

                if ($status_result) {
                    $row = $status_result->fetch_assoc();
                    $status_count = $row['status_count'];

                    if ($status_count >= 1) {
                        echo '<i class="bi bi-bell-fill" style="position: relative;">
                                <div class="pulsing-div"></div>
                              </i>';
                    } else {
                        echo '<i class="bi bi-bell"></i>';
                    }
                } else {
                    // Handle query error
                    echo '<i class="bi bi-bell"></i>';
                }
                ?>
                <span class="link_name">Announcement</span>
              </a>
            </div>
          </li>
        </ul>
      </div>
      <!-- End Side Bar -->
      <section class="home-section">
          <div class="home-content">
              <i class="bx bx-menu"></i>
              <div class="profile">
                  <?php 
                  $default_image = "uploads/default.jpeg";

                  // if profile image is empty, set it to default image
                  if (empty($user_profile_data['picture'])) {
                      $user_profile_data['picture'] = $default_image;
                  }
                  ?>
                  <?php echo "<span>" . $user_profile_data['surname'] . "</span>";?> 
                  <div class="pro-file">
                    <div class="imgPro">
                      <img class='profilepic' src='./student/<?php echo $user_profile_data['picture']; ?>' alt='Profile Picture'>
                    </div>
                  </div>
              </div>
              <div class="menu">
                <ul>
                    <li>
                      <a href="./student/profile.php"><i class="bi bi-person-vcard"></i>Profile</a>
                    </li>
                    <li>
                      <a href="logout.php"><i class="bi bi-box-arrow-left"></i>Logout</a>
                    </li>
              </ul>
            </div>
          </div>
          <div class="home-main-container">
          <?php
          $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
          $schoolyear_result = $con->query($schoolyear_query);
            $schoolyear_data = $schoolyear_result->fetch_assoc();
          if($schoolyear_data){
            $schoolyear_id = $schoolyear_data['schoolyear_id'];
            $semester_id = $schoolyear_data['semester_id'];

          }else{
            echo"<script>console.log('No School Year Yet');</script>";
          }
          
          $checkIfEnrolled = "SELECT COUNT(*) AS enrolledStudentStatus FROM enrolledstudent WHERE student_number = $studentNumber AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
          $checkIfEnrolledResult = $con->query($checkIfEnrolled);
          $checkIfEnrolledData = $checkIfEnrolledResult->fetch_assoc();
          $isEnrolled = $checkIfEnrolledData['enrolledStudentStatus'];
          if($isEnrolled > 0){
          ?>
          <div class="currentSchoolyear">
            <div class="schoolYear-content border rounded shadow">
              <i class='bx bx-calendar'></i>
              <span style="font-weight: 500;"><?php echo  $schoolyear_data['schoolyear'] . " - " .$schoolyear_data['schoolyear'] + 1 ?> | <?php $semester_name = ($semester_id == 1) ? "First Semester" : "Second Semester";
              echo $semester_name;
              ?></span>
            </div>
          </div>
          <?php
          }else{
            ?>
            <div class="currentSchoolyear">
              <div class="schoolYear-content border rounded shadow">
                <i class='bx bxs-user-x' style="font-size: 24px;"></i>
                <span style="font-weight: 500;">Not Enrolled</span>
              </div>
            </div>
            <?php
          }
          if($student_group){
            $check_attendance_status = "SELECT * FROM attendancetable WHERE user_account_id = $student_account_id AND group_id = $student_group";
          $check_attendance_status_result = $con->query($check_attendance_status);
          if($check_attendance_status && $check_attendance_status_result->num_rows > 0){
            $check_attendance_status_data = $check_attendance_status_result->fetch_assoc();
            $remark_status = $check_attendance_status_data['remark_status'];
            
            if($remark_status == 1){
              echo'
                  <div style="width: 90%;" class="pt-2">
                    <div class="alert alert-warning mx-2 my-0 w-100" role="alert">
                      You are Absent last meeting make sure to report to Incharge person of your Platoon/Cluster before next Meeting.
                    </div>
                  </div>
                  ';
            }elseif($remark_status == 2){
              echo'
                  <div style="width: 90%;" class="pt-2">
                    <div class="alert alert-warning m-0 w-100" role="alert">
                      You don\'t have Time out make sure to report to Incharge person.
                    </div>
                  </div>
                  ';
            }elseif($remark_status == 6){
              echo'
              <div style="width: 90%;" class="pt-2">
                <div class="alert alert-danger m-0 w-100" role="alert">
                  You are Inactive you need to report at the OSAS about this Matter.
                </div>
              </div>
              ';
            }
            // echo"<script>console.log($student_group);</script>";
          }
          }
          ?>
          <div class="currentSchoolyear">
            <div class="infoContainer border rounded shadow">
              <div class="dashStudentNumber">
                <label>Student Number</label>
                <!-- <span><?php echo $user_profile_data['student_number'] ;?></span> -->
                <span><?php echo isset($user_profile_data['student_number']) ? $user_profile_data['student_number'] : 'No Information'; ?></span>
              </div>  

              <div class="dashStudentNumber">
                <label>Name</label>
                <span><?php echo isset($user_profile_data['full_name']) ? $user_profile_data['full_name'] : 'No Information'; ?></span>
              </div>

              <div class="dashStudentNumber">
                <label>Year Level</label>
                <span><?php echo isset($user_profile_data['year_level']) ? $user_profile_data['year_level'] : 'No Information'; ?></span>
              </div>  

              <div class="dashStudentNumber">
                  <label>Section</label>
                  <span>
                      <?php
                      if($yearLevel){
                        $section = $user_profile_data['course_code'] . ' ' . $yearLevel . $user_profile_data['semester_id'] . $user_profile_data['student_section'];
                      
                        echo !empty($section) ? $section : 'No Record';
                      }else{
                        echo"No Information";
                      }
                      
                      ?>
                  </span>
              </div>
 
            </div>
            
          </div>

          <div class="currentSchoolyears">
            <div class="infoContainers border rounded shadow infoCourseContainers">
              <div class="dashStudentNumbers">
                <label>Course</label>
                <span><?php echo isset($user_profile_data['course']) ? $user_profile_data['course'] : 'No Course'; ?></span>
              </div>
              <hr style="margin: .5rem 0; color: black; width: 100%;">
              <div class="dashStudentNumbers">
                <label>Component</label>
                <span><?php echo isset($user_profile_data['component_name']) ? $user_profile_data['component_name'] : 'No Component'; ?></span>
              </div>
            </div>
            <div class="infoContainers border rounded shadow infoGrourpContainers">
              <div class="dashStudentNumbers">
                <label>Group Name</label>
                <?php
                if($student_group){
                  echo"<span>$groupName</span>";
                }else{
                  echo"<span>No Group</span>";
                }
                ?>
              </div>
              <hr style="margin: .5rem 0; color: black; width: 100%;">
              <div class="dashStudentNumbers">
                <label>Incharge Name</label>
                <?php
                if($student_group){
                  if ($inchargeNameResult && $inchargeNameResult->num_rows > 0) {
                    $inchargeNameData = $inchargeNameResult->fetch_assoc();
                    echo '<span>' . $inchargeNameData['full_name'] . '</span>';
                  } else {
                    echo '<span>No Incharge</span>';
                  }
                }else{
                  echo"<span>No Group</span>";
                }
                ?>
              </div>
            </div>
          </div>
          
          <div class="currentSchoolyears">
            <div class="infoContainers border rounded shadow infoAddressContainers">
              <div class="dashStudentNumbers">
                <label>Addres</label>
                <span><?php echo $user_profile_data['homeaddress'] ;?></span>
              </div> 

              <hr style="margin: .5rem 0; color: black; width: 100%;"> 

              <div class="dashStudentNumbers">
                <label>Contact Number</label>
                <span><?php echo $user_profile_data['contactNumber'] ;?></span>
              </div>  

              <hr style="margin: .5rem 0; color: black; width: 100%;">

              <div class="dashStudentNumbers">
                <label>Email Address</label>
                <span><?php echo $user_profile_data['email_address'] ;?></span>
              </div>  
            </div>
          </div>
          </div>
      </section>
    </div>
    <script src="asset/js/index.js"></script>
    <script src="./asset/js/topbar.js"></script>
  </body>
</html>
