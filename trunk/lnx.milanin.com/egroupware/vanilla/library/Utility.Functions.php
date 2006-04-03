<?php
/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Lussumo's Software Library.
* Lussumo's Software Library is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Lussumo's Software Library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
* 
* Description: Non-application specific helper functions
* Applications utilizing this file: Vanilla; Filebrowser;
*/

function AddDaysToTimeStamp($TimeStamp, $NumberOfDaysToAdd) {
	if ($NumberOfDaysToSubtract == 0) {
		return $TimeStamp;
	} else {
		return strtotime("+".$NumberOfDaysToAdd." day", $TimeStamp);
	}
}

function SubtractDaysFromTimeStamp($TimeStamp, $NumberOfDaysToSubtract) {
	if ($NumberOfDaysToSubtract == 0) {
		return $TimeStamp;
	} else {
		return strtotime("-".$NumberOfDaysToSubtract." day", $TimeStamp);
	}
}

// Based on the total number of items and the number of items per page,
// this function will calculate how many pages there are.
// Returns the number of pages available
function CalculateNumberOfPages($ItemCount, $ItemsPerPage) {
	$TmpCount = ($ItemCount/$ItemsPerPage);
	$RoundedCount = intval($TmpCount);
	$PageCount = 0;
	if ($TmpCount > 1) {
		if ($TmpCount > $RoundedCount) {
			$PageCount = $RoundedCount + 1;
		} else {
			$PageCount = $RoundedCount;
		}
	} else {
		$PageCount = 1;
	}
	return $PageCount;
}

// performs the opposite of htmlentities
function DecodeHtmlEntities($String) {
	/*
   $TranslationTable = get_html_translation_table(HTML_ENTITIES);
	print_r($TranslationTable);
   $TranslationTable = array_flip($TranslationTable);
   return strtr($String, $TranslationTable);
	
	return html_entity_decode(htmlentities($String, ENT_COMPAT, 'UTF-8'));
   */
   $String= html_entity_decode($String,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
   $String= preg_replace('/&#(\d+);/me',"chr(\\1)",$String); #decimal notation
   $String= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$String);  #hex notation
   return $String;
	
}

// return the opposite of the given boolean value
function FlipBool($Bool) {
	$Bool = ForceBool($Bool, 0);
	return $Bool?0:1;
}

// Take a value and force it to be an array.
function ForceArray($InValue, $DefaultValue) {
	if(is_array($InValue)) {
		$aReturn = $InValue;
	} else {
		// assume it's a string
		$sReturn = trim($InValue);
		$length = strlen($sReturn);
		if($length == 0) {
			$aReturn = $DefaultValue;
		} else {
			$aReturn = array($sReturn);
		}
	}
	return $aReturn;
}

// Force a boolean value
// Accept a default value if the input value does not represent a boolean value
function ForceBool($InValue, $DefaultBool) {
	// If the invalue doesn't exist (ie an array element that doesn't exist) use the default
	if (!$InValue) $InValue = $DefaultBool;
	$InValue = strtoupper($InValue);
	$bReturn = $DefaultBool;
	
	if ($InValue == "TRUE" || $InValue == "FALSE" || $InValue == 1 || $InValue == 0 || $InValue == "Y" || $InValue == "N") {
		if ($InValue == "TRUE" || $InValue == 1 || $InValue == "Y") {
			$bReturn = 1;
		} else {
			$bReturn = 0;
		}
	}
	return $bReturn;
}

// Take a value and force it to be a float (decimal) with a specific number of decimal places.
function ForceFloat($InValue, $DefaultValue, $DecimalPlaces = 2) {
	$fReturn = floatval($InValue);
	if ($fReturn == 0) $fReturn = $DefaultValue;
	$fReturn = number_format($fReturn, $DecimalPlaces);
	return $fReturn;
}

// Check both the get and post incoming data for a variable
function ForceIncomingArray($VariableName, $DefaultValue) {
	// First check the querystring
	$aReturn = ForceSet(@$_GET[$VariableName], $DefaultValue);
	$aReturn = ForceArray($aReturn, $DefaultValue);
	// If the default value was defined, then check the post variables
	if ($aReturn == $DefaultValue) {
		$aReturn = ForceSet(@$_POST[$VariableName], $DefaultValue);
		$aReturn = ForceArray($aReturn, $DefaultValue);
	}
	return $aReturn;	
}

