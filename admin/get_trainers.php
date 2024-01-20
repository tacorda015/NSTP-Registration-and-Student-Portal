<?php
include('../connection.php');
$con = connection();
// Check if the connection was successful
if(mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Retrieve the list of trainers who are not in charge of any group from the database
$trainer_query = "SELECT * FROM trainertable WHERE group_id IS NULL OR group_id = ''";
$trainer_result = mysqli_query($con, $trainer_query);

// Create an array to store the trainers
$trainers = array();

// Loop through the query results and store the trainers in the array
while ($trainer_row = mysqli_fetch_assoc($trainer_result)) {
    $trainers[] = $trainer_row;
}

// Close the database connection
mysqli_close($con);

// Return the trainers in JSON format
echo json_encode($trainers);
?>
