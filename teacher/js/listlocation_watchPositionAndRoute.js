// Function to watch the position and update the route
function watchPositionAndRoute() {
  navigator.geolocation.watchPosition(
    function (position) {
      updateRoute(position);
    },
    function (error) {
      console.error('Error getting current location:', error);
    },
    {
      enableHighAccuracy: true,
    }
  );
}
