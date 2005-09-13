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
	$setup_info['messenger']['name']      = 'messenger';
	$setup_info['messenger']['title']     = 'Messenger';
	$setup_info['messenger']['version']   = '0.8.1';
	$setup_info['messenger']['app_order'] = '19';
	$setup_info['messenger']['enable']    = 1;

	/* The tables this app creates */
	$setup_info['messenger']['tables']    = array(
		'phpgw_messenger_messages'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['messenger']['hooks'][] = 'preferences';
	$setup_info['messenger']['hooks'][] = 'home';
	$setup_info['messenger']['hooks'][] = 'admin';
	$setup_info['messenger']['hooks'][] = 'after_navbar';
	$setup_info['messenger']['hooks'][] = 'sidebox_menu';

	/* Dependencies for this app to work */
	$setup_info['messenger']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','1.0.0')
	);
?>
