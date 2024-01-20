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

$group_id = $useraccount_data['group_id'];
$role_account_id = $useraccount_data['role_account_id'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 
// Calling the side bar
include_once('./studentsidebar.php');
?>
        <div class="home-main-container">
            <div class="studentList-container">
                <div class="page-title">
                    <div class="titleContainer">
                        <span class="group_id">Schedule Calendar</span>
                    </div>
                </div>
                <div class="tableContainers">
                    <div id="calendar"></div>
                </div>
                <!-- Start popup dialog box -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">Event in this Date</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="img-container">
                                    <div class="row">
                                        <div class="col-sm-12" id="existingEventsSection"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End popup dialog box -->
            </div>
        </div>
    </section>
    </div>
    <script>
    $(document).ready(function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            // height: 'auto',
            // aspectRatio: 1.2,
            selectable: true,
            dateClick: function (info) {
                var clickedDate = info.dateStr;

                // Fetch and display existing events for the clicked date or date range
                displayExistingEvents(clickedDate);

                // Open the modal to add a new event
                $('#exampleModal').modal('show');

                // Set the clicked date in the hidden input field
                $('#clickedDate').val(clickedDate);

                // Set the start and end dates in the modal to the clicked date
                $('#event_start_date').val(clickedDate);
                $('#event_end_date').val(clickedDate);

                calendar.unselect(); // Clear the date selection after modal is shown
            },
        });
        calendar.render();

        function displayExistingEvents(date) {
            $.ajax({
                url: 'fetch_schedule.php',
                dataType: 'json',
                success: function (response) {
                    var result = response.data;
                    var existingEvents = result.filter(function (event) {
                        return date >= event.start && date < event.end;
                    });

                    if (existingEvents.length > 0) {
                        var existingEventsHTML = '<h6>Events this date:</h6><ul style="margin:0 0 0 20px">';
                        existingEvents.forEach(function (event) {
                            // Format date and time using the Date object
                            var startDate = new Date(event.start + ' ' + event.timestart);
                            var endDate = new Date(event.end + ' ' + event.timeend);

                            // Format date and time strings
                            var formattedStartDate = startDate.toLocaleString();
                            var formattedEndDate = endDate.toLocaleString();

                            existingEventsHTML += '<li>' + event.title + ' (From ' + formattedStartDate + ' To ' + formattedEndDate + ')' + '</li>';
                        });
                        existingEventsHTML += '</ul>';
                        $('#existingEventsSection').html(existingEventsHTML);
                    }else {
                        $('#existingEventsSection').html('<p>No Activity Schedule for this date.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.log('ajax error = ' + error);
                    console.log(xhr.responseText); // Log the response for debugging purposes
                    alert('An error occurred while fetching events. Please check the console for more details.');
                },
            });
        }

        function display_events() {
            var events = [];
            $.ajax({
                url: 'fetch_schedule.php',
                dataType: 'json',
                success: function (response) {
                    var result = response.data;
                    $.each(result, function (i, item) {
                        var eventColor = generateRandomColor();
                        events.push({
                            id: result[i].event_id,
                            title: result[i].title,
                            start: result[i].start,
                            end: result[i].end,
                            backgroundColor: eventColor,
                            borderColor: eventColor,
                            textColor: '#ffffff',
                            url: result[i].url,
                        });
                    });
                    calendar.addEventSource(events);
                },
                error: function (xhr, status, error) {
                    console.log('ajax error = ' + error);
                    console.log(xhr.responseText); // Log the response for debugging purposes
                    alert('An error occurred while fetching events. Please check the console for more details.');
                },
            });
        }
        function generateRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        display_events();
    });
    </script>
    <script src="../asset/js/index.js"></script>
    <script src="../asset/js/topbar.js"></script>
</body>
</html>