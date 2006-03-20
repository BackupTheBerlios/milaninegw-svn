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
			'config'          => True,
			'save_config'          => True,
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
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));
			$GLOBALS['phpgw']->template->set_var('lang_remove',lang('Remove'));
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
			$GLOBALS['phpgw']->template->set_var('board_link','<a href="/vanilla">'.lang("Go to")." ".lang("Discussions").'</a>');
			$GLOBALS['phpgw']->template->set_var('config_link',
                            '<a href="'.$GLOBALS['phpgw_info']['server']['webserver_url'].'/index.php?menuaction=vanilla.uivanilla.config">'
                            .lang("Go to")." ".lang("Settings").'</a>');
			
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
		
		function config($save_result=NULL)
                {
                        $GLOBALS['phpgw']->template->set_file('_config','config.tpl');
                        $GLOBALS['phpgw']->template->set_block('_config','header');
                        $GLOBALS['phpgw']->template->set_block('_config','footer');
                        $GLOBALS['phpgw']->template->set_block('_config','cat_watcher');
                        $GLOBALS['phpgw']->template->set_block('_config','cat_watchers');
                        $GLOBALS['phpgw']->template->set_block('_config','disc_watcher');
                        $GLOBALS['phpgw']->template->set_block('_config','disc_watchers');
                        
                        $GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_watch',lang('Watch'));
			$GLOBALS['phpgw']->template->set_var('lang_cat_watchers',lang('Categories watching'));
			$GLOBALS['phpgw']->template->set_var('lang_disc_watchers',lang('Discussions watching'));
                        if (!is_null($save_result))
                        {
                            $GLOBALS['phpgw']->template->set_var('save_messages','<div style="border: 1px solid; width: 100%;">'.
                              (($save_result) ? lang('Saved config') : lang('Failed to save config')).
                              '</div>');
                        }
                        $this->set_common_langs();
                        $this->display_headers();
                        
                        $row_class='row_on';
                        foreach ($this->bo->read_category_watchers() as $cat_watcher){
                          $GLOBALS['phpgw']->template->set_var('cat_name',$cat_watcher['cat_name']);
                          $GLOBALS['phpgw']->template->set_var('cat_watch_yes','<input type="radio" value="1" name="cat_watch['
                                  .$cat_watcher['cat_id'].']" id="cat_watch_'.$cat_watcher['cat_id'].'_yes" '
                                  .($cat_watcher['cat_watch']==0 ? '' : 'checked="checked"').' >'
                          );
                          $GLOBALS['phpgw']->template->set_var('cat_watch_no','<input type="radio" value="0" name="cat_watch['
                                  .$cat_watcher['cat_id'].']" id="cat_watch_'.$cat_watcher['cat_id'].'_no" '
                                  .($cat_watcher['cat_watch']==0 ? 'checked="checked"' : '').' >'
                          );
                                  $GLOBALS['phpgw']->template->set_var('row_class',$row_class);
                          $GLOBALS['phpgw']->template->fp('cat_watchers_list','cat_watcher',TRUE);
                          $row_class= ($row_class=='row_on') ? 'row_off' : 'row_on';
                        }
                        
                        $row_class='row_on';
                        foreach ($this->bo->read_disc_watchers() as $disc_watcher){
                          $GLOBALS['phpgw']->template->set_var('disc_name',$disc_watcher['disc_name']);
                          if ($disc_watcher['disc_id']>0)
                          {
                            $GLOBALS['phpgw']->template->set_var('disc_watch_remove',
                                  '<input type="checkbox" value="0" name="disc_watch['
                                  .$disc_watcher['disc_id'].']" id="disc_watch_'.$disc_watcher['disc_id'].'_remove" >');
                          }else{
                            $GLOBALS['phpgw']->template->set_var('disc_watch_remove','');
                          }
                                  $GLOBALS['phpgw']->template->set_var('row_class',$row_class);
                          $GLOBALS['phpgw']->template->fp('disc_watchers_list','disc_watcher',TRUE);
                          $row_class= ($row_class=='row_on') ? 'row_off' : 'row_on';
                        }
                        
                        $GLOBALS['phpgw']->template->pfp('out','header');
                        $GLOBALS['phpgw']->template->pfp('out','cat_watchers');
                        $GLOBALS['phpgw']->template->pfp('out','disc_watchers');
                        $GLOBALS['phpgw']->template->pfp('out','footer');
                        $GLOBALS['phpgw']->common->phpgw_footer();
                }
                
                function save_config()
                {
//                    print_r($_POST);
                  //Categories save 
                  $this->config(($this->bo->save_cat_watchers($_POST['cat_watch'])+
                                 $this->bo->save_disc_watchers($_POST['disc_watch']))
                  );
                  
                }
                  

	}
