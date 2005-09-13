<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
   Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

   phpGroupWare - http://www.phpgroupware.org

   This file is part of JiNN

   JiNN is free software; you can redistribute it and/or modify it under
   the terms of the GNU General Public License as published by the Free
   Software Foundation; Version 2 of the License.

   JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
   WARRANTY; without even the implied warranty of MERCHANTABILITY or 
   FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
   for more details.

   You should have received a copy of the GNU General Public License 
   along with JiNN; if not, write to the Free Software Foundation, Inc.,
   59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
   */

   class bouser 
   {
	  var $public_functions = Array
	  (
		 'object_update'		=> True,
		 'object_insert'		=> True,
		 'del_object'			=> True,
		 'save_object_config'	=> True,
		 'get_plugin_afa'		=> True,
		 'copy_object'			=> True
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
	  var $browse_settings;

	  var $repeat_input;
	  var $where_key;
	  var $where_value;
	  var $where_string;

	  function bouser()
	  {
		 $this->common = CreateObject('jinn.bocommon');
		 $this->current_config=$this->common->get_config();		

		 $this->so = CreateObject('jinn.sojinn');

		 $this->include_plugins();
		 $this->magick = CreateObject('jinn.boimagemagick.inc.php');	

		 $this->read_sessiondata();

		 $_form = $_POST['form'];
		 $_site_id = $_POST['site_id'];
		 $_site_object_id = $_POST['site_object_id'];

		 list($_where_string,$_where_key,$_where_value,$_repeat_input)=$this->common->get_global_vars(array('where_string','where_key','where_value','repeat_input'));

		 if(!empty($_repeat_input)) $this->repeat_input  = $_repeat_input;

		 if(!empty($_where_key))	$this->where_key  = $_where_key;

		 if(!empty($_where_value)) $this->where_value  = $_where_value;

		 if(!empty($_where_string)) 
		 {
			$this->where_string  = base64_decode($_where_string);
			$this->where_string_encoded  = $_where_string;
		 }

		 if (($_form=='main_menu')|| !empty($site_id)) $this->site_id  = $_site_id;
		 if (($_form=='main_menu') || !empty($site_object_id)) $this->site_object_id  = $_site_object_id;

		 if ($this->site_id) $this->site = $this->so->get_site_values($this->site_id);
		 if ($this->site_object_id) $this->site_object = $this->so->get_object_values($this->site_object_id);


	  }



	  function save_sessiondata()
	  {
		 $data = array(
			'message' => $this->message, 
			'site_id' => $this->site_id,
			'site_object_id' => $this->site_object_id,
			'browse_settings'=>	$this->browse_settings
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
			$this->browse_settings	= $data['browse_settings'];
		 }
	  }

	  function set_limits($limit_start,$limit_stop,$direction,$num_rows)
	  {

		 if ($limit_start>$limit_stop) unset($limit_stop);
		 if ($direction==">")
		 {
			$limit_start=$limit_stop;
			$limit_stop=($limit_stop+30);
			$limit="LIMIT $limit_start,30";
		 }
		 elseif ($direction=="<")
		 {
			$limit_start=($limit_start-30);
			$limit_stop=($limit_start+30);
			$limit="LIMIT $limit_start,30";
		 }
		 elseif ($direction=="<<")
		 {
			$limit_start=0;
			$limit_stop=30;
			$limit="LIMIT 0,30";
		 }
		 elseif ($direction==">>")
		 {
			$limit_start=$num_rows-30;
			$limit_stop=$num_rows;
			$limit="LIMIT $limit_start,30";
		 }
		 elseif (($limit_start) && ($limit_stop)) $limit="LIMIT $limit_start,".($limit_stop-$limit_start);
		 elseif (($limit_start) && (!$limit_stop)) $limit="LIMIT $limit_start,30";
		 elseif ((!$limit_start) && ($limit_stop)) $limit="LIMIT 0,$limit_stop";
		 else {
			$limit="LIMIT 0,30";
			$limit_start=0;
			$limit_stop=30;
		 }

		 $returnlimit = array
		 (
			'start'=>$limit_start,
			'stop'=>$limit_stop,
			'SQL'=>$limit
		 );

		 return $returnlimit;

	  }

	  //remove this one
	  function get_records($table,$where_key,$where_value,$offset,$limit,$value_reference,$order_by='',$field_list='*',$where_condition='')
	  {
		 if (!$value_reference)
		 {
			$value_reference='num';
		 }

		 $records = $this->so->get_record_values($this->site_id,$table,$where_key,$where_value,$offset,$limit,$value_reference,$order_by,$field_list,$where_condition);


		 return $records;
	  }

	  function object_insert()
	  {
		 $data=$this->http_vars_pairs($GLOBALS[HTTP_POST_VARS],$GLOBALS[HTTP_POST_FILES]);
		 $status=$this->so->insert_object_data($this->site_id,$this->site_object[table_name],$data);

		 $many_data=$this->http_vars_pairs_many($GLOBALS[HTTP_POST_VARS], $GLOBALS[HTTP_POST_FILES]);
		 $many_data['FLD'.$status['idfield']]=$status['id'];
		 $status_relations=$this->so->update_object_many_data($this->site_id, $many_data);

		 if ($status[status]==1)	$this->message['info']='Record successfully added';
		 else $this->message[error]=lang('Record NOT succesfully deleted. Unknown error');

		 $this->save_sessiondata();

		 if($_POST['continue'] && $status[where_string])
		 {
			$this->common->exit_and_open_screen('jinn.uiu_edit_record.display_form&where_string='.base64_encode($status[where_string]));
		 }
		 else
		 {

			if($_POST[repeat_input]=='true')
			{
			   $this->common->exit_and_open_screen('jinn.uiu_edit_record.display_form&repeat_input=true');
			}
			else
			{
			   $this->common->exit_and_open_screen('jinn.uiuser.index');
			}
		 }

	  }

	  function object_update()
	  {
		 /* exit and go to del function */
		 if($GLOBALS[HTTP_POST_VARS][delete])
		 {
			$this->del_object();
		 }

		 $where_key = $this->where_key;
		 $where_value = $this->where_value;
		 $where_string=$this->where_string;
		 $table=$this->site_object[table_name];

		 $many_data=$this->http_vars_pairs_many($GLOBALS[HTTP_POST_VARS], $GLOBALS[HTTP_POST_FILES]);

		 $status=$this->so->update_object_many_data($this->site_id, $many_data);

		 $data=$this->http_vars_pairs($GLOBALS[HTTP_POST_VARS], $GLOBALS[HTTP_POST_FILES]);

		 $status=$this->so->update_object_data($this->site_id, $table, $data, $where_key,$where_value,$where_string);

		 if ($status[status]==1)	$this->message[info]='Record succesfully saved';
		 else $this->message[error]='Record NOT succesfully saved';

		 $this->save_sessiondata();

		 if($_POST['continue'])
		 {
			$this->common->exit_and_open_screen('jinn.uiu_edit_record.display_form&where_string='.base64_encode($status[where_string]));
		 }
		 else
		 {
			$this->common->exit_and_open_screen('jinn.uiuser.index');
		 }
	  }

	  function del_object()
	  {
		 $table=$this->site_object[table_name];
		 $where_key=stripslashes($this->where_key);
		 $where_value=stripslashes($this->where_value);
		 $where_string=stripslashes($this->where_string);

		 $status=$this->so->delete_object_data($this->site_id, $table, $where_key,$where_value,$where_string);

		 if ($status==1)	$this->message[info]=lang('Record succesfully deleted');
		 else $this->message[error]=lang('Record NOT succesfully deleted. Unknown error');

		 $this->save_sessiondata();
		 $this->common->exit_and_open_screen('jinn.uiuser.index');
	  }

	  function copy_object()
	  {
		 $table=$this->site_object[table_name];
		 $where_key=$this->where_key;
		 $where_value=$this->where_value;

		 $status=$this->so->copy_object_data($this->site_id,$table,$where_key,$where_value);
		 if ($status==1)	$this->message[info]=lang('Record succesfully copied');
		 else $this->message[error]=lang('Record NOT succesfully copied. Unknown error');

		 $this->save_sessiondata();
		 $this->common->exit_and_open_screen('jinn.uiuser.index');
	  }


	  function extract_1w1_relations($string)
	  {
		 $relations_array = explode('|',$string);

		 foreach($relations_array as $relation)
		 {
			$relation_part=explode(':',$relation);
			if ($relation_part[0]=='1')
			{
			   $relation_arr[$relation_part[1]] = array
			   (
				  'type'=>$relation_part[0],
				  'field_org'=>$relation_part[1],
				  'related_with'=>$relation_part[3],
				  'display_field'=>$relation_part[4]
			   );
			}

		 }
		 return $relation_arr;
	  }

	  function extract_1wX_relations($string)
	  {
		 $relations_array = explode('|',$string);

		 foreach($relations_array as $relation)
		 {
			$relation_part=explode(':',$relation);
			if ($relation_part[0]=='2')
			{
			   $tmp=explode('.',$relation_part[1]);
			   $via_table=$tmp[0];
			   $tmp=explode('.',$relation_part[4]);
			   $display_table=$tmp[0];

			   $relation_arr[] = array
			   (
				  'type'=>$relation_part[0],
				  'via_primary_key'=>$relation_part[1],
				  'via_foreign_key'=>$relation_part[2],
				  'via_table'=>$via_table,
				  'foreign_key'=>$relation_part[3],
				  'display_field'=>$relation_part[4],
				  'display_table'=>$display_table
			   );
			}
		 }
		 return $relation_arr;
	  }
	  function get_related_field($relation_array)
	  {
		 $table_info=explode('.',$relation_array[related_with]);
		 $table=$table_info[0];
		 $related_field=$table_info[1];

		 $table_info2=explode('.',$relation_array[display_field]);
		 $table_display=$table_info2[0];
		 $display_field=$table_info2[1];

		 $allrecords=$this->get_records($table,'','','','','name',$display_field);

		 if(is_array($allrecords))
		 foreach ($allrecords as $record)
		 {
			$related_fields[]=array
			(
			   'value'=>$record[$related_field],
			   'name'=>$record[$display_field]
			);
		 }
		 return $related_fields;
	  }

	  function get_related_value($relation_array,$value)
	  {
		 $table_info=explode('.',$relation_array[related_with]);
		 $table=$table_info[0];
		 $related_field=$table_info[1];

		 $table_info2=explode('.',$relation_array[display_field]);
		 $table_display=$table_info2[0];
		 $display_field=$table_info2[1];

		 $allrecords=$this->get_records($table,'','','','','name',$display_field);


		 if(is_array($allrecords))
		 foreach ($allrecords as $record)
		 {
			if($record[$related_field]==$value) return $record[$display_field];
		 }
	  }

	  function http_vars_pairs($HTTP_POST_VARS,$HTTP_POST_FILES) 
	  {

		 while(list($key, $val) = each($HTTP_POST_VARS)) 
		 {
			if(substr($key,0,3)=='FLD')
			{
			   /* Check for plugin need and plugin availability */
			   if ($filtered_data=$this->get_plugin_sf($key,$HTTP_POST_VARS,$HTTP_POST_FILES))				
			   {
				  if ($filtered_data==-1) $filtered_data='';
				  $data[] = array
				  (
					 'name' => substr($key,3),
					 'value' =>  $filtered_data  //addslashes($val)
				  );
			   }
			   else // if there's no plugin, just save the vals
			   {
				  $data[] = array
				  (
					 'name' => substr($key,3),

					 'value' => addslashes($val) 
				  );
			   }
			}
		 }

		 return $data;

	  }


	  function http_vars_pairs_many($HTTP_POST_VARS) {

		 while(list($key, $val) = each($HTTP_POST_VARS)) {


			if(substr($key,0,3)=='MAN'||substr($key,0,5)=='FLDid')
			{

			   $data = array_merge($data,array
			   (
				  $key=> $val
			   ));
			}
		 }
		 return $data;
	  }		



	  function read_preferences($key)
	  {
		 $GLOBALS['phpgw']->preferences->read_repository();

		 $prefs = array();

		 if ($GLOBALS['phpgw_info']['user']['preferences']['jinn'])
		 {
			$prefs = $GLOBALS['phpgw_info']['user']['preferences']['jinn'][$key];
		 }
		 return $prefs;
	  }

	  function save_preferences($key,$prefs)
	  {
		 $GLOBALS['phpgw']->preferences->read_repository();

		 $GLOBALS['phpgw']->preferences->change('jinn',$key,$prefs);
		 $GLOBALS['phpgw']->preferences->save_repository(True);
	  }

	  /****************************************************************************\
	  * 	Config site_objects                                              *
	  \****************************************************************************/

	  function save_object_config()
	  {

		 $prefs_order_new=$GLOBALS[HTTP_POST_VARS][ORDER];
		 $prefs_show_hide_read=$this->read_preferences('show_fields');

		 $show_fields_entry=$this->site_object[object_id];

		 while(list($key, $x) = each($GLOBALS[HTTP_POST_VARS]))
		 {
			if(substr($key,0,4)=='SHOW')
			$show_fields_entry.=','.substr($key,4);
		 }

		 if($prefs_show_hide_read) 
		 {
			$prefs_show_hide_arr=explode('|',$prefs_show_hide_read);

			foreach($prefs_show_hide_arr as $pref_s_h)
			{

			   $pref_array=explode(',',$pref_s_h);
			   if($pref_array[0]!=$this->site_object[object_id])
			   {
				  $prefs_show_hide_new.=implode(',',$pref_array);
			   }
			}

			if($prefs_show_hide_new) $prefs_show_hide_new.='|';
			$prefs_show_hide_new.=$show_fields_entry;
		 }
		 else
		 {
			$prefs_show_hide_new=$show_fields_entry;
		 }

		 $this->save_preferences('show_fields',$prefs_show_hide_new);
		 $this->save_preferences('default_order',$prefs_order_new);

		 $this->common->exit_and_open_screen('jinn.uiuser.browse_objects');
	  }


	  /*--------------------------------------------------
	  FIXME all field related plugins must move to dedicated class
	  -------------------------------------------*
	  
	  /**
	  * get storage filter from plugin 
	  */
	  function get_plugin_sf($key,$HTTP_POST_VARS,$HTTP_POST_FILES)
	  {
		 global $local_bo;
		 $local_bo=$this;
		 $plugins=explode('|',str_replace('~','=',$this->site_object['plugins']));

		 foreach($plugins as $plugin)
		 {
			$sets=explode(':',$plugin);

			/* make plug config array for this field */
			if($sets[3]) $conf_str = explode(';',$sets[3]);

			if(is_array($conf_str))
			{
			   foreach($conf_str as $conf_entry)
			   {
				  list($conf_key,$val)=explode('=',$conf_entry);	
				  $conf_arr[$conf_key]=$val;
			   }
			}

			if (substr($key,3)==$sets[0])
			{
			   if(!$data=@call_user_func('plg_sf_'.$sets[1],$key,$HTTP_POST_VARS,$HTTP_POST_FILES,$conf_arr)) return;
			}
		 }
		 return $data;
	  }


	  /**
	  * get readonly view function from plugin 
	  */
	  function get_plugin_ro($fieldname,$value,$where_val_encoded,$attr)
	  {
		 //			die($fieldname);
		 global $local_bo;
		 $local_bo=$this;
		 $plugins=explode('|',str_replace('~','=',$this->site_object['plugins']));
		 foreach($plugins as $plugin)
		 {	
			$sets=explode(':',$plugin);

			/* make plug config array for this field */
			if($sets[3]) $conf_str = explode(';',$sets[3]);
			if(is_array($conf_str))
			{
			   foreach($conf_str as $conf_entry)
			   {
				  list($key,$val)=explode('=',$conf_entry);	
				  $conf_arr[$key]=$val;		
			   }
			}

			if ($fieldname==$sets[0])
			{
			   if(!$new_value=@call_user_func('plg_ro_'.$sets[1],$value,$conf_arr,$where_val_encoded,$fieldname)) 
			   {
			   }
			}
		 }
		 if (!$new_value)
		 {
			$new_value=$value;
		 }

		 return $new_value;
	  }


	  /**
	  * get browse view function from plugin 
	  */
	  function get_plugin_bv($fieldname,$value,$where_val_encoded,$fieldname)
	  {
		 global $local_bo;
		 $local_bo=$this;
		 $plugins=explode('|',str_replace('~','=',$this->site_object['plugins']));
		 foreach($plugins as $plugin)
		 {	
			$sets=explode(':',$plugin);

			/* make plug config array for this field */
			if($sets[3]) $conf_str = explode(';',$sets[3]);
			if(is_array($conf_str))
			{
			   foreach($conf_str as $conf_entry)
			   {
				  list($key,$val)=explode('=',$conf_entry);	
				  $conf_arr[$key]=$val;		
			   }
			}

			if ($fieldname==$sets[0])
			{
			   if(!$new_value=@call_user_func('plg_bv_'.$sets[1],$value,$conf_arr,$where_val_encoded,$fieldname)) 
			   {
			   }
			}
		 }
		 if (!$new_value)
		 {
			$new_value=$value;
			if(strlen($new_value)>15)
			{
			   $new_value=strip_tags($new_value);
			   $new_value = substr($new_value,0,15). ' ...';
			}
		 }
		 return $new_value;

	  }

	  /**
	  * get input function from plugin 
	  */
	  function get_plugin_fi($input_name,$value,$type,$attr_arr)
	  {
		 global $local_bo;
		 $local_bo=$this;

		 $plugins=explode('|',str_replace('~','=',$this->site_object['plugins']));
		 foreach($plugins as $plugin)
		 {	
			$sets=explode(':',$plugin);

			/* make plug config array for this field */
			if($sets[3]) $conf_str = explode(';',$sets[3]);
			if(is_array($conf_str))
			{
			   foreach($conf_str as $conf_entry)
			   {
				  list($key,$val)=explode('=',$conf_entry);	
				  $conf_arr[$key]=$val;		
			   }
			}

			if (substr($input_name,3)==$sets[0])
			{
			   //FIXME all plugins must get an extra argument in the sf_func
			   if(!$input=@call_user_func('plg_fi_'.$sets[1],$input_name,$value,$conf_arr,$attr_arr)) 
			   {
			   }
			}
		 }

		 if (!$input) $input=call_user_func('plg_fi_def_'.$type,$input_name,$value,'',$attr_arr);

		 return $input;

	  }

	  /**
	  * get autonome form action function from plugin 
	  */
	  function get_plugin_afa()
	  {
		 global $local_bo;
		 $local_bo=$this;

		 $action_plugin_name=$_GET[plg];

		 $plugins=explode('|',str_replace('~','=',$this->site_object['plugins']));
		 foreach($plugins as $plugin)
		 {	
			$sets=explode(':',$plugin);

			if($sets[3]) $conf_str = explode(';',$sets[3]);
			if(is_array($conf_str))
			{
			   unset($conf_arr);
			   foreach($conf_str as $conf_entry)
			   {
				  list($key,$val)=explode('=',$conf_entry);	
				  $conf_arr[$key]=$val;		
			   }
			}

			if ($action_plugin_name==$sets[1])
			{
			   $call_plugin=$sets[1];
			   break;
			}
		 }

		 if($call_plugin)
		 {
			//FIXME all plugins must get an extra argument in the sf_func
			$success=@call_user_func('plg_afa_'.$sets[1],$_GET[where],$_GET[attributes],$conf_arr);
		 }

		 if ($succes)
		 {
			$this->message[info]=lang('Action was succesful.');

			$this->save_sessiondata();
			$this->common->exit_and_open_screen('jinn.uiuser.index');
		 }
		 else
		 {
			$this->message[error]=lang('Action was not succesful. Unknown error');

			$this->save_sessiondata();
			$this->common->exit_and_open_screen('jinn.uiuser.index');
		 }
	  }

	  /**
	  * include ALL plugins
	  */
	  function include_plugins()
	  {
		 global $local_bo;
		 $local_bo=$this;
		 if ($handle = opendir(PHPGW_SERVER_ROOT.'/jinn/plugins')) {

			/* This is the correct way to loop over the directory. */

			while (false !== ($file = readdir($handle))) 
			{ 
			   if (substr($file,0,7)=='plugin.')
			   {

				  include_once(PHPGW_SERVER_ROOT.'/jinn/plugins/'.$file);
			   }
			}
			closedir($handle); 
		 }
	  }

   }
?>
