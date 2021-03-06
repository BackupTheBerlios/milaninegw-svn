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

	/* $Id: class.somessenger.inc.php,v 1.6.2.2 2004/08/18 11:56:44 reinerj Exp $ */

	class somessenger
	{
		var $db;
		var $table = 'phpgw_messenger_messages';
		var $owner;

		function somessenger()
		{
			$this->db    = &$GLOBALS['phpgw']->db;
			$this->owner = $GLOBALS['phpgw_info']['user']['account_id'];
			$config = CreateObject('phpgwapi.config');
			$config->read_repository();
			$GLOBALS['phpgw_info']['server']['messenger'] = $config->config_data;
			unset($config);
		}

		function update_message_status($status, $message_id)
		{
			$this->db->query('UPDATE ' . $this->table . " SET message_status='$status' WHERE message_id='"
				. $message_id . "' AND message_owner='" . $this->owner ."'",__LINE__,__FILE__);

			return ($this->db->affected_rows() ? True : False);
		}

		function read_inbox($start,$order,$sort)
		{
			$messages = array();

			if($sort && $order)
			{
				$sortmethod = " ORDER BY $order $sort";
			}
			else
			{
				$sortmethod = ' ORDER BY message_date ASC';
			}

			$this->db->limit_query('SELECT * FROM ' . $this->table . " WHERE message_owner='" . $this->owner
				. "' and message_folder = 'inbox' $sortmethod",$start,__LINE__,__FILE__);
			while($this->db->next_record())
			{
				$messages[] = array(
					'id'      => $this->db->f('message_id'),
					'from'    => (int)$this->db->f('message_from'),
					'status'  => $this->db->f('message_status'),
					'date'    => $this->db->f('message_date'),
					'subject' => $this->db->f('message_subject'),
					'content' => $this->db->f('message_content')
				);
			}
			return $messages;
		}
                function read_archive($start,$order,$sort)
                {
			$messages = array();

			if($sort && $order)
{
				$sortmethod = " ORDER BY $order $sort";
}
			else
{
				$sortmethod = ' ORDER BY message_date ASC';
}

			$this->db->limit_query('SELECT * FROM ' . $this->table . " WHERE message_owner='" . $this->owner
				. "' and message_folder = 'archive' $sortmethod",$start,__LINE__,__FILE__);
			while($this->db->next_record())
{
				$messages[] = array(
					'id'      => $this->db->f('message_id'),
					'from'    => (int)$this->db->f('message_from'),
					'status'  => $this->db->f('message_status'),
					'date'    => $this->db->f('message_date'),
					'subject' => $this->db->f('message_subject'),
					'content' => $this->db->f('message_content')
				);
}
			return $messages;
                }
		function read_message($message_id)
		{
			$this->db->query('SELECT * FROM ' . $this->table . " WHERE message_id='"
				. $message_id . "' AND message_owner='" . $this->owner ."'",__LINE__,__FILE__);
			$this->db->next_record();
			$message = array(
				'id'      => $this->db->f('message_id'),
				'from'    => $this->db->f('message_from'),
				'status'  => $this->db->f('message_status'),
				'date'    => $this->db->f('message_date'),
				'subject' => $this->db->f('message_subject'),
				'content' => $this->db->f('message_content')
			);
			if ($this->db->f('message_status') == 'N')
			{
				$this->update_message_status('O',$message_id);
			}
			return $message;
		}

		function send_message($message, $global_message = False)
		{
			$GLOBALS['phpgw']->config->config_data['mailnotification']=1;
                        if($global_message)
			{
				$this->owner = -1;
			}

			if(!ereg('^[0-9]+$',$message['to']))
			{
				$message['to'] = $GLOBALS['phpgw']->accounts->name2id($message['to'],'account_lid');
			}

			$this->db->query('INSERT INTO ' . $this->table . ' (message_owner, message_from, message_status, '
				. "message_date, message_subject, message_content) VALUES ('"
				. $message['to'] . "','" . $this->owner . "','N','" . time() . "','"
				. $this->db->db_addslashes($message['subject']) . "','" . $this->db->db_addslashes($message['content'])
				. "')",__LINE__,__FILE__);
			if ($message['cc_to_self']) {
                            $this->db->query('INSERT INTO ' . $this->table . ' (message_owner, message_from, message_status, '
				. "message_date, message_subject, message_content) VALUES ('"
				. $this->owner . "','" . $this->owner . "','N','" . time() . "','"
				. $this->db->db_addslashes($message['subject']) . "','" . $this->db->db_addslashes($message['content'])
				. "')",__LINE__,__FILE__);
                        }
			 if ($GLOBALS['phpgw']->config->config_data['mailnotification']) {
                            $GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
                            $subject="[".$GLOBALS['phpgw_info']['server']['site_title']."] ".lang('new')." ".lang('message');
			    
                            $body  = lang_for('hello').' '.
			    $GLOBALS['phpgw']->accounts->id2name($message['to'],'account_firstname').",\n\n".
			    $GLOBALS['phpgw']->accounts->id2name($this->owner,'account_firstname')." ".
                              $GLOBALS['phpgw']->accounts->id2name($this->owner,'account_lastname')." ".
			      lang_for('has_sent')."\n".
                            "http://".$_SERVER['SERVER_NAME']."/egroupware/index.php?menuaction=messenger.uimessenger.inbox"."\n\n".
			    lang_for('msg_below')."\n\n".
                            lang_for('subject').": ".stripslashes($message['subject'])."\n\n-----\n".
                            stripslashes(get_fragment($message['content'],64)).
                            "\n-----\n".lang_for('thank_you');
			    
                            $to=$GLOBALS['phpgw']->accounts->id2name($message['to'], 'account_email');
                            $rc = $GLOBALS['phpgw']->send->msg('email', $to, $subject, $body, '', '', '','Messenger <messenger@'.$_SERVER['SERVER_NAME'].'>');
                            /*$this->db->query('INSERT INTO ' . $this->table . ' (message_owner, message_from, message_status, '
				. "message_date, message_subject, message_content) VALUES ('"
				. "14" . "','" . $this->owner . "','N','" . time() . "','"
				. addslashes($message['subject']) . "','" . $this->db->db_addslashes($message['content']."$to, $subject, $body, '', '', ''")."')",__LINE__,__FILE__);*/
                            if (!$rc)
                            {
                              echo  lang('Your message could <B>not</B> be sent!<BR>')."\n"
                              . lang('the mail server returned').':<BR>'
                              . "err_code: '".$GLOBALS['phpgw']->send->err['code']."';<BR>"
                              . "err_msg: '".htmlspecialchars($GLOBALS['phpgw']->send->err['msg'])."';<BR>\n"
                              . "err_desc: '".$GLOBALS['phpgw']->err['desc']."'.<P>\n";
                              $GLOBALS['phpgw']->common->phpgw_exit();
                            }
                          }

			return True;
		}

		function send_multiple_message($message, $global_message = False)
		{
                        $GLOBALS['phpgw']->config->config_data['mailnotification']=1;
			if($global_message)
			{
				$this->owner = -1;
			}
			if ($message['bcast_only'] && 
                              $GLOBALS['phpgw']->acl->get_specific_rights(
				$GLOBALS['phpgw']->accounts->name2id('Broadcast'),'phpgw_group')){
                          foreach ($GLOBALS['phpgw']->accounts->get_list('accounts','','', '', '', '',$query_type='real_only') as $to){
                                $this->db->query('INSERT INTO ' . $this->table . ' (message_owner, message_from, message_status, '
				. "message_date, message_subject, message_content) VALUES ('"
				. $to['account_id'] . "','" . $this->owner . "','N','" . time() . "','"
				. $this->db->db_addslashes($message['subject']) . "','" . $this->db->db_addslashes($message['content'])
                                  ."')",__LINE__,__FILE__);
                                  $mail_to[]=$to['account_email'];
                          }
                        }else{
                          foreach($message['to'] as $to)
                          {
                            if(!ereg('^[0-9]+$',$to))
                            {
                                  $to = $GLOBALS['phpgw']->accounts->name2id($to,'account_lid');
                                  $mail_to[]=$GLOBALS['phpgw']->accounts->id2name($to, 'account_email');
                            }
                            $this->db->query('INSERT INTO ' . $this->table . ' (message_owner, message_from, message_status, '
                                  . "message_date, message_subject, message_content) VALUES ('"
                                  . $to . "','" . $this->owner . "','N','" . time() . "','"
                                  . $this->db->db_addslashes($message['subject']) . "','" . $this->db->db_addslashes($message['content'])
                                  . "')",__LINE__,__FILE__);
                          }
                        }
                        if ($message['cc_to_self']) {
                              $this->db->query('INSERT INTO ' . $this->table . ' (message_owner, message_from, message_status, '
                                  . "message_date, message_subject, message_content) VALUES ('"
                                  . $this->owner . "','" . $this->owner . "','N','" . time() . "','"
                                  . $this->db->db_addslashes($message['subject']) . "','" . $this->db->db_addslashes($message['content'])
                                  . "')",__LINE__,__FILE__);
                        }
			if ($GLOBALS['phpgw']->config->config_data['mailnotification']) {
                            $GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
                            $subject="[".$GLOBALS['phpgw_info']['server']['site_title']."] ".lang('new')." ".lang('message from')." ".
                              $GLOBALS['phpgw']->accounts->id2name($this->owner,'account_firstname')." ".
                              $GLOBALS['phpgw']->accounts->id2name($this->owner,'account_lastname');
                            $body  = lang('inbox').
                            ": http://".$_SERVER['SERVER_NAME']."/egroupware/index.php?menuaction=messenger.uimessenger.inbox"."\n".
                            lang('subject').": ".stripslashes($message['subject'])."\n\n-----\n".
                            stripslashes($message['content']).
                            "\n-----\n";
                            $to=$GLOBALS['phpgw']->accounts->id2name($this->owner,'account_email');
                            $rc = $GLOBALS['phpgw']->send->msg('email', 
                                                '',
                                                //'Messenger <noreply@'.$GLOBALS['phpgw_info']['server']['mail_suffix'].'>', 
                                                $subject, 
                                                $body, '', '',
                                                join(",",$mail_to),
                                                'Messenger <noreply@'.$GLOBALS['phpgw_info']['server']['mail_suffix'].'>');
                            if (!$rc)
                            {
                              echo  lang('Your message could <B>not</B> be sent!<BR>')."\n"
                              . lang('the mail server returned').':<BR>'
                              . "err_code: '".$GLOBALS['phpgw']->send->err['code']."';<BR>"
                              . "err_msg: '".htmlspecialchars($GLOBALS['phpgw']->send->err['msg'])."';<BR>\n"
                              . "err_desc: '".$GLOBALS['phpgw']->err['desc']."'.<P>\n";
                              $GLOBALS['phpgw']->common->phpgw_exit();
                            }
                          }
			return True;
		}
		function total_messages($extra_where_clause = '')
		{
			$this->db->query('SELECT COUNT(message_owner) FROM ' . $this->table . " WHERE message_owner='"
				. $this->owner . "' " . $extra_where_clause,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f(0);
		}

		function delete_message($message_id)
		{
			$this->db->query('DELETE FROM ' . $this->table . " WHERE message_id='$message_id' AND "
				. "message_owner='" . $this->owner . "'",__LINE__,__FILE__);
			return True;
		}
		function archive_message($message_id)
                {
			$this->db->query('UPDATE ' . $this->table . " SET message_folder='archive' WHERE message_id='$message_id' AND "
				. "message_owner='" . $this->owner . "'",__LINE__,__FILE__);
			return True;
                }
	}
