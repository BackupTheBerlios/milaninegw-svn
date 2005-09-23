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
	
	// $Id: hook_preferences.inc.php,v 1.8 2004/01/27 19:09:35 reinerj Exp $
	// $Source: /cvsroot/egroupware/tts/inc/hook_preferences.inc.php,v $

	$values = array(
		'Preferences'		=> $GLOBALS['phpgw']->link('/preferences/preferences.php','appname=tts'),
		'Edit Categories'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app='.$appname.'&cats_level=True&global_cats=True')
	);
	display_section('tts','Trouble Ticket System',$values);
?>
