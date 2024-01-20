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

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('./adminsidebar.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addabout'])) {
    // Retrieve the form data
    $about_content = $_POST['aboutContent'];
    $about_status = $_POST['aboutStatus'];
    $component_name = $_POST['aboutComponent'];
    
    // Check if the new home page should be published
    if ($about_status == 1) {
        // Set the status of all other home pages to unpublished
        $update_query = "UPDATE abouttable SET about_status = 0 WHERE about_component = '$component_name'";
        $con->query($update_query);
    }
    
    // Upload the image file
    $image_name = $_FILES['aboutImg']['name'];
    $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
    $image_tmp = $_FILES['aboutImg']['tmp_name'];
    $image_path = '../assets/img/aboutimg/' . $image_name;
    
    // Check if the file with the same name already exists
    $counter = 1;
    while (file_exists($image_path)) {
        $filename_parts = pathinfo($image_name);
        $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
        $image_path = '../assets/img/aboutimg/' . $new_filename;
        $counter++;
    }
    
    $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
    if ($_FILES['aboutImg']['size'] > $max_image_size) {
        echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
    } elseif (move_uploaded_file($image_tmp, $image_path)) {
        // Image uploaded successfully, insert data into the database
        $insert_query = "INSERT INTO abouttable (about_content, about_img, about_component, about_status) VALUES ('$about_content', '$image_path', '$component_name', '$about_status')";
        if ($con->query($insert_query) === true) {
            // Success message
            echo "<script>Swal.fire('Success', 'Data saved successfully.', 'success').then(() => {
                window.location.href = 'about-page.php';
            });</script>";
        } else {
            // Error message
            echo "<script>Swal.fire('Error', 'Error: " . $con->error . "', 'error');</script>";
        }
    } else {
        // Error message if failed to move the uploaded file
        echo "<script>Swal.fire('Error', 'Failed to upload image.', 'error');</script>";
    }
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateaboutpage'])) {
    // Get the form data
    $aboutId = $_POST["update_about_id"];
    $aboutContent = $_POST["update_about_content"];
    $aboutComponent = $_POST["update_about_component"];
    $aboutStatus = $_POST["update_about_status"];

    $allowUpdate = true;

    // Check if the new about page should be published
    if ($aboutStatus == 1) {
        // Set the status of all other about pages to unpublished
        $update_query = "UPDATE abouttable SET about_status = 0 WHERE about_component = '$aboutComponent'";
        $con->query($update_query);
    }elseif($aboutStatus == 0){
        // Check if there are any other published pages
        $check_published_query = "SELECT COUNT(*) AS published_count FROM abouttable WHERE about_status = 1 AND about_id != $aboutId AND about_component = '$aboutComponent'";
        $published_result = $con->query($check_published_query);
        $published_count = $published_result->fetch_assoc()['published_count'];

        if ($published_count <= 0) {
            $allowUpdate = false;
            echo "<script>Swal.fire('Error', 'At least one page must be published.', 'error');</script>";
            
        }
    }
    
    if($allowUpdate){
        // Check if a new image is uploaded
        if (!empty($_FILES["update_about_img"]["name"])) {
            $select_query = "SELECT about_img FROM abouttable WHERE about_id = $aboutId";
            $select_result = $con->query($select_query);
            $row = $select_result->fetch_assoc();
            $currentImagePath = $row['about_img'];
            
            // Upload the new image file
            $image_name = $_FILES['update_about_img']['name'];
            $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
            $image_tmp = $_FILES['update_about_img']['tmp_name'];
            $image_path = '../assets/img/aboutimg/' . $image_name;
            
            // Check if the file with the same name already exists
            $counter = 1;
            while (file_exists($image_path)) {
                $filename_parts = pathinfo($image_name);
                $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
                $image_path = '../assets/img/aboutimg/' . $new_filename;
                $counter++;
            }
            
            $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
            if ($_FILES['update_about_img']['size'] > $max_image_size) {
                echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
            } elseif (move_uploaded_file($image_tmp, $image_path)) {
                // Image uploaded successfully, update data in the database
                $update_query = "UPDATE abouttable SET about_content = '$aboutContent', about_img = '$image_path', about_status = '$aboutStatus' WHERE about_id = $aboutId";
                if ($con->query($update_query) === true) {
                    // Success message
                    echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success').then(() => {
                        window.location.href = 'about-page.php';
                    });</script>";
                } else {
                    // Error message
                    echo "<script>Swal.fire('Error', 'Error: " . $con->error . "', 'error');</script>";
                }
                
                // Remove the previous image file
                if (file_exists($currentImagePath)) {
                    unlink($currentImagePath);
                }
            } else {
                // Error message if failed to move the uploaded file
                echo "<script>Swal.fire('Error', 'Failed to upload image.', 'error');</script>";
            }
        } else {
            // If no new image is uploaded, update data without changing the image path
            $update_query = "UPDATE abouttable SET about_content = '$aboutContent', about_status = '$aboutStatus' WHERE about_id = $aboutId";
            if ($con->query($update_query) === true) {
                // Success message
                echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success').then(() => {
                    window.location.href = 'about-page.php';
                });</script>";
            } else {
                // Error message
                echo "<script>Swal.fire('Error', 'Error: " . $con->error . "', 'error');</script>";
            }
        }
    }
}
?>
<div class="home-main-container">
    <div class="studentList-container">
        <div class="page-title">
            <div class="titleContainer">
                <span class="group_id">About Page</span>
            </div>
        </div>

        <div class="buttonsContainer">
            <div class="buttonHolder">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAboutModal">
                    <i class='bx bx-plus-circle' ></i>Add About Page
                </button>
            </div>
        </div>
    
        <!-- Modal -->
        <div class="modal fade" id="addAboutModal" tabindex="-1" role="dialog" aria-labelledby="addAboutModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAboutModalLabel">Add About Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="aboutContent">Content</label>
                                <textarea class="form-control" id="aboutContent" name="aboutContent" rows="5" required></textarea>
                            </div>
                            <div class="form-group landingPageImageContainer">
                                <label for="aboutImg">Image</label>
                                <input type="file" class="form-control-file" id="aboutImg" name="aboutImg" accept="image/*" required>
                            </div>
                            <div class="form-group">
                                <label for="aboutComponent">Component</label>
                                <select class="form-control" id="aboutComponent" name="aboutComponent" required>
                                    <option value="ROTC">ROTC</option>
                                    <option value="CWTS">CWTS</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="aboutStatus">Status</label>
                                <select class="form-control" id="aboutStatus" name="aboutStatus" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublish</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="addabout">Add About Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Modal -->
        <div class="modal fade" id="updateabout" tabindex="-1" role="dialog" aria-labelledby="updateAboutModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateAboutModalLabel">Update About Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="update_about_content">About Content:</label>
                                <textarea class="form-control" id="update_about_content" rows="4" name="update_about_content" required></textarea>
                            </div>
                            <div class="form-group landingPageImageContainer">
                                <label for="update_about_img">About Image:</label>
                                <input type="file" class="form-control-file" id="update_about_img" name="update_about_img" accept="image/*">
                            </div>
                            <!-- Add the image preview element -->
                            <div class="form-group landingPageImageContainer">
                                <label>Current Image:</label>
                                <img id="update_about_img_preview" src="" alt="About Image Preview" class="img-thumbnail">
                            </div>
                            <div class="form-group">
                                <label for="update_about_component">About Component:</label>
                                <input type="text" class="form-control" id="update_about_component" name="update_about_component" readonly>
                            </div>
                            <div class="form-group">
                                <label for="update_about_status">About Status:</label>
                                <select class="form-control" id="update_about_status" name="update_about_status" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublished</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="update_about_id" name="update_about_id" value="">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="updateaboutpage">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php
        // Retrieve the home data from the database
        $sql = "SELECT * FROM abouttable";
        $result = $con->query($sql);
        ?>
        <div class="tableContainer">
            <table class="table table-sm caption-top">
            <caption>List of About Page</caption>
                <thead class="custom-thead">
                    <tr>
                        <th>About Content</th>
                        <th>About Image</th>
                        <th>About Component</th>
                        <th>About Status</th>
                        <th class='thAction'>Action</th>
                    </tr>
                </thead>
                <tbody id="file-table-body">
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='about-" . htmlspecialchars($row["about_id"]) . "'>";
                            echo "<td data-label='About Content'>" . htmlspecialchars($row["about_content"]) . "</td>";
                            echo "<td data-label='About Image'><img class='landingPageImage' src='" . htmlspecialchars($row["about_img"]) . "' alt='About Image'></td>";
                            echo "<td data-label='About Component' class='about-component'>" . htmlspecialchars($row["about_component"]) . "</td>";
                            echo "<td data-label='About Status'>" . ($row["about_status"] == 1 ? "Publish" : "Unpublished") . "</td>";
                            echo "<td data-label='Action'>
                                    <div class='groupButton'>
                                        <button type='button' class='btn btn-primary update-about-button' data-bs-toggle='modal' data-bs-target='#updateabout'>
                                            <i class='bx bx-wrench'></i>Update
                                        </button>
                                        <button type='button' class='btn btn-danger delete-about-button' data-aboutid='" . htmlspecialchars($row["about_id"]) . "'>
                                            <i class='bx bx-trash' ></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No data found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>

