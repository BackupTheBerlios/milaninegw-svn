<?php
	/**************************************************************************\
	* eGroupWare - infolog                                                     *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.33.2.1 2004/08/28 14:36:26 ralfbecker Exp $ */

	$setup_info['infolog']['name']      = 'infolog';
	$setup_info['infolog']['version']   = '1.0.0.001';
	$setup_info['infolog']['app_order'] = 5;
	$setup_info['infolog']['tables']    = array('phpgw_infolog','phpgw_links','phpgw_infolog_extra');
	$setup_info['infolog']['enable']    = 1;

	$setup_info['infolog']['author'] = 
 	$setup_info['infolog']['maintainer'] = array(
		'name'  => 'Ralf Becker',
		'email' => 'ralfbecker@outdoor-training.de'
	);
	$setup_info['infolog']['license']  = 'GPL';
	$setup_info['infolog']['description'] =
		'<p><b>CRM</b> (customer-relation-management) type app using Addressbook providing
		Todo List, Notes and Phonelog. <b>InfoLog</b> is orininaly based on eGroupWare\'s
		old ToDo-List and has the features of all 3 mentioned applications plus fully working ACL
		(including Add+Private attributes, add for to addreplys/subtasks).</p>
		<p>Responsibility for a task (ToDo) or a phonecall can be <b>delegated</b> to an other
		user. All entries can be linked to addressbook entries, projects and/or calendar events.
		This allows you to <b>log all activity of a contact</b>/address or project.
		The entries may be viewed or added from InfoLog direct or from within
		the contact/address, project or calendar view.</p>
		<p>Other documents / files can be linked to InfoLog entries and are store in the VFS
		(eGroupWare\'s virtual file system). An extension of the VFS allows to symlink
		the files to a fileserver, instead of placeing a copy in the VFS
		(<i>need to be configured in the admin-section</i>).
		It is planed to include emails and faxes into InfoLog in the future.</p>';
	$setup_info['infolog']['note'] =
		'<p>Their is a <b>CSV import filter</b> (in the admin-section) to import existing data.
		It allows to interactivly assign fields, customize the values with regular
		expressions and direct calls to php-functions (e.g. to link the phone calls 
		(again) to the addressbook entrys).</p>
		<p><b>More information</b> about InfoLog and the current development-status can be found on the
		<a href="http://www.egroupware.org/infolog" target="_blank">InfoLog page on our Website</a>.</p>';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['infolog']['hooks']['preferences'] = 'infolog.admin_prefs_sidebox_hooks.all_hooks';
	$setup_info['infolog']['hooks'][] = 'settings';
	$setup_info['infolog']['hooks']['admin'] = 'infolog.admin_prefs_sidebox_hooks.all_hooks';
	$setup_info['infolog']['hooks'][] = 'deleteaccount';
	$setup_info['infolog']['hooks'][] = 'home';
	$setup_info['infolog']['hooks']['addressbook_view'] = 'infolog.uiinfolog.hook_view';
	$setup_info['infolog']['hooks']['projects_view']    = 'infolog.uiinfolog.hook_view';
	$setup_info['infolog']['hooks']['calendar_view']    = 'infolog.uiinfolog.hook_view';
	$setup_info['infolog']['hooks']['infolog']          = 'infolog.uiinfolog.hook_view';
	$setup_info['infolog']['hooks']['calendar_include_events'] = 'infolog.boinfolog.cal_to_include';
	$setup_info['infolog']['hooks']['calendar_include_todos']  = 'infolog.boinfolog.cal_to_include';
	$setup_info['infolog']['hooks']['sidebox_menu'] = 'infolog.admin_prefs_sidebox_hooks.all_hooks';

	/* Dependencies for this app to work */
	$setup_info['infolog']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.14','0.9.15','0.9.16','1.0.0')
	);
	$setup_info['infolog']['depends'][] = array(
		 'appname' => 'etemplate',
		 'versions' => Array('0.9.15','0.9.16','1.0.0')
	);




