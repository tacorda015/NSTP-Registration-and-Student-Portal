<?php
include('../connection.php');
$con = connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['homeId'])) {
    $homeId = $_POST['homeId'];

    // Check if the home page to be deleted is published
    $check_published_query = "SELECT COUNT(*) AS published_count FROM hometable WHERE home_id = $homeId AND home_status = 0";
    $published_result = $con->query($check_published_query);
    $published_count = $published_result->fetch_assoc()['published_count'];

    if ($published_count <= 0) {
        // There will be no published data remaining after deletion, prevent the deletion
        echo 'error';
    } else {
        // Retrieve the image path before deleting the row
        $select_query = "SELECT home_img FROM hometable WHERE home_id = $homeId";
        $select_result = $con->query($select_query);
        $row = $select_result->fetch_assoc();
        $image_path = $row['home_img'];

        // Delete the row from the table
        $delete_query = "DELETE FROM hometable WHERE home_id = $homeId";
        if ($con->query($delete_query) === true) {
            // Remove the image file from storage
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            echo 'success';
        } else {
            echo 'error';
        }
    }
}elseif ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['videoId'])) {
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
