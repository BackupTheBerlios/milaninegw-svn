<?php
	/**************************************************************************\
	* EGroupWare - FeLaMiMail                                                  *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.18 2004/07/02 22:31:39 ralfbecker Exp $ */

	$setup_info['felamimail']['name']      		= 'felamimail';
	$setup_info['felamimail']['title']     		= 'FeLaMiMail';
	$setup_info['felamimail']['version']		= '1.0.0';
	$setup_info['felamimail']['app_order'] 		= 2;
	$setup_info['felamimail']['enable']    		= 1;

	$setup_info['felamimail']['author']		= 'Lars Kneschke';
	$setup_info['felamimail']['license']		= 'GPL';
	$setup_info['felamimail']['description']	=
		'IMAP emailclient for EGroupware';
	#$setup_info['felamimail']['based_on']		=
	#	'This port is based on Squirrelmail, which is a standalone IMAP client.';
	#$setup_info['felamimail']['based_on_url']	= 'http://www.squirrelmail.org';
	$setup_info['felamimail']['maintainer'] 	= 'Lars Kneschke';
	$setup_info['felamimail']['maintainer_email'] 	= 'lkneschke@linux-at-work.de';

	$setup_info['felamimail']['tables']    = array('phpgw_felamimail_cache','phpgw_felamimail_folderstatus','phpgw_felamimail_displayfilter');

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['felamimail']['hooks'][] = 'preferences';
	$setup_info['felamimail']['hooks'][] = 'admin';
	$setup_info['felamimail']['hooks'][] = 'settings';
	$setup_info['felamimail']['hooks'][] = 'home';
	$setup_info['felamimail']['hooks'][] = 'sidebox_menu';
	$setup_info['felamimail']['hooks'][] = 'notifywindow';
	$setup_info['felamimail']['hooks']['addaccount']	= 'felamimail.bofelamimail.addAccount';
	$setup_info['felamimail']['hooks']['deleteaccount']	= 'felamimail.bofelamimail.deleteAccount';
	$setup_info['felamimail']['hooks']['editaccount']	= 'felamimail.bofelamimail.updateAccount';
	$setup_info['felamimail']['hooks']['edit_user']		= 'felamimail.bofelamimail.adminMenu';

	/* Dependacies for this app to work */
	$setup_info['felamimail']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','0.9.15','1.0.0')
	);
	$setup_info['felamimail']['depends'][] = array(
		'appname'  => 'emailadmin',
		'versions' => Array('0.0.008','1.0.0')
	);
?>
