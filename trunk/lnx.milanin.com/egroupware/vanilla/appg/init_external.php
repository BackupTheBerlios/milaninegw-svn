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
* Description: Constants and objects specific to external to the forum (ie. sign in, apply, password retrieval).
*/

// GLOBAL INCLUDES
include(agAPPLICATION_PATH."appg/headers.php");
include(sgLIBRARY."Utility.Functions.php");
include(sgLIBRARY."Utility.Database.class.php");
include(sgLIBRARY."Utility.SqlBuilder.class.php");
include(sgLIBRARY."Utility.Parameters.class.php");
include(sgLIBRARY."Utility.MessageCollector.class.php");
include(sgLIBRARY."Utility.ErrorManager.class.php");
include(sgLIBRARY."Utility.ObjectFactory.class.php");
include(sgLIBRARY."Utility.StringManipulator.class.php");
include(sgLIBRARY."Utility.Context.class.php");
include(sgLIBRARY."Utility.Page.class.php");
include(sgLIBRARY."Utility.Writer.class.php");
include(sgLIBRARY."Utility.Control.class.php");
include(sgLIBRARY."Vanilla.Functions.php");
include(sgLIBRARY."Vanilla.Session.class.php");
include(sgLIBRARY."Vanilla.User.class.php");
include(agAPPLICATION_PATH."controls/Common.Controls.php");

// INSTANTIATE THE CONTEXT OBJECT
// The context object handles the following:
// - Open a connection to the database
// - Create a user session (autologging in any user with valid cookie credentials)
// - Instantiate debug and warning collectors
// - Instantiate an error manager
// - Define global variables relative to the current context (SelfUrl
$Context = new Context();

// DEFINE THE LANGUAGE DICTIONARY
include(agAPPLICATION_PATH."appg/language.php");

// INSTANTIATE THE PAGE OBJECT
// The page object handles collecting all page controls
// and writing them when it's events are fired.
$Page = new Page($Context);

// FIRE INITIALIZATION EVENT
$Page->FireEvent("Page_Init");

// DEFINE THE MASTER PAGE CONTROLS
$Head = $Context->ObjectFactory->NewContextObject($Context, "Head");
$Body = $Context->ObjectFactory->NewContextObject($Context, "ExternalBody");
$Foot = $Context->ObjectFactory->NewContextObject($Context, "ExternalFoot");
$PageEnd = $Context->ObjectFactory->NewContextObject($Context, "PageEnd");

// INCLUDE EXTENSIONS
include(agAPPLICATION_PATH."appg/extensions.php");

// BUILD THE PAGE HEAD
// Every page will require some basic definitions for the header.
$Head->AddScript("./js/global.js");
$Head->AddScript("./js/vanilla.js");
$Head->AddScript("/egroupware/jscripts/tiny_mce/tiny_mce.js");
$Head->AddStyleSheet($Context->StyleUrl."signin.css", "screen");
$Head->AddStyleSheet($Context->StyleUrl."signin.handheld.css", "handheld");

// Add the end of the page
$Page->AddControl("Head_Render", $Head);
$Page->AddControl("Page_Unload", $PageEnd);

// Include the control file for this page
$Page->Import($Page->ControlFile);
?>