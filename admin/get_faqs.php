<?php
include('../connection.php');
$con = connection();

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Retrieve the FAQ data from the database
$sql = "SELECT faq_id, faq_question, faq_answer, faq_status FROM faqtable";
$result = $con->query($sql);

// Generate the table rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr id='faq-" . htmlspecialchars($row["faq_id"]) . "'>";
        echo "<td data-label='Question' data-faqid='" . htmlspecialchars($row["faq_id"]) . "'>" . htmlspecialchars($row["faq_question"]) . "</td>";
        echo "<td data-label='Answer'>" . htmlspecialchars($row["faq_answer"]) . "</td>";
        echo "<td data-label='Status'>" . ($row["faq_status"] == 1 ? "Publish" : "Unpublish") . "</td>";
        echo "<td data-label='Action'>
                <div class='groupButton'>
                    <button type='button' class='btn btn-primary update-faq-button' data-bs-toggle='modal' data-bs-target='#updatefaq'>
                        <i class='bx bx-wrench'></i>Update
                    </button>
                    <button type='button' class='btn btn-danger delete-faq-button' data-faqid='" . htmlspecialchars($row["faq_id"]) . "'>
                        <i class='bx bx-trash' ></i>Delete
                    </button>
                </div> 
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No FAQs found</td></tr>";
}

// Close the result and connection
$result->close();
$con->close();
?>
