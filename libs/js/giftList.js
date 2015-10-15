$(document).ready(function(){
  $("#newGift").click(function(){
    EDIT("gift",[],0);
  });
});

function operateFormatter(value, row, index)
{
  return [
      '<button id="edit" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></br>',
      '<button id="remove" type="button" class="btn btn-danger"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>'
  ].join('');
}
window.operateEvents =
{
    'click #edit': function (e, value, row, index) {
      EDIT("gift",row,value);
    },
    'click #remove': function (e, value, row, index) {
      REMOVE(value);
    }
};

var loadTable = function loadTable()
{
  var $table = $('#giftlist_table');
  $table.bootstrapTable('refresh', {url: 'rest/index.php/giftList'});
}
