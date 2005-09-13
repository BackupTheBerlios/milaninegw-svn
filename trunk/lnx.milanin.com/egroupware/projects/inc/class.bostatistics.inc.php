<?php
	/*******************************************************************\
	* eGroupWare - Projects                                             *
	* http://www.egroupware.org                                         *
	* This program is part of the GNU project, see http://www.gnu.org/  *
	*                                                                   *
	* Project Manager                                                   *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* Written by Lars Kneschke [lkneschke@linux-at-work.de]             *
	* -----------------------------------------------                   *
	* Copyright 2000 - 2004 Free Software Foundation, Inc               *
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
	/* $Id: class.bostatistics.inc.php,v 1.18.2.2 2004/11/06 12:15:28 ralfbecker Exp $ */
	/* $Source: /cvsroot/egroupware/projects/inc/class.bostatistics.inc.php,v $ */

	class bostatistics
	{
		var $debug;
		var $start;
		var $query;
		var $order;
		var $sort;
		var $type;

		var $public_functions = array
		(
			'get_userstat_pro'	=> True,
			'get_stat_hours'	=> True,
			'get_userstat_all'	=> True,
			'get_users'			=> True,
			'get_employees'		=> True
		);

		function bostatistics()
		{
			$action			= get_var('action',array('GET'));
			$this->debug		= False;
			$this->sostatistics	= CreateObject('projects.sostatistics');
			$this->boprojects	= CreateObject('projects.boprojects',True,$action);
			$this->displayCharset	= $GLOBALS['phpgw']->translation->charset();
			$this->botranslation	= CreateObject('phpgwapi.translation');

			$this->start		= $this->boprojects->start;
			$this->query		= $this->boprojects->query;
			$this->filter		= $this->boprojects->filter;
			$this->order		= $this->boprojects->order;
			$this->sort			= $this->boprojects->sort;
			$this->cat_id		= $this->boprojects->cat_id;

			$this->date_diff	= 0;
		}

		function get_users($type, $start, $sort, $order, $query)
		{
			$pro_employees = $this->boprojects->read_projects_acl();

			if (!is_array($pro_employees)) return false;
			
			$users = array();
			foreach((array) $pro_employees as $uid)
			{
				$GLOBALS['phpgw']->accounts->get_account_name($uid,$lid,$lastname,$firstname);
				
				if ($query && stristr($lid,$query) == false && stristr($lastname,$query) == false && stristr($firstname,$query) == false)
				{
					continue;
				}
				$users[] = array(
					'account_id'		=> $uid,
					'account_lid'		=> $lid,
					'account_firstname'	=> $firstname,
					'account_lastname'	=> $lastname,
				);
			}
			$this->total_records = count($users);
			
			switch($order)
			{
				default:
					$order = 'account_lid';
					// fall-through
				case 'account_lid':
				case 'account_firstname':
				case 'account_lastname':
					$sign = $sort == 'DESC' ? '-' : '';
					usort($users,create_function('$a,$b',"return $sign"."strcasecmp(\$a['$order'],\$b['$order']);"));
					break;
			}
			$maxmatchs = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			if (!$maxmatchs) $maxmatchs = 12;

			if ($start || $this->total_records > $maxmatchs)
			{
				$users = array_slice($users,(int)$start,$maxmatchs);
			}
			return $users;
		}

		function get_userstat_pro($account_id, $values)
		{
			return $this->sostatistics->user_stat_pro($account_id, $values);
		}

		function get_stat_hours($type, $account_id, $project_id, $values)
		{
			return $this->sostatistics->stat_hours($type, $account_id, $project_id, $values);
		}

		function get_employees($project_id, $values)
		{
			return $this->sostatistics->pro_stat_employees($project_id, $values);
		}
		
		/**
		* creates the image for the gantt chart
		*
		* @param $_params	array containing projectdata, start- and enddate
		* @param $_filename	filename for the image, if empty image gets printed to browser
		* @author	Lars Kneschke / Bettina Gille
		* @returns	nothing - writes image to disk
		*/
		function show_graph($params, $_filename='')
		{
			$modernJPGraph = false;
			
			// no gd support
			if(!function_exists('imagecopyresampled')) 
				return false;
			
			DEFINE("TTF_DIR",PHPGW_SERVER_ROOT."/projects/ttf-bitstream-vera-1.10/");
			if(file_exists(PHPGW_SERVER_ROOT . '/../jpgraph/src/jpgraph.php'))
			{
				include(PHPGW_SERVER_ROOT . '/../jpgraph/src/jpgraph.php');
				include(PHPGW_SERVER_ROOT . '/../jpgraph/src/jpgraph_gantt.php');
			}
			else
			{
				include(PHPGW_SERVER_ROOT . '/projects/inc/jpgraph-1.5.2/src/jpgraph.php');
				include(PHPGW_SERVER_ROOT . '/projects/inc/jpgraph-1.5.2/src/jpgraph_gantt.php');
			}
			//_debug_array($params);
			$project_array	= $params['project_array'];
			$sdate		= $params['sdate'];
			$edate		= $params['edate'];
			$showMilestones	= $params['showMilestones'];
			$showResources	= $params['showResources'];

			$bocalendar	= CreateObject('calendar.bocalendar');
			$this->graph	= CreateObject('phpgwapi.gdgraph',$this->debug);
			$bolink		= CreateObject('infolog.bolink');

			//$this->boprojects->order = 'parent';
			$this->boprojects->limit	= False;
			$this->boprojects->html_output	= False;

			if(is_array($project_array))
			{
				$projects = array();
				foreach($project_array as $pro)
				{
					$project = $this->boprojects->list_projects(array('action' => 'mainsubsorted','project_id' => $pro,'mstones_stat' => True));

					if(is_array($project))
					{
						$i = count($projects);
						for($k=0;$k<count($project);$k++)
						{
							$projects[$i+$k] = $project[$k];
						}
					}
				}
			}

			if(is_array($projects))
			{
				$modernJPGraph = version_compare('1.13',JPG_VERSION);
				
				$sdate = $sdate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$sdateout = $GLOBALS['phpgw']->common->show_date($sdate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				$edate = $edate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$edateout = $GLOBALS['phpgw']->common->show_date($edate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
#				$this->graph->title = lang('Gantt chart from %1 to %2',$sdateout,$edateout);

				// Standard calls to create a new graph
				if($modernJPGraph)
					$graph = new GanttGraph(940,-1,"auto");
				else
					$graph = new GanttGraph(-1,-1,"auto");
				
				$graph->SetShadow();
				$graph->SetBox();
				
				$duration = $edate - $sdate;
				
				if($duration < 5958000)
				{
					$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
					if($modernJPGraph)
						$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAYWNBR);
					else
						$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
				}
				elseif($duration < 13820400)
				{
					$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
					$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HWEEK);
				}
				else
				{
					$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH);
				}
				
				// For the week we choose to show the start date of the week
				// the default is to show week number (according to ISO 8601)
				#$graph->scale->SetDateLocale('de_DE');
				
				// Change the scale font
				$graph->scale->week->SetFont(FF_VERA,FS_NORMAL,8);
				$graph->scale->year->SetFont(FF_VERA,FS_BOLD,10);
				
				// Titles for chart
				$graph->title->Set(lang('project overview'));
				$graph->subtitle->Set(lang('from %1 to %2',$sdateout,$edateout));
				$graph->title->SetFont(FF_VERA,FS_BOLD,12);
				$graph->subtitle->SetFont(FF_VERA,FS_BOLD,10);
				
				// set the start and end date
				// add one day to the end is needed internaly by jpgraph
				$graph->SetDateRange(date('Y-m-d 00:00:00',$sdate), date('Y-m-d',$edate+86400));
				
				foreach($projects as $pro)
				{
					$ptime_pro = $this->boprojects->return_value('ptime',$pro[project_id]);
					$acc = $this->boprojects->get_budget(array('project_id' => $pro[project_id],'ptime' => $ptime_pro));
					if($ptime_pro > 0)
						$finnishedPercent = (100/$ptime_pro)*$acc[uhours_jobs_wminutes];
					else
						$finnishedPercent = 0;
					$previous = '';
					if($pro['previous'] > 0)
					{
						$previous = $this->boprojects->read_single_project($pro['previous']);
						$spro[] = array
						(
							'title'			=> str_repeat(' ',$spro['level']) . '[!]' . $previous['title'],
							'extracolor'		=> 'darkorange',
							'sdate'			=> $previous['sdate'],
							'edate'			=> $previous['edate'],
							'pro_id'		=> $previous['project_id'],
							'f_sdate'		=> $pro['sdate']
						);

						$color_legend['previous'] = array('title'	=> '[!]' . lang('previous project'),
													'extracolor'	=> 'darkorange');
					}
					
					// add a empty row before new project
					if($pro['level'] == 0 && $counter > 0)
						$counter++;
					
					$spro = array
					(
						'title'		=> $pro['title'],
						'sdate'		=> $pro['sdate'],
						'edate'		=> $pro['edate']?$pro['edate']:mktime(0,0,0,date('m'),date('d'),date('Y')),
						'color'		=> $pro['level'],
						'pro_id'	=> $pro['project_id'],
						'previous'	=> $pro['previous']
					);
					
					// convert title to iso-8859-1
					$spro[title] = $this->botranslation->convert(
						$spro[title],
						$this->displayCharset,
						'iso-8859-1');
					
					if($spro[edate] < $sdate)
						continue;
						
					if($spro[edate] > $edate)
						$spro[edate] = $edate;
						
					if($spro[sdate] < $sdate)
						$spro[sdate] = $sdate;

					$bar = new GanttBar($counter,
						$spro[title],
						date('Y-m-d',$spro[sdate]),
						date('Y-m-d',$spro[edate]),
						round($finnishedPercent).'%',
						0.5);
					
					// mark beginn of new project bold
					if($pro['level'] == 0)
					{
						$bar->title->SetFont(FF_VERA,FS_BOLD,9);
						#$bar->title->SetColor("#9999FF");
						$bar->SetPattern(BAND_SOLID,"#9999FF");
					}
					else
					{
						// For illustration lets make each bar be red with yellow diagonal stripes
						$bar->SetPattern(BAND_SOLID,"#ccccFF");
						#$bar->title->SetColor("#ccccFF");
					}
						
					
					// To indicate progress each bar can have a smaller bar within
					// For illustrative purpose just set the progress to 50% for each bar
					$bar->progress->SetHeight(0.2);
					$bar->SetColor('#777777');
					if($finnishedPercent > 100)
					{
						$bar->progress->Set(1);
						#$bar->progress->SetPattern(GANTT_SOLID,"darkred",98);
						$bar->caption->SetColor("red");

					}
					else
					{
						$bar->progress->Set($finnishedPercent/100);
						#$bar->progress->SetPattern(GANTT_SOLID,"darkgreen",98);
					}
					$bar->caption->SetFont(FF_VERA,FS_NORMAL,8);
					// ... and add the bar to the gantt chart
					$graphs['bars'][] = $bar;
					#$graph->Add($bar);
					
					$counter++;

					// check for Resources
					if($showResources == 'true')
					{
						$linkedObjects = $bolink->get_links('projects',$pro[project_id]);
						$projectACL = $this->boprojects->get_acl_for_project($pro[project_id]);
						if(is_array($projectACL))
						// if beginn
						foreach($projectACL as $accountID)
						{
							#_debug_array($projectData);
							$accountData = CreateObject('phpgwapi.accounts',$accountID);
							$accountData->read_repository();
							
							$accountName = $GLOBALS['phpgw']->common->display_fullname
							(
								$accountData->data['account_lid'],
								$accountData->data['firstname'],
								$accountData->data['lastname']
							);
							$calData = Array
							(
								'syear'		=> date('Y',$sdate),
								'smonth'	=> date('m',$sdate),
								'sday'		=> date('d',$sdate),
								'eyear'		=> date('Y',$edate),
								'emonth'	=> date('m',$edate),
								'eday'		=> date('d',$edate),
								'owner'		=> array($accountID)
							);
							$calEntries = $bocalendar->store_to_cache($calData);
							$bocalendar->remove_doubles_in_cache
							(
								date('Y',$sdate).date('m',$sdate).date('d',$sdate),
								date('Y',$edate).date('m',$edate).date('d',$edate)
							);
							$calEntries = $bocalendar->cached_events;
							#_debug_array($calEntries);
							if(is_array($calEntries) && count($calEntries))
							{
								#_debug_array($calEntries);
								foreach($calEntries as $calDayDate => $calDayEntries)
								{
									foreach($calDayEntries as $calDayEntry)
									{
										if ($calDayEntry['recur_type'])
										{
											$bocalendar->set_recur_date($calDayEntry,$calDayDate);
										}
									
										#_debug_array($calDayEntry);
										if (!$bocalendar->rejected_no_show($calDayEntry))
										{
											$startDate = date('Y-m-d H:i:s',mktime
											(
												$calDayEntry['start']['hour'],
												$calDayEntry['start']['min'],
												$calDayEntry['start']['sec'],
												$calDayEntry['start']['month'],
												$calDayEntry['start']['mday'],
												$calDayEntry['start']['year']
											));
											$endDate = date('Y-m-d H:i:s',mktime
											(
												$calDayEntry['end']['hour'],
												$calDayEntry['end']['min'],
												$calDayEntry['end']['sec'],
												$calDayEntry['end']['month'],
												$calDayEntry['end']['mday'],
												$calDayEntry['end']['year']
											));
											#$endDate = $startDate+1000;
											#_debug_array($startDate);
											$bar = new GanttBar($counter,
												str_repeat(' ',$pro['level']+1).$accountName,
												$startDate,
												$endDate,
												'',
												0.5);
											$bar->SetPattern(BAND_SOLID,"#DDDDDD");
											$bar->SetColor('#CCCCCC');
											#$bar->SetShadow(true,"darkgray");
											if (count($projectLinks = $bolink->get_links('calendar',$calDayEntry['id'],'projects')))
											{
												$projectLinks = array_flip($projectLinks);
												#_debug_array($projectLinks);
												if(isset($projectLinks[$pro[project_id]]))
												{
													$bar->SetPattern(BAND_SOLID,"#33FF33");
													$bar->SetColor('#33FF33');
												}
											}
											$graphs['bars'][] = $bar;
											#$graph->Add($bar);
										}
										else
										{
											print "rejected<br>";
										}
									}
								}
								$counter++;
							}
						}
						// if end
					}

					// check for milstones
					if(is_array($pro['mstones']) && $showMilestones == 'true')
					{
						$msColor = "#999999";
						foreach($pro['mstones'] as $ms)
						{
							if($sdate < $ms['edate'] &&  $ms['edate'] <= $edate)
							{
								$ms[title] = $this->botranslation->convert(
									$ms[title],
									$this->displayCharset,
									'iso-8859-1');
								
								$msData = array
								(
									'title'		=> $ms['title'],
									'extracolor'	=> 'yellow',
									'edate'		=> $ms['edate'],
									'pro_id'	=> $pro['project_id']
								);
							
								// Create a milestone mark
								$ms = new MileStone($counter, str_repeat(' ',$pro['level']+1) . lang('Milestone'),date('Y-m-d',$msData['edate']),$msData['title']);
								$ms->caption->SetFont(FF_VERA,FS_NORMAL,8);
								$ms->title->SetFont(FF_VERA,FS_NORMAL,8);
								$ms->mark->SetColor($msColor);
								$ms->mark->SetFillColor('#EEEEEE');
								$graphs['ms'][$counter] = $ms;
							
								// Create a vertical line to emphasize the milestone
								$vl = new GanttVLine(date('Y-m-d',$msData[edate]),'',$msColor,2);
								$vl->SetDayOffset(0.5); // Center the line in the day
								$graphs['vl'][$counter] = $vl;
							
								$counter++;
							}
						}
					}
					
				}

				// add the vertical lines
				if(is_array($graphs['vl']))
				{
					foreach($graphs['vl'] as $graphCounter => $graphPointer)
					{
						$graph->Add($graphPointer);
						
					}
				}

				// add the milestones
				if(is_array($graphs['ms']))
				{
					foreach($graphs['ms'] as $graphCounter => $graphPointer)
					{
						$graph->Add($graphPointer);
					}
				}
				
				// add the resources
				if(is_array($graphs['bars']))
				{
					foreach($graphs['bars'] as $graphCounter => $graphPointer)
					{
						$graph->Add($graphPointer);
					}
				}
				
				#$graph->Stroke(PHPGW_SERVER_ROOT . SEP . 'phpgwapi' . SEP . 'images' . SEP . 'draw_tmp.png');
				$graph->Stroke($_filename);
			}
		}
	}
?>
