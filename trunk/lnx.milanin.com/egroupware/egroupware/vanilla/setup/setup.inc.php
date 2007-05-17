<?php
	/**************************************************************************\
	* eGroupWare - Messenger                                                   *
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

	/* $Id: setup.inc.php,v 1.10 2004/03/28 12:52:26 reinerj Exp $ */

	/* Basic information about this app */
	$setup_info['profile']['name']      = 'profile';
	$setup_info['profile']['title']     = 'Member Profile';
	$setup_info['profile']['version']   = '0.9.2.6-milaninegw';
	$setup_info['profile']['app_order'] = 2;
	$setup_info['profile']['enable']    = 1;

	/* The tables this app creates */

	/* The hooks this app includes, needed for hooks registration */
	//$setup_info['profile']['hooks'][] = 'admin';
	//$setup_info['profile']['hooks'][] = 'home';
	//$setup_info['profile']['hooks'][] = 'preferences';
	//$setup_info['profile']['hooks'][] = 'settings';
	//$setup_info['profile']['hooks'][] = 'sidebox_menu';

	/* Dependencies for this app to work */
	$setup_info['profile']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','1.0.0')
	);
?>
