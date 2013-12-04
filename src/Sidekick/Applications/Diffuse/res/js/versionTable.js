/**
 * User: Sam.Waters
 * Date: 31/07/13 11:55
 */
$(document).ready(function() {
  $("#updateButton").on("click",function(e) {
    e.preventDefault();
    $("#modal").modal("show");
    $.ajax({
      url: "/diffuse",
      type: "POST",
      data: "state=" + $("#stateSelect").val() + "&allplatforms=" + $("#allPlatforms").prop("checked"),
      success: function(data) {
        updateTable(data);
        $("#modal").modal("hide");
      }
    });
  });
});

function updateTable(data) {
  var $tbl=$("#versionTable").find("tbody");
  $tbl.empty();
  for(var version in data) {
    if(!data.hasOwnProperty(version)) continue;
    $tr=$("<tr></tr>");
    $tr.append("<td>" + data[version]["project"] + "</td>");
    $tr.append("<td><a href='/P"+PROJECTID+"/diffuse/projects/" + data[version]["projectid"] + "/v/" + version + "'>" + data[version]["version"] + "</a></td>");
    $tr.append("<td>" + data[version]["type"] + "</td>");
    $tr.append("<td>" + statesList(data[version]["states"]) + "</td>");
    $tr.append("<td>" + data[version]["updated"] + "</td>");
    $tbl.append($tr);
  }
}

function statesList(states) {
  var html="";
  for(var i in states) {
    if(!states.hasOwnProperty(i)) continue;
    html+=i + ": " + states[i] + "<br />";
  }
  return html;
}
