<?php
	/**************************************************************************\
	* eGroupWare - Webpage news admin                                          *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	* --------------------------------------------                             *
	* This program was sponsered by Golden Glair productions                   *
	* http://www.goldenglair.com                                               *
	\**************************************************************************/

	/* $Id: index.php,v 1.12 2004/01/27 23:27:02 reinerj Exp $ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'news_admin',
		'noheader' => True,
		'nonavbar' => True,
	);
	include('../header.inc.php');

	$ui = CreateObject('news_admin.uinews');
	$ui->read_news();

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
