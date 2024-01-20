<?php
include('../connection.php');
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
if($role_data['role_name'] == 'Student') 
{ 
  header('Location: student.php'); 
}
elseif ($role_data['role_name'] == 'Teacher') 
{ 
  header('Location: teacher.php');
} 

// Calling the sidebar
include_once('adminsidebar.php');
?>

      <!-- Start Form -->
      <div class="content-container">
        <div class="container">
          <header>Registration</header>

          <form action="#">
            <div class="form first">
              <div class="details personal">
                <span class="title">Personal Details</span>

                <div class="fields">
                  <div class="input-field">
                    <label>First Name</label>
                    <input
                      type="text"
                      placeholder="Enter first name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Middle Name</label>
                    <input
                      type="text"
                      placeholder="Enter middle name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Last Name</label>
                    <input type="text" placeholder="Enter last name" required />
                  </div>

                  <div class="input-field">
                    <label>Component</label>
                    <select required>
                      <option disabled selected>Select Component</option>
                      <option>ROTC</option>
                      <option>CWTS</option>
                    </select>
                  </div>

                  <div class="input-field">
                    <label>Course</label>
                    <input
                      type="text"
                      placeholder="Enter your course"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Section/Year</label>
                    <input
                      type="text"
                      placeholder="Enter your section/year"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Student Number</label>
                    <input
                      type="number"
                      placeholder="Enter your student number"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Date of Birth</label>
                    <input
                      type="date"
                      placeholder="Enter birth date"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Age</label>
                    <input
                      type="number"
                      placeholder="Enter your age"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Email</label>
                    <input
                      type="text"
                      placeholder="Enter your email"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Status</label>
                    <select required>
                      <option disabled selected>Select Status</option>
                      <option>Single</option>
                      <option>Married</option>
                      <option>Widowed</option>
                    </select>
                  </div>

                  <div class="input-field">
                    <label>Mobile Number</label>
                    <input
                      type="number"
                      placeholder="Enter mobile number"
                      required />
                  </div>
                </div>
              </div>

              <div class="details education">
                <span class="title">Educational Details</span>

                <div class="fields">
                  <div class="input-field">
                    <label>Elementary Level</label>
                    <input
                      type="text"
                      placeholder="Enter school name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Year Graduated</label>
                    <input
                      type="number"
                      placeholder="Enter year graduated"
                      required />
                  </div>

                  <div class="input-field">
                    <label>School Type</label>
                    <select required>
                      <option disabled selected>Select School Type</option>
                      <option>Public</option>
                      <option>Private</option>
                    </select>
                  </div>

                  <div class="input-field">
                    <label>Junior Level</label>
                    <input
                      type="text"
                      placeholder="Enter school name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Year Graduated</label>
                    <input
                      type="number"
                      placeholder="Enter year graduated"
                      required />
                  </div>

                  <div class="input-field">
                    <label>School Type</label>
                    <select required>
                      <option disabled selected>Select School Type</option>
                      <option>Public</option>
                      <option>Private</option>
                    </select>
                  </div>

                  <div class="input-field">
                    <label>Senior Level</label>
                    <input
                      type="text"
                      placeholder="Enter school name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Year Graduated</label>
                    <input
                      type="number"
                      placeholder="Enter year graduated"
                      required />
                  </div>

                  <div class="input-field">
                    <label>School Type</label>
                    <select required>
                      <option disabled selected>Select School Type</option>
                      <option>Public</option>
                      <option>Private</option>
                    </select>
                  </div>
                </div>
              </div>
              <button class="nextBtn">
                <span class="btnText">Next</span>
                <i class="uil uil-navigator"></i>
              </button>
            </div>

            <div class="form second">
              <div class="details address">
                <span class="title">Address Details</span>

                <div class="fields">
                  <div class="input-field">
                    <label>Address Type</label>
                    <input
                      type="text"
                      placeholder="Permanent or Temporary"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Nationality</label>
                    <input
                      type="text"
                      placeholder="Enter nationality"
                      required />
                  </div>

                  <div class="input-field">
                    <label>State</label>
                    <input
                      type="text"
                      placeholder="Enter your state"
                      required />
                  </div>

                  <div class="input-field">
                    <label>District</label>
                    <input
                      type="text"
                      placeholder="Enter your district"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Block Number</label>
                    <input
                      type="number"
                      placeholder="Enter block number"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Ward Number</label>
                    <input
                      type="number"
                      placeholder="Enter ward number"
                      required />
                  </div>
                </div>
              </div>

              <div class="details family">
                <span class="title">Family Details</span>

                <div class="fields">
                  <div class="input-field">
                    <label>Father Name</label>
                    <input
                      type="text"
                      placeholder="Enter father name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Mother Name</label>
                    <input
                      type="text"
                      placeholder="Enter mother name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Grandfather</label>
                    <input
                      type="text"
                      placeholder="Enter grandfther name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Spouse Name</label>
                    <input
                      type="text"
                      placeholder="Enter spouse name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Father in Law</label>
                    <input
                      type="text"
                      placeholder="Father in law name"
                      required />
                  </div>

                  <div class="input-field">
                    <label>Mother in Law</label>
                    <input
                      type="text"
                      placeholder="Mother in law name"
                      required />
                  </div>
                </div>

                <div class="buttons">
                  <div class="backBtn">
                    <i class="uil uil-navigator"></i>
                    <span class="btnText">Back</span>
                  </div>

                  <button class="sumbit">
                    <span class="btnText">Submit</span>
                    <i class="uil uil-navigator"></i>
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <!-- End Form -->
    </section>
    <script src="../asset/js/index.js"></script>
    <script src="../asset/js/topbar.js"></script>
  </body>
</html>
