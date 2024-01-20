<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();

$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];

if (isset($_FILES['picture'])) {
    $picture = $_FILES['picture'];

    // Check if a new profile picture has been uploaded
    if (!empty($picture) && $picture['error'] == UPLOAD_ERR_OK) {

        $uploads_dir = 'uploads/user_' . $user_id . '/';

        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }

        $upload_file = $uploads_dir . basename($picture['name']);

        // Add (1) to the filename if it already exists
        $file_name = $picture['name'];
        $file_path = $uploads_dir . $file_name;
        $counter = 1;
        while (file_exists($file_path)) {
            $file_name = pathinfo($picture['name'], PATHINFO_FILENAME) . '(' . $counter . ').' . pathinfo($picture['name'], PATHINFO_EXTENSION);
            $file_path = $uploads_dir . $file_name;
            $counter++;
        }

        move_uploaded_file($picture['tmp_name'], $file_path);

        // Update the user's profile picture in the database
        $picture_url = $file_path;
        $profile_query = "INSERT INTO profilepicture (user_account_id, picture_pathfile) VALUES ('$user_id', '$picture_url')";

        $result = mysqli_query($con, $profile_query);

        if ($result) {
            // Return success response
            echo "Success";
        } else {
            // Return error response
            echo "Error updating profile: " . mysqli_error($con);
        }
    } else {
        // Return error response
        echo "Error uploading file";
    }
}
?>
