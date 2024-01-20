<?php
include('../connection.php');
session_start();
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}
date_default_timezone_set('Asia/Manila');

// get user data from session
$user_data = $_SESSION['user_data'];
$role = "SELECT * FROM roleaccount WHERE role_account_id = {$user_data['role_account_id']}";
$result = $con->query($role);
$role_data = $result->fetch_assoc();
$group_id = $user_data['group_id'];

// echo "<script>console.log('group_id: " . $group_id . "');</script>";

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('./teachersidebar.php');

$user_account_id = $_SESSION['student_data'];
$user_profile_query = "SELECT t.* , g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE user_account_id = $user_account_id";
$user_profile_result = $con->query($user_profile_query);
$user_profile_data = $user_profile_result->fetch_assoc();

$default_image = "uploads/default.jpeg";

// if profile image is empty, set it to default image
if (empty($user_profile_data['picture'])) {
    $user_profile_data['picture'] = '../student/' . $default_image;
}else{
    $user_profile_data['picture'] = '../student/' . $user_profile_data['picture'];
}

$profileimage = $user_profile_data['picture'];
$passwordshow = base64_decode($user_profile_data['password']);
?>
<style>
</style>
        <div class="home-main-container">
            <div class="studentList-container">
                <div class="insideform">
                    <div class="minisidebar">
                        <div class="back-container">
                        <a href="./studentgroup.php"><i class='bx bx-arrow-back'></i></a>
                        </div>
                        <div class="profilepic-container">
                            <?php echo "<img class='profilepic' src='./$profileimage'><br>"; ?>
                        </div>
                        <div class="userData">
                            <span><?php echo $user_profile_data['full_name']; ?></span>
                            <label for="full_name">Student Name</label>
                        </div>
                    </div>
                    <div class="minicontent">
                        <span class="minicontentTitle">Personal Information</span>
                        <input type="hidden" value="<?php echo $user_id ?>" id="userId">
                        <div class="inputContainer">
                            <label for="student_number">Student Number</label>
                            <span><?php echo !empty($user_profile_data['student_number']) ? $user_profile_data['student_number'] : 'No Information'; ?></span>
                        </div>
                        <div class="inputContainer">
                            <label for="year_level">Year Level</label>
                            <span><?php echo !empty($user_profile_data['year_level']) ? $user_profile_data['year_level'] : 'No Information'; ?></span>
                        </div>
                        <div class="inputContainer">
                            <label for="user_section">Section</label>
                            <span><?php echo !empty($user_profile_data['user_section']) ? $user_profile_data['user_section'] : 'No Information'; ?></span>
                        </div>
                        

                        <div class="inputContainer">
                            <label for="course">Course</label>
                            <span><?php echo !empty($user_profile_data['course']) ? $user_profile_data['course'] : 'No Information'; ?></span>
                        </div>
                        <div class="inputContainer">
                            <label for="email_address">Email Address</label>
                            <span><?php echo !empty($user_profile_data['email_address']) ? $user_profile_data['email_address'] : 'No Information'; ?></span>
                        </div>
                        <div class="inputContainer">
                            <label for="contactNumber">Contact Number</label>
                            <div class="span-container">
                                <span id="contactNumberValue"><?php echo !empty($user_profile_data['contactNumber']) ? $user_profile_data['contactNumber'] : 'No Information'; ?></span>                         
                            </div>
                        </div>

                        <div class="inputContainer">
                            <label for="homeaddress">Home Address</label>
                            <div class="span-container">
                                <span id="homeAddressValue"><?php echo !empty($user_profile_data['homeaddress']) ? $user_profile_data['homeaddress'] : 'No Information'; ?></span>
                            </div>
                        </div>
                        <div class="inputContainer">
                            <label for="component_name">Component</label>
                            <span><?php echo !empty($user_profile_data['component_name']) ? $user_profile_data['component_name'] : 'No Information'; ?></span>
                        </div>
                        <div class="inputContainer">
                            <label for="component_id">Group Name</label>
                            <span><?php echo !empty($user_profile_data['group_name']) ? $user_profile_data['group_name'] : 'No Group Assigned'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>
<script src="../assets/js/qrscanner.js"></script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>


</body>
</html>
