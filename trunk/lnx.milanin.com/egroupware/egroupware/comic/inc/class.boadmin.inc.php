<?php

/*************************************************************************\
* Daily Comics (eGroupWare application)                                   *
* http://www.egroupware.org                                               *
* This file is written by: Sam Wynn <neotexan@wynnsite.com>               *
*                          Rick Bakker <r.bakker@linvision.com>           *
* --------------------------------------------                            *
* This program is free software; you can redistribute it and/or modify it *
* under the terms of the GNU General Public License as published by the   *
* Free Software Foundation; either version 2 of the License, or (at your  *
* option) any later version.                                              *
\*************************************************************************/

/* $Id: class.boadmin.inc.php,v 1.2 2004/01/27 15:19:10 reinerj Exp $ */

class boadmin
{
	var $so;

	function boadmin()
	{
		$this->so = CreateObject('comic.soadmin');
	}

	function admin_global_options_data()
	{
		$field = $this->so->admin_global_options_data();

		return ($field);
	}

	function update_global_options($field)
	{
		$this->so->update_global_options($field);

		return (lang("Global Options Updated"));
	}
}

?>
