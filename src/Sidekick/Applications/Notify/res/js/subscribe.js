/**
 * Created with JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 27/11/13
 * Time: 11:01
 * To change this template use File | Settings | File Templates.
 */
$(function(){
  $('#contactMethod').on('change', function(){
    $(this).parent('form').submit();
  })
})
