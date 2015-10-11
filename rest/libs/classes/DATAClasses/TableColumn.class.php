<?php
class TableColumn
{
	private $_name = "";
	private $_title = "";
	private $_dataType = FALSE;
	private $_isLinkedValue = FALSE;
	private $_tds = array(
		"colspan" => "''",
		"rowspan" => "''"
		);
	private $_dataTypes = array(
		"image",
		"url",
		"euro",
		"number",
		"select",
		"hidden",
		"reference"
		);
	private $_references = array();
	
	function __construct($_name, $_title = FALSE, $_isLinkedValue = FALSE, $_references = FALSE, $_dataType = FALSE, $_tds = FALSE) 
	{
		if($_name)
		{
			$this->setName($_name);
			if($_title) $this->setTitle($_title);
			if($_isLinkedValue) $this->setIsLinkedValue(TRUE);
			if($_references) $this->setReferences($_references);
			if($_dataType) $this->setDataType($_dataType);
			if($_tds && is_array($_tds)) $this->setTds($_tds);
		}else return FALSE;
	}
	
	public function getName(){ return $this->_name; }
	public function setName($_name) { $this->_name = $_name; }
	
	public function getIdName() { return "element[".$this->getName()."]"; }
	
	public function IsLinkedValue(){ return $this->_isLinkedValue; }
	public function setIsLinkedValue($_isLinkedValue) { $this->_isLinkedValue = $_isLinkedValue; }
	
	public function getDataType(){ return $this->_dataType; }
	public function setDataType($_dataType) { $this->_dataType = ($_dataType && in_array($_dataType, $this->getDataTypes()))?$_dataType:FALSE ; }
	
	public function getDataTypes(){ return $this->_dataTypes; }
	
	public function getTitle(){ return $this->_title; }
	public function setTitle($_title) { $this->_title = $_title; }
	
	public function getReferences(){ return $this->_references; }
	public function setReferences($_references) { $this->_references = $_references; }
	
	public function getReference($_id) { return ($this->_references[$_id])?$this->_references[$_id]:FALSE; }
	
	public function getTd($_id){ return ($this->_tds[$_id])?$this->_tds[$_id]:FALSE; }
	public function setTd($_id, $_td) { $this->_tds[$_id] = "'".$_td."'"; }
	
	public function getTds(){ return $this->_tds; }
	public function setTds($_tds) { foreach($_tds as $_id => $_td) $this->setTd($_id, $_td); }
	
	public function TD() { return http_build_query($this->getTds()); }
	
}
?>