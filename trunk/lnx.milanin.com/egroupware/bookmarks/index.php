<?php
	/**************************************************************************\
	* eGroupWare - Bookmarks                                                 *
	* http://www.egroupware.org                                              *
	* Based on Bookmarker Copyright (C) 1998  Padraic Renaghan                 *
	*                     http://www.renaghan.com/bookmarker                   *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.19 2004/01/25 21:32:43 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'bookmarks',
		'nonavbar'   => True,
		'noheader' => True
	);
	include('../header.inc.php');

	$obj = createobject('bookmarks.ui');
	$obj->init();
	$GLOBALS['phpgw']->common->phpgw_footer();

?>
