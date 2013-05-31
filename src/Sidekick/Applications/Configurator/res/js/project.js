/**
 * Created with JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 30/05/13
 * Time: 08:53
 * To change this template use File | Settings | File Templates.
 */
$('.project').mouseenter(function(){
  $('.project-actions').hide();
  $('.project-actions', this).show();
});

$('.project').mouseleave(function(){
  $('.project-actions').hide();
})

