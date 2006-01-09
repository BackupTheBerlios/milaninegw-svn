<?php
/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*
* Description: Search & Search management classes (handles manipulation of saved searches)
*/

class Search {
   var $SearchID;			// The unique identifier assigned to this search by the system
   var $Label;				// The label assigned to this search by the user
   var $Type;				// The type of search to perform
   var $Keywords;			// The keywords defined by the user
	var $Query;				// The actual string to be searched on in the sql query
   var $Categories;		// The category names to search in (comment & discussion search)
   var $AuthUsername;	// The author's username to filter to (comment & discussion search)
   var $WhisperFilter;	// Should the search be limited to whispers
	var $Roles;				// The roles to filter to (user search)
	var $UserOrder;		// The order to sort results in (user search)
   var $HighlightWords;	// Breaks the query into words to be highlighted in search results
	
   // Clears all properties
   function Clear() {
      $this->SearchID = 0;
      $this->Label = "";
      $this->Type = "Topics";
      $this->Keywords = "";
		$this->Query = "";
      $this->Categories = 0;
      $this->AuthUsername = "";
		$this->WhisperFilter = 0;
		$this->Roles = 0;
		$this->UserOrder = "";
		$this->HighlightWords = array();
   }
	
	function DefineType($InValue) {
      if ($InValue != "Users" && $InValue != "Comments") $InValue = "Topics";
		return $InValue;
	}

   function GetPropertiesFromDataSet($DataSet, $ParseKeywords = "0") {
		$ParseKeywords = ForceBool($ParseKeywords, 0);
		
      $this->SearchID = ForceInt(@$DataSet["SearchID"], 0);
      $this->Label = ForceString(@$DataSet["Label"], "");
      $this->Type = $this->DefineType(ForceString(@$DataSet["Type"], ""));
      $this->Keywords = urldecode(ForceString(@$DataSet["Keywords"], ""));
		if ($ParseKeywords) $this->ParseKeywords($this->Type, $this->Keywords);
   }
    
   function GetPropertiesFromForm() {
      $this->SearchID = ForceIncomingInt("SearchID", 0);
      $this->Label = ForceIncomingString("Label", "");
		$this->Type = $this->DefineType(ForceIncomingString("Type", ""));
		$this->Keywords = urldecode(ForceIncomingString("Keywords", ""));
		
		// Parse out the keywords differently based on the type of search
		$Advanced = ForceIncomingBool("Advanced", 0);
		if ($Advanced) {
			// Load all of the search variables from the form
	      $this->Categories = ForceIncomingString("Categories", "");
			$this->AuthUsername = ForceIncomingString("AuthUsername", "");
			$this->Roles = ForceIncomingString("Roles", "");
			$this->UserOrder = ForceIncomingString("UserOrder", "");
			$this->Query = $this->Keywords;
         
			// Build the keyword definition
         $KeyDef = "";
         if ($this->Type == "Users") {
				if ($this->Roles != "") $KeyDef = "roles:".$this->Roles.";";
				if ($this->UserOrder != "") $KeyDef .= "sort:".$this->UserOrder.";";
				$this->Keywords = $KeyDef.$this->Keywords;
			} else {
				if ($this->Categories != "") $KeyDef = "cats:".$this->Categories.";";
				if ($this->AuthUsername != "") $KeyDef .= $this->AuthUsername.":";
				$this->Keywords = $KeyDef.$this->Keywords;
			}			
		} else {
			// Load all of the search variables from the keyword definition
         $this->ParseKeywords($this->Type, $this->Keywords);			
		}
   }
	
	function ParseKeywords($Type, $Keywords) {
		if ($Type == "Users") {
			// Parse twice to hit both of the potential keyword assignment operators (roles or sort)
			$this->Query = $this->ParseUserKeywords($Keywords);
			$this->Query = $this->ParseUserKeywords($this->Query);
		} else {
			// Check for category assignments
			$this->Query = $Keywords;
			$CatPos = strpos($this->Query, "cats:");
			if ($CatPos !== false && $CatPos == 0) {
				$this->Query = $this->ParsePropertyAssignment("Categories", 5, $this->Query);
			}
			
			// Check for whisper filtering
			$WhisperPos = strpos($this->Query, "whisper;");
			if ($WhisperPos !== false && $WhisperPos == 0) {
				$this->WhisperFilter = 1;
				$this->Query = substr($this->Query, 8);
			}
			
			// Check for username assignment
         $ColonPos = strpos($this->Query, ":");
			if ($ColonPos !== false && $ColonPos != 0) {
				// If a colon was found, check to see that it didn't occur before any quotes
            $QuotePos = strpos($this->Query, "\"");
				if ($QuotePos === false || $QuotePos > $ColonPos) {
					$this->AuthUsername = substr($this->Query, 0, $ColonPos);
					$this->Query = substr($this->Query, $ColonPos+1);
				}
			}
		}
		$Highlight = $this->Query;
		if ($Highlight != "") {
			$Highlight = eregi_replace("\"", "", $Highlight);
			$Highlight = eregi_replace(" and ", "", $Highlight);
			$Highlight = eregi_replace(" or ", "", $Highlight);
			$this->HighlightWords = explode(" ", $Highlight);
		}
	}
	
	function ParsePropertyAssignment($Property, $PropertyLength, $Keywords) {
		$sReturn = $Keywords;
		$DelimiterPos = false;
		$sReturn = substr($sReturn, $PropertyLength);
		$DelimiterPos = strpos($sReturn, ";");
		if ($DelimiterPos !== false) {
			$this->$Property = substr($sReturn, 0, $DelimiterPos);
		} else {
			$this->$Property = substr($sReturn, 0);
		}
		return substr($sReturn, $DelimiterPos+1);
	}
	
