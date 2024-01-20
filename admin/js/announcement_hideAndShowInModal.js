$(document).ready(function () {
  $('#recipient').on('change', function () {
    var recipientOption = $(this).val();

    if (recipientOption === 'rotcgroups') {
      $('#rotcgroupSection').show();
      $('#cwtsgroupSection').hide();
      $('#specificSection').hide();
    } else if (recipientOption === 'cwtsgroups') {
      $('#rotcgroupSection').hide();
      $('#cwtsgroupSection').show();
      $('#specificSection').hide();
    } else if (recipientOption === 'specific') {
      $('#rotcgroupSection').hide();
      $('#cwtsgroupSection').hide();
      $('#specificSection').show();
    } else {
      $('#rotcgroupSection').hide();
      $('#cwtsgroupSection').hide();
      $('#specificSection').hide();
    }
  });
});
