<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
	Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

	phpGroupWare - http://www.phpgroupware.org

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


	// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = Array(
		'Global Configuration' => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
		'Add Site' => $GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.add_edit_site'),
		'Browses Through Sites' => $GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.browse_phpgw_jinn_sites'),
		'Import Site' => $GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.import_phpgw_jinn_site'),
		'Access Rights' => $GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.access_rights')
	);
	//Do not modify below this line
	display_section($appname,$title,$file);
?>