// Check both the get and post incoming data for a variable
function ForceIncomingBool($VariableName, $DefaultBool) {
	// First check the querystring
	$bReturn = ForceSet(@$_GET[$VariableName], $DefaultBool);
	$bReturn = ForceBool($bReturn, $DefaultBool);
	// If the default value was defined, then check the post variables
	if ($bReturn == $DefaultBool) {
		$bReturn = ForceSet(@$_POST[$VariableName], $DefaultBool);
		$bReturn = ForceBool($bReturn, $DefaultBool);
	}
	return $bReturn;	
}

function ForceIncomingCookieString($VariableName, $DefaultValue) {
	$sReturn = ForceSet(@$_COOKIE[$VariableName], $DefaultValue);
	$sReturn = ForceString($sReturn, $DefaultValue);
	return $sReturn;	
}

// Check both the get and post incoming data for a variable
// Does not allow integers to be less than 0
function ForceIncomingInt($VariableName, $DefaultValue) {
	// First check the querystring
	$iReturn = ForceSet(@$_GET[$VariableName], $DefaultValue);
	$iReturn = ForceInt($iReturn, $DefaultValue);
	// If the default value was defined, then check the form variables
	if ($iReturn == $DefaultValue) {
		$iReturn = ForceSet(@$_POST[$VariableName], $DefaultValue);
		$iReturn = ForceInt($iReturn, $DefaultValue);
	}
	// If the value found was less than 0, set it to the default value
	if($iReturn < 0) $iReturn == $DefaultValue;

	return $iReturn;	
}

// Check both the get and post incoming data for a variable
function ForceIncomingString($VariableName, $DefaultValue) {
	// First check the querystring
	$sReturn = ForceSet(@$_GET[$VariableName], $DefaultValue);
	$sReturn = ForceString($sReturn, $DefaultValue);
	// If the default value was defined, then check the post variables
	if ($sReturn == $DefaultValue) {
		$sReturn = ForceSet(@$_POST[$VariableName], $DefaultValue);
		$sReturn = ForceString($sReturn, $DefaultValue);
	}
	// And strip slashes from the string
   $sReturn = stripslashes($sReturn);
	return $sReturn;	
}

// Take a value and force it to be an integer.
function ForceInt($InValue, $DefaultValue) {
	$iReturn = intval($InValue);
	if ($iReturn == 0) $iReturn = $DefaultValue;
	return $iReturn;
}

// Takes a variable and checks to see if it's set. 
// Returns the value if set, or the default value if not set.
function ForceSet($InValue, $DefaultValue) {
	if(isset($InValue)) {
		$sReturn = $InValue;
	} else {
		$sReturn = $DefaultValue;
	}
	return $sReturn;
}

// Take a value and force it to be a string.
function ForceString($InValue, $DefaultValue) {
	if (is_string($InValue)) {
		$sReturn = trim($InValue);
		$length = strlen($sReturn);
		if($length == 0) $sReturn = $DefaultValue;
	} else {
		$sReturn = $DefaultValue;
	}
	return $sReturn;
}

// Take date parts and put them in mysql_friendly format
// If no values are supplied, it will return the current date
function FormatDate($Year = "", $Month = "", $Day = "", $Format = "mysql", $Hour = "", $Minute = "", $Second = "") {
	// Manipulate year
	$Year = ForceInt($Year, 0);
	
	if ($Year == 0 || strlen($Year) != 4) $Year = date("Y", mktime());
	// Manipulate month
	$Month = ForceInt($Month, 0);
	if ($Month <= 0 || $Month > 12) $Month = date("n", mktime());
	// Manipulate Day
	$Day = ForceInt($Day, 0);
	if ($Day <= 0 || $Day > 31) $Day = date("j", mktime());
	// Manipulate Hour
	$Hour = ForceInt($Hour, 0);
	if ($Hour <= 0 || $Hour > 23) $Hour = 0;
	// Manipulate Minute
	$Minute = ForceInt($Minute, 0);
	if ($Minute <= 1 || $Minute > 60) $Minute = 0;
	// Manipulate Second
	$Second = ForceInt($Second, 0);
	if ($Second <= 1 || $Second > 60) $Second = 0;
	
	if ($Format == "unixtimestamp") {
		return mktime($Hour, $Minute, $Second, $Month, $Day, $Year);
	} else {
		$Month = PrefixString($Month, "0", 2);
		$Day = PrefixString($Day, "0", 2);
		$Hour = PrefixString($Hour, "0", 2);
		$Minute = PrefixString($Minute, "0", 2);
		$Second = PrefixString($Second, "0", 2);
		return $Year."-".$Month."-".$Day." ".$Hour.":".$Minute.":".$Second;
	}
}

