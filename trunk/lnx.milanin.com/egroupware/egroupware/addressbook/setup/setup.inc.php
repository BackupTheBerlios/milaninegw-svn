<?php
	/**************************************************************************\
	* eGroupWare - Addressbook                                                 *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.36.2.1 2004/08/02 14:05:26 ralfbecker Exp $ */

	/* Basic information about this app */
	$setup_info['addressbook']['name']      = 'addressbook';
	$setup_info['addressbook']['title']     = 'Addressbook';
	$setup_info['addressbook']['version']   = '1.0.0';
	$setup_info['addressbook']['app_order'] = 4;
	$setup_info['addressbook']['enable']    = 1;

	$setup_info['addressbook']['author'] = 'Joseph Engo, Miles Lott';
	$setup_info['addressbook']['note']   = 'The phpgwapi manages contact data.  Addressbook manages servers for its remote capability.';
	$setup_info['addressbook']['license']  = 'GPL';
	$setup_info['addressbook']['description'] =
		'Contact manager with Vcard support.<br>
		 Always have your address book available for updates or look ups from anywhere. <br>
		 Share address book contact information with others. <br>
		 Link contacts to calendar events or InfoLog entires like phonecalls.<br> 
		 Addressbook is the eGroupWare default contact application. <br>
		 It makes use of the eGroupWare contacts class to store and retrieve 
		 contact information via SQL or LDAP.';

	$setup_info['addressbook']['maintainer'] = 'eGroupWare coreteam';
	$setup_info['addressbook']['maintainer_email'] = 'egroupware-developers@lists.sourceforge.net';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['addressbook']['hooks'][] = 'admin';
	$setup_info['addressbook']['hooks'][] = 'add_def_pref';
	$setup_info['addressbook']['hooks'][] = 'config_validate';
	$setup_info['addressbook']['hooks'][] = 'home';
	$setup_info['addressbook']['hooks'][] = 'addaccount';
	$setup_info['addressbook']['hooks'][] = 'editaccount';
	$setup_info['addressbook']['hooks'][] = 'deleteaccount';
	$setup_info['addressbook']['hooks'][] = 'notifywindow';
	$setup_info['addressbook']['hooks'][] = 'sidebox_menu';
	$setup_info['addressbook']['hooks'][] = 'preferences';

	/* Dependencies for this app to work */
	$setup_info['addressbook']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.14','0.9.15','1.0.0')
	);
?>
