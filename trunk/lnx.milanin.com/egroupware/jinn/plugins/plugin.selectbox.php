<?php
   /*******************************************************************\
   * eGroupWare - JiNN                                                 *
   * http://www.egroupware.org                                         *
   * ----------------------------------------------------------------- *
   * Jinn is Not Nuke, a mutli-user, multi-site CMS for eGroupWare.    *
   * Copyright (C)2002-2004, 2003 Pim Snel <pim@lingewoud.nl>          *
   * ----------------------------------------------------------------- *
   * Select-box Plugin                                                 *
   * This file is part of JiNN                                         *
   * ----------------------------------------------------------------- *
   * This library is free software; you can redistribute it and/or     *
   * modify it under the terms of the GNU General Public License as    *
   * published by the Free Software Foundation; Version 2 of the       *
   * License                                                           *
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

   $this->plugins['selectbox']['name'] 		= 'selectbox';
   $this->plugins['selectbox']['title']		= 'Select Box';
   $this->plugins['selectbox']['author']	= 'Pim Snel';
   $this->plugins['selectbox']['version']	= '0.4';
   $this->plugins['selectbox']['enable']	= 1;
   $this->plugins['selectbox']['description']	= 'List a couple of values in a listbox....';
   $this->plugins['selectbox']['db_field_hooks']	= array
   (
	  'string',
	  'char',
	  'int',
	  'tinyint',
	  'smallint',
	  'varchar',
	  'longtext',
	  'text'
   );
   $this->plugins['selectbox']['config']		= array
   (
	  'Keys_seperated_by_commas'=>array('one,two,three','area',''),
	  'Value_seperated_by_commas'=>array('one,two,three','area',''),
	  'Default_value'=>array('one','text',''),
	  'Empty_option_available'=> array(array('yes','no'),'select','')
   );

   function plg_fi_selectbox($field_name,$value, $config,$attr_arr)
   {
	  $pos_values=explode(',',$config['Value_seperated_by_commas']);

	  if(is_array($pos_values))
	  {

		 if($config['Keys_seperated_by_commas'])
		 {
			$pos_keys=explode(',',$config['Keys_seperated_by_commas']);
			if(is_array($pos_keys) && count($pos_keys)==count($pos_values)) 
			{
			   $keys=$pos_keys;
			}

		 }

		 if(!$keys)	
		 {
			$keys=$pos_values;
		 }


		 $input='<select name="'.$field_name.'">';
			if($config['Empty_option_available']=='yes') $input.='<option>';
			$i=0;
			foreach($pos_values as $pos_val) 
			{
			   unset($selected);
			   if(empty($value) && $pos_val==$config['Default_value']) $selected='SELECTED';	
			   //	  die($value.' '.$pos_val);
			   if($value==$pos_val) $selected='SELECTED';	
			   $input.='<option '.$selected.' value="'.trim($pos_val).'">'.trim($keys[$i]).'</option>';
			   $i++;
			}
			$input.='</select>';
	  }	
	  else
	  {
		 $input= '<input name="'.$field_name.'" type=text value="'.$value.'">';
	  }

	  return $input;
   }

   function plg_ro_selectbox($value, $config,$where_val_enc)
   {
	  plg_bv_selectbox($value, $config,$where_val_enc);
   }

   function plg_bv_selectbox($value, $config,$where_val_enc)
   {

	  $pos_values=explode(',',$config['Value_seperated_by_commas']);

	  if(is_array($pos_values))
	  {

		 if($config['Keys_seperated_by_commas'])
		 {
			$pos_keys=explode(',',$config['Keys_seperated_by_commas']);
			if(is_array($pos_keys) && count($pos_keys)==count($pos_values)) 
			{
			   $keys=$pos_keys;
			}

		 }

		 if(!$keys)	
		 {
			$keys=$pos_values;
		 }


		 $i=0;
		 foreach($pos_values as $pos_val) 
		 {
			if($value==$pos_val) $display = trim($keys[$i]);	
			$i++;
		 }
	  }	

	  return $display;
   }
?>
