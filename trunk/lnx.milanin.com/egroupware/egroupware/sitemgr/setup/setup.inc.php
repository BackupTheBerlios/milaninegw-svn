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

	/* $Id: setup.inc.php,v 1.24 2004/07/02 22:25:26 ralfbecker Exp $ */

	$setup_info['sitemgr']['name']      = 'sitemgr';
	$setup_info['sitemgr']['title']     = 'SiteMgr Web Content Management';
	$setup_info['sitemgr']['version']   = '1.0.0';
	$setup_info['sitemgr']['app_order'] = 14;
	$setup_info['sitemgr']['tables']    = array('phpgw_sitemgr_pages','phpgw_sitemgr_pages_lang','phpgw_sitemgr_categories_state','phpgw_sitemgr_categories_lang','phpgw_sitemgr_modules','phpgw_sitemgr_blocks','phpgw_sitemgr_blocks_lang','phpgw_sitemgr_content','phpgw_sitemgr_content_lang','phpgw_sitemgr_active_modules','phpgw_sitemgr_properties','phpgw_sitemgr_sites');
	$setup_info['sitemgr']['enable']    = 1;
	$setup_info['sitemgr']['author'] = 'Michael Totschnig and others';
	$setup_info['sitemgr']['maintainer'] = array(
		'name'  => 'Ralf Becker',
		'email' => 'ralfbecker@outdoor-training.de'
	);
	$setup_info['sitemgr']['license']  = 'GPL';
	$setup_info['sitemgr']['description'] = nl2br(
'This program will generate a dynamic web site with discrete sections that various eGroupWare users may edit, if the administrator gives them permission to do so.  In effect, the generated website can have sections which independent departments are in charge of maintaining.  The site administrator can choose a theme and create headers, footers, and sidebars to enforce a sitewide look and feel.  Site sections can be viewable public (viewable by anonymous users) or private (viewable by specified users and groups only).

<b>Former Contributors and Maintainers</b>
Michael Totschnig (totschnig.michael -AT- uqam.ca)
wrote multilingual facets of sitemgr, and conceived the modularized architecture

Team 10 in the UC Irvine Systems Design Course, ICS 125, chose this as their project.  Seek3r served as the project\'s "customer" and the team wrote extensive requirements and design documents followed by the actual coding of the project.  The course is ten weeks long, but coding doesn\'t start until part-way through week 6, so version 1.0 of sitemgr was programmed in an intensive 3 weeks.

<u>Credits</u>
ICS 125 Team 10:

Tina Alinaghian (tina -AT- checkyour6.net)
Austin Lee (anhjah -AT- hotmail.com)
Siu Leung (rurouni_master -AT- hotmail.com)
Fang Ming Lo (flo -AT- uci.edu)
Patrick Walsh (mr_e -AT- phpgroupware.org)

Professor:
Hadar Ziv (profziv -AT- aol.com)

TA:
Arijit Ghosh (arijitg -AT- uci.edu)');
	$setup_info['sitemgr']['note'] = '';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['sitemgr']['hooks'][] = 'preferences';
	$setup_info['sitemgr']['hooks'][] = 'about';
	$setup_info['sitemgr']['hooks'][] = 'admin';
	$setup_info['sitemgr']['hooks'][] = 'sidebox_menu';

	/* Dependencies for this app to work */
	$setup_info['sitemgr']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','0.9.15','1.0.0')
	);












