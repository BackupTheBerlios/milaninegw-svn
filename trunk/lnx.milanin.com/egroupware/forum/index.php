<?php
	/*****************************************************************************\
	* phpGroupWare - Forums                                                       *
	* http://www.phpgroupware.org                                                 *
	* Written by Jani Hirvinen <jpkh@shadownet.com>                               *
	* -------------------------------------------                                 *
	*  This program is free software; you	can redistribute it and/or modify it   *
	*  under the terms of	the GNU	General	Public License as published by the  *
	*  Free Software Foundation; either version 2	of the License,	or (at your *
	*  option) any later version.                                                 *
	\*****************************************************************************/

	/* $Id: index.php,v 1.26 2002/01/20 03:37:07 skeeter Exp $ */

	$phpgw_flags = Array(
		'currentapp'	=>	'forum',
		'noheader'	=>	True,
		'nonavbar'	=>	True,
		'noappheader'	=>	True,
		'noappfooter'	=>	True,
		'nofooter'	=>	True
	);

	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;
	
	include('../header.inc.php');

	Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiforum.index'));
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
