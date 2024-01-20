<?php
include('../connection.php');
session_start();
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}

// get user data from session
$user_data = $_SESSION['user_data'];
$role = "SELECT * FROM roleaccount WHERE role_account_id = {$user_data['role_account_id']}";
$result = $con->query($role);
$role_data = $result->fetch_assoc();
$group_id = $user_data['group_id'];
// echo $group_id;

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('./teachersidebar.php');
?>
<style>
    .location-container .table-container {
  width: 100%;
  overflow-x: auto;
  max-height: 365px;
  overflow-y: auto;
  margin-top: 1rem;
  scroll-behavior: smooth;
}

.location-container .table-container::-webkit-scrollbar {
  display: none;
}

.location-container .responsive-table {
  width: 100%;
  border-collapse: collapse;
  background-color: #f5f5f5;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  table-layout: fixed; /* Add this line for sticky the thead */
}

.location-container .responsive-table th,
.location-container .responsive-table td {
  padding: 0.5rem;
  text-align: center;
  /* white-space: nowrap; Add this line for sticky the thead */
  overflow: hidden; /* Add this line for sticky the thead */
  text-overflow: ellipsis; /* Add this line for sticky the thead */
}

.location-container .responsive-table td {
  padding: 0.5rem;
  text-align: left;
  /* white-space: nowrap; Add this line for sticky the thead */
  overflow: hidden; /* Add this line for sticky the thead */
  text-overflow: ellipsis; /* Add this line for sticky the thead */
}

.location-container .responsive-table th {
  /* background-color: #adb5bd; */
  color: #e5f1f9;
  position: sticky; /* Add this line for sticky the thead */
  top: 0; /* Add this line for sticky the thead */
  /* z-index: 1; Add this line for sticky the thead */
  background-color: #3294c5;
}
.location-container .responsive-table td {
  text-align: left;
  font-weight: 500;
}
.location-container .responsive-table td:last-child {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.location-container .responsive-table tbody tr:nth-child(even) {
  background-color: #e5f1f9;
}
.location-container .responsive-table tbody tr:nth-child(odd) {
  background-color: #c5e2f2;
}

.location-container {
  width: 100%;
  margin: 0 2rem 1rem;
  padding: 3rem 2rem;
  height: calc(95vh - 7rem);
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 3rem;
  border: 1px solid #58aed8;
  background-color: #f2f9fd;
  border-radius: 8px;
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.5);
  position: relative;
}

.location-container .group-id {
  text-align: center;
  font-size: 1.5rem;
  word-wrap: break-word;
}

.location-container .header-container {
  width: 100%;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  height: fit-content;
}
.location-container .header-container .title-containers {
  width: 100%;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}
.location-container .header-container .title-containers:last-child {
  flex-direction: row-reverse;
}
.location-container .header-container {
  gap: 12px;
  display: flex;
  flex-direction: column;
}
.location-container span.search-icon {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 15px;
  height: 15px;
  background-size: 100%;
  cursor: pointer;
  background-image: url(../assets/img/xIcon2.png);
}
.location-container .title1-container {
  display: flex;
  align-items: center;
  flex-direction: row-reverse;
  white-space: nowrap;
}
.location-container .title1-container h2 {
  font-size: 1.75rem;
}
.location-container .title1-container label {
  margin-right: 10px;
  font-size: 1.2rem;
}
.location-container .title-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: center;
  margin-bottom: 0.75rem;
}
.location-container .title-container h2 {
  font-size: 2.5rem;
  color: #132c3e;
  padding-bottom: 1rem;
}
.location-container .action-btn {
  display: flex;
  gap: 5px;
}
@media screen and (max-width: 991px) and (min-width: 768px) {
  .location-container {
    display: block;
    justify-content: center;
    align-items: center;
    margin-top: 1rem;
  }
  .location-container .table-container {
    max-height: 365px;
  }

  .location-container .responsive-table::-webkit-scrollbar {
    display: none;
  }

  .location-container .responsive-table thead {
    display: block;
    position: absolute;
    top: -9999px;
    left: -9999px;
  }

  .location-container .responsive-table tr {
    border: 1px solid #3294c5;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    margin-bottom: 0.5rem;
  }

  .location-container .responsive-table td {
    border: none;
    display: inline-block;
    text-align: right;
    padding: 0.5rem;
    font-size: 1.1rem;
    width: 100%; /* Add this line */
    box-sizing: border-box; /* Add this line */
    white-space: normal; /* Add this line for sticky the thead */
  }
  .location-container .responsive-table td:last-child {
    display: block;
  }

  .location-container .responsive-table td::before {
    content: attr(data-label);
    float: left;
    font-weight: 600;
    text-transform: uppercase;
    margin-right: 0.5rem;
  }

  .location-container .title-container {
    text-align: center;
    margin-bottom: 1rem;
    min-width: 300px;
  }

  .location-container .title-container label {
    display: block;
    font-size: 1rem;
    margin-bottom: 0.5rem;
  }
  .location-container .title1-container {
    flex-direction: column;
  }
  .location-container .action-btn {
    justify-content: flex-end;
  }
}

