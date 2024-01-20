$(document).ready(function () {
  var selectedRecipients = []; // Array to store the selected recipients

  $('#specificRecipients').on('input', function () {
    var input = $(this).val();

    if (input.length > 0) {
      $.ajax({
        type: 'POST',
        url: 'fetch_recipients.php',
        data: { input: input },
        success: function (response) {
          var suggestionsContainer = $('#suggestions');
          suggestionsContainer.html(response);

          var suggestions = suggestionsContainer.find('.suggestion');
          if (suggestions.length > 0) {
            suggestionsContainer.show();
            if (suggestions.length > 5) {
              suggestions.slice(5).hide();
            }

            suggestionsContainer
              .off('click')
              .on('click', '.suggestion', function () {
                var clickedSuggestion = $(this).text().trim();

                // Find the clicked suggestion's data attributes
                var userAccountId = $(this).data('user_account_id');
                var emailAddress = $(this).data('email_address');
                var fullName = $(this).data('full_name');

                // Create an object with the recipient's details
                var recipientData = {
                  user_account_id: userAccountId,
                  email_address: emailAddress,
                  full_name: fullName,
                };

                // Add the recipient's details to the selected recipients array if not already present
                var isAlreadySelected = selectedRecipients.some(function (
                  recipient
                ) {
                  return recipient.user_account_id === userAccountId;
                });

                if (!isAlreadySelected) {
                  selectedRecipients.push(recipientData);
                  updateRecipientsContainer();
                }
                // Clear the input field and hide suggestions
                $('#specificRecipients').val('');
                suggestionsContainer.empty().hide();

                // Convert the selectedRecipients array to a JSON string
                var selectedRecipientsString =
                  JSON.stringify(selectedRecipients);

                // Set the value of the input field
                document.getElementById('hiddenspecificRecipients').value =
                  selectedRecipientsString;
                if (selectedRecipients.length != 0) {
                  document.getElementById('sendButton').disabled = false;
                  validateForm();
                }
              });

            suggestions.each(function () {
              var suggestionText = $(this).text().trim();
              var regex = new RegExp(input, 'i');
              var isNumber = !isNaN(input);

              if (isNumber) {
                $(this).show();
              } else if (regex.test(suggestionText)) {
                $(this).show();
              } else {
                $(this).hide();
              }
            });
          } else {
            suggestionsContainer.hide();
          }
        },
      });
    } else {
      $('#suggestions').empty().hide();
    }
  });

  $('#recipientsContainer').on('click', '.remove-recipient', function () {
    var removedRecipient = $(this).parent().data('recipient');

    // Remove the recipient from the selected recipients
    var index = selectedRecipients.indexOf(removedRecipient);
    if (index !== -1) {
      selectedRecipients.splice(index, 1);
      updateRecipientsContainer();
    }
  });

  function updateRecipientsContainer() {
    var recipientsContainer = $('#recipientsContainer');
    recipientsContainer.empty();

    selectedRecipients.forEach(function (recipient) {
      var recipientElement = $(
        '<div class="recipient">' +
          recipient.full_name +
          '<span class="remove-recipient">&times;</span></div>'
      );
      recipientElement.data('recipient', recipient); // Store the recipient data as a data attribute
      recipientsContainer.append(recipientElement);
    });

    // Add a click event handler to the newly added "x" buttons
    recipientsContainer
      .find('.remove-recipient')
      .off('click')
      .on('click', function () {
        var removedRecipient = $(this).parent().data('recipient');
        var index = selectedRecipients.findIndex(function (recipient) {
          return recipient.user_account_id === removedRecipient.user_account_id;
        });

        if (index !== -1) {
          selectedRecipients.splice(index, 1);
          updateRecipientsContainer();
        }

        // Log the array of selected recipients
        console.log('Selected Recipients:', selectedRecipients);

        // Update the hidden input field value
        var selectedRecipientsString = JSON.stringify(selectedRecipients);
        document.getElementById('hiddenspecificRecipients').value =
          selectedRecipientsString;
        if (selectedRecipients.length == 0) {
          document.getElementById('sendButton').disabled = true;
        } else {
          // document.getElementById("sendButton").disabled = false;
          validateForm();
        }
      });
  }

  updateRecipientsContainer(); // Call the updateRecipientsContainer function initially
});
