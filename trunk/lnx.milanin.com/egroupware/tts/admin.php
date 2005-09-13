<?php
  /**************************************************************************\
  * eGroupWare - TTS                                                         *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: admin.php,v 1.9 2004/04/29 16:52:07 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'  => 'tts', 
		'noheader'    => True, 
		'nonavbar'    => True, 
		'noappheader' => True,
		'noappfooter' => True,
		'enable_config_class'     => True,
		'enable_nextmatchs_class' => True
	);

	include('../header.inc.php');

 	if ($_POST['cancel'])
 	{
 		$GLOBALS['phpgw']->redirect_link('/tts/index.php');
 	}

	$option_names = array(lang('Disabled'), lang('Users choice'), lang('Force'));
	$owner_selected = array ();
	$group_selected = array ();
	$assigned_selected = array ();

	$GLOBALS['phpgw']->config->read_repository();

	if ($_POST['submit'])
	{
		if ($_POST['ownernotification'])
		{
			$GLOBALS['phpgw']->config->config_data['ownernotification'] = True;
		}
		else
		{
			unset($GLOBALS['phpgw']->config->config_data['ownernotification']);
		}

		if ($_POST['groupnotification'])
		{
			$GLOBALS['phpgw']->config->config_data['groupnotification'] = True;
		}
		else
		{
			unset($GLOBALS['phpgw']->config->config_data['groupnotification']);
		}

		if ($_POST['assignednotification'])
		{
			$GLOBALS['phpgw']->config->config_data['assignednotification'] = True;
		}
		else
		{
			unset($GLOBALS['phpgw']->config->config_data['assignednotification']);
		}
		if( $GLOBALS['phpgw']->config->config_data['ownernotification'] ||
			$GLOBALS['phpgw']->config->config_data['groupnotification'] ||
			$GLOBALS['phpgw']->config->config_data['assignednotification']) {
			$GLOBALS['phpgw']->config->config_data['mailnotification'] = True;
		} else {
			unset($GLOBALS['phpgw']->config->config_data['mailnotification']);
		}
		$GLOBALS['phpgw']->config->save_repository(True);
		$GLOBALS['phpgw']->redirect_link('/tts/index.php');
	}

	$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['tts']['title'] . ' - ' . lang('Administration');
	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();

	$GLOBALS['phpgw']->template->set_file(array('admin' => 'admin.tpl'));

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/tts/admin.php'));

	$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
	$GLOBALS['phpgw']->template->set_var('tr_color',$tr_color);

	$GLOBALS['phpgw']->template->set_var('lang_ownernotification',lang('notify changes to ticket owner by e-mail'));
	if ($GLOBALS['phpgw']->config->config_data['ownernotification'])
	{
		$GLOBALS['phpgw']->template->set_var('ownernotification',' checked');
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('ownernotification','');
	}
	
	$GLOBALS['phpgw']->template->set_var('lang_groupnotification',lang('notify changes to ticket group by e-mail'));
	if ($GLOBALS['phpgw']->config->config_data['groupnotification'])
	{
		$GLOBALS['phpgw']->template->set_var('groupnotification',' checked');
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('groupnotification','');
	}
	
	$GLOBALS['phpgw']->template->set_var('lang_assignednotification',lang('notify changes to ticket assignee by e-mail'));
	if ($GLOBALS['phpgw']->config->config_data['assignednotification'])
	{
		$GLOBALS['phpgw']->template->set_var('assignednotification',' checked');
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('assignednotification','');
	}
	
 	$GLOBALS['phpgw']->template->set_var('lang_submit',lang('Save'));
 	$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));

	$GLOBALS['phpgw']->template->pparse('out','admin');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
