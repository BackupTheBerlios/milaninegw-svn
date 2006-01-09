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
* Description: Discussion & Discussion Management classes
*/

class Discussion {
	var $DiscussionID;
	var $FirstCommentID;
	var $CategoryID;
	var $Category;
	var $AuthUserID;
	var $AuthFullName;		// Display purposes only - The user's name
	var $AuthUsername;		// Display purposes only - The user's username
	var $LastUserID;		// The user that last added comments to the Discussion
	var $LastFullName;		// Display purposes only - The user's name
	var $LastUsername;		// Display purposes only - The user's username
	var $Active;			// Boolean value indicating if the Discussion is visible to non-administrators
	var $Closed;			// Boolean value indicating if the Discussion will allow any further Comments to be added
	var $Sticky;			// Boolean value indicating if the Discussion should appear at the top of the list
	var $Bookmarked;		// Boolean value indicating if the Discussion has been bookmared by the current user
	var $Name;
	var $DateCreated;
	var $DateLastActive;
	var $CountComments;		// Number of Comments currently in this Discussion
   var $CountReplies;		// Number of replies currently in this Discussion (one less than the Comment count)
	var $Comment;				// Only used when creating/editing a discussion
   var $LastViewed;
	var $LastViewCountComments;
	var $NewComments;
	var $Status;
	var $LastPage;				// The last page of the discussion
  	// Used to prevent double posts and "back button" posts
   var $UserDiscussionCount;
	var $WhisperUserID;		// I stripped out whispers as an extension, but the class breaks if this isn't in here. Need to redo the way discussion lists are drawn

	
	// Clears all properties
	function Clear() {
		$this->DiscussionID = 0;
		$this->FirstCommentID = 0;
		$this->CategoryID = 0;
		$this->Category = "";
		$this->AuthUserID = 0;
		$this->AuthFullName = "";
		$this->AuthUsername = "";
		$this->LastUserID = 0;
		$this->LastFullName = "";
		$this->LastUsername = "";
		$this->Active = 0;
		$this->Closed = 0;
		$this->Sticky = 0;
		$this->Bookmarked = 0;
		$this->Name = "";
		$this->DateCreated = "";
		$this->DateLastActive = "";
		$this->CountComments = 0;
		$this->CountReplies = 0;
		$this->Comment = 0;
		$this->LastViewed = "";
		$this->LastViewCountComments = 0;
		$this->NewComments = 0;
		$this->Status = "Unread";
		$this->LastPage = 1;
		$this->UserDiscussionCount = 0;
		$this->WhisperUserID = 0;
	}
	
	function Discussion() {
		$this->Clear();
	}

	function ForceNameSpaces() {
		$this->Name = $this->SplitString($this->Name, agMAX_TOPIC_WORD_LENGTH);
	}
	
	// Retrieve properties from current DataRowSet
	function GetPropertiesFromDataSet($DataSet) {
		$this->DiscussionID = ForceInt(@$DataSet["DiscussionID"], 0);
		$this->FirstCommentID = ForceInt(@$DataSet["FirstCommentID"], 0);
		$this->CategoryID = ForceInt(@$DataSet["CategoryID"], 0);
		$this->Category = ForceString(@$DataSet["Category"], "");
		$this->AuthUserID = ForceInt(@$DataSet["AuthUserID"], 0);
		$this->AuthFullName = ForceString(@$DataSet["AuthFullName"], "");
		$this->AuthUsername = ForceString(@$DataSet["AuthUsername"], "");
		$this->LastUserID = ForceInt(@$DataSet["LastUserID"], 0);
		$this->LastFullName = ForceString(@$DataSet["LastFullName"], "");
		$this->LastUsername = ForceString(@$DataSet["LastUsername"], "");
		$this->Active = ForceBool(@$DataSet["Active"], 0);
		$this->Closed = ForceBool(@$DataSet["Closed"], 0);
		$this->Sticky = ForceBool(@$DataSet["Sticky"], 0);
		$this->Bookmarked = ForceBool(@$DataSet["Bookmarked"], 0);
		$this->Name = ForceString(@$DataSet["Name"], "");
		$this->DateCreated = UnixTimestamp(@$DataSet["DateCreated"]);
		$this->DateLastActive = UnixTimestamp(@$DataSet["DateLastActive"]);
		$this->CountComments = ForceInt(@$DataSet["CountComments"], 0);
		$this->CountReplies = $this->CountComments - 1;
		if ($this->CountReplies < 0) $this->CountReplies = 0;
		$this->LastViewed = UnixTimestamp(@$DataSet["LastViewed"]);
		$this->LastViewCountComments = ForceInt(@$DataSet["LastViewCountComments"], 0);
		if ($this->LastViewed != "") {
			$this->NewComments = $this->CountComments - $this->LastViewCountComments;
			if ($this->NewComments < 0) $this->NewComments = 0;
		} else {
			$this->NewComments = $this->CountComments;
		}
		$this->Status = $this->GetStatus();
		
		// Define the last page
		$this->LastPage = CalculateNumberOfPages($this->CountComments, agCOMMENTS_PER_PAGE);
	}	

