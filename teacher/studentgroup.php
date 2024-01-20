<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}

// get user data from session
$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = {$user_id}";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$role_account_id = $useraccount_data['role_account_id'];
$component_name = $useraccount_data['component_name'];
$student_number = $useraccount_data['student_number'];
$teacher_group_id = $useraccount_data['group_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} 

if ($component_name == 'ROTC') {
    $rotc_query = "SELECT * FROM trainertable WHERE trainer_uniquenumber = {$student_number}";
    $rotc_result = $con->query($rotc_query);

    if ($rotc_result && $rotc_result->num_rows > 0) {
        $incharge_data = $rotc_result->fetch_assoc();
        $group_id = $incharge_data['group_id'];
    }
} elseif ($component_name == 'CWTS') {
    $cwts_query = "SELECT * FROM teachertable WHERE teacher_uniquenumber = {$student_number}";
    $cwts_result = $con->query($cwts_query);

    if ($cwts_result && $cwts_result->num_rows > 0) {
        $incharge_data = $cwts_result->fetch_assoc();
        $group_id = $incharge_data['group_id'];
    }
}

// Calling the sidebar
include_once('./teachersidebar.php');
?>
        <div class="home-main-container">
          <div class="studentList-container">
              <?php
              if (isset($group_id)) {
                  $group_query = "SELECT group_name FROM grouptable WHERE group_id = {$group_id}";
                  $group_result = $con->query($group_query);
                  $group_data = $group_result->fetch_assoc();
                  ?>
                  <div class='page-title'>
                    <div class='titleContainer'>
                      <span class='group_id'><?php echo $group_data['group_name'] ?></span>
                      <label class='in-charge-label'>Group Name</label>
                    </div>
                    <form method="get" enctype="multipart/form-data" action="studentgroup.php">
                      <div class="search-container">
                          <input id="search"  type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>                       
                          <button type="submit"><i class='bx bx-search'></i></button>
                      </div>
                    </form>
                  </div>
                  <div class="buttonsContainer">
                        <?php
                            if($component_name === 'CWTS'){
                              echo '<div class="buttonHolder">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                          <i class="bx bx-user-plus"></i> Add Student
                                        </button>

                                        <a href="studentgroup_download.php?group_id=' . $teacher_group_id . '" class = "btn btn-primary">
                                          <i class="bx bx-download"></i>Export Student List
                                        </a>
                                    </div>';
                            }else{
                                echo '<div class="buttonHolder">
                                        <a href="studentgroup_download.php?group_id=' . $teacher_group_id . '" class = "btn btn-primary">
                                          <i class="bx bx-download"></i>Export Student List
                                        </a>
                                      </div>';
                            }
                        ?>
                  </div>
                  <?php
                  $student_query = "SELECT * FROM useraccount WHERE group_id = $group_id AND role_account_id = 2";

                  if (isset($_GET['search']) && !empty($_GET['search'])) {
                      $search = mysqli_real_escape_string($con, $_GET['search']);
                      $student_query .= " AND (full_name LIKE '%$search%' OR email_address LIKE '%$search%' OR student_number LIKE '%$search%' OR user_status LIKE '%$search%')";
                  }

                  // Pagination variables
                  $recordsPerPage = 10;
                  if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                      $currentPage = intval($_GET['page']);
                  } else {
                      $currentPage = 1;
                  }

                  // Calculate the offset for the current page
                  $offset = ($currentPage - 1) * $recordsPerPage;

                  // Add LIMIT and OFFSET clauses to the query for pagination
                  $student_query .= " LIMIT $recordsPerPage OFFSET $offset";

                  $student_result = $con->query($student_query);

                  if ($student_result) {
                      if ($student_result->num_rows > 0) {
                          echo "<div class='tableContainer'>";
                          echo "<table class='table table-sm caption-top'>";
                          echo "<caption>List of Student</caption>";
                          echo "<thead class=\"custom-thead\"><tr><th>Student name</th><th>Contact Number</th><th>Student number</th><th>Status</th><th class='thAction'>Action</th></tr></thead>";
                          echo "<tbody>";
                          while ($student_data = $student_result->fetch_assoc()) {
                              
                              echo "<tr>";
                                echo "<td data-label='Student Name'>" . $student_data['full_name'] . "</td>";
                                echo "<td data-label='Contact Number'>" . $student_data['contactNumber'] . "</td>";
                                echo "<td data-label='Student Number'>" . $student_data['student_number'] . "</td>";
                                echo "<td data-label='Status'>" . $student_data['user_status'] . "</td>";
                                echo "<td data-label='Action'>
                                <div class='groupButton'>
                                    <form method='post' action='process_student.php'>
                                        <input type='hidden' name='user_account_id' value='" . $student_data['user_account_id'] . "'>
                                        <button type='submit' class='btn btn-primary'>
                                        <i class='bx bx-show-alt'></i>View</button>
                                    </form>
                                    <button type='button' class='btn btn-danger' onclick='removeStudent(\"" . $student_data["student_number"] . "\")'>
                                    <i class='bx bx-user-minus'></i>Remove</button>
                                </div>
                                </td>";
                                echo "</tr>";
                          }
                          echo "</tbody></table></div>";

                          // Get the total number of records for pagination
                          $totalRecordsQuery = "SELECT COUNT(*) AS total FROM useraccount WHERE group_id = $group_id AND role_account_id = 2";
                          if (isset($_GET['search']) && !empty($_GET['search'])) {
                              $search = mysqli_real_escape_string($con, $_GET['search']);
                              $totalRecordsQuery .= " AND (full_name LIKE '%$search%' OR email_address LIKE '%$search%' OR student_number LIKE '%$search%' OR user_status LIKE '%$search%')";
                          }
                          $totalRecordsResult = $con->query($totalRecordsQuery);
                          $totalRecordsData = $totalRecordsResult->fetch_assoc();
                          $totalRecords = $totalRecordsData['total'];

                          // Calculate the total number of pages
                          $totalPages = ceil($totalRecords / $recordsPerPage);

                          // Pagination links using Bootstrap
                          echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                                  <ul class='pagination justify-content-center'>";

                          // Pagination links - Previous
                          $prevPage = $currentPage - 1;
                          echo "<li class='page-item " . ($currentPage == 1 ? 'disabled' : '') . "'>
                                  <a class='page-link' href='?page=$prevPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&laquo; Previous</a>
                              </li>";

                          for ($i = max(1, $currentPage - 2); $i <= min($currentPage + 2, $totalPages); $i++) {
                              echo "<li class='page-item " . ($i == $currentPage ? 'active' : '') . "'>
                                      <a class='page-link' href='?page=$i" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>$i</a>
                                    </li>";
                          }

                          // Pagination links - Next
                          $nextPage = $currentPage + 1;
                          echo "<li class='page-item " . ($currentPage == $totalPages ? 'disabled' : '') . "'>
                                  <a class='page-link' href='?page=$nextPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>Next &raquo;</a>
                              </li>
                          </ul>
                          </nav>";
                      } else {
                        echo "<h2 style='height:50%' class='d-flex justify-content-center align-items-center'>No Student Found.</h2>";
                      }
                  } else {
                      echo "<h2 style='text-align:center;' >No Student Assigned yet.</h2>";
                  }
                  ?>
                  <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="addStudentModalLabel">Add Student to Group</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="studentgroup.php" method="POST"> 
                                  <input type="hidden" name="group_id" value="<?php echo $group_id ?>">
                                      <div class="table-responsive">
                                          <table class="table table-striped">
                                              <thead>
                                                  <tr>
                                                    <th>Select</th>
                                                    <th>Name</th>
                                                    <th>Student Number</th>
                                                  </tr>
                                              </thead>
                                              <tbody>
                                              <?php
                                                $studentquery = "SELECT * FROM useraccount WHERE (group_id IS NULL OR group_id = '') AND role_account_id = 2 AND component_name = 'CWTS'";
                                                $studentresult = mysqli_query($con, $studentquery);

                                                if (mysqli_num_rows($studentresult) > 0) {
                                                    while ($studentrow = mysqli_fetch_array($studentresult)) {
                                                        echo "<tr onclick='toggleRowSelection(this)'>";
                                                        echo "<td data-label='Select'><input type='checkbox' name='user_account_id[]' value='" . $studentrow['user_account_id'] . "' onclick='toggleCheckbox(event)'></td>";
                                                        echo "<td data-label='Full Name'>" . $studentrow['full_name'] . "</td>";
                                                        echo "<td data-label='Student Number'>" . $studentrow['student_number'] . "</td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='3'>No students available</td></tr>";
                                                }
                                                ?>
                                              </tbody>
                                          </table>
                                      </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="addStudentButton" class="btn btn-primary">Add Students</button>
                                  </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                  <?php
              } else {
                  echo "<h2 style='text-align:center;' >No Assigned Group yet.</h2>";
              }
              ?>
          </div>
        </div>
    </section>
  </div>
  <script>
  function toggleRowSelection(row) {
    var checkbox = row.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;

    // Prevent the checkbox's default behavior from being triggered
    // event.stopPropagation();
  }
    function toggleCheckbox(event) {
    // Prevent the row's click event from being triggered
    event.stopPropagation();
    }
  
  document.getElementById("addStudentButton").addEventListener("click", function() {
    var checkboxes = document.getElementsByName("user_account_id[]");
    var selectedUserIds = [];
    for (var i = 0; i < checkboxes.length; i++) {
      if (checkboxes[i].checked) {
        selectedUserIds.push(checkboxes[i].value);
      }
    }
    
    if (selectedUserIds.length === 0) {
      Swal.fire("Error", "Please select at least one student to add to the group.", "error");
      return;
    }
    
    var group_id = "<?php echo $group_id; ?>";
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_groupstudent.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
      if (xhr.readyState == 4 && xhr.status == 200) {
        var response = xhr.responseText;
        if (response === "success") {
          Swal.fire("Success", "Successfully added the student to the group!", "success").then(function() {
            window.location = "studentgroup.php";
          });
        } else {
          Swal.fire("Error", response, "error").then(function() {
            window.location = "studentgroup.php";
          });
        }
      }
    };
    xhr.send("group_id=" + group_id + "&user_account_ids=" + JSON.stringify(selectedUserIds));
  });

  function removeStudent(studentNumber) {
    Swal.fire({
      title: "Confirmation",
      text: "Are you sure you want to remove this student from the group?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Remove",
      cancelButtonText: "Cancel"
    }).then(function(result) {
      if (result.isConfirmed) {
        // Send Ajax request to remove the student
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "remove_groupstudent.php?remove_student=" + studentNumber, true);
        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4) {
            if (xhr.status == 200) {
              var response = xhr.responseText;
              if (response === "success") {
                Swal.fire("Success", "The student was successfully removed from the group!", "success").then(function() {
                  window.location = "studentgroup.php";
                });
              } else {
                Swal.fire("Error", "There was an error removing the student from the group.", "error");
              }
            } else {
              Swal.fire("Error", "There was an error removing the student from the group.", "error");
            }
          }
        };
        xhr.send();
      }
    });
  }
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>