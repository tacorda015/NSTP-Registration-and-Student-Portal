<?php
session_start();
// include('includeslanding/header.php');
include('connection.php');
$con = connection();

if (isset($_SESSION['user_id'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>NSTP Portal</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- FOR Event Calendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <!-- Favicons -->
    <link href="assets/img/Logo.png" rel="icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet" />
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- Boxiocns CDN Link -->
    <linkhref="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"rel="stylesheet" />
    <link rel="stylesheet" href="../boxicons-2.1.4/css/boxicons.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- For bootstrap -->
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css" />
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- End bootstrap -->
  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <style>
      /* .gdesc-inner{
        position: relative;
      } */
      .gdesc-inner h6{
        position: absolute;
        top: 10px;
        right: 10px;
      }
      .glightbox-clean .gdesc-inner{
        position: relative;
        padding: 30px 20px;
      }
      .carousel-image {
        width: 80vw; /* Adjust the width as per your needs */
        height: 30vh; /* Adjust the height as per your needs */
        object-fit: scale-down;
      }
      .modal-backdrop.fade.show{
        display: none;
      }

      /* FOR CALENDAR */
      .fc-scroller.fc-scroller-liquid-absolute::-webkit-scrollbar, .fc-scroller::-webkit-scrollbar{
        display: none;
      }
      @media screen and (max-width: 991px) and (min-width: 768px){
          .team-container .table-container {
              max-height: 465px !important;
          }
      }
      @media (max-width: 550px) {
          .fc .fc-toolbar{
              flex-direction: column;
              gap: .25rem;
          }
          .fc-view-harness.fc-view-harness-active{
              height: 430px !important;
          }
          .fc .fc-toolbar.fc-header-toolbar{
              margin-bottom: 1rem !important;
          }
      }
      .fc-view-harness.fc-view-harness-active{
          height: 450px !important;
      }
    </style>
  </head>

  <body>
    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top">
      <div class="container d-flex align-items-center">
        <h1 class="logo me-auto">
          <img src="./assets/img/Logo3.png" alt="">
          <a href="index.php" style="color: black">NSTP Portal</a>
        </h1>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.php" class="logo me-auto"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

        <nav id="navbar" class="navbar">
          <ul>
            <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
            <li><a class="nav-link scrollto" href="#about">About</a></li>
            <li><a class="nav-link scrollto" href="#portfolio">Gallery</a> </li>
            <li><a class="nav-link scrollto" href="#team">Developer Team</a></li>
            <!-- <li><a class="getstarted " data-bs-toggle="modal" data-bs-target="#enrollmodal" >Enroll</a > </li>
            <li><a class="getstarted " data-bs-toggle="modal" data-bs-target="#loginModal" >Log In</a > </li> -->
            <li><a class="nav-link scrollto" href="./portal.php">Portal</a></li>
          </ul>
          <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>
        <!-- .navbar -->
      </div>
    </header>
    <!-- End Header -->

    
    <!-- ======= Hero Section ======= -->
    <section id="hero" class="d-flex align-items-center">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 d-flex flex-column justify-content-center pt-4 pt-lg-0 order-2 order-lg-1"
            data-aos="fade-up"
            data-aos-delay="200">
            <?php
            $home_query = "SELECT * FROM hometable WHERE home_status = 1";
            $home_result = $con->query($home_query);

            if ($home_result->num_rows > 0) {
                $home_row = $home_result->fetch_assoc();
                $home_content = $home_row['home_content'];
                $home_img = str_replace('../', './', $home_row['home_img']);
                
                echo '<span style="font-size: 1.5rem;">' . $home_content . '</span>';
            } else {
                echo "No published home page found.";
            }
            ?>

            </div>
            <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-in" data-aos-delay="200" style="display: flex; justify-content: center; align-items: center;">
                <!-- <img src="assets/img/hero1.png" class="img-fluid animated" alt="" /> -->
                <?php
                echo '<img src="' . $home_img . '" class="img-fluid animated" alt="" />';
                ?>
            </div>
        </div>
      </div>
    </section>
    <!-- End Hero -->
    
    <main id="main">
      <!-- ======= Clients Section ======= -->
      <section id="about" class="clients section-bg"> 
        <div class="container">
          <div class="section-title">
            <h2>About</h2>
          </div>
          <div class="row clients-container" data-aos="zoom-in">
            <div class="clients-content w-50 col-lg-2 col-md-4 col-6 d-flex align-items-center">
              <?php
              $aboutrotc_query = "SELECT * FROM abouttable WHERE about_status = 1 AND about_component = 'ROTC'";
              $aboutrotc_result = $con->query($aboutrotc_query);
  
              if ($aboutrotc_result->num_rows > 0) {
                  $about_row = $aboutrotc_result->fetch_assoc();
                  $about_content = $about_row['about_content'];
                  $aboutrotc_img = str_replace('../', './', $about_row['about_img']);

                  echo '<img src="' . $aboutrotc_img . '" class="img-fluid animated" alt="" />';
                  echo '<span style="font-size: 1.2rem;">' . $about_content . '</span>';
              } else {
                  echo "No published about page found.";
              }
              ?>
              <!-- <img
                src="assets/img/clients/rotcflag.png"
                class="img-fluid"alt="" />
              <span><strong>Reserve Officer Training Corps (ROTC): </strong>This component focuses on military training and instilling discipline, leadership, and patriotism among students. It involves physical training, drills, and lessons on national defense.</span> -->
            </div>

            <div class="clients-content w-50 col-lg-2 col-md-4 col-6 d-flex align-items-center">
            <?php
              $aboutcwts_query = "SELECT * FROM abouttable WHERE about_status = 1 AND about_component = 'CWTS'";
              $aboutcwts_result = $con->query($aboutcwts_query);
  
              if ($aboutcwts_result->num_rows > 0) {
                  $about_row = $aboutcwts_result->fetch_assoc();
                  $about_content = $about_row['about_content'];
                  $aboutcwts_img = str_replace('../', './', $about_row['about_img']);

                  echo '<img src="' . $aboutcwts_img . '" class="img-fluid animated" alt="" />';
                  echo '<span style="font-size: 1.2rem;">' . $about_content . '</span>';
              } else {
                  echo "No published about page found.";
              }
              ?>
              <!-- <img
                src="assets/img/clients/cwtsbook.png"
                class="img-fluid" alt="" />
              <span><strong>Civic Welfare Training Service (CWTS): </strong>The CWTS component emphasizes community service and engagement. Students involved in CWTS participate in various activities that contribute to community development, such as environmental projects, health programs, and literacy campaigns.</span> -->
            </div>
          </div>
        </div>
      </section>
      <!-- End Cliens Section -->

      <!-- Start of News Updates -->
      <section id="newupdate" class="newsupdate" style="background-color: #effbfc; border-top: 1px solid #c5e2f2">
  <div class="container" data-aos="fade-up" style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 70vh; width: 50vw;">
    <div class="section-title mt-5 pb-0">
      <h2>News and Updates</h2>
    </div>
    <?php
    $newsupdate_query = "SELECT * FROM newsupdatetable";
    $newsupdate_result = $con->query($newsupdate_query);
    
    // Check if there are any rows in the result
    if ($newsupdate_result->num_rows > 0) {
      $firstRow = true;
    ?>
    <div id="carouselExampleCaptions" class="carousel carousel-dark slide" data-bs-ride="carousel" data-bs-interval="300000">
      <div class="carousel-indicators" style="bottom: -3rem;">
        <?php
        $indicatorCount = 0;
        
        // Loop through each row in the result
        while ($newsupdate_data = $newsupdate_result->fetch_assoc()) {
          $activeClass = $indicatorCount === 0 ? 'active' : '';
          echo '<button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="' . $indicatorCount . '" class="' . $activeClass . '" aria-current="' . $activeClass . '" aria-label="Slide ' . ($indicatorCount + 1) . '"></button>';
          $indicatorCount++;
        }
        ?>
      </div>
      <div class="carousel-inner">
        <?php
        // Reset the result pointer to the beginning
        $newsupdate_result->data_seek(0);
        
        // Loop through each row in the result
        while ($newsupdate_data = $newsupdate_result->fetch_assoc()) {
          $srcimg = str_replace('../', './', $newsupdate_data['newsupdate_img']);
          $newstitle = $newsupdate_data['newsupdate_title'];
          $newscontent = htmlspecialchars($newsupdate_data['newsupdate_content']);
          $newsdate = $newsupdate_data['newsupdate_date'];
          
          // Add the active class to the first item
          $activeClass = $firstRow ? 'active' : '';
          $firstRow = false;
          
          // Display the carousel item
          echo '<div class="carousel-item ' . $activeClass . '">';
          echo '<div class="d-flex gap-2 flex-column justify-content-center align-items-center">';
          echo '<img src="' . $srcimg . '" class="d-block carousel-image" alt="...">';
          echo '<div class="content-container">';
          echo '<h5 style="color: #132c3e;">' . $newstitle . '</h5>';
          // echo '<h5 style="color: #132c3e;">' . $newscontent . '</h5>';
          echo '<p style="color: #132c3e;">' . $newsdate . '</p>';
          echo '<button class="read-more-btn btn btn-secondary">Read More</button>';
          echo '</div></div></div>';
        }
        ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
    <?php
    } else {
      echo '<p>No news updates found.</p>';
    }
    ?>
  </div>
</section>

      <!-- End of News Updates -->

      <!-- ======= Portfolio Section ======= -->
      <section id="portfolio" class="portfolio">
        <div class="container" data-aos="fade-up">
          <div class="section-title">
            <h2>Gallery</h2>
          </div>

          <ul
            id="portfolio-flters"
            class="d-flex justify-content-center"
            data-aos="fade-up"
            data-aos-delay="100">
            <li data-filter="*" class="filter-active">All</li>
            <li data-filter=".filter-app">CWTS</li>
            <li data-filter=".filter-card">ROTC</li>
            <!-- <li data-filter=".filter-web">Goal</li> -->
          </ul>

          <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">
          <?php
              $gallery_query = "SELECT * FROM (
                  SELECT *
                  FROM gallerytable
                  WHERE gallery_status = 1 AND gallery_component = 'CWTS'
                  UNION
                  SELECT *
                  FROM gallerytable
                  WHERE gallery_status = 1 AND gallery_component = 'ROTC'
              ) AS combined_gallery
              ORDER BY gallery_id DESC";

              $gallery_result = $con->query($gallery_query);

              if ($gallery_result->num_rows > 0) {
                  while ($gallery_row = $gallery_result->fetch_assoc()) {
                      $gallery_img = str_replace('../', './', $gallery_row['gallery_img']);
                      $galleryComponent = $gallery_row['gallery_component'];
                      $galleryTitle = $gallery_row['gallery_title'];
                      $galleryTime = $gallery_row['gallery_time'];
                      $filterClass = ($galleryComponent == 'CWTS') ? 'filter-card' : 'filter-app';

                      echo '<a href="' . $gallery_img . '" data-gallery="portfolioGallery" class="portfolio-lightbox preview-link" title="<h6> '. htmlspecialchars($galleryTime) . '</h6><h4>' . htmlspecialchars($galleryTitle) . '</h4>">      
                      <div class="col-lg-4 col-md-6 portfolio-item '.$filterClass.'">
                          <div class="portfolio-img">
                            <img src="'. $gallery_img .'" class="img-fluid" alt="" />
                          </div>
                          <div class="portfolio-info">
                            <h4>'.htmlspecialchars($galleryTitle).'</h4>
                            <h4 style="font-size: 0.7em;">Date Posted: '.htmlspecialchars($galleryTime).'</h4>
                            <p>'.$galleryComponent.'</p>
                          </div>
                        </div>
                        </a>';
                  }
              } else {
                  echo "No published pictures found.";
              }
          ?>

            <!-- <div class="col-lg-4 col-md-6 portfolio-item filter-web">
              <div class="portfolio-img">
                <img
                  src="assets/img/portfolio/leadership.jpg"
                  class="img-fluid"
                  alt="" />
              </div>
              <div class="portfolio-info">
                <h4>Leadership</h4>
                <p>NSTP Goal</p>
                <a
                  href="assets/img/portfolio/leadership.jpg"
                  data-gallery="portfolioGallery"
                  class="portfolio-lightbox preview-link"
                  title="Leadership"
                  ><i class="bx bx-plus"></i
                ></a>
              </div>
            </div> -->

          </div>
        </div>
      </section>
      <!-- End Portfolio Section -->

      <!-- Start of News Updates -->
      <section id="newupdate" class="newsupdate" style="background-color: #effbfc; border-top: 1px solid #c5e2f2">
        <div class="container" data-aos="fade-up" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
          <div class="section-title mt-5 pb-0">
            <h2>Event Calendar</h2>
          </div>
          <div class="calendar-container d-flex justify-content-center align-items-center">
              <div id="calendar" style="width: 80vw;"></div>
          </div>
          <!-- Start popup dialog box -->
          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
              <div class="modal-dialog modal-md" role="document" style="margin-top: 50%; transform: translateY(-50%);">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="modalLabel">Event in this Date</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <div class="img-container">
                              <div class="row">
                                  <div class="col-sm-12" id="existingEventsSection"></div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                  </div>
              </div>
          </div>
          <!-- End popup dialog box -->
        </div>
      </section>
      <!-- End of News Updates -->

      <!-- ======= Team Section ======= -->
      <section id="team" class="team section-bg">
        <div class="container" data-aos="fade-up">
          <div class="section-title">
            <h2>Developer Team</h2>
          </div>
          <div class="row">
            <?php
            $team_query = "SELECT * FROM teamtable WHERE team_status = 1";
            $team_result = $con->query($team_query);
            if($team_result->num_rows > 0){
              while ($team_row = $team_result->fetch_assoc()) {
                $team_name = $team_row['team_name'];
                $team_role = $team_row['team_role'];
                $team_content = $team_row['team_content'];
                $team_fb = $team_row['team_fb'];
                $team_twitter = $team_row['team_twitter'];
                $team_instagram = $team_row['team_instagram'];
                $team_status = $team_row['team_status'];
                $team_picture = str_replace('../', './', $team_row['team_picture']);
                if (empty($team_fb) || $team_fb == 'None') {
                  $team_fb = 'https://www.facebook.com/';
                }
                if (empty($team_twitter) || $team_twitter == 'None') {
                  $team_twitter = 'https://twitter.com/login';
                }
                if (empty($team_instagram) || $team_instagram == 'None') {
                  $team_instagram = 'https://www.instagram.com/';
                }
                ?>
                <div class="col-lg-6 my-2" data-aos="zoom-in" data-aos-delay="100">
                <div class="member d-flex align-items-start">
                  <div class="pic">
                    <img src="<?php echo $team_picture; ?>" class="img-fluid" alt="Team Picture" />
                  </div>
                  <div class="member-info">
                    <h4><?php echo $team_name; ?></h4>
                    <span><?php echo $team_role; ?></span>
                    <p><?php echo $team_content;?></p>
                    <div class="social">
                      <a href="<?php echo $team_twitter; ?>" target="_blank"><i class="ri-twitter-fill"></i></a>
                      <a href="<?php echo $team_fb; ?>" target="_blank"><i class="ri-facebook-fill"></i></a>
                      <a href="<?php echo $team_instagram; ?>" target="_blank"><i class="ri-instagram-fill"></i></a>
                    </div>
                  </div>
                </div>
              </div>
              <?php }
            }else{
              echo "No published in Team page found.";
            }
            ?>
          </div>
        </div>
      </section>
      <!-- End Team Section -->
      <?php
      $faq_query = "SELECT faq_id, faq_question, faq_answer FROM faqtable WHERE faq_status = 1";
      $faq_result = mysqli_query($con,$faq_query);

      ?>
      <!-- ======= Frequently Asked Questions Section ======= -->
      <section id="faq" class="faq section-bg">
        <div class="container" data-aos="fade-up">
          <div class="section-title">
            <h2>Frequently Asked Questions</h2>
          </div>

          <div class="faq-list">
            <ul>
              <?php
              
                // Loop throught the database result and displayeach FAQ   items.
                while($row = mysqli_fetch_assoc($faq_result)){
                  $faq_id = $row['faq_id'];
                  $faq_question = $row['faq_question'];
                  $faq_answer = $row['faq_answer'];
              ?>
              <li data-aos="fade-up" data-aos-delay="100">
                <i class="bx bx-help-circle icon-help"></i>
                <a data-bs-toggle="collapse" class="collapse" data-bs-target="#faq-list-<?php echo $faq_id; ?>">
                  <?php echo $faq_question; ?>
                  <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i>
                </a>
                <div id="faq-list-<?php echo $faq_id; ?>" class="collapse <?php if ($faq_status == 'active') echo 'show'; ?>" data-bs-parent=".faq-list">
                  <p>
                    <?php echo $faq_answer; ?>
                  </p>
                </div>
              </li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </section>
      <!-- End Frequently Asked Questions Section -->
    </main>
    <!-- End #main -->


  


    <!-- ======= Footer ======= -->
    <footer id="footer">
      <div class="container footer-bottom clearfix">
        <div class="copyright">
          &copy; Copyright <strong><span>NSTP Portal</span></strong
          >. All Rights Reserved
        </div>
        <div class="credits">
        <p> Designed by: <b>Eduardo Tacorda, Norvine Hermeno, Johm Lawrence Jaril, Sheila Permacio</b></p>
        </div>
      </div>
    </footer>
    <!-- End Footer -->

    <div id="preloader"></div>
    <a
      href="#"
      class="back-to-top d-flex align-items-center justify-content-center"
      ><i class="bi bi-arrow-up-short"></i
    ></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
    <!-- for alert time limit -->
    <script>
    // Set the time limit for the alert box in milliseconds
    const ALERT_TIMEOUT = 5000; // 5 seconds

    // Get a reference to the alert box
    const alertBox = document.getElementById('alert-box');

    // Hide the alert box after the time limit expires
    setTimeout(() => {
      alertBox.classList.remove('show');
    }, ALERT_TIMEOUT);
    </script>
    <script>
  $(document).ready(function () {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          // height: 'auto',
          // aspectRatio: 1.2,
          selectable: true,
          dateClick: function (info) {
              var clickedDate = info.dateStr;

              // Fetch and display existing events for the clicked date or date range
              displayExistingEvents(clickedDate);

              // Open the modal to add a new event
              $('#exampleModal').modal('show');

              // Set the clicked date in the hidden input field
              $('#clickedDate').val(clickedDate);

              // Set the start and end dates in the modal to the clicked date
              $('#event_start_date').val(clickedDate);
              $('#event_end_date').val(clickedDate);

              calendar.unselect(); // Clear the date selection after modal is shown
          },
      });
      calendar.render();

      function displayExistingEvents(date) {
          $.ajax({
              url: 'fetch_events.php',
              dataType: 'json',
              success: function (response) {
                  var result = response.data;
                  var existingEvents = result.filter(function (event) {
                      return date >= event.start && date <= event.end;
                  });

                  if (existingEvents.length > 0) {
                      var existingEventsHTML = '<h6>Events this date:</h6><ul style="margin:0 0 0 20px">';
                      existingEvents.forEach(function (event) {
                          existingEventsHTML += '<li>' + event.title + '</li>';
                      });
                      existingEventsHTML += '</ul>';
                      $('#existingEventsSection').html(existingEventsHTML);
                  } else {
                      $('#existingEventsSection').html('<p>No Events for this date.</p>');
                  }
              },
              error: function (xhr, status, error) {
                  console.log('ajax error = ' + error);
                  console.log(xhr.responseText); // Log the response for debugging purposes
                  alert('An error occurred while fetching events. Please check the console for more details.');
              },
          });
      }

      function display_events() {
          var events = [];
          $.ajax({
              url: 'fetch_events.php',
              dataType: 'json',
              success: function (response) {
                  var result = response.data;
                  $.each(result, function (i, item) {
                      var eventColor = generateRandomColor();
                      events.push({
                          id: result[i].event_id,
                          title: result[i].title,
                          start: result[i].start,
                          end: result[i].end,
                          backgroundColor: eventColor,
                          borderColor: eventColor,
                          textColor: '#ffffff',
                          url: result[i].url,
                      });
                  });
                  calendar.addEventSource(events);
              },
              error: function (xhr, status, error) {
                  console.log('ajax error = ' + error);
                  console.log(xhr.responseText); // Log the response for debugging purposes
                  alert('An error occurred while fetching events. Please check the console for more details.');
              },
          });
      }
      function generateRandomColor() {
          var letters = '0123456789ABCDEF';
          var color = '#';
          for (var i = 0; i < 6; i++) {
              color += letters[Math.floor(Math.random() * 16)];
          }
          return color;
      }

      display_events();
  });
</script>
  </body>
</html>
