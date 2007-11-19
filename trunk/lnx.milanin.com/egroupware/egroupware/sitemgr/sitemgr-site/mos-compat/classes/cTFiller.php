<?
/*
Version 	0.918
Modified: 	20/09/2006
Capital letter is a public function else private function, so don't use them.
*/
class cTFiller extends cTemplate
{
	var $defaults = array();
	var $errorsBlocks = array();
	
	function cTFiller($root = ".") {
		parent::cTemplate($root);
	}
	
	function GetActualValue($value)	{
		return ( get_magic_quotes_gpc() && !is_array($value) ) ? stripslashes($value) : $value;
	}
	
	function HasValidationErrors() {
		return (count($this->errorsBlocks) != 0);
	}
	
	function IsValidField($controlID) {
		return !( array_key_exists($controlID."_ErrReq", $this->errorsBlocks) || array_key_exists($controlID."_ErrRule", $this->errorsBlocks) );
	}
	
	function arrReset(&$array) {
		if(is_array($array)) reset($array);
	}
		
	function getCheckedValue($value, $default, $type, $checkedValue) {
		$result = "";
		$isChecked = ( $value == $default || ( is_array($default) && in_array($value, $default) ) );
		if($isChecked == true && ($type == "DDL" || $type == "MDDL") )
			$result = $checkedValue;
		return $result;
	}

	function fillList($data, $cfg) {
		if( !is_array($data) ) return;
		$this->arrReset($data);
		$result = array();
		$isColumn = false; 
		$j=1;
		if( isset($cfg["colCount"]) && is_numeric($cfg["colCount"]) ) $isColumn = true;
		
		$length = count($data);
		
		while (list($key, $value) = each($data)) {
			$key = ($cfg["use_key"] === true ? $key : $value);
			if(  !( is_array($cfg["exceptionKeys"]) && in_array($key, $cfg["exceptionKeys"]) !== false )   )
			{
				$beforeText = "";
				$afterText = "";
				
				if($isColumn && $length == count($data) ) $beforeText .= '<table class="'.$cfg['control_id'].'" cellpadding="0" cellspacing="0" border="0">';
				$length--;
				
				if( $isColumn )
				{
					if($j > $cfg["colCount"]) 
						$j = 1;
					if($j == 1) $beforeText .= "<tr>";
					if($j <= $cfg["colCount"]) $beforeText .= "<td>";
					$afterText .= "</td>";
					if($j == $cfg["colCount"]) $afterText .= "</tr>";
					if($length == 0)
						$afterText .= "</table>";
					$j++;
					
				}
				else
				{
					$afterText = "";
					$beforeText = "";
				}
				//RenderArrayTable($blockName, $array, $blockValues=array(), $onItemBind="", $onItemCreated="")
				array_push($result, array(	"Before"=>$beforeText, "After"=>$afterText, "VALUE"		=> htmlspecialchars($key),
														"TEXT"		=> ($cfg["use_html_replace"] === true ? htmlspecialchars($value) : $value), 
														"CHECKED" 	=> $this->getCheckedValue($key, $this->defaults[$cfg["control_id"]], $cfg["control_type"], $cfg["checked_value"])) );
			}
		}
		return $result;
	}
	
	function isTextField($type) {
		return ($type == "TXT" || $type == "TXTA" || $type == "PWD");
	}

