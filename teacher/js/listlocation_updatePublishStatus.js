// // JavaScript function to handle the publish button click
// function updatePublishStatus(locationId, publishStatus) {
//   // Send an AJAX request to update the publish status
//   var xhr = new XMLHttpRequest();
//   xhr.open('POST', 'update_publish.php', true);
//   xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//   xhr.onreadystatechange = function () {
//     if (xhr.readyState === 4 && xhr.status === 200) {
//       // Update the publish button text and data-publish attribute
//       var publishButton = document.querySelector(
//         '[data-location-id="' + locationId + '"]'
//       );
//       if (publishButton) {
//         publishButton.setAttribute('data-publish', publishStatus);
//         publishButton.textContent =
//           publishStatus === '1' ? 'Unpublish' : 'Publish';
//       }
//     }
//   };
//   xhr.send('location_id=' + locationId + '&publish_status=' + publishStatus);
// }

// // Add event listener to publish buttons
// var publishButtons = document.getElementsByClassName('publish-button');
// for (var i = 0; i < publishButtons.length; i++) {
//   publishButtons[i].addEventListener('click', function () {
//     var locationId = this.getAttribute('data-location-id');
//     var publishStatus = this.getAttribute('data-publish');
//     var newPublishStatus = publishStatus === '1' ? '0' : '1';
//     updatePublishStatus(locationId, newPublishStatus);
//   });
// }

// JavaScript function to handle the publish button click
function updatePublishStatus(locationId, publishStatus) {
  // Send an AJAX request to update the publish status
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'update_publish.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      console.log(xhr.readyState, xhr.status, xhr.responseText);
      // Parse the response message
      var response = xhr.responseText.trim();

      // Display SweetAlert based on the response
      if (response === 'success') {
        var successMessage =
          publishStatus === '1' ? 'Unpublished' : 'Published';
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: 'Location ' + successMessage + ' successfully.',
        }).then(function () {
          // Reload the page after a successful update
          location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred while updating publish status.',
        });
      }
    }
  };
  xhr.send('location_id=' + locationId + '&publish_status=' + publishStatus);
}

// Add event listener to publish buttons
var publishButtons = document.getElementsByClassName('publish-button');
for (var i = 0; i < publishButtons.length; i++) {
  publishButtons[i].addEventListener('click', function () {
    var locationId = this.getAttribute('data-location-id');
    var publishStatus = this.getAttribute('data-publish');
    var newPublishStatus = publishStatus === '1' ? '0' : '1';
    updatePublishStatus(locationId, newPublishStatus);
  });
}
