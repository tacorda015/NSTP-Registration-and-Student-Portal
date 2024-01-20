<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();

if (isset($_GET['file_id'])) {
  $file_id = $_GET['file_id'];

  $sql = "SELECT * FROM filetable WHERE file_id = {$file_id}";
  $result = mysqli_query($con, $sql);
  $row = mysqli_fetch_assoc($result);

  $filename = $row['file_name'];
  $filepath = '../groupmodule/group_' . $group_id . '/' . $filename; // Adjust the file path based on your file location

  if (file_exists($filepath)) {
    // Send the file as a response
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($filepath));
    readfile($filepath);
    exit;
  } else {
    // File not found
    echo 'File not found.';
  }
} else {
  // Invalid file ID
  echo 'Invalid file ID.';
}
?>
