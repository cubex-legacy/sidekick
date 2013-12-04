/**
 * Created with JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 05/07/13
 * Time: 10:26
 * To change this template use File | Settings | File Templates.
 */
$(function(){
  $('#addConfigBtn').click(function(){
    $('#approveForm').toggle();
    $(this).hide();
  });

  $('#cancelBtn').click(event, function(){
    event.preventDefault();
    $('#approveForm').hide();
    $('#addConfigBtn').show();
  });
});


function updateField(obj, projectId, role)
{
  var name = $(obj).attr('name');
  var post = {projectId:projectId, role:role};
  post[name] = $(obj).val();
  var postUrl = '/P'+PROJECTID+'/diffuse/approval/'+projectId+'/'+role+'/edit';

  $.post(postUrl, post, function(data){
    console.log(data);
    location.reload();
  });
}
