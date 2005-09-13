<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
	Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

	eGroupWare - http://www.egroupware.org

	This file is part of JiNN

	JiNN is free software; you can redistribute it and/or modify it under
	the terms of the GNU General Public License as published by the Free
	Software Foundation; either version 2 of the License, or (at your 
	option) any later version.

	JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or 
	FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License 
	along with JiNN; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
	*/

	$phpgw_flags = Array(
		'currentapp'	=>	'jinn',
		'noheader'	=>	True,
		'nonavbar'	=>	True,
		'noappheader'	=>	True,
		'noappfooter'	=>	True,
		'nofooter'	=>	True
	);

	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;

	include('../header.inc.php');

//	Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiuser.index'));



//die();
//echo urlencode($GLOBALS[hidden_val]);
//die();
Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.plug_config&plug_orig='.$GLOBALS[HTTP_GET_VARS][plug_orig].'&plug_name='.$GLOBALS[HTTP_GET_VARS][plug_name].'&hidden_name='.$GLOBALS[HTTP_GET_VARS][hidden_name].'&hidden_val='.$GLOBALS[HTTP_GET_VARS][hidden_val]));

	$GLOBALS['phpgw']->common->phpgw_exit();

?>
