<?php
  /**************************************************************************\
  * eGroupWare                                                               *
  * http://www.egroupware.org                                              *
  * Written by Joseph Engo <jengo@phpgroupware.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id: hook_admin.inc.php,v 1.10 2004/01/27 23:27:02 reinerj Exp $ */
	{
		$file = Array
		(
			'Global Categories'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicategories.index&appname=' . $appname),
			'Configure Access Permissions' => $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiacl.acllist'),
			'Configure RSS exports' => $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiexport.exportlist'),
		);
		display_section($appname,$appname,$file);
	}
?>
