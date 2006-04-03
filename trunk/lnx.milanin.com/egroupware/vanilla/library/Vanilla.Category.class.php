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
* Description: Category & category management classes
*/

class Category {
	var $CategoryID;
	var $Name;
	var $Description;
	var $Blocked;					// Is this category blocked by the viewing user
   var $AllowedRoles;			// Contains the roles that are allowed to take part in this category
	var $DiscussionCount;		// aggregate - display only
	
	function Category() {
		$this->Clear();
	}
	
	// Clears all properties
	function Clear() {
		$this->CategoryID = 0;
		$this->Name = "";
		$this->Description = "";
		$this->DiscussionCount = 0;
		$this->Blocked = 0;
		$this->AllowedRoles = array();
	}

	function FormatPropertiesForDatabaseInput() {
		$this->Name = FormatStringForDatabaseInput($this->Name, 1);
		$this->Description = FormatStringForDatabaseInput($this->Description, 1);
	}
	
	function FormatPropertiesForDisplay() {
		$this->Name = FormatStringForDisplay($this->Name, 1);
		$this->Description = FormatStringForDisplay($this->Description, 1);
	}
	
	function GetPropertiesFromDataSet($DataSet) {
		$this->CategoryID = ForceInt(@$DataSet["CategoryID"], 0);
		$this->Name = ForceString(@$DataSet["Name"], "");
		$this->Description = ForceString(@$DataSet["Description"], "");
		$this->DiscussionCount = ForceInt(@$DataSet["DiscussionCount"], 0);
		$this->Blocked = ForceBool(@$DataSet["Blocked"], 0);
	}	

	function GetPropertiesFromForm(&$Context) {
		$this->CategoryID = ForceIncomingInt("CategoryID", 0);
		$this->Name = ForceIncomingString("Name", "");
		$this->Description = ForceIncomingString("Description", "");
		$p = $Context->ObjectFactory->NewObject($this->Context, "Parameters");
		$p->DefineCollection($_POST, "CategoryRoleBlock_", 1);
		while (list($key, $val) = each($p->aParameters)) {
			$RoleID = ForceInt(str_replace("CategoryRoleBlock_","",$key), 0);
			if ($RoleID > 0) $this->AllowedRoles[] = $RoleID;
		}
	}
}

class CategoryManager {
	var $Name;				// The name of this class
   var $Context;			// The context object that contains all global objects (database, error manager, warning collector, session, etc)
	
	function CategoryManager(&$Context) {
		$this->Name = "CategoryManager";
		$this->Context = &$Context;
	}
	
	function GetCategories($IncludeCount = "0", $OrderByPreference = "0") {
		$OrderByPreference = ForceBool($OrderByPreference, 0);
		$s = $this->GetCategoryBuilder($IncludeCount);
		if ($OrderByPreference && $this->Context->Session->UserID > 0) {
			// Order by the user's preference (unblocked categories first)
			$s->AddOrderBy("Blocked", "b", "asc");
		}
		// Order by the category name
		$s->AddOrderBy("Order", "c", "asc");
		$s->AddWhere("c.cat_appname","vanilla","=");
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCategories", "An error occurred while retrieving categories.");
	}
	
	function GetCategoryBuilder($IncludeCount = "0") {
		$IncludeCount = ForceBool($IncludeCount, 0);
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("phpgw_categories", "c","");
		$s->AddSelect(array(array("cat_id","CategoryID"), array("cat_name","Name"), array("cat_description","Description")), "c", "", "", "", 1);
		if ($IncludeCount) {
			if ($this->Context->Session->User->AdminCategories && $this->Context->Session->User->Setting("ShowDeletedDiscussions")) {
				$s->AddJoin("Discussion", "d", "CategoryID", "c", "cat_id", "left join");
			} else {
				$s->AddJoin("Discussion", "d", "CategoryID and d.Active = 1", "c", "cat_id", "left join");
			}
			$s->AddSelect("DiscussionID", "d", "DiscussionCount", "count");
		}
		if ($this->Context->Session->UserID > 0) {
			$s->AddJoin("CategoryBlock", "b", "CategoryID and b.UserID = ".$this->Context->Session->UserID, "c", "cat_id", "left join");
			$s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = ".$this->Context->Session->User->RoleID, "c", "cat_id", "left join");
			$s->AddSelect("Blocked", "b", "Blocked", "coalesce", "0");
		} else {
			$s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = 1", "c", "cat_id", "left join");
		}
		
		$s->AddWhere("coalesce(crb.Blocked, 0)", "0", "=", "and", "", 0, 0);
		$s->AddWhere("c.cat_owner",join(",",array_keys($_SESSION['UserGroups'])).")","in (","and","",0.0);
// 		echo "<!--session: [".join(",",array_keys($_SESSION['UserGroups']))."]-->";
		return $s;
// 		echo "<!--[".print_r($s,1)."]-->";
		
	}

