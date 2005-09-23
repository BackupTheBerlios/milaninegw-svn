<?php
  /**************************************************************************\
  * eGroupWare - Headlines  Sidebox-Menu for idots-template                  *
  * http://www.egroupware.org                                                *
  * Written by Pim Snel <pim@lingewoud.nl>                                   *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_sidebox_menu.inc.php,v 1.2 2004/01/27 18:35:52 reinerj Exp $ */
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
	{
		$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'];
		$file = Array(
			'Select Headlines to Display' => $GLOBALS['phpgw']->link('/headlines/preferences.php'),
			'Select layout' => $GLOBALS['phpgw']->link('/headlines/preferences_layout.php')
		);

		if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
		{
			$file['Headline Site Management'] = $GLOBALS['phpgw']->link('/headlines/admin.php');
		}
		display_sidebox($appname,$menu_title,$file);
	}
}
?>
