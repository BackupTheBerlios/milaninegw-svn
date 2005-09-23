<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
	Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

	phpGroupWare - http://www.phpgroupware.org

	This file is part of JiNN

	JiNN is free software; you can redistribute it and/or modify it under
	the terms of the GNU General Public License as published by the Free
	Software Foundation; either version 2 of the License, or (at your 
	option) any later version.

	JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or 
	FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License 
	along with JiNN; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
	*/

	class boadmin 
	{
		var $public_functions = Array(
		'del_phpgw_jinn_sites'=> True,
		'del_phpgw_jinn_site_objects' => True,
		'insert_phpgw_jinn_sites'=> True,
		'insert_phpgw_jinn_site_objects'=> True,
		'update_phpgw_jinn_sites'=> True,
		'update_phpgw_jinn_site_objects' => True,
		'access_rights'=> True,
		'save_access_rights_object'=> True,
		'save_access_rights_site'=> True,
		'export_site'=> True,
		);

		var $so;
		var $session;

		var $message;

		var $site_object_id; 
		var $site_object; 
		var $site_id; 
		var $site; 
		var $local_bo;
		var $magick;

		var $current_config;
		var $action;
		var $common;

		var $where_key;
		var $where_value;

		function boadmin()
		{
			$this->common = CreateObject('jinn.bocommon');

			$this->so = CreateObject('jinn.sojinn');
			$this->current_config=$this->common->get_config();		

			$this->read_sessiondata();
			$this->use_session = True;

			$_form = $GLOBALS['HTTP_POST_VARS']['form'] ? $GLOBALS['HTTP_POST_VARS']['form']   : $GLOBALS['HTTP_GET_VARS']['form'];
			$_action = $GLOBALS['HTTP_POST_VARS']['action'] ? $GLOBALS['HTTP_POST_VARS']['action']   : $GLOBALS['HTTP_GET_VARS']['action'];
			$_site_id = $GLOBALS['HTTP_POST_VARS']['site_id'] ? $GLOBALS['HTTP_POST_VARS']['site_id']   : $GLOBALS['HTTP_GET_VARS']['site_id'];
			$_site_object_id = $GLOBALS['HTTP_POST_VARS']['site_object_id'] ? $GLOBALS['HTTP_POST_VARS']['site_object_id']    : $GLOBALS['HTTP_GET_VARS']['site_object_id'];

			$_where_key = $GLOBALS['HTTP_POST_VARS']['where_key'] ? $GLOBALS['HTTP_POST_VARS']['where_key']    : $GLOBALS['HTTP_GET_VARS']['where_key'];

			$_where_value = $GLOBALS['HTTP_POST_VARS']['where_value'] ? $GLOBALS['HTTP_POST_VARS']['where_value']    : $GLOBALS['HTTP_GET_VARS']['where_value'];

			if(!empty($_where_key))
			{
				$this->where_key  = $_where_key;
			}
		
			if(!empty($_where_value))
			{
				$this->where_value  = $_where_value;
			}

			if((!empty($_action) && empty($this->action)) || !empty($_action))
			{
				$this->action  = $_action;
			}
			if (($_form=='main_menu')|| (!empty($site_id)))
			{
				$this->site_id  = $_site_id;
			}

			if (($_form=='main_menu')|| (!empty($site_object_id)))
			{
				$this->site_object_id  = $_site_object_id;
			}

			// get array of site and object
			$this->site = $this->so->get_site_values($this->site_id);

			if ($this->site_object_id)
			{
				$this->site_object = $this->so->get_object_values($this->site_object_id);
			}

			$this->include_plugins();

		}

		function save_sessiondata()
		{
			$data = array(
				'message' => $this->message, # this must be recplaced with new message
				'site_id' => $this->site_id,
				'site_object_id' => $this->site_object_id
			);

			$GLOBALS['phpgw']->session->appsession('session_data','jinn',$data);
		}

		function read_sessiondata()
		{
			if ($GLOBALS['HTTP_POST_VARS']['form']!='main_menu')
			{
				$data = $GLOBALS['phpgw']->session->appsession('session_data','jinn');
				$this->message 		= $data['message'];
				$this->site_id 		= $data['site_id'];
				$this->site_object_id	= $data['site_object_id'];
			}
		}

		/****************************************************************************\
		* delete record from external table                                          *
		\****************************************************************************/

		function delete_phpgw_data($table,$where_key,$where_value)
		{

			return $status;
		}

		/****************************************************************************\
		* insert data into phpgw table                                               *
		\****************************************************************************/

		function insert_phpgw_data($table,$HTTP_POST_VARS,$HTTP_POST_FILES)
		{

			$data=$this->http_vars_pairs($HTTP_POST_VARS,$HTTP_POST_FILES);
			$status=$this->so->insert_phpgw_data($table,$data);

			return $status;
		}

		/****************************************************************************\
		* update data in phpgw table                                                 *
		\****************************************************************************/

		function update_phpgw_data($table,$HTTP_POST_VARS,$HTTP_POST_FILES,$where_key,$where_value)
		{

			/*************************************
			* start relation section             *
			*************************************/

			if ($HTTP_POST_VARS[FLDrelations])
			{
				// check if there are relations to delete
				$relations_to_delete=$this->common->filter_array_with_prefix($HTTP_POST_VARS,'DEL');
				if (count($relations_to_delete)>0){

					$relations_org=explode('|',$HTTP_POST_VARS[FLDrelations]);
					foreach($relations_org as $relation_org)
					{
						if (!in_array($relation_org,$relations_to_delete))
						{
							if ($new_org_relation) $new_org_relation.='|';
							$new_org_relation.=$relation_org;
						}
					}
					$HTTP_POST_VARS[FLDrelations]=$new_org_relation;
				}
			}

			// check if new ONE WITH MANY relation parts are complete else drop them
			if($HTTP_POST_VARS['1_relation_org_field'] && $HTTP_POST_VARS['1_relation_table_field'] 
			&& $HTTP_POST_VARS['1_display_field'])
			{
				$new_relation='1:'.$HTTP_POST_VARS['1_relation_org_field'].':null:'.$HTTP_POST_VARS['1_relation_table_field']
				.':'.$HTTP_POST_VARS['1_display_field'];

				if ($HTTP_POST_VARS['FLDrelations']) $HTTP_POST_VARS['FLDrelations'].='|';
				$HTTP_POST_VARS['FLDrelations'].=$new_relation;
			}

			// check if new MANY WITH MANY relation parts are complete else drop them
			if($HTTP_POST_VARS['2_relation_via_primary_key'] && $HTTP_POST_VARS['2_relation_foreign_key'] 
			&& $HTTP_POST_VARS['2_relation-via-foreign-key'] && $HTTP_POST_VARS['2_display_field'])
			{
				$new_relation='2:'.$HTTP_POST_VARS['2_relation_via_primary_key'].':'.$HTTP_POST_VARS['2_relation-via-foreign-key'].':'
				.$HTTP_POST_VARS['2_relation_foreign_key'].':'.$HTTP_POST_VARS['2_display_field'];

				if ($HTTP_POST_VARS['FLDrelations']) $HTTP_POST_VARS['FLDrelations'].='|';

				$HTTP_POST_VARS['FLDrelations'].=$new_relation;
			}

			// check if new ONE TO ONE relation parts are complete else drop them
			if($HTTP_POST_VARS['3_relation_org_field'] && $HTTP_POST_VARS['3_relation_table_field'] 
			&& $HTTP_POST_VARS['3_relation_object_conf'])
			{
			   $new_relation='3:'.$HTTP_POST_VARS['3_relation_org_field'].':null:'.$HTTP_POST_VARS['3_relation_table_field']
			   .':'.$HTTP_POST_VARS['3_relation_object_conf'];

			   if ($HTTP_POST_VARS['FLDrelations']) $HTTP_POST_VARS['FLDrelations'].='|';
			   $HTTP_POST_VARS['FLDrelations'].=$new_relation;
			}

			// check all pluginfield for values
			// put values in http_post_var

			if ($HTTP_POST_VARS[FLDplugins])
			{
				$HTTP_POST_VARS['FLDplugins']=$this->http_vars_pairs_plugins($HTTP_POST_VARS);
			}


			$data=$this->http_vars_pairs($HTTP_POST_VARS,$HTTP_POST_FILES);
			$status=$this->so->update_phpgw_data($table,$data, $where_key,$where_value);

			return $status;
		}

		function save_access_rights_site()
		{
			reset ($GLOBALS[HTTP_POST_VARS]);
			$site_id=$GLOBALS[HTTP_POST_VARS]['site_id'];
			
			while (list ($key, $val) = each ($GLOBALS[HTTP_POST_VARS])) 
			{
				if (substr($key,0,6)=='editor')	$editors[]=$val;
			}

			if (is_array($editors)) $editors=array_unique($editors);

			$status=$this->so->update_site_access_rights($editors,$site_id);

			if ($status==1)	$this->message[info]=lang('Access rights for Site succesfully editted');
			else $this->message[error]=lang('Access rights for Site NOT succesfully editted');

			$this->save_sessiondata();
//			$this->common->exit_and_open_screen('jinn.uiadmin.access_rights');
			// FIXME keep nextmatch sorting filter etc...
			$this->common->exit_and_open_screen('jinn.uiadmin.set_access_rights_sites&site_id='.$site_id);
		}


		function save_access_rights_object()
		{
			reset ($GLOBALS[HTTP_POST_VARS]);
			$site_id=$GLOBALS[HTTP_POST_VARS]['site_id'];
			$object_id=$GLOBALS[HTTP_POST_VARS]['object_id'];
			
			while (list ($key, $val) = each ($GLOBALS[HTTP_POST_VARS])) 
			{
			   if (substr($key,0,6)=='editor')	$editors[]=$val;
			   //if (substr($key,0,7)=='xeditor')	$existing_editors[]=$val;//existing editors
			}

			if (is_array($editors)) $editors=array_unique($editors);

			$status=$this->so->update_object_access_rights($editors,$GLOBALS[HTTP_POST_VARS]['object_id']);

			if ($status==1)	$this->message[info]=lang('Access rights for site-object succesfully editted');
			else $this->message[error]=lang('Access rights for site-object NOT succesfully editted');

			$this->save_sessiondata();
			
			//			$this->common->exit_and_open_screen('jinn.uiadmin.access_rights');
			// FIXME keep nextmatch sorting filter etc...
			$this->common->exit_and_open_screen('jinn.uiadmin.set_access_rights_site_objects&object_id='.$object_id.'&site_id='.$site_id);
		}

		// FIXME rename
		function insert_phpgw_jinn_sites()
		{
		   $table='phpgw_jinn_sites';

			$status=$this->insert_phpgw_data($table,$GLOBALS[HTTP_POST_VARS],$GLOBAL[HTTP_POST_FILES]);

			if ($status>0)	
			{
				$this->message[info]=lang('Site succesfully added');
    		//	$this->message[error]='';
				
			}
			else 
			{
			//	$this->message[info]='';
				$this->message[error]=lang('Site NOT succesfully added, unknown error');
			}

			$this->save_sessiondata();
			if($GLOBALS[HTTP_POST_VARS]['continue'])
			{
				$this->common->exit_and_open_screen('jinn.uiadmin.add_edit_site&where_key=site_id&where_value='.$status);
			}
			else
			{
				$this->common->exit_and_open_screen('jinn.uiadmin.browse_phpgw_jinn_sites');
			}
		}

		// FIXME rename
		function insert_phpgw_jinn_site_objects()
		{
			$status=$this->insert_phpgw_data('phpgw_jinn_site_objects',$GLOBALS[HTTP_POST_VARS],$GLOBAL[HTTP_POST_FILES]);
			if ($status>0)	$this->message[info]=lang('Site Object succesfully added');
			else $this->message[error]=lang('Site Object NOT succesfully added, unknown error');

			$this->save_sessiondata();
			if($GLOBALS[HTTP_POST_VARS]['continue'])
			{
				$this->common->exit_and_open_screen('jinn.uiadmin.add_edit_object&where_key=object_id&where_value='.$status);
			}
			else
			{
				$this->common->exit_and_open_screen('jinn.uiadmin.add_edit_site&where_key=site_id&where_value='.$GLOBALS[HTTP_POST_VARS][FLDparent_site_id]);
			}

			

		}

		// FIXME rename
		function update_phpgw_jinn_sites()
		{
			$table='phpgw_jinn_sites';

			$status = $this->update_phpgw_data($table,$GLOBALS[HTTP_POST_VARS],$GLOBAL[HTTP_POST_FILES],$this->where_key,$this->where_value);
			if ($status==1)	$this->message[info]=lang('Site succesfully saved');
			else $this->message[error]=lang('Site NOT succesfully saved, unknown error');

			$this->save_sessiondata();
			if($GLOBALS[HTTP_POST_VARS]['continue'])
			{
				//FIXME 
				$this->common->exit_and_open_screen('jinn.uiadmin.add_edit_site&where_key=site_id&where_value='.$this->where_value);
			}
			else
			{
				$this->common->exit_and_open_screen('jinn.uiadmin.browse_phpgw_jinn_sites');
			}
		}

		function update_phpgw_jinn_site_objects()
		{
			$table='phpgw_jinn_site_objects';


//			_debug_array($_POST);
//			die();
			$status = $this->update_phpgw_data($table,$GLOBALS[HTTP_POST_VARS],$GLOBAL[HTTP_POST_FILES],$this->where_key,$this->where_value);

			if ($status==1)	$this->message[info]=lang('Site Object succesfully saved');
			else $this->message[error]=lang('Site Object NOT succesfully saved, unknown error');

			$this->save_sessiondata();
			if($GLOBALS[HTTP_POST_VARS]['continue'])
			{
				$this->common->exit_and_open_screen('jinn.uiadmin.add_edit_object&where_key='.$this->where_key.'&where_value='.$this->where_value);
			}
			else
			{
				$this->common->exit_and_open_screen('jinn.uiadmin.add_edit_site&where_key=site_id&where_value='.$GLOBALS[HTTP_POST_VARS][FLDparent_site_id]);
			}
		}

		// FIXME rename
		function del_phpgw_jinn_sites()
		{
//			var_dump($this->where_value);
//			die();
			$status=$this->so->delete_phpgw_data('phpgw_jinn_sites',$this->where_key,$this->where_value);

			if ($status==1)	$this->message[info]=lang('site succesfully deleted');
			else $this->message[error]=lang('Site NOT succesfully deleted, Unknown error');

			$this->save_sessiondata();
			$this->common->exit_and_open_screen('jinn.uiadmin.browse_phpgw_jinn_sites');
		}

		//FIXME rename
		function del_phpgw_jinn_site_objects()
		{
			$records = $this->so->get_phpgw_record_values('phpgw_jinn_site_objects',$this->where_key,$this->where_value,'','','name');	
			
			$status=$this->so->delete_phpgw_data('phpgw_jinn_site_objects',$this->where_key,$this->where_value);

			if ($status==1)	$this->message[info]=lang('Site Object succesfully deleted');
			else $this->message[error]=lang('Site Object NOT succesfully deleted, Unknown error');

			$this->save_sessiondata();
			
			$this->common->exit_and_open_screen('jinn.uiadmin.add_edit_site&where_key=site_id&where_value='.$records['0']["parent_site_id"]);
		
		}

		function get_phpgw_records($table,$where_key,$where_value,$offset,$limit,$value_reference)
		{
			if (!$value_reference)
			{
				$value_reference='num';
			}

			$records = $this->so->get_phpgw_record_values($table,$where_key,$where_value,$offset,$limit,$value_reference);

			return $records;
		}
		
		function http_vars_pairs($HTTP_POST_VARS,$HTTP_POST_FILES) 
		{
			while(list($key, $val) = each($HTTP_POST_VARS)) 
			{
				if(substr($key,0,3)=='FLD')
				{
					$data[] = array
					(
						'name' => substr($key,3),
						'value' => addslashes($val) 
					);
				}
			}

			return $data;
		}

		/**
		* make array with pairs of keys and values from http_post_vars 
		*/
		// try this with filter_array_with_prefix
		function http_vars_pairs_plugins($HTTP_POST_VARS) 
		{
			while(list($key, $val) = each($HTTP_POST_VARS)) {

				if(substr($key,0,7)=='CFG_PLG' && $val)
				{
					$cfg[substr($key,7)]=$val;
				}
			}
			reset($HTTP_POST_VARS);	
			while(list($key, $val) = each($HTTP_POST_VARS)) 
			{

				if(substr($key,0,3)=='PLG' && $val)
				{
					if($data) $data .='|';
					$data .= substr($key,3).':'.$val.':xx:'.$cfg[substr($key,3)];
				}
			}

			return $data;
		}


		/**
		* include ALL plugins
		*/
		function include_plugins()
		{
			global $local_bo;
			$local_bo=$this;
			//die('hallo');
			if ($handle = opendir(PHPGW_SERVER_ROOT.'/jinn/plugins')) {

				/* This is the correct way to loop over the directory. */

				while (false !== ($file = readdir($handle))) 
				{ 
					if (substr($file,0,7)=='plugin.')
					{

						include(PHPGW_SERVER_ROOT.'/jinn/plugins/'.$file);
					}
				}
				closedir($handle); 
			}
		}

		/**
		* het plugins that hook with the given fieldtype
		*
		* @return array with plugins
		* @param string $fieldtype 
		*/
		function plugin_hooks($fieldtype)
		{
			if ($fieldtype=='blob') $fieldtype='text';

			if (count($this->plugins>0))
			{
				foreach($this->plugins as $plugin)
				{
					foreach($plugin['db_field_hooks'] as $hook)
					{
						if ($hook==$fieldtype) 
						{
							$plugin_hooks[]=array(
								'value'=>$plugin['name'],
								'name'=>$plugin['title']
							);
						}
					}

				}

				return $plugin_hooks;
			}
		}


	}


	?>
