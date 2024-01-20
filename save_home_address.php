<?php
include('./connection.php');
$con = connection();
// Assuming you already have a database connection established in $con variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['homeAddress'])) {
    // Sanitize and validate the home address
    $baranggay = $_POST['baranggay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $homeAddress = $_POST['homeAddress'];

    // $homeAddress = $baranggay . ', ' . $city . ', ' . $province;
    // $updatedHomeAddress = $_POST['homeAddress']; // You may need to apply additional sanitization/validation here
    $userId = $_POST['user_account_id']; // You may need to apply additional sanitization/validation here

    $roleCheck_query = "SELECT role_account_id, component_name, student_number FROM useraccount WHERE user_account_id = '$userId'";
    $roleCheck_result = mysqli_query($con, $roleCheck_query);
    $roleCheck_data = $roleCheck_result->fetch_assoc();
    $role = $roleCheck_data['role_account_id'];
    $component = $roleCheck_data['component_name'];
    $unique = $roleCheck_data['student_number'];

    if ($role == 2 || $role == 1) {
      // Update the home address in the database
      $query = "UPDATE useraccount SET baranggay = '$baranggay', city = '$city', province = '$province', homeaddress = '$homeAddress' WHERE user_account_id = '$userId'";
      // $query = "UPDATE useraccount SET homeaddress = '$updatedHomeAddress' WHERE user_account_id = '$userId'";

      if (mysqli_query($con, $query)) {
        echo "Home address updated successfully";
      } else {
        echo "Error updating home address: " . mysqli_error($con);
      }
    } elseif ($role == 3 && $component === "ROTC") {
      // Update the home address in the useraccount table
      
      $query = "UPDATE useraccount SET baranggay = '$baranggay', city = '$city', province = '$province', homeaddress = '$homeAddress' WHERE user_account_id = '$userId'";
      $trainer = "UPDATE trainertable SET trainer_address = '$homeAddress' WHERE trainer_uniquenumber = '$unique'";

      if (mysqli_query($con, $query) && mysqli_query($con, $trainer)) {
        echo "Home address updated successfully";
      } else {
        echo "Error updating home address: " . mysqli_error($con);
      }
    } elseif ($role == 3 && $component === "CWTS") {
      // Update the home address in the useraccount table
      $query = "UPDATE useraccount SET baranggay = '$baranggay', city = '$city', province = '$province', homeaddress = '$homeAddress' WHERE user_account_id = '$userId'";
      $teacher = "UPDATE teachertable SET teacher_address = '$homeAddress' WHERE teacher_uniquenumber = '$unique'";

      if (mysqli_query($con, $query) && mysqli_query($con, $teacher)) {
        echo "Home address updated successfully";
      } else {
        echo "Error updating home address: " . mysqli_error($con);
      }
    } else {
      // Update the home address in the database
      $query = "UPDATE useraccount SET homeaddress = '$updatedHomeAddress' WHERE user_account_id = '$userId'";

      if (mysqli_query($con, $query)) {
        echo "Home address updated successfully";
      } else {
        echo "Error updating home address: " . mysqli_error($con);
      }
    }
  } else {
    echo "Home address is missing";
  }
} else {
  echo "Invalid request";
}
?>
