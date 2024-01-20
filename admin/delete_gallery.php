<?php
include('../connection.php');
$con = connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['galleryId'])) {
    $galleryId = $_POST['galleryId'];
    $galleryComponent = $_POST['galleryComponent'];

    // Check if the gallery page to be deleted is published
    $check_published_query = "SELECT COUNT(*) AS published_count FROM gallerytable WHERE gallery_id = $galleryId AND gallery_status = 0";
    $published_result = $con->query($check_published_query);
    $published_count = $published_result->fetch_assoc()['published_count'];

    if ($published_count <= 0) {
        // There will be no published data remaining after deletion, prevent the deletion
        echo 'error';
    } else {
        // Retrieve the image path before deleting the row
        $select_query = "SELECT gallery_img FROM gallerytable WHERE gallery_id = $galleryId";
        $select_result = $con->query($select_query);
        $row = $select_result->fetch_assoc();
        $image_path = $row['gallery_img'];

        // Delete the row from the table
        $delete_query = "DELETE FROM gallerytable WHERE gallery_id = $galleryId";
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
}
?>
