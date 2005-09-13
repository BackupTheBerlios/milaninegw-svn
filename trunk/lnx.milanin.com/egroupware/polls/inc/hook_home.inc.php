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

  /* $Id: hook_home.inc.php,v 1.1 2004/01/08 18:57:51 shrykedude Exp $ */

	$hp_display = (int)$GLOBALS['phpgw_info']['user']['preferences']['polls']['homepage_display'];
	if($hp_display > 0)
	{
		$obj = CreateObject('polls.ui');
		$obj->view();
	}

?>
