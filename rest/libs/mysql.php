<?php
	function SQL_CONNECT()
	{
		$conn = new mysqli(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_DATASPACE);
		if ($conn->connect_error || !$conn->set_charset("utf8"))return FALSE;
		else return $conn;
	}

	function SQL_SEND_QUERY($_query)
	{
		$conn=SQL_CONNECT();
		if(isset($_query))
		{
			if($conn !== FALSE)
			{
				$return = $conn->query($_query);
			}else return FALSE;
		}else return FALSE;
		$conn->close();
		return $return;
	}

	function SQL_QUERY($_conn, $_query)
	{
		if(isset($_query) && is_object($_conn))
		{
			if($_conn !== FALSE)
			{
				$return = $_conn->query($_query);
			}else return FALSE;
		}else return FALSE;
		return $return;
	}

	function SQL_QUERYS($_conn, $_querys)
	{
		if(is_array($_querys) && is_object($_conn))
		{
			if($_conn !== FALSE)
			{
				$return = TRUE;
				try
				{
					$_conn->beginTransaction();
					foreach($_querys as $query)
					{
						$_conn->query($query);
					}
				}
				catch (Exception $e)
				{
					$_conn->rollback();
					$return = FALSE;
				}
			}else return FALSE;
		}else return FALSE;
		if($return !== FALSE) $_conn->commit();
		return $return;
	}

	function SQL_INSERT($_table, $_values)
	{
		if(is_array($_values) && isset($_table))
		{
			$conn=SQL_CONNECT();
			$query = "INSERT INTO `" . SQL_CLEAN_VALUE($conn, $_table) . "`";
			$colums = array();
			$values = array();
			foreach($_values as $colum => $value)
			{
				$colums[] = "`".SQL_CLEAN_VALUE($conn, $colum)."`";
				$values[] = "'" . SQL_CLEAN_VALUE($conn, $value) . "'";
			}
			$query .= "(" . implode(", ",$colums) . ") VALUES (" . implode(", ",$values) . ")";
			$id = SQL_QUERY($conn, $query)?$conn->insert_id:FALSE;
			$conn->close();
			return $id;
		}else return FALSE;
	}

	function SQL_DELETE_ID($_table, $_id)
	{
		if(isset($_id) && isset($_table))
		{
			$conn=SQL_CONNECT();
			$query = "DELETE FROM " . SQL_CLEAN_VALUE($conn, $_table) . " WHERE id='" . SQL_CLEAN_VALUE($conn, $_id) . "'";
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			return $result;
		}else return FALSE;
	}

	function SQL_DELETE_WHERE($_table, $_where)
	{
		if(isset($_where) && isset($_table))
		{
			$conn=SQL_CONNECT();
			$query = "DELETE FROM " . SQL_CLEAN_VALUE($conn, $_table) . " WHERE " . $_where;
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			return $result;
		}else return FALSE;
	}

	function SQL_UPDATE_OR_ADD_NEW($_table, $_values, $_id = FALSE)
	{
		if(is_array($_values) && isset($_table))
		{
			if($_id) return SQL_UPDATE_ID($_table, $_values, $_id);
			else return SQL_INSERT($_table, $_values);
		}else return FALSE;
	}

	function SQL_UPDATE_ID($_table, $_values, $_id)
	{
		if(is_array($_values) && isset($_table) && isset($_id))
		{
			$conn=SQL_CONNECT();
			$query = "UPDATE " . SQL_CLEAN_VALUE($conn, $_table) . " ";
			$colums_values = array();
			foreach($_values as $colum => $value)
			{
				$colums_values[] = SQL_CLEAN_VALUE($conn, $colum) . "='" . SQL_CLEAN_VALUE($conn, $value) . "'";
			}
			$query .= "SET " . implode(",",$colums_values) . " WHERE id='" . SQL_CLEAN_VALUE($conn, $_id) . "'";
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			return ($result)?$_id:FALSE;
		}else return FALSE;
	}

	function SQL_UPDATE_WHERE($_table, $_values, $_where)
	{
		if(is_array($_values) && isset($_table) && isset($_where))
		{
			$conn=SQL_CONNECT();
			$query = "UPDATE " . SQL_CLEAN_VALUE($conn, $_table) . " ";
			$colums_values = array();
			foreach($_values as $colum => $value)
			{
				$colums_values[] = SQL_CLEAN_VALUE($conn, $colum) . "='" . SQL_CLEAN_VALUE($conn, $value) . "'";
			}
			$query .= "SET " . implode(",",$colums_values) . " WHERE " . $_where;
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			return $result;
		}else return FALSE;
	}

	function SQL_SELECT($_columns = FALSE, $_table, $_joinTable = FALSE, $_joinTableId = FALSE, $_where = FALSE, $_order = FALSE, $_offset = FALSE, $_limit = FALSE, $_object = FALSE, $_onlyFirstRow = FALSE)
	{
		if(isset($_table))
		{
			$conn=SQL_CONNECT();
			$tab = SQL_CLEAN_VALUE($conn, $_table);
			$columns = "SELECT ".(($_object)?$tab.".id ":(($_columns)?$tab.".id, ".SQL_CLEAN_VALUE($conn, $_columns)." ":"* "));
			$table = "FROM ".$tab." ";
			$join = ($_joinTable && $_joinTableId)?"INNER JOIN ".SQL_CLEAN_VALUE($conn, $_joinTable)." ON ".$tab.".id=".SQL_CLEAN_VALUE($conn, $_joinTable).".".SQL_CLEAN_VALUE($conn, $_joinTableId)." ":"";
			$where = "";
			if($_where)
			{
				$where = "WHERE ";
				if(is_array($_where))
				{
					$whereColumns = array();
					foreach($_where as $column => $value) $where.= SQL_CLEAN_VALUE($conn, $column)."='".SQL_CLEAN_VALUE($conn, $value)."' AND ";
					$where = rtrim($where, "AND ");
				}
				else $where .= $_where." ";
			}
			$order = ($_order)?"ORDER BY ".SQL_CLEAN_VALUE($conn, $_order)." ":"";
			$limit = ($_limit)?"LIMIT ".SQL_CLEAN_VALUE($conn, $_limit)." ":"";
			$offset = ($_offset)?"OFFSET ".SQL_CLEAN_VALUE($conn, $_offset)." ":"";

			$query = $columns.$table.$join.$where.$order.$limit.$offset;
			//die($query);
			//debug($query);
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if($result)
			{
				if($result->num_rows <= 0) return array();
			}else return FALSE;
		}else return FALSE;

		$return = array();

		$onlyOneColumn = FALSE;
		$all = FALSE;

		if(!$_columns || stringContains("*", $_columns)) $all = TRUE;
		elseif(!is_array($_columns))
		{
			$_columns = explode(",", trim($_columns));
		}

		if(!$all && is_array($_columns) && count($_columns) === 1)
		{
			reset($_columns);
			$onlyOneColumn = current($_columns);
		}

		if($_onlyFirstRow)
		{
			if($onlyOneColumn) $return = $result->fetch_assoc()[$onlyOneColumn];
			else $return = $result->fetch_assoc();
		}
		elseif($_object)
		{
			$ids = array();
			while($row = $result->fetch_assoc())
			{
				$id = $row["id"];
				$ids[$id] = $id;
			}
			$return = IDstoObject($ids, $_object);
		}
		else
		{
			while($row = $result->fetch_assoc())
			{
				$id = $row["id"];
				if($all) $return[$id] = $row;
				elseif($onlyOneColumn) $return[$id] = $row[$onlyOneColumn];
				else foreach($_columns as $column) $return[$id][$column] = $row[$column];
			}
		}

		return $return;
	}

	function SQL_SELECT_ID($_table, $_id)
	{
		if(isset($_table) && isset($_id))
		{
			$conn=SQL_CONNECT();
			$query = "SELECT * FROM " . SQL_CLEAN_VALUE($conn, $_table) . " WHERE id='" . SQL_CLEAN_VALUE($conn, $_id) . "'";
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if ($result->num_rows <= 0) return false;
		}else return FALSE;
		return $result->fetch_assoc();
	}

	function SQL_SELECT_IDS($_table, $_where=FALSE)
	{
		if(isset($_table))
		{
			return ($_where)?SQL_SELECT_COLUMN($_table, "id", $_where):SQL_SELECT_COLUMN($_table, "id");
		}else return FALSE;
	}

	function SQL_SELECT_VALUE_WHERE($_table, $_column, $_where)
	{
		if(isset($_table) && isset($_column) && isset($_where))
		{
			$conn=SQL_CONNECT();
			$query = "SELECT ".SQL_CLEAN_VALUE($conn, $_column)." FROM " . SQL_CLEAN_VALUE($conn, $_table) . " WHERE " . $_where;
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if ($result->num_rows <= 0) return false;
			$value = $result->fetch_assoc()[$_column];
			return $value?$value:FALSE;
		}else return FALSE;
	}

	function SQL_SELECT_VALUE_SEARCH($_table, $_column, $_searchColumn, $_searchValue)
	{
		if(isset($_table) && isset($_column) && isset($_searchColumn) && isset($_searchValue))
		{
			$conn=SQL_CONNECT();
			$query = "SELECT ".SQL_CLEAN_VALUE($conn, $_column)." FROM ".SQL_CLEAN_VALUE($conn, $_table)." WHERE ".SQL_CLEAN_VALUE($conn, $_searchColumn)." = '".SQL_CLEAN_VALUE($conn, $_searchValue)."'";
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if ($result->num_rows <= 0) return false;
			$value = $result->fetch_assoc()[$_column];
			return $value?$value:FALSE;
		}else return FALSE;
	}

	function SQL_SELECT_VALUE_ID($_table, $_column, $_id)
	{
		if(isset($_table) && isset($_column) && isset($_id))
		{
			$conn=SQL_CONNECT();
			$query = "SELECT ".SQL_CLEAN_VALUE($conn, $_column)." FROM ".SQL_CLEAN_VALUE($conn, $_table)." WHERE id='".$_id."'";
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if ($result->num_rows <= 0) return false;
			$value = $result->fetch_assoc()[$_column];
			return $value?$value:FALSE;
		}else return FALSE;
	}

	function SQL_SELECT_OBJECTS($_table, $_where=FALSE)
	{
		$objects = array();
		if(isset($_table))
		{
			$ids = ($_where)?SQL_SELECT_IDS($_table, $_where):SQL_SELECT_IDS($_table);
			$object = classAndTable($_table, "table");
			if(!$object) return FALSE;
		}else return FALSE;
		return IDstoObject($ids, $object);
	}

	function SQL_SELECT_COLUMN($_table, $_column, $_where=FALSE)
	{
		if(isset($_table) && isset($_column))
		{
			$values = array();
			$conn = SQL_CONNECT();
			$query = "SELECT id," . SQL_CLEAN_VALUE($conn,$_column) . " FROM " . SQL_CLEAN_VALUE($conn, $_table);
			if($_where) $query .= " WHERE " . $_where;
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if (is_object($result) && $result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
				{
					$values[$row["id"]] = $row[$_column];
				}
			}else return FALSE;
		}else return FALSE;
		return $values;
	}

	function SQL_GET_NEXT_ID($_table)
	{
		if(isset($_table))
		{
			$conn=SQL_CONNECT();
			$query = "SELECT MAX(id) FROM " . SQL_CLEAN_VALUE($conn, $_table);
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if ($result->num_rows <= 0) return false;
			$id = (int)$result->fetch_assoc()["MAX(id)"];
			if($id == NULL) $id = 0;
			return $id + 1;
		}else return FALSE;
	}

	function SQL_SELECT_DISTINCT($_table, $_column, $_where=FALSE)
	{
		if(isset($_table) && isset($_column))
		{
			$values = array();
			$conn = SQL_CONNECT();
			$query = "SELECT DISTINCT " . $_column . " FROM " . SQL_CLEAN_VALUE($conn, $_table);
			if($_where) $query .= " WHERE " . $_where;
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if ($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
				{
					$values[] = $row[$_column];
				}
			}else return FALSE;
		}else return FALSE;
		return $values;
	}

	function SQL_CONTAINS_WHERE($_table, $_where)
	{
		if(isset($_table) && isset($_where))
		{
			$conn = SQL_CONNECT();
			$query = "SELECT * FROM " . SQL_CLEAN_VALUE($conn, $_table) . " WHERE " . $_where;
			$result = SQL_QUERY($conn, $query);
			$conn->close();
		}else return FALSE;
		return ($result != FALSE && $result->num_rows > 0);
	}

	function SQL_GET_ID($_table, $_where)
	{
		if(isset($_table) && isset($_where))
		{
			$conn=SQL_CONNECT();
			$query = "SELECT id FROM " . SQL_CLEAN_VALUE($conn, $_table) . " WHERE " . $_where;
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if ($result->num_rows <= 0) return false;
		}else return FALSE;
		$id = $result->fetch_assoc()["id"];
		return is_numeric($id)?$id:FALSE;
	}

	function SQL_CLEAN_VALUE($_conn, $_string)
	{
		if(isset($_string) && is_object($_conn))
		{
			return $_conn->real_escape_string($_string);
		}else return FALSE;
	}

	function SQL_COUNT_ROWS($_table, $_where, $_joinTable = false, $_joinTableId= false)
	{
		if(isset($_table) && isset($_where))
		{
			$conn=SQL_CONNECT();
			$join = ($_joinTable && $_joinTableId)?" INNER JOIN ".SQL_CLEAN_VALUE($conn, $_joinTable)." ON ".$_table.".id=".SQL_CLEAN_VALUE($conn, $_joinTable).".".SQL_CLEAN_VALUE($conn, $_joinTableId):"";
			$query = "SELECT COUNT(".$_table.".id) FROM " . SQL_CLEAN_VALUE($conn, $_table) . $join . " WHERE " . $_where;
			$result = SQL_QUERY($conn, $query);
			$conn->close();
			if ($result->num_rows <= 0) return FALSE;
		}else return FALSE;
		$count = $result->fetch_assoc()["COUNT(".$_table.".id)"];
		return is_numeric($count)?$count:FALSE;
	}
?>
