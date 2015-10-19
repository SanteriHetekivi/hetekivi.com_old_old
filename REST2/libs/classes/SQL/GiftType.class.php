<?php
class GiftType extends SQLClass
{
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="title");
		$this->_table = "gifts_types";
	}
}
?>