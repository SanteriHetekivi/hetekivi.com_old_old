function setupEdit()
{
  var isNew = ($("#id").val()>0)?false:true;
  $.getJSON("rest/index.php/gift_positions", {new : isNew} , function(data){
      var options = "";
      var selected = $( 'select[name="gift_position"]' ).val();
      $.each(data, function(index, value){
          var sel = (selected == index)?"selected":"";
          options += "<option "+sel+" value='" + index + "'>" + data[index] + "</option>";
      });
      $('select[name="gift_position"]').html(options);
  });
  $.getJSON("rest/index.php/gift_types", function(data){
      var options = "";
      var selected = $( 'select[name="gifts_types_id"]' ).val();
      $.each(data, function(index, value){
          var sel = (selected == index)?"selected":"";
          options += "<option "+sel+" value='" + index + "'>" + data[index] + "</option>";
      });
      $( 'select[name="gifts_types_id"]' ).html(options);
  });
  var oldGift = $( 'select[id="oldGifts"]' ).val();
  if(typeof oldGift === "undefined" || !oldGift)
  {
    console.log("hi");
    $.getJSON("rest/index.php/getOldGifts" , function(data){
        var options = "";
        console.log(data);
        options += "<option value=''>SELECT</option>";
        $.each(data, function(index, value){
            options += "<option value='" + index + "'>" + value + "</option>";
        });
        $( 'select[id="oldGifts"]' ).html(options);
    });
  }
}

function onHideEdit()
{
  $( 'select[id="oldGifts"]' ).val("");
}

$(document).ready(function()
{

  $("#image").change(function()
  {
    $("#imagePrev").attr("src", $("#image").val());
  });
  $("#oldGifts").change(function()
  {
    var id = $("#oldGifts").val();
    $.getJSON("rest/index.php/gift", {id:id}, function(data){
        EDIT("gift",data,0);
    });
  });
});
