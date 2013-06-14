/**
 * Created with JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 14/06/13
 * Time: 16:28
 * To change this template use File | Settings | File Templates.
 */
function toggle(selector)
{
  $(selector).toggle('fast', function (){
    var text = $(selector + 'Trigger').text();
    if ($.trim(text) == '+')
    {
      $(selector + '-trigger').text('-');
    }
    else
    {
      $(selector + '-trigger').text('+');
    }
  })
}
