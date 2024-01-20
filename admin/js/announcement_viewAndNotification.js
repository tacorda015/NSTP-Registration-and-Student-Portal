$(document).ready(function () {
  // Attach click event handler to the announcement rows
  $('.announcement-row').click(function () {
    var receiver = $(this)
      .closest('tr')
      .find('td[data-label="Receiver"]')
      .text();
    var subject = $(this).closest('tr').find('td[data-label="Subject"]').text();
    var message = $(this).closest('tr').find('td[data-label="Message"]').text();
    var date = $(this).closest('tr').find('td[data-label="Date"]').text();

    // Replace newline characters with HTML line break tags
    message = message.replace(/\n/g, '<br>');

    // Update the modal with the announcement details
    $('#modalReceiver').text(receiver);
    $('#modalSubject').text(subject);
    $('#modalMessage').html(message); // Use .html() to interpret the <br> tags
    $('#modalDate').text(date);

    // Show the modal
    $('#announcementModal').modal('show');
  });
});

$(document).ready(function () {
  // Attach click event handler to the announcement rows
  $('.announcement-row').click(function () {
    var row = $(this).closest('tr');
    var announcementId = $(this).data('announcement-id');

    // Update the view_status via AJAX
    $.ajax({
      url: 'update_view_status.php',
      type: 'POST',
      data: { announcementId: announcementId },
      success: function (response) {
        // Update the UI or perform any additional actions
        console.log(response);
        row.find('td[data-label="Sender"]').css('font-weight', '500');
        row.find('td[data-label="Receiver"]').css('font-weight', '500');
        row.find('td[data-label="Subject"]').css('font-weight', '500');
        row.find('td[data-label="Message"]').css('font-weight', '500');
        row.find('td[data-label="Date"]').css('font-weight', '500');

        // Update the teacher sidebar
        updateAdminSidebar();
      },
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      },
    });
  });

  // Function to update the teacher sidebar
  function updateAdminSidebar() {
    // Make an AJAX request to retrieve the updated sidebar HTML
    $.ajax({
      url: 'get_sidebar.php', // Replace with the actual URL to retrieve the updated sidebar content
      type: 'GET',
      success: function (response) {
        // Update the teacher sidebar with the retrieved HTML
        $('.admin-sidebar').html(response);
      },
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      },
    });
  }
});
