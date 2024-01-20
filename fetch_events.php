<?php
session_start();
include('./connection.php');
$con = connection();

// Retrieve events from the database
$sql = "SELECT * FROM calendar_event";
$result = $con->query($sql);

$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $start = $row["event_start_date"];
        $end = date('Y-m-d', strtotime($row["event_end_date"]. ' + 1 day')); // Adjust the end date by adding one day

        $event = [
            "event_id" => $row["event_id"],
            "title" => $row["event_name"],
            "start" => $start,
            "end" => $end,
            "color" => "",
            "url" => ""
        ];
        $events[] = $event;
    }
}

// Send the response back as JSON
header("Content-type: application/json");
echo json_encode(["data" => $events]);
?>