function FormatHyperlink($InString, $ExternalTarget = "1", $LinkText = "") {
	$ExternalTarget = ForceBool($ExternalTarget, 0);
	$Target = "";
	if ($ExternalTarget) $Target = " target=\"_blank\"";
	if (strpos($InString, "http://") == 0 && strpos($InString, "http://") !== false) {
		if ($LinkText == "") {
			$Display = $InString;
			if (substr($Display, strlen($Display)-1,1) == "/") $Display = substr($Display, 0, strlen($Display)-1);
			$Display = str_replace("http://", "", $Display);
		} else {
			$Display = $LinkText;
		}
		return "<a href=\"".$InString."\"".$Target.">".$Display."</a>";
	} elseif (strpos($InString, "mailto:") == 0 && strpos($InString, "mailto:") !== false) {
		if ($LinkText == "") {
			$Display = str_replace("mailto:", "", $InString);
		} else {
			$Display = $LinkText;
		}
		return "<a href=\"".$InString."\"".$Target.">".$Display."</a>";
	} elseif (strpos($InString, "ftp://") == 0 && strpos($InString, "ftp://") !== false) {
		if ($LinkText == "") {
			$Display = str_replace("ftp://", "", $InString);
		} else {
			$Display = $LinkText;
		}
		return "<a href=\"".$InString."\"".$Target.">".$Display."</a>";
	} elseif (strpos($InString, "aim:goim?screenname=") == 0 && strpos($InString, "aim:goim?screenname=") !== false) {
		if ($LinkText == "") {
			$Display = str_replace("aim:goim?screenname=", "", $InString);
		} else {
			$Display = $LinkText;
		}
		return "<a href=\"".$InString."\"".$Target.">".$Display."</a>";
	} else {
		return ($LinkText == "")?$InString:$LinkText;
	}
}

function FormatHtmlStringForNonDisplay($inValue) {
	$sReturn = ForceString($inValue, "");
	// $sReturn = stripslashes($sReturn);
	$sReturn = htmlspecialchars($sReturn);
	$sReturn = str_replace("\r\n", "<br />", $sReturn);
	return $sReturn;
}

function FormatHtmlStringInline($inValue, $StripSlashes = "0") {
	$sReturn = ForceString($inValue, "");
	if (ForceBool($StripSlashes, 0)) $sReturn = stripslashes($sReturn);
	$sReturn = preg_replace("/<([^>])+>|&([^;])+/", "", $sReturn);
	$sReturn = htmlspecialchars($sReturn);
	$sReturn = str_replace("\r\n", " ", $sReturn);
	return $sReturn;
}

function FormatPlural($Number, $Singular, $Plural, $IncludeNumber = "1") {
	$IncludeNumber = ForceBool($IncludeNumber, 0);
	$Return = "";
	if ($Number == 1) {
		$Return = ($IncludeNumber?$Number." ":"").$Singular;
	} else {
		$Return = ($IncludeNumber?$Number." ":"").$Plural;
	}
	return $Return;
}

function FormatPossessive($String) {
	$Possessed = $String;
	if ($String != "") {
		if (strtolower(substr($String, strlen($String)-1, 1)) == "s") {
			$Possessed .= "'";
		} else {
			$Possessed .= "'s";
		}
	}
	return $Possessed;
}

// Formats a value so it's safe to insert into the database
function FormatStringForDatabaseInput($inValue, $bStripHtml = "0") {
	$bStripHtml = ForceBool($bStripHtml, 0);
	// $sReturn = stripslashes($inValue);
   $sReturn = $inValue;
	if ($bStripHtml) $sReturn = strip_tags($sReturn);
	$sReturn = ForceString($sReturn, "");
	$sReturn = addslashes($sReturn);
	return $sReturn;
}

// Takes a user defined string and formats it for page display. 
// You can optionally remove html from the string.
function FormatStringForDisplay($inValue, $bStripHtml = false) {
	$sReturn = ForceString($inValue, "");
	// $sReturn = stripslashes($sReturn);
	if ($bStripHtml) {
		$sReturn = strip_tags($sReturn);
		$sReturn = str_replace("\r\n", "<br />", $sReturn);
	}
	$sReturn = htmlspecialchars($sReturn);

	return $sReturn;
}

