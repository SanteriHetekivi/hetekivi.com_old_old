$(document).ready(function(){
  var error = getUrlParameter("error");
  if(error)
  {
    showAlert("error",error,5);
  }
  $.getJSON("rest/index.php/login",  function(login)
  {
    if(login) window.location.replace("home.html");
    else $("#login").load("libs/html/popup/login.html");
  });
});
