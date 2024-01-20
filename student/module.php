<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}

// get user data from session
$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = $user_id";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$role_account_id = $useraccount_data['role_account_id'];
$group_id = $useraccount_data['group_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 
// Calling the side bar
include_once('./studentsidebar.php');
?>
    <div class="home-main-container">
        <div class="studentList-container">
        <?php
        if ($group_id != null) {
            $group_query = "SELECT group_name FROM grouptable WHERE group_id = $group_id";
            $group_result = $con->query($group_query);
            $group_data = $group_result->fetch_assoc();
        
        ?>
            
                <div class="page-title">
                    <div class="titleContainer">
                        <span class="group_id"><?php echo $group_data['group_name']; ?></span>
                        <label class="in-charge-label">Group Name</label>
                    </div>
                    <form method="get" enctype="multipart/form-data" action="module.php">
                        <div class="search-container">
                            <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-primary" type="submit"><i class='bx bx-search'></i></button>
                        </div>
                    </form>
                </div>
                <div class="page-title">
                    <?php
                    $component_name = $useraccount_data['component_name'];

                    if ($component_name == 'ROTC') {
                        $rotc_query = "SELECT * FROM trainertable WHERE group_id = {$group_id}";
                        $rotc_result = $con->query($rotc_query);
                    
                        if ($rotc_result && $rotc_result->num_rows > 0) {
                            $incharge_data = $rotc_result->fetch_assoc();
                            $incharge_name = $incharge_data['trainer_name'];
                        } else {
                            $incharge_name = "No Incharge Person Assigned";
                        }
                    } elseif ($component_name == 'CWTS') {
                        $cwts_query = "SELECT * FROM teachertable WHERE group_id = {$group_id}";
                        $cwts_result = $con->query($cwts_query);
                    
                        if ($cwts_result && $cwts_result->num_rows > 0) {
                            $incharge_data = $cwts_result->fetch_assoc();
                            $incharge_name = $incharge_data['teacher_name'];
                        } else {
                            $incharge_name = "No Incharge Person Assigned";
                        }
                    } else {
                        $incharge_name = "No Incharge Person Assigned";
                    }
                    
                    ?>
                    <div class="titleContainer">
                        <span class="group-id"><?php echo $incharge_name; ?></span>
                        <label>Person in charge</label>
                    </div>
                </div>
            <?php
            $recordsPerPage = 10;
            if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                $currentPage = intval($_GET['page']);
            } else {
                $currentPage = 1;
            }

            // Calculate the offset for the current page
            $offset = ($currentPage - 1) * $recordsPerPage;

            $sql = "SELECT * FROM filetable WHERE group_id = {$group_id}";
        
            // Start of Search Function
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = mysqli_real_escape_string($con, $_GET['search']);
                $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%' OR file_name LIKE '%$search%')";
            }
            // End Of Search Function
        
            $sql .= " LIMIT $recordsPerPage OFFSET $offset";    
            $result = mysqli_query($con, $sql);
            if (mysqli_num_rows($result) > 0) {
            ?>
                <div class="tableContainer">
                    <table class="table table-sm caption-top">
                    <caption>List of Module</caption>
                    <thead class="custom-thead"><tr><th>File Name</th><th>Title</th><th>Description</th><th>Upload Date</th><th class='thAction'>Action</th></tr></thead>
                    <tbody id="file-table-body">
            <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $file_id = $row['file_id']; // Assuming 'id' is the primary key column
                    $title = htmlspecialchars($row['title']);
                    $description = htmlspecialchars($row['description']);
                    $filename = htmlspecialchars($row['file_name']);
                    $date = htmlspecialchars($row['date_upload']);
                
                echo '<tr>
                    <tr id="file-row-' . $file_id . '">'; ?>
                    <td data-label="File name"><?php echo $filename; ?></td>
                    <td data-label="Title"><?php echo $title; ?></td>
                    <td data-label="Description"><?php echo $description; ?></td>
                    <td data-label="Date Uploaded"><?php echo $date; ?></td>
                    <td data-label="Action"><button class="btn btn-primary" onclick="confirmDownload(<?php echo $file_id . ', \'' . $filename . '\''; ?>);">Download</button></td>
                    </tr>
                <?php } ?>
        
                </tbody></table></div>
                <!-- Pagination links using Bootstrap -->
                <nav aria-label="Page navigation" class = 'tablePagination'>
                    <ul class="pagination justify-content-center">
                        <?php
                        // Get the total number of records for pagination
                        $totalRecordsQuery = "SELECT COUNT(*) AS total FROM filetable WHERE group_id = {$group_id}";
                        $totalRecordsResult = mysqli_query($con, $totalRecordsQuery);
                        $totalRecordsData = mysqli_fetch_assoc($totalRecordsResult);
                        $totalRecords = $totalRecordsData['total'];

                        // Calculate the total number of pages
                        $totalPages = ceil($totalRecords / $recordsPerPage);

                         // Pagination links - Previous
                         $prevPage = $currentPage - 1;
                         echo "<li class='page-item " . ($currentPage == 1 ? 'disabled' : '') . "'>
                                 <a class='page-link' href='?page=$prevPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&laquo; Previous</a>
                             </li>";

                        for ($i = max(1, $currentPage - 2); $i <= min($currentPage + 2, $totalPages); $i++) {
                            echo "<li class='page-item " . ($i == $currentPage ? 'active' : '') . "'>
                                    <a class='page-link' href='?page=$i" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>$i</a>
                                </li>";
                        }

                        // Pagination links - Next
                        $nextPage = $currentPage + 1;
                        echo "<li class='page-item " . ($currentPage == $totalPages ? 'disabled' : '') . "'>
                                <a class='page-link' href='?page=$nextPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>Next &raquo;</a>
                            </li>";
                        ?>
                    </ul>
                </nav>
        <?php     } else { ?>
                <h2 style="text-align:center;">No files found.</h2>
         <?php   } ?>
            <!-- </div> -->
        <?php    } else { ?>
            <!-- <div class="home-main-container"> -->
                    <h2 class="text-center">No Assigned Group yet.</h2>
            
        <?php   } ?>
            </div>
        </div>
    </section>
    </div>
    <script src="../asset/js/index.js"></script>
    <script src="../asset/js/topbar.js"></script>
    <script src="./js/search.js"></script>
    <script>
        addSearchFunctionality('search', '.search-icon', './module.php');
    </script>
    <script>
    function confirmDownload(file_id, filename) {
    Swal.fire({
        title: 'Confirmation',
        text: 'Are you sure you want to download this file?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Download',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const group_id = <?php echo $group_id; ?>; // Get the group ID value
            downloadFile(file_id, filename, group_id);    
        }
    });
}

function downloadFile(file_id, filename, group_id) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `downloading_files.php?file_id=${file_id}&group_id=${group_id}`, true);
    xhr.responseType = 'blob';

    xhr.onload = function() {
        if (this.status === 200) {
            const blob = new Blob([this.response], { type: 'application/octet-stream' });

            // Create a temporary anchor element to initiate the download
            const a = document.createElement('a');
            const url = window.URL.createObjectURL(blob);
            a.href = url;
            a.download = filename;
            a.click();

            // Cleanup
            window.URL.revokeObjectURL(url);
            Swal.fire('Success', 'The file has been downloaded.', 'success');
        } else {
            Swal.fire('Error', 'Failed to download the file.', 'error');
        }
    };

    xhr.send();
}

    </script>
</body>
</html>