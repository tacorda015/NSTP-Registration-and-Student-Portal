<?php
include('../connection.php');
$con = connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsId'])) {
    $newsId = $_POST['newsId'];

    $checkimg = "SELECT newsupdate_img FROM newsupdatetable WHERE newsupdate_id = $newsId";
    $checkimg_result = $con->query($checkimg);
    $checkimg_data = $checkimg_result->fetch_assoc();
    $image_path = $checkimg_data['newsupdate_img'];

    $delete_query = "DELETE FROM newsupdatetable WHERE newsupdate_id = $newsId";
    $delete_result = $con->query($delete_query);

    if($delete_result){
        if(file_exists($image_path)){
            unlink($image_path);
        }
        echo 'success';
    }else{
        echo 'error';
    }
}
?>
