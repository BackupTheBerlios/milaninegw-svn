<?php
	/**************************************************************************\
	* Anglemail - setup files for eGroupWare                                   *
	* http://www.anglemail.org                                                 *
	* eGroupWare - Email                                                       *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.29 2004/07/02 22:30:45 ralfbecker Exp $ */

	$setup_info['email']['name']      = 'email';
	$setup_info['email']['title']     = 'Email';
	$setup_info['email']['version']   = '1.0.0';
	$setup_info['email']['app_order'] = '2';
	$setup_info['email']['enable']    = 1;
	$setup_info['email']['tables']    = array('phpgw_anglemail');

	$setup_info['email']['author'] = '&quot;Angles&quot; Angelo Tony Puglisi';
	$setup_info['email']['license']  = 'GPL';
	$setup_info['email']['description'] =
		'AngleMail for eGroupWare at www.anglemail.org is an Email reader with multiple accounts and mailbox filtering.';
	$setup_info['email']['maintainer'] = '&quot;Angles&quot; Angelo Tony Puglisi';
	$setup_info['email']['maintainer_email'] = 'angles@aminvestments.com';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['email']['hooks'][] = 'admin';
	$setup_info['email']['hooks'][] = 'email_add_def_prefs';
	$setup_info['email']['hooks'][] = 'home';
	$setup_info['email']['hooks'][] = 'notifywindow';
	$setup_info['email']['hooks'][] = 'notifywindow_simple';
	$setup_info['email']['hooks'][] = 'add_def_prefs';
	$setup_info['email']['hooks'][] = 'preferences';
	$setup_info['email']['hooks'][] = 'sidebox_menu';

	/* Dependencies for this app to work */
	$setup_info['email']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.13','0.9.14','0.9.15','1.0.0')
	);
?>
