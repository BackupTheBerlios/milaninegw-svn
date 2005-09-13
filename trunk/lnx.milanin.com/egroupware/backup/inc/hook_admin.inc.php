<?php
  /**************************************************************************\
  * eGroupWare - Administration                                            *
  * http://www.egroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id: hook_admin.inc.php,v 1.6 2004/01/25 18:10:35 reinerj Exp $ */

	{
// Only Modify the $file and $title variables.....
		$title = $appname;
		$file = Array
		(
			'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
			'Backup Administration' => $GLOBALS['phpgw']->link('/index.php','menuaction=backup.uibackup.backup_admin')
		);

//Do not modify below this line
		display_section($appname,$title,$file);
	}
?>
