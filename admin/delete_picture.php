<?php
session_start();
include('../connection.php');
$con = connection();

$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];

$check_picture = "SELECT picture FROM useraccount WHERE user_account_id = $user_id";
$check_picture_result = $con->query($check_picture);
$check_picture_data = $check_picture_result->fetch_assoc();

$user_picture = $check_picture_data['picture'];

if (isset($_POST['picture_id'])) {
    $pictureId = $_POST['picture_id'];
    $picture_pathfile = $_POST['picture_pathfile'];

    if ($user_picture === $picture_pathfile) {
        $userPicture_query = "UPDATE useraccount SET picture = 'uploads/default.jpeg' WHERE user_account_id = $user_id";
        $userPicture_result = $con->query($userPicture_query);
    }
    
    // Query to delete the picture from the database
    $deleteQuery = "DELETE FROM profilepicture WHERE picture_id = $pictureId";
    $result = $con->query($deleteQuery);
    
    
    if ($result) {
        if (file_exists($picture_pathfile)) {
            // Delete the file from the file system
            unlink($picture_pathfile);
        }
        
        // Deletion was successful
        echo json_encode(['success' => true]);
    } else {
        // Deletion failed
        echo json_encode(['success' => false]);
    }
} else {
    // Invalid request
    echo json_encode(['success' => false]);
}
?>