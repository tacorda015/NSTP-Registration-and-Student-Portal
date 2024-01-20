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

if ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
} 
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

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
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="./assets/css/mainStyle.css">

    <!-- Link for Fontawosome -->
    <script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>

    <style>
      #donutchart-container {
        display: flex;
        justify-content: center; /* Center-align the container horizontally */
        align-items: center; /* Center-align the container vertically */
        flex-direction: column;
        flex-grow: 1;
        min-width: 250px;
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
      /* #rotcStudentsContainer svg,
      #nstpGroupContainer svg,
      #enrolledVsRegisterContainer svg{
        border-radius: .5rem;
      } */
      /* #rotcStudentsContainer rect,
      #nstpGroupContainer rect,
      #enrolledVsRegisterContainer rect{
        fill: #e5f1f9;
      } */
    </style>
  </head>
  <body>
    <div class="wrapper">
        <!-- Start Side Bar -->
        <div class="sidebar close">
          <div class="logo-details">
            <a href="./admin.php">
              <img src="./assets/img/logo3.png" alt="NSTP Logo">
            </a>
            <!-- <span class="logo_name">NSTP</span> -->
          </div>
          <ul class="nav-links">
            <li>
              <a href="./admin.php"><i class="bi bi-columns-gap"></i>
                <span class="link_name">Dashboard</span>
              </a>
            </li>
            <li>
              <div class="iocn-link">
                <a href="admin/hero-page.php"><i class="bi bi-house-gear-fill"></i>
                  <span class="link_name">Landing Page</span>
                </a>
                <i class="bx bxs-chevron-down arrow"></i>
              </div>
                <ul class="sub-menu">
                  <li>
                    <a href="admin/hero-page.php">
                      <i class="bi bi-house-gear-fill"></i>
                      <span>Home Page</span>  
                    </a>
                  </li>
                  <li>
                    <a href="admin/videos-page.php">
                      <i class="bi bi-film"></i>
                      <span>Videos</span>  
                    </a>
                  </li>
                  <li>
                    <a href="admin/about-page.php">
                      <i class="bi bi-info-square"></i>
                      <span>About Page</span>
                    </a>
                  </li>
                  <li>
                    <a href="admin/news-update.php">
                      <i class="bi bi-newspaper"></i>
                      <span>News and Update</span>
                    </a>
                  </li>
                  <li>
                    <a href="admin/gallery-page.php">
                      <i class="bi bi-images"></i>
                      <span>Gallery Page</span>
                    </a>
                  </li>
                  <li>
                    <a href="admin/event-calendar.php">
                      <i class="bi bi-calendar-plus"></i>
                      <span>Event Calendar</span>
                    </a>
                  </li>
                  <li>
                    <a href="admin/team-page.php">
                      <i class="bi bi-people"></i>
                      <span>Team Page</span>
                    </a>
                  </li>
                  <li>
                    <a href="admin/faQuestion.php">
                      <i class="bi bi-question-square"></i>
                      <span>Frequently Ask Question</span>
                    </a>
                  </li>
                </ul>
            </li>
            <li>
              <div class="iocn-link">
                <a href="admin/grouplist.php"><i class="bi bi-people-fill"></i>
                  <span class="link_name">Group List</span>
                </a>
              </div>
            </li>
            <li>
              <div class="iocn-link">
                <a href="admin/studentlist.php"><i class="bi bi-person-fill"></i>
                  <span class="link_name">Student List</span>
                </a>
              </div>
            </li>
            <li>
              <div class="iocn-link">
                <a href="admin/teacherlist.php"><i class="bi bi-person-fill"></i>
                  <span class="link_name">Advisers</span>
                </a>
              </div>
            </li>
            <li>
              <div class="iocn-link">
                <a href="admin/trainerlist.php"><i class="bi bi-person-fill"></i>
                  <span class="link_name">Training Staff</span>
                </a>
              </div>
            </li>
            <!-- <li>
              <div class="iocn-link">
                <a href="admin/enrollmentform.php"><i class="bx bxs-user"></i>
                  <span class="link_name">Enrollment Form</span>
                </a>
              </div>
              <ul class="sub-menu">
                <li><a class="link_name" href="admin/enrollmentform.php">Enrollment Form</a></li>
              </ul>
            </li> -->
            <li>
              <div class="iocn-link">
                <a href="admin/view-grade.php"><i class="bi bi-award"></i>
                  <span class="link_name">Student Grade</span>
                </a>
              </div>
            </li>
            <li>
              <div class="iocn-link">
                <a href="admin/import.php"><i class="bi bi-file-earmark-arrow-up"></i>
                  <span class="link_name">Import files</span>
                </a>
              </div>
            </li>
            <li class="admin-sidebar">
              <div class="iocn-link">
                <a href="admin/announcement.php">
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
              <ul class="sub-menu">
                <li><a href="admin/announcement.php"><i class="bx bxs-bell"></i>Announcement</a></li>
              </ul>
            </li>

            <li style="visibility: hidden; margin-top: 30px;">
              <div class="iocn-link">
                <a href="./faQuestion.php"><i class="fa-solid fa-circle-question"></i>
                  <span class="link_name">Frequently Ask Question</span>
                </a>
              </div>
              <ul class="sub-menu">
                <li><a class="link_name" href="./faQuestion.php">Frequently Ask Question</a></li>
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
                ?>
                <?php echo "<span>" . $picture_data['surname'] . "</span>";?> 
                <div class="pro-file">
                  <div class="imgPro">
                    <img class='profilepic' src='./admin/<?php echo $picture_data['picture']; ?>' alt='Profile Picture'>
                  </div>
                </div>
          </div>
          <div class="menu">
            <ul>
              <li>
                <a href="./admin/profile.php"><i class='bx bxs-user-rectangle'></i>Profile</a>
              </li>
              <li>
                <a href="logout.php"><i class='bx bx-log-out'></i>Logout</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="home-main-container">
          <?php
            $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
            $schoolyear_result = $con->query($schoolyear_query);
            if($schoolyear_result->num_rows > 0){
            $schoolyear_data = $schoolyear_result->fetch_assoc();
            if($schoolyear_data){
              $schoolyear_id = $schoolyear_data['schoolyear_id'];
              $semester_id = $schoolyear_data['semester_id'];

              $check_group_incharge = "SELECT COUNT(*) AS incharge_count FROM grouptable WHERE incharge_person IS NULL AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
              $check_group_incharge_result = $con->query($check_group_incharge);
              $check_group_incharge_data = $check_group_incharge_result->fetch_assoc();
              
              if($check_group_incharge_data){
                $incharge_count = $check_group_incharge_data['incharge_count'];
                if($incharge_count > 0){
                  echo'
                    <div class="currentSchoolyear">
                      <div class="alert alert-light w-100 d-flex align-items-center border rounded shadow" role="alert">
                      Group lacks assigned person. <a href="admin/grouplist.php" class="alert-link">Click here.</a>
                      </div>
                    </div>
                  ';
                }
              }
              ?>
              <div class="currentSchoolyear">
                <div class="schoolYear-content d-flex border rounded shadow align-items-center">
                  <i class='bx bx-calendar'></i>
                  <span style="font-weight: 500;">School Year <?php echo  $schoolyear_data['schoolyear'] . " - " .$schoolyear_data['schoolyear'] + 1 ?> | <?php $semester_name = ($semester_id == 1) ? "First Semester" : "Second Semester";
                  echo $semester_name;
                  ?></span>
                </div>
              </div>
              <?php
            }else{
              echo"<h1 style='text-align: center; margin-top: 1rem;'>No School Year Yet</h1>";
            }
          
          ?>
          <div class="cardContainer">
          <?php

          if($schoolyear_data){
            $sql = "SELECT COUNT(*) AS total, SUM(u.component_name='ROTC') AS rotc_count, SUM(u.component_name='CWTS') AS cwts_count 
            FROM useraccount AS u
            INNER JOIN enrolledstudent AS e ON u.student_number = e.student_number AND u.schoolyear_id = e.schoolyear_id AND u.semester_id = e.semester_id
            WHERE u.component_name != '' AND u.role_account_id = 2 AND u.schoolyear_id = $schoolyear_id AND u.semester_id = $semester_id";
    
            // Execute the query
            $result = $con->query($sql);

            // Check if any results were found
            if ($result->num_rows > 0) {
              // Fetch the data for each row
              $row = $result->fetch_assoc();
              // while($row = $result->fetch_assoc()) {
                // Calculate the ROTC and CWTS percentages based on the total components
                $rotc_percent = ($row['total'] > 0) ? round(($row['rotc_count'] / $row['total']) * 100) : 0;
                $cwts_percent = ($row['total'] > 0) ? round(($row['cwts_count'] / $row['total']) * 100) : 0;
            }
            
            // Query to count the number of teachers
            $teacher_query = "SELECT COUNT(*) AS teacher_count
            FROM useraccount
            WHERE role_account_id = 3 AND component_name = 'CWTS' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id
            ";
            $teacher_result = $con->query($teacher_query);
            $teacher_row = $teacher_result->fetch_assoc();

            // Query to count the number of trainers
            $trainer_query = "SELECT COUNT(*) AS trainer_count
            FROM useraccount
            WHERE role_account_id = 3 AND component_name = 'ROTC' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id
            ";
            $trainer_result = $con->query($trainer_query);
            $trainer_row = $trainer_result->fetch_assoc();

            // Query to count ROTC groups
            $sql_rotc = "SELECT COUNT(*) AS rotc_count FROM grouptable WHERE component_id = 1 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $result_rotc = $con->query($sql_rotc);
            $row_rotc = $result_rotc->fetch_assoc();
            $rotcCount = $row_rotc['rotc_count'];
            // Query to count CWTS groups
            $sql_cwts = "SELECT COUNT(*) AS cwts_count FROM grouptable WHERE component_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $result_cwts = $con->query($sql_cwts);
            $row_cwts = $result_cwts->fetch_assoc();
            $cwtsCount = $row_cwts['cwts_count'];

            // Query to count the number of registered students
            $registered_query = "SELECT COUNT(*) AS total_registered FROM enrolledstudent WHERE registration_status = 'Registered' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $registered_result = $con->query($registered_query);
            $registered_row = $registered_result->fetch_assoc();

            // Query to count the total number of enrolled students
            $enrolled_query = "SELECT COUNT(*) AS total_enrolled FROM enrolledstudent WHERE registration_status = 'Not Registered' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $enrolled_result = $con->query($enrolled_query);
            $enrolled_row = $enrolled_result->fetch_assoc();
            ?>


            <div class="cardHolder border rounded shadow" style="height: 400px;">
              <div class="text-center w-100 d-flex flex-column gap-3" id="enrolledVsRegisterContainer">
                <canvas style="max-height: 300px;" id="enrolledVsRegisterChart"></canvas>
                <span class="">Registered and Enrolled in NSTP</span>
              </div>
            </div>
            <div class="cardHolder border rounded shadow" style="height: 400px;">
              <div class="text-center w-100 d-flex flex-column gap-3" id="rotcStudentsContainer">
                <canvas style="max-height: 300px;" id="rotcStudentsChart"></canvas>
                <span class="">CWTS and ROTC Students</span>
              </div>
            </div>
            <div class="cardHolder border rounded shadow" style="height: 400px;">
              <div class="text-center w-100 d-flex flex-column gap-3" id="nstpGroupContainer">
                <canvas style="max-height: 300px;" id="nstpGroupChart"></canvas>
                <span class="">Number of Group in NSTP</span>
              </div>
            </div>
            

            <div class="cardHolder border rounded shadow">
              <div class="cardBase">
                <div class="cardHead">
                  <span>Number of Training Staff in ROTC</span>
                </div>
                <div class="cardBody">
                  <div class="body-first">
                    <span><?php echo $trainer_row['trainer_count']; ?></span><span>Number of Trainer</span><span>in ROTC</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="cardHolder border rounded shadow">
              <div class="cardBase">
                <div class="cardHead">
                  <span>Number of Adviser in CWTS</span>
                </div>
                <div class="cardBody">
                  <div class="body-first">
                    <span><?php echo $teacher_row['teacher_count']; ?></span><span>Number of  Teacher</span><span>in CWTS</span>
                  </div>
                </div>
              </div>
            </div>

          <?php
          }
        }else{
          echo"<h1 style='text-align: center; margin-top: 1rem;'>No School Year Yet</h1>";
        }
          ?>
          </div>
          </div>
        </section>
    </div>
 
    <!-- Load Chart.js library first -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Your custom script -->
