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
$role = "SELECT * FROM roleaccount WHERE role_account_id = {$user_data['role_account_id']}";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
}

// get user profile data
$user_id = $user_data['user_account_id'];
$user_profile_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE user_account_id = {$user_id}";
$user_profile_result = $con->query($user_profile_query);
$user_profile_data = $user_profile_result->fetch_assoc();

// Calling the side bar
include_once('./studentsidebar.php');

$default_image = "uploads/default.jpeg";

// if profile image is empty, set it to default image
if (empty($user_profile_data['picture'])) {
    $user_profile_data['picture'] = $default_image;
}

$qr_code = $user_data['qrimage'];
$profileimage = $user_profile_data['picture'];
$passwordshow = base64_decode($user_profile_data['password']);

if(isset($_POST['updateprofile'])){
    // Get updated profile data from POST request
    $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, base64_encode($_POST['password']));
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $picture = $_FILES['picture'];

    // Check if a new profile picture has been uploaded
    if ($picture['error'] == UPLOAD_ERR_OK) {

        $uploads_dir = 'uploads/user_' . $user_id . '/';

        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }
        $upload_file = $uploads_dir . basename($picture['name']);
        move_uploaded_file($picture['tmp_name'], $upload_file);
        // Update the user's profile picture in the database
        $picture_url = $upload_file;
        $query = "UPDATE useraccount SET picture = '$picture_url' WHERE user_account_id = $user_id";
        $profile_query = "INSERT INTO profilepicture (user_account_id, picture_pathfile) VALUES ('$user_id', '$picture_url')";

        mysqli_query($con, $profile_query);
        mysqli_query($con, $query);
    }
    // Construct and execute SQL query to update user profile data
    $sql = "UPDATE useraccount SET full_name='$full_name', username='$username', password='$password', email_address='$email' WHERE user_account_id=$user_id";
    $result = mysqli_query($con, $sql);

    // Check if the update was successful
    if($result) {
        // Redirect the user to the updated profile page
        header("Location: profile.php");
    } else {
        // Display an error message
        echo "Error updating profile: " . mysqli_error($con);
    }
}
?>
<style>
    label{display: block;}
</style>
        <div class="profile-container">
            <form method="POST" action="profile.php" enctype="multipart/form-data"> <!-- add enctype attribute here -->

                <label for="full_name">Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo $user_profile_data['full_name']; ?>" required>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $user_profile_data['username']; ?>" required>

                <label for="username">Password:</label>
                <input type="text" id="password" name="password" value="<?php echo $passwordshow; ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user_profile_data['email_address']; ?>" required>

                <label for="component_name">Component Name:</label>
                <input type="text" id="component_name" name="component_name" value="<?php echo $user_profile_data['component_name'] ? $user_profile_data['component_name'] : 'No component yet'; ?>" required readonly>

                <label for="group_name">Group Name:</label>
                <input type="text" id="group_name" name="group_name" value="<?php echo ($user_profile_data['group_name'] != '' ? $user_profile_data['group_name'] : 'No group yet'); ?>" required readonly>

                <label for="student_number">Student Number:</label>
                <input type="text" id="student_number" name="student_number" value="<?php echo $user_profile_data['student_number']; ?>" required readonly>

                <!-- Add a new input field for file uploads -->
                <label for="picture">Upload New Profile Picture:</label>
                <input type="file" id="picture" name="picture" accept="image/*">
                <a href="./gallery.php">Gallery</a>
                <button type="submit" name="updateprofile">Save Changes</button>
            </form>
            <hr>
            <p>Unique Qr code: <?php echo "<img src='../$qr_code'><br>"; ?></p>
            <p>Profile Image: <?php echo "<img style= 'height: auto; width: 150px;'src='./$profileimage'><br>"; ?></p>

        </div>
    </section>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>
