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
	/* $Id: class.boprojecthours.inc.php,v 1.20.2.3 2004/11/06 12:15:27 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.boprojecthours.inc.php,v $

	class boprojecthours
	{
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $status;
		var $project_id;

		var $public_functions = array
		(
			'list_hours'		=> True,
			'check_values'		=> True,
			'save_hours'		=> True,
			'read_single_hours'	=> True,
			'delete_hours'		=> True
		);

		function boprojecthours()
		{
			$action = get_var('action',array('GET'));

			$this->boprojects	= CreateObject('projects.boprojects',True,$action);
			$this->boconfig		= CreateObject('projects.boconfig');

			$this->sohours		= $this->boprojects->sohours;
			$this->account		= $this->boprojects->account;
			$this->grants		= $this->boprojects->grants;

			$this->start		= $this->boprojects->start;
			$this->query		= $this->boprojects->query;
			$this->filter		= $this->boprojects->filter;
			$this->order		= $this->boprojects->order;
			$this->sort		= $this->boprojects->sort;
			$this->status		= $this->boprojects->status;
			$this->project_id	= $this->boprojects->project_id;
			$this->cat_id		= $this->boprojects->cat_id;
			$this->siteconfig	= $this->boprojects->siteconfig;
		}

		function add_perms($pro)
		{
			$coordinator = $this->boprojects->return_value('co',$this->project_id);

			if ($this->boprojects->check_perms($this->grants[$coordinator],PHPGW_ACL_ADD) || $coordinator == $this->account)
			{
				return True;
			}

			if($this->member())
			{
				return True;
			}
			//$main = $this->boprojects->return_value('main',$this->project_id);
			//$main_co = $this->boprojects->return_value('co',intval($pro['main']));
			if($this->boprojects->check_perms($this->grants[$pro['main_co']],PHPGW_ACL_ADD) || $pro['main_co'] == $this->account)
			{
				return True;
			}
			$parent = $this->boprojects->return_value('parent',$this->project_id);
			$parent_co = $this->boprojects->return_value('co',$parent);
			if($this->boprojects->check_perms($this->grants[$parent_co],PHPGW_ACL_ADD) || $parent_co == $this->account)
			{
				return True;
			}
			if($this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager'))
			{
				return True;
			}
			return False;
		}

		function edit_perms($pro)
		{
			$pro['action'] = isset($pro['action'])?$pro['action']:'edit';

			switch($pro['action'])
			{
				case 'delete':	$acl = PHPGW_ACL_DELETE; break;
				default:		$acl = PHPGW_ACL_EDIT; break;
			}

			if (($pro['status'] != 'billed') && ($pro['status'] != 'closed'))
			{
				if ($pro['employee'] == $this->account && !$pro['adminonly'])
				{
					return True;
				}

				$coordinator = $this->boprojects->return_value('co',$this->project_id);
				if ($this->boprojects->check_perms($this->grants[$coordinator],$acl) || $coordinator == $this->account)
				{
					return True;
				}

				//$main_co = $this->boprojects->return_value('co',intval());
				if($this->boprojects->check_perms($this->grants[$pro['main_co']],$acl) || $pro['main_co'] == $this->account)
				{
					return True;
				}
				$parent = $this->boprojects->return_value('parent',$this->project_id);
				$parent_co = $this->boprojects->return_value('co',$parent);
				if($this->boprojects->check_perms($this->grants[$parent_co],$acl) || $parent_co == $this->account)
				{
					return True;
				}
				if($this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager'))
				{
					return True;
				}
				return False;
			}
		}

		function format_htime($hdate = '')
		{
			$hdate = (int)$hdate;

			if ($hdate > 0)
			{
				$htime['date'] = $GLOBALS['phpgw']->common->show_date($hdate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$hdate = $hdate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$hour = date('H',$hdate);
				$min = date('i',$hdate);
				$htime['time'] = $GLOBALS['phpgw']->common->formattime($hour,$min);
			}
			else
			{
				$htime['date'] = 0;
				$htime['time'] = 0;
			}
			return $htime;
		}

		function hdate_format($hdate = '')
		{
			if (!$hdate)
			{
				$dateval['month'] = date('m',time());
				$dateval['day'] = date('d',time());
				$dateval['year'] = date('Y',time());
				$dateval['hour'] = date('H',time());
				$dateval['min'] = date('i',time());
			}
			else
			{
				$dateval['month'] = date('m',$hdate);
				$dateval['day'] = date('d',$hdate);
				$dateval['year'] = date('Y',$hdate);
				$dateval['hour'] = date('H',$hdate);
				$dateval['min'] = date('i',$hdate);
			}
			return $dateval;
		}

		function list_hours()
		{
			$hours_list = $this->sohours->read_hours(array('start' => $this->start,'limit' => $this->limit,'query' => $this->query,'filter' => $this->filter,
																	'sort' => $this->sort,'order' => $this->order,'status' => $this->state,'project_id' => $this->project_id));
			$this->total_records = $this->sohours->total_records;

			while(is_array($hours_list) && list(,$hour) = each($hours_list))
			{
				$hours[] = array
				(
					'hours_id'			=> $hour['hours_id'],
					'project_id'		=> $hour['project_id'],
					'hours_descr'		=> $GLOBALS['phpgw']->strip_html($hour['hours_descr']),
					'activity_title'	=> $this->siteconfig['accounting']=='activity'?$this->boprojects->return_value('act',$hour['activity_id']):'',
					'status'			=> $hour['status'],
					'statusout'			=> lang($hour['status']),
					'sdate'				=> $hour['start_date'],
					'edate'				=> $hour['end_date'],
					'minutes'			=> $hour['minutes'],
					'wh'				=> $this->sohours->format_wh($hour['minutes']),
					'employee'			=> $hour['employee'],
					'employeeout'		=> $GLOBALS['phpgw']->common->grab_owner_name($hour['employee']),
					'sdate_formatted'	=> $this->format_htime($hour['sdate']),
					'edate_formatted'	=> $this->format_htime($hour['edate']),
					'billable'			=> $hour['billable'],
				);
			}
			return $hours;
		}

		function read_single_hours($hours_id)
		{
			$hours = $this->sohours->read_single_hours($hours_id);

			return array_merge((array) $hours,array(
				'hours_descr'		=> $GLOBALS['phpgw']->strip_html($hours['hours_descr']),
				'statusout'			=> lang($hours['status']),
				'wh'				=> $this->sohours->format_wh($hours['minutes']),
				'employeeout'		=> $GLOBALS['phpgw']->common->grab_owner_name($hours['employee']),
				'activity_title'	=> $this->siteconfig['accounting']=='activity'?$this->boprojects->return_value('act',$hours['activity_id']):'',
				'remark'			=> nl2br($GLOBALS['phpgw']->strip_html($hours['remark'])),
				'sdate_formatted'	=> $this->hdate_format($hours['sdate']),
				'edate_formatted'	=> $this->hdate_format($hours['edate']),
				'stime_formatted'	=> $this->format_htime($hours['sdate']),
				'etime_formatted'	=> $this->format_htime($hours['edate']),
			));
		}

		function member()
		{
			return $this->boprojects->member($this->project_id);
		}

		function check_values($values)
		{
			if(!$values['project_id'])
			{
				$error[] = lang('please select a project for time tracking');
			}

			if (strlen($values['hours_descr']) > 250)
			{
				$error[] = lang('Description can not exceed 250 characters in length');
			}

			if (strlen($values['remark']) > 8000)
			{
				$error[] = lang('Remark can not exceed 8000 characters in length !');
			}

			/*if ($values['shour'] && ($values['shour'] != 0) && ($values['shour'] != 12))
			{
				if ($values['sampm']=='pm')
				{
					$values['shour'] = $values['shour'] + 12;
				}
			}

			if ($values['shour'] && ($values['shour'] == 12))
			{
				if ($values['sampm']=='am')
				{
					$values['shour'] = 0;
				}
			}

			if ($values['ehour'] && ($values['ehour'] != 0) && ($values['ehour'] != 12))
			{
				if ($values['eampm']=='pm')
				{
					$values['ehour'] = $values['ehour'] + 12;
				}
			}

			if ($values['ehour'] && ($values['ehour'] == 12))
			{
				if ($values['eampm']=='am')
				{
					$values['ehour'] = 0;
				}
			}*/

			if (! @checkdate($values['smonth'],$values['sday'],$values['syear']))
			{
				$error[] = lang('You have entered an invalid start date');
			}

			if ($values['emonth'] || $values['eday'] || $values['eyear'])
			{
				if (! checkdate($values['emonth'],$values['eday'],$values['eyear']))
				{
					$error[] = lang('You have entered an invalid end date');
				}
			}

			if($this->siteconfig['accounting'] == 'activity')
			{
				$activity = $this->boconfig->read_single_activity($values['activity_id']);

				if (! is_array($activity))		
				{
					$error[] = lang('You have selected an invalid activity');
				}
				else
				{
					if ($activity['remarkreq']=='Y' && (!$values['remark']))
					{
						$error[] = lang('Please enter a remark');
					}
				}
			}

			if (is_array($error))
			{
				return $error;
			}
		}

		function check_ttracker($values)
		{
			if(!$values['project_id'])
			{
				$error[] = lang('please select a project for time tracking');
			}

			if($values['start'] || $values['continue'])
			{
				$is_active = $this->sohours->check_ttracker($values['project_id'],'active');
				if($is_active)
				{
					$error[] = lang('time tracking for this project is already active');
				}
			}
			else if($values['stop'] || $values['pause'])
			{
				$is_active = $this->sohours->check_ttracker($values['project_id'],'inactive');
				if($is_active)
				{
					$error[] = lang('time tracking for this project has been stopped already');
				}
			}
			return $error;
		}

		function save_hours($values)
		{
			if ($values['shour'] && ($values['shour'] != 0) && ($values['shour'] != 12))
			{
				if ($values['sampm']=='pm')
				{
					$values['shour'] = $values['shour'] + 12;
				}
			}

			if ($values['shour'] && ($values['shour'] == 12))
			{
				if ($values['sampm']=='am')
				{
					$values['shour'] = 0;
				}
			}

			if ($values['ehour'] && ($values['ehour'] != 0) && ($values['ehour'] != 12))
			{
				if ($values['eampm']=='pm')
				{
					$values['ehour'] = $values['ehour'] + 12;
				}
			}

			if ($values['ehour'] && ($values['ehour'] == 12))
			{
				if ($values['eampm']=='am')
				{
					$values['ehour'] = 0;
				}
			}

			if ($values['smonth'] || $values['sday'] || $values['syear'])
			{
				$values['sdate'] = mktime($values['shour'],$values['smin'],0,$values['smonth'], $values['sday'], $values['syear']);
			}
			
			if (!$values['sdate'])
			{
				$values['sdate'] = time();
			}

			if ($values['emonth'] || $values['eday'] || $values['eyear'])
			{
				$values['edate'] = mktime($values['ehour'],$values['emin'],0,$values['emonth'],$values['eday'],$values['eyear']);
			}

			$values['w_minutes'] = $values['hours']*60+$values['minutes'];

			if($values['track_id'] || $values['action'] == 'apply')
			{
				$this->ttracker($values);
			}
			else
			{
				if (!$values['employee'])
				{
					$values['employee'] = $this->sohours->account;
				}

				$values['project_id']	= $this->project_id;
				$values['pro_parent']	= $this->boprojects->return_value('parent',$this->project_id);
				$values['pro_main']	= $values['pro_main']?$values['pro_main']:$this->project_id;

				if (intval($values['hours_id']) > 0)
				{
					$this->sohours->edit_hours($values);
				}
				else
				{
					$this->sohours->add_hours($values);
				}
				$pro = $this->boprojects->read_single_project($this->project_id,'budget','subs');

				// HOURS ALARM

				$hours_percent = $this->boprojects->soconfig->get_event_extra('hours limit');
				$hours_percent = $hours_percent>0?$hours_percent:100;
				$pro['ptime_min_percent'] = ($pro['ptime_min']*intval($hours_percent))/100;
				//echo 'PTIME_MIN_PERCENT: ' . $pro['ptime_min_percent'];
				//echo 'uhours_jobs_all: ' . $pro['uhours_jobs_all_wminutes'];
				if($pro['uhours_jobs_all_wminutes'] >= $pro['ptime_min_percent'])
				{
					//echo 'uhours_jobs_all ' . $pro['uhours_jobs_all_wminutes'] . ' >= ' . $pro['ptime_min_percent'];
					$alarm = $this->boprojects->soprojects->get_alarm(array('project_id' => $this->project_id));

					if(is_array($alarm))
					{
						$alarm_id = $alarm['alarm_id'];
						if($pro['ptime_min'] != $alarm['extra'])
						{
							$this->boprojects->soprojects->update_alarm(array('alarm_id' => $alarm['alarm_id'],'extra' => $pro['ptime_min']));
						}
					}
					else
					{
						$alarm_id = $this->boprojects->soprojects->add_alarm(array('project_id' => $this->project_id,'extra' => $pro['ptime_min']));
					}
					$return = $this->boprojects->send_alarm(array('project_id' => $this->project_id,'event_type' => 'hours limit','project_name' =>
															$pro['title'] . ' [' . $pro['number'] . ']','ptime' => $pro['ptime'],'uhours_jobs_all' =>
															$pro['uhours_jobs_all']));
					if($return)
					{
						$this->boprojects->soprojects->update_alarm(array('alarm_id' => $alarm_id,'send' => '0','extra' => $pro['ptime_min']));
					}
				}

				// BUDGET ALARM

				$budget_percent = $this->boprojects->soconfig->get_event_extra('budget limit');
				$budget_percent = $budget_percent>0?$budget_percent:100;
				$pro['budget_percent'] = ($pro['budgetSum']*intval($budget_percent))/100;

				if($pro['u_budget_jobs'] >= $pro['budget_percent'])
				{
					//echo 'u_budget_jobs ' . $pro['u_budget_jobs'] . ' >= ' . $pro['budget_percent'];
					$alarm = $this->boprojects->soprojects->get_alarm(array('project_id' => $this->project_id,'action' => 'budget'));

					if(is_array($alarm))
					{
						$alarm_id = $alarm['alarm_id'];
						if($pro['budget'] != $alarm['extra'])
						{
							$this->boprojects->soprojects->update_alarm(array('alarm_id' => $alarm['alarm_id'],'extra' => $pro['budget']));
						}
					}
					else
					{
						$alarm_id = $this->boprojects->soprojects->add_alarm(array('project_id' => $this->project_id,'action' => 'budget','extra' => $pro['budget']));
					}
					$return = $this->boprojects->send_alarm(array('project_id' => $this->project_id,'event_type' => 'budget limit','project_name' =>
															$pro['title'] . ' [' . $pro['number'] . ']','budget' => $pro['budget'],'u_budget_jobs' =>
															$pro['u_budget_jobs']));
					if($return)
					{
						$this->boprojects->soprojects->update_alarm(array('alarm_id' => $alarm_id,'send' => '0','extra' => $pro['budget']));
					}
				}
			}
		}

		function delete_hours($values)
		{
			$this->sohours->delete_hours($values);
		}

		function list_ttracker()
		{
			$tracking = $this->sohours->list_ttracker();
			$project_list = $this->boprojects->select_project_list(array('action' => 'all','filter' => 'noadmin','formatted' => False));

			//_debug_array($htracker);

			if(is_array($project_list))
			{
				foreach($project_list as $key => $pro)
				{
					$hours[$key] = array
					(
						'project_title'	=> $GLOBALS['phpgw']->strip_html($pro['title']) . ' [' . $GLOBALS['phpgw']->strip_html($pro['p_number']) . ']',
						'project_id'	=> $pro['project_id']
					);

					if(is_array($tracking))
					{
					foreach($tracking as $track)
					{
						if($track['project_id'] == $pro['project_id'])
						{
							$hours[$key]['hours'][] = array
							(
								'track_id'			=> $track['track_id'],
								'activity_title'	=> $this->boprojects->return_value('act',$track['activity_id']),
								'hours_descr'		=> $GLOBALS['phpgw']->strip_html($track['hours_descr']),
								'status'			=> $track['status'],
								'sdate_formatted'	=> $this->format_htime($track['sdate']),
								'edate'				=> $track['edate'],
								'edate_formatted'	=> $this->format_htime($track['edate']),
								'remark'			=> nl2br($GLOBALS['phpgw']->strip_html($track['remark'])),
								'wh'				=> $this->sohours->format_wh($track['minutes']),
								'billable'			=> $track['billable'],
							);
						}
					}
					}
				}
				//_debug_array($hours);
				return $hours;
			}
		}

		function ttracker($values)
		{
			if(!isset($values['action']))
			{
				$values['action'] = isset($values['start'])?'start':(isset($values['stop'])?'stop':(isset($values['pause'])?'pause':(isset($values['continue'])?'continue':'edit')));
			}

			switch($values['action'])
			{
				case 'save':	
					$this->sohours->save_ttracker(); 
					break;
				default:	
					$this->sohours->ttracker($values); 
					break;
			}
		}

		function read_single_track($track_id)
		{
			$track = $this->sohours->read_single_track($track_id);

			//_debug_array($hours);
			return array_merge((array)$track,array(
				'wh'				=> $track['minutes'] > 0 ? $this->sohours->format_wh($track['minutes']) : 0,
				'hours_descr'		=> $GLOBALS['phpgw']->strip_html($track['hours_descr']),
				'remark'			=> $GLOBALS['phpgw']->strip_html($track['remark']),
				'sdate_formatted'	=> $this->hdate_format($track['sdate']),
				'edate_formatted'	=> $track['edate'] > 0 ? $this->hdate_format($track['edate']) : 0,
				'stime_formatted'	=> $this->format_htime($track['sdate']),
				'etime_formatted'	=> $this->format_htime($track['edate']),
			));
		}
	}
?>
