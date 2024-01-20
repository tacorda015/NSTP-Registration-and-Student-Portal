// Function to update the route based on current position
function updateRoute(position) {
  var startLng = position.coords.longitude;
  var startLat = position.coords.latitude;

  // Remove previous markers
  if (startMarker) {
    startMarker.remove();
  }
  if (endMarker) {
    endMarker.remove();
  }

  // Create marker for start location
  startMarker = new mapboxgl.Marker()
    .setLngLat([startLng, startLat])
    .addTo(map);

  // Create marker for end location
  endMarker = new mapboxgl.Marker()
    .setLngLat([currentDestination.lng, currentDestination.lat])
    .addTo(map);

  // Calculate the distance between two coordinates using the Haversine formula
  function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius of the Earth in kilometers
    const dLat = toRadians(lat2 - lat1);
    const dLon = toRadians(lon2 - lon1);
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(toRadians(lat1)) *
        Math.cos(toRadians(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c * 1000; // Convert to meters
    return distance;
  }

  // Convert degrees to radians
  function toRadians(degrees) {
    return degrees * (Math.PI / 180);
  }

  // Calculate the distance between the user's location and the destination
  var distance = calculateDistance(
    startLat,
    startLng,
    currentDestination.lat,
    currentDestination.lng
  );

  // Define the radius for triggering the notification
  var notificationRadius = 50; // 50 meters

  // If the distance is less than or equal to the radius, display a notification
  if (distance <= notificationRadius) {
    Swal.fire({
      title: 'You have reached the area!',
      icon: 'success',
      confirmButtonText: 'OK',
    }).then(function () {
      // Perform additional actions upon reaching the destination
      clearMarkersAndRoute();
      currentDestination = null;

      // Hide the cancel button of the current route
      var cancelButton =
        currentRouteButton.parentNode.querySelector('.cancel-button');
      cancelButton.style.display = 'none';
    });
  }
  // Remove the previous route layer from the map
  if (routeSource) {
    map.removeLayer('route');
    map.removeSource('route');
  }

  // Create the URL for the Mapbox Directions API request
  var directionsUrl = createDirectionsURL(
    startLng,
    startLat,
    currentDestination.lng,
    currentDestination.lat
  );

  // Fetch the route data
  fetch(directionsUrl)
    .then(function (response) {
      return response.json();
    })
    .then(function (data) {
      var route = data.routes[0].geometry.coordinates;
      var steps = data.routes[0].legs[0].steps;

      // Create a GeoJSON source with the route coordinates
      var routeGeoJSON = {
        type: 'Feature',
        properties: {},
        geometry: {
          type: 'LineString',
          coordinates: route,
        },
      };

      // Add the route to the map as a new layer
      routeSource = {
        type: 'geojson',
        data: routeGeoJSON,
      };

      map.addSource('route', routeSource);

      map.addLayer({
        id: 'route',
        type: 'line',
        source: 'route',
        layout: {
          'line-join': 'round',
          'line-cap': 'round',
        },
        paint: {
          'line-color': '#00C4FF',
          'line-width': 5,
        },
      });

      if (!routeShown) {
        var bounds = route.reduce(function (bounds, coord) {
          return bounds.extend(coord);
        }, new mapboxgl.LngLatBounds(route[0], route[0]));

        map.fitBounds(bounds, {
          padding: 60,
        });
        // Set the routeShown flag to true
        routeShown = true;
      }

      var instructionsContainer = document.getElementById('instructions');
      instructionsContainer.innerHTML = '';

      var totalDistance = 0;
      var totalDuration = 0;

      steps.forEach(function (step) {
        var instruction = step.maneuver.instruction;
        var distance = step.distance;
        var duration = step.duration;

        var distanceString = getFormattedDistance(distance);
        var durationString = getFormattedDuration(duration);

        instructionsContainer.innerHTML += `
        <div class="instruction-item" style= "border: 1px solid black; max-width: 200px; padding: 2px 10px;">
        <p class="instruction-text">${instruction}</p>
        <p class="distance-text">Distance: ${distanceString}</p>
        <p class="duration-text">Duration: ${durationString}</p>
        </div>`;
      });

      // Function to format the distance as kilometers and meters
      function getFormattedDistance(distance) {
        var kilometers = Math.floor(distance / 1000);
        var meters = Math.round(distance % 1000);

        if (kilometers > 0) {
          return kilometers + ' km ' + meters + ' m';
        } else {
          return meters + ' m';
        }
      }

      // Function to format the duration as hours, minutes, and seconds
      function getFormattedDuration(duration) {
        var hours = Math.floor(duration / 3600);
        var minutes = Math.floor((duration % 3600) / 60);
        var seconds = Math.round(duration % 60);

        var durationString = '';

        if (hours > 0) {
          durationString += hours + ' hr ';
        }

        if (minutes > 0) {
          durationString += minutes + ' min ';
        }

        if (seconds > 0) {
          durationString += seconds + ' sec';
        }

        return durationString;
      }
      // Show the instructions container
      instructionsContainer.style.display = 'block';
    })
    .catch(function (error) {
      console.error('Error calculating route:', error);
    });
}
