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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addteam'])) {
    $team_name = mysqli_real_escape_string($con, $_POST['team_name']);
    $team_role = mysqli_real_escape_string($con, $_POST['team_role']);
    $team_content = mysqli_real_escape_string($con, $_POST['team_content']);
    $team_fb = mysqli_real_escape_string($con, $_POST['team_fb']);
    $team_twitter = mysqli_real_escape_string($con, $_POST['team_twitter']);
    $team_instagram = mysqli_real_escape_string($con, $_POST['team_instagram']);
    $team_status = $_POST['team_status'];

    // Upload the image file
    $image_name = $_FILES['team_picture']['name'];
    $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
    $image_tmp = $_FILES['team_picture']['tmp_name'];
    $image_path = '../assets/img/teamimg/' . $image_name;

    // Check if the file with the same name already exists
    $counter = 1;
    while (file_exists($image_path)) {
        $filename_parts = pathinfo($image_name);
        $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
        $image_path = '../assets/img/teamimg/' . $new_filename;
        $counter++;
    }

    $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
    if ($_FILES['team_picture']['size'] > $max_image_size) {
        echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
    } elseif (move_uploaded_file($image_tmp, $image_path)) {
        // Image uploaded successfully, insert data into the database
        $insert_query = "INSERT INTO teamtable (team_name, team_role, team_picture, team_content, team_fb, team_twitter, team_instagram, team_status, onOFF) VALUES ('$team_name', '$team_role', '$image_path', '$team_content', '$team_fb', '$team_twitter', '$team_instagram', $team_status, 1)";
        if ($con->query($insert_query) === true) {
            // Success message
            echo "<script>Swal.fire('Success', 'Data saved successfully.', 'success').then(() => {
                window.location.href = 'team-page.php';
            });</script>";
        } else {
            // Error message
            echo "<script>Swal.fire('Error', 'Error: " . $con->error . "', 'error').then(() => {
                window.location.href = 'team-page.php';</script>";
        }
    } else {
        // Error message if failed to move the uploaded file
        echo "<script>Swal.fire('Error', 'Failed to upload image.', 'error').then(() => {
            window.location.href = 'team-page.php';</script>";
    }
}


