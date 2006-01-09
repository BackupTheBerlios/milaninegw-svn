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
* Description: Display, add, and manipulate discussion comments
*/

include("appg/settings.php");
include(sgLIBRARY."Input.Select.class.php");
include(sgLIBRARY."Input.Radio.class.php");
include(sgLIBRARY."Utility.Pagelist.class.php");
include(sgLIBRARY."Vanilla.Discussion.class.php");
include(sgLIBRARY."Vanilla.Comment.class.php");
include(sgLIBRARY."Vanilla.Category.class.php");
include(sgLIBRARY."Vanilla.Search.class.php");
include("appg/init_internal.php");

// 1. DEFINE VARIABLES AND PROPERTIES SPECIFIC TO THIS PAGE

// Ensure the user is allowed to view this page
$Context->Session->Check(agSAFE_REDIRECT);

// Instantiate data managers to be used in this page
$DiscussionManager = $Context->ObjectFactory->NewContextObject($Context, "DiscussionManager");

// Create the comment grid
$DiscussionID = ForceIncomingInt("DiscussionID", 0);
$CommentGrid = $Context->ObjectFactory->NewContextObject($Context, "CommentGrid", $DiscussionManager, $DiscussionID);

// Create the comment form
if ($CommentGrid->ShowForm) {
	$CommentForm = $Context->ObjectFactory->NewContextObject($Context, "DiscussionForm");
	$CommentForm->LoadData();
}

// Define properties of the page controls that are specific to this page
$Menu->CurrentTab = "discussions";
$Panel->CssClass = "CommentPanel";
$Body->CssClass = "Comments";
$Context->PageTitle = $CommentGrid->Discussion->Name;

// 2. BUILD PAGE CONTROLS

// Build the control panel
	AddDiscussionOptionsToPanel($Context, $Panel);
	AddBookmarksToPanel($Context, $Panel, $DiscussionManager, $DiscussionID);
	AddDiscussionsToPanel($Context, $Panel, $DiscussionManager, "GetDiscussionsByUserID", agPANEL_USERDISCUSSIONS_COUNT, $Context->GetDefinition("YourDiscussions"), "Recent", $Context->Session->User->Setting("ShowRecentDiscussions"));
	AddDiscussionsToPanel($Context, $Panel, $DiscussionManager, "GetViewedDiscussionsByUserID", agPANEL_HISTORY_COUNT, $Context->GetDefinition("History"), "History", $Context->Session->User->Setting("ShowBrowsingHistory"));
	if ($Context->Session->UserID > 0) {
		$SearchManager = $Context->ObjectFactory->NewContextObject($Context, "SearchManager");
		AddSearchesToPanel($Context, $Panel, $SearchManager, agPANEL_SEARCH_COUNT);
	}

	AddAppendixToPanel($Context, $Panel, "Comments");
	AddTextModeToPanel($Context, $Panel);
	AddGuestInfoToPanel($Context, $Panel);

	// Add the comment form to the comment grid
	if ($CommentGrid->ShowForm) $CommentGrid->AddControl($CommentForm);
	
	
	// Add discussion options to the panel
   AddDiscussionOptions($Context, $Panel, $CommentGrid->Discussion);
	
	// Add the comment grid to the body
	$Body->AddControl($CommentGrid);


// 3. ADD CONTROLS TO THE PAGE

	$Page->AddControl("Head_Render", $Head);
	$Page->AddControl("Menu_Render", $Menu);
	$Page->AddControl("Panel_Render", $Panel);
	$Page->AddControl("Body_Render", $Body);

// 4. FIRE PAGE EVENTS
$Page->FireEvents();
?>