<?php
include('../connection.php');
$con = connection();
// Include your database connection and any necessary functions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the announcementId parameter is provided
    if (isset($_POST['announcementId'])) {
        $announcementId = $_POST['announcementId'];

        // TODO: Implement your logic to hide the announcement based on the announcementId

        // Example code to hide the announcement using mysqli
        $deleteQuery = "UPDATE announcementtable SET view_status = 0 WHERE announcement_id = '$announcementId'";

        if ($con->query($deleteQuery)) {
            // Announcement hide successfully
            echo "success";
        } else {
            // Error occurred while hiding the announcement
            echo "error";
        }
    } else {
        // Announcement batch parameter is missing
        echo "error";
    }
} else {
    // Invalid request method
    echo "error";
}
?>
