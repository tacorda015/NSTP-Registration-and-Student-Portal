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
$role = "SELECT * FROM roleaccount WHERE role_account_id = {$user_data['role_account_id']}";
$result = $con->query($role);
$role_data = $result->fetch_assoc();
$group_id = $user_data['group_id'];

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

// Retrieve the schedule for today and the given group_id
$currentDate = date('Y-m-d');
$query = "SELECT * FROM scheduletable WHERE group_id = $group_id AND schedule_date = '$currentDate'";
$result = $con->query($query);
$schedule_date = $result->fetch_assoc();


?>
<style>
    .attendance-notification{
        background-color: #c5e2f2;
        border: 1px solid #58aed8;
        width: 100%;
    }
    #responseMessage{
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
</style>
<div class="home-main-container">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-5">
                <h2 style="text-align: center;">Qr Code Scanner</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-5">
                <h2 id="heading" style="text-align: center;">
                    
                </h2>
                <?php
                    // if ($schedule_date) {
                    //     echo "<h2>Schedule Date: " . $schedule_date['schedule_date'] ."</h2>" ;
                    // } else {
                    //     echo "<h2>No schedule today.</h2>";
                    // }
                    // Retrieve the current date and time
                    $currentDate = date('Y-m-d');
                    $currentTime = date('H:i:s');

                    if ($schedule_date) {
                        // if ($schedule_date['schedule_date'] < $currentDate) {
                            if ($schedule_date['schedule_end'] < $currentTime) {
                            // Schedule date has passed
                            // Retrieve all students without time-in records
                            $query = "SELECT ua.user_account_id, ua.group_id, ua.full_name, ua.student_number
                                    FROM useraccount ua
                                    WHERE ua.group_id = $group_id
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
                        }else{
                            echo "Schedule end time has not passed yet.";
                        }
                    } else {
                        echo "<h2>No schedule today.</h2>";
                    }

                    ?>
            </div>
        </div>
        <div class="row mx-2">
            <div class="col-md-6">
                <div id="reader" width="250px"></div>
                <div id="message" style="display: none;"></div>
            </div>
            <div class="col-md-6 d-flex flex-column align-items-center">
            <div id="responseMessageContainer" class="attendance-notification" style="display: none;">
                <div id="responseMessage"></div>
            </div>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student Number</th>
                            <th>Time</th>
                            <th>Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody"></tbody>
                </table>
                <br>
                <div>
                    <button id="timeInBtn" class="btn btn-primary">Time In</button>
                    <button id="timeOutBtn" class="btn btn-primary">Time Out</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>
<script src="../assets/js/qrscanner.js"></script>
<!-- <script>

let scannedCodes = [];

function onScanSuccess(decodedText, decodedResult) {
  if (scannedCodes.includes(decodedText)) {
    displayMessage('Already Scanned');
    return;
  }

  //   scannedCodes.push(decodedText);
  displayMessage('QR Code Scanned: ' + decodedText);

  const timeInBtn = document.getElementById('timeInBtn');
  const timeOutBtn = document.getElementById('timeOutBtn');

  if (timeInBtn.classList.contains('active')) {
    // Time-in button is selected
    recordAttendance(decodedText, 'time-in');
  } else if (timeOutBtn.classList.contains('active')) {
    // Time-out button is selected
    recordAttendance(decodedText, 'time-out');
  } else {
    displayMessage('Please select either Time In or Time Out');
  }
}
// function recordAttendance(studentNumber, attendanceType) {
//   const xhr = new XMLHttpRequest();
//   //   xhr.open('POST', 'scan.php', true);
//   xhr.open('POST', 'scan.php?attendance_type=' + attendanceType, true);

//   xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//   xhr.onreadystatechange = function () {
//     if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
//       console.log(xhr.responseText);
//       updateAttendanceTable(attendanceType); // Pass the attendanceType parameter
//     }
//   };
//   const params =
//     'student_number=' +
//     encodeURIComponent(studentNumber) +
//     '&attendance_type=' +
//     attendanceType;
//   xhr.send(params);
// }
function recordAttendance(studentNumber, attendanceType) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'scan.php?attendance_type=' + attendanceType, true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      console.log(xhr.responseText);
      updateAttendanceTable(attendanceType); // Pass the attendanceType parameter

      // Display the message from scan.php in scanner.php
      const responseMessageContainer = document.getElementById('responseMessageContainer');
      const responseMessage = document.getElementById('responseMessage');
      responseMessage.textContent = xhr.responseText;
      responseMessageContainer.style.display = 'block';
    }
  };
  const params =
    'student_number=' +
    encodeURIComponent(studentNumber) +
    '&attendance_type=' +
    attendanceType;
  xhr.send(params);
}


function handleButtonClick(event) {
  const buttons = document.querySelectorAll('.btn-primary');
  buttons.forEach((button) => {
    button.classList.remove('active');
  });

  event.target.classList.add('active');

  const attendanceType =
    event.target.id === 'timeInBtn' ? 'time-in' : 'time-out';
  updateAttendanceTable(attendanceType);

  const heading = document.getElementById('heading');

  if (event.target.id === 'timeInBtn') {
    heading.textContent = 'TIME IN';
  } else if (event.target.id === 'timeOutBtn') {
    heading.textContent = 'TIME OUT';
  }
}

const timeInBtn = document.getElementById('timeInBtn');
const timeOutBtn = document.getElementById('timeOutBtn');
timeInBtn.addEventListener('click', handleButtonClick);
timeOutBtn.addEventListener('click', handleButtonClick);

// Set initial state
timeInBtn.classList.add('active');
const heading = document.getElementById('heading');
heading.textContent = 'TIME IN';
updateAttendanceTable('time-in');

function onScanFailure(error) {
  console.warn(`Code scan error = ${error}`);
}

function displayMessage(message) {
  const messageElement = document.getElementById('message');
  messageElement.textContent = message;
}
const html5QrcodeScanner = new Html5QrcodeScanner(
  'reader',
  { fps: 10, qrbox: { width: 150, height: 150 } },
  false
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);

function updateAttendanceTable(attendanceType) {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', 'get_attendance.php?attendance_type=' + attendanceType, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      const tableBody = document.getElementById('attendanceTableBody');
      tableBody.innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

</script> -->
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>


</body>
</html>
