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
include("appg/init_internal.php");

// 1. DEFINE VARIABLES AND PROPERTIES SPECIFIC TO THIS PAGE

	// Ensure the user is allowed to view this page
	$Context->Session->Check(agSAFE_REDIRECT);
	if (!agUSE_CATEGORIES) header("location: index.php");
	
	// Define properties of the page controls that are specific to this page
	$Menu->CurrentTab = "categories";
	$Panel->CssClass = "CategoryPanel";
	$Body->CssClass = "Categories";
	$Context->PageTitle = $Context->GetDefinition("Categories");

// 2. BUILD PAGE CONTROLS

	// Build the control panel
	AddDiscussionOptionsToPanel($Context, $Panel);
   AddAppendixToPanel($Context, $Panel, "Category");
	AddTextModeToPanel($Context, $Panel);
	AddGuestInfoToPanel($Context, $Panel);

	// Add the category list to the body
	$CategoryList = $Context->ObjectFactory->NewContextObject($Context, "CategoryList");
	$Body->AddControl($CategoryList);

// 3. ADD CONTROLS TO THE PAGE

	$Page->AddControl("Head_Render", $Head);
	$Page->AddControl("Menu_Render", $Menu);
	$Page->AddControl("Panel_Render", $Panel);
	$Page->AddControl("Body_Render", $Body);

// 4. FIRE PAGE EVENTS

	$Page->FireEvents();
	
?>