<?php
session_start();
include('../connection.php');
$con = connection();

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the event details from the POST data
    $event_name = $_POST["event_name"];
    $event_start_date = $_POST["event_start_date"];
    $event_end_date = $_POST["event_end_date"];

    // Perform any necessary validation on the input data

    // Insert the event into the database
    $sql = "INSERT INTO calendar_event (event_name, event_start_date, event_end_date)
            VALUES ('$event_name', '$event_start_date', '$event_end_date')";

    if (mysqli_query($con, $sql)) {
        $response = array(
            "status" => true,
            "msg" => "Event saved successfully."
        );
    } else {
        $response = array(
            "status" => false,
            "msg" => "Error: " . mysqli_error($con)
        );
    }

    // Send the response back as JSON
    header("Content-type: application/json");
    echo json_encode($response);
} else {
    // If the request is not a POST request, return an error
    $response = array(
        "status" => false,
        "msg" => "Invalid request method."
    );

    // Send the response back as JSON
    header("Content-type: application/json");
    echo json_encode($response);
}
?>
