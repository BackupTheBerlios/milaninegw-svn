<?php
	/*******************************************************************\
	* eGroupWare - Projects                                             *
	* http://www.egroupware.org                                         *
	*                                                                   *
	* Project Manager                                                   *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright 2000 - 2003 Free Software Foundation, Inc               *
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
	/* $Id: class.uideliveries.inc.php,v 1.38.2.1 2004/11/06 12:15:29 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.uideliveries.inc.php,v $

	class uideliveries
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
			'list_projects'		=> True,
			'delivery'			=> True,
			'list_deliveries'	=> True,
			'show_delivery'		=> True,
			'fail'				=> True
		);

		function uideliveries()
		{
			$action = get_var('action',array('POST','GET'));

			$this->boprojects				= CreateObject('projects.boprojects',True, $action);
			$this->bodeliveries				= CreateObject('projects.bodeliveries');
			$this->nextmatchs				= CreateObject('phpgwapi.nextmatchs');
			$this->sbox						= CreateObject('phpgwapi.sbox');
			$this->cats						= CreateObject('phpgwapi.categories');
			$this->account					= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->grants					= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->start					= $this->boprojects->start;
			$this->query					= $this->boprojects->query;
			$this->filter					= $this->boprojects->filter;
			$this->order					= $this->boprojects->order;
			$this->sort						= $this->boprojects->sort;
			$this->cat_id					= $this->boprojects->cat_id;
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
			$this->boprojects->save_sessiondata($data, $action);
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
			$GLOBALS['phpgw']->template->set_var('lang_work_date',lang('Work date'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));
			$GLOBALS['phpgw']->template->set_var('lang_customer',lang('Customer'));
			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_project_num',lang('Project ID'));
			$GLOBALS['phpgw']->template->set_var('lang_project',lang('Project'));
			$GLOBALS['phpgw']->template->set_var('lang_stats',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_delivery_num',lang('Delivery ID'));
			$GLOBALS['phpgw']->template->set_var('lang_delivery_date',lang('Delivery date'));
			$GLOBALS['phpgw']->template->set_var('lang_activity',lang('Activity'));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
			$GLOBALS['phpgw']->template->set_var('lang_print_delivery',lang('Print delivery'));
			$GLOBALS['phpgw']->template->set_var('lang_sumaes',lang('Sum workunits'));
			$GLOBALS['phpgw']->template->set_var('lang_position',lang('Position'));
			$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Workunits'));
			$GLOBALS['phpgw']->template->set_var('lang_delivery_date',lang('Delivery date'));
			$GLOBALS['phpgw']->template->set_var('lang_work_date',lang('Work date'));
			$GLOBALS['phpgw']->template->set_var('lang_submit',lang('Submit'));
			$GLOBALS['phpgw']->template->set_var('lang_jobs',lang('Jobs'));
		}

		function display_app_header()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'idots')
			{
				$GLOBALS['phpgw']->template->set_file(array('header' => 'header.tpl'));
				$GLOBALS['phpgw']->template->set_block('header','projects_header');

				if ($this->boprojects->isprojectadmin('pad'))
				{
					$GLOBALS['phpgw']->template->set_var('admin_info',lang('Administrator'));
					$GLOBALS['phpgw']->template->set_var('break1','&nbsp;|&nbsp;');
					$GLOBALS['phpgw']->template->set_var('space1','&nbsp;&nbsp;&nbsp;');
					$GLOBALS['phpgw']->template->set_var('link_activities',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_activities&action=act'));                                                                                                         
					$GLOBALS['phpgw']->template->set_var('lang_activities',lang('Activities'));                                                                                                                               
					$GLOBALS['phpgw']->template->set_var('link_budget',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_budget&action=mains'));
					$GLOBALS['phpgw']->template->set_var('lang_budget',lang('budget'));
				}

				if ($this->boprojects->isprojectadmin('pbo'))
				{
					$GLOBALS['phpgw']->template->set_var('book_info',lang('Bookkeeper'));
					$GLOBALS['phpgw']->template->set_var('break2','&nbsp;|&nbsp;');
					$GLOBALS['phpgw']->template->set_var('space2','&nbsp;&nbsp;&nbsp;');
					$GLOBALS['phpgw']->template->set_var('link_billing',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uibilling.list_projects&action=mains'));
					$GLOBALS['phpgw']->template->set_var('lang_billing',lang('Billing'));
					$GLOBALS['phpgw']->template->set_var('link_delivery',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uideliveries.list_projects&action=mains'));
					$GLOBALS['phpgw']->template->set_var('lang_delivery',lang('Deliveries'));
				}

				$GLOBALS['phpgw']->template->set_var('link_jobs',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&action=subs'));
				$GLOBALS['phpgw']->template->set_var('link_hours',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojecthours.list_hours'));
				$GLOBALS['phpgw']->template->set_var('link_statistics',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uistatistics.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
				$GLOBALS['phpgw']->template->set_var('link_projects',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
				$GLOBALS['phpgw']->template->set_var('link_archiv',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.archive&action=amains'));
				$GLOBALS['phpgw']->template->set_var('lang_archiv',lang('archive'));

				$GLOBALS['phpgw']->template->fp('app_header','projects_header');
			}
			$this->set_app_langs();
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}


		function list_projects()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_parent	= get_var('pro_parent',array('POST','GET'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_parent?lang('list jobs'):lang('list projects'));
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'bill_list.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');

			if (!$action)
			{
				$action = 'mains';
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uideliveries.list_projects',
				'pro_parent'	=> $pro_parent,
				'action'		=> $action,
				'cat_id'		=> $this->cat_id
			);

			if (!$this->start)
			{
				$this->start = 0;
			}

			if (!$pro_parent)
			{
				$pro_parent = 0;
			}

			$params = array
			(
				'start'		=> $this->start,
				'limit'		=> True,
				'query'		=> $this->query,
				'filter'	=> $this->filter,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'status'	=> 'active',
				'cat_id'	=> $this->cat_id,
				'type'		=> $action,
				'parent'	=> $pro_parent
			);

			$pro = $this->boprojects->list_projects($params);

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

			if ($action == 'mains')
			{
				$action_list= '<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '" name="form">' . "\n"
							. '<select name="cat_id" onChange="this.form.submit();"><option value="">' . lang('Select category') . '</option>' . "\n"
							. $this->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';
				$GLOBALS['phpgw']->template->set_var(lang_header,lang('Project list'));
			}
			else
			{
				$action_list= '<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .'" name="form">' . "\n"
							. '<select name="pro_parent" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
							. $this->boprojects->select_project_list('mains', $status, $pro_parent) . '</select>';
				$GLOBALS['phpgw']->template->set_var('lang_header',lang('Job list'));
			}

			$GLOBALS['filter']	= $this->filter;
			$GLOBALS['query']	= $this->query;

			$GLOBALS['phpgw']->template->set_var('action_list',$action_list);
			$GLOBALS['phpgw']->template->set_var('filter_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));

// ---------------- list header variable template-declarations --------------------------

			$GLOBALS['phpgw']->template->set_var(sort_number,$this->nextmatchs->show_sort_order($this->sort,'num',$this->order,'/index.php',lang('Project ID'),$link_data));

			if ($action == 'mains')
			{
				$GLOBALS['phpgw']->template->set_var(sort_action,$this->nextmatchs->show_sort_order($this->sort,'customer',$this->order,'/index.php',lang('Customer'),$link_data));
				$lang_action = '<td width="5%" align="center">' . lang('Jobs') . '</td>' . "\n";
				$GLOBALS['phpgw']->template->set_var('lang_action',$lang_action);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var(sort_action,$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Start date'),$link_data));
				$GLOBALS['phpgw']->template->set_var('lang_action','');
			}

			$GLOBALS['phpgw']->template->set_var('sort_status',$this->nextmatchs->show_sort_order($this->sort,'status',$this->order,'/index.php',lang('Status'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_end_date',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('Date due'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_coordinator',$this->nextmatchs->show_sort_order($this->sort,'coordinator',$this->order,'/index.php',lang('Coordinator'),$link_data));
			$GLOBALS['phpgw']->template->set_var('h_lang_part',lang('Delivery note'));
			$GLOBALS['phpgw']->template->set_var('h_lang_partlist',lang('Delivery list'));

// -------------- end header declaration ---------------------------------------

            for ($i=0;$i<count($pro);$i++)
            {
				$this->nextmatchs->template_alternate_row_color(&$GLOBALS['phpgw']->template);
				$edate = $pro[$i]['edate'];
				if ($edate == 0)
				{
					$edateout = '&nbsp;';
				}
				else
				{
					$month  = $GLOBALS['phpgw']->common->show_date(time(),'n');
					$day    = $GLOBALS['phpgw']->common->show_date(time(),'d');
					$year   = $GLOBALS['phpgw']->common->show_date(time(),'Y');

					$edate = $edate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
					$edateout = $GLOBALS['phpgw']->common->show_date($edate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					if (mktime(2,0,0,$month,$day,$year) == $edate) { $edateout = '<b>' . $edateout . '</b>'; }
					if (mktime(2,0,0,$month,$day,$year) >= $edate) { $edateout = '<font color="CC0000"><b>' . $edateout . '</b></font>'; }
				}

				if ($action == 'mains')
				{
					$td_action = ($pro[$i]['customerout']?$pro[$i]['customerout']:'&nbsp;');
				}
				else
				{
					$td_action = ($pro[$i]['sdateout']?$pro[$i]['sdateout']:'&nbsp;');
				}

// --------------- template declaration for list records -------------------------------------

				$GLOBALS['phpgw']->template->set_var(array
				(
					'number'		=> $pro[$i]['number'],
					'td_action'		=> $td_action,
					'status'		=> lang($pro[$i]['status']),
					'title'			=> ($pro[$i]['title']?$pro[$i]['title']:'&nbsp;'),
					'end_date'		=> $edateout,
					'coordinator'	=> $pro[$i]['coordinatorout']
				));

				$link_data['project_id'] = $pro[$i]['project_id'];
				$link_data['menuaction'] = 'projects.uideliveries.delivery';

				$GLOBALS['phpgw']->template->set_var('part',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('lang_part',lang('Delivery'));

				$link_data['menuaction']	= 'projects.uideliveries.list_deliveries';
				$link_data['action']		= 'del';
				$GLOBALS['phpgw']->template->set_var('partlist',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('lang_partlist',lang('Delivery list'));

				if ($action == 'mains')
				{
					$action_entry = '<td align="center"><a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uideliveries.list_projects'
																. '&pro_parent=' . $pro[$i]['project_id'] . '&action=subs') . '">' . lang('Jobs')
																. '</a></td>' . "\n";
					$GLOBALS['phpgw']->template->set_var('action_entry',$action_entry);
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('action_entry','');
				}

				$GLOBALS['phpgw']->template->parse('list','projects_list',True);
			}

// ------------------------- end record declaration ------------------------

			$GLOBALS['phpgw']->template->set_var('lang_all_partlist',lang('All delivery notes'));
			$GLOBALS['phpgw']->template->set_var('all_partlist',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uideliveries.list_deliveries&action=del'
											. '&project_id='));

			$GLOBALS['phpgw']->template->set_var('lang_all_part2list','');
			$GLOBALS['phpgw']->template->set_var('all_part2list','');

			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
			$this->save_sessiondata($action);
		}

		function delivery()
		{
			$action			= get_var('action',array('POST','GET'));
			$project_id		= get_var('project_id',array('POST','GET'));

			$Delivery		= get_var('Delivery',array('POST','GET'));
			$delivery_id	= get_var('delivery_id',array('POST','GET'));

			$values			= get_var('values',array('POST'));
			$select			= get_var('select',array('POST'));
			$referer		= get_var('referer',array('POST'));

			if (! $Delivery)
			{
				$referer = $GLOBALS['HTTP_SERVER_VARS']['HTTP_REFERER'] ? $GLOBALS['HTTP_SERVER_VARS']['HTTP_REFERER'] : $GLOBALS['HTTP_REFERER'];
			}

			if (!$project_id)
			{
				Header('Location: ' . $referer);
			}

			$prefs = $this->boprojects->get_prefs();

			if ($Delivery)
			{
				$values['project_id']	= $project_id;
				$pro = $this->boprojects->read_single_project($project_id);
				$values['customer']		= $pro['customer'];

				if ($delivery_id)
				{
					$values['delivery_id'] = $delivery_id;
				}

				$error = $this->bodeliveries->check_values($values, $select);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					if ($delivery_id)
					{
						$this->bodeliveries->update_delivery($values, $select);
					}
					else
					{
						$delivery_id = $this->bodeliveries->delivery($values, $select);
					}
				}
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uideliveries.delivery',
				'pro_parent'	=> $pro_parent,
				'action'		=> $action,
				'project_id'	=> $project_id,
				'delivery_id'	=> $delivery_id
			);

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('hours_list_t' => 'del_listhours.tpl'));
			$GLOBALS['phpgw']->template->set_block('hours_list_t','hours_list','list');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('create delivery');

			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('hidden_vars','<input type="hidden" name="referer" value="' . $referer . '">');
			$GLOBALS['phpgw']->template->set_var('doneurl',$referer);

			$pro = $this->boprojects->read_single_project($project_id);

			$title = $GLOBALS['phpgw']->strip_html($pro['title']);
			if (! $title)  $title  = '&nbsp;';
			$GLOBALS['phpgw']->template->set_var('project',$title . ' [' . $GLOBALS['phpgw']->strip_html($pro['number']) . ']');

			if (!$pro['customer'])
			{
				$GLOBALS['phpgw']->template->set_var('customer',lang('You have no customer selected !'));
			}
			else
			{
				$customer = $this->boprojects->read_single_contact($pro['customer']);
				if (!$customer[0]['org_name']) { $customername = $customer[0]['n_given'] . ' ' . $customer[0]['n_family']; }
				else { $customername = $customer[0]['org_name'] . ' [ ' . $customer[0]['n_given'] . ' ' . $customer[0]['n_family'] . ' ]'; }
				$GLOBALS['phpgw']->template->set_var('customer',$customername);
			}

			if(!$delivery_id)
			{
				$GLOBALS['phpgw']->template->set_var('lang_choose',lang('Generate Delivery ID ?'));
				$GLOBALS['phpgw']->template->set_var('choose','<input type="checkbox" name="values[choose]" value="True">');
				$GLOBALS['phpgw']->template->set_var('print_delivery',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uideliveries.fail'));
				$GLOBALS['phpgw']->template->set_var('delivery_num',$values['delivery_num']);
				$hours = $this->bodeliveries->read_hours($project_id, $action);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_choose','');
				$GLOBALS['phpgw']->template->set_var('choose','');
				$GLOBALS['phpgw']->template->set_var('print_delivery',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uideliveries.show_delivery'
																		. '&delivery_id=' . $delivery_id));
				$del = $this->bodeliveries->read_single_delivery($delivery_id);
				$GLOBALS['phpgw']->template->set_var('delivery_num',$del['delivery_num']);
				$hours = $this->bodeliveries->read_delivery_hours($project_id, $delivery_id, $action);
			}

			if ($del['date'])
			{
				$values['month'] = date('m',$del['date']);
				$values['day'] = date('d',$del['date']);
				$values['year'] = date('Y',$del['date']);
			}
			else
			{
				$values['month'] = date('m',time());
				$values['day'] = date('d',time());
				$values['year'] = date('Y',time());
			}

			$GLOBALS['phpgw']->template->set_var('date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[year]',$values['year']),
																				$this->sbox->getMonthText('values[month]',$values['month']),
																				$this->sbox->getDays('values[day]',$values['day'])));

			if ($prefs['activity_bill'] == 'wu')
			{
				$GLOBALS['phpgw']->template->set_var('lang_sumaes',lang('Sum workunits'));
				$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Workunits'));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_sumaes',lang('Sum hours'));
				$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Hours'));
			}

			$sumaes=0;
			if (is_array($hours))
			{
				while (list($null,$note) = each($hours))
				{
					$this->nextmatchs->template_alternate_row_color(&$GLOBALS['phpgw']->template);

					$select = '<input type="checkbox" name="select[' . $note['hours_id'] . ']" value="True" checked>';

					$activity = $GLOBALS['phpgw']->strip_html($note['act_descr']);
					if (! $activity)  $activity  = '&nbsp;';

					$hours_descr = $GLOBALS['phpgw']->strip_html($note['hours_descr']);
					if (! $hours_descr)  $hours_descr  = '&nbsp;';

					$start_date = $note['sdate'];
					if ($start_date == 0) { $start_dateout = '&nbsp;'; }
					else
					{
						$start_date = $start_date + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
						$start_dateout = $GLOBALS['phpgw']->common->show_date($start_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}

					if ($prefs['activity_bill'] == 'wu')
					{
						if ($note['minperae'] != 0)
						{
							$aes = ceil($note['minutes']/$note['minperae']);
						}
						$sumaes += $aes;
					}
					else
					{
						$aes = floor($note['minutes']/60) . ':'
								. sprintf("%02d",(int)($note['minutes']-floor($note['minutes']/60)*60));

						$sumhours += $note['minutes'];
						$sumaes = floor($sumhours/60) . ':'
								. sprintf("%02d",(int)($sumhours-floor($sumhours/60)*60));
					}
// --------------------- template declaration for list records ---------------------------

					$GLOBALS['phpgw']->template->set_var(array('select' => $select,
										'activity' => $activity,
									'hours_descr' => $hours_descr,
										'status' => lang($note['status']),
									'start_date' => $start_dateout,
											'aes' => $aes));

					if (($note['status'] != 'billed') && ($note['status'] != 'closed'))
					{
						$link_data['menuaction']	= 'projects.uiprojecthours.edit_hours';
						$link_data['hours_id']		= $note['hours_id'];
						$GLOBALS['phpgw']->template->set_var('edithour',$GLOBALS['phpgw']->link('/index.php',$link_data));
						$GLOBALS['phpgw']->template->set_var('lang_edit_entry',lang('Edit'));
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var('edithour','');
						$GLOBALS['phpgw']->template->set_var('lang_edit_entry','&nbsp;');
					}
					$GLOBALS['phpgw']->template->fp('list','hours_list',True);

// -------------------------- end record declaration --------------------------
				}
			}

			if ($delivery_id && ($action != 'amains') && ($action != 'asubs'))
			{
				$hours = $this->bodeliveries->read_hours($project_id, $action);
				if (is_array($hours))
				{
					while (list($null,$note) = each($hours))
					{
						$this->nextmatchs->template_alternate_row_color(&$GLOBALS['phpgw']->template);

						$select = '<input type="checkbox" name="select[' . $note['hours_id'] . ']" value="True">';

						$activity = $GLOBALS['phpgw']->strip_html($note['act_descr']);
						if (! $activity)  $activity  = '&nbsp;';
	
						$hours_descr = $GLOBALS['phpgw']->strip_html($note['hours_descr']);
						if (! $hours_descr)  $hours_descr  = '&nbsp;';

						$start_date = $note['sdate'];
						if ($start_date == 0) { $start_dateout = '&nbsp;'; }
						else
						{
							$start_date = $start_date + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
							$start_dateout = $GLOBALS['phpgw']->common->show_date($start_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
						}

					if ($prefs['activity_bill'] == 'wu')
					{
						if ($note['minperae'] != 0)
						{
							$aes = ceil($note['minutes']/$note['minperae']);
						}
					//	$sumaes += $aes;
					}
					else
					{
						$aes = floor($note['minutes']/60) . ':'
								. sprintf("%02d",(int)($note['minutes']-floor($note['minutes']/60)*60));

					/*	$sumhours += $note['minutes'];
						$sumaes = floor($sumhours/60) . ':'
								. sprintf("%02d",(int)($sumhours-floor($sumhours/60)*60)); */
					}

