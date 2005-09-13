<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for eGroupWare
	Copyright (C)2002, 2004 Pim Snel <pim@lingewoud.nl>

	eGroupWare - http://www.egroupware.org

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


	/* DEFAULT/FALLBACK BLOB/TEXT/TEXTAREA PLUGIN */
	$this->plugins['def_blob']['name'] 				= 'def_blob';
	$this->plugins['def_blob']['title']				= 'Default Textarea';
	$this->plugins['def_blob']['version']			= '1.1';
	$this->plugins['def_blob']['author']			= 'Pim Snel';
	$this->plugins['def_blob']['enable']			= 1;
	$this->plugins['def_blob']['db_field_hooks']	= array
	(
		'longtext',
		'text',
		'blob',
	 );

	 $this->plugins['def_blob']['config']		= array
	 (
		'New_height_in_pixels' => array('100','text','maxlength=3 size=3'), 
	 );
	   

	function plg_fi_def_blob($field_name,$value, $config,$attr_arr)
	{
	   if($config['New_height_in_pixels'] && is_numeric(intval($config['New_height_in_pixels']))) $height=intval($config['New_height_in_pixels']);
	   else $height = '100';
	   
	   $input='<textarea name="'.$field_name.'" style="padding:1px;border:solid 1px #cccccc;width:460px; height:'.$height.'px">'.$value.'</textarea>';
		return $input;
	}


	/* DEFAULT/FALLBACK VARCHAR PLUGIN */
	$this->plugins['def_string']['name'] 			= 'def_string';
	$this->plugins['def_string']['title']			= 'default varchar';
	$this->plugins['def_string']['author']		= 'Pim Snel';
	$this->plugins['def_string']['version']		= '1.0';
	$this->plugins['def_string']['enable']			= 1;
	$this->plugins['def_string']['db_field_hooks']	= array
	(
		'string',
		'varchar',
		'char'
	);

	function plg_fi_def_string($field_name, $value, $config,$attr_arr)
	{
	   if($attr_arr['max_size'])
	   {
		  if($attr_arr['max_size']>40) $size=40;
		  else $size=$attr_arr['max_size'];

		  $max='size="'.$size.'" maxlength="'.$attr_arr['max_size'].'"';	
	   }

	   $input='<input type="text" name="'.$field_name.'" '.$max.' value="'.$value.'">';

		return $input;

	}	

	/* DEFAULT/FALLBACK INTEGER PLUGIN */
	$this->plugins['def_int']['name'] 			= 'def_int';
	$this->plugins['def_int']['title']			= 'default int plugin';
	$this->plugins['def_int']['version']		= '1.0';
	$this->plugins['def_int']['author']		= 'Pim Snel';
	$this->plugins['def_int']['enable']			= 1;
	$this->plugins['def_int']['db_field_hooks']	= array
	(
	   'int',
	   'tinyint'
	);

	function plg_fi_def_int($field_name,$value, $config,$attr_arr)
	{
		$input='<input type="text" name="'.$field_name.'" size="10" value="'.$value.'">';

		return $input;
	}

	/* DEFAULT/FALLBACK TIMESPAMP/DATE PLUGIN */
	$this->plugins['def_timestamp']['name'] 			= 'def_timestamp';
	$this->plugins['def_timestamp']['title']			= 'default timestamp plugin';
	$this->plugins['def_timestamp']['version']		= '1.0';
	$this->plugins['def_timestamp']['author']		= 'Pim Snel';
	$this->plugins['def_timestamp']['enable']			= 1;
	$this->plugins['def_timestamp']['db_field_hooks']	= array
	(
		'timestamp',	
	);

	function plg_fi_def_timestamp($field_name,$value, $config,$attr_arr)
	{

		global $local_bo;
		$input=$local_bo->common->format_date($value);

		return $input;
	}


?>
