<?php
  /**************************************************************************\
  * eGroupWare - Setup                                                       *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: tables_current.inc.php,v 1.8.2.1 2004/07/25 01:34:58 ralfbecker Exp $ */

	$phpgw_baseline = array(
		'phpgw_tts_tickets' => array(
			'fd' => array(
				'ticket_id' 					=> array('type' => 'auto','nullable' => False),
				'ticket_group' 				=> array('type' => 'varchar','precision' => '40'),
				'ticket_priority' 			=> array('type' => 'int','precision' => '2','nullable' => False),
				'ticket_owner' 				=> array('type' => 'varchar','precision' => '10'),
				'ticket_assignedto' 			=> array('type' => 'varchar','precision' => '10'),
				'ticket_subject' 				=> array('type' => 'varchar','precision' => '255'),
				'ticket_category' 			=> array('type' => 'varchar','precision' => '25'),
				'ticket_billable_hours' 	=> array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => False),
				'ticket_billable_rate' 		=> array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => False),
				'ticket_status' 				=> array('type' => 'char','precision' => '1','nullable' => False),
				'ticket_details' 				=> array('type' => 'text','nullable' => False),
				'ticket_state' 				=> array('type' => 'int','precision' => '4','nullable' => False, 'default'=>'-1')
			),
			'pk' => array('ticket_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_tts_views' => array(
			'fd' => array(
				'view_id' 					=> array('type' => 'int','precision' => '4','nullable' => False),
				'view_account_id' 		=> array('type' => 'varchar','precision' => '40','nullable' => True),
				'view_time' 				=> array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'phpgw_tts_states' => array(
			'fd' => array(
				'state_id' 					=> array('type' => 'auto','nullable' => False),
				'state_name' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'state_description' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'state_initial' 			=> array('type' => 'int', 'precision'=>'4', 'nullable' => False, 'default'=>'0')
			),
			'pk' => array('state_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_tts_transitions' => array(
			'fd' => array(
				'transition_id' 				=> array('type' => 'auto','nullable' => False),
				'transition_name' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'transition_description' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'transition_source_state' 	=> array('type' => 'int','precision' => '4','nullable' => False),
				'transition_target_state' 	=> array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('transition_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

