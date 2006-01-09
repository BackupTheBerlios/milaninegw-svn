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
* Description: The string manipulation classes are used to format user comments for saving to the database or displaying on the screen
* Applications utilizing this file: Vanilla;
*/

// An interface for string manipulation classes
class StringFormatter {
   // You can optionally pass this formatter a collection of other formatters and it will sequentially call the parse method on all of them.
   var $ChildFormatters;
   
   function Constructor() {
      $this->ChildFormatters = array();
   }
   
   // String is the string to be parsed.
   // Object is an object related to the string in some way (in case you
   // want to pass in some kind of object to perform other manipulations,
   // like the comment object to retrieve author information).
   // FormatPurpose is a value indicating what the purpose of formatting
   // the string is. ie. Are you formatting for database input or screen
   // display?
   // The function should return the formatted string
   function Parse($String, $Object, $FormatPurpose) {
      $String = $this->ParseChildren($String, $Object, $FormatPurpose);
      return $String;
   }
   
   function ParseChildren($String, $Object, $FormatPurpose) {
      $ChildFormatterCount = count($this->ChildFormatters);
      for ($i = 0; $i < $ChildFormatterCount; $i++) {
         $Formatter = $this->ChildFormatters[$i];
         $String = $Formatter->Parse($String, $Object, $FormatPurpose);
      }
      return $String;
   }
}

// An implementation of the string filter interface for plain text strings
class TextFormatter extends StringFormatter {
   function Parse ($String, $Object, $FormatPurpose) {
      $sReturn = $String;
      // Only format plain text strings if they are being displayed (save in database as is)
      if ($FormatPurpose == agFORMATSTRINGFORDISPLAY) {
         $sReturn = htmlspecialchars($sReturn);
         $sReturn = str_replace("\r\n", "<br />", $sReturn);
         $sReturn = str_replace("/me", $this->GetAccountLink($Object), $sReturn);
         $sReturn = $this->AutoLink($sReturn);
      } else {
         // You'd think I should be formatting the string for safe database
         // input here, but I don't want to leave that in the hands of plugin
         // authors. So, I perform that in the validation call on the comment
         // object when it is being saved (CommentManager->SaveComment)
      }
      return $sReturn;
   }
   
   function AutoLink($String) {
      // autolink example from www.zend.com (Code Gallery ) by http://www.zend.com/search_code_author.php?author=goten
      return preg_replace("/(?<!<a href=\")(?<!\")(?<!\">)((http|https|ftp):\/\/[\w?=&.\/-;#~%-]+)/","<a href=\"\\1\" target=\"_blank\">\\1</a>",$String);
   }

   function GetAccountLink($Object) {
		if ($Object->AuthUserID != "" && $Object->AuthUsername != "") {
			return "<a href=\"account.php?u=".$Object->AuthUserID."\">".$Object->AuthUsername."</a>";
		} else {
			return "/me";
		}
   }
}

// A class for managing string manipulator classes (globally)
class StringManipulator {
   var $Formatters; // An associative array of string formatters   
   
   // Constructor
   function StringManipulator() {
      $this->Formatters = array();  
   }
   
   function AddManipulator($ObjectName, $Object) {
      $this->Formatters[$ObjectName] = $Object;
   }
   
   function Parse($String, $Object, $Format, $FormatPurpose) {
      if (array_key_exists($Format, $this->Formatters)) {
         $Formatter = $this->Formatters[$Format];
      } else {
         // If the requested formatter wasn't found, use the default
         $Formatter = $this->Formatters[agDEFAULTSTRINGFORMAT];
      }
      return $Formatter->Parse($String, $Object, $FormatPurpose);
   }
}

?>