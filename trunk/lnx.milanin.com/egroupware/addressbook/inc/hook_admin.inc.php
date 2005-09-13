<?php
	/**************************************************************************\
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	// $Id: hook_admin.inc.php,v 1.16 2004/01/25 22:02:59 reinerj Exp $
	// $Source: /cvsroot/egroupware/addressbook/inc/hook_admin.inc.php,v $

	// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = Array(
		'Site Configuration' => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
		'Edit custom fields' => $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uifields.index'),
		'Global Categories' => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicategories.index&appname=addressbook')
	);
	//Do not modify below this line
	display_section($appname,$title,$file);
?>
