var sendingEmail = false;
// Confirmation message when refreshing or leaving the page
window.addEventListener('beforeunload', function (e) {
  if (sendingEmail) {
    // Show confirmation message only if email sending process has started
    e.preventDefault();
    e.returnValue = '';

    var confirmationMessage =
      'Changes you made may not be saved. Are you sure you want to leave this page?';
    (e || window.event).returnValue = confirmationMessage;
    return confirmationMessage;
  }
});
document
  .getElementById('emailForm')
  .addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Call the sendEmail function
    sendEmail();
  });

function sendEmail() {
  sendingEmail = true;
  document.getElementById('sendButton').setAttribute('disabled', 'disabled'); // Disable the button
  document.getElementById('loader-overlay').style.display = 'block'; // Show the loader overlay
  document.getElementById('loader').style.display = 'block'; // Show the loader

  // Send the form data asynchronously
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'send_addstudent.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        var response = JSON.parse(xhr.responseText);
        if (response.status === 'success') {
          // Success
          sendingEmail = false;
          document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
          document.getElementById('loader').style.display = 'none'; // Hide the loader
          Swal.fire({
            title: 'Success',
            text: response.message,
            icon: 'success',
          }).then(function () {
            window.location.href = 'studentlist.php';
          });
        } else {
          // Error
          sendingEmail = false;
          document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
          document.getElementById('loader').style.display = 'none'; // Hide the loader
          Swal.fire({
            title: 'Error',
            text: response.message,
            icon: 'error',
          }).then(function () {
            window.location.href = 'studentlist.php';
          });
        }
      } else {
        // Error
        document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
        document.getElementById('loader').style.display = 'none'; // Hide the loader
        Swal.fire({
          title: 'Error',
          text: 'Email could not be sent. Please try again.',
          icon: 'error',
        }); // Display error message
      }
    }
  };

  // var fullName = document.getElementById('add_full_name').value;
  var firstname = document.getElementById('firstname').value;
  var middlename = document.getElementById('middlename').value;
  var surname = document.getElementById('surname').value;
  var emailAddress = document.getElementById('add_email_address').value;
  var studentNumber = document.getElementById('add_student_number').value;
  var componentId = document.getElementById('component_id').value;
  var groupId = document.getElementById('choosegroup').value;

  var data =
    'firstname=' +
    encodeURIComponent(firstname) +
    '&middlename=' +
    encodeURIComponent(middlename) +
    '&surname=' +
    encodeURIComponent(surname) +
    '&email_address=' +
    encodeURIComponent(emailAddress) +
    '&student_number=' +
    encodeURIComponent(studentNumber) +
    '&component_id=' +
    encodeURIComponent(componentId) +
    '&group_id=' +
    encodeURIComponent(groupId);

  xhr.send(data);
  // var formData = new FormData(document.getElementById("emailForm"));
  // formData.append("add_student", ""); // Add the 'add_student' parameter to the form data
  // xhr.send(formData);
}
