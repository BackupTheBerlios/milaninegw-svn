<?php
  /**************************************************************************\
  * eGroupWare - Messenger's Sidebox-Menu for idots-template                 *
  * Written by Pim Snel <pim@lingewoud.nl>                                   *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_sidebox_menu.inc.php,v 1.2 2004/01/27 20:04:36 reinerj Exp $ */
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	if($GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
		$file = Array(
			'Compose global message' => $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.compose_global')
		);

		display_sidebox($appname,$menu_title,$file);
	}
}
?>
