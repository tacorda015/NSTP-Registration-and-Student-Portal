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
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = {$user_id}";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$component_name = $useraccount_data['component_name'];
$student_number = $useraccount_data['student_number'];
$role_account_id = $useraccount_data['role_account_id'];
$group_id = $useraccount_data['group_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} 

if ($component_name == 'ROTC') {
    $rotc_query = "SELECT * FROM trainertable WHERE trainer_uniquenumber = {$student_number}";
    $rotc_result = $con->query($rotc_query);
    $incharge_data = $rotc_result->fetch_assoc();
} elseif ($component_name == 'CWTS') {
    $cwts_query = "SELECT * FROM teachertable WHERE teacher_uniquenumber = {$student_number}";
    $cwts_result = $con->query($cwts_query);
    $incharge_data = $cwts_result->fetch_assoc();
}

// Calling the sidebar
include_once('./teachersidebar.php');
?>
            <div class='home-main-container'>
                <div class='studentList-container'>
<?php

if ($group_id !== null) {
    $recordsPerPage = 10;
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $currentPage = intval($_GET['page']);
    } else {
        $currentPage = 1;
    }

    // Calculate the offset for the current page
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Modify your SQL query to include the LIMIT and OFFSET clauses for pagination
    $sql_file = "SELECT * FROM filetable WHERE group_id = $group_id";

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchQuery = mysqli_real_escape_string($con, $_GET['search']);
        $sql_file .= " AND (title LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%' OR file_name LIKE '%$searchQuery%')";
    }

    $sql_file .= " ORDER BY file_id DESC LIMIT $recordsPerPage OFFSET $offset";

    $result = $con->query($sql_file);

    $group_query = "SELECT group_name FROM grouptable WHERE group_id = {$group_id}";
    $group_result = $con->query($group_query);
    $group_data = $group_result->fetch_assoc();

    if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['updatelecture'])){
        $fileId = $_POST['fileId'];
        $fileTitle = $_POST['fileTitle'];
        $fileDescription = $_POST['fileDescription'];

        $updateFile = "UPDATE filetable SET title = '$fileTitle', description = '$fileDescription' WHERE file_id = $fileId";
        $updateFileResult = $con->query($updateFile);

        if($updateFileResult){
            echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Updating Module successfully.',
                icon: 'success',
                confirmButtonText: 'OK',
              }).then(function () {
                window.location = 'modulelist.php';
              });
              </script>";
        }else{
            echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'There was an error uploading the module. Please try again later.',
                icon: 'error',
                confirmButtonText: 'OK',
              });
              </script>";
        }
    }

    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addFile'])) {
        echo "<script>console.log('dsdssssssdsd');</script>";
        // Get the file data
        $file = $_FILES['file'];

        // Get the metadata from the form
        $title = $_POST['title'];
        $description = $_POST['description'];

        // Check if the file was uploaded without errors
        if ($file['error'] === UPLOAD_ERR_OK) {
            // Get the file information
            $name = basename($file['name']);
            $type = $file['type'];
            $size = $file['size'];
            $tmp_name = $file['tmp_name'];

            // Check the file size
            $max_size = 1024 * 1024 * 10; // 10 MB (adjust this value as needed)
            if ($size > $max_size) {
                // Display an error message
                echo "File size exceeds the allowed limit.";
                exit; // Exit the script to prevent further execution
            }

            // Check the file extension
            $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'odt', 'ods', 'odp', 'odg', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg', 'mp3', 'wav', 'ogg', 'mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'zip', 'rar']; // Add any other allowed extensions here
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), $allowed_extensions)) {
                die('Error: Invalid file extension');
            }


            // Generate a unique filename
            $filename = $name;
            $i = 1;
            while (file_exists("../groupmodule/group_$group_id/$filename")) {
                $filename = pathinfo($name, PATHINFO_FILENAME) . " ($i)." . $extension;
                $i++;
            }

            // Move the uploaded file to a new location
            $upload_dir = '../groupmodule/group_' . $group_id . '/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $destination = $upload_dir . $filename;
            if (!move_uploaded_file($tmp_name, $destination)) {
                die('Error: Failed to move uploaded file');
            }

            // Store file metadata in the database
            $title = mysqli_real_escape_string($con, $title);
            $description = mysqli_real_escape_string($con, $description);
            $filename = mysqli_real_escape_string($con, $filename);
            $type = mysqli_real_escape_string($con, $type);
            $size = mysqli_real_escape_string($con, $size);
            $date = date('Y-m-d H:i:s');

            $sql = "INSERT INTO filetable (title, group_id, description, file_name, file_type, file_size, date_upload) 
                    VALUES ('$title', '$group_id', '$description', '$filename', '$type', '$size', '$date')";
            $result = $con->query($sql);
            if (!$result) {
                // Display an error message
                echo "Failed to store file metadata.";
                exit; // Exit the script to prevent further execution
            }
        } else {
            die('Error: Failed to upload file');
        }
    }
    ?>
                <div id="loader-overlay" class="loader-overlay"></div>
                <div id="loader" class="loader">Sending <span></span></div>
                    <div class='page-title'>
                        <div class="titleContainer">
                            <span class="group-id"><?php echo $group_data['group_name']; ?></span>
                            <label class='in-charge-label'>Group Name</label>
                        </div>
                        <?php
                            if (mysqli_num_rows($result) > 0) {
                        ?> 
                        <form method="get" enctype="multipart/form-data">
                            <div class="search-container">
                                <input id="search"  type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>
                                <button type="submit"><i class='bx bx-search'></i></button>
                            </div>
                        </form>
                        <?php
                            }
                        ?>
                    </div>

                    <div class='buttonsContainer'>
                        <div class='buttonHolder'>
                            <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#uploadmodule'>
                            <i class='bx bx-cloud-upload'></i>Upload Module</button>
                        </div>
                    </div>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                    ?> 
                    <div class='tableContainer'>
                        <table class='table table-sm caption-top'>
                        <caption>List of Module</caption>
                            <thead class="custom-thead">
                                <tr>
                                    <th>File Name</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Upload Date</th>
                                    <th class='thAction'>Action</th>
                                </tr>
                            </thead>
                            <tbody id='file-body'>
                                <?php
                                    while ($row = mysqli_fetch_assoc($result)) {

                                        $file_id = $row['file_id']; // Assuming 'id' is the primary key column
                                        $title = htmlspecialchars($row['title']);
                                        $description = htmlspecialchars($row['description']);
                                        $filename = htmlspecialchars($row['file_name']);
                                        $date = htmlspecialchars($row['date_upload']);

                                        echo "<tr id='file-row-$file_id'>";
                                        echo "<td data-label='File name'><a href='../groupmodule/group_{$group_id}/{$filename}'>{$filename}</a></td>";
                                        echo "<td data-label='Title'>{$title}</td>";
                                        echo "<td data-label='Description'>{$description}</td>";
                                        echo "<td data-label='Date Uploaded'>{$date}</td>";
                                        echo "<td data-label='Action'>
                                                <div class=\"groupButton\">
                                                    <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#fileUpdateModal" . $file_id . "'>
                                                    <i class='bx bx-wrench'></i>Update</button>
                                                    <button class='btn btn-danger' onclick='deleteFile($file_id)'>
                                                    <i class='bx bx-trash' ></i>Delete</button>
                                                </div>
                                        </td>";
                                        echo "</tr>";
                                        echo "<div class='modal fade' id='fileUpdateModal".$file_id."' tabindex='-1' aria-labelledby='updatemodalLabel' aria-hidden='true'>
                                        <div class='modal-dialog'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='updatemodalLabel'>Update File Information</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>
                                                <form method='post' enctype='multipart/form-data'>
                                                    <div class='modal-body'>
                                                        <input type='hidden' name='fileId' value='".$file_id."'>
                                                        <div class='form-group'>
                                                            <label for='fileTitle'>File Title:</label>
                                                            <input type='text' class='form-control' id='fileTitle' name='fileTitle' value='".$title."' required>
                                                        </div>
                                                        <div class='form-group'>
                                                            <label for='fileDescription'>File Description:</label>
                                                            <input type='text' class='form-control' name='fileDescription' id='fileDescription' value='".$description."' required>
                                                        </div>
                                                       <div class='form-group'>
                                                            <label for='fileUploaded'>File Uploaded:</label>
                                                            <input type='text' class='form-control' id='fileUploaded' name='fileUploaded' value='".$date."' readonly>
                                                        </div>
                                                       <div class='form-group'>
                                                            <label for='fileName'>File Uploaded:</label>
                                                            <input type='text' class='form-control' id='fileName' name='fileName' value='".$filename."' readonly>
                                                        </div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                                        <button type='submit' class='btn btn-primary' name='updatelecture'>Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>

                <?php
                    // Get the total number of records for pagination
                    $totalRecordsQuery = "SELECT COUNT(*) AS total FROM filetable WHERE group_id = $group_id";
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $totalRecordsQuery .= " AND (title LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%' OR file_name LIKE '%$searchQuery%')";
                    }

                    $totalRecordsResult = $con->query($totalRecordsQuery);
                    $totalRecordsData = $totalRecordsResult->fetch_assoc();
                    $totalRecords = $totalRecordsData['total'];

                    // Calculate the total number of pages
                    $totalPages = ceil($totalRecords / $recordsPerPage);

                    // Pagination links using Bootstrap
                    echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                        <ul class='pagination justify-content-end'>";

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
                        </li>
                    </ul>
                    </nav>";
                } else {
                    echo "<h2 style='text-align:center;'>No files found</h2>";
                }
            }else{
                echo "<h2 style='text-align: center;'>No Assigned Group yet.</h2>";
            }
        ?>
            <!-- Start of Upload file modal -->
            <div class="modal fade" id="uploadmodule" tabindex="1" aria-labelledby="uploadmodule" aria-hidden="true">
                <div class="modal-dialog" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">Upload Module</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="modulelist.php" method="POST" enctype="multipart/form-data" id="upload-form">
                            <div class="modal-body" style="z-index: 0;">
                                <div class="form-group">
                                    <label for="title">Title:</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description:</label>
                                    <input type="text" class="form-control" id="description" name="description" required>
                                </div>
                                <div class="form-group">
                                    <label for="file">Select file:</label>
                                    <input type="file" class="form-control" id="file" name="file" required>
                                </div>
                                <input type="hidden" name="addFile" value="TRUE">
                            </div>
                            <div class="modal-footer">
                                <button type="button"class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="addFile" id="submit-button">Upload Module</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End of Upload file modal -->
            </div>
        </div>
    </section>
