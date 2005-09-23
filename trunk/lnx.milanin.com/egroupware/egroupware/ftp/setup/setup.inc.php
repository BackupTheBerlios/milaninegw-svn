<?php
	/**************************************************************************\
	* eGroupWare - FTP                                                         *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.11 2004/07/02 22:32:36 ralfbecker Exp $ */

	/* Basic information about this app */
	$setup_info['ftp']['name']      = 'ftp';
	$setup_info['ftp']['title']     = 'FTP';
	$setup_info['ftp']['version']   = '1.0.0';
	$setup_info['ftp']['app_order'] = 20;
	$setup_info['ftp']['enable']    = 1;

	$setup_info['ftp']['author'] = 'Joseph Engo';
	$setup_info['ftp']['license']  = 'GPL';
	$setup_info['ftp']['description'] =
		'FTP client.';
	$setup_info['ftp']['maintainer'] = array(
		'name' => 'eGroupWare developers',
		'email' => 'egroupware-developers@lists.sourceforge.net'
	);

	/* Dependencies for this app to work */
	$setup_info['ftp']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','1.0.0')
	);
?>
