<?php
	/**************************************************************************\
	* eGroupWare - Comics                                                      *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.16 2004/07/02 22:29:47 ralfbecker Exp $ */

	/* Basic information about this app */
	$setup_info['comic']['name']      = 'comic';
	$setup_info['comic']['title']     = 'Comics';
	$setup_info['comic']['version']   = '1.0.0';
	$setup_info['comic']['app_order'] = 21;
	$setup_info['comic']['enable']    = 1;

	$setup_info['comic']['author'] = 'Sam Wynn';
	$setup_info['comic']['license']  = 'GPL';
	$setup_info['comic']['description'] = 'Displays comic strips.';
	$setup_info['comic']['maintainer'] = array(
		'name' => 'eGroupWare Developers',
		'email' => 'egroupware-developers@lists.sourceforge.net'
	);

	/* The tables this app creates */
	$setup_info['comic']['tables']    = array(
		'phpgw_comic',
		'phpgw_comic_admin',
		'phpgw_comic_data'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['comic']['hooks'][] = 'admin';
	$setup_info['comic']['hooks'][] = 'home';
	$setup_info['comic']['hooks'][] = 'preferences';
	$setup_info['comic']['hooks'][] = 'sidebox_menu';


	/* Dependencies for this app to work */
	$setup_info['comic']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.14','1.0.0')
	);
?>
