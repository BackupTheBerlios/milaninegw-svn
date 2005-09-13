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
	/* $Id: class.bodeliveries.inc.php,v 1.21.2.1 2004/11/06 12:15:27 ralfbecker Exp $ */
	/* $Source: /cvsroot/egroupware/projects/inc/class.bodeliveries.inc.php,v $ */

	class bodeliveries
	{
		var $public_functions = array
		(
			'check_values'			=> True,
			'read_hours'			=> True,
			'read_delivery_hours'	=> True,
			'read_single_delivery'	=> True,
			'delivery'				=> True,
			'update_delivery'		=> True,
			'read_deliveries'		=> True,
			'get_address_data'		=> True,
			'read_delivery_pos'		=> True
		);

		function bodeliveries()
		{
			$this->sodeliveries	= CreateObject('projects.sodeliveries');
			$this->soprojects	= CreateObject('projects.soprojects');
			$this->contacts		= CreateObject('phpgwapi.contacts');
			$this->boprojects	= CreateObject('projects.boprojects');
		}

		function read_hours($project_id, $action)
		{
			$hours = $this->sodeliveries->read_hours($project_id, $action);
			return $hours;
		}

		function read_delivery_hours($project_id, $delivery_id, $action)
		{
			$hours = $this->sodeliveries->read_delivery_hours($project_id, $delivery_id, $action);
			return $hours;
		}

		function read_delivery_pos($delivery_id)
		{
			$hours = $this->sodeliveries->read_delivery_pos($delivery_id);
			return $hours;
		}

		function get_site_config()
		{
			return $this->boprojects->get_site_config();
		}

		function read_deliveries($start, $query, $sort, $order, $limit, $project_id)
		{
			$co = $this->get_site_config();
			$del = $this->sodeliveries->read_deliveries(array('start' => $start,'query' => $query,'sort' => $sort,'order' => $order,'limit' => $limit,
															'project_id' => $project_id,'owner' => $co['invoice_acl']));
			$this->total_records = $this->sodeliveries->total_records;
			return $del;
		}

		function read_single_delivery($delivery_id)
		{
			$del = $this->sodeliveries->read_single_delivery($delivery_id);
			return $del;
		}

		function get_address_data($format, $abid, $afont, $asize)
		{
			if ($format == 'address')
			{
				$address = $this->contacts->formatted_address($abid,True,$afont,$asize);
			}
			elseif ($format == 'line')
			{
				$address = $this->contacts->formatted_address_line($abid,True,$afont,$asize);
			}
			else
			{
				$address = $this->contacts->formatted_address_full($abid,True,$afont,$asize);
			}
			return $address;
		}

		function check_values($values,$select)
		{
			if (!$values['choose'])
			{
				if (!$values['delivery_num'])
				{
					$error[] = lang('Please enter an ID !');
				}
				else
				{
					$num = $this->sodeliveries->exists($values);
					if ($num)
					{
						$error[] = lang('That ID has been used already !');
					}
				}
			}

			if (! is_array($select))
			{
				$error[] = lang('The delivery note contains no items !');				
			}

			if (! $values['customer'])
			{
				$error[] = lang('You have no customer selected !');
			}

			if (! checkdate($values['month'],$values['day'],$values['year']))
			{
				$error[] = lang('You have entered an invalid date !');
			}

			if (is_array($error))
			{
				return $error;
			}
		}

		function delivery($values,$select)
		{
			if ($values['choose'])
			{
				$values['delivery_num'] = $this->soprojects->create_deliveryid();
			}

			$values['date'] = mktime(2,0,0,$values['month'],$values['day'],$values['year']);

			$delivery_id = $this->sodeliveries->delivery($values,$select);
			return $delivery_id;
		}

		function update_delivery($values,$select)
		{
			$values['date'] = mktime(2,0,0,$values['month'],$values['day'],$values['year']);

			$this->sodeliveries->update_delivery($values,$select);
		}
	}
?>
