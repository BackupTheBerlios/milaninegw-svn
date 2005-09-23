<?php
	/**************************************************************************\
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	// $Id: hook_admin.inc.php,v 1.2 2004/01/27 16:58:16 reinerj Exp $
	// $Source: /cvsroot/egroupware/etemplate/doc/et_notes/inc/hook_admin.inc.php,v $

	{
		$values = array
		(
			'Global Categories' => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicategories.index&appname=' . $appname . '&global_cats=True')
		);

		display_section($appname,$appname,$values);
	}
?>
