<?php
	/**************************************************************************\
	* eGroupWare - Wiki Sidebox-Menu for idots-template                        *
	* http://www.egroupware.org                                                *
	* Written by Pim Snel <pim@lingewoud.nl>                                   *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_admin.inc.php,v 1.3 2004/04/12 13:02:06 ralfbecker Exp $ */

	// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = Array(
		'Site Configuration' => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
//		'Lock / Unlock Pages' => $GLOBALS['phpgw']->link('/wiki/index.php','action=admin&locking=1'),
		'Block / Unblock hosts' => $GLOBALS['phpgw']->link('/wiki/index.php','action=admin&blocking=1'),
	);
	//Do not modify below this line
	display_section($appname,$title,$file);
?>
