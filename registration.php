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
  <title>Registration</title>

  <!-- Favicons -->
  <link href="assets/img/Logo.png" rel="icon" />

  <!-- Link for Fontawosome -->
  <script src="https://kit.fontawesome.com/189d4cd299.js" crossorigin="anonymous"></script>

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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- <link rel="stylesheet" href="./assets/css/main-style.css"> -->
  <link rel="stylesheet" href="./assets/css/mainStyle.css">
<style>
  @media only screen and (min-width: 768px) {
    .classForm{
      padding: 0 .5rem;
    }
  }
</style>
</head>
<body style="background-color: #fff !important;">
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
            <li><a class="nav-link scrollto active" href="index.php#hero">Home</a></li>
            <li><a class="nav-link scrollto" href="index.php#about">About</a></li>
            <li><a class="nav-link scrollto" href="index.php#portfolio">Gallery</a> </li>
            <li><a class="nav-link scrollto" href="index.php#eventCalendar">Event Calendar</a></li>
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
    <div class="container-sm col-md-10 mt-4 pt-2 h-100">
      <h1 class="text-center pb-3">Registration</h1>
      <form id="registration-form" class="classForm row g-3 border shadow rounded py-2 mx-1 needs-validation overflow-auto" novalidate method="POST">

        <div class="form-floating col-md-4 px-1">
          <input type="text" name="firstName" class="form-control" id="floatingInput" placeholder="First Name" required>
          <label for="floatingInput"><i class='bx bx-user'></i> First Name</label>
          <div class="invalid-feedback">
            Please provide your First Name.
          </div>
        </div>

        <div class="form-floating col-md-4 px-1">
          <input type="text" name="middleName" class="form-control" id="floatingInput" placeholder="Middle Name">
          <label for="floatingInput"><i class='bx bx-user'></i> Middle Name</label>
          <div class="invalid-feedback">
            Please provide your Middle Name.
          </div>
        </div>

        <div class="form-floating col-md-4 px-1">
          <input type="text" name="lastName" class="form-control" id="floatingInput" placeholder="Last Name" required>
          <label for="floatingInput"><i class='bx bx-user'></i> Last Name</label>
          <div class="invalid-feedback">
            Please provide your Last Name.
          </div>
        </div>

        <div class="form-floating col-md-6 px-1">
          <input type="text" name="studentEmail" class="form-control" id="floatingInput" placeholder="Student Email" required>
          <label for="floatingInput"><i class='bx bx-envelope'></i> Student Email</label>
          <div class="invalid-feedback">
            Please provide your Email.
          </div>
        </div>

        <div class="form-floating col-md-6 px-1">
          <input type="text" name="studentNumber" class="form-control" id="studentNumber" placeholder="Student Number" required>
          <label for="floatingInput"><i class='bx bxs-user-detail'></i> Student Number</label>
          <div class="invalid-feedback">
            Please provide your Student Number.
          </div>
        </div>

        <div class="form-floating col-md-6 px-1">
          <select id="course" name="studentCourse" class="form-select" aria-label="Student Course" required>
              <option value="" selected disabled hidden>Course</option>
              <?php
                  $course_query = "SELECT course_name FROM coursetable";
                  $course_result = $con->query($course_query);

                  while($row = $course_result->fetch_assoc()){
                  $course_name = $row['course_name'];
                  echo"<option value='$course_name'>$course_name</option>";
                  }
              ?>
            </select>
          <label for="floatingSelect"><i class='bx bx-user-circle'></i> Course</label>
          <div class="invalid-feedback">
            Please provide your Course.
          </div>
        </div>

        <div class="form-floating col-md-3 px-1">
          <select id="year-level" name="studentYearLevel" class="form-select" required>
            <option value="" selected disabled hidden>Year Level</option>
            <option value="First Year">First Year</option>
            <option value="Second Year">Second Year</option>
            <option value="Third Year">Third Year</option>
            <option value="Fourth Year">Fourth Year</option>
          </select>
          <label for="floatingInput"><i class='bx bx-bar-chart-alt'></i> Student Year Level</label>
          <div class="invalid-feedback">
            Please provide your Year Level.
          </div>
        </div>

        <div class="form-floating col-md-3 px-1">
          <select id="studentSection" name="studentSection" class="form-select" required>
            <option value="" selected disabled hidden>Section</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
            <option value="F">F</option>
            <option value="G">G</option>
            <option value="H">H</option>
          </select>
          <label for="floatingInput"><i class="bi bi-stripe"></i> Student Section</label>
          <div class="invalid-feedback">
            Please provide your Section.
          </div>
        </div>

        <div class="form-floating col-md-3 px-1">
          <select id="sex" name="studentGender" class="form-select" required>
            <option value="" selected disabled hidden>Sex</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
          <label for="floatingInput"><i class="bi bi-gender-ambiguous"></i> Student Gender</label>
          <div class="invalid-feedback">
            Please provide your Gender.
          </div>
        </div>

        <div class="form-floating col-md-3 px-1">
          <select id="studentBirthMonth" name="studentBirthMonth" class="form-select" required>
            <option value="" selected disabled hidden>Month</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
          <label for="floatingInput"><i class="bi bi-calendar-month"></i> Birth Month</label>
          <div class="invalid-feedback">
            Please provide your Birth Month.
          </div>
        </div>

        <div class="form-floating col-md-3 px-1">
          <input type="text" placeholder="Day" name="studentBirthDay" id="studentBirthDay" min="1" max="31" class="form-select" required/>
          <label for="floatingInput"><i class="bi bi-calendar-date"></i> Birth Day</label>
          <div class="invalid-feedback">
            Please provide your Birth Day.
          </div>
        </div>

        <div class="form-floating col-md-3 px-1">
          <select id="studentBirthYear" name="studentBirthYear" class="form-select" required>
            <option value="" selected disabled hidden>Year</option>
            <?php
                $currentYear = date("Y");
                $newcurrentYear = $currentYear - 10;
                for ($year = $newcurrentYear; $year >= $newcurrentYear - 30; $year--) {
                echo "<option value='".$year."'>".$year."</option>";
                }
            ?>
          </select>
          <label for="floatingInput"><i class="bi bi-calendar3-week"></i> Birth Year</label>
          <div class="invalid-feedback">
            Please provide your Birth Year.
          </div>
        </div>

        <?php
          $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
          $schoolyear_result = $con->query($schoolyear_query);
          $schoolyear_data = $schoolyear_result->fetch_assoc();
          if($schoolyear_data){
            $schoolyear_id = $schoolyear_data['schoolyear_id'];
            $semester_id = $schoolyear_data['semester_id'];

            $countEnrolled = "SELECT COUNT(*) AS enrolledcount FROM enrolledstudent WHERE schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $countEnrolled_result = $con->query($countEnrolled);
            $countEnrolled_data = $countEnrolled_result->fetch_assoc();
            $enrolledcount = $countEnrolled_data['enrolledcount'];
            $sixtyPercentEnrolled = 0.6 * $enrolledcount;
            // $sixtyPercentEnrolled = 0.02 * $enrolledcount;

            $cwtsflag = false; // this is will be the trigger if the 60% are meet

            $cwtscount = "SELECT COUNT(*) AS cwtsregisteredcount FROM useraccount WHERE component_name = 'CWTS' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
            $cwtscount_result = $con->query($cwtscount);
            $cwtscount_data = $cwtscount_result->fetch_assoc();
            $cwtsregisteredcount = $cwtscount_data['cwtsregisteredcount'];

            echo'<input type="hidden" name="sixtyPercentEnrolled" id="sixtyPercentEnrolled" value="'. $sixtyPercentEnrolled .'"/>';
            echo'<input type="hidden" name="cwtsregisteredcount" id="cwtsregisteredcount" value="'. $cwtsregisteredcount .'"/>';

            echo"<script>console.log($cwtsregisteredcount);</script>";
            echo"<script>console.log($sixtyPercentEnrolled);</script>";
            // Check if $cwtsregisteredcount is greater than or equal to $sixtyPercentEnrolled
            if ($cwtsregisteredcount >= $sixtyPercentEnrolled) {
              $cwtsflag = true;
              echo'<input type="hidden" id="cwtsflag" value="'. $cwtsflag .'"/>
                    <div class="form-floating col-md-6 px-1">
                      <select id="studentComponent" name="studentComponent" class="form-select" required>
                        <option value="" selected disabled hidden>Choose Component</option>
                        <option value="1">ROTC</option>
                      </select>
                      <label for="floatingInput"><i class="bi bi-c-circle"></i> Student Component</label>
                      <div class="invalid-feedback">
                        Please provide your Component.
                      </div>
                    </div>';
            } else {
            echo '<div class="form-floating col-md-6 px-1">
                    <select id="studentComponent" name="studentComponent" class="form-select" required>
                      <option value="" selected disabled hidden>Choose Component</option>
                      <option value="1">ROTC</option>
                      <option value="2">CWTS</option>
                    </select>
                    <label for="floatingInput"><i class="bi bi-c-circle"></i> Student Component</label>
                    <div class="invalid-feedback">
                      Please provide your Component.
                    </div>
                  </div>';
            }
          }
        ?>

        <div class="form-floating col-md-6 px-1">
          <input type="text" name="studentContactNumber" class="form-control" id="studentContactNumber" required placeholder="Student Contact Number">
          <label for="floatingInput"><i class="bi bi-telephone"></i> Student Contact Number</label>
          <div class="invalid-feedback">
            Please provide your Contact Number.
          </div>
        </div>

        <div class="form-floating col-md-4 px-1">
          <input type="text" name="studentStreet" class="form-control" id="floatingInput" required placeholder="Street/Baranggay">
          <label for="floatingInput"><i class="bi bi-house-door"></i> Street/Baranggay</label>
          <div class="invalid-feedback">
            Please provide your Baranggay.
          </div>
        </div>

        <div class="form-floating col-md-4 px-1">
          <input type="text" name="studentCity" class="form-control" id="floatingInput" required placeholder="City/Municipality">
          <label for="floatingInput"><i class="bi bi-house-door"></i> City/Municipality</label>
          <div class="invalid-feedback">
            Please provide your City.
          </div>
        </div>

        <div class="form-floating col-md-4 px-1">
          <input type="text" name="studentProvince" class="form-control" id="floatingInput" required placeholder="Province">
          <label for="floatingInput"><i class="bi bi-house-door"></i> Province</label>
          <div class="invalid-feedback">
            Please provide your Province.
          </div>
        </div>
        
        <div class="form-floating col-md-6 px-1">
          <input type="password" name="studentPassword" class="form-control" id="setPassword" required placeholder="Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$">
          <label for="floatingInput"><i class="bi bi-shield-lock"></i> Password</label>
          <div class="invalid-feedback" id="setPassword-error">
            Please provide a valid Password with at least 8 characters, one uppercase letter, one lowercase letter, and one special character.
          </div>
        </div>
        
        <div class="form-floating col-md-6 px-1">
          <input type="password" name="studentConfirmPassword" class="form-control" id="confirmPassword" required placeholder="Confirm Password">
          <label for="floatingInput"><i class="bi bi-shield-lock-fill"></i> Confirm Password</label>
          <div class="invalid-feedback" id="confirmPassword-error">
            Please provide your Confirm Password.
          </div>
        </div>
        
        <div class="col-12 d-flex justify-content-center align-items-center gap-3">
          <button type="submit" class="btn btn-primary">Register</button>
        </div>
        <div class="col-12 d-flex justify-content-end">
          <a href="./portal.php">Already Register? Click Here to Sign-In</a>
        </div>
      </form>
    </div>
  </div>
  <script>
  // Function to send the form data via AJAX
  function submitForm() {
    // Serialize the form data
    const formData = $('#registration-form').serialize();

    $.ajax({
      type: 'POST',
      url: 'registerQuery.php', // Replace with the path to your PHP script
      // dataType: 'json',
      data: formData,
      success: function (response) {
        if (response.status === 'success') {
          // Form submission was successful
          Swal.fire({
              icon: 'success',
              title: response.title,
              text: response.message,
          }).then((result) => {
              // Reload the page after the user clicks "OK"
              if (result.isConfirmed || result.isDismissed) {
                  location.reload();
              }
          });
      }else if (response.status === 'error') {
          // Form submission encountered an error
          Swal.fire({
            icon: 'error',
            title: response.title,
            text: response.message, // Display the error message from the server
          });
        }
      },
      error: function (xhr, status, error) {
        // Handle other errors
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred while submitting the form.',
        });
        console.error(error);
      }
    });
  }

  // Add a submit event listener to the form
  $('#registration-form').submit(function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Check if the form is valid according to Bootstrap validation
    if (this.checkValidity()) {
      // If the form is valid, submit it via AJAX
      submitForm();
    } else {
      // If the form is not valid, show the validation messages
      $(this).addClass('was-validated');
    }
  });
