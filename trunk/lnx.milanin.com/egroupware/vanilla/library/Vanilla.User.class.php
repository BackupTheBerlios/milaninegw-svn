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
* Description: Container for user properties and a user management class.
*/
class User {
	// Basic User Properties
	var $UserID;
	var $RoleID;
	var $Role;
	var $RoleIcon;
	var $RoleDescription;
	var $StyleID;
	var $Style;
	var $StyleUrl;
	var $CustomStyle;
	var $Name;
	var $FirstName;
	var $LastName;
	var $FullName;
	var $ShowName;
	var $Password;
	var $Email;
	var $UtilizeEmail;
	var $Icon;
	var $Picture;
	var $Attributes;
	var $DateFirstVisit;
	var $DateLastActive;
	var $CountVisit;
	var $CountDiscussions;
	var $CountComments;
	var $RemoteIp;
	var $AgreeToTerms;
	var $ReadTerms;
	var $BlocksCategories;
	var $DefaultFormatType;
	var $Discovery;
	var $DisplayIcon;				// The icon to display for the user. Normally the user-defined icon, but if the user has a role icon it will appear here instead

	// Spam blocking variables
	var $LastDiscussionPost;
	var $DiscussionSpamCheck;
	var $LastCommentPost;
	var $CommentSpamCheck;

	// Access Abilities (relating to the user role)
	var $CanLogin;
	var $CanPostDiscussion;
	var $CanPostComment;
	var $CanPostHTML;
	var $CanViewIps;
	var $AdminUsers;
	var $AdminCategories;
	var $MasterAdmin;
	var $ShowAllWhispers;
	var $SendNewApplicantNotifications;
	var $UserMainGroup;
	var $UserMainGroupName;
	var $UserGroups;
	
	// Password Manipulation Properties
	var $OldPassword;
	var $NewPassword;
	var $ConfirmPassword;

	// An associative array of user-defined settings as defined by LUM_UserSetting & LUM_Setting
	var $Settings;
	
	var $Context;
	
	//EGW
        var $EGW_Sessions_Timeout;
   
	function Clear() {
		$this->UserID = 0;
		$this->RoleID = 0;
		$this->Role = "";
		$this->RoleIcon = "";
		$this->RoleDescription = "";
		$this->StyleID = 0;
		$this->Style = "";
		$this->StyleUrl = "";
		$this->CustomStyle = "";
		$this->Name = "";
		$this->FirstName = "";
		$this->LastName = "";
		$this->FullName = "";
		$this->ShowName = 1;
		$this->Password = "";
		$this->Email = "";
		$this->UtilizeEmail = 0;
		$this->Icon = "";
		$this->Picture = "";
		$this->Attributes = array();
		$this->DateFirstVisit = "";
		$this->DateLastActive = "";
		$this->CountVisit = 0;
		$this->CountDiscussions = 0;
		$this->CountComments = 0;
		$this->RemoteIp = "";
		$this->AgreeToTerms = 0;
		$this->ReadTerms = 0;
		$this->BlocksCategories = 0;
		$this->DefaultFormatType = agDEFAULTSTRINGFORMAT;
		$this->Discovery = "";
		$this->DisplayIcon = "";
		$this->SendNewApplicantNotifications = 0;
		$this->UserMainGroup=array();
		$this->UserGroups=array();
		
		$this->Settings = array();
		
		$this->CanLogin = 1;
		$this->CanPostDiscussion = 0;
		$this->CanViewIps = 0;
		$this->CanPostComment = 0;
		$this->CanPostHTML = 0;
		$this->AdminUsers = 0;
		$this->AdminCategories = 0;
		$this->MasterAdmin = 0;
		$this->ShowAllWhispers = 0;
		$this->EGW_Sessions_Timeout=1800;
	}
	
	function FormatPropertiesForDatabaseInput() {
		$this->CustomStyle = FormatStringForDatabaseInput($this->CustomStyle, 1);
		$this->Name = FormatStringForDatabaseInput($this->Name, 1);
		$this->FirstName = FormatStringForDatabaseInput($this->FirstName, 1);
		$this->LastName= FormatStringForDatabaseInput($this->LastName, 1);
		$this->Email = FormatStringForDatabaseInput($this->Email, 1);
		$this->Icon = FormatStringForDatabaseInput($this->Icon, 1);
		$this->Picture = FormatStringForDatabaseInput($this->Picture, 1);
		$this->Password = FormatStringForDatabaseInput($this->Password, 1);
		$this->OldPassword = FormatStringForDatabaseInput($this->OldPassword, 1);
		$this->NewPassword = FormatStringForDatabaseInput($this->NewPassword, 1);
		$this->ConfirmPassword = FormatStringForDatabaseInput($this->ConfirmPassword, 1);
		$this->Attributes = SerializeArray($this->Attributes);
		$this->Discovery = FormatStringForDatabaseInput($this->Discovery, 1);
	}
	
	function FormatPropertiesForDisplay() {
		$this->Name = FormatStringForDisplay($this->Name, 1);
		$this->FirstName = FormatStringForDisplay($this->FirstName, 1);
		$this->LastName = FormatStringForDisplay($this->LastName, 1);
		$this->FullName = FormatStringForDisplay($this->FullName, 1);
		$this->Email = FormatStringForDisplay($this->Email, 1);
		$this->Password = "";
		$this->Picture = FormatStringForDisplay($this->Picture, 0);
		$this->Icon = FormatStringForDisplay($this->Icon, 0);
		$this->DisplayIcon = FormatStringForDisplay($this->DisplayIcon, 0);
		$this->Style = FormatStringForDisplay($this->Style, 0);
	}
	
