<?php
include('../connection.php');
$con = connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the announcementId parameter is provided
    if (isset($_POST['announcementId'])) {
        $announcementId = $_POST['announcementId'];

        // TODO: Implement your logic to update the announcement status based on the announcementId

        // Example code to update the announcement status using mysqli
        $updateQuery = "UPDATE announcementtable SET view_status = 0 WHERE announcement_id = '$announcementId'";
        if ($con->query($updateQuery)) {
            // Announcement status updated successfully
            echo "success";
        } else {
            // Error occurred while updating the announcement status
            echo "error";
        }
    } else {
        // Announcement ID parameter is missing
        echo "error";
    }
} else {
    // Invalid request method
    echo "error";
}
?>
