function disablestudent(accountId) {
  Swal.fire({
    icon: 'warning',
    title: 'Confirmation',
    text: 'Are you sure you want to disable this student?',
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
  // Send an AJAX request to Disable the student record
  $.ajax({
    type: 'POST',
    url: 'disable_student.php', // Replace with the actual PHP file to handle the deletion
    data: { user_account_id: accountId },
    success: function (response) {
      if (response === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Disable Successful',
          text: 'The student record has been disable.',
          showConfirmButton: false,
          timer: 3000,
        }).then(function () {
          window.location.href = 'studentlist.php';
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Disable Failed',
          text:
            'An error occurred while disabling the student record: ' + response,
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        icon: 'error',
        title: 'Disable Failed',
        text: 'An error occurred while disabling the student record: ' + error,
      });
    },
  });
}

// function confirmDelete(studentNumber) {
//   Swal.fire({
//     icon: 'warning',
//     title: 'Confirmation',
//     text: 'Are you sure you want to delete this student?',
//     showCancelButton: true,
//     confirmButtonText: 'Delete',
//     cancelButtonText: 'Cancel',
//   }).then((result) => {
//     if (result.isConfirmed) {
//       deleteStudent(studentNumber);
//     }
//   });
// }

// function deleteStudent(studentNumber) {
//   // Send an AJAX request to delete the student record
//   $.ajax({
//     type: 'POST',
//     url: 'delete_student.php', // Replace with the actual PHP file to handle the deletion
//     data: { student_number: studentNumber },
//     success: function (response) {
//       if (response === 'success') {
//         Swal.fire({
//           icon: 'success',
//           title: 'Delete Successful',
//           text: 'The student record has been deleted.',
//           showConfirmButton: false,
//           timer: 3000,
//         }).then(function () {
//           window.location.href = 'studentlist.php';
//         });
//       } else {
//         Swal.fire({
//           icon: 'error',
//           title: 'Delete Failed',
//           text:
//             'An error occurred while deleting the student record: ' + response,
//         });
//       }
//     },
//     error: function (xhr, status, error) {
//       Swal.fire({
//         icon: 'error',
//         title: 'Delete Failed',
//         text: 'An error occurred while deleting the student record: ' + error,
//       });
//     },
//   });
// }
