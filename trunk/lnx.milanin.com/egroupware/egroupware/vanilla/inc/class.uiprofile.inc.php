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

	/* $Id: class.uimessenger.inc.php,v 1.14.2.3 2005/01/28 08:01:46 dawnlinux Exp $ */

	class uiprofile
	{
		var $bo;
		var $template;
		var $public_functions = array(
			'info'          => True,
		);

		function uiprofile()
		{
			$this->bo         = CreateObject('profile.boprofile');
		}

		
		function set_common_langs()
		{
			$GLOBALS['phpgw']->template->set_var('lang_my_profile',lang('My Profile'));
		}

		
		function info()
		{
                        $GLOBALS['phpgw']->template->set_file('_info','info.tpl');
                        $GLOBALS['phpgw']->template->set_block('_info','info');
			$this->set_common_langs();
			$GLOBALS['phpgw']->template->set_var('edit_link','<a href="/members/profile/edit.php">'.lang("Edit Profile").'</a>');
                        $GLOBALS['phpgw']->template->set_var('show_link','<a href="/members/profile/index.php">'.lang("View Profile").'</a>');
			$GLOBALS['phpgw']->template->set_var('relative_percentage',$this->bo->get_relative_percentage());
                        /*$row_class='row_on';
                        foreach ($this->bo->latest_discussions() as $discussion){
                          $GLOBALS['phpgw']->template->set_var('ld_name',sprintf("%25.25s...",$discussion['Name']));
                          $GLOBALS['phpgw']->template->set_var('last_active',$discussion['DateLastActive']);
                          $GLOBALS['phpgw']->template->set_var('row_class',$row_class);
                          $GLOBALS['phpgw']->template->fp('latest_discussions','latest_discussion',TRUE);
                          $row_class= ($row_class=='row_on') ? 'row_off' : 'row_on';
                        }
			$row_class='row_on';
			foreach ($this->bo->popular_discussions() as $discussion){
                          $GLOBALS['phpgw']->template->set_var('pd_name',sprintf("%25.25s...",$discussion['Name']));
                          $GLOBALS['phpgw']->template->set_var('comments_count',$discussion['CountComments']);
                          $GLOBALS['phpgw']->template->set_var('row_class',$row_class);
                          $GLOBALS['phpgw']->template->fp('popular_discussions','popular_discussion',TRUE);
                          $row_class= ($row_class=='row_on') ? 'row_off' : 'row_on';
                        }
*/
			


                        //$extra_menuaction = '&menuaction=vanilla.uivanilla.info';
			$GLOBALS['phpgw']->template->pfp('out','info');
		}

	}
