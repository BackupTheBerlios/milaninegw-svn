<?php
  /**************************************************************************\
  * eGroupWare - Calendar                                                    *
  * http://www.egroupware.org                                                *
  * Based on Webcalendar by Craig Knudsen <cknudsen@radix.net>               *
  *          http://www.radix.net/~cknudsen                                  *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_home.inc.php,v 1.47 2004/01/27 00:29:26 reinerj Exp $ */

	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo 'Failed attempt to break in via an old Security Hole!<br>'."\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	$showevents = (int)$GLOBALS['phpgw_info']['user']['preferences']['calendar']['mainscreen_showevents'];
	if($showevents>0)
	{
		$GLOBALS['phpgw']->translation->add_app('calendar');
		if(!is_object($GLOBALS['phpgw']->datetime))
		{
			$GLOBALS['phpgw']->datetime = CreateObject('phpgwapi.datetime');
		}

		$GLOBALS['date'] = date('Ymd',$GLOBALS['phpgw']->datetime->users_localtime);
		$GLOBALS['g_year'] = substr($GLOBALS['date'],0,4);
		$GLOBALS['g_month'] = substr($GLOBALS['date'],4,2);
		$GLOBALS['g_day'] = substr($GLOBALS['date'],6,2);
		$GLOBALS['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
		$GLOBALS['css'] = "\n".'<style type="text/css">'."\n".'<!--'."\n"
			. ExecMethod('calendar.uicalendar.css').'-->'."\n".'</style>';

		if($showevents==2)
		{
			$_page = "small";
		}
		else
		{
			$page_ = explode('.',$GLOBALS['phpgw_info']['user']['preferences']['calendar']['defaultcalendar']);
			$_page = substr($page_[0],0,7);	// makes planner from planner_{user|category}
			if ($_page=='index' || ($_page != 'day' && $_page != 'week' && $_page != 'month' && $_page != 'year' && $_page != 'planner'))
			{
				$_page = 'month';
//			$GLOBALS['phpgw']->preferences->add('calendar','defaultcalendar','month');
//			$GLOBALS['phpgw']->preferences->save_repository();
			}
		}

		if(!@file_exists(PHPGW_INCLUDE_ROOT.'/calendar/inc/hook_home_'.$_page.'.inc.php'))
		{
			$_page = 'day';
		}
		include(PHPGW_INCLUDE_ROOT.'/calendar/inc/hook_home_'.$_page.'.inc.php');

		$title = lang('Calendar');

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

		$app_id = $GLOBALS['phpgw']->applications->name2id('calendar');
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

		$portalbox->data = Array();

		echo "\n".'<!-- BEGIN Calendar info -->'."\n".$portalbox->draw($GLOBALS['extra_data'])."\n".'<!-- END Calendar info -->'."\n";
		unset($cal);
	}
	flush();
	unset($showevents);
?>