	function GetPropertiesFromDataSet($DataSet) {
		$this->UserID = ForceInt(@$DataSet["UserID"],0);
		$this->RoleID = ForceInt(@$DataSet["RoleID"],0);
		$this->Role = ForceString(@$DataSet["Role"],"");
		$this->UserMainGroup = array(@$DataSet["UserMainGroup"]=>@$DataSet["UserMainGroupName"]);
		if ($this->RoleID == 0 && $this->Context) $this->Role = $this->Context->GetDefinition("Applicant");
		$this->RoleIcon = ForceString(@$DataSet["RoleIcon"],"");
		$this->RoleDescription = ForceString(@$DataSet["RoleDescription"],"");
		$this->StyleID = ForceInt(@$DataSet["StyleID"], 0);
		$this->Style = ForceString(@$DataSet["Style"], "");
		$this->StyleUrl = ForceString(@$DataSet["StyleUrl"], "");
		$this->CustomStyle = ForceString(@$DataSet["CustomStyle"], "");
		$this->Name = ForceString(@$DataSet["Name"],"");
		$this->FirstName = ForceString(@$DataSet["FirstName"], "");
		$this->LastName= ForceString(@$DataSet["LastName"], "");
		$this->FullName = ForceString(@$DataSet["FullName"], "");
		$this->ShowName = ForceBool(@$DataSet["ShowName"], 0);
		$this->Email = ForceString(@$DataSet["Email"],"");
		$this->UtilizeEmail = ForceBool(@$DataSet["UtilizeEmail"], 0);
		$this->Icon = ForceString(@$DataSet["Icon"], "");
		$this->Picture = ForceString(@$DataSet["Picture"],"");
		$this->Discovery = ForceString(@$DataSet["Discovery"], "");
		$this->Attributes = "";
		$this->Attributes = ForceString(@$DataSet["Attributes"],"");
		$this->Attributes = UnserializeArray($this->Attributes);
		$this->Attributes = UnserializeArray($this->Attributes);
		$this->UserGroups = $this->GetUsersEGWGroupByUserID($this->UserID);
		$this->SendNewApplicantNotifications = ForceBool(@$DataSet["SendNewApplicantNotifications"], 0);
		if ($this->RoleIcon != "") {
			$this->DisplayIcon = $this->RoleIcon;
		} else {
			$this->DisplayIcon = $this->Icon;
		}
		
		$this->Settings = "";
		$this->Settings = ForceString(@$DataSet["Settings"],"");
		$this->Settings = UnserializeAssociativeArray($this->Settings);
		$this->DateFirstVisit = UnixTimestamp(@$DataSet["DateFirstVisit"]);
		$this->DateLastActive = UnixTimestamp(@$DataSet["DateLastActive"]);
		$this->CountVisit = ForceInt(@$DataSet["CountVisit"],0);
		$this->CountDiscussions = ForceInt(@$DataSet["CountDiscussions"],0);
		$this->CountComments = ForceInt(@$DataSet["CountComments"],0);
		$this->RemoteIp = ForceString(@$DataSet["RemoteIp"],"");
		$this->BlocksCategories = ForceBool(@$DataSet["UserBlocksCategories"], 0);
		$this->DefaultFormatType = ForceString(@$DataSet["DefaultFormatType"], agDEFAULTSTRINGFORMAT);
		
		$this->CanLogin = ForceBool(@$DataSet["CanLogin"],0);
		$this->CanPostDiscussion = ForceBool(@$DataSet["CanPostDiscussion"],0);
		$this->CanViewIps = ForceBool(@$DataSet["CanViewIps"], 0);
		$this->CanPostComment = ForceBool(@$DataSet["CanPostComment"],0);
		$this->CanPostHTML = ForceBool(@$DataSet["CanPostHTML"],0);
		$this->AdminUsers = ForceBool(@$DataSet["AdminUsers"],0);
		$this->AdminCategories = ForceBool(@$DataSet["AdminCategories"], 0);
		$this->MasterAdmin = ForceBool(@$DataSet["MasterAdmin"], 0);
		$this->ShowAllWhispers = ForceBool(@$DataSet["ShowAllWhispers"], 0);
		
		if (!$this->AdminCategories) {
			$this->Setting["ShowDeletedDiscussions"] = 0;
			$this->Setting["ShowDeletedComments"] = 0;
		}
			
		// change the user's style if they've selected no style
		if ($this->StyleID == 0) {
			$this->Style = "Custom";
			$this->StyleUrl = ForceString($this->CustomStyle, agDEFAULT_STYLE);
		}
	}
	function GetUsersEGWGroupByUserID($UserID){
                $UserGroups=array();
                //$RecordsToReturn = ForceInt($RecordsToReturn, 0);
                $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("phpgw_acl", "acl","");
		$s->AddSelect("account_lid","grp", 'EGWGroup');
		$s->AddSelect("account_id","grp", 'EGWGroupID');
		$s->AddJoin("phpgw_accounts","grp","account_id","acl","acl_location","left join","");
		$s->AddWhere("acl.acl_rights","1", "=");
                $s->AddWhere("acl.acl_appname","phpgw_group","=","and");
                $s->AddWhere("acl.acl_account",$UserID,"=","and");
		$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetUsersEGWGroupByUserID", "An error occurred while attempting to retrieve the requested user information.");
			while ($rows = $this->Context->Database->GetRow($result)) {
				$UserGroups["".$rows["EGWGroupID"].""] = $rows["EGWGroup"];
                        }
                return $UserGroups;
}
	function GetPropertiesFromForm() {
		$this->UserID = ForceIncomingInt("u", 0);
		$this->RoleID = ForceIncomingInt("RoleID", 0);
		$this->StyleID = ForceIncomingInt("StyleID", 0);
		$this->CustomStyle = ForceIncomingString("CustomStyle", "");
		$this->Name = ForceIncomingString("Name", "");
		$this->FirstName = ForceIncomingString("FirstName", "");
		$this->LastName = ForceIncomingString("LastName", "");
		$this->ShowName = ForceIncomingBool("ShowName", 0);
		$this->Email = ForceIncomingString("Email", "");
		$this->UtilizeEmail = ForceIncomingBool("UtilizeEmail",0);
		$this->Password = ForceIncomingString("Password", "");
		$this->Icon = PrependString("http://", ForceIncomingString("Icon",""));
		$this->Picture = PrependString("http://", ForceIncomingString("Picture",""));
		$this->AgreeToTerms = ForceIncomingBool("AgreeToTerms", 0);		
		$this->ReadTerms = ForceIncomingBool("ReadTerms", 0);
		$this->Discovery = ForceIncomingString("Discovery", "");
		
		$this->OldPassword = ForceIncomingString("OldPassword", "");
		$this->NewPassword = ForceIncomingString("NewPassword", "");
		$this->ConfirmPassword = ForceIncomingString("ConfirmPassword", "");
		
		// Retrieve attributes from the form
		$AttributeCount = ForceIncomingInt("LabelValuePairCount", 0);
		$Label = "";
		$Value = "";
		for ($i = 0; $i < $AttributeCount; $i++) {
			$Label = ForceIncomingString("Label".($i+1), "");
			$Label = strip_tags($Label);
			$Label = str_replace("\\\"", "", $Label);
			$Value = ForceIncomingString("Value".($i+1), "");
			$Value = strip_tags($Value);
			$Value = str_replace("\\\"", "", $Value);
			if ($Label != "" && $Value != "") $this->Attributes[] = array("Label" => $Label, "Value" => $Value);
		}
	}
	
	// Call this method to retrieve a setting (boolean) value rather than accessing the settings array directly and catching an error if the particular setting is not defined
	function Setting($SettingName, $DefaultValue = "0") {
		if (array_key_exists($SettingName, $this->Settings)) {
			return ForceBool($this->Settings[$SettingName], 0);
		} else {
			return ForceBool($DefaultValue, 0);
		}
	}
	
	function User(&$Context) {
		$this->Context = &$Context;
	}
}

class UserRoleHistory {
	var $UserID;
	var $Username;
	var $FullName;
	var $RoleID;
	var $Role;
	var $RoleDescription;
	var $RoleIcon;
	var $AdminUserID;
	var $AdminUsername;
	var $AdminFullName;
	var $Notes;
	var $Date;
	
	function Clear() {
		$this->UserID = 0;
		$this->Username = "";
		$this->FullName = "";
		$this->RoleID = 0;
		$this->Role = "";
		$this->RoleDescription = "";
		$this->RoleIcon = "";
		$this->AdminUserID = 0;
		$this->AdminUsername = "";
		$this->AdminFullName = "";
		$this->Notes = "";
		$this->Date = "";
	}
	
	function FormatPropertiesForDisplay(&$Context) {
		$this->Username = FormatStringForDisplay($this->Username, 0);
		$this->FullName = FormatStringForDisplay($this->FullName, 0);
		$this->AdminUsername = FormatStringForDisplay($this->AdminUsername, 0);
		$this->AdminFullName = FormatStringForDisplay($this->AdminFullName, 0);
		$AdminUser = $Context->ObjectFactory->NewObject($Context, "Comment");
		$AdminUser->Clear();
		$AdminUser->AuthUsername = $this->AdminUsername;
		$AdminUser->AuthUserID = $this->AdminUserID;
		$this->Notes = $Context->FormatString($this->Notes, $AdminUser, "Text", agFORMATSTRINGFORDISPLAY);
	}
	
	function GetPropertiesFromDataSet($DataSet) {
		$this->UserID = ForceInt(@$DataSet['UserID'],0);
		$this->Username = ForceString(@$DataSet['Username'],"");
		$this->FullName = ForceString(@$DataSet['FullName'],"");
		$this->RoleID = ForceInt(@$DataSet['RoleID'],0);
		$this->Role = ForceString(@$DataSet['Role'],"");
		$this->RoleDescription = ForceString(@$DataSet['RoleDescription'],"");
		$this->RoleIcon = ForceString(@$DataSet['RoleIcon'],"");
		$this->AdminUserID = ForceInt(@$DataSet['AdminUserID'],0);
		$this->AdminUsername = ForceString(@$DataSet['AdminUsername'],"");
		$this->AdminFullName =ForceString(@$DataSet['AdminFullName'],"");
		$this->Notes = ForceString(@$DataSet['Notes'],"");
		$this->Date = UnixTimestamp(@$DataSet["Date"]);
	}
	
	function GetPropertiesFromForm() {
		$this->UserID = ForceIncomingInt("u", 0);
		$this->RoleID = ForceIncomingInt("RoleID", 0);
		$this->Notes = ForceIncomingString("Notes", "");
	}
}


class UserManager {
	var $Name;				// The name of this class
   var $Context;			// The context object that contains all global objects (database, error manager, warning collector, session, etc)
	
	function AddBookmark($UserID, $DiscussionID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("UserBookmark", "b");
		$s->AddFieldNameValue("UserID", $UserID);
		$s->AddFieldNameValue("DiscussionID", $DiscussionID);
		$this->Context->Database->Insert($this->Context, $s, $this->Name, "AddBookmark", "An error occurred while adding the bookmark.");
	}
	
	function AddCategoryBlock($CategoryID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("CategoryBlock", "b");
		$s->AddFieldNameValue("UserID", $this->Context->Session->UserID);
		$s->AddFieldNameValue("CategoryID", $CategoryID);
		$s->AddFieldNameValue("Blocked", 1);
		// Don't stress over errors (ie. duplicate entries) since this is indexed and duplicates cannot be inserted
		if ($this->Context->Database->Insert($this->Context, $s, $this->Name, "AddCategoryBlock", "Failed to add category block.", 0, 0)) {
			$s->Clear();
			$s->SetMainTable("User", "u");
			$s->AddFieldNameValue("UserBlocksCategories", "1");
			$s->AddWhere("UserID", $this->Context->Session->UserID, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "AddCategoryBlock", "Failed to update category block.", 0);
		}
	}

	function AddCommentBlock($CommentID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("CommentBlock", "b");
		$s->AddFieldNameValue("BlockingUserID", $this->Context->Session->UserID);
		$s->AddFieldNameValue("BlockedCommentID", $CommentID);
		$s->AddFieldNameValue("Blocked", 1);
		// Don't stress over errors (ie. duplicate entries) since this is indexed and duplicates cannot be inserted
		$this->Context->Database->Insert($this->Context, $s, $this->Name, "AddCommentBlock", "Failed to add comment block.", 0, 0);
	}

