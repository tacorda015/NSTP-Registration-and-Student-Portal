<?php
include('../connection.php');
$con = connection();
// Check if the connection was successful
if(mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Retrieve the list of teachers who are not in charge of any group from the database
$teacher_query = "SELECT * FROM teachertable WHERE group_id IS NULL OR group_id = ''";
$teacher_result = mysqli_query($con, $teacher_query);

// Create an array to store the teachers
$teachers = array();

// Loop through the query results and store the teachers in the array
while ($teacher_row = mysqli_fetch_assoc($teacher_result)) {
    $teachers[] = $teacher_row;
}

// Close the database connection
mysqli_close($con);

// Return the teachers in JSON format
echo json_encode($teachers);
?>
