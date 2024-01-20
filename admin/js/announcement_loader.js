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

function sendEmail() {
  sendingEmail = true;
  document.getElementById('sendButton').setAttribute('disabled', 'disabled'); // Disable the button
  document.getElementById('loader-overlay').style.display = 'block'; // Show the loader overlay
  document.getElementById('loader').style.display = 'block'; // Show the loader

  // Send the form data asynchronously
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'send_addannouncement.php', true);
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
            window.location.href = 'announcement.php';
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
            window.location.href = 'announcement.php';
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

  var recipient = document.getElementById('recipient').value;
  var rotcgroup = document.getElementById('rotcgroup').value;
  var cwtsgroup = document.getElementById('cwtsgroup').value;
  var specificRecipients = document.getElementById('specificRecipients').value;
  var hiddenspecificRecipients = document.getElementById(
    'hiddenspecificRecipients'
  ).value;
  var subject = document.getElementById('subject').value;
  var message = document.getElementById('message').value;

  var data =
    'recipient=' +
    encodeURIComponent(recipient) +
    '&rotcgroup=' +
    encodeURIComponent(rotcgroup) +
    '&cwtsgroup=' +
    encodeURIComponent(cwtsgroup) +
    '&specificRecipients=' +
    encodeURIComponent(specificRecipients) +
    '&hiddenspecificRecipients=' +
    encodeURIComponent(hiddenspecificRecipients) +
    '&subject=' +
    encodeURIComponent(subject) +
    '&message=' +
    encodeURIComponent(message);

  xhr.send(data);
}
