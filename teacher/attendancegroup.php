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
$user_account_id = $user_data['user_account_id'];
$retrieve_query = "SELECT * FROM useraccount WHERE user_account_id = $user_account_id";
$retrieve_result = $con->query($retrieve_query);
$retrieve_data = $retrieve_result->fetch_assoc();

$teacher_group_id = $retrieve_data['group_id'];
$contactNumber = $retrieve_data['contactNumber'];
$role_account_id = $retrieve_data['role_account_id'];

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

date_default_timezone_set('Asia/Manila');
// Calling the sidebar
include_once('./teachersidebar.php');
?>
<div class="home-main-container">
    <div class="studentList-container">
    <?php
    if ($teacher_group_id !== null && $teacher_group_id !== ''){
    ?>
        <div class='page-title'>
            <div class="titleContainer">
                <span class="group-id">Attendance Monitoring</span>
                <?php
                $currentDate = date('Y-m-d');
                $currentTime = date('H:i:s');
                $query = "SELECT * FROM scheduletable WHERE group_id = $teacher_group_id AND schedule_date = '$currentDate'";
                $result = $con->query($query);
                $schedule_date = $result->fetch_assoc();
                if ($schedule_date && $schedule_date['schedule_end'] < $currentTime) {
                    echo "<label class='in-charge-label'>Activity Schedule Already Finished.</label>";
                } elseif($schedule_date){
                    echo "<div>
                            <label style='font-weight:500;'>Schedule Date: </label>
                            <label style='font-weight:400;'>" . $schedule_date['schedule_date'] . "</label>
                        </div>
                        <div>
                            <label style='font-weight:500;'>Time: </label>
                            <label style='font-weight:400;'>" . $schedule_date['schedule_start'] . " - " . $schedule_date['schedule_end'] . "</label>
                        </div> ";
                }else {
                    echo "<label class='in-charge-label'>No schedule today.</label>";
                }
                ?>
            </div>
            <form method="get" enctype="multipart/form-data" action="attendancegroup.php"> 
                <div class="search-container">
                    <input id="search"  type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>
                    <button type="submit"><i class='bx bx-search'></i></button>
                </div>
            </form>
        </div>
        <div class='buttonsContainer'>
          <!-- <div class="buttonHolder">
              <a href="attendancegroup_download.php?group_id=<?php echo $teacher_group_id; ?>" class="download-button btn btn-primary d-flex justify-content-center align-items-center gap-1"><i class="bx bx-download"></i>Export Attendance</a>
          </div> -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <i class="bx bx-download"></i> Export Attendance
        </button>
        <!-- Export Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Export Attendance</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="attendancegroup_download.php" method="get">
                <div class="modal-body">
                    <input type="hidden" name="group_id" value="<?php echo $teacher_group_id; ?>">
                    <div class="form-floating mb-3">
                        <select name="specificData" id="specificData" class="form-select" required>
                            <?php
                                $specificQuery = "SELECT activity_date FROM attendancetable WHERE group_id = '$teacher_group_id' GROUP BY activity_date ORDER BY activity_date DESC";
                                $specificResult = $con->query($specificQuery);
                                if ($specificResult->num_rows > 0) {
                                    while ($datarow = $specificResult->fetch_assoc()) {
                                        echo '<option value="' . $datarow['activity_date'] . '">' . $datarow['activity_date'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="" disabled>No Attendance</option>';
                                }
                            ?>
                        </select>
                        <label for="specificData">Select Activity Date</label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
            </div>
        </div>
        </div>
        </div>
        <?php
        // Retrieve the schedule for today and the given group_id
        $attendance_query = "SELECT group_id FROM scheduletable WHERE group_id = $teacher_group_id";
        $attendance_result = mysqli_query($con, $attendance_query);
        $attendanceResult = mysqli_fetch_assoc($attendance_result);
        if ($attendanceResult){
        ?>
        <div class='tableContainer'>
            <table class='table table-sm caption-top'>
            <caption>List of Attendace</caption>
                <thead class="custom-thead">
                    <tr>
                        <th>Student Name</th>
                        <!-- <th>Date</th> -->
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Type</th>
                        <th class='thAction'>Action</th>
                    </tr>
                </thead>
                <tbody id='attendanceTableBody'>
                   <?php
                   $recordsPerPage = 10;
                   if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                       $currentPage = intval($_GET['page']);
                   } else {
                       $currentPage = 1;
                   }
                   
                   // Calculate the offset for the current page
                   $offset = ($currentPage - 1) * $recordsPerPage;
                   
                   $query = "SELECT * FROM attendancetable WHERE group_id = '$teacher_group_id'";
                   
                   if(isset($_GET['search']) && !empty($_GET['search'])) {
                       $search = mysqli_real_escape_string($con, $_GET['search']);
                       $query .= " AND (student_name LIKE '%$search%' OR student_number LIKE '%$search%' OR activity_date LIKE '%$search%' OR attendance_status LIKE '%$search%')";
                   }
                   
                   // Add LIMIT and OFFSET clauses to the query for pagination
                   $query .="  ORDER BY attendance_id DESC LIMIT $recordsPerPage OFFSET $offset";
                   
                   $result = $con->query($query);
                   
                   if ($result) {
                       $rowCounter = 0;
                       $studentAbsences = array(); // Store absences per student
                       $studentRemark = array();
                       if($result->num_rows > 0){
                           while ($row = $result->fetch_assoc()) {
                               $attendance_id = $row['attendance_id'];
                               $rowStyle = '';
                               if ($row['attendance_status'] === 'Absent') {
                                   $rowStyle .= ($rowCounter % 2 === 0) ? 'background-color: #ffc8c8;' : 'background-color: #ffdcdc;';
                               } elseif ($row['attendance_status'] === 'Late') {
                                   $rowStyle .= ($rowCounter % 2 === 0) ? 'background-color: #ffffcc;' : 'background-color: #ffffe6;';
                               }
                   
                               // Retrieve student name
                               $studentName = $row['student_name'];
                   
                               // Initialize absences for the student if not already set
                               if (!isset($studentAbsences[$studentName])) {
                                   $studentAbsences[$studentName] = 0;
                               }
                               if (!isset($studentRemark[$studentName])) {
                                   $studentRemark[$studentName] = 0;
                               }
                   
                               echo "<tr id='attendance-row-$attendance_id' style='$rowStyle' class='attendance-row' data-announcement-batch='$attendance_id'>";
                               echo "<td data-label='Student Name:'>" . $studentName . "</td>";
                               echo "<td data-label='Activity Date:' style='display: none;'>" . $row['activity_date'] . "</td>";
                               echo "<td data-label='Remark Status:' style='display: none;'>" . $row['remark_status'] . "</td>";
                               // echo "<td data-label='Remark Status:'>" . $row['remark_status'] . "</td>";
                   
                               if ($row['remark_status'] == '4') {
                                   // Increment the count for the student
                                   $studentRemark[$studentName]++;
                                   
                                   if ($studentRemark[$studentName] === 3) {
                                       $update_remark_query = "UPDATE attendancetable SET remark_status = 6 WHERE student_name = '$studentName'";
                                       $con->query($update_remark_query);
                                   }
                               }
                   
                               if ($row['time-in'] === null) {
                                   echo "<td data-label='Time-In:'>--:--</td>";
                               } else {
                                   $timeIn = date('H:i', strtotime($row['time-in']));
                                   echo "<td data-label='Time-In:'>" . $timeIn . "</td>";
                               }
                   
                               if ($row['time-out'] === null) {
                                   echo "<td data-label='Time-Out:'>--:--</td>";
                               } else {
                                   $timeOut = date('H:i', strtotime($row['time-out']));
                                   echo "<td data-label='Time-Out:'>" . $timeOut . "</td>";
                               }
                   
                               if (is_null($row['time-out']) && $row['activity_date'] < $currentDate) {
                                   if ($row['attendance_status'] === "Present" && is_null($row['trigger_remark'])) {
                                       $update_query = "UPDATE attendancetable SET trigger_remark = 0, remark_status = 2, attendance_status = 'No Time-out' WHERE attendance_id = $attendance_id";
                                       $con->query($update_query);
                                       echo "<td data-label='Type:'>No Time-out</td>";
                                   } elseif ($row['attendance_status'] === "Late" && is_null($row['trigger_remark'])) {
                                       $update_query = "UPDATE attendancetable SET trigger_remark = 0, remark_status = 2, attendance_status = 'Late and No Time-out'  WHERE attendance_id = $attendance_id";
                                       $con->query($update_query);
                                       echo "<td data-label='Type:'>Late and No Time-out</td>";
                                   } elseif ($row['attendance_status'] === "Absent") {
                                       $update_query = "UPDATE attendancetable SET trigger_remark = 0 WHERE attendance_id = $attendance_id";
                                       $con->query($update_query);
                                       echo "<td data-label='Type:'>" . $row['attendance_status'] . "</td>";
                   
                                       // Increment absences for the student
                                       $studentAbsences[$studentName]++;
                                       // echo($studentAbsences[$studentName]);
                                       if ($studentAbsences[$studentName] === 3) {
                                           $update_remark_query = "UPDATE attendancetable SET remark_status = 6 WHERE student_name = '$studentName'";
                                           $con->query($update_remark_query);
                                       }
                                   } else {
                                       echo "<td data-label='Type:'>" . $row['attendance_status'] . "</td>";
                                   }
                               } else {
                                   echo "<td data-label='Type:'>" . $row['attendance_status'] . "</td>";
                               }
                   
                               
                               echo "<td data-label='Remark:' style='display: none;'>" . $row['remark'] . "</td>";
                   
                               echo "<td data-label='Action:'>
                                       <div class='groupButton'>
                                           <button class='btn btn-primary' onclick='openViewModal($attendance_id)'>
                                           <i class='bx bx-show-alt'></i>View</button>
                                           
                                           <button class='btn btn-primary update-button' onclick='openUpdateModal($attendance_id)'>
                                           <i class='bx bx-wrench'></i>Update</button>
                                       </div>
                                   </td>";
                               echo "</tr>";
                               $rowCounter++;
                           }
                       }else{
                           echo"<span>No Record Found</span>";
                       }
                   } else {
                       echo "Error: " . $con->error;
                   }
                   ?>
                </tbody>
            </table>
        </div>
        <?php 
            // Get the total number of records for pagination
            $totalRecordsQuery = "SELECT COUNT(*) AS total FROM attendancetable WHERE group_id = '$teacher_group_id'";
            $totalRecordsResult = $con->query($totalRecordsQuery);
            $totalRecordsData = $totalRecordsResult->fetch_assoc();
            $totalRecords = $totalRecordsData['total'];
        
            // Calculate the total number of pages
            $totalPages = ceil($totalRecords / $recordsPerPage);
        
            // Output the pagination links with a unique separator
            echo "<!-- PAGINATION_SPLIT -->";
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
        ?>
        <!-- <div style="opacity: 0; height: 10vh;">space Container</div> -->
        <!-- Details Modal -->
        <div class="modal fade" id="attendancegroup" tabindex="-1" role="dialog" aria-labelledby="attendancegroupLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="attendancegroupLabel">Attendance Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Student Name: </strong> <span id="Student_Name"></span></p>
                        <p><strong>Activity Date: </strong> <span id="Activity_Date"></span></p>
                        <p><strong>Time-In: </strong> <span id="Time_In"></span></p>
                        <p><strong>Time-Out: </strong> <span id="Time_Out"></span></p>
                        <p><strong>Type: </strong> <span id="Status"></span></p>
                        <!-- Add the id attributes for Activity_Date and Remark -->
                        <p><strong>Remark:</strong> <span id="Remark"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Update Modal -->
        <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateForm" method="GET" action="update_attendance.php">
                        <div class="modal-body">
                            <input type="hidden" id="attendanceId" name="attendanceId">
                            <div class="form-group">
                                <label for="studentName">Student Name:</label>
                                <input type="text" class="form-control" id="studentName" name="studentName" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="timeIn">Time In:</label>
                                <input type="time" class="form-control" id="timeIn" name="timeIn">
                            </div>
                            <div class="form-group">
                                <label for="timeOut">Time Out:</label>
                                <input type="time" class="form-control" id="timeOut" name="timeOut">
                            </div>
                            <div class="form-group">
                                <label for="remark_status">Status:</label>
                                <select class="form-control" id="remark_status" name="remark_status">
                                    <?php
                                    $statusQuery = "SELECT remarkstatus_id, remarkstatus_name FROM remarkstatustable";
                                    $statusResult = $con->query($statusQuery);

                                    if ($statusResult && $statusResult->num_rows > 0) {
                                        while ($statusRow = $statusResult->fetch_assoc()) {
                                            $statusId = (int)$statusRow['remarkstatus_id'];
                                            $statusName = $statusRow['remarkstatus_name'];
                                            echo "<option value='$statusId'>$statusName</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks:</label>
                                <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="attendanceupdate">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php
        }else{
          echo "<h2 style='text-align: center;'>No Attendance Yet.</h2>";
        }
    }else{
        echo "<h2 style='text-align: center;'>No Assigned Group yet.</h2>";
    }
    ?>
    </div>
</div>
</section>
</div>
<!-- <script src="./js/search.js"></script> -->
<script>
    function openViewModal(attendanceId) {
    var row = $('#attendance-row-' + attendanceId);
    var studentName = row.find('td[data-label="Student Name:"]').text();
    var timeIn = row.find('td[data-label="Time-In:"]').text();
    var timeOut = row.find('td[data-label="Time-Out:"]').text();
    var status = row.find('td[data-label="Type:"]').text();

    $('#Student_Name').text(studentName);
    $('#Time_In').text(timeIn);
    $('#Time_Out').text(timeOut);
    $('#Status').text(status);

    // Retrieve the activity_data and remark values
    var activityData = row.find('td[data-label="Activity Date:"]').text();
    var remark = row.find('td[data-label="Remark:"]').text();

    // Set the activity_data and remark values in the modal
    var modalBody = $('#attendancegroup').find('.modal-body');
    modalBody.find('#Activity_Date').text(activityData);
    modalBody.find('#Remark').text(remark);

    $('#attendancegroup').modal('show');
}


    function openUpdateModal(attendanceId) {
    var row = $('#attendance-row-' + attendanceId);
    var studentName = row.find('td[data-label="Student Name:"]').text();
    var timeIn = row.find('td[data-label="Time-In:"]').text();
    var timeOut = row.find('td[data-label="Time-Out:"]').text();
    var status = row.find('td[data-label="Type:"]').text();
    var remark = row.find('td[data-label="Remark:"]').text();
    var remark_status = row.find('td[data-label="Remark Status:"]').text();
    // console.log(remark);

    $('#attendanceId').val(attendanceId);
    $('#studentName').val(studentName);
    $('#timeIn').val(timeIn);
    $('#timeOut').val(timeOut);
    // $('#status').val(remark_status);
    $('#remark_status').val(remark_status);
    // $('#Remark').val(remark);
    $('#remark').val(remark);

    $('#updateModal').modal('show');
}

    // Submit the update form
    $('#updateForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var url = $(this).attr('action');

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            success: function(response) {
                // Handle the response here
                Swal.fire({
                  icon: 'success',
                  title: 'Update Successful',
                  text: 'Attendance has been updated.',
                  showConfirmButton: false,
                  timer: 1500
                }).then(function () {
                    window.location.href = 'attendancegroup.php';
                });
                // Close the update modal
                $('#updateModal').modal('hide');
            },
            error: function(xhr, status, error) {
                // Handle the error here
                console.error(error);
            }
        });
    });
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>
