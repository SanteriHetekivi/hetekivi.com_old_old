<?php

/**
 *
 */
class Rest
{
  private $auth = null;
  private $messages = null;
  private $data = null;

  function __construct()
  {
    if(!isset($_SESSION["auth"])) $_SESSION["auth"]=new Auth();
	  $this->auth = &$_SESSION["auth"];
  }

  public function CALL()
  {

    if(isset($_SERVER["PATH_INFO"]) && isset($_SERVER["REQUEST_METHOD"]))
    {
      $function = onlyLetters($_SERVER["PATH_INFO"]);
      $method = $_SERVER["REQUEST_METHOD"];

      $variables = false;
      $postdata = file_get_contents("php://input");

      if($method === "POST")
      {
        if(isset($_POST) && !empty($_POST)) $variables = $_POST;
        else $variables = json_decode($postdata, true);
      }
      else if($method === "GET") $variables = $_GET;

      if($this->CHECK() && $function && is_array($variables))
      {
        $this->$function($variables);
      }
      elseif($function == "LOGIN") $this->LOGIN($variables);
      else $this->ERROR("Not locked in!");
    }else $this->ERROR("Incorrect call!");
    header('Content-Type: application/json');
    header('Content-type: text/plain; charset=utf-8');
    $data = $this->data;
    $this->data = false;
    echo json_encode($data);
    if($method === "POST") $this->BACK();
  }

  private function BACK()
  {
    $location = (!empty($_SERVER["HTTP_REFERER"]))?$_SERVER["HTTP_REFERER"]:ADDRESS."index.html";
    header("Location: ".$location);
    die("Redirecting to: ".$location);
  }

  private function GETObject($parameters)
  {
    $data = false;
    $userid = $this->userID();
    if(isset($parameters["object"]) && isClass($parameters["object"]) && isset($parameters["id"]))
    {
      $objectName = onlyLetters($parameters["object"]);
      $table = classAndTable($objectName, "class");
      $ids = (is_array($parameters["id"]))?$parameters["id"]:array($parameters["id"]);
      $order = (isset($parameters["order"]))?$parameters["order"]:FALSE;

      if(hasUserLink($parameters["object"]))
      {
        /*$oColumns = getObjectByName($objectName)->getColumnNames();
        unset($oColumns["id"]);
        foreach ($variable as $key => $value) {
          # code...
        }
        $oColumns = new UserLink()->getColumnNames();*/
        $data = SQL_SELECT(
          $_columns = FALSE,
          $_table=$table,
          $_joinTable = "users_links",
          $_joinTableId = $table."_id",
          $_where = "users_links.users_id='".$userid."' AND ".$table."_id IN ('".implode("','", $ids)."')",
          $_order = $order,
      		$_offset = FALSE,
      		$_limit = FALSE,
      		$_object = FALSE,
      		$_onlyFirstRow = FALSE,
      		$_noIDKeys = TRUE
        );
      }else
      {
        $data = SQL_SELECT(
          $_columns = "*, COUNT(*)",
          $_table=$table,
          $_joinTable = FALSE,
          $_joinTableId = FALSE,
          $_where = "id IN ('".implode("','", $ids)."')",
          $_order = $order,
      		$_offset = FALSE,
      		$_limit = FALSE,
      		$_object = FALSE,
      		$_onlyFirstRow = FALSE,
      		$_noIDKeys = TRUE
        );
      }
    }
    if($data)
    {
      $this->DATA($data, $objectName);
      $this->DATA(COUNT($data), $objectName."COUNT");
    }
    else $this->ERROR("Getting failed!");
  }

  private function EDITObject($parameters)
  {
    $success = false;
    if(isset($parameters["object"]) && isClass($parameters["object"]))
    {
      $id = (isset($parameters["id"]))?$parameters["id"]:0;
      unset($parameters["object"]);
      eval("\$object = new ".$parameters["object"]."(".$id.");");
      if(is_object($object))
      {
        if(isset($parameters["columns"]) && is_array($parameters["columns"]))
        {
          foreach ($parameters["columns"] as $column => $value) $object->setValue($column, $value);
        }
        if(isset($parameters["linkedColumns"]) && is_array($parameters["linkedColumns"]))
        {
          foreach ($parameters["linkedColumns"] as $column => $value) $object->setLinkedValue($column, $value);
        }
        debug($object);
        //$success = $object->COMMIT();
      }
    }

    if($success) $this->MESSAGE("Editing successful!");
    else $this->ERROR("Editing failed!");
  }


  private function REMOVEObject($parameters)
  {
    $success = false;
    if(isset($parameters["object"]) && isClass($parameters["object"]) && isset($parameters["id"])
      && is_numeric($parameters["id"]) && $parameters["id"] > 0)
    {
      eval("\$object = new ".$parameters["object"]."(".$parameters["id"].");");
      if(is_object($object)) $success = $object->REMOVE();
    }

    if($success) $this->MESSAGE("Remove successful!");
    else $this->ERROR("Remove failed!");
  }

