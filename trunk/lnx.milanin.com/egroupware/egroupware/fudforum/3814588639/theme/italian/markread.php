<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: markread.php.t,v 1.1.1.1 2003/10/17 21:11:25 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function check_return($returnto)
{
	if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: /egroupware/fudforum/3814588639/index.php?t=index&'._rsidl);
	} else {
		if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto.'&S='.s);
		} else {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto);
		}
	}
	exit;
}


	if (_uid) {
		if (!isset($_GET['id'])) {
			user_mark_all_read(_uid);
		} else if ((int)$_GET['id']) {
			user_mark_forum_read(_uid, (int)$_GET['id'], $usr->last_read);
		}
	}

	check_return($usr->returnto);
	exit();
?>