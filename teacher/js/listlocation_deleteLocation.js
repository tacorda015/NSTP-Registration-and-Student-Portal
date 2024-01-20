// JavaScript function to handle the delete button click
function deleteLocation(locationId) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You are about to delete this location',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      // Send an AJAX request to delete the location
      // var xhr = new XMLHttpRequest();
      // xhr.open('POST', 'delete_location.php', true);
      // xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      // xhr.onreadystatechange = function () {
      //   if (xhr.readyState === 4 && xhr.status === 200) {
      //     // Remove the row from the table
      //     var row = document.getElementById('locationRow' + locationId);
      //     if (row) {
      //       row.parentNode.removeChild(row);
      //     }

      //     // Display success message
      //     Swal.fire({
      //       title: 'Deleted!',
      //       text: 'The location has been deleted successfully',
      //       icon: 'success',
      //     });
      //   }
      // };
      // xhr.send('location_id=' + locationId);

      $.ajax({
        url: 'delete_location.php',
        type: 'POST',
        dataType: 'json',
        data: {
          location_id: locationId,
        },
        success: function (response) {
          if (response.status === true) {
            // Event deleted successfully, you can update the UI as needed
            // Show a success message
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.msg,
              confirmButtonText: 'OK',
            }).then(function () {
              location.reload(); // Reload the page after user clicks "OK"
            });
          } else {
            // Error message
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response.msg,
              confirmButtonText: 'OK',
            });
          }
        },
        error: function (xhr, status, error) {
          // Handle AJAX error
          console.log('AJAX error: ' + error);
          console.log(xhr.responseText);
          alert(
            'An error occurred while deleting the event. Please check the console for more details.'
          );
        },
      });
    }
  });
}

// Add event listener to delete buttons
var deleteButtons = document.getElementsByClassName('delete-button');
for (var i = 0; i < deleteButtons.length; i++) {
  deleteButtons[i].addEventListener('click', function () {
    var locationId = this.getAttribute('data-location-id');
    deleteLocation(locationId);
  });
}
