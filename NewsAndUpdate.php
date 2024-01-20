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
    <link href="assets/img/Logo.png" rel="icon"/>

    <!-- Link for Fontawosome -->
    <script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>

    <!-- For customized CSS -->
    <!-- <link href="assets/css/style.css" rel="stylesheet"/> -->
    <link href="assets/css/mainStyle.css" rel="stylesheet"/>

    <!-- For bootstrap -->
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css"/>
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- For AJAX -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <title>News and Updates</title>
    
    <link rel="stylesheet" href="./assets/css/main-style.css">
    <style>
      body{
        background-color: #e5f1f9;
      }
      .content-container{
        position: relative;
        top: 70px;
        margin: 2rem 3rem;
      }
      .title-container{
        width: 100%;
        padding: 1rem 2rem;
        text-align: center;
        margin-bottom: 1rem;
      }
      .title-container span{
        font-size: 3rem;
        font-weight: 600;
      }
      .img-container{
        width: 100%;
        padding: 1rem 6rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: center;
      }
      .img-container img{
        border-radius: 8px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
      }
      .newscontent-container{
        padding: 1rem 4rem;
        margin: 0 3rem 1rem;
      }
      .newscontent-container span{
        font-size: 1rem;
        font-weight: 400;
      }
      .btn-container{
        width: 100%;
        position: relative;
        padding: 1rem 3rem;
        display: flex;
        flex-direction: row;
        margin: 1rem 0 3rem;
        justify-content: space-between;
      }
      .btn-container button{
        border: none;
        background-color: #e5f1f9;
        border-bottom: 1px solid #1d5e87;
        color: #1d5e87;
      }

      .btn-container button:hover{
        color: #58aed8;
        border-bottom: 1px solid #58aed8;
        transform: translateY(-1px);
      }
      @media screen and (max-width: 991px) and (min-width: 768px) {
        .title-container span{
          font-size: 2rem;
        }
      }
      @media screen and (max-width: 767px) {
        .content-container{
          margin: 1.5rem 1rem;
        }
        .title-container span{
          font-size: 1.5rem;
        }
        .img-container{
          padding: .75rem 1.5rem;
        }
      }
    </style>
  </head>
  <body>
    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top">
      <div class="container d-flex align-items-center mx-md-5 mx-2">
        <h1 class="logo me-auto">
          <img src="./assets/img/logo3.png" alt="">
          <a href="index.php" style="color: #fff">NSTP Portal</a>
        </h1>
      </div>
    </header>
    <!-- End Header -->
      <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsId'])) {
            // Retrieve the newsId value
            $newsId = $_POST['newsId'];

            // Perform the database query and retrieve the blog post data
            $newsupdate_query = "SELECT * FROM newsupdatetable WHERE newsupdate_id = $newsId";
            $newsupdate_result = $con->query($newsupdate_query);
            $newsupdate_data = $newsupdate_result->fetch_assoc();

            $newstitle = $newsupdate_data['newsupdate_title'];
            $newscontent = htmlspecialchars($newsupdate_data['newsupdate_content']);
            $newsimg = str_replace('../', './', $newsupdate_data['newsupdate_img']);
            $newsdate = $newsupdate_data['newsupdate_date'];
            ?>

            
            <div class="content-container">
              <div class="title-container">
                  <span><?php echo $newstitle ?></span>
              </div>
              <!-- <div class="date-container">
                <p><?php echo $newsdate ?></p>
              </div> -->
              <div class="img-container">
                <img src="<?php echo $newsimg ?>" alt="<?php echo $newstitle ?>" style="max-width: 90%; height: auto;">
              </div>
              <div class="newscontent-container">
                <span><?php echo nl2br($newscontent) ?></span>
              </div>
            
              <?php
                // Add Next and Previous Buttons
                // Get the next and previous news updates
                $nextQuery = "SELECT * FROM newsupdatetable WHERE newsupdate_id > '$newsId' ORDER BY newsupdate_id ASC LIMIT 1";
                $prevQuery = "SELECT * FROM newsupdatetable WHERE newsupdate_id < '$newsId' ORDER BY newsupdate_id DESC LIMIT 1";

                $nextResult = $con->query($nextQuery);
                $nextData = $nextResult->fetch_assoc();

                $prevResult = $con->query($prevQuery);
                $prevData = $prevResult->fetch_assoc();

                // Get the first and last news updates
                $firstQuery = "SELECT * FROM newsupdatetable ORDER BY newsupdate_id ASC LIMIT 1";
                $lastQuery = "SELECT * FROM newsupdatetable ORDER BY newsupdate_id DESC LIMIT 1";

                $firstResult = $con->query($firstQuery);
                $firstData = $firstResult->fetch_assoc();

                $lastResult = $con->query($lastQuery);
                $lastData = $lastResult->fetch_assoc();
                ?>

              <div class="btn-container">
                <?php
                // Display Previous Button if available
                if ($prevData) {
                  echo '<form action="NewsAndUpdate.php" method="post">';
                  echo '<input type="hidden" name="newsId" value="' . $prevData['newsupdate_id'] . '">';
                  echo '<button type="submit" >'. $prevData['newsupdate_title'] .'</button>';
                  // echo '<button type="submit">Previous</button>';
                  echo '</form>';
                } else {
                  // If no "Previous" update, go to the last news update
                  echo '<form action="NewsAndUpdate.php" method="post">';
                  echo '<input type="hidden" name="newsId" value="' . $lastData['newsupdate_id'] . '">';
                  echo '<button type="submit">'. $lastData['newsupdate_title'] .'</button>';
                  // echo '<button type="submit">Last</button>';
                  echo '</form>';
                }
                // Display Next Button if available
                if ($nextData) {
                  echo '<form action="NewsAndUpdate.php" method="post">';
                  echo '<input type="hidden" name="newsId" value="' . $nextData['newsupdate_id'] . '">';
                  echo '<button type="submit">'. $nextData['newsupdate_title'] .'</button>';
                  // echo '<button type="submit">Next</button>';
                  echo '</form>';
                } else {
                  // If no "Next" update, go to the first news update
                  echo '<form action="NewsAndUpdate.php" method="post">';
                  echo '<input type="hidden" name="newsId" value="' . $firstData['newsupdate_id'] . '">';
                  echo '<button type="submit">'. $firstData['newsupdate_title'] .'</button>';
                  // echo '<button type="submit">First</button>';
                  echo '</form>';
                }
                
                ?>
              </div>
              <div style="opacity: 0;">Bottom Space</div>
            </div>
        <?php
          } else {
            // Handle the case when the newsId parameter is missing in the POST request
            // For example, redirect back to the main page or display an error message.
            header('Location: index.php'); // Change 'index.php' to the appropriate URL.
            exit;
          }
      ?>
  </body>
</html>
