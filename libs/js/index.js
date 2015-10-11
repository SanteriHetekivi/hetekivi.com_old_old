$(document).ready(function(){
  $.getJSON("rest/index.php/login",  function(login)
  {
    if(login) window.location.replace("home.html");
    else $("#login").load("login.html");
  });
});
