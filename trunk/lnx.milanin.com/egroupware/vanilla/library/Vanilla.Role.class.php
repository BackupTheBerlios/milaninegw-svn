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
* Description: Container for role properties and a role management class.
*/
class Role {
	var $RoleID;
	var $Name;
	var $Icon;
	var $Description;
	var $CanLogin;
	var $CanPostDiscussion;
	var $CanPostComment;
	var $CanPostHTML;
	var $CanViewIps;
	var $AdminUsers;
	var $AdminCategories;
	var $MasterAdmin;
	var $ShowAllWhispers;
	
	function Clear() {
		$this->RoleID = 0;
		$this->Name = "";
		$this->Icon = "";
		$this->Description = "";
		$this->CanLogin = 0;
		$this->CanPostDiscussion = 0;
		$this->CanPostComment = 0;
		$this->CanPostHTML = 0;
		$this->CanViewIps = 0;
		$this->AdminUsers = 0;
		$this->AdminCategories = 0;
		$this->MasterAdmin = 0;
		$this->ShowAllWhispers = 0;		
	}
	
	function FormatPropertiesForDatabaseInput() {
		$this->Name = FormatStringForDatabaseInput($this->Name, 1);
		$this->Icon = FormatStringForDatabaseInput($this->Icon, 1);
		$this->Description = FormatStringForDatabaseInput($this->Description, 1);
	}
	
	function FormatPropertiesForDisplay() {
		$this->Name = FormatStringForDisplay($this->Name, 0);
		$this->Description = FormatStringForDisplay($this->Description, 0);
	}
	
	function GetPropertiesFromDataSet($DataSet) {
		$this->RoleID = ForceInt(@$DataSet["RoleID"],0);
		$this->Name = ForceString(@$DataSet["Name"],"");
		$this->Icon = ForceString(@$DataSet["Icon"],"");
		$this->Description = ForceString(@$DataSet["Description"],"");
		$this->CanLogin = ForceBool(@$DataSet["CanLogin"],0);
		$this->CanPostDiscussion = ForceBool(@$DataSet["CanPostDiscussion"],0);
		$this->CanPostComment = ForceBool(@$DataSet["CanPostComment"],0);
		$this->CanPostHTML = ForceBool(@$DataSet["CanPostHTML"],0);
		$this->CanViewIps = ForceBool(@$DataSet["CanViewIps"],0);
		$this->AdminUsers = ForceBool(@$DataSet["AdminUsers"],0);
		$this->AdminCategories = ForceBool(@$DataSet["AdminCategories"],0);
		$this->MasterAdmin = ForceBool(@$DataSet["MasterAdmin"],0);
		$this->ShowAllWhispers = ForceBool(@$DataSet["ShowAllWhispers"],0);
	}
	
	function GetPropertiesFromForm() {
		$this->RoleID = ForceIncomingInt("RoleID", 0);
		$this->Name = ForceIncomingString("Name", "");
		$this->Icon = ForceIncomingString("Icon", "");
		$this->Description = ForceIncomingString("Description", "");
		$this->CanLogin = ForceIncomingBool("CanLogin",0);
		$this->CanPostDiscussion = ForceIncomingBool("CanPostDiscussion",0);
		$this->CanPostComment = ForceIncomingBool("CanPostComment",0);
		$this->CanPostHTML = ForceIncomingBool("CanPostHTML",0);
		$this->CanViewIps = ForceIncomingBool("CanViewIps",0);
		$this->AdminUsers = ForceIncomingBool("AdminUsers",0);
		$this->AdminCategories = ForceIncomingBool("AdminCategories",0);
		$this->MasterAdmin = ForceIncomingBool("MasterAdmin", 0);
		$this->ShowAllWhispers = ForceIncomingBool("ShowAllWhispers",0);
	}
	
	function Role() {
		$this->Clear();
	}
}

class RoleManager {
	var $Name;					// The name of this class
   var $Context;				// The context object that contains all global objects (database, error manager, warning collector, session, etc)
	
	// Returns a SqlBuilder object with all of the user properties already defined in the select
	function GetRoleBuilder() {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("Role", "r");
		$s->AddSelect(array("RoleID", "Name", "Icon", "Description", "CanLogin", "CanPostDiscussion", "CanPostComment", "CanPostHTML", "CanViewIps", "AdminUsers", "AdminCategories", "MasterAdmin", "ShowAllWhispers"), "r");
		$s->AddWhere("Active", "1", "=");
		return $s;
	}
	
