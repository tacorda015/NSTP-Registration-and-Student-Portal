<?php
include('../connection.php');
$con = connection();

$attendanceType = $_GET['attendance_type'];

if ($attendanceType === 'time-in') {
    $timeField = "time-in";
} elseif ($attendanceType === 'time-out') {
    $timeField = "time-out";
}

// Retrieve the current date
$currentDate = date('Y-m-d');

$query = "SELECT * FROM `attendancetable`
          WHERE activity_date = '$currentDate'
          AND attendance_status != 'Absent'
          AND `$timeField` IS NOT NULL
          ORDER BY `$timeField` DESC";
$result = $con->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $attendance_id = $row['attendance_id'];
        echo "<tr id='attendance-row-$attendance_id'>";
        echo "<td data-label='Student Name'>".$row['student_name']."</td>";
        echo "<td data-label='Time'>".$row[$timeField]."</td>";
        // echo "<td data-label='Status'>".$row['attendance_status']."</td>";
        echo "</tr>";
    }
} else {
    echo '<tr><td colspan="5">No Attendance.</td></tr>';
}

?>
