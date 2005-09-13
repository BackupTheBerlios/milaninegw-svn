<?php
    /**************************************************************************\
    * eGroupWare - Stock Quotes                                                *
    * http://www.egroupware.org                                                *
    * --------------------------------------------                             *
    * This program is free software; you can redistribute it and/or modify it  *
    * under the terms of the GNU General Public License as published by the    *
    * Free Software Foundation; either version 2 of the License, or (at your   *
    * option) any later version.                                               *
    /**************************************************************************\
    /* $Id: setup.inc.php,v 1.17 2004/07/02 22:40:58 ralfbecker Exp $ */

	$setup_info['stocks']['name']      = 'stocks';
	$setup_info['stocks']['title']     = 'Stock Quotes';
	$setup_info['stocks']['version']   = '1.0.0';
	$setup_info['stocks']['app_order'] = 18;
	$setup_info['stocks']['enable']    = 1;

	$setup_info['stocks']['tables'] = '';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['stocks']['hooks'][] = 'preferences';
	$setup_info['stocks']['hooks'][] = 'home';
	$setup_info['stocks']['hooks'][] = 'sidebox_menu';

	/* Dependencies for this app to work */
	$setup_info['stocks']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.14','1.0.0')
	);
?>
