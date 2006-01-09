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
* Description: Manage major errors resulting in page not functioning properly.
* Applications utilizing this file: Vanilla;
*/
class Error {
	var $AffectedElement;	// The element (class) that has encountered the error
	var $AffectedFunction;	// The function or method that has encountered the error
	var $Message;				// Actual error message
	var $Code;					// Any related code to help identify the error
}

class ErrorManager {
	// Public Variables
	var $StyleSheet;		// A custom stylesheet may be supplied for the error display
	
	// Public, Read Only Variables
	var $ErrorCount;		// Number of errors encountered

	// Private Variables
	var $Errors;			// Collection of error objects
	
	function AddError(&$Context, $AffectedElement, $AffectedFunction, $Message, $Code = "", $WriteAndKill = 1) {
		if ($Context) {
			$Error = $Context->ObjectFactory->NewObject($Context, "Error");
		} else {
			$Error = new Error();
		}
		$Error->AffectedElement = $AffectedElement;
		$Error->AffectedFunction = $AffectedFunction;
		$Error->Message = $Message;
		$Error->Code = $Code;
		$this->Errors[] = $Error;
		$this->ErrorCount += 1;	
		if ($WriteAndKill == 1) $this->Write($Context);
	}
	
	function Clear() {
		$this->ErrorCount = 0;
		$this->Errors = array();
	}
	
	function ErrorManager() {
		$this->Clear();
	}
	
	function Iif($True = "1", $False = "0") {
		if ($this->ErrorCount == 0) {
			return $True;
		} else {
			return $False;			
		}
	}	
	
	function Write(&$Context) {
		echo("<html>
			<head>
			<title>The page could not be displayed</title>
			");
			if ($this->StyleSheet == "") {
				echo("<style>
				body { background: #ffffff; padding: 20px; }
				body, div, h1, h2, h3, h4, p { font-family: Trebuchet MS, tahoma, arial, verdana;  color: #000; line-height: 160%; }
				h1 { font-size: 22px; }
				h2 { color: #c00; font-size: 14px; margin-bottom: 10px; }
				h3, h4, p, .Foot { font-size: 12px; font-weight: normal; margin: 0px; padding: 0px; }
				p { padding-top: 20px; }
				code { display: block; font-size: 11px; padding-left: 10px; padding-right: 10px; background: #f5f5f5; font-family:'courier new',courier,serif; }
				a, a:link, a:visited { color: #36f; background: #ffc; text-decoration: none; }
				a:hover { color: #36F; background: #ffa; text-decoration: none; }
				.Foot { padding-top: 20px; }
				.Sql { font-size: 11px; padding: 8px; background: #f0f0f0; margin-bottom: 6px; }
				</style>
				");
			} else {
				echo("<link rel=\"stylesheet\" href=\"".$this->StyleSheet."\" />\r\n");
			}
			echo("</head>
			<body>
			<h1>A fatal, non-recoverable error has occurred</h1>
			<h2>Technical information (for support personel):</h2>
			");
			for ($i = 0; $i < count($this->Errors); $i++) {
				echo("<h3>Error Message: ".ForceString($this->Errors[$i]->Message, "No error message supplied")."</h3>
					<h4>Affected Elements: ".ForceString($this->Errors[$i]->AffectedElement, "undefined").".".ForceString($this->Errors[$i]->AffectedFunction, "undefined")."();</h4>
					");
				$Code = ForceString($this->Errors[$i]->Code, "");
				if ($Code != "") {
					echo("<p>The error occurred on or near:</p>
						<code>".$Code."</code>
						");
				}
			}
			if ($Context) {
				if ($Context->Mode == agMODE_DEBUG && $Context->SqlCollector) {
					echo("<h2>Database queries run prior to error</h2>");
					$Context->SqlCollector->Write();
				}
			}
			echo("<div class=\"Foot\">For additional support documentation, visit the Lussumo Documentation website at: <a href=\"http://lussumo.com/docs\">lussumo.com/support</a></div>
			</body>
			</html>");
			// Cleanup
         if ($Context) $Context->Unload();
			die();
	}
	function GetSimple() {
		$sReturn = "";
		for ($i = 0; $i < count($this->Errors); $i++) {
			$sReturn .= ForceString($this->Errors[$i]->Message, "No error message supplied\r\n");
		}
		return $sReturn;
	}
}
?>