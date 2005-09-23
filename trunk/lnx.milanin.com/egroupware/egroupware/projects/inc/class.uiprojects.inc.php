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
	* Copyright 2004 Lars Kneschke                                      *
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
	/* $Id: class.uiprojects.inc.php,v 1.108.2.9 2005/02/18 13:24:46 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.uiprojects.inc.php,v $

	class uiprojects
	{           
	
		var $action;
		var $grants;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $status;

		var $public_functions = array
		(
			'edit_resources'	=> True,
			'editMilestone'		=> True,
			'editMilestone'		=> True,
			'export_project'	=> True,
			'handle_fileupload'	=> True,
			'list_projects'		=> True,
			'list_projects_home'	=> True,
			'edit_project'		=> True,
			'delete_project'	=> True,
			'view_project'		=> True,
			'abook'			=> True,
			'list_budget'		=> True,
			'project_mstones'	=> True,
			'assign_employee_roles'	=> True
		);

		function uiprojects()
		{
			$action = get_var('action',array('GET','POST'));

			$this->boprojects				= CreateObject('projects.boprojects',True, $action);
			$this->nextmatchs				= CreateObject('phpgwapi.nextmatchs');
			$this->sbox					= CreateObject('phpgwapi.sbox');

			$this->start					= $this->boprojects->start;
			$this->query					= $this->boprojects->query;
			$this->filter					= $this->boprojects->filter;
			$this->order					= $this->boprojects->order;
			$this->sort					= $this->boprojects->sort;
			$this->cat_id					= $this->boprojects->cat_id;
			$this->status					= $this->boprojects->status;
			
//ndee 140504
			if (!is_object($this->jscal))
				{
					$this->jscal = CreateObject('phpgwapi.jscalendar');
				}
//ndee

		}

		function export_project()
		{
			$pro_main	= get_var('pro_main',array('POST','GET'));
			$export		= get_var('export',array('GET'));
			
			if(!preg_match('/^email|pdf$/',$export) || !$pro_main)
			{
				$this->list_projects();
				
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

#			if ($_GET['cat_id'])
#			{
#				$this->cat_id = $_GET['cat_id'];
#			}
			
			switch($export)
			{
				case 'email':
			if(get_var('send_email',array('POST')))
			{
				$emailTo = get_var('email_to',array('POST'));
				
				$this->boprojects->exportProjectEMail($pro_main,$emailTo);
				
				$this->list_projects('subs',$pro_main);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_main ? lang('list jobs') : lang('list projects'));

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'export_email.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','project_main_mail');

			$this->set_app_langs();

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main);
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',
					'menuaction=projects.uiprojects.view_project&action=mains&project_id='. 
					$pro_main));

				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);

				$linkData = array
				(
					'menuaction'	=> 'projects.uiprojects.export_project',
					'pro_main'	=> $pro_main,
					'export'	=> 'email'
				);
				$GLOBALS['phpgw']->template->set_var('url_action',$GLOBALS['phpgw']->link('/index.php',$linkData));
			}
			
			$GLOBALS['phpgw']->template->pfp('out','project_main_mail');
					break;
				case 'pdf':
					if($pdfData = $this->boprojects->exportProjectPDF2($pro_main))
					{
						header("Content-Disposition: filename=projectoverview.pdf");
						#header("Content-Disposition: attachment; filename=example.pdf");
						header('Pragma: public');
						header("Content-Type: application/pdf");
						header('Content-Length: ' . strlen($pdfData));
						
						print $pdfData;
					}
					break;
				default:
					$this->list_projects();
					break;
			} 
			
		}
		
		function handle_fileupload()
		{
			$bolink		= CreateObject('infolog.bolink');
			$project_id	= get_var('project_id',array('GET','POST'));
			
			if($_POST['delete_files'])
			{
				if(is_array($selectedFiles = get_var('selected_file')))
				{
					#_debug_array($selectedFiles);
					foreach($selectedFiles as $bolinkID => $bolinkData)
					{
						$bolink->unlink($bolinkID);
					}
				}
			}
			elseif($_POST['addfile'])
			{
				if($_FILES['attachfile']['error'] == 0)
					$bolink->link('projects',$project_id,$bolink->vfs_appname,$_FILES['attachfile']);
			}
			
			$this->edit_project();
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

			$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
			$GLOBALS['phpgw']->template->set_var('lang_select_category',lang('Select category'));

			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));
			$GLOBALS['phpgw']->template->set_var('lang_number',lang('Project ID'));

			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));
			$GLOBALS['phpgw']->template->set_var('lang_cdate',lang('Date created'));
			$GLOBALS['phpgw']->template->set_var('lang_last_update',lang('last update'));

			$GLOBALS['phpgw']->template->set_var('lang_start_date_planned',lang('start date planned'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due_planned',lang('date due planned'));

			$GLOBALS['phpgw']->template->set_var('lang_access',lang('access'));
			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_project',lang('Project'));

			$GLOBALS['phpgw']->template->set_var('lang_ttracker',lang('time tracker'));
			$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_roles',lang('roles'));
			$GLOBALS['phpgw']->template->set_var('lang_role',lang('role'));

			$GLOBALS['phpgw']->template->set_var('lang_jobs',lang('Jobs'));
			$GLOBALS['phpgw']->template->set_var('lang_act_number',lang('Activity ID'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));

			$GLOBALS['phpgw']->template->set_var('lang_investment_nr',lang('investment nr'));
			$GLOBALS['phpgw']->template->set_var('lang_customer',lang('Customer'));
			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			$GLOBALS['phpgw']->template->set_var('lang_employees',lang('Employees'));
			$GLOBALS['phpgw']->template->set_var('lang_creator',lang('creator'));
			$GLOBALS['phpgw']->template->set_var('lang_processor',lang('processor'));
			$GLOBALS['phpgw']->template->set_var('lang_previous',lang('previous project'));
			$GLOBALS['phpgw']->template->set_var('lang_bookable_activities',lang('Bookable activities'));
			$GLOBALS['phpgw']->template->set_var('lang_billable_activities',lang('Billable activities'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('edit'));
			$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_remarkreq',lang('Remark required'));

			$GLOBALS['phpgw']->template->set_var('lang_customer_nr',lang('customer nr'));
			$GLOBALS['phpgw']->template->set_var('lang_url',lang('project url'));
			$GLOBALS['phpgw']->template->set_var('lang_reference',lang('external reference'));

			$GLOBALS['phpgw']->template->set_var('lang_stats',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_ptime',lang('time planned'));
			$GLOBALS['phpgw']->template->set_var('lang_utime',lang('time used'));
			$GLOBALS['phpgw']->template->set_var('lang_month',lang('month'));

			$GLOBALS['phpgw']->template->set_var('lang_done',lang('done'));
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('save'));
			$GLOBALS['phpgw']->template->set_var('lang_apply',lang('apply'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_search',lang('search'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('delete'));
			$GLOBALS['phpgw']->template->set_var('lang_back',lang('back'));

			$GLOBALS['phpgw']->template->set_var('lang_parent',lang('Parent project'));
			$GLOBALS['phpgw']->template->set_var('lang_main',lang('Main project'));

			$GLOBALS['phpgw']->template->set_var('lang_add_milestone',lang('add milestone'));
			$GLOBALS['phpgw']->template->set_var('lang_milestones',lang('milestones'));

			$GLOBALS['phpgw']->template->set_var('lang_result',lang('result'));
			$GLOBALS['phpgw']->template->set_var('lang_test',lang('test'));
			$GLOBALS['phpgw']->template->set_var('lang_quality',lang('quality check'));

			$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('accounting system'));
			$GLOBALS['phpgw']->template->set_var('lang_factor_project',lang('factor project'));
			$GLOBALS['phpgw']->template->set_var('lang_factor_employee',lang('factor employee'));
			$GLOBALS['phpgw']->template->set_var('lang_accounting_factor_for_project',lang('accounting factor for project'));
			$GLOBALS['phpgw']->template->set_var('lang_select_factor',lang('select factor'));
			$GLOBALS['phpgw']->template->set_var('lang_non_billable',lang('not billable'));

			$GLOBALS['phpgw']->template->set_var('lang_pbudget',lang('budget planned'));
			$GLOBALS['phpgw']->template->set_var('lang_ubudget',lang('budget used'));
			$GLOBALS['phpgw']->template->set_var('lang_plus_jobs',lang('+ jobs'));

			$GLOBALS['phpgw']->template->set_var('lang_per_hour',lang('per hour'));
			$GLOBALS['phpgw']->template->set_var('lang_per_day',lang('per day'));

			$GLOBALS['phpgw']->template->set_var('lang_percent',lang('percent'));
			$GLOBALS['phpgw']->template->set_var('lang_amount',lang('amount'));

			$GLOBALS['phpgw']->template->set_var('lang_events',lang('events'));
			$GLOBALS['phpgw']->template->set_var('lang_priority',lang('priority'));

			$GLOBALS['phpgw']->template->set_var('lang_available',lang('available'));
			$GLOBALS['phpgw']->template->set_var('lang_used_billable',lang('used billable'));
			$GLOBALS['phpgw']->template->set_var('lang_planned',lang('planned'));
			$GLOBALS['phpgw']->template->set_var('lang_used_total',lang('used total'));

			$GLOBALS['phpgw']->template->set_var('lang_invoicing_method',lang('invoicing method'));
			$GLOBALS['phpgw']->template->set_var('lang_discount',lang('discount'));
			$GLOBALS['phpgw']->template->set_var('lang_extra_budget',lang('extra budget'));

			$GLOBALS['phpgw']->template->set_var('lang_billable',lang('billable'));
			$GLOBALS['phpgw']->template->set_var('lang_send',lang('send'));
			$GLOBALS['phpgw']->template->set_var('lang_project_overview',lang('Project overview'));
			$GLOBALS['phpgw']->template->set_var('lang_milestones',lang('Milestones'));
			$GLOBALS['phpgw']->template->set_var('lang_files',lang('Files'));
			$GLOBALS['phpgw']->template->set_var('lang_add',lang('add file'));
			$GLOBALS['phpgw']->template->set_var('lang_delete_selected',lang('delete selected files'));
			$GLOBALS['phpgw']->template->set_var('lang_export_as',lang('export as'));
			$GLOBALS['phpgw']->template->set_var('lang_open_popup',lang('open popup'));
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
					switch($this->boprojects->siteconfig['accounting'])
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

				$GLOBALS['phpgw']->template->fp('app_header','projects_header');
			}

			if(!@is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			switch(get_var('menuaction',array('POST','GET')))
			{
				case 'projects.uiprojects.list_projects':
					$GLOBALS['phpgw']->js->validate_file('jscode','list_projects','projects');
					break;
				default:
					$GLOBALS['phpgw']->js->validate_file('tabs','tabs');
					$GLOBALS['phpgw']->js->validate_file('jscode','edit_project','projects');
					$GLOBALS['phpgw']->js->set_onload('javascript:initAll();');
					break;
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			#if(get_var('menuaction',array('POST','GET')) != 'projects.uiprojects.editMilestone')
			echo parse_navbar();
			$this->set_app_langs();
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

		function priority_list($selected = 0)
		{
			for($i=1;$i<=10;$i++)
			{
				$list .= '<option value="' . $i . '"' . ($i == $selected?' SELECTED>':'>') . $i . '</option>';
			}
			return $list;
		}

		function list_projects($_action=false, $_pro_main=false)
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'),
				$GLOBALS['phpgw']->session->appsession('pro_main','projects'));

			if ($_GET['cat_id'])
			{
				$this->cat_id = $_GET['cat_id'];
			}

			if (!$action)
			{
				$action = 'mains';
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_main ? lang('list jobs') : lang('list projects'));

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'list.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','pro_sort_cols','sort_cols');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','pro_cols','cols');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','project_main','main');

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.list_projects',
				'pro_main'		=> $pro_main,
				'action'		=> $action
			);

			if($pro_main)
			{
				$GLOBALS['phpgw']->session->appsession('pro_main','projects',$pro_main);
				$main = $this->boprojects->read_single_project($pro_main);
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',
					'menuaction=projects.uiprojects.view_project&action=mains&project_id='. 
					$pro_main));

				$linkData = array
				(
					'menuaction'	=> 'projects.uiprojects.export_project',
					'pro_main'	=> $pro_main,
					'export'	=> 'email'
				);
				$GLOBALS['phpgw']->template->set_var('url_export_email',$GLOBALS['phpgw']->link('/index.php',$linkData));
				
				$linkData = array
				(
					'menuaction'	=> 'projects.uiprojects.export_project',
					'pro_main'	=> $pro_main,
					'export'	=> 'pdf'
				);
				$GLOBALS['phpgw']->template->set_var('url_export_pdf',$GLOBALS['phpgw']->link('/index.php',$linkData));
				
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->parse('main','project_main',True);
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
			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format($this->status));

// ---------------- list header variable template-declarations --------------------------

			$prefs = $this->boprojects->read_prefs();

			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('title'),$link_data));
			foreach($prefs['columns'] as $col)
			{
				$col_align = '';
				switch($col)
				{
					case 'number':			$cname = lang('project id'); $db = 'p_number'; break;
					case 'priority':		$cname = lang('priority');  $col_align= 'right';break;
					case 'sdate_formatted':		$cname = lang('start date'); $db = 'start_date'; $col_align= 'center'; break;
					case 'edate_formatted':		$cname = lang('date due'); $db = 'end_date'; $col_align= 'center'; break;
					case 'phours':			$cname = lang('time planned'); $db = 'time_planned';  $col_align= 'right';break;
					case 'budget':			$cname = $prefs['currency'] . ' ' . lang('budget'); $col_align= 'right'; break;
					case 'e_budget':		$cname = $prefs['currency'] . ' ' . lang('extra budget'); $col_align= 'right'; break;
					case 'coordinatorout':		$cname = ($action=='mains'?lang('coordinator'):lang('job manager')); $db = 'coordinator'; break;
					case 'customerout':		$cname = lang('customer'); break; $db = 'customer'; break;
					case 'investment_nr':		$cname = lang('investment nr'); break;
					case 'previousout':		$cname = lang('previous'); $db = 'previous'; break;
					case 'customer_nr':		$cname = lang('customer nr'); break;
					case 'url':			$cname = lang('url'); break;
					case 'reference':		$cname = lang('reference'); break;
					case 'accountingout':		$cname = lang('accounting'); $db = 'accounting'; break;
					case 'project_accounting_factor':		$cname = $prefs['currency'] . ' ' . lang('project') . ' ' . lang('accounting factor') . ' '
															. lang('per hour'); $db = 'acc_factor'; $col_align = 'right'; break;
					case 'project_accounting_factor_d':		$cname = $prefs['currency'] . ' ' . lang('project') . ' ' . lang('accounting factor') . ' '
															. lang('per day'); $db = 'acc_factor_d'; $col_align = 'right'; break;
					case 'billableout':		$cname = lang('billable'); $db = 'billable'; $col_align= 'center';break;
					case 'psdate_formatted':	$cname = lang('start date planned'); $db = 'psdate'; $col_align= 'center'; break;
					case 'pedate_formatted':	$cname = lang('date due planned'); $db = 'pedate'; $col_align= 'center'; break;
					case 'discountout':		$cname = lang('discount'); $db = 'discount'; $col_align= 'right'; break;
				}

				($col=='mstones'?$sort_column = lang('milestones'):
				$sort_column = $this->nextmatchs->show_sort_order($this->sort,($db?$db:$col),$this->order,'/index.php',$cname?$cname:lang($col),$link_data));
				$GLOBALS['phpgw']->template->set_var('col_align',$col_align?$col_align:'left');
				$GLOBALS['phpgw']->template->set_var('sort_column',$sort_column);
				$GLOBALS['phpgw']->template->fp('sort_cols','pro_sort_cols',True);
			}

// -------------- end header declaration ---------------------------------------

			if(is_array($pro))
			{
				foreach($pro as $p)
            			{
					$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------

					$link_data['project_id'] = $p['project_id'];
					if ($action == 'mains')
					{
						$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&pro_main='
									. $p['project_id'] . '&action=subs');
					}
					else
					{
						$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_hours&project_id='
									. $p['project_id'] . '&action=hours&pro_main=' . $pro_main);
					}

					$GLOBALS['phpgw']->template->set_var(array
					(
						'title'			=> $p['title']?$p['title']:lang('browse'),
						'projects_url'	=> $projects_url
					));

					$GLOBALS['phpgw']->template->set_var('pro_column','');
					foreach($prefs['columns'] as $col)
					{
						$edit = 'yes';
						switch($col)
						{
							case 'priority':
							case 'discountout':
							case 'e_budget':
							case 'budget':
							case 'project_accounting_factor':
							case 'project_accounting_factor_d':
							case 'phours': 
								$col_align = 'right'; 
								break;
							case 'sdate_formatted':
							case 'edate_formatted':
							case 'psdate_formatted':
							case 'pedate_formatted':
							case 'billableout': 
								$col_align = 'center'; 
								break;
							default:			
								$col_align = 'left';
								break;
						}
						
						switch($col)
						{
							case 'priority':
								$p[$col] = $this->boprojects->formatted_priority($p[$col]);
								break;
							case 'budget':
								$p[$col] = $p['budgetSum'];
								break;
						}

						$GLOBALS['phpgw']->template->set_var('col_align',$col_align);
						$GLOBALS['phpgw']->template->set_var('column',$p[$col]);
						$GLOBALS['phpgw']->template->fp('pro_column','pro_cols',True);
					}
					//$GLOBALS['phpgw']->template->set_var('pro_column',$pdata);

					if (!$this->boprojects->edit_perms
						(
							array
							(
								'action' => $action,
								'coordinator' => $p['coordinator'],
								'main' => $p['main'], 
								'parent' => $p['parent']
							)
						)
					)
					{
						$edit = 'no';
					}

					$link_data['menuaction'] = 'projects.uiprojects.view_project';
					$GLOBALS['phpgw']->template->set_var('view_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$GLOBALS['phpgw']->template->set_var('view_img',$GLOBALS['phpgw']->common->image('phpgwapi','view'));

					$link_data['menuaction'] = 'projects.uiprojects.edit_project';
					$GLOBALS['phpgw']->template->set_var('edit_url',($edit=='no'?'':$GLOBALS['phpgw']->link('/index.php',$link_data)));
					$GLOBALS['phpgw']->template->set_var('edit_img',($edit=='no'?'':'<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','edit') . '" title="' . lang('edit')
																				. '" border="0">'));

					if ($this->boprojects->add_perms(array('action' => $action,'coordinator' => $p['coordinator'],
														'main_co' => $main['coordinator'],'parent' => $p['parent'])))
					{
						$GLOBALS['phpgw']->template->set_var('add_job_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.edit_project&action=subs&pro_parent='
																	. $p['project_id'] . '&pro_main=' . ($pro_main?$pro_main:$p['project_id'])));
						$GLOBALS['phpgw']->template->set_var('add_job_img','<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','new') . '" title="' . lang('add job')
																		. '" border="0">');
					}
					$GLOBALS['phpgw']->template->fp('list','projects_list',True);
				}
			}
// ------------------------- end record declaration ------------------------

// --------------- template declaration for Add Form --------------------------

			$link_data['menuaction'] = 'projects.uiprojects.edit_project';
			unset($link_data['project_id']);

			if($action=='subs' && !$pro_main)
			{
				$GLOBALS['phpgw']->template->set_var('add','');
			}
			else if ($this->boprojects->add_perms(array('action' => $action,'main_co' => $main['coordinator'])))
			{
				$GLOBALS['phpgw']->template->set_var('add','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
											. '"><input type="submit" name="Add" value="' . lang('Add') .'"></form>');
			}

// ----------------------- end Add form declaration ----------------------------

			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
		}

		function list_projects_home()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));

			if ($_GET['cat_id'])
			{
				$this->cat_id = $_GET['cat_id'];
			}

			$menuaction	= get_var('menuaction',Array('GET'));
			if ($menuaction)
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_main ? lang('list jobs') : lang('list projects'));
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
			}
			else
			{
				$this->boprojects->cats->app_name = 'projects';
			}

			$this->t = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('projects'));
			$this->t->set_file(array('projects_list_t' => 'home_list.tpl'));
			$this->t->set_block('projects_list_t','projects_list','list');

			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);


			if (!$action)
			{
				$action = 'mains';
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.list_projects_home',
				'pro_main'		=> $pro_main,
				'action'		=> $action
			);

			$this->status = 'active';
			$this->boprojects->filter = 'anonym';
			$pro = $this->boprojects->list_projects(array('action' => $action,'parent' => $pro_main));

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$this->t->set_var('left',$left);
			$this->t->set_var('right',$right);

			$this->t->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

			if ($action == 'mains')
			{
				$action_list= '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
							. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';
				$this->t->set_var('lang_action',lang('Jobs'));
			}
			else
			{
				$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
							. $this->boprojects->select_project_list(array('status' => $this->status,'selected' => $pro_main,'filter' => 'anonym')) . '</select>';
				$this->t->set_var('lang_action',lang('Work hours'));
			}

			$this->t->set_var('action_list',$action_list);
			$this->t->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$this->t->set_var('status_list',$this->status_format($this->status,False));

// ---------------- list header variable template-declarations --------------------------

			$this->t->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'num',$this->order,'/index.php',lang('Project ID'),$link_data));
			$this->t->set_var('lang_milestones',lang('milestones'));
			$this->t->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));
			$this->t->set_var('sort_end_date',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('Date due'),$link_data));
			$this->t->set_var('sort_coordinator',$this->nextmatchs->show_sort_order($this->sort,'coordinator',$this->order,'/index.php',($pro_main?lang('job manager'):lang('Coordinator')),$link_data));

			if($action == 'subs')
			{
				$this->t->set_var('add_job_row','<td>' . lang('add job') . '</td>');
			}

// -------------- end header declaration ---------------------------------------

            for ($i=0;$i<count($pro);$i++)
            {
				$this->nextmatchs->template_alternate_row_color($this->t);

				if ($action == 'mains')
				{
					$td_action = ($pro[$i]['customerout']?$pro[$i]['customerout']:'&nbsp;');
				}
				else
				{
					$td_action = ($pro[$i]['sdateout']?$pro[$i]['sdateout']:'&nbsp;');
				}

				if ($pro[$i]['level'] > 0)
				{
					$space = '&nbsp;.&nbsp;';
					$spaceset = str_repeat($space,$pro[$i]['level']);
				}

// --------------- template declaration for list records -------------------------------------

				if ($action == 'mains')
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects_home&pro_main='
														. $pro[$i]['project_id'] . '&action=subs');
				}
				else
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_hours&project_id='
									. $pro[$i]['project_id'] . '&action=hours&pro_main=' . $pro_main);
				}

				$this->t->set_var(array
				(
					'number'		=> $pro[$i]['number'],
					'milestones'	=> (isset($pro[$i]['mstones'])?$pro[$i]['mstones']:'&nbsp;'),
					'title'			=> $spaceset . ($pro[$i]['title']?$pro[$i]['title']:'&nbsp;'),
					'projects_url'	=> $projects_url,
					'end_date'		=> $pro[$i]['edateout'],
					'coordinator'	=> $pro[$i]['coordinatorout']
				));
				$link_data['project_id'] = $pro[$i]['project_id'];
				$link_data['public_view'] = True;
				$link_data['menuaction'] = 'projects.uiprojects.view_project';
				$this->t->set_var('view',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$this->t->set_var('lang_view_entry',lang('View'));
				$this->t->fp('list','projects_list',True);
			}

// ------------------------- end record declaration ------------------------

			$this->save_sessiondata($action);

			$menuaction	= get_var('menuaction',Array('GET'));
			if ($menuaction)
			{
				list($app,$class,$method) = explode('.',$menuaction);
				$var['app_tpl']	= $method;
				$this->t->pfp('out','projects_list_t',True);
			}
			else
			{
				return $this->t->fp('out','projects_list_t',True);
			}
		}

		function employee_format($data)
		{
			$type			= ($data['type']?$data['type']:'selectbox');
			$selected		= $data['selected']?$data['selected']:$this->boprojects->get_acl_for_project($data['project_id']);
			$project_only		= $data['project_only']?$data['project_only']:False;
			$admins_included	= $data['admins_included']?$data['admins_included']:False;

			if($project_only)
			{
				$data['pro_parent']	= $data['project_id'];
				$data['action']		= 'subs';
			}

			if (!is_array($selected))
			{
				$selected = explode(',',$selected);
			}

			switch($type)
			{
				case 'selectbox':
					$employees = $this->boprojects->selected_employees(array('action' => $data['action'],'pro_parent' => $data['pro_parent'],
																			'admins_included' => $admins_included));
					break;
				case 'popup':
					$employees	= $this->boprojects->selected_employees(array('project_id' => $data['project_id']));
					break;
			}

			//_debug_array($employees);
			//_debug_array($selected);
			while (is_array($employees) && list($null,$account) = each($employees))
			{
				$s .= '<option value="' . $account['account_id'] . '"';
				if (in_array($account['account_id'],$selected))
				{
					$s .= ' SELECTED';
				}
				$s .= '>';
				$s .= $GLOBALS['phpgw']->common->display_fullname($account['account_lid'],$account['account_firstname'],$account['account_lastname'])
					. '</option>' . "\n";
			}
			return $s;
		}

		function editMilestone()
		{

			if ($_GET['deleteMS'])
			{
				$this->boprojects->delete_item(array('id' => intval($_GET['s_id'])));
				$message = lang('milestone has been deleted');
				$this->edit_project();
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			$this->set_app_langs();
			
			if (!is_object($this->jscal))
			{
				$this->jscal = CreateObject('phpgwapi.jscalendar');
			}

			// called as link
			$msID 		= intval(get_var('s_id',array('GET')));
			$projectID	= intval(get_var('project_id',array('GET')));

			if(!$projectID) return false;

			if(get_var('save',array('POST')))
			{
				$msID			= intval(get_var('milestoneID',array('GET')));
				$values 		= get_var('values',array('POST'));
				$edate 			= $this->jscal->input2date($values['edate']);
				$values['edate']	= $edate['raw'];
				$values['s_id']		= $msID;
				$values['old_edate']	= get_var('old_edate',array('POST'));
				$values['project_id']	= $projectID;
				$values['title']	= $values['title'];
				$values['description']	= $values['description'];

				$this->boprojects->save_mstone($values);
				
				$refreshURL = urldecode(get_var('refresh_url',array('GET')));
#				print $refreshURL;
				print "<script type=\"text/javascript\">
				opener.location.href = '".$refreshURL."';
				window.close();
				</script>";
			}
			
			$linkData = array
			(
				'menuaction'	=> 'projects.uiprojects.editMilestone',
				'project_id'	=> $projectID,
				'refresh_url'	=> urlencode($_SERVER["HTTP_REFERER"]."&tabpage=2")
			);			
			if($msID > 0)
			{
				$msData = $this->boprojects->get_single_mstone($msID);
				$linkData['milestoneID'] = $msID;			
			}
			
			//_debug_array($msData);

			$GLOBALS['phpgw']->template->set_file(array('milestoneForm' => 'editMilestones.tpl'));
			$GLOBALS['phpgw']->template->set_block('milestoneForm','main');
			
			$GLOBALS['phpgw']->template->set_var('title',$msData['title']);
			$GLOBALS['phpgw']->template->set_var('description',$msData['description']);
			
			$GLOBALS['phpgw']->template->set_var('actionURL',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			$GLOBALS['phpgw']->template->set_var('end_date_select',$this->jscal->input('values[edate]',$msData['edate']?$msData['edate']:time()));
			$GLOBALS['phpgw']->template->set_var('old_edate',$msData['edate']);
			
			$GLOBALS['phpgw']->template->pfp('out','main');
		}

		function edit_project()
		{
			if (!is_object($this->jscal))
			{
				$this->jscal = CreateObject('phpgwapi.jscalendar');
			}

			$action			= get_var('action',array('GET','POST'));
			$pro_main		= get_var('pro_main',array('GET','POST'));
			$pro_parent		= get_var('pro_parent',array('GET','POST'));
			$book_activities	= get_var('book_activities',array('POST'));
			$bill_activities	= get_var('bill_activities',array('POST'));

			$project_id		= get_var('project_id',array('GET','POST'));
			$name			= get_var('name',array('POST'));
			$values			= get_var('values',array('POST'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.list_projects',
				'pro_main'	=> $pro_main,
				'action'	=> $action,
				'project_id'	=> $project_id,
				'pro_parent'	=> $pro_parent
			);

			if ($_POST['save'] || $_POST['apply'])
			{
				//echo 'POST: SAVE';
				$this->cat_id = ($values['cat']?$values['cat']:'');
				$values['coordinator']	= $_POST['accountid'];
				$values['employees']	= $_POST['employees'];

				$values['project_id']	= $project_id;
				$values['customer']	= $_POST['abid'];

				$values['book_activities'] = $book_activities;
				$values['bill_activities'] = $bill_activities;

				$startdate = $this->jscal->input2date($values['startdate']);
				$values['sdate']	= $startdate['raw'];
				$values['sday']		= $startdate['day'];
				$values['smonth']	= $startdate['month'];
				$values['syear']	= $startdate['year'];

				$startdate = $this->jscal->input2date($values['enddate']);
				$values['edate']	= $startdate['raw'];
				$values['eday']		= $startdate['day'];
				$values['emonth']	= $startdate['month'];
				$values['eyear']	= $startdate['year'];

				$startdate = $this->jscal->input2date($values['pstartdate']);
				$values['psdate']	= $startdate['raw'];
				$values['psday']	= $startdate['day'];
				$values['psmonth']	= $startdate['month'];
				$values['psyear']	= $startdate['year'];
				
				$startdate = $this->jscal->input2date($values['penddate']);
				$values['pedate']	= $startdate['raw'];
				$values['peday']	= $startdate['day'];
				$values['pemonth']	= $startdate['month'];
				$values['peyear']	= $startdate['year'];
				
				if ($values['accounting'] == 'non')
				{
					$values['billable'] = 'N';
				}				
				if(is_array($values['budget']))
				{
					foreach($values['budget'] as $singleBudget)
					{
						$formatedBudget[$singleBudget['year']][$singleBudget['month']] = $singleBudget['text'];
					}
					$values['budget'] = $formatedBudget;
				}
				//$values['parent'] = $pro_parent;

				$error = $this->boprojects->check_values($action, $values);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message_main',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$project_id = $this->boprojects->save_project($action, $values);
					$link_data['project_id'] = $project_id;
					if($_POST['save'])
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var('message',lang('project %1 has been saved',$values['title']));
					}
				}
			}

			if($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['delete'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.delete_project';
				$link_data['pa_id'] = $project_id;
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['mstone'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.project_mstones';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['roles'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.assign_employee_roles';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($action == 'mains')
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($project_id ? lang('edit project') : lang('add project'));
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($project_id ? lang('edit job') : lang('add job'));
			}

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('edit_form' => 'form.tpl'));
			$GLOBALS['phpgw']->template->set_block('edit_form','main','mainhandle');

			$GLOBALS['phpgw']->template->set_block('edit_form','navbar','navbarhandle');

			/*$GLOBALS['phpgw']->template->set_block('edit_form','msfield1','msfield1handle');
			$GLOBALS['phpgw']->template->set_block('edit_form','msfield2','msfield2handle');
			$GLOBALS['phpgw']->template->set_block('edit_form','mslist','mslisthandle');*/

			#$GLOBALS['phpgw']->template->set_block('edit_form','rolefield1','rolefield1handle');
			#$GLOBALS['phpgw']->template->set_block('edit_form','rolefield2','rolefield2handle');
			#$GLOBALS['phpgw']->template->set_block('edit_form','rolelist','rolelisthandle');

			$GLOBALS['phpgw']->template->set_block('edit_form','accounting_act','accounting_acthandle');
			$GLOBALS['phpgw']->template->set_block('edit_form','accounting_own','accounting_ownhandle');

			//milestones
			$GLOBALS['phpgw']->template->set_block('edit_form','mstone_list','list');
			$GLOBALS['phpgw']->template->set_block('edit_form','project_data','pro');

			$prefs = $this->boprojects->read_prefs();

			$GLOBALS['phpgw']->template->set_var('addressbook_link',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.abook'));
			$GLOBALS['phpgw']->template->set_var('accounts_link',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.accounts_popup'));
			$GLOBALS['phpgw']->template->set_var('e_accounts_link',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.e_accounts_popup'));

			$GLOBALS['phpgw']->template->set_var('lang_address_book',lang('address book'));
			$link_data['menuaction'] = 'projects.uiprojects.edit_project';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'planned');
			}

			if(!$pro_parent && is_array($main) || ($pro_main == $pro_parent && is_array($main)))
			{
				$parent = $main;
			}

			if($pro_parent && !is_array($parent))
			{
				$parent = $this->boprojects->read_single_project($pro_parent,'planned');
			}

			if(is_array($parent))
			{
				$values['sdate']	= $parent['sdate']?$parent['sdate']:time();
				$values['edate']	= $parent['edate']?$parent['edate']:0;
				$values['psdate']	= $parent['psdate']?$parent['psdate']:0;
				$values['pedate']	= $parent['pedate']?$parent['pedate']:0;
			}

			if ($project_id)
			{
				$GLOBALS['phpgw']->template->set_var('navbarhandle','');
				$GLOBALS['phpgw']->template->fp('navbarhandle','navbar',True);
				$values = $this->boprojects->read_single_project($project_id);
				$GLOBALS['phpgw']->template->set_var('old_status',$values['status']);
				$GLOBALS['phpgw']->template->set_var('old_parent',$values['parent']);
				$GLOBALS['phpgw']->template->set_var('old_edate',$values['edate']);
				$GLOBALS['phpgw']->template->set_var('old_coordinator',$values['coordinator']);
				$GLOBALS['phpgw']->template->set_var('lang_choose','');
				$GLOBALS['phpgw']->template->set_var('choose','');
				$this->cat_id = $values['cat'];

				$values['sday']		= $values['sdate']?date('d',$values['sdate']):0;
				$values['smonth']	= $values['sdate']?date('m',$values['sdate']):0;
				$values['syear']	= $values['sdate']?date('Y',$values['sdate']):0;

				$values['eday']		= $values['edate']?date('d',$values['edate']):0;
				$values['emonth']	= $values['edate']?date('m',$values['edate']):0;
				$values['eyear']	= $values['edate']?date('Y',$values['edate']):0;

				$values['psday']	= $values['psdate']?date('d',$values['psdate']):0;
				$values['psmonth']	= $values['psdate']?date('m',$values['psdate']):0;
				$values['psyear']	= $values['psdate']?date('Y',$values['psdate']):0;

				$values['peday']	= $values['pedate']?date('d',$values['pedate']):0;
				$values['pemonth']	= $values['pedate']?date('m',$values['pedate']):0;
				$values['peyear']	= $values['pedate']?date('Y',$values['pedate']):0;

//-- milestones --

				/*$GLOBALS['phpgw']->template->fp('msfield1handle','msfield1',True);
				$mstones = $this->boprojects->get_mstones($project_id);

				$link_data['menuaction'] = 'projects.uiprojects.edit_mstone';

				while (is_array($mstones) && list(,$ms) = each($mstones))
				{
					$link_data['s_id'] = $ms['s_id'];
					$GLOBALS['phpgw']->template->set_var('ms_edit_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$GLOBALS['phpgw']->template->set_var('s_title',$ms['title']);
					$GLOBALS['phpgw']->template->set_var('s_edateout',$this->boprojects->formatted_edate($ms['edate']));
					$GLOBALS['phpgw']->template->fp('mslisthandle','mslist',True);
				}
				$GLOBALS['phpgw']->template->set_var('lang_edit_mstones',lang('edit milestones'));
				$GLOBALS['phpgw']->template->fp('msfield2handle','msfield2',True);
*/
				$GLOBALS['phpgw']->template->set_var('edit_mstones_button','<input type="submit" name="mstone" value="' . lang('edit milestones') . '">');
				$GLOBALS['phpgw']->template->set_var('edit_roles_button','<input type="submit" name="roles" value="' . lang('edit roles and events') . '">');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('choose','<input type="checkbox" id="createProjectID" name="values[choose]" onclick="changeProjectIDInput(this)" value="True"' . (isset($values['choose'])?' checked':'') . '>');

				switch($action)
				{
					case 'mains':
						$values['smonth']	= isset($values['smonth'])?$values['smonth']:date('m',time());
						$values['sday']		= isset($values['sday'])?$values['sday']:date('d',time());
						$values['syear']	= isset($values['syear'])?$values['syear']:date('Y',time());

						$values['emonth']	= isset($values['emonth'])?$values['emonth']:0;
						$values['eday']		= isset($values['eday'])?$values['eday']:0;
						$values['eyear']	= isset($values['eyear'])?$values['eyear']:0;

						$values['psmonth']	= isset($values['psmonth'])?$values['psmonth']:date('m',time());
						$values['psday']	= isset($values['psday'])?$values['psday']:date('d',time());
						$values['psyear']	= isset($values['psyear'])?$values['psyear']:date('Y',time());

						$values['pemonth']	= isset($values['pemonth'])?$values['pemonth']:0;
						$values['peday']	= isset($values['peday'])?$values['peday']:0;
						$values['peyear']	= isset($values['peyear'])?$values['peyear']:0;


						$values['access']	= isset($values['access'])?$values['access']:'public';
						break;
					case 'subs':
						$values['smonth']	= isset($values['smonth'])?$values['smonth']:date('m',$values['sdate']);
						$values['sday']		= isset($values['sday'])?$values['sday']:date('d',$values['sdate']);
						$values['syear']	= isset($values['syear'])?$values['syear']:date('Y',$values['sdate']);

						if($values['psdate'] > 0)
						{
							$values['psmonth']	= isset($values['psmonth'])?$values['psmonth']:date('m',$values['psdate']);
							$values['psday']	= isset($values['psday'])?$values['psday']:date('d',$values['psdate']);
							$values['psyear']	= isset($values['psyear'])?$values['psyear']:date('Y',$values['psdate']);
						}
						else
						{
							$values['psmonth'] = $values['psday'] = $values['psyear'] = 0;
						}

						if($values['edate'] > 0)
						{
							$values['emonth']	= isset($values['emonth'])?$values['emonth']:date('m',$values['edate']);
							$values['eday']		= isset($values['eday'])?$values['eday']:date('d',$values['edate']);
							$values['eyear']	= isset($values['eyear'])?$values['eyear']:date('Y',$values['edate']);
						}
						else
						{
							$values['emonth'] = $values['eday'] = $values['eyear'] = 0;
						}

						if($values['pedate'] > 0)
						{
							$values['pemonth']	= isset($values['pemonth'])?$values['pemonth']:date('m',$values['pedate']);
							$values['peday']	= isset($values['peday'])?$values['peday']:date('d',$values['pedate']);
							$values['peyear']	= isset($values['peyear'])?$values['peyear']:date('Y',$values['pedate']);
						}
						else
						{
							$values['pemonth'] = $values['peday'] = $values['peyear'] = 0;
						}
						break;
				}
			}

//ndee 130504 new date selectors
			$GLOBALS['phpgw']->template->set_var('start_date_select',$this->jscal->input('values[startdate]',$values['sdate']?$values['sdate']:time()+(60*60*24*7)));
			$GLOBALS['phpgw']->template->set_var('end_date_select',$this->jscal->input('values[enddate]',$values['edate']?$values['edate']:''));
			$GLOBALS['phpgw']->template->set_var('pstart_date_select',$this->jscal->input('values[pstartdate]',$values['psdate']?$values['psdate']:time()+(60*60*24*7)));
			$GLOBALS['phpgw']->template->set_var('pend_date_select',$this->jscal->input('values[penddate]',$values['pedate']?$values['pedate']:''));

//ndee 130504 new date selectors
			$uiwidgets	= CreateObject('projects.uiwidgets');

			if ($action == 'mains')
			{
				$cat = '<select name="values[cat]"><option value="">' . lang('None') . '</option>'
						.	$this->boprojects->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';

				$GLOBALS['phpgw']->template->set_var('cat',$cat);
				$GLOBALS['phpgw']->template->set_var('lang_number',lang('Project ID'));
				$GLOBALS['phpgw']->template->set_var('lang_choose',($project_id?'':lang('generate project id')));
				$GLOBALS['phpgw']->template->set_var('budget_select',$uiwidgets->dateSelectBox($values['budget'],'values[budget]','['.$prefs['currency'].'.c]'));

				//$GLOBALS['phpgw']->template->set_var('pcosts','<input type="text" name="values[pcosts]" value="' . $values['pcosts'] . '"> [' . $prefs['currency'] . $prefs['currency'] . '.cc]');
			}
			elseif($action == 'subs')
			{
				$GLOBALS['phpgw']->template->set_var('pro_main',$main['title'] . ' [' . $main['number'] . ']');
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action=mains&project_id='
																						. $pro_main));
				$GLOBALS['phpgw']->template->set_var('lang_sum_jobs',lang('sum jobs'));
				$GLOBALS['phpgw']->template->set_var('lang_available',lang('available'));

				$GLOBALS['phpgw']->template->set_var('ptime_main',$main['ptime']);
				$GLOBALS['phpgw']->template->set_var('ptime_jobs',sprintf("%01.2f",$main['ptime_jobs']));
				$GLOBALS['phpgw']->template->set_var('atime',sprintf("%01.2f",$main['atime']));
				$GLOBALS['phpgw']->template->set_var('lang_budget_main',lang('budget main project') . ':&nbsp;' . $prefs['currency']);
				$GLOBALS['phpgw']->template->set_var('budget_main',$main['budgetSum']);
				$GLOBALS['phpgw']->template->set_var('pbudget_jobs',sprintf("%01.2f",$main['pbudget_jobs']));
				$GLOBALS['phpgw']->template->set_var('apbudget',sprintf("%01.2f",$main['ap_budget_jobs']));

				$GLOBALS['phpgw']->template->fp('mainhandle','main',True);

				$values['coordinator']		= isset($values['coordinator'])?$values['coordinator']:$parent['coordinator'];
				$values['coordinatorout']	= isset($values['coordinatorout'])?$values['coordinatorout']:$parent['coordinatorout'];
				$values['parent']		= isset($values['parent'])?$values['parent']:$parent['project_id'];
				$values['customer']		= isset($values['customer'])?$values['customer']:$parent['customer'];
				$values['number']		= isset($values['number'])?$values['number']:$parent['number'];
				$values['investment_nr']	= isset($values['investment_nr'])?$values['investment_nr']:$parent['investment_nr'];
				$values['customer_nr']		= isset($values['customer_nr'])?$values['customer_nr']:$parent['customer_nr'];
				$values['url']			= isset($values['url'])?$values['url']:$parent['url'];
				$values['reference']		= isset($values['reference'])?$values['reference']:$parent['reference'];

				$values['budget']		= isset($values['budget'])?$values['budget']:sprintf("%01.2f",$parent['ap_budget_jobs']);
				$GLOBALS['phpgw']->template->set_var('budget_select',$uiwidgets->dateSelectBox($values['budget'],'values[budget]','['.$prefs['currency'].'.c]'));
				$values['ptime']		= isset($values['ptime'])?$values['ptime']:sprintf("%01.2f",$parent['atime']);

				$values['e_budget']		= isset($values['e_budget'])?$values['e_budget']:$parent['e_budget'];
				$values['access']		= isset($values['access'])?$values['access']:$parent['access'];
				$values['priority']		= isset($values['priority'])?$values['priority']:$parent['priority'];
				$values['accounting']		= isset($values['accounting'])?$values['accounting']:$parent['accounting'];
				$values['project_accounting_factor']	= isset($values['project_accounting_factor'])?$values['project_accounting_factor']:$parent['project_accounting_factor'];
				$values['project_accounting_factor_d']	= isset($values['project_accounting_factor_d'])?$values['project_accounting_factor_d']:$parent['project_accounting_factor_d'];
				$values['billable']		= isset($values['billable'])?$values['billable']:$parent['billable'];
				$values['inv_method']		= isset($values['inv_method'])?$values['inv_method']:$parent['inv_method'];

				$GLOBALS['phpgw']->template->set_var
				(
					'parent_select',
					'<select name="values[parent]">' . $this->boprojects->select_project_list
					(
						array
						(
							'action' => 'mainandsubs',
							#'status' => $values['status'],
							'self' => $project_id,
							'selected' => $values['parent'],
							'main' => $pro_main
						)
					) . '</select>'
				);

				$GLOBALS['phpgw']->template->set_var('lang_choose',($project_id?'':lang('generate job id')));
				$GLOBALS['phpgw']->template->set_var('cat',$this->boprojects->cats->id2name($main['cat']));
				$this->cat_id = $main['cat'];

				$GLOBALS['phpgw']->template->set_var('lang_action',lang('Edit job'));
				$GLOBALS['phpgw']->template->set_var('lang_number',lang('Job ID'));
			}

			$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);

			$month = $this->boprojects->return_date();
			$GLOBALS['phpgw']->template->set_var('month',$month['monthformatted']);

			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format($values['status'],(($action == 'mains')?True:False)));
			$GLOBALS['phpgw']->template->set_var('priority_list',$this->priority_list($values['priority']));

			$aradio = '<input type="radio" name="values[access]" value="private"' . ($values['access'] == 'private'?' checked':'') . '>' . lang('private');
			$aradio .= '<input type="radio" name="values[access]" value="public"' . ($values['access'] == 'public'?' checked':'') . '>' . lang('public') . '<br>';
			$aradio .= '<input type="radio" name="values[access]" value="anonym"' . ($values['access'] == 'anonym'?' checked':'') . '>' . lang('anonymous public');

			$GLOBALS['phpgw']->template->set_var('access',$aradio);

			$GLOBALS['phpgw']->template->set_var
			(
				'previous_select',
				$this->boprojects->select_project_list
				(
					array
					(
						'action' => 'all',
						'status' => $values['status'],
						'self' => $project_id,
						'selected' => $values['previous']
					)
				)
			);

			if($this->boprojects->siteconfig['accounting'] == 'own')
			{
				$GLOBALS['phpgw']->template->set_var('acc_employee_selected',($values['billable']!='N' && $values['accounting']=='employee'?' selected="1"':''));
				$GLOBALS['phpgw']->template->set_var('acc_project_selected',($values['billable']!='N' && $values['accounting']=='project'?' selected="1"':''));
				$GLOBALS['phpgw']->template->set_var('acc_non_billable_selected',($values['billable']=='N'?' selected="1"':''));
				$GLOBALS['phpgw']->template->set_var('project_accounting_factor',$values['project_accounting_factor']);
				$GLOBALS['phpgw']->template->set_var('project_accounting_factor_d',$values['project_accounting_factor_d']);

				$GLOBALS['phpgw']->template->fp('accounting_ownhandle','accounting_own',True);
			}
			else
			{
				if($action == 'mains')
				{
					// ------------ activites bookable ----------------------
					$GLOBALS['phpgw']->template->set_var('book_activities_list',$this->boprojects->select_activities_list($project_id,False));

					// -------------- activities billable ----------------------
					$GLOBALS['phpgw']->template->set_var('bill_activities_list',$this->boprojects->select_activities_list($project_id,True));
					$GLOBALS['phpgw']->template->fp('accounting_acthandle','accounting_act',True);
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('book_activities_list',$this->boprojects->select_pro_activities($project_id, $pro_main, False));				
    					$GLOBALS['phpgw']->template->set_var('bill_activities_list',$this->boprojects->select_pro_activities($project_id, $pro_main, True));
					$GLOBALS['phpgw']->template->fp('accounting_acthandle','accounting_act',True);
				}
			}

			$GLOBALS['phpgw']->template->set_var('discount',$values['discount']);
			$GLOBALS['phpgw']->template->set_var('dt_amount',$values['discount_type']=='amount'?'checked':'');
			$GLOBALS['phpgw']->template->set_var('dt_percent',$values['discount_type']=='percent'?'checked':'');

			$GLOBALS['phpgw']->template->set_var('budget',$values['budget']);
			$GLOBALS['phpgw']->template->set_var('e_budget',$values['e_budget']);
			$GLOBALS['phpgw']->template->set_var('number',$values['number']);
			$GLOBALS['phpgw']->template->set_var('title',$values['title']);
			$GLOBALS['phpgw']->template->set_var('descr',$values['descr']);
			$GLOBALS['phpgw']->template->set_var('ptime',$values['ptime']);
			$GLOBALS['phpgw']->template->set_var('investment_nr',$values['investment_nr']);
			$GLOBALS['phpgw']->template->set_var('customer_nr',$values['customer_nr']);

			$GLOBALS['phpgw']->template->set_var('inv_method',$values['inv_method']);
			$GLOBALS['phpgw']->template->set_var('reference',$values['reference']);
			$GLOBALS['phpgw']->template->set_var('url',$values['url']);

			$GLOBALS['phpgw']->template->set_var('result',$values['result']);
			$GLOBALS['phpgw']->template->set_var('test',$values['test']);
			$GLOBALS['phpgw']->template->set_var('quality',$values['quality']);

