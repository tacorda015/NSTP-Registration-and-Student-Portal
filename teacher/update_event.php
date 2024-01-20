<?php
session_start();
include('../connection.php');
$con = connection();

// Check if event_id is provided in the POST request
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $event_title = $_POST['event_title'];
    $event_start = $_POST['event_start'];
    $event_end = $_POST['event_end'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $newLocationName = $_POST['newLocationName'];


    if(empty($start_time) && empty($end_time)){
        $sql = "UPDATE scheduletable SET location_id = '$newLocationName', schedule_title = '$event_title', schedule_date = '$event_start', schedule_date_end = '$event_end' WHERE schedule_id = '$event_id'";
    }elseif(!empty($start_time) && empty($end_time)){
        $sql = "UPDATE scheduletable SET location_id = '$newLocationName', schedule_title = '$event_title', schedule_date = '$event_start', schedule_date_end = '$event_end', schedule_start = '$start_time' WHERE schedule_id = '$event_id'";
    }elseif(empty($start_time) && !empty($end_time)){
        $sql = "UPDATE scheduletable SET location_id = '$newLocationName', schedule_title = '$event_title', schedule_date = '$event_start', schedule_date_end = '$event_end', schedule_end = '$end_time' WHERE schedule_id = '$event_id'";
    }else{
        $sql = "UPDATE scheduletable SET location_id = '$newLocationName', schedule_title = '$event_title', schedule_date = '$event_start', schedule_date_end = '$event_end', schedule_start = '$start_time', schedule_end = '$end_time' WHERE schedule_id = '$event_id'";
    }
    
    $sqlresult = $con->query($sql);

    if ($sqlresult) {
        // Deletion successful
        $response = [
            "status" => true,
            "msg" => "Schedule Successful Update."
        ];
    } else {
        // Error in deletion
        $response = [
            "status" => false,
            "msg" => "Error Updating Schedule."
        ];
    }
} else {
    // Event ID not provided in the request
    $response = [
        "status" => false,
        "msg" => "Schedule ID not provided."
    ];
}

// Send the response back as JSON
header("Content-type: application/json");
echo json_encode($response);
?>