// Function to add a new location to the table
// function addLocationToTable(placeName, locationId) {
//   // Create a new row element
//   var newRow = document.createElement('tr');

//   // Create table cells for the location data
//   var nameCell = document.createElement('td');
//   nameCell.textContent = placeName;

//   var actionCell = document.createElement('td');

//   var deleteButton = document.createElement('button');
//   deleteButton.className = 'delete-button btn btn-danger';
//   deleteButton.setAttribute('data-location-id', locationId);
//   deleteButton.textContent = 'Delete';

//   // Append the cells to the row
//   actionCell.appendChild(deleteButton);
//   newRow.appendChild(nameCell);
//   newRow.appendChild(actionCell);

//   // Get the table body and append the new row
//   var tableBody = document.querySelector('.location-table tbody');
//   tableBody.appendChild(newRow);
// }

// Function to save location to the database
function saveLocationToDatabase(coordinates, placeName, group_id) {
  Swal.fire({
    title: 'Save Location',
    text: 'Do you want to save this location?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'OK',
  }).then(function (result) {
    if (result.isConfirmed) {
      // User clicked "OK," proceed to save location
      // Send an AJAX request to save the location data to the database
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'save-location.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            // Parse the response to get the new location ID
            var response = JSON.parse(xhr.responseText);
            var locationId = response.location_id;

            // Add the new location to the table
            // addLocationToTable(placeName, locationId);

            // Show a SweetAlert2 success message
            Swal.fire({
              title: 'Location Saved',
              text: 'The location has been saved successfully.',
              icon: 'success',
              showCancelButton: false,
              confirmButtonText: 'OK',
            }).then(function () {
              window.location = 'listlocation.php';
            });

            console.log('Location saved successfully');
          } else {
            console.error('Error saving location:', xhr.status);
          }
        }
      };

      var params =
        'name=' +
        encodeURIComponent(placeName) +
        '&lng=' +
        encodeURIComponent(coordinates.lng) +
        '&lat=' +
        encodeURIComponent(coordinates.lat) +
        '&group_id=' +
        encodeURIComponent(group_id);

      xhr.send(params);
    }
  });
}
