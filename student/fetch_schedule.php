<?php
session_start();
include('../connection.php');
$con = connection();

$user_data = $_SESSION['user_data'];

$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = {$user_id}";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();
$group_id = $useraccount_data['group_id'];

// Retrieve events from the database
if(!empty($group_id)){
    $sql = "SELECT * FROM scheduletable WHERE group_id = $group_id";
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
                "timestart" => $row['schedule_start'],
                "timeend" => $row['schedule_end'],
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
