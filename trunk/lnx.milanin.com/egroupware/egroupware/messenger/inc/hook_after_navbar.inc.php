<?php
	/**************************************************************************\
	* eGroupWare - Messenger                                                   *
	* http://www.egroupware.org                                                *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_after_navbar.inc.php,v 1.6 2004/01/27 20:04:36 reinerj Exp $ */

	if($GLOBALS['phpgw_info']['flags']['currentapp'] != 'messenger' &&
		$GLOBALS['phpgw_info']['flags']['currentapp'] != 'welcome')
	{
		$GLOBALS['phpgw']->db->query("select count(*) from phpgw_messenger_messages where message_owner='"
			. $GLOBALS['phpgw_info']['user']['account_id'] . "' and message_status='N'",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->next_record();

		if($GLOBALS['phpgw']->db->f(0))
		{
			echo '<center><a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.inbox')
				. '">' . lang('You have %1 new message' . ($GLOBALS['phpgw']->db->f(0)>1?'s':''),$GLOBALS['phpgw']->db->f(0)) . '</a>'
				. '</center>';
		}
	}
