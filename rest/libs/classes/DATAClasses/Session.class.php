<?php
class Session
{
	public $user = false;
	public $lockedIn = false;
	public $tables = array();
	public $data = array();
	function __construct()
	{
		$this->clean();
	}

	function clean()
	{
		$this->user = false;
		$this->lockedIn = false;
		$this->tables = array();
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
		$this->clean();
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

	function getUserID()
	{
		if(is_object($this->user))
		{
			return $this->user->getID();
		}else return false;
	}

	function setData($name, $data)
	{
		$userid = $this->getUserID();
		if($userid && isset($name) && isset($data))
		{
			$this->data[$userid][$name] = $data;
			return true;
		}else return false;
	}
	function getData($name)
	{
		$userid = $this->getUserID();
		if(isset($name) && isset($this->data[$userid][$name]))
		{
			return $this->data[$userid][$name];
		}else return false;
	}
	function checkPage($_page){ return in_array($_page,$this->getPages()); }
}
?>
