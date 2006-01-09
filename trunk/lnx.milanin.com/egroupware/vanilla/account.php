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
* Description: Display and manipulate user account information
*/

include("appg/settings.php");
include(sgLIBRARY."Input.Select.class.php");
include(sgLIBRARY."Input.Radio.class.php");
include(sgLIBRARY."Input.Validator.class.php");
include(sgLIBRARY."Utility.Email.class.php");
include(sgLIBRARY."Vanilla.Role.class.php");
include(sgLIBRARY."Vanilla.Comment.class.php");
include("appg/init_internal.php");

// 1. DEFINE VARIABLES AND PROPERTIES SPECIFIC TO THIS PAGE

	// Ensure the user is allowed to view this page
	$Context->Session->Check(agSAFE_REDIRECT);
	
	$UserManager = $Context->ObjectFactory->NewContextObject($Context, "UserManager");
	$AccountUserID = ForceIncomingInt("u", $Context->Session->UserID);
	$AccountUser = $UserManager->GetUserById($AccountUserID);
	if ($Context->Session->User && $Context->Session->User->AdminUsers) {
		// Allow anything
	} else {
		if ($AccountUser && $AccountUser->RoleID == 1) {
			$Context->WarningCollector->Add($Context->GetDefinition("ErrUserNotFound"));
			$AccountUser = false;
		}
	}
	
	// If a user id was not supplied, assume that this user doesn't have an active account and kick them back to the index
	if ($AccountUserID == 0) {
		header("location: index.php");
		die();
	}
	
	// Define properties of the page controls that are specific to this page
	$Menu->CurrentTab = "account";
	$Panel->CssClass = "AccountPanel";
	$Body->CssClass = "AccountPageBody";
	if ($AccountUser->UserID == $Context->Session->UserID) {
		$Context->PageTitle = $Context->GetDefinition("MyAccount");
	} else {
		$Context->PageTitle = $AccountUser->Name;
	}

// 2. BUILD PAGE CONTROLS

	// Build the control panel
	AddAccountOptionsToPanel($Context, $Panel, $AccountUser);
	AddTextModeToPanel($Context, $Panel);
	AddGuestInfoToPanel($Context, $Panel);

	// Create the account profile
	$Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "Account", $AccountUser));
	$Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "RoleHistory", $UserManager, $AccountUserID));
	$Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "Discovery", $AccountUser));
	$Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "IpHistory", $UserManager, $AccountUserID));
	
	// Forms
	$Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "IdentityForm", $UserManager, $AccountUser));
	$Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "PasswordForm", $UserManager, $AccountUserID));
	$Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "FunctionalityForm", $UserManager, $AccountUser));
	$Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "RoleForm", $UserManager, $AccountUser));

// 3. ADD CONTROLS TO THE PAGE

	$Page->AddControl("Head_Render", $Head);
	$Page->AddControl("Menu_Render", $Menu);
	$Page->AddControl("Panel_Render", $Panel);
	$Page->AddControl("Body_Render", $Body);

// 4. FIRE PAGE EVENTS

	$Page->FireEvents();

?>