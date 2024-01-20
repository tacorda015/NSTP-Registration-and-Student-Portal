<?php
include('./connection.php');
$con = connection();
// Assuming you already have a database connection established in $con variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['password'])) {
    // Sanitize and validate the contact number
    $updatedPassword = $_POST['password']; // You may need to apply additional sanitization/validation here
    $userId = $_POST['user_account_id']; // You may need to apply additional sanitization/validation here

    // Prepare the SQL statement with parameter binding
    $query = "UPDATE useraccount SET password = ? WHERE user_account_id = ?";
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
      // Bind the parameters
      mysqli_stmt_bind_param($stmt, "si", $updatedPassword, $userId);

      // Execute the statement
      if (mysqli_stmt_execute($stmt)) {
        echo "Password updated successfully";
      } else {
        echo "Error updating password: " . mysqli_error($con);
      }

      // Close the statement
      mysqli_stmt_close($stmt);
    } else {
      echo "Error in prepared statement: " . mysqli_error($con);
    }
  } else {
    echo "Password is missing";
  }
} else {
  echo "Invalid request";
}
?>
