<?php
	/**************************************************************************\
	* eGroupWare Wiki - UserInterface                                       *
	* http://www.egroupware.org                                                *
	* -------------------------------------------------                        *
	* Copyright (C) 2004 RalfBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.6 2004/04/12 13:02:05 ralfbecker Exp $ */

$GLOBALS['phpgw_info']['flags'] = array(
	'currentapp' => 'wiki',
	'noheader'   => True
);

// the phpGW header.inc.php got included later by lib/init.php
require('lib/main.php');

?>
