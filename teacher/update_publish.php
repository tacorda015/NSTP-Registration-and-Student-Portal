<?php
include('../connection.php');
session_start();
$con = connection();
$user_data = $_SESSION['user_data'];

$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = {$user_id}";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$group_id = $useraccount_data['group_id'];


if (isset($_POST['location_id']) && isset($_POST['publish_status'])) {
    $locationId = $_POST['location_id'];
    $publishStatus = $_POST['publish_status'];
    error_log("Debug: Location ID - " . $locationId . " | Publish Status - " . $publishStatus);
    
    if ($publishStatus == 1){
        // Perform the update query for the selected location
        $query = "UPDATE activitylocation SET publish = 0 WHERE location_id = $locationId";
    }else{
        $updateAllQuery = "UPDATE activitylocation SET publish = 0 WHERE group_id = $group_id AND publish = 1";
        mysqli_query($con, $updateAllQuery);

        // Perform the update query for the selected location
        $query = "UPDATE activitylocation SET publish = 1 WHERE location_id = $locationId";
    }
    

    // Execute the query
    $result = mysqli_query($con, $query);

    if ($result) {
        // Update successful
        // echo "Publish status updated successfully.";
        // echo "error_log(\"Debug: Location ID - \" . $locationId . \" | Publish Status - \" . $publishStatus);";
        echo"success";
    } else {
        // Update failed
        // echo "Error updating publish status: " . mysqli_error($con);
        echo "error";
    }
} else {
    // location_id or publish_status parameter is missing
    echo "Invalid request. Please provide both location ID and publish status.";
}
?>