//--------- coordinator -------------

			$GLOBALS['phpgw']->template->set_var('lang_coordinator',($pro_main?lang('job manager'):lang('Coordinator')));

			if (!is_object($GLOBALS['phpgw']->uiaccountsel))
			{
				$GLOBALS['phpgw']->uiaccountsel = CreateObject('phpgwapi.uiaccountsel');
			}

			$GLOBALS['phpgw']->template->set_var
			(
				'coordinator_accounts',
				$GLOBALS['phpgw']->uiaccountsel->selection
				(
					'accountid',
					'coordinator_accounts',
					$values['coordinator'] ? $values['coordinator'] : $GLOBALS['phpgw_info']['user']['account_id'],
					'accounts',
					0,false,'style="width:250px;"'
				)
			);

			if(is_array($values['employees']))
			{
				$selectedAccounts = $values['employees'];
			}
			else
			{
				$selectedAccounts = @array_flip($this->boprojects->get_acl_for_project($project_id?$project_id:$parent['project_id']));
			}
			$GLOBALS['phpgw']->template->set_var
			(
				'employees_accounts',
				$GLOBALS['phpgw']->uiaccountsel->selection
				(
					'employees[]',
					'employees_accounts',
					$selectedAccounts,
					'accounts',
					5,false,'style="width:250px;"'
				)
			);

			$abid = $values['customer'];
			$customer = $this->boprojects->read_single_contact($abid);
			if ($customer[0]['org_name'] == '') 
			{ 
				$name = $customer[0]['n_given'] . ' ' . 
				$customer[0]['n_family']; 
			}
			else 
			{
				$name = $customer[0]['org_name'] . ' [ ' . 
				$customer[0]['n_given'] . ' ' . 
				$customer[0]['n_family'] . ' ]'; 
			}

			$GLOBALS['phpgw']->template->set_var('name',$name);
			$GLOBALS['phpgw']->template->set_var('abid',$abid);

			if ($project_id && $this->boprojects->edit_perms(array('action' => $action,'coordinator' => $values['coordinator'],'main_co' => $main['coordinator'],
													'parent_co' => $parent['coordinator'],'type' => 'delete')))
			{
				$GLOBALS['phpgw']->template->set_var('delete_button','<input type="submit" name="delete" value="' . lang('Delete') .'">');
			}


