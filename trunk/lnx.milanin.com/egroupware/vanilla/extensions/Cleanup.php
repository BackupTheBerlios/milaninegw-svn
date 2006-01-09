<?php
/*
Extension Name: Cleanup
Extension Url: http://lussumo.com/docs/
Description: Allows administrators to do various clean-up tasks on the database like removing dead user accounts, permanently deleting hidden comments and discussions, purging all discussions & some backup procedures.
Version: 1.0
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

define("agMYSQL_DUMP_PATH", "");

// If looking at the settings page, use this form
if (($Context->SelfUrl == "settings.php") && ($Context->Session->User->AdminUsers && $Context->Session->User->AdminCategories)) {
	class CleanupForm extends PostBackControl {
      var $Name;                 // The name of this form
      var $HiddenDiscussions;    // The number of hidden discussions in the database
      var $HiddenComments;       // The number of hidden comments in the database
      var $InactiveUsers;        // The number of inactive users in the database
      var $NumberOfUsersRemoved; // The number of users that were removed by the user cleanup process
		
		function CleanupForm(&$Context) {
			$this->ValidActions = array("Cleanup", "CleanupUsers", "CleanupComments", "CleanupDiscussions", "PurgeDiscussions", "BackupDatabase");
			$this->Constructor($Context);
         $this->Name = "CleanupForm";
			if ($this->IsPostBack) {
				if ($this->PostBackAction == "CleanupUsers") {
					$Days = ForceIncomingInt("Days", 30);
					$InactiveUsers = $this->GetInactiveUsers($Days);
					if (count($InactiveUsers) > 0) {
						// Wipe out category blocks
						$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
						$s->SetMainTable("CategoryBlock", "cb");
						$s->AddWhere("UserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user category blocks.");
						
						// Wipe out clippings
                  $s->Clear();
						$s->SetMainTable("Clipping", "c");
						$s->AddWhere("UserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user clippings.");
						
						// Wipe out comment blocks
                  $s->Clear();
						$s->SetMainTable("CommentBlock", "c");
						$s->AddWhere("BlockingUserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user comment blocks.");
						
						// Wipe out the ip history
                  $s->Clear();
						$s->SetMainTable("IpHistory", "I");
						$s->AddWhere("UserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user IP history.");
						
						// Update any styles associated with this user to be system styles
                  $s->Clear();
						$s->SetMainTable("Style", "s");
						$s->AddFieldNameValue("AuthUserID", "0");
						$s->AddWhere("AuthUserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Update($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user style relations.");
						
						// Wipe out any user blocks
                  $s->Clear();
						$s->SetMainTable("UserBlock", "ub");
						$s->AddWhere("BlockingUserID", "(".implode(",",$InactiveUsers).")", "in", "or", "", 0);
						$s->AddWhere("BlockedUserID", "(".implode(",",$InactiveUsers).")", "in", "or", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user blocks.");
						
						// Wipe out bookmarks
                  $s->Clear();
						$s->SetMainTable("UserBookmark", "ub");
						$s->AddWhere("UserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user bookmarks.");
						
						// Wipe out user discussion watch
                  $s->Clear();
						$s->SetMainTable("UserDiscussionWatch", "udw");
						$s->AddWhere("UserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user discussion tracking data.");
						
						// Wipe out role history
                  $s->Clear();
						$s->SetMainTable("UserRoleHistory", "urh");
						$s->AddWhere("UserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user role history.");
						
						// Wipe out saved searches
                  $s->Clear();
						$s->SetMainTable("UserSearch", "us");
						$s->AddWhere("UserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove user searches.");
						
						// Delete the users
                  $s->Clear();
						$s->SetMainTable("User", "u");
						$s->AddWhere("UserID", "(".implode(",",$InactiveUsers).")", "in", "and", "", 0);
						$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove the users.");
					}
					$this->NumberOfUsersRemoved = count($InactiveUsers);
					$this->PostBackValidated = 1;
					
				} elseif ($this->PostBackAction == "CleanupComments") {
					// First get all of the hidden comment ids
					$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
					$s->SetMainTable("Comment", "c");
					$s->AddWhere("Deleted", "1", "=", "and", "", 0);
					$s->AddWhere("Deleted", "1", "=", "or");
					$s->AddSelect("CommentID", "c");
					$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to retrieve hidden comments.");
					$HiddenCommentIDs = array();
					while ($Row = $this->Context->Database->GetRow($Result)) {
						$HiddenCommentIDs[] = ForceInt($Row["CommentID"], 0);
					}
					$HiddenCommentIDs[] = 0;
					
					// Now remove comment blocks
					$s->Clear();
					$s->SetMainTable("CommentBlock", "cb");
					$s->AddWhere("BlockedCommentID", "(".implode(",",$HiddenCommentIDs).")", "in", "and", "", 0);
					$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove hidden comment blocks.");
					
					// Now remove the comments
					$s->Clear();
					$s->SetMainTable("Comment", "c");
					$s->AddWhere("Deleted", "1", "=", "and", "", 0);
					$s->AddWhere("Deleted", "1", "=", "or");
					$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove hidden comments.");
					$this->PostBackValidated = 1;
					
				} elseif ($this->PostBackAction == "CleanupDiscussions") {
					// First get all of the hidden discussion ids
					$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
					$s->SetMainTable("Discussion", "d");
					$s->AddSelect("DiscussionID", "d");
					$s->AddWhere("Active", "0", "=", "and", "", 0);
					$s->AddWhere("Active", "0", "=", "or");
					$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to retrieve hidden discussions.");
					$HiddenDiscussionIDs = array();
					while ($Row = $this->Context->Database->GetRow($Result)) {
						$HiddenDiscussionIDs[] = ForceInt($Row["DiscussionID"], 0);
					}
					$HiddenDiscussionIDs[] = 0;
					
					// Now remove comments associated with those discussions
               $s->Clear();
					$s->SetMainTable("Comment", "c");
					$s->AddWhere("DiscussionID", "(".implode(",",$HiddenDiscussionIDs).")", "in", "and", "", 0);
					$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove hidden discussion comments.");
					
					// Clean up the whisper tables
               $s->Clear();
					$s->SetMainTable("DiscussionUserWhisperFrom", "wf");
					$s->AddWhere("DiscussionID", "(".implode(",",$HiddenDiscussionIDs).")", "in", "and", "", 0);
					$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove hidden discussion whisper data.");
               
               $s->Clear();
					$s->SetMainTable("DiscussionUserWhisperTo", "wt");
					$s->AddWhere("DiscussionID", "(".implode(",",$HiddenDiscussionIDs).")", "in", "and", "", 0);
					$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove hidden discussion whisper data.");
					
					// Remove bookmarks
               $s->Clear();
					$s->SetMainTable("UserBookmark", "ub");
					$s->AddWhere("DiscussionID", "(".implode(",",$HiddenDiscussionIDs).")", "in", "and", "", 0);
					$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove hidden discussion bookmark data.");
					
					// Discussion Watch data
               $s->Clear();
					$s->SetMainTable("UserDiscussionWatch", "uw");
					$s->AddWhere("DiscussionID", "(".implode(",",$HiddenDiscussionIDs).")", "in", "and", "", 0);
					$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove hidden discussion watch data.");
					
					// Now remove the discussions themselves
               $s->Clear();
					$s->SetMainTable("Discussion", "d");
					$s->AddWhere("Active", "0", "=", "and", "", 0);
					$s->AddWhere("Active", "0", "=", "or");
					$this->Context->Database->Delete($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to remove hidden discussions.");
					$this->PostBackValidated = 1;
					
				} elseif ($this->PostBackAction == "PurgeDiscussions") {
					// Purge Whisper tables
               $Sql = "truncate table LUM_DiscussionUserWhisperFrom";
					$this->Context->Database->Execute($this->Context, $Sql, $this->Name, "Constructor", "An error occurred while attempting to truncate whisper relationships.");
               $Sql = "truncate table LUM_DiscussionUserWhisperTo";
					$this->Context->Database->Execute($this->Context, $Sql, $this->Name, "Constructor", "An error occurred while attempting to truncate whisper relationships.");
					
					// Comment Blocks
               $Sql = "truncate table LUM_CommentBlock";
					$this->Context->Database->Execute($this->Context, $Sql, $this->Name, "Constructor", "An error occurred while attempting to truncate comment blocks.");
               
					// Comments
               $Sql = "truncate table LUM_Comment";
					$this->Context->Database->Execute($this->Context, $Sql, $this->Name, "Constructor", "An error occurred while attempting to truncate comments.");
               
					// Discussions
               $Sql = "truncate table LUM_Discussion";
					$this->Context->Database->Execute($this->Context, $Sql, $this->Name, "Constructor", "An error occurred while attempting to truncate discussions.");
               
					// Bookmarks
               $Sql = "truncate table LUM_UserBookmark";
					$this->Context->Database->Execute($this->Context, $Sql, $this->Name, "Constructor", "An error occurred while attempting to truncate bookmarks.");
               
               // User discussion watch
               $Sql = "truncate table LUM_UserDiscussionWatch";
					$this->Context->Database->Execute($this->Context, $Sql, $this->Name, "Constructor", "An error occurred while attempting to truncate user discussion tracking data.");
					
					$this->PostBackValidated = 1;
					
				} elseif ($this->PostBackAction == "BackupDatabase") {
					$FileName = date("Y-m-d-H-i",mktime())."-".dbNAME.".sql";
					$Return = 1;
					$StringArray = array();
					// In order to enable the "system" function in windows, you've got to give
					// "read & execute" and "read" access to the internet guest account:
					// (machinename\iuser_machinename).
					@system(agMYSQL_DUMP_PATH."mysqldump --opt -u ".dbUSER." --password=".dbPASSWORD." ".dbNAME." > ".agAPPLICATION_PATH."images/".$FileName);
					SaveAsDialogue(agAPPLICATION_PATH."images/",$FileName,1);
					
            } elseif ($this->PostBackAction == "Cleanup") {
					// Load some stats
					
					// 1. The number of hidden discussions
					$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
					$s->SetMainTable("Discussion", "d");
					$s->AddSelect("DiscussionID", "d", "HiddenDiscussionCount", "count");
					$s->AddWhere("Active", "0", "=", "and", "", 0);
					$s->AddWhere("Active", "0", "=", "or");
					$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to retrieve hidden discussion statistics.");
					$this->HiddenDiscussions = 0;
					while ($Row = $this->Context->Database->GetRow($Result)) {
						$this->HiddenDiscussions = ForceInt($Row["HiddenDiscussionCount"], 0);
					}
					
					// 2. The number of hidden comments
					$s->Clear();
					$s->SetMainTable("Comment", "d");
					$s->AddSelect("CommentID", "d", "HiddenCommentCount", "count");
					$s->AddWhere("Deleted", "1", "=", "and", "", 0);
					$s->AddWhere("Deleted", "1", "=", "or");
					$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to retrieve hidden comment statistics.");
					$this->HiddenComments = 0;
					while ($Row = $this->Context->Database->GetRow($Result)) {
						$this->HiddenComments = ForceInt($Row["HiddenCommentCount"], 0);
					}
					
					// 3. The number of non-posting users
					$this->InactiveUsers = count($this->GetInactiveUsers());
				}
			}
		}
		
		function GetInactiveUsers($DaysOfMembership = "0") {
			$MembershipDate = SubtractDaysFromTimeStamp(mktime(), $DaysOfMembership);
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddSelect("UserID", "u");
			$s->AddWhere("CountComments", "0", "=", "and", "", 0, 1);
			$s->AddWhere("CountComments", "0", "=", "or");
			$s->EndWhereGroup();
			$s->AddWhere("CountDiscussions", "0", "=", "and", "", 0, 1);
			$s->AddWhere("CountDiscussions", "0", "=", "or");
			$s->EndWhereGroup();
			if ($DaysOfMembership > 0) $s->AddWhere("DateFirstVisit", MysqlDateTime($MembershipDate), "<");
			$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to retrieve inactive user statistics.");
			$this->InactiveUsers = 0;
			$aInactiveUsers = array();
			while ($Row = $this->Context->Database->GetRow($Result)) {
				$aInactiveUsers[] = ForceInt($Row["UserID"], 0);
			}
			
			if (count($aInactiveUsers) > 0) {
				// Now (of these users), remove ones that have whispered
				$s->Clear();
				$s->SetMainTable("DiscussionUserWhisperFrom", "wf");
				$s->AddSelect("WhisperFromUserID", "wf");
				$s->AddWhere("WhisperFromUserID", "(".implode(",",$aInactiveUsers).")", "in", "and", "", 0);
				$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to retrieve inactive user statistics.");
				$CurrentWhisperUserID = 0;
				while ($Row = $this->Context->Database->GetRow($Result)) {
					$CurrentWhisperUserID = ForceInt($Row["WhisperFromUserID"], 0);
					$Key = array_search($CurrentWhisperUserID, $aInactiveUsers);
					if ($Key !== false) array_splice($aInactiveUsers, $Key, 1);
				}
			}
			
			if (count($aInactiveUsers) > 0) {
				// Now (of these users), remove ones that have received whispers
				$s->Clear();
				$s->SetMainTable("DiscussionUserWhisperTo", "wt");
				$s->AddSelect("WhisperToUserID", "wt");
				$s->AddWhere("WhisperToUserID", "(".implode(",",$aInactiveUsers).")", "in", "and", "", 0);
				$Result = $this->Context->Database->Select($this->Context, $s, $this->Name, "Constructor", "An error occurred while attempting to retrieve inactive user statistics.");
				$CurrentWhisperUserID = 0;
				while ($Row = $this->Context->Database->GetRow($Result)) {
					$CurrentWhisperUserID = ForceInt($Row["WhisperToUserID"], 0);
					$Key = array_search($CurrentWhisperUserID, $aInactiveUsers);
					if ($Key !== false) array_splice($aInactiveUsers, $Key, 1);
				}
			}
			
			return $aInactiveUsers;
		}
		
		function Render_ValidPostBack() {
			$this->Context->Writer->Add("<div class=\"SettingsForm\">");
			if ($this->PostBackAction == "CleanupUsers") {
				$this->Context->Writer->Add("<h1>".$this->Context->GetDefinition("CleanupUsers")."</h1>
				<div class=\"Form LanguageChange\">
					<div class=\"InputNote\">".$this->NumberOfUsersRemoved.$this->Context->GetDefinition("UsersRemovedSuccessfully")."</div>");
			} elseif ($this->PostBackAction == "CleanupComments") {
				$this->Context->Writer->Add("<h1>".$this->Context->GetDefinition("CleanupComments")."</h1>
				<div class=\"Form LanguageChange\">
					<div class=\"InputNote\">".$this->Context->GetDefinition("CommentsRemovedSuccessfully")."</div>");
			} elseif ($this->PostBackAction == "CleanupDiscussions") {
				$this->Context->Writer->Add("<h1>".$this->Context->GetDefinition("CleanupDiscussions")."</h1>
				<div class=\"Form LanguageChange\">
					<div class=\"InputNote\">".$this->Context->GetDefinition("DiscussionsRemovedSuccessfully")."</div>");
			} elseif ($this->PostBackAction == "PurgeDiscussions") {
				$this->Context->Writer->Add("<h1>".$this->Context->GetDefinition("PurgeDiscussions")."</h1>
				<div class=\"Form LanguageChange\">
					<div class=\"InputNote\">".$this->Context->GetDefinition("DiscussionsPurgedSuccessfully")."</div>");
			}
					$this->Context->Writer->Add("<div class=\"FormLink\"><a href=\"./settings.php?PostBackAction=Cleanup\">".$this->Context->GetDefinition("ClickHereToContinue")."</a></div>
				</div>
			</div>");
		}
		
		function Render_NoPostBack() {
			if ($this->IsPostBack) {
				if ($this->PostBackAction == "Cleanup") {
					$DaySelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
					$DaySelect->Name = "Days";
					$DaySelect->CssClass = "InlineSelect";
					for ($i = 0; $i < 11; $i++) {
						$DaySelect->AddOption($i, $i);
					}
					$i = 15;
					while ($i < 31) {
						$DaySelect->AddOption($i, $i);
						$i += 5;
					}
					$i = 40;
					while ($i < 61) {
						$DaySelect->AddOption($i, $i);
						$i += 10;
					}
					$i = 90;
					while ($i < 370) {
						$DaySelect->AddOption($i, $i);
						$i += 30;
					}
					$DaySelect->SelectedID = 30;
					$this->Context->Writer->Write("<div class=\"SettingsForm\">
						<h1>".$this->Context->GetDefinition("SystemCleanup")."</h1>
						<div class=\"Form Cleanup\">
							<div class=\"InputNote\">
								<h2>".$this->Context->GetDefinition("BackupDatabase")."</h2>
								<a href=\"settings.php?PostBackAction=BackupDatabase\">".$this->Context->GetDefinition("ClickHereToBackupDatabase")."</a>
								<p>".$this->Context->GetDefinition("BackupDatabaseNotes")."</p>
							</div>


							<div class=\"InputNote\">
								<h2>".$this->Context->GetDefinition("CleanupUsers")."</h2>
								<script language=\"Javascript\">
									function RemoveUsers() {
										if (confirm('".$this->Context->GetDefinition("RemoveUsersConfirm")."')) {
											document.location = 'settings.php?PostBackAction=CleanupUsers&Days='+document.frmUserCleanup.Days.options[document.frmUserCleanup.Days.selectedIndex].value;
										}
									}
								</script>
								<form name=\"frmUserCleanup\">
									".$this->Context->GetDefinition("ThereAreCurrentlyXUsers")
									.$this->InactiveUsers
									.$this->Context->GetDefinition("RemoveUsersOlderThan")
									.$DaySelect->Get()
									.$this->Context->GetDefinition("DaysColon")
									."<a href=\"javascript:RemoveUsers();\">".$this->Context->GetDefinition("Go")."</a>
								</form>
							</div>
							<div class=\"InputNote\">
								<h2>".$this->Context->GetDefinition("CleanupDiscussions")."</h2>
								<script language=\"Javascript\">
									function RemoveDiscussions() {
										if (confirm('".$this->Context->GetDefinition("RemoveDiscussionsConfirm")."')) {
											document.location = 'settings.php?PostBackAction=CleanupDiscussions';
										}
									}
									function RemoveComments() {
										if (confirm('".$this->Context->GetDefinition("RemoveCommentsConfirm")."')) {
											document.location = 'settings.php?PostBackAction=CleanupComments';
										}
									}
									function PurgeDiscussions() {
										if (confirm('".$this->Context->GetDefinition("PurgeDiscussionsConfirm")."')) {
											document.location = 'settings.php?PostBackAction=PurgeDiscussions';
										}
									}
								</script>
								<p>"								
									.$this->Context->GetDefinition("ThereAreCurrently")
									.$this->HiddenDiscussions
									.$this->Context->GetDefinition("HiddenDiscussions")
									."<a href=\"javascript:RemoveDiscussions();\">".$this->Context->GetDefinition("ClickHereToRemoveAllHiddenDiscussions")."</a>
								</p>
								<p>"								
									.$this->Context->GetDefinition("ThereAreCurrently")
									.$this->HiddenComments
									.$this->Context->GetDefinition("HiddenComments")
									."<a href=\"javascript:RemoveComments();\">".$this->Context->GetDefinition("ClickHereToRemoveAllHiddenComments")."</a>
								</p>
								<p><a href=\"javascript:PurgeDiscussions();\">".$this->Context->GetDefinition("ClickHereToPurgeAllDiscussions")."</a></p>
							</div>
						</div>
					</div>");
				}					
			}
		}
	}
	
	$CleanupForm = $Context->ObjectFactory->NewContextObject($Context, "CleanupForm");
	$Body->AddControl($CleanupForm);
	$Panel->AddListItem($Context->GetDefinition("AdministrativeOptions"), $Context->GetDefinition("SystemCleanup"), "settings.php?PostBackAction=Cleanup");
}
