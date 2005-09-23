<?php
	/**************************************************************************\
	* eGroupWare - phpgwapi setup                                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	// $Id: setup.inc.php,v 1.81.2.6 2005/04/15 16:33:27 ralfbecker Exp $

	/* Basic information about this app */
	$setup_info['phpgwapi']['name']      = 'phpgwapi';
	$setup_info['phpgwapi']['title']     = 'phpgwapi';
	$setup_info['phpgwapi']['version']   = '1.0.0.007';
	$setup_info['phpgwapi']['versions']['current_header'] = '1.27';
	$setup_info['phpgwapi']['enable']    = 3;
	$setup_info['phpgwapi']['app_order'] = 1;

	/* The tables this app creates */
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_config';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_applications';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_acl';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_accounts';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_preferences';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_sessions';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_app_sessions';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_access_log';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_hooks';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_languages';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_lang';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_nextid';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_categories';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_addressbook';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_addressbook_extra';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_log';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_log_msg';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_interserv';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_vfs';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_history_log';
	$setup_info['phpgwapi']['tables'][]  = 'phpgw_async';

	/* Basic information about this app */
	$setup_info['notifywindow']['name']      = 'notifywindow';
	$setup_info['notifywindow']['title']     = 'Notify Window';
	$setup_info['notifywindow']['version']   = '1.0.0';
	$setup_info['notifywindow']['enable']    = 2;
	$setup_info['notifywindow']['app_order'] = 1;
	$setup_info['notifywindow']['tables']    = '';
	$setup_info['notifywindow']['hooks'][]   = 'home';






