<?php
  /**************************************************************************\
  * eGroupWare - Calendar - Planner                                          *
  * http://www.egroupware.org                                                *
  * Based on Webcalendar by Craig Knudsen <cknudsen@radix.net>               *
  *          http://www.radix.net/~cknudsen                                  *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_home_planner.inc.php,v 1.3 2004/01/27 00:29:26 reinerj Exp $ */

	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo 'Failed attempt to break in via an old Security Hole!<br>'."\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	$GLOBALS['extra_data'] = '<table width="100%" cellpadding="0"><tr><td bgcolor="white">'.
		ExecMethod('calendar.uicalendar.planner').'</td></tr></table>';
?>
