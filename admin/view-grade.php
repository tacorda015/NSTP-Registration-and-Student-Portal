<?php
session_start();
ob_start();
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
include_once('adminsidebar.php');
?> 
        <div class="home-main-container">
            <div class="studentList-container">
                <div class="page-title">
                    <div class="titleContainer">
                        <span>Student Grade</span>
                    </div>
                    <form method="get" enctype="multipart/form-data" action="view-grade.php">
                        <div class="search-container">
                            <input id="search" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autofocus>
                            <button class="btn btn-primary" type="submit"><i class='bx bx-search'></i></button>
                        </div>
                    </form>
                </div>
                <div class="buttonsContainer">
                    <div class="buttonHolder">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Downloadmodal">
                            <i class="bx bx-download"></i>Export Grade List
                        </button>
                        <!-- <div class="download-button-container">
                            <a href="studentlist_download.php?" class="download-button btn btn-primary d-flex justify-content-center align-items-center gap-1"><i class="bx bx-download"></i>Student List</a>
                        </div> -->
                    </div>
                </div>
                    
                <!-- FOR DOWNLOAD -->
                <div class="modal fade" id="Downloadmodal" tabindex="1" aria-labelledby="DownloadmodalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 style="text-align: center; padding: 5px 0;">Download List</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="get" action="gradelist_download.php" id="downloadForm">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="choosegroup">Course:</label>
                                        <select class="form-control" id="download" name="download" required>
                                            <option value="" hidden>Choose List</option>
                                            <?php
                                                $course_query = "SELECT course_name FROM coursetable";
                                                $course_result = $con->query($course_query);

                                                while($row = $course_result->fetch_assoc()){
                                                    $course_name = $row['course_name'];
                                                    echo"<option value='$course_name'>$course_name</option>";
                                                }
                                            ?>
                                            <!-- <option value="noGrade">Student No Grade</option> -->
                                        </select>
                                    </div>
                                    <?php
                                        $schoolyear_query = "SELECT * FROM schoolyeartable";
                                        $schoolyear_result = mysqli_query($con, $schoolyear_query);

                                        if ($schoolyear_result) {
                                            echo'<div class="form-group my-2">';
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
                                    <button type="submit" id="downloadbutton" class="btn btn-primary" name="downloadbutton" disabled>Download</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                    <!-- End of modal -->
                <?php
                
                $schoolyear_query = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
                $schoolyear_result = $con->query($schoolyear_query);
                $schoolyear_data = $schoolyear_result->fetch_assoc();
                if($schoolyear_data){
                    $schoolyear_id = $schoolyear_data['schoolyear_id'];
                    $semester_id = $schoolyear_data['semester_id'];

                    // Pagination setup
                    $recordsPerPage = 10;
                    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                        $currentPage = intval($_GET['page']);
                    } else {
                        $currentPage = 1;
                    }

                    $grade_query = "SELECT g.*, u.full_name, u.course, u.student_number, u.user_account_id, group_table.group_name FROM gradetable g 
                                    LEFT JOIN useraccount u ON g.student_id = u.user_account_id 
                                    LEFT JOIN grouptable group_table ON g.group_id = group_table.group_id 
                                    WHERE g.schoolyear_id = $schoolyear_id AND g.semester_id = $semester_id";

                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($con, $_GET['search']);
                        $grade_query .= " AND (u.full_name LIKE '%$search%' OR u.student_number LIKE '%$search%' OR u.course LIKE '%$search%' OR group_table.group_name LIKE '%$search%' OR g.student_grade LIKE '%$search%')";
                    }

                    $grade_query .= " ORDER BY g.grade_id DESC";

                    // Modify the query to include LIMIT and OFFSET clauses for pagination
                    $offset = ($currentPage - 1) * $recordsPerPage;
                    $grade_query .= " LIMIT $recordsPerPage OFFSET $offset";

                    $result = mysqli_query($con, $grade_query);


                    if (mysqli_num_rows($result) > 0) {
                        echo "<div class='tableContainer'>
                                <table class='table table-sm caption-top'>
                                <caption>List of Grade</caption>
                                    <thead class=\"custom-thead\">
                                        <tr>
                                            <th>Full Name</th>
                                            <th>Student Number</th>
                                            <th>Student Grade</th>
                                            <th>Course</th>
                                            <th>Group Name</th>
                                        </tr>
                                    </thead>
                                <tbody id='file-table-body'>";
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                        <td data-label='Full Name'>
                                            <form method='post' action='user_data.php'>
                                                <input type='hidden' name='use_account_id' value='".$row['user_account_id']."'>
                                                <input type='hidden' name='grade_id' value='".$row['grade_id']."'>
                                                <button type='submit' class='clickableCharacter'>
                                                    ".$row['full_name']."
                                                </button>
                                            </form>
                                        </td>
                                        <td data-label='Student Number'>" . $row['student_number'] . "</td>
                                        <td data-label='Student Grade'>{$row['student_grade']}</td>
                                        <td data-label='Student Course'>".($row['course'] !== null && $row['course'] !== '' ? $row['course'] : 'No Course')."</td>
                                        <td data-label='Group Name'>".($row['group_name'] !== null && $row['group_name'] !== '' ? $row['group_name'] : 'No group assigned')."</td>
                                    </tr>";
                                    }
                            echo '</tbody>
                            </table>
                        </div>';

                        // Pagination links using Bootstrap
                        echo "<nav aria-label='Page navigation' class = 'tablePagination'>
                        <ul class='pagination justify-content-center'>";

                        // Determine the total number of pages
                        $totalRecordsQuery = "SELECT COUNT(*) as total FROM gradetable g 
                                    LEFT JOIN useraccount u ON g.student_id = u.user_account_id 
                                    LEFT JOIN grouptable group_table ON g.group_id = group_table.group_id 
                                    WHERE g.schoolyear_id = $schoolyear_id AND g.semester_id = $semester_id";

                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($con, $_GET['search']);
                        $totalRecordsQuery .= " AND (u.full_name LIKE '%$search%' OR u.student_number LIKE '%$search%' OR u.course LIKE '%$search%' OR group_table.group_name LIKE '%$search%' OR g.student_grade LIKE '%$search%')";
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
                    } else {
                        echo '<h2 style="text-align:center;">No Records Found.</h2>';
                    }
                }else{
                    echo '<h2 style="text-align:center;">No School Year Yet.</h2>';
                }
            ?>
        </div>
        </div>
</section>
</div>
<script src="../asset/js/index.js"></script>
<script src="./js/search.js"></script>
<script src="../asset/js/topbar.js"></script>
<script>
    // Get the select element and download button
    const selectElement = document.getElementById('download');
    const downloadButton = document.getElementById('downloadbutton');

    // Add event listener to the select element
    selectElement.addEventListener('change', function() {
        // Enable the download button if a selection is made
        if (selectElement.value !== '') {
        downloadButton.disabled = false;
        } else {
        // Disable the download button if no selection is made
        downloadButton.disabled = true;
        }
    });
    addSearchFunctionality('search', '.search-icon', 'view-grade.php');
</script>
  </body>
</html>