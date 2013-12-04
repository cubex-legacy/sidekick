/**
 * @author oke.ugwu
 */

$(function(){
  $('.foreign-text').on('blur change paste', function(){
    var post = {};
    post['rowKey'] = $(this).data('rowkey');
    post['lang'] = $(this).data('lang');
    post['text'] = $(this).text().trim();

    var postUrl = '/P'+PROJECTID+'/rosetta/edit';
    $.post(postUrl, post, function(data){
      console.log(post);
      console.log(data);
    });
  });

  $('.select-all').on('click', function(){
    var checkBoxes = $('.text-selecter');
    var selectAll = $(this);
    $.each(checkBoxes, function(index, obj){
      $(obj).prop("checked", selectAll.prop("checked"));
    });

    toggleApproveSelectedButton();
  });

  $('.text-selecter').on('change', function(){
    console.log('changes');
    if(!$(this).prop('checked'))
    {
      $('.select-all').prop('checked', false);
    }

    toggleApproveSelectedButton();
  });

  function toggleApproveSelectedButton()
  {
    var selectedCount = $('.text-selecter:checked').length;
    if(selectedCount > 0)
    {
      $('.approve-selected').prop('disabled', false);
    }
    else
    {
      $('.approve-selected').prop('disabled', true);
    }
  }

  $('.approve-selected').on('click', function(){
    var checkBoxes = $('.text-selecter');
    $.each(checkBoxes, function(index, obj){
      if($(obj).prop("checked"))
      {
        var post = {};
        post['rowKey'] = $(obj).data('rowkey');
        post['lang'] = $(obj).data('lang');

        var postUrl = '/P'+PROJECTID+'/rosetta/approve';
        $.post(postUrl, post, function(data){
          //hide approved rows
          console.log(data);
          $(obj).parent().parent().hide();
        });
      }
    });
  })

});
