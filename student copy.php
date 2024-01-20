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
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
} 

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <!--<title> Drop Down Sidebar Menu </title>-->
    <link rel="stylesheet" href="asset/css/style.css" />
    <!-- Boxiocns CDN Link -->
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <div class="sidebar close">
      <div class="logo-details">
        <i class="bx bxs-color"></i>
        <span class="logo_name">NSTP</span>
      </div>
      <ul class="nav-links">
        <li>
          <a href="./student.php">
            <i class="bx bx-grid-alt"></i>
            <span class="link_name">Dashboard</span>
          </a>
          <ul class="sub-menu blank">
            <li><a class="link_name" href="./student.php">Dashboard</a></li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="student/attendance.php">
              <i class="bx bx-font-family"></i>
              <span class="link_name">Attendance</span>
            </a>
            <i class="bx bxs-chevron-down arrow"></i>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="student/attendance.php">Attendance</a></li>
            <li>
              <a href="student/attendance.php">
                <i class="bx bx-list-ol"></i>
                <span>Attendance List</span>
              </a>
            </li>
            <li>
              <a href="student/qrcode.php">
                <i class="bx bx-qr"></i>
                <span>QR Code</span>
              </a>
            </li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="student/module.php">
              <i class="bx bxs-book"></i>
              <span class="link_name">Module</span>
            </a>
            <i class="bx bxs-chevron-down arrow"></i>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="student/module.php">Module</a></li>
            <li>
              <a href="student/module.php">
                <i class="bx bx-list-ol"></i>
                <span>Module List</span>
              </a>
            </li>
            <li>
              <a href="student/grade.php">
                <i class="bx bx-folder"></i>
                <span>Grade</span>
              </a>
            </li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="student/locationlist.php">
              <i class="bx bxs-user"></i>
              <span class="link_name">Map</span>
            </a>
            <i class="bx bxs-chevron-down arrow"></i>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="student/locationlist.php">Map</a></li>
            <li>
              <a href="student/locationlist.php">
                <i class="bx bx-list-ol"></i>
                <span>Location List</span>
              </a>
            </li>
            <li>
              <a href="student/locationactivity.php">
                <i class="bx bxs-map-pin"></i>
                <span>Activity Location</span>
              </a>
            </li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="student/announce.php">
              <i class="bx bx-chat"></i>
              <span class="link_name">Announcement</span>
            </a>
            <i class="bx bxs-chevron-down arrow"></i>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name" href="student/announce.php">Announcement</a></li>
            <li>
              <a href="student/announce.php">
                <i class="bx bx-message-detail"></i>
                <span> Announcement List </span>
              </a>
            </li>
          </ul>
        </li>
        <li>
          <div class="profile-details">
            <div class="profile-content">
              <!--<img src="image/profile.jpg" alt="profileImg">-->
            </div>
            <div class="name-job">
              <div class="profile_name">Prem Shahi</div>
              <div class="job">Web Desginer</div>
            </div>
            <a href="logout.php">
            <i class="bx bx-log-out">
            </i>
            </a>
          </div>
        </li>
      </ul>
    </div>
    <!-- End Side Bar -->
    <section class="home-section">
      <div class="home-content">
        <i class="bx bx-menu"></i>
        <span class="text">
        <?php echo "Welcome, {$user_data['username']}!"; ?>
        </span>
      </div>
      <div class="holder" style="display: grid; place-items: center; background: #333; height: calc(100% - 60px);">
<h1>This is the Student Dash file Page.</h1>
<h1>Under Construction</h1>
</div>
    </section>
    <script src="asset/js/index.js"></script>
  </body>
</html>