	function AddUserBlock($UserID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("UserBlock", "b");
		$s->AddFieldNameValue("BlockingUserID", $this->Context->Session->UserID);
		$s->AddFieldNameValue("BlockedUserID", $UserID);
		$s->AddFieldNameValue("Blocked", 1);
		// Don't stress over errors (ie. duplicate entries) since this is indexed and duplicates cannot be inserted
		$this->Context->Database->Insert($this->Context, $s, $this->Name, "AddCommentBlock", "Failed to add user block.", 0, 0);
	}
	
	function ApproveApplicant($ApplicantID) {
		$urh = $this->Context->ObjectFactory->NewObject($this->Context, "UserRoleHistory");
		$urh->UserID = $ApplicantID;
		$urh->Notes = $this->Context->GetDefinition("NewMemberWelcomeAboard");
		$urh->RoleID = agAPPROVAL_ROLE;
		$this->AssignRole($urh);
		return $this->Context->WarningCollector->Iif();
	}
	
	function AssignRole($UserRoleHistory, $NewUser = "0") {
		$NewUser = ForceBool($NewUser, 0);
		if (!$this->Context->Session->User->AdminUsers && !$NewUser) {
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionInsufficient"));
		} elseif ($UserRoleHistory->Notes == "") {
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrRoleNotes"));
		} else {			
			// Assign the user to the role first
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddFieldNameValue("RoleID", $UserRoleHistory->RoleID);
			$s->AddWhere("UserID", $UserRoleHistory->UserID, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "AssignRole", "An error occurred while assigning the user to a role.");
			
			// Now record the change
			$UserRoleHistory->Notes = FormatStringForDatabaseInput($UserRoleHistory->Notes);
			$s->Clear();
			$s->SetMainTable("UserRoleHistory", "h");
			$s->AddFieldNameValue("UserID", $UserRoleHistory->UserID);
			$s->AddFieldNameValue("RoleID", $UserRoleHistory->RoleID);
			$s->AddFieldNameValue("Date", MysqlDateTime());
			$s->AddFieldNameValue("AdminUserID", ($NewUser?0:$this->Context->Session->UserID));
			$s->AddFieldNameValue("Notes", $UserRoleHistory->Notes);
			$s->AddFieldNameValue("RemoteIp", GetRemoteIp(1));
			$this->Context->Database->Insert($this->Context, $s, $this->Name, "AssignRole", "An error occurred while recording the role change.");
			
			// Now email the user about the role change
         if (!$NewUser) {
				// Retrieve user information
            $AffectedUser = $this->GetUserById($UserRoleHistory->UserID);
					
				$e = $this->Context->ObjectFactory->NewContextObject($this->Context, "Email");
				$e->HtmlOn = 0;
				$e->WarningCollector = &$this->Context->WarningCollector;
				$e->ErrorManager = &$this->Context->ErrorManager;
				$e->AddFrom(agSUPPORT_EMAIL, agSUPPORT_NAME);
				$e->AddRecipient($AffectedUser->Email, $AffectedUser->Name);
				$e->Subject = agAPPLICATION_TITLE." ".$this->Context->GetDefinition("AccountChangeNotification");
				$e->BodyText = $this->Context->GetDefinition("MessageToInformYou").agAPPLICATION_TITLE.$this->Context->GetDefinition("HasBeenChanged").strtolower($AffectedUser->Role).$this->Context->GetDefinition("Status");
				if ($AffectedUser->CanLogin) {
					$e->BodyText .= $this->Context->GetDefinition("YouCanSignInTo")
					.agAPPLICATION_TITLE
					.$this->Context->GetDefinition("At")
					."http://"					
					.agDOMAIN
					.(substr(agDOMAIN, strlen(agDOMAIN)-1) == "/" ? "":"/")
					.$this->Context->GetDefinition("ReviewRoleChange");
				}
				$e->Send();
			}
		}
		return $this->Context->WarningCollector->Iif();
	}
	
	function ChangePassword($User) {
		// Ensure that the person performing this action has access to do so
		// Everyone can edit themselves
		if ($this->Context->Session->UserID != $User->UserID && !$this->Context->Session->User->AdminUsers) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionInsufficient"));
		$User->FormatPropertiesForDatabaseInput();
		if ($this->Context->WarningCollector->Count() == 0) {
			// Ensure that the supplied "old password" is valid
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddSelect("UserID", "u");
			$s->AddWhere("Password", $User->OldPassword, "=", "and", "md5");
			$s->AddWhere("UserID", $User->UserID, "=");
			$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "ChangePassword", "An error occurred while validating the user's old password.");
			if ($this->Context->Database->RowCount($Result) == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrOldPasswordBad"));
		}
		
		// Validate inputs
		Validate($this->Context->GetDefinition("NewPasswordLower"), 1, $User->NewPassword, 100, "", $this->Context);
		if ($User->NewPassword != $User->ConfirmPassword) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrNewPasswordMatchBad"));

		if ($this->Context->WarningCollector->Count() == 0) {
			$s->Clear();
			$s->SetMainTable("User", "u");
			$s->AddFieldNameValue("Password", $User->NewPassword, 1, "md5");
			$s->AddWhere("UserID", $User->UserID, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "ChangePassword", "An error occurred while attempting to update the password.");
		}
		return $this->Context->WarningCollector->Iif();
	}
	
	function CreateUser($User) {
		$User->FormatPropertiesForDatabaseInput();
//                 echo "<!--validation starting-->\n";
		// Instantiate a new validator for each field
		Validate($this->Context->GetDefinition("FirstNameLower"), 1, $User->FirstName, 50, "", $this->Context);
		Validate($this->Context->GetDefinition("LastNameLower"), 1, $User->LastName, 50, "", $this->Context);
		Validate($this->Context->GetDefinition("EmailLower"), 1, $User->Email, 200, "(.+)@(.+)\.(.+)", $this->Context);
		Validate($this->Context->GetDefinition("UsernameLower"), 1, $User->Name, 20, "", $this->Context);
		//Validate($this->Context->GetDefinition("PasswordLower"), 1, $User->NewPassword, 50, "", $this->Context);
// 		echo "<!--validation successful-->\n";
		//if ($User->Discovery == "") $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrDiscovery"));
		//if ($User->NewPassword != $User->ConfirmPassword) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPasswordsMatchBad"));
		//if (!$User->AgreeToTerms) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrAgreeTOS"));
		//if (!$User->ReadTerms) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrReadTOS"));
		
		// Ensure the username isn't taken already
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("User", "u");
		$s->AddSelect("UserID", "u");
		$s->AddWhere("Name", $User->Name, "=");
		$MatchCount = 0;
		$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "CreateUser", "A fatal error occurred while validating your input.");
		$MatchCount = $this->Context->Database->RowCount($result);
		if ($MatchCount > 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrUsernameTaken"));
// 		echo "<!--validation successful again [".$this->Context->WarningCollector->Count()."] -->\n";
		// If validation was successful
		if ($this->Context->WarningCollector->Count() == 0) {
			$s->Clear();
			$s->SetMainTable("User", "u");
			$s->AddFieldNameValue("UserID", $User->UserID);
			$s->AddFieldNameValue("FirstName", $User->FirstName);
			$s->AddFieldNameValue("LastName", $User->LastName);
			$s->AddFieldNameValue("Name", $User->Name);
			$s->AddFieldNameValue("Email", $User->Email);
			$s->AddFieldNameValue("Password", $User->NewPassword, 1, "md5");
			$s->AddFieldNameValue("Discovery", $User->Discovery);
			$s->AddFieldNameValue("DateFirstVisit", MysqlDateTime());
			$s->AddFieldNameValue("DateLastActive", MysqlDateTime());
			$s->AddFieldNameValue("CountVisit", 0);
			$s->AddFieldNameValue("CountDiscussions", 0);
			$s->AddFieldNameValue("CountComments", 0);
			$s->AddFieldNameValue("RoleID", agDEFAULT_ROLE);
			$s->AddFieldNameValue("StyleID", 1);
			$s->AddFieldNameValue("UtilizeEmail", 0);
			$s->AddFieldNameValue("RemoteIP", GetRemoteIp(1));
			$User->UserID = $this->Context->Database->Insert($this->Context, $s, $this->Name, "CreateUser", "An error occurred while creating a new user.");
// 			echo "<!--inserted successfuly -->\n";	
			$Urh = $this->Context->ObjectFactory->NewObject($this->Context, "UserRoleHistory");
			$Urh->UserID = $User->UserID;
			$Urh->AdminUserID = 0;
			$Urh->RoleID = agDEFAULT_ROLE;
			if (agALLOW_IMMEDIATE_ACCESS) {
				$Urh->Notes = $this->Context->GetDefinition("RegistrationAccepted");
			} else {
				$Urh->Notes = $this->Context->GetDefinition("RegistrationPendingApproval");
			}
			$this->AssignRole($Urh, 1);
			
			// Notify user administrators
         if (!agALLOW_IMMEDIATE_ACCESS) {
				$s->Clear();
				$s->SetMainTable("User", "u");
				$s->AddJoin("Role", "r", "RoleID", "u", "RoleID", "inner join");
				$s->AddWhere("r.AdminUsers", 1, "=");
				$s->AddWhere("u.SendNewApplicantNotifications", 1, "=");
				$s->AddSelect(array("Name", "Email"), "u");
				$Administrators = $this->Context->Database->Select($this->Context, $s, $this->Name, "CreateUser", "An error occurred while retrieving administrator email addresses.", 0);
				// Fail silently if an error occurs while notifying administrators
				if ($Administrators) {
					if ($this->Context->Database->RowCount($Administrators) > 0) {
						$e = $this->Context->ObjectFactory->NewContextObject($this->Context, "Email");
						$e->HtmlOn = 0;
						$e->ErrorManager = &$this->Context->ErrorManager;
						$e->WarningCollector = &$this->Context->WarningCollector;
						$e->AddFrom(agSUPPORT_EMAIL, agSUPPORT_NAME);
						$AdminEmail = "";
						$AdminName = "";
						while ($Row = $this->Context->Database->GetRow($Administrators)) {
							$AdminEmail = ForceString($Row["Email"], "");
							$AdminName = ForceString($Row["Name"], "");
							if ($AdminEmail != "") $e->AddRecipient($AdminEmail, $AdminName);
						}
						$e->Subject = $this->Context->GetDefinition("NewCaps")." ".agAPPLICATION_TITLE." ".$this->Context->GetDefinition("Applicant");
						$e->BodyText = $this->Context->GetDefinition("ApplicationCompletedBy")
							." "
							.$User->Name
							." "
							.$this->Context->GetDefinition("For")
							." "
							.agAPPLICATION_TITLE
							.$this->Context->GetDefinition("ApplicantSuppliedInformation")
							.FormatHtmlStringInline($User->Discovery, 1)
							.$this->Context->GetDefinition("ReviewApplication")
							."http://"
							.agDOMAIN;
						@$e->Send();
					}				
				}
			}
		}
		return $this->Context->WarningCollector->Iif();
	}
	
