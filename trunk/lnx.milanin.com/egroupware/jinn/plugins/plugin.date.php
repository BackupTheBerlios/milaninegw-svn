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

	/* 
	plugin.date.php contains the standard image-upload plugin for 
	JiNN number off standardly available 
	plugins for JiNN. 
	*/

	$this->plugins['date']['name']				= 'date';
	$this->plugins['date']['title']				= 'Date plugin';
	$this->plugins['date']['version']			= '0.1.1';
	$this->plugins['date']['author']			= 'Pim Snel';
	$this->plugins['date']['description']		= 'create date input box and date storage method, (At this time Dutch only)';
	$this->plugins['date']['enable']			= 1;
	$this->plugins['date']['db_field_hooks']	= array('date');
	
	function plg_fi_date($field_name,$value,$config,$attr_arr)
	{	
		global $local_bo;
		$field_name=substr($field_name,3);	
	
		$months_arr[0]='';
		$months_arr[1]='januari';
		$months_arr[2]='februari';
		$months_arr[3]='maart';
		$months_arr[4]='april';
		$months_arr[5]='mei';
		$months_arr[6]='juni';
		$months_arr[7]='juli';
		$months_arr[8]='augustus';
		$months_arr[9]='september';
		$months_arr[10]='oktober';
		$months_arr[11]='november';
		$months_arr[12]='december';
		
		$today = date("Y-m-d"); 
			
		if($value=='0000-00-00') $value=$today; 		
		
		list($tyear,$tmonth,$tday)=explode('-',$today);
		list($year,$month,$day)=explode('-',$value);

		$dsel[intval($day)]='SELECTED';
		$msel[intval($month)]='SELECTED';
		$ysel[intval($year)]='SELECTED';
//		var_dump($tmonth);	
		$input='Het is vandaag '.$tday.' '.$months_arr[intval($tmonth)].' '.$tyear.'<br>';

		/* day */
		
		$input.='<select name="DATD'.$field_name.'">';
		$input.='<option value=""></option>';

		for($i=1;$i<=31;$i++)
		{
			$input.='<option value="'.$i.'" '.$dsel[$i].'>'.$i.'</option>';
		}
		$input.='</select>';


		/* months */
		

		$input.='<select name="DATM'.$field_name.'">';
		$input.='<option value=""></option>';
	
		for($i=1;$i<=12;$i++)
		{
			$input.='<option value="'.$i.'" '.$msel[$i].'>'.$months_arr[$i].'</option>';
		}
		
		$input.='</select>';	
		

		/* year */
		
		$input.='<select name="DATY'.$field_name.'">';
		$input.='<option value=""></option>';

		for($i=2000;$i<=2015;$i++)
		{
			$input.='<option value="'.$i.'" '.$ysel[$i].'>'.$i.'</option>';
		}
		
		$input.='</select>';
		
		$input.='<input type="hidden" name="FLD'.$field_name.'" value="">';
		//die(var_dump($input));
		return $input;
	}

	function plg_sf_date($field_name,$HTTP_POST_VARS,$HTTP_POST_FILES,$config)
	{
		global $local_bo;
		
		$dates=$local_bo->common->filter_array_with_prefix($HTTP_POST_VARS,'DAT');
		$new_date=$dates[2].'-'.$dates[1].'-'.$dates[0];

		$HTTP_POST_VARS[$field_name]==$new_date;

//		die(var_dump($dates));
		if($new_date) return $new_date;
		return '-1'; /* return -1 when there no value to give but the function finished succesfully */
	}

	function plg_ro_date($value,$conf_array,$where_val_enc)
	{
	   return plg_bv_date($value,$conf_array,$where_val_enc);
	}

	
	function plg_bv_date($value,$conf_array,$where_val_enc)
	{
		$months_arr[0]='';
		$months_arr[1]='jan';
		$months_arr[2]='feb';
		$months_arr[3]='maa';
		$months_arr[4]='apr';
		$months_arr[5]='mei';
		$months_arr[6]='jun';
		$months_arr[7]='jul';
		$months_arr[8]='aug';
		$months_arr[9]='sep';
		$months_arr[10]='okt';
		$months_arr[11]='nov';
		$months_arr[12]='dec';
		
		list($year,$month,$day)=explode('-',$value);
		
		$value=intval($day).' '.$months_arr[intval($month)].' '.$year;
		return $value;
	}

?>
