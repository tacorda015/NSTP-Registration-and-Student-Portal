<?php
session_start();
include('../connection.php');
$con = connection();

$user_data = $_SESSION['user_data'];
$user_account_id = $user_data['user_account_id'];

$retrieve_query = "SELECT * FROM useraccount WHERE user_account_id = $user_account_id";
$retrieve_result = $con->query($retrieve_query);
$retrieve_data = $retrieve_result->fetch_assoc();
$group_id = $retrieve_data['group_id'];

if(!empty($group_id)){
    // Retrieve events from the database
    $sql = "SELECT s.*, a.location_name, a.location_id FROM scheduletable s
    LEFT JOIN activitylocation a ON s.location_id = a.location_id 
    WHERE s.group_id = $group_id";
    $result = $con->query($sql);

    $events = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $start = $row["schedule_date"];
            $end = date('Y-m-d', strtotime($row["schedule_date_end"]. ' + 1 day')); // Adjust the end date by adding one day
            
            $event = [
                "event_id" => $row["schedule_id"],
                "title" => $row["schedule_title"],
                "start" => $start,
                "end" => $end,
                "timeStart" => $row['schedule_start'],
                "timeEnd" => $row['schedule_end'],
                "locationId" => $row['location_id'],
                "locationName" => $row['location_name'],
                "color" => "",
                "url" => ""
            ];
            $events[] = $event;
        }
    }
}else{
    $event = [
        "event_id" => "",
        "title" => "",
        "start" => "",
        "end" => "",
        "color" => "",
        "url" => ""
    ];
    $events[] = $event;
}

// Send the response back as JSON
header("Content-type: application/json");
echo json_encode(["data" => $events]);
?>
