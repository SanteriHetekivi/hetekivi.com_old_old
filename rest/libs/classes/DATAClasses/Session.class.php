<?php
class Session
{
	public $user = false;
	public $lockedIn = false;
	public $tables = array();

	function __construct()
	{
		$lockedIn = false;
	}

	function Login($_usename,$_password)
	{
		$userid = SQL_GET_ID($_table="users", $_where="username='".$_usename."'");
		if(is_numeric($userid))
		{
			$mUser = new User($userid);
			if($mUser->checkPassword($_password))
			{
				$this->user = $mUser;
				$this->lockedIn = true;
				$this->pages = array("HOME"=>"home","MANGA SEARCH"=>"mangaSearch","GIFT LIST" => "giftList","LOGOUT"=>"logout");
			}else return FALSE;
		}else return FALSE;
		return TRUE;
	}

	function Logout()
	{
		$this->lockedIn = false;
		$this->user = NULL;
		$this->pages = array("HOME"=>"home","LOGIN"=>"login");
		return true;
	}

	function checkLogin()
	{
		return ($this->lockedIn && is_object($this->user));
	}

	function getPages()
	{
		$pages = array(
			"HOME"=>"home",
			"GIFT LIST"=>"giftList",
			"LOGIN"=>"login"
			);
		if($this->checkLogin())
		{
			if($this->user->getValue("group") == "admin")
			{
				$pages = array(
					"HOME"=>"home",
					"MAL"=>"mal",
					"MANGA SEARCH"=>"mangaSearch",
					"GIFT LIST"=>"giftList",
					"LOGOUT"=>"logout"
				);
			}
			else
			{
				$pages = array(
					"HOME"=>"home",
					"MAL"=>"mal",
					"MANGA SEARCH"=>"mangaSearch",
					"GIFT LIST"=>"giftList",
					"LOGOUT"=>"logout"
				);
			}
		}
		return $pages;
	}

	function checkPage($_page){ return in_array($_page,$this->getPages()); }
}
?>
