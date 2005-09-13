<?php
	/**************************************************************************\
	* eGoupWare - Addressbook                                                  *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.16.2.1 2004/07/25 01:34:58 ralfbecker Exp $ */

	/* Basic information about this app */
	$setup_info['tts']['name']      = 'tts';
	$setup_info['tts']['title']     = 'Trouble Ticket System';
	$setup_info['tts']['version']   = '1.0.002';
	$setup_info['tts']['app_order'] = 10;
	$setup_info['tts']['enable']    = 1;

	/* The tables this app creates */
	$setup_info['tts']['tables']    = array('phpgw_tts_tickets','phpgw_tts_views','phpgw_tts_states','phpgw_tts_transitions');

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['tts']['hooks'][]   = 'admin';
	$setup_info['tts']['hooks'][]   = 'home';
	$setup_info['tts']['hooks'][]   = 'preferences';
	$setup_info['tts']['hooks'][]   = 'settings';
	$setup_info['tts']['hooks'][]   = 'sidebox_menu';

	/* Dependencies for this app to work */
	$setup_info['tts']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.14','0.9.15','0.9.16','1.0.0')
	);

