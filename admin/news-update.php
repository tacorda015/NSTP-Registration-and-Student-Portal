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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addnew'])) {
    // Retrieve the form data
    $newsupdate_title = mysqli_real_escape_string($con, $_POST['newstitle']);
    $newsupdate_content = mysqli_real_escape_string($con, $_POST['newsContent']);
    $newsupdate_status = mysqli_real_escape_string($con, $_POST['newstatus']);

    
    // Upload the image file
    $image_name = $_FILES['newImg']['name'];
    $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
    $image_tmp = $_FILES['newImg']['tmp_name'];
    $image_path = '../assets/img/newsupdateImg/' . $image_name;
    
    // Check if the file with the same name already exists
    $counter = 1;
    while (file_exists($image_path)) {
        $filename_parts = pathinfo($image_name);
        $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
        $image_path = '../assets/img/newsupdateImg/' . $new_filename;
        $counter++;
    }
    
    $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
    if ($_FILES['newImg']['size'] > $max_image_size) {
        echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
    } elseif (move_uploaded_file($image_tmp, $image_path)) {
        // Image uploaded successfully, insert data into the database
        $insert_query = "INSERT INTO newsupdatetable (newsupdate_title, newsupdate_content, newsupdate_img, newsupdate_status, newsupdate_date) VALUES ('$newsupdate_title', '$newsupdate_content', '$image_path', '$newsupdate_status', NOW())";
        if ($con->query($insert_query) === true) {
            // Success message
            echo "<script>Swal.fire('Success', 'Data saved successfully.', 'success').then(() => {
                window.location.href = 'news-update.php';
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updatenewsupdate'])) {
    // Get the form data
    $newsupdate_id = $_POST["newsupdate_id"];
    $newsupdate_title = $_POST["update_news_title"];
    $newsupdate_content = $_POST["update_news_content"];
    $newsupdate_status = $_POST["update_news_status"];

    $allowUpdate = true;
    
    if($allowUpdate){
        // Check if a new image is uploaded
        if (!empty($_FILES["update_news_img"]["name"])) {
            $select_query = "SELECT newsupdate_img FROM newsupdatetable WHERE newsupdate_id = $newsupdate_id";
            $select_result = $con->query($select_query);
            $row = $select_result->fetch_assoc();
            $currentImagePath = $row['newsupdate_img'];
            
            // Upload the new image file
            $image_name = $_FILES['update_news_img']['name'];
            $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
            $image_tmp = $_FILES['update_news_img']['tmp_name'];
            $image_path = '../assets/img/newsupdateImg/' . $image_name;
            
            // Check if the file with the same name already exists
            $counter = 1;
            while (file_exists($image_path)) {
                $filename_parts = pathinfo($image_name);
                $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
                $image_path = '../assets/img/newsupdateImg/' . $new_filename;
                $counter++;
            }
            
            $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
            if ($_FILES['update_news_img']['size'] > $max_image_size) {
                echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
            } elseif (move_uploaded_file($image_tmp, $image_path)) {
                // Image uploaded successfully, update data in the database
                $update_query = "UPDATE newsupdatetable SET newsupdate_title = '$newsupdate_title',newsupdate_content = '$newsupdate_content', newsupdate_img = '$image_path', newsupdate_status = '$newsupdate_status' WHERE newsupdate_id = $newsupdate_id";
                if ($con->query($update_query) === true) {
                    // Success message
                    echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success').then(() => {
                        window.location.href = 'news-update.php';
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
            $update_query = "UPDATE newsupdatetable SET newsupdate_title = '$newsupdate_title', newsupdate_content = '$newsupdate_content', newsupdate_status = '$newsupdate_status' WHERE newsupdate_id = $newsupdate_id";
            if ($con->query($update_query) === true) {
                // Success message
                echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success').then(() => {
                    window.location.href = 'news-update.php';
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
                <span class="group_id">News and Update Page</span>
            </div>
        </div>

        <div class="buttonsContainer">
            <div class="buttonHolder">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsUpdate">
                    <i class='bx bx-plus-circle' ></i>Add News and Update
                </button>
            </div>
        </div>
    
        <!-- Modal -->
        <div class="modal fade" id="addNewsUpdate" tabindex="-1" role="dialog" aria-labelledby="addNewsUpdateLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewsUpdateLabel">Add News and Update</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="newstitle">Title</label>
                                <input class="form-control" type="text" name="newstitle" id="newstitle" require>
                            </div>
                            <div class="form-group">
                                <label for="newsContent">Content</label>
                                <textarea class="form-control" id="newsContent" name="newsContent" rows="5" required></textarea>
                            </div>
                            <div class="form-group landingPageImageContainer">
                                <label for="newImg">Image</label>
                                <input type="file" class="form-control-file" id="newImg" name="newImg" accept="image/*" required>
                            </div>
                            <div class="form-group">
                                <label for="newstatus">Status</label>
                                <select class="form-control" id="newstatus" name="newstatus" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublish</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="addnew">Add News and Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Modal -->
        <div class="modal fade" id="updatenewsupdate" tabindex="-1" role="dialog" aria-labelledby="updatenewsupdateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updatenewsupdateModalLabel">Update About Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="update_news_title">News Title:</label>
                                <input type="text" class="form-control" id="update_news_title" name="update_news_title" required>
                            </div>
                            <div class="form-group">
                                <label for="update_news_content">News Content:</label>
                                <textarea class="form-control" id="update_news_content" rows="4" name="update_news_content" required></textarea>
                            </div>
                            <div class="form-group landingPageImageContainer">
                                <label for="update_news_img">News Image:</label>
                                <input type="file" class="form-control-file" id="update_news_img" name="update_news_img" accept="image/*">
                            </div>
                            <!-- Add the image preview element -->
                            <div class="form-group landingPageImageContainer">
                                <label>Current Image:</label>
                                <img id="update_news_img_preview" src="" alt="About Image Preview" class="img-thumbnail">
                            </div>
                            <div class="form-group">
                                <label for="update_news_status">News Status:</label>
                                <select class="form-control" id="update_news_status" name="update_news_status" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublished</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                            <input type="hidden" id="newsupdate_id" name="newsupdate_id" value="">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="updatenewsupdate">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Retrieve the home data from the database
        $sql = "SELECT * FROM newsupdatetable";
        $result = $con->query($sql);
        ?>
        <div class="tableContainer">
            <table class="table table-sm caption-top">
            <caption>List of News and Update</caption>
                <thead class="custom-thead">
                    <tr>
                        <th>News Title</th>
                        <th>News Content</th>
                        <th>News Image</th>
                        <th>News Status</th>
                        <th class='thAction'>Action</th>
                    </tr>
                </thead>
                <tbody id="file-table-body">
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='newsupdate-" . htmlspecialchars($row["newsupdate_id"]) . "'>";
                            echo "<td data-label='News Title'>" . htmlspecialchars($row["newsupdate_title"]) . "</td>";
                            echo "<td data-label='News Content'>" . htmlspecialchars($row["newsupdate_content"]) . "</td>";
                            echo "<td data-label='News Image'><img class='landingPageImage' src='" . htmlspecialchars($row["newsupdate_img"]) . "' alt='News Update Image'></td>";
                            echo "<td data-label='News Status'>" . ($row["newsupdate_status"] == 1 ? "Publish" : "Unpublished") . "</td>";
                            echo "<td data-label='Action'>
                                    <div class='groupButton'>
                                        <button type='button' class='btn btn-primary update-newsupdatebutton' data-bs-toggle='modal' data-bs-target='#updatenewsupdate'>
                                            <i class='bx bx-wrench'></i>Update
                                        </button>
                                        <button type='button' class='btn btn-danger delete-newsupdatebutton' data-newsupdate='" . htmlspecialchars($row["newsupdate_id"]) . "'>
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
        $('.delete-newsupdatebutton').click(function() {
            var newsId = $(this).data('newsupdate');

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
                        url: 'delete_newsUpdate.php',
                        type: 'POST',
                        data: { newsId: newsId}, // Pass the value of aboutComponent
                        success: function(response) {
                            // If the deletion is successful, remove the corresponding row from the table
                            if (response == 'success') {
                                $('#newsupdate-' + newsId).remove();
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
        var newstitle = row.querySelector("td[data-label='News Title']").textContent;
        var newscontent = row.querySelector("td[data-label='News Content']").textContent;
        var newsImgSrc = row.querySelector("td[data-label='News Image'] img").getAttribute("src");
        var newsStatus = row.querySelector("td[data-label='News Status']").textContent;
        // var newsId = row.id;
        var newsId = row.id.replace("newsupdate-", "");

        console.log(newstitle);
        console.log(newscontent);
        console.log(newsImgSrc);
        console.log(newsStatus);
        console.log(newsId);

        // Populate the form fields with the data
        document.getElementById("update_news_title").value = newstitle;
        document.getElementById("update_news_content").value = newscontent;
        document.getElementById("newsupdate_id").value = newsId;

        // Set the image preview in the modal
        var imagePreview = document.getElementById("update_news_img_preview");
        imagePreview.src = newsImgSrc;

        // Set the news status in the select element
        var newsStatusSelect = document.getElementById("update_news_status");
        if (newsStatus === "Publish") {
            newsStatusSelect.value = "1";
        } else {
            newsStatusSelect.value = "0";
        }
    }

    // Add event listeners to the update buttons
    var updateButtons = document.getElementsByClassName("update-newsupdatebutton");
    for (var i = 0; i < updateButtons.length; i++) {
        updateButtons[i].addEventListener("click", handleUpdateAbout);
    }
});

</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>