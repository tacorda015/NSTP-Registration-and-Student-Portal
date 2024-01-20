<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}
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
include_once('adminsidebar.php');

// Start of Add Group PHP Side
if (isset($_POST['add_group'])) {
    $component_id = mysqli_real_escape_string($con, $_POST['component_id']);
    $group_name = mysqli_real_escape_string($con, $_POST['group_name']);
    $incharge_person = mysqli_real_escape_string($con, $_POST['incharge_person']);
    $number_student = mysqli_real_escape_string($con, $_POST['number_student']);

    $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
    $schoolyear_result = $con->query($schoolyear_query);
    $schoolyear_data = $schoolyear_result->fetch_assoc();
    $schoolyear_id = $schoolyear_data['schoolyear_id'];
    $semester_id = $schoolyear_data['semester_id'];

    if ($component_id == 1) {
        $trainer_query = "SELECT * FROM trainertable WHERE trainer_name = '$incharge_person'";
        $trainer_result = mysqli_query($con, $trainer_query);
        $trainer_row = mysqli_fetch_assoc($trainer_result);
        $incharge_trainer_id = $trainer_row['trainer_id'];
        $trainer_uniquenumber = isset($trainer_row['trainer_uniquenumber']) ? $trainer_row['trainer_uniquenumber'] : '';

        $incharge_teacher_id = null;
        $teacher_uniquenumber = '';
    } else if ($component_id == 2) {
        $teacher_query = "SELECT * FROM teachertable WHERE teacher_name = '$incharge_person'";
        $teacher_result = mysqli_query($con, $teacher_query);
        $teacher_row = mysqli_fetch_assoc($teacher_result);
        $incharge_teacher_id = $teacher_row['teacher_id'];
        $teacher_uniquenumber = isset($teacher_row['teacher_uniquenumber']) ? $teacher_row['teacher_uniquenumber'] : '';

        $incharge_trainer_id = null;
        $trainer_uniquenumber = '';
    } else {
        $incharge_teacher_id = null;
        $incharge_trainer_id = null;
        $teacher_uniquenumber = '';
        $trainer_uniquenumber = '';
    }

    $group_query = "INSERT INTO grouptable (schoolyear_id, semester_id, component_id, group_name, incharge_person, number_student) VALUES ($schoolyear_id, $semester_id, '$component_id', '$group_name', ";

    if ($incharge_teacher_id != null) {
        // if the incharge person is a teacher
        $group_query .= "'$incharge_teacher_id', ";
    } else if ($incharge_trainer_id != null) {
        // if the incharge person is a trainer
        $group_query .= "'$incharge_trainer_id', ";
    }

    $group_query .= "'$number_student')";

    mysqli_query($con, $group_query);

    // Get the ID of the newly inserted group
    $group_id = mysqli_insert_id($con);

    // Update the teachertable or trainertable with the group_id
    if ($incharge_teacher_id != null) {
        // if the incharge person is a teacher
        $teacher_query = "UPDATE teachertable SET group_id = '$group_id' WHERE teacher_id = '$incharge_teacher_id'";
        mysqli_query($con, $teacher_query);
        $useraccount_cross_query = "UPDATE useraccount SET group_id = '$group_id' WHERE student_number = '$teacher_uniquenumber'";
        mysqli_query($con, $useraccount_cross_query);
    } else if ($incharge_trainer_id != null) {
        // if the incharge person is a trainer
        $trainer_query = "UPDATE trainertable SET group_id = '$group_id' WHERE trainer_id = '$incharge_trainer_id'";
        mysqli_query($con, $trainer_query);
        $useraccount_cross_query = "UPDATE useraccount SET group_id = '$group_id' WHERE student_number = '$trainer_uniquenumber'";
        mysqli_query($con, $useraccount_cross_query);
    }

    // Display SweetAlert success message
    echo "
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Group Added',
        text: 'The group has been added successfully!',
        showConfirmButton: false,
        timer: 1500
    }).then(function () {
        window.location.href = 'grouplist.php';
    });
    </script>";

    exit(); // Terminate further execution
}
// End of Add Group PHP Side
// Start of Deleting Group PHP Side
if (isset($_POST['delete_group_id']) && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == '1') {
    // Get the group ID from the form
    $group_id = $_POST['delete_group_id'];

    // Construct the SQL query to delete the record
    $query = "DELETE FROM grouptable WHERE group_id='$group_id'";

    // Execute the query and check for errors
    if (mysqli_query($con, $query)) {
        // Update useraccount table to set the group_id to NULL for students in the deleted group
        $update_query = "UPDATE useraccount SET group_id = NULL WHERE group_id = '$group_id'";
        mysqli_query($con, $update_query);

        // Update trainertable to set the group_id to NULL for trainers in the deleted group
        $trainer_query = "UPDATE trainertable SET group_id = NULL WHERE group_id = '$group_id'";
        mysqli_query($con, $trainer_query);

        // Update teachertable to set the group_id to NULL for teachers in the deleted group
        $teacher_query = "UPDATE teachertable SET group_id = NULL WHERE group_id = '$group_id'";
        mysqli_query($con, $teacher_query);

        // Display success message after redirecting to grouplist.php
        header("Location: grouplist.php?success=1");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($con);
    }
}
// End of Deleting Group PHP Side
// Start of Updating Group PHP Side
if (isset($_POST['update_group'])) {
    // Get the group ID from the form
    $group_id = mysqli_real_escape_string($con, $_POST['group_id']);

    // Sanitize and validate the input values
    $group_name = mysqli_real_escape_string($con, $_POST['group_name']);
    $incharge_person = mysqli_real_escape_string($con, $_POST['incharge_person']);
    $component_id = mysqli_real_escape_string($con, $_POST['component_name']);
    $number_student = mysqli_real_escape_string($con, $_POST['number_student']);

    // Check if the new number of students is lower than the current number of students in the group
    $current_number_query = "SELECT COUNT(*) AS current_number FROM useraccount WHERE group_id='$group_id' AND role_account_id = 2";
    $current_number_result = mysqli_query($con, $current_number_query);
    $current_number_row = mysqli_fetch_assoc($current_number_result);
    $current_number = $current_number_row['current_number'];

    if ($number_student < $current_number) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Number of Students',
                    text: 'Cannot set the number of students lower than the current number.',
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = 'grouplist.php';
                });
              </script>";
        exit;
    }

    // Construct the SQL query to update the record
    $query = "UPDATE grouptable SET group_name='$group_name', incharge_person='$incharge_person', component_id='$component_id', number_student='$number_student' WHERE group_id='$group_id'";

    if($component_id == "1"){
         // Update the group_id field in the trainertable table
        $trainer_query = "UPDATE trainertable SET group_id=NULL WHERE group_id='$group_id'";
        mysqli_query($con, $trainer_query);
    }elseif($component_id == "2"){
        // Update the group_id field in the teachertable table
        $teacher_query = "UPDATE teachertable SET group_id=NULL WHERE group_id='$group_id'";
        mysqli_query($con, $teacher_query);
    }

    // Update the group_id field in useraccount table
    $useraccount_group_query = "UPDATE useraccount SET group_id=NULL WHERE group_id='$group_id' AND role_account_id = 3";
    mysqli_query($con, $useraccount_group_query);


    // Execute the query and check for errors
    if (mysqli_query($con, $query)) {
        if($component_id == 1){
            // Update the group_id field in the trainertable table
            $trainer_query = "UPDATE trainertable SET group_id='$group_id' WHERE trainer_id='$incharge_person'";
            mysqli_query($con, $trainer_query);

            // Update the group_id field in the useraccount table
            $useraccount_query = "UPDATE useraccount SET group_id='$group_id' WHERE student_number IN (SELECT trainer_uniquenumber FROM trainertable WHERE trainer_id = '$incharge_person')";
        }elseif($component_id == 2){
            // Update the group_id field in the teachertable table
            $teacher_query = "UPDATE teachertable SET group_id='$group_id' WHERE teacher_id='$incharge_person'";
            mysqli_query($con, $teacher_query);

            // Update the group_id field in the useraccount table
            $useraccount_query = "UPDATE useraccount SET group_id='$group_id' WHERE student_number IN (SELECT teacher_uniquenumber FROM teachertable WHERE teacher_id = '$incharge_person')";
        }

        mysqli_query($con, $useraccount_query);

        // Display success message after updating the record
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Group Updated',
                    text: 'The group information has been updated successfully.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'grouplist.php';
                });
              </script>";
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}

