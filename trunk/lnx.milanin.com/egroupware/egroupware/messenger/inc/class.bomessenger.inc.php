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

	class bomessenger
	{
		var $so;
		var $public_functions = array(
			'send_message'         => True,
			'send_global_message'  => True,
			'send_multiple_message'=> True,
			'reply'                => True,
			'forward'              => True,
			'list_methods'         => True
		);
		var $soap_functions = array();
		var $xmlrpc_methods = array();

		function bomessenger()
		{
			$this->so = CreateObject('messenger.somessenger');
		}

		function update_message_status($p)
		{
			return $this->so->update_message_status($p['status'],$p['message_id']);
		}

		function send_global_message($message='')
		{
			if(!$GLOBALS['phpgw']->acl->check('run',PHPGW_ACL_READ,'admin') || $cancel)
			{
				return False;
			}

			if(!$message['subject'])
			{
				$errors[] = lang('You must enter a subject');
			}

			if(!$message['content'])
			{
				$errors[] = lang("You didn't enter anything for the message");
			}

			if(is_array($errors))
			{
				return $errors;
			}
			else
			{
				$account_info = $GLOBALS['phpgw']->accounts->get_list('accounts');

				$this->so->db->transaction_begin();
				while(list(,$account) = @each($account_info))
				{
					$message['to'] = $account['account_lid'];
					$this->so->send_message($message,True);
				}
				$this->so->db->transaction_commit();
				return True;
			}
		}

		function check_for_missing_fields($message)
		{
			$acctid = $GLOBALS['phpgw']->accounts->name2id($message['to']);

			if(!$acctid)
			{
				if($message['to'])
				{
					$errors[] = lang("I can't find the username %1 on the system",$message['to']);
				}
				else
				{
					$errors[] = lang('You must enter the username this message is for');
				}
			}

			$acct = CreateObject('phpgwapi.accounts',$GLOBALS['phpgw']->accounts->name2id($message['to']));
			$acct->read_repository();
			if($acct->is_expired() && $GLOBALS['phpgw']->accounts->name2id($message['to'],'account_lid'))
			{
				$errors[] = lang("Sorry, %1's account is not currently active",$message['to']);
			}

			if(!$message['subject'])
			{
				$errors[] = lang('You must enter a subject');
			}

			if(!$message['content'])
			{
				$errors[] = lang("You didn't enter anything for the message");
			}
			return $errors;
		}

		function send_message($message='')
		{
			if(!$GLOBALS['phpgw']->acl->check('run',PHPGW_ACL_READ,'messenger'))
			{
				return False;
			}
			$errors = $this->check_for_missing_fields($message);

			if(is_array($errors))
			{
				return $errors;
			}
			else
			{
				return $this->so->send_message($message);
			}
		}

		function send_multiple_message($message='')
		{
			if(!$GLOBALS['phpgw']->acl->check('run',PHPGW_ACL_READ,'messenger'))
			{
				return False;
			}
			 if(count($message['to']) == 0)
			{
			      $errors[] = lang('You must enter the username this message is for');
			}
			else
			{   
			      foreach($message['to'] as $to)
			      {  
			            $acctid = $GLOBALS['phpgw']->accounts->name2id($to);

				    if(!$acctid)
				    {
				      if($to)
				      { 
					$errors[] = lang("I can't find the username %1 on the system",$to);
				      }
			            }

				    $acct = CreateObject('phpgwapi.accounts',$GLOBALS['phpgw']->accounts->name2id($to));
				    $acct->read_repository();
				    if($acct->is_expired() && $GLOBALS['phpgw']->accounts->name2id($to,'account_lid'))
				    {
				      $errors[] = lang("Sorry, %1's account is not currently active",$to);
				    }
			      }
			} 
     
			if(!$message['subject'])
			{
				$errors[] = lang('You must enter a subject');
			}

			if(!$message['content'])
			{
				$errors[] = lang("You didn't enter anything for the message");
			}

			if(is_array($errors))
			{
				return $errors;
			}
			else
			{
				return $this->so->send_multiple_message($message);
			}
		}

		function read_inbox($values)
		{
			$start = $values['start'];
			$order = $values['order'];
			$sort  = $values['sort'];

			$messages = $this->so->read_inbox((int)$start,$order,$sort);

			while(is_array($messages) && list(,$message) = each($messages))
			{
				if($message['from'] == -1)
				{
					$cached['-1']       = -1;
					$cached_names['-1'] = lang('Global Message');
				}

				// Cache our results, so we don't query the same account multiable times
				if(!$cached[$message['from']])
				{
					$acct = CreateObject('phpgwapi.accounts',$message['from']);
					$acct->read_repository();
					$cached[$message['from']]       = $message['from'];
					$cached_names[$message['from']] = $GLOBALS['phpgw']->common->display_fullname($acct->data['account_lid'],$acct->data['firstname'],$acct->data['lastname']);
				}

				/*
				** N - New
				** R - Replied
				** O - Old (read)
				** F - Forwarded
				*/
				if($message['status'] == 'N')
				{
					$message['subject'] = '<b>' . $message['subject'] . '</b>';
					//$message['status'] = 'N';
					$message['date'] = '<b>' . $GLOBALS['phpgw']->common->show_date($message['date']) . '</b>';
					$message['from'] = '<b>' . $cached_names[$message['from']] . '</b>';
				}
				else
				{
					$message['date'] = $GLOBALS['phpgw']->common->show_date($message['date']);
					$message['from'] = $cached_names[$message['from']];
				}

				if($message['status'] == 'O')
				{
					//$message['status'] = '&nbsp;';
				}

				$_messages[] = array(
					'id'      => $message['id'],
					'from'    => $message['from'],
					'status'  => $message['status'],
					'date'    => $message['date'],
					'subject' => $message['subject'],
					'content' => $message['content']
				);
			}

			if(@is_null($_messages))
			{
				return array();
			}

			return $_messages;
		}
                function read_archive($values)
{
			$start = $values['start'];
			$order = $values['order'];
			$sort  = $values['sort'];

			$messages = $this->so->read_archive((int)$start,$order,$sort);

			while(is_array($messages) && list(,$message) = each($messages))
{
				if($message['from'] == -1)
{
					$cached['-1']       = -1;
					$cached_names['-1'] = lang('Global Message');
}

				// Cache our results, so we don't query the same account multiable times
				if(!$cached[$message['from']])
{
					$acct = CreateObject('phpgwapi.accounts',$message['from']);
					$acct->read_repository();
					$cached[$message['from']]       = $message['from'];
					$cached_names[$message['from']] = $GLOBALS['phpgw']->common->display_fullname($acct->data['account_lid'],$acct->data['firstname'],$acct->data['lastname']);
}

				/*
 ** N - New
 ** R - Replied
 ** O - Old (read)
 ** F - Forwarded
     */
				if($message['status'] == 'N')
{
					$message['subject'] = '<b>' . $message['subject'] . '</b>';
					//$message['status'] = 'N';
					$message['date'] = '<b>' . $GLOBALS['phpgw']->common->show_date($message['date']) . '</b>';
					$message['from'] = '<b>' . $cached_names[$message['from']] . '</b>';
}
				else
{
					$message['date'] = $GLOBALS['phpgw']->common->show_date($message['date']);
					$message['from'] = $cached_names[$message['from']];
}

				if($message['status'] == 'O')
{
					//$message['status'] = '&nbsp;';
}

				$_messages[] = array(
					'id'      => $message['id'],
					'from'    => $message['from'],
					'status'  => $message['status'],
					'date'    => $message['date'],
					'subject' => $message['subject'],
					'content' => $message['content']
				);
}

			if(@is_null($_messages))
{
				return array();
}

			return $_messages;
                      }
		function read_message($message_id)
		{
			$message = $this->so->read_message((int)$message_id);

			$message['date'] = $GLOBALS['phpgw']->common->show_date($message['date']);

			if($message['from'] == -1)
			{
				$message['from']           = lang('Global Message');
				$message['global_message'] = True;
			}
			else
			{
				$acct = CreateObject('phpgwapi.accounts',$message['from']);
				$acct->read_repository();
				$message['from'] = $GLOBALS['phpgw']->common->display_fullname($acct->data['account_lid'],$acct->data['firstname'],$acct->data['lastname']);
			}

			return $message;
		}

		function read_message_for_reply($message_id,$type,$n_message='')
		{
			$message = $this->so->read_message((int)$message_id);

			$acct = CreateObject('phpgwapi.accounts',$message['from']);
			$acct->read_repository();

			if(!$n_message['content'])
			{
				$content_array = explode("\n",$message['content']);

				$new_content_array[] = ' ';
				$new_content_array[] = '> ' . $GLOBALS['phpgw']->common->display_fullname($acct->data['account_lid'],$acct->data['firstname'],$acct->data['lastname']) . ' wrote:';
				$new_content_array[] = '>';
				while(list(,$line) = each($content_array))
				{
					$new_content_array[] = '> ' . $line;
				}
				$message['content'] = implode("\n",$new_content_array);
			}

			$message['subject'] = $type . ': ' . $message['subject'];
			$message['from']    = $acct->data['account_lid'];

			return $message;
		}

		function delete_message($messages='')
		{
			if(!is_array($messages))
			{
				return False;
			}

			$this->so->db->transaction_begin();
			while(list(,$message_id) = each($messages))
			{
				$this->so->delete_message($message_id);
			}
			$this->so->db->transaction_commit();

			return True;
		}

		function reply($message_id='',$n_message='')
		{
			if(!$message_id)
			{
				return False;
			}

			$errors = $this->check_for_missing_fields($n_message);
			if(is_array($errors))
			{
				return $errors;
			}
			else
			{
				$this->so->send_message($n_message);
				return $this->so->update_message_status('R',$message_id);
			}
		}

		function forward($message_id='',$n_message='')
		{
			if(!$message_id)
			{
				return False;
			}

			$errors = $this->check_for_missing_fields($n_message);

			if(is_array($errors))
			{
				return $errors;
			}
			else
			{
				$this->so->send_message($n_message);
				return $this->so->update_message_status('F',$message_id);
			}
		}

		function total_messages($extra_where_clause = '')
		{
			return $this->so->total_messages($extra_where_clause);
		}

		function list_methods($_type='xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			*/
			if(is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'delete_message' => array(
							'function'  => 'delete_message',
							'signature' => array(array(xmlrpcBoolean,xmlrpcStruct)),
							'docstring' => lang('Delete a message.')
						),
						'read_message' => array(
							'function'  => 'read_message',
							'signature' => array(array(xmlrpcStruct,xmlrpcInt)),
							'docstring' => lang('Read a single message.')
						),
						'read_inbox' => array(
							'function'  => 'read_inbox',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Read a list of messages.')
						),
						'send_message' => array(
							'function'  => 'send_message',
							'signature' => array(array(xmlrpcBoolean,xmlrpcStruct)),
							'docstring' => lang('Send a message to a single recipient.')
						),
						'update_message_status' => array(
							'function'  => 'update_message_status',
							'signature' => array(array(xmlrpcBoolean,xmlrpcStruct)),
							'docstring' => lang('Update a message status')
						),
						'send_global_message' => array(
							'function'  => 'send_global_message',
							'signature' => array(array(xmlrpcBoolean,xmlrpcStruct)),
							'docstring' => lang('Send a global message.')
						),
						'reply' => array(
							'function'  => 'reply',
							'signature' => array(array(xmlrpcBoolean,xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Reply to a received message.')
						),
						'forward' => array(
							'function'  => 'forward',
							'signature' => array(array(xmlrpcBoolean,xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Forward a message to another user.')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		function get_messenger_users()
		{
			$users = array();
			$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts');
			foreach ($accounts as $account)
			{
				$GLOBALS['phpgw']->acl->account_id = $account['account_id'];
				$user_acl = $GLOBALS['phpgw']->acl->read_repository();
				foreach ($user_acl as $user_app)
				{
					if ($user_app[appname] == 'messenger' && $user_app['location'] == 'run' && $user_app['rights'] == 1)
					{
						$users[] = $account;
						continue 2;
					}
				}
			}

			return $users;
		}
	}