	function FillBlockWithStaticValues($cfg, $blockName="", $staticValues = array(), $dbLink=null)
	{
		global $db;
		$blockValues = $staticValues;
		$lists = array();

		while ( is_array($cfg["fields"]) && list($field, $ctrlCfg) = each($cfg["fields"]))
		{
			if( $ctrlCfg["disabled"] === true) $blockValues[$ctrlCfg["control_id"]."_DISABLED"] = " disabled ";
			if( $this->isTextField($ctrlCfg["control_type"]) && $ctrlCfg["bindable"] !== false )
				$blockValues[$ctrlCfg["control_id"]."_VALUE"] = htmlspecialchars($this->defaults[$ctrlCfg["control_id"]]);
			//need to improve;
			if( $ctrlCfg["control_type"] == "CHK")
				$blockValues[$ctrlCfg["control_id"]."_CHECKED"] = ( $ctrlCfg["value_on"] == $this->defaults[$ctrlCfg["control_id"]] ) ? " checked=\"checked\"" : "";
		}
		if(is_array($cfg["lists"])) reset($cfg["lists"]);
		while ( is_array($cfg["lists"]) && list($cfgKey, $ctrlCfg) = each($cfg["lists"]))
		{
			$arr = array();
			if(is_array($ctrlCfg["source"]))
				$arr = $ctrlCfg["source"];
			elseif(function_exists ($ctrlCfg["source"]))
				$arr = call_user_func($ctrlCfg["source"]);
			elseif( strtolower(substr($ctrlCfg["source"], 0, 7)) == 'select ' && defined('DB_TYPE') )
				{
					if($dbLink != null)
					{
						if($res = mysql_query ($ctrlCfg["source"], $dbLink))
						while($rs = mysql_fetch_row($res))
							$arr[ $rs[0] ] = $rs[1];
					}
					else
					{
						if($res = $db->sql_query($ctrlCfg["source"]))
						while($rs = $db->sql_fetchrow($res))
							$arr[ $rs[0] ] = $rs[1];
					}
				}

			
			$lists[ $blockName.($blockName == "" ? "" : ".").$ctrlCfg["control_id"] ] = $this->FillList($arr, $ctrlCfg);
			if( $ctrlCfg["disabled"] === true )
				$blockValues[$ctrlCfg["control_id"]."_DISABLED"] = " disabled ";
		}
		
		//fill content.
		$blockName == "" ? $this->assign_vars($blockValues) : $this->assign_block_vars($blockName, $blockValues);
		while (list($boxName, $matrix) = each($lists))
			while (list($tmp, $values) = each($matrix))
				$this->assign_block_vars($boxName, $values);

		while( list($boxName, $errMes) = each($this->errorsBlocks) )
			$this->assign_block_vars($blockName.($blockName=="" ? "" : ".").$boxName, array("Message"=>$errMes));
	}





	/*********TABLE RENDER**********/
	function RenderDbTable($blockName, $resLink, $blockValues=array(), $onItemBind="", $onItemCreated="")
	{
		global $db;
		$this->assign_block_vars($blockName, $blockValues);
		$itemIndex = -1;
		while($rs = $db->sql_fetchrow($resLink))
		{
			$itemIndex++;
			if( function_exists ($onItemBind) )
				call_user_func($onItemBind, &$rs, $itemIndex, $blockValues);
			$this->assign_block_vars($blockName.".ROW", array_merge($rs, $blockValues) );
		}
		if($itemIndex == -1)
			$this->assign_block_vars($blockName.".EMPTY", $blockValues);
	}

	function RenderArrayTable($blockName, $array, $blockValues=array(), $onItemBind="", $onItemCreated="")
	{
		global $db;
		$this->assign_block_vars($blockName, $blockValues);
		$itemIndex = -1;
		$this->arrReset(&$array);
		while (list(, $value) = each($array)) {
		   	$itemIndex++;
			if( function_exists ($onItemBind) )
				call_user_func($onItemBind, &$value, $itemIndex, $blockValues);
			$this->assign_block_vars($blockName.".ROW", array_merge($value, $blockValues) );
		}
		
		if($itemIndex == -1)
			$this->assign_block_vars($blockName.".EMPTY", $blockValues);
	}








