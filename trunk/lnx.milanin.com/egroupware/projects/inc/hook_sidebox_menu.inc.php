<?php
	/**************************************************************************\
	* eGroupWare - projects's Sidebox-Menu for idots-template                  *
	* http://www.egroupware.org                                                *
	* Written by Pim Snel <pim@lingewoud.nl>                                   *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_sidebox_menu.inc.php,v 1.6.2.1 2004/11/06 12:15:56 ralfbecker Exp $ */
	{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

			$boprojects = CreateObject('projects.boprojects');
			$appname = 'projects';
			$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');

			$file = array(
				'Projects' => 
				$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&action=mains'),
				
				'Jobs' => 
				$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&action=subs'),
				
				'Work hours' => 
				$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_projects&action=mains'),
				
				'time tracker' => 
				$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.ttracker'),
				
				'Statistics' => 
				$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uistatistics.list_projects&action=mains'),
			);

			if ($boprojects->isprojectadmin('pad') || $boprojects->isprojectadmin('pmanager'))
			{
				$file['Budget'] = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_budget&action=mains');
				switch($boprojects->siteconfig['accounting'])
				{
					case 'activity':
						$file['Activities'] = 
							$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_activities&action=act');
						break;
					default:
						$file['Accounting'] = 
							$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_employees&action=accounting');
				}
			}

			display_sidebox($appname,$menu_title,$file);

			if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
			{
				$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Preferences');
				$pref_file['Preferences'] = 
					$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.preferences');
				$pref_file['Grant Access'] =
					$GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uiaclprefs.index&acl_app='.$appname);
				$pref_file['Edit categories'] = 
					$GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app=projects&cats_level=True&global_cats=True');
				display_sidebox($appname,$menu_title,$pref_file);
			}

			if ($GLOBALS['phpgw_info']['user']['apps']['admin'] || $boprojects->isprojectadmin('pad') || $boprojects->isprojectadmin('pmanager'))
			{
				$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Administration');

				if ($GLOBALS['phpgw_info']['user']['apps']['admin'] )
				{
					$admin_file['Site Configuration'] =
						$GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname);
					/* $admin_file['managing committee'] =
						$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_admins&action=pmanager'); */
					$admin_file['project administrators'] =
						$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_admins&action=pad');
					/* $admin_file['sales department'] =
						$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_admins&action=psale'); */
					$admin_file['Global Categories'] =
						$GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicategories.index&appname=' . $appname);
				}
				if ($boprojects->isprojectadmin('pad') || $boprojects->isprojectadmin('pmanager'))
				{
					$admin_file['Roles'] =
						$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_roles&action=role');
					$admin_file['costs'] =
						$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_roles&action=role&role_type=cost');
					$admin_file['events'] =
						$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_events');
				}
				display_sidebox($appname,$menu_title,$admin_file);
			}
			unset($boprojects);
	}
?>
