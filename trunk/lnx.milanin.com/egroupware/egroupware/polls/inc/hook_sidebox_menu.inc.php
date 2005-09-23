<?php
  /**************************************************************************\
  * eGroupWare - Polls                                                       *
  * http://www.egroupware.org                                                *
  * Copyright (c) 1999 Till Gerken (tig@skv.org)                             *
  * Modified by Greg Haygood (shrykedude@bellsouth.net)                      *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_sidebox_menu.inc.php,v 1.3.2.1 2004/07/30 01:44:09 shrykedude Exp $ */
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	$menu_title = 'Polls Menu';
	$file = Array(
		'Current Poll'
			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'polls.ui.index')),
		'View Results' 
			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'polls.ui.vote','show_results'=>$GLOBALS['poll_settings']['currentpoll']))
	);
	display_sidebox($appname,$menu_title,$file);

/*
	$menu_title = 'Preferences';
	$file = Array(

	);
	display_sidebox($appname,$menu_title,$file);
*/

	if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
        $menu_title = 'Administration';
        $file = Array(
				'Poll Settings'
					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'polls.ui.admin','action'=>'settings')),
                'Show Questions'
					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'polls.ui.admin','action'=>'show','type'=>'question')),
                'Add Questions'
					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'polls.ui.admin','action'=>'add','type'=>'question')),
                'Add Answers'
					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'polls.ui.admin','action'=>'add','type'=>'answer')),

        );

		display_sidebox($appname,$menu_title,$file);
	}
}
?>
