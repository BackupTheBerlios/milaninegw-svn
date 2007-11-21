<?
class cSqlCommand
{
	var $columnValues =array();
	
	function cSqlCommand($dbType = "mysql")
	{
	
	}
	
	function SetValuesViaConfig($formConfig, $formValues)
	{
		$this->columnValues = array();
		if(is_array($formConfig["fields"])) reset($formConfig["fields"]);
		
		while ( is_array($formConfig["fields"]) && list($list_block, $subConfig) = each($formConfig["fields"]))
			if($subConfig["disabled"] !== true)
			{
				if(isset($subConfig["DbField"]) && ($subConfig["control_type"] == "TXT" || $subConfig["control_type"] == "TXTA"))
				{
					$value = $formValues[ $subConfig["control_id"] ];
					if($subConfig["use_html_replace"] !== false) $value = htmlspecialchars($value);
					$this->columnValues[ $subConfig["DbField"] ] = addslashes($value);
				}
				
				if(isset($subConfig["DbField"]) && $subConfig["control_type"] == "CHK")
				{
					$this->columnValues[ $subConfig["DbField"] ] = $formValues[ $subConfig["control_id"] ] == "" ? 0 : $formValues[ $subConfig["control_id"] ];
				}
				if( isset($subConfig["DbConvert"]) && method_exists($this, $subConfig["DbConvert"])  )
				{
					$this->columnValues[ $subConfig["DbField"] ] = call_user_method($subConfig["DbConvert"], $this, $this->columnValues[ $subConfig["DbField"] ] );
				}
			}

		if(is_array($formConfig["lists"])) reset($formConfig["lists"]);
		while ( is_array($formConfig["lists"]) && list($list_block/*key*/, $subConfig/*value array*/) = each($formConfig["lists"]))
			if($subConfig["disabled"] !== true)
			{
				if(isset($subConfig["DbField"]) && ($subConfig["control_type"] == "DDL") )
				{
					$this->columnValues[ $subConfig["DbField"] ] = addslashes(htmlspecialchars($formValues[ $subConfig["control_id"] ]));
				}
				
				if(isset($subConfig["DbField"]) && ($subConfig["control_type"] == "MDDL") )
				{
					$this->columnValues[ $subConfig["DbField"] ] = addslashes(htmlspecialchars(implode(',', $formValues[ $subConfig["control_id"] ]) ));
				}
			}
	}
	
	function AddColumnValue($columnName, $columnValue)
	{
		$this->columnValues[ $columnName ] = addslashes(htmlspecialchars($columnValue));
	}
	
	function PrepareUpdateSQL($tableName, $idKey, $id)
	{
		reset($this->columnValues);
		$query = sprintf("update `%s` set ", $tableName);
		
		while ( list($key, $value) = each($this->columnValues) )
			$query .= sprintf(" `%s` = '%s',", $key, $value);
		//print $query;exit;
		$query = substr($query, 0, -1).sprintf(" where %s=%d", $idKey, $id);
		return $query;
	}
	
	function PrepareInsertSQL($tableName)
	{
		reset($this->columnValues);
		$query = sprintf("insert into `%s` (%s) values ('%s')", $tableName, implode(", ", array_keys($this->columnValues)) , implode( "', '", $this->columnValues ));
		return $query;
	}
	
	function ConvertToMysqlDate($date)
	{
		$date = split("/", $date);
		return sprintf("%s-%s-%s", $date[2], $date[1], $date[0]);
	}
}
?>