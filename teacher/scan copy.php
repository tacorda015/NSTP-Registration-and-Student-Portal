<?php
// include('../connection.php');
// $con = connection();
// session_start();

// if (isset($_POST['student_number']) && isset($_POST['attendance_type'])) {
//     $student_number = $_POST['student_number'];
//     $attendance_type = $_POST['attendance_type'];
//     $studentN = base64_decode($student_number);

//     // Prepare and execute the SQL query to fetch student data
//     $student_query = "SELECT * FROM useraccount WHERE student_number = '$studentN'";
//     $student_result = mysqli_query($con, $student_query);
//     $student_data = mysqli_fetch_assoc($student_result);

//     if ($student_data) {
//         $user_account_id = $student_data['user_account_id'];
//         $student_name = $student_data['full_name'];
//         $student_group_id = $student_data['group_id'];

//         // Check if the student's group_id matches the teacher's group_id
//         $teacher_group_id = $_SESSION['user_data']['group_id'];

//         // Log the value of group_id
//         echo "<script>console.log('Teacher\'s group_id: $teacher_group_id');</script>";
//         echo "<script>console.log('Student\'s group_id: $student_group_id');</script>";
//         echo "<script>console.log('Student\'s group_id: $attendance_type');</script>";

//         // Check if both group_id values are NULL or if they match
//         if (($student_group_id === null && $teacher_group_id === null) || $student_group_id === $teacher_group_id) {
//             // Check if the student has already timed-in for the day
//             $current_date = date('Y-m-d');
//             $time_in_query = "SELECT * FROM attendancetable WHERE student_number = '$studentN' AND DATE(`time-in`) = '$current_date'";
//             $time_in_result = mysqli_query($con, $time_in_query);
//             $existing_record = mysqli_fetch_assoc($time_in_result);

//             if ($attendance_type === 'time-in') {
//                 if ($existing_record) {
//                     // Student has already timed-in
//                     echo "Student has already timed-in for the day";
//                 } else {
//                     // Prepare and execute the SQL query to insert the data
//                     $sql = "INSERT INTO attendancetable (`user_account_id`, `student_name`, `student_number`, `time-in`, `time-out`, `attendance_status`, `remark`) VALUES ('$user_account_id', '$student_name','$studentN', NOW(), '','','')";
//                     if ($con->query($sql) === TRUE) {
//                         echo "Time-in recorded successfully";
//                     } else {
//                         echo "Error: " . $sql . "<br>" . $con->error;
//                     }
//                 }
//             } elseif ($attendance_type === 'time-out') {
//                 if ($existing_record) {
//                     // Update the existing record with the time-out value
//                     $time_out_query = "UPDATE attendancetable SET `time-out` = NOW() WHERE student_number = '$studentN' AND DATE(`time-in`) = '$current_date'";
//                     if ($con->query($time_out_query) === TRUE) {
//                         echo "Time-out recorded successfully";
//                     } else {
//                         echo "Error: " . $time_out_query . "<br>" . $con->error;
//                     }
//                 } else {
//                     // Student has not timed-in yet
//                     echo "Student has not timed-in yet";
//                 }
//             } else {
//                 // Invalid attendance type
//                 echo "Invalid attendance type";
//             }
//         } else {
//             echo "Student does not belong to the same group as the teacher";
//         }
//     } else {
//         echo "Student data not found";
//     }

//     ob_start();
//     include('get_attendance.php');
//     $tableHTML = ob_get_clean();

//     echo $tableHTML;
//     // $con->close();
// }
?>

<?php

date_default_timezone_set('Asia/Manila');

include('../connection.php');
$con = connection();
session_start();

