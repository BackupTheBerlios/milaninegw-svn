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

	// $Id: hook_add_def_pref.inc.php,v 1.4 2004/02/22 14:32:55 milosch Exp $

	$GLOBALS['pref']->change('addressbook','company','addressbook_True');
	$GLOBALS['pref']->change('addressbook','lastname','addressbook_True');
	$GLOBALS['pref']->change('addressbook','firstname','addressbook_True');
	$GLOBALS['pref']->change('addressbook','default_category','');
?>
