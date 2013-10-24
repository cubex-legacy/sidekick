/**
 * Created with JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 24/10/13
 * Time: 10:08
 * To change this template use File | Settings | File Templates.
 */
$(function(){
  $('#event-name').on('focus', function(){
    $('#icon-edit-name').hide();
  });
  $('#event-name').on('blur', function(){
    $('#icon-edit-name').show();
  });

  $('#event-description').on('focus', function(){
    $('#icon-edit-description').hide();
  });
  $('#event-description').on('blur', function(){
    $('#icon-edit-description').show();
  });

  $('.event-data').on('blur change paste', function(){
    var post = {};
    post['id'] = $(this).data('eventid');
    if($(this).attr('id') == 'event-name')
    {
      post['name'] = $(this).text().trim();
    }
    else if($(this).attr('id') == 'event-description')
    {
      post['description'] = $(this).text().trim();
    }

    var postUrl = '/events/'+ post['id'];
    $.post(postUrl, post, function(data){
      console.log(post);
      console.log(data);
    });


  });

});
