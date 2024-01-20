<?php
include('./connection.php');
$con = connection();
// Assuming you already have a database connection established in $con variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['contactNumber'])) {
    // Sanitize and validate the contact number
    $updatedContactNumber = $_POST['contactNumber']; // You may need to apply additional sanitization/validation here
    $userId = $_POST['user_account_id']; // You may need to apply additional sanitization/validation here

    // Make sure the contact number is enclosed in single quotes to preserve the leading zero
    $updatedContactNumber = "'" . mysqli_real_escape_string($con, $updatedContactNumber) . "'";

    $roleCheck_query = "SELECT role_account_id, component_name, student_number FROM useraccount WHERE user_account_id = '$userId'";
    $roleCheck_result = mysqli_query($con, $roleCheck_query);
    $roleCheck_data = $roleCheck_result->fetch_assoc();
    $role = $roleCheck_data['role_account_id'];
    $component = $roleCheck_data['component_name'];
    $unique = $roleCheck_data['student_number'];

    if ($role == 2) {
      // Update the contact number in the database
      $query = "UPDATE useraccount SET contactNumber = $updatedContactNumber WHERE user_account_id = '$userId'";

      if (mysqli_query($con, $query)) {
        echo "Contact number updated successfully";
      } else {
        echo "Error updating contact number: " . mysqli_error($con);
      }
    } elseif ($role == 3 && $component == "ROTC") {
      // Prepare the SQL statement with parameter binding
      $query = "UPDATE useraccount SET contactNumber = $updatedContactNumber WHERE user_account_id = '$userId'";
      $trainer = "UPDATE trainertable SET trainer_contactnumber = $updatedContactNumber WHERE trainer_uniquenumber = '$unique'";

      if (mysqli_query($con, $query) && mysqli_query($con, $trainer)) {
        echo "Contact number updated successfully";
      } else {
        echo "Error updating contact number: " . mysqli_error($con);
      }
    } elseif ($role == 3 && $component == "CWTS") {
      // Prepare the SQL statement with parameter binding
      $query = "UPDATE useraccount SET contactNumber = $updatedContactNumber WHERE user_account_id = '$userId'";
      $teacher = "UPDATE teachertable SET teacher_contactnumber = $updatedContactNumber WHERE teacher_uniquenumber = '$unique'";

      if (mysqli_query($con, $query) && mysqli_query($con, $teacher)) {
        echo "Contact number updated successfully";
      } else {
        echo "Error updating contact number: " . mysqli_error($con);
      }
    } else {
      // Update the contact number in the database
      $query = "UPDATE useraccount SET contactNumber = $updatedContactNumber WHERE user_account_id = '$userId'";

      if (mysqli_query($con, $query)) {
        echo "Contact number updated successfully";
      } else {
        echo "Error updating contact number: " . mysqli_error($con);
      }
    }
  } else {
    echo "Contact number is missing";
  }
} else {
  echo "Invalid request";
}
?>
