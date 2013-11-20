/**
 * @author oke.ugwu
 */

$(function(){
  $('.foreign-text').on('blur change paste', function(){
    var post = {};
    post['rowKey'] = $(this).data('rowkey');
    post['lang'] = $(this).data('lang');
    post['text'] = $(this).text().trim();

    var postUrl = '/rosetta/edit';
    $.post(postUrl, post, function(data){
      console.log(post);
      console.log(data);
    });
  });

});
