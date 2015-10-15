var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

var showAlert = function getUrlParameter(type, text, timeoutSec)
{
  var timeout = (timeoutSec>0)?timeoutSec*1000:2000;
  var color = "white";
  if(type=="error") color = "red";
  else if(type=="message") color = "green";

  if($("#ModalAlert").length)
  {
    $("#ModalAlertText").text(text);
    $("#ModalAlertContent").css("background-color",color);
    $("#ModalAlert").modal("show");
    setTimeout(function(){ $("#ModalAlert").modal("hide"); }, timeout*1000);
  }
  else {
    $("#alert").load("libs/html/popup/alert.html", function()
    {
      $("#ModalAlertText").text(text);
      $("#ModalAlertContent").css("background-color",color);
      $("#ModalAlert").modal("show");
      setTimeout(function(){ $("#ModalAlert").modal("hide"); }, timeout*1000);
    });
  }
}

var REMOVE = function REMOVE(id)
{
  $.post("rest/index.php/remove", {id: id}, function(result){
    if(result == true)
    {
      showAlert("message","Removed!",1);
      loadTable();
    }
    else {
      showAlert("error","Remove failed!",1);
    }
  });
}

var EDIT = function EDIT(object, row, id)
{
  this.setForm = function setForm(name, row, id)
  {
    var title = "";
    $("#"+name+"Form *").filter(':input').each(function(i, input){
      var column = $(input).attr('id');
      var type = $(input).prop('nodeName');
      var value = (row[column])?row[column]:"";
      if(value == "" && type == "SELECT") $(input).val($("#"+column+" option:first").val());
      else $(input).val(value);
      if(column == "title") title = value;
      else if(column == "image") $("#imagePrev").attr("src", value);
    });
    $("#id").val(id);
    $("#"+name+"Title").text(id+": "+title);
  }
  this.setLiseners = function setLiseners(name, object)
  {
    $("#"+name+"Save").click(function()
    {
      var data = $("#"+name+"Form").serialize();
      data += "&object="+object;
      $.post("rest/index.php/edit", data , function(result)
      {
        if(result == true)
        {
          showAlert("message","Edited!",0.1);
          loadTable();
          setForm(name,[],0);
          onHideEdit();
          $("#"+object+"Edit").modal("hide");
        }
        else {
          showAlert("error","Edit failed!",0.1);
        }
      });
    });
    $("#"+object+"Edit").on('hidden.bs.modal', function ()
    {
      onHideEdit();
    });
  }

  var name = object+"Edit";

  if($("#"+name).length)
  {
    setForm(name, row, id);
    setupEdit();
    $("#"+object+"Edit").modal("show");
  }
  else
  {
    $("#"+object+"Modal").load("libs/html/edit/"+object+".html", function()
    {
      setForm(name, row, id);
      setupEdit();
      setLiseners(name, object);
      $("#"+object+"Edit").modal("show");
    });
  }
}

function isEmpty(obj) {

    // null and undefined are "empty"
    if (obj == null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}
