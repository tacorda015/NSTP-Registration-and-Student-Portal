<?php
include('../connection.php');
$con = connection();
session_start();
$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = {$user_id}";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

// Retrieve the input and user_group_id from the AJAX request
$input = $_POST['input'];
$userGroupId = $useraccount_data['group_id'];

// Prepare the SQL statement
$stmt = $con->prepare("SELECT user_account_id, full_name, email_address, student_number
                      FROM useraccount
                      WHERE (full_name LIKE ? OR email_address LIKE ? OR student_number LIKE ?)
                      AND group_id = ?
                      AND role_account_id = 2");

$inputLike = '%' . $input . '%';
$stmt->bind_param("sssi", $inputLike, $inputLike, $inputLike, $userGroupId);

// Execute the statement
$stmt->execute();

// Fetch the results
$result = $stmt->get_result();
// Display the suggestions
if ($result->num_rows > 0) {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $accountId = $row['user_account_id'];
        $fullName = $row['full_name'];
        $emailAddress = $row['email_address'];
        $studentNumber = $row['student_number'];

        // Output the suggestion with data attributes
        echo "<div class='suggestion' data-user_account_id='$accountId' data-email_address='$emailAddress' data-full_name='$fullName' data-student_number='$studentNumber'>$fullName ($emailAddress)</div>";
    }
} else {
    echo "<div class='no-suggestion'>No matching recipients found.</div>";
}

// Close the statement and database connection
$stmt->close();
$con->close();
?>
