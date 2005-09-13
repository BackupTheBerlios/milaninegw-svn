<?php
  /**************************************************************************\
  * Comic - eGroupWare addon application                                     *
  * http://www.egroupware.org                                                *
  * Written by Robert Schader <bobs@product-des.com>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_sidebox_menu.inc.php,v 1.2 2004/01/27 15:19:13 reinerj Exp $ */
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	$menu_title = 'Preferences';
	$file = Array(
			'My Comics' => $GLOBALS['phpgw']->link('/comic/preferences.php', "returnmain=1")
	);

	display_sidebox($appname,$menu_title,$file);

	if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
        $menu_title = 'Administration';
        $file = Array(
                'Global Options'  => $GLOBALS['phpgw']->link('/comic/admin_options.php'),
                'Global Comics'   => $GLOBALS['phpgw']->link('/comic/admin_comics.php')
        );

		display_sidebox($appname,$menu_title,$file);
	}
}
?>
