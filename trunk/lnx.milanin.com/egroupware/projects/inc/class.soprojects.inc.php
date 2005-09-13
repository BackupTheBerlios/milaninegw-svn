<?php
	/*******************************************************************\
	* eGroupWare - Projects                                             *
	* http://www.egroupware.org                                         *
	*                                                                   *
	* Project Manager                                                   *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* DB-Layer partialy reworked by RalfBecker-AT-outdoor-training.de   *
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
	/* $Id: class.soprojects.inc.php,v 1.64.2.3 2004/11/06 12:15:28 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.soprojects.inc.php,v $

	class soprojects
	{
		var $db;
		var $grants;
		var $column_array;
		var $budget_table = 'phpgw_p_budget';
		var $project_table = 'phpgw_p_projects';

		function soprojects()
		{
			$this->db		= $GLOBALS['phpgw']->db;
			$this->db->set_app('projects');
			$this->db2		= clone($this->db);
			$this->grants		= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->currency 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			$this->year		= $GLOBALS['phpgw']->common->show_date(time(),'Y');
			$this->member		= $this->get_acl_projects();
			$this->soconfig		= CreateObject('projects.soconfig');
			$this->siteconfig	= $this->get_site_config();

			$this->column_array 	= array();
		}

		/**
		 * Update the budget of a project
		 *
		 * @param int $project_id project-id
		 * @param array $budgets 2-dim. array of 'year' and 'month' with budget-amount
		 * @param db-object &$db as this class used so many db-instances, just select the one you want to use
		 */
		function _update_budget($project_id,$budgets,&$db)
		{
			$db->delete($this->budget_table,array(
					'project_id' => $project_id,
				),__LINE__,__FILE__);
			
			if(!is_array($budgets)) return;

			foreach($budgets as $year => $yearData)
			{
				if(!is_array($yearData)) continue;

				foreach($yearData as $month => $budget)
				{
					$db->insert($this->budget_table,array(
							'project_id'	=> $project_id,
							'budget_year'	=> $year,
							'budget_month'	=> $month,
							'budget_amount'	=> $budget,
						),False,__LINE__,__FILE__);
				}
			}
		}

		function project_filter($type)
		{
			switch ($type)
			{
				case 'subs':			$s = ' and parent != 0'; break;
				case 'mains':			$s = ' and parent = 0'; break;
				default: return False;
			}
			return $s;
		}

		function db2projects($column = False)
		{
			$db = clone($this->db);
			
			$i = 0;
			while ($this->db->next_record())
			{
				if($column)
				{
					$projects[$i] = array();
					for($k=0;$k<count($this->column_array);$k++)
					{
						switch($this->column_array[$k])
						{
							case 'budget':
								// some legacy code, to move existing budgets to the new
								// budget table
								if(intval($this->db->f('budget')))
								{
									$projects[$i]['budget']['0']['0'] = $this->db->f('budget');
									$projects[$i]['budgetSum'] = $this->db->f('budget');
									
									$this->_update_budget($this->db->f('project_id'),array(array($this->db->f('budget'))),$db);

									$query = "update phpgw_p_projects set budget='0' where project_id='".$this->db->f('project_id')."'";
									$db->query($query,__LINE__, __FILE__);
								}
								$projects[$i] = array_merge($projects[$i], 
									$this->getBudget($this->db->f('project_id')));
								break;
							default:
								$projects[$i][$this->column_array[$k]] = 
									$this->db->f($this->column_array[$k]);
								break;
						}
					}
					$i++;
				}
				else
				{
					$newProject = array
					(
						'project_id'		=> $this->db->f('project_id'),
						'parent'			=> $this->db->f('parent'),
						'number'			=> $this->db->f('p_number'),
						'access'			=> $this->db->f('access'),
						'cat'				=> $this->db->f('category'),
						'sdate'				=> $this->db->f('start_date'),
						'edate'				=> $this->db->f('end_date'),
						'coordinator'		=> $this->db->f('coordinator'),
						'customer'			=> $this->db->f('customer'),
						'status'			=> $this->db->f('status'),
						'descr'				=> $this->db->f('descr'),
						'title'				=> $this->db->f('title'),
						//'budget'			=> $this->db->f('budget'),
						'e_budget'			=> $this->db->f('e_budget'),
						'ptime'				=> $this->db->f('time_planned'),
						'owner'				=> $this->db->f('owner'),
						'cdate'				=> $this->db->f('date_created'),
						'processor'			=> $this->db->f('processor'),
						'udate'				=> $this->db->f('entry_date'),
						'investment_nr'		=> $this->db->f('investment_nr'),
						'main'				=> $this->db->f('main'),
						'level'				=> $this->db->f('level'),
						'previous'			=> $this->db->f('previous'),
						'customer_nr'		=> $this->db->f('customer_nr'),
						'url'				=> $this->db->f('url'),
						'reference'			=> $this->db->f('reference'),
						'result'			=> $this->db->f('result'),
						'test'				=> $this->db->f('test'),
						'quality'			=> $this->db->f('quality'),
						'accounting'		=> $this->db->f('accounting'),
						'project_accounting_factor'	=> $this->db->f('acc_factor'),
						'project_accounting_factor_d'	=> $this->db->f('acc_factor_d'),
						'billable'			=> $this->db->f('billable'),
						'psdate'			=> $this->db->f('psdate'),
						'pedate'			=> $this->db->f('pedate'),
						'priority'			=> $this->db->f('priority'),
						'discount'			=> $this->db->f('discount'),
						'discount_type'		=> $this->db->f('discount_type'),
						'inv_method'		=> $this->db->f('inv_method')
					);
					// some legacy code, to move existing budgets to the new
					// budget table
					if(intval($this->db->f('budget')))
					{
						$newProject['budget']['0']['0'] = $this->db->f('budget');
						$newProject['budgetSum'] = $this->db->f('budget');
						
						$this->_update_budget($newProject['project_id'],array(array($this->db->f('budget'))),$db);

						$query = "update phpgw_p_projects set budget='0' where project_id='". $newProject['project_id'] ."'";
						$db->query($query,__LINE__, __FILE__);
					}
					$newProject = array_merge($newProject, $this->getBudget($newProject['project_id']));
					$projects[] = $newProject;
				}
			}
			return $projects;
		}
		
		/**
		* @return array
		* @param int $_projectID the project_id
		* @desc Reads the budget data from the database and merges them with
		* first parameter
		*/
		function getBudget($_projectID)
		{
			$db = clone($this->db);
			
			$db->select($this->budget_table,'budget_amount,budget_month,budget_year',array(
					'project_id' => $_projectID,
				 ),__LINE__, __FILE__,false,'ORDER BY budget_year,budget_month');
			
			$budget		= array
			(
				'budget'	=> array(),
				'budgetSum'	=> 0
			);
			
			while($db->next_record())
			{
				$budget['budget'][$db->f('budget_year')][$db->f('budget_month')] = $db->f('budget_amount');
				$budget['budgetSum'] += $db->f('budget_amount');
			}
			return $budget;
		}
		
		function getProjectResources($_projectID, $_employees)
		{
			$resources = array();
		
			if(!is_array($_employees)) return false;
			
			$employees = implode(',',$_employees);
			
			$query = "select employee,resource from phpgw_p_resources where employee in($employees) and project_id='$_projectID'";
			
			$this->db->query($query, __LINE__, __FILE__);
			
			while($this->db->next_record())
			{
				$resources[$this->db->f('employee')] = array
				(
					'resource'	=> $this->db->f('resource'),
				);
			}
			
			return $resources;
		}

		function read_projects($values)
		{
			$start		= intval($values['start']);
			$limit		= (isset($values['limit'])?$values['limit']:True);
			$filter		= (isset($values['filter'])?$values['filter']:'none');
			$sort		= $values['sort']?$values['sort']:'ASC';
			$order		= $values['order']?$values['order']:'p_number,title,start_date';
			$status		= isset($values['status'])?$values['status']:'active';
			$action		= (isset($values['action'])?$values['action']:'mains');

			$cat_id		= intval($values['cat_id']);
			$main		= intval($values['main']);
			$parent		= intval($values['parent']);
			$project_id	= intval($values['project_id']);
			$column		= (isset($values['column'])?$values['column']:False);

			$query	= $this->db->db_addslashes($values['query']);

			if ($status)
			{
				$statussort = " AND status = '" . $status . "' ";
			}
			else
			{
				$statussort = " AND status != 'archive' ";
			}

			$ordermethod = " order by $order $sort";

			if ($filter == 'none' || $filter == 'noadmin')
			{
				if ($filter == 'none' && ($this->soconfig->isprojectadmin('pad') || $this->soconfig->isprojectadmin('pmanager') || $this->soconfig->isprojectadmin('psale')))
				{
					$filtermethod = " ( access != 'private' OR coordinator = " . $this->account . ' )';
				}
				else
				{
					$filtermethod = ' ( coordinator=' . $this->account;
					if (is_array($this->grants))
					{
						$grants = $this->grants;
						while (list($user) = each($grants))
						{
							$public_user_list[] = $user;
						}
						reset($public_user_list);
						$filtermethod .= " OR (access != 'private' AND coordinator in(" . implode(',',$public_user_list) . '))';
					}

					if (is_array($this->member))
					{
						$filtermethod .= " OR (access != 'private' AND project_id in(" . implode(',',$this->member) . '))';
					}
					$filtermethod .= ' )';
				}
			}
			elseif ($filter == 'yours')
			{
				$filtermethod = ' coordinator=' . $this->account;
			}
			elseif ($filter == 'anonym')
			{
				$filtermethod = " access = 'anonym' ";
			}
			else
			{
				$filtermethod = ' coordinator=' . $this->account . " AND access='private'";
			}

			if ($cat_id > 0)
			{
				$filtermethod .= ' AND category=' . $cat_id;
			}

			switch($action)
			{
				case 'all':				break;
				case 'mains':			$parent_select = ' AND parent=0'; break;
				case 'subs':			$parent_select = ' AND (parent=' . $parent . ' AND parent != 0)'; break;
				case 'mainandsubs':		$parent_select = ' AND main=' . $main; break;
				case 'mainsubsorted':	$parent_select = ' AND project_id=' . $project_id; break;
			}

			if ($query)
			{
				switch($this->db->Type)
				{
					case 'sapdb':	// dont search in descr as it's a text/LONG column
					case 'maxdb':
						$querymethod = " AND (title like '%$query%' OR p_number like '%$query%') ";
						break;
					default:
						$querymethod = " AND (title like '%$query%' OR p_number like '%$query%' OR descr like '%$query%') ";
						break;
				}
			}

			$column_select = ((is_string($column) && $column != '')?$column:'*');
			$this->column_array = explode(',',$column);

			$sql = "SELECT $column_select from phpgw_p_projects WHERE $filtermethod $statussort $querymethod";

			if ($limit && $action == 'mains')
			{
				$this->db2->query($sql . $parent_select,__LINE__,__FILE__);

				//echo 'query main: ' . $sql . $parent_select;

				$total = $this->db2->num_rows();

				$this->db->limit_query($sql . $parent_select . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $parent_select . $ordermethod,__LINE__,__FILE__);
				$total = $this->db->num_rows();
			}

			$pro = $this->db2projects($column);

			if ($main == 0 && $action != 'mains' && $action != 'all')
			{
				$num_pro = count($pro);
				for ($i=0;$i < $num_pro;$i++)
				{
					$sub_select = ' AND parent=' . $pro[$i]['project_id'] . ' AND level=' . ($pro[$i]['level']+1);

					$this->db->query($sql . $sub_select . $ordermethod,__LINE__,__FILE__);
					$total += $this->db->num_rows();
					$subpro = $this->db2projects($column);

					$num_subpro = count($subpro);
					if ($num_subpro != 0)
					{
						$newpro = array();
						for ($k = 0; $k <= $i; $k++)
						{
							$newpro[$k] = $pro[$k];
						}
						for ($k = 0; $k < $num_subpro; $k++)
						{
							$newpro[$k+$i+1] = $subpro[$k];
						}
						for ($k = $i+1; $k < $num_pro; $k++)
						{
							$newpro[$k+$num_subpro] = $pro[$k];
						}
						$pro = $newpro;
						$num_pro = count($pro);
					}
				}
			}

			$this->total_records = $total;
			if ($limit && $main == 0 && $action != 'mains')
			{
				$max = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
				$max = $max + $start;

				$k=0;
				for($i=$start;$i<$max;++$i)
				{
					if(is_array($pro[$i]))
					{
						$spro[$k] = $pro[$i];
						++$k;
					}
				}
				if(is_array($spro))
				{
					$pro = $spro;
				}
			}

			//_debug_array($pro);
			return $pro;
		}

		function read_single_project($project_id)
		{
			$this->db->query('SELECT * from phpgw_p_projects WHERE project_id=' . intval($project_id),__LINE__,__FILE__);

			list($project) = $this->db2projects();

			return $project;
		}

		function select_project_list($values)
		{
			$formatted = isset($values['formatted'])?$values['formatted']:True;

			$pro = $this->read_projects(array
						(
							'limit'		=> False,
							'status'	=> $values['status'],
							'action'	=> (isset($values['action'])?$values['action']:'mains'),
							'main'		=> $values['main'],
							'filter'	=> $values['filter'],
							'column'	=> 'project_id,p_number,level,title'
						));

			if($values['self'])
			{
				for ($i=0;$i<count($pro);$i++)
				{
					if ($pro[$i]['project_id'] == $values['self'])
					{
						unset($pro[$i]);
					}
				}
			}

			if(is_array($pro) && $formatted)
			{
				$s = '';
				foreach($pro as $p)
				{
					$s .= '<option value="' . $p['project_id'] . '"';
					if ($p['project_id'] == $values['selected'])
					{
						$s .= ' selected';
					}
					$s .= '>';

					for ($j=0;$j<$p['level'];$j++)
					{
						$s .= '&nbsp;.&nbsp;';
					}

					$s .= $GLOBALS['phpgw']->strip_html($p['title']) . ' [ ' . $GLOBALS['phpgw']->strip_html($p['number']?$p['number']:$p['p_number']) . ' ]';
					$s .= '</option>';
				}
			}
			return $formatted?$s:$pro;
		}

		/**
		* @return unknown
		* @param unknown $values
		* @desc adds a new project
		*/
		function add_project($values)
		{
			$values['e_budget']	= $values['e_budget'] + 0.0;
			$values['discount']	= $values['discount'] + 0.0;
			$values['project_accounting_factor'] = $values['project_accounting_factor'] + 0.0;
			$values['project_accounting_factor_d'] = $values['project_accounting_factor_d'] + 0.0;
			$values['parent']	= intval($values['parent']);

			if ($values['parent'] > 0)
			{
				$values['main']		= intval($this->id2item(array('item_id' => $values['parent'],'item' => 'main')));
				$values['level']	= intval($this->id2item(array('item_id' => $values['parent'],'item' => 'level'))+1);
			}

			$this->db->lock($this->project_table);

			$p_id = $this->_add_update_project($values);

			$this->db->unlock();

			if ($p_id)
			{
				$this->_update_budget($p_id,$values['budget'],$this->db);
				
				if ($values['parent'] == 0)
				{
					$this->db->query('UPDATE phpgw_p_projects SET main=' . $p_id . ' WHERE project_id=' . $p_id,__LINE__,__FILE__);
				}

				if (is_array($values['book_activities']))
				{
					while($activ=each($values['book_activities']))
					{
						$this->db->query('insert into phpgw_p_projectactivities (project_id,activity_id,billable) values (' . $p_id . ','
										. $activ[1] . ",'N')",__LINE__,__FILE__);
					}
				}

				if (is_array($values['bill_activities']))
				{
					while($activ=each($values['bill_activities']))
					{
						$this->db->query('insert into phpgw_p_projectactivities (project_id,activity_id,billable) values (' . $p_id . ','
										. $activ[1] . ",'Y')",__LINE__,__FILE__);
					}
				}
				return $p_id;
			}
			return False;
		}

		function subs($parent,&$subs,&$main)
		{
			if (!is_array($main))
			{
				$this->db->query('SELECT * from phpgw_p_projects WHERE main=' . $main,__LINE__,__FILE__);
				$main = $this->db2projects();
				//echo "main: "; _debug_array($main);
			}
			reset($main);
			for ($n = 0; $n < count($main); $n++)
			{
				$pro = $main[$n];
				if ($pro['parent'] == $parent)
				{
					//echo "Adding($pro[project_id])<br>";
					$subs[$pro['project_id']] = $pro;
					$this->subs($pro['project_id'],$pro,$main);
				}
			}
		}

		function reparent($values)
		{
			$id = $values['project_id'];
			$parent = $values['parent'];
			$old_parent = $values['old_parent'];
			$main = $old_parent ? intval($this->id2item(array('item_id' => $old_parent))) : $id;
			//echo "<p>reparent: $id/$main: $old_parent --> $parent</p>\n";

			$subs = array();
			$this->subs($id,$subs,$main);
         //echo "<p>subs($id) = "; _debug_array($subs);

			if (isset($subs[$parent]))
			{
				//echo "<p>new parent $parent is sub of $id</p>\n";
				$parent = $subs[$parent];
				$parent['old_parent'] = $parent['parent'];
				$parent['parent'] = intval($values['old_parent']);
				$this->reparent($parent);

				unset($parent['old_parent']);
				unset($parent['main']);

				$this->edit_project($parent);
				$this->reparent($values);
				return;
			}

			$new_main = $parent ? $this->id2item(array('item_id' => $parent)) : $id;
			$new_parent_level = $parent ? $this->id2item(array('item_id' => $parent,'item' => 'level')) : -1;
			$old_parent_level = $old_parent ? $this->id2item(array('item_id' => $old_parent,'item' => 'level')) : -1;
			$level_adj = $old_parent_level - $new_parent_level;
			reset($subs);
         //echo "new_main=$new_main,level_adj = $level_adj<br>";
			while (list($n) = each($subs))
			{
				$subs[$n]['main'] = $new_main;
				$subs[$n]['level'] -= $level_adj;
				//echo "<p>$n: id=".$subs[$n]['project_id']." set main to $new_main, subs[$n] = \n"; _debug_array($subs[$n]);
				$this->edit_project($subs[$n]);
			}
		}
		
		/**
		 * Creates or updates a project, shared code from add_project and edit_project
		 *
		 * @internal
		 * @param array &$values
		 * @param int (new) project-id
		 */
		function _add_update_project(&$values)
		{
			$data = array(
					'access'	=> isset($values['access']) ? $values['access'] : 'public',
					'entry_date'	=> time(),
					'start_date'	=> $values['sdate'],
					'end_date'	=> (int)$values['edate'],
					'coordinator'	=> $values['coordinator'],
					'customer'	=> $values['customer'],
					'status'	=> $values['status'],
					'descr'		=> $values['descr'],
					'title'		=> $values['title'],
					'p_number'	=> $values['number'],
					'time_planned'	=> $values['ptime'],
					'processor'	=> $this->account,
					'investment_nr'	=> $values['investment_nr'],
					'inv_method'	=> $values['inv_method'],
					'parent'	=> $values['parent'],
					'previous'	=> $values['previous'],
					'customer_nr'	=> $values['customer_nr'],
					'url'		=> $values['url'],
					'reference'	=> $values['reference'],
					'result'	=> $values['result'],
					'test'		=> $values['test'],
					'quality'	=> $values['quality'],
					'accounting'	=> $values['accounting'],
					'acc_factor'	=> $values['project_accounting_factor'],
					'acc_factor_d'	=> $values['project_accounting_factor_d'],
					'billable'	=> $values['billable'] ? 'N' : 'Y',
					'discount_type'	=> $values['discount_type'],
					'psdate'	=> $values['psdate'],
					'pedate'	=> (int)$values['pedate'],
					'priority'	=> $values['priority'],
					'e_budget'	=> $values['e_budget'],
					'discount'	=> $values['discount'],
				);
			if(isset($values['main']))	$data['main']		= $values['main'];
			if(isset($values['level']))	$data['level']		= $values['level'];
			if(isset($values['cat']))	$data['category']	= $values['cat'];
				
			if (!(int) $values['project_id'])
			{
				$this->db->insert($this->project_table,$data,False,__LINE__,__FILE__);
				
				$values['project_id'] = $this->db->get_last_insert_id($this->project_table,'project_id');
			}
			else
			{
				$this->db->update($this->project_table,$data,array(
						'project_id' => $values['project_id']
					),__LINE__,__FILE__);
			}
			return $values['project_id'];
		}			

		/**
		* @return unknown
		* @param unknown $values
		* @desc modifies a already existing project
		*/
		function edit_project($values)
		{
			$values['project_id'] = intval($values['project_id']);
			
			if($values['project_id'] == 0)
				return false;

			if (is_array($values['book_activities']))
			{
				$this->db2->query('delete from phpgw_p_projectactivities where project_id=' . $values['project_id']
								. " and billable='N'",__LINE__,__FILE__);

				while($activ=each($values['book_activities']))
				{
					$this->db->query('insert into phpgw_p_projectactivities (project_id, activity_id, billable) values (' . $values['project_id']
									. ',' . $activ[1] . ",'N')",__LINE__,__FILE__);
				}
			}

			if (is_array($values['bill_activities']))
			{
				$this->db2->query('delete from phpgw_p_projectactivities where project_id=' . $values['project_id']
								. " and billable='Y'",__LINE__,__FILE__);

				while($activ=each($values['bill_activities']))
				{
					$this->db->query('insert into phpgw_p_projectactivities (project_id, activity_id, billable) values (' . $values['project_id']
									. ',' . $activ[1] . ",'Y')",__LINE__,__FILE__);
				}
			}

			$values['e_budget']			= $values['e_budget'] + 0.0;
			$values['discount']			= $values['discount'] + 0.0;
			$values['project_accounting_factor'] = $values['project_accounting_factor'] + 0.0;
			$values['project_accounting_factor_d'] = $values['project_accounting_factor_d'] + 0.0;

			if (isset($values['old_parent']) && $values['old_parent'] != $values['parent'])
			{
				$this->reparent($values);
			}
			if (!isset($values['main']) || !isset($values['level']))
			{
				if ($values['parent'] > 0)
				{
					$values['main']		= intval($this->id2item(array('item_id' => $values['parent'],'item' => 'main')));
					$values['level']	= intval($this->id2item(array('item_id' => $values['parent'],'item' => 'level'))+1);
				}
				else
				{
					$values['main'] = $values['project_id'];
				}
			}

			$this->_add_update_project($values);

			if ($values['status'] == 'archive')
			{
				$this->db->update($this->project_table,array(
						'status' => 'archive',
					),array(
						'parent' => $values['project_id'],
					),__LINE__,__FILE__);
			}
			
			if($values['oldstatus'] && $values['oldstatus'] == 'archive' && $values['status'] != 'archive')
			{
				$this->db->update($this->project_table,array(
						'status' => $values['status'],
					),array(
						'parent' => $values['project_id'],
					),__LINE__,__FILE__);
			}

			// update budget
			$this->_update_budget($values['project_id'],$values['budget'],$this->db);
			
			$values['old_edate'] = intval($values['old_edate']);
			if ($values['old_edate'] > 0 && $values['edate'] > 0 && $values['old_edate'] != $values['edate'])
			{
				$this->db->select($this->project_table,'project_id,title,p_number,start_date,end_date',array(
						'previous' => $values['project_id']
					),__LINE__,__FILE__);

				while($this->db->next_record())
				{
					$following[] = array(
						'project_id'	=> $this->db->f('project_id'),
						'title'			=> $this->db->f('title'),
						'number'		=> $this->db->f('p_number'),
						'sdate'			=> $this->db->f('start_date'),
						'edate'			=> $this->db->f('end_date')
					);
				};

				//_debug_array($following);

				if (is_array($following))
				{
					if ($this->siteconfig['dateprevious'] == 'yes')
					{
						$diff = abs($values['edate']-$values['old_edate']);

						if ($values['old_edate'] > $values['edate'])
						{
							$op = 'sub';
						}
						else
						{
							$op = 'add';
						}
					}
					foreach($following as $key => $fol)
					{
						if ($this->siteconfig['dateprevious'] == 'yes')
						{
							$nsdate = $op=='add'?$fol['sdate']+$diff:$fol['sdate']-$diff;
							$nedate = intval($fol['edate'])>0?($op=='add'?$fol['edate']+$diff:$fol['edate']-$diff):0;
							//$npsdate = intval($fol['psdate'])>0?($op=='add'?$fol['psdate']+$diff:$fol['psdate']-$diff):0;
							//$npedate = intval($fol['pedate'])>0?($op=='add'?$fol['pedate']+$diff:$fol['pedate']-$diff):0;

							$this->db->update($this->project_table,array(
									'start_date'	=> $nsdate,
									'end_date'		=> $nedate,
									'entry_date'	=> time(),
									'processor'		=> $this->account,
								),array(
									'project_id' => $fol['project_id']
								),__LINE__,__FILE__);

							$following[$key]['nsdate'] = $nsdate;
							$following[$key]['nedate'] = $nedate;
						}
						$this->db->query('SELECT s_id,edate,title,description from phpgw_p_mstones WHERE project_id=' . intval($fol['project_id']),__LINE__,__FILE__);

						while($this->db->next_record())
						{
							$stones[] = array
							(
								's_id'		=> $this->db->f('s_id'),
								'edate'		=> $this->db->f('edate'),
								'description'	=> $this->db->f('description'),
								'title'		=> $this->db->f('title')
							);
						};
						$following[$key]['mstones'] = $stones;

						if ($this->siteconfig['dateprevious'] == 'yes' && is_array($stones))
						{
							foreach($stones as $skey => $stone)
							{
								$snedate = $op=='add'?$stone['edate']+$diff:$stone['edate']-$diff;

								$this->db->query('UPDATE phpgw_p_mstones set edate=' . intval($snedate) . ' WHERE s_id=' . intval($stone['s_id']),__LINE__,__FILE__);
								$stones[$skey]['snedate'] = $snedate;
							}
						}
					}
					return $following;
				}
				return False;
			}
		}

		function return_value($action,$pro_id)
		{
			$pro_id = intval($pro_id);
			switch ($action)
			{
				case 'act':
					$this->db->query('SELECT a_number,descr from phpgw_p_activities where id=' . $pro_id,__LINE__,__FILE__);
					if ($this->db->next_record())
					{
						$bla = $GLOBALS['phpgw']->strip_html($this->db->f('descr')) . ' [' . $GLOBALS['phpgw']->strip_html($this->db->f('a_number')) . ']';
					}
					break;
				case 'cost':
					$this->db->query('SELECT cost_id,cost_name from phpgw_p_costs where cost_id=' . $pro_id,__LINE__,__FILE__);
					if ($this->db->next_record())
					{
						$bla = $GLOBALS['phpgw']->strip_html($this->db->f('cost_name'));
					}
					break;
				case 'budget':
				case 'budgetSum':
					$budgetData = $this->getBudget($pro_id);
					$bla = $budgetData[$action];
					break;
				default:
					switch ($action)
					{
						case 'co':			$column = 'coordinator'; break;		
						case 'main':		$column = 'main'; break;
						case 'level':		$column = 'level'; break;
						case 'parent':		$column = 'parent'; break;
						case 'pro':			$column = 'p_number,title'; break;
						case 'edate':		$column = 'end_date'; break;
						case 'sdate':		$column = 'start_date'; break;
						case 'phours':
						case 'ptime':		$column = 'time_planned'; break;	
						case 'invest':		$column = 'investment_nr'; break;
						//case 'budget':		$column = 'budget'; break;
						case 'e_budget':	$column = 'e_budget'; break;
						case 'previous':	$column = 'previous'; break;
						case 'billable':	$column = 'billable'; break;
					}

					$this->db->query('SELECT ' . $column . ' from phpgw_p_projects where project_id=' . $pro_id,__LINE__,__FILE__);
					if ($this->db->next_record())
					{
						switch($action)
						{
							case 'pro':
								$bla = $GLOBALS['phpgw']->strip_html($this->db->f('title')) . ' ['
									. $GLOBALS['phpgw']->strip_html($this->db->f('p_number')) . ']';
								break;
							case 'phours':
								$bla = $this->db->f('time_planned')/60;
								break;
							default:
								$bla = $GLOBALS['phpgw']->strip_html($this->db->f($column));
						}
					}
			}
			return $bla;
		}

		function exists($action, $check = 'number', $num = '', $project_id = '')
		{
			$project_id = intval($project_id);

			switch ($action)
			{
				default:	$p_table = ' phpgw_p_projects'; $column = ' p_number'; break;
			}

			if ($check == 'number')
			{
				if ($project_id > 0)
				{
					$editexists = ' and project_id !=' . $project_id;
				}

				$this->db->query("select count(*) from $p_table where $column='$num'" .  $editexists,__LINE__,__FILE__);
			}

			if ($check == 'par')
			{
				$this->db->query('select count(*) from phpgw_p_projects where parent=' . $project_id,__LINE__,__FILE__);
			}
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

// returns project-,invoice- and delivery-ID

		function add_leading_zero($num)  
		{
/*			if ($id_type == "hex")
			{
				$num = hexdec($num);
				$num++;
				$num = dechex($num);
			}
			else
			{
				$num++;
			} */

			$num++;

			if (strlen($num) == 4)
				$return = $num;
			if (strlen($num) == 3)
				$return = "0$num";
			if (strlen($num) == 2)
				$return = "00$num";
			if (strlen($num) == 1)
				$return = "000$num";
			if (strlen($num) == 0)
				$return = "0001";

			return strtoupper($return);
		}

		function create_projectid()
		{
			$prefix = 'P-' . $this->year . '-';

			$this->db->query("select max(p_number) from phpgw_p_projects where p_number like ('$prefix%') and parent=0");
			$this->db->next_record();
			$max = $this->add_leading_zero(substr($this->db->f(0),-4));

			return $prefix . $max;
		}

		function create_jobid($pro_parent)
		{
			/*$parent_level = $this->id2item(array('project_id' => $pro_parent, 'item' => 'level'));
			switch($parent_level)
			{
				case 0:		$add = ' / '; break;
				default:	$add = ''; break;
			}*/

			$this->db->select($this->project_table,'p_number',array('project_id'=>$pro_parent),__LINE__,__FILE__);
			$this->db->next_record();
			$prefix = $this->db->f('p_number') . '/';

			$this->db->select($this->project_table,'max(p_number)','p_number LIKE '.$this->db->quote($prefix.'%'),__LINE__,__FILE__);
			$this->db->next_record();
			$max = $this->add_leading_zero(substr($this->db->f(0),-4));

			return $prefix . $max;
		}

		function create_activityid()
		{
			$prefix = 'A-' . $this->year . '-';

			$this->db->query("select max(a_number) from phpgw_p_activities where a_number like ('$prefix%')");
			$this->db->next_record();
			$max = $this->add_leading_zero(substr($this->db->f(0),-4));

			return $prefix . $max;
		}

		function create_deliveryid()
		{
			$prefix = 'D-' . $this->year . '-';
			$this->db->query("select max(d_number) from phpgw_p_delivery where d_number like ('$prefix%')");
			$this->db->next_record();
			$max = $this->add_leading_zero(substr($this->db->f(0),-4));

			return $prefix . $max;
		}

		function create_invoiceid()
		{
			$prefix = 'I-' . $this->year . '-';
			$this->db->query("select max(i_number) from phpgw_p_invoice where i_number like ('$prefix%')");
			$this->db->next_record();
			$max = $this->add_leading_zero(substr($this->db->f(0),-4));

			return $prefix . $max;
		}

		function delete_project($project_id, $subs = False)
		{
			$project_id = intval($project_id);

			if ($subs)
			{
				$subdelete = ' OR main =' . $project_id;
			}

			$this->db->query('DELETE from phpgw_p_projects where project_id=' . $project_id . $subdelete,__LINE__,__FILE__);

			if ($subs)
			{
				$subdelete = ' or pro_parent=' . $project_id;
			}

			$this->db->query('DELETE from phpgw_p_hours where project_id=' . $project_id . $subdelete,__LINE__,__FILE__); 

			$this->db->query('select id from phpgw_p_delivery where project_id=' . $project_id,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$del[] = array
				(
					'id'	=> $this->db->f('id')
				);
			}

			if (is_array($del))
			{
				for ($i=0;$i<=count($del);$i++)
				{
					$this->db->query('Delete from phpgw_p_deliverypos where delivery_id=' . intval($del[$i]['id']),__LINE__,__FILE__);
				}
				$this->db->query('DELETE from phpgw_p_delivery where project_id=' . $project_id,__LINE__,__FILE__);
			}

			$this->db->query('select id from phpgw_p_invoice where project_id=' . $project_id,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$inv[] = array
				(
					'id'	=> $this->db->f('id')
				);
			}

			if (is_array($inv))
			{
				for ($i=0;$i<=count($inv);$i++)
				{
					$this->db->query('Delete from phpgw_p_invoicepos where invoice_id=' . intval($inv[$i]['id']),__LINE__,__FILE__);
				}
				$this->db->query('DELETE from phpgw_p_invoice where project_id=' . $project_id,__LINE__,__FILE__);
			}
		}

		function delete_account_project_data($account_id)
		{
			$account_id = intval($account_id);
			if ($account_id > 0)
			{
				$this->db->query('delete from phpgw_categories where cat_owner=' . $account_id . " AND cat_appname='projects'",__LINE__,__FILE__);
				$this->db->query('delete from phpgw_p_hours where employee=' . $account_id,__LINE__,__FILE__);
				$this->db->query('select project_id from phpgw_p_projects where coordinator=' . $account_id,__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					$drop_list[] = $this->db->f('project_id');
				}

				if (is_array($drop_list))
				{
					reset($drop_list);
//					_debug_array($drop_list);
//					exit;

					$subdelete = ' OR parent in (' . implode(',',$drop_list) . ')';

					$this->db->query('DELETE from phpgw_p_projects where project_id in (' . implode(',',$drop_list) . ')'
									. $subdelete,__LINE__,__FILE__);

					$this->db->query('select id from phpgw_p_delivery where project_id in (' . implode(',',$drop_list) . ')',__LINE__,__FILE__);

					while ($this->db->next_record())
					{
						$del[] = array
						(
							'id'	=> $this->db->f('id')
						);
					}

					if (is_array($del))
					{
						for ($i=0;$i<=count($del);$i++)
						{
							$this->db->query('Delete from phpgw_p_deliverypos where delivery_id=' . intval($del[$i]['id']),__LINE__,__FILE__);
						}

						$this->db->query('DELETE from phpgw_p_delivery where project_id in (' . implode(',',$drop_list) . ')',__LINE__,__FILE__);
					}


					$this->db->query('select id from phpgw_p_invoice where project_id in (' . implode(',',$drop_list) . ')',__LINE__,__FILE__);

					while ($this->db->next_record())
					{
						$inv[] = array
						(
							'id'	=> $this->db->f('id')
						);
					}

					if (is_array($inv))
					{
						for ($i=0;$i<=count($inv);$i++)
						{
							$this->db->query('Delete from phpgw_p_invoicepos where invoice_id=' . intval($inv[$i]['id']),__LINE__,__FILE__);
						}

						$this->db->query('DELETE from phpgw_p_invoice where project_id in (' . implode(',',$drop_list) . ')',__LINE__,__FILE__);
					}
				}
			}
		}

		function change_owner($old, $new)
		{
			$old = intval($old);
			$new = intval($new);

			$this->db->query('UPDATE phpgw_p_projects set coordinator=' . $new . ' where coordinator=' . $old,__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_hours set employee=' . $new . ' where employee=' . $old,__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_projectmembers set account_id=' . $new . ' where (account_id=' . $old . " AND type='aa')",__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_invoice set owner=' . $new . ' where owner=' . $old,__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_delivery set owner=' . $new . ' where owner=' . $old,__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_categories set cat_owner=' . $new . ' where cat_owner=' . $old . " AND cat_appname='projects'",__LINE__,__FILE__);
		}


// -------- SUM BUDGET ---------------

		function sum_budget($values)
		{
			//$action		= $values['action']?$values['action']:'mains';
			//$bcolumn	= $values['bcolumn']?$values['bcolumn']:'budget';
			//$project_id	= intval($values['project_id']);

			$values['column'] = 'project_id,level';

			$projects = $this->read_projects($values);

			//_debug_array($projects);

			if(!count($projects))	// no projects found
			{
				return 0;
			}
			$pro = array();
			foreach($projects as $project)
			{
				$pro[] = $project['project_id'];
			}

			// RalfBecker: no idea what this $bcolumn business is about, there are no other columns to sum over !!!
			//$sql = 'SELECT SUM(' . $bcolumn . ') as sumvalue from phpgw_p_budget where project_id in(' . implode(',',$pro) . ')';
			
			$this->db->select($this->budget_table,'SUM(budget_amount)',array(
					'project_id' => $pro
				),__LINE__,__FILE__);
			
			return $this->db->next_record() ? $this->db->f(0) : 0;
		}

		function get_planned_value($option)
		{
			$action		= (isset($option['action'])?$option['action']:'main');
			$project_id	= (isset($option['project_id'])?$option['project_id']:0);
			$parent_id	= (isset($option['parent_id'])?$option['parent_id']:0);

			$project_id = intval($project_id);
			$parent_id = intval($parent_id);

			switch($action)
			{
				case 'tmain':
				case 'bmain':	$filter = 'main=' . $parent_id . ' and project_id !=' . $parent_id; break;
				case 'tparent':
				case 'ebparent':
				case 'bparent':	$filter = 'parent=' . $parent_id; break;
			}

			switch($action)
			{
				case 'ebparent': $column = 'e_budget'; break;
				case 'tmain':
				case 'tparent':	$column = 'time_planned'; break;
			}

			if($project_id > 0)
			{
				$editfilter = ' and phpgw_p_projects.project_id !=' . $project_id;
			}

			switch($action)
			{
				case 'bmain':
				case 'bparent':
					$query = "SELECT SUM($this->budget_table.budget_amount) as sumvalue from $this->budget_table,phpgw_p_projects where ( $filter $editfilter  and $this->budget_table.project_id=phpgw_p_projects.project_id)";
					break;
				default:
					$query = 'SELECT SUM(' . $column . ') as sumvalue from phpgw_p_projects where (' . $filter . $editfilter . ')';
					break;
			}
			//print "$query<br>";
			$this->db->query($query ,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f('sumvalue');
			}
		}

		function item2id($data = 0)
		{
			$item_id	= (isset($data['item_id'])?$data['item_id']:'event_id');
			$item		= $data['item'];
			$action		= (isset($data['action'])?$data['action']:'event');

			switch($action)
			{
				case 'event':	$table = 'phpgw_p_events'; $column = 'event_name'; break;
			}

			$this->db->query("SELECT $item_id FROM $table WHERE $column='" . $item . "'",__LINE__,__FILE__);
			$this->db->next_record();

			if ($this->db->f($item_id))
			{
				return $this->db->f(0);
			}
		}

		function id2item($data)
		{
			if(is_array($data))
			{
				$item_id	= intval($data['item_id']);
				$item		= (isset($data['item'])?$data['item']:'main');
				$action		= (isset($data['action'])?$data['action']:'pro');
			}

			switch($action)
			{
				case 'role':	$table = 'phpgw_p_roles'; $column = 'role_id'; break;
				case 'event':	$table = 'phpgw_p_events'; $column = 'event_id'; break;
				default:		$table = 'phpgw_p_projects'; $column = 'project_id'; break;
			}

			$this->db->query("SELECT $item FROM $table WHERE $column=" . $item_id,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f(0);
			}
		}

		function get_mstones($project_id = '')
		{
			$this->db->query("SELECT * FROM phpgw_p_mstones WHERE project_id=" . intval($project_id),__LINE__,__FILE__);

			while($this->db->next_record())
			{
				$stones[] = array
				(
					's_id'		=> $this->db->f('s_id'),
					'title'		=> $this->db->f('title'),
					'description'	=> $this->db->f('description'),
					'edate'		=> $this->db->f('edate')
				);
			}
			return $stones;
		}

		function get_single_mstone($s_id = '')
		{
			$this->db->query("SELECT * FROM phpgw_p_mstones WHERE s_id=" . intval($s_id),__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$stone = array
				(
					's_id'		=> $this->db->f('s_id'),
					'title'		=> $this->db->f('title'),
					'description'	=> $this->db->f('description'),
					'edate'		=> $this->db->f('edate')
				);
			}
			return $stone;
		}

		function add_mstone($values)
		{
			$this->db->query('INSERT into phpgw_p_mstones (project_id,title,description,edate) VALUES (' . intval($values['project_id']) . 
				",'" . $this->db->db_addslashes($values['title']) . "'," . 
				"'" . $this->db->db_addslashes($values['description']) . "'," .
				intval($values['edate']) . ')',
				__LINE__,
				__FILE__);
			return $this->db->get_last_insert_id('phpgw_p_mstones','s_id');
		}

		function edit_mstone($values)
		{
			$this->db->query('UPDATE phpgw_p_mstones set edate=' . intval($values['edate']) . 
				", title='" . $this->db->db_addslashes($values['title']) . "' " . 
				", description='" . $this->db->db_addslashes($values['description']) . "' " . 
				'WHERE s_id=' . intval($values['s_id']),__LINE__,__FILE__);
		}

		function delete_mstone($s_id = '')
		{
			$this->db->query('DELETE from phpgw_p_mstones where s_id=' . intval($s_id),__LINE__,__FILE__);
		}

		function delete_acl($project_id)
		{
			$this->db->query("DELETE from phpgw_acl where acl_appname='projects' AND acl_location='$project_id'"
							. ' AND acl_rights=7',__LINE__,__FILE__);
		}

		function get_acl_projects()
		{
			$this->db->query("SELECT acl_location from phpgw_acl where acl_appname = 'projects' and acl_rights=7 and acl_account="
								. $this->account,__LINE__,__FILE__);

			while($this->db->next_record())
			{
				$projects[] = $this->db->f(0);
			}
			return $projects;
		}

		function get_employee_projects($account_id = '')
		{
			$this->account = intval($account_id);
			$coord = $this->read_projects(array('filter' => 'yours','action' => 'all','limit' => False,'column' => 'title,p_number,level,project_id',
												'order' => 'main'));

			$pros = $this->get_acl_projects();
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$space = '&nbsp;.&nbsp;';
			for($i=0;$i<count($pros);$i++)
			{
				$level = $spaceset = '';
				$level = $this->return_value('level',$pros[$i]);
				
				$pro_name = $this->return_value('pro',$pros[$i]);
				if (!$pro_name) continue;	// project does not exist any more

				if ($level > 0)
				{
					$spaceset = str_repeat($space,$level);
				}

				$pro[] = array
				(
					'pro_name'	=> $spaceset . $pro_name
				);
			};

			if(is_array($coord))
			{
				foreach($coord as $co)
				{
					if(!is_array($pros) || (is_array($pros) && !in_array($co['project_id'],$pros)))
					{
						$spaceset = '';
						if ($co['level'] > 0)
						{
							$spaceset = str_repeat($space,$co['level']);
						}
						$pro[] = array
						(
							'pro_name'	=> $spaceset . $co['title'] . ' [' . $co['p_number'] . ']'
						);
					}
				}	
			}
			return $pro;
		}

		function member($project_id)
		{
			$this->db->query("SELECT acl_account from phpgw_acl where acl_appname = 'projects' and acl_rights=7 and acl_location='"
								. intval($project_id)."'",__LINE__,__FILE__);

			while($this->db->next_record())
			{
				$members[] = $this->db->f(0);
			}

			if (is_array($members) && in_array($this->account,$members))
			{
				return True;
			}
			return False;
		}

		function read_employee_roles($data)
		{
			$project_id = intval($data['project_id']);
			$column		= isset($data['column'])?$data['column']:'*';
			$account_id	= intval($data['account_id']);
			$event_type	= $data['event_type']?$data['event_type']:'';

			//echo 'SOPROJECTS->read->employee_roles: DATA ';
			//_debug_array($data);

			if($account_id > 0)
			{
				$emp_select = ' and account_id=' . $account_id;
			}

			$this->db->query('SELECT * from phpgw_p_projectmembers where project_id=' . $project_id . " and type='role'" . $emp_select,__LINE__,__FILE__);

			while($this->db->next_record())
			{
				if($column != '*')
				{
					$roles = $this->db->f($column);
				}
				else
				{
					$roles[] = array
					(
						'r_id'			=> $this->db->f('id'),
						'account_id'	=> $this->db->f('account_id'),
						'role_id'		=> $this->db->f('role_id'),
						'events'		=> explode(',',$this->db->f('events'))
					);
				}
			}

			if($event_type && $event_type != '')
			{
				//echo 'event_type: ' . $event_type;

				if(is_string($event_type))
				{
					$event_type = explode(',',$event_type);
				}

				for($i=0;$i<=count($event_type);$i++)
				{
					$event_id = $this->item2id(array('item' => $event_type[$i]));

					for ($k=0;$k<=count($roles);$k++)
					{
						if(is_array($roles[$k]['events']) && in_array($event_id,$roles[$k]['events']))
						{
							$eroles[] = array
							(
								'r_id'			=> $roles[$k]['r_id'],
								'account_id'	=> $roles[$k]['account_id'],
								'role_id'		=> $roles[$k]['role_id'],
								'events'		=> array($event_id)
							);
						}
					}
				}
				$roles = is_array($eroles)?$eroles:False;
			}

			//echo 'SOPROJECTS->read_employee_roles: ROLES ';
			//_debug_array($roles);
			return $roles;
		}

		function save_employee_role($values,$edit = False)
		{
			if(!$edit)
			{
				$this->db->query('INSERT into phpgw_p_projectmembers (project_id,account_id,type,role_id,events) values(' . intval($values['project_id']) . ','
							. intval($values['account_id']) . ",'role'," . intval($values['role_id']) . ",'"
							. (is_array($values['events'])?implode(',',$values['events']):'') . "')",__LINE__,__FILE__);
			}
			else
			{
				$this->db->query('UPDATE phpgw_p_projectmembers set role_id=' . intval($values['role_id']) . ",events='" . (is_array($values['events'])?implode(',',$values['events']):'')
								. "' where type='role' and project_id=" . intval($values['project_id']) . ' and account_id=' . intval($values['account_id']) . ' and id='
								. intval($values['r_id']),__LINE__,__FILE__);

			}
		}

		function add_alarm($data = 0)
		{
			$project_id = intval($data['project_id']);
			$action		= isset($data['action'])?$data['action']:'hours';
			$extra		= intval($data['extra']);

			$this->db->query('INSERT into phpgw_p_alarm (project_id,alarm_type,alarm_extra,alarm_send) values(' . $project_id . ",'" . $action . "',"
							. $extra . ',1)',__LINE__,__FILE__);

			return $this->db->get_last_insert_id('phpgw_p_alarm','alarm_id');
		}

		function update_alarm($data)
		{
			$alarm_id	= intval($data['alarm_id']);
			$extra		= intval($data['extra']);
			$send		= isset($data['send'])?$data['send']:'1';

			$this->db->query('UPDATE phpgw_p_alarm set alarm_extra=' . $extra . ", alarm_send='" . $send . "' where alarm_id=" . $alarm_id,__LINE__,__FILE__);
		}

		function drop_alarm($project_id = 0,$action = 'edit')
		{
			$this->db->query('DELETE from phpgw_p_alarm where project_id=' . intval($project_id) . " and alarm_type='" . $action . "'",__LINE__,__FILE__);
		}

		function check_alarm($project_id = 0,$action = 'hours')
		{
			$this->db->query('SELECT * from phpgw_p_alarm where project_id=' . intval($project_id) . " and alarm_send='1' and alarm_type='" . $action . "'",__LINE__,__FILE__);

			if($this->db->next_record())
			{
				return True;
			}
			return False;
		}

		function get_alarm($data)
		{
			$project_id = intval($data['project_id']);
			$action		= isset($data['action'])?$data['action']:'hours';

			$this->db->query('SELECT * from phpgw_p_alarm where project_id=' . intval($project_id) . " and alarm_type='" . $action . "'",__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$alarm = array
				(
					'alarm_id'	=> $this->db->f('alarm_id'),
					'extra'		=> $this->db->f('alarm_extra')
				);
				return $alarm;
			}
			return False;
		}

		function check_employee_alarm($data)
		{
			$employee	= intval($data['employee']);
			$type		= isset($data['type'])?$data['type']:'assignment to role';
			$project_id	= intval($data['project_id']);

			$event_id = $this->soprojects->item2id(array('item' => $type));

			$events = $this->read_employee_roles(array('project_id' => $project_id,'employee' => $employee, 'column' => 'events'));

			if(is_string($events) && $events != '')
			{
				$events = explode(',',$events);
			}

			if(is_array($events) && in_array($event_id,$events))
			{
				return $event_id;
			}
			return False;
		}

		function get_site_config($default = True)
		{
			$this->config = CreateObject('phpgwapi.config','projects');
			$this->config->read_repository();

			if ($this->config->config_data)
			{
				$items = $this->config->config_data;
			}

			if($default)
			{
				$items['hwday']			= isset($items['hwday'])?$items['hwday']:8;
				$items['accounting']	= isset($items['accounting'])?$items['accounting']:'own';
				$items['activity_bill']	= isset($items['activity_bill'])?$items['activity_bill']:'h';
				$items['dateprevious']	= isset($items['dateprevious'])?$items['dateprevious']:'no';
			}
			return $items;
		}
	}
?>
