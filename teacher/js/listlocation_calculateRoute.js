// Function to calculate the route and start watching position
function calculateRoute(destination) {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        showRouteClicked = true; // Set showRouteClicked flag
        watchPositionAndRoute();
        updateRoute(position);
      },
      function (error) {
        console.error('Error getting current location:', error);
      },
      {
        enableHighAccuracy: true,
      }
    );
  } else {
    console.error('Geolocation is not supported by this browser.');
  }
}
