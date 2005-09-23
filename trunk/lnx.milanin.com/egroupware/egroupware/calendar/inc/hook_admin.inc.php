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

	/* $Id: hook_admin.inc.php,v 1.14 2004/01/27 00:29:26 reinerj Exp $ */
{
// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = Array(
		'Site Configuration' => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
		'Custom fields and sorting' => $GLOBALS['phpgw']->link('/index.php','menuaction=calendar.uicustom_fields.index'),
		'Calendar Holiday Management' => $GLOBALS['phpgw']->link('/index.php','menuaction=calendar.uiholiday.admin'),
		'Import CSV-File' => $GLOBALS['phpgw']->link('/calendar/csv_import.php'),
		'Global Categories' => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicategories.index&appname=calendar')
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