// the milestones part
			if ($project_id)
			{
				$pro = $this->boprojects->read_single_project($project_id);
				$GLOBALS['phpgw']->template->set_var('title_pro',$pro['title']);
				$GLOBALS['phpgw']->template->set_var('pro_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action='
														. ($pro['level']==0?'mains':'subs') . '&project_id=' . $project_id));
				$GLOBALS['phpgw']->template->set_var('coordinator_pro',$pro['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_pro',$pro['number']);
				$GLOBALS['phpgw']->template->set_var('customer_pro',$pro['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_pro',$pro['url']);
				$GLOBALS['phpgw']->template->parse('pro','project_data',True);

				$GLOBALS['phpgw']->template->set_var('message',$message);
				$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$mstones = $this->boprojects->get_mstones($project_id);

				$link_data['menuaction'] = 'projects.uiprojects.editMilestone';
				$GLOBALS['phpgw']->template->set_var('add_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

				for($i=0;$i<count($mstones);$i++)
				{
					$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
		
					$link_data['s_id']			= $mstones[$i]['s_id'];
					$link_data['edit']			= True;
					
					if($mstones[$i]['description'])
					{
						$msTitle = '<b>'.$mstones[$i]['title'].'</b><br>'.$mstones[$i]['description'];
					}
					else
					{
						$msTitle = '<b>'.$mstones[$i]['title'].'</b>';
					}
					$GLOBALS['phpgw']->template->set_var(array
					(
						'datedue'	=> $this->boprojects->formatted_edate($mstones[$i]['edate']),
						'edit_url'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
						'title'		=> $msTitle
					));
					unset($link_data['edit']);

					if ($this->boprojects->edit_perms(array('action' => $action,'project_id' => $project_id,'mstone' => True,'type' => 'delete')))
					{
						$link_data['menuaction']	= 'projects.uiprojects.editMilestone';
						$link_data['deleteMS']		= True;

						$GLOBALS['phpgw']->template->set_var('delete_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
						$GLOBALS['phpgw']->template->set_var('delete_img','<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','delete')
																		. '" border="0" title="' . lang('delete') . '">');
						unset($link_data['deleteMS']);
					}
					$GLOBALS['phpgw']->template->parse('list','mstone_list',True);
				}
			}

			$GLOBALS['phpgw']->template->set_var('old_edate',$values['edate']);
			$GLOBALS['phpgw']->template->set_var('s_id',$values['s_id']);
			$GLOBALS['phpgw']->template->set_var('lang_new',lang('new milestone'));
			$GLOBALS['phpgw']->template->set_var('lang_save_mstone',lang('save milestone'));
			$GLOBALS['phpgw']->template->set_var('new_checked',$values['new']?' checked':'');
			$GLOBALS['phpgw']->template->set_var('title',$GLOBALS['phpgw']->strip_html($values['title']));
			$GLOBALS['phpgw']->template->set_var('description',$GLOBALS['phpgw']->strip_html($values['description']));

			if (!$values['edate'])
			{
				$values['edate']	= time();
			}

#			$GLOBALS['phpgw']->template->set_var('end_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[eyear]',$values['eyear']),
#				$this->sbox->getMonthText('values[emonth]',$values['emonth']),
#				$this->sbox->getDays('values[eday]',$values['eday'])));
#			$GLOBALS['phpgw']->template->set_var('end_date_select',
#				$this->jscal->input('values[enddate]',$values['edate']?$values['edate']:''));

			unset($uiwidgets);

// the file manager part
			$uiwidgets	= CreateObject('projects.uiwidgets');
			$bolink		= CreateObject('infolog.bolink');
			$link_data['tabpage'] = 3;
			
			$headValues = array(lang('name'),lang('size'),'');
			if ($project_id)
			{
				$attachedFiles = $bolink->get_links('projects',$project_id,'file');
	
				if(is_array($attachedFiles))
				{
					foreach($attachedFiles as $fileData)
					{
						$fileLinkData = array
						(
							'menuaction'	=> 'infolog.bolink.get_file',
							'app'		=> 'projects',
							'id'		=> $project_id,
							'filename'	=> $fileData['id']
						);
						$rowID = $uiwidgets->tableViewAddRow();
						$uiwidgets->tableViewAddTextCell($rowID,'<a href="'.
							$GLOBALS['phpgw']->link('/index.php',$fileLinkData).'">'.
							$fileData['id'].'</a>');
						$uiwidgets->tableViewAddTextCell($rowID,$fileData['size'],'center');
						$uiwidgets->tableViewAddTextCell($rowID,'<input type="checkbox" name="selected_file['.$fileData['link_id'].']">','center');
					}
				}
				$GLOBALS['phpgw']->template->set_var('files_table',$uiwidgets->tableView($headValues));
			}
			
			$link_data['menuaction'] = 'projects.uiprojects.handle_fileupload';
			$GLOBALS['phpgw']->template->set_var('action_url_files',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out','edit_form');

			
		}

		function view_project()
		{
			$action		= $_GET['action'] ? $_GET['action'] : 'mains';
			$pro_main	= $_GET['pro_main'];
			$project_id	= $_GET['project_id'];
			$public_view	= $_GET['public_view'];
			$referer	= $_GET['referer'];

			if(!$referer)  //$_POST['back'] && !$_POST['done'] && !$_POST['edit'])
			{
				$referer = $_SERVER['HTTP_REFERER'];
			}

			//echo 'REFERER: ' . $referer;

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.view_project',
				'pro_main'	=> $pro_main,
				'action'	=> $action,
				'project_id'	=> $project_id,
				'referer'	=> $referer,
				'public_view'	=> $public_view
			);

			if($_POST['back'])
			{
				Header('Location: ' . $referer);
				//$GLOBALS['phpgw']->redirect_link($referer);
			}

			if($_POST['edit'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.edit_project';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['mstone'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.project_mstones';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['roles'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.assign_employee_roles';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['done'])
			{
				if ($public_view)
				{
					$menu = 'projects.uiprojects.list_projects_home';
				}
				else
				{
					$menu = 'projects.uiprojects.list_projects';
				}
				$link_data['menuaction'] = $menu;
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . 
				($pro_main?lang('view job'):lang('view project'));

			if (isset($public_view))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->set_app_langs();
			}
			else
			{
				$this->display_app_header();
			}


			$GLOBALS['phpgw']->template->set_file(array('view' => 'view3.tpl'));
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$prefs = $this->boprojects->read_prefs();

			$values = $this->boprojects->read_single_project($project_id);

			//_debug_array($values);

			if ($action == 'mains' || $action == 'amains')
			{
				$GLOBALS['phpgw']->template->set_var('project_view',
					$this->boprojects->createHTMLOutput('mains',$values)
				);
			}
			else if($pro_main && $action == 'subs')
			{
				$main = $this->boprojects->read_single_project($pro_main);

				$GLOBALS['phpgw']->template->set_var('project_view',
					$this->boprojects->createHTMLOutput('subs', $values, $main)
				);
			}
			
			$GLOBALS['phpgw']->template->pfp('out','view');


			if (!isset($public_view))
			{
				$GLOBALS['phpgw']->hooks->process(array(
					'location'   => 'projects_view',
					'project_id' => $project_id
				));
			}

		}

		function delete_project()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= intval(get_var('pro_main',array('POST','GET')));

			$subs		= get_var('subs',array('POST'));
			$pa_id		= intval(get_var('pa_id',array('POST','GET')));

			switch($action)
			{
				case 'mains':	$deleteheader = lang('are you sure you want to delete this project');
								$header = lang('delete project');
								break;
				case 'subs':	$deleteheader = lang('are you sure you want to delete this job');
								$header = lang('delete job');
								break;
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.list_projects',
				'pro_main'		=> $pro_main,
				'pa_id'			=> $pa_id,
				'action'		=> $action
			);

			if ($_POST['yes'])
			{
				$this->boprojects->delete_project($pa_id,(isset($_POST['subs'])?True:False));
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['no'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header;

			$this->display_app_header();
			$GLOBALS['phpgw']->template->set_file(array('pa_delete' => 'delete.tpl'));

			$GLOBALS['phpgw']->template->set_var('lang_subs','');
			$GLOBALS['phpgw']->template->set_var('subs', '');

			$GLOBALS['phpgw']->template->set_var('deleteheader',$deleteheader);
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));

			$exists = $this->boprojects->exists('mains', 'par', $num ='', $pa_id);

			if ($exists)
			{
				$GLOBALS['phpgw']->template->set_var('lang_subs',lang('Do you also want to delete all sub projects ?'));
				$GLOBALS['phpgw']->template->set_var('subs','<input type="checkbox" name="subs" value="True">');
			}

			$link_data['menuaction'] = 'projects.uiprojects.delete_project';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->pfp('out','pa_delete');
		}

		function abook()
		{
			$start		= get_var('start',array('POST'));
			$cat_id 	= get_var('cat_id',array('POST'));
			$sort		= get_var('sort',array('GET','POST'));
			$order		= get_var('order',array('GET','POST'));
			$filter		= get_var('filter',array('POST'));
			$qfilter	= get_var('qfilter',array('POST'));
			$query		= get_var('query',array('POST'));

			$GLOBALS['phpgw']->template->set_file(array('abook_list_t' => 'addressbook.tpl'));
			$GLOBALS['phpgw']->template->set_block('abook_list_t','abook_list','list');

			$this->boprojects->cats->app_name = 'addressbook';

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_var('lang_action',lang('Address book'));
			$GLOBALS['phpgw']->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.abook',
				'start'		=> $start,
				'sort'		=> $sort,
				'order'		=> $order,
				'cat_id'	=> $cat_id,
				'filter'	=> $filter,
				'query'		=> $query
			);

			$link_data_nm = array
			(
				'menuaction'	=> 'projects.uiprojects.abook',
				'start'		=> $start,
				'cat_id'	=> $cat_id,
				'filter'	=> $filter,
				'query'		=> $query
			);

			if (! $start) { $start = 0; }

			if (!$filter) { $filter = 'none'; }

			$qfilter = 'tid=n';

			switch ($filter)
			{
				case 'none': break;		
				case 'private': $qfilter .= ',access=private'; break;
				case 'yours': $qfilter .= ',owner=' . $this->boprojects->account; break;
			}

			if ($cat_id)
			{
				$qfilter .= ',cat_id=' . $cat_id;
			}
 
			$entries = $this->boprojects->read_abook($start, $query, $qfilter, $sort, $order);

// --------------------------------- nextmatch ---------------------------

			$left = $this->nextmatchs->left('/index.php',$start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$start));

// -------------------------- end nextmatch ------------------------------------

			$GLOBALS['phpgw']->template->set_var('cats_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('cats_list',$this->boprojects->cats->formatted_list('select','all',$cat_id,True));
			$GLOBALS['phpgw']->template->set_var('filter_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($filter));
			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $query)));

// ---------------- list header variable template-declarations --------------------------

// -------------- list header variable template-declaration ------------------------

			$GLOBALS['phpgw']->template->set_var('sort_company',$this->nextmatchs->show_sort_order($sort,'org_name',$order,'/index.php',lang('Company'),$link_data_nm));
			$GLOBALS['phpgw']->template->set_var('sort_firstname',$this->nextmatchs->show_sort_order($sort,'n_given',$order,'/index.php',lang('Firstname'),$link_data_nm));
			$GLOBALS['phpgw']->template->set_var('sort_lastname',$this->nextmatchs->show_sort_order($sort,'n_family',$order,'/index.php',lang('Lastname'),$link_data_nm));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));

