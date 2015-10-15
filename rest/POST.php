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
      case 'remove':
        if(isset($variables["id"]))
        {
          $object = new UserLink($variables["id"]);
          die($object->REMOVE());
        }
        else ERROR("No ID given!");
        break;
      case 'edit':
        die(edit($variables));
        break;
      case 'giftList':
        die(giftList($variables));
        break;
      case 'uploadMAL':
        if(!uploadMAL()) ERROR("Upload failed!");
        elseif(!SQL_loadMyanimelistXMLToDataspace()) ERROR("MAL update failed!");
        else MESSAGE("Update completed!");
        break;
      default:
        ERROR("Function named ".$function." does not exist!");
        break;
    }
    BACK();
    return true;
  }

  function edit($variables)
  {

    if(isset($variables["object"]))
    {
      $id = 0;
      $ob = $variables["object"];
      unset($variables["object"]);
      if(isset($variables[$ob."s_id"]))
      {
        $id=$variables[$ob."s_id"];
        unset($variables[$ob."s_id"]);
        if(isset($variables["id"])) unset($variables["id"]);
        $object = getObjectByName($ob, $id);
        $link = $object->getUserLink();
      }
      if(is_object($object))
      {
        $objectColumns = $object->getColumnNames();
        $linkColumns = $link->getColumnNames();
        if(is_array($objectColumns) && is_array($linkColumns))
        {
          foreach ($variables as $col => $value) {
            if (in_array($col, $linkColumns)) $object->setLinkedValue($col, $value);
    				else if(in_array($col, $objectColumns)) $object->setValue($col, $value);
          }
          return $object->COMMIT();
        }
      }
    }
    return FALSE;
  }
  function uploadMAL()
  {
    //Needs file_uploads = On
    $filetype = pathinfo(TMP_XML_FILE_PATH,PATHINFO_EXTENSION);
    $return = FALSE;
    if($filetype == "xml" && $_FILES["fileToUpload"]["size"] < 1000000)
    {
      $return = move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], TMP_XML_FILE_PATH);
    }
    return $return;

  }
?>
