<?php
	/*******************************************************************\
	* eGroupWare - Projects                                             *
	* http://www.egroupware.org                                         *
	*                                                                   *
	* Project Manager                                                   *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright 2000 - 2004 Free Software Foundation, Inc               *
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
	/* $Id: class.uiprojecthours.inc.php,v 1.42.2.2 2004/11/06 12:15:55 ralfbecker Exp $ */

	class uiprojecthours
	{
		var $grants;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $state;
		var $cat_id;
		var $project_id;

		var $public_functions = array
		(
			'list_hours'	=> True,
			'edit_hours'	=> True,
			'delete_hours'	=> True,
			'view_hours'	=> True,
			'list_projects'	=> True,
			'ttracker'		=> True,
			'edit_ttracker'	=> True
		);

		function uiprojecthours()
		{
			$this->bohours					= CreateObject('projects.boprojecthours');
			$this->boprojects				= $this->bohours->boprojects;
			$this->nextmatchs				= CreateObject('phpgwapi.nextmatchs');
			$this->sbox					= CreateObject('phpgwapi.sbox');
			$this->account					= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->grants					= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->start					= $this->bohours->start;
			$this->query					= $this->bohours->query;
			$this->filter					= $this->bohours->filter;
			$this->order					= $this->bohours->order;
			$this->sort						= $this->bohours->sort;
			$this->status					= $this->bohours->status;
			$this->state					= $this->bohours->state;
			$this->cat_id					= $this->bohours->cat_id;
			$this->project_id				= $this->bohours->project_id;
			$this->siteconfig				= $this->bohours->siteconfig;
		}

		function save_sessiondata($action)
		{
			$data = array
			(
				'start'			=> $this->start,
				'query'			=> $this->query,
				'filter'		=> $this->filter,
				'order'			=> $this->order,
				'sort'			=> $this->sort,
				'status'		=> $this->status,
				'state'			=> $this->state,
				'project_id'	=> $this->project_id,
				'cat_id'		=> $this->cat_id
			);
			$this->boprojects->save_sessiondata($data,$action);
		}

		function set_app_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

			$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));
			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));
			$GLOBALS['phpgw']->template->set_var('lang_access',lang('Private'));

			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_jobs',lang('Jobs'));
			$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_ttracker',lang('time tracker'));
			$GLOBALS['phpgw']->template->set_var('lang_archiv',lang('archive'));
			$GLOBALS['phpgw']->template->set_var('lang_roles',lang('roles'));

			$GLOBALS['phpgw']->template->set_var('lang_number',lang('Project ID'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));

			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('done'));
			$GLOBALS['phpgw']->template->set_var('lang_apply',lang('apply'));
			$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));

			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));
			$GLOBALS['phpgw']->template->set_var('lang_customer',lang('Customer'));
			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));

			$GLOBALS['phpgw']->template->set_var('lang_date',lang('date'));
			$GLOBALS['phpgw']->template->set_var('lang_time',lang('time'));

			$GLOBALS['phpgw']->template->set_var('lang_activity',lang('Activity'));
			$GLOBALS['phpgw']->template->set_var('lang_costtype',lang('Costtype'));
			$GLOBALS['phpgw']->template->set_var('lang_project',lang('Project'));
			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Short description'));
			$GLOBALS['phpgw']->template->set_var('lang_remark',lang('Remark'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_employee',lang('Employee'));
			$GLOBALS['phpgw']->template->set_var('lang_work_date',lang('Work date'));
			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End date'));
			$GLOBALS['phpgw']->template->set_var('lang_work_time',lang('Work time'));
			$GLOBALS['phpgw']->template->set_var('lang_start_time',lang('Start time'));
			$GLOBALS['phpgw']->template->set_var('lang_end_time',lang('End time'));
			$GLOBALS['phpgw']->template->set_var('lang_select_project',lang('Select project'));

			$GLOBALS['phpgw']->template->set_var('lang_minperae',lang('Minutes per workunit'));
			$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per hour/workunit'));

			$GLOBALS['phpgw']->template->set_var('lang_till',lang('till'));
			$GLOBALS['phpgw']->template->set_var('lang_from',lang('from'));
			$GLOBALS['phpgw']->template->set_var('lang_entry',lang('entry'));

			$GLOBALS['phpgw']->template->set_var('lang_url',lang('project url'));
			$GLOBALS['phpgw']->template->set_var('lang_main',lang('Main project'));

			$GLOBALS['phpgw']->template->set_var('lang_planned',lang('planned'));
			$GLOBALS['phpgw']->template->set_var('lang_used',lang('used'));
			$GLOBALS['phpgw']->template->set_var('lang_used_total',lang('used total'));
			$GLOBALS['phpgw']->template->set_var('lang_available',lang('available'));

			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_plus_jobs',lang('+ jobs'));

			$GLOBALS['phpgw']->template->set_var('lang_budget_planned',lang('budget planned'));

			$GLOBALS['phpgw']->template->set_var('lang_used_billable',lang('used billable'));
			$GLOBALS['phpgw']->template->set_var('lang_used_not_billable',lang('used not billable'));

			$GLOBALS['phpgw']->template->set_var('lang_utime_billable',lang('time used billable'));

			$GLOBALS['phpgw']->template->set_var('lang_total_time',lang('time used total'));

			$GLOBALS['phpgw']->template->set_var('lang_ttracker_actions',lang('time tracking actions'));
			$GLOBALS['phpgw']->template->set_var('lang_manuell_entries',lang('manuell entries'));
			$GLOBALS['phpgw']->template->set_var('lang_billable',lang('billable'));
			$GLOBALS['phpgw']->template->set_var('lang_time_of_journey',lang('time of journey'));
			$GLOBALS['phpgw']->template->set_var('lang_distance',lang('distance'));
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
					$GLOBALS['phpgw']->template->fp('admin_header','projects_admin_header');
				}

				$GLOBALS['phpgw']->template->set_var('link_jobs',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&action=subs'));
				$GLOBALS['phpgw']->template->set_var('link_hours',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('link_ttracker',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.ttracker'));
				$GLOBALS['phpgw']->template->set_var('link_statistics',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uistatistics.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('link_projects',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('link_archiv',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.archive&action=amains'));
				$GLOBALS['phpgw']->template->fp('app_header','projects_header');
			}
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->set_app_langs();
		}

		function list_projects()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));

			if ($_GET['cat_id'])
			{
				$this->cat_id = $_GET['cat_id'];
			}

			if (!$action)
			{
				$action = 'mains';
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.list_projects',
				'pro_main'		=> $pro_main,
				'action'		=> $action
			);

			$pro = $this->boprojects->list_projects(array('action' => $action,'parent' => $pro_main,'page' => 'hours'));

			if($action=='subs' && !is_array($pro))
			{
					$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=projects.uiprojecthours.list_hours&project_id='
													. $pro_main . '&action=hours');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_main?lang('list jobs'):lang('list projects'));

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'list_pro_hours.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','project_main','main');

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'hours');
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action=mains&project_id='
																						. $pro_main));
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('utime_main',$this->boprojects->formatTime($main['uhours_jobs']));
				$GLOBALS['phpgw']->template->set_var('ptime_main',$this->boprojects->formatTime($main['ptime']));
				$GLOBALS['phpgw']->template->set_var('atime_main',$this->boprojects->formatTime($main['ahours_jobs']));
				$GLOBALS['phpgw']->template->parse('main','project_main',True);
			}

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

			if ($action == 'mains')
			{
				$action_list= '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
							. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';
			}
			else
			{
				$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
							. $this->boprojects->select_project_list(array('status' => $this->status, 'selected' => $pro_main)) . '</select>';
			}

			$GLOBALS['phpgw']->template->set_var('action_list',$action_list);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format_pro($this->status));

