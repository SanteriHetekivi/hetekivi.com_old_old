<?php

  /**
   *
   */
  class Auth
  {
    public $user = false;
    private $data = false;

    function __construct()
    {
      $this->isLogged = false;
      $this->user = false;
      $this->data = false;
    }

    public function LOGIN($username, $password)
    {
      $userid = SQL_GET_ID($_table="users", $_where="username='".$username."'");
      if(is_numeric($userid))
  		{
  			$mUser = new User($userid);
  			if($mUser->checkPassword($password))
  			{
  				$this->user = $mUser;
  				$this->data = json_decode($mUser->getValue("data"));
  			}else return FALSE;
  		}else return FALSE;
		  return TRUE;
    }

    public function LOGOUT($username, $password)
    {
      $this->isLogged = false;
      $this->user = false;
      $this->data = false;
      return TRUE;
    }

    public function CHECK()
    {
      $user = $this->user;
      return (isset($user) && $user && is_object($user) && $user->getID() > 0);
    }

  }




?>
