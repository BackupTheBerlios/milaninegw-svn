<?php
	/**************************************************************************\
	* eGroupWare - email/addressbook redirect to standard API addressbook      *
	* http://www.eGroupWare.org                                                *
	* -----------------------------------------------                          *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	
	/* $Id: addressbook.php,v 1.43 2003/11/02 19:12:10 ralfbecker Exp $ */

	header('Location: ../phpgwapi/addressbook.php?'.$_SERVER['QUERY_STRING']);
