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


if (isset($_POST['use_account_id'])) {
  $use_account_id = $_POST['use_account_id'];
  echo"<script>console.log($use_account_id);</script>";
  $_SESSION['student_data'] = $use_account_id;
}
if(isset($_POST['grade_id'])){
$grade_id = $_POST['grade_id'];
echo"<script>console.log($grade_id);</script>";
}
$student_user_account_id = $_SESSION['student_data'];
$user_profile_query = "SELECT t.* , g.group_name, g.number_student FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE user_account_id = $student_user_account_id";
$user_profile_result = $con->query($user_profile_query);
$user_profile_data = $user_profile_result->fetch_assoc();
$group_id = $user_profile_data['group_id'];
$user_role = $user_profile_data['role_account_id'];
$component_name = $user_profile_data['component_name'];

$student_count_query = "SELECT COUNT(*) as count FROM useraccount WHERE group_id = '$group_id' AND role_account_id = 2";
$student_count_result = $con->query($student_count_query);
$student_count_data = mysqli_fetch_assoc($student_count_result);

if($group_id){
    $incharge_query = "SELECT * FROM useraccount WHERE group_id = $group_id AND role_account_id = 3";
    $incharge_result = $con->query($incharge_query);
    $incharge_data = $incharge_result->fetch_assoc();
    
    if(!$incharge_data){
        $incharge_data ['full_name'] = 'No Person Incharge Assigned';
    }
    
}else{
    $incharge_data ['full_name'] = 'No Person Incharge Assigned';
}


$default_image = "uploads/default.jpeg";

if($user_role == 2){
    // if profile image is empty, set it to default image
    if (empty($user_profile_data['picture'])) {
        $user_profile_data['picture'] = '../student/' . $default_image;
    }else{
        $user_profile_data['picture'] = '../student/' . $user_profile_data['picture'];
    }
}elseif($user_role == 3){
    // if profile image is empty, set it to default image
    if (empty($user_profile_data['picture'])) {
        $user_profile_data['picture'] = '../teacher/' . $default_image;
    }else{
        $user_profile_data['picture'] = '../teacher/' . $user_profile_data['picture'];
    }
}

$profileimage = $user_profile_data['picture'];
$passwordshow = base64_decode($user_profile_data['password']);
?>
        <div class="home-main-container">
        <div class="studentList-container">
                <div class="insideform">
                    <div class="minisidebar">
                        <div class="back-container">
                        <?php
                            if(isset($_POST['groupview'])){
                                echo "<a href=\"./groupview.php\"><i class='bx bx-arrow-back'></i></a>";
                            }elseif(isset($_POST['grade_id'])){
                                echo "<a href=\"./view-grade.php\"><i class='bx bx-arrow-back'></i></a>";
                            }else{
                                if ($user_role == 2) {
                                    echo "<a href=\"./studentlist.php\"><i class='bx bx-arrow-back'></i></a>";
                                } elseif ($user_role == 3 && $component_name == 'ROTC') {
                                    echo "<a href=\"./trainerlist.php\"><i class='bx bx-arrow-back'></i></a>";
                                } elseif ($user_role == 3 && $component_name == 'CWTS') {
                                    echo "<a href=\"./teacherlist.php\"><i class='bx bx-arrow-back'></i></a>";
                                }
                            }
                        ?>
                        </div>
                        <div class="profilepic-container">
                            <?php echo "<img class='profilepic' src='./$profileimage'><br>"; ?>
                        </div>
                        <?php
                            if ($user_role == 2) {
                                echo "<div class=\"userData\"><span>" . $user_profile_data['full_name'] . "</span>
                                <label for=\"full_name\">Student Name</label></div>";
                            } elseif ($user_role == 3 && $component_name == 'ROTC') {
                                echo "<div class=\"userData\"><span>" . $user_profile_data['full_name'] . "</span>
                                <label for=\"Training Staff Name\">Trainer Name</label></div>";
                            } elseif ($user_role == 3 && $component_name == 'CWTS') {
                                echo "<div class=\"userData\"><span>" . $user_profile_data['full_name'] . "</span>
                                <label for=\"full_name\">Coordinator Name</label></div>";
                            }
                        ?>
                    </div>
                    <div class="minicontent">
                        <span class="minicontentTitle">Personal Information</span>
                        <input type="hidden" value="<?php echo $user_id ?>" id="userId">
                        
                        <?php
                        if ($user_role == 2) {
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"serialNumber\">Serial Number</label>
                            <span>" . ($user_profile_data['serialNumber'] ? $user_profile_data['serialNumber'] : 'No Information') . "</span>
                            </div>";
                        }

                        if ($user_role == 2) {
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"student_number\">Student Number</label>
                            <span>" . ($user_profile_data['student_number'] ? $user_profile_data['student_number'] : 'No Information') . "</span>
                            </div>";
                        }
                        
                        if ($user_role == 2) {
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"year_level\">Year Level</label>
                            <span>" . ($user_profile_data['year_level'] ? $user_profile_data['year_level'] : 'No Information') . "</span>
                            </div>";
                        }
                        
                        if ($user_role == 2) {
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"student_section\">Section</label>
                            <span>" . ($user_profile_data['student_section'] ? $user_profile_data['student_section'] : 'No Information') . "</span>
                            </div>";
                        }
                        
                        if ($user_role == 2 || ($user_role == 3 && $component_name == 'ROTC')) {
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"course\">Course</label>
                            <span>" . ($user_profile_data['course'] ? $user_profile_data['course'] : 'No Information') . "</span>
                            </div>";
                        }elseif ($user_role == 3 && $component_name == 'CWTS'){
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"course\">Department</label>
                            <span>" . ($user_profile_data['course'] ? $user_profile_data['course'] : 'No Information') . "</span>
                            </div>";
                        }
                        ?>
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
                        <?php
                        if ($user_role == 2) {
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"component_id\">Person Incharge</label>
                            <span>" . ($incharge_data['full_name'] ? $incharge_data['full_name'] : 'No Person Incharge Assigned') . "</span>
                            </div>";
                        }elseif ($user_role == 3){
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"student_capacity\">Student Capacity</label>
                            <span>" . ($user_profile_data['number_student'] ? $user_profile_data['number_student'] : 'No Group Assigned') . "</span>
                            </div>";
                            echo "
                            <div class=\"inputContainer\">
                            <label for=\"number_student\">Number of Student</label>
                            <span>" . ($student_count_data['count'] ? $student_count_data['count'] : 'No Student Assigned') . "</span>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
    <script src="../asset/js/index.js"></script>
    <script src="./js/search.js"></script>
    <script src="../asset/js/topbar.js"></script>
    <script>addSearchFunctionality('search', '.search-icon', 'groupview.php');</script>
  </body>
</html>
