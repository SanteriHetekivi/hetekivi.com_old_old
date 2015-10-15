<?php
  function toArrayJson($data)
  {
    $return = array();
    if(is_array($data))
    {
      foreach ($data as $key=>$val ){
          $return[] = $data[$key];
      }
    }
    return $return;
  }
  function getUser() { return $_SESSION["session"]->user; }
  function getUserID()
	{
		$user = getUser();
		return ($user)?$user->getID():FALSE;
	}
  function checkLogin()
	{
		$session=&$_SESSION["session"];
		if(is_object($session)) return $session->checkLogin();
		else return false;
	}

  function logout()
	{
		$session=&$_SESSION["session"];
		if(is_object($session)) return $session->Logout();
		else return false;
	}
  function toMysqlDateTime($_dateTime){ return date("Y-m-d H:i:s", $_dateTime); }

	function clean($_string)
	{
		// Replace non-AllLet characters with space
		$_string = preg_replace("/[^A-Za-z]/", ' ', $_string);
		// Replace Multiple spaces with single space
		$_string = preg_replace('/ +/', ' ', $_string);
		// Trim the string of leading/trailing space
		return strtolower(trim($_string));
	}

	function passwordHash($_password)
	{
		//return password_hash(SALT2.$_password.SALT1, PASSWORD_DEFAULT);
		return sha1(SALT2.$_password.SALT1);
	}

	function checkPassword($_password,$_hashedPassword)
	{
		//return password_verify(SALT2.$_password.SALT1 , $_hashedPassword);
		//die((sha1(SALT2.$_password.SALT1) === $_hashedPassword));
		return (sha1(SALT2.$_password.SALT1) === $_hashedPassword);
	}
  function euro($_value = false){ return (is_numeric($_value))?number_format($_value,2,',',' '):FALSE; }

  function euroString($number){ return euro($number)." &euro;"; }

  function stringContains($word, $string){ return (stripos($string, $word) !== FALSE); }

  function IDstoObject($ids, $object)
	{
		if($ids && $object && is_array($ids))
		{
			$objects = array();
			foreach($ids as $id)
			{
				$objects[$id] = getObjectByName($object, $id);
				if(!$objects[$id]) return FALSE;
			}
		}else return FALSE;
		return $objects;
	}
  function malStatus($id, $mal_or_user="user", $manga_or_anime="manga")
  {
    if(isset($id))
    {
      $statuses = array();
      if($mal_or_user == "mal") $statuses = array(
    		0 => "ERROR!",
    		1 => "Ongoing",
    		2 => "Finished",
    	);
      elseif($manga_or_anime == "manga") $statuses = array(
        0 => "ERROR!",
        1 => "Reading",
        2 => "Completed",
        3 => "On Hold",
        4 => "Dropped",
        6 => "Plan to Read"
      );
      elseif($manga_or_anime == "anime") $statuses = array(
        0 => "ERROR!",
        1 => "Watching",
        2 => "Completed",
        3 => "On Hold",
        4 => "Dropped",
        6 => "Plan to Watch"
      );
      if(isset($statuses[$id])) return $statuses[$id];
      else return $statuses;
    }
    return false;
  }
  function getSessionData($name=false)
  {
    $session=$_SESSION["session"];
    return (is_object($session))?$session->getData($name):FALSE;
  }
  function setSessionData($name, $data)
  {
    $session=&$_SESSION["session"];
    return (is_object($session))?$session->setData($name, $data):FALSE;
  }
  function getMangaImage($_link,$_mangaPage)
	{
		$imageUrl = "https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/600px-No_image_available.svg.png";
		if($_mangaPage=="Batoto") $query = "//img[starts-with(@src,'http://img.bato.to/forums/uploads/7')]";
		elseif($_mangaPage=="MangaFox")$query ="//img[starts-with(@src,'http://a.mfcdn.net/store/manga')]";
		else return $imageUrl;

		libxml_use_internal_errors(true);
		$mangaPage = new DOMDocument();
		$mangaPage->loadHTMLFile($_link);
		$xpath = new DOMXPath($mangaPage);
		$image = $xpath->query($query);
		$imageUrl = $image->item(0)->getAttribute('src');
    return $imageUrl;
	}
?>