// ------------------------- end header declaration --------------------------------

			for ($i=0;$i<count($entries);$i++)
			{
				$GLOBALS['phpgw']->template->set_var('tr_color',$this->nextmatchs->alternate_row_color($tr_color));
				$firstname = $entries[$i]['n_given'];
				if (!$firstname) { $firstname = '&nbsp;'; }
				$lastname = $entries[$i]['n_family'];
				if (!$lastname) { $lastname = '&nbsp;'; }
				$company = $entries[$i]['org_name'];
				if (!$company) { $company = '&nbsp;'; }

// ---------------- template declaration for list records -------------------------- 

				$GLOBALS['phpgw']->template->set_var(array('company' 	=> $company,
									'firstname' 	=> $firstname,
									'lastname'	=> $lastname,
									'abid'		=> $entries[$i]['id']));

				$GLOBALS['phpgw']->template->parse('list','abook_list',True);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ' - ' . lang('addressbook');
			$GLOBALS['phpgw']->common->phpgw_header();
			$GLOBALS['phpgw']->template->parse('out','abook_list_t',True);
			$GLOBALS['phpgw']->template->p('out');

			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function list_budget()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list budget');

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'list_budget.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','pcosts','pc');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','project_main','main');

			$prefs = $this->boprojects->read_prefs();
			$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);

			if (!$action)
			{
				$action = 'mains';
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.list_budget',
				'pro_main'	=> $pro_main,
				'action'	=> $action
			);

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'budget','mains');
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',
					'menuaction=projects.uiprojects.view_project&action=mains&project_id='.
					$pro_main));
					
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);

				$GLOBALS['phpgw']->template->set_var('ubudget_main',$main['u_budget_jobs']);    //sprintf("%01.2f",$main['u_budget_jobs']));
				$GLOBALS['phpgw']->template->set_var('abudget_main',sprintf("%01.2f",$main['a_budget_jobs']));

				$GLOBALS['phpgw']->template->set_var('pbudget_main',sprintf("%01.2f",$main['budgetSum']));
				$GLOBALS['phpgw']->template->parse('main','project_main',True);
			}

			$pro = $this->boprojects->list_projects(array('action' => $action,'parent' => $pro_main,'page' => 'budget'));

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
				$GLOBALS['phpgw']->template->set_var('lang_action',lang('Jobs'));
			}
			else
			{
				$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
							. $this->boprojects->select_project_list(array('status' => $this->status, 'selected' => $pro_main)) . '</select>';
				$GLOBALS['phpgw']->template->set_var('lang_action',lang('Work hours'));
			}

			$GLOBALS['phpgw']->template->set_var('action_list',$action_list);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format($this->status));

