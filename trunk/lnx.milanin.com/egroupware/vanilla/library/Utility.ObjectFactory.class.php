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
* Description: Instantiates objects. Allows developer's to clone and change existing classes and have their objects used throughout the application, instead of the application default objects.
* Applications utilizing this file: Vanilla;
*/
class ObjectFactory {
	var $ClassIndex;        // An array containing name/value pairs mapping labels to class names
   var $ControlStrings;		// An array containing strings to be written in controls when they are rendered

	// Adds a string to the control strings collection
   // If the option ControlRenderCode parameter is used, it will write
   // the string to the control when that code is used as a reference
   // by the control (allowing you to write strings to particular places
   // in the control's render method)
	function AddControlString($ControlClassName, $ControlRenderCode = "", $String) {
		if ($ControlRenderCode != "") $ControlClassName = $ControlClassName.".".$ControlRenderCode;
		if (!array_key_exists($ControlClassName, $this->ControlStrings)) {
			$this->ControlStrings[$ControlClassName] = array();
		}
		$this->ControlStrings[$ControlClassName][] = $String;		
	}
	
	// Private (used internally - should use NewObject or NewContextObject externally)
   function CreateObject(&$Context, $ClassLabel, $IsContextObject, $Param1 = "", $Param2 = "", $Param3 = "", $Param4 = "", $Param5 = "", $Param6 = "", $Param7 = "", $Param8 = "", $Param9 = "", $Param10 = "") {
      if (!array_key_exists($ClassLabel, $this->ClassIndex)) {
         // If the class has not yet been defined, assume the class label is the class name
         $ClassName = $ClassLabel;
      } else {
         $ClassName = $this->ClassIndex[$ClassLabel];
      }
      if (!class_exists($ClassName)) $Context->ErrorManager->AddError($Context, "ObjectFactory", "NewObject", "The \"".$ClassName."\" class referenced by \"".$ClassLabel."\" does not appear to exist.");
		if ($IsContextObject) {
			return new $ClassName($Context, $Param1, $Param2, $Param3, $Param4, $Param5, $Param6, $Param7, $Param8, $Param9, $Param10);
		} else {
			return new $ClassName($Param1, $Param2, $Param3, $Param4, $Param5, $Param6, $Param7, $Param8, $Param9, $Param10);
		}
	}
	
	// Almost identical to NewObject, but passes the context by reference as the first variable in the constructor of the object
	function NewContextObject(&$Context, $ClassLabel, $Param1 = "", $Param2 = "", $Param3 = "", $Param4 = "", $Param5 = "", $Param6 = "", $Param7 = "", $Param8 = "", $Param9 = "", $Param10 = "") {
		return $this->CreateObject($Context, $ClassLabel, 1, $Param1, $Param2, $Param3, $Param4, $Param5, $Param6, $Param7, $Param8, $Param9, $Param10);
	}
	
	// Create a new object based on a class name. Will gracefully error out if the class does not exist
	function NewObject(&$Context, $ClassLabel, $Param1 = "", $Param2 = "", $Param3 = "", $Param4 = "", $Param5 = "", $Param6 = "", $Param7 = "", $Param8 = "", $Param9 = "", $Param10 = "") {
		return $this->CreateObject($Context, $ClassLabel, 0, $Param1, $Param2, $Param3, $Param4, $Param5, $Param6, $Param7, $Param8, $Param9, $Param10);
	}
   
   function ObjectFactory () {
      $this->ClassIndex = array();
		$this->ControlStrings = array();
   }
   
   // For debugging, allow the current references to be written
   function PrintReferences() {
      while (list($Label, $Name) = each($this->ClassIndex)) {
         echo("<div>".$Label.": ".$Name."</div>");
      }      
   }
	
	// Takes a string from the control string collection and returns it based on the supplied controlclassname and control render code
	function RenderControlStrings($ControlClassName, $ControlRenderCode = "") {
		$sReturn = "";
		if ($ControlRenderCode != "") $ControlClassName = $ControlClassName.".".$ControlRenderCode;
		if (array_key_exists($ControlClassName, $this->ControlStrings)) {
			for ($i = 0; $i < count($this->ControlStrings[$ControlClassName]); $i++) {
				$sReturn .= $this->ControlStrings[$ControlClassName][$i];
			}
		}
		return $sReturn;
	}
   
	function SetReference($ClassLabel, $ClassName = "") {
      if ($ClassName == "") $ClassName = $ClassLabel;
      $this->ClassIndex[$ClassLabel] = $ClassName;
	}	
}
?>