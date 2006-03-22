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
* Description: Discussion Comment & Management classes
*/

class Comment {
	var $CommentID;
	var $DiscussionID;
	var $Discussion;				// Display purposes only
   var $CategoryID;
	var $Category;
	var $AuthUserID;
	var $AuthFullName;		// Display purposes only - User's real name
	var $AuthUsername;		// Display purposes only - User's username
	var $AuthIcon;
	var $AuthRoleID;
	var $AuthRole;
	var $AuthRoleIcon;
	var $AuthRoleDesc;
	var $AuthCanPostHtml;	// Is this author in a role where posting html is allowed
	var $AuthBlocked;			// Is this author blocked from posting html by the viewing user?
   var $CommentBlocked;		// Is this comment blocked from visible html by the viewing user?
	var $EditUserID;
	var $EditFullName;		// Display purposes only - Editing user's real name
	var $EditUsername;		// Display purposes only - Editing user's username
	var $DateCreated;
	var $DateEdited;
	var $Body;				// Actual body of the comment
   var $FormatType;		// How should the comment be formatted for display?
	var $Deleted;			// Boolean value indicating if the comment shows up to users
	var $DateDeleted;
	var $DeleteUserID;
	var $DeleteFullName;	// Display purposes only - Deleting user's real name
	var $DeleteUsername;	// Display purposes only - Deleting user's username
	var $RemoteIp;
	var $Status;
	// Used to prevent double posts and "back button" posts
   var $UserCommentCount;
	var $ShowHtml;
	var $WhisperUserID;	// Deprecated
	
	// Clears all properties
	function Clear() {
		$this->CommentID = 0;
		$this->DiscussionID = 0;
		$this->Discussion = "";
		$this->CategoryID = 0;
		$this->Category = "";
		$this->AuthUserID = 0;
		$this->AuthFullName = "";
		$this->AuthUsername = "";
		$this->AuthIcon = "";
		$this->AuthRoleID = 0;
		$this->AuthRole = "";
		$this->AuthRoleIcon = "";
		$this->AuthRoleDesc = "";
		$this->AuthCanPostHtml = 0;
		$this->AuthBlocked = 0;
		$this->CommentBlocked = 0;
		$this->EditUserID = 0;
		$this->EditFullName = "";
		$this->EditUsername = "";
		$this->DateCreated = "";
		$this->DateEdited = "";
		$this->Body = "";
		$this->FormatType = "Text";
		$this->Deleted = 0;
		$this->DateDeleted = "";
		$this->DeleteUserID = 0;
		$this->DeleteFullName = "";
		$this->DeleteUsername = "";
		$this->RemoteIp = "";
		$this->Status = "";
		$this->UserCommentCount = 0;
		$this->ShowHtml = 1;
		$this->WhisperUserID = 0;
	}
	
	function Comment() {
		$this->Clear();
	}
	
	function FormatPropertiesForDatabaseInput() {
		$this->Body = FormatStringForDatabaseInput($this->Body);
	}
	
	function FormatPropertiesForDisplay($Context, $ForFormDisplay = "0") {
// 		if (!$Context->Session->User->Setting("HtmlOn", 1)) 
                $this->ShowHtml = 1;
		if ($this->Deleted) $this->ShowHtml = 0;
		if (!$this->AuthCanPostHtml) $this->ShowHtml = 0;
		if ($this->AuthBlocked) $this->ShowHtml = 0;
		if ($this->CommentBlocked) $this->ShowHtml = 0;

		$this->AuthFullName = FormatStringForDisplay($this->AuthFullName);
		$this->AuthUsername = FormatStringForDisplay($this->AuthUsername);
		$this->EditFullName = FormatStringForDisplay($this->EditFullName);
		$this->EditUsername = FormatStringForDisplay($this->EditUsername);
		$this->DeleteFullName = FormatStringForDisplay($this->DeleteFullName);
		$this->DeleteUsername = FormatStringForDisplay($this->DeleteUsername);
		$this->Discussion = FormatStringForDisplay($this->Discussion);
		$this->Category = FormatStringForDisplay($this->Category);
		if ($ForFormDisplay) {
			$this->Body = htmlspecialchars($this->Body);
		} else {
			// Force the comment into plain text mode if html is turned off
			if (!$this->ShowHtml) $this->FormatType = agDEFAULTSTRINGFORMAT;
			$this->Body = $Context->FormatString($this->Body, $this, $this->FormatType, agFORMATSTRINGFORDISPLAY);
		}
		$this->AuthIcon = FormatStringForDisplay($this->AuthIcon);
		return $this->ShowHtml;
	}
	
