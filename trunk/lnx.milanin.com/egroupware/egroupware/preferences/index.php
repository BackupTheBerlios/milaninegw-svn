<?php
	/**************************************************************************\
	* phpGroupWare - preferences                                               *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.33 2004/01/10 22:54:35 milosch Exp $ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'preferences',
		'disable_Template_class' => True
	);
	include('../header.inc.php');

	$pref_tpl = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$templates = Array(
		'pref' => 'index.tpl'
	);

	$pref_tpl->set_file($templates);

	$pref_tpl->set_block('pref','list');
	$pref_tpl->set_block('pref','app_row');
	$pref_tpl->set_block('pref','app_row_noicon');
	$pref_tpl->set_block('pref','link_row');
	$pref_tpl->set_block('pref','spacer_row');

	if ($GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		// This is where we will keep track of our position.
		// Developers won't have to pass around a variable then
		$session_data = $GLOBALS['phpgw']->session->appsession('session_data','preferences');

		if (! is_array($session_data))
		{
			$session_data = array('type' => 'user');
			$GLOBALS['phpgw']->session->appsession('session_data','preferences',$session_data);
		}

		if (! $GLOBALS['HTTP_GET_VARS']['type'])
		{
			$type = $session_data['type'];
		}
		else
		{
			$type = $GLOBALS['HTTP_GET_VARS']['type'];
			$session_data = array('type' => $type);
			$GLOBALS['phpgw']->session->appsession('session_data','preferences',$session_data);
		}

		$tabs[] = array(
			'label' => lang('Your preferences'),
			'link'  => $GLOBALS['phpgw']->link('/preferences/index.php','type=user')
		);
		$tabs[] = array(
			'label' => lang('Default preferences'),
			'link'  => $GLOBALS['phpgw']->link('/preferences/index.php','type=default')
		);
		$tabs[] = array(
			'label' => lang('Forced preferences'),
			'link'  => $GLOBALS['phpgw']->link('/preferences/index.php','type=forced')
		);

		switch($type)
		{
			case 'user':    $selected = 0; break;
			case 'default': $selected = 1; break;
			case 'forced':  $selected = 2; break;
		}
		$pref_tpl->set_var('tabs',$GLOBALS['phpgw']->common->create_tabs($tabs,$selected));
	}

	// This func called by the includes to dump a row header
	function section_start($appname='',$icon='')
	{
		global $pref_tpl;

		$pref_tpl->set_var('icon_backcolor',$GLOBALS['phpgw_info']['theme']['row_off']);
//		$pref_tpl->set_var('link_backcolor',$GLOBALS['phpgw_info']['theme']['row_off']);
		$pref_tpl->set_var('a_name',$appname);
		$pref_tpl->set_var('app_name',$GLOBALS['phpgw_info']['apps'][$appname]['title']);
		$pref_tpl->set_var('app_icon',$icon);
		if ($icon)
		{
			$pref_tpl->parse('rows','app_row',True);
		}
		else
		{
			$pref_tpl->parse('rows','app_row_noicon',True);
		}
	}

	function section_item($pref_link='',$pref_text='')
	{
		global $pref_tpl;

		$pref_tpl->set_var('pref_link',$pref_link);

		if (strtolower($pref_text) == 'grant access' && $GLOBALS['phpgw_info']['server']['deny_user_grants_access'])
		{
			return False;
		}
		else
		{
			$pref_tpl->set_var('pref_text',$pref_text);
		}

		$pref_tpl->parse('rows','link_row',True);
	}

	function section_end()
	{
		global $pref_tpl;

		$pref_tpl->parse('rows','spacer_row',True);
	}

	function display_section($appname,$file,$file2=False)
	{
		if ($file2)
		{
			$file = $file2;
		}
		section_start($appname,$GLOBALS['phpgw']->common->image($appname,Array('navbar',$appname)));

		while(list($text,$url) = each($file))
		{
			section_item($url,lang($text));
		}
		section_end();
	}

	$GLOBALS['phpgw']->hooks->process('preferences',array('preferences'));
	$pref_tpl->pfp('out','list');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
