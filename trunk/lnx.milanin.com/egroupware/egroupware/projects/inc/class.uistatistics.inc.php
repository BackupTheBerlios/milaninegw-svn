<?php
	/*******************************************************************\
	* eGroupWare - Projects                                             *
	* http://www.egroupware.org                                         *
	*                                                                   *
	* Project Manager                                                   *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* Written by Lars Kneschke [lkneschke@linux-at-work.de]             *
	* -----------------------------------------------                   *
	* Copyright 2000 - 2004 Free Software Foundation, Inc.              *
	* Copyright 2004 - 2004 Lars Kneschke                               *
	*                                                                   *
	* This program is free software; you can redistribute it and/or     *
	* modify it under the terms of the GNU General Public License as    *
	* published by the Free Software Foundation; either version 2 of    *
	* the License, or (at your option) any later version.               *
	*                                                                   *
	* This program is distributed in the hope that it will be useful,   *
	* but WITHOUT ANY WARRANTY; without even the implied warranty of    *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU  *
	* General Public License for more details.                          *
	*                                                                   *
	* You should have received a copy of the GNU General Public License *
	* along with this program; if not, write to the Free Software       *
	* Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.         *
	\*******************************************************************/
	/* $Id: class.uistatistics.inc.php,v 1.32.2.1 2004/11/06 12:15:56 ralfbecker Exp $ */
	/* $Source: /cvsroot/egroupware/projects/inc/class.uistatistics.inc.php,v $ */

	class uistatistics
	{
		var $action;
		var $grants;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'list_projects'	=> True,
			'list_users'	=> True,
			'user_stat'	=> True,
			'project_gantt'	=> True,
			'show_graph'	=> True,
			'show_stat'	=> True
		);

		function uistatistics()
		{
			$action = get_var('action',array('POST','GET'));

			$this->bostatistics				= CreateObject('projects.bostatistics');
			$this->boprojects				= $this->bostatistics->boprojects;
			$this->nextmatchs				= CreateObject('phpgwapi.nextmatchs');
			$this->sbox						= CreateObject('phpgwapi.sbox');
			$this->cats						= CreateObject('phpgwapi.categories');
			$this->account					= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->grants					= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->start					= $this->bostatistics->start;
			$this->query					= $this->bostatistics->query;
			$this->filter					= $this->bostatistics->filter;
			$this->order					= $this->bostatistics->order;
			$this->sort						= $this->bostatistics->sort;
			$this->cat_id					= $this->bostatistics->cat_id;
			$this->status					= $this->bostatistics->status;
			$this->siteconfig				= $this->bostatistics->boprojects->siteconfig;
		}

		function save_sessiondata($action)
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'filter'	=> $this->filter,
				'order'		=> $this->order,
				'sort'		=> $this->sort,
				'cat_id'	=> $this->cat_id,
				'status'	=> $this->status
			);
			$this->boprojects->save_sessiondata($data, $action);
		}

		function set_app_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

			$GLOBALS['phpgw']->template->set_var('lang_archiv',lang('archive'));
			$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_ttracker',lang('time tracker'));
			$GLOBALS['phpgw']->template->set_var('lang_roles',lang('roles'));

			$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_calculate',lang('Calculate'));
			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));
			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));
			$GLOBALS['phpgw']->template->set_var('lang_project',lang('Project'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Hours'));
			$GLOBALS['phpgw']->template->set_var('lang_jobs',lang('Jobs'));
			$GLOBALS['phpgw']->template->set_var('lang_activity',lang('Activity'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));
			$GLOBALS['phpgw']->template->set_var('lang_customer',lang('Customer'));
			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
			$GLOBALS['phpgw']->template->set_var('lang_firstname',lang('Firstname'));
			$GLOBALS['phpgw']->template->set_var('lang_lastname',lang('Lastname'));
			$GLOBALS['phpgw']->template->set_var('lang_employee',lang('Employee'));
			$GLOBALS['phpgw']->template->set_var('lang_billedonly',lang('Billed only'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_minperae',lang('Minutes per workunit'));
	    		$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per workunit'));
			$GLOBALS['phpgw']->template->set_var('lang_stat',lang('Statistic'));
			$GLOBALS['phpgw']->template->set_var('lang_userstats',lang('User statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_view',lang('view'));

			$GLOBALS['phpgw']->template->set_var('lang_gantt_chart',lang('gantt chart'));
			$GLOBALS['phpgw']->template->set_var('lang_show_chart',lang('show gantt chart'));
			$GLOBALS['phpgw']->template->set_var('lang_view_employees',lang('view employees'));

			$GLOBALS['phpgw']->template->set_var('lang_main',lang('Main project'));
			$GLOBALS['phpgw']->template->set_var('lang_number',lang('Project ID'));
			$GLOBALS['phpgw']->template->set_var('lang_url',lang('project url'));
		}

		function display_app_header()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'idots')
			{
				$GLOBALS['phpgw']->template->set_file(array('header' => 'header.tpl'));
				$GLOBALS['phpgw']->template->set_block('header','projects_header');
				$GLOBALS['phpgw']->template->set_block('header','projects_admin_header');

				if ($this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager'))
				{
					switch($this->siteconfig['accounting'])
					{
						case 'activity':
							$GLOBALS['phpgw']->template->set_var('link_accounting',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_activities&action=act'));                                                                                                         
							$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('Activities'));
							break;
						default:
							$GLOBALS['phpgw']->template->set_var('link_accounting',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_employees&action=accounting'));                                                                                                         
							$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('Accounting'));
					}
					$GLOBALS['phpgw']->template->set_var('link_budget',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_budget&action=mains'));
					$GLOBALS['phpgw']->template->set_var('lang_budget',lang('budget'));
					$GLOBALS['phpgw']->template->fp('admin_header','projects_admin_header');
				}

				$GLOBALS['phpgw']->template->set_var('link_jobs',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&action=subs'));
				$GLOBALS['phpgw']->template->set_var('link_hours',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('link_ttracker',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.ttracker'));
				$GLOBALS['phpgw']->template->set_var('link_statistics',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uistatistics.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('link_projects',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&action=mains'));

				$GLOBALS['phpgw']->template->fp('app_header','projects_header');
			}
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->set_app_langs();
		}

		function admin_header_info()
		{
			if ($this->boprojects->isprojectadmin('pad'))
			{
				$admin_header = '&nbsp;&gt;&nbsp;' . lang('administrator');
			}

			if ($this->boprojects->isprojectadmin('pmanager'))
			{
				$admin_header .= '&nbsp;&gt;&nbsp;' .  lang('manager');
			}

			if ($this->boprojects->isprojectadmin('psale'))
			{
				$admin_header .= '&nbsp;&gt;&nbsp;' .  lang('seller');
			}
			return $admin_header;
		}

		function status_format($status = '', $showarchive = True)
		{
			if (!$status)
			{
				$status = $this->status = 'active';
			}

			switch ($status)
			{
				case 'active':		$stat_sel[0]=' selected'; break;
				case 'nonactive':	$stat_sel[1]=' selected'; break;
				case 'archive':		$stat_sel[2]=' selected'; break;
			}

			$status_list = '<option value="active"' . $stat_sel[0] . '>' . lang('Active') . '</option>' . "\n"
						. '<option value="nonactive"' . $stat_sel[1] . '>' . lang('Nonactive') . '</option>' . "\n";

			if ($showarchive)
			{
				$status_list .= '<option value="archive"' . $stat_sel[2] . '>' . lang('Archive') . '</option>' . "\n";
			}
			return $status_list;
		}

		function list_projects()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));
			$pro_users	= get_var('pro_users',array('POST','GET'));
			$values		= get_var('values',array('POST','GET'));

			if($_POST['userstats'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=projects.uistatistics.list_users');
			}

			$pro_user = array();
			if($_POST['viewuser'])
			{
				if(is_array($values['project_id']))
				{
					$i = 0;
					foreach($values['project_id'] as $pro_id => $val)
					{
						$pro_user[$i] = $pro_id;
						$i++;
					}
				}
				else
				{
					$msg = lang('you have no projects selected');
				}
			}

			if($_POST['viewgantt'])
			{
				if(is_array($values['gantt_id']))
				{
					$i = 0;
					foreach($values['gantt_id'] as $pro_id => $val)
					{
						$gantt_user[$i] = $pro_id;
						$i++;
					}
					$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=projects.uistatistics.project_gantt&project_id='
														. implode(',',$gantt_user));
				}
				else
				{
					$msg = lang('you have no projects selected');
				}
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_main?lang('list jobs'):lang('list projects'));
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'stats_projectlist.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','user_list','users');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','user_cols','cols');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','project_main','main');

			$GLOBALS['phpgw']->template->set_var('msg',$msg);

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main);
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action=mains&project_id='
																						. $pro_main));
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->parse('main','project_main',True);
			}

			if (!$action)
			{
				$action = 'mains';
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uistatistics.list_projects',
				'pro_main'		=> $pro_main,
				'action'		=> $action,
				'cat_id'		=> $this->cat_id
			);

			if (!$this->start)
			{
				$this->start = 0;
			}

			$pro = $this->boprojects->list_projects(array('action' => $action,'parent' => $pro_main));

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

			if ($action == 'mains')
			{
				$action_list = '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
							. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';
			}
			else
			{
				$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
							. $this->boprojects->select_project_list(array('status' => $status, 'selected' => $pro_main)) . '</select>';
			}

			$GLOBALS['phpgw']->template->set_var('action_list',$action_list);
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format($this->status));

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

