<?php
session_start();
include('../connection.php');
$con = connection();
if (isset($_POST['group_id']) && isset($_POST['user_account_ids'])) {
  $group_id = $_POST['group_id'];
  $user_account_ids = json_decode($_POST['user_account_ids']);
  
  if (empty($user_account_ids)) {
    echo "No students selected.";
    exit;
  }
  
  // Get the number of students allowed in the group
  $query = "SELECT number_student FROM grouptable WHERE group_id = $group_id";
  $result = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($result);
  $allowed_number = $row['number_student'];
  
  // Count the number of students in the group
  $query = "SELECT COUNT(*) as count FROM useraccount WHERE group_id = $group_id AND role_account_id = 2";
  $result = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($result);
  $current_number = $row['count'];
  
  // Check if the current number of students is less than the allowed number
  // if ($current_number + count($user_account_ids) <= $allowed_number) {
  if ($current_number + count($user_account_ids) <= (int)$allowed_number) {
    // Insert user accounts into the group
    foreach ($user_account_ids as $user_account_id) {
      $stmt = $con->prepare("UPDATE useraccount SET group_id = ? WHERE user_account_id = ?");
      $stmt->bind_param("ii", $group_id, $user_account_id);
      $stmt->execute();
    }
    
    echo "success";
  } else {
    echo "The group has reached its maximum number of students.";
  }
}
?>
