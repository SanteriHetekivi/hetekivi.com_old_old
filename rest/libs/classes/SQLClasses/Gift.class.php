<?php
class Gift extends SQLClass
{
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="title");
		$this->addColumn($_name="gifts_types_id", $type="id");
		$this->addColumn($_name="price", $type=false, $_value=0.00, $_size=5, $_empty=true);
		$this->addColumn($_name="url", $type="url");
		$this->addColumn($_name="image", $type="url");	
		$this->_table = "gifts";
	}
	
	public function getPosition() { return $this->getLinkedValue("gift_position"); }
	
	public function setPosition($position) { return $this->setLinkedValue("gift_position", $position); }
	
	public function canShow() { return ($this->getLinkedValue("gift_show"))?TRUE:FALSE; } 
	
	public function motherID() { return $this->getLinkedValue("gift_mother_id"); } 
	
	public function getChildren()
	{
		return SQL_SELECT_OBJECTS($_table="gifts", $_where="id IN (SELECT gifts_id FROM users_links WHERE gifts_id > 0 AND users_id = '".getUserID()."' AND gift_mother_id='".$this->getID()."') ");
	}
	
	public function countChildren()
	{
		$childrenCount = SQL_COUNT_ROWS($_table="users_links", $_where=" gifts_id > 0 AND users_id = '".getUserID()."' AND gift_mother_id='".$this->getID()."'");
		return 	(is_numeric($childrenCount))?$childrenCount:FALSE;
	}
	
	public function hasChildren()
	{
		$childrenCount = $this->countChildren();
		return 	($childrenCount && $childrenCount > 0)?TRUE:FALSE;
	}
	
	public function isChildren(){ return ($this->motherID()>0); }
	
	public function getTypeTitle()
	{
		return SQL_SELECT_VALUE_ID($_table="gifts_types", $_column="title", $_id=$this->getValue("gifts_types_id"));
	}
}
?>