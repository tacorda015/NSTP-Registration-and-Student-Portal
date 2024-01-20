<?php
include('../connection.php');
$con = connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the announcementBatch and senderId parameters are provided
    if (isset($_POST['announcementBatch']) && isset($_POST['sender_id']) && isset($_POST['accountID']) && isset($_POST['announcementId'])) {
        $announcementBatch = $_POST['announcementBatch'];
        $sender_id = $_POST['sender_id'];
        $accountID = $_POST['accountID'];
        $announcementId = $_POST['announcementId'];

        if ($sender_id ==$accountID) {
            // Sender is not the current user, update view_status to 0
            $deleteQuery = "DELETE FROM announcementtable WHERE announcement_batch = '$announcementBatch'";
        } else {
            // Sender is the current user, delete the announcement
            $deleteQuery = "UPDATE announcementtable SET view_status = 0 WHERE announcement_id = '$announcementId'";
        }

        if ($con->query($deleteQuery)) {
            // Delete operation successful
            echo "success";
        } else {
            // Error occurred during the delete operation
            echo "error";
        }
    } else {
        // Required parameters are missing
        echo "error";
    }
} else {
    // Invalid request method
    echo "error";
}

?>
