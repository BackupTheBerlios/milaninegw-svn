<?php
  /**************************************************************************\
  * eGroupWare - PHPSysInfo                                                *
  * http://www.egroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_admin.inc.php,v 1.7 2004/01/20 20:16:51 reinerj Exp $ */

{ 
// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = Array(
		'view system information' => $GLOBALS['phpgw']->link('/phpsysinfo/index.php')
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
