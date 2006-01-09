<?php
/*
Extension Name: Whispers
Extension Url: http://lussumo.com/docs/
Description: Allows users to whisper private comments & discussions to each other
Version: 1.1
Author: Mark O'Sullivan
Author Url: http://www.markosullivan.ca/
*/

/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*/

// Let it skip over these classes if it doesn't need them
if (in_array($Context->SelfUrl, array("search.php", "comments.php", "post.php", "index.php"))) {
   // Set the new object factory references 
   $Context->ObjectFactory->SetReference("Discussion", "WhisperDiscussion");
   $Context->ObjectFactory->SetReference("DiscussionManager", "WhisperDiscussionManager");
   $Context->ObjectFactory->SetReference("Comment", "WhisperComment");
   $Context->ObjectFactory->SetReference("CommentManager", "WhisperCommentManager");
   
   class WhisperDiscussion extends Discussion {
      var $WhisperUserID;	// If this discussion was whispered to a particular user
      var $WhisperFullName;		// Display purposes only - The user's name
      var $WhisperUsername;		// Display purposes only - The user's username
      var $CountWhispersTo;
      var $CountWhispersFrom;
      
      // Clears all properties
      function Clear() {
         $this->DiscussionID = 0;
         $this->FirstCommentID = 0;
         $this->CategoryID = 0;
         $this->Category = "";
         $this->AuthUserID = 0;
         $this->AuthFullName = "";
         $this->AuthUsername = "";
         $this->WhisperUserID = 0;
         $this->WhisperFullName = "";
         $this->WhisperUsername = "";
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
         $this->CountWhispersTo = 0;
         $this->CountWhispersFrom = 0;
         $this->CountComments = 0;
         $this->CountReplies = 0;
         $this->Comment = 0;
         $this->LastViewed = "";
         $this->LastViewCountComments = 0;
         $this->NewComments = 0;
         $this->Status = "Unread";
         $this->LastPage = 1;
         $this->UserDiscussionCount = 0;
      }
      
      function WhisperDiscussion() {
         $this->Clear();
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
         $this->WhisperUserID = ForceInt(@$DataSet["WhisperUserID"], 0);
         $this->WhisperFullName = ForceString(@$DataSet["WhisperFullName"], "");
         $this->WhisperUsername = ForceString(@$DataSet["WhisperUsername"], "");
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
         
         $WhisperFromDateLastActive = UnixTimestamp(@$DataSet["WhisperFromDateLastActive"]);
         $WhisperFromLastUserID = ForceInt(@$DataSet["WhisperFromLastUserID"], 0);
         $WhisperFromLastFullName = ForceString(@$DataSet["WhisperFromLastFullName"], "");
         $WhisperFromLastUsername = ForceString(@$DataSet["WhisperFromLastUsername"], "");
         $this->CountWhispersFrom = ForceInt(@$DataSet["CountWhispersFrom"], 0);
         
         $WhisperToDateLastActive = UnixTimestamp(@$DataSet["WhisperToDateLastActive"]);
         $WhisperToLastUserID = ForceInt(@$DataSet["WhisperToLastUserID"], 0);
         $WhisperToLastFullName = ForceString(@$DataSet["WhisperToLastFullName"], "");
         $WhisperToLastUsername = ForceString(@$DataSet["WhisperToLastUsername"], "");
         $this->CountWhispersTo = ForceInt(@$DataSet["CountWhispersTo"], 0);
         
         $this->CountComments += $this->CountWhispersFrom;
         $this->CountComments += $this->CountWhispersTo;
         $this->CountReplies = $this->CountComments - 1;
         if ($this->CountReplies < 0) $this->CountReplies = 0;
         
         if ($WhisperFromDateLastActive != "") {
            if ($this->DateLastActive < $WhisperFromDateLastActive) {
               $this->DateLastActive = $WhisperFromDateLastActive;
               $this->LastUserID = $WhisperFromLastUserID;
               $this->LastFullName = $WhisperFromLastFullName;
               $this->LastUsername = $WhisperFromLastUsername;
            }
         }
         if ($WhisperToDateLastActive != "") {
            if ($this->DateLastActive < $WhisperToDateLastActive) {
               $this->DateLastActive = $WhisperToDateLastActive;
               $this->LastUserID = $WhisperToLastUserID;
               $this->LastFullName = $WhisperToLastFullName;
               $this->LastUsername = $WhisperToLastUsername;
            }
         }
         
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
         $this->WhisperUsername = ForceIncomingString("WhisperUsername", "");
         $this->WhisperUsername = stripslashes($this->WhisperUsername);
         $this->Name = ForceIncomingString("Name", "");
         $this->UserDiscussionCount = ForceIncomingInt("UserDiscussionCount", 0);
         // Load the comment
         $this->Comment = $Context->ObjectFactory->NewObject($this->Context, "Comment");
         $this->Comment->GetPropertiesFromForm($Context);
      }
      
      function GetStatus() {
         $sReturn = "";
         if ($this->Closed) $sReturn = " Closed";
         if ($this->WhisperUserID > 0) $sReturn .= " Whispered";
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
         $this->WhisperFullName = FormatStringForDisplay($this->WhisperFullName);
         $this->WhisperUsername = FormatStringForDisplay($this->WhisperUsername);
         $this->LastFullName = FormatStringForDisplay($this->LastFullName);
         $this->LastUsername = FormatStringForDisplay($this->LastUsername);
         $this->Category = FormatStringForDisplay($this->Category);
         $this->Name = FormatStringForDisplay($this->Name);
      }
   }
   
   class WhisperDiscussionManager extends DiscussionManager {
      function WhisperDiscussionManager(&$Context) {
         $this->Name = "WhisperDiscussionManager";
         $this->Context = &$Context;
      }	
   
      // Returns a SqlBuilder object with all of the Discussion properties already defined in the select
      function GetDiscussionBuilder($s = 0) {
         if (!$s) $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
         $s->SetMainTable("Discussion", "t");
         $s->AddSelect(array("DiscussionID", "FirstCommentID", "AuthUserID", "WhisperUserID", "Active", "Closed", "Sticky", "Name", "DateCreated", "LastUserID", "DateLastActive", "CountComments", "CategoryID"), "t");
   
         // Get author data
         $s->AddJoin("User", "u", "UserID", "t", "AuthUserID", "left join");
         $s->AddSelect("Name", "u", "AuthUsername");
         $s->AddSelect("FirstName", "u", "AuthFullName", "concat", "' ',u.LastName");
   
         // Get last poster data
         $s->AddJoin("User", "lu", "UserID", "t", "LastUserID", "left join");
         $s->AddSelect("Name", "lu", "LastUsername");
         $s->AddSelect("FirstName", "lu", "LastFullName", "concat", "' ',lu.LastName");
         
         // Get Whisper user data
         $s->AddJoin("User", "wt", "UserID", "t", "WhisperUserID", "left join");
         $s->AddSelect("Name", "wt", "WhisperUsername");
         
         // Get data on the last user to send a whisper (to the current user) in the discussion
         if ($this->Context->Session->User->ShowAllWhispers) {
            // Get the counts (grouped - hence the need to move the "whisper to" and "whisper from" values to the Discussion table for admins).
            // Select "whisper from" and "whisper to" columns from the Discussion table
            $s->AddJoin("DiscussionUserWhisperFrom", "tuwf", "DiscussionID", "t", "DiscussionID", "left join");
            $s->AddJoin("User", "wluf", "UserID", "t", "WhisperFromLastUserID", "left join");
            $s->AddJoin("User", "wlut", "UserID", "t", "WhisperToLastUserID", "left join");
            $s->AddSelect(array("WhisperFromLastUserID", "WhisperToLastUserID"), "t");
            $s->AddSelect("DateLastWhisper", "t", "WhisperFromDateLastActive");
            $s->AddSelect("DateLastWhisper", "t", "WhisperToDateLastActive");
            // Get the total whisper from count
            $s->AddSelect("TotalWhisperCount", "t", "CountWhispersFrom");
            // Count the whispers to (admin's see all)
            $s->AddSelect("0", "", "CountWhispersTo");
         } else {
            // Select "whisper from" columns from the user-specific tables         
            // Get data on the last user to receive a whisper (for the current, viewing user)
            $s->AddJoin("DiscussionUserWhisperFrom", "tuwf", "DiscussionID and tuwf.WhisperFromUserID = ".$this->Context->Session->UserID, "t", "DiscussionID", "left join");
            $s->AddJoin("User", "wluf", "UserID", "tuwf", "LastUserID", "left join");
            $s->AddSelect("LastUserID", "tuwf", "WhisperFromLastUserID");
            $s->AddSelect("DateLastActive", "tuwf", "WhisperFromDateLastActive");
            // Get the total whisper from count
            $s->AddSelect("CountWhispers", "tuwf", "CountWhispersFrom");
            
            // Select "whisper to" columns from the user specific tables
            // Get data on the last user to send a whisper (for the current, viewing user)
            $s->AddJoin("DiscussionUserWhisperTo", "tuwt", "DiscussionID and tuwt.WhisperToUserID = ".$this->Context->Session->UserID, "t", "DiscussionID", "left join");
            $s->AddJoin("User", "wlut", "UserID", "tuwt", "LastUserID", "left join");
            $s->AddSelect("LastUserID", "tuwt", "WhisperToLastUserID");
            $s->AddSelect("DateLastActive", "tuwt", "WhisperToDateLastActive");
            // Count the whispers to
            $s->AddSelect("CountWhispers", "tuwt", "CountWhispersTo");
         }
         
         // Now that the wluf and wlut tables are defined, assign the whisper names
         $s->AddSelect("Name", "wluf", "WhisperFromLastUsername");
         $s->AddSelect("FirstName", "wluf", "WhisperFromLastFullName", "concat", "' ',wluf.LastName");
         $s->AddSelect("Name", "wlut", "WhisperToLastUsername");
         $s->AddSelect("FirstName", "wlut", "WhisperToLastFullName", "concat", "' ',wlut.LastName");
         
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
         $s->AddGroupBy("DiscussionID", "t");
         return $s;
      }
      
      function GetDiscussionById($DiscussionID, $RecordDiscussionView = "0") {
         $RecordDiscussionView = ForceBool($RecordDiscussionView, 0);
         $Discussion = $this->Context->ObjectFactory->NewObject($this->Context, "Discussion");
         $s = $this->GetDiscussionBuilder();
         $s->AddWhere("t.DiscussionID", $DiscussionID, "=");
         if (!$this->Context->Session->User->ShowAllWhispers) {
            // If the user cannot view all whispers, make sure that:
            // if the current topic is a whisper, make sure it is the
            // author or the whisper recipient viewing
            $s->AddWhere("t.AuthUserID = ".$this->Context->Session->UserID." or t.WhisperUserID = ".$this->Context->Session->UserID." or t.WhisperUserID", 0, "=", "and", "", 1, 1);
            $s->EndWhereGroup();
         }
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
         if ($PrivateDiscussionsOnly) {
            $s->AddWhere("t.WhisperUserID", $this->Context->Session->UserID, "=", "and", "", 0, 1);
            $s->AddWhere("t.AuthUserID", $this->Context->Session->UserID, "=", "or", "", 0, 1);
            $s->AddWhere("t.WhisperUserID", 0, ">", "and");
            $s->EndWhereGroup();
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
         if (!$this->Context->Session->User->ShowAllWhispers) {
            // If the user cannot view all whispers, make sure that:
            // if the current topic is a whisper, make sure it is the
            // author or the whisper recipient viewing
            $s->AddWhere("t.AuthUserID = ".$this->Context->Session->UserID." or t.WhisperUserID = ".$this->Context->Session->UserID." or t.WhisperUserID", 0, "=", "and", "", 1, 1);
            $s->EndWhereGroup();
         }
         
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
         if ($PrivateDiscussionsOnly) {
            $s->AddWhere("t.WhisperUserID", $this->Context->Session->UserID, "=", "and", "", 0, 1);
            $s->AddWhere("t.AuthUserID", $this->Context->Session->UserID, "=", "or", "", 0, 1);
            $s->AddWhere("t.WhisperUserID", 0, ">", "and");
            $s->EndWhereGroup();
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
         if (!$this->Context->Session->User->ShowAllWhispers) {
            // If the user cannot view all whispers, make sure that:
            // if the current topic is a whisper, make sure it is the
            // author or the whisper recipient viewing
            $s->AddWhere("t.AuthUserID = ".$this->Context->Session->UserID." or t.WhisperUserID = ".$this->Context->Session->UserID." or t.WhisperUserID", 0, "=", "and", "", 1, 1);
            $s->EndWhereGroup();
         }
         
         $s->AddOrderBy("Sticky", "t");
         if ($this->Context->Session->User->ShowAllWhispers) {
            $s->AddOrderBy("greatest(t.DateLastWhisper, t.DateLastActive)", "", "desc");
         } else {
            $s->AddOrderBy("greatest(tuwf.DateLastActive, t.DateLastActive)", "", "desc");
         }
         if ($RowsPerPage > 0) $s->AddLimit($FirstRecord, $RowsPerPage);
   
         return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetDiscussionList", "An error occurred while retrieving discussions.");
      }
   
      function GetDiscussionSearch($RowsPerPage, $CurrentPage, $Search) {
         $s = $this->GetSearchBuilder($Search);
         if ($this->Context->Session->User->ShowAllWhispers) {
            $s->AddOrderBy("greatest(t.DateLastWhisper, t.DateLastActive)", "", "desc");
         } else {
            $s->AddOrderBy("greatest(tuwf.DateLastActive, t.DateLastActive)", "", "desc");
         }
         if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");
         if ($RowsPerPage > 0) {
            $CurrentPage = ForceInt($CurrentPage, 1);
            if ($CurrentPage < 1) $CurrentPage == 1;
            $RowsPerPage = ForceInt($RowsPerPage, 50);
            $FirstRecord = ($CurrentPage * $RowsPerPage) - $RowsPerPage;
         }		
         if ($RowsPerPage > 0) $s->AddLimit($FirstRecord, $RowsPerPage+1);
   
         return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetDiscussionSearch", "An error occurred while retrieving search results.");
      }
      
      function GetPrivateDiscussionsByUserID($UserID, $RecordsToReturn = "0") {
         $UserID = ForceInt($UserID, 0);
         $RecordsToReturn = ForceInt($RecordsToReturn, 0);
         
         $s = $this->GetDiscussionBuilder();
         if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");
         $s->AddWhere("t.WhisperUserID", $UserID, "=", "and", "", 0, 1);
         $s->AddWhere("t.AuthUserID", $UserID, "=", "or", "", 0, 1);
         $s->AddWhere("t.WhisperUserID", 0, ">", "and");
         $s->EndWhereGroup();
         $s->EndWhereGroup();
         $s->AddOrderBy("DateLastActive", "t", "desc");
         if ($RecordsToReturn > 0) $s->AddLimit(0, $RecordsToReturn);
   
         return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetPrivateDiscussionsByUserID", "An error occurred while retrieving private discussions.");
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
         if ($Search->WhisperFilter) $s->AddWhere("t.WhisperUserID", 0, ">");
         if ($Search->AuthUsername != "") $s->AddWhere("u.Name", $Search->AuthUsername, "=");
         if (!$this->Context->Session->User->ShowAllWhispers) {
            // If the user cannot view all whispers, make sure that:
            // if the current topic is a whisper, make sure it is the
            // author or the whisper recipient viewing
            $s->AddWhere("(t.AuthUserID = ".$this->Context->Session->UserID." or t.WhisperUserID = ".$this->Context->Session->UserID." or t.WhisperUserID", 0, "=");
            $s->EndWhereGroup();
         }
   
         return $s;
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
               
               // Validate the whisperusername
               $CommentManager->ValidateWhisperUsername($Discussion);
               
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
                        $s->AddFieldNameValue("WhisperUserID", $Discussion->WhisperUserID);
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
   }
   
   class WhisperComment extends Comment {
      var $WhisperUserID;	// The user being whispered to
      var $WhisperUsername;
      
      // Clears all properties
      function Clear() {
         $this->CommentID = 0;
         $this->DiscussionID = 0;
         $this->DiscussionWhisperUserID = 0;
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
         $this->WhisperUserID = 0;
         $this->WhisperUsername = "";
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
      }
      
      function WhisperComment() {
         $this->Clear();
      }
      
      function FormatPropertiesForDatabaseInput() {
         $this->Body = FormatStringForDatabaseInput($this->Body);
         $this->WhisperUsername = FormatStringForDatabaseInput($this->WhisperUsername);
      }
      
      function FormatPropertiesForDisplay($Context, $ForFormDisplay = "0") {
         if (!$Context->Session->User->Setting("HtmlOn", 1)) $this->ShowHtml = 0;
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
         $this->WhisperUsername = FormatStringForDisplay($this->WhisperUsername);
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
         $this->WhisperUsername = FormatStringForDisplay($this->WhisperUsername);
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
         $this->DiscussionWhisperUserID = ForceInt(@$DataSet["DiscussionWhisperUserID"], 0);
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
         $this->WhisperUserID = ForceInt(@$DataSet["WhisperUserID"], 0);
         $this->WhisperUsername = ForceString(@$DataSet["WhisperUsername"], "");
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
         $this->WhisperUsername = ForceIncomingString("WhisperUsername", "");
         $this->Body = ForceIncomingString("Body", "");
         $this->UserCommentCount = ForceIncomingInt("UserCommentCount", 0);
         // Pass the body into a formatter for db input
         $this->Body = $Context->FormatString($this->Body, $this, $this->FormatType, agFORMATSTRINGFORDATABASE);
      }
      
      function GetStatus($UserID) {
         $sReturn = "";
         if ($this->WhisperUserID > 0) {
            if ($this->AuthUserID == $UserID) {
               $sReturn = "WhisperFrom";
            } else {
               $sReturn = "WhisperTo";
            }
         } elseif ($this->DiscussionWhisperUserID > 0) {
            if ($this->AuthUserID == $this->DiscussionWhisperUserID) {
               $sReturn = "WhisperFrom";
            } else {
               $sReturn = "WhisperTo";
            }
            if ($this->DiscussionWhisperUserID != $UserID) {
               if ($sReturn == "WhisperFrom") {
                  $sReturn = "WhisperTo";
               } else {
                  $sReturn = "WhisperFrom";
               }
            }
         }
         return $sReturn;
      }	
   }
   
   class WhisperCommentManager extends CommentManager {
      
      function WhisperCommentManager(&$Context) {
         $this->Name = "WhisperCommentManager";
         $this->Context = &$Context;
      }
      
      // Returns a SqlBuilder object with all of the comment properties already defined in the select
      function GetCommentBuilder($s = 0) {
         if (!$s) $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
         $s->SetMainTable("Comment", "m");
         $s->AddSelect(array("CommentID", "DiscussionID", "Body", "FormatType", "DateCreated", "DateEdited", "DateDeleted", "Deleted", "AuthUserID", "EditUserID", "DeleteUserID", "RemoteIp", "WhisperUserID"), "m");
         $s->AddSelect("FirstName", "a", "AuthFullName", "concat", "' ',a.LastName");
         $s->AddSelect("WhisperUserID", "t", "DiscussionWhisperUserID");
         $s->AddSelect("Name", "a", "AuthUsername");
         $s->AddSelect("Name", "w", "WhisperUsername");
         $s->AddSelect("Icon", "a", "AuthIcon");
         $s->AddSelect("Name", "r", "AuthRole");
         $s->AddSelect("RoleID", "r", "AuthRoleID");
         $s->AddSelect("Description", "r", "AuthRoleDesc");
         $s->AddSelect("Icon", "r", "AuthRoleIcon");
         $s->AddSelect("CanPostHtml", "r", "AuthCanPostHtml");
         $s->AddSelect("FirstName", "e", "EditFullName", "concat", "' ',e.LastName");
         $s->AddSelect("Name", "e", "EditUsername");
         $s->AddSelect("FirstName", "d", "DeleteFullName", "concat", "' ',d.LastName");
         $s->AddSelect("Name", "d", "DeleteUsername");
         $s->AddSelect("Blocked", "ab", "AuthBlocked", "coalesce", "0");
         $s->AddSelect("Blocked", "cb", "CommentBlocked", "coalesce", "0");
         $s->AddJoin("User", "a", "UserID", "m", "AuthUserID", "inner join");
         $s->AddJoin("Role", "r", "RoleID", "a", "RoleID", "left join");
         $s->AddJoin("User", "e", "UserID", "m", "EditUserID", "left join");
         $s->AddJoin("User", "d", "UserID", "m", "DeleteUserID", "left join");
         $s->AddJoin("User", "w", "UserID", "m", "WhisperUserID", "left join");
         $s->AddJoin("UserBlock", "ab", "BlockedUserID and ab.BlockingUserID = ".$this->Context->Session->UserID, "m", "AuthUserID", "left join");
         $s->AddJoin("CommentBlock", "cb", "BlockedCommentID and cb.BlockingUserID = ".$this->Context->Session->UserID, "m", "CommentID", "left join");
         $s->AddJoin("Discussion", "t", "DiscussionID", "m", "DiscussionID", "inner join");
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
      
      function GetCommentCount($DiscussionID) {
         $TotalNumberOfRecords = 0;
         $DiscussionID = ForceInt($DiscussionID, 0);
         
         // If the current user is admin, see if they can view inactive comments
         // If the current user is not admin only show active comments
         $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
         $s->SetMainTable("Comment", "m");
         $s->AddSelect("CommentID", "m", "Count", "count");
         $s->AddJoin("Discussion", "t", "DiscussionID", "m", "DiscussionID", "inner join");
         if (!$this->Context->Session->User->ShowAllWhispers) {
            $s->AddWhere("m.WhisperUserID", $this->Context->Session->UserID, "=", "and", "", 1, 1);
            $s->AddWhere("m.WhisperUserID", "null", "is", "or", "", 0);
            $s->AddWhere("m.WhisperUserID", "0", "=", "or", "", 0);
            $s->AddWhere("m.WhisperUserID", "0", "=", "or", "", 1);
            $s->AddWhere("m.AuthUserID", $this->Context->Session->UserID, "=", "or");
            $s->EndWhereGroup();
         }
         if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedComments")) {
            $s->AddWhere("m.Deleted", 0, "=", "and", "", 1, 1);
            $s->AddWhere("m.Deleted", 0, "=", "or", "" ,0);
            $s->EndWhereGroup();
         }
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
         if (!$this->Context->Session->User->ShowAllWhispers) {
            $s->AddWhere("m.WhisperUserID", $this->Context->Session->UserID, "=", "and", "", 1, 1);
            $s->AddWhere("m.WhisperUserID", "null", "is", "or", "", 0);
            $s->AddWhere("m.WhisperUserID", "0", "=", "or", "", 0);
            $s->AddWhere("m.WhisperUserID", "0", "=", "or", "", 1);
            $s->AddWhere("m.AuthUserID", $this->Context->Session->UserID, "=", "or");
            $s->EndWhereGroup();
         }
         // If the user isn't a root admin, assign some limitations on this query
         if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedComments")) {
            $s->AddWhere("m.Deleted", 0, "=", "and", "", 1, 1);
            $s->AddWhere("m.Deleted", 0, "=", "or", "" ,0);
            $s->EndWhereGroup();
         }
         $s->AddWhere("m.DiscussionID", $DiscussionID, "=");
         $s->AddOrderBy("DateCreated", "m", "asc");
         if ($RowsPerPage > 0) $s->AddLimit($FirstRecord, $RowsPerPage);
   
         return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCommentList", "An error occurred while attempting to retrieve the requested comments.");
      }
      
      function GetSearchBuilder($Search) {
         $Search->FormatPropertiesForDatabaseInput();
         $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlSearch");
         $s = $this->GetCommentBuilder($s);
         $s->UserQuery = $Search->Query;
         $s->SearchFields = array("m.Body");
         $s->DefineSearch();
         $s->AddSelect("Name", "t", "Discussion");
         $s->AddJoin("phpgw_categories", "c", "cat_id", "t", "CategoryID", "left join","");
         $s->AddSelect("CategoryID", "t");
         $s->AddSelect("cat_Name", "c", "Category");
         
         // If the current user is not admin only show active discussions & comments
         if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedComments")) $s->AddWhere("m.Deleted", "0", "=");
         if (!$this->Context->Session->User->AdminCategories || !$this->Context->Session->User->Setting("ShowDeletedDiscussions")) $s->AddWhere("t.Active", "1", "=");			
   
         if ($Search->Categories != "") {
            $Cats = explode(",",$Search->Categories);
            $CatCount = count($Cats);
            $s->AddWhere("1", "0", "=", "and", "", 0, 1);			
            for ($i = 0; $i < $CatCount; $i++) {
               $s->AddWhere("c.Name", trim($Cats[$i]), "=", "or");
            }
            $s->EndWhereGroup();			
         }
         if ($Search->WhisperFilter) $s->AddWhere("m.WhisperUserID", 0, ">");
         if ($Search->AuthUsername != "") $s->AddWhere("a.Name", $Search->AuthUsername, "=");
         if (!$this->Context->Session->User->ShowAllWhispers) {
            // If the user cannot view all whispers, make sure that:
            // if the current topic is a whisper, make sure it is the
            // author or the whisper recipient viewing
            $s->AddWhere("(t.AuthUserID = ".$this->Context->Session->UserID." or t.WhisperUserID = ".$this->Context->Session->UserID." or t.WhisperUserID", 0, "=");
            $s->EndWhereGroup();
            $s->AddWhere("(m.AuthUserID = ".$this->Context->Session->UserID." or m.WhisperUserID = ".$this->Context->Session->UserID." or m.WhisperUserID", 0, "=");
            $s->EndWhereGroup();
         }
   
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
                  $this->ValidateWhisperUsername($SaveComment);
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
                        // This SaveComment was put in because if a spam block went into effect, the user's text would then be display in the comment box in a database-saving format.
                        $Comment = $SaveComment;
                        
                        $s->SetMainTable("Comment", "m");
                        $s->AddFieldNameValue("Body", $Comment->Body);
                        $s->AddFieldNameValue("FormatType", $Comment->FormatType);
                        $s->AddFieldNameValue("RemoteIp", GetRemoteIp(1));
                        $s->AddFieldNameValue("DiscussionID", $Comment->DiscussionID);
                        $s->AddFieldNameValue("AuthUserID", $this->Context->Session->UserID);
                        $s->AddFieldNameValue("DateCreated", MysqlDateTime());
                        $s->AddFieldNameValue("WhisperUserID", $Comment->WhisperUserID);
                        
                        $Comment->CommentID = $this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveComment", "An error occurred while creating a new discussion comment.");
                     
                        // If there were no errors, update the discussion count & time
                        if ($Comment->WhisperUserID) {
                           // Whisper-to table
                           if ($Comment->WhisperUserID != $this->Context->Session->UserID) {
                              // Only record the whisper to if the user is not whispering to him/herself - this is to make sure that the counts come out correctly when counting replies for a discussion
                              $s->Clear();
                              $s->SetMainTable("DiscussionUserWhisperTo", "tuwt");
                              $s->AddFieldNameValue("CountWhispers", "CountWhispers+1", "0");
                              $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
                              $s->AddFieldNameValue("LastUserID", $this->Context->Session->UserID);
                              $s->AddWhere("DiscussionID", $Comment->DiscussionID, "=");
                              $s->AddWhere("WhisperToUserID", $Comment->WhisperUserID, "=");
                              if ($this->Context->Database->Update($this->Context, $s, $this->Name, "SaveComment", "An error occurred while updating the discussion's comment summary.") <= 0) {
                                 // If no records were updated, then insert a new row to the table for this discussion/user whisper
                                 $s->Clear();
                                 $s->SetMainTable("DiscussionUserWhisperTo", "tuwt");
                                 $s->AddFieldNameValue("CountWhispers", "1");
                                 $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
                                 $s->AddFieldNameValue("DiscussionID", $Comment->DiscussionID);
                                 $s->AddFieldNameValue("WhisperToUserID", $Comment->WhisperUserID);
                                 $s->AddFieldNameValue("LastUserID", $this->Context->Session->UserID);
                                 $this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveComment", "An error occurred while updating the discussion's comment summary.");
                              }
                           }
                           // Whisper-from table
                           $s->Clear();
                           $s->SetMainTable("DiscussionUserWhisperFrom", "tuwf");
                           $s->AddFieldNameValue("CountWhispers", "CountWhispers+1", "0");
                           $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
                           $s->AddFieldNameValue("LastUserID", $this->Context->Session->UserID);
                           $s->AddWhere("DiscussionID", $Comment->DiscussionID, "=");
                           $s->AddWhere("WhisperFromUserID", $this->Context->Session->UserID, "=");
                           if ($this->Context->Database->Update($this->Context, $s, $this->Name, "SaveComment", "An error occurred while updating the discussion's comment summary.") <= 0) {
                              // If no records were updated, then insert a new row to the table for this discussion/user whisper
                              $s->Clear();
                              $s->SetMainTable("DiscussionUserWhisperFrom", "tuwf");
                              $s->AddFieldNameValue("CountWhispers", "1");
                              $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
                              $s->AddFieldNameValue("DiscussionID", $Comment->DiscussionID);
                              $s->AddFieldNameValue("WhisperFromUserID", $this->Context->Session->UserID);
                              $s->AddFieldNameValue("LastUserID", $this->Context->Session->UserID);
                              $this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveComment", "An error occurred while updating the discussion's comment summary.");
                           }
                           // Update the discussion table
                           $s->Clear();
                           $s->SetMainTable("Discussion", "t");
                           $s->AddFieldNameValue("DateLastWhisper", MysqlDateTime());
                           $s->AddFieldNameValue("WhisperToLastUserID", $Comment->WhisperUserID);
                           $s->AddFieldNameValue("WhisperFromLastUserID", $this->Context->Session->UserID);
                           $s->AddFieldNameValue("TotalWhisperCount", "TotalWhisperCount+1", 0);
                           $s->AddWhere("DiscussionID", $Comment->DiscussionID, "=");
                           $this->Context->Database->Update($this->Context, $s, $this->Name, "SaveComment", "An error occurred while updating the discussion's whisper summary.");
                        } else {
                           $s->Clear();
                           $s->SetMainTable("Discussion", "t");
                           $s->AddFieldNameValue("CountComments", "CountComments+1", "0");
                           $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
                           $s->AddFieldNameValue("LastUserID", $this->Context->Session->UserID);
                           $s->AddWhere("DiscussionID", $Comment->DiscussionID, "=");
                           $this->Context->Database->Update($this->Context, $s, $this->Name, "SaveComment", "An error occurred while updating the discussion's comment summary.");
                        }
                     }
                  } else {
                     // Format the values for db input
                     $Comment->FormatPropertiesForDatabaseInput();
                     
                     // Get information about the comment being edited
                     $s->SetMainTable("Comment", "m");
                     $s->AddSelect(array("AuthUserID", "WhisperUserID"), "m");
                     $s->AddWhere("CommentID", $Comment->CommentID, "=");
                     $CommentData = $this->Context->Database->Select($this->Context, $s, $this->Name, "SaveComment", "An error occurred while retrieving information about the comment.");
                     $WhisperToUserID = 0;
                     $WhisperFromUserID = 0;
                     while ($Row = $this->Context->Database->GetRow($CommentData)) {
                        $WhisperToUserID = ForceInt($Row["WhisperUserID"], 0);
                        $WhisperFromUserID = ForceInt($Row["AuthUserID"], 0);
                     }
                     if ($WhisperToUserID > 0 && $Comment->WhisperUserID == 0) {
                        // If the original comment was whispered and the new one isn't
                        // 1. Update the whisper count for this discussion
                        $this->UpdateWhisperCount($Comment->DiscussionID, $WhisperFromUserID, $WhisperToUserID, "-");
                        // 2. Update the comment count for this discussion
                        $this->UpdateCommentCount($Comment->DiscussionID, "+");
                        
                     } elseif ($WhisperToUserID == 0 && $Comment->WhisperUserID > 0){                  
                        // If the original comment was not whispered and the new one is
                        // 1. Update the comment count for this discussion
                        $this->UpdateCommentCount($Comment->DiscussionID, "-");					
                        // 2. Update the whisper count for this discussion
                        $this->UpdateWhisperCount($Comment->DiscussionID, $WhisperFromUserID, $Comment->WhisperUserID, "+");
                        
                     } else {
                        // Otherwise, the counts do not need to be manipulated
                     }
                        
                     // Finally, update the comment
                     $s->Clear();
                     $s->SetMainTable("Comment", "m");
                     $s->AddFieldNameValue("WhisperUserID", $Comment->WhisperUserID);
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
            $s->AddSelect(array("AuthUserID", "WhisperUserID"), "m");
            $s->AddWhere("CommentID", $CommentID, "=");
            // Don't touch comments that are already switched to the selected status
            $s->AddWhere("Deleted", $Switch, "<>");
            $CommentData = $this->Context->Database->Select($this->Context, $s, $this->Name, "SwitchCommentProperty", "An error occurred while retrieving information about the comment.");
            $WhisperToUserID = 0;
            $WhisperFromUserID = 0;
            while ($Row = $this->Context->Database->GetRow($CommentData)) {
               $WhisperToUserID = ForceInt($Row["WhisperUserID"], 0);
               $WhisperFromUserID = ForceInt($Row["AuthUserID"], 0);
            }
            $MathOperator = ($Switch == 0?"+":"-");
            if ($WhisperToUserID > 0) {
               // If this was a whisper, update the whisper count tables
               // Update the discussion table (holds the whisper count for admins)
               $this->UpdateWhisperCount($DiscussionID, $WhisperFromUserID, $WhisperToUserID, $MathOperator);
               
            } elseif ($WhisperFromUserID > 0) {
               // If this was not a whisper, update the discussion table comment count
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
      
      // Handles manipulating the count values for a discussion when adding, hiding, or removing a whispered comment
      function UpdateWhisperCount($DiscussionID, $WhisperFromUserID, $WhisperToUserID, $MathOperator) {
         $Math = "+";
         if ($MathOperator != "+") $Math = "-";
         
         // 1. Update the whispercount for this discussion
         
         $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
         $s->SetMainTable("Discussion", "t");
         $s->AddFieldNameValue("TotalWhisperCount", "TotalWhisperCount".$Math."1", 0);
         $s->AddWhere("DiscussionID", $DiscussionID, "=");
         $this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateWhisperCount", "An error occurred while manipulating the discussion's comment count.");
         
         // 2. Update the DiscussionUserWhisperFrom table
         $s->Clear();
         $s->SetMainTable("DiscussionUserWhisperFrom", "tuwf");
         $s->AddFieldNameValue("CountWhispers", "CountWhispers".$Math."1", 0);
         $s->AddWhere("DiscussionID", $DiscussionID, "=");
         $s->AddWhere("WhisperFromUserID", $WhisperFromUserID, "=");
         // If no rows were affected, make sure to insert the data
         if ($Math == "+" && $this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateWhisperCount", "An error occurred while manipulating the whisper count for the user who sent the whisper.") == 0) {
            $s->Clear();
            $s->SetMainTable("DiscussionUserWhisperFrom", "tuwf");
            $s->AddFieldNameValue("CountWhispers", "1");
            $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
            $s->AddFieldNameValue("DiscussionID", $DiscussionID);
            $s->AddFieldNameValue("WhisperFromUserID", $WhisperFromUserID);
            $s->AddFieldNameValue("LastUserID", $WhisperFromUserID);
            $this->Context->Database->Insert($this->Context, $s, $this->Name, "UpdateWhisperCount", "An error occurred while updating the discussion's comment summary.");
         }
         
         // 3. Update the DiscussionUserWhisperTo table
         // But only if the user was not whispering to him/herself (because this value is not incremented if that is the case)
         if ($WhisperToUserID != $WhisperFromUserID) {
            $s->Clear();
            $s->SetMainTable("DiscussionUserWhisperTo", "tuwt");
            $s->AddFieldNameValue("CountWhispers", "CountWhispers".$Math."1", 0);
            $s->AddWhere("DiscussionID", $DiscussionID, "=");
            $s->AddWhere("WhisperToUserID", $WhisperToUserID, "=");
            // If no rows were affected, make sure to insert the data
            if ($Math == "+" && $this->Context->Database->Update($this->Context, $s, $this->Name, "UpdateWhisperCount", "An error occurred while manipulating the whisper count for the user who received the whisper.") == 0) {
               $s->Clear();
               $s->SetMainTable("DiscussionUserWhisperTo", "tuwt");
               $s->AddFieldNameValue("CountWhispers", "1");
               $s->AddFieldNameValue("DateLastActive", MysqlDateTime());
               $s->AddFieldNameValue("DiscussionID", $DiscussionID);
               $s->AddFieldNameValue("WhisperToUserID", $WhisperToUserID);
               $s->AddFieldNameValue("LastUserID", $WhisperFromUserID);
               $this->Context->Database->Insert($this->Context, $s, $this->Name, "UpdateWhisperCount", "An error occurred while updating the discussion's comment summary.");
            }
         }
      }	
      
      function ValidateWhisperUsername(&$Comment) {
         if ($Comment->WhisperUsername != "") {
            $Name = FormatStringForDatabaseInput($Comment->WhisperUsername);
            $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
            $s->SetMainTable("User", "u");
            $s->AddSelect("UserID", "u");
            $s->AddWhere("Name", $Name, "=");
            $Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "ValidateWhisperUsername", "An error occurred while attempting to validate the username entered as the whisper recipient.");
            while ($Row = $this->Context->Database->GetRow($Result)) {
               $Comment->WhisperUserID = ForceInt($Row["UserID"], 0);
            }
            if ($Comment->WhisperUserID == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrWhisperInvalid"));
         }
         return $this->Context->WarningCollector->Iif();
      }	
   }   
}


// Add the appendix to the comments page if applicable
if ($Context->SelfUrl == "comments.php" && $Context->Session->UserID > 0 && $Context->Session->User->Setting("ShowAppendices", 1)) {
   $Context->ObjectFactory->AddControlString("VanillaFunctions", "AddAppendixToPanel", "<h2>".$Context->GetDefinition("Appendix")."</h2>
      <ul class=\"LinkedList Appendix\">
         <li class=\"Appendix WhisperFrom\">".$Context->GetDefinition("YouWhispered")."</li>
         <li class=\"Appendix WhisperTo\">".$Context->GetDefinition("WhisperedToYou")."</li>
      </ul>");
}

// Add "show private discussions in control panel" option to the functionality form on the account page
if ($Context->SelfUrl == "account.php") {
   $Context->ObjectFactory->AddControlString("FunctionalityForm", "RenderPreferences", "<div class=\"CheckBox\">".GetDynamicCheckBox("ShowPrivateDiscussions", 1, $Context->Session->User->Setting("ShowPrivateDiscussions"), "PanelSwitch('ShowPrivateDiscussions');", $Context->GetDefinition("DisplayPrivateDiscussions"))."</div>");
}

// Add private discussions to panel on index and comments page
if (in_array($Context->SelfUrl, array("comments.php", "index.php")) && $Context->Session->UserID > 0) {
   // I perform this extra check so I don't have to instantiate a new discussion manager needlessly
   if ($Context->Session->User->Setting("ShowPrivateDiscussions")) {
      $dm = $Context->ObjectFactory->NewContextObject($Context, "DiscussionManager");   
      AddDiscussionsToPanel($Context, $Panel, $dm, "GetPrivateDiscussionsByUserID", agPANEL_PRIVATE_COUNT, $Context->GetDefinition("Private"), "Private", $Context->Session->User->Setting("ShowPrivateDiscussions"));
   }
}

// Add the whisper input to the discussion & comment forms
if (in_array($Context->SelfUrl, array("comments.php", "post.php"))) {
   // Need to load the appropriate whisperusername if editing
   $WhisperUsername = ForceIncomingString("WhisperUsername","");
	$CommentID = ForceIncomingInt("CommentID", 0);
   $Discussion = 0;
   $dm = 0;
   $DiscussionID = ForceIncomingInt("DiscussionID", 0);
   if ($CommentID > 0) {
   	$Comment = $Context->ObjectFactory->NewObject($Context, "Comment");
   	$cm = $Context->ObjectFactory->NewContextObject($Context, "CommentManager");
		$Comment = $cm->GetCommentById($CommentID, $Context->Session->UserID);
      $WhisperUsername = ForceIncomingString("WhisperUsername", $Comment->WhisperUsername);
      if ($Comment) {
			$DiscussionID = $Comment->DiscussionID;
         $dm = $Context->ObjectFactory->NewContextObject($Context, "DiscussionManager");
			$Discussion = $dm->GetDiscussionById($Comment->DiscussionID);
         if ($Discussion->FirstCommentID == $CommentID) $WhisperUsername = $Discussion->WhisperUsername;
      }
   }
   
   $WhisperInput = "
   <script>
		var wac = new AutoComplete('wac');
	</script>
			
   <dt class=\"WhisperInputLabel\">".$Context->GetDefinition("WhisperYourCommentsTo")."</dt>
   <dd class=\"WhisperInput\">
      <input type=\"text\" name=\"WhisperUsername\" class=\"WhisperBox\" maxlength=\"20\" value=\"".FormatStringForDisplay($WhisperUsername, 0)."\" onKeyUp=\"return wac.LoadData(this, event, 'WhisperACContainer');\" onblur=\"wac.HideAutoComplete();\" autocomplete=\"off\" />
      <br /><div id=\"WhisperACContainer\" class=\"AutoCompleteContainer\" style=\"display: none;\"></div>
   </dd>";

	if ($DiscussionID == 0) $Context->ObjectFactory->AddControlString("DiscussionForm", "GetDiscussionForm", $WhisperInput);
   // Don't allow whispers if the discussion is already private
   if (!$Discussion && $DiscussionID > 0) {
      if (!$dm) $dm = $Context->ObjectFactory->NewContextObject($Context, "DiscussionManager");
      $Discussion = $dm->GetDiscussionById($DiscussionID);
   }
   if ($Discussion && $Discussion->WhisperUserID > 0) {
      // Don't add the whisper input
   } else {
      $Context->ObjectFactory->AddControlString("DiscussionForm", "GetCommentForm", $WhisperInput);
   }
}

// Add the "your private discussions" option to the control panel
if (
   in_array($Context->SelfUrl, array("categories.php", "comments.php", "index.php", "post.php"))
   && $Context->Session->UserID > 0
   && !$Context->Session->User->Setting("ShowPrivateDiscussions")
   ) $Panel->AddListItem($Context->GetDefinition("DiscussionFilters"), $Context->GetDefinition("PrivateDiscussions"), "./?View=Private");

?>