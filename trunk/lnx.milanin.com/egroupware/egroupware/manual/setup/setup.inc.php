<?php
	/**************************************************************************\
	* phpGroupWare - Online User Manual                                        *
	* http://www.eGroupWare.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.9 2004/06/29 17:44:41 ralfbecker Exp $ */

	/* Basic information about this app */
	$setup_info['manual']['name']      = 'manual';
	$setup_info['manual']['title']     = 'User Manual';
	$setup_info['manual']['version']   = '1.0.0';
	$setup_info['manual']['app_order'] = 5;
	$setup_info['manual']['enable']    = 4;	// popup

	$setup_info['manual']['author']    =
	$setup_info['manual']['maintainer'] = 'Ralf Becker';
	$setup_info['manual']['maintainer_email'] = 'RalfBecker@outdoor-training.de';
	$setup_info['manual']['license']   = 'GPL';
	$setup_info['manual']['description'] =
		'The new eGW Online User Manual uses the Wiki app.';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['manual']['hooks']['admin'] = 'manual.uimanualadmin.menu';

	/* Dependencies for this app to work */
	$setup_info['manual']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.14','1.0.0')
	);
	$setup_info['manual']['depends'][] = array(
		 'appname' => 'wiki',
		 'versions' => Array('0.9.15.005','1.0.0')
	);
?>