	// Retrieve a properties from incoming form variables
	function GetPropertiesFromForm(&$Context) {
		$this->DiscussionID = ForceIncomingInt("DiscussionID", 0);
		$this->CategoryID = ForceIncomingInt("CategoryID", 0);
		$this->Name = ForceIncomingString("Name", "");
		$this->UserDiscussionCount = ForceIncomingInt("UserDiscussionCount", 0);
		// Load the comment
      $this->Comment = $Context->ObjectFactory->NewObject($this->Context, "Comment");
		$this->Comment->GetPropertiesFromForm($Context);
	}
	
	function GetStatus() {
		$sReturn = "";
		if ($this->Closed) $sReturn = " Closed";
		if ($this->Sticky) $sReturn .= " Sticky";
		if ($this->Bookmarked) $sReturn .= " Bookmarked";
		if ($this->LastViewed != "") {
			$sReturn .= " Read";
		} else {
			$sReturn .= " Unread";
		}
		if ($this->NewComments > 0) {
			$sReturn .= " NewComments";
		} else {
			$sReturn .= " NoNewComments";
		}
		return $sReturn;
	}
	
	function FormatPropertiesForDisplay() {
		$this->AuthFullName = FormatStringForDisplay($this->AuthFullName);
		$this->AuthUsername = FormatStringForDisplay($this->AuthUsername);
		$this->LastFullName = FormatStringForDisplay($this->LastFullName);
		$this->LastUsername = FormatStringForDisplay($this->LastUsername);
		$this->Category = FormatStringForDisplay($this->Category);
		$this->Name = FormatStringForDisplay($this->Name);
	}

	function SplitString($String, $MaxLength) {
		if (strlen($String) > $MaxLength) {
			$Words = explode(" ", $String);
			for ($i = 0; $i < count($Words); $i++) {
				if (strlen($Words[$i]) >= $MaxLength) {
					$Words[$i] = substr($Words[$i], 0, $MaxLength)." ".$this->SplitString(substr($Words[$i], $MaxLength), $MaxLength);
				}
			}
			$String = implode(" ",$Words);
		}
		return $String;
	}
}

class DiscussionManager {
	var $Name;				// The name of this class
   var $Context;			// The context object that contains all global objects (database, error manager, warning collector, session, etc)
	
	function DiscussionManager(&$Context) {
		$this->Name = "DiscussionManager";
		$this->Context = &$Context;
	}	