if (isset($_POST['student_number']) && isset($_POST['attendance_type'])) {
    $student_number = $_POST['student_number'];
    $attendance_type = $_POST['attendance_type'];
    $studentN = base64_decode($student_number);

    // Prepare and execute the SQL query to fetch student data
    $student_query = "SELECT * FROM useraccount WHERE student_number = '$studentN'";
    $student_result = mysqli_query($con, $student_query);
    $student_data = mysqli_fetch_assoc($student_result);

    if ($student_data) {
        $user_account_id = $student_data['user_account_id'];
        $student_name = $student_data['full_name'];
        $student_group_id = $student_data['group_id'];

        // Check if the student's group_id matches the teacher's group_id
        $teacher_group_id = $_SESSION['user_data']['group_id'];

        // Log the value of group_id
        echo "<script>console.log('Teacher\'s group_id: $teacher_group_id');</script>";
        echo "<script>console.log('Student\'s group_id: $student_group_id');</script>";
        echo "<script>console.log('Student\'s group_id: $attendance_type');</script>";

        // Check if both group_id values are NULL or if they match
        if (($student_group_id === null && $teacher_group_id === null) || $student_group_id === $teacher_group_id) {
            // Check if the student has a schedule for today
            $current_date = date('Y-m-d');
            $schedule_query = "SELECT schedule_start, schedule_end FROM scheduletable WHERE group_id = '$teacher_group_id' AND schedule_date = '$current_date'";
            $schedule_result = mysqli_query($con, $schedule_query);
            $schedule_data = mysqli_fetch_assoc($schedule_result);

            if ($schedule_data) {
                $schedule_start = $schedule_data['schedule_start'];
                $schedule_end = $schedule_data['schedule_end'];

                // Compare current time with schedule_start to determine attendance status
                $current_time = date('H:i:s');
                if ($current_time <= $schedule_start) {
                    $attendance_status = 'Present';
                    echo "<script>console.log('Student\'s current time: $current_time');</script>";
                    echo "<script>console.log('Student\'s schedule: $schedule_start');</script>";
                } elseif ($current_time <= $schedule_end) {
                    $attendance_status = 'Late';
                    echo "<script>console.log('Student\'s current time: $current_time');</script>";
                    echo "<script>console.log('Student\'s schedule: $schedule_start');</script>";
                } else {
                    $attendance_status = 'Absent';
                }

                // Check if the student has already timed-in for the day
                $time_in_query = "SELECT * FROM attendancetable WHERE student_number = '$studentN' AND DATE(`time-in`) = '$current_date'";
                $time_in_result = mysqli_query($con, $time_in_query);
                $existing_record = mysqli_fetch_assoc($time_in_result);

                if ($attendance_type === 'time-in') {
                    if ($existing_record) {
                        // Student has already timed-in
                        echo "Student has already timed-in for the day";
                    } else {
                        // Prepare and execute the SQL query to insert the data
                        $sql = "INSERT INTO attendancetable (`user_account_id`, `student_name`, `student_number`, `time-in`, `time-out`, `attendance_status`, `remark`) VALUES ('$user_account_id', '$student_name','$studentN', NOW(), '','$attendance_status','')";
                        if ($con->query($sql) === TRUE) {
                            echo "Time-in recorded successfully";
                        } else {
                            echo "Error: " . $sql . "<br>" . $con->error;
                        }
                    }
                } elseif ($attendance_type === 'time-out') {
                    if ($existing_record) {
                        // Update the existing record with the time-out value
                        $time_out_query = "UPDATE attendancetable SET `time-out` = NOW() WHERE student_number = '$studentN' AND DATE(`time-in`) = '$current_date'";
                        if ($con->query($time_out_query) === TRUE) {
                            echo "Time-out recorded successfully";
                        } else {
                            echo "Error: " . $time_out_query . "<br>" . $con->error;
                        }
                    } else {
                        // Student has not timed-in yet
                        echo "Student has not timed-in yet";
                    }
                } else {
                    // Invalid attendance type
                    echo "Invalid attendance type";
                }
            } else {
                echo "No schedule today for the student";
            }
        } else {
            echo "Student does not belong to the same group as the teacher";
        }
    } else {
        echo "Student data not found";
    }

    ob_start();
    include('get_attendance.php');
    $tableHTML = ob_get_clean();

    echo $tableHTML;
    // $con->close();
}
?>
