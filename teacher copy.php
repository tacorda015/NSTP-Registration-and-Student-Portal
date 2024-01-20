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
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8" />

<!-- For Css -->
<link rel="stylesheet" href="./assets/css/main-style.css">
<!-- <link rel="stylesheet" href="./assets/css/teacher.css"> -->

<!-- Favicons -->
<link href="assets/img/logo.png" rel="icon" />

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
        <img src="./assets/img/logo3.png" alt="NSTP Logo">
        <!-- <span class="logo_name">NSTP</span> -->
      </div>
      <ul class="nav-links">
        <li>
          <a href="./teacher.php"><i class='bx bxs-dashboard'></i>
            <span class="link_name">Dashboard</span>
          </a>
          <ul class="sub-menu blank">
            <li><a class="link_name" href="./teacher.php">Dashboard</a></li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/attendancegroup.php"><i class="fa-solid fa-calendar-days"></i>
              <span class="link_name">Attendance</span>
            </a>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="./teacher/attendancegroup.php">Attendance</a></li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/scanner.php"><i class='bx bx-qr-scan'></i>
              <span class="link_name">QR Scanner</span>
            </a>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="./teacher/scanner.php">QR Scanner</a></li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/studentgroup.php">
              <i class="fa-solid fa-user-large"></i>
              <span class="link_name">Student</span>
            </a>
            <i class="bx bxs-chevron-down arrow"></i>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="./teacher/studentgroup.php">Student</a></li>
            <li>
              <a href="./teacher/studentgroup.php">
                <i class="bx bx-list-ol"></i>
                <span>Student List</span>
              </a>
            </li>
            <li>
              <a href="./teacher/inputgrade.php">
                <i class="bx bx-plus"></i>
                <span>Input Grade</span>
              </a>
            </li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/modulelist.php">
              <i class="fa-solid fa-book"></i>
              <span class="link_name">Upload Lecture</span>
            </a>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="./teacher/modulelist.php">Upload Lecture</a></li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./teacher/listlocation.php">
            <i class="fa-sharp fa-solid fa-map-location-dot"></i>
              <span class="link_name">Map</span>
            </a>
            <i class="bx bxs-chevron-down arrow"></i>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="./teacher/listlocation.php">Map</a></li>
            <li>
              <a href="./teacher/listlocation.php">
                <i class="bx bx-list-ol"></i>
                <span>Location List</span>
              </a>
            </li>
            <li>
              <a href="./teacher/locationadd.php">
                <i class="bx bxs-location-plus"></i>
                <span>Add Location</span>
              </a>
            </li>
          </ul>
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
                      echo '<i class="fa-solid fa-bullhorn" style="position: relative;">
                              <div class="pulsing-div"></div>
                            </i>';
                  } else {
                      echo '<i class="fa-solid fa-bullhorn"></i>';
                  }
              } else {
                  // Handle query error
                  echo '<i class="fa-solid fa-bullhorn"></i>';
              }
              ?>
              <span class="link_name">Announcement</span>
            </a>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="./teacher/announcelist.php">Announcement</a></li>
          </ul>
        </li>
      </ul>
      </div>
      <section class="home-section">
      <div class="home-content">
          <i class="bx bx-menu"></i>
          <div class="profile">
            <?php 
                $picture_id = $user_data['user_account_id'];
                $picture_query = "SELECT * FROM useraccount WHERE user_account_id = $picture_id";
                $picture_result = $con->query($picture_query);
                $picture_data = $picture_result->fetch_assoc();
                $default_image = "uploads/default.jpeg";
                // if profile image is empty, set it to default image
                if (empty($picture_data['picture'])) {
                    $picture_data['picture'] = $default_image;
              }
              $role = "SELECT * FROM roleaccount WHERE role_account_id = {$user_data['role_account_id']}";
              $result = $con->query($role);
              $role_data = $result->fetch_assoc();
              ?>
              <h3><?php echo "{$picture_data['full_name']} <br /><span>{$role_data['role_name']}</span></h3>"?> 
              <div class="pro-file">
                <div class="imgPro">
                  <img class='profilepic' src='./teacher/<?php echo $picture_data['picture']; ?>' alt='Profile Picture'>
                </div>
              </div>
          </div>
          <div class="menu">
            <ul>
              <li>
                <a href="./teacher/teacherprofile.php"><i class='bx bxs-user-rectangle'></i>Profile</a>
              </li>
              <li>
                <a href="./logout.php"><i class='bx bx-log-out'></i>Logout</a>
              </li>
            </ul>
          </div>
      </div>
        <div class="teacherdash-container">
            <div class="new-div"></div>
        </div>
    </section>
  </div>
<script src="./asset/js/index.js"></script>
<script src="./asset/js/topbar.js"></script>
</body>
</html>
