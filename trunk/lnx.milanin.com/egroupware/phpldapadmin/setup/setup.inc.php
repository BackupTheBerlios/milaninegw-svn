<?php
	/**************************************************************************\
	* eGroupWare - phpldapadmin                                                *
	* http://www.eGroupWare.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.6 2004/07/02 22:38:37 ralfbecker Exp $ */

	$setup_info['phpldapadmin']['name']      = 'phpldapadmin';
	$setup_info['phpldapadmin']['version']   = '1.0.0';
	$setup_info['phpldapadmin']['app_order'] = 42;
	$setup_info['phpldapadmin']['tables']    = array();
	$setup_info['phpldapadmin']['enable']    = 1;

	$setup_info['phpldapadmin']['author'] = array(
		'name' => 'phpldapadmin project',
		'email' => 'phpldapadmin-devel@lists.sourceforge.net'
	);
 	$setup_info['phpldapadmin']['maintainer'] = array(
		'name'  => 'Ralf Becker',
		'email' => 'ralfbecker@outdoor-training.de'
	);
	$setup_info['phpldapadmin']['license']  = 'GPL';
	$setup_info['phpldapadmin']['description'] =
		'A comprehensive LDAP administration tool.<br>For more info visit the <a href="http://phpldapadmin.sourceforge.net/">Homepage</a> of the phpldapadmin project.';
	$setup_info['phpldapadmin']['note'] =
		'At the moment you need to configure it by editing or creating a config.php file (from the config.php.example file).';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['phpldapadmin']['hooks'] = array(
	);

	/* Dependencies for this app to work */
	$setup_info['phpldapadmin']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.14','1.0.0')
	);
?>
