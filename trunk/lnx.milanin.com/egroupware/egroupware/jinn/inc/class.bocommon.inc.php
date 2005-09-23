<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for eGroupWare
	Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

	eGroupWare - http://www.egroupware.org

	This file is part of JiNN

	JiNN is free software; you can redistribute it and/or modify it under
	the terms of the GNU General Public License as published by the Free
	Software Foundation; version 2 of the License.

	JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or 
	FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License 
	along with JiNN; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
	*/

	class bocommon
	{
	   var $so;
	   var $prefs;

		function bocommon()
		{
			$this->so = CreateObject('jinn.sojinn');
			$this->prefs = $this->read_preferences_all();
		}
  
		function read_preferences_all()
		{
		   $GLOBALS['phpgw']->preferences->read_repository();

		   $prefs = array();

		   if ($GLOBALS['phpgw_info']['user']['preferences']['jinn'])
		   {
			  $prefs = $GLOBALS['phpgw_info']['user']['preferences']['jinn'];
		   }
		   return $prefs;
		}

		/**
		* exit and redirect within session
		*
		* @param string $menu_action phpgw link to function in class
		*/
		function exit_and_open_screen($menu_action)
		{
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction='.$menu_action));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		/****************************************************************************\
		* format timestamp date to europian format                                   *
		\****************************************************************************/
		// gebruik zo snel mogelijk phpgwapi functie
		function format_date($input)
		{
			// Deze functie converteert bv. 200124061216  naar:  24-06-2001 12:16
			$jaar = substr($input,0,4);
			$maand = substr($input,4,2);
			$dag = substr($input,6,2);
			$uren = substr($input,8,2);
			$minuten = substr($input,10,2);
			return("$dag-$maand-$jaar $uren:$minuten");
		}


		/**
		* return egw configuration setting for JiNN
		*
		* @return ???
		*/
		function get_config()
		{
			$c = CreateObject('phpgwapi.config',$config_appname);

			$c->read_repository();

			if ($c->config_data)
			{
				return $c->config_data;
			}
		}



		function filter_array_with_prefix($array,$prefix)
		{
			while (list ($key, $val) = each ($array)) 
			{

				if (substr($key,0,strlen($prefix))==$prefix)
				{
					$return_array[]=$val;
				}
			}
			return $return_array;
		}

		/*
		Function to retrieve a global get or post var where get overrules post
		*/
		// FIXME remove this
		function get_global_var($name,$priority='get')
		{
		   if($priority=='post')
		   {
			  $tmp_var=($_POST[$name]?$_POST[$name]:$_GET[$name]);
		   }
		   else
		   {
			  $tmp_var=($_GET[$name]?$_GET[$name]:$_POST[$name]);
		   }

		   if($tmp_var)
		   {
			  return $tmp_var;
		   }
		   else
		   {
			  return false;
		   }
		}

		// FIXME remove this
		function get_global_vars($name_arr,$priority='get')
		{
		   if(is_array($name_arr))
		   {
			  foreach($name_arr as $name)
			  {
				 $tmp_arr[]=$this->get_global_var($name,$priority);
			  }
			  return $tmp_arr;
		   }
		}

		function check_safe_mode()
		{
			if (ini_get('safe_mode'))
			{
				$safe_mode='On';
			}
			else
			{
				$safe_mode='Off';
			}
			return $safe_mode;
		}


		// remove this one, so->site_table_metadata replaces this and uiconfig is the only class that uses this one
/*		function get_object_column_names($site_id,$table)
		{
			$fields_props=$this->so->site_table_metadata($site_id,$table);

			foreach ($fields_props as $field_props)
			{
				$column_names[]=$field_props[name];
			}

			return $column_names;
		}
*/
		function get_sites_allowed($uid)
		{
			$groups=$GLOBALS['phpgw']->accounts->membership();

			if (count ($groups)>0)
			{
				foreach ( $groups as $groupfields )
				{
					$group[]=$groupfields[account_id];
				}
			}

			$user_sites=$this->so->get_sites_for_user($uid,$group);
			//		die(var_dump($user_sites));
			return $user_sites;
		}

		function get_objects_allowed($site_id,$uid)
		{
			$groups=$GLOBALS['phpgw']->accounts->membership();

			if (count ($groups)>0)
			{
				foreach ( $groups as $groupfields )
				{
					$group[]=$groupfields[account_id];
				}
			}

			$objects=$this->so->get_objects($site_id,$uid,$group);
			return $objects;
		}


	}


