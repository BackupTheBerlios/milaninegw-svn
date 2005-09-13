<?php
  /**************************************************************************\
  *eGroupWare - Setup                                                        *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id: default_records.inc.php,v 1.5.2.2 2005/02/12 15:56:39 ralfbecker Exp $ */

	$oProc->query("DELETE FROM phpgw_tts_states");
	$oProc->query("DELETE FROM phpgw_tts_transitions");

	$states = array(
		'UNDEFINED' => 'The ticket was existent before the Petri Net infrastructure was defined and should be assigned a state.',
		'NEW'       => 'A new ticket has been reported.',
		'ACCEPTED'  => 'The ticket has been accepted by the owner, who is about to work on it.',
		'REOPENED'  => 'The ticket has been reopened for further work.',
		'RESOLVED'  => 'The ticket has been successfully resolved.',
		'VERIFIED'  => 'The ticket has been verified and has to be worked on.',
		'CLOSED'    => 'The ticket has been closed without resolution.',
		'TOVALIDATE'=> 'The ticket has been worked on and the work requires validation.',
		'NEEDSWORK' => 'The ticket has been worked on, but requires more work.',
		'INVALID'   => 'The owner of the ticket was not able to confirm the issue.',
		'DUPLICATE' => 'The ticket was found a duplicate of another ticket.',
	);
	foreach($states as $state => $desc)
	{
		$oProc->query("insert into phpgw_tts_states(state_name,state_description,state_initial) values('$state','$desc',".(int)($state=='NEW').')');
		$states[$state] = $oProc->m_odb->get_last_insert_id('phpgw_tts_states','state_id');
	}

	foreach($states as $state => $num)
	{
		if ($state != 'UNDEFINED')
	{
		$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
				." values('TO$state','Put the preexistent ticket into the state %1.',$states[UNDEFINED],$num)");
		}
	}
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('ACCEPT','Accept the ticket into verification process.',$states[NEW],$states[ACCEPTED])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('VERIFY','Verify the ticket to work on it.',$states[ACCEPTED],$states[VERIFIED])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('INVALIDATE','The ticket is invalid and cannot be worked on.',$states[ACCEPTED],$states[INVALID])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('DUPLICATE','The ticket is a duplicate of another ticket and should be closed.',$states[ACCEPTED],$states[DUPLICATE])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('CLOSE','Close the invalid ticket.',$states[INVALID],$states[CLOSED])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('MOREWORK','I worked on the ticket, but did not finish.',$states[VERIFIED],$states[NEEDSWORK])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('COMPLETED','I worked on the ticket and the work requires validation.',$states[VERIFIED],$states[TOVALIDATE])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('COMPLETED','I worked on the ticket and the work requires validation.',$states[NEEDSWORK],$states[TOVALIDATE])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('NOT COMPLETED','The validation of the ticket was unsuccessfull. The ticket requires more work.',$states[TOVALIDATE],$states[NEEDSWORK])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('RESOLVE','The ticket resolution was successfully validated. Close the ticket.',$states[TOVALIDATE],$states[RESOLVED])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('REOPEN','The closed ticket requires more work. Reopen it.',$states[CLOSED],$states[REOPENED])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('REOPEN','The closed ticket requires more work. Reopen it.',$states[RESOLVED],$states[REOPENED])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('NO DUPLICATE','The ticket is essentially not a duplicate of another ticket. Reopen it.',$states[DUPLICATE],$states[REOPENED])");
	$oProc->query("insert into phpgw_tts_transitions(transition_name,transition_description,transition_source_state,transition_target_state)"
		." values('ACCEPT','Accept the ticket into verification process.',$states[REOPENED],$states[ACCEPTED])");
?>
