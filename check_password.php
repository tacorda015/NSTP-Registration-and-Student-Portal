<?php
include('./connection.php');
$con = connection();
// Assuming you already have a database connection established in $con variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['current_password'])) {
    // Sanitize and validate the contact number
    $current_password = base64_encode($_POST['current_password']); // You may need to apply additional sanitization/validation here
    $userId = $_POST['user_account_id']; // You may need to apply additional sanitization/validation here

    // Prepare the SQL statement with parameter binding
    $query = "SELECT password FROM useraccount WHERE user_account_id = '$userId'";
    $result = $con->query($query);
    $data = $result->fetch_assoc();
    $databasePassword = $data['password'];
    
    if($current_password == $databasePassword){
      echo "success";
    }else{
      echo "error";
    }
  } else {
    echo "error";
  }
} else {
  http_response_code(400);
  echo "Bad Request";
}
?>
