<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}

// get user data from session
$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = $user_id";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$role_account_id = $useraccount_data['role_account_id'];
$group_id = $useraccount_data['group_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
} 

// Calling the side bar
include_once('./studentsidebar.php');
?>
                <div class="home-main-container">
                    <div class="studentList-containers">
                        <div id="map"></div>
                        <div class="buttonsContainer">
                            <div class="buttonHolder">
                                <button class="marker-button btn btn-primary" id="goToMyLocationButton" onclick="goToMyLocation()">
                                    <i class='bx bx-street-view' style="font-size: 24px;"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="intruc" style="display: none;">
                            <!-- <button class="toggle-instructions-button">-</button>
                            <div class="instructions-container">
                            <div id="instructions" class="instructions-content"></div>
                            </div> -->
                        </div>
                        <?php
                            if(!empty($group_id)){
                                // Fetch all locations from the database
                                $query = "SELECT * FROM activitylocation WHERE group_id = $group_id AND publish = 1";
                                $result = mysqli_query($con, $query);

                                // Check if any locations were found
                                if (mysqli_num_rows($result) > 0) {
                                    // Display the table header with CSS classes for styling
                                    echo "<div class='tableContainer'>";
                                    echo "<table class='table table-sm caption-top'>";
                                    echo "<caption>List of Location</caption>";
                                    echo "<thead class=\"custom-thead\"><tr><th>Location Name</th><th class='thAction'>Show Routes</th></tr></thead>";

                                        // Loop through the locations and display them in table rows with CSS classes
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $name = $row['location_name'];
                                            $latitude = $row['location_latitude'];
                                            $longitude = $row['location_longitude'];
                                            $location_id = $row['location_id'];

                                            echo "<tr id='locationRow" . $location_id . "'>";
                                            echo "<td data-label='Location Name'>" . $name . "</td>";
                                            echo "<td data-label='Show Routes'>
                                                    <div class = 'groupButton'>
                                                        <button type='button' class='show-routes-button btn btn-primary' data-lat='" . $latitude . "' data-lng='" . $longitude . "'>
                                                            <i class='bx bx-run'></i>Routes
                                                        </button>
                                                        <button type='button' class='cancel-button btn btn-warning' style='display:none; text-indent: 1rem''>
                                                            <i class='bx bx-minus-circle'></i>Cancel
                                                        </button>
                                                    </div>
                                                </td>";
                                            echo "</tr>";
                                    }
                                        echo "</table>";
                                    echo "</div>";
                                } else {
                                    echo "<h2 style = 'text-align:center; padding-top:1rem'>No Activity locations found.</h2>";
                                }
                            }else{
                                echo"<h2 style='text-align:center'>No Assigned Group Yet</h2>";
                            }
                        ?>
                        <div style="opacity: 0; height: 2rem;">Sample Divider</div>
                    </div>
                </div>
            </section>
        </div>
        
        <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
        <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.js"></script>
        <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
        <script src="../teacher/js/listlocation_goToMyLocation.js"></script>
        <script src="../teacher/js/listlocation_updateRoute.js"></script>
        <script src="../teacher/js/listlocation_watchPositionAndRoute.js"></script>
        <script src="../teacher/js/listlocation_showRoutesButton.js"></script>
        <script src="../teacher/js/listlocation_calculateRoute.js"></script>
        <script>
        mapboxgl.accessToken ='pk.eyJ1IjoidGFjb3JkYTAxNSIsImEiOiJjbGdmbm56bmkwM2EyM3Ntd25scGNwNDR5In0.IrsFy2lTHKUeSZsuAbDCNA';
        var showRouteClicked = false;
        var routeShown = false;
        var marker = null;
        var startMarker = null;
        var endMarker = null;
        var routeSource = null;
        var currentDestination = null;
        var cancelButtons = document.querySelectorAll('.cancel-button');
        var intruc = document.querySelector('.intruc');
        var routeDisplayed = false;
        var currentDestination = null;
        // Get the toggle instructions button
        var toggleInstructionsButton = document.querySelector('.toggle-instructions-button');
        // Get the instructions container
        var instructionsContainer = document.querySelector('.instructions-container');
        // Get the instructions content
        var instructionsContent = document.querySelector('.instructions-content');

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
        // Add a marker on map click
        map.on('click', function (e) {
            var coordinates = e.lngLat;

            // Remove the previous marker
            if (marker) {
            marker.remove();
            }

            // Create a new marker at the clicked location
            marker = new mapboxgl.Marker()
            .setLngLat(coordinates)
            .addTo(map);

            // Reverse geocode to get location name
            fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${coordinates.lng},${coordinates.lat}.json?access_token=${mapboxgl.accessToken}`)
            .then(function (response) {
            return response.json();
            }).then(function (data) {
            var placeName = data.features[0].place_name;

            // Create a popup with the location name and a button to add activity
            var popupContent = '<div style="display: grid;">' + '<p>' + placeName + '</p>' + '</div>';

            var popup = new mapboxgl.Popup().setHTML(popupContent);

            // Open the popup on marker click
            marker.setPopup(popup).togglePopup();

            // Handle add activity button click
            var addActivityButton = document.querySelector('.add-activity-button');
            addActivityButton.addEventListener('click', function () {
                // Save the location to the database
                saveLocationToDatabase(coordinates, placeName, <?php echo $group_id; ?>);
            });
            }).catch(function (error) {
            console.error('Error:', error);
            });
        });

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
        // Create the URL for the Mapbox Directions API request
        function createDirectionsURL(startLng, startLat, destinationLng, destinationLat) {
            return `https://api.mapbox.com/directions/v5/mapbox/cycling/${startLng},${startLat};${destinationLng},${destinationLat}?geometries=geojson&steps=true&access_token=${mapboxgl.accessToken}`;
        }
        var currentCancelButton;

        cancelButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            clearMarkersAndRoute();
            intruc.style.display = 'none';
            button.style.display = 'none';
            currentDestination = null;
            currentCancelButton = button; // Assign the clicked button to the global variable
        });
        });
        function cancelRoute() {
        clearMarkersAndRoute();
        intruc.style.display = 'none';
        if (currentCancelButton) {
            currentCancelButton.style.display = 'none';
        }
        routeDisplayed = false;
        currentDestination = null;
        }

        document.addEventListener('keydown', function (event) {
        if (event.keyCode === 27) {
            cancelRoute();
        }
        });
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
        </script>
        <script src="../asset/js/index.js"></script>
        <script src="../asset/js/topbar.js"></script>
    </body>
</html>