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

if ($role_data['role_name'] == 'Student') {
    header('Location: student.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 

// Calling the sidebar
include_once('adminsidebar.php');

// get user profile data
$user_id = $user_data['user_account_id'];
$user_profile_query = "SELECT * FROM useraccount WHERE user_account_id = $user_id";
$user_profile_result = $con->query($user_profile_query);
$user_profile_data = $user_profile_result->fetch_assoc();

$default_image = "uploads/default.jpeg";

// if profile image is empty, set it to default image
if (empty($user_profile_data['picture'])) {
    $user_profile_data['picture'] = $default_image;
}

$qr_code = $user_data['qrimage'];
$profileimage = $user_profile_data['picture'];
$passwordshow = base64_decode($user_profile_data['password']);

?>
<style>

</style>
  <div class="home-main-container">
      <div class="studentList-container">
          <div class="insideform">
              <div class="minisidebar">
                  <div class="profilepic-container">
                      <a href="gallery.php" class="picture-container">
                          <?php echo "<img class='profilepic' src='./$profileimage'><br>"; ?>
                          <i class="bi bi-camera-fill my-icon"></i>
                      </a>
                  </div>
                  <span><?php echo $user_profile_data['full_name']; ?></span>
              </div>
              <div class="minicontent">
                  <span class="minicontentTitle">Personal Information</span>
                  <input type="hidden" value="<?php echo $user_id ?>" id="userId">
                  <div class="inputContainer">
                      <label for="full_name">Name</label>
                      <span><?php echo !empty($user_profile_data['full_name']) ? $user_profile_data['full_name'] : 'No Information'; ?></span>
                  </div>
                  <div class="inputContainer">
                      <label for="email_address">Email Address</label>
                      <span><?php echo !empty($user_profile_data['email_address']) ? $user_profile_data['email_address'] : 'No Information'; ?></span>
                  </div>
                  <div class="inputContainer">
                      <label for="contactNumber">Contact Number</label>
                      <div class="span-container">
                          <span id="contactNumberValue"><?php echo !empty($user_profile_data['contactNumber']) ? $user_profile_data['contactNumber'] : 'No Information'; ?></span>
                          <div class="icon-container">
                              <i class="fa-solid fa-pen-to-square" id="editContactNumber"></i>
                          </div>
                      </div>
                  </div>

                  <div class="inputContainer">
                      <label for="homeaddress">Home Address</label>
                      <div class="span-container">
                          <span id="homeAddressValue"><?php echo !empty($user_profile_data['homeaddress']) ? $user_profile_data['homeaddress'] : 'No Information'; ?></span>
                          <input type="hidden" name="baranggay" id="baranggay" value="<?php echo !empty($user_profile_data['baranggay']) ? $user_profile_data['baranggay'] : 'No Information'; ?>">
                          <input type="hidden" name="city" id="city" value="<?php echo !empty($user_profile_data['city']) ? $user_profile_data['city'] : 'No Information'; ?>">
                          <input type="hidden" name="province" id="province" value="<?php echo !empty($user_profile_data['province']) ? $user_profile_data['province'] : 'No Information'; ?>">
                          <div class="icon-container">
                              <i class="fa-solid fa-pen-to-square" id="editHomeAddress"></i>
                          </div>
                      </div>
                  </div>
                  <div class="inputContainer">
                      <label for="password">Password</label>
                      <div class="span-container">
                          <span id="passwordValue"><?php echo !empty($passwordshow) ? str_repeat("*", strlen($passwordshow)) : 'No Information'; ?></span>
                          <div class="icon-container">
                              <i class="fa-solid fa-pen-to-square" id="editPassword"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  </section>
</div>
<script>
  function bindEditPassword() {
  var cancelIcon;
  var passwordInput;
  var repeatPasswordInput;

  $('#editPassword').off().click(function() {
    var passwordValue = $('#passwordValue').text();
    var userIdValue = $('#userId').val(); // Retrieve the user ID value

    // Replace the span with password inputs to make it editable
    $('#passwordValue').replaceWith('<div class="passwordInputs"><input type="password" id="currentPassword" placeholder="Current Password"><input type="password" id="passwordInput" placeholder="New Password"><input type="password" id="repeatPasswordInput" placeholder="Confirm Password"></div>');

    // Change the edit icon to a save icon
    $(this).removeClass('fa-pen-to-square').addClass('fa-save');

    // Create a new cancel icon and append it next to the save icon
    if (!cancelIcon) { // Only create the cancel icon if it doesn't exist
      cancelIcon = $('<i class="fas fa-times cancel-icon"></i>');
      $(this).after(cancelIcon);

      // Attach the cancel icon click event handler
      cancelIcon.click(function() {
        // Replace the password inputs with the original password span
        $('.passwordInputs').replaceWith('<span id="passwordValue">' + passwordValue + '</span>');

        // Change the save icon back to an edit icon
        $('#editPassword').removeClass('fa-save').addClass('fa-pen-to-square');

        // Remove the cancel icon
        cancelIcon.remove();
        cancelIcon = null; // Reset the cancel icon variable

        // Rebind the click event for editing the password
        bindEditPassword();
      });
    }

    // Update the click event to handle saving the password
    $(this).off().click(function() {
      var currentPassword = $('#currentPassword').val();
      var password = $('#passwordInput').val();
      var repeatPassword = $('#repeatPasswordInput').val();

      $.ajax({
          url: '../check_password.php',
          method: 'POST',
          data: {
            current_password: currentPassword,
            user_account_id: userIdValue
          },
          success: function(response) {
            if (response === "success") {
              // Input validation check for password length and complexity
              if (password.length < 8 || !/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                      Swal.fire({
                        icon: 'error',
                        title: 'Invalid Password',
                        text: 'Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, and one digit.'
                      });
                      return;
                    }

                    // Input validation check for password match
                    if (password !== repeatPassword) {
                      Swal.fire({
                        icon: 'error',
                        title: 'Passwords Do Not Match',
                        text: 'The entered passwords do not match. Please try again.'
                      });
                      return;
                    }

                    // Encrypt the password using Base64
                    var encryptedPassword = btoa(password); // Use btoa for Base64 encoding
                    console.log(encryptedPassword);
                    // Send the encrypted password to be saved in the database using AJAX
                    $.ajax({
                      url: '../save_password.php',
                      method: 'POST',
                      data: {
                        password: encryptedPassword,
                        user_account_id: userIdValue
                      },
                      success: function(response) {
                        // Replace the password inputs with the updated password span
                        $('.passwordInputs').replaceWith('<span id="passwordValue">********</span>');

                        // Change the save icon back to an edit icon
                        $('#editPassword').removeClass('fa-save').addClass('fa-pen-to-square');

                        // Remove the cancel icon if it exists
                        if (cancelIcon) {
                          cancelIcon.remove();
                          cancelIcon = null;
                        }

                        // Show success notification
                        Swal.fire({
                          icon: 'success',
                          title: 'Password Saved',
                          text: 'The password has been successfully updated.'
                        });

                        // Rebind the click event for editing the password
                        bindEditPassword();
                      },
                      error: function(xhr, status, error) {
                        // Show error notification
                        Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'An error occurred while saving the password. Please try again later.'
                        });
                      }
                    });
            }else {
              // Current password is incorrect, show an error message
              Swal.fire({
                icon: 'error',
                title: 'Incorrect Password',
                text: 'The current password is incorrect. Please try again.'
              });
            }
          },
          error: function(xhr, status, error) {
            // Show error notification
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while checking the current password. Please try again later.'
            });
          }
        });
    });
  });
}

