/**
 * Created with JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 13/05/13
 * Time: 14:42
 * To change this template use File | Settings | File Templates.
 */
  $(function(){
    $('.env-edit').blur(function(){
      val = $(this).val();
      id = $(this).attr('id');

      //make ajax call to save new environment name
      $.get('environments/update/'+id+'/'+val, function(response){
        response = jQuery.parseJSON(response);
        $('.env-edit-'+response.id).val(response.name);
        $('#env-label-'+response.id).text(response.name);
        $('#env-filename-'+response.id).text(response.filename);

        $('.env-edit-'+response.id).hide();
        $('#env-label-'+response.id).show();
        console.log(response);
      });

    });

    $('.add-env').click(function(){
      val  = $('.env-name').val();
      $.get('environments/add/'+val, function(response){
        console.log('Response from ajax: '+response);
        window.location = 'environments';
      })
    })


  });

  function toggleEdit(env)
  {
    $('#env-label-'+env).hide();
    $('.env-edit-'+env).show();
    $('.env-edit-'+env).focus();
  }