  private function LOGIN($parameters)
  {
    $success  = false;
    if(isset($parameters["username"]) && isset($parameters["password"]))
    {
      $success = (is_object($this->auth) && $this->auth->LOGIN($parameters["username"], $parameters["password"]));
    }

    if($success) $this->MESSAGE("Logged in!");
    else $this->ERROR("Login failed!");
  }

  private function ISLOGEDIN($parameters)
  {
    $this->DATA($this->CHECK(), $id = "ISLOGEDIN");
  }

  private function LOGOUT($parameters)
  {

    $success = (is_object($this->auth) && $this->auth->LOGOUT());

    if($success) $this->MESSAGE("Logged in!");
    else $this->ERROR("Login failed!");
  }

  private function ERROR($text=false)
  {
    if(isset($text))$this->DATA(array($text=>$text), "ERROR");
    else $this->ERROR("No text set on ERROR call!");
  }
  private function MESSAGE($text)
  {
    if(isset($text))$this->DATA(array($text=>$text), "MESSAGE");
    else $this->ERROR("No text set on MESSAGE call!");
  }

  private function DATA($data, $id = false)
  {
    if($id)
    {
      if(isset($this->data[$id])) $this->data[$id] += $data;
      else $this->data[$id] = $data;
    }
    else $this->data[] = $data;
  }

  private function CHECK()
  {
    return (is_object($this->auth))?$this->auth->CHECK():false;
  }

  private function userID()
  {
    return (is_object($this->auth) && is_object($this->auth->user))?$this->auth->user->getID():false;
  }


  /*UNIUGE FUNCTIONS*/
  function giftList($variables)
  {
    $order = (isset($variables["order"]))?$variables["order"]:"desc";
    $limit = (isset($variables["limit"]) && $variables["limit"] != "ALL")?$variables["limit"]:false;
    $offset = (isset($variables["offset"]))?$variables["offset"]:false;
    $userId = $this->userID();;
    if($userId)
    {
      $IDs = SQL_SELECT(
        $_columns = "id",
        $_table="gifts",
        $_joinTable = "users_links",
        $_joinTableId = "gifts_id",
        $_where = "users_links.users_id='".$userId."'",
        $_order = "users_links.gift_position ".$order,
        $_offset = $offset,
        $_limit = $limit,
        $_object = FALSE,
        $_onlyFirstRow = FALSE);
      $variables = array("object"=>"Gift", "order"=>$order, "id"=>$IDs);
      $this->GETObject($variables);
      $this->DATA(SQL_SELECT("title","gifts_types"), "GiftTypes");
    }else $this->ERROR("Getting gifts failed!");
  }

  function MAL($variables)
  {

    $ob = (isset($variables["manga_or_anime"]) && $variables["manga_or_anime"] == "anime")?$variables["manga_or_anime"]:"manga";
    $sort = (isset($variables["sort"]))?$variables["sort"]:"manga_anime_last_updated";
    if($sort=="mal_status") $sort = "status";
    elseif($sort=="user_status") $sort = "manga_anime_status";
    elseif($sort=="completed_parts") $sort = "manga_anime_parts";
    $order = (isset($variables["order"]))?$variables["order"]:"desc";
    $limit = (isset($variables["limit"]))?$variables["limit"]:FAlSE;
    $offset = (isset($variables["offset"]))?$variables["offset"]:FAlSE;
    $search = (isset($variables["search"]))?" AND title LIKE '%".$variables["search"]."%'":"";
    $order = $sort." ".$order;
    $table = $ob."s";
    $join_id = $ob."s_id";
    $userId = $this->userID();;
    if($userId)
    {
      $where = "users_id='".$userId."' AND ".$join_id.">'0'".$search;
      $IDs =  SQL_SELECT(
        $_columns = "id",
        $_table=$table,
        $_joinTable ="users_links",
        $_joinTableId = $join_id,
        $_where = $where,
        $_order = $order,
        $_offset = $offset,
        $_limit = $limit,
        $_object = FALSE,
        $_onlyFirstRow = FALSE);
      $variables = array("object"=>"Manga", "order"=>$order, "id"=>$IDs);
      $this->GETObject($variables);
      $MALstatuses = array(
    		0 => "ERROR!",
    		1 => "Ongoing",
    		2 => "Finished",
    	);
      $statuses = array();
      if($ob == "manga") $statuses = array(
        0 => "ERROR!",
        1 => "Reading",
        2 => "Completed",
        3 => "On Hold",
        4 => "Dropped",
        6 => "Plan to Read"
      );
      elseif($ob == "anime") $statuses = array(
        0 => "ERROR!",
        1 => "Watching",
        2 => "Completed",
        3 => "On Hold",
        4 => "Dropped",
        6 => "Plan to Watch"
      );
      $this->DATA($MALstatuses, "MALstatuses");
      $this->DATA($statuses, "Statuses");
    }else ERROR("Getting mal failed!");
  }


}




?>
