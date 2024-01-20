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
?>
        <div class="home-main-container">
            <div class="studentList-container">
                <div class="page-title">
                    <div class="titleContainer">
                        <span class="group_id">Event Calendar Page</span>
                    </div>
                </div>
                <div class="tableContainersCalendar">
                    <div id="calendar"></div>
                </div>
                <!-- Start popup dialog box -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">Add New Event</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="img-container">
                                    <div class="row">
                                        <div class="col-sm-12" id="existingEventsSection"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">  
                                            <div class="form-group">
                                                <label for="event_name">Event name</label>
                                                <input type="text" name="event_name" id="event_name" class="form-control" placeholder="Enter your event name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">  
                                            <div class="form-group">
                                                <label for="event_start_date">Event start</label>
                                                <input type="date" name="event_start_date" id="event_start_date" class="form-control onlydatepicker" placeholder="Event start date" min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">  
                                            <div class="form-group">
                                                <label for="event_end_date">Event end</label>
                                                <input type="date" name="event_end_date" id="event_end_date" class="form-control" placeholder="Event end date" min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="save_event()">Save Event</button>
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
 $(document).ready(function() {
    // Set the min attribute for the "From" date input to the current date
    $('#event_start_date').attr('min', function(){
        return new Date().toISOString().split("T")[0];
    });

    // Update the min attribute for the "To" date input based on the "From" date
    $('#event_start_date').on('change', function() {
      var fromDate = $(this).val();
      $('#event_end_date').attr('min', fromDate);
    });
});
  
function save_event() {
    var event_name = $('#event_name').val();
    var event_start_date = $('#event_start_date').val();
    var event_end_date = $('#event_end_date').val();
    if (event_name == '' || event_start_date == '' || event_end_date == '') {
        alert('Please enter all required details.');
        return false;
    }
    $.ajax({
        url: 'save_event.php',
        type: 'POST',
        dataType: 'json',
        data: {
            event_name: event_name,
            event_start_date: event_start_date,
            event_end_date: event_end_date,
        },
        success: function (response) {
            $('#exampleModal').modal('hide');
            if (response.status == true) {
                // Success message with SweetAlert2
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.msg,
                    confirmButtonText: 'OK',
                }).then(function () {
                    location.reload(); // Reload the page after user clicks "OK"
                });
            } else {
                // Error message with SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.msg,
                    confirmButtonText: 'OK',
                });
            }
        },
        error: function (xhr, status, error) {
            console.log('ajax error = ' + error);
            console.log(xhr.responseText); // Log the response for debugging purposes

            // Error message with SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while fetching events. Please check the console for more details.',
                confirmButtonText: 'OK',
            });
        },

    });
    return false;
}

$(document).ready(function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        // height: 'auto',
        // aspectRatio: 1.2,
        selectable: true,
        dateClick: function (info) {
            var clickedDate = info.dateStr;

            // Fetch existing events for the clicked date
            displayExistingEvents(clickedDate, function (existingEvents) {
                if (existingEvents.length > 0 || clickedDate >= getCurrentDate()) {
                    // Display the modal if there are events for the clicked date
                    // or if the date is not in the past
                    $('#exampleModal').modal('show');
                }
            });

            // Set the clicked date in the hidden input field
            $('#clickedDate').val(clickedDate);

            // Set the start and end dates in the modal to the clicked date
            $('#event_start_date').val(clickedDate);
            $('#event_end_date').val(clickedDate);

            calendar.unselect(); // Clear the date selection after modal is shown
        },
    });
    calendar.render();

    function displayExistingEvents(date, callback) {
        $.ajax({
            url: 'fetch_events.php',
            dataType: 'json',
            success: function (response) {
                var result = response.data;
                var existingEvents = result.filter(function (event) {
                    return date >= event.start && date < event.end;
                });

                if (existingEvents.length > 0) {
                    var existingEventsHTML = '<h6>Events this date:</h6>';
                    existingEventsHTML += '<table class="table table-bordered">';
                    existingEventsHTML += '<thead><tr><th>Event Name</th><th>Action</th></tr></thead>';
                    existingEventsHTML += '<tbody>';
                    
                    existingEvents.forEach(function (event) {
                        existingEventsHTML += '<tr>';
                        existingEventsHTML += '<td>' + event.title + '</td>';
                        existingEventsHTML += '<td><button class="btn btn-danger" onclick="deleteEvent(' + event.event_id + ')"><i class="bx bx-trash"></i></button></td>';
                        existingEventsHTML += '</tr>';
                    });
                    
                    existingEventsHTML += '</tbody>';
                    existingEventsHTML += '</table>';
                    
                    $('#existingEventsSection').html(existingEventsHTML);
                } else {
                    $('#existingEventsSection').empty();
                }
                if (typeof callback === 'function') {
                    callback(existingEvents);
                }

            },
            error: function (xhr, status, error) {
                console.log('ajax error = ' + error);
                console.log(xhr.responseText); // Log the response for debugging purposes
                alert('An error occurred while fetching events. Please check the console for more details.');
            },
        });
    }
    function getCurrentDate() {
        var today = new Date();
        var year = today.getFullYear();
        var month = String(today.getMonth() + 1).padStart(2, '0');
        var day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }


    function display_events() {
        var events = [];
        $.ajax({
            url: 'fetch_events.php',
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

// Add a delete function
function deleteEvent(eventId) {
    // Show a SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to delete this event. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            // User confirmed the deletion, make the AJAX call to delete the event
            $.ajax({
                url: 'delete_event.php', // Replace with the actual URL to delete_event.php
                type: 'POST',
                dataType: 'json',
                data: {
                    event_id: eventId
                },
                success: function (response) {
                    if (response.status === true) {
                        // Event deleted successfully, you can update the UI as needed
                        // For example, remove the event from the FullCalendar
                        // calendar.getEventById(eventId).remove();
                        console.log(eventId);
                        // Show a success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.msg,
                            confirmButtonText: 'OK',
                        }).then(function () {
                            location.reload(); // Reload the page after user clicks "OK"
                        });
                    } else {
                        // Error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.msg,
                            confirmButtonText: 'OK',
                        });
                    }
                },
                error: function (xhr, status, error) {
                    // Handle AJAX error
                    console.log('AJAX error: ' + error);
                    console.log(xhr.responseText);
                    alert('An error occurred while deleting the event. Please check the console for more details.');
                },
            });
        }
    });
}



</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>