	function DefineVerificationKey() {
		// Define the key as an MD5'd string containing 
		// the user's current ip (minus "." delimiters) 
		// concatentated with the current unix timestamp.
		return md5(str_replace(".","",GetRemoteIp()).time());
	}
	
	function GetApplicantCount() {
		$ApplicantData = $this->GetUsersByRoleId(0);
		if ($ApplicantData) {
			return $this->Context->Database->RowCount($ApplicantData);
		} else {
			return 0;
		}
	}

	function GetIpHistory($UserID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("IpHistory", "i");
		$s->AddSelect("IpHistoryID", "i", "UsageCount", "count");
		$s->AddSelect("RemoteIp", "i");
		$s->AddGroupBy("RemoteIp", "i");
		$s->AddWhere("UserID", $UserID, "=");
		$ResultSet = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetIpHistory", "An error occurred while retrieving historical IP usage data.");
		$IpData = array();
		$SharedWith = array();
		$CurrentIp = "";
		$UsageCount = 0;
		$SharedUserName = "";
		$SharedUserID = "";
		while ($Row = $this->Context->Database->GetRow($ResultSet)) {
			$CurrentIp = ForceString($Row["RemoteIp"], "");
			$UsageCount = ForceInt($Row["UsageCount"], 0);
			$UserData = $this->GetUsersByIp($CurrentIp);
			while ($UserRow = $this->Context->Database->GetRow($UserData)) {
				$SharedUserName = ForceString($UserRow["Name"], "");
				$SharedUserID = ForceInt($UserRow["UserID"], 0);
				if ($SharedUserID > 0 && $SharedUserID != $UserID) {
					$SharedWith[] = array("UserID" => $SharedUserID, "Name" => $SharedUserName);
				}
			}
			$IpData[] = array("IP" => $CurrentIp, "UsageCount" => $UsageCount, "SharedWith" => $SharedWith);
			$SharedWith = array();
		}
		return $IpData;
	}
	
	// Returns a SqlBuilder object with all of the user properties already defined in the select
	function GetUserBuilder() {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("User", "u");
		$s->AddSelect(array(array("account_id","UserID"), array("account_lid","Name"), array("account_primary_group ","UserMainGroup"),array("account_firstname","FirstName"), array("account_lastname","LastName"), array("account_email","Email")),"egwu");
                $s->AddSelect("'/members/_icons/data/'","","Icon","concat","mi.filename");
                $s->AddSelect("'/members/_icons/data/'","","Picture","concat","mi.filename");
                $s->AddSelect(array("Attributes", "CountVisit", "CountDiscussions", "CountComments", "RemoteIp", "DateFirstVisit", "DateLastActive", "RoleID", "StyleID", "CustomStyle", "ShowName", "UserBlocksCategories", "DefaultFormatType", "Discovery", "SendNewApplicantNotifications","UtilizeEmail"),"u");
		$s->AddSelect(array("CanLogin", "CanPostDiscussion", "CanPostComment", "CanPostHTML", "CanViewIps", "AdminUsers", "AdminCategories", "MasterAdmin", "ShowAllWhispers"), "r");
		$s->AddSelect("Name", "r", "Role");
		$s->AddSelect("account_firstname", "egwu", "FullName", "concat", "' ',egwu.account_lastname");
		$s->AddSelect("Description", "r", "RoleDescription");
		$s->AddSelect("Icon", "r", "RoleIcon");
		$s->AddSelect("Url", "s", "StyleUrl");
		$s->AddSelect("Name", "s", "Style");
		$s->AddSelect("account_lid", "egwg", "UserMainGroupName");
		$s->AddJoin("Role", "r", "RoleID", "u", "RoleID", "left join");
		$s->AddJoin("Style", "s", "StyleID", "u", "StyleID", "left join");
		$s->AddJoin("phpgw_accounts","egwu","account_id","u","UserID","left join","");
		$s->AddJoin("phpgw_accounts","egwg","account_id","egwu","account_primary_group","left join","");
		$s->AddJoin("users","mu","ident","egwu","account_id","left join","members_");
		$s->AddJoin("icons","mi","ident","mu","icon","left join","members_");
		return $s;
	}
	function EGW_GetUserById($UserID) {
                $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("phpgw_accounts", "egwu","");
		$s->AddSelect(array(array("account_id","UserID"), array("account_lid","Name"), array("account_firstname","FirstName"), array("account_lastname","LastName"), array("account_email","Email")),"egwu");
		$s->AddWhere("account_id", $UserID, "=");
		return $s;
        }
        
	function GetUserById($UserID) {
		$s = $this->GetUserBuilder();
		$s->AddWhere("UserID", $UserID, "=");

		$User = $this->Context->ObjectFactory->NewContextObject($this->Context, "User");
		$UserData = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetUserById", "An error occurred while attempting to retrieve the requested user.");
		if ($this->Context->Database->RowCount($UserData) == 0) {
                        $t_s = $this->EGW_GetUserById($UserID);
                        $EGW_UserData = $this->Context->Database->Select($this->Context, $t_s, $this->Name, "EGW_GetUserById", "An error occurred while attempting to retrieve the requested user from EGW.");
                        if ($this->Context->Database->RowCount($EGW_UserData) == 0) {
                            $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrUserNotFound"));
                        } else {
                            while ($rows = $this->Context->Database->GetRow($EGW_UserData)) {
                                $rows['NewPassword'] = md5(rand());
                                $rows['ConfirmPassword']=$rows['NewPassword'];
                                $rows['RoleID']='1';
//                                 echo "<!--[".print_r($rows,1)."]-->\n";
				$User->GetPropertiesFromDataSet($rows);
                            }
//                             echo "<!-- a new one, creating -->\n";
                            $this->CreateUser($User);
//                             echo "<!-- a new one  created -->\n";
                        }
		} else {
			while ($rows = $this->Context->Database->GetRow($UserData)) {
				$User->GetPropertiesFromDataSet($rows);
			}
		}
		
		return $this->Context->WarningCollector->Iif($User, false);
	}
	
	function GetUserIdByName($Username) {
		$Username = FormatStringForDatabaseInput(ForceString($Username, ""), 1);
		$UserID = 0;
		if ($Username != "") {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddSelect("UserID", "u");
			$s->AddWhere("Name", $Username, "=");
			$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetUserIdByName", "An error occurred while attempting to retrieve the requested user information.");
			while ($rows = $this->Context->Database->GetRow($result)) {
				$UserID = ForceInt($rows["UserID"], 0);
			}
		}
		return $UserID;
	}
	
