<?php
include('../connection.php');
$con = connection();

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['videoId'])) {
    $videoId = $_POST['videoId'];
    
    // Check if the video with the provided videoId exists in the database
    $checkQuery = "SELECT video_status FROM videostable WHERE video_id = $videoId";
    $checkResult = $con->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        $row = $checkResult->fetch_assoc();
        $videoStatus = $row['video_status'];

        // Check if the video is not published (status 0)
        if ($videoStatus != 1) {
            // Video is not published, delete it from the database
            $deleteQuery = "DELETE FROM videostable WHERE video_id = $videoId";

            if ($con->query($deleteQuery) === TRUE) {
                echo 'success';
            } else {
                echo 'error';
            }
        } else {
            // Video is published, prevent deletion
            echo 'error';
        }
    } else {
        // Video with the provided videoId not found
        echo 'error';
    }
}
?>
