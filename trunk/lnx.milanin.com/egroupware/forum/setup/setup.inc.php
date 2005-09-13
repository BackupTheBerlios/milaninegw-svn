<?php
	/****************************************************************************\
	* eGroupWare - Forums                                                        *
	* http://www.egroupware.org                                                  *
	* Written by Jani Hirvinen <jpkh@shadownet.com>                              *
	* -------------------------------------------                                *
	*  This program is free software; you	can redistribute it and/or modify it *
	*  under the terms of	the GNU	General	Public License as published by the   *
	*  Free Software Foundation; either version 2	of the License, or (at your  *
	*  option) any later version.                                                *
	\****************************************************************************/

	/* $Id: setup.inc.php,v 1.22.2.1 2004/09/10 15:46:33 alpeb Exp $ */

	$setup_info['forum']['name'] = 'forum';
	$setup_info['forum']['title'] = 'forum';
	$setup_info['forum']['version'] = '1.0.0';
	$setup_info['forum']['app_order'] = 7;
	$setup_info['forum']['enable'] = 1;

	$setup_info['forum']['author'] = 'Joseph Engo';
	$setup_info['forum']['license']  = 'GPL';
	$setup_info['forum']['description'] =
		'Subject matter message board.';
	$setup_info['forum']['maintainer'] = array(
		'name' => 'eGroupWare coreteam',
		'email' => 'egroupware-developers@lists.sourceforge.net'
	);

	/* the table info */
	$setup_info['forum']['tables'] = array(
		'phpgw_forum_body',
		'phpgw_forum_categories',
		'phpgw_forum_forums',
		'phpgw_forum_threads'
	);

	/* the hooks */
	$setup_info['forum']['hooks'][] = 'admin';
	$setup_info['forum']['hooks'][] = 'settings';
	$setup_info['forum']['hooks'][] = 'preferences';
	$setup_info['forum']['hooks'][] = 'sidebox_menu';

	/* the dependencies */
	$setup_info['forum']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.14','0.9.15','1.0.0')
	);
?>
