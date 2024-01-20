<?php
include('../connection.php');
$con = connection();
// Check if the connection was successful
if(mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Retrieve the list of groups from the database
$group_query = "SELECT * FROM grouptable WHERE component_id = 1";
$group_result = mysqli_query($con, $group_query);

// Create an array to store the groups
$groups = array();

// Loop through the query results and store the groups in the array
while ($group_row = mysqli_fetch_assoc($group_result)) {
    // Get the number of students allowed in the group
    $allowed_number = $group_row['number_student'];

    // Count the number of students in the group
    $current_number_query = "SELECT COUNT(*) as count FROM useraccount WHERE group_id = {$group_row['group_id']}";
    $current_number_result = mysqli_query($con, $current_number_query);
    $current_number_row = mysqli_fetch_assoc($current_number_result);
    $current_number = $current_number_row['count'];

    // Check if the number of students in the group is less than the number of allowed students
    if ($current_number < $allowed_number) {
        // If it is, add the group to the list of options
        $groups[] = $group_row;
    }
}

// Close the database connection
mysqli_close($con);

// Return the groups in JSON format
echo json_encode($groups);
?>