function GetBasicCheckBox($Name, $Value = 1, $Checked, $Attributes = "") {
	return "<input type=\"checkbox\" name=\"".$Name."\" value=\"".$Value."\" ".(($Checked == 1)?" checked=\"checked\"":"")." $Attributes />";
}

function GetBool($Bool, $True = "Yes", $False = "No") {
	return ($Bool ? $True : $False);
}

function GetDynamicCheckBox($Name, $Value = 1, $Checked, $OnClick, $Text, $Attributes = "") {
	$CheckBoxID = $Name."ID";
	$Attributes .= " id=\"".$CheckBoxID."\" onclick=\"".$OnClick."\"";
	// return GetBasicCheckBox($Name, $Value, $Checked, $Attributes)
	// 	." <a href=\"javascript:CheckBox('".$CheckBoxID."');".$OnClick."\">".$Text."</a>";
   return "<label>".GetBasicCheckBox($Name, $Value, $Checked, $Attributes)." ".$Text."</label>";
}

function GetEmail($Email, $LinkText = "") {
	if ($Email == "") {
		return "&nbsp;";
	} else {
		$EmailParts = explode("@", $Email);
		if (count($EmailParts) == 2) {
			return "<script type=\"text/javascript\">\r\nWriteEmail('".$EmailParts[1]."', '".$EmailParts[0]."', '".$LinkText."');\r\n</script>";
		} else {
			// Failsafe
			return "<a href=\"mailto:".$Email."\">".($LinkText==""?$Email:$LinkText)."</a>";
		}
	}
}

function GetImage($ImageUrl, $Height = "", $Width = "", $TagIdentifier = "", $EmptyImageReplacement = "&nbsp;") {
	$sReturn = "";
	if (ReturnNonEmpty($ImageUrl) == "&nbsp;") {
		$sReturn =  $EmptyImageReplacement;
	} else {
		$sReturn = "<img src=\"$ImageUrl\"";
		if ($Height != "") $sReturn .= " height=\"$Height\"";
		if ($Width != "") $sReturn .= " width=\"$Width\"";
		if ($TagIdentifier != "") $sReturn .= " id=\"$TagIdentifier\"";
		$sReturn .= " alt=\"\" border=\"0\" />";
	}
	return $sReturn;
}

function GetRemoteIp($FormatIpForDatabaseInput = "0") {
	$FormatIpForDatabaseInput = ForceBool($FormatIpForDatabaseInput, 0);
	$sReturn = ForceString(@$_SERVER['REMOTE_ADDR'], "");
	if (strlen($sReturn) > 20) $sReturn = substr($sReturn, 0, 19);
	if ($FormatIpForDatabaseInput) $sReturn = FormatStringForDatabaseInput($sReturn, 1);
	return $sReturn;	
}

// allows inline if statements
function Iif($Condition, $TruePart, $FalsePart) {
	if ($Condition) {
		return $TruePart;
	} else {
		return $FalsePart;
	}
}

function PrefixString($string, $prefix, $length) {
	if (strlen($string) >= $length) {
		return $string;
	} else {
		return substr(($prefix.$string),strlen($prefix.$string)-$length, $length);
	}
}

function PrependString($Prepend, $String) {
	$sPrepend = strtolower($Prepend);
	$sString = strtolower($String);
	$pos = strpos($sString, $sPrepend);
	if (($pos !== false && $pos == 0) || $String == "") {
		return $String;
	} else {
		return $Prepend.$String;
	}
}

// If a value is empty, return the non-empty value
function ReturnNonEmpty($InValue, $NonEmptyValue = "&nbsp;") {
	if (trim($InValue) == "") {
		return $NonEmptyValue;
	} else {
		return $InValue;
	}
}

function SaveAsDialogue($FolderPath, $FileName, $DeleteFile = "0") {
	$DeleteFile = ForceBool($DeleteFile, 0);
	if ($FolderPath != "") {
		if (substr($FolderPath,strlen($FolderPath)-1) != "/") $FolderPath = $FolderPath."/";
	}
	$FolderPath = $FolderPath.$FileName;
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=$FileName");
	header("Content-Transfer-Encoding: binary");
	readfile($FolderPath);
	if ($DeleteFile) unlink($FolderPath);
	die();
}

