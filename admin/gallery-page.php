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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addgallery'])) {
    // Retrieve the form data
    $galler_status = $_POST['galleryStatus'];
    $galler_title = $_POST['galleryTitle'];
    $component_name = $_POST['galleryComponent'];
    
    // Upload the image file
    $image_name = $_FILES['galleryImg']['name'];
    $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
    $image_tmp = $_FILES['galleryImg']['tmp_name'];
    $image_path = '../assets/img/galleryimg/' . $image_name;
    
    // Check if the file with the same name already exists
    $counter = 1;
    while (file_exists($image_path)) {
        $filename_parts = pathinfo($image_name);
        $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
        $image_path = '../assets/img/galleryimg/' . $new_filename;
        $counter++;
    }
    
    $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
    if ($_FILES['galleryImg']['size'] > $max_image_size) {
        echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
    } elseif (move_uploaded_file($image_tmp, $image_path)) {
        // Image uploaded successfully, insert data into the database
        $insert_query = "INSERT INTO gallerytable (gallery_title, gallery_img, gallery_component, gallery_status, gallery_time) VALUES ('$galler_title', '$image_path', '$component_name', '$galler_status', NOW())";
        if ($con->query($insert_query) === true) {
            // Success message
            echo "<script>Swal.fire('Success', 'Data saved successfully.', 'success').then(() => {
                window.location.href = 'gallery-page.php';
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updategallery'])) {
    // Get the form data
    $galleryId = $_POST["update_gallery_id"];
    $galleryTitle = $_POST["update_gallery_title"];
    $galleryComponent = $_POST["update_gallery_component"];
    $galleryStatus = $_POST["update_gallery_status"];

    $allowUpdate = true;

    // Check if the new about page should be published
    if($galleryStatus == 0){
        // Check if there are any other published pages
        $check_published_query = "SELECT COUNT(*) AS published_count FROM gallerytable WHERE gallery_status = 1 AND gallery_component = '$galleryComponent' AND gallery_id != $galleryId";
        $published_result = $con->query($check_published_query);
        $published_count = $published_result->fetch_assoc()['published_count'];
        echo "Published Count: " . $published_count;
        if ($published_count <= 0) {
            $allowUpdate = false;
            echo "<script>Swal.fire('Error', 'At least one page must be published.', 'error');</script>";
            
        }
    }
    
    if($allowUpdate){
        // Check if a new image is uploaded
        if (!empty($_FILES["update_gallery_img"]["name"])) {
            $select_query = "SELECT gallery_img FROM gallerytable WHERE gallery_id = $galleryId";
            $select_result = $con->query($select_query);
            $row = $select_result->fetch_assoc();
            $currentImagePath = $row['gallery_img'];
            
            // Upload the new image file
            $image_name = $_FILES['update_gallery_img']['name'];
            $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
            $image_tmp = $_FILES['update_gallery_img']['tmp_name'];
            $image_path = '../assets/img/galleryimg/' . $image_name;
            
            // Check if the file with the same name already exists
            $counter = 1;
            while (file_exists($image_path)) {
                $filename_parts = pathinfo($image_name);
                $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
                $image_path = '../assets/img/galleryimg/' . $new_filename;
                $counter++;
            }
            
            $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
            if ($_FILES['update_gallery_img']['size'] > $max_image_size) {
                echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
            } elseif (move_uploaded_file($image_tmp, $image_path)) {
                // Image uploaded successfully, update data in the database
                $update_query = "UPDATE gallerytable SET gallery_title = '$galleryTitle', gallery_img = '$image_path', gallery_status = '$galleryStatus' WHERE gallery_id = $galleryId";
                if ($con->query($update_query) === true) {
                    // Success message
                    echo "<script>
                            Swal.fire('Success', 'Data updated successfully.', 'success').then(() => {
                                window.location.href = 'gallery-page.php';
                            });
                        </script>";

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
            $update_query = "UPDATE gallerytable SET gallery_title = '$galleryTitle', gallery_status = '$galleryStatus' WHERE gallery_id = $galleryId";
            if ($con->query($update_query) === true) {
                // Success message
                echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success').then(() => {
                    window.location.href = 'gallery-page.php';
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
                <span class="group_id">Gallery Page</span>
            </div>
        </div>
        <div class="buttonsContainer">
            <div class="buttonHolder">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                    <i class='bx bx-plus-circle' ></i>Add Gallery
                </button>
            </div>
        </div>

        <!-- Add Gallery Modal -->
        <div class="modal fade" id="addGalleryModal" tabindex="-1" aria-labelledby="addGalleryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addGalleryModalLabel">Add Gallery Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add your form fields here to capture gallery information -->
                        <form method="POST" enctype="multipart/form-data">

                            <div class="form-group">
                                <label for="galleryTitle">Gallery Title</label>
                                <input type="text" class="form-control" id="galleryTitle" name="galleryTitle" required>
                            </div>

                            <div class="form-group">
                                <label for="galleryImg" class="form-label">Gallery Image</label>
                                <input type="file" class="form-control" id="galleryImg" name="galleryImg" accept="image/*" required>
                            </div>

                            <div class="form-group">
                                <label for="galleryComponent" class="form-label">Gallery Component</label>
                                <select class="form-control" id="galleryComponent" name="galleryComponent" required>
                                    <option value="ROTC">ROTC</option>
                                    <option value="CWTS">CWTS</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="galleryStatus" class="form-label">Gallery Status</label>
                                <select class="form-control" id="galleryStatus" name="galleryStatus" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublished</option>
                                </select>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="addgallery">Add Gallery Data</button>
                    </div>
                        </form>
                </div>
            </div>
        </div>

        <!-- Update Gallery Modal -->
        <div class="modal fade" id="updategallery" tabindex="-1" aria-labelledby="updategalleryLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updategalleryLabel">Update Gallery Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add your form fields here to capture gallery information -->
                        <form method="POST" enctype="multipart/form-data">

                            <div class="form-group">
                                <label for="update_gallery_title" class="form-label">Gallery Title</label>
                                <input type="text" class="form-control" id="update_gallery_title" name="update_gallery_title">
                            </div>

                            <div class="form-group">
                                <label for="update_gallery_img" class="form-label">Gallery Image</label>
                                <input type="file" class="form-control" id="update_gallery_img" name="update_gallery_img" accept="image/*">
                            </div>

                            <div class="form-group landingPageImageContainer">
                                <label>Current Image:</label>
                                <img id="update_gallery_img_preview" src="" alt="Gallery Image Preview" class="img-thumbnail">
                            </div>

                            <div class="form-group">
                                <label for="update_gallery_component" class="form-label">Gallery Component</label>
                                <input type="text" class="form-control" id="update_gallery_component" name="update_gallery_component" readonly>
                            </div>
                            <div class="form-group">
                                <label for="update_gallery_status" class="form-label">Gallery Status</label>
                                <select class="form-control" id="update_gallery_status" name="update_gallery_status" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublished</option>
                                </select>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="update_gallery_id" name="update_gallery_id" value="">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="updategallery">Save Changes</button>
                    </div>
                        </form>
                </div>
            </div>
        </div>

        <?php
        // Retrieve the home data from the database
        $sql = "SELECT * FROM gallerytable ORDER BY gallery_status DESC, gallery_id DESC";
        $result = $con->query($sql);
        ?>
        <div class="tableContainer">
            <table class="table table-sm caption-top">
            <caption>List of Gallery</caption>
                <thead class="custom-thead">
                    <tr>
                        <th>Gallery Title</th>
                        <th>Gallery Image</th>
                        <th>Gallery Component</th>
                        <th>Gallery Status</th>
                        <th class='thAction'>Action</th>
                    </tr>
                </thead>
                <tbody id="file-table-body">
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='gallery-" . htmlspecialchars($row["gallery_id"]) . "'>";
                            echo "<td data-label='Gallery Title'>" . htmlspecialchars($row["gallery_title"]) . "</td>";
                            echo "<td data-label='Gallery Image'><img class='landingPageImage' src='" . htmlspecialchars($row["gallery_img"]) . "' alt='Gallery Image'></td>";
                            echo "<td data-label='Gallery Component' class='gallery-component'>" . htmlspecialchars($row["gallery_component"]) . "</td>";
                            echo "<td data-label='Gallery Status'>" . ($row["gallery_status"] == 1 ? "Publish" : "Unpublished") . "</td>";
                            echo "<td data-label='Action'>
                                    <div class='groupButton'>
                                        <button type='button' class='btn btn-primary update-gallery-button' data-bs-toggle='modal' data-bs-target='#updategallery'>
                                            <i class='bx bx-wrench'></i>Update
                                        </button>
                                        <button type='button' class='btn btn-danger delete-gallery-button' data-galleryid='" . htmlspecialchars($row["gallery_id"]) . "'>
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
        $('.delete-gallery-button').click(function() {
            var galleryId = $(this).data('galleryid');
            var galleryComponent = $(this).closest('tr').find('.gallery-component').text(); // Retrieve the value of the about component

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
                        url: 'delete_gallery.php',
                        type: 'POST',
                        data: { galleryId: galleryId, galleryComponent: galleryComponent }, // Pass the value of galleryComponent
                        success: function(response) {
                            // If the deletion is successful, remove the corresponding row from the table
                            if (response == 'success') {
                                $('#gallery-' + galleryId).remove();
                                Swal.fire('Success', 'Data deleted successfully.', 'success');
                            } else if (response == 'error') {
                                // Show an error message if deletion was prevented
                                Swal.fire('Error', 'Cannot delete. The published data must be unpublished first before being deleted.', 'error');
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
    function handleUpdateGallery(event) {
        // Get the row element
        var row = event.target.closest("tr");

        // Get the data from the row
        var galleryTitle = row.querySelector("td[data-label='Gallery Title']").textContent;
        var galleryImgSrc = row.querySelector("td[data-label='Gallery Image'] img").getAttribute("src");
        var galleryComponent = row.querySelector("td[data-label='Gallery Component']").textContent;
        var galleryStatus = row.querySelector("td[data-label='Gallery Status']").textContent;
        var galleryId = row.id.replace("gallery-", "");

        // Populate the form fields with the data
        document.getElementById("update_gallery_title").value = galleryTitle;
        document.getElementById("update_gallery_component").value = galleryComponent;
        document.getElementById("update_gallery_id").value = galleryId;

        // Set the image preview in the modal
        var imagePreview = document.getElementById("update_gallery_img_preview");
        imagePreview.src = galleryImgSrc;

        // Set the home status in the select element
        var galleryStatusSelect = document.getElementById("update_gallery_status");
        if (galleryStatus === "Publish") {
            galleryStatusSelect.value = "1";
        } else {
            galleryStatusSelect.value = "0";
        }
    }

    // Add event listeners to the update buttons
    var updateButtons = document.getElementsByClassName("update-gallery-button");
    for (var i = 0; i < updateButtons.length; i++) {
        updateButtons[i].addEventListener("click", handleUpdateGallery);
    }
});
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
