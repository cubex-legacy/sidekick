/**
 * Created with JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 26/07/13
 * Time: 09:51
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
  var postData = {};
  postData['serviceClass'] = $('#form-StageEdit-serviceClass').val();
  console.log($('#form-StageEdit-serviceClass').val());
  $.post('getConfigurationOptions', postData, function(data){
    setInnerHtml(data)
  });

  $('#form-StageEdit-serviceClass').on("change", function(){
    var postData = {};
    postData['serviceClass'] = $(this).val();
    $.post('getConfigurationOptions', postData, function(data){
      setInnerHtml(data)
    });
  });

  function setInnerHtml(data)
  {
    $('#configurationTable').html(data);
    $(".config-value-toggle").on("change", function(){
      var textareaId = $(this).data('config-id');
      if($(this).is(':checked'))
      {
        $('#'+textareaId).removeAttr('disabled');
      }
      else
      {
        $('#'+textareaId).attr('disabled', 'disabled');
      }
    });
  }
});

