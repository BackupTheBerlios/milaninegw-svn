<?php
	/**************************************************************************\
	* eGroupWare - Filemanager                                                 *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.26 2004/07/02 22:31:52 ralfbecker Exp $ */

	$setup_info['filemanager']['name']    = 'filemanager';
	$setup_info['filemanager']['title']   = 'Filemanager';
	$setup_info['filemanager']['version'] = '1.0.0';
	$setup_info['filemanager']['app_order'] = 6;
	$setup_info['filemanager']['enable']  = 1;

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['filemanager']['hooks'] = array
	(
		'add_def_pref',
		'admin',
		'deleteaccount',
		'settings',
		'sidebox_menu',
		'personalizer',
		'preferences',
	);

	/* Dependencies for this app to work */
	$setup_info['filemanager']['depends'][] = array
	(
		 'appname' => 'phpgwapi',
		 'versions' => array('0.9.14','0.9.16','1.0.0')
	);
?>
