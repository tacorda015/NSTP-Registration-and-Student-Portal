<?php
include('../connection.php');
session_start();
$con = connection();

// Retrieve the location data from the request
$name = $_POST['name'];
$latitude = $_POST['lat'];
$longitude = $_POST['lng'];
$group_id = $_POST['group_id'];

// Prepare the insert statement
$query = "INSERT INTO activitylocation (location_name, location_latitude, location_longitude, group_id) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'sddd', $name, $latitude, $longitude, $group_id);

// Execute the insert statement
if (mysqli_stmt_execute($stmt)) {
    // Retrieve the newly inserted location ID
    $newLocationId = mysqli_insert_id($con);
    
    // Return the new location ID in the response
    $response = array('location_id' => $newLocationId);
    echo json_encode($response);
} else {
    echo "Error inserting location: " . mysqli_error($con);
}

// Close the connection
mysqli_close($con);
?>