// ---------------- list header variable template-declarations --------------------------

			$GLOBALS['phpgw']->template->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'p_number',$this->order,'/index.php',(isset($pro_main)?lang('job id'):lang('Project ID')),$link_data));

			$GLOBALS['phpgw']->template->set_var('sort_planned',$this->nextmatchs->show_sort_order($this->sort,'time_planned',$this->order,'/index.php',lang('planned'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));

// -------------- end header declaration ---------------------------------------

			for ($i=0;$i<count($pro);$i++)
            		{
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------

				// we call this page only for the main projcts now
				$link_data['project_id'] = $pro[$i]['project_id'];
				#if ($action == 'mains')
				#{
				#	$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_projects&pro_main='
				#				. $pro[$i]['project_id'] . '&action=subs');
				#}
				#else
				#{
					$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_hours&project_id='
									. $pro[$i]['project_id'] . '&action=hours' . '&pro_main=' . $pro_main);
				#}

				$GLOBALS['phpgw']->template->set_var(array
				(
					'number'			=> $pro[$i]['number'],
					'title'				=> $pro[$i]['title']?$pro[$i]['title']:lang('browse'),
					'projects_url'			=> $projects_url,
					'phours'			=> $this->boprojects->formatTime($pro[$i]['phours']),
					'uhours_pro'			=> $this->boprojects->formatTime($pro[$i]['uhours_pro']),
					'uhours_jobs'			=> $this->boprojects->formatTime($pro[$i]['uhours_jobs']),
					'uhours_pro_nobill'		=> $this->boprojects->formatTime($pro[$i]['uhours_pro_nobill']),
					'uhours_jobs_nobill'		=> $this->boprojects->formatTime($pro[$i]['uhours_jobs_nobill']),
					'uhours_pro_bill'		=> $this->boprojects->formatTime($pro[$i]['uhours_pro_bill']),
					'uhours_jobs_bill'		=> $this->boprojects->formatTime($pro[$i]['uhours_jobs_bill']),
					'ahours_pro'			=> $this->boprojects->formatTime($pro[$i]['ahours_pro']),
					'ahours_jobs'			=> $this->boprojects->formatTime($pro[$i]['ahours_jobs'])
				));
				$GLOBALS['phpgw']->template->parse('list','projects_list',True);
			}

// ------------------------- end record declaration ------------------------

// --------------- template declaration for Add Form --------------------------

