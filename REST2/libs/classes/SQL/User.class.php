<?php
class User extends SQLClass
{
	public $gifts = FALSE;
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="username", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="password", $type=false, $_value="", $_size=255, $_empty=false);
		$this->addColumn($_name="group", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="data", $type="TXT");
		$this->_table = "users";
	}

	protected function Before_COMMIT()
	{
		$this->setValue("password",passwordHash($this->getValue("password")));
	}

	public function checkPassword($_password)
	{

		if($this->inDataspace())
		{
			return checkPassword($_password ,$this->getValue("password"));
		}else return FALSE;
	}

	public function SELECT_Gifts()
	{
		$this->gifts = SQL_SELECT(
      $_columns = FALSE,
      $_table="gifts",
      $_joinTable = "users_links",
      $_joinTableId = "gifts_id",
      $_where = "users_links.users_id='".$this->getID()."'",
      $_order = "users_links.gift_position ASC",
      $_offset = FALSE,
      $_limit = FALSE,
      $_object = "gift",
      $_onlyFirstRow = FALSE);
		return ($this->gifts)?TRUE:FALSE;
	}
}
?>
