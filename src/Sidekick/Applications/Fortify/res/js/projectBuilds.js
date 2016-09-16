$('.projectBuild').on('click', function(){

  var postData = {'add' : $(this).is(':checked'), 'value' : $(this).val()};
  console.log(postData);
  $.post('/fortify/build-configs/aprojects', postData, function(response){
    console.log(response);
  });

});