	function GetBookmarkedDiscussionsByUserID($UserID, $RecordsToReturn = "0", $IncludeDiscussionID = "0") {
		$IncludeDiscussionID = ForceInt($IncludeDiscussionID, 0);
		$UserID = ForceInt($UserID, 0);
		$RecordsToReturn = ForceInt($RecordsToReturn, 0);
		
		$s = $this->GetDiscussionBuilder();
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");
		$s->AddWhere("b.DiscussionID", "t.DiscussionID", "=", "and", "", 0, 1);
		$s->AddWhere("b.UserID", $UserID, "=");
		$s->EndWhereGroup();
		$s->AddWhere("t.DiscussionID", $IncludeDiscussionID, "=", "or");
		$s->AddOrderBy("DateLastActive", "t", "desc");
		if ($RecordsToReturn > 0) $s->AddLimit(0, $RecordsToReturn);
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetBookmarkedDiscussionsByUserID", "An error occurred while retrieving discussions.");
	}
	
	// Returns a SqlBuilder object with all of the Discussion properties already defined in the select
	function GetDiscussionBuilder($s = 0) {
		if (!$s) $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->SetMainTable("Discussion", "t");
		$s->AddSelect(array("DiscussionID", "FirstCommentID", "AuthUserID", "Active", "Closed", "Sticky", "Name", "DateCreated", "LastUserID", "DateLastActive", "CountComments", "CategoryID"), "t");

		// Get author data
		$s->AddJoin("phpgw_accounts", "egwu", "account_id", "t", "AuthUserID", "left join","");
		$s->AddSelect("account_lid", "egwu", "AuthUsername");
		$s->AddSelect("account_firstname", "egwu", "AuthFullName", "concat", "' ',egwu.account_lastname");

		// Get last poster data
		$s->AddJoin("phpgw_accounts", "egwlu", "account_id", "t", "LastUserID", "left join","");
		$s->AddSelect("account_lid", "egwlu", "LastUsername");
		$s->AddSelect("account_firstname", "egwlu", "LastFullName", "concat", "' ',egwlu.account_lastname");
		
		// Get category data
		$s->AddJoin("phpgw_categories", "c", "cat_id", "t", "CategoryID", "left join","");
		$s->AddSelect("cat_name", "c", "Category");
		
		// Limit to roles with access to this category
      if ($this->Context->Session->UserID > 0) {
			$s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = ".$this->Context->Session->User->RoleID, "t", "CategoryID", "left join");
		} else {
			$s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = 1", "t", "CategoryID", "left join");
		}
		$s->AddWhere("coalesce(crb.Blocked, 0)", "0", "=", "and", "", 0, 0);
			
		
		// Bookmark data
		$s->AddJoin("UserBookmark", "b", "DiscussionID and b.UserID = ".$this->Context->Session->UserID, "t", "DiscussionID", "left outer join");
		$s->AddSelect("DiscussionID is not null", "b", "Bookmarked");
		
		// Discussion watch data for the current user
		$s->AddJoin("UserDiscussionWatch", "utw", "DiscussionID and utw.UserID = ".$this->Context->Session->UserID, "t", "DiscussionID", "left join");
		$s->AddSelect("LastViewed", "utw");
		$s->AddSelect("CountComments", "utw", "LastViewCountComments", "coalesce", "0");
		$s->AddWhere("c.cat_owner",join(",",array_keys($_SESSION['UserGroups'])).")","in (","and","",0.0);
		$s->AddGroupBy("DiscussionID", "t");
		return $s;
	}
	
