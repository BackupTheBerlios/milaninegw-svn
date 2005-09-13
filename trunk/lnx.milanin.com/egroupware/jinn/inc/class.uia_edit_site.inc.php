<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for eGroupWare
   Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

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

   class uia_edit_site  extends uiadmin
   {
	  function uia_edit_site($bo)
	  {
		 $this->bo = $bo;
		 $this->template = $GLOBALS['phpgw']->template;

		 $this->ui = CreateObject('jinn.uicommon');
	  }

	  function render_form($where_key,$where_value)
	  {
		 $this->template->set_file(array
		 (
			'form_site' => 'frm_edit_site.tpl',
		 ));

		 $this->template->set_block('form_site','header','header');
		 $this->template->set_block('form_site','rows','rows');
		 $this->template->set_block('form_site','footer','form_footer');
		 $this->template->set_block('form_site','object_buttons','object_buttons');

		 if ($where_key && $where_value)
		 {
			$form_action = $GLOBALS[phpgw]->link('/index.php',"menuaction=jinn.boadmin.update_phpgw_jinn_sites");
			$where_key_form="<input type=\"hidden\" name=\"where_key\" value=\"$where_key\">";
			$where_value_form="<input type=\"hidden\" name=\"where_value\" value=\"$where_value\">";
			$values_object= $this->bo->get_phpgw_records('phpgw_jinn_sites',$where_key,$where_value,'','','name');
			$add_edit_button=lang('save');
			$action=lang('edit phpgw_jinn_sites');
		 }
		 else
		 {
			$form_action = $GLOBALS[phpgw]->link('/index.php',"menuaction=jinn.boadmin.insert_phpgw_jinn_sites");
			$add_edit_button=lang('add');
			$action=lang('add phpgw_jinn_sites' );
			$parent_site_id=$_POST['parent_site_id'];
		 }

		 $this->template->set_var('form_action',$form_action);
		 $this->template->set_var('where_key_form',$where_key_form);
		 $this->template->set_var('where_value_form',$where_value_form);
		 $this->template->parse('out','header');


		 $fields=$this->bo->so->phpgw_table_metadata('phpgw_jinn_sites');

		 foreach ($fields as $fieldproperties)
		 {

			$edit_value=$values_object[0][$fieldproperties[name]];

			if ($row_color==$GLOBALS['phpgw_info']['theme']['row_on'])
			{
			   $row_color=$GLOBALS['phpgw_info']['theme']['row_off'];
			}
			else
			{
			   $row_color=$GLOBALS['phpgw_info']['theme']['row_on'];
			}

			$input_name='FLD'.$fieldproperties[name];
			$display_name = lang(ucfirst(strtolower(ereg_replace("_", " ", $fieldproperties[name]))));
			$input_max_length=' maxlength="'. $fieldproperties[len].'"';
			$input_length=$fieldproperties[len];
			$value=$values_object[0][$fieldproperties[name]];

			if ($input_length>40)
			{
			   $input_length=40;
			}

			if (eregi("auto_increment", $fieldproperties[flags]) || $fieldproperties['default']=="nextval('seq_phpgw_jinn_sites'::text)")
			{
			   if (!$value)
			   {
				  $display_value=lang('automatic');
			   }
			   else
			   {
				  $display_value=lang('automatic');
			   }

			   $input='<input type="hidden" name="'.$input_name.'" value="'.$value.'">'.$display_value;
			}

			elseif ($fieldproperties[name]=='parent_site_id')
			{
			   if($value) // when we are editing
			   {
				  $parent_site_name=$this->bo->so->get_site_name($value);
				  $parent_site_id=$value; //id for further use in formgeneration
				  $input="<input type=hidden name=\"$input_name\" value=\"$value\">";
				  $input.=$parent_site_name;
			   }
			   elseif($parent_site_id) //when we are adding
			   {
				  $parent_site_name=$this->bo->so->get_site_name($parent_site_id);
				  $input="<input type=hidden name=\"$input_name\" value=\"$parent_site_id\">";
				  $input.=$parent_site_name;
			   }
			   else // when we are adding without parent_site_id; this must
			   {
				  die('Something went wrong, contact the uberadministrator');
			   }
			}
			// FIXME make real secure: leave empty and  only store in the database when value is filled
			elseif ($fieldproperties[name]=='site_db_user' || $fieldproperties[name]=='site_db_password')
			{
			   $input='<input type="password" name="'.$input_name.'" size="'.$input_length.'" $input_max_length" value="'.$value.'">';
			}
			elseif ($fieldproperties[name]=='serialnumber')
			{
			   $input='<input type="hidden" name="'.$input_name.'" value="'.time().'">'.$value;
			}

			elseif ($fieldproperties[name]=='upload_path')
			{
			   $input='<input type="text" name="'.$input_name.'" size="'.$input_length.'" $input_max_length" value="'.$value.'">';
			}
			elseif ($fieldproperties[type]=='string' || $fieldproperties[type] == 'varchar')
			{
			   $input='<input type="text" name="'.$input_name.'" size="'.$input_length.'" input_max_length" value="'.$value.'">';
			}
			elseif ($fieldproperties[type]=='int' || $fieldproperties[type]=='int4')
			{
			   $input='<input type="text" name="'.$input_name.'" size="10" value="'.$value.'">';
			}
			elseif ($fieldproperties[type]=='timestamp')
			{
			   if ($value)
			   {
				  $input=$this->bo->format_date($value);
			   }
			   else
			   {
				  $input = lang('automatic');
			   }
			}
			else
			{
			   $value = ereg_replace ("(<br />|<br/>)","",$value);
			   $input='<textarea name="'.$input_name.'" cols="60" rows="15">'.$value.'</textarea>';
			}

			$this->template->set_var('row_color',$row_color);
			$this->template->set_var('input',$input);
			$this->template->set_var('fieldname',$display_name);
			$this->template->parse('row','rows',true);

		 }

		 $onclick_export='location=\''.$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiadmin.export_site&where_key=site_id&where_value='.$values_object[0][site_id]).'\'';

		 $this->template->set_var('confirm_del',lang('Are you sure?'));
		 $this->template->set_var('save_button',lang('save and finish'));
		 $this->template->set_var('save_and_continue_button',lang('save and continue'));
		 $this->template->set_var('reset_form',lang('reset form'));
		 $this->template->set_var('lang_delete',lang('delete'));
		 $this->template->set_var('lang_cancel',lang('cancel'));
		 $this->template->set_var('onclick_delete','location=\''.$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.boadmin.del_phpgw_jinn_sites&where_key=site_id&where_value='.$where_value).'\'');
		 $this->template->set_var('onclick_cancel','location=\''.$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiadmin.browse_phpgw_jinn_sites&where_key=site_id&where_value='.$where_value).'\'');
		 $this->template->set_var('lang_test_access',lang('test database access'));
		 $this->template->set_var('test_access_link',$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.test_db_access'));
		 $this->template->set_var('onclick_export',$onclick_export);
		 $this->template->set_var('lang_export',lang('export this site'));
		 $this->template->set_var('extra_buttons',$extra_buttons);

		 $this->template->parse('footer','footer');
		 
		 $this->template->pparse('out','header');
		 $this->template->pparse('out','row');
		 $this->template->pparse('out','footer');
	  }

   }
?>
