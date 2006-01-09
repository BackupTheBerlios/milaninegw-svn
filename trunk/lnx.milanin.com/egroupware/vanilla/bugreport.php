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
* Description: Web form that handles user sign-ins
*/

include("appg/settings.php");
include("appg/init_external.php");
include(sgLIBRARY."Input.Validator.class.php");
include(sgLIBRARY."Utility.Email.class.php");

// Define properties of the page controls that are specific to this page
$Body->CssClass = "BugReportContainer";
$Context->PageTitle = $Context->GetDefinition("BugReport");
$BugForm = $Context->ObjectFactory->NewContextObject($Context, "BugForm");
$BugForm->LoadData();
$Body->AddControl($BugForm);
$Foot->CssClass = "BugReport";
	
// Add the body to the page
$Page->AddControl("Body_Render", $Body);
$Page->AddControl("Foot_Render", $Foot);

// 4. FIRE PAGE EVENTS
$Page->FireEvents();
?>
