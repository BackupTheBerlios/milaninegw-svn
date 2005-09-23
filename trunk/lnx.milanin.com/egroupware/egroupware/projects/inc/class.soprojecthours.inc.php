<?php
	/*******************************************************************\
	* eGroupWare - Projects                                             *
	* http://www.egroupware.org                                         *
	*                                                                   *
	* Project Manager                                                   *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* Written by Lars Kneschke [lkneschke@linux-at-work.de]             *
	* DB-Layer reworked by RalfBecker-AT-outdoor-training.de            *
	* -----------------------------------------------                   *
	* Copyright 2000 - 2004 Free Software Foundation, Inc               *
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
	/* $Id: class.soprojecthours.inc.php,v 1.19.2.1 2004/11/06 12:15:28 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.soprojecthours.inc.php,v $

	class soprojecthours
	{
		var $db;
		var $grants;
		var $ttracker_table = 'phpgw_p_ttracker';
		var $hours_table = 'phpgw_p_hours';
		var $activities_table = 'phpgw_p_activities';
		var $projectactivities_table = 'phpgw_p_projectactivities';
		var $projects_table = 'phpgw_p_projects';

		function soprojecthours()
		{
			$this->db		= $GLOBALS['phpgw']->db;
			$this->db->set_app('projects');
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->column_array = array();
		}

		function db2hours($column = False)
		{
			$i = 0;
			while ($this->db->next_record())
			{
				if($column)
				{
					$hours[$i] = array();
					foreach($this->column_array as $col)
					{
						$hours[$i][$col] = $this->db->f($col);
					}
					$i++;
				}
				else
				{
					$hours[] = array(
						'hours_id'		=> $this->db->f('id'),
						'project_id'	=> $this->db->f('project_id'),
						'cost_id'		=> $this->db->f('cost_id'),
						'pro_parent'	=> $this->db->f('pro_parent'),
						'pro_main'		=> $this->db->f('pro_main'),
						'hours_descr'	=> $this->db->f('hours_descr'),
						'status'		=> $this->db->f('status'),
						'minutes'		=> $this->db->f('minutes'),
						'sdate'			=> $this->db->f('start_date'),
						'edate'			=> $this->db->f('end_date'),
						'employee'		=> $this->db->f('employee'),
						'activity_id'	=> $this->db->f('activity_id'),
						'remark'		=> $this->db->f('remark'),
						'billable'		=> $this->db->f('billable'),
						'km_distance'	=> $this->db->f('km_distance'),
						't_journey'		=> $this->db->f('t_journey')
					);
				}
			}
			return $hours;
		}

		function read_hours($values)
		{
			$start			= intval($values['start']);
			$limit			= $values['limit'] ? $values['limit'] : True;
			$filter			= $values['filter']?$values['filter'] : 'none';
			$sort			= preg_match('/^(ASC|DESC){1}$/i',$values['sort']) ? $values['sort'] : 'ASC';
			$order			= preg_match('/^[a-zA-Z0-9_]*$/',$values['order']) ? $values['order'] : 'start_date';
			$status			= $values['status'] ? $values['status'] : 'all';
			$project_id		= intval($values['project_id']);
			$query			= $values['query'];
			$column			= isset($values['column']) ? $values['column'] : False;
			$parent_select		= isset($values['parent_select']);

			//_debug_array($values);

			$ordermethod = $order?"ORDER BY $order $sort":'';

			if ($parent_select)
			{
				$where = array('pro_parent'=>$project_id);
			}
			else
			{
				$where = array('project_id'=>$project_id);
			}

			if ($status != 'all')
			{
				$where['status'] = $status;
			}

			if ($filter == 'yours')
			{
				$where['employee'] = $this->account;
			}

			if ($query)
			{
				$query = $this->db->quote("%$query%");
				$columns_to_search = array('remark','minutes','hour_descr');

				switch($this->db->Type)
				{
					case 'sapdb':
					case 'maxdb':
						$columns_to_search = array('minutes','hour_descr');	// no remark as it's text/LONG and cant be searched
						break;
				}
				$where[] = '('.implode(" LIKE $query OR ",$columns_to_search)." LIKE $query)";
			}

			$column_select = is_string($column) && $column != '' ? $column : '*';
			$this->column_array = explode(',',$column);

			if($limit)
			{
				$this->db->select($this->hours_table,'count(*)',$where,__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f(0);
				$this->db->select($this->hours_table,$column_select,$where,__LINE__,__FILE__,$start,$ordermethod);
			}
			else
			{
				$this->db->select($this->hours_table,$column_select,$where,__LINE__,__FILE__,false,$ordermethod);
			}
			return $this->db2hours();
		}

		function read_single_hours($hours_id)
		{
			$this->db->select($this->hours_table,'*',array('id'=>$hours_id),__LINE__,__FILE__);
			list($hours) = $this->db2hours();

			return $hours;
		}

		function add_hours($values)
		{
			$this->db->insert($this->hours_table,array(
					'project_id'	=> $values['project_id'],
					'activity_id'	=> $values['activity_id'],
					'cost_id'		=> $values['cost_id'],
					'entry_date'	=> time(),
					'start_date'	=> $values['sdate'],
					'end_date'		=> $values['edate'],
					'hours_descr'	=> $values['hours_descr'],
					'remark'		=> $values['remark'],
					'billable'		=> $values['billable'],
					'minutes'		=> $values['w_minutes'],
					'status'		=> $values['status'],
					'employee'		=> $values['employee'],
					'pro_parent'	=> $values['pro_parent'],
					'pro_main'		=> $values['pro_main'],
					'km_distance'	=> 0.0 + $values['km_distance'],
					't_journey'		=> 0.0 + $values['t_journey'],
				),false,__LINE__,__FILE__);
				
			return $this->db->get_last_insert_id($this->hours_table,'id');
		}

		function edit_hours($values)
		{
			$this->db->update($this->hours_table,array(
					'activity_id'	=> $values['activity_id'],
					'cost_id'		=> $values['cost_id'],
					'entry_date'	=> time(),
					'start_date'	=> $values['sdate'],
					'end_date'		=> $values['edate'],
					'hours_descr'	=> $values['hours_descr'],
					'remark'		=> $values['remark'],
					'billable'		=> $values['billable'],
					'minutes'		=> $values['w_minutes'],
					'status'		=> $values['status'],
					'employee'		=> $values['employee'],
					'pro_parent'	=> $values['pro_parent'],
					'pro_main'		=> $values['pro_main'],
					'km_distance'	=> 0.0 + $values['km_distance'],
					't_journey'		=> 0.0 + $values['t_journey'],
				),array(
					'id'			=> $values['hours_id'],
				),__LINE__,__FILE__); 
		}

		function delete_hours($values)
		{
			switch($values['action'])
			{
				case 'track':	$h_table = $this->ttracker_table; $column = 'track_id'; break;
				default:		$h_table = $this->hours_table; $column = 'id'; break;
			}

			$this->db->delete($h_table,array($column=>$values['id']),__LINE__,__FILE__);
		}

		/*function update_hours_act($activity_id, $minperae)
		{
			$this->db->update($this->hours_table,array(
					'minperae'		=> $minperae
				),array(
					'activity_id'	=> $activity_id,
					'minperae'		=> 0,				
				),__LINE__,__FILE__); 
		}*/

		function format_wh($minutes = 0)
		{
			if($minutes)
			{
				$sign = $minutes < 0 ? '-' : '';
				$abs_minutes = abs($minutes);

				$wh = array(
					'whours_formatted'	=> $sign . (int) ($abs_minutes/60),
					'wmin_formatted'	=> sprintf('%02d',$abs_minutes - 60*(int)($abs_minutes/60)),
					'wminutes'			=> $minutes,
					'whwm'				=> sprintf('%s%d:%02d',$sign,(int) ($abs_minutes/60),$abs_minutes - 60*(int)($abs_minutes/60)),
				);
			}
			else
			{
				$wh = array
				(
					'whours_formatted'	=> 0,
					'wmin_formatted'	=> 0,
					'wminutes'			=> 0,
					'whwm'				=> 0
				);
			}
			return $wh;
		}

		function calculate_activity_budget($params = 0)
		{
			$where = array('project_id' => is_array($params['project_array']) ? $params['project_array'] : $params['project_id']);
			
			$this->db->select($this->projectactivities_table,'id,activity_id,billable',$where,__LINE__,__FILE__);

			$activities = array();
			while($this->db->next_record())
			{
				$activities[] = array
				(
					'activity_id'	=> $this->db->f('activity_id'),
					'billable'		=> $this->db->f('billable')
				);
			}

			$bbudget = $budget = 0;
			foreach($activities as $activity)
			{
				$this->db->select($this->activities_table,'minperae,billperae',array(
						'id' => $activity['activity_id']
					),__LINE__,__FILE__);
				$this->db->next_record();
				$activity['minperae'] = $this->db->f('minperae');
				$activity['billperae'] = $this->db->f('billperae');
				
				$where['activity_id'] = $activity['activity_id'];
				$where['billable'] = $activity['billable'];	
				$this->db->select($this->hours_table,'SUM(minutes)',$where,__LINE__,__FILE__);
				$this->db->next_record();
				$activity['utime'] = $this->db->f(0);

				$factor_per_minute = $activity['billperae']/60;
				if($activity['billable'] == 'Y')
				{
					$bbudget += round($factor_per_minute * $activity['utime'],2);
				}
				$budget += round($factor_per_minute * $activity['utime'],2);
			}
			return count($activities) ? array('bbudget' => $bbudget,'budget' => $budget) : false;
		}

		function get_activity_time_used($params = 0)
		{
			$where = array('project_id' => is_array($params['project_array']) ? $params['project_array'] : $params['project_id']);

			if($params['no_billable'] || $params['is_billable'])
			{
				$where['billable'] = $params['no_billable'] ? 'N' : 'Y';
			}
			$this->db->select($this->hours_table,'SUM(minutes)',$where,__LINE__,__FILE__);

			if($this->db->next_record())
			{
				return $this->db->f(0);
				//return $this->format_wh($hours);
			}
			return False;
		}

		function get_time_used($params = 0)
		{
			switch($params['action'])
			{
				case 'mains':
					$where = array('pro_main' => $params['project_id']);
					break;
				default:
					$where = array('project_id' => is_array($params['project_array']) ? $params['project_array'] : $params['project_id']);
					break;
			}

			if($params['no_billable'] || $params['is_billable'])
			{
				$where['billable'] = $params['no_billable'] ? 'N' : 'Y';
			}
			$this->db->select($this->hours_table,'SUM(minutes)',$where,__LINE__,__FILE__);

			if($this->db->next_record())
			{
				return $this->db->f(0);
				//return $this->format_wh($hours);
			}
			return False;
		}

		function get_project_employees($params = 0)
		{
			switch($params['action'])
			{
				case 'mains':
					$where = array('pro_main' => $params['project_id']);
					break;
				default:
					$where = array('project_id' => is_array($params['project_array']) ? $params['project_array'] : $params['project_id']);
					break;
			}

			$this->db->select($this->hours_table,'DISTINCT employee',$where,__LINE__,__FILE__);

			$emps = array();
			while($this->db->next_record())
			{
				$emps[] = $this->db->f('employee');
			}
			return $emps;
		}

		function get_employee_time_used($params = 0)
		{
			$where = array('project_id' => is_array($params['project_array']) ? $params['project_array'] : $params['project_id']);

			if($params['no_billable'] || $params['is_billable'])
			{
				$where['billable'] = $params['no_billable'] ? 'N' : 'Y';
			}
			foreach($this->get_project_employees($params) as $emp)
			{
				$where['employee'] = $emp;
				$this->db->select($this->hours_table,'SUM(minutes)',$where,__LINE__,__FILE__);
				if($this->db->next_record())
				{
					//$minutes = $this->db->f(0);
					$bemp[] = array(
						'employee'	=> $emp,
						'utime'		=> $this->db->f(0)
					);
				}
			}
			return $bemp;
		}

		function db2track()
		{
			$track = array();
			while ($this->db->next_record())
			{
				$track[] = array(
					'track_id'		=> $this->db->f('track_id'),
					'project_id'	=> $this->db->f('project_id'),
					'cost_id'		=> $this->db->f('cost_id'),
					'hours_descr'	=> $this->db->f('hours_descr'),
					'status'		=> $this->db->f('status'),
					'minutes'		=> $this->db->f('minutes'),
					'sdate'			=> $this->db->f('start_date'),
					'edate'			=> $this->db->f('end_date'),
					'employee'		=> $this->db->f('employee'),
					'activity_id'	=> $this->db->f('activity_id'),
					'remark'		=> $this->db->f('remark'),
					'km_distance'	=> $this->db->f('km_distance'),
					't_journey'		=> $this->db->f('t_journey'),
					'billable'		=> $this->db->f('billable'),
				);
			}
			return $track;
		}


		function list_ttracker()
		{
			$this->db->select($this->ttracker_table,'*',array('employee'=>$this->account),__LINE__,__FILE__,false,'ORDER BY project_id,start_date ASC');
			
			return $this->db2track();
		}

		function read_single_track($track_id)
		{
			$this->db->select($this->ttracker_table,'*',array('track_id'=>$track_id),__LINE__,__FILE__);
			
			list($hours) = $this->db2track();
			
			return $hours;
		}

		function format_ttime($diff)
		{
			$tdiff = array();
			$tdiff['days'] = floor($diff/60/60/24);
			$diff -= $tdiff['days']*60*60*24;
			$tdiff['hrs'] = floor($diff/60/60);
			$diff -= $tdiff['hrs']*60*60;
			$tdiff['mins'] = round($diff/60);
			//$diff -= $minsDiff*60;
			//$secsDiff = $diff;
			return $tdiff;
		}

		function get_max_track($project_id = '',$status = False)
		{
			$where = array(
				'project_id'	=> $project_id,
				'employee'		=> $this->account,
			);
			if($status)
			{
				$where[] = "status != 'apply'";
			}

			$this->db->select($this->ttracker_table,'max(track_id)',$where,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f(0);
		}

		function check_ttracker($project_id = '',$status='active')
		{
			$where = array('track_id' => $this->get_max_track($project_id));
			//echo 'MAX: ' . $track_id;

			switch($status)
			{
				case 'active':		
					$where[] = "(status='start' OR status='continue') AND end_date=0"; 
					break;
				case 'inactive':	
					$where[] = "status='stop'"; 
					break;
			}
			$this->db->select($this->ttracker_table,'minutes',$where,__LINE__,__FILE__);
			
			return $this->db->next_record();
		}

		function ttracker($values)
		{
			$values['km_distance']	= $values['km_distance'] + 0.0;
			$values['t_journey']	= $values['t_journey'] + 0.0;
			$project_id				= (int) $values['project_id'];

			#_debug_array($values);

			switch($values['action'])
			{
				case 'start':
				case 'continue':
					$db2 = clone($this->db);
					$this->db->select($this->ttracker_table,'track_id,start_date,project_id',array(
							'employee'	=> $this->account,
							'project_id != ' . $project_id,
							"(status='start' OR status='continue') AND minutes=0"
						),__LINE__,__FILE__);

					while($this->db->next_record())
					{
						$wtime = $this->format_ttime(time() - $this->db->f('start_date'));
						$work_time = 60 * $wtime['hrs'] + $wtime['mins'];
						$db2->update($this->ttracker_table,array(
								'end_date'	=> time(),
								'minutes'	=> $work_time,
							),array(
								'track_id'	=> $this->db->f('track_id')
							),__LINE__,__FILE__);

						$db2->insert($this->ttracker_table,array(
								'project_id'	=> $this->db2->f('project_id'),
								'activity_id'	=> 0,
								'start_date'	=> time(),
								'end_date'		=> 0,
								'employee'		=> $this->account,
								'status'		=> 'pause',
								'hours_descr'	=> 'pause',
								'remark'		=> '',
								'billable'  	=> $values['billable'],
							),false,__LINE__,__FILE__);
					};
					// fall-through
				case 'pause':
				case 'stop':
					$max = (int) $this->get_max_track($project_id);
					$this->db->select($this->ttracker_table,'start_date',array('track_id'=>$max),__LINE__,__FILE__);
					$this->db->next_record();
					$sdate = $this->db->f(0);
					$edate = time();
					$wtime = $this->format_ttime($edate - $sdate);
					$work_time = 60 * $wtime['hrs'] + $wtime['mins'];

					$this->db->update($this->ttracker_table,array(
							'minutes'	=> $work_time,
							'end_date'	=> $edate,
						),array(
							'track_id'	=> $max
						),__LINE__,__FILE__);

					$this->db->insert($this->ttracker_table,array(
							'project_id'	=> $project_id,
							'activity_id'	=> $values['activity_id'],
							'cost_id'		=> $values['cost_id'],
							'start_date'	=> time(),
							'end_date'		=> 0,
							'employee'		=> $this->account,
							'status'		=> $values['action'],
							'hours_descr'	=> $values['hours_descr'] ? $values['hours_descr'] : $values['action'],
							'remark'		=> $values['remark'],
							'billable'  	=> $values['billable'],
						),false,__LINE__,__FILE__);

					if($values['action'] == 'stop')
					{
						$this->db->update($this->ttracker_table,array('stopped'=>'Y'),array(
								'employee'	=> $this->account,
								'project_id'=> $project_id
							),__LINE__,__FILE__);
					}
					break;

				case 'edit':
					$this->db->update($this->ttracker_table,array(
							'activity_id'	=> $values['activity_id'],
							'start_date'	=> $values['sdate'],
							'end_date'		=> $values['edate'],
							'minutes'		=> $values['w_minutes'],
							'hours_descr'	=> $values['hours_descr'],
							'remark'		=> $values['remark'],
							'billable'  	=> $values['billable'],
						),array(
							'track_id'		=> $values['track_id']
						),__LINE__,__FILE__);
					break;

				case 'apply':
					$this->db->insert($this->ttracker_table,array(
							'project_id'	=> $project_id,
							'activity_id'	=> (int)$values['activity_id'],
							'cost_id'		=> $values['cost_id'],
							'employee'		=> $this->account,
							'start_date'	=> $values['sdate'],
							'end_date'		=> $values['sdate'],	// RalfBecker: is that correct not $values['edate'] ?
							'minutes'		=> $values['w_minutes'],
							'hours_descr'	=> $values['hours_descr']?$values['hours_descr']:'',
							'status'		=> $values['action'],
							'remark'		=> $values['remark'],
							't_journey'		=> 0.0 + $values['t_journey'],
							'km_distance'	=> 0.0 + $values['km_distance'],
							'stopped'		=> 'Y',
							'billable'  	=> $values['billable'],
						),false,__LINE__,__FILE__);

					//return $this->db->get_last_insert_id('phpgw_p_ttracker','track_id');
					break;
			}
		}

		function save_ttracker()
		{
			$this->db->select($this->ttracker_table,'*',array(
					"status !='pause' and status != 'stop' and end_date > 0 and minutes > 0 and stopped='Y'",
					'employee'	=> $this->account,
				),__LINE__,__FILE__);

			$hours = $this->db2track();

			foreach($hours as $hour)
			{
				$hour['pro_parent']	= $this->return_value('pro_parent',$hour['project_id']);
				$hour['pro_main']	= $this->return_value('pro_main',$hour['project_id']);

				$this->db->insert($this->hours_table,array(
						'project_id'	=> $hour['project_id'],
						'activity_id'	=> $hour['activity_id'],
						'cost_id'		=> $hour['cost_id'],
						'entry_date'	=> time(),
						'start_date'	=> $hour['sdate'],
						'end_date'		=> $hour['edate'],
						'hours_descr'	=> $hour['hours_descr'],
						'remark'		=> $hour['remark'],
						'minutes'		=> $hour['minutes'],
						'status'		=> 'done',
						'employee'		=> $hour['employee'],
						'pro_parent'	=> $hour['pro_parent'],
						'pro_main'		=> $hour['pro_main'],
						'billable'		=> $hour['billable'],
						't_journey'		=> 0.0 + $hour['t_journey'],
						'km_distance'	=> 0.0 + $hour['km_distance'],
					),false,__LINE__,__FILE__);

				$this->db->delete($this->ttracker_table,array('track_id'=>$hour['track_id']),__LINE__,__FILE__);
			}
			$this->db->delete($this->ttracker_table,array(
					'employee'	=> $this->account,
					"(status='pause' OR status='stop') AND stopped='Y'"
				),__LINE__,__FILE__);
		}

		function return_value($action,$pro_id)
		{
			switch ($action)
			{
				case 'pro_main':	$column = 'main'; break;
				case 'pro_parent':	$column = 'parent'; break;
			}

			$this->db->select($this->projects_table,$column,array('project_id'=>$pro_id),__LINE__,__FILE__);

			return $this->db->next_record() ? $this->db->f(0) : false;
		}
	}
?>