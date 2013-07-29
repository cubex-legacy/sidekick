/**
 * Created with JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 26/07/13
 * Time: 09:51
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
  $("#addConfig").on("click", function(e) {
    e.preventDefault();
    $("#configurationTable").append(createRow("configuration"));
  });
  $("#addDep").on("click", function(e) {
    e.preventDefault();
    $("#dependencyTable").append(createRow("dependency"));
  });
  $(document).on("click","a.deleteRow",function(e) {
    e.preventDefault();
    $(this).closest("tr").remove();
  })
});

function createRow(name)
{
  var tr=$("<tr></tr>");
  tr.append("<td><input type='text' name='" + name + "Keys[]' /></td>");
  tr.append("<td><textarea name='" + name + "Values[]'></textarea></td>");
  tr.append("<td><a href='#' class='deleteRow' title='Delete'><i class='icon icon-trash'></i></a></td>");
  return tr;
}
