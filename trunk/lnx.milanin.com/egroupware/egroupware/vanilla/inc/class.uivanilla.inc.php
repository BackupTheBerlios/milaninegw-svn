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

	class uivanilla
	{
		var $bo;
		var $template;
		var $public_functions = array(
			'info'          => True,
                        'discussions'        => True,
			'read_post'   => True,
			'reply_post'          => True,
		);

		function uivanilla()
		{
			$this->bo         = CreateObject('vanilla.bovanilla');
		}

		function display_headers($extras = '')
		{
			$GLOBALS['phpgw']->template->set_file('_header','vanilla_header.tpl');
			$GLOBALS['phpgw']->template->set_block('_header','global_header');
			//$GLOBALS['phpgw']->template->set_var('lang_inbox','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.inbox') . '">' . lang('Inbox') . '</a>');
			
			$GLOBALS['phpgw']->template->fp('app_header','global_header');

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		function set_common_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

			$GLOBALS['phpgw']->template->set_var('lang_latest_discussions',lang('Latest discussions'));
			$GLOBALS['phpgw']->template->set_var('lang_popular_discussions',lang('Most popular discussions'));
			$GLOBALS['phpgw']->template->set_var('lang_subject',lang('Subject'));
			$GLOBALS['phpgw']->template->set_var('lang_content',lang('Message'));
			$GLOBALS['phpgw']->template->set_var('lang_date',lang('Date'));
		}

		
		function info()
		{
			$total = $this->bo->total_messages();
                        
                        $this->display_headers();
                        $GLOBALS['phpgw']->template->set_file('_info','info.tpl');
                        $GLOBALS['phpgw']->template->set_block('_info','discussions');
			$GLOBALS['phpgw']->template->set_block('_info','latest_discussion');
			$GLOBALS['phpgw']->template->set_block('_info','popular_discussion');
			$this->set_common_langs();
			
                        $row_class='row_on';
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

			


                        $extra_menuaction = '&menuaction=vanilla.uivanilla.info';
			$GLOBALS['phpgw']->template->pfp('out','discussions');
		}

	}
