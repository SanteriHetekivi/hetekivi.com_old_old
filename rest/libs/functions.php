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
?>
