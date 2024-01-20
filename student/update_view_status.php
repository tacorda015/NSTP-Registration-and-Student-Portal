<?php
session_start();
ob_start();
include('../connection.php');
$con = connection();
$user_data = $_SESSION['user_data'];
$accountID = $user_data['user_account_id'];

if (isset($_POST['announcementId'])) {
    $announcementId = $_POST['announcementId'];

    $sender_query = "SELECT * FROM announcementtable WHERE announcement_id = ? AND sender_id = ?";
    $sender_statement = $con->prepare($sender_query);
    $sender_statement->bind_param("ii", $announcementId, $accountID);
    $sender_statement->execute();
    $sender_result = $sender_statement->get_result();

    if ($sender_result->num_rows > 0) {
        // Update the sender_view to 0 in the database
        $updateQuery = "UPDATE announcementtable SET sender_view = 0 WHERE announcement_id = ?";
    } else {
        // Update the view_status to 0 in the database
        $updateQuery = "UPDATE announcementtable SET view_status = 0 WHERE announcement_id = ?";
    }

    $update_statement = $con->prepare($updateQuery);
    $update_statement->bind_param("i", $announcementId);
    $updateResult = $update_statement->execute();

    if ($updateResult) {
        echo "View status updated successfully";
    } else {
        echo "Error updating view status: " . $con->error;
    }
} else {
    echo "Invalid announcement ID";
}
?>
