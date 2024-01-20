<?php
include('../connection.php');
$con = connection();

$response = [];
// Check if the form data was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated values from the request
    $onOff = $_POST['onOff'];

    $query = "UPDATE teamtable SET onOff = $onOff";
    $result = $con->query($query);

    if ($result && $onOff == 1) {
        // FAQ updated successfully
        $response = [
            "status" => true,
            "msg" => "Team Developer Succesfully Published"
        ];
    } elseif($result && $onOff == 0) {
        $response = [
            "status" => true,
            "msg" => "Team Developer Succesfully Unpublished"
        ];
    }else{
        $response = [
            "status" => false,
            "msg" => "Team Developer Unsuccessful Publish"
        ];
    }
}
header("Content-type: application/json");
echo json_encode($response);
?>
