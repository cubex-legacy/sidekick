$(document).ready(function(){
  $($('.icon-trash').parents('a')[0]).on('click', function(){
    return confirm('Are you sure to delete?');
  });
});
$('.hoverActions').on({
                       mouseenter: function () {
                         $(this).find('.hoverHide').css('display', 'none');
                         $(this).find('.hoverShow').css('display', 'table-cell');
                       },
                        mouseleave: function () {
                          $(this).find('.hoverShow').css('display', 'none');
                          $(this).find('.hoverHide').css('display', 'table-cell');
                        }
});