if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['updateteampage'])){

    $teamId = mysqli_real_escape_string($con, $_POST['update_team_id']);
    $update_name = mysqli_real_escape_string($con, $_POST['update_team_name']);
    $update_role = mysqli_real_escape_string($con, $_POST['update_team_role']);
    $update_content = mysqli_real_escape_string($con, $_POST['update_team_content']);
    $update_fb = mysqli_real_escape_string($con, $_POST['update_team_fb']);
    $update_twitter = mysqli_real_escape_string($con, $_POST['update_team_twitter']);
    $update_instagram = mysqli_real_escape_string($con, $_POST['update_team_instagram']);
    $update_status = mysqli_real_escape_string($con, $_POST['update_team_status']);


    $allowUpdate = true;
    if($update_status == 0){
        // Check if there are any other published pages
        $check_published_query = "SELECT COUNT(*) AS published_count FROM teamtable WHERE team_status = 1 AND team_id != $teamId";
        $published_result = $con->query($check_published_query);
        $published_count = $published_result->fetch_assoc()['published_count'];

        if ($published_count <= 0) {
            $allowUpdate = false;
            echo "<script>Swal.fire('Error', 'At least one page must be published.', 'error');</script>";
        }
    }
    if($allowUpdate){
        // Check if a new image is uploaded
        if (!empty($_FILES["update_team_picture"]["name"])) {
            $select_query = "SELECT team_picture FROM teamtable WHERE team_id = $teamId";
            $select_result = $con->query($select_query);
            $row = $select_result->fetch_assoc();
            $currentImagePath = $row['team_picture'];
            
            // Upload the new image file
            $image_name = $_FILES['update_team_picture']['name'];
            $image_name = str_replace('/', '', $image_name); // Remove forward slashes from the image name
            $image_tmp = $_FILES['update_team_picture']['tmp_name'];
            $image_path = '../assets/img/teamimg/' . $image_name;
            
            // Check if the file with the same name already exists
            $counter = 1;
            while (file_exists($image_path)) {
                $filename_parts = pathinfo($image_name);
                $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
                $image_path = '../assets/img/teamimg/' . $new_filename;
                $counter++;
            }
            
            $max_image_size = 10 * 1024 * 1024; // 10 MB (adjust the size as needed)
            if ($_FILES['update_team_picture']['size'] > $max_image_size) {
                echo "<script>Swal.fire('Error', 'Image size should not exceed 10MB.', 'error');</script>";
            } elseif (move_uploaded_file($image_tmp, $image_path)) {
                // Image uploaded successfully, update data in the database
                $update_query = "UPDATE teamtable SET team_name = '$update_name', team_picture = '$image_path', team_status = '$update_status', team_role = '$update_role', team_content = '$update_content', team_fb = '$update_fb', team_twitter = '$update_twitter', team_instagram = '$update_instagram' WHERE team_id = $teamId";
                if ($con->query($update_query) === true) {
                    // Success message
                    echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success').then(() => {
                        window.location.href = 'team-page.php';
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
            $update_query = "UPDATE teamtable SET team_name = '$update_name', team_status = '$update_status', team_role = '$update_role', team_content = '$update_content', team_fb = '$update_fb', team_twitter = '$update_twitter', team_instagram = '$update_instagram' WHERE team_id = $teamId";
            if ($con->query($update_query) === true) {
                // Success message
                echo "<script>Swal.fire('Success', 'Data updated successfully.', 'success').then(() => {
                    window.location.href = 'team-page.php';
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
                        <span class="group_id">Team Page</span>
                    </div>
                </div>
            
                <div class="buttonsContainer">
                    <div class="buttonHolder">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeamModal">
                            <i class='bx bx-plus-circle' ></i>Add Team
                        </button>
                        <div class="form-group">
                        <select name="flexSwitchCheckChecked" class="form-control" id="flexSwitchCheckChecked" onchange="myFunction()" style="border: 1px solid blue;">
                            <?php 
                                $retrieve  = "SELECT * FROM teamtable LIMIT 1";
                                $retrieveResult = $con->query($retrieve);
                                $retrieveData = $retrieveResult->fetch_assoc();
                                $onff= $retrieveData['onOff'];
                                if($onff == 1){
                                    echo "<option value='1' selected>Publish the Page</option>";
                                    echo "<option value='0'>Unpublish the Page</option>";
                                }else{
                                    echo "<option value='1'>Publish the Page</option>";
                                    echo "<option value='0' selected>Unpublish the Page</option>";
                                }
                            ?>
                        </select>
                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="addTeamModal" tabindex="-1" role="dialog" aria-labelledby="addTeamModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addTeamModalLabel">Add Team Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="team_name">Name</label>
                                            <input type="text" class="form-control" id="team_name" name="team_name" required>
                                        </div>
                                        <div class="form-group team_picture_container d-flex flex-column gap-1">
                                            <label for="team_picture">Image</label>
                                            <input type="file" class="form-control-file" id="team_picture" name="team_picture" accept="image/*" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="team_role">Role</label>
                                            <select class="form-control" id="team_role" name="team_role" required>
                                                <option value="Developer">Developer</option>
                                                <option value="Designer">Designer</option>
                                                <option value="Researcher">Researcher</option>
                                                <option value="Content Creator">Content Creator</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="team_content">Content</label>
                                            <input class="form-control" id="team_content" name="team_content" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="team_fb">Facebook Page</label>
                                            <input class="form-control" id="team_fb" name="team_fb">
                                        </div>
                                        <div class="form-group">
                                            <label for="team_twitter">Twitter Page</label>
                                            <input class="form-control" id="team_twitter" name="team_twitter">
                                        </div>
                                        <div class="form-group">
                                            <label for="team_instagram">Instagram Page</label>
                                            <input class="form-control" id="team_instagram" name="team_instagram">
                                        </div>
                                        <div class="form-group">
                                            <label for="team_status">Status</label>
                                            <select class="form-control" id="team_status" name="team_status" required>
                                                <option value="1">Publish</option>
                                                <option value="0">Unpublish</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="addteam">Add Team Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Update Modal -->
                <div class="modal fade" id="updateteam" tabindex="-1" role="dialog" aria-labelledby="updateteamModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateteamModalLabel">Update Team Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="update_team_name">Name:</label>
                                        <input type="text" class="form-control" id="update_team_name" name="update_team_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="update_team_role">Role:</label>
                                        <select class="form-control" id="update_team_role" name="update_team_role" required>
                                            <option value="Developer">Developer</option>
                                            <option value="Designer">Designer</option>
                                            <option value="Researcher">Researcher</option>
                                            <option value="Content Creator">Content Creator</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="update_team_content">Content:</label>
                                        <input type="text" class="form-control" id="update_team_content" name="update_team_content" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="update_team_fb">FB page:</label>
                                        <input type="text" class="form-control" id="update_team_fb" name="update_team_fb">
                                    </div>
                                    <div class="form-group">
                                        <label for="update_team_twitter">Twitter Page:</label>
                                        <input type="text" class="form-control" id="update_team_twitter" name="update_team_twitter">
                                    </div>
                                    <div class="form-group">
                                        <label for="update_team_instagram">Instagram Page:</label>
                                        <input type="text" class="form-control" id="update_team_instagram" name="update_team_instagram">
                                    </div>
                                    <div class="form-group d-flex flex-column gap-1">
                                        <label for="update_team_picture">Picture:</label>
                                        <input type="file" class="form-control-file" id="update_team_picture" name="update_team_picture" accept="image/*">
                                    </div>
                                    <!-- Add the image preview element -->
                                    <div class="form-group d-flex flex-column gap-1">
                                        <label>Current Image:</label>
                                        <img id="update_team_picture_preview" src="" alt="Team Image Preview" class="img-thumbnail" style="height: auto; width: 100px;">
                                    </div>
                                    <div class="form-group">
                                        <label for="update_team_status">Status</label>
                                        <select class="form-control" id="update_team_status" name="update_team_status" required>
                                            <option value="1">Publish</option>
                                            <option value="0">Unpublish</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" id="update_team_id" name="update_team_id" value="">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="updateteampage">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                // Retrieve the home data from the database
                $sql = "SELECT * FROM teamtable";
                $result = $con->query($sql);
                ?>
                <div class="tableContainer">
                    <table class="table table-sm caption-top">
                    <caption>List of Team</caption>
                        <thead class="custom-thead">
                            <tr>
                                <th>Name</th>
                                <th class='d-none small_screen'>Role</th>
                                <th class='d-none small_screen'>Content</th>
                                <th class='d-none small_screen'>FB Page</th>
                                <th class='d-none small_screen'>Twitter Page</th>
                                <th class='d-none small_screen'>Instagram Page</th>
                                <th>Picture</th>
                                <th>Status</th>
                                <th class='thAction'>Action</th>
                            </tr>
                        </thead>
                        <tbody id="file-table-body">
                            <?php
                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr id='team-" . htmlspecialchars($row["team_id"]) . "'>";
                                    echo "<td data-label='Name'>" . htmlspecialchars($row["team_name"]) . "</td>";
                                    echo "<td data-label='Role' class='d-none small_screen'>" . htmlspecialchars($row["team_role"]) . "</td>";
                                    echo "<td data-label='Content' class='d-none small_screen'>" . htmlspecialchars($row["team_content"]) . "</td>";
                                    echo "<td data-label='FB Page' class='d-none small_screen'>" . (!empty($row["team_fb"]) ? htmlspecialchars($row["team_fb"]) : "None") . "</td>";
                                    echo "<td data-label='Twitter Page' class='d-none small_screen'>" . (!empty($row["team_twitter"]) ? htmlspecialchars($row["team_twitter"]) : "None") . "</td>";
                                    echo "<td data-label='Instagram Page' class='d-none small_screen'>" . (!empty($row["team_instagram"]) ? htmlspecialchars($row["team_instagram"]) : "None") . "</td>";
                                    echo "<td data-label='Picture'><img class='landingPageImage' src='" . htmlspecialchars($row["team_picture"]) . "' alt='Team Image'></td>";
                                    echo "<td data-label='Status'>" . ($row["team_status"] == 1 ? "Publish" : "Unpublished") . "</td>
                                        <td data-label='Action'>
                                            <div class='groupButton'>
                                                <button type='button' class='btn btn-primary update-team-button' data-bs-toggle='modal' data-bs-target='#updateteam'>
                                                    <i class='bx bx-wrench'></i>Update
                                                </button>
                                                <button type='button' class='btn btn-danger delete-team-button' data-teamid='" . htmlspecialchars($row["team_id"]) . "'>
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
    $('#flexSwitchCheckChecked').on('change', myFunction);
});

function myFunction() {
    console.log('Change event triggered');
    var onOff = document.getElementById("flexSwitchCheckChecked").value;
    console.log('Sending data to server: ' + onOff);

    $.ajax({
        url: 'update_team.php',
        type: 'POST',
        dataType: 'json', // Specify the expected data type
        data: { onOff: onOff },
        success: function(result) {
            if (result.status === true) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: result.msg,
                    confirmButtonText: 'OK',
                }).then(function() {
                    location.reload();
                });
            } else if (result.status === false) {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: result.msg,
                    confirmButtonText: 'OK',
                });
            }
        },
        error: function(xhr, status, error) {
            // Handle AJAX error
            console.log('AJAX error: ' + error);
            console.log(xhr.responseText);
            alert('An error occurred while updating the page. Please check the console for more details.');
        },
    });
}