	function FormatPropertiesForSafeDisplay() {
		$this->AuthFullName = FormatStringForDisplay($this->AuthFullName);
		$this->AuthUsername = FormatStringForDisplay($this->AuthUsername);
		$this->EditFullName = FormatStringForDisplay($this->EditFullName);
		$this->EditUsername = FormatStringForDisplay($this->EditUsername);
		$this->DeleteFullName = FormatStringForDisplay($this->DeleteFullName);
		$this->DeleteUsername = FormatStringForDisplay($this->DeleteUsername);
		$this->Discussion = FormatStringForDisplay($this->Discussion);
		$this->Category = FormatStringForDisplay($this->Category);
		$this->Body = FormatHtmlStringInline($this->Body);
		$this->AuthIcon = FormatStringForDisplay($this->AuthIcon);
	}

	// Retrieve a properties from current DataRowSet
	// Returns void
	function GetPropertiesFromDataSet($DataSet, $UserID) {
		$this->CommentID = ForceInt(@$DataSet["CommentID"], 0);
		$this->DiscussionID = ForceInt(@$DataSet["DiscussionID"], 0);
		$this->Discussion = ForceString(@$DataSet["Discussion"], "");
		$this->CategoryID = ForceInt(@$DataSet["CategoryID"], 0);
		$this->Category = ForceString(@$DataSet["Category"], "");
		$this->AuthUserID = ForceInt(@$DataSet["AuthUserID"], 0);
		$this->AuthFullName = ForceString(@$DataSet["AuthFullName"], "");
		$this->AuthUsername = ForceString(@$DataSet["AuthUsername"], "");
		$this->AuthIcon = ForceString(@$DataSet["AuthIcon"], "");
		$this->AuthRoleID = ForceInt(@$DataSet["AuthRoleID"], 0);
		$this->AuthRole = ForceString(@$DataSet["AuthRole"], "");
		$this->AuthRoleIcon = ForceString(@$DataSet["AuthRoleIcon"], "");
		$this->AuthRoleDesc = ForceString(@$DataSet["AuthRoleDesc"], "");
		$this->AuthCanPostHtml = ForceBool(@$DataSet["AuthCanPostHtml"], 0);
		$this->AuthBlocked = ForceBool(@$DataSet["AuthBlocked"], 0);
		$this->CommentBlocked = ForceBool(@$DataSet["CommentBlocked"], 0);
		$this->EditUserID = ForceInt(@$DataSet["EditUserID"], 0);
		$this->EditFullName = ForceString(@$DataSet["EditFullName"], "");
		$this->EditUsername = ForceString(@$DataSet["EditUsername"], "");
		$this->DateCreated = UnixTimestamp(@$DataSet["DateCreated"]);
		$this->DateEdited = UnixTimestamp(@$DataSet["DateEdited"]);
		$this->Body = ForceString(@$DataSet["Body"], "");
		$this->FormatType = ForceString(@$DataSet["FormatType"], "Text");
		$this->Deleted = ForceBool(@$DataSet["Deleted"], 0);
		$this->DateDeleted = UnixTimestamp(@$DataSet["DateDeleted"]);
		$this->DeleteUserID = ForceInt(@$DataSet["DeleteUserID"], 0);
		$this->DeleteFullName = ForceString(@$DataSet["DeleteFullName"], "");
		$this->DeleteUsername = ForceString(@$DataSet["DeleteUsername"], "");
		$this->RemoteIp = ForceString(@$DataSet["RemoteIp"], "");
		$this->Status = $this->GetStatus($UserID);
		if ($this->AuthRoleIcon != "") $this->AuthIcon = $this->AuthRoleIcon;
	}

	// Retrieve properties from incoming form variables
	// Returns void	
	function GetPropertiesFromForm(&$Context) {
		$this->CommentID = ForceIncomingInt("CommentID", 0);
		$this->DiscussionID = ForceIncomingInt("DiscussionID", 0);
		$this->FormatType = ForceIncomingString("FormatType", "Text");
		$this->Body = ForceIncomingString("Body", "");
		$this->UserCommentCount = ForceIncomingInt("UserCommentCount", 0);
		// Pass the body into a formatter for db input
      $this->Body = $Context->FormatString($this->Body, $this, $this->FormatType, agFORMATSTRINGFORDATABASE);
	}
	
