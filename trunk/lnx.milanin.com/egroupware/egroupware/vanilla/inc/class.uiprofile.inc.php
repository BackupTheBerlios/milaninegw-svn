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
                  $GLOBALS['phpgw']->template->set_var('lang_my_profile_stats',lang('My Statistics'));
                  $GLOBALS['phpgw']->template->set_var('lang_views_by_members',lang('views by members'));
                  $GLOBALS['phpgw']->template->set_var('lang_views_by_guests',lang('views by guests'));
                  $GLOBALS['phpgw']->template->set_var('lang_in_last_days',lang('in last %1 days',14));
                  $GLOBALS['phpgw']->template->set_var('lang_guest_from',lang('referred by'));
                  $GLOBALS['phpgw']->template->set_var('lang_guest_last_date',lang('last time'));
                  $GLOBALS['phpgw']->template->set_var('lang_guest_counter',lang('counter'));
		}

		
		function info()
		{
                        $GLOBALS['phpgw']->template->set_file('_info','info.tpl');
                        $GLOBALS['phpgw']->template->set_block('_info','info');
                        $GLOBALS['phpgw']->template->set_block('_info','stats');
                        $GLOBALS['phpgw']->template->set_block('_info','member_view');
                        $GLOBALS['phpgw']->template->set_block('_info','guest_view');
			$this->set_common_langs();
			$GLOBALS['phpgw']->template->set_var('edit_link','<a href="/members/profile/edit.php">'.lang("Edit Profile").'</a>');
                        $GLOBALS['phpgw']->template->set_var('show_link','<a href="/members/profile/index.php">'.lang("View Profile").'</a>');
			$GLOBALS['phpgw']->template->set_var('relative_percentage',$this->bo->get_relative_percentage());
                        $row_class='row_on';
                        foreach ($this->bo->members_views as $v){
                          $GLOBALS['phpgw']->template->set_var('member_icon',$v['icon']);
                          $GLOBALS['phpgw']->template->set_var('member_name',$v['name']);
                          $GLOBALS['phpgw']->template->set_var('member_date',$v['date']);
                          $GLOBALS['phpgw']->template->set_var('row_class',$row_class);
                          $GLOBALS['phpgw']->template->fp('members_views','member_view',TRUE);
                          $row_class= ($row_class=='row_on') ? 'row_off' : 'row_on';
                        }
			$row_class='row_on';
			foreach ($this->bo->guests_views as $v){
                          $GLOBALS['phpgw']->template->set_var('guest_from',$v['referral']);
                          $GLOBALS['phpgw']->template->set_var('guest_last_date',$v['date']);
                          $GLOBALS['phpgw']->template->set_var('guest_counter',$v['counter']);
                          $GLOBALS['phpgw']->template->set_var('row_class',$row_class);
                          $GLOBALS['phpgw']->template->fp('guests_views','guest_view',TRUE);
                          $row_class= ($row_class=='row_on') ? 'row_off' : 'row_on';
                        }

			


                        //$extra_menuaction = '&menuaction=vanilla.uivanilla.info';
			$GLOBALS['phpgw']->template->pfp('out','info');
                        $GLOBALS['phpgw']->template->pfp('out','stats');
		}

	}
