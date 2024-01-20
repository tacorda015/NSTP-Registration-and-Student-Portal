<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicons -->
    <link href="../assets/img/Logo.png" rel="icon" />

    <!-- FOR Event Calendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <!-- For Css -->
    <!-- <link rel="stylesheet" href="../assets/css/main-style.css"> -->
    <link rel="stylesheet" href="../assets/css/mainStyle.css">

    <!-- Boxiocns CDN Link -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../boxicons-2.1.4/css/boxicons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- For bootstrap -->
    <script src="../node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css" />
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../node_modules/bootstrap-icons/font/bootstrap-icons.css">

    <!-- For Ajax/Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->

    <!-- Sweet Alert -->
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">

    <!-- Link for Fontawosome -->
    <script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    
  </head>
  <body>
    <div class="wrapper">
        <!-- Start Side Bar -->
        <div class="sidebar close">
          <div class="logo-details">
            <a href="../admin.php">
              <img src="../assets/img/logo3.png" alt="NSTP Logo">
            </a>
          </div>
          <ul class="nav-links">
            <li>
              <a href="../admin.php">
              <i class="bi bi-columns-gap"></i>
                <span class="link_name">
                Dashboard
                </span>
              </a>
            </li>
            <li>
              <div class="iocn-link">
                <a href="./hero-page.php"><i class="bi bi-house-gear-fill"></i>
                  <span class="link_name">Landing Page</span>
                </a>
                <i class="bx bxs-chevron-down arrow"></i>
              </div>
                <ul class="sub-menu">
                  <li>
                    <a href="./hero-page.php">
                      <i class="bi bi-house-gear-fill"></i>
                      <span>Home Page</span>  
                    </a>
                  </li>
                  <li>
                    <a href="./videos-page.php">
                      <i class="bi bi-film"></i>
                      <span>Videos</span>  
                    </a>
                  </li>
                  <li>
                    <a href="./about-page.php">
                      <i class="bi bi-info-square"></i>
                      <span>About Page</span>
                    </a>
                  </li>
                  <li>
                    <a href="./news-update.php">
                      <i class="bi bi-newspaper"></i>
                      <span>News and Update</span>
                    </a>
                  </li>
                  <li>
                    <a href="./gallery-page.php">
                      <i class="bi bi-images"></i>
                      <span>Gallery Page</span>
                    </a>
                  </li>
                  <li>
                    <a href="./event-calendar.php">
                      <i class="bi bi-calendar-plus"></i>
                      <span>Event Calendar</span>
                    </a>
                  </li>
                  <li>
                    <a href="./team-page.php">
                      <i class="bi bi-people"></i>
                      <span>Team Page</span>
                    </a>
                  </li>
                  <li>
                    <a href="./faQuestion.php">
                      <i class="bi bi-question-square"></i>
                      <span>Frequently Ask Question</span>
                    </a>
                  </li>
                </ul>
            </li>
            <li>
              <div class="iocn-link">
                <a href="./grouplist.php"><i class="bi bi-people-fill"></i>
                  <span class="link_name">Group List</span>
                </a>
              </div>
              <ul class="sub-menu">
                <li>
                  <a href="./grouplist.php">
                    <i class="bi bi-people-fill"></i>
                    <span>Group List</span>
                  </a>
                </li>
              </ul>
            </li>
            <li>
              <div class="iocn-link">
                <a href="./studentlist.php"><i class="bi bi-person-fill"></i>
                  <span class="link_name">Student List</span>
                </a>
              </div>
            </li>
            <li>
              <div class="iocn-link">
                <a href="./teacherlist.php"><i class="bi bi-person-fill"></i>
                  <span class="link_name">Advisers</span>
                </a>
              </div>
            </li>
            <li>
              <div class="iocn-link">
                <a href="./trainerlist.php"><i class="bi bi-person-fill"></i>
                  <span class="link_name">Training Staff</span>
                </a>
              </div>
            </li>
            <!-- <li>
              <div class="iocn-link">
                <a href="./enrollmentform.php"><i class="bx bxs-user"></i>
                  <span class="link_name">Enrollment Form</span>
                </a>
              </div>
              <ul class="sub-menu">
                <li><a class="link_name" href="./enrollmentform.php">Enrollment Form</a></li>
              </ul>
            </li> -->
            <li>
              <div class="iocn-link">
                <a href="./view-grade.php"><i class="bi bi-award"></i>
                  <span class="link_name">Student Grade</span>
                </a>
              </div>
            </li>
            <li>
              <div class="iocn-link">
                <a href="./import.php">
                  <i class="bi bi-file-earmark-arrow-up"></i>
                  <span class="link_name">Import files</span>
                </a>
              </div>
            </li>
            <li class="admin-sidebar">
              <div class="iocn-link">
                <a href="./announcement.php">
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
                <li><a href="./announcement.php"><i class="bx bxs-bell"></i>Announcement</a></li>
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
        <!-- End Side Bar -->
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
                    <?php echo "<img class='profilepic' src='{$picture_data['picture']}' alt='Profile Picture'>"; ?>
                  </div>
                </div>
              </div>
            <div class="menu">
              <ul>
                <li>
                  <a href="../admin/profile.php"><i class='bx bxs-user-rectangle'></i>Profile</a>
                </li>
                <li>
                  <a href="../logout.php"><i class='bx bx-log-out'></i>Logout</a>
                </li>
              </ul>
            </div>
          </div>