// ---------------- list header variable template-declarations --------------------------

			$GLOBALS['phpgw']->template->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'p_number',$this->order,'/index.php',($pro_main?lang('job id'):lang('Project ID')),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));

			$GLOBALS['phpgw']->template->set_var('sort_planned',$this->nextmatchs->show_sort_order($this->sort,'budget',$this->order,'/index.php',lang('planned'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_used',lang('used'));
			$GLOBALS['phpgw']->template->set_var('sort_used_jobs',lang('used - jobs included'));
			$GLOBALS['phpgw']->template->set_var('sort_available_budget',lang('budget available'));

// -------------- end header declaration ---------------------------------------

            for ($i=0;$i<count($pro);$i++)
            {
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------

				$link_data['project_id'] = $pro[$i]['project_id'];
				if ($action == 'mains')
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_budget&action=subs&pro_main=' . $pro[$i]['project_id']);
				}
				else
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_hours&project_id='
									. $pro[$i]['project_id'] . '&action=hours&pro_main=' . $pro_main);
				}

				$GLOBALS['phpgw']->template->set_var(array
				(
					'number'	=> $pro[$i]['number'],
					'sub_url'	=> $projects_url,
					'title'		=> $pro[$i]['title']?$pro[$i]['title']:lang('browse'),
					'p_budget'	=> ($pro[$i]['budget']?$pro[$i]['budgetSum']:'&nbsp;'),
					'u_budget'	=> $pro[$i]['u_budget_colored'],
					'u_budget_jobs'	=> $pro[$i]['u_budget_jobs_colored'],
					'b_budget'	=> sprintf("%01.2f",$pro[$i]['b_budget']),
					'b_budget_jobs'	=> sprintf("%01.2f",$pro[$i]['b_budget_jobs']),
					'a_budget'	=> sprintf("%01.2f",$pro[$i]['a_budget']),
					'a_budget_jobs'	=> sprintf("%01.2f",$pro[$i]['a_budget_jobs']),
				));
				$GLOBALS['phpgw']->template->parse('list','projects_list',True);

				$sum_a_budget_jobs += $pro[$i]['a_budget_jobs'];
				$sum_a_budget += $pro[$i]['a_budget'];
				$sum_u_budget += $pro[$i]['u_budget'];
				$sum_b_budget += $pro[$i]['b_budget'];
				$sum_u_budget_jobs += $pro[$i]['u_budget_jobs'];
				$sum_b_budget_jobs += $pro[$i]['b_budget_jobs'];
			}

