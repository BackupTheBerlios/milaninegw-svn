<?php
    /**************************************************************************\
    * eGroupWare - Forum                                                       *
    * http://www.egroupware.org                                                *
    * -----------------------------------------------                          *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

	/* $Id: hook_sidebox_menu.inc.php,v 1.1.2.1 2004/09/10 15:44:37 alpeb Exp $ */
{
	if($GLOBALS['phpgw_info']['user']['apps']['preferences'])
	{
		$menu_title = lang('Preferences');
		$file = Array(
			'Preferences'		=> $GLOBALS['phpgw']->link('/preferences/preferences.php','appname=forum'),
		);
		display_sidebox($appname,$menu_title,$file);
	}	

	if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
        $menu_title = 'Administration';
        $file = Array(
			'Forum Administration' => $GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiadmin.index')
        );
		display_sidebox($appname,$menu_title,$file);
	}
}
?>
