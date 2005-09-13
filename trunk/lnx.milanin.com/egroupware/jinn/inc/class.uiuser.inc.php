<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
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

   class uiuser 
   {
	  var $public_functions = Array
	  (
		 'index'				=> True,
		 'add_edit_object'		=> True,
		 'browse_objects'		=> True,
		 'file_download'		=> True,
		 'config_objects'		=> True,
		 'img_popup'			=> True,
		 'save_object_config'	=> True
	  );

	  var $bo;
	  var $ui;
	  var $template;


	  function uiuser()
	  {
		 $this->bo = CreateObject('jinn.bouser');

		 $this->template = $GLOBALS['phpgw']->template;

		 $this->ui = CreateObject('jinn.uicommon');

		 if($this->bo->so->config[server_type]=='dev')
		 {
			$dev_title_string='<font color="red">'.lang('Development Server').'</font> ';
		 }
		 $this->ui->app_title=$dev_title_string;//.lang('Moderator Mode');
	  }

	  /********************************
	  *  create the default index page                                                          
	  */
	  function index()
	  {

		 if ($this->bo->site_object_id && $this->bo->site_object['parent_site_id']==$this->bo->site_id )
		 {
			$this->bo->save_sessiondata();
			$this->bo->common->exit_and_open_screen('jinn.uiuser.browse_objects');
		 }
		 else
		 {

			if (!$this->bo->site_id)
			{
			   $this->bo->message['info']=lang('Select site to moderate');
			}
			else 
			{
			   $this->bo->message['info']=lang('Select site-object to moderate');
			}

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);

			$this->ui->header('Index');
			$this->ui->msg_box($this->bo->message);

			$this->main_menu();
			$this->bo->save_sessiondata();
		 }
	  }

	  /****************************************************************************\
	  * create main menu                                                           *
	  \****************************************************************************/

	  function main_menu()
	  {
		 $this->template->set_file(array(
			'main_menu' => 'main_menu.tpl'));

			// get sites for user and group and make options
			$sites=$this->bo->common->get_sites_allowed($GLOBALS['phpgw_info']['user']['account_id']);

			if(is_array($sites))
			{
			   foreach($sites as $site_id)
			   {
				  $site_arr[]=array(
					 'value'=>$site_id,
					 'name'=>$this->bo->so->get_site_name($site_id)
				  );
			   }
			}
			else
			{
			   $this->bo->message[error]=lang('There is not site you have access to. Ask your administrator to give you access to your site of site-objects or check if any site exist');

			   if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
			   {
				  $this->bo->message[info]='<a href="'.$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiadmin.add_edit_site').'">'.lang('Create a new site now').'</a>';
			   }
			   else
			   {

				  $this->bo->message[info]='';
			   }
			   $this->ui->msg_box($this->bo->message);


			}

			$site_options=$this->ui->select_options($site_arr,$this->bo->site_id,true);


			if ($this->bo->site_id)
			{
			   $objects=$this->bo->common->get_objects_allowed($this->bo->site_id, $GLOBALS['phpgw_info']['user']['account_id']);

			   if (is_array($objects))
			   {
				  foreach ( $objects as $object_id) 
				  {
					 $objects_arr[]=array(
						'value'=>$object_id,
						'name'=>$this->bo->so->get_object_name($object_id)
					 );
				  }
			   }

			   $object_options=$this->ui->select_options($objects_arr,$this->bo->site_object_id,true);

			}
			else
			{
			   unset($this->bo->site_object_id);
			}

			$this->template->set_var('jinn_main_menu',lang('JiNN Main Menu'));

			// set menu
			$this->template->set_var('site_objects',$object_options);
			$this->template->set_var('site_options',$site_options);

			$this->template->set_var('main_form_action',$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.index'));
			$this->template->set_var('select_site',lang('select site'));
			$this->template->set_var('select_object',lang('select_object'));
			$this->template->set_var('go',lang('go'));

			/* set admin shortcuts */
			// if site if site admin
			if($this->bo->site_id && $userisadmin)
			{
			   $admin_site_link='<br><a href="'.$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiadminaddedit.').'">'.
				  lang('admin:: edit site').'</a>';
			}
			$this->template->set_var('admin_site_link',$admin_site_link);
			$this->template->set_var('admin_object_link',$admin_object_link);

			$this->template->pparse('out','main_menu');

		 }


		 function browse_objects()
		 {
			if($this->bo->site_object[max_records]==1)
			{
			   $columns=$this->bo->so->site_table_metadata($this->bo->site_id, $this->bo->site_object['table_name']);
			   if(!is_array($columns)) $columns=array();

			   /* walk through all table columns and fill different array */
			   foreach($columns as $onecol)
			   {
				  //create more simple col_list with only names //why
				  $all_col_names_list[]=$onecol[name];

				  /* check for primaries and create array */
				  if (eregi("primary_key", $onecol[flags]) && $onecol[type]!='blob') // FIXME howto select long blobs
				  {						
					 $pkey_arr[]=$onecol[name];
				  }
				  elseif($onecol[type]!='blob') // FIXME howto select long blobs
				  {
					 $akey_arr[]=$onecol[name];
				  }
			   }

			   $records=$this->bo->get_records($this->bo->site_object[table_name],'','',0,1,'name',$orderby,'*',$where_condition);
			   if(count($records)>0)
			   {
				  foreach($records as $recordvalues)
				  {
					 unset($where_string);
					 if(count($pkey_arr)>0)
					 {
						foreach($pkey_arr as $pkey)
						{
						   if($where_string) $where_string.=' AND ';
						   $where_string.= '('.$pkey.' = \''. $recordvalues[$pkey].'\')';
						}

						$where_string=base64_encode($where_string);
					 }
				  }
			   
				  $this->bo->common->exit_and_open_screen('jinn.uiu_edit_record.view_record&where_string='.$where_string);
			   }
	
			   else
			   {
				  $this->list_records();
			   }
			}
			else
			{
   			   $this->list_records();
			}
			
		 }

		 /*******************************\
		 * 	Browse through site_objects  *
		 \*******************************/

		 function list_records()
		 {
			if(!$this->bo->so->test_JSO_table($this->bo->site_object))
			{
			   unset($this->bo->site_object_id);
			   $this->bo->message['error']=lang('Failed to open table. Please check if table <i>%1</i> still exists in database',$this->bo->site_object['table_name']);

			   $this->bo->save_sessiondata();
			   $this->bo->common->exit_and_open_screen('jinn.uiuser.index');
			}				

			$this->ui->header('browse through objects');
			$this->ui->msg_box($this->bo->message);

			$this->main_menu();	

			$this->template->set_file(array(
			   'list_records' => 'list_records.tpl',
			));

			$this->template->set_block('list_records','header','header');
			$this->template->set_block('list_records','column_name','column_name');
			$this->template->set_block('list_records','column_field','column_field');
			$this->template->set_block('list_records','row','row');
			$this->template->set_block('list_records','empty_row','empty_row');
			$this->template->set_block('list_records','footer','footer');
			
			$pref_columns_str=$this->bo->read_preferences('show_fields'); 
			$default_order=$this->bo->read_preferences('default_order');

			list($offset,$asc,$orderby,$filter,$navdir,$limit_start,$limit_stop,$direction,$show_all_cols,$search)=$this->bo->common->get_global_vars(array('offset','asc','orderby','filter','navdir','limit_start','limit_stop','direction','show_all_cols','search'));

			if(!$offset) $offset= $this->bo->browse_settings['offset'];
			if(!$asc)    $asc=    $this->bo->browse_settings['asc']; // FIXME remove?
			if(!$filter) $filter= $this->bo->browse_settings['filter'];
			if(!$orderby)  $orderby = $this->bo->browse_settings['orderby'];
			$this->bo->browse_settings = array
			(
			   'offset'=>$offset,
			   'range'=>$range,
			   'navdir'=>$navdir, // FIXME test
			   'orderby'=>$orderby,
			   'filter'=>$filter
			);

			if(!$orderby && $default_order) $orderby=$default_order;

			$num_rows=$this->bo->so->num_rows_table($this->bo->site_id,$this->bo->site_object['table_name']);

			$limit=$this->bo->set_limits($limit_start,$limit_stop,$direction,$num_rows);

			$this->template->set_var('limit_start',$limit['start']);
			$this->template->set_var('limit_stop',$limit['stop']);
			$this->template->set_var('orderby',$orderby);
			$this->template->set_var('menu_action',$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiuser.browse_objects'));
			$this->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			$this->template->set_var('start_at',lang('start at record'));
			$this->template->set_var('stop_at',lang('stop at record'));
			$this->template->set_var('search_for',lang('search for string'));
			$this->template->set_var('show',lang('show'));
			$this->template->set_var('search',lang('search'));
			$this->template->set_var('action_config_table',$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiuser.config_table'));
			$this->template->set_var('lang_config_this_tableview',lang('Configure this tableview'));
			$this->template->set_var('search_string',$search);
			//			$this->template->set_var('show_all_cols',$show_all_cols);
			$this->template->set_var('lang_Actions',lang('Actions'));
			$this->template->set_var('edit',lang('edit'));
			$this->template->set_var('delete',lang('delete'));
			$this->template->set_var('copy',lang('copy'));
			$this->template->set_var('show_all_cols',$show_all_cols);

			$this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->template->set_var('table_title',$this->bo->site_object[name]);

			$popuplink=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.img_popup');

			$this->template->set_var('popuplink',$popuplink);

			$LIMIT="LIMIT $limit[start],$limit[stop]";

			/* get one with many relations */
			$relation1_array=$this->bo->extract_1w1_relations($this->bo->site_object['relations']);
			if (count($relation1_array)>0)
			{
			   foreach($relation1_array as $relation1)
			   {
				  $fields_with_relation1[]=$relation1[field_org];
			   }
			}

			/* get prefered columnnames to show */
			if ($pref_columns_str)
			{
			   $all_prefs_show_hide=explode('|',$pref_columns_str);
			   foreach($all_prefs_show_hide as $pref_show_hide)
			   {
				  $pref_show_hide_arr=explode(',',$pref_show_hide);
				  if($pref_show_hide_arr[0]==$this->bo->site_object_id)
				  {
					 $pref_columns=array_slice($pref_show_hide_arr,1);

					 //is this necessary?	
					 foreach($pref_columns as $pref_col)
					 {
						$valid_pref_columns[]=array('name'=>$pref_col);
					 }

				  }
			   }
			}


			$columns=$this->bo->so->site_table_metadata($this->bo->site_id, $this->bo->site_object['table_name']);
			if(!is_array($columns)) $columns=array();

			/* walk through all table columns and fill different array */
			foreach($columns as $onecol)
			{
				
			   //create more simple col_list with only names //why
			   $all_col_names_list[]=$onecol[name];

			   /* check for primaries and create array */
			   if (eregi("primary_key", $onecol[flags]) && $onecol[type]!='blob') // FIXME howto select long blobs
			   {						
				  $pkey_arr[]=$onecol[name];
			   }
			   elseif($onecol[type]!='blob') // FIXME howto select long blobs
			   {
				  $akey_arr[]=$onecol[name];
			   }

			   /* format search condition */
			   if ($search)
			   {
				  if ($where_condition)
				  {
					 $where_condition.= " OR {$onecol[name]} LIKE '%$search%'";
					 $limit="";
				  }
				  else
				  {
					 $where_condition = " {$onecol[name]} LIKE '%$search%'";
				  }
			   }

			}

			/* which/how many column to show, all, the prefered, or the default thirst 4 */
			if ($show_all_cols=='True')
			{
			   $col_list=$columns;
			}
			elseif($pref_columns)
			{
			   $col_list=$valid_pref_columns;
			}
			else
			{
			   $col_list=array_slice($columns,0,4);
			}



			/*	check if orderbyfield exist else drop orderby it	*/
			if(!in_array(trim(substr($orderby,0,(strlen($orderby)-4))),$all_col_names_list)) unset($orderby);
			//	unset($all_col_names_list);


			// make columnheaders
			foreach ($col_list as $col)
			{

			   //--- this is a special hack for the hide-this-field-plugin ----//
			   $testvalue=$this->bo->get_plugin_bv($col[name],'x',$where_string,$col[name]);
			   if($testvalue=='__hide__')
			   {
				  continue ;
			   }
   
			   $col_names_list[]=$col[name];
			   unset($orderby_link);
			   unset($orderby_image);
			   if ($col[name] == trim(substr($orderby,0,(strlen($orderby)-4))))
			   {
				  if (substr($orderby,-4)== 'DESC')
				  {
					 $orderby_link = $col[name].' ASC';
					 $orderby_image = '<img src="'. $GLOBALS['phpgw']->common->image('jinn','desc.png').'" border="0">';
				  }
				  else 
				  {
					 $orderby_link = $col[name].' DESC';
					 $orderby_image = '<img src="'. $GLOBALS['phpgw']->common->image('jinn','asc.png').'" border="0">';
				  }
			   }
			   else
			   {
				  $orderby_link = $col[name].' ASC';
			   }

			   $this->template->set_var('colhead_bg_color',$GLOBALS['phpgw_info']['theme']['th_bg']);
			   $this->template->set_var('colhead_order_link',$GLOBALS[phpgw]->link("/index.php","menuaction=jinn.uiuser.browse_objects&orderby=$orderby_link&search=$search&limit_start=$limit_start&limit_stop=$limit_stop&show_all_cols=$show_all_cols"));
			   $this->template->set_var('colhead_name',str_replace('_','&nbsp;',$col[name]));
			   $this->template->set_var('colhead_order_by_img',$orderby_image);

			   $this->template->parse('colnames','column_name',true);
			}

			$this->template->parse('out','header');
			$this->template->pparse('out','header');

			//------------ end header --------------//

			if(!is_array($pkey_arr))
			{
			   $pkey_arr=$akey_arr;
			   unset($akey_arr);
			}

			$records=$this->bo->get_records($this->bo->site_object[table_name],'','',$limit[start],$limit[stop],'name',$orderby,'*',$where_condition);

			if(count($records)>0)
			{
			   foreach($records as $recordvalues)
			   {
				  unset($where_string);
				  if(count($pkey_arr)>0)
				  {
					 foreach($pkey_arr as $pkey)
					 {
						if($where_string) $where_string.=' AND ';
						$where_string.= '('.$pkey.' = \''. $recordvalues[$pkey].'\')';
					 }

					 $where_string=base64_encode($where_string);
				  }

				  if ($bgclr==$GLOBALS['phpgw_info']['theme']['row_off'])
				  {
					 $bgclr='#ffffff';
				  }
				  else
				  {
					 $bgclr=$GLOBALS['phpgw_info']['theme']['row_off'];
				  }

				  if(count($recordvalues)>0)
				  {
						// action_links
						$this->template->set_var('colfield_bg_color',$bgclr);
						$this->template->set_var('colfield_lang_edit',lang('edit'));
						$this->template->set_var('colfield_edit_link',$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiu_edit_record.display_form&where_string='.$where_string));
						$this->template->set_var('colfield_edit_img_src',$GLOBALS[phpgw]->common->image('phpgwapi','edit'));

						$this->template->set_var('colfield_lang_view',lang('view'));
						$this->template->set_var('colfield_view_link',$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiu_edit_record.view_record&where_string='.$where_string));
						$this->template->set_var('colfield_view_img_src',$GLOBALS[phpgw]->common->image('phpgwapi','view'));
						
						$this->template->set_var('colfield_lang_delete',lang('delete'));
						$this->template->set_var('colfield_delete_link',$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.bouser.del_object&where_string='.$where_string));
						$this->template->set_var('colfield_lang_confirm',lang('Are you sure?'));
						$this->template->set_var('colfield_delete_img_src',$GLOBALS[phpgw]->common->image('phpgwapi','delete'));

						$this->template->set_var('colfields','');

						foreach($col_names_list  as $onecolname)
						{
						   $recordvalue=$recordvalues[$onecolname];
						   if (is_array($fields_with_relation1) && in_array($onecolname,$fields_with_relation1))
						   {
							  $related_value=$this->bo->get_related_value($relation1_array[$onecolname],$recordvalue);
							  $recordvalue= '<i>'.$related_value.'</i> ('.$recordvalue.')';
						   }
						   else
						   {	
							  $recordvalue=$this->bo->get_plugin_bv($onecolname,$recordvalue,$where_string,$onecolname);
						   }

						   if (empty($recordvalue))
						   {
							  $recordvalue="&nbsp;";

						   }

						   $this->template->set_var('colfield_bg_color',$bgclr);
						   $this->template->set_var('colfield_value',$recordvalue);

						   $this->template->parse('colfields','column_field',true);
						}

						$this->template->parse('rows','row',true);
						$this->template->pparse('out','row');

			

				  }// end if table has fields


			   }//end foreach row

			}
			else
			{
			   $this->template->set_var('lang_no_records',lang('No records found'));
			   $this->template->set_var('colspan',(count($col_names_list)+3));
			   $this->template->pparse('out','empty_row');
			}

			$this->template->parse('out','footer');
			$this->template->pparse('out','footer');

			unset($this->message);

			unset($this->bo->message);
			$this->bo->save_sessiondata();
		 }

		 /****************************************************************************\
		 * 	Config site_objects                                              *
		 \****************************************************************************/

		 function config_objects()
		 {
			$this->ui->header(lang('configure browse view'));

			if(!$this->bo->site_object_id)
			{
			   $this->bo->message['error']=lang('No object selected. No able to configure this view');
			   $this->ui->msg_box($this->bo->message);
			   $this->main_menu();	

			}
			else
			{
			   $this->ui->msg_box($this->bo->message);
			   $this->main_menu();	
			   $main = CreateObject('jinn.uiconfig',$this->bo);
			   $main->show_fields();
			}

			$this->bo->save_sessiondata();
		 }

		 function file_download()
		 {

			$file_name=$_GET['file'];

			if(file_exists($file_name))
			{

			   $browser=	CreateObject('phpgwapi.browser'); 

			   $browser->content_header($file_name);

			   $handle = fopen ($file_name, "r");
			   $contents = fread ($handle, filesize ($file_name));
			   fclose ($handle);

			   echo $contents;
			}
			else
			{
			   die(lang('ERROR: the file %1 doesn\'t exists, please contact the webmaster',$file_name));
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		 }

		 function img_popup()
		 {
			$attributes=base64_decode($_GET[attr]);
			$new_path=base64_decode($_GET[path]);
			$this->template->set_file(array(
			   'imgpopup' => 'imgpopup.tpl'
			));

			$this->template->set_var('img',$new_path);
			$this->template->set_var('ctw',lang('close this window'));
			$this->template->pparse('out','imgpopup');
		 }

	  }
   ?>
