$(document).ready(function () {
  $('#component_id').change(function () {
    var component_id = $(this).val();
    if (component_id == 1 || component_id == 'ROTC') {
      var url = 'get_trainers.php';
    } else if (component_id == 2 || component_id == 'CWTS') {
      var url = 'get_teachers.php';
    }
    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
      data: { component_id: component_id },
      success: function (response) {
        var len = response.length;
        $('#incharge_person').empty();
        for (var i = 0; i < len; i++) {
          var teacher_name = response[i]['teacher_name'];
          var trainer_name = response[i]['trainer_name'];
          if (component_id == 1 || component_id == 'ROTC') {
            $('#incharge_person').append(
              "<option value='" +
                trainer_name +
                "'>" +
                trainer_name +
                '</option>'
            );
          } else if (component_id == 2 || component_id == 'CWTS') {
            $('#incharge_person').append(
              "<option value='" +
                teacher_name +
                "'>" +
                teacher_name +
                '</option>'
            );
          }
        }
        // Set the value of incharge_person to the selected option
        $('#incharge_person').val($('#incharge_person option:selected').val());
      },
    });
  });
});
