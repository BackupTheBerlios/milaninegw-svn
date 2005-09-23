<?php
	/**************************************************************************\
	* eGroupWare - Preferences                                                 *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_settings.inc.php,v 1.3 2004/01/27 18:35:52 reinerj Exp $ */

	$GLOBALS['phpgw']->db->query('SELECT con,display FROM phpgw_headlines_sites ORDER BY display asc',__LINE__,__FILE__);
	while ($GLOBALS['phpgw']->db->next_record())
	{
		$_headlines[$GLOBALS['phpgw']->db->f('con')] = $GLOBALS['phpgw']->db->f('display');
	}

	create_select_box('Select Headline News sites','headlines',$_headlines,(count($_headlines)>10?10:count($_headlines)));
?>
