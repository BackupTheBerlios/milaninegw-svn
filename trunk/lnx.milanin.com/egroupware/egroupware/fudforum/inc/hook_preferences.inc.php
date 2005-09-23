<?php
	/**************************************************************************\
	* phpGroupWare - FUDforum preferences/profil                               *
	* http://www.eGroupWare.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_preferences.inc.php,v 1.4 2003/11/08 17:47:00 iliaa Exp $ */

	{
		$file = Array
		(
			'Preferences' => $GLOBALS['phpgw']->link('/fudforum/'.sprintf("%u", crc32($GLOBALS['phpgw_info']['user']['domain'])).'/index.php','t=register'),
		);

//Do not modify below this line
		display_section($appname,$file);
	}
?>
