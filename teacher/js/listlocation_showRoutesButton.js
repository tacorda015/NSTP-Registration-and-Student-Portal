// Get all the "Show Routes" buttons
var showRoutesButtons = document.querySelectorAll('.show-routes-button');

// Initialize a variable to keep track of the currently displayed route
var currentRouteButton = null;

// Add event listener to each button
showRoutesButtons.forEach(function (button) {
  button.addEventListener('click', function () {
    var instructionsContainer = document.querySelector('.intruc');
    instructionsContainer.style.display = 'block';

    // Set the new destination
    var destinationLng = parseFloat(button.dataset.lng);
    var destinationLat = parseFloat(button.dataset.lat);
    currentDestination = { lng: destinationLng, lat: destinationLat };

    // Calculate the route
    calculateRoute(currentDestination);
    routeDisplayed = true; // Update the state of the "Show Routes" button
    routeShown = false;

    // Hide the "Cancel" button of the previous "Show Routes" if it exists
    if (currentRouteButton) {
      var previousCancelButton =
        currentRouteButton.parentNode.querySelector('.cancel-button');
      previousCancelButton.style.display = 'none';
    }

    // Show the "Cancel" button of the current "Show Routes"
    var cancelButton = button.parentNode.querySelector('.cancel-button');
    cancelButton.style.display = 'block';

    // Update the current route button
    currentRouteButton = button;
  });
});
