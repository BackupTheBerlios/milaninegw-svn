<?php
	/**************************************************************************\
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* Written by Dan Kuykendall <seek3r@phpgroupware.org>                      *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: anon_wrapper.php,v 1.6 2004/01/26 23:26:19 reinerj Exp $ */

	// TODO:
	// Limit which users can access this program (ACL check)
	// Global disabler
	// Detect bad logins and passwords, spit out generic message

	// If your are going to use multiable accounts, remove the following lines
	$login  = 'anonymous';
	$passwd = 'anonymous';

	$GLOBALS['phpgw_info']['flags'] = array(
		'disable_Template_class' => True,
		'login' => True,
		'currentapp' => 'login',
		'noheader'  => True
	);
	include('./header.inc.php');

	// If your are going to use multiable accounts, remove the following lines 
	// You must create the useraccount and check its permissions before use 

	$login  = 'anonymous'; 
	$passwd = 'anonymous'; 

	$sessionid = $GLOBALS['phpgw']->session->create($login,$passwd,'text');
	$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php'));
?>