// ----------------------- end Add form declaration ----------------------------

			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
		}

		function list_hours()
		{
			$action		= get_var('action',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list work hours');
			$this->display_app_header();

			$this->project_id = intval($project_id);

			$GLOBALS['phpgw']->template->set_file(array('hours_list_t' => 'hours_listhours.tpl'));
			$GLOBALS['phpgw']->template->set_block('hours_list_t','hours_list','list');
			$GLOBALS['phpgw']->template->set_block('hours_list_t','project_main','main');

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.list_hours',
				'project_id'	=> $this->project_id,
				'pro_main'		=> $pro_main,
				'action'		=> 'hours'
			);

			if($this->project_id)
			{
				$main = $this->boprojects->read_single_project($this->boprojects->return_value('main',$this->project_id),'hours');
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action=mains&project_id='
																						. $main['project_id']));
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('utime_main',$this->boprojects->formatTime($main['uhours_jobs']));
				$GLOBALS['phpgw']->template->set_var('ptime_main',$this->boprojects->formatTime($main['ptime']));
				$GLOBALS['phpgw']->template->set_var('atime_main',$this->boprojects->formatTime($main['ahours_jobs']));
				$GLOBALS['phpgw']->template->parse('main','project_main',True);
			}

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter(array('format' => 'yours','filter' => $this->filter)));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));

			$GLOBALS['phpgw']->template->set_var('project_list',$this->boprojects->select_project_list(array('action' => 'all','status' => $this->status,'selected' => $this->project_id)));

			/* seems not to be used atm. RalfBecker 2004/11/04
			switch($this->state)
			{
				case 'all': $state_sel[0]=' selected';break;
				case 'open': $state_sel[1]=' selected';break;
				case 'done': $state_sel[2]=' selected';break;
				case 'billed': $state_sel[3]=' selected';break;
			}

			$state_list = '<option value="all"' . $state_sel[0] . '>' . lang('Show all') . '</option>' . "\n"
						. '<option value="open"' . $state_sel[1] . '>' . lang('Open') . '</option>' . "\n"
						. '<option value="done"' . $state_sel[2] . '>' . lang('Done') . '</option>' . "\n"
						. '<option value="billed"' . $state_sel[3] . '>' . lang('Billed') . '</option>' . "\n";

			$GLOBALS['phpgw']->template->set_var('state_list',$state_list);
			*/

			$hours = $this->bohours->list_hours();

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->bohours->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bohours->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->bohours->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

