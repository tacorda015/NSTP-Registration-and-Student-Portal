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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addvideosmodal'])) {
    // Retrieve the form data
    $video_title = $_POST['video_title'];
    $video_link = $_POST['video_link'];
    $video_status = $_POST['video_status'];
    
    // Check if the new home page should be published
    if ($video_status == 1) {
        // Set the status of all other home pages to unpublished
        $update_query = "UPDATE videostable SET video_status = 0 ";
        $con->query($update_query);
    }
    
    $insert_query = "INSERT INTO videostable (video_title, video_link, video_status) VALUES ('$video_title', '$video_link', '$video_status')";
    if ($con->query($insert_query) === true) {
        // Success message
        echo "<script>Swal.fire('Success', 'Data saved successfully.', 'success').then(function() {
            window.location.href = 'videos-page.php'; // Replace with your desired page
        });</script>";
    } else {
        // Error message
        echo "<script>Swal.fire('Error', 'Error: " . $con->error . "', 'error');</script>";
    }
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updatevideos'])) {
    // Get the form data
    $video_id = mysqli_real_escape_string($con, $_POST["update_video_id"]);
    $video_title = mysqli_real_escape_string($con, $_POST["update_video_title"]);
    $video_link = mysqli_real_escape_string($con, $_POST["update_video_iframe"]);
    $video_status = mysqli_real_escape_string($con, $_POST["update_video_status"]);


    $allowUpdate = true;

    // Check if the new home page should be published
    if ($video_status == 1) {
        // Set the status of all other home pages to unpublished
        $update_query = "UPDATE videostable SET video_status = 0 ";
        $con->query($update_query);
    }elseif($video_status == 0){
        // Check if there are any other published pages
        $check_published_query = "SELECT COUNT(*) AS published_count FROM videostable WHERE video_status = 1 AND video_id != $video_id";
        $published_result = $con->query($check_published_query);
        $published_count = $published_result->fetch_assoc()['published_count'];

        if ($published_count <= 0) {
            $allowUpdate = false;
            echo "<script>Swal.fire('Error', 'At least one page must be published.', 'error');</script>";
            
        }
    }
    
    if($allowUpdate){
        $update_query = "UPDATE videostable SET video_title = '$video_title', video_link = '$video_link', video_status = '$video_status' WHERE video_id = $video_id";
        if ($con->query($update_query) === true) {
            // Success message
            echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success').then(function() {
                window.location.href = 'videos-page.php'; // Replace with your desired page
            });</script>";
        } else {
            // Error message
            echo "<script>Swal.fire('Error', 'Error: " . $con->error . "', 'error');</script>";
        }
    }
}
?>
<div class="home-main-container">
    <div class="studentList-container">
        <div class="page-title">
            <div class="titleContainer">
                <h2 class="group_id">Videos Page</h2>
            </div>
        </div>
        <div class="buttonsContainer">
            <div class="buttonHolder">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addvideos">
                    <i class='bx bx-plus-circle' ></i>Add Video
                </button>
            </div>
        </div>
    
        <!-- Modal -->
        <div class="modal fade" id="addvideos" tabindex="-1" role="dialog" aria-labelledby="addvideos" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addvideos">Add Video</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                        
                            <div class="form-group">
                                <label for="video_title">Videos Title:</label>
                                <textarea class="form-control" id="video_title" name="video_title" required></textarea>
                            </div>
                            <div class="form-group homeimages-container">
                                <label for="video_link">Embed Code:</label>
                                <textarea class="form-control" id="video_link" name="video_link" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="video_status">Status:</label>
                                <select class="form-control" id="video_status" name="video_status" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublished</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="addvideosmodal">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Modal -->
        <!-- Update Modal -->
        <div class="modal fade" id="updatevideos" tabindex="-1" role="dialog" aria-labelledby="updatevideosModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updatevideosModalLabel">Update Video</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="update-video-form" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="update_video_title">Video Title:</label>
                                <textarea class="form-control" id="update_video_title" rows="4" name="update_video_title" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="update_video_iframe">Embed Code:</label>
                                <textarea class="form-control" id="update_video_iframe" rows="4" name="update_video_iframe" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="update_video_status">Video Status:</label>
                                <select class="form-control" id="update_video_status" name="update_video_status" required>
                                    <option value="1">Publish</option>
                                    <option value="0">Unpublished</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="update_video_id" name="update_video_id" value="">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="updatevideos">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <?php
        // Retrieve the home data from the database
        $sql = "SELECT * FROM videostable";
        $result = $con->query($sql);
        ?>
        <div class="tableContainer">
            <table class="table table-sm caption-top">
            <caption>List of Videos</caption>
                <thead class="custom-thead">
                    <tr>
                        <th>Videos Title</th>
                        <th class='thAction'>Video</th>
                        <th class='thAction'>Video Status</th>
                        <th class='thAction'>Action</th>
                    </tr>
                </thead>
                <tbody id="file-table-body">
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='video-" . htmlspecialchars($row["video_id"]) . "'>";
                            echo "<td data-label='Video Title'>" . htmlspecialchars($row["video_title"]) . "</td>";
                            echo "<td data-label='Video'>";
                            // Assuming $row["video_link"] contains the embed code provided by the user
                            echo "<div class='video-container thAction'>";
                            echo $row["video_link"]; // This is the embed code, e.g., iframe code from YouTube
                            echo "</div>";
                            echo "</td>";
                            echo "<td data-label='Video Status' class='thAction'>" . ($row["video_status"] == 1 ? "Publish" : "Unpublished") . "</td>";
                            echo "<td data-label='Action'>
                                    <div class='groupButton'>
                                        <button type='button' class='btn btn-primary update-video-button' data-bs-toggle='modal' data-bs-target='#updatevideos'>
                                            <i class='bx bx-wrench'></i>Update
                                        </button>
                                        <button type='button' class='btn btn-danger delete-video-button' data-videoid='" . htmlspecialchars($row["video_id"]) . "'>
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
    $('.delete-video-button').click(function() {
        var videoId = $(this).data('videoid');
        console.log(videoId);
        
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
                // console.log($videoId);
                // User confirmed, make an AJAX request to delete_home.php
                $.ajax({
                    url: 'delete_home.php',
                    type: 'POST',
                    data: { videoId: videoId },
                    success: function(response) {
                        // If the deletion is successful, remove the corresponding row from the table
                        if (response == 'success') {
                            $('#video-' + videoId).remove();
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
document.addEventListener("DOMContentLoaded", function () {
    // Function to handle the click event of the update button
    function handleUpdateVideo(event) {
        // Get the row element
        var row = event.target.closest("tr");

        // Get the data from the row
        var videoTitle = row.querySelector("td[data-label='Video Title']").textContent;
        var videoEmbedCode = row.querySelector("td[data-label='Video'] .video-container").innerHTML; // Use .innerHTML instead of .textContent
        var videoStatus = row.querySelector("td[data-label='Video Status']").textContent;
        var videoId = row.id.replace("video-", "");

        // Populate the form fields with the data
        document.getElementById("update_video_title").value = videoTitle;
        document.getElementById("update_video_iframe").value = videoEmbedCode;
        document.getElementById("update_video_id").value = videoId;

        // Set the video status in the select element
        var videoStatusSelect = document.getElementById("update_video_status");
        if (videoStatus === "Publish") {
            videoStatusSelect.value = "1";
        } else {
            videoStatusSelect.value = "0";
        }
    }

    // Add event listeners to the update buttons
    var updateButtons = document.getElementsByClassName("update-video-button");
    for (var i = 0; i < updateButtons.length; i++) {
        updateButtons[i].addEventListener("click", handleUpdateVideo);
    }
});
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>
