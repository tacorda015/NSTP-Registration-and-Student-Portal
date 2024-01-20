// Start Uploading files

// Reset the form fields when the modal is closed
$('#uploadmodule').on('hidden.bs.modal', function () {
  $('#upload-form')[0].reset();
});

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

// Add an event listener to the form submission
document.getElementById('upload-form').addEventListener('submit', function (e) {
  e.preventDefault(); // Prevent form submission

  sendingEmail = true;
  document.getElementById('sendButton').setAttribute('disabled', 'disabled'); // Disable the button
  document.getElementById('loader-overlay').style.display = 'block'; // Show the loader overlay
  document.getElementById('loader').style.display = 'block'; // Show the loader

  // Get the file input element and selected file
  var fileInput = document.getElementById('file');
  var file = fileInput.files[0];

  // Check if a file is selected
  if (!file) {
    // Display a SweetAlert2 error message if no file is selected
    Swal.fire({
      title: 'Error!',
      text: 'Please select a file.',
      icon: 'error',
      confirmButtonText: 'OK',
    });
    return; // Stop further execution of the code
  }

  // Get the file size
  var fileSize = file.size;

  // Check if the file size exceeds the limit
  var maxSize = 1024 * 1024 * 10; // 10 MB (adjust this value as needed)
  if (fileSize > maxSize) {
    // Display a SweetAlert2 error message if the file size exceeds the limit
    Swal.fire({
      title: 'Error!',
      text: 'The file size exceeds the allowed limit(50MB).',
      icon: 'error',
      confirmButtonText: 'OK',
    });
    return; // Stop further execution of the code
  }

  // Proceed with form submission using AJAX
  var form = document.getElementById('upload-form');
  var formData = new FormData(form);

  $.ajax({
    url: form.action,
    type: form.method,
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      sendingEmail = false;
      document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
      document.getElementById('loader').style.display = 'none'; // Hide the loader
      // Show SweetAlert2 success message
      Swal.fire({
        title: 'Success!',
        text: 'The module has been uploaded successfully.',
        icon: 'success',
        confirmButtonText: 'OK',
      }).then(function () {
        window.location = 'modulelist.php';
      });
    },
    error: function () {
      // Show SweetAlert2 error message
      sendingEmail = false;
      document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
      document.getElementById('loader').style.display = 'none'; // Hide the loader
      Swal.fire({
        title: 'Error!',
        text: 'There was an error uploading the module. Please try again later.',
        icon: 'error',
        confirmButtonText: 'OK',
      });
    },
  });
});

//   End of Uploading files
