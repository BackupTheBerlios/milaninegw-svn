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
* Description: Manages the global variables for the context of the page.
* Applications utilizing this file: Vanilla;
*/
class Context {
   // Public Properties
   var $Session;
   var $Database;
   var $WarningCollector;
   var $ErrorManager;
   var $SqlCollector;
   var $Writer;
	var $ObjectFactory;
   var $SelfUrl;
   var $StyleUrl;
   var $Mode;              // Debug, Release, etc
   var $BodyAttributes;
   var $PageTitle;
	var $StringManipulator;
	var $Dictionary;
   
	// Destructor (not called automatically thanks to php)
	function Unload() {
		if ($this->Database) $this->Database->CloseConnection();
		unset($this->Session);
		unset($this->Database);
		unset($this->WarningCollector);
		unset($this->ErrorManager);
		unset($this->SqlCollector);
		unset($this->Writer);
		unset($this->SelfUrl);
		unset($this->StyleUrl);
		unset($this->Mode);
		unset($this->BodyAttributes);
		unset($this->PageTitle);
		unset($this->StringManipulator);
		unset($this->Dictionary);
	}
   
   // Constructor
   function Context() {
		$this->BodyAttributes = "";
		$this->StyleUrl = "";
		$this->PageTitle = "";		
		$this->Dictionary = array();
		
		$this->CommentFormats = array();
		$this->CommentFormats[] = "Text";
		$this->CommentFormats[] = "Html";
		
		// Create an object factory
      $this->ObjectFactory = new ObjectFactory();

      // Current Mode
      $this->Mode = ForceIncomingCookieString("Mode", "");
		
      // Url of the current page
      $this->SelfUrl = basename(ForceString(@$_SERVER['PHP_SELF'], "index.php"));
      
      // Instantiate a string writer
      $this->Writer = new Writer();
      
      // Instantiate a SqlCollector (for debugging)
      $this->SqlCollector = new MessageCollector();
      $this->SqlCollector->CssClass = "Sql";
      
      // Instantiate a Warning collector (for user errors)
      $this->WarningCollector = new MessageCollector();
      
      // Instantiate an Error manager (for fatal errors)
      $this->ErrorManager = new ErrorManager();
      
      // Instantiate a Database object (for performing database actions)
      $this->Database = new MySQL(dbHOST, dbNAME, dbUSER, dbPASSWORD, $this);
      
		// Instantiate the string manipulation object
      $this->StringManipulator = new StringManipulator();
		// Add the plain text manipulator
      $TextFormatter = new TextFormatter();
		$this->StringManipulator->AddManipulator(agDEFAULTSTRINGFORMAT, $TextFormatter);
		
      // Instantiate a Session object (to identify and profile the current user)
      $this->Session = new Session();
      $this->Session->Start($this);
		
		// The style url (as defined by the user session)
      if (@$this->Session->User) $this->StyleUrl = ForceString($this->Session->User->StyleUrl, agDEFAULT_STYLE);
   }
	
	function FormatString($String, $Object, $Format, $FormatPurpose) {
		return $this->StringManipulator->Parse($String, $Object, $Format, $FormatPurpose);
	}
	
	function KillProcess($AffectedElement, $AffectedFunction, $Message, $Code = "") {
		$this->ErrorManager->AddError($this, $AffectedElement, $AffectedFunction, $Message, $Code, 1);
	}
	
	function GetDefinition($Code) {
      if (array_key_exists($Code, $this->Dictionary)) {
         return $this->Dictionary[$Code];
         // return "X".$this->Dictionary[$Code]."X";
         // return "sss";
      } else {
         return $Code;
      }
	}
}
?>