	function GetStatus($UserID) {
		return "";
	}	
}

class CommentManager {
	var $Name;				// The name of this class
   var $Context;			// The context object that contains all global objects (database, error manager, warning collector, session, etc)
	
	function CommentManager(&$Context) {
		$this->Name = "CommentManager";
		$this->Context = &$Context;
	}
	
	// Returns a SqlBuilder object with all of the comment properties already defined in the select
	function GetCommentBuilder($s = 0) {
		if (!$s) $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("Comment", "m");
		$s->AddSelect(array("CommentID", "DiscussionID", "Body", "FormatType", "DateCreated", "DateEdited", "DateDeleted", "Deleted", "AuthUserID", "EditUserID", "DeleteUserID", "RemoteIp"), "m");
		$s->AddSelect("account_firstname", "a", "AuthFullName", "concat", "' ',a.account_lastname");
		$s->AddSelect("account_lid", "a", "AuthUsername");
// 		$s->AddSelect("account_firstname", "a", "AuthIcon");
		$s->AddSelect("Name", "r", "AuthRole");
		$s->AddSelect("RoleID", "r", "AuthRoleID");
		$s->AddSelect("Description", "r", "AuthRoleDesc");
		$s->AddSelect("Icon", "r", "AuthRoleIcon");
		$s->AddSelect("CanPostHtml", "r", "AuthCanPostHtml");
		$s->AddSelect("account_firstname", "e", "EditFullName", "concat", "' ',e.account_lastname");
		$s->AddSelect("account_lid", "e", "EditUsername");
		$s->AddSelect("account_firstname", "d", "DeleteFullName", "concat", "' ',d.account_lastname");
		$s->AddSelect("account_lid", "d", "DeleteUsername");
		$s->AddSelect("Blocked", "ab", "AuthBlocked", "coalesce", "0");
		$s->AddSelect("Blocked", "cb", "CommentBlocked", "coalesce", "0");
		$s->AddSelect("'".agICONSPREFIX."'", "", "AuthIcon", "concat", "i.filename");
		$s->AddSelect("Name","t","Discussion");
		$s->AddJoin("phpgw_accounts", "a", "account_id", "m", "AuthUserID", "left join","");
		$s->AddJoin("User","ou","UserID","a","account_id","left join");
		$s->AddJoin("Role", "r", "RoleID", "ou", "RoleID", "left join");
		$s->AddJoin("phpgw_accounts", "e", "account_id", "m", "EditUserID", "left join","");
		$s->AddJoin("phpgw_accounts", "d", "account_id", "m", "DeleteUserID", "left join","");
		$s->AddJoin("UserBlock", "ab", "BlockedUserID and ab.BlockingUserID = ".$this->Context->Session->UserID, "m", "AuthUserID", "left join");
		$s->AddJoin("CommentBlock", "cb", "BlockedCommentID and cb.BlockingUserID = ".$this->Context->Session->UserID, "m", "CommentID", "left join");
		$s->AddJoin("Discussion", "t", "DiscussionID", "m", "DiscussionID", "inner join");
		$s->AddJoin("members_icons", "i", "owner", "m", "AuthUserID", "left join","");
		$s->AddGroupBy("CommentID", "m");
		
		// Limit to roles with access to this category
      if ($this->Context->Session->UserID > 0) {
			$s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = ".$this->Context->Session->User->RoleID, "t", "CategoryID", "left join");
		} else {
			$s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = 1", "t", "CategoryID", "left join");
		}
		$s->AddWhere("coalesce(crb.Blocked, 0)", "0", "=", "and", "", 0, 0);
		
		return $s;
	}
		
