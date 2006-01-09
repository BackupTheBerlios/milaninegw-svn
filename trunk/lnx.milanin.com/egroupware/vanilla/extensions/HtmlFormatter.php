<?php
/*
Extension Name: Html Formatter
Extension Url: http://lussumo.com/docs/
Description: Allows html to be used in strings, but breaks out all "script" related activities
Version: 2.0
Author: SirNot
Author Url: N/A
*/

/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*/

class HtmlFormatter extends StringFormatter {
	function CheckProtocol($Check, $Allow, $Extra, $Prefix, $Suffix) {
		$sReturn = stripslashes($Prefix);
		if(!in_array($Check, $Allow)) $sReturn .= ($Extra.'http://');
	
		else $sReturn .= ($Extra.$Check.':');
		$sReturn .= stripslashes($Suffix);
		
		return $sReturn;
	}
	
	function Execute($String) {
		$AllowedProtocols = array('http', 'ftp', 'https', 'irc', 'gopher');
		
		$Patterns = array(
			"/o(?i)(n)/", //block all js events, but keep it as exact as possible in case 
			"/O(?i)(n)/", //we're mistaking it for a url or something
			"/<a(.+?)href\s*=(\W*)([\w\d\x0a\x0d#&;]+?):([^>]+?)>/esi", 
				//on some browsers the js protocol will still work even if it
				//contains html entities or a newline seperating 'java' and 'script'
			"/s(?i)(cript)/", //now we can go through and cancel out any script tags
			"/S(?i)(cript)/"
		);
		$Replacements = array(
			"&#111;\\1", 
			"&#79;\\1", 
			'$this->CheckProtocol("\\3", $AllowedProtocols, "href="."\\2", "<a"."\\1", "\\4".">")', 
			"&#115;\\1", 
			"&#83;\\1"
		);
		
		return str_replace("\r\n", "<br />", preg_replace($Patterns, $Replacements, $String));
	}
	
   function Parse($String, $Object, $FormatPurpose) {
      if ($FormatPurpose == agFORMATSTRINGFORDISPLAY) {
         // Do this transformation if the string is being displayed
         return $this->Execute($String);
      } else {
         // Do not perform this transformation if the string is being saved to the db
         return $String;
      }
   }
}

// Instantiate the formatter and add it to the context object's string manipulator
$HtmlFormatter = $Context->ObjectFactory->NewContextObject($Context, "HtmlFormatter");
$Context->StringManipulator->AddManipulator("Html", $HtmlFormatter);
?>