// Attach click event handler to the delete button
$('.delete-button').click(function () {
  event.stopPropagation();
  var announcementBatch = $(this).data('announcement-batch');

  // Show confirmation dialog using SweetAlert2
  Swal.fire({
    title: 'Confirmation',
    text: 'Are you sure you want to delete this announcement?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      // User confirmed the deletion, send AJAX request to delete_announcement.php
      $.ajax({
        url: 'delete_announcement.php',
        type: 'POST',
        data: { announcementBatch: announcementBatch },
        success: function (response) {
          if (response === 'success') {
            // Announcement deleted successfully, show success alert
            Swal.fire({
              title: 'Success',
              text: 'The announcement has been deleted.',
              icon: 'success',
            }).then(() => {
              // Optionally, you can reload the page or perform any other actions
              location.reload();
            });
          } else {
            // Error occurred while deleting the announcement, show error alert
            Swal.fire({
              title: 'Error',
              text: 'An error occurred while deleting the announcement.',
              icon: 'error',
            });
          }
        },
        error: function () {
          // AJAX request failed, show error alert
          Swal.fire({
            title: 'Error',
            text: 'An error occurred while deleting the announcement.',
            icon: 'error',
          });
        },
      });
    }
  });
});
