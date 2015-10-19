$(document).ready(function(){
//  $.get("rest/index.php/error",  function(error) { if(error)showAlert("error",error,5); });
  //$.get("rest/index.php/message",  function(message) { if(message)showAlert("message",message,5); });

  $.getJSON("rest/index.php/login",  function(login)
  {
    if(login == true)
    {
      $("#navLogin").attr("href","rest/index.php/logout");
      $("#navLogin").attr("data-toggle","");
      $("#navLogin").attr("data-target","");
      $("#navLogin").text("Logout");
    }
    else
    {
      $("#login").load("libs/html/popup/login.html");
      $("#navLogin").attr("href","");
      $("#navLogin").attr("data-toggle","modal");
      $("#navLogin").attr("data-target","#ModalLogin");
      $("#navLogin").text("Login");
    }
  });
});
