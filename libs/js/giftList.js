$(document).ready(function(){

  $.getJSON("rest/index.php/giftList",  function(data)
  {
    var $table = $('#giftlist_table');
    $(function () {
      $table.bootstrapTable({data: data});
    });
  });
});
