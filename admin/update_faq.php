<?php
include('../connection.php');
$con = connection();

// Check if the form data was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated values from the request
    $updateId = $_POST['update_faq_id'];
    $updateQuestion = $_POST['update_faq_question'];
    $updateAnswer = $_POST['update_faq_answer'];
    $updateStatus = $_POST['update_faq_status'];

    // Update the FAQ entry in the database
    $stmt = $con->prepare("UPDATE faqtable SET faq_question = ?, faq_answer = ?, faq_status = ? WHERE faq_id = ?");
    $stmt->bind_param("ssii", $updateQuestion, $updateAnswer, $updateStatus, $updateId);

    if ($stmt->execute()) {
        // FAQ updated successfully
        echo 'success';
    } else {
        // Error occurred while updating the FAQ entry
        echo 'error';
    }

    $stmt->close();
    $con->close();
}

?>
