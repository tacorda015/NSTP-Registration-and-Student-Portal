// Start Ajax Add Student to Group
$(document).ready(function () {
  $('#addComponentId').change(function () {
    var component_id = $(this).val();
    if (component_id == 1 || component_id == 'ROTC') {
      var url = 'get_rotcgroup.php';
    } else if (component_id == 2 || component_id == 'CWTS') {
      var url = 'get_cwtsgroup.php';
    }
    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
      data: { component_id: component_id },
      success: function (response) {
        var len = response.length;
        $('#addGroupId').empty();
        $('#addGroupId')
          .empty()
          .append(
            "<option value='' selected disabled hidden>Choose Group</option>"
          );
        for (var i = 0; i < len; i++) {
          var group_id = response[i]['group_id'];
          var group_name = response[i]['group_name'];
          if (component_id == 1 || component_id == 'ROTC') {
            $('#addGroupId').append(
              "<option value='" + group_id + "'>" + group_name + '</option>'
            );
          } else if (component_id == 2 || component_id == 'CWTS') {
            $('#addGroupId').append(
              "<option value='" + group_id + "'>" + group_name + '</option>'
            );
          }
        }
        // Set the value of addGroupId to the selected option
        $('#addGroupId').val($('#addGroupId option:selected').val());
      },
    });
  });
});
// End Ajax Add Student to Group

// Start Ajax Update Student to Group
// $(document).ready(function () {
//   $('.updatecomponent').change(function () {
//     var component_id = $(this).val();
//     if (component_id == 1 || component_id == 'ROTC') {
//       var url = 'get_rotcgroup.php';
//     } else if (component_id == 2 || component_id == 'CWTS') {
//       var url = 'get_cwtsgroup.php';
//     }
//     $.ajax({
//       url: url,
//       type: 'POST',
//       dataType: 'json',
//       data: { component_id: component_id },
//       success: function (response) {
//         var len = response.length;
//         $('.updatechoosegroup').empty();
//         if (len > 0) {
//           for (var i = 0; i < len; i++) {
//             var group_id = response[i]['group_id'];
//             var group_name = response[i]['group_name'];
//             $('.updatechoosegroup').append(
//               "<option value='" +
//                 group_id +
//                 "' onchange='displayGroup(this.value)'>" +
//                 group_name +
//                 '</option>'
//             );
//           }
//         } else {
//           $('.updatechoosegroup').append(
//             "<option value='' selected hidden>No assigned group</option>"
//           );
//         }
//         // Set the value of updatechoosegroup to the selected option
//         $('.updatechoosegroup').val(
//           $('.updatechoosegroup option:selected').val()
//         );
//       },
//     });
//   });
// });
// End Ajax Update Student to Group
