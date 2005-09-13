<?php
	/**************************************************************************\
	* eGroupWare - Stock Quotes                                                *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: hook_home.inc.php,v 1.23 2004/04/29 16:48:01 reinerj Exp $ */

	$d1 = strtolower(substr($GLOBALS['phpgw_info']['server']['app_inc'],0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo 'Failed attempt to break in via an old Security Hole!<br>'."\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	$tmp_app_inc = $GLOBALS['phpgw']->common->get_inc_dir('stocks');

	if ($GLOBALS['phpgw_info']['user']['apps']['stocks'] && $GLOBALS['phpgw_info']['user']['preferences']['stocks']['enabled'])
	{
		$title = lang('Stocks');
		
		$portalbox = CreateObject('phpgwapi.listbox',
			Array(
				'title'	=> $title,
				'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'width'	=> '100%',
				'outerborderwidth'	=> '0',
				'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi/templates/default','bg_filler')
			)
		);

		$app_id = $GLOBALS['phpgw']->applications->name2id('stocks');
		$GLOBALS['portal_order'][] = $app_id;
		$var = Array(
			'up'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
			'down'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
			'close'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
			'question'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
			'edit'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id)
		);

		while(list($key,$value) = each($var))
		{
			$portalbox->set_controls($key,$value);
		}

		include($tmp_app_inc . '/functions.inc.php');
		$portalbox->data = Array();

		echo "\n".'<!-- BEGIN Stock Quotes info -->'."\n".$portalbox->draw('<td>'."\n".return_quotes()."\n".'</td>')."\n".'<!-- END Stock Quotes info -->'."\n";
	}
?>
