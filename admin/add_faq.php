<?php
include('../connection.php');
$con = connection();

// Ensure the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $faqQuestion = $_POST["faq_question"];
    $faqAnswer = $_POST["faq_answer"];
    $faqStatus = $_POST["faq_status"];

    // Prepare the SQL statement
    $sql = "INSERT INTO faqtable (faq_question, faq_answer, faq_status) VALUES (?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssi", $faqQuestion, $faqAnswer, $faqStatus);

    // Execute the statement
    if ($stmt->execute()) {
        // FAQ entry added successfully
        echo "success";
    } else {
        // Error occurred while adding the FAQ entry
        echo "error";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$con->close();
?>
