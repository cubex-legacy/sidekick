$(function(){
  $('.severity').change(function(){

    var post = {};
    post['severity'] = $(this).val();
    post['eventTypeId'] = $(this).data('eventtypeid');

    var postUrl = '/events/subscribe';
    $.post(postUrl, post, function(data){
      console.log(data);
      location.reload();
    });

  })
})