	function GetRoleById($RoleID) {
		$s = $this->GetRoleBuilder();
		$s->AddWhere("r.RoleID", $RoleID, "=");

		$Role = $this->Context->ObjectFactory->NewObject($this->Context, "Role");
		$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetRoleById", "An error occurred while attempting to retrieve the requested role.");
		if ($this->Context->Database->RowCount($result) == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrRoleNotFound"));
		while ($rows = $this->Context->Database->GetRow($result)) {
			$Role->GetPropertiesFromDataSet($rows);
		}
		
		return $this->Context->WarningCollector->Iif($Role, false);
	}
	
	function GetRoles($RoleToExclude = "0") {
		$RoleToExclude = ForceInt($RoleToExclude, 0);
		$s = $this->GetRoleBuilder();
		$s->AddOrderBy("RoleID", "r", "asc");
		$s->AddWhere("RoleID", $RoleToExclude, "<>");
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetRoles", "An error occurred while attempting to retrieve roles.");
	}
	
	function RemoveRole($RemoveRoleID, $ReplacementRoleID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("User", "u");
		$s->AddSelect("UserID", "u");
		$s->AddWhere("RoleID", $RemoveRoleID, "=");
		$OldRoleUsers = $this->Context->Database->Select($this->Context, $s, $this->Name, "RemoveRole", "An error occurred while attempting to remove the role.");
		
		if ($this->Context->Database->RowCount($OldRoleUsers) > 0) {
			$um = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
			// Reset the role for all of the affected users
         $urh = $this->Context->ObjectFactory->NewObject($this->Context, "UserRoleHistory");
			$urh->RoleID = $ReplacementRoleID;
			$urh->AdminUserID = $this->Context->Session->UserID;
			$urh->Notes = "The user's previous role has been made obselete.";
			while ($row = $this->Context->Database->GetRow($OldRoleUsers)) {
				$urh->UserID = ForceInt($row["UserID"], 0);
				$um->AssignRole($urh);
			}
		}		
		
		$s->Clear();
		$s->SetMainTable("Role", "r");
		$s->AddFieldNameValue("Active", "0");
		$s->AddWhere("RoleID", $RemoveRoleID, "=");
		$this->Context->Database->Update($this->Context, $s, $this->Name, "RemoveRole", "An error occurred while attempting to remove the role.");
		return 1;
	}
	
	function RoleManager(&$Context) {
		$this->Name = "RoleManager";
		$this->Context = &$Context;
	}	

	function SaveRole(&$Role) {
		// Ensure that the person performing this action has access to do so
		if (!$this->Context->Session->User->AdminUsers) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionInsufficient"));
		
		if ($this->Context->WarningCollector->Count() == 0) {
			// Validate the properties
			if($this->ValidateRole($Role)) {
				$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
				$s->SetMainTable("Role", "r");
				$s->AddFieldNameValue("Name", $Role->Name);
				$s->AddFieldNameValue("Icon", $Role->Icon);
				$s->AddFieldNameValue("Description", $Role->Description);
				$s->AddFieldNameValue("CanLogin", $Role->CanLogin);
				$s->AddFieldNameValue("CanPostDiscussion", $Role->CanPostDiscussion);
				$s->AddFieldNameValue("CanPostComment", $Role->CanPostComment);
				$s->AddFieldNameValue("CanPostHTML", $Role->CanPostHTML);
				$s->AddFieldNameValue("CanViewIps", $Role->CanViewIps);
				$s->AddFieldNameValue("AdminUsers", $Role->AdminUsers);
				$s->AddFieldNameValue("AdminCategories", $Role->AdminCategories);
				$s->AddFieldNameValue("MasterAdmin", $Role->MasterAdmin);
				$s->AddFieldNameValue("ShowAllWhispers", $Role->ShowAllWhispers);
				if ($Role->RoleID > 0) {
					$s->AddWhere("RoleID", $Role->RoleID, "=");
					$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveRole", "An error occurred while attempting to update the role.");
				} else {
					$Role->RoleID = $this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveRole", "An error occurred while creating a new role.");
				}
			}
		}
		return $this->Context->WarningCollector->Iif($Role, false);
	}
	
	
	// Validates and formats Role properties ensuring they're safe for database input
	// Returns: boolean value indicating success
	function ValidateRole(&$Role) {
		$ValidatedRole = $Role;
		$ValidatedRole->FormatPropertiesForDatabaseInput();
		
		Validate($this->Context->GetDefinition("RoleNameLower"), 1, $ValidatedRole->Name, 100, "", $this->Context);
		
		// If validation was successful, then reset the properties to db safe values for saving
		if ($this->Context->WarningCollector->Count() == 0) $Role = $ValidatedRole;
		
		return $this->Context->WarningCollector->Iif();
	}
}
?>