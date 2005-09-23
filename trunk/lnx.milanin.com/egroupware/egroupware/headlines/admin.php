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

	/* $Id: admin.php,v 1.18 2004/01/27 18:35:51 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only'              => True,
		'currentapp'              => 'headlines',
		'enable_nextmatchs_class' => True,
		'noheader'                => True,
		'nonavbar'                => True,
	);
	include('../header.inc.php');

	$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Headline Sites');
	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();

	// This is done for a reason (jengo)
	$GLOBALS['phpgw']->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('headlines'));
	$GLOBALS['phpgw']->template->set_file(array(
		'admin' => 'admin.tpl'
	));
	$GLOBALS['phpgw']->template->set_block('admin','list');
	$GLOBALS['phpgw']->template->set_block('admin','row');
	$GLOBALS['phpgw']->template->set_block('admin','row_empty');

	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('lang_site',lang('Site'));
	$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
	$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
	$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));
	$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));

	$GLOBALS['phpgw']->db->query('select count(*) from phpgw_headlines_sites',__LINE__,__FILE__);
	$GLOBALS['phpgw']->db->next_record();

	if (! $GLOBALS['phpgw']->db->f(0))
	{
		$GLOBALS['phpgw']->template->set_var('lang_row_empty',lang('No headlines found'));
		$GLOBALS['phpgw']->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
		$GLOBALS['phpgw']->template->parse('rows','row_empty');
	}

	$GLOBALS['phpgw']->db->query('select con,display from phpgw_headlines_sites order by display',__LINE__,__FILE__);
	while ($GLOBALS['phpgw']->db->next_record())
	{
		$GLOBALS['phpgw']->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

		$GLOBALS['phpgw']->template->set_var('row_display',$GLOBALS['phpgw']->db->f('display'));
		$GLOBALS['phpgw']->template->set_var('row_edit',$GLOBALS['phpgw']->link('/headlines/editheadline.php','con='.$GLOBALS['phpgw']->db->f('con')));
		$GLOBALS['phpgw']->template->set_var('row_delete',$GLOBALS['phpgw']->link('/headlines/deleteheadline.php','con='.$GLOBALS['phpgw']->db->f('con')));
		$GLOBALS['phpgw']->template->set_var('row_view',$GLOBALS['phpgw']->link('/headlines/viewheadline.php','con='.$GLOBALS['phpgw']->db->f('con')));

		$GLOBALS['phpgw']->template->parse('rows','row',True);
	}

	$GLOBALS['phpgw']->template->set_var('add_url',$GLOBALS['phpgw']->link('/headlines/newheadline.php'));
	$GLOBALS['phpgw']->template->set_var('grab_more_url',$GLOBALS['phpgw']->link('/headlines/grabnewssites.php'));
	$GLOBALS['phpgw']->template->set_var('lang_grab_more',lang('Grab New News Sites'));

	$GLOBALS['phpgw']->template->pfp('out','list');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
