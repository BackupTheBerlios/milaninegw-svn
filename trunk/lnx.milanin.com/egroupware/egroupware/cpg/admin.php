<?php
// ------------------------------------------------------------------------- //
// Coppermine Photo Gallery 1.3.2                                            //
// ------------------------------------------------------------------------- //
// Copyright (C) 2002-2004 Gregory DEMAR                                     //
// http://www.chezgreg.net/coppermine/                                       //
// ------------------------------------------------------------------------- //
// Updated by the Coppermine Dev Team                                        //
// (http://coppermine.sf.net/team/)                                          //
// see /docs/credits.html for details                                        //
// ------------------------------------------------------------------------- //
// This program is free software; you can redistribute it and/or modify      //
// it under the terms of the GNU General Public License as published by      //
// the Free Software Foundation; either version 2 of the License, or         //
// (at your option) any later version.                                       //
// ------------------------------------------------------------------------- //
// CVS version: $Id: admin.php,v 1.4 2004/08/24 20:49:03 joestewart Exp $
// ------------------------------------------------------------------------- //

define('IN_COPPERMINE', true);
define('ADMIN_PHP', true);

require('include/init.inc.php');

if (!USER_CAN_CREATE_ALBUMS && !USER_IS_ADMIN) cpg_die(ERROR, $lang_errors['access_denied'], __FILE__, __LINE__);

if (!isset($_GET['admin_mode']) || !isset($_GET['referer'])) cpg_die(CRITICAL_ERROR, $lang_errors['param_missing'], __FILE__, __LINE__);

$admin_mode = (int)$_GET['admin_mode'] ? 1 : 0;
$referer = $_GET['referer'] ? $_GET['referer'] : 'index.php';
$USER['am'] = $admin_mode;
if(!$GLOBALS['phpgw']->session->appsession('session_data','cpgUSER')) {
   $GLOBALS['phpgw']->session->appsession('session_data','cpgUSER',$USER);
}

if (!$admin_mode) $referer = 'index.php';

pageheader($lang_info, "<META http-equiv=\"refresh\" content=\"1;url=$referer\">");
msg_box($lang_info, $lang_admin_php[$admin_mode], $lang_continue, $referer);
pagefooter();
ob_end_flush();

?>
