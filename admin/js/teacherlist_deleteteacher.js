function disableteacher(accountId) {
  Swal.fire({
    icon: 'warning',
    title: 'Confirmation',
    text: 'Are you sure you want to disable this teacher?',
    showCancelButton: true,
    confirmButtonText: 'Disable',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      confirmDisable(accountId);
    }
  });
}

function confirmDisable(accountId) {
  // Send an AJAX request to Disable the teacher record
  $.ajax({
    type: 'POST',
    url: 'disable_teacher.php', // Replace with the actual PHP file to handle the deletion
    data: { user_account_id: accountId },
    success: function (response) {
      if (response === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Disable Successful',
          text: 'The teacher record has been disable.',
          showConfirmButton: false,
          timer: 3000,
        }).then(function () {
          window.location.href = 'teacherlist.php';
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Disable Failed',
          text:
            'An error occurred while disabling the teacher record: ' + response,
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        icon: 'error',
        title: 'Disable Failed',
        text: 'An error occurred while disabling the teacher record: ' + error,
      });
    },
  });
}
// function confirmDelete(teacherId) {
//   Swal.fire({
//     icon: 'warning',
//     title: 'Confirmation',
//     text: 'Are you sure you want to delete this teacher?',
//     showCancelButton: true,
//     confirmButtonText: 'Delete',
//     cancelButtonText: 'Cancel'
//   }).then((result) => {
//     if (result.isConfirmed) {
//       deleteTeacher(teacherId);
//     }
//   });
// }

// function deleteTeacher(teacherId) {
//   // Send an AJAX request to delete the teacher record
//   $.ajax({
//     type: 'POST',
//     url: 'delete_teacher.php', // Replace with the actual PHP file to handle the deletion
//     data: { teacher_id: teacherId },
//     success: function(response) {
//       if (response === 'success') {
//         Swal.fire({
//           icon: 'success',
//           title: 'Delete Successful',
//           text: 'The teacher record has been deleted.',
//           showConfirmButton: false,
//           timer: 2000
//         }).then(function() {
//           window.location.href = 'teacherlist.php';
//         });
//       } else {
//         Swal.fire({
//           icon: 'error',
//           title: 'Delete Failed',
//           text: 'An error occurred while deleting the teacher record: ' + response
//         });
//       }
//     },
//     error: function(xhr, status, error) {
//       Swal.fire({
//         icon: 'error',
//         title: 'Delete Failed',
//         text: 'An error occurred while deleting the teacher record: ' + error
//       });
//     }
//   });
// }
