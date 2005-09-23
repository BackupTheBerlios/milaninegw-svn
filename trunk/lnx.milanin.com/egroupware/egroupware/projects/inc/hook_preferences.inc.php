<?php
	/**************************************************************************\
	* eGroupWare - Project Prefs                                               *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: hook_preferences.inc.php,v 1.27.2.1 2004/11/06 12:15:56 ralfbecker Exp $ */

	{
		$title = $appname;
		$file = Array
		(
			'Preferences'     => $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.preferences'),
			'Grant Access'    => $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uiaclprefs.index&acl_app='.$appname),
			'Edit categories' => $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app=projects&cats_level=True&global_cats=True')
		);

		$pro_soconfig = CreateObject('projects.soconfig');
		if($pro_soconfig->isprojectadmin('pad') || $pro_soconfig->isprojectadmin('pmanager'))
		{
		$afile = Array
		(
			'roles'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_roles&action=role'),
			'events'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_events')
		);
		unset($pro_soconfig);
		}

		if(is_array($afile))
		{
			$file += $afile;
		}

		display_section($appname,$title,$file);
	}
?>
