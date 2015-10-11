<?php
  function runFunction($function, $variables)
  {
    $data = "";
    switch ($function) {
      case 'giftList':
        $data=giftList();
        break;
      case 'login':
        $data=login();
        break;
      case 'logout':
        if(logout()) BACK();
        else ERROR("Logout failed!");
        break;
      default:
        return false;
        break;
    }
    echo json_encode($data);
    return true;
  }

  function giftList()
  {
    $user=$_SESSION["session"]->user;
    if(is_object($user) && $user->SELECT_Gifts())
    {

      $gifts = $user->gifts;
      if(is_array($gifts))
      {

        $giftTypes = SQL_getGiftTypes();
        $return = array();
        foreach ($gifts as $key => $gift) {
          $typeId = $gift->getValue("gifts_types_id");
          $return[] = array(
            "position"  => $gift->getLinkedValue("gift_position"),
            "image"     => $gift->getValue("image"),
            "title"     => $gift->getValue("title"),
            "url"       => $gift->getValue("url"),
            "type"      => (isset($giftTypes[$typeId]))?$giftTypes[$typeId]:"",
            "price"     => euroString($gift->getValue("price"))
          );
        }
      }else ERROR("Getting gifts failed!");
    }else ERROR("Getting gifts failed!");
    return $return;
  }

  function giftTypes()
  {
    return
    $gifts = SQL_SELECT(
      $_columns = FALSE,
      $_table="gifts_types",
      $_joinTable = FALSE,
      $_joinTableId = FALSE,
      $_where = FALSE,
      $_order = FALSE,
      $_offset = FALSE,
      $_limit = FALSE,
      $_object = FALSE,
      $_onlyFirstRow = FALSE);
  }

  function login()
  {
    return checkLogin();
  }
?>