@media screen and (max-width: 767px) {
  .location-container
    .header-container
    .title-containers
    .titles-container
    h2 {
    font-size: 1.2rem;
  }
  .location-container
    .header-container
    .title-containers
    .titles-container
    label {
    font-size: 0.75rem;
    text-align: center;
  }

  .location-container {
    position: relative;
    width: 100%;
    height: 85vh;
    display: block;
    justify-content: center;
    align-items: center;
    margin-top: 0;
    padding: 1rem;
  }
  .location-container .table-container {
    max-height: 45vh;
  }
  .location-container .responsive-table::-webkit-scrollbar {
    display: none;
  }

  .location-container .responsive-table thead {
    display: block;
    position: absolute;
    top: -9999px;
    left: -9999px;
  }
  .location-container .responsive-table td:last-child {
    display: block;
  }

  .location-container .responsive-table tr {
    border: 1px solid #ccc;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 0.5rem;
  }

  .location-container .responsive-table td {
    border: none;
    display: inline-block;
    text-align: right;
    padding: 0.5rem;
    font-size: 12px;
    width: 100%; /* Add this line */
    box-sizing: border-box; /* Add this line */
    white-space: normal; /* Add this line for sticky the thead */
  }

  .location-container .responsive-table td::before {
    content: attr(data-label);
    float: left;
    font-weight: bold;
    text-transform: uppercase;
    margin-right: 0.5rem;
  }

  .location-container .responsive-table tbody tr:nth-child(even) {
    background-color: white;
  }

  .location-container .title-container {
    margin: 0;
    padding-bottom: 10px;
  }

  .location-container .title-container h2 {
    font-size: 1.5rem;
    padding: 0;
  }

  .location-container .title-container label {
    display: block;
    font-size: 1rem;
    margin-bottom: 0.5rem;
  }
  .location-container .title1-container {
    flex-direction: column;
  }
  .location-container .action-btn {
    justify-content: flex-end;
  }
}
#map {
        height: 400px;
        width: 100%;
      }

      .marker-button {
        margin: 10px 0;
        position: absolute;
        top: 6rem;
        left: 2.6rem;
        /* z-index: 1; */
        padding: 10px;
      }

      .location-table {
        border-collapse: collapse;
        width: 100%;
      }

      path{
        fill: #30A2FF;
      }

      .location-table th, .location-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
      }

      .location-table th {
        background-color: #f2f2f2;
      }

      .location-table tr:nth-child(even) {
        background-color: #f9f9f9;
      }
      .mapboxgl-ctrl-geocoder input[type='text']{
        padding: 6px 35px !important;
        background-color: transparent;

      }
      .instructions-container::-webkit-scrollbar{
        display: none;
      }

      .instructions-container p {
        margin: 0;
        padding: 5px 0;
      }
      #instructions{
        font-weight: 500;
      }

      .instructions-container.minimized {
        height: 0;
        padding: 0;
        margin: 0;
      }

      .intruc {
        position: absolute;
        top: 100px;
        left: 0;
        margin-top: 10px;
      }

      .instructions-container {
        position: relative;
        z-index: 1;
        background-color: #fff6b8;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
        margin-left: 10px;
      }

      .toggle-instructions-button {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        z-index: 2;
        display: block;
        transition: all 0.3s ease; /* Add a transition for smooth movement */
      }

      .toggle-instructions-button.left {
        right: auto;
        left: 10px;
      }


      .instructions-container.minimized .instructions-content {
      display: none;
      }

      .instructions-container.minimized .toggle-instructions-button {
        top: 10px;
        left: 10px;
      }

      .instructions-content {
        display: block;
      }
      .mapboxgl-ctrl-geocoder--icon{
        top: 10px;
      }
