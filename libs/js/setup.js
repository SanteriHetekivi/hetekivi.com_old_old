$(document).ready(function(){
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
      $("#login").load("login.html");
      $("#navLogin").attr("href","");
      $("#navLogin").attr("data-toggle","modal");
      $("#navLogin").attr("data-target","#ModalLogin");
      $("#navLogin").text("Login");
    }
  });
});
