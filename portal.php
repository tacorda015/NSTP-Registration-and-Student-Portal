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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In</title>

  <!-- Favicons -->
  <link href="assets/img/Logo.png" rel="icon" />

  <!-- For bootstrap -->
  <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="./node_modules/bootstrap-icons/font/bootstrap-icons.css" />
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
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- <link rel="stylesheet" href="./assets/css/main-style.css"> -->
  <link rel="stylesheet" href="./assets/css/mainStyle.css">
<style>
  @media only screen and (min-width: 768px) {
    .classForm{
      padding: 0 .5rem;
    }
  }
  .overlay {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.7); /* Adjust the alpha value for the desired transparency */
    }
</style>

</head>
<body style="background-color: #fff !important; height: 100vh;">
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
    ?>
  <div class="col-12 d-flex pt-5">
    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top">
      <div class="container d-flex align-items-center">
        <h1 class="logo me-auto">
          <img src="./assets/img/Logo3.png" alt="">
          <a href="index.php" style="color: #fff;  text-decoration: none;">NSTP Portal</a>
        </h1>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.php" class="logo me-auto"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

        <nav id="navbar" class="navbar">
          <ul>
            <li><a class="nav-link scrollto" href="index.php#hero">Home</a></li>
            <li><a class="nav-link scrollto" href="index.php#about">About</a></li>
            <li><a class="nav-link scrollto" href="index.php#portfolio">Gallery</a> </li>
            <li><a class="nav-link scrollto" href="index.php#eventCalendar">Event Calendar</a></li>
            <!-- <li><a class="getstarted " data-bs-toggle="modal" data-bs-target="#enrollmodal" >Enroll</a > </li>
            <li><a class="getstarted " data-bs-toggle="modal" data-bs-target="#loginModal" >Log In</a > </li> -->
            <li><a class="nav-link scrollto active" href="./portal.php">Portal</a></li>
          </ul>
          <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>
        <!-- .navbar -->
      </div>
    </header>
    <!-- End Header -->
    <div class="container-sm col-md-10 my-4 mt-md-5 py-2 h-100">
        <div class="container border rounded shadow position-relative">
            <div class="row h-100 position-relative rounded-end-3" style="background-image: url(./assets/img/home.jpg); background-repeat: no-repeat; background-size: cover;">
                <!-- Left Side Content -->
                <div class="col-md-6 border bg-white p-4">
                    <div class="row"><span class="fs-3 text-center mt-1 mt-md-3">Sign In</span></div>
                    <form method="post">
                        <div class="row g-3 py-4">
                            <div class="form-floating px-1">
                                <input type="text" name="email" class="form-control" id="emailfloatingInput" placeholder="Email Address" required autocomplete="off" autofocus>
                                <label for="emailfloatingInput"><i class='bx bx-user'></i> Email Address</label>
                                <div class="invalid-feedback">
                                    Please provide your Email Address.
                                </div>
                            </div>
                            <div class="form-floating px-1">
                                <input type="password" name="password" class="form-control" id="passwordfloatingInput" placeholder="Password" required autocomplete="off">
                                <label for="passwordfloatingInput"><i class="bi bi-key-fill"></i> Password</label>
                                <div class="invalid-feedback">
                                    Please provide your Password.
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-center align-items-center gap-3">
                                <button type="submit" name="signin" class="btn btn-primary">Sign In</button>
                            </div>
                            <div class="col-12 d-flex justify-content-center align-items-center gap-3">
                            <a href="./forgot-password.php">Forgot password?</a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right Side Content with Overlay -->
                <div class="col-md-6 border align-items-center d-flex position-relative" style="min-height: 300px;">
                    <!-- Background Overlay -->
                    <div class="overlay rounded-end-3"></div>

                    <div class="row g-3 text-white fs-3" style="z-index: 1;">
                        <span class="text-center">Not yet Registered?</span>
                        <span class="text-center">Register and keep updated to NSTP Activity</span>
                        <div class="w-auto mx-auto">
                            <a href="./registration.php" class="btn btn-primary">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  <script src="assets/js/main.js"></script>
</body>
</html>