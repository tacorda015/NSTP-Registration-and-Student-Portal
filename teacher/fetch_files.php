<?php
// session_start();
// include('../connection.php');
// $con = connection();

// $group_id = $_POST['group_id'];
// $searchQuery = isset($_POST['search']) ? $_POST['search'] : '';

// // Modify your SQL query to include the search condition
// $sql = "SELECT * FROM filetable WHERE group_id = {$group_id}";

// if (!empty($searchQuery)) {
//     $searchQuery = mysqli_real_escape_string($con, $searchQuery);
//     $sql .= " AND (title LIKE '%{$searchQuery}%' OR description LIKE '%{$searchQuery}%' OR file_name LIKE '%{$searchQuery}%')";
// }

// $result = mysqli_query($con, $sql);
// if(mysqli_num_rows($result) > 0){
//     while ($row = mysqli_fetch_assoc($result)) {
//         $file_id = $row['file_id']; // Assuming 'id' is the primary key column
//         $title = htmlspecialchars($row['title']);
//         $description = htmlspecialchars($row['description']);
//         $filename = htmlspecialchars($row['file_name']);
//         $date = htmlspecialchars($row['date_upload']);

//         echo "<tr id='file-row-$file_id'>";
//         echo "<td data-label='File name'><a href='../groupmodule/group_{$group_id}/{$filename}'>{$filename}</a></td>";
//         echo "<td data-label='Title'>{$title}</td>";
//         echo "<td data-label='Description'>{$description}</td>";
//         echo "<td data-label='Date Uploaded'>{$date}</td>";
//         echo "<td data-label='Action'><button class='btn btn-danger' onclick='deleteFile($file_id)'>Delete</button></td>";
//         echo "</tr>";
//     }
// } else {
//     echo '<tr><td colspan="5">No files uploaded.</td></tr>';
// }
?>
<?php
session_start();
include('../connection.php');
$con = connection();

$group_id = $_POST['group_id'];
$searchQuery = isset($_POST['search']) ? $_POST['search'] : '';

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$recordsPerPage = 10;

// Modify your SQL query to include the search condition
$sql = "SELECT * FROM filetable WHERE group_id = {$group_id}";

if (!empty($searchQuery)) {
    $searchQuery = mysqli_real_escape_string($con, $searchQuery);
    $sql .= " AND (title LIKE '%{$searchQuery}%' OR description LIKE '%{$searchQuery}%' OR file_name LIKE '%{$searchQuery}%')";
}

// Get total number of records for pagination
$result = mysqli_query($con, $sql);
$totalRecords = mysqli_num_rows($result);

// Calculate total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);

// Calculate the offset for the current page
$offset = ($page - 1) * $recordsPerPage;

// Add LIMIT and OFFSET clauses to the SQL query for pagination
$sql .= " LIMIT $recordsPerPage OFFSET $offset";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    $tableRows = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $file_id = $row['file_id']; // Assuming 'id' is the primary key column
        $title = htmlspecialchars($row['title']);
        $description = htmlspecialchars($row['description']);
        $filename = htmlspecialchars($row['file_name']);
        $date = htmlspecialchars($row['date_upload']);

        $tableRows .= "<tr id='file-row-$file_id'>";
        $tableRows .= "<td data-label='File name'><a href='../groupmodule/group_{$group_id}/{$filename}'>{$filename}</a></td>";
        $tableRows .= "<td data-label='Title'>{$title}</td>";
        $tableRows .= "<td data-label='Description'>{$description}</td>";
        $tableRows .= "<td data-label='Date Uploaded'>{$date}</td>";
        $tableRows .= "<td data-label='Action'><button class='btn btn-danger' onclick='deleteFile($file_id)'>Delete</button></td>";
        $tableRows .= "</tr>";
    }
    $pagination = "<nav aria-label='Page navigation'>
        <ul class='pagination justify-content-center'>";

    $pagination .= "<li class='page-item " . ($page == 1 ? 'disabled' : '') . "'>
            <a class='page-link' href='javascript:void(0);' onclick='refreshFiles(1)'>&laquo;</a>
          </li>";

    for ($i = max(1, $page - 2); $i <= min($page + 2, $totalPages); $i++) {
        $pagination .= "<li class='page-item " . ($i == $page ? 'active' : '') . "'>
                <a class='page-link' href='javascript:void(0);' onclick='refreshFiles($i)'>$i</a>
              </li>";
    }

    $pagination .= "<li class='page-item " . ($page == $totalPages ? 'disabled' : '') . "'>
            <a class='page-link' href='javascript:void(0);' onclick='refreshFiles($totalPages)'>&raquo;</a>
          </li>
        </ul>
    </nav>";

    $response = [
        'table' => $tableRows,
        'pagination' => $pagination
    ];

    // Send the JSON response with the appropriate headers
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_SLASHES);
} else {
    $response = [
        'table' => '<tr><td colspan="5">No files uploaded.</td></tr>',
        'pagination' => ''
    ];

    // Send the JSON response with the appropriate headers
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_SLASHES);
}
?>