// --------------------- template declaration for list records ---------------------------

						$GLOBALS['phpgw']->template->set_var(array('select' => $select,
											'activity' => $activity,
										'hours_descr' => $hours_descr,
											'status' => lang($note['status']),
										'start_date' => $start_dateout,
											'	aes' => $aes));

						if (($note['status'] != 'billed') && ($note['status'] != 'closed'))
						{
							$link_data['menuaction']	= 'projects.uiprojecthours.edit_hours';
							$link_data['hours_id']		= $note['hours_id'];
							$GLOBALS['phpgw']->template->set_var('edithour',$GLOBALS['phpgw']->link('/index.php',$link_data));
							$GLOBALS['phpgw']->template->set_var('lang_edit_entry',lang('Edit'));
						}
						else
						{
							$GLOBALS['phpgw']->template->set_var('edithour','');
							$GLOBALS['phpgw']->template->set_var('lang_edit_entry','&nbsp;');
						}
						$GLOBALS['phpgw']->template->fp('list','hours_list',True);

// -------------------------- end record declaration --------------------------
					}
				}
			}

			$GLOBALS['phpgw']->template->set_var(sum_aes,$sumaes);

			if (! $delivery_id)
			{
				$GLOBALS['phpgw']->template->set_var('delivery','<input type="submit" name="Delivery" value="' . lang('Create delivery') . '">');
			} 
			else
			{
				$GLOBALS['phpgw']->template->set_var('delivery','<input type="submit" name="Delivery" value="' . lang('Update delivery') . '">');
			}

			if ($action == 'amains' || $action == 'asubs')
			{
				$GLOBALS['phpgw']->template->set_var('delivery','');
			}

			$GLOBALS['phpgw']->template->pfp('out','hours_list_t',True);
		}

		function list_deliveries()
		{
			$action		= get_var('action',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'bill_listinvoice.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');

			$link_data = array
			(
				'menuaction'	=> 'projects.uideliveries.list_deliveries',
				'action'		=> $action,
				'project_id'	=> $project_id
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list deliveries');

			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));

			if (! $this->start)
			{
				$this->start = 0;
			}

			if (!$project_id)
			{
				$project_id = '';
			}

			$del = $this->bodeliveries->read_deliveries($this->start, $this->query, $this->sort, $this->order, True, $project_id);

