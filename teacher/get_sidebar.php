<?php
// Include the necessary files and initialize the session
session_start();
include('../connection.php');
$con = connection();

// Retrieve the user data from the session
$user_data = $_SESSION['user_data'];
$userId = $user_data['user_account_id'];

// Query to fetch the status count for the teacher sidebar
$status_query = "SELECT COUNT(*) AS status_count FROM announcementtable WHERE view_status = 1 AND sender_id != $userId AND recipient_id = $userId";
$status_result = $con->query($status_query);

// Check if the query was successful
if ($status_result) {
    $row = $status_result->fetch_assoc();
    $status_count = $row['status_count'];

    // Generate the updated sidebar HTML based on the status count
    $sidebar_html = '<li>';
    $sidebar_html .= '<div class="iocn-link">';
    $sidebar_html .= '<a href="./announcelist.php">';

    if ($status_count >= 1) {
        $sidebar_html .= '<i class="bi bi-bell-fill" style="position:relative;">';
        $sidebar_html .= '<div style="border-radius: 50%; background: red; position: absolute; top: 5px; right: 5px; width:10px; height:10px;"></div>';
        $sidebar_html .= '</i>';
    } else {
        $sidebar_html .= '<i class="bi bi-bell"></i>';
    }

    $sidebar_html .= '<span class="link_name">Announcement</span>';
    $sidebar_html .= '</a>';
    $sidebar_html .= '</div>';
    $sidebar_html .= '<ul class="sub-menu">';
    $sidebar_html .= '<li><a class="link_name" href="./announcelist.php">Announcement</a></li>';
    $sidebar_html .= '</ul>';
    $sidebar_html .= '</li>';

    // Output the updated sidebar HTML
    echo $sidebar_html;
} else {
    // Handle query error
    // Output a default or error message
    echo 'Error retrieving sidebar content';
}
?>
