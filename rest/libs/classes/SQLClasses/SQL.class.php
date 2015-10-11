<?php
class SQLClass
{
	protected $_columns = array();
	protected $_table;
	protected $_new = false;
	protected $_userLink = false;

	public function __construct($_values, $_pass_to_intiliazing=FALSE)
	{
		$this->initializeValues($_pass_to_intiliazing);
		if(isset($_values) && $_values)
		{
			if(isset($_values["id"]) && is_numeric($_values["id"]) && $_values["id"] != 0)
			{
				$this->SELECT($_values["id"]);
				$this->setValues($_values);
			}
			elseif(is_numeric($_values) && $_values != 0) $this->SELECT($_values);
			else
			{
				$this->setValues($_values);
				$this->setNewId();
			}
		}
		else $this->setNewId();
		$this->SELECT_LINK();
		$this->AfterCONSTRUCT();
	}

	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		return TRUE;
	}

	protected function AfterCONSTRUCT()
	{
		return true;
	}

	protected function addColumn($_name, $_type=FALSE, $_value=FALSE, $_size=FALSE, $_empty=false)
	{
		if($_name)
		{
			if($_name == "id" || $_type == "id") 			$this->_columns[$_name] = new Column(0,10,false);
			elseif($_type == "id_empty") 					$this->_columns[$_name] = new Column(0,10,true);
			elseif($_type == "boolean") 					$this->_columns[$_name] = new Column(0,1,true);
			elseif($_name == "title" || $_type == "title") 	$this->_columns[$_name] = new Column("",100,false);
			elseif($_type == "url") 						$this->_columns[$_name] = new Column("",500,true);
			elseif($_type == "datetime") 					$this->_columns[$_name] = new Column(toMysqlDateTime(0),500,true);
			elseif($_value !== FALSE && $_size !== FALSE)	$this->_columns[$_name] = new Column($_value,$_size,$_empty);
			else return FALSE;
		}else return FALSE;
		return TRUE;
	}

	public function getTable(){ return $this->_table; }


	public function setValue($_column, $_value)
	{
		if(isset($this->_columns[$_column])) $this->_columns[$_column]->setValue($_value);
	}

	public function getValue($_column)
	{
		return (isset($this->_columns[$_column]))?$this->_columns[$_column]->getValue():FALSE;
	}

	public function setLinkedValue($_column, $_value)
	{
		if($this->_userLink && $this->_userLink->_columns[$_column]) $this->_userLink->_columns[$_column]->setValue($_value);
	}

	public function getLinkedValue($_column)
	{
		return ($this->_userLink && $this->_userLink->_columns[$_column])?$this->_userLink->_columns[$_column]->getValue():FALSE;
	}

	public function getID() { return $this->getValue("id"); }

	public function getLinkedID() { return $this->getLinkedValue("id"); }

	public function setValues($_values)
	{
		foreach($_values as $column => $value) $this->setValue($column, $value);
	}

	public function SELECT_LINK()
	{
		$userID = getUserID();
		$table = $this->getTable();
		if($userID && ($table==="mangas" || $table==="animes" || $table==="gifts"))
		{
			$id = $this->getID();
			$linkID = SQL_GET_ID("users_links", $table."_id='".$id."' AND users_id='".$userID."'");
			$this->_userLink = new UserLink($linkID);
			$this->setLinkedValue("users_id", $userID);
		}else return FALSE;
	}

	private function getAllLinks()
	{
		return SQL_SELECT_OBJECTS($_table = "users_links", $_where = $this->getTable()."_id='".$this->getID()."'");
	}

	public function getValues()
	{
		$values = array();
		foreach($this->_columns as $name => $column) $values[$name] = $column->getValue();
		return $values;
	}

	public function getMaxSize($_column)
	{
		return ($this->_columns[$_column]->getSize())?$this->_columns[$_column]->getSize():false;
	}

	protected function BeforeCOMMIT()
	{
		return true;
	}

	private function CHECK()
	{
		foreach($this->_columns as $_column)
		{
			if(!$_column->check()) return FALSE;
		}
		return TRUE;
	}

	public function COMMIT()
	{
		$this->BeforeCOMMIT();
		if($this->CHECK())
		{
			$id = ($this->isOld())?$this->getID():FALSE;
			$id = SQL_UPDATE_OR_ADD_NEW($this->getTable(), $this->getValues(), $id);

			if($id)
			{
				$this->SELECT($id);
				if($this->_userLink)
				{
					$this->setLinkedValue($this->getTable()."_id", $id);
					$commit = $this->_userLink->COMMIT();
					if(!$commit) return FALSE;
				}
				return $this->AfterCOMMIT();
			}else return FALSE;
		}else return FALSE;
	}

	protected function AfterCOMMIT()
	{
		return true;
	}

	public function REMOVE()
	{
		if(SQL_DELETE_ID($_table=$this->getTable(), $_id=$this->getValue("id")))
		{
			$userLinks = $this->getAllLinks();
			foreach($userLinks as $userLink) $userLink->REMOVE();
			return $this->AfterREMOVE();
		}else return FALSE;
	}

	protected function AfterREMOVE()
	{
		return true;
	}

	public function SELECT($_id = FALSE)
	{
		$id = ($_id)?$_id:$this->getID();
		$values = SQL_SELECT_ID($this->getTable(), $id);
		if(is_array($values)) $this->setValues($values);
	}

	public function isOld()
	{
		return SQL_CONTAINS_WHERE($_table=$this->getTable(), $_where="id='" . $this->getValue("id") . "'");
	}

	public function setNewId()
	{
		$id = SQL_GET_NEXT_ID($this->getTable());
		if(is_numeric($id)) $this->setValue("id", $id);
	}

	public function inDataspace()
	{
		$where = "";
		$valuePairs = array();
		foreach($this->getValues() as $column => $value)
		{
			$valuePairs[] = "`". $column . "`='" . $value . "'";
		}
		$where = implode(" AND ",$valuePairs);
		return SQL_CONTAINS_WHERE($_table=$this->getTable(), $_where=$where);
	}

}
?>
