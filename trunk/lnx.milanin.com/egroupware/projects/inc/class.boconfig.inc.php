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
	/* $Id: class.boconfig.inc.php,v 1.7.2.2 2004/11/06 12:15:27 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.boconfig.inc.php,v $

	class boconfig
	{
		var $action;
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $cat_id;

		var $public_functions = array
		(
			'save_prefs'				=> True,
			'selected_employees'		=> True,
			'read_accounting_factors'	=> True,
			'save_accounting_factor'	=> True,
			'read_admins'				=> True,
			'list_admins'				=> True,
			'selected_admins'			=> True,
			'edit_admins'				=> True,
			'read_single_activity'		=> True,
			'exists'					=> True,
			'check_pa_values'			=> True,
			'list_activities'			=> True,
			'save_activity'				=> True,
			'delete_pa'					=> True,
			'list_roles'				=> True,
			'save_role'					=> True,
			'save_event'				=> True
		);

		function boconfig()
		{
			$action			= get_var('action',array('GET'));
			$this->debug		= False;
			$this->boprojects	= CreateObject('projects.boprojects',True,$action);
			$this->soconfig		= $this->boprojects->soconfig;
			$this->start		= $this->boprojects->start;
			$this->query		= $this->boprojects->query;
			$this->filter		= $this->boprojects->filter;
			$this->order		= $this->boprojects->order;
			$this->sort		= $this->boprojects->sort;
			$this->cat_id		= $this->boprojects->cat_id;
		}

		function save_prefs($prefs)
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			if (is_array($prefs))
			{
				$GLOBALS['phpgw']->preferences->change('projects','columns',implode(',',$prefs));
				$GLOBALS['phpgw']->preferences->save_repository(True);
			}
		}

		function selected_employees()
		{
			$emps = $this->boprojects->read_projects_acl();
			if (is_array($emps))
			{
				for($i=0;$i<count($emps);$i++)
				{
					$this->accounts = CreateObject('phpgwapi.accounts',$emps[$i]);
					$this->accounts->read_repository();

					$empl[] = array
					(
						'account_id'		=> $this->accounts->data['account_id'],
						'account_lid'		=> $this->accounts->data['account_lid'],
						'account_firstname'	=> $this->accounts->data['firstname'],
						'account_lastname'	=> $this->accounts->data['lastname']
					);
				}
			}
			asort($empl);
			reset($empl);
			return $empl;
		}

		function read_accounting_factors($data = 0)
		{
			$factors = $this->soconfig->read_employees(array('start' => $this->start,'sort' => $this->sort,'order' => $this->order,
																		'query' => $this->query,'limit' => isset($data['limit'])?$data['limit']:$this->limit,
																		'account_id' => intval($data['account_id'])));
			$this->total_records = $this->soconfig->total_records;
			if(is_array($factors))
			{
				foreach($factors as $emp)
				{
					$emps[] = array
					(
						'id'			=> $emp['id'],
						'account_id'	=> $emp['account_id'],
						'account_name'	=> $GLOBALS['phpgw']->common->grab_owner_name($emp['account_id']),
						'accounting'	=> $emp['accounting'],
						'd_accounting'	=> $emp['d_accounting']
					);
				}
				asort($emps);
				reset($emps);
				return $emps;
			}
			return False;
		}

		function save_accounting_factor($values)
		{
			$h = $this->boprojects->siteconfig['hwday'];

			if(intval($values['accounting']) > 0 && intval($values['d_accounting']) == 0)
			{
				$values['d_accounting'] = round($values['accounting']* $h,2);
			}
			else if(intval($values['accounting']) == 0 && intval($values['d_accounting']) > 0)
			{
				$values['accounting'] = round($values['d_accounting']/$h,2);
			}
			$this->boprojects->soconfig->save_accounting_factor($values);
		}

		function read_admins($action,$type)
		{
			$admins = $this->boprojects->soconfig->read_admins($action,$type);
			$this->total_records = $this->boprojects->soconfig->total_records;
			return $admins;
		}

		function list_admins($action)
		{
			$admins = $this->boprojects->soconfig->read_admins($action,$type='');

			//_debug_array($admins);

			$this->total_records = $this->boprojects->soconfig->total_records;

			if(is_array($admins))
			{
				foreach($admins as $ad)
				{
					$accounts = CreateObject('phpgwapi.accounts',$ad['account_id']);
					$accounts->read_repository();
					$admin_data[] = array
					(
						'account_id'	=> $ad['account_id'],
						'lid'			=> $accounts->data['account_lid'],
						'firstname'		=> $accounts->data['firstname'],
						'lastname'		=> $accounts->data['lastname'],
						'type'			=> $accounts->get_type($ad['account_id'])
					);
					unset($accounts);
				}
			}
			return $admin_data;
		}

		function selected_admins($action,$type = 'user')
		{
			$is_admin = $this->read_admins($action,$type);
			$selected = array();
			$i = 0;
			if(is_array($is_admin))
			{
				foreach($is_admin as $ad)
				{
					$selected[$i] = $ad['account_id'];
					++$i;
				}
			}

			$aclusers = $this->boprojects->read_projects_acl(False);

			$alladmins = $type=='user'?$aclusers['users']:$aclusers['groups'];

			if (is_array($alladmins))
			{
				for($i=0;$i<count($alladmins);++$i)
				{
					$selected_admins .= '<option value="' . $alladmins[$i] . '"';
					if(in_array($alladmins[$i],$selected))
					{
						$selected_admins .= ' selected';
					}
					$selected_admins .= '>' . $GLOBALS['phpgw']->common->grab_owner_name($alladmins[$i]) . '</option>' . "\n";
				}
			}
			return $selected_admins;
		}

		function edit_admins($action,$users,$groups)
		{
			$this->boprojects->soconfig->edit_admins($action,$users,$groups);
		}

		function read_single_activity($activity_id)
		{
			$single_act = $this->boprojects->soconfig->read_single_activity($activity_id);
			return $single_act;
		}

		function exists($values)
		{
			return $this->boprojects->soconfig->exists($values);
		}

		function check_pa_values($values, $action = 'activity')
		{
			switch($action)
			{
				case 'role':
				case 'cost':
					if (strlen($values[$action.'_name']) > 250)
					{
						$error[] = lang('name not exceed 250 characters in length');
					}

					if (!$values[$action.'_name'])
					{
						$error[] = lang('Please enter a name');
					}
					break;
				default:
					if (strlen($values['descr']) > 250)
					{
						$error[] = lang('Description can not exceed 250 characters in length');
					}

					if (! $values['choose'])
					{
						if (! $values['number'])
						{
							$error[] = lang('Please enter an ID');
						}
						else
						{
							// check for 
							$exists = $this->exists(array('check' => 'number', 'number' => $values['number'],'pa_id' => $values['activity_id']));

							if ($exists)
							{
								$error[] = lang('That ID has been used already');
							}
							if (strlen($values['number']) > 20)
							{
								$error[] = lang('id can not exceed 20 characters in length');
							}
						}
					}

					if ((! $values['billperae']) || ($values['billperae'] == 0))
					{
						$error[] = lang('please enter the bill');
					}

					$config = $this->soconfig->get_site_config();

					if ($config['activity_bill'] == 'wu')
					{
						if ((! $values['minperae']) || ($values['minperae'] == 0))
						{
							$error[] = lang('please enter the minutes per workunit');
						}
					}
					break;
			}

			if (is_array($error))
			{
				return $error;
			}
			return False;
		}

		function list_activities()
		{
			$act_list = $this->boprojects->soconfig->read_activities(array('start' => $this->start,'limit' => $this->limit,'query' => $this->query,
																'sort' => $this->sort,'order' => $this->order,'cat_id' => $this->cat_id));
			$this->total_records = $this->boprojects->soconfig->total_records;
			return $act_list;
		}

		function save_activity($values)
		{
			if ($values['choose'])
			{
				$values['number'] = $this->boprojects->create_activityid();
			}

			if ($values['activity_id'])
			{
				if ($values['activity_id'] && intval($values['activity_id']) > 0)
				{
					$this->boprojects->soconfig->edit_activity($values);

					if ($values['minperae'])
					{
						// $this->boprojects->sohours->update_hours_act($values['activity_id'],$values['minperae']); This function doesn't exist anymore, seems the script does the cost calculation at runtime. Am I right, maintainer of this app?
					}
				}
			}
			else
			{
				$this->boprojects->soconfig->add_activity($values);
			}
		}

		function delete_pa($action, $pa_id)
		{
			$this->boprojects->soconfig->delete_pa($action, $pa_id);
		}

		function list_roles($_roleType)
		{
			$roles = $this->boprojects->list_roles($_roleType);
			$this->total_records = $this->boprojects->total_records;
			return $roles;
		}

		function save_role($role_name, $_roleType)
		{
			$this->boprojects->soconfig->save_role($role_name, $_roleType);
		}

		function save_event($values)
		{
			$this->soconfig->save_event($values);
		}
	}
?>
