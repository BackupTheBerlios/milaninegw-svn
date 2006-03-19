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
* Description: Constants and objects specific to forum pages.
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
include(sgLIBRARY."Utility.Notify.class.php");
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
$Menu = $Context->ObjectFactory->NewContextObject($Context, "Menu");
$Panel = $Context->ObjectFactory->NewContextObject($Context, "Panel");
$Body = $Context->ObjectFactory->NewContextObject($Context, "Body");
$Foot = $Context->ObjectFactory->NewContextObject($Context, "Foot");
$PageEnd = $Context->ObjectFactory->NewContextObject($Context, "PageEnd");

// BUILD THE PAGE HEAD
// Every page will require some basic definitions for the header.
$Head->AddScript("./js/global.js");
$Head->AddScript("./js/vanilla.js");
$Head->AddScript("./js/data.js");
$Head->AddScript("./js/protect.js");
$Head->AddScript("./js/autocomplete.js");
$Head->AddScript("./js/sort.js");
$Head->AddScript("/egroupware/jscripts/tiny_mce/tiny_mce.js");
$Head->AddStyleSheet($Context->StyleUrl."global.css", "screen");
$Head->AddStyleSheet("/egroupware/sitemgr/sitemgr-site/templates/rhuk_orange_smoothie/css/template_css.css", "screen");
$Head->AddStyleSheet($Context->StyleUrl."global.handheld.css", "handheld");
$Head->AddString("<link rel=\"alternate\" type=\"application/atom+xml\" href=\"".PrependString("http://", AppendFolder(agDOMAIN, "feeds/?Type=atom"))."\" title=\"".$Context->GetDefinition("Atom")." ".$Context->GetDefinition("Feed")."\" />");
$Head->AddString('<script language="javascript" type="text/javascript">
                  tinyMCE.init({
				theme : "advanced",
                                mode: "exact",
                                elements: "CommentBox",
				plugins : "table",
				theme_advanced_buttons1_add : "forecolor,backcolor",
				theme_advanced_buttons3_add_before : "tablecontrols,separator",
				theme_advanced_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1", // Theme specific setting CSS classes
				debug : false
			});
		</script>'
);
// BUILD THE MAIN MENU
$Menu->AddTab($Context->GetDefinition("Discussions"), "discussions", "./", "DiscussionsTab");
if (agUSE_CATEGORIES) $Menu->AddTab($Context->GetDefinition("Categories"), "categories", "categories.php", "CategoriesTab");
$Menu->AddTab($Context->GetDefinition("Search"), "search", "search.php", "SearchTab");
if ($Context->Session->UserID > 0) {
	if ($Context->Session->User->AdminCategories
	|| $Context->Session->User->AdminUsers
	|| $Context->Session->User->MasterAdmin) $Menu->AddTab($Context->GetDefinition("Settings"), "settings", "settings.php", "SettingsTab");
	$Menu->AddTab($Context->GetDefinition("Account"), "account", "account.php", "AccountTab");
}

// INCLUDE EXTENSIONS
include(agAPPLICATION_PATH."appg/extensions.php");

// Add the end of the page
$Page->AddControl("Foot_Render", $Foot);
$Page->AddControl("Page_Unload", $PageEnd);

// Include the control file for this page
$Page->Import($Page->ControlFile);
?>
