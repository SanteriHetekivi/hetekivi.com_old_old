<?php
class TableExtra
{
	private $_title = "";
	private $_name = "";
	private $_values = array();
	private $_onClick = "";
	private $_onChange = "";
	private $_extraTypes = array(
		"row_button",
		"edit_input"
		);
	private $_extraType = FALSE;
	private $_inputType = "select";
	
	function __construct($name, $extraType, $title = FALSE, $values = FALSE, $inputType = FALSE, $onClick = FALSE, $onChange = FALSE) 
	{
		if($name && $extraType)
		{
			$this->setExtraType($extraType);
			if($this->getExtraType())
			{
				$this->setName($name);
				if($title) $this->setTitle($title);
				if($values) $this->setValues($values);
				if($inputType) $this->setInputType($inputType);
				if($onClick) $this->setOnClick($onClick);
				if($onChange) $this->setOnChange($onChange);
			} return FALSE;
		}else return FALSE;
		
		return TRUE;
	}
	
	public function getName(){ return $this->_name; }
	public function setName($name) { $this->_name = $name; }
			
	public function getExtraType(){ return $this->_extraType; }
	public function setExtraType($extraType) { $this->_extraType = ($extraType && in_array($extraType, $this->getExtraTypes()))?$extraType:FALSE ; }
	
	public function getExtraTypes(){ return $this->_extraTypes; }
	
	public function getTitle(){ return $this->_title; }
	public function setTitle($title) { $this->_title = $title; }
	
	public function getValues(){ return $this->_values; }
	public function setValues($values) { $this->_values = ($values && is_array($values))?$values:array() ; }

	public function getOnClick(){ return $this->_onClick; }
	public function setOnClick($onClick) { $this->_onClick = $onClick; }
	
	public function getOnChange(){ return $this->_onChange; }
	public function setOnChange($onChange) { $this->_onChange = $onChange; }
	
	public function getInputType(){ return $this->_inputType; }
	public function setInputType($inputType) { $this->_inputType = $inputType; }
}
?>