/**
 * Created with JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 31/05/13
 * Time: 12:08
 * To change this template use File | Settings | File Templates.
 */
$('.phuse-search').keyup(function(){
  var q = $('.phuse-search').val();
  if(q.length > 0)
  {
    $.get('/phuse/search/'+q, function(data){
      if(data.length > 0)
      {
        $('.phuse-results').html('<h2>Packages</h2>'+data);
        $('.phuse-results').slideDown();
      }

    })
  }
  else
  {
    $('.phuse-results').slideUp();
  }


})
