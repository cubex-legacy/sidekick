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

      $('[name="deploymentHosts['+arr[a]+']"]').attr('checked', this.checked ? 'true' : false);
    };
  });
});