// ---------------- list header variable template-declarations --------------------------

			$GLOBALS['phpgw']->template->set_var('sort_hours_descr',$this->nextmatchs->show_sort_order($this->sort,$this->siteconfig['accounting']=='own'?'hours_descr':'activity',$this->order,'/index.php',lang('Activity'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_status',$this->nextmatchs->show_sort_order($this->sort,'status',$this->order,'/index.php',lang('Status'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_start_date',$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Work date'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_start_time',$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Start time'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_end_time',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('End time'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_hours',$this->nextmatchs->show_sort_order($this->sort,'minutes',$this->order,'/index.php',lang('Hours'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_employee',$this->nextmatchs->show_sort_order($this->sort,'employee',$this->order,'/index.php',lang('Employee'),$link_data));

// -------------- end header declaration ---------------------------------------

			for ($i=0;$i<count($hours);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

// ---------------- template declaration for list records ------------------------------

				$link_data['hours_id'] = $hours[$i]['hours_id'];

				$hours_desr = $this->siteconfig['accounting']=='own'?$hours[$i]['hours_descr']:$hours[$i]['activity_title'];
				if(empty($hours_desr)) $hours_desr = '--';
				if ($this->bohours->edit_perms
					(
						array
						(
							'main' => $main['project_id'],
							'main_co' => $main['coordinator'],
							'status' => $hours[$i]['status'],
							'employee' => $hours[$i]['employee']
						)
					)
				)
				{
					$link_data['menuaction'] = 'projects.uiprojecthours.edit_hours';
					$descr = '<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '">'
						. $hours_desr . '</a>';
				}
				else
				{
					$descr = $hours_desr;
				}

				$GLOBALS['phpgw']->template->set_var
				(
					array
					(
						'employee'	=> $hours[$i]['employeeout'],
						'hours_descr'	=> $descr,
						'billable'	=> $hours[$i]['billable'] == 'Y' ? 'X' : '',
						'status'	=> $hours[$i]['statusout'],
						'start_date'	=> $hours[$i]['sdate_formatted']['date'],
						'start_time'	=> $hours[$i]['sdate_formatted']['time'],
						'end_time'	=> $hours[$i]['edate_formatted']['time'],
						'wh'		=> $this->boprojects->formatTime($hours[$i]['wh']['whwm'])
					)
				);

				$link_data['menuaction'] = 'projects.uiprojecthours.view_hours';
				$GLOBALS['phpgw']->template->set_var('view_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('view_img',$GLOBALS['phpgw']->common->image('phpgwapi','view'));
				$GLOBALS['phpgw']->template->set_var('lang_view_hours',lang('view hours'));
				$GLOBALS['phpgw']->template->fp('list','hours_list',True);

// --------------------------- end record declaration -----------------------------------

			}

			$ptime_pro = $this->boprojects->return_value('ptime',$this->project_id);
			$acc = $this->boprojects->get_budget(array('project_id' => $this->project_id,'ptime' => $ptime_pro));

			$GLOBALS['phpgw']->template->set_var('uhours_pro',$this->boprojects->colored($acc['uhours_pro'],$ptime_pro,$acc['uhours_pro_wminutes'],'hours'));
			$GLOBALS['phpgw']->template->set_var('uhours_jobs',$this->boprojects->colored($acc['uhours_jobs'],$ptime_pro,$acc['uhours_jobs_wminutes'],'hours'));
			$GLOBALS['phpgw']->template->set_var('ahours_jobs',$this->boprojects->formatTime($acc['ahours_jobs']));
			$GLOBALS['phpgw']->template->set_var('phours',$ptime_pro/60 . '.00');

			if ($this->bohours->add_perms(array('main' => $main['project_id'],'main_co' => $main['coordinator'])))
			{
				$link_data['menuaction'] = 'projects.uiprojecthours.edit_hours';
				unset($link_data['hours_id']);
				$GLOBALS['phpgw']->template->set_var('action','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
																. '"><input type="submit" value="' . lang('Add') . '"></form>');
			}

			$this->save_sessiondata('hours');
			$GLOBALS['phpgw']->template->pfp('out','hours_list_t',True);
		}

// ------ TTRACKER ----------

		function ttracker()
		{
			if (!is_object($this->jscal))
			{
				$this->jscal = CreateObject('phpgwapi.jscalendar');
			}
			$values		= get_var('values',array('POST'));

			//_debug_array($values);

			$this->project_id = intval($values['project_id']);
			if ($this->siteconfig['accounting'] == 'activity')
			{
				$values['billable'] = substr($values['activity_id'],-1);
			}
			else
			{
				$project = $this->boprojects->read_single_project($this->project_id);
				$values['billable'] = !is_array($values) || $values['billable'] == 'Y' && $project['billable'] != 'N' ? 'Y' : 'N';
			}
			if($values['start'] || $values['stop'] || $values['continue'] || $values['pause'])
			{
				$error = $this->bohours->check_ttracker($values);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->bohours->ttracker($values);
				}
			}

			if($values['apply'])
			{
				$values['action'] = 'apply';
				$values['ttracker'] = True;
				$values += $this->jscal->input2date($values['start_date'],false,'sday','smonth','syear');
				$error = $this->bohours->check_values($values);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->bohours->save_hours($values);
				}
			}

			if($values['save'])
			{
				$values['action'] = 'save';
				$this->bohours->ttracker($values);
			}

			if($_GET['delete'])
			{
			   $this->bohours->delete_hours(array('action' => 'track','id' => $_GET['track_id']));
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('time tracker');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('ttracker_t' => 'ttracker.tpl'));
			$GLOBALS['phpgw']->template->set_block('ttracker_t','ttracker','track');
			$GLOBALS['phpgw']->template->set_block('ttracker_t','ttracker_list','listhandle');

			$GLOBALS['phpgw']->template->set_block('ttracker_t','activity','activityhandle');
			$GLOBALS['phpgw']->template->set_block('ttracker_t','cost','costhandle');
			$GLOBALS['phpgw']->template->set_block('ttracker_t','act_own','actownhandle');

			$GLOBALS['phpgw']->template->set_var('lang_select_project',lang('select project'));
			$GLOBALS['phpgw']->template->set_var('lang_start',lang('start'));
			$GLOBALS['phpgw']->template->set_var('lang_stop',lang('stop'));
			$GLOBALS['phpgw']->template->set_var('lang_pause',lang('pause'));
			$GLOBALS['phpgw']->template->set_var('lang_continue',lang('continue'));
			$GLOBALS['phpgw']->template->set_var('lang_comment',lang('comment'));
			$GLOBALS['phpgw']->template->set_var('lang_action',lang('action'));

			$curr_date = $this->bohours->format_htime(time());

			$GLOBALS['phpgw']->template->set_var('curr_date',$curr_date['date']);
			$GLOBALS['phpgw']->template->set_var('curr_time',$curr_date['time']);

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.ttracker',
				'project_id'	=> $this->project_id
			);

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('remark',$values['remark']);
			$GLOBALS['phpgw']->template->set_var('hours',$values['hours']);
			$GLOBALS['phpgw']->template->set_var('minutes',$values['minutes']);
			$GLOBALS['phpgw']->template->set_var('km_distance',$values['km_distance']);
			$GLOBALS['phpgw']->template->set_var('t_journey',$values['t_journey']);

			$GLOBALS['phpgw']->template->set_var('start_date_select',$this->jscal->input('values[start_date]',time()));																

			if($this->siteconfig['accounting'] == 'activity')
			{
				$GLOBALS['phpgw']->template->set_var('activity_list',$this->boprojects->select_hours_activities($this->project_id,$values['activity_id']));
				$GLOBALS['phpgw']->template->fp('activityhandle','activity',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('hours_descr',$values['hours_descr']);
				if ($project['billable'] != 'N')
				{
	 				$GLOBALS['phpgw']->template->set_var('billable_checked','<input type="checkbox" name="values[billable]" value="Y"'.
						($values['billable'] == 'N' ? '' : ' checked="1"').'> '.lang('billable'));
				}
				$GLOBALS['phpgw']->template->fp('actownhandle','act_own',True);
			}
			$GLOBALS['phpgw']->template->set_var('cost_list',$this->boprojects->select_hours_costs($this->project_id,$values['cost_id']));
			$GLOBALS['phpgw']->template->fp('costhandle','cost',True);

			$tracking = $this->bohours->list_ttracker();
			//_debug_array($tracking);
			
			$projects = array('' => lang('select project'));
			foreach((array)$tracking as $track)
			{
				$projects[$track['project_id']] = $track['project_title'];

				if (!count($track['hours'])) continue;	// dont list projects without hours

				$GLOBALS['phpgw']->template->set_var('project_title',$track['project_title']);
				$GLOBALS['phpgw']->template->set_var('project_id',$track['project_id']);

				$GLOBALS['phpgw']->template->set_var('thours_list','');

				for($i=0;$i<count($track['hours']);$i++)
				{
					$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

// ---------------- template declaration for list records ------------------------------

					if($track['hours'][$i]['wh']['whours_formatted'] == 0 && $track['hours'][$i]['wh']['wmin_formatted'] == 0)
					{
						$wh = '';
					}
					else
					{
						$wh = $track['hours'][$i]['wh']['whours_formatted'] . ':' . sprintf("%02d",$track['hours'][$i]['wh']['wmin_formatted']);
					}

					switch($track['hours'][$i]['status'])
					{
						case 'apply':	$at = $track['hours'][$i]['sdate_formatted']['date']; break;
						default:		$at = $track['hours'][$i]['sdate_formatted']['time'];
					}

					$GLOBALS['phpgw']->template->set_var(array(
						'hours_descr'	=> $this->siteconfig['accounting']=='own'?$track['hours'][$i]['hours_descr']:$track['hours'][$i]['activity_title'],
						'billable'		=> $track['hours'][$i]['billable']=='Y' ? 'X' : '',
						'statusout'		=> lang($track['hours'][$i]['status']),
						'start_date'	=> $track['hours'][$i]['sdate_formatted']['date'],
						'start_time'	=> $track['hours'][$i]['status'] != 'apply' ? $track['hours'][$i]['sdate_formatted']['time'] : $track['hours'][$i]['sdate_formatted']['date'],
						'apply_time'	=> $at,
						'end_time'		=> $track['hours'][$i]['status'] == 'apply' || !$track['hours'][$i]['edate'] ? '' : $track['hours'][$i]['edate_formatted']['time'],
						'wh'			=> $wh,
						'delete_url'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.ttracker&delete=True&track_id=' . $track['hours'][$i]['track_id']),
						'edit_url'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.edit_ttracker&track_id=' . $track['hours'][$i]['track_id']),
						'delete_img'	=> $GLOBALS['phpgw']->common->image('phpgwapi','delete'),
						'lang_delete'	=> lang('delete'),
					));

					$GLOBALS['phpgw']->template->fp('thours_list','ttracker_list',True);
				}
				$GLOBALS['phpgw']->template->fp('track','ttracker',True);
			}
			if (!is_object($GLOBALS['phpgw']->html))
			{
				$GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
			}
			$GLOBALS['phpgw']->template->set_var('select_project',
				$GLOBALS['phpgw']->html->select('values[project_id]',$this->project_id,$projects,true,'onchange="this.form.submit();"'));

			$GLOBALS['phpgw']->template->set_var('listhandle','');
			$GLOBALS['phpgw']->template->pfp('out','ttracker_t',True);
			$this->save_sessiondata('hours');
		}

		function edit_ttracker()
		{
			if (!is_object($this->jscal))
			{
				$this->jscal = CreateObject('phpgwapi.jscalendar');
			}
			$track_id	= get_var('track_id',array('POST','GET'));
			$values		= $_POST['values'];

			if ($this->siteconfig['accounting'] == 'activity')
			{
				$values['billable'] = substr($values['activity_id'],-1);
			}
			else
			{
				$values['billable'] = $values['billable'] == 'Y' ? 'Y' : 'N';
			}
			if($_POST['save'] || $_POST['cancel'])
			{
				if($_POST['save'])
				{
					$values['track_id']	= $track_id;
					
					$values += $this->jscal->input2date($values['sdate'],false,'sday','smonth','syear');
					$values += $this->jscal->input2date($values['edate'],false,'eday','emonth','eyear');

					$values['billable'] = $this->siteconfig['accounting'] == 'activity' ? substr($values['activity_id'],-1) :
						($values['billable'] == 'Y' ? 'Y' : 'N');
					$this->bohours->save_hours($values);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=projects.uiprojecthours.ttracker');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit time tracker entry');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('ttracker_form' => 'ttracker_form.tpl'));
			$GLOBALS['phpgw']->template->set_block('ttracker_form','activity','activityhandle');
			$GLOBALS['phpgw']->template->set_block('ttracker_form','act_own','actownhandle');

			$values = $this->bohours->read_single_track($track_id);

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.edit_ttracker',
				'track_id'		=> $track_id
			);

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if($this->siteconfig['accounting'] == 'activity')
			{
				$GLOBALS['phpgw']->template->set_var('activity_list',$this->boprojects->select_hours_activities($values['project_id'],$values['activity_id'],$values['billable']));
				$GLOBALS['phpgw']->template->fp('activityhandle','activity',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('hours_descr',$values['hours_descr']);
				$project = $this->boprojects->read_single_project($values['project_id']);
				if ($project['billable'] != 'N')
				{
	 				$GLOBALS['phpgw']->template->set_var('billable_checked','<input type="checkbox" name="values[billable]" value="Y"'.
						($values['billable'] == 'N' ? '' : ' checked="1"').'> '.lang('billable'));
				}
				$GLOBALS['phpgw']->template->fp('actownhandle','act_own',True);
			}

			$GLOBALS['phpgw']->template->set_var('start_date_select',$this->jscal->input('values[sdate]',$values['sdate']));

			$amsel = ' checked';
			$pmsel = '';

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
			{
				if ($values['sdate_formatted']['hour'] >= 12)
				{
					$amsel = '';
					$pmsel = ' checked'; 
					if ($values['sdate_formatted']['hour'] > 12)
					{
						$values['sdate_formatted']['hour'] = $values['sdate_formatted']['hour'] - 12;
					}
				}

				if ($values['sdate_formatted']['hour'] == 0)
				{
					$values['sdate_formatted']['hour'] = 12;
				}

				$sradio = '<input type="radio" name="values[sampm]" value="am"' . $amsel . '>am';
				$sradio .= '<input type="radio" name="values[sampm]" value="pm"' . $pmsel . '>pm';
				$GLOBALS['phpgw']->template->set_var('sradio',$sradio);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('sradio','');
			}

			$GLOBALS['phpgw']->template->set_var('shour',$values['sdate_formatted']['hour']);
			$GLOBALS['phpgw']->template->set_var('smin',$values['sdate_formatted']['min']);

			$GLOBALS['phpgw']->template->set_var('end_date_select',$this->jscal->input('values[edate]',$values['edate']));

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
			{
				if ($values['edate_formatted']['hour'] >= 12)
				{
					$amsel = '';
					$pmsel = ' checked';

					if ($values['edate_formatted']['hour'] > 12)
					{
						$values['edate_formatted']['hour'] = $values['edate_formatted']['hour'] - 12;
					}
				}
				if ($values['edate_formatted']['hour'] == 0)
				{
					$values['edate_formatted']['hour'] = 12;
				}

				$eradio = '<input type="radio" name="values[eampm]" value="am"' . $amsel . '>am';
				$eradio .= '<input type="radio" name="values[eampm]" value="pm"' . $pmsel . '>pm';
				$GLOBALS['phpgw']->template->set_var('eradio',$eradio);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('eradio','');
			}

			$GLOBALS['phpgw']->template->set_var('ehour',$values['edate_formatted']['hour']);
			$GLOBALS['phpgw']->template->set_var('emin',$values['edate_formatted']['min']);


			$GLOBALS['phpgw']->template->set_var('remark',$values['remark']);
			$GLOBALS['phpgw']->template->set_var('hours_descr',$values['hours_descr']);

			$GLOBALS['phpgw']->template->set_var('hours',$values['wh']['whours_formatted']);
			$GLOBALS['phpgw']->template->set_var('minutes',$values['wh']['wmin_formatted']);

			//$GLOBALS['phpgw']->template->set_var('project_name',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('pro',$this->project_id)));

			$GLOBALS['phpgw']->template->pfp('out','ttracker_form');
		}

		function status_format($status = '')
		{
			switch ($status)
			{
				case 'open'	:	$stat_sel[0]=' selected'; break;
				case 'done'	:	$stat_sel[1]=' selected'; break;
				default		:	$stat_sel[1]=' selected'; break;
			}

			$status_list = '<option value="open"' . $stat_sel[0] . '>' . lang('Open') . '</option>' . "\n"
						. '<option value="done"' . $stat_sel[1] . '>' . lang('Done') . '</option>' . "\n";

			return $status_list;
		}

		function status_format_pro($status = '', $showarchive = True)
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

		function employee_format($employee = '')
		{
			if (! $employee)
			{
				$employee = $this->account;
			}

			$employees = $this->boprojects->selected_employees();

			while (list($null,$account) = each($employees))
			{
				$employee_list .= '<option value="' . $account['account_id'] . '"';
				if($account['account_id'] == $employee)
				$employee_list .= ' selected';
				$employee_list .= '>' . $account['account_firstname'] . ' ' . $account['account_lastname']
										. ' [ ' . $account['account_lid'] . ' ]' . '</option>' . "\n";
			}
			return $employee_list;
		}

		function edit_hours()
		{
			if (!is_object($this->jscal))
			{
				$this->jscal = CreateObject('phpgwapi.jscalendar');
			}
			$project_id		= get_var('project_id',array('POST','GET'));
			$pro_main		= get_var('pro_main',array('POST','GET'));
			$hours_id		= get_var('hours_id',array('POST','GET'));

			$values			= get_var('values',array('POST'));
			$referer		= get_var('referer',array('GET'));

			$delivery_id	= get_var('delivery_id',array('POST','GET'));
			$invoice_id		= get_var('invoice_id',array('POST','GET'));

			if(!$referer)
			{
				$referer = $_SERVER['HTTP_REFERER'];
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.edit_hours',
				'hours_id'	=> $hours_id,
				'project_id'	=> $this->project_id,
				'pro_main'	=> $pro_main,
				'delivery_id'	=> $delivery_id,
				'invoice_id'	=> $invoice_id,
				'referer'		=> $referer
			);

			if ($_POST['save'])
			{
				$values['pro_main']		= $pro_main;
				$values['project_id'] 		= $this->project_id;
				$values['hours_id']		= $hours_id;
				
				if ($this->siteconfig['accounting'] == 'activity')
				{
					$values['billable'] = substr($values['activity_id'],-1);
				}
				else
				{
					$values['billable'] = $values['billable'] == 'Y' ? 'Y' : 'N';
				}
				$values += $this->jscal->input2date($values['sdate'],false,'sday','smonth','syear');
				$values += $this->jscal->input2date($values['edate'],false,'eday','emonth','eyear');
				
				$error = $this->bohours->check_values($values);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->bohours->save_hours($values);
					Header('Location: ' . $referer);
				}
			}

			if($_POST['cancel'])
			{
				$link_data['menuaction'] = 'projects.uiprojecthours.list_hours';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['delete'])
			{
				$link_data['menuaction'] = 'projects.uiprojecthours.delete_hours';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($hours_id?lang('edit work hours'):lang('add work hours'));
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('hours_form' => 'hours_formhours.tpl'));
			$GLOBALS['phpgw']->template->set_block('hours_form','activity','activityhandle');
			$GLOBALS['phpgw']->template->set_block('hours_form','activity_own','actownhandle');
			$GLOBALS['phpgw']->template->set_block('hours_form','cost','costhandle');
			$GLOBALS['phpgw']->template->set_block('hours_form','main','mainhandle');

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'hours');
				$GLOBALS['phpgw']->template->set_var('pro_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action=mains&project_id='
																						. $pro_main));
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('utime_main',$this->boprojects->formatTime($main['uhours_jobs']));
				$GLOBALS['phpgw']->template->set_var('ptime_main',$this->boprojects->formatTime($main['ptime']));
				$GLOBALS['phpgw']->template->set_var('atime_main',$this->boprojects->formatTime($main['ahours_jobs']));
				$GLOBALS['phpgw']->template->fp('mainhandle','main',True);
			}

			if ($hours_id)
			{
				$values = $this->bohours->read_single_hours($hours_id);

				$activity_id	= $values['activity_id'];
				$costID		= $values['cost_id'];
				$pro_parent	= $values['pro_parent'];
			}
			else
			{
				if(!is_array($values))
				{
					$values['sdate_formatted'] = $values['edate_formatted'] = $this->bohours->hdate_format();
					$values['sdate'] = $values['edate'] = time();
				}
			}
			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format($values['status']));

			$GLOBALS['phpgw']->template->set_var('start_date_select',$this->jscal->input('values[sdate]',$values['sdate']));
			
			$amsel = ' checked';
			$pmsel = '';

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
			{
				if ($values['sdate_formatted']['hour'] >= 12)
				{
					$amsel = '';
					$pmsel = ' checked'; 
					if ($values['sdate_formatted']['hour'] > 12)
					{
						$values['sdate_formatted']['hour'] = $values['sdate_formatted']['hour'] - 12;
					}
				}

				if ($values['sdate_formatted']['hour'] == 0)
				{
					$values['sdate_formatted']['hour'] = 12;
				}

				$sradio = '<input type="radio" name="values[sampm]" value="am"' . $amsel . '>am';
				$sradio .= '<input type="radio" name="values[sampm]" value="pm"' . $pmsel . '>pm';
				$GLOBALS['phpgw']->template->set_var('sradio',$sradio);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('sradio','');
			}

			$GLOBALS['phpgw']->template->set_var('shour',$values['sdate_formatted']['hour']);
			$GLOBALS['phpgw']->template->set_var('smin',$values['sdate_formatted']['min']);

			$GLOBALS['phpgw']->template->set_var('end_date_select',$this->jscal->input('values[edate]',$values['edate']));

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
			{
				if ($values['edate_formatted']['hour'] >= 12)
				{
					$amsel = '';
					$pmsel = ' checked';

					if ($values['edate_formatted']['hour'] > 12)
					{
						$values['edate_formatted']['hour'] = $values['edate_formatted']['hour'] - 12;
					}
				}
				if ($values['edate_formatted']['hour'] == 0)
				{
					$values['edate_formatted']['hour'] = 12;
				}

				$eradio = '<input type="radio" name="values[eampm]" value="am"' . $amsel . '>am';
				$eradio .= '<input type="radio" name="values[eampm]" value="pm"' . $pmsel . '>pm';
				$GLOBALS['phpgw']->template->set_var('eradio',$eradio);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('eradio','');
			}

			$GLOBALS['phpgw']->template->set_var('ehour',$values['edate_formatted']['hour']);
			$GLOBALS['phpgw']->template->set_var('emin',$values['edate_formatted']['min']);


			$GLOBALS['phpgw']->template->set_var('remark',$values['remark']);
			$GLOBALS['phpgw']->template->set_var('hours_descr',$values['hours_descr']);

			$GLOBALS['phpgw']->template->set_var('hours',$values['wh']['whours_formatted']);
			$GLOBALS['phpgw']->template->set_var('minutes',$values['wh']['wmin_formatted']);

			$GLOBALS['phpgw']->template->set_var('project_name',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('pro',$this->project_id)));

			$GLOBALS['phpgw']->template->set_var('km_distance',$values['km_distance']);
			$GLOBALS['phpgw']->template->set_var('t_journey',$this->boprojects->formatTime($values['t_journey']));

			if($this->siteconfig['accounting'] == 'activity')
			{
				$GLOBALS['phpgw']->template->set_var('activity_list',$this->boprojects->select_hours_activities($this->project_id,$values['activity_id'],$values['billable']));
				$GLOBALS['phpgw']->template->fp('activityhandle','activity',True);
			}
			else
			{
				$project = $this->boprojects->read_single_project($this->project_id);
				if ($project['billable'] != 'N')
				{
	 				$GLOBALS['phpgw']->template->set_var('billable_checked','<input type="checkbox" name="values[billable]" value="Y"'.
						($values['billable'] == 'N' ? '' : ' checked="1"').'> '.lang('billable'));
				}
				$GLOBALS['phpgw']->template->fp('actownhandle','activity_own',True);
			}
			$GLOBALS['phpgw']->template->set_var('cost_list',$this->boprojects->select_hours_costs($this->project_id, $costID));
			$GLOBALS['phpgw']->template->fp('costhandle','cost',True);

			/*if ($values['pro_parent'] > 0)
			{
				$GLOBALS['phpgw']->template->set_var('pro_parent',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('pro',$pro_parent)));
				$GLOBALS['phpgw']->template->set_var('lang_pro_parent',lang('Main project:'));
			}*/

			if ($this->bohours->edit_perms(array('adminonly' => True,'status' => $values['status'],'main_co' => $main['coordinator'])))
			{
				$GLOBALS['phpgw']->template->set_var('employee','<select name="values[employee]">' . $this->employee_format($values['employee'])
																	. '</select>');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('employee',$values['employeeout']);
			}



			if ($hours_id && $this->bohours->edit_perms(array('action' => 'delete','status' => $values['status'],'main_co' => $main['coordinator'])))
			{
				$GLOBALS['phpgw']->template->set_var('delete','<input type="submit" name="delete" value="' . lang('Delete') .'">');
			}

			$this->save_sessiondata('hours');
			$GLOBALS['phpgw']->template->pfp('out','hours_form');
		}

		function view_hours()
		{
			$hours_id	= get_var('hours_id',array('GET'));
			//$referer	= get_var('referer',array('POST'));
			$project_id	= get_var('project_id',array('GET'));
			$pro_main	= get_var('pro_main',array('GET'));

			$referer = $GLOBALS['HTTP_SERVER_VARS']['HTTP_REFERER'] ? $GLOBALS['HTTP_SERVER_VARS']['HTTP_REFERER'] : $GLOBALS['HTTP_REFERER'];

			if (!$hours_id)
			{
				$GLOBALS['phpgw']->redirect_link($referer);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('view work hours');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('hours_view' => 'hours_view.tpl'));
			$GLOBALS['phpgw']->template->set_block('hours_view','main','mainhandle');
			$GLOBALS['phpgw']->template->set_var('doneurl',$referer . '&project_id=' . $project_id);

			$prefs = $this->boprojects->get_prefs();

			$values = $this->bohours->read_single_hours($hours_id);

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'hours');
				$GLOBALS['phpgw']->template->set_var('pro_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action=mains&project_id='
																						. $pro_main));
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('utime_main',$main['uhours_jobs']);
				$GLOBALS['phpgw']->template->set_var('ptime_main',$main['ptime']);
				$GLOBALS['phpgw']->template->set_var('atime_main',$main['ahours_jobs']);
				$GLOBALS['phpgw']->template->fp('mainhandle','main',True);
			}

			$GLOBALS['phpgw']->template->set_var('status',$values['statusout']);

			$GLOBALS['phpgw']->template->set_var('sdate',$values['stime_formatted']['date']);
			$GLOBALS['phpgw']->template->set_var('stime',$values['stime_formatted']['time']);

			$GLOBALS['phpgw']->template->set_var('edate',$values['etime_formatted']['date']);
			$GLOBALS['phpgw']->template->set_var('etime',$values['etime_formatted']['time']);

			$GLOBALS['phpgw']->template->set_var('remark',$values['remark']);

			$GLOBALS['phpgw']->template->set_var('hours',$values['wh']['whours_formatted']);
			$GLOBALS['phpgw']->template->set_var('minutes',$values['wh']['wmin_formatted']);

			$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);
			$GLOBALS['phpgw']->template->set_var('minperae',$values['minperae']);
			$GLOBALS['phpgw']->template->set_var('billperae',$values['billperae']);
			$GLOBALS['phpgw']->template->set_var('employee',$values['employeeout']);
			$GLOBALS['phpgw']->template->set_var('km_distance',$values['km_distance']);
			$GLOBALS['phpgw']->template->set_var('t_journey',$values['t_journey']);

			$GLOBALS['phpgw']->template->set_var('project_name',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('pro',$values['project_id'])));

			if($this->siteconfig['accounting'] == 'activity')
			{
				$GLOBALS['phpgw']->template->set_var('activity',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('act',$values['activity_id'])));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('activity',$values['hours_descr']);
			}
			$GLOBALS['phpgw']->template->set_var('cost',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('cost',$values['cost_id'])));
			$GLOBALS['phpgw']->template->pfp('out','hours_view');
		}

		function delete_hours()
		{
			$hours_id	= get_var('hours_id',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.list_hours',
				'hours_id'		=> $hours_id,
				'project_id'	=> $project_id
			);

			if ($_POST['yes'] || $_POST['no'])
			{
				if($_POST['yes'])
				{
					$this->bohours->delete_hours(array('id' => $hours_id));
				}
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('delete work hours');

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('hours_delete' => 'delete.tpl'));

			$GLOBALS['phpgw']->template->set_var('lang_subs','');
			$GLOBALS['phpgw']->template->set_var('subs', '');
			$GLOBALS['phpgw']->template->set_var('deleteheader',lang('Are you sure you want to delete this entry ?'));
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));

			$link_data['menuaction'] = 'projects.uiprojecthours.delete_hours';
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->pfp('out','hours_delete');
		}
	}
?>