// -------------------- nextmatch variable template-declarations -----------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->bodeliveries->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bodeliveries->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->bodeliveries->total_records,$this->start));

// ------------------------ end nextmatch template -------------------------------------------

// ---------------- list header variable template-declarations -------------------------------

			$GLOBALS['phpgw']->template->set_var('sort_num',$this->nextmatchs->show_sort_order($this->sort,'num',$this->order,'/index.php',lang('Delivery ID'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_customer',$this->nextmatchs->show_sort_order($this->sort,'customer',$this->order,'/index.php',lang('Customer'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_date',$this->nextmatchs->show_sort_order($this->sort,'date',$this->order,'/index.php',lang('Date'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_sum','');
			$GLOBALS['phpgw']->template->set_var('lang_data',lang('Delivery'));

// -------------- end header declaration -----------------

			if (is_array($del))
			{
				while (list($null,$note) = each($del))
				{
					$this->nextmatchs->template_alternate_row_color(&$GLOBALS['phpgw']->template);
					$title = $GLOBALS['phpgw']->strip_html($note['title']);
					if (! $title) $title  = '&nbsp;';

					$date = $note['date'];
					if ($date == 0)
						$dateout = '&nbsp;';
					else
					{
						$date = $date + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
						$dateout = $GLOBALS['phpgw']->common->show_date($date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}

					if ($note['customer'] != 0) 
					{
						$customer = $this->boprojects->read_single_contact($note['customer']);
            			if (!$customer[0]['org_name']) { $customerout = $customer[0]['n_given'] . ' ' . $customer[0]['n_family']; }
            			else { $customerout = $customer[0]['org_name'] . ' [ ' . $customer[0]['n_given'] . ' ' . $customer[0]['n_family'] . ' ]'; }
					}
					else { $customerout = '&nbsp;'; }

					$GLOBALS['phpgw']->template->set_var('sum','');

// ------------------ template declaration for list records ----------------------------------

					$GLOBALS['phpgw']->template->set_var(array('num' => $GLOBALS['phpgw']->strip_html($note['delivery_num']),
									'customer' => $customerout,
										'title' => $title,
										'date' => $dateout));

					$link_data['delivery_id']	= $note['delivery_id'];
					$link_data['project_id']	= $note['project_id'];
					$link_data['menuaction']	= 'projects.uideliveries.delivery';
					$GLOBALS['phpgw']->template->set_var('td_data',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$GLOBALS['phpgw']->template->set_var('lang_td_data',lang('Delivery'));

					$GLOBALS['phpgw']->template->fp('list','projects_list',True);

// ------------------------ end record declaration --------------------------------------------
				}
			}
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
			$this->save_sessiondata('del');
		}

		function show_delivery()
		{
			$delivery_id = get_var('delivery_id',array('GET'));

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('del_list_t' => 'del_deliveryform.tpl'));
			$GLOBALS['phpgw']->template->set_block('del_list_t','del_list','list');

			$prefs = $this->boprojects->read_prefs();
			$GLOBALS['phpgw']->template->set_var('myaddress',$this->bodeliveries->get_address_data('line',$prefs['abid'],$prefs['ifont'],$prefs['mysize']));
			$GLOBALS['phpgw']->template->set_var('fulladdress',$this->bodeliveries->get_address_data('full',$prefs['abid'],$prefs['ifont'],$prefs['mysize']));

			$GLOBALS['phpgw']->template->set_var('site_title',$GLOBALS['phpgw_info']['site_title']);
			$charset = $GLOBALS['phpgw']->translation->translate('charset');
			$GLOBALS['phpgw']->template->set_var('charset',$charset);
			$GLOBALS['phpgw']->template->set_var('font',$prefs['ifont']);
			$GLOBALS['phpgw']->template->set_var('fontsize',$prefs['allsize']);
			$GLOBALS['phpgw']->template->set_var('img_src',$GLOBALS['phpgw_info']['server']['webserver_url'] . '/projects/doc/logo.jpg');
			$GLOBALS['phpgw']->template->set_var('lang_delivery_note_for_project',lang('Delivery note for project'));

			$del = $this->bodeliveries->read_single_delivery($delivery_id);

			if ($prefs)
			{
				$GLOBALS['phpgw']->template->set_var('customer',$this->bodeliveries->get_address_data('address',$del['customer'],$prefs['ifont'],$prefs['allsize']));
			}

			$del['date'] = $del['date'] + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
			$delivery_dateout = $GLOBALS['phpgw']->common->show_date($del['date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$GLOBALS['phpgw']->template->set_var('delivery_date',$delivery_dateout);

			$GLOBALS['phpgw']->template->set_var('delivery_num',$GLOBALS['phpgw']->strip_html($del['delivery_num']));
			$GLOBALS['phpgw']->template->set_var('project_num',$GLOBALS['phpgw']->strip_html($del['project_num']));
			$title = $GLOBALS['phpgw']->strip_html($del['title']);
			if (! $title) { $title  = '&nbsp;'; }
			$GLOBALS['phpgw']->template->set_var('title',$title);

			if ($prefs['activity_bill'] == 'wu')
			{
				$GLOBALS['phpgw']->template->set_var('lang_sumaes',lang('Sum workunits'));
				$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Workunits'));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_sumaes',lang('Sum hours'));
				$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Hours'));
			}

			$pos = 0;
			$hours = $this->bodeliveries->read_delivery_pos($delivery_id);

			if (is_array($hours))
			{
				while (list($null,$note) = each($hours))
				{
					$pos++;
					$GLOBALS['phpgw']->template->set_var('pos',$pos);

					if ($note['sdate'] == 0)
					{
						$hours_dateout = '&nbsp;';
					}
					else
					{
						$note['sdate'] = $note['sdate'] + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
						$hours_dateout = $GLOBALS['phpgw']->common->show_date($note['sdate'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}

					$GLOBALS['phpgw']->template->set_var('hours_date',$hours_dateout);

					if ($prefs['activity_bill'] == 'wu')
					{
						if ($note['minperae'] != 0)
						{
							$aes = ceil($note['minutes']/$note['minperae']);
						}
						$sumaes += $aes;
					}
					else
					{
						$aes = floor($note['minutes']/60) . ':'
								. sprintf("%02d",(int)($note['minutes']-floor($note['minutes']/60)*60));

						$sumhours += $note['minutes'];
						$sumaes = floor($sumhours/60) . ':'
								. sprintf("%02d",(int)($sumhours-floor($sumhours/60)*60));
					}

					$GLOBALS['phpgw']->template->set_var('aes',$aes);
					$act_descr = $GLOBALS['phpgw']->strip_html($note['act_descr']);
					if (! $act_descr) { $act_descr  = '&nbsp;'; }
					$GLOBALS['phpgw']->template->set_var('act_descr',$act_descr);
					$GLOBALS['phpgw']->template->set_var('billperae',$note['billperae']);
					$hours_descr = $GLOBALS['phpgw']->strip_html($note['hours_descr']);
					if (! $hours_descr) { $hours_descr  = '&nbsp;'; }
					$GLOBALS['phpgw']->template->set_var('hours_descr',$hours_descr);
					$GLOBALS['phpgw']->template->fp('list','del_list',True);
				}
			}
			$GLOBALS['phpgw']->template->set_var('sumaes',$sumaes);

			$GLOBALS['phpgw']->template->pfp('out','del_list_t',True);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function fail()
		{
			echo '<p><center>' . lang('You have to CREATE a delivery or invoice first !');
			echo '</center>';
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
	}
?>
