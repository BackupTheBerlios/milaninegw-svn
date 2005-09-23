<?php
	/**************************************************************************\
	* eGroupWare - home                                                        *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_home.inc.php,v 1.10.2.1 2004/11/06 12:15:56 ralfbecker Exp $ */

	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo "Failed attempt to break in via an old Security Hole!<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	//if ($GLOBALS['phpgw_info']['user']['preferences']['todo']['mainscreen_showevents'] == True)
	//{

		$pro = CreateObject('projects.uiprojects');
		$extra_data = '<td>'."\n".$pro->list_projects_home().'</td>'."\n";

		$portalbox = CreateObject('phpgwapi.listbox',
			Array(
				'title'     => '<font color="#FFFFFF">'.lang('projects').'</font>',
				'primary'   => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'tertiary'  => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'width'     => '100%',
				'outerborderwidth' => '0',
				'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi/templates/default','bg_filler')
			)
		);
		$app_id = $GLOBALS['phpgw']->applications->name2id('projects');
		$GLOBALS['portal_order'][] = $app_id;
		$var = Array(
			'up'       => Array('url' => '/set_box.php', 'app' => $app_id),
			'down'     => Array('url' => '/set_box.php', 'app' => $app_id),
			'close'    => Array('url' => '/set_box.php', 'app' => $app_id),
			'question' => Array('url' => '/set_box.php', 'app' => $app_id),
			'edit'     => Array('url' => '/set_box.php', 'app' => $app_id)
		);

		while(list($key,$value) = each($var))
		{
			$portalbox->set_controls($key,$value);
		}

		$portalbox->data = array();

		echo "\n".'<!-- projects info -->'."\n".$portalbox->draw($extra_data).'<!-- projects info -->'."\n";
	//}
?>
