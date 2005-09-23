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

	$setup_info['sitemgr-link']['name']      = 'sitemgr-link';
	$setup_info['sitemgr-link']['title']     = 'SiteMgr Public Web Site';
	$setup_info['sitemgr-link']['version']   = '1.0.0';
	$setup_info['sitemgr-link']['app_order'] = 9;
	$setup_info['sitemgr-link']['tables']    = array();
	$setup_info['sitemgr-link']['enable']    = 1;

	/* Dependencies for this app to work */
	$setup_info['sitemgr-link']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','0.9.15','1.0.0')
	);
	$setup_info['sitemgr-link']['depends'][] = array(
		'appname' => 'sitemgr',
		'versions' => array('1.0.0')
	);
?>
