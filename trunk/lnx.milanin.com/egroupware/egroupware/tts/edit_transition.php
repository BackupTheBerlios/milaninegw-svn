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

	// $Id: edit_transition.php,v 1.4 2004/01/27 19:09:33 reinerj Exp $
	// $Source: /cvsroot/egroupware/tts/edit_transition.php,v $

	$GLOBALS['phpgw_info']['flags']['currentapp']          = 'tts';
	$GLOBALS['phpgw_info']['flags']['enable_send_class']   = True;
	$GLOBALS['phpgw_info']['flags']['enable_config_class'] = True;
	$GLOBALS['phpgw_info']['flags']['enable_categories_class'] = True;
	$GLOBALS['phpgw_info']['flags']['noheader']            = True;

	include('../header.inc.php');

	$transition_id = intval(get_var('transition_id',array('POST','GET')));

	if($_POST['cancel'])
	{
		$GLOBALS['phpgw']->redirect_link('/tts/transitions.php');
	}

	$GLOBALS['phpgw']->config->read_repository();

	if($_POST['save'])
	{
		$transition = $_POST['transition'];
		if (get_magic_quotes_gpc())
		{
			foreach(array('name','description') as $name)
			{
				$transition[$name] = stripslashes($transition[$name]);
			}
		}

		if (!$transition_id)
		{
			$GLOBALS['phpgw']->db->query("insert into phpgw_tts_transitions (transition_name,transition_description,transition_source_state,transition_target_state) values ('"
			. addslashes($transition['name']) . "','"
			. addslashes($transition['description']) . "',"
			. intval($transition['source_state']) . ", "
			. intval($transition['target_state']). ")",__LINE__,__FILE__);
		}
		else
		{
			$GLOBALS['phpgw']->db->query("update phpgw_tts_transitions "
				. " set transition_name='". addslashes($transition['name']) . "', "
				. " transition_description='". addslashes($transition['description']) . "', "
				. " transition_source_state=". intval($transition['source_state']). ", "
				. " transition_target_state=". intval($transition['target_state'])
				. " WHERE transition_id=".$transition_id,__LINE__,__FILE__);
	
		}
		$GLOBALS['phpgw']->redirect_link('/tts/transitions.php');
	}
	else
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['tts']['title'].
			' - '.($transition_id ? lang('Create new transition') : lang('Edit the transition'));
		$GLOBALS['phpgw']->common->phpgw_header();

		// select the ticket that you selected
		$GLOBALS['phpgw']->db->query("select * from phpgw_tts_transitions where transition_id='$transition_id'",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->next_record();

		$transition['name']           = try_lang($GLOBALS['phpgw']->db->f('transition_name'));
		$transition['source_state']   = try_lang($GLOBALS['phpgw']->db->f('transition_source_state'));
		$transition['target_state']   = try_lang($GLOBALS['phpgw']->db->f('transition_target_state'));
		$transition['description']    = try_lang($GLOBALS['phpgw']->db->f('transition_description'),$transition['target_state']);

		$GLOBALS['phpgw']->template->set_file(array(
			'edit_transition'   => 'edit_transition.tpl'
		));
		$GLOBALS['phpgw']->template->set_block('edit_transition','form');

		$GLOBALS['phpgw']->template->set_var('form_action', $GLOBALS['phpgw']->link('/tts/edit_transition.php','&transition_id='.$transition_id));

		$GLOBALS['phpgw']->template->set_var('lang_transition_name',lang('Transition name'));
		$GLOBALS['phpgw']->template->set_var('lang_transition_description', lang('Description'));
		$GLOBALS['phpgw']->template->set_var('lang_source_state', lang('Source State'));
		$GLOBALS['phpgw']->template->set_var('lang_target_state', lang('Target State'));
		$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
		$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));

		$GLOBALS['phpgw']->template->set_var('value_name',$transition['name']);
		$GLOBALS['phpgw']->template->set_var('value_description',$transition['description']);
		$GLOBALS['phpgw']->template->set_var('options_source_state',listid_field('phpgw_tts_states','state_name','state_id',$transition['source_state']));
		$GLOBALS['phpgw']->template->set_var('options_target_state',listid_field('phpgw_tts_states','state_name','state_id',$transition['target_state']));

		$GLOBALS['phpgw']->template->pfp('out','form');
		$GLOBALS['phpgw']->common->phpgw_footer();
	}

?>
