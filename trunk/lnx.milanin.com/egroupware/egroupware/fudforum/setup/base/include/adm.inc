<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: adm.inc,v 1.1.1.1 2003/10/17 21:12:41 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (!defined('shell_script')) {
	if ($GLOBALS['FUD_OPT_2'] & 32768) {
		$GLOBALS['FUD_OPT_2'] ^= 32768;
		$t = 1;
	}
	define('fud_theme', 'theme/default/');
	fud_use('db.inc');
	fud_use('cookies.inc');
	fud_use('users.inc');
	if (isset($t)) {
		$GLOBALS['FUD_OPT_2'] |= 32768;
	}
}

define('__fud_index_name__', 'index.php');

if (!($GLOBALS['usr']->users_opt & 1048576)) {
        exit;
}
if (isset($_POST['btn_cancel'])) {
	if (defined('back_to_main')) {
		header('Location: admglobal.php?'._rsidl);
		exit;
	}
	$_POST = $_GET = null;
}

function errorify($err)
{
	return '<font color="red">'.$err.'</font><br />';
}
?>