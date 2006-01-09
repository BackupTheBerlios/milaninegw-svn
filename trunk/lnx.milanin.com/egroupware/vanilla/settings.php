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
* Description: Web forms that handle manipulating user & application settings
*/

include("appg/settings.php");
include(sgLIBRARY."Input.Select.class.php");
include(sgLIBRARY."Input.Radio.class.php");
include(sgLIBRARY."Input.Checkbox.class.php");
include(sgLIBRARY."Input.Validator.class.php");
include(sgLIBRARY."Utility.Constant.class.php");
include(sgLIBRARY."Vanilla.Role.class.php");
include(sgLIBRARY."Vanilla.Category.class.php");
include(sgLIBRARY."Utility.Email.class.php");
include("appg/init_internal.php");

// Ensure the user is allowed to view this page
if (!$Context->Session->User->AdminCategories
   && !$Context->Session->User->AdminUsers
   && !$Context->Session->User->MasterAdmin) header("location: account.php");

// 1. DEFINE VARIABLES AND PROPERTIES SPECIFIC TO THIS PAGE

   $Menu->CurrentTab = "settings";
   $Panel->CssClass = "SettingsPanel";
   $Body->CssClass = "SettingsPageBody";
   $Context->PageTitle = $Context->GetDefinition("AdministrativeSettings");

// 2. BUILD PAGE CONTROLS

   // Build the control panel
   AddSettingOptionsToPanel($Context, $Panel);
   
   // Create the default view
   $Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "SettingsHelp"));

   // Forms
   $Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "CategoryForm"));
   $Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "RoleForm"));
   $Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "GlobalsForm"));
   $Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "ExtensionForm"));
   $Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "RegistrationForm"));
   $Body->AddControl($Context->ObjectFactory->NewContextObject($Context, "LanguageForm"));

// 3. ADD CONTROLS TO THE PAGE

   $Page->AddControl("Head_Render", $Head);
   $Page->AddControl("Menu_Render", $Menu);
   $Page->AddControl("Panel_Render", $Panel);
   $Page->AddControl("Body_Render", $Body);

// 4. FIRE PAGE EVENTS

   $Page->FireEvents();
   
?>