</div>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
<script>
    // Start Uploading files

// Reset the form fields when the modal is closed
$('#uploadmodule').on('hidden.bs.modal', function () {
  $('#upload-form')[0].reset();
});

var sendingEmail = false;
// Confirmation message when refreshing or leaving the page
window.addEventListener('beforeunload', function (e) {
  if (sendingEmail) {
    // Show confirmation message only if email sending process has started
    e.preventDefault();
    e.returnValue = '';

    var confirmationMessage =
      'Changes you made may not be saved. Are you sure you want to leave this page?';
    (e || window.event).returnValue = confirmationMessage;
    return confirmationMessage;
  }
});

// Add an event listener to the form submission
document.getElementById('upload-form').addEventListener('submit', function (e) {
  e.preventDefault(); // Prevent form submission

  sendingEmail = true;
  document.getElementById('submit-button').setAttribute('disabled', 'disabled'); // Disable the button
  document.getElementById('loader-overlay').style.display = 'block'; // Show the loader overlay
  document.getElementById('loader').style.display = 'block'; // Show the loader
  console.log('dsdsdsd');

  // Get the file input element and selected file
  var fileInput = document.getElementById('file');
  var file = fileInput.files[0];

  // Check if a file is selected
  if (!file) {
    // Display a SweetAlert2 error message if no file is selected
    Swal.fire({
      title: 'Error!',
      text: 'Please select a file.',
      icon: 'error',
      confirmButtonText: 'OK',
    });
    return; // Stop further execution of the code
  }

  // Get the file size
  var fileSize = file.size;

  // Check if the file size exceeds the limit
  var maxSize = 1024 * 1024 * 10; // 10 MB (adjust this value as needed)
  if (fileSize > maxSize) {
    // Display a SweetAlert2 error message if the file size exceeds the limit
    Swal.fire({
      title: 'Error!',
      text: 'The file size exceeds the allowed limit(50MB).',
      icon: 'error',
      confirmButtonText: 'OK',
    });
    return; // Stop further execution of the code
  }

  // Proceed with form submission using AJAX
  var form = document.getElementById('upload-form');
  var formData = new FormData(form);

  $.ajax({
    url: form.action,
    type: form.method,
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      sendingEmail = false;
      document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
      document.getElementById('loader').style.display = 'none'; // Hide the loader
      // Show SweetAlert2 success message
      Swal.fire({
        title: 'Success!',
        text: 'The module has been uploaded successfully.',
        icon: 'success',
        confirmButtonText: 'OK',
      }).then(function () {
        window.location = 'modulelist.php';
      });
    },
    error: function () {
      // Show SweetAlert2 error message
      sendingEmail = false;
      document.getElementById('loader-overlay').style.display = 'none'; // Hide the loader overlay
      document.getElementById('loader').style.display = 'none'; // Hide the loader
      Swal.fire({
        title: 'Error!',
        text: 'There was an error uploading the module. Please try again later.',
        icon: 'error',
        confirmButtonText: 'OK',
      });
    },
  });
});

//   End of Uploading files

</script>
<!-- <script src="../assets/js/sweetalerts.js"></script> -->
</body>
</html>