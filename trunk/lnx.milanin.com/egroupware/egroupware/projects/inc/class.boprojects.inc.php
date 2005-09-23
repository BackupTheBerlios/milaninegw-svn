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
	/* $Id: class.boprojects.inc.php,v 1.87.2.11 2004/11/15 13:52:46 ralfbecker Exp $ */
	// $Source: /cvsroot/egroupware/projects/inc/class.boprojects.inc.php,v $

	class boprojects
	{
		var $action;
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $cat_id;
		var $status;
		var $html_output;

		var $public_functions = array
		(
			'save_sessiondata'		=> True,
			'cached_accounts'		=> True,
			'list_projects'			=> True,
			'check_perms'			=> True,
			'check_values'			=> True,
			'select_project_list'		=> True,
			'save_project'			=> True,
			'read_single_project'		=> True,
			'delete_pa'			=> True,
			'exists'			=> True,
			'employee_list'			=> True,
			'read_abook'			=> True,
			'read_single_contact'		=> True,
			'return_value'			=> True,
			'change_owner'			=> True
		);

		function boprojects($is_active=False, $action = '')
		{
			$this->soprojects	= CreateObject('projects.soprojects');
			$this->sohours		= CreateObject('projects.soprojecthours');
			$this->soconfig		= $this->soprojects->soconfig;
			$this->contacts		= CreateObject('phpgwapi.contacts');
			$this->cats		= CreateObject('phpgwapi.categories');
			$this->debug		= False;
			$this->siteconfig	= $this->soprojects->siteconfig;

			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->grants			= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->html_output	= True;

			if ($is_active)
			{
				$this->read_sessiondata($action);
				$this->use_session = True;

				$_start		= get_var('start',array('POST','GET'));
				$_query		= get_var('query',array('POST','GET'));
				$_sort		= get_var('sort',array('POST','GET'));
				$_order		= get_var('order',array('POST','GET'));
				$_cat_id	= get_var('cat_id',array('POST','GET'));
				$_filter	= get_var('filter',array('POST','GET'));
				$_status	= get_var('status',array('POST','GET'));
				$_state		= get_var('state',array('POST','GET'));
				$_project_id	= get_var('project_id',array('POST','GET'));

				if((!empty($_start) && is_numeric($_start)) || ($_start == '0') || ($_start == 0))
				{
					if($this->debug) { echo '<br>overriding $start: "' . $this->start . '" now "' . $_start . '"'; }
					$this->start = $_start;
				}

				if((empty($_query) && !empty($this->query)) || (!empty($_query) && eregi('^[a-z_0-9]+$',$_query)))
				{
					$this->query  = $_query;
				}

				if(isset($_status) && !empty($_status))
				{
					switch($_status)
					{
						case 'nonactive':
						case 'active':
						case 'archive':
							$this->status = $_status;
							break;
						default:
							$this->status = 'active';
							break;
					}
				}

				if(isset($_state) && !empty($_state))
				{
					switch($_state)
					{
						case 'all':
						case 'open':
						case 'done':
						case 'billed':
							$this->state = $_state;
							break;
						default:
							$this->state = 'all';
							break;
					}
				}

				if(isset($_cat_id) && !empty($_cat_id) && (is_numeric($_cat_id) || $_cat_id == 'none'))
				{
					$this->cat_id = $_cat_id;
				}

				/*if(isset($_project_id) && !empty($_project_id))
				{
					$this->project_id = $_project_id;
				}*/

				if(is_numeric($_project_id))
				{
					$this->project_id = $_project_id;
				}

				if(isset($_sort) && !empty($_sort))
				{
					if($this->debug)
					{
						echo '<br>overriding $sort: "' . $this->sort . '" now "' . $_sort . '"';
					}
					switch(strtolower($_sort))
					{
						case 'asc':
							$this->sort	= 'ASC';
							break;
						case 'desc':
							$this->sort	= 'DESC';
							break;
						default:
							$this->sort	= 'ASC';
							break;
					}
				}

				if(isset($_order) && !empty($_order) && eregi('^[a-z_0-9]+$',$_order)) 
				{
					if($this->debug)
					{
						echo '<br>overriding $order: "' . $this->order . '" now "' . $_order . '"';
					}
					$this->order  = $_order;
				}

				if(isset($_filter) && !empty($_filter))
				{
					if($this->debug) { echo '<br>overriding $filter: "' . $this->filter . '" now "' . $_filter . '"'; }
					switch($_filter)
					{
						case 'yours':
						case 'private':
						case 'none':
							$this->filter = $_filter;
							break;
						default:
							$this->filter = 'none';
							break;
							
					}
				}
				$this->limit = True;
			}
		}
		
		/**
		* hook to check the workload
		*
		* this hooks gets called by calendar, when a new events gets created
		* we use this function to send a message, when the workload is to high
		*
		* @param _hookValues contains the hook values as array
		* @returns nothing
		*/
		function checkWorkLoad($_hookValues)
		{
			$profileID 	= 1;

			$template	= CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('projects'));
			$bocalendar	= CreateObject('calendar.bocalendar');
			$bolink		= CreateObject('infolog.bolink');
			
			// find all calendar entries for event participants
			$calData = Array
			(
				'syear'		=> $_hookValues['hookValues']['start']['year'],
				'smonth'	=> $_hookValues['hookValues']['start']['month'],
				'sday'		=> $_hookValues['hookValues']['start']['mday'],
				'eyear'		=> $_hookValues['hookValues']['start']['year'],
				'emonth'	=> $_hookValues['hookValues']['start']['month'],
				'eday'		=> $_hookValues['hookValues']['start']['mday'],
				'owner'		=> array_keys($_hookValues['hookValues']['participants'])
			);
			$calEntries = $bocalendar->store_to_cache($calData);
			$dateString = $calData['syear'].sprintf('%02d',$calData['smonth']).sprintf('%02d',$calData['sday']);
			$bocalendar->remove_doubles_in_cache($dateString,$dateString);
			$calEntries = $bocalendar->cached_events;
			if(is_array($calEntries[$dateString]) && count($calEntries[$dateString]))
			{
				foreach($calEntries[$dateString] as $calDayEntry)
				{
					foreach($calDayEntry['participants'] as $participant => $status)
					{
						$eventStartTime = mktime
						(
							$calDayEntry['start']['hour'],
							$calDayEntry['start']['min'],
							$calDayEntry['start']['sec'],
							$calDayEntry['start']['month'],
							$calDayEntry['start']['day'],
							$calDayEntry['start']['year']
						);
						$eventEndTime = mktime
						(
							$calDayEntry['end']['hour'],
							$calDayEntry['end']['min'],
							$calDayEntry['end']['sec'],
							$calDayEntry['end']['month'],
							$calDayEntry['end']['day'],
							$calDayEntry['end']['year']
						);
						$eventDuration = $eventEndTime-$eventStartTime;
						$userData[$participant]['duration'][] = $eventDuration;
						$userData[$participant]['eventID'][] = $calDayEntry['id'];
					}
				}
			}
			#_debug_array($userData);
			
			$mail		= CreateObject('phpgwapi.send');
			
			$mail->IsHTML(true);
			
			$template->set_file(array('email_project_t' => 'email_workload.tpl'));
			$template->set_block('email_project_t','body_text');
			$template->set_block('email_project_t','body_html');

			$mail->From	= 'noreply@';
			$mail->FromName	= 'eGroupWare System';
			$mail->Priority	= '3';
			$mail->Encoding = 'quoted-printable';
			$mail->AddCustomHeader("X-Mailer: Projects for eGroupWare");

			$GLOBALS['phpgw_info']['user']['preferences']['common']['account_display'] = 'all';

			// check if one participant works more then 8 hours
			foreach($userData as $participant => $participantData)
			{
				// participant need to mork more then 8 hours?
				if(array_sum($participantData['duration']) > 28800)
				{
					// check if any event is linked to project
					foreach($participantData['eventID'] as $eventID)
					{
						$links = $bolink->get_links('calendar',$eventID,'projects');
						if(count($links) > 0)
						{
							$mail->ClearAddresses();
							// the event is linked to some project
							// lets inform the project coordinator
							$projectData = $this->read_single_project($links[0]);
							#_debug_array($projectData);
							$coordinator = $projectData['coordinator'];
							$toEMailAddress = $GLOBALS['phpgw']->accounts->id2name($coordinator,'account_email');
							$mail->AddAddress($toEMailAddress);

							$mail->Subject = lang('workload warning for project').': '.
								$projectData['title'];
							$template->set_var('project_list',
								'<pre>'.print_r($projectData,true)."</pre>");
							$template->set_var('lang_workload_warning_for',
								lang('workload warning for project'));
							$template->set_var('lang_project_name',
								$projectData['title']);
							$template->set_var('lang_project_description',
								$projectData['descr']);
								
							$GLOBALS['phpgw']->accounts->get_account_name
							(
								$participant,
								$accountLID,
								$accountFirstName,
								$accountLastName
							);
							$accountDisplayName = $GLOBALS['phpgw']->common->display_fullname
							(
								$accountLID,
								$accountFirstName,
								$accountLastName
							);
							$template->set_var('employee',$accountDisplayName);
							$template->set_var('lang_is_schedules_for',
								lang
								(
									'is scheduled for more then %1 hours on %2',
									'8',
									$calData['syear'].'-'.sprintf('%02d',$calData['smonth']).'-'.sprintf('%02d',$calData['sday'])
								)
							);

							$mail->Body	= $template->fp('out','body_html');
							$mail->AltBody	= $template->fp('out','body_text');
			
							@set_time_limit(120);
							if(!$mail->Send())
							{
								$this->errorInfo = $mail->ErrorInfo;
								return false;
							}
						}
					}
				}
			}
			
		}

		function createHTMLOutput($_action, $_values, $_dataMainProject = array(), 
			$_displayNavbar=true, $_displayFooter=true,
			$_displayOverview=true, $_displayMilestones=true, $_displayFiles=true)
		{
			$template = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
		
			$template->set_file(array('view' => 'view2.tpl'));
			$template->set_block('view','main');
			$template->set_block('view','sub','subhandle');
			$template->set_block('view','accounting_act','acthandle');
			$template->set_block('view','accounting_own','ownhandle');
			$template->set_block('view','accounting_own_project','ownprojecthandle');
			$template->set_block('view','accounting_both','bothhandle');
			$template->set_block('view','nonanonym','nonanonymhandle');
			$template->set_block('view','emplist','emplisthandle');
			$template->set_block('view','navbar','navbarhandle');
			$template->set_block('view','div_overview','div_overviewhandle');
			$template->set_block('view','div_milestone','div_milestonehandle');
			$template->set_block('view','div_files','div_fileshandle');
			$template->set_block('view','overview_footer','overview_footerhandle');

			$template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$prefs = $this->read_prefs();

			#$values = $this->read_single_project($project_id);

			//_debug_array($values);
			
			if($_displayNavbar== true)
				$template->parse('navbar_placeholder','navbar',True);

			if ($_action == 'mains' || $action == 'amains')
			{
				$template->set_var('cat',$this->cats->id2name($_values['cat']));
				$template->set_var('pcosts',$_values['pcosts']);
			}
			else if($_action == 'subs')
			{
				$main = $_dataMainProject;

				$template->set_var('cat',$this->cats->id2name($main['cat']));
				$template->set_var('pcosts',$main['pcosts']);
				$template->set_var('lang_number',lang('Job ID'));

				$link_data['project_id'] = $_values['parent'];
				$template->set_var('pro_parent',$this->return_value('pro',$_values['parent']));	
				$template->set_var('parent_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action='
					. ($_values['main']==$_values['parent']?'mains':'subs') . '&project_id='
					. $_values['parent'] . '&pro_main=' . $_values['main']));

				$template->set_var('pro_main',$this->return_value('pro',$_values['main']));
				$template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php','menuaction=projects.uiprojects.view_project&action=mains&project_id='
					. $_values['main']));
				$template->set_var('previous',$this->return_value('pro',$_values['previous']));
				$template->fp('subhandle','sub',True);
			}

			$template->set_var('investment_nr',($_values['investment_nr']?$_values['investment_nr']:$main['investment_nr']));

			$template->set_var('number',$_values['number']);
			$template->set_var('title',($_values['title']?$_values['title']:'&nbsp;'));
			$template->set_var('descr',($_values['descr']?$_values['descr']:'&nbsp;'));
			$template->set_var('status',lang($_values['status']));
			$template->set_var('access',lang($_values['access']));
			$uiwidgets	= CreateObject('projects.uiwidgets');
			$template->set_var('budget',$uiwidgets->dateSelectBox($_values['budget'],'values[budget]','['.$prefs['currency'].'.c]',true));
			$template->set_var('ebudget',$_values['e_budget']);

			$template->set_var('discount',$_values['discount']);
			$template->set_var('discount_type',$_values['discount_type']=='amount'?$prefs['currency']:'%');

			$template->set_var('inv_method',$_values['inv_method']);

			$template->set_var('reference',$_values['reference']);
			$template->set_var('url',$_values['url']);

			$template->set_var('result',$_values['result']);
			$template->set_var('test',$_values['test']);
			$template->set_var('quality',$_values['quality']);
			$template->set_var('priority',$this->formatted_priority($_values['priority']));

			$template->set_var('currency',$prefs['currency']);

			$month = $this->return_date();
			$template->set_var('month',$month['monthformatted']);

			$template->set_var('ptime',$_values['ptime']);

			$template->set_var('uhours_jobs',$_values['uhours_jobs_all']);

			$template->set_var('sdate',$_values['sdate_formatted']);
			$template->set_var('edate',$_values['edate_formatted']);

			$template->set_var('psdate',$_values['psdate_formatted']);
			$template->set_var('pedate',$_values['pedate_formatted']);

			$template->set_var('udate',$_values['udate_formatted']);
			$template->set_var('cdate',$_values['cdate_formatted']);

//--------- coordinator -------------

			$template->set_var('lang_coordinator',($pro_main?lang('job manager'):lang('Coordinator')));
			$template->set_var('coordinator',$_values['coordinatorout']);
			$template->set_var('owner',$GLOBALS['phpgw']->common->grab_owner_name($_values['owner']));
			$template->set_var('processor',$GLOBALS['phpgw']->common->grab_owner_name($_values['processor']));

