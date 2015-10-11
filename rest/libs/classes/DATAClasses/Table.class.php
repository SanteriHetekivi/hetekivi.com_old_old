<?php
class Table
{
	private $_columns = array();
	private $_extras = array();
	private $_mainHeaders = array();
	private $_object = array();
	private $_elements = array();
	private $_order = FALSE;
	private $_page = 1;
	private $_callPage = FALSE;
	private $_add = FALSE;
	private $_edit = FALSE;
	private $_Class = FALSE;
	
	function __construct($callPage, $_object, $_add = FALSE, $_edit = FALSE, $_page = FALSE, $_order = FALSE, $_mainHeaders = FALSE, $_columns = FALSE, $_elements = FALSE) 
	{
		if($callPage && $_object)
		{
			$this->setCallPage($callPage);
			$this->setObject($_object);
			if($_add) 			$this->setAdd($_add);
			if($_edit) 			$this->setEdit($_edit);
			if($_page) 			$this->setPage($_page);
			if($_order)			$this->setFullOrder($_order);
			if($_mainHeaders) 	$this->setPage($_mainHeaders);
			if($_columns) 		$this->setPage($_columns);
			if($_elements) 		$this->setPage($_elements);
		}else return FALSE;
		
	}
	
	
	public function UPDATE()
	{
		$page = (isset($_GET["p"]))?$_GET["p"]:FALSE;
		$order = (isset($_GET["o"]))?$_GET["o"]:FALSE;
		if($page) $this->setPage($_GET["p"]);
		elseif($order) $this->toggleOrder($_GET["o"]);
			
		$this->makeElements();
		//debug($this);
	}
	
	public function getElements(){ return $this->_elements; }
	public function setElements($_elements) { $this->_elements = $_elements; }
	
	public function getElement($_id){ return ($this->_elements[$_id])?$this->_elements[$_id]:FALSE; }
	public function setElement($_id, $_element) { $this->_elements[$_id] = $_element; }

	
	public function makeElements()
	{
		$object = $this->getObject();
		if($object) $object = strtolower($object);
		else return FALSE;

		$table = FALSE;
		if($object)
		{
			$table = classAndTable($object, "class");
			$joinTable = (hasUserLink($object))?"users_links":FALSE;
			if($joinTable)
			{
				$joinTableId = $table."_id";
				$where = array(
					$joinTable.".users_id" => getUserID()
				);
			}
			else
			{
				$joinTableId = FALSE;
				$where = FALSE;
			}
		}else return FALSE;
		
		$limit = 10;
		
		$page = $this->getPage();
		$offset = FALSE;
		if($page && is_numeric($page) && $page > 1)
		{
			$offset = ($page - 1)*$limit;
		}
		if($table)
		{
			$elements = SQL_SELECT($_columns = FALSE, $table, $joinTable, $joinTableId, $where, $this->getOrder(), $offset, $limit, $object, $_onlyFirstRow = FALSE);
			if($elements)
			{
				if(!is_array($elements)) $elements = array();
				$this->setElements($elements);
				return TRUE;
			}
			else return FALSE;
		}else return FALSE;
		
	}
	
	public function getColumn($_id){ return ($this->_columns[$_id])?$this->_columns[$_id]:FALSE; }
	public function setColumn($_id, $_column) { $this->_columns[$_id] = $_column; }
	
	public function getColumns(){ return $this->_columns; }
	public function setColumns($_columns) { $this->_columns = $_columns; }

	public function getColumnsNames()
	{
		$names = array();
		foreach($this->getColumns() as $id => $column) $names[$id] = $column->getName();
		return $names;
	}
	
	public function addColumn($_name, $_title = FALSE, $_isLinkedValue = FALSE, $_references = FALSE, $_dataType = FALSE, $_tds = FALSE) 
	{ 
		if($_dataType == "url") $this->addColumn("title", "Title", FALSE, FALSE, "hidden", FALSE);
		$this->_columns[$_name] = new TableColumn($_name, $_title, $_isLinkedValue, $_references, $_dataType,  $_tds); 
	}
	
	public function updateColumnReferences($_id, $_references)
	{
		if($this->_columns[$_id]) return $this->_columns[$_id]->setReferences($_references);
		else return false;
	}
	public function removeColumn($_id) { unset($this->_columns[$_id]); }
	
	public function getMainHeader($_id){ return ($this->_mainHeaders[$_id])?$this->_mainHeaders[$_id]:FALSE; }
	public function setMainHeader($_id, $_mainHeader) { $this->_mainHeaders[$_id] = $_mainHeader; }
	
	public function getMainHeaders(){ return $this->_mainHeaders; }
	public function setMainHeaders($_mainHeaders) { $this->_mainHeaders = $_mainHeaders; }

	public function addMainHeader($_id, $_name, $_title = FALSE, $_tds = FALSE) { $this->_mainHeaders[$_id] = new TableColumn($_name, $_title, $_tds); }
	public function removeMainHeader($_id) { unset($this->_mainHeaders[$_id]); }

	public function getObject(){ return $this->_object; }
	public function setObject($_object) { $this->_object = $_object; }
	
