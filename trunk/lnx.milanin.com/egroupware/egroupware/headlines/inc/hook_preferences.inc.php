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
  /* $Id: hook_preferences.inc.php,v 1.12 2004/01/27 18:35:52 reinerj Exp $ */
{
// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = Array(
		'Select Headlines to Display' => $GLOBALS['phpgw']->link('/headlines/preferences.php'),
		'Select layout' => $GLOBALS['phpgw']->link('/headlines/preferences_layout.php')
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
