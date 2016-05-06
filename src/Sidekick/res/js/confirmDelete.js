$(document).ready(function(){
  $('a.icon-trash').on('click', function(){
    return confirm('Are you sure to delete?');
  });
});
