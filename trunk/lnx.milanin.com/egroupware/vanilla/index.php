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
* Description: Display and manipulate discussions
*/

include("appg/settings.php");
include_once(sgLIBRARY."Vanilla.Discussion.class.php");
include_once(sgLIBRARY."Vanilla.Comment.class.php");
include_once(sgLIBRARY."Vanilla.Search.class.php");
include_once(sgLIBRARY."Utility.Pagelist.class.php");
include_once(sgLIBRARY."Input.Validator.class.php");
include("appg/init_internal.php");

// 1. DEFINE VARIABLES AND PROPERTIES SPECIFIC TO THIS PAGE

	// Ensure the user is allowed to view this page
	$Context->Session->Check(agSAFE_REDIRECT);
	
	// Instantiate data managers to be used in this page
	$DiscussionManager = $Context->ObjectFactory->NewContextObject($Context, "DiscussionManager");
	$SearchManager = $Context->ObjectFactory->NewContextObject($Context, "SearchManager");
	
	// Define properties of the page controls that are specific to this page
	$Menu->CurrentTab = "discussions";
	$Panel->CssClass = "DiscussionPanel";
	$Body->CssClass = "Discussions";

// 2. BUILD PAGE CONTROLS

	// Panel
	AddDiscussionOptionsToPanel($Context, $Panel);
	AddBookmarksToPanel($Context, $Panel, $DiscussionManager);
	AddDiscussionsToPanel($Context, $Panel, $DiscussionManager, "GetDiscussionsByUserID", agPANEL_USERDISCUSSIONS_COUNT, $Context->GetDefinition("YourDiscussions"), "Recent", $Context->Session->User->Setting("ShowRecentDiscussions"));
	AddDiscussionsToPanel($Context, $Panel, $DiscussionManager, "GetViewedDiscussionsByUserID", agPANEL_HISTORY_COUNT, $Context->GetDefinition("History"), "History", $Context->Session->User->Setting("ShowBrowsingHistory"));
	AddSearchesToPanel($Context, $Panel, $SearchManager, agPANEL_SEARCH_COUNT);
	//AddAppendixToPanel($Context, $Panel, "Discussion");
	//AddTextModeToPanel($Context, $Panel);
	AddGuestInfoToPanel($Context, $Panel);
		
	// Add the discussion grid to the body
	$CategoryID = ForceIncomingInt("CategoryID", 0);
	$View = ForceIncomingString("View", "");
	$DiscussionGrid = $Context->ObjectFactory->NewContextObject($Context, "DiscussionGrid", $DiscussionManager, $CategoryID, $View);
	$Body->AddControl($DiscussionGrid);

// 3. ADD CONTROLS TO THE PAGE

	$Page->AddControl("Head_Render", $Head);
	$Page->AddControl("Menu_Render", $Menu);
	$Page->AddControl("Panel_Render", $Panel);
	$Page->AddControl("Body_Render", $Body);

// 4. FIRE PAGE EVENTS
	$Page->FireEvents();

?>
