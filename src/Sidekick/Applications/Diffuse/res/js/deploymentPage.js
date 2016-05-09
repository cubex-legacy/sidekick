/**
 * Created with JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 26/07/13
 * Time: 09:51
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
  $('[id^=group-]').on("click", function(){
    var id = this.id;
    var arr = hostGroups[id.replace('group-','')];
    for(a in arr){

      $('[name="deploymentHosts['+arr[a]+']"]').prop('checked', $(this).prop("checked"));
    }
  });
  $('.hostsHead').on('click', function(){
    $($(this).parents()[0]).find('.hostsList').css('display','block');
  });

  $('[name^="deploymentHosts["').on('click', function(){ 
    $($(this).parents()[1]).find('.headCheckBox').prop("checked", false);
  });

  setInterval(function(){
    $('.hostsContainer').each(function(){
      var container = $(this);
      var active = false;
      $(this).find('input[type="checkbox"]').each(function(){
        if($(this).prop('checked')){
          active = true
        }
      });
      if(active){
        container.addClass('activeDeployHost');
      }else
      {
        container.removeClass('activeDeployHost');
      }

    });}, 1000);

});

