<?php
	/**************************************************************************\
	* eGroupWare                                                               *
	* http://www.egroupware.orgvv                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: default_records.inc.php,v 1.8 2004/01/25 21:36:17 reinerj Exp $ */

	$oProc->query("DELETE FROM phpgw_config WHERE config_app='bookmarks'");
	$oProc->query("INSERT INTO phpgw_config (config_app, config_name, config_value) VALUES ('bookmarks','mail_footer','\n\n--\nThis was sent from eGroupWare\nhttp://www.egroupware.org\n')");
?>
