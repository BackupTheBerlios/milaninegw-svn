<?php
	/*******************************************************************\
	* eGroupWare - Projects                                             *
	* http://www.egroupware.org                                         *
	*                                                                   *
	* Project Manager                                                   *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright 2000 - 2004 Free Software Foundation, Inc.              *
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
	/* $Id: class.uiconfig.inc.php,v 1.13.2.3 2004/11/06 12:15:28 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.uiconfig.inc.php,v $

	class uiconfig
	{
		var $action;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'edit_activity'		=> True,
			'list_activities'	=> True,
			'list_admins'		=> True,
			'list_roles'		=> True,
			'list_employees'	=> True,
			'edit_admins'		=> True,
			'abook'				=> True,
			'preferences'		=> True,
			'delete_pa'			=> True,
			'list_employees'	=> True,
			'list_events'		=> True
		);

		function uiconfig()
		{
			$this->boconfig		= CreateObject('projects.boconfig');
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->start		= $this->boconfig->start;
			$this->query		= $this->boconfig->query;
			$this->filter		= $this->boconfig->filter;
			$this->order		= $this->boconfig->order;
			$this->sort		= $this->boconfig->sort;
			$this->cat_id		= $this->boconfig->cat_id;

			$this->siteconfig	= $this->boconfig->boprojects->siteconfig;
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
				'cat_id'	=> $this->cat_id
			);
			$this->boconfig->boprojects->save_sessiondata($data, $action);
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
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_cdate',lang('Date created'));
			$GLOBALS['phpgw']->template->set_var('lang_last_update',lang('last update'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));
			$GLOBALS['phpgw']->template->set_var('lang_access',lang('access'));

			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_jobs',lang('Jobs'));
			$GLOBALS['phpgw']->template->set_var('lang_ttracker',lang('time tracker'));
			$GLOBALS['phpgw']->template->set_var('lang_archiv',lang('archive'));
			$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_roles',lang('roles'));
			$GLOBALS['phpgw']->template->set_var('lang_event',lang('event'));

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

			$GLOBALS['phpgw']->template->set_var('lang_add_milestone',lang('add milestone'));
			$GLOBALS['phpgw']->template->set_var('lang_milestones',lang('milestones'));

			$GLOBALS['phpgw']->template->set_var('lang_result',lang('result'));
			$GLOBALS['phpgw']->template->set_var('lang_test',lang('test'));
			$GLOBALS['phpgw']->template->set_var('lang_quality',lang('quality check'));
		}

		function display_app_header()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'idots')
			{
				$GLOBALS['phpgw']->template->set_file(array('header' => 'header.tpl'));
				$GLOBALS['phpgw']->template->set_block('header','projects_header');
				$GLOBALS['phpgw']->template->set_block('header','projects_admin_header');

				if ($this->boconfig->boprojects->isprojectadmin())
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
			$this->set_app_langs();
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		function accounts_popup()
		{
			$GLOBALS['phpgw']->accounts->accounts_popup('projects');
		}

		function e_accounts_popup()
		{
			$GLOBALS['phpgw']->accounts->accounts_popup('e_projects');
		}

		function employee_format($selected = '')
		{
			$emps = $this->boconfig->selected_employees();

			//_debug_array($employees);
			//_debug_array($selected);
			while (is_array($emps) && list($null,$account) = each($emps))
			{
				$s .= '<option value="' . $account['account_id'] . '"';
				if($selected == $account['account_id'])
				{
					$s .= ' SELECTED';
				}
				$s .= '>';
				$s .= $GLOBALS['phpgw']->common->display_fullname($account['account_lid'],$account['account_firstname'],$account['account_lastname'])
						. '</option>' . "\n";
			}
			return $s;
		}

		function list_employees()
		{
			$id			= $_GET['id'];
			$account_id	= $_GET['account_id'];
			$values		= $_POST['values'];

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_employees',
				'action'		=> 'accounting'
			);

			if($account_id)
			{
				list($values) = $this->boconfig->read_accounting_factors(array('account_id' => $account_id,'limit' => False));
			}

			if ($values['save'])
			{
				//_debug_array($values);
				$this->boconfig->save_accounting_factor($values);
				$GLOBALS['phpgw']->template->set_var('message',($account_id?lang('factor has been updated'):lang('factor has been saved')));
			}

			if ($_GET['delete'])
			{
				$this->boconfig->delete_pa('accounting',$id);
				$GLOBALS['phpgw']->template->set_var('message',lang('factor has been deleted'));
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('accounting');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('emp_list_t' => 'list_employees.tpl'));
			$GLOBALS['phpgw']->template->set_block('emp_list_t','emp_list','list');

			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$emps = $this->boconfig->read_accounting_factors();

//--------------------------------- nextmatch --------------------------------------------
 
			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

    		$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boconfig->total_records,$this->start));
 
// ------------------------------ end nextmatch ------------------------------------------
 
//------------------- list header variable template-declarations -------------------------

			$GLOBALS['phpgw']->template->set_var('sort_name',$this->nextmatchs->show_sort_order($this->sort,'account_id',$this->order,'/index.php',lang('employee'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_per_hour',$this->nextmatchs->show_sort_order($this->sort,'accounting',$this->order,'/index.php',lang('per hour'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_per_day',$this->nextmatchs->show_sort_order($this->sort,'d_accounting',$this->order,'/index.php',lang('per day'),$link_data));
			$GLOBALS['phpgw']->template->set_var('currency',$GLOBALS['phpgw_info']['user']['preferences']['common']['currency']);

			$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('accounting'));
// -------------------------- end header declaration --------------------------------------

			for ($i=0;$i<count($emps);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

				$GLOBALS['phpgw']->template->set_var(array
				(
					'emp_name'				=> $emps[$i]['account_name'],
					'edit_url'				=> $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_employees&account_id='
																	. $emps[$i]['account_id']),
					'factor'				=> $emps[$i]['accounting'],
					'd_factor'				=> $emps[$i]['d_accounting'],
					'delete_emp'			=> $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.list_employees&id='
																	. $emps[$i]['id'] . '&delete=True'),
					'delete_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','delete'),
					'lang_delete_factor'	=> lang('delete factor')
				));
				$GLOBALS['phpgw']->template->fp('list','emp_list',True);
			}
			$GLOBALS['phpgw']->template->set_var('accounting',$values['accounting']);
			$GLOBALS['phpgw']->template->set_var('d_accounting',$values['d_accounting']);
			$GLOBALS['phpgw']->template->set_var('lang_save_factor',lang('save factor'));
			$GLOBALS['phpgw']->template->set_var('emp_select',$this->employee_format($values['account_id']));

			$this->save_sessiondata('accounting');
			$GLOBALS['phpgw']->template->pfp('out','emp_list_t',True);
		}


		function delete_pa()
		{
			$action		= get_var('action',array('POST','GET'));
			$pa_id		= intval(get_var('pa_id',array('POST','GET')));

			switch($action)
			{
				case 'act':	$menu = 'projects.uiconfig.list_activities';
							$deleteheader = lang('are you sure you want to delete this activity');
							$header = lang('delete activity');
							break;
			}

			$link_data = array
			(
				'menuaction'	=> $menu,
				'pa_id'			=> $pa_id,
				'action'		=> $action
			);

			if ($_POST['yes'])
			{
				$del = $pa_id;

				if ($subs)
				{
					$this->boconfig->delete_pa($action, $del, True);
				}
				else
				{
					$this->boconfig->delete_pa($action, $del, False);
				}
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

			$link_data['menuaction'] = 'projects.uiconfig.delete_pa';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->pfp('out','pa_delete');
		}

		function list_activities()
		{
			$action = 'act';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list activities');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('activities_list_t' => 'listactivities.tpl'));
			$GLOBALS['phpgw']->template->set_block('activities_list_t','activities_list','list');

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_activities',
				'action'		=> 'act'
			);

			$act = $this->boconfig->list_activities();

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boconfig->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

            $GLOBALS['phpgw']->template->set_var('cat_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('categories_list',$this->boconfig->boprojects->cats->formatted_list('select','all',$this->cat_id,'True'));
            $GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
            $GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));

			switch($this->siteconfig['activity_bill'])
			{
				case 'wu':	$bill = lang('Bill per workunit'); break;
				case 'h':	$bill = lang('Bill per hour'); break;
				default :	$bill = lang('Bill per hour'); break;
			}

// ----------------- list header variable template-declarations ---------------------------
  
			$GLOBALS['phpgw']->template->set_var('currency',$GLOBALS['phpgw_info']['user']['preferences']['common']['currency']);
			$GLOBALS['phpgw']->template->set_var('sort_num',$this->nextmatchs->show_sort_order($this->sort,'a_number',$this->order,'/index.php',lang('Activity ID')));
			$GLOBALS['phpgw']->template->set_var('sort_descr',$this->nextmatchs->show_sort_order($this->sort,'descr',$this->order,'/index.php',lang('Description')));
			$GLOBALS['phpgw']->template->set_var('sort_billperae',$this->nextmatchs->show_sort_order($this->sort,'billperae',$this->order,'/index.php',$bill));

			if ($this->siteconfig['activity_bill'] == 'wu')
			{
				$GLOBALS['phpgw']->template->set_var('sort_minperae','<td width="10%" align="right">' . $this->nextmatchs->show_sort_order($this->sort,'minperae',
									$this->order,'/index.php',lang('Minutes per workunit') . '</td>'));
			}

// ---------------------------- end header declaration -------------------------------------

            for ($i=0;$i<count($act);$i++)
            {
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
				$descr = $GLOBALS['phpgw']->strip_html($act[$i]['descr']);
				if (! $descr)
				{
					$descr  = '&nbsp;';
				}

// ------------------- template declaration for list records -------------------------
      
				$GLOBALS['phpgw']->template->set_var(array('num'	=> $GLOBALS['phpgw']->strip_html($act[$i]['number']),
										'descr' => $descr,
									'billperae' => $act[$i]['billperae']));

				if ($this->siteconfig['activity_bill'] == 'wu')
				{
					$GLOBALS['phpgw']->template->set_var('minperae','<td align="right">' . $act[$i]['minperae'] . '</td>');
				}

				$link_data['menuaction']	= 'projects.uiconfig.edit_activity';
				$link_data['activity_id']	= $act[$i]['activity_id'];
				$GLOBALS['phpgw']->template->set_var('edit',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$link_data['menuaction']	= 'projects.uiconfig.delete_pa';
				$link_data['pa_id']	= $act[$i]['activity_id'];
				$GLOBALS['phpgw']->template->set_var('delete',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$GLOBALS['phpgw']->template->fp('list','activities_list',True);

// ------------------------------- end record declaration --------------------------------

			}

// ------------------------- template declaration for Add Form ---------------------------

			$link_data['menuaction'] = 'projects.uiconfig.edit_activity';
			unset($link_data['activity_id']);
			unset($link_data['pa_id']);
			$GLOBALS['phpgw']->template->set_var('add_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
			$this->save_sessiondata('act');
			$GLOBALS['phpgw']->template->pfp('out','activities_list_t',True);

// -------------------------------- end Add form declaration ------------------------------

		}

		function edit_activity()
		{
			$activity_id	= get_var('activity_id',array('POST','GET'));
			$values		= get_var('values',array('POST'));
			
			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_activities',
				'action'	=> 'act'
			);

			if ($_POST['save'])
			{
				$this->cat_id		= ($values['cat']?$values['cat']:'');
				$values['activity_id']	= $activity_id;

				$error = $this->boconfig->check_pa_values($values);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->boconfig->save_activity($values);
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

			if($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($activity_id?lang('edit activity'):lang('add activity'));

			$this->display_app_header();

			$form = ($activity_id?'edit':'add');

			$GLOBALS['phpgw']->template->set_file(array('edit_activity' => 'formactivity.tpl'));

			$GLOBALS['phpgw']->template->set_var('done_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiconfig.edit_activity&activity_id=' . $activity_id));

			$GLOBALS['phpgw']->template->set_var('currency',$GLOBALS['phpgw_info']['user']['preferences']['common']['currency']);

			if ($activity_id)
			{
				$values = $this->boconfig->read_single_activity($activity_id);
				$this->cat_id = $values['cat'];
				$GLOBALS['phpgw']->template->set_var('lang_choose','');
				$GLOBALS['phpgw']->template->set_var('choose','');
				$GLOBALS['phpgw']->template->set_var('edit_mode','update');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_choose',lang('Generate Activity ID ?'));
				$GLOBALS['phpgw']->template->set_var('choose','<input type="checkbox" name="values[choose]" value="True" onclick="changeProjectIDInput(this)">');
				$GLOBALS['phpgw']->template->set_var('edit_mode','add');
			}

			$GLOBALS['phpgw']->template->set_var('cats_list',$this->boconfig->boprojects->cats->formatted_list('select','all',$this->cat_id,True));
			$GLOBALS['phpgw']->template->set_var('num',$GLOBALS['phpgw']->strip_html($values['number']));
			$descr  = $GLOBALS['phpgw']->strip_html($values['descr']);
			if (! $descr) $descr = '';
			$GLOBALS['phpgw']->template->set_var('descr',$descr);

			if ($values['remarkreq'] == 'N'):
				$stat_sel[0]=' selected';
			elseif ($values['remarkreq'] == 'Y'):
				$stat_sel[1]=' selected';
			endif;

			$remarkreq_list = '<option value="N"' . $stat_sel[0] . '>' . lang('No') . '</option>' . "\n"
					. '<option value="Y"' . $stat_sel[1] . '>' . lang('Yes') . '</option>' . "\n";

			$GLOBALS['phpgw']->template->set_var('remarkreq_list',$remarkreq_list);

			if ($this->siteconfig['activity_bill'] == 'wu')
			{
				$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per workunit'));
				$GLOBALS['phpgw']->template->set_var('lang_minperae',lang('Minutes per workunit'));
				$GLOBALS['phpgw']->template->set_var('minperae','<input type="text" name="values[minperae]" value="' . $values['minperae'] . '">');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per hour'));
			}

			$GLOBALS['phpgw']->template->set_var('billperae',$values['billperae']);

			#$link_data['menuaction']	= 'projects.uiconfig.delete_pa';
			#$link_data['pa_id']	= $values[$i]['activity_id'];
			#$GLOBALS['phpgw']->template->set_var('deleteurl',$GLOBALS['phpgw']->link('/index.php',$link_data));
			#$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));

			$this->save_sessiondata('act');
			$GLOBALS['phpgw']->template->pfp('out','edit_activity');
		}

		function list_admins()
		{
			$action = get_var('action',array('POST','GET'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.edit_admins',
				'action'		=> $action
			);

			if ($_POST['add'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			switch($action)
			{
				case 'psale':		$header_info = lang('seller list'); break;
				case 'pmanager':	$header_info = lang('manager list'); break;
				default:			$header_info = lang('administrator list'); break;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header_info;
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('admin_list_t' => 'list_admin.tpl'));
			$GLOBALS['phpgw']->template->set_block('admin_list_t','admin_list','list');
			$GLOBALS['phpgw']->template->set_block('admin_list_t','group_list','glist');

			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$link_data['menuaction'] = 'projects.uiconfig.list_admins';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$admins = $this->boconfig->list_admins($action);

			//_debug_array($admins);

//--------------------------------- nextmatch --------------------------------------------
 
			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

    		$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boconfig->total_records,$this->start));
 
// ------------------------------ end nextmatch ------------------------------------------
 
//------------------- list header variable template-declarations -------------------------

			$GLOBALS['phpgw']->template->set_var('sort_lid',lang('Username'));
			$GLOBALS['phpgw']->template->set_var('sort_lastname',lang('Lastname'));
			$GLOBALS['phpgw']->template->set_var('sort_firstname',lang('Firstname'));
			$GLOBALS['phpgw']->template->set_var('lang_group',lang('group'));
// -------------------------- end header declaration --------------------------------------

			for ($i=0;$i<count($admins);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
				$lid = $admins[$i]['lid'];

				if ($admins[$i]['type']=='u')
				{
					$GLOBALS['phpgw']->template->set_var(array
					(
						'lid'		=> $admins[$i]['lid'],
						'firstname'	=> $admins[$i]['firstname'],
						'lastname'	=> $admins[$i]['lastname']
					));
					$GLOBALS['phpgw']->template->fp('list','admin_list',True);
				}
				if ($admins[$i]['type']=='g')
				{
					$GLOBALS['phpgw']->template->set_var('lid',$admins[$i]['lid']);
					$GLOBALS['phpgw']->template->fp('glist','group_list',True);
				}
			}

			$GLOBALS['phpgw']->template->pfp('out','admin_list_t',True);
			$this->save_sessiondata($action);
		}

		function edit_admins()
		{
			$users	= get_var('users',array('POST'));
			$groups = get_var('groups',array('POST'));
			$action = get_var('action',array('POST','GET'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_admins',
				'action'		=> $action
			);

			if ($_POST['save'])
			{
				$this->boconfig->edit_admins($action,$users,$groups);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			switch($action)
			{
				case 'psale':		$header_info = lang('edit seller list'); break;
				case 'pmanager':	$header_info = lang('edit manager list'); break;
				default:
					$header_info = lang('edit administrator list'); 
					break;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header_info;
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('admin_add' => 'form_admin.tpl'));

			$link_data['menuaction'] = 'projects.uiconfig.edit_admins';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('users_list',$this->boconfig->selected_admins($action));
			$GLOBALS['phpgw']->template->set_var('groups_list',$this->boconfig->selected_admins($action,'group'));
			$GLOBALS['phpgw']->template->set_var('lang_users_list',lang('Select users'));
			$GLOBALS['phpgw']->template->set_var('lang_groups_list',lang('Select groups'));

			$GLOBALS['phpgw']->template->pfp('out','admin_add');
		}

		function list_roles()
		{
			$role_id	= get_var('role_id',array('POST','GET'));
			$role_name	= $_POST['role_name'];
			$role_type	= preg_match("/^role$|^cost$/",get_var('role_type',array('GET')))?get_var('role_type',array('GET')):'role';

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_roles',
				'role_id'	=> $role_id,
				'role_type'	=> $role_type,
				'action'	=> 'role'
			);

			if ($_POST['save'])
			{
				$error = $this->boconfig->check_pa_values(array($role_type.'_name' => $role_name),$role_type);
				if(is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->boconfig->save_role($role_name,$role_type);
					$GLOBALS['phpgw']->template->set_var('message',($role_id?lang($role_type.' %1 has been updated',$role_name):lang($role_type.' %1 has been saved',$role_name)));
				}
			}

			if ($_GET['delete'])
			{
				$this->boconfig->delete_pa($role_type,$role_id);
				$GLOBALS['phpgw']->template->set_var('message',lang($role_type.' has been deleted'));
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			switch($role_type)
			{
				case 'cost':
					$GLOBALS['phpgw_info']['flags']['app_header'] = 
						lang('projects') . ': ' . lang('costs list');
					break;
				default:
					$GLOBALS['phpgw_info']['flags']['app_header'] = 
						lang('projects') . ': ' . lang('roles list');
					break;
			}
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('roles_list_t' => 'list_roles.tpl'));
			$GLOBALS['phpgw']->template->set_block('roles_list_t','roles_list','list');

			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$roles = $this->boconfig->list_roles($role_type);

//--------------------------------- nextmatch --------------------------------------------
 
			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

	    		$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boconfig->total_records,$this->start));
 
// ------------------------------ end nextmatch ------------------------------------------
 
//------------------- list header variable template-declarations -------------------------

			$GLOBALS['phpgw']->template->set_var('sort_name',$this->nextmatchs->show_sort_order($this->sort,$role_type.'_name',$this->order,'/index.php',lang('name'),$link_data));

// -------------------------- end header declaration --------------------------------------

			for ($i=0;$i<count($roles);$i++)
			{
				$link_data = array
				(
					'menuaction'	=> 'projects.uiconfig.list_roles',
					'role_id'	=> $roles[$i][$role_type.'_id'],
					'role_type'	=> $role_type,
					'delete'	=> True
				);
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

				$GLOBALS['phpgw']->template->set_var('role_name',$roles[$i][$role_type.'_name']);
				$GLOBALS['phpgw']->template->set_var('delete_role',
					$GLOBALS['phpgw']->link('/index.php',$link_data)
				);

				$GLOBALS['phpgw']->template->fp('list','roles_list',True);
			}
			$GLOBALS['phpgw']->template->set_var('lang_add_role',lang('add '.$role_type));
			$this->save_sessiondata($role_type);
			$GLOBALS['phpgw']->template->pfp('out','roles_list_t',True);
		}

		function list_events()
		{
			//$event_id	= get_var('event_id',array('POST','GET'));
			$values		= $_POST['values'];

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_events'
			);

			if ($_POST['save'])
			{
				$this->boconfig->save_event($values);
				$GLOBALS['phpgw']->template->set_var('message',($event_id?lang('event %1 has been updated',$role_name):lang('event %1 has been saved',$role_name)));
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit events');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('event_list_t' => 'list_events.tpl'));
			$GLOBALS['phpgw']->template->set_block('event_list_t','event_list','list');

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$events = $this->boconfig->boprojects->list_events();

			for ($i=0;$i<count($events);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

				if($events[$i]['event_type'] == 'limits')
				{
					$extra = $events[$i]['event_extra'] . '&nbsp;' . lang('days before');
					$values['limit'] = $values['limit']?$values['limit']:$events[$i]['event_extra'];
				}
				if($events[$i]['event_type'] == 'percent')
				{
					$extra = $events[$i]['event_extra']==0?100:$events[$i]['event_extra'] . '&nbsp;' . lang('% to');
					$values['percent'] = $values['percent']?$values['percent']:$events[$i]['event_extra'];
				}
				$GLOBALS['phpgw']->template->set_var('event_name',lang($events[$i]['event_name']));
				$GLOBALS['phpgw']->template->set_var('event_extra',$extra);
				$GLOBALS['phpgw']->template->fp('list','event_list',True);
			}

			$GLOBALS['phpgw']->template->set_var('event_select_limit',$this->boconfig->boprojects->action_format($selected = $values['event_id_limit'],$action = 'event',$type = 'limits'));
			$GLOBALS['phpgw']->template->set_var('event_select_percent',$this->boconfig->boprojects->action_format($selected = $values['event_id_percent'],$action = 'event',$type = 'percent'));

			$GLOBALS['phpgw']->template->set_var('lang_days',lang('days'));
			$GLOBALS['phpgw']->template->set_var('lang_before',lang('before'));
			$GLOBALS['phpgw']->template->set_var('lang_alarm',lang('alarm'));

			$GLOBALS['phpgw']->template->set_var('limit',$values['limit']);
			$GLOBALS['phpgw']->template->set_var('percent',$values['percent']);

			$GLOBALS['phpgw']->template->pfp('out','event_list_t',True);
		}

		function abook()
		{
			$start		= get_var('start',array('POST'));
			$cat_id 	= get_var('cat_id',array('POST'));
			$sort		= get_var('sort',array('POST'));
			$order		= get_var('order',array('POST'));
			$filter		= get_var('filter',array('POST'));
			$qfilter	= get_var('qfilter',array('POST'));
			$query		= get_var('query',array('POST'));

			$GLOBALS['phpgw']->template->set_file(array('abook_list_t' => 'addressbook.tpl'));
			$GLOBALS['phpgw']->template->set_block('abook_list_t','abook_list','list');

			$this->boprojects->cats->app_name = 'addressbook';

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_var('title',$GLOBALS['phpgw_info']['site_title']);
			$GLOBALS['phpgw']->template->set_var('lang_action',lang('Address book'));
			$GLOBALS['phpgw']->template->set_var('charset',$GLOBALS['phpgw']->translation->translate('charset'));
			$GLOBALS['phpgw']->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.abook',
				'start'			=> $start,
				'sort'			=> $sort,
				'order'			=> $order,
				'cat_id'		=> $cat_id,
				'filter'		=> $filter,
				'query'			=> $query
			);

			if (! $start) { $start = 0; }

			if (!$filter) { $filter = 'none'; }

			$qfilter = 'tid=n';

			switch ($filter)
			{
				case 'none': break;		
				case 'private': $qfilter .= ',access=private'; break;
				case 'yours': $qfilter .= ',owner=' . $this->account; break;
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

			$GLOBALS['phpgw']->template->set_var('sort_company',$this->nextmatchs->show_sort_order($sort,'org_name',$order,'/index.php',lang('Company'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_firstname',$this->nextmatchs->show_sort_order($sort,'per_first_name',$order,'/index.php',lang('Firstname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_lastname',$this->nextmatchs->show_sort_order($sort,'per_last_name',$order,'/index.php',lang('Lastname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));

// ------------------------- end header declaration --------------------------------

			for ($i=0;$i<count($entries);$i++)
			{
				$GLOBALS['phpgw']->template->set_var('tr_color',$this->nextmatchs->alternate_row_color($tr_color));
				$firstname = $entries[$i]['per_first_name'];
				if (!$firstname) { $firstname = '&nbsp;'; }
				$lastname = $entries[$i]['per_last_name'];
				if (!$lastname) { $lastname = '&nbsp;'; }
				$company = $entries[$i]['org_name'];
				if (!$company) { $company = '&nbsp;'; }

// ---------------- template declaration for list records -------------------------- 

				$GLOBALS['phpgw']->template->set_var(array('company' 	=> $company,
									'firstname' 	=> $firstname,
									'lastname'		=> $lastname,
									'abid'			=> $entries[$i]['contact_id']));

				$GLOBALS['phpgw']->template->parse('list','abook_list',True);
			}

			$GLOBALS['phpgw']->template->parse('out','abook_list_t',True);
			$GLOBALS['phpgw']->template->p('out');

			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function preferences()
		{
			if ($_POST['save'])
			{
				$this->boconfig->save_prefs($_POST['cols']);
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			$link_data = array
			(
				'menuaction' => 'projects.uiconfig.preferences'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('preferences');

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$GLOBALS['phpgw']->template->set_file(array('prefs' => 'preferences.tpl'));
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$prefs = $this->boconfig->boprojects->read_prefs();

			//_debug_array($prefs);

			$columns = array
			(
				'priority',
				'number',
				'investment_nr',
				'coordinatorout',
				'customerout',
				'customer_nr',
				'sdate_formatted',
				'edate_formatted',
				'psdate_formatted',
				'pedate_formatted',
				'previousout',
				'phours',
				'budget',
				'e_budget',
				'url',
				'reference',
				'accountingout',
				'project_accounting_factor',
				'project_accounting_factor_d',
				'billableout',
				'discountout',
				'mstones'
			);

			foreach($columns as $col)
			{
				switch($col)
				{
					case 'number':			$cname = lang('project id'); break;
					case 'priority':		$cname = lang('priority'); break;
					case 'sdate_formatted':		$cname = lang('start date'); break;
					case 'edate_formatted':		$cname = lang('date due'); break;
					case 'phours':			$cname = lang('time planned'); break;
					case 'budget':			$cname = lang('budget'); break;
					case 'e_budget':		$cname = lang('extra budget'); break;
					case 'coordinatorout':		$cname = lang('coordinator'); break;
					case 'customerout':		$cname = lang('customer'); break;
					case 'investment_nr':		$cname = lang('investment nr'); break;
					case 'previousout':		$cname = lang('previous'); break;
					case 'customer_nr':		$cname = lang('customer nr'); break;
					case 'url':			$cname = lang('url'); break;
					case 'reference':		$cname = lang('reference'); break;
					case 'accountingout':		$cname = lang('accounting'); break;
					case 'billableout':		$cname = lang('billable'); break;
					case 'psdate_formatted':	$cname = lang('start date planned'); break;
					case 'pedate_formatted':	$cname = lang('date due planned'); break;
					case 'discountout':		$cname = lang('discount'); break;
					case 'mstones':			$cname = lang('milestones'); break;
					case 'project_accounting_factor':	$cname = lang('accounting factor') . ' ' . lang('per hour'); break;
					case 'project_accounting_factor_d':	$cname = lang('accounting factor') . ' ' . lang('per day'); break;
				}

				$sel .= '<option value="' . $col . (in_array($col,$prefs['columns'])?'" SELECTED':'"') . '>' . "\n" 
						. $cname . '</option>' . "\n";
			}

			$GLOBALS['phpgw']->template->set_var('lang_select_columns',lang('columns to show in the projects list'));
			$GLOBALS['phpgw']->template->set_var('column_select',$sel);
			$GLOBALS['phpgw']->template->pfp('out','prefs');
		}
	}
?>
