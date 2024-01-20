<?php
include('../connection.php');
$con = connection();

// Retrieve the input from the AJAX request
$input = $_POST['input'];

// Prepare the SQL statement
$stmt = $con->prepare("SELECT user_account_id, full_name, email_address, student_number FROM useraccount WHERE full_name LIKE ? OR email_address LIKE ? OR student_number LIKE ?");
$inputLike = '%' . $input . '%';
$stmt->bind_param("sss", $inputLike, $inputLike, $inputLike);

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
        echo "<div class='suggestion' data-user_account_id='$accountId' data-email_address='$emailAddress' data-full_name='$fullName' data-student_number='$studentNumber'>$fullName ($emailAddress)</div>";    }
} else {
    echo "<div class='no-suggestion'>No matching recipients found.</div>";
}

// Close the statement and database connection
$stmt->close();
$con->close();
?>

