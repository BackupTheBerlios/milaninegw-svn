<?php
	/**************************************************************************\
	* eGroupWare - Messenger                                                   *
	* http://www.egroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.4 2004/01/27 20:04:35 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'profile',
		'noheader'   => False,
		'nonavbar'   => False
	);
	include('../header.inc.php');

	$obj = createobject('profile.uiprofile');
	$obj->info();

	$GLOBALS['phpgw']->common->phpgw_footer();