</section>
</div>
<script>
    $(document).ready(function() {
        // Handle delete button click event
        $('.delete-about-button').click(function() {
            var aboutId = $(this).data('aboutid');
            var aboutComponent = $(this).closest('tr').find('.about-component').text(); // Retrieve the value of the about component

            // Display a confirmation dialog
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to delete this data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, make an AJAX request to delete_about.php
                    $.ajax({
                        url: 'delete_about.php',
                        type: 'POST',
                        data: { aboutId: aboutId, aboutComponent: aboutComponent }, // Pass the value of aboutComponent
                        success: function(response) {
                            // If the deletion is successful, remove the corresponding row from the table
                            if (response == 'success') {
                                $('#about-' + aboutId).remove();
                                Swal.fire('Success', 'Data deleted successfully.', 'success');
                            } else if (response == 'error') {
                                // Show an error message if deletion was prevented
                                Swal.fire('Error', 'Cannot delete. At least one published data must remain.', 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Function to handle the click event of the update button
    function handleUpdateAbout(event) {
        // Get the row element
        var row = event.target.closest("tr");

        // Get the data from the row
        var aboutContent = row.querySelector("td[data-label='About Content']").textContent;
        var aboutImgSrc = row.querySelector("td[data-label='About Image'] img").getAttribute("src");
        var aboutComponent = row.querySelector("td[data-label='About Component']").textContent;
        var aboutStatus = row.querySelector("td[data-label='About Status']").textContent;
        var aboutId = row.id.replace("about-", "");

        // Populate the form fields with the data
        document.getElementById("update_about_content").value = aboutContent;
        document.getElementById("update_about_component").value = aboutComponent;
        document.getElementById("update_about_id").value = aboutId;

        // Set the image preview in the modal
        var imagePreview = document.getElementById("update_about_img_preview");
        imagePreview.src = aboutImgSrc;

        // Set the home status in the select element
        var aboutStatusSelect = document.getElementById("update_about_status");
        if (aboutStatus === "Publish") {
            aboutStatusSelect.value = "1";
        } else {
            aboutStatusSelect.value = "0";
        }
    }

    // Add event listeners to the update buttons
    var updateButtons = document.getElementsByClassName("update-about-button");
    for (var i = 0; i < updateButtons.length; i++) {
        updateButtons[i].addEventListener("click", handleUpdateAbout);
    }
});
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>