// ---------------- list header variable template-declarations --------------------------

			$GLOBALS['phpgw']->template->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'p_number',$this->order,'/index.php',$action=='mains'?lang('Project ID'):lang('job id'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_sdate',$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Start date'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_edate',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('Date due'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_coordinator',$this->nextmatchs->show_sort_order($this->sort,'coordinator',$this->order,'/index.php',$action=='mains'?lang('Coordinator'):lang('job manager'),$link_data));
			$GLOBALS['phpgw']->template->set_var('user_img',$GLOBALS['phpgw']->common->image('phpgwapi','users'));
			$GLOBALS['phpgw']->template->set_var('user_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

// -------------- end header declaration ---------------------------------------

			for ($i=0;$i<count($pro);$i++)
            {
				if(in_array($pro[$i]['project_id'],$pro_user))
				{
					$emps[$pro[$i]['project_id']] = $this->boprojects->get_employee_roles(array('project_id' => $pro[$i]['project_id'],'formatted' => True));
				}

				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------

				if ($action == 'mains')
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uistatistics.list_projects&pro_main='
									. $pro[$i]['project_id'] . '&action=subs');
				}
				else
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_hours&project_id='
									. $pro[$i]['project_id'] . '&action=hours&pro_main=' . $pro_main);
				}

				$GLOBALS['phpgw']->template->set_var(array
				(
					'number'	=> $pro[$i]['number'],
					'title'		=> ($pro[$i]['title']?$pro[$i]['title']:lang('browse')),
					'projects_url'	=> $projects_url,
					'sdate'		=> $pro[$i]['sdateout'],
					'edate'		=> $pro[$i]['edateout'],
					'coordinator'	=> $pro[$i]['coordinatorout'],
					'view_img'	=> $GLOBALS['phpgw']->common->image('phpgwapi','view'),
					'radio_user_checked'	=> $_POST['viewuser']?(in_array($pro[$i]['project_id'],$pro_user)?' checked':''):'',
					'project_id'	=> $pro[$i]['project_id']
				));

				$link_data['project_id'] = $pro[$i]['project_id'];
				$link_data['pro_users']	= $pro[$i]['project_id'];
				$link_data['menuaction'] = 'projects.uistatistics.list_projects';
				$GLOBALS['phpgw']->template->set_var('user_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$link_data['menuaction'] = 'projects.uiprojects.view_project';
				$GLOBALS['phpgw']->template->set_var('view_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$link_data['menuaction'] = 'projects.uistatistics.project_stat';
				$GLOBALS['phpgw']->template->set_var('stat',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('lang_gantt_entry',lang('gantt chart'));

				$GLOBALS['phpgw']->template->set_var('employee_list','');
				$GLOBALS['phpgw']->template->set_var('users','');
				if(is_array($emps[$pro[$i]['project_id']]))
				{
					foreach($emps[$pro[$i]['project_id']] as $e)
					{
						$GLOBALS['phpgw']->template->set_var('emp_name',$e['emp_name']);
						$GLOBALS['phpgw']->template->set_var('emp_roles',$e['role_name']);
						$GLOBALS['phpgw']->template->fp('users','user_list',True);
					}
					$GLOBALS['phpgw']->template->set_var('lang_name',lang('name'));
					$GLOBALS['phpgw']->template->set_var('lang_role',lang('role'));
					$GLOBALS['phpgw']->template->fp('employee_list','user_cols',True);
				}
				$GLOBALS['phpgw']->template->fp('list','projects_list',True);
			}

// ------------------------- end record declaration ------------------------

			$GLOBALS['phpgw']->template->set_var('lang_view_gantt',lang('view gantt chart'));
			$GLOBALS['phpgw']->template->set_var('lang_view_users',lang('view users'));

			$this->save_sessiondata('pstat');
			$GLOBALS['phpgw']->template->set_var('cols','');
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
		}

		function coordinator_format($employee = '')
		{
			if (! $employee)
			{
				$employee = $this->account;
			}

			$employees = $this->boprojects->employee_list();

			while (list($null,$account) = each($employees))
			{
				$coordinator_list .= '<option value="' . $account['account_id'] . '"';
				if($account['account_id'] == $employee)
				$coordinator_list .= ' selected';
				$coordinator_list .= '>' . $account['account_firstname'] . ' ' . $account['account_lastname']
										. ' [ ' . $account['account_lid'] . ' ]' . '</option>' . "\n";
			}
			return $coordinator_list;
		}

		function list_users()
		{
			$values	= $_POST['values'];

			$pro_user = array();
			if(is_array($values['account_id']))
			{
				$i = 0;
				foreach($values['account_id'] as $a_id => $val)
				{
					$pro_user[$i] = $a_id;
					$i++;
				}
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('User statistics');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('user_list_t' => 'stats_userlist.tpl'));
			$GLOBALS['phpgw']->template->set_block('user_list_t','user_list','list');
			$GLOBALS['phpgw']->template->set_block('user_list_t','pro_list','pro');
			$GLOBALS['phpgw']->template->set_block('user_list_t','pro_cols','cols');


			$link_data = array
			(
				'menuaction'	=> 'projects.uistatistics.list_users',
				'action'		=> 'ustat'
			);

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['query'] = $this->query;	// else nextmatch wont display it
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(1));

			if (!$this->start)
			{
				$this->start = 0;
			}
			$users = $this->bostatistics->get_users('accounts', $this->start, $this->sort, $this->order, $this->query);

// ------------- nextmatch variable template-declarations -------------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->bostatistics->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bostatistics->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->bostatistics->total_records,$this->start));

// ------------------------ end nextmatch template --------------------------------------

// --------------- list header variable template-declarations ---------------------------

			$GLOBALS['phpgw']->template->set_var('sort_lid',$this->nextmatchs->show_sort_order($this->sort,'account_lid',$this->order,'/index.php',lang('Username'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_firstname',$this->nextmatchs->show_sort_order($this->sort,'account_firstname',$this->order,'/index.php',lang('Firstname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_lastname',$this->nextmatchs->show_sort_order($this->sort,'account_lastname',$this->order,'/index.php',lang('Lastname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_stat',lang('Statistic'));

// ------------------------- end header declaration -------------------------------------

			for ($i=0;$i<count($users);$i++)
			{
				if(in_array($users[$i]['account_id'],$pro_user))
				{
					$pro[$users[$i]['account_id']] = $this->boprojects->get_employee_projects($users[$i]['account_id']);
				}

				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

// --------------------- template declaration for list records ---------------------------

				$GLOBALS['phpgw']->template->set_var(array
				(
					'lid'			=> $users[$i]['account_lid'],
					'firstname'		=> $users[$i]['account_firstname'],
					'lastname'		=> $users[$i]['account_lastname'],
					'radio_checked'	=> (in_array($users[$i]['account_id'],$pro_user)?' checked="1"':''),
					'account_id'	=> $users[$i]['account_id']
				));

				$GLOBALS['phpgw']->template->set_var('project_list','');
				$GLOBALS['phpgw']->template->set_var('pro','');
				if(is_array($pro[$users[$i]['account_id']]))
				{
					foreach($pro[$users[$i]['account_id']] as $p)
					{
						$GLOBALS['phpgw']->template->set_var('pro_name',$p['pro_name']);
						$GLOBALS['phpgw']->template->fp('pro','pro_list',True);
					}
					$GLOBALS['phpgw']->template->set_var('lang_name',lang('name'));
					$GLOBALS['phpgw']->template->fp('project_list','pro_cols',True);
				}

				$GLOBALS['phpgw']->template->set_var('lang_stat_entry',lang('Statistic'));
				$GLOBALS['phpgw']->template->fp('list','user_list',True);
			}

// ------------------------------- end record declaration ---------------------------------

			$GLOBALS['phpgw']->template->pfp('out','user_list_t',True);
			$this->save_sessiondata($action);
		}

		function user_stat()
		{
			$submit		= get_var('submit',array('POST'));
			$values		= get_var('values',array('POST','GET'));
			$account_id	= get_var('account_id',array('POST','GET'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uistatistics.user_stat',
				'action'		=> 'ustat',
				'account_id'	=> $account_id
			);

			if (! $account_id)
			{
				$phpgw->redirect_link('/index.php','menuaction=projects.uistatistics.list_users&action=ustat');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('User statistics')
														. $this->admin_header_info();
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('user_stat_t' => 'stats_userstat.tpl'));
			$GLOBALS['phpgw']->template->set_block('user_stat_t','user_stat','stat');

			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$cached_data = $this->boprojects->cached_accounts($account_id);
			$employee = $GLOBALS['phpgw']->strip_html($cached_data[$account_id]['firstname']
                                        . ' ' . $cached_data[$account_id]['lastname'] . ' ['
                                        . $cached_data[$account_id]['account_lid'] . ' ]');

			$GLOBALS['phpgw']->template->set_var('employee',$employee);

			$this->nextmatchs->alternate_row_color($GLOBALS['phpgw']->template);

			if (!$values['sdate'])
			{
				$values['smonth']	= 0;
				$values['sday']		= 0;
				$values['syear']	= 0;
			}
			else
			{
				$values['smonth']	= date('m',$values['sdate']);
				$values['sday']		= date('d',$values['sdate']); 
				$values['syear']	= date('Y',$values['sdate']);
			}

			if (!$values['edate'])
			{
				$values['emonth']	= 0;
				$values['eday']		= 0;
				$values['eyear']	= 0;
			}
			else
			{
				$values['emonth']	= date('m',$values['edate']);
				$values['eday']		= date('d',$values['edate']); 
				$values['eyear']	= date('Y',$values['edate']);
			}

			$GLOBALS['phpgw']->template->set_var('start_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[syear]',$values['syear']),
																							$this->sbox->getMonthText('values[smonth]',$values['smonth']),
																							$this->sbox->getDays('values[sday]',$values['sday'])));
			$GLOBALS['phpgw']->template->set_var('end_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[eyear]',$values['eyear']),
																							$this->sbox->getMonthText('values[emonth]',$values['emonth']),
																							$this->sbox->getDays('values[eday]',$values['eday'])));

// -------------- calculate statistics --------------------------

			$GLOBALS['phpgw']->template->set_var('billed','<input type="checkbox" name="values[billed]" value="True"'
										. ($values['billed'] == 'private'?' checked':'') . '>');

			$pro = $this->bostatistics->get_userstat_pro($account_id, $values);

			if (is_array($pro))
			{
				while (list($null,$userpro) = each($pro))
				{
					$summin = 0;
					$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
					$GLOBALS['phpgw']->template->set_var('e_project',$GLOBALS['phpgw']->strip_html($userpro['title']) . ' ['
											. $GLOBALS['phpgw']->strip_html($userpro['num']) . ']');
					$GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
					$GLOBALS['phpgw']->template->set_var('e_hours','&nbsp;');
					$GLOBALS['phpgw']->template->fp('stat','user_stat',True);

					$hours = $this->bostatistics->get_stat_hours('both', $account_id, $userpro['project_id'], $values); 
					for ($i=0;$i<=count($hours);$i++)
					{
						if ($hours[$i]['num'] != '')
						{
							$GLOBALS['phpgw']->template->set_var('e_project','&nbsp;');
							$GLOBALS['phpgw']->template->set_var('e_activity',$GLOBALS['phpgw']->strip_html($hours[$i]['descr']) . ' ['
													. $GLOBALS['phpgw']->strip_html($hours[$i]['num']) . ']');
							$summin += $hours[$i]['min'];
							$hrs = floor($hours[$i]['min']/60) . ':' . sprintf ("%02d",(int)($hours[$i]['min']-floor($hours[$i]['min']/60)*60));
							$GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
							$GLOBALS['phpgw']->template->fp('stat','user_stat',True);
						}
					}

					$GLOBALS['phpgw']->template->set_var('e_project','&nbsp;');
					$GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
					$hrs = floor($summin/60) . ':' . sprintf ("%02d",(int)($summin-floor($summin/60)*60)); 
					$GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
					$GLOBALS['phpgw']->template->fp('stat','user_stat',True);
				}
			}

			$allhours = $this->bostatistics->get_stat_hours('account', $account_id, $project_id ='', $values);

			$summin=0;
			$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
			$GLOBALS['phpgw']->template->set_var('e_project','<b>' . lang('Overall') . '</b>');
			$GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('e_hours','&nbsp;');
			$GLOBALS['phpgw']->template->fp('stat','user_stat',True);

			if (is_array($allhours))
			{
				while (list($null,$userall) = each($allhours))
				{
					$GLOBALS['phpgw']->template->set_var('e_project','&nbsp;');
					$GLOBALS['phpgw']->template->set_var('e_activity',$GLOBALS['phpgw']->strip_html($userall['descr']) . ' ['
													. $GLOBALS['phpgw']->strip_html($userall['num']) . ']');
					$summin += $userall['min'];
					$hrs = floor($userall['min']/60) . ':' . sprintf ("%02d",(int)($userall['min']-floor($userall['min']/60)*60));
					$GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
					$GLOBALS['phpgw']->template->fp('stat','user_stat',True);
				}
			}
			
			$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
			$GLOBALS['phpgw']->template->set_var('e_project','<b>' . lang('Sum') . '</b>');
			$GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
			$hrs = floor($summin/60) . ':' . sprintf ("%02d",(int)($summin-floor($summin/60)*60)); 
			$GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
			$GLOBALS['phpgw']->template->fp('stat','user_stat',True);
			$GLOBALS['phpgw']->template->pfp('out','user_stat_t',True);
		}

		function project_gantt()
		{
			$showResources	= get_var('show_resources',array('POST'));
			$showMilestones	= get_var('show_milestones',array('POST'));
			
			$project_id	= get_var('project_id',array('GET','POST'));
 			$sdate		= get_var('sdate',array('GET','POST'));
			$edate		= get_var('edate',array('GET','POST'));

			if (! $project_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=projects.uistatistics.list_projects&action=mains');
			}
			else
			{
				$project_array = explode(',',$project_id);
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uistatistics.project_gantt',
				'action'	=> $action,
				'project_id'	=> $project_id
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('gantt chart') . $this->admin_header_info();

			$jscal = CreateObject('phpgwapi.jscalendar');	// before phpgw_header() !!!
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('project_stat' => 'stats_gant.tpl'));

			if(is_array($sdate))
			{
				$start_array	= $jscal->input2date($sdate['str']);
				$start_val	= $start_array['raw'];
			}

			if(is_array($edate))
			{
				$end_array	= $jscal->input2date($edate['str']);
				$end_val	= $end_array['raw'];
			}
			
			$projectData = $this->boprojects->read_single_project($project_array[0]);
			
			if($start_val)
				$start	= $start_val;
			else
				$start	= $projectData['sdate'];
			#$start	= $start_val?$start_val:mktime(12,0,0,date('m'),date('d'),date('Y'));
			#$end	= $end_val?$end_val:mktime(12,0,0,date('m'),date('d')+30,date('Y'));
			if($end_val)
			{
				$end	= $end_val;
			}
			else
			{
				$end	= $projectData['edate']?$projectData['edate']:mktime(12,0,0,date('m'),date('d')+30,date('Y'));;
			}

			$GLOBALS['phpgw']->template->set_var('sdate_select',$jscal->input('sdate[str]',$start));
			$GLOBALS['phpgw']->template->set_var('edate_select',$jscal->input('edate[str]',$end));

			$GLOBALS['phpgw']->template->set_var('project_id',$project_id);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_show_milestones', lang('show milestones'));
			$GLOBALS['phpgw']->template->set_var('lang_show_resources', lang('show planned resources'));
			if($showMilestones == 'true')
			{
				$GLOBALS['phpgw']->template->set_var('show_milestones_checked','checked');
			}
			if($showResources == 'true')
			{
				$GLOBALS['phpgw']->template->set_var('show_resources_checked','checked');
			}

			$link_data = array
			(
				'menuaction'		=> 'projects.uistatistics.show_graph',
				'project_id'		=> $project_id,
				'start'			=> $start,
				'end'			=> $end,
				'show_resources'	=> $showResources,
				'show_milestones'	=> $showMilestones
			);
			$GLOBALS['phpgw']->template->set_var('pix_src',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->pfp('out','project_stat');
		}
		
		function show_graph()
		{
			$project_id	= get_var('project_id',array('GET'));
			$start		= get_var('start',array('GET'));
			$end		= get_var('end',array('GET'));
			$showMilestones	= get_var('show_milestones',array('GET'));
			$showResources	= get_var('show_resources',array('GET'));

			if (!$project_id)
			{
				return false;
			}
			else
			{
				$project_array = explode(',',$project_id);
			}
			$this->bostatistics->show_graph
			(
				array
				(
					'project_array'		=> $project_array,
					'sdate'			=> $start, 
					'edate'			=> $end,
					'showMilestones'	=> $showMilestones,
					'showResources'		=> $showResources
				)
			);
			
			return true;
		}

		function show_stat($project_id)
		{
			$this->bostatistics->show_graph($project_id);
		}

	}
?>
