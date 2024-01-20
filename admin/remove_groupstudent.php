<?php
session_start();
include('../connection.php');
$con = connection();
if (isset($_GET['remove_student'])) {
  $student_number = mysqli_real_escape_string($con, $_GET['remove_student']);

  // Update the group_id of the selected student to NULL
  $sql = "UPDATE useraccount SET group_id = NULL WHERE student_number = '$student_number'";
  $result = $con->query($sql);

  // Check if the update was successful
  if ($result) {
    echo "success";
  } else {
    echo "error";
  }
}
?>
