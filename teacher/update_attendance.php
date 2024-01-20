<?php
include('../connection.php');
$con = connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have the attendance ID passed from the form
    $attendanceId = $_POST['attendanceId'];

    // Assuming you have the updated values passed from the form
    $studentName = $_POST['studentName'];
    $timeIn = $_POST['timeIn'];
    $timeOut = $_POST['timeOut'];
    $remark_status = $_POST['remark_status'];
    $Remarks = $_POST['remark'];

    // Convert the string value of $remark_status to an integer
    $remark_status = intval($remark_status);

    // Handle null value for time-out
    $timeInValue = !empty($timeIn) ? "'$timeIn'" : "NULL";
    // Handle null value for time-out
    $timeOutValue = !empty($timeOut) ? "'$timeOut'" : "NULL";

    // Perform the update query
    $updateQuery = "UPDATE attendancetable SET `student_name` = '$studentName', `time-in` = $timeInValue, `time-out` = $timeOutValue, `remark_status` = '$remark_status', `remark` = '$Remarks' WHERE attendance_id = '$attendanceId'";
    $result = $con->query($updateQuery);

    if ($result) {
        // Fetch the updated table rows and return as a response
        include('student_attendance.php');
    } else {
        // Update failed
        echo "Error updating data: " . $con->error;
    }
}

?>
