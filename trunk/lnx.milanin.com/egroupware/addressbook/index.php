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

	/* $Id: index.php,v 1.125 2004/01/25 21:49:43 reinerj Exp $ */

	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'addressbook',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$obj = CreateObject('addressbook.uiaddressbook');
	$obj->index();

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
