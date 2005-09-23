<?php
    /**************************************************************************\
    * eGroupWare - Polls                                                       *
    * http://www.egroupware.org                                                *
    * Copyright (c) 1999 Till Gerken (tig@skv.org)                             *
    * Modified by Greg Haygood (shrykedude@bellsouth.net)                      *
    * -----------------------------------------------                          *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

	/* $Id: setup.inc.php,v 1.12 2004/07/02 22:39:11 ralfbecker Exp $ */

	/* Basic information about this app */
	$setup_info['polls']['name']      = 'polls';
	$setup_info['polls']['title']     = 'Polls';
	$setup_info['polls']['version']   = '1.0.0';
	$setup_info['polls']['app_order'] = 17;
	$setup_info['polls']['enable']    = 1;

	/* The tables this app creates */
	$setup_info['polls']['tables']    = array(
		'phpgw_polls_data',
		'phpgw_polls_desc',
		'phpgw_polls_user',
		'phpgw_polls_settings'
	);

	$setup_info['polls']['hooks'][]   = 'admin';
	$setup_info['polls']['hooks'][]   = 'sidebox_menu';
	/* Dependencies for this app to work */
	$setup_info['polls']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.14','1.0.0')
	);

