<?php
class Column
{
	private $_value;
	private $_size;
	private $_empty;
	
	public function __construct($value, $size, $empty) 
	{
		if($this->checkAll($value, $size, $empty))
		{
			$this->setSize($size);
			$this->setEmpty($empty);
			$this->setValue($value);
		}
	}
	
	public function setValue($value){if($this->checkValue($value)) $this->_value = $value;}
	public function getValue(){return $this->_value;}
	
	public function setSize($size){if($this->checkSize($size)) $this->_size = $size;}
	public function getSize(){return $this->_size;}
	
	public function setEmpty($empty){if($this->checkEmpty($empty))$this->_empty = $empty;}
	public function getEmpty(){return $this->_empty;}
	
	public function check()
	{
		$value = $this->getValue();
		$size = $this->getSize();
		$empty = $this->getEmpty();
		if($this->checkAll($value, $size, $empty))
		{
			return (strlen((string)$value) <= $size) && ($empty || (!$empty && !empty($value)));
		}
	}
	
	private function checkAll($value, $size, $empty)
	{
		return ($this->checkValue($value) && $this->checkSize($size)  && $this->checkEmpty($empty));
	}
	
	private function checkSize($size)
	{
		return (isset($size) && is_int($size));
	}
	
	private function checkEmpty($empty)
	{
		return (isset($empty) && is_bool($empty));
	}
	
	private function checkValue($value)
	{
		return isset($value);
	}
	
}
?>