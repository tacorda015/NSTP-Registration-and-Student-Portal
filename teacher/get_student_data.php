<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();

header('Content-Type: application/json'); // Set the response header to JSON

if (isset($_GET["grade_id"]) && isset($_GET["student_id"])) {
    $groupId = $_GET["grade_id"];
    $studentId = $_GET["student_id"];

    $query = "SELECT g.*, u.full_name FROM gradetable g INNER JOIN useraccount u ON g.student_id = u.user_account_id WHERE g.grade_id = $groupId AND g.student_id = $studentId";
    $result = $con->query($query);

    if ($result && $result->num_rows > 0) {
        $studentData = $result->fetch_assoc();
        echo json_encode($studentData);
    } else {
        echo json_encode(array('error' => 'No data found for the given student_id'));
    }
} else {
    echo json_encode(array('error' => 'No student_id parameter provided'));
}
?>
