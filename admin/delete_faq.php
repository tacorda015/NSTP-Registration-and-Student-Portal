<?php
include('../connection.php');
$con = connection();

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if the delete_faq_id parameter is set and valid
if (isset($_POST['delete_faq_id']) && is_numeric($_POST['delete_faq_id'])) {
    $faqId = $_POST['delete_faq_id'];

    // Prepare the DELETE statement
    $stmt = $con->prepare("DELETE FROM faqtable WHERE faq_id = ?");
    $stmt->bind_param("i", $faqId);

    // Execute the DELETE statement
    if ($stmt->execute()) {
        // Deletion successful
        echo "success";
    } else {
        // Error occurred while deleting the FAQ
        echo "error";
    }

    // Close the statement
    $stmt->close();
} else {
    // Invalid delete_faq_id parameter
    echo "error";
}

// Close the connection
$con->close();
?>
