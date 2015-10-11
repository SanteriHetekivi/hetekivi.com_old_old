<?php
  function runFunction($function, $variables)
  {
    $session=&$_SESSION["session"];
    switch ($function) {
      case 'login':
        if(isset($variables["username"]) && isset($variables["password"]))
        {
          if($session->Login($variables["username"],$variables["password"]));
          else ERROR("Login failed!");
        }
        else ERROR("Username or password not set!");
        break;

      default:
        ERROR("Function named ".$function." does not exist!");
        break;
    }
    BACK();
    return true;
  }

?>