	function GetUserRoleHistoryByUserId($UserID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("UserRoleHistory", "h");
		$s->AddSelect(array("UserID", "RoleID", "AdminUserID", "Notes", "Date"), "h");
		$s->AddJoin("Role", "r", "RoleID", "h", "RoleID", "inner join");
		$s->AddJoin("User", "u", "UserID", "h", "UserID", "inner join");
		$s->AddJoin("User", "a", "UserID", "h", "AdminUserID", "left join");
		$s->AddSelect("Name", "u", "Username");
		$s->AddSelect("FirstName", "u", "FullName", "concat", "' ',u.LastName");
		$s->AddSelect("Name", "a", "AdminUsername");
		$s->AddSelect("FirstName", "a", "AdminFullName", "concat", "' ',a.LastName");
		$s->AddSelect("Name", "r", "Role");
		$s->AddSelect("Description", "r", "RoleDescription");
		$s->AddSelect("Icon", "r", "RoleIcon");
		$s->AddWhere("h.UserID", $UserID, "=");
		$s->AddOrderBy("Date", "h", "desc");
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetUserRoleHistoryByUserId", "An error occurred while attempting to retrieve the user's role history.");
	}
	
	function GetUsersByIp($Ip) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("User", "u");
		$s->AddJoin("IpHistory", "i", "UserID and i.RemoteIp = '".$Ip."'", "u", "UserID", "inner join");
		$s->AddSelect(array("UserID", "Name"), "u");
		$s->AddGroupBy("UserID", "u");
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetUsersByIp", "An error occurred while retrieving users by IP.");
	}
	
	function GetUsersByRoleId($RoleID, $RecordsToReturn = "0") {
		$RecordsToReturn = ForceInt($RecordsToReturn, 0);
		$s = $this->GetUserBuilder();
		$s->AddWhere("u.RoleID", $RoleID, "=");
		if ($RecordsToReturn > 0) $s->AddLimit(0,$RecordsToReturn);
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetUsersByRoleId", "An error occurred while attempting to retrieve users from the specified role.");
	}
        