</script>
<script>
  // Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})();

const studentNumberInput = document.getElementById('studentNumber');
studentNumberInput.addEventListener('input', function (event) {
  const inputValue = event.target.value;
  // Remove any non-digit characters from the input value
  const sanitizedValue = inputValue.replace(/\D/g, '');
  event.target.value = sanitizedValue;
});

const studentBirthDayInput = document.getElementById('studentBirthDay');
studentBirthDayInput.addEventListener('input', function (event) {
  const inputValue = event.target.value;
  // Remove any non-digit characters from the input value
  const sanitizedValue = inputValue.replace(/\D/g, '');
  event.target.value = sanitizedValue;
});

const studentContactNumberInput = document.getElementById('studentContactNumber');
studentContactNumberInput.addEventListener('input', function (event) {
  const inputValue = event.target.value;
  if (inputValue.length === 1 && !/[0-9+]/.test(inputValue)) {
    // If the first character is neither a plus sign nor a number, clear the input value
    event.target.value = '';
  } else if (inputValue.length > 1 && !/^[+0-9]*$/.test(inputValue)) {
    // If there are characters other than plus sign and numbers, remove them
    event.target.value = inputValue.replace(/[^+0-9]/g, '');
  }
});

// Compare password and confirm password
const passwordInput = document.getElementById('setPassword');
const confirmPasswordInput = document.getElementById('confirmPassword');
const confirmPasswordError = document.getElementById('confirmPassword-error');

