<?php
// Include your database connection and any required dependencies
include('../connection.php');
$con = connection();

// Check if the file_id parameter is present
if (isset($_POST['file_id'])) {
  $file_id = $_POST['file_id'];

  // Query to retrieve the file information
  $selectQuery = "SELECT * FROM filetable WHERE file_id = '$file_id'";
  $selectResult = mysqli_query($con, $selectQuery);

  if ($selectResult && mysqli_num_rows($selectResult) > 0) {
    $fileRow = mysqli_fetch_assoc($selectResult);
    $filename = $fileRow['file_name'];
    $group_id = $fileRow['group_id'];

    // Query to delete the file from the database
    $deleteQuery = "DELETE FROM filetable WHERE file_id = '$file_id'";
    $deleteResult = mysqli_query($con, $deleteQuery);

    if ($deleteResult) {
      // File deleted successfully from the database, now delete the file from the groupmodule directory
      $filePath = "../groupmodule/group_$group_id/$filename";
      if (file_exists($filePath)) {
        unlink($filePath); // Delete the file
      }

      echo "success";
    } else {
      // Error occurred while deleting file from the database
      echo "error";
    }
  } else {
    // File not found in the database
    echo "invalid";
  }
} else {
  // Invalid request, file_id parameter is missing
  echo "invalid";
}
?>