// ------------------------- end record declaration ------------------------

// --------------- template declaration for sum  --------------------------

			$GLOBALS['phpgw']->template->set_var('lang_sum_budget',lang('sum budget'));
			$GLOBALS['phpgw']->template->set_var('sum_budget',$this->boprojects->sum_budget(array('action' => $action,'parent' => $pro_main)));

			$GLOBALS['phpgw']->template->set_var('sum_budget_used',$action=='subs'?sprintf("%01.2f",$sum_u_budget):'');
			$GLOBALS['phpgw']->template->set_var('sum_a_budget',$action=='subs'?sprintf("%01.2f",$sum_a_budget):'');
			$GLOBALS['phpgw']->template->set_var('sum_b_budget',$action=='subs'?sprintf("%01.2f",$sum_b_budget):'');

			$GLOBALS['phpgw']->template->set_var('sum_budget_jobs',$action == 'mains'?sprintf("%01.2f",$sum_u_budget_jobs):'');

			$GLOBALS['phpgw']->template->set_var('sum_b_budget_jobs',$action == 'mains'?sprintf("%01.2f",$sum_b_budget_jobs):'');

			$GLOBALS['phpgw']->template->set_var('sum_a_budget_jobs',$action == 'mains'?sprintf("%01.2f",$sum_a_budget_jobs):'');

