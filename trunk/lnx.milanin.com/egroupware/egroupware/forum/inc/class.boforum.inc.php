<?php
	/*****************************************************************************\
	* phpGroupWare - boForums                                                     *
	* http://www.phpgroupware.org                                                 *
	* Written by Mark A Peters <skeeter@phpgroupware.org>                         *
	* Based off of Jani Hirvinen <jpkh@shadownet.com>                             *
	* -------------------------------------------                                 *
	*  This program is free software; you	can redistribute it and/or modify it  *
	*  under the terms of	the GNU	General	Public License as published by the    *
	*  Free Software Foundation; either version 2	of the License,	or (at your   *
	*  option) any later version.                                                 *
	\*****************************************************************************/

	/* $Id: class.boforum.inc.php,v 1.11 2003/08/28 14:26:08 ralfbecker Exp $ */

	class boforum
	{
		var $public_functions = array(
			'reply' => True,
			'post'  => True,
			'delete_category'	=> True,
			'delete_forum'	=> True,
			'category'	=> True,
			'forum'	=> True
		);

		var $debug = False;
		
		var $so;

		var $use_session;

		var $view;
		var $location;
		var $cat_id;
		var $forum_id;

		function boforum($session=0)
		{
			$this->so = CreateObject('forum.soforum');
			
			if($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$var = Array(
				'view',
				'location',
				'cat_id',
				'forum_id'
			);

			for($i=0;$i<count($var);$i++)
			{
				$var_str = $var[$i];
//				$this->$var_str = (@isset($GLOBALS['HTTP_GET_VARS'][$var_str])?intval($GLOBALS['HTTP_GET_VARS'][$var_str]):$this->$var_str);
//				$this->$var_str = (@isset($GLOBALS['HTTP_POST_VARS'][$var_str])?intval($GLOBALS['HTTP_POST_VARS'][$var_str]):$this->$var_str);
				$this->$var_str = (@isset($GLOBALS['HTTP_GET_VARS'][$var_str])?$GLOBALS['HTTP_GET_VARS'][$var_str]:$this->$var_str);
				$this->$var_str = (@isset($GLOBALS['HTTP_POST_VARS'][$var_str])?$GLOBALS['HTTP_POST_VARS'][$var_str]:$this->$var_str);
			}
			if(!@isset($this->view))
			{
				$this->view = $GLOBALS['phpgw_info']['user']['preferences']['forum']['default_view'];
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				if($this->debug) { echo '<br>Save:'; _debug_array($data); }
				$GLOBALS['phpgw']->session->appsession('session_data','forum',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','forum');
			if($this->debug) { echo '<br>Read:'; _debug_array($data); }

			$this->view     = $data['view'];
			$this->location = $data['location'];
			$this->cat_id   = $data['cat_id'];
			$this->forum_id = $data['forum_id'];
		}
		
		function post()
		{
			if ($GLOBALS['HTTP_POST_VARS']['action'] == 'post')
			{
				$data = Array(
					'cat_id'	=> $GLOBALS['HTTP_POST_VARS']['cat_id'],
					'forum_id'	=> $GLOBALS['HTTP_POST_VARS']['forum_id'],
					'postdate'	=> time() - ((60 * 60) * intval($GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'])),
					'subject'	=> $GLOBALS['HTTP_POST_VARS']['subject'],
					'message'	=> $GLOBALS['HTTP_POST_VARS']['message']
				);

				$this->so->add_post($data);
			}
			$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiforum.threads'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function reply()
		{
			if ($GLOBALS['HTTP_POST_VARS']['action'] == 'reply')
			{
				$stat = 0;

				$next_f_body_id = $this->so->get_max_body_id() + 1;

				$next_f_threads_id = $this->so->get_max_thread_id() + 1;

				if ($GLOBALS['HTTP_POST_VARS']['pos'] != 0)
				{
					$this->so->fix_pos($GLOBALS['HTTP_POST_VARS']['thread'],$GLOBALS['HTTP_POST_VARS']['pos']);
				}
				else
				{
					$GLOBALS['HTTP_POST_VARS']['pos'] = 1;
				}

				$data = Array(
					'pos' => $GLOBALS['HTTP_POST_VARS']['pos'],
					'thread'	=> $GLOBALS['HTTP_POST_VARS']['thread'],
					'depth'	=> $GLOBALS['HTTP_POST_VARS']['depth'],
					'postdate'	=> time() - ((60 * 60) * intval($GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'])),
					'parent'	=> $GLOBALS['HTTP_POST_VARS']['msg'],
					'cat_id'	=> $GLOBALS['HTTP_POST_VARS']['cat_id'],
					'forum_id'	=> $GLOBALS['HTTP_POST_VARS']['forum_id'],
					'subject'	=> $GLOBALS['HTTP_POST_VARS']['subject'],
					'message'	=> $GLOBALS['HTTP_POST_VARS']['message']
				);

				$this->so->add_reply($data);
			}
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiforum.threads'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function delete_category()
		{
			if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiforum.index'));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$this->so->delete_category($this->cat_id);
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiadmin.index'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function delete_forum()
		{
			if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiforum.index'));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$this->so->delete_forum($this->cat_id,$this->forum_id);
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiadmin.index'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function category()
		{
			if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiforum.index'));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$this->so->save_category($GLOBALS['HTTP_POST_VARS']['cat']);
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiadmin.index'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function forum()
		{
			if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiforum.index'));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$this->so->save_forum($GLOBALS['HTTP_POST_VARS']['forum']);
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=forum.uiadmin.index'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function get_all_cat_info()
		{
			$cats = $this->so->get_cat_ids();

			if (is_array($cats))
			{
				while(list($key,$cat) = each($cats))
				{
					$summary[$key] = $cat;
					$temp = $this->so->get_thread_summary($cat['id']);
					$summary[$key]['last_post'] = $temp['last_post'];
					$summary[$key]['total'] = $temp['total'];
				}
				return $summary;
			}
		}

		function get_cat_info($cat_id)
		{
			return $this->so->get_cat_info($cat_id);
		}

		function get_forum_info($cat_id,$forum_id)
		{
			$forum = $this->so->get_forum_info($cat_id,$forum_id);
			return $forum[0];
		}

		function get_forums_for_cat($cat_id)
		{
			$forums = $this->so->get_forum_info($cat_id);
			while($forums && list($key,$forum) = each($forums))
			{
				$summary[$key] = $forum;
				$temp = $this->so->get_thread_summary($cat_id,$forum['id']);
				$summary[$key]['last_post'] = $temp['last_post'];
				$summary[$key]['total'] = $temp['total'];
			}
			return $summary;
		}
		
		function get_thread($cat_id,$forum_id,$collapsed=False)
		{
			return $this->so->get_thread($cat_id,$forum_id,$collapsed);
		}

		function read_msg($cat_id,$forum_id,$msg_id)
		{
			return $this->so->read_msg($cat_id,$forum_id,$msg_id);
		}
	}
?>
