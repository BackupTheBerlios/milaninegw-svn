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
	/* $Id: class.soconfig.inc.php,v 1.7.2.2 2004/11/06 12:15:28 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.soconfig.inc.php,v $

	class soconfig
	{
		var $db;
		var $db2;
		var $currency;

		function soconfig()
		{
			$this->db		= $GLOBALS['phpgw']->db;
			$this->db2		= clone($this->db);
			$this->currency = $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function get_site_config()
		{
			$this->config = CreateObject('phpgwapi.config','projects');
			$this->config->read_repository();

			if ($this->config->config_data)
			{
				$items = $this->config->config_data;
			}
			return $items;
		}

		function bill_lang()
		{
			$config = $this->get_site_config();

			switch ($config['activity_bill'])
			{
				case 'wu':	$l = lang('per workunit'); break;
				default:	$l = lang('per hour'); break;
			}
			return $l;
		}

		function activities_list($project_id = '',$billable = False)
		{
			if ($billable)
			{
				$bill_filter = " AND billable='Y'";
			}
			else
			{
				$bill_filter = " AND billable='N'";
			}

			$this->db->query('SELECT phpgw_p_activities.id,a_number,descr,billperae,activity_id from phpgw_p_activities,phpgw_p_projectactivities '
							. 'WHERE phpgw_p_projectactivities.project_id=' . $project_id . ' AND phpgw_p_activities.id='
							. 'phpgw_p_projectactivities.activity_id' . $bill_filter,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$act[] = array
				(
					'num'		=> $this->db->f('a_number'),
					'descr'		=> $this->db->f('descr'),
					'billperae'	=> $this->db->f('billperae')
				);
			}
			return $act;
		}

		function select_activities_list($project_id = '',$billable = False)
		{
			if ($billable)
			{
				$bill_filter = " AND billable='Y'";
			}
			else
			{
				$bill_filter = " AND billable='N'";
			}

			$this->db2->query('SELECT activity_id from phpgw_p_projectactivities WHERE project_id=' . intval($project_id) . $bill_filter,__LINE__,__FILE__);
			while ($this->db2->next_record())
			{
				$selected[] = array('activity_id' => $this->db2->f('activity_id'));
			}

			$this->db2->query('SELECT id,a_number,descr,billperae FROM phpgw_p_activities ORDER BY descr asc');
			while ($this->db2->next_record())
			{
				$activities_list .= '<option value="' . $this->db2->f('id') . '"';
				for ($i=0;$i<count($selected);$i++)
				{
					if($selected[$i]['activity_id'] == $this->db2->f('id'))
					{
						$activities_list .= ' selected';
					}
				}
				$activities_list .= '>' . $GLOBALS['phpgw']->strip_html($this->db2->f('descr')) . ' ['
										. $GLOBALS['phpgw']->strip_html($this->db2->f('a_number')) . ']';
				if($billable)
				{
					$activities_list .= ' ' . $this->currency . ' ' . $this->db2->f('billperae') . ' ' . $this->bill_lang();
				}

				$activities_list .= '</option>' . "\n";
			}
			return $activities_list;
		}

		function select_pro_activities($project_id = '', $pro_parent, $billable = False)
		{
			if ($billable)
			{
				$bill_filter = " AND billable='Y'";
			}
			else
			{
				$bill_filter = " AND billable='N'";
			}

			$this->db2->query('SELECT activity_id from phpgw_p_projectactivities WHERE project_id=' . intval($project_id) . $bill_filter,__LINE__,__FILE__);
			while ($this->db2->next_record())
			{
				$selected[] = array('activity_id' => $this->db2->f('activity_id'));
			}

			$this->db2->query('SELECT a.id, a.a_number, a.descr, a.billperae, pa.activity_id FROM phpgw_p_activities as a, phpgw_p_projectactivities as pa'
							. ' WHERE pa.project_id=' . intval($pro_parent) . $bill_filter . ' AND pa.activity_id=a.id ORDER BY a.descr asc');
			while ($this->db2->next_record())
			{
				$activities_list .= '<option value="' . $this->db2->f('id') . '"';
				for ($i=0;$i<count($selected);$i++)
				{
					if($selected[$i]['activity_id'] == $this->db2->f('id'))
					{
						$activities_list .= ' selected';
					}
				}

				if (! is_array($selected))
				{
					$activities_list .= ' selected';
				}

				$activities_list .= '>' . $GLOBALS['phpgw']->strip_html($this->db2->f('descr')) . ' ['
										. $GLOBALS['phpgw']->strip_html($this->db2->f('a_number')) . ']';

				if($billable)
				{
					$activities_list .= ' ' . $this->currency . ' ' . $this->db2->f('billperae') . ' ' . $this->bill_lang();
				}

				$activities_list .= '</option>' . "\n";
			}
			return $activities_list;
		}

		function select_hours_activities($project_id, $activity = '',$billable = '')
		{
			$this->db2->query('SELECT activity_id,a_number,descr,billperae,billable FROM phpgw_p_projectactivities,phpgw_p_activities WHERE project_id ='
							. intval($project_id) . ' AND phpgw_p_projectactivities.activity_id=phpgw_p_activities.id order by descr asc',__LINE__,__FILE__);

			while ($this->db2->next_record())
			{
				$hours_act .= '<option value="' . $this->db2->f('activity_id') . $this->db2->f('billable') . '"';
				if(!$billable && $this->db2->f('activity_id') == intval($activity) || 
					$this->db2->f('activity_id').$this->db2->f('billable') == $activity.$billable)
				{
					$hours_act .= ' selected';
				}
				$hours_act .= '>' . $GLOBALS['phpgw']->strip_html($this->db2->f('descr')) . ' ['
									. $GLOBALS['phpgw']->strip_html($this->db2->f('a_number')) . ']';

				if($this->db2->f('billable') == 'Y')
				{
					$hours_act .= ' ' . $this->currency . ' ' . $this->db2->f('billperae') . ' ' . $this->bill_lang();
				}
				$hours_act .= '</option>' . "\n";
			}
			return $hours_act;
		}

		function select_hours_costs($_projectID, $_costID)
		{
			$this->db->query('SELECT cost_id,cost_name FROM phpgw_p_costs order by cost_name asc',__LINE__,__FILE__);

			$hours_act .= '<option value="0"></option>';
			while ($this->db->next_record())
			{
				$hours_act .= '<option value="' . $this->db->f('cost_id') . '"';
				if($this->db->f('cost_id') == intval($_costID))
				{
					$hours_act .= ' selected';
				}
				$hours_act .= '>' . $GLOBALS['phpgw']->strip_html($this->db->f('cost_name'));

				$hours_act .= '</option>' . "\n";
			}
			return $hours_act;
		}

		function return_value($action,$pro_id)
		{
			$pro_id = intval($pro_id);
			switch($action)
			{
				case 'act':
					$sql = 'SELECT a_number,descr from phpgw_p_activities where id=' . $pro_id;

					break;
				case 'acc':
					$sql = "SELECT accounting from phpgw_p_projectmembers where account_id=$pro_id AND type='accounting'";
					break;
			}
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				switch($action)
				{
					case 'act':
						$bla = $GLOBALS['phpgw']->strip_html($this->db->f('descr')) . ' [' . $GLOBALS['phpgw']->strip_html($this->db->f('a_number')) . ']';
						break;
					case 'acc':
						$bla = $this->db->f('accounting');
						break;
				}
				return $bla;
			}
			return False;
		}

		function exists($values)
		{
			$pa_id	= isset($values['pa_id'])?$values['pa_id']:0;
			$number	= isset($values['number'])?$values['number']:'';
			$action = isset($values['action'])?$values['action']:'activity';
			$check	= isset($values['check'])?$values['check']:'';

			$pa_id = intval($pa_id);

			switch ($action)
			{
				case 'activity'		: $p_table = 'phpgw_p_activities'; $column = "a_number='" . $number . "'";break;
				case 'accounting'	: $p_table = 'phpgw_p_projectmembers'; $column = 'account_id=' . $pa_id; break;
			}

			if ($check == 'number' && $pa_id > 0)
			{
				$additon = ' and id !=' . $pa_id;
			}
			elseif($action == 'accounting')
			{
				$additon = " and type='accounting'"; 
			}

			$this->db->query("select count(*) from $p_table where $column" . $additon,__LINE__,__FILE__);

			$this->db->next_record();

			if ($this->db->f(0))
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function read_admins($action = 'pad',$type = '')
		{
			switch($type)
			{
				case 'user':
					switch($action)
					{
						case 'pmanager':	$filter = "type='ma'"; break;
						case 'psale':		$filter = "type='sa'"; break;
						case 'pad':			$filter = "type='aa'"; break;
					}
					break;
				case 'group':
					switch($action)
					{
						case 'pmanager':	$filter = "type='mg'"; break;
						case 'psale':		$filter = "type='sg'"; break;
						case 'pad':			$filter = "type='ag'"; break;
					}
					break;
				case 'all': $filter = "(type != 'accounting' AND type != 'role')"; break;
				default:
					switch($action)
					{
						case 'pmanager':	$filter = "type='ma' or type='mg'"; break;
						case 'psale':		$filter = "type='sa' or type='sg'"; break;
						case 'pad':			$filter = "type='aa' or type='ag'"; break;
					}
					break;
			}

			$sql = 'select account_id,type from phpgw_p_projectmembers WHERE ' . $filter;
			$this->db->query($sql);
			$this->total_records = $this->db->num_rows();
			while ($this->db->next_record())
			{
				$admins[] = array('account_id' => $this->db->f('account_id'),
										'type' => $this->db->f('type'));
			}
			return $admins;
		}

		function isprojectadmin($action = 'pad')
		{
			$admin_groups = $GLOBALS['phpgw']->accounts->membership($this->account);
			$admins = $this->read_admins($action);

			#_debug_array($admins);

			for ($i=0;$i<count($admins);$i++)
			{
				switch($action)
				{
					case 'pmanager':
						$type_a = 'ma';
						$type_g = 'mg';
						break;
					case 'psale':
						$type_a = 'sa';
						$type_g = 'sg';
						break;
					default:
						$type_a = 'aa';
						$type_g = 'ag';
						break;
				}
				if ($admins[$i]['type'] == $type_a && $admins[$i]['account_id'] == $this->account)
				{
					return True;
				}
				elseif ($admins[$i]['type'] == $type_g)
				{
					if (is_array($admin_groups))
					{
						for ($j=0;$j<count($admin_groups);$j++)
						{
							if ($admin_groups[$j]['account_id'] == $admins[$i]['account_id'])
							return True;
						}
					}
				}
				#else
				#{
				#	return False;
				#}
			}
			return False;
		}

		function edit_admins($action,$users = '', $groups = '')
		{
			switch($action)
			{
				case 'psale':		$filter = "sa' OR type='sg"; break;
				case 'pmanager':	$filter = "ma' OR type='mg"; break;
				default:			$filter = "aa' OR type='ag"; break;
			}

			$this->db->query("DELETE from phpgw_p_projectmembers WHERE type='" . $filter . "'",__LINE__,__FILE__);

			if (is_array($users))
			{
				switch($action)
				{
					case 'psale':		$type = 'sa'; break;
					case 'pmanager':	$type = 'ma'; break;
					default:			$type = 'aa'; break;
				}

				while($activ=each($users))
				{
					$this->db->query('insert into phpgw_p_projectmembers (project_id, account_id,type) values (0,' . $activ[1] . ",'"
									. $type . "')",__LINE__,__FILE__);
				}
			}

			if (is_array($groups))
			{
				switch($action)
				{
					case 'psale':		$type = 'sg'; break;
					case 'pmanager':	$type = 'mg'; break;
					default:			$type = 'ag'; break;
				}

				while($activ=each($groups))
				{
					$this->db->query('insert into phpgw_p_projectmembers (project_id, account_id,type) values (0,' . $activ[1] . ",'"
									. $type . "')",__LINE__,__FILE__);
				}
			}
		}

		function read_activities($values)
		{
			$start	= isset($values['start'])?$values['start']:0;
			$limit	= isset($values['limit'])?$values['limit']:True;
			$sort	= isset($values['sort'])?$values['sort']:'ASC';
			$order	= isset($values['order'])?$values['order']:'a_number';
			$cat_id	= isset($values['cat_id'])?$values['cat_id']:0;

			$query	= $this->db->db_addslashes($values['query']);

			$ordermethod = " order by $order $sort";

			if ($query)
			{
				$filtermethod = " where (descr like '%$query%' or a_number like '%$query%' or minperae like '%$query%' or billperae like '%$query%')";

				if ($cat_id > 0)
				{
					$filtermethod .= ' AND category=' . $cat_id;
				}
			}
			else
			{
				if ($cat_id > 0)
				{
					$filtermethod = ' WHERE category=' . $cat_id;
				}
			}

			$sql = 'select * from phpgw_p_activities' . $filtermethod;
			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if ($limit)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$i = 0;
			while ($this->db->next_record())
			{
				$act[$i]['activity_id']	= $this->db->f('id');
				$act[$i]['cat']			= $this->db->f('category');
				$act[$i]['number']		= $this->db->f('a_number');
				$act[$i]['descr']		= $this->db->f('descr');
				$act[$i]['remarkreq']	= $this->db->f('remarkreq');
				$act[$i]['billperae']	= $this->db->f('billperae');
				$act[$i]['minperae']	= $this->db->f('minperae');
				$i++;
			}
			return $act;
		}

		function read_single_activity($activity_id)
		{
			$this->db->query('SELECT * from phpgw_p_activities WHERE id=' . intval($activity_id),__LINE__,__FILE__);
	
			if ($this->db->next_record())
			{
				$act['activity_id']	= $this->db->f('id');
				$act['cat']			= $this->db->f('category');
				$act['number']		= $this->db->f('a_number');
				$act['descr']		= $this->db->f('descr');
				$act['remarkreq']	= $this->db->f('remarkreq');
				$act['billperae']	= $this->db->f('billperae');
				$act['minperae']	= $this->db->f('minperae');
				return $act;
			}
		}

		function add_activity($values)
		{
			$values['number']		= $this->db->db_addslashes($values['number']);
			$values['descr'] 		= $this->db->db_addslashes($values['descr']);
			$values['billperae']	= $values['billperae'] + 0.0;

			$this->db->query("insert into phpgw_p_activities (a_number,category,descr,remarkreq,billperae,minperae) values ('"
							. $values['number'] . "'," . intval($values['cat']) . ",'" . $values['descr'] . "','" . $values['remarkreq'] . "',"
							. $values['billperae'] . ','  . intval($values['minperae']) . ')',__LINE__,__FILE__);
		}

		function edit_activity($values)
		{
			$values['number']		= $this->db->db_addslashes($values['number']);
			$values['descr']		= $this->db->db_addslashes($values['descr']);
			$values['billperae']	= $values['billperae'] + 0.0;

			$this->db->query("update phpgw_p_activities set a_number='" . $values['number'] . "', category=" . intval($values['cat'])
							. ",remarkreq='" . $values['remarkreq'] . "',descr='" . $values['descr'] . "',billperae="
							. $values['billperae'] . ',minperae=' . intval($values['minperae']) . ' where id=' . intval($values['activity_id']),__LINE__,__FILE__);
		}

		function delete_pa($action, $pa_id)
		{
			$pa_id = intval($pa_id);

			switch ($action)
			{
				case 'act':
				case 'activity':	$p_table = 'phpgw_p_activities'; $p_column = 'id'; break;
				case 'role':		$p_table = 'phpgw_p_roles'; $p_column = 'role_id'; break;
				case 'cost':		$p_table = 'phpgw_p_costs'; $p_column = 'cost_id'; break;
				case 'emp_role':
				case 'accounting':	$p_table = 'phpgw_p_projectmembers'; $p_column = 'id'; break;
			}

			$this->db->query("DELETE from $p_table where $p_column=" . $pa_id,__LINE__,__FILE__);

			if ($action == 'activity')
			{
				$this->db->query('DELETE from phpgw_p_projectactivities where activity_id=' . $pa_id,__LINE__,__FILE__);
			}
		}

		function read_employees($values)
		{
			$start		= intval($values['start']);
			$limit		= (isset($values['limit'])?$values['limit']:True);
			$sort		= (isset($values['sort'])?$values['sort']:'ASC');
			$order		= (isset($values['order'])?$values['order']:'account_id');
			$query		= $this->db->db_addslashes($values['query']);
			$account_id	= intval($values['account_id']);

			$ordermethod = " order by $order $sort";

			if($account_id > 0)
			{
				$acc_select = ' and account_id=' . $account_id;
			}

			$sql = "SELECT * from phpgw_p_projectmembers WHERE type='accounting'";

			if($limit)
			{
				$this->db2->query($sql,__LINE__,__FILE__);
				$this->total_records = $this->db2->num_rows();
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $acc_select . $ordermethod,__LINE__,__FILE__);
			}

			while($this->db->next_record())
			{
				$emps[] = array
				(
					'id'			=> $this->db->f('id'),
					'account_id'	=> $this->db->f('account_id'),
					'accounting'	=> $this->db->f('accounting'),
					'd_accounting'	=> $this->db->f('d_accounting')
				);
			}
			return $emps;
		}

		function save_accounting_factor($values)
		{
			$exists = $this->exists(array('action' => 'accounting','pa_id' => $values['account_id']));

			$values['accounting']	= $values['accounting'] + 0.0;
			$values['d_accounting']	= $values['d_accounting'] + 0.0;

			if($exists)
			{
				$this->db->query('UPDATE phpgw_p_projectmembers set accounting=' . $values['accounting'] . ', d_accounting=' . $values['d_accounting']
								. ' where account_id=' . intval($values['account_id']) . " and type='accounting'",__LINE__,__FILE__);
			}
			else
			{
				$this->db->query('INSERT into phpgw_p_projectmembers (account_id,type,accounting,d_accounting) values(' . intval($values['account_id'])
								. ",'accounting'," . $values['accounting'] . ',' . $values['d_accounting'] . ')',__LINE__,__FILE__);
			}
		}

		function list_roles($values)
		{
			$start		= intval($values['start']);
			$limit		= (isset($values['limit'])?$values['limit']:True);
			$sort		= (isset($values['sort'])?$values['sort']:'ASC');
			$roleType	= (isset($values['roleType'])?$values['roleType']:'role');
			$order		= (isset($values['order'])?$values['order']:$roleType.'_name');
			$query	= $this->db->db_addslashes($values['query']);

			$ordermethod = " order by $order $sort";

			if ($query)
			{
				$querymethod = " WHERE ($roleType_name like '%$query%') ";
			}
			
			switch($roleType)
			{
				case 'cost':
					$sql = 'SELECT * from phpgw_p_costs' . $querymethod;
					break;
				default:
					$sql = 'SELECT * from phpgw_p_roles' . $querymethod;
					break;
			}

			if ($limit)
			{
				$this->db2->query($sql,__LINE__,__FILE__);
				$this->total_records = $this->db2->num_rows();
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$roles[] = array
				(
					$roleType.'_id'		=> $this->db->f($roleType.'_id'),
					$roleType.'_name'	=> $this->db->f($roleType.'_name')
				);
			}
			return $roles;
		}

		function save_role($role_name, $roleType)
		{
			$role_name = $this->db->db_addslashes($role_name);
			switch($roleType)
			{
				case 'cost':
					$this->db->query("INSERT into phpgw_p_costs (cost_name) values ('" . $role_name . "')",__LINE__,__FILE__);
					break;
				case 'role':
					$this->db->query("INSERT into phpgw_p_roles (role_name) values ('" . $role_name . "')",__LINE__,__FILE__);
					break;
			}
		}

		function list_events($type = '')
		{
			if($type)
			{
				$type_select = " where event_type='$type'";
			}

			$this->db->query('SELECT * from phpgw_p_events ' . $type_select . 'order by event_type asc',__LINE__,__FILE__);

			while($this->db->next_record())
			{
				$events[] = array
				(
					'event_id'		=> $this->db->f('event_id'),
					'event_name'	=> $this->db->f('event_name'),
					'event_type'	=> $this->db->f('event_type'),
					'event_extra'	=> $this->db->f('event_extra')
				);
			}
			return $events;
		}

		function save_event($values)
		{
			$this->db->query('UPDATE phpgw_p_events set event_extra=' . intval($values['limit']) . ' where event_id=' . intval($values['event_id_limit']),__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_events set event_extra=' . intval($values['percent']) . ' where event_id=' . intval($values['event_id_percent']),__LINE__,__FILE__);
		}

		function get_event_extra($event_name)
		{
			$this->db->query('SELECT event_extra from phpgw_p_events where event_name=' . "'" . $event_name . "'",__LINE__,__FILE__);			
			$this->db->next_record();
			return $this->db->f(0);
		}
	}
?>
