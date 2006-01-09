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
* Description: Web form that handles creating & editing discussion topics & comments
*/

include("appg/settings.php");
include(sgLIBRARY."Input.Select.class.php");
include(sgLIBRARY."Input.Radio.class.php");
include(sgLIBRARY."Input.Validator.class.php");
include(sgLIBRARY."Utility.Pagelist.class.php");
include(sgLIBRARY."Vanilla.Category.class.php");
include(sgLIBRARY."Vanilla.Discussion.class.php");
include(sgLIBRARY."Vanilla.Comment.class.php");
include("appg/init_internal.php");

// 1. DEFINE VARIABLES AND PROPERTIES SPECIFIC TO THIS PAGE

	// Ensure the user is allowed to view this page
	$Context->Session->Check(agSAFE_REDIRECT);
	
	// Create the comment grid
	$Post = $Context->ObjectFactory->NewContextObject($Context, "Post");
	$Post->Title = $Context->GetDefinition("StartANewDiscussion");
	
	// Create the comment form
	$CommentForm = $Context->ObjectFactory->NewContextObject($Context, "DiscussionForm");
	$CommentForm->LoadData();
	// Only people with active sessions can post
	if ($Context->Session->UserID == 0) {
		$Context->WarningCollector->Add($this->Context->GetDefinition("ErrSignInToDiscuss"));
		$CommentForm->FatalError = 1;
	}
	
	// Define properties of the page controls that are specific to this page
	$Menu->CurrentTab = "discussions";
	$Panel->CssClass = "PostPanel";
	$Body->CssClass = "StartDiscussion";
	$Context->PageTitle = FormatStringForDisplay($CommentForm->Discussion->Name, 1);
	if ($Context->PageTitle == "") {
		$Context->PageTitle = $Context->GetDefinition("StartANewDiscussion");
	} else {
		if ($CommentForm->CommentID == 0) {
			$Post->Title = $Context->GetDefinition("AddYourComments");
		} elseif ($CommentForm->CommentID > 0) {
			$Post->Title = $Context->GetDefinition("EditComments");
		} else {
			$Post->Title = $Context->GetDefinition("EditDiscussion");
		}
	}

// 2. BUILD PAGE CONTROLS

	// Build the control panel
	$Panel->AddListItem($Context->GetDefinition("Options"), $Context->GetDefinition("BackToDiscussions"), "./");
	AddDiscussionOptionsToPanel($Context, $Panel);
	AddTextModeToPanel($Context, $Panel);

	// Add the comment form
	$Post->AddControl($CommentForm);
	
	// Add the form to the body
	$Body->AddControl($Post);

// 3. ADD CONTROLS TO THE PAGE

	$Page->AddControl("Head_Render", $Head);
	$Page->AddControl("Menu_Render", $Menu);
	$Page->AddControl("Panel_Render", $Panel);
	$Page->AddControl("Body_Render", $Body);

// 4. FIRE PAGE EVENTS

	$Page->FireEvents();

?>