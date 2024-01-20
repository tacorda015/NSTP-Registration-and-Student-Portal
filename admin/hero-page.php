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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addhomepage'])) {
    // Retrieve the form data
    $home_content = $_POST['home_content'];
    $home_status = $_POST['home_status'];
    
    // Check if the new home page should be published
    if ($home_status == 1) {
        // Set the status of all other home pages to unpublished
        $update_query = "UPDATE hometable SET home_status = 0 ";
        $con->query($update_query);
    }
    
    // Upload the image file
    $image_name = $_FILES['home_img']['name'];
    $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
    $image_tmp = $_FILES['home_img']['tmp_name'];
    $image_path = '../assets/img/homeimg/' . $image_name;
    
    // Check if the file with the same name already exists
    $counter = 1;
    while (file_exists($image_path)) {
        $filename_parts = pathinfo($image_name);
        $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
        $image_path = '../assets/img/homeimg/' . $new_filename;
        $counter++;
    }
    
    $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
    if ($_FILES['home_img']['size'] > $max_image_size) {
        echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
    } elseif (move_uploaded_file($image_tmp, $image_path)) {
        // Image uploaded successfully, insert data into the database
        $insert_query = "INSERT INTO hometable (home_content, home_img, home_status) VALUES ('$home_content', '$image_path', '$home_status')";
        if ($con->query($insert_query) === true) {
            // Success message
            echo "<script>Swal.fire('Success', 'Data saved successfully.', 'success');</script>";
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updatehomepage'])) {
    // Get the form data
    $homeId = $_POST["update_home_id"];
    $homeContent = $_POST["update_home_content"];
    $homeStatus = $_POST["update_home_status"];

    $allowUpdate = true;

    // Check if the new home page should be published
    if ($homeStatus == 1) {
        // Set the status of all other home pages to unpublished
        $update_query = "UPDATE hometable SET home_status = 0 ";
        $con->query($update_query);
    }elseif($homeStatus == 0){
        // Check if there are any other published pages
        $check_published_query = "SELECT COUNT(*) AS published_count FROM hometable WHERE home_status = 1 AND home_id != $homeId";
        $published_result = $con->query($check_published_query);
        $published_count = $published_result->fetch_assoc()['published_count'];

        if ($published_count <= 0) {
            $allowUpdate = false;
            echo "<script>Swal.fire('Error', 'At least one page must be published.', 'error');</script>";
            
        }
    }
    
    if($allowUpdate){
        // Check if a new image is uploaded
        if (!empty($_FILES["update_home_img"]["name"])) {
            $select_query = "SELECT home_img FROM hometable WHERE home_id = $homeId";
            $select_result = $con->query($select_query);
            $row = $select_result->fetch_assoc();
            $currentImagePath = $row['home_img'];
            
            // Upload the new image file
            $image_name = $_FILES['update_home_img']['name'];
            $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
            $image_tmp = $_FILES['update_home_img']['tmp_name'];
            $image_path = '../assets/img/homeimg/' . $image_name;
            
            // Check if the file with the same name already exists
            $counter = 1;
            while (file_exists($image_path)) {
                $filename_parts = pathinfo($image_name);
                $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
                $image_path = '../assets/img/homeimg/' . $new_filename;
                $counter++;
            }
            
            $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
            if ($_FILES['update_home_img']['size'] > $max_image_size) {
                echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
            } elseif (move_uploaded_file($image_tmp, $image_path)) {
                // Image uploaded successfully, update data in the database
                $update_query = "UPDATE hometable SET home_content = '$homeContent', home_img = '$image_path', home_status = '$homeStatus' WHERE home_id = $homeId";
                if ($con->query($update_query) === true) {
                    // Success message
                    echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success');</script>";
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
            $update_query = "UPDATE hometable SET home_content = '$homeContent', home_status = '$homeStatus' WHERE home_id = $homeId";
            if ($con->query($update_query) === true) {
                // Success message
                echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success');</script>";
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
                <span class="group_id">Hero Page</span>
            </div>
        </div>
        <div class="buttonsContainer">
            <div class="buttonHolder">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addhome">
                    <i class='bx bx-plus-circle' ></i>Add Home Page
                </button>
            </div>
        </div>
    
        <!-- Modal -->
        <div class="modal fade" id="addhome" tabindex="-1" role="dialog" aria-labelledby="addhome" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addhome">Add Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="home_content">Content:</label>
                                <textarea class="form-control" id="home_content" name="home_content" required></textarea>
                            </div>
                            <div class="form-group landingPageImageContainer">
                                <label for="home_img">Image:</label>
                                <input type="file" class="form-control-file" id="home_img" name="home_img" accept="image/*" required>
                            </div>
                            <div class="form-group">
                                <label for="home_status">Status:</label>
                                <select class="form-control" id="home_status" name="home_status" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublished</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="addhomepage">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Update Modal -->
        <div class="modal fade" id="updatehome" tabindex="-1" role="dialog" aria-labelledby="updateHomeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateHomeModalLabel">Update Home</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="update-home-form" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="update_home_content">Home Content:</label>
                                <textarea class="form-control" id="update_home_content" rows="4" name="update_home_content" required></textarea>
                            </div>
                            <div class="form-group landingPageImageContainer">
                                <label for="update_home_img">Home Image:</label>
                                <input type="file" class="form-control-file" id="update_home_img" name="update_home_img" accept="image/*">
                                <!-- <input type="file" class="form-control-file" id="update_home_img" name="update_home_img" accept="image/*"> -->
                            </div>
                            <!-- Add the image preview element -->
                            <div class="form-group landingPageImageContainer">
                                <label>Current Image:</label>
                                <img id="update_home_img_preview" src="" alt="Home Image Preview" class="img-thumbnail">
                            </div>
                            <div class="form-group">
                                <label for="update_home_status">Home Status:</label>
                                <select class="form-control" id="update_home_status" name="update_home_status" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublished</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="update_home_id" name="update_home_id" value="">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="updatehomepage">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <?php
        // Retrieve the home data from the database
        $sql = "SELECT * FROM hometable";
        $result = $con->query($sql);
        ?>
        <div class="tableContainer">
            <table class="table table-sm caption-top">
            <caption>List of Home Page</caption>
                <thead class="custom-thead">
                    <tr>
                        <th>Home Content</th>
                        <th>Home Image</th>
                        <th>Home Status</th>
                        <th class='thAction'>Action</th>
                    </tr>
                </thead>
                <tbody id="file-table-body">
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='home-" . htmlspecialchars($row["home_id"]) . "'>
                                    <td data-label='Home Content'>" . htmlspecialchars($row["home_content"]) . "</td>
                                    <td data-label='Home Image'><img class='landingPageImage' src='" . htmlspecialchars($row["home_img"]) . "' alt='Home Image' class='home-image'></td>
                                    <td data-label='Home Status'>" . ($row["home_status"] == 1 ? "Publish" : "Unpublished") . "</td>
                                    <td data-label='Action'>
                                        <div class='groupButton'>
                                            <button type='button' class='btn btn-primary update-home-button' data-bs-toggle='modal' data-bs-target='#updatehome'>
                                                <i class='bx bx-wrench'></i>Update
                                            </button>
                                            <button type='button' class='btn btn-danger delete-home-button' data-homeid='" . htmlspecialchars($row["home_id"]) . "'>
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
    $('.delete-home-button').click(function() {
        var homeId = $(this).data('homeid');
        
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
                // User confirmed, make an AJAX request to delete_home.php
                $.ajax({
                    url: 'delete_home.php',
                    type: 'POST',
                    data: { homeId: homeId },
                    success: function(response) {
                        // If the deletion is successful, remove the corresponding row from the table
                        if (response == 'success') {
                            $('#home-' + homeId).remove();
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
    function handleUpdateHome(event) {
        // Get the row element
        var row = event.target.closest("tr");

        // Get the data from the row
        var homeContent = row.querySelector("td[data-label='Home Content']").textContent;
        var homeImgSrc = row.querySelector("td[data-label='Home Image'] img").getAttribute("src");
        var homeStatus = row.querySelector("td[data-label='Home Status']").textContent;
        var homeId = row.id.replace("home-", "");

        // Populate the form fields with the data
        document.getElementById("update_home_content").value = homeContent;
        document.getElementById("update_home_id").value = homeId;

        // Set the image preview in the modal
        var imagePreview = document.getElementById("update_home_img_preview");
        imagePreview.src = homeImgSrc;

        // Set the home status in the select element
        var homeStatusSelect = document.getElementById("update_home_status");
        if (homeStatus === "Publish") {
            homeStatusSelect.value = "1";
        } else {
            homeStatusSelect.value = "0";
        }
    }

    // Add event listeners to the update buttons
    var updateButtons = document.getElementsByClassName("update-home-button");
    for (var i = 0; i < updateButtons.length; i++) {
        updateButtons[i].addEventListener("click", handleUpdateHome);
    }
});
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>
