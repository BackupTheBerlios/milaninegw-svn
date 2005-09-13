<?php
	/**************************************************************************\
	* eGroupWare - Headlines Administration                                    *
	* http://www.egroupware.org                                                *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: deleteheadline.php,v 1.15 2004/01/27 18:35:51 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only' => True,
		'currentapp' => 'headlines',
		'enable_nextmatchs_class' => True,
		'nonavbar'                => True,
		'noheader'                => True
	);
	include('../header.inc.php');

	$con = (int)get_var('con',array('POST','GET'));

	if ($_POST['no'])
	{
		$GLOBALS['phpgw']->redirect_link('/headlines/admin.php','cd=16');
	}

	if ($con && $_POST['yes'])
	{
		$GLOBALS['phpgw']->db->transaction_begin();

		$GLOBALS['phpgw']->db->query("delete from phpgw_headlines_sites where con=$con",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->query("delete from phpgw_headlines_cached where site=$con",__LINE__,__FILE__);

		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_preferences",__LINE__,__FILE__);
		while ($GLOBALS['phpgw']->db->next_record())
		{
			if ($GLOBALS['phpgw']->db->f('preference_owner') == $GLOBALS['phpgw_info']['user']['account_id'])
			{
				if ($GLOBALS['phpgw_info']['user']['preferences']['headlines'][$con])
				{
					$GLOBALS['phpgw']->preferences->delete('headlines',$con);
					$GLOBALS['phpgw']->preferences->commit();
				}
			}
			else
			{
				$phpgw_newuser['user']['preferences'] = $GLOBALS['phpgw']->db->f('preference_value');
				if ($phpgw_newuser['user']['preferences']['headlines'][$con])
				{
					$GLOBALS['phpgw']->preferences->delete_newuser('headlines',$con);
					$GLOBALS['phpgw']->preferences->commit_user($GLOBALS['phpgw']->db->f('preference_owner'));
				}
			}
		}

		$GLOBALS['phpgw']->db->transaction_commit();
		$GLOBALS['phpgw']->redirect_link('/headlines/admin.php','cd=16');
	}

	$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Headlines Administration - Delete headline');
	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();

	// This is done for a reason (jengo)
	$GLOBALS['phpgw']->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('headlines'));

	$GLOBALS['phpgw']->template->set_file(array(
		'delete_form' => 'admin_delete.tpl'
	));

	$GLOBALS['phpgw']->template->set_var('lang_message',lang('Are you sure you want to delete this news site ?'));
	$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));
	$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/headlines/deleteheadline.php'));
	$GLOBALS['phpgw']->template->set_var('con',$con);

	$GLOBALS['phpgw']->template->pfp('out','delete_form');

	$GLOBALS['phpgw']->common->phpgw_footer();