	function GetCommentById($CommentID, $UserID) {
		$Comment = $this->Context->ObjectFactory->NewObject($this->Context, "Comment");

		$s = $this->GetCommentBuilder();
		if (!$this->Context->Session->User->AdminCategories) $s->AddWhere("m.Deleted", "0", "=");
		
		$s->AddWhere("m.CommentID", $CommentID, "=");
			
		$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCommentById", "An error occurred while attempting to retrieve the requested comment.");
		if ($this->Context->Database->RowCount($result) == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrCommentNotFound"));
		while ($rows = $this->Context->Database->GetRow($result)) {
			$Comment->GetPropertiesFromDataSet($rows, $UserID);
		}
		return $this->Context->WarningCollector->Iif($Comment, false);
	}
	
	function GetCommentCount($DiscussionID) {
		$TotalNumberOfRecords = 0;
		$DiscussionID = ForceInt($DiscussionID, 0);
		
		// If the current user is admin, see if they can view inactive comments
		// If the current user is not admin only show active comments
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("Comment", "m");
		$s->AddSelect("CommentID", "m", "Count", "count");
		$s->AddJoin("Discussion", "t", "DiscussionID", "m", "DiscussionID", "inner join");
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedComments")) {
			$s->AddWhere("m.Deleted", 0, "=", "and", "", 1, 1);
			$s->AddWhere("m.Deleted", 0, "=", "or", "" ,0);
			$s->EndWhereGroup();
		}
		// If the whisper extension was on - we want to make sure that whispers aren't included in the count
		$s->AddWhere("m.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("m.WhisperUserID", 0, "=", "or", "" ,0);
		$s->AddWhere("m.WhisperUserID", "null", "is", "or", "" ,0);
		$s->EndWhereGroup();

		$s->AddWhere("m.DiscussionID", $DiscussionID, "=");
		$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCommentCount", "An error occurred while retrieving comment information.");
		while ($rows = $this->Context->Database->GetRow($result)) {
			$TotalNumberOfRecords = $rows['Count'];
		}
		return $TotalNumberOfRecords;
	}
	
	function GetCommentList($RowsPerPage, $CurrentPage, $DiscussionID) {
		$RowsPerPage = ForceInt($RowsPerPage, 50);
		$CurrentPage = ForceInt($CurrentPage, 1);
		$DiscussionID = ForceInt($DiscussionID, 0);
		
		if ($RowsPerPage > 0) {
			$CurrentPage = ForceInt($CurrentPage, 1);
			if ($CurrentPage < 1) $CurrentPage == 1;
			$RowsPerPage = ForceInt($RowsPerPage, 50);
			$FirstRecord = ($CurrentPage * $RowsPerPage) - $RowsPerPage;
		}
		
		$s = $this->GetCommentBuilder();
		if (!$this->Context->Session->User->AdminCategories) {
			$s->AddWhere("m.Deleted", 0, "=", "and", "", 1, 1);
			$s->AddWhere("m.Deleted", 0, "=", "or", "" ,0);
			$s->EndWhereGroup();
		}
		// If the whisper extension was on - we want to make sure that whispers aren't included in the count
		$s->AddWhere("m.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("m.WhisperUserID", 0, "=", "or", "" ,0);
		$s->AddWhere("m.WhisperUserID", "null", "is", "or", "" ,0);
		$s->EndWhereGroup();
		
		$s->AddWhere("m.DiscussionID", $DiscussionID, "=");
		$s->AddOrderBy("DateCreated", "m", "asc");
		if ($RowsPerPage > 0) $s->AddLimit($FirstRecord, $RowsPerPage);

		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCommentList", "An error occurred while attempting to retrieve the requested comments.");
	}
	
	function GetCommentSearch($RowsPerPage, $CurrentPage, $Search) {
		$s = $this->GetSearchBuilder($Search);
		$s->AddOrderBy("DateCreated", "m", "desc");
		if ($RowsPerPage > 0) {
			$CurrentPage = ForceInt($CurrentPage, 1);
			if ($CurrentPage < 1) $CurrentPage == 1;
			$RowsPerPage = ForceInt($RowsPerPage, 50);
			$FirstRecord = ($CurrentPage * $RowsPerPage) - $RowsPerPage;
		}		
		if ($RowsPerPage > 0) $s->AddLimit($FirstRecord, $RowsPerPage+1);
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCommentSearch", "An error occurred while retrieving search results.");
	}
	
	function GetSearchBuilder($Search) {
		$Search->FormatPropertiesForDatabaseInput();
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlSearch");
		$s = $this->GetCommentBuilder($s);
		$s->UserQuery = $Search->Query;
		$s->SearchFields = array("m.Body");
		$s->DefineSearch();
		$s->AddSelect("Name", "t", "Discussion");
		$s->AddJoin("phpgw_categories", "c", "Cat_id", "t", "CategoryID", "left join","");
		$s->AddSelect("CategoryID", "t");
		$s->AddSelect("cat_Name", "c", "Category");
		
		// If the current user is not admin only show active discussions & comments
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedComments")) $s->AddWhere("m.Deleted", "1", "!=");
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");			
                $s->AddWhere("c.cat_owner",join(",",array_keys($_SESSION['UserGroups'])).")","in (","and","",0.0);
		if ($Search->Categories != "") {
			$Cats = explode(",",$Search->Categories);
			$CatCount = count($Cats);
			$s->AddWhere("1", "0", "=", "and", "", 0, 1);			
			for ($i = 0; $i < $CatCount; $i++) {
				$s->AddWhere("c.Name", trim($Cats[$i]), "=", "or");
			}
			$s->EndWhereGroup();			
		}
		if ($Search->AuthUsername != "") $s->AddWhere("a.Name", $Search->AuthUsername, "=");
		$s->AddWhere("m.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("m.WhisperUserID", 0, "=", "or", "", 0);
		$s->AddWhere("m.WhisperUserID", "null", "is", "or", "", 0);
		$s->EndWhereGroup();
		$s->AddWhere("t.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("t.WhisperUserID", 0, "=", "or", "", 0);
		$s->AddWhere("t.WhisperUserID", "null", "is", "or", "", 0);
		$s->EndWhereGroup();
		return $s;
	}
	
	function SaveComment(&$Comment, $SkipValidation = 0) {
		if (!$this->Context->Session->User->CanPostComment) {
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionAddComments"));
		} else {
			// If not editing, and the posted comment count is less than the
			// user's current comment count, silently skip the posting and
			// redirect as if everything is normal.
			if (!$SkipValidation && $Comment->CommentID == 0 && $Comment->UserCommentCount < $this->Context->Session->User->CountComments) {
				// Silently fail to post the data
				// Need to get the user's last posted commentID in this discussion and direct them to it
				$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
				$s->SetMainTable("Comment", "c");
				$s->AddSelect("CommentID", "c");
				$s->AddWhere("AuthUserID", $this->Context->Session->UserID, "=");
				$s->AddWhere("DiscussionID", $Comment->DiscussionID, "=");
				$s->AddOrderBy("DateCreated", "c", "desc");
				$s->AddLimit(0,1);
				$LastCommentData = $this->Context->Database->Select($this->Context, $s, $this->Name, "SaveComment", "An error occurred while retrieving your last comment in this discussion.");
				while ($Row = $this->Context->Database->GetRow($LastCommentData)) {
					$Comment->CommentID = ForceInt($Row["CommentID"], 0);
				}
				// Make sure we got it
				if ($Comment->CommentID == 0) $this->Context->ErrorManager->AddError($this->Context, $this->Name, "SaveComment", "Your last comment in this discussion could not be found.");
			} else {
				// Validate the properties
				$SaveComment = $Comment;
				if (!$SkipValidation) {
					$this->ValidateComment($SaveComment);
				}
				if ($this->Context->WarningCollector->Iif()) {
					$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
					
					// If creating a new object
					if ($SaveComment->CommentID == 0) {	
						// Update the user info & check for spam
						if (!$SkipValidation) {
							$UserManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
							$UserManager->UpdateUserCommentCount($this->Context->Session->UserID);
						}
						
						// Format the values for db input
						$SaveComment->FormatPropertiesForDatabaseInput();
				
						// Proceed with the save if there are no warnings
						if ($this->Context->WarningCollector->Count() == 0) {
							$Comment = $SaveComment;
							$s->SetMainTable("Comment", "m");
							$s->AddFieldNameValue("Body", $Comment->Body);
							$s->AddFieldNameValue("FormatType", $Comment->FormatType);
							$s->AddFieldNameValue("RemoteIp", GetRemoteIp(1));
							$s->AddFieldNameValue("DiscussionID", $Comment->DiscussionID);
							$s->AddFieldNameValue("AuthUserID", $this->Context->Session->UserID);
							$s->AddFieldNameValue("DateCreated", MysqlDateTime());
							
							$Comment->CommentID = $this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveComment", "An error occurred while creating a new discussion comment.");
						
							$s->Clear();
							$s->SetMainTable("Discussion", "t");
							$s->AddFieldNameValue("CountComments", "CountComments+1", "0");
							$s->AddFieldNameValue("DateLastActive", MysqlDateTime());
							$s->AddFieldNameValue("LastUserID", $this->Context->Session->UserID);
							$s->AddWhere("DiscussionID", $Comment->DiscussionID, "=");
							$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveComment", "An error occurred while updating the discussion's comment summary.");
						}
					} else {
						// Format the values for db input
						$Comment->FormatPropertiesForDatabaseInput();
				
						// Finally, update the comment
						$s->Clear();
						$s->SetMainTable("Comment", "m");
						$s->AddFieldNameValue("Body", $Comment->Body);
						$s->AddFieldNameValue("FormatType", $Comment->FormatType);
						$s->AddFieldNameValue("RemoteIp", GetRemoteIp(1));
						$s->AddFieldNameValue("EditUserID", $this->Context->Session->UserID);
						$s->AddFieldNameValue("DateEdited", MysqlDateTime());
						$s->AddWhere("CommentID", $Comment->CommentID, "=");
						$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveComment", "An error occurred while attempting to update the discussion comment.");
					}
				}
			}
		}
		$n=$this->Context->ObjectFactory->NewContextObject($this->Context, "Notify");
                $n->NotifyComment($Comment->CommentID,$this);
		return $this->Context->WarningCollector->Iif($Comment,false);
	}
	
	function SwitchCommentProperty($CommentID, $DiscussionID, $Switch) {
		$DiscussionID = ForceInt($DiscussionID, 0);
		$CommentID = ForceInt($CommentID, 0);
		if ($DiscussionID == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrDiscussionID"));
		if ($CommentID == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrCommentID"));
		if (!$this->Context->Session->User->AdminCategories) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionComments"));
		
		if ($this->Context->WarningCollector->Count() == 0) {
			// Get some information about the comment being manipulated
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("Comment", "m");
			$s->AddSelect(array("AuthUserID"), "m");
			$s->AddWhere("CommentID", $CommentID, "=");
			// Don't touch comments that are already switched to the selected status
			$s->AddWhere("Deleted", $Switch, "<>");
			$CommentData = $this->Context->Database->Select($this->Context, $s, $this->Name, "SwitchCommentProperty", "An error occurred while retrieving information about the comment.");
			$AuthUserID = 0;
			while ($Row = $this->Context->Database->GetRow($CommentData)) {
				$AuthUserID = ForceInt($Row["AuthUserID"], 0);
			}
         $MathOperator = ($Switch == 0?"+":"-");
			if ($AuthUserID > 0) {
				// Update the discussion table comment count
				$this->UpdateCommentCount($DiscussionID, $MathOperator);
          
			}
			// And finally, mark the comment as deleted
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("Comment", "m");
			$s->AddFieldNameValue("Deleted", $Switch);
			if ($Switch == 1) {
				$s->AddFieldNameValue("DeleteUserID", $this->Context->Session->UserID);
				$s->AddFieldNameValue("DateDeleted", MysqlDateTime());
			}
			$s->AddWhere("CommentID", $CommentID, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "SwitchCommentProperty", "An error occurred while marking the comment as inactive.");
		}
		return $this->Context->WarningCollector->Iif();
	}
	
	// Handles manipulating the count value for a discussion when adding, hiding, or deleting a comment
	function UpdateCommentCount($DiscussionID, $MathOperator) {
		$Math = "+";
		if ($MathOperator != "+") $Math = "-";
		
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("Discussion", "d");
		$s->AddFieldNameValue("CountComments", "CountComments".$Math."1", 0);
		$s->AddWhere("DiscussionID", $DiscussionID, "=");
		$this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateCommentCount", "An error occurred while manipulating the comment count for the discussion.");
	}
	
	// Validates and formats properties ensuring they're safe for database input
	// Returns: boolean value indicating success
	function ValidateComment(&$Comment, $DiscussionIDRequired = "1") {
		$DiscussionIDRequired = ForceBool($DiscussionIDRequired, 0);
		if ($DiscussionIDRequired) {
			$Comment->DiscussionID = ForceInt($Comment->DiscussionID, 0);
			if ($Comment->DiscussionID == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrDiscussionID"));
		}
		
		// First update the values so they are safe for db input
		$Body = FormatStringForDatabaseInput($Comment->Body);

		// Instantiate a new validator for each field
		Validate($this->Context->GetDefinition("CommentsLower"), 1, $Body, agMAX_COMMENT_LENGTH, "", $this->Context);
		
		return $this->Context->WarningCollector->Iif();
	}
}
?>
