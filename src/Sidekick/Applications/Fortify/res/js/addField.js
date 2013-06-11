jQuery(document).ready(
  function ()
  {
    $('[data-field-to-add]').click(
      function ()
      {
        var i = 0;
        var fieldToAdd = $(this).data('field-to-add');
        while ($('[name="' + fieldToAdd + '[' + (i + 1) + ']"]').length > 0){
          i++;
        }
        var field = $('[name="' + fieldToAdd + '[' + i + ']"]');
        var clone = field.clone();
        clone.attr('name', fieldToAdd + '[' + (i + 1) + ']').val('');
        clone.insertAfter(field);
      });
  });