function SerializeArray($InArray) {
	$sReturn = "";
	if (is_array($InArray)) {
		if (count($InArray) > 0) {
			$sReturn = serialize($InArray);
			$sReturn = addslashes($sReturn);
		}
	}
	return $sReturn;
}

// Cuts a string to the specified length. 
// Then moves back to the previous space so words are not sliced half-way through.
function SliceString($InString, $Length) {
	$Space = " ";
	$sReturn = "";
	if (strlen($InString) > $Length) {
		$sReturn = substr(trim($InString), 0, $Length); 
		$sReturn = substr($sReturn, 0, strlen($sReturn) - strpos(strrev($sReturn), $Space));
	    $sReturn .= "...";
	} else {
		$sReturn = $InString;
	}
	return $sReturn;
}

function TimeDiff($Time, $TimeToCompare = "") {
	if ($TimeToCompare == "") $TimeToCompare = time();
	$Difference = $TimeToCompare-$Time;
	$Days = floor($Difference/60/60/24);
	$Difference -= $Days*60*60*24;
	$Hours = floor($Difference/60/60);
	$Difference -= $Hours*60*60;
	$Minutes = floor($Difference/60);
	$Difference -= $Minutes*60;
	$Seconds = $Difference;
   
	if ($Days > 7) {
		return date("M jS Y", $Time);
	} elseif ($Days > 0) {
		return FormatPlural($Days, "day ago", "days ago");
	} elseif ($Hours > 0) {
		return FormatPlural($Hours, "hour ago", "hours ago");
	} elseif ($Minutes > 0) {
		return FormatPlural($Minutes, "minute ago", "minutes ago");
	} else {
		return FormatPlural($Seconds, "second ago", "seconds ago");
	}    
}

// Convert a datetime to a timestamp
function UnixTimestamp($DateTime) {
	$Return = "";
	if (strlen($DateTime) == 19) {
		// Datetime comes in the format: YYYY-MM-DD HH:MM:SS
		$Year = substr($DateTime, 0, 4);
		$Month = substr($DateTime, 5, 2);
		$Day = substr($DateTime, 8, 2);
		$Hour = substr($DateTime, 11, 2);
		$Minute = substr($DateTime, 14, 2);
		$Second = substr($DateTime, 17, 2);
		$Return = FormatDate($Year, $Month, $Day, "unixtimestamp", $Hour, $Minute, $Second);
	}
	return $Return;
}

function MysqlDateTime($Timestamp = "") {
	if ($Timestamp == "") $Timestamp = mktime();
	return date("Y-m-d H:i:s", $Timestamp);
}

function UnserializeArray($InSerialArray) {
	$aReturn = array();
	if ($InSerialArray != "" && !is_array($InSerialArray)) {
		$aReturn = unserialize($InSerialArray);
		if (is_array($aReturn)) {
			for ($i = 0; $i < count($aReturn); $i++) {
				$aReturn[$i] = array_map("stripslashes", $aReturn[$i]);
			}
		}
	}
	return $aReturn;	
}

function UnserializeAssociativeArray($InSerialArray) {
	$aReturn = array();
	if ($InSerialArray != "" && !is_array($InSerialArray)) {
		$aReturn = unserialize($InSerialArray);
	}
	return $aReturn;	
}

// Instantiate a simple validator
function Validate($InputName, $IsRequired, $Value, $MaxLength, $ValidationExpression, &$Context) {
//         echo "<!-- in the Validate -->\n";
	$Validator = $Context->ObjectFactory->NewContextObject($Context, "Validator");
// 	echo "<!-- Validator constructed -->\n";
	$Validator->InputName = $InputName;
	$Validator->isRequired = $IsRequired;
	$Validator->Value = $Value;
	$Validator->MaxLength = $MaxLength;
	if ($ValidationExpression != "") {
		$Validator->ValidationExpression = $ValidationExpression;
		$Validator->ValidationExpressionErrorMessage = $Context->GetDefinition("ErrImproperFormat")." ".$InputName;
	}
	return $Validator->Validate();
}

function WriteEmail($Email, $LinkText = "") {
	echo(GetEmail($Email, $LinkText));
}

// Create the html_entity_decode function for users prior to PHP 4.3.0
if (!function_exists("html_entity_decode")) {
	function html_entity_decode($String) {
		$TranslationTable = get_html_translation_table(HTML_ENTITIES);
		$TranslationTable = array_flip($TranslationTable);
		return strtr($String, $TranslationTable);
	}
}
	
?>