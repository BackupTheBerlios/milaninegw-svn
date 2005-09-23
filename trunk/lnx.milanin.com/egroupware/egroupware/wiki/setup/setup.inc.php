<?php
	/**************************************************************************\
	* eGroupWare - Setup                                                       *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: setup.inc.php,v 1.7.2.1 2004/07/31 14:06:03 ralfbecker Exp $ */

	$setup_info['wiki']['name']      = 'wiki';
	$setup_info['wiki']['title']     = 'Wiki';
	$setup_info['wiki']['version']   = '1.0.0.001';
	$setup_info['wiki']['app_order'] = 11;
	$setup_info['wiki']['enable']    = 1;

	$setup_info['wiki']['author']    = 'Tavi Team';
	$setup_info['wiki']['license']   = 'GPL';
	$setup_info['wiki']['description'] =
		'Wiki is a modified and enhanced version of <a href="http://tavi.sf.net" target="_new">WikkiTikkiTavi</a> for use with eGroupware.';
	$setup_info['wiki']['maintainer'] = 'Ralf Becker';
	$setup_info['wiki']['maintainer_email'] = 'RalfBecker@outdoor-training.de';

	$setup_info['wiki']['tables'][] = 'phpgw_wiki_links';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_pages';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_rate';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_interwiki';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_sisterwiki';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_remote_pages';
	
	/* The hooks this app includes, needed for hooks registration */
	$setup_info['wiki']['hooks'] = array(
		'admin',
		'sidebox_menu',
		'manual'
	);

	/* Dependencies for this app to work */
	$setup_info['wiki']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14', '0.9.15','1.0.0')
	);
	$setup_info['wiki']['depends'][] = array
	(
		'appname'  => 'etemplate',
		'versions' => Array('0.9.14', '0.9.15','1.0.0')
	);







