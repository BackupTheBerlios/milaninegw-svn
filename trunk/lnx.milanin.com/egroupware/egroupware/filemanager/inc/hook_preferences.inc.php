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

  /* $Id: hook_preferences.inc.php,v 1.14 2004/02/02 04:18:17 milosch Exp $ */
{
// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = Array(
//		'Preferences Old' => $GLOBALS['phpgw']->link('/filemanager/preferences.php'),
		'Preferences' => $GLOBALS['phpgw']->link('/preferences/preferences.php','appname='.$appname)
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