$(document).ready(function() {
  bindEditPassword();
});


  function capitalizeFirstLetter(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }
  function bindEditHomeAddress() {
    var cancelIcon;

    $('#editHomeAddress').off().click(function() {
      var userIdValue = document.getElementById('userId').value;
      var baranggay = document.getElementById('baranggay').value;
      var city = document.getElementById('city').value;
      var province = document.getElementById('province').value;
      var homeAddressValue = $('#homeAddressValue').text();
      console.log(userIdValue);
      console.log(baranggay);
      console.log(city);
      console.log(province);
      console.log(homeAddressValue);

      // Replace the span with an input field to make it editable
      $('#homeAddressValue').replaceWith('<div class="homeaddressInput"><input type="text" id="baranggay" placeholder="Baranggay" value="'+ baranggay +'"><input type="text" id="city" placeholder="City" value = "'+ city +'"><input type="text" id="province" placeholder="Province" value = "'+ province +'"></div>');

      // Change the edit icon to a save icon
      $(this).removeClass('fa-pen-to-square').addClass('fa-save');

      // Create a new cancel icon and append it next to the save icon
      if (!cancelIcon) { // Only create the cancel icon if it doesn't exist
        cancelIcon = $('<i class="fas fa-times cancel-icon"></i>');
        $(this).after(cancelIcon);

        // Attach the cancel icon click event handler
        cancelIcon.click(function() {
          // Replace the input fields with the original home address span
          $('.homeaddressInput').replaceWith('<span id="homeAddressValue">' + homeAddressValue + '</span>');

          // Change the save icon back to an edit icon
          $('#editHomeAddress').removeClass('fa-save').addClass('fa-pen-to-square');

          // Remove the cancel icon
          cancelIcon.remove();
          cancelIcon = null; // Reset the cancel icon variable

          // Rebind the click event for editing home address
          bindEditHomeAddress();
        });
      }

      // Update the click event to handle saving the home address
      $(this).off().click(function() {
        var updatedbaranggay = capitalizeFirstLetter($('#baranggay').val());
        var updatedcity = capitalizeFirstLetter($('#city').val());
        var updatedprovince = capitalizeFirstLetter($('#province').val());

        // Input validation check if empty
        if (updatedprovince.trim() === '') {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Home Address',
            text: 'Please enter your home address.'
          });
          return;
        }

        // Send the updated home address to be saved in the database using AJAX
        $.ajax({
          url: '../save_home_address.php',
          method: 'POST',
          data: {
            baranggay: updatedbaranggay,
            city: updatedcity,
            province: updatedprovince,
            homeAddress: updatedbaranggay + ', ' + updatedcity + ', ' + updatedprovince,
            user_account_id: userIdValue
          },
          success: function(response) {
            // Hide the input boxes and display the updated home address
            $('.homeaddressInput').hide();
            $('#homeAddressValue').text(updatedbaranggay + ', ' + updatedcity + ', ' + updatedprovince);
            $('#homeAddressValue').show();

            // Change the save icon back to an edit icon
            $('#editHomeAddress').removeClass('fa-save').addClass('fa-pen-to-square');

            // Remove the cancel icon if it exists
            if (cancelIcon) {
              cancelIcon.remove();
              cancelIcon = null; // Reset the cancel icon variable
            }

            // Show success notification
            Swal.fire({
              icon: 'success',
              title: 'Home Address Saved',
              text: 'The home address has been successfully updated.'
            }).then(() => {
                        window.location.href = './profile.php';
                    });

            // Rebind the click event for editing home address
            bindEditHomeAddress();
          },
          error: function(xhr, status, error) {
            // Show error notification
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while saving the home address. Please try again later.'
            });
          }
        });
      });
    });
  }

  $(document).ready(function() {
    bindEditHomeAddress();
  });