confirmPasswordInput.addEventListener('input', function () {
  if (passwordInput.value !== confirmPasswordInput.value) {
    confirmPasswordInput.setCustomValidity("Passwords do not match");
    confirmPasswordError.textContent = "Passwords do not match";
  } else {
    confirmPasswordInput.setCustomValidity("");
    confirmPasswordError.textContent = "Please provide your Confirm Password.";
  }
});

// Get references to the year level and student component select elements
const yearLevelSelect = document.getElementById('year-level');
const studentComponentSelect = document.getElementById('studentComponent');

// Add a change event listener to the year level select
yearLevelSelect.addEventListener('change', function () {
  const selectedYearLevel = yearLevelSelect.value;
  
  // If the selected year level is "First Year," show both ROTC and CWTS options
  if (selectedYearLevel === 'First Year') {
    studentComponentSelect.innerHTML = `
      <option value="" selected hidden>Choose Component</option>
      <option value="1">ROTC</option>
      <option value="2">CWTS</option>
    `;
  } else {
    // For other year levels (2nd, 3rd, 4th), show only ROTC option
    studentComponentSelect.innerHTML = `
      <option value="" selected hidden>Choose Component</option>
      <option value="1">ROTC</option>
    `;
  }
});
</script>
<script src="assets/js/main.js"></script>
</body>
</html>