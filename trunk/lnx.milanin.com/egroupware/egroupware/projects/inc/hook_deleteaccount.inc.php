<?php
	/**************************************************************************\
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_deleteaccount.inc.php,v 1.10.2.1 2004/11/06 12:15:56 ralfbecker Exp $ */

	// Delete all records for a user
	$pro = CreateObject('projects.boprojects');

	if(intval($_POST['new_owner']) == 0)
	{
		$pro->delete_project(intval($_POST['account_id']),0,'account');
	}
	else
	{
		$pro->change_owner(intval($_POST['account_id']),intval($_POST['new_owner']));
	}
?>
