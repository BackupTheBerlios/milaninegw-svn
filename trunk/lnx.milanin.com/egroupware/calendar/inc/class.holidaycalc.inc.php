<?php 
  /**************************************************************************\
  * eGroupWare - holidaycalc                                                 *
  * http://www.egroupware.org                                                *
  * Based on Yoshihiro Kamimura <your@itheart.com>                           *
  *          http://www.itheart.com                                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: class.holidaycalc.inc.php,v 1.6 2004/01/27 00:29:26 reinerj Exp $ */

	if (empty($GLOBALS['phpgw_info']['user']['preferences']['common']['country']))
	{
		$rule = 'US';
	}
	else
	{
		$rule = $GLOBALS['phpgw_info']['user']['preferences']['common']['country'];
	}

	$calc_include = PHPGW_INCLUDE_ROOT.'/calendar/inc/class.holidaycalc_'.$rule.'.inc.php';
	if(@file_exists($calc_include))
	{
		include($calc_include);
	}
	else
	{
		include(PHPGW_INCLUDE_ROOT.'/calendar/inc/class.holidaycalc_US.inc.php');
	}
?>
