<?php
include('../connection.php');
$con = connection();

// Include your database connection logic here

if (isset($_POST['component_name'])) {
    $component_name = $_POST['component_name'];

    if($component_name == 'ROTC'){
        $groupIDS = 1;
    }else{
        $groupIDS = 2;
    }

    // Query the database to get groups based on the selected component_name
    $group_option_query = "SELECT * FROM grouptable WHERE component_id = '$groupIDS'";
    $group_option_result = mysqli_query($con, $group_option_query);

    if (mysqli_num_rows($group_option_result) > 0) {
        while ($group_row = mysqli_fetch_assoc($group_option_result)) {
            echo "<option value='" . $group_row['group_id'] . "'>" . $group_row['group_name'] . "</option>";
        }
    } else {
        echo "<option value='' selected hidden>No groups available</option>";
    }
}
?>
