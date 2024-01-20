function goToMyLocation() {
  var marker = null; // Initialize marker variable
  var flyAnimation = null; // Initialize fly animation variable

  // Check if the Geolocation API is supported by the browser
  if ('geolocation' in navigator) {
    // Get the user's current position
    navigator.geolocation.watchPosition(
      function (position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        // Create a new marker at the current location if it doesn't exist
        if (!marker) {
          marker = new mapboxgl.Marker()
            .setLngLat([longitude, latitude])
            .addTo(map);
        } else {
          // If the marker already exists, update its position
          marker.setLngLat([longitude, latitude]);
        }

        // Cancel any ongoing fly animation
        if (flyAnimation) {
          flyAnimation.cancel();
        }

        // Fly to the current location
        flyAnimation = map.flyTo({
          center: [longitude, latitude],
          zoom: 15,
          essential: true, // Animate the flyTo transition
        });
      },
      function (error) {
        console.error('Error getting current location:', error);
      },
      {
        enableHighAccuracy: true, // Enable high accuracy for better location precision
      }
    );

    // Remove the marker when the map starts to be dragged
    map.on('dragstart', function () {
      //   if (marker) {
      //     marker.remove();
      //     marker = null;
      //   }
      if (flyAnimation) {
        flyAnimation.cancel();
        flyAnimation = null;
      }
      map.off('moveend', handleMapMoveEnd);
    });
  } else {
    console.error('Geolocation is not supported by this browser.');
  }
}
