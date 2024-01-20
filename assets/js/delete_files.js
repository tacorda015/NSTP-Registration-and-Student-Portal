function deleteFile(file_id) {
  // Display a confirmation alert
  Swal.fire({
    title: 'Are you sure?',
    text: 'Once deleted, this file cannot be recovered!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      // User confirmed the deletion, perform the delete operation
      // Send an AJAX request or navigate to a delete endpoint
      // for deleting the file with the given file_id

      // Example AJAX request (using jQuery):
      $.ajax({
        url: 'delete_file.php',
        type: 'POST',
        data: { file_id: file_id },
        success: function (response) {
          if (response === 'success') {
            // Display a success message
            Swal.fire('Deleted!', 'The file has been deleted.', 'success').then(
              () => {
                // Refresh the file list
                $('#file-row-' + file_id).remove();
              }
            );
          } else {
            // Display an error message
            Swal.fire(
              'Error!',
              'An error occurred while deleting the file1.',
              'error'
            );
          }
        },
        error: function (xhr, status, error) {
          // Display an error message
          Swal.fire(
            'Error!',
            'An error occurred while deleting the file2.',
            'error'
          );
        },
      });
    }
  });
}
