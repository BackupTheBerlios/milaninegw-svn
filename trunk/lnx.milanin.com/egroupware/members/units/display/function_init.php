<?php

	// Display unit intialisation routines

	// Variables used in basic templating
		global $screen;
		$screen['mainbody'] = "";
		$screen['headers'] = "";
		$screen['title'] = "";
		$screen['menu'] = "";
		$screen['sidebar'] = "";
		$screen['footer'] = "";
		$screen['messages'] = "";
		
	// Initialise RTF edit
		$data['display:topofpage:headers'][] =  "<script language=\"JavaScript\" type=\"text/javascript\" src=\"/units/display/rtfedit/richtext.js\"></script>";
	
	// Function to sanitise RTF edit text
	function RTESafe($strText) {
		//returns safe code for preloading in the RTE
		$tmpString = trim($strText);
		
		//convert all types of single quotes
		$tmpString = str_replace(chr(145), chr(39), $tmpString);
		$tmpString = str_replace(chr(146), chr(39), $tmpString);
		$tmpString = str_replace("'", "&#39;", $tmpString);
		
		//convert all types of double quotes
		$tmpString = str_replace(chr(147), chr(34), $tmpString);
		$tmpString = str_replace(chr(148), chr(34), $tmpString);
		
		//replace carriage returns & line feeds
		$tmpString = str_replace(chr(10), " ", $tmpString);
		$tmpString = str_replace(chr(13), " ", $tmpString);
		
		return $tmpString;
	}

	function DisplayGW_dropdown($metaID, $value, $isReadOnly = true, $ctrlID="", $fullParam="", $lang="en")
	{
		$res = $isReadOnly ? "" : "<select name=\"".$ctrlID."\" id=\"".$ctrlID."\">";
		$sql = sprintf("SELECT data as value from other_data where name='%s' and lang='%s'", $metaID, $lang);
		$obj = db_query($sql);
		$arr = is_array($fullParam["source"]) ? $fullParam["source"] : explode("\n", $obj[0]->value);
		$arr = array_map("trim", $arr);
		if( !$isReadOnly )
			$res .= "<option value=\"-1\""."></option>";

		//$arr contains all valid values from database && $parameter - contains selected value;
		if (count($arr) > 0) 
		{
			foreach($arr as $i=>$j) 
			{
				$oValue = $i;
				if(	$fullParam["use_key"] === false)
					$oValue = $arr[$i];
						
				if($value == $oValue && $isReadOnly)
					$res .= $arr[$i];
				elseif(!$isReadOnly)
				{
					
					$res .= "<option value=\"$oValue\"".($oValue."" == $value."" ? " selected" : "").">".stripslashes($arr[$i])."</option>";
				}
			}
		}
		
		if( !$isReadOnly )
			$res .= "</select>";
			
		return $res;
	}
	
	function DisplayGW_GroupCheckBox($metaID, $value, $isReadOnly = true, $ctrlID="", $type="chk", $lang="en")
	{
		$res = "";
		$sql = sprintf("SELECT data as value from other_data where name='%s' and lang='%s'", $metaID, $lang);
		$obj = db_query($sql);
		$arr = explode("\n", $obj[0]->value);
		$arr = array_map("trim", $arr);
		//$arr contains all valid values from database && $parameter[0] - contains selected value(s);
		if(!is_array($value))
			$param_arr = $value == "" ? array() : explode(",", $value);
		else
			$param_arr = $value;

		if (count($arr) > 0) 
		{
			if(!$isReadOnly)
				$res .= '<table border="1">';
			
			$catched = false;
			for($i=0; $i<count($arr); $i++) 
			{
				if($isReadOnly && !(array_search($i, $param_arr) === FALSE))
				{
					$res .= ($catched ? ", " : "").$arr[$i];
					$catched = true;
				}
				elseif(!$isReadOnly)
				{
					if($i % 2 == 0)
						$res .= "<tr>";
					
					switch($type)
					{
						case "chk":
							$res .= "<td><input type=\"Checkbox\" ".(array_search($i, $param_arr) === FALSE ? "" : " checked")." name=\"".$ctrlID."[]\" value=\"".$i."\" > ".stripslashes($arr[$i])."</td>";
							break;
							
						case "radio":
							$res .= "<td><input type=\"radio\" ".(array_search($i, $param_arr) === FALSE ? "" : " checked")." name=\"".$ctrlID."\" value=\"".$i."\" > ".stripslashes($arr[$i])."</td>";
							break;
					}
					
					
					if($i % 2 == 1)
						$res .= "</tr>";
				}
			}
			
			if(!$isReadOnly && count($arr) % 2 == 1)
				$res .= "<td>&nbsp;</td>";
								
			if(!$isReadOnly)
				$res .= '</table>';
		}
		return $res;
	}
?>