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

	class uimessenger
	{
		var $bo;
		var $template;
		var $public_functions = array(
			'inbox'          => True,
			'compose'        => True,
			'compose_global' => True,
			'compose_multiple'=> True,
			'read_message'   => True,
			'reply'          => True,
			'forward'        => True,
			'delete'         => True
		);

		function uimessenger()
		{
			$this->bo         = CreateObject('messenger.bomessenger');
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');
		}

		function display_headers($extras = '')
		{
			$GLOBALS['phpgw']->template->set_file('_header','messenger_header.tpl');
			$GLOBALS['phpgw']->template->set_block('_header','global_header');
			$GLOBALS['phpgw']->template->set_var('lang_inbox','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.inbox') . '">' . lang('Inbox') . '</a>');
			$GLOBALS['phpgw']->template->set_var('lang_compose','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.compose') . '">' . lang('Compose to single-user') . '</a>');
			$GLOBALS['phpgw']->template->set_var('lang_compose_multiple','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.compose_multiple') . '">' . lang('Compose to multi-users') . '</a>');

			if($extras['nextmatchs_left'])
			{
				$GLOBALS['phpgw']->template->set_var('nextmatchs_left',$extras['nextmatchs_left']);
			}

			if($extras['nextmatchs_right'])
			{
				$GLOBALS['phpgw']->template->set_var('nextmatchs_right',$extras['nextmatchs_right']);
			}

			$GLOBALS['phpgw']->template->fp('app_header','global_header');

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		function set_common_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

			$GLOBALS['phpgw']->template->set_var('lang_to',lang('Send message to'));
			$GLOBALS['phpgw']->template->set_var('lang_from',lang('Message from'));
			$GLOBALS['phpgw']->template->set_var('lang_subject',lang('Subject'));
			$GLOBALS['phpgw']->template->set_var('lang_content',lang('Message'));
			$GLOBALS['phpgw']->template->set_var('lang_date',lang('Date'));
		}

		function delete()
		{
			$messages = get_var('messages', array('GET','POST'));
			$this->bo->delete_message($messages);

			$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
		}

		function inbox()
		{
			$start = get_var('start',array('GET','POST'));
			$order = get_var('order',array('GET','POST'));
			$sort  = get_var('sort',array('GET','POST'));
			$total = $this->bo->total_messages();

			$extra_menuaction = '&menuaction=messenger.uimessenger.inbox';
			$extra_header_info['nextmatchs_left']  = $this->nextmatchs->left('/index.php',$start,$total,$extra_menuaction);
			$extra_header_info['nextmatchs_right'] = $this->nextmatchs->right('/index.php',$start,$total,$extra_menuaction);

			$this->display_headers($extra_header_info);

			$GLOBALS['phpgw']->template->set_file('_inbox','inbox.tpl');
			$GLOBALS['phpgw']->template->set_block('_inbox','list');
			$GLOBALS['phpgw']->template->set_block('_inbox','row');
			$GLOBALS['phpgw']->template->set_block('_inbox','row_empty');

			$this->set_common_langs();
			$GLOBALS['phpgw']->template->set_var('sort_date','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_date',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('Date') . '</a>');
			$GLOBALS['phpgw']->template->set_var('sort_subject','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_subject',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('Subject') . '</a>');
			$GLOBALS['phpgw']->template->set_var('sort_from','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_from',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('From') . '</a>');

			$params = array(
				'start' => $start,
				'order' => $order,
				'sort'  => $sort
			);
			$messages = $this->bo->read_inbox($params);

			while(is_array($messages) && list(,$message) = each($messages))
			{
				$status = $message['status'] . '-';
				if($message['status'] == 'N' || $message['status'] == 'O')
				{
					$status = '&nbsp;';
				}

				$GLOBALS['phpgw']->template->set_var('row_from',$message['from']);
				$GLOBALS['phpgw']->template->set_var('row_date',$message['date']);
				$GLOBALS['phpgw']->template->set_var('row_subject','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.read_message&message_id=' . $message['id']) . '">' . $message['subject'] . '</a>');
				$GLOBALS['phpgw']->template->set_var('row_status',$status);
				$GLOBALS['phpgw']->template->set_var('row_checkbox','<input type="checkbox" name="messages[]" value="' . $message['id'] . '">');

				$GLOBALS['phpgw']->template->fp('rows','row',True);
			}

			if(!is_array($messages))
			{
				$GLOBALS['phpgw']->template->set_var('lang_empty',lang('You have no messages'));
				$GLOBALS['phpgw']->template->fp('rows','row_empty',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.delete'));
				$GLOBALS['phpgw']->template->set_var('button_delete','<input type="image" src="' . PHPGW_IMAGES . '/delete.gif" name="delete" title="' . lang('Delete selected') . '" border="0">');
			}

			$GLOBALS['phpgw']->template->pfp('out','list');
		}

		function set_compose_read_blocks()
		{
			$GLOBALS['phpgw']->template->set_file('_form','form.tpl');

			$GLOBALS['phpgw']->template->set_block('_form','form');
			$GLOBALS['phpgw']->template->set_block('_form','form_to');
			$GLOBALS['phpgw']->template->set_block('_form','form_date');
			$GLOBALS['phpgw']->template->set_block('_form','form_from');
			$GLOBALS['phpgw']->template->set_block('_form','form_buttons');
			$GLOBALS['phpgw']->template->set_block('_form','form_read_buttons');
			$GLOBALS['phpgw']->template->set_block('_form','form_read_buttons_for_global');
		}

		function compose_global()
		{
			if(!$GLOBALS['phpgw']->acl->check('run',PHPGW_ACL_READ,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
			}

			$message = $_POST['message'];
			if($_POST['send'])
			{
				$errors = $this->bo->send_global_message($message);
				if(@is_array($errors))
				{
					$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
				}
			}

			$this->display_headers();
			$this->set_compose_read_blocks();

			$this->set_common_langs();
			$GLOBALS['phpgw']->template->set_var('header_message',lang('Compose global message'));

			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.compose_global'));
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="message[subject]" value="' . $message['subject'] . '">');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea name="message[content]" rows="20" wrap="hard" cols="76">' . $message['content'] . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$GLOBALS['phpgw']->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$GLOBALS['phpgw']->template->fp('buttons','form_buttons');
			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		function compose()
		{
			$message = $_POST['message'];
			$message_to = get_var('message_to', array('GET','POST'));

			if($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
			}
			if($_POST['send'])
			{
				$errors = $this->bo->send_message($message);
				if(@is_array($errors))
				{
					$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
				}
			}
			if (isset($message_to) && $message_to != ""){
				$message['to']=$message_to;
			}
			// recipient dropdown field stuff added by tobi (gabele@uni-sql.de)
			$tobox = '<input name="message[to]" value="' . $message['to'] . '" size="30">';
			$sndid = 0;
			if($message['to'] != '')
			{
				$sndid=$GLOBALS['phpgw']->accounts->name2id($message['to']);
			}
			$myownid=$GLOBALS['phpgw_info']['user']['account_id'];
			if(@isset($GLOBALS['phpgw_info']['server']['messenger']['use_selectbox']))
			{
				$users = $this->bo->get_messenger_users();

				$str = '<option value="" selected>'.lang('Select User').'</option>'."\n";

				foreach($users as $user)
				{
					if($user['account_id'] != (int)$myownid)
					{
						$str .= '    <option value="' .$user['account_lid']. '"'.($sndid==$user['account_id'] ?' selected':'').'>'.$user['account_firstname'].' '.$user['account_lastname'].'</option>'."\n";
					}
				}

				$tobox = "\n".'   <select name="message[to]" size="1">'."\n".$str.'   </select>';
			}

			$this->display_headers();
			$this->set_compose_read_blocks();

			$this->set_common_langs();
			$GLOBALS['phpgw']->template->set_var('header_message',lang('Compose message'));

			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.compose'));
			$GLOBALS['phpgw']->template->set_var('value_to',$tobox);
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="message[subject]" value="' . $message['subject'] . '" size="30">');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea name="message[content]" rows="20" wrap="hard" cols="76">' . $message['content'] . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$GLOBALS['phpgw']->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$GLOBALS['phpgw']->template->fp('to','form_to');
			$GLOBALS['phpgw']->template->fp('buttons','form_buttons');
			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		function compose_multiple()
		{
			$message = $_POST['message'];

			if($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
			}
			if($_POST['send'])
			{
				$errors = $this->bo->send_multiple_message($message);
				if(@is_array($errors))
				{
					$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
				}
			}

			// recipient dropdown field stuff added by tobi (gabele@uni-sql.de)
			$sndid = array();
			
			if(count($message['to']) != 0)
			{  
			   foreach($message['to'] as $to)
			   {
			        $sndid[] = $GLOBALS['phpgw']->accounts->name2id($to);
			   } 
			}   
			

			$myownid=$GLOBALS['phpgw_info']['user']['account_id'];
			
			$users = $this->bo->get_messenger_users();
			$str = '';
			foreach($users as $user)
			{
				if($user['account_id'] != (int)$myownid)
				{
					$str .= '    <option value="' .$user['account_lid']. '"'.(in_array($user['account_id'],$sndid) ?' selected':'').'>'.$user['account_firstname'].' '.$user['account_lastname'].'</option>'."\n";
				}
			}

				$tobox = "\n".'   <select name="message[to][]" multiple="1" size="7">'."\n".$str.'   </select>';
	
			$this->display_headers();
			$this->set_compose_read_blocks();

			$this->set_common_langs();
			$GLOBALS['phpgw']->template->set_var('header_message',lang('Compose message'));

			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.compose_multiple'));
			$GLOBALS['phpgw']->template->set_var('value_to',$tobox);
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="message[subject]" value="' . $message['subject'] . '" size="30">');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea name="message[content]" rows="20" wrap="hard" cols="76">' . $message['content'] . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$GLOBALS['phpgw']->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$GLOBALS['phpgw']->template->fp('to','form_to');
			$GLOBALS['phpgw']->template->fp('buttons','form_buttons');
			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		function read_message()
		{
			$message_id = $_GET['message_id'] ? $_GET['message_id'] : $_POST['message_id'];
			$message = $this->bo->read_message($message_id);

			$this->display_headers();
			$this->set_compose_read_blocks();
			$this->set_common_langs();

			$GLOBALS['phpgw']->template->set_var('header_message',lang('Read message'));

			$GLOBALS['phpgw']->template->set_var('value_from',$message['from']);
			$GLOBALS['phpgw']->template->set_var('value_subject',$message['subject']);
			$GLOBALS['phpgw']->template->set_var('value_date',$message['date']);
			$GLOBALS['phpgw']->template->set_var('value_content','<pre>' . $GLOBALS['phpgw']->strip_html($message['content']) . '</pre>');

			$GLOBALS['phpgw']->template->set_var('link_delete','<a href="'
				. $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.delete&messages%5B%5D=' . $message['id'])
				. '">' . lang('Delete') . '</a>');

			$GLOBALS['phpgw']->template->set_var('link_reply','<a href="'
				. $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.reply&message_id=' . $message['id'])
				. '">' . lang('Reply') . '</a>');

			$GLOBALS['phpgw']->template->set_var('link_forward','<a href="'
				. $GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.forward&message_id=' . $message['id'])
				. '">' . lang('Forward') . '</a>');

			switch($message['status'])
			{
				case 'N':
					$GLOBALS['phpgw']->template->set_var('value_status',lang('New'));
					break;
				case 'R':
					$GLOBALS['phpgw']->template->set_var('value_status',lang('Replied'));
					break;
				case 'F':
					$GLOBALS['phpgw']->template->set_var('value_status',lang('Forwarded'));
					break;
			}

			if($message['global_message'])
			{
				$GLOBALS['phpgw']->template->fp('read_buttons','form_read_buttons_for_global');
			}
			else
			{
				$GLOBALS['phpgw']->template->fp('read_buttons','form_read_buttons');
			}

			$GLOBALS['phpgw']->template->fp('date','form_date');
			$GLOBALS['phpgw']->template->fp('from','form_from');
			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		// recipient dropdown field stuff added by tobi (gabele@uni-sql.de)
		function build_part_list(&$users,$accounts,$owner)
		{
			if(!is_array($accounts))
			{
				return;
			}
			foreach($accounts as $id)
			{
				$id = (int)$id;
				if($id == $owner)
				{
					continue;
				}
				elseif(!isset($users[$id]))
				{
					if($GLOBALS['phpgw']->accounts->exists($id) == True && $GLOBALS['phpgw']->accounts->get_type($id) == 'u')
					{
						$users[$id] = Array(
							'name' => $GLOBALS['phpgw']->common->grab_owner_name($id),
							'type' => $GLOBALS['phpgw']->accounts->get_type($id)
						);
					}
				}
			}
			if(!function_exists('strcmp_name'))
			{
				function strcmp_name($arr1,$arr2)
				{
					if($diff = strcmp($arr1['type'],$arr2['type']))
					{
						return $diff; // groups before users
					}
					return strnatcasecmp($arr1['name'],$arr2['name']);
				}
			}
			uasort($users,'strcmp_name');
		}

		function reply()
		{
			$message_id = get_var('message_id', array('GET','POST'));
			$message = get_var('message','POST');
			$n_message = get_var('n_message','POST');

			if(!$message)
			{
				$message = $this->bo->read_message_for_reply($message_id,'RE');
			}
			if($_POST['send'])
			{
				$errors = $this->bo->send_message($n_message);
				if(@is_array($errors))
				{
					$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
				}
			}
			// recipient dropdown field stuff added by tobi (gabele@uni-sql.de)
			$tobox = '<input name="n_message[to]" value="' . $message['from'] . '" size="30">';
			$sndid = 0;
			if($message['from'] != '')
			{
				$sndid=$GLOBALS['phpgw']->accounts->name2id($message['from']);
			}
			$myownid=$GLOBALS['phpgw_info']['user']['account_id'];
			if(@isset($GLOBALS['phpgw_info']['server']['messenger']['use_selectbox']))
			{
				$users = $this->bo->get_messenger_users();
				$str = '';
				foreach($users as $user)
				{
					if($user['account_id'] != (int)$myownid)
					{
						$str .= '    <option value="' .$user['account_lid']. '"'.($sndid==$user['account_id'] ?' selected':'').'>'.$user['account_firstname'].' '.$user['account_lastname'].'</option>'."\n";
					}
				}

				$tobox = "\n".'   <select name="n_message[to]" size="1">'."\n".$str.'   </select>';
				if(count($users) <= 1)
				{
					$tobox = '<input name="n_message[to]" value="' . $message['from'] . '" size="30">';
				}
			}

			// end dropdown

			$this->display_headers();
			$this->set_compose_read_blocks();
			$this->set_common_langs();

			$GLOBALS['phpgw']->template->set_var('header_message',lang('Reply to a message'));

			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.reply&message_id=' . $message['id']));
			$GLOBALS['phpgw']->template->set_var('value_to',$tobox);
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="n_message[subject]" value="' . stripslashes($message['subject']) . '" size="30">');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea name="n_message[content]" rows="20" wrap="hard" cols="76">' . stripslashes($message['content']) . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$GLOBALS['phpgw']->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$GLOBALS['phpgw']->template->fp('to','form_to');
			$GLOBALS['phpgw']->template->fp('buttons','form_buttons');
			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		function forward()
		{
			$message_id = get_var('message_id', array('GET','POST'));
			$message = get_var('message','POST');
			$n_message = get_var('n_message','POST');

			if(!$message)
			{
				$message = $this->bo->read_message_for_reply($message_id,'FW');
			}
			if($_POST['send'])
			{
				$errors = $this->bo->send_message($n_message);
				if(@is_array($errors))
				{
					$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=messenger.uimessenger.inbox');
				}
			}
			// recipient dropdown field stuff added by tobi (gabele@uni-sql.de)
			$tobox = '<input name="n_message[to]" value="' . $message['from'] . '" size="30">';
			$sndid = 0;
			if($message['from'] != '')
			{
				$sndid=$GLOBALS['phpgw']->accounts->name2id($message['from']);
			}
			$myownid = $GLOBALS['phpgw_info']['user']['account_id'];
			if(@isset($GLOBALS['phpgw_info']['server']['messenger']['use_selectbox']))
			{
				$users = $this->bo->get_messenger_users();
				$str = '';
				foreach($users as $user)
				{
					if($user['account_id'] != (int)$myownid)
					{
						$str .= '    <option value="' .$user['account_lid']. '"'.($sndid==$user['account_id'] ?' selected':'').'>'.$user['account_firstname'].' '.$user['account_lastname'].'</option>'."\n";
					}
				}

				$tobox = "\n".'   <select name="n_message[to]" size="1">'."\n".$str.'   </select>';
				if(count($users) <= 1)
				{
					$tobox = '<input name="n_message[to]" value="' . $message['from'] . '" size="30">';
				}
			}
			// end dropdown

			$this->display_headers();
			$this->set_compose_read_blocks();
			$this->set_common_langs();

			$GLOBALS['phpgw']->template->set_var('header_message',lang('Forward a message'));

			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=messenger.uimessenger.forward&message_id=' . $message['id']));
			$GLOBALS['phpgw']->template->set_var('value_to',$tobox);
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="n_message[subject]" value="' . stripslashes($message['subject']) . '" size="30">');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea name="n_message[content]" rows="20" wrap="hard" cols="76">' . stripslashes($message['content']) . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$GLOBALS['phpgw']->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$GLOBALS['phpgw']->template->fp('to','form_to');
			$GLOBALS['phpgw']->template->fp('buttons','form_buttons');
			$GLOBALS['phpgw']->template->pfp('out','form');
		}
	}