	function GetDiscussionById($DiscussionID, $RecordDiscussionView = "0") {
		$RecordDiscussionView = ForceBool($RecordDiscussionView, 0);
		$Discussion = $this->Context->ObjectFactory->NewObject($this->Context, "Discussion");
		$s = $this->GetDiscussionBuilder();
		$s->AddWhere("t.DiscussionID", $DiscussionID, "=");
		$s->AddWhere("t.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("t.WhisperUserID", 0, "=", "or", "" ,0);
		$s->AddWhere("t.WhisperUserID", "null", "is", "or", "" ,0);
		$s->EndWhereGroup();				
		$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetDiscussionById", "An error occurred while attempting to retrieve the requested discussion.");
		if ($this->Context->Database->RowCount($result) == 0) {
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrDiscussionNotFound"));
			$Discussion = false;
		}
		while ($rows = $this->Context->Database->GetRow($result)) {
			$Discussion->GetPropertiesFromDataSet($rows);
		}
		if ($Discussion && $RecordDiscussionView) {
			$s->Clear();
			$s->SetMainTable("UserDiscussionWatch", "utw");
			$s->AddFieldNameValue("CountComments", $Discussion->CountComments);
			$s->AddFieldNameValue("LastViewed", MysqlDateTime());
			// If there was no entry, create a new one
			if ($Discussion->LastViewed == "") {
				$s->AddFieldNameValue("UserID", $this->Context->Session->UserID);
				$s->AddFieldNameValue("DiscussionID", $DiscussionID);
				// fail silently
            $this->Context->Database->Insert($this->Context, $s, $this->Name, "GetDiscussionById", "An error occurred while recording this discussion viewing.", 0, 0);
			} else {
				// otherwise update
            $s->AddWhere("UserID", $this->Context->Session->UserID, "=");
            $s->AddWhere("DiscussionID", $Discussion->DiscussionID, "=");
				// fail silently
            $this->Context->Database->Update($this->Context, $s, $this->Name, "GetDiscussionById", "An error occurred while recording this discussion viewing.", 0);
			}
		}
		return $this->Context->WarningCollector->Iif($Discussion, false);
	}
	
	function GetDiscussionCount($CategoryID, $BookmarkedDiscussionsOnly = "0", $PrivateDiscussionsOnly = "0", $DiscussionStarterUserID = "0") {
		$CategoryID = ForceInt($CategoryID, 0);
		$BookmarkedDiscussionsOnly = ForceBool($BookmarkedDiscussionsOnly, 0);
		$PrivateDiscussionsOnly = ForceBool($PrivateDiscussionsOnly, 0);
		$DiscussionStarterUserID = ForceInt($DiscussionStarterUserID, 0);
		$TotalNumberOfRecords = 0;
		
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
		$s->AddSelect("DiscussionID", "t", "Count", "count");
		$s->SetMainTable("Discussion", "t");
		$s->AddJoin("UserBookmark", "b", "DiscussionID and b.UserID = ".$this->Context->Session->UserID, "t", "DiscussionID", "left outer join");

		// Limit to roles with access to this category
      if ($this->Context->Session->UserID > 0) {
			$s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = ".$this->Context->Session->User->RoleID, "t", "CategoryID", "left join");
		} else {
			$s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = 1", "t", "CategoryID", "left join");
		}
		$s->AddWhere("coalesce(crb.Blocked, 0)", "0", "=", "and", "", 0, 0);


		if ($BookmarkedDiscussionsOnly) {
			$s->AddWhere("b.DiscussionID", "t.DiscussionID", "=", "and", "", 0, 1);
			$s->AddWhere("b.UserID", $this->Context->Session->UserID, "=");
			$s->EndWhereGroup();
		}
		if ($DiscussionStarterUserID > 0) $s->AddWhere("t.AuthUserID", $DiscussionStarterUserID, "=");
		
		// If the current user is not admin only show active Discussions
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");
		if ($CategoryID > 0) {
			$s->AddWhere("t.CategoryID", $CategoryID, "=");
		} elseif ($this->Context->Session->UserID > 0) {
			$s->AddJoin("CategoryBlock", "cb", "CategoryID and cb.UserID = ".$this->Context->Session->UserID, "t", "CategoryID", "left join");
			$s->AddWhere("coalesce(cb.Blocked,0)", 1, "<>");
		}
		$s->AddWhere("t.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("t.WhisperUserID", 0, "=", "or", "" ,0);
		$s->AddWhere("t.WhisperUserID", "null", "is", "or", "" ,0);
		$s->EndWhereGroup();				
	
		$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetDiscussionCount", "An error occurred while retrieving Discussion information.");
		while ($rows = $this->Context->Database->GetRow($result)) {
			$TotalNumberOfRecords = $rows['Count'];
		}
		return $TotalNumberOfRecords;
	}
	
	function GetDiscussionList($RowsPerPage, $CurrentPage, $CategoryID, $BookmarkedDiscussionsOnly = "0", $PrivateDiscussionsOnly = "0", $DiscussionStarterUserID = "0") {
		$CategoryID = ForceInt($CategoryID, 0);
		$BookmarkedDiscussionsOnly = ForceBool($BookmarkedDiscussionsOnly, 0);
		$PrivateDiscussionsOnly = ForceBool($PrivateDiscussionsOnly, 0);
		$DiscussionStarterUserID = ForceInt($DiscussionStarterUserID, 0);
		$TotalNumberOfRecords = 0;
		
		if ($RowsPerPage > 0) {
			$CurrentPage = ForceInt($CurrentPage, 1);
			if ($CurrentPage < 1) $CurrentPage == 1;
			$RowsPerPage = ForceInt($RowsPerPage, 50);
			$FirstRecord = ($CurrentPage * $RowsPerPage) - $RowsPerPage;
		}
		
		$s = $this->GetDiscussionBuilder();
		if ($BookmarkedDiscussionsOnly) {
			$s->AddWhere("b.DiscussionID", "t.DiscussionID", "=", "and", "", 0, 1);
			$s->AddWhere("b.UserID", $this->Context->Session->UserID, "=");
			$s->EndWhereGroup();
		}
		if ($DiscussionStarterUserID > 0) $s->AddWhere("t.AuthUserID", $DiscussionStarterUserID, "=");
		$s->AddGroupBy("DiscussionID", "t");
		
		// If the current user is not admin only show active Discussions
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");
		if ($CategoryID > 0) {
			$s->AddWhere("t.CategoryID", $CategoryID, "=");
		} elseif ($this->Context->Session->UserID > 0) {
			$s->AddJoin("CategoryBlock", "cb", "CategoryID and cb.UserID = ".$this->Context->Session->UserID, "t", "CategoryID", "left join");
			$s->AddWhere("coalesce(cb.Blocked,0)", 1, "<>");
		}
		$s->AddWhere("t.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("t.WhisperUserID", 0, "=", "or", "" ,0);
		$s->AddWhere("t.WhisperUserID", "null", "is", "or", "" ,0);
		$s->EndWhereGroup();				
		
		$s->AddOrderBy("Sticky", "t");
		$s->AddOrderBy("t.DateLastActive", "", "desc");
		if ($RowsPerPage > 0) $s->AddLimit($FirstRecord, $RowsPerPage);

		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetDiscussionList", "An error occurred while retrieving discussions.");
	}

	function GetDiscussionsByUserID($UserID, $RecordsToReturn = "0") {
		$UserID = ForceInt($UserID, 0);
		$RecordsToReturn = ForceInt($RecordsToReturn, 0);
		
		$s = $this->GetDiscussionBuilder();
		$s->AddWhere("t.AuthUserID", $UserID, "=");
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");
		$s->AddWhere("t.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("t.WhisperUserID", 0, "=", "or", "" ,0);
		$s->AddWhere("t.WhisperUserID", "null", "is", "or", "" ,0);
		$s->EndWhereGroup();		
		$s->AddOrderBy("DateLastActive", "t", "desc");
		if ($RecordsToReturn > 0) $s->AddLimit(0, $RecordsToReturn);

		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetDiscussionsByUserID", "An error occurred while retrieving discussions.");
	}
	
	function GetDiscussionSearch($RowsPerPage, $CurrentPage, $Search) {
		$s = $this->GetSearchBuilder($Search);
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");
		if ($RowsPerPage > 0) {
			$CurrentPage = ForceInt($CurrentPage, 1);
			if ($CurrentPage < 1) $CurrentPage == 1;
			$RowsPerPage = ForceInt($RowsPerPage, 50);
			$FirstRecord = ($CurrentPage * $RowsPerPage) - $RowsPerPage;
		}		
		if ($RowsPerPage > 0) $s->AddLimit($FirstRecord, $RowsPerPage+1);
		$s->AddWhere("t.WhisperUserID", 0, "=", "and", "", 1, 1);
		$s->AddWhere("t.WhisperUserID", 0, "=", "or", "" ,0);
		$s->AddWhere("t.WhisperUserID", "null", "is", "or", "" ,0);
		$s->EndWhereGroup();		
		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetDiscussionSearch", "An error occurred while retrieving search results.");
	}
	
	function GetSearchBuilder($Search) {
		$Search->FormatPropertiesForDatabaseInput();
		$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlSearch");
		$s = $this->GetDiscussionBuilder($s);
		$s->UserQuery = $Search->Query;
		$s->SearchFields = array("t.Name");
		$s->DefineSearch();
		
		// If the current user is not admin only show active Discussions
		if (!$this->Context->Session->User->AdminCategories) $s->AddWhere("t.Active", "1", "=");
		if ($Search->Categories != "") {
			$Cats = explode(",",$Search->Categories);
			$CatCount = count($Cats);
			$s->AddWhere("1", "0", "=", "and", "", 0, 1);			
			for ($i = 0; $i < $CatCount; $i++) {
				$s->AddWhere("c.Name", trim($Cats[$i]), "=", "or");
			}
			$s->EndWhereGroup();			
		}
		if ($Search->AuthUsername != "") $s->AddWhere("u.Name", $Search->AuthUsername, "=");

		return $s;
	}
	
	function GetViewedDiscussionsByUserID($UserID, $RecordsToReturn = "0") {
		$UserID = ForceInt($UserID, 0);
		$RecordsToReturn = ForceInt($RecordsToReturn, 0);
		
		$s = $this->GetDiscussionBuilder();
		if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");
		$s->AddWhere("utw.UserID", $UserID, "=");
		$s->AddOrderBy("LastViewed", "utw", "desc");
		if ($RecordsToReturn > 0) $s->AddLimit(0, $RecordsToReturn);

		return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetViewedDiscussionsByUserID", "An error occurred while retrieving discussions.");
	}
	
	function SaveDiscussion($Discussion) {
		if (!$this->Context->Session->User->CanPostDiscussion) {
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionStartDiscussions"));
		} else {		
			// If not editing, and the posted discussion count is less than the
			// user's current discussion count, silently skip the posting and
			// redirect as if everything is normal.
			if ($Discussion->DiscussionID == 0 && $Discussion->UserDiscussionCount < $this->Context->Session->User->CountDiscussions) {
				// Silently fail to post the data
				// Need to get the user's last posted discussionID and direct them to it
				$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
				$s->SetMainTable("Discussion", "d");
				$s->AddSelect("DiscussionID", "d");
				$s->AddWhere("AuthUserID", $this->Context->Session->UserID, "=");
				$s->AddOrderBy("DateCreated", "d", "desc");
				$s->AddLimit(0,1);
				$LastDiscussionData = $this->Context->Database->Select($this->Context, $s, $this->Name, "SaveDiscussion", "An error occurred while retrieving your last discussion.");
				while ($Row = $this->Context->Database->GetRow($LastDiscussionData)) {
					$Discussion->DiscussionID = ForceInt($Row["DiscussionID"], 0);
				}
				// Make sure we got it
				if ($Discussion->DiscussionID == 0) $this->Context->ErrorManager->AddError($this->Context, $this->Name, "SaveDiscussion", "Your last discussion could not be found.");
			} else {
				$NewDiscussion = 0;
				$OldDiscussion = false;
				if ($Discussion->DiscussionID == 0) {
					$NewDiscussion = 1;
				} else {
					$OldDiscussion = $this->GetDiscussionById($Discussion->DiscussionID);			
				}
				// Validate the Discussion topic
				$Name = FormatStringForDatabaseInput($Discussion->Name);
				Validate($this->Context->GetDefinition("DiscussionTopicLower"), 1, $Name, 100, "", $this->Context);
				if ($Discussion->CategoryID <= 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrSelectCategory"));
				
				// Validate first comment
				$Discussion->Comment->DiscussionID = $Discussion->DiscussionID;
				if ($OldDiscussion) {
					$Discussion->Comment->CommentID = $OldDiscussion->FirstCommentID;
				} else {
					$Discussion->Comment->CommentID = 0;
				}
				$CommentManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "CommentManager");
				$CommentManager->ValidateComment($Discussion->Comment, 0);
				
				// If updating, validate that this is admin or the author
				if (!$NewDiscussion) {
					if ($OldDiscussion->AuthUserID != $this->Context->Session->UserID && !$this->Context->Session->User->AdminCategories) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionEditComments"));
				}
				
				// If validation was successful, then reset the properties to db safe values for saving
				if ($this->Context->WarningCollector->Count() == 0) $Discussion->Name = $Name;
		
				if($this->Context->WarningCollector->Iif()) {
					$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
					
					// Update the user info & check for spam
					if ($NewDiscussion) {
						$UserManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
						$UserManager->UpdateUserDiscussionCount($this->Context->Session->UserID);
					}
					
					// Proceed with the save if there are no warnings
					if ($this->Context->WarningCollector->Count() == 0) {
						$s->SetMainTable("Discussion");
						$s->AddFieldNameValue("Name", $Discussion->Name);
						$s->AddFieldNameValue("CategoryID", $Discussion->CategoryID);
						if ($NewDiscussion) {				
							$s->AddFieldNameValue("AuthUserID", $this->Context->Session->UserID);
							$s->AddFieldNameValue("DateCreated", MysqlDateTime());
							$s->AddFieldNameValue("DateLastactive", MysqlDateTime());
							$s->AddFieldNameValue("CountComments", 0);
							$Discussion->DiscussionID = $this->Context->Database->Insert($this->Context, $s, $this->Name, "NewDiscussion", "An error occurred while creating a new discussion.");
							$Discussion->Comment->DiscussionID = $Discussion->DiscussionID;
						} else {
							$s->AddWhere("DiscussionID", $Discussion->DiscussionID, "=");
							$this->Context->Database->Update($this->Context, $s, $this->Name, "NewDiscussion", "An error occurred while updating the discussion.");
						}
					}
					
					// Now save the associated Comment
					if ($Discussion->Comment->DiscussionID > 0) {
						$CommentManager->SaveComment($Discussion->Comment, 1);
						
						// Now update the topic table so that we know what the first comment in the discussion was
						if ($Discussion->Comment->CommentID > 0 && $NewDiscussion) {
							$s->Clear();
							$s->SetMainTable("Discussion", "d");
							$s->AddFieldNameValue("FirstCommentID", $Discussion->Comment->CommentID);
							$s->AddWhere("DiscussionID", $Discussion->Comment->DiscussionID, "=");
							$this->Context->Database->Update($this->Context, $s, $this->Name, "NewDiscussion", "An error occurred while updating discussion properties.");
						}
					}
				}
			}
		}
		return $this->Context->WarningCollector->Iif($Discussion,false);		
	}
	
	function SwitchDiscussionProperty($DiscussionID, $PropertyName, $Switch) {
		$DiscussionID = ForceInt($DiscussionID, 0);
		if ($DiscussionID == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrDiscussionID"));
		
		if ($this->Context->WarningCollector->Count() == 0) {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("Discussion");
			$s->AddFieldNameValue($PropertyName, $Switch);
			$s->AddWhere("DiscussionID", $DiscussionID, "=");
			if (!$this->Context->Session->User->AdminCategories) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionCategoryDiscussions"));
			if ($this->Context->Database->Update($this->Context, $s, $this->Name, "SwitchDiscussionProperty", "An error occurred while manipulating the ".$PropertyName." property of the discussion.", 0) <= 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionDiscussionEdit"));
		}
		return $this->Context->WarningCollector->Iif();
	}
}
?>