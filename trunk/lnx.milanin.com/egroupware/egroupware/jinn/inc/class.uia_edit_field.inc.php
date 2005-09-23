<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for eGroupWare
   Copyright (C)2002, 2004 Pim Snel <pim@lingewoud.nl>

   eGroupWare - http://www.eGroupware.org

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

   // FIXME do we need to extend?
   class uia_edit_field 
   {

	  var $public_functions = Array(
		 'display'=> True
	  );
	  var $where_key;
	  var $where_value;
	  var $parent_site_id;
	  var $bool_edit_record=false;
	  var $valid_table_name;
	  var $object_values;
	  var $table_array;
	  var $available_tables;

	  // FIXME Can't we get the bo from somewhere else?
	  function uia_edit_field()
	  {
		 if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
		 {
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiuser.index'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		 }

		 $this->bo = CreateObject('jinn.boadmin');
		 $this->template = $GLOBALS['phpgw']->template;

		 $this->ui = CreateObject('jinn.uicommon');

		 if($this->bo->so->config[server_type]=='dev')
		 {
			$dev_title_string='<font color="red">'.lang('Development Server').'</font> ';
		 }

	  }

	  function display()
	  {
		 $this->where_key=$where_key;
		 $this->where_value=$where_value;

		 $this->template->set_file(array
		 (
			'form_site' => 'frm_edit_object.tpl',
		 ));

		 if ($this->where_key && $this->where_value)
		 {
			$this->bool_edit_record=true;
			$this->object_values=$this->bo->so->get_object_values($this->where_value);
			$this->parent_site_id=$this->object_values[parent_site_id];
		 }
		 else
		 {
			$this->parent_site_id=$_GET[parent_site_id];
		 }

		 $this->available_tables=$this->bo->so->site_tables_names($this->parent_site_id);

		 $this->template->set_block('form_site','header','header');
		 $this->template->set_block('form_site','rows','rows');

		 $this->template->set_block('form_site','plugins_header','plugins_header');
		 $this->template->set_block('form_site','plugins_row','plugins_row');
		 $this->template->set_block('form_site','plugins_footer','plugins_footer');

		 $this->template->set_block('form_site','relations_header','relations_header');
		 $this->template->set_block('form_site','relations1','relations1');
		 $this->template->set_block('form_site','relations2','relations2');
		 $this->template->set_block('form_site','relations3','relations3');
		 $this->template->set_block('form_site','relation_defined1','relation_defined1');
		 $this->template->set_block('form_site','relation_defined2','relation_defined2');
		 $this->template->set_block('form_site','relation_defined3','relation_defined3');
		 $this->template->set_block('form_site','relations_footer','relations_footer');

		 $this->template->set_block('form_site','footer','form_footer');

		 $this->render_header();
		 $this->render_body();
		 $this->render_footer();

		 $this->template->pparse('out','header');
		 $this->template->pparse('out','row');

		 if($this->bool_edit_record && $this->valid_table_name)
		 {
			$this->render_plugins();
			$this->render_relations();

			$this->template->pparse('out','plugins_header');
			$this->template->pparse('out','plugins_rows');
			$this->template->pparse('out','plugins_footer');
			$this->template->pparse('out','relations_header');
			if($this->type1_num) $this->template->pparse('out','relations_defined1');
			$this->template->pparse('out','relations1');
			if($this->type2_num) $this->template->pparse('out','relations_defined2');
			$this->template->pparse('out','relations2');
			if($this->type3_num)$this->template->pparse('out','relations_defined3');
			$this->template->pparse('out','relations3');
			$this->template->pparse('out','relations_footer');
		 }
		 $this->template->pparse('out','footer');
	  }

	  function render_header()
	  {
		 if ($this->bool_edit_record)
		 {
			$form_action = $GLOBALS[phpgw]->link('/index.php',"menuaction=jinn.boadmin.update_phpgw_jinn_site_objects");
			$action=lang('edit '. 'phpgw_jinn_site_objects');
			$where_key_form='<input type="hidden" name="where_key" value="'.$this->where_key.'">';
			$where_value_form='<input type="hidden" name="where_value" value="'.$this->where_value.'">';
		 }
		 else
		 {
			$form_action = $GLOBALS[phpgw]->link('/index.php',"menuaction=jinn.boadmin.insert_phpgw_jinn_site_objects");
			$action=lang('add '. 'phpgw_jinn_site_objects' );
		 }

		 $this->template->set_var('form_action',$form_action);
		 $this->template->set_var('where_key_form',$where_key_form);
		 $this->template->set_var('where_value_form',$where_value_form);
		 $this->template->parse('out','header');
	  }

	  function render_body()
	  {
		 $fields=$this->bo->so->phpgw_table_metadata('phpgw_jinn_site_objects');

		 if($this->bool_edit_record)
		 {
			$values_object= $this->bo->get_phpgw_records('phpgw_jinn_site_objects',$this->where_key,$this->where_value,'','','name');
		 }

		 foreach ($fields as $testone => $fieldproperties)
		 {
			$edit_value=$values_object[0][$fieldproperties[name]];

			$input_name='FLD'.$fieldproperties[name];
			$display_name = lang(ucfirst(strtolower(ereg_replace("_", " ", $fieldproperties[name]))));
			$input_max_length=' maxlength="'. $fieldproperties[len].'"';
			$input_length=$fieldproperties[len];
			$value=$values_object[0][$fieldproperties[name]];

			if ($input_length>40)
			{
			   $input_length=40;
			}

			if (eregi("auto_increment", $fieldproperties[flags]) || $fieldproperties['default']=="nextval('seq_phpgw_jinn_site_objects'::text)")
			{
			   if (!$value)
			   {
				  $display_value=lang('automatic');
			   }
			   else
			   {
				  $display_value=$value;
			   }

			   $input='<input type="hidden" name="'.$input_name.'" value="'.$value.'">'.$display_value;
			}

			elseif ($fieldproperties[name]=='parent_site_id')
			{
			   if($value) // when we are editing
			   {
				  $parent_site_name=$this->bo->so->get_site_name($value);
				  $this->parent_site_id=$value; //id for further use in formgeneration
				  $input="<input type=hidden name=\"$input_name\" value=\"$value\">";
				  $input.=$parent_site_name;
			   }
			   elseif($this->parent_site_id) //when we are adding
			   {
				  $parent_site_name=$this->bo->so->get_site_name($this->parent_site_id);
				  $input='<input type=hidden name="'.$input_name.'" value="'.$this->parent_site_id.'">';
				  $input.=$parent_site_name;
			   }
			}
			elseif ($fieldproperties[name]=='table_name')
			{
			   $table_name=$value;
			   $tables=$this->available_tables;

			   if(!is_array($tables[0]))
			   {
				  $error_msg='<font color=red>'.lang('Could not find any tables! Check your database name, database username or database password or create one or more  tables in the database.').'</font><br>';

				  $input=$error_msg;
			   }
			   else
			   {
				  foreach($tables as $table)
				  {
					 $tables_check_arr[]=$table[table_name];
					 $this->table_array[]=array
					 (
						'name'=> $table[table_name],
						'value'=> $table[table_name]
					 );
				  }

				  if($this->bool_edit_record && in_array($table_name,$tables_check_arr))
				  {
					 $this->valid_table_name=true;
				  }

				  elseif(!$this->bool_edit_record && !$value)
				  {
					 $this->valid_table_name=true;
				  }
				  else
				  {
					 $error_msg='<font color=red>'.lang('Tablename <i>%1</i> is not correct. Probably the tablename has changed or or the table is deleted. Please select a new table or delete this object',$table_name).'</font><br>';
				  }

				  $input=$error_msg.'<select name="'.$input_name.'">';

					 $input.=$this->ui->select_options($this->table_array,$value,false);
					 $input.='</select>';
			   }
			}
			elseif ($fieldproperties[name]=='upload_path')
			{
			   $input='<input type="text" name="'.$input_name.'" size="'.$input_length.'" $input_max_length" value="'.$value.'">';
			}
			elseif ($fieldproperties[type]=='varchar' || $fieldproperties[type]=='string')
			{
			   $input='<input type="text" name="'.$input_name.'" size="'.$input_length.'" input_max_length" value="'.$value.'">';
			}
			elseif ($fieldproperties[name]=='max_records')
			{
			   unset($selected);
			   if($value==1) $selected='selected';
			   $input='<select name="'.$input_name.'"><option value="">'.lang('unlimited').'</option><option '.$selected.' value="1">'.lang('only one').'</option></select>';
			   
			}
			elseif ($fieldproperties[name]=='serialnumber')
			{
			   $input='<input type="hidden" name="'.$input_name.'" value="'.time().'">'.$value;
			}
			elseif ($fieldproperties[type]=='int')
			{
			   $input='<input type="text" name="'.$input_name.'" size="10" value="'.$value.'">';
			}
			elseif ($fieldproperties[name]=='help_information')
			{
			   continue;
			}
			elseif ($fieldproperties[name]=='relations')
			{
			   continue;
			}

			elseif($fieldproperties[name]=='plugins') 				
			{
			   continue;
			}

			// when it doesn't fit anywhere
			else
			{
			   $value = ereg_replace ("(<br />|<br/>)","",$value);
			   $input='<textarea name="'.$input_name.'" cols="60" rows="15">'.$value.'</textarea>';
			}

			if ($row_color==$GLOBALS['phpgw_info']['theme']['row_on'])
			{
			   $row_color=$GLOBALS['phpgw_info']['theme']['row_off'];
			}
			else
			{
			   $row_color=$GLOBALS['phpgw_info']['theme']['row_on'];
			}
			$this->template->set_var('row_color',$row_color);
			$this->template->set_var('input',$input);
			$this->template->set_var('fieldname',$display_name);

			$this->template->parse('row','rows',true);
		 }

	  }

	  function render_plugins()
	  {
		 $value=$this->object_values[plugins];
		 $table_name=$this->object_values[table_name];

		 if ($this->bool_edit_record && $this->valid_table_name)
		 {
			if(!$value) $value='TRUE';

			$hidden_value='<input type="hidden" name="FLDplugins" value="'.$value.'">';

			if ($fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table_name))
			{
			   $this->template->set_var('lang_fields',lang('field'));
			   $this->template->set_var('hidden_value',$hidden_value);
			   $this->template->set_var('lang_field_plugin',lang('Field Plugin'));
			   $this->template->set_var('lang_field_plugins',lang('Field Plugins'));
			   $this->template->parse('out','plugins_header');

			   $plugin_settings=explode('|',$value);

			   foreach($fields as $field)
			   {

				  unset($sets);
				  unset($plg_name);
				  unset($plg_conf);
				  if (is_array($plugin_settings))
				  {
					 foreach($plugin_settings as $setting)
					 {
						$sets=explode(':',$setting);
						if ($sets[0]==$field['name'])
						{
						   $plg_name=$sets[1];
						   $plg_conf=$sets[3];
						}

					 }
				  }

				  $this->template->set_var('field_name',$field['name']);

				  $plugin_hooks=$this->bo->plugin_hooks($field['type']);
				  $options=$this->ui->select_options($plugin_hooks,$plg_name,true);

				  if ($field['name']!='id' && $options)
				  {
					 $popup_onclick='parent.window.open(\''.$GLOBALS['phpgw']->link('/jinn/plgconfwrapper.php','foo=bar').'&plug_orig='.$plg_name.'&plug_name=\'+document.frm.PLG'.$field['name'].'.value+\'&hidden_name=CFG_PLG'.$field['name'].'&hidden_val='.rawurlencode($plg_conf).'\', \'pop'.$field['name'].'\', \'width=400,height=400,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no\')';

					 $popup_onclick_afc='parent.window.open(\''.$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uia_edit_field.display&object_id='.$this->$this->where_value.'&field_key='.$field['name']).'\', \'pop'.$field['name'].'\', \'width=400,height=400,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no\')';

					 $this->template->set_var('plg_options',$options);
					 
					 $this->template->set_var('plg_conf',$plg_conf);
					 $this->template->set_var('lang_field_configuration',lang('Field configuration'));
					 $this->template->set_var('lang_afc',lang('advanced configuration'));
					 $this->template->set_var('lang_plugin_conf',lang('configure field plugin'));
					 $this->template->set_var('popup_onclick',$popup_onclick);
					 $this->template->set_var('popup_onclick_afc',$popup_onclick_afc);

					 $this->template->parse('plugins_rows','plugins_row',true);

				  }
			   }
			}
		 }

		 if ($row_color==$GLOBALS['phpgw_info']['theme']['row_on'])
		 {
			$row_color=$GLOBALS['phpgw_info']['theme']['row_off'];
		 }
		 else
		 {
			$row_color=$GLOBALS['phpgw_info']['theme']['row_on'];
		 }

		 $this->template->set_var('prow_color',$row_color);
		 $this->template->parse('out','plugins_footer');
	  }

	  function render_relations()
	  {
		 // FIXME re-use options vars to speed up rendering
		 
		 /* 
		 relation type 1 = one to many relation
		 relation type 2 = many to many relation
		 relation type 3 = one to one relation
		 */

		 $value=$this->object_values[relations];
		 $table_name=$this->object_values[table_name];

		 $hidden_value='<input type="hidden" name="FLDrelations" value="'.$value.'">';

		 if ($row_color==$GLOBALS['phpgw_info']['theme']['row_on'])
		 {
			$row_color=$GLOBALS['phpgw_info']['theme']['row_off'];
		 }
		 else
		 {
			$row_color=$GLOBALS['phpgw_info']['theme']['row_on'];
		 }
		 $this->template->set_var('lang_relations',lang('relations'));
		 $this->template->set_var('rrow_color',$row_color);
		 $this->template->set_var('hidden_value',$hidden_value);
		 $this->template->parse('out','relations_header');

		 $i=1;
		 if ($value)
		 {
			$relations=explode('|',$value);
			$this->type1_num=0;
			$this->type2_num=0;
			$this->type3_num=0;

			foreach($relations as $relation)
			{
			   $relation_parts=explode(':',$relation);
			   if ($relation_parts[0]==1)
			   {
				  $r1txt=lang('%1 has a one-to-many with %2 showing %3',$relation_parts[1],$relation_parts[3],$relation_parts[4]);
				  $this->type1_num++;
				  $this->template->set_var('total_num',$i);
				  $this->template->set_var('lang_delete',lang('delete'));
				  $this->template->set_var('type1_num',$this->type1_num);
				  $this->template->set_var('relation',$relation);
				  $this->template->set_var('r1txt',$r1txt);

				  $this->template->parse('relations_defined1','relation_defined1',true);
			   }
			   elseif ($relation_parts[0]==2)
			   {
				  $r2txt=lang('The identifierfield of this table, %1, represented by %2 has a many-to-many with %3 represented by %4 showing %5',$table_name,$relation_parts[1],$relation_parts[3],$relation_parts[2],$relation_parts[4]);
				  $this->type2_num++;
				  $this->template->set_var('total_num',$i);
				  $this->template->set_var('lang_delete',lang('delete'));
				  $this->template->set_var('type2_num',$this->type2_num);
				  $this->template->set_var('relation',$relation);
				  $this->template->set_var('r2txt',$r2txt);

				  $this->template->parse('relations_defined2','relation_defined2',true);
			   }
			   elseif ($relation_parts[0]==3)
			   {
				  $r3txt=lang('%1 has a one-to-one relation with %2 using the configuration of object %3',$relation_parts[1],$relation_parts[3],$relation_parts[4]);
				  $this->type3_num++;
				  $this->template->set_var('total_num',$i);
				  $this->template->set_var('lang_delete',lang('delete'));
				  $this->template->set_var('type3_num',$this->type3_num);
				  $this->template->set_var('relation',$relation);
				  $this->template->set_var('r3txt',$r3txt);

				  $this->template->parse('relations_defined3','relation_defined3',true);
			   }

			   
			   $i++;
			}
		 }


		 /*********************************
		 * ADD NEW ONE WITH MANY RELATION *
		 *********************************/
		 if($fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table_name))
		 {

			$this->template->set_var('lang_new_rel1',lang('Add new ONE TO MANY'));

			foreach($fields as $field)
			{
			   $fields_array[]=array
			   (
				  'name'=> $field[name],
				  'value'=> $field[name]
			   );
			}

			$rel1_options1=$this->ui->select_options($fields_array,$value,true);
			$this->template->set_var('rel1_options1',$rel1_options1);


			$this->template->set_var('lang_field',lang('field'));
			$this->template->set_var('lang_has_1rel',lang('has a ONE TO MANY relation with'));

			foreach($this->table_array as $table)
			{
			   $fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table[name]);
			   foreach($fields as $field)
			   {
				  $related_fields_array[]=array
				  (
					 'name'=> $table[name].'.'.$field[name],
					 'value'=> $table[name].'.'.$field[name]
				  );
			   }
			}
			$rel1_options2=$this->ui->select_options($related_fields_array,'',true);
			$this->template->set_var('rel1_options2',$rel1_options2);

			$this->template->set_var('lang_displaying',lang('field to display'));

			foreach($this->table_array as $table)
			{
			   $fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table[name]);
			   foreach($fields as $field)
			   {
				  $display_fields_array[]=array
				  (
					 'name'=> $table[name].'.'.$field[name],
					 'value'=> $table[name].'.'.$field[name]
				  );
			   }
			}
			$rel1_options3=$this->ui->select_options($display_fields_array,'',true);
			$this->template->set_var('rel1_options3',$rel1_options3);

			$this->template->parse('out','relations1');
		 }

		 /**********************************
		 * ADD NEW MANY WITH MANY RELATION *
		 **********************************/

		 if (is_array($this->table_array))
		 {


			$this->template->set_var('lang_new_rel2',lang('Add new MANY TO MANY relation'));
			$this->template->set_var('lang_the_id_of',lang('The identifyer from this table (%1.id) represented by',$table_name));


			foreach($this->table_array as $table)
			{
			   $fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table[name]);
			   foreach($fields as $field)
			   {
				  $fields_array[]=array
				  (
					 'name'=> $table[name].'.'.$field[name],
					 'value'=> $table[name].'.'.$field[name]
				  );
			   }
			}
			$rel2_options1=$this->ui->select_options($fields_array,$value,true);
			$this->template->set_var('rel2_options1',$rel2_options1);

			$this->template->set_var('lang_has_rel2_with',lang('has a MANY TO MANY relation with'));

			foreach($this->table_array as $table)
			{
			   $fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table[name]);
			   foreach($fields as $field)
			   {
				  $related_fields_array[]=array
				  (
					 'name'=> $table[name].'.'.$field[name],
					 'value'=> $table[name].'.'.$field[name]
				  );
			   }
			}
			$rel2_options2=$this->ui->select_options($related_fields_array,'',true);
			$this->template->set_var('rel2_options2',$rel2_options2);

			$this->template->set_var('lang_represented_by',lang('represented by:'));

			foreach($this->table_array as $table)
			{
			   $fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table[name]);
			   foreach($fields as $field)
			   {
				  $display_fields_array[]=array
				  (
					 'name'=> $table[name].'.'.$field[name],
					 'value'=> $table[name].'.'.$field[name]
				  );
			   }
			}
			$rel2_options3=$this->ui->select_options($display_fields_array,'',true);
			$this->template->set_var('rel2_options3',$rel2_options3);

			$this->template->set_var('lang_showing',lang('showing'));

			foreach($this->table_array as $table)
			{
			   $fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table[name]);
			   foreach($fields as $field)
			   {
				  $related_fields_array[]=array
				  (
					 'name'=> $table[name].'.'.$field[name],
					 'value'=> $table[name].'.'.$field[name]
				  );
			   }
			}
			$rel2_options4=$this->ui->select_options($related_fields_array,'',true);
			$this->template->set_var('rel2_options4',$rel2_options4);

			$this->template->parse('out','relations2');
		 }


		 /************************************
		 * ADD NEW ONE TO ONE RELATION (3)  *
		 ************************************/
		 if($fields=$this->bo->so->site_table_metadata($this->parent_site_id,$table_name))
		 {
			$this->template->set_var('lang_new_rel3',lang('Add new one-to-one relation'));

			$this->template->set_var('lang_field',lang('field'));
			unset($fields_array);
			foreach($fields as $field)
			{
			   $fields_array[]=array
			   (
				  'name'=> $field[name],
				  'value'=> $field[name]
			   );
			}

			$rel3_options1=$this->ui->select_options($fields_array,$value,true);
			$this->template->set_var('rel3_options1',$rel3_options1);
			
			$this->template->set_var('lang_has_3rel',lang('has a ONE-TO-ONE relation with'));

			$rel3_options2=$this->ui->select_options($related_fields_array,'',true);
			$this->template->set_var('rel3_options2',$rel3_options2);
			
			$this->template->set_var('lang_object_conf',lang('Using object configuration'));

			$objects_array=$this->bo->get_phpgw_records('phpgw_jinn_site_objects','parent_site_id',$this->parent_site_id,$limit[start],$limit[stop],'name');

			foreach($objects_array as $object)
			{
			   $objects[]=array
			   (
				  'name'=> $object[name],
				  'value'=> $object[object_id]
			   );
			}
			$rel3_options3=$this->ui->select_options($objects,'',true);
			$this->template->set_var('rel3_options3',$rel3_options3);
		

			$this->template->parse('out','relations3');
		 }



		 $this->template->parse('out','relations_footer');
	  }

	  function render_footer()
	  {
		 $cancel_link=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiadmin.add_edit_site&cancel=true&where_key=site_id&where_value='.$this->parent_site_id);

		 $delete_link=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.boadmin.del_phpgw_jinn_site_objects&where_key=object_id&where_value='.$this->where_value);

		 $this->template->set_var('confirm_del',lang('Are you sure?'));
		 $this->template->set_var('save_button',lang('save and finish'));
		 $this->template->set_var('save_and_continue_button',lang('save and contiue'));
		 $this->template->set_var('reset_form',lang('reset form'));
		 $this->template->set_var('lang_delete',lang('delete'));
		 $this->template->set_var('link_delete',$delete_link);
		 $this->template->set_var('cancel_link',$cancel_link);
		 $this->template->set_var('cancel_text',lang('cancel'));
		 $this->template->parse('out','footer');
	  }
   }
?>