// ----------------------- end sum declaration ----------------------------

			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
		}

		function project_mstones()
		{
			$action		= get_var('action',array('GET','POST'));
			$project_id	= get_var('project_id',array('GET','POST'));
			$values		= get_var('values',array('POST'));
			$s_id		= get_var('s_id',array('GET','POST'));

			if(!$_POST['save'] && !$_GET['delete'] && !$_POST['done'] && !$_GET['edit'])
			{
				$referer = get_var('referer',array('POST'));
			}
			if($_POST['save'] || $_GET['delete'] || $_POST['done'] || $_GET['back'] || $_GET['edit'])
			{
				$referer = get_var('referer',array('GET'));
			}
			if(!$referer)  //$_POST['back'] && !$_POST['done'] && !$_POST['edit'])
			{
				$referer = $_SERVER['HTTP_REFERER'];
			}

			//echo 'REFERER: ' . $referer;

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.project_mstones',
				'action'	=> $action,
				'project_id'	=> $project_id,
				'referer'	=> $referer
			);

			if ($_POST['save'])
			{
				$values['s_id']		= $values['new']?'':$s_id;
				$values['project_id']	= $project_id;
				$error = $this->boprojects->check_mstone($values);
				if(is_array($error))
				{
					$message = $GLOBALS['phpgw']->common->error_list($error);
				}
				else
				{
					$this->boprojects->save_mstone($values);
					$message = lang('milestone has been saved');
				}
			}

			if ($_POST['done'])
			{
				Header('Location: ' . $referer);
			}

			if ($_GET['delete'])
			{
				$this->boprojects->delete_item(array('id' => $s_id));
				$message = lang('milestone has been deleted');
			}

			if($_GET['edit'])
			{
				$values = $this->boprojects->get_single_mstone($s_id);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($s_id?lang('edit milestone'):lang('add milestone'));
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('mstone_list_t' => 'list_mstones.tpl'));
			$GLOBALS['phpgw']->template->set_block('mstone_list_t','mstone_list','list');
			$GLOBALS['phpgw']->template->set_block('mstone_list_t','project_data','pro');

			$pro = $this->boprojects->read_single_project($project_id);
			$GLOBALS['phpgw']->template->set_var('title_pro',$pro['title']);
			$GLOBALS['phpgw']->template->set_var('pro_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action='
														. ($pro['level']==0?'mains':'subs') . '&project_id=' . $project_id));
			$GLOBALS['phpgw']->template->set_var('coordinator_pro',$pro['coordinatorout']);
			$GLOBALS['phpgw']->template->set_var('number_pro',$pro['number']);
			$GLOBALS['phpgw']->template->set_var('customer_pro',$pro['customerout']);
			$GLOBALS['phpgw']->template->set_var('url_pro',$pro['url']);
			$GLOBALS['phpgw']->template->parse('pro','project_data',True);

			$GLOBALS['phpgw']->template->set_var('message',$message);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$mstones = $this->boprojects->get_mstones($project_id);

			for($i=0;$i<count($mstones);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

				$link_data['s_id']			= $mstones[$i]['s_id'];
				$link_data['edit']			= True;

				$GLOBALS['phpgw']->template->set_var(array
				(
					'datedue'	=> $this->boprojects->formatted_edate($mstones[$i]['edate']),
					'edit_url'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'description'	=> $mstones[$i]['description'],
					'title'		=> $mstones[$i]['title']
				));
				unset($link_data['edit']);

				if ($this->boprojects->edit_perms(array('action' => $action,'project_id' => $project_id,'mstone' => True,'type' => 'delete')))
				{
					$link_data['menuaction']	= 'projects.uiprojects.project_mstones';
					$link_data['delete']		= True;

					$GLOBALS['phpgw']->template->set_var('delete_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$GLOBALS['phpgw']->template->set_var('delete_img','<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','delete')
																		. '" border="0" title="' . lang('delete') . '">');
					unset($link_data['delete']);
				}
				$GLOBALS['phpgw']->template->parse('list','mstone_list',True);
			}

			$GLOBALS['phpgw']->template->set_var('old_edate',$values['edate']);
			$GLOBALS['phpgw']->template->set_var('s_id',$values['s_id']);
			$GLOBALS['phpgw']->template->set_var('lang_new',lang('new milestone'));
			$GLOBALS['phpgw']->template->set_var('lang_save_mstone',lang('save milestone'));
			$GLOBALS['phpgw']->template->set_var('new_checked',$values['new']?' checked':'');
			$GLOBALS['phpgw']->template->set_var('title',$GLOBALS['phpgw']->strip_html($values['title']));
			$GLOBALS['phpgw']->template->set_var('description',$GLOBALS['phpgw']->strip_html($values['description']));

			if (!$values['edate'])
			{
				$values['emonth']	= $values['emonth']?$values['emonth']:date('m',time());
				$values['eday']		= $values['eday']?$values['eday']:date('d',time());
				$values['eyear']	= $values['eyear']?$values['eyear']:date('Y',time());
			}
			else
			{
				$values['eday'] = date('d',$values['edate']);
				$values['emonth'] = date('m',$values['edate']);
				$values['eyear'] = date('Y',$values['edate']);
			}

			$GLOBALS['phpgw']->template->set_var('end_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[eyear]',$values['eyear']),
																							$this->sbox->getMonthText('values[emonth]',$values['emonth']),
																							$this->sbox->getDays('values[eday]',$values['eday'])));

			$GLOBALS['phpgw']->template->pfp('out','mstone_list_t',True);
		}

		function assign_employee_roles()
		{
			$action		= get_var('action',array('GET','POST'));
			$r_id		= get_var('r_id',array('GET','POST'));
			$project_id	= get_var('project_id',array('GET','POST'));
			$values		= get_var('values',array('POST'));

			if(!$_POST['save'] && !$_GET['delete'] && !$_POST['done'] && !$_GET['edit'])
			{
				$referer = get_var('referer',array('POST'));
			}
			if($_POST['save'] || $_GET['delete'] || $_POST['done'] || $_GET['edit'])
			{
				$referer = get_var('referer',array('GET'));
			}
			if(!$referer)  //$_POST['back'] && !$_POST['done'] && !$_POST['edit'])
			{
				$referer = $_SERVER['HTTP_REFERER'];
			}

			//echo 'REFERER: ' . $referer;

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.assign_employee_roles',
				'action'		=> $action,
				'project_id'	=> $project_id,
				's_id'			=> $s_id,
				'referer'		=> $referer
			);

			if ($_POST['save'])
			{
				//$values['s_id']		= $s_id;
				$values['project_id']	= $project_id;
				$this->boprojects->save_employee_role($values);
				$GLOBALS['phpgw']->template->set_var('message',lang('assignment has been saved'));
			}

			if ($_POST['done'])
			{
				Header('Location: ' . $referer);
				//$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_GET['delete'])
			{
				$this->boprojects->delete_item(array('id' => $r_id,'action' => 'emp_role'));
				$message = lang('assignment has been deleted');
			}

			if($_GET['edit'])
			{
				list($values) = $this->boprojects->get_employee_roles(array('project_id' => $project_id,'account_id' => $_GET['account_id']));
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('assign roles and events');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('role_list_t' => 'form_emp_roles.tpl'));
			$GLOBALS['phpgw']->template->set_block('role_list_t','role_list','list');
			$GLOBALS['phpgw']->template->set_var('message',$message);

			$roles = $this->boprojects->get_employee_roles(array('project_id' => $project_id,'formatted' => True));

			$GLOBALS['phpgw']->template->set_var('sort_name',lang('employee'));
			$GLOBALS['phpgw']->template->set_var('sort_role',lang('role'));

			if (!$this->boprojects->edit_perms(array('action' => $action,'project_id' => $project_id,'mstone' => True,'type' => 'delete')))
			{
				$delete_rights = 'no';
			}

			$emps	= $this->boprojects->get_acl_for_project($project_id);
			$co	= $this->boprojects->return_value('co',$project_id);
			for ($i=0;$i<count($roles);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

				$GLOBALS['phpgw']->template->set_var('emp_name',$roles[$i]['emp_name']);

				if(is_array($emps))
				{
					if(in_array($roles[$i]['account_id'],$emps) || $co == $roles[$i]['account_id'])
					{
						$link_data['account_id']	= $roles[$i]['account_id'];
						$link_data['edit']			= True; 
						$GLOBALS['phpgw']->template->set_var('edit_link','<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '">');
						$GLOBALS['phpgw']->template->set_var('end_link','</a>');
						$link_data['edit']			= False;

						$link_data['r_id'] = $roles[$i]['r_id'];
						$link_data['delete'] = True;
						$GLOBALS['phpgw']->template->set_var('delete_role',($delete_rights=='no'?'':'<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '">'));
						$link_data['delete'] = False;
						$GLOBALS['phpgw']->template->set_var('delete_img',($delete_rights=='no'?'':'<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','delete')
																	. '" . border="0" title="' . lang('delete') . '"></a>'));
					}
				}
				$GLOBALS['phpgw']->template->set_var('role_name',$roles[$i]['role_name']);
				$GLOBALS['phpgw']->template->set_var('events',$roles[$i]['events']);
				$GLOBALS['phpgw']->template->parse('list','role_list',True);
			}

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('role_select',$this->boprojects->action_format($values['role_id']));
			$GLOBALS['phpgw']->template->set_var('event_select',$this->boprojects->action_format($values['events'],'event'));
			$GLOBALS['phpgw']->template->set_var('lang_select_role',lang('select'));
			$GLOBALS['phpgw']->template->set_var('emp_select',$this->employee_format(array('type' => 'selectbox','project_id' => $project_id,'selected' => $values['account_id']
																							,'project_only' => True,'admins_included' => True)));
			$GLOBALS['phpgw']->template->set_var('lang_assign',lang('assign'));
			$GLOBALS['phpgw']->template->pfp('out','role_list_t',True);
		}
	}
?>
