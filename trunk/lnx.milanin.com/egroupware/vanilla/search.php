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
* Description: Web forms that handle running, saving, and removing searches
*/

include("appg/settings.php");
include(sgLIBRARY."Vanilla.Discussion.class.php");
include_once(sgLIBRARY."Vanilla.Comment.class.php");
include(sgLIBRARY."Vanilla.Search.class.php");
include(sgLIBRARY."Vanilla.Role.class.php");
include(sgLIBRARY."Utility.Pagelist.class.php");
include(sgLIBRARY."Input.Select.class.php");
include(sgLIBRARY."Input.Radio.class.php");
include("appg/init_internal.php");
include(sgLIBRARY."Utility.SqlSearch.class.php");

// 1. DEFINE VARIABLES AND PROPERTIES SPECIFIC TO THIS PAGE

	// Ensure the user is allowed to view this page
	$Context->Session->Check(agSAFE_REDIRECT);
	
	// Instantiate data managers to be used in this page
	$SearchManager = $Context->ObjectFactory->NewContextObject($Context, "SearchManager");
	
	// Define properties of the page controls that are specific to this page
	$Context->PageTitle = $Context->GetDefinition("Search");
	$Menu->CurrentTab = "search";
	$Panel->CssClass = "SearchPanel";
	$Body->CssClass = "Search";
	$Head->AddScript("./js/vanillasearch.js");

// 2. BUILD PAGE CONTROLS

	// Search form
	$SearchForm = $Context->ObjectFactory->NewContextObject($Context, "SearchForm");
	$SearchForm->LoadData($SearchManager);
	$Body->AddControl($SearchForm);
	
	// Control Panel
	AddSearchesToPanel($Context, $Panel, $SearchManager, agPANEL_SEARCH_COUNT, 1, "SavedSearchContainer");
   if ($SearchForm->PostBackAction == "Search" && $Context->Session->UserID > 0) {
      if ($SearchForm->Search->Type == "Topics") AddAppendixToPanel($Context, $Panel, "Discussion");
      if ($SearchForm->Search->Type == "Comments") AddAppendixToPanel($Context, $Panel, "Comment");
   }
	AddTextModeToPanel($Context, $Panel);
	AddGuestInfoToPanel($Context, $Panel);
	
// 3. ADD CONTROLS TO THE PAGE

	$Page->AddControl("Head_Render", $Head);
	$Page->AddControl("Menu_Render", $Menu);
	$Page->AddControl("Panel_Render", $Panel);
	$Page->AddControl("Body_Render", $Body);

// 4. FIRE PAGE EVENTS

	$Page->FireEvents();
	
?>