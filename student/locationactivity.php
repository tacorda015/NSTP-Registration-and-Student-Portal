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

// Calling the side bar
include_once('./studentsidebar.php')
?>
            <div class="import-container">
              <h1>This is the Student location activity Page.</h1>
              <h1>Under Construction</h1>
            </div>
        </section>
        <script src="../asset/js/index.js"></script>
        <script src="../asset/js/topbar.js"></script>
    </body>
</html>