<?php
  /*************************************************************************\
  * eGroupWare Setup - Projects                                             *
  * http://www.egroupware.org                                               *
  * --------------------------------------------                            *
  * This program is free software; you can redistribute it and/or modify it *
  * under the terms of the GNU General Public License as published by the   *
  * Free Software Foundation; either version 2 of the License, or (at your  *
  * option) any later version.                                              *
  \*************************************************************************/

  /* $Id: setup.inc.php,v 1.42.2.1 2004/11/06 12:15:57 ralfbecker Exp $ */

	$setup_info['projects']['name']      = 'projects';
	$setup_info['projects']['version']   = '1.0.0.004';
	$setup_info['projects']['app_order'] = 8;
	$setup_info['projects']['enable']    = 1;

	$setup_info['projects']['author'] = 'Bettina Gille';

	$setup_info['projects']['license']  = 'GPL';
	$setup_info['projects']['description'] = 'Advanced project management';

	$setup_info['projects']['maintainer'] = array(
		'name'	=> 'Lars Kneschke',
		'email'	=> 'lkneschke@users.sourceforge.net',
	);

	$setup_info['projects']['tables'] = array
	(
		'phpgw_p_projects',
		'phpgw_p_activities',
		'phpgw_p_budget',
		'phpgw_p_projectactivities',
		'phpgw_p_hours',
		'phpgw_p_projectmembers',
		'phpgw_p_invoice',
		'phpgw_p_invoicepos',
		'phpgw_p_delivery',
		'phpgw_p_deliverypos',
		'phpgw_p_mstones',
		'phpgw_p_roles',
		'phpgw_p_costs',
		'phpgw_p_ttracker',
		'phpgw_p_events',
		'phpgw_p_alarm',
		'phpgw_p_resources'
	);

/* The hooks this app includes, needed for hooks registration */

	$setup_info['projects']['hooks'] = array
	(
		'preferences',
		'admin',
		'add_def_pref',
		'deleteaccount',
		'home',
		'sidebox_menu'
	);
	
	$setup_info['projects']['hooks']['updateevent']	= 'projects.boprojects.checkWorkLoad';

/* Dependencies for this app to work */

	$setup_info['projects']['depends'][] = array
	(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16','0.9.17','0.9.14','1.0.0','1.0.1')
	);	
	$setup_info['projects']['depends'][] = array
	(
		 'appname' => 'infolog',
		 'versions' => Array('1.0.0','1.0.1')
	);
