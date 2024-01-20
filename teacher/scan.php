<?php

date_default_timezone_set('Asia/Manila');

include('../connection.php');
$con = connection();
session_start();

$user_data = $_SESSION['user_data'];

$user_account_id = $user_data['user_account_id'];

$retrieve_query = "SELECT * FROM useraccount WHERE user_account_id = $user_account_id";
$retrieve_result = $con->query($retrieve_query);
$retrieve_data = $retrieve_result->fetch_assoc();

if (isset($_POST['student_number']) && isset($_POST['attendance_type'])) {
    $student_number = $_POST['student_number'];
    $attendance_type = $_POST['attendance_type'];
    $studentN = base64_decode($student_number);

    $message = "";
    
    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
    $schoolyear_result = $con->query($schoolyear_query);
    $schoolyear_data = $schoolyear_result->fetch_assoc();
    $schoolyear_id = $schoolyear_data['schoolyear_id'];
    $semester_id = $schoolyear_data['semester_id'];

    // Prepare and execute the SQL query to fetch student data
    $student_query = "SELECT * FROM useraccount WHERE student_number = '$studentN' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
    $student_result = mysqli_query($con, $student_query);
    $student_data = mysqli_fetch_assoc($student_result);

    
    // if ($student_remark != 1){
        if ($student_data) {
            $user_account_id = $student_data['user_account_id'];
            $student_name = $student_data['full_name'];
            $student_group_id = $student_data['group_id'];
    
            // Check if the student's group_id matches the teacher's group_id
            $teacher_group_id = $retrieve_data['group_id'];
    
            // Check if both group_id values are NULL or if they match
            if (($student_group_id === null && $teacher_group_id === null) || $student_group_id === $teacher_group_id) {
                // Check if the student has a schedule for today
                $current_date = date('Y-m-d');
                $schedule_query = "SELECT * FROM scheduletable WHERE group_id = '$teacher_group_id' AND schedule_date = '$current_date'";
                $schedule_result = mysqli_query($con, $schedule_query);
                $schedule_data = mysqli_fetch_assoc($schedule_result);

                // $student_remark_query = "SELECT * FROM attendancetable WHERE student_number = '$studentN' ORDER BY activity_date DESC LIMIT 1";
                $student_remark_query = "SELECT * FROM attendancetable WHERE student_number = '$studentN' AND activity_date < CURDATE() ORDER BY activity_date DESC LIMIT 1";
                $student_remark_result = mysqli_query($con, $student_remark_query);
                $student_remark_data = mysqli_fetch_assoc($student_remark_result);
                // $student_remark = $student_remark_data['remark_status'];
                
                if ($student_remark_data !== null) {
                    $student_remark = $student_remark_data['remark_status'];
                } else {
                    $student_remark = null; // or any default value you prefer
                }
                if($student_remark == 6){
                    $message ="Student is Inactive";
                }elseif ($student_remark != 1){
                    if ($schedule_data) {
                        $schedule_start = $schedule_data['schedule_start'];
                        $schedule_end = $schedule_data['schedule_end'];
                        $schedule_date = $schedule_data['schedule_date'];
        
                        // Compare current time with schedule_start to determine attendance status
                        $current_time = date('H:i:s');

                        
                        if ($current_time <= $schedule_start) {
                            $attendance_status = 'Present';
                            $remark_status = 5;
                        } elseif ($current_time <= $schedule_end) {
                            $attendance_status = 'Late';
                            $remark_status = 5;
                        } else {
                            $attendance_status = 'Absent';
                            $remark_status = 1;
                        }
        
                        $status_query = "SELECT * FROM attendancetable WHERE user_account_id = '$user_account_id'";
                        $status_result = mysqli_query($con, $status_query);
                        $status_data = mysqli_fetch_assoc($status_result);
                        // $status = $status_data['attendance_status'];

                        if ($status_data !== null) {
                            $status = $status_data['attendance_status'];
                        } else {
                            $status = null; // or any default value you prefer
                        }
                        
                        if ($status === 'Absent') {
                            $message = "Student is absent and cannot time-out";
                        } else {
                            // Check if the student has already timed-in for the day
                            $time_in_query = "SELECT * FROM attendancetable WHERE student_number = '$studentN' AND activity_date = '$current_date'";
                            $time_in_result = mysqli_query($con, $time_in_query);
                            $existing_record = mysqli_fetch_assoc($time_in_result);
                        
                            if ($attendance_type === 'time-in') {
                                if ($existing_record && $existing_record['attendance_status'] === 'Absent') { // ADD HERE THAT CHECK THE END OF SCHEDULE AND CURRENT TIME
                                    // Student has already timed-in
                                    $message = "The Student is Absent";
                                } elseif ($existing_record){
                                    // Student has already timed-in
                                    // $message = "Student has already timed-in for the day";
                                    $message = "Student has already Timed-in";
                                } else {
                                    // Prepare and execute the SQL query to insert the data
                                    $sql = "INSERT INTO attendancetable (`user_account_id`, `group_id`, `student_name`, `student_number`, `activity_date`, `time-in`, `time-out`, `attendance_status`, `remark`, `remark_status`) VALUES ('$user_account_id', '$teacher_group_id', '$student_name', '$studentN', NOW(), CURRENT_TIME(), NULL,'$attendance_status','', $remark_status)";
                                    if ($con->query($sql) === TRUE) {
                                        $message = "Time-in recorded successfully";
                                    } else {
                                        $message = "Error: " . $sql . "<br>" . $con->error;
                                    }
                                }
                            } elseif ($attendance_type === 'time-out') {
                                if ($existing_record) {
        
                                    $existing_date = $existing_record['activity_date'];
                                    if ($existing_date === $current_date){
        
                                        // Check if the student's attendance status allows time-out
                                        if ($existing_record['attendance_status'] === 'Present' || $existing_record['attendance_status'] === 'Late') {
                                            // Check if the Student Already Time-out
                                            if ($existing_record['time-out'] === NULL){
                                                // Update the existing record with the time-out value
                                                $time_out_query = "UPDATE attendancetable SET `time-out` = CURRENT_TIME(), remark_status = 0 WHERE student_number = '$studentN' AND activity_date = '$current_date'";
                                                if ($con->query($time_out_query) === TRUE) {
                                                    $message = "Time-out recorded successfully";
                                                } else {
                                                    $message = "Error: " . $time_out_query . "<br>" . $con->error;
                                                }
                                            }else{
                                                $message ="Student has already time-out";
                                            }
                                        } else {
                                            $message = "Student's is Absent and Unauthorized time out";
                                        }
        
                                    } else{
                                        $message ="The Time-in and Time-out are Not Requested on the same day";
                                    }
                                } else {
                                    // Student has not timed-in yet
                                    $message = "Student has not Timed-in yet";
                                }
                            } else {
                                // Invalid attendance type
                                $message = "Invalid attendance type";
                            }
                            }
                            
                        } else {
                            $message = "No Activity Schedule Today";
                        }
                    }else{
                        $message = "Need to Create a Report for being Absent";
                    }
                } else {
                    $message = "Student does not belong to the Group";
                }
            } else {
                $message = "Student data not found";
            }
    // }else{
    //     $message = "You cannot Time-in, Need to Create a Report";
    // }

    $message = trim($message, '"');
    $message = ucfirst($message);
    echo $message;



    ob_start();
    include('get_attendance.php');
    $tableHTML = ob_get_clean();

    // echo $tableHTML;
    // $con->close();
    mysqli_close($con);
}
?>
