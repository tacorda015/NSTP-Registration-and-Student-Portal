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
$useraccount_query = " SELECT t.*, g.group_name FROM useraccount t 
                        LEFT JOIN grouptable g ON t.group_id = g.group_id
                        WHERE user_account_id = {$user_id}";
$useraccount_result = $con->query($useraccount_query);
$user_profile_data = $useraccount_result->fetch_assoc();

$role_account_id = $user_profile_data['role_account_id'];
$studentNumber = $user_profile_data['student_number'];
$studentGroupId = $user_profile_data['group_id'];
$student_group = $user_profile_data['group_name'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

  if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
    } elseif ($role_data['role_name'] == 'Student') {
      header('Location: student.php');
      ob_end_flush();
  } 
  
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8" />

<!-- For Css -->
<link rel="stylesheet" href="./assets/css/mainStyle.css">
<!-- <link rel="stylesheet" href="./assets/css/main-style.css"> -->
<!-- <link rel="stylesheet" href="./assets/css/teacher.css"> -->

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
<script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- Link for Fontawosome -->
<script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>

  <div class="wrapper">
    <div class="sidebar close">
      <div class="logo-details">
        <a href="./teacher.php">
          <img src="./assets/img/logo3.png" alt="NSTP Logo">
        </a>
        <!-- <span class="logo_name">NSTP</span> -->
      </div>
      <ul class="nav-links">
        <li>
          <div class="iocn-link">
            <a href="./teacher.php"><i class="bi bi-person-circle"></i>
              <span class="link_name">Profile</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/attendancegroup.php"><i class="bi bi-calendar3"></i>
              <span class="link_name">Attendance</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/scanner.php"><i class="bi bi-qr-code-scan"></i>
              <span class="link_name">QR Scanner</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/studentgroup.php">
              <i class="bi bi-person-fill"></i>
              <span class="link_name">Student</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/inputgrade.php">
              <i class="bi bi-upload"></i>
              <span class="link_name">Upload Grades</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/modulelist.php">
              <i class="bi bi-journals"></i>
              <span class="link_name">Upload Lecture</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/listlocation.php"><i class="bi bi-geo-alt"></i>
              <span class="link_name">Map</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/schedule-page.php"><i class="bi bi-calendar-event"></i>
              <span class="link_name">Schedule</span>
            </a>
          </div>
        </li>
        <li class="teacher-sidebar">
          <div class="iocn-link">
            <a href="./teacher/announcelist.php">
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
      <section class="home-section">
      <div class="home-content">
          <i class="bx bx-menu"></i>
          <div class="profile">
            <?php 
                $picture_query = "SELECT * FROM useraccount WHERE user_account_id = $user_id";
                $picture_result = $con->query($picture_query);
                $picture_data = $picture_result->fetch_assoc();
                $default_image = "uploads/default.jpeg";
                $component_name = $picture_data['component_name'];
                // if profile image is empty, set it to default image
                if (empty($picture_data['picture'])) {
                    $picture_data['picture'] = $default_image;
              }
              echo "<span>" . $picture_data['surname'] . "</span>";
              ?>
              <div class="pro-file">
                <div class="imgPro">
                  <img class='profilepic' src='./teacher/<?php echo $picture_data['picture']; ?>' alt='Profile Picture'>
                </div>
              </div>
          </div>
          <div class="menu">
            <ul>
              <li>
                <a href="./teacher/teacherprofile.php"><i class="bi bi-person-vcard"></i>Profile</a>
              </li>
              <li>
                <a href="./logout.php"><i class="bi bi-box-arrow-left"></i>Logout</a>
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

          $checkIfEnrolled = "SELECT COUNT(*) AS enrolledStudentStatus FROM useraccount WHERE student_number = $studentNumber AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
          $checkIfEnrolledResult = $con->query($checkIfEnrolled);
          $checkIfEnrolledData = $checkIfEnrolledResult->fetch_assoc();
          $isEnrolled = $checkIfEnrolledData['enrolledStudentStatus'];
          if($isEnrolled > 0){
          ?>
          <div class="currentSchoolyear">
            <div class="schoolYear-content border rounded shadow">
              <i class='bx bx-calendar'></i>
              <span style="font-weight: 500;">School Year <?php echo  $schoolyear_data['schoolyear'] . " - " .$schoolyear_data['schoolyear'] + 1 ?> | <?php $semester_name = ($semester_id == 1) ? "First Semester" : "Second Semester";
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
                <span style="font-weight: 500;">Not assigned to be Incharge</span>
              </div>
            </div>
            <?php
          }
          ?>
          <div class="currentSchoolyear">
            <div class="border rounded shadow infoContainer">
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

            </div>
            
          </div>

          <div class="currentSchoolyears">
            <div class="border rounded shadow infoContainers infoCourseContainers">
              <div class="dashStudentNumbers">
                <?php
                if ($user_profile_data['component_name'] === 'ROTC') {
                    echo "<label>Course</label>";
                    echo "<span>" . (isset($user_profile_data['course']) ? $user_profile_data['course'] : 'No Course') . "</span>";
                } else {
                    echo "<label>Department</label>";
                    echo "<span>" . (isset($user_profile_data['course']) ? $user_profile_data['course'] : 'No Course') . "</span>";
                }
                ?>
              </div>
              <hr style="margin: .5rem 0; color: black; width: 100%;">
              <div class="dashStudentNumbers">
                <label>Component</label>
                <span><?php echo isset($user_profile_data['component_name']) ? $user_profile_data['component_name'] : 'No Component'; ?></span>
              </div>
            </div>
            <div class="border rounded shadow infoContainers infoGrourpContainers">
              <div class="dashStudentNumbers">
                <label>Group Name</label>
                <?php
                if($student_group){
                  echo"<span>$student_group</span>";
                }else{
                  echo"<span>No Group</span>";
                }
                ?>
              </div>
              <hr style="margin: .5rem 0; color: black; width: 100%;">
              <div class="dashStudentNumbers">
                <label>Number of Students</label>
                <?php
                echo "<script>console.log($studentGroupId 'dasd')</script>";
                if(!empty($studentGroupId)){
                  $studentCountQuery = "SELECT COUNT(*) AS studentCount FROM useraccount WHERE role_account_id = 2 AND group_id = $studentGroupId";
                  $studentCountResult = $con->query($studentCountQuery);
                  if($studentCountResult){
                    $studentCountData = $studentCountResult->fetch_assoc();
                    $studentCount = $studentCountData['studentCount'];
                    echo"<span>". $studentCount ."</span>";
                  }else{
                    echo"<span>No student yet</span>";
                  }
                }else{
                  echo"<span>No Group</span>";
                }
                
                ?>
              </div>
            </div>
          </div>
          
          <div class="currentSchoolyears">
            <div class="border rounded shadow infoContainers infoAddressContainers">
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
<script src="./asset/js/index.js"></script>
<script src="./asset/js/topbar.js"></script>
</body>
</html>
