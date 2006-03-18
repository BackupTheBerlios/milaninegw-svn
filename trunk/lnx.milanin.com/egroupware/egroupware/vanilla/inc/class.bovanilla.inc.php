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

	/* $Id: class.bomessenger.inc.php,v 1.12.2.2 2004/08/18 11:56:44 reinerj Exp $ */

	class bovanilla
	{
		var $so;

		function bovanilla()
		{
			$this->so = CreateObject('vanilla.sovanilla');
		}



		function total_messages()
		{
			return $this->so->total_messages();
		}
		function latest_discussions()
                {
                  return $this->so->top_discussions();
                }
                function popular_discussions()
                {
                  return $this->so->top_discussions('t.CountComments');
                }
                function read_category_watchers()
                {
                  return $this->so->read_category_watchers();
                }
                function save_cat_watchers($cat_watchers)
                {
                  return $this->so->write_category_watchers($cat_watchers);
                }


	}