// End of Updating Group PHP Side
?>
        <div class="home-main-container">
        <div class="studentList-container">
        <!-- Start of Add Group Modal -->
        <div class="modal fade" id="creategroupmodal" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Create Group</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" enctype="multipart/form-data" action="grouplist.php" id="create-group-form">
                        <div class="modal-body" style="z-index: 0;">
                            <div class="form-group">
                                <label for="group_name">Name:</label>
                                <input type="text" class="form-control" id="group_name" name="group_name" required>
                            </div>

                            <div class="form-group">
                                <label for="component_id">Component:</label>
                                <select class="form-control" id="component_id" name="component_id" required>
                                    <option value="" selected disabled hidden>Choose here</option>
                                    <?php
                                        // Retrieve the list of components from the database
                                        $component_query = "SELECT * FROM componenttable";
                                        $component_result = mysqli_query($con, $component_query);
                                        while ($component_row = mysqli_fetch_assoc($component_result)) {
                                            echo "<option value='".$component_row['component_id']."'>".$component_row['component_name']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="incharge_person">Incharge Person:</label>
                                <select class="form-control" id="incharge_person" name="incharge_person" required></select>
                            </div>
                            <div class="form-group">
                                <label for="number_student">Number of Students:</label>
                                <input type="number" class="form-control" id="number_student" name="number_student" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="add_group">Create Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End of Add Group Modal -->
                <div class="page-title">
                    <div class="titleContainer">
                        <span class='group_id'>NSTP Group</span>
                    </div>
                    <form method="get" enctype="multipart/form-data" action="grouplist.php"> 
                        <div class="search-container">
                            <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>
                            <button class="btn btn-primary" type="submit"><i class='bx bx-search'></i></button>
                        </div>
                    </form>
                </div>
                <div class="buttonsContainer">
                    <div class="buttonHolder">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creategroupmodal">
                            <i class='bx bx-plus-circle'></i>Create Group
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#downloadmodal">
                            <i class="bx bx-download"></i>Export Group List
                        </button>
                    </div>
                </div>
                <!-- START OF Download MODAL -->
                <div class="modal fade" id="downloadmodal" tabindex="1" aria-labelledby="downloadmodalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 style="text-align: center; padding: 5px 0;">Download Group List</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" enctype="multipart/form-data" action="grouplist_download.php">
                                <div class="modal-body">
                                    <?php
                                        $schoolyear_query = "SELECT * FROM schoolyeartable";
                                        $schoolyear_result = mysqli_query($con, $schoolyear_query);

                                        if ($schoolyear_result) {
                                            echo'<div class="form-group">';
                                            echo '<label for="schoolyear">Select School Year:</label>';
                                            echo '<select name="schoolyear_id" class="form-control" id="schoolyear">';
                                            
                                            while ($row = mysqli_fetch_assoc($schoolyear_result)) {
                                                $schoolyearID = $row['schoolyear_id'];
                                                $schoolyearStart = $row['schoolyear_start'];
                                                $schoolyearEnd = $row['schoolyear_end'];
                                                $semester_id = $row['semester_id'];
                                            
                                                $semesterText = ($semester_id == 1) ? 'First Semester' : 'Second Semester';
                                            
                                                echo '<option value="' . $schoolyearID . '">' . $schoolyearStart . ' - ' . $schoolyearEnd . ' - ' . $semesterText . '</option>';
                                            }
                                            
                                            echo '</select>';
                                            echo'</div>';
                                            } else {
                                            echo 'Error: ' . mysqli_error($con);
                                            }
                                    ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="downloadgrouplist" id="import_button">Download</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- END OF Download MODAL -->
                <?php
                $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                $schoolyear_result = $con->query($schoolyear_query);
                $schoolyear_data = $schoolyear_result->fetch_assoc();
                if ($schoolyear_data){
                    $schoolyear_id = $schoolyear_data['schoolyear_id'];
                    $semester_id = $schoolyear_data['semester_id'];

                    echo "<script>console.log($schoolyear_id);</script>";
                    echo "<script>console.log($semester_id);</script>";

                    // Pagination setup
                    $recordsPerPage = 10;
                    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                        $currentPage = intval($_GET['page']);
                    } else {
                        $currentPage = 1;
                    }

                    // Modify the query to include LIMIT and OFFSET clauses for pagination
                    $offset = ($currentPage - 1) * $recordsPerPage;

                    $query = "SELECT grouptable.*, componenttable.component_name, teachertable.teacher_name, trainertable.trainer_name
                            FROM grouptable 
                            LEFT JOIN componenttable ON grouptable.component_id = componenttable.component_id 
                            LEFT JOIN trainertable ON grouptable.incharge_person = trainertable.trainer_id
                            LEFT JOIN teachertable ON grouptable.incharge_person = teachertable.teacher_id";

                    // Add the filtering for schoolyear_id and semester_id within the same WHERE clause
                    $query .= " WHERE grouptable.schoolyear_id = $schoolyear_id AND grouptable.semester_id = $semester_id";

                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($con, $_GET['search']);
                        $query .= " AND (grouptable.group_name LIKE '%$search%' OR teachertable.teacher_name LIKE '%$search%' OR trainertable.trainer_name LIKE '%$search%' OR componenttable.component_name LIKE '%$search%' OR grouptable.number_student LIKE '%$search%')";
                    }

                    // Add the ORDER BY clause to sort by group_id
                    $query .= " ORDER BY group_name, component_id  LIMIT $recordsPerPage OFFSET $offset";

                    $result = mysqli_query($con, $query);

                    if (mysqli_num_rows($result) > 0) {
                        echo "<div class='tableContainer'>";
                        echo "<table class='table table-sm caption-top'>";
                        echo "<caption>List of Group</caption>";
                        echo "<thead class=\"custom-thead\"><tr><th>Group Name</th><th>Component</th><th>Incharge Person</th><th>Number of Student</th><th class='thAction'>Action</th></tr></thead>";
                        echo "<tbody id='file-table-body'>";

                        while ($row = mysqli_fetch_assoc($result)) {
                            $getNumberOfStudent = "SELECT COUNT(*) AS numberStudent FROM useraccount WHERE group_id = '{$row['group_id']}' AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id AND role_account_id = 2";
                            $getNumberOfStudentResult = $con->query($getNumberOfStudent);
                            $getNumberOfStudentData = $getNumberOfStudentResult->fetch_assoc();

                            echo    "<tr>";
                            echo    "<td data-label='Group Name'>
                                        <form method='post' action='./groupview.php'>
                                            <input type='hidden' name='group_id' value='".$row['group_id']."'>
                                            <button type='submit' class='clickableCharacter'>
                                                ".$row['group_name']."
                                            </button>
                                        </form>
                                    </td>";
                            echo    "<td data-label='Component Name'>".$row['component_name']."</td>";
                            echo "<td data-label='Incharge Name'>".(
                                ($row['component_name'] == "CWTS" && (empty($row['teacher_name']) || is_null($row['teacher_name']))) ? 
                                    'No Assign' : 
                                    (($row['component_name'] == "ROTC" && (empty($row['trainer_name']) || is_null($row['trainer_name']))) ? 
                                        'No Assign' : 
                                        (($row['component_name'] == "CWTS") ? $row['teacher_name'] : $row['trainer_name']))
                            )."</td>";                            
                            // echo    "<td data-label='Number of Student'>".$row['number_student']."</td>";
                            echo    "<td data-label='Number of Student'>".$getNumberOfStudentData['numberStudent']."</td>";
                            echo "<td data-label='Action'>
                                        <div class='groupButton'>
                                            <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#groupupdatemodal" . $row['group_id'] . "'>
                                            <i class='bx bx-wrench'></i>Update</button>
                                            <form method='post' id='deleteForm" . $row['group_id'] . "'>
                                                <input type='hidden' name='delete_group_id' value='" . $row['group_id'] . "'>
                                                <input type='hidden' name='confirm_delete' id='confirm_delete_" . $row['group_id'] . "' value='0'>
                                                <button type='button' class='btn btn-danger' onclick='confirmDelete(\"" . $row['group_id'] . "\")'>
                                                <i class='bx bx-trash'></i>Delete</button>
                                            </form>
                                        </div>
                                    </td>";
                            // echo    "<td data-label='Component Name'>".$row['schoolyear_id']."</td>";
                            // echo    "<td data-label='Component Name'>".$row['semester_id']."</td>";
                            echo "</tr>";
                            echo "<div class='modal fade' id='groupupdatemodal".$row['group_id']."' tabindex='-1' aria-labelledby='updatemodalLabel' aria-hidden='true'>
                                        <div class='modal-dialog'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='updatemodalLabel'>Update Group Information</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>
                                                <form method='post' enctype='multipart/form-data' action='grouplist.php'>
                                                    <div class='modal-body'>
                                                        <input type='hidden' name='group_id' value='".$row['group_id']."'>
                                                        <div class='form-group'>
                                                            <label for='group_name'>Group Name:</label>
                                                            <input type='text' class='form-control' id='group_name' name='group_name' value='".$row['group_name']."' required>
                                                        </div>
                                                        <div class='form-group'>
                                                            <label for='component_name'>Component Name:</label>
                                                            <input type='text' class='form-control' id='component_name' value='".$row['component_name']."' disabled>
                                                            <input type='hidden' name='component_name' value='".$row['component_id']."'>
                                                        </div>";

                                                        if($row['component_name'] == 'ROTC'){
                                                            echo "<div class='form-group'>
                                                                        <label for='incharge_person' class='form-label'>Incharge Person</label>
                                                                        <select class='form-control' id='incharge_person' name='incharge_person' required>";
                                                                        
                                                            // Retrieve the list of trainers
                                                            $trainer_query = "SELECT trainer_id, trainer_name FROM trainertable WHERE group_id IS NULL OR group_id = ''";
                                                            $trainer_result = mysqli_query($con, $trainer_query);
                                                            
                                                            // Retrieve the incharge_person assigned to the group
                                                            $incharge_person_query = "SELECT trainer_id, trainer_name FROM trainertable WHERE group_id = '{$row['group_id']}'";
                                                            $incharge_person_result = mysqli_query($con, $incharge_person_query);
                                                            $incharge_person_row = mysqli_fetch_assoc($incharge_person_result);
                                                            
                                                            // Add the incharge_person assigned to the group to the options
                                                            $selected = ($row['incharge_person'] == $incharge_person_row['trainer_id']) ? 'selected' : '';
                                                            echo "<option value='".$incharge_person_row['trainer_id']."' ".$selected." hidden>".$incharge_person_row['trainer_name']."</option>";
                                                            
                                                            // Loop through each trainer and add an option to the select element
                                                            while ($trainer_row = mysqli_fetch_assoc($trainer_result)) {
                                                                $selected = ($row['incharge_person'] == $trainer_row['trainer_id']) ? 'selected' : '';
                                                                echo "<option value='".$trainer_row['trainer_id']."' ".$selected.">".$trainer_row['trainer_name']."</option>";
                                                            }
                                                            
                                                            echo "</select></div>";
                                                        }
                                                        else if($row['component_name'] == 'CWTS'){
                                                            echo "<div class='form-group'>
                                                                        <label for='incharge_person' class='form-label'>Incharge Person</label>
                                                                        <select class='form-control' id='incharge_person' name='incharge_person' required>";
                                                                        
                                                            // Retrieve the list of teachers
                                                            $teacher_query = "SELECT teacher_id, teacher_name FROM teachertable WHERE group_id IS NULL OR group_id = ''";
                                                            $teacher_result = mysqli_query($con, $teacher_query);
                                                            
                                                            // Retrieve the incharge_person assigned to the group
                                                            $incharge_person_query = "SELECT teacher_id, teacher_name FROM teachertable WHERE group_id = '{$row['group_id']}'";
                                                            $incharge_person_result = mysqli_query($con, $incharge_person_query);
                                                            $incharge_person_row = mysqli_fetch_assoc($incharge_person_result);
                                                            
                                                            // Add the incharge_person assigned to the group to the options
                                                            $selected = ($row['incharge_person'] == $incharge_person_row['teacher_id']) ? 'selected' : '';
                                                            echo "<option value='".$incharge_person_row['teacher_id']."' ".$selected.">".$incharge_person_row['teacher_name']."</option>";
                                                            
                                                            // Loop through each teacher and add an option to the select element
                                                            while ($teacher_row = mysqli_fetch_assoc($teacher_result)) {
                                                                $selected = ($row['incharge_person'] == $teacher_row['teacher_id']) ? 'selected' : '';
                                                                echo "<option value='".$teacher_row['teacher_id']."' ".$selected.">".$teacher_row['teacher_name']."</option>";
                                                            }
                                                            echo "</select></div>";
                                                            }
                                                            
                                                    echo"    <div class='form-group'>
                                                            <label for='number_student'>Number of Students:</label>
                                                            <input type='number' class='form-control' id='number_student' name='number_student' value='".$row['number_student']."' required>
                                                        </div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                                        <button type='submit' class='btn btn-primary' name='update_group'>Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>";
                        }
                        echo "</tbody></table></div>";
                        
                        // Pagination links using Bootstrap
                        echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                        <ul class='pagination justify-content-center'>";

                        // Determine the total number of pages
                        $totalRecordsQuery = "SELECT COUNT(*) as total FROM grouptable 
                        LEFT JOIN componenttable ON grouptable.component_id = componenttable.component_id 
                        LEFT JOIN trainertable ON grouptable.incharge_person = trainertable.trainer_id
                        LEFT JOIN teachertable ON grouptable.incharge_person = teachertable.teacher_id";

                        $totalRecordsQuery .= " WHERE grouptable.schoolyear_id = $schoolyear_id AND grouptable.semester_id = $semester_id";

                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = mysqli_real_escape_string($con, $_GET['search']);
                            $totalRecordsQuery .= " AND grouptable.group_name LIKE '%$search%' OR teachertable.teacher_name LIKE '%$search%' OR trainertable.trainer_name LIKE '%$search%' OR componenttable.component_name LIKE '%$search%' OR grouptable.number_student LIKE '%$search%'";
                        }

                        $totalRecordsResult = mysqli_query($con, $totalRecordsQuery);
                        $totalRecordsRow = mysqli_fetch_assoc($totalRecordsResult);
                        $totalRecords = $totalRecordsRow['total'];

                        $totalPages = ceil($totalRecords / $recordsPerPage);

                        // Pagination links - Previous
                        $prevPage = $currentPage - 1;
                        echo "<li class='page-item " . ($currentPage == 1 ? 'disabled' : '') . "'>
                                <a class='page-link' href='?page=$prevPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>&laquo; Previous</a>
                            </li>";

                        for ($i = max(1, $currentPage - 2); $i <= min($currentPage + 2, $totalPages); $i++) {
                        echo "<li class='page-item " . ($i == $currentPage ? 'active' : '') . "'>
                            <a class='page-link' href='?page=$i" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>$i</a>
                        </li>";
                        }
                        // Pagination links - Next
                        $nextPage = $currentPage + 1;
                        echo "<li class='page-item " . ($currentPage == $totalPages ? 'disabled' : '') . "'>
                                <a class='page-link' href='?page=$nextPage" . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '') . "'>Next &raquo;</a>
                            </li>
                        </ul>
                        </nav>";
                    }else{
                        echo '<h2 style="text-align:center;">No Record of Group Found.</h2>';
                    }
                }else{
                    echo"<h1 style='text-align: center; margin-top: 10%'>No School Year Yet</h1>";
                }
                ?>
            </div>
        </div>
        </section>
    </div>
    <script src="../asset/js/index.js"></script>
    <script src="../asset/js/topbar.js"></script>
    <script src="./js/grouplist_component.js"></script>
    <script src="./js/search.js"></script>
    <script>
        addSearchFunctionality('search', '.search-icon', 'grouplist.php');
    </script>
    <script>
    function confirmDelete(groupId) {
        Swal.fire({
            icon: 'warning',
            title: 'Delete Group',
            text: 'Are you sure you want to delete this group?',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set the value of confirm_delete input to 1 and submit the form
                document.getElementById('confirm_delete_' + groupId).value = '1';
                document.getElementById('deleteForm' + groupId).submit();
            }
        });
    }
    // Function to display success message after deletion
    function showDeleteSuccess() {
        Swal.fire({
            icon: 'success',
            title: 'Group Deleted',
            text: 'The group has been successfully deleted.',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }
    // Function to delay the success message
    function delaySuccessMessage() {
        setTimeout(showDeleteSuccess, 400); // Delay of 400 milliseconds (1 second)
    }

    // Check if the success parameter is present in the URL
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
    // Call the delaySuccessMessage function after the page loads
    window.addEventListener('load', delaySuccessMessage);
    <?php endif; ?>
</script>
</body>
</html>

