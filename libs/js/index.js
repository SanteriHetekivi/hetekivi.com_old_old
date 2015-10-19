$(document).ready(function(){
  var error = getUrlParameter("error");
  if(error)
  {
    showAlert("error",error,5);
  }
  $.getJSON("REST2/index.php/ISLOGEDIN",  function(login)
  {
    if(login.ISLOGEDIN == true) window.location.replace("home.html");
    else $("#login").load("libs/html/popup/login.html");
  });
});