/*		SELECT acl.`acl_rights` , acc.account_lid, grp.account_lid
FROM `phpgw_acl` acl
LEFT JOIN phpgw_accounts acc ON acc.account_id = acl.`acl_account`
LEFT JOIN phpgw_accounts grp ON grp.account_id = `acl_location`
WHERE `acl_appname` = 'phpgw_group'
AND `acl_rights` =1
AND acc.account_id =14 */
	function GetUserSearch($Search, $RowsPerPage, $CurrentPage) {
		$s = $this->GetSearchBuilder($Search);
		$SortField = $Search->UserOrder;
		if (!in_array($SortField, array("Name", "Date"))) $SortField = "Name";
		if ($SortField != "Name") $SortField = "DateLastActive";
		$SortDirection = ($SortField == "Name"?"asc":"desc");
		$s->AddOrderBy($SortField, "u", $SortDirection);
		if ($RowsPerPage > 0) {
			$CurrentPage = ForceInt($CurrentPage, 1);
			if ($CurrentPage < 1) $CurrentPage == 1;
			$RowsPerPage = ForceInt($RowsPerPage, 50);
			$FirstRecord = ($CurrentPage * $RowsPerPage) - $RowsPerPage;
		}		
		if ($RowsPerPage > 0) $s->AddLimit($FirstRecord, $RowsPerPage+1);
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetUserSearch", "An error occurred while retrieving search results.");
	}
	
	function GetSearchBuilder($Search) {
		$Search->FormatPropertiesForDatabaseInput();
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlSearch");
		$s->UserQuery = $Search->Query;
		$s->SearchFields = array("u.Name");
		if ($this->Context->Session->User->AdminUsers) {
			$s->SearchFields[] = "u.FirstName";
			$s->SearchFields[] = "u.LastName";
			$s->SearchFields[] = "u.Email";
		}
		$s->SetMainTable("User", "u");
		$s->AddJoin("Style", "s", "StyleID", "u", "StyleID", "left join");
		$s->AddJoin("Role", "r", "RoleID", "u", "RoleID", "left join");
		$s->AddSelect(array("UserID", "RoleID", "StyleID", "CustomStyle", "FirstName", "LastName", "Name", "Email", "UtilizeEmail", "Icon", "CountVisit", "CountDiscussions", "CountComments", "DateFirstVisit", "DateLastActive"), "u");
		$s->AddSelect("Name", "s", "Style");
		$s->AddSelect("Name", "r", "Role");
		$s->AddSelect("Icon", "r", "RoleIcon");
		$s->DefineSearch();
		if ($Search->Roles != "") {
			$Roles = explode(",",$Search->Roles);
			$RoleCount = count($Roles);
			$s->AddWhere("1", "0", "=", "and", "", 0, 1);
			for ($i = 0; $i < $RoleCount; $i++) {
				if ($Roles[$i] == $this->Context->GetDefinition("Applicant")) {
					$s->AddWhere("u.RoleID", 0, "=", "or", "", 1);
					$s->AddWhere("u.RoleID", 0, "=", "or", "" ,0);
				} else {
					$s->AddWhere("r.Name", trim($Roles[$i]), "=", "or");
				}
			}
			$s->EndWhereGroup();
		}
		if ($this->Context->Session->User && $this->Context->Session->User->AdminUsers) {
			// Allow the applicant search
		} else {
			// DON'T allow the applicant search
			$s->AddWhere("u.RoleID", 0, "<>", "and");
		}
		return $s;
	}
	
	// Just retrieve user properties relevant to the session
	function GetSessionDataById($UserID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("User", "u");
		$s->AddSelect(array("Name", "UserID", "RoleID", "StyleID", "CustomStyle", "UserBlocksCategories", "DefaultFormatType", "Settings", "SendNewApplicantNotifications"), "u");
		$s->AddSelect(array("CanLogin", "CanViewIps", "CanPostDiscussion", "CanPostComment", "CanPostHTML", "AdminUsers", "AdminCategories", "MasterAdmin", "ShowAllWhispers"), "r");
		$s->AddSelect("Url", "s", "StyleUrl");
		$s->AddJoin("Role", "r", "RoleID", "u", "RoleID", "left join");
		$s->AddJoin("Style", "s", "StyleID", "u", "StyleID", "left join");
		$s->AddWhere("u.UserID", $UserID, "=");
		$User = $this->Context->ObjectFactory->NewContextObject($this->Context, "User");
		$User = $this->GetUserById($UserID);
		$UserData = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetSessionDataById", "An error occurred while attempting to retrieve the requested user.");
		if ($this->Context->Database->RowCount($UserData) == 0) {
			// This warning is in plain english, because at the point that
         // this method is called, the dictionary object is not yet loaded
         // (this is called in the context object's constructor when the session is started)
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("The requested user could not be found."));
		} else {
			while ($rows = $this->Context->Database->GetRow($UserData)) {
				$User->GetPropertiesFromDataSet($rows);
			}
		}
		return $this->Context->WarningCollector->Iif($this->GetUserById($UserID), false);
	}
	
	function HideHtml() {
		return $this->SwitchUserSetting("HtmlOn", 0);
	}
	
	function LogIp($UserID) {
		if (agLOG_ALL_IPS) {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("IpHistory", "i");
			$s->AddFieldNameValue("UserID", $UserID);
			$s->AddFieldNameValue("RemoteIp", GetRemoteIp(1));
			$s->AddFieldNameValue("DateLogged", MysqlDateTime());
			$this->Context->Database->Insert($this->Context, $s, $this->Name, "LogIp", "An error occurred while logging user data.");
		}
	}
	
	function RemoveApplicant($UserID) {
		// Ensure that the user has not made any contributions to the application in any way
      
		// Styles
      $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("Style", "s");
		$s->AddSelect("StyleID", "s");
		$s->AddWhere("AuthUserID", $UserID, "=");
		$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "RemoveApplicant", "An error occurred while removing the user.");
		if ($this->Context->Database->RowCount($Result) > 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrRemoveUserStyle"));
		if ($this->Context->WarningCollector->Count() > 0) return false;
		
		// Comments
		$s->Clear();
		$s->SetMainTable("Comment", "m");
		$s->AddSelect("CommentID", "m");
		$s->AddWhere("AuthUserID", $UserID, "=");
		$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "RemoveApplicant", "An error occurred while removing the user.");
		if ($this->Context->Database->RowCount($Result) > 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrRemoveUserComments"));
		if ($this->Context->WarningCollector->Count() > 0) return false;
		
		// Discussions
		$s->Clear();
		$s->SetMainTable("Discussion", "t");
		$s->AddSelect("DiscussionID", "t");
		$s->AddWhere("AuthUserID", $UserID, "=");
		$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "RemoveApplicant", "An error occurred while removing the user.");
		if ($this->Context->Database->RowCount($Result) > 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrRemoveUserDiscussions"));
		if ($this->Context->WarningCollector->Count() > 0) return false;
		
		// Remove other data the user has created
      // Bookmarks
		$s->Clear();
		$s->SetMainTable("UserBookmark", "b");
		$s->AddWhere("UserID", $UserID, "=");
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveApplicant", "An error occurred while removing the user's bookmarks.");
		
		// Role History
		$s->Clear();
		$s->SetMainTable("UserRoleHistory", "r");
		$s->AddWhere("UserID", $UserID, "=");
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveApplicant", "An error occurred while removing the user's role history.");
      
		// Searches
		$s->Clear();
		$s->SetMainTable("UserSearch", "s");
		$s->AddWhere("UserID", $UserID, "=");
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveApplicant", "An error occurred while removing the user's saved searches.");
		
		// Discussion Watch
		$s->Clear();
		$s->SetMainTable("UserDiscussionWatch", "w");
		$s->AddWhere("UserID", $UserID, "=");
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveApplicant", "An error occurred while removing the user's discussion history.");
		
		// Remove the user
		$s->Clear();
		$s->SetMainTable("User", "u");
		$s->AddWhere("UserID", $UserID, "=");
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveApplicant", "An error occurred while removing the user.");
		
		return true;
	}
	
	function RemoveBookmark($UserID, $DiscussionID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("UserBookmark", "b");
		$s->AddWhere("UserID", $UserID, "=");
		$s->AddWhere("DiscussionID", $DiscussionID, "=");
		$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveBookmark", "An error occurred while removing the bookmark.");
	}
	
	function RemoveCategoryBlock($CategoryID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("CategoryBlock", "b");
		$s->AddWhere("CategoryID", $CategoryID, "=");
		$s->AddWhere("UserID", $this->Context->Session->UserID, "=");
		// Don't stress over errors (ie. duplicate entries) since this is indexed and duplicates cannot be inserted
		if ($this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveCategoryBlock", "An error occurred while removing the category block.", 0)) {
			$s->Clear();
			$s->SetMainTable("CategoryBlock", "b");
			$s->AddWhere("UserID", $this->Context->Session->UserID, "=");			
			$s->AddSelect("CategoryID", "b");
			if ($this->Context->Database->Select($this->Context, $s, $this->Name, "RemoveCategoryBlock", "Related category block information could not be found.", 0)) {
				if ($this->Context->Database->RowCount($Result) == 0) {
					$s->Clear();
					$s->SetMainTable("User", "u");
					$s->AddFieldNameValue("UserBlocksCategories", "0");
					$s->AddWhere("UserID", $this->Context->Session->UserID, "=");
					$this->Context->Database->Update($this->Context, $s, $this->Name, "RemoveCategoryBlock", "An error occurred while updating category block information.", 0);
				}
			}			
		}
	}
	
	function RemoveCommentBlock($CommentID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("CommentBlock", "b");
		$s->AddWhere("BlockedCommentID", $CommentID, "=");
		$s->AddWhere("BlockingUserID", $this->Context->Session->UserID, "=");
		// Don't stress over errors (ie. duplicate entries) since this is indexed and duplicates cannot be inserted
      $this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveCommentBlock", "An error occurred while removing the comment block.", 0);
	}

	function RemoveUserBlock($UserID) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("UserBlock", "b");
		$s->AddFieldNameValue("BlockingUserID", $this->Context->Session->UserID);
		$s->AddFieldNameValue("BlockedUserID", $UserID);
		// Don't stress over errors (ie. duplicate entries) since this is indexed and duplicates cannot be inserted
      $this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveUserBlock", "An error occurred while removing the user block.", 0);
	}
	
	function RequestPasswordReset($Username) {
		$Username = FormatStringForDatabaseInput($Username, "");
		$Email = false;
		if ($Username == "") {
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrInvalidUsername"));
		} else {
			// Attempt to retrieve email address
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User");
			$s->AddSelect(array("Email", "Name", "UserID"));
			$s->AddWhere("Name", $Username, "=");
			
			
			$UserResult = $this->Context->Database->Select($this->Context, $s, $this->Name, "RequestPasswordReset", "An error occurred while retrieving account information.");
			if ($this->Context->Database->RowCount($UserResult) == 0) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrAccountNotFound"));
			} else {
				$Name = "";
				$Email = "";
				$UserID = 0;
				while ($rows = $this->Context->Database->GetRow($UserResult)) {
					$UserID = ForceInt($rows["UserID"], 0);
					$Email = ForceString($rows["Email"], "");
					$Name = FormatStringForDisplay($rows["Name"], 1);
				}
				// Now that we have the email, generate an email verification key
				$EmailVerificationKey = $this->DefineVerificationKey();
				
				// Insert the email verification key into the user table
				$s->Clear();
				$s->SetMainTable("User");
				$s->AddFieldNameValue("EmailVerificationKey", $EmailVerificationKey,1);
				$s->AddWhere("UserID", $UserID, "=");
				$this->Context->Database->Update($this->Context, $s, $this->Name, "RequestPasswordReset", "An error occurred while managing your account information.");
				
				// If there are no errors, send the user an email
				if ($this->Context->WarningCollector->Count() == 0) {
					$e = $this->Context->ObjectFactory->NewContextObject($this->Context, "Email");
					$e->HtmlOn = 0;
					$e->WarningCollector = &$this->Context->WarningCollector;
					$e->ErrorManager = &$this->Context->ErrorManager;
					$e->AddFrom(agSUPPORT_EMAIL, agSUPPORT_NAME);
					$e->AddRecipient($Email, $Name);
					$e->Subject = agAPPLICATION_TITLE." ".$this->Context->GetDefinition("PasswordResetRequest");
					$e->BodyText = $this->Context->GetDefinition("RequestToResetPassword")
						.agAPPLICATION_TITLE
						.$this->Context->GetDefinition("IfYouDidntRequest")
						."http://"
						.agDOMAIN
						.(substr(agDOMAIN, strlen(agDOMAIN)-1) == "/" ? "":"/")
						."passwordreset.php?u="
						.$UserID
						."&k="
						.$EmailVerificationKey;
					$e->Send();
				}	
			}
		}
		return $this->Context->WarningCollector->Iif($Email,false);
	}
	
	function ResetPassword($PassUserID, $EmailVerificationKey, $NewPassword, $ConfirmPassword) {
		// Validate the passwords
      if ($NewPassword == "") $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPasswordRequired"));
		if ($NewPassword != $ConfirmPassword) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPasswordsMatchBad"));
		
		if ($this->Context->WarningCollector->Count() == 0) {
			$NewPassword = FormatStringForDatabaseInput($NewPassword, 1);
			$EmailVerificationKey = FormatStringForDatabaseInput($EmailVerificationKey);
			
			// Attempt to retrieve email address
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User");
			$s->AddFieldNameValue("EmailVerificationKey", "", 1);
			$s->AddFieldNameValue("Password", $NewPassword, 1, "md5");
			$s->AddWhere("UserID", $PassUserID, "=");
			$s->AddWhere("EmailVerificationKey", $EmailVerificationKey, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "ResetPassword", "An error occurred while updating your password.");
		}
		return $this->Context->WarningCollector->Iif();
	}
	
	function SaveIdentity($User) {
		// Ensure that the person performing this action has access to do so
		// Everyone can edit themselves
		if ($this->Context->Session->UserID != $User->UserID && !$this->Context->Session->User->AdminUsers) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionInsufficient"));
		
		if ($this->Context->WarningCollector->Count() == 0) {
			// Validate the properties
			if($this->ValidateUser($User)) {
				$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
				$s->SetMainTable("User", "u");
				if (agALLOW_NAME_CHANGE == "1") $s->AddFieldNameValue("Name", $User->Name);
				$s->AddFieldNameValue("FirstName", $User->FirstName);
				$s->AddFieldNameValue("LastName", $User->LastName);
				$s->AddFieldNameValue("ShowName", $User->ShowName);
				$s->AddFieldNameValue("Email", $User->Email);
				$s->AddFieldNameValue("UtilizeEmail", $User->UtilizeEmail);
				$s->AddFieldNameValue("Icon", $User->Icon);
				$s->AddFieldNameValue("Picture", $User->Picture);
				$s->AddFieldNameValue("Attributes", $User->Attributes);
				$s->AddWhere("UserID", $User->UserID, "=");
				$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveIdentity", "An error occurred while attempting to update the identity data.");
			}
		}
		return $this->Context->WarningCollector->Iif();
	}
	
	function SaveStyle($User) {
		// Ensure that the person performing this action has access to do so
		// Everyone can edit themselves
		if ($this->Context->Session->UserID != $User->UserID && !$this->Context->Session->User->AdminUsers) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionInsufficient"));
		
		if ($this->Context->WarningCollector->Count() == 0) {
			// Make sure they've got a style of some kind
			if ($User->CustomStyle == "" && $User->StyleID == 0) $User->StyleID = 1;
			$User->FormatPropertiesForDatabaseInput();
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddFieldNameValue("StyleID", $User->StyleID);
			if ($User->StyleID == 0) $s->AddFieldNameValue("CustomStyle", $User->CustomStyle);
			$s->AddWhere("UserID", $User->UserID, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveStyle", "An error occurred while attempting to update the style data.");
		}
		$this->Context->Session->User->StyleID = $User->StyleID;
		$this->Context->Session->User->CustomStyle = $User->CustomStyle;
		
		return $this->Context->WarningCollector->Iif();
	}
	
	function SetCookieCredentials($EncryptedUserID, $VerificationKey) {
		// Note: 31104000 is 60*60*24*30*12.. or 1 year
		// echo("setting pass to: ".$EncryptedUserID);
		// echo("setting name to: ".$VerificationKey);
		// Note: these cookie are purposefully named incorrectly 
		// to fool anyone that tries to mess with them.
		setcookie("pass", $EncryptedUserID, time()+31104000,"/",agCOOKIE_DOMAIN);
		setcookie("name", $VerificationKey, time()+31104000,"/",agCOOKIE_DOMAIN);
	}
	
	function SetDefaultFormatType($UserID, $FormatType) {
		$this->Context->Session->User->DefaultFormatType = $FormatType;
		return $this->SwitchUserProperty($UserID, "DefaultFormatType", $FormatType);
	}
	
	function ShowHtml() {
		return $this->SwitchUserSetting("HtmlOn", 1);
	}
	
	function SwitchUserProperty($UserID, $PropertyName, $Switch) {
		$UserID = ForceInt($UserID, 0);
		if ($UserID == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrUserID"));
		
		if (!$this->Context->Session->User->AdminUsers && $UserID != $this->Context->Session->UserID) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionUserSettings"));
		
		if ($this->Context->WarningCollector->Count() == 0) {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User");
			$s->AddFieldNameValue($PropertyName, $Switch);
			$s->AddWhere("UserID", $UserID, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "SwitchUserProperty", "An error occurred while manipulating user properties.");
		}
		return $this->Context->WarningCollector->Iif();
	}	
	
	function SwitchUserSetting($SettingName, $Switch, $DefaultValue = "0") {
		$Switch = ForceBool($Switch, 0);
		if ($this->Context->Session->UserID == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrUserID"));
		
		if ($this->Context->WarningCollector->Count() == 0) {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			// Set the value for the user
         $this->Context->Session->User->Settings[$SettingName] = $Switch;
			// Serialize and save the settings
         $SerializedSettings = SerializeArray($this->Context->Session->User->Settings);
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User");
			$s->AddFieldNameValue("Settings", $SerializedSettings);
			$s->AddWhere("UserID", $this->Context->Session->User->UserID, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "SwitchUserSetting", "An error occurred while manipulating user settings.");
		}
		return $this->Context->WarningCollector->Iif();
	}
	
	function UpdateLastVisit($UserID, $VerificationKey) {
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("User", "u");
		$s->AddFieldNameValue("DateLastActive", MysqlDateTime());
		$s->AddFieldNameValue("VerificationKey", $VerificationKey);
		$s->AddFieldNameValue("CountVisit", "CountVisit+1", 0);
		$s->AddFieldNameValue("RemoteIp", GetRemoteIp(1));
		$s->AddWhere("UserID", $UserID, "=");
		$this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateLastVisit", "An error occurred while updating your account.");
		$this->LogIp($UserID);
	}
	
	function UpdateUserCommentCount($UserID) {
		if ($this->Context->WarningCollector->Count() == 0) {
			$UserID = ForceInt($UserID, 0);
			
			if ($UserID == 0) $this->Context->ErrorManager->AddError($this->Context, $this->Name, "UpdateUserCommentCount", "User identifier not supplied");
			
			// Select the LastCommentPost, and CommentSpamCheck values from the user's profile
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddSelect("(unix_timestamp('".MysqlDateTime()."')- unix_timestamp(LastCommentPost))", "", "DateDiff");
			$s->AddSelect("CommentSpamCheck");
			$s->AddWhere("UserID", $UserID, "=");
	
			$DateDiff = "";
			$CommentSpamCheck = 0;
			$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "UpdateUserCommentCount", "An error occurred while retrieving user activity data.");
			while ($rows = $this->Context->Database->GetRow($result)) {
				$DateDiff = ForceString($rows["DateDiff"], "");
				// echo($s->GetSelect()."<br />");
				$CommentSpamCheck = ForceInt($rows["CommentSpamCheck"], 0);
			}
			
			// If a non-numeric value was returned, then this is the user's first post
			$SecondsSinceLastPost = ForceInt($DateDiff, 0);
			// If the LastCommentPost is less than 30 seconds ago 
			// and the CommentSpamCheck is greater than five, throw a warning
			if ($SecondsSinceLastPost < agCOMMENT_THRESHOLD_PUNISHMENT && $CommentSpamCheck >= agCOMMENT_POST_THRESHOLD && $DateDiff != "") $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrSpamCommentsStart").agCOMMENT_POST_THRESHOLD.$this->Context->GetDefinition("ErrSpamCommentsMiddle1").agCOMMENT_TIME_THRESHOLD.$this->Context->GetDefinition("ErrSpamCommentsMiddle2").agCOMMENT_THRESHOLD_PUNISHMENT.$this->Context->GetDefinition("ErrSpamCommentsEnd"));
	
			$s->Clear();
			$s->SetMainTable("User", "u");
			if ($this->Context->WarningCollector->Count() == 0) {
				// make sure to update the "datelastactive" field
            $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
				$s->AddFieldNameValue("CountComments", "CountComments+1", 0);
				// If the LastCommentPost is less than 30 seconds ago 
				// and the DiscussionSpamCheck is less than 6, 
				// update the user profile and add 1 to the CommentSpamCheck
				if ($SecondsSinceLastPost == 0) {
					$s->AddFieldNameValue("LastCommentPost", MysqlDateTime());
				} elseif ($SecondsSinceLastPost < agCOMMENT_TIME_THRESHOLD && $CommentSpamCheck <= agCOMMENT_POST_THRESHOLD && $DateDiff != "") {
					$s->AddFieldNameValue("CommentSpamCheck", "CommentSpamCheck+1", 0);
				} else {
					// If the LastCommentPost is more than 60 seconds ago, 
					// set the CommentSpamCheck to 1, LastCommentPost to now(), 
					// and update the user profile
					$s->AddFieldNameValue("CommentSpamCheck", 1);
					$s->AddFieldNameValue("LastCommentPost", MysqlDateTime());
				}
				$s->AddWhere("UserID", $UserID, "=");
				$this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateUserCommentCount", "An error occurred while updating the user profile.");
			} else {
				// Update the "Waiting period" every time they try to post again
            $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
				$s->AddFieldNameValue("LastCommentPost", MysqlDateTime());
				$s->AddWhere("UserID", $UserID, "=");
				$this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateUserCommentCount", "An error occurred while updating the user profile.");
			}
		}
	
		return $this->Context->WarningCollector->Iif();	
	}
	
	function UpdateUserDiscussionCount($UserID) {
		if ($this->Context->WarningCollector->Iif()) {
			$UserID = ForceInt($UserID, 0);
			
			if ($UserID == 0) $this->Context->ErrorManager->AddError($this->Context, $this->Name, "UpdateUserDiscussionCount", "User identifier not supplied");
			
			// Select the LastDiscussionPost, and DiscussionSpamCheck values from the user's profile
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddSelect("(unix_timestamp('".MysqlDateTime()."')- unix_timestamp(LastDiscussionPost))", "", "DateDiff");
			$s->AddSelect("DiscussionSpamCheck");
			$s->AddWhere("UserID", $UserID, "=");
			$DateDiff = "";
			$DiscussionSpamCheck = 0;
			$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "UpdateUserDiscussionCount", "An error occurred while retrieving user activity data.");
			while ($rows = $this->Context->Database->GetRow($result)) {
				$DateDiff = ForceString($rows['DateDiff'], "");
				$DiscussionSpamCheck = ForceInt($rows['DiscussionSpamCheck'], 0);
			}
			$SecondsSinceLastPost = ForceInt($DateDiff, 0);
			
			// If the LastDiscussionPost is less than 1 minute ago 
			// and the DiscussionSpamCheck is greater than three, throw a warning
			if ($SecondsSinceLastPost < agDISCUSSION_THRESHOLD_PUNISHMENT && $DiscussionSpamCheck >= agDISCUSSION_POST_THRESHOLD && $DateDiff != "") $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrSpamDiscussionsStart").agDISCUSSION_POST_THRESHOLD.$this->Context->GetDefinition("ErrSpamDiscussionsMiddle1").agDISCUSSION_TIME_THRESHOLD.$this->Context->GetDefinition("ErrSpamDiscussionsMiddle2").agDISCUSSION_THRESHOLD_PUNISHMENT.$this->Context->GetDefinition("ErrSpamDiscussionsEnd"));
			
			$s->Clear();
			$s->SetMainTable("User", "u");
			if ($this->Context->WarningCollector->Count() == 0) {
            $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
				$s->AddFieldNameValue("CountDiscussions", "CountDiscussions+1", 0);
				// If the LastDiscussionPost is less than 1 minute ago 
				// and the DiscussionSpamCheck is less than four, 
				// update the user profile and add 1 to the DiscussionSpamCheck
				if ($SecondsSinceLastPost < agDISCUSSION_TIME_THRESHOLD && $DiscussionSpamCheck <= agDISCUSSION_POST_THRESHOLD && $DateDiff != "") {
					$s->AddFieldNameValue("DiscussionSpamCheck", "DiscussionSpamCheck+1", 0);
				} else {
					// If the LastDiscussionPost is more than 1 minute ago, 
					// set the DiscussionSpamCheck to 1, LastDiscussionPost to now(), 
					// and update the user profile
					$s->AddFieldNameValue("DiscussionSpamCheck", 1);
					$s->AddFieldNameValue("LastDiscussionPost", MysqlDateTime());
				}
				$s->AddWhere("UserID", $UserID, "=");
				$this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateUserDiscussionCount", "An error occurred while updating the user profile.");
			} else {
				// Update the "Waiting period" every time they try to post again
            $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
				$s->AddFieldNameValue("LastDiscussionPost", MysqlDateTime());
				$s->AddWhere("UserID", $UserID, "=");
				$this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateUserCommentCount", "An error occurred while updating the user profile.");
			}
		}
		
		return $this->Context->WarningCollector->Iif();
	}
	
	// Constructor
	function UserManager(&$Context) {
		$this->Name = "UserManager";
		$this->Context = &$Context;
	}	
	
	function ValidateCookieCredentials() {
		// Retrieve cookie values
		$EncryptedUserID = ForceIncomingCookieString("pass", "");
		$VerificationKey = ForceIncomingCookieString("name", "");
		$UserID = 0;
		$EGW_Session_ID = ForceIncomingCookieString("sessionid","");
		
		if ($EGW_Session_ID != ""){
                        $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
                        $s->SetMainTable("phpgw_config","egwc","");
                        $s->AddSelect("config_value","egwc");
                        $s->AddWhere("config_app","phpgwapi","=");
			$s->AddWhere("config_name","sessions_timeout","=");
                        $r=$this->Context->Database->Select($this->Context, $s, $this->Name, "GetEGWSessionsTimeout", "An error occurred while getting sessions timeout", 0);
                        if (!$r){
                          $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrBadSessionsTimeout").$this->Context->Database->ConnectionError());
                        } else {
                          while ($rows = $this->Context->Database->GetRow($r)) {
						$EGW_Sessions_Timeout = ForceInt($rows['config_value'], 0);
                          }
                        }
                        $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("phpgw_sessions", "egws","");
			$s->AddSelect(array("session_id","session_lid","session_dla"), "egws");
			$s->AddSelect("account_id","egwu","UserID");
			$s->AddJoin("phpgw_accounts","egwu","account_lid","egws","session_lid","left join","");
			$s->AddWhere("session_id",$EGW_Session_ID,"=");
			$s->AddWhere("session_dla",(time()-$EGW_Sessions_Timeout),">");
			$r=$this->Context->Database->Select($this->Context, $s, $this->Name, "GetEGWSession", "An error occurred while getting session", 0);
                        if (!$r){
                          $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrBadSession").$this->Context->Database->ConnectionError());
                        } else {
                          while ($rows = $this->Context->Database->GetRow($r)) {
						$UserID = ForceInt($rows['UserID'], 0);
                          }
                        }
                }
/*
		if ($EncryptedUserID != "" && $VerificationKey != "") {
			// Compare against db values
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddSelect("UserID", "u");
			$s->AddSelect("UserID", "u", "EncryptedUserID", "md5");
			$s->AddWhere("md5(UserID)", $EncryptedUserID, "=");
			$s->AddWhere("VerificationKey", $VerificationKey, "=");
			
			$UserResult = $this->Context->Database->Select($this->Context, $s, $this->Name, "ValidateCookieCredentials", "An error occurred while validating your credentials", 0);
			if (!$UserResult) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrBadCredentials").$this->Context->Database->ConnectionError());
			} else {
				if ($this->Context->Database->RowCount($UserResult) == 0) {
					// Silently fail if checking cookie credentials fails
					$this->Context->Session->End();
				} else {
					// Set session variables
					while ($rows = $this->Context->Database->GetRow($UserResult)) {
						$UserID = ForceInt($rows['UserID'], 0);
					}
					if ($UserID > 0) {
						// Set a new verification key
                  $VerificationKey = $this->DefineVerificationKey();
						// Update the user's information
                  $this->UpdateLastVisit($UserID, $VerificationKey);
						// Set the "remembery" cookies
						$this->SetCookieCredentials($EncryptedUserID, $VerificationKey);
					}					
				}
			}
		}
*/		
		return $UserID;
	}	

	// Validates and formats User properties ensuring they're safe for database input
	// Returns: boolean value indicating success
	// Usage: $Boolean = $UserManager->ValidateUser($MyUser);
	function ValidateUser(&$User) {
		// First update the values so they are safe for db input
		$SafeUser = $User;
		$SafeUser->FormatPropertiesForDatabaseInput();

		// Instantiate a new validator for each field
		Validate($this->Context->GetDefinition("FirstNameLower"), 1, $SafeUser->FirstName, 20, "", $this->Context);
		Validate($this->Context->GetDefinition("LastNameLower"), 1, $SafeUser->LastName, 20, "", $this->Context);
		if (agALLOW_NAME_CHANGE == "1") Validate($this->Context->GetDefinition("UsernameLower"), 1, $SafeUser->Name, 20, "", $this->Context);
		Validate($this->Context->GetDefinition("EmailLower"), 1, $SafeUser->Email, 200, "(.+)@(.+)\.(.+)", $this->Context);
		
		// Ensure the username isn't taken already
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("User", "u");
		$s->AddSelect("UserID", "u");
		$s->AddWhere("Name", $SafeUser->Name, "=");
		if ($User->UserID > 0) $s->AddWhere("UserID", $User->UserID, "<>");
		$MatchCount = 0;
		$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "ValidateUser", "A fatal error occurred while validating your input.");
		$MatchCount = $this->Context->Database->RowCount($result);
		
		if ($MatchCount > 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrUsernameTaken"));
		
		// If validation was successful, then reset the properties to db safe values for saving
		if ($this->Context->WarningCollector->Count() == 0) $User = $SafeUser;
		
		return $this->Context->WarningCollector->Iif();
	}
	
	function ValidateUserCredentials($Username, $Password, $PersistentSession) {
		// Validate the username and password that have been set
		$Username = FormatStringForDatabaseInput($Username);
		$Password = FormatStringForDatabaseInput($Password);
		
		if ($Username == "") $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrInvalidUsername"));
		if ($Password == "") $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrInvalidPassword"));
		// Only continue if there have been no errors/warnings
		if ($this->Context->WarningCollector->Count() == 0) {
			// Retrieve matching username/password values
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddSelect("UserID", "u");
			$s->AddSelect("UserID", "u", "EncryptedUserID", "md5");
			$s->AddJoin("Role", "r", "RoleID", "u", "RoleID", "left join");
			$s->AddSelect("CanLogin", "r");
			$s->AddWhere("u.Name", $Username, "=");
			$s->AddWhere("u.Password", $Password, "=", "and", "md5");

			$UserResult = $this->Context->Database->Select($this->Context, $s, $this->Name, "ValidateUserCredentials","An error occurred while validating your credentials.");
			if ($this->Context->Database->RowCount($UserResult) == 0) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrUserCombination"));
			} else {
				$UserID = 0;
				$CanLogin = 0;
				$EncryptedUserID = "";
				$VerificationKey = "";
				while ($rows = $this->Context->Database->GetRow($UserResult)) {
					$EncryptedUserID = $rows["EncryptedUserID"];
					$VerificationKey = $this->DefineVerificationKey();
					$UserID = ForceInt($rows['UserID'], 0);
					$CanLogin = ForceBool($rows["CanLogin"], 0);
				}
				if (!$CanLogin) {
					$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrNoLogin"));
				} else {
					$this->Context->Session->Start($this->Context, $UserID);
					
					// Update the user's information
					$this->UpdateLastVisit($UserID, $VerificationKey);
					// Set the "remembery" cookies
               if ($PersistentSession) $this->SetCookieCredentials($EncryptedUserID, $VerificationKey);
				}
			}
		}
		return $this->Context->WarningCollector->Iif();
	}	
	
	function VerifyPasswordResetRequest($VerificationUserID, $EmailVerificationKey) {
		$VerificationUserID = ForceInt($VerificationUserID, 0);
		$EmailVerificationKey = ForceString($EmailVerificationKey, "");
		$EmailVerificationKey = FormatStringForDatabaseInput($EmailVerificationKey);
		
		// Attempt to retrieve email address
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("User");
		$s->AddSelect("UserID");
		$s->AddWhere("UserID", $VerificationUserID, "=");
		$s->AddWhere("EmailVerificationKey", $EmailVerificationKey, "=");
		$UserResult = $this->Context->Database->Select($this->Context, $s, $this->Name, "VerifyPasswordResetRequest", "An error occurred while retrieving account information.");
		if ($this->Context->Database->RowCount($UserResult) == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPasswordResetRequest"));
		return $this->Context->WarningCollector->Iif();
	}	
}
?>