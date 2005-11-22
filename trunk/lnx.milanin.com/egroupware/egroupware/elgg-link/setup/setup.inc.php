<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.8 2004/07/02 22:40:39 ralfbecker Exp $ */

	$setup_info['elgg-link']['name']      = 'elgg-link';
	$setup_info['elgg-link']['title']     = 'Members List';
	$setup_info['elgg-link']['version']   = '0.0.1';
	$setup_info['elgg-link']['app_order'] = 1;
	$setup_info['elgg-link']['tables']    = array();
	$setup_info['elgg-link']['enable']    = 1;

	/* Dependencies for this app to work */
	$setup_info['elgg-link']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','0.9.15','1.0.0')
	);
?>
