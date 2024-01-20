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

function recordAttendance(studentNumber, attendanceType) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'scan.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      console.log(xhr.responseText);
      updateAttendanceTable();
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
}

const timeInBtn = document.getElementById('timeInBtn');
const timeOutBtn = document.getElementById('timeOutBtn');
timeInBtn.addEventListener('click', handleButtonClick);
timeOutBtn.addEventListener('click', handleButtonClick);

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

function updateAttendanceTable() {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', 'get_attendance.php', true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      const tableBody = document.getElementById('attendanceTableBody');
      tableBody.innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}
