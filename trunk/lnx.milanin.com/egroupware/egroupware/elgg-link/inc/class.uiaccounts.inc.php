<?php
	/**************************************************************************\
	* eGroupWare - account administration                                      *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: class.uiaccounts.inc.php,v 1.58.2.3 2005/02/15 08:29:29 ralfbecker Exp $ */

	class uiaccounts
	{
		var $public_functions = array
		(
			'list_groups'		=> True,
			'list_users'		=> True,
			'view_user'			=> True,
		);

		var $bo;
		var $nextmatchs;
		var $apps_with_acl = array(
			'addressbook' => True,
			'todo'        => True,
			'calendar'    => True,
			'notes'       => True,
			'projects'    => True,
			'phonelog'    => True,
			'infolog'     => True,
			'filemanager' => True,
			'tts'         => True,
			'bookmarks'   => True,
			'img'         => True,
			'netsaint'    => True,
			'inv'         => True,
			'phpbrain'    => True,
		);

		function uiaccounts()
		{
			$this->bo = createobject('elgg-link.boaccounts');
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');
			@set_time_limit(300);
		}

		function row_action($action,$type,$account_id)
		{
			return '<a href="'.$GLOBALS['phpgw']->link('/index.php',Array(
				'menuaction' => 'elgg-link.uiaccounts.'.$action.'_'.$type,
				'account_id' => $account_id
			)).'"> '.lang($action).' </a>';
		}

		function list_groups()
		{
			if ($GLOBALS['phpgw']->acl->check('group_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/admin/index.php'));
			}

			$GLOBALS['cd'] = ($_GET['cd']?$_GET['cd']:0);

			if(isset($_POST['query']))
			{
				// limit query to limit characters
				if(eregi('^[a-z_0-9]+$',$_POST['query']))
					$GLOBALS['query'] = $_POST['query'];
			}
			
			if(isset($_POST['start']))
			{
				$start = (int)$_POST['start'];
			}
			else
			{
				$start = 0;
			}

			switch($_GET['order'])
			{
				case 'account_lid':
					$order = $_GET['order'];
					break;
				default:
					$order = 'account_lid';
					break;
			}

			switch($_GET['sort'])
			{
				case 'ASC':
				case 'DESC':
					$sort = $_GET['sort'];
					break;
				default:
					$sort = 'ASC';
					break;
			}
			
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			/*if(!@is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file('jscode','openwindow','admin');
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['admin']['title'].' - '.
				lang('User groups');*/
			$GLOBALS['phpgw']->common->phpgw_header();

			$p = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$p->set_file(
				array(
					'groups'   => 'groups.tpl'
				)
			);
			$p->set_block('groups','list','list');
			$p->set_block('groups','row','row');
			$p->set_block('groups','row_empty','row_empty');

			if (! $GLOBALS['phpgw']->acl->check('account_access',2,'admin'))
			{
				$account_info = $GLOBALS['phpgw']->accounts->get_list('groups',$start,$sort, $order, $GLOBALS['query']);
			}
			else
			{
				$account_info = $GLOBALS['phpgw']->accounts->get_list('groups',$start,$sort, $order);
			}
			$total = $GLOBALS['phpgw']->accounts->total;

			$var = Array(
				'th_bg'             => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'left_next_matchs'  => $this->nextmatchs->left('/index.php',$start,$total,'menuaction=elgg-link.uiaccounts.list_groups'),
				'right_next_matchs' => $this->nextmatchs->right('/index.php',$start,$total,'menuaction=elgg-link.uiaccounts.list_groups'),
				'lang_groups' => lang('%1 - %2 of %3 user groups',$start+1,$start+count($account_info),$total),
				'sort_name'     => $this->nextmatchs->show_sort_order($sort,'account_lid',$order,'/index.php',lang('name'),'menuaction=elgg-link.uiaccounts.list_groups'),
				'header_edit'   => lang('Edit'),
				'header_delete' => lang('Delete')
			);
			$p->set_var($var);

			if (!count($account_info) || !$total)
			{
				$p->set_var('message',lang('No matches found'));
				$p->parse('rows','row_empty',True);
			}
			else
			{
				if (! $GLOBALS['phpgw']->acl->check('group_access',8,'admin'))
				{
					$can_view = True;
				}

				/*if (! $GLOBALS['phpgw']->acl->check('group_access',16,'admin'))
				{
					$can_edit = True;
				}

				if (! $GLOBALS['phpgw']->acl->check('group_access',32,'admin'))
				{
					$can_delete = True;
				}
                                */
				foreach($account_info as $account)
				{
					$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
					$var = Array(
						'tr_color'    => $tr_color,
						'group_name'  => (!$account['account_lid']?'&nbsp;':$account['account_lid']),
						'delete_link' => $this->row_action('delete','group',$account['account_id'])
					);
					$p->set_var($var);
					$p->set_var('edit_link','&nbsp;');
					$p->set_var('delete_link','&nbsp;');

					$p->fp('rows','row',True);

				}
			}
			$var = Array(
				/*'new_action'    => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiaccounts.add_group'),*/
				'search_action' => $GLOBALS['phpgw']->link('/index.php','menuaction=elgg-link.uiaccounts.list_groups')
			);
			$p->set_var($var);

			/*if (! $GLOBALS['phpgw']->acl->check('group_access',4,'admin'))
			{
				$p->set_var('input_add','<input type="submit" value="' . lang('Add') . '">');
			}

			if (! $GLOBALS['phpgw']->acl->check('group_access',2,'admin'))
			{*/
				$p->set_var('input_search',lang('Search') . '&nbsp;<input name="query" value="'.htmlspecialchars(stripslashes($GLOBALS['query'])).'">');
			//}

			$p->pfp('out','list');
		}

		function list_users($param_cd='')
		{
			if ($GLOBALS['phpgw']->acl->check('account_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/admin/index.php'));
			}
			if (!is_object($GLOBALS['phpgw']->html))
			{
				$GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
			}

			if($param_cd)
			{
				$cd = $param_cd;
			}
			
			if(isset($_REQUEST['query']))
			{
				// limit query to limit characters
				if(eregi('^[a-z_0-9]+$',$_REQUEST['query']))
					$GLOBALS['query'] = $_REQUEST['query'];
			}
			
			if(isset($_REQUEST['start']))
			{
				$start = (int)$_REQUEST['start'];
			}
			else
			{
				$start = 0;
			}

			switch($_REQUEST['order'])
			{
				case 'account_lastname':
				case 'account_firstname':
                                case 'account_occupation':
                                case 'account_industry':
				case 'account_membership_date':
					$order = $_REQUEST['order'];
					break;
				default:
					$order = 'account_firstname';
					break;
			}

			switch($_REQUEST['sort'])
			{
				case 'ASC':
				case 'DESC':
					$sort = $_REQUEST['sort'];
					break;
				default:
					$sort = 'ASC';
					break;
			}

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			/*if(!@is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file('jscode','openwindow','admin');
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['admin']['title'].' - '.
				lang('User accounts');*/
				
			$GLOBALS['phpgw']->common->phpgw_header();
			$p = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);

			$p->set_file(
				Array(
					'list' => 'accounts.tpl'
				)
			);
			$p->set_block('list','row','rows');
			$p->set_block('list','row_empty','row_empty');
			$p->set_block('list','letter_search','letter_search_cells');
                        //$_type='both',$start = '',$sort = '',$order='',$query='',$offset= '',$query_type='',$offline=TRUE
			$search_param = array(
				'type' => 'accounts',
				'start' => $start,
				'sort' => $sort,
				'order' => $order,
				'query' => 'query',
				'offset' =>'',
				'query_type' => $_REQUEST['query_type'],
				'offline' => TRUE
			);
			if (!$GLOBALS['phpgw']->acl->check('account_access',2,'admin'))
			{
				$search_param['query'] = $GLOBALS['query'];
			}
			$account_info = $GLOBALS['phpgw']->accounts->get_online_members(
                                'accounts',
				$start,
				$sort,
				$order,
				$GLOBALS['query'],
				'',
				$_REQUEST['query_type'],
				TRUE
                        );
			$total = $GLOBALS['phpgw']->accounts->total;

			$link_data = array(
				'menuaction' => 'elgg-link.uiaccounts.list_users',
				'group_id'   => $_REQUEST['group_id'],
				'query_type' => $_REQUEST['query_type'],
			);
			$uiaccountsel = CreateObject('phpgwapi.uiaccountsel');
			$p->set_var(array(
				'left_next_matchs'   => $this->nextmatchs->left('/index.php',$start,$total,$link_data),
				'lang_showing' => ($_REQUEST['group_id'] ? $GLOBALS['phpgw']->common->grab_owner_name($_REQUEST['group_id']).': ' : '').
					($GLOBALS['query'] ? lang("Search %1 '%2'",lang($uiaccountsel->query_types[$_REQUEST['query_type']]),$GLOBALS['query']).': ' : '')
					.$this->nextmatchs->show_hits($total,$start),
				'right_next_matchs'  => $this->nextmatchs->right('/index.php',$start,$total,$link_data),
				'lang_lastname'      => $this->nextmatchs->show_sort_order($sort,'account_lastname',$order,'/index.php',lang('last name'),$link_data),
				'lang_firstname'     => $this->nextmatchs->show_sort_order($sort,'account_firstname',$order,'/index.php',lang('first name'),$link_data),
				'lang_industry'=>				$this->nextmatchs->show_sort_order($sort,'account_industry',$order,'/index.php',lang('industry'),$link_data),
				'lang_occupation'=>				$this->nextmatchs->show_sort_order($sort,'account_occupation',$order,'/index.php',lang('occupation'),$link_data),
				'lang_membership_date' => $this->nextmatchs->show_sort_order($sort,'account_membership_date',$order,'/index.php',lang('membership_date'),$link_data),
				'lang_linkedin' => lang('LinkedIn ID'),
				'lang_weblog' => lang('view weblog'),
				'lang_full_profile' => lang('view full profile'),
				'lang_send_message' => lang('send a message'),
				'lang_view'    => lang('view'),
				'lang_search'  => lang('search')
			));
			$link_data += array(
				'order'      => $order,
				'sort'       => $sort,
			);
			$p->set_var(array(
				'query_type' => is_array($uiaccountsel->query_types) ? $GLOBALS['phpgw']->html->select('query_type',$_REQUEST['query_type'],$uiaccountsel->query_types) : '',
				'lang_group' => lang('group'),
				'group' => $uiaccountsel->selection('group_id','admin_uiaccount_listusers_group_id',$_REQUEST['group_id'],'groups',0,False,'','this.form.submit();',lang('all')),
				'accounts_url' => $GLOBALS['phpgw']->link('/index.php',$link_data),
			));
			$letters = lang('alphabet');
			$letters = explode(',',substr($letters,-1) != '*' ? $letters : 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z');
			$link_data['query_type'] = 'start';
			foreach($letters as $letter)
			{
				$link_data['query'] = $letter;
				$p->set_var(array(
					'letter' => $letter,
					'link'   => $GLOBALS['phpgw']->link('/index.php',$link_data),
					'class'  => $GLOBALS['query'] == $letter && $_REQUEST['query_type'] == 'start' ? 'letter_box_active' : 'letter_box',
				));
				$p->fp('letter_search_cells','letter_search',True);
			}
			unset($link_data['query']);
			unset($link_data['query_type']);
			$p->set_var(array(
				'letter' => lang('all'),
				'link'   => $GLOBALS['phpgw']->link('/index.php',$link_data),
				'class'  => $_REQUEST['query_type'] != 'start' || !in_array($GLOBALS['query'],$letters) ? 'letter_box_active' : 'letter_box',
			));
			$p->fp('letter_search_cells','letter_search',True);

			/*if (! $GLOBALS['phpgw']->acl->check('account_access',4,'admin'))
			{
				$p->set_var('new_action',$GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiaccounts.add_user'));
				$p->set_var('input_add','<input type="submit" value="' . lang('Add') . '">');
			}
*/
			if (!count($account_info) || !$total)
			{
				$p->set_var('message',lang('No matches found'));
				$p->parse('rows','row_empty',True);
			}
			else
			{
				
					$can_view = True;
				

				foreach($account_info as $account)
				{
					$p->set_var('class',$this->nextmatchs->alternate_row_color('',True));
                                        if ($account['account_session'] == 1){
                                          $p->set_var('online','font-weight: bold');
                                        }else{
                                          $p->set_var('online','font-weight: normal');
                                        }
					$p->set_var($account);
					$p->set_var('row_linkedin',( ($account['account_linkedin'] > 0) ?
                                        '<a href="https://www.linkedin.com/profile?viewProfile=&key='.
                                        $account['account_linkedin'].
                                        '" title="'.lang('view')." ".lang('LinkedIn')." ".lang('profile').'"'.
                                        ' target=_blank>'.
                                        '<img src="/egroupware/elgg-link/templates/default/images/linkedin_logo.gif"/>'.
                                        '</a>' 
                                        : '')
                                        );
					//echo '<!--'.print_r($account,1).'-->';
					$p->set_var('row_edit','&nbsp;');
					$p->set_var('row_delete','&nbsp;');
					$p->set_var('row_view',lang('view'));
					$p->set_var('profile_link','/members/'.$account['account_lid']);
					$p->set_var('weblog_link','/members/'.$account['account_lid']."/weblog");
					$p->set_var('message_link',
                                          $GLOBALS['phpgw']->link('/index.php',Array(
                                            'menuaction' => 'messenger.uimessenger.compose',
                                            'message_to' => $account['account_lid'])
                                          )
                                        );
                                        //$this->row_action('view','user',$account['account_id']));
					$p->parse('rows','row',True);
				}
			}		// End else
			$p->pfp('out','list');
		}

		
		function view_user()
		{
			if ($GLOBALS['phpgw']->acl->check('account_access',8,'admin') || ! $_GET['account_id'])
			{
				$this->list_users();
				return False;
			}
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			if(!@is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file('jscode','openwindow','admin');
			$GLOBALS['phpgw']->common->phpgw_header();

			$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$t->set_unknowns('remove');
			$t->set_file(
				Array(
					'account' => 'account_form.tpl'
				)
			);
			$t->set_block('account','form','form');
			$t->set_block('account','form_logininfo');
			$t->set_block('account','link_row');

			$var = Array(
				'th_bg'        => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'tr_color1'    => $GLOBALS['phpgw_info']['theme']['row_on'],
				'tr_color2'    => $GLOBALS['phpgw_info']['theme']['row_off'],
				'lang_action'  => lang('View user account'),
				'lang_loginid' => lang('LoginID'),
				'lang_linkedin' => lang('LinkedIn ID'),
				'lang_membership_date' => lang('Member since'),
				'lang_account_active'   => lang('Account active'),
				'lang_lastname'      => lang('Last Name'),
				'lang_groups'        => lang('Groups'),
				'lang_anonymous'     => lang('Anonymous user (not shown in list sessions)'),
				'lang_changepassword'=> lang('Can change password'),
				'lang_firstname'     => lang('First Name'),
				'lang_lastlogin'     => lang('Last login'),
				'lang_lastloginfrom' => lang('Last login from'),
				'lang_expires' => lang('Expires')
			);

			$t->parse('password_fields','form_logininfo',True);

			$account = CreateObject('phpgwapi.accounts',(int)$_GET['account_id'],'u');
			$userData = $account->read_repository();

			$var['account_lid']       = $userData['account_lid'];
			$var['account_firstname'] = $userData['firstname'];
			$var['account_lastname']  = $userData['lastname'];
			$var['account_linkedin']  = '<a href="https://www.linkedin.com/profile?viewProfile=&key='
                                                    .$userData['linkedin'].'">'.$userData['linkedin'].'</a>';
                        $var['account_membership_date']= $userData['membership_date'];

			$acl = CreateObject('phpgwapi.acl',(int)$_GET['account_id']);
			$var['anonymous']         = $acl->check('anonymous',1,'phpgwapi') ? '&nbsp;&nbsp;X' : '&nbsp;';
			$var['changepassword']    = $acl->check('changepassword',0xFFFF,'preferences') ? '&nbsp;&nbsp;X' : '&nbsp;';
			unset($acl);

			if ($userData['status'])
			{
				$var['account_status'] = lang('Enabled');
			}
			else
			{
				$var['account_status'] = '<b>' . lang('Disabled') . '</b>';
			}

			// Last login time
			if ($userData['lastlogin'])
			{
				$var['account_lastlogin'] = $GLOBALS['phpgw']->common->show_date($userData['lastlogin']);
			}
			else
			{
				$var['account_lastlogin'] = lang('Never');
			}

			// Last login IP
			if ($userData['lastloginfrom'])
			{
				$var['account_lastloginfrom'] = $userData['lastloginfrom'];
			}
			else
			{
				$var['account_lastloginfrom'] = lang('Never');
			}

			// Account expires
			if ($userData['expires'] != -1)
			{
				$var['input_expires'] = $GLOBALS['phpgw']->common->show_date($userData['expires']);
			}
			else
			{
				$var['input_expires'] = lang('Never');
			}

			// Find out which groups they are members of
			$usergroups = $account->membership((int)$_GET['account_id']);
			if(!@is_array($usergroups))
			{
				$var['groups_select'] = lang('None');
			}
			else
			{
				while (list(,$group) = each($usergroups))
				{
					$group_names[] = $group['account_name'];
				}
				$var['groups_select'] = implode(', ',$group_names);
			}

			$account_lastlogin      = $userData['account_lastlogin'];
			$account_lastloginfrom  = $userData['account_lastloginfrom'];
			$account_status         = $userData['account_status'];

			// create list of available app
			$i = 0;

			$availableApps = $GLOBALS['phpgw_info']['apps'];
			@asort($availableApps);
			@reset($availableApps);
			foreach($availableApps as $app => $data) 
			{
				if ($data['enabled'] && $data['status'] != 2) 
				{
					$perm_display[$i]['appName'] = $app;
					$perm_display[$i]['title']   = $data['title'];
					$i++;
				}
			}

			// create apps output
			$apps = CreateObject('phpgwapi.applications',(int)$_GET['account_id']);
			$db_perms = $apps->read_account_specific();

			@reset($db_perms);

			for ($i=0;$i<count($perm_display);$i++)
			{
				if ($perm_display[$i]['title'])
				{
					$part1 = sprintf("<td>%s</td><td>%s</td>",$perm_display[$i]['title'],($_userData['account_permissions'][$perm_display[$i]['appName']] || $db_perms[$perm_display[$i]['appName']]?'&nbsp;&nbsp;X':'&nbsp'));
				}

				$i++;

				if ($perm_display[$i]['title'])
				{
					$part2 = sprintf("<td>%s</td><td>%s</td>",$perm_display[$i]['title'],($_userData['account_permissions'][$perm_display[$i]['appName']] || $db_perms[$perm_display[$i]['appName']]?'&nbsp;&nbsp;X':'&nbsp'));
				}
				else
				{
					$part2 = '<td colspan="2">&nbsp;</td>';
				}

				$appRightsOutput .= sprintf("<tr bgcolor=\"%s\">$part1$part2</tr>\n",$GLOBALS['phpgw_info']['theme']['row_on']);
			}

			$var['permissions_list'] = $appRightsOutput;

			// create the menu on the left, if needed
//			$menuClass = CreateObject('admin.uimenuclass');
			// This is now using ExecMethod()
			$var['rows'] = ExecMethod('admin.uimenuclass.createHTMLCode','view_user');
			$t->set_var($var);
			$t->pfp('out','form');
		}

		
	}
?>