</script>

<script>
  $(document).ready(function() {
  function bindEditContactNumber() {
    var cancelIcon; // Declare the cancel icon variable outside the click event handler

    $('#editContactNumber').off().click(function() {
      var userIdValue = document.getElementById('userId').value;
      var contactNumberValue = $('#contactNumberValue').text();

      // Replace the span with an input field to make it editable
      if (contactNumberValue === 'No Information') {
        $('#contactNumberValue').replaceWith('<input type="text" id="contactNumberInput" value="">');
      } else {
        $('#contactNumberValue').replaceWith('<input type="text" id="contactNumberInput" value="' + contactNumberValue + '">');
      }
      // Change the edit icon to a save icon
      $(this).removeClass('fa-pen-to-square').addClass('fa-save');

      // Create a new cancel icon and append it next to the save icon
      if (!cancelIcon) { // Only create the cancel icon if it doesn't exist
        cancelIcon = $('<i class="fas fa-times cancel-icon"></i>');
        $(this).after(cancelIcon);

        // Attach the cancel icon click event handler
        cancelIcon.click(function() {
          // Replace the input field with the original contact number span
          $('#contactNumberInput').replaceWith('<span id="contactNumberValue">' + contactNumberValue + '</span>');

          // Change the save icon back to an edit icon
          $('#editContactNumber').removeClass('fa-save').addClass('fa-pen-to-square');

          // Remove the cancel icon
          cancelIcon.remove();
          cancelIcon = null; // Reset the cancel icon variable

          // Rebind the click event for editing contact number
          bindEditContactNumber();
        });
      }

      // Update the click event to handle saving the contact number
      $(this).off().click(function() {
        var updatedContactNumber = $('#contactNumberInput').val();

        // Input validation using regular expression
        var contactNumberRegex = /^(09|\+639)\d{9}$/;
        if (!contactNumberRegex.test(updatedContactNumber)) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Contact Number',
            text: 'Please enter a valid contact number.'
          });
          return;
        }

        // Send the updated contact number to be saved in the database using AJAX
        $.ajax({
          url: '../save_contact_number.php',
          method: 'POST',
          data: {
            contactNumber: updatedContactNumber,
            user_account_id: userIdValue
          },
          success: function(response) {
            // Update the display with the saved contact number
            $('#contactNumberInput').replaceWith('<span id="contactNumberValue">' + updatedContactNumber + '</span>');

            // Change the save icon back to an edit icon
            $('#editContactNumber').removeClass('fa-save').addClass('fa-pen-to-square');

            // Remove the cancel icon if it exists
            if (cancelIcon) {
              cancelIcon.remove();
              cancelIcon = null; // Reset the cancel icon variable
            }

            // Show success notification
            Swal.fire({
              icon: 'success',
              title: 'Contact Number Saved',
              text: 'The contact number has been successfully updated.'
            });

            // Rebind the click event for editing contact number
            bindEditContactNumber();
          },
          error: function(xhr, status, error) {
            // Show error notification
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while saving the contact number. Please try again later.'
            });
          }
        });
      });
    });
  }

  bindEditContactNumber();
});
</script>

<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>
