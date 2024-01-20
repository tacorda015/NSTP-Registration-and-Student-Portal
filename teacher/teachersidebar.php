<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- For Css -->
    <!-- <link rel="stylesheet" href="../assets/css/teacher.css"> -->
    <!-- <link rel="stylesheet" href="../assets/css/main-style.css"> -->
    <link rel="stylesheet" href="../assets/css/mainStyle.css">

    <!-- Favicons -->
    <link href="../assets/img/Logo.png" rel="icon" />

    <!-- FOR Event Calendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Boxiocns CDN Link -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../boxicons-2.1.4/css/boxicons.min.css">

    <!-- bootstrap -->
    <link rel="stylesheet" href="../node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css" />

    <!-- For Ajax/Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <!-- Link for Fontawosome -->
    <script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>

    <!-- Sweet Alert -->
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">

    <!-- MapBox -->
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.css" type="text/css" />
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" type="text/css" />
    
    <!-- For QR Code Scanner -->
    <script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>

    <script src="../assets/js/delete_files.js"></script>
  </head>
  <body>
  <div class="wrapper">
    <!-- Start of Side bar -->
    <div class="sidebar close">
      <div class="logo-details">
        <a href="../teacher.php">
          <img src="../assets/img/logo3.png" alt="NSTP Logo">
        </a>
      </div>
      <ul class="nav-links">
        <li>
          <div class="iocn-link">
            <a href="../teacher.php"><i class="bi bi-person-circle"></i>
              <span class="link_name">Profile</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./attendancegroup.php"><i class="bi bi-calendar3"></i>
              <span class="link_name">Attendance</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./scanner.php"><i class="bi bi-qr-code-scan"></i>
              <span class="link_name">QR Scanner</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./studentgroup.php">
               <i class="bi bi-person-fill"></i>
              <span class="link_name">Student</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./inputgrade.php">
              <i class="bi bi-upload"></i>
              <span class="link_name">Upload Grades</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./modulelist.php">
              <i class="bi bi-journals"></i>
              <span class="link_name">Upload Lecture</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./listlocation.php"><i class="bi bi-geo-alt"></i>
              <span class="link_name">Map</span>
            </a>
          </div>
        </li>
        <li>
          <div class="iocn-link">
            <a href="./schedule-page.php"><i class="bi bi-calendar-event"></i>
              <span class="link_name">Schedule</span>
            </a>
          </div>
        </li>
        <li class="teacher-sidebar">
          <div class="iocn-link">
            <a href="./announcelist.php">
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
                $picture_id = $user_data['user_account_id'];
                $picture_query = "SELECT * FROM useraccount WHERE user_account_id = $picture_id";
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
                  <?php echo "<img class='profilepic' src='{$picture_data['picture']}' alt='Profile Picture'>"; ?>
                </div>
              </div>
          </div>
          <div class="menu">
            <ul>
              <li>
                <a href="./teacherprofile.php"><i class="bi bi-person-vcard"></i>Profile</a>
              </li>
              <li>
                <a href="../logout.php"><i class="bi bi-box-arrow-left"></i>Logout</a>
              </li>
          </ul>
        </div>
      </div>