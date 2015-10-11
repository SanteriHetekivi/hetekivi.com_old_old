<?php
  setlocale(LC_MONETARY,"fi_FI");
  require_once("conf.php");
  require_once(LIBS_PATH."mysql.php");
  require_once(LIBS_PATH."functions.php");
  require_once(LIBS_PATH."mysqlFunctions.php");
  require_once(LIBS_PATH."classes.php");
  session_name("HETEKIVI");
  session_start();
  if(!isset($_SESSION["session"])) $_SESSION["session"]=new Session();
	$session=&$_SESSION["session"];

  function ERROR($text = FALSE)
  {
    $location = (!empty($_SERVER["HTTP_REFERER"]))?$_SERVER["HTTP_REFERER"]:ADDRESS."index.html";
    $location .= "?error=".$text;
    header("Location: ".$location);
    die("Redirecting to: ".$location);
  }

  function BACK()
  {
    $location = (!empty($_SERVER["HTTP_REFERER"]))?$_SERVER["HTTP_REFERER"]:ADDRESS."index.html";
    header("Location: ".$location);
    die("Redirecting to: ".$location);
  }

  header('Content-Type: application/json');
  header('Content-type: text/plain; charset=utf-8');

  if(isset($_SERVER["PATH_INFO"]) && isset($_SERVER["REQUEST_METHOD"]))
  {
    $function = ltrim ($_SERVER["PATH_INFO"], '/');
    $method = $_SERVER["REQUEST_METHOD"];
    $variables = array();
    if($method === "POST" && isset($_POST)) {
      $variables = $_POST;
      require_once("POST.php");
    }
    else if($method === "GET"){
      $variables = $_GET;
      require_once("GET.php");
    }
    else ERROR("REQUEST_METHOD is not POST or GET");

    if(checkLogin() || $function=="login")
    {
      if(!runFunction($function, $variables))
      {
        ERROR("Running function ".$function." with variables ".json_encode($variables)." failed");
      }
    }else ERROR("Not logged in!");


  }
  else ERROR("No PATH_INFO or REQUEST_METHOD");
  exit();
?>