// ----------------------------------- customer ------

			$template->set_var('customer',$_values['customerout']);
			$template->set_var('customer_nr',$_values['customer_nr']);

// --------- emps & roles ------------------------------
			$emps = $this->get_employee_roles(array('project_id' => $_values['project_id'],'formatted' => True));

			while (is_array($emps) && list(,$emp) = each($emps))
			{
				$template->set_var('emp_name',$emp['emp_name']);
				$template->set_var('events',$emp['events']);
				$template->set_var('role_name',$emp['role_name']);
				$template->fp('emplisthandle','emplist',True);
			}

			if (!isset($public_view))
			{
				if($this->siteconfig['accounting'] == 'own')
				{
					$template->set_var('accounting_factor',$_values['billable']=='Y'?($_values['accounting']=='employee'?lang('factor employee'):lang('factor project')):lang('not billable'));
					if ($_values['billable'] == 'N' || $_values['accounting']=='employee')
					{
						$template->fp('accounting_settings','accounting_own',True);
					}
					else
					{
						$template->set_var('project_accounting_factor',$_values['project_accounting_factor']);
						$template->set_var('project_accounting_factor_d',$_values['project_accounting_factor_d']);
						$template->fp('accounting_settings','accounting_own_project',True);
					}
				}
				else
				{
// ------------ activites bookable ----------------------
					$boact = $this->activities_list($_values['project_id'],False);
					if (is_array($boact))
					{
						while (list($null,$bo) = each($boact))
						{
							$boact_list .=	$bo['descr'] . ' [' . $bo['num'] . ']' . '<br>';
						}
					}

					$template->set_var('book_activities_list',$boact_list);
// -------------- activities billable ---------------------- 

					$billact = $this->activities_list($_values['project_id'],True);
					if (is_array($billact))
					{
						while (list($null,$bill) = each($billact))
						{
							$billact_list .=	$bill['descr'] . ' [' . $bill['num'] . ']' . "\n";
						}
					}
					$template->set_var('bill_activities_list',$billact_list);
					$template->fp('accounting_settings','accounting_act',True);
				}
				$template->fp('accounting_2settings','accounting_both',True);
				$template->fp('nonanonymhandle','nonanonym',True);

				if ($this->edit_perms(array('action' => $action,
							'coordinator' => $_values['coordinator'],
							'main' => $_values['main'],
							'parent' => $_values['parent']))
					&& $_displayFooter == true
				)
				{
					$template->set_var('edit_button','<input type="submit" name="edit" value="' . lang('edit') .'">');
					$template->set_var('edit_milestones_button','<input type="submit" name="mstone" value="' . lang('edit milestones') .'">');
					$template->set_var('edit_roles_events_button','<input type="submit" name="roles" value="' . lang('edit roles and events') .'">');
					$template->parse('overview_footer_placeholder','overview_footer',True);
				}
			}
			$template->set_var('ownhandle','');
			$template->set_var('acthandle','');
			$template->set_var('bothhandle','');

			// the milestones part
			$uiwidgets	= CreateObject('projects.uiwidgets');

			$mstones = $this->get_mstones($_values['project_id']);

			if(is_array($mstones))
			{
				while(list(,$ms) = each($mstones))
				{
					#_debug_array($ms);
					$template->set_var('s_title',$ms['title']);
					$template->set_var('s_edateout',$this->formatted_edate($ms['edate']));
					$rowID = $uiwidgets->tableViewAddRow();
					if($ms['description'])
					{
						$uiwidgets->tableViewAddTextCell($rowID,'<b>'.$ms['title'].'</b><br>'.$ms['description']);
					}
					else
					{
						$uiwidgets->tableViewAddTextCell($rowID,'<b>'.$ms['title'].'</b>');
					}
					$uiwidgets->tableViewAddTextCell($rowID,$this->formatted_edate($ms['edate']),'center');
				}
				$headValues = array(lang('title'),lang('Date due'));
				$template->set_var('milestones_table',$uiwidgets->tableView($headValues));
			}
			unset($uiwidgets);
			
			// the file manager part
			$uiwidgets	= CreateObject('projects.uiwidgets');
			$bolink		= CreateObject('infolog.bolink');
			
			$headValues = array(lang('name'),lang('size'));
			$attachedFiles = $bolink->get_links('projects',$_values['project_id'],'file');

			if(is_array($attachedFiles))
			{
				foreach($attachedFiles as $fileData)
				{
					$fileLinkData = array
					(
						'menuaction'	=> 'infolog.bolink.get_file',
						'app'		=> 'projects',
						'id'		=> $_values['project_id'],
						'filename'	=> $fileData['id']
					);
					$rowID = $uiwidgets->tableViewAddRow();
					$uiwidgets->tableViewAddTextCell($rowID,'<a href="'.
						$GLOBALS['phpgw']->link('/index.php',$fileLinkData).'">'.
						$fileData['id'].'</a>');
					$uiwidgets->tableViewAddTextCell($rowID,$fileData['size'],'center');
				}
			}
			
			$template->set_var('files_table',$uiwidgets->tableView($headValues));

			$template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

			$template->set_var('lang_category',lang('Category'));
			$template->set_var('lang_select',lang('Select'));
			$template->set_var('lang_select_category',lang('Select category'));

			$template->set_var('lang_descr',lang('Description'));
			$template->set_var('lang_title',lang('Title'));
			$template->set_var('lang_none',lang('None'));
			$template->set_var('lang_number',lang('Project ID'));

			$template->set_var('lang_start_date',lang('Start Date'));
			$template->set_var('lang_date_due',lang('Date due'));
			$template->set_var('lang_cdate',lang('Date created'));
			$template->set_var('lang_last_update',lang('last update'));

			$template->set_var('lang_start_date_planned',lang('start date planned'));
			$template->set_var('lang_date_due_planned',lang('date due planned'));

			$template->set_var('lang_access',lang('access'));
			$template->set_var('lang_projects',lang('Projects'));
			$template->set_var('lang_project',lang('Project'));

			$template->set_var('lang_ttracker',lang('time tracker'));
			$template->set_var('lang_statistics',lang('Statistics'));
			$template->set_var('lang_roles',lang('roles'));
			$template->set_var('lang_role',lang('role'));

			$template->set_var('lang_jobs',lang('Jobs'));
			$template->set_var('lang_act_number',lang('Activity ID'));
			$template->set_var('lang_title',lang('Title'));
			$template->set_var('lang_status',lang('Status'));
			$template->set_var('lang_budget',lang('Budget'));

			$template->set_var('lang_investment_nr',lang('investment nr'));
			$template->set_var('lang_customer',lang('Customer'));
			$template->set_var('lang_coordinator',lang('Coordinator'));
			$template->set_var('lang_employees',lang('Employees'));
			$template->set_var('lang_creator',lang('creator'));
			$template->set_var('lang_processor',lang('processor'));
			$template->set_var('lang_previous',lang('previous project'));
			$template->set_var('lang_bookable_activities',lang('Bookable activities'));
			$template->set_var('lang_billable_activities',lang('Billable activities'));
			$template->set_var('lang_edit',lang('edit'));
			$template->set_var('lang_view',lang('View'));
			$template->set_var('lang_hours',lang('Work hours'));
			$template->set_var('lang_remarkreq',lang('Remark required'));

			$template->set_var('lang_customer_nr',lang('customer nr'));
			$template->set_var('lang_url',lang('project url'));
			$template->set_var('lang_reference',lang('external reference'));

			$template->set_var('lang_stats',lang('Statistics'));
			$template->set_var('lang_ptime',lang('time planned'));
			$template->set_var('lang_utime',lang('time used'));
			$template->set_var('lang_month',lang('month'));

			$template->set_var('lang_done',lang('done'));
			$template->set_var('lang_save',lang('save'));
			$template->set_var('lang_apply',lang('apply'));
			$template->set_var('lang_cancel',lang('cancel'));
			$template->set_var('lang_search',lang('search'));
			$template->set_var('lang_delete',lang('delete'));
			$template->set_var('lang_back',lang('back'));

			$template->set_var('lang_parent',lang('Parent project'));
			$template->set_var('lang_main',lang('Main project'));

			$template->set_var('lang_add_milestone',lang('add milestone'));
			$template->set_var('lang_milestones',lang('milestones'));

			$template->set_var('lang_result',lang('result'));
			$template->set_var('lang_test',lang('test'));
			$template->set_var('lang_quality',lang('quality check'));

			$template->set_var('lang_accounting',lang('accounting system'));
			$template->set_var('lang_factor_project',lang('factor project'));
			$template->set_var('lang_factor_employee',lang('factor employee'));
			$template->set_var('lang_accounting_factor_for_project',lang('accounting factor for project'));
			$template->set_var('lang_select_factor',lang('select factor'));
			$template->set_var('lang_non_billable',lang('not billable'));

			$template->set_var('lang_pbudget',lang('budget planned'));
			$template->set_var('lang_ubudget',lang('budget used'));
			$template->set_var('lang_plus_jobs',lang('+ jobs'));

			$template->set_var('lang_per_hour',lang('per hour'));
			$template->set_var('lang_per_day',lang('per day'));

			$template->set_var('lang_percent',lang('percent'));
			$template->set_var('lang_amount',lang('amount'));

			$template->set_var('lang_events',lang('events'));
			$template->set_var('lang_priority',lang('priority'));

			$template->set_var('lang_available',lang('available'));
			$template->set_var('lang_used_billable',lang('used billable'));
			$template->set_var('lang_planned',lang('planned'));
			$template->set_var('lang_used_total',lang('used total'));

			$template->set_var('lang_invoicing_method',lang('invoicing method'));
			$template->set_var('lang_discount',lang('discount'));
			$template->set_var('lang_extra_budget',lang('extra budget'));

			$template->set_var('lang_billable',lang('billable'));
			$template->set_var('lang_send',lang('send'));
			$template->set_var('lang_project_overview',lang('Project overview'));
			$template->set_var('lang_milestones',lang('Milestones'));
			$template->set_var('lang_files',lang('Files'));
			$template->set_var('lang_add',lang('add file'));
			$template->set_var('lang_delete_selected',lang('delete selected files'));

			if($_displayOverview == true)
				$template->parse('div_placeholder','div_overview',True);
			if($_displayMilestones == true)
				$template->parse('div_placeholder','div_milestone',True);
			if($_displayFiles == true)
				$template->parse('div_placeholder','div_files',True);

			return $template->fp('out','main');

		}

		function exportProjectEMail($_pro_main,$_emailTo)
		{
			if(!$_pro_main || !$_emailTo)
				return false;

			$mainProjectData	= $this->read_single_project($_pro_main);
			$subProjectsData	= $this->list_projects(array('action' => 'subs','parent' => $_pro_main));
			$prefs = $this->read_prefs();
			$nextmatchs 		= CreateObject('phpgwapi.nextmatchs');
			
			$bostatistics	= CreateObject('projects.bostatistics');
			$template	= CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			
			$mail		= CreateObject('phpgwapi.send');

			$mail->IsHTML(true);
			
			$template->set_file(array('email_project_t' => 'export_email_body.tpl'));
			$template->set_block('email_project_t','body_text');
			$template->set_block('email_project_t','body_html');
			
			// create the html body
			$template->set_var('title_main',$mainProjectData['title']);
			$template->set_var('coordinator_main',$mainProjectData['coordinatorout']);
			$template->set_var('number_main',$mainProjectData['number']);
			$template->set_var('customer_main',$mainProjectData['customerout']);
			$template->set_var('url_main',$mainProjectData['url']);

			$htmlBody = $this->createHTMLOutput('mains',$mainProjectData,'',false, false,true,true,false);
			
			if(is_array($subProjectsData))
			{
				foreach($subProjectsData as $singelSubProjectData)
				{
					$htmlBody .= $this->createHTMLOutput('subs',
						$singelSubProjectData, $mainProjectData, false,
						false,true,true,false);
				}
			}
			$template->set_var('project_list',$htmlBody);

			// translations
			$template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			
			$template->set_var('lang_main',lang('Main project'));
			$template->set_var('lang_number',lang('Project ID'));
			$template->set_var('lang_url',lang('project url'));
			$template->set_var('lang_coordinator',lang('Coordinator'));
			$template->set_var('lang_customer',lang('Customer'));
			$template->set_var('lang_enable_html',lang('Please enable HTML view of EMails.'));
			
			// create project overview
			#$GLOBALS['phpgw_info']['server']['temp_dir'] . SEP .
			
			if($tempFileName = tempnam($GLOBALS['phpgw_info']['server']['temp_dir'],'project'))
			{
				$start = $mainProjectData[sdate];
				$end = $mainProjectData[edate];
				$bostatistics->show_graph
				(
					array
					(
						'project_array'         => array($_pro_main),
						'sdate'                 => $start?$start:mktime(12,0,0,date('m'),date('d'),date('Y')),
						'edate'                 => $end?$end:mktime(12,0,0,date('m'),date('d')+30,date('Y')),
						'showMilestones'        => true,
						'showResources'         => true
					),
					$tempFileName
				);
				if(filesize($tempFileName) > 0)
				{
					 $mail->AddAttachment($tempFileName,'project_overview.png','base64','image/png');
				}
			}
			
			$mail->From	= $GLOBALS['phpgw_info']['user']['email'];
			$mail->FromName	= $GLOBALS['phpgw_info']['user']['fullname'];
			$mail->Priority	= '3';
			$mail->Encoding = 'quoted-printable';
			$mail->CharSet  = $GLOBALS['phpgw']->translation->charset();
			$mail->AddCustomHeader("X-Mailer: Projects for eGroupWare");
			#if(isset($emailSettings['organizationName']))
			#	$mail->AddCustomHeader("Organization: ".$emailSettings['organizationName']);
				
			$mail->AddAddress($_emailTo);
			
			$mail->Subject = lang('Project Overview').': '.
						$mainProjectData['title'].' '.
						$GLOBALS['phpgw']->common->show_date();
			
			$mail->Body	= $template->fp('out','body_html');
			$mail->AltBody	= $template->fp('out','body_text');
			
			@set_time_limit(120);
			if(!$mail->Send())
			{
				$this->errorInfo = $mail->ErrorInfo;
				return false;
			}
			if($tempFileName)
			{
				unlink($tempFileName);
			}
		}
		

		function exportProjectPDF2($_pro_main)
		{
			if(!$_pro_main)
				return false;

			$botranslation 		= CreateObject('phpgwapi.translation');
			$pdf			= CreateObject('phpgwapi.pdf');
			$bostatistics		= CreateObject('projects.bostatistics');
			
			$mainProjectData	= $this->read_single_project($_pro_main);
			$subProjectsData	= $this->list_projects(array('action' => 'subs','parent' => $_pro_main));
			$prefs 			= $this->read_prefs();
			
			if($mainProjectData['customerout'] == '&nbsp;') $mainProjectData['customerout'] = '';
			
			#_debug_array($subProjectsData);exit;
			
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->SetFont('Arial','',10);

			// main project name row
			$content = lang('Main project').': '.$mainProjectData['title'];
			$pdf->Cell(0,7,$content,B);
			$pdf->Ln();
			
			$pdf->SetFont('Arial','',8);
			
			// row1
			$content = lang('Project ID').':';
			$pdf->Cell(30,5,$content,0);

			$content = $mainProjectData['number'];
			$pdf->Cell(65,5,$content,0);

			$content = lang('project url').':';
			$pdf->Cell(30,5,$content,0);

			if($mainProjectData['url'])
			{
				$content = 'http://'.$mainProjectData['url'];
				$pdf->Cell(65,5,$content,0);
			}
			$pdf->Ln();
			
			// row2
			$content = lang('Coordinator').':';
			$pdf->Cell(30,5,$content,0);

			$content = $mainProjectData['coordinatorout'];
			$pdf->Cell(65,5,$content,0);

			$content = lang('Customer').':';
			$pdf->Cell(30,5,$content,0);
			
			$content = $mainProjectData['customerout'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			// main project data
			$content = lang('investment nr').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($mainProjectData['investment_nr'],False,'iso-8859-1');
			$pdf->MultiCell(65,5,$content,0);
			
			$content = lang('previous project').':';
			$pdf->Cell(30,5,$content,0);
			$pdf->Ln();

			$content = lang('Category').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($this->cats->id2name($mainProjectData['cat']),False,'iso-8859-1');
			$pdf->MultiCell(65,5,$content,0);
			$pdf->Ln();

			$content = lang('Description').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($mainProjectData['descr'],False,'iso-8859-1');
			$pdf->MultiCell(160,5,$content,0);
			$pdf->Ln();

			$content = lang('Status').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert(lang($mainProjectData['status']),False,'iso-8859-1');
			$pdf->Cell(65,5,$content,0);
			$content = lang('access').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert(lang($mainProjectData['access']),False,'iso-8859-1');
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('priority'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['priority'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('project url'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			if($mainProjectData['url'])
				$content = 'http://'.$mainProjectData['url'];
			else
				$content = '';
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('external reference'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			if($mainProjectData['reference'])
				$content = 'http://'.$mainProjectData['reference'];
			else
				$content = '';
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('start date planned'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['psdate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('date due planned'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['pedate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Start Date'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['sdate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('Date due'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['edate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('creator'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $GLOBALS['phpgw']->common->grab_owner_name($mainProjectData['owner']);
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('Date created'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['cdate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('processor'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $GLOBALS['phpgw']->common->grab_owner_name($mainProjectData['processor']);
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('last update'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['udate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Customer'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['customerout'];
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('customer nr'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['customer_nr'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Coordinator'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['coordinatorout'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();
			
			// employes and roles
			$emps = $this->get_employee_roles(array('project_id' => $_pro_main,'formatted' => False));
			
			$content = $botranslation->convert(lang('Employees'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$pdf->Cell(50,5,'',0);
			$content = $botranslation->convert(lang('role'),False,'iso-8859-1');
			$pdf->Cell(50,5,$content,0,0,'C');
			$content = $botranslation->convert(lang('events'),False,'iso-8859-1');
			$pdf->Cell(60,5,$content,0,0,'C');
			$pdf->Ln();
			if(is_array($emps))
			{
				foreach($emps as $employee)
				{
					$displayEvent = '';
					if(is_array($employee['eventNames']))
					{
						foreach($employee['eventNames'] as $eventName)
						{
							$displayEvent .= "$eventName\n";
						}
					}
					$pdf->Cell(30,5,'',0);
					$content = $botranslation->convert($employee['emp_name'],False,'iso-8859-1');
					$pdf->MultiCell(50,5,$content,0);
					$content = $botranslation->convert($employee['role_name'],False,'iso-8859-1');
					$pdf->MultiCell(50,5,$content,0);
					$content = $botranslation->convert($displayEvent,False,'iso-8859-1');
					$pdf->MultiCell(60,5,$content,0);
					$pdf->Ln();
				}
			}

			$content = $botranslation->convert(lang('time planned'),False,'iso-8859-1').':'
				.$botranslation->convert(lang('Work hours'),False,'iso-8859-1');
			$pdf->Cell(50,5,$content,0);
			$content = $mainProjectData['ptime'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Budget'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert(lang('year'),False,'iso-8859-1');
			$pdf->Cell(20,5,$content,0,0,'C');
			$content = $botranslation->convert(lang('month'),False,'iso-8859-1');
			$pdf->Cell(20,5,$content,0,0,'C');
			$content = $botranslation->convert(lang('budget'),False,'iso-8859-1');
			$pdf->Cell(25,5,$content,0,0,'C');
			$content = $botranslation->convert(lang('extra budget'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['e_budget'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();
			if(is_array($mainProjectData['budget']))
			{
				foreach($mainProjectData['budget'] as $year => $budgetData)
				{
					foreach($budgetData as $month => $budget)
					{
						if(!$year) $year = "---";
						if(!$month) $month = "---";
						$pdf->Cell(30,5,'',0);
						$pdf->Cell(20,5,$year,0,0,'C');
						$pdf->Cell(20,5,$month,0,0,'C');
						$pdf->Cell(25,5,$budget,0,0,'R');
						$pdf->Ln();
					}
				}
			}

			$content = $botranslation->convert(lang('Bookable activities'),False,'iso-8859-1').':';
			$pdf->Cell(40,5,$content,0);
			$boact = $this->activities_list($_pro_main,False);
			$content = '';
			if (is_array($boact))
			{
				foreach($boact as $activity)
				{
					$content .=  $activity['descr'] . ' [' . $activity['num'] . ']'."\n";
				}
			} 
			$pdf->MultiCell(150,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Billable activities'),False,'iso-8859-1').':';
			$pdf->Cell(40,5,$content,0);
			$billact = $this->activities_list($_pro_main,True);
			$content = '';
			if (is_array($billact))
			{
				foreach($billact as $activity)
				{
					$content .=  $activity['descr'] . ' [' . $activity['num'] . ']'."\n";
				}
			} 
			$pdf->MultiCell(150,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('invoicing method'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($mainProjectData['inv_method'],False,'iso-8859-1');
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('discount'),False,'iso-8859-1').':';
			if($mainProjectData['discount_type'] == 'percent')
				$content .= ' %';
			elseif($mainProjectData['discount_type'] == 'amount')
				$content .= ''; // add currency
			$pdf->Cell(30,5,$content,0);
			$content = $mainProjectData['discount'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('result'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($mainProjectData['result'],False,'iso-8859-1');
			$pdf->MultiCell(160,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('test'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($mainProjectData['test'],False,'iso-8859-1');
			$pdf->MultiCell(160,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('quality check'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($mainProjectData['quality'],False,'iso-8859-1');
			$pdf->MultiCell(160,5,$content,0);
			$pdf->Ln();

			if(is_array($subProjectsData))
			{
				foreach($subProjectsData as $subProjectData)
				{
					$pdf->AddPage();
########################




			// main project name row
			$subProjectData['title'] = str_replace('&nbsp;','',$subProjectData['title']);
			$content = lang('subproject').': '.$botranslation->convert($subProjectData['title'],False,'iso-8859-1');
			$pdf->Cell(0,7,$content,B);
			$pdf->Ln();
			
			$pdf->SetFont('Arial','',8);
			
			// row1
			$content = lang('Project ID').':';
			$pdf->Cell(30,5,$content,0);

			$content = $subProjectData['number'];
			$pdf->Cell(65,5,$content,0);

			$content = lang('project url').':';
			$pdf->Cell(30,5,$content,0);

			if($subProjectData['url'])
			{
				$content = 'http://'.$subProjectData['url'];
				$pdf->Cell(65,5,$content,0);
			}
			$pdf->Ln();
			
			// main project data
			$content = lang('investment nr').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($subProjectData['investment_nr'],False,'iso-8859-1');
			$pdf->MultiCell(65,5,$content,0);
			
			$content = lang('previous project').':';
			$pdf->Cell(30,5,$content,0);
			$pdf->Ln();

			$content = lang('Category').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($this->cats->id2name($subProjectData['cat']),False,'iso-8859-1');
			$pdf->MultiCell(65,5,$content,0);
			$pdf->Ln();

			$content = lang('Description').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($subProjectData['descr'],False,'iso-8859-1');
			$pdf->MultiCell(160,5,$content,0);
			$pdf->Ln();

			$content = lang('Status').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert(lang($subProjectData['status']),False,'iso-8859-1');
			$pdf->Cell(65,5,$content,0);
			$content = lang('access').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert(lang($subProjectData['access']),False,'iso-8859-1');
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('priority'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['priority'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('project url'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			if($subProjectData['url'])
				$content = 'http://'.$subProjectData['url'];
			else
				$content = '';
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('external reference'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			if($subProjectData['reference'])
				$content = 'http://'.$subProjectData['reference'];
			else
				$content = '';
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('start date planned'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['psdate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('date due planned'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['pedate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Start Date'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['sdate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('Date due'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['edate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('creator'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $GLOBALS['phpgw']->common->grab_owner_name($subProjectData['owner']);
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('Date created'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['cdate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('processor'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $GLOBALS['phpgw']->common->grab_owner_name($subProjectData['processor']);
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('last update'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['udate_formatted'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Customer'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($subProjectData['customerout'],False,'iso-8859-1');
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('customer nr'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['customer_nr'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Coordinator'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['coordinatorout'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();
			
			// employes and roles
			$emps = $this->get_employee_roles(array('project_id' => $_pro_main,'formatted' => False));
			
			$content = $botranslation->convert(lang('Employees'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$pdf->Cell(50,5,'',0);
			$content = $botranslation->convert(lang('role'),False,'iso-8859-1');
			$pdf->Cell(50,5,$content,0,0,'C');
			$content = $botranslation->convert(lang('events'),False,'iso-8859-1');
			$pdf->Cell(60,5,$content,0,0,'C');
			$pdf->Ln();
			if(is_array($emps))
			{
				foreach($emps as $employee)
				{
					$displayEvent = '';
					if(is_array($employee['eventNames']))
					{
						foreach($employee['eventNames'] as $eventName)
						{
							$displayEvent .= "$eventName\n";
						}
					}
					$pdf->Cell(30,5,'',0);
					$content = $botranslation->convert($employee['emp_name'],False,'iso-8859-1');
					$pdf->MultiCell(50,5,$content,0);
					$content = $botranslation->convert($employee['role_name'],False,'iso-8859-1');
					$pdf->MultiCell(50,5,$content,0);
					$content = $botranslation->convert($displayEvent,False,'iso-8859-1');
					$pdf->MultiCell(60,5,$content,0);
					$pdf->Ln();
				}
			}

			$content = $botranslation->convert(lang('time planned'),False,'iso-8859-1').':'
				.$botranslation->convert(lang('Work hours'),False,'iso-8859-1');
			$pdf->Cell(50,5,$content,0);
			$content = $subProjectData['ptime'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Budget'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert(lang('year'),False,'iso-8859-1');
			$pdf->Cell(20,5,$content,0,0,'C');
			$content = $botranslation->convert(lang('month'),False,'iso-8859-1');
			$pdf->Cell(20,5,$content,0,0,'C');
			$content = $botranslation->convert(lang('budget'),False,'iso-8859-1');
			$pdf->Cell(25,5,$content,0,0,'C');
			$content = $botranslation->convert(lang('extra budget'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['e_budget'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();
			if(is_array($subProjectData['budget']))
			{
				foreach($subProjectData['budget'] as $year => $budgetData)
				{
					foreach($budgetData as $month => $budget)
					{
						if(!$year) $year = "---";
						if(!$month) $month = "---";
						$pdf->Cell(30,5,'',0);
						$pdf->Cell(20,5,$year,0,0,'C');
						$pdf->Cell(20,5,$month,0,0,'C');
						$pdf->Cell(25,5,$budget,0,0,'R');
						$pdf->Ln();
					}
				}
			}

			$content = $botranslation->convert(lang('Bookable activities'),False,'iso-8859-1').':';
			$pdf->Cell(40,5,$content,0);
			$boact = $this->activities_list($_pro_main,False);
			$content = '';
			if (is_array($boact))
			{
				foreach($boact as $activity)
				{
					$content .=  $activity['descr'] . ' [' . $activity['num'] . ']'."\n";
				}
			} 
			$pdf->MultiCell(150,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('Billable activities'),False,'iso-8859-1').':';
			$pdf->Cell(40,5,$content,0);
			$billact = $this->activities_list($_pro_main,True);
			$content = '';
			if (is_array($billact))
			{
				foreach($billact as $activity)
				{
					$content .=  $activity['descr'] . ' [' . $activity['num'] . ']'."\n";
				}
			} 
			$pdf->MultiCell(150,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('invoicing method'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($subProjectData['inv_method'],False,'iso-8859-1');
			$pdf->Cell(65,5,$content,0);
			$content = $botranslation->convert(lang('discount'),False,'iso-8859-1').':';
			if($subProjectData['discount_type'] == 'percent')
				$content .= ' %';
			elseif($subProjectData['discount_type'] == 'amount')
				$content .= ''; // add currency
			$pdf->Cell(30,5,$content,0);
			$content = $subProjectData['discount'];
			$pdf->Cell(65,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('result'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($subProjectData['result'],False,'iso-8859-1');
			$pdf->MultiCell(160,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('test'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($subProjectData['test'],False,'iso-8859-1');
			$pdf->MultiCell(160,5,$content,0);
			$pdf->Ln();

			$content = $botranslation->convert(lang('quality check'),False,'iso-8859-1').':';
			$pdf->Cell(30,5,$content,0);
			$content = $botranslation->convert($subProjectData['quality'],False,'iso-8859-1');
			$pdf->MultiCell(160,5,$content,0);
			$pdf->Ln();








#########################
				}
			}

			if($tempFileName = tempnam($GLOBALS['phpgw_info']['server']['temp_dir'],'project'))
			{
				$start = $mainProjectData[sdate];
				$end = $mainProjectData[edate];
				$bostatistics->show_graph
				(
					array
					(
						'project_array'         => array($_pro_main),
						'sdate'                 => $start?$start:mktime(12,0,0,date('m'),date('d'),date('Y')),
						'edate'                 => $end?$end:mktime(12,0,0,date('m'),date('d')+30,date('Y')),
						'showMilestones'        => true,
						'showResources'         => true
					),
					$tempFileName
				);
				if(filesize($tempFileName) > 0)
				{
					$pdf->AddPage();
					$pdf->Image($tempFileName,5,5,200,0,'PNG');
				}
			}


			$pdfFile = $pdf->Output('','S');

			if($tempFileName)
				unlink($tempFileName);

			return $pdfFile;

		}

		# projects stores the time internal in following format
		# hh.mm
		# this function transforms it to hh:mm
		function formatTime($_time)
		{
			// just the hour
			if(!str_replace('.',':',$_time))
				return $_time.":00";
				
			return str_replace('.',':',$_time);
		}
		
		function type($action)
		{
			switch ($action)
			{
				case 'mains'		: $column = 'projects_mains'; break;
				case 'subs'		: $column = 'projects_subs'; break;
				case 'pad'		: $column = 'projects_pad'; break;
				case 'amains'		: $column = 'projects_amains'; break;
				case 'asubs'		: $column = 'projects_asubs'; break;
				case 'ustat'		: $column = 'projects_ustat'; break;
				case 'pstat'		: $column = 'projects_pstat'; break;
				case 'act'		: $column = 'projects_act'; break;
				case 'pad'		: $column = 'projects_pad'; break;
				case 'role'		: $column = 'projects_role'; break;
				case 'accounting'	: $column = 'projects_accounting'; break;
				case 'hours'		: $column = 'projects_hours'; break;
			}
			return $column;
		}

		function save_sessiondata($data, $action)
		{
			if ($this->use_session)
			{
				$column = $this->type($action);
				$GLOBALS['phpgw']->session->appsession('session_data',$column, $data);
			}
		}

		function read_sessiondata($action)
		{
			$column = $this->type($action);
			$data = $GLOBALS['phpgw']->session->appsession('session_data',$column);

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->filter		= $data['filter'];
			$this->order		= $data['order'];
			$this->sort		= $data['sort'];
			$this->cat_id		= $data['cat_id'];
			$this->status		= $data['status'];
			$this->state		= $data['state'];
			$this->project_id	= $data['project_id'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}
		
		function create_activityid()
		{
			return $this->soprojects->create_activityid();
		}

		function edit_perms($pro)
		{
			$type = isset($pro['type'])?$pro['type']:'edit';

			switch($type)
			{
				case 'delete':	
					$acl = PHPGW_ACL_DELETE;
					break;
				default:
					$acl = PHPGW_ACL_EDIT;
					break;
			}

			if($this->check_perms($this->grants[$pro['coordinator']],$acl) || $pro['coordinator'] == $this->account)
			{
				return True;
			}
			if($this->isprojectadmin('pad') || $this->isprojectadmin('pmanager'))
			{
				return True;
			}

			switch($pro['action'])
			{
				case 'mains': break;
				case 'subs':
					if($pro['main_co'])
					{
						$main_co = $pro['main_co'];
					}
					else
					{
						$main_co = $this->soprojects->return_value('co',$pro['main']);
					}
					if($this->check_perms($this->grants[$main_co],$acl) || $main_co == $this->account)
					{
						return True;
					}
					if($pro['parent_co'])
					{
						$parent_co = $pro['parent_co'];
					}
					else
					{
						$parent_co = $this->soprojects->return_value('co',$pro['parent']);
					}
					if($this->check_perms($this->grants[$parent_co],$acl) || $parent_co == $this->account)
					{
						return True;
					}
					break;
			}
			return False;
		}

		function add_perms($pro)
		{	
			if($this->status == 'archive')
			{
				return False;
			}

			switch($pro['action'])
			{
				case 'mains':
					if (intval($this->cat_id) > 0)
					{
						$cat = $this->cats->return_single($this->cat_id);

						if ($cat[0]['app_name'] == 'phpgw' || $cat[0]['owner'] == -1)
						{
							return True;
						}
						else if ($this->check_perms($this->grants[$cat[0]['owner']],PHPGW_ACL_ADD) || $cat[0]['owner'] == $this->account)
						{
							return True;
						}
					}
					else if(intval($this->cat_id) == 0)
					{
						return True;
					}
					else if($this->check_perms($this->grants[$pro['coordinator']],PHPGW_ACL_ADD) || $pro['coordinator'] == $this->account && !is_array($cat))
					{
						return True;
					}
					else if($this->isprojectadmin('pad') || $this->isprojectadmin('pmanager') && !is_array($cat))
					{
						return True;
					}
					break;
				case 'subs':
					if($this->check_perms($this->grants[$pro['coordinator']],PHPGW_ACL_ADD) || $pro['coordinator'] == $this->account)
					{
						return True;
					}
					//$main_co = $this->soprojects->return_value('co',$pro['main']);
					if($this->check_perms($this->grants[$pro['main_co']],PHPGW_ACL_ADD) || $pro['main_co'] == $this->account)
					{
						return True;
					}
					$parent_co = $this->soprojects->return_value('co',$pro['parent']);
					if($this->check_perms($this->grants[$parent_co],PHPGW_ACL_ADD) || $parent_co == $this->account)
					{
						return True;
					}
					if($this->isprojectadmin('pad') || $this->isprojectadmin('pmanager'))
					{
						return True;
					}
					break;
			}
			return False;
		}

		function cached_accounts($account_id)
		{
			$this->accounts = CreateObject('phpgwapi.accounts',$account_id);

			$this->accounts->read_repository();

			$cached_data[$this->accounts->data['account_id']]['account_id']		= $this->accounts->data['account_id'];
			$cached_data[$this->accounts->data['account_id']]['account_lid']	= $this->accounts->data['account_lid'];
			$cached_data[$this->accounts->data['account_id']]['firstname']		= $this->accounts->data['firstname'];
			$cached_data[$this->accounts->data['account_id']]['lastname']		= $this->accounts->data['lastname'];

			return $cached_data;
		}

		function return_date()
		{
			$date = array
			(
				'month'		=> $GLOBALS['phpgw']->common->show_date(time(),'n'),
				'day'		=> $GLOBALS['phpgw']->common->show_date(time(),'d'),
				'year'		=> $GLOBALS['phpgw']->common->show_date(time(),'Y')
			);

			$date['daydate']	= mktime(2,0,0,$date['month'],$date['day'],$date['year']);
			$date['monthdate']	= mktime(2,0,0,$date['month']+2,0,$date['year']);
			$date['monthformatted'] = $GLOBALS['phpgw']->common->show_date($date['monthdate'],'n/Y');
			return $date;
		}

		function read_abook($start, $query, $filter, $sort, $order)
		{
			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$cols = array
			(
				'n_given' => 'n_given',
				'n_family'  => 'n_family',
				'org_name'  => 'org_name'
			);

			$entries = $this->contacts->read($start,$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'], $cols, $query, $filter, $sort, $order, $account_id);

			$this->total_records = $this->contacts->total_records;
			return $entries;
		}

		function read_single_contact($abid)
		{
			$cols = array('n_given' => 'n_given',
				'n_family' => 'n_family',
				'org_name' => 'org_name');

			return $this->contacts->read_single_entry($abid,$cols);
		}

		function return_value($action,$item)
		{
			return $this->soprojects->return_value($action,$item);
		}

		function read_projects_acl($useronly = True)
		{		
			$aclusers	= $GLOBALS['phpgw']->acl->get_ids_for_location('run',1,'projects');
			$acl_users	= $GLOBALS['phpgw']->accounts->return_members($aclusers);

			if($useronly)
			{
				$employees	= $acl_users['users'];
				return $employees;
			}
			else
			{
				return $acl_users;
			}
		}

		function get_acl_for_project($project_id = '')
		{
			$project_id = intval($project_id);
			return $GLOBALS['phpgw']->acl->get_ids_for_location($project_id, 7);
		}

		function get_employee_projects($account_id = '')
		{
			return $this->soprojects->get_employee_projects($account_id);
		}
		
		function getProjectResources($_projectID = '')
		{
			$resources = array();
		
			$projectID = intval($_projectID);
			if($projectID != $_projectID) return false;
			
			$employees = $this->get_acl_for_project($projectID);
			
			$resources = $this->soprojects->getProjectResources($projectID, $employees);
			
			foreach($employees as $employee)
			{
				$accountData = CreateObject('phpgwapi.accounts',$employee);
				$accountData->read_repository();

				$resources[] = array
				(
					'employee'	=> $employee,
					'resource'	=> $resources[$employee].'.....',
					'name'		=> $GLOBALS['phpgw']->common->display_fullname
							   (
								$accountData->data['account_lid'],
								$accountData->data['firstname'],
								$accountData->data['lastname']
							   ),
				);
			}
			
			return $resources;
		}

		function selected_employees($data = 0)
		{
			$project_id = intval($data['project_id']);
			$pro_parent = intval($data['pro_parent']);

			if(intval($project_id) > 0)
			{
				$emps = $this->get_acl_for_project($project_id);
			}
			else
			{
				$emps = $this->read_projects_acl();
			}
			// remove duplicates
			$emps = array_unique($emps);
			sort($emps);
			
			if(isset($data['action']) && $data['action'] == 'subs')
			{
				$parent_select = $this->get_acl_for_project($pro_parent);

				$k = 0;
				if(is_array($parent_select))
				{
					for($i=0;$i<count($emps);$i++)
					{
						if(in_array($emps[$i],$parent_select))
						{
							$emp[$k] = $emps[$i];
							$k++;
						}
					}
				}
				if(is_array($emp))
				{
					$emps = array();
					$emps = $emp;
				}
			}

			if($data['admins_included'] == True)
			{
				$co = $this->soprojects->return_value('co',$project_id?$project_id:$pro_parent);

				//echo 'CO:' . $co;
				if(is_array($emps) && !in_array($co,$emps))
				{
					$i = count($emps);
					$emps[$i] = $co;
				}
			}

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
			asort($empl);
			reset($empl);
			return $empl;
		}

		function get_time_used($data)
		{
			if($this->siteconfig['accounting'] == 'activity')
			{
				return $this->sohours->get_activity_time_used($data);
			}
			else
			{
				return $this->sohours->get_time_used($data);
			}
		}

		function calculate_budget($factor,$minutes, $project_id = 0,$project_array = 0,$is_billable = False)
		{
			if($this->siteconfig['accounting'] == 'activity')
			{
				$budget = $this->sohours->calculate_activity_budget(array('project_id' => $project_id,'project_array' => $project_array));
				return $is_billable?$budget['bbudget']:$budget['budget'];
			}
			else
			{
				$factor_per_minute = $factor/60;
				return round($factor_per_minute*$minutes,2);
			}
		}

// BUDGET FOR ACTIVIES

		function get_activity_budget($params)
		{
			$subs = $this->get_sub_projects($params);
			if(is_array($subs))
			{
				$i = 0;
				foreach($subs as $sub)
				{
					$sub_pro[$i] = $sub['project_id'];
					$i++;
					$sum_budget += $sub['budgetSum'];
					$sum_ptime += $sub['time_planned'];
				}

				$acc = array();

				if($params['page'] == 'planned')
				{
					$acc['pbudget_jobs']	= $sum_budget;
					$ptimejobs				= $this->sohours->format_wh($sum_ptime);
					$acc['ptime_jobs']		= $ptimejobs['whwm'];
					$acc['ptime_jobs_min']	= $sum_ptime;
					return $acc;
				}
			}
			$uhours_pro			= $this->sohours->get_activity_time_used(array('project_id' => $params['project_id']));
			$uhours_pro_nobill	= $this->sohours->get_activity_time_used(array('project_id' => $params['project_id'],'no_billable' => True));
			$uhours_pro_bill	= $uhours_pro - $uhours_pro_nobill;

			$formatted_uhours_pro			= $this->sohours->format_wh($uhours_pro);
			$formatted_uhours_pro_bill		= $this->sohours->format_wh($uhours_pro_bill);
			$formatted_uhours_pro_nobill	= $this->sohours->format_wh($uhours_pro_nobill);

			$acc['uhours_pro']				= $formatted_uhours_pro['whwm'];
			$acc['uhours_pro_nobill']		= $formatted_uhours_pro_nobill['whwm'];
			$acc['uhours_pro_bill']			= $formatted_uhours_pro_bill['whwm'];
			$acc['uhours_pro_wminutes']		= $uhours_pro;

			$formatted_ahours_pro			= $this->sohours->format_wh($params['ptime'] - $uhours_pro);
			$acc['ahours_pro']				= $formatted_ahours_pro['whwm'];

			$uhours_jobs		= $this->sohours->get_activity_time_used(array('project_array' => $sub_pro));
			$uhours_jobs_nobill	= $this->sohours->get_activity_time_used(array('project_array' => $sub_pro,'no_billable' => True));
			$uhours_jobs_bill	= $uhours_jobs - $uhours_jobs_nobill;

			$formatted_uhours_jobs			= $this->sohours->format_wh($uhours_jobs);
			$formatted_uhours_jobs_bill		= $this->sohours->format_wh($uhours_jobs_bill);
			$formatted_uhours_jobs_nobill	= $this->sohours->format_wh($uhours_jobs_nobill);

			$acc['uhours_jobs']				= $formatted_uhours_jobs['whwm'];
			$acc['uhours_jobs_nobill']		= $formatted_uhours_jobs_nobill['whwm'];
			$acc['uhours_jobs_bill']		= $formatted_uhours_jobs_bill['whwm'];
			$acc['uhours_jobs_wminutes']	= $uhours_jobs;

			$formatted_ahours_jobs			= $this->sohours->format_wh($params['ptime'] - $uhours_jobs);
			$acc['ahours_jobs']				= $formatted_ahours_jobs['whwm'];

			if($params['page'] == 'budget')
			{
				$acc['u_budget'] = $this->calculate_budget(0,0,$params['project_id']);
				$acc['b_budget'] = $this->calculate_budget(0,0,$params['project_id'],0,True);

				$acc['u_budget_jobs'] = $this->calculate_budget(0,0,0,$sub_pro);
				$acc['b_budget_jobs'] = $this->calculate_budget(0,0,0,$sub_pro,True);
			}
			return $acc;
		}

		function get_budget($params)
		{
			if($this->siteconfig['accounting'] == 'activity')
			{
				return $this->get_activity_budget($params);
			}
			else
			{
				if(!$params['billable'])
				{
					$params['billable'] = $this->return_value('billable',$params['project_id']);
				}

				$subs = $this->get_sub_projects($params);

				if(is_array($subs))
				{
					$i = 0;
					foreach($subs as $sub)
					{
						switch($sub['billable'])
						{
							case 'N': $sub_pro_nobill[$i]	= $sub['project_id']; break;
							case 'Y': $sub_pro_bill[$i]		= $sub['project_id']; break;
						}

						$sub_pro[$i] = $sub['project_id'];
						$i++;
						//if(is_array($sub['budget']))
						//{
						//	foreach($sub['budget'] as $budgetYear)
						//	{
						//		foreach($budgetYear as $budgetMonth)
						//		{
						//			$sum_budget += $budgetMonth;
						//		}
						//	}
						//}
						$sum_budget += $sub['budgetSum'];
						$sum_ptime += $sub['time_planned'];
					}
				}

				$acc = array();

				if($params['page'] == 'planned')
				{
					$acc['pbudget_jobs']	= $sum_budget;
					$ptimejobs				= $this->sohours->format_wh($sum_ptime);
					$acc['ptime_jobs']		= $ptimejobs['whwm'];
					$acc['ptime_jobs_min']	= $sum_ptime;
					return $acc;
				}

				$uhours_pro = $this->get_time_used(array('project_id' => $params['project_id']));

				if($params['billable'] == 'Y')
				{
					$uhours_pro_nobill	= $this->sohours->get_time_used(array('project_id' => $params['project_id'],'no_billable' => True));
					$uhours_pro_bill	= $uhours_pro - $uhours_pro_nobill;
				}
				elseif($params['billable'] == 'N')
				{
					$uhours_pro_nobill	= $uhours_pro;
					$uhours_pro_bill	= 0;
				}

				$formatted_uhours_pro			= $this->sohours->format_wh($uhours_pro);
				$formatted_uhours_pro_bill		= $this->sohours->format_wh($uhours_pro_bill);
				$formatted_uhours_pro_nobill	= $this->sohours->format_wh($uhours_pro_nobill);

				$acc['uhours_pro']			= $formatted_uhours_pro['whwm'];
				$acc['uhours_pro_nobill']	= $formatted_uhours_pro_nobill['whwm'];
				$acc['uhours_pro_bill']		= $formatted_uhours_pro_bill['whwm'];
				$acc['uhours_pro_wminutes']	= $uhours_pro;

				$formatted_ahours_pro		= $this->sohours->format_wh($params['ptime'] - $uhours_pro);
				$acc['ahours_pro']			= $formatted_ahours_pro['whwm'];

				$uhours_jobs			= $this->sohours->get_time_used(array('project_array' => $sub_pro));
				$uhours_jobs_bill		= ($sub_pro_bill)? $this->sohours->get_time_used(array('project_array' => $sub_pro_bill,'is_billable' => True)) : 0;

				$jobs_nobill_billpro	= ($sub_pro_bill)? $this->sohours->get_time_used(array('project_array' => $sub_pro_bill,'no_billable' => True)) : 0;
				$jobs_nobill			= $this->sohours->get_time_used(array('project_array' => $sub_pro_nobill));

				$uhours_jobs_nobill		= $jobs_nobill_billpro + $jobs_nobill;

				$formatted_uhours_jobs			= $this->sohours->format_wh($uhours_jobs);
				$formatted_uhours_jobs_bill		= $this->sohours->format_wh($uhours_jobs_bill);
				$formatted_uhours_jobs_nobill	= $this->sohours->format_wh($uhours_jobs_nobill);

				$acc['uhours_jobs']				= $formatted_uhours_jobs['whwm'];
				$acc['uhours_jobs_nobill']		= $formatted_uhours_jobs_nobill['whwm'];
				$acc['uhours_jobs_bill']		= $formatted_uhours_jobs_bill['whwm'];
				$acc['uhours_jobs_wminutes']	= $uhours_jobs;

				$formatted_ahours_jobs		= $this->sohours->format_wh($params['ptime'] - $uhours_jobs);
				$acc['ahours_jobs']			= $formatted_ahours_jobs['whwm'];

				if($params['page'] == 'budget')
				{
					switch($params['accounting'])
					{
						case 'project':
							$acc['u_budget']	= $this->calculate_budget($params['project_accounting_factor'],$uhours_pro);
							$acc['b_budget']	= $this->calculate_budget($params['project_accounting_factor'],$uhours_pro_bill);
							break;
						case 'employee':
							$emps_pro = $this->sohours->get_employee_time_used(array('project_id' => $params['project_id']));
							for($i=0;$i<count($emps_pro);$i++)
							{
								$factor	= $this->soconfig->return_value('acc',$emps_pro[$i]['employee']);
								$bill	= $this->calculate_budget($factor,$emps_pro[$i]['utime']);
								$acc['u_budget'] += $bill;
							}

							if($params['billable'] == 'Y')
							{
								$emps_pro_bill = $this->sohours->get_employee_time_used(array('project_id' => $params['project_id'],'is_billable' => True));
								for($i=0;$i<count($emps_pro_bill);$i++)
								{
									$factor	= $this->soconfig->return_value('acc',$emps_pro_bill[$i]['employee']);
									$bill	= $this->calculate_budget($factor,$emps_pro_bill[$i]['utime']);
									$acc['b_budget'] += $bill;
								}
							}
							//_debug_array($emps_pro);
							break;
					}

					for($i=0;$i<count($subs);$i++)
					{
						$pro_budget = 0;
						switch($subs[$i]['accounting'])
						{
							case 'project':
								$time_used	= $this->get_time_used(array('project_id' => $subs[$i]['project_id']));
								$pro_budget	= $this->calculate_budget($subs[$i]['acc_factor'],$time_used);

								if($subs[$i]['billable'] == 'Y')
								{
									$time_used_bill		= $this->get_time_used(array('project_id' => $subs[$i]['project_id'],'is_billable' => True));
									$pro_budget_bill	= $this->calculate_budget($subs[$i]['acc_factor'],$time_used_bill);
								}
								break;
							case 'employee':
								$emps_pro = $this->sohours->get_employee_time_used(array('project_id' => $subs[$i]['project_id']));

								$pro_budget = $pro_budget_bill = 0;
								for($k=0;$k<count($emps_pro);$k++)
								{
									$factor	= $this->soconfig->return_value('acc',$emps_pro[$k]['employee']);
									$bill	= $this->calculate_budget($factor,$emps_pro[$k]['utime']);
									$pro_budget += $bill;
								}
								if($subs[$i]['billable'] == 'Y')
								{
									$emps_pro_bill = $this->sohours->get_employee_time_used(array('project_id' => $subs[$i]['project_id'],'is_billable' => True));
									for($k=0;$k<count($emps_pro_bill);$k++)
									{
										$factor	= $this->soconfig->return_value('acc',$emps_pro_bill[$k]['employee']);
										$bill	= $this->calculate_budget($factor,$emps_pro_bill[$k]['utime']);
										$pro_budget_bill += $bill;
									}
								}
								break;
						}
						$acc['u_budget_jobs'] += $pro_budget;
						$acc['b_budget_jobs'] += $pro_budget_bill;
					}
				}
				//_debug_array($acc);
				return $acc;
			}
		}

		function get_sub_projects($params)
		{
			switch($params['page'])
			{
				case 'planned':			$column = 'project_id,level,budget,time_planned'; break;
				case 'hours':
				case 'budget':			$column = 'project_id,accounting,acc_factor,billable,level'; break;
			}
			$subs = $this->soprojects->read_projects(array('column' => $column,'limit' => False,'action' => 'subs','parent' => $params['project_id']));

			$i = count($subs);
			$subs[$i]['project_id'] = $params['project_id'];
			$subs[$i]['accounting'] = $params['accounting'];
			$subs[$i]['billable'] = $params['billable'];
			$subs[$i]['acc_factor'] = $params['project_accounting_factor'];

			//_debug_array($subs);
			return $subs;
		}

		function colored($value, $limit = 0,$used = 0,$action = 'budget')
		{
			$event_extra = $this->soconfig->get_event_extra($action=='budget'?'budget limit':'hours limit');

			$used_percent = ($limit*intval($event_extra))/100;

			if($this->html_output && ($used > $used_percent))
			{
				if($action == 'hours')
					return '<font color="#CC0000"><b>' . $this->formatTime($value) . '</b></font>';
				else
					return '<font color="#CC0000"><b>' . sprintf("%01.2f",$value) . '</b></font>';
			}
			
			if($action == 'hours')
				return $this->formatTime($value);
			else
				return sprintf("%01.2f",$value);
		}

		function formatted_priority($pri = 0)
		{
			$green	= $pri <= 3?True:False;
			$yel	= ($pri > 3 && $pri <= 7)?True:False;
			$red	= $pri > 7?True:False;

			$color = ($green?'38BB00':($yel?'ECC200':'CC0000'));

			return '<font color="#' . $color . '">' . $pri . '</font>';
		}

		function list_projects($params)
		{
			$pro_list = $this->soprojects->read_projects(array
			(
				'start'		=> $this->start,
				'limit'		=> isset($params['limit']) ? $params['limit'] : $this->limit,
				'query'		=> isset($params['query']) ? $params['query'] : $this->query,
				'filter'	=> $this->filter,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'status'	=> $this->status,
				'cat_id'	=> ($params['action'] == 'mains'?$this->cat_id:0),
				'action'	=> $params['action'],
				'parent'	=> $params['parent'],
				'main'		=> $params['main'],
				'project_id'	=> $params['project_id']
			));

			$this->total_records = $this->soprojects->total_records;
			if(is_array($pro_list))
			{
				foreach($pro_list as $pro)
				{
					#_debug_array($pro);
					/*$cached_data = $this->cached_accounts($pro['coordinator']);
					$coordinatorout = $GLOBALS['phpgw']->common($cached_data[$pro['coordinator']]['account_lid']
                                        . ' [' . $cached_data[$pro['coordinator']]['firstname'] . ' '
                                        . $cached_data[$pro['coordinator']]['lastname'] . ' ]');*/

					$customerout = '';
					if ($pro['customer'])
					{
						$customer = $this->read_single_contact($pro['customer']);
    						if ($customer[0]['org_name'] == '') 
    						{ 
    							$customerout = $customer[0]['n_given'] . ' ' . $customer[0]['n_family']; 
    						}
    						else
    						{
    							$customerout = $customer[0]['org_name'] . ' [ ' . $customer[0]['n_given'] . 
    							' ' . $customer[0]['n_family'] . ' ]'; 
    						}
					}

					$mstones = $this->get_mstones($pro['project_id']);

					if (!isset($params['mstones_stat']))
					{
						$mlist = '';
						if (is_array($mstones))
						{
							$mlist = '<table width="100%" border="0" cellpadding="0" cellspacing="0">' . "\n";
							for ($i=0;$i<count($mstones);$i++)
							{
								$mlist .= '<tr><td width="50%">' . $mstones[$i]['title'] . '</td><td width="50%" align="right">' . $this->formatted_edate($mstones[$i]['edate']) . '</td></tr>' . "\n";
							}
							$mlist .= '</table>';
						}
					}

					if($params['page'] == 'budget' || $params['page'] == 'hours')
					{
						$params['project_id']					= $pro['project_id'];
						$params['accounting']					= $pro['accounting'];
						$params['project_accounting_factor']	= $pro['project_accounting_factor'];
						$params['billable']						= $pro['billable'];
						$params['ptime']						= $pro['ptime'];
						$acc = $this->get_budget($params);
					}

					$uhours_pro		= $this->colored($acc['uhours_pro'],$pro['ptime'],$acc['uhours_pro_wminutes'],'hours');
					$uhours_jobs	= $this->colored($acc['uhours_jobs'],$pro['ptime'],$acc['uhours_jobs_wminutes'],'hours');

					$ubudget_pro	= $this->colored($acc['u_budget'],$pro['budgetSum'],$acc['u_budget']);
					$ubudget_jobs	= $this->colored($acc['u_budget_jobs'],$pro['budgetSum'],$acc['u_budget_jobs']);

					$spaceset = '';
					if ($pro['level'] > 0)
					{
						$spaceset = str_repeat($this->html_output?'&nbsp;.&nbsp;':'.',$pro['level']);
					}

					$projects[] = array
					(
						'project_id'		=> $pro['project_id'],
						'priority'		=> $pro['priority'],
						'title'			=> $spaceset . $GLOBALS['phpgw']->strip_html($pro['title']),
						'number'		=> $GLOBALS['phpgw']->strip_html($pro['number']),
						'descr'			=> $GLOBALS['phpgw']->strip_html($pro['descr']),
						'investment_nr'		=> $GLOBALS['phpgw']->strip_html($pro['investment_nr']),
						'coordinatorout'	=> $GLOBALS['phpgw']->common->grab_owner_name($pro['coordinator']),
						'customerout'		=> $customerout,
						'customer_nr'		=> $GLOBALS['phpgw']->strip_html($pro['customer_nr']),
						'sdate_formatted'	=> $this->formatted_edate($pro['sdate'],False),
						'edate_formatted'	=> $this->formatted_edate($pro['edate']),
						'sdate'			=> $pro['sdate'],
						'edate'			=> $pro['edate'],
						'psdate'		=> $pro['psdate'],
						'pedate'		=> $pro['pedate'],
						'psdate_formatted'	=> $this->formatted_edate($pro['psdate'],False),
						'pedate_formatted'	=> $this->formatted_edate($pro['pedate'],False),
						'previous_formatted'	=> $this->return_value('pro',$pro['previous']),
						'phours'		=> ($pro['ptime']/60) . ':00',
						'budgetSum'		=> $pro['budgetSum'],
						'budget'		=> $pro['budget'],
						'e_budget'		=> $pro['e_budget'],
						'url'			=> $GLOBALS['phpgw']->strip_html($pro['url']),
						'reference'		=> $GLOBALS['phpgw']->strip_html($pro['reference']),
						'accountingout'		=> lang('per') . ' ' . lang($pro['accounting']),
						'project_accounting_factor'	=> $pro['project_accounting_factor'],
						'project_accounting_factor_d'	=> $pro['project_accounting_factor_d'],
						'billableout'		=> $pro['billable']=='Y'?lang('yes'):lang('no'),
						'discountout'		=> $pro['discount_type']=='percent'?'%':$GLOBALS['phpgw_info']['user']['preferences']['common']['currency']
												 . ' ' . $pro['discount'],
						'mstones'		=> (isset($params['mstones_stat'])?$mstones:$mlist),
						'main'			=> $pro['main'],
						'parent'		=> $pro['parent'],
						'coordinator'		=> $pro['coordinator'],
						'previous'		=> $pro['previous'],
						'status'		=> $pro['status'],
						'level'			=> $pro['level'],
						'test'                  => $GLOBALS['phpgw']->strip_html($pro['test']),
						'quality'               => $GLOBALS['phpgw']->strip_html($pro['quality']),
						'result'                => $GLOBALS['phpgw']->strip_html($pro['result']),
						'inv_method'		=> $GLOBALS['phpgw']->strip_html($pro['inv_method']),
						'discount'		=> $pro['discount'],
						'discount_type'		=> $pro['discount_type'],
						'uhours_pro'		=> $uhours_pro,      //$acc['uhours_pro']?$acc['uhours_pro']:'0:00',
						'uhours_pro_nobill'	=> $acc['uhours_pro_nobill']?$acc['uhours_pro_nobill']:'0',
						'uhours_pro_bill'	=> $acc['uhours_pro_bill']?$acc['uhours_pro_bill']:'0',
						'uhours_jobs'		=> $uhours_jobs,     //$acc['uhours_jobs']?$acc['uhours_jobs']:'0',
						'uhours_jobs_nobill'	=> $acc['uhours_jobs_nobill']?$acc['uhours_jobs_nobill']:'0',
						'uhours_jobs_bill'	=> $acc['uhours_jobs_bill']?$acc['uhours_jobs_bill']:'0',
						'ahours_pro'		=> $acc['ahours_pro']?$acc['ahours_pro']:'0',
						'ahours_jobs'		=> $acc['ahours_jobs']?$acc['ahours_jobs']:'0',
						'u_budget'			=> $acc['u_budget'] ? $acc['u_budget'] : '0.00',
						'u_budget_colored'	=> $ubudget_pro,
						'u_budget_jobs'		=> $acc['u_budget_jobs'] ? $acc['u_budget_jobs'] : '0.00',
						'u_budget_jobs_colored'	=> $ubudget_jobs, 
						'a_budget'		=> $pro['budgetSum']-$acc['u_budget'],
						'a_budget_jobs'		=> $pro['budgetSum']-$acc['u_budget_jobs'],
						'b_budget'		=> $acc['b_budget']?$acc['b_budget']:'0',
						'b_budget_jobs'		=> $acc['b_budget_jobs']?$acc['b_budget_jobs']:'0',
					);
				}
			}
			return $projects;
		}

		function format_date($date = 0)
		{
			$d = array();
			if($date > 0)
			{
				$d['date'] = $date + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$d['date_formatted'] = $GLOBALS['phpgw']->common->show_date($date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			return $d;
		}

		function formatted_edate($edate = 0,$colored = True)
		{
			$edate = intval($edate);

			$month  = $GLOBALS['phpgw']->common->show_date(time(),'n');
			$day    = $GLOBALS['phpgw']->common->show_date(time(),'d');
			$year   = $GLOBALS['phpgw']->common->show_date(time(),'Y');

			if ($edate > 0)
			{
				$edate = $edate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$edateout = $GLOBALS['phpgw']->common->show_date($edate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			if($this->html_output && $colored)
			{
				$event_extra = $this->soconfig->get_event_extra('project date due');

				/*if (mktime(2,0,0,$month,$day+($event_extra*2),$year) >= $edate)
				{
					$edateout = '<font color="#ECC200"><b>' . $edateout . '</b></font>';
				}*/
				if (mktime(12,0,0,$month,$day+$event_extra,$year) >= $edate)
				{
					$edateout = '<font color="#CC0000"><b>' . $edateout . '</b></font>';
				}
			}
			return $edateout;
		}

		function read_single_project($project_id,$page = 'bla',$action = 'subs')
		{
			$pro = $this->soprojects->read_single_project($project_id);

			if($page == 'budget' || $page == 'hours' || $page = 'planned')
			{
				$acc = $this->get_budget(array('project_accounting_factor' => $pro['project_accounting_factor'],'accounting' => $pro['accounting'],
											'project_id' => $project_id,'page' => $page,'action' => $action,'ptime' => $pro['ptime']));
				$atime = $this->sohours->format_wh($pro['ptime']-$acc['ptime_jobs_min']);
			}

			$uhours_pro		= $this->colored($acc['uhours_pro'],$pro['ptime'],$acc['uhours_pro_wminutes'],'hours');
			$uhours_jobs	= $this->colored($acc['uhours_jobs'],$pro['ptime'],$acc['uhours_jobs_wminutes'],'hours');

			$ubudget_pro	= $this->colored($acc['u_budget'],$pro['budgetSum'],$acc['u_budget']);
			$ubudget_jobs	= $this->colored($acc['u_budget_jobs'],$pro['budgetSum'],$acc['u_budget_jobs']);

			$project = array
			(
				'ptime'			=> ($pro['ptime']/60) . '.00',
				'ptime_min'		=> $pro['ptime'],
				'ptime_jobs'		=> $acc['ptime_jobs'],
				'atime'			=> $atime['whwm'],
				'title'			=> $GLOBALS['phpgw']->strip_html($pro['title']),
				'number'		=> $GLOBALS['phpgw']->strip_html($pro['number']),
				'investment_nr'		=> $GLOBALS['phpgw']->strip_html($pro['investment_nr']),
				'descr'			=> $GLOBALS['phpgw']->strip_html($pro['descr']),
				'budgetSum'		=> $pro['budgetSum'],
				'budget'		=> $pro['budget'],
				'e_budget'		=> $pro['e_budget'],
				'pbudget_jobs'		=> $acc['pbudget_jobs']?$acc['pbudget_jobs']:'0.00',
				'ap_budget_jobs'	=> $pro['budgetSum']-$acc['pbudget_jobs'],
				'a_budget'		=> $pro['budgetSum']-$acc['u_budget'],
				'a_budget_jobs'		=> $pro['budgetSum']-$acc['u_budget_jobs'],
				'u_budget'		=> $ubudget_pro,       //$acc['u_budget']?$acc['u_budget']:'0.00',
				'u_budget_jobs'		=> $ubudget_jobs,      //$acc['u_budget_jobs']?$acc['u_budget_jobs']:'0.00',
				'project_id'		=> $pro['project_id'],
				'parent'		=> $pro['parent'],
				'main'			=> $pro['main'],
				'cat'			=> $pro['cat'],
				'access'		=> $pro['access'],
				'coordinator'		=> $pro['coordinator'],
				'coordinatorout'	=> $GLOBALS['phpgw']->common->grab_owner_name($pro['coordinator']),
				'customer'		=> $pro['customer'],
				'status'		=> $pro['status'],
				'owner'			=> $pro['owner'],
				'processor'		=> $pro['processor'],
				'previous'		=> $pro['previous'],
				'url'			=> $GLOBALS['phpgw']->strip_html($pro['url']),
				'reference'		=> $GLOBALS['phpgw']->strip_html($pro['reference']),
				'customer_nr'		=> $GLOBALS['phpgw']->strip_html($pro['customer_nr']),
				'test'			=> $GLOBALS['phpgw']->strip_html($pro['test']),
				'quality'		=> $GLOBALS['phpgw']->strip_html($pro['quality']),
				'result'		=> $GLOBALS['phpgw']->strip_html($pro['result']),
				'accounting'		=> $pro['accounting'],
				'project_accounting_factor'	=> $pro['project_accounting_factor'],
				'project_accounting_factor_d'	=> $pro['project_accounting_factor_d'],
				'billable'		=> $pro['billable'],
				'uhours_pro'		=> $uhours_pro,          //$acc['uhours_pro']?$acc['uhours_pro']:'0.00',
				'uhours_pro_nobill'	=> $acc['uhours_pro_nobill']?$acc['uhours_pro_nobill']:'0.00',
				'uhours_pro_bill'	=> $acc['uhours_pro_bill']?$acc['uhours_pro_bill']:'0.00',
				'uhours_jobs'		=> $uhours_jobs,          //$acc['uhours_jobs']?$acc['uhours_jobs']:'0.00',
				'uhours_jobs_nobill'	=> $acc['uhours_jobs_nobill']?$acc['uhours_jobs_nobill']:'0.00',
				'uhours_jobs_bill'	=> $acc['uhours_jobs_bill']?$acc['uhours_jobs_bill']:'0.00',
				'uhours_jobs_wminutes'	=> $acc['uhours_jobs_wminutes']?$acc['uhours_jobs_wminutes']:0,
				'ahours_pro'		=> $acc['ahours_pro']?$acc['ahours_pro']:'0.00',
				'ahours_jobs'		=> $acc['ahours_jobs']?$acc['ahours_jobs']:'0.00',
				'priority'		=> $pro['priority'],
				'inv_method'		=> $GLOBALS['phpgw']->strip_html($pro['inv_method']),
				'discount'		=> $pro['discount'],
				'discount_type'		=> $pro['discount_type']
			);

			$project['edate']			= $pro['edate'];
			$project['edate_formatted'] = $this->formatted_edate($pro['edate']);

			$date = $this->format_date($pro['sdate']);
			$project['sdate']			= $date['date'];
			$project['sdate_formatted'] = $date['date_formatted'];

			$date = $this->format_date($pro['udate']);
			$project['udate']			= $date['date'];
			$project['udate_formatted'] = $date['date_formatted'];

			$date = $this->format_date($pro['cdate'] == 0?$pro['sdate']:$pro['cdate']);
			$project['cdate']			= $date['date'];
			$project['cdate_formatted'] = $date['date_formatted'];

			$date = $this->format_date($pro['pedate']);
			$project['pedate']				= $date['date'];
			$project['pedate_formatted']	= $date['date_formatted'];

			$date = $this->format_date($pro['psdate']);
			$project['psdate']				= $date['date'];
			$project['psdate_formatted']	= $date['date_formatted'];

			if ($pro['customer'] > 0) 
			{
				$customer = $this->read_single_contact($pro['customer']);
				if ($customer[0]['org_name'] == '')
				{
					$project['customerout'] = $customer[0]['n_given'] .
					' ' . $customer[0]['n_family'];
				}
				else 
				{
					$project['customerout'] = $customer[0]['org_name'] . 
					' [ ' . $customer[0]['n_given'] . ' ' . 
					$customer[0]['n_family'] . ' ]'; }
			}
			else { $project['customerout'] = '&nbsp;'; }

			//_debug_array($project);
			return $project;

		}

		function sum_budget($values)
		{
			return $this->soprojects->sum_budget(array
									(
										'start'		=> $this->start,
										'limit'		=> False,
										'query'		=> $this->query,
										'filter'	=> $this->filter,
										'sort'		=> $this->sort,
										'order'		=> $this->order,
										'status'	=> $this->status,
										'cat_id'	=> ($values['action'] == 'mains'?$this->cat_id:0),
										'action'	=> $values['action'],
										'parent'	=> $values['parent'],
										'main'		=> $values['main'],
										'bcolumn'	=> $values['bcolumn']
									));
		}

		function exists($action, $check, $num, $pa_id)
		{
			$exists = $this->soprojects->exists($action, $check , $num, $pa_id);
			if ($exists)
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function check_values($action, $values)
		{
			if (strlen($values['descr']) > 8000)
			{
				$error[] = lang('Description can not exceed 8000 characters in length');
			}

			if (!$values['coordinator'])
			{
				$error[] = lang('please choose a project coordinator');
			}

			if (strlen($values['title']) > 250)
			{
				$error[] = lang('title can not exceed 250 characters in length');
			}

			if (!$values['choose'])
			{
				if (! $values['number'])
				{
					$error[] = lang('Please enter an ID');
				}
				else
				{
					if (strlen($values['number']) > 250)
					{
						$error[] = lang('id can not exceed 250 characters in length');
					}
					if ($this->exists('','number',$values['number'],$values['project_id']))
					{
						$error[] = lang('id is already taken, choose an other one');
					}
				}
			}

			if($this->siteconfig['accounting'] == 'activity')
			{
				if ((!$values['book_activities']) && (!$values['bill_activities']))
				{
					$error[] = lang('please choose activities for the project');
				}
			}
			else
			{
				switch($values['accounting'])
				{
					case 'project':
						if($values['project_accounting_factor_d'] <= 0.0 && $values['project_accounting_factor'] <= 0.0)
						{
							$error[] = lang('please set the accounting factor for the project');
						}
						break;
					case '':
						$error[] = lang('please choose the accounting system for the project');
						break;
				}
			}

			$values['discount'] = ($values['discount']=='0.00')?0:$values['discount'];
			if($values['discount'] > 0 && !$values['discount_type'])
			{
				$error[] = lang('please choose the discount type');
			}

			if ($values['smonth'] || $values['sday'] || $values['syear'])
			{
				if (! checkdate($values['smonth'],$values['sday'],$values['syear']))
				{
					$error[] = lang('You have entered an invalid start date');
				}
			}

			if ($values['emonth'] || $values['eday'] || $values['eyear'])
			{
				if (! checkdate($values['emonth'],$values['eday'],$values['eyear']))
				{
					$error[] = lang('You have entered an invalid date due');
				}
			}

			if ($values['psmonth'] || $values['psday'] || $values['psyear'])
			{
				if (! checkdate($values['psmonth'],$values['psday'],$values['psyear']))
				{
					$error[] = lang('You have entered an invalid planned start date');
				}
			}

			if ($values['pemonth'] || $values['peday'] || $values['peyear'])
			{
				if (! checkdate($values['pemonth'],$values['peday'],$values['peyear']))
				{
					$error[] = lang('You have entered an invalid planned end date');
				}
			}

			if ($values['previous'])
			{
				$edate = $this->return_value('edate',$values['previous']);

				if (intval($edate) == 0)
				{
					$error[] = lang('the choosen previous project does not have an end date specified');
				}
			}

			if ($action == 'subs')
			{
				$main_edate = $this->return_value('edate',$values['parent']);				

				if ($main_edate != 0)
				{
					$checkdate = mktime(12,0,0,$values['emonth'],$values['eday'],$values['eyear']);

					if ($checkdate > $main_edate)
					{
						$error[] = lang('ending date can not be after parent projects date due');
					}
				}

				$main_sdate = $this->return_value('sdate',$values['parent']);				

				if ($main_sdate != 0)
				{
					$checkdate = mktime(12,0,0,$values['smonth'],$values['sday'],$values['syear']);

					if ($checkdate < $main_sdate)
					{
						$error[] = lang('start date can not be before parent projects start date');
					}
				}

				$ptime_parent	= $this->soprojects->return_value('ptime',$values['parent']);
				$sum_ptime	= $this->soprojects->get_planned_value(array('action' => 'tparent','parent_id' => $values['parent']
							,'project_id' => $values['project_id']));

				$pminutes = intval($values['ptime'])*60;

				if (($pminutes+$sum_ptime) > $ptime_parent)
				{
					$error[] = lang('planned time sum of all sub projects is bigger than the planned time of the main project');
				}

				$budget_parent	= $this->soprojects->return_value('budgetSum',$values['parent']);
				$sum_budget	= $this->soprojects->get_planned_value(array('action' => 'bparent','parent_id' => $values['parent']
																	,'project_id' => $values['project_id']));
				//print "Parent: $budget_parent Sum: $sum_budget<br>";
				//_debug_array($values['budget']);
				$sumProjectBudget=0;
				if(is_array($values['budget']))
				{
					foreach ($values['budget'] as $budgetData)
					{
							$sumProjectBudget += $budgetData['text'];
					}
				}
				if (($sumProjectBudget+$sum_budget) > $budget_parent)
				{
					$error[] = lang('budget sum of all sub projects is bigger than the budget of the main project');
				}

				$ebudget_parent	= $this->soprojects->return_value('e_budget',$values['parent']);
				$sum_ebudget	= $this->soprojects->get_planned_value(array('action' => 'ebparent','parent_id' => $values['parent']
																	,'project_id' => $values['project_id']));
				if (($values['e_budget']+$esum_budget) > $ebudget_parent)
				{
					$error[] = lang('extra budget sum of all sub projects is bigger than the extra budget of the main project');
				}
			}

			if (is_array($error))
			{
				return $error;
				//_debug_array($error);
			}
		}

		function save_project($action, $values)
		{
			if ($values['choose'])
			{
				switch($action)
				{
					case 'mains':
						$values['number'] = $this->soprojects->create_projectid(); break;
					default:
						$values['number'] = $this->soprojects->create_jobid($values['parent']); break;
				}
			}

			$values['ptime'] = intval($values['ptime'])*60;
			
//ndee 130504
			if (!is_object($this->jscal))
				{
					$this->jscal = CreateObject('phpgwapi.jscalendar');
				}
//ndee

//NDEE 140504

			$startdate = $this->jscal->input2date($values['startdate']);
			$values['sday'] = $startdate['day'];
			$values['smonth'] = $startdate['month'];
			$values['syear'] = $startdate['year'];

			$enddate = $this->jscal->input2date($values['enddate']);
			$values['eday'] = $enddate['day'];
			$values['emonth'] = $enddate['month'];
			$values['eyear'] = $enddate['year'];

			$pstartdate = $this->jscal->input2date($values['pstartdate']);
			$values['psday'] = $pstartdate['day'];
			$values['psmonth'] = $pstartdate['month'];
			$values['psyear'] = $pstartdate['year'];

			$penddate = $this->jscal->input2date($values['penddate']);
			$values['peday'] = $penddate['day'];
			$values['pemonth'] = $penddate['month'];
			$values['peyear'] = $penddate['year'];
//NDEE


			if ($values['smonth'] || $values['sday'] || $values['syear'])
			{
				$values['sdate'] = mktime(12,0,0,$values['smonth'], $values['sday'], $values['syear']) - 60*60*$GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
			}
			
			if (!$values['sdate'])
			{
				$values['sdate'] = time() - 60*60*$GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
			}

			if ($values['emonth'] || $values['eday'] || $values['eyear'])
			{
				$values['edate'] = mktime(12,0,0,$values['emonth'],$values['eday'],$values['eyear']) - 60*60*$GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
			}
			$values['edate'] = intval($values['edate']);

			if ($values['pemonth'] || $values['peday'] || $values['peyear'])
			{
				$values['pedate'] = mktime(12,0,0,$values['pemonth'],$values['peday'],$values['peyear']) - 60*60*$GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
			}

			if ($values['psmonth'] || $values['psday'] || $values['psyear'])
			{
				$values['psdate'] = mktime(12,0,0,$values['psmonth'],$values['psday'],$values['psyear']) - 60*60*$GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
			}

			if (!$values['previous'] && $values['parent'])
			{
				$values['previous'] = $this->return_value('previous',$values['parent']);
			}

			if ($values['project_accounting_factor'] <= 0.0)
			{
				$values['project_accounting_factor'] = $values['project_accounting_factor_d'] / $this->siteconfig['hwday'];
			}
			if ($values['project_accounting_factor_d'] <= 0.0)
			{
				$values['project_accounting_factor_d'] = $values['project_accounting_factor'] * $this->siteconfig['hwday'];
			}

			//_debug_array($values);

			$values['project_name'] = $values['title'] . ' [' . $values['number'] . ']'; 
			if (intval($values['project_id']) > 0)
			{
				$following = $this->soprojects->edit_project($values);

				if(is_array($following))
				{
					$return = $this->send_alarm(array('project_name' => $values['project_name'],'event_type' => 'project dependencies','project_id' => $values['project_id'],
											'following' => $following,'edate' => $values['edate'],'old_edate' => $values['old_edate'],'is_previous' => True));

					if($return)
					{
						foreach($following as $fol)
						{
							$fol['previous_name']		= $values['project_name'];
							$fol['previous_edate']		= $values['edate'];
							$fol['previous_old_edate']	= $values['old_edate'];
							$fol['project_name']		= $fol['title'] . ' [' . $fol['number'] . ']';
							$fol['event_type']			= 'project dependencies';
							$this->send_alarm($fol);
						}
					}
				}

				$this->send_alarm(array('project_name' => $values['project_name'],'event_type' => 'changes of project data','project_id' => $values['project_id']));

				if($values['coordinator'] != $values['old_coordinator'])
				{
					$this->send_alarm(array('account_id' => $values['coordinator'],'events' => array($event_id),'project_name' => $values['project_name'],
											'event_type' => 'assignment to role','project_id' => $values['project_id']));
				}
			}
			else
			{
				$values['project_id'] = $this->soprojects->add_project($values);
			}

			$values['project_id'] = intval($values['project_id']);

			$values['old_edate'] = intval($values['old_edate']);
			$async = CreateObject('phpgwapi.asyncservice');
			if($values['edate'] > 0 && $values['old_edate'] != $values['edate'])
			{
				$event_extra = $this->soconfig->get_event_extra('project date due');
				$next = mktime(date('H',time()),date('i',time())+1,0,$values['emonth'],$values['eday']-$event_extra,$values['eyear']);

				$edate = $this->format_date($values['edate']);
				$async->cancel_timer('projects-' . $values['project_id']);
				$async->set_timer
				(
					$next,
					'projects-' . $values['project_id'],
					'projects.boprojects.send_alarm',
					array
					(
						'project_id' => $values['project_id'],
						'event_type' => 'project date due',
						'edate' => $edate['date_formatted'],
						'project_name' => $values['project_name']
					),
					$values['coordinator']
				);
			}

			if($values['edate'] == 0)
			{
				$aid = 'projects-' . $values['project_id'];
				$async->cancel_timer($aid);
			}
			unset($async);

			//_debug_array($values['employees']);
			if (is_array($values['employees']))
			{
				$this->soprojects->delete_acl($values['project_id']);
				for($i=0;$i<count($values['employees']);$i++)
				{
					$GLOBALS['phpgw']->acl->add_repository('projects',$values['project_id'],$values['employees'][$i],7);
				}
			}
			return $values['project_id'];
		}

		function select_project_list($values)
		{
			return $this->soprojects->select_project_list($values);
		}

		function delete_project($pa_id, $subs, $action = 'pro')
		{
			if ($action == 'account')
			{
				$this->soprojects->delete_account_project_data($pa_id);
			}
			else
			{
				$this->soprojects->delete_project($pa_id, $subs);
			}
		}

		function change_owner($old, $new)
		{
			$this->soprojects->change_owner($old, $new);
		}

		function get_mstones($project_id)
		{
			$mstones = $this->soprojects->get_mstones($project_id);

			if(is_array($mstones))
			{
				foreach($mstones as $ms)
				{
					$stones[] = array
					(
						'title'		=> $GLOBALS['phpgw']->strip_html($ms['title']),
						'description'	=> $GLOBALS['phpgw']->strip_html($ms['description']),
						'edate'		=> $ms['edate'],
						's_id'		=> $ms['s_id']
					);
				}
				return $stones;
			}
			return False;
		}

		function get_single_mstone($s_id)
		{
			return $this->soprojects->get_single_mstone($s_id);
		}

		function check_mstone($values)
		{
			if (strlen($values['title']) > 250)
			{
				$error[] = lang('title can not exceed 250 characters in length');
			}
			if (strlen($values['description']) > 250)
			{
				$error[] = lang('description can not exceed 250 characters in length');
			}
			if ($values['emonth'] || $values['eday'] || $values['eyear'])
			{
				if (! checkdate($values['emonth'],$values['eday'],$values['eyear']))
				{
					$error[] = lang('You have entered an invalid date due');
				}
			}

			$pro_edate = $this->return_value('edate',$values['project_id']);				

			if ($pro_edate > 0)
			{
				$checkdate = mktime(12,0,0,$values['emonth'],$values['eday'],$values['eyear']);

				if ($checkdate > $pro_edate)
				{
					$error[] = lang('ending date can not be after projects date due');
				}
			}
			if(is_array($error))
			{
				return $error;
			}
		}

		function save_mstone($values)
		{
			if ($values['emonth'] || $values['eday'] || $values['eyear'])
			{
				$values['edate'] = mktime(12,0,0,$values['emonth'],$values['eday'],$values['eyear']);
			}
			$values['edate'] = intval($values['edate']);

			if (intval($values['s_id']) > 0)
			{
				$this->soprojects->edit_mstone($values);
			}
			else
			{
				$values['s_id'] = $this->soprojects->add_mstone($values);
			}

			$values['old_edate'] = intval($values['old_edate']);
			$async = CreateObject('phpgwapi.asyncservice');
			if($values['edate'] > 0 && $values['old_edate'] != $values['edate'])
			{
				$co = $this->soprojects->return_value('co',$values['project_id']);
				$event_extra = $this->soconfig->get_event_extra('milestone date due');
				$next = mktime
				(
					date('H',time()),
					date('i',time())+1,
					0,
					date('m',$values['edate']),
					date('d',$values['edate'])-$event_extra,
					date('Y',$values['edate'])
				);
				
				$edate = $this->format_date($values['edate']);
/*				$async->write
				(
					array
					(
						'id'		=> 'ms-' . $values['s_id'] . '-project-' . $values['project_id'], 
						'next'		=> $next,
						'account_id'	=> $co,
						'method'	=> 'projects.boprojects.send_alarm',
						'data'		=> array
						(
							'project_id'	=> $values['project_id'],
							'event_type'	=> 'milestone date due',
							'edate'		=> $edate['date_formatted'],
							'ms_title'	=> $values['title']
						)
					)
				);*/
				$asyncID = 'ms-' . $values['s_id'] . '-project-' . $values['project_id'];
				$async->cancel_timer($asyncID);
				$async->set_timer
				(
					$next,
					$asyncID,
					'projects.boprojects.send_alarm',
					array
					(
							'project_id'	=> $values['project_id'],
							'event_type'	=> 'milestone date due',
							'edate'		=> $edate['date_formatted'],
							'ms_title'	=> $values['title']
					),
					$co
				);
			}
			if($values['edate'] == 0)
			{
				$aid = 'ms-' . $values['s_id'] . '-project-' . $values['project_id'];
				$async->cancel_timer($aid);
			}
			unset($async);
			#$this->send_alarm
			#(
			#	array
			#	(
			#			'project_id'	=> $values['project_id'],
			#			'event_type'	=> 'milestone date due',
			#			'edate'		=> $edate['date_formatted'],
			#			'ms_title'	=> $values['title']
			#	)
			#);
			return $values['s_id'];
		}

		function delete_item($values)
		{
			switch($values['action'])
			{
				case 'emp_role':	$this->soprojects->soconfig->delete_pa($values['action'],$values['id']); break;
				default:			$this->soprojects->delete_mstone($values['id']);
			}
		}

		function member($project_id = '')
		{
			return $this->soprojects->member($project_id);
		}


// ------------ ALARM ----------------

		function send_alarm($values)
		{
			$event_type	= isset($values['event_type'])?$values['event_type']:'assignment to role';
			$project_name	= isset($values['project_name'])?$values['project_name']:$this->soprojects->return_value('pro',$values['project_id']);

			switch($event_type)
			{
				case 'assignrolepro':
					$values['event_type'] = 'assignment to project,assignment to role';
					$emp_events	= $this->soprojects->read_employee_roles($values);
					break;
				default:
					$emp_events = $this->soprojects->read_employee_roles($values);
					break;
			}

			#echo 'BOPROJECTS->alarm EVENTS: ';
			#_debug_array($emp_events);

			$notify_hours	= $this->soprojects->check_alarm($values['project_id'],'hours');
			$notify_budget	= $this->soprojects->check_alarm($values['project_id'],'budget');

			for($k=0;$k<count($emp_events);$k++)
			{
				for($i=0;$i<count($emp_events[$k]['events']);$i++)
				{
					$event		= $this->soprojects->id2item(array('action' => 'event','item_id' => $emp_events[$k]['events'][$i],'item' => 'event_name'));
					$co		= $this->soprojects->return_value('co',$values['project_id']);
					$subject	= lang('project') .  ': ' . $project_name . ': ' . lang($event) . ' ';

					switch($event_type)
					{
						case 'project date due':
						case 'milestone date due':
						case 'budget limit':
						case 'hours limit': $subject .= lang('has reached'); break;
						case 'project dependencies': $subject .=  ', ' . ($values['is_previous']?lang('end date has changed'):lang('previous projects end date has changed')); break;
					}

					$send_alarm = False;
					switch($event)
					{
						case 'changes of project data':
							$send_alarm = True;
							$msg = $subject;
							break;
						case 'assignment to role':
							$send_alarm = True;
							if($co == $emp_events[$k]['account_id'])
							{
								$role_name = lang('coordinator');
							}
							else
							{
								$role_name = $this->soprojects->id2item(array('action' => 'role','item_id' => $emp_events[$k]['role_id'],'item' => 'role_name'));
							}
							$msg = lang($event) . ': ' . $role_name;
							break;
						case 'project dependencies':
							$send_alarm = True;
							$changedate = $this->siteconfig['dateprevious'] == 'yes'?True:False;
							if($values['is_previous'])
							{
								$edate = $this->format_date($values['edate']);
								$oedate = $this->format_date($values['old_edate']);
								$msg = lang('previous project') . ': ' . $project_name . "\n"
									. lang('old end date') . ': ' . $oedate['date_formatted'] . "\n"
									. lang('new end date') . ': ' . $edate['date_formatted'] . "\n\n"
							 		. lang('projects, which are assigned as sequencing') . ':' . "\n"
									. ($changedate?lang('changed start date and end date of projects bellow'):'') . "\n\n";

								if(is_array($values['following']))
								{
									foreach($values['following'] as $fol)
									{
										$sdate	= $this->format_date($fol['sdate']);
										$nsdate	= ($changedate?$this->format_date($fol['nsdate']):'');
										$edate	= $this->format_date($fol['edate']);
										$nedate	= ($changedate?$this->format_date($fol['nedate']):'');
										$msg .= $fol['title'] . ' [' . $fol['number'] . '] ' . "\n"
											. ($changedate?lang('old start date'):lang('start date')) . ': ' . $sdate['date_formatted'] . ' '
											. ($changedate?lang('new start date') . ': ' . $nsdate['date_formatted']:'') . "\n"
											. ($changedate?lang('old end date'):lang('end date')) . ': ' . $edate['date_formatted'] . ' '
											. ($changedate?lang('new end date') . ': ' . $nedate['date_formatted']:'') . "\n";

										if(is_array($fol['mstones']))
										{
											foreach($fol['mstones'] as $stone)
											{
												$sedate	= $this->format_date($stone['edate']);
												$snedate	= ($changedate?$this->format_date($stone['snedate']):'');
												$msg .= lang('milestone') . ' ' . $stone['title'] . "\n"
														. ($changedate?lang('old end date'):lang('end date')) . ': ' . $sedate['date_formatted'] . ' '
														. ($changedate?lang('new end date') . ': ' . $snedate['date_formatted']:'') . "\n";
											}
										}
										$msg .= "\n";
									}
								}
							}
							else
							{
								$previous_edate = $this->format_date($values['previous_edate']);
								$previous_oedate = $this->format_date($values['previous_old_edate']);

								$sdate	= $this->format_date($values['sdate']);
								$nsdate	= ($changedate?$this->format_date($values['nsdate']):'');
								$edate	= $this->format_date($values['edate']);
								$nedate	= ($changedate?$this->format_date($values['nedate']):'');

								$msg = lang('previous project') . ': ' . $values['previous_name'] . "\n"
										. lang('old end date') . ': ' . $previous_oedate['date_formatted'] . "\n"
										. lang('new end date') . ': ' . $previous_edate['date_formatted'] . "\n\n"

										. lang('sequencing project') . ': ' . $project_name . "\n"
										. ($changedate?lang('changed start date and end date'):'') . "\n"
										. ($changedate?lang('old start date'):lang('start date')) . ': ' . $sdate['date_formatted'] . ' '
										. ($changedate?lang('new start date') . ': ' . $nsdate['date_formatted']:'') . "\n"
										. ($changedate?lang('old end date'):lang('end date')) . ': ' . $edate['date_formatted'] . ' '
										. ($changedate?lang('new end date') . ': ' . $nedate['date_formatted']:'') . "\n";

								if(is_array($values['mstones']))
								{
									foreach($values['mstones'] as $stone)
									{
										$sedate	= $this->format_date($stone['edate']);
										$snedate	= ($changedate?$this->format_date($stone['snedate']):'');
										$msg .= lang('milestone') . ' ' . $stone['title'] . "\n"
												. ($changedate?lang('old end date'):lang('end date')) . ': ' . $sedate['date_formatted'] . ' '
												. ($changedate?lang('new end date') . ': ' . $snedate['date_formatted']:'') . "\n";
									}
								}
							}
							break;
						case 'hours limit':
							$send_alarm = $notify_hours?True:False;
							$msg = lang($event) . ': ' . $values['ptime'] . "\n"
									. lang('hours used total') . ': ' . $values['uhours_jobs_all'];
							break;
						case 'budget limit':
							$send_alarm = $notify_budget?True:False;
							$msg = lang($event) . ': ' . $values['budget'] . "\n"
									. lang('budget used total') . ': ' . $GLOBALS['phpgw_info']['user']['preferences']['common']['currency']
									. ' ' . $values['u_budget_jobs'];
							break;
						case 'assignment to project':
							$send_alarm = True;
							$msg = lang($event) . ': ' . $project_name;
							break;
						case 'project date due':
							$send_alarm = $event_type=='project date due'?True:False;
							$msg = lang($event) . ': ' . $values['edate'];
							break;
						case 'milestone date due':
							$send_alarm = $event_type=='milestone date due'?True:False;
							$msg = lang($event) . ': ' . $values['edate'] . "\n";
							$msg .= lang('milestone') . ': ' . $values['ms_title'] . "\n";
							$msg .= lang('project') . ': ' . $project_name;
							break;
					}

					if($send_alarm)
					{
						$sender = $GLOBALS['phpgw']->accounts->id2name($co,'account_email');
						$to = $GLOBALS['phpgw']->accounts->id2name($emp_events[$k]['account_id'],'account_email');

						$msgtype = '"projects";';

						if(!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject,$msg,''/*$msgtype*/,'','','',$sender);
						//echo "<p>send(to='$to', sender='$sender'<br>subject='$subject') returncode=$returncode<br>".nl2br($body)."</p>\n";

					}
				}
			}
			return $returncode;
		}

		function activities_list($project_id, $billable)
		{
			$activities_list = $this->soprojects->soconfig->activities_list($project_id, $billable);
			return $activities_list;
		}

		function select_activities_list($project_id, $billable)
		{
			$activities_list = $this->soprojects->soconfig->select_activities_list($project_id, $billable);
			return $activities_list;
		}

		function select_pro_activities($project_id, $pro_parent, $billable)
		{
			$activities_list = $this->soprojects->soconfig->select_pro_activities($project_id, $pro_parent, $billable);
			return $activities_list;
		}

		function select_hours_activities($project_id,$act,$billable='')
		{
			if (!$billable)
			{
				$billable = substr($act,-1);
				if ($billable == 'Y' || $billable == 'N')
				{
					$act = substr($act,0,-1);
				}
				else
				{
					$billable = '';
				}
			}
			return $this->soprojects->soconfig->select_hours_activities($project_id,$act,$billable);
		}

		function select_hours_costs($_projectID, $_costID)
		{
			$costs_list = $this->soprojects->soconfig->select_hours_costs($_projectID, $_costID);
			return $costs_list;
		}

		function isprojectadmin($action = 'pad')
		{
			return $this->soprojects->soconfig->isprojectadmin($action);
		}

		function read_prefs($default = True)
		{
			//$GLOBALS['phpgw']->preferences->read_repository();

			$prefs = array();
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['projects']['columns']))
			{
				$cols = $GLOBALS['phpgw_info']['user']['preferences']['projects']['columns'];
				$prefs['columns'] = explode(',',$cols);
			}
			else if($default)
			{
				$prefs['columns'] = array('priority','number','customerout','coordinatorout','edateout');
			}
			$prefs['currency'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			return $prefs;
		}

		function get_prefs()
		{
			return $this->read_prefs();
		}

		function get_employee_roles($data)
		{
			$formatted = isset($data['formatted'])?$data['formatted']:False;

			$emp_roles = $this->soprojects->read_employee_roles($data);

			if(is_array($emp_roles))
			{
				foreach($emp_roles as $emp)
				{
					$eventNames = array();
					if (is_array($emp['events']) && $formatted)
					{
						$eformatted = '';
						$eformatted = '<table width="100%" border="0" cellpadding="0" cellspacing="0">' . "\n";
						for ($i=0;$i<count($emp['events']);$i++)
						{
							$e = $this->soprojects->id2item(array('action' => 'event','item_id' => $emp['events'][$i],'item' => 'event_name'));
							$eformatted .= '<tr><td width="100%">' . ($e ? lang($e) : '') . '</td></tr>' . "\n";
						}
						$eformatted .= '</table>';
					}
					elseif(is_array($emp['events']))
					{
						for ($i=0;$i<count($emp['events']);$i++)
						{
							$e = $this->soprojects->id2item(array('action' => 'event','item_id' => $emp['events'][$i],'item' => 'event_name'));
							$eventNames[]= $e;
						}
					}

					$user[] = array
					(
						'r_id'		=> $emp['r_id'],
						'account_id'	=> $emp['account_id'],
						'emp_name'	=> $GLOBALS['phpgw']->common->grab_owner_name($emp['account_id']),
						'role_id'	=> $emp['role_id'],
						'role_name'	=> $GLOBALS['phpgw']->strip_html($this->soprojects->id2item(array('item_id' => $emp['role_id'],'item' => 'role_name','action' => 'role'))),
						'events'	=> $formatted?$eformatted:$emp['events'],
						'eventNames'	=> $eventNames
					);
				}
				return $user;
			}
			return False;
		}

		function save_employee_role($values)
		{
			$old_roles = $this->soprojects->read_employee_roles(array('project_id' => $values['project_id'],'account_id' => $values['account_id']));

			if(is_array($old_roles))
			{
				list($old_roles) = $old_roles;
				$values['r_id'] = $old_roles['r_id'];
			}

			$this->soprojects->save_employee_role($values,(is_array($old_roles)?True:False));

			if(is_array($old_roles['events']) && is_array($values['events']))
			{
				$event_role_id = $this->soprojects->item2id(array('item' => 'assignment to role'));
				$values['role_id'] = intval($values['role_id']);

				if(!in_array($event_role_id,$old_roles['events']) && in_array($event_role_id,$values['events']) && $values['role_id'] > 0)
				{
					$send_role = True;
				}
				if(in_array($event_role_id,$values['events']) && intval($old_roles['role_id']) != $values['role_id'] && $values['role_id'] > 0)
				{
					$send_role = True;
				}

				if($send_role)
				{
					$values['event_type'] = 'assignment to role';
					$this->send_alarm($values);
				}

				$event_assignpro_id = $this->soprojects->item2id(array('item' => 'assignment to project'));

				if(!in_array($event_assignpro_id,$old_roles['events']) && in_array($event_assignpro_id,$values['events']))
				{
					$values['event_type'] = 'assignment to project';
					$this->send_alarm($values);
				}
			}

			if(!is_array($old_roles['events']) && is_array($values['events']))
			{
				$values['event_type'] = 'assignrolepro';
				$this->send_alarm($values);
			}
		}

		function list_roles($_roleType)
		{
			$roles = $this->soprojects->soconfig->list_roles
			(
				array
				(
					'start' 	=> $this->start,
					'sort' 		=> $this->sort,
					'order' 	=> $this->order,
					'query' 	=> $this->query,
					'roleType'	=> $_roleType,
					'limit' 	=> $this->limit
				)
			);
			
			$this->total_records = $this->soprojects->soconfig->total_records;

			if(is_array($roles))
			{
				foreach($roles as $role)
				{
					$emp_roles[] = array
					(
						$_roleType.'_id'	=> $role[$_roleType.'_id'],
						$_roleType.'_name'	=> $GLOBALS['phpgw']->strip_html($role[$_roleType.'_name'])
					);
				}
				return $emp_roles;
			}
			return False;
		}

		function get_granted_roles($project_id)
		{
			$emps = $this->selected_employees($project_id);
			$roles	= $this->get_employee_roles($project_id);

			if(is_array($emps))
			{
				foreach($emps as $emp)
				{
					$assigned_role = '';
					for($i=0;$i<count($roles);$i++)
					{
						if($roles[$i]['account_id'] == $emp['account_id'])
						{
							$assigned_role = $roles[$i]['role_name'];
						}
					}

					$assigned[] = array
					(
						'emp_name'	=> $GLOBALS['phpgw']->common->display_fullname($emp['account_lid'],$emp['account_firstname'],$emp['account_lastname']),
						'role_name'	=> $assigned_role
					);
				}
				return $assigned;
			}
			return False;
		}

		function list_events($type = '')
		{
			return $this->soprojects->soconfig->list_events($type);
		}

		function get_event_extra($type = '')
		{
			return $this->soprojects->get_event_extra($type);
		}

		function action_format($selected = 0,$action = 'role',$type = '')
		{
			$this->limit = False;

			switch($action)
			{
				case 'event':	
					$list = $this->list_events($type); 
					break;
				default:		
					$list = $this->list_roles('role'); 
					break;
			}

			if(!is_array($selected))
			{
				$selected = explode(',',$selected);
			}

			//_debug_array($selected);

			$id		= $action . '_id';
			$name	= $action . '_name';

			if(is_array($list))
			{
				foreach($list as $li)
				{
					$list_list .= '<option value="' . $li[$id] . '"';
					if(in_array($li[$id],$selected))
					{
						$list_list .= ' selected';
					}
					$list_list .= '>' . ($action=='event'?lang($li[$name]):$li[$name]) . '</option>' . "\n";
				}
				return $list_list;
			}
			return False;
		}
	}
?>
