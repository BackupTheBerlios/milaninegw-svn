<?php
	/**************************************************************************\
	* eGroupWare - Stock Quotes                                                *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/

	/* $Id: hook_sidebox_menu.inc.php,v 1.2.2.1 2004/09/22 17:26:32 alpeb Exp $ */

{
// Only Modify the $file and $title variables.....
	$title = lang('Stock Quotes');
	$file = Array(
		'Preferences' => $GLOBALS['phpgw']->link('/stocks/preferences.php')
	);
	display_sidebox($appname,$title,$file);
}
?>
