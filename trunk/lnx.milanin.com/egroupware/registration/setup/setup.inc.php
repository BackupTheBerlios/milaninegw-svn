<?php
	/**************************************************************************\
	* eGroupWare - Registration                                                *
	* http://www.egroupware.org                                                *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.13 2004/07/02 22:40:23 ralfbecker Exp $ */

	/* Basic information about this app */
	$setup_info['registration']['name']      = 'registration';
	$setup_info['registration']['title']     = 'Registration';
	$setup_info['registration']['version']   = '1.0.0';
	$setup_info['registration']['app_order'] = '40';
	$setup_info['registration']['enable']    = 2;

	/* The tables this app creates */
	$setup_info['registration']['tables']    = array(
		'phpgw_reg_accounts',
		'phpgw_reg_fields'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['registration']['hooks'][] = 'admin';
	$setup_info['registration']['hooks'][] = 'logout';

	/* Dependencies for this app to work */
	$setup_info['registration']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','0.9.15','0.9.16','1.0.0')
	);
?>
