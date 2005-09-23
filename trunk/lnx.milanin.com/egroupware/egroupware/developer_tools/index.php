<?php
	/**************************************************************************\
	* eGroupWare - Developer Tools                                             *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.4 2004/01/27 15:24:42 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'developer_tools',
		'noheader'   => True,
		'nofooter'   => True
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=developer_tools.uilangfile.index');
