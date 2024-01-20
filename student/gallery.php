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
$user_id = $user_data['user_account_id'];

$check_picture = "SELECT picture FROM useraccount WHERE user_account_id = $user_id";
$check_picture_result = $con->query($check_picture);
$check_picture_data = $check_picture_result->fetch_assoc();

$user_picture = $check_picture_data['picture'];

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
    ob_end_flush();
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
    ob_end_flush();
} 

$qr_code = $user_data['qrimage'];
// Calling the side bar
include_once('./studentsidebar.php');
?>
<style>

</style>

      <div class="home-main-container">
        <div class="studentList-container">
            <div class="buttonsContainer" style="margin-top: 1.5rem;">
              <div class="buttonHolders">
                <a href="./profile.php" class="btn btn-secondary"><i class='bx bx-arrow-back'></i>Profile</a>
                <form class="uploadform" method="post" enctype="multipart/form-data">
                    <input type="file" name="picture" accept="image/*" id="picture-input" onchange="enableImportButton(this)">
                    <button type="button" id="upload-button" class="btn btn-primary" onclick="uploadImage()" disabled>Upload Image</button>
                </form>
              </div>
            </div>
            <div class="form-container">
                <form class="additionalButton" method="post" enctype="multipart/form-data">
                    <?php
                    echo '<div class="img-area">';
                    $gallery = "SELECT * FROM profilepicture WHERE user_account_id = $user_id";
                    $gallery_result = $con->query($gallery);
                    if ($gallery_result->num_rows > 0) {
                        while ($gallery_data = $gallery_result->fetch_assoc()) {
                            echo '<label>
                                    <input class="input-radiobtn" type="radio" name="picture_pathfile" value="'.$gallery_data['picture_pathfile'].'">
                                    <input class="input-radiobtn" type="radio" name="picture_id" value="'.$gallery_data['picture_id'].'">
                                    <img class="selectable" src="'.$gallery_data['picture_pathfile'].'" alt="#">
                                  </label>';
                        }
                    } else {
                        echo 'No Picture Yet Upload Picture';
                    }
                    echo '</div>';
                    ?>
                    <div class="function-btn" style="display: none; width: 100%;">
                        <div class="buttonsContainer">
                          <div class="buttonHolder">
                            <button type="submit" class="btn btn-primary setbtn" name="set_profile_picture"><i class='bx bx-user-pin'></i>Profile</button>
                            <button type="submit" class="btn btn-danger deletebtn" name="delete_picture"><i class='bx bx-trash' ></i>Picture</button>
                          </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
                if (isset($_POST['set_profile_picture'])) {
                  echo"<script>console.log('hello');</script>";
                $picture_pathfile = $_POST['picture_pathfile'];
                if (!empty($picture_pathfile)) {
                    $update_query = "UPDATE useraccount SET picture='$picture_pathfile' WHERE user_account_id=$user_id";
                    if ($con->query($update_query)) {
                      echo '<script>
                            Swal.fire({
                              icon: "success",
                              title: "Profile picture updated successfully!",
                              showConfirmButton: false,
                              timer: 3000,
                              timerProgressBar: true,
                            }).then(function() {
                              window.location.href = "profile.php";
                            });
                            </script>';

                      
                    } else {
                      echo '<script>
                          Swal.fire({
                            icon: "error",
                            title: "Error updating profile picture",
                            text: "Please try again later.",
                            timer: 3000,
                            timerProgressBar: true,
                          });
                          </script>';
                      
                    }
                } else {
                  echo '<script>
                  Swal.fire({
                    icon: "warning",
                    title: "Please select a profile picture",
                  });
                  </script>';
                  
                }
                }
            ?>
    </div>
    </section>
  </div>
  <script>
   document.addEventListener('DOMContentLoaded', function() {
  const imgArea = document.querySelector('.img-area');
  const functionBtn = document.querySelector('.function-btn');
  const selectableImages = document.querySelectorAll('.selectable');

  let selectedImage = null;

  selectableImages.forEach(function(image) {
    image.addEventListener('click', function() {
      const radioButton = image.previousElementSibling;
      radioButton.checked = !radioButton.checked;

      if (radioButton.checked) {
        if (selectedImage === image) {
          image.classList.remove('selected');
          functionBtn.style.display = 'none';
          selectedImage = null;
        } else {
          selectableImages.forEach(function(img) {
            img.classList.remove('selected');
          });

          image.classList.add('selected');
          functionBtn.style.display = 'block';
          selectedImage = image;
        }
      } else {
        image.classList.remove('selected');
        functionBtn.style.display = 'none';
        selectedImage = null;
      }
    });
  });
});



  $(document).ready(function() {
    $('.deletebtn').click(function(event) {
        event.preventDefault();
        var pictureId = $('input[name="picture_id"]:checked').val();
        var picture_pathfile = $('input[name="picture_pathfile"]:checked').val();

        if (pictureId) {
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Do you want to delete this profile picture?',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteProfilePicture(pictureId, picture_pathfile);
                }
            });
        } else {
            console.log('Please select a picture to delete');
        }
    });

    function deleteProfilePicture(pictureId, picturePath) {
        $.ajax({
            type: 'POST',
            url: 'delete_picture.php', // Replace with the actual PHP file that handles the delete action
            data: {
                picture_id: pictureId,
                picture_pathfile: picturePath
            },
            success: function(response) {
                // Handle the success response here
                const data = JSON.parse(response);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Picture Deleted',
                        text: 'Profile picture deleted successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = './gallery.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error deleting profile picture. Please try again later.'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Handle the error response here
                console.error('Error deleting picture:', error);
            }
        });
    }
});

function enableImportButton(input) {
    var uploadButton = document.getElementById("upload-button");
    if (input.files && input.files[0]) {
      var file = input.files[0];
      var fileType = file.type;
      var validImageTypes = ["image/jpeg", "image/png", "image/gif"]; // Add any additional image types you want to allow

      if (validImageTypes.includes(fileType)) {
        uploadButton.disabled = false;
      } else {
        uploadButton.disabled = true;
      }
    } else {
      uploadButton.disabled = true;
    }
  }

function uploadImage() {
    var fileInput = document.getElementById('picture-input');
    var file = fileInput.files[0];

    var formData = new FormData();
    formData.append('picture', file);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload_image.php', true);

    xhr.onload = function () {
      if (xhr.status === 200) {
        // Success
        console.log(xhr.responseText);
        if (xhr.responseText === 'Success') {
          Swal.fire({
            icon: 'success',
            title: 'File Uploaded',
            text: 'The file has been uploaded successfully!',
          }).then(() => {
            window.location.href = './gallery.php';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Upload Failed',
            text: xhr.responseText,
          });
        }
      } else {
        // Error occurred
        console.error(xhr.responseText);
        Swal.fire({
          icon: 'error',
          title: 'Upload Failed',
          text: 'An error occurred during the file upload.',
        });
      }
    };

    xhr.send(formData);
  }
</script>
<script src="../asset/js/index.js"></script>
<script src="../asset/js/topbar.js"></script>
</body>
</html>