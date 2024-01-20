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
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = $user_id";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$role_account_id = $useraccount_data['role_account_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('./adminsidebar.php');


if (isset($_POST['group_id'])) {
  $group_id = $_POST['group_id'];

  $_SESSION['group_id'] = $group_id;
  // retrieve group information
  $result = mysqli_query($con, "SELECT * FROM grouptable WHERE group_id = '$group_id'");

   // query succeeded, retrieve row
   $row = mysqli_fetch_assoc($result);

   // FOR ADDING STUDENT
   $component_id = $row['component_id'];
} elseif (isset($_SESSION['group_id'])) {

  // retrieve group information from the session variable
  $group_id = $_SESSION['group_id'];
  $result = mysqli_query($con, "SELECT * FROM grouptable WHERE group_id = '$group_id'");

  // query succeeded, retrieve row
  $row = mysqli_fetch_assoc($result);

   // FOR ADDING STUDENT
   $component_id = $row['component_id'];
}


// Start of Displaying Group Name
$group_id = $_SESSION['group_id'];

$stmt = $con->prepare("SELECT teachertable.teacher_name, trainertable.trainer_name FROM grouptable LEFT JOIN teachertable ON grouptable.group_id = teachertable.group_id LEFT JOIN trainertable ON grouptable.group_id = trainertable.group_id WHERE grouptable.group_id = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
$incharge_data = $result->fetch_assoc();
// End OF Displaying Group Name

?>
        <div class="home-main-container">
            <div class="studentList-container">
                <div class="page-title">
                  <div class="titleContainer">
                    <span class="group_id"><?php echo $row['group_name']; ?></span>
                    <label class="in-charge-label">Group Name</label>
                  </div>
                  <form method="get" enctype="multipart/form-data" action="groupview.php">
                    <div class="search-container">
                      <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                      <button class="btn btn-primary" type="submit"><i class='bx bx-search'></i></button>
                    </div>
                  </form>
                </div>
                <div class="personInchargeContainer">
                  <div class="page-title">
                    <div class="titleContainer">
                    <?php
                        if ($incharge_data['teacher_name'] !== null) {
                          echo "<span>" . $incharge_data['teacher_name'] . "</span><label>Person Incharge</label>";
                        } elseif ($incharge_data['trainer_name'] !== null) {
                          echo "<span>" . $incharge_data['trainer_name'] . "</span><label>Person Incharge</label>";
                        } else {
                          echo "<span>No Person Incharge Assigned.</span>";
                        }
                    ?>
                    </div>
                  </div>
                  <div class="buttonsContainer">
                    <div class="buttonHolder">
                      <a href="./grouplist.php"><button class="btn btn-primary"><i class='bx bx-left-arrow-alt'></i>BACK</button></a>
                    </div>
                  </div>
                </div>
                <div class="buttonsContainer">
                    <div class="buttonHolder">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class='bx bx-user-plus'></i>Add Student
                        </button>
                        <div class="download-button-container">
                            <form action="studentlist_download.php" method="get">
                            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                            <input type="hidden" name="group_name" value="<?php echo $row['group_name']; ?>">
                            <input type="hidden" name="schoolyear_id" value="<?php echo $row['schoolyear_id']; ?>">
                            <button type="submit" name="download" class="btn btn-primary d-flex justify-content-center align-items-center gap-1"><i class="bx bx-download"></i>Student List</button>                             
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                    // Pagination setup
                    $recordsPerPage = 10;
                    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                        $currentPage = intval($_GET['page']);
                    } else {
                        $currentPage = 1;
                    }

                    // Modify the query to include LIMIT and OFFSET clauses for pagination
                    $offset = ($currentPage - 1) * $recordsPerPage;

                    // Fetch data from both tables based on group_id
                    $sql = "SELECT ua.full_name, ua.*
                    FROM useraccount ua
                    INNER JOIN grouptable gt ON ua.group_id = gt.group_id
                    WHERE ua.group_id = '$group_id' AND ua.role_account_id = 2";

                    if(isset($_GET['search']) && !empty($_GET['search'])) {
                      $search = mysqli_real_escape_string($con, $_GET['search']);
                      $sql .= " AND (ua.full_name LIKE '%$search%' OR ua.student_number LIKE '%$search%' OR ua.component_name LIKE '%$search%')";
                    }

                    $sql .= " ORDER BY user_account_id DESC LIMIT $recordsPerPage OFFSET $offset";

                    $result = $con->query($sql);
                    if(mysqli_num_rows($result) > 0) {
                      echo "<div class='tableContainer'>";
                      echo "<table class='table table-sm caption-top'>";
                      echo "<caption>List of Student</caption>";
                      echo "<thead class=\"custom-thead\"><tr><th>Student Name</th><th>Student Number</th><th>Component Name</th><th class='thAction'>Actions</th></tr></thead>";
                      echo "<tbody id='file-table-body'>";

                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td data-label='Full Name'>
                                <form method='post' action='user_data.php'>
                                    <input type='hidden' name='use_account_id' value='".$row['user_account_id']."'>
                                    <input type='hidden' name='groupview' value='groupview'>
                                    <button type='submit' class='clickableCharacter'>
                                        ".$row['full_name']."
                                    </button>
                                </form>
                            </td>";
                        echo "<td data-label='Student Number'>" . $row['student_number'] . "</td>";
                        echo "<td data-label='Component Name'>" . $row['component_name'] . "</td>";
                        echo "<td data-label='Action'>
                                <div class=\"groupButton\">
                                  <button type='button' class='btn btn-danger' onclick='removeStudent(\"" . $row["student_number"] . "\")'>
                                  <i class='bx bx-user-minus'></i>Remove
                                  </button>
                                </div>
                              </td>";
                        echo "</tr>";                
                      }
                        echo "</tbody></table></div>";
                        // Pagination links using Bootstrap
                        echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                        <ul class='pagination justify-content-center'>";

                        // Determine the total number of pages
                        $totalRecordsQuery = "SELECT COUNT(*) as total FROM useraccount 
                        INNER JOIN grouptable ON useraccount.group_id = grouptable.group_id
                        WHERE useraccount.group_id = $group_id AND useraccount.role_account_id = 2";

                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = mysqli_real_escape_string($con, $_GET['search']);
                            $totalRecordsQuery .= " AND (useraccount.full_name LIKE '%$search%' OR useraccount.student_number LIKE '%$search%' OR useraccount.component_name LIKE '%$search%')";
                        }

                        $totalRecordsResult = mysqli_query($con, $totalRecordsQuery);
                        $totalRecordsRow = mysqli_fetch_assoc($totalRecordsResult);
                        $totalRecords = $totalRecordsRow['total'];

                        $totalPages = ceil($totalRecords / $recordsPerPage);

                        // Pagination links
                        echo "<li class='page-item " . ($currentPage == 1 ? 'disabled' : '') . "'>
                        <a class='page-link' href='?page=1" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&laquo;</a>
                        </li>";

                        for ($i = max(1, $currentPage - 2); $i <= min($currentPage + 2, $totalPages); $i++) {
                        echo "<li class='page-item " . ($i == $currentPage ? 'active' : '') . "'>
                            <a class='page-link' href='?page=$i" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>$i</a>
                        </li>";
                        }

                        echo "<li class='page-item " . ($currentPage == $totalPages ? 'disabled' : '') . "'>
                        <a class='page-link' href='?page=$totalPages" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&raquo;</a>
                        </li>
                        </ul>
                        </nav>";
                      }else{
                        echo '<h2 style="text-align:center; margin-top: 3rem;">No Student Found.</h2>';
                    }
                ?>
                  <!-- Start Of Add Student Into Group Modal -->

                <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="addStudentModalLabel">Add Student to Group</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="groupview.php" method="POST"> 
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
                                                   $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                                                   $schoolyear_result = $con->query($schoolyear_query);
                                                   $schoolyear_data = $schoolyear_result->fetch_assoc();
                                                   $schoolyear_id = $schoolyear_data['schoolyear_id'];
                                                   $semester_id = $schoolyear_data['semester_id'];
                                                   
                                                   $studentquery = "SELECT * FROM useraccount WHERE (group_id IS NULL OR group_id = '') AND role_account_id = 2 AND component_name = ";
                                                   if ($component_id == 1) {
                                                       $studentquery .= "'ROTC'";
                                                   } elseif ($component_id == 2) {
                                                       $studentquery .= "'CWTS'";
                                                   }
                                                   
                                                   // Add the condition for schoolyear_id and semester_id
                                                   $studentquery .= " AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
                                                   
                                                   $studentresult = mysqli_query($con, $studentquery);

                                                    if(mysqli_num_rows($studentresult) > 0){
                                                      while($studentrow = mysqli_fetch_array($studentresult)) {
                                                        echo "<tr onclick='toggleRowSelection(this)'>";
                                                        // echo "<td data-label='Select'><input type='checkbox' name='user_account_id[]' value='" . $studentrow['user_account_id'] . "'></td>";
                                                        echo "<td data-label='Select'><input type='checkbox' name='user_account_id[]' value='" . $studentrow['user_account_id'] . "' onclick='toggleCheckbox(event)'></td>";
                                                        echo "<td data-label='Full Name'>" . $studentrow['full_name'] . "</td>";
                                                        echo "<td data-label='Student Number'>" . $studentrow['student_number'] . "</td>";
                                                        echo "</tr>";
                                                      }
                                                    }else{
                                                      echo "<tr><td colspan='3'>No available student</td></tr>";
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

                  <!-- End Of Add Student Into Group Modal -->
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
            window.location = "groupview.php";
          });
        } else {
          Swal.fire("Error", response, "error").then(function() {
            window.location = "groupview.php";
          });
        }
      }
    };
    xhr.send("group_id=" + group_id + "&user_account_ids=" + JSON.stringify(selectedUserIds));
  });
</script>

<script>
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
                  window.location = "groupview.php";
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
    <script src="./js/search.js"></script>
    <script src="../asset/js/topbar.js"></script>
    <script>addSearchFunctionality('search', '.search-icon', 'groupview.php');</script>
  </body>
</html>
