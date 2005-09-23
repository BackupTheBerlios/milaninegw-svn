<?php
	/*****************************************************************************\
	* phpGroupWare - soForums                                                     *
	* http://www.phpgroupware.org                                                 *
	* storage layer reworked by Ralf Becker <RalfBecker-AT-outdoor-training.de>   *
	* Written by Mark A Peters <skeeter@phpgroupware.org>                         *
	* Based off of Jani Hirvinen <jpkh@shadownet.com>                             *
	* -------------------------------------------                                 *
	*  This program is free software; you	can redistribute it and/or modify it  *
	*  under the terms of	the GNU	General	Public License as published by the    *
	*  Free Software Foundation; either version 2	of the License,	or (at your   *
	*  option) any later version.                                                 *
	\*****************************************************************************/

	/* $Id: class.soforum.inc.php,v 1.9.2.1 2004/09/08 15:44:51 ralfbecker Exp $ */

	class soforum
	{
		var $debug=False;
		
		var $db;	/* @var $db db */
		var $threads_table,$body_table,$forums_table,$categories_table;

		function soforum()
		{
			$this->db = $GLOBALS['phpgw']->db;
			$this->db->set_app('forum');
			
			foreach(array('threads','body','forums','categories') as $name)
			{
				$table = $name . '_table';
				$this->$table = 'phpgw_forum_'.$name;	// only reference to the prefix
			}
		}

		function delete_category($cat_id)
		{
			$this->db->delete($this->threads_table,array('cat_id'=>$cat_id),__LINE__,__FILE__);
			$this->db->delete($this->body_table,array('cat_id'=>$cat_id),__LINE__,__FILE__);
			$this->db->delete($this->forums_table,array('cat_id'=>$cat_id),__LINE__,__FILE__);
			$this->db->delete($this->categories_table,array('id'=>$cat_id),__LINE__,__FILE__);
		}

		function delete_forum($cat_id,$forum_id)
		{
			$this->db->delete($this->threads_table,array('cat_id'=>$cat_id,'for_id'=>$forum_id),__LINE__,__FILE__);
			$this->db->delete($this->body_table,array('cat_id'=>$cat_id,'for_id'=>$forum_id),__LINE__,__FILE__);
			$this->db->delete($this->forums_table,array('cat_id'=>$cat_id,'id'=>$forum_id),__LINE__,__FILE__);
		}

		function save_category($cat)
		{
			$data = array(
				'name'	=> $cat['name'],
				'descr'	=> $cat['descr']
			);	
			
			
			if($cat['id'])
			{
				$this->db->update($this->categories_table,$data,array('id' => $cat['id']),__LINE__,__FILE__);
			}
			else
			{
				$this->db->insert($this->categories_table,$data,false,__LINE__,__FILE__);
			}
		}

		function save_forum($forum)
		{
			if($forum['id'])
			{
				if(intval($forum['orig_cat_id']) == intval($forum['cat_id']))
				{
					if($this->debug)
					{
						echo '<!-- Setting name/descr for CAT_ID: '.$forum['cat_id'].' and ID: '.$forum['id'].' -->'."\n";
					}
					$this->db->update($this->forums_table,array(
							'name'		=> $forum['name'],
							'descr'		=> $forum['descr'],
						),array(
							'id'		=> $forum['id'],
							'cat_id'	=> $forum['cat_id'],
						),__LINE__,__FILE__);
				}
				else
				{
					$this->db->update($this->forums_table,array(
							'name'		=> $forum['name'],
							'descr'		=> $forum['descr'],
							'cat_id'	=> $forum['cat_id'],
						),array(
							'id'		=> $forum['id'],
							'cat_id'	=> $forum['orig_cat_id'],
						),__LINE__,__FILE__);

					$this->db->update($this->threads_table,array(
							'cat_id'	=> $forum['cat_id'],
						),array(
							'for_id'	=> $forum['id'],
							'cat_id'	=> $forum['orig_cat_id'],
						),__LINE__,__FILE__);

					$this->db->update($this->body_table,array(
							'cat_id'	=> $forum['cat_id'],
						),array(
							'for_id'	=> $forum['id'],
							'cat_id'	=> $forum['orig_cat_id'],
						),__LINE__,__FILE__);
				}
			}
			else
			{
				if($this->debug)
				{
					echo '<-- Cat ID: '.$forum['cat_id'].' -->'."\n";
				}
				$this->db->insert($this->forums_table,array(
						'cat_id'	=> $forum['cat_id'],
						'name'		=> $forum['name'],
						'descr'		=> $forum['descr'],
						'perm'		=> 0,
						'groups'	=> 0,
					),false,__LINE__,__FILE__);
			}
		}

		function add_reply($data)
		{
			$this->db->insert($this->body_table,array(
					'cat_id'	=> $data['cat_id'],
					'for_id'	=> $data['forum_id'],
					'message'	=> $data['message'],
				),false,__LINE__,__FILE__);

			$this->db->insert($this->threads_table,array(
					'pos'		=> $data['pos'],
					'thread'	=> $data['thread'],
					'depth'		=> $data['depth'],
					'postdate'	=> $data['postdate'],
					'main'		=> $this->db->get_last_insert_id($this->body_table,'id'),
					'parent'	=> $data['parent'],
					'cat_id'	=> $data['cat_id'],
					'for_id'	=> $data['forum_id'],
					'thread_owner' => $GLOBALS['phpgw_info']['user']['account_id'],
					'subject'	=> $data['subject'],
					'stat'		=> 0,
					'n_replies'	=> 0,
				),false,__LINE__,__FILE__);
					
			$this->db->update($this->threads_table,array(
					'n_replies = n_replies+1'
				),array(
					'thread'	=> $data['thread']
				),__LINE__,__FILE__);

		}

		function add_post($data)
		{
			$this->db->insert($this->body_table,array(
					'cat_id'	=> $data['cat_id'],
					'for_id'	=> $data['forum_id'],
					'message'	=> $data['message'],
				),false,__LINE__,__FILE__);

			$body_id = $this->db->get_last_insert_id($this->body_table,'id');

			$this->db->insert($this->threads_table,array(
					'pos'		=> 0,
					'thread'	=> $body_id,
					'depth'		=> 0,
					'postdate'	=> $data['postdate'],
					'main'		=> $body_id,
					'parent'	=> -1,
					'cat_id'	=> $data['cat_id'],
					'for_id'	=> $data['forum_id'],
					'thread_owner' => $GLOBALS['phpgw_info']['user']['account_id'],
					'subject'	=> $data['subject'],
					'stat'		=> 0,
					'n_replies'	=> 0,
				),false,__LINE__,__FILE__);
		}

		// RalfBecker: the following 3 functions are quite riscy in a true multiuser environment, one should use db::get_last_insert_id() instead !!!
		function get_max_forum_id()
		{
			$this->db->select($this->forums_table,'max(id)',false,__LINE__,__FILE__);

			return $this->db->next_record() ? $this->db->f(0) : 0;
		}

		function get_max_body_id()
		{
			$this->db->select($this->body_table,'max(id)',false,__LINE__,__FILE__);

			return $this->db->next_record() ? $this->db->f(0) : 0;
		}

		function get_max_thread_id()
		{
			$this->db->select($this->threads_table,'max(id)',false,__LINE__,__FILE__);

			return $this->db->next_record() ? $this->db->f(0) : 0;
		}

		function fix_pos($thread,$pos)
		{
			$db2 = $GLOBALS['phpgw']->db;
			$this->db->select($this->threads_table,'id,pos',array(
					'thread' => $thread,
					'pos>='.(int)$pos,
				),__LINE__,__FILE__,false,'ORDER BY pos DESC');

			while($this->db->next_record())
			{
				$db2->update($this->threads_table,array(
						'pos'		=> $this->db->f('pos') + 1,
					),array(
						'thread'	=> $thread,
						'id'		=> $this->db->f('id'),
					),__LINE__,__FILE__);
			}
		}

		function get_cat_ids()
		{
			$this->db->select($this->categories_table,'*',false,__LINE__,__FILE__,false,'ORDER BY id');

			while($this->db->next_record())
			{
				$cats[] = Array(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name'),
					'descr'	=> $this->db->f('descr')
				);
			}
			return $cats;
		}

		function get_cat_info($cat_id)
		{
			$this->db->select($this->categories_table,'*',array('id'=>$cat_id),__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$cat = Array(
					'id'	=>	$cat_id,
					'name'	=> $this->db->f('name'),
					'descr'	=> $this->db->f('descr')
				);
			}
			return $cat;
		}

		function get_thread_summary($cat_id,$forum_id=0,$thread_id=0)
		{
			$where = array(
				'cat_id' => $cat_id,
			);
			if($forum_id) $where['for_id'] = $forum_id;
			if($thread_id) $where['thread'] = $thread_id;

			$db2 = $GLOBALS['phpgw']->db;
			$db2->select($this->threads_table,'max(postdate),count(id)',$where,__LINE__,__FILE__);
			$db2->next_record();
			if($db2->f(0))
			{
				$forum['last_post'] = $GLOBALS['phpgw']->common->show_date($db2->from_timestamp($db2->f(0)));
			}
			else
			{
				$forum['last_post'] = '&nbsp;';
			}
			$forum['total'] = $db2->f(1);

			return $forum;
		}

		function get_forum_info($cat_id,$forum_id=0)
		{
			$where = array(
				'cat_id' => $cat_id,
			);
			if($forum_id) $where['id'] = $forum_id;
			
			$this->db->select($this->forums_table,'*',$where,__LINE__,__FILE__);
			while($this->db->next_record())
			{
				$forum[] = Array(
					'cat_id'	=>	$cat_id,
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name'),
					'descr'	=> $this->db->f('descr')
				);
			}
			return $forum;
		}

		function get_thread($cat_id,$forum_id,$collapsed)
		{
			$where = array(
				'cat_id' => $cat_id,
				'for_id' => $forum_id,
			);

			$this->db->select($this->threads_table,'*',$where,__LINE__,__FILE__,false,'ORDER BY '.($collapsed ? 'postdate DESC' : 'thread DESC, postdate, depth'));
			while($this->db->next_record())
			{
				if($collapsed)
				{
					$temp = $this->get_thread_summary($cat_id,$forum_id,$this->db->f('id'));
					$last_post = $temp['last_post'];
				}
				$thread[] = Array(
					'id'	=> $this->db->f('id'),
					'subject'	=> $this->db->f('subject'),
					'author'		=> $this->db->f('thread_owner'),
					'replies'	=> $this->db->f('n_replies'),
					'pos'		=> $this->db->f('pos'),
					'depth'	=> $this->db->f('depth'),
					'last_reply'=> ($last_post?$last_post:$GLOBALS['phpgw']->common->show_date($this->db->from_timestamp($this->db->f('postdate'))))
				);

			}
			return $thread;
		}

		function read_msg($cat_id,$forum_id,$msg_id)
		{
			$db2 = $GLOBALS['phpgw']->db;
			$db2->select($this->threads_table,'thread',array('id'=>$msg_id),__LINE__,__FILE__);
			$db2->next_record();
			$this->db->select($this->threads_table,'*',array(
					'id >= '.(int)$msg_id,
					'cat_id' => $cat_id,
					'for_id' => $forum_id,
					'thread' => $db2->f('thread'),
				),__LINE__,__FILE__,false,'ORDER BY parent,id');

			if(!$this->db->num_rows())
			{
				return False;
			}
			while($this->db->next_record())
			{
				$subject = $this->db->f('subject');
				if (!$subject)
				{
					$subject = '[ ' . lang('No subject') . ' ]';
				}

				$db2->select($this->body_table,'*',array('id'=>$this->db->f('id')),__LINE__,__FILE__);
				$db2->next_record();
				$message = $GLOBALS['phpgw']->strip_html($db2->f('message'));

				$msg[] = Array(
					'id'	=> $this->db->f('id'),
					'main'	=> $this->db->f('main'),
					'parent'	=> $this->db->f('parent'),
					'thread'	=> $this->db->f('thread'),
					'depth'	=> ($this->db->f('depth') + 1),
					'pos'		=> $this->db->f('pos'),
					'subject'	=> $subject,
					'thread_owner'	=> $this->db->f('thread_owner'),
					'postdate'	=> $this->db->f('postdate'),
					'message'	=> $message
				);

			}
			return $msg;
		}
	}
?>
