<?php
    /**************************************************************************\
    * eGroupWare - Knowledge Base                                              *
    * http://www.egroupware.org                                                *
    * -----------------------------------------------                          *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

	/* $Id: hook_preferences.inc.php,v 1.1 2004/05/26 07:32:30 ralfbecker Exp $ */
{

	$file = Array(
		'Preferences'		=> $GLOBALS['phpgw']->link('/preferences/preferences.php','appname=phpbrain'),
		'Edit Categories'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app='.$appname.'&cats_level=True&global_cats=True')
	);
	display_section($appname,$file);
}