</script>
<script>
    $(document).ready(function() {
        // Handle delete button click event
        $('.delete-team-button').click(function() {
            var teamId = $(this).data('teamid');

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
                        url: 'delete_team.php',
                        type: 'POST',
                        data: { teamId: teamId},
                        success: function(response) {
                            // If the deletion is successful, remove the corresponding row from the table
                            if (response == 'success') {
                                $('#team-' + teamId).remove();
                                Swal.fire('Success', 'Data deleted successfully.', 'success');
                            } else if (response == 'error') {
                                // Show an error message if deletion was prevented
                                Swal.fire('Error', 'Cannot delete. At least one published data must remain.', 'error').then(() => {window.location.href = 'team-page.php';
                                });
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
    function handleUpdateTeam(event) {
        // Get the row element
        var row = event.target.closest("tr");

        // Get the data from the row
        var teamName = row.querySelector("td[data-label='Name']").textContent;
        var teamRole = row.querySelector("td[data-label='Role']").textContent;
        var teamContent = row.querySelector("td[data-label='Content']").textContent;
        var teamFB = row.querySelector("td[data-label='FB Page']").textContent;
        var teamTwitter = row.querySelector("td[data-label='Twitter Page']").textContent;
        var teamInstagram = row.querySelector("td[data-label='Instagram Page']").textContent;
        var teamImgSrc = row.querySelector("td[data-label='Picture'] img").getAttribute("src");
        var teamStatus = row.querySelector("td[data-label='Status']").textContent;
        var teamId = row.id.replace("team-", "");

        // Populate the form fields with the data
        document.getElementById("update_team_name").value = teamName;
        document.getElementById("update_team_role").value = teamRole;
        document.getElementById("update_team_content").value = teamContent;
        document.getElementById("update_team_fb").value = teamFB;
        document.getElementById("update_team_twitter").value = teamTwitter;
        document.getElementById("update_team_instagram").value = teamInstagram;
        document.getElementById("update_team_id").value = teamId;

        // Set the image preview in the modal
        var imagePreview = document.getElementById("update_team_picture_preview");
        imagePreview.src = teamImgSrc;

        // Set the home status in the select element
        var teamStatusSelect = document.getElementById("update_team_status");
        if (teamStatus === "Publish") {
            teamStatusSelect.value = "1";
        } else {
            teamStatusSelect.value = "0";
        }
    }

    // Add event listeners to the update buttons
    var updateButtons = document.getElementsByClassName("update-team-button");
    for (var i = 0; i < updateButtons.length; i++) {
        updateButtons[i].addEventListener("click", handleUpdateTeam);
    }
});
</script>
<script>
function removeDNoneClass() {
    var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    
    var elements = document.querySelectorAll(".small_screen");
    for (var i = 0; i < elements.length; i++) {
        if (screenWidth < 990) {
            elements[i].classList.remove("d-none");
        } else {
            elements[i].classList.add("d-none");
        }
    }
}

// Call the function when the page loads and when the window is resized
window.addEventListener('load', removeDNoneClass);
window.addEventListener('resize', removeDNoneClass);
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>