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
$user_account_id = $user_data['user_account_id'];

$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = $user_account_id";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$group_id = $useraccount_data['group_id'];
$role_account_id = $useraccount_data['role_account_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
} 
date_default_timezone_set('Asia/Manila');
// Calling the side bar
include_once('./studentsidebar.php');
?>
                    <div class="home-main-container">
                        <div class="studentList-container">
                            <?php
                                if($group_id !== null){
                            ?>
                            <div class='page-title'>
                                <div class='titleContainer'>
                                    <span class="group-id">Attendance Monitoring</span>
                                    <?php
                                    $currentDate = date('Y-m-d');
                                    $currentTime = date('H:i:s');
                                    $query = "SELECT * FROM scheduletable WHERE group_id = $group_id AND schedule_date = '$currentDate'";
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
                            </div>
                            <div class="buttonsContainer">
                                <div class="buttonHolder">
                                    <a href="attendance_download.php?user_id=<?php echo $user_account_id; ?>" class="download-button btn btn-primary">
                                    <i class="bx bx-download"></i>Export Attendance</a>
                                    
                                    <?php
                                        $remark_query = "SELECT remark_status FROM attendancetable WHERE group_id = '$group_id' AND user_account_id = '$user_account_id' ORDER BY attendance_id DESC LIMIT 1";
                                        $remark_result = $con->query($remark_query);
                                        
                                        if ($remark_result && $remark_result->num_rows > 0) {
                                            $row = $remark_result->fetch_assoc();
                                            $remark_status = $row['remark_status'];
                                        
                                            if ($remark_status == 6) {
                                                echo'<div class="alert alert-danger" role="alert">
                                                        You are Inactive
                                                    </div>';
                                            } elseif ($remark_status == 2) {
                                                echo'<div class="alert alert-warning" role="alert">
                                                        You Forgot to Time-out
                                                    </div>';
                                            } elseif ($remark_status == 1) {
                                                echo'<div class="alert alert-warning" role="alert">
                                                        Need to create a Report
                                                    </div>';
                                            } else {
                                                // Handle other remark_status values if needed
                                            }
                                        } else {
                                            // echo "No remark status found";
                                        }                                        
                                    ?>
                                </div>
                            </div>
                            <?php 
                             $recordsPerPage = 10;
                             if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                                 $currentPage = intval($_GET['page']);
                             } else {
                                 $currentPage = 1;
                             }

                             // Calculate the offset for the current page
                             $offset = ($currentPage - 1) * $recordsPerPage;

                             $attendance_query = "SELECT * FROM attendancetable WHERE user_account_id = '$user_account_id' AND group_id = '$group_id' ORDER BY attendance_id DESC";
                             $attendance_query .= " LIMIT $recordsPerPage OFFSET $offset";    
                                                                     
                             $attendance_result = $con->query($attendance_query);
                             if($attendance_result && $attendance_result->num_rows > 0){
                            ?>
                            <div class="tableContainer">
                                <table class="table table-sm caption-top">
                                    <caption>List of Attendace</caption>
                                    <thead class="custom-thead">
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Time-in</th>
                                            <th>Time-out</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <?php
                                           
                                                $rowCounter = 0;
                                                while($row = $attendance_result->fetch_assoc()){
                                                    $attendance_id = $row['attendance_id'];
                                                    $rowStyle = '';
                                                    if ($row['attendance_status'] === 'Absent') {
                                                        $rowStyle .= ($rowCounter % 2 === 0) ? 'background-color: #ffc8c8;' : 'background-color: #ffdcdc;';
                                                    } elseif ($row['attendance_status'] === 'Late') {
                                                        $rowStyle .= ($rowCounter % 2 === 0) ? 'background-color: #ffffcc;' : 'background-color: #ffffe6;';
                                                    }
                                                    // echo $attendance_id;
                                                    echo "<tr id='attendance-row-$attendance_id' style='$rowStyle'>";
                                                    echo "<td data-label='Activity Date:'>" . $row['activity_date'] . "</td>";
                                                    echo "<td data-label='Status:'>" . $row['attendance_status'] . "</td>";

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

                                                    echo "<td data-label='Remark:'>" . $row['remark'] . "</td>";    
                                                    echo "</tr>";
                                                    $rowCounter++;
                                                }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            // Get the total number of records for pagination
                            $totalRecordsQuery = "SELECT COUNT(*) AS total FROM attendancetable WHERE user_account_id = '$user_account_id' AND group_id = '$group_id'";
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
                        }else{
                            echo "<h2 style='text-align: center;'>No Student Attendance</h2>";
                        }
                                }else{
                                    echo "<h2 style='text-align: center;'>No Assigned Group yet.</h2>";
                                }
                            ?>
                        </div>
                    </div>
                </section>
            </div>
        <script src="../asset/js/index.js"></script>
        <script src="../asset/js/topbar.js"></script>
    </body>
</html>