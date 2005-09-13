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

	/**************************************************************************\
	* This file should be generated for you. It should never be edited by hand *
	\**************************************************************************/
	/* $Id: tables_current.inc.php,v 1.25.2.2 2004/11/06 12:15:57 ralfbecker Exp $ */

	$phpgw_baseline = array(
		'phpgw_p_projects' => array(
			'fd' => array(
				'project_id' => array('type' => 'auto','nullable' => False),
				'p_number' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'owner' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'start_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'end_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'coordinator' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'customer' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'status' => array('type' => 'varchar','precision' => '9','default' => 'active','nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'title' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'budget' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => False),
				'category' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'parent' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'time_planned' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'date_created' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'processor' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'investment_nr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'main' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'level' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'previous' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'customer_nr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'reference' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'url' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'result' => array('type' => 'text','nullable' => True),
				'test' => array('type' => 'text','nullable' => True),
				'quality' => array('type' => 'text','nullable' => True),
				'accounting' => array('type' => 'varchar','precision' => '8','nullable' => True),
				'acc_factor' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => False),
				'billable' => array('type' => 'char','precision' => '1','default' => 'N','nullable' => False),
				'psdate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'pedate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'priority' => array('type' => 'int','precision' => '2','default' => '0','nullable' => True),
				'discount' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'e_budget' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'inv_method' => array('type' => 'text','nullable' => True),
				'acc_factor_d' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'discount_type' => array('type' => 'varchar','precision' => '7','nullable' => True)
			),
			'pk' => array('project_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_activities' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'a_number' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'remarkreq' => array('type' => 'char','precision' => '1','default' => 'N','nullable' => False),
				'minperae' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'billperae' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => False),
				'category' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('a_number'),
			'uc' => array()
		),
		'phpgw_p_projectactivities' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'activity_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'billable' => array('type' => 'char','precision' => '1','default' => 'N','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_hours' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'employee' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'activity_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'entry_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'start_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'end_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'remark' => array('type' => 'text','nullable' => True),
				'minutes' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'status' => array('type' => 'varchar','precision' => '6','default' => 'done','nullable' => False),
				'hours_descr' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'dstatus' => array('type' => 'char','precision' => '1','default' => 'o','nullable' => True),
				'pro_parent' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'pro_main' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'billable' => array('type' => 'char','precision' => '1','default' => 'Y','nullable' => False),
				'km_distance' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				't_journey' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'cost_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('project_id','cost_id'),
			'uc' => array()
		),
		'phpgw_p_projectmembers' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'account_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'type' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'accounting' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'role_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'events' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'd_accounting' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_invoice' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'i_number' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'i_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'customer' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'i_sum' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => False),
				'owner' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('i_number')
		),
		'phpgw_p_invoicepos' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'invoice_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'hours_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_delivery' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'd_number' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'd_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'customer' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'owner' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('d_number')
		),
		'phpgw_p_deliverypos' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'delivery_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'hours_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_mstones' => array(
			'fd' => array(
				's_id' => array('type' => 'auto','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'title' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'description' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'edate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('s_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_roles' => array(
			'fd' => array(
				'role_id' => array('type' => 'auto','nullable' => False),
				'role_name' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('role_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_costs' => array(
			'fd' => array(
				'cost_id' => array('type' => 'auto','nullable' => False),
				'cost_name' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('cost_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_ttracker' => array(
			'fd' => array(
				'track_id' => array('type' => 'auto','nullable' => False),
				'employee' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'project_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'start_date' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'end_date' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'remark' => array('type' => 'text'),
				'hours_descr' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'status' => array('type' => 'varchar','precision' => '8'),
				'minutes' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'km_distance' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0'),
				't_journey' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0'),
				'stopped' => array('type' => 'char','precision' => '1','default' => 'N'),
				'cost_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'billable' => array('type' => 'char','precision' => '1','default' => 'Y')
			),
			'pk' => array('track_id'),
			'fk' => array(),
			'ix' => array('project_id'),
			'uc' => array()
		),
		'phpgw_p_events' => array(
			'fd' => array(
				'event_id' => array('type' => 'auto','nullable' => False),
				'event_name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'event_type' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'event_extra' => array('type' => 'int','precision' => '2','default' => '0','nullable' => True)
			),
			'pk' => array('event_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_alarm' => array(
			'fd' => array(
				'alarm_id' => array('type' => 'auto','nullable' => False),
				'alarm_type' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'alarm_extra' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'alarm_send' => array('type' => 'char','precision' => '1','default' => '1','nullable' => True)
			),
			'pk' => array('alarm_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_p_resources' => array(
			'fd' => array(
				'employee' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'resource' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('project_id'),
			'fk' => array('project_id','employee'),
			'ix' => array('employee'),
			'uc' => array()
		),
		'phpgw_p_budget' => array(
			'fd' => array(
				'budget_id' => array('type' => 'auto','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'budget_amount' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => False,'default' => '0'),
				'budget_year' => array('type' => 'int','precision' => '4','default' => '0'),
				'budget_month' => array('type' => 'int','precision' => '4','default' => '0')
			),
			'pk' => array('budget_id'),
			'fk' => array(),
			'ix' => array('project_id','budget_year','budget_month'),
			'uc' => array()
		)
	);

?>
