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
	$setup_info['vanilla']['name']      = 'vanilla';
	$setup_info['vanilla']['title']     = 'Discussions Board';
	$setup_info['vanilla']['version']   = '0.9.2.6-milaninegw';
	$setup_info['vanilla']['app_order'] = 4;
	$setup_info['vanilla']['enable']    = 1;

	/* The tables this app creates */

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['vanilla']['hooks'][] = 'admin';
	$setup_info['vanilla']['hooks'][] = 'home';
	$setup_info['vanilla']['hooks'][] = 'preferences';
	$setup_info['vanilla']['hooks'][] = 'settings';
	$setup_info['vanilla']['hooks'][] = 'sidebox_menu';

	/* Dependencies for this app to work */
	$setup_info['vanilla']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','1.0.0')
	);
?>