	public function getOrder(){ return $this->_order; }
	public function setOrder($_name, $_order) { $this->_order = $_name." ".$_order; }
	public function setFullOrder($_order) { $this->_order = $_order; }
	public function toggleOrder($_name)
	{
		if($_name && !empty($_name) && in_array($_name, $this->getNames()))
		{
			$order = $this->getOrder();
			
			if(stringContains($_name, $order) && stringContains("DESC", $order)) $newOrder = "ASC";
			else $newOrder = "DESC";

			$this->setOrder($_name, $newOrder);
		}else return FALSE;
	}
	public function getOrderName(){ return strtok($this->getOrder(), " "); }
	public function getPage(){ return $this->_page; }
	public function setPage($_page) { $this->_page = ($_page && is_numeric($_page) && $_page > 1)?$_page:1; }
	
	public function getCallPage(){ return $this->_callPage; }
	public function setCallPage($_callPage) { $this->_callPage = $_callPage; }
	
	public function getEdit(){ return $this->_edit; }
	public function setEdit($_edit) { $this->_edit = $_edit; }
		
	public function getAdd(){ return $this->_add; }
	public function setAdd($_add) { $this->_add = $_add; }
	
	public function getNextPage() { return $this->getPage()+1; }
	public function getLastPage() { return $this->getPage()-1; }
	public function HTML()
	{
		$smarty = startSMARTY();
		
		$smarty->assign("table", $this);
		
		return $smarty->fetch("table.tpl");
	}
	
	public function getNames()
	{
		$names = array();
		$columns = $this->getColumns();
		if($columns && is_array($columns) && !empty($columns))
		{
			foreach($columns as $id => $column) $names[$id] = $column->getName();
		}
		return $names;
	}
	
	public function getColumnCount()
	{
		$count = 0;
		if($this->getEdit()) ++$count;
		$count += count($this->getExtrasByType("row_button"));
		$columns = $this->getColumns();
		if($columns && is_array($columns) && !empty($columns))
		{
			foreach($columns as $column) 
			{
				$colspan = $column->getTd("colspan");
				$count += ($colspan && is_numeric($colspan) && $colspan > 1)?$column->getTd("colspan"):1;
			}
		}
		return $count;
	}
	
	public function getNextUrl() { return "index.php?page=".$this->getCallPage()."&p=".$this->getNextPage()."&o=".$this->getOrderName(); }
	public function getLastUrl() { return "index.php?page=".$this->getCallPage()."&p=".$this->getLastPage()."&o=".$this->getOrderName(); }
	
	public function getOrderUrl($_name) { return "index.php?page=".$this->getCallPage()."&p=".$this->getPage()."&o=".$_name; }
	
	public function SAVE()
	{
		$element = ($_POST["element"])?$_POST["element"]:FALSE;

		if($element && isset($element["id"]))
		{
			$id = $element["id"];
			unset($element["id"]);
			$myElement = getObjectByName($this->getObject(), $id);
			
			$columnNames = array_map('strtolower',$this->getColumnsNames());

			if($myElement && $columnNames && is_array($columnNames))
			{
				foreach($element as $column => $value)
				{
					if(false !== $key = array_search(strtolower($column), $columnNames))
					{
						if(FALSE != $col = $this->getColumn($key))
						{
							if($col->IsLinkedValue()) $myElement->setLinkedValue($column, $value);
							else $myElement->setValue($column, $value);
						}else return FALSE;
					}else return FALSE;
				}
				$commit = $myElement->COMMIT();
				return ($commit)?$this->makeElements():FALSE;
			}
		}else return FALSE;
		
	}
	
	public function getExtra($_id){ return ($this->_extras[$_id])?$this->_extras[$_id]:FALSE; }
	public function setExtra($_id, $_extra) { $this->_extras[$_id] = $_extra; }
	
	public function getExtras(){ return $this->_extras; }
	public function setExtras($_extras) { $this->_extras = $_extras; }
	
	public function addExtra($name, $extraType, $title = FALSE, $values = FALSE, $inputType = FALSE, $onClick = FALSE, $onChange = FALSE) 
	{ $this->_extras[$name] = new TableExtra($name, $extraType, $title, $values, $inputType, $onClick, $onChange); }
	
	public function getExtrasByType($type)
	{
		$extras = array();
		$allExtras = $this->getExtras();
		foreach($allExtras as $extra)
		{
			if($extra->getExtraType() == $type) $extras[] = $extra;
		}
		return $extras;
	}
	
	public function updateExtraValues($_id, $_values)
	{
		if($this->_extras[$_id]) return $this->_extras[$_id]->setValues($_values);
		else return false;
	}
	
	public function updateExtraScript($_id, $_type, $_script)
	{
		if($this->_extras[$_id] && $_type) 
		{
			if(strtolower($_type) == "onclick") return $this->_extras[$_id]->setOnClick($_script);
			elseif(strtolower($_type) == "onchange") return $this->_extras[$_id]->setOnChange($_script);
			else return FALSE;
		} 
		else return false;
	}
}
?>