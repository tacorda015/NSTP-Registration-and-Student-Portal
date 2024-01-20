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
// function recordAttendance(studentNumber, attendanceType) {
//   const xhr = new XMLHttpRequest();
//   xhr.open('POST', 'scan.php?attendance_type=' + attendanceType, true);
//   xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//   xhr.onreadystatechange = function () {
//     if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
//       console.log(xhr.responseText);
//       updateAttendanceTable(attendanceType); // Pass the attendanceType parameter

//       // Display the message from scan.php in scanner.php
//       const responseMessageContainer = document.getElementById(
//         'responseMessageContainer'
//       );
//       const responseMessage = document.getElementById('responseMessage');
//       responseMessage.textContent = xhr.responseText;
//       responseMessageContainer.style.display = 'block';
//       // Hide the response message after 2 seconds
//       setTimeout(function () {
//         responseMessageContainer.style.display = 'none';
//       }, 200);
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
      const responseMessageContainer = document.getElementById(
        'responseMessageContainer'
      );
      const responseMessage = document.getElementById('responseMessage');
      responseMessage.textContent = xhr.responseText;
      responseMessageContainer.classList.remove('hide');
      responseMessageContainer.classList.add('show');

      // Hide the response message after 2 seconds
      setTimeout(function () {
        responseMessageContainer.classList.remove('show');
      }, 4000);
      responseMessageContainer.classList.add('hide');
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

// function onScanFailure(error) {
//   console.warn(`Code scan error = ${error}`);
// }

function displayMessage(message) {
  const messageElement = document.getElementById('message');
  messageElement.textContent = message;
}
const html5QrcodeScanner = new Html5QrcodeScanner(
  'reader',
  { fps: 10, qrbox: { width: 150, height: 150 } },
  // { fps: 10, qrbox: 50 },
  false
);
// html5QrcodeScanner.render(onScanSuccess, onScanFailure);
html5QrcodeScanner.render(onScanSuccess);

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
