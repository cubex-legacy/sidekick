/**
 * @author: Sam.Waters
 * Date: 22/08/13 11:20
 *
 * To use:
 * Add a class of dynamic-table and an ID to the table you want to update
 * Add a class of dynamic-table-button and a data-dynamic="table-id-here" attribute to the Add button
 *
 * Make sure you define the table with <th> headers, as these are used to name the text fields
 * e.g. <th>Name</th> will result in a text field with the name name[] being created
 *
 * The delete icons are added automatically
 *
 */
$(document).on("click", ".dynamic-table-button", function(e) {
  e.preventDefault();
  var $this = $(this);
  var tbl = $this.attr("data-dynamic");
  var $tbl = $("#" + tbl);
  if($tbl.length==0) return;
  var rowCount = $tbl.find("th").length - 1;
  var $tbody = $tbl.find("tbody").first();
  var $tr = $("<tr></tr>");
  for(i=0;i<rowCount;i++) {
    var title = $tbl.find("th:eq(" + i + ")").text();
    title = title.toLowerCase();
    var $td = $("<td></td>");
    $td.append("<input type='text' name='" + title + "[]' />");
    $tr.append($td);
  }
  $tr.append("<td><a href='#' class='dynamic-row-delete'><i class='icon-trash'></i></a></td>");
  $tbody.append($tr);
});

$(document).on("click", "a.dynamic-row-delete", function(e) {
  e.preventDefault();
  if(!confirm("Are you sure you want to remove this parameter?")) return;
  var $this=$(this);
  var $tr = $this.closest("tr");
  $tr.remove();
});