	/**REFACTOR PLEASE***/
	function SetDefaultsFromRecordSet($cfg, $rs)
	{
		
		if(is_array($cfg["lists"])) reset($cfg["lists"]);
		while ( is_array($cfg["lists"]) && list($list_block, $subConfig) = each($cfg["lists"]))
			$this->defaults[$subConfig["control_id"]] = isset( $rs[ $subConfig["DbField"] ] ) && $rs[ $subConfig["DbField"] ]!="0" ? $rs[ $subConfig["DbField"] ] : $subConfig["default_value"];
	
		if(is_array($cfg["fields"])) reset($cfg["fields"]);
		while ( is_array($cfg["fields"]) && list($list_block, $subConfig) = each($cfg["fields"]))
			$this->defaults[$subConfig["control_id"]] = isset( $rs[ $subConfig["DbField"] ] ) ?
								(function_exists ($subConfig["DbPageConvert"]) ? 
									call_user_func($subConfig["DbPageConvert"], $rs[ $subConfig["DbField"] ]) : $rs[ $subConfig["DbField"] ] ) 
																							  : $subConfig["default_value"];
	}
	//collection posted data according to the config file.
	function CollectPostedData($cfg, $isPostBack = false, $isGetMethod = true)
	{
		$posted_data = ($isGetMethod) ? $_GET : $_POST;
		if(is_array($cfg["lists"])) reset($cfg["lists"]);
		while ( is_array($cfg["lists"]) && list($list_block, $subConfig) = each($cfg["lists"]))
			if($subConfig["disabled"] !== true)
				$this->defaults[$subConfig["control_id"]] = $isPostBack ? $this->GetActualValue($posted_data[$subConfig["control_id"]]) : $subConfig["default_value"];
		
		if(is_array($cfg["fields"])) reset($cfg["fields"]);
		while ( is_array($cfg["fields"]) && list($list_block, $subConfig) = each($cfg["fields"]))
			if($subConfig["disabled"] !== true)
				$this->defaults[$subConfig["control_id"]] = $isPostBack ? $this->GetActualValue($posted_data[$subConfig["control_id"]]) : $subConfig["default_value"];
	}

	function ValidatePostedData($cfg, $isPostBack=false)
	{
		
		if(!$isPostBack) return;
		
		$this->errorsBlocks = array();
		if(is_array($cfg["fields"])) reset($cfg["fields"]);
		while ( is_array($cfg["fields"]) && list($list_block/*key*/, $subConfig/*value array*/) = each($cfg["fields"]))
		{
			if($subConfig["control_type"] == "CHK" || $subConfig["control_type"] == "TXT" || $subConfig["control_type"] == "TXTA" || $subConfig["control_type"] == "PWD")
			{
				if($subConfig["required"] === true && trim($this->defaults[$subConfig["control_id"]]) == "")
				{
					$this->errorsBlocks[$subConfig["control_id"]."_ErrReq"] =
						isset($subConfig["required_message"]) ? $subConfig["required_message"] : "";
				}
				elseif( count($subConfig["validatorFun"])>0)
				{
					if( function_exists ($subConfig["validatorFun"]) )
					if( call_user_func($subConfig["validatorFun"], $this->defaults[$subConfig["control_id"]]) === false )
					{
						$this->errorsBlocks[$subConfig["control_id"]."_ErrRule"] =
							isset($subConfig["validator_message"]) ? $subConfig["validator_message"] : "";
					}
				}
			}
		}
		
		if(is_array($cfg["lists"])) reset($cfg["lists"]);
		while ( is_array($cfg["lists"]) && list($list_block/*key*/, $subConfig/*value array*/) = each($cfg["lists"]))
		{
			
			if( $subConfig["control_type"] == "DDL" || $subConfig["control_type"] == "MDDL")
			{
				if($subConfig["required"] === true && trim($this->defaults[$subConfig["control_id"]]) == "")
				{
					$this->errorsBlocks[$subConfig["control_id"]."_ErrReq"] =
						isset($subConfig["required_message"]) ? $subConfig["required_message"] : "";
				}
				elseif( count($subConfig["validatorFun"])>0)
				{
					if( function_exists ($subConfig["validatorFun"]) )
					if( call_user_func($subConfig["validatorFun"], $this->defaults[$subConfig["control_id"]]) === false )
					{
						$this->errorsBlocks[$subConfig["control_id"]."_ErrRule"] =
							isset($subConfig["validator_message"]) ? $subConfig["validator_message"] : "";
					}
				}
			}
		}
	}
}
?>