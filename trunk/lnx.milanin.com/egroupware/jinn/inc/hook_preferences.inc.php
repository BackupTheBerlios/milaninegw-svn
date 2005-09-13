<?php
	/**************************************************************************\
	* eGroupWare - JiNN Preferences                                            *
	* http://www.egroupware.org                                                *
	* Written by Pim Snel <pim@egroupware.org>                                 *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; version 2 of the License.                     *
	\**************************************************************************/

	/* $Id: hook_preferences.inc.php,v 1.1 2004/01/29 01:00:29 mipmip Exp $ */
	{
		$title = $appname;
		$file = Array(
			'Preferences' => $GLOBALS['phpgw']->link('/preferences/preferences.php','appname='.$appname)
		);
		display_section($appname,$title,$file);
	}
