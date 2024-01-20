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
$role = "SELECT * FROM roleaccount WHERE role_account_id = {$user_data['role_account_id']}";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

$user_account_id = $user_data['user_account_id'];

$retrieve_query = "SELECT * FROM useraccount WHERE user_account_id = $user_account_id";
$retrieve_result = $con->query($retrieve_query);
$retrieve_data = $retrieve_result->fetch_assoc();
$group_id = $retrieve_data['group_id'];

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('./teachersidebar.php');
?>
        <div class="home-main-container">
            <div class="studentList-container">
                <div id="loader-overlay" class="loader-overlay"></div>
                <div id="loader" class="loader">Sending <span></span></div>
                
                <?php
                if(!empty($group_id)){
                
                echo'
                <div class="page-title">
                    <div class="titleContainer">
                        <span class="group_id">Schedule Calendar</span>
                    </div>
                </div>
                <div class="tableContainersCalendar">
                    <div id="calendar"></div>
                </div>';
                ?>
                <!-- Start popup dialog box -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">Add Activity Schedule</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="img-container">
                                    <div class="row">
                                        <div class="col-sm-12" id="existingEventsSection"></div>
                                    </div>
                                    <input type="hidden" name="group_id" id="group_id" value="<?php echo $group_id?>">
                                    <div class="row">
                                        <div class="col-sm-12">  
                                            <div class="form-group">
                                                <label for="message_who">Who:</label>
                                                <input type="text" name="message_who" id="message_who" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">  
                                            <div class="form-group">
                                                <label for="message_what">What:</label>
                                                <input type="text" name="message_what" id="message_what" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="message_what">When:</label>
                                        <!-- From date input -->
                                        <div class="col-sm-6">  
                                            <div class="form-group">
                                                <label for="event_start_date">Date: From</label>
                                                <input type="date" name="event_start_date" id="event_start_date" class="form-control onlydatepicker" placeholder="Event start date" min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>

                                            <!-- To date input -->
                                        <div class="col-sm-6">  
                                            <div class="form-group">
                                                <label for="event_end_date">To</label>
                                                <input type="date" name="event_end_date" id="event_end_date" class="form-control" placeholder="Event end date" min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">  
                                            <div class="form-group">
                                                <label for="event_start_datetime">Time: From</label>
                                                <input type="time" name="event_start_datetime" id="event_start_datetime" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">  
                                            <div class="form-group">
                                                <label for="event_end_datetime">To</label>
                                                <input type="time" name="event_end_datetime" id="event_end_datetime" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">  
                                            <div class="form-group">
                                                <label for="message_where">Where:</label>
                                                <?php
                                                $location_query = "SELECT location_name, location_id FROM activitylocation WHERE group_id = $group_id";
                                                $location_result = $con->query($location_query);
                                                ?>

                                                <select name="message_where" id="message_where" class="form-control">
                                                    <?php if ($location_result->num_rows > 0): ?>
                                                        <?php while ($row = $location_result->fetch_assoc()): ?>
                                                            <option value="<?php echo $row['location_id']; ?>"><?php echo $row['location_name']; ?></option>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <option value="" selected disabled>No activity location</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">  
                                            <div class="form-group">
                                                <label for="message_notes">Notes:</label>
                                                <!-- <input type="text" name="message_notes" id="message_notes" class="form-control"> -->
                                                <textarea name="message_notes" id="message_notes" class="form-control" rows="4"></textarea>
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
                <?php
                }else{
                    echo "<h2 style='text-align:center;' >No Assigned Group yet.</h2>";
                }
                ?>
                <div class="modal fade" id="updateEvent" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Event</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="updateEventName">Event Name:</label>
                                    <input type="hidden" name="updateEventId" id="updateEventId">
                                    <input type="text" name="updateEventName" id="updateEventName" class="form-control" placeholder="Enter event name">
                                </div>
                                <div class="form-group">
                                    <label for="updateStartDate">Start Date:</label>
                                    <input type="date" name="updateStartDate" id="updateStartDate" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="updateEndDate">End Date:</label>
                                    <input type="date" name="updateEndDate" id="updateEndDate" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="updateStartTime">Current Time Schesule:</label>
                                    <div class="input-group ">
                                        <div class="form-floating">
                                            <input type="text" name="updateStartTime" id="updateStartTime" class="form-control" readonly>
                                            <label for="floatingInput">Time Start</label>
                                        </div>
                                        <div class="form-floating">
                                            <input type="text" name="updateEndTime" id="updateEndTime" class="form-control" readonly>
                                            <label for="floatingInput">Time End</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="updateStartTime">Set New Time Schesule:</label>
                                    <div class="input-group ">
                                        <div class="form-floating">
                                            <input type="time" name="newUpdateStartTime" id="newUpdateStartTime" class="form-control">
                                            <label for="floatingInput">Time Start</label>
                                        </div>
                                        <div class="form-floating">
                                            <input type="time" name="newUpdateEndTime" id="newUpdateEndTime" class="form-control">
                                            <label for="floatingInput">Time End</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="updateLocationName">Current Location:</label>
                                    <input type="text" name="updateLocationName" id="updateLocationName" class="form-control">
                                </div> -->
                                <div class="form-group">
                                    <label for="updateNewLocationName">Set New Activity Location:</label>
                                    <select name="updateNewLocationName" id="updateNewLocationName" class="form-control">
                                        <?php 
                                            $locationOptionQuery = "SELECT location_id, location_name FROM activitylocation WHERE group_id = '$group_id'";
                                            $locationOptionResult = $con->query($locationOptionQuery);
                                            if($locationOptionResult->num_rows > 0){
                                                while($row = $locationOptionResult->fetch_assoc()){
                                                    // echo"<option value=" .$row['location_id'].">".$row['location_name']."</option>";
                                                    // if ($row['location_id'] !== $selectedlocationID) {
                                                        echo "<option value=" . $row['location_id'] . ">" . $row['location_name'] . "</option>";
                                                    // }
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="updateEvent()">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
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

  $(document).ready(function() {
    // Set the min attribute for the "From" date input to the current date
    $('#updateStartDate').attr('min', function(){
        return new Date().toISOString().split("T")[0];
    });

    // Update the min attribute for the "To" date input based on the "From" date
    $('#updateStartDate').on('change', function() {
      var fromDate = $(this).val();
      $('#updateEndDate').attr('min', fromDate);
    });
  });
</script>

<script>
  $(document).ready(function() {
    // Disable submit button by default
    $('#exampleModal .modal-footer .btn-primary').prop('disabled', true);

    // Function to enable/disable submit button based on form completion
    function checkFormCompletion() {
      var who = $('#message_who').val();
      var what = $('#message_what').val();
      var startDate = $('#event_start_date').val();
      var endDate = $('#event_end_date').val();
      var startTime = $('#event_start_datetime').val();
      var endTime = $('#event_end_datetime').val();
      var where = $('#message_where').val();
      var notes = $('#message_notes').val();

      // Check if all required fields are filled
      if (who !== '' && what !== '' && startDate !== '' && endDate !== '' && startTime !== '' && endTime !== '' && where !== '' && notes !== '') {
        $('#exampleModal .modal-footer .btn-primary').prop('disabled', false);
      } else {
        $('#exampleModal .modal-footer .btn-primary').prop('disabled', true);
      }
    }

    // Bind the checkFormCompletion function to input events
    $('#message_who, #message_what, #event_start_date, #event_end_date, #event_start_datetime, #event_end_datetime, #message_where, #message_notes').on('input', function() {
      checkFormCompletion();
    });

  });
</script>
<script>
var sendingEmail = false;
// Confirmation message when refreshing or leaving the page
window.addEventListener('beforeunload', function (e) {
    if (sendingEmail) {
        // Show confirmation message only if email sending process has started
        e.preventDefault();
        e.returnValue = '';

        var confirmationMessage = 'Changes you made may not be saved. Are you sure you want to leave this page?';
        (e || window.event).returnValue = confirmationMessage;
        return confirmationMessage;
    }
});

function save_event() {

    // THIS IS FOR LOADDER
    sendingEmail = true;
    document.getElementById("loader-overlay").style.display = "block"; // Show the loader overlay
    document.getElementById("loader").style.display = "block"; // Show the loader

    var group_id = $('#group_id').val();
    var message_who = $('#message_who').val();
    var message_what = $('#message_what').val();
    var event_start_date = $('#event_start_date').val();
    var event_end_date = $('#event_end_date').val();
    var event_start_datetime = $('#event_start_datetime').val();
    var event_end_datetime = $('#event_end_datetime').val();
    var message_where = $('#message_where').val();
    var message_notes = $('#message_notes').val();
    if (message_who == '' || message_what == '' || event_start_date == '' || event_end_date == '' || event_start_datetime == '' || event_end_datetime == '' || message_where == '' || message_notes == '') {
        alert('Please enter all required details.');
        return false;
    }
    $.ajax({
        url: 'save_schedule.php',
        type: 'POST',
        dataType: 'json',
        data: {
            group_id: group_id,
            message_who: message_who,
            message_what: message_what,
            event_start_date: event_start_date,
            event_end_date: event_end_date,
            event_start_datetime: event_start_datetime,
            event_end_datetime: event_end_datetime,
            message_where: message_where,
            message_notes: message_notes,
        },
        success: function (response) {
            $('#exampleModal').modal('hide');
            if (response.status == true) {
                sendingEmail = false;
                document.getElementById("loader-overlay").style.display = "none"; // Hide the loader overlay
                document.getElementById("loader").style.display = "none"; // Hide the loader
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
                sendingEmail = false;
                document.getElementById("loader-overlay").style.display = "none"; // Hide the loader overlay
                document.getElementById("loader").style.display = "none"; // Hide the loader
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

            sendingEmail = false;
            document.getElementById("loader-overlay").style.display = "none"; // Hide the loader overlay
            document.getElementById("loader").style.display = "none"; // Hide the loader
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
        themeSystem: 'bootstrap5',
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
            url: 'fetch_schedule.php',
            dataType: 'json',
            success: function (response) {
                var result = response.data;
                var existingEvents = result.filter(function (event) {
                    return date >= event.start && date < event.end;
                });
                
                if (existingEvents.length > 0) {
                    var existingEventsHTML = '<h6>Schedule this date:</h6>';
                    existingEventsHTML += '<table class="table table-bordered">';
                    existingEventsHTML += '<thead><tr><th>Event Name</th><th class="w-25">Action</th></tr></thead>';
                    existingEventsHTML += '<tbody>';

                    existingEvents.forEach(function (event) {
                        existingEventsHTML += '<tr>';
                        existingEventsHTML += '<td>' + event.title + '(' + event.timeStart + '-' + event.timeEnd + ')' + '</td>';
                        existingEventsHTML += '<td>';
                        existingEventsHTML += '<div class="d-flex gap-2 justify-content-center">';
                        existingEventsHTML += '<button class="btn btn-danger" onclick="deleteEvent(' + event.event_id + ')"><i class="bx bx-trash"></i></button>';
                        existingEventsHTML += '<button class="btn btn-primary" onclick="prepareEditData(' + event.event_id + ', \'' + event.title + '\', \'' + event.start + '\', \'' + event.end + '\', \' ' + event.timeStart + '\', \' ' + event.timeEnd + '\', \' ' + event.locationName + ' \', \' ' + event.locationId + ' \')"><i class="bx bxs-edit"></i></button>';
                        existingEventsHTML += '</div>';
                        existingEventsHTML += '</td>';
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
                // Error message with SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while fetching events. Please check the console for more details.',
                confirmButtonText: 'OK',
            });
                // alert('An serror occurred while fetching events. Please check the console for more details.');
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
                // Error message with SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while fetching events. Please check the console for more details.',
                    confirmButtonText: 'OK',
                });
                // alert('An error occurred while fetching events. Please check the console for more details.');
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
                url: '../admin/delete_event.php', // Replace with the actual URL to delete_event.php
                type: 'POST',
                dataType: 'json',
                data: {
                    event_id: eventId,
                    schedule: true
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
function prepareEditData(eventId, eventTitle, eventStart, eventEnd, timeStart, timeEnd, locationName, locationID) {
    selectedEventId = eventId;
    selectedEventTitle = eventTitle;
    selectedEventStart = eventStart;
    selectedTimeStart = timeStart;
    selectedTimeEnd = timeEnd;
    selectedlocationName = locationName;
    selectedlocationID = locationID;

    // Assuming eventEnd is a string in the format 'YYYY-MM-DD'
    var eventEndDate = new Date(eventEnd); // Convert the string to a Date object
    eventEndDate.setDate(eventEndDate.getDate() - 1); // Subtract 1 day

    // Now, eventEndDate contains the updated date (1 day earlier)
    selectedEventEnd = eventEndDate.toISOString().slice(0, 10); // Convert it back to 'YYYY-MM-DD' format

    // Trigger the edit modal
    editEvent();
}
function editEvent() {
    // Use the global variables to populate the modal fields
    $('#updateEventId').val(selectedEventId);
    $('#updateEventName').val(selectedEventTitle);

    // Delay opening the modal to ensure the values are set in the fields
    setTimeout(function () {
        $('#updateStartDate').val(selectedEventStart);
        $('#updateEndDate').val(selectedEventEnd);
        $('#updateStartTime').val(selectedTimeStart); // Set the value for start time
        $('#updateEndTime').val(selectedTimeEnd); // Set the value for end time
        $('#updateLocationName').val(selectedlocationName); // Set the value for end time

        var x = document.getElementById("updateNewLocationName");
        var option = document.createElement("option");
        option.value = selectedlocationID;
        option.text = selectedlocationName;
        option.selected = true; 
        x.add(option);

        var selectedLocationID = selectedlocationID;
        var options = x.options;

        for (var i = 0; i < options.length; i++) {
            var optionValue = options[i].value;
            if (optionValue == selectedLocationID) {
                options[i].style.display = "none"; // Hide the option
            } else {
                options[i].style.display = "block"; // Show the option
            }
        }

        // Show the modal for editing
        $('#updateEvent').modal('show');
        $('#exampleModal').modal('hide');
    }, 100); // Adjust the delay time as needed
}

function updateEvent() {
    // Retrieve the updated event data from the modal fields
    var eventId = $('#updateEventId').val();
    var eventTitle = $('#updateEventName').val();
    var eventStart = $('#updateStartDate').val();
    var eventEnd = $('#updateEndDate').val();
    var startTime = $('#newUpdateStartTime').val();
    var endTime = $('#newUpdateEndTime').val();
    var newLocationName = $('#updateNewLocationName').val();
    console.log(newLocationName);
    console.log(endTime);

    // Make an AJAX call to update the event using the retrieved data
    $.ajax({
        url: 'update_event.php',
        type: 'POST',
        dataType: 'json',
        data: {
            event_id: eventId,
            event_title: eventTitle,
            event_start: eventStart,
            event_end: eventEnd,
            start_time: startTime,
            end_time: endTime,
            newLocationName: newLocationName
        },
        
        success: function (response) {
            if (response.status === true) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.msg,
                    confirmButtonText: 'OK',
                }).then(function () {
                    location.reload(); // Reload the page after user clicks "OK"
                });
            } else {
                // Handle error case
                alert('Error updating event: ' + response.msg);
            }
        },
        
        error: function (xhr, status, error) {
            // Handle AJAX error
            console.log('AJAX error: ' + error);
            console.log(xhr.responseText);
            alert('An error occurred while updating the event. Please check the console for more details.');
        }
    });
}

</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>