</style>
        <div class="home-main-container">
            <div class="location-container">
            <div id="map"></div>
            <button class="marker-button" onclick="goToMyLocation()">Go to My Location</button>
            <div class="intruc" style="display: none;">
            <button class="toggle-instructions-button">-</button>
            <div class="instructions-container">
                <div id="instructions" class="instructions-content"></div>
            </div>
            </div>
            <?php
                // Fetch all locations from the database
                $query = "SELECT * FROM activitylocation WHERE group_id = $group_id";
                $result = mysqli_query($con, $query);

                // Check if any locations were found
                if (mysqli_num_rows($result) > 0) {
                    // Display the table header with CSS classes for styling
                    echo "<table class='location-table'>";
                    echo "<tr><th>Location Name</th><th>Show Routes</th><th>Action</th></tr>";

                    // Loop through the locations and display them in table rows with CSS classes
                    while ($row = mysqli_fetch_assoc($result)) {
                        $name = $row['location_name'];
                        $latitude = $row['location_latitude'];
                        $longitude = $row['location_longitude'];
                        $location_id = $row['location_id'];
                        $publish = $row['publish'];

                        echo "<tr>";
                        echo "<td>" . $name . "</td>";
                        echo "<td><button type='button' class='show-routes-button' data-lat='" . $latitude . "' data-lng='" . $longitude . "'>Show Routes</button>
                              <button type='button' class='cancel-button' style='display:none;'>Cancel</button></td>";

                        // Publish button
                        if ($publish == 1) {
                            echo "<td><button type='button' class='publish-button' data-location-id='" . $location_id . "' data-publish='0'>Unpublish</button>
                                  <button type='button' class='delete-button' data-location-id='" . $location_id . "'>Delete</button></td>";
                        } else {
                            echo "<td><button type='button' class='publish-button' data-location-id='" . $location_id . "' data-publish='1'>Publish</button>
                                  <button type='button' class='delete-button' data-location-id='" . $location_id . "'>Delete</button></td>";
                        }

                        echo "</tr>";
                    }

                    // Close the table
                    echo "</table>";
                } else {
                    echo "No locations found.";
                }
                ?>

            </div>
        </div>
    </section>
</div>
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
        <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.js"></script>
        <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
          <script>
              // JavaScript function to handle the publish button click
              function updatePublishStatus(locationId, publishStatus) {
                  // Send an AJAX request to update the publish status
                  var xhr = new XMLHttpRequest();
                  xhr.open("POST", "update_publish.php", true);
                  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                  xhr.onreadystatechange = function () {
                      if (xhr.readyState === 4 && xhr.status === 200) {
                          // Reload the page after successful update
                          location.reload();
                      }
                  };
                  xhr.send("location_id=" + locationId + "&publish_status=" + publishStatus);
              }

              // Add event listener to publish buttons
              var publishButtons = document.getElementsByClassName("publish-button");
              for (var i = 0; i < publishButtons.length; i++) {
                  publishButtons[i].addEventListener("click", function () {
                      var locationId = this.getAttribute("data-location-id");
                      var publishStatus = this.getAttribute("data-publish");
                      updatePublishStatus(locationId, publishStatus);
                  });
              }
          </script>
          <script>
              // JavaScript function to handle the delete button click
              function deleteLocation(locationId) {
                  if (confirm("Are you sure you want to delete this location?")) {
                      // Send an AJAX request to delete the location
                      var xhr = new XMLHttpRequest();
                      xhr.open("POST", "delete_location.php", true);
                      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                      xhr.onreadystatechange = function () {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                              // Reload the page after successful deletion
                              location.reload();
                          }
                      };
                      xhr.send("location_id=" + locationId);
                  }
              }

              // Add event listener to delete buttons
              var deleteButtons = document.getElementsByClassName("delete-button");
              for (var i = 0; i < deleteButtons.length; i++) {
                  deleteButtons[i].addEventListener("click", function () {
                      var locationId = this.getAttribute("data-location-id");
                      deleteLocation(locationId);
                  });
              }
          </script>
        <script>
        mapboxgl.accessToken ='pk.eyJ1IjoidGFjb3JkYTAxNSIsImEiOiJjbGdmbm56bmkwM2EyM3Ntd25scGNwNDR5In0.IrsFy2lTHKUeSZsuAbDCNA';

        var map = new mapboxgl.Map({
          container: 'map',
          style: 'mapbox://styles/mapbox/streets-v11',
          center: [120.864249, 14.4129026], // Longitude, Latitude of the map center
          zoom: 16, // Initial zoom level
        });
        map.addControl(new mapboxgl.NavigationControl());

        // Add the control to the map. For Search
        const geocoder = new MapboxGeocoder({
          accessToken: mapboxgl.accessToken,
          mapboxgl: mapboxgl,
        });

        map.addControl(geocoder, 'top-left');

        var marker = null;

        // Add a marker on map click
        map.on('click', function (e) {
          var coordinates = e.lngLat;

          // Remove the previous marker
          if (marker) {
          marker.remove();
          }

          // Create a new marker at the clicked location
          marker = new mapboxgl.Marker({ color: '#ff0000' })
          .setLngLat(coordinates)
          .addTo(map);

          // Reverse geocode to get location name
          fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${coordinates.lng},${coordinates.lat}.json?access_token=${mapboxgl.accessToken}`)
          .then(function (response) {
            return response.json();
          })
          .then(function (data) {
            var placeName = data.features[0].place_name;

            // Create a popup with the location name and a button to add activity
            var popupContent =
            '<div style="display: grid;">' +
            '<p>' +
            placeName +
            '</p>' +
            '<button class="add-activity-button">Add Activity Location</button>' +
            '</div>';

            var popup = new mapboxgl.Popup().setHTML(popupContent);

            // Open the popup on marker click
            marker.setPopup(popup).togglePopup();

            // Handle add activity button click
            var addActivityButton = document.querySelector('.add-activity-button');
            addActivityButton.addEventListener('click', function () {
            // Save the location to the database
            saveLocationToDatabase(coordinates, placeName, <?php echo $group_id; ?>);
            });
          })
          .catch(function (error) {
            console.error('Error:', error);
          });
        });

      
        var showRouteClicked = false;

        // Get current location and add a marker
        function trackLocation() {
          // Check if the Geolocation API is supported by the browser
          if ('geolocation' in navigator) {
            // Watch the user's position and update the marker accordingly
            navigator.geolocation.watchPosition(
              function (position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                // Remove the previous marker if it exists
                if (marker) {
                  marker.remove();
                }

                // Create a new marker at the current location
                marker = new mapboxgl.Marker({ color: '#ff0000' })
                .setLngLat([longitude, latitude])
                .addTo(map);

                // Only center the map if the "Show Routes" button is not active
                // if (!showRouteClicked && !userLocationInitialized) {
                //   map.flyTo({ center: [longitude, latitude], zoom: 15 });
                // }
                if (showRouteClicked) {
                  startMarker.getElement().style.color = '#ff0000';
                }
              },
              function (error) {
                console.error('Error tracking location:', error);
              },
              {
                enableHighAccuracy: true, // Enable high accuracy for better location precision
              }
            );
          } else {
            console.error('Geolocation is not supported by this browser.');
          }
        }

        // Call the trackLocation() function when the "Go to My Location" button is clicked
        var markerButton = document.querySelector('.marker-button');
        markerButton.addEventListener('click', function () {
          trackLocation();
        });

        // Call the trackLocation() function when the map loads
        map.on('load', function () {
          trackLocation();
        });

        // Function to save location to the database
        function saveLocationToDatabase(coordinates, placeName, group_id) {
          // Send an AJAX request to your server to save the location data to the database
          var xhr = new XMLHttpRequest();
          xhr.open('POST', 'save-location.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

          xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
              if (xhr.status === 200) {
                location.reload();
                console.log('Location saved successfully');
              } else {
                console.error('Error saving location:', xhr.status);
              }
            }
          };

          var params = 'name=' + encodeURIComponent(placeName) +
          '&lng=' + encodeURIComponent(coordinates.lng) +
          '&lat=' + encodeURIComponent(coordinates.lat) +
          '&group_id=' + encodeURIComponent(group_id);

          xhr.send(params);
        }

        //     // Start of displaying the routes

        var startMarker = null;
        var endMarker = null;
        var routeSource = null;
        var currentDestination = null;

        // Function to clear previous markers and route
        function clearMarkersAndRoute() {
          if (startMarker) {
            startMarker.remove();
            startMarker = null;
          }
          if (endMarker) {
            endMarker.remove();
            endMarker = null;
          }
          if (routeSource) {
            map.removeLayer('route');
            map.removeSource('route');
            routeSource = null;
          }
        }

        // Get all the "Show Routes" buttons
        var showRoutesButtons = document.querySelectorAll('.show-routes-button');

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

            var cancelButton = button.parentNode.querySelector('.cancel-button');
            cancelButton.style.display = 'block';
          });
        });

        // Create the URL for the Mapbox Directions API request
        function createDirectionsURL(startLng, startLat, destinationLng, destinationLat) {
          return `https://api.mapbox.com/directions/v5/mapbox/cycling/${startLng},${startLat};${destinationLng},${destinationLat}?geometries=geojson&steps=true&access_token=${mapboxgl.accessToken}`;
        }


        // Define a variable to track if the route has been shown
        var routeShown = false;

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
          if (marker) {
          marker.remove();
        }

          // Create marker for start location
          startMarker = new mapboxgl.Marker({ color: '#00ff00' })
          .setLngLat([startLng, startLat])
          .addTo(map);

          // Create marker for end location
          endMarker = new mapboxgl.Marker({ color: '#ff0000' })
          .setLngLat([currentDestination.lng, currentDestination.lat])
          .addTo(map);

          // Calculate the distance between the user's location and the destination
          var distance = calculateDistance(
            startLat,
            startLng,
            currentDestination.lat,
            currentDestination.lng
          );

          // Define the radius for triggering the notification
          var notificationRadius = 30; // 10 meters

          // If the distance is less than or equal to the radius, display a notification
          if (distance <= notificationRadius) {
            alert('You have reached the area!');
            // Perform additional actions upon reaching the destination
            clearMarkersAndRoute();
            currentDestination = null;
          }



          // Remove the previous route layer from the map
          if (routeSource) {
            map.removeLayer('route');
            map.removeSource('route');
          }

          // Create the URL for the Mapbox Directions API request
          var directionsUrl = createDirectionsURL(startLng, startLat, currentDestination.lng, currentDestination.lat);

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

            // // Fit the map view to the route
            // var bounds = route.reduce(function (bounds, coord) {
            //   return bounds.extend(coord);
            // }, new mapboxgl.LngLatBounds(route[0], route[0]));

            // map.fitBounds(bounds, {
            //   padding: 60,
            // });
            // Fit the map view to the route
            // Fit the map view to the route on the first show route click
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


        //   Start of cancelation of showing the Path
        var cancelButtons = document.querySelectorAll('.cancel-button');
        var intruc = document.querySelector('.intruc');
        var routeDisplayed = false;
        var currentDestination = null;

        cancelButtons.forEach(function (button) {
          button.addEventListener('click', function () {
            clearMarkersAndRoute();
            intruc.style.display = 'none';
            button.style.display = 'none';
            currentDestination = null;
          });
        });

        document.addEventListener('keydown', handleKeyDown);

        function cancelRoute() {
          clearMarkersAndRoute();
          intruc.style.display = 'none';
          button.style.display = 'none';
          routeDisplayed = false;
          currentDestination = null;
        }

        function handleKeyDown(event) {
          if (event.keyCode === 27) {
            // Esc key pressed
            cancelRoute();
          }
        }

        


        // Get the toggle instructions button
        var toggleInstructionsButton = document.querySelector('.toggle-instructions-button');

        // Get the instructions container
        var instructionsContainer = document.querySelector('.instructions-container');

        // Get the instructions content
        var instructionsContent = document.querySelector('.instructions-content');

        // Add event listener to the button
        toggleInstructionsButton.addEventListener('click', function () {
          // Check if the instructions container is currently minimized
          if (instructionsContainer.classList.contains('minimized')) {
            // If it is minimized, remove the 'minimized' class to maximize it
            instructionsContainer.classList.remove('minimized');
            toggleInstructionsButton.textContent = '-';
            toggleInstructionsButton.classList.remove('left');
          } else {
            // If it is not minimized, add the 'minimized' class to minimize it
            instructionsContainer.classList.add('minimized');
            toggleInstructionsButton.textContent = '+';
            toggleInstructionsButton.classList.add('left');
          }
        });

        function goToMyLocation() {
          // Check if the Geolocation API is supported by the browser
          if ('geolocation' in navigator) {
            // Get the user's current position
            navigator.geolocation.watchPosition(
              function (position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                // Center the map on the current location
                map.flyTo({ center: [longitude, latitude], zoom: 15 });
              },
              function (error) {
                console.error('Error getting current location:', error);
              },
              {
                enableHighAccuracy: true, // Enable high accuracy for better location precision
              }
            );
          } else {
            console.error('Geolocation is not supported by this browser.');
          }
        }

        // Call the goToMyLocation() function when the "Go to My Location" button is clicked
        var goToMyLocationButton = document.getElementById('goToMyLocationButton');
        goToMyLocationButton.addEventListener('click', function () {
          goToMyLocation();
        });

       



        </script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>