<?php
	/**************************************************************************\
	* eGroupWare - headlines                                                   *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: preferences_layout.php,v 1.6 2004/01/27 18:35:51 reinerj Exp $ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'headlines',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	if ($_POST['cancel'] || $_POST['save'] || $_POST['headlines_layout'])
	{
		if (!$_POST['cancel'])
		{
			$GLOBALS['phpgw']->preferences->add('headlines','headlines_layout',$_POST['headlines_layout']);
			$GLOBALS['phpgw']->preferences->add('headlines','mainscreen_showheadlines',$_POST['mainscreen'] ? 1 : '');
			$GLOBALS['phpgw']->preferences->save_repository();
		}
		$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/headlines/index.php'));
	}
	$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Headlines layout');
	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();

	$GLOBALS['phpgw']->template->set_file(array(
		'layout1' => 'basic_sample.tpl',
		'layout2' => 'color_sample.tpl',
		'layout3' => 'gray_sample.tpl',
		'body'    => 'preferences_layout.tpl'
	));

	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/headlines/preferences_layout.php'));
	$GLOBALS['phpgw']->template->set_var('save_label',lang('Save'));
	$GLOBALS['phpgw']->template->set_var('cancel_label',lang('Cancel'));

	$GLOBALS['phpgw']->template->set_var('template_label',lang('Choose layout'));

	if ($_POST['save'])
	{
		$selected[$_POST['headlines_layout']] = ' selected';
	}
	else
	{
		$selected[$GLOBALS['phpgw_info']['user']['preferences']['headlines']['headlines_layout']] = ' selected';
		if($GLOBALS['phpgw_info']['user']['preferences']['headlines']['mainscreen_showheadlines'])
		{
			$GLOBALS['phpgw']->template->set_var('mainscreen_checked',' checked');
		}
	}

	$s  = '<option value="basic"' . $selected['basic'] . '>' . lang('Basic') . '</option>';
	$s .= '<option value="color"' . $selected['color'] . '>' . lang('Color') . '</option>';
	$s .= '<option value="gray"'  . $selected['gray'] . '>' . lang('Gray') . '</option>';
	$GLOBALS['phpgw']->template->set_var('template_options',$s);

	$GLOBALS['phpgw']->template->set_var('lang_mainscreen', lang('show headlines on homepage'));
	$GLOBALS['phpgw']->template->set_var('sample',lang('Sample'));
	$GLOBALS['phpgw']->template->set_var('basic',lang('Basic'));
	$GLOBALS['phpgw']->template->parse('layout_1','layout1');
	$GLOBALS['phpgw']->template->set_var('color',lang('Color'));
	$GLOBALS['phpgw']->template->parse('layout_2','layout2');
	$GLOBALS['phpgw']->template->set_var('gray',lang('Gray'));
	$GLOBALS['phpgw']->template->parse('layout_3','layout3');

	$GLOBALS['phpgw']->template->pfp('out','body');
	$GLOBALS['phpgw']->common->phpgw_footer();
