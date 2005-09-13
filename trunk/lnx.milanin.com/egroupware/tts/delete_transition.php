<?php
	/**************************************************************************\
	* eGroupWare - Trouble Ticket System                                       *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: delete_transition.php,v 1.3 2004/01/27 19:09:33 reinerj Exp $ */

	/* Note to self:
	** Self ... heres the query to use when limiting access to entrys within a group
	** The acl class *might* handle this instead .... not sure
	** select distinct group_ticket_id, phpgw_tts_groups.group_ticket_id, phpgw_tts_tickets.*
	** from phpgw_tts_tickets, phpgw_tts_groups where ticket_id = group_ticket_id and group_id in (14,15);
	*/

	/* ACL levels
	** 1 - Read ticket within your group only
	** 2 - Close ticket
	** 4 - Allow to make changes to priority, billing hours, billing rate, category, and assigned to
	*/

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'tts';
	$GLOBALS['phpgw_info']['flags']['enable_contacts_class'] = True;
	$GLOBALS['phpgw_info']['flags']['enable_categories_class'] = True;
	$GLOBALS['phpgw_info']['flags']['enable_nextmatchs_class'] = True;
	$GLOBALS['phpgw_info']['flags']['noheader'] = True;
	include('../header.inc.php');

	// select what tickets to view
	$transition_id = intval(get_var('transition_id',array('POST','GET')));

	if($_POST['delete'] && $transition_id)
	{
		$GLOBALS['phpgw']->db->query("delete from phpgw_tts_transitions where transition_id=$transition_id",__LINE__,__FILE__);
	}

	if ($_POST['delete'] || $_POST['cancel'] || !$transition_id)
	{
		$GLOBALS['phpgw']->redirect_link('/tts/transitions.php');
	}

	$GLOBALS['phpgw']->template->set_file('delete_transition','delete_transition.tpl');

	$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['tts']['title'].
		' - '.lang('Deleting the transition');
	$GLOBALS['phpgw']->common->phpgw_header();

	$s=id2field('phpgw_tts_transitions','transition_name','transition_id',$transition_id);
	$GLOBALS['phpgw']->template->set_var('lang_are_you_sure',lang('You want to delete the transition %1. Are you sure?',"'".$s."'"));

	$GLOBALS['phpgw']->template->set_var('delete_transition_link',
		$GLOBALS['phpgw']->link('/tts/delete_transition.php','transition_id='.$transition_id));
	$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
	$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));

	$GLOBALS['phpgw']->template->pfp('out','delete_transition');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