<script>
  // Function to create a donut chart
  function createEnrolledVsRegisterChart(data) {
  var ctx = document.getElementById('enrolledVsRegisterChart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      title: {
        display: false
      },
      legend: {
        display: true,
        position: 'bottom'
      }
    }
  });
}

function createRotcStudentsChart(data) {
  var ctx = document.getElementById('rotcStudentsChart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      title: {
        display: false
      },
      legend: {
        display: true,
        position: 'bottom'
      }
    }
  });
}

function createNstpGroupChart(data) {
  var ctx = document.getElementById('nstpGroupChart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      title: {
        display: false
      },
      legend: {
        display: true,
        position: 'bottom'
      }
    }
  });
}


  // Donut chart data for Registered vs Enrolled
var enrolledVsRegisterData = {
  labels: ['Registered', 'Not Registered'],
  datasets: [{
    data: [<?php echo $registered_row['total_registered']; ?>, <?php echo $enrolled_row['total_enrolled']; ?>],
    backgroundColor: ['#FF5733', '#3366FF']
  }]
};

// Donut chart data for CWTS and ROTC Students
var rotcStudentsData = {
  labels: ['ROTC Students', 'CWTS Students'],
  datasets: [{
    data: [<?php echo $row['rotc_count']; ?>, <?php echo $row['total'] - $row['rotc_count']; ?>],
    backgroundColor: ['#FF5733', '#3366FF']
  }]
};

// Donut chart data for Number of Group in NSTP
var nstpGroupData = {
  labels: ['ROTC Groups', 'CWTS Groups'],
  datasets: [{
    data: [<?php echo $rotcCount; ?>, <?php echo $cwtsCount; ?>],
    backgroundColor: ['#FF5733', '#3366FF']
  }]
};

// Create the donut charts
createEnrolledVsRegisterChart(enrolledVsRegisterData);
createRotcStudentsChart(rotcStudentsData);
createNstpGroupChart(nstpGroupData);

</script>

    <script src="asset/js/index.js"></script>
    <script src="./asset/js/topbar.js"></script>
  </body>
</html>
