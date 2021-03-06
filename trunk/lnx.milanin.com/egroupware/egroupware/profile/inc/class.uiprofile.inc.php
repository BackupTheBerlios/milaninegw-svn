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
                var $percentage;
		var $public_functions = array(
			'info'          => True,
          		'stats'         => True,
          		'links'         => True,
		);

		function uiprofile()
		{
			$this->bo         = CreateObject('profile.boprofile');
                        $this->percentage = $this->bo->relative_percentage;
		}

		
		function set_common_langs($hooked=False)
		{
                  $GLOBALS['phpgw']->template->set_var('lang_my_profile',
          						($hooked ?lang( 'My profile')
              							 :lang('Status'))
            					      );
                  $GLOBALS['phpgw']->template->set_var('lang_my_profile_stats',lang('My Statistics'));
                  $GLOBALS['phpgw']->template->set_var('lang_views_by_members',lang('views by members'));
                  $GLOBALS['phpgw']->template->set_var('lang_views_by_guests',lang('views by guests'));
                  $GLOBALS['phpgw']->template->set_var('lang_in_last_days',lang('in last %1 days',14));
                  $GLOBALS['phpgw']->template->set_var('lang_guest_from',lang('referred by'));
                  $GLOBALS['phpgw']->template->set_var('lang_guest_last_date',lang('last time'));
                  $GLOBALS['phpgw']->template->set_var('lang_guest_counter',lang('counter'));
                  $GLOBALS['phpgw']->template->set_var('lang_my_profile_actions',lang('Actions'));
		}

		function stats()
                {
                	$GLOBALS['phpgw']->template->set_file('_stats','stats.tpl');
                        $GLOBALS['phpgw']->template->set_block('_stats','stats');
                        $GLOBALS['phpgw']->template->set_block('_stats','member_view');
                        $GLOBALS['phpgw']->template->set_block('_stats','guest_view');
                        $this->set_common_langs();
                        $row_class='row_on';
						if(is_array($this->bo->members_views))
                        foreach ($this->bo->members_views as $v){
                          $GLOBALS['phpgw']->template->set_var('member_icon',$v['icon']);
                          $GLOBALS['phpgw']->template->set_var('member_name',$v['name']);
                          $GLOBALS['phpgw']->template->set_var('member_date',$v['date']);
                          $GLOBALS['phpgw']->template->set_var('row_class',$row_class);
                          $GLOBALS['phpgw']->template->fp('members_views','member_view',TRUE);
                          $row_class= ($row_class=='row_on') ? 'row_off' : 'row_on';
                        }
			$row_class='row_on';
			if(is_array($this->bo->guests_views))
			foreach ($this->bo->guests_views as $v){
                          $GLOBALS['phpgw']->template->set_var('guest_from',$v['referral']);
                          $GLOBALS['phpgw']->template->set_var('guest_last_date',$v['date']);
                          $GLOBALS['phpgw']->template->set_var('guest_counter',$v['counter']);
                          $GLOBALS['phpgw']->template->set_var('row_class',$row_class);
                          $GLOBALS['phpgw']->template->fp('guests_views','guest_view',TRUE);
                          $row_class= ($row_class=='row_on') ? 'row_off' : 'row_on';
                        }
                        $GLOBALS['phpgw']->template->pfp('out','stats');
                }
		function info($hooked=False)
		{
            		if ($hooked) {
                          $GLOBALS['phpgw']->template->set_root('profile/templates/default');
                        }
                        $GLOBALS['phpgw']->template->set_file('_info','info.tpl');
                        $GLOBALS['phpgw']->template->set_block('_info','info');
			$this->set_common_langs($hooked);
                        if ($hooked){
                          $GLOBALS['phpgw']->template->set_var('edit_link','<a href="/members/profile/edit.php">'.lang("Edit Profile").'</a>');
                          $GLOBALS['phpgw']->template->set_var('show_link','<a href="/members/profile/index.php">'.lang("View Profile").'</a>');
                        }
			$GLOBALS['phpgw']->template->set_var('relative_percentage',$this->bo->get_relative_percentage());
                        return $GLOBALS['phpgw']->template->pfp('out','info');
		}
                function links()
                {
                  $GLOBALS['phpgw']->template->set_file('_links','links.tpl');
                  $GLOBALS['phpgw']->template->set_block('_links','links');
                  $this->set_common_langs($hooked);
                  $GLOBALS['phpgw']->template->set_var('edit_profile_link','<a href="/members/profile/edit.php">'.lang("Edit Profile").'</a>');
                  $GLOBALS['phpgw']->template->set_var('show_profile_link','<a href="/members/profile/index.php">'.lang("View Profile").'</a>');
                  
                  $GLOBALS['phpgw']->template->set_var('edit_homepage_link','<a href="/members/_home/edit.php">'.lang("Edit Homepage").'</a>');
                  $GLOBALS['phpgw']->template->set_var('show_homepage_link','<a href="/members/_home/">'.lang("View Homepage").'</a>');
                  
                  $GLOBALS['phpgw']->template->set_var('edit_weblog_link','<a href="/members/_weblog/edit.php">'.lang("Edit Weblog").'</a>');
                  $GLOBALS['phpgw']->template->set_var('show_weblog_link','<a href="/members/_weblog/">'.lang("View Weblog").'</a>');
                  
                  return $GLOBALS['phpgw']->template->pfp('out','links');
                }

	}
