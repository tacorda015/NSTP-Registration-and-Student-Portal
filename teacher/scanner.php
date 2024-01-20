<?php
include('../connection.php');
session_start();
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}
date_default_timezone_set('Asia/Manila');

// get user data from session
$user_data = $_SESSION['user_data'];
$user_account_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = $user_account_id";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$role_account_id = $useraccount_data['role_account_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

$schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
$schoolyear_result = $con->query($schoolyear_query);
$schoolyear_data = $schoolyear_result->fetch_assoc();

if($schoolyear_data){
    $schoolyear_id = $schoolyear_data['schoolyear_id'];
    $semester_id = $schoolyear_data['semester_id'];

    $retrieve_query = "SELECT * FROM useraccount WHERE user_account_id = $user_account_id AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
    $retrieve_result = $con->query($retrieve_query);
    $retrieve_data = $retrieve_result->fetch_assoc();
    $group_id = $retrieve_data['group_id'];
}else{
    $group_id = '';
}
// echo "<script>console.log('group_id: " . $group_id . "');</script>";

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('./teachersidebar.php');
?>

<style>
    #heading{
        font-size: 18px;
    }
    caption .groupButton{
        padding-bottom: 10px;
    }
    .alert-info.hide{
        margin: 0;
        display: none;
        padding: 0;
    }
    .alert-info.show.hide{
        margin: 0;
        display: block;
        font-size: 13.5px;
        padding: 5px;
        text-align: center;
    }
</style>
<div class="home-main-container">
    <div class="studentList-container">
        <?php
        if($group_id !== null && $group_id !== ''){
            // Retrieve the schedule for today and the given group_id
            $currentDate = date('Y-m-d');
            $query = "SELECT * FROM scheduletable WHERE group_id = $group_id AND schedule_date = '$currentDate'";
            $result = $con->query($query);
            $schedule_date = $result->fetch_assoc();
            ?>
            <div class="page-title">
                <div class="titleContainer">
                    <span class="group-id">Qr Code Scanner</span>
                    <?php
                        $currentDate = date('Y-m-d');
                        $currentTime = date('H:i:s');
                        
                        if ($schedule_date) {
                            $date = $schedule_date['schedule_date'];
                            $start = $schedule_date['schedule_start'];
                            $end = $schedule_date['schedule_end'];
                        
                                if ($end < $currentTime) {
                                // Schedule date has passed
                                // Retrieve all students without time-in records
                                $query = "SELECT ua.user_account_id, ua.group_id, ua.full_name, ua.student_number
                                        FROM useraccount ua
                                        WHERE ua.group_id = $group_id
                                            AND role_account_id = 2
                                            AND ua.user_account_id NOT IN (
                                                SELECT at.user_account_id
                                                FROM attendancetable at
                                                WHERE activity_date = '$currentDate'
                                            )";
                                $result = $con->query($query);

                                // Iterate over the students and save their absent records
                                while ($student = $result->fetch_assoc()) {
                                    $user_account_id = $student['user_account_id'];
                                    $group_id = $student['group_id'];
                                    $student_name = $student['full_name'];
                                    $student_number = $student['student_number'];

                                    // Prepare and execute the SQL query to insert the absent record
                                    $insertQuery = "INSERT INTO attendancetable (`user_account_id`, `group_id`, `student_name`, `student_number`, `activity_date`, `time-in`, `time-out`, `attendance_status`, `remark_status`)
                                                    VALUES ($user_account_id, $group_id, '$student_name', '$student_number', '$currentDate', NULL, NULL, 'Absent', 1)";
                                    $con->query($insertQuery);
                                }
                                echo "<label>Activity Schedule Already Finished.</label>";
                            }else{
                                echo "<label>Date: $date <br>Time: $start - $end</label>";
                            }
                        } else {
                            echo "<label>No schedule today.</label>";
                        }
                    ?>
                </div>
            </div>
                <div class="scannerContainer">
                    <div id="reader" width="250px"></div>
                    <div id="message" style="display: none;"></div>
                    <div class="tableContainerScanner">
                        <table class="table table-sm caption-top">
                        <caption>
                            <h2 id="heading" style="text-align: center; padding:5px 0 10px;"></h2>
                            <div class="groupButton">
                                <button id="timeInBtn" class="btn btn-primary">Time In</button>
                                <button id="timeOutBtn" class="btn btn-primary">Time Out</button>
                            </div>
                            <div class="alert alert-info hide" role="alert" id="responseMessageContainer">
                            <div id="responseMessage"></div>
                        </caption>
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <!-- <th>Student Number</th> -->
                                    <th>Time</th>
                                    <!-- <th>Attendance Status</th> -->
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }else{
            echo "<h2 style='text-align:center;' >No Assigned Group yet.</h2>";
        }
        ?>
        </div>
    </section>
</div>

<script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>
<script src="../assets/js/qrscanner.js"></script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>


</body>
</html>
