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

	/* $Id: hook_deleteaccount.inc.php,v 1.7 2004/01/25 22:02:59 reinerj Exp $ */

	$contacts = CreateObject('phpgwapi.contacts');

	if((int)$_POST['new_owner'] == 0)
	{
		$contacts->delete_all((int)$_POST['account_id']);
	}
	else
	{
		$contacts->change_owner((int)$_POST['account_id'],(int)$_POST['new_owner']);
	}
?>
