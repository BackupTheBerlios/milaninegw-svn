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

	/* $Id: hook_preferences.inc.php,v 1.12 2004/01/27 19:26:33 reinerj Exp $ */

{
// Only Modify the $file and $title variables.....
	$title = 'Stock Quotes';
	$file = Array(
		'Select displayed stocks' => $GLOBALS['phpgw']->link('/stocks/preferences.php')
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
