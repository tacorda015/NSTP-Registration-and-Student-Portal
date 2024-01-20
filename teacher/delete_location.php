<?php
include('../connection.php');
session_start();
$con = connection();
if (isset($_POST['location_id'])) {
    $locationId = $_POST['location_id'];

    $checkLocation = "SELECT * FROM activitylocation WHERE location_id = $locationId";
    $checkLocationResult = $con->query($checkLocation);

    if($checkLocationResult->num_rows > 0){
        $checkLocationData = $checkLocationResult->fetch_assoc();
        if($checkLocationData['publish'] == 1){
            $response = [
                'status' => false,
                'msg' => 'Cannot delete the location, currently published',
            ];
        }else{
            
            $query = "DELETE FROM activitylocation WHERE location_id = $locationId";
    
            // Execute the query
            $result = mysqli_query($con, $query);
        
            if ($result) {
                // Deletion successful
                $response = [
                    'status' => true,
                    'msg' => 'Location deleted successfully',
                ];
            } else {
                // Deletion failed
                $response = [
                    'status' => false,
                    'msg' => 'Error deleting location: ' . mysqli_error($con),
                ];
            }
        }
    }else{
        $response = [
            'status' => false,
            'msg' => 'Invalid request. Please provide a location ID.',
        ];
    }

} else {
    $response = [
        'status' => false,
        'msg' => 'Invalid request. Please provide a location ID.',
    ];
}
header("Content-type: application/json");
echo json_encode($response);
?>