	function ParseUserKeywords($Keywords) {
		$sReturn = $Keywords;
		// Check for roles or sort definition
		$RolePos = strpos($sReturn, "roles:");
		$SortPos = strpos($sReturn, "sort:");
		if ($RolePos !== false && $RolePos == 0) {
			$sReturn = $this->ParsePropertyAssignment("Roles", 6, $sReturn);
		} elseif ($SortPos !== false && $SortPos == 0) {
			$sReturn = $this->ParsePropertyAssignment("UserOrder", 5, $sReturn);			
		}
		return $sReturn;
	}
	
	function FormatPropertiesForDatabaseInput() {
		$this->Label = FormatStringForDatabaseInput($this->Label);
		$this->Keywords = FormatStringForDatabaseInput($this->Keywords);
		$this->Query = FormatStringForDatabaseInput($this->Query);
		$this->AuthUsername = FormatStringForDatabaseInput($this->AuthUsername);
		$this->Categories = FormatStringForDatabaseInput($this->Categories);
		$this->Roles = FormatStringForDatabaseInput($this->Roles);
	}

   function FormatPropertiesForDisplay() {
      $this->Label = FormatStringForDisplay($this->Label);
      $this->Keywords = FormatStringForDisplay($this->Keywords);
      $this->AuthUsername = FormatStringForDisplay($this->AuthUsername);
		$this->Query = FormatStringForDisplay($this->Query);
   }
}

class SearchManager {
   var $Name;				// The name of this class
   var $Context;			// The context object that contains all global objects (database, error manager, warning collector, session, etc)
	
	function DeleteSearch($SearchID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("UserSearch", "us");
		$s->AddWhere("SearchID", $SearchID, "=");
		$s->AddWhere("UserID", $this->Context->Session->UserID, "=");
		echo($s->GetDelete());
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "DeleteSearch", "An error occurred while deleting your search.");
		return true;
	}
	
   // Returns a SqlBuilder object with all of the topic properties already defined in the select
   function GetSearchBuilder() {
      $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
	   $s->AddSelect(array("SearchID", "Label", "UserID", "Type", "Keywords"), "us");
      $s->SetMainTable("UserSearch", "us");
      return $s;
   }	
	
   function GetSearchById($SearchID) {
      $Search = $this->Context->ObjectFactory->NewObject($this->Context, "Search");
      $s = $this->GetSearchBuilder();
      $s->AddWhere("us.SearchID", $SearchID, "=");
      $s->AddWhere("UserID", $this->Context->Session->UserID, "=");
      $result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetSearchById", "An error occurred while attempting to retrieve the requested search.");
		if ($this->Context->Database->RowCount($result) == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrSearchNotFound"));
		while ($rows = $this->Context->Database->GetRow($result)) {
			$Search->GetPropertiesFromDataSet($rows, 1);
		}
      return $this->Context->WarningCollector->Iif($Search, false);
   }
	
   function GetSearchCount($UserID) {
      $UserID = ForceInt($UserID, 0);
      $TotalNumberOfRecords = 0;
      
      $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
      $s->AddSelect("SearchID", "us", "Count", "count");
      $s->SetMainTable("VanillaUserSearch", "us");
      $s->AddWhere("UserID", $UserID, "=");
         
      $result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetSearchCount", "An error occurred while retrieving search summary data.");
		while ($rows = $this->Context->Database->GetRow($result)) {
			$TotalNumberOfRecords = $rows['Count'];
		}
      return $TotalNumberOfRecords;
   }
	
   function GetSearchList($RecordsToRetrieve = "0", $UserID) {
      $RecordsToRetrieve = ForceInt($RecordsToRetrieve, 0);
      
      $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
      $s = $this->GetSearchBuilder();
		$s->AddWhere("UserID", $UserID, "=");
      if ($RecordsToRetrieve > 0) $s->AddLimit(0, $RecordsToRetrieve);
   
      return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetSearchList", "An error occurred while retrieving saved searches.");
   }

	function SaveSearch(&$Search) {
      // Validate the topic properties
      if ($Search->Label == "") $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrSearchLabel"));
      // If validation was successful, then reset the properties to db safe values for saving
      if ($this->Context->WarningCollector->Count() == 0) {
			$SearchToSave = $Search;
         $SearchToSave->FormatPropertiesForDatabaseInput();
         $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
         
         // Proceed with the save if there are no warnings
         if ($this->Context->WarningCollector->Count() == 0) {
				$s->SetMainTable("UserSearch");
				$s->AddFieldNameValue("Label", $SearchToSave->Label);
				$s->AddFieldNameValue("UserID", $this->Context->Session->UserID);
				$s->AddFieldNameValue("Type", $SearchToSave->Type);
				$s->AddFieldNameValue("Keywords", $SearchToSave->Keywords);
				if ($SearchToSave->SearchID > 0) {
					$s->AddWhere("SearchID", $SearchToSave->SearchID, "=");
					$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveSearch", "An error occurred while saving your search.");
				} else {
					$Search->SearchID = $this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveSearch", "An error occurred while creating your search.");
				}
         }
         
      }
      return $this->Context->WarningCollector->Iif();
   }
	
   function SearchManager(&$Context) {
      $this->Name = "SearchManager";
		$this->Context = &$Context;
   }	
}
?>