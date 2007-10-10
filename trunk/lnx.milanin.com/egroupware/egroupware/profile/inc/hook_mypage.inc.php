<?php
	/**************************************************************************\
	* eGroupWare - Messenger                                                   *
	* http://www.egroupware.org                                                *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_mypage.inc.php,v 1.6 2004/01/27 20:04:36 reinerj Exp $ */

	global $hooks_string;

	$lastlogin = $GLOBALS['phpgw']->session->appsession('account_previous_login','phpgwapi');
	$hooks_string['vanilla']='Wow, vanilla: ['.$lastlogin.']<br/>';
	