	function GetCategoryById($CategoryID) {
		$Category = $this->Context->ObjectFactory->NewObject($this->Context, "Category");
		$s = $this->GetCategoryBuilder();
		$s->AddWhere("c.cat_id", $CategoryID, "=");
		$ResultSet = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCategoryById", "An error occurred while attempting to retrieve the requested category.");
		if ($this->Context->Database->RowCount($ResultSet) == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrCategoryNotFound"));
		while ($rows = $this->Context->Database->GetRow($ResultSet)) {
			$Category->GetPropertiesFromDataSet($rows);
		}
		return $this->Context->WarningCollector->Iif($Category, false);
	}
	
	function GetCategoryRoleBlocks($CategoryID = "0") {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("Role", "r");
		$s->AddJoin("CategoryRoleBlock", "crb", "RoleID and crb.CategoryID = $CategoryID", "r", "RoleID", "left join");
		$s->AddSelect(array("RoleID", "Name"), "r");
		$s->AddSelect("Blocked", "crb", "Blocked", "coalesce", "0");
		$s->AddWhere("r.Active", "1", "=");
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCategoryRoleBlocks", "An error occurred while retrieving category role blocks.");
	}
		
	function RemoveCategory($RemoveCategoryID, $ReplacementCategoryID) {
		$ReplacementCategoryID = ForceInt($ReplacementCategoryID, 0);
		if ($ReplacementCategoryID <= 0) {
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrCategoryReplacement"));
			return false;
		}
		// Reassign the user-assigned categorizations
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("Discussion", "d");
		$s->AddFieldNameValue("CategoryID", $ReplacementCategoryID);
		$s->AddWhere("CategoryID", $RemoveCategoryID, "=");
		$this->Context->Database->Update($this->Context, $s, $this->Name, "RemoveCategory", "An error occurred while attempting to re-assign user categorizations.");
		
		// remove user blocks
		$s->Clear();
		$s->SetMainTable("CategoryBlock", "b");
		$s->AddWhere("CategoryID", $RemoveCategoryID, "=");
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveCategory", "An error occurred while attempting to remove user-assigned blocks on the selected category.");
		
		// Now remove the category itself
      $s->Clear();
		$s->SetMainTable("Category", "c");
		$s->AddWhere("CategoryID", $RemoveCategoryID, "=");
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveCategory", "An error occurred while attempting to remove the category.");
		return true;
	}
	
	function SaveCategory(&$Category) {
		// Validate the properties
		if($this->ValidateCategory($Category)) {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("Category", "c");
			$s->AddFieldNameValue("Name", $Category->Name);
			$s->AddFieldNameValue("Description", $Category->Description);
			
			// If creating a new object
			if ($Category->CategoryID == 0) {
				$Category->CategoryID = $this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveCategory", "An error occurred while creating a new category.");
			} else 	{
				$s->AddWhere("CategoryID", $Category->CategoryID, "=");
				$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveCategory", "An error occurred while attempting to update the category.");
			}
			
			// Now update the blocked roles
         $s->Clear();
			$s->SetMainTable("CategoryRoleBlock", "crb");
			$s->AddWhere("CategoryID", $Category->CategoryID, "=");
			$this->Context->Database->Delete($this->Context, $s, $this->Name, "SaveCategory", "An error occurred while removing old role block definitions for this category.");
			
			$s->Clear();
			$s->SetMainTable("Role", "r");
			$s->AddSelect("RoleID", "r");
			$s->AddWhere("Active", 1, "=");
			$s->AddWhere("RoleID", "(0,".implode(",",$Category->AllowedRoles).")", "not in", "and", "", 0);
			$BlockedRoles = $this->Context->Database->Select($this->Context, $s, $this->Name, "SaveCategory", "An error occurred while retrieving blocked roles.");
			
			while ($Row = $this->Context->Database->GetRow($BlockedRoles)) {
				$RoleID = ForceInt($Row["RoleID"], 0);
				if ($RoleID > 0) {
					$s->Clear();
					$s->SetMainTable("CategoryRoleBlock", "crb");
					$s->AddFieldNameValue("CategoryID", $Category->CategoryID);
					$s->AddFieldNameValue("RoleID", $RoleID);
					$s->AddFieldNameValue("Blocked", 1);
					$this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveCategory", "An error occurred while adding new role block definitions for this category.");
				}
			}
		}
		return $this->Context->WarningCollector->Iif($Category, false);
	}
	
	function SaveCategoryOrder() {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$ItemCount = ForceIncomingInt("SortItemCount", 0) + 1;
		for ($i = 1; $i < $ItemCount; $i++) {
			$CategoryID = ForceIncomingInt("Sort_".$i, 0);
			if ($CategoryID > 0) {
				$s->Clear();
				$s->SetMainTable("Category", "c");
				$s->AddFieldNameValue("`Order`", $i);
				$s->AddWhere("CategoryID", $CategoryID, "=");
				$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveCategoryOrder", "An error occurred while attempting to update the category sort order.", 0);
			}
		}
	}

	// Validates and formats properties ensuring they're safe for database input
	// Returns: boolean value indicating success
	function ValidateCategory(&$Category) {
		// First update the values so they are safe for db input
		$ValidatedCategory = $Category;
		$ValidatedCategory->FormatPropertiesForDatabaseInput();

		// Instantiate a new validator for each field
		Validate($this->Context->GetDefinition("CategoryNameLower"), 1, $ValidatedCategory->Name, 100, "", $this->Context);
	
		// If validation was successful, then reset the properties to db safe values for saving
		if ($this->Context->WarningCollector->Count() == 0) $Category = $ValidatedCategory;
		
		return $this->Context->WarningCollector->Iif();
	}
}
?>