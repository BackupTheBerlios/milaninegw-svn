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
* Description: Global application constants
*/

// Database Settings
define("dbHOST", "localhost"); 
define("dbNAME", "egroupware_trunk"); 
define("dbUSER", "egw"); 
define("dbPASSWORD", ""); 

// Path Settings
define("agAPPLICATION_PATH", "/var/www/egroupware/vanilla/"); 
define("agEGW_APPLICATION_PATH", agAPPLICATION_PATH."../egroupware"); 
define("sgLIBRARY", agAPPLICATION_PATH."library/");
define("agEXTENSIONS", agAPPLICATION_PATH."extensions/");
define("agLANGUAGES", agAPPLICATION_PATH."languages/");
define("agICONSPREFIX","/members/_icons/data/");

// Application Settings
define("agAPPLICATION_TITLE", "B.C. Milan IN forum"); 
define("agBANNER_TITLE", "B.C. Milan IN forum"); 
define("agDEFAULT_STYLE", "styles/milanin/"); 
define("agDOMAIN", "egroupware/vanilla"); 
define("agSAFE_REDIRECT", "/egroupware/login.php?cd=10"); 
define("agDISCUSSIONS_PER_PAGE", "30"); 
define("agDISCUSSIONS_PER_FEED", "20"); 
define("agCOMMENTS_PER_PAGE", "30"); 
define("agSEARCH_RESULTS_PER_PAGE", "30"); 
define("agCOOKIE_DOMAIN", "egroupware"); 
define("agSUPPORT_EMAIL", "webmaster@milanin.com"); 
define("agSUPPORT_NAME", "Webmaster"); 
define("agALLOW_NAME_CHANGE", "0"); 
define("agPUBLIC_BROWSING", "0"); 
define("agUSE_CATEGORIES", "1"); 
define("agLOG_ALL_IPS", "1"); 

// Panel Settings
define("agPANEL_BOOKMARK_COUNT", "20"); 
define("agPANEL_PRIVATE_COUNT", "5"); 
define("agPANEL_HISTORY_COUNT", "5"); 
define("agPANEL_USERDISCUSSIONS_COUNT", "5"); 
define("agPANEL_SEARCH_COUNT", "20"); 

// Discussion Settings
define("agMAX_COMMENT_LENGTH", "5000"); 
define("agMAX_TOPIC_WORD_LENGTH", "45"); 
define("agDISCUSSION_POST_THRESHOLD", "3"); 
define("agDISCUSSION_TIME_THRESHOLD", "60"); 
define("agDISCUSSION_THRESHOLD_PUNISHMENT", "120"); 
define("agCOMMENT_POST_THRESHOLD", "5"); 
define("agCOMMENT_TIME_THRESHOLD", "60"); 
define("agCOMMENT_THRESHOLD_PUNISHMENT", "120"); 
define("agTEXT_WHISPERED", "Private"); 
define("agTEXT_STICKY", "Sticky"); 
define("agTEXT_CLOSED", "Locked"); 
define("agTEXT_HIDDEN", "Hidden"); 
define("agTEXT_BOOKMARKED", ""); 
define("agTEXT_PREFIX", "["); 
define("agTEXT_SUFFIX", "]"); 

// String Formatting Settings
define("agDEFAULTSTRINGFORMAT", "Html"); 
define("agFORMATSTRINGFORDISPLAY", "DISPLAY"); 
define("agFORMATSTRINGFORDATABASE", "DATABASE"); 

// Application Mode Constants
define("agMODE_DEBUG", "DEBUG"); 
define("agMODE_RELEASE", "RELEASE"); 
define("agMODE_UPGRADE", "UPGRADE"); 

// Registration settings
define("agDEFAULT_ROLE", "3"); 
define("agALLOW_IMMEDIATE_ACCESS", "1"); 
define("agAPPROVAL_ROLE", "3"); 

// Application version - Don't change this value or you may have
// problems upgrading later.
define("agVANILLA_VERSION", "0.9.2.6-milaninegw"); 
// Note: Vanilla 0.9.2.6 included some extremely important security patches.
// If you do not have this version installed, we highly recommend that you replace your old vanilla
// files with this version. It is okay to leave all files in the appg folder as they are.
?>
