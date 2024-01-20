<?php
session_start();
include('../connection.php');
$con = connection();

// Check if event_id is provided in the POST request
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $schedule = $_POST['schedule'];

    // Prepare and execute the SQL statement to delete the event
    if($schedule){
        $sql = "DELETE FROM scheduletable WHERE schedule_id = ?";
    }else{
        $sql = "DELETE FROM calendar_event WHERE event_id = ?";
    }
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        // Deletion successful
        $response = [
            "status" => true,
            "msg" => "Event deleted successfully."
        ];
    } else {
        // Error in deletion
        $response = [
            "status" => false,
            "msg" => "Error deleting the event."
        ];
    }

    $stmt->close();
} else {
    // Event ID not provided in the request
    $response = [
        "status" => false,
        "msg" => "Event ID not provided."
    ];
}

// Send the response back as JSON
header("Content-type: application/json");
echo json